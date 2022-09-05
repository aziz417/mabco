<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function index()
    {
        return view('admin.unit.index');
    }

    public function getData()
    {
        $unit = Unit::latest()->get();

        return DataTables::of($unit)
        ->addIndexColumn()
        ->addColumn('status',function ($unit){
            if($unit->status == 0)
            {

                return '<div>
                        <label class="switch patch">
                            <input type="checkbox" class="status_toggle" data-value="'.$unit->id.'" id="status_change" value="'.$unit->id.'">
                            <span class="slider"></span>
                        </label>
                        </div>';
            }else{
                return '<div>
                    <label class="switch patch">
                        <input type="checkbox" id="status_change"  class="status_toggle" data-value="'.$unit->id.'"  value="'.$unit->id.'" checked>
                        <span class="slider"></span>
                    </label>
                    </div>';
            }

        })
        ->editColumn('action', function ($unit) {
            $return = "<div class=\"btn-group\">";
            if (!empty($unit->name))
            {
                $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/unit/edit/$unit->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                        ||
                        <a rel=\"$unit->id\" rel1=\"unit/destroy\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-danger deleteRecord \"><i class='fa fa-trash'></i></a>
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
        return view('admin.unit.create');
    }

    public function store(Request $request)
    {
        if($request->isMethod('post'))
        {
            DB::beginTransaction();

            try{

                $unit = new Unit();

                $unit->user_id = Auth::id();
                $unit->name = $request->name;
                $unit->status = 0;

                $unit->save();

                DB::commit();

                return response()->json([
                    'message' => 'Unit store successful'
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
        $unit = Unit::findOrFail($id);

        return view('admin.unit.edit', compact('unit'));
    }

    public function update(Request $request, $id)
    {
        if($request->_method == 'PUT')
        {
            DB::beginTransaction();

            try{

                $unit =  Unit::findOrFail($id);

                $unit->user_id = Auth::id();
                $unit->name = $request->name;

                $unit->save();

                DB::commit();

                return response()->json([
                    'message' => 'Unit updated successful'
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
        $unit =  Unit::findOrFail($id);
        $unit->delete();

        return response()->json([
            'message' => 'Unit deleted successful'
        ],Response::HTTP_OK);
    }

    public function statusChange($id)
    {
        $unit =  Unit::findOrFail($id);

        if($unit->status == 0)
        {
            $unit->update(['status' => 1]);

            return response()->json([
                'message' => 'Unit is active'
            ],Response::HTTP_OK);
        }else{
            $unit->update(['status' => 0]);

            return response()->json([
                'message' => 'Unit is inactive'
            ],Response::HTTP_OK);
        }
    }
}
