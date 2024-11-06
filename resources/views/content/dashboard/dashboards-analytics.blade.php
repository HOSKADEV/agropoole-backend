@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')

@endsection

@section('content')
    @if (auth()->user()->role_is('admin'))
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{ __('Welcome to your admin dashboard!') }} 🎉</h5>
                                <p class="mb-4">
                                    {{ __('Here, you can asily track annual revenue trends, monitor weekly and monthly order patterns, and gain insights into user behavior through comprehensive reports.') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140"
                                    alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                    data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (in_array(auth()->user()->role_is(), ['provider', 'broker', 'store', 'driver']))
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card h-100">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title m-0">{{ __('Orders inbox') }}</h5>
                                {{-- <p class="text-muted small mb-0">{{ __('Real-time order statistics') }}</p> --}}
                            </div>
                        </div>

                        <!-- Main Stats Row -->
                        <div class="row g-3">
                            <!-- Total Orders -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('Total') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($all_times_inbox_count > 0) text-primary @else text-secondary @endif">
                                        {{ $all_times_inbox_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($all_times_inbox_count > 0) text-primary @else text-secondary @endif">
                                        <i class="bx bx-calendar me-1"></i>
                                        <span class="small">{{ __('All time') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- This month Orders -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('New this month') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($this_month_inbox_count > 0) text-info @else text-secondary @endif">
                                        {{ $this_month_inbox_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($this_month_inbox_count > 0) text-info @else text-secondary @endif">
                                        <i class="bx bx-calendar-event me-1"></i>
                                        <span class="small">{{ __('This month') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Today's Orders -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('New Today') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($today_inbox_count > 0) text-success @else text-secondary @endif">
                                        {{ $today_inbox_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($today_inbox_count > 0) text-success @else text-secondary @endif">
                                        <i class="bx bx-trending-up me-1"></i>
                                        <span class="small">{{ __('Today') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Actions -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('Need Action') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($pending_inbox_count > 0) text-warning @else text-secondary @endif">
                                        {{ $pending_inbox_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($pending_inbox_count > 0) text-warning @else text-secondary @endif">
                                        <i class="bx bx-bell me-1"></i>
                                        <span class="small">{{ __('Pending') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (in_array(auth()->user()->role_is(), ['broker', 'store']))
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card h-100">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title m-0">{{ __('Orders outbox') }}</h5>
                                {{-- <p class="text-muted small mb-0">{{ __('Real-time order statistics') }}</p> --}}
                            </div>
                        </div>

                        <!-- Main Stats Row -->
                        <div class="row g-3">
                            <!-- Total Orders -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('Total') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($all_times_outbox_count > 0) text-primary @else text-secondary @endif">
                                        {{ $all_times_outbox_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($all_times_outbox_count > 0) text-primary @else text-secondary @endif">
                                        <i class="bx bx-calendar me-1"></i>
                                        <span class="small">{{ __('All time') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- This month Orders -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('New this month') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($this_month_outbox_count > 0) text-info @else text-secondary @endif">
                                        {{ $this_month_outbox_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($this_month_outbox_count > 0) text-info @else text-secondary @endif">
                                        <i class="bx bx-calendar-event me-1"></i>
                                        <span class="small">{{ __('This month') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Today's Orders -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('New Today') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($today_outbox_count > 0) text-success @else text-secondary @endif">
                                        {{ $today_outbox_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($today_outbox_count > 0) text-success @else text-secondary @endif">
                                        <i class="bx bx-trending-up me-1"></i>
                                        <span class="small">{{ __('Today') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Actions -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('Need Action') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($pending_outbox_count > 0) text-warning @else text-secondary @endif">
                                        {{ $pending_outbox_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($pending_outbox_count > 0) text-warning @else text-secondary @endif">
                                        <i class="bx bx-bell me-1"></i>
                                        <span class="small">{{ __('Pending') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (in_array(auth()->user()->role_is(), ['provider', 'broker', 'store']))
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card h-100">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title m-0">{{ __('Stocks') }}</h5>
                                {{-- <p class="text-muted small mb-0">{{ __('Real-time order statistics') }}</p> --}}
                            </div>
                        </div>

                        <!-- Main Stats Row -->
                        <div class="row g-3">
                            <!-- Total Orders -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('Total') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($all_times_stock_count > 0) text-primary @else text-secondary @endif">
                                        {{ $all_times_stock_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($all_times_stock_count > 0) text-primary @else text-secondary @endif">
                                        <i class="bx bx-calendar me-1"></i>
                                        <span class="small">{{ __('All time') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- This month Orders -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('New this month') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($this_month_stock_count > 0) text-info @else text-secondary @endif">
                                        {{ $this_month_stock_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($this_month_stock_count > 0) text-info @else text-secondary @endif">
                                        <i class="bx bx-calendar-event me-1"></i>
                                        <span class="small">{{ __('This month') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Today's Orders -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('New Today') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($today_stock_count > 0) text-success @else text-secondary @endif">
                                        {{ $today_stock_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($today_stock_count > 0) text-success @else text-secondary @endif">
                                        <i class="bx bx-trending-up me-1"></i>
                                        <span class="small">{{ __('Today') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Actions -->
                            <div class="col-6 col-md-3">
                                <div class="d-flex flex-column border rounded p-3 h-100">
                                    <span class="text-muted small">{{ __('Need Action') }}</span>
                                    <h3
                                        class="mt-2 mb-1 @if ($insufficient_stock_count > 0) text-danger @else text-secondary @endif">
                                        {{ $insufficient_stock_count }}
                                    </h3>
                                    <div
                                        class="d-flex align-items-center mt-auto rounded-bottom @if ($insufficient_stock_count > 0) text-danger @else text-secondary @endif">
                                        <i class="bx bx-error me-1"></i>
                                        <span class="small">{{ __('Insufficient') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif




@endsection
