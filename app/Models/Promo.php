<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stock_id',
        'target_quantity',
        'new_price',
    ];

    protected $casts = [
        'stock_id' => 'integer',
        'target_quantity' => 'integer',
        'new_price' => 'double',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
