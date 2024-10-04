<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
      'cart_id',
      //'product_id',
      'stock_id',
      'unit_name',
      'pack_name',
      'unit_price',
      'pack_price',
      'pack_units',
      'type',
      'quantity',
      'discount',
      'amount'
    ];

    protected $casts = [
      //'product_id' => 'integer',
      'stock_id' => 'integer',
      'cart_id' => 'integer',
      'unit_price' => 'double',
      'pack_price' => 'double',
      'pack_units' => 'integer',
      'quantity' => 'integer',
      'discount' => 'double',
      'amount' => 'double',
    ];

    public function cart(){
      return $this->belongsTo(Cart::class);
    }

    /* public function product(){
      return $this->belongsTo(Product::class);
    } */

    public function stock(){
      return $this->belongsTo(Stock::class)->withTrashed();
    }

    public function name(){
      return $this->type == 'unit' ? $this->unit_name : $this->pack_name;
    }

    public function price(){
      return $this->type == 'unit' ? $this->unit_price : $this->pack_price;
    }

    /* public function amount(){

      $product = $this->product;
      $discount = is_null($product->discount()) ? 0 : $product->discount()->amount;
      $quantity = $this->quantity;
      $amount = 0;

      if ($product->has_pack() && $quantity >= $product->pack_units) {
        $pack_quantity = intdiv($quantity, $product->pack_units);
        $amount += $pack_quantity * ($product->pack_price * (1 - ($discount / 100)));
        $quantity = $quantity % $product->pack_units;

      }
      if ($quantity > 0) {
        $amount += $quantity * ($product->unit_price * (1 - ($discount / 100)));
      }

      return $amount;
    } */

    public function amount(){

      return $this->unit_price * $this->quantity;
    }


    public function refresh_stocks(){
      $seller_stock = $this->stock;
      $seller_stock->quantity -= $this->quantity;
      $seller_stock->save();

      $buyer_stock = Stock::firstOrNew([
        'user_id' => $this->cart->order->buyer_id,
        'product_id' => $seller_stock->product_id,
        'price'=> $this->unit_price,
      ],[
        'quantity' => 0,
        'min_quantity' => 0,
        'show_price' => 0,
        'status'=> 'unavailable'
      ]);

      $buyer_stock->quantity += $this->quantity;
      $buyer_stock->save();
    }

}
