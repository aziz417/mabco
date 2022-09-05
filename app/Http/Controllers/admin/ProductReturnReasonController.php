<?php

namespace App\Http\Controllers\admin;

use App\ProductReturnReason;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductReturnReasonController extends Controller
{
    public function index()
    {
        return view('admin.return_reason.index');
    }

    public function getData()
    {
        $return_reason = ProductReturnReason::latest()->get();

        return DataTables::of($return_reason)
            ->addIndexColumn()
            ->addColumn('status',function ($return_reason){
                if($return_reason->status == 0)
                {

                    return '<div>
                        <label class="switch patch">
                            <input type="checkbox" class="status_toggle" data-value="'.$return_reason->id.'" id="status_change" value="'.$return_reason->id.'">
                            <span class="slider"></span>
                        </label>
                        </div>';
                }else{
                    return '<div>
                    <label class="switch patch">
                        <input type="checkbox" id="status_change"  class="status_toggle" data-value="'.$return_reason->id.'"  value="'.$return_reason->id.'" checked>
                        <span class="slider"></span>
                    </label>
                    </div>';
                }

            })
            ->editColumn('action', function ($return_reason) {
                $return = "<div class=\"btn-group\">";
                if (!empty($return_reason->title))
                {
                    $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/return_reason/edit/$return_reason->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                        ||
                        <a rel=\"$return_reason->id\" rel1=\"return_reason/destroy\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-danger deleteRecord \"><i class='fa fa-trash'></i></a>
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
        return view('admin.return_reason.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:200',
        ]);

        if($request->isMethod('post'))
        {
            DB::beginTransaction();

            try{

                $return_reason = new ProductReturnReason();

                $return_reason->user_id = Auth::id();

                $return_reason->title = $request->title;
                $return_reason->status = 1;

                $return_reason->save();

                DB::commit();

                return response()->json([
                    'message' => 'Product Return Reason store successful'
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
        $return_reason = ProductReturnReason::findOrFail($id);

        return view('admin.return_reason.edit', compact('return_reason'));
    }

    public function update(Request $request, $id)
    {
        if($request->_method == 'PUT')
        {
            DB::beginTransaction();

            try{

                $return_reason = ProductReturnReason::findOrFail($id);

                $return_reason->user_id = Auth::id();

                $return_reason->title = $request->title;

                $return_reason->save();

                DB::commit();

                return response()->json([
                    'message' => 'Product Return Reason updated successful'
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
        $return_reason = ProductReturnReason::findOrFail($id);
        $return_reason->delete();

        return response()->json([
            'message' => 'Product Return Reason destroy successful'
        ],Response::HTTP_OK);
    }

    public function statusChange($id)
    {
        $return_reason = ProductReturnReason::findOrFail($id);

        if($return_reason->status == 0)
        {
            $return_reason->update(['status' => 1]);

            return response()->json([
                'message' => 'Product Return Reason is active'
            ],Response::HTTP_OK);
        }else{
            $return_reason->update(['status' => 0]);

            return response()->json([
                'message' => 'Product Return Reason is inactive'
            ],Response::HTTP_OK);
        }
    }
}
