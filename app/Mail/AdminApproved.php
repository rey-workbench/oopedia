<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminApproved extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(private User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun Admin Disetujui',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-approved',
            with: ['user' => $this->user],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
