#!/bin/bash

set -e  # Exit if any command fails

# 📩 Ask for commit message
read -p "📝 Enter your commit message: " msg

echo "🔍 Running tests inside Docker..."


# Run Laravel tests inside Docker container
docker exec ecommercelaravel-app-1 php artisan test

echo "✅ Tests passed!"


echo "🚀 Adding and committing changes..."
git add .
git commit -m "ci: $msg"

echo "📤 Pushing to development branch..."
git checkout development
git pull origin development
git push origin development

echo "🔄 Switching to main branch..."
git checkout main

echo "⬇️ Pulling latest from origin/main..."
git pull origin main

echo "🔀 Merging development into main..."
git merge development -m "merge after '$msg'"

echo "📤 Pushing merged changes to main..."
git push origin main

echo "✅ Done: Development merged into Main!"
