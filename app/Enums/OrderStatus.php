<?php

namespace App\Enums;

enum OrderStatus : int
{
    case ORDERED=1;
    
    case IN_TRANSIT=2;

    case DELIVERED=3;

    case CANCELLED=4;

}