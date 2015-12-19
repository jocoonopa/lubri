<?php

namespace App\Handlers\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent;

use App\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent;
use App\Utility\Chinghwa\Helper\Flap\PIS_Goods\FixCPrefixGoods\DataHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class MailNotifyEvent
{
	protected $to = [
		'vivian@chinghwa.com.tw'     => '謝玉英',
		'melodyhong@chinghwa.com.tw' => '洪鑾英',
		'pyeh@chinghwa.com.tw'       => '葉晴慧',
		'adam@chinghwa.com.tw'       => '丁東煌'
	];

	protected $cc = [
		'jocoonopa@chinghwa.com.tw' => '洪小閎'
	];

	protected $subject;

	public function __construct()
	{
		$this->subject = '輔翼商品轉贈品修改紀錄通知' . date('Y-m-d H:i:s');
	}

    /**
     * Handle the event.
     *
     * @param  FixCPrefixGoodsEvent  $event
     * @return void
     */
    public function handle(FixCPrefixGoodsEvent $event)
    {
        $converts = with(new DataHelper)->fetchGoodsesBySerNos(array_keys($event->getOriginGoodses()));

    	Mail::send(
    		'emails.flap.PIS_Goods.fixCprefix', 
    		[
    			'originGoodses' => $event->getOriginGoodses(),
    			'convertGoodses' => $converts, 
    			'masses' => $event->getMassCodesList(), 
    			'title' => $this->subject
    		], 
    		function ($m) {
    			$m->subject($this->subject)->to($this->to)->cc($this->cc);
    	});

    	return $converts;
    }
}
