<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Helper\Flap\RetailSaleByPersonTypeHelper;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Helper\MailHelper;
use Carbon\Carbon;

class RetailSaleByPersonTypeController extends Controller
{
	protected $startDate;
	protected $endDate;

	public function process()
	{
		$emps = Processor::getArrayResult($this->getQb($this->setStartDate()->getStartDate(), $this->setEndDate()->getEndDate()), 'Pos');
		
		with(new RetailSaleByPersonTypeHelper($emps, $this->getStartDate()))->createAndStore();
		
		return with(new MailHelper($this->getMailConfig()))->mail(RetailSaleByPersonTypeHelper::COMPLETE_MSG);
	}

	protected function setStartDate()
	{
		$this->startDate = new Carbon('first day of last month');

		return $this;
	}

	protected function getStartDate()
	{
		return $this->startDate;
	}

	protected function setEndDate()
	{
		$this->endDate = new Carbon('last day of last month');

		return $this;
	}

	protected function getEndDate()
	{
		return $this->endDate;
	}

	protected function getMailConfig()
	{
		$subject = RetailSaleByPersonTypeHelper::TITLE . $this->getStartDate()->format('Ym');

		return [
			'template' => 'emails.creditCard',
			'title' => $subject,
			'subject' => $subject,
			'cc' => $this->getCCList(),
			'to' => $this->getToList(),
			'filepath' => [RetailSaleByPersonTypeHelper::getFileRealPathWithDate($this->getStartDate()->format('Ym'))]
		];
	}

	protected function getToList()
    {
        return [
            'lingying3025@chinghwa.com.tw' => '6521吳俐穎',
            'meganlee@chinghwa.com.tw' => '6500李惠淑',
            'amy@chinghwa.com.tw' => '6221李佩蓉'
        ];
    }

    protected function getCCList()
    {
        return [
            'sl@chinghwa.com.tw' => '6700莊淑玲',
            'swhsu@chinghwa.com.tw' => '6800徐士偉',
            'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘',
            'jocoonopa@chinghwa.com.tw' => '6231小閎'
        ];
    }

	public function getQb(Carbon $startDate, Carbon $endDate)
	{
		return str_replace(['$startDate', '$endDate'], [$startDate->format('Ymd'), $endDate->format('Ymd')], file_get_contents(__DIR__ . '/../../../../storage/sql/RetailSale/person.sql'));
	}
}