<?php

namespace App\Http\Controllers\admin;

use App\Bank;
use App\Http\Controllers\Controller;
use App\LargeTransaction;
use App\Order;
use App\Transaction;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    public function index()
    {
        return view('admin.order-manage.transaction.index');
    }


    public function getData()
    {
        $order = Order::select('orders.*',
            'user1.name as seller_name',
            'user2.name as retailer_name',
            'user3.total_out_standing as total_out_standing',
            'user3.name as retailer_name'
            )

        ->leftJoin('users as user1', function ($join) {
            $join->on('orders.seller_id', '=', 'user1.id');
        })

        ->leftJoin('users as user2', function ($join) {
            $join->on('orders.retailer_id', '=', 'user2.id');
        })
        ->leftJoin('users as user3', function ($join) {
            $join->on('orders.retailer_id', '=', 'user3.id');
        })
        ->orderBy('orders.id', 'desc')
        ->where('orders.approval','=',1)
        ->get();

        $order = $order->map(function ($or){
            // $or->payable_amount = $or->bill_without_discount - $or->order_payable_amount;
            $or->payable_amount = $or->bill - $or->order_payable_amount;
            return $or;
        });

    return DataTables::of($order)
        ->addIndexColumn()
        ->editColumn('action', function ($order) {
            $return = "<div class=\"btn-group\">";
            if (!empty($order->id)) {
                if($order->order_payable_amount > 0){
                    $return .= "
                <div class=\"btn-group\">
                    <a href=\"/transaction/create/$order->id\" style='margin-right: 5px' class=\"btn btn-sm btn-primary\"><i class='fa fa-plus'></i></a>
                </div>";
                }else{
                    $return .= "
                <div class=\"btn-group\">
                    <a href=\"/transaction/create/$order->id\" style='margin-right: 5px' class=\"btn btn-sm btn-primary d-none\"><i class='fa fa-plus'></i></a>
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

    public function create($id)
    {
        $order = Order::select('orders.*')
                    ->leftJoin('users', function($join){
                        $join->on('orders.seller_id','=','users.id');
                    })
                    ->where('orders.id', $id)
                    ->orderBy('orders.id','desc')
                    ->first();

        $sellers = User::role('seller')->get();
        $retailers = User::role('retailer')->get();

        $old_out_standing = User::where('id', $order->retailer_id)->first()->total_out_standing ?? 0;

        $banks = Bank::all();

        return view('admin.order-manage.transaction.transaction-index', compact(
            'sellers',
            'retailers',
            'banks',
            'old_out_standing',
            'order'
        ));
    }

    public function transactionGetData($id, $retailer_id){
        $transactions = Transaction::select('transactions.*',
            'banks.name'
        )

            ->leftJoin('banks', function ($join) {
                $join->on('transactions.bank_id', '=', 'banks.id');
            })
            ->orderBy('transactions.due_amount', 'desc')
            ->where('transactions.order_id', $id)
            ->where('transactions.retailer_id', $retailer_id)
            ->get();

        return DataTables::of($transactions)
            ->addIndexColumn()
            ->editColumn('action', function ($transactions) {
                $return = "<div class=\"btn-group\">";
                if (!empty($transactions->id)) {
                    $return .= "
                <div class=\"btn-group\">
                    <a href=\"/transaction/create/$transactions->id\" style='margin-right: 5px' class=\"btn btn-sm btn-primary\"><i class='fa fa-plus'></i></a>
                </div>";
                }
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'action'
            ])
            ->make(true);
    }

    // public function sellerData()
    // {
    //     $seller_id = $_GET['seller_id'];
    //     $order_id = $_GET['order_id'];

    //     $seller_order = Order::where('id', $order_id)->where('seller_id', $seller_id)->latest()->first();

    //     return response()->json([
    //         'seller_order' => $seller_order
    //     ],Response::HTTP_OK);
    // }

    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            DB::beginTransaction();
            try {

                $transaction = new Transaction();
                $transaction->date = $request->date;
                $transaction->seller_id = $request->seller_id;
                $transaction->retailer_id = $request->retailer_id;
                $transaction->order_id = $request->order_id;
                $transaction->payment_type = $request->payment_type;
                $transaction->payable_amount = $request->payable_amount;
                $transaction->receive_amount = $request->receive_amount;
                $transaction->due_amount = $request->due_amount;
                $transaction->bank_id = $request->bank_id;

                $transaction->save();

                $order = Order::where('id', $request->order_id)->first();
                if ($order->order_payable_amount > 0){
                    $order->order_payable_amount = $order->order_payable_amount - $request->receive_amount;
                    $order->save();
                }

                if($request->receive_amount){
                    $user = User::where(['seller_id' => $request->seller_id, 'id' => $request->retailer_id])->first();
                    $user->total_out_standing =  $user->total_out_standing - $request->receive_amount;
                    $user->save();
                }


                DB::commit();

                return response()->json([
                    'message' => 'Transaction store Successful'
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

}
