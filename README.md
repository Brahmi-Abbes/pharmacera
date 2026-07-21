# Pharmacera

A pharmacy inventory and point-of-sale management system built for small to mid-size pharmacies. Tracks stock down to the batch level, enforces FIFO-aware selling against real expiry dates, and alerts staff before stock runs out or medicine expires.

**Live demo:** [link once deployed]
**Demo video:** [link once recorded]

## The problem

Small pharmacies frequently track inventory in spreadsheets or paper — no automatic expiry tracking, no protection against overselling a batch, no audit trail of who changed what. Pharmacera solves this with a real admin system: role-based access for owners, pharmacists, and cashiers, race-condition-safe stock deduction, and automatic email alerts the moment stock crosses a low threshold or runs out.

## Core features

- **Inventory management** — medicines organized by category, tracked in individual batches with their own purchase price, quantity, and expiry date
- **FIFO-aware point of sale** — barcode scanning support, automatically sells from the batch closest to expiry first
- **Race-condition-safe stock deduction** — every stock mutation runs inside a database transaction with row-level locking, so two simultaneous sales can never oversell the same batch
- **Role-based access control** — Admin, Pharmacist, and Cashier roles, each with a distinct, deliberately scoped set of permissions enforced at both the resource and relation-manager level
- **Automated email alerts** — low-stock and out-of-stock notifications fire the moment a threshold is crossed, sent to admins and pharmacists via a queued job
- **Full audit trail** — every change to stock, sales, and batches is logged with before/after values via activity logging
- **PDF reporting** — daily and monthly reports covering revenue, staff performance, top-selling medicines, stock value, and expiry risk
- **Multi-language** — English, French, and Arabic (with RTL support)
- **Dashboard** — real-time stats, low-stock table, expiring-batches table, and a top-sellers chart

## Tech stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.5 |
| Admin panel | Filament v5 |
| Database | MySQL 8 |
| Auth & permissions | Spatie Laravel Permission |
| Audit logging | Spatie Laravel Activitylog |
| PDF generation | barryvdh/laravel-dompdf |
| Testing | Pest |
| Containerization | Docker + Docker Compose |
| CI/CD | GitHub Actions |
| Deployment | Render |

## Architecture decisions worth noting

- **Stock is never mutated with `decrement()`/`increment()`** — always loaded, changed, and saved through Eloquent, specifically so activity logging captures every change. Wrapped in `DB::transaction()` with `lockForUpdate()` so this remains safe under concurrent sales.
- **Authorization logic is centralized** in a `HasRoleAuthorization` trait, reused by every Filament resource *and* by relation managers, rather than duplicated per-resource.
- **Notifications are queue-driven**, dispatched via the database queue driver, with a dedicated worker container running independently of the web process.

## Running locally

```bash
git clone https://github.com/Brahmi-Abbes/pharmacera.git
cd pharmacera
cp .env.example .env
docker compose up --build
```

In a second terminal:
```bash
docker compose exec app php artisan migrate --seed
```

Visit `http://127.0.0.1:8000/admin` — seeded credentials are in `database/seeders/RoleSeeder.php`.

To run the test suite:
```bash
docker compose exec app php artisan test
```

## Test coverage

Pest test suite covering the areas most likely to break silently: stock deduction on sale creation, edit, and delete; batch-switching mid-edit with automatic rollback on insufficient stock; low-stock and out-of-stock notification triggers, including that a single threshold crossing fires exactly once and doesn't double-fire when stock jumps straight from healthy to zero.

## Author

**Brahmi Abbes** — [GitHub](https://github.com/Brahmi-Abbes)

## License

This code is publicly visible for portfolio purposes. Commercial use, resale, or redistribution is not permitted without written permission from the author.