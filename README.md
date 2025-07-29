# 🛒 Laravel E-Commerce Backend

This is a backend API for an E-Commerce platform built using **Laravel**, powered by **MySQL**, **Docker**, and **Nginx** for easy deployment and scalability.

---

## ⚙️ Tech Stack

- **PHP** (Laravel Framework)
- **MySQL** (Database)
- **Docker** (Containerization)
- **Nginx** (Web server / Reverse proxy)
- **Composer** (Dependency Management)

---

## 📁 Branches

> We use two main branches:

- `main`: ✅ Production-ready and stable code.
- `development`: 🚧 Ongoing development and feature testing.

### 🔀 How to Work With Branches

1. Clone the repository:

```bash
git clone https://github.com/abdelrhman-arfat/Laravel-Ecommerce.git
```

2. Switch to development branch:

```bash
   git checkout development
```

3. After developing and testing your changes, merge to main:

```bash
   git checkout main
   git pull origin main
   git merge development
   git push origin main
```

---

## CI.sh Script :

```bash
#!/bin/bash

read -p "📝 Enter your commit message: " msg

echo "🔍 Running tests inside Docker..."
test_output=$(docker exec your_folder_name-app-1 php artisan test)
echo "$test_output"

# Check if the test output includes any FAILURES
if echo "$test_output" | grep -q "FAILURES"; then
  echo "❌ Some tests failed! Fix them before committing."
  exit 1
fi

# Confirm all tests passed
if echo "$test_output" | grep -q "Tests:.*passed"; then
  echo "✅ Tests passed!"
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

```

---

## 🚀 Getting Started (with Docker)

### 1. Clone the repository

```bash
git clone https://github.com/abdelrhman-arfat/Laravel-Ecommerce.git
```

### 2. Create .env file

cp .env.example .env

Update the `.env` file with the following (match Docker MySQL service):

```bash

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=secret
```

cp .env.worker all in the .env file but update the name to work with docker service

---

### 3. Run Docker

```bash
docker-compose up -d --build
```

Wait until all containers (app, nginx, mysql) are up and running.

---

### 5. Generate App Key

```bash
docker exec -it app php artisan key:generate
```

---

### 6. Run Migrations

```bash
docker exec -it app php artisan migrate
```

---

## 🧪 Test API

The backend API will be available at:

http://localhost:8000/api

Use Postman, Insomnia, or any REST client to test your endpoints.

---

## 📦 Folde```r Structure

```bash
.
├── src # Laravel application code (app, routes, etc.)
├── docker-compose.yml
├── nginx/
│ └── default.conf
└── .env
```

---

## 🐳 Docker Services

| Service | Description    | Port |
| ------- | -------------- | ---- |
| app     | Laravel (PHP)  | 9000 |
| nginx   | Web server     | 8000 |
| db      | MySQL database | 3306 |

---

## 🛠 Useful Commands

---

## 📦 Deployment Notes

- Develop and test in `development` branch.
- Merge only fully tested features to `main`.

---
