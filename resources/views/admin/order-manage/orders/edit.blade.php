@extends('layouts.admin.master')

@section('page')
    Order Edit
@endsection

@push('css')
    <style>
        .custom_disabled {
            pointer-events: none;
        }
        .old-product-delete-btn{
            color: #fff !important;
            background: red;
            border-radius:50%;
            padding: 2px 9px;
            border: none;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div id="success_message"></div>

            <div id="error_message"></div>

            <form action="" method="post" id="order_edit">
                @method('PUT')
                @csrf

                <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="date">Date</label>
                                        <input type="date" value="{{ $order->date }}" name="date"
                                               class="form-control custom_disabled">
                                    </div>


                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="seller_id">Seller Name</label>
                                            <select name="seller_id" onchange="retailerSelect(this)" id="seller_id" class="form-control">
                                                <option value="">Select Seller</option>
                                                @forelse ($sellers as $seller)
                                                    <option value="{{ $seller->id }}"
                                                        {{ $order->seller_id == $seller->id ? 'selected' : '' }}>
                                                        {{ $seller->name }}
                                                    </option>
                                                @empty
                                                    <option value="">Data Not Found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="retailer_id">Retailer Name</label>
                                            <select name="retailer_id" id="retailer" onchange="oldOutStanding(this)" class="form-control">
                                                <option value="">Select Retailer</option>
                                                @forelse ($retailers as $retailer)
                                                    <option value="{{ $retailer->id }}"
                                                        {{ $order->retailer_id == $retailer->id ? 'selected' : '' }}>
                                                        {{ $retailer->name }}
                                                    </option>
                                                @empty
                                                    <option value="">Data Not Found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Old Out standing</label>
                                        <input type="text" value="{{ $old_out_standing }}"
                                               name="old_out_standing" id="old_out_standing" readonly
                                               class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="address">Address <span style="font-size: 12px"> (You can change previous address)</span></label>
                                        <input type="text" value="{{ $order->address }}" name="address" id="address" class="form-control">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-row">
                                        @foreach ($order_details as $key => $item)

                                            @php
                                                $key++;
                                            @endphp
                                            <div class="old-products row" id="old_item_row_{{ $key }}">
                                                <input name="old_item_ids[]" value="{{ $item->id }}" type="hidden">
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <button class="old-product-delete-btn" onclick="oldItemDelete({{ $item->id }}, {{ $key }})" type="button">X</button>
                                                        <label for="brand_id_{{ $key }}">Brand Name</label>
                                                        <select name="brand_id[]" required
                                                                onchange="selectProducts(this, {{ $key }})"
                                                                id="brand_id_{{ $key }}" class="form-control">
                                                            <option value="">Select Brand</option>
                                                            @forelse ($brands as $brand)
                                                                <option
                                                                    {{ $item->brand_id == $brand->id ? 'selected' : '' }} value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                            @empty
                                                                <option value="">No Data Found</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="category_id_{{ $key }}">Category Name</label>
                                                        <select name="category_id[]" required id="category_id_{{ $key }}"
                                                                onchange="selectProducts(this, {{ $key }})"
                                                                class="form-control">
                                                            <option value="">Select Category</option>
                                                            @forelse ($categories as $category)
                                                                <option
                                                                    {{ $item->category_id == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @empty
                                                                <option value="">No Data Found</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="product_id_{{ $key }}">Product Name</label>
                                                        <select onchange="product(this, {{ $key }})" name="product_id[]"
                                                                required id="product_id_{{ $key }}"
                                                                class="form-control product_id">
                                                            <option value="">Select Product</option>
                                                            @forelse ($products as $product)
                                                                <option
                                                                    {{ $item->product_id == $product->id ? 'selected' : '' }} value="{{ $product->id }}">{{ $product->name }}</option>
                                                            @empty
                                                                <option value="">No Data Found</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-1">
                                                    <div class="form-group">
                                                        <label for="unit_id_{{ $key }}">Unit Name</label>
                                                        <select name="unit_id[]" required id="unit_id_{{ $key }}"
                                                                class="form-control unit_id custom_disabled">
                                                            <option value="">Select Unit</option>
                                                            @forelse ($units as $unit)
                                                                @if($item->unit_id == $unit->id)
                                                                    <option
                                                                        {{ $item->unit_id == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                                @endif
                                                            @empty
                                                                <option value="">No Data Found</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-1">
                                                    <div class="form-group">
                                                        <label for="stock_{{ $key }}">Stock</label>
                                                        <input type="number" id="stock_{{ $key }}" value="{{ $item->stock }}"
                                                               class="form-control custom_disabled">
                                                    </div>
                                                </div>

                                                <div class="col-sm-1">
                                                    <div class="form-group">
                                                        <label for="price_{{ $key }}">P. Price</label>
                                                        <input value="{{ $item->price }}" type="text"
                                                               name="product_price[]" required id="price_{{ $key }}"
                                                               class="form-control custom_disabled">
                                                    </div>
                                                </div>

                                                <div class="col-sm-1">
                                                    <div class="form-group">
                                                        <label for="quantity_{{ $key }}"
                                                               class="control-label">Quantity</label>
                                                        <input value="{{ $item->quantity }}" type="number" min="1"
                                                               name="quantity[]"
                                                               onchange="calculation(this, {{ $key }})" required
                                                               id="quantity_{{ $key }}" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="total_price_{{ $key }}">Total Price</label>
                                                        <input value="{{ $item->total_price }}" type="text"
                                                               name="total_price[]" required
                                                               id="total_price_{{ $key }}"
                                                               class="form-control sub-total custom_disabled">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <input name="deleteAbleIdes" type="hidden" id="deleteAbleIdes">

                                        <div class="newAddMore" id="newAddMore"></div>
                                    </div>

                                </div>
                                <div class="form-group col-md-3" style="margin-top: 35px">
                                    <a class="btn btn-sm btn-primary" onclick="addNewItem()"><i class="fa fa-plus"></i></a>
                                    <a class="btn btn-sm btn-danger" onclick="removeItem(this)"><i class="fa fa-minus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="unit_id">Amount</label>
                                            <input type="text" value="{{ $order->bill_without_discount }}" name="amount" id="amount"
                                                   class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="commission_type">Commission Type</label>
                                            <select onchange="commissionCalculat()" name="commission_type" id="commission_type" class="form-control">
                                                <option value="">Select Payment Type</option>
                                                <option value="percentage"
                                                        @if($order->commission_type == 'percentage') selected @endif>
                                                    Percentage (%)
                                                </option>
                                                <option value="taka"
                                                        @if($order->commission_type == 'taka') selected @endif>Taka
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="commission_value">Commission Value</label>
                                            <input onchange="commissionCalculat()" type="text" value="{{ $order->commission_value }}"
                                                   name="commission_value" id="commission_value" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="total_discount">Total Discount</label>
                                            <input type="text" value="{{ $order->total_discount }}"
                                                   name="total_discount" id="total_discount" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="bill">Bill</label>
                                            <input data-old-bill="{{ $order->bill }}" type="text" value="{{ $order->bill }}" name="bill" id="bill"
                                                   class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Total Out Standing</label>
                                            <input type="text" value="{{ $old_out_standing == 0 ? $order->bill : $old_out_standing }}"
                                                   name="total_out_standing" readonly id="total_out_standing"
                                                   class="form-control">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <a href="{{ route('order') }}" class="btn btn-warning">Back</a>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function retailerSelect(e) {
            var seller_id = $(e).val();

            $.get("{{ route('get.retailer') }}", {seller_id: seller_id}, function (response) {
                var options = `<option selected disabled value="">Select Retailer</option>`;
                if (response) {
                    for (var i = 0; i < response.length; i++) {
                        options += `<option style="text-transform: capitalize;" value="${response[i]['id']}">${response[i]['name']}</option>`
                    }
                    $("#retailer").html(options)
                }
            });
        }

        // get retailer old out standing
        function oldOutStanding(e) {
            var retailer_id = $(e).val();
            $.get("{{ route('order.get.old_out_standing') }}", {retailer_id: retailer_id}, function (response) {
                if (response) {
                    $("#orderForm").removeClass('d-none')
                    $("#old_out_standing").val(response['total_out_standing'])
                    $("#address").val(response['address'])
                }
            })
        }

        let rowCount = {{ count($order_details) }} + 1;

        function addNewItem() {
            $("#newAddMore").append(`
            <div class="row perItem addMoreAttributeSection">
            <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="brand_id_${rowCount}">Brand Name</label>
                                                        <select name="brand_id[]" required onchange="selectProducts(this, ${rowCount})" id="brand_id_${rowCount}" class="form-control">
                                                            <option value="">Select Brand</option>
                                                            @forelse ($brands as $brand)
            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                            @empty
            <option value="">No Data Found</option>
@endforelse
            </select>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="category_id_${rowCount}">Category Name</label>
            <select name="category_id[]" required id="category_id_${rowCount}" onchange="selectProducts(this, ${rowCount})" class="form-control">
                <option value="">Select Category</option>
@forelse ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @empty
            <option value="">No Data Found</option>
@endforelse
            </select>
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group">
            <label for="product_id_${rowCount}">Product Name</label>
            <select onchange="product(this, ${rowCount})" name="product_id[]" required id="product_id_${rowCount}" class="form-control product_id">
                <option value="">Select Product</option>

            </select>
        </div>
    </div>

    <div class="col-sm-1">
        <div class="form-group">
            <label for="unit_id_${rowCount}">Unit Name</label>
            <select name="unit_id[]" required id="unit_id_${rowCount}" class="form-control unit_id custom_disabled">

            </select>
        </div>
    </div>
       <div class="col-sm-1">
        <div class="form-group">
            <label for="stock_${rowCount}">Stock</label>
            <input type="number" id="stock_${rowCount}" class="form-control custom_disabled">
        </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
            <label for="price_${rowCount}">P. Price</label>
            <input type="number" name="product_price[]" required id="price_${rowCount}" class="form-control custom_disabled">
        </div>
    </div>


    <div class="col-sm-1">
        <div class="form-group">
            <label for="quantity_${rowCount}" class="control-label">Quantity</label>
            <input type="number" min="1" onchange="calculation(this, ${rowCount})" name="quantity[]" required id="quantity_${rowCount}" class="form-control">
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group">
            <label for="total_price_${rowCount}">Total Price</label>
            <input type="text" name="total_price[]" id="total_price_${rowCount}" required class="form-control sub-total custom_disabled">
        </div>
    </div>
`)
            rowCount++;
        }

        // last item delete
        function removeItem() {
            $(".addMoreAttributeSection:last").remove()
            totalAmountCalculation();
        }

        function selectProducts(e, rowId) {
            var options = `<option selected disabled value="">Select Product</option>`;

            $("#unit_id_" + rowId).html(`<option selected disabled value=""></option>`)
            $("#stock_" + rowId).val('')
            $("#price_" + rowId).val('')
            $("#quantity_" + rowId).val('')
            $("#total_price_" + rowId).val('')

            var category = $("#category_id_" + rowId).val()
            var brand = $("#brand_id_" + rowId).val()

            $.get("{{ route('brand.category.products') }}", {
                category_id: category,
                brand_id: brand
            }, function (response) {
                if (response) {

                    for (var i = 0; i < response.length; i++) {
                        options += `<option style="text-transform: capitalize;" value="${response[i]['id']}">${response[i]['name']}</option>`
                    }
                    $("#product_id_" + rowId).html(options)
                }
            });

            totalAmountCalculation();
        }

        // product wise unit stock price
        function product(e, rowId) {
            var product_id = $(e).val()
            $.get("{{ route('productUnitStockPrice') }}", {product_id: product_id}, function (response) {
                if (response) {
                    $("#unit_id_" + rowId).html(`<option value="${response['id']}">${response['name']}</option>`);
                    $("#stock_" + rowId).val(response['quantity'])
                    $("#price_" + rowId).val(response['price'])
                }
            });

            totalAmountCalculation();
        }

        let idArray = [];
        function oldItemDelete(itemId, rowId){

            swal({
                    title: "Are You Sure?",
                    text: "You will not be able to recover this record again",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, Delete It"
                },
                function(){
                    idArray.push(itemId)
                    localStorage.setItem("deleteAbleItemIds", idArray);
                    $("#old_item_row_"+rowId).remove()
                    totalAmountCalculation();
                });
        }
    </script>

    <script>
        let total_bill = 0;
        let in_total_out_standing = 0;
        let in_total_bill = 0;
        let will_be_subtracted = 0;
        let will_be_added = 0;

        function calculation(e, rowId) {
            var quantity = $(e).val()
            var stock = $("#stock_" + rowId).val();

            if (parseInt(stock) < parseInt(quantity) || parseInt(quantity) < 1) {
                $(e).closest(".form-group").find(".valids").remove()
                $(e).after($('<span class="valids" style="color: red;"> This quantity not valid</span>'))
            } else {
                $(e).closest(".form-group").find(".valids").remove()
            }

            var price = $("#price_" + rowId).val()
            var subTotal = price * quantity;
            $("#total_price_" + rowId).val(parseFloat(subTotal))

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
            var old_out_standing = $("#old_out_standing").val();

            in_total_bill = total_bill;

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
            }

            var old_in_total_bill = parseFloat($("#bill").data("old-bill"))

            // Old out standing calculate if the bill is more or less

            if (old_in_total_bill < in_total_bill){
                will_be_added = parseFloat(in_total_bill) - parseFloat(old_in_total_bill);
                if (will_be_added > 0){
                    in_total_out_standing = parseFloat(old_out_standing) + parseFloat(will_be_added)
                }
            }else{
                will_be_subtracted = parseFloat(old_in_total_bill) - parseFloat(in_total_bill)
                if(will_be_subtracted > 0){
                    in_total_out_standing = parseFloat(old_out_standing) - parseFloat(will_be_subtracted)
                }
            }
            if (parseFloat(old_out_standing) === 0 ){
                $("#total_out_standing").val(parseFloat(in_total_bill));
            }else{
                $("#total_out_standing").val(in_total_out_standing);
            }

            $("#bill").val(parseFloat(in_total_bill));
        }
    </script>

    <script>
        $(document).ready(function () {
            $("#order_edit").on("submit", function (e) {
                e.preventDefault();
                var id = $("#order_id").val();

                var oldIdes = localStorage.getItem("deleteAbleItemIds");
                $("#deleteAbleIdes").val(oldIdes)
                localStorage.removeItem("deleteAbleItemIds");

                var formData = new FormData($("#order_edit").get(0));
                $.ajax({
                    url: "{{ route('order.update','') }}/" + id,
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
                                window.location = "/order"
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
