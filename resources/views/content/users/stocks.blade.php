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
            height: 40px;
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
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
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
        <form id="form" method="GET" action="{{ route('user-stocks', $user->id) }}">
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
                <div class="col-md-3 mb-3">
                    <div class="card product-card">
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
                            @if ($stock->show_price)
                                <div class="product-price shadow-lg">
                                    <span class="h6 mb-0">{{ $stock_price }} Dzd</span>
                                </div>
                            @endif
                            <div class="image-preview">
                                <img src="{{ $product_image }}" alt="{{ $product_name }}" class="preview-image">
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column py-2 px-3">
                            <h5 class="card-title product-name mb-2 text-center">{{ $product_name }}</h5>
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
