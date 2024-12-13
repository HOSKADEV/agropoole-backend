<?php

namespace App\Http\Controllers\authentications;

use Hash;
use Session;
use App\Models\User;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class RegisterBasic extends Controller
{
  public function index()
  {
    $states = State::all();
    return view('content.authentications.auth-register-basic',compact('states'));
  }

/*   public function register(Request $request)
  {
    dd($request->all());
    $request->validate([
      'username' => 'required|unique:users',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6',
    ]);


    User::create([
      'name' => $request->username,
      'email' => $request->email,
      'password' => Hash::make($request->password)
    ]);

    //dd($user);

    return redirect("/");
  } */

  public function register(Request $request){

    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'city_id' => 'required|exists:cities,id',
      'email' => 'required|email|unique:users',
      'phone' => 'required',
      'password' => 'required',
      'role' => 'required|in:1,2,3,5',
    ]);

    if ($validator->fails()){
      return redirect()->back()->withInput()->withError($validator->errors()->first());
    }

    User::create([
      'name' => $request->name,
      'enterprise_name' => $request->name,
      'city_id' => $request->city_id,
      'email' => $request->email,
      'phone' => $request->phone,
      'password' => Hash::make($request->password),
      'role' => $request->role,
      'status'=> 2
    ]);

    return redirect("/auth/login-basic")->withSuccess('Account created and now waiting admin approval');

  }
}
