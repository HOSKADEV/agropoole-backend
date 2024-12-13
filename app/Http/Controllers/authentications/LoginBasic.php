<?php

namespace App\Http\Controllers\authentications;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;

class LoginBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function login(Request $request)
  {
    //dd($request);
    /* $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]); */

    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if ($validator->fails()){
      return redirect("/auth/login-basic")->withError($validator->errors()->first());
    }

    $user = User::withTrashed()->where('email', $request->email)->first();

    if ($user?->trashed()) {
      if (Carbon::now()->diffInDays($user->deleted_at) <= 90) {
        $user->restore();
      } else {
        $user->nullify();
      }
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
      if($user?->status != 'active'){
        return redirect("/auth/login-basic")
        ->withWarning('Account not active yet');
      }else{
        return redirect()->intended('/')
        ->withSuccess('Signed in');
      }
    }

    return redirect("/auth/login-basic")->withError('Login details are not valid');
  }

  public function redirect() {
    return Socialite::driver('google')->redirect();
  }

  public function callback() {
    $google_user = Socialite::driver('google')->stateless()->user();
    //dd($google_user);
    $user = User::withTrashed()->where('email', $google_user->email)->first();

    if ($user?->trashed()) {
      if (Carbon::now()->diffInDays($user->deleted_at) <= 90) {
        $user->restore();
      } else {
        $user = $user->nullify();
      }
    }

    if ($user) {
      if($user?->status != 'active'){
        return redirect()->route('login')->withWarning('Account not active yet');
      }else{
        Auth::login($user);
        return redirect()->route('dashboard-analytics')->withSuccess('Signed in');
      }

    } else {
      return redirect()->route('login')->withError('Login details are not valid');
    }

  }

}
