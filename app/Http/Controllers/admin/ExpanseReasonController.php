<?php

namespace App\Http\Controllers\admin;

use App\ExpanseReason;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ExpanseReasonController extends Controller
{
    public function index()
    {
        return view('admin.expanse_reason.index');
    }

    public function getData()
    {
        $expanse_reason = ExpanseReason::latest()->get();

        return DataTables::of($expanse_reason)
            ->addIndexColumn()
            ->addColumn('status',function ($expanse_reason){
                if($expanse_reason->status == 0)
                {

                    return '<div>
                        <label class="switch patch">
                            <input type="checkbox" class="status_toggle" data-value="'.$expanse_reason->id.'" id="status_change" value="'.$expanse_reason->id.'">
                            <span class="slider"></span>
                        </label>
                        </div>';
                }else{
                    return '<div>
                    <label class="switch patch">
                        <input type="checkbox" id="status_change"  class="status_toggle" data-value="'.$expanse_reason->id.'"  value="'.$expanse_reason->id.'" checked>
                        <span class="slider"></span>
                    </label>
                    </div>';
                }

            })
            ->editColumn('action', function ($expanse_reason) {
                $return = "<div class=\"btn-group\">";
                if (!empty($expanse_reason->title))
                {
                    $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/expanse_reason/edit/$expanse_reason->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                        ||
                        <a rel=\"$expanse_reason->id\" rel1=\"expanse_reason/destroy\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-danger deleteRecord \"><i class='fa fa-trash'></i></a>
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
        return view('admin.expanse_reason.create');
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

                $expanse_reason = new ExpanseReason();

                $expanse_reason->user_id = Auth::id();

                $expanse_reason->title = $request->title;
                $expanse_reason->status = 1;

                $expanse_reason->save();

                DB::commit();

                return response()->json([
                    'message' => 'Expanse Reason store successful'
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
        $expanse_reason = ExpanseReason::findOrFail($id);

        return view('admin.expanse_reason.edit', compact('expanse_reason'));
    }

    public function update(Request $request, $id)
    {
        if($request->_method == 'PUT')
        {
            DB::beginTransaction();

            try{

                $expanse_reason = ExpanseReason::findOrFail($id);

                $expanse_reason->user_id = Auth::id();

                $expanse_reason->title = $request->title;

                $expanse_reason->save();

                DB::commit();

                return response()->json([
                    'message' => 'Expanse Reason updated successful'
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
        $expanse_reason = ExpanseReason::findOrFail($id);
        $expanse_reason->delete();

        return response()->json([
            'message' => 'Expanse Reason destroy successful'
        ],Response::HTTP_OK);
    }

    public function statusChange($id)
    {
        $expanse_reason = ExpanseReason::findOrFail($id);

        if($expanse_reason->status == 0)
        {
            $expanse_reason->update(['status' => 1]);

            return response()->json([
                'message' => 'Expanse Reason is active'
            ],Response::HTTP_OK);
        }else{
            $expanse_reason->update(['status' => 0]);

            return response()->json([
                'message' => 'Expanse Reason is inactive'
            ],Response::HTTP_OK);
        }
    }
}
