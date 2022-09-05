<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\LargeTransaction;
use App\Order;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LargeTransactionController extends Controller
{
    public function index()
    {
        $count = \App\LargeTransaction::count();
        $sellers = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'retailer.total_out_standing as total_out_standing',
                'users.status as status',
                'roles.name as role_name'
            )
            ->leftJoin('users as retailer', function ($join) {
                $join->on('users.seller_id', '=', 'retailer.id');
            })
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'seller')
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();

        return view('admin.large_transaction.index', compact('sellers', 'count'));
    }

    public function getData()
    {
        $largeTransaction = LargeTransaction::select('large_transactions.*',
            'seller.name as seller_name',
            'retailer.total_out_standing as total_out_standing'
        )
            ->leftJoin('users as seller', function ($join) {
                $join->on('large_transactions.seller_id', '=', 'seller.id');
            })
            ->leftJoin('users as retailer', function ($join) {
                $join->on('large_transactions.retailer_id', '=', 'retailer.id');
            })
            ->latest()
            ->get();

        $largeTransaction = $largeTransaction->map(function ($lt) {
            $lt->now_out_standing = $lt->old_out_standing - $lt->amount;
            $lt->total_out_standing = $lt->now_out_standing + $lt->amount;
            return $lt;
        });

        return DataTables::of($largeTransaction)
            ->addIndexColumn()
            ->editColumn('action', function ($order) {
                $return = "<div class=\"btn-group\">";
                if (!empty($largeTransaction->id)) {
                    if ($order->approval != 1){
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

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer',
        ]);

        if ($request->isMethod('post')) {
            DB::beginTransaction();

            try {

                $transaction = new LargeTransaction();
                $transaction->retailer_id = json_encode($request->retailer_id);
                $transaction->seller_id = $request->seller_id;
                $transaction->date = $request->date;
                $transaction->old_out_standing = $request->old_out_standing;
                $transaction->amount = $request->amount;
                $transaction->transaction_number = $request->transaction_number;

                $transaction->save();

                // this retailer order_payable_amount update
                $transaction_amount = $request->amount;
                if ($transaction_amount > 0) {
                    foreach ($request->retailer_id as $retailer) {
                        if ($transaction_amount > 0) {
                            $orders = Order::where(['seller_id' => $request->seller_id, 'retailer_id' => $retailer])
                                ->where('order_payable_amount', '>', 0)->get();
                            if ($orders) {
                                foreach ($orders as $order) {
                                    $order_payable_amount = $order->order_payable_amount;
                                    if ($transaction_amount > 0) {
                                        if ($order && $order_payable_amount > 0) {
                                            if ($transaction_amount >= $order_payable_amount) {

                                                $transaction_amount = $transaction_amount - $order_payable_amount;
                                                $order->order_payable_amount = 0;
                                                $order->save();

                                                $user = User::where(['seller_id' => $order->seller_id, 'id' => $order->retailer_id])->first();
                                                $user->total_out_standing = $user->total_out_standing - $order_payable_amount;
                                                $user->save();

                                            } else {

                                                $new_order_payable_amount = $order_payable_amount - $transaction_amount;
                                                $order->order_payable_amount = $new_order_payable_amount;
                                                $order->save();

                                                $user = User::where(['seller_id' => $order->seller_id, 'id' => $order->retailer_id])->first();
                                                $user->total_out_standing = $user->total_out_standing - $transaction_amount;
                                                $user->save();

                                                $transaction_amount = 0;
                                            }
                                        }
                                    } else {
                                        break;
                                    }
                                }
                            }

                        } else {
                            break;
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'message' => 'Transaction store successful'
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

    public function largeTransactionRetailerList(Request $request)
    {
        $seller_id = $request->seller_id;

        $query = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'users.total_out_standing',
                'users.status as status',
                'roles.name as role_name'
            )
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'retailer')
            ->where('users.seller_id', '=', $seller_id)
            ->where('users.total_out_standing', '>', 0)
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc');
        $total_old_out_standing = $query->sum('total_out_standing');
        $retailers = $query->get();

        return ['totalOldOutStanding' => $total_old_out_standing, 'retailers' => $retailers];
    }

}
