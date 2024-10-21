@extends('layouts/contentNavbarLayout')

@section('title', __('Users'))

@section('vendor-style')
    <style>
        .product-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .owner-info {
            height: 60px;
            display: flex;
            align-items: center;
        }

        .product-image-container {
            position: relative;
            height: 200px;
            /* Adjust as needed */
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-price {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background-color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .product-name {
            height: 50px;
            /* Adjust as needed */
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .quantity-controls {
            height: 40px;
            /* Adjust as needed */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-section {
            display: none;
            transition: all 0.3s ease;
        }

        .quantity-section.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-button {
            width: 100%;
            transition: all 0.3s ease;
        }

        .cart-button.hidden {
            display: none;
        }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
            text-align: center;
            font-weight: bold;
            font-size: 0.85rem
        }

        svg {
            width: 40px !important;
            height: 40px !important;
        }
    </style>
@endsection
@section('vendor-script')
    <script></script>
@endsection


@section('content')

    <h4 class="fw-bold py-3 mb-3">
        <span class="text-muted fw-light">{{ __('Users') }} /</span> {{ __('Browse users') }}
        {{-- <button type="button" class="btn btn-primary" id="create" style="float:right">{{ __('Add Stock') }}</button> --}}
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card mb-3 pb-3">
        <form id="form" method="GET" action="{{ route('user-browse') }}">
            <div class="row  justify-content-between">
                <div class="form-group col mx-3 my-3">
                    <label for="state" class="form-label">{{ __('State filter') }}</label>
                    <select class="form-select" id="state" name="state">
                        <option value=""> {{ __('Not selected') }}</option>
                        @foreach ($states as $state)
                        <option value="{{$state->id}}" {{request('state') == $state->id ? 'selected' : ''}}> {{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if (auth()->user()->role_is('broker'))
                <div class="form-group col mx-3 my-3">
                  <label for="role" class="form-label">{{ __('Role filter') }}</label>
                  <select class="form-select" id="role" name="role">
                      <option value=""> {{ __('Not selected') }}</option>
                      <option value="1" {{request('role') == '1' ? 'selected' : ''}}> {{ __('provider') }}</option>
                      <option value="3" {{request('role') == '3' ? 'selected' : ''}}> {{ __('store') }}</option>
                  </select>
              </div>
                @endif

<div class="form-group col mx-3 my-3">
                  <label for="client" class="form-label">{{ __('Client filter') }}</label>
                  <select class="form-select" id="client" name="client">
                      <option value=""> {{ __('Not selected') }}</option>
                      <option value="1" {{request('client') == '1' ? 'selected' : ''}}> {{ __('Yes') }}</option>
                      <option value="2" {{request('client') == '2' ? 'selected' : ''}}> {{ __('No') }}</option>
                  </select>
              </div>

                <div class="form-group col mx-3 my-3">
                    <label for="search" class="form-label">{{ __('Search') }}</label>
                    <input class="form-control" id="search" name="search" value="{{ request()->get('search') }}">
                </div>
            </div>
        </form>
    </div>

    <div class="row">

        @if (count($users->items()))
            @foreach ($users->items() as $user)


                  <div class="card col-md-auto m-2 p-0" >

                    <div class="card-body" style="width:19.8rem !important">
                        <div class="d-flex justify-content-start align-items-center mb-6">
                            <div class="avatar me-3">
                                <img src="{{ $user->image() }}" alt="Avatar" class="rounded-circle">
                            </div>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-body text-nowrap">
                                    <h6 class="mb-0">{{ $user->enterprise() }}</h6>
                                </a>
                                <span>{{ __($user->role_is()) }}</span>
                            </div>
                        </div>
                        {{-- <div class="d-flex justify-content-start align-items-center mb-6">
                            <span
                                class="avatar rounded-circle bg-label-success me-3 d-flex align-items-center justify-content-center"><i
                                    class='bx bx-cart bx-lg'></i></span>
                            <h6 class="text-nowrap mb-0">12 Orders</h6>
                        </div> --}}
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1">{{__('Contact info')}}</h6>
                            {{-- <h6 class="mb-1"><a href="{{ $user->location() }}"><i class="bx bx-map"></i></a>
                            </h6> --}}
                        </div>

                        <h6 class=" mb-1 text-fit"><i class="bx bxs-envelope"></i> {{ $user->email }}</h6>
                        <h6 class=" mb-1 text-fit"><i class="bx bxs-phone"></i> {{ $user->phone }}</h6>
                        <h6 class=" mb-0 text-fit"><i class="bx bxs-map"></i> <a href="{{ $user->location() }}">{{ $user->address() }}</a></h6>
                    </div>
                </div>

            @endforeach
        @else
            <div class="container-xxl container-p-y d-flex justify-content-center">
                <div class="misc-wrapper" style="text-align: center !important">
                    <h3 class="mb-2 mx-2">{{ __('No stock') }}</h3>
                    <p class="mb-6 mx-2">
                        {{ __('This product you are looking for is not available at the moment') }}
                    </p>
                    <div class="mt-6">
                        <img src="{{ url('/assets/img/illustrations/Empty-amico.png') }}" width="400" class="img-fluid">
                    </div>
                </div>
            </div>

        @endif



    </div>


    {{ $users->onEachSide(1)->links() }}



@endsection

@section('page-script')
<script>
  $(document).ready(function() {
            function submitForm() {
                $("#form").submit();
            }
            $('#search').on('keyup', function(event) {
                $("#search").focus();

                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            /* $('#category').on('change', function() {
                $('#subcategory').val(null);
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            })

            $('#subcategory').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            }) */

            $('#state').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            })
            $('#role').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            })
            $('#client').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            })
        });
    </script>
@endsection
