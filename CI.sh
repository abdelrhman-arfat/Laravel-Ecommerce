#!/bin/bash

read -p "📝 Enter your commit message: " msg

echo "🔍 Running tests inside Docker..."
docker exec ecommercelaravel-app-1 php artisan test
test_status=$?

if [ $test_status -ne 0 ]; then
  echo "❌ Tests failed! Fix the errors before committing."
  exit 1
else
  echo "✅ Tests passed!"
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
