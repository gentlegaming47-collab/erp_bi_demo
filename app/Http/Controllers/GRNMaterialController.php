<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GRNMaterialDetails;
use App\Models\GRNMaterailSecondaryDetails;
use App\Models\GRNMaterial;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\DispatchPlan;
use App\Models\DispatchPlanDetails;
use App\Models\LoadingEntryDetails;
use App\Models\LoadingEntrySecondaryDetails;
use App\Models\GRNVerification;
use App\Models\ItemDetails;
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
use App\Models\QCApproval;
use Illuminate\Support\Facades\Schema;
use Svg\Tag\Rect;

class GRNMaterialController extends Controller
{
    public function manage()
    {
        return view('manage.manage-grn');
    }

    public function create()
    {
        $getTransporter = Transporter::select('id','transporter_name') ->where('status', '=', 'active')->orderBy('transporter_name', 'asc')->get();
        return view('add.add-grn_details')->with(['getTransporter' =>$getTransporter]);
    }


    public function index(GRNMaterial $grnMaterial,Request $request,DataTables $dataTables)
    {

      
        if($request->PageName == 'manage-grn_location'){
            $grn_type_id_fix = ['3'];
        }else if($request->PageName == 'manage-grn_details'){
            $grn_type_id_fix = ['1','2'];
        }

        $locationCode = getCurrentLocation()->id;

        $year_data = getCurrentYearData();

        $grnMaterial = GRNMaterial::select(['grn_material_receipt.grn_id', 'grn_material_receipt.grn_sequence','grn_material_receipt.grn_type_value_fix','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','grn_material_receipt.grn_type_id_fix',
        DB::raw('SUM(material_receipt_grn_details.amount) as amount'),
       'locations.location_name',
       //  DB::raw('(CASE WHEN grn_type_value_fix ="From Location" THEN locations.location_name ELSE suppliers.supplier_name END) as name'),
        'grn_material_receipt.bill_no',
        'grn_material_receipt.bill_date',
        'grn_material_receipt.created_by_user_id','grn_material_receipt.created_on', 'grn_material_receipt.last_by_user_id','grn_material_receipt.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])

        ->leftJoin('suppliers','suppliers.id' ,'grn_material_receipt.supplier_id')
        ->leftJoin('locations','locations.id' ,'grn_material_receipt.to_location_id')
        ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_id' ,'grn_material_receipt.grn_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'grn_material_receipt.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'grn_material_receipt.last_by_user_id')
        ->where('grn_material_receipt.current_location_id', $locationCode)
        ->where('grn_material_receipt.year_id', '=', $year_data->id)
        ->whereIn('grn_material_receipt.grn_type_id_fix', $grn_type_id_fix)
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
      
        ->editColumn('created_by_user_id', function($grnMaterial){
            if($grnMaterial->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$grnMaterial->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($grnMaterial){
            if($grnMaterial->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$grnMaterial->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('created_on', function($grnMaterial){
            if ($grnMaterial->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $grnMaterial->created_on)->format(DATE_TIME_FORMAT);
                return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('grn_material_receipt.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
    
        ->editColumn('amount', function($grnMaterial){
            return $grnMaterial->amount > 0 ? number_format((float)$grnMaterial->amount, 3, '.','') : number_format((float) 0, 3, '.','');
       })
        ->editColumn('last_on', function($grnMaterial){
            if ($grnMaterial->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $grnMaterial->last_on)->format(DATE_TIME_FORMAT);
                 return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('grn_material_receipt.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($grnMaterial){
            $action = "<div>";
                
            if($grnMaterial->grn_type_id_fix == 3){
                if(hasAccess("grn_location","print")){
                $action .="<a id='print_a' target='_blank' href='".route('print-grn_location',['id' => base64_encode($grnMaterial->grn_id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
                }

                if(hasAccess("grn_location","edit")){
                $action .="<a id='edit_a' href='".route('edit-grn_location',['id' => base64_encode($grnMaterial->grn_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }

              

            }else{
                if(hasAccess("grn_details","print")){
                $action .="<a id='print_a' target='_blank' href='".route('print-grn_details',['id' => base64_encode($grnMaterial->grn_id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
                }

                if(hasAccess("grn_details","edit")){
                $action .="<a id='edit_a' href='".route('edit-grn_details',['id' => base64_encode($grnMaterial->grn_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }

            }    
            
            if(hasAccess("grn_details","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->editColumn('grn_date', function($grnMaterial){
            if ($grnMaterial->grn_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $grnMaterial->grn_date)->format(DATE_FORMAT); return $formatedDate2;
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

        ->filterColumn('grn_material_receipt.bill_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.bill_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->addColumn('name', function($grnMaterial) {
            return $grnMaterial->grn_type_value_fix == "From Location" ? $grnMaterial->location_name : $grnMaterial->supplier_name;
        })

        ->filterColumn('name', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('location_name', 'like', "%$keyword%")
                  ->orWhere('supplier_name', 'like', "%$keyword%");
            });
        })

        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'options','grn_date','name','bill_date'])
        ->make(true);
    }
    

    public function store(Request $request){
        // dd($request);

        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation();

        $existNumber = GRNMaterial::where('grn_number','=',$request->grn_number)->where('grn_sequence','=',$request->grn_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationCode->id)->lockForUpdate()->first(); 

        if($existNumber){
            $latestNo = $this->getLatestGrnNo($request);              
            $tmp =  $latestNo->getContent();
            $area = json_decode($tmp, true);
            $grn_number =   $area['latest_po_no'];
            $grn_sequence = $area['number'];
        }else{
           $grn_number = $request->grn_number;
           $grn_sequence = $request->grn_sequence;
        }       

        if($request->grn_type_fix_id == 1 )
            $grnType = "Agaist PO" ;
        elseif ($request->grn_type_fix_id == 2)
            $grnType = "Manual";
        elseif ($request->grn_type_fix_id == 3){
            $grnType = "From Location";
        }

        $totalQty = 0;
        $totalAmount = 0;

        foreach ($request->item_id as $ctKey => $ctVal) {
            if ($ctVal != null) {
                $totalQty += $request->grn_qty[$ctKey];
                $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";
            }
        }
        DB::beginTransaction();
        try{
            $grn_data=  GRNMaterial::create([
                'grn_type_id_fix'         => $request->grn_type_fix_id,
                'grn_type_value_fix'      => $grnType,
                'grn_sequence'            => $grn_sequence,
                'grn_number'              => $grn_number,
                'total_qty'               => $totalQty,
                'total_amount'            => $totalAmount,
                'grn_date'                => $request->grn_date  != '' ? Date::createFromFormat('d/m/Y', $request->grn_date)->format('Y-m-d') : null,
                'current_location_id'     => $locationCode->id,
                'supplier_id'             => $request->grn_supplier_id,
                'to_location_id'        => $request->grn_type_fix_id == "3" ? $request->location_id : null,
                'bill_no'                 => $request->challan_bill_no,
                'bill_date'               => $request->bill_date !='' ? Date::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d') : null,
                'transporter_id'          => $request->transporter,
                // 'vehicle_no'              => $request->grn_type_fix_id == 3 ? $request->vehicle_no : $request->vehicle,
                'vehicle_no'              => $request->vehicle,
                'lr_no_date'              => $request->lr_no_date,
                'special_notes'           => $request->sp_notes,
                'year_id'                 => $year_data->id,
                'company_id'              => Auth::user()->company_id,
                'created_by_user_id'      => Auth::user()->id,
                'created_on'              => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            ]);
            if ($grn_data->save()) {
                foreach ($request->item_id as $ctKey => $ctVal )
                {
                    if($request->grn_type_fix_id == '3'){
                        if ($ctVal != null) {                         

                            $mismatch_qty = floatval($request->loading_qty[$ctKey] ?? 0) - floatval($request->grn_qty[$ctKey] ?? 0);
                         
                            if($request->grn_details_type[$ctKey])
                           
                                $gen_material_details =  GRNMaterialDetails::create([
                                    'grn_id'        => $grn_data->grn_id,
                                    'po_details_id' => isset($request->po_details_id[$ctKey]) ? $request->po_details_id[$ctKey] : null,
                                    'item_id'       => $ctVal,
                                    'grn_qty'       => isset($request->grn_qty[$ctKey])       ? $request->grn_qty[$ctKey]       : null,
                                    'rate_per_unit' => isset($request->rate_unit[$ctKey])     ? $request->rate_unit[$ctKey]     : null,
                                    'amount'        => isset($request->amount[$ctKey])        ? $request->amount[$ctKey]        : null,
                                    'remarks'       => isset($request->remarks[$ctKey])       ? $request->remarks[$ctKey]       : null,
                                    'dc_details_id' => isset($request->dp_details_id[$ctKey]) ? $request->dp_details_id[$ctKey]  != 0 ? $request->dp_details_id[$ctKey] :null : null,                            
                                    'le_details_id' => isset($request->le_details_id[$ctKey]) ? $request->le_details_id[$ctKey]  != 0 ? $request->le_details_id[$ctKey] :null : null,                            
                                    'is_approved'  =>  null,   
                                    'qc_required'  => 'No' ,                            
                                    'service_item'  => 'No',                         
                                    // 'qc_required'  => isset($request->qc_required[$ctKey]) ? $request->qc_required[$ctKey] : 'No' ,                            
                                    // 'service_item'  =>  isset($request->service_item[$ctKey]) ? $request->service_item[$ctKey] : 'No',
                                    'mismatch_qty'  =>  $mismatch_qty,
                                    'grn_details_type'  =>  isset($request->grn_details_type[$ctKey])       ? $request->grn_details_type[$ctKey]       : null,
                                    'status'=> 'Y',
                                ]);

                                
                                if(empty($request->item_details_id[$ctKey]) && $request->item_details_id[$ctKey] == 0){    
                                      stockEffect($locationCode->id,$ctVal,$request->pre_item[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN',$gen_material_details->grn_details_id);                                
                                    // if(isset($request->qc_required[$ctKey]) &&  $request->qc_required[$ctKey] == 'No' &&  $request->service_item[$ctKey] == 'No'){
                                    //     stockEffect($locationCode->id,$ctVal,$request->pre_item[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN',$gen_material_details->grn_details_id);
                                    // }
                                }                           




                            if(!empty($request->item_details_id[$ctKey]) &&  $request->item_details_id[$ctKey] != 0 ){

                                $gen_material_secondary_details =  GRNMaterailSecondaryDetails::create([
                                    'grn_details_id'        => $gen_material_details->grn_details_id,
                                    'le_secondary_details_id' => isset($request->le_secondary_details_id[$ctKey]) ? $request->le_secondary_details_id[$ctKey] != 0 ? $request->le_secondary_details_id[$ctKey]  :null  : null,
                                    'dp_details_id'       => isset($request->dp_details_id[$ctKey]) ? $request->dp_details_id[$ctKey]  != 0 ? $request->dp_details_id[$ctKey] :null : null,         
                                    'le_details_id'       => isset($request->le_details_id[$ctKey]) ? $request->le_details_id[$ctKey]  != 0 ? $request->le_details_id[$ctKey] :null : null,                            
                                    'item_id' => $ctVal,
                                    'item_details_id'     => isset($request->item_details_id[$ctKey]) ? $request->item_details_id[$ctKey]   : null,
                                    'grn_qty'       => isset($request->grn_qty[$ctKey])       ? $request->grn_qty[$ctKey]   : null,
                                    'rate_per_unit' => isset($request->rate_unit[$ctKey])     ? $request->rate_unit[$ctKey]     : null,
                                    'amount'        => isset($request->amount[$ctKey])        ? $request->amount[$ctKey]        : null,
                                    'remarks'       => isset($request->remarks[$ctKey])       ? $request->remarks[$ctKey]       : null,
                                    'mismatch_qty' =>  $mismatch_qty,  
                                    'status'=> 'Y',                          
                                   
                                ]);

                                 stockDetailsEffect($locationCode->id,$request->item_details_id[$ctKey],$request->item_details_id[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN Secondary Details',$gen_material_secondary_details->grn_secondary_details_id,'Yes','GRN',$gen_material_details->grn_details_id );

                                // if(isset($request->qc_required[$ctKey]) &&  $request->qc_required[$ctKey] == 'No' &&  $request->service_item[$ctKey] == 'No'){
                                //     stockDetailsEffect($locationCode->id,$request->item_details_id[$ctKey],$request->item_details_id[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN Secondary Details',$gen_material_secondary_details->grn_secondary_details_id,'Yes','GRN',$gen_material_details->grn_details_id );
                                // }
                                
                            }
                          

                        }
                      

                    }else{
                            if ($ctVal != null) {

                                if($request->grn_type_fix_id == 1 ){
                                    $pend_po_qty = PurchaseOrderDetails::select(                
                                    DB::raw("(SELECT purchase_order_details.po_qty -  (SELECT IFNULL(SUM(psid.sc_qty),0) FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),        
                                    )                
                                    ->where('purchase_order_details.po_details_id','=',$request->po_details_id[$ctKey])                      
                                    ->pluck('pend_po_qty')->first();

                                    $pend_po_qty = number_format((float)$pend_po_qty, 3, '.','');
                                        
                                    
                                }

                                // $qc_required = Item::where('id',$ctVal)->pluck('qc_required')->first();
                                

                                $gen_material_details =  GRNMaterialDetails::create([
                                    'grn_id'        => $grn_data->grn_id,
                                    'po_details_id' => isset($request->po_details_id[$ctKey]) ? $request->po_details_id[$ctKey] : null,
                                    'item_id'       => $ctVal,
                                    'grn_qty'       => isset($request->grn_qty[$ctKey])       ? $request->grn_qty[$ctKey]       : null,
                                    'rate_per_unit' => isset($request->rate_unit[$ctKey])     ? $request->rate_unit[$ctKey]     : null,
                                    'amount'        => isset($request->amount[$ctKey])        ? $request->amount[$ctKey]        : null,
                                    'remarks'       => isset($request->remarks[$ctKey])       ? $request->remarks[$ctKey]       : null,
                                    'dc_details_id' => isset($request->dp_details_id[$ctKey]) ? $request->dp_details_id[$ctKey] : null,
                                    'le_details_id' => isset($request->le_details_id[$ctKey]) ? $request->le_details_id[$ctKey] : null,
                                    // 'is_approved'  => $request->grn_type_fix_id == 1  ?  $pend_po_qty < $request->grn_qty[$ctKey] ? 'N' : 'Y' : null,
                                    'is_approved'  => $request->grn_type_fix_id == 1  ?  $pend_po_qty < $request->grn_qty[$ctKey] ? 'N' : null : null,                            
                                    'qc_required'  => $request->grn_type_fix_id == 1  ? isset($request->qc_required[$ctKey]) ? $request->qc_required[$ctKey] : 'No' : 'No',                            
                                    'service_item'  => $request->grn_type_fix_id == 1  ? isset($request->service_item[$ctKey]) ? $request->service_item[$ctKey] : 'No' : 'No',
                                    'mismatch_qty'=> 0,
                                    'grn_details_type'=> null,
                                    'status'=> 'Y',
                                ]);

                                // increaseStockQty($locationCode->id,$ctVal,$request->grn_qty[$ctKey],0,'add');
                                if($request->grn_type_fix_id == 1 ){                        
                                    if(isset($request->qc_required[$ctKey]) &&  $request->qc_required[$ctKey] == 'No' &&  $request->service_item[$ctKey] == 'No'){
                                        stockEffect($locationCode->id,$ctVal,$request->pre_item[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN',$gen_material_details->grn_details_id);
                                    }
                                }
                                // else{
                                //     stockEffect($locationCode->id,$ctVal,$request->pre_item[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN',$gen_material_details->grn_details_id);
                                // }
                            }

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
                    'response_code' => '1',
                    'response_message' => 'Record Not Inserted.',
                ]);
            }
           
        }
        catch(\Exception $e){
// dd($e->getMessage(),$e->getLine());
            // DB::rollBack();
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => 'Error Occured Record Not Inserted',
            //     'original_error' => $e->getMessage()
            // ]);

            DB::rollBack();            
            getActivityLogs("GRN", "add", $e->getMessage(),$e->getLine());  
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


    public function show($id)
    {
        $tId = DB::table('grn_material_receipt')
        ->where('grn_id', base64_decode($id))
        ->value('transporter_id');

        $getTransporter = Transporter::select('transporters.id','transporters.transporter_name')
        ->where(function ($query) use ($tId) {
            $query->where('transporters.id', '=', $tId) 
            ->orWhere(function ($subQuery){
                $subQuery->where('transporters.status', '=', 'active');
            });
         
        })->orderBy('transporters.transporter_name', 'asc')->get();
        
        return view('edit.edit-grn_details')->with(['id'=>$id,'getTransporter' =>$getTransporter]);
    }


    public function edit(Request $request, $id)
    // public function edit(GRNMaterial $grnMaterial, $id)
    {
        $isAnyPartInUse = false;

        $location = getCurrentLocation();

        $grnMaterial = GRNMaterial::select('grn_material_receipt.grn_id','grn_material_receipt.grn_type_id_fix','grn_material_receipt.current_location_id','grn_material_receipt.grn_sequence','grn_material_receipt.grn_number','grn_material_receipt.grn_date','grn_material_receipt.supplier_id','grn_material_receipt.to_location_id','grn_material_receipt.bill_no','grn_material_receipt.bill_date','grn_material_receipt.transporter_id','grn_material_receipt.vehicle_no','grn_material_receipt.lr_no_date','grn_material_receipt.special_notes')->where('grn_id', $id)->first();
        //$grnMaterial = GRNMaterial::where('grn_id', $id)->first();


        $grnMaterial->grn_date = $grnMaterial->grn_date !='' ?  Date::createFromFormat('Y-m-d', $grnMaterial->grn_date)->format('d/m/Y') : '';

        $grnMaterial->bill_date = $grnMaterial->bill_date !='' ? Date::createFromFormat('Y-m-d', $grnMaterial->bill_date)->format('d/m/Y') : '';

        $grnMaterial->lr_date  = $grnMaterial->lr_date !='' ? Date::createFromFormat('Y-m-d', $grnMaterial->lr_date)->format('d/m/Y') : '';

        // $grnMaterialDetails = GRNMaterialDetails::select('material_receipt_grn_details.*', 'items.item_name', 'items.item_code','purchase_order.po_number','purchase_order.po_date','items.item_code' ,'units.unit_name',
        // DB::raw("(SELECT purchase_order_details.po_qty -  (SELECT IFNULL(SUM(psid.sc_qty),0) FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),     )
        // ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        // ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        // ->leftJoin('items', 'items.id', 'material_receipt_grn_details.item_id')
        // ->leftJoin('units','units.id','=','items.unit_id')
        // ->where('grn_id','=',$id)
        // ->get();

        if($grnMaterial->grn_type_id_fix  == 3){
            $grnMaterialDetails = GRNMaterialDetails::select('material_receipt_grn_details.*',
            'material_receipt_grn_details.dc_details_id as dp_details_id',
            'grn_secondary_details.grn_secondary_details_id',

            'dispatch_plan.dp_date',
            'loading_entry.vehicle_no',
            'item_groups.item_group_name', 'units.unit_name','items.item_name' ,'items.item_code','item_details.secondary_item_name','loading_entry_details.loading_qty','item_details.item_details_id','grn_secondary_details.le_secondary_details_id',
     
            DB::raw("CASE  WHEN grn_secondary_details.grn_secondary_details_id IS NOT NULL THEN grn_secondary_details.grn_qty
            ELSE material_receipt_grn_details.grn_qty  END as grn_qty"),

            DB::raw("CASE  WHEN grn_secondary_details.grn_secondary_details_id IS NOT NULL THEN grn_secondary_details.amount
            ELSE material_receipt_grn_details.amount  END as amount"),

            DB::raw("CASE  WHEN grn_secondary_details.grn_secondary_details_id IS NOT NULL THEN grn_secondary_details.rate_per_unit
            ELSE material_receipt_grn_details.rate_per_unit  END as rate_per_unit"),

            DB::raw("CASE  WHEN grn_secondary_details.grn_secondary_details_id IS NOT NULL THEN grn_secondary_details.remarks
            ELSE material_receipt_grn_details.remarks  END as remarks"),


            DB::raw("CASE  WHEN loading_entry.dp_number IS NOT NULL THEN loading_entry.dp_number 
            ELSE dispatch_plan.dp_number  END as dp_number"),

            
            DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),

          
            )
            ->leftJoin('grn_secondary_details','grn_secondary_details.grn_details_id','=','material_receipt_grn_details.grn_details_id')
            ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
            ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
            ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
            ->leftJoin('loading_entry','loading_entry.le_id','=','loading_entry_details.le_id')
            ->leftJoin('items','items.id','=','material_receipt_grn_details.item_id')
            ->leftJoin('item_details','item_details.item_details_id','=','grn_secondary_details.item_details_id')
            ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')       
            ->leftJoin('units','units.id','=','items.unit_id')
            ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')
            ->where('grn_id','=',$id)
            ->get();

        }else{
             // material_receipt_grn_details.*         all filed are rquired 
            $grnMaterialDetails = GRNMaterialDetails::select('material_receipt_grn_details.*', 'items.item_name', 'items.item_code','purchase_order.po_id','purchase_order.po_number','purchase_order.po_date','items.item_code' ,'units.unit_name', 'item_groups.item_group_name','items.secondary_unit','material_receipt_grn_details.dc_details_id as dp_details_id','purchase_order_details.po_qty',
            DB::raw("(SELECT purchase_order_details.po_qty -  (SELECT IFNULL(SUM(psid.sc_qty),0) FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"), 
            DB::raw("(SELECT dispatch_plan_details.plan_qty  - (SELECT IFNULL(SUM(gid.grn_qty),0)
            FROM material_receipt_grn_details AS gid WHERE gid.dc_details_id = dispatch_plan_details.dp_details_id )) as pend_plan_qty"),
            'location_stock.stock_qty','material_request.mr_number','material_request.mr_date', 'dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.plan_qty',
            // DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE material_receipt_grn_details.item_id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"), 
            )
            ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
            ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
            ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
            ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
            ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
            ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
            ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
            ->leftJoin('items', 'items.id', 'material_receipt_grn_details.item_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            // ->leftJoin('location_stock', 'location_stock.item_id', 'material_receipt_grn_details.item_id')
            ->leftJoin('units','units.id','=','items.unit_id')
            // ->where('location_stock.location_id','=',$location->id)
            ->leftJoin('location_stock', function ($join) use ($location) {
                $join->on('location_stock.item_id', '=', 'material_receipt_grn_details.item_id')
                    ->where('location_stock.location_id', '=', $location->id); 
            })
            ->where('grn_id','=',$id)
            ->get();

        }
        
       

        if ($grnMaterialDetails != null) {
            foreach ($grnMaterialDetails as $cpKey => $cpVal) {
                if ($cpVal->po_date != null) {
                    $cpVal->po_date = Date::createFromFormat('Y-m-d', $cpVal->po_date)->format('d/m/Y');
                }
                if ($cpVal->dp_date != null) {
                    $cpVal->dp_date = Date::createFromFormat('Y-m-d', $cpVal->dp_date)->format('d/m/Y');
                }
                if ($cpVal->mr_date != null) {
                    $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
                }
                  if($grnMaterial->grn_type_id_fix  == 3){
                   $cpVal->item_detail = ItemDetails::select('item_details.item_details_id','item_details.secondary_item_name')->where('item_id',$cpVal->item_id)->get();

                   if($cpVal->le_secondary_details_id != null){
                        // $cpVal->plan_qty = LoadingEntrySecondaryDetails::where('le_secondary_details_id',$cpVal->le_secondary_details_id)->sum('plan_qty');

                         $plan_qty = LoadingEntrySecondaryDetails::where('le_secondary_details_id',$cpVal->le_secondary_details_id)->sum('plan_qty');

                         $cpVal->plan_qty  = $plan_qty;
                         $cpVal->loading_qty  = $plan_qty;
                   }
                  }
               
            }
        }


            if($grnMaterialDetails != null){
                $grnMaterialDetails = $grnMaterialDetails->filter(function ($item) use (&$isAnyPartInUse,&$grnMaterial) {
                    // if($item->pend_po_qty != null){
                        $item->org_pend_qty = $item->pend_po_qty ;

                        $item->pend_po_qty = $item->pend_po_qty > 0 ? $item->pend_po_qty : 0;

                        // $item->org_pend_qty = $item->pend_po_qty > 0 ?  $item->pend_po_qty+$item->grn_qty : 0;
                        
                    // }

                    if($grnMaterial->grn_date != null){
                        $date = Date::createFromFormat('d/m/Y', $grnMaterial->grn_date)->format('Y-m-d');
                    }

                   
                   

                     if($grnMaterial->grn_type_id_fix  == 3){                     

                        $isGRNVarificationFound = GRNVerification::where('grn_details_id',$item->grn_details_id)->where('grn_secondary_details_id',$item->grn_secondary_details_id)->get();

                        $isFound = QCApproval::where('qc_approval.grn_details_id',$item->grn_details_id)->sum('qc_qty');

                        if ($isFound) {
                            $item->used_qty = $isFound;
                        } else {
                            $item->used_qty = 0;
                        }
                        
                        if ($isFound || $isGRNVarificationFound->isNotEmpty() || LiveUpdateSecDate($date,$item->item_id)) {
                            $item->in_use = true;
                            $isAnyPartInUse = true;
                        } else {
                            $item->in_use = false;
                        }
                     }else{

                        $newRequest = new Request();

                        $newRequest->po_details_id = $item->po_details_id;
                        $newRequest->record_id = $item->grn_details_id;
                        $newRequest->total_qty = $item->po_qty;

                        $item->show_pend_qty = self::getPendingQty($newRequest);

                        $isFound = QCApproval::where('qc_approval.grn_details_id',$item->grn_details_id)->sum('qc_qty');
                        
                        if($isFound || $item->secondary_unit == 'Yes' || LiveUpdateSecDate($date,$item->item_id)){
                            $item->in_use = true;
                            $isAnyPartInUse = true;
                            $item->used_qty = $isFound;

                            if($item->secondary_unit == 'Yes'){
                                $item->second_unit_in_use = true;
                                  $isAnyPartInUse = true;
                            }else{
                                $item->second_unit_in_use = false;
                            }
                        }else{
                            $item->in_use = false;
                            $item->used_qty = 0;
                        }
                        
                     }                 




                    return $item;

                })->values();
            }

            
        if($grnMaterial){
            $grnMaterial->in_use = false;
            if($isAnyPartInUse == true){
                $grnMaterial->in_use = true;
            }
        }



        if ($grnMaterialDetails) {
            return response()->json([
                'grnMaterialDetails' => $grnMaterialDetails,
                'grnMaterial' => $grnMaterial,
                'response_code' => '1',
                'response_message' => '',
            ]);
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }


    public function update(Request $request)
    {   
        // dd($request);
        $year_data = getCurrentYearData();
            $locationCode = getCurrentLocation();

            if($request->grn_type_fix_id == 1 )
                $grnType = "Agaist PO" ;
            elseif ($request->grn_type_fix_id == 2)
                $grnType = "Manual";
            elseif ($request->grn_type_fix_id == 3){
                $grnType = "From Location";
            }

            $totalQty = 0;
            $totalAmount = 0;            
            foreach ($request->item_id as $ctKey => $ctVal) {
                if ($ctVal != null) {
                    $totalQty += $request->grn_qty[$ctKey];
                    $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";
                }
            }
            DB::beginTransaction();

            try{
                $grn_data=  GRNMaterial::where('grn_id','=',$request->id)->update([
                    'grn_type_id_fix'         => $request->grn_type_fix_id,
                    'grn_type_value_fix'      => $grnType,
                    'grn_sequence'            => $request->grn_sequence,
                    'grn_number'              => $request->grn_number,
                    'total_qty'               => $totalQty,
                    'total_amount'            => $totalAmount,
                    'grn_date'                => $request->grn_date  != '' ? Date::createFromFormat('d/m/Y', $request->grn_date)->format('Y-m-d') : null,
                    'current_location_id'     => $locationCode->id,
                    'supplier_id'             => $request->grn_supplier_id,
                    'to_location_id'        => $request->grn_type_fix_id == "3" ? $request->location_id : null,
                    'bill_no'                 => $request->challan_bill_no,
                    'bill_date'               => $request->bill_date !='' ? Date::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d') : null,
                    'transporter_id'          => $request->transporter,
                    // 'vehicle_no'              => $request->grn_type_fix_id == 3 ? $request->vehicle_no : $request->vehicle,
                    'vehicle_no'              => $request->vehicle,
                    'lr_no_date'              => $request->lr_no_date,
                    'special_notes'           => $request->sp_notes,
                    'year_id'                 => $year_data->id,
                    'company_id'              => Auth::user()->company_id,
                    'last_by_user_id'      => Auth::user()->id,
                    'last_on'              => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                ]);

                    if($grn_data)
                    {

                        if($request->grn_type_fix_id == 3 ){
                            if (isset($request->grn_details_id) && !empty($request->grn_details_id)) {
                                $editplanDetails =  GRNMaterialDetails::where('grn_id',$request->id)->update(['status' => 'D', ]);
        
                                $editgrnSecondDetails =  GRNMaterialDetails::where('grn_id',$request->id)->get();
                                if($editgrnSecondDetails != null){
                                    foreach($editgrnSecondDetails as $okey => $oval){
                                        $pdpDetails = GRNMaterailSecondaryDetails::where('grn_details_id',$oval->grn_details_id)->update([
                                            'status' => 'D',
                                        ]);

                                    }
                                }

                                if (isset($request->grn_details_id) && !empty($request->grn_details_id)) {
                                    foreach ($request->grn_details_id as $ctKey => $ctVal) { 

                                        $mismatch_qty = floatval($request->loading_qty[$ctKey] ?? 0) - floatval($request->grn_qty[$ctKey] ?? 0);                             
                                          
                                        if($ctVal == "0"){    

                                          
                                            $gen_material_details =  GRNMaterialDetails::create([
                                                'grn_id'        => $request->id,
                                                'po_details_id' => isset($request->po_details_id[$ctKey]) ? $request->po_details_id[$ctKey] : null,
                                                'item_id' => isset($request->item_id[$ctKey]) ? $request->item_id[$ctKey]   : null,
                                                'grn_qty'       => isset($request->grn_qty[$ctKey])       ? $request->grn_qty[$ctKey]       : null,
                                                'rate_per_unit' => isset($request->rate_unit[$ctKey])     ? $request->rate_unit[$ctKey]     : null,
                                                'amount'        => isset($request->amount[$ctKey])        ? $request->amount[$ctKey]        : null,
                                                'remarks'       => isset($request->remarks[$ctKey])       ? $request->remarks[$ctKey]       : null,
                                                'dc_details_id' => isset($request->dp_details_id[$ctKey]) ? $request->dp_details_id[$ctKey]  != 0 ? $request->dp_details_id[$ctKey] :null : null,                            
                                                'le_details_id' => isset($request->le_details_id[$ctKey]) ? $request->le_details_id[$ctKey]  != 0 ? $request->le_details_id[$ctKey] :null : null,                            
                                                'is_approved'  =>  null,    
                                                'qc_required'  => 'No' ,                            
                                                'service_item'  => 'No',                        
                                                // 'qc_required'  => isset($request->qc_required[$ctKey]) ? $request->qc_required[$ctKey] : 'No' ,                            
                                                // 'service_item'  => isset($request->service_item[$ctKey]) ? $request->service_item[$ctKey] : 'No' ,
                                                'mismatch_qty'  =>  $mismatch_qty,
                                                'grn_details_type'  =>  isset($request->grn_details_type[$ctKey])       ? $request->grn_details_type[$ctKey]       : null,
                                                'status'=> 'Y',
                                            ]);

                                            if(empty($request->item_details_id[$ctKey]) && $request->item_details_id[$ctKey] == 0){
                                                stockEffect($locationCode->id,$request->item_id[$ctKey],$request->item_id[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN',$gen_material_details->grn_details_id);
                                                
                                                // if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                //     stockEffect($locationCode->id,$request->item_id[$ctKey],$request->item_id[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN',$gen_material_details->grn_details_id);
                                                // }
                                            }


                                            $second_grndetail_id =  $gen_material_details->grn_details_id;
                                        




                                            if(!empty($request->item_details_id[$ctKey]) &&  $request->item_details_id[$ctKey] != 0 ){

                                                $gen_material_secondary_details =  GRNMaterailSecondaryDetails::create([
                                                    'grn_details_id'        => $second_grndetail_id,
                                                    'le_secondary_details_id' => isset($request->le_secondary_details_id[$ctKey]) ? $request->le_secondary_details_id[$ctKey] != 0 ? $request->le_secondary_details_id[$ctKey]  :null  : null,
                                                    'dp_details_id'       => isset($request->dp_details_id[$ctKey]) ? $request->dp_details_id[$ctKey]  != 0 ? $request->dp_details_id[$ctKey] :null : null,         
                                                    'le_details_id'       => isset($request->le_details_id[$ctKey]) ? $request->le_details_id[$ctKey]  != 0 ? $request->le_details_id[$ctKey] :null : null,                            
                                                    'item_id' => isset($request->item_id[$ctKey]) ? $request->item_id[$ctKey]   : null,
                                                    'item_details_id'     => isset($request->item_details_id[$ctKey]) ? $request->item_details_id[$ctKey]   : null,
                                                    'grn_qty'       => isset($request->grn_qty[$ctKey])       ? $request->grn_qty[$ctKey]   : null,
                                                    'rate_per_unit' => isset($request->rate_unit[$ctKey])     ? $request->rate_unit[$ctKey]     : null,
                                                    'amount'        => isset($request->amount[$ctKey])        ? $request->amount[$ctKey]        : null,
                                                    'remarks'       => isset($request->remarks[$ctKey])       ? $request->remarks[$ctKey]       : null,
                                                    'mismatch_qty' =>  $mismatch_qty,  
                                                    'status'=> 'Y',                          
                                                
                                                ]);

                                                 stockDetailsEffect($locationCode->id,$request->item_details_id[$ctKey],$request->item_details_id[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN Secondary Details',$gen_material_secondary_details->grn_secondary_details_id,'Yes', 'GRN',$second_grndetail_id);

                                                // if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                //  stockDetailsEffect($locationCode->id,$request->item_details_id[$ctKey],$request->item_details_id[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN Secondary Details',$gen_material_secondary_details->grn_secondary_details_id,'Yes', 'GRN',$second_grndetail_id);
                                                // }
                                                
                                            }
                          

                                        }else{
                                                     
                                                  $pre_data = GRNMaterialDetails::select('qc_required','service_item')->where('grn_details_id',$ctVal)->first();                                       
                                                    $gen_material_details =  GRNMaterialDetails::where('grn_details_id',$ctVal)->update([
                                                        'grn_id'        => $request->id,
                                                        'po_details_id' => isset($request->po_details_id[$ctKey]) ? $request->po_details_id[$ctKey] : null,
                                                        'item_id' => isset($request->item_id[$ctKey]) ? $request->item_id[$ctKey]   : null,
                                                        'grn_qty'       => isset($request->grn_qty[$ctKey])       ? $request->grn_qty[$ctKey]       : null,
                                                        'rate_per_unit' => isset($request->rate_unit[$ctKey])     ? $request->rate_unit[$ctKey]     : null,
                                                        'amount'        => isset($request->amount[$ctKey])        ? $request->amount[$ctKey]        : null,
                                                        'remarks'       => isset($request->remarks[$ctKey])       ? $request->remarks[$ctKey]       : null,
                                                        'dc_details_id' => isset($request->dp_details_id[$ctKey]) ? $request->dp_details_id[$ctKey]  != 0 ? $request->dp_details_id[$ctKey] :null : null,                            
                                                        'le_details_id' => isset($request->le_details_id[$ctKey]) ? $request->le_details_id[$ctKey]  != 0 ? $request->le_details_id[$ctKey] :null : null,                            
                                                        'is_approved'  =>  null,   
                                                        'qc_required'  => 'No',                            
                                                        'service_item'  => 'No',                         
                                                        // 'qc_required'  => isset($request->qc_required[$ctKey]) ? $request->qc_required[$ctKey] : 'No',                            
                                                        // 'service_item'  => isset($request->service_item[$ctKey]) ? $request->service_item[$ctKey] : 'No', 
                                                        'mismatch_qty'  =>  $mismatch_qty,
                                                        'grn_details_type'  =>  isset($request->grn_details_type[$ctKey])       ? $request->grn_details_type[$ctKey]       : null,
                                                        'status'=> 'Y',
                                                    ]);


                                                    if(empty($request->item_details_id[$ctKey]) && $request->item_details_id[$ctKey] == 0){

                                                        if($request->pre_item_detail[$ctKey] != 0){
                                                        //    dd($request);
                                                            $pre_details_qty = ItemDetails::where('item_details_id',$request->pre_item_detail[$ctKey])->sum('secondary_qty');
                                                            $pre_details_qty = number_format((float)$pre_details_qty, 3, '.','');


                                                            $org_grn_qty = $request->org_grn_qty[$ctKey] * $pre_details_qty;  
                                                       

                                                            stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$request->grn_qty[$ctKey] ,$org_grn_qty,'edit','U','GRN',$ctVal);
                                                        }else{
                                                         ;
                                                             stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$request->grn_qty[$ctKey] , $request->org_grn_qty[$ctKey],'edit','U','GRN',$ctVal);

                                                        }

                                                      
                                                    }else{

                                                        $pre_details_qty = ItemDetails::where('item_details_id',$request->pre_item_detail[$ctKey])->sum('secondary_qty');
                                                        $pre_details_qty = number_format((float)$pre_details_qty, 3, '.','');


                                                        $org_grn_qty = $request->org_grn_qty[$ctKey] * $pre_details_qty;  

                                                       

                                                        $cue_details_qty = ItemDetails::where('item_details_id',$request->item_details_id[$ctKey])->sum('secondary_qty');
                                                        $cue_details_qty = number_format((float)$cue_details_qty, 3, '.','');


                                                        $grn_qty = $request->grn_qty[$ctKey] * $cue_details_qty;  

                                                        if($request->pre_item_detail[$ctKey] == 0){
                                                           
                                                          stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$grn_qty , $request->org_grn_qty[$ctKey],'edit','U','GRN',$ctVal);

                                                        }else{
                                                            
                                                         stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$grn_qty, $org_grn_qty,'edit','U','GRN',$ctVal);

                                                        }
                                                    


                                                        
                                                    }

                                                  

                                                    // $pre_qc_required  =  $pre_data->qc_required;
                                                    // $pre_service_item =  $pre_data->service_item;
                                               
                                                 
                                                    //     if(empty($request->item_details_id[$ctKey]) && $request->item_details_id[$ctKey] == 0){

                                                    //         if($request->pre_item_detail[$ctKey] != 0){
                                                    //         //    dd($request);
                                                    //             $pre_details_qty = ItemDetails::where('item_details_id',$request->pre_item_detail[$ctKey])->sum('secondary_qty');
                                                    //             $pre_details_qty = number_format((float)$pre_details_qty, 3, '.','');


                                                    //             $org_grn_qty = $request->org_grn_qty[$ctKey] * $pre_details_qty;  

                                                    //             // below use to  service , qc_require Pre Item and current Item is same
                                                    //             if($pre_qc_required == $request->qc_required[$ctKey] && $pre_service_item == $request->service_item[$ctKey]){
                                                        
                                                    //                 if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //                 stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$request->grn_qty[$ctKey] ,$org_grn_qty,'edit','U','GRN',$ctVal);
                                                    //                 }
                                                    //             }else{
                                                    //                  // below use to service , qc_require  Pre Item  and current Item is diffrent  
                                                    //                 if($pre_qc_required == 'No' && $pre_service_item == 'No'){
                                                    //                   stockEffect($locationCode->id,$request->pre_item[$ctKey],$request->pre_item[$ctKey],0, $org_grn_qty,'delete','U','GRN',$ctVal);

                                                    //                 }

                                                    //                 if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //                   stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$request->grn_qty[$ctKey] ,0,'add','U','GRN',$ctVal);
                                                    //                 }
                                                    //             }

                                                    //         }else{
                                                    //              // below use to  service , qc_require Pre Item and current Item is same
                                                    //             if($pre_qc_required == $request->qc_required[$ctKey] && $pre_service_item == $request->service_item[$ctKey]){
                                                    //                 if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //                     stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$request->grn_qty[$ctKey] , $request->org_grn_qty[$ctKey],'edit','U','GRN',$ctVal);
                                                    //                 }
                                                    //             }else{
                                                    //                  // below use to service , qc_require  Pre Item  and current Item is diffrent  
                                                    //                 if($pre_qc_required == 'No' && $pre_service_item == 'No'){
                                                    //                   stockEffect($locationCode->id,$request->pre_item[$ctKey],$request->pre_item[$ctKey],0, $request->org_grn_qty[$ctKey],'delete','U','GRN',$ctVal);
                                                    //                 }

                                                    //                 if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //                   stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$request->grn_qty[$ctKey] ,0,'add','U','GRN',$ctVal);
                                                    //                 }

                                                    //             }

                                                    //         }

                                                      
                                                    //     }else{

                                                    //         $pre_details_qty = ItemDetails::where('item_details_id',$request->pre_item_detail[$ctKey])->sum('secondary_qty');
                                                    //         $pre_details_qty = number_format((float)$pre_details_qty, 3, '.','');


                                                    //         $org_grn_qty = $request->org_grn_qty[$ctKey] * $pre_details_qty;  

                                                        

                                                    //         $cue_details_qty = ItemDetails::where('item_details_id',$request->item_details_id[$ctKey])->sum('secondary_qty');
                                                    //         $cue_details_qty = number_format((float)$cue_details_qty, 3, '.','');


                                                    //         $grn_qty = $request->grn_qty[$ctKey] * $cue_details_qty;  

                                                    //         if($request->pre_item_detail[$ctKey] == 0){
                                                            
                                                    //             if($pre_qc_required == $request->qc_required[$ctKey] && $pre_service_item == $request->service_item[$ctKey]){
                                                            
                                                    //                 if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //                     stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$grn_qty , $request->org_grn_qty[$ctKey],'edit','U','GRN',$ctVal);
                                                    //                 }
                                                    //             }else{
                                                    //                 if($pre_qc_required == 'No' && $pre_service_item == 'No'){
                                                    //                 stockEffect($locationCode->id,$request->pre_item[$ctKey],$request->pre_item[$ctKey],0, $request->org_grn_qty[$ctKey],'delete','U','GRN',$ctVal);
                                                    //                 }

                                                    //                 if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //                 stockEffect($locationCode->id,$request->item_id[$ctKey],$request->item_id[$ctKey],$grn_qty ,0,'add','U','GRN',$ctVal);
                                                    //                 }

                                                    //             }

                                                    //         }else{
                                                    //             if($pre_qc_required == $request->qc_required[$ctKey] && $pre_service_item == $request->service_item[$ctKey]){
                                                    //                 if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //                     stockEffect($locationCode->id,$request->item_id[$ctKey],$request->pre_item[$ctKey],$grn_qty, $org_grn_qty,'edit','U','GRN',$ctVal);
                                                    //                 }
                                                    //             }else{
                                                    //                 if($pre_qc_required == 'No' && $pre_service_item == 'No'){
                                                    //                   stockEffect($locationCode->id,$request->pre_item[$ctKey],$request->pre_item[$ctKey],0, $org_grn_qty,'delete','U','GRN',$ctVal);

                                                    //                 }

                                                    //                 if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //                   stockEffect($locationCode->id,$request->item_id[$ctKey],$request->item_id[$ctKey],$grn_qty ,0,'add','U','GRN',$ctVal);
                                                    //                 }
                                                    //             }

                                                    //         }
                                                            
                                                    //     }                                           
                                               
                                                

                                                if(!empty($request->item_details_id[$ctKey]) &&  $request->item_details_id[$ctKey] != 0 &&  $request->grn_secondary_details_id[$ctKey] != 0){

                                                    $gen_material_secondary_details =  GRNMaterailSecondaryDetails::where('grn_secondary_details_id',$request->grn_secondary_details_id[$ctKey])->update([
                                                        'grn_details_id'        => $ctVal,
                                                        'le_secondary_details_id' => isset($request->le_secondary_details_id[$ctKey]) ? $request->le_secondary_details_id[$ctKey] != 0 ? $request->le_secondary_details_id[$ctKey]  :null  : null,
                                                        'dp_details_id'       => isset($request->dp_details_id[$ctKey]) ? $request->dp_details_id[$ctKey]  != 0 ? $request->dp_details_id[$ctKey] :null : null,         
                                                        'le_details_id'       => isset($request->le_details_id[$ctKey]) ? $request->le_details_id[$ctKey]  != 0 ? $request->le_details_id[$ctKey] :null : null,                            
                                                        'item_id' => isset($request->item_id[$ctKey]) ? $request->item_id[$ctKey]   : null,
                                                        'item_details_id'     => isset($request->item_details_id[$ctKey]) ? $request->item_details_id[$ctKey]   : null,
                                                        'grn_qty'       => isset($request->grn_qty[$ctKey])       ? $request->grn_qty[$ctKey]   : null,
                                                        'rate_per_unit' => isset($request->rate_unit[$ctKey])     ? $request->rate_unit[$ctKey]     : null,
                                                        'amount'        => isset($request->amount[$ctKey])        ? $request->amount[$ctKey]        : null,
                                                        'remarks'       => isset($request->remarks[$ctKey])       ? $request->remarks[$ctKey]       : null,
                                                        'mismatch_qty' =>  $mismatch_qty,  
                                                        'status'=> 'Y',                          
                                                    
                                                    ]);

                                                     stockDetailsEffect($locationCode->id,$request->item_details_id[$ctKey],$request->pre_item_detail[$ctKey],$request->grn_qty[$ctKey],$request->org_grn_qty[$ctKey],'edit','U','GRN Secondary Details',$request->grn_secondary_details_id[$ctKey], 'No', 'GRN',$ctVal);

                                                    // if($pre_qc_required == $request->qc_required[$ctKey] && $pre_service_item == $request->service_item[$ctKey]){

                                                    //     if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //         stockDetailsEffect($locationCode->id,$request->item_details_id[$ctKey],$request->pre_item_detail[$ctKey],$request->grn_qty[$ctKey],$request->org_grn_qty[$ctKey],'edit','U','GRN Secondary Details',$request->grn_secondary_details_id[$ctKey], 'No', 'GRN',$ctVal);
                                                    //     }
                                                    // }else{
                                                    //     if($pre_qc_required == 'No' && $pre_service_item == 'No'){
                                                    //         stockDetailsEffect($locationCode->id,$request->pre_item_detail[$ctKey],$request->pre_item_detail[$ctKey],0,$request->org_grn_qty[$ctKey],'delete','U','GRN Secondary Details',$dval->grn_secondary_details_id,'No', 'GRN',$ctVal);
                                                    //     }

                                                    //     if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                    //       stockDetailsEffect($locationCode->id,$request->item_details_id[$ctKey],$request->item_details_id[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN Secondary Details',$request->grn_secondary_details_id[$ctKey],'No', 'GRN',$ctVal);
                                                    //     }

                                                    // }

                                                    
                                                }else{                                                   
                                                    if($request->item_details_id[$ctKey] != 0){
                                                         $gen_material_secondary_details =  GRNMaterailSecondaryDetails::create([
                                                        'grn_details_id'        => $ctVal,
                                                        'le_secondary_details_id' => isset($request->le_secondary_details_id[$ctKey]) ? $request->le_secondary_details_id[$ctKey] != 0 ? $request->le_secondary_details_id[$ctKey]  :null  : null,
                                                        'dp_details_id'       => isset($request->dp_details_id[$ctKey]) ? $request->dp_details_id[$ctKey]  != 0 ? $request->dp_details_id[$ctKey] :null : null,         
                                                        'le_details_id'       => isset($request->le_details_id[$ctKey]) ? $request->le_details_id[$ctKey]  != 0 ? $request->le_details_id[$ctKey] :null : null,                            
                                                        'item_id' => isset($request->item_id[$ctKey]) ? $request->item_id[$ctKey]   : null,
                                                        'item_details_id'     => isset($request->item_details_id[$ctKey]) ? $request->item_details_id[$ctKey]   : null,
                                                        'grn_qty'       => isset($request->grn_qty[$ctKey])       ? $request->grn_qty[$ctKey]   : null,
                                                        'rate_per_unit' => isset($request->rate_unit[$ctKey])     ? $request->rate_unit[$ctKey]     : null,
                                                        'amount'        => isset($request->amount[$ctKey])        ? $request->amount[$ctKey]        : null,
                                                        'remarks'       => isset($request->remarks[$ctKey])       ? $request->remarks[$ctKey]       : null,
                                                        'mismatch_qty' =>  $mismatch_qty,  
                                                        'status'=> 'Y',                            
                                                
                                                        ]);

                                                         stockDetailsEffect($locationCode->id,$request->item_details_id[$ctKey],$request->item_details_id[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN Secondary Details',$gen_material_secondary_details->grn_secondary_details_id,'No', 'GRN',$ctVal);

                                                        // if(isset($request->qc_required[$ctKey]) && $request->qc_required[$ctKey] == 'No' && $request->service_item[$ctKey] == 'No'){
                                                        //   stockDetailsEffect($locationCode->id,$request->item_details_id[$ctKey],$request->item_details_id[$ctKey],$request->grn_qty[$ctKey],0,'add','U','GRN Secondary Details',$gen_material_secondary_details->grn_secondary_details_id,'No', 'GRN',$ctVal);
                                                        // }

                                                    }
                                                   
                                                }  
                                                
                                                
                                                $DeleteGrnSecondDetails = GRNMaterailSecondaryDetails::where('grn_details_id','=',$ctVal)->where('status', 'D')->get();

                                        
                                                if(!$DeleteGrnSecondDetails->isEmpty()){
                                                    foreach($DeleteGrnSecondDetails as $dkey=>$dval){    

                                                         stockDetailsEffect($locationCode->id,$dval->item_details_id,$dval->item_details_id,0,$dval->grn_qty,'delete','U','GRN Secondary Details',$dval->grn_secondary_details_id,'No', 'GRN',$ctVal);
                                                         
                                                        // if($pre_qc_required == 'No' && $pre_service_item == 'No'){                    
                                                        // // if($uval['qc_required'] == 'No' && $uval['service_item'] == 'No'){                    
                                                        // stockDetailsEffect($locationCode->id,$dval->item_details_id,$dval->item_details_id,0,$dval->grn_qty,'delete','U','GRN Secondary Details',$dval->grn_secondary_details_id,'No', 'GRN',$ctVal);
                                                        // }

                                                        GRNMaterailSecondaryDetails::where('grn_secondary_details_id','=',$dval->grn_secondary_details_id)->delete();
                                                    }                       

                                                }

                                               
                                        }
                                        
                                    }

                                    // $updateGrnDetailsData =  GRNMaterialDetails::where('grn_id',$request->id)->where('status', 'Y')->get();  
                                  
                                    // foreach($updateGrnDetailsData as $ukey=>$uval){ 
                                                
                                    //     $checkSecond =  GRNMaterailSecondaryDetails::where('grn_details_id','=',$uval['grn_details_id'])->first();

                                    //     if(!empty($checkSecond)){
                                         
                                    //         $sumQty = GRNMaterailSecondaryDetails::where('grn_details_id','=',$uval['grn_details_id'])->sum('grn_qty');
                                            
                                    //         if($uval['dc_details_id'] != null){
                                    //             // dd($sumQty,'if');
                                    //             $dcQtySum = LoadingEntryDetails::where('dp_details_id',$uval['dc_details_id'])->sum('loading_qty');

                                    //             $grnQty = $dcQtySum - $sumQty; 
                                    //             $rate_unit = floatval($checkSecond->rate_per_unit ?? 0);
                                    //             $amount = $sumQty * $rate_unit;

                                    //             $upGrnData = GRNMaterialDetails::where('grn_details_id',$uval['grn_details_id'])->update([
                                    //                 'grn_qty'  => $sumQty,
                                    //                 'remarks'  => null,
                                    //                 'rate_per_unit' => $rate_unit,
                                    //                 'amount' => $amount,
                                    //                 'mismatch_qty' => $grnQty,
                                    //             ]);

                                    //         }else{
                                    //             $grnQty = 0 - $sumQty;
                                    //             $rate_unit = $uval['rate_per_unit'];
                                    //             $amount = $uval['amount'];

                                    //              $upGrnData = GRNMaterialDetails::where('grn_details_id',$uval['grn_details_id'])->update([
                                    //                 'grn_qty'  => $sumQty,
                                    //                 'remarks'  => null,
                                    //                 'rate_per_unit' => $rate_unit,
                                    //                 'amount' => $amount,
                                    //                 'mismatch_qty' => $grnQty,
                                    //             ]);
                                                
                                    //         }

                                        

                                    //     }
                                        
                                       
                                    // }

                                    $oldGrnDetailsData =  GRNMaterialDetails::where('grn_id',$request->id)->where('status', 'D')->get();  
                                    // dd($oldGrnDetailsData);
                                    foreach($oldGrnDetailsData as $gkey=>$gval){    
                                        
                                        $oldGrnSecondDetails = GRNMaterailSecondaryDetails::where('grn_details_id','=',$gval['grn_details_id'])->get();

                                        if(!$oldGrnSecondDetails->isEmpty()){
                                            foreach($oldGrnSecondDetails as $okey=>$oval){  
                                                // if($gval['qc_required'] == 'No' && $gval['service_item'] == 'No'){                      
                                                //  stockDetailsEffect($locationCode->id,$oval->item_details_id,$oval->item_details_id,0,$oval->grn_qty,'delete','U','GRN Secondary Details',$oval->grn_secondary_details_id, 'Yes', 'GRN',$gval['grn_details_id']);
                                                // }

                                                stockDetailsEffect($locationCode->id,$oval->item_details_id,$oval->item_details_id,0,$oval->grn_qty,'delete','U','GRN Secondary Details',$oval->grn_secondary_details_id, 'Yes', 'GRN',$gval['grn_details_id']);

                                                GRNMaterailSecondaryDetails::where('grn_secondary_details_id','=',$oval->grn_secondary_details_id)->delete();
                                            }                       

                                        }else{
                                            // if($gval['qc_required'] == 'No' && $gval['service_item'] == 'No'){
                                            //  stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0, $gval['grn_qty'],'delete','U','GRN',$gval['grn_details_id']);
                                            // }

                                            stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0, $gval['grn_qty'],'delete','U','GRN',$gval['grn_details_id']);
                                        }
                                      
                                     
                                        
                                    }

                                    $deleteGrnDetailsData =  GRNMaterialDetails::where('grn_id',$request->id)->where('status', 'D')->delete();  
                                }
                            }


                        }else{
                            if (isset($request->grn_details_id) && !empty($request->grn_details_id)) {
                                $editplanDetails =  GRNMaterialDetails::where('grn_id',$request->id)->update(['status' => 'D', ]);                                      
                                if (isset($request->grn_details_id) && !empty($request->grn_details_id)) {
                                    foreach ($request->grn_details_id as $sodKey => $sodVal) { 
                                        
                                        if($request->grn_type_fix_id == 1 && isset($request->po_details_id[$sodKey])){
                                            $pend_po_qty = PurchaseOrderDetails::select(                
                                            DB::raw("(SELECT purchase_order_details.po_qty -  (SELECT IFNULL(SUM(psid.sc_qty),0) FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),        
                                            )                
                                            ->where('purchase_order_details.po_details_id','=',$request->po_details_id[$sodKey])                      
                                            ->pluck('pend_po_qty')->first();

                                            $pend_po_qty = number_format((float)$pend_po_qty, 3, '.','');
                                            
                                        }

                                        if($sodVal == "0"){                                    
                                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                       
                                                $grn_order_details=   GRNMaterialDetails::create([
                                                    'grn_id'  => $request->id,
                                                    'po_details_id' => isset($request->po_details_id[$sodKey]) ? $request->po_details_id[$sodKey] : null,
                                                    'item_id' => $request->item_id[$sodKey],
                                                    'grn_qty' => isset($request->grn_qty[$sodKey]) ? $request->grn_qty[$sodKey] : null,
                                                    'rate_per_unit' => isset($request->rate_unit[$sodKey]) ? $request->rate_unit[$sodKey] : null,
                                                    'amount'  => isset($request->amount[$sodKey]) ? $request->amount[$sodKey] : null,
                                                    'remarks' => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : null,
                                                    'dc_details_id' => isset($request->dp_details_id[$sodKey]) ? $request->dp_details_id[$sodKey] : null,
                                                    // 'is_approved'  => $request->grn_type_fix_id == 1  ?  $pend_po_qty < $request->grn_qty[$sodKey] ? 'N' : 'Y' : null,
                                                    'is_approved'  => $request->grn_type_fix_id == 1  ?  $pend_po_qty < $request->grn_qty[$sodKey] ? 'N' : null : null,
                                                    'qc_required'  => $request->grn_type_fix_id == 1  ? isset($request->qc_required[$sodKey]) ? $request->qc_required[$sodKey] : 'No' : 'No',
                                                    'service_item'  => $request->grn_type_fix_id == 1  ? isset($request->service_item[$sodKey]) ? $request->service_item[$sodKey] : 'No' : 'No',
                                                    'mismatch_qty' => 0,
                                                    'grn_details_type' => null,
                                                    'status'=> 'Y',
                                                ]);

                                    

                                                if($request->grn_type_fix_id == 1 ){
                                                    if(isset($request->qc_required[$sodKey]) && $request->qc_required[$sodKey] == 'No' && $request->service_item[$sodKey] == 'No'){
                                                        stockEffect($locationCode->id,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->grn_qty[$sodKey],0,'add','U','GRN',$grn_order_details->grn_details_id);
                                                    }
                                                }
                                            }
                                        }else{

                                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){                                        
                                            
                                                if( $request->grn_type_fix_id == 1){
                                                 $grnApprove = GRNMaterialDetails::where('grn_details_id',$sodVal)->pluck('is_approved')->first();

                                              
                                                    if($grnApprove == 'Y'){
                                                        $is_approved = 'Y';

                                                    }else{
                                                        if($grnApprove == 'N'){
                                                            $pend_po_qty = $pend_po_qty < 0 ?  $request->org_grn_qty[$sodKey] + $pend_po_qty: $pend_po_qty;
        
                                                            $is_approved  = $request->grn_type_fix_id == 1  ?  $pend_po_qty < $request->grn_qty[$sodKey] ? 'N' : null : null;
                                                                                                        
                                                        }else{  
        
                                                            $pend_po_qty = $pend_po_qty >= 0 ? $pend_po_qty + $request->org_grn_qty[$sodKey] : $pend_po_qty;
        
                                                            $is_approved  = $request->grn_type_fix_id == 1  ?  $pend_po_qty < $request->grn_qty[$sodKey] ? 'N' : null : null;   
                                                        }
                                                    }                               

                                                }else{
                                                    $is_approved = null;
                                                }


                                                $grn_order_details =  GRNMaterialDetails::where('grn_details_id',$sodVal)->update([
                                                    'grn_id' => $request->id,
                                                    'po_details_id' => isset($request->po_details_id[$sodKey]) ? $request->po_details_id[$sodKey] : null,
                                                    'item_id' => $request->item_id[$sodKey],
                                                    'grn_qty'=> isset($request->grn_qty[$sodKey]) ? $request->grn_qty[$sodKey] : null,
                                                    'rate_per_unit' => isset($request->rate_unit[$sodKey]) ? $request->rate_unit[$sodKey] :  null,
                                                    'amount' => isset($request->amount[$sodKey]) ? $request->amount[$sodKey] : null,
                                                    'remarks' => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : null,
                                                    'dc_details_id' => isset($request->dp_details_id[$sodKey]) ? $request->dp_details_id[$sodKey] : null,
                                                    'is_approved'  => $is_approved,
                                                    'qc_required'  =>  $request->grn_type_fix_id == 1  ? isset($request->qc_required[$sodKey]) ? $request->qc_required[$sodKey] : 'No' : 'No',
                                                    'service_item'  =>  $request->grn_type_fix_id == 1  ? isset($request->service_item[$sodKey]) ? $request->service_item[$sodKey] : 'No' : 'No',
                                                    'mismatch_qty' => 0,
                                                    'grn_details_type' => null,
                                                    'status'=> 'Y',
                                                ]);
                                            
                                                if($request->grn_type_fix_id == 1 ){

                                                    if(isset($request->qc_required[$sodKey]) && $request->qc_required[$sodKey] == 'No' && $request->service_item[$sodKey] == 'No'){
                                                    stockEffect($locationCode->id,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->grn_qty[$sodKey] , $request->org_grn_qty[$sodKey],'edit','U','GRN',$sodVal);
                                                    }
                                                } 
                                            }                              
                                        }

                                    }
                                    
                                    $oldGrnDetailsData =  GRNMaterialDetails::where('grn_id',$request->id)->where('status', 'D')->get();  
                                    // dd($oldGrnDetailsData);
                                    foreach($oldGrnDetailsData as $gkey=>$gval){                          
                                        if($request->grn_type_fix_id == 1 ){
                                            if($gval['qc_required'] == 'No' && $gval['service_item'] == 'No'){
                                            stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0, $gval['grn_qty'],'delete','U','GRN',$gval['grn_details_id']);
                                            }
                                        }else{
                                            stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0, $gval['grn_qty'],'delete','U','GRN',$gval['grn_details_id']);
                                        }
                                        
                                    }

                                    $deleteGrnDetailsData =  GRNMaterialDetails::where('grn_id',$request->id)->where('status', 'D')->delete();                                
                                }
                                   
                            }
                           
                        }
                        DB::commit();
                                return response()->json([
                                    'response_code' => '1',
                                    'response_message' => 'Record Updated Successfully.',
                                ]);
                    }


                else{
                    DB::rollBack();
                    return response()->json([
                        'response_code' => '1',
                        'response_message' => 'Record Not Updated.',
                    ]);
                }
            }
            catch(\Exception $e){
// dd($e->getMessage(),$e->getLine());
                // DB::rollBack();
                // return response()->json([
                //     'response_code' => '0',
                //     'response_message' => 'Error Occured Record Not Updated',
                //     'original_error' => $e->getMessage()
                // ]);

                DB::rollBack(); 
                getActivityLogs("GRN", "update", $e->getMessage(),$e->getLine());  
            
                if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => $e->getMessage(),
                    ]);
                }else{
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Error Occured Record Not Inserted',
                        'original_error' => $e->getLine()
                    ]);
                }
            }

    }


    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{

            $date = GRNMaterial::where('grn_id',$request->id)->value('grn_date');

            // this cose use to stock maintain
            $locationCode = getCurrentLocation();

            $grn_verification = GRNVerification::
            leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','grn_verification.grn_details_id')
            ->where('material_receipt_grn_details.grn_id',$request->id)->get();
            if($grn_verification->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, GRN Is Used In GRN Verification.",
                ]);
            }

            $qc_data = QCApproval::
            leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','qc_approval.grn_details_id')
            ->where('material_receipt_grn_details.grn_id',$request->id)->get();
            if($qc_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, GRN Is Used In QC Approval.",
                ]);
            }

            $grn_type_fix_id = GRNMaterial::where('grn_id',$request->id)->pluck('grn_type_id_fix')->first();
            $oldGrnDetails = GRNMaterialDetails::where('grn_id','=',$request->id)->get();
            $oldGrnDetailsData = [];
            if($oldGrnDetails != null){
                $oldGrnDetailsData = $oldGrnDetails->toArray();
            }
            
            foreach($oldGrnDetailsData as $gkey=>$gval){               
                // $qty = -$gval['grn_qty'];
                // increaseStockQty($locationCode->id,$gval['item_id'],$qty);
                // increaseStockQty($locationCode->id,$gval['item_id'],0,$gval['grn_qty'],'delete');

                $SecUnitBeforeUpdate =  LiveUpdateSecDate($date,$gval['item_id']);

            
                if($SecUnitBeforeUpdate === true){                
                    DB::rollBack();     
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
                    ]);
                }

                    

                if($grn_type_fix_id == 3){
                    $oldGrnSecondDetails = GRNMaterailSecondaryDetails::where('grn_details_id','=',$gval['grn_details_id'])->get();

                    if(!$oldGrnSecondDetails->isEmpty()){
                        foreach($oldGrnSecondDetails as $okey=>$oval){  
                            if($gval['qc_required'] == 'No' && $gval['service_item'] == 'No'){                      
                                stockDetailsEffect($locationCode->id,$oval->item_details_id,$oval->item_details_id,0,$oval->grn_qty,'delete','U','GRN Secondary Details',$oval->grn_secondary_details_id,'Yes','GRN',$gval['grn_details_id']);
                            }

                            GRNMaterailSecondaryDetails::where('grn_secondary_details_id','=',$oval->grn_secondary_details_id)->delete();
                        }                       

                    }else{
                        if($gval['qc_required'] == 'No' && $gval['service_item'] == 'No'){
                            stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0,$gval['grn_qty'],'delete','U','GRN',$gval['grn_details_id']);
                        }
                    }

                   
                }

                if($grn_type_fix_id == 1){
                     $oldGrnSecondDetails = GRNMaterailSecondaryDetails::where('grn_details_id','=',$gval['grn_details_id'])->get();

                    if(!$oldGrnSecondDetails->isEmpty()){
                        foreach($oldGrnSecondDetails as $okey=>$oval){  
                            if($gval['qc_required'] == 'No' && $gval['service_item'] == 'No'){                      
                                stockDetailsEffect($locationCode->id,$oval->item_details_id,$oval->item_details_id,0,$oval->grn_qty,'delete','U','GRN Secondary Details',$oval->grn_secondary_details_id,'Yes','GRN',$gval['grn_details_id']);
                            }

                            GRNMaterailSecondaryDetails::where('grn_secondary_details_id','=',$oval->grn_secondary_details_id)->delete();
                        }                       

                    }else{
                        if($gval['qc_required'] == 'No' && $gval['service_item'] == 'No'){
                            stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0,$gval['grn_qty'],'delete','U','GRN',$gval['grn_details_id']);
                        }
                    }
                    // if($gval['qc_required'] == 'No' && $gval['service_item'] == 'No'){
                    // stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0,$gval['grn_qty'],'delete','U','GRN',$gval['grn_details_id']);
                    // }
                }else{
                    if($grn_type_fix_id == 2){
                    stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0,$gval['grn_qty'],'delete','U','GRN',$gval['grn_details_id']);
                    }

                }
            }


            GRNMaterialDetails::where('grn_id',$request->id)->delete();
            GRNMaterial::destroy($request->id);


            DB::commit();

            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
             DB::rollBack();
            getActivityLogs("GRN", "delete", $e->getMessage(),$e->getLine());  
             
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

    public function getLatestGrnNo(Request $request)
    {
          $modal  =  GRNMaterial::class;
          $sequence = 'grn_sequence';
          $prefix = 'GRN';
          $po_num_format = getLatestSequence($modal,$sequence,$prefix);

          $locationName = getCurrentLocation();

          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $po_num_format['format'],
            'number'        => $po_num_format['isFound'],
            'location'      => $locationName
        ]);
    }

    public function getPoSupplier(Request $request){  
        

        
     
        // $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();



        if($request->grn_type_fix_id == 1){

        $get_po_supplier = PurchaseOrderDetails::select('suppliers.supplier_name','suppliers.id',
        // DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)
        // FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)
        // FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),        

        DB::raw("(SELECT purchase_order_details.po_qty -  (SELECT IFNULL(SUM(psid.sc_qty),0) FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),        
     
        // DB::raw("((SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.po_id = purchase_order.po_id) - (SELECT IFNULL(SUM(psid.sc_qty), 0) FROM purchase_order_short_close AS psid
        // WHERE psid.po_details_id IN ( SELECT pod.po_details_id FROM purchase_order_details AS pod
        // WHERE pod.po_id = purchase_order.po_id)) - (SELECT IFNULL(SUM(gid.grn_qty), 0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id IN ( SELECT pod.po_details_id
        // FROM purchase_order_details AS pod WHERE pod.po_id = purchase_order.po_id ))) AS pend_po_qty"), 
        )

        ->leftJoin('purchase_order','purchase_order.po_id', 'purchase_order_details.po_id')
        ->leftJoin('suppliers','suppliers.id', 'purchase_order.supplier_id')
        // ->where('purchase_order.current_location_id',$locationCode->id)
        ->where('purchase_order.to_location_id',$locationCode->id)
        ->where('purchase_order.is_approved','=',1)
        // ->where('purchase_order.year_id',$year_data->id)               
        ->whereIn('purchase_order.year_id',$yearIds)               
        ->having('pend_po_qty','>',0)
        ->get();
        

        $get_po_supplier = $get_po_supplier->unique(['id']);

        $get_po_supplier = $get_po_supplier->values()->all();



        }else{

            $get_po_supplier =  Supplier::select('id','supplier_name')->orderBy('supplier_name','asc')->get();

        }
     



        return response()->json([
            'response_code' => 1,
            'get_po_supplier'  => $get_po_supplier,
            // 'getSupplier' => $getSupplier
        ]);
    }
  
    // public function getPoListForGrn(Request $request)
    // {

    //     $yearIds = getCompanyYearIdsToTill();
    //     $locationCode = getCurrentLocation();

    //     if(isset($request->id)){
    //         $pod_id = GRNMaterialDetails::select('po_details_id')->where('grn_id','=',$request->id)->get();  

    //         $edit_po_data = GRNMaterialDetails::select(['purchase_order.po_number','purchase_order.po_date','purchase_order_details.po_details_id','purchase_order_details.del_date as del_date','purchase_order_details.po_qty',
    //         // 'material_receipt_grn_details.grn_qty as pend_po_qty',
    //         'suppliers.supplier_name', 'locations.location_name',
    //         'items.item_name' ,'units.unit_name', 'items.item_code','item_groups.item_group_name',
    //         // DB::raw("((SELECT IFNULL(SUM(gid.grn_qty),0)
    //         // FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"), 
    //         DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)
    //         FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)
    //         FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),
    //         ])
    //         ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
    //         ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
    //         ->leftJoin('items','items.id','=','purchase_order_details.item_id')           
    //         ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
    //         ->leftJoin('units','units.id','=','items.unit_id')
    //         ->leftJoin('suppliers','suppliers.id','=','purchase_order.supplier_id')

    //         ->leftJoin('locations','locations.id','=','purchase_order.to_location_id')
    //         ->where('material_receipt_grn_details.grn_id',$request->id)
    //         ->get();           

    //     }

    //     $po_data = PurchaseOrder::select(['purchase_order.po_number','purchase_order.po_date','purchase_order_details.po_details_id','purchase_order_details.del_date as del_date','purchase_order_details.po_qty',  'items.item_name' ,'units.unit_name',
    //     'items.item_code', 'suppliers.supplier_name','item_groups.item_group_name', 'locations.location_name',

    //     DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)

    //     FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)

    //     FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"), 
    //      ])
    //     ->leftJoin('purchase_order_details','purchase_order_details.po_id','=','purchase_order.po_id')
    //     ->leftJoin('items','items.id','=','purchase_order_details.item_id')           
    //     ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
    //     ->leftJoin('units','units.id','=','items.unit_id')

    //     ->leftJoin('suppliers','suppliers.id','=','purchase_order.supplier_id')

    //     ->leftJoin('locations','locations.id','=','purchase_order.to_location_id')
        
    //     ->where('purchase_order.supplier_id', $request->grn_supplier_id)
    //     // ->where('purchase_order.current_location_id',$locationCode->id)
    //     ->where('purchase_order.to_location_id',$locationCode->id)
    //     ->where('purchase_order.is_approved','=',1)
    //     ->whereIn('purchase_order.year_id',$yearIds)  
    //     ->having('pend_po_qty','>',0)      
    //     ->get();

    //     // dd($edit_po_data,$po_data);

    //     if(isset($edit_po_data)){
    //         $data = collect($po_data)->merge($edit_po_data);
    //         $grouped = $data->groupBy('po_details_id');    
            

    //         $merged = $grouped->map(function ($items) {
    //             return $items->reduce(function ($carry, $item) {
    //                 if (!$carry) {
    //                     return $item;
    //                 }
    //                 // $carry->pend_po_qty += (float) $item->pend_po_qty;
    //                 return $carry;
    //             });
    //         });
    
    //         $po_data = $merged->values();   

    //     }



    //     if ($po_data != null) {
    //         foreach ($po_data as $cpKey => $cpVal) {
    //             if ($cpVal->po_date != null) {
    //                 $cpVal->po_date = Date::createFromFormat('Y-m-d', $cpVal->po_date)->format('d/m/Y');
    //             }
    //             if ($cpVal->del_date != null) {
    //                 $cpVal->del_date = Date::createFromFormat('Y-m-d', $cpVal->del_date)->format('d/m/Y');
    //             }
    //             $cpVal->pend_po_qty =  $cpVal->pend_po_qty > 0 ?  $cpVal->pend_po_qty : 0;
               
    //         }
    //     }


    //     $po_data = $po_data->sortBy('po_details_id')->values();

    //     if ($po_data != null) {
    //         return response()->json([
    //             'response_code' => 1,
    //             'po_data'  => $po_data,            
    //         ]);
    //     }else{
    //         return response()->json([
    //             'response_code' => 1,
    //             'po_data'  => [],            
    //         ]);

    //     }

    // }

    public function getPoListForGrn(Request $request)
    {

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        if(isset($request->id)){
            $pod_id = GRNMaterialDetails::select('po_details_id')->where('grn_id','=',$request->id)->get();  

            $edit_po_data = GRNMaterialDetails::select(['material_receipt_grn_details.grn_details_id','purchase_order.po_id','purchase_order.po_number','purchase_order.po_date','locations.location_name','items.secondary_unit',      
            DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)
            FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)
            FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),
            ])
            ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
             ->leftJoin('items','items.id','=','purchase_order_details.item_id')  
            ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')          
            ->leftJoin('locations','locations.id','=','purchase_order.to_location_id')
            ->where('material_receipt_grn_details.grn_id',$request->id)
            ->get();           

        }

        $po_data = PurchaseOrder::select(['purchase_order.po_id','purchase_order.po_number','purchase_order.po_date','locations.location_name','items.secondary_unit',
        // DB::raw("((SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.po_id = purchase_order.po_id) - (SELECT IFNULL(SUM(psid.sc_qty), 0) FROM purchase_order_short_close AS psid
        // WHERE psid.po_details_id IN ( SELECT pod.po_details_id FROM purchase_order_details AS pod
        // WHERE pod.po_id = purchase_order.po_id)) - (SELECT IFNULL(SUM(gid.grn_qty), 0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id IN ( SELECT pod.po_details_id
        // FROM purchase_order_details AS pod WHERE pod.po_id = purchase_order.po_id ))) AS pend_po_qty"),
        DB::raw("(SELECT purchase_order_details.po_qty -  (SELECT IFNULL(SUM(psid.sc_qty),0) FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),   
    
         ])
        ->leftJoin('purchase_order_details','purchase_order_details.po_id','=','purchase_order.po_id')
        ->leftJoin('items','items.id','=','purchase_order_details.item_id')  
        ->leftJoin('locations','locations.id','=','purchase_order.to_location_id')        
        ->where('purchase_order.supplier_id', $request->grn_supplier_id)
        ->where('purchase_order.to_location_id',$locationCode->id)
        ->where('purchase_order.is_approved','=',1)
        ->whereIn('purchase_order.year_id',$yearIds)  
        ->having('pend_po_qty','>',0) 
        // ->groupBY('purchase_order.po_id')   
        ->groupBY('purchase_order.po_id','purchase_order_details.po_details_id')   
        ->get();

        $po_data = $po_data->unique(['po_id']);

        
        if(isset($edit_po_data)){
            $data = collect($po_data)->merge($edit_po_data);
            $grouped = $data->groupBy('po_id');    
            

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    // $carry->pend_po_qty += (float) $item->pend_po_qty;
                    return $carry;
                });
            });
    
            $po_data = $merged->values();   

        }



        if ($po_data != null) {
            foreach ($po_data as $cpKey => $cpVal) {
                if ($cpVal->po_date != null) {
                    $cpVal->po_date = Date::createFromFormat('Y-m-d', $cpVal->po_date)->format('d/m/Y');
                }
                if ($cpVal->del_date != null) {
                    $cpVal->del_date = Date::createFromFormat('Y-m-d', $cpVal->del_date)->format('d/m/Y');
                }
                $cpVal->pend_po_qty =  $cpVal->pend_po_qty > 0 ?  $cpVal->pend_po_qty : 0;     

                $inUse = PurchaseOrderDetails::select('qc_approval.grn_details_id')
                ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.po_details_id','=','purchase_order_details.po_details_id')     
                ->leftJoin('qc_approval','qc_approval.grn_details_id','=','material_receipt_grn_details.grn_details_id')   
                ->whereNotNull('material_receipt_grn_details.grn_details_id')        
                ->whereNotNull('qc_approval.grn_details_id')        
                ->where('po_id','=',$cpVal->po_id)->get();
                

                if($inUse->isNotEmpty() || $cpVal->secondary_unit == 'Yes'){
                    $cpVal->in_use = true;
                }else{
                    $cpVal->in_use = false;
                }
               
            }
        }


        $po_data = $po_data->sortBy('purchase_order.po_id')->values();

        if ($po_data != null) {
            return response()->json([
                'response_code' => 1,
                'po_data'  => $po_data,            
            ]);
        }else{
            return response()->json([
                'response_code' => 1,
                'po_data'  => [],            
            ]);

        }

    }


     public function getPoItemListForGrn(Request $request)
    {

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        $request->chkPOId = explode(',', $request->chkPOId);


        if(isset($request->id)){
            $pod_id = GRNMaterialDetails::select('po_details_id')->where('grn_id','=',$request->id)->get();  

            $edit_po_data = GRNMaterialDetails::select(['material_receipt_grn_details.grn_details_id','purchase_order.po_number','purchase_order.po_date','purchase_order_details.po_details_id','purchase_order_details.del_date as del_date','purchase_order_details.po_qty','purchase_order.po_id','items.secondary_unit',
            'material_receipt_grn_details.grn_qty',
            'suppliers.supplier_name', 'locations.location_name',
            'items.item_name' ,'units.unit_name', 'items.item_code','item_groups.item_group_name',
            // DB::raw("((SELECT IFNULL(SUM(gid.grn_qty),0)
            // FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"), 
            DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)
            FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)
            FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),
            ])
            ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
            ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
            ->leftJoin('items','items.id','=','purchase_order_details.item_id')           
            ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
            ->leftJoin('units','units.id','=','items.unit_id')
            ->leftJoin('suppliers','suppliers.id','=','purchase_order.supplier_id')

            ->leftJoin('locations','locations.id','=','purchase_order.to_location_id')
            ->where('material_receipt_grn_details.grn_id',$request->id)
            ->whereIn('purchase_order_details.po_id',$request->chkPOId)  
            ->get();           

        }

        $po_data = PurchaseOrder::select(['purchase_order.po_number','purchase_order.po_date','purchase_order_details.po_details_id','purchase_order_details.del_date as del_date','purchase_order_details.po_qty',  'items.item_name' ,'units.unit_name','purchase_order.po_id',
        'items.item_code', 'suppliers.supplier_name','item_groups.item_group_name', 'locations.location_name',
'items.secondary_unit',
        DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)
        FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)
        FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"), 

        DB::raw('0 as grn_qty'),
         ])
        ->leftJoin('purchase_order_details','purchase_order_details.po_id','=','purchase_order.po_id')
        ->leftJoin('items','items.id','=','purchase_order_details.item_id')           
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        ->leftJoin('units','units.id','=','items.unit_id')

        ->leftJoin('suppliers','suppliers.id','=','purchase_order.supplier_id')

        ->leftJoin('locations','locations.id','=','purchase_order.to_location_id')
        
        ->where('purchase_order.supplier_id', $request->grn_supplier_id)
        // ->where('purchase_order.current_location_id',$locationCode->id)
        ->where('purchase_order.to_location_id',$locationCode->id)
        ->where('purchase_order.is_approved','=',1)
        ->whereIn('purchase_order.year_id',$yearIds)  
        ->whereIn('purchase_order_details.po_id',$request->chkPOId)  
        ->having('pend_po_qty','>',0)      
        ->get();

        // dd($edit_po_data,$po_data);

        if(isset($edit_po_data)){
            $data = collect($po_data)->merge($edit_po_data);
            $grouped = $data->groupBy('po_details_id');    
            

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    // $carry->pend_po_qty += (float) $item->pend_po_qty;
                    $carry->grn_qty += (float) $item->grn_qty;
                    return $carry;
                });
            });
    
            $po_data = $merged->values();   

        }



        if ($po_data != null) {

            foreach ($po_data as $cpKey => $cpVal) {
                if ($cpVal->po_date != null) {
                    $cpVal->po_date = Date::createFromFormat('Y-m-d', $cpVal->po_date)->format('d/m/Y');
                }
                if ($cpVal->del_date != null) {
                    $cpVal->del_date = Date::createFromFormat('Y-m-d', $cpVal->del_date)->format('d/m/Y');
                }
                $cpVal->pend_po_qty =  $cpVal->pend_po_qty;
                // $cpVal->pend_po_qty =  $cpVal->pend_po_qty > 0 ?  $cpVal->pend_po_qty : 0;

                if(isset($cpVal->grn_details_id)){   
                    $newRequest = new Request();
    
                    $newRequest->po_details_id = $cpVal->po_details_id;
                    $newRequest->record_id = $cpVal->grn_details_id;
                    $newRequest->total_qty = $cpVal->po_qty;
    
                    $cpVal->show_pend_qty = self::getPendingQty($newRequest);

                    $inUse = QCApproval::select('grn_details_id')       
                    ->where('grn_details_id','=',$cpVal->grn_details_id)->get();
    
                    if($inUse->isNotEmpty() || $cpVal->secondary_unit == 'Yes'){
                        $cpVal->in_use = true;
                    }else{
                        $cpVal->in_use = false;
                    }

                }else{
                    $cpVal->show_pend_qty = $cpVal->pend_po_qty;
                    $cpVal->in_use = false;
                }
               
            }
        }


        $po_data = $po_data->sortBy('po_id')
        ->sortBy('po_details_id')
        ->values();


        if ($po_data != null) {
            return response()->json([
                'response_code' => 1,
                'po_data'  => $po_data,            
            ]);
        }else{
            return response()->json([
                'response_code' => 1,
                'po_data'  => [],            
            ]);

        }

    }



    public function getPoPartDataForGrn(Request $request){

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        $request->po_ids = explode(',', $request->po_ids);


        if(isset($request->id)){
            $pod_id = GRNMaterialDetails::select('po_details_id')->where('grn_id','=',$request->id)->get();          

            $edit_po_data = GRNMaterialDetails::select(['purchase_order.po_id','purchase_order.po_number','purchase_order.po_date','purchase_order_details.po_details_id','purchase_order_details.del_date as del_date','purchase_order_details.po_qty', 'purchase_order_details.rate_per_unit', 'purchase_order_details.discount',
            'purchase_order_details.amount','purchase_order_details.item_id','items.item_code' ,'purchase_order_details.remarks', 'item_groups.item_group_name',
            'units.unit_name',
            // 'material_receipt_grn_details.grn_qty as pend_po_qty',     
            // DB::raw('COALESCE(material_receipt_grn_details.grn_details_id, 0) 
            // as grn_details_id')

            DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)
            FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)
            FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),
            'material_receipt_grn_details.qc_required','material_receipt_grn_details.service_item',
            ])                
            ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
            ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
            ->leftJoin('items','items.id','=','purchase_order_details.item_id')           
            ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
            ->leftJoin('units','units.id','=','items.unit_id')
            ->whereIn('material_receipt_grn_details.po_details_id', $request->po_ids)
            ->where('material_receipt_grn_details.grn_id','=',$request->id)
            ->get();            

        }


        $po_data =  PurchaseOrder::select(['purchase_order.po_id','purchase_order.po_number','purchase_order.po_date','purchase_order.total_qty','purchase_order.po_id','purchase_order_details.po_qty',
        DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)
        FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)
        FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),  'purchase_order_details.rate_per_unit', 'purchase_order_details.discount','purchase_order_details.amount','purchase_order_details.item_id','items.item_code' ,'purchase_order_details.remarks','units.unit_name','purchase_order_details.po_details_id', 'item_groups.item_group_name', 'units.unit_name','items.qc_required' ,'items.service_item' ,
        // DB::raw("((SELECT IFNULL(SUM(gid.grn_qty),0)
        // FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as grn_qty"),    
        // DB::raw('COALESCE(material_receipt_grn_details.grn_details_id, 0) 
        //         as grn_details_id')
                
        ])
        ->leftJoin('purchase_order_details','purchase_order_details.po_id','=','purchase_order.po_id')
        ->leftJoin('items','items.id','=','purchase_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        
        // ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.po_details_id','=','purchase_order_details.po_details_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->whereIn('purchase_order_details.po_details_id', $request->po_ids)
        // ->where('purchase_order.current_location_id',$locationCode->id)
        ->where('purchase_order.to_location_id',$locationCode->id)
        ->where('purchase_order.is_approved','=',1)
        ->whereIn('purchase_order.year_id',$yearIds)  
        ->having('pend_po_qty','>',0)      
        ->get();
        

        if(isset($edit_po_data)){
            $data = collect($po_data)->merge($edit_po_data);
            $grouped = $data->groupBy('po_details_id');    

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    // $carry->pend_po_qty += (float) $item->pend_po_qty;
                    return $carry;
                });
            });
    
            $po_data = $merged->values();   

        }

      
        if ($po_data != null) {
            $changedItemIds = [];
            foreach ($po_data as $cpKey => $cpVal) {

                if ($cpVal->po_date != null) {
                    $cpVal->po_date = Date::createFromFormat('Y-m-d', $cpVal->po_date)->format('d/m/Y');
                }

                if(isset($request->id)){  
                    $grn_qty =   GRNMaterialDetails::where('po_details_id','=',$cpVal->po_details_id)->where('grn_id',$request->id)->sum('grn_qty');
                    $grn_id = GRNMaterialDetails::where('po_details_id','=',$cpVal->po_details_id)->where('grn_id',$request->id)->first();
                    $cpVal->grn_qty = $grn_qty;
                    $cpVal->grn_details_id = $grn_id!= null ? $grn_id->grn_details_id : 0;    
                    
                    if($grn_id!= null){
                        $isFound = QCApproval::where('qc_approval.grn_details_id',$grn_id->grn_details_id)->sum('qc_qty');
                    
                        if($isFound){
                            $cpVal->in_use = true;
                            $cpVal->used_qty = $isFound;
                        }else{
                            $cpVal->in_use = false;
                            $cpVal->used_qty = 0;
                        }
                    }else{
                        $cpVal->in_use = false;
                            $cpVal->used_qty = 0;
                    }
                   
                    
                }else{
                    $cpVal->grn_qty = 0;
                    $cpVal->grn_details_id = 0;
                    $cpVal->in_use = false;
                    $cpVal->used_qty = 0;
                }

                $cpVal->pend_po_qty =  $cpVal->pend_po_qty > 0 ?  $cpVal->pend_po_qty : 0;

                $newRequest = new Request();

                $newRequest->po_details_id = $cpVal->po_details_id;
                $newRequest->record_id = $cpVal->grn_details_id;
                $newRequest->total_qty = $cpVal->po_qty;

                $cpVal->show_pend_qty = self::getPendingQty($newRequest);

                $dis_amt = ($cpVal->po_qty * $cpVal->rate_per_unit * ($cpVal->discount / 100));
                $final_rate_per_unit = $cpVal->rate_per_unit - ($dis_amt / $cpVal->po_qty);
                $cpVal->rate_per_unit = $final_rate_per_unit;

                $changedItemId = Item::where('id', $cpVal->item_id)
                ->where(function($query) {
                    $query->where('items.status', 'deactive')
                    ->orWhere('items.service_item', 'Yes');                     
                })
                ->pluck('id')
                ->first();
                if ($changedItemId) {
                    $changedItemIds[] = $changedItemId; // Now it works
                }


              
            }
        }
// dd($po_data);

        // $po_data = $po_data->sortBy('purchase_order.po_id')->values();
        $po_data = $po_data->sortBy('po_id')
        ->sortBy('po_details_id')
        ->values();

        if($changedItemIds){
            $item = Item::select('id','item_name')->whereIN('id',$changedItemIds)->get();
        }else{
            $item = '';
        }

        
        if ($po_data != null) {
            return response()->json([
                'response_code' => '1',
                'po_data' => $po_data,
                'item' => $item,

               
            ]);
        } else {
            return response()->json([
                'response_code' => '1',
                'po_data' => []
            ]);
        }
    }



    public function getDcLocation(Request $request){    

        $locationCode = getCurrentLocation();
        $yearIds = getCompanyYearIdsToTill();      
        
        $dc_details_id = GRNMaterialDetails::select('dc_details_id')   
        ->whereNotNull('dc_details_id')             
        ->pluck('dc_details_id')
        ->toArray();
       
        $get_dc_location = DispatchPlanDetails::select('locations.location_name','locations.id',)
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
        ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
        ->leftJoin('locations','locations.id', 'sales_order.current_location_id')
        ->where('sales_order.to_location_id',$locationCode->id)
        ->where('locations.id', '!=', $locationCode->id)     
        ->whereNotIn('dispatch_plan_details.dp_details_id',$dc_details_id) 
        ->whereNotNull('sales_order_details.mr_details_id')             
        ->whereIn('dispatch_plan.year_id',$yearIds)               
        ->get(); 

        
        $get_dc_location = $get_dc_location->unique(['id']);
        $get_dc_location = $get_dc_location->values()->all();
        
        return response()->json([
            'response_code' => 1,
            'get_dc_location'  => $get_dc_location,
        ]);
    }


    public function getDcListForGrn(Request $request)
    {
        $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        // if(isset($request->id)){
        //     $dp_id = GRNMaterialDetails::select('dc_details_id')->where('grn_id','=',$request->id)->get();  

        //     $edit_dp_data = GRNMaterialDetails::select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.dp_details_id','dispatch_plan_details.plan_qty','material_receipt_grn_details.grn_qty as pend_plan_qty','material_request.mr_number','material_request.mr_date',
        //     'items.item_name' ,'units.unit_name', 'items.item_code','item_groups.item_group_name',
        //     'loading_entry.vehicle_no','transporters.transporter_name',
        //     // DB::raw("((SELECT IFNULL(SUM(gid.grn_qty),0)
        //     // FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"), 
        //     'material_receipt_grn_details.grn_details_id'
        //     ])
        //     ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
        //     ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
        //     ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
        //     ->leftJoin('loading_entry','loading_entry.le_id','=','loading_entry_details.le_id')
        //     ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        //     ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
        //     ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
        //     ->leftJoin('items','items.id','=','dispatch_plan_details.item_id')           
        //     ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')    
        //     ->leftJoin('transporters','transporters.id','=','loading_entry.transporter_id')       
        //     ->leftJoin('units','units.id','=','items.unit_id')
        //     ->where('material_receipt_grn_details.grn_id',$request->id)
        //     ->get();           

        // }

        
        $dc_details_id = GRNMaterialDetails::select('dc_details_id')   
        ->whereNotNull('dc_details_id')             
        ->pluck('dc_details_id')
        ->toArray();

        $dc_data = DispatchPlan::select(['loading_entry.le_id','loading_entry.dp_number','dispatch_plan.dp_date','loading_entry.vehicle_no','transporters.transporter_name',
           DB::raw("CASE 
                WHEN loading_entry.dp_number IS NOT NULL THEN loading_entry.dp_number 
                ELSE dispatch_plan.dp_number 
             END as dp_number"),
         ])
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
        ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','=','loading_entry_details.le_id')
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')      
        ->leftJoin('transporters','transporters.id','=','loading_entry.transporter_id')
        ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
        ->leftJoin('locations','locations.id', 'sales_order.current_location_id')      
        ->where('dispatch_plan.current_location_id','=',$request->grn_location_id)
        ->where('sales_order.to_location_id',$locationCode->id)
        ->where('locations.id', '!=', $locationCode->id)
        ->whereIn('dispatch_plan.year_id',$yearIds) 
        ->whereNotIn('dispatch_plan_details.dp_details_id',$dc_details_id) 
        ->whereNotNull('sales_order_details.mr_details_id')   
        ->whereNotNull('loading_entry_details.dp_details_id')          
        ->groupBy('loading_entry.le_id')          
        ->get();
   

        if(isset($edit_dp_data)){
            $data = collect($dc_data)->merge($edit_dp_data);
            $grouped = $data->groupBy('dp_details_id');    
            

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pend_plan_qty += (float) $item->pend_plan_qty;
                    return $carry;
                });
            });
    
            $dc_data = $merged->values();   

        }



        if ($dc_data != null) {
            foreach ($dc_data as $cpKey => $cpVal) {
                if ($cpVal->dp_date != null) {
                    $cpVal->dp_date = Date::createFromFormat('Y-m-d', $cpVal->dp_date)->format('d/m/Y');
                }
                if ($cpVal->mr_date != null) {
                    $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
                }
                
                if(isset($cpVal->grn_details_id)){
                    $inUse = QCApproval::select('grn_details_id')       
                    ->where('grn_details_id','=',$cpVal->grn_details_id)->get();
    
                    if($inUse->isNotEmpty()){
                        $cpVal->in_use = true;
                    }else{
                        $cpVal->in_use = false;
                    }
                }else{
                    $cpVal->in_use = false;
                }

              
               
            }
        }


        $dc_data = $dc_data->sortBy('dp_details_id')->values();

        if ($dc_data != null) {
            return response()->json([
                'response_code' => 1,
                'dc_data'  => $dc_data,            
            ]);
        }else{
            return response()->json([
                'response_code' => 1,
                'dc_data'  => [],            
            ]);

        }

    }

    public function getDcPartDataForGrn(Request $request){

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        $request->le_ids = explode(',', $request->le_ids);


        // if(isset($request->id)){
        //     $dp_id = GRNMaterialDetails::select('dc_details_id')->where('grn_id','=',$request->id)->get();          

        //     $edit_dp_data = GRNMaterialDetails::select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.dp_details_id','dispatch_plan_details.plan_qty',
        //     // 'material_receipt_grn_details.grn_qty as pend_plan_qty',
        //     'material_request.mr_number','material_request.mr_date','material_receipt_grn_details.item_id',
        //     'items.item_name' ,'units.unit_name', 'items.item_code','item_groups.item_group_name',
        //     'sales_order_details.rate_per_unit', 'material_receipt_grn_details.qc_required','material_receipt_grn_details.service_item',
        //     ])                
        //     ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
        //     ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
        //     ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        //     ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
        //     ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
        //     ->leftJoin('items','items.id','=','dispatch_plan_details.item_id')           
        //     ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        //     ->leftJoin('units','units.id','=','items.unit_id')
        //     ->whereIn('material_receipt_grn_details.dc_details_id', $request->dc_detail_ids)
        //     ->where('material_receipt_grn_details.grn_id','=',$request->id)
        //     ->get();            

        // }


        // $dc_data =  LoadingEntryDetails::select(['dispatch_plan.dp_number','dispatch_plan.dp_date',
        // 'units.unit_name','dispatch_plan_details.dp_details_id', 'item_groups.item_group_name', 'units.unit_name','items.item_name' ,'dispatch_plan_details.item_id as item_id','items.item_code','material_request.mr_number','material_request.mr_date', 'dispatch_plan_details.plan_qty','sales_order_details.rate_per_unit','items.qc_required' ,'items.service_item' ,

        //    DB::raw("CASE 
        //         WHEN loading_entry.dp_number IS NOT NULL THEN loading_entry.dp_number 
        //         ELSE dispatch_plan.dp_number 
        //      END as dp_number"),

        // ])
        // ->leftJoin('loading_entry','loading_entry.le_id','=','loading_entry_details.le_id')     
        // ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')     
        // ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')     
        // ->leftJoin('items','items.id','=','dispatch_plan_details.item_id')
        // ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')       
        // ->leftJoin('units','units.id','=','items.unit_id')
        // ->whereIn('loading_entry.le_id', $request->le_ids)       
        // ->whereIn('loading_entry.year_id',$yearIds) 
        // ->get();


        $dc_data =  DispatchPlan::select(['dispatch_plan.dp_date','loading_entry_details.dp_details_id','loading_entry_details.le_details_id','loading_entry.vehicle_no',
        // 'units.unit_name', 
           DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),
        'item_groups.item_group_name', 'items.item_name' ,'dispatch_plan_details.item_id','items.item_code','sales_order_details.rate_per_unit','items.qc_required' ,'items.service_item' ,'item_details.secondary_item_name',
        // 'loading_entry_details.loading_qty','loading_entry_secondary_details.plan_qty',
         DB::raw("CASE WHEN loading_entry_secondary_details.plan_qty IS NOT NULL THEN loading_entry_secondary_details.plan_qty ELSE loading_entry_details.loading_qty END as loading_qty"),
        'item_details.item_details_id','loading_entry_secondary_details.le_secondary_details_id',
        DB::raw("'from_loading' as grn_details_type"),


         DB::raw("CASE 
                WHEN loading_entry.dp_number IS NOT NULL THEN loading_entry.dp_number 
                ELSE dispatch_plan.dp_number 
             END as dp_number"),

        ])
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
        ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
        ->leftJoin('loading_entry_secondary_details','loading_entry_secondary_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','=','loading_entry_details.le_id')
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
        ->leftJoin('items','items.id','=','dispatch_plan_details.item_id')
        ->leftJoin('item_details','item_details.item_details_id','=','loading_entry_secondary_details.item_details_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')       
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')
        ->where('sales_order.to_location_id',$locationCode->id)  
        ->whereIn('loading_entry.le_id', $request->le_ids)       
        ->whereIn('loading_entry.year_id',$yearIds) 
        ->get();


        if(isset($edit_dp_data)){
            $data = collect($dc_data)->merge($edit_dp_data);
            $grouped = $data->groupBy('dp_details_id');    

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pend_plan_qty += (float) $item->pend_plan_qty;
                    return $carry;
                });
            });
    
            $dc_data = $merged->values();   

        }

      
        if ($dc_data != null) {
            $changedItemIds = [];
            foreach ($dc_data as $cpKey => $cpVal) {

                if ($cpVal->dp_date != null) {
                    $cpVal->dp_date = Date::createFromFormat('Y-m-d', $cpVal->dp_date)->format('d/m/Y');
                }
                if ($cpVal->mr_date != null) {
                    $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
                }

                if(isset($request->id)){  
                    $grn_qty =   GRNMaterialDetails::where('dc_details_id','=',$cpVal->dp_details_id)->where('grn_id',$request->id)->sum('grn_qty');
                    $grn_id = GRNMaterialDetails::where('dc_details_id','=',$cpVal->dp_details_id)->where('grn_id',$request->id)->first();

                    $cpVal->grn_qty = $grn_qty;
                    $cpVal->grn_details_id = $grn_id!= null ? $grn_id->grn_details_id : 0;                      
                    $cpVal->rate_per_unit = $grn_id!= null ? $grn_id->rate_per_unit : $cpVal->rate_per_unit;             
                    
                    if($grn_id!= null){
                        $isFound = QCApproval::where('qc_approval.grn_details_id',$grn_id->grn_details_id)->sum('qc_qty');
                    
                        if($isFound){
                            $cpVal->in_use = true;
                            $cpVal->used_qty = $isFound;
                        }else{
                            $cpVal->in_use = false;
                            $cpVal->used_qty = 0;
                        }
                    }else{
                        $cpVal->in_use = false;
                            $cpVal->used_qty = 0;
                    }
                    
                }else{
                    $cpVal->grn_qty = 0;
                    $cpVal->grn_details_id = 0;
                    $cpVal->in_use = false;
                    $cpVal->used_qty = 0;
                }

                $changedItemId = Item::where('id', $cpVal->item_id)
                ->where(function($query) {
                    $query->where('items.status', 'deactive')
                    ->orWhere('items.service_item', 'Yes');                     
                })
                ->pluck('id')
                ->first();
                if ($changedItemId) {
                    $changedItemIds[] = $changedItemId; // Now it works
                }

                $cpVal->item_detail = ItemDetails::select('item_details.item_details_id','item_details.secondary_item_name')->where('item_id',$cpVal->item_id)->get();
              
            }
        }
// dd($po_data);

        $dc_data = $dc_data->sortBy('dp_details_id')->values();

        if($changedItemIds){
            $item = Item::select('id','item_name')->whereIN('id',$changedItemIds)->get();
        }else{
            $item = '';
        }

        
        if ($dc_data != null) {
            return response()->json([
                'response_code' => '1',
                'dc_data' => $dc_data,
                'item' => $item,
               
            ]);
        } else {
            return response()->json([
                'response_code' => '1',
                'dc_data' => []
            ]);
        }
    }


    public function getexceedGrnQty(Request $request){
        $isAnyPartInUse = false;
        $grnData = GRNMaterialDetails::where('grn_id','=',$request->grn_id)->get();

        if($grnData != null){
            foreach($grnData as $key=>$val){
                $isFound = GRNMaterialDetails::where('grn_details_id','=',$val['grn_details_id'])->where('is_approved','=','N')->first();
                if($isFound != null){                  
                    $isAnyPartInUse = true;
                }

            }  
            return response()->json([
                'in_use' => $isAnyPartInUse,
                'response_code' => '1',
                'response_message' => '',
            ]);
         }else{
            return response()->json([
                'in_use' => false,
                'response_code' => '0',
                'response_message' => '',
            ]);
         }


    }



    public function getPendingQty(Request $request){
        $exectQty = $request->total_qty;       
        $exectQty = number_format((float)$exectQty, 3, '.','');   

        $oldRecords = GRNMaterialDetails::select(DB::raw('SUM(grn_qty) as sum'))
        ->where('po_details_id','=',$request->po_details_id)
        ->where('grn_details_id','<=',$request->record_id)
        ->groupBy(['po_details_id'])
        ->first();

        if($oldRecords != null){

            $diff = $exectQty - number_format((float)$oldRecords->sum, 3, '.','');
          
            return $diff;
        }else{
            return abs($exectQty);
        } 
        
    }

    public function managePendingGrn(){
        return view('manage.manage-pending_grn_list');
    }


    public function indexPendingGrn(){

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();           

        $grn_data = DispatchPlan::select(['dispatch_plan.dp_number','dispatch_plan.dp_date','material_request.mr_number','material_request.mr_date',       
        // DB::raw("(SELECT loading_entry_details.loading_qty - (SELECT IFNULL(SUM(gid.grn_qty),0)
        // FROM material_receipt_grn_details AS gid WHERE gid.dc_details_id = dispatch_plan_details.dp_details_id)) as pend_plan_qty"), 

        DB::raw("SUM(loading_entry_details.loading_qty - (
            SELECT IFNULL(SUM(gid.grn_qty),0)
            FROM material_receipt_grn_details AS gid
            WHERE gid.dc_details_id = dispatch_plan_details.dp_details_id
        )) as pend_plan_qty")
         ])
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
        ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
        ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')        
        ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
        ->leftJoin('locations','locations.id', 'sales_order.current_location_id')      
        ->where('sales_order.to_location_id',$locationCode->id)
        ->where('locations.id', '!=', $locationCode->id)
        ->whereIn('dispatch_plan.year_id',$yearIds) 
        ->whereNotNull('sales_order_details.mr_details_id')   
        ->whereNotNull('loading_entry_details.dp_details_id')   
        ->groupBy('dispatch_plan.dp_number', 'material_request.mr_number')
        ->having('pend_plan_qty','>','0');


      
    
       return DataTables::of($grn_data)
    
       ->editColumn('dp_date', function($grn_data){           
           if ($grn_data->dp_date != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d', $grn_data->dp_date)->format('d/m/Y'); 
               
               return $formatedDate1;
    
           }else{
               return '';
           }
       })

       ->editColumn('mr_date', function($grn_data){           
           if ($grn_data->mr_date != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d', $grn_data->mr_date)->format('d/m/Y'); 
               
               return $formatedDate1;
    
           }else{
               return '';
           }
       })  
    
       ->rawColumns(['dp_date','mr_date'])
       ->make(true);
    
    }




    // public function getDcLocation(Request $request){    
    //     // $year_data = getCurrentYearData();
    //     $locationCode = getCurrentLocation();
    //     $yearIds = getCompanyYearIdsToTill();        
       
    //     $get_dc_location = DispatchPlanDetails::select('locations.location_name','locations.id',
    //     // DB::raw("(SELECT dispatch_plan_details.plan_qty - (SELECT IFNULL(SUM(gid.grn_qty),0) FROM material_receipt_grn_details AS gid WHERE gid.dc_details_id = dispatch_plan_details.dp_details_id)) as pend_plan_qty"),   
    //     DB::raw("(SELECT loading_entry_details.loading_qty - (SELECT IFNULL(SUM(gid.grn_qty),0)
    //     FROM material_receipt_grn_details AS gid WHERE gid.dc_details_id = dispatch_plan_details.dp_details_id)) as pend_plan_qty"),      
    //     )
    //     ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
    //     ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
    //     ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
    //     ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
    //     ->leftJoin('locations','locations.id', 'sales_order.current_location_id')
    //     // ->leftJoin('locations','locations.id', 'dispatch_plan.current_location_id')
    //     ->where('sales_order.to_location_id',$locationCode->id)
    //     ->where('locations.id', '!=', $locationCode->id)
    //     // ->where('dispatch_plan.year_id',$year_data->id) 
    //     ->whereNotNull('sales_order_details.mr_details_id')             
    //     ->whereIn('dispatch_plan.year_id',$yearIds)               
    //     ->having('pend_plan_qty','>',0)
    //     ->get(); 
        
    //     $get_dc_location = $get_dc_location->unique(['id']);
    //     $get_dc_location = $get_dc_location->values()->all();
        
    //     return response()->json([
    //         'response_code' => 1,
    //         'get_dc_location'  => $get_dc_location,
    //     ]);
    // }


    //  public function getDcListForGrn(Request $request)
    // {
    //     $year_data = getCurrentYearData();
    //     $yearIds = getCompanyYearIdsToTill();
    //     $locationCode = getCurrentLocation();

    //     if(isset($request->id)){
    //         $dp_id = GRNMaterialDetails::select('dc_details_id')->where('grn_id','=',$request->id)->get();  

    //         $edit_dp_data = GRNMaterialDetails::select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.dp_details_id','dispatch_plan_details.plan_qty','material_receipt_grn_details.grn_qty as pend_plan_qty','material_request.mr_number','material_request.mr_date',
    //         'items.item_name' ,'units.unit_name', 'items.item_code','item_groups.item_group_name',
    //         'loading_entry.vehicle_no','transporters.transporter_name',
    //         // DB::raw("((SELECT IFNULL(SUM(gid.grn_qty),0)
    //         // FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"), 
    //         'material_receipt_grn_details.grn_details_id'
    //         ])
    //         ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
    //         ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
    //         ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
    //         ->leftJoin('loading_entry','loading_entry.le_id','=','loading_entry_details.le_id')
    //         ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
    //         ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
    //         ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
    //         ->leftJoin('items','items.id','=','dispatch_plan_details.item_id')           
    //         ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')    
    //         ->leftJoin('transporters','transporters.id','=','loading_entry.transporter_id')       
    //         ->leftJoin('units','units.id','=','items.unit_id')
    //         ->where('material_receipt_grn_details.grn_id',$request->id)
    //         ->get();           

    //     }

    //     $dc_data = DispatchPlan::select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.dp_details_id','dispatch_plan_details.plan_qty','items.item_name' ,'units.unit_name',
    //     'items.item_code', 'item_groups.item_group_name', 'locations.location_name','material_request.mr_number','material_request.mr_date','loading_entry.vehicle_no','transporters.transporter_name',
    //     // DB::raw("(SELECT dispatch_plan_details.plan_qty - (SELECT IFNULL(SUM(gid.grn_qty),0)
    //     // FROM material_receipt_grn_details AS gid WHERE gid.dc_details_id = dispatch_plan_details.dp_details_id)) as pend_plan_qty"), 
    //     DB::raw("(SELECT loading_entry_details.loading_qty - (SELECT IFNULL(SUM(gid.grn_qty),0)
    //     FROM material_receipt_grn_details AS gid WHERE gid.dc_details_id = dispatch_plan_details.dp_details_id)) as pend_plan_qty"), 
    //      ])
    //     ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
    //     ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','dispatch_plan_details.dp_details_id')
    //     ->leftJoin('loading_entry','loading_entry.le_id','=','loading_entry_details.le_id')
    //     ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
    //     ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
    //     ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
    //     ->leftJoin('items','items.id','=','dispatch_plan_details.item_id')           
    //     ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
    //     ->leftJoin('units','units.id','=','items.unit_id')
    //     ->leftJoin('transporters','transporters.id','=','loading_entry.transporter_id')
    //     ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
    //     ->leftJoin('locations','locations.id', 'sales_order.current_location_id')      
    //     // ->leftJoin('locations','locations.id','=','dispatch_plan.current_location_id')        
    //     ->where('dispatch_plan.current_location_id','=',$request->grn_location_id)
    //     ->where('sales_order.to_location_id',$locationCode->id)
    //     ->where('locations.id', '!=', $locationCode->id)
    //     // ->where('dispatch_plan.year_id',$year_data->id) 
    //     ->whereIn('dispatch_plan.year_id',$yearIds) 
    //     ->whereNotNull('sales_order_details.mr_details_id')   
    //     ->whereNotNull('loading_entry_details.dp_details_id')   
    //     ->having('pend_plan_qty','>','0')     
    //     ->get();

    //     if(isset($edit_dp_data)){
    //         $data = collect($dc_data)->merge($edit_dp_data);
    //         $grouped = $data->groupBy('dp_details_id');    
            

    //         $merged = $grouped->map(function ($items) {
    //             return $items->reduce(function ($carry, $item) {
    //                 if (!$carry) {
    //                     return $item;
    //                 }
    //                 $carry->pend_plan_qty += (float) $item->pend_plan_qty;
    //                 return $carry;
    //             });
    //         });
    
    //         $dc_data = $merged->values();   

    //     }



    //     if ($dc_data != null) {
    //         foreach ($dc_data as $cpKey => $cpVal) {
    //             if ($cpVal->dp_date != null) {
    //                 $cpVal->dp_date = Date::createFromFormat('Y-m-d', $cpVal->dp_date)->format('d/m/Y');
    //             }
    //             if ($cpVal->mr_date != null) {
    //                 $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
    //             }
                
    //             if(isset($cpVal->grn_details_id)){
    //                 $inUse = QCApproval::select('grn_details_id')       
    //                 ->where('grn_details_id','=',$cpVal->grn_details_id)->get();
    
    //                 if($inUse->isNotEmpty()){
    //                     $cpVal->in_use = true;
    //                 }else{
    //                     $cpVal->in_use = false;
    //                 }
    //             }else{
    //                 $cpVal->in_use = false;
    //             }

              
               
    //         }
    //     }


    //     $dc_data = $dc_data->sortBy('dp_details_id')->values();

    //     if ($dc_data != null) {
    //         return response()->json([
    //             'response_code' => 1,
    //             'dc_data'  => $dc_data,            
    //         ]);
    //     }else{
    //         return response()->json([
    //             'response_code' => 1,
    //             'dc_data'  => [],            
    //         ]);

    //     }

    // }

//        public function getDcPartDataForGrn(Request $request){

//         $yearIds = getCompanyYearIdsToTill();
//         $locationCode = getCurrentLocation();

//         $request->dc_detail_ids = explode(',', $request->dc_detail_ids);


//         if(isset($request->id)){
//             $dp_id = GRNMaterialDetails::select('dc_details_id')->where('grn_id','=',$request->id)->get();          

//             $edit_dp_data = GRNMaterialDetails::select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.dp_details_id','dispatch_plan_details.plan_qty',
//             // 'material_receipt_grn_details.grn_qty as pend_plan_qty',
//             'material_request.mr_number','material_request.mr_date','material_receipt_grn_details.item_id',
//             'items.item_name' ,'units.unit_name', 'items.item_code','item_groups.item_group_name',
//             'sales_order_details.rate_per_unit', 'material_receipt_grn_details.qc_required','material_receipt_grn_details.service_item',
//             ])                
//             ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
//             ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
//             ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
//             ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
//             ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
//             ->leftJoin('items','items.id','=','dispatch_plan_details.item_id')           
//             ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
//             ->leftJoin('units','units.id','=','items.unit_id')
//             ->whereIn('material_receipt_grn_details.dc_details_id', $request->dc_detail_ids)
//             ->where('material_receipt_grn_details.grn_id','=',$request->id)
//             ->get();            

//         }


//         $dc_data =  DispatchPlan::select(['dispatch_plan.dp_number','dispatch_plan.dp_date',
//         DB::raw("(SELECT dispatch_plan_details.plan_qty  - (SELECT IFNULL(SUM(gid.grn_qty),0)
//         FROM material_receipt_grn_details AS gid WHERE gid.dc_details_id = dispatch_plan_details.dp_details_id )) as pend_plan_qty"),'units.unit_name','dispatch_plan_details.dp_details_id', 'item_groups.item_group_name', 'units.unit_name','items.item_name' ,'dispatch_plan_details.item_id as item_id','items.item_code','material_request.mr_number','material_request.mr_date', 'dispatch_plan_details.plan_qty','sales_order_details.rate_per_unit','items.qc_required' ,'items.service_item' ,

//         ])
//         ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
//         ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
//         ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
//         ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
//         ->leftJoin('items','items.id','=','dispatch_plan_details.item_id')
//         ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')       
//         ->leftJoin('units','units.id','=','items.unit_id')
//         ->whereIn('dispatch_plan_details.dp_details_id', $request->dc_detail_ids)
//         // ->where('dispatch_plan.current_location_id',$locationCode->id)
//         // ->where('locations.id', '!=', $locationCode->id)
//         ->whereIn('dispatch_plan.year_id',$yearIds)  
//         ->having('pend_plan_qty','>',0)      
//         ->get();

//         if(isset($edit_dp_data)){
//             $data = collect($dc_data)->merge($edit_dp_data);
//             $grouped = $data->groupBy('dp_details_id');    

//             $merged = $grouped->map(function ($items) {
//                 return $items->reduce(function ($carry, $item) {
//                     if (!$carry) {
//                         return $item;
//                     }
//                     $carry->pend_plan_qty += (float) $item->pend_plan_qty;
//                     return $carry;
//                 });
//             });
    
//             $dc_data = $merged->values();   

//         }

      
//         if ($dc_data != null) {
//             $changedItemIds = [];
//             foreach ($dc_data as $cpKey => $cpVal) {

//                 if ($cpVal->dp_date != null) {
//                     $cpVal->dp_date = Date::createFromFormat('Y-m-d', $cpVal->dp_date)->format('d/m/Y');
//                 }
//                 if ($cpVal->mr_date != null) {
//                     $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
//                 }

//                 if(isset($request->id)){  
//                     $grn_qty =   GRNMaterialDetails::where('dc_details_id','=',$cpVal->dp_details_id)->where('grn_id',$request->id)->sum('grn_qty');
//                     $grn_id = GRNMaterialDetails::where('dc_details_id','=',$cpVal->dp_details_id)->where('grn_id',$request->id)->first();

//                     $cpVal->grn_qty = $grn_qty;
//                     $cpVal->grn_details_id = $grn_id!= null ? $grn_id->grn_details_id : 0;                      
//                     $cpVal->rate_per_unit = $grn_id!= null ? $grn_id->rate_per_unit : $cpVal->rate_per_unit;             
                    
//                     if($grn_id!= null){
//                         $isFound = QCApproval::where('qc_approval.grn_details_id',$grn_id->grn_details_id)->sum('qc_qty');
                    
//                         if($isFound){
//                             $cpVal->in_use = true;
//                             $cpVal->used_qty = $isFound;
//                         }else{
//                             $cpVal->in_use = false;
//                             $cpVal->used_qty = 0;
//                         }
//                     }else{
//                         $cpVal->in_use = false;
//                             $cpVal->used_qty = 0;
//                     }
                    
//                 }else{
//                     $cpVal->grn_qty = 0;
//                     $cpVal->grn_details_id = 0;
//                     $cpVal->in_use = false;
//                     $cpVal->used_qty = 0;
//                 }

//                 $changedItemId = Item::where('id', $cpVal->item_id)
//                 ->where(function($query) {
//                     $query->where('items.status', 'deactive')
//                     ->orWhere('items.service_item', 'Yes');                     
//                 })
//                 ->pluck('id')
//                 ->first();
//                 if ($changedItemId) {
//                     $changedItemIds[] = $changedItemId; // Now it works
//                 }
              
//             }
//         }
// // dd($po_data);

//         $dc_data = $dc_data->sortBy('dp_details_id')->values();

//         if($changedItemIds){
//             $item = Item::select('id','item_name')->whereIN('id',$changedItemIds)->get();
//         }else{
//             $item = '';
//         }

        
//         if ($dc_data != null) {
//             return response()->json([
//                 'response_code' => '1',
//                 'dc_data' => $dc_data,
//                 'item' => $item,
               
//             ]);
//         } else {
//             return response()->json([
//                 'response_code' => '1',
//                 'dc_data' => []
//             ]);
//         }
//     }


}