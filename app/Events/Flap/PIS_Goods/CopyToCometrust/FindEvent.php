<?php

namespace App\Events\Flap\PIS_Goods\CopyToCometrust;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FindEvent extends Event
{
    use SerializesModels;

    const EXPLODE_CHAR = ' ';

    protected $codes      = [];
    protected $goodses    = [];

    private $replaceChars = [',', '，', '、', ';', '&', '#'];

    /**
     * Create a new event instance.
     *
     * @param mixed $codes
     * @return void
     */
    public function __construct($codestr = NULL)
    {
        if (!empty($codestr)) {
            $codestr = str_replace($this->replaceChars, self::EXPLODE_CHAR, $codestr);

            $this->setCodes(explode(self::EXPLODE_CHAR, $codestr));
        }
    }

    public function setCodes(array $codes)
    {
        $this->codes = $codes;

        return $this;
    }

    public function getCodes()
    {
        return $this->codes;
    }

    public function setGoodses(array $goodses)
    {
        $this->goodses = $goodses;

        return $this;
    }

    public function getGoodses()
    {
        return $this->goodses;
    }

    public function getMassCodes()
    {
        return array_diff($this->getCodes(), array_pluck($this->getGoodses(), 'Code'));
    }
}
