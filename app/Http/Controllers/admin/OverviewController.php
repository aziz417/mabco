<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Order;
use App\OrderDistributes;
use App\ReturnProducts;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OverviewController extends Controller
{
    public function index()
    {
        $user = User::where('id', 15)->first();
        return view('admin.overview.index', compact('user'));
    }

    public function sellerInformation(Request $request){
        return User::findOrFail($request->id);
    }

    public function getData($id)
    {
        if ($id != "null"){
            // retailer
            $users = DB::table('users')
                ->select(
                    'users.*',
                    'roles.name as role_name',
                    'orders.bill',
                    'orders.retailer_id as retailer_id',
                    'transactions.receive_amount'
                )
                ->leftJoin('orders', function ($join) {
                    $join->on('users.id', '=', 'orders.retailer_id');
                })
                ->leftJoin('transactions', function ($join) {
                    $join->on('users.id', '=', 'transactions.retailer_id');
                })
                ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
                ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
                ->where('users.id','!=', Auth::id())
                ->where('users.deleted_at','=', null)
                ->where('roles.name','retailer')
                ->where('users.seller_id', $id)
                ->groupBy('users.id')
                ->orderBy('users.id','desc')
                ->get();

            $users = $users->filter(function ($u){
                if (Order::where('retailer_id', $u->id)->first()){
                    return $u;
                }
            });

            $user = $users->map(function ($usr) use($id){
                $usr->bill = Order::where(['retailer_id' => $usr->id, 'seller_id' => $id])->sum('bill');
                $usr->receive_amount = Transaction::where(['retailer_id' => $usr->id, 'seller_id' => $usr->seller_id])->sum('receive_amount');
                return $usr;
            });
        }else{
            $users = DB::table('users')
                ->select(
                    'users.*',
                    'roles.name as role_name',
                    'orders.bill',
                    'orders.seller_id as seller_id',
                    'transactions.receive_amount'
                )
                ->leftJoin('orders', function ($join) {
                    $join->on('users.id', '=', 'orders.seller_id');
                })
                ->leftJoin('transactions', function ($join) {
                    $join->on('users.id', '=', 'transactions.seller_id');
                })
                ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
                ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
                ->where('users.id','!=', Auth::id())
                ->where('users.deleted_at','=', null)
                ->where('roles.name','seller')
                ->groupBy('users.id')
                ->orderBy('users.id','desc')
                ->get();

            $users = $users->filter(function ($u){
                if (Order::where('seller_id', $u->seller_id)->first()){
                    return $u;
                }
            });

            $user = $users->map(function ($usr){
                $usr->bill = Order::where('seller_id', $usr->seller_id)->sum('bill');
                $usr->total_out_standing = User::where('seller_id', $usr->id)->sum('total_out_standing');
                $usr->receive_amount = Transaction::where('seller_id', $usr->seller_id)->sum('receive_amount');
                return $usr;
            });
        }
        
        
        // foreach ($user as $u) {
        //     $useold = User::where("seller_id", $u->id)->first();
        //     $bill = $u->bill;
        //     $useold->update([
        //      'total_out_standing' => $bill
        //     ]);
        // }


        return DataTables::of($user)
            ->addIndexColumn()
            ->editColumn('action', function ($user) {
                $return = "<div class=\"btn-group\">";
                if ($user->role_name === 'seller'){
                    if (!empty($user->id)) {
                        $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/overview/history/$user->role_name/1/$user->id\" style='margin-right: 5px' class=\"btn btn-sm btn-primary\"><i class='fa fa-file-invoice'></i> History</a>
                        ||
                        <a rel=\"$user->id\" rel1=\"overview/retailers\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-info retailers\"><i class='fa fa-users'></i> Retailers</a>

                    </div>";
                    }
                }else{
                    if (!empty($user->id)) {
                        $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/overview/history/$user->role_name/$user->seller_id/$user->id\" style='margin-right: 5px' class=\"btn btn-sm btn-primary\"><i class='fa fa-file-invoice'></i>History</a>
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

    public function history($role, $seller, $id)
    {
       if ($role == 'retailer'){
           // transaction history
           $user = User::where('id', $id)->first();
           $transactions = DB::table('transactions')->where('transactions.retailer_id', $id)
               ->select('transactions.*', 'orders.order_code')
               ->leftJoin('orders', function ($join) {
                   $join->on('transactions.retailer_id', '=', 'orders.retailer_id');
               })
               ->groupBy('transactions.id')
               ->get();

           $transaction_total_payable = DB::table('orders')->where('retailer_id', $id)->where('approval', 1)->sum('bill');

           $transaction_receive_amount = Transaction::where('retailer_id', $id)->sum('receive_amount');

           // return and damaged history
           $returns = DB::table('return_product_details')
               ->leftJoin('products', function ($join) {
                   $join->on('return_product_details.product_id', '=', 'products.id');
               })
               ->leftJoin('return_products', function ($join) {
                   $join->on('return_product_details.return_product_id', '=', 'return_products.id');
               })
               ->where(
                   ['return_products.retailer_id' => $id,
                       'return_products.type' => 'return',
                       'return_products.approve' => 1]
               )
               ->orderBy('return_product_details.id', 'desc')
               ->groupBy('return_product_details.id')
               ->get();

           $return_products = DB::table('return_product_details')
               ->leftJoin('return_products', function ($join) {
                   $join->on('return_product_details.return_product_id', '=', 'return_products.id');
               })
               ->where(
                   ['return_products.retailer_id' => $id,
                       'return_products.type' => 'return',
                       'return_products.approve' => 1]
               )
               ->sum('return_product_details.return_quantity');

           $total_return_amount = DB::table('return_products')
               ->where(['return_products.retailer_id' => $id, 'return_products.type' => 'return'])
               ->where('return_products.approve', 1)
               ->sum('return_products.return_amount');

           $total_return_discount_amount = DB::table('return_products')
               ->where(['return_products.retailer_id' => $id, 'return_products.type' => 'return'])
               ->where('return_products.approve', 1)
               ->sum('return_products.discount');


           $damages = DB::table('return_product_details')
               ->leftJoin('products', function ($join) {
                   $join->on('return_product_details.product_id', '=', 'products.id');
               })
               ->leftJoin('return_products', function ($join) {
                   $join->on('return_product_details.return_product_id', '=', 'return_products.id');
               })
               ->where(
                   ['return_products.retailer_id' => $id,
                       'return_products.type' => 'damage',
                       'return_products.approve' => 1]
               )
               ->orderBy('return_product_details.id', 'desc')
               ->groupBy('return_product_details.id')
               ->get();

           $damage_products = DB::table('return_product_details')
               ->leftJoin('return_products', function ($join) {
                   $join->on('return_product_details.return_product_id', '=', 'return_products.id');
               })
               ->where(
                   ['return_products.retailer_id' => $id,
                       'return_products.type' => 'damage',
                       'return_products.approve' => 1]
               )
               ->sum('return_product_details.return_quantity');

           $total_damage_amount = DB::table('return_products')
               ->where(['return_products.retailer_id' => $id, 'return_products.type' => 'damage'])
               ->where('return_products.approve', 1)
               ->sum('return_products.return_amount');

           $total_damage_discount_amount = DB::table('return_products')
               ->where(['return_products.retailer_id' => $id, 'return_products.type' => 'damage'])
               ->where('return_products.approve', 1)
               ->sum('return_products.discount');

           // order history
           $order_products = DB::table('order_details')
               ->select(
                   'order_details.quantity',
                   'order_details.product_price as price',
                   'order_details.total_price as sub_total',
                   'orders.order_code',
                   'orders.date',
                   'brands.name as brand_name',
                   'products.name as product_name',
               )
               ->leftJoin('products', function ($join) {
                   $join->on('order_details.product_id', '=', 'products.id');
               })
               ->leftJoin('orders', function ($join) {
                   $join->on('order_details.order_id', '=', 'orders.id');
               })
               ->leftJoin('brands', function ($join) {
                   $join->on('order_details.brand_id', '=', 'brands.id');
               })
               ->where(['orders.retailer_id' => $id, 'orders.approval' => 1])
               ->where('order_details.quantity', '>', 0)
               ->groupBy('order_details.id')
               ->get();


           $query =  Order::where(['retailer_id' => $id, 'approval' => 1]);
           $total_order_bill = $query->sum('bill_without_discount');
           $total_order_discount = $query->sum('total_discount');
           $orders = $query->get();


       }else{
           //seller
           // transaction history
           $user = User::where('id', $id)->first();
           $user->total_out_standing = User::where('seller_id', $id)->sum('total_out_standing');

           $transactions = DB::table('transactions')->where('transactions.seller_id', $id)
               ->select('transactions.*', 'orders.order_code')
               ->leftJoin('orders', function ($join) {
                   $join->on('transactions.seller_id', '=', 'orders.seller_id');
               })
               ->groupBy('transactions.id')
               ->get();
           $transaction_total_payable = DB::table('orders')->where('seller_id', $id)->where('approval', 1)->sum('bill');

           $transaction_receive_amount = Transaction::where('seller_id', $id)->sum('receive_amount');

           // return and damaged history
           $returns = DB::table('return_product_details')
               ->leftJoin('products', function ($join) {
                   $join->on('return_product_details.product_id', '=', 'products.id');
               })
               ->leftJoin('return_products', function ($join) {
                   $join->on('return_product_details.return_product_id', '=', 'return_products.id');
               })
               ->where(
                   ['return_products.seller_id' => $id,
                       'return_products.type' => 'return',
                       'return_products.approve' => 1]
               )
               ->orderBy('return_product_details.id', 'desc')
               ->groupBy('return_product_details.id')
               ->get();

           $return_products = DB::table('return_product_details')
               ->leftJoin('return_products', function ($join) {
                   $join->on('return_product_details.return_product_id', '=', 'return_products.id');
               })
               ->where(
                   ['return_products.seller_id' => $id,
                       'return_products.type' => 'return',
                       'return_products.approve' => 1]
               )
               ->sum('return_product_details.return_quantity');

           $total_return_amount = DB::table('return_products')
               ->where(['return_products.seller_id' => $id, 'return_products.type' => 'return'])
               ->where('return_products.approve', 1)
               ->sum('return_products.return_amount');

           $total_return_discount_amount = DB::table('return_products')
               ->where(['return_products.seller_id' => $id, 'return_products.type' => 'return'])
               ->where('return_products.approve', 1)
               ->sum('return_products.discount');


           $damages = DB::table('return_product_details')
               ->leftJoin('products', function ($join) {
                   $join->on('return_product_details.product_id', '=', 'products.id');
               })
               ->leftJoin('return_products', function ($join) {
                   $join->on('return_product_details.return_product_id', '=', 'return_products.id');
               })
               ->where(
                   ['return_products.seller_id' => $id,
                       'return_products.type' => 'damage',
                       'return_products.approve' => 1]
               )
               ->orderBy('return_product_details.id', 'desc')
               ->groupBy('return_product_details.id')
               ->get();

           $damage_products = DB::table('return_product_details')
               ->leftJoin('return_products', function ($join) {
                   $join->on('return_product_details.return_product_id', '=', 'return_products.id');
               })
               ->where(
                   ['return_products.seller_id' => $id,
                       'return_products.type' => 'damage',
                       'return_products.approve' => 1]
               )
               ->sum('return_product_details.return_quantity');

           $total_damage_amount = DB::table('return_products')
               ->where(['return_products.seller_id' => $id, 'return_products.type' => 'damage'])
               ->where('return_products.approve', 1)
               ->sum('return_products.return_amount');

           $total_damage_discount_amount = DB::table('return_products')
               ->where(['return_products.seller_id' => $id, 'return_products.type' => 'damage'])
               ->where('return_products.approve', 1)
               ->sum('return_products.discount');

           // order history
           $order_products = DB::table('order_details')
               ->select(
                   'order_details.quantity',
                   'order_details.product_price as price',
                   'order_details.total_price as sub_total',
                   'orders.order_code',
                   'orders.date',
                   'brands.name as brand_name',
                   'products.name as product_name',
               )
               ->leftJoin('products', function ($join) {
                   $join->on('order_details.product_id', '=', 'products.id');
               })
               ->leftJoin('orders', function ($join) {
                   $join->on('order_details.order_id', '=', 'orders.id');
               })
               ->leftJoin('brands', function ($join) {
                   $join->on('order_details.brand_id', '=', 'brands.id');
               })
               ->where(['orders.seller_id' => $id, 'orders.approval' => 1])
               ->where('order_details.quantity', '>', 0)
               ->groupBy('order_details.id')
               ->get();


           $query =  Order::where(['seller_id' => $id, 'approval' => 1]);
           $total_order_bill = $query->sum('bill_without_discount');
           $total_order_discount = $query->sum('total_discount');
           $orders = $query->get();
       }

        return view('admin.overview.history', compact(
            'user',
            'transactions',
            'transaction_total_payable',
            'returns',
            'return_products',
            'total_return_amount',
            'total_return_discount_amount',
            'damages',
            'damage_products',
            'total_damage_amount',
            'total_damage_discount_amount',
            'transaction_receive_amount',
            'total_order_bill',
            'total_order_discount',
            'order_products',
            'role',
            'orders'
        ));
    }
}
