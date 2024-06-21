<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
      //'user_id',
      //'cart_id',
      'seller_id',
      'buyer_id',
      'phone',
      'status',
      'longitude',
      'latitude',
      'note',
    ];

    protected $casts = [
      //'user_id' => 'integer',
      //'cart_id' => 'integer',
      'seller_id' => 'integer',
      'buyer_id' => 'integer',
      'longitude' => 'double',
      'latitude' => 'double',
    ];

    protected $softCascade = ['invoice','delivery'];

    /* public function user(){
      return $this->belongsTo(User::class);
    }

    public function cart(){
      return $this->belongsTo(Cart::class);
    } */

    public function cart(){
      return $this->HasOne(Cart::class);
    }

    public function seller(){
      return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer(){
      return $this->belongsTo(User::class, 'buyer_id');
    }

    public function histories(){
      return $this->hasMany(History::class);
    }

    public function items(){
      return $this->cart->items;
    }

    public function invoice(){
      return $this->hasOne(Invoice::class);
    }

    public function delivery(){
      return $this->hasOne(Delivery::class);
    }

    public function phone(){
      /* return is_null($this->phone) ? null : '0'.$this->phone; */
      return $this->phone;
    }

    public function address(){
      return 'https://maps.google.com/?q='.$this->longitude.','.$this->latitude;
    }

}
