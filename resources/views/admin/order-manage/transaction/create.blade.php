@extends('layouts.admin.master')

@section('page')
    Transaction Create
@endsection

@push('css')
    <style>
        .custom_disable {
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div id="success_message"></div>

            <div id="error_message"></div>

            <div class="card card-primary">
                <div class="card-header">@yield('page')</div>

                <div class="card-body">
                    <form action="" method="post" id="transaction_post">
                        @csrf

                        <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">

                        <div class="row">

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" readonly value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                                           name="date" id="date" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="seller_id">Seller Name</label>
                                    <select name="seller_id" id="seller_id" class="form-control seller_id custom_disable"
                                            required>
                                        @forelse ($sellers as $seller)
                                            @if ($order->seller_id == $seller->id)
                                                <option value="{{ $seller->id }}" selected>{{ $seller->name }}</option>
                                            @endif
                                        @empty
                                            <option value="">Data Not Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="payment_type">Payment Type</label>
                                    <select name="payment_type" onchange="getBank(this)" id="payment_type"
                                            class="form-control" required>
                                        <option value="">Select Payment Type</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank">Bank</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div id="banks" class="form-group">
                                    <label for="bank_id">Bank</label>
                                    <select name="bank_id" id="bank_id" class="form-control custom_disable">
                                        <option value="">Select Bank</option>
                                        @forelse ($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @empty
                                            <option value="">Data Not Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <label for="">Total Out Standing</label>
                                <input type="text" value="{{ $order->total_out_standing }}" name="total_out_standing"
                                       id="total_out_standing" class="form-control" readonly>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="payable_amount" class="control-label">Payable Amount</label>
                                    <input type="text" value="{{ $order->total_out_standing }}" readonly required
                                           name="payable_amount" id="payable_amount" class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="receive_amount" class="control-label">Receive Amount</label>
                                    <input type="number" required max="{{ $order->order_payable_amount }}" name="receive_amount" min="1" id="receive_amount"
                                           class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="due_amount">Due Amount</label>
                                    <input type="text" value="{{ $order->total_out_standing }}" readonly
                                           name="due_amount" id="due_amount" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <a href="{{ route('transaction') }}" class="btn btn-warning">Back</a>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- /.card-header -->
            <div class="card-body table-responsive">
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
                        <th>Total</th>
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
                        <th>Total</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
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
                    url: '{!!  route('order.transaction.getData') !!}',
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
        // get banks
        function getBank(event) {
            console.log($(event).val())
            if ($(event).val() == 'bank') {
                $("#bank_id").removeClass('custom_disable')
            } else {
                $("#bank_id").addClass('custom_disable')
            }
        }

        $("#receive_amount").bind('keyup mouseup', function () {

            var receive_amount = $("#receive_amount").val();
            var total_out_standing = $("#total_out_standing").val();
            var total_due = total_out_standing - receive_amount;

            $("#due_amount").val(total_due);
        })
    </script>
    <script>
        $(document).ready(function () {

            $("#transaction_post").on("submit", function (e) {
                e.preventDefault();

                var formData = new FormData($("#transaction_post").get(0));

                $.ajax({
                    url: "{{ route('transaction.store') }}",
                    type: "post",
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {

                        if (data.message) {
                            toastr.options =
                                {
                                    "closeButton": true,
                                    "progressBar": true
                                };
                            toastr.success(data.message);
                        }

                        $("form").trigger("reset");

                        $('.form-group').find('.valids').hide();
                    },

                    error: function (err) {

                        if (err.status === 422) {
                            $.each(err.responseJSON.errors, function (i, error) {
                                var el = $(document).find('[name="' + i + '"]');
                                el.after($('<span class="valids" style="color: red;">' + error + '</span>'));
                            });
                        }

                        if (err.status === 500) {
                            $('#error_message').html('<div class="alert alert-error">\n' +
                                '<button class="close" data-dismiss="alert">×</button>\n' +
                                '<strong>Error! ' + err.responseJSON.error + '</strong>' +
                                '</div>');
                        }
                    }
                });
            })
        })
    </script>
@endpush
