<!-- BEGIN: Vendor JS-->
<script src="{{ asset(mix('assets/vendor/libs/jquery/jquery.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/popper/popper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/bootstrap.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>
@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.1/sorting/datetime-moment.js"></script>

{{-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->

<script>
    $('#change_password').on('click', function() {
        $("#change_password_modal").modal("show");
    });

    $('#update_profil').on('click', function() {
        $("#update_profil_modal").modal("show");
    });

    $('#update_profil_state').on('change', function() {

        var state_id = $(this).val();

        $.ajax({
            url: '{{ url('city/get?all=1') }}',
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

                    var cities = document.getElementById('update_profil_city');
                    cities.innerHTML =
                        '<option value="">{{ __('Not selected') }}</option>';

                    for (var i = 0; i < response.data.length; i++) {
                        var option = document.createElement('option');
                        option.value = response.data[i].id;
                        option.innerHTML = response.data[i].name;
                        cities.appendChild(option);
                    }

                }
            }
        });
    });

    $('#submit_update_profil').on('click', function() {
        Swal.fire({
            title: "{{ __('Wait a moment') }}",
            icon: 'info',
            html: '<div style="height:50px;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden"></div></div>',
            showCloseButton: false,
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
        });

        var queryString = new FormData($("#update_profil_form")[0]);



        $("#update_profil_modal").modal("hide");


        $.ajax({
            url: "{{ url('user/update') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: queryString,
            dataType: 'JSON',
            contentType: false,
            processData: false,
            success: function(response) {
                Swal.close();
                if (response.status == 1) {
                    Swal.fire({
                        title: "{{ __('Success') }}",
                        text: "{{ __('success') }}",
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        location.reload();
                    });
                } else {
                    //console.log(response.message);
                    Swal.fire(
                        "{{ __('Error') }}",
                        response.message,
                        'error'
                    );
                }
            },
            error: function(data) {
                Swal.close();
                //var errors = data.responseJSON;
                //console.log(errors);
                Swal.fire(
                    "{{ __('Error') }}",
                    errors.message,
                    'error'
                );
                // Render the errors with js ...
            }
        });
    });

    $('#submit_password').on('click', function() {

        var old_password = $('#old_password').val();
        var new_password = $('#new_password').val();
        var new_password_confirmation = $('#new_password_confirmation').val();

        $.ajax({
            url: '{{ url('user/change_password') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: {
                old_password: old_password,
                new_password: new_password,
                new_password_confirmation: new_password_confirmation,
            },
            dataType: 'JSON',
            //contentType: false,
            //processData: false,
            success: function(response) {
                if (response.status == 1) {
                    $("#change_password_modal").modal("hide");
                    Swal.fire(
                        "{{ __('Success') }}",
                        "{{ __('success') }}",
                        'success'
                    );

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

    $('#empty_cart').on('click', function() {
        Swal.fire({
            title: "{{ __('Warning') }}",
            text: "{{ __('Are you sure?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes') }}",
            cancelButtonText: "{{ __('No') }}"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: "{{ url('cart/empty') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET',
                    success: function(response) {
                        if (response.status == 1) {

                            Swal.fire(
                                "{{ __('Success') }}",
                                "{{ __('success') }}",
                                'success'
                            ).then((result) => {
                                location.reload();
                            });
                        }
                    }
                });


            }
        })
    });

    $('#finish_order').on('click', function() {

        var queryString = new FormData($("#finish_order_form")[0]);



        $("#cartModal").modal("hide");


        $.ajax({
            url: "{{ url('order/create') }}",
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
                    Swal.fire({
                        title: "{{ __('Success') }}",
                        text: "{{ __('success') }}",
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
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

    $(document.body).on('change', '#avatar', function() {
        const fileInput = document.querySelector('#avatar');
        if (fileInput.files[0]) {
            $('#uploaded-avatar').attr('src', window.URL.createObjectURL(fileInput.files[0]));
        }
    });

    $(document.body).on('click', '#avatar-reset', function() {
        const fileInput = document.querySelector('#avatar');
        fileInput.value = '';
        $('#uploaded-avatar').attr('src', $('#old-avatar').attr('src'));
    });
</script>
