<?php

namespace App\Models;

use App\Enums\Shippingmethed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    use HasFactory;

    protected $casts = ['shipping_method' => Shippingmethed::class];
}
