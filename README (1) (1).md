# 🌿 Nutrition & Fitness Platform

> A modular, full-stack PHP web application covering the four pillars of a digital health service — progress tracking, fitness coaching, meal planning, and an e-commerce product catalogue. Each module is independently deployable, shares a consistent MVC skeleton, and ships with both a polished public front-end and a feature-rich admin back-office.

![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-22c55e?style=flat-square)

---

## 📑 Table of Contents

- [Overview](#-overview)
- [Module Map](#-module-map)
- [Architecture](#-architecture)
- [Module: Progress Tracking](#-progress-tracking)
- [Module: Coaching](#-coaching)
- [Module: Meal Planning](#-meal-planning)
- [Module: Products & Orders](#-products--orders)
- [URL Routing](#-url-routing)
- [Tech Stack](#-tech-stack)
- [Bundled Libraries](#-bundled-libraries)
- [Installation](#-installation)
- [Notes & Caveats](#-notes--caveats)

---

## 🗺 Overview

The platform is split into **four self-contained PHP modules**. Each module:

- Runs off its own `index.php` front controller and MySQL database
- Exposes a **frontoffice** (public-facing) and a **backoffice** (admin panel)
- Uses the **Silva** Bootstrap 5 admin theme for the backoffice and the **Nutrio** nutrition template for the frontoffice
- Requires no framework, no npm, and no build step — just PHP + MySQL

| Module | Folder | Domain |
|--------|--------|--------|
| Progress Tracking | `progress/` | Goals, records, reports, mood tracking |
| Coaching | `coaching/` | Training programs, exercises, consultations |
| Meal Planning | `meal/` | Diet plans, recipes, macro tracking |
| Products & Orders | `produits/` | Catalogue, stock, order fulfilment |

---

## 🧩 Module Map

```
project-root/
├── progress/       # 📈 Progress tracking
├── coaching/       # 🏋️  Fitness coaching
├── meal/           # 🥗  Meal planning
└── produits/       # 🛒  Products & orders
```

Each module is completely independent — you can deploy any subset without touching the others.

---

## 🏗 Architecture

Every module follows an identical MVC layout:

```
<module>/
├── index.php                   # Front controller + URL router
├── config/
│   ├── Database.php            # PDO singleton — configure credentials here
│   └── schema.sql              # CREATE TABLE definitions
├── controllers/
│   ├── BaseController.php      # render(), redirect(), json() helpers
│   └── *Controller.php         # One controller per resource domain
├── models/
│   └── *.php                   # Active-Record / Repository classes (PDO)
├── views/
│   ├── layouts/
│   │   ├── backoffice.php      # Silva admin shell
│   │   ├── frontoffice.php     # Nutrio public shell
│   │   └── default.php         # Bare layout (no chrome)
│   └── <domain>/
│       ├── index.php           # List view
│       ├── form.php            # Create / edit form
│       └── show.php            # Detail view
└── assets/
    ├── backoffice/             # Silva theme + vendored JS/CSS libs
    ├── frontoffice/            # Nutrio static assets
    └── imgs/                   # Shared images (logo, etc.)
```

> **Routing pattern:** All requests hit `index.php`. The `?route=` query-string parameter controls dispatch — e.g. `?route=backoffice/goals`. Helper functions `route_url()` and `asset_url()` are defined at the top of each `index.php`.

---

## 📈 Progress Tracking

**Folder:** `progress/` — **Entry point:** `progress/index.php`

Users define measurable health or fitness goals with a start value, target value, and deadline. They then log progress records against each goal over time. A report layer aggregates records into summaries. Adherence scores and mood snapshots give coaches richer insight at a glance.

### Controllers

| Controller | Responsibility |
|---|---|
| `ProgressGoalController` | CRUD for goals |
| `ProgressRecordController` | CRUD for individual measurement records |
| `ProgressReportController` | Generate and display aggregated reports |
| `BaseController` | Shared render / redirect / JSON helpers |

### Views

```
views/
├── goals/        index.php · form.php · show.php
├── records/      index.php · form.php
├── reports/      index.php · form.php
├── updates/      index.php
├── dashboard.php
└── home.php
```

### Database Schema

#### `progress_goals`

| Column | Type | Notes |
|--------|------|-------|
| `id` | `INT PK AUTO_INCREMENT` | Primary key |
| `title` | `VARCHAR(150) NOT NULL` | Goal name |
| `metric` | `VARCHAR(100) NOT NULL` | e.g. "Body Weight", "Bench Press" |
| `start_value` | `DECIMAL(10,2) NOT NULL` | Baseline measurement |
| `target_value` | `DECIMAL(10,2) NOT NULL` | Success threshold |
| `unit` | `VARCHAR(30) NOT NULL` | kg, lbs, reps, … |
| `start_date` | `DATE NOT NULL` | |
| `target_date` | `DATE NOT NULL` | Deadline |
| `status` | `ENUM` | `ACTIVE` · `COMPLETED` · `ON_HOLD` |
| `description` | `TEXT` | Optional notes |
| `created_at` | `TIMESTAMP` | Auto-set |
| `updated_at` | `TIMESTAMP` | Auto-updated |

#### `progress_records`

| Column | Type | Notes |
|--------|------|-------|
| `id` | `INT PK AUTO_INCREMENT` | |
| `progress_goal_id` | `INT NOT NULL` | → `progress_goals(id)` CASCADE DELETE |
| `record_date` | `DATE NOT NULL` | |
| `recorded_value` | `DECIMAL(10,2) NOT NULL` | Actual measurement |
| `adherence_score` | `INT` | 0–100, nullable |
| `mood` | `ENUM` | `LOW` · `STEADY` · `HIGH` |
| `record_type` | `ENUM` | `CHECKPOINT` · `MILESTONE` · `MEASUREMENT` · `NOTE` · `REPORT` |
| `notes` | `TEXT` | Free-form entry |
| `created_at` | `TIMESTAMP` | Auto-set |
| `updated_at` | `TIMESTAMP` | Auto-updated |

---

## 🏋️ Coaching

**Folder:** `coaching/` — **Entry point:** `coaching/index.php`

Coaches can publish structured training programs and attach exercises with muscle group, set/rep, and rest-time metadata. The consultation sub-system lets prospective clients submit enquiries that staff can view and respond to. A static page repository powers editable marketing copy without a CMS.

### Controllers

| Controller | Responsibility |
|---|---|
| `BackofficeController` | Admin views: programs, exercises, consultations, pages |
| `FrontofficeController` | Public views: programs listing, consultation form |
| `HomeController` | Public home page |
| `BaseController` | Shared helpers |

### Views

```
views/
├── backoffice/   dashboard.php · consultation.php · exercises.php · programs.php · page.php
├── frontoffice/  programs.php · consultation.php · about-us.php · page.php
├── errors/       not-found.php
└── layouts/      backoffice.php · frontoffice.php · default.php · raw.php
```

### Database Schema

#### `programs`

| Column | Type | Notes |
|--------|------|-------|
| `id` | `INT PK AUTO_INCREMENT` | |
| `title` | `VARCHAR(100) NOT NULL` | Program name |
| `goal_type` | `VARCHAR(50)` | e.g. "Fat Loss", "Muscle Gain" |
| `duration_weeks` | `INT` | |
| `description` | `VARCHAR(255)` | |

#### `exercises`

| Column | Type | Notes |
|--------|------|-------|
| `id` | `INT PK AUTO_INCREMENT` | |
| `program_id` | `INT` | → `programs(id)` CASCADE DELETE |
| `name` | `VARCHAR(100) NOT NULL` | |
| `description` | `VARCHAR(255)` | Cues / technique notes |
| `muscle_group` | `VARCHAR(50)` | e.g. "Chest", "Legs" |
| `sets` | `INT` | |
| `reps` | `INT` | |
| `rest_seconds` | `INT` | |

---

## 🥗 Meal Planning

**Folder:** `meal/` — **Entry point:** `meal/index.php`

Nutritionists create tiered diet plans (Beginner → Advanced) with a daily calorie target. Each plan contains multiple recipes tagged by meal type and day number, with full macronutrient detail and an optional image upload. Recipe images are stored at `meal/assets/uploads/recipes/`.

### Controllers

| Controller | Responsibility |
|---|---|
| `DietPlanController` | CRUD for diet plans |
| `RecipeController` | CRUD for recipes + image upload handling |
| `BackofficeController` | Admin routing |
| `FrontofficeController` | Public routing |
| `HomeController` | Public home page |

### Views

```
views/
├── backoffice/
│   ├── diet-plans/   index.php · form.php · show.php
│   ├── recipes/      form.php
│   └── dashboard.php
├── frontoffice/
│   ├── diet-plans/   index.php · show.php
│   └── about-us.php
├── errors/           not-found.php
└── layouts/          backoffice.php · frontoffice.php · default.php · raw.php
```

### Database Schema

#### `diet_plans`

| Column | Type | Notes |
|--------|------|-------|
| `id` | `INT PK AUTO_INCREMENT` | |
| `title` | `VARCHAR(150) NOT NULL` | |
| `goal` | `VARCHAR(100) NOT NULL` | e.g. "Weight Loss", "Muscle Gain" |
| `duration_days` | `INT NOT NULL` | Length of plan in days |
| `description` | `TEXT` | |
| `target_calories_per_day` | `DECIMAL(8,2)` | Nullable |
| `level` | `ENUM` | `BEGINNER` · `INTERMEDIATE` · `ADVANCED` |
| `status` | `ENUM` | `ACTIVE` · `INACTIVE` |
| `created_at` | `TIMESTAMP` | Auto-set |
| `updated_at` | `TIMESTAMP` | Auto-updated |

#### `recipes`

| Column | Type | Notes |
|--------|------|-------|
| `id` | `INT PK AUTO_INCREMENT` | |
| `diet_plan_id` | `INT NOT NULL` | → `diet_plans(id)` CASCADE DELETE |
| `name` | `VARCHAR(150) NOT NULL` | |
| `meal_type` | `ENUM NOT NULL` | `BREAKFAST` · `LUNCH` · `DINNER` · `SNACK` |
| `day_number` | `INT` | Which day of the plan (nullable) |
| `calories` | `DECIMAL(8,2)` | kcal per serving |
| `proteins` | `DECIMAL(8,2)` | grams |
| `carbs` | `DECIMAL(8,2)` | grams |
| `fats` | `DECIMAL(8,2)` | grams |
| `prep_time_minutes` | `INT` | Nullable |
| `instructions` | `TEXT` | |
| `image` | `VARCHAR(255)` | Relative path under `assets/uploads/recipes/` |
| `created_at` | `TIMESTAMP` | Auto-set |
| `updated_at` | `TIMESTAMP` | Auto-updated |

---

## 🛒 Products & Orders

**Folder:** `produits/` — **Entry point:** `produits/index.php`

A lean e-commerce layer for selling nutrition supplements or fitness merchandise. Products are grouped into categories, each with a SKU, price, and stock quantity. The order system captures customer details, line-item pricing, and fulfilment status from `PENDING` through to `SHIPPED` or `CANCELLED`.

### Controllers

| Controller | Responsibility |
|---|---|
| `ProductController` | CRUD for products |
| `ProductCategoryController` | CRUD for categories |
| `OrderController` | Create, view, and update order status |
| `BaseController` | Shared helpers |

### Views

```
views/
├── categories/   index.php · form.php · show.php
├── products/     index.php · form.php
├── orders/       index.php · form.php · show.php
├── dashboard.php
└── layouts/      backoffice.php · frontoffice.php
```

### Database Schema

#### `product_categories`

| Column | Type | Notes |
|--------|------|-------|
| `id` | `INT PK AUTO_INCREMENT` | |
| `name` | `VARCHAR(150) NOT NULL` | |
| `description` | `TEXT` | |
| `status` | `ENUM` | `ACTIVE` · `INACTIVE` |
| `created_at` | `TIMESTAMP` | Auto-set |
| `updated_at` | `TIMESTAMP` | Auto-updated |

#### `products`

| Column | Type | Notes |
|--------|------|-------|
| `id` | `INT PK AUTO_INCREMENT` | |
| `product_category_id` | `INT NOT NULL` | → `product_categories(id)` CASCADE DELETE |
| `name` | `VARCHAR(150) NOT NULL` | |
| `sku` | `VARCHAR(80) NOT NULL` | Unique stock-keeping unit |
| `price` | `DECIMAL(10,2) NOT NULL` | |
| `stock_quantity` | `INT NOT NULL` | |
| `status` | `ENUM` | `ACTIVE` · `DRAFT` · `OUT_OF_STOCK` |
| `description` | `TEXT` | |
| `created_at` | `TIMESTAMP` | Auto-set |
| `updated_at` | `TIMESTAMP` | Auto-updated |

#### `orders`

| Column | Type | Notes |
|--------|------|-------|
| `id` | `INT PK AUTO_INCREMENT` | |
| `product_id` | `INT` | → `products(id)` SET NULL on delete |
| `product_name` | `VARCHAR(150) NOT NULL` | Snapshot at time of purchase (denormalised) |
| `customer_name` | `VARCHAR(150) NOT NULL` | |
| `customer_email` | `VARCHAR(255) NOT NULL` | |
| `quantity` | `INT NOT NULL` | |
| `unit_price` | `DECIMAL(10,2) NOT NULL` | Price locked at order time |
| `total_amount` | `DECIMAL(12,2) NOT NULL` | `quantity × unit_price` |
| `status` | `ENUM` | `PENDING` · `PAID` · `SHIPPED` · `CANCELLED` |
| `order_date` | `DATE NOT NULL` | |
| `shipping_address` | `TEXT` | |
| `notes` | `TEXT` | |
| `created_at` | `TIMESTAMP` | Auto-set |
| `updated_at` | `TIMESTAMP` | Auto-updated |

---

## 🔀 URL Routing

All traffic passes through each module's `index.php` via the `?route=` query parameter.

| URL | Route value | Description |
|-----|-------------|-------------|
| `index.php` | `frontoffice/home` | Public home page (default) |
| `index.php?route=frontoffice/goals` | `frontoffice/goals` | Public goals listing |
| `index.php?route=backoffice/dashboard` | `backoffice/dashboard` | Admin dashboard |
| `index.php?route=backoffice/goals&action=create` | `backoffice/goals` | New goal form |
| `index.php?route=backoffice/goals&action=edit&id=5` | `backoffice/goals` | Edit goal #5 |
| `index.php?route=backoffice/goals&action=delete&id=5` | `backoffice/goals` | Delete goal #5 |

Use the built-in helpers to generate links safely:

```php
route_url('backoffice/goals', ['action' => 'edit', 'id' => $id])
asset_url('assets/imgs/logo.png')
```

---

## 🛠 Tech Stack

| Layer | Technology | Notes |
|-------|-----------|-------|
| **Language** | PHP 7.4+ (strict types) | Custom MVC, no framework |
| **Database** | MySQL 5.7 / MariaDB 10.3+ | One DB per module, FK constraints enforced |
| **Admin UI** | Silva by ZoyoThemes | Bootstrap 5 admin template |
| **Public UI** | Nutrio by RadiantThemes | Nutrition & fitness landing page template |
| **Charts** | ApexCharts | SVG charts pre-wired in admin JS init files |
| **Tables** | DataTables + Bootstrap 5 integration | Sortable, searchable, paginated admin listings |
| **Rich text** | Quill.js | WYSIWYG editor for descriptions |
| **Date picker** | Flatpickr | Lightweight date/time input |
| **Calendar** | FullCalendar | Used in coaching module |
| **Icons** | Feather Icons + Material Design Icons | Both bundled locally |

---

## 📦 Bundled Libraries

All vendored locally — **no CDN dependency, no npm install required**.

| Library | Purpose |
|---------|---------|
| `jquery` | DOM utility (required by DataTables + plugins) |
| `bootstrap 5` | Base UI framework (JS bundle + CSS) |
| `apexcharts` | Interactive SVG charts |
| `datatables.net` + BS5 integration | Sortable & searchable tables |
| `flatpickr` | Date / time picker |
| `fullcalendar` | Calendar view |
| `quill` | Rich text / WYSIWYG editor |
| `jsvectormap` + world map data | SVG vector maps |
| `glightbox` | Image & video lightbox |
| `simplebar` | Custom styled scrollbars |
| `moment.js` | Date parsing & formatting |
| `feather-icons` | Open-source SVG icon set |
| `jquery.counterup` + `waypoints` | Animated stat counters on scroll |
| `node-waves` | Material ripple effect on click |

---

## ⚙️ Installation

### Requirements

- PHP **7.4** or higher
- MySQL **5.7** / MariaDB **10.3** or higher
- A web server with PHP support — Apache, Nginx, XAMPP, Laragon, or similar

### Step 1 — Extract the archive

Unzip the project into your web server document root:

```
htdocs/          ← XAMPP
www/             ← Laragon
/var/www/html/   ← Apache on Linux
```

### Step 2 — Create the databases

Each module uses its own database. Create them via the MySQL CLI or phpMyAdmin:

```sql
CREATE DATABASE progress_db;
CREATE DATABASE coaching_db;
CREATE DATABASE meal_db;
CREATE DATABASE produits_db;
```

### Step 3 — Import the schemas

```bash
mysql -u root -p progress_db  < progress/config/schema.sql
mysql -u root -p meal_db      < meal/config/schema_diet_plans_recipes.sql
mysql -u root -p produits_db  < produits/config/schema.sql

# Coaching tables are listed in coaching/tables.txt — paste into phpMyAdmin's SQL tab
```

### Step 4 — Configure database credentials

Edit `config/Database.php` inside each module you want to run:

```php
private $host     = 'localhost';
private $dbname   = 'progress_db';   // change per module
private $username = 'root';
private $password = '';
```

### Step 5 — Open in your browser

Each module is self-contained and accessed directly via its folder:

```
http://localhost/progress/
http://localhost/coaching/
http://localhost/meal/
http://localhost/produits/
```

To open the admin back-office, append the backoffice route:

```
http://localhost/progress/?route=backoffice/dashboard
http://localhost/coaching/?route=backoffice/dashboard
http://localhost/meal/?route=backoffice/dashboard
http://localhost/produits/?route=backoffice/dashboard
```

---

## ⚠️ Notes & Caveats

> **No authentication layer.**
> The backoffice routes are open to anyone who knows the URL. Before any public deployment, add session-based middleware or HTTP Basic Auth in front of all `?route=backoffice/*` paths.

> **Writable uploads directory.**
> The `meal/assets/uploads/recipes/` folder must be writable by the web-server process for recipe image uploads to work. On Linux: `chmod 775 meal/assets/uploads/recipes/`

> **Assets are pre-built.**
> The `assets/backoffice/` and `assets/frontoffice/` directories contain minified, vendored files. No npm, Webpack, or build step is needed — just serve them as static files.

> **Fully independent modules.**
> The four modules share no database, no session, and no PHP code. You can deploy any subset without touching the others.

> **Denormalised order snapshot.**
> In the `produits` module, `orders.product_name` stores the product name at the time of purchase. This prevents historical orders from being affected if the original product record is later renamed or deleted.
