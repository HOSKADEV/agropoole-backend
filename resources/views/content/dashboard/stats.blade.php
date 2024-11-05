@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@php
    $inbox_status_chart = empty($inbox_status_chart) ?: $inbox_status_chart->build();
    $outbox_status_chart = empty($outbox_status_chart) ?: $outbox_status_chart->build();
    $inbox_monthly_chart = empty($inbox_monthly_chart) ?: $inbox_monthly_chart->build();
    $outbox_monthly_chart = empty($outbox_monthly_chart) ?: $outbox_monthly_chart->build();

    $top_stocks_chart = empty($top_stocks_chart) ?: $top_stocks_chart->build();
@endphp

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{ empty($inbox_status_chart) ?: $inbox_status_chart->script() }}
    {{ empty($outbox_status_chart) ?: $outbox_status_chart->script() }}
    {{ empty($inbox_monthly_chart) ?: $inbox_monthly_chart->script() }}
    {{ empty($outbox_monthly_chart) ?: $outbox_monthly_chart->script() }}
    {{ empty($top_stocks_chart) ?: $top_stocks_chart->script() }}
@endsection

@section('content')
    <form id="form" method="GET" action="{{ route('stats') }}">
        @if (in_array(auth()->user()->role_is(), ['provider', 'broker', 'store', 'driver']))
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="card h-100">
                        <div class="card-title mx-3 mt-3">
                            <div class="row  justify-content-between">
                                <div class="form-group col-8 card-title-elements">
                                    <h5 class="mx-4 mt-1">{{ __('Inbox Status Chart') }} </h5>
                                </div>
                                <div class="form-group col-4">
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
                            <div class="form-group col-8 card-title-elements">
                                <h5 class="mx-4 mt-1">{{ __('Inbox Monthly Chart') }} </h5>
                            </div>
                        </div>

                        <div class="card-body">
                            {{ $inbox_monthly_chart->container() }}
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
                                <div class="form-group col-8 card-title-elements">
                                    <h5 class="mx-4 mt-1">{{ __('Outbox Status Chart') }} </h5>
                                </div>
                                <div class="form-group col-4">
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
                            <div class="form-group col-8 card-title-elements">
                                <h5 class="mx-4 mt-1">{{ __('Outbox Monthly Chart') }} </h5>
                            </div>
                        </div>

                        <div class="card-body">
                            {{ $outbox_monthly_chart->container() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (in_array(auth()->user()->role_is(), ['provider', 'broker', 'store']))
        <div class="row">

          <div class="col-4 mb-4">
              <div class="card h-100">
                  <div class="card-title mx-3 mt-3">
                      <div class="row  justify-content-between">
                          <div class="form-group col-6 card-title-elements">
                              <h5>{{ __('Top Stocks') }} </h5>
                          </div>
                          <div class="form-group col-6">
                              <select class="form-select" id="topStocksFilter" name="topStocksFilter">
                                  <option value="" {{ request('topStocksFilter') ?: 'selected' }}>
                                      {{ __('All time') }}</option>
                                  <option value="1" {{ empty(request('topStocksFilter')) ?: 'selected' }}>
                                      {{ __('This month') }}</option>
                              </select>
                          </div>
                      </div>
                  </div>

                  <div class="card-body">
                      {{ $top_stocks_chart->container() }}
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

            $('#outboxStatusFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

            $('#topStocksFilter').on('change', function() {
                timer = setTimeout(function() {
                    submitForm();
                }, 1000);
            });

        });
    </script>
@endsection
