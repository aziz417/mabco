@extends('layouts.admin.master')

@section('page')
    Stock Edit
@endsection

@push('css')

@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div id="success_message"></div>

            <div id="error_message"></div>

            <div class="card card-primary">
                <div class="card-header">@yield('page')</div>

                <div class="card-body">
                    <form action="" method="post" id="stock_edit">
                        @method('PUT')
                        @csrf

                        <input type="hidden" name="" id="stock_id" value="{{ $stock->id }}">

                        <div class="form-group row">
                            <label for="product_id">Product Name</label>
                            <select onchange="getCategoryBrand(this)" name="product_id" id="product_id" class="form-control" required>
                                <option value="">Select Product</option>
                                @forelse ($products as $product)
                                    <option {{ $stock->product_id == $product->id ? 'selected' : ''}} value="{{ $product->id }}">{{ $product->name }}</option>
                                @empty
                                    <option value="">Data Not Found</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-group row">
                            <label for="category_id">Category Name</label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @forelse ($categories as $category)
                                    <option {{ $stock->category_id == $category->id ? 'selected' : ''}} value="{{ $category->id }}">{{ $category->name }}</option>
                                @empty
                                    <option value="">Data Not Found</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-group row">
                            <label for="brand_id">Brand Name</label>
                            <select name="brand_id" id="brand_id" class="form-control" required>
                                <option value="">Select Brand</option>
                                @forelse ($brands as $brand)
                                    <option {{ $stock->brand_id == $brand->id ? 'selected' : ''}} value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @empty
                                    <option value="">Data Not Found</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-group row">
                            <label for="unit_id">Unit Name</label>
                            <select name="unit_id" id="unit_id" class="form-control" required>
                                <option value="">Select Unit</option>
                                @forelse ($units as $unit)
                                    <option {{ $stock->unit_id == $unit->id ? 'selected' : ''}} value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @empty
                                    <option value="">Data Not Found</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-group row">
                            <label for="quantity" class="control-label">Quantity
                                <span style="font-size: 12px; color: #d39e00"><label for="reduce_quantity">Do you want to reduce the quantity?</label>
                                    <input id="reduce_quantity" onchange="reduceQuantity({{ $stock->quantity }})" type="checkbox" name="reduce_quantity" value="1">
                                </span>
                            </label>
                            <input type="number" min="{{ $stock->quantity }}" max="" value="{{ $stock->quantity }}" name="quantity" id="quantity" class="form-control">
                        </div>

                        <div class="form-group">
                            <a href="{{ route('stock') }}" class="btn btn-warning">Back</a>
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

        //
        function reduceQuantity(quantity){
            if($("#reduce_quantity").prop('checked') === true){
                $("#quantity").attr({
                    "max" : quantity,        // substitute your own
                    "min" : 1          // values (or variables) here
                });
            }else{
                $("#quantity").attr({
                    "max" : "",        // substitute your own
                    "min" : quantity          // values (or variables) here
                });
            }
        }
        // get product wise category and brand
        function getCategoryBrand(event){

            let product_id = $(event).val()

            $.get("{{ route('stock.product.category_brand') }}", { id: product_id }, function(response){
                if(response){
                    $("#category_id").html(`<option selected value="${response['category_brand'].category_id}">${response['category_brand'].category_name}</option>`);
                    $("#brand_id").html(`<option selected value="${response['category_brand'].brand_id}">${response['category_brand'].brand_name}</option>`);
                    $("#unit_id").html(`<option selected value="${response['category_brand'].unit_id}">${response['category_brand'].unit_name}</option>`);
                }
            });
        }

        $(document).ready(function () {

            $("#stock_edit").on("submit",function (e) {
                e.preventDefault();

                var id = $("#stock_id").val();

                var formData = new FormData( $("#stock_edit").get(0));

                $.ajax({
                    url : "{{ route('stock.update','') }}/"+id,
                    type: "post",
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {

                        if (data.message){
                            toastr.options =
                                {
                                    "closeButton" : true,
                                    "progressBar" : true
                                };
                            toastr.success(data.message);
                        }

                        $("form").trigger("reset");
                        setTimeout(function (){
                            location.reload();
                        }, 2000)

                        $('.form-group').find('.valids').hide();
                    },

                    error: function (err) {

                        if (err.status === 422) {
                            $.each(err.responseJSON.errors, function (i, error) {
                                var el = $(document).find('[name="' + i + '"]');

                                el.nextAll().remove();
                                el.after($('<span class="valids" style="color: red;">' + error + '</span>'));

                            });
                        }


                        if (err.status === 500)
                        {
                            $('#error_message').html('<div class="alert alert-error">\n' +
                                '<button class="close" data-dismiss="alert">×</button>\n' +
                                '<strong>Error! '+err.responseJSON.error+'</strong>' +
                                '</div>');
                        }
                    }
                });
            })
        })
    </script>
@endpush
