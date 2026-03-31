<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\SalesOrderDetailsDetails;
use App\Models\TransactionSOShortClose;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;

class PendingSOforDispatchController extends Controller
{
    public function manage()
    {
        return view('manage.manage-pending_so_for_dispatch');
    }

    public function index(SalesOrder $sales_order,Request $request,DataTables $dataTables)
    {

         $yearIds = getCompanyYearIdsToTill();
         $locationCode = getCurrentLocation()->id;

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


        $sales_order = SalesOrderDetail::select(['sales_order.so_number','sales_order.id','sales_order.so_date','sales_order.so_sequence', 'sales_order.so_from_value_fix','sales_order.so_type_value_fix','locations.location_name','sales_order.customer_reg_no','sales_order.customer_name','villages.village_name','talukas.taluka_name','mis_category.mis_category','items.item_group_id','sales_order_details.remarks',
        'sales_order_details.so_details_id','sales_order_details.so_id','countries.country_name','states.state_name', 'items.item_name','items.item_code','item_groups.item_group_name', 'customer_groups.customer_group_name','units.unit_name', 'villages.village_name as customer_village','districts.district_name','dealers.dealer_name', 'sales_order_details.fitting_item','items.id as item_id',
        
        DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),   

        DB::raw('((sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id), 0))) as pend_so_qty')

        
        ])
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
        ->whereIN('sales_order_details.so_details_id',array_merge($sod_details_id,$so_details_id));
        // ->groupBy('sales_order.id')

        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $sales_order->whereDate('sales_order.so_date','>=',$from);

                $sales_order->whereDate('sales_order.so_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $sales_order->where('sales_order.so_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $sales_order->where('sales_order.so_date','<=',$to);

        }
        


        // filter for search data

        // if($request->customer_name !=''){
        //     $sales_order->where('sales_order.customer_name','like', "%{$request->customer_name}%");
        // }

        // if($request->location_id !=''){
        //     $sales_order->where('sales_order.to_location_id','=',$request->location_id);
        // }
       
        // if($request->cust_group_id !=''){
        //     $sales_order->where('sales_order.customer_group_id','=',$request->cust_group_id);
        // }

        // if($request->dealer_id !=''){
        //     $sales_order->where('sales_order.dealer_id','=',$request->dealer_id);
        // }

        // if($request->mis_category_id !=''){
        //     $sales_order->where('sales_order.mis_category_id','=',$request->mis_category_id);
        // }

        // if($request->item_id !=''){
        //     $sales_order->where('sales_order_details.item_id', '=', $request->item_id);
        // }

        // if($request->group_id !=''){
        //     $sales_order->where('item_groups.id', '=', $request->group_id);
        // }

        // if($request->code_id !=''){
        //     $sales_order->where('items.id', '=', $request->code_id);
        // }

        // if($request->village_name !=''){
        //     $sales_order->where('villages.village_name','like', "%{$request->village_name}%");
        // }

        // if($request->taluka_name !=''){
        //     $sales_order->where('talukas.taluka_name','like', "%{$request->taluka_name}%");
        // }

        // if($request->district_name !=''){
        //     $sales_order->where('districts.district_name','like', "%{$request->district_name}%");
        // }

        // if($request->state_name !=''){
        //     $sales_order->where('states.state_name','like', "%{$request->state_name}%");
        // }

        // if($request->so_number !=''){
        //     $sales_order->where('sales_order.so_number','like', "%{$request->so_number}%");
        // }

        // if($request->so_type_id !=''){
        //     $sales_order->where('sales_order.so_type_value_fix','=',$request->so_type_id);
        // }

       

        // end search terms


        return DataTables::of($sales_order)

        ->editColumn('item_name', function($sales_order){ 
            if($sales_order->item_name != ''){
                $item_name = ucfirst($sales_order->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

        ->editColumn('pend_so_qty', function($sales_order){

            return $sales_order->pend_so_qty > 0 || $sales_order->pend_so_qty != ""  ? number_format((float)$sales_order->pend_so_qty, 3, '.','') : '';
        })
        ->filterColumn('pend_so_qty', function($query, $keyword) {
            $query->whereRaw("(
                (sales_order_details.so_qty - IFNULL(
                    (SELECT SUM(dispatch_plan_details.plan_qty) 
                    FROM dispatch_plan_details 
                    WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id), 0)
                ) LIKE ?)", ["%{$keyword}%"]);
        })
      
        ->editColumn('type', function($sales_order){
            if($sales_order->so_from_id_fix != ''){
                $type = ucfirst($sales_order->so_from_id_fix);
                return $type;
            }else{
                return '';
            }
        })
        ->editColumn('so_type_value_fix', function($sales_order){
            if($sales_order->so_type_value_fix != ''){
                $so_type_value_fix = ucfirst($sales_order->so_type_value_fix);
                return $so_type_value_fix;
            }else{
                return '';
            }
        })
        // ->editColumn('so_from_value_fix', function($sales_order){
        //     if($sales_order->so_from_value_fix != ''){
        //         $so_from_value_fix = ucfirst($sales_order->so_from_value_fix);
        //         return $so_from_value_fix;
        //     }else{
        //         return '';
        //     }
        // })
        ->editColumn('so_from_value_fix', function($sales_order){
            if($sales_order->so_from_value_fix != ''){
                if($sales_order->so_from_value_fix == 'customer'){
                    $so_from_value_fix = 'Subsidy';
                }else if($sales_order->so_from_value_fix == 'cash_carry'){
                    $so_from_value_fix = 'Cash & Carry';
                }else{
                    $so_from_value_fix = ucfirst($sales_order->so_from_value_fix);
                }
                return $so_from_value_fix;
            }else{
                return '';
            }
        })

        ->filterColumn('sales_order.so_from_value_fix', function($query, $keyword) {
            $query->where(function($query) use ($keyword) {
                if(stripos('Subsidy', $keyword) !== false) {
                    $query->orWhere('so_from_value_fix', 'customer');
                } else if(stripos('cash & carry', $keyword) !== false) {
                    $query->orWhere('so_from_value_fix', 'cash_carry');
                } else {
                    $query->orWhere('so_from_value_fix', 'like', "%{$keyword}%");
                }
            });
        })
      
        // ->addColumn('name', function($sales_order){           
        //     return $sales_order->so_from_value_fix == "location" ? $sales_order->location_name : $sales_order->customer_name;
        // })
        ->filterColumn('name', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('location_name', 'like', "%$keyword%")
                  ->orWhere('customer_name', 'like', "%$keyword%");
            });
        })
        ->editColumn('so_date', function($sales_order){
            if ($sales_order->so_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $sales_order->so_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('sales_order.so_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_order.so_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('dealer_name', function($sales_order){
            if($sales_order->dealer_name != ''){
                $name = ucfirst($sales_order->dealer_name);
                return $name;
            }else{
                return '';
            }
        })
        
        ->rawColumns(['so_date','type','so_from_value_fix','so_type_value_fix','name','dealer_name'])
        ->make(true);
    }
}