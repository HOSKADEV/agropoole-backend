@extends('layouts/contentNavbarLayout')

@section('title', __('Products'))

@section('content')

    <h4 class="fw-bold py-3 mb-3">{{ __('Products') }}
        {{-- <span class="text-muted fw-light">{{ __('Products') }} /</span> {{ __('Browse products') }} --}}
        @if (auth()->user()->role_is('provider'))
            <button type="button" class="btn btn-primary" id="create" style="float:right">{{ __('Add Product') }}</button>
        @endif
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
            @if (!auth()->user()->role_is('provider'))
                {{-- @if (auth()->user()->role_is('provider'))
                <div class="form-group col mx-3 my-3">
                    <label for="type" class="form-label">{{ __('Availability filter') }}</label>
                    <select class="form-select" id="availability" name="availability">
                        <option value=""> {{ __('Not selected') }}</option>
                        <option value="1"> {{ __('Available') }}</option>
                        <option value="2"> {{ __('Unavailable') }}</option>
                    </select>
                </div>
            @else --}}
                <div class="form-group col mx-3 my-3">
                    <label for="category" class="form-label">{{ __('Provider filter') }}</label>
                    <select class="form-select" id="provider" name="provider">
                        <option value=""> {{ __('Not selected') }}</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}"> {{ $provider->enterprise() }} </option>
                        @endforeach
                    </select>
                </div>

            @endif

            <div class="form-group col mx-3 my-3">
                <label for="type" class="form-label">{{ __('Stock filter') }}</label>
                <select class="form-select" id="stock" name="stock">
                    <option value=""> {{ __('Not selected') }}</option>
                    <option value="1"> {{ __('Stocked') }}</option>
                    <option value="2"> {{ __('Not stocked') }}</option>
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
                        <th>{{ __('Created at') }}</th>
                        <th>{{ __('Pack units') }}</th>
                        {{-- <th>{{ __('is_available') }}</th> --}}
                        <th>{{ __('in_stock') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- product modal --}}
    <div class="modal fade" id="modal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h4 class="fw-bold py-1 mb-1">{{ __('Add product') }}</h4> --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="form_type" hidden />
                    <input type="text" class="form-control" id="id" name="id" hidden />
                    <form class="form-horizontal" onsubmit="event.preventDefault()" action="#"
                        enctype="multipart/form-data" id="form">

                        <div class="card-body">
                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                <div hidden><img src="{{ asset('assets/img/icons/file-not-found.jpg') }}" alt="image"
                                        class="d-block rounded" height="100" width="100" id="old-image" /> </div>
                                <img src="{{ asset('assets/img/icons/file-not-found.jpg') }}" alt="image"
                                    class="d-block rounded" height="100" width="100" id="uploaded-image" />
                                <div class="button-wrapper">
                                    <label for="image" class="btn btn-primary" tabindex="0">
                                        <span class="d-none d-sm-block">{{ __('Upload new image') }}</span>
                                        <i class="bx bx-upload d-block d-sm-none"></i>
                                        <input class="image-input" type="file" id="image" name="image" hidden
                                            accept="image/png, image/jpeg" />
                                    </label>
                                    <button type="button" class="btn btn-outline-secondary image-reset">
                                        <i class="bx bx-reset d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">{{ __('Reset') }}</span>
                                    </button>
                                    <br>
                                    {{-- <small class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</small> --}}
                                </div>
                            </div>
                        </div>
                        <hr class="my-0">

                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="unit_name" name="unit_name"
                                placeholder="{{ __('Unit name') }}" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="pack_units">{{ __('Pack units') }}</label>
                            <input type="number" class="form-control" id="pack_units" name="pack_units"
                                placeholder="{{ __('Pack Units') }}" />
                        </div>

                        <input type="hidden" class="form-control" id="unit_price" name="unit_price" value="0" />

                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('Subcategory') }}</label>
                            <div class="input-group input-group-merge">
                                <select class="form-select" id="category_id">
                                    <option value=""> {{ __('Select category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"> {{ $category->name }} </option>
                                    @endforeach
                                </select>
                                <select class="form-select" id="subcategory_id" name="subcategory_id">
                                    <option value=""> {{ __('Select category first') }} </option>
                                </select>
                            </div>
                        </div>

                        {{-- <div class="mb-3">
                          <label class="form-label" for="name">{{ __('Status') }}</label>
                          <select class="form-select" name="status" id="status">
                              <option value="available"> {{ __('Available') }}</option>
                              <option value="unavailable"> {{ __('Unavailable') }}</option>
                          </select>
                      </div> --}}

                        <div class="mb-3" style="text-align: center">
                            <button type="submit" id="submit" name="submit"
                                class="btn btn-primary">{{ __('Send') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- stock modal --}}
    <div class="modal fade" id="stock_modal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h4 class="fw-bold py-1 mb-1">{{ __('Add stock') }}</h4> --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" onsubmit="event.preventDefault()" action="#"
                        enctype="multipart/form-data" id="stock_form">

                        <input type="text" class="form-control" id="product_id" name="product_id" hidden />

                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('Price') }}</label>
                            <input type="number" class="form-control" name="price" />
                        </div>

                        <div class="row  justify-content-between">

                            <div class="form-group col-md-6 p-3">
                                <label class="form-label" for="quantity">{{ __('Quantity') }}</label>
                                <input type="number" class="form-control" name="quantity" />
                            </div>

                            <div class="form-group col-md-6 p-3">
                                <label class="form-label" for="min_quantity">{{ __('Min quantity') }}</label>
                                <input type="number" class="form-control" name="min_quantity" />
                            </div>

                        </div>

                        @if (auth()->user()->role_is('store'))
                            <input type="hidden" name="show_price" id="show_price">
                        @else
                            <div class="mb-3">
                                <label class="form-label" for="show_price">{{ __('Show price') }}</label>
                                <select class="form-select" name="show_price" id="show_price">
                                    <option value="1"> {{ __('Yes') }}</option>
                                    <option value="0"> {{ __('No') }}</option>
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('Status') }}</label>
                            <select class="form-select" name="status">
                                <option value="available"> {{ __('Available') }}</option>
                                <option value="unavailable"> {{ __('Unavailable') }}</option>
                            </select>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="stock_has_promo" name="has_promo"
                                value="1">
                            <label class="form-check-label" for="stock_has_promo">{{ __('Has promo') }}</label>
                        </div>
                        <div id="stock_promo_fields" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="stock_target_quantity">{{ __('Promo target quantity') }}</label>
                                <input type="number" min="1" class="form-control" id="stock_target_quantity"
                                    name="target_quantity" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="stock_new_price">{{ __('Promo new price') }}</label>
                                <input type="number" min="0" step="0.01" class="form-control"
                                    id="stock_new_price" name="new_price" />
                            </div>
                        </div>

                        <div class="mb-3" style="text-align: center">
                            <button type="submit" id="submit_stock" name="submit_stock"
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

            function load_data(category = null, subcategory = null, stock = null, availability = null, provider =
                null) {
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
                        url: "{{ url('product/list') }}",
                        data: {
                            category: category,
                            subcategory: subcategory,
                            stock: stock,
                            availability: availability,
                            provider: provider
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

                                 return '<div class="d-flex justify-content-start align-items-center product-name"><div class="avatar-wrapper"><div class="avatar avatar me-4 rounded-2 bg-label-secondary"><img src="' +
                                     data[0] +
                                     '" class="rounded"></div></div><div class="d-flex flex-column"><h6 class="text-nowrap mb-0">' +
                                     data[1] + '</h6></div></div>';

                             }
                         },*/
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
                            data: 'created_at',
                            name: 'created_at'
                        },

                        /* {
                            data: 'availability',
                            name: 'availability',
                            render: function(data) {
                                if (data == false) {
                                    return '<span class="badge bg-label-danger">{{ __('No') }}</span>';
                                } else {
                                    return '<span class="badge bg-label-success">{{ __('Yes') }}</span>';
                                }
                            }
                        }, */

                        {
                            data: 'pack_units',
                            name: 'pack_units'
                        },
                        {
                            data: 'in_stock',
                            name: 'in_stock',
                            render: function(data) {
                                if (data == false) {
                                    return '<span class="badge bg-label-danger">{{ __('No') }}</span>';
                                } else {
                                    return '<span class="badge bg-label-success">{{ __('Yes') }}</span>';
                                }
                            }
                        },


                        /* {
                            data: 'stock',
                            name: 'stock'
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
                var category = $('#category').val();
                var subcategory = $('#subcategory').val();
                var stock = $('#stock').val();
                var availability = $('#availability').val();
                var provider = $('#provider').val();

                var table = $('#laravel_datatable').DataTable();
                table.destroy();
                load_data(category, subcategory, stock, availability, provider);
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

            $('#stock').on('change', function() {

                refresh_table();

            });

            $('#availability').on('change', function() {

                refresh_table();

            });

            $('#provider').on('change', function() {

                refresh_table();

            });

            /* $('#unit_name').on('blur', function() {

                var unit_name = document.getElementById('unit_name').value;

                document.getElementById('pack_name').value = ' (حزمة) ' + unit_name;


            }); */


            $('#create').on('click', function() {
                document.getElementById('form').reset();
                document.getElementById('form_type').value = "create";
                document.getElementById('uploaded-image').src =
                    "{{ asset('assets/img/icons/file-not-found.jpg') }}";
                document.getElementById('old-image').src =
                    "{{ asset('assets/img/icons/file-not-found.jpg') }}";
                $("#modal").modal('show');
            });


            $(document.body).on('click', '.update', function() {
                document.getElementById('form').reset();
                document.getElementById('form_type').value = "update";
                var product_id = $(this).attr('table_id');
                $("#id").val(product_id);

                $.ajax({
                    url: '{{ url('product/update') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: {
                        product_id: product_id
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status == 1) {

                            document.getElementById('unit_name').value = response.data
                                .unit_name;
                            document.getElementById('pack_units').value = response.data
                                .pack_units;
                            /* document.getElementById('pack_name').value = response.data
                                .pack_name;
                            document.getElementById('unit_price').value = response.data
                                .unit_price;
                            document.getElementById('pack_price').value = response.data
                                .pack_price;
                            document.getElementById('pack_units').value = response.data
                                .pack_units;
                            document.getElementById('unit_type').value = response.data
                                .unit_type; */
                            //document.getElementById('status').value = response.data.status;

                            var image = response.data.image == null ?
                                "{{ asset('assets/img/icons/file-not-found.jpg') }}" : response
                                .data.image;

                            document.getElementById('uploaded-image').src = image;
                            document.getElementById('old-image').src = image;

                            console.log(response.data.category_id);
                            document.getElementById('category_id').value = response.data
                                .category_id;

                            $('#category_id').trigger("change", function() {
                                document.getElementById('subcategory_id').value =
                                    response.data.subcategory_id;
                            });



                            $("#modal").modal("show");
                        }
                    }
                });
            });

            $('#category_id').on('change', function(e, callback) {
                var category_id = document.getElementById('category_id').value;
                $.when(
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

                                var subcategories = document.getElementById('subcategory_id');
                                subcategories.innerHTML =
                                    '<option value="">{{ __('Not selected') }}</option>';

                                for (var i = 0; i < response.data.length; i++) {
                                    var option = document.createElement('option');
                                    option.value = response.data[i].id;
                                    option.innerHTML = response.data[i].name;
                                    subcategories.appendChild(option);
                                }

                            }
                        }
                    })
                ).done(function(a1, a2) {
                    callback();
                });



            });

            $('#submit').on('click', function() {
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

                /* var formdata = new FormData($("#form")[0]); */
                var queryString = new FormData($("#form")[0]);

                var formtype = document.getElementById('form_type').value;
                //console.log(formtype);
                if (formtype == "create") {
                    url = "{{ url('product/create') }}";
                }

                if (formtype == "update") {
                    url = "{{ url('product/update') }}";
                    queryString.append("product_id", document.getElementById('id').value)
                }

                $("#modal").modal("hide");


                $.ajax({
                    url: url,
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

            $(document.body).on('click', '.delete', function() {

                var product_id = $(this).attr('table_id');

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
                            url: "{{ url('product/delete') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            data: {
                                product_id: product_id
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

            $(document.body).on('click', '.add_stock', function() {
                var product_id = $(this).attr('table_id');
                document.getElementById('stock_form').reset();
                document.getElementById('product_id').value = product_id;
                $("#stock_modal").modal('show');
            });

            $('#submit_stock').on('click', function() {

                var formdata = new FormData($("#stock_form")[0]);

                var hasPromoEl = document.getElementById('stock_has_promo');
                if (hasPromoEl && !hasPromoEl.checked) {
                    formData.append('has_promo', '0');
                }

                $("#stock_modal").modal("hide");

                $.ajax({
                    url: "{{ url('stock/create') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: formdata,
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

            $(document).on('change', '#stock_has_promo', function() {
                $('#stock_promo_fields').toggle(this.checked);
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
