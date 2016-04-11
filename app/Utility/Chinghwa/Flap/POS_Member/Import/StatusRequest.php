<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

abstract class StatusRequest {
    protected $content;
    protected $status = 0;

    public function __construct($content)
    {
        $this->setContent($content);
    }

    /**
     * Gets the value of content.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the value of content.
     *
     * @param mixed $content the content
     *
     * @return self
     */
    protected function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the value of status.
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the value of status.
     *
     * @param mixed $status the status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $this->status|$status;

        return $this;
    }
}