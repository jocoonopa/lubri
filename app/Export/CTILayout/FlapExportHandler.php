<?php
/*
 * This file is extends of Class Command.
 *
 * (c) Jocoonopa <jocoonopa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Export\CTILayout;

use App\Model\State;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use App\Utility\Chinghwa\ORM\CTI\Campaign;
use App\Utility\Chinghwa\ORM\CTI\CampaignCallList;
use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use App\Export\Mould\FVMemberMould;
use Input;

class FlapExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    const CHUNK_SIZE = 50;
    const LIST_COUNT_LIMIT = 5000;

    protected $mould;

    /**
     * ExcelHelper::rmi('Z') === 25
     *
     * ps: 要增加一個方法，根據 code 和 corps 決定回傳的 agentCD
     * 
     * @param  App\Export\CTILayout\Export $export
     * @return App\Export\CTILayout\Export $export
     */
    public function handle($export)
    {
        $this->setMould(new FVMemberMould);

        $callLists = [];

        $engOptions = [
            'agentCD'    => Input::get('eng_emp_codes', []),
            'sourceCD'   => Input::get('eng_source_cd', []),
            'campaignCD' => Input::get('eng_campaign_cds', []),
            'assignDate' => trim(Input::get('eng_assign_date'))
        ];

        if (!$this->isIgnoreEngCondition($engOptions)) {
            $count = CampaignCallList::fetchCtiResCount($engOptions);
        
            if (self::LIST_COUNT_LIMIT < $count) {
                throw new \Exception('瑛聲名單數目過多(超過' . self::LIST_COUNT_LIMIT . '筆)，請重新設定查詢條件!');
            }

            $callLists = CampaignCallList::fetchCtiRes($engOptions);
        }
        
        $flapOptions = [
            'empCodes'    => Input::get('flap_emp_codes', []),
            'memberCodes' => Input::get('flap_source_cds', [])
        ];

        return $export->setFile($this->proc($callLists, $flapOptions));
    }

    protected function isIgnoreEngCondition(array $engOptions)
    {
        $bool = true;

        foreach ($engOptions as $eachCondition) {
            if (!empty($eachCondition)) {
                $bool = false;

                break;
            }
        }

        return $bool;
    }

    protected function proc(array $callLists, array $flapOptions)
    {
        $members = array_pluck($callLists, 'SourceCD');
        $members = array_merge(array_get($flapOptions, 'memberCodes'), $members);

        return $this->getCTILayoutData($members, $flapOptions);   
    }

    public function getCTILayoutData(array $memberCodes, array $flapOptions)
    {
        $empCodes = array_get($flapOptions, 'empCodes', []);

        if (!empty($empCodes)) { 
            $q = Processor::table('Customer_lubri')->whereIn('emp_id', $empCodes);

            $memberCodes = array_merge(array_pluck(Processor::getArrayResult($q), 'cust_id'), $memberCodes);
        }   

        return $this->appendToFile($memberCodes);
    }

    protected function appendToFile(array $memberCodes)
    {
        if (!file_exists(storage_path('excel/exports/ctilayout/'))) {
            mkdir(storage_path('excel/exports/ctilayout/'), 0777);
        }

        $fname = storage_path('excel/exports/ctilayout/') . 'cti_' . time() . '.csv';

        $file = fopen($fname, 'w');
        fwrite($file, bomstr());

        $memberCodeChunks = array_chunk($memberCodes, self::CHUNK_SIZE);
        
        foreach ($memberCodeChunks as $chunk) {
            $sql = str_replace('$memberCode', sqlInWrap($chunk), Processor::getStorageSql('CTILayout.sql'));
            
            foreach (Processor::getArrayResult($sql) as $member) {
                $appendStr = implode(',', $this->getMould()->getRow($member));

                fwrite($file, $appendStr . "\r\n");
            }   
        }

        fclose($file);

        return $fname;
    }

    /**
     * Gets the value of mould.
     *
     * @return mixed
     */
    public function getMould()
    {
        return $this->mould;
    }

    /**
     * Sets the value of mould.
     *
     * @param mixed $mould the mould
     *
     * @return self
     */
    protected function setMould($mould)
    {
        $this->mould = $mould;

        return $this;
    }
}