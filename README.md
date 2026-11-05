# CRM Management SaaS

Production-oriented, multi-tenant CRM SaaS built exactly to the specified stack:
**HTML5, CSS3, Bootstrap 5, JavaScript (AJAX/Fetch), Core PHP 8+ (no Laravel/React/Vue/Node), MySQL via PDO, Chart.js, Bootstrap Icons.**

## Features
- **Auth:** register (creates tenant + tenant-admin + 14-day trial + default pipeline stages), login/logout, remember-me (hashed token cookie), forgot/reset password (hashed, expiring tokens), suspended account & suspended tenant checks, `password_hash`, secure sessions, CSRF on every POST, XSS escaping via `e()`.
- **Tenant isolation:** every tenant query is scoped by `tenant_id` from the session; API endpoints verify related rows belong to the tenant.
- **Dashboard:** customers / leads / deals / won revenue / conversion / pending tasks, Chart.js leads chart, recent activities.
- **Customers:** CRUD, search + status filter, categories, CSV export, CSV import (actually parses uploaded CSV: name,email,phone,company,category), plan customer-limit enforcement.
- **Leads:** CRUD, sources, status, 0–100 scoring, search, CSV export, one-click convert-to-customer.
- **Deals / Pipeline:** Kanban board grouped by deal stages, HTML5 drag-and-drop with Fetch AJAX to `api/deal_stage_update.php`, deal CRUD, stage totals.
- **Tasks:** CRUD, assignment to users, priority, status toggle, due dates, recurring flag.
- **Employees:** CRUD, departments (create + assign), job titles, salary, status.
- **Reports:** sales by stage, leads by source/conversion, won revenue by month (chart), date filter, CSV export.
- **Settings:** workspace settings (stored per-tenant), profile update, password change.
- **Subscriptions:** Free / Starter / Professional / Enterprise, monthly & yearly, upgrade/downgrade writes a new subscription + payment + invoice in a transaction, trial handling, coupon validity check, invoices list.
- **Super Admin (`super-admin/`):** platform dashboard & stats, tenants list with suspend/reactivate, plans CRUD, subscriptions + payments management, coupons CRUD, support tickets, announcements, platform settings, audit-log view.
- **Audit & activity logs** on key actions.

## Installation
1. Requirements: PHP 8.1+, MySQL 8+ (or MariaDB 10.4+), a web server (Apache/Nginx) or PHP built-in server.
2. Create the database and import the schema + demo data:
   ```bash
   mysql -u root -p < database.sql
   ```
   This creates database `crm_saas` with all tables, foreign keys, indexes and demo seed data.
3. Configure the DB connection via environment variables (or edit `config/config.php` defaults):
   `DB_HOST`, `DB_NAME` (default `crm_saas`), `DB_USER`, `DB_PASS`.
4. Serve the folder:
   ```bash
   php -S localhost:8000 -t .
   ```
5. Open `http://localhost:8000/auth/login.php`.

## Demo logins (password for all: `password`)
| Role | Email | URL after login |
|---|---|---|
| Super Admin | `super@crmpro.test` | `super-admin/index.php` |
| Tenant Admin (Demo Company) | `admin@demo.com` | `dashboard.php` |
| Manager | `manager@demo.com` | `dashboard.php` |

Demo coupon: `WELCOME10` (10% off).

## Project structure
```
config/        DB + app config (PDO, env-based)
includes/      functions (CSRF, escaping, audit, tenant helpers), auth guards, header/footer/sidebar
auth/          login, register, logout, forgot/reset password
api/           AJAX endpoints (deal stage update)
super-admin/   platform dashboard, tenants, plans, subscriptions, coupons, tickets, announcements, settings
assets/        CSS + JS (theme, kanban drag-drop, charts)
database.sql   complete schema + demo seed data
vercel-demo/   static in-browser demo (see below)
```

## Deployment notes
- **PHP/MySQL hosting (production app):** deploy to any PHP 8 + MySQL host / VPS (Apache/Nginx + PHP-FPM). Set env vars, import `database.sql`, point the web root at this folder, enable HTTPS (session cookie Secure flag activates automatically), and disable display_errors in production.
- **Payment integration:** payments/invoices are recorded in DB with a gateway-ready architecture — plug Stripe/PayPal credentials into the subscription change flow in `subscription.php` where the payment row is created, and mark payments `paid` only after gateway confirmation/webhook.
- **Vercel:** Vercel cannot run Core PHP/MySQL, so `vercel-demo/index.html` is a single self-contained static demo (Bootstrap 5 + Chart.js via CDN, data in localStorage) that mirrors the CRM — dashboard/stats/charts, customers & leads CRUD, drag-drop Kanban, tasks, and plans/invoices. Deploy that folder to Vercel for a live demo link; keep the full PHP app on PHP hosting / GitHub.

## Security notes
PDO prepared statements everywhere, `password_hash`/`password_verify`, CSRF tokens, output escaping, secure + HttpOnly + SameSite session cookies, tenant-scoped queries, audit logging. Change demo passwords immediately in production and remove/rotate seeded accounts.
