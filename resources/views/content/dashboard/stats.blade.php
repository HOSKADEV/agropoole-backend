@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{ empty($inbox_status_chart) ?: $inbox_status_chart->script() }}
    {{ empty($outbox_status_chart) ?: $outbox_status_chart->script() }}
    {{ empty($inbox_monthly_chart) ?: $inbox_monthly_chart->script() }}
    {{ empty($outbox_monthly_chart) ?: $outbox_monthly_chart->script() }}

@endsection

@section('content')
    @if (in_array(auth()->user()->role_is(), ['provider', 'broker', 'store', 'driver']))
        <div class="row">
            <div class="col-6 mb-4">
                <div class="card h-100">

                    <h5 class="mx-4 mt-1">{{ __('Inbox Status Chart') }} </h5>
                    <div class="mb-2">
                        {{ $inbox_status_chart->container() }}
                    </div>
                </div>
            </div>

            <div class="col-6 mb-4">
                <div class="card h-100">

                    <h5 class="mx-4 mt-1">{{ __('Inbox Monthly Chart') }} </h5>
                    <div class="mb-2">
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

                    <h5 class="mx-4 mt-1">{{ __('Outbox Status Chart') }} </h5>
                    <div class="mb-2">
                        {{ $outbox_status_chart->container() }}
                    </div>
                </div>
            </div>
            <div class="col-6 mb-4">
                <div class="card h-100">

                    <h5 class="mx-4 mt-1">{{ __('Outbox Monthly Chart') }} </h5>
                    <div class="mb-2">
                        {{ $outbox_monthly_chart->container() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
