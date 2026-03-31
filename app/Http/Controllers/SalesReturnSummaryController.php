<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
use Str;
use App\Models\Location;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetails;

class SalesReturnSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-sales_return_summary');
    }

    public function index(SalesReturn $SalesReturn,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();


        $sales_return = SalesReturn::select(['sales_return.sr_id','sales_return.sr_sequence','sales_return.sr_number','sales_return.customer_name','dispatch_plan.dp_number','dispatch_plan.dp_date',
        'sales_return.sr_date','sales_return.transporter_id','transporters.transporter_name',
        'sales_return.vehicle_no', 'sales_return.lr_no_date','sales_return.sp_note','items.item_name','item_details.secondary_item_name','sales_return_details.sr_qty',  DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),  
            'sales_return_details.remark',    
        ])

        ->leftJoin('sales_return_details','sales_return_details.sr_id','=','sales_return.sr_id')
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','sales_return.dp_no_id')  
        ->leftJoin('items','items.id','=','sales_return_details.item_id') 
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')    
        ->leftJoin('item_details','item_details.item_details_id','=','sales_return_details.item_details_id')   
        ->leftJoin('sales_order','sales_order.customer_name','=','sales_return.customer_name')
        ->leftJoin('transporters','transporters.id','=','sales_return.transporter_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'sales_return.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'sales_return.last_by_user_id')
        ->where('sales_return.current_location_id','=',$location->id)
        ->where('sales_return.year_id', '=', $year_data->id);
        // ->groupBy('sales_return.sr_number');
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


            $sales_return->whereDate('sales_return.sr_date','>=',$from);

            $sales_return->whereDate('sales_return.sr_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $sales_return->where('sales_return.sr_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $sales_return->where('sales_return.sr_date','<=',$to);

        }  
        // dd($sales_order);

        return DataTables::of($sales_return)

        ->editColumn('transporter_name', function($sales_return){ 
            if($sales_return->transporter_name != ''){
                $transporter_name = ucfirst($sales_return->transporter_name);
                return $transporter_name;
            }else{
                return '';
            }
        })
        ->editColumn('vehicle_no', function($sales_return){ 
            if($sales_return->vehicle_no != ''){
                $vehicle_no = ucfirst($sales_return->vehicle_no);
                return $vehicle_no;
            }else{
                return '';
            }
        })
        ->editColumn('sr_from_value_fix', function($sales_return){
            if($sales_return->sr_from_value_fix != ''){
                if($sales_return->sr_from_value_fix == 'customer'){
                    $sr_from_value_fix = 'Subsidy';
                }elseif($sales_return->sr_from_value_fix == 'cash_carry'){
                    $sr_from_value_fix = 'Cash & Carry';
                }else{
                    $sr_from_value_fix = ucfirst($sales_return->sr_from_value_fix);
                }
                return $sr_from_value_fix;
            }else{
                return '';
            }
        })

        ->filterColumn('sr_from_value_fix', function($query, $keyword) {
            $query->where(function($query) use ($keyword) {
                if (stripos('Subsidy', $keyword) !== false) {
                    $query->orWhere('sr_from_value_fix', 'customer');
                } elseif (stripos('cash & carry', $keyword) !== false) {
                    $query->orWhere('sr_from_value_fix', 'cash_carry');
                } else {
                    $query->orWhere('sr_from_value_fix', 'like', "%{$keyword}%");
                }
            });
        })
        ->editColumn('sr_date', function($sales_return){
            if ($sales_return->sr_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $sales_return->sr_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('sales_return.sr_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_return.sr_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('dp_date', function($sales_return){
            if ($sales_return->dp_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $sales_return->dp_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('dispatch_plan.dp_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dispatch_plan.dp_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('sr_qty', function($sales_return){

            return $sales_return->sr_qty > 0 ? number_format((float)$sales_return->sr_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
        ->make(true);
    }

}
