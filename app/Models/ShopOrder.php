<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_line_id',
        'Payment_method',
        'Shipping_address',
        'order_total',
        'shipping_method',
        'order_status',
    ];

    protected $casts = ['order_status' => OrderStatus::class];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class, 'order_id');
    }
}
