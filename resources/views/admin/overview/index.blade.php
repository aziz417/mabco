@extends('layouts.admin.master')

@section('page')
    <span class="role_set">Sellers</span>
@endsection

@push('css')

@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="success_message"></div>

            <div id="error_message"></div>

            <div class="card">
                <div class="row">
                    <div class="col-sm-7">
                        <div class="card-body d-none" id="seller_info">
                            <h4><span>Seller Info</span>  </h4>

                            <div class="row">
                                <div class="col-3">
                                    <h6>Name:</h6>
                                    <h6>Phone:</h6>
                                    <h6>Email:</h6>
                                    <h6>Out Standing:</h6>
                                </div>
                                <div class="col-9">
                                    <h6 id="seller_name"></h6>
                                    <h6 id="seller_phone"></h6>
                                    <h6 id="seller_email"></h6>
                                    <h6 id="seller_total_out_standing"></h6>
                                </div>
                                <a class="btn-sm btn-info" href="{{ route('overview') }}">Back</a>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-5"></div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="data-table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Total Sell Amount</th>
                            <th>Total Receive Amount</th>
                            <th>Total Out Standing</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Total Sell Amount</th>
                            <th>Total Receive Amount</th>
                            <th>Total Out Standing</th>
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
            var id = null;
            getOverviewData(id)
        });

        $(document).on('click','.retailers', function(e){
            e.preventDefault();
            var id = $(this).attr('rel');

            $("#data-table").dataTable().fnDestroy();
            sellerInformation(id)
            getOverviewData(id)
        });

        function sellerInformation(id){
            $.get("{{ route('seller.information') }}", {id: id}, function (response) {
                if (response) {
                    $(".role_set").html('Retailers')
                    $("#seller_info").removeClass('d-none')
                    $("#seller_name").html(response.name)
                    $("#seller_phone").html(response.phone)
                    $("#seller_email").html(response.email)
                    $("#seller_total_out_standing").html(response.total_out_standing)
                }
            })
        }

        function getOverviewData(id){
            $('#data-table').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                pagingType: "full_numbers",
                ajax: {
                    url: 'overview/getData/'+id,
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'phone', name: 'phone'},
                    {data: 'bill', name: 'bill'},
                    {data: 'receive_amount', name: 'receive_amount'},
                    {data: 'total_out_standing', name: 'total_out_standing'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        }
    </script>

@endpush
