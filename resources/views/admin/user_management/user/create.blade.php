@extends('layouts.admin.master')

@section('page')
    User Create
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
                <form action="" method="post" id="user_post">
                    @csrf

                    <div class="form-group row">
                        <label for="name" class="control-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="form-group row">
                        <label for="phone" class="control-label">Phone</label>
                        <input type="number" name="phone" id="phone" class="form-control" required>
                    </div>

                    <div class="form-group row">
                        <label for="address" class="control-label">Address</label>
                        <input type="text" name="address" id="address" class="form-control">
                    </div>

                    <div class="form-group row">
                        <label for="password" class="control-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="form-group row">
                        <label for="role" class="control-label">Role</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group row d-none" id="seller">
                        <label for="seller_id" class="control-label">Seller</label>
                        <select name="seller_id" id="seller_id" class="form-control">

                        </select>
                    </div>

                    <div class="form-group">
                        <a href="{{ route('user') }}" class="btn btn-warning">Back</a>
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
        $("#role").on('change', function (){
            if ($("#role option:selected").text() === 'retailer'){
                $("#seller_id option:selected").prop("selected", true)
                //all seller show and set
                $.get("{{ route('all-seller-get') }}", function (response) {
                    if (response) {
                        var options = `<option selected disabled value="">Select Seller</option>`;
                        for (var i = 0; i < response.length; i++) {
                            options += `<option style="text-transform: capitalize;" value="${response[i]['id']}">${response[i]['name']}</option>`
                        }
                        $("#seller_id").html(options)
                        $("#seller_id").prop('required', true);
                        $("#seller").removeClass("d-none");
                    }
                });

            }else{
                $("#seller_id").prop('required', false);
                $("#seller").addClass("d-none");
                $("#seller_id").val('');
            }
        })
    </script>
<script>
    $(document).ready(function () {
        $("#user_post").on("submit", function (e) {
            e.preventDefault();

            var formData = $("#user_post").serializeArray();

            $.ajax({
                url: "{{ route('user.store') }}",
                type: "POST",
                data: $.param(formData),
                dataType: "json",
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

                    if (err.status === 500) {
                        $('#error_message').html(
                            '<div class="alert alert-danger alert-dismissible fade show mt-10" role="alert">' +
                            '<strong>' + err.responseJSON.errors + '</strong>' +
                            '</div>'
                        );
                    }

                    if (err.status === 400) {
                        $('#error_message').html(
                            '<div class="alert alert-danger alert-dismissible fade show mt-10" role="alert">' +
                            '<strong>' + err.responseJSON.errors + '</strong>' +
                            '</div>'
                        );
                    }
                }
            });
        })
    })
</script>
@endpush
