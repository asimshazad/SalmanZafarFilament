<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $mailData;
    protected $emailTemplate;
    protected $subject_title;
    /**
     * Create a new job instance.
     */
    public function __construct($mailData, EmailTemplate $emailTemplate, $subject_title)
    {
        $this->mailData = $mailData;
        $this->emailTemplate = $emailTemplate;
        $this->subject_title = $subject_title;
    }

        /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this
            //->from($this->emailTemplate->from_email, $this->emailTemplate->from_name)
            ->subject($this->parseEmailSubject())
            ->view('emails.systemEmailTemplate')
            ->with([
                'emailContent' => $this->parseEmailContent(),
            ]);
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

    /**
     * Parse the email content with dynamic data.
     *
     * @return string
     */
    public function parseEmailContent()
    {
        $parsedContent = $this->emailTemplate->content;

        $parsedContent = str_replace('{{ user_name }}', $this->mailData['user_name'], $parsedContent);
        $parsedContent = str_replace('{{ body }}', $this->mailData['body'], $parsedContent);
        $parsedContent = str_replace('{{ title }}', $this->subject_title, $parsedContent);
        $parsedContent = str_replace('{{ current_year }}', date("Y"), $parsedContent);

        return $parsedContent;
    }

    public function parseEmailSubject()
    {
        $parsedSubject = $this->emailTemplate->email_subject;

        $parsedSubject = str_replace('{{ subject }}', $this->subject_title, $parsedSubject);

        return $parsedSubject;
    }
}