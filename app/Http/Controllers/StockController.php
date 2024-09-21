<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\StockResource;
use App\Http\Resources\StockCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PaginatedStockCollection;

class StockController extends Controller
{
  public function create(Request $request){
    //dd($request->all());

    $request->mergeIfMissing(['user_id' => auth()->id()]);

    $validator = Validator::make($request->all(), [
      'user_id' => 'required|exists:users,id',
      'product_id' => 'required|exists:products,id',
      'price' => 'required|numeric',
      'quantity' => 'required|integer',
      'min_quantity' => 'required|integer',
      'show_price' => 'required|in:0,1',
      'status' => 'sometimes|in:available,unavailable'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status'=> 0,
        'message' => $validator->errors()->first()
      ]);
    }
    try{


      $stock = Stock::create($request->all());


      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new StockResource($stock)
      ]);

    }catch(Exception $e){
      return response()->json([
        'status' => 0,
        'message' => $e->getMessage()
      ]
    );
    }
  }

  public function update(Request $request){

    $validator = Validator::make($request->all(), [
      'stock_id' => 'required|exists:stocks,id',
      'price' => 'sometimes|numeric',
      'quantity' => 'sometimes|integer',
      'min_quantity' => 'sometimes|integer',
      'show_price' => 'sometimes|in:0,1',
      'status' => 'sometimes|in:available,unavailable'
    ]);

    if ($validator->fails()){
      return response()->json([
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try{

      $stock = Stock::findOrFail($request->stock_id);

      $stock->update($request->except('stock_id'));


      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new StockResource($stock)
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
      'stock_id' => 'required',
    ]);

    if ($validator->fails()){
      return response()->json([
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try{

      $stock = Stock::findOrFail($request->stock_id);

      $stock->delete();

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

  public function restore(Request $request){

    $validator = Validator::make($request->all(), [
      'stock_id' => 'required',
    ]);

    if ($validator->fails()){
      return response()->json([
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try{

      $stock = Stock::withTrashed()->findOrFail($request->stock_id);

      $stock->restore();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new StockResource($stock)
      ]);

    }catch(Exception $e){
      return response()->json([
        'status' => 0,
        'message' => $e->getMessage()
      ]
    );
    }

  }

  public function get(Request $request){  //paginated

    $request->mergeIfMissing(['user_id' => $this->get_user_from_token($request->bearerToken())->id]);

    $validator = Validator::make($request->all(), [
      'user_id' => 'required|exists:users,id',
      'category_id' => 'sometimes|missing_with:subcategory_id|exists:categories,id',
      'subcategory_id' => 'sometimes|exists:subcategories,id',
      'search' => 'sometimes|string',
    ]);

    if ($validator->fails()){
      return response()->json([
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try{



    $stocks = Stock::join('products', function($join){
      $join->on('stocks.product_id', '=', 'products.id')
      ->where('products.deleted_at', null);
    })
    ->select('stocks.*','products.subcategory_id','products.unit_name')
    ->where('stocks.user_id',$request->user_id)->orderBy('stocks.created_at','DESC');

    if($request->user()->id != $request->user_id){
      $stocks = $stocks->where('stocks.status','available');
    }

    if($request->has('category_id')){

      $category = Category::findOrFail($request->category_id);
      $category_subs = $category->subcategories()->pluck('id')->toArray();
      $stocks = $stocks->whereIn('subcategory_id',$category_subs);
    }

    if($request->has('subcategory_id')){

      $stocks = $stocks->where('subcategory_id',$request->subcategory_id);
    }

    if($request->has('search')){

      $stocks = $stocks->where('unit_name', 'like', '%' . $request->search . '%');
                            //->orWhere('pack_name', 'like', '%' . $request->search . '%');
    }



    if($request->has('all')){
      $stocks = $stocks->get();
      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new StockCollection($stocks)
      ]);

    }
      $stocks = $stocks->paginate(10);


    return response()->json([
      'status' => 1,
      'message' => 'success',
      'data' => new PaginatedStockCollection($stocks)
    ]);

  }catch(Exception $e){
    return response()->json([
      'status' => 0,
      'message' => $e->getMessage()
    ]
  );
  }

  }

  public function multi_create(Request $request){

    try{

      DB::beginTransaction();

      $user = $request->user();


      if($user->role == '1'){
        $products = $user->products()->whereNotIn('id',$user->stocks()->pluck('product_id')->toArray())->get();
      }else{
        $stocked_products = $user->stocks()->pluck('product_id')->toArray();
        /* $product_owners = Product::whereIn('id',$stocked_products)->pluck('user_id')->toArray();
        $products = Product::whereIn('user_id',$product_owners)
        //->where('status','available')
        ->whereNotIn('id',$stocked_products)
        ->get(); */

        $categories = Product::whereIN('products.id',$stocked_products)
        ->leftJoin('subcategories', 'products.subcategory_id', 'subcategories.id')
        ->leftJoin('categories', 'subcategories.category_id', 'categories.id')
        ->pluck('categories.id')->toArray();

        $subcategories = Subcategory::whereIn('category_id', $categories)
        ->pluck('subcategories.id')->toArray();

        $products = Product::where('status','available')
        ->whereIn('subcategory_id',$subcategories)
        ->whereNotIn('id',$stocked_products)
        ->get();

      }

      $stocks = [];
        foreach($products as $product){
          $stocks += [$product->id => [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'price' => $product->unit_price,
            'quantity' => 0,
            'min_quantity' => 0,
            'show_price' => 0,
            'status' => 'unavailable'
          ]] ;
        }

        $stocks = Stock::insert($stocks);

        DB::commit();

      return response()->json([
        'status' => 1,
        'message' => 'success',
      ]);

    }catch(Exception $e){
      DB::rollBack();
      return response()->json([
        'status' => 0,
        'message' => $e->getMessage()
      ]
    );
    }
  }

}
