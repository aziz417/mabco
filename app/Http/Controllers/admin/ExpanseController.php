<?php

namespace App\Http\Controllers\admin;

use App\Expanse;
use App\ExpanseReason;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ExpanseController extends Controller
{
    public function index()
    {
        return view('admin.expanse.index');
    }

    public function getData()
    {
        $expanse = Expanse::
        select('expanses.*',
            'expanse_reasons.title',
        )
            ->leftJoin('expanse_reasons', function ($join) {
                $join->on('expanses.expanse_reasons_id', '=', 'expanse_reasons.id');
            })
        ->latest()->get();

        return DataTables::of($expanse)
            ->addIndexColumn()
            ->addColumn('status',function ($expanse){
                if($expanse->status == 0)
                {

                    return '<div>
                        <label class="switch patch">
                            <input type="checkbox" class="status_toggle" data-value="'.$expanse->id.'" id="status_change" value="'.$expanse->id.'">
                            <span class="slider"></span>
                        </label>
                        </div>';
                }else{
                    return '<div>
                    <label class="switch patch">
                        <input type="checkbox" id="status_change"  class="status_toggle" data-value="'.$expanse->id.'"  value="'.$expanse->id.'" checked>
                        <span class="slider"></span>
                    </label>
                    </div>';
                }

            })
            ->editColumn('action', function ($expanse) {
                $return = "<div class=\"btn-group\">";
                if (!empty($expanse->title))
                {
                    $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/expanse/edit/$expanse->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                        ||
                        <a rel=\"$expanse->id\" rel1=\"expanse/destroy\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-danger deleteRecord \"><i class='fa fa-trash'></i></a>
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
        $expanse_reasons = ExpanseReason::where('status', 1)->get();
        return view('admin.expanse.create', compact('expanse_reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount'  => 'required|integer',
        ]);

        if($request->isMethod('post'))
        {
            DB::beginTransaction();

            try{

                $expanse = new Expanse();

                $expanse->user_id = Auth::id();
                $expanse->expanse_reasons_id = $request->expanse_reasons_id;
                $expanse->date = $request->date;
                $expanse->amount = $request->amount;
                $expanse->status = 1;

                $expanse->save();

                DB::commit();

                return response()->json([
                    'message' => 'Expanse store successful'
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
        $expanse = Expanse::findOrFail($id);
        $expanse_reasons = ExpanseReason::where('status', 1)->get();

        return view('admin.expanse.edit', compact('expanse', 'expanse_reasons'));
    }

    public function update(Request $request, $id)
    {
        if($request->_method == 'PUT')
        {
            DB::beginTransaction();

            try{
                $expanse = Expanse::findOrFail($id);

                $expanse->user_id = Auth::id();
                $expanse->expanse_reasons_id = $request->expanse_reasons_id;
                $expanse->date = $request->date;
                $expanse->amount = $request->amount;
                $expanse->status = 1;

                $expanse->save();

                DB::commit();

                return response()->json([
                    'message' => 'Expanse updated successful'
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
        $expanse = Expanse::findOrFail($id);
        $expanse->delete();

        return response()->json([
            'message' => 'Expanse destroy successful'
        ],Response::HTTP_OK);
    }

    public function statusChange($id)
    {
        $expanse = Expanse::findOrFail($id);

        if($expanse->status == 0)
        {
            $expanse->update(['status' => 1]);

            return response()->json([
                'message' => 'Expanse is active'
            ],Response::HTTP_OK);
        }else{
            $expanse->update(['status' => 0]);

            return response()->json([
                'message' => 'Expanse is inactive'
            ],Response::HTTP_OK);
        }
    }
}
