<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\SalesOrder;
use App\Models\Admin;
use Date;

class PrintSalesOrderReportController extends Controller
{
     public function manage()
    {
        return view('manage.print_sales_order_report');
    }

     public function index(SalesOrder $SalesOrder,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();


        $sales_order = SalesOrder::select(['sales_order.id','sales_order.so_sequence','sales_order.so_from_value_fix','sales_order.so_number','sales_order.customer_name','locations.location_name',
        'sales_order.so_type_value_fix',
        'sales_order.so_date','sales_order.created_on','sales_order.created_by_user_id','sales_order.last_by_user_id','sales_order.last_on',
        // DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),
        'sales_order_details.so_qty', 'sales_order_details.rate_per_unit','sales_order_details.so_amount','items.item_name','items.item_code','item_groups.item_group_name', 'customer_groups.customer_group_name', 'sales_order.customer_reg_no', 'sales_order.customer_village', 'sales_order.customer_taluka', 'districts.district_name', 'countries.country_name','states.state_name', 'units.unit_name','dealers.dealer_name','villages.village_name','talukas.taluka_name','mis_category.mis_category',
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
        ->where('current_location_id','=',$location->id)
        ->where('sales_order.year_id', '=', $year_data->id)
        ->groupBy('sales_order.so_number');

        // dd($sales_order);
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

        return DataTables::of($sales_order)
        ->editColumn('id', function($sales_order){ 
            if($sales_order->id != ''){
                $id = base64_encode($sales_order->id);
                return $id;
            }else{
                return '';
            }
        })
        ->editColumn('created_by_user_id', function($sales_order){
            if($sales_order->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$sales_order->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->editColumn('last_by_user_id', function($sales_order){
            if($sales_order->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$sales_order->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })

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
            
        ->editColumn('created_on', function($sales_order){
            if ($sales_order->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $sales_order->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->editColumn('last_on', function($sales_order){
            if ($sales_order->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $sales_order->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        // ->addColumn('options',function($sales_order){
        //     $action = "<div>";
        //     if(hasAccess("sales_order","print")){
        //         $action .="<a id='print_a' target='_blank' href='".route('print-sales_order',['id' => base64_encode($sales_order->id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";

        //         $so_fiting_item = SalesOrderDetail::select(['sales_order_details.so_details_id','items.item_name',])
        //         ->leftJoin('items','items.id','=','sales_order_details.item_id')
        //         ->where('sales_order_details.fitting_item','yes')
        //         ->where('so_id',$sales_order->id)
        //         ->orderBy('items.item_name','asc')
        //         ->get();
            
        //         if($so_fiting_item->count() > 0){
        //             $action .="<a id='print_a' target='_blank' href='".route('print-sales_order_fitting',['id' => base64_encode($sales_order->id)]) ."' data-placement='top' data-original-title='SO Fitini item' title='Print'><i class='iconfa-print action-icon'></i></a>";
        //         }else{
        //             // session()->flash('message','No Data Available For Print!');
        //             // $action .="<a id='print_a'  href='".route('print-sales_order_fitting',['id' => base64_encode($sales_order->id)]) ."' data-placement='top' data-original-title='SO Fitini item' title='Print'><i class='iconfa-print action-icon'></i></a>";

        //         }
        //     }
        //     if(hasAccess("sales_order","edit")){
        //     $action .="<a id='edit_a' href='".route('edit-sales_order',['id' => base64_encode($sales_order->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
        //     }
        //     if(hasAccess("sales_order","delete")){
        //     $action .= "<i id='del_a'  href='".route('delete-sales_order',['id' => base64_encode($sales_order->id)]) ."'  data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
        //     }
        //     $action .= "</div>";
        //     return $action;
        // })   
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

        // ->filterColumn('sales_order.so_from_value_fix', function($query, $keyword) {
        //     $query->where(function($query) use ($keyword) {
        //         if (stripos('Subsidy', $keyword) !== false) {
        //             $query->orWhere('so_from_value_fix', 'customer');
        //         } elseif (stripos('cash & carry', $keyword) !== false) {
        //             $query->orWhere('so_from_value_fix', 'cash_carry');
        //         } else {
        //             $query->orWhere('so_from_value_fix', 'like', "%{$keyword}%");
        //         }
        //     });
        // })
         ->filterColumn('so_from_value_fix', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('sales_order.so_from_value_fix', 'like', "%{$keyword}%")
                ->orWhereRaw("CASE 
                    WHEN sales_order.so_from_value_fix = 'customer' THEN 'Subsidy'
                    WHEN sales_order.so_from_value_fix = 'cash_carry' THEN 'Cash & Carry'
                    ELSE sales_order.so_from_value_fix
                    END LIKE ?", ["%{$keyword}%"]);
            });
        })
        // ->editColumn('name', function($sales_order){
        //     if($sales_order->name != ''){
        //         $name = ucfirst($sales_order->name);
        //         return $name;
        //     }else{
        //         return '';
        //     }
        // })
        ->addColumn('name', function($sales_order){           
            return $sales_order->so_from_value_fix == "location" ? $sales_order->location_name : $sales_order->customer_name;
        })
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
        
         ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','city_name','options','so_date','so_our_date','type','so_from_value_fix','so_type_value_fix','name','dealer_name'])
        ->make(true);
    }
}