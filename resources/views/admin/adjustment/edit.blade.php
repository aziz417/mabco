@extends('layouts.admin.master')

@section('page')
    Return Edit
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
                    <form action="" id="adjustment_edit" method="post">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="adjustment_id" value="{{ $adjustment->id }}" id="adjustment_id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input name="title" id="title" value="{{ $adjustment->title }}" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="date">Date</label>
                                <input type="date" value="{{ $adjustment->date }}"
                                       name="date" class="form-control custom_disabled">
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="seller_id">Seller Name</label>
                                    <select name="seller_id" id="seller_id" onchange="retailerSelect(this)"
                                            class="form-control" required>
                                        <option value="">Select Seller</option>
                                        @forelse ($sellers as $seller)
                                            <option {{ $seller->id == $adjustment->seller_id ? 'selected': '' }} value="{{ $seller->id }}">{{ $seller->name }}</option>
                                        @empty
                                            <option value="">Data Not Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="retailer_id">Retailer Name</label>
                                    <select name="retailer_id" id="retailer" onchange="oldOutStanding(this)"
                                            class="form-control" required>
                                            @forelse ($retailers as $retailer)

                                                    <option {{ $retailer->id == $adjustment->retailer_id ? 'selected': '' }} value="{{ $retailer->id }}">{{ $retailer->name }}</option>

                                            @empty
                                                <option value="">Data Not Found</option>
                                            @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="old_out_standing">O.O.standing</label>
                                    <input type="text" value="{{ $adjustment->old_out_standing }}" name="old_out_standing" readonly id="old_out_standing" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="amount">Adjustment Amount</label>
                                    <input type="text" value="{{ $adjustment->amount }}" name="amount" id="amount" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="now_out_standing">Now Out Standing</label>
                                    <input type="text" value="{{ $adjustment->old_out_standing - $adjustment->amount }}" name="now_out_standing" readonly id="now_out_standing" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="adjustment_number">A.Number</label>
                                    <input type="text" value="{{ $count == 0 ? 1 : $count }}" name="adjustment_number" readonly id="adjustment_number" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="brn btn-success p-1" style="margin-top: 32px">Submit</button>
                            </div>

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

            $("#adjustment_edit").on("submit", function (e) {
                e.preventDefault();

                if ($(".valids").length > 0) {
                    alert('quantity will be less or equal to the stock')
                    return;
                }

                var formData = new FormData($("#adjustment_edit").get(0));
                var id = $("#adjustment_id").val();

                $.ajax({
                    url : "{{ route('adjustment.update','') }}/"+id,
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
            $.get("{{ route('adjustment.retailer.list') }}", {seller_id: seller_id}, function (response) {
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
        function oldOutStanding(e) {
            var retailer_id = $(e).val();
            $.get("{{ route('adjustment.retailer.outStanding') }}", {retailer_id: retailer_id}, function (response) {
                if (response) {
                    $("#old_out_standing").val(response)
                    $("#amount").attr({
                        "max" : response,        // substitute your own
                        "min" : 1          // values (or variables) here
                    });
                }
            });
        }
    </script>

    <script>
        $("#amount").bind('keyup mouseup', function (){
            var adjustment_amount = $(this).val()
            var old_out_standing = $("#old_out_standing").val()
            if (parseFloat(adjustment_amount) > parseFloat(old_out_standing)){
                $(this).closest(".form-group").find(".valids").remove()
                $(this).after($('<span class="valids" style="color: red;"> This quantity not valid</span>'))
            }else{
                $(this).closest(".form-group").find(".valids").remove()
                $("#now_out_standing").val(old_out_standing - adjustment_amount)
            }
        })
    </script>
@endpush
