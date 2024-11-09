<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\State;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PaginatedUserCollection;
use App\Http\Resources\UserWithStockCollection;
use App\Http\Resources\PaginatedUserWithStockCollection;

class UserController extends Controller
{

  public function index()
  {
    if (auth()->user()->role_is('admin')) {
      return view('content.users.list');
    } else {
      return redirect()->route('pages-misc-error');
    }

  }

  public function browse(Request $request)
  {

    $user = auth()->user();
    $states = State::all();
    $users = User::where('status', 'active')->whereNotNull('email');

    $users = $user->role_is('broker')
      ? $users->whereIn('role', [1, 3])
      : $users->where('role', 2);




    if ($request->state) {
      $users = $users->whereHas('city', function ($query) use ($request) {
        $query->where('state_id', $request->state);
      });
    }

    if ($request->role) {
      $users->where('role', $request->role);
    }

    if ($request->client) {

      $query = function ($query) use ($user) {
        $query->whereHas('boughtOrders', function ($q) use ($user) {
          $q->where('seller_id', $user->id);
        });
        $query->orWhereHas('soldOrders', function ($q) use ($user) {
          $q->where('buyer_id', $user->id);
        });
      };



      $users = $request->client == 1
        ? $users->where($query)
        : $users->whereNot($query);
    }





    if ($request->search) {
      $users = $users->where(function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->search . '%');
        $query->orWhere('enterprise_name', 'like', '%' . $request->search . '%');
      });
    }

    $users = $users->latest()->paginate(12)->appends($request->all());

    return view('content.users.browse')
      ->with('states', $states)
      ->with('users', $users);





  }

  public function stocks($id, Request $request)
  {
    $auth_user = auth()->user();
    $user = User::find($id);

    if ($auth_user->can_see_stock_of($user)) {

      $categories = Category::all();
      $subcategories = Subcategory::where('category_id', $request->category)->get();
      $stocks = Stock::where('user_id', $id);

      if(!$auth_user->soldOrders()->where('buyer_id',$user->id)->count()){
        $stocks->where('status', 'available');
      }

      //dd($stocks->get());
      if ($request->category) {

        $stocks = $stocks->whereHas('product', function ($query) use ($request) {
          $query->whereHas('subcategory', function ($query) use ($request) {
            $query->where('category_id', $request->category);
          });
        });
      }


      if ($request->subcategory) {
        $stocks = $stocks->whereHas('product', function ($query) use ($request) {
          $query->where('subcategory_id', $request->subcategory);
        });
      }

      if ($request->search) {
        $stocks = $stocks->whereHas('product', function ($query) use ($request) {
          $query->where('unit_name', 'like', '%' . $request->search . '%');
        });
      }

      $stocks = $stocks->latest()->with(['owner', 'product'])->paginate(8)->appends($request->all());

      return view('content.users.stocks')
        ->with('categories', $categories)
        ->with('subcategories', $subcategories)
        ->with('stocks', $stocks)
        ->with('user', $user);

    } else {
      return redirect()->route('pages-misc-error');
    }

  }
  public function update(Request $request)
  {

    $request->mergeIfMissing(['user_id' => auth()->id()]);

    $validator = Validator::make($request->all(), [
      //'firstname' => 'sometimes|string',
      //'lastname' => 'sometimes|string',
      'user_id' => 'required|exists:users,id',
      'city_id' => 'sometimes|exists:cities,id',
      'name' => 'sometimes|string',
      'phone' => 'sometimes|numeric',
      /* 'phone' => ['sometimes','numeric','digits:10',Rule::unique('users')->ignore($user->id)], */
      'email' => ['sometimes', 'email', Rule::unique('users')->ignore(auth()->id())],
      'image' => 'sometimes|mimetypes:image/*',
      'status' => 'sometimes|in:1,2,3',

      'enterprise_name' => 'sometimes',
      'longitude' => 'sometimes',
      'latitude' => 'sometimes',
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

      $user = User::find($request->user_id);

      $user->update($request->except('image'));

      if ($request->hasFile('image')) {
        $path = $request->image->store('/uploads/users/images', 'upload');

        /* $file = $request->image;
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = 'users/' . $user->id . '/' . md5(time().$name) . '.' . $extension;
        $url = $this->firestore($file->get(),$filename);
        $user->image = $url; */

        $user->image = $path;

        $user->save();
      }

      if ($request->has('status')) {

        $user->refresh();

        if ($user->fcm_token) {
          $this->send_fcm_device(__('user.account.title'), __('user.account.' . $user->status), $user->fcm_token);
        }

        if ($request->status == 3) {
          $user->fcm_token = null;
          $user->tokens()->delete();
          $user->save();
        }
      }

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new UserResource($user)
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
      'user_id' => 'required',
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

      $user = User::findOrFail($request->user_id);

      $user->delete();

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
      'user_id' => 'required',
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

      $user = User::withTrashed()->findOrFail($request->user_id);

      $user->restore();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new UserResource($user)
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

  public function change_password(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'old_password' => 'required',
      'new_password' => 'required|min:8|confirmed',
    ]);



    if ($validator->fails()) {
      return response()->json([
        'status' => 0,
        'message' => $validator->errors()->first()
      ]);

    }

    $user = auth()->user();

    if (Hash::check($request->old_password, $user->password)) {

      $user->password = Hash::make($request->new_password);
      $user->save();

      return response()->json([
        'status' => 1,
        'message' => 'password changed'
      ]);

    } else {

      return response()->json([
        'status' => 0,
        'message' => 'wrong password'
      ]);

    }

  }

  public function reset_password(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'user_id' => 'required|exists:users,id',
    ]);



    if ($validator->fails()) {
      return response()->json([
        'status' => 0,
        'message' => $validator->errors()->first()
      ]);

    }

    try {
      $user = User::findOrFail($request->user_id);
      $user->password = Hash::make($user->name ?? $user->email);
      $user->save();

      return response()->json([
        'status' => 1,
        'message' => 'password reseted'
      ]);
    } catch (Exception $e) {

      return response()->json([
        'status' => 0,
        'message' => $e->getMessage()
      ]);

    }

  }

  public function deactivate(Request $request)
  {
    try {

      $user = $request->user();

      $user->update(['status' => 2, 'email' => null, 'fcm_token' => null]);

      $user->tokens()->delete();

      return response()->json([
        'status' => 1,
        'message' => 'success',
      ]);
    } catch (Exception $e) {
      return response()->json([
        'status' => 0,
        'message' => $e->getMessage(),
      ]);
    }

  }

  public function get(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'role' => 'required|in:1,2,3,4,5',
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

      $users = User::where('users.status', 'active')->where('users.role', $request->role);


      if ($request->role != '5') {
        $users = $users->join('stocks', 'users.id', 'stocks.user_id')
          ->select('users.*', DB::raw('COUNT(stocks.user_id)'))
          ->groupBy('stocks.user_id');
      }

      $users = $request->has('all')
        ? ($request->has('with_stocks')
          ? new UserWithStockCollection($users->get())
          : new UserCollection($users->get()))
        : ($request->has('with_stocks')
          ? new PaginatedUserWithStockCollection($users->paginate(10))
          : new PaginatedUserCollection($users->paginate(10)));

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => $users
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

  public function info(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'user_id' => 'required|exists:users,id',
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

      $user = User::find($request->user_id);

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new UserResource($user),
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
