<?php

namespace App\Http\API\V1\Repositories\Product;

use App\Http\API\V1\Repositories\BaseRepository;
use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    
}
