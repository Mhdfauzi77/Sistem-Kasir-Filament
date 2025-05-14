<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Factories\HasFactory;


class Setting extends Model

{
    use HasFactory;
    protected $fillable = [
        'shop',
        'address',
        'phone',
    ];

    // public function getShopAttribute($value)
    // {
    //     return $value;
    // }

    // public function getAddressAttribute($value)
    // {
    //     return $value;
    // }

    // public function getPhoneAttribute($value)
    // {
    //     return $value;
    // }
}
