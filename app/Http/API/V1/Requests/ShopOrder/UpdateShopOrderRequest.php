<?php

namespace App\Http\API\V1\Requests\ShopOrder;


use App\Enums\OrderStatus;
use App\Enums\PaymenMethod;
use App\Enums\ShippingMethed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShopOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'order_line_id' => [
            Rule::exists('order_lines', 'id'),
                              ],
           'Shipping_address' => ['string'],
           'order_total' => ['numeric'],
           'Payment_method' => [Rule::enum(PaymenMethod::class)],
           'shipping_method' => [Rule::enum(ShippingMethed::class)],
           'order_status' => [Rule::enum(OrderStatus::class)],

        ];
    }
}
