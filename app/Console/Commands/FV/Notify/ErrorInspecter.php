<?php

namespace App\Console\Commands\FV\Notify;

use App\Model\Log\FVSyncQue;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Mail;

class ErrorInspecter extends Command
{
    const SUBHOURS = 8;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fv:ei';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect if there are ques stuck';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ques = FVSyncQue::where('updated_at', '<=', Carbon::now()->subHours(self::SUBHOURS))
        ->whereIn('status_code', [
            FVSyncQue::STATUS_INIT,
            FVSyncQue::STATUS_WRITING,
            FVSyncQue::STATUS_IMPORTING
        ])->get();

        return 0 === $ques->count() ? NULL : $this->notify($ques);
    }

    protected function notify($ques)
    {
        return Mail::send('emails.fv.errorinspect', ['ques' => $ques], function ($m) {
            $m
                ->subject("同步排程阻塞通知" . Carbon::now()->format('Y-m-d H:i:s'))
                ->to([
                    'jocoonopa@gmail.com' => '洪小閎',
                    'selfindex@chinghwa.com.tw' => '李濬凡'
                ])
            ;
        });
    }
}
