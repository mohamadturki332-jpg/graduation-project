# Help Desk — Milestones

High-level delivery plan for the IT Help Desk Ticket System (see `docs/PRD.md`).

## M1 — Foundation
- [x] Configure `.env` for MySQL and create the database
- [x] Confirm Tailwind v4 + Vite build (`npm run dev` / `npm run build`)
- [x] Add base Blade layout (`layouts/app.blade.php`) with nav + flash slots
- [x] Replace `welcome` route with a redirect to login

## M2 — Authentication & RBAC
- [x] Migration: add `role` enum (`admin`, `agent`, `employee`) to `users`
- [x] Cast `role` on `User` model and add `hasRole()` helper
- [x] Login form + controller; remove all registration routes/views
- [x] `EnsureUserHasRole` middleware aliased as `role` in `bootstrap/app.php`
- [x] Post-login redirect by role (admin → dashboard, agent → tickets, employee → my tickets)

## M3 — User Management (Admin)
- [x] `UserController` with index/create/store/edit/update/destroy behind `role:admin`
- [x] FormRequests for create/update with unique-email and role rules
- [x] Blade views: user list with role badges, create/edit forms
- [x] `AdminSeeder` to bootstrap the first admin account

## M4 — Ticket Domain
- [x] Migration: `tickets` (title, description, category/priority/status enums, `user_id`, nullable `agent_id`)
- [x] Migration: `attachments` (`ticket_id`, `file_path`, `file_name`)
- [x] `Ticket` model with `user`, `agent`, `attachments` relations + enum casts
- [x] `Attachment` model with `belongsTo(Ticket)`
- [x] `TicketFactory` and `AttachmentFactory`

## M5 — Employee Experience
- [x] Submit-ticket form (title, description, category, priority, file inputs)
- [x] `StoreTicketRequest` with category/priority validation and file rules (jpg/png/pdf, size cap)
- [x] Persist uploads via `Storage` on the local disk and link rows in `attachments`
- [x] "My Tickets" index scoped to `auth()->id()`
- [x] Ticket detail view with attachment download links

## M6 — Agent Experience
- [x] Unassigned tickets queue (status = open, `agent_id` null)
- [x] Self-assign action: set `agent_id`, transition status to `in_progress`
- [x] "My Tickets" view (where `agent_id = auth()->id()`)
- [x] Status update enforcing linear flow `open → in_progress → closed`
- [x] Priority update (low/medium/high)
- [x] `TicketPolicy` for view/assign/update authorization

## M7 — Admin Dashboard
- [x] `DashboardController` returning the three status counts
- [x] Dashboard view with three counter cards
- [x] Read-only "All Tickets" index for admin

## M8 — Hardening & Handoff
- [x] Tighten file upload rules (mime check, max size, unique filenames)
- [x] Responsive pass on desktop and tablet breakpoints
- [x] Feature tests covering the role × action matrix
- [x] Feature tests covering ticket workflow transitions
- [x] Demo seeders (sample agents, employees, tickets) and README run steps
