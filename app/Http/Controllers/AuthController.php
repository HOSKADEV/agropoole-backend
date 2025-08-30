<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Auth\UserQuery;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Illuminate\Support\Facades\Validator;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;
use App\Jobs\SendWelcomeEmail; // added

class AuthController extends Controller
{
  //
  public function register(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'city_id' => 'required|exists:cities,id',
      'email' => 'required|unique:users',
      'phone' => 'required',
      'name' => 'required',
      'enterprise_name' => 'sometimes',
      'password' => 'required',
      'role' => 'required|in:1,2,3,4,5',
      'longitude' => 'sometimes',
      'latitude' => 'sometimes',
      'delivery_price' => 'sometimes',
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

      DB::beginTransaction();

      if ($request->role != '4') {
        $request->mergeIfMissing(['enterprise_name' => $request->name]);
        $request->mergeIfMissing(['status' => 2]);
      }
      /* if($request->role == '1' || $request->role == '5'){
        $request->mergeIfMissing(['status'=> 2]);
      } */
      $request->merge(['password' => Hash::make($request->password)]);
      $user = User::create($request->all());

      //return ($user);

      DB::commit();

      $user->refresh();

      if ($user->role_is() != 'admin') {
        SendWelcomeEmail::dispatch($user);
      }

      $token = $user->createToken($this->random())->plainTextToken;

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'token' => $token,
        'data' => new UserResource($user),
      ]);

    } catch (Exception $e) {
      //dd($e->getMessage());
      DB::rollBack();
      return response()->json([
        'status' => 0,
        'message' => $e->getMessage(),
      ]);
    }
  }

  public function login(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'email' => 'required_without:uid',
      'password' => 'required_without:uid',
      'uid' => 'required_without:email,password',
      'fcm_token' => 'sometimes',
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

      if ($request->uid) {
        $auth = Firebase::auth();
        $firebase_user = $auth->getUser($request->uid);
      }

      $email = $request->uid ? $firebase_user->email : $request->email;

      $user = User::withTrashed()->where('email', $email)->first();

      if ($user?->trashed()) {
        if (Carbon::now()->diffInDays($user->deleted_at) <= 90) {
          $user->restore();
        } else {
          $user->nullify();
        }
      }

      if ($request->uid) {

        $user = User::firstOrCreate(
          ['email' => $firebase_user->email],
          [
            'name' => $firebase_user->displayName,
            'phone' => $firebase_user->phoneNumber,
            'image' => $firebase_user->photoUrl,
          ]
        );

        if ($user->wasRecentlyCreated && $user->role_is() != 'admin') {
          SendWelcomeEmail::dispatch($user);
        }

      } else {

        $credentials = $request->only('email', 'password');

        $user = Auth::attempt($credentials) ? User::where('email', $request->email)->first() : NULL;

      }

      #$user = User::where('email',$firebase_user->email)->first();

      if (empty($user)) {
        throw new Exception(__('wrong credentails'));
      } else {
        $user->refresh();
      }

      /* switch($user->status){
        case 'blocked' : throw new Exception(__('blocked account'));
        case 'inactive' : throw new Exception(__('inactive account'));
      } */

      if ($request->has('fcm_token')) {
        $user->fcm_token = $request->fcm_token;
        $user->save();
      }

      if (empty($user->name)) {
        $user->name = 'user#' . $user->id;
        $user->save();
      }

      $token = $user->createToken($this->random())->plainTextToken;

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'token' => $token,
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

  public function logout(Request $request)
  {
    try {

      $request->user()->currentAccessToken()->delete();

      return response()->json([
        'status' => 1,
        'message' => 'logged out',
      ]);
    } catch (Exception $e) {
      return response()->json([
        'status' => 0,
        'message' => $e->getMessage(),
      ]);
    }

  }
}
