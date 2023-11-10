<?php

namespace App\Http\API\V1\Repositories\Permission;

use App\Http\API\V1\Core\PaginatedData;
use App\Http\API\V1\Repositories\BaseRepository;
use Spatie\Permission\Models\Permission;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class PermissionRepository extends BaseRepository
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function index(): PaginatedData
    {
        $filters = [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
        ];
        $sorts = [
            AllowedSort::field('id'),
            AllowedSort::field('name'),
            AllowedSort::field('description'),
        ];

        return parent::filter(Permission::class, $filters, $sorts);
    }
}
