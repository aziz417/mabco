@extends('layouts.admin.master')

@section('page')
    Order Cancel
@endsection

@push('css')

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
            <div class="card-body  table-responsive">
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
                        <th>Status</th>
                        <th>Date</th>
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
                        <th>Status</th>
                        <th>Date</th>
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
                url: '{!!  route('order.getCancelData') !!}',
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
                {data: 'status', name: 'status'},
                {data: 'date', name: 'date'},
            ]
        });
    });
</script>
@endpush
