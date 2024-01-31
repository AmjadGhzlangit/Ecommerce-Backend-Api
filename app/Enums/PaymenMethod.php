<?php

namespace App\Enums;


enum PaymenMethod: string
{
    case CASH_ON_DELIVERY = 'CASH_ON_DELIVERY';
    case CREDIT_CARD = 'CREDIT_CARD';
    case PAYPAL = 'PAYPAL';


}


