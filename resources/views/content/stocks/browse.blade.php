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

    <h4 class="fw-bold py-3 mb-3">{{ __('Stocks') }}
        {{-- <span class="text-muted fw-light">{{ __('Stocks') }} /</span> {{ __('Browse stocks') }} --}}
        {{-- <button type="button" class="btn btn-primary" id="create" style="float:right">{{ __('Add Stock') }}</button> --}}
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card mb-3 pb-3">
        <form id="form" method="GET" action="{{ route('cart-index') }}">
            <div class="row  justify-content-between">
                <div class="form-group col mx-3 my-3">
                    <label for="category" class="form-label">{{ __('Category filter') }}</label>
                    <select class="form-select" id="category" name="category">
                        <option value=""> {{ __('Not selected') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request()->get('category') == $category->id ? 'selected' : '' }}> {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col mx-3 my-3">
                    <label for="subcategory" class="form-label">{{ __('Subcategory filter') }}</label>
                    <select class="form-select" id="subcategory" name="subcategory">
                        <option value=""> {{ __('Not selected') }}</option>
                        @foreach ($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}"
                                {{ request()->get('subcategory') == $subcategory->id ? 'selected' : '' }}>
                                {{ $subcategory->name }} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col mx-3 my-3">
                    <label for="category" class="form-label">{{auth()->user()->role_is('broker') ? __('Provider filter') : __('Broker filter')}}</label>
                    <select class="form-select" id="owner" name="owner">
                        <option value=""> {{ __('Not selected') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ request()->get('owner') == $user->id ? 'selected' : '' }}> {{ $user->enterprise() }}
                            </option>
                        @endforeach
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

        @if (count($stocks->items()))
            @foreach ($stocks->items() as $stock)
                @php
                    $stock_id = $stock->id;
                    $stock_price = $stock->show_price ? $stock->price : null;
                    $product_name = $stock->product->unit_name;
                    $product_image = $stock->product->image();
                    $owner_name = $stock->owner->enterprise();
                    $owner_image = $stock->owner->image();
                    $quantity = $stock->in_cart();
                @endphp
                <div class="col-md-3 mb-4">
                    <div class="card product-card"
                        data-stock-id="{{ $stock_id }}"
                        data-stock-price="{{ $stock_price }}"
                        data-product-name="{{ $product_name }}"
                        data-product-image="{{ $product_image }}"
                        data-owner-name="{{ $owner_name }}"
                        data-owner-image="{{ $owner_image }}">
                        <div class="card-header owner-info p-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <img src="{{ $owner_image }}" alt="User" class="rounded-circle"
                                        style="width: 40px; height: 40px;">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $owner_name }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="product-image-container">
                            <img class="product-image" src="{{ $product_image }}" alt="{{ $product_name }}">
                            @if ($stock->show_price)
                                <div class="product-price">
                                    <span class="h6 mb-0">{{ $stock_price }} Dzd</span>
                                </div>
                            @endif

                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title product-name mb-3">{{ $product_name }}</h6>
                            <div class="mt-auto">
                                <button class="btn btn-primary text-fit cart-button {{ $quantity ? 'hidden' : '' }}"
                                    onclick="toggleQuantityControls(this)">
                                    <i class='bx bx-cart mx-1'></i>{{__('Add to Cart')}}
                                </button>
                                <div class="quantity-section {{ $quantity ? 'active' : 'hidden' }}">
                                    <button class="btn btn-outline-primary btn-sm" onclick="decrementQuantity(this)">
                                        <i class='bx bx-minus'></i>
                                    </button>
                                    <input type="number" class="form-control form-control-sm mx-2"
                                        value="{{ $quantity }}" min="0" style="width: 60px;"
                                        onchange="handleQuantityChange(this)">
                                    <button class="btn btn-outline-primary btn-sm" onclick="incrementQuantity(this)">
                                        <i class='bx bx-plus'></i>
                                    </button>
                                </div>
                            </div>
                        </div>
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


    {{ $stocks->onEachSide(1)->links() }}



@endsection

@section('page-script')
    <script>
        function toggleQuantityControls(button) {
            const parentDiv = button.parentElement;
            const quantitySection = parentDiv.querySelector('.quantity-section');
            const cartButton = parentDiv.querySelector('.cart-button');

            cartButton.classList.add('hidden');
            quantitySection.classList.add('active');
            // Set initial quantity to 1 when showing controls
            const inputElement = quantitySection.querySelector('input');
            inputElement.value = 1;
            const changeEvent = new Event('change', {
                bubbles: true
            });
            inputElement.dispatchEvent(changeEvent);
        }

        function showCartButton(section) {
            const parentDiv = section.parentElement;
            const cartButton = parentDiv.querySelector('.cart-button');
            section.classList.remove('active');
            cartButton.classList.remove('hidden');
        }

        let updateCartTimer;
        let formdata = new FormData();

        // Function to handle quantity changes
        function handleQuantityChange(input) {
            const value = parseInt(input.value) || 0;
            input.value = Math.max(0, value);

            // If quantity becomes 0, show the cart button
            if (input.value === '0') {
                const section = input.closest('.quantity-section');
                showCartButton(section);
            }

            // Clear existing timer if any
            if (updateCartTimer) {
                clearTimeout(updateCartTimer);
            }
            const card = $(input).closest('.card');
            const stockId = card.data('stock-id');
            const stockPrice = card.data('stock-price');
            const productName = card.data('product-name');
            const productImage = card.data('product-image');
            const ownerName = card.data('owner-name');
            const ownerImage = card.data('owner-image');
            const quantity = input.value;

            formdata.append(`items[stock_${stockId}][stock_id]`, stockId);
            formdata.append(`items[stock_${stockId}][stock_price]`, stockPrice);
            formdata.append(`items[stock_${stockId}][product_name]`, productName);
            formdata.append(`items[stock_${stockId}][product_image]`, productImage);
            formdata.append(`items[stock_${stockId}][owner_name]`, ownerName);
            formdata.append(`items[stock_${stockId}][owner_image]`, ownerImage);
            formdata.append(`items[stock_${stockId}][quantity]`, quantity);


            // Start new timer
            updateCartTimer = setTimeout(() => {

                console.log(formdata);
                $.ajax({
                    url: '/cart/refresh', // Adjust this to your actual endpoint
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: formdata,
                    dataType: 'JSON',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Cart update failed:', error);
                        // Optionally show error to user
                    }
                });

            }, 2000); // 2 second delay
        }

        // Update all quantity change handlers to use the new function
        function incrementQuantity(button) {
            const section = button.closest('.quantity-section');
            const input = section.querySelector('input[type="number"]');
            input.value = parseInt(input.value) + 1;
            handleQuantityChange(input);
        }

        function decrementQuantity(button) {
            const section = button.closest('.quantity-section');
            const input = section.querySelector('input[type="number"]');
            const newValue = Math.max(0, parseInt(input.value) - 1);
            input.value = newValue;
            handleQuantityChange(input);
        }

        // Also bind to manual input changes

        /* $(document).on('change', '.quantity-section input[type="number"]', function() {
            handleQuantityChange(this);
        }); */

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

            $('#category').on('change', function() {
                $('#subcategory').val(null);
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            })

            $('#subcategory').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            })

            $('#owner').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            })
        });
    </script>
@endsection
