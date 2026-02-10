#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.." || exit 1
PHPUNIT="./vendor/bin/phpunit"
# TARGET_TEST previously pointed at an Easypay SDK callback test that was removed.
# Update this to a different flaky test when using the script.
# TARGET_TEST="Tests\\Feature\\SomeOtherTest::test_example"

echo "Finding interfering test class for: $TARGET_TEST"

# Get ordered list of test classes (unique)
mapfile -t ALL_TESTS < <($PHPUNIT --list-tests 2>/dev/null | sed -n 's/^\s*\(.*::.*\)$/\1/p')
if [ ${#ALL_TESTS[@]} -eq 0 ]; then
  echo "Could not list tests (phpunit --list-tests returned nothing)." >&2
  exit 2
fi

# Find index of target
TARGET_INDEX=-1
for i in "${!ALL_TESTS[@]}"; do
  if [ "${ALL_TESTS[$i]}" = "$TARGET_TEST" ]; then
    TARGET_INDEX=$i
    break
  fi
done

if [ $TARGET_INDEX -lt 0 ]; then
  echo "Target test not found in phpunit --list-tests output." >&2
  exit 2
fi

echo "Target found at index $TARGET_INDEX"

# Build a unique list of classes that appear before the target
declare -A classes_seen
PREREQ_CLASSES=()
for ((j=0;j<TARGET_INDEX;j++)); do
  full=${ALL_TESTS[$j]}
  cls=${full%%::*}
  if [ -z "${classes_seen[$cls]+x}" ]; then
    classes_seen[$cls]=1
    PREREQ_CLASSES+=("$cls")
  fi
done

echo "Will test ${#PREREQ_CLASSES[@]} candidate classes..."

# For each prereq class: run that class, then the target test. If the target fails after running the class, report it.
for cls in "${PREREQ_CLASSES[@]}"; do
  echo "\n==> Running candidate class: $cls (then running target test)"
  set -x
  $PHPUNIT --stop-on-failure "$cls" >/dev/null
  set +x

  echo "Running target test now..."
  if $PHPUNIT --filter "$TARGET_TEST" --no-coverage --stop-on-failure; then
    echo "--> OK: running $cls then target succeeded"
  else
    echo "\n=== FOUND INTERFERING CLASS: $cls ==="
    exit 0
  fi
done

echo "No single preceding test-class reliably reproduces the flake. Consider finer-grained (per-test) bisect or add tracing." 
exit 0
