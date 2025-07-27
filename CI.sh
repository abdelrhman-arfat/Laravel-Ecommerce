#!/bin/bash

set -e  # Exit if any command fails

echo "🔍 Running tests inside Docker..."

# Go to Laravel app folder
cd src

# Run Laravel tests inside Docker container
docker compose exec ecommercelaravel-app-1 php artisan test

echo "✅ Tests passed!"

# Back to project root (outside src)
cd ..

echo "🚀 Adding and committing changes..."
git add .
git commit -m "ci: update from development"

echo "📤 Pushing to development branch..."
git checkout development
git pull origin development
git push origin development

echo "🔄 Switching to main branch..."
git checkout main

echo "⬇️ Pulling latest from origin/main..."
git pull origin main

echo "🔀 Merging development into main..."
git merge development

echo "📤 Pushing merged changes to main..."
git push origin main

echo "✅ Done: Development merged into Main!"
