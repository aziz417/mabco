<?php

namespace App\Http\Controllers\admin;

use App\Adjustment;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdjustmentController extends Controller
{
    public function index()
    {
        return view('admin.adjustment.index');
    }

    public function getData()
    {
        $adjustment = Adjustment::select('adjustments.*',
            'retailer.name as retailer_name',
            'retailer.total_out_standing as total_out_standing',
            'seller.name as seller_name'
        )
            ->leftJoin('users as retailer', function ($join) {
                $join->on('adjustments.retailer_id', '=', 'retailer.id');
            })
            ->leftJoin('users as seller', function ($join) {
                $join->on('adjustments.seller_id', '=', 'seller.id');
            })
            ->latest()
            ->get();

        $adjustment = $adjustment->map(function ($ad){
            $ad->now_out_standing = $ad->old_out_standing - $ad->amount;
            $ad->total_out_standing = $ad->now_out_standing + $ad->amount;
            return $ad;
        });

        return DataTables::of($adjustment)
            ->addIndexColumn()
            ->editColumn('action', function ($adjustment) {
                $return = "<div class=\"btn-group\">";
                if (!empty($adjustment->title))
                {
                    $return .= "
                    <div class=\"btn-group\">
                        <a href=\"/adjustment/edit/$adjustment->id\" style='margin-right: 5px' class=\"btn btn-sm btn-warning\"><i class='fa fa-edit'></i></a>

                    </div>
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

    public function create()
    {
        $count = Adjustment::count();
        $sellers = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'retailer.total_out_standing as total_out_standing',
                'users.status as status',
                'roles.name as role_name'
            )
            ->leftJoin('users as retailer', function ($join) {
                $join->on('users.seller_id', '=', 'retailer.id');
            })
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'seller')
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();

        return view('admin.adjustment.create', compact('sellers', 'count'));
    }

    public function adjustmentRetailerList(Request $request)
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
            ->where('users.total_out_standing', '>', 0)
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();

        return $retailers;

    }
    public function outStanding(Request $request){
        return User::where('id', $request->retailer_id)->first()->total_out_standing;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:200',
            'amount'  => 'required|integer',
        ]);

        if($request->isMethod('post'))
        {
            DB::beginTransaction();

            try{

                $adjustment = new Adjustment();

                $adjustment->retailer_id = $request->retailer_id;
                $adjustment->seller_id = $request->seller_id;
                $adjustment->title = $request->title;
                $adjustment->date = $request->date;
                $adjustment->old_out_standing = $request->old_out_standing;
                $adjustment->amount = $request->amount;
                $adjustment->adjustment_number = $request->adjustment_number;

                $adjustment->save();

                $user = User::where(['seller_id' => $request->seller_id, 'id' => $request->retailer_id])->first();
                $amount = $user->total_out_standing - $request->amount;
                $user->total_out_standing = $amount;
                $user->save();

                DB::commit();

                return response()->json([
                    'message' => 'Adjustment store successful'
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
        $sellers = User::role('seller')->get();
        $retailers = User::role('retailer')->get();
        $count = Adjustment::count();

        $adjustment = Adjustment::findOrFail($id);

        return view('admin.adjustment.edit', compact('adjustment', 'count', 'sellers', 'retailers'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'title'  => 'required|string|max:200',
            'amount'  => 'required|integer',
        ]);
        if($request->_method == 'PUT')
        {
            DB::beginTransaction();

            try{

                $adjustment = Adjustment::findOrFail($id);

                $adjustment->retailer_id = $request->retailer_id;
                $adjustment->seller_id = $request->seller_id;
                $adjustment->title = $request->title;
                $adjustment->date = $request->date;
                $adjustment->old_out_standing = $request->old_out_standing;
                $adjustment->amount = $request->amount;
                $adjustment->adjustment_number = $request->adjustment_number;

                $adjustment->save();

                $user = User::where(['seller_id' => $request->seller_id, 'id' => $request->retailer_id])->first();
                $amount = $request->now_out_standing;
                $user->total_out_standing = $amount;
                $user->save();

                DB::commit();

                return response()->json([
                    'message' => 'Adjustment updated successful'
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
        $adjustment = Adjustment::findOrFail($id);
        $adjustment->delete();

        return response()->json([
            'message' => 'Adjustment destroy successful'
        ],Response::HTTP_OK);
    }
}
