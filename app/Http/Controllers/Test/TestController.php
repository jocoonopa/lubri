<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\SendReminderEmail;
use App\Model\User;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Input;
use IronMQ\IronMQ;
use Storage;
use Redis;
use Mail;

class TestController extends Controller
{
    public function mail()
    {
        return Mail::raw(__CLASS__ . '###', function ($m) {
            $m->to('jocoonopa@chinghwa.com.tw')->subject('the test');
        });
    }

    public function redis()
    {
        Redis::set('user:name', 'Taylor');

        $user = Redis::get('user:name');

        dd($user);
    }

    public function slack()
    {
        // Instantiate with defaults, so all messages created
        // will be sent from 'Cyril' and to the #accounting channel
        // by default. Any names like @regan or #channel will also be linked.
        $settings = [
            'username'   => env('SLACK_USERNAME'),
            'channel'    => env('SLACK_CHANNEL'),
            'link_names' => true
        ];

        $client = new \Maknz\Slack\Client(env('SLACK_WEBHOOKS'), $settings);

        $client->send('Test Message!');
        $client->to(array_get($settings, 'channel'))->send('Are we rich yet?');
        $client->from('lubri')->to(array_get($settings, 'channel'))->send('Adventure time!');

        return '';
    }

    /**
     * php artisan queue:listen --queue=TestQue
     */
    public function iron()
    {
        // $ironmq = new IronMQ(array(
        //     'project_id' => env('IRON_PROJECT_ID'),
        //     'token' => env('IRON_TOJEN'),
        //     'host' => env('IRON_HOST')
        // ));

        // $ironmq->ssl_verifypeer = false;

        // $ironmq->postMessage(env('IRON_QUEUE'), "Test Message FROM " . __CLASS__ . ':' . __FUNCTION__);

        $job = with(new SendReminderEmail(User::find(89)))->onQueue(env('IRON_QUEUE'))->delay(15);
        $this->dispatch($job);
        
        return __CLASS__ . ':' . __FUNCTION__ . ':' . env('IRON_QUEUE');
    }

    public function testwatcher()
    {
        //throw new \Exception('error');
        // pr(Input::all());

        Storage::disk('local')->prepend('..\logs\watcher.log', 'Call at' . Carbon::now()->format('Ymd H:i:s') . ',data ' . json_encode(Input::all()));

        return __FUNCTION__;
    }

    public function exportfile()
    {   
        $fileName = time();

        Storage::disk('local')->put($fileName, 'Contents');

        return $fileName;
    }

    public function backmail() 
    {
        set_time_limit(0);

        $startTime = microtime(true);

        $chunkSize = 200;
        $r = explode (',', file_get_contents(__DIR__ . '/backemail.txt'));

        $realGetCountTotal = 0;
        $totalNum = count($r);

        for ($i = 0; $i < $totalNum; $i = $i + $chunkSize) {
            $partialArr = array_slice($r, $i, $chunkSize);
            
            $data = Processor::getArrayResult($this->getQuery($partialArr));
            $realGetCount = count($data);

            echo "{$i}:realGet:{$realGetCount}<br/>";
            Processor::execErp($this->getUpdateQuery(array_pluck($data, 'SerNo')));

            $realGetCountTotal += $realGetCount;
        }

        $endTime = microtime(true);

        dd("費時:" . floor($endTime - $startTime) . ",共計{$realGetCountTotal}人");
    }

    protected function getQuery($partialArr) 
    {
        return "SELECT POS_Member.SerNo, POS_Member.Code, POS_Member.Name, POS_Member.E_Mail, CCS_MemberFlags.Distflags_6  FROM POS_Member WITH(NOLOCK) LEFT JOIN CCS_MemberFlags WITH(NOLOCK) ON CCS_MemberFlags.MemberSerNoStr=POS_Member.SerNo WHERE POS_Member.E_Mail IN(" . implode(',', $partialArr) . ")";
    }

    protected function getUpdateQuery($sernos)
    {
        return "UPDATE CCS_MemberFlags SET Distflags_6='A' WHERE CCS_MemberFlags.MemberSerNoStr IN('" . implode("','", $sernos) . "')";
    }
}
