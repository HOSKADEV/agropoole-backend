<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Http\Resources\StockResource;
use App\Http\Resources\StockCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PaginatedStockCollection;
use App\Models\Promo;

class StockController extends Controller
{

  public function index()
  {
    if (in_array(auth()->user()->role_is(), ['provider', 'broker', 'store'])) {
      $categories = Category::all();
      return view('content.stocks.list')
        ->with('categories', $categories);
    } else {
      return redirect()->route('pages-misc-error');
    }
  }

  public function create(Request $request)
  {
    //dd($request->all());

    $request->mergeIfMissing(['user_id' => auth()->id()]);

    if (auth()->user()->role_is('store')) {
      $request->merge(['show_price' => '1']);
    }

    $validator = Validator::make($request->all(), [
      'user_id' => 'required|exists:users,id',
      'product_id' => 'required|exists:products,id',
      'price' => 'sometimes|nullable|numeric',
      'quantity' => 'required|integer',
      'min_quantity' => 'required|integer',
      'show_price' => 'required|in:0,1',
      'status' => 'sometimes|in:available,unavailable',

      'has_promo' => 'sometimes|boolean',
      'target_quantity' => 'sometimes|nullable|required_if:has_promo,true|integer|min:1',
      'new_price' => 'sometimes|nullable|required_if:has_promo,true|numeric|min:0',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 0,
        'message' => $validator->errors()->first()
      ]);
    }
    try {


      $stock = Stock::create($request->except('has_promo', 'target_quantity', 'new_price'));

      if ($request->boolean('has_promo')) {
        $stock->promo()->create([
          'target_quantity' => (int) $request->target_quantity,
          'new_price' => (float) $request->new_price,
        ]);
      }

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new StockResource($stock)
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

  public function update(Request $request)
  {

    if (auth()->user()->role_is('store')) {
      $request->merge(['show_price' => '1']);
    }

    $validator = Validator::make($request->all(), [
      'stock_id' => 'required|exists:stocks,id',
      'price' => 'sometimes|nullable|numeric',
      'quantity' => 'sometimes|integer',
      'min_quantity' => 'sometimes|integer',
      'show_price' => 'sometimes|in:0,1',
      'status' => 'sometimes|in:available,unavailable',

      'has_promo' => 'sometimes|boolean',
      'target_quantity' => 'sometimes|nullable|required_if:has_promo,true|min:1',
      'new_price' => 'sometimes|nullable|required_if:has_promo,true|numeric|min:0',
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

      $stock = Stock::findOrFail($request->stock_id);

      $stock->update($request->except('stock_id', 'has_promo', 'target_quantity', 'new_price'));

      if ($request->filled('has_promo')) {
        if ($request->has_promo) {
          Promo::updateOrCreate(
            ['stock_id' => $stock->id],
            $request->only('target_quantity', 'new_price')
          );
        } else {
          Promo::where('stock_id', $stock->id)->delete();
        }
      }

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new StockResource($stock)
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

  public function delete(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'stock_id' => 'required',
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

      $stock = Stock::findOrFail($request->stock_id);

      $stock->delete();

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
      'stock_id' => 'required',
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

      $stock = Stock::withTrashed()->findOrFail($request->stock_id);

      $stock->restore();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new StockResource($stock)
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
  {  //paginated

    $request->mergeIfMissing(['user_id' => auth()->id()]);

    if (!auth()->id() || auth()->id() != $request->user_id) {
      $request->merge(['status' => 'available']);
    }

    $validator = Validator::make($request->all(), [
      'user_id' => 'required|exists:users,id',
      'category_id' => 'sometimes|missing_with:subcategory_id|exists:categories,id',
      'subcategory_id' => 'sometimes|exists:subcategories,id',
      'status' => 'sometimes|in:available,unavailable,all',
      'search' => 'sometimes|string',
      'has_promo' => 'sometimes|boolean',
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

      $stocks = Stock::with('product', 'promo')
        ->where('user_id', $request->user_id)
        ->latest();

      if ($request->has('category_id')) {
        $categoryId = $request->category_id;
        $stocks = $stocks->whereHas('product', function ($query) use ($categoryId) {
          $query->whereHas('subcategory', function ($sub) use ($categoryId) {
            $sub->where('category_id', $categoryId);
          });
        });
      }

      if ($request->has('subcategory_id')) {
        $subCategoryId = $request->subcategory_id;
        $stocks = $stocks->whereHas('product', function ($query) use ($subCategoryId) {
          $query->where('subcategory_id', $subCategoryId);
        });
      }

      if ($request->has('search')) {
        $stocks = $stocks->whereHas('product', function ($query) use ($request) {
          $query->where('unit_name', 'like', '%' . $request->search . '%');
          //->orWhere('pack_name', 'like', '%' . $request->search . '%');
        });
      }

      if ($request->has('status')) {
        $stocks = $request->status != 'all'
          ? $stocks->where('status', $request->status)
          : $stocks;
      }

      if ($request->filled('has_promo')) {
        if ($request->has_promo) {
          $stocks = $stocks->whereHas('promo');
        } else {
          $stocks = $stocks->whereDoesntHave('promo');
        }
      }

      if ($request->has('all')) {
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

    } catch (Exception $e) {
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }

  }

  public function multi_create(Request $request)
  {

    try {

      DB::beginTransaction();

      $user = $request->user();


      if ($user->role == '1') {
        $products = $user->products()->whereNotIn('id', $user->stocks()->pluck('product_id')->toArray())->get();
      } else {
        $stocked_products = $user->stocks()->pluck('product_id')->toArray();
        /* $product_owners = Product::whereIn('id',$stocked_products)->pluck('user_id')->toArray();
        $products = Product::whereIn('user_id',$product_owners)
        //->where('status','available')
        ->whereNotIn('id',$stocked_products)
        ->get(); */

        /* $categories = Product::whereIN('products.id',$stocked_products)
        ->leftJoin('subcategories', 'products.subcategory_id', 'subcategories.id')
        ->leftJoin('categories', 'subcategories.category_id', 'categories.id')
        ->pluck('categories.id')->toArray();

        $subcategories = Subcategory::whereIn('category_id', $categories)
        ->pluck('subcategories.id')->toArray();

        $products = Product::where('status','available')
        ->whereIn('subcategory_id',$subcategories)
        ->whereNotIn('id',$stocked_products)
        ->get(); */

        $products = Product::where('status', 'available')
          ->whereNotIn('id', $stocked_products)
          ->get();

      }

      $stocks = [];
      foreach ($products as $product) {
        $stocks += [
          $product->id => [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'price' => $product->unit_price,
            'quantity' => 0,
            'min_quantity' => 0,
            'show_price' => 0,
            'status' => 'unavailable',
            'created_at' => now(),
            'updated_at' => now(),
          ]
        ];
      }

      $stocks = Stock::insert($stocks);

      DB::commit();

      return response()->json([
        'status' => 1,
        'message' => 'success',
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

  public function owner(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'stock_id' => 'required|exists:stocks,id',
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

      $stock = Stock::findOrFail($request->stock_id);

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new UserResource($stock->owner),
      ]);

    } catch (Exception $e) {
      //dd($e->getMessage());

      return response()->json([
        'status' => 0,
        'message' => $e->getMessage(),
      ]);
    }
  }

}
