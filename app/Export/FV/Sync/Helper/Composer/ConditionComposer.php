<?php

namespace App\Export\FV\Sync\Helper\Composer;

use Input;

class ConditionComposer
{
    public static function composeEngConditions()
    {
        return [
            'agentCD'    => Input::get('eng_emp_codes', []),
            'sourceCD'   => Input::get('eng_source_cds', []),
            'campaignCD' => Input::get('eng_campaign_cds', []),
            'assignDate' => trim(Input::get('eng_assign_date'))
        ];
    }

    public static function composeFlapConditions()
    {
        return [
            'empCodes'    => Input::get('flap_emp_codes', []),
            'memberCodes' => Input::get('flap_source_cds', [])
        ];
    }

    public static function composeMixedConditions()
    {
        return [
            'eng'  => self::composeEngConditions(), 
            'flap' => self::composeFlapConditions()
        ];
    }
}