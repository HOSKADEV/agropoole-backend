<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use App\Models\Cart;
use App\Models\Item;
use App\Models\User;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

  public function index(Request $request){

    $user = auth()->user();

    if (in_array($user->role_is(), ['broker','store'] )) {

      $categories = Category::all();
      $subcategories = Subcategory::where('category_id', $request->category)->get();
      $users = User::where('role', $user->role - 1)->has('stocks', '>', 0)->get();

      $stocks = Stock::whereHas('owner', function($query) use ($user){
        $query->where('role', $user->role - 1);
      });


      if($request->owner){
      $stocks = $stocks->where('user_id', $request->owner);
    }

    if($request->category){

      $stocks = $stocks->whereHas('product', function($query) use ($request){
        $query->whereHas('subcategory', function($query) use ($request){
          $query->where('category_id', $request->category);
        });
      });
    }


    if($request->subcategory){
      $stocks = $stocks->whereHas('product', function($query) use ($request){
        $query->where('subcategory_id',$request->subcategory);
      });
    }

    if($request->search){
      $stocks = $stocks->whereHas('product', function($query) use ($request){
        $query->where('unit_name','like', '%'.$request->search.'%');
      });
    }

    $stocks = $stocks->latest()->with(['owner','product'])->paginate(8)->appends($request->all());

    return view('content.stocks.browse')
      ->with('categories',$categories)
      ->with('subcategories',$subcategories)
      ->with('stocks',$stocks)
      ->with('users',$users);

    }else{
      return redirect()->route('pages-misc-error');
    }





  }
  public function update(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'product_id' => 'sometimes|exists:products,id',
      'quantity' => 'sometimes|integer'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 0,
        'message' => $validator->errors()->first()
      ]);
    }

    try {

      DB::beginTransaction();

      $user = auth()->user();
      $cart = $user->cart();

      if ($request->has('product_id')) {
        if ($request->quantity == 0) {
          $item = Item::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

          if (!is_null($item)) {
            $item->delete();
          }
        } else {
          Item::updateOrInsert(
            ['cart_id' => $cart->id, 'product_id' => $request->product_id],
            ['quantity' => $request->quantity, 'deleted_at' => null]
          );
        }

        /* $item = Item::where('cart_id',$cart->id)
          ->where('product_id',$request->product_id)
          ->first();

        if(!is_null($item)){
          $item->delete();
        }

        if($request->quantity > 0 ){
          $request->merge(['cart_id' => $cart->id]);
          Item::create($request->all());
        } */

      }

      DB::commit();

      $cart->refresh();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => ['total' => $cart->total()],
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

    try {

      $user = auth()->user();
      $cart = $user->cart();

      $cart->delete();

      Cart::create(['user_id' => $user->id, 'type' => 'current']);


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

  public function get(Request $request)
  {

    try {

      $user = auth()->user();
      $cart = $user->cart();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new CartResource($cart)
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

  public function products(Request $request)
  {

    try {

      $user = auth()->user();
      $cart = $user->cart();

      $products = Item::where('cart_id', $cart->id)->sum('quantity');

      //dd($products);

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => ['products' => intval($products)],
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

  public function refresh(Request $request)
  {
    //dd($request->all());

    $cart = session()->get('cart') ?? [];
    $items = $request->items;

    $new_cart = array_filter(array_merge($cart, $items), function ($item) {
      return $item['quantity'] != 0;
    });

    session()->put(['cart' => $new_cart]);

    return response()->json([
      'status' => 1,
      'message' => 'success'
    ]);
  }

  public function empty()
  {
    //dd($request->all());

    session()->put(['cart' => []]);

    return response()->json([
      'status' => 1,
      'message' => 'success'
    ]);
  }

}
