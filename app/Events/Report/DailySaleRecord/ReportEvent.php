<?php

namespace App\Events\Report\DailySaleRecord;

use App\Events\Event;
use App\Export\DailySaleRecord\Export;
use Illuminate\Queue\SerializesModels;

class ReportEvent extends Event
{
    use SerializesModels;

    protected $export;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Export $export)
    {
        $this->setExport($export);
    }

    /**
     * Gets the value of export.
     *
     * @return mixed
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * Sets the value of export.
     *
     * @param mixed $export the export
     *
     * @return self
     */
    protected function setExport($export)
    {
        $this->export = $export;

        return $this;
    }
}
