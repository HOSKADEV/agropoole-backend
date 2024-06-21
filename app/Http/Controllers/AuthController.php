<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Depot;
use App\Models\Store;
use App\Models\Factory;
use Illuminate\Http\Request;
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

class AuthController extends Controller
{
    //
    public function register(Request $request){

      $validator = Validator::make($request->all(), [
        'city_id' => 'required|exists:cities,id',
        'email' => 'required|unique:users',
        'phone' => 'required',
        'name' => 'required',
        'enterprise_name' => 'sometimes',
        'password' => 'required',
        'role' => 'required|in:1,2,3,4',
        'longitude' => 'sometimes',
        'latitude' => 'sometimes',
      ]);

      if ($validator->fails()){
        return response()->json([
            'status' => 0,
            'message' => $validator->errors()->first()
          ]
        );
      }




      try {

        DB::beginTransaction();

        if($request->role != '4'){
          $request->mergeIfMissing(['enterprise_name' => $request->name]);
        }
        if($request->role == '1'){
          $request->mergeIfMissing(['status'=> 2]);
        }
        $request->merge(['password' => Hash::make($request->password)]);
        $user = User::create($request->all());

        //return ($user);

        $token = $user->createToken($this->random())->plainTextToken;

        DB::commit();

        return response()->json([
          'status'=> 1,
          'message' => 'success',
          'token' => $token,
          'data' => new UserResource($user),
        ]);

      } catch (Exception $e) {
          //dd($e->getMessage());
          DB::rollBack();
          return response()->json([
          'status'=> 0,
          'message' => $e->getMessage(),
        ]);
      }
    }

    public function login(Request $request){

      $validator = Validator::make($request->all(), [
        'email' => 'required_without:uid',
        'password' => 'required_without:uid',
        'uid' => 'required_without:email,password',
        'fcm_token' => 'sometimes',
      ]);

      if ($validator->fails()){
        return response()->json([
            'status' => 0,
            'message' => $validator->errors()->first()
          ]
        );
      }

      try {

        if($request->uid){
          $auth = Firebase::auth();

          $firebase_user = $auth->getUser($request->uid);

          $user = User::where('email',$firebase_user->email)->first();

          if(is_null($user)){
            $user = User::create([
              'name' => $firebase_user->displayName,
              'email' => $firebase_user->email,
              'phone' => $firebase_user->phoneNumber,
              'image' => $firebase_user->photoUrl,
            ]);
          }
        }else{

          $credentials = $request->only('email', 'password');

          $user = Auth::attempt($credentials) ? User::where('email',$request->email)->first() : NULL;

        }

      #$user = User::where('email',$firebase_user->email)->first();

      if(empty($user)){
        throw new Exception('wrong credentails');
      }else{
        $user->refresh();
      }

      /* switch($user->status){
        case 'blocked' : throw new Exception('blocked account');
        case 'inactive' : throw new Exception('inactive account');
      } */

      if($request->has('fcm_token')){
        $user->fcm_token = $request->fcm_token;
        $user->save();
      }

      if(empty($user->name)){
        $user->name = 'user#'.$user->id;
        $user->save();
      }

      $token = $user->createToken($this->random())->plainTextToken;

        return response()->json([
          'status'=> 1,
          'message' => 'success',
          'token' => $token,
          'data' => new UserResource($user),
        ]);

      } catch (Exception $e) {
          //dd($e->getMessage());

          return response()->json([
          'status'=> 0,
          'message' => $e->getMessage(),
        ]);
      }


    }

    public function logout(Request $request){
      try{

        $request->user()->currentAccessToken()->delete();

        return response()->json([
          'status'=> 1,
          'message' => 'logged out',
        ]);
      }catch(Exception $e){
        return response()->json([
          'status'=> 0,
          'message' => $e->getMessage(),
        ]);
      }

    }
}
