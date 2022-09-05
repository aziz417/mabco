<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Order;
use App\ProductReturnReason;
use App\ReturnProducts;
use App\Stock;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReturnController extends Controller
{
    public function index()
    {
        return view('admin.return.index');
    }

    public function getData()
    {
        $returns = DB::table('return_products')
            ->select(
                'return_products.*',
                'sellers.name as seller_name',
                'retailers.name as retailer_name'
            )
            ->leftJoin('users as sellers', function ($join) {
                $join->on('return_products.seller_id', '=', 'sellers.id');
            })
            ->leftJoin('users as retailers', function ($join) {
                $join->on('return_products.retailer_id', '=', 'retailers.id');
            })
            ->orderBy('return_products.id', 'desc')
            ->get();

        return DataTables::of($returns)
            ->addIndexColumn()
            ->addColumn('approve', function ($returns) {
                if ($returns->approve == 0) {
                    return '<div>
                        <label class="switch patch">
                            <input type="checkbox" class="status_toggle" data-value="' . $returns->id . '" id="status_change" value="' . $returns->id . '">
                            <span class="slider"></span>
                        </label>
                        </div>';
                } else {
                    return '<div>
                    <label class="switch patch custom_disabled">
                        <input type="checkbox" id="status_change"  class="status_toggle" data-value="' . $returns->id . '"  value="' . $returns->id . '" checked>
                        <span class="slider"></span>
                    </label>
                    </div>';
                }
            })
            ->editColumn('action', function ($returns) {
                $return = "<div class=\"btn-group\">";
                if (!empty($returns->return_code)) {
                    if ($returns->approve == 1) {
                        $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/return/show/$returns->id\" style='margin-right: 5px' class=\"btn btn-sm btn-primary\"><i class='fa fa-eye'></i></a>
                    </div>
                            ";
                    } else {
                        $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/return/show/$returns->id\" style='margin-right: 5px' class=\"btn btn-sm btn-primary\"><i class='fa fa-eye'></i></a>
                        ||
                        <a href=\"/return/edit/$returns->id\" id='edit-$returns->id' style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                    </div>
                            ";
                    }
                }
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'approve', 'action'
            ])
            ->make(true);
    }

    public function statusChange($id)
    {
        $returnP = ReturnProducts::findOrFail($id);

        if ($returnP->approve == 0) {
            // total out standing update here
            $user = User::where(['seller_id' => $returnP->seller_id, 'id' => $returnP->retailer_id])->first();
            $user->total_out_standing = $user->total_out_standing - $returnP->return_amount;
            $user->save();

            $return_able_all_products = DB::table('return_product_details')
                ->where('return_product_id', $returnP->id)
                ->get();

            foreach ($return_able_all_products as $return_able_all_product) {
                //product quantity and sub-total update from order details table
                $order_details = DB::table('order_details')->where(
                    ['product_id' => $return_able_all_product->product_id,
                        'order_id' => $return_able_all_product->order_id,
                        'category_id' => $return_able_all_product->category_id,
                        'brand_id' => $return_able_all_product->brand_id,
                    ])->first();

                if ($order_details) {
                    $subTotal = $return_able_all_product->return_quantity * $return_able_all_product->product_price;

                    DB::table('order_details')->where(
                        ['product_id' => $return_able_all_product->product_id,
                            'order_id' => $return_able_all_product->order_id,
                            'category_id' => $return_able_all_product->category_id,
                            'brand_id' => $return_able_all_product->brand_id,
                        ])
                        ->update([
                            'quantity' => $order_details->quantity - $return_able_all_product->return_quantity,
                            'total_price' => $order_details->total_price - $subTotal,
                        ]);
                }

                // if type is return update stock this product quantity
                if ($returnP->type === 'return') {
                    $stock = Stock::where(
                        ['product_id' => $return_able_all_product->product_id,
                            'category_id' => $return_able_all_product->category_id,
                            'brand_id' => $return_able_all_product->brand_id,
                        ])->first();

                    $stock->quantity = $stock->quantity + $return_able_all_product->return_quantity;
                    $stock->save();
                }
            }

            $returnP->update(['approve' => 1]);

            return response()->json([
                'message' => 'Return/Damage is active'
            ], Response::HTTP_OK);
        } else {
            // total out standing update here
            $user = User::where(['seller_id' => $returnP->seller_id, 'id' => $returnP->retailer_id])->first();
            $user->total_out_standing = $user->total_out_standing + $returnP->return_amount;
            $user->save();

            $return_able_all_products = DB::table('return_product_details')
                ->where('return_product_id', $returnP->id)
                ->get();

            foreach ($return_able_all_products as $return_able_all_product) {
                //product quantity and sub-total update from order details table
                $order_details = DB::table('order_details')->where(
                    ['product_id' => $return_able_all_product->product_id,
                        'order_id' => $return_able_all_product->order_id,
                        'category_id' => $return_able_all_product->category_id,
                        'brand_id' => $return_able_all_product->brand_id,
                    ])->first();

                if ($order_details) {
                    $subTotal = $return_able_all_product->return_quantity * $return_able_all_product->product_price;

                    DB::table('order_details')->where(
                        ['product_id' => $return_able_all_product->product_id,
                            'order_id' => $return_able_all_product->order_id,
                            'category_id' => $return_able_all_product->category_id,
                            'brand_id' => $return_able_all_product->brand_id,
                        ])
                        ->update([
                            'quantity' => $order_details->quantity + $return_able_all_product->return_quantity,
                            'total_price' => $order_details->total_price + $subTotal,
                        ]);
                }

                // if type is return update stock this product quantity
                if ($returnP->type === 'return') {
                    $stock = Stock::where(
                        ['product_id' => $return_able_all_product->product_id,
                            'category_id' => $return_able_all_product->category_id,
                            'brand_id' => $return_able_all_product->brand_id,
                        ])->first();

                    $stock->quantity = $stock->quantity - $return_able_all_product->return_quantity;
                    $stock->save();
                }
            }

            $returnP->update(['approve' => 0]);

            return response()->json([
                'message' => 'Return/Damage is inactive'
            ], Response::HTTP_OK);
        }
    }

    public function create()
    {
        $sellers = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'users.status as status',
                'roles.name as role_name'
            )
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'seller')
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();

        $sellers = $sellers->filter(function ($seller) {
            $orderTableExistSeller = Order::where('seller_id', $seller->id)->first();
            if ($orderTableExistSeller) {
                return $orderTableExistSeller;
            }
        });

        return view('admin.return.create', compact('sellers'));
    }

    public function retailerWiseProducts(Request $request)
    {
        $seller_id = $request->seller_id;
        $retailer_id = $request->retailer_id;
        $type = $request->type;
        $user = User::where(['seller_id' => $request->seller_id, 'id' => $request->retailer_id])->first();
        $total_out_standing = $user->total_out_standing;

        $products = DB::table('order_details')
            ->select(
                'orders.order_code as order_code',
                'orders.id as order_id',
                'orders.seller_id as seller_id',
                'orders.retailer_id as retailer_id',
                'brands.name as brand_name',
                'brands.id as brand_id',
                'categories.name as category_name',
                'categories.id as category_id',
                'products.name as product_name',
                'products.id as product_id',
                'units.name as unit_name',
                'units.id as unit_id',
                'order_details.product_price as price',
                'order_details.quantity as quantity',
                'order_details.total_price as total_price',
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
            ->leftJoin('categories', function ($join) {
                $join->on('order_details.category_id', '=', 'categories.id');
            })
            ->leftJoin('units', function ($join) {
                $join->on('order_details.unit_id', '=', 'units.id');
            })
            ->where('order_details.quantity', '>', 0)
            ->where('orders.approval', 1)
            ->where('orders.cancel', 0)
            ->where(['orders.seller_id' => $seller_id, 'orders.retailer_id' => $retailer_id])
            ->orderBy('order_details.id', 'desc')
            ->get();

        $sellers = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'users.status as status',
                'roles.name as role_name'
            )
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'seller')
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();

        $sellers = $sellers->filter(function ($seller) {
            $orderTableExistSeller = Order::where('seller_id', $seller->id)->first();
            if ($orderTableExistSeller) {
                return $orderTableExistSeller;
            }
        });

        $retailers = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'users.status as status',
                'roles.name as role_name'
            )
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'retailer')
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();

        $retailers = $retailers->filter(function ($retailer) {
            $orderTableExistRetailer = Order::where('retailer_id', $retailer->id)->first();
            if ($orderTableExistRetailer) {
                return $orderTableExistRetailer;
            }
        });

        $total_sub_total = DB::table('order_details')
            ->leftJoin('orders', function ($join) {
                $join->on('order_details.order_id', '=', 'orders.id');
            })
            ->where('order_details.quantity', '>', 0)
            ->where(['orders.seller_id' => $seller_id, 'orders.retailer_id' => $retailer_id, 'orders.approval' => 1])
            ->sum('total_price');

        $return_reasons = ProductReturnReason::where('status', 1)->get();


        return view('admin.return.create', compact('return_reasons', 'total_sub_total', 'total_out_standing', 'seller_id', 'retailer_id', 'type', 'products', 'sellers', 'retailers'));
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            DB::beginTransaction();

            try {
                $return_code = ReturnProducts::orderBy('id', 'desc')->first();

                if ($return_code == null) {
                    $return_code = 1000;
                }else{
                    $return_code = ReturnProducts::orderBy('id', 'desc')->first()->return_code;
                }

                $return_product = ReturnProducts::create([
                    'return_code' => $return_code + 1,
                    'seller_id' => $request->seller_id,
                    'retailer_id' => $request->retailer_id,
                    'type' => $request->type,
                    'approve' => 0,
                    'total_amount' => $request->total_amount,
                    'commission_type' => $request->commission_type,
                    'commission_value' => $request->commission_value,
                    'discount' => $request->discount,
                    'return_amount' => $request->return_amount,
                    'date' => date('Y-m-d'),
                ]);

                foreach ($request->product_id as $key => $item) {
                    if (!empty($request->return_quantity[$key]) && $request->return_quantity[$key] > 0) {

                        DB::table('return_product_details')->insert([
                            'return_product_id' => $return_product->id,
                            'order_code' => $request->order_code[$key],
                            'brand_id' => $request->brand_id[$key],
                            'category_id' => $request->category_id[$key],
                            'order_id' => $request->order_id[$key],
                            'product_id' => $item,
                            'order_quantity' => $request->order_quantity[$key],
                            'product_return_reason_id' => $request->product_return_reason_id[$key],
                            'unit_id' => $request->unit_id[$key],
                            'product_price' => $request->product_price[$key],
                            'return_quantity' => $request->return_quantity[$key],
                            'sub_total_price' => $request->sub_total_price[$key],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }

                DB::commit();

                return response()->json([
                    'message' => 'Return Product store successful'
                ], Response::HTTP_OK);

            } catch (QueryException $e) {
                DB::rollBack();

                $error = $e->getMessage();

                return response()->json([
                    'error' => $error
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function show($id)
    {
        $return = ReturnProducts::findOrFail($id);
        $seller = User::where('seller_id', $return->seller_id)->first();
        $retailer = User::where('id', $return->retailer_id)->first();

        $products = DB::table('return_product_details')
            ->select(
                'return_product_details.*',
                'brands.name as brand_name',
                'categories.name as category_name',
                'products.name as product_name',
                'units.name as unit_name',
                'product_return_reasons.title as reason_title',
                'order_details.quantity as quantity',
            )
            ->leftJoin('products', function ($join) {
                $join->on('return_product_details.product_id', '=', 'products.id');
            })
            ->leftJoin('orders', function ($join) {
                $join->on('return_product_details.order_id', '=', 'orders.id');
            })
            ->leftJoin('order_details', function ($join) {
                $join->on('return_product_details.order_id', '=', 'order_details.order_id');
            })
            ->leftJoin('product_return_reasons', function ($join) {
                $join->on('return_product_details.product_return_reason_id', '=', 'product_return_reasons.id');
            })
            ->leftJoin('brands', function ($join) {
                $join->on('return_product_details.brand_id', '=', 'brands.id');
            })
            ->leftJoin('categories', function ($join) {
                $join->on('return_product_details.category_id', '=', 'categories.id');
            })
            ->leftJoin('units', function ($join) {
                $join->on('return_product_details.unit_id', '=', 'units.id');
            })
            ->where('return_product_details.return_product_id', $id)
            ->groupBy('return_product_details.id')
            ->get();

        return view('admin.return.show', compact('seller', 'retailer', 'products', 'return'));
    }

    public function edit($id)
    {
        $return = ReturnProducts::findOrfail($id);
        $seller_id = $return->seller_id;
        $retailer_id = $return->retailer_id;
        $type = $return->type;
        $user = User::where(['seller_id' => $return->seller_id, 'id' => $return->retailer_id])->first();
        $total_out_standing = $user->total_out_standing;

        $products = DB::table('return_product_details')
            ->select(
                'return_product_details.*',
                'orders.order_code as order_code',
                'orders.id as order_id',
                'orders.seller_id as seller_id',
                'orders.retailer_id as retailer_id',
                'brands.name as brand_name',
                'brands.id as brand_id',
                'categories.name as category_name',
                'categories.id as category_id',
                'products.name as product_name',
                'products.id as product_id',
                'units.name as unit_name',
                'units.id as unit_id',
                'return_product_details.product_price as price',
                'return_product_details.order_quantity as order_quantity',
                'return_product_details.return_quantity as return_quantity',
                'return_product_details.sub_total_price as total_price',
            )
            ->leftJoin('products', function ($join) {
                $join->on('return_product_details.product_id', '=', 'products.id');
            })
            ->leftJoin('orders', function ($join) {
                $join->on('return_product_details.order_id', '=', 'orders.id');
            })
            ->leftJoin('brands', function ($join) {
                $join->on('return_product_details.brand_id', '=', 'brands.id');
            })
            ->leftJoin('categories', function ($join) {
                $join->on('return_product_details.category_id', '=', 'categories.id');
            })
            ->leftJoin('units', function ($join) {
                $join->on('return_product_details.unit_id', '=', 'units.id');
            })
            ->where('return_product_details.return_product_id', $return->id)
            ->get();

        $sellers = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'users.status as status',
                'roles.name as role_name'
            )
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'seller')
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();

        $sellers = $sellers->filter(function ($seller) {
            $orderTableExistSeller = Order::where('seller_id', $seller->id)->first();
            if ($orderTableExistSeller) {
                return $orderTableExistSeller;
            }
        });

        $retailers = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'users.status as status',
                'roles.name as role_name'
            )
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'retailer')
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();

        $retailers = $retailers->filter(function ($retailer) {
            $orderTableExistRetailer = Order::where('retailer_id', $retailer->id)->first();
            if ($orderTableExistRetailer) {
                return $orderTableExistRetailer;
            }
        });

        $total_sub_total = DB::table('return_product_details')
            ->where('return_product_id', $id)
            ->sum('sub_total_price');

        $return_reasons = ProductReturnReason::where('status', 1)->get();

        return view('admin.return.edit', compact('return', 'return_reasons', 'total_sub_total', 'total_out_standing', 'seller_id', 'retailer_id', 'type', 'products', 'sellers', 'retailers'));
    }

    public function update(Request $request, $id)
    {
        if ($request->isMethod('PUT')) {
            DB::beginTransaction();

            try {
                $return_product = ReturnProducts::findOrFail($id);
                $return_product->update([
                    'seller_id' => $request->seller_id,
                    'retailer_id' => $request->retailer_id,
                    'type' => $request->type,
                    'approve' => 0,
                    'total_amount' => $request->total_amount,
                    'commission_type' => $request->commission_type,
                    'commission_value' => $request->commission_value,
                    'discount' => $request->discount,
                    'return_amount' => $request->return_amount,
                ]);

                $return_product_details = DB::table('return_product_details')
                    ->where('return_product_id', $id)->get();

                foreach ($return_product_details as $key => $return_product_detail) {
                    $return_product_d = DB::table('return_product_details')
                        ->where('id', $return_product_detail->id)->update([
//                        'brand_id' => $request->brand_id[$key],
//                        'category_id' => $request->category_id[$key],
//                        'order_id' => $request->order_id[$key],
//                        'product_id' => $request->product_id[$key],
//                        'order_quantity' => $request->order_quantity[$key],
//                        'product_return_reason_id' => $request->product_return_reason_id[$key],
//                        'unit_id' => $request->unit_id[$key],
//                        'product_price' => $request->product_price[$key],
                        'return_quantity' => $request->return_quantity[$key],
                        'sub_total_price' => $request->sub_total_price[$key],
                        'updated_at' => Carbon::now(),
                    ]);
                }


                DB::commit();

                return response()->json([
                    'message' => 'Return Product store successful'
                ], Response::HTTP_OK);

            } catch (QueryException $e) {
                DB::rollBack();

                $error = $e->getMessage();

                return response()->json([
                    'error' => $error
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function returnRetailerList(Request $request)
    {
        $seller_id = $request->seller_id;

        $retailers = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'users.status as status',
                'roles.name as role_name'
            )
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'retailer')
            ->where('users.seller_id', '=', $seller_id)
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();

        $retailers = $retailers->filter(function ($retailer) {
            $orderTableExistRetailer = Order::where('retailer_id', $retailer->id)->where('approval', 1)->first();
            if ($orderTableExistRetailer) {
                return $orderTableExistRetailer;
            }
        })->values();

        return $retailers;
    }
}
