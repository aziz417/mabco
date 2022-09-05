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
            btransaction-radius:30px;
            box-shadow:0 0 0 2px #777, 0 0 4px #777;
            cursor:pointer;
            btransaction:4px solid transparent;
            overflow:hidden;
            transition:.4s;
        }
        .slider:before {
            position:absolute;
            content:"";
            width:100%;
            height:100%;
            background:#777;
            btransaction-radius:30px;
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

                            <div class="col-sm-2">
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
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="retailer_id">Retailer Name</label>
                                    <select name="retailer_id" id="retailer_id" class="form-control retailer_id custom_disable"
                                            required>
                                        @forelse ($retailers as $retailer)
                                            @if ($order->retailer_id == $retailer->id)
                                                <option value="{{ $retailer->id }}" selected>{{ $retailer->name }}</option>
                                            @endif
                                        @empty
                                            <option value="">Data Not Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
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
                                <input type="text" value="{{ $old_out_standing }}" name="total_out_standing"
                                       id="total_out_standing" class="form-control" readonly>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="payable_amount" class="control-label">Order Payable Amount</label>
                                    <input type="text" value="{{ $order->order_payable_amount }}" readonly required
                                           name="payable_amount" id="payable_amount" class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="receive_amount" class="control-label">Receive Amount</label>
                                    <input type="number" max="{{ $order->order_payable_amount }}" required name="receive_amount" min="1" id="receive_amount"
                                           class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="due_amount">Due Amount</label>
                                    <input type="text" value="{{ $old_out_standing }}" readonly
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
            <div class="card card-primary">
                <div class="order-preview" style="border: 1px solid #ddd; padding: 10px 20px">
                    <h2>Order Over View</h2>
                    <div class="row" >
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="due_amount">Order Code</label>
                                <p>{{ $order->order_code }}</p>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="due_amount">Old Out Standing</label>
                                <p>{{ $old_out_standing }}</p>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label for="due_amount">Bill</label>
                                <p>{{ $order->bill }}</p>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label for="due_amount">W.D.Bill</label>
                                <p>{{ $order->bill_without_discount }}</p>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label for="due_amount">C.Type</label>
                                <p>{{ $order->commission_type }}</p>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label for="due_amount">Discount</label>
                                <p>{{ $order->total_discount }}</p>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="due_amount">Date</label>
                                <p>{{ date('d-m-Y', strtotime($order->created_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- /.card-header -->
            <div class="card-body table-responsive">
                <table id="data-table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>#Sl NO</th>
                        <th>Payable Amount</th>
                        <th>Receive Amount</th>
                        <th>Payment Type</th>
                        <th>Bank Name</th>
                        <th>Due Amount</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>#Sl NO</th>
                        <th>Payable Amount</th>
                        <th>Receive Amount</th>
                        <th>Payment Type</th>
                        <th>Bank Name</th>
                        <th>Due Amount</th>
                        <th>Date</th>
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
                    url: '{!!  route('order.transaction.getData', [$order->id, $order->retailer_id]) !!}',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'payable_amount', name: 'payable_amount'},
                    {data: 'receive_amount', name: 'receive_amount'},
                    {data: 'payment_type', name: 'payment_type'},
                    {data: 'name', name: 'name'},
                    {data: 'due_amount', name: 'due_amount'},
                    {data: 'date', name: 'date'},
                ]
            });

        });
    </script>
    <script>
        // get banks
        function getBank(event) {
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

                        setTimeout(function (){
                           location.reload()
                        }, 2000)

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
