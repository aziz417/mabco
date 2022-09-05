<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
</head>
<body>
<!-- JQVMap -->
<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-body">

                <br>
                <br>
                <h2 style="text-align: center">Collection Invoice</h2>
                <br>
                <br>

                <div class="row" style="margin-bottom: 25px">
                    <div class="col-md-6 text-left">
                        <h5>Mabco Laboratories Ltd.</h5>
                        <span>Corporate Office : 73/H/B, Dhanmondi Central Road , Dhaka.</span><br>
                        <span>Distribute Office : 26/6, Bank Town, Savar, Dhaka.</span>
                    </div>
                    <div class="col-md-6 text-right">
                        <h5>Date: {{ Carbon\Carbon::now()->format('Y-m-d') }}</h5>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#No</th>
                                <th>Seller Name</th>
                                <th>Order Code</th>
                                <th>Brand</th>
                                <th>Payment Type</th>
                                <th>Payable Amount</th>
                                <th>Receive Amount</th>
                                <th>Due Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($collections as $key => $collection)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $collection->name }}</td>
                                    <td>{{ $collection->order_code }}</td>
                                    <td>{{ $collection->brand_name }}</td>
                                    <td>{{ $collection->payment_type }}</td>
                                    <td>{{ $collection->payable_amount }}</td>
                                    <td>{{ $collection->receive_amount }}</td>
                                    <td>{{ $collection->due_amount }}</td>
                                </tr>
                            @empty

                            @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6"></div>

                    <div class="col-md-6">
                        <table class="table table-bordered table-striped table-hover text-right">
                            <tr>
                                <td>In Total Payable Amount</td>
                                <td>:</td>
                                <td>{{ $payable_amount }}</td>
                            </tr>
                            <tr>
                                <td>In Total Receive Amount</td>
                                <td>:</td>
                                <td>{{ $receive_amount }}</td>
                            </tr>

                            <tr>
                                <td>In Total Due Amount</td>
                                <td>:</td>
                                <td>{{ $payable_amount - $receive_amount }}</td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 80px">
            <div class="col-sm-2"></div>
            <div class="col-sm-3 text-center"><h4 style="border-top: 3px dotted">Received By</h4></div>
            <div class="col-sm-2"></div>
            <div class="col-sm-3 text-center"><h4 style="border-top: 3px dotted">Authorize</h4></div>
            <div class="col-sm-2"></div>
        </div>
        <br>
        <br>
        <p style="text-align: center; width: 100%">Developed and Maintained By Skies Engineering and Technologies Ltd.<br><i>www.setcolbd.com</i></p>
    </div>
</div>

</body>
</html>
