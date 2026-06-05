# SahelHub ERP

A multi-tenant ERP SaaS platform built on Laravel 13 and React/Inertia.js.

## Modules

| Module | Description |
|---|---|
| HRM | Employee management, branches, departments, payroll |
| Recruitment | Job postings, applications, candidate pipeline |
| Account | Revenue, expenses, bank transactions, payments |
| Timesheet | Time tracking and attendance |
| Performance | Employee reviews and KPIs |
| Training | Training programs and sessions |
| Goal | OKR / goal tracking |
| Budget Planner | Budget management and forecasting |
| Double Entry | Full double-entry accounting |
| Contract | Contract lifecycle management |
| Lead | Lead and CRM pipeline |
| Quotation | Quotes and proposals |
| POS | Point of sale |
| Product & Service | Product catalog management |
| Form Builder | Custom form creation |
| Taskly | Task and project management |
| Calendar | Shared calendar with Google Calendar sync |
| Zoom Meeting | Meeting scheduling via Zoom |
| AI Assistant | AI-powered assistant |
| Webhook | Outgoing webhooks for external integrations |
| Support Ticket | Internal helpdesk ticketing |
| Landing Page | Tenant-facing landing page builder |
| Slack / Telegram / Twilio | Notification integrations |
| Stripe / PayPal | Payment gateway integrations |
| Google Captcha | Bot protection |

## Tech Stack

- **Backend:** Laravel 13, PHP 8.2+
- **Frontend:** React 18 + TypeScript, Inertia.js v3
- **Database:** MySQL 8.4
- **Auth:** Laravel Sanctum, Google 2FA, Social login (Google, Microsoft)
- **Storage:** Local / AWS S3 (via Flysystem)
- **Queue:** Database-backed jobs
- **Build:** Vite 5, Tailwind CSS

## Requirements

- PHP 8.2+ with extensions: `exif`, `gd`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- MySQL 8.0+
- Node.js 18+ and npm
- Composer 2

## Local Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Edit .env with your database credentials, then run migrations
php artisan migrate --seed

# 5. Build frontend assets
npm run build

# 6. Start the dev server
php artisan serve
```

The app will be available at `http://localhost:8000`.

For frontend hot-reload during development:
```bash
npm run dev
```

## Default Credentials

After seeding, log in with:

| Role | Email | Password |
|---|---|---|
| Super Admin | superadmin@example.com | 1234 |
| Company | company@example.com | 1234 |

**Change these immediately after first login.**

## Multi-Tenancy

Each company is an isolated tenant. Super admin manages companies and global settings. Company admins manage their own users, modules, and data. All data access is scoped to `created_by` (company owner ID).

## Security

- IDOR protection on all tenant-owned resources
- XSS sanitization via DOMPurify (frontend) and `strip_tags` allowlists (backend)
- SQL injection prevention via column allowlists on all sortable queries
- File upload restricted to explicit MIME type allowlists
- SSL verification enforced on outgoing webhook requests
- 2FA support via Google Authenticator

## License

MIT
