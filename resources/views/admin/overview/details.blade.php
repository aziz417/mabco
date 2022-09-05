@extends('layouts.admin.master')

@section('page')
    Sales Invoice Details
@endsection

@push('css')

@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-body">


                <div class="row" style="margin-bottom: 25px">
                    <div class="col-md-6 text-left">
                        <h5>Mabco Laboratories Ltd.</h5>
                        <span>Corporate Office : 73/H/B, Dhanmondi Central Road , Dhaka.</span><br>
                        <span>Distribute Office : 26/6, Bank Town, Savar, Dhaka.</span>
                    </div>
                    <div class="col-md-6 text-right">
                        <h5>Date: {{ Carbon\Carbon::now()->format('Y-m-d') }}</h5>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-striped table-hover">
                            <tr>
                                <td>Order No</td>
                                <td>:</td>
                                <td>{{ $create_order->id }}</td>
                            </tr>

                            <tr>
                                <td>Created By</td>
                                <td>:</td>
                                <td>{{ $create_order->user_name }}</td>
                            </tr>

                            <tr>
                                <td>Phone No</td>
                                <td>:</td>
                                <td>{{ $create_order->user_phone }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-bordered table-striped table-hover">
                            <tr>
                                <td>Retailer</td>
                                <td>:</td>
                                <td>{{ $seller->name }}</td>
                            </tr>

                            <tr>
                                <td>Phone No</td>
                                <td>:</td>
                                <td>{{ $seller->phone }}</td>
                            </tr>

                            <tr>
                                <td>Address</td>
                                <td>:</td>
                                <td>{{ $seller->address }}</td>
                            </tr>
                        </table>
                    </div>

                    <br><br>

                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order_details as $od)
                                <tr>
                                    <td>{{ $od->category_name }}</td>
                                    <td>{{ $od->product_name }}</td>
                                    <td>{{ $od->quantity }}</td>
                                    <td>{{ $od->unit_name }}</td>
                                    <td>{{ $od->product_price }}</td>
                                    <td>{{ $od->total_price }}</td>
                                </tr>
                                @empty

                                @endforelse

                            </tbody>
                        </table>
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-6"></div>

                    <div class="col-md-6">
                        <table class="table table-bordered table-striped table-hover text-right">
                            <tr>
                                <td>Old Out Standing</td>
                                <td>:</td>
                                <td>{{ $order->old_out_standing }}</td>
                            </tr>
                            <tr>
                                <td>Total Amount</td>
                                <td>:</td>
                                <td>{{ $order->bill }}</td>
                            </tr>
                            <tr>
                                <td>Discount</td>
                                <td>:</td>
                                <td>@if ($order->commission_type == 'percentage')
                                    %
                                    @else
                                    Tk
                                    @endif
                                    {{ $order->total_discount }}</td>
                            </tr>
                            <tr>
                                <td>Total Bill</td>
                                <td>:</td>
                                <td>{{ $order->bill }}</td>
                            </tr>
                            @if ($transaction->receive_amount)
                            <tr>
                                <td>Total Receive</td>
                                <td>:</td>
                                <td>{{ $transaction->receive_amount }}</td>
                            </tr>
                            @endif

                            <tr>
                                <td>Total Out Standing</td>
                                <td>:</td>
                                <td>{{ $order->total_out_standing - $transaction->receive_amount }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 80px">
            <div class="col-sm-2"></div>
            <div class="col-sm-3 text-center"><h4 style="border-top: 3px dotted">Received By</h4></div>
            <div class="col-sm-2"></div>
            <div class="col-sm-3 text-center"><h4 style="border-top: 3px dotted">Authorize</h4></div>
            <div class="col-sm-2"></div>
        </div>

        <div class="row" style="margin-bottom: 25px;margin-top:25px">
            <div class="col-md-12 text-center">
                <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')

@endpush
