@extends('layouts/contentNavbarLayout')

@section('title', __('Stocks'))

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

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
            text-align: center;
        }

        svg {
            width: 40px !important;
            height: 40px !important;
        }
    </style>
@endsection
@section('vendor-script')
    <script>
        function incrementQuantity(button) {
            var input = button.previousElementSibling;
            input.value = parseInt(input.value) + 1;
            updateQuantity(input);
        }

        function decrementQuantity(button) {
            var input = button.nextElementSibling;
            input.value = Math.max(1, parseInt(input.value) - 1);
            updateQuantity(input);
        }

        function updateQuantity(input) {
            input.value = Math.max(1, parseInt(input.value) || 1);
        }
    </script>
@endsection


@section('content')

    <h4 class="fw-bold py-3 mb-3">
        <span class="text-muted fw-light">{{ __('Stocks') }} /</span> {{ __('Browse stocks') }}
        {{-- <button type="button" class="btn btn-primary" id="create" style="float:right">{{ __('Add Stock') }}</button> --}}
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card mb-4">
        <div class="row  justify-content-between">
            <div class="form-group col mx-3 my-3">
                <label for="category" class="form-label">{{ __('Category filter') }}</label>
                <select class="form-select" id="category" name="category">
                    <option value=""> {{ __('Not selected') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"> {{ $category->name }} </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col mx-3 my-3">
                <label for="subcategory" class="form-label">{{ __('Subcategory filter') }}</label>
                <select class="form-select" id="subcategory" name="subcategory">
                    <option value=""> {{ __('Not selected') }}</option>
                </select>
            </div>

            <div class="form-group col mx-3 my-3">
                <label for="category" class="form-label">{{ __('Provider filter') }}</label>
                <select class="form-select" id="provider" name="provider">
                    <option value=""> {{ __('Not selected') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"> {{ $user->enterprise() }} </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col mx-3 my-3">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input class="form-control" id="search" name="search">
            </div>
        </div>
    </div>

    <div class="row  justify-content-between">

        @foreach ($stocks->items() as $stock)
            <div class="col-md-3 mb-4">
                <div class="card product-card">
                    <div class="card-header owner-info p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="{{ $stock->owner->image() }}" alt="User" class="rounded-circle"
                                    style="width: 40px; height: 40px;">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-truncate">{{ $stock->owner->enterprise() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="product-image-container">
                        <img class="product-image" src="{{ $stock->product->image() }}"
                            alt="{{ $stock->product->unit_name }}">
                        @if ($stock->show_price)
                            <div class="product-price">
                                <span class="h6 mb-0">{{ $stock->price }} Dzd</span>
                            </div>
                        @endif

                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title product-name mb-3">{{ $stock->product->unit_name }}</h6>
                        <div class="quantity-controls mt-auto">
                            <button class="btn btn-outline-primary btn-sm" onclick="decrementQuantity(this)"><i
                                    class='bx bx-minus'></i></button>
                            <input type="number" class="form-control form-control-sm mx-2" value="1" min="1"
                                style="width: 60px;" onchange="updateQuantity(this)">
                            <button class="btn btn-outline-primary btn-sm" onclick="incrementQuantity(this)"><i
                                    class='bx bx-plus'></i></button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>


    {{ $stocks->onEachSide(1)->links() }}



@endsection

@section('page-script')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
