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

class GRNVerificationController extends Controller
{
    public function manage()
    {
        return view('manage.manage-grn_verification');
    }

    public function create()
    {
        return view('add.add-grn_verification');
    }

    public function getPendingGRNVerification(GRNMaterial $grnMaterial,Request $request,DataTables $dataTables)
    {
        $locationCode = getCurrentLocation()->id;

        $year_data = getCurrentYearData();

        // $used_grn_details_ids = GRNVerification::select('grn_details_id')->pluck('grn_details_id')->toArray();

        $usedPairs = GRNVerification::select('grn_details_id', 'grn_secondary_details_id')
        ->get()
        ->map(function ($row) {
            return $row->grn_details_id . '-' . ($row->grn_secondary_details_id ?? 'null');
        })
        ->toArray();

        $grnMaterial = GRNMaterialDetails::select(['grn_material_receipt.grn_id', 
        'grn_material_receipt.grn_number','grn_material_receipt.grn_date','items.item_name',
        'item_details.secondary_item_name','material_receipt_grn_details.grn_details_id','material_receipt_grn_details.item_id','grn_secondary_details.grn_secondary_details_id','item_details.item_details_id','grn_material_receipt.to_location_id','locations.location_name',
        'dispatch_plan.dp_number','dispatch_plan.dp_date',
        DB::raw("CASE  WHEN grn_secondary_details.grn_secondary_details_id IS NOT NULL THEN grn_secondary_details.mismatch_qty
        ELSE material_receipt_grn_details.mismatch_qty  END as mismatch_qty"),
        DB::raw("CASE  WHEN loading_entry_secondary_details.plan_qty IS NOT NULL THEN loading_entry_secondary_details.plan_qty
        ELSE dispatch_plan_details.plan_qty  END as plan_qty"),
        'material_receipt_grn_details.grn_qty',
         DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),
        ])

        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id' ,'material_receipt_grn_details.grn_id')
        ->leftJoin('grn_verification','grn_verification.grn_details_id' ,'material_receipt_grn_details.grn_details_id')
        ->leftJoin('grn_secondary_details','grn_secondary_details.grn_details_id' ,'material_receipt_grn_details.grn_details_id')
        ->leftJoin('item_details','item_details.item_details_id','=','grn_secondary_details.item_details_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
        ->leftJoin('loading_entry_secondary_details','loading_entry_secondary_details.le_secondary_details_id','=','grn_secondary_details.le_secondary_details_id')
        ->leftJoin('locations','locations.id','=','grn_material_receipt.current_location_id')
        // ->leftJoin('locations','locations.id','=','grn_material_receipt.to_location_id')
        ->leftJoin('items','items.id','=','material_receipt_grn_details.item_id')
         ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')
        ->where('grn_material_receipt.year_id', '=', $year_data->id)
        ->where('grn_material_receipt.current_location_id', $locationCode)
        ->where('grn_material_receipt.grn_type_id_fix','=', 3)
        ->whereNotNull('material_receipt_grn_details.mismatch_qty')
        ->whereRaw("
            CASE 
                WHEN grn_secondary_details.grn_secondary_details_id IS NOT NULL 
                    THEN grn_secondary_details.mismatch_qty
                ELSE material_receipt_grn_details.mismatch_qty
            END != 0.000
        ")
         ->whereNotIn(
            DB::raw("CONCAT(material_receipt_grn_details.grn_details_id, '-', COALESCE(grn_secondary_details.grn_secondary_details_id, 'null'))"),
            $usedPairs
         );
        // ->groupBy('grn_material_receipt.grn_number');

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
            
            if($grnMaterial->item_details_id != ""){
                
                return $grnMaterial->grn_qty ? $grnMaterial->grn_qty : 0;
            }else{
                return $grnMaterial->grn_qty ? number_format((float)$grnMaterial->grn_qty, 3, '.','') : number_format((float) 0, 3, '.','');

            }
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
        
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'options','grn_date','name','bill_date'])
        ->make(true);
    }

    public function index(GRNVerification $gv,Request $request,DataTables $dataTables)
    {

        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation()->id;


        $grnMaterial = GRNVerification::select(['grn_material_receipt.grn_number','grn_material_receipt.grn_date','grn_verification.gv_id','grn_verification.gv_reason',
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

        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $grnMaterial->whereDate('grn_verification.gv_date','>=',$from);

                $grnMaterial->whereDate('grn_verification.gv_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $grnMaterial->where('grn_verification.gv_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $grnMaterial->where('grn_verification.gv_date','<=',$to);

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
            // return $grnMaterial->grn_qty ? number_format((float)$grnMaterial->grn_qty, 3, '.','') : number_format((float) 0, 3, '.','');

            if($grnMaterial->secondary_item_name != ""){
                
                return $grnMaterial->grn_qty ? $grnMaterial->grn_qty : 0;
            }else{
                return $grnMaterial->grn_qty ? number_format((float)$grnMaterial->grn_qty, 3, '.','') : number_format((float) 0, 3, '.','');

            }
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
       ->addColumn('options',function($grnMaterial){
           $action = "<div>";        
            if(hasAccess("grn_verification","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
           return $action;
       })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'options','grn_date'])
        ->make(true);
    }
    

    public function store(Request $request){

        // dd($request->all());
        $year_data = getCurrentYearData();
            $locationCode = getCurrentLocation();

        DB::beginTransaction();

        try {

            $gv_data = json_decode($request->gv_data, true);

                foreach ($gv_data as $gv_details) {

                    $gv_data = GRNVerification::create([
                        'gv_date'                  => Date::createFromFormat('d/m/Y', $request->gv_date)->format('Y-m-d'),
                        'grn_details_id'           => $gv_details['grn_details_id'] ?? null,
                        'grn_secondary_details_id' => $gv_details['grn_secondary_details_id'] ?? null,
                        'item_details_id'          => $gv_details['item_details_id'] ?? null,
                        'item_id'                  => $gv_details['item_id'] ?? null,
                        'mismatch_qty'             => $gv_details['mismatch_qty'] ?? null,
                        'gv_reason'                => $gv_details['gv_reason'] ?? null,
                        'current_location_id'      => $locationCode->id,
                        'to_location_id'           => $gv_details['to_location_id'] ?? null,
                        'year_id'                  => $year_data->id,
                        'company_id'               => Auth::user()->company_id,
                        'created_by_user_id'       => Auth::user()->id,
                        'created_on'               => Carbon::now('Asia/Kolkata')->toDateTimeString(),

                    ]);

                       $mismatchQty = (float) $gv_details['mismatch_qty'];

                    

                        if (!empty($gv_details['item_details_id'])) {
                          

                            $mismatchQty = (float) $gv_details['mismatch_qty'];
                            // dd($gv_details['item_details_id']);

                            if ($mismatchQty > 0) {
                                // Positive mismatch
                                stockDetailsEffect($gv_details['to_location_id'],$gv_details['item_details_id'],$gv_details['item_details_id'],$mismatchQty,$mismatchQty,'add','U','GRN Verification',$gv_data->gv_id,'Yes','GRN Verification',$gv_data->gv_id);


                            } elseif ($mismatchQty < 0) {
                                $checkItem = LocationDetailStock::where('item_details_id',$gv_details['item_details_id'])->where('location_id',$gv_details['to_location_id'])->first();

                                if($checkItem == null){
                                    DB::rollBack();       
                                    return response()->json([
                                        'response_code' => '0',
                                        'response_message' => 'Insufficient Stock',
                                    ]);
                                }

                                // Negative mismatch
                                $positiveQty = number_format(abs($mismatchQty), 3, '.', '');

                                stockDetailsEffect($gv_details['to_location_id'],$gv_details['item_details_id'],$gv_details['item_details_id'],$positiveQty,$positiveQty,'add','D','GRN Verification',$gv_data->gv_id,'Yes','GRN Verification',$gv_data->gv_id);

                            }
                        }else{

                            if ($mismatchQty > 0) {
                                // Positive mismatch
                                stockEffect($gv_details['to_location_id'],$gv_details['item_id'],$gv_details['item_id'],$mismatchQty,$mismatchQty,'add','U','GRN Verification',$gv_data->gv_id);


                            } elseif ($mismatchQty < 0) {
                                $checkItem = LocationStock::where('item_id',$gv_details['item_id'])->where('location_id',$gv_details['to_location_id'])->first();

                                if($checkItem == null){
                                    DB::rollBack();       
                                    return response()->json([
                                        'response_code' => '0',
                                        'response_message' => 'Insufficient Stock',
                                    ]);
                                }


                                // Negative mismatch
                                $positiveQty = number_format(abs($mismatchQty), 3, '.', '');

                                stockEffect($gv_details['to_location_id'],$gv_details['item_id'],$gv_details['item_id'],$positiveQty,$positiveQty,'add','D','GRN Verification',$gv_data->gv_id);
                            }

                        }
                }
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Inserted Successfully.',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();            
            getActivityLogs("GRN Verification", "add", $e->getMessage(),$e->getLine());  
            if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Error Occured Record Not Inserted',
                    'original_error' => $e->getMessage()
                ]);
            }
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{

            // this cose use to stock maintain
            $locationCode = getCurrentLocation();

            $grn_verification_data = GRNVerification::where('gv_id',$request->id)->first();

            if($grn_verification_data != null){

                $mismatchQty = (float) $grn_verification_data->mismatch_qty;                    

                if ($grn_verification_data->item_details_id != null) {
                    
                    if ($mismatchQty > 0) {
                        // Positive mismatch
                        stockDetailsEffect($grn_verification_data->to_location_id,$grn_verification_data->item_details_id,$grn_verification_data->item_details_id,0,$mismatchQty,'delete','U','GRN Verification',$request->id,'Yes','GRN Verification',$request->id);


                    } elseif ($mismatchQty < 0) {                               

                        // Negative mismatch
                        $positiveQty = number_format(abs($mismatchQty), 3, '.', '');

                        stockDetailsEffect($grn_verification_data->to_location_id,$grn_verification_data->item_details_id,$grn_verification_data->item_details_id,0,$positiveQty,'delete','D','GRN Verification',$request->id,'Yes','GRN Verification',$request->id);

                    }
                }else{

                    if ($mismatchQty > 0) {
                        // Positive mismatch
                        stockEffect($grn_verification_data->to_location_id,$grn_verification_data->item_id,$grn_verification_data->item_id,0,$mismatchQty,'delete','U','GRN Verification',$request->id);


                    } elseif ($mismatchQty < 0) {                             


                        // Negative mismatch
                        $positiveQty = number_format(abs($mismatchQty), 3, '.', '');

                        stockEffect($grn_verification_data->to_location_id,$grn_verification_data->item_id,$grn_verification_data->item_id,0,$positiveQty,'delete','D','GRN Verification',$request->id);
                    }

                }
                
            }

            GRNVerification::destroy($request->id);

            DB::commit();

            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
             DB::rollBack();
            getActivityLogs("GRN Verification", "delete", $e->getMessage(),$e->getLine());  
             
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $error_msg,
                ]);
            }else if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }
          
        }
    }
}