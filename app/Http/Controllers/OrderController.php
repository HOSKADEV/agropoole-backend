<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use App\Models\Set;
use App\Models\Cart;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Driver;
use App\Models\History;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\OrderCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\PaginatedOrderCollection;
use Kreait\Firebase\Exception\FirebaseException;

class OrderController extends Controller
{

  public function index()
  {
    //$drivers = Driver::all();
    //$shipping = Set::where('name', 'shipping')->first();
    return view('content.orders.list');
      //->with('drivers', $drivers)
      //->with('shipping', $shipping);
  }

  public function inbox()
  {
    $drivers = User::where('role', 5)->where('status','ACTIVE')->get();
    //$shipping = Set::where('name', 'shipping')->first();
    return view('content.orders.inbox')
      ->with('drivers', $drivers);
      //->with('shipping', $shipping);
  }

  public function outbox()
  {
    //$drivers = User::where('role', 5)->where('status','ACTIVE')->get();
    //$shipping = Set::where('name', 'shipping')->first();
    return view('content.orders.outbox');
      //->with('drivers', $drivers);
      //->with('shipping', $shipping);
  }

  public function info($id){
    $order = Order::findOrFail($id);

    return view('content.orders.info')
    ->with('order',$order);
  }

  public function distance(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'longitude' => 'required|string',
      'latitude' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 0,
        'message' => $validator->errors()->first()
      ]);
    }
    try {

      $start_point = [
        'lng' => 33.386027,
        'lat' => 6.839005
      ];

      $end_point = [
        'lng' => floatval($request->longitude),
        'lat' => floatval($request->latitude)
      ];

      $distance = $this->calc_distance($start_point, $end_point);
      /* $true_price = ($distance/1000) * 20;
      $actual_price = min(max($true_price,100),500); */
      $price = $this->delivery_price($distance);

      return response()->json(
        [
          'status' => 1,
          'data' => [
            'distance' => number_format($distance / 1000, 2, '.', ','),
            'price' => number_format($price, 2, '.', ',')
          ]
        ]
      );

    } catch (Exception $e) {
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }
  }
  /*  public function create(Request $request)
   {
     $validator = Validator::make($request->all(), [

       'phone' => 'required|numeric|digits:10',
       'longitude' => 'required|string',
       'latitude' => 'required|string',
       //'products' => 'required|array',
       //'products.*.id' => 'required|distinct|exists:products,id',
       //'products.*.quantity' => 'required|numeric'
     ]);

     if ($validator->fails()) {
       return response()->json([
         'status' => 0,
         'message' => $validator->errors()->first()
       ]);
     }
     try {
       $user = auth()->user();


       $items = $user->cart()->items;

       if($items->count() == 0){
         throw new Exception(__('empty cart'));
       }

       $cart = Cart::create(['user_id' => $user->id , 'type' => 'order']);

       foreach ($items as $item) {
         $quantity = $item->quantity;
         $product = Product::find($item->product_id);
         $discount = is_null($product->discount()) ? 0 : $product->discount()->amount;
         $product->add_to_cart($cart->id,$quantity,$discount);
       }

       $request->merge(['user_id' => $user->id, 'cart_id' => $cart->id]);

       $order = Order::create($request->all());

       $start_point = [
         'lng' => 33.386027,
         'lat' => 6.839005
       ];

       $end_point = [
         'lng' => floatval($request->longitude),
         'lat' => floatval($request->latitude)
       ];

       $distance = $this->calc_distance($start_point,$end_point);
       //$true_price = ($distance/1000) * 20;
       //$actual_price = min(max($true_price,100),500);
       $price = $this->delivery_price($distance);

       $invoice = Invoice::create([
         'order_id' => $order->id,
         //'tax_type' => $request->tax_type,
         'tax_amount' => $price,
       ]);

       $invoice->total();

       $cart = $user->cart();

       $cart->delete();

       $admin_tokens = User::where('role',0)->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

       $this->send_fcm_multi(__('New order'),__('There is a new order pending'),$admin_tokens);

       return response()->json([
         'status' => 1,
         'message' => 'success',
         'data' => new OrderResource($order)
       ]);

     } catch (Exception $e) {
       return response()->json(
         [
           'status' => 0,
           'message' => $e->getMessage()
         ]
       );
     }
   } */


  public function create(Request $request)
  {
    if (count(session('cart',[]))) {
      $request->mergeIfMissing(['stocks' => session('cart')]);
      session()->put(['cart' => []]);
    }

    $validator = Validator::make($request->all(), [

      'phone' => 'required|numeric|digits:10',
      'longitude' => 'required|string',
      'latitude' => 'required|string',
      'stocks' => 'required|array',
      'stocks.*.stock_id' => 'required|distinct|exists:stocks,id',
      'stocks.*.quantity' => 'required|numeric'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 0,
        'message' => $validator->errors()->first()
      ]);
    }
    //dd($request->all());
    try {

      DB::beginTransaction();

      $buyer = $request->user();

      $stock_ids = array_column($request->stocks, 'stock_id');
      $seller_ids = Stock::whereIn('id', $stock_ids)->distinct('user_id')->pluck('user_id')->toArray();

      foreach ($seller_ids as $seller_id) {

        $request->merge([
          'buyer_id' => $buyer->id,
          'seller_id' => $seller_id,
        ]);

        $order = Order::create($request->except('stocks'));

        History::create(['order_id' => $order->id, 'user_id' => $buyer->id]);

        $cart = Cart::create(['order_id' => $order->id]);

        $stocks = array_filter($request->stocks, function ($value) use ($seller_id) {
          return Stock::where('id', $value['stock_id'])->where('user_id', $seller_id)->count();
        });

        foreach ($stocks as $stock) {
          $quantity = $stock['quantity'];
          $stock = Stock::find($stock['stock_id']);
          $stock->add_to_cart($cart->id, $quantity);
        }

        $order->refresh();
        $order->notify();
      }

      DB::commit();



      return response()->json([
        'status' => 1,
        'message' => 'success',
        //'data' => new OrderResource($order)
      ]);

    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }
  }


  /* public function update(Request $request){

    //dd($request->only(['status','note']));

    $validator = Validator::make($request->all(), [
      'order_id' => 'required|exists:orders,id',
      'driver_id' => 'sometimes|exists:drivers,id',
      'status' => 'sometimes',
      //'tax_type' => 'sometimes|in:1,2',
      'tax_amount' => 'sometimes|numeric',
      'payment_method' => 'sometimes|in:1,2',
      'note' => 'sometimes'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 0,
        'message' => $validator->errors()->first()
      ]);
    }
    try {

      $order = Order::find($request->order_id);

      if($request->has('status')){

        if($request->status == 'ongoing'){

          Delivery::create([
            'order_id' => $request->order_id,
            'driver_id' => $request->driver_id,
          ]);

          $invoice = $order->invoice;

          $invoice->pdf();
        }

        if($request->status == 'delivered'){

          $now = Carbon::now()->toDateString();
          $invoice = $order->invoice;
          $delivery = $order->delivery;

          $invoice->is_paid = 'yes';
          $invoice->paid_at = $now;
          $invoice->payment_method = $request->payment_method;

          $delivery->delivered_at = $now;

          $invoice->save();
          $delivery->save();

        }


        //$order->status = $request->status;
        //$order->save();

        $this->send_fcm_device(
          __('Order status update'),
          __('Your order is '.$request->status),
          $order->user->fcm_token
        );

      }

      //dd($request->only(['status','note']));

      $order->update($request->only(['status','note']));


      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => $order
      ]);

    } catch (Exception $e) {
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }

  } */


  public function update(Request $request)
  {

    //dd($request->only(['status','note']));

    $validator = Validator::make($request->all(), [
      'order_id' => 'required|exists:orders,id',
      'driver_id' => 'sometimes|exists:users,id',
      'status' => 'sometimes|in:pending,accepted,canceled,confirmed,shipped,ongoing,delivered,received',
      //'tax_type' => 'sometimes|in:1,2',
      //'tax_amount' => 'sometimes|numeric',
      //'payment_method' => 'sometimes|in:1,2',
      'note' => 'sometimes'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 0,
        'message' => $validator->errors()->first()
      ]);
    }
    try {

      DB::beginTransaction();

      $user = $request->user();

      $order = Order::find($request->order_id);

      if ($request->has('status')) {

        /*  if($request->status == 'shipped'){

           $delivery = $order->deliveries()->where('driver_id',$request->driver_id ?? $request->user()->id)->first();

           if(empty($delivery)){
             throw new Exception(__('the selected driver is not assigned to the order'));
           }

           Delivery::where('order_id', $request->order_id)
           ->whereNot('id', $delivery->id)
           ->delete();
         } */

        if ($request->status == 'received') {

          foreach ($order->cart->items as $item) {
            $item->refresh_stocks();
          }

        }


        History::create(['order_id' => $order->id, 'user_id' => $user->id, 'status' => $request->status]);

      }

      if ($request->has('driver_id')) {
        /* Delivery::updateOrInsert([
          'order_id' => $request->order_id,
          'driver_id' => $request->driver_id,
          'deleted_at' => null
        ],[
          'created_at' => Carbon::now(),
          'updated_at' => Carbon::now(),
        ]); */

        $order->deliveries()->delete();
        Delivery::create([
          'order_id' => $request->order_id,
          'driver_id' => $request->driver_id,
        ]);
      }

      //dd($request->only(['status','note']));

      $order->update($request->only(['status', 'note']));


      DB::commit();


      if ($request->has('status')) {
        $order->refresh();
        $order->notify();

        if ($request->status == 'received') {

          foreach ($order->cart->items as $item) {
            $item->stock->notify();
          }

        }
      }




      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new OrderResource($order)
      ]);

    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }

  }


  public function delete(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'order_id' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json(
        [
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try {

      $order = Order::findOrFail($request->order_id);
      $cart = $order->cart;

      $order->delete();
      $cart->delete();

      return response()->json([
        'status' => 1,
        'message' => 'success',
      ]);

    } catch (Exception $e) {
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }

  }

  public function restore(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'order_id' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json(
        [
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try {

      $order = Order::withTrashed()->findOrFail($request->order_id);

      $order->restore();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new OrderResource($order)
      ]);

    } catch (Exception $e) {
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }

  }

  public function get(Request $request)
  {

    $validator = Validator::make($request->all(), [
      //'order_id' => 'sometimes',
      'type' => 'required|in:1,2,3',
      'status' => 'sometimes|in:pending,accepted,canceled,confirmed,shipped,ongoing,delivered,received'
    ]);

    if ($validator->fails()) {
      return response()->json(
        [
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try {

      $user = $request->user();

      $orders = match (intval($request->type)) {
        1 => Order::where('buyer_id', $user->id),
        2 => Order::where('seller_id', $user->id),
        3 => Order::join('deliveries', function ($join) use ($user) {
            $join->on('orders.id', '=', 'deliveries.order_id');
            $join->where('deliveries.driver_id', '=', $user->id);
            $join->where('deliveries.deleted_at', '=', null);
          })->select('orders.*')
      };

      if ($request->has('status')) {

        $orders = $orders->where('status', $request->status);

      }

      $orders = $orders->orderBy('updated_at', 'DESC');

      if ($request->has('all')) {

        $orders = new OrderCollection($orders->get());

      } else {
        $orders = new PaginatedOrderCollection($orders->paginate(10));
      }


      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => $orders
      ]);

    } catch (Exception $e) {
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }
  }



}
