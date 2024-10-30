@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ $inbox_status_chart->cdn() }}"></script>
    {{ $inbox_status_chart->script() }}
    {{ $outbox_status_chart->script() }}
    {{ $inbox_monthly_chart->script() }}
    {{ $outbox_monthly_chart->script() }}

@endsection

@section('content')
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
    <div class="row">
        <div class="col-6 mb-4">
            <div class="card">

                <h5 class="mx-4 mt-1">{{ __('Inbox Status Chart') }} </h5>
                <div class="mb-2">
                    {{ $inbox_status_chart->container() }}
                </div>
            </div>
        </div>

        <div class="col-6 mb-4">
          <div class="card">

              <h5 class="mx-4 mt-1">{{ __('Outbox Status Chart') }} </h5>
              <div class="mb-2">
                  {{ $outbox_status_chart->container() }}
              </div>
          </div>
      </div>
    </div>
    <div class="row">
      <div class="col-6 mb-4">
          <div class="card">

              <h5 class="mx-4 mt-1">{{ __('Inbox Monthly Chart') }} </h5>
              <div class="mb-2">
                  {{ $inbox_monthly_chart->container() }}
              </div>
          </div>
      </div>

      <div class="col-6 mb-4">
        <div class="card">

            <h5 class="mx-4 mt-1">{{ __('Outbox Monthly Chart') }} </h5>
            <div class="mb-2">
                {{ $outbox_monthly_chart->container() }}
            </div>
        </div>
    </div>
  </div>

@endsection
