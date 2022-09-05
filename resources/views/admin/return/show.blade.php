@extends('layouts.admin.master')

@section('page')
    Return Show
@endsection

@push('css')
    <style>
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

                <div class="card-body">
                    <div class="row" style="border-bottom: 2px solid #ccc">
                        <div class="col-md-3">
                            <label for="date">Date</label>
                            <p>{{ $return->date }}</p>
                        </div>
                        <div class="col-md-3">
                            <label for="type">Type</label>
                            <div class="form-group">
                                <p>{{ ucfirst($return->type) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="seller_id">Seller Name</label>
                                <p>{{ $seller->name }}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="retailer_id">Retailer Name</label>
                                <p>{{ $retailer->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                    @forelse($products as $key => $product)
                        <div class="form-row" style="border-bottom: 4px solid #ccc; padding-top: 20px">
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="order_code{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">Order
                                        Code</label>
                                    <p>{{ $product->order_code ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="brand_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">B.Name</label>
                                    <p>{{ $product->brand_name }}</p>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="category_{{ $key }}"
                                           class="{{ $key == 0 ? '' : 'd-none' }}">C.Name</label>
                                    <p>{{ $product->category_name }}</p>

                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="product_name_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">Product
                                        Name</label>
                                    <p>{{ $product->product_name }}</p>

                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="unit_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">P.Unit</label>
                                    <p>{{ $product->unit_name }}</p>

                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="price_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">P.
                                        Price</label>
                                    <p>{{ $product->product_price }}</p>
                                </div>
                            </div>

                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="order_quantity_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">O.Quantity</label>
                                    <p>{{ $product->quantity }}</p>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="return_reason_{{ $key }}"
                                           class="{{ $key == 0 ? '' : 'd-none' }}">Reason</label>
                                    <p>{{ $product->reason_title }}</p>

                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="return_quantity_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">R.Quantity</label>
                                    <p>{{ $product->return_quantity }}</p>
                                </div>
                            </div>

                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="total_price_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">Total
                                        Price</label>
                                    <p>{{ $product->return_quantity * $product->product_price }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <h2>No Product Found</h2>
                    @endforelse

                        <div class="mt-20" style="margin-top: 30px">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="unit_id">Amount</label>
                                        <p>{{ $return->total_amount }}</p>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="commission_type">Commission Type</label>
                                        <p>{{ $return->commission_type }}</p>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="commission_value">Commission Value</label>
                                        <p>{{ $return->commission_value }}</p>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="total_discount">Total Discount</label>
                                        <p>{{ $return->discount }}</p>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="return_amount">Return Amount</label>
                                        <p>{{ $return->return_amount }}</p>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="">Total Out Standing</label>
                                        <p>{{ $retailer->total_out_standing }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

@endsection

@push('js')

@endpush
