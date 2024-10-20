@php
    $containerNav = $containerNav ?? 'container-fluid';
    $navbarDetached = $navbarDetached ?? '';
    $user = auth()->user();
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
        <div class="nav-item d-flex align-items-center">
            <i class="bx bx-search fs-4 lh-0"></i>
            <input type="text" class="form-control border-0 shadow-none" placeholder="Search..."
                aria-label="Search...">
        </div>
    </div>
    <!-- /Search -->
    <ul class="navbar-nav flex-row align-items-center ms-auto">

        {{--  <!-- Place this tag where you want the button to render. -->
          <li class="nav-item lh-1 me-3">
            <a class="github-button" href="https://github.com/themeselection/sneat-html-laravel-admin-template-free" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star themeselection/sneat-html-laravel-admin-template-free on GitHub">Star</a>
          </li> --}}
        @if (count(session()->get('cart') ?? []))
            <li class="nav-item">

                <button type="button" class="btn text-nowrap d-inline-block" data-bs-toggle="modal"
                    data-bs-target="#cartModal">
                    <span class="tf-icons bx bx-sm bx-cart"></span>
                    <span
                        class="badge rounded-pill bg-danger text-white badge-notifications">{{ count(session()->get('cart')) }}</span>
                </button>

            </li>
        @endif

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    {{-- <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                    @include('_partials.macros', ['width' => 25, 'withbg' => '#696cff']) --}}
                    <img class="w-px-40 h-px-40 rounded-circle" src="{{ $user->image() }}">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    {{-- <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                                @include('_partials.macros', ['width' => 25, 'withbg' => '#696cff']) --}}
                                    <img class="w-px-40 h-px-40 rounded-circle" src="{{ $user->image() }}">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block">{{ $user->enterprise() }}</span>
                                <small class="text-muted">{{ __($user->role_is()) }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                {{-- <li>
                <a class="dropdown-item" href="javascript:void(0);">
                  <i class="bx bx-user me-2"></i>
                  <span class="align-middle">My Profile</span>
                </a>
              </li> --}}

                <li>
                    <a class="dropdown-item" href="{{ url('/lang') }}">
                        <i class='bx bx-refresh me-2'></i>
                        <span class="align-middle">{{ __('Switch language') }}</span>
                    </a>
                </li>
                @if ($user->role_is('admin'))
                    <li>
                        <a class="dropdown-item" href="{{ url('/version') }}">
                            <i class='bx bx-cog me-2'></i>
                            <span class="align-middle">{{ __('Settings') }}</span>
                        </a>
                    </li>
                @endif

                {{-- <li>
                <a class="dropdown-item" href="javascript:void(0);">
                  <span class="d-flex align-items-center align-middle">
                    <i class="flex-shrink-0 bx bx-credit-card me-2 pe-1"></i>
                    <span class="flex-grow-1 align-middle">Billing</span>
                    <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                  </span>
                </a>
              </li> --}}
                <li>
                    <div class="dropdown-divider"></div>
                </li>

                <li>
                    <a class="dropdown-item" href="#" id="change_password">
                        <i class='bx bx-key me-2'></i>
                        <span class="align-middle">{{ __('Change password') }}</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('/auth/logout') }}">
                        <i class='bx bx-power-off me-2'></i>
                        <span class="align-middle">{{ __('Log Out') }}</span>
                    </a>
                </li>
            </ul>
        </li>
        <!--/ User -->
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
                <h4 class="fw-bold py-1 mb-1">{{ __('Change password') }}</h4>
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

                <div class="mb-4">
                    <button type="submit" id="submit_password" name="submit_password" class="btn btn-primary"
                        style="margin-left: 40%">{{ __('Change') }}</button>
                </div>

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
                                <th>Product</th>
                                <th>Owner</th>
                                <th>Quantity</th>
                                <th>Price</th>
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
                <button type="button" class="btn btn-label-danger" id="empty_cart">Empty Cart</button>
                <button type="button" class="btn btn-label-primary" id="finish_order">Finish Order</button>

                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>
