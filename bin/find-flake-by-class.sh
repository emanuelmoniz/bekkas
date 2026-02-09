#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.." || exit 1
PHPUNIT=./vendor/bin/phpunit
TARGET_TEST="Tests\\Feature\\EasypaySdkErrorHandlingTest::test_logSdkError_already_paid_with_pending_payment_updates_payment_and_marks_order_when_paid_remotely"
TARGET_FILE="tests/Feature/EasypaySdkErrorHandlingTest.php"

echo "Searching for interfering test class by running each Feature test class before the target..."

for f in $(ls tests/Feature/*Test.php | sort); do
  [ "$f" != "$TARGET_FILE" ] || continue
  # extract FQCN from file (namespace + class)
  ns=$(sed -n 's/^namespace\s\+\([^;]\+\);/\1/p' "$f" | tr -d '\r')
  cls=$(sed -n 's/^class\s\+\([A-Za-z0-9_]+\)\s.*/\1/p' "$f" | head -n1 | tr -d '\r')
  if [ -z "$ns" ] || [ -z "$cls" ]; then
    echo "Could not parse class from $f; skipping"
    continue
  fi
  fqcn="$ns\\$cls"
  echo "\n==> Running class: $fqcn (file: $f)"
  set -x
  $PHPUNIT --stop-on-failure "$fqcn" >/dev/null
  set +x

  echo "Now running target test..."
  if $PHPUNIT --filter "$TARGET_TEST" --no-coverage --stop-on-failure; then
    echo "OK: running $fqcn then target succeeded"
  else
    echo "\n=== FOUND INTERFERING CLASS: $fqcn (file: $f) ==="
    exit 0
  fi
done

echo "No interfering class found (per-class ordering did not reproduce flake)." 
exit 0
