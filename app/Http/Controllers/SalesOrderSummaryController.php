<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Date;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;

class SalesOrderSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-sales_order_summary');
    }

    public function index(SalesOrder $SalesOrder,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();


        $sales_order = SalesOrder::select(['sales_order.id','sales_order.so_sequence','sales_order.so_from_value_fix','sales_order.so_number','sales_order.customer_name','locations.location_name','sales_order.so_type_value_fix', 'sales_order.so_date','sales_order_details.so_qty', 'sales_order_details.rate_per_unit','sales_order_details.so_amount','items.item_name','items.item_code','item_groups.item_group_name', 'customer_groups.customer_group_name', 'sales_order.customer_reg_no', 'sales_order.customer_village', 'sales_order.customer_taluka', 'districts.district_name', 'countries.country_name','states.state_name', 'units.unit_name','dealers.dealer_name','villages.village_name','talukas.taluka_name','mis_category.mis_category','items.item_group_id','sales_order_details.remarks',

        DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),   

        ])

        ->leftJoin('customer_groups','customer_groups.id','=','sales_order.customer_group_id')
        ->leftJoin('sales_order_details','sales_order_details.so_id','=','sales_order.id')
        ->leftJoin('locations','locations.id','=','sales_order.to_location_id')
        ->leftJoin('items','items.id','=','sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('districts','districts.id','=','sales_order.customer_district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('talukas','talukas.id','=','sales_order.customer_taluka')
        ->leftJoin('villages','villages.id','=','sales_order.customer_village')      
        ->leftJoin('dealers','dealers.id','=','sales_order.dealer_id')
        ->leftJoin('mis_category','mis_category.id','=','sales_order.mis_category_id')
        ->where('current_location_id','=',$location->id);


        // filter for search data

        if($request->from_date != "" && $request->to_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');   
           

            $sales_order->whereDate('sales_order.so_date','>=',$from);

            $sales_order->whereDate('sales_order.so_date','<=',$to);

        }else if($request->from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $sales_order->where('sales_order.so_date','>=',$from);

        }else if($request->to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');

            $sales_order->where('sales_order.so_date','<=',$to);

        }

        // if($request->customer_name !=''){
        //     $sales_order->where('sales_order.customer_name','like', "%{$request->customer_name}%");
        // }

        // if($request->location_id !=''){
        //     $sales_order->where('sales_order.to_location_id','=',$request->location_id);
        // }

        // if($request->so_type_id !=''){
        //     $sales_order->where('sales_order.so_type_value_fix','=',$request->so_type_id);
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

        ->editColumn('so_qty', function($sales_order){

            return $sales_order->so_qty > 0 || $sales_order->so_qty != ""  ? number_format((float)$sales_order->so_qty, 3, '.','') : '';
        })

        ->editColumn('rate_per_unit', function($sales_order){

            return $sales_order->rate_per_unit > 0 ? number_format((float)$sales_order->rate_per_unit, 2, '.','') : '';
        })
            
        ->editColumn('so_amount', function($sales_order){

            return $sales_order->so_amount > 0 ? number_format((float)$sales_order->so_amount, 2, '.','') : '';
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
                }elseif($sales_order->so_from_value_fix == 'cash_carry'){
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
                if (stripos('Subsidy', $keyword) !== false) {
                    $query->orWhere('so_from_value_fix', 'customer');
                } elseif (stripos('cash & carry', $keyword) !== false) {
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
        
        ->rawColumns(['city_name','options','so_date','so_our_date','type','so_from_value_fix','so_type_value_fix','name','dealer_name'])
        ->make(true);
    }


}