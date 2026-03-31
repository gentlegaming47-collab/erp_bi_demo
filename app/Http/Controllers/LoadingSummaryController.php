<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoadingEntry;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;


class LoadingSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-loading_summary');
    }

    public function index(LoadingEntry $loading_data,Request $request,DataTables $dataTables)
    {
    $year_data = getCurrentYearData();
    $location = getCurrentLocation();

        $loading_data = LoadingEntry::select(['loading_entry.le_id','dispatch_plan.dp_id','dispatch_plan.dp_date','dispatch_plan.dp_sequence','dispatch_plan.dp_number','loading_entry.vehicle_no','loading_entry.transporter_id','loading_entry.loading_by','loading_entry.driver_name','loading_entry.driver_mobile_no','transporters.transporter_name','sales_order.so_number','sales_order.so_date','items.item_name','items.item_code','item_groups.item_group_name', 'customer_groups.customer_group_name','units.unit_name','dispatch_plan_details.plan_qty','dealers.dealer_name','villages.village_name','districts.district_name','sales_order.customer_name','locations.location_name',
        
         DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),  
        
        ]) 


    ->leftJoin('loading_entry_details','loading_entry_details.le_id','=','loading_entry.le_id')
    ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id', 'loading_entry_details.dp_details_id')
    ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')
    ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
    ->leftJoin('transporters','transporters.id', 'loading_entry.transporter_id')
    ->leftJoin('items','items.id', 'dispatch_plan_details.item_id')
    ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
    ->leftJoin('units','units.id', 'items.unit_id')
    ->leftJoin('customer_groups','customer_groups.id','=','sales_order.customer_group_id')
    ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
    ->leftJoin('districts','districts.id','=','sales_order.customer_district_id')
    ->leftJoin('villages','villages.id','=','sales_order.customer_village')
    ->leftJoin('locations','locations.id','=','sales_order.to_location_id')

    ->where('loading_entry.current_location_id','=',$location->id)
    // ->where('loading_entry.year_id','=',$year_data->id)
    ->groupBy(['dispatch_plan.dp_id']);


    // filter for search data

    if($request->from_date != "" && $request->to_date != ""){

        $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

        $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');   
        

        $loading_data->whereDate('dispatch_plan.dp_date','>=',$from);

        $loading_data->whereDate('dispatch_plan.dp_date','<=',$to);

    }else if($request->from_date != ""){

        $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

        $loading_data->where('dispatch_plan.dp_date','>=',$from);

    }else if($request->to_date != ""){

        $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');

        $loading_data->where('dispatch_plan.dp_date','<=',$to);

    }

    // if($request->customer_name !=''){
    //     $loading_data->where('sales_order.customer_name','like', "%{$request->customer_name}%");
    // }

    // if($request->location_id !=''){
    //     $loading_data->where('sales_order.to_location_id','=',$request->location_id);
    // }

    // if($request->village_name !=''){
    //     $loading_data->where('villages.village_name','like', "%{$request->village_name}%");
    // }

    // if($request->district_name !=''){
    //     $loading_data->where('districts.district_name','like', "%{$request->district_name}%");
    // }

    // if($request->dealer_id !=''){
    //     $loading_data->where('sales_order.dealer_id','=',$request->dealer_id);
    // }

    // if($request->item_id !=''){
    //     $loading_data->where('sales_order_details.item_id', '=', $request->item_id);
    // }

    // if($request->dp_number !=''){
    //     $loading_data->where('dispatch_plan.dp_number','like', "%{$request->dp_number}%");
    // }

    // if($request->so_number !=''){
    //     $loading_data->where('sales_order.so_number','like', "%{$request->so_number}%");
    // }

    // end search terms

    return DataTables::of($loading_data)
    
    ->editColumn('dp_date', function($loading_data){
        if ($loading_data->dp_date != null) {
            $date = Date::createFromFormat('Y-m-d', $loading_data->dp_date)->format(DATE_FORMAT);
            return $date;
        }else{
            return '';
        }
    })
    ->editColumn('so_date', function($loading_data){
        if ($loading_data->so_date != null) {
            $date = Date::createFromFormat('Y-m-d', $loading_data->so_date)->format(DATE_FORMAT);
            return $date;
        }else{
            return '';
        }
    })
    ->filterColumn('dispatch_plan.dp_date', function ($query, $keyword) {
        $query->whereRaw("DATE_FORMAT(dispatch_plan.dp_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
    })
    ->filterColumn('sales_order.so_date', function ($query, $keyword) {
        $query->whereRaw("DATE_FORMAT(sales_order.so_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
    })

    //     ->addColumn('name', function($loading_data){           
    //     return $loading_data->so_from_value_fix == "location" ? $loading_data->location_name : $loading_data->customer_name;
    // })
    ->filterColumn('name', function($query, $keyword) {
        $query->where(function($q) use ($keyword) {
            $q->where('location_name', 'like', "%$keyword%")
                ->orWhere('customer_name', 'like', "%$keyword%");
        });
    })
    ->editColumn('plan_qty', function($loading_data){

        return $loading_data->plan_qty > 0 || $loading_data->plan_qty != ""  ? number_format((float)$loading_data->plan_qty, 3, '.','') : '';
    })
    ->filterColumn('plan_qty', function($query, $keyword) {
        $keyword = trim($keyword);

    // Numeric filtering
        if (is_numeric($keyword)) {
            // exact or approximate numeric match
            $query->whereRaw("CAST(plan_qty AS CHAR) LIKE ?", ["%{$keyword}%"]);
        } else {
            // fallback for any string-based search (if needed)
            $query->whereRaw("CAST(plan_qty AS CHAR) LIKE ?", ["%{$keyword}%"]);
        }
    })

    
    ->rawColumns(['dp_date','so_date','plan_qty','name'])
    ->make(true);
    }
}