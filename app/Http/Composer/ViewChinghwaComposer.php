<?php

namespace App\Http\Composer;

use Illuminate\Contracts\View\View;

class ViewChinghwaComposer
{
    public function compose(View $view)
    {
        return $view->with('github', 'GitHub');
    }
}