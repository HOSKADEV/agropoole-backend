@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@php
    $inbox_status_chart = empty($inbox_status_chart) ? null : $inbox_status_chart->build();
    $outbox_status_chart = empty($outbox_status_chart) ? null : $outbox_status_chart->build();
    $inbox_count_chart = empty($inbox_count_chart) ? null : $inbox_count_chart->build();
    $outbox_count_chart = empty($outbox_count_chart) ? null : $outbox_count_chart->build();
    $inbox_amount_chart = empty($inbox_amount_chart) ? null : $inbox_amount_chart->build();

    $top_products_chart = empty($top_products_chart) ? null : $top_products_chart->build();
    $top_buyers_chart = empty($top_buyers_chart) ? null : $top_buyers_chart->build();
    $top_states_chart = empty($top_states_chart) ? null : $top_states_chart->build();
    $top_sellers_chart = empty($top_sellers_chart) ? null : $top_sellers_chart->build();
@endphp

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{ empty($inbox_status_chart) ?: $inbox_status_chart->script() }}
    {{ empty($outbox_status_chart) ?: $outbox_status_chart->script() }}
    {{ empty($inbox_count_chart) ?: $inbox_count_chart->script() }}
    {{ empty($outbox_count_chart) ?: $outbox_count_chart->script() }}
    {{ empty($inbox_amount_chart) ?: $inbox_amount_chart->script() }}
    {{ empty($top_products_chart) ?: $top_products_chart->script() }}
    {{ empty($top_buyers_chart) ?: $top_buyers_chart->script() }}
    {{ empty($top_states_chart) ?: $top_states_chart->script() }}
    {{ empty($top_sellers_chart) ?: $top_sellers_chart->script() }}
@endsection

@section('content')
    <form id="form" method="GET" action="{{ route('stats') }}">
        @if (in_array(auth()->user()->role_is(), ['provider', 'broker', 'store', 'driver']))
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="card h-100">
                        <div class="card-title mx-3 mt-3">
                            <div class="row  justify-content-between">
                                <div class="form-group col-7 card-title-elements">
                                    <h5 class="mx-4 mt-1">{{ __('Inbox Status Chart') }} </h5>
                                </div>
                                <div class="form-group col-5">
                                    <select class="form-select" id="inboxStatusFilter" name="inboxStatusFilter">
                                        <option value="" {{ request('inboxStatusFilter') ?: 'selected' }}>
                                            {{ __('All time') }}</option>
                                        <option value="1" {{ empty(request('inboxStatusFilter')) ?: 'selected' }}>
                                            {{ __('This month') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            {{ $inbox_status_chart->container() }}
                        </div>
                    </div>
                </div>

                <div class="col-6 mb-4">
                    <div class="card h-100">

                        <div class="card-title mx-3 mt-3">
                            <div class="row  justify-content-between">
                                <div class="form-group col-7 card-title-elements">
                                    <h5 class="mx-4 mt-1">{{ __('Inbox Count Chart') }} </h5>
                                </div>
                                <div class="form-group col-5">
                                    <select class="form-select" id="inboxCountFilter" name="inboxCountFilter">
                                        <option value="monthly" {{ request('inboxCountFilter') != 'daily' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                        <option value="daily" {{ request('inboxCountFilter') == 'daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            {{ $inbox_count_chart->container() }}
                        </div>
                    </div>
                </div>


            </div>
        @endif

        @if (in_array(auth()->user()->role_is(), ['broker', 'store']))
            <div class="row">

                <div class="col-6 mb-4">
                    <div class="card h-100">
                        <div class="card-title mx-3 mt-3">
                            <div class="row  justify-content-between">
                                <div class="form-group col-7 card-title-elements">
                                    <h5 class="mx-4 mt-1">{{ __('Outbox Status Chart') }} </h5>
                                </div>
                                <div class="form-group col-5">
                                    <select class="form-select" id="outboxStatusFilter" name="outboxStatusFilter">
                                        <option value="" {{ request('outboxStatusFilter') ?: 'selected' }}>
                                            {{ __('All time') }}</option>
                                        <option value="1" {{ empty(request('outboxStatusFilter')) ?: 'selected' }}>
                                            {{ __('This month') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            {{ $outbox_status_chart->container() }}
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card h-100">

                      <div class="card-title mx-3 mt-3">
                        <div class="row  justify-content-between">
                            <div class="form-group col-7 card-title-elements">
                                <h5 class="mx-4 mt-1">{{ __('Outbox Count Chart') }} </h5>
                            </div>
                            <div class="form-group col-5">
                                <select class="form-select" id="outboxCountFilter" name="outboxCountFilter">
                                    <option value="monthly" {{ request('outboxCountFilter') != 'daily' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                    <option value="daily" {{ request('outboxCountFilter') == 'daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                        <div class="card-body">
                            {{ $outbox_count_chart->container() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (in_array(auth()->user()->role_is(), ['provider', 'broker', 'store']))
            <div class="row">

                <div class="col-4 mb-4">
                    <div class="card h-100">
                        <div class="card-title mx-3 mt-3 mb-1">
                            <div class="row  justify-content-center">
                                <div class="form-group col-8 text-center">
                                    <h5>{{ __('Top Products') }} </h5>
                                </div>
                                <div class="form-group col-8">
                                    <select class="form-select" id="topProductsFilter" name="topProductsFilter">
                                        <option value="" {{ request('topProductsFilter') ?: 'selected' }}>
                                            {{ __('All time') }}</option>
                                        <option value="1" {{ empty(request('topProductsFilter')) ?: 'selected' }}>
                                            {{ __('This month') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body m-1 p-1">
                            {{ $top_products_chart->container() }}
                        </div>
                    </div>
                </div>

                <div class="col-4 mb-4">
                    <div class="card h-100">
                        <div class="card-title mx-3 mt-3 mb-1">
                            <div class="row  justify-content-center">
                                <div class="form-group col-8 text-center">
                                    <h5>{{ __('Top Buyers') }} </h5>
                                </div>
                                <div class="form-group col-8">
                                    <select class="form-select" id="topBuyersFilter" name="topBuyersFilter">
                                        <option value="" {{ request('topBuyersFilter') ?: 'selected' }}>
                                            {{ __('All time') }}</option>
                                        <option value="1" {{ empty(request('topBuyersFilter')) ?: 'selected' }}>
                                            {{ __('This month') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body m-1 p-1">
                            {{ $top_buyers_chart->container() }}
                        </div>
                    </div>
                </div>

                <div class="col-4 mb-4">
                    <div class="card h-100">
                        <div class="card-title mx-3 mt-3 mb-1">
                            <div class="row  justify-content-center">
                                <div class="form-group col-8 text-center">
                                    <h5>{{ __('Top States') }} </h5>
                                </div>
                                <div class="form-group col-8">
                                    <select class="form-select" id="topStatesFilter" name="topStatesFilter">
                                        <option value="" {{ request('topStatesFilter') ?: 'selected' }}>
                                            {{ __('All time') }}</option>
                                        <option value="1" {{ empty(request('topStatesFilter')) ?: 'selected' }}>
                                            {{ __('This month') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body m-1 p-1">
                            {{ $top_states_chart->container() }}
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">

              <div class="col-12 mb-4">
                  <div class="card h-100">
                      <div class="card-title mx-3 mt-3 mb-1">
                          <div class="row  justify-content-between">
                              <div class="form-group col-10 card-title-elements">
                                  <h5>{{ __('Inbox Amount Chart') }} </h5>
                              </div>
                              <div class="form-group col-2">
                                  <select class="form-select" id="inboxAmountFilter" name="inboxAmountFilter">
                                    @php
                                      $this_year = now()->year;
                                    @endphp
                                      @for ($year = $this_year; $year>=2024; $year--)
                                      <option value="{{$year}}" {{ request('inboxAmountFilter') == $year ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                      @endfor

                                  </select>
                              </div>
                          </div>
                      </div>

                      <div class="card-body m-1 p-1">
                          {{ $inbox_amount_chart->container() }}
                      </div>
                  </div>
              </div>

          </div>
        @endif

        @if (in_array(auth()->user()->role_is(), ['driver']))
            <div class="row">

                <div class="col-12 mb-4">
                    <div class="card h-100">
                        <div class="card-title mx-3 mt-3 mb-1">
                            <div class="row  justify-content-between">
                                <div class="form-group col-7 card-title-elements">
                                    <h5>{{ __('Top Sellers') }} </h5>
                                </div>
                                <div class="form-group col-5">
                                    <select class="form-select" id="topSellersFilter" name="topSellersFilter">
                                        <option value="" {{ request('topSellersFilter') ?: 'selected' }}>
                                            {{ __('All time') }}</option>
                                        <option value="1" {{ empty(request('topSellersFilter')) ?: 'selected' }}>
                                            {{ __('This month') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body m-1 p-1">
                            {{ $top_sellers_chart->container() }}
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </form>

@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            function submitForm() {
                $("#form").submit();
            }
            $('#inboxStatusFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#inboxAmountFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#outboxStatusFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#inboxCountFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#outboxCountFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#topProductsFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });
            $('#topBuyersFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#topStatesFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#topSellersFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });
        });
    </script>
@endsection
