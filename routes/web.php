<?php

use App\Http\Controllers\AgentTicketController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Two-factor login: after a correct password, a 6-digit code is emailed and
// must be entered here before the session is signed in.
Route::get('/login/verify', [LoginController::class, 'showVerifyForm'])->name('login.verify');
Route::post('/login/verify', [LoginController::class, 'verify'])->name('login.verify.submit');
Route::post('/login/verify/resend', [LoginController::class, 'resend'])->name('login.verify.resend');

Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

// Language switcher — no auth so it also works from the login screen.
Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, SetLocale::SUPPORTED, true), 404);
    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Requester-conversation routes; policy-gated (not role-gated) so admin, primary
// technician, and the employee owner can all act here.
Route::middleware('auth')->group(function () {
    // Delete a conversation message; gated by CommentPolicy::delete (author OR admin).
    Route::delete('/tickets/{ticket}/comments/{comment}', [CommentController::class, 'destroy'])->name('tickets.comments.destroy');

    // Post a conversation message (emails the requester when a technician/admin
    // replies); gated by TicketPolicy::converse.
    Route::post('/tickets/{ticket}/reply', [CommentController::class, 'reply'])->name('tickets.reply');

    // Share/reassign are gated by TicketPolicy (admin OR current primary), not by role,
    // so they live outside the role:agent group so admins can act here too.
    // Admin hand-assigns an open ticket to a technician; gated by TicketPolicy::assignToAgent.
    Route::post('/tickets/{ticket}/admin-assign', [AgentTicketController::class, 'adminAssign'])->name('tickets.admin-assign');

    // Correct a ticket's category; gated by TicketPolicy::updateCategory (admin OR
    // primary technician). Outside role:agent so admins can fix miscategorised
    // (e.g. email-auto-detected) tickets too.
    Route::patch('/tickets/{ticket}/category', [AgentTicketController::class, 'updateCategory'])->name('tickets.category');

    Route::post('/tickets/{ticket}/share', [AgentTicketController::class, 'share'])->name('tickets.share');
    Route::delete('/tickets/{ticket}/share/{user}', [AgentTicketController::class, 'unshare'])->name('tickets.unshare');
    Route::patch('/tickets/{ticket}/reassign', [AgentTicketController::class, 'reassign'])->name('tickets.reassign');

    // Hand a ticket back to the shared queue (clears the primary + collaborators
    // and reopens it); gated by TicketPolicy::release (admin OR current primary).
    Route::patch('/tickets/{ticket}/release', [AgentTicketController::class, 'release'])->name('tickets.release');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/ratings', [DashboardController::class, 'ratings'])->name('admin.ratings');
    Route::get('/admin/report', [DashboardController::class, 'report'])->name('admin.report');
    Route::get('/admin/tickets', [DashboardController::class, 'tickets'])->name('admin.tickets.index');
    Route::delete('/admin/tickets/{ticket}', [DashboardController::class, 'destroyTicket'])->name('admin.tickets.destroy');

    Route::resource('admin/users', UserController::class)
        ->names('users')
        ->except(['show']);
});

// Agent-only literals (/tickets and /tickets/mine) MUST be declared before
// the wildcard /tickets/{ticket} below, or Laravel binds "mine" as a ticket
// id and returns 404 via implicit binding.
Route::middleware(['auth', 'role:agent'])->group(function () {
    Route::get('/tickets', [AgentTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/mine', [AgentTicketController::class, 'mine'])->name('tickets.mine');
    Route::get('/my-ratings', [AgentTicketController::class, 'ratings'])->name('tickets.ratings');
});

// Read-only ticket show is open to agents AND admins so admins can use the
// Team panel (share / reassign). Write actions below remain agent-only.
Route::middleware(['auth', 'role:admin,agent'])->group(function () {
    // Literal route — must stay above the /tickets/{ticket} wildcard. Polled by
    // the ticket lists to auto-refresh when a new ticket arrives.
    Route::get('/tickets/latest-id', [AgentTicketController::class, 'latestId'])->name('tickets.latest-id');
    Route::get('/tickets/{ticket}', [AgentTicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/attachments/{attachment}', [AgentTicketController::class, 'viewAttachment'])->name('tickets.attachment');
});

Route::middleware(['auth', 'role:agent'])->group(function () {
    Route::post('/tickets/{ticket}/assign', [AgentTicketController::class, 'assign'])->name('tickets.assign');
    Route::patch('/tickets/{ticket}/status', [AgentTicketController::class, 'updateStatus'])->name('tickets.status');
    Route::patch('/tickets/{ticket}/priority', [AgentTicketController::class, 'updatePriority'])->name('tickets.priority');
});

Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/my-tickets', [TicketController::class, 'index'])->name('my-tickets.index');
    Route::get('/my-tickets/create', [TicketController::class, 'create'])->name('my-tickets.create');
    Route::post('/my-tickets', [TicketController::class, 'store'])->name('my-tickets.store');
    Route::get('/my-tickets/{ticket}', [TicketController::class, 'show'])->name('my-tickets.show');
    Route::post('/my-tickets/{ticket}/rate', [TicketController::class, 'rate'])->name('my-tickets.rate');
    Route::get('/my-tickets/{ticket}/attachments/{attachment}', [TicketController::class, 'downloadAttachment'])->name('my-tickets.attachment');
});
