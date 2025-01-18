<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
  use HasFactory, SoftDeletes, SoftCascadeTrait;

  protected $fillable = [
    'user_id',
    'product_id',
    'price',
    'quantity',
    'min_quantity',
    'show_price',
    'status'
  ];

  public function getPriceAttribute($value)
  {
    return floatval($value);
  }

  public function product()
  {
    return $this->belongsTo(Product::class)->withTrashed();
  }

  public function items(){
    return $this->hasMany(Item::class);
  }

  public function owner()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function add_to_cart($cart_id, $quantity)
  {

    Item::create([
      'cart_id' => $cart_id,
      'stock_id' => $this->id,
      'unit_name' => $this->product->unit_name,
      'unit_price' => $this->price,
      'quantity' => $quantity,
      'amount' => $quantity * $this->price
    ]);

  }

  public function in_cart()
  {
    $cart = session()->get('cart') ?? [];
    $stock_id = $this->id;
    //dd($cart);
    foreach($cart as $key => $item){

      if($item['stock_id'] == $stock_id){
        return intval($item['quantity']);
      }
    }
    return 0;

    /* $filtered_array = array_filter($cart, function ($item) use ($stock_id) {
      return $item['stock_id'] == $stock_id;
    });

    if (!empty($filtered_array)) {
      $found_item = current($filtered_array);
      return $found_item['quantity'];
    }

    return 0; */

  }

  public function notify()
  {
    if ($this->owner?->fcm_token && $this->quantity <= $this->min_quantity) {
      $controller = new Controller();
      $controller->send_fcm_device($this->product->unit_name, __('stock.quantity.min'), $this->owner?->fcm_token);
    }
  }
}
