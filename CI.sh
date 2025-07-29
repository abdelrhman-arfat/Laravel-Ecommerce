#!/bin/bash

read -p "📝 Enter your commit message: " msg

echo "🔍 Running tests inside Docker..."
cd ./src
test_output=$(php artisan test)
echo "$test_output"


# Check if the test output includes any FAILURES
if echo "$test_output" | grep -q "FAILURES"; then
  echo "❌ Some tests failed! Fix them before committing."
  exit 1
fi

# Confirm all tests passed
if echo "$test_output" | grep -q "Tests:.*passed"; then
  echo "✅ Tests passed!"
  cd ..
else
  echo "❌ Tests did not complete successfully. Check the output above."
  exit 1
fi

# Go to development branch if not already on it
current_branch=$(git rev-parse --abbrev-ref HEAD)
if [ "$current_branch" != "development" ]; then
  echo "🔁 Switching to development branch..."
  git checkout development
fi

git pull origin development

# Check for changes before committing
if [ -n "$(git status --porcelain)" ]; then
  echo "🚀 Committing changes..."
  git add .
  git commit -m "ci: $msg"
  git push origin development
else
  echo "⚠️ No changes to commit."
fi

read -p "🔄 Do you want to merge development into main? (y/n): " confirm
if [[ "$confirm" == "y" || "$confirm" == "Y" ]]; then
  git checkout main
  git pull origin main
  git merge development -m "merge after '$msg'"
  git push origin main
  echo "✅ Merged and pushed to main!"
else
  echo "ℹ️ Skipped merging to main."
fi
