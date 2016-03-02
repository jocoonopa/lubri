<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Model\User;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReminderEmail extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param  Mailer  $mailer
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        // $mailer->send('emails.reminder', ['user' => $this->user], function ($m) {
        //     $m->to('jocoonopa@chinghwa.com.tw', '小閎')->subject("{$this->user->username}帳號修改通知");
        // });
    }
}
