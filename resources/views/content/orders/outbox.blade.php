@extends('layouts/contentNavbarLayout')

@section('title', __('Orders'))

@section('content')

    <h4 class="fw-bold py-3 mb-3">
        <span class="text-muted fw-light">{{ __('Outbox') }} /</span> {{ __('Browse outbox') }}
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">

        <div class="row  justify-content-between">

            <div class="form-group col-md-3 mx-3 my-3">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input class="form-control" id="search" name="search" value="{{ request()->get('search') }}">
            </div>

            <div class="form-group col-md-3 mx-3 my-3">
                <label for="category" class="form-label">{{ __('Status filter') }}</label>
                <select class="form-select" id="status" name="status">
                    <option value=""> {{ __('Not selected') }}</option>
                    <option value="pending"> {{ __('Pending') }}</option>
                    <option value="accepted"> {{ __('Accepted') }}</option>
                    <option value="canceled"> {{ __('Canceled') }}</option>
                    <option value="confirmed"> {{ __('Confirmed') }}</option>
                    <option value="shipped"> {{ __('Shipped') }}</option>
                    <option value="ongoing"> {{ __('Ongoing') }}</option>
                    <option value="delivered"> {{ __('Delivered') }}</option>
                    <option value="received"> {{ __('Received') }}</option>
                </select>
            </div>

        </div>

        <div class="table-responsive text-nowrap">
            <table class="table" id="laravel_datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Seller') }}</th>
                        {{-- <th>{{ __('Buyer') }}</th> --}}
                        <th>{{ __('Created at') }}</th>
                        <th>{{ __('Status') }}</th>
                        {{--           <th>{{__('Driver')}}</th>
          <th>{{__('Purchase amount')}}</th>
          <th>{{__('Tax amount')}}</th>
          <th>{{__('Total amount')}}</th> --}}
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

            function load_data(status = null, search = null) {
                //$.fn.dataTable.moment( 'YYYY-M-D' );
                var table = $('#laravel_datatable').DataTable({

                    responsive: true,
                    processing: true,
                    serverSide: true,
                    searching: false,
                    lengthChange: false,
                    pageLength: 10,

                    ajax: {
                        url: "{{ url('order/list') }}",
                        data: {
                            status: status,
                            search: search,
                            type: 1
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
                            data: 'seller',
                            name: 'seller',
                            render: function(data) {

                                return '<div class="d-flex align-items-center"><img src="' + data[
                                        0] +
                                    '"width="40" height="40"class="rounded-circle me-2"><span>' +
                                    data[1] +
                                    '</span></div>';

                            }
                        },

                        {
                            data: 'created_at',
                            name: 'created_at'
                        },

                        {
                            data: 'status',
                            name: 'status',
                            render: function(data) {
                                if (data == 'pending') {
                                    return '<span class="badge bg-label-secondary">{{ __('pending') }}</span>';
                                }
                                if (data == 'accepted') {
                                    return '<span class="badge bg-label-blue">{{ __('accepted') }}</span>';
                                }
                                if (data == 'canceled') {
                                    return '<span class="badge bg-label-red">{{ __('canceled') }}</span>';
                                }
                                if (data == 'confirmed') {
                                    return '<span class="badge bg-label-orange">{{ __('confirmed') }}</span>';
                                }
                                if (data == 'shipped') {
                                    return '<span class="badge bg-label-yellow">{{ __('shipped') }}</span>';
                                }
                                if (data == 'ongoing') {
                                    return '<span class="badge bg-label-cyan">{{ __('ongoing') }}</span>';
                                }
                                if (data == 'delivered') {
                                    return '<span class="badge bg-label-teal">{{ __('delivered') }}</span>';
                                }
                                if (data == 'received') {
                                    return '<span class="badge bg-label-green">{{ __('received') }}</span>';
                                }

                            }


                        },


                        /*  {
                             data: 'driver',
                             name: 'driver'
                         },

                         {
                             data: 'purchase_amount',
                             name: 'purchase_amount'
                         },

                         {
                             data: 'tax_amount',
                             name: 'tax_amount'
                         },

                         {
                             data: 'total_amount',
                             name: 'total_amount'
                         }, */

                        {
                            data: 'action',
                            name: 'action',
                            render: function(data) {
                                return '<div class="dropdown"><button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button><div class="dropdown-menu">' +
                                    data + '</div></div>'
                                // return '<span>' + data + '</span>';
                            }
                        }

                    ]
                });
            }

            $('#status').on('change', function() {

                var status = document.getElementById('status').value;
                var search = document.getElementById('search').value;
                var table = $('#laravel_datatable').DataTable();
                table.destroy();
                load_data(status, search);

            });

            $('#search').on('change keyup blur', function() {

                var status = document.getElementById('status').value;
                var search = document.getElementById('search').value;
                var table = $('#laravel_datatable').DataTable();
                table.destroy();
                load_data(status, search);

            });
        });



        function update_order(order_id, status) {

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
                        url: "{{ url('order/update') }}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: {
                            order_id: order_id,
                            status: status
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
        }



        $(document.body).on('click', '.refuse', function() {

            var order_id = $(this).attr('table_id');

            update_order(order_id, 'canceled');

        });

        $(document.body).on('click', '.accept', function() {

            var order_id = $(this).attr('table_id');

            update_order(order_id, 'accepted');
        });

        $(document.body).on('click', '.confirm', function() {

            var order_id = $(this).attr('table_id');

            update_order(order_id, 'confirmed');
        });

        $(document.body).on('click', '.ongoing', function() {

            var order_id = $(this).attr('table_id');

            update_order(order_id, 'ongoing')
        });

        $(document.body).on('click', '.deliver', function() {

            var order_id = $(this).attr('table_id');

            update_order(order_id, 'delivered')
        });

        $(document.body).on('click', '.receive', function() {

            var order_id = $(this).attr('table_id');

            update_order(order_id, 'received')
        });
    </script>
@endsection
