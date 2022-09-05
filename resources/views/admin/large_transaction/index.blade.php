@extends('layouts.admin.master')

@section('page')
    Large Transaction
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>


    <style type="text/css">
        .select2-container--default .select2-selection--single {
            border: 1px solid #ccc;
            border-radius: 4px;
            height: 39px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff !important;
        }
        /* Basic Rules */
        .switch input {
            display: none;
        }

        .switch {
            display: inline-block;
            width: 55px;
            height: 25px;
            margin: 8px;
            transform: translateY(50%);
            position: relative;
        }

        /* Style Wired */
        .slider {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: 30px;
            box-shadow: 0 0 0 2px #777, 0 0 4px #777;
            cursor: pointer;
            border: 4px solid transparent;
            overflow: hidden;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            width: 100%;
            height: 100%;
            background: #777;
            border-radius: 30px;
            transform: translateX(-30px);
            transition: .4s;
        }

        input:checked + .slider:before {
            transform: translateX(30px);
            background: limeGreen;
        }

        input:checked + .slider {
            box-shadow: 0 0 0 2px limeGreen, 0 0 2px limeGreen;
        }

        /* Style Flat */
        .switch.flat .slider {
            box-shadow: none;
        }

        .switch.flat .slider:before {
            background: #FFF;
        }

        .switch.flat input:checked + .slider:before {
            background: white;
        }

        .switch.flat input:checked + .slider {
            background: limeGreen;
        }

        .patch {
            margin-top: -25px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="success_message"></div>

            <div id="error_message"></div>

            <div class="card">
                <div class="card-body">
                    <form action="" id="largeTransaction" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
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

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="retailer_id">Select Retailers</label>
                                    <select name="retailer_id[]" multiple="multiple" id="retailer"
                                            class="form-control retailers" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label style="font-size: 10px">Selected Retailers Old Outstanding</label>
                                    <input class="form-control" readonly id="selectedRetailersOldOutStanding" value="">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p style="color: #ef6808">Notice: The transaction amount will be deducted from the old outstanding of your selected retailers.</p>
                            </div>
                            <div class="col-md-2">
                                <label for="date">Date</label>
                                <input type="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                                       name="date" class="form-control custom_disabled">
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="old_out_standing">O.O.standing</label>
                                    <input type="text" name="old_out_standing" readonly id="old_out_standing" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="amount">Transaction Amount</label>
                                    <input type="text" name="amount" id="amount" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="now_out_standing">Now Out Standing</label>
                                    <input type="text" name="now_out_standing" readonly id="now_out_standing" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="transaction_number">T.Number</label>
                                    <input type="text" value="{{ $count == 0 ? 1 : $count }}" name="transaction_number" readonly id="transaction_number" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="brn btn-success p-1" style="margin-top: 32px">Submit</button>
                            </div>

                        </div>
                    </form>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="data-table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Transaction No</th>
                            <th>Seller</th>
                            <th>Date</th>
                            <th>O.O.standing</th>
                            <th>T.Amount</th>
                            <th>N.O.Standing</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#Sl NO</th>
                            <th>Transaction No</th>
                            <th>Seller</th>
                            <th>Date</th>
                            <th>O.O.standing</th>
                            <th>T.Amount</th>
                            <th>N.O.Standing</th>
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
        $(document).ready(function () {

            $('#data-table').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                pagingType: "full_numbers",
                ajax: {
                    url: '{!! route('largeTransaction.getData') !!}',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'transaction_number', name: 'transaction_number'},
                    {data: 'seller_name', name: 'seller_name'},
                    {data: 'date', name: 'date'},
                    {data: 'total_out_standing', name: 'total_out_standing'},
                    {data: 'amount', name: 'amount'},
                    {data: 'now_out_standing', name: 'now_out_standing'},
                    {data: 'action', name: 'action', orderable: false, searchable: true}
                ]
            });

        });
    </script>

    <script>
        $(document).ready(function () {

            $("#largeTransaction").on("submit", function (e) {
                e.preventDefault();
                if (parseFloat($("#selectedRetailersOldOutStanding").val()) < parseFloat($("#amount").val())){
                    alert('Selected Retailer Total Outstanding well be equal or less transaction amount')
                    return
                }
                if ($(".valids").length > 0) {
                    alert('Transaction will be less to the old out standing')
                }

                var formData = new FormData($("#largeTransaction").get(0));

                $.ajax({
                    url: "{{ route('largeTransaction.store') }}",
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
            $.get("{{ route('largeTransaction.retailer.list') }}", {seller_id: seller_id}, function (response) {
                var options = `<option disabled value="">Select Retailer</option>`;
                if (response) {
                    for (var i = 0; i < response['retailers'].length; i++) {
                        options += `<option style="text-transform: capitalize;" value="${response['retailers'][i]['id']}">${response['retailers'][i]['name']}-${response['retailers'][i]['total_out_standing']}</option>`
                    }
                    $("#retailer").html(options)
                    $("#old_out_standing").val(response['totalOldOutStanding'])
                    $("#amount").attr({
                        "max" : response['totalOldOutStanding'],        // substitute your own
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function () {
            $('.retailers').select2();
        });
        var selectedRetailersOldOutStanding = 0;
        $('.retailers').on('select2:select', function (e) {
            selectedRetailersOldOutStanding += parseFloat(e.params.data.text.split('-')[1])
            $("#selectedRetailersOldOutStanding").val(selectedRetailersOldOutStanding)
        });
        $('.retailers').on('select2:unselecting', function (e) {
            var args = JSON.stringify(e.params, function (key, value) {
                selectedRetailersOldOutStanding -= parseFloat(value['args'].data.text.split('-')[1])
                $("#selectedRetailersOldOutStanding").val(selectedRetailersOldOutStanding)
            });
        });
    </script>

@endpush
