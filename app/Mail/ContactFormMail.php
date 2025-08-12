<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $userMessage;
    public $phone;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $email, $message, $phone = null, $subject = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->userMessage = $message;
        $this->phone = $phone;
        $this->subject = $subject;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $emailSubject = $this->subject ? "رسالة جديدة: {$this->subject} من {$this->name}" : "رسالة جديدة من {$this->name}";

        return new Envelope(
            subject: $emailSubject,
            replyTo: $this->email,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $emailSubject = $this->subject ? "رسالة جديدة: {$this->subject} من {$this->name}" : "رسالة جديدة من {$this->name}";

        return $this->subject($emailSubject)
                    ->view('emails.contact');
    }
}
