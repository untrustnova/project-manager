<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Leave;

class LeaveStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $leave;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Leave $leave, $status)
    {
        $this->leave = $leave;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Leave Request ' . ucfirst($this->status) . ' - Project Manager',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.leave-status',
            with: [
                'leave' => $this->leave,
                'status' => $this->status,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
