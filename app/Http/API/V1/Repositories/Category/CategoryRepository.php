<?php

namespace App\Http\API\V1\Repositories\Category;

use App\Http\API\V1\Core\PaginatedData;
use App\Http\API\V1\Repositories\BaseRepository;
use App\Models\Category;
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
            AllowedFilter::partial('id'),
            AllowedFilter::partial('name'),
        ];

        $sorts = [
            AllowedSort::field('id'),
            AllowedSort::field('name'),
        ];

        return $this->filter($filters, $sorts);

    }
}
