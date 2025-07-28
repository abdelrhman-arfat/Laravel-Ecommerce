#!/bin/bash

# Stop script if any command fails
set -e

read -p "📝 Enter your commit message: " msg

echo "🔍 Running tests inside Docker..."
docker exec ecommercelaravel-app-1 php artisan test
echo "✅ Tests passed!"

# Get current branch name
current_branch=$(git rev-parse --abbrev-ref HEAD)

# Checkout to development if not already on it
if [ "$current_branch" != "development" ]; then
  echo "🔁 Switching to development branch..."
  git checkout development
fi

# Pull latest changes from development
echo "⬇️ Pulling latest from development..."
git pull origin development

# Add and commit changes
echo "🚀 Committing changes..."
git add .
git commit -m "ci: $msg"

# Push to development
echo "📤 Pushing to development branch..."
git push origin development

# Merge to main
echo "🔁 Switching to main branch..."
git checkout main
echo "⬇️ Pulling latest from main..."
git pull origin main
echo "🔀 Merging development into main..."
git merge development -m "merge after '$msg'"
echo "📤 Pushing to main..."
git push origin main

echo "✅ Done: Changes pushed to both development and main!"
