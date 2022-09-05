<?php

namespace App\Http\Controllers\admin;

use App\Brand;
use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\ReturnProducts;
use App\SalesPersion;
use App\Stock;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function show(){
        $brands = Brand::where('status', 1)->get();
        $sellers = User::role('seller')->get();
        return view("admin.report.report", compact('sellers', 'brands'));
    }

    public function reportManage(Request $request){

        $report_type = $request->report_type;
        $date_type = $request->date_type;
        $today_date = $request->today_date;
        $form_date = $request->form_date;
        $to_date = $request->to_date;
        $brand = $request->brand;
        $seller = $request->seller;

        // dd($brand);

        //        date format
        $start_time = ' '.'00:00:00';
        $end_time = ' '.'23:59:59';

        if ($date_type === 'today_date'){
            $dateOne = $today_date.$start_time;
            $dateTwo = $today_date.$end_time;
        }else{
            $dateOne = $form_date.$start_time;
            $dateTwo = $to_date.$end_time;
        }

        // sale report
        if ($report_type === 'sale'){
            $query = DB::table('transactions')
                ->whereBetween('transactions.created_at', [$dateOne, $dateTwo])
                ->select(
                    'transactions.payment_type',
                    'transactions.payable_amount',
                    'transactions.receive_amount',
                    'transactions.due_amount',
                    'banks.name as bank_name',
                    'users.name'
                )

                ->leftJoin('users', function ($join) {
                    $join->on('transactions.seller_id', '=', 'users.id');
                })
                ->leftJoin('banks', function ($join) {
                    $join->on('transactions.bank_id', '=', 'banks.id');
                });

            if ($seller){
               $query = $query->where('transactions.seller_id', $seller);
               $sales = $query->get();

            }else{
               $sales = $query->get();
            }

            $total_payable_amount = $query->sum('transactions.payable_amount');
            $total_receive_amount = $query->sum('transactions.receive_amount');
            $total_due_amount = $query->sum('transactions.due_amount');


            return view('admin.report.sale', compact('total_due_amount','sales', 'total_payable_amount', 'total_receive_amount'));
        }

        // collection report
        if ($report_type === 'collection'){
            $query = DB::table('transactions')
                ->distinct()
                ->whereBetween('transactions.created_at', [$dateOne, $dateTwo])
                ->select(
                    'orders.order_code',
                    'transactions.payment_type',
                    'transactions.payable_amount',
                    'transactions.receive_amount',
                    'transactions.due_amount',
                    'order_details.brand_id',
                    'brands.name as brand_name',
                    'users.name'
                )
                ->leftJoin('users', function ($join) {
                    $join->on('transactions.seller_id', '=', 'users.id');
                })
                ->leftJoin('orders', function ($join) {
                    $join->on('transactions.order_id', '=', 'orders.id');
                })
                ->leftJoin('order_details', function ($join) {
                    $join->on('orders.id', '=', 'order_details.order_id');
                })
                ->leftJoin('brands', function ($join) {
                    $join->on('order_details.brand_id', '=', 'brands.id');
                });

            if ($seller && $brand){
                $query = $query->where(['transactions.seller_id' => $seller, 'order_details.brand_id' => $brand]);
                $collections = $query->get();

            }else{
                $collections = $query->get();
            }

            if ($seller && !$brand){
                $query = $query->where('transactions.seller_id', $seller);
                $collections = $query->get();

            }else{
                $collections = $query->get();
            }

            if ($brand && !$seller){
                $query = $query->where('order_details.brand_id', $brand);
                $collections = $query->get();
            }else{
                $collections = $query->get();
            }

            $payable_amount = $query->sum('transactions.payable_amount');
            $receive_amount = $query->sum('transactions.receive_amount');
            $due_amount = $query->sum('transactions.due_amount');

            return view('admin.report.collection', compact('collections','payable_amount', 'receive_amount', 'due_amount'));
        }


        // stock report
        if ($report_type === 'stock'){

            $query = DB::table('inventories')
                ->distinct()
                ->whereBetween('inventories.created_at', [$dateOne, $dateTwo])
                ->where('inventories.type', 'in')

                ->select(
                    // 'stocks.quantity',
                    'inventories.add_or_less as quantity',
                    'inventories.product_name',
                    'inventories.brand_name',
                    // 'brands.name as brand_name',
                    'inventories.category_name',
                    'inventories.unit_name'
                )
                
                ->leftJoin('stocks', function ($join) {
                    $join->on('stocks.id', '=', 'inventories.stock_id');
                });

            if ($brand){
                $query = $query->where('stocks.brand_id', $brand);
                $stocks = $query->get();
            }else{
                $stocks = $query->get();
            }

            // dd($stocks);

            return view('admin.report.stock', compact('stocks'));
        }


        // return report
        if ($report_type === 'return'){

            
            $query =DB::table('return_products')
                ->distinct()
                ->whereBetween('return_products.created_at', [$dateOne, $dateTwo])
                ->select(
                    'return_products.total_amount',
                    'return_products.discount',
                    'return_products.return_amount',
                    'return_product_details.order_code',
                    'brands.name as brand_name',
                    'users.name'
                )
                ->leftJoin('users', function ($join) {
                    $join->on('return_products.seller_id', '=', 'users.id');
                })
                ->leftJoin('return_product_details', function ($join) {
                    $join->on('return_products.id', '=', 'return_product_details.return_product_id');
                })
                ->leftJoin('brands', function ($join) {
                    $join->on('return_product_details.brand_id', '=', 'brands.id');
                });

                if($query->where('return_products.type','return')){

                    if ($brand && $seller){
                        $query = $query->where(['brands.id' => $brand, 'return_products.seller_id' => $seller]);
                        $returns = $query->get();
                    }else{
                        $returns = $query->get();
                    }

                    if ($seller && !$brand){
                        $query = $query->where('return_products.seller_id', $seller);
                        $returns = $query->get();

                    }else{
                        $returns = $query->get();
                    }

                    if ($brand && !$seller){
                        $query = $query->where('brands.id', $brand);
                        $returns = $query->get();
                    }else{
                        $returns = $query->get();
                    }

            

                    // dd($returns);

                    $total_amount = $query->sum('return_products.total_amount');
                    $discount = $query->sum('return_products.discount');
                    $return_amount = $query->sum('return_products.return_amount');

                    return view('admin.report.return', compact('returns','total_amount', 'discount', 'return_amount'));
                }
        }

        // damage report
        if ($report_type === 'damage'){


            $query =DB::table('return_products')
                ->distinct()
                ->whereBetween('return_products.created_at', [$dateOne, $dateTwo])
                ->select(
                    'return_products.total_amount',
                    'return_products.discount',
                    'return_products.return_amount',
                    'return_product_details.order_code',
                    'brands.name as brand_name',
                    'users.name'
                )
                ->leftJoin('users', function ($join) {
                    $join->on('return_products.seller_id', '=', 'users.id');
                })
                ->leftJoin('return_product_details', function ($join) {
                    $join->on('return_products.id', '=', 'return_product_details.return_product_id');
                })
                ->leftJoin('brands', function ($join) {
                    $join->on('return_product_details.brand_id', '=', 'brands.id');
                });

                
            if($query->where('return_products.type','damage')){

                if ($brand && $seller){
                    $query = $query->where(['brands.id' => $brand, 'return_products.seller_id' => $seller]);
                    $damages = $query->get();
                }else{
                    $damages = $query->get();
                }

                if ($seller && !$brand){
                    $query = $query->where('return_products.seller_id', $seller);
                    $damages = $query->get();

                }else{
                    $damages = $query->get();
                }

                if ($brand && !$seller){
                    $query = $query->where('brands.id', $brand);
                    $damages = $query->get();
                }else{
                    $damages = $query->get();
                }

            


                // dd($damages);

                $total_amount = $query->sum('return_products.total_amount');
                $discount = $query->sum('return_products.discount');
                $return_amount = $query->sum('return_products.return_amount');

                return view('admin.report.damage', compact('damages','total_amount', 'discount', 'return_amount'));
            }

        }
    }
}
