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
use App\Models\SOShortClose;
use App\Models\Admin;

class SOShortCloseController extends Controller
{
    public function manage()
    {
        return view('manage.manage-so_short_close');
    }

    public function create()
    {
        return view('add.add-so_short_close');
    }

    public function index(SOShortClose $SoShort,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();
        $so_short_data = SOShortClose::select(['so_short_close.sosc_id','so_short_close.sc_date','so_short_close.sc_qty','so_short_close.reason','sales_order.so_number','sales_order.so_date','sales_order.customer_name','sales_order.customer_reg_no', 'items.item_name','items.item_code','item_groups.item_group_name', 
        'units.unit_name', 'sales_order_details.so_qty','so_short_close.created_by_user_id','so_short_close.created_on','so_short_close.last_by_user_id','so_short_close.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'
        ])
        ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'so_short_close.so_details_id')
        ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
        ->leftJoin('items','items.id', 'sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        ->leftJoin('units','units.id', 'items.unit_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'so_short_close.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'so_short_close.last_by_user_id')
        ->where('so_short_close.current_location_id','=',$location->id)
        ->where('so_short_close.year_id','=',$year_data->id);
         if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $so_short_data->whereDate('so_short_close.sc_date','>=',$from);
            $so_short_data->whereDate('so_short_close.sc_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $so_short_data->where('so_short_close.sc_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');
            $so_short_data->where('so_short_close.sc_date','<=',$to);

        } 



        return DataTables::of($so_short_data)
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
        ->filterColumn('so_short_close.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(so_short_close.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($so_short_data){
            if ($so_short_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $so_short_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('so_short_close.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(so_short_close.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('so_qty', function($so_short_data){

            return $so_short_data->so_qty > 0 ? number_format((float)$so_short_data->so_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
        ->editColumn('sc_qty', function($so_short_data){

            return $so_short_data->sc_qty > 0 ? number_format((float)$so_short_data->sc_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
        ->editColumn('sc_date', function($so_short_data){
            if ($so_short_data->sc_date != null) {
                $date = Date::createFromFormat('Y-m-d', $so_short_data->sc_date)->format(DATE_FORMAT);
                
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
        ->filterColumn('so_short_close.sc_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(so_short_close.sc_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
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


    public function getSOData()
    {
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation()->id;
        $so_data = SalesOrderDetail::select(['sales_order.so_number','sales_order.so_date','sales_order.customer_name','sales_order.customer_reg_no', 'items.item_name','items.item_code','item_groups.item_group_name', 
        'units.unit_name', 'sales_order_details.so_qty','sales_order.id','sales_order_details.so_details_id',
        DB::raw("(SELECT sales_order_details.so_qty  -  (SELECT IFNULL(SUM(so_short_close.sc_qty),0)
        FROM so_short_close  WHERE so_short_close.so_details_id = sales_order_details.so_details_id) -
        (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.so_details_id = sales_order_details.so_details_id)) pend_so_map_qty"),     
         
         ])
        ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
        ->leftJoin('items','items.id', 'sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')        
        ->leftJoin('units','units.id', 'items.unit_id')
        ->where('sales_order.so_type_id_fix','=',2)
        ->where('sales_order.current_location_id','=',$locationCode)
        ->whereIn('sales_order.year_id',$yearIds)
        ->having('pend_so_map_qty','>',0)
        ->get();

        if($so_data != null){
            $so_data = $so_data->filter(function ($so_data) {
                if($so_data->so_date != null){
                    $so_data->so_date = Date::createFromFormat('Y-m-d', $so_data->so_date)->format('d/m/Y');
                }
                return $so_data;
            })->values();
        }

        if ($so_data != null) {
            return response()->json([
                'response_code' => '1',
                'so_data' => $so_data
            ]);
        } else {
            return response()->json([
                'response_code' => '0',
                'so_data' => []
            ]);
        }


        
    }

    public function store(Request $request){
        $validated = $request->validate([
            'so_short_date' => 'required',
        ],
        [
           'so_short_date.required' => 'Please Enter Short Close Date',
        ]);


        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;
        DB::beginTransaction();

        try{

            $request->so_short_details = json_decode($request->so_short_details,true);

            if(isset($request->so_short_details) && !empty($request->so_short_details)){
                foreach($request->so_short_details as $ctKey => $ctVal){
                    if(isset($ctVal['so_detail_id'])){

                        $soQtySum = round(SalesOrderDetail::where('so_details_id',$ctVal['so_detail_id'])->sum('so_qty'),3);

                        $useSOScQtySum = round(SOShortClose::where('so_details_id',$ctVal['so_detail_id'])->sum('sc_qty'),3);

                        $soQty = isset($ctVal['short_close_qty']) && $ctVal['short_close_qty'] > 0 ? $ctVal['short_close_qty'] : 0;
                        $soScQtySum = $useSOScQtySum + $soQty;    
                        

                        if($soQtySum < $soScQtySum){
                            DB::rollBack();
                            return response()->json([
                                'response_code' => '0',
                                'response_message' => 'Short Close Qty. Is Used',                               
                            ]);
                        }

                    }
                    
                    if($ctVal != null){
                        $so_short_data =  SOShortClose::create([
                        'so_details_id'=> (isset($ctVal['so_detail_id']) &&  $ctVal['so_detail_id'] != "") ? $ctVal['so_detail_id'] : null,
                        'sc_date'=> Date::createFromFormat('d/m/Y', $request->so_short_date)->format('Y-m-d'),
                        'sc_qty'=>(isset($ctVal['short_close_qty']) && $ctVal['short_close_qty'] > 0) ? $ctVal['short_close_qty'] : 0,
                        'reason'=>(isset($ctVal['so_reason']) &&  $ctVal['so_reason'] != "") ? $ctVal['so_reason'] : '',
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
           
            SOShortClose::where('sosc_id','=',$request->id)->delete();
            
            
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