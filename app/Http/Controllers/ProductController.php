<?php

namespace App\Http\Controllers;

use Session;
use Exception;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PaginatedProductCollection;

class ProductController extends Controller
{

  public function index(){
    if (auth()->user()->role_is('provider')) {
      return view('content.products.list')
      ->with('categories',Category::all());
    } else if(in_array(auth()->user()->role_is(), ['broker','store']) ) {
      $providers = User::where('role',1)->where('status','active')->has('products', '>', '0')->get();
      return view('content.products.list')
      ->with('categories',Category::all())
      ->with('providers', $providers);
    }else{
      return redirect()->route('pages-misc-error');
    }
  }
  public function create(Request $request){
    //dd($request->all());
    $request->merge(['user_id' => auth()->id()]);

    $validator = Validator::make($request->all(), [
      'user_id' => 'required|exists:users,id',
      'subcategory_id' => 'required|exists:subcategories,id',
      'unit_name' => 'required|string',
      'pack_name'=> 'sometimes|string',
      'image' => 'sometimes|mimetypes:image/*',
      'unit_price' => 'required|numeric',
      //'unit_type' => 'required|in:1,2,3',
      'pack_price' => 'required_with:pack_units|nullable|numeric',
      'pack_units' => 'required_with:pack_price|nullable|integer',
      'status' => 'sometimes|in:available,unavailable'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status'=> 0,
        'message' => $validator->errors()->first()
      ]);
    }
    try{


      $product = Product::create($request->except('image'));

      if($request->hasFile('image')){
        $path = $request->image->store('uploads/products/images','upload');

        /* $file = $request->image;
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = 'products/' . $product->id . '/' . md5(time().$name) . '.' . $extension;
        $url = $this->firestore($file->get(),$filename);
        $product->image = $url;*/
        $product->image = $path;
        $product->save();
      }


      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new ProductResource($product)
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
      'product_id' => 'required|exists:products,id',
      'unit_name' => 'sometimes|string',
      'pack_name'=> 'sometimes|string',
      'image' => 'sometimes|mimetypes:image/*',
      'unit_price' => 'sometimes|numeric',
      'unit_type' => 'sometimes|in:1,2,3',
      'pack_price' => 'required_with:pack_units|nullable|numeric',
      'pack_units' => 'required_with:pack_price|nullable|integer',
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

      $product = Product::findOrFail($request->product_id);

      $product->update($request->except('image','product_id'));

      if($request->hasFile('image')){
        $path = $request->image->store('uploads/products/images','upload');

        /* $file = $request->image;
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = 'products/' . $product->id . '/' . md5(time().$name) . '.' . $extension;
        $url = $this->firestore($file->get(),$filename);
        $product->image = $url;*/
        $product->image = $path;
        $product->save();
      }

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new ProductResource($product)
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
      'product_id' => 'required',
    ]);

    if ($validator->fails()){
      return response()->json([
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try{

      $product = Product::findOrFail($request->product_id);

      $product->delete();

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
      'product_id' => 'required',
    ]);

    if ($validator->fails()){
      return response()->json([
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try{

      $product = Product::withTrashed()->findOrFail($request->product_id);

      $product->restore();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new ProductResource($product)
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


    /* if(!is_null($request->bearerToken())){
      Session::put('user_id', $this->get_user_from_token($request->bearerToken())->id);
    } */

    //$products = Product::where('status','available')->orderBy('created_at','DESC');

    $products = Product::where('user_id',$request->user_id)
    ->orderBy('created_at','DESC');

    if($request->user()->id != $request->user_id){
      $products = $products->where('status','available');
    }

    if($request->has('category_id')){

      $category = Category::findOrFail($request->category_id);
      $category_subs = $category->subcategories()->pluck('id')->toArray();
      $products = $products->whereIn('subcategory_id',$category_subs);
    }

    if($request->has('subcategory_id')){

      $subcategory = Subcategory::findOrFail($request->subcategory_id);
      $sub_products = $subcategory->products()->pluck('id')->toArray();
      $products = $products->whereIn('id',$sub_products);
    }

    if($request->has('search')){

      $products = $products->where('unit_name', 'like', '%' . $request->search . '%');
                            //->orWhere('pack_name', 'like', '%' . $request->search . '%');
    }

    if($request->has('all')){
      $products = $products->get();
      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new ProductCollection($products)
      ]);

    }
      $products = $products->paginate(10);


    return response()->json([
      'status' => 1,
      'message' => 'success',
      'data' => new PaginatedProductCollection($products)
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
