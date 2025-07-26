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
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=root
```

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
