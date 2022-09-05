@extends('layouts.admin.master')

@section('page')
    Expanse Create
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
                <form action="" method="post" id="expanse">
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <label for="date">Date</label>
                            <input type="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                                   name="date" class="form-control custom_disabled">
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="expanse_reasons_id">Expanse Reason</label>
                                <select name="expanse_reasons_id" id="expanse_reasons_id"
                                        class="form-control" required>
                                    <option value="">Select Expanse Reason</option>
                                    @forelse ($expanse_reasons as $expanse_reason)
                                        <option value="{{ $expanse_reason->id }}">{{ $expanse_reason->title }}</option>
                                    @empty
                                        <option value="">Data Not Found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="text" name="amount" id="amount" required class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <a href="{{ route('expanse_reason') }}" class="btn btn-warning">Back</a>
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

        $("#expanse").on("submit",function (e) {
            e.preventDefault();

            var formData = new FormData( $("#expanse").get(0));

            $.ajax({
                url : "{{ route('expanse.store') }}",
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
