<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\TestEntity;

/**
 * Class TestEntityTransformer
 * @package namespace App\Transformers;
 */
class TestEntityTransformer extends TransformerAbstract
{

    /**
     * Transform the \TestEntity entity
     * @param \TestEntity $model
     *
     * @return array
     */
    public function transform(TestEntity $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
