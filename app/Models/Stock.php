<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    protected $fillable = [
      'user_id' ,
      'product_id' ,
      'price' ,
      'quantity' ,
      'min_quantity' ,
      'show_price'
    ];

    public function product(){
      return $this->belongsTo(Product::class);
    }

    public function owner(){
      return $this->belongsTo(User::class, 'user_id');
    }
}
