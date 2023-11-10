<?php

namespace App\Http\API\V1\Repositories\Role;

use App\Http\API\V1\Core\PaginatedData;
use App\Http\API\V1\Repositories\BaseRepository;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
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

        return parent::filter(Role::class, $filters, $sorts);
    }

    public function indexPermissions(Role $role): PaginatedData
    {
        $filters = [
            AllowedFilter::partial('id'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
        ];

        $sorts = [
            AllowedSort::field('id'),
            AllowedSort::field('name'),
            AllowedSort::field('description'),
        ];

        return $this->filter($role->permissions(), $filters, $sorts);
    }

    public function editPermissions(Role $role, $data): void
    {
        $role->permissions()->sync(array_values($data)[0]);
    }
}
