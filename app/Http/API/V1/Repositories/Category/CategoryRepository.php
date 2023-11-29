<?php

namespace App\Http\API\V1\Repositories\Category;

use App\Http\API\V1\Core\PaginatedData;
use App\Http\API\V1\Repositories\BaseRepository;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function index(): PaginatedData
    {
        $filters = [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('parent_id'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
        ];
        $sorts = [
            AllowedSort::field('id'),
            AllowedSort::field('parent_id'),
            AllowedSort::field('name'),
            AllowedSort::field('description'),
        ];

        return parent::filter(Category::class, $filters, $sorts);
    }

    public function store($data): Category|Model
    {
        $category = parent::store($data);
        $category->save();
        $category->refresh();

        return $category;
    }

    public function update(Category|Model $category, $data): Category|Model
    {
        $categoryupdetede = parent::Update($category, $data);
        $categoryupdetede->refresh();

        return $categoryupdetede;
    }
}
