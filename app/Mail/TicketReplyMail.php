<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent synchronously (NOT queued): a technician posts this from the web app and
 * the only queue worker in the demo topology is the email listener, so a queued
 * reply might sit undelivered until its next tick — or never, if IMAP is off.
 * The requester conversation post itself is already persisted before we try to
 * send, and CommentController swallows send failures, so a slow/failing SMTP
 * never breaks the reply.
 */
class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $replyBody  the technician's reply text
     * @param  string  $agentName  who is replying (shown to the requester)
     */
    public function __construct(
        public Ticket $ticket,
        public string $replyBody,
        public string $agentName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رد على تذكرتك — Ticket #'.$this->ticket->id.': '.$this->ticket->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-reply',
        );
    }
}
