@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@php
    $order_status_chart = empty($order_status_chart) ? null : $order_status_chart->build();
    $order_count_chart = empty($order_count_chart) ? null : $order_count_chart->build();
    $user_status_chart = empty($user_status_chart) ? null : $user_status_chart->build();
    $user_count_chart = empty($user_count_chart) ? null : $user_count_chart->build();
@endphp

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{ empty($order_status_chart) ?: $order_status_chart->script() }}
    {{ empty($order_count_chart) ?: $order_count_chart->script() }}
    {{ empty($user_status_chart) ?: $user_status_chart->script() }}
    {{ empty($user_count_chart) ?: $user_count_chart->script() }}
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
        <form id="form" method="GET" action="{{ route('dashboard-analytics') }}">
            <div class="row">

                <div class="col-6 mb-4">
                    <div class="card h-100">
                        <div class="card-title mx-3 mt-3">
                            <div class="row  justify-content-between">
                                <div class="form-group col-7 card-title-elements">
                                    <h5 class="mx-4 mt-1">{{ __('Order Status Chart') }} </h5>
                                </div>
                                <div class="form-group col-5">
                                    <select class="form-select" id="orderStatusFilter" name="orderStatusFilter">
                                        <option value="" {{ request('orderStatusFilter') ?: 'selected' }}>
                                            {{ __('All time') }}</option>
                                        <option value="1" {{ empty(request('orderStatusFilter')) ?: 'selected' }}>
                                            {{ __('This month') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            {{ $order_status_chart->container() }}
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card h-100">

                        <div class="card-title mx-3 mt-3">
                            <div class="row  justify-content-between">
                                <div class="form-group col-7 card-title-elements">
                                    <h5 class="mx-4 mt-1">{{ __('Order Count Chart') }} </h5>
                                </div>
                                <div class="form-group col-5">
                                    <select class="form-select" id="orderCountFilter" name="orderCountFilter">
                                        <option value="monthly"
                                            {{ request('orderCountFilter') != 'daily' ? 'selected' : '' }}>
                                            {{ __('Monthly') }}</option>
                                        <option value="daily"
                                            {{ request('orderCountFilter') == 'daily' ? 'selected' : '' }}>
                                            {{ __('Daily') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            {{ $order_count_chart->container() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

              <div class="col-6 mb-4">
                  <div class="card h-100">
                      <div class="card-title mx-3 mt-3">
                          <div class="row  justify-content-between">
                              <div class="form-group col-7 card-title-elements">
                                  <h5 class="mx-4 mt-1">{{ __('User Status Chart') }} </h5>
                              </div>
                              <div class="form-group col-5">
                                  <select class="form-select" id="userStatusFilter" name="userStatusFilter">
                                      <option value="" {{ request('userStatusFilter') ?: 'selected' }}>
                                          {{ __('All time') }}</option>
                                      <option value="1" {{ empty(request('userStatusFilter')) ?: 'selected' }}>
                                          {{ __('This month') }}</option>
                                  </select>
                              </div>
                          </div>
                      </div>

                      <div class="card-body">
                          {{ $user_status_chart->container() }}
                      </div>
                  </div>
              </div>
              <div class="col-6 mb-4">
                  <div class="card h-100">

                      <div class="card-title mx-3 mt-3">
                          <div class="row  justify-content-between">
                              <div class="form-group col-7 card-title-elements">
                                  <h5 class="mx-4 mt-1">{{ __('User Count Chart') }} </h5>
                              </div>
                              <div class="form-group col-5">
                                  <select class="form-select" id="userCountFilter" name="userCountFilter">
                                      <option value="monthly"
                                          {{ request('userCountFilter') != 'daily' ? 'selected' : '' }}>
                                          {{ __('Monthly') }}</option>
                                      <option value="daily"
                                          {{ request('userCountFilter') == 'daily' ? 'selected' : '' }}>
                                          {{ __('Daily') }}</option>
                                  </select>
                              </div>
                          </div>
                      </div>

                      <div class="card-body">
                          {{ $user_count_chart->container() }}
                      </div>
                  </div>
              </div>
          </div>
        </form>
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

@section('page-script')
    <script>
        $(document).ready(function() {
            function submitForm() {
                $("#form").submit();
            }
            $('#orderStatusFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#orderCountFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#userStatusFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#userCountFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });
        });
    </script>
@endsection
