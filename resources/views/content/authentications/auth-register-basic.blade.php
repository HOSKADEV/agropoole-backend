@extends('layouts/blankLayout')

@section('title', 'Register Basic - Pages')

@section('page-style')
    <!-- Page -->
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/register.css') }}">
    <style>
      #map {
          height: 100%;
          max-height: 460px;
          width: 100%;
          border-radius: 0.375rem;
      }
      .form-section {
          display: flex;
          gap: 2rem;
          padding-top: 2rem;
      }
      .form-fields {
          flex: 1;
      }
      .map-section {
          flex: 1;
          position: relative;
      }
      @media (max-width: 992px) {
          .form-section {
              flex-direction: column;
          }
          #map {
              min-height: 350px;
          }
      }
      .hidden-input {
          display: none;
      }
  </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/cleave.js') }}"></script>
    <script src="{{ asset('assets/js/cleave-phone.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/wizard-ex-property-listing.js') }}"></script>
@endsection


@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="card">
                <div class="card-header">
                    <div class="app-brand justify-content-center">
                        <img src="{{ url('agropole.png') }}" class="d-block rounded" height="60" width="180" />
                    </div>
                </div>
                <form class="form-horizontal" method="POST" action="{{ url('/auth/register-action') }}"
                    id="formAuthentication">
                    @csrf
                    <div class="card-body">
                        <div class="row g-6">
                            <div class="col-12">
                                <div class="row g-6">
                                    <div class="col-md mb-md-0">
                                        <div
                                            class="form-check custom-option custom-option-icon {{ old('role', '1') == '1' ? 'checked' : '' }}">
                                            <label class="form-check-label custom-option-content" for="customRadioProvider">
                                                <span class="custom-option-body">
                                                    <i class="bx bxs-factory"></i>
                                                    <span class="custom-option-title">{{ __('I am a Provider') }}</span>
                                                    <small>{{ __('Create new products and sell to brokers') }}</small>
                                                </span>
                                                <input name="role" class="form-check-input" type="radio" value="1"
                                                    id="customRadioProvider"
                                                    {{ old('role', '1') == '1' ? 'checked' : '' }}>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md mb-md-0">
                                        <div
                                            class="form-check custom-option custom-option-icon {{ old('role') == '2' ? 'checked' : '' }}">
                                            <label class="form-check-label custom-option-content" for="customRadioBroker">
                                                <span class="custom-option-body">
                                                    <i class="bx bxs-buildings"></i>
                                                    <span class="custom-option-title">{{ __('I am a Broker') }}</span>
                                                    <small>{{ __('Buy from providers, sell to stores in bulk') }}</small>
                                                </span>
                                                <input name="role" class="form-check-input" type="radio" value="2"
                                                    id="customRadioBroker" {{ old('role') == '2' ? 'checked' : '' }}>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md mb-md-0">
                                        <div
                                            class="form-check custom-option custom-option-icon {{ old('role') == '3' ? 'checked' : '' }}">
                                            <label class="form-check-label custom-option-content" for="customRadioStore">
                                                <span class="custom-option-body">
                                                    <i class="bx bxs-store"></i>
                                                    <span class="custom-option-title">{{ __('I am a Store') }}</span>
                                                    <small>{{ __('Buy from brokers, sell to clients in normal quantities') }}</small>
                                                </span>
                                                <input name="role" class="form-check-input" type="radio" value="3"
                                                    id="customRadioStore" {{ old('role') == '3' ? 'checked' : '' }}>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md mb-md-0">
                                        <div
                                            class="form-check custom-option custom-option-icon {{ old('role') == '5' ? 'checked' : '' }}">
                                            <label class="form-check-label custom-option-content" for="customRadioDriver">
                                                <span class="custom-option-body">
                                                    <i class="bx bxs-truck"></i>
                                                    <span class="custom-option-title">{{ __('I am a Driver') }}</span>
                                                    <small>{{ __('Deliver orders between different roles in the supply chain') }}</small>
                                                </span>
                                                <input name="role" class="form-check-input" type="radio" value="5"
                                                    id="customRadioDriver" {{ old('role') == '5' ? 'checked' : '' }}>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <!-- Left side - Form fields -->
                            <div class="form-fields">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="name">{{ __('Name') }}</label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            placeholder="John Doe" value="{{ old('name') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="phone">{{ __('Phone') }}</label>
                                        <input type="text" id="phone" name="phone"
                                            class="form-control contact-number-mask" placeholder="06 12 34 57 89"
                                            value="{{ old('phone') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="email">{{ __('Email') }}</label>
                                        <input type="text" id="email" name="email" class="form-control"
                                            placeholder="{{ __('john.doe@example.com') }}" value="{{ old('email') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="state">{{ __('State') }}</label>
                                        <select class="form-select" id="state" name="state">
                                            <option value="">{{ __('Not selected') }}</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}"
                                                    {{ old('state') == $state->id ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="city_id">{{ __('City') }}</label>
                                        <select class="form-select" id="city_id" name="city_id">
                                            <option value="">{{ __('Not selected') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="password">{{ __('Password') }}</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" class="form-control" name="password"
                                                placeholder="············" aria-describedby="passwordToggler">
                                            <span id="passwordToggler" class="input-group-text cursor-pointer">
                                                <i class="bx bx-hide"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right side - Map -->
                            <div class="map-section">
                                <label class="form-label">{{ __('Location on Map') }}</label>
                                <div id="map"></div>
                                <!-- Hidden coordinate inputs -->
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                            </div>
                        </div>

                    </div>
                    <div class="card-footer row justify-content-between">
                        <div class="col-md-auto text-center">
                            <a href="{{ url('auth/login-basic') }}"
                                class="d-flex align-items-center justify-content-center">
                                <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                                {{ __('Back to login') }}
                            </a>
                        </div>
                        <div class="col-md-auto text-center">
                            <button type="submit" class="btn btn-primary mx-auto">
                                {{ __('Create an account') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // Initialize the map centered on Algeria
            var map = L.map('map').setView([33.7538, 4.0588], 6); // Centered on Algiers with zoom level 6
            var marker = null;

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add marker on click
            map.on('click', function(e) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker(e.latlng).addTo(map);

                // Update hidden inputs
                $('#latitude').val(e.latlng.lat.toFixed(6));
                $('#longitude').val(e.latlng.lng.toFixed(6));
            });

            // Set initial marker if coordinates exist
            @if (old('latitude') && old('longitude'))
                marker = L.marker([{{ old('latitude') }}, {{ old('longitude') }}]).addTo(map);
                map.setView([{{ old('latitude') }}, {{ old('longitude') }}], 15);
            @endif

            // City population code
            function populateCities(state_id, selectedCityId = null) {
                $.ajax({
                    url: '{{ url('api/v1/city/get?all=1') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: {
                        state_id: state_id
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status == 1) {
                            var cities = document.getElementById('city_id');
                            cities.innerHTML = '<option value="">{{ __('Not selected') }}</option>';
                            for (var i = 0; i < response.data.length; i++) {
                                var option = document.createElement('option');
                                option.value = response.data[i].id;
                                option.innerHTML = response.data[i].name;
                                if (selectedCityId && response.data[i].id == selectedCityId) {
                                    option.selected = true;
                                }
                                cities.appendChild(option);
                            }
                        }
                    }
                });
            }

            $('#state').on('change', function() {
                var state_id = $(this).val();
                populateCities(state_id);
            });

            @if (old('state'))
                populateCities({{ old('state') }}, {{ old('city_id') ?? 'null' }});
            @endif
        });
    </script>
@endsection
