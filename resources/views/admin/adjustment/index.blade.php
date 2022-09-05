@extends('layouts.admin.master')

@section('page')
    Adjustment
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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="success_message"></div>

            <div id="error_message"></div>

            <div class="card">
                <div class="card-header">
                    <a href="{{ route('adjustment.create') }}" class="btn btn-sm btn-primary  float-right"><i
                            class="fas fa-plus"></i> Add @yield('page')</a>
                    <h3 class="card-title">@yield('page') List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="data-table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Adjustment No</th>
                            <th>Seller</th>
                            <th>Retailer</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>O.O.standing</th>
                            <th>Ad.Amount</th>
                            <th>N.O.Standing</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Adjustment No</th>
                            <th>Seller</th>
                            <th>Retailer</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>O.O.standing</th>
                            <th>Ad.Amount</th>
                            <th>N.O.Standing</th>
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
                    url: '{!! route('adjustment.getData') !!}',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'adjustment_number', name: 'adjustment_number'},
                    {data: 'seller_name', name: 'seller_name'},
                    {data: 'retailer_name', name: 'retailer_name'},
                    {data: 'title', name: 'title'},
                    {data: 'date', name: 'date'},
                    {data: 'total_out_standing', name: 'total_out_standing'},
                    {data: 'amount', name: 'amount'},
                    {data: 'now_out_standing', name: 'now_out_standing'},
                    {data: 'action', name: 'action', orderable: false, searchable: true}
                ]
            });

        });
    </script>

@endpush
