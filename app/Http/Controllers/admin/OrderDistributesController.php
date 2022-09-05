<?php

namespace App\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use App\Order;
use App\OrderDistributes;
use App\Product;
use App\Stock;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderDistributesController extends Controller
{
    public function index()
    {
        return view('admin.distribute.index');
    }

    public function getData()
    {
        $order = Order::select('orders.*',
            'sellers.name as seller_name',
            'retailers.name as retailer_name'
        )
            ->leftJoin('users as sellers', function ($join) {
                $join->on('orders.seller_id', '=', 'sellers.seller_id');
            })
            ->leftJoin('users as retailers', function ($join) {
                $join->on('orders.retailer_id', '=', 'retailers.id');
            })
            ->orderBy('orders.id', 'desc')
            ->groupBy('orders.id')
            ->where('orders.approval', '=', 1)
            ->get();


        return DataTables::of($order)
            ->addIndexColumn()
            ->addColumn('distribute', function ($order) {
                if ($order->distribute == 0) {
                    return '<div>
                        <label class="switch patch">
                            <input type="checkbox" class="status_toggle" data-value="' . $order->id . '" id="status_change" value="' . $order->id . '">
                            <span class="slider"></span>
                        </label>
                        </div>';
                } else {
                    return '<div>
                    <label class="switch patch custom_disabled">
                        <input type="checkbox" class="status_toggle" checked>
                        <span class="slider"></span>
                    </label>
                    </div>';
                }
            })
            ->editColumn('action', function ($order) {
                $return = "<div class=\"btn-group\">";
                if (!empty($order->id)) {
                    $return .= "
                    <div class=\"btn-group\">
                        <a rel=\"$order->id\" rel1=\"order/chalan\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-primary order_chalan \"><i class='fa fa-print'></i></a>
                    </div>";
                }
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'action', 'distribute'
            ])
            ->make(true);
    }


    public function distributeApprove($id)
    {
        $order = Order::findOrFail($id);

        if ($order->distribute == 0) {
            $order->update(['distribute' => 1]);
            return response()->json([
                'message' => 'Order distributed is active'
            ], Response::HTTP_OK);
        } else {

            $order->update(['distribute' => 0]);
            return response()->json([
                'message' => 'Order distributed is in-active'
            ], Response::HTTP_OK);
        }
    }

    public function chalan(Request $request)
    {
        $id = $request->id;
        $order = Order::select('orders.*',
            'sellers.name as seller_name',
            'retailers.name as retailer_name',
            'retailers.address as retailer_address'

        )
            ->leftJoin('users as sellers', function ($join) {
                $join->on('orders.seller_id', '=', 'sellers.seller_id');
            })
            ->leftJoin('users as retailers', function ($join) {
                $join->on('orders.retailer_id', '=', 'retailers.id');
            })
            ->where('orders.approval', '=', 1)
            ->where('orders.id', '=', $id)
            ->first();

        $order->seller_phone = User::where('id', $order->seller_id)->first()->phone;
        $order->retailer_phone = User::where('id', $order->retailer_id)->first()->phone;

        $products = DB::table('order_details')
            ->where('order_details.order_id', $id)
            ->select(
                'order_details.*',
                'products.price',
                'products.name as product_name',
                'brands.name as brand_name',
                'categories.name as category_name',
                'units.name as unit_name',
            )
            ->leftJoin('products', function ($join) {
                $join->on('order_details.product_id', '=', 'products.id');
            })
            ->leftJoin('brands', function ($join) {
                $join->on('order_details.brand_id', '=', 'brands.id');
            })
            ->leftJoin('categories', function ($join) {
                $join->on('order_details.category_id', '=', 'categories.id');
            })
            ->leftJoin('units', function ($join) {
                $join->on('order_details.unit_id', '=', 'units.id');
            })
            ->get();


        return view('admin.invoice.chalan', compact(
            'products',
            'order',
        ));
    }

    public function invoice(Request $request)
    {
        $id = $request->id;
        $order = Order::select('orders.*',
            'sellers.name as seller_name',
            'retailers.name as retailer_name',
            'retailers.address as retailer_address'
        )
            ->leftJoin('users as sellers', function ($join) {
                $join->on('orders.seller_id', '=', 'sellers.seller_id');
            })
            ->leftJoin('users as retailers', function ($join) {
                $join->on('orders.retailer_id', '=', 'retailers.id');
            })
            ->where('orders.approval', '=', 1)
            ->where('orders.id', '=', $id)
            ->first();

        $order->seller_phone = User::where('id', $order->seller_id)->first()->phone;
        $order->retailer_phone = User::where('id', $order->retailer_id)->first()->phone;

        $products = DB::table('order_details')
            ->where('order_details.order_id', $id)
            ->select(
                'order_details.*',
                'products.price',
                'products.name as product_name',
                'brands.name as brand_name',
                'categories.name as category_name',
                'units.name as unit_name',
            )
            ->leftJoin('products', function ($join) {
                $join->on('order_details.product_id', '=', 'products.id');
            })
            ->leftJoin('brands', function ($join) {
                $join->on('order_details.brand_id', '=', 'brands.id');
            })
            ->leftJoin('categories', function ($join) {
                $join->on('order_details.category_id', '=', 'categories.id');
            })
            ->leftJoin('units', function ($join) {
                $join->on('order_details.unit_id', '=', 'units.id');
            })
            ->get();

        $old_out_standing = User::where('id', $order->retailer_id)->first()->total_out_standing ?? 0;
        $receive_amount = Transaction::where([
            'order_id' => $id, 'retailer_id' => $order->retailer_id, 'seller_id' => $order->seller_id
        ])->sum('receive_amount');

        return view('admin.invoice.invoice', compact(
            'products',
            'order',
            'old_out_standing',
            'receive_amount',
        ));
    }
}
