<?php

namespace App\Presenters;

use App\Transformers\TestEntityTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class TestEntityPresenter
 *
 * @package namespace App\Presenters;
 */
class TestEntityPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new TestEntityTransformer();
    }
}
