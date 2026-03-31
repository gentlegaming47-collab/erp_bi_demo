<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
use App\Models\GRNMaterialDetails;
use App\Models\GRNMaterailSecondaryDetails;
use App\Models\GRNMaterial;
use App\Models\GRNVerification;
use App\Models\LocationStock;
use App\Models\LocationDetailStock;

class GRNVerificationSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-grn_verification_summary');
    }

     public function index(GRNVerification $gv,Request $request,DataTables $dataTables)
    {

        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation()->id;


        $grnMaterial = GRNVerification::select(['grn_material_receipt.grn_number','grn_material_receipt.grn_sequence','grn_verification.gv_date','grn_material_receipt.grn_date','grn_verification.gv_id','grn_verification.gv_reason',
        'grn_verification.gv_date','items.item_name','item_details.secondary_item_name','locations.location_name','grn_verification.mismatch_qty',
    
        'dispatch_plan.dp_number','dispatch_plan.dp_date',
        DB::raw("CASE  WHEN grn_secondary_details.grn_secondary_details_id IS NOT NULL THEN grn_secondary_details.mismatch_qty
        ELSE material_receipt_grn_details.mismatch_qty  END as mismatch_qty"),
        DB::raw("CASE  WHEN loading_entry_secondary_details.plan_qty IS NOT NULL THEN loading_entry_secondary_details.plan_qty
        ELSE dispatch_plan_details.plan_qty  END as plan_qty"),
        'material_receipt_grn_details.grn_qty',
         DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),

        ])

        ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id' ,'grn_verification.grn_details_id')
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id' ,'material_receipt_grn_details.grn_id')
        ->leftJoin('grn_secondary_details','grn_secondary_details.grn_secondary_details_id' ,'grn_verification.grn_secondary_details_id')
        ->leftJoin('item_details','item_details.item_details_id','=','grn_verification.item_details_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
        ->leftJoin('loading_entry_secondary_details','loading_entry_secondary_details.le_secondary_details_id','=','grn_secondary_details.le_secondary_details_id')
        ->leftJoin('locations','locations.id','=','grn_material_receipt.current_location_id')
        // ->leftJoin('locations','locations.id','=','grn_material_receipt.to_location_id')
        ->leftJoin('items','items.id','=','material_receipt_grn_details.item_id')
          ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')
        ->where('grn_material_receipt.current_location_id', $locationCode)

        ->where('grn_material_receipt.year_id', '=', $year_data->id);

        if($request->from_date != "" && $request->to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');


                $grnMaterial->whereDate('grn_verification.gv_date','>=',$from);

                $grnMaterial->whereDate('grn_verification.gv_date','<=',$to);

        }else if($request->from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

                $grnMaterial->where('grn_verification.gv_date','>=',$from);

        }else if($request->to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');

                $grnMaterial->where('grn_verification.gv_date','<=',$to);

        }  
        if($request->grn_number !=''){
            $grnMaterial->where('grn_material_receipt.grn_number','like', "%{$request->grn_number}%");
        }
         if($request->item_id !=''){
            $grnMaterial->where('material_receipt_grn_details.item_id', '=', $request->item_id);
        }
        return DataTables::of($grnMaterial)
        ->filterColumn('unit_name', function($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('units.unit_name', 'like', "%{$keyword}%")
                ->orWhere('second_unit.unit_name', 'like', "%{$keyword}%");
            });
        })
      
        ->editColumn('grn_date', function($grnMaterial){
            if ($grnMaterial->grn_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $grnMaterial->grn_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('grn_material_receipt.grn_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.grn_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('gv_date', function($grnMaterial){
            if ($grnMaterial->gv_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $grnMaterial->gv_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('grn_verification.gv_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_verification.gv_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
          ->editColumn('dp_date', function($grnMaterial){
            if ($grnMaterial->dp_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $grnMaterial->dp_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('dispatch_plan.dp_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dispatch_plan.dp_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        
        ->editColumn('mismatch_qty', function($grnMaterial){
            return $grnMaterial->mismatch_qty ? number_format((float)$grnMaterial->mismatch_qty, 3, '.','') : number_format((float) 0, 3, '.','');
       })
        ->editColumn('grn_qty', function($grnMaterial){
            return $grnMaterial->grn_qty ? number_format((float)$grnMaterial->grn_qty, 3, '.','') : number_format((float) 0, 3, '.','');
       })

           ->addColumn('plan_qty', function($grnMaterial){

            return $grnMaterial->plan_qty ? number_format((float)$grnMaterial->plan_qty, 3, '.','') : number_format((float) 0, 3, '.','');
       })

        ->filterColumn('plan_qty', function ($query, $keyword) {
            $query->whereRaw("
                CASE  
                    WHEN loading_entry_secondary_details.plan_qty IS NOT NULL 
                        THEN loading_entry_secondary_details.plan_qty
                    ELSE dispatch_plan_details.plan_qty  
                END LIKE ?", ["%{$keyword}%"]);
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'options','grn_date'])
        ->make(true);
    }
    
}
