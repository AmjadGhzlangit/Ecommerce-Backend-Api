<?php

namespace App\Http\API\V1\Repositories\ShopOrder;

use App\Http\API\V1\Core\PaginatedData;
use App\Http\API\V1\Repositories\BaseRepository;
use App\Models\ShopOrder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ShopOrderRepository extends BaseRepository
{
    public function __construct(ShopOrder $model)
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
