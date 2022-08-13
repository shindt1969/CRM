<?php

namespace Webkul\Core\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use Webkul\Admin\Http\Controllers\Controller;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

abstract class Repository extends BaseRepository implements CacheableInterface {

    use CacheableRepository;

    /**
     * Find data by field and value
     *
     * @param  string  $field
     * @param  string  $value
     * @param  array  $columns
     * @return mixed
     */
    public function findOneByField($field, $value = null, $columns = ['*'])
    {
        $model = $this->findByField($field, $value, $columns = ['*']);

        return $model->first();
    }

    /**
     * Find data by field and value
     *
     * 有可能會被 core.php 呼叫
     * @param  string  $field
     * @param  string  $value
     * @param  array  $columns
     * @return mixed
     */
    public function findOneWhere(array $where, $columns = ['*'])
    {
        $model = $this->findWhere($where, $columns);

        return $model->first();
    }

    /**
     * Find data by id
     *
     * @param  int  $id
     * @param  array  $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->find($id, $columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by id
     *
     * @param  int  $id
     * @param  array  $columns
     * @return mixed
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        try{
            $model = $this->model->findOrFail($id, $columns);
        }catch(ModelNotFoundException $e){
            Log::info("\package\webkul\core\src\Eloquent\repository->findOrFail: try to get invalid id=> ".$id);
            throw new HttpResponseException(Controller::ReturnJsonFailMsg(config('app.error_code.can_not_find_this_record')));
        }

        $this->resetModel();

        return $this->parserResult($model);
    }

     /**
     * Count results of repository
     *
     * @param  array  $where
     * @param  string  $columns
     * @return int
     */
    public function count(array $where = [], $columns = '*')
    {
        $this->applyCriteria();
        $this->applyScope();

        if ($where) {
            $this->applyConditions($where);
        }

        $result = $this->model->count($columns);
        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * @param  string  $columns
     * @return mixed
     */
    public function sum($columns)
    {
        $this->applyCriteria();
        $this->applyScope();

        $sum = $this->model->sum($columns);
        $this->resetModel();

        return $sum;
    }

    /**
     * @param  string  $columns
     * @return mixed
     */
    public function avg($columns)
    {
        $this->applyCriteria();
        $this->applyScope();

        $avg = $this->model->avg($columns);
        $this->resetModel();

        return $avg;
    }

    /**
     * @return mixed
     */
    public function getModel($data = [])
    {
        return $this->model;
    }

    public function update(array $attributes, $id)
    {
        try{
            return parent::update($attributes, $id);
        }catch(ModelNotFoundException $e){
            throw new HttpResponseException(Controller::ReturnJsonFailMsg(config('app.error_code.can_not_find_this_record')));
        }
    }

    public function search(array $where, $columns = ['*'])
    {
        $model = parent::findWhere($where, $columns);

        if (is_null($model->first())){
            Log::info("\package\webkul\core\src\Eloquent\repository->search: record not found");
            throw new HttpResponseException(Controller::ReturnJsonFailMsg(config('app.error_code.can_not_find_this_record')));
        };

        return $model->first();
    }

}
