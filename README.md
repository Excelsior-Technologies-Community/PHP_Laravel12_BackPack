# PHP_Laravel12_BackPack

A Laravel and Backpack Admin Panel based Product and Category Management System with full CRUD operations, dashboard analytics, image upload, and role‑based admin access.

This project is ideal for Laravel learners, admin panel development practice, MCA or college submissions, portfolio projects, and inventory or product management demonstrations.

---

## Features

Admin Dashboard with Statistics
Category CRUD (Create, Read, Update, Delete)
Product CRUD with Image Upload
Relationship Management between Category and Products
Stock and Availability Tracking
Custom Admin Sidebar Menu
Search and Filters
Data Validation
Export Functionality
Seeder with Sample Data
Clean UI using Backpack Admin Panel

---

## Technology Stack

Laravel 12
Backpack for Laravel
MySQL
Blade Templates
Bootstrap Admin UI
PHP 8 or Higher

---

## Installation Guide

### Step 1: Create Laravel Project

```
composer create-project laravel/laravel LaravelBackpackProject
cd LaravelBackpackProject
```

### Step 2: Install Backpack

```
composer require backpack/crud
php artisan backpack:install
```

---

## Database Configuration

Update `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backpack_project
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```
php artisan migrate
```

---

## Modules Included

### 1. Category Module

Name
Description
Active or Inactive Status
Relationship with Products

### 2. Product Module

Category Selection
Name
Description
Price
Quantity
Image Upload
Availability Checkbox
Stock Status Indicator

---

## Dashboard

The dashboard includes:

Total Products
Total Categories
Available Products
Low Stock Products
Recent Products Table
Top Categories Table

---

## Project Structure (Important Files)

```
app/
 ├── Models/
 │   ├── Category.php
 │   └── Product.php
 ├── Http/Controllers/Admin/
 │   ├── CategoryCrudController.php
 │   ├── ProductCrudController.php
 │   └── DashboardController.php

 database/
 ├── migrations/
 └── seeders/

resources/views/vendor/backpack/
```

---

## File Storage Setup

```
php artisan storage:link
```

---

## Seeder and Sample Data

Run Seeder:

```
php artisan db:seed
```

Seeder includes:

5 Categories
5 Products
Admin User

---

## Default Admin Login

URL: `http://localhost:8000/admin`
Email: `admin@example.com`
Password: `password`

---

## Run Project

```
php artisan key:generate
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan serve
```

---

## Available Routes

| Route            | Description       |
| ---------------- | ----------------- |
| /admin           | Admin Panel       |
| /admin/product   | Manage Products   |
| /admin/category  | Manage Categories |
| /admin/dashboard | Custom Dashboard  |

---

## Validation Rules

### Category

Name Required
Description Optional

### Product

Name Required
Category Required
Price Numeric
Quantity Integer

---

## Export and Filters

Backpack automatically provides:

Column Filters
Sorting
Export CSV and Excel
Pagination

---

## Learning Outcomes

Laravel CRUD Operations
Admin Panel Development
Model Relationships
File Upload Handling
Dashboard Analytics
Backpack Configuration

---

## Screenshots (Optional)

You can add screenshots such as:
``
/screenshots/dashboard.png
<img width="1878" height="673" alt="image" src="https://github.com/user-attachments/assets/cb4e5e23-d34a-4166-b940-b242d2102616" />
/screenshots/products.png
<img width="1887" height="877" alt="image" src="https://github.com/user-attachments/assets/0286b806-f8a1-4886-93e2-c69c3e466c72" />

/screenshots/categories.png
<img width="1898" height="869" alt="image" src="https://github.com/user-attachments/assets/361607f1-dbed-45f2-ab7c-0bcf79944ec4" />

```

---

## Contributing

Pull requests are welcome. For major changes, open an issue first to discuss proposed updates.

---

## License

This project is open‑source and free to use for learning and educational purposes.

---

## Author

Mihir Mehta
PHP Laravel Developer
