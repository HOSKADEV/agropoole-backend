<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
      'driver_id',
      'order_id',
      'delivered_at'
    ];

    public function driver(){
      return $this->belongsTo(User::class, 'driver_id');
    }

    public function order(){
      return $this->belongsTo(Order::class);
    }

    public function notify($type){

      $controller = new Controller();

      if($this->order?->seller?->fcm_token){
        $controller->send_fcm_device(__('Order n°').$this->order_id , __('delivery.seller.'.$type), $this->order->seller->fcm_token);
      }
      if($this->driver?->fcm_token){
        $controller->send_fcm_device(__('Order n°').$this->order_id , __('delivery.driver.'.$type), $this->driver->fcm_token);
      }
    }
}
