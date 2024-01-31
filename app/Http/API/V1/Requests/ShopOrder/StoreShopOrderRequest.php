<?php

namespace App\Http\API\V1\Requests\ShopOrder;

use App\Enums\OrderStatus;
use App\Enums\PaymenMethod;
use App\Enums\ShippingMethed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShopOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_line_id' => ['required',
             Rule::exists('order_lines', 'id'),
                               ],
            'Shipping_address' => ['required', 'string'],
            'order_total' => ['required', 'numeric'],
            'Payment_method' => ['required', Rule::enum(PaymenMethod::class)],
            'shipping_method' => ['required', Rule::enum(ShippingMethed::class)],
            'order_status' => ['required', Rule::enum(OrderStatus::class)],




        ];
    }
}
