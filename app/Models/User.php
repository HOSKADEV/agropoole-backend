<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    //'firstname',
    //'lastname',
    'city_id',
    'name',
    'email',
    'phone',
    'image',
    'password',
    'role',
    'status',
    'fcm_token',
    'enterprise_name',
    'longitude',
    'latitude'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function city()
  {
    return $this->belongsTo(City::class);
  }

  public function products()
  {
    return $this->hasMany(Product::class);
  }

  public function stocks()
  {
    return $this->hasMany(Stock::class);
  }

  public function deliveries()
  {
    return $this->hasMany(Delivery::class, 'driver_id');
  }

  public function carts()
  {
    return $this->hasMany(Cart::class);
  }

  // Orders where user is the seller
  public function soldOrders()
  {
    return $this->hasMany(Order::class, 'seller_id');
  }

  // Orders where user is the buyer
  public function boughtOrders()
  {
    return $this->hasMany(Order::class, 'buyer_id');
  }

  public function deliveredOrders()
  {
    return $this->hasManyThrough(Order::class, Delivery::class, 'driver_id', 'id', 'id', 'order_id');
  }

  public function pendingSoldOrders()
  {
    return $this->soldOrders()->whereIn('status', ['pending', 'confirmed']);
  }

  // Orders where user is the buyer
  public function pendingBoughtOrders()
  {
    return $this->boughtOrders()->whereIn('status', ['accepted', 'delivered']);
  }

  public function pendingDeliveredOrders()
  {
    return $this->deliveredOrders()->whereIn('status', ['shipped', 'ongoing'])->select('orders.*');
  }
  public function orders()
  {

    return $this->soldOrders()->union($this->boughtOrders());
  }

  public function notifications()
  {
    return $this->hasMany(Notification::class);
  }

  public function fullname()
  {
    //return $this->firstname . ' ' . $this->lastname;
    return $this->name;
  }

  public function enterprise()
  {
    //return $this->firstname . ' ' . $this->lastname;
    return empty($this->enterprise_name) ? $this->name : $this->enterprise_name;
  }

  public function phone()
  {
    /* return is_null($this->phone) ? null : '0'.$this->phone; */
    return $this->phone;
  }

  public function cart()
  {
    $cart = $this->carts()->where('type', 'current')->first();

    if (is_null($cart)) {
      $cart = Cart::create(['user_id' => $this->id, 'type' => 'current']);
    } else {
      foreach ($cart->items as $item) {
        if (is_null($item->product)) {
          $item->delete();
        }
      }
    }

    return $cart;
  }

  public function location()
  {
    return 'https://maps.google.com/?q=' . $this->latitude . ',' . $this->longitude;
  }

  public function address()
  {
    return $this->city?->state?->name . '/' . $this->city?->name;
  }

  public function notify($title, $content)
  {
    $controller = new Controller();
    if ($this->fcm_token) {
      $controller->send_fcm_device($title, $content, $this->fcm_token);
    }
  }

  public function role_is($role = null)
  {

    $roles = [
      0 => 'admin',
      1 => 'provider',
      2 => 'broker',
      3 => 'store',
      4 => 'client',
      5 => 'driver',
    ];

    return $role ? $roles[$this->role] == $role : $roles[$this->role];
  }

  public function image()
  {
    $image = 'assets/img/avatars/avatar.png';
    if ($this->role_is('admin')) {
      $image = 'logo.png';
    } else if ($this->image) {
      $image = $this->image;
    }

    return url($image);
  }

  public function can_see_stock_of($user)
  {
    if (empty($user)) {
      return false;
    }

    $permissions = [
      'admin' => [],
      'provider' => ['broker'],
      'broker' => ['provider', 'store'],
      'store' => ['broker'],
      'client' => [],
      'driver' => []
    ];

    return in_array($user->role_is(), $permissions[$this->role_is()]) ? true : false;
  }


  public function topStocks($date = null)
  {
    return $this->stocks()->withCount([
      'items' => function ($query) use ($date) {
        if ($date) {
          $query->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month);
        }
      }
    ])->having('items_count', '>', 0)->orderBy('items_count', 'DESC');
  }

  public function topProducts($date = null)
  {
    return $this->stocks()->join('items', function ($query) use ($date) {
      $query->on('items.stock_id', '=', 'stocks.id');
      if ($date) {
        $query->whereYear('items.created_at', $date->year)->whereMonth('items.created_at', $date->month);
      }
    })
      ->join('products', 'stocks.product_id', 'products.id')
      ->groupBy('product_id')
      ->selectRaw('products.*, COUNT(items.id) AS items_count')
      //->having('items_count', '>', 0)
      ->orderBy('items_count', 'DESC');
  }
  public function topBuyers($date = null)
  {
    $result = $this->soldOrders();

    if ($date) {
      $result->whereYear('orders.created_at', $date->year)->whereMonth('orders.created_at', $date->month);
    }

    return $result->join('users', 'orders.buyer_id', 'users.id')
      ->groupBy('buyer_id')
      ->selectRaw('users.*, COUNT(orders.id) AS orders')
      ->orderBy('orders', 'DESC');
  }

  public function topSellers($date = null)
  { //for driver
    $result = $this->deliveredOrders();
    if ($date) {
      $result = $result->whereYear('deliveries.created_at', $date->year)->whereMonth('deliveries.created_at', $date->month);
    }

    return $result->join('users', 'orders.seller_id', 'users.id')
      ->groupBy('seller_id')
      ->selectRaw('users.*, COUNT(orders.id) AS orders')
      ->orderBy('orders', 'DESC');
  }

  public function topStates($date = null)
  {
    $result = $this->soldOrders();

    if ($date) {
      $result->whereYear('orders.created_at', $date->year)->whereMonth('orders.created_at', $date->month);
    }
    return $result->join('users', 'orders.seller_id', 'users.id')
      ->join('cities', 'users.city_id', 'cities.id')
      ->join('states', 'cities.state_id', 'states.id')
      ->groupBy('state_id')
      ->selectRaw('states.*, COUNT(orders.id) AS orders_count')
      ->orderBy('orders_count', 'DESC');

  }
}
