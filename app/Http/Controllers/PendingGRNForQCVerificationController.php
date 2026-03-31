<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QCApproval;
use App\Models\GRNMaterialDetails;
use Illuminate\Support\Facades\DB;
use Date;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DataTables;
use App\Models\Admin;
use App\Models\SupplierRejectoionDetails;

class PendingGRNForQCVerificationController extends Controller
{
    public function manage()
    {
        return view('manage.manage-pending_grn_for_qc_verification');
    }
    public function index(QCApproval $QCApproval,Request $request,DataTables $dataTables)
    {
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        $grn_data = GRNMaterialDetails::select(['material_receipt_grn_details.grn_details_id','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','items.item_name' ,'items.item_code', 'item_groups.item_group_name','material_receipt_grn_details.grn_qty',
        DB::raw("(IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) - (SELECT IFNULL(SUM(qc_approval.qc_qty), 0) FROM qc_approval WHERE qc_approval.grn_details_id = material_receipt_grn_details.grn_details_id)) AS pend_grn_qty"),'item_details.item_details_id','item_details.secondary_item_name','grn_material_receipt.grn_sequence',        
        DB::raw('0 as qc_qty'),
        DB::raw('0 as ok_qty'),
        DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"), 
        ])
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
        ->leftJoin('grn_secondary_details','grn_secondary_details.grn_details_id','=','material_receipt_grn_details.grn_details_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
        ->leftJoin('items', 'items.id', 'material_receipt_grn_details.item_id')
        ->leftJoin('item_details','item_details.item_details_id','=','grn_secondary_details.item_details_id')
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
        // ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')   
        ->where('material_receipt_grn_details.qc_required','=','Yes')
        ->where('grn_material_receipt.grn_type_id_fix','=','1')
        ->where('grn_material_receipt.current_location_id',$locationCode->id)
        ->whereIn('grn_material_receipt.year_id',$yearIds)  
        ->groupBy('material_receipt_grn_details.grn_details_id')
        ->having('pend_grn_qty','>',0);
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


            $grn_data->whereDate('grn_material_receipt.grn_date','>=',$from);

            $grn_data->whereDate('grn_material_receipt.grn_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $grn_data->where('grn_material_receipt.grn_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $grn_data->where('grn_material_receipt.grn_date','<=',$to);

        }  
        // dd($sales_order);

        return DataTables::of($grn_data)
        ->editColumn('pend_grn_qty', function($row){
            return $row->pend_grn_qty > 0 ? number_format((float)$row->pend_grn_qty, 3, '.', '') : '';
        })
        ->editColumn('grn_qty', function($row){
            return $row->grn_qty > 0 ? number_format((float)$row->grn_qty, 3, '.', '') : '';
        })
        ->filterColumn('pend_grn_qty', function($query, $keyword) {
            $sql = "(IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) - 
                    (SELECT IFNULL(SUM(qc_approval.qc_qty), 0) 
                    FROM qc_approval 
                    WHERE qc_approval.grn_details_id = material_receipt_grn_details.grn_details_id))";
            $query->havingRaw("$sql LIKE ?", ["%{$keyword}%"]);
        })

        ->editColumn('grn_date', function($grn_data){
            if ($grn_data->grn_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $grn_data->grn_date)->format(DATE_FORMAT); return $formatedDate3;
            }else{
                return '';
            }
        })
        ->editColumn('po_date', function($grn_data){
            if ($grn_data->po_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $grn_data->po_date)->format(DATE_FORMAT); return $formatedDate3;
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
        ->make(true);
    
        // ->get();

    }
}
