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
      return $this->belongsTo(Product::class)->withTrashed();
    }

    public function owner(){
      return $this->belongsTo(User::class, 'user_id');
    }

    public function add_to_cart($cart_id, $quantity){

        Item::create([
          'cart_id' => $cart_id,
          'stock_id' => $this->id,
          'unit_name' => $this->product->unit_name,
          'unit_price' => $this->price,
          'quantity' => $quantity,
          'amount' => $quantity * $this->price
        ]);

    }
}
