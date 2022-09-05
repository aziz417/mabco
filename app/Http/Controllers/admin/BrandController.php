<?php

namespace App\Http\Controllers\admin;

use App\Brand;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function index()
    {
        return view('admin.brand.index');
    }

    public function getData()
    {
        $brand = Brand::latest()->get();

        return DataTables::of($brand)
        ->addIndexColumn()
        ->addColumn('status',function ($brand){
            if($brand->status == 0)
            {

                return '<div>
                        <label class="switch patch">
                            <input type="checkbox" class="status_toggle" data-value="'.$brand->id.'" id="status_change" value="'.$brand->id.'">
                            <span class="slider"></span>
                        </label>
                        </div>';
            }else{
                return '<div>
                    <label class="switch patch">
                        <input type="checkbox" id="status_change"  class="status_toggle" data-value="'.$brand->id.'"  value="'.$brand->id.'" checked>
                        <span class="slider"></span>
                    </label>
                    </div>';
            }

        })
        ->editColumn('action', function ($brand) {
            $return = "<div class=\"btn-group\">";
            if (!empty($brand->name))
            {
                $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/brand/edit/$brand->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                        ||
                        <a rel=\"$brand->id\" rel1=\"brand/destroy\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-danger deleteRecord \"><i class='fa fa-trash'></i></a>
                    </div>


                            ";
            }
            $return .= "</div>";
            return $return;
        })
        ->rawColumns([
            'action','status'
        ])
        ->make(true);
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:200',
        ]);

        if($request->isMethod('post'))
        {
            DB::beginTransaction();

            try{

                $brand = new Brand();

                $brand->user_id = Auth::id();

                $brand->name = $request->name;
                $brand->status = 1;

                $brand->save();

                DB::commit();

                return response()->json([
                    'message' => 'Brand store successful'
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
        $brand = Brand::findOrFail($id);

        return view('admin.brand.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        if($request->_method == 'PUT')
        {
            DB::beginTransaction();

            try{

                $brand = Brand::findOrFail($id);

                $brand->user_id = Auth::id();

                $brand->name = $request->name;

                $brand->save();

                DB::commit();

                return response()->json([
                    'message' => 'Brand updated successful'
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
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return response()->json([
            'message' => 'Brand destroy successful'
        ],Response::HTTP_OK);
    }

    public function statusChange($id)
    {
        $brand = Brand::findOrFail($id);

        if($brand->status == 0)
        {
            $brand->update(['status' => 1]);

            return response()->json([
                'message' => 'Brand is active'
            ],Response::HTTP_OK);
        }else{
            $brand->update(['status' => 0]);

            return response()->json([
                'message' => 'Brand is inactive'
            ],Response::HTTP_OK);
        }
    }
}
