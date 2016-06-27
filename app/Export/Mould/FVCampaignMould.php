<?php

namespace App\Export\Mould;

class FVCampaignMould extends FVMould
{
    protected $ingoreHeads = ['modified_at', 'created_at', 'lineNum'];
    protected $dateFileds = ['StartDate', 'EndDate'];

    /**
     * 對每個活動物件處理:
     *    1. 移除不需要的欄位
     *    2. 處理字串 
     *        a. 時間格式轉換為 Y/m/d
     *        b. 過濾全形半形空白, 逗號, 換行符號
     */
    public function getRow(array $campaign)
    {
        $this->removeIgnoreColumn($campaign)->convertDateVal($campaign);

        foreach ($campaign as $colName => $val) {
            $campaign[$colName] = $this->transfer(array_get($campaign, $colName));
        }

        return $campaign;
    }

    /**
     * 將日期轉換為 Y/m/d 格式
     * 
     * @param  array $campaign
     * @return string       
     */
    protected function convertDateVal(&$campaign)
    {
        foreach ($this->dateFileds as $dateField) {
            $campaign[$dateField] = with(new \DateTime($campaign[$dateField]))->format('Y/m/d');
        }

        return $this;
    }
}