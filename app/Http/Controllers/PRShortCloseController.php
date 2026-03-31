<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PRShortClose;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionDetails;
use App\Models\PurchaseOrderDetails;
use Carbon\Carbon;
use Date;
use App\Models\Admin;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PRShortCloseController extends Controller
{
        public function manage()
        {
            return view('manage.manage-pr_short_close');
        }


        public function index(PRShortClose $PrShort,Request $request,DataTables $dataTables)
        {
            $year_data = getCurrentYearData();
            $location = getCurrentLocation();

            $pr_short_data = PurchaseRequisition::select(['purchase_requisition.pr_number', 'purchase_requisition.pr_id as pr_id','purchase_requisition.pr_date', 'purchase_requisition_short_close.pr_sc_date','purchase_requisition.pr_form_value_fix','suppliers.supplier_name','locations.location_name','items.item_name','items.item_code','item_groups.item_group_name', 'units.unit_name','purchase_requisition_details.req_qty','purchase_requisition_short_close.reason','purchase_requisition_short_close.pr_sc_qty','purchase_requisition_short_close.last_by_user_id','purchase_requisition_short_close.last_on','purchase_requisition_short_close.created_by_user_id','purchase_requisition_short_close.created_on','purchase_requisition_short_close.year_id', 'purchase_requisition_short_close.prsc_id','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
    
            ->leftJoin('purchase_requisition_details','purchase_requisition_details.pr_id', 'purchase_requisition.pr_id')
    
            ->leftJoin('purchase_requisition_short_close','purchase_requisition_short_close.pr_details_id', 'purchase_requisition_details.pr_details_id')
    
            ->leftJoin('suppliers','suppliers.id', 'purchase_requisition_details.supplier_id')

            ->leftJoin('items','items.id', 'purchase_requisition_details.item_id')

            ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')

            ->leftJoin('units','units.id', 'items.unit_id')
            ->leftJoin('admin AS created_user', 'created_user.id', '=', 'purchase_requisition_short_close.created_by_user_id')
            ->leftJoin('admin AS last_user', 'last_user.id', '=', 'purchase_requisition_short_close.last_by_user_id')
            ->leftJoin('locations','locations.id','=','purchase_requisition.to_location_id')
            ->where('purchase_requisition_short_close.current_location_id','=',$location->id)

            ->where('purchase_requisition_short_close.year_id','=',$year_data->id);
        
    
            if($request->trans_from_date != "" && $request->trans_to_date != ""){
                
                    $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                    $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                    $pr_short_data->whereDate('purchase_requisition_short_close.pr_sc_date','>=',$from);

                    $pr_short_data->whereDate('purchase_requisition_short_close.pr_sc_date','<=',$to);

            }else if($request->trans_from_date != ""){

                    $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                    $pr_short_data->where('purchase_requisition_short_close.pr_sc_date','>=',$from);

            }else if($request->trans_to_date != ""){

                    $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                    $pr_short_data->where('purchase_requisition_short_close.pr_sc_date','<=',$to);

            }  
    
            return DataTables::of($pr_short_data)
            ->editColumn('created_by_user_id', function($pr_short_data){
                if($pr_short_data->created_by_user_id != null){
                    $created_by_user_id = Admin::where('id','=',$pr_short_data->created_by_user_id)->first('user_name');
                    return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
                }else{
                    return '';
                }
            })
            ->filterColumn('created_by_user_id', function ($query, $keyword) {
                $query->where('created_user.user_name', 'like', "%{$keyword}%");
            })
            ->editColumn('last_by_user_id', function($pr_short_data){
                if($pr_short_data->last_by_user_id != null){
                    $last_by_user_id = Admin::where('id','=',$pr_short_data->last_by_user_id)->first('user_name');
                    return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
                }else{
                    return '';
                }
    
            })
            ->filterColumn('last_by_user_id', function ($query, $keyword) {
                $query->where('last_user.user_name', 'like', "%{$keyword}%");
            })
            ->editColumn('created_on', function($pr_short_data){
                if ($pr_short_data->created_on != null) {
                    $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $pr_short_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
                }else{
                    return '';
                }
            })
            ->filterColumn('purchase_requisition_short_close.created_on', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(purchase_requisition_short_close.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->editColumn('last_on', function($pr_short_data){
                if ($pr_short_data->last_on != null) {
                    $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $pr_short_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
                }else{
                    return '';
                }
            })
            ->filterColumn('purchase_requisition_short_close.last_on', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(purchase_requisition_short_close.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->editColumn('req_qty', function($pr_short_data){
    
                return $pr_short_data->req_qty > 0 ? number_format((float)$pr_short_data->req_qty, 3, '.','') : number_format((float) 0, 3,'.','');
            })
            ->editColumn('pr_sc_qty', function($pr_short_data){
    
                return $pr_short_data->pr_sc_qty > 0 ? number_format((float)$pr_short_data->pr_sc_qty, 3, '.') : number_format((float) 0, 3,'.','');
            })
            ->editColumn('pr_date', function($pr_short_data){
    
                if ($pr_short_data->pr_date != null) {
                    $date = Date::createFromFormat('Y-m-d', $pr_short_data->pr_date)->format(DATE_FORMAT);
                    
                    return $date;
                }else{
                    return '';
                }
            })
            ->editColumn('pr_sc_date', function($pr_short_data){
                if ($pr_short_data->pr_sc_date != null) {
                    $date = Date::createFromFormat('Y-m-d', $pr_short_data->pr_sc_date)->format(DATE_FORMAT);
                    
                    return $date;
                }else{
                    return '';
                }
            })
            ->filterColumn('purchase_requisition_short_close.pr_sc_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(purchase_requisition_short_close.pr_sc_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })

            ->filterColumn('purchase_requisition.pr_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(purchase_requisition.pr_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->editColumn('item_name', function($pr_short_data){ 
                if($pr_short_data->item_name != ''){
                    $item_name = ucfirst($pr_short_data->item_name);
                    return $item_name;
                }else{
                    return '';
                }
            })

            ->editColumn('pr_form_value_fix', function($pr_short_data){
                if ($pr_short_data->pr_form_value_fix != null) {
                    if($pr_short_data->pr_form_value_fix == 'from_location'){
                        $pr_form_value_fix = 'From Location';
                    }else{
                        $pr_form_value_fix = 'Manual';
                    }
                    return $pr_form_value_fix;
                }else{
                    return 'Manual';
                }
            })
            // ->filterColumn('purchase_requisition.pr_form_value_fix', function($query, $keyword) {
            //         if (stripos('from location', $keyword) !== false) {
            //             $query->where('pr_form_value_fix', 'from_location');
            //         }
            //         elseif (stripos('manual', $keyword) !== false) {
            //             $query->where(function($q){
            //                 $q->whereNull('pr_form_value_fix')
            //                 ->orWhere('pr_form_value_fix', 'manual');
            //             });
            //         }
            // })
            ->filterColumn('purchase_requisition.pr_form_value_fix', function($query, $keyword) { 
                $keyword = strtolower(trim($keyword));
                if (stripos('from location', $keyword) !== false) {
                    $query->where('pr_form_value_fix', 'from_location');
                } elseif (stripos('manual', $keyword) !== false) {
                    $query->where(function($q){
                        $q->whereNull('pr_form_value_fix')
                        ->orWhere('pr_form_value_fix', 'manual');
                    });
                } else {
                    // Return no results for any other keyword
                    $query->whereRaw('1 = 0');
                }
            })
            ->addColumn('options',function($purchase_requisition){
                $action = "<div>";
              
                if(hasAccess("po_short_close","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
                $action .= "</div>";
                return $action;
            })
            ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'date','pr_sc_qty','options'])
            ->make(true);
    
    
    
        }
    
        public function create()
        {
            return view('add.add-pr_short_close');
        }

        public function getPRData ()
        {

            $yearIds = getCompanyYearIdsToTill();
            $locationCode = getCurrentLocation()->id;
        
                $pr_data = PurchaseRequisitionDetails::select([
                    'purchase_requisition.pr_number', 
                    'purchase_requisition.pr_id as pr_id', 
                    'purchase_requisition.pr_date',
                    'purchase_requisition.pr_form_value_fix',
                    'locations.location_name',
                    'purchase_requisition_details.req_qty',
                    'suppliers.supplier_name',
                    'items.item_name',
                    'items.item_code', 
                    'item_groups.item_group_name', 
                    'units.unit_name', 
                    'purchase_requisition_details.pr_details_id as PRId',

                    DB::raw("(
                        (SELECT IFNULL(SUM(prd.req_qty), 0) 
                         FROM purchase_requisition_details AS prd 
                         WHERE prd.pr_details_id = purchase_requisition_details.pr_details_id)
                        -
                        (SELECT IFNULL(SUM(psid.pr_sc_qty), 0) 
                         FROM purchase_requisition_short_close AS psid  
                         WHERE psid.pr_details_id = purchase_requisition_details.pr_details_id)

                        -
                        (SELECT IFNULL(SUM(pod.po_qty), 0) 
                        FROM purchase_order_details AS pod  
                        WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id)
                    ) AS pend_req_qty")
                    
                    
                    ])
                    ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')

                    ->leftJoin('purchase_requisition_short_close','purchase_requisition_short_close.pr_details_id', 'purchase_requisition_details.pr_details_id')             
            
                    ->leftJoin('suppliers','suppliers.id', 'purchase_requisition_details.supplier_id')
            
                    ->leftJoin('items','items.id', 'purchase_requisition_details.item_id')

                    ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
            
                    ->leftJoin('units','units.id', 'items.unit_id')

                    ->leftJoin('locations','locations.id','=','purchase_requisition.to_location_id')
            
                    ->whereIn('purchase_requisition.year_id',$yearIds)
            
                    ->where('purchase_requisition.current_location_id',$locationCode)
            
                    ->having('pend_req_qty','>',0)

                    ->groupBy('purchase_requisition_details.pr_details_id')
            
                    ->get();
        
            
                    if($pr_data != null){
                        $pr_data = $pr_data->filter(function ($pr_data) {
                            if($pr_data->pr_date != null){
                                $pr_data->pr_date = Date::createFromFormat('Y-m-d', $pr_data->pr_date)->format('d/m/Y');
                            }
                            if ($pr_data->pr_form_value_fix != null) {
                                if($pr_data->pr_form_value_fix == 'from_location'){
                                    $pr_data->pr_form_value_fix = 'From Location';
                                }else{
                                    $pr_data->pr_form_value_fix = 'Manual';
                                }
                            }else{
                                $pr_data->pr_form_value_fix = 'Manual';
                            }
                            return $pr_data;

                        })->values();
                    }

                    if ($pr_data != null) {
                        return response()->json([
                            'response_code' => '1',
                            'pr_data' => $pr_data
                        ]);
                    } else {
                        return response()->json([
                            'response_code' => '0',
                            'pr_data' => []
                        ]);
                    }
        }


        public function store(Request $request){

            $validated = $request->validate([
                'pr_short_date' => 'required',
            ],
            [
               'pr_short_date.required' => 'Please Enter PR Short Close Date',
            ]);
    
    
            $year_data = getCurrentYearData();
            $locationID = getCurrentLocation()->id;
            DB::beginTransaction();
    
            try{
    
                $request->pr_short_details = json_decode($request->pr_short_details,true);
    
                if(isset($request->pr_short_details) && !empty($request->pr_short_details)){
                    foreach($request->pr_short_details as $ctKey => $ctVal){

                        if(isset($ctVal['pr_detail_id'])){

                            $prQtySum = PurchaseRequisitionDetails::where('pr_details_id',$ctVal['pr_detail_id'])->sum('req_qty');

                            $usePOQtySum = PRShortClose::where('pr_details_id',$ctVal['pr_detail_id'])->sum('pr_sc_qty');

                            $poQty = isset($ctVal['so_pr_qty']) && $ctVal['so_pr_qty'] > 0 ? $ctVal['so_pr_qty'] : 0;
                            $poQtySum = $usePOQtySum + $poQty;                          

                            if(number_format($prQtySum, 3) < number_format($poQtySum, 3)){
                                DB::rollBack();
                                return response()->json([
                                    'response_code' => '0',
                                    'response_message' => 'PR Qty. Is Used',                               
                                ]);
                            }

                        }
                        
                        
                        if($ctVal != null){
                               $ps_short_data =  PRShortClose::create([

                                    'current_location_id'=>$locationID,
                                    'pr_details_id'=> (isset($ctVal['pr_detail_id']) &&  $ctVal['pr_detail_id'] != "") ? $ctVal['pr_detail_id'] : null,

                                    'pr_sc_date'=> Date::createFromFormat('d/m/Y', $request->pr_short_date)->format('Y-m-d'),

                                    'pr_sc_qty'=>(isset($ctVal['so_pr_qty']) && $ctVal['so_pr_qty'] > 0) ? $ctVal['so_pr_qty'] : 0,

                                    'reason'=>(isset($ctVal['pr_reason']) &&  $ctVal['pr_reason'] != "") ? $ctVal['pr_reason'] : null,

                                    'year_id' => $year_data->id,

                                    'company_id' => Auth::user()->company_id,

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
                getActivityLogs("Purchase Requisition Short Close", "add", $e->getMessage(),$e->getLine());
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Error Occured Record Not Inserted',
                    'original_error' => $e->getMessage()
                ]);
            }
    
        }

        public function destroy(Request $request)
        {
            DB::beginTransaction();
            
            try{

                $pr_details_data = PRShortClose::select('pr_details_id')             
                ->where('prsc_id',$request->id)->get();

                $po_data = PurchaseOrderDetails::select('pr_details_id')               
                ->whereIn('pr_details_id',$pr_details_data)->get();

                if($po_data->isNotEmpty()){ 
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Purchase Requisition Short Close Is Used In PO.",
                    ]);
                }

                
                PRShortClose::where('prsc_id','=',$request->id)->delete();

                DB::commit();
                
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Deleted Successfully.',
                ]);
            }catch(\Exception $e){  
                
                DB::rollBack();
                getActivityLogs("Purchase Requisition Short Close", "delete", $e->getMessage(),$e->getLine());
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