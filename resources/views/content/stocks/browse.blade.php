@extends('layouts/contentNavbarLayout')

@section('title', __('Stocks'))

@section('vendor-style')
    <style>
        .product-card {
            transition: all 0.3s ease;
            height: 100%;
        }

        .product-image-container {
            position: relative;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-name {
            height: 50px;
            /* Adjust as needed */
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .owner-info {
            height: 60px;
            display: flex;
            align-items: center;
        }

        .product-price {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgb(225, 225, 225);
            padding: 8px 12px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
            min-width: 100px;
        }

        .product-price .unit-price .price-value {
            font-weight: 600;
        }

        .product-price .promo-hint small {
            font-size: 0.75rem;
        }

        /* Pack units badge - top right corner */
        .pack-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #1369B9;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 5;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Promo badge - top left corner */
        .promo-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #dc3545;
            color: white;
            padding: 6px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 5;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            max-width: 120px;
            text-align: center;
            line-height: 1.2;
        }

        .image-preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgb(255, 255, 255);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        .preview-image {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .product-image-container:hover .image-preview {
            display: flex;
        }

        .fs-7 {
            font-size: 0.875rem;
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

        /* Small helper for the pack info line under the title */
        .pack-inline-info {
            font-size: 0.8rem;
        }

        .strike-old {
            text-decoration: line-through;
            color: #999;
            margin-right: 6px;
            font-size: 0.9rem;
        }

        /* Unit/Pack toggle buttons */
        .unit-pack-toggle {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
        }

        .unit-pack-toggle .btn {
            font-size: 0.75rem;
            padding: 2px 8px;
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
                    <label for="category"
                        class="form-label">{{ auth()->user()->role_is('broker') ? __('Provider filter') : __('Broker filter') }}</label>
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
                {{-- inside the @foreach ($stocks->items() as $stock) block, update the data extraction --}}
                @php
                    $stock_id = $stock->id;
                    $stock_price = $stock->show_price ? $stock->price : null;
                    $product = $stock->product;
                    $product_name = $product->unit_name;
                    $product_image = $product->image();
                    $owner_name = $stock->owner->enterprise();
                    $owner_image = $stock->owner->image();
                    $quantity = $stock->in_cart();

                    // Promo data
                    $hasPromo = !is_null($stock->promo);
                    $promoTarget = $hasPromo ? ($stock->promo->target_quantity ?? null) : null;
                    $promoPrice = $hasPromo ? ($stock->promo->new_price ?? null) : null;

                    // Pack data: show only if pack_units exists (ignore name/price)
                    $hasPack = !empty($product->pack_units);
                    $packUnits = $hasPack ? (int) $product->pack_units : null;
                @endphp
                <div class="col-md-3 mb-4">
                    <div class="card product-card"
                        data-stock-id="{{ $stock_id }}"
                        data-stock-price="{{ $stock_price }}"
                        data-product-name="{{ $product_name }}"
                        data-product-image="{{ $product_image }}"
                        data-owner-name="{{ $owner_name }}"
                        data-owner-image="{{ $owner_image }}"
                        data-has-promo="{{ $hasPromo ? 1 : 0 }}"
                        data-promo-target="{{ $promoTarget }}"
                        data-promo-price="{{ $promoPrice }}"
                        data-has-pack="{{ $hasPack ? 1 : 0 }}"
                        data-pack-units="{{ $packUnits }}"
                    >
                        <div class="card-header owner-info p-2 w-100 mx-2">
                            <div class="d-flex align-items-center w-100">
                                <div class="avatar flex-shrink-0 me-2">
                                    <img src="{{ $owner_image }}" alt="User" class="rounded-circle"
                                        style="width: 32px; height: 32px;">
                                </div>
                                <div class="flex-grow-1 w-75">
                                    <h5 class="mb-0 text-fit" title="{{ $owner_name }}">{{ $owner_name }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="product-image-container position-relative mx-3 mt-2">
                            <img class="product-image rounded-3" src="{{ $product_image }}" alt="{{ $product_name }}">

                            {{-- Promo badge - top left corner --}}
                            @if ($hasPromo && $promoTarget && $promoPrice)
                                <div class="promo-badge">
                                    <div><i class='bx bx-purchase-tag'></i> {{ __('Promo') }}</div>
                                    <div style="font-size: 0.65rem;">{{ $promoPrice }} Dzd {{ __('from') }} {{ $promoTarget }}+</div>
                                </div>
                            @endif

                            {{-- Pack units badge - top right corner --}}
                            @if ($hasPack)
                                <div class="pack-badge">
                                    <i class='bx bx-package'></i> {{ $packUnits }}
                                </div>
                            @endif

                            @if ($stock->show_price)
                                <div class="product-price shadow-lg">
                                    <div class="unit-price">
                                        @if ($hasPromo && $promoTarget && $promoPrice)
                                            <span class="strike-old d-none" data-role="old-price">{{ $stock_price }} Dzd</span>
                                        @endif
                                        <span class="price-value" data-original="{{ $stock_price }}">{{ $stock_price }} Dzd</span>
                                    </div>
                                    {{-- Promo hint removed from price box --}}
                                </div>
                            @endif
                            <div class="image-preview">
                                <img src="{{ $product_image }}" alt="{{ $product_name }}" class="preview-image">
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title product-name mb-1 text-fit text-center" title="{{ $product_name }}">{{ $product_name }}</h5>

                            <div class="mt-auto">
                                <button class="btn btn-primary cart-button {{ $quantity ? 'hidden' : '' }}"
                                    onclick="toggleQuantityControls(this)">
                                    <span style="font-size:0.8rem !important"><i class='bx bx-cart mx-1'></i>{{ __('Add to Cart') }}</span>
                                </button>
                                <div class="quantity-section {{ $quantity ? 'active' : 'hidden' }}">
                                    {{-- Unit/Pack toggle buttons for products with packs - positioned above --}}
                                    {{-- @if ($hasPack)
                                        <div class="unit-pack-toggle w-100">
                                            <div class="btn-group" role="group" aria-label="Unit type">
                                                <input type="radio" class="btn-check" name="unit-type-{{ $stock_id }}" id="unit-{{ $stock_id }}" autocomplete="off" checked>
                                                <label class="btn btn-outline-primary" for="unit-{{ $stock_id }}">{{ __('Units') }}</label>

                                                <input type="radio" class="btn-check" name="unit-type-{{ $stock_id }}" id="pack-{{ $stock_id }}" autocomplete="off">
                                                <label class="btn btn-outline-success" for="pack-{{ $stock_id }}">{{ __('Packs') }}</label>
                                            </div>
                                        </div>
                                    @endif --}}

                                    {{-- Quantity controls positioned below the toggle --}}
                                    <div class="d-flex align-items-center justify-content-center w-100">
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
                </div>
            @endforeach
        @else
            <div class="container-xxl container-p-y d-flex justify-content-center">
                <div class="misc-wrapper" style="text-align: center !important">
                    <h3 class="mb-2 mx-2">{{ __('No results') }}</h3>
                    <p class="mb-6 mx-2">
                        {{ __('Your search did not return any results') }}
                    </p>
                    <div class="mt-6">
                        <img src="{{ url('/assets/img/illustrations/Search-rafiki.png') }}" width="400" class="img-fluid">
                    </div>
                </div>
            </div>

        @endif



    </div>


    {{ $stocks->onEachSide(1)->links('pagination::bootstrap') }}



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

            // Update preview price immediately without triggering AJAX
            updatePricePreview($(inputElement).closest('.card'), 1);

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

        // Update price preview (unit price displayed) based on promo rules
        function updatePricePreview(card, quantity) {
            const hasPromo = parseInt(card.data('has-promo')) === 1;
            const promoTarget = parseInt(card.data('promo-target'));
            const promoPrice = parseFloat(card.data('promo-price'));
            const stockPrice = parseFloat(card.data('stock-price'));

            if (isNaN(stockPrice)) {
                return; // No price to show
            }

            const priceBox = card.find('.product-price');
            const priceValueEl = priceBox.find('.unit-price .price-value');
            const oldPriceEl = priceBox.find('.unit-price [data-role="old-price"]');

            let effectivePrice = stockPrice;

            if (hasPromo && !isNaN(promoTarget) && !isNaN(promoPrice) && quantity >= promoTarget) {
                effectivePrice = promoPrice;
                // If we have an old price span, show it and highlight current as promo
                if (oldPriceEl.length) {
                    oldPriceEl.removeClass('d-none');
                }
                priceValueEl.addClass('text-danger');
            } else {
                if (oldPriceEl.length) {
                    oldPriceEl.addClass('d-none');
                }
                priceValueEl.removeClass('text-danger');
            }

            priceValueEl.text(`${effectivePrice} Dzd`);
        }

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
            const stockPrice = parseFloat(card.data('stock-price'));
            const productName = card.data('product-name');
            const productImage = card.data('product-image');
            const ownerName = card.data('owner-name');
            const ownerImage = card.data('owner-image');
            const quantity = parseInt(input.value) || 0;

            // Calculate effective price based on promo rules
            const hasPromo = parseInt(card.data('has-promo')) === 1;
            const promoTarget = parseInt(card.data('promo-target'));
            const promoPrice = parseFloat(card.data('promo-price'));

            let effectivePrice = stockPrice;
            if (hasPromo && !isNaN(promoTarget) && !isNaN(promoPrice) && quantity >= promoTarget) {
                effectivePrice = promoPrice;
            }

            // Update the price preview immediately (no server call yet)
            updatePricePreview(card, quantity);

            // Prepare data for server - use effective price instead of stock price
            formdata.append(`items[stock_${stockId}][stock_id]`, stockId);
            formdata.append(`items[stock_${stockId}][stock_price]`, effectivePrice); // Changed from stockPrice to effectivePrice
            formdata.append(`items[stock_${stockId}][product_name]`, productName);
            formdata.append(`items[stock_${stockId}][product_image]`, productImage);
            formdata.append(`items[stock_${stockId}][owner_name]`, ownerName);
            formdata.append(`items[stock_${stockId}][owner_image]`, ownerImage);
            formdata.append(`items[stock_${stockId}][quantity]`, quantity);

            // Start new timer
            updateCartTimer = setTimeout(() => {

                // console.log(formdata);
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
            const card = $(button).closest('.card');
            const section = button.closest('.quantity-section');
            const input = section.querySelector('input[type="number"]');
            const hasPack = parseInt(card.data('has-pack')) === 1;
            const packUnits = parseInt(card.data('pack-units')) || 1;

            // Check if pack mode is selected
            const isPackMode = hasPack && section.querySelector('input[name*="unit-type"]:checked')?.id.includes('pack');
            const increment = isPackMode ? packUnits : 1;

            input.value = parseInt(input.value || '0') + increment;
            handleQuantityChange(input);
        }

        function decrementQuantity(button) {
            const card = $(button).closest('.card');
            const section = button.closest('.quantity-section');
            const input = section.querySelector('input[type="number"]');
            const hasPack = parseInt(card.data('has-pack')) === 1;
            const packUnits = parseInt(card.data('pack-units')) || 1;

            // Check if pack mode is selected
            const isPackMode = hasPack && section.querySelector('input[name*="unit-type"]:checked')?.id.includes('pack');
            const decrement = isPackMode ? packUnits : 1;

            const newValue = Math.max(0, parseInt(input.value || '0') - decrement);
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

            // Initialize price preview based on existing quantities in cart
            $('.card.product-card').each(function() {
                const card = $(this);
                const qtyInput = card.find('.quantity-section input[type="number"]');
                if (qtyInput.length) {
                    const q = parseInt(qtyInput.val() || '0');
                    updatePricePreview(card, q);
                }
            });
        });
    </script>
@endsection
