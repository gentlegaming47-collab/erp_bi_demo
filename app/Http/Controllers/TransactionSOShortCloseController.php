<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Date;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\TransactionSOShortClose;
use App\Models\Admin;
use App\Models\DispatchPlanDetails;

class TransactionSOShortCloseController extends Controller
{
    //
    public function manage()
    {
        return view('manage.manage-transaction_so_short_close');
    }

    public function create()
    {
        return view('add.add-transaction_so_short_close');
    }

     public function index(TransactionSOShortClose $SoShort,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();
        $so_short_data = TransactionSOShortClose::select([
            'transaction_so_short_close.tr_sosc_id',
            'transaction_so_short_close.tr_sc_date',
            'transaction_so_short_close.tr_sc_qty',
            'transaction_so_short_close.reason',
            'sales_order.so_number',
            'sales_order.so_date',
            'sales_order.customer_name',
            'sales_order.customer_reg_no',
            'items.item_name',
            'items.item_code',
            'item_groups.item_group_name',
            'units.unit_name',
            'sales_order_details.so_qty',
            'transaction_so_short_close.created_by_user_id',
            'transaction_so_short_close.created_on',
            'transaction_so_short_close.last_by_user_id',
            'transaction_so_short_close.last_on',
            'locations.location_name',
            'customer_groups.customer_group_name',
            'sales_order.customer_name',
            'sales_order.so_from_value_fix',
            'sales_order.so_type_value_fix',
            'created_user.user_name as created_by_name',
            'last_user.user_name as last_by_name'
        ])
        ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'transaction_so_short_close.so_details_id')
        ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
        ->leftJoin('locations','locations.id','=','sales_order.to_location_id')
        ->leftJoin('items','items.id', 'sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        ->leftJoin('units','units.id', 'items.unit_id')
        ->leftJoin('customer_groups','customer_groups.id','=','sales_order.customer_group_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'transaction_so_short_close.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'transaction_so_short_close.last_by_user_id')
        ->where('transaction_so_short_close.current_location_id','=',$location->id)
        ->where('transaction_so_short_close.year_id','=',$year_data->id);
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $so_short_data->whereDate('transaction_so_short_close.tr_sc_date','>=',$from);

                $so_short_data->whereDate('transaction_so_short_close.tr_sc_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $so_short_data->where('transaction_so_short_close.tr_sc_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $so_short_data->where('transaction_so_short_close.tr_sc_date','<=',$to);

        }  
        return DataTables::of($so_short_data)

        ->editColumn('type', function($so_short_data){
            if($so_short_data->so_from_id_fix != ''){
                $type = ucfirst($so_short_data->so_from_id_fix);
                return $type;
            }else{
                return '';
            }
        })
        ->editColumn('so_type_value_fix', function($so_short_data){
            if($so_short_data->so_type_value_fix != ''){
                $so_type_value_fix = ucfirst($so_short_data->so_type_value_fix);
                return $so_type_value_fix;
            }else{
                return '';
            }
        })
        ->editColumn('so_from_value_fix', function($so_short_data){
            if($so_short_data->so_from_value_fix != ''){
                if($so_short_data->so_from_value_fix == 'customer'){
                    $so_from_value_fix = 'Subsidy';
                }elseif($so_short_data->so_from_value_fix == 'cash_carry'){
                    $so_from_value_fix = 'Cash & Carry';
                }else{
                    $so_from_value_fix = ucfirst($so_short_data->so_from_value_fix);
                }
                return $so_from_value_fix;
            }else{
                return '';
            }
        })
        ->filterColumn('so_from_value_fix', function($query, $keyword) {
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
        ->addColumn('name', function($so_short_data){           
            return $so_short_data->so_from_value_fix == "location" ? $so_short_data->location_name : $so_short_data->customer_name;
        })
        ->filterColumn('name', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('location_name', 'like', "%$keyword%")
                  ->orWhere('customer_name', 'like', "%$keyword%");
            });
        })

        ->editColumn('created_by_user_id', function($so_short_data){
            if($so_short_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$so_short_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($so_short_data){
            if($so_short_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$so_short_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('created_on', function($so_short_data){
            if ($so_short_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $so_short_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('transaction_so_short_close.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(transaction_so_short_close.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($so_short_data){
            if ($so_short_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $so_short_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('transaction_so_short_close.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(transaction_so_short_close.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('so_qty', function($so_short_data){

            return $so_short_data->so_qty > 0 ? number_format((float)$so_short_data->so_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
        ->editColumn('tr_sc_qty', function($so_short_data){

            return $so_short_data->tr_sc_qty > 0 ? number_format((float)$so_short_data->tr_sc_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
        ->editColumn('tr_sc_date', function($so_short_data){
            if ($so_short_data->tr_sc_date != null) {
                $date = Date::createFromFormat('Y-m-d', $so_short_data->tr_sc_date)->format(DATE_FORMAT);
                
                return $date;
            }else{
                return '';
            }
        })
        ->editColumn('so_date', function($so_short_data){
            if ($so_short_data->so_date != null) {
                $so_date = Date::createFromFormat('Y-m-d', $so_short_data->so_date)->format(DATE_FORMAT);
                return $so_date;
            }else{
                return '';
            }
        })
        ->filterColumn('transaction_so_short_close.tr_sc_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(transaction_so_short_close.tr_sc_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('sales_order.so_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_order.so_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
      
        ->addColumn('options',function($purchase_order){
            $action = "<div>";          
            if(hasAccess("so_short_close","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'date','sc_qty', 'so_date', 'options'])
        ->make(true);
    }


    
    // public function getTransactionSOShortData()
    // {
    //     $yearIds = getCompanyYearIdsToTill();
    //     $locationCode = getCurrentLocation()->id;

    //     $dpd_sod_ids = DispatchPlanDetails::select('so_details_id')->pluck('so_details_id')->toArray();

    //     $so_data = SalesOrderDetail::select([
    //         'sales_order.so_number',
    //         'sales_order.so_date',
    //         'sales_order.customer_name',
    //         'items.item_name',
    //         'items.item_code',
    //         'item_groups.item_group_name',
    //         'units.unit_name',
    //         'sales_order_details.so_qty',
    //         'sales_order.id',
    //         'sales_order_details.so_details_id',
    //         'sales_order.so_from_id_fix',
    //          'locations.location_name',
    //         DB::raw("CASE 
    //             WHEN sales_order.so_from_id_fix = 1 THEN 'Subsidy'
    //             WHEN sales_order.so_from_id_fix = 2 THEN 'Cash & Carry'
    //             WHEN sales_order.so_from_id_fix = 3 THEN 'Location'
    //             ELSE sales_order.so_from_value_fix
    //         END as so_from_value_fix"),
    //         'sales_order.so_type_id_fix',
    //         DB::raw("CASE 
    //             WHEN sales_order.so_type_id_fix = 1 THEN 'General'
    //             WHEN sales_order.so_type_id_fix = 2 THEN 'Replacement'
    //             ELSE sales_order.so_type_value_fix
    //         END as so_type_value_fix"),
    //         'customer_groups.customer_group_name',
    //         'sales_order.customer_name',
    //         DB::raw("(SELECT sales_order_details.so_qty  -  (SELECT IFNULL(SUM(transaction_so_short_close.tr_sc_qty),0) FROM transaction_so_short_close  WHERE transaction_so_short_close.so_details_id = sales_order_details.so_details_id) -
    //         (SELECT IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) pend_so_qty"),
    //     ])
    //     ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
    //     ->leftJoin('locations','locations.id','=','sales_order.to_location_id')
    //     ->leftJoin('items','items.id', 'sales_order_details.item_id')
    //     ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')        
    //     ->leftJoin('units','units.id', 'items.unit_id')
    //     ->leftJoin('customer_groups','customer_groups.id','=','sales_order.customer_group_id')
    //     // ->where('sales_order.so_type_id_fix','=',2)
    //     ->where('sales_order.current_location_id','=',$locationCode)
    //     ->whereIn('sales_order.year_id',$yearIds)
    //     ->whereNotIn('sales_order_details.so_details_id',$dpd_sod_ids)

    //     ->having('pend_so_qty','>',0)
    //     ->get();

    //     if($so_data != null){
    //         $so_data = $so_data->filter(function ($so_data) {
    //             if($so_data->so_date != null){
    //                 $so_data->so_date = Date::createFromFormat('Y-m-d', $so_data->so_date)->format('d/m/Y');
    //             }
               
    //             return $so_data;
               
    //         })->values();
    //     }

    //     if ($so_data != null) {
    //         return response()->json([
    //             'response_code' => '1',
    //             'so_data' => $so_data
    //         ]);
    //     } else {
    //         return response()->json([
    //             'response_code' => '0',
    //             'so_data' => []
    //         ]);
    //     }


        
    // }

        
    public function getTransactionSOShortData()
    {
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation()->id;

        // $dpd_sod_ids = DispatchPlanDetails::select('so_details_id')->pluck('so_details_id')->toArray();

        $so_data = SalesOrderDetail::select([
            'sales_order.so_number',
            'sales_order.so_date',
            'sales_order.customer_name',
            'items.item_name',
            'items.item_code',
            'item_groups.item_group_name',
            'units.unit_name',
            'sales_order_details.so_qty',
            'sales_order.id',
            'sales_order_details.so_details_id',
            'sales_order.so_from_id_fix',
            'sales_order.so_from_value_fix',
            'sales_order.so_type_value_fix',
             'locations.location_name',       
          
            'customer_groups.customer_group_name',
            // DB::raw("(SELECT sales_order_details.so_qty  -  (SELECT IFNULL(SUM(transaction_so_short_close.tr_sc_qty),0) FROM transaction_so_short_close  WHERE transaction_so_short_close.so_details_id = sales_order_details.so_details_id) -
            // (SELECT IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) pend_so_qty"),

             DB::raw("(SELECT sales_order_details.so_qty  -  (SELECT IFNULL(SUM(transaction_so_short_close.tr_sc_qty),0) FROM transaction_so_short_close  WHERE transaction_so_short_close.so_details_id = sales_order_details.so_details_id) -
            (SELECT IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id) 
            -
            (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.so_details_id = sales_order_details.so_details_id)
            
            ) pend_so_qty"),
        ])
        ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
        ->leftJoin('locations','locations.id','=','sales_order.to_location_id')
        ->leftJoin('items','items.id', 'sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')        
        ->leftJoin('units','units.id', 'items.unit_id')
        ->leftJoin('customer_groups','customer_groups.id','=','sales_order.customer_group_id')
        // ->where('sales_order.so_type_id_fix','=',2)
        ->where('sales_order.current_location_id','=',$locationCode)
        ->whereIn('sales_order.year_id',$yearIds)
        // ->where('sales_order.id',1042)
        // ->whereNotIn('sales_order_details.so_details_id',$dpd_sod_ids)

        ->havingRaw('
        (sales_order_details.so_qty - 
            (SELECT IFNULL(SUM(transaction_so_short_close.tr_sc_qty),0) 
            FROM transaction_so_short_close WHERE transaction_so_short_close.so_details_id = sales_order_details.so_details_id) -
            (SELECT IFNULL(SUM(dispatch_plan_details.plan_qty),0) 
            FROM dispatch_plan_details WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id) -
            (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.so_details_id = sales_order_details.so_details_id)
        ) > 0
    ');
    // dd($so_data);

        return DataTables::of($so_data)
        ->addColumn('options', function($so_data) {
            static $idx = 0;
            $autofocus = $idx == 0 ? 'autofocus' : '';
            $action = '<input type="checkbox" name="so_detail_id[]" id="so_detail_ids_' . $so_data->so_details_id . '" value="' . $so_data->so_details_id . '" onchange="manageQtyfield(this)"' . $autofocus . '/>';
             $idx++;
            return $action;
        })

        ->addColumn('short_close_qty', function($so_data) {
            $pendQty = number_format((float)$so_data->pend_so_qty, 3, '.', '');
            $action = '<input type="text" name="tr_sc_qty[]" id="tr_sc_qty_'. $so_data->so_details_id . '"  max="' . $pendQty . '" onblur="formatPoints(this,3)" class="input-mini isNumberKey" value="' . $pendQty . '" readonly tabindex="-1"/>';
            return $action;
        })

        ->addColumn('reason', function($so_data) {          
            $action = '<textarea  name="reason[]" id="reason_' . $so_data->so_details_id . '" rows="4" disabled></textarea>';
            return $action;
        })

        ->editColumn('so_date', function($so_data){
            if ($so_data->so_date != null) {
                $date = Date::createFromFormat('Y-m-d', $so_data->so_date)->format(DATE_FORMAT);
                
                return $date;
            }else{
                return '';
            }
        })
         ->filterColumn('sales_order.so_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_order.so_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

      

        ->editColumn('so_type_value_fix', function($so_data){
            if($so_data->so_type_value_fix != ''){
                $so_type_value_fix = ucfirst($so_data->so_type_value_fix);
                return $so_type_value_fix;
            }else{
                return '';
            }
        })
        ->editColumn('so_from_value_fix', function($so_data){
            if($so_data->so_from_value_fix != ''){
                if($so_data->so_from_value_fix == 'customer'){
                    $so_from_value_fix = 'Subsidy';
                }elseif($so_data->so_from_value_fix == 'cash_carry'){
                    $so_from_value_fix = 'Cash & Carry';
                }else{
                    $so_from_value_fix = ucfirst($so_data->so_from_value_fix);
                }
                return $so_from_value_fix;
            }else{
                return '';
            }
        })
        ->filterColumn('so_from_value_fix', function($query, $keyword) {
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
        
        ->addColumn('name', function($sales_order){           
            return $sales_order->so_from_value_fix == "location" ? $sales_order->location_name : $sales_order->customer_name;
        })
        ->filterColumn('name', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('location_name', 'like', "%$keyword%")
                  ->orWhere('customer_name', 'like', "%$keyword%");
            });
        })

        ->editColumn('so_qty', function($so_data){

            return $so_data->so_qty > 0 ? number_format((float)$so_data->so_qty, 3, '.','') : number_format((float) 0, 3,'.','');
        })
        ->addColumn('pend_so_qty', function($so_data){
            return $so_data->pend_so_qty > 0 ? number_format((float)$so_data->pend_so_qty, 3, '.','') : number_format((float) 0, 3,'.','');
        })
        ->rawColumns(['options','reason','short_close_qty'])
        ->make(true);
        
    }




     public function store(Request $request){
        $validated = $request->validate([
            'tr_sc_date' => 'required',
        ],
        [
           'tr_sc_date.required' => 'Please Enter Short Close Date',
        ]);


        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;
        DB::beginTransaction();

        try{

            $request->tr_so_short_close_details = json_decode($request->tr_so_short_close_details,true);

            if(isset($request->tr_so_short_close_details) && !empty($request->tr_so_short_close_details)){
                foreach($request->tr_so_short_close_details as $ctKey => $ctVal){
                    if(isset($ctVal['so_detail_id'])){

                        $soQtySum = SalesOrderDetail::where('so_details_id',$ctVal['so_detail_id'])->sum('so_qty');

                        $useSOScQtySum = TransactionSOShortClose::where('so_details_id',$ctVal['so_detail_id'])->sum('tr_sc_qty');

                        $soQty = isset($ctVal['short_close_qty']) && $ctVal['short_close_qty'] > 0 ? $ctVal['short_close_qty'] : 0;
                        $soScQtySum = $useSOScQtySum + $soQty;                          

                        if(number_format($soQtySum, 3) < number_format($soScQtySum, 3)){
                            DB::rollBack();
                            return response()->json([
                                'response_code' => '0',
                                'response_message' => 'Short Close Qty. Is Used',                               
                            ]);
                        }

                    }
                    
                    if($ctVal != null){
                        $so_short_data =  TransactionSOShortClose::create([
                        'so_details_id'=> (isset($ctVal['so_detail_id']) &&  $ctVal['so_detail_id'] != "") ? $ctVal['so_detail_id'] : null,
                        'tr_sc_date'=> Date::createFromFormat('d/m/Y', $request->tr_sc_date)->format('Y-m-d'),
                        'tr_sc_qty'=>(isset($ctVal['short_close_qty']) && $ctVal['short_close_qty'] > 0) ? $ctVal['short_close_qty'] : 0,
                        'reason'=>(isset($ctVal['reason']) &&  $ctVal['reason'] != "") ? $ctVal['reason'] : '',
                        'current_location_id'=>$locationID,
                        'company_id' => Auth::user()->company_id,
                        'year_id' => $year_data->id,
                        'created_by_user_id' => Auth::user()->id,
                        'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                        ]);
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
            
        }catch(\Exception $e){
            DB::rollBack();
            getActivityLogs("Customer Replacement SO Short Close", "add", $e->getMessage(),$e->getLine());  

            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }


    
    public function destroy(Request $request)
    {
        try{
           
            TransactionSOShortClose::where('tr_sosc_id','=',$request->id)->delete();
            
            
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){      
            DB::rollBack();
            getActivityLogs("Customer Replacement SO Short Close", "delete", $e->getMessage(),$e->getLine());  

            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
            }else{
                $error_msg = "Record Not Deleted";
            }
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }






}