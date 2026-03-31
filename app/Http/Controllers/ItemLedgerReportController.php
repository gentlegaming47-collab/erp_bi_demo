<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocationStock;
use App\Models\GRNMaterialDetails;
use App\Models\QCApproval;
use App\Models\SupplierRejectoionDetails;
use App\Models\ItemReturnDetail;
use App\Models\ItemIssueDetail;
use App\Models\ItemProductionDetail;
use App\Models\ItemAssemblyProduction;
use App\Models\ItemAssemblyProductionDetails;
use App\Models\DispatchPlanDetails;
use App\Models\DispatchPlanDetailsDetails;
use App\Models\ReplacementItemDecisionDetails;
use DataTables;

class ItemLedgerReportController extends Controller
{
    public function manage()
    {
        return view('manage.manage-item_ledger_report');
    }

    public function index(LocationStock $location_stock,Request $request,DataTables $dataTables)
    { 
        $Location = getCurrentLocation();

        $location_stock = LocationStock::select(['location_stock.ls_id','location_stock.item_id','location_stock.stock_qty','items.item_name','items.item_code'])
        ->leftJoin('locations','locations.id','=','location_stock.location_id')
        ->leftJoin('items','items.id','=','location_stock.item_id')
       ->where('location_stock.location_id','=',$Location->id);

        return DataTables::of($location_stock)
        ->editColumn('item_name', function($location_stock){ 
            if($location_stock->item_name != ''){
                $item_name = ucfirst($location_stock->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

        ->editColumn('stock_qty', function($location_stock){
            if($location_stock->stock_qty != null || $location_stock->stock_qty){
                $stockQty = number_format((float)$location_stock->stock_qty, 3, '.','');
                
                return isset($stockQty)?$stockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })

        ->addColumn('stock_in',function($location_stock) use ($Location) {
            $grnQty = GRNMaterialDetails::
            leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
            ->where('material_receipt_grn_details.qc_required','=','No')
            ->where('material_receipt_grn_details.service_item','=','No')
            ->where('grn_material_receipt.current_location_id',$Location->id)
            ->where('material_receipt_grn_details.item_id',$location_stock->item_id)->sum('material_receipt_grn_details.grn_qty');

            $qcQty = QCApproval::where('current_location_id',$Location->id)->where('item_id','=',$location_stock->item_id)->sum('ok_qty');

            $itemReturnQty = ItemReturnDetail::
            leftJoin('item_return','item_return.item_return_id','=','item_return_details.item_return_id')          
            ->where('item_return.current_location_id',$Location->id)
            ->where('item_return_details.item_id',$location_stock->item_id)->sum('item_return_details.return_qty');

            $itemProdQty = ItemProductionDetail::
            leftJoin('item_production','item_production.ip_id','=','item_production_details.ip_id')          
            ->where('item_production.current_location_id',$Location->id)
            ->where('item_production_details.item_id',$location_stock->item_id)->sum('item_production_details.production_qty');

            $itemAssQty = ItemAssemblyProduction::where('current_location_id',$Location->id)->where('item_id','=',$location_stock->item_id)->sum('assembly_qty');

            $repItemQty = ReplacementItemDecisionDetails::
            leftJoin('replacement_item_decision','replacement_item_decision.replacement_id','=','replacement_item_decision_details.replacement_id')   
            ->where('replacement_item_decision.replacement_type_id_fix','=',1)       
            ->where('replacement_item_decision.current_location_id',$Location->id)
            ->where('replacement_item_decision_details.item_id',$location_stock->item_id)->sum('replacement_item_decision_details.decision_qty');

            $totalStockIn = $grnQty + $qcQty + $itemReturnQty + $itemProdQty + $itemAssQty + $repItemQty;

            $totalStockIn = round($totalStockIn, 3);

            return number_format((float)$totalStockIn, 3, '.','');
           
        })
        // ->filterColumn('stock_in', function($query, $keyword) use ($Location) {
        //     $keyword = floatval($keyword);

        //     $query->whereRaw("( 
        //         (SELECT IFNULL(SUM(mrgd.grn_qty),0)
        //         FROM material_receipt_grn_details mrgd
        //         LEFT JOIN grn_material_receipt gmr ON gmr.grn_id = mrgd.grn_id
        //         WHERE mrgd.qc_required = 'No'
        //         AND mrgd.service_item = 'No'
        //         AND gmr.current_location_id = ?
        //         AND mrgd.item_id = items.id)
        //         +
        //         (SELECT IFNULL(SUM(ok_qty),0) FROM qc_approval
        //         WHERE current_location_id = ? AND item_id = items.id)
        //         +
        //         (SELECT IFNULL(SUM(item_return_details.return_qty),0)
        //         FROM item_return_details
        //         LEFT JOIN item_return ON item_return.item_return_id = item_return_details.item_return_id
        //         WHERE item_return.current_location_id = ? 
        //         AND item_return_details.item_id = items.id)
        //         +
        //         (SELECT IFNULL(SUM(item_production_details.production_qty),0)
        //         FROM item_production_details
        //         LEFT JOIN item_production ON item_production.ip_id = item_production_details.ip_id
        //         WHERE item_production.current_location_id = ?
        //         AND item_production_details.item_id = items.id)
        //         +
        //         (SELECT IFNULL(SUM(assembly_qty),0)
        //         FROM item_assembly_production
        //         WHERE current_location_id = ? AND item_id = items.id)
        //         +
        //         (SELECT IFNULL(SUM(replacement_item_decision_details.decision_qty),0)
        //         FROM replacement_item_decision_details
        //         LEFT JOIN replacement_item_decision 
        //         ON replacement_item_decision.replacement_id = replacement_item_decision_details.replacement_id
        //         WHERE replacement_item_decision.replacement_type_id_fix = 1
        //         AND replacement_item_decision.current_location_id = ?
        //         AND replacement_item_decision_details.item_id = items.id)
        //     ) LIKE ?", [
        //         $Location->id, $Location->id, $Location->id, $Location->id, $Location->id, $Location->id,
        //         "%{$keyword}%"
        //     ]);
        // })
        ->filterColumn('stock_in', function($query, $keyword) use ($Location){
            $query->whereRaw("(
                (SELECT IFNULL(SUM(mrgd.grn_qty),0)
                FROM material_receipt_grn_details mrgd
                LEFT JOIN grn_material_receipt gmr ON gmr.grn_id = mrgd.grn_id
                WHERE mrgd.qc_required = 'No'
                AND mrgd.service_item = 'No'
                AND gmr.current_location_id = ?
                AND mrgd.item_id = items.id)
                +
                (SELECT IFNULL(SUM(ok_qty),0)
                FROM qc_approval
                WHERE current_location_id = ? AND item_id = items.id)
                +
                (SELECT IFNULL(SUM(item_return_details.return_qty),0)
                FROM item_return_details
                LEFT JOIN item_return ON item_return.item_return_id = item_return_details.item_return_id
                WHERE item_return.current_location_id = ? AND item_return_details.item_id = items.id)
                +
                (SELECT IFNULL(SUM(item_production_details.production_qty),0)
                FROM item_production_details
                LEFT JOIN item_production ON item_production.ip_id = item_production_details.ip_id
                WHERE item_production.current_location_id = ? AND item_production_details.item_id = items.id)
                +
                (SELECT IFNULL(SUM(assembly_qty),0)
                FROM item_assembly_production
                WHERE current_location_id = ? AND item_id = items.id)
                +
                (SELECT IFNULL(SUM(replacement_item_decision_details.decision_qty),0)
                FROM replacement_item_decision_details
                LEFT JOIN replacement_item_decision ON replacement_item_decision.replacement_id = replacement_item_decision_details.replacement_id
                WHERE replacement_item_decision.replacement_type_id_fix = 1
                AND replacement_item_decision.current_location_id = ?
                AND replacement_item_decision_details.item_id = items.id)
            ) LIKE ?", [
                $Location->id, $Location->id, $Location->id, $Location->id, $Location->id, $Location->id,
                "%{$keyword}%"
            ]);
        })
        ->addColumn('stock_out',function($location_stock) use ($Location) {

            $srcQty = SupplierRejectoionDetails::
            leftJoin('supplier_rejection_challan','supplier_rejection_challan.src_id','=','supplier_rejection_challan_details.src_id')          
            ->where('supplier_rejection_challan.current_location_id',$Location->id)
            ->where('supplier_rejection_challan_details.item_id',$location_stock->item_id)->sum('supplier_rejection_challan_details.challan_qty');

            $itemIssueQty = ItemIssueDetail::
            leftJoin('item_issue','item_issue.item_issue_id','=','item_issue_details.item_issue_id')          
            ->where('item_issue.current_location_id',$Location->id)
            ->where('item_issue_details.item_id',$location_stock->item_id)->sum('item_issue_details.issue_qty');

            $itemAssQty = ItemAssemblyProductionDetails:: 
            leftJoin('item_assembly_production','item_assembly_production.iap_id','=','item_assembly_production_details.iap_id')          
            ->where('item_assembly_production.current_location_id',$Location->id)
            ->where('item_assembly_production_details.item_id',$location_stock->item_id)->sum('item_assembly_production_details.consumption_qty');

            $dpQty = DispatchPlanDetails:: 
            leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')  
            ->where('dispatch_plan_details.fitting_item','=','No')        
            ->where('dispatch_plan.current_location_id',$Location->id)
            ->where('dispatch_plan_details.item_id',$location_stock->item_id)->sum('dispatch_plan_details.plan_qty');

            $dpDetailQty = DispatchPlanDetailsDetails:: 
            leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','dispatch_plan_details_details.dp_details_id')  
            ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')  
            ->where('dispatch_plan.current_location_id',$Location->id)
            ->where('dispatch_plan_details_details.item_id',$location_stock->item_id)->sum('dispatch_plan_details_details.plan_qty');

            $totalStockOut = $srcQty + $itemIssueQty + $itemAssQty + $dpQty + $dpDetailQty;

            $totalStockOut = round($totalStockOut, 3);

            return number_format((float)$totalStockOut, 3, '.','');
           
        })
        // ->filterColumn('stock_out', function($query, $keyword) use ($Location) {
        //     $keyword = floatval($keyword);

        //     $query->whereRaw("( 
        //         (SELECT IFNULL(SUM(srd.challan_qty),0)
        //         FROM supplier_rejection_challan_details AS srd
        //         LEFT JOIN supplier_rejection_challan src 
        //         ON src.src_id = srd.src_id
        //         WHERE src.current_location_id = ?
        //         AND srd.item_id = items.id)
        //         +
        //         (SELECT IFNULL(SUM(iid.issue_qty),0)
        //         FROM item_issue_details AS iid
        //         LEFT JOIN item_issue ii 
        //         ON ii.item_issue_id = iid.item_issue_id
        //         WHERE ii.current_location_id = ?
        //         AND iid.item_id = items.id)
        //         +
        //         (SELECT IFNULL(SUM(iapd.consumption_qty),0)
        //         FROM item_assembly_production_details AS iapd
        //         LEFT JOIN item_assembly_production iap 
        //         ON iap.iap_id = iapd.iap_id
        //         WHERE iap.current_location_id = ?
        //         AND iapd.item_id = items.id)
        //         +
        //         (SELECT IFNULL(SUM(dp.plan_qty),0)
        //         FROM dispatch_plan_details AS dp
        //         LEFT JOIN dispatch_plan d 
        //         ON d.dp_id = dp.dp_id
        //         WHERE dp.fitting_item = 'No'
        //         AND d.current_location_id = ?
        //         AND dp.item_id = items.id)
        //         +
        //         (SELECT IFNULL(SUM(dpd.plan_qty),0)
        //         FROM dispatch_plan_details_details AS dpd
        //         LEFT JOIN dispatch_plan_details dp 
        //         ON dp.dp_details_id = dpd.dp_details_id
        //         LEFT JOIN dispatch_plan d 
        //         ON d.dp_id = dp.dp_id
        //         WHERE d.current_location_id = ?
        //         AND dpd.item_id = items.id)
        //     ) LIKE ?", [
        //         $Location->id, $Location->id, $Location->id, $Location->id, $Location->id,
        //         "%{$keyword}%"
        //     ]);
        // })
        ->filterColumn('stock_out', function($query, $keyword) use ($Location){
            $query->whereRaw("(
                (SELECT IFNULL(SUM(srd.challan_qty),0)
                FROM supplier_rejection_challan_details AS srd
                LEFT JOIN supplier_rejection_challan src ON src.src_id = srd.src_id
                WHERE src.current_location_id = ? AND srd.item_id = items.id)
                +
                (SELECT IFNULL(SUM(iid.issue_qty),0)
                FROM item_issue_details AS iid
                LEFT JOIN item_issue ii ON ii.item_issue_id = iid.item_issue_id
                WHERE ii.current_location_id = ? AND iid.item_id = items.id)
                +
                (SELECT IFNULL(SUM(iapd.consumption_qty),0)
                FROM item_assembly_production_details AS iapd
                LEFT JOIN item_assembly_production iap ON iap.iap_id = iapd.iap_id
                WHERE iap.current_location_id = ? AND iapd.item_id = items.id)
                +
                (SELECT IFNULL(SUM(dp.plan_qty),0)
                FROM dispatch_plan_details AS dp
                LEFT JOIN dispatch_plan d ON d.dp_id = dp.dp_id
                WHERE dp.fitting_item = 'No' AND d.current_location_id = ? AND dp.item_id = items.id)
                +
                (SELECT IFNULL(SUM(dpd.plan_qty),0)
                FROM dispatch_plan_details_details AS dpd
                LEFT JOIN dispatch_plan_details dp ON dp.dp_details_id = dpd.dp_details_id
                LEFT JOIN dispatch_plan d ON d.dp_id = dp.dp_id
                WHERE d.current_location_id = ? AND dpd.item_id = items.id)
            ) LIKE ?", [
                $Location->id, $Location->id, $Location->id, $Location->id, $Location->id,
                "%{$keyword}%"
            ]);
        })
        ->rawColumns(['stock_in','stock_out'])
        ->make(true);
    }
}