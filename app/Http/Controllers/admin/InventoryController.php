<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    public function index($type)
    {
        return view('admin.stock.inventory', compact('type'));
    }

    public function inventoryGetData($type)
    {
        $inventory = Inventory::where('type', $type)->latest()->get();
        $inventory = $inventory->filter(function ($inventoryRow){
            $inventoryRow->date = date('d-m-Y g:i a', strtotime($inventoryRow->created_at));
            return $inventoryRow;
        });
        return DataTables::of($inventory)->addIndexColumn()->make(true);
    }
}
