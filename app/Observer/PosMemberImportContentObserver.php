<?php

namespace App\Observer;

use App\Model\Flap\PosMemberImportContent;

class PosMemberImportContentObserver 
{
    public function updating(PosMemberImportContent $content)
    {   
        PosMemberImportContent::unsetEventDispatcher();

        $className = $content->task()->first()->kind()->first()->observer;

        return with(new $className)->updatingContent($content);
    }    
}