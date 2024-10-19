@extends('layouts/contentNavbarLayout')

@section('title', __('Orders'))

@section('content')

    <h4 class="fw-bold py-3 mb-3">
        <span class="text-muted fw-light">{{ __('Orders') }} /</span> {{ __('Browse orders') }}
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
                        {{-- <th>{{ __('Seller') }}</th> --}}
                        <th>{{ __('Buyer') }}</th>
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

    {{-- invoice modal --}}
    {{-- <div class="modal fade" id="invoice_modal"  aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="fw-bold py-1 mb-1">{{__('Create invoice')}}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">


        <form class="form-horizontal" onsubmit="event.preventDefault()" action="#"
          enctype="multipart/form-data" id="invoice_form">

            <input type="text" id="invoice_order_id" name="order_id" hidden />

            <div class="mb-3">
              <label class="form-label" for="tax_type">{{__('Tax type')}}</label>
              <select class="form-select" id="tax_type" name="tax_type">
                <option value="1" > {{__('Fixed')}}</option>
                <option value="2" > {{__('Percentage')}}</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label" for="tax_amount">{{__('Tax amount')}}</label>
              <input type="number" class="form-control" id="tax_amount" name="tax_amount">
              </select>
            </div>

          <div class="mb-3" style="text-align: center">
            <button type="submit" id="submit_invoice" name="submit_invoice" class="btn btn-primary">{{__('Send')}}</button>
          </div>



        </form>
      </div>
    </div>
  </div>
</div> --}}

    {{-- payment modal --}}
    <div class="modal fade" id="payment_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="fw-bold py-1 mb-1">{{ __('Order payment') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" onsubmit="event.preventDefault()" action="#"
                        enctype="multipart/form-data" id="payment_form">

                        <input type="text" id="payment_order_id" name="order_id" hidden />

                        <div class="mb-3">
                            <label class="form-label" for="payment_method">{{ __('Payment method') }}</label>
                            <select class="form-select" id="payment_method" name="payment_method">
                                <option value="1"> {{ __('Card') }}</option>
                                <option value="2"> {{ __('Cash') }}</option>
                            </select>
                        </div>


                        <div class="mb-3" style="text-align: center">
                            <button type="submit" id="submit_payment" name="submit_payment"
                                class="btn btn-primary">{{ __('Send') }}</button>
                        </div>



                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- driver modal --}}
    <div class="modal fade" id="driver_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="fw-bold py-1 mb-1">{{ __('Ship order') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" onsubmit="event.preventDefault()" action="#"
                        enctype="multipart/form-data" id="driver_form">


                        <input type="text" id="driver_order_id" name="order_id" hidden />

                        <div class="mb-3">
                            <label class="form-label" for="driver_id">{{ __('Driver') }}</label>
                            <select class="form-select" id="driver_id" name="driver_id">
                                <option value=""> {{ __('Select driver') }}</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}"> {{ $driver->fullname() }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" style="text-align: center">
                            <button type="submit" id="submit_driver" name="submit_driver"
                                class="btn btn-primary">{{ __('Send') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- note modal --}}
    <div class="modal fade" id="note_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="fw-bold py-1 mb-1">{{ __('Order note') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" onsubmit="event.preventDefault()" action="#"
                        enctype="multipart/form-data" id="note_form">


                        <input type="text" id="note_order_id" name="order_id" hidden />

                        <div class="mb-3">
                            <label class="form-label" for="driver_id">{{ __('Note') }}</label>
                            <textarea id="note" name="note" class="form-control" rows="5" style="height: 125;" dir="rtl"></textarea>
                        </div>
                        <div class="mb-3" style="text-align: center">
                            <button type="submit" id="submit_note" name="submit_note"
                                class="btn btn-primary">{{ __('Send') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

     {{-- info modal --}}
   <div class="modal fade" id="info_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h4 class="fw-bold py-1 mb-1">{{ __('Order info') }}</h4> --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                  <div class="row justify-content-center">
                    <div class="card col-md-5 mx-4 shadow-lg" style="box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15), 0 -0.25rem 0.5rem rgba(0, 0, 0, 0.08);">
                      <!-- First card content remains the same -->
                      <div class="card-header">
                        <h5 class="card-title m-0">Customer details</h5>
                      </div>
                      <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-6">
                          <div class="avatar me-3">
                            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
                          </div>
                          <div class="d-flex flex-column">
                            <a href="app-user-view-account.html" class="text-body text-nowrap">
                              <h6 class="mb-0">Shamus Tuttle</h6>
                            </a>
                            <span>Customer ID: #58909</span>
                          </div>
                        </div>
                        <div class="d-flex justify-content-between">
                          <h6 class="mb-1">Contact info</h6>
                          <h6 class="mb-1"><a href="javascript:void(0)"><i class="bx bx-map me-2"></i></a></h6>
                        </div>
                        <p class="mb-1">Email: Shamus889@yahoo.com</p>
                        <p class="mb-0">Mobile: +1 (609) 972-22-22</p>
                      </div>
                    </div>

                    <div class="card col-md-5 mx-4 shadow-lg" style="box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15), 0 -0.25rem 0.5rem rgba(0, 0, 0, 0.08);">
                      <!-- Second card content remains the same -->
                      <div class="card-header">
                        <h5 class="card-title m-0">Customer details</h5>
                      </div>
                      <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-6">
                          <div class="avatar me-3">
                            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
                          </div>
                          <div class="d-flex flex-column">
                            <a href="app-user-view-account.html" class="text-body text-nowrap">
                              <h6 class="mb-0">Shamus Tuttle</h6>
                            </a>
                            <span>Customer ID: #58909</span>
                          </div>
                        </div>
                        <div class="d-flex justify-content-between">
                          <h6 class="mb-1">Contact info</h6>
                          <h6 class="mb-1"><a href="javascript:void(0)"><i class="bx bx-map me-2"></i></a></h6>
                        </div>
                        <p class="mb-1">Email: Shamus889@yahoo.com</p>
                        <p class="mb-0">Mobile: +1 (609) 972-22-22</p>
                      </div>
                    </div>
                  </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            load_data();

            function load_data(status = null) {
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
                            type: 2
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
                            data: 'seller',
                            name: 'seller'
                        }, */

                        {
                            data: 'buyer',
                            name: 'buyer',
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
                                //return '<span>' + data + '</span>';
                            }
                        }

                    ]
                });
            }

            $('#status').on('change', function() {

                var status = document.getElementById('status').value;

                var table = $('#laravel_datatable').DataTable();
                table.destroy();
                load_data(status);

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

        $(document.body).on('click', '.info', function() {

            var order_id = $(this).attr('table_id');

            $("#info_modal").modal('show');
        });


        $(document.body).on('click', '.ship', function() {
            document.getElementById('driver_form').reset();
            var order_id = $(this).attr('table_id');
            document.getElementById('driver_order_id').value = order_id;
            $("#driver_modal").modal('show');
        });

        $(document.body).on('click', '.note', function() {
            document.getElementById('note_form').reset();
            var order_id = $(this).attr('table_id');
            document.getElementById('note_order_id').value = order_id;

            $.ajax({
                url: "{{ url('order/update') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {
                    order_id: order_id
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status == 1) {


                        document.getElementById('note').innerHTML = response.data.note;
                        $("#note_modal").modal('show');
                    }
                }
            });


        });

        $(document.body).on('click', '#submit_note', function() {

            var formdata = new FormData($("#note_form")[0]);

            $.ajax({
                url: "{{ url('order/update') }}",
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

                        Swal.fire(
                            "{{ __('Success') }}",
                            "{{ __('success') }}",
                            'success'
                        )
                    }
                }
            });

            $("#note_modal").modal('hide');
        });

        $(document.body).on('click', '.payment', function() {
            document.getElementById('payment_form').reset();
            var order_id = $(this).attr('table_id');
            document.getElementById('payment_order_id').value = order_id;
            $("#payment_modal").modal('show');
        });

        /*  $('#submit_invoice').on('click', function() {
           var formdata = new FormData($("#invoice_form")[0]);
           formdata.append('status','accepted');
           $("#driver_modal").modal('hide');

           $.ajax({
                   url: "{{ url('order/update') }}",
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },
                   type:'POST',
                   data:formdata,
                   dataType : 'JSON',
                   contentType: false,
                   processData: false,
                   success:function(response){
                       if(response.status==1){

                         Swal.fire(
                           "{{ __('Success') }}",
                           "{{ __('success') }}",
                           'success'
                         ).then((result)=>{
                           location.reload();
                         });
                       }
                     }
                 });

         }); */

        $(document.body).on('click', '.delete', function() {

            var order_id = $(this).attr('table_id');

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
                        url: "{{ url('order/delete') }}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: {
                            order_id: order_id
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

        $('#submit_driver').on('click', function() {
            var formdata = new FormData($("#driver_form")[0]);
            formdata.append('status', 'ongoing');
            $("#driver_modal").modal('show');

            $.ajax({
                url: "{{ url('order/update') }}",
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

        });

        $('#submit_payment').on('click', function() {
            var formdata = new FormData($("#payment_form")[0]);
            formdata.append('status', 'delivered');
            $("#payment_modal").modal('hide');

            $.ajax({
                url: "{{ url('order/update') }}",
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
        });

        $('#shipping_switch').on('change', function() {
            var checkbox = document.getElementById('shipping_switch');
            var status = checkbox.checked ? 1 : 0;
            $.ajax({
                url: "{{ url('shipping/switch') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                dataType: 'JSON',
                data: {
                    status: status,
                },
                //contentType: false,
                //processData: false,
                success: function(response) {
                    if (response.status == 1) {
                        location.reload();
                    }
                }
            });

        });

        $(document).on('click', '.invoice', function() {

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

            var invoice_id = $(this).attr('table_id');


            $.ajax({
                url: '{{ url('invoice/update') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {
                    invoice_id: invoice_id
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status == 1) {
                        Swal.close();
                        window.open(response.data)
                    }
                }
            });
        });
    </script>
@endsection
