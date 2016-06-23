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

        $callLists = CampaignCallList::fetchCtiRes([
            'agentCD'    => $this->getAgentCD(),
            'sourceCD'   => !empty(Input::get('source_cd')) ? explode(',', trim(Input::get('source_cd'))) : [],
            'campaignCD' => !empty(Input::get('campaign_cd')) ? explode(',', trim(Input::get('campaign_cd'))) : [],
            'assignDate' => trim(Input::get('assign_date'))
        ]);

        return $export->setFile($this->appendToFile($this->getMembers($callLists, ['sourceCD' => Input::get('source_cd')])));
    }

    /**
     * 專員代號和部門條件有關連，這邊邏輯示若專員代號條件為空，則專員代號為部門條件的所以專員代號;
     * 若專員代號條件不為空，則和部門條件的集合取交集;
     * 若專員代號條件集合不為空，部門條件為空，則取專員代號條件集合
     * 
     * @return array
     */
    protected function getAgentCD()
    {
        $agentCD = [];

        $code = !empty(Input::get('code')) ? explode(',', trim(Input::get('code'))) : [];
        $codeFromCorp = [];
        $corpCodeStr = sqlInWrap(Input::get('corps', []));

        if (!empty($corpCodeStr)) {
            $codeFromCorp = array_pluck(Processor::getArrayResult("SELECT HRS_Employee.Code FROM HRS_Employee LEFT JOIN FAS_CORP ON HRS_Employee.CorpSerNo = FAS_CORP.SerNo WHERE FAS_CORP.Code IN ({$corpCodeStr})"), 'Code');
        }

        if (empty($code)) {
            $agentCD = $codeFromCorp;
        } else if (!empty($codeFromCorp)) {
            foreach ($agentCD as $key => $val) {
                if (!in_array($val, $codeFromCorp)) {
                    unset($agentCD[$key]);
                }
            }
        }

        return $agentCD;
    }

    /**
     * 如果 $callLists 為空，表示沒有從 CTI 那邊撈取到任何資料，因此走 getMemberDependOnFlap 的流程，
     * 反之則要走 getMemberDependBothFlapAndCTI 的流程
     * 
     * @param  array  $callLists
     * @param  array  $options  
     * @return array
     */
    protected function getMembers(array $callLists, array $options)
    {
        return empty($callLists) ? $this->getMemberDependOnFlap($options) : $this->getMemberDependBothFlapAndCTI($callLists, $options);   
    }

    protected function getMemberDependOnFlap(array $options)
    {  
        return array_filter($this->getCTILayoutData(array_get($options, 'sourceCD')), [$this, 'filterMember']);
    }

    /**
     * 原本是長這樣:
     *  
     *  foreach ($callLists as $calllist) {           
     *   $member              = array_get($this->getCTILayoutData(array_get($calllist, 'SourceCD')), 0);    
     *   $member['AgentCD']   = array_get($calllist, 'AgentCD');
     *   $member['AgentName'] = array_get($calllist, 'AgentName');
     *
     *   $members[] = $member;
     *  }  
     *
     * 因為需要提升效能且不需要使用到 CTI 資料，因此改為以下版本
     * 
     * @param  array $callLists
     * @param  array $options  
     * @return array           
     */
    protected function getMemberDependBothFlapAndCTI($callLists, $options)
    {
        $members = [];

        $members = $this->getCTILayoutData(array_pluck($callLists, 'SourceCD'));

        return array_filter($members, [$this, 'filterMember']);
    }

    /**
     * 從瑛聲取得的會員代碼，傳入此函式取得其在輔翼的資料。
     *
     * 這邊由於瑛聲拋過來的資料可能會很大， TSQL 在 IN 條件很多的時候效能會非常差，
     * 因此這邊理應改寫成分段執行以利效能提升
     * 
     * @param  array $memberCode
     * @return array
     */
    public function getCTILayoutData($memberCode)
    {
        $data = [];

        $memberCodeChunks = array_chunk($memberCode, self::CHUNK_SIZE);

        foreach ($memberCodeChunks as $chunk) {
            $sql = str_replace('$memberCode', sqlInWrap($chunk), Processor::getStorageSql('CTILayout.sql'));
            
            $data = array_merge(Processor::getArrayResult($sql), $data);
        }
                        
        return $data;      
    }

    /**
     * @deprecated This method depracted at 2016-06-17
     */
    protected function inCorps(array $member)
    {
        $corps = Input::get('corps');

        if (empty($corps)) {
            return true;
        }

        return in_array(array_get($member, '部門'), $corps);
    }

    protected function filterMember($member)
    {
        return NULL !== $member;
    }

    protected function appendToFile(array $members)
    {
        if (!file_exists(storage_path('excel/exports/ctilayout/'))) {
            mkdir(storage_path('excel/exports/ctilayout/'), 0777);
        }

        $fname = storage_path('excel/exports/ctilayout/') . str_replace(',', '-', Input::get('campaign_cd')) . '_' . time() . '.csv';

        $file = fopen($fname, 'w');

        foreach ($members as $member) {
            $appendStr = implode(',', $this->getMould()->getRow($member));
            $appendStr = cb5($appendStr);

            fwrite($file, $appendStr . "\r\n");
        }   

        fclose($file);

        return $fname;
    }

    public function getCampaigns()
    {
        return Campaign::findValid();
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