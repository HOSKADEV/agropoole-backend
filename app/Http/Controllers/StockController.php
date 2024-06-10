<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Stock;
use Illuminate\Http\Request;
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
      'show_price' => 'required|in:0,1'
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
      'show_price' => 'sometimes|in:0,1'
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
    ]);

    if ($validator->fails()){
      return response()->json([
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try{



    $stocks = Stock::where('user_id',$request->user_id)->orderBy('created_at','DESC');



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
}
