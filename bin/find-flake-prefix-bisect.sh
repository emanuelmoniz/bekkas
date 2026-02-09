#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.." || exit 1
PHPUNIT=./vendor/bin/phpunit
TARGET_TEST="Tests\\Feature\\EasypaySdkErrorHandlingTest::test_logSdkError_already_paid_with_pending_payment_updates_payment_and_marks_order_when_paid_remotely"

FILES=(tests/Feature/*Test.php)
TOTAL=${#FILES[@]}

echo "Running prefix bisect across ${TOTAL} test files to find minimal prefix that reproduces the flake."

low=1
high=$TOTAL
found_idx=0

run_prefix() {
  n=$1
  echo "Running prefix of first $n files..."
  args=("${FILES[@]:0:n}")
  # run prefix
  $PHPUNIT "${args[@]}" --stop-on-failure >/dev/null
  # then run target
  if $PHPUNIT --filter "$TARGET_TEST" --no-coverage --stop-on-failure; then
    return 1
  else
    return 0
  fi
}

# quick check: does full prefix reproduce?
if ! run_prefix "$TOTAL"; then
  echo "Full prefix does NOT reproduce the flake (unexpected). Aborting.";
  exit 2
fi

while [ $low -le $high ]; do
  mid=$(( (low + high) / 2 ))
  echo "\nTesting prefix size: $mid (range $low..$high)"
  if run_prefix "$mid"; then
    # flake reproduced with first mid files -> try smaller
    found_idx=$mid
    high=$((mid - 1))
  else
    # flake not reproduced -> need more files
    low=$((mid + 1))
  fi
done

if [ $found_idx -gt 0 ]; then
  echo "\nMinimal reproducing prefix size: $found_idx"
  echo "Files in minimal prefix:"; printf '%s\n' "${FILES[@]:0:found_idx}"
else
  echo "No reproducing prefix found. The flake may be non-deterministic or require a specific ordering not captured by simple prefixes.";
fi
exit 0
