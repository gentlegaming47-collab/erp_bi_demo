<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Supplier;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Date;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\GRNMaterialDetails;

class POApprovalController extends Controller
{
    public function manage()
    {
        return view('manage.manage-po_approval');
    }

    public function create() {
        return view('add.add-po_approval');
    }

    public function index(PurchaseOrder $purchase_order,Request $request,DataTables $dataTables)
    {

        // $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();
        
        $purchase_order = PurchaseOrder::select([
        'purchase_order.*','purchase_order_details.po_details_id','locations.location_name','to_location.location_name as to_location','suppliers.supplier_name'])
        //->leftJoin('purchase_order','purchase_order.po_id' ,'purchase_order_details.po_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_id' ,'purchase_order.po_id')
        ->leftJoin('locations','locations.id','purchase_order.current_location_id')
        ->leftJoin('locations as to_location','to_location.id','purchase_order.to_location_id')
        ->leftJoin('suppliers','suppliers.id','purchase_order.supplier_id')
     
        ->where('purchase_order.is_approved', 0)
        ->whereIn('purchase_order.year_id', $yearIds)
        ->groupBy('purchase_order.po_number')
        ->get();

        if($purchase_order != null){
            foreach($purchase_order as $val){
                if($val->po_date != null){
                    $val->po_date = Date::createFromFormat('Y-m-d', $val->po_date)->format('d/m/Y');
                }

                if($val->ref_date != null){
                    $val->ref_date = Date::createFromFormat('Y-m-d', $val->ref_date)->format('d/m/Y');
                }
            }
        }

        if($purchase_order != "")
        {
            return response()->json([               
                'getMaterial' => $purchase_order, 
                'response_code' => 1,
                'response_message' => "Get Material Record",
            ]);
        }else{
            return response()->json([               
                'response_code' => 0,
                'response_message' => "Material Not Found",
            ]);
        }
    }

    public function store(Request $request)
    {

        DB::beginTransaction();
        try{

            $request->approve_data = json_decode($request->approve_data,true);
            $request->approve_data_table = json_decode($request->approve_data_table,true);

            if(isset($request->approve_data) && !empty($request->approve_data)){
                foreach($request->approve_data as $ctKey => $ctVal){
                    
                    $store = PurchaseOrder::where('po_id', $ctVal['poId'])->update([
                        'is_approved' => 1,
                    ]);  

                    // if(strtolower(Auth::user()->user_type) == "director" || Auth::user()->id == 1 )
                    // {
                    //     $store = PurchaseOrder::where('po_id', $ctVal['poId'])->update([
                    //         'is_approved' => 1,
                    //     ]);    
                    // }
                }

                foreach($request->approve_data_table as $rKey => $rVal){
                    $update = PurchaseOrderDetails::where('po_details_id', $rVal['po_details_id'])->update([
                        'po_qty' => $rVal['po_qty'],
                        'amount' => $rVal['amount'],
                    ]);  
                }
                    
            }
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Approved Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            getActivityLogs("PO Approval", "add", $e->getMessage(),$e->getLine());  

            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Approved',
                'original_error' => $e->getMessage()
            ]);
        }
    }


    
    public function getPOApprovalDetails(Request $request){      

        $poApprorvl = PurchaseOrderDetails::select(['purchase_order_details.po_id','items.item_name', 'items.item_code', 'units.unit_name', 'purchase_order_details.po_qty','item_groups.item_group_name','purchase_order_details.del_date','purchase_order_details.rate_per_unit','purchase_order_details.amount','purchase_order_details.remarks','purchase_order_details.po_details_id','purchase_order_details.discount'])
        ->leftJoin('items','items.id','purchase_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','items.unit_id')
        ->where('purchase_order_details.po_id', $request->po_id)->get();

        if ($poApprorvl != null) {
            foreach ($poApprorvl as $poKey => $poVal) {
                if ($poVal->del_date != null) {
                    $poVal->del_date = Date::createFromFormat('Y-m-d', $poVal->del_date)->format('d/m/Y');
                }
            }
        }


        if($poApprorvl)
        {
            return response()->json([               
                'poApprorvl' => $poApprorvl, 
                'response_code' => 1,               
            ]);
        }else{
            return response()->json([               
                'response_code' => 0,
                'response_message' => "Not Data Found",
            ]);
        }

    }


    // for manage po approval

    public function indexforManage(PurchaseOrder $purchase_order1,Request $request,DataTables $dataTables)
    {

        $location = getCurrentLocation();
        $yearIds = getCompanyYearIdsToTill();

        $purchase_order1 = PurchaseOrder::select([
            'purchase_order.po_id','purchase_order.po_number','purchase_order.po_date','purchase_order.po_sequence','purchase_order.person_name','suppliers.supplier_name','locations.location_name','items.item_name','items.item_code','purchase_order_details.po_qty', 'purchase_order.last_by_user_id','purchase_order.created_by_user_id', 'purchase_order_details.rate_per_unit','created_user.user_name as created_by_name','last_user.user_name as last_by_name', 'purchase_order.created_on','purchase_order.last_on',
            DB::raw('SUM(purchase_order_details.amount) as amount'), 
            'purchase_order_details.del_date', 'purchase_order_details.remarks', 'item_groups.item_group_name', 'units.unit_name'])
            ->leftJoin('purchase_order_details','purchase_order_details.po_id' ,'purchase_order.po_id')
            ->leftJoin('suppliers','suppliers.id' ,'purchase_order.supplier_id')
            ->leftJoin('locations','locations.id' ,'purchase_order.to_location_id')
            ->leftJoin('items','items.id','=','purchase_order_details.item_id')
            ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
            ->leftJoin('units','units.id','=','items.unit_id')
             ->leftJoin('admin AS created_user', 'created_user.id', '=', 'purchase_order.created_by_user_id')
             ->leftJoin('admin AS last_user', 'last_user.id', '=', 'purchase_order.last_by_user_id')
            ->whereIN('purchase_order.year_id', $yearIds)
            ->where('purchase_order.is_approved', '=', 1)
            ->groupBy('purchase_order.po_number');
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


            $purchase_order1->whereDate('purchase_order.po_date','>=',$from);

            $purchase_order1->whereDate('purchase_order.po_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $purchase_order1->where('purchase_order.po_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $purchase_order1->where('purchase_order.po_date','<=',$to);

        } 
      
        return DataTables::of($purchase_order1)

        ->editColumn('created_by_user_id', function($purchase_order1){
            if($purchase_order1->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$purchase_order1->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
       
        ->editColumn('amount', function($purchase_order1){
            return $purchase_order1->amount > 0 ? number_format((float)$purchase_order1->amount, 3, '.','') : number_format((float) 0, 3, '.','');

        })
     
        ->editColumn('last_by_user_id', function($purchase_order1){
            if($purchase_order1->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$purchase_order1->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
         ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('po_date', function($purchase_order1){           
            if ($purchase_order1->po_date != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d', $purchase_order1->po_date)->format('d/m/Y'); 
                
                return $formatedDate1;

            }else{
                return '';
            }
        })
        ->filterColumn('purchase_order.po_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.po_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('ref_date', function($purchase_order1){           
            if ($purchase_order1->ref_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $purchase_order1->ref_date)->format('d/m/Y'); 
                
                return $formatedDate3;

            }else{
                return '';
            }
        })
        // ->filterColumn('purchase_order.ref_date', function ($query, $keyword) {
        //     $query->whereRaw("DATE_FORMAT(purchase_order.ref_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        // })
        ->editColumn('created_on', function($purchase_order1){
            if ($purchase_order1->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $purchase_order1->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('purchase_order.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($purchase_order1){
            if ($purchase_order1->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $purchase_order1->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
          ->filterColumn('purchase_order.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
      
        ->addColumn('options',function($purchase_order){
            $action = "<div>";
            if(hasAccess("po_approval","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
       
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options','po_date','ref_date'])
        ->make(true);

      
    }


    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{

            $grn_data = GRNMaterialDetails::
            leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
            ->where('purchase_order_details.po_id',$request->id)->get();
            if($grn_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, PO Is Used In GRN.",
                ]);
            }
            
            $poData = PurchaseOrderDetails::select('po_details_id')->where('po_id','=',$request->id)->get();

            $grnData = GRNMaterialDetails::whereIn('po_details_id',$poData)->get();

            if($grnData->isEmpty()){

                $purchase_order1 = PurchaseOrder::where('po_id', $request->id)->update([
                    'is_approved' => 0,    
                ]);

            }else{
                return response()->json([
                    'response_code'    => '0',
                    'response_message' => "This Is Used Somewhere, You Can't Delete",
                ]);

            }
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){     
            DB::rollBack();    
            getActivityLogs("PO Approval", "delete", $e->getMessage(),$e->getLine());  
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



    public function managePendingPo(){
        return view('manage.manage-pending_po_list');
    }

    public function indexPendingPo(Request $request,DataTables $dataTables){

         $yearIds = getCompanyYearIdsToTill();
         $locationCode = getCurrentLocation();        
      

         $get_po_supplier = PurchaseOrderDetails::select('suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','purchase_order.po_sequence','locations.location_name','items.item_name' ,'units.unit_name',
        'items.item_code', 'item_groups.item_group_name', 'purchase_order_details.del_date','purchase_order_details.po_qty',
        DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)
        FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)
        FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),  )
        
        ->leftJoin('purchase_order','purchase_order.po_id', 'purchase_order_details.po_id')
        ->leftJoin('locations','locations.id','=','purchase_order.to_location_id')
        ->leftJoin('items','items.id','=','purchase_order_details.item_id')           
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('suppliers','suppliers.id', 'purchase_order.supplier_id')
        ->where('purchase_order.to_location_id',$locationCode->id)
        ->where('purchase_order.is_approved','=',1)
        ->whereIn('purchase_order.year_id',$yearIds)               
        ->having('pend_po_qty','>',0);

        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $get_po_supplier->whereDate('purchase_order.po_date','>=',$from);
            $get_po_supplier->whereDate('purchase_order.po_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $get_po_supplier->where('purchase_order.po_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');
            $get_po_supplier->where('purchase_order.po_date','<=',$to);

        } 
        return DataTables::of($get_po_supplier)

        ->editColumn('po_date', function($get_po_supplier){           
            if ($get_po_supplier->po_date != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d', $get_po_supplier->po_date)->format('d/m/Y'); 
                
                return $formatedDate1;

            }else{
                return '';
            }
        })
        ->editColumn('del_date', function($get_po_supplier){           
            if ($get_po_supplier->del_date != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d', $get_po_supplier->del_date)->format('d/m/Y'); 
                
                return $formatedDate1;

            }else{
                return '';
            }
        })

        ->filterColumn('purchase_order.po_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.po_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('purchase_order_details.del_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order_details.del_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->editColumn('pend_po_qty', function($get_po_supplier){
            return $get_po_supplier->pend_po_qty > 0 ? number_format((float)$get_po_supplier->pend_po_qty, 3, '.','') : number_format((float) 0, 3, '.','');

        })
        ->filterColumn('pend_po_qty', function($query, $keyword) {
            $query->whereRaw("(
                (SELECT IFNULL(SUM(pod.po_qty),0)
                FROM purchase_order_details AS pod
                WHERE pod.po_details_id = purchase_order_details.po_details_id)  
            - (SELECT IFNULL(SUM(psid.sc_qty),0)
                FROM purchase_order_short_close AS psid  
                WHERE psid.po_details_id = purchase_order_details.po_details_id)
            - (SELECT IFNULL(SUM(gid.grn_qty),0)
                FROM material_receipt_grn_details AS gid
                WHERE gid.po_details_id = purchase_order_details.po_details_id)
            ) LIKE ?", ["%{$keyword}%"]);
        })

        ->editColumn('po_qty', function($get_po_supplier){
            return $get_po_supplier->po_qty > 0 ? number_format((float)$get_po_supplier->po_qty, 3, '.','') : number_format((float) 0, 3, '.','');

        })

        ->rawColumns(['po_date','del_date','pend_po_qty','po_qty'])
        ->make(true);

    }
       
}
    