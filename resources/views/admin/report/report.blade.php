@extends('layouts.admin.master')

@section('page')
    Report
@endsection

@push('css')
    <style>
        .custom_btn_style {
            height: 30px !important;
            display: flex !important;
            align-items: center !important;
        }

        .disable {
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="p-3">
                    <h2>Report:</h2>
                    <div class="row">
                        <div class="col-sm-3 d-flex justify-content-between" id="date_type_select">
                            <div>
                                <label>Today And Other Day</label>
                                <input type="radio" name="date_type" value="today_date">
                            </div>
                            <div>
                                <label>Date To Date</label>
                                <input type="radio" name="date_type" value="date_to_date" class="">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="today d-none">
                                <label>Today Date And Other Day</label>
                                <input type="date" value="{{ date('Y-m-d') }}" name="today" id="today_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="date_to_date d-none">
                                <label>Form Date</label>
                                <input type="date" name="date_form" class="form-control" id="form_date">
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="date_to_date d-none">
                                <label>To Date</label>
                                <input type="date" name="date_to" class="form-control" id="to_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row d-none" id="reportTypeField">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="brand">
                                    <span class="date_type_text"></span> Brand Wise
                                </label>
                                <select name="brand" onchange="saleShow(this)" id="brand" class="form-control">
                                    <option value=""  selected>Select Brand</option>
                                    @forelse($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ ucwords($brand->name) }}</option>
                                    @empty
                                        <option value="">No Data Found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="sales_person">
                                    <span class="date_type_text"></span> Sales Person Wise
                                </label>
                                <select name="seller" id="seller" class="form-control" onchange="stockShow(this)">
                                    <option value="" selected>Select Sales Person</option>
                                    @forelse($sellers as $seller)
                                        <option value="{{ $seller->id }}">{{ ucwords($seller->name) }}</option>
                                    @empty
                                        <option value="">No Data Found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4"></div>
                                        {{--<div class="col-sm-2" id="saleShow">--}}
                                        {{--<div class="form-group">--}}
                                        {{--<label for="sale">--}}
                                        {{-- <span class="date_type_text"></span> Sale--}}
                                        {{--</label>--}}
                                        {{--<input name="report" id="sale" type="radio" value="sale">--}}
                                        {{--</div>--}}
                                        {{-- </div>--}}
                        <div class="col-sm-2" id="stockShow">
                            <div class="form-group">
                                <label for="stock">
                                    <span class="date_type_text"></span> Stock
                                </label>
                                <input name="report" id="stockId" type="radio" value="stock">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="collection">
                                    <span class="date_type_text"></span> Collection
                                </label>
                                <input name="report" id="collectionId" type="radio" value="collection">
                            </div>
                        </div>
                        <div class="col-sm-2" id="return">
                            <div class="form-group">
                                <label for="return">
                                    <span class="date_type_text"></span> Return
                                </label>
                                <input name="report" id="returnId" type="radio" value="return">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="damage">
                                    <span class="date_type_text"></span> Damage
                                </label>
                                <input name="report" id="damageId" type="radio" value="damage">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <button class="btn btn-success" id="reportBtn" onclick="report()">Report</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="salePrint" style="display: none"></div>
@endsection

@push('js')
    <script>
        function report() {
            var date_type = $('input[name=date_type]:checked', '#date_type_select').val();
            var today_date = $("#today_date").val();
            var form_date = $("#form_date").val();
            var to_date = $("#to_date").val();
            var report_type = $('input[name=report]:checked', '#reportTypeField').val();
            var seller = $("#seller").val();
            var brand = $("#brand").val();

            if (!report_type){
                alert('Please Select Report Type')
                return;
            }
            

            // if($("#returnId").prop("checked", true)){

            //     if(!brand){
            //         alert('Please Select Brand And Seller Name');
            //         return;
            //     }else if(!seller){
            //         alert('Please Select Seller Name');
            //         return;
            //     }

            // }

            if (date_type == 'today_date') {
                form_date = null;
                to_date = null;
            } else if (date_type == 'date_to_date') {
                today_date = null;
            }


            $.get("{{ route('reportManage') }}", {
                report_type: report_type,
                date_type: date_type,
                today_date: today_date,
                form_date: form_date,
                to_date: to_date,
                brand: brand,
                seller: seller,

            }, function (response) {
                if (response) {

                    // console.log(response)

                    $("#salePrint").html(response)

                    var divToPrint = document.getElementById("salePrint");

                    var newWin = window.open('', 'Print-Window');

                    newWin.document.open();

                    newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

                    newWin.document.close();

                    setTimeout(function () {
                        newWin.close();
                    }, 1000);
                }
            });
        }
    </script>
    <script>

        function stockShow(e){
            var persion = $(e).val();
            if(persion){
                $("#stockShow").addClass('d-none')
                $("#stock").prop('checked', false)
            }else {
                $("#stockShow").removeClass('d-none')
            }
        }

        function saleShow(e){
            var brand = $(e).val();
            // console.log(brand);
            if(brand){
                $("#saleShow").addClass('d-none')
                $("#sale").prop('checked', false)
            }else {
                $("#saleShow").removeClass('d-none')
            }
        }
    </script>
    <script>
        $('#date_type_select input').on('change', function () {
            $(".custom_btn_style").removeClass('disable')
            $("#reportTypeField").removeClass('d-none')

            var date_type = $('input[name=date_type]:checked', '#date_type_select').val();
            if (date_type == 'today_date') {
                $(".today").removeClass('d-none')
                $(".date_to_date").addClass('d-none')
                $(".date_type_text").html('Today ')
            } else {
                $(".date_to_date").removeClass('d-none')
                $(".today").addClass('d-none')
                $(".date_type_text").html('Date To Date ')
            }
        });
    </script>
@endpush
