<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use App\Models\DispatchPlan;
use App\Models\SalesOrderDetail;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetailsDetails;
use App\Models\DispatchPlanDetails;
use App\Models\DispatchPlanDetailsDetails;
use App\Models\DispatchPlanSecondaryDetails;
use App\Models\LoadingEntryDetails;
use App\Models\SOShortClose;
use App\Models\SOMappingDetails;
use App\Models\LocationStock;
use App\Models\TransactionSOShortClose;
use App\Models\ItemDetails;
use App\Models\Item;
use App\Models\ItemRawMaterialMappingDetail;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Date;

class DispatchPlanController extends Controller
{
    public function manage()
    {
        return view('manage.manage-dispatch_plan');
    }

    public function create()
    {
        return view('add.add-dispatch_plan');
    }

    public function index(DispatchPlan $dispatch_plan,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();

//         $dispatch_plan = DispatchPlan::select(['dispatch_plan.dp_id','dispatch_plan.dp_date','dispatch_plan.dp_number', 'sales_order.so_number','sales_order.so_date','sales_order.customer_name','districts.district_name','locations.location_name', 'dealers.dealer_name','items.item_name','items.item_code', 'item_groups.item_group_name', 'units.unit_name','dispatch_plan_details.plan_qty','dispatch_plan.last_by_user_id','dispatch_plan.last_on','dispatch_plan.created_by_user_id','dispatch_plan.created_on','locations.location_name',

//         DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'), 
// // 'sales_order_details.so_qty',
//         // DB::raw('
//         // CASE 
//         //     WHEN (SELECT MAX(sod.sod_details_id) FROM sales_order_detail_details sod WHERE sod.so_details_id = sales_order_details.so_details_id) IS NOT NULL THEN 
//         //         (SELECT MIN(sod.so_qty) FROM sales_order_detail_details sod WHERE sod.so_details_id = sales_order_details.so_details_id)
//         //     ELSE 
//         //         (sales_order_details.so_qty)
//         // END as so_qty')
        
//         DB::raw("(SELECT sales_order_details.so_qty - IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details WHERE sales_order_details.so_details_id = dispatch_plan_details.so_details_id) as pending_so_qty"),
     
//         // DB::raw("(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty)
//         // FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id), 0 ) - IFNULL((SELECT SUM(so_short_close.sc_qty) FROM so_short_close
//         // WHERE so_short_close.so_details_id = sales_order_details.so_details_id), 0)
//         // ) as pending_so_qty")
        
//           'sales_order.customer_village',
//           'villages.village_name',
//         ])
//         ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id', 'dispatch_plan.dp_id')
//         ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')      
//         ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')        
//         ->leftJoin('villages','villages.id','=','sales_order.customer_village')
//         ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
//         ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
//         ->leftJoin('items','items.id', 'dispatch_plan_details.item_id')
//         ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
//         ->leftJoin('units','units.id', 'items.unit_id')
//         ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
//         ->where('dispatch_plan.current_location_id','=',$location->id)
//         ->where('dispatch_plan.year_id','=',$year_data->id);


        $dispatch_plan = DispatchPlan::select(['dispatch_plan.dp_id','dispatch_plan.dp_date','dispatch_plan.dp_number','dispatch_plan.dispatch_from_value_fix','dispatch_plan.last_by_user_id','dispatch_plan.last_on','dispatch_plan.created_by_user_id','dispatch_plan.created_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name','dispatch_plan.dp_sequence',]) 
        
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'dispatch_plan.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'dispatch_plan.last_by_user_id')   
        ->where('dispatch_plan.current_location_id','=',$location->id)
        ->where('dispatch_plan.year_id','=',$year_data->id);

        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


            $dispatch_plan->whereDate('dispatch_plan.dp_date','>=',$from);

            $dispatch_plan->whereDate('dispatch_plan.dp_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $dispatch_plan->where('dispatch_plan.dp_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $dispatch_plan->where('dispatch_plan.dp_date','<=',$to);

        } 
        return DataTables::of($dispatch_plan)
        ->editColumn('created_by_user_id', function($dispatch_plan){
            if($dispatch_plan->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$dispatch_plan->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($dispatch_plan){
            if($dispatch_plan->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$dispatch_plan->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
 
        ->editColumn('created_on', function($dispatch_plan){
            if ($dispatch_plan->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $dispatch_plan->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('dispatch_plan.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dispatch_plan.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($dispatch_plan){
            if ($dispatch_plan->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $dispatch_plan->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('dispatch_plan.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dispatch_plan.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('dp_date', function($dispatch_plan){
            if ($dispatch_plan->dp_date != null) {
                $date = Date::createFromFormat('Y-m-d', $dispatch_plan->dp_date)->format(DATE_FORMAT);
                
                return $date;
            }else{
                return '';
            }
        })
        ->filterColumn('dispatch_plan.dp_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dispatch_plan.dp_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        // ->editColumn('so_date', function($dispatch_plan){
        //     if ($dispatch_plan->so_date != null) {
        //         $so_date = Date::createFromFormat('Y-m-d', $dispatch_plan->so_date)->format(DATE_FORMAT);
        //         return $so_date;
        //     }else{
        //         return '';
        //     }
        // })
        // ->editColumn('plan_qty', function($dispatch_plan){

        //     return $dispatch_plan->plan_qty > 0 ? number_format((float)$dispatch_plan->plan_qty, 3, '.') : '';
        // })
        // ->editColumn('pend_so_qty', function($dispatch_plan){           
        //     return $dispatch_plan->pending_so_qty > 0 ? number_format((float)$dispatch_plan->pending_so_qty, 3, '.') :number_format((float)0, 3, '.');


        //     // return $spi_grn_data->pend_po_qty > 0 ? $spi_grn_data->pend_po_qty : 0;

        // })

        // ->editColumn('item_name', function($dispatch_plan){ 
        //     if($dispatch_plan->item_name != ''){
        //         $item_name = ucfirst($dispatch_plan->item_name);
        //         return $item_name;
        //     }else{
        //         return '';
        //     }
        // })

        ->addColumn('customer_group', function($dispatch_plan){ 
           
            return $customer_group = DispatchPlanDetails::select('customer_groups.customer_group_name')
            ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
            ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
            ->leftJoin('customer_groups', 'customer_groups.id', '=', 'sales_order.customer_group_id')
            ->where('dp_id', $dispatch_plan->dp_id)
            ->whereNotNull('customer_groups.customer_group_name') // Exclude null values
            ->groupBy('customer_groups.customer_group_name') // Group by customer group name
            ->pluck('customer_group_name') // Get only the column values
            ->implode(', '); // Convert to comma-separated string
        
       
        })
        // ->filterColumn('customer_group', function ($query, $keyword) {
        //     $query->whereIn('dp_id', function ($subQuery) use ($keyword) {
        //         $subQuery->select('dispatch_plan_details.dp_id')
        //             ->from('dispatch_plan_details')
        //             ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
        //             ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
        //             ->leftJoin('customer_groups', 'customer_groups.id', '=', 'sales_order.customer_group_id')
        //             ->where('customer_groups.customer_group_name', 'like', "%{$keyword}%");
        //     });
        // })
        ->filterColumn('customer_group', function ($query, $keyword) {
            $keywords = explode(',', $keyword);
            $query->whereIn('dp_id', function ($subQuery) use ($keywords) {
                $subQuery->select('dispatch_plan_details.dp_id')
                    ->from('dispatch_plan_details')
                    ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
                    ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
                    ->leftJoin('customer_groups', 'customer_groups.id', '=', 'sales_order.customer_group_id');

                foreach ($keywords as $key) {
                    $key = trim($key);
                    $subQuery->orWhere('customer_groups.customer_group_name', 'like', "%{$key}%");
                }
            });
        })

        ->addColumn('so_number', function($dispatch_plan){ 
           
            return $so_no = DispatchPlanDetails::select('sales_order.so_number')
            ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
            ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')      
            ->where('dp_id', $dispatch_plan->dp_id)     
            ->groupBy('sales_order.so_number') 
            ->pluck('so_number') 
            ->implode(', '); 
        
       
        })
        // ->filterColumn('so_number', function ($query, $keyword) {
        //     $query->whereIn('dp_id', function ($subQuery) use ($keyword) {
        //         $subQuery->select('dispatch_plan_details.dp_id')
        //             ->from('dispatch_plan_details')
        //             ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
        //             ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')                 
        //             ->where('sales_order.so_number', 'like', "%{$keyword}%");
        //     });
        // })

        ->filterColumn('so_number', function ($query, $keyword) {
            $keywords = explode(',', $keyword);
            $query->whereIn('dp_id', function ($subQuery) use ($keywords) {
                $subQuery->select('dispatch_plan_details.dp_id')
                    ->from('dispatch_plan_details')
                    ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
                    ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id');

                foreach ($keywords as $key) {
                    $key = trim($key);
                    $subQuery->orWhere('sales_order.so_number', 'like', "%{$key}%");
                }
            });
        })

        ->editColumn('dispatch_from_value_fix', function($dispatch_plan) {
            $value = $dispatch_plan->dispatch_from_value_fix;

            if (empty($value)) {
                // return "";
                $value = DispatchPlanDetails::select('sales_order.so_from_value_fix')
            ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
            ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
            ->where('dispatch_plan_details.dp_id', $dispatch_plan->dp_id)
            ->groupBy('sales_order.so_from_value_fix')
            ->pluck('so_from_value_fix')
            ->implode(', ');
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

        ->filterColumn('dispatch_from_value_fix', function($query, $keyword) {
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


        ->addColumn('options',function($dispatch_plan){            
            $action = "<div>";
            if(hasAccess("dispatch_plan","print")){   

                $dp_details = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id','dispatch_plan_details.dp_id','dispatch_plan_details.item_id','dispatch_plan_details.plan_qty','items.item_name','units.unit_name as unitName',
                ])
                ->leftJoin('items','items.id', 'dispatch_plan_details.item_id')
                ->leftJoin('units','units.id', 'items.unit_id')
                ->where('items.print_dispatch_plan',"Yes")
                ->where('dp_id',$dispatch_plan->dp_id)->get();
               
                if($dp_details->count() > 0)
                {
                    $action .="<a id='print_a' target='_blank' href='".route('print-dispatch_plan',['id' => base64_encode($dispatch_plan->dp_id)]) ."' data-placement='top' data-original-title='Print' title='Total Print'><i class='iconfa-print action-icon'></i></a>";

                    $action .="<a id='print_a' target='_blank' href='".route('print-farmer_dispatch_plan',['id' => base64_encode($dispatch_plan->dp_id)]) ."' data-placement='top' data-original-title='Farmer Print' title='Farmer Wise Print'><i class='iconfa-print action-icon'></i></a>";
                    
                    $action .="<a id='print_a' target='_blank' href='".route('print-farmer_wise_total_dispatch_plan',['id' => base64_encode($dispatch_plan->dp_id)]) ."' data-placement='top' data-original-title='Farmer Print' title='Farmer Wise Total Print'><i class='iconfa-print action-icon'></i></a>";

                }
                // else{
                //       session()->flash('message','No Data Available For Print!');
                //       $action .="<a id='print_a'  href='".route('print-dispatch_plan',['id' => base64_encode($dispatch_plan->dp_id)]) ."' data-placement='top' data-original-title='Print' title='Total Print'><i class='iconfa-print action-icon'></i></a>";

                //       $action .="<a id='print_a'  href='".route('print-farmer_dispatch_plan',['id' => base64_encode($dispatch_plan->dp_id)]) ."' data-placement='top' data-original-title='Farmer Print' title='Farmer Wise Print'><i class='iconfa-print action-icon'></i></a>";

                //       $action .="<a id='print_a'  href='".route('print-farmer_wise_total_dispatch_plan',['id' => base64_encode($dispatch_plan->dp_id)]) ."' data-placement='top' data-original-title='Farmer Print' title='Farmer Wise Total Print'><i class='iconfa-print action-icon'></i></a>";
                // }
            }
            // if(hasAccess("dispatch_plan","print")){
            //     $action .="<a id='print_a' target='_blank' href='".route('print-farmer_dispatch_plan',['id' => base64_encode($dispatch_plan->dp_id)]) ."' data-placement='top' data-original-title='Farmer Print' title='Farmer Wise Print'><i class='iconfa-print action-icon'></i></a>";
            // }
             if(hasAccess("dispatch_plan","edit")){
             $action .="<a id='edit_a' href='".route('edit-dispatch_plan',['id' => base64_encode($dispatch_plan->dp_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
             }
            if(hasAccess("dispatch_plan","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        // ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'date', 'so_date', 'plan_qty','pend_so_qty','options'])
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'date', 'options'])
        ->make(true);
    }

    public function getLatestDispatchNo(Request $request)
    {
          $modal  =  DispatchPlan::class;
          $sequence = 'dp_sequence';
          $prefix = 'DP';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_dp_no'  => $sup_num_format['format'],
            'number'        => $sup_num_format['isFound'],
            'location'      => $locationName
        ]);

    }

    public function getSOData(Request $request){
        // $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();

        $locationCode = getCurrentLocation()->id;

        if(isset($request->id)){
            $sod_id = DispatchPlanDetails::select('so_details_id')->where('dp_id','=',$request->id)->get();  

            $edit_so_data = DispatchPlanDetails::select(['sales_order.so_number','sales_order.id',         'sales_order.so_date','sales_order_details.so_details_id', 'sales_order.special_notes',
            DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),'dispatch_plan_details.dp_details_id',
            'villages.village_name as customer_village',
            'districts.district_name',
            'dealers.dealer_name',   
            'customer_groups.customer_group_name',       
             'sales_order.so_from_value_fix', 
             'sales_order.so_from_id_fix',    'sales_order.so_type_value_fix',  'sales_order.customer_reg_no','talukas.taluka_name','states.state_name', 'countries.country_name','mis_category.mis_category',
            ])
            ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
            ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')   
            ->leftJoin('customer_groups','customer_groups.id', 'sales_order.customer_group_id')
            ->leftJoin('villages','villages.id','=','sales_order.customer_village')
            ->leftJoin('talukas','talukas.id','=','sales_order.customer_taluka')
            ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
            ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')    
            ->leftJoin('states','states.id','=','districts.state_id') 
            ->leftJoin('countries','countries.id','=','states.country_id')   
            ->leftJoin('mis_category','mis_category.id','=','sales_order.mis_category_id')              
            ->leftJoin('locations','locations.id', 'sales_order.to_location_id') 
            ->where('dispatch_plan_details.dp_id',$request->id)
            ->get(); 
        }
        
        // $sod_detail = DispatchPlanDetailsDetails::select('sales_order_detail_details.so_details_id')
        // ->leftJoin('sales_order_detail_details','sales_order_detail_details.sod_details_id','=','dispatch_plan_details_details.so_details_detail_id')
        // ->get();

        // $tr_sc_sod_ids = TransactionSOShortClose::select('so_details_id')->pluck('so_details_id')->toArray();

        // $sod_details_id = SalesOrderDetailsDetails::select(['sales_order.id',
        // DB::raw("((SELECT sales_order_detail_details.so_qty -  IFNULL(SUM(dispatch_plan_details_details.plan_qty),0) FROM dispatch_plan_details_details  WHERE dispatch_plan_details_details.so_details_detail_id = sales_order_detail_details.sod_details_id)) as pend_sod_qty")
        // ])
        // ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'sales_order_detail_details.so_details_id')  
        // ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
        // ->having('pend_sod_qty','>', 0)  
        // ->pluck('sales_order.id')
        // ->toArray();

        // $so_details_id = SalesOrderDetail::select(['sales_order.id',
        // DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty")
        // ])
        // ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
        // ->whereIn('sales_order.year_id',$yearIds)
        // ->where('sales_order.current_location_id',$locationCode)
        // ->where('sales_order_details.fitting_item','no')
        // ->having('pend_so_qty','>', 0)  
        // ->pluck('sales_order.id')
        // ->toArray();
       
        // if(!is_null($request->so_from_id_fix)){
        //     if($request->so_from_id_fix == 1){
        //         $so_from_id_fix = ['1','2'];
        //     }else{
        //         $so_from_id_fix = ['3'];
        //     }

        // }else{
        //     $so_from_id_fix = '';
        // }

        // $so_data = SalesOrderDetail::select(['sales_order.so_number','sales_order.id','sales_order.so_date', 
        // 'sales_order_details.so_details_id','sales_order_details.so_id','sales_order.special_notes',
        // DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),   
        // 'villages.village_name as customer_village',
        // 'districts.district_name',
        // 'dealers.dealer_name',  
        // 'customer_groups.customer_group_name',
        // 'sales_order.so_from_value_fix',  'sales_order.so_from_id_fix',  'sales_order.so_type_value_fix','sales_order.customer_reg_no','talukas.taluka_name','states.state_name', 'countries.country_name','mis_category.mis_category',
        // ])
        // ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
        // ->leftJoin('customer_groups','customer_groups.id', 'sales_order.customer_group_id')
        // ->leftJoin('villages','villages.id','=','sales_order.customer_village')
        // ->leftJoin('talukas','talukas.id','=','sales_order.customer_taluka')
        // ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
        // ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
        // ->leftJoin('states','states.id','=','districts.state_id')     
        // ->leftJoin('countries','countries.id','=','states.country_id')  
        // ->leftJoin('mis_category','mis_category.id','=','sales_order.mis_category_id')         
        // ->leftJoin('locations','locations.id', 'sales_order.to_location_id') 
        // // ->where('sales_order.year_id',$year_data->id)
        // ->whereIn('sales_order.year_id',$yearIds)
        // ->where('sales_order.current_location_id',$locationCode)
        // // ->whereNotIn('sales_order_details.so_details_id',$sod_detail)
        // ->whereIN('sales_order.id',array_merge($sod_details_id,$so_details_id))
        // // ->whereIN('sales_order.id',array_merge($sod_details_id,$so_details_id,$so_details_seond_id))
        // ->when(!is_null($request->so_from_id_fix), function ($query) use ($so_from_id_fix) {
        //     return $query->whereIn('sales_order.so_from_id_fix', $so_from_id_fix);
        // })
        // ->groupBy('sales_order.id')       
        // ->get();


        $tr_sc_sod_ids = TransactionSOShortClose::select('so_details_id')->pluck('so_details_id')->toArray();

        $sod_details_id = SalesOrderDetailsDetails::select(['sales_order.id','sales_order_details.so_details_id',
        DB::raw("((SELECT sales_order_detail_details.so_qty -  IFNULL(SUM(dispatch_plan_details_details.plan_qty),0) FROM dispatch_plan_details_details  WHERE dispatch_plan_details_details.so_details_detail_id = sales_order_detail_details.sod_details_id)) as pend_sod_qty")
        ])
        ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'sales_order_detail_details.so_details_id')  
        ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
        ->having('pend_sod_qty','>', 0)  
        ->pluck('sales_order_details.so_details_id')
        ->toArray();


        $so_details_id = SalesOrderDetail::select(['sales_order.id','sales_order_details.so_details_id',
        DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty")
        ])
        ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
        ->whereIn('sales_order.year_id',$yearIds)
        ->where('sales_order.current_location_id',$locationCode)
        ->where('sales_order_details.fitting_item','no')
        ->having('pend_so_qty','>', 0)  
        ->pluck('sales_order_details.so_details_id')
        ->toArray();

        if(!is_null($request->so_from_id_fix)){
            if($request->so_from_id_fix == 1){
                $so_from_id_fix = ['1','2'];
            }else{
                $so_from_id_fix = ['3'];
            }

        }else{
            $so_from_id_fix = '';
        }


        $so_data = SalesOrderDetail::select(['sales_order.so_number','sales_order.id','sales_order.so_date', 
        'sales_order_details.so_details_id','sales_order_details.so_id','sales_order.special_notes',
        DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),   
        'villages.village_name as customer_village',
        'districts.district_name',
        'dealers.dealer_name',  
        'customer_groups.customer_group_name',
        'sales_order.so_from_value_fix',  'sales_order.so_from_id_fix',  'sales_order.so_type_value_fix','sales_order.customer_reg_no','talukas.taluka_name','states.state_name', 'countries.country_name','mis_category.mis_category', ])
        ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
        ->leftJoin('customer_groups','customer_groups.id', 'sales_order.customer_group_id')
        ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
        ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')        
        ->leftJoin('locations','locations.id', 'sales_order.to_location_id') 
        ->leftJoin('talukas','talukas.id','=','sales_order.customer_taluka')
        ->leftJoin('villages','villages.id','=','sales_order.customer_village')
        ->leftJoin('mis_category','mis_category.id','=','sales_order.mis_category_id')
        ->leftJoin('items','items.id','=','sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->whereIn('sales_order.year_id',$yearIds)
        ->where('sales_order.current_location_id',$locationCode)
        ->whereNotIn('sales_order_details.so_details_id',$tr_sc_sod_ids)
        ->whereIN('sales_order_details.so_details_id',array_merge($sod_details_id,$so_details_id))
         ->when(!is_null($request->so_from_id_fix), function ($query) use ($so_from_id_fix) {
            return $query->whereIn('sales_order.so_from_id_fix', $so_from_id_fix);
        })
        ->groupBy('sales_order.id')       
        ->get();   

        if(isset($edit_so_data)){
            $data = collect($so_data)->merge($edit_so_data);
            $grouped = $data->groupBy('id');    
            

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    return $carry;
                });
            });
    
            $so_data = $merged->values();   

        }
        
        

    if ($so_data != null) {

            foreach ($so_data as $cpKey => $cpVal) {
                if ($cpVal->so_date != null) {
                    $cpVal->so_date = Date::createFromFormat('Y-m-d', $cpVal->so_date)->format('d/m/Y');
                }


                if(isset($request->id)){  
                    $dpd_id = DispatchPlanDetails::where('so_details_id','=',$cpVal->so_details_id)->where('dp_id',$request->id)->first();   
                   
                    $cpVal->dp_details_id = $dpd_id!= null ? $dpd_id->dp_details_id : 0;  
                    
                    $total_loading_qty = LoadingEntryDetails::where('dp_details_id', '=', $cpVal->dp_details_id)->sum('loading_qty');
        
                    if($total_loading_qty != null && $total_loading_qty > 0){
                        $cpVal->in_use = true;
                    } else {
                        $cpVal->in_use = false;                      
                    }
                    
                }else{
                    $cpVal->in_use = false;
                }

                if($cpVal->so_from_value_fix != null){
                        if($cpVal->so_from_value_fix == 'customer'){
                            $so_from_value_fix = 'Subsidy';
                        }elseif($cpVal->so_from_value_fix == 'cash_carry'){
                            $so_from_value_fix = 'Cash & Carry';
                        }else{
                            $so_from_value_fix = ucfirst($cpVal->so_from_value_fix);
                        }
                        $cpVal->so_number = $cpVal->so_number . ' ('.$so_from_value_fix .')';
                }else{
                        $cpVal->so_number = $cpVal->so_number;
                }
                     
                
            }
        }
        $so_data = $so_data->sortBy('id')
        ->values();

        if ($so_data != null) {
                return response()->json([
                    'response_code' => '1',
                    'so_data' => $so_data
                ]);
        } else {
                return response()->json([
                    'response_code' => '0',
                    'po_data' => []
                ]);
                }
    }

    // public function getSOData(Request $request){
    //     // $year_data = getCurrentYearData();
    //     $yearIds = getCompanyYearIdsToTill();

    //     $locationCode = getCurrentLocation()->id;

    //     if(isset($request->id)){
    //         $sod_id = DispatchPlanDetails::select('so_details_id')->where('dp_id','=',$request->id)->get();  

    //         $edit_so_data = DispatchPlanDetails::select(['sales_order.so_number','sales_order.id',             'sales_order.so_date','sales_order_details.so_details_id', 
    //         DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),'dispatch_plan_details.dp_details_id',
    //         'villages.village_name as customer_village',
    //         'districts.district_name',
    //         'dealers.dealer_name',   
    //         'customer_groups.customer_group_name',            
    //         ])
    //         ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
    //         ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')   
    //         ->leftJoin('customer_groups','customer_groups.id', 'sales_order.customer_group_id')
    //         ->leftJoin('villages','villages.id','=','sales_order.customer_village')
    //         ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
    //         ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')              
    //         ->leftJoin('locations','locations.id', 'sales_order.to_location_id') 
    //         ->where('dispatch_plan_details.dp_id',$request->id)
    //         ->get(); 
    //     }
        
    //     $sod_detail = DispatchPlanDetailsDetails::select('sales_order_detail_details.so_details_id')
    //     ->leftJoin('sales_order_detail_details','sales_order_detail_details.sod_details_id','=','dispatch_plan_details_details.so_details_detail_id')
    //     ->get();
        
    //     $pnding_so_data = SalesOrderDetail::select(['sales_order.id',
    //     DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty")
    //     ])
    //     ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
    //     // ->where('sales_order.year_id',$year_data->id)
    //     ->whereIn('sales_order.year_id',$yearIds)
    //     ->where('sales_order.current_location_id',$locationCode)
    //     ->whereNotIn('sales_order_details.so_details_id',$sod_detail)
    //     ->having('pend_so_qty','>', 0)  
    //     ->pluck('sales_order.id');

    //     $so_data = SalesOrderDetail::select(['sales_order.so_number','sales_order.id','sales_order.so_date', 
    //     'sales_order_details.so_details_id','sales_order_details.so_id',
    //     DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),   
    //     'villages.village_name as customer_village',
    //     'districts.district_name',
    //     'dealers.dealer_name',  
    //     'customer_groups.customer_group_name',
    //     ])
    //     ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
    //     ->leftJoin('customer_groups','customer_groups.id', 'sales_order.customer_group_id')
    //     ->leftJoin('villages','villages.id','=','sales_order.customer_village')
    //     ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
    //     ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')          
    //     ->leftJoin('locations','locations.id', 'sales_order.to_location_id') 
    //     // ->where('sales_order.year_id',$year_data->id)
    //     ->whereIn('sales_order.year_id',$yearIds)
    //     ->where('sales_order.current_location_id',$locationCode)
    //     ->whereNotIn('sales_order_details.so_details_id',$sod_detail)
    //     ->whereIN('sales_order.id',$pnding_so_data)
    //     ->groupBy('sales_order.id')       
    //     ->get();

        

    //     if(isset($edit_so_data)){
    //         $data = collect($so_data)->merge($edit_so_data);
    //         $grouped = $data->groupBy('id');    
            

    //         $merged = $grouped->map(function ($items) {
    //             return $items->reduce(function ($carry, $item) {
    //                 if (!$carry) {
    //                     return $item;
    //                 }
    //                 return $carry;
    //             });
    //         });
    
    //         $so_data = $merged->values();   

    //     }
        
        

    // if ($so_data != null) {

    //         foreach ($so_data as $cpKey => $cpVal) {
    //             if ($cpVal->so_date != null) {
    //                 $cpVal->so_date = Date::createFromFormat('Y-m-d', $cpVal->so_date)->format('d/m/Y');
    //             }


    //             if(isset($request->id)){  
    //                 $dpd_id = DispatchPlanDetails::where('so_details_id','=',$cpVal->so_details_id)->where('dp_id',$request->id)->first();   
                   
    //                 $cpVal->dp_details_id = $dpd_id!= null ? $dpd_id->dp_details_id : 0;  
                    
    //                 $total_loading_qty = LoadingEntryDetails::where('dp_details_id', '=', $cpVal->dp_details_id)->sum('loading_qty');
        
    //                 if($total_loading_qty != null && $total_loading_qty > 0){
    //                     $cpVal->in_use = true;
    //                 } else {
    //                     $cpVal->in_use = false;                      
    //                 }
                    
    //             }else{
    //                 $cpVal->in_use = false;
    //             }
                     
                
    //         }
    //     }
    //     $so_data = $so_data->sortBy('id')
    //     ->values();

    //     if ($so_data != null) {
    //             return response()->json([
    //                 'response_code' => '1',
    //                 'so_data' => $so_data
    //             ]);
    //     } else {
    //             return response()->json([
    //                 'response_code' => '0',
    //                 'po_data' => []
    //             ]);
    //             }
    // }

    // old function
    // public function getSOData()
    // {
    //     $year_data = getCurrentYearData();
    //     $locationCode = getCurrentLocation()->id;
    //     $sod_detail = DispatchPlanDetailsDetails::select('sales_order_detail_details.so_details_id')
    //     ->leftJoin('sales_order_detail_details','sales_order_detail_details.sod_details_id','=','dispatch_plan_details_details.so_details_detail_id')
    //     ->get();

    //         $so_data = SalesOrderDetail::select([
    //             'sales_order.so_number', 
    //             'sales_order.id', 
    //             'sales_order.so_date',
    //             'sales_order.customer_name',
    //              // 'sales_order.customer_village',
    //             'villages.village_name as customer_village',
    //             'districts.district_name',
    //             'locations.location_name',
    //             'dealers.dealer_name',
    //             'items.item_name',
    //             'sales_order_details.item_id',
    //             'items.item_code', 
    //             // 'items.fitting_item',
    //             'sales_order_details.fitting_item',
    //             'item_groups.item_group_name', 
    //             'units.unit_name as unitName', 
    //             'sales_order_details.so_details_id',
    //             'sales_order_details.so_qty',
    //             // 'sales_order_detail_details.sod_details_id  as SODDId',
    //             // 'sales_order_detail_details.so_qty  as sodd_qty',

    //             DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),        
                
    //             DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty")

    //             // DB::raw("(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty)
    //             // FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id), 0 ) - IFNULL((SELECT SUM(so_short_close.sc_qty) FROM so_short_close
    //             // WHERE so_short_close.so_details_id = sales_order_details.so_details_id), 0)
    //             // ) as pend_so_qty")
            

    //             // DB::raw('
    //             // CASE 
    //             //     WHEN (SELECT MAX(sod.sod_details_id) FROM sales_order_detail_details sod WHERE sod.so_details_id = sales_order_details.so_details_id) IS NOT NULL THEN 
    //             //         ((SELECT MIN(sod.so_qty) FROM sales_order_detail_details sod WHERE sod.so_details_id = sales_order_details.so_details_id) - IFNULL((SELECT SUM(dpd.plan_qty) FROM dispatch_plan_details dpd WHERE dpd.so_details_id = sales_order_details.so_details_id), 0))
    //             //     ELSE                         
    //             //         (sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id), 0))
    //             // END as pend_so_qty')
    
                
    //             ])
    //             ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
    //             // ->leftJoin('sales_order_detail_details','sales_order_detail_details.so_details_id', 'sales_order_details.so_details_id')
    //             ->leftJoin('villages','villages.id','=','sales_order.customer_village')
    //             ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
    //             ->leftJoin('items','items.id', 'sales_order_details.item_id')
    //             ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
    //             ->leftJoin('units','units.id', 'items.unit_id')
    //             ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
    //             ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
    //             ->where('sales_order.year_id',$year_data->id)
    //             ->where('sales_order.current_location_id',$locationCode)
    //             ->whereNotIn('sales_order_details.so_details_id',$sod_detail)
    //             ->having('pend_so_qty','>', 0)
    //             ->get();
    //             if($so_data != null){
    //                 $po_data = $so_data->filter(function ($so_data) {
    //                     if($so_data->so_date != null){
    //                         $so_data->so_date = Date::createFromFormat('Y-m-d', $so_data->so_date)->format('d/m/Y');
    //                     }
                        

    //                     $locationId = getCurrentLocation()->id;
                        
    //                     if($so_data->so_details_id){
    //                         $sod_detail = SalesOrderDetailsDetails::where('so_details_id',$so_data->so_details_id)->get();
    //                         // dd($sod_detail);
    //                         if(!$sod_detail->isEmpty()){

    //                             // $so_data->pend_so_qty = 1;
    //                             $so_data->stock_qty = 1;

    //                             foreach($sod_detail as $skey => $sval){                      
    //                                $qtySum = LocationStock::where('item_id',$sval->item_id)->where('location_id',$locationId)->sum('stock_qty');
    //                             //    dd($sval->item_id);                                
    //                             //    $so_qty = $so_data->so_qty <= $qtySum ? $so_data->so_qty : 0;
    //                                $so_qty = $sval->so_qty <= $qtySum ? 1 : 0;

    //                                 if($so_qty == 0){
    //                                     // $so_data->pend_so_qty = 0;
    //                                     $so_data->stock_qty = 0;
    //                                 }
    //                             }
    //                         }else{                                
    //                             $qtySum = LocationStock::where('item_id',$so_data->item_id)->where('location_id',$locationId)->sum('stock_qty');                                
    //                             // $so_data->pend_so_qty = $so_data->pend_qty <= $qtySum ? $so_data->pend_qty : 0;
    //                             $so_data->stock_qty = $qtySum;

    //                         }
    //                     }
    //                     return $so_data;
    //                 })->values();
    //             }

    //             if ($po_data != null) {
    //                     return response()->json([
    //                         'response_code' => '1',
    //                         'so_data' => $so_data
    //                     ]);
    //             } else {
    //                     return response()->json([
    //                         'response_code' => '0',
    //                         'po_data' => []
    //                     ]);
    //             }
    // }

    public function store(Request $request)
    {
// dd($request);
        $validated = $request->validate([
            'dp_date' => 'required',
        ],
        [
           'dp_date.required' => 'Please Select Dispatch Plan Date',
        ]);


        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;


        $existNumber = DispatchPlan::where('dp_number','=',$request->dp_number)->where('dp_sequence','=',$request->dp_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationID)->lockForUpdate()->first();
          
        if($existNumber){
            $latestNo = $this->getLatestDispatchNo($request);
            $tmp =  $latestNo->getContent();
            $area = json_decode($tmp, true);
            $dp_number =   $area['latest_dp_no'];
            $dp_sequence = $area['number'];              
        }else{
           $dp_number = $request->dp_number;
           $dp_sequence = $request->dp_sequence;
        }

        if($request->dispatch_from_id_fix == 1){
            $dispatchFormValue =  "customer,cash_carry";
        }
        // elseif($request->dispatch_from_id_fix == 2){
        //     $dispatchFormValue = "cash_carry";
        // }
        elseif($request->dispatch_from_id_fix == 3){
            $dispatchFormValue =  "location";
        }

        $multiple_loading_entry = isset($request->multiple_loading_entry) ? 'Yes' : 'No';

        
        DB::beginTransaction();

        try{
            // first save dispatch form
            $dispatch_plan=  DispatchPlan::create([
                'current_location_id' => $locationID,                
                'dispatch_from_id_fix' => $request->dispatch_from_id_fix,                
                'dispatch_from_value_fix' => $dispatchFormValue,                
                'dp_sequence'         => $dp_sequence,            
                'dp_number'           => $dp_number,
                'dp_date'             => Date::createFromFormat('d/m/Y', $request->dp_date)->format('Y-m-d'),
                'dp_date'             => Date::createFromFormat('d/m/Y', $request->dp_date)->format('Y-m-d'),
                'multiple_loading_entry'  => $multiple_loading_entry,
                'year_id'             => $year_data->id,
                'company_id'          => Auth::user()->company_id,
                'created_on'          => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id'  => Auth::user()->id
            ]);
            if($dispatch_plan){
                $request->dispatch_plan_details = json_decode($request->dispatch_plan_details,true);
                $request->dispatch_plan_details_details = json_decode($request->dispatch_plan_details_details,true);
                $request->dispatch_plan_secondary_details = json_decode($request->dispatch_plan_secondary_details,true);

                if(isset($request->dispatch_plan_details) && !empty($request->dispatch_plan_details)){
                    foreach($request->dispatch_plan_details as $ctKey => $ctVal){
                        if(isset($ctVal['so_detail_id'])  && $ctVal['fitting_item']  == 'no'){

                            $soQtySum =  round(SalesOrderDetail::where('so_details_id',$ctVal['so_detail_id'])->sum('so_qty'),3);
            
                            $useDCQtySum =  round(DispatchPlanDetails::where('so_details_id',$ctVal['so_detail_id'])->sum('plan_qty'),3);
                         

                            $dcQty = isset($ctVal['plan_qty']) && $ctVal['plan_qty'] > 0 ? $ctVal['plan_qty'] : 0;
                            $dcQtySum = $useDCQtySum + $dcQty;  
                            
                            
                            if($soQtySum < $dcQtySum){                            
                                DB::rollBack();
                                return response()->json([
                                    'response_code' => '0',
                                    'response_message' => 'Plan Qty. Is Used',                               
                                ]);
                            }
            
                        }else{

                            $soQtySum = round(SalesOrderDetailsDetails::where('so_details_id',$ctVal['so_detail_id'])->sum('so_qty'),3);

                            if(isset($request->dispatch_plan_details_details[$ctVal['so_detail_id']]) && !empty($request->dispatch_plan_details_details[$ctVal['so_detail_id']])){

                                foreach($request->dispatch_plan_details_details[$ctVal['so_detail_id']] as $skey => $sval){

                                    $useDCQtySum = round(DispatchPlanDetailsDetails::where('so_details_detail_id',$sval['so_details_detail_id'])->sum('plan_qty'),3);

                                    $dcQty = isset($sval['plan_qty']) && $sval['plan_qty'] > 0 ? $sval['plan_qty'] : 0;

                                    $dcQtySum = $useDCQtySum + $dcQty;       

                                    if($soQtySum < $dcQtySum){
                                        DB::rollBack();
                                        return response()->json([
                                            'response_code' => '0',
                                            'response_message' => 'Plan Qty. Is Used',                               
                                        ]);
                                    }
                                }                                    
                            }       

                            // $sod_detail = SalesOrderDetailsDetails::where('so_details_id',$ctVal['so_detail_id'])->get();
                            // if(!$sod_detail->isEmpty()){

                            //     foreach($sod_detail as $skey => $sval){

                            //         $useDCQtySum = round(DispatchPlanDetailsDetails::where('so_details_detail_id',$sval->sod_details_id)->sum('plan_qty'),3);
            
                            //         $dcQty = isset($sval->so_qty) && $sval->so_qty > 0 ? $sval->so_qty : 0;
                            //         $dcQtySum = $useDCQtySum + $dcQty;                          
                    
                            //         if($soQtySum < $dcQtySum){
                            //             DB::rollBack();
                            //             return response()->json([
                            //                 'response_code' => '0',
                            //                 'response_message' => 'Plan Qty. Is Used',                               
                            //             ]);
                            //         }
                            //     }
                            // }           
                           
                        }

                        if($ctVal != null){
                            $dp_details =  DispatchPlanDetails::create([
                                'dp_id' => $dispatch_plan->dp_id,
                                'so_details_id'=> (isset($ctVal['so_detail_id']) &&  $ctVal['so_detail_id'] != "") ? $ctVal['so_detail_id'] : null,
                                'item_id'=>(isset($ctVal['item_id']) != '' ? $ctVal['item_id'] : ''),
                                'plan_qty'=>(isset($ctVal['plan_qty']) && $ctVal['plan_qty'] > 0) ? $ctVal['plan_qty'] : 0,
                                'fitting_item'=>(isset($ctVal['fitting_item']) != '' ? $ctVal['fitting_item'] : ''),
                                'secondary_unit'=> $ctVal['secondary_unit'] == 'null' ? null :  $ctVal['secondary_unit'],
                                'allow_partial_dispatch'=>(isset($ctVal['allow_partial_dispatch']) != '' ? $ctVal['allow_partial_dispatch'] : ''),
                                'so_from_value_fix'=>(isset($ctVal['so_from_value_fix']) != '' ? $ctVal['so_from_value_fix'] : ''),
                                'wt_pc'=>(isset($ctVal['wt_pc']) != '' ? $ctVal['wt_pc'] : ''),
                                'status'=> 'Y',
                            ]);

                            if($ctVal['fitting_item']  == 'no'){
                            stockEffect($locationID,$ctVal['item_id'],$ctVal['item_id'],$ctVal['plan_qty'],0,'add','D','Dispatch Details',$dp_details->id);
                            }
                              
                               

                            if($ctVal['so_detail_id'] != null){
                                // $sod_detail = SalesOrderDetailsDetails::where('so_details_id',$ctVal['so_detail_id'])->get();
                                // if(!$sod_detail->isEmpty()){

                                //     foreach($sod_detail as $skey => $sval){
                                //         $dpd_details =  DispatchPlanDetailsDetails::create([
                                //             'dp_details_id' => $dp_details->id,
                                //             'so_details_detail_id'=> $sval->sod_details_id,
                                //             'item_id'=> $sval->item_id,
                                //             'plan_qty'=>$sval->so_qty,                                               
                                //             'status'=> 'Y',
                                //        ]);
                                        
                                //        stockEffect($locationID,$sval->item_id,$sval->item_id,$sval->so_qty,0,'add','D','Dispatch Details Details',$dpd_details->dpd_details_id);
                                        
                                //     }                          
                                    
                                // }
                                
                                    
                                if(isset($request->dispatch_plan_details_details[$ctVal['so_detail_id']]) && !empty($request->dispatch_plan_details_details[$ctVal['so_detail_id']])){

                                    foreach($request->dispatch_plan_details_details[$ctVal['so_detail_id']] as $skey => $sval){

                                        $dpd_details =  DispatchPlanDetailsDetails::create([
                                            'dp_details_id' => $dp_details->id,
                                            'so_details_detail_id'=> $sval['so_details_detail_id'],
                                            'item_id'=> $sval['item_id'],
                                            'plan_qty'=> $sval['plan_qty'],                             
                                            'status'=> 'Y',
                                        ]);

                                        stockEffect($locationID,$sval['item_id'],$sval['item_id'],$sval['plan_qty'],0,'add','D','Dispatch Details Details',$dpd_details->dpd_details_id);
                                    }
                                    
                                }    
                                
                                
                                if(isset($request->dispatch_plan_secondary_details[$ctVal['so_detail_id']]) && !empty($request->dispatch_plan_secondary_details[$ctVal['so_detail_id']])){

                                    foreach($request->dispatch_plan_secondary_details[$ctVal['so_detail_id']] as $secondkey => $secondval){

                                        $dpd_second_details =  DispatchPlanSecondaryDetails::create([
                                            'dp_details_id' => $dp_details->id,
                                            'so_details_id'=> $secondval['so_details_id'],
                                            'item_id'=> $secondval['item_id'],
                                            'item_details_id'=> $secondval['item_details_id'],
                                            'plan_qty'=>$secondval['plan_qty'],                             
                                            'status'=> 'Y',
                                        ]);

                                        stockDetailsEffect($locationID,$secondval['item_details_id'],$secondval['item_details_id'],$secondval['plan_qty'],0,'add','D','Dispatch Secondary Details',$dpd_second_details->dp_secondary_details_id,'No','Dispatch Details Details',$dp_details->id);

                                    
                                    }
                                    
                                }                                          
                                
                            }

                        }
                    }
                    DB::commit();
                    return response()->json([
                        'response_code' => '1',
                        'response_message' => 'Record Inserted Successfully.',
                    ]);
                }else{
                    DB::rollBack();
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Record Not Inserted',
                    ]);
                }
            }else {
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Inserted.',
                ]);
            }
        }catch (\App\Exceptions\InsufficientStockException $e) {            
            DB::rollBack();     
            getActivityLogs("Dispatch Plan", "add", $e->getMessage(),$e->getLine(),$e->item);  
   
            if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                    'item' => $e->item,
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Error Occured Record Not Inserted',
                    'original_error' => $e->getMessage()
                ]);
            }
        }        
        catch(\Exception $e){

            // DB::rollBack();
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => 'Error Occured Record Not Inserted',
            //     'original_error' => $e->getMessage()
            // ]);

            DB::rollBack();    
            getActivityLogs("Dispatch Plan", "add", $e->getMessage(),$e->getLine());  
        
            if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Error Occured Record Not Inserted',
                    'original_error' => $e->getMessage()
                ]);
            }
        }
    } // store function end


    public  function show($id)
    {
        $so_data = DispatchPlan::where('dp_id', '=', base64_decode($id))->get();
        // dd($so_data);
        return view('edit.edit-dispatch_plan', compact('so_data', 'id'));
    }

    public function edit($id)
    {
        $isAnyPartInUse = false;
        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation()->id;

        $do_data =  DispatchPlan::select('dispatch_plan.dp_id','dispatch_plan.dp_sequence','dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan.special_notes','dispatch_plan.dispatch_from_id_fix','dispatch_plan.multiple_loading_entry')->where('dp_id',$id)->first();
        // $do_data =  DispatchPlan::where('dp_id',$id)->first();

        $do_data->dp_date = $do_data->dp_date !='' ?  Date::createFromFormat('Y-m-d', $do_data->dp_date)->format('d/m/Y') : '';

        $dp_details = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id','dispatch_plan_details.dp_id','dispatch_plan_details.so_details_id','dispatch_plan_details.item_id','dispatch_plan_details.plan_qty as so_qty','dispatch_plan_details.fitting_item','sales_order.so_number','sales_order.so_date','sales_order.customer_name','districts.district_name','locations.location_name', 'dealers.dealer_name','items.item_name','items.item_code', 'item_groups.item_group_name',  'units.unit_name as unitName','sales_order.id','sales_order_details.so_qty as total_so_qty','sales_order_details.so_details_id','dispatch_plan_details.secondary_unit','dispatch_plan_details.allow_partial_dispatch','dispatch_plan_details.so_from_value_fix','dispatch_plan_details.wt_pc',


        // 'sales_order.customer_village',
        'villages.village_name as customer_village','customer_groups.customer_group_name', 

        DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'), 
        
        DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty"),

        // DB::raw("(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty)
        // FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id), 0 ) - IFNULL((SELECT SUM(so_short_close.sc_qty) FROM so_short_close
        // WHERE so_short_close.so_details_id = sales_order_details.so_details_id), 0)
        // ) as pend_so_qty")
        
        // DB::raw('
        // CASE 
        //     WHEN (SELECT MAX(sod.sod_details_id) FROM sales_order_detail_details sod WHERE sod.so_details_id = sales_order_details.so_details_id) IS NOT NULL THEN 
        //         ((SELECT MIN(sod.so_qty) FROM sales_order_detail_details sod WHERE sod.so_details_id = sales_order_details.so_details_id) - IFNULL((SELECT SUM(dpd.plan_qty) FROM dispatch_plan_details dpd WHERE dpd.so_details_id = sales_order_details.so_details_id), 0))
        //     ELSE 
        //         (sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id), 0))
        // END as pend_so_qty')

        
        ])
        

        ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')

        ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')

        ->leftJoin('customer_groups','customer_groups.id', 'sales_order.customer_group_id')

        ->leftJoin('districts','districts.id', 'sales_order.customer_district_id') 

        ->leftJoin('villages','villages.id','=','sales_order.customer_village')
        

        ->leftJoin('locations','locations.id', 'sales_order.to_location_id')

        ->leftJoin('items','items.id', 'dispatch_plan_details.item_id')

        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')

        ->leftJoin('units','units.id', 'items.unit_id')

       ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')

        ->where('dp_id',$id)->get();
        // dd($dp_details);
               
        $so_data_id = []; 
        $so_data_detail_id = []; 
        $groupedDetails = [];
        if($do_data != null){
            // $dp_details = $dp_details->filter(function ($dp_details) {
            //     if($dp_details->so_date != null){
            //         $dp_details->so_date = Date::createFromFormat('Y-m-d', $dp_details->so_date)->format('d/m/Y');
            //     }
            //     return $dp_details;
            // })->values();
           $dp_details->each(function ($dp_details) use (&$isAnyPartInUse,&$so_data_id,&$so_data_detail_id, &$groupedDetails,&$do_data) {
                if($dp_details->so_date != null){
                    $dp_details->so_date = Date::createFromFormat('Y-m-d', $dp_details->so_date)->format('d/m/Y');
                }

                    $total_loading_qty = LoadingEntryDetails::where('dp_details_id', '=', $dp_details->dp_details_id)->sum('loading_qty');

                    if($dp_details->fitting_item == 'yes'){
                        $check_data = LoadingEntryDetails::where('dp_details_id', '=', $dp_details->dp_details_id)->first();

                        if($check_data != null){
                            $dp_details->in_use = true;
                            $isAnyPartInUse = true;
                            $dp_details->used_qty = $dp_details->so_qty;
                        }else{
                            $dp_details->in_use = false;
                            $dp_details->used_qty = 0;
                        }
                            
                    }else{
                        if($do_data->dp_date != null){
                           $date = Date::createFromFormat('d/m/Y', $do_data->dp_date)->format('Y-m-d');
                        }
                        if($total_loading_qty != null && $total_loading_qty > 0 || LiveUpdateSecDate($date,$dp_details->item_id)){
                            $dp_details->in_use = true;
                            $dp_details->used_qty = $total_loading_qty;
                            $isAnyPartInUse = true;
                        } else {
                            $dp_details->in_use = false;
                            $dp_details->used_qty = 0;
                        }
                    }
    
                    


                    $newRequest = new Request();

                    $newRequest->so_details_id = $dp_details->so_details_id;
                    $newRequest->record_id = $dp_details->dp_details_id;
                    $newRequest->total_qty = $dp_details->total_so_qty;
    
                    $dp_details->show_pend_qty = self::getPendingQty($newRequest);


                    $locationId = getCurrentLocation()->id;
                        
                    if($dp_details->so_details_id){
                        $sod_detail = SalesOrderDetailsDetails::where('so_details_id',$dp_details->so_details_id)->get();
                         
                        
                        
                        $sod_detail_total_qty = SalesOrderDetailsDetails::where('so_details_id',$dp_details->so_details_id)->sum('so_qty');
                        if(!$sod_detail->isEmpty()){

                            $dp_detail = DispatchPlanDetailsDetails::select('so_details_detail_id','item_id','plan_qty','dpd_details_id','plan_qty as org_plan_qty')->where('dp_details_id',$dp_details->dp_details_id)->get();
                            $sodDetailArray = [];

                            // $dp_details->stock_qty = 0;
                            $dp_details->stock_qty = 1;

                            // foreach($sod_detail as $skey => $sval){                      
                            //    $qtySum = LocationStock::where('item_id',$sval->item_id)->where('location_id',$locationId)->sum('stock_qty');
                               
                            //    $so_qty = $dp_details->so_qty <= $qtySum ? $dp_details->so_qty : 0;

                            //     if($so_qty == 0){
                            //         // $so_data->pend_so_qty = 0;
                            //         $dp_details->stock_qty = 0;
                            //     }
                            // }

                             $sodDetailArray[$dp_details->so_details_id] =  $dp_detail;
                             $dp_details->sodDetailArray =  $sodDetailArray;

                               $dp_details->require_qty =  $sod_detail_total_qty;
                               $dp_details->check_plan_qty =  $sod_detail_total_qty;

                        }else{                                
                            $qtySum = LocationStock::where('item_id',$dp_details->item_id)->where('location_id',$locationId)->sum('stock_qty');                                
                            // $so_data->pend_so_qty = $so_data->pend_qty <= $qtySum ? $so_data->pend_qty : 0;
                            $dp_details->stock_qty = $qtySum;

                            $sodDetailArray = [];
                            $dp_details->sodDetailArray = $sodDetailArray;
                            

                             $dp_details->require_qty =  $dp_details->so_qty;
                             $dp_details->check_plan_qty =  $dp_details->so_qty;


                        }
                    }

                    if($dp_details->secondary_unit == "Yes"){

                        $dp_second_details = DispatchPlanSecondaryDetails::select('dispatch_plan_secondary_details.*','dispatch_plan_secondary_details.plan_qty as org_plan_qty')->where('so_details_id',$dp_details->so_details_id)->where('dp_details_id',$dp_details->dp_details_id)->get();
                       
                        $dp_details->sodSecondaryDetailArray = $dp_second_details;

                    }else{
                         $dp_details->sodSecondaryDetailArray = [];
                    }

                // Grouping logic
                if (!isset($groupedDetails[$dp_details->id])) {
                    $groupedDetails[$dp_details->id] = []; // Initialize an array for this so_details_id
                }
                $groupedDetails[$dp_details->id][] =$dp_details->so_details_id;

                $so_data_id[] = $dp_details->id;
                // $so_data_detail_id[] = $dp_details->so_details_id;

                if($dp_details->pend_so_qty + $dp_details->so_qty == $dp_details->total_so_qty){
                    $dp_details->allow_multi_vehicle =   'No';
                }else{
                    $dp_details->allow_multi_vehicle =   'Yes';
                }
                   
                    
            })->values();
        }

        if($do_data){
            $do_data->in_use = false;
            if($isAnyPartInUse == true){
                $do_data->in_use = true;
            }
        }


  $so_data_id =  array_unique($so_data_id);
  $mergedSoArray = [];
        
  if($so_data_id != null){
    
    $soArry = SalesOrderDetail::select('so_details_id','so_id')->whereIn('so_id',$so_data_id)->get();

    if($soArry != null){
    foreach($soArry as $key=>$val){
        if (!isset($mergedSoArray[$val->so_id])) {
            $mergedSoArray[$val->so_id] = []; // Initialize an array for this so_details_id
        }
        $mergedSoArray[$val->so_id][] =$val->so_details_id;           
    }

    }    
     
  
    $result = [];

    foreach ($groupedDetails as $key => $values) {
        if (isset($mergedSoArray[$key])) {
            $result[$key] = array_diff($values, $mergedSoArray[$key]);
            $result[$key]= array_diff($mergedSoArray[$key], $values);
        } else {
            $result[$key] = $values;
            // $result[$key] = [];
        }
    }
    
    foreach ($mergedSoArray as $key => $values) {
        if (!isset($groupedDetails[$key])) {
            // $result[$key] = [];
            $result[$key] = $values;
        }
    }

  }

        if ($do_data != null) {
                return response()->json([
                    'response_code' => '1',
                    'do_data' => $do_data,
                    'dp_details' => $dp_details,
                    'edit_item' => $result
                ]);
        } else {
                return response()->json([
                    'response_code' => '0',
                    'po_data' => []
                ]);
        }
    }



    public function update(Request $request)
    {
        // dd($request);
        $validated = $request->validate(
            [             
                'dp_date' => 'required',            
            ],
            [               
                'dp_date.required' => 'Please Select Dispatch Plan Date',
            ]);

            $year_data = getCurrentYearData();
            $locationID = getCurrentLocation()->id;
            DB::beginTransaction();


            if($request->old_dispatch_from_id_fix != null){
                if($request->dispatch_from_id_fix == 1){
                    $dispatchFormValue =  "customer,cash_carry";
                }
                // elseif($request->dispatch_from_id_fix == 2){
                //     $dispatchFormValue = "cash_carry";
                // }
                elseif($request->dispatch_from_id_fix == 3){
                    $dispatchFormValue =  "location";
                }
                 $dispatchFormIdFixValue = $request->dispatch_from_id_fix;
            }else{
                 $dispatchFormIdFixValue = null;
                 $dispatchFormValue = null;
            }
           
            $multiple_loading_entry = isset($request->multiple_loading_entry) ? 'Yes' : 'No';

            try{
                $dispatch_plan=  DispatchPlan::where("dp_id", "=", $request->id)->update([
                    'current_location_id' => $locationID,     
                    'dispatch_from_id_fix' => $dispatchFormIdFixValue,                
                    'dispatch_from_value_fix' => $dispatchFormValue,                  
                    'dp_sequence'         => $request->dp_sequence,            
                    'dp_number'           => $request->dp_number,
                    'dp_date'             => Date::createFromFormat('d/m/Y', $request->dp_date)->format('Y-m-d'),
                    'multiple_loading_entry'  => $multiple_loading_entry,
                    'special_notes'       => $request->special_notes,
                    'year_id'             => $year_data->id,
                    'company_id'          => Auth::user()->company_id,
                    'last_by_user_id' => Auth::user()->id,
                    'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),       
                ]);

                if($dispatch_plan){
                    $request->dispatch_plan_details = json_decode($request->dispatch_plan_details,true);
                    $request->dispatch_plan_details_details = json_decode($request->dispatch_plan_details_details,true);
                    $request->dispatch_plan_secondary_details = json_decode($request->dispatch_plan_secondary_details,true);


                    if(isset($request->dispatch_plan_details) && !empty($request->dispatch_plan_details)){

                        $editplanDetails =  DispatchPlanDetails::where('dp_id',$request->id)->update([
                            'status' => 'D',
                        ]);
    
                        $editplanDetails =  DispatchPlanDetails::where('dp_id',$request->id)->get();
                        if($editplanDetails != null){
                            foreach($editplanDetails as $okey => $oval){
                                $pdpDetails = DispatchPlanDetailsDetails::where('dp_details_id',$oval->dp_details_id)->update([
                                    'status' => 'D',
                                ]);

                                $pdpsecondDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$oval->dp_details_id)->update([
                                    'status' => 'D',
                                ]);
                            }
                        }
                        foreach($request->dispatch_plan_details as $ctKey => $ctVal){
                            if($ctVal != null){

                                if($ctVal['dp_details_id'] == '0'){
                                    $dp_details =  DispatchPlanDetails::create([
                                        'dp_id' => $request->id,
                                        'so_details_id'=> (isset($ctVal['so_detail_id']) &&  $ctVal['so_detail_id'] != "") ? $ctVal['so_detail_id'] : null,
                                        'item_id'=>(isset($ctVal['item_id']) != '' ? $ctVal['item_id'] : ''),
                                        'plan_qty'=>(isset($ctVal['plan_qty']) && $ctVal['plan_qty'] > 0) ? $ctVal['plan_qty'] : 0,
                                        'fitting_item'=>(isset($ctVal['fitting_item']) != '' ? $ctVal['fitting_item'] : ''),
                                        'secondary_unit'=> $ctVal['secondary_unit'] == 'null' ? null :  $ctVal['secondary_unit'],
                                        'allow_partial_dispatch'=>(isset($ctVal['allow_partial_dispatch']) != '' ? $ctVal['allow_partial_dispatch'] : ''),
                                        'so_from_value_fix'=>(isset($ctVal['so_from_value_fix']) != '' ? $ctVal['so_from_value_fix'] : ''),
                                        'wt_pc'=>(isset($ctVal['wt_pc']) != '' ? $ctVal['wt_pc'] : ''),
                                        'status'=> 'Y',
                                   ]);

                                   if($ctVal['fitting_item']  == 'no'){
                                    stockEffect($locationID,$ctVal['item_id'],$ctVal['item_id'],$ctVal['plan_qty'],0,'add','D','Dispatch Details',$dp_details->id);
                                   }
                                  
                                   
    
                                    // if($ctVal['so_detail_id'] != null){
                                    //     $sod_detail = SalesOrderDetailsDetails::where('so_details_id',$ctVal['so_detail_id'])->get();
                                    //     if(!$sod_detail->isEmpty()){
    
                                    //         foreach($sod_detail as $skey => $sval){
                                    //             $dpd_details =  DispatchPlanDetailsDetails::create([
                                    //                 'dp_details_id' => $dp_details->id,
                                    //                 'so_details_detail_id'=> $sval->sod_details_id,
                                    //                 'item_id'=> $sval->item_id,
                                    //                 'plan_qty'=>$sval->so_qty,                                               
                                    //                 'status'=> 'Y',
                                    //            ]);
                                               
                                    //            stockEffect($locationID,$sval->item_id,$sval->item_id,$sval->so_qty,0,'add','D','Dispatch Details Details',$dpd_details->dpd_details_id);
                                               
                                    //         }                          
                                            
                                    //     }
                                    // }

                                     if(isset($request->dispatch_plan_details_details[$ctVal['so_detail_id']]) && !empty($request->dispatch_plan_details_details[$ctVal['so_detail_id']])){

                                    foreach($request->dispatch_plan_details_details[$ctVal['so_detail_id']] as $skey => $sval){

                                        $dpd_details =  DispatchPlanDetailsDetails::create([
                                            'dp_details_id' => $dp_details->id,
                                            'so_details_detail_id'=> $sval['so_details_detail_id'],
                                            'item_id'=> $sval['item_id'],
                                            'plan_qty'=>$sval['plan_qty'],                             
                                            'status'=> 'Y',
                                        ]);

                                        stockEffect($locationID,$sval['item_id'],$sval['item_id'],$sval['plan_qty'],0,'add','D','Dispatch Details Details',$dpd_details->dpd_details_id);
                                    }
                                    
                                }               

                                if(isset($request->dispatch_plan_secondary_details[$ctVal['so_detail_id']]) && !empty($request->dispatch_plan_secondary_details[$ctVal['so_detail_id']])){

                                    foreach($request->dispatch_plan_secondary_details[$ctVal['so_detail_id']] as $secondkey => $secondval){

                                        $dpd_second_details =  DispatchPlanSecondaryDetails::create([
                                            'dp_details_id' => $dp_details->id,
                                            'so_details_id'=> $secondval['so_details_id'],
                                            'item_id'=> $secondval['item_id'],
                                            'item_details_id'=> $secondval['item_details_id'],
                                            'plan_qty'=>$secondval['plan_qty'],                             
                                            'status'=> 'Y',
                                        ]);

                                        stockDetailsEffect($locationID,$secondval['item_details_id'],$secondval['item_details_id'],$secondval['plan_qty'],0,'add','D','Dispatch Secondary Details',$dpd_second_details->dp_secondary_details_id,'No','Dispatch Details Details',$dp_details->id);

                                    
                                    }
                                    
                                }             
    

                                }else{

                                    $dp_details =  DispatchPlanDetails::where('dp_details_id',$ctVal['dp_details_id'])->update([
                                        'dp_id' => $request->id,
                                        'so_details_id'=> (isset($ctVal['so_detail_id']) &&  $ctVal['so_detail_id'] != "") ? $ctVal['so_detail_id'] : null,
                                        'item_id'=>(isset($ctVal['item_id']) != '' ? $ctVal['item_id'] : ''),
                                        'plan_qty'=>(isset($ctVal['plan_qty']) && $ctVal['plan_qty'] > 0) ? $ctVal['plan_qty'] : 0,
                                        'fitting_item'=>(isset($ctVal['fitting_item']) != '' ? $ctVal['fitting_item'] : ''),
                                        'secondary_unit'=> $ctVal['secondary_unit'] == 'null' ? null :  $ctVal['secondary_unit'],
                                        'allow_partial_dispatch'=>(isset($ctVal['allow_partial_dispatch']) != '' ? $ctVal['allow_partial_dispatch'] : ''),
                                        'so_from_value_fix'=>(isset($ctVal['so_from_value_fix']) != '' ? $ctVal['so_from_value_fix'] : ''),
                                        'wt_pc'=>(isset($ctVal['wt_pc']) != '' ? $ctVal['wt_pc'] : ''),
                                        'status'=> 'Y',
                                    ]);
    
                                    if($ctVal['fitting_item']  == 'no'){
                                        stockEffect($locationID,$ctVal['item_id'],$ctVal['item_id'],$ctVal['plan_qty'],$ctVal['org_plan_qty'],'edit','D','Dispatch Details',$ctVal['dp_details_id']);
                                    }
    
    
                                    // if($ctVal['dp_details_id'] != null){
                                    //     $dpd_detail = DispatchPlanDetailsDetails::where('dp_details_id',$ctVal['dp_details_id'])->get();
    
                                    //     if(!$dpd_detail->isEmpty()){
    
                                    //         // stockEffect($locationID,$ctVal['item_id'],$ctVal['item_id'],1,0,'add','D');
                                    //         foreach($dpd_detail as $skey => $sval){
                                    //             $dpd_details =  DispatchPlanDetailsDetails::where('dpd_details_id',$sval->dpd_details_id)->update([
                                    //                 'dp_details_id' => $sval->dp_details_id,
                                    //                 'so_details_detail_id'=> $sval->so_details_detail_id,
                                    //                 'item_id'=> $sval->item_id,
                                    //                 'plan_qty'=>$sval->plan_qty,                                             
                                    //                 'status'=> 'Y',
                                    //            ]);
                                               
                                    //            stockEffect($locationID,$sval->item_id,$sval->item_id,$sval->plan_qty,$sval->plan_qty,'edit','D','Dispatch Details Details',$sval->dpd_details_id);
                                               
                                    //         }                          
                                            
                                    //     }
                                    // }

                                  
                                    if(isset($request->dispatch_plan_details_details[$ctVal['so_detail_id']]) && !empty($request->dispatch_plan_details_details[$ctVal['so_detail_id']])){ 
                                     
                                        foreach($request->dispatch_plan_details_details[$ctVal['so_detail_id']] as $skey => $sval){
                                            if($sval['dpd_details_id'] == '0'){
                                                $dpd_details =  DispatchPlanDetailsDetails::create([
                                                'dp_details_id' => $ctVal['dp_details_id'],
                                                'so_details_detail_id'=> $sval['so_details_detail_id'],
                                                'item_id'=> $sval['item_id'],
                                                'plan_qty'=>$sval['plan_qty'],                             
                                                'status'=> 'Y',
                                                ]);

                                                stockEffect($locationID,$sval['item_id'],$sval['item_id'],$sval['plan_qty'],0,'add','D','Dispatch Details Details',$dpd_details->dpd_details_id);

                                            }else{
                                                $dpd_details =  DispatchPlanDetailsDetails::where('dpd_details_id',$sval['dpd_details_id'])->update([
                                                'dp_details_id' => $ctVal['dp_details_id'],
                                                'so_details_detail_id'=> $sval['so_details_detail_id'],
                                                'item_id'=> $sval['item_id'],
                                                'plan_qty'=>$sval['plan_qty'],                               
                                                'status'=> 'Y',
                                                ]);

                                                stockEffect($locationID,$sval['item_id'],$sval['item_id'],$sval['plan_qty'],$sval['org_plan_qty'],'edit','D','Dispatch Details Details',$sval['dpd_details_id']);
                                            }
                                          
                                        }
                                        
                                       $checkdpdDetails = DispatchPlanDetailsDetails::where('dp_details_id',$ctVal['dp_details_id'])->where('status','D')->get();

                                        if(!$checkdpdDetails->isEmpty()){                          
                                            foreach($checkdpdDetails as $dkey=>$dval){
                                                $pdpDetails = DispatchPlanDetailsDetails::where('dpd_details_id',$dval->dpd_details_id)->where('status','D')->first();
                                                if($pdpDetails != null){
                                                    stockEffect($locationID,$pdpDetails->item_id,$pdpDetails->item_id,0,$pdpDetails->plan_qty,'delete','D','Dispatch Details Details',$pdpDetails->dpd_details_id);
                                                    DispatchPlanDetailsDetails::where('dpd_details_id',$pdpDetails->dpd_details_id)->where('status','D')->delete();

                                                }                                       
                                            }                      
                                        } 
                                    }   
                                    
                                    
                                    if(isset($request->dispatch_plan_secondary_details[$ctVal['so_detail_id']]) && !empty($request->dispatch_plan_secondary_details[$ctVal['so_detail_id']])){

                                        foreach($request->dispatch_plan_secondary_details[$ctVal['so_detail_id']] as $secondkey => $secondval){
                                            if($secondval['dp_secondary_details_id'] == '0'){

                                                $dpd_second_details =  DispatchPlanSecondaryDetails::create([
                                                'dp_details_id' => $ctVal['dp_details_id'],
                                                'so_details_id'=> $secondval['so_details_id'],
                                                'item_id'=> $secondval['item_id'],
                                                'item_details_id'=> $secondval['item_details_id'],
                                                'plan_qty'=>$secondval['plan_qty'],                             
                                                'status'=> 'Y',
                                                ]);

                                                stockDetailsEffect($locationID,$secondval['item_details_id'],$secondval['item_details_id'],$secondval['plan_qty'],0,'add','D','Dispatch Secondary Details',$dpd_second_details->dp_secondary_details_id,'No','Dispatch Details Details',$ctVal['dp_details_id']);

                                            }else{
                                                $dpd_second_details =  DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondval['dp_secondary_details_id'])->update([
                                                'dp_details_id' => $ctVal['dp_details_id'],
                                                'so_details_id'=> $secondval['so_details_id'],
                                                'item_id'=> $secondval['item_id'],
                                                'item_details_id'=> $secondval['item_details_id'],
                                                'plan_qty'=>$secondval['plan_qty'],                             
                                                'status'=> 'Y',
                                                ]);

                                               stockDetailsEffect($locationID,$secondval['item_details_id'],$secondval['item_details_id'],$secondval['plan_qty'],$secondval['org_plan_qty'],'edit','D','Dispatch Secondary Details',$secondval['dp_secondary_details_id'],'No','Dispatch Details Details',$ctVal['dp_details_id']);

                                            }

                                         
                                        
                                        }

                                        $checksecondDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$ctVal['dp_details_id'])->where('status','D')->get();

                                        if(!$checksecondDetails->isEmpty()){                          
                                            foreach($checksecondDetails as $dkey=>$dval){
                                                $secondDetails = DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$dval->dp_secondary_details_id)->where('status','D')->first();
                                                if($secondDetails != null){
                                                    stockDetailsEffect($locationID,$secondDetails->item_details_id,$secondDetails->item_details_id,0,$secondDetails->plan_qty,'delete','D','Dispatch Secondary Details',$secondDetails->dp_secondary_details_id,'No','Dispatch Details Details',$ctVal['dp_details_id']);
                                                    DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondDetails->dp_secondary_details_id)->where('status','D')->delete();

                                                }                                       
                                            }                      
                                        } 
                                        
                                    }             
                                }
                                
                            }
                        }

                        $PlanDetails =  DispatchPlanDetails::where('dp_id',$request->id)->where('status','D')->get();

                        if($PlanDetails != null){
                            foreach($PlanDetails as $okey => $oval){
                                $dpdDetails = DispatchPlanDetailsDetails::where('dp_details_id',$oval->dp_details_id)->where('status','D')->get();

                                if(!$dpdDetails->isEmpty()){                          
                                    foreach($dpdDetails as $dkey=>$dval){
                                        $pdpDetails = DispatchPlanDetailsDetails::where('dpd_details_id',$dval->dpd_details_id)->where('status','D')->first();

                                        if($pdpDetails != null){
                                            stockEffect($locationID,$pdpDetails->item_id,$pdpDetails->item_id,0,$pdpDetails->plan_qty,'delete','D','Dispatch Details Details',$pdpDetails->dpd_details_id);
                                            DispatchPlanDetailsDetails::where('dpd_details_id',$pdpDetails->dpd_details_id)->where('status','D')->delete();

                                        }                                       
                                    }                      
                                } 


                                $oldSecondData = DispatchPlanSecondaryDetails::where('dp_details_id',$oval->dp_details_id)->where('status','D')->get();
                                if(!$oldSecondData->isEmpty()){
                                    foreach($oldSecondData as $dkey=>$dval){       
                                        stockDetailsEffect($locationID,$dval->item_details_id,$dval->item_details_id,0,$dval->plan_qty,'delete','D','Dispatch Secondary Details',$dval->dpd_details_id,'No','Dispatch Details Details',$oval->dp_details_id);
                                        DispatchPlanSecondaryDetails::where('dp_details_id',$dval->dp_details_id)->delete();
                                    }                    
                                }      
                                
                                $dpDetails = DispatchPlanDetails::where('dp_details_id',$oval->dp_details_id)->where('status','D')->first();


                                if($dpDetails != null){
                                    if($dpDetails->fitting_item == 'no'){
                                        stockEffect($locationID,$dpDetails->item_id,$dpDetails->item_id,0,$dpDetails->plan_qty,'delete','D','Dispatch Details',$dpDetails->dp_details_id);
                                    }
                                   
                                    DispatchPlanDetails::where('dp_details_id',$dpDetails->dp_details_id)->where('status','D')->delete();
                                }
                            }
                        }

                        DB::commit();
                        return response()->json([
                            'response_code' => '1',
                            'response_message' => 'Record Updated Successfully.',
                        ]);                 

                    }else{
                     DB::rollBack();
                        return response()->json([
                            'response_code' => '0',
                            'response_message' => 'Record Not Updated',
                        ]);
                    }
                }else {
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Record Not Updated',
                    ]);
                }

            }
            catch (\App\Exceptions\InsufficientStockException $e) {            

                DB::rollBack();     
                getActivityLogs("Dispatch Plan", "update", $e->getMessage(),$e->getLine(),$e->item);  
       
                if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => $e->getMessage(),
                        'item' => $e->item,
                        
                    ]);
                }else{
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Error Occured Record Not Updated',
                        'original_error' => $e->getMessage()
                    ]);
                }
            }
            catch (\Exception $e) {   
                dd($e->getMessage(),$e->getLine())  ;          
                // DB::rollBack();
                // return response()->json([
                //     'response_code' => '0',
                //     'response_message' => 'Error Occured Record Not Updated',
                //     'original_error' => $e->getMessage()
                // ]);

                DB::rollBack();     
                getActivityLogs("Dispatch Plan", "update", $e->getMessage(),$e->getLine());  
       
                if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => $e->getMessage(),
                    ]);
                }else{
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Error Occured Record Not Updated',
                        'original_error' => $e->getMessage()
                    ]);
                }
            }
    }


    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $date = DispatchPlan::where('dp_id',$request->id)->value('dp_date');
            $loading_data = LoadingEntryDetails::
            leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
            ->where('dispatch_plan_details.dp_id',$request->id)->get();
            if($loading_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Dispatch Plan Is Used In Loading Entry.",
                ]);
            }
            
            // this is use for stock maintain
            $locationID = getCurrentLocation()->id;
            $oldDpDetails = DispatchPlanDetails::where('dp_id','=',$request->id)->get();
            $oldDpDetailsData = [];
            if($oldDpDetails != null){
                $oldDpDetailsData = $oldDpDetails->toArray();
            }
            foreach($oldDpDetailsData as $gkey=>$gval){
            $SecUnitBeforeUpdate =  LiveUpdateSecDate($date,$gval['item_id']);

            
            if($SecUnitBeforeUpdate === true){                
                DB::rollBack();     
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
                ]);
            }

                if($gval['fitting_item'] == 'no'){
                    $qty = $gval['plan_qty'];             
                    stockEffect($locationID,$gval['item_id'],$gval['item_id'],0,$qty,'delete','D','Dispatch Details',$gval['dp_details_id']);
                }
                
                $oldDpdData = DispatchPlanDetailsDetails::where('dp_details_id',$gval['dp_details_id'])->get();
               
                if(!$oldDpdData->isEmpty()){
                    foreach($oldDpdData as $dkey=>$dval){       
                        stockEffect($locationID,$dval->item_id,$dval->item_id,0,$dval->plan_qty,'delete','D','Dispatch Details Details',$dval->dpd_details_id);
                        DispatchPlanDetailsDetails::where('dpd_details_id',$dval->dpd_details_id)->delete();
                    }                    
                }      
                
                $oldSecondData = DispatchPlanSecondaryDetails::where('dp_details_id',$gval['dp_details_id'])->get();
                if(!$oldSecondData->isEmpty()){

                    foreach($oldSecondData as $dkey=>$dval){       
                        stockDetailsEffect($locationID,$dval->item_details_id,$dval->item_details_id,0,$dval->plan_qty,'delete','D','Dispatch Secondary Details',$dval->dpd_details_id,'No','Dispatch Details Details',$gval['dp_details_id']);
                        DispatchPlanSecondaryDetails::where('dp_details_id',$dval->dp_details_id)->delete();
                    }                    
                }      
            }         

            DispatchPlanDetails::where('dp_id',$request->id)->delete();
            DispatchPlan::destroy($request->id);

            DB::commit();
            
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }
        catch (\App\Exceptions\InsufficientStockException $e) {            

            DB::rollBack();     
            getActivityLogs("Dispatch Plan", "delete", $e->getMessage(),$e->getLine(),$e->item);  
   
            if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Error Occured Record Not Updated',
                    'original_error' => $e->getMessage()
                ]);
            }
        }
        catch(\Exception $e){
            // if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
            //     $error_msg = "This is used somewhere, you can't delete";
            // }else{
            //     $error_msg = "Record Not Deleted";
            // }
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => $error_msg,
            // ]);

            DB::rollBack(); 
            getActivityLogs("Dispatch Plan", "delete", $e->getMessage());  
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
                // $error_msg = "You Can't Delete, Dispatch Plan Is Used In Loading Entry.";
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $error_msg,
                ]);
            }else if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }
          
        }
    }


    public function getPendingQty(Request $request){
        $exectQty = $request->total_qty;
        $exectQty = number_format((float)$exectQty, 3, '.','');

        $oldRecords = DispatchPlanDetails::select(DB::raw('SUM(plan_qty) as sum'))
        ->where('so_details_id','=',$request->so_details_id)
        ->where('dp_details_id','<=',$request->record_id)
        ->groupBy(['so_details_id'])
        ->first();

        // $oldScRecords = SOShortClose::select(DB::raw('SUM(sc_qty) as sc_sum'))
        // ->where('so_details_id','=',$request->so_details_id)
        // ->groupBy(['so_details_id'])
        // ->first();
        
        // $sc_sum = $oldScRecords ? $oldScRecords->sc_sum : 0;

        if($oldRecords != null){

            $diff = $exectQty - number_format((float)$oldRecords->sum, 3, '.','');
            // $diff = $exectQty - $oldRecords->sum - $sc_sum;

            // if($diff > 0){
            //     return $diff;
            // }else{
            //     return abs($exectQty);
            // }
            return $diff;
        }else{
            return abs($exectQty);
        } 
        
    }

    public function isPartInUse(Request $request){
        if(isset($request->so_part_id) && $request->so_part_id != ""){    
            $isFound = null;  
    
            $isFound =  DispatchPlanDetails::where('so_details_id','=',$request->so_part_id)->first(); 
            
            $isFound =  SOShortClose::where('so_details_id','=',$request->so_part_id)->first(); 

            $isFound =  SOMappingDetails::where('so_details_id','=',$request->so_part_id)->first(); 
    
            if($isFound != null){
                return response()->json([
                    'response_code' => '1',
                    'response_message' => "This is used somewhere, you can't delete",
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "",
                ]);
            }
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => "",
            ]);
        }
    }




public function getSODetailData(Request $request){
    // $year_data = getCurrentYearData();
    $yearIds = getCompanyYearIdsToTill();

    $locationCode = getCurrentLocation()->id;
    $request->chkSOId = explode(',', $request->chkSOId);

    if(isset($request->id)){
        $sod_id = DispatchPlanDetails::select('so_details_id')->where('dp_id','=',$request->id)->get();  

        $edit_so_data = DispatchPlanDetails::select(['sales_order.so_number', 'sales_order.id','sales_order.so_date','sales_order.customer_name','villages.village_name as customer_village','districts.district_name','locations.location_name','dealers.dealer_name','items.item_name','sales_order_details.remarks',
        'sales_order_details.item_id','items.item_code','sales_order_details.fitting_item',
        'item_groups.item_group_name','units.unit_name as unitName','sales_order_details.so_details_id',
        'sales_order_details.so_qty as org_so_qty',
        DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),             
        // DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty"),
        DB::raw('0 as pend_so_qty'), 
        'dispatch_plan_details.plan_qty as so_qty' , 'dispatch_plan_details.dp_details_id',   
         'dispatch_plan_details.allow_partial_dispatch',  
         'dispatch_plan_details.so_from_value_fix',  
        ])
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')   
        ->leftJoin('villages','villages.id','=','sales_order.customer_village')
        ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
        ->leftJoin('items','items.id', 'sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        ->leftJoin('units','units.id', 'items.unit_id')
        ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
        ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
        ->where('dispatch_plan_details.dp_id',$request->id)
        ->whereIn('sales_order_details.so_id',$request->chkSOId)       
        ->get();

    }

       



    // $sod_detail = DispatchPlanDetailsDetails::select('sales_order_detail_details.so_details_id')
    // ->leftJoin('sales_order_detail_details','sales_order_detail_details.sod_details_id','=','dispatch_plan_details_details.so_details_detail_id')
    // ->get();
    $tr_sc_sod_ids = TransactionSOShortClose::select('so_details_id')->pluck('so_details_id')->toArray();


    $sod_details_id = SalesOrderDetailsDetails::select(['sales_order.id','sales_order_details.so_details_id',
    DB::raw("((SELECT sales_order_detail_details.so_qty -  IFNULL(SUM(dispatch_plan_details_details.plan_qty),0) FROM dispatch_plan_details_details  WHERE dispatch_plan_details_details.so_details_detail_id = sales_order_detail_details.sod_details_id)) as pend_sod_qty")
    ])
    ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'sales_order_detail_details.so_details_id')  
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
    ->having('pend_sod_qty','>', 0)  
    ->pluck('sales_order_details.so_details_id')
    ->toArray();

    $so_details_id = SalesOrderDetail::select(['sales_order.id','sales_order_details.so_details_id',
    DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty")
    ])
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
    ->whereIn('sales_order.year_id',$yearIds)
    ->where('sales_order.current_location_id',$locationCode)
    ->where('sales_order_details.fitting_item','no')
    // ->where(function($query) {
    //     $query->where('sales_order_details.secondary_unit', '!=', 'Yes')
    //         ->orWhereNull('sales_order_details.secondary_unit');
    // })
    ->having('pend_so_qty','>', 0)  
    ->pluck('sales_order_details.so_details_id')
    ->toArray();

    // $so_details_seond_id = SalesOrderDetail::select(['sales_order.id','sales_order_details.so_details_id',
    // DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_secondary_details.plan_qty),0) FROM dispatch_plan_secondary_details  WHERE dispatch_plan_secondary_details.so_details_id = sales_order_details.so_details_id)) as pend_so_second_qty")
    // ])
    // ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
    // ->whereIn('sales_order.year_id',$yearIds)
    // ->where('sales_order.current_location_id',$locationCode)
    // ->where('sales_order_details.fitting_item','no')
    // ->where('sales_order_details.secondary_unit','=','Yes')
    // ->having('pend_so_second_qty','>', 0)  
    // ->pluck('sales_order_details.so_details_id')
    // ->toArray();

    $so_data = SalesOrderDetail::select(['sales_order.so_number', 'sales_order.id', 'sales_order.so_date',
    'sales_order.customer_name','villages.village_name as customer_village','districts.district_name','locations.location_name','dealers.dealer_name','items.item_name','sales_order_details.remarks','items.show_item_in_print',
    'sales_order_details.item_id','items.item_code','sales_order_details.fitting_item',
    'item_groups.item_group_name','units.unit_name as unitName','sales_order_details.so_details_id',
    'sales_order_details.so_qty',DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),             
    DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty"), 
    DB::raw('0 as so_qty'),      
    'sales_order_details.so_qty as org_so_qty', 
    'sales_order_details.allow_partial_dispatch',
    'sales_order.so_from_value_fix',
    ])
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')            
    ->leftJoin('villages','villages.id','=','sales_order.customer_village')
    ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
    ->leftJoin('items','items.id', 'sales_order_details.item_id')
    ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
    ->leftJoin('units','units.id', 'items.unit_id')
    ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
    ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
    // ->where('sales_order.year_id',$year_data->id)
    ->whereIn('sales_order.year_id',$yearIds)
    ->whereNotIn('sales_order_details.so_details_id',$tr_sc_sod_ids)

    ->where('sales_order.current_location_id',$locationCode)
    // ->whereNotIn('sales_order_details.so_details_id',$sod_detail)
    ->whereIN('sales_order_details.so_details_id',array_merge($sod_details_id,$so_details_id))
    // ->whereIN('sales_order_details.so_details_id',array_merge($sod_details_id,$so_details_id,$so_details_seond_id))
    ->whereIn('sales_order.id',$request->chkSOId)
    ->having('pend_so_qty','>', 0)
    ->get();
// dd($so_data);
    if(isset($edit_so_data)){
        $data = collect($so_data)->merge($edit_so_data);
        $grouped = $data->groupBy('so_details_id');    
        

        $merged = $grouped->map(function ($items) {
            return $items->reduce(function ($carry, $item) {
                if (!$carry) {
                    return $item;
                }
                $carry->pend_so_qty += (float) $item->pend_so_qty;
                $carry->so_qty += (float) $item->so_qty;
                // $carry->grn_qty += (float) $item->grn_qty;
                return $carry;
            });
        });

        $so_data = $merged->values();   

    }

    if($so_data != null){
        $so_data = $so_data->filter(function ($so_data) use ($request) {
            if($so_data->so_date != null){
                $so_data->so_date = Date::createFromFormat('Y-m-d', $so_data->so_date)->format('d/m/Y');
            }
            

            $locationId = getCurrentLocation()->id;
            
            if($so_data->so_details_id){
                $sod_detail = SalesOrderDetailsDetails::where('so_details_id',$so_data->so_details_id)->get();

                if(!$sod_detail->isEmpty()){
                    $so_data->stock_qty = 1;

                    foreach($sod_detail as $skey => $sval){                      
                        $qtySum = LocationStock::where('item_id',$sval->item_id)->where('location_id',$locationId)->sum('stock_qty');                                          
                        $so_qty = $sval->so_qty <= $qtySum ? 1 : 0;
                        if($so_qty == 0){
                            $so_data->stock_qty = 0;
                        }
                    }
                }else{                                
                    $qtySum = LocationStock::where('item_id',$so_data->item_id)->where('location_id',$locationId)->sum('stock_qty');                                
                    $so_data->stock_qty = $qtySum;

                }
            }

           


            if(isset($request->id)){  
                $dpd_id = DispatchPlanDetails::where('so_details_id','=',$so_data->so_details_id)->where('dp_id',$request->id)->first();     
                $so_data->dp_details_id = $dpd_id!= null ? $dpd_id->dp_details_id : 0;  
                
                $total_loading_qty = LoadingEntryDetails::where('dp_details_id', '=', $so_data->dp_details_id)->sum('loading_qty');
    
                if($total_loading_qty != null && $total_loading_qty > 0){
                    $so_data->in_use = true;
                    $so_data->used_qty = $total_loading_qty;
                    $isAnyPartInUse = true;
                } else {
                    $so_data->in_use = false;
                    $so_data->used_qty = 0;
                }
                
            }else{
                $so_data->dp_details_id = 0;
            }

             $total_so_qty = SalesOrderDetail::where('so_details_id',$so_data->so_details_id)->sum('so_qty');
            
            $newRequest = new Request();

            $newRequest->so_details_id = $so_data->so_details_id;
            $newRequest->record_id = $so_data->dp_details_id;
            if(isset($request->id)){                 
                if($so_data->dp_details_id != '0'){
                    $newRequest->total_qty = $total_so_qty;
                }else{
                    $newRequest->total_qty = $so_data->pend_so_qty;
                }
             }else{
                 $newRequest->total_qty = $so_data->pend_so_qty;
             }

            // $so_data->pend_so_qty = self::getPendingQty($newRequest);
            $so_data->show_pend_so_qty = self::getPendingQty($newRequest);
            
            return $so_data;
        })->values();
    }

    $so_data = $so_data
    ->sortBy('id') // Secondary sort key
    ->sortBy('so_details_id') // Primary sort key
    ->values(); // Reindex numerically

    
    if ($so_data != null) {
            return response()->json([
                'response_code' => '1',
                'so_data' => $so_data
            ]);
    } else {
            return response()->json([
                'response_code' => '0',
                'po_data' => []
            ]);
    }
}


public function getSOPartData(Request $request){
    // $year_data = getCurrentYearData();
    $yearIds = getCompanyYearIdsToTill();

    $locationCode = getCurrentLocation()->id;

    $request->so_details_ids = explode(',', $request->so_details_ids);


    if(isset($request->id)){
        $sod_id = DispatchPlanDetails::select('so_details_id')->where('dp_id','=',$request->id)->get();  

        $edit_so_data = DispatchPlanDetails::select(['sales_order.so_number','sales_order.id','sales_order.so_date','sales_order.customer_name','villages.village_name as customer_village','districts.district_name','locations.location_name','dealers.dealer_name','items.item_name','sales_order_details.item_id','items.item_code','sales_order_details.fitting_item','item_groups.item_group_name', 
        'units.unit_name as unitName','sales_order_details.so_details_id','sales_order_details.so_qty',        
        DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),
        DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty"),
        'dispatch_plan_details.plan_qty as so_qty' , 'dispatch_plan_details.dp_details_id'  ,'customer_groups.customer_group_name', 'dispatch_plan_details.allow_partial_dispatch',
        'dispatch_plan_details.secondary_unit', 
        'dispatch_plan_details.so_from_value_fix',  
        'dispatch_plan_details.wt_pc',   
        'sales_order_details.so_qty as check_allow_for_so_qty',  
                 
        ])
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')   
        ->leftJoin('customer_groups','customer_groups.id', 'sales_order.customer_group_id')
        ->leftJoin('villages','villages.id','=','sales_order.customer_village')
        ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
        ->leftJoin('items','items.id', 'sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        ->leftJoin('units','units.id', 'items.unit_id')
        ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
        ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
        ->where('dispatch_plan_details.dp_id',$request->id)
        ->whereIn('sales_order_details.so_details_id',$request->so_details_ids)       
        ->get();

    }

    
    // $sod_detail = DispatchPlanDetailsDetails::select('sales_order_detail_details.so_details_id')
    // ->leftJoin('sales_order_detail_details','sales_order_detail_details.sod_details_id','=','dispatch_plan_details_details.so_details_detail_id')
    // ->get();

    $tr_sc_sod_ids = TransactionSOShortClose::select('so_details_id')->pluck('so_details_id')->toArray();


    $sod_details_id = SalesOrderDetailsDetails::select(['sales_order.id','sales_order_details.so_details_id',
    DB::raw("((SELECT sales_order_detail_details.so_qty -  IFNULL(SUM(dispatch_plan_details_details.plan_qty),0) FROM dispatch_plan_details_details  WHERE dispatch_plan_details_details.so_details_detail_id = sales_order_detail_details.sod_details_id)) as pend_sod_qty")
    ])
    ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'sales_order_detail_details.so_details_id')  
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
    ->having('pend_sod_qty','>', 0)  
    ->pluck('sales_order_details.so_details_id')
    ->toArray();


    $so_details_id = SalesOrderDetail::select(['sales_order.id','sales_order_details.so_details_id',
    DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty")
    ])
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
    ->whereIn('sales_order.year_id',$yearIds)
    ->where('sales_order.current_location_id',$locationCode)
    ->where('sales_order_details.fitting_item','no')
    ->having('pend_so_qty','>', 0)  
    ->pluck('sales_order_details.so_details_id')
    ->toArray();

    $so_data = SalesOrderDetail::select(['sales_order.so_number','sales_order.id','sales_order.so_date',
    'sales_order.customer_name','villages.village_name as customer_village','districts.district_name',
    'locations.location_name','dealers.dealer_name','items.item_name','sales_order_details.item_id',
    'items.item_code','sales_order_details.fitting_item','item_groups.item_group_name', 'sales_order_details.secondary_unit','sales_order_details.allow_partial_dispatch',
    'units.unit_name as unitName','sales_order_details.so_details_id','customer_groups.customer_group_name', 
    // 'sales_order_details.so_qty',        
     
    DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),           
    DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty"),
    DB::raw('0 as so_qty'),       
    'sales_order.so_from_value_fix',  
    'items.wt_pc',   
    'items.require_raw_material_mapping',   
    'items.secondary_unit as item_secondary_unit',   
    'sales_order_details.so_qty as check_allow_for_so_qty',        
        
    ])
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
    ->leftJoin('customer_groups','customer_groups.id', 'sales_order.customer_group_id')
    ->leftJoin('villages','villages.id','=','sales_order.customer_village')
    ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
    ->leftJoin('items','items.id', 'sales_order_details.item_id')
    ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
    ->leftJoin('units','units.id', 'items.unit_id')
    ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
    ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
    // ->where('sales_order.year_id',$year_data->id)
    ->whereIn('sales_order.year_id',$yearIds)
    ->whereNotIn('sales_order_details.so_details_id',$tr_sc_sod_ids)
    ->where('sales_order.current_location_id',$locationCode)
    ->whereIn('sales_order_details.so_details_id', $request->so_details_ids)
    // ->whereNotIn('sales_order_details.so_details_id',$sod_detail)
    ->whereIN('sales_order_details.so_details_id',array_merge($sod_details_id,$so_details_id))
    ->having('pend_so_qty','>', 0)
    ->get();

    if(isset($edit_so_data)){
        $data = collect($so_data)->merge($edit_so_data);
        $grouped = $data->groupBy('so_details_id');    
        

        $merged = $grouped->map(function ($items) {
            return $items->reduce(function ($carry, $item) {
                if (!$carry) {
                    return $item;
                }
                // $carry->pend_so_qty += (float) $item->pend_so_qty;
                $carry->so_qty += (float) $item->so_qty;
                return $carry;
            });
        });

        $so_data = $merged->values();   

    }

    if($so_data != null){
        $so_data = $so_data->filter(function ($so_data)use ($request) {

            if($so_data->so_date != null){
                $so_data->so_date = Date::createFromFormat('Y-m-d', $so_data->so_date)->format('d/m/Y');
            }
            

            $locationId = getCurrentLocation()->id;
            
            if($so_data->so_details_id){
                // $sod_detail = SalesOrderDetailsDetails::where('so_details_id',$so_data->so_details_id)->get();
                $sod_detail = SalesOrderDetailsDetails::select('sod_details_id as so_details_detail_id','item_id','so_qty', DB::raw("((SELECT sales_order_detail_details.so_qty -  IFNULL(SUM(dispatch_plan_details_details.plan_qty),0) FROM dispatch_plan_details_details  WHERE dispatch_plan_details_details.so_details_detail_id = sales_order_detail_details.sod_details_id)) as plan_qty"),DB::raw('0 as dpd_details_id'),'so_qty as org_plan_qty')
                ->where('so_details_id',$so_data->so_details_id)
                ->having('plan_qty','>',0)
                ->get();

                if(!$sod_detail->isEmpty()){
                    $so_data->stock_qty = 1;

                    $sodDetailArray = [];
                    $total_so_detail_qty = 0;
                    foreach($sod_detail as $skey => $sval){                      
                        $qtySum = LocationStock::where('item_id',$sval->item_id)->where('location_id',$locationId)->sum('stock_qty');                      
                      

                        if($so_data->fitting_item == 'yes'){
                            $so_qty = $sval->plan_qty <= $qtySum ? 1 : 0;
                             if($so_qty == 0){
                                $so_data->stock_qty = 0;                           
                            }
                        }else{
                            $so_qty = $sval->so_qty <= $qtySum ? 1 : 0;
                            
                            if($so_qty == 0){
                                $so_data->stock_qty = 0;                           
                            }
                        }           
                        
                        $total_so_detail_qty += $sval->plan_qty;
                        
                    }

                

                    if($so_data->stock_qty ==  0){
                         $sodDetailArray = [];
                          $so_data->sodDetailArray = $sodDetailArray;
                    }else{
                        $sodDetailArray[$so_data->so_details_id] =  $sod_detail;
                        $so_data->sodDetailArray =  $sodDetailArray;
                    }

                     
                }else{                                
                    $qtySum = LocationStock::where('item_id',$so_data->item_id)->where('location_id',$locationId)->sum('stock_qty');

                    $so_data->stock_qty = $qtySum;
                    $sodDetailArray = [];

                    $so_data->sodDetailArray = $sodDetailArray;

                   
                     

                 

                }
            }

            if(isset($request->id)){  
                $dpd_id = DispatchPlanDetails::where('so_details_id','=',$so_data->so_details_id)->where('dp_id',$request->id)->first();     
                $so_data->dp_details_id = $dpd_id!= null ? $dpd_id->dp_details_id : 0;  
                
                $total_loading_qty = LoadingEntryDetails::where('dp_details_id', '=', $so_data->dp_details_id)->sum('loading_qty');
    
                if($total_loading_qty != null && $total_loading_qty > 0){
                    $so_data->in_use = true;
                    $so_data->used_qty = $total_loading_qty;
                    $isAnyPartInUse = true;
                } else {
                    $so_data->in_use = false;
                    $so_data->used_qty = 0;
                }
                
            }else{
                $so_data->dp_details_id = 0;
            }

            $total_so_qty = SalesOrderDetail::where('so_details_id',$so_data->so_details_id)->sum('so_qty');

            $newRequest = new Request();

            $newRequest->so_details_id = $so_data->so_details_id;
            $newRequest->record_id = $so_data->dp_details_id;
            // $newRequest->total_qty = $total_so_qty;

            if(isset($request->id)){                 
                if($so_data->dp_details_id != '0'){
                    $newRequest->total_qty = $total_so_qty;
                }else{
                    $newRequest->total_qty = $so_data->pend_so_qty;
                }
             }else{
                 $newRequest->total_qty = $so_data->pend_so_qty;
             }        

            $so_data->show_pend_qty = self::getPendingQty($newRequest);

            if($so_data->dp_details_id == 0){

                if($so_data->fitting_item == 'yes'){
                     $so_data->require_qty =  $total_so_detail_qty;
                    $so_data->check_plan_qty =  $total_so_detail_qty;
                }else{
                    $so_data->require_qty =  $so_data->pend_so_qty;
                    $so_data->check_plan_qty =  $so_data->pend_so_qty;
                }

                if($so_data->pend_so_qty == $so_data->check_allow_for_so_qty){
                    $so_data->allow_multi_vehicle =   'No';
                }else{
                    $so_data->allow_multi_vehicle =   'Yes';
                }
               
               
            }else{
                if($so_data->fitting_item == 'yes'){

                    $sod_detail_total_qty = SalesOrderDetailsDetails::where('so_details_id',$so_data->so_details_id)->sum('so_qty');

                    $so_data->require_qty =  $so_data->sod_detail_total_qty;
                    $so_data->check_plan_qty =  $so_data->sod_detail_total_qty;


                }else{
                    $so_data->require_qty =  $so_data->so_qty;
                    $so_data->check_plan_qty =  $so_data->so_qty;
                }

                if($so_data->pend_so_qty + $so_data->so_qty == $so_data->check_allow_for_so_qty){
                    $so_data->allow_multi_vehicle =   'No';
                }else{
                    $so_data->allow_multi_vehicle =   'Yes';
                }
               
               

            }


            if($so_data->require_raw_material_mapping == 'Yes' && $so_data->item_secondary_unit == 'Yes'){
                // $detail_wt_pc = ItemDetails::where('item_id',$so_data->item_id)->sum('secondary_wt_pc');
                // $so_data->wt_pc = $detail_wt_pc;
                // $detail_wt_pc = ItemDetails::where('item_id',$so_data->item_id)->sum('secondary_wt_pc');
                $so_data->wt_pc = 0.000;

            }else if($so_data->require_raw_material_mapping == 'Yes' && $so_data->item_secondary_unit == 'No'){               
                // $mappin_sum = ItemRawMaterialMappingDetail::where('item_id','=',$so_data->item_id)->sum('wt_pc');
                // dd($mappin_sum);
                // dd($mappin_sum);

                $mappin_sum = 0;
                $getItem = ItemRawMaterialMappingDetail::where('item_id','=',$so_data->item_id)->get();

                foreach($getItem as $fkey=>$fval){
                    $item_wt_pc = Item::where('id',$fval->raw_material_id)->sum('wt_pc');
                    $mappin_sum += $item_wt_pc;

                }
                
                $so_data->wt_pc =  $mappin_sum;
            }else{
                $so_data->wt_pc =  $so_data->wt_pc;
                
            }

           

                
            return $so_data;
        })->values();
    }

    $so_data = $so_data->sortBy('id')
        ->sortBy('so_details_id')
        ->values();

    if ($so_data != null) {
            return response()->json([
                'response_code' => '1',
                'so_data' => $so_data
            ]);
    } else {
            return response()->json([
                'response_code' => '0',
                'po_data' => []
            ]);
    }
}


public function getFittingSoItemForDispatch(Request $request){
   $locationCode = getCurrentLocation()->id;

    $soFittingItem = SalesOrderDetailsDetails::select([
    'sales_order_detail_details.sod_details_id',
    'sales_order_detail_details.item_id',
    'items.item_name',
    'items.item_code',
    'item_groups.item_group_name',
    'units.unit_name',
    'sales_order_detail_details.so_qty',
    DB::raw('0 as dpd_details_id'),
    DB::raw("((SELECT sales_order_detail_details.so_qty -  IFNULL(SUM(dispatch_plan_details_details.plan_qty),0) FROM dispatch_plan_details_details  WHERE dispatch_plan_details_details.so_details_detail_id = sales_order_detail_details.sod_details_id)) as pend_sod_qty"),
    DB::raw('IFNULL(location_stock.stock_qty, 0) as stock_qty') // Use IFNULL to handle NULL values
    ])
    ->leftJoin('items', 'items.id', 'sales_order_detail_details.item_id')
    ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
    ->leftJoin('units', 'units.id', 'items.unit_id')
    ->leftJoin('location_stock', function ($join) use ($locationCode) {
        $join->on('location_stock.item_id', '=', 'sales_order_detail_details.item_id')
            ->where('location_stock.location_id', '=', $locationCode); // Ensure location-specific stock
    })
    ->where('sales_order_detail_details.so_details_id', $request->so_detail_id)
    ->get();


    if(isset($request->id) && $soFittingItem->isNotEmpty()){
        foreach($soFittingItem as $sKey => $sVal){
        //   $dodData = DispatchPlanDetailsDetails::select('dpd_details_id','plan_qty')->where('so_details_detail_id',$sVal->sod_details_id)->first();
          $dodData = DispatchPlanDetails::select('dispatch_plan_details_details.dpd_details_id','dispatch_plan_details_details.plan_qty')
          ->leftJoin('dispatch_plan_details_details','dispatch_plan_details_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
          ->where('dispatch_plan_details.dp_id',$request->id)
          ->where('dispatch_plan_details_details.so_details_detail_id',$sVal->sod_details_id)
          ->first();
            
          if($dodData != null){

              $sVal->pend_sod_qty = $sVal->pend_sod_qty + round($dodData->plan_qty,3)   ;
              $sVal->org_plan_qty = round($dodData->plan_qty,3);
              $sVal->dpd_details_id = $dodData->dpd_details_id;
          }else{
              $sVal->pend_sod_qty = $sVal->pend_sod_qty ;
                $sVal->dpd_details_id = 0;
          }   

        }

    }
    $soFittingItem = $soFittingItem->filter(function ($item) {
        return $item->pend_sod_qty > 0;
    })->values(); // reset array



    if ($soFittingItem != null) {
        return response()->json([
            'response_code' => '1',
            'soFittingItem' => $soFittingItem
        ]);
    } else {
        return response()->json([
            'response_code' => '0',
            'soFittingItem' => []
        ]);
    }

}


public function getSecondarySoItemForDispatch(Request $request){
   $locationCode = getCurrentLocation()->id;

    $soSecondaryItem = SalesOrderDetail::select([
    'sales_order_details.so_details_id',
    'sales_order_details.item_id',
    'item_details.item_details_id',
    'item_details.secondary_item_name as item_name',
    'item_details.secondary_qty',
    'item_details.secondary_wt_pc',
    'items.item_code',
    'item_groups.item_group_name',
    'units.unit_name',
    'seond_units.unit_name as second_unit',
    'sales_order_details.so_qty',
    DB::raw('0 as dp_secondary_details_id'),
    DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_sod_qty"),
    // DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_secondary_details.plan_qty),0) FROM dispatch_plan_secondary_details  WHERE dispatch_plan_secondary_details.so_details_id = sales_order_details.so_details_id)) as pend_sod_qty"),
    DB::raw('IFNULL(location_stock_details.stock_qty, 0) as stock_qty'), 
    DB::raw('IFNULL(location_stock_details.secondary_stock_qty, 0) as secondary_stock_qty') 
    ])
    ->leftJoin('items', 'items.id', 'sales_order_details.item_id')
    ->leftJoin('item_details', 'item_details.item_id', 'items.id')
    ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
    ->leftJoin('units', 'units.id', 'items.unit_id')
    ->leftJoin('units as seond_units','seond_units.id','=','items.second_unit')
    ->leftJoin('location_stock_details', function ($join) use ($locationCode) {
        $join->on('location_stock_details.item_details_id', '=', 'item_details.item_details_id')
            ->where('location_stock_details.location_id', '=', $locationCode); // Ensure location-specific stock
    })
    ->where('sales_order_details.so_details_id', $request->so_detail_id)
    ->get();
// dd($soSecondaryItem);


    if(isset($request->id) && $soSecondaryItem->isNotEmpty()){
        foreach($soSecondaryItem as $sKey => $sVal){        

          $secondData = DispatchPlanSecondaryDetails::select('dispatch_plan_secondary_details.dp_secondary_details_id','dispatch_plan_secondary_details.plan_qty')
          ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','dispatch_plan_secondary_details.dp_details_id')
          ->where('dispatch_plan_secondary_details.so_details_id',$sVal->so_details_id)
          ->where('dispatch_plan_secondary_details.item_details_id',$sVal->item_details_id)
          ->where('dispatch_plan_details.dp_id',$request->id)
        //   ->groupBy('dispatch_plan_secondary_details.dp_secondary_details_id')
          ->first();

        //   $totalsecondQty = DispatchPlanSecondaryDetails::
        //   leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','dispatch_plan_secondary_details.dp_details_id')
        //   ->where('dispatch_plan_secondary_details.so_details_id',$sVal->so_details_id)
        // //   ->where('dispatch_plan_secondary_details.item_details_id',$sVal->item_details_id)
        //   ->where('dispatch_plan_details.dp_id',$request->id)
        //   ->sum('dispatch_plan_secondary_details.plan_qty');

         $totalsecondQty = DispatchPlanDetails::where('dispatch_plan_details.so_details_id', $request->so_detail_id)->sum('dispatch_plan_details.plan_qty'); 

          if($secondData != null){
            $sVal->dp_secondary_details_id = $secondData->dp_secondary_details_id;
            $sVal->org_plan_qty = $secondData->plan_qty;
          }
 
          $sVal->pend_sod_qty = $sVal->pend_sod_qty + $totalsecondQty;
        }


    }
    // $soSecondaryItem = $soSecondaryItem->filter(function ($item) {
    //     return $item->pend_sod_qty > 0;
    // })->values(); // reset array



    if ($soSecondaryItem->isNotempty()) {
        return response()->json([
            'response_code' => '1',
            'soSecondaryItem' => $soSecondaryItem
        ]);
    } else {
        return response()->json([
            'response_code' => '0',
            'soSecondaryItem' => []
        ]);
    }

}



public function getAssemblySoItemForDispatch(Request $request){
   $locationCode = getCurrentLocation()->id;


 $soSecondaryItem = SalesOrderDetail::select([
    'sales_order_details.so_details_id',
    'sales_order_details.item_id',
    'items.item_name',
    'items.item_code',
    'item_groups.item_group_name',
    'units.unit_name',
    'sales_order_details.so_qty',
    'item_raw_material_mapping_details.raw_material_id',
    DB::raw('0 as dp_secondary_details_id'),
    DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_secondary_details.plan_qty),0) FROM dispatch_plan_secondary_details  WHERE dispatch_plan_secondary_details.so_details_id = sales_order_details.so_details_id)) as pend_sod_qty"),
    DB::raw('IFNULL(location_stock.stock_qty, 0) as stock_qty') 
    ])
    ->leftJoin('item_raw_material_mapping_details', 'item_raw_material_mapping_details.item_id', 'sales_order_details.item_id')
    ->leftJoin('items', 'items.id', 'item_raw_material_mapping_details.raw_material_id')
    ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
    ->leftJoin('units', 'units.id', 'items.unit_id')
    ->leftJoin('location_stock', function ($join) use ($locationCode) {
        $join->on('location_stock.item_id', '=', 'item_raw_material_mapping_details.raw_material_id')
            ->where('location_stock.location_id', '=', $locationCode); 
    })
    ->where('sales_order_details.so_details_id', $request->so_detail_id)
    ->get();

   

    if(isset($request->id) && $soSecondaryItem->isNotEmpty()){
        foreach($soSecondaryItem as $sKey => $sVal){        

          $secondData = DispatchPlanSecondaryDetails::select('dispatch_plan_secondary_details.dp_secondary_details_id','dispatch_plan_secondary_details.plan_qty')
          ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','dispatch_plan_secondary_details.dp_details_id')
          ->where('dispatch_plan_secondary_details.so_details_id',$sVal->so_details_id)
          ->where('dispatch_plan_secondary_details.raw_material_id',$sVal->raw_material_id)
          ->where('dispatch_plan_details.dp_id',$request->id)
          ->first();

          $totalsecondQty = DispatchPlanSecondaryDetails::
          leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','dispatch_plan_secondary_details.dp_details_id')
          ->where('dispatch_plan_secondary_details.so_details_id',$sVal->so_details_id)
          ->where('dispatch_plan_details.dp_id',$request->id)
          ->sum('dispatch_plan_secondary_details.plan_qty');

          if($secondData != null){
            $sVal->dp_secondary_details_id = $secondData->dp_secondary_details_id;
            $sVal->org_plan_qty = $secondData->plan_qty;
            $sVal->stock_qty = $sVal->stock_qty + $secondData->plan_qty;
          }
 
          $sVal->pend_sod_qty = $sVal->pend_sod_qty + $totalsecondQty;
         

        

       

        }


    }
    


    if ($soSecondaryItem->isNotempty()) {
        return response()->json([
            'response_code' => '1',
            'soSecondaryItem' => $soSecondaryItem
        ]);
    } else {
        return response()->json([
            'response_code' => '0',
            'soSecondaryItem' => []
        ]);
    }

}

public function managePendingSo(){
    return view('manage.manage-pending_so_list');
}

public function indexPendingSo(){

    $yearIds = getCompanyYearIdsToTill();

    $locationCode = getCurrentLocation()->id;
 

    $sod_detail = DispatchPlanDetailsDetails::select('sales_order_detail_details.so_details_id')
    ->leftJoin('sales_order_detail_details','sales_order_detail_details.sod_details_id','=','dispatch_plan_details_details.so_details_detail_id')
    ->get();
    
    $pnding_so_data = SalesOrderDetail::select(['sales_order.id',
    DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_so_qty")
    ])
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')  
    // ->where('sales_order.year_id',$year_data->id)
    ->whereIn('sales_order.year_id',$yearIds)
    ->where('sales_order.current_location_id',$locationCode)
    ->whereNotIn('sales_order_details.so_details_id',$sod_detail)
    ->having('pend_so_qty','>', 0)  
    ->pluck('sales_order.id');
    

    $so_data = SalesOrderDetail::select(['sales_order.so_date','sales_order.net_amount','sales_order.customer_reg_no','sales_order.so_from_value_fix','sales_order.customer_name','locations.location_name',   
    
    'villages.village_name',
    'districts.district_name',
    'dealers.dealer_name', 'mis_category.mis_category' ,'talukas.taluka_name',
    ])
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
    ->leftJoin('customer_groups','customer_groups.id', 'sales_order.customer_group_id')
    ->leftJoin('villages','villages.id','=','sales_order.customer_village')
    ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
    ->leftJoin('talukas','talukas.id','=','sales_order.customer_taluka')
    ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')          
    ->leftJoin('locations','locations.id', 'sales_order.to_location_id') 
    ->leftJoin('mis_category','mis_category.id', 'sales_order.mis_category_id') 
    // ->where('sales_order.year_id',$year_data->id)
    ->whereIn('sales_order.year_id',$yearIds)
    ->where('sales_order.current_location_id',$locationCode)
    ->whereNotIn('sales_order_details.so_details_id',$sod_detail)
    ->whereIN('sales_order.id',$pnding_so_data)
    ->groupBy('sales_order.id');

  


   return DataTables::of($so_data)

   ->editColumn('so_date', function($so_data){           
       if ($so_data->so_date != null) {
           $formatedDate1 = Date::createFromFormat('Y-m-d', $so_data->so_date)->format('d/m/Y'); 
           
           return $formatedDate1;

       }else{
           return '';
       }
   })
 
   ->editColumn('net_amount', function($so_data){
       return $so_data->net_amount > 0 ? number_format((float)$so_data->net_amount, 2, '.','') : number_format((float) 0, 2, '.','');

   })
   ->editColumn('name', function($so_data){ 
    return $so_data->so_from_value_fix == "location" ? $so_data->location_name : $so_data->customer_name;
    })
    ->filterColumn('name', function($query, $keyword) {
        $query->where(function($q) use ($keyword) {
            $q->where('location_name', 'like', "%$keyword%")
            ->orWhere('customer_name', 'like', "%$keyword%");
        });
    }) 

   ->rawColumns(['so_date','net_amount','name'])
   ->make(true);

}

}