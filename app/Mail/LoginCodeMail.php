<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One-time login verification code. Deliberately NOT queued: the user is
 * sitting on the verify screen waiting for it, and the demo environment only
 * drains the queue on the email-listener cycle.
 */
class LoginCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $userName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رمز الدخول — Login code: '.$this->code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.login-code',
        );
    }
}
