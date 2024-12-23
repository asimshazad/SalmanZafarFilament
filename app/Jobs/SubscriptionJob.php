<?php

namespace App\Jobs;

use App\Mail\SubscriptionMail;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->mailData['user_email'])
        ->send(new SubscriptionMail($this->mailData, $this->emailTemplate, $this->subject_title));
    }
}
