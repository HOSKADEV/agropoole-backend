@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
      <div class="col-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <h5>{{__('Inbox')}}</h5>
              <div class="avatar flex-shrink-0">
                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-archive-in"></i></span>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <p class="mb-1">{{__('All times')}}</p>
            <h4 class="card-title mb-3">{{$all_times_inbox_count}}</h4>
            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
              </div>

              <div class="col">
                <p class="mb-1">{{__('This month')}}</p>
            <h4 class="card-title mb-3">{{$this_month_inbox_count}}</h4>
            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <div class="avatar flex-shrink-0">
                <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success" class="rounded">
              </div>
              <div class="dropdown">
                <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="bx bx-dots-vertical-rounded text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3" style="">
                  <a class="dropdown-item" href="javascript:void(0);">View More</a>
                  <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                </div>
              </div>
            </div>
            <p class="mb-1">Profit</p>
            <h4 class="card-title mb-3">$12,628</h4>
            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
          </div>
        </div>
      </div>
      <div class="col-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <div class="avatar flex-shrink-0">
                <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success" class="rounded">
              </div>
              <div class="dropdown">
                <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="bx bx-dots-vertical-rounded text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3" style="">
                  <a class="dropdown-item" href="javascript:void(0);">View More</a>
                  <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                </div>
              </div>
            </div>
            <p class="mb-1">Profit</p>
            <h4 class="card-title mb-3">$12,628</h4>
            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
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
