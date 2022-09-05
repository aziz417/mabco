@extends('layouts.admin.master')

@section('page')
    Product Edit
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
                <form action="" method="post" id="product_edit">
                    @method('PUT')
                    @csrf

                    <input type="hidden" name="" id="product_id" value="{{ $product->id }}">

                    <div class="form-group row">
                        <label for="name" class="control-label">Category</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">Select Category</option>
                            @forelse ($categories as $category)
                                <option value="{{ $category->id }}" @if ($product->category_id == $category->id)
                                    selected
                                @endif>{{ $category->name }}</option>
                            @empty
                                <option value="">No Data Found</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="control-label">Brand</label>
                        <select name="brand_id" id="brand_id" class="form-control">
                            <option value="">Select Category</option>
                            @forelse ($brand as $b)
                                <option value="{{ $b->id }}" @if ($product->brand_id == $b->id)
                                    selected
                                @endif>{{ $b->name }}</option>
                            @empty
                                <option value="">No Data Found</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group row">
                        <label for="name">Name</label>
                        <input type="text" value="{{ $product->name }}" name="name" id="name" class="form-control">
                    </div>

                    <div class="form-group row">
                        <label for="unit_id" class="control-label">Unit</label>
                        <select name="unit_id" id="unit_id" class="form-control" required>
                            <option value="">Select Unit</option>
                            @forelse ($units as $unit)
                                <option value="{{ $unit->id }}" @if ($product->unit_id == $unit->id)
                                selected
                                    @endif>{{ $unit->name }}</option>
                            @empty
                                <option value="">No Data Found</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group row">
                        <label for="price">Price</label>
                        <input type="text" value="{{ $product->price }}" name="price" id="price" class="form-control">
                    </div>

                    <div class="form-group">
                        <a href="{{ route('product') }}" class="btn btn-warning">Back</a>
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
    $(document).ready(function () {

        $("#product_edit").on("submit",function (e) {
            e.preventDefault();

            var id = $("#product_id").val();

            var formData = new FormData( $("#product_edit").get(0));

            $.ajax({
                url : "{{ route('product.update','') }}/"+id,
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
