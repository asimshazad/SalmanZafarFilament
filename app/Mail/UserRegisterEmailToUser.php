<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisterEmailToUser extends Mailable
{
    use Queueable, SerializesModels;

    public $emailTemplate;

    public $user;

    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, EmailTemplate $emailTemplate, $password)
    {
        $this->emailTemplate = $emailTemplate;
        $this->user = $user;
        $this->password = $password;
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

        $parsedContent = str_replace('{{ name }}', $this->user->name, $parsedContent);
        $parsedContent = str_replace('{{ password }}', $this->password, $parsedContent);
        $parsedContent = str_replace('{{ email }}', $this->user->email, $parsedContent);
        $parsedContent = str_replace('{{ current_year }}', date("Y"), $parsedContent);

        return $parsedContent;
    }
}
