<?php

namespace App\Http\Controllers\admin;

use App\Brand;
use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockRequest;
use App\Inventory;
use App\Product;
use App\Stock;
use App\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = DB::table('products')->select('id', 'name')->latest()->get();

//        $products = $products->filter(function ($product, $key) {
//            $stockExist = Stock::where('product_id', $product->id)->first();
//            if (!$stockExist) {
//                return $product;
//            }
//        });

        return view('admin.stock.index', compact(
            'products',
        ));
    }


    public function getData()
    {

        $stock = Stock::select(
            'stocks.id',
            'products.name as product_name',
            'categories.name as category_name',
            'brands.name as brand_name',
            'units.name as unit_name',
            'stocks.quantity',
            DB::raw('products.price * stocks.quantity AS total_price')
        )
            ->leftJoin('products', function ($join) {
                $join->on('stocks.product_id', '=', 'products.id');
            })
            ->leftJoin('categories', function ($join) {
                $join->on('stocks.category_id', '=', 'categories.id');
            })
            ->leftJoin('brands', function ($join) {
                $join->on('stocks.brand_id', '=', 'brands.id');
            })
            ->leftJoin('units', function ($join) {
                $join->on('stocks.unit_id', '=', 'units.id');
            })
            ->orderBy('stocks.id', 'desc')
            ->get();

        return DataTables::of($stock)
            ->addIndexColumn()
            ->editColumn('action', function ($stock) {
                $return = "<div class=\"btn-group\">";
                if (!empty($stock->id)) {
                    $return .= "
                        <a href=\"/stock/edit/$stock->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                        ||
                            <a rel=\"$stock->id\" rel1=\"stock/destroy\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-danger deleteRecord \"><i class='fa fa-trash'></i></a>
                                ";
                }
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'action'
            ])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockRequest $request)
    {
        if ($request->isMethod('post')) {
            DB::beginTransaction();
            try {

                foreach ($request->product_id as $key => $product) {
                    $stock = Stock::where('product_id', $product)->latest()->first();
                    if ($stock){
                        $previousQuantity = $stock->quantity;
                        $stock->update([
                            'quantity' => $request->quantity[$key]+$previousQuantity,
                        ]);
                    }else{
                        $stock = Stock::create([
                            'product_id'  => $product,
                            'category_id' => $request->category_id[$key],
                            'brand_id' => $request->brand_id[$key],
                            'unit_id'  => $request->unit_id[$key],
                            'quantity' => $request->quantity[$key],
                        ]);
                    }

                    // query
                    $product       = Product::where('id', $product)->select('name', 'price')->first();
                    $category_name = Category::where('id', $request->category_id[$key])->first()->name;
                    $brand_name = Brand::where('id', $request->brand_id[$key])->first()->name;
                    $unit_name  = Unit::where('id', $request->unit_id[$key])->first()->name;

                    Inventory::create([
                        'stock_id' => $stock->id,
                        'user_id'  => Auth()->user()->id,
                        'product_name'  => $product['name'],
                        'category_name' => $category_name,
                        'brand_name' => $brand_name,
                        'unit_name'  => $unit_name,
                        'old_quantity' => 0,
                        'add_or_less' => $request->quantity[$key],
                        'now_quantity' => $request->quantity[$key],
                        'type'   => 'in',
                        'price'  => $product['price'],
                        'amount' => $request->quantity[$key] * $product['price'],
                    ]);
                }

                DB::commit();

                return response()->json([
                    'message' => 'Stock store successful'
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

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $products = DB::table('products')->select('id', 'name')->latest()->get();
        $categories = DB::table('categories')->select('id', 'name')->latest()->get();
        $brands = DB::table('brands')->select('id', 'name')->latest()->get();
        $units = DB::table('units')->select('id', 'name')->latest()->get();
        $stock = Stock::findOrFail($id);
        return view('admin.stock.edit', compact(
            'products',
            'brands',
            'units',
            'categories',
            'stock'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StockRequest $request, $id)
    {
        if ($request->_method == 'PUT') {
            DB::beginTransaction();

            try {

                $stock = Stock::findOrFail($id);

                //

                $product       = Product::where('id', $request->product_id)->select('name', 'price')->first();
                $category_name = Category::where('id', $request->category_id)->first()->name;
                $brand_name = Brand::where('id', $request->brand_id)->first()->name;
                $unit_name  = Unit::where('id', $request->unit_id)->first()->name;

                if ($stock->quantity < $request->quantity){
                    //$add_quantity = $request->quantity - $stock->quantity;
                    $add_quantity = $request->quantity - $stock->quantity;

                    Inventory::create([
                        'stock_id' => $stock->id,
                        'user_id'  => Auth()->user()->id,
                        'product_name'  => $product['name'],
                        'category_name' => $category_name,
                        'brand_name' => $brand_name,
                        'unit_name'  => $unit_name,
                        'old_quantity' => $stock->quantity,
                        'add_or_less' => $add_quantity,
                        'now_quantity' => $request->quantity,
                        'type'   => 'in',
                        'price'  => $product['price'],
                        'amount' => $request->quantity * $product['price'],
                    ]);
                }else{
                    if ($request->reduce_quantity){
                        $inventory = Inventory::where('stock_id', $stock->id)->latest()->first();
                        $less_quantity =  $stock->quantity - $request->quantity;

                        $inventory->update([
                            'stock_id' => $stock->id,
                            'user_id'  => Auth()->user()->id,
                            'product_name'  => $product['name'],
                            'category_name' => $category_name,
                            'brand_name' => $brand_name,
                            'unit_name'  => $unit_name,
                            'old_quantity' => $stock->quantity,
                            'add_or_less' => $inventory->add_or_less - $less_quantity,
                            'now_quantity' => $request->quantity,
                            'type'   => 'in',
                            'price'  => $product['price'],
                            'amount' => $request->quantity * $product['price'],
                        ]);
                    }
                }

                $stock->product_id = $request->product_id;
                $stock->category_id = $request->category_id;
                $stock->brand_id = $request->brand_id;
                $stock->unit_id = $request->unit_id;
                $stock->quantity = $request->quantity;

                $stock->save();

                DB::commit();

                return response()->json([
                    'message' => 'Stock updated successful'
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();

        return response()->json([
            'message' => 'Stock destroy successful'
        ], Response::HTTP_OK);
    }

    public function stockProductCategoryBrand(Request $request)
    {
        $id = $request->id;
        $productPriceCategoryBrand = DB::table('products')->where('products.id', $id)->select(
            'categories.name as category_name',
            'categories.id as category_id',
            'brands.name as brand_name',
            'brands.id as brand_id',
            'units.name as unit_name',
            'units.id as unit_id'
        )
            ->leftJoin('categories', function ($join) {
                $join->on('products.category_id', '=', 'categories.id');
            })
            ->leftJoin('brands', function ($join) {
                $join->on('products.brand_id', '=', 'brands.id');
            })
            ->leftJoin('units', function ($join) {
                $join->on('products.unit_id', '=', 'units.id');
            })
            ->first();
        $data = ['category_brand' => $productPriceCategoryBrand];
        return $data;
    }
}
