@extends('layouts.admin.master')

@section('page')
    Stock
@endsection

@push('css')
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
            border-radius:30px;
            box-shadow:0 0 0 2px #777, 0 0 4px #777;
            cursor:pointer;
            border:4px solid transparent;
            overflow:hidden;
            transition:.4s;
        }
        .slider:before {
            position:absolute;
            content:"";
            width:100%;
            height:100%;
            background:#777;
            border-radius:30px;
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
                <div class="card-header">@yield('page') Create</div>

                <div class="card-body">
                    <form action="" method="post" id="stock_post">
                        @csrf
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="product_id">Product Name</label>
                                        <select name="product_id[]" onchange="getCategoryBrand(this, 1)" id="product_id" class="form-control" required>
                                            <option value="">Select Product</option>
                                            @forelse ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @empty
                                                <option value="">Data Not Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="category_id_1">Category Name</label>
                                        <select name="category_id[]" id="category_id_1" class="form-control" required>
                                            <option value="">Select Category</option>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="brand_id_1">Brand Name</label>
                                        <select name="brand_id[]" id="brand_id_1" class="form-control" required>
                                            <option value="">Select Brand</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="unit_id_1">Unit Name</label>
                                        <select name="unit_id[]" id="unit_id_1" class="form-control" required>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="quantity" class="control-label">Quantity</label>
                                        <input type="number" name="quantity[]" id="quantity" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                        <div class="newAddMore" id="newAddMore"></div>


                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addNewItem()" id="addRow"><i class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)" id="deleteRow"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-success float-right">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@yield('page')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body  table-responsive">
                    <table id="data-table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Product Name</th>
                            <th>Category Name</th>
                            <th>Brand Name</th>
                            <th>Unit Name</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Product Name</th>
                            <th>Category Name</th>
                            <th>Brand Name</th>
                            <th>Unit Name</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // get product wise category and brand
        function getCategoryBrand(event, idName){
            $("#category_id_"+idName).html(`<option selected value="">Select Category</option>`)
            $("#brand_id_"+idName).html(`<option selected value="">Select Brand</option>`)
            $("#unit_id_"+idName).html(`<option selected value="">Select Unit</option>`)
            let product_id = $(event).val()

            $.get("{{ route('stock.product.category_brand') }}", { id: product_id }, function(response){
                if(response){
                    $("#category_id_"+idName).html(`<option selected value="${response['category_brand'].category_id}">${response['category_brand'].category_name}</option>`);
                    $("#brand_id_"+idName).html(`<option selected value="${response['category_brand'].brand_id}">${response['category_brand'].brand_name}</option>`);
                    $("#unit_id_"+idName).html(`<option selected value="${response['category_brand'].unit_id}">${response['category_brand'].unit_name}</option>`);
                }
            });
        }
    </script>
    <script>
        // stock new item add
        let rowCount = 2;
        function addNewItem(){
            $("#newAddMore").append(`
            <div class="addMoreAttributeSection">
                 <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="product_id_${rowCount}">Product Name</label>
                                        <select onchange="getCategoryBrand(this, ${rowCount})" name="product_id[]" id="product_id_${rowCount}" class="form-control" required>
                                            <option value="">Select Product</option>
                                            @forelse ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @empty
            <option value="">Data Not Found</option>
@endforelse
            </select>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label for="category_id_${rowCount}">Category Name</label>
            <select name="category_id[]" id="category_id_${rowCount}" class="form-control" required>
                <option value="">Select Category</option>

            </select>
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group">
            <label for="brand_id_${rowCount}">Brand Name</label>
            <select name="brand_id[]" id="brand_id_${rowCount}" class="form-control" required>
                <option value="">Select Brand</option>

            </select>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="unit_id_${rowCount}">Unit Name</label>
            <select name="unit_id[]" id="unit_id_${rowCount}" class="form-control" required>

            </select>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="quantity" class="control-label">Quantity</label>
            <input type="number" name="quantity[]" id="quantity" class="form-control" required>
        </div>
    </div>
</div>
</div>
`)
            rowCount++;
        }

        // stock item remove
        function removeItem(){
            $(".addMoreAttributeSection:last").remove()
        }
    </script>
    <script>
        $(document).ready(function () {

            $("#stock_post").on("submit",function (e) {
                e.preventDefault();

                if($("select:has(option[value='null']:selected)").val() == 'null') {
                    $(".valids").remove()
                    $("select:has(option[value='null']:selected)")
                        .after($('<span class="valids" style="color: red;">Please select valid data</span>'))

                    return;
                }


                var formData = new FormData( $("#stock_post").get(0));
                $.ajax({
                    url : "{{ route('stock.store') }}",
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


                        setTimeout(function() {
                            location.reload();
                        }, 3000);

                        $('#data-table').DataTable().ajax.reload(null, false);

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

    <script>
        $(document).ready(function(){

            $('#data-table').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                pagingType: "full_numbers",
                ajax: {
                    url: '{!!  route('stock.getData') !!}',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'product_name', name: 'product_name'},
                    {data: 'category_name', name: 'category_name'},
                    {data: 'brand_name', name: 'brand_name'},
                    {data: 'unit_name', name: 'unit_name'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'total_price', name: 'total_price'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });

        });
    </script>

    <script>
        $(document).on('click','.deleteRecord', function(e){
            e.preventDefault();
            var id = $(this).attr('rel');
            var deleteFunction = $(this).attr('rel1');
            swal({
                    title: "Are You Sure?",
                    text: "You will not be able to recover this record again",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, Delete It"
                },
                function(){
                    $.ajax({
                        type: "DELETE",
                        url: deleteFunction+'/'+id,
                        data: {id:id},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {

                            $('#data-table').DataTable().ajax.reload(null, false);

                            if (data.message){
                                toastr.options =
                                    {
                                        "closeButton" : true,
                                        "progressBar" : true
                                    };
                                toastr.success(data.message);
                            }
                        }
                    });
                });
        });
    </script>
@endpush
