<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'udid',
        'fcm_token',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
