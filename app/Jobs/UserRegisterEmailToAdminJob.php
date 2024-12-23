<?php

namespace App\Jobs;

use App\Mail\UserRegisterEmailToAdmin;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class UserRegisterEmailToAdminJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $emailTemplate;
    protected $password;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user, EmailTemplate $emailTemplate, $password)
    {
        $this->user = $user;
        $this->emailTemplate = $emailTemplate;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to('asim.shahzad20@gmail.com') // will be replaced to admin email
        ->send(new UserRegisterEmailToAdmin($this->user, $this->emailTemplate, $this->password));
    }
}
