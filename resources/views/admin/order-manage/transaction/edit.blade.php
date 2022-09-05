@extends('layouts.admin.master')

@section('page')
    Transaction Approve
@endsection

@push('css')
    <style>
        .custom_disable{
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
                    <form action="" method="post" id="transaction_edit">
                        @method('PUT')
                        @csrf

                        <input type="hidden" name="" id="transaction_id" value="{{ $transaction->id }}">

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="seller_id">Seller Name</label>
                                    <select name="seller_id" id="seller_id" onchange="getOrders(this)" class="form-control seller_id" required>
                                        <option value="">Select Seller</option>
                                        @forelse ($sellers as $seller)
                                            <option {{ $transaction->seller_id == $seller->id ? 'selected' : '' }} value="{{ $seller->id }}">{{ $seller->name }}</option>
                                        @empty
                                            <option value="">Data Not Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" value="{{ $transaction->date }}" name="date" id="date" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="order_id">Order</label>
                                    <select name="order_id" id="order_id" onchange="getOrderTotalPrice(this)" class="form-control order_id" required>
                                        <option value="">Select Order</option>
                                        @forelse ($orders as $order)
                                            <option {{ $transaction->order_id == $order->id ? 'selected' : '' }} value="{{ $order->id }}">{{ $order->order_code }}</option>
                                        @empty
                                            <option value="">Data Not Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="bill" class="control-label">Amount Of Total Bill</label>
                                    <input type="number" value="{{ $transaction->bill }}"  required name="bill" id="bill" class="form-control custom_disable">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="total_bill" class="control-label">Total Bill</label>
                                    <input type="number" value="{{ $transaction->total_bill }}"  required name="total_bill" id="total_bill" class="form-control custom_disable">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="commission_type" class="control-label">Commission Type</label>
                                    <input type="text" value="{{ $transaction->commission_type }}"  required name="commission_type" id="commission_type" class="form-control custom_disable">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="commission_value" class="control-label">Commission Value</label>
                                    <input type="number" value="{{ $transaction->commission_value }}"  required name="commission_value" id="commission_value" class="form-control custom_disable">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="total_discount" class="control-label">Total Discount</label>
                                    <input type="number" value="{{ $transaction->total_discount }}"  required name="total_discount" id="total_discount" class="form-control custom_disable">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="payment_type">Payment Type</label>
                                    <select name="payment_type" onchange="getBank(this)" id="payment_type" class="form-control" required>
                                        <option value="">Select Payment Type</option>
                                        <option {{ $transaction->payment_type == 'cash' ? 'selected' : '' }} value="cash">Cash</option>
                                        <option {{ $transaction->payment_type == 'bank' ? 'selected' : '' }}  value="bank">Bank</option>
                                    </select>
                                </div>

                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="payable_amount" class="control-label">Payable Amount</label>
                                    <input type="number" value="{{ $transaction->payable_amount }}" required name="payable_amount" id="payable_amount" class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="due_amount">Due Amount</label>
                                    <input type="number" value="{{ $transaction->due_amount }}" name="due_amount" id="due_amount" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div id="banks" class="form-group">
                                    <label for="bank_id">Bank</label>
                                    <select name="bank_id" id="bank_id" class="form-control custom_disable">
                                        <option value="">Select Bank</option>
                                        @forelse ($banks as $bank)
                                            <option {{ $transaction->bank_id == $bank->id ? 'selected' : '' }} value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @empty
                                            <option value="">Data Not Found</option>
                                        @endforelse
                                    </select>
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
        </div>
    </div>
@endsection

@push('js')
    <script>
        // calculate due amount
        $("#payable_amount").bind('keyup mouseup', function (){
            var total = $("#total_bill").val();
            var payable_amount = $("#payable_amount").val();
            var due_amount = total - payable_amount;
            $("#due_amount").val(due_amount);
        })

        // get overview wise order
        function getOrders(event){
            var options = `<option selected disabled value="">Select Order</option>`;
            $(".order_id").html(options)

            let seller_id = $(event).val()

            $.get("{{ route('transaction.seller.orders') }}", { id: seller_id }, function(response){
                if(response){
                    for(var i = 0;  i<response.length; i++){
                        options+= `<option style="text-transform: capitalize;" value="${response[i].id}">${response[i].order_code}</option>`
                    }
                    $(".order_id").html(options)
                }
            });
        }

        // get banks
        function getBank(event){
            console.log($(event).val())
            if($(event).val() == 'bank'){
                $("#bank_id").removeClass('custom_disable')
            }else {
                $("#bank_id").addClass('custom_disable')
            }
        }

        // get order total price
        function getOrderTotalPrice(event){
            let order_id = $(event).val()
            $.get("{{ route('transaction.order.total') }}", { id: order_id }, function(response){
                var totalPrice =  response[0]
                if(response){
                    $("#bill").val(totalPrice.bill)
                    $("#total_bill").val(totalPrice.total_bill)
                    if($.isNumeric(totalPrice.commission_value)){
                        $("#commission_type").val(totalPrice.commission_type)
                        $("#commission_value").val(totalPrice.commission_value)
                        $("#total_discount").val(totalPrice.total_discount)
                    }
                }
            });
        }
    </script>

    <script>
        $(document).ready(function () {

            $("#transaction_edit").on("submit", function (e) {
                e.preventDefault();

                var id = $("#transaction_id").val();

                var formData = new FormData($("#transaction_edit").get(0));

                $.ajax({
                    url: "{{ route('transaction.update','') }}/" + id,
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
                            setTimeout(function() {
                               window.location = "/transaction"
                            }, 2000);
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
