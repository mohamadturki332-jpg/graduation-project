# Product Requirements Document
## IT Help Desk Ticket System
### Laravel Web Application — Graduation Project

**Date:** April 2026
**Last Updated:** July 2026 (post-delivery revision — reflects the shipped system)

> **Revision note:** The original PRD (April 2026) defined the baseline scope. All eight
> milestones are now complete, and several capabilities that were originally listed as out of
> scope or future work were subsequently implemented. This revision documents the system **as
> delivered**. Sections describing post-baseline additions are marked **(Added post-baseline)**.
> Notably, the ticket workflow was expanded from the original three linear statuses to seven
> statuses (see Section 6), the ticket comment thread evolved into a requester conversation
> with email delivery (Section 13), requester satisfaction ratings and an operations/SLA
> report were added (Section 15), and the whole UI gained dark mode and full English/Arabic
> localization (Section 16).

---

## Table of Contents

1. [Overview](#1-overview)
2. [Project Information](#2-project-information)
3. [Goals & Objectives](#3-goals--objectives)
4. [User Roles & Permissions](#4-user-roles--permissions)
5. [Ticket Structure](#5-ticket-structure)
6. [Ticket Workflow](#6-ticket-workflow)
7. [Ticket Categories](#7-ticket-categories)
8. [Ticket Priorities](#8-ticket-priorities)
9. [System Pages](#9-system-pages)
10. [Authentication & Access Control](#10-authentication--access-control)
11. [Technology Stack](#11-technology-stack)
12. [Database Schema](#12-database-schema)
13. [Collaboration Features (Added post-baseline)](#13-collaboration-features-added-post-baseline)
14. [Email-to-Ticket Intake (Added post-baseline)](#14-email-to-ticket-intake-added-post-baseline)
15. [Satisfaction Ratings & Reporting (Added post-baseline)](#15-satisfaction-ratings--reporting-added-post-baseline)
16. [User Interface & Localization (Added post-baseline)](#16-user-interface--localization-added-post-baseline)
17. [Non-Functional Requirements](#17-non-functional-requirements)
18. [Out of Scope](#18-out-of-scope)
19. [Future Considerations](#19-future-considerations)

---

## 1. Overview

This document defines the product requirements for an internal IT Help Desk Ticket System built as a Laravel web application. The system enables company employees to submit IT support tickets and allows the IT department to manage, prioritize, and resolve those tickets efficiently.

The system is designed for a single company's internal use, where employees report technical issues and the IT support team handles them through a structured ticketing workflow.

---

## 2. Project Information

| Field | Details |
|---|---|
| Project Name | IT Help Desk Ticket System |
| Project Type | Graduation Project |
| Technology Stack | Laravel (PHP Framework) |
| Application Type | Web Application |
| Target Environment | Single Company — Internal Use |
| Date | April 2026 |

---

## 3. Goals & Objectives

- Provide employees with a simple way to report IT issues and track their status.
- Enable IT support technicians to manage and resolve tickets efficiently.
- Give the admin a clear dashboard to monitor ticket volume and statuses.
- Streamline internal IT support operations through a structured workflow.

---

## 4. User Roles & Permissions

The system has three distinct user roles, each with specific permissions and access levels.

### 4.1 Admin (IT Manager)

The admin is the system administrator who manages users and oversees all operations.

- Add, edit, and delete users (employees and technicians), including each user's department.
- Assign roles to users (Employee or Technician).
- View the admin dashboard with ticket statistics.
- View all tickets in the system (with keyword/status/priority/category filtering), including the real sender of email-sourced tickets.
- Hand-assign an open, unassigned ticket directly to a chosen technician.
- Reassign any assigned ticket from one technician to another.
- Delete any ticket (its attachments and messages are removed with it).
- Participate in the requester conversation on any ticket.
- Trigger an on-demand email-inbox fetch from the dashboard.
- View technician satisfaction ratings (the dashboard leaderboard and a dedicated Ratings page) and generate the printable operations/SLA report.

### 4.2 IT Support Technician

Technicians are IT staff responsible for resolving tickets submitted by employees.


- View all new (unassigned) tickets on the tickets page.
- Pick and assign tickets to themselves (picking moves the ticket to In Progress).
- Update ticket status across the full status set while the ticket is active (see Section 6); terminal statuses are final.
- Update ticket priority (Low, Medium, High) on tickets assigned to them, until the ticket reaches a terminal status.
- Work on multiple tickets simultaneously.
- Add collaborators (co-assign other technicians) to a ticket they own.
- Reassign (transfer ownership of) a ticket they currently own to another technician.
- Converse with the requester on tickets where they are the primary technician; their replies are also emailed to the requester.
- View their own satisfaction ratings: overall average, star distribution, and written feedback — with the requester's identity hidden (feedback is anonymous to the technician).

### 4.3 Employee

Employees are company staff who submit tickets when they encounter IT issues.

- Submit new support tickets with title, description, category, priority, and attachments.
- View only their own tickets and track status (with the same filter sidebar as the technician queue).
- Message the support team through the conversation thread on their own tickets.
- Rate the handling technician (1–5 stars plus an optional comment) once their ticket has been solved; a rating can be updated afterwards.

### Roles & Permissions Summary

| Permission | Admin | Technician | Employee |
|---|---|---|---|
| Manage Users | ✓ | ✗ | ✗ |
| View Dashboard | ✓ | ✗ | ✗ |
| View All Tickets | ✓ | ✓ | ✗ |
| Pick / Self-Assign Tickets | ✗ | ✓ | ✗ |
| Hand-Assign Tickets to a Technician | ✓ | ✗ | ✗ |
| Delete Tickets | ✓ | ✗ | ✗ |
| Set Priority | ✗ | ✓ (assigned, active) | ✓ (on submit) |
| Update Ticket Status | ✗ | ✓ (assigned, active) | ✗ |
| Submit Tickets | ✗ | ✗ | ✓ |
| View Own Tickets | ✗ | ✗ | ✓ |
| Co-assign / Add Collaborators | ✓ | ✓ (primary only) | ✗ |
| Reassign Ticket Ownership | ✓ | ✓ (primary only) | ✗ |
| Requester Conversation | ✓ | ✓ (primary only) | ✓ (own tickets) |
| Rate Handling Technician | ✗ | ✗ | ✓ (own solved tickets) |
| View Own Ratings | ✗ | ✓ | ✗ |
| View Ratings & Reports | ✓ | ✗ | ✗ |

---

## 5. Ticket Structure

Each ticket in the system contains the following information:

| Field | Type | Set By | Description |
|---|---|---|---|
| Ticket ID | Auto-generated | System | Unique identifier |
| Title | Text | Employee | Brief description of the issue |
| Description | Text (Long) | Employee | Detailed explanation of the problem |
| Category | Select | Employee | Network / Hardware / Software / Access Request |
| Attachments | Files | Employee | Screenshots or supporting documents |
| Priority | Select | Employee / Technician | Low / Medium / High |
| Status | Select | Technician | Open / In Progress / On Hold / Waiting for Resources / Rejected / Closed / Resolved |
| Submitted By | Auto-filled | System | The employee who created the ticket |
| Assigned To | Auto-filled | System | The primary technician who picked the ticket |
| Collaborators | Auto-filled | System | Additional technicians co-assigned to the ticket |
| Conversation | Text | Requester, primary technician, admin | Persisted message thread; technician replies are also emailed to the requester |
| Source Email / Name | Auto-filled | System | Original sender when the ticket arrived by email |
| Rating / Comment | Stars (1–5) + Text | Employee | Requester's satisfaction rating of the handling technician, set once the ticket is solved — *Added post-baseline* |
| Resolved At | Timestamp | System | When the ticket first entered a solved state, used for resolution-time reporting — *Added post-baseline* |
| Created At | Timestamp | System | Date and time of submission |
| Updated At | Timestamp | System | Last modification date |

---

## 6. Ticket Workflow

> **Revised post-baseline:** the original design used three linear statuses
> (Open → In Progress → Closed). The delivered system expands this to **seven
> statuses**: four active states the technician can move freely between, and
> three terminal states that permanently lock the ticket.

### 6.1 Status Definitions

| Status | Kind | Description |
|---|---|---|
| Open | Active | Submitted and awaiting a technician; the only state in which a ticket can be picked or hand-assigned. |
| In Progress | Active | A technician has picked the ticket and is actively working on it. |
| On Hold | Active | Work is temporarily paused. |
| Waiting for Resources | Active | Blocked until parts, licenses, approvals, or third parties come through. |
| Rejected | Terminal | The request was declined; no work will be done. |
| Closed | Terminal | The ticket was closed without a confirmed fix (e.g. withdrawn, duplicate, no longer relevant). |
| Resolved | Terminal | The issue was fixed. |

### 6.2 Workflow Rules

1. An employee submits a ticket (or one arrives by email) → status **Open**, unassigned.
2. A technician picks the ticket (self-assign), or an admin hand-assigns it to a technician → status becomes **In Progress**. Both paths require the ticket to still be Open and unassigned, and are guarded against two people assigning it at the same moment.
3. While the ticket is in any **active** state, its assigned technician (primary or collaborator) may move it to any other status except back to Open — including directly to a terminal state.
4. **Terminal** statuses (Rejected / Closed / Resolved) are final: status, priority, collaborators, and ownership can no longer change, and there is no reopen flow. The requester conversation remains readable, and admins can still delete the ticket.

---

## 7. Ticket Categories

Tickets are classified into four categories to help organize and route issues:

| Category | Examples |
|---|---|
| Network | Wi-Fi not working, VPN issues, internet connectivity problems |
| Hardware | Laptop not turning on, broken keyboard, monitor issues |
| Software | Application crashes, installation requests, software updates |
| Access Request | System access, password resets, permission requests |

---

## 8. Ticket Priorities

Priorities are set by the employee when submitting the ticket, and can be updated by the technician:

| Priority | Description |
|---|---|
| Low | Non-urgent issues that do not affect daily work, such as software installation requests. |
| Medium | Issues that affect productivity but have a workaround, such as email problems. |
| High | Critical issues that block work entirely, such as a server outage or complete device failure. |

---

## 9. System Pages

### 9.1 Login Page

All users log in through a shared login page using credentials provided by the admin. After login, users are redirected based on their role.

### 9.2 Admin Dashboard

The admin dashboard displays key ticket statistics:

- Counter cards grouping the seven statuses: awaiting assignment (Open), being worked on (In Progress), blocked (On Hold / Waiting for Resources), and completed (Resolved / Closed / Rejected).
- A completion percentage and a distribution-by-priority breakdown.
- A technician leaderboard ranking technicians by number of solved tickets, with each one's average satisfaction rating shown alongside (ties broken by the higher average).
- Quick links to the ticket list and user management, and a "Fetch inbox now" action that runs the email intake on demand.

### 9.3 User Management Page (Admin Only)

The admin can manage all system users from this page:

- View a list of all users with their roles, departments, and derived job titles.
- Filter the list by keyword (name/email/ID), role, and department.
- Add new users and assign them a role (Employee or Technician) and a department.
- Edit existing user information.
- Delete users from the system, with safeguards: a user who still owns tickets cannot be deleted (their submitter identity would be lost), and deleting a technician automatically releases their active tickets back to the open queue so no ticket is stranded.

### 9.4 All Tickets Page (Technician View)

Technicians see a list of all open/unassigned tickets. They can browse and pick tickets to work on. Once a technician picks a ticket, it is assigned to them. The list has a filter sidebar (keyword, status, priority, category) and auto-refreshes when a new ticket arrives (for example via email intake).

### 9.5 My Tickets Page (Technician View)

Technicians see a list of tickets they have picked/assigned to themselves. From here they can update the priority and status of each ticket. The ticket detail view also provides a Team panel for adding collaborators or reassigning ownership, and a comment thread for discussion. 

### 9.6 Submit Ticket Page (Employee View)

Employees fill out a form to submit a new ticket with the following fields: title, description, category (dropdown), priority (dropdown), and file attachments.

### 9.7 My Tickets Page (Employee View)

Employees see a list of their own submitted tickets in the same dense filterable table as the technician queue (assigned technician, category, priority and status badges). They can click a ticket to view its details and message the support team through the conversation thread. Once a ticket has been solved, the detail page shows a rating card where they can rate the handling technician (1–5 stars plus an optional comment). *(Conversation and rating added post-baseline.)*

### 9.8 All Tickets Page (Admin View) *(Added post-baseline)*

The admin manages every ticket in the system from a page that mirrors the technician queue: the same dense table (requester — including the real sender of email tickets — department, assigned technician, category, priority, status), the same filter sidebar and auto-refresh, plus a per-ticket delete action with confirmation.

### 9.9 My Ratings Page (Technician View) *(Added post-baseline)*

Each technician has a dedicated page summarizing the satisfaction feedback on the tickets they handled: an overall average rating, the total number of ratings, a star-distribution breakdown, and the list of written comments. The comments are **anonymous** — the technician reads the feedback but never sees which requester wrote it.

### 9.10 Ratings Page (Admin View) *(Added post-baseline)*

The admin sees a performance table for every technician (solved-ticket count, average rating, number of ratings) and a list of all written feedback. Unlike the technician's anonymous view, the admin sees the requester's identity for oversight, and can filter the feedback down to a single technician.

### 9.11 Report Page (Admin View) *(Added post-baseline)*

A printable operations report, with an optional date-range filter and a print button. For the selected window it summarizes: ticket totals; breakdowns by status, priority, and category; completion rate and backlog; the intake-channel split (email vs. web form); resolution-time (SLA) figures (average, fastest, slowest); per-technician performance; and customer-satisfaction (CSAT) figures with recent comments. The page renders in a print-friendly light layout regardless of the active theme.

---

## 10. Authentication & Access Control

The system uses the following authentication approach:

- **No self-registration:** The admin creates all user accounts.
- **Login credentials:** Users log in with email and password provided by the admin.
- **Role-based access control (RBAC):** Each user is assigned a role (Admin, Technician, or Employee).
- **Route protection:** Each page is accessible only to authorized roles using Laravel middleware.
- **Two-factor login by email code (Added post-baseline):** For accounts with a deliverable email address, a correct password is not enough on its own — the system emails a 6-digit one-time code that must be entered before the session is created. The code is stored hashed and expires after 10 minutes, and can be resent. Internal demo/seeded accounts on the company `@nctkap.com` domain are exempt and sign in with password only.
- **Brute-force protection (Added post-baseline):** Login attempts are rate-limited per email + IP address, with a short lockout after several consecutive failures. Code verification and code resend are rate-limited as well.

---

## 11. Technology Stack

| Component | Technology |
|---|---|
| Backend Framework | Laravel (PHP) |
| Frontend | Blade Templates (Laravel default) |
| Database | MySQL |
| Authentication | Laravel built-in authentication |
| File Storage | Laravel Storage (local disk) |
| CSS Framework | Tailwind CSS |
| Email Intake *(post-baseline)* | IMAP (webklex/laravel-imap) |
| Outbound Email *(post-baseline)* | SMTP (Laravel Mail) |

---

## 12. Database Schema

The system requires the following database tables:

### 12.1 Users Table

| Column | Type | Notes |
|---|---|---|
| id | BIGINT (PK) | Auto-increment primary key |
| name | VARCHAR | Full name of the user |
| email | VARCHAR (unique) | Login email address |
| password | VARCHAR | Hashed password |
| role | ENUM | admin, agent, employee (the `agent` value is shown in the UI as "Technician") |
| department | ENUM | it, hr, finance, sales, operations, support (default: operations) — *Added post-baseline* |
| otp_code | VARCHAR (nullable, hashed, hidden) | Hashed one-time login code for two-factor sign-in — *Added post-baseline* |
| otp_expires_at | TIMESTAMP (nullable) | Expiry of the one-time login code — *Added post-baseline* |
| created_at | TIMESTAMP | Account creation date |
| updated_at | TIMESTAMP | Last update date |

### 12.2 Tickets Table

| Column | Type | Notes |
|---|---|---|
| id | BIGINT (PK) | Auto-increment primary key |
| title | VARCHAR | Brief issue description |
| description | TEXT | Detailed issue description |
| category | ENUM | network, hardware, software, access_request |
| priority | ENUM | low, medium, high — set by employee, updatable by technician |
| status | ENUM | open, in_progress, on_hold, waiting_for_resources, rejected, closed, resolved (default: open) — *expanded post-baseline* |
| user_id | BIGINT (FK) | Employee who submitted the ticket |
| agent_id | BIGINT (FK, nullable) | Primary technician assigned to the ticket |
| source_email | VARCHAR (nullable) | Original sender's email for email-sourced tickets — *Added post-baseline* |
| source_name | VARCHAR (nullable) | Original sender's display name for email-sourced tickets — *Added post-baseline* |
| rating | TINYINT (nullable) | Requester satisfaction rating, 1–5 — *Added post-baseline* |
| rating_comment | TEXT (nullable) | Optional written feedback from the requester — *Added post-baseline* |
| rated_at | TIMESTAMP (nullable) | When the requester submitted the rating — *Added post-baseline* |
| resolved_at | TIMESTAMP (nullable) | When the ticket first entered a solved state, for resolution-time reporting — *Added post-baseline* |
| created_at | TIMESTAMP | Submission date |
| updated_at | TIMESTAMP | Last update date |

### 12.3 Attachments Table

| Column | Type | Notes |
|---|---|---|
| id | BIGINT (PK) | Auto-increment primary key |
| ticket_id | BIGINT (FK) | Associated ticket |
| file_path | VARCHAR | Storage path of the file |
| file_name | VARCHAR | Original file name |
| created_at | TIMESTAMP | Upload date |

### 12.4 Comments Table *(Added post-baseline)*

| Column | Type | Notes |
|---|---|---|
| id | BIGINT (PK) | Auto-increment primary key |
| ticket_id | BIGINT (FK) | Associated ticket |
| user_id | BIGINT (FK) | Author of the comment |
| body | TEXT | Comment text |
| visibility | VARCHAR(20) | `internal` (legacy technician notes, no longer displayed) or `reply` (requester-conversation messages) |
| created_at | TIMESTAMP | Post date |
| updated_at | TIMESTAMP | Last update date |

### 12.5 Ticket–Agent Pivot Table (`ticket_agent`) *(Added post-baseline)*

Supports co-assigning multiple technicians (collaborators) to a single ticket.

| Column | Type | Notes |
|---|---|---|
| ticket_id | BIGINT (FK) | Associated ticket — part of composite primary key |
| user_id | BIGINT (FK) | Collaborating technician — part of composite primary key |
| created_at | TIMESTAMP | When the technician was added as a collaborator |

---

## 13. Collaboration Features (Added post-baseline)

These capabilities were added after the original baseline to support real-world team workflows.

### 13.1 Requester Conversation

Each ticket carries a persisted conversation thread between the requester and the handling team, shown on the ticket detail page as chat bubbles. Access is deliberately narrow: the employee who opened the ticket, the ticket's **primary** technician, and admins can read and post — collaborators the ticket was merely shared with cannot, keeping the conversation private to the one handling technician.

When a technician or admin replies, the message is also **emailed to the requester** — for email-sourced tickets, to the original external sender. The employee's own messages stay on-site only. Posting and deleting happen over AJAX without a page reload, and each author (or an admin) can delete their own messages.

> An earlier internal-notes feature (technician-only comments hidden from the requester) was retired from the UI; its data remains in the `comments` table with `visibility = internal` and is never displayed.

### 13.2 Co-Assignment (Collaborators)

A ticket's primary technician (or an admin) can add other technicians as collaborators via the ticket's Team panel. Collaborators gain the same access to the ticket as the primary technician, allowing several technicians to work an issue together. Collaboration is stored in the `ticket_agent` pivot table.

### 13.3 Reassignment

An admin, or the ticket's current primary technician, can transfer ownership of a ticket to a different technician. On transfer, the previous primary technician loses access to the ticket (unless separately added as a collaborator). The operation runs inside a database transaction to keep assignment state consistent.

---

## 14. Email-to-Ticket Intake (Added post-baseline)

The system can ingest support requests sent by email, turning them into tickets automatically.

### 14.1 Behaviour

- An IMAP inbox is polled on a schedule (and via a near-real-time listener) for unread messages.
- Each new message is converted into a ticket: the subject becomes the title, the body becomes the description, with defaults of category = Software, priority = Medium, status = Open.
- Senders can steer the classification from the email itself, checked in this order: explicit `Priority:` / `Category:` lines (English or Arabic) in the subject or body; `[bracket]` tags in the subject (e.g. `[urgent]`, `[شبكة]`); a bare urgency word (urgent / asap / critical / عاجل / طارئ) anywhere in the subject; and finally a keyword guess over the subject words. Arabic comparisons normalize spelling variants (hamza/alef forms, alef maqsura). Recognised tags are stripped from the ticket title; anything unrecognised falls back to the defaults above.
- If the sender's email matches a known user, the ticket is attributed to that user; otherwise it is attributed to a placeholder "External Sender" account, while the real sender's address and name are preserved in `source_email` / `source_name` for admin visibility.
- Supported email attachments (JPG, PNG, PDF) are imported and stored like web-form attachments.
- Processed messages are marked as read so they are never imported twice (idempotent).

### 14.2 Auto-Acknowledgement

After a ticket is created from an email, the system sends the sender a confirmation email containing the ticket reference and subject. Automated/system addresses (no-reply, mailer-daemon, postmaster, internal help-desk addresses) are skipped to prevent mail loops. Delivery failures are logged but never abort the import.

In parallel, an **admin notification email** describing the new ticket (title, sender, category, priority, a body excerpt, and a link to the ticket) is sent to a configured notification address, or to all admin accounts when none is configured. Both emails are queued so sending never blocks the intake process.

### 14.3 Configuration

Email intake and outbound mail use standard IMAP and SMTP settings supplied via environment configuration. When IMAP credentials are not configured, the intake process exits cleanly and the rest of the system is unaffected.

> **Deployment note:** in the demo environment the whole system — web server, database,
> and email intake — runs on the presenter's machine. The intake is a `tickets:fetch-emails --interval=5`
> loop started automatically at Windows logon, and that same loop drains the outbound
> mail queue. This is intentional demo scaffolding chosen for a zero-infrastructure
> presentation. A production deployment would move the application unchanged to an
> always-on server, with a supervised `queue:work` process and an OS cron invoking the
> Laravel scheduler replacing the logon script.

---

## 15. Satisfaction Ratings & Reporting (Added post-baseline)

These capabilities were added after the original baseline to measure service quality and give the admin an operational view of the help desk.

### 15.1 Requester Satisfaction Rating

Once a ticket reaches a **solved** terminal state (Resolved or Closed) **and** a technician actually handled it, the employee who opened it can rate that technician from 1 to 5 stars and add an optional written comment. Rejected tickets, and solved tickets that were never assigned to a technician, cannot be rated. A rating can be updated later (re-rating overwrites the previous one). The rating, comment, and timestamp are stored on the ticket.

### 15.2 Technician "My Ratings"

Each technician has a personal ratings page (see [Section 9.9](#99-my-ratings-page-technician-view-added-post-baseline)) showing their overall average, rating count, star distribution, and written feedback. **Feedback is anonymous to the technician** — the query that drives this page deliberately excludes the requester's identity, so a technician can read the comments but never learn who wrote them.

### 15.3 Admin Ratings View & Leaderboard

- The admin dashboard shows a **technician leaderboard** ranked by solved-ticket count, with each technician's average rating shown alongside (ties broken by the higher average).
- A dedicated admin **Ratings page** (see [Section 9.10](#910-ratings-page-admin-view-added-post-baseline)) lists every technician's performance and all written feedback. Unlike the technician's anonymous view, the admin sees the requester's identity for oversight and can filter the feedback by a specific technician.

### 15.4 Resolution-Time (SLA) Tracking & Printable Report

- When a ticket first enters a solved state, the system stamps a `resolved_at` timestamp, which enables measuring resolution time (submission → resolved). The stamp is set once and never overwritten.
- A dedicated admin **Report page** (see [Section 9.11](#911-report-page-admin-view-added-post-baseline)) presents an operations summary for an optional date-range window: ticket totals, breakdowns by status/priority/category, completion rate and backlog, the intake-channel split (email vs. web), resolution-time (SLA) figures (average / fastest / slowest), per-technician performance, and customer-satisfaction (CSAT) figures with recent comments. It renders in a print-friendly layout.

> **Note:** resolution-time data is only captured for tickets resolved through the workflow after this feature shipped; tickets that were seeded, imported, or resolved earlier show no SLA figure until they next pass through a solved state.

---

## 16. User Interface & Localization (Added post-baseline)

- **Bilingual UI:** every user-facing page is available in English and Arabic. A sidebar toggle switches the language per session, and Arabic renders fully right-to-left (mirrored sidebar, tables, and forms). Dates and enum labels (statuses, priorities, categories, departments) are translated.
- **Dark mode:** a sidebar toggle switches between light and dark themes, persisted in the browser and applied before first paint to avoid flashing.
- **Job titles:** users are displayed with a title derived from role + department — System Administrator (admin), Technician (agent), and per-department employee titles (IT Specialist, HR Specialist, Accountant, Sales Representative, Operator, Support Officer) — shown in the user list and under the signed-in user's name.
- **Consistent filtering:** the technician queue, the admin ticket list, the employee ticket list, and the user management page all share the same filter-sidebar pattern (keyword search plus enum checkboxes/chips) with filter-preserving pagination.
- **Live behaviour:** ticket lists auto-refresh when new tickets arrive, and conversation messages post and delete over AJAX without page reloads.
- **Status/priority badges:** all lists render status and priority as colored pill badges (priority: high = red, medium = amber, low = gray).

---

## 17. Non-Functional Requirements

- **Security:** Passwords must be hashed. Routes must be protected by role-based middleware.
- **Validation:** All forms must have server-side validation for required fields and file types.
- **File Uploads:** Supported formats should include images (JPG, PNG) and documents (PDF). Maximum file size should be defined.
- **Responsive Design:** The interface should work well on desktop and tablet screens.
- **Performance:** The system should handle the expected number of company employees without performance issues.

---

## 18. Out of Scope

The following features remain intentionally excluded from the delivered system:

- Push notifications.
- Ticket escalation workflow.
- Self-registration for users.
- Multi-company or SaaS support.
- Public API endpoints for mobile or third-party integration.

> **Note:** Several items originally listed as out of scope or future work were subsequently
> implemented and are now documented in-line: email notifications and ticket comments/threads
> ([Section 13](#13-collaboration-features-added-post-baseline) and
> [Section 14](#14-email-to-ticket-intake-added-post-baseline)), and reporting/analytics beyond
> the dashboard counters plus SLA resolution-time tracking
> ([Section 15](#15-satisfaction-ratings--reporting-added-post-baseline)).

---

## 19. Future Considerations

The following features could be added in future versions:

- Email notifications to employees/technicians when ticket **status** changes (the current email integration covers inbound intake and a submission acknowledgement, not status-change alerts).
- Ticket escalation to senior technicians or managers.
- Knowledge base for common IT issues and solutions.
- SLA **targets** with automated breach alerts (the delivered system measures and reports resolution time — see [Section 15.4](#154-resolution-time-sla-tracking--printable-report) — but does not yet enforce response-time targets or send breach notifications).

> **Note:** "Advanced reporting and analytics" and basic "SLA tracking" — originally listed here
> as future work — were delivered as the admin Ratings and Report pages
> ([Section 15](#15-satisfaction-ratings--reporting-added-post-baseline)).