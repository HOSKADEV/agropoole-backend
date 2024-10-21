@extends('layouts/contentNavbarLayout')

@section('title', __('Info'))

@section('content')

    <h4 class="fw-bold py-3 mb-3">
        <span class="text-muted fw-light">{{ __('Order') }} /</span> {{ __('Info') }}
    </h4>


    <!-- Content wrapper -->
    <div class="content-wrapper">

        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">


            <!-- Order Details Table -->

            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0">{{__('Order details')}}</h5>
                            {{-- <h6 class="m-0"><a href=" javascript:void(0)">Edit</a></h6> --}}
                        </div>
                        <div class="card-datatable table-responsive">
                            <table class="datatables-order-details table border-top">
                                <thead>
                                    <tr>
                                        {{-- <th></th> --}}
                                        <th>#</th>
                                        <th class="w-50">{{__('Product')}}</th>
                                        <th class="w-25">{{__('Price')}}</th>
                                        <th class="w-25">{{__('Quantity')}}</th>
                                        <th>{{__('Amount')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->cart->items as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center text-nowrap">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3"><img src="{{ $item->image() }}"
                                                                class="rounded-2"></div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="text-heading mb-0">{{ $item->name() }}</h6>
                                                        <small></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span>{{ $item->price() }}</span></td>
                                            <td><span class="text-body">{{ $item->quantity }}</span></td>
                                            <td><span class="text-body">{{ $item->amount }}</span></td>
                                        </tr>
                                    @endforeach

                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>{{__('Total')}}</th>
                                    <th>{{ $order->cart->total() }}</th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title m-0">{{__('Shipping activity')}}</h5>
                        </div>
                        <div class="card-body pt-1">
                            <ul class="timeline pb-0 mb-0">
                              @php
                              $histories = $order->histories;
                              @endphp
                                @foreach ($histories as $key => $history)
                                    <li
                                        class="timeline-item timeline-item-transparent {{$history  === $histories->last() ? 'border-transparent pb-0': 'border-primary' }}">
                                        <span class="timeline-point timeline-point-primary"></span>
                                        <div class="timeline-event pb-0">
                                            <div
                                                class="timeline-header">
                                                <h6 class="mb-0">{{ __($history->status) }}</h6>
                                                <small class="text-muted">{{ $history->created_at() }}</small>
                                            </div>
                                            <p class="mt-3">{{ $history->message() }}</p>
                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header row justify-content-center">
                            <h5 class="card-title  col m-0">{{__('Seller details')}}</h5>
                            <i class="col-md-2 my-auto bx bxs-user"></i>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-start align-items-center mb-6">
                                <div class="avatar me-3">
                                    <img src="{{ $order->seller->image() }}" alt="Avatar" class="rounded-circle">
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="app-user-view-account.html" class="text-body text-nowrap">
                                        <h6 class="mb-0">{{ $order->seller->enterprise() }}</h6>
                                    </a>
                                    <span>{{ __($order->seller->role_is()) }}</span>
                                </div>
                            </div>
                            {{-- <div class="d-flex justify-content-start align-items-center mb-6">
                                <span
                                    class="avatar rounded-circle bg-label-success me-3 d-flex align-items-center justify-content-center"><i
                                        class='bx bx-cart bx-lg'></i></span>
                                <h6 class="text-nowrap mb-0">12 Orders</h6>
                            </div> --}}
                            <div class="d-flex justify-content-between">
                              <h6 class="mb-1">{{__('Contact info')}}</h6>
                              {{-- <h6 class="mb-1"><a href="{{ $user->location() }}"><i class="bx bx-map"></i></a>
                              </h6> --}}
                          </div>

                          <h6 class=" mb-1 text-fit"><i class="bx bxs-envelope"></i> {{ $order->seller->email }}</h6>
                          <h6 class=" mb-1 text-fit"><i class="bx bxs-phone"></i> {{ $order->seller->phone }}</h6>
                          <h6 class=" mb-0 text-fit"><i class="bx bxs-map"></i> <a href="{{ $order->seller->location() }}">{{ $order->seller->address() }}</a></h6>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header row justify-content-center">
                            <h5 class="card-title  col m-0">{{__('Buyer details')}}</h5>
                            <i class="col-md-2 my-auto bx bxs-user"></i>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-start align-items-center mb-6">
                                <div class="avatar me-3">
                                    <img src="{{ $order->buyer->image() }}" alt="Avatar" class="rounded-circle">
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="app-user-view-account.html" class="text-body text-nowrap">
                                        <h6 class="mb-0">{{ $order->buyer->enterprise() }}</h6>
                                    </a>
                                    <span>{{ __($order->buyer->role_is()) }}</span>
                                </div>
                            </div>
                            {{-- <div class="d-flex justify-content-start align-items-center mb-6">
                              <span
                                  class="avatar rounded-circle bg-label-success me-3 d-flex align-items-center justify-content-center"><i
                                      class='bx bx-cart bx-lg'></i></span>
                              <h6 class="text-nowrap mb-0">12 Orders</h6>
                          </div> --}}
                          <div class="d-flex justify-content-between">
                            <h6 class="mb-1">{{__('Contact info')}}</h6>
                            {{-- <h6 class="mb-1"><a href="{{ $user->location() }}"><i class="bx bx-map"></i></a>
                            </h6> --}}
                        </div>

                        <h6 class=" mb-1 text-fit"><i class="bx bxs-envelope"></i> {{ $order->buyer->email }}</h6>
                        <h6 class=" mb-1 text-fit"><i class="bx bxs-phone"></i> {{ $order->buyer->phone }}</h6>
                        <h6 class=" mb-0 text-fit"><i class="bx bxs-map"></i> <a href="{{ $order->buyer->location() }}">{{ $order->buyer->address() }}</a></h6>
                        </div>
                    </div>
                    @if ($order->delivery)
                        <div class="card mb-3">
                            <div class="card-header row justify-content-center">
                                <h5 class="card-title  col m-0">{{__('Driver details')}}</h5>
                                <i class="col-md-2 my-auto bx bxs-truck"></i>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-start align-items-center mb-6">
                                    <div class="avatar me-3">
                                        <img src="{{ $order->driver->image() }}" alt="Avatar" class="rounded-circle">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="app-user-view-account.html" class="text-body text-nowrap">
                                            <h6 class="mb-0">{{ $order->driver->enterprise() }}</h6>
                                        </a>
                                        <span>{{ __($order->driver->role_is()) }}</span>
                                    </div>
                                </div>
                                {{-- <div class="d-flex justify-content-start align-items-center mb-6">
                            <span
                                class="avatar rounded-circle bg-label-success me-3 d-flex align-items-center justify-content-center"><i
                                    class='bx bx-cart bx-lg'></i></span>
                            <h6 class="text-nowrap mb-0">12 Orders</h6>
                        </div> --}}
                        <div class="d-flex justify-content-between">
                          <h6 class="mb-1">{{__('Contact info')}}</h6>
                          {{-- <h6 class="mb-1"><a href="{{ $user->location() }}"><i class="bx bx-map"></i></a>
                          </h6> --}}
                      </div>

                      <h6 class=" mb-1 text-fit"><i class="bx bxs-envelope"></i> {{ $order->driver->email }}</h6>
                      <h6 class=" mb-1 text-fit"><i class="bx bxs-phone"></i> {{ $order->driver->phone }}</h6>
                      <h6 class=" mb-0 text-fit"><i class="bx bxs-map"></i> {{ $order->driver->address() }}</h6>
                            </div>
                        </div>
                    @else
                    @endif



                </div>
            </div>





        </div>
    </div>

@endsection
