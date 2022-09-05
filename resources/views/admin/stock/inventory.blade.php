@extends('layouts.admin.master')

@section('page')
    Stock-{{ ucfirst($type) }}
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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="success_message"></div>

            <div id="error_message"></div>

            <div class="card card-primary">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@yield('page')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body  table-responsive">
                    <table id="data-table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Date</th>
                            <th>P.Name</th>
                            <th>C.Name</th>
                            <th>B.Name</th>
                            <th>U.Name</th>
                            <th>Old Q</th>
                            @if($type == 'in')
                                <th style="background: #0e84b5">In Q</th>
                            @else
                                <th style="background: #0e84b5">Out Q</th>
                            @endif
                            <th>Now Q</th>
                            <th>T.Price</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Date</th>
                            <th>P.Name</th>
                            <th>C.Name</th>
                            <th>B.Name</th>
                            <th>U.Name</th>
                            <th>Old Q</th>
                            @if($type == 'in')
                                <th style="background: #0e84b5">In Q</th>
                            @else
                                <th style="background: #0e84b5">Out Q</th>
                            @endif
                            <th>Now Q</th>
                            <th>T.Price</th>
                            <th>Amount</th>
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
        $(document).ready(function(){

            $('#data-table').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                pagingType: "full_numbers",
                ajax: {
                    url: '{!!  route('inventory.getData', $type) !!}',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'date', name: 'date'},
                    {data: 'product_name', name: 'product_name'},
                    {data: 'category_name', name: 'category_name'},
                    {data: 'brand_name', name: 'brand_name'},
                    {data: 'unit_name', name: 'unit_name'},
                    {data: 'old_quantity', name: 'old_quantity'},
                    {data: 'add_or_less', name: 'add_or_less'},
                    {data: 'now_quantity', name: 'now_quantity'},
                    {data: 'price', name: 'price'},
                    {data: 'amount', name: 'amount'},
                ]
            });

        });
    </script>
@endpush
