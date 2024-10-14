@extends('layouts/contentNavbarLayout')

@section('title', __('Users'))

@section('content')

    <h4 class="fw-bold py-3 mb-3">
        <span class="text-muted fw-light">{{ __('Users') }} /</span> {{ __('Browse users') }}
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">{{ __('Users table') }}

            <select class="filter-select" id="status" name="status">
                <option value=""> {{ __('Status filter') }} </option>
                <option value="active"> {{ __('Active') }} </option>
                <option value="inactive"> {{ __('Inactive') }} </option>
                <option value="blocked"> {{ __('Blocked') }} </option>
            </select>

            <select class="filter-select" id="type" name="type">
                <option value=""> {{ __('Type filter') }} </option>
                <option value="1"> {{ __('Provider') }} </option>
                <option value="2"> {{ __('Broker') }} </option>
                <option value="3"> {{ __('Store') }} </option>
                <option value="4"> {{ __('Client') }} </option>
                <option value="5"> {{ __('Driver') }} </option>
            </select>
        </h5>
        <div class="table-responsive text-nowrap">
            <table class="table" id="laravel_datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Enterprise') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Created at') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection


@section('page-script')
    <script>
        $(document).ready(function() {
            load_data();

            function load_data(status = null, type = null) {
                //$.fn.dataTable.moment( 'YYYY-M-D' );
                var table = $('#laravel_datatable').DataTable({

                    responsive: true,
                    processing: true,
                    serverSide: true,
                    pageLength: 100,

                    ajax: {
                        url: "{{ url('user/list') }}",
                        data: {
                            status: status,
                            type: type,
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

                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'enterprise',
                            name: 'enterprise'
                        },

                        {
                            data: 'phone',
                            name: 'phone'
                        },


                        {
                            data: 'email',
                            name: 'email'
                        },


                        {
                            data: 'type',
                            name: 'type',
                            render: function(data) {
                                if (data == 1) {
                                    return '<span class="badge bg-label-primary">{{ __('Provider') }}</span>';
                                } else if (data == 2) {
                                    return '<span class="badge bg-label-success">{{ __('Broker') }}</span>';
                                } else if (data == 3) {
                                    return '<span class="badge bg-label-warning">{{ __('Store') }}</span>';
                                } else if (data == 4) {
                                    return '<span class="badge bg-label-info">{{ __('Client') }}</span>';
                                } else if (data == 5) {
                                    return '<span class="badge bg-label-danger">{{ __('Driver') }}</span>';
                                }
                            }
                        },


                        {
                            data: 'status',
                            name: 'status',
                            render: function(data) {
                                if (data == 'blocked') {
                                    return '<span class="badge bg-danger">{{ __('Blocked') }}</span>';
                                } else if (data == 'active') {
                                    return '<span class="badge bg-success">{{ __('Active') }}</span>';
                                } else if (data == 'inactive') {
                                    return '<span class="badge bg-warning">{{ __('Inactive') }}</span>';
                                }
                            }
                        },

                        {
                            data: 'created_at',
                            name: 'created_at'
                        },

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

            $('#status').on('change', function() {

                var status = document.getElementById('status').value;
                var type = document.getElementById('type').value;

                var table = $('#laravel_datatable').DataTable();
                table.destroy();
                load_data(status, type);

            });

            $('#type').on('change', function() {

                var status = document.getElementById('status').value;
                var type = document.getElementById('type').value;

                var table = $('#laravel_datatable').DataTable();
                table.destroy();
                load_data(status, type);

            });

            $(document.body).on('click', '.delete', function() {

                var user_id = $(this).attr('table_id');

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
                            url: "{{ url('user/update') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            data: {
                                user_id: user_id,
                                status: 3
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

            $(document.body).on('click', '.restore', function() {

                var user_id = $(this).attr('table_id');

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
                            url: "{{ url('user/update') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            data: {
                                user_id: user_id,
                                status: 1
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

            $(document.body).on('click', '.reset_password', function() {

                var user_id = $(this).attr('table_id');

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
                            url: "{{ url('user/reset_password') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            data: {
                                user_id: user_id,
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
        });
    </script>
@endsection
