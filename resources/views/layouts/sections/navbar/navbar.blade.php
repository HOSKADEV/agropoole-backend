@php
    $containerNav = $containerNav ?? 'container-fluid';
    $navbarDetached = $navbarDetached ?? '';
    $auth_user = auth()->user();
    $auth_user_state = $auth_user->city->state_id;
    $states = DB::table('states')->get();
    $cities = DB::table('cities')->where('state_id', $auth_user_state)->get();
@endphp

<!-- Navbar -->
@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
    <nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme"
        id="layout-navbar">
@endif
@if (isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="{{ $containerNav }}">
@endif

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                @include('_partials.macros', ['width' => 25, 'withbg' => '#696cff'])
            </span>
            <span class="app-brand-text demo menu-text fw-bolder">{{ config('variables.templateName') }}</span>
        </a>
    </div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
    <div
        class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
        <div class="d-flex">
            <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online" id="update_profil">
                    {{-- <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
            @include('_partials.macros', ['width' => 25, 'withbg' => '#696cff']) --}}
                    <img class="w-px-40 h-px-40 rounded-circle" src="{{ $auth_user->image() }}">
                </div>
            </div>
            <div class="flex-grow-1">
                <span class="fw-semibold d-block">{{ $auth_user->enterprise() }}</span>
                <small class="text-muted">{{ __($auth_user->role_is()) }}</small>
            </div>
        </div>
    </div>
    <ul class="navbar-nav flex-row align-items-center ms-auto">
        @if (count(session()->get('cart') ?? []))
            <li class="nav-item w-px-50">
                <a class="nav-link d-flex justify-content-center align-items-center" data-bs-toggle="modal"
                    href="#" data-bs-target="#cartModal">
                    <span class="tf-icons bx bx-sm bx-cart"></span>
                    <span
                        class="badge rounded-pill bg-danger text-white badge-notifications">{{ count(session()->get('cart')) }}</span>
                </a>
            </li>
        @endif

        @if ($auth_user->role_is('admin'))
            <li class="nav-item w-px-50">
                <a class="nav-link d-flex justify-content-center align-items-center" href="{{ url('/version') }}">
                    <i class="bx bx-cog bx-sm"></i>
                </a>
            </li>
        @endif

        <li class="nav-item w-px-50">
            <a class="nav-link d-flex justify-content-center align-items-center" href="#" id="change_password">
                <i class='bx bx-key bx-sm'></i>
            </a>
        </li>
        <li class="nav-item w-px-50">
            <a class="nav-link d-flex justify-content-center align-items-center" href="{{ url('/auth/logout') }}">
                <i class='bx bx-power-off bx-sm'></i>
            </a>
        </li>
    </ul>
</div>

@if (!isset($navbarDetached))
    </div>
@endif
</nav>
<!-- / Navbar -->

{{-- change password modal --}}
<div class="modal fade" id="change_password_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <h4 class="fw-bold py-1 mb-1">{{ __('Change password') }}</h4> --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label for="old_password" class="form-label">{{ __('Old password') }}</label>
                    <input type="password" class="form-control" id="old_password" name="old_password">
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">{{ __('New password') }}</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                </div>

                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">{{ __('Confirm new password') }}</label>
                    <input type="password" class="form-control" id="new_password_confirmation"
                        name="new_password_confirmation">
                </div>

                <br>

                <div class="mb-4 text-center">
                    <button type="submit" id="submit_password" name="submit_password"
                        class="btn btn-primary">{{ __('Change') }}</button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update_profil_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <h4 class="fw-bold py-1 mb-1">{{ __('Change password') }}</h4> --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" onsubmit="event.preventDefault()" action="#"
                    enctype="multipart/form-data" id="update_profil_form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-body h-50">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    {{-- <div hidden><img src="{{ $auth_user->image() }}" alt="image"
                                            class="d-block rounded" height="100" width="100" id="old-avatar" />
                                    </div> --}}
                                    <img src="{{ $auth_user->image() }}" alt="image" class="d-block rounded"
                                        height="100" width="100" id="uploaded-avatar" />
                                    <div class="button-wrapper">
                                        <label for="avatar" class="btn btn-primary mb-3" tabindex="0">
                                            <span class="d-none d-sm-block">{{ __('Upload new image') }}</span>
                                            <i class="bx bx-upload d-block d-sm-none"></i>
                                            <input type="file" id="avatar" name="image" hidden
                                                accept="image/png, image/jpeg" />
                                        </label>
                                        <button type="button" class="btn btn-outline-secondary" id="avatar-reset">
                                            <i class="bx bx-reset d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">{{ __('Reset') }}</span>
                                        </button>
                                        <br>
                                        {{-- <small class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</small> --}}
                                    </div>
                                </div>
                            </div>
                            {{-- <hr class="my-0"> --}}

                            <div class="mb-3">
                                <label class="form-label" for="name">{{ __('Name') }}</label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ $auth_user->name }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="enterprise_name">{{ __('Enterprise') }}</label>
                                <input type="text" class="form-control" name="enterprise_name"
                                    value="{{ $auth_user->enterprise_name }}">
                            </div>
                        </div>
                        <div class="col-md-6">


                            <div class="mb-3">
                                <label class="form-label" for="phone">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" name="phone"
                                    value="{{ $auth_user->phone }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="email">{{ __('Email') }}</label>
                                <input type="text" class="form-control" name="email"
                                    value="{{ $auth_user->email }}">
                            </div>


                            <div class="mb-3">
                                <label class="form-label" for="state">{{ __('State') }}</label>
                                <select class="form-select" id="update_profil_state">
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ $state->id == $auth_user_state ? 'selected' : '' }}>{{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="city">{{ __('City') }}</label>
                                <select class="form-select" id="update_profil_city" name="city">
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ $city->id == $auth_user->city_id ? 'selected' : '' }}>{{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="mb-4 text-center">
                <button type="submit" id="submit_update_profil"
                    class="btn btn-primary">{{ __('Send') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="finish_order_form" class="form-horizontal" onsubmit="event.preventDefault()"
                    action="#" enctype="multipart/form-data">
                    <input type="hidden" name="phone" value="{{ auth()->user()->phone }}" />
                    <input type="hidden" name="longitude" value="{{ auth()->user()->longitude }}" />
                    <input type="hidden" name="latitude" value="{{ auth()->user()->latitude }}" />
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Seller') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count(session()->get('cart') ?? []))
                                @foreach (session('cart') as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item['product_image'] }}"
                                                    alt="{{ $item['product_name'] }}" width="50" height="50"
                                                    class="card-img-avatar me-2">
                                                <span>{{ $item['product_name'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item['owner_image'] }}"
                                                    alt="{{ $item['owner_name'] }}" width="50" height="50"
                                                    class="rounded-circle me-2">
                                                <span>{{ $item['owner_name'] }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ $item['stock_price'] ? $item['stock_price'] : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-danger" id="empty_cart">{{ __('Empty Cart') }}</button>
                <button type="button" class="btn btn-label-primary"
                    id="finish_order">{{ __('Place Order') }}</button>

                <button type="button" class="btn btn-label-secondary"
                    data-bs-dismiss="modal">{{ __('Close') }}</button>

            </div>
        </div>
    </div>
</div>
