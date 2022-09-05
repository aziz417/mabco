<?php

namespace App\Http\Controllers\admin;

use App\Brand;
use App\Category;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\Order;
use App\Product;
use App\Stock;
use App\Unit;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.order-manage.orders.index');
    }

    public function getData()
    {
        $order = Order::select('orders.*',
            'creator.name as user_name',
            'retailer.name as retailer_name',
            'retailer.total_out_standing as total_out_standing',
            'seller.name as seller_name'
        )
            ->leftJoin('users as creator', function ($join) {
                $join->on('orders.user_id', '=', 'creator.id');
            })
            ->leftJoin('users as retailer', function ($join) {
                $join->on('orders.retailer_id', '=', 'retailer.id');
            })
            ->leftJoin('users as seller', function ($join) {
                $join->on('orders.seller_id', '=', 'seller.id');
            })
            ->orderBy('orders.id', 'desc')
            ->get();

        return DataTables::of($order)
            ->addIndexColumn()
            ->editColumn('action', function ($order) {
                $return = "<div class=\"btn-group\">";
                if (!empty($order->id)) {
                    if ($order->approval != 1) {
                        $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/order/edit/$order->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                        ||
                        <a rel=\"$order->id\" rel1=\"order/destroy\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-danger deleteRecord \"><i class='fa fa-trash'></i></a>
                    </div>";
                    }
                }
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'action'
            ])
            ->make(true);
    }


    public function create()
    {
        $sellers = User::role('seller')->get();

        $products = DB::table('products')->where('approve', 1)->select('id', 'name')->latest()->get();
        $categories = DB::table('categories')->where('status', 1)->select('id', 'name')->latest()->get();
        $brands = DB::table('brands')->where('status', 1)->select('id', 'name')->latest()->get();
        $units = DB::table('units')->where('status', 1)->select('id', 'name')->latest()->get();

        return view('admin.order-manage.orders.create', compact(
            'products',
            'brands',
            'units',
            'categories',
            'sellers'
        ));
    }

    public function getOldOutStanding(Request $request)
    {
        return User::where('id', $request->retailer_id)->select('address', 'total_out_standing')->first();
    }

    public function getPrice()
    {
        $product_id = $_GET['product_id'];

        $price = Product::where('id', $product_id)->first();

        return response()->json([
            'price' => $price
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            DB::beginTransaction();
            try {
                $last_order_code = Order::orderBy('id', 'desc')->first();
                if (!empty($last_order_code->order_code)) {
                    $last_order_code = explode('#', $last_order_code->order_code);
                    $latest_order_code = $last_order_code[1] + 1;
                } else {
                    $latest_order_code = 1255;
                }

                $order = Order::create([
                    'order_code' => '#' . $latest_order_code,
                    'date' => $request->date,
                    'seller_id' => $request->seller_id,
                    'retailer_id' => $request->retailer_id,
                    'user_id' => Auth::id(),
                    'bill_without_discount' => $request->amount,
                    'address' => $request->address,
                    'order_payable_amount' => $request->bill,
                    // 'order_payable_amount' => $request->amount,
                    'commission_type' => $request->commission_type,
                    'commission_value' => $request->commission_value,
                    'total_discount' => $request->total_discount,
                    'bill' => $request->bill,
                ]);

                foreach ($request->product_id as $key => $product) {
                    DB::table('order_details')->insert([
                        'order_id' => $order->id,
                        'category_id' => $request->category_id[$key],
                        'brand_id' => $request->brand_id[$key],
                        'product_id' => $request->product_id[$key],
                        'product_price' => $request->product_price[$key],
                        'unit_id' => $request->unit_id[$key],
                        'quantity' => $request->quantity[$key],
                        'total_price' => $request->total_price[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }

                DB::commit();

                return response()->json([
                    'message' => 'Order store Successful'
                ], Response::HTTP_CREATED);

            } catch (QueryException $e) {
                DB::rollBack();

                $error = $e->getMessage();

                return response()->json([
                    'error' => $error
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);

        $order_details = DB::table('order_details')
            ->where('order_details.order_id', $id)
            ->select('order_details.*', 'stocks.quantity as stock', 'products.price')
            ->leftJoin('stocks', function ($join) {
                $join->on('order_details.product_id', '=', 'stocks.product_id');
            })
            ->leftJoin('products', function ($join) {
                $join->on('order_details.product_id', '=', 'products.id');
            })
            ->groupBy('order_details.product_id')
            ->get();

        $sellers = User::role('seller')->get();
        $retailers = User::role('retailer')->get();

        $products = DB::table('stocks')
            ->where('stocks.quantity', '>', 0)
            ->select('products.name', 'products.id')
            ->leftJoin('products', function ($join) {
                $join->on('stocks.product_id', '=', 'products.id');
            })
            ->groupBy('stocks.product_id')
            ->get();

        $categories = DB::table('categories')->where('status', 1)->select('id', 'name')->latest()->get();
        $brands = DB::table('brands')->where('status', 1)->select('id', 'name')->latest()->get();
        $units = DB::table('units')->where('status', 1)->select('id', 'name')->latest()->get();
        $old_out_standing = User::where(['seller_id' => $order->seller_id, 'id' => $order->retailer_id])->first()->total_out_standing ?? 0;


        return view('admin.order-manage.orders.edit', compact(
            'products',
            'brands',
            'units',
            'categories',
            'sellers',
            'order',
            'order_details',
            'retailers',
            'old_out_standing'
        ));
    }

    public function update(Request $request, $id)
    {
        if ($request->_method == 'PUT') {
            DB::beginTransaction();

            try {
                $deleteAbleIdes = $request->deleteAbleIdes;
                if ($deleteAbleIdes) {
                    $idArray = explode(',', $deleteAbleIdes);

                    foreach ($idArray as $deleteAbleId) {
                        DB::table('order_details')->where(['order_id' => $id, 'id' => $deleteAbleId])->delete();

                    }
                }

                $order = Order::findOrFail($id);

                $order->update([
                    'date' => $request->date,
                    'seller_id' => $request->seller_id,
                    'retailer_id' => $request->retailer_id,
                    'user_id' => Auth::id(),
                    'bill_without_discount' => $request->amount,
                    'order_payable_amount' => $request->bill,
                    // 'order_payable_amount' => $request->amount,
                    'address' => $request->address,
                    'commission_type' => $request->commission_type,
                    'commission_value' => $request->commission_value,
                    'total_discount' => $request->total_discount,
                    'bill' => $request->bill,
                    'approval' => $request->status == 'approval' ? 1 : 0,
                    'cancel' => $request->status == 'cancel' ? 1 : 0,
                ]);

                if ($request->status == 'approval') {
                    $user = User::where(['seller_id' => $request->seller_id, 'id' => $request->retailer_id])->first();
                    $user->total_out_standing = $request->total_out_standing;
                    $user->save();
                    $phone = $user->phone;
//                    $message = ' স্যার, আপনার '.$order->order_code .' অর্ডারটি অনুমোদন করা হয়েছে ';


                    // $message = 'Sir, Apnar ' . $order->order_code . ' order no ti approve kora hoyeche.';
                    // $message = str_replace('#', '%20', $message);
                    // $sms = str_replace(' ', '%20', $message);
                    // $ch = curl_init();
                    
                    
//                    http://api.rankstelecom.com/api/v3/sendsms/plain?user=setcoldigital&password=setc@1Digi1626ta1&sender=8804445601617&SMSText=&GSM=8801797506292&datacoding=8
//                    curl_setopt($ch, CURLOPT_URL, 'http://api.rankstelecom.com/api/v3/sendsms/plain?user=setcoldigital&password=setc@1Digi1626ta1&sender=8804445601617&SMSText='.$sms.'&GSM=88'.$phone.'&datacoding=8');
                    
                    // curl_setopt($ch, CURLOPT_URL, 'http://api.rankstelecom.com/api/v3/sendsms/plain?user=setcoldigital&password=setc@1Digi1626ta1&sender=8804445601617&SMSText=' . $sms . '&GSM=88' . $phone . '&type=longSMS');
                    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    // curl_exec($ch);
                    // curl_close($ch);
                    
                    

                } elseif ($request->status == 'cancel') {
                    $user = User::where(['seller_id' => $request->seller_id, 'id' => $request->retailer_id])->first();
                    $user->total_out_standing = $request->total_out_standing;
                    $user->save();
                    $phone = $user->phone;
//                    $message = ' স্যার, আপনার '.$order->order_code .' অর্ডারটি অনুমোদন করা হয়েছে ';
                    $message = 'Sir, Apnar ' . $order->order_code . ' order no ti Cancel kora hoyeche.';
                    $message = str_replace('#', '%20', $message);
                    $sms = str_replace(' ', '%20', $message);
                    $ch = curl_init();
//                    http://api.rankstelecom.com/api/v3/sendsms/plain?user=setcoldigital&password=setc@1Digi1626ta1&sender=8804445601617&SMSText=&GSM=8801797506292&datacoding=8
//                    curl_setopt($ch, CURLOPT_URL, 'http://api.rankstelecom.com/api/v3/sendsms/plain?user=setcoldigital&password=setc@1Digi1626ta1&sender=8804445601617&SMSText='.$sms.'&GSM=88'.$phone.'&datacoding=8');
                    curl_setopt($ch, CURLOPT_URL, 'http://api.rankstelecom.com/api/v3/sendsms/plain?user=setcoldigital&password=setc@1Digi1626ta1&sender=8804445601617&SMSText=' . $sms . '&GSM=88' . $phone . '&type=longSMS');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_exec($ch);
                    curl_close($ch);
                }

                foreach ($request->product_id as $key => $product) {
                    if (!empty($request->old_item_ids[$key])) {

                        if ($request->status === 'approval') {
                            // stock out
                            $stock = Stock::where(
                                [
                                    'product_id' => $request->product_id[$key],
                                    'category_id' => $request->category_id[$key],
                                    'brand_id' => $request->brand_id[$key],
                                    'unit_id' => $request->unit_id[$key],
                                ])->first();

                            $stock->update([
                                'quantity' => $stock->quantity - $request->quantity[$key],
                            ]);

                            $product = Product::where('id', $request->product_id[$key])->select('name', 'price')->first();
                            $category_name = Category::where('id', $request->category_id[$key])->first()->name;
                            $brand_name = Brand::where('id', $request->brand_id[$key])->first()->name;
                            $unit_name = Unit::where('id', $request->unit_id[$key])->first()->name;

                            Inventory::create([
                                'stock_id' => $stock->id,
                                'user_id' => Auth()->user()->id,
                                'product_name' => $product['name'],
                                'category_name' => $category_name,
                                'brand_name' => $brand_name,
                                'unit_name' => $unit_name,
                                'old_quantity' => $stock->quantity,
                                'add_or_less' => $request->quantity[$key],
                                'now_quantity' => $stock->quantity - $request->quantity[$key],
                                'type' => 'out',
                                'price' => $product['price'],
                                'amount' => $request->quantity[$key] * $product['price'],
                            ]);
                        }

                        DB::table('order_details')->where('id', $request->old_item_ids[$key])->update([
                            'order_id' => $id,
                            'category_id' => $request->category_id[$key],
                            'brand_id' => $request->brand_id[$key],
                            'product_id' => $request->product_id[$key],
                            'product_price' => $request->product_price[$key],
                            'unit_id' => $request->unit_id[$key],
                            'quantity' => $request->quantity[$key],
                            'total_price' => $request->total_price[$key],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    } else {
                        DB::table('order_details')->insert([
                            'order_id' => $id,
                            'category_id' => $request->category_id[$key],
                            'brand_id' => $request->brand_id[$key],
                            'product_id' => $request->product_id[$key],
                            'product_price' => $request->product_price[$key],
                            'unit_id' => $request->unit_id[$key],
                            'quantity' => $request->quantity[$key],
                            'total_price' => $request->total_price[$key],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }

                DB::commit();

                return response()->json([
                    'message' => 'Order Update successful'
                ], Response::HTTP_OK);


            } catch (QueryException $e) {
                DB::rollBack();

                $error = $e->getMessage();

                return response()->json([
                    'error' => $error
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        DB::table('order_details')->where('order_id', $id)->delete();

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successful'
        ], Response::HTTP_OK);
    }

    public function approveList()
    {
        return view('admin.order-manage.order-approve.index');
    }

    public function getApproveData()
    {
        $order = Order::select('orders.*',
            'creator.name as user_name',
            'retailer.name as retailer_name',
            'retailer.total_out_standing as total_out_standing',
            'seller.name as seller_name'
        )
            ->leftJoin('users as creator', function ($join) {
                $join->on('orders.user_id', '=', 'creator.id');
            })
            ->leftJoin('users as retailer', function ($join) {
                $join->on('orders.retailer_id', '=', 'retailer.id');
            })
            ->leftJoin('users as seller', function ($join) {
                $join->on('orders.seller_id', '=', 'seller.id');
            })
            ->orderBy('orders.id', 'desc')
            ->where('orders.approval', '=', 1)
            ->orWhere('orders.cancel', '!=', 1)
            ->get();

        return DataTables::of($order)
            ->addIndexColumn()
            ->addColumn('status', function ($order) {
                if ($order->approval == 1) {

                    return '<span class="badge bg-success">Approved</span>';
                }

                if ($order->cancel == 1) {

                    return '<span class="badge bg-danger">Canceled</span>';
                }

            })
            ->editColumn('action', function ($order) {
                $return = "<div class=\"btn-group\">";
                if (!empty($order->id)) {
                    $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/order/approve/$order->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-eye'></i></a>
                    </div>
                    ||
                    <div class=\"btn-group\">
                        <a rel=\"$order->id\" rel1=\"order/invoice\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-primary order_invoice \"><i class='fa fa-print'></i></a>
                    </div>
                    ";
                }
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'action', 'status'
            ])
            ->make(true);
    }

    public function statusChange($id)
    {
        $order = Order::findOrFail($id);

        if ($order->approval == 0) {
            $order->update(['approval' => 1]);

            return response()->json([
                'message' => 'Order is approve'
            ], Response::HTTP_OK);
        } else {
            $order->update(['approval' => 0]);

            return response()->json([
                'message' => 'Order is cancel'
            ], Response::HTTP_OK);
        }
    }


    public function approve($id)
    {
        $order = Order::findOrFail($id);

        $order_details = DB::table('order_details')
            ->where('order_details.order_id', $id)
            ->select('order_details.*', 'stocks.quantity as stock', 'products.price')
            ->leftJoin('stocks', function ($join) {
                $join->on('order_details.product_id', '=', 'stocks.product_id');
            })
            ->leftJoin('products', function ($join) {
                $join->on('order_details.product_id', '=', 'products.id');
            })
            ->groupBy('stocks.product_id')
            ->get();

        $sellers = User::role('seller')->get();
        $retailers = User::role('retailer')->get();

        $products = DB::table('stocks')
            ->where('stocks.quantity', '>', 0)
            ->select('products.name', 'products.id')
            ->leftJoin('products', function ($join) {
                $join->on('stocks.product_id', '=', 'products.id');
            })
            ->get();

        $categories = DB::table('categories')->where('status', 1)->select('id', 'name')->latest()->get();
        $brands = DB::table('brands')->where('status', 1)->select('id', 'name')->latest()->get();
        $units = DB::table('units')->where('status', 1)->select('id', 'name')->latest()->get();
        $old_out_standing = User::where(['seller_id' => $order->seller_id, 'id' => $order->retailer_id])->first()->total_out_standing ?? 0;

        return view('admin.order-manage.order-approve.approve', compact(
            'products',
            'brands',
            'units',
            'categories',
            'sellers',
            'order',
            'order_details',
            'retailers',
            'old_out_standing'
        ));
    }

    public function cancelList()
    {
        return view('admin.order-manage.order_cancel.index');
    }

    public function getCancelData()
    {
        $order = Order::select('orders.*',
            'creator.name as user_name',
            'retailer.name as retailer_name',
            'retailer.total_out_standing as total_out_standing',
            'overview.name as seller_name'
        )
            ->leftJoin('users as creator', function ($join) {
                $join->on('orders.user_id', '=', 'creator.id');
            })
            ->leftJoin('users as retailer', function ($join) {
                $join->on('orders.retailer_id', '=', 'retailer.id');
            })
            ->leftJoin('users as overview', function ($join) {
                $join->on('orders.seller_id', '=', 'overview.id');
            })
            ->orderBy('orders.id', 'desc')
            ->where('orders.cancel', '=', 1)
            ->get();

        return DataTables::of($order)
            ->addIndexColumn()
            ->addColumn('status', function ($order) {
                if ($order->approval == 1) {

                    return '<span class="badge bg-success">Order is approve</span>';
                }

                if ($order->cancel == 1) {

                    return '<span class="badge bg-danger">Order is cancel</span>';
                }

            })
            ->rawColumns([
                'status'
            ])
            ->make(true);
    }

    // get retailer
    public function getRetailer(Request $request)
    {
        $seller_id = $request->seller_id;
        $retailers = User::where('seller_id', $seller_id)->get();
        return $retailers;
    }

    public function brandCategoryProducts(Request $request)
    {
        $category_id = $request->category_id;
        $brand_id = $request->brand_id;

        if ($category_id) {
            $products = DB::table('stocks')
                ->where('stocks.quantity', '>', 0)
                ->select('products.name', 'products.id')
                ->leftJoin('products', function ($join) {
                    $join->on('stocks.product_id', '=', 'products.id');
                })
                ->where('stocks.category_id', $category_id)
                ->groupBy('stocks.product_id')
                ->get();
        }

        if ($brand_id) {
            $products = DB::table('stocks')
                ->where('stocks.quantity', '>', 0)
                ->select('products.name', 'products.id')
                ->leftJoin('products', function ($join) {
                    $join->on('stocks.product_id', '=', 'products.id');
                })
                ->where('stocks.brand_id', $brand_id)
                ->groupBy('stocks.product_id')
                ->get();
        }

        if ($category_id && $brand_id) {
            $products = DB::table('stocks')
                ->where('stocks.quantity', '>', 0)
                ->select('products.name', 'products.id')
                ->leftJoin('products', function ($join) {
                    $join->on('stocks.product_id', '=', 'products.id');
                })
                ->where(['stocks.brand_id' => $brand_id, 'stocks.category_id' => $category_id])
                ->groupBy('stocks.product_id')
                ->get();
        }

        return $products;
    }

    public function productUnitStockPrice(Request $request)
    {
        $product_id = $request->product_id;
        $product = DB::table('stocks')
            ->where('stocks.product_id', $product_id)
            ->where('stocks.quantity', '>', 0)
            ->select('stocks.quantity', 'units.name', 'units.id', 'products.price')
            ->leftJoin('products', function ($join) {
                $join->on('stocks.product_id', '=', 'products.id');
            })
            ->leftJoin('units', function ($join) {
                $join->on('stocks.unit_id', '=', 'units.id');
            })
            ->first();
        return \response()->json($product);
    }
}
