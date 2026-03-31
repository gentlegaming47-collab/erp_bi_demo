<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Svg\Tag\Rect;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Date;
use Illuminate\Support\Facades\Auth;
use Str;
use App\Models\Supplier;
use App\Models\Transporter;
use App\Models\Item;
use App\Models\GRNMaterial;

class GRNSummaryLocationController extends Controller
{
    public function manage()
    {
        return view('manage.manage-grn_summary_location');
    }

    public function index(GRNMaterial $grnMaterial,Request $request,DataTables $dataTables)
    {
        $locationCode = getCurrentLocation()->id;

        $year_data = getCurrentYearData();

        $grnMaterial = GRNMaterial::select(['grn_material_receipt.grn_id', 'grn_material_receipt.grn_sequence','grn_material_receipt.grn_type_value_fix','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','grn_material_receipt.grn_type_id_fix','purchase_order.po_number',
        'purchase_order.po_date','items.item_name','item_details.secondary_item_name','items.item_code','material_receipt_grn_details.grn_qty','material_receipt_grn_details.rate_per_unit','material_receipt_grn_details.remarks','transporters.transporter_name','grn_material_receipt.lr_no_date','grn_material_receipt.vehicle_no','grn_material_receipt.special_notes',
        DB::raw('SUM(material_receipt_grn_details.amount) as amount'),
         DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"), 
       'locations.location_name',
       //  DB::raw('(CASE WHEN grn_type_value_fix ="From Location" THEN locations.location_name ELSE suppliers.supplier_name END) as name'),
        'grn_material_receipt.bill_no',
        'grn_material_receipt.bill_date','grn_material_receipt.to_location_id','dispatch_plan.dp_number','dispatch_plan.dp_date'])

        ->leftJoin('suppliers','suppliers.id' ,'grn_material_receipt.supplier_id')
        ->leftJoin('transporters','transporters.id' ,'grn_material_receipt.transporter_id')
        ->leftJoin('locations','locations.id' ,'grn_material_receipt.to_location_id')
        ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_id' ,'grn_material_receipt.grn_id')
        ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id' ,'material_receipt_grn_details.dc_details_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id' ,'loading_entry_details.dp_details_id')
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id' ,'dispatch_plan_details.dp_id')
        ->leftJoin('items','items.id' ,'material_receipt_grn_details.item_id')
        ->leftJoin('item_details','item_details.item_id' ,'items.id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')   
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id' ,'material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id' ,'purchase_order_details.po_id')
        ->where('grn_material_receipt.current_location_id', $locationCode)
        ->where('grn_material_receipt.year_id', '=', $year_data->id)
        ->where('grn_material_receipt.grn_type_id_fix',3)
        ->groupBy('grn_material_receipt.grn_number');
         if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $grnMaterial->whereDate('grn_material_receipt.grn_date','>=',$from);

                $grnMaterial->whereDate('grn_material_receipt.grn_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $grnMaterial->where('grn_material_receipt.grn_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $grnMaterial->where('grn_material_receipt.grn_date','<=',$to);

        }  
        return DataTables::of($grnMaterial)

        ->editColumn('amount', function($grnMaterial){
            return $grnMaterial->amount > 0 ? number_format((float)$grnMaterial->amount, 3, '.','') : number_format((float) 0, 3, '.','');
       })
        ->editColumn('grn_qty', function($grnMaterial){
            return $grnMaterial->grn_qty > 0 ? number_format((float)$grnMaterial->grn_qty, 3, '.','') : number_format((float) 0, 3, '.','');
       })
        ->editColumn('rate_per_unit', function($grnMaterial){
            return $grnMaterial->rate_per_unit > 0 ? number_format((float)$grnMaterial->rate_per_unit, 3, '.','') : number_format((float) 0, 3, '.','');
       })

        ->editColumn('grn_date', function($grnMaterial){
            if ($grnMaterial->grn_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $grnMaterial->grn_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->editColumn('dp_date', function($grnMaterial){
            if ($grnMaterial->dp_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $grnMaterial->dp_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->editColumn('bill_date', function($grnMaterial){
            if ($grnMaterial->bill_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $grnMaterial->bill_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })

        ->filterColumn('grn_material_receipt.grn_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.grn_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->filterColumn('dispatch_plan.dp_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dispatch_plan.dp_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('grn_material_receipt.bill_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.bill_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->make(true);
    }
}
