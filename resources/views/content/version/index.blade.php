@extends('layouts/contentNavbarLayout')

@section('title', __('Version'))

@section('vendor-script')
    <script src="{{ asset('assets/vendor/js/quill.js') }}"></script>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/quill.css') }}" />
@endsection

@section('content')

    <h4 class="fw-bold py-3 mb-3">
        <span class="text-muted fw-light"></span> {{ __('Version') }}
    </h4>

    <form class="form-horizontal" onsubmit="event.preventDefault()" action="#" enctype="multipart/form-data"
        id="version_form">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Android') }}</h5>
                        <small class="text-muted float-end">{{ __('Android version') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="android_version_number">{{ __('Version number') }}</label>
                            <input type="text" class="form-control" id="android_version_number"
                                name="android_version_number" value="{{ $android->version_number }}" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="android_build_number">{{ __('Build number') }}</label>
                            <input type="text" class="form-control" id="android_build_number" name="android_build_number"
                                value="{{ $android->build_number }}" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="android_priority">{{ __('Priority') }}</label>
                            <select class="form-select" id="android_priority" name="android_priority">
                                <option value="0" @if ($android->priority == 0) selected @endif>
                                    {{ __('Optional') }}</option>
                                <option value="1" @if ($android->priority == 1) selected @endif>
                                    {{ __('Required') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="android_link">{{ __('Link') }}</label>
                            <textarea class="form-control" id="android_link" name="android_link"> {{ $android->link }}</textarea>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('iOS') }}</h5>
                        <small class="text-muted float-end">{{ __('iOS version') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="ios_version_number">{{ __('Version number') }}</label>
                            <input type="text" class="form-control" id="ios_version_number" name="ios_version_number"
                                value="{{ $ios->version_number }}" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ios_build_number">{{ __('Build number') }}</label>
                            <input type="text" class="form-control" id="ios_build_number" name="ios_build_number"
                                value="{{ $ios->build_number }}" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ios_priority">{{ __('Priority') }}</label>
                            <select class="form-select" id="ios_priority" name="ios_priority">
                                <option value="0" @if ($ios->priority == 0) selected @endif>
                                    {{ __('Optional') }}</option>
                                <option value="1" @if ($ios->priority == 1) selected @endif>
                                    {{ __('Required') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ios_link">{{ __('Link') }}</label>
                            <textarea class="form-control" id="ios_link" name="ios_link">{{ $ios->link }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3" style="text-align: center">
            <button type="submit" id="submit" name="submit" class="btn btn-primary">{{ __('Send') }}</button>
        </div>
    </form>

    <h4 class="fw-bold py-3 mb-3">
        <span class="text-muted fw-light"></span> {{ __('Privacy policy') }}
    </h4>

    <div class="row">
        <!-- Snow Theme -->
        <div id="privacy_policy" hidden>{!! $privacy_policy !!}</div>
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <div id="snow-editor">
                    </div>
                    <div class="mt-3" style="text-align: center">
                        <button id="submit_privacy" class="btn btn-primary">{{ __('Send') }}</button>
                    </div>
                </div>
            </div>
        </div>



    @endsection

    @section('page-script')
        <script>
            $(document).ready(function() {
                const quill = new Quill("#snow-editor", {
                    theme: "snow",
                });



                quill.clipboard.dangerouslyPasteHTML($('#privacy_policy').html());

                $('#submit').on('click', function() {
                    var queryString = new FormData($("#version_form")[0]);
                    //console.log(queryString);
                    $.ajax({
                        url: '{{ url('version/update') }}',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: queryString,
                        dataType: 'JSON',
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status == 1) {
                                Swal.fire(
                                    "{{ __('Success') }}",
                                    "{{ __('success') }}",
                                    'success'
                                ).then((result) => {
                                    location.reload();
                                });
                            } else {
                                console.log(response.message);
                                Swal.fire(
                                    "{{ __('Error') }}",
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                            Swal.fire(
                                "{{ __('Error') }}",
                                errors.message,
                                'error'
                            );
                            // Render the errors with js ...
                        }


                    });

                });

                $('#submit_privacy').on('click', function() {

                    $.ajax({
                        url: '{{ url('documentation/update') }}',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: {
                            name: 'privacy_policy',
                            content_en: quill.getSemanticHTML()
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            if (response.status == 1) {
                                Swal.fire(
                                    "{{ __('Success') }}",
                                    "{{ __('success') }}",
                                    'success'
                                ).then((result) => {
                                    location.reload();
                                });
                            } else {
                                console.log(response.message);
                                Swal.fire(
                                    "{{ __('Error') }}",
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                            Swal.fire(
                                "{{ __('Error') }}",
                                errors.message,
                                'error'
                            );
                            // Render the errors with js ...
                        }


                    });

                });
            });
        </script>
    @endsection
