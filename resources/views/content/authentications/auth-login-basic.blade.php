@extends('layouts/blankLayout')

@section('title', 'Login Basic - Pages')

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <img src="{{ url('agropole.png') }}" alt="image" class="d-block rounded" height="60" width="180" />
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">{{ __('Welcome to') }} {{ config('variables.templateName') }}! 👋</h4>
                        <p class="mb-4">{{ __('Please sign-in to your account and start the adventure') }}</p>

                        <form id="formAuthentication" class="mb-3" action="{{ url('/auth/login-action') }}"
                            method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email') }}</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="{{ __('Enter your email') }}" autofocus>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">{{ __('Password') }}</label>
                                    {{-- <a href="{{url('auth/forgot-password-basic')}}">
                  <small>Forgot Password?</small>
                </a> --}}
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me">
                                    <label class="form-check-label" for="remember-me">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Sign in') }}</button>
                            </div>
                            <div class="mb-3">
                                <a class="btn btn-outline-primary w-100" href="{{ url('/auth/redirect') }}">
                                    <span class="tf-icons bx bxl-google bx-18px me-2"></span>
                                    {{ __('Sign in with Google') }}
                                </a>
                            </div>
                        </form>

                        <p class="text-center">
            <span>{{__('New on our platform?')}}</span>
            <a href="{{url('auth/register-basic')}}">
              <span>{{__('Create an account')}}</span>
            </a>
          </p>
                    </div>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
    </div>
@endsection
