<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Bank;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BankController extends Controller
{
    public function index()
    {
        return view('admin.bank.index');
    }

    public function getData()
    {
        $bank = Bank::latest()->get();

        return DataTables::of($bank)
        ->addIndexColumn()
        ->addColumn('status',function ($bank){
            if($bank->status == 0)
            {

                return '<div>
                        <label class="switch patch">
                            <input type="checkbox" class="status_toggle" data-value="'.$bank->id.'" id="status_change" value="'.$bank->id.'">
                            <span class="slider"></span>
                        </label>
                        </div>';
            }else{
                return '<div>
                    <label class="switch patch">
                        <input type="checkbox" id="status_change"  class="status_toggle" data-value="'.$bank->id.'"  value="'.$bank->id.'" checked>
                        <span class="slider"></span>
                    </label>
                    </div>';
            }

        })
        ->editColumn('action', function ($bank) {
            $return = "<div class=\"btn-group\">";
            if (!empty($bank->name))
            {
                $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/bank/edit/$bank->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>
                        ||
                        <a rel=\"$bank->id\" rel1=\"bank/destroy\" href=\"javascript:\" style='margin-right: 5px' class=\"btn btn-sm btn-danger deleteRecord \"><i class='fa fa-trash'></i></a>
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
        return view('admin.bank.create');
    }

    public function store(Request $request)
    {
        if($request->isMethod('post'))
        {
            DB::beginTransaction();

            try{

                $bank = new Bank();

                $bank->name = $request->name;
                $bank->status = 0;

                $bank->save();

                DB::commit();

                return response()->json([
                    'message' => 'Bank store successful'
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
        $bank = Bank::findOrFail($id);

        return view('admin.bank.edit', compact('bank'));
    }

    public function update(Request $request, $id)
    {
        if($request->_method == 'PUT')
        {
            DB::beginTransaction();

            try{

                $bank =  Bank::findOrFail($id);

                $bank->name = $request->name;

                $bank->save();

                DB::commit();

                return response()->json([
                    'message' => 'Bank updated successful'
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
        $bank =  Bank::findOrFail($id);
        $bank->delete();

        return response()->json([
            'message' => 'Bank deleted successful'
        ],Response::HTTP_OK);
    }

    public function statusChange($id)
    {
        $bank =  Bank::findOrFail($id);
        info($bank);

        if($bank->status == 0)
        {
            $bank->update(['status' => 1]);

            return response()->json([
                'message' => 'Bank is active'
            ],Response::HTTP_OK);
        }else{
            $bank->update(['status' => 0]);

            return response()->json([
                'message' => 'Bank is inactive'
            ],Response::HTTP_OK);
        }
    }
}
