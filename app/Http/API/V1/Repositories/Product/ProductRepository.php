<?php

namespace App\Http\API\V1\Repositories\Product;

use App\Http\API\V1\Core\PaginatedData;
use App\Http\API\V1\Repositories\BaseRepository;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
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

        return parent::filter(Product::class, $filters, $sorts);
    }

    public function store($data): Product|Model
    {
        $product = parent::store($data);
        $product->save();
        $product->refresh();

        return $product;
    }

    public function update(Product|Model $product, $data): Product|Model
    {
        $productupdetede = parent::update($product, $data);
        $productupdetede->refresh();

        return $productupdetede;
    }
}
