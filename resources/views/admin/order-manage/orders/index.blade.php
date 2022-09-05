@extends('layouts.admin.master')

@section('page')
    Order
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

        .large-table-container-3 {
            overflow-x: scroll;
            overflow-y: auto;
        }

        .large-table-container-3 table {

        }

        .large-table-fake-top-scroll-container-3 {
            max-width: 500px;
            overflow-x: scroll;
            overflow-y: auto;
            margin-bottom: 12px;
        }

        .large-table-fake-top-scroll-container-3 div {
            background-color: red; /*Just for test, to see the 'fake' div*/
            font-size: 1px;
            line-height: 1px;
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
                    <a href="{{ route('order.create') }}" class="btn btn-sm btn-primary  float-right"><i
                            class="fas fa-plus"></i> Add @yield('page')</a>
                    <h3 class="card-title">@yield('page')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <div class="large-table-fake-top-scroll-container-3">
                        <div>&nbsp;</div>
                    </div>
                    <div class="large-table-container-3">
                        <table id="data-table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#Sl NO</th>
                                <th>Order Code</th>
                                <th>User</th>
                                <th>Seller</th>
                                <th>Retailer</th>
                                <th>Amount</th>
                                <th>Commission Type</th>
                                <th>Discount</th>
                                <th>Bill</th>
                                <th>Total Out Standing</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>#Sl NO</th>
                                <th>Order Code</th>
                                <th>User</th>
                                <th>Seller</th>
                                <th>Retailer</th>
                                <th>Amount</th>
                                <th>Commission Type</th>
                                <th>Discount</th>
                                <th>Bill</th>
                                <th>Total Out Standing</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection

@push('js')

    <script>
        $(document).ready(function () {
            var tableContainer = $(".large-table-container-3");
            var table = $(".large-table-container-3 table");
            var fakeContainer = $(".large-table-fake-top-scroll-container-3");
            var fakeDiv = $(".large-table-fake-top-scroll-container-3 div");

            var tableWidth = table.width();
            fakeDiv.width(tableWidth);

            fakeContainer.scroll(function() {
                tableContainer.scrollLeft(fakeContainer.scrollLeft());
            });

            $('#data-table').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                pagingType: "full_numbers",
                ajax: {
                    url: '{!!  route('order.getData') !!}',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'order_code', name: 'order_code'},
                    {data: 'user_name', name: 'user_name'},
                    {data: 'seller_name', name: 'seller_name'},
                    {data: 'retailer_name', name: 'retailer_name'},
                    {data: 'bill_without_discount', name: 'bill_without_discount'},
                    {data: 'commission_type', name: 'commission_type'},
                    {data: 'total_discount', name: 'total_discount'},
                    {data: 'bill', name: 'bill'},
                    {data: 'total_out_standing', name: 'total_out_standing'},
                    {data: 'date', name: 'date'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
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

                            $('#data-table').DataTable().ajax.reload();

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
@endpush
