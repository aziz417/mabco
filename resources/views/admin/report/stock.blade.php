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
                <h2 style="text-align: center">Product Return Invoice</h2>
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
                                <th>Product Name</th>
                                <th>Brand Name</th>
                                <th>Category Name</th>
                                <th>Unit Name</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($stocks as $key => $stock)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $stock->product_name }}</td>
                                    <td>{{ $stock->brand_name }}</td>
                                    <td>{{ $stock->category_name }}</td>
                                    <td>{{ $stock->unit_name }}</td>
                                    <td>{{ $stock->quantity }}</td>
                                </tr>
                            @empty

                            @endforelse

                            </tbody>
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
