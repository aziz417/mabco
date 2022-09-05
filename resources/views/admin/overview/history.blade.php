@extends('layouts.admin.master')

@section('page')
    {{ ucfirst($role) }}  History
@endsection

@push('css')
    <style>
        h4, h3{
            padding: 10px;
            background: #0e84b5;
            color: #fff!important;
            border-radius: 5px;
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

                <div class="row">
                    <div class="col-sm-7">
                        <div class="card-header">
                            <h6> {{ ucfirst($role) }} Information</h6>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <h6>Name:</h6>
                                    <h6>Phone:</h6>
                                    <h6>Email:</h6>
                                    <h6>Out Standing:</h6>
                                </div>
                                <div class="col-9">
                                    <h6>{{ $user->name }}</h6>
                                    <h6>{{ $user->phone }}</h6>
                                    <h6>{{ $user->email }}</h6>
                                    <h6>{{ $user->total_out_standing }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5"></div>
                </div>

                <div class="card-body table-responsive">
                    <h4>Transaction History</h4>
                    <div class="row" style="border-bottom: 2px solid #ccc">
                        <div class="col-4"><h6>Total Payable: {{ $transaction_total_payable }}</h6></div>
                        <div class="col-4"><h6>Total Receive: {{ $transaction_receive_amount }}</h6></div>
                        <div class="col-4"><h6>Total Due: {{ $transaction_total_payable-$transaction_receive_amount }}</h6></div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Date</th>
                            <th>Payment Type</th>
                            <th>Payable Amount</th>
                            <th>Receive Amount</th>
                            <th>Due</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($transactions as $key => $transaction)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $transaction->date }}</td>
{{--                            <td>{{ $transaction->order_code }}</td>--}}
                            <td>{{ $transaction->payment_type }}</td>
                            <td>{{ $transaction->payable_amount }}</td>
                            <td>{{ $transaction->receive_amount }}</td>
                            <td>{{ $transaction->due_amount }}</td>
                        </tr>
                        @empty
                            <h5 class="text-center">NO Transaction</h5>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-body table-responsive">
                    <h4>Return History</h4>
                    <div class="row" style="border-bottom: 2px solid #ccc">
                        <div class="col-3"><h6>Total Product: {{ $return_products }}</h6></div>
                        <div class="col-4"><h6>Total Discount Amount: {{ $total_return_discount_amount }}</h6></div>
                        <div class="col-5"><h6>Total Amount <span style="font-size: 10px">(Without Discount)</span>: {{ $total_return_amount }}</h6></div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Date</th>
                            <th>Product Name</th>
                            <th>Return Quantity</th>
                            <th>Return Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($returns as $key => $return)
                            <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $return->date }}</td>
                            <td>{{ $return->name }}</td>
                            <td>{{ $return->return_quantity }}</td>
                            <td>{{ $return->return_quantity*$return->price }}</td>
                        </tr>
                        @empty
                            <h5 class="text-center">NO Return</h5>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-body table-responsive">
                    <h4>Damage History</h4>
                    <div class="row" style="border-bottom: 2px solid #ccc">
                        <div class="col-3"><h6>Total Product: {{ $damage_products }}</h6></div>
                        <div class="col-4"><h6>Total Discount Amount: {{ $total_damage_discount_amount }}</h6></div>
                        <div class="col-5"><h6>Total Amount <span style="font-size: 10px">(Without Discount)</span>: {{ $total_damage_amount }}</h6></div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Date</th>
                            <th>Product Name</th>
                            <th>Return Quantity</th>
                            <th>Return Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($damages as $key => $damage)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $damage->date }}</td>
                                <td>{{ $damage->name }}</td>
                                <td>{{ $damage->return_quantity }}</td>
                                <td>{{ $damage->return_quantity*$damage->price }}</td>
                            </tr>
                        @empty
                            <h5 class="text-center">NO Damage</h5>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-body table-responsive">
                    <h4>Order History</h4>
                    <div class="row" style="border-bottom: 2px solid #ccc">
                        <div class="col-3"><h6>Total Bill: {{ $total_order_bill }}</h6></div>
                        <div class="col-4"><h6>Total Discount Amount: {{ $total_order_discount }}</h6></div>
                        <div class="col-5"><h6>Total Amount <span style="font-size: 10px">(Without Discount)</span>: {{ $total_order_bill-$total_order_discount }}</h6></div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Date</th>
                            <th>Order Code</th>
                            <th>Total Bill</th>
                            <th>Discount</th>
                            <th>Without Discount Bill</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $key => $order)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $order->date }}</td>
                                <td>{{ $order->order_code }}</td>
                                <td>{{ $order->bill }}</td>
                                <td>{{ $order->total_discount }}</td>
                                <td>{{ $order->bill_without_discount }}</td>
                                <td><a rel="{{ $order->id }}" rel1="order/invoice" href="javascript:" style='margin-right: 5px' class="btn btn-sm btn-primary order_invoice"><i class='fa fa-print'></i></a></td>
                            </tr>
                        @empty
                            <h5 class="text-center">NO Order History</h5>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-body table-responsive">
                    <h4>Product History</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Date</th>
                            <th>Order Code</th>
                            <th>Product Name</th>
                            <th>Brand Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($order_products as $key => $order_product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $order_product->date }}</td>
                                <td>{{ $order_product->order_code }}</td>
                                <td>{{ $order_product->product_name }}</td>
                                <td>{{ $order_product->brand_name }}</td>
                                <td>{{ $order_product->quantity }}</td>
                                <td>{{ $order_product->price }}</td>
                                <td>{{ $order_product->sub_total }}</td>
                            </tr>
                        @empty
                            <h5 class="text-center">NO Product History</h5>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <div id="invoicePrint" style="display: none"></div>

@endsection

@push('js')
    <script>
        $(document).on('click','.order_invoice', function(e){
            e.preventDefault();
            var id = $(this).attr('rel');
            $.get("{{ route('order.invoice') }}", { id: id }, function (response) {
                if (response) {

                    // console.log(response)

                    $("#invoicePrint").html(response)

                    var divToPrint = document.getElementById("invoicePrint");

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
