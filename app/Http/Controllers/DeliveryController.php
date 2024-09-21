<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    public function create(Request $request){
      $validator = Validator::make($request->all(), [
        'order_id' => 'required|exists:orders,id',
        'driver_id' => 'required|exists:users,id',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'status'=> 0,
          'message' => $validator->errors()->first()
        ]);
      }
      try{
        $order = Order::find($request->order_id);

        $delivery = $order->deliveries()->where('driver_id',$request->driver_id)->first();

        if($delivery){
          throw new Exception('delivery already exists');
        }

        $delivery = Delivery::create($request->all());

        $delivery->notify('create');

        return response()->json([
          'status' => 1,
          'message' => 'success',
        ]);

      }catch(Exception $e){
        return response()->json([
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
      }
    }

    public function delete(Request $request){
      $validator = Validator::make($request->all(), [
        'order_id' => 'required|exists:orders,id',
        'driver_id' => 'required|exists:users,id',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'status'=> 0,
          'message' => $validator->errors()->first()
        ]);
      }
      try{

        $order = Order::find($request->order_id);

        $delivery = $order->deliveries()->where('driver_id',$request->driver_id)->first();

        if($delivery){
          $delivery->notify('delete');
          $delivery->delete();
        }

        return response()->json([
          'status' => 1,
          'message' => 'success',
        ]);

      }catch(Exception $e){
        return response()->json([
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
      }
    }
}
