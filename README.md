# ClickHouse Demo (Laravel + Filament)

A small demo that shows **MySQL as the source of truth for orders**, **async sync into ClickHouse for analytics**, and a **Filament admin dashboard** that reads those analytics from ClickHouse.

```
Filament creates Order
        │
        ▼
   MySQL `orders`
        │  OrderObserver → SyncOrderToClickHouse job
        ▼
   Database queue (jobs table)
        │  sail artisan queue:work
        ▼
   ClickHouse `order_events`
        │
        ▼
   Filament dashboard widgets (aggregates)
```

## Stack

| Piece | Role |
|---|---|
| Laravel 13 + Sail | App runtime |
| MySQL 8.4 | OLTP (orders, users, jobs) |
| ClickHouse | OLAP (`order_events`) |
| Filament 5 | Admin UI (`/admin`) |
| `laravel-clickhouse/laravel-clickhouse` | ClickHouse DB driver |

## Requirements

- Docker (Sail)
- Composer, Node (for assets if you change the frontend)

## Quick start

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

In a second terminal, start a queue worker (required for orders created in Filament):

```bash
./vendor/bin/sail artisan queue:work
```

Open the admin panel:

- App: [http://localhost:8000/admin](http://localhost:8000/admin) (`APP_PORT=8000`)
- Email: `admin@example.com`
- Password: `password`

### Useful local URLs

| Service | URL |
|---|---|
| Filament admin | http://localhost:8000/admin |
| phpMyAdmin (MySQL) | http://localhost:8080 |
| ClickHouse HTTP / Web UI | http://localhost:8123 |

## Admin login & users

Seeding creates the default admin via `AdminUserSeeder`.

You can also create more users:

1. **From Filament** — after login, open **Users** and create a user  
2. **From CLI**

```bash
./vendor/bin/sail artisan make:filament-user
```

`App\Models\User` implements `FilamentUser`, so authenticated users can access the admin panel.

## Demo walkthrough

1. Log in at `/admin`
2. Open the dashboard — **Order analytics (ClickHouse)** shows counts/revenue from `order_events`
3. Create an order under **Orders**
4. With `queue:work` running, the job inserts a row into ClickHouse
5. Refresh the dashboard — totals update

Seed data: **10 orders today** + **290 orders** over the previous ~90 days.

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

## Inspect ClickHouse data

Table: `demo.order_events`

**Web SQL UI** (from http://localhost:8123 → Web SQL UI):

```sql
SELECT * FROM demo.order_events ORDER BY ordered_at DESC LIMIT 50;
```

**CLI:**

```bash
./vendor/bin/sail exec clickhouse clickhouse-client \
  --user demo --password password --database demo \
  --query "SELECT count() FROM order_events"
```

Credentials match `.env`: user `demo`, password `password`, database `demo`.

## Project map

| Path | Purpose |
|---|---|
| `app/Models/Order.php` | MySQL order model + observer binding |
| `app/Observers/OrderObserver.php` | Dispatches sync job on create |
| `app/Jobs/SyncOrderToClickHouse.php` | Writes to ClickHouse |
| `app/Filament/Resources/Orders/` | Order CRUD |
| `app/Filament/Resources/Users/` | Admin user management |
| `app/Filament/Widgets/OrderAnalyticsOverview.php` | Dashboard stats from ClickHouse |
| `database/migrations/clickhouse/` | ClickHouse schema (loaded outside `testing`) |
| `compose.yaml` | Sail: MySQL, ClickHouse, phpMyAdmin |

## Environment notes

- Inside Sail, hosts are service names (`mysql`, `clickhouse`)
- ClickHouse HTTP port for the Laravel client is **8123** (not the native 9000 port)
- MySQL and ClickHouse migrations both run with `sail artisan migrate` (ClickHouse path is registered in `AppServiceProvider`)

## Tests

```bash
php artisan test --compact
```

Feature tests use in-memory SQLite and skip ClickHouse migrations.

## License

This demo is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
