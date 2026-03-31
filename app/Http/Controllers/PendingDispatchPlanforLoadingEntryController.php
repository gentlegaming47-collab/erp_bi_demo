<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoadingEntry;
use App\Models\DispatchPlan;
use App\Models\LoadingEntryDetails;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class PendingDispatchPlanforLoadingEntryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-pending_dispatch_plan_for_loading_entry');
    }

    public function index(LoadingEntry $loading_data,Request $request,DataTables $dataTables)
    {

        $yearIds      = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        $dp_details_id = LoadingEntryDetails::pluck('dp_details_id')
        ->toArray();

        $loading_data = DispatchPlan::distinct()->select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan.dp_sequence','dispatch_plan.special_notes','dispatch_plan.dp_id','dispatch_plan_details.dp_details_id','sales_order.so_number','sales_order.so_date','sales_order.customer_name','sales_order.so_from_value_fix','locations.location_name','items.item_name','items.item_code', 'units.unit_name','dealers.dealer_name','villages.village_name','districts.district_name','sales_order_details.so_details_id','dispatch_plan_details.plan_qty', 'sales_order_details.fitting_item','items.id as item_id','sales_order.to_location_id',   

             DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),  

            DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_qty"),

        ])

        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
         ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
        ->leftJoin('locations','locations.id','=','sales_order.to_location_id')
        ->leftJoin('items','items.id', 'dispatch_plan_details.item_id')
        ->leftJoin('units','units.id', 'items.unit_id')
        ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
        ->leftJoin('districts','districts.id','=','sales_order.customer_district_id')
        ->leftJoin('villages','villages.id','=','sales_order.customer_village')
        ->where('dispatch_plan.current_location_id',$locationCode->id)
        ->whereIn('dispatch_plan.year_id',$yearIds) 
        ->whereNotIn('dispatch_plan_details.dp_details_id',$dp_details_id);

        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $loading_data->whereDate('dispatch_plan.dp_date','>=',$from);

                $loading_data->whereDate('dispatch_plan.dp_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $loading_data->where('dispatch_plan.dp_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $loading_data->where('dispatch_plan.dp_date','<=',$to);

        }


         
        // if($request->customer_name !=''){

        //     $loading_data->where('sales_order.customer_name','like', "%{$request->customer_name}%");
        // }

        // if($request->location_id !=''){
        //     $loading_data->where('sales_order.to_location_id','=',$request->location_id);
        // }

        // if($request->dealer_id !=''){
        //     $loading_data->where('sales_order.dealer_id','=',$request->dealer_id);
        // }

        // if($request->item_id !=''){
        //     $loading_data->where('sales_order_details.item_id', '=', $request->item_id);
        // }

        // if($request->village_name !=''){
        //     $loading_data->where('villages.village_name','like', "%{$request->village_name}%");
        // }

        // if($request->district_name !=''){
        //     $loading_data->where('districts.district_name','like', "%{$request->district_name}%");
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
        // ->addColumn('name', function($loading_data){           
        //     return $loading_data->so_from_value_fix == "location" ? $loading_data->location_name : $loading_data->customer_name;
        // })
        ->filterColumn('name', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('location_name', 'like', "%$keyword%")
                    ->orWhere('customer_name', 'like', "%$keyword%");
            });
        })
        ->editColumn('pend_qty', function($loading_data){

            $loading_data->pend_qty = $loading_data->pend_qty + $loading_data->plan_qty;

            return $loading_data->pend_qty > 0 || $loading_data->pend_qty != ""  ? number_format((float)$loading_data->pend_qty, 3, '.','') : '';
        })
        ->filterColumn('pend_qty', function($query, $keyword) {
            $query->whereRaw("CAST((sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id),0) + dispatch_plan_details.plan_qty) AS CHAR) LIKE ?", ["%{$keyword}%"]);
        })
        ->rawColumns(['dp_date','so_date','name'])
        ->make(true);



    }
  
}