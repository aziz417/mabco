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

                <h2 style="text-align: center">Chalan</h2>
                <h4 style="text-align: center">Chalan No: CH-{{ $order->order_code }}</h4>
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
                    <div class="col-8">
                        <div class="row">
                            <div class="col-4">
                                <h6>Seller Name:</h6>
                                <h6>Seller Phone:</h6>
                                <h6>Retailer Name:</h6>
                                <h6>Retailer Phone:</h6>
                                <h6>Retailer Address:</h6>
                            </div>
                            <div class="col-6">
                                <h6>{{ $order->seller_name }}</h6>
                                <h6>{{ $order->seller_phone }}</h6>
                                <h6>{{ $order->retailer_name }}</h6>
                                <h6>{{ $order->retailer_phone }}</h6>
                                <h6>{{ $order->retailer_address }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-6"></div>
                </div>
                <br>


                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#No</th>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($products as $key => $product)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->brand_name }}</td>
                                    <td>{{ $product->category_name }}</td>
                                    <td>{{ $product->unit_name }}</td>
                                    <td>{{ $product->quantity }}</td>
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
            <div class="col-sm-3 text-center"><h4 style="border-top: 3px dotted">Authorized</h4></div>
            <div class="col-sm-2"></div>
        </div>
        <br>
        <br>
        <p style="text-align: center; width: 100%">Developed and Maintained By Skies Engineering and Technologies
            Ltd.<br><i>www.setcolbd.com</i></p>
    </div>
</div>

</body>
</html>
