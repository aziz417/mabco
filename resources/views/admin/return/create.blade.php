@extends('layouts.admin.master')

@section('page')
    Return Create
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
                    <form action="{{ route('retailer_wise.products') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label for="date">Date</label>
                                <input type="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                                       name="date" class="form-control custom_disabled">
                            </div>
                            <div class="col-md-3">
                                <label for="type">Type</label>
                                <div class="form-group">
                                    <label for="return">Return</label>
                                    <input {{ isset($type) && $type == 'return' ? 'checked': '' }} type="radio" name="type" required value="return" class="">
                                    <label for="return">Damage</label>
                                    <input {{ isset($type) && $type == 'damage' ? 'checked': '' }} type="radio" name="type" required value="damage" class="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="seller_id">Seller Name</label>
                                    <select name="seller_id" id="seller_id" onchange="retailerSelect(this)"
                                            class="form-control" required>
                                        <option value="">Select Seller</option>
                                        @forelse ($sellers as $seller)
                                            <option {{ isset($type) && $seller->id == $seller_id ? 'selected': '' }} value="{{ $seller->id }}">{{ $seller->name }}</option>
                                        @empty
                                            <option value="">Data Not Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="retailer_id">Retailer Name</label>
                                    <select name="retailer_id" id="retailer" onchange="retailerOrderList(this)"
                                            class="form-control" required>
                                        @if(isset($type))
                                            @forelse ($retailers as $retailer)
                                                @if($retailer->id == $retailer_id)
                                                    <option {{ isset($type) && $retailer->id == $retailer_id ? 'selected': '' }} value="{{ $retailer_id }}">{{ $retailer->name }}</option>
                                                @endif
                                            @empty
                                                <option value="">Data Not Found</option>
                                            @endforelse
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                    <button type="submit" class="brn btn-success p-1" style="margin-top: 32px">Submit</button>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                    @if(isset($products))
                        <form action="" method="post" id="product_return">
                            @csrf
                            <input type="hidden" value="{{ $seller_id }}" name="seller_id">
                            <input type="hidden" value="{{ $retailer_id }}" name="retailer_id">
                            <input type="hidden" value="{{ $type }}" name="type">

                            @forelse($products as $key => $product)
                            <input type="hidden" name="order_id[]" value="{{ $product->order_id }}">
                            <div class="form-row">
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="order_code{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">Order Code</label>
                                        <input name="order_code[]" value="{{ $product->order_code }}" class="form-control custom_disabled" id="order_code{{ $key }}">
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="brand_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">B.Name</label>
                                        <select name="brand_id[]" class="form-control custom_disabled" id="brand_{{ $key }}">
                                            <option selected value="{{ $product->brand_id }}">{{ $product->brand_name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="category_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">C.Name</label>
                                        <select name="category_id[]" class="form-control custom_disabled" id="category_{{ $key }}">
                                            <option selected value="{{ $product->category_id }}">{{ $product->category_name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="product_name_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">Product Name</label>
                                        <select name="product_id[]" class="form-control custom_disabled" id="product_name_{{ $key }}">
                                            <option selected value="{{ $product->product_id }}">{{ $product->product_name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="unit_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">P.Unit</label>
                                        <select name="unit_id[]" class="form-control custom_disabled" id="unit_{{ $key }}">
                                            <option selected value="{{ $product->unit_id }}">{{ $product->unit_name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="price_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">P. Price</label>
                                        <input name="product_price[]" value="{{ $product->price }}" class="form-control custom_disabled" id="product_price_{{ $key }}">
                                    </div>
                                </div>

                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="order_quantity_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">O.Quantity</label>
                                        <input type="number" name="order_quantity[]" value="{{ $product->quantity }}" id="order_quantity_{{ $key }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="return_reason_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">Reason</label>
                                        <select name="product_return_reason_id[]" class="form-control " id="return_reason_{{ $key }}">
                                            <option value="0">Select Reason</option>
                                            @forelse($return_reasons as $return_reason)
                                                <option value="{{ $return_reason->id }}">{{ $return_reason->title }}</option>
                                            @empty
                                                <h2>No data</h2>
                                            @endforelse
                                        </select>

                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="return_quantity_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">R.Quantity</label>
                                        <input onchange="calculation(this, {{ $key }})" required type="number" min="0" max="{{ $product->quantity }}" name="return_quantity[]" id="return_quantity_{{ $key }}"
                                               class="form-control return-quantity">
                                    </div>
                                </div>

                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="total_price_{{ $key }}" class="{{ $key == 0 ? '' : 'd-none' }}">Total Price</label>
                                        <input type="number" id="total_price_{{ $key }}" data-old-sub-total="{{ $product->total_price }}" value="{{ $product->total_price }}" name="sub_total_price[]" required
                                               class="form-control sub-total custom_disabled">
                                    </div>
                                </div>
                            </div>
                            @empty
                                <h2>No Product Found</h2>
                            @endforelse

                            <div class="mt-20">
                                <h2 class="text-center"><strong>Calculation seller</strong></h2>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="unit_id">Amount</label>
                                            <input type="text" name="total_amount" id="amount" value="{{ $total_sub_total }}" class="form-control custom_disabled">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="commission_type">Commission Type</label>
                                            <select name="commission_type" onchange="commissionCalculat()" id="commission_type"
                                                    class="form-control">
                                                <option value="">Select Payment Type</option>
                                                <option value="percentage">Percentage (%)</option>
                                                <option value="taka">Taka</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="commission_value">Commission Value</label>
                                            <input type="text" onchange="commissionCalculat()" name="commission_value"
                                                   id="commission_value" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="total_discount">Total Discount</label>
                                            <input type="text" name="discount" id="total_discount"
                                                   class="form-control custom_disabled">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="return_amount">Return Amount</label>
                                            <input type="text" data-old-bill="{{ $total_sub_total }}" name="return_amount" id="bill" value="" class="form-control custom_disabled">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Total Out Standing</label>
                                            <input data-old-out-standing="{{ $total_out_standing }}" type="text" value="{{ $total_out_standing }}" name="total_out_standing" id="total_out_standing" readonly
                                                   class="form-control custom_disabled">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <a href="{{ route('return') }}" class="btn btn-warning">Back</a>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        let total_bill = 0;
        let in_total_out_standing = 0;
        let in_total_bill = 0;
        let subtraction = 0;

        function calculation(e, rowId) {
            var return_quantity = $(e).val()
            if (return_quantity > 0){
                $("#return_reason_"+rowId).prop('required', true)
                $(e).prop('required', true)
            }else{
                $("#return_reason_"+rowId).prop('required', false)
                $(e).prop('required', false)
            }
            $('.return-quantity').each(function (index, element) {
                var rQuantity = parseFloat($(element).val());
                if (rQuantity > 0) {
                    $(element).prop('required', true)
                }else{
                    $(element).prop('required', false)
                }
            });

            var order_quantity = $("#order_quantity_" + rowId).val();

            if (parseInt(order_quantity) < parseInt(return_quantity) || parseInt(return_quantity) < 0) {
                $(e).closest(".form-group").find(".valids").remove()
                $(e).after($('<span class="valids" style="color: red;"> This quantity not valid</span>'))
            } else {
                $(e).closest(".form-group").find(".valids").remove()
            }

            var price = parseFloat($("#product_price_" + rowId).val())
            var newSubTotal = parseFloat(price) * parseFloat(return_quantity);
            var oldSubTotal = $("#total_price_"+rowId).data('oldSubTotal')
            var subTotal = parseFloat(oldSubTotal) - parseFloat(newSubTotal);
            $("#total_price_" + rowId).val(subTotal)

            totalAmountCalculation();
        }

        function commissionCalculat() {
            if ($("#commission_type").val()) {
                $("#commission_value").prop('required', true)
            } else {
                $("#commission_value").prop('required', false)
            }
            totalAmountCalculation();
        }

        function totalAmountCalculation() {
            total_bill = 0;
            $('.sub-total').each(function (index, element) {
                var val = parseFloat($(element).val());
                if (!isNaN(val)) {
                    total_bill += val;
                }
            });


            $("#amount").val(parseFloat(total_bill));
            var old_out_standing = $("#total_out_standing").data('oldOutStanding');
            var out_standing = $("#total_out_standing").val();
            var oldBill = $("#bill").data("oldBill")
            subtraction = oldBill - total_bill;
            in_total_bill = total_bill;
            in_total_out_standing = old_out_standing - subtraction;

            var commission_type = $("#commission_type").val()

            if (commission_type) {
                let total_commission = 0;
                var commission_value = $("#commission_value").val();

                if (commission_type === 'percentage') {
                    total_commission = total_bill * commission_value / 100;
                } else {
                    total_commission = commission_value;
                }
                $("#total_discount").val(total_commission)
                in_total_bill = total_bill - total_commission;
                in_total_out_standing = in_total_out_standing - total_commission;
                subtraction = subtraction - total_commission;
            }

            $("#total_out_standing").val(in_total_out_standing);
            $("#bill").val(parseFloat(subtraction));
        }
    </script>

    <script>
        $(document).ready(function () {

            $("#product_return").on("submit", function (e) {
                e.preventDefault();

                if ($(".valids").length > 0) {
                    alert('quantity will be less or equal to the stock')
                    return;
                }

                var formData = new FormData($("#product_return").get(0));

                $.ajax({
                    url: "{{ route('return.store') }}",
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

                        setTimeout(function () {
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
    <script>
        function retailerSelect(e) {
            var seller_id = $(e).val();

            $.get("{{ route('return.retailer.list') }}", {seller_id: seller_id}, function (response) {
                var options = `<option selected disabled value="">Select Retailer</option>`;
                if (response) {
                    for (var i = 0; i < response.length; i++) {

                        options += `<option style="text-transform: capitalize;" value="${response[i]['id']}">${response[i]['name']}</option>`
                    }
                    $("#retailer").html(options)
                }
            });
        }
    </script>

    <script>
        function retailerOrderList(e) {
            var retailer_id = $(e).val();
            var seller_id = $("#seller_id").val();

            $.get('return/getData/' + seller_id + '/' + retailer_id, {
                retailer_id: retailer_id,
                seller_id: seller_id
            }, function (response) {
                if (response) {
                    console.log(response)
                }
            })
        }
    </script>
@endpush
