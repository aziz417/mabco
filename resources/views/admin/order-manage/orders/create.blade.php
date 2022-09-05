@extends('layouts.admin.master')

@section('page')
    Order Create
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
        .select2-container--default .select2-selection--single {
            border: 1px solid #ccc;
            border-radius: 4px;
            height: 39px !important;
        }

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

            <form action="" method="post" id="order_post">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="date">Date</label>
                                        <input type="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                                               name="date" class="form-control custom_disabled">
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="seller_id">Seller Name</label>
                                            <select name="seller_id" id="seller_id" onchange="retailerSelect(this)"
                                                    class="form-control" required>
                                                <option value="">Select Seller</option>
                                                @forelse ($sellers as $seller)
                                                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                                @empty
                                                    <option value="">Data Not Found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="retailer_id">Retailer Name</label>
                                            <select name="retailer_id" id="retailer" onchange="oldOutStanding(this)"
                                                    class="form-control" required>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Old Out standing</label>
                                        <input type="text" name="old_out_standing" id="old_out_standing" readonly
                                               class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="address">Address <span style="font-size: 12px"> (You can change previous address)</span></label>
                                        <input type="text" name="address" id="address" class="form-control">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="d-none" id="orderForm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div id="dataAdd">

                                                <div class="form-row">
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="brand_id_1">Brand Name</label>
                                                            <select name="brand_id[]" required
                                                                    onchange="selectProducts(this, 1)" id="brand_id_1"
                                                                    class="form-control">
                                                                <option value="">Select Brand</option>
                                                                @forelse ($brands as $brand)
                                                                    <option
                                                                        value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                                @empty
                                                                    <option value="">No Data Found</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="category_id_1">C.Name</label>
                                                            <select name="category_id[]" required id="category_id_1"
                                                                    onchange="selectProducts(this, 1)"
                                                                    class="form-control">
                                                                <option value="">Select Category</option>
                                                                @forelse ($categories as $category)
                                                                    <option
                                                                        value="{{ $category->id }}">{{ $category->name }}</option>
                                                                @empty
                                                                    <option value="">No Data Found</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="product_id_1">Product Name</label>
                                                            <select onchange="product(this, 1)" name="product_id[]"
                                                                    required id="product_id_1"
                                                                    class="form-control product_id">
                                                                <option value="">Select Product</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="unit_id_1">Unit Name</label>
                                                            <select name="unit_id[]" required id="unit_id_1"
                                                                    class="form-control unit_id custom_disabled">

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="stock_1">Stock</label>
                                                            <input type="number" id="stock_1"
                                                                   class="form-control custom_disabled">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="price_1">P. Price</label>
                                                            <input type="text" name="product_price[]" required
                                                                   id="price_1" class="form-control custom_disabled">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="quantity_1"
                                                                   class="control-label">Quantity</label>
                                                            <input type="number" min="1" name="quantity[]"
                                                                   onchange="calculation(this, 1)" required
                                                                   id="quantity_1" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="total_price_1">Total Price</label>
                                                            <input type="text" name="total_price[]" required
                                                                   id="total_price_1"
                                                                   class="form-control sub-total custom_disabled">
                                                        </div>
                                                    </div>

                                                    <div class="newAddMore" id="newAddMore"></div>
                                                </div>

                                        </div>
                                    </div>
                                    <div class="form-group col-md-3" style="margin-top: 35px">
                                        <a class="btn btn-sm btn-primary" id="addRow" onclick="addNewItem()"><i
                                                class="fa fa-plus"></i></a>
                                        <a class="btn btn-sm btn-danger" id="deleteRow" onclick="removeItem(this)"><i
                                                class="fa fa-minus"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="unit_id">Amount</label>
                                <input type="text" name="amount" id="amount" class="form-control custom_disabled">
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
                                       id="commission_value" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="total_discount">Total Discount</label>
                                <input type="text" name="total_discount" id="total_discount"
                                       class="form-control custom_disabled">
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="bill">Bill</label>
                                <input type="text" name="bill" id="bill" class="form-control custom_disabled">
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Total Out Standing</label>
                                <input type="text" value="00" name="total_out_standing" id="total_out_standing" readonly
                                       class="form-control custom_disabled">
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

        let rowCount = 2;

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
            <label for="category_id_${rowCount}">C.Name</label>
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
            <input type="text" name="product_price[]" required id="price_${rowCount}" class="form-control custom_disabled">
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function () {
            $('.products').select2();
        });
    </script>

    <script>
        let total_bill = 0;
        let in_total_out_standing = 0;
        let in_total_bill = 0;

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

            $("#commission_type").on("click", function () {
                var commission_type = $("#commission_type").val();
                if (commission_type) {
                    $("#commission_value").prop('readonly', false)
                } else {
                    $("#commission_value").prop('readonly', true )
                }
            });

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
            in_total_out_standing = parseFloat(total_bill) + parseFloat(old_out_standing)

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
                in_total_out_standing = parseFloat(in_total_bill) + parseFloat(old_out_standing);
            }

            $("#total_out_standing").val(in_total_out_standing);
            $("#bill").val(parseFloat(in_total_bill));
        }
    </script>

    <script>
        $(document).ready(function () {

            $("#order_post").on("submit", function (e) {
                e.preventDefault();

                if ($(".valids").length > 0) {
                    alert('quantity will be less or equal to the stock')
                    return;
                }

                var formData = new FormData($("#order_post").get(0));

                $.ajax({
                    url: "{{ route('order.store') }}",
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
@endpush
