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

use App;
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
    protected $writer;

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
        $engOptions  = $this->composeEngConditions();
        $flapOptions = $this->composeFlapOptions();

        if ($this->setMould(new FVMemberMould)->initWriter()->isIgnoreEngCondition($engOptions)) {
            return $export->setFile($this->fetchFlapDataIntoFileAndGetIt([], $flapOptions));
        }
        
        if (self::LIST_COUNT_LIMIT > CampaignCallList::fetchCtiResCount($engOptions)) {
            return $export->setFile($this->fetchFlapDataIntoFileAndGetIt(CampaignCallList::fetchCtiRes($engOptions), $flapOptions));
        } else {
            throw new \Exception('瑛聲名單數目過多(超過' . self::LIST_COUNT_LIMIT . '筆)，請重新設定查詢條件!');
        }        
    }

    protected function composeEngConditions()
    {
        return [
            'agentCD'    => Input::get('eng_emp_codes', []),
            'sourceCD'   => Input::get('eng_source_cd', []),
            'campaignCD' => Input::get('eng_campaign_cds', []),
            'assignDate' => trim(Input::get('eng_assign_date'))
        ];
    }

    protected function composeFlapOptions()
    {
        return [
            'empCodes'    => Input::get('flap_emp_codes', []),
            'memberCodes' => Input::get('flap_source_cds', [])
        ];
    }

    protected function initWriter()
    {
        return $this->setWriter(App::make('App\Export\FV\Sync\MemberFileWriter'));
    }

    protected function isIgnoreEngCondition(array $engOptions)
    {
        foreach ($engOptions as $eachCondition) {
            if (empty($eachCondition)) {
                continue;
            }

            return false;
        }

        return true;
    }

    protected function fetchFlapDataIntoFileAndGetIt(array $callLists, array $flapOptions)
    {
        $memberCodes = array_pluck($callLists, 'SourceCD');
        $memberCodes = array_merge(array_get($flapOptions, 'memberCodes'), $memberCodes);

        return $this->appendToFile($this->fetchMemberCodes(array_get($flapOptions, 'empCodes', []), $memberCodes)); 
    }

    protected function fetchMemberCodes($empCodes, $memberCodes)
    {
        return empty($empCodes) ? $memberCodes : array_merge(array_pluck(Processor::getArrayResult($this->genFetchMemberCodesQ($empCodes)), 'cust_id'), $memberCodes);
    }

    protected function genFetchMemberCodesQ($empCodes)
    {
        return Processor::table('Customer_lubri')->whereIn('emp_id', $empCodes);
    }

    protected function appendToFile(array $memberCodes)
    {
        $this->getWriter()->open()->put(bomstr());

        foreach (array_chunk($memberCodes, self::CHUNK_SIZE) as $chunk) {            
            foreach ($this->fetchMembers($chunk) as $member) {
                $this->getWriter()->put($this->toStringMember($member));
            }   
        }

        $this->getWriter()->close();

        return $this->getWriter()->getFname();
    }

    protected function fetchMembers(array $chunk)
    {
        return Processor::getArrayResult(str_replace('$memberCode', sqlInWrap($chunk), Processor::getStorageSql('CTILayout.sql')));
    }

    protected function toStringMember(array $member)
    {
        return implode(',', $this->getMould()->getRow($member)) . "\r\n";
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

    /**
     * Gets the value of writer.
     *
     * @return mixed
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Sets the value of writer.
     *
     * @param mixed $writer the writer
     *
     * @return self
     */
    protected function setWriter($writer)
    {
        $this->writer = $writer;

        return $this;
    }
}