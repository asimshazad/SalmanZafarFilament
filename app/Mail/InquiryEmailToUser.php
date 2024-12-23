<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryEmailToUser extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiry;
    public $emailTemplate;

    /**
     * Create a new message instance.
     */
    public function __construct(Inquiry $inquiry, EmailTemplate $emailTemplate)
    {
        $this->inquiry = $inquiry;
        $this->emailTemplate = $emailTemplate;
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
            ->subject($this->emailTemplate->email_subject)
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

        $parsedContent = str_replace('{{ name }}', $this->inquiry->name, $parsedContent);
        $parsedContent = str_replace('{{ email }}', $this->inquiry->email, $parsedContent);
        $parsedContent = str_replace('{{ phone }}', $this->inquiry->phone, $parsedContent);
        $parsedContent = str_replace('{{ subject }}', $this->inquiry->subject, $parsedContent);
        $parsedContent = str_replace('{{ message }}', $this->inquiry->message, $parsedContent);
        $parsedContent = str_replace('{{ current_year }}', date("Y"), $parsedContent);

        return $parsedContent;
    }
}
