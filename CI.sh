#!/bin/bash
set -e

read -p "📝 Enter your commit message: " msg

echo "🔍 Running tests inside Docker..."
docker exec ecommercelaravel-app-1 php artisan test

echo "✅ Tests passed!"

# Switch to development and pull latest
git checkout development
git pull origin development

# Add and commit
echo "🚀 Committing changes..."
git add .
git commit -m "ci: $msg"

# Push to development
git push origin development

# Merge to main
git checkout main
git pull origin main
git merge development -m "merge after '$msg'"
git push origin main

echo "✅ Done: Pushed to both development and main!"
