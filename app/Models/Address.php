<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillabil =
    [
        'unit_number',
        'street_number',
        'street_adders',
        'city',
        'region',
        'post_code',
        'country_id',


    ];
}
