<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DispatchPlan;
use App\Models\DispatchPlanDetails;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Date;
use DataTables;

class DispatchPlanSummaryController extends Controller
{
   public function manage()
    {
        return view('manage.manage-dispatch_plan_summary');
    }


public function index(Request $request, DataTables $dataTables)
{
  $year_data = getCurrentYearData();
        $location = getCurrentLocation();

    $dp_data = DispatchPlan::select([
        'dispatch_plan.dp_number',
        'dispatch_plan.dp_date',
        'dispatch_plan.dp_sequence',
        'dispatch_plan.dispatch_from_value_fix',
        'sales_order.so_number',
        'sales_order.so_date',
        'sales_order.customer_name',
       'locations.location_name',
        'dealers.dealer_name as dealer_name',
        'customer_groups.customer_group_name',
        'villages.village_name',
        'districts.district_name',
        'items.item_name',
        'items.item_code',
        'item_groups.item_group_name',
        'dispatch_plan_details.plan_qty',
        'units.unit_name as unit_name',
        'sales_order.so_from_value_fix',

        DB::raw('(CASE WHEN sales_order.so_from_value_fix = "location" 
                       THEN locations.location_name 
                       ELSE sales_order.customer_name END) as name'),
        // DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),
    ])
    ->leftJoin('dispatch_plan_details', 'dispatch_plan_details.dp_id', '=', 'dispatch_plan.dp_id')
    ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
    ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
    ->leftJoin('dealers', 'dealers.id', '=', 'sales_order.dealer_id')
    ->leftJoin('customer_groups', 'customer_groups.id', '=', 'sales_order.customer_group_id')
    ->leftJoin('districts', 'districts.id', '=', 'sales_order.customer_district_id')
    ->leftJoin('items', 'items.id', '=', 'sales_order_details.item_id')
    ->leftJoin('units', 'units.id', '=', 'items.unit_id')
    ->leftJoin('item_groups', 'item_groups.id', '=', 'items.item_group_id')
    ->leftJoin('locations','locations.id','=','sales_order.to_location_id')
    ->leftJoin('villages','villages.id','=','sales_order.customer_village')
    ->where('sales_order.current_location_id','=',$location->id);

    // Filter logic
    if($request->from_date != "" && $request->to_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');


            $dp_data->whereDate('dispatch_plan.dp_date','>=',$from);

            $dp_data->whereDate('dispatch_plan.dp_date','<=',$to);

        }else if($request->from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $dp_data->where('dispatch_plan.dp_date','>=',$from);

        }else if($request->to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');

            $dp_data->where('dispatch_plan.dp_date','<=',$to);

        }

        if($request->customer_name !=''){
            $dp_data->where('sales_order.customer_name','like', "%{$request->customer_name}%");
        }
        if($request->location_id !=''){
            $dp_data->where('sales_order.to_location_id','=',$request->location_id);
        }
        if($request->cust_group_id !=''){
            $dp_data->where('sales_order.customer_group_id','=',$request->cust_group_id);
        }
        if($request->dealer_id !=''){
            $dp_data->where('sales_order.dealer_id','=',$request->dealer_id);
        }
        if($request->village_name !=''){
            $dp_data->where('villages.village_name','like', "%{$request->village_name}%");
        }
        if($request->district_name !=''){
            $dp_data->where('districts.district_name','like', "%{$request->district_name}%");
        }
        if($request->item_id !=''){
                $dp_data->where('sales_order_details.item_id', '=', $request->item_id);
        }
         if($request->dp_number !=''){
            $dp_data->where('dispatch_plan.dp_number','like', "%{$request->dp_number}%");
        }
         if($request->so_number !=''){
            $dp_data->where('sales_order.so_number','like', "%{$request->so_number}%");
        }

    return DataTables::of($dp_data)
        ->editColumn('dp_date', function($row) {
            return $row->dp_date ? Date::createFromFormat('Y-m-d', $row->dp_date)->format(DATE_FORMAT) : '';
        })
         ->editColumn('so_date', function($row) {
            return $row->so_date ? Date::createFromFormat('Y-m-d', $row->so_date)->format(DATE_FORMAT) : '';
        })
        ->filterColumn('dispatch_plan.dp_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dispatch_plan.dp_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->filterColumn('sales_order.so_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_order.so_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('plan_qty', function($row) {
            return number_format($row->plan_qty, 3, '.', '');
        })
        ->editColumn('item_name', function($row) {
            return ucfirst($row->item_name ?? '');
        })
        ->editColumn('dealer_name', function($row) {
            return ucfirst($row->dealer_name ?? '');
        })
         ->editColumn('dispatch_from_value_fix', function($row) {
            $value = $row->dispatch_from_value_fix;

            if (empty($value)) {
                $value = $row->so_from_value_fix;
            }
            $map = [
                'customer' => 'Subsidy',
                'cash_carry' => 'Cash & Carry',
                'location' => 'Location',
                // Add more mappings if needed
            ];

            $values = explode(',', $value);
            $mappedValues = array_map(function($item) use ($map) {
                $item = trim($item);
                return $map[$item] ?? ucfirst($item);
            }, $values);

            return implode(', ', $mappedValues);
        })

        ->filterColumn('dispatch_plan.dispatch_from_value_fix', function($query, $keyword) {
            $query->whereRaw("
                REPLACE(
                    REPLACE(
                        REPLACE(dispatch_plan.dispatch_from_value_fix, 'customer', 'Subsidy'),
                        'cash_carry', 'Cash & Carry'
                    ),
                    'location', 'Location'
                ) LIKE ?
            ", ["%{$keyword}%"]);
        })

        ->filterColumn('name', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('location_name', 'like', "%$keyword%")
                  ->orWhere('customer_name', 'like', "%$keyword%");
            });
        })
        ->make(true);
}




}
