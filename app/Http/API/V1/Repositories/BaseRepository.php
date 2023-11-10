<?php

namespace App\Http\API\V1\Repositories;

use App\Http\API\V1\Core\PaginatedData;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseRepository
{
    protected Model $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function filter($relation, $filters = [], $sorts = [], $defaultSorts = ['-id'], $perPage = 15): PaginatedData
    {
        $per_page = request('per_page', $perPage);
        $entities = QueryBuilder::for($relation)
            ->defaultSorts($defaultSorts)
            ->allowedFilters($filters)
            ->allowedSorts($sorts);

        return new PaginatedData($entities, $per_page);
    }

    public function index(): PaginatedData
    {
        return $this->filter($this->model, [], []);
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function store($data): Model
    {
        return $this->model::create($data);
    }

    public function update(Model $model, $attributes): Model
    {
        $model->fill($attributes);
        $model->save();

        return $model;
    }

    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }
}
