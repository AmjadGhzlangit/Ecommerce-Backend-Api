<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class UserNameFilter implements Filter
{
    public function __construct()
    {
    }

    public function __invoke(Builder $query, $value, string $property): Builder
    {

        $query->where('first_name', 'like', '%' . $value . '%')
            ->get();

        return $query;
    }
}
