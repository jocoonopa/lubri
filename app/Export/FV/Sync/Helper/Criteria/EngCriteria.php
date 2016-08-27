<?php 

namespace App\Export\FV\Sync\Helper\Criteria;

class EngCriteria
{
    protected $options;
    protected $conditions;

    public function apply(array $engOptions)
    {
        return $this->setOptions($engOptions)->handle();
    }

    public function getWhereStr()
    {
        return $this->convertConditionToSql();
    }

    protected function convertConditionToSql()
    {
        return implode(' AND ', $this->getConditions());
    }

    protected function handle()
    {
        return $this
            ->handleEmpCodes()
            ->handleSourceCD()
            ->handleCampaignCds()
            ->handleAssignDate()
        ;
    }

    protected function handleEmpCodes()
    {
        $agentCD = array_get($this->getOptions(), 'agentCD');

        if (empty($agentCD)) {
            return $this;
        }

        $this->conditions[] = 'CampaignCallList.AgentCD IN (' . sqlInWrap($agentCD) . ')';

        return $this;
    }

    protected function handleSourceCD()
    {
        $sourceCD = array_get($this->getOptions(), 'sourceCD');

        if (empty($sourceCD)) {
            return $this;
        }

        $this->conditions[] = 'CampaignCallList.SourceCD IN (' . sqlInWrap($sourceCD) . ')';

        return $this;
    }

    protected function handleCampaignCds()
    {
        $campaignCD = array_get($this->getOptions(), 'campaignCD');
        
        if (empty($campaignCD)) {
            return $this;
        }

        $this->conditions[] = 'CampaignCallList.CampaignCD IN (' . sqlInWrap($campaignCD) . ')';

        return $this;
    }

    protected function handleAssignDate()
    {
        $assignDate = array_get($this->getOptions(), 'assignDate');

        if (empty($assignDate)) {
            return $this;
        }
        
        $this->conditions[] = "CampaignCallList.AssignDate >= '{$assignDate}'";

        return $this;
    }

    /**
     * Gets the value of options.
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the value of options.
     *
     * @param mixed $options the options
     *
     * @return self
     */
    protected function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Gets the value of conditions.
     *
     * @return mixed
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Sets the value of conditions.
     *
     * @param mixed $conditions the conditions
     *
     * @return self
     */
    protected function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }
}