<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTWD extends Model
{
    //
    protected $table = 'orders_twd';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'name',
        'address',
        'price',
        'currency'
    ];

    // 自動處理 JSON
    protected $casts = [
        'address' => 'array'
    ];

    // 啟用自動 timestamps
    public $timestamps = true;
}
