# Help Desk — IT Ticket System

A small role-based help desk built on Laravel 12 + Blade + Tailwind v4 + MySQL. Submitted as a graduation project — see `docs/PRD.md` for the full product spec.

## What it does

- **Employees** submit tickets with category, priority, and optional attachments (jpg/png/pdf, ≤ 5 MB each, max 5 per ticket), track them in a filterable list, and message the support team through a per-ticket conversation thread.
- **Agents** pick from an unassigned queue, move tickets through the workflow statuses (7 statuses: 4 active, 3 terminal), adjust priority, co-assign collaborators, and converse with the requester (replies are also emailed).
- **Admins** manage user accounts (no public sign-up), see a stats dashboard, and manage the full ticket list — filter, hand-assign to a technician, or delete.

Three roles with strict middleware + policy enforced access. Tickets can also arrive by email, and the whole UI supports dark mode and English/Arabic (RTL). See `docs/PRD.md` for the as-delivered spec and its out-of-scope list.

## Requirements

- PHP **8.2+**
- Composer
- MySQL / MariaDB (the project is developed against XAMPP's MariaDB on Windows)
- Node.js **20+** (for Vite + Tailwind v4)

## Setup

```bash
# 1. Install PHP and JS dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
# Edit .env and set DB_DATABASE / DB_USERNAME / DB_PASSWORD to match your local MySQL.

# 3. Database
php artisan migrate --seed

# 4. Build assets
npm run build
```

## Running the app

```bash
# Concurrently runs php artisan serve, queue:listen, pail (log tail), and Vite dev:
composer dev
```

Then visit <http://127.0.0.1:8000>. The root URL redirects to `/login`.

If you only want the HTTP server (no live asset rebuild), use `php artisan serve` and `npm run build` once.

## Login security

- **Rate limiting:** 5 failed login attempts per email+IP trigger a 60-second lockout; the verification step and code resend are throttled too.
- **Two-factor login (email OTP):** after a correct password, a 6-digit code (hashed at rest, 10-minute expiry) is emailed to the account and must be entered before the session is signed in. Accounts on the company `@nctkap.com` domain are exempt — the seeded demo accounts use it — so the demo flow below works out of the box. Accounts on any other (real, deliverable) address get the full two-step flow.

## Demo accounts

After `php artisan migrate --seed`, the following accounts are available (password is `password` for all):

| Email                       | Role     | Lands on        |
|-----------------------------|----------|-----------------|
| `admin@nctkap.com`          | admin    | `/dashboard`    |
| `agent@nctkap.com`          | agent    | `/tickets`      |
| `employee@nctkap.com`       | employee | `/my-tickets`   |
| `tariq@nctkap.com`          | agent    | `/tickets`      |
| `bandar@nctkap.com`         | agent    | `/tickets`      |
| `reem@nctkap.com`           | agent    | `/tickets`      |
| `nawaf@nctkap.com`          | employee | `/my-tickets`   |
| `maha@nctkap.com`           | employee | `/my-tickets`   |
| `saud@nctkap.com`           | employee | `/my-tickets`   |
| `hind@nctkap.com`           | employee | `/my-tickets`   |
| `turki.m@nctkap.com`        | employee | `/my-tickets`   |

The seeder also creates ~14 sample tickets distributed across statuses, categories, and priorities so the dashboard counters and the agent queue render meaningfully.

`DemoSeeder` is idempotent — re-running `php artisan db:seed` won't duplicate users (uses `firstOrCreate`) or tickets (sentinel-title check).

## Email to ticket

Tickets can also arrive by email. The app polls a configured IMAP inbox and creates a ticket from each new message — subject becomes the title, body becomes the description, status defaults to `open` with `medium` priority. The category and priority are detected from keywords/tags in the message when possible; when the category can't be determined the ticket is left `uncategorized` for a technician to classify rather than guessed. Senders matching a known user email are credited; unknown senders are attached to the `external@nctkap.com` placeholder employee (seeded by `AdminSeeder`).

The original sender's name and address are always recorded on the ticket (`source_name` / `source_email`), so even when a message is attributed to the External Sender, the ticket page shows a **"Received by email from … &lt;…&gt;"** banner identifying who actually sent it. The admin All Tickets table also shows the real sender in the **Submitted by** column for email-sourced tickets.

**Attachments** on the email (jpg / jpeg / png / pdf) are imported and stored against the ticket, using the same allow-list as the web form. Other file types are skipped.

**Auto-reply:** after a message becomes a ticket, the sender receives a confirmation email (`App\Mail\TicketReceivedMail`, an RTL Arabic template with the ticket id + subject). Automated/placeholder addresses (no-reply, mailer-daemon, postmaster, and the `external@nctkap.com` placeholder) are skipped to avoid mail loops, and a delivery failure never aborts the import. This needs working SMTP — configure `MAIL_*` in `.env` (Gmail SMTP reuses the same account + App Password as IMAP).

**One-time XAMPP setup:** uncomment `extension=imap` and `extension=zip` in `C:\xampp\php\php.ini`, then restart PHP / Apache.

**Configure the inbox** in `.env` (Gmail example):

```dotenv
IMAP_HOST=imap.gmail.com
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_USERNAME=helpdesk-tickets@gmail.com
IMAP_PASSWORD=xxxx-xxxx-xxxx-xxxx   # Gmail App Password (not your regular password)
IMAP_FOLDER=INBOX
```

Leave `IMAP_USERNAME` blank to disable — the command becomes a no-op.

**Run the fetch:**

```bash
php artisan tickets:fetch-emails               # on-demand, single pull
php artisan schedule:work                       # in another terminal — fires every 5 min
php artisan tickets:fetch-emails --interval=5   # near-real-time loop (same command, pulls every N seconds, min 5)
```

Or click **Fetch inbox now** on the admin dashboard. Imported messages are marked Seen so re-runs don't duplicate.

For hands-off import on Windows, `scripts/listen-tickets.vbs` launches `tickets:fetch-emails --interval=5` in a hidden window; copy it into your Startup folder to start it automatically at logon. It only imports while the machine is on and MySQL (XAMPP) is running.

## Deployment notes (demo vs. production)

Everything currently runs on the presenter's machine, which acts as the server: `php artisan serve` hosts the app at `127.0.0.1:8000`, MySQL (XAMPP) runs locally, and email intake relies on the Startup-folder VBS script launching `tickets:fetch-emails --interval=5` at Windows logon. That polling loop also drains the outbound mail queue itself (`queue:work --stop-when-empty` after each cycle), so no separate worker process exists.

This is **deliberate demo-environment scaffolding**: it needs zero server infrastructure, but the system only works while the machine is on, and the listener must be restarted manually after changes to the ingest code.

A production deployment would replace exactly those pieces — with no application code changes:

- an always-on Linux host (VPS or managed Laravel hosting) serving the app over HTTPS behind a real web server;
- **Supervisor** (or systemd) keeping `php artisan queue:work` and `php artisan tickets:fetch-emails --interval=5` running, restarting them on failure and on deploy;
- a **cron** entry running `php artisan schedule:run` every minute as the scheduled fallback fetch.

## Tests

```bash
composer test                  # full suite (PHPUnit, in-memory SQLite)
php artisan test --filter=...  # single test class or method
```

The suite covers:

- **Role × action matrix** — every protected route is allowed for the right role and forbidden (403 / login redirect) for the others.
- **Ticket workflow** — submission with/without attachments, validation (bad MIME, too many files), agent self-assign with contention guard, linear status transitions, priority updates, and the policy denials around them.

## Project layout

- `app/Http/Controllers/` — `LoginController`, `DashboardController` (admin), `UserController` (admin), `AgentTicketController`, `TicketController` (employee).
- `app/Policies/TicketPolicy.php` — auto-discovered, gates view/assign/updateStatus/updatePriority.
- `app/Http/Middleware/EnsureUserHasRole.php` — aliased as `role` in `bootstrap/app.php`.
- `database/seeders/` — `AdminSeeder` (the three baseline accounts) + `DemoSeeder` (extra fixtures).
- `resources/views/` — Blade templates organized by role: `admin/`, `tickets/` (agent), `my-tickets/` (employee), `users/` (admin), shared shell `layouts/nct.blade.php`.

## Tooling

```bash
./vendor/bin/pint              # format PHP (Laravel Pint)
npm run dev                    # Vite dev server (HMR)
npm run build                  # production asset build
php artisan migrate:fresh --seed   # reset + reseed the DB
```
