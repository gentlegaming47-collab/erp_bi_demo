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

class QCApprovalSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-qc_approval_summary');
    }

    public function index(QCApproval $QCApproval,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();
// dd("dsdasdsad");
        $qc_data = QCApproval::select(['qc_approval.qc_id','qc_approval.qc_number','qc_approval.qc_sequence','qc_approval.qc_date','qc_approval.ok_qty','qc_approval.qc_qty','qc_approval.reject_qty','qc_approval.rejection_reason','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','items.item_name' ,'items.item_code' , 'item_groups.item_group_name','material_receipt_grn_details.grn_qty',
           DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),  ])
        ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','qc_approval.grn_details_id')
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
        ->leftJoin('items', 'items.id', 'qc_approval.item_id')
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'qc_approval.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'qc_approval.last_by_user_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')   
        ->where('qc_approval.year_id','=',$year_data->id)
        ->where('qc_approval.current_location_id','=',$location->id);
       
              
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $qc_data->whereDate('qc_approval.qc_date','>=',$from);

                $qc_data->whereDate('qc_approval.qc_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $qc_data->where('qc_approval.qc_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $qc_data->where('qc_approval.qc_date','<=',$to);

        }  

       return DataTables::of($qc_data)      

       ->editColumn('qc_date', function($qc_data){
            if ($qc_data->qc_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $qc_data->qc_date)->format(DATE_FORMAT); return $formatedDate3;
            }else{
                return '';
            }
        })
       ->editColumn('grn_date', function($qc_data){
            if ($qc_data->grn_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $qc_data->grn_date)->format(DATE_FORMAT); return $formatedDate3;
            }else{
                return '';
            }
        })
       ->editColumn('po_date', function($qc_data){
            if ($qc_data->po_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $qc_data->po_date)->format(DATE_FORMAT); return $formatedDate3;
            }else{
                return '';
            }
        })

        ->filterColumn('qc_approval.qc_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(qc_approval.qc_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('grn_material_receipt.grn_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.grn_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('purchase_order.po_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.po_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->editColumn('grn_qty', function($qc_data) {
            return $qc_data->grn_qty > 0 
                ? number_format((float)$qc_data->grn_qty, 3, '.', '') 
                : number_format(0, 3, '.', ''); 
            })
        ->editColumn('qc_qty', function($qc_data) {
            return $qc_data->ok_qty > 0 
                ? number_format((float)$qc_data->qc_qty, 3, '.', '') 
                : number_format(0, 3, '.', ''); 
            })
        ->editColumn('reject_qty', function($qc_data) {
            return $qc_data->ok_qty > 0 
                ? number_format((float)$qc_data->reject_qty, 3, '.', '') 
                : number_format(0, 3, '.', ''); 
            })
       ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options'])
       ->make(true);
    }

}
