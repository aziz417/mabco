<?php

namespace App\Http\Controllers\admin;

use App\Brand;
use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Product;
use App\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Image;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.product.index');
    }

    public function getData()
    {
        $product = Product::select('products.*','users.name as user_name','categories.name as category_name','brands.name as brand_name', 'units.name as unit_name')
                    ->leftJoin('users', function($join){
                        $join->on('products.user_id','=','users.id');
                    })
                    ->leftJoin('categories', function($join){
                        $join->on('products.category_id','=','categories.id');
                    })
                    ->leftJoin('brands', function($join){
                        $join->on('products.brand_id','=','brands.id');
                    })
                    ->leftJoin('units', function($join){
                        $join->on('products.unit_id','=','units.id');
                    })
                    ->orderBy('products.id','desc')
                    ->get();

        return DataTables::of($product)
        ->addIndexColumn()
        ->addColumn('approve',function ($product){
            if($product->approve == 0)
            {

                return '<div>
                        <label class="switch patch">
                            <input type="checkbox" class="status_toggle" data-value="'.$product->id.'" id="status_change" value="'.$product->id.'">
                            <span class="slider"></span>
                        </label>
                        </div>';
            }else{
                return '<div>
                    <label class="switch patch">
                        <input type="checkbox" id="status_change"  class="status_toggle" data-value="'.$product->id.'"  value="'.$product->id.'" checked>
                        <span class="slider"></span>
                    </label>
                    </div>';
            }

        })
        ->editColumn('action', function ($product) {
            $return = "<div class=\"btn-group\">";
            if (!empty($product->id))
            {
                $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/product/edit/$product->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                        ||
                        <a rel=\"$product->id\" rel1=\"product/destroy\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-danger deleteRecord \"><i class='fa fa-trash'></i></a>
                    </div>


                            ";
            }
            $return .= "</div>";
            return $return;
        })
        ->rawColumns([
            'action','approve'
        ])
        ->make(true);
    }

    public function create()
    {
        $categories = Category::latest()->get();
        $brand = Brand::latest()->get();
        $units = Unit::latest()->get();

        return view('admin.product.create', compact('units','categories','brand'));
    }

    public function store(ProductRequest $request)
    {
        if($request->isMethod('post'))
        {
            DB::beginTransaction();

            try{

                $product = new Product();

                $product->user_id = Auth::id();
                $product->category_id = $request->category_id;
                $product->unit_id = $request->unit_id;
                $product->brand_id = $request->brand_id;
                $product->name = $request->name;
                $product->price = $request->price;
                $product->approve = 1;

                $product->save();

                DB::commit();

                return response()->json([
                    'message' => 'Product store successful'
                ],Response::HTTP_CREATED);

            }catch(QueryException $e){
                DB::rollBack();

                $error = $e->getMessage();

                return response()->json([
                    'error' => $error
                ],Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function edit($id)
    {
        $categories = Category::latest()->get();
        $brand = Brand::latest()->get();
        $product = Product::findOrFail($id);
        $units = Unit::latest()->get();


        return view('admin.product.edit', compact('categories','brand','product', 'units'));
    }

    public function update(ProductRequest $request, $id)
    {
        if($request->_method == 'PUT')
        {
            DB::beginTransaction();

            try{

                $product = Product::findOrFail($id);

                $product->user_id = Auth::id();
                $product->unit_id = $request->unit_id;
                $product->category_id = $request->category_id;
                $product->brand_id = $request->brand_id;
                $product->name = $request->name;
                $product->price = $request->price;

                $product->save();

                DB::commit();

                return response()->json([
                    'message' => 'Product updated successful'
                ],Response::HTTP_OK);


            }catch(QueryException $e){
                DB::rollBack();

                $error = $e->getMessage();

                return response()->json([
                    'error' => $error
                ],Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successful'
        ],Response::HTTP_OK);
    }

    public function statusChange($id)
    {
        $product = Product::findOrFail($id);

        if($product->approve == 0)
        {
            $product->update(['approve' => 1]);

            return response()->json([
                'message' => 'Product is approve'
            ],Response::HTTP_OK);
        }else{
            $product->update(['approve' => 0]);

            return response()->json([
                'message' => 'Product approve is cancel'
            ],Response::HTTP_OK);
        }
    }
}
