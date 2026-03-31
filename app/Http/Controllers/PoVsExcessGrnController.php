<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GRNMaterialDetails;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Date;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;  

class PoVsExcessGrnController extends Controller
{
    public function manage()
    {
        return view('manage.manage-po_vs_excess_grn');
    }

    public function index(GRNMaterialDetails $purchase_order1,Request $request,DataTables $dataTables)
    {

        $yearIds = getCompanyYearIdsToTill();
        $location = getCurrentLocation();
        
        $purchase_order1 = GRNMaterialDetails::select('grn_material_receipt.grn_date','grn_material_receipt.grn_number','suppliers.supplier_name','grn_material_receipt.bill_no','grn_material_receipt.bill_date','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name','material_receipt_grn_details.grn_qty', 'purchase_order_details.po_qty','purchase_order.po_number','purchase_order.po_date',
        DB::raw("(SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id) as total_grn_qty")
        )
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
        ->leftJoin('items','items.id','=','material_receipt_grn_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')

        ->whereIn('grn_material_receipt.year_id',$yearIds)
        ->where('material_receipt_grn_details.is_approved','=','Y')
        ->where('material_receipt_grn_details.is_approved','!=',null);
        // ->groupBy('grn_material_receipt.grn_number');
        // dd($purchase_order1);
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


            $purchase_order1->whereDate('grn_material_receipt.grn_date','>=',$from);

            $purchase_order1->whereDate('grn_material_receipt.grn_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $purchase_order1->where('grn_material_receipt.grn_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $purchase_order1->where('grn_material_receipt.grn_date','<=',$to);

        } 
        return DataTables::of($purchase_order1)

        ->editColumn('po_qty', function($purchase_order1){
            if($purchase_order1->po_qty != ""){
                return $purchase_order1->po_qty > 0 ? number_format((float)$purchase_order1->po_qty, 3, '.','') : number_format((float) 0, 3, '.','');
            }
        })
     
        ->editColumn('grn_qty', function($purchase_order1){
            if($purchase_order1->grn_qty != ""){
                return $purchase_order1->grn_qty > 0 ? number_format((float)$purchase_order1->grn_qty, 3, '.','') : number_format((float) 0, 3, '.','');
            }
        })
        ->editColumn('excess_qty', function($purchase_order1) {           
            if ($purchase_order1->po_qty != "" && $purchase_order1->total_grn_qty) {
                $excess_qty = $purchase_order1->total_grn_qty - $purchase_order1->po_qty;               
            }
            return number_format((float)$excess_qty, 3, '.','');
        })
        ->filterColumn('excess_qty', function($query, $keyword) {
            $keyword = floatval($keyword); 
            $query->whereRaw("((SELECT IFNULL(SUM(gid.grn_qty),0) 
                FROM material_receipt_grn_details AS gid 
                WHERE gid.po_details_id = purchase_order_details.po_details_id) - purchase_order_details.po_qty) = ?", [$keyword]);
        })

        ->editColumn('grn_date', function($purchase_order1){           
            if ($purchase_order1->grn_date != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d', $purchase_order1->grn_date)->format('d/m/Y'); 
                return $formatedDate1;
            }else{
                return '';
            }
        })
        ->editColumn('bill_date', function($purchase_order1){           
            if ($purchase_order1->bill_date != null) {
                $bill_date = Date::createFromFormat('Y-m-d', $purchase_order1->bill_date)->format('d/m/Y'); 
                return $bill_date;
            }else{
                return '';
            }
        })
        ->editColumn('po_date', function($purchase_order1){           
            if ($purchase_order1->po_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $purchase_order1->po_date)->format('d/m/Y'); 
                return $formatedDate2;
            }else{
                return '';
            }
        })

        ->filterColumn('grn_material_receipt.grn_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.grn_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('grn_material_receipt.bill_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.bill_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('purchase_order.po_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.po_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        
        ->rawColumns(['grn_date','po_date','po_qty','grn_qty','excess_qty','bill_date'])
        ->make(true);
    }
}