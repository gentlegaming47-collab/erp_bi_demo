<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\GRNMaterialDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Date;
use Illuminate\Support\Facades\Auth;


class GrnAgainstPOApprovalController extends Controller
{
    public function manage()
    {
        return view('manage.manage-grn_against_po_approval');
    }

    public function index(PurchaseOrder $purchase_order,Request $request,DataTables $dataTables)
    {

        // $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();
        $location = getCurrentLocation();
        
        // $purchase_order = GRNMaterialDetails::select([
        // 'purchase_order.*','purchase_order_details.po_details_id','locations.location_name','to_location.location_name as to_location'])
        // //->leftJoin('purchase_order','purchase_order.po_id' ,'purchase_order_details.po_id')
        // ->leftJoin('purchase_order_details','purchase_order_details.po_id' ,'purchase_order.po_id')
        // ->leftJoin('locations','locations.id','purchase_order.current_location_id')
        // ->leftJoin('locations as to_location','to_location.id','purchase_order.to_location_id')
     
        // ->where('purchase_order.is_approved', 1)
        // // ->where('purchase_order.year_id', '=', $yearIds)
        // ->groupBy('purchase_order.po_number')
        // ->get();

        $purchase_order = GRNMaterialDetails::select('material_receipt_grn_details.*', 'suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','grn_material_receipt.grn_number','grn_material_receipt.grn_date','purchase_order_details.po_qty','grn_material_receipt.bill_no','grn_material_receipt.bill_date','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name', 
        //  DB::raw("(SELECT purchase_order_details.po_qty -  (SELECT IFNULL(SUM(psid.sc_qty),0) FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id))as pend_po_qty"), 

        DB::raw("(SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id) as total_grn_qty")

        )
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id') 
        ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
        ->leftJoin('items','items.id','=','material_receipt_grn_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->whereIn('grn_material_receipt.year_id', $yearIds)
        ->where('material_receipt_grn_details.is_approved','=','N')
        ->where('material_receipt_grn_details.is_approved','!=',null)
        ->where('grn_material_receipt.grn_type_id_fix','=','1')

        // ->where('purchase_order.to_location_id','=',$location->id)
        ->get();

        // dd($purchase_order);

        if($purchase_order != null){
            foreach($purchase_order as $val){
                if($val->po_date != null){
                    $val->po_date = Date::createFromFormat('Y-m-d', $val->po_date)->format('d/m/Y');
                }

                if($val->pend_po_qty != null){
                    $val->pend_po_qty = $val->pend_po_qty > 0 ? $val->pend_po_qty : 0;
                }

                if($val->bill_date != null){
                    $val->bill_date = Date::createFromFormat('Y-m-d', $val->bill_date)->format('d/m/Y');
                }else{
                    $val->bill_date = "";
                }

                if($val->grn_date != null){
                    $val->grn_date = Date::createFromFormat('Y-m-d', $val->grn_date)->format('d/m/Y');
                }else{
                    $val->grn_date = "";
                }

                if($val->bill_no != null){
                    $val->bill_no;
                }else{
                    $val->bill_no = "";
                }

                // if ($val->po_qty != "" && $val->grn_qty) {
                //     $val->excess_qty = $val->grn_qty - $val->po_qty;
                // }else{
                //     $val->excess_qty = "";
                // }
                if ($val->po_qty != "" && $val->total_grn_qty) {
                    $val->excess_qty = $val->total_grn_qty - $val->po_qty;
                    $val->grn_qty = $val->total_grn_qty;
                }else{
                    $val->excess_qty = "";
                }
                // return number_format((float)$excess_qty, 3, '.');
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

            if(isset($request->approve_data) && !empty($request->approve_data)){
                foreach($request->approve_data as $ctKey => $ctVal){

                        $store = GRNMaterialDetails::where('grn_details_id', $ctVal['grn_details_id'])->update([
                            'is_approved' => 'Y',
                        ]);   
         
                        // if(strtolower(Auth::user()->user_type) == "director" || Auth::user()->id == 1 )
                        // {
                        //     $store = GRNMaterialDetails::where('grn_details_id', $ctVal['grn_details_id'])->update([
                        //         'is_approved' => 'Y',
                        //     ]);    
                        // }
                    }
                    
            }
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Approved Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            getActivityLogs("Excess GRN Qty. Approval against PO", "add", $e->getMessage(),$e->getLine());  
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Approved',
                'original_error' => $e->getMessage()
            ]);
        }
    }
}