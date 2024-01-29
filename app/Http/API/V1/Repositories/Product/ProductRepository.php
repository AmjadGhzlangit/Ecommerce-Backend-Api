<?php

namespace App\Http\API\V1\Repositories\Product;

use App\Http\API\V1\Core\PaginatedData;
use App\Http\API\V1\Repositories\BaseRepository;
use App\Models\Product;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function index(): PaginatedData
    {
        $filters = [
            AllowedFilter::partial('name'),
        ];

        $sorts = [
            AllowedSort::field('name'),
        ];

        return $this->filter($filters, $sorts);
    }
}
