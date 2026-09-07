<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTicketAdminNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $senderEmail  the original email sender (source_email)
     * @param  string|null  $senderName  the original sender's display name
     */
    public function __construct(
        public Ticket $ticket,
        public string $senderEmail,
        public ?string $senderName = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New email ticket #'.$this->ticket->id.' — '.$this->ticket->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-ticket-admin',
        );
    }
}
