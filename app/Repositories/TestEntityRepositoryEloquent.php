<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TestEntityRepository;
use App\Entities\TestEntity;
use App\Validators\TestEntityValidator;

/**
 * Class TestEntityRepositoryEloquent
 * @package namespace App\Repositories;
 */
class TestEntityRepositoryEloquent extends BaseRepository implements TestEntityRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TestEntity::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return TestEntityValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
