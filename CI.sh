#!/bin/bash


read -p "📝 Enter your commit message: " msg

echo "🔍 Running tests inside Docker..."
docker exec ecommercelaravel-app-1 php artisan test
echo "✅ Tests passed!"

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
  if git commit -m "ci: $msg"; then
    echo "📤 Pushing to development..."
    git push origin development
  else
    echo "❌ Git commit failed. Please check your message."
    exit 1
  fi
else
  echo "⚠️ No changes to commit."
fi

# Ask before merging to main
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
