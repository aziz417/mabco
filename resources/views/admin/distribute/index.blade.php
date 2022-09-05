@extends('layouts.admin.master')

@section('page')
    Order Distribute
@endsection

@push('css')
    <style type="text/css">
        /* Basic Rules */
        .switch input {
            display:none;
        }
        .switch {
            display:inline-block;
            width:55px;
            height:25px;
            margin:8px;
            transform:translateY(50%);
            position:relative;
        }
        /* Style Wired */
        .slider {
            position:absolute;
            top:0;
            bottom:0;
            left:0;
            right:0;
            border-radius:30px;
            box-shadow:0 0 0 2px #777, 0 0 4px #777;
            cursor:pointer;
            border:4px solid transparent;
            overflow:hidden;
            transition:.4s;
        }
        .slider:before {
            position:absolute;
            content:"";
            width:100%;
            height:100%;
            background:#777;
            border-radius:30px;
            transform:translateX(-30px);
            transition:.4s;
        }

        input:checked + .slider:before {
            transform:translateX(30px);
            background:limeGreen;
        }
        input:checked + .slider {
            box-shadow:0 0 0 2px limeGreen,0 0 2px limeGreen;
        }

        /* Style Flat */
        .switch.flat .slider {
            box-shadow:none;
        }
        .switch.flat .slider:before {
            background:#FFF;
        }
        .switch.flat input:checked + .slider:before {
            background:white;
        }
        .switch.flat input:checked + .slider {
            background:limeGreen;
        }
        .patch{
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
                    <h3 class="card-title">@yield('page')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="data-table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Order Code</th>
                            <th>Seller</th>
                            <th>Retailer</th>
                            <th>Without D.Bill</th>
                            <th>Discount</th>
                            <th>Bill</th>
                            <th>Date</th>
                            <th>Distribute</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Order Code</th>
                            <th>Seller</th>
                            <th>Retailer</th>
                            <th>Without D.Bill</th>
                            <th>Discount</th>
                            <th>Bill</th>
                            <th>Date</th>
                            <th>Distribute</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <div id="chalanPrint" style="display: none"></div>
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
                    url: '{!!  route('order_distribute.getData') !!}',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'order_code', name: 'order_code'},
                    {data: 'seller_name', name: 'seller_name'},
                    {data: 'retailer_name', name: 'retailer_name'},
                    {data: 'bill_without_discount', name: 'bill_without_discount'},
                    {data: 'total_discount', name: 'total_discount'},
                    {data: 'bill', name: 'bill'},
                    {data: 'date', name: 'date'},
                    {data: 'distribute', name: 'distribute'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });

        });
    </script>

    <script>
        $(document).on('change','.status_toggle', function (e) {
            e.preventDefault();
            var id = $(this).attr('value');
            $(this).closest('.switch').addClass('custom_disabled')

            $.ajax({
                type: "GET",
                url: "{{ route('order.distribute.approve','') }}/"+id,
                dataType: 'json',
                success: function (data) {
                    if (data.message){
                        toastr.options =
                            {
                                "closeButton" : true,
                                "progressBar" : true
                            };
                        toastr.success(data.message);
                    }
                }
            });
        })
    </script>

    <script>
        $(document).on('change','.print_chalan', function (e) {
            e.preventDefault();

            var id = $(this).attr('value');

            $.ajax({
                type: "GET",
                url: "{{ route('order.distribute.approve','') }}/"+id,
                dataType: 'json',
                success: function (data) {
                    if (data.message){
                        toastr.options =
                            {
                                "closeButton" : true,
                                "progressBar" : true
                            };
                        toastr.success(data.message);
                    }
                }
            });
        })
    </script>
    <script>
        $(document).on('click','.order_chalan', function(e){
            e.preventDefault();
            var id = $(this).attr('rel');
            $.get("{{ route('order.chalan') }}", { id: id }, function (response) {
                if (response) {

                    // console.log(response)

                    $("#chalanPrint").html(response)

                    var divToPrint = document.getElementById("chalanPrint");

                    var newWin = window.open('', 'Print-Window');

                    newWin.document.open();

                    newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

                    newWin.document.close();

                    setTimeout(function () {
                        newWin.close();
                    }, 1000);
                }
            });
        });
    </script>
@endpush
