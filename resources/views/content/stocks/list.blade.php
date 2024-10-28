@extends('layouts/contentNavbarLayout')

@section('title', __('My stock'))

@section('content')

    <h4 class="fw-bold py-3 mb-3">{{ __('My stock') }}
        {{-- <span class="text-muted fw-light">{{ __('Stocks') }} /</span> {{ __('My stock') }} --}}
        <button type="button" class="btn btn-primary" id="multi_create" style="float:right"><span
                class="tf-icons bx bx-infinite bx-18px me-2"></span>{{ __('Global add') }}</button>
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="row  justify-content-between">
            <div class="form-group col mx-3 my-3">
                <label for="category" class="form-label">{{ __('Category filter') }}</label>
                <select class="form-select" id="category" name="category">
                    <option value=""> {{ __('Not selected') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"> {{ $category->name }} </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col mx-3 my-3">
                <label for="subcategory" class="form-label">{{ __('Subcategory filter') }}</label>
                <select class="form-select" id="subcategory" name="subcategory">
                    <option value=""> {{ __('Not selected') }}</option>
                </select>
            </div>

            <div class="form-group col mx-3 my-3">
                <label for="type" class="form-label">{{ __('Quantity status filter') }}</label>
                <select class="form-select" id="sufficiency" name="sufficiency">
                    <option value=""> {{ __('Not selected') }}</option>
                    <option value="1"> {{ __('Sufficient') }}</option>
                    <option value="2"> {{ __('Insufficient') }}</option>
                </select>
            </div>

            <div class="form-group col mx-3 my-3">
                <label for="type" class="form-label">{{ __('Availability filter') }}</label>
                <select class="form-select" id="availability" name="availability">
                    <option value=""> {{ __('Not selected') }}</option>
                    <option value="1"> {{ __('Available') }}</option>
                    <option value="2"> {{ __('Unavailable') }}</option>
                </select>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table" id="laravel_datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Image') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('is_available') }}</th>
                        <th>{{ __('Created at') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- stock modal --}}
    <div class="modal fade" id="modal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                   {{--  <h4 class="fw-bold py-1 mb-1">{{ __('Edit stock') }}</h4> --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" onsubmit="event.preventDefault()" action="#"
                        enctype="multipart/form-data" id="form">

                        <input type="text" class="form-control" id="stock_id" name="stock_id" hidden />

                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('Price') }}</label>
                            <input type="number" class="form-control" id="price" name="price" />
                        </div>

                        <div class="row  justify-content-between">

                            <div class="form-group col-md-6 p-3">
                                <label class="form-label" for="quantity">{{ __('Quantity') }}</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" />
                            </div>

                            <div class="form-group col-md-6 p-3">
                                <label class="form-label" for="min_quantity">{{ __('Min quantity') }}</label>
                                <input type="number" class="form-control" id="min_quantity" name="min_quantity" />
                            </div>

                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="show_price">{{ __('Show price') }}</label>
                            <select class="form-select" name="show_price" id="show_price">
                                <option value="1"> {{ __('Yes') }}</option>
                                <option value="0"> {{ __('No') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('Status') }}</label>
                            <select class="form-select" name="status" id="status">
                                <option value="available"> {{ __('Available') }}</option>
                                <option value="unavailable"> {{ __('Unavailable') }}</option>
                            </select>
                        </div>


                        <div class="mb-3" style="text-align: center">
                            <button type="submit" id="submit" name="submit"
                                class="btn btn-primary">{{ __('Send') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            load_data();

            function load_data(category = null, subcategory = null, sufficiency = null, availability = null) {
                //$.fn.dataTable.moment( 'YYYY-M-D' );
                var table = $('#laravel_datatable').DataTable({

                    responsive: true,
                    processing: true,
                    serverSide: true,
                    pageLength: 10,
                    columnDefs: [{
                        searchable: false,
                        targets: 1
                    }],

                    ajax: {
                        url: "{{ url('stock/list') }}",
                        data: {
                            category: category,
                            subcategory: subcategory,
                            sufficiency: sufficiency,
                            availability: availability
                        },
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    },

                    columns: [

                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        /* {
                            data: 'name_image',
                            name: 'name_image',
                            render: function(data) {

                                return '<div class="d-flex justify-content-start align-items-center stock-name"><div class="avatar-wrapper"><div class="avatar avatar me-4 rounded-2 bg-label-secondary"><img src="' +
                                    data[0] +
                                    '" class="rounded"></div></div><div class="d-flex flex-column"><h6 class="text-nowrap mb-0">' +
                                    data[1] + '</h6></div></div>';

                            }
                        }, */
                        {
                            data: 'image',
                            name: 'image',
                            render: function(data) {

                                return '<div class="avatar avatar me-4 rounded-2 bg-label-secondary"><img src="' +
                                    data + '" class="rounded">';

                            }
                        },

                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'price',
                            name: 'price'
                        },

                        {
                            data: 'quantity',
                            name: 'quantity',
                            render: function(data) {

                                if (data[0] <= data[1]) {
                                    return '<span class="badge bg-label-danger">' + data[0] +
                                        '</span>';
                                } else {
                                    return '<span class="badge bg-label-success">' + data[0] +
                                        '</span>';
                                }

                            }
                        },

                        {
                            data: 'availability',
                            name: 'availability',
                            render: function(data) {
                                if (data == false) {
                                    return '<span class="badge bg-label-danger">{{ __('No') }}</span>';
                                } else {
                                    return '<span class="badge bg-label-success">{{ __('Yes') }}</span>';
                                }
                            }
                        },

                        {
                            data: 'created_at',
                            name: 'created_at'
                        },

                        /* {
                            data: 'discount',
                            name: 'discount'
                        }, */

                        {
                            data: 'action',
                            name: 'action',
                            render: function(data) {
                                /* return '<div class="dropdown"><button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button><div class="dropdown-menu">'
                                  +data+'</div></div>' */
                                return '<span>' + data + '</span>';
                            }
                        }

                    ]
                });
            }

            function refresh_table() {
                var category = document.getElementById('category').value;
                var subcategory = document.getElementById('subcategory').value;
                var sufficiency = document.getElementById('sufficiency').value;
                var availability = document.getElementById('availability').value;

                var table = $('#laravel_datatable').DataTable();
                table.destroy();
                load_data(category, subcategory, sufficiency, availability);
            }

            $('#category').on('change', function() {

                var category_id = document.getElementById('category').value;

                $.ajax({
                    url: '{{ url('subcategory/get?all=1') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: {
                        category_id: category_id
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status == 1) {

                            var subcategories = document.getElementById('subcategory');
                            subcategories.innerHTML =
                                '<option value="">{{ __('Not selected') }}</option>';
                            console.log(response.data);
                            for (var i = 0; i < response.data.length; i++) {
                                var option = document.createElement('option');
                                option.value = response.data[i].id;
                                option.innerHTML = response.data[i].name;
                                subcategories.appendChild(option);
                            }

                        }
                    }
                });


                refresh_table();
            });

            $('#subcategory').on('change', function() {

                refresh_table();

            });

            $('#sufficiency').on('change', function() {

                refresh_table();

            });

            $('#availability').on('change', function() {

                refresh_table();

            });

            /* $('#create').on('click', function() {
                document.getElementById('form').reset();
                document.getElementById('form_type').value = "create";
                document.getElementById('uploaded-image').src =
                    "{{ asset('assets/img/icons/file-not-found.jpg') }}";
                document.getElementById('old-image').src =
                    "{{ asset('assets/img/icons/file-not-found.jpg') }}";
                $("#modal").modal('show');
            }); */


            $(document.body).on('click', '.update', function() {
                document.getElementById('form').reset();
                var stock_id = $(this).attr('table_id');
                $("#stock_id").val(stock_id);

                $.ajax({
                    url: '{{ url('stock/update') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: {
                        stock_id: stock_id
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status == 1) {

                            document.getElementById('price').value = response.data.price;
                            document.getElementById('quantity').value = response.data.quantity;
                            document.getElementById('min_quantity').value = response.data
                                .min_quantity;
                            document.getElementById('show_price').value = response.data
                                .show_price;
                            document.getElementById('status').value = response.data.status;

                            $("#modal").modal("show");
                        }
                    }
                });
            });


            $('#submit').on('click', function() {

                /* var formdata = new FormData($("#form")[0]); */
                var queryString = new FormData($("#form")[0]);

                $("#modal").modal("hide");


                $.ajax({
                    url: "{{ url('stock/update') }}",
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

            $(document.body).on('click', '.delete', function() {

                var stock_id = $(this).attr('table_id');

                Swal.fire({
                    title: "{{ __('Warning') }}",
                    text: "{{ __('Are you sure?') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('Delete') }}",
                    cancelButtonText: "{{ __('Cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: "{{ url('stock/delete') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            data: {
                                stock_id: stock_id
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
                                }
                            }
                        });


                    }
                })
            });

            $('#multi_create').on('click', function() {


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
                            url: "{{ url('stock/create/multi') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
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
                                }
                            }
                        });


                    }
                })
            });

            $(document.body).on('change', '.image-input', function() {
                const fileInput = document.querySelector('.image-input');
                if (fileInput.files[0]) {
                    document.getElementById('uploaded-image').src = window.URL.createObjectURL(fileInput
                        .files[0]);
                }
            });
            $(document.body).on('click', '.image-reset', function() {
                const fileInput = document.querySelector('.image-input');
                fileInput.value = '';
                document.getElementById('uploaded-image').src = document.getElementById('old-image').src;
            });
        });
    </script>
@endsection
