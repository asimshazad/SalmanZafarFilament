<?php

namespace App\Jobs;

use App\Mail\InquiryEmailToUser;
use App\Mail\SendInquiryEmailToUser;
use App\Models\EmailTemplate;
use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class InquiryJobToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $inquiry;
    protected $emailTemplate;
    /**
     * Create a new job instance.
     */
    public function __construct(Inquiry $inquiry, EmailTemplate $emailTemplate)
    {
        $this->inquiry = $inquiry;
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->inquiry->email)
        ->send(new InquiryEmailToUser($this->inquiry, $this->emailTemplate));
    }
}
