<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemProduction;
use App\Models\ItemProductionDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;

class ItemProductionSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-item_production_summary');
    }
    public function index(ItemProduction $itemProduction,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();


        $item_production = ItemProduction::select([
        'ip_number',
        'ip_sequence',
        'ip_date',
        'items.item_name',
        'items.item_code',
        'item_production_details.production_qty',
        'remarks',
        'item_production.ip_id',
        'item_groups.item_group_name',
        'units.unit_name'])

       ->leftJoin('item_production_details','item_production_details.ip_id','=','item_production.ip_id')
       ->leftJoin('items','items.id','=','item_production_details.item_id')
       ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
       ->leftJoin('units','units.id','=','items.unit_id')
    //    ->where('item_production.year_id','=',$year_data->id)
       ->where('item_production.current_location_id','=',$location->id);

        // filter for search data
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $item_production->whereDate('item_production.ip_date','>=',$from);
            $item_production->whereDate('item_production.ip_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $item_production->where('item_production.ip_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');
            $item_production->where('item_production.ip_date','<=',$to);

        } 
        // if($request->item_id !=''){
        //     $item_production->where('item_production_details.item_id', '=', $request->item_id);
        // }
        // if($request->ip_number !=''){
        //     $item_production->where('item_production.ip_number','like', "%{$request->ip_number}%");
        // }
        // end search terms


        return DataTables::of($item_production)

        ->editColumn('item_name', function($item_production){
            if($item_production->item_name != ''){
                $item_name = ucfirst($item_production->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

        ->editColumn('production_qty', function($item_production){

            return $item_production->production_qty > 0 || $item_production->production_qty != ""  ? number_format((float)$item_production->production_qty, 3, '.','') : '';
        })



        ->editColumn('ip_date', function($item_production){
            if ($item_production->ip_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $item_production->ip_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('item_production.ip_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_production.ip_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->make(true);
    }
}
