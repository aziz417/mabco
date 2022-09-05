@extends('layouts.admin.master')

@section('page')
    Return
@endsection

@push('css')

    <style type="text/css">
        /* Basic Rules */
        .switch input {
            display: none;
        }

        .switch {
            display: inline-block;
            width: 55px;
            height: 25px;
            margin: 8px;
            transform: translateY(50%);
            position: relative;
        }

        /* Style Wired */
        .slider {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: 30px;
            box-shadow: 0 0 0 2px #777, 0 0 4px #777;
            cursor: pointer;
            border: 4px solid transparent;
            overflow: hidden;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            width: 100%;
            height: 100%;
            background: #777;
            border-radius: 30px;
            transform: translateX(-30px);
            transition: .4s;
        }

        input:checked + .slider:before {
            transform: translateX(30px);
            background: limeGreen;
        }

        input:checked + .slider {
            box-shadow: 0 0 0 2px limeGreen, 0 0 2px limeGreen;
        }

        /* Style Flat */
        .switch.flat .slider {
            box-shadow: none;
        }

        .switch.flat .slider:before {
            background: #FFF;
        }

        .switch.flat input:checked + .slider:before {
            background: white;
        }

        .switch.flat input:checked + .slider {
            background: limeGreen;
        }

        .patch {
            margin-top: -25px;
        }
        .custom_disabled {
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="success_message"></div>

            <div id="error_message"></div>

            <div class="card">
                <div class="card-header">
                    <a href="{{ route('return.create') }}" class="btn btn-sm btn-primary  float-right"><i
                            class="fas fa-plus"></i> Add @yield('page')</a>
                    <h3 class="card-title">@yield('page') List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="data-table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Return Code</th>
                            <th>Type</th>
                            <th>Seller Name</th>
                            <th>Retailer Name</th>
                            <th>Total Amount</th>
                            <th>Discount</th>
                            <th>Return Amount</th>
                            <th>Date</th>
                            <th>Approve</th>
                            <th>Action</th>

                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Return Code</th>
                            <th>Type</th>
                            <th>Seller Name</th>
                            <th>Retailer Name</th>
                            <th>Total Amount</th>
                            <th>Discount</th>
                            <th>Return Amount</th>
                            <th>Date</th>
                            <th>Approve</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            $('#data-table').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                pagingType: "full_numbers",
                ajax: {
                    url: '{!! route('return.get_return_list') !!}/',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'return_code', name: 'return_code'},
                    {data: 'type', name: 'type'},
                    {data: 'seller_name', name: 'seller_name'},
                    {data: 'retailer_name', name: 'retailer_name'},
                    {data: 'total_amount', name: 'total_amount'},
                    {data: 'discount', name: 'discount'},
                    {data: 'return_amount', name: 'return_amount'},
                    {data: 'date', name: 'date'},
                    {data: 'approve', name: 'approve'},
                    {data: 'action', name: 'action', orderable: false, searchable: true}
                ]
            });

        });
    </script>

    <script>
        $(document).on('click', '.deleteRecord', function (e) {
            e.preventDefault();
            var id = $(this).attr('rel');
            var deleteFunction = $(this).attr('rel1');
            swal({
                    title: "Are You Sure?",
                    text: "You will not be able to recover this record again",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, Delete It"
                },
                function () {
                    $.ajax({
                        type: "DELETE",
                        url: deleteFunction + '/' + id,
                        data: {id: id},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {

                            $('#data-table').DataTable().ajax.reload(null, false);

                            if (data.message) {
                                toastr.options =
                                    {
                                        "closeButton": true,
                                        "progressBar": true
                                    };
                                toastr.success(data.message);
                            }
                        }
                    });
                });
        });
    </script>
    <script>
        $(document).on('change', '.status_toggle', function (e) {
            e.preventDefault();

            var id = $(this).attr('value');
            $(this).closest('.switch').addClass('custom_disabled')
            $("#edit-"+id).addClass('d-none')

            $.ajax({
                type: "GET",
                url: "{{ route('return.status_change','') }}/" + id,
                dataType: 'json',
                success: function (data) {
                    if (data.message) {
                        toastr.options =
                            {
                                "closeButton": true,
                                "progressBar": true
                            };
                        console.log('dgf')
                        toastr.success(data.message);
                    }
                }
            });
        })
    </script>
@endpush
