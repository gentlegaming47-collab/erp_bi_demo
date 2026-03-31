<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReplacementItemDecision;
use App\Models\ReplacementItemDecisionDetails;
use App\Models\SOMapping;
use App\Models\SOMappingDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Date;
use Carbon\Carbon;
use DataTables;
use App\Models\Admin;
use Illuminate\Validation\Rule;

class ReplacementItemDecisionController extends Controller
{
    public function manage()
    {
        return view('manage.manage-replacement_item_decision');
    }

    public function index(ReplacementItemDecision $replacement_item_decision,Request $request,DataTables $dataTables){
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();

        $replacement_decision = ReplacementItemDecision::select(['replacement_item_decision.replacement_id','replacement_item_decision.replacement_type_value_fix', 'items.item_name','items.item_code', 'item_groups.item_group_name', 'units.unit_name','replacement_item_decision_details.decision_qty','so_mapping.customer_name','replacement_item_decision.last_by_user_id','replacement_item_decision.last_on','replacement_item_decision.created_by_user_id','replacement_item_decision.created_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'        
        ])
        ->leftJoin('replacement_item_decision_details','replacement_item_decision_details.replacement_id', 'replacement_item_decision.replacement_id')
        ->leftJoin('so_mapping_details','so_mapping_details.so_mapping_details_id', 'replacement_item_decision_details.so_mapping_details_id')       
        ->leftJoin('so_mapping','so_mapping.mapping_id', 'so_mapping_details.mapping_id')       
        ->leftJoin('items','items.id', 'replacement_item_decision_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        ->leftJoin('units','units.id', 'items.unit_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'replacement_item_decision.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'replacement_item_decision.last_by_user_id')
        ->where('replacement_item_decision.current_location_id','=',$location->id)
        ->where('replacement_item_decision.year_id','=',$year_data->id);

        
        return DataTables::of($replacement_decision)
        ->editColumn('created_by_user_id', function($replacement_decision){
            if($replacement_decision->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$replacement_decision->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($replacement_decision){
            if($replacement_decision->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$replacement_decision->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('created_on', function($replacement_decision){
            if ($replacement_decision->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $replacement_decision->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('replacement_item_decision.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(replacement_item_decision.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($replacement_decision){
            if ($replacement_decision->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $replacement_decision->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('replacement_item_decision.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(replacement_item_decision.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('mapping_date', function($replacement_decision){
            if ($replacement_decision->mapping_date != null) {
                $date = Date::createFromFormat('Y-m-d', $replacement_decision->mapping_date)->format(DATE_FORMAT);
                
                return $date;
            }else{
                return '';
            }
        })
        ->filterColumn('so_mapping.mapping_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(so_mapping.mapping_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('decision_qty', function($replacement_decision){

            return $replacement_decision->decision_qty > 0 ? number_format((float)$replacement_decision->decision_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
        ->editColumn('item_name', function($replacement_decision){ 
            if($replacement_decision->item_name != ''){
                $item_name = ucfirst($replacement_decision->item_name);
                return $item_name;
            }else{
                return '';
            }
        })
        ->addColumn('options',function($replacement_decision){
            $action = "<div>";
            if(hasAccess("dispatch_plan","edit")){
            $action .="<a id='edit_a' href='".route('edit-replacement_item_decision',['id' => base64_encode($replacement_decision->replacement_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("po_short_close","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'mapping_date','decision_qty', 'options'])
        ->make(true);
    }

    public function create()
    {
        return view('add.add-replacement_item_decision');
    }

    public function store(Request $request)
    {
        // dd($request);
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

        if($request->replacement_fix_id == 1 ){
            $replacementType = "Store" ;
        }else{
            $replacementType = "Scrap" ;
        }
 
        DB::beginTransaction();        

        try{
            // first save dispatch form
            $replacement =  ReplacementItemDecision::create([
                'replacement_type_id_fix'   => $request->replacement_fix_id,            
                'replacement_type_value_fix' => $replacementType,               
                'current_location_id' => $locationID,                
                'year_id'             => $year_data->id,
                'company_id'          => Auth::user()->company_id,
                'created_on'          => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id'  => Auth::user()->id
            ]);
            if($replacement){

                $request->so_mapping_details = json_decode($request->so_mapping_details,true);

                if(isset($request->so_mapping_details) && !empty($request->so_mapping_details)){
                    foreach($request->so_mapping_details as $ctKey => $ctVal){
                        if(isset($ctVal['so_mapping_details_id'])){

                            $soMapQtySum = round(SOMappingDetails::where('so_mapping_details_id',$ctVal['so_mapping_details_id'])->sum('map_qty'),3);
    
                            $usedecQtySum = round(ReplacementItemDecisionDetails::where('so_mapping_details_id',$ctVal['so_mapping_details_id'])->sum('decision_qty'),3);
    
                            $decisionQty = isset($ctVal['decision_qty']) && $ctVal['decision_qty'] > 0 ? $ctVal['decision_qty'] : 0;
                            $desQtySum = $usedecQtySum + $decisionQty;                          
    
                            if($soMapQtySum < $desQtySum){
                                DB::rollBack();
                                return response()->json([
                                    'response_code' => '0',
                                    'response_message' => 'Decision Qty. Is Used',                               
                                ]);
                            }
    
                        }
                        
                        if($ctVal != null){
                            $replacement_details =  ReplacementItemDecisionDetails::create([
                                'replacement_id' => $replacement->replacement_id,
                                'so_mapping_details_id'=> (isset($ctVal['so_mapping_details_id']) &&  $ctVal['so_mapping_details_id'] != "") ? $ctVal['so_mapping_details_id'] : null,
                                'item_id'=>(isset($ctVal['item_id']) != '' ? $ctVal['item_id'] : ''),
                                'decision_qty'=>(isset($ctVal['decision_qty']) && $ctVal['decision_qty'] > 0) ? $ctVal['decision_qty'] : 0,   

                                'item_details_id'=>(isset($ctVal['item_details_id']) && $ctVal['item_details_id'] != '') ? $ctVal['item_details_id'] : null,                                  
                                'decision_detail_qty'=>(isset($ctVal['decision_detail_qty']) && $ctVal['decision_detail_qty'] != '') ? $ctVal['decision_detail_qty'] : null,       
                                'status'=> 'Y',                               
                            ]);

                            if($request->replacement_fix_id == 1){
                                if(isset($ctVal['item_details_id']) && $ctVal['item_details_id'] != ''){
                                   stockDetailsEffect($locationID,$ctVal['item_details_id'],$ctVal['item_details_id'],$ctVal['decision_detail_qty'],0,'add','U','ReplacementItemDecision',$replacement_details->replacement_details_id,'Yes','ReplacementItemDecision',$replacement_details->replacement_details_id );

                                }else{
                                  stockEffect($locationID,$ctVal['item_id'],$ctVal['item_id'],$ctVal['decision_qty'],0,'add','U','ReplacementItemDecision',$replacement_details->replacement_details_id);
                                }

                              
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
                        'response_code' => '0',
                        'response_message' => 'Record Not Inserted',
                    ]);
                }
            }else {
                 DB::rollBack();
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Inserted.',
                ]);
            }
        }catch(\Exception $e){

            DB::rollBack();    
            getActivityLogs("Replacement Item Decision", "add", $e->getMessage(),$e->getLine());  
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
        return view('edit.edit-replacement_item_decision')->with(['id' => $id ]);
    }


    public function edit($id)
    {
        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation()->id;

        $replacement_data =  ReplacementItemDecision::where('replacement_id',$id)->first();


        $replacement_details = ReplacementItemDecisionDetails::select(['replacement_item_decision_details.replacement_details_id','replacement_item_decision_details.replacement_id','replacement_item_decision_details.so_mapping_details_id','replacement_item_decision_details.item_id','replacement_item_decision_details.decision_qty','so_mapping.so_mapping_number','so_mapping.mapping_date','so_mapping.customer_name','items.item_name','items.item_code', 'item_groups.item_group_name',  'so_mapping_details.map_qty','so_mapping_details.item_detail_qty' ,'replacement_item_decision_details.decision_detail_qty','replacement_item_decision_details.item_details_id' ,'item_details.secondary_qty',
        DB::raw("CASE WHEN item_details.item_details_id IS NOT NULL THEN units2.unit_name ELSE units1.unit_name END as unit_name"),         
        ])
        ->leftJoin('so_mapping_details','so_mapping_details.so_mapping_details_id','=','replacement_item_decision_details.so_mapping_details_id')
        ->leftJoin('so_mapping','so_mapping.mapping_id','=','so_mapping_details.mapping_id')
        ->leftJoin('items','items.id','=','replacement_item_decision_details.item_id')
         ->leftJoin('item_details','item_details.item_details_id','=','replacement_item_decision_details.item_details_id')    
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        // ->leftJoin('units','units.id','=','items.unit_id')
         ->leftJoin('units as units1','units1.id','=','items.unit_id')
        ->leftJoin('units as units2','units2.id','=','items.second_unit')        
        ->where('replacement_item_decision_details.replacement_id',$id)
        ->get();
               
// dd($replacement_details);
        if($replacement_details != null){
         
            foreach($replacement_details as $rKey=>$rVal){
                if ($rVal->mapping_date != null) {
                    $rVal->mapping_date = Date::createFromFormat('Y-m-d', $rVal->mapping_date)->format('d/m/Y');
                }

                $newRequest = new Request();
                $newRequest->so_mapping_details_id = $rVal->so_mapping_details_id;
                $newRequest->record_id = $rVal->replacement_id;
                $newRequest->total_qty = $rVal->map_qty;
                $newRequest->total_detail_qty = $rVal->item_detail_qty;
                $rVal->show_pend_qty = self::getPendingQty($newRequest);
                $rVal->show_pend_detail_qty = self::getPendingDetailQty($newRequest);

            }
           
        }


        if ($replacement_data != null) {
                return response()->json([
                    'response_code' => '1',
                    'replacement_data' => $replacement_data,
                    'replacement_details' => $replacement_details
                ]);
        } else {
                return response()->json([
                    'response_code' => '0',
                    'replacement_data' => []
                ]);
        }
    }

    public function update(Request $request){

        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

        if($request->replacement_fix_id == 1 ){
            $replacementType = "Store" ;
        }else{
            $replacementType = "Scrap" ;
        }
 
        DB::beginTransaction();        

        try{
            $replacement =  ReplacementItemDecision::where('replacement_id',$request->id)->update([
                'replacement_type_id_fix'   => $request->replacement_fix_id,            
                'replacement_type_value_fix' => $replacementType,               
                'current_location_id' => $locationID,                
                'year_id'             => $year_data->id,
                'company_id'          => Auth::user()->company_id,
                'last_by_user_id'    => Auth::user()->id,
                'last_on'            => Carbon::now('Asia/Kolkata')->toDateTimeString(), 
            ]);
            if($replacement){

                $request->so_mapping_details = json_decode($request->so_mapping_details,true);

                $oldReplacementDetails = ReplacementItemDecisionDetails::where('replacement_id','=',$request->id)->update([
                    'status' => 'D'
                ]);

                // $oldReplacementDetails = ReplacementItemDecisionDetails::where('replacement_id','=',$request->id)->get();
                // $oldReplacementDetailsData = [];
                // if($oldReplacementDetails != null){
                //     $oldReplacementDetailsData = $oldReplacementDetails->toArray();
                // }

                if(isset($request->so_mapping_details) && !empty($request->so_mapping_details)){
                    foreach($request->so_mapping_details as $ctKey => $ctVal){                    
                        if($ctVal != null){
                            $replacement_details =  ReplacementItemDecisionDetails::where('replacement_details_id',$ctVal['replacement_details_id'])->where('replacement_id',$request->id)->update([
                                'replacement_id' => $request->id,
                                'so_mapping_details_id'=> (isset($ctVal['so_mapping_details_id']) &&  $ctVal['so_mapping_details_id'] != "") ? $ctVal['so_mapping_details_id'] : null,
                                'item_id'=>(isset($ctVal['item_id']) != '' ? $ctVal['item_id'] : ''),
                                'decision_qty'=>(isset($ctVal['decision_qty']) && $ctVal['decision_qty'] > 0) ? $ctVal['decision_qty'] : 0,   
                                
                                 'item_details_id'=>(isset($ctVal['item_details_id']) && $ctVal['item_details_id'] != '') ? $ctVal['item_details_id'] : null,                                  
                                'decision_detail_qty'=>(isset($ctVal['decision_detail_qty']) && $ctVal['decision_detail_qty'] != '') ? $ctVal['decision_detail_qty'] : null,      
                                'status'=> 'Y',      
                            ]);

                            if($request->replacement_fix_id == 1){                     

                                if(isset($ctVal['item_details_id']) && $ctVal['item_details_id'] != ''){
                                   stockDetailsEffect($locationID,$ctVal['item_details_id'],$ctVal['item_details_id'],$ctVal['decision_detail_qty'],$ctVal['org_decision_detail_qty'],'edit','U','ReplacementItemDecision',$ctVal['replacement_details_id'],'Yes','ReplacementItemDecision',$ctVal['replacement_details_id']);

                                }else{
                                 stockEffect($locationID,$ctVal['item_id'],$ctVal['item_id'],$ctVal['decision_qty'],$ctVal['org_decision_qty'],'edit','U','ReplacementItemDecision',$ctVal['replacement_details_id']);
                                }
                            }

                            // foreach ($oldReplacementDetailsData as $key => $value) {
                            //     if ($value['replacement_details_id'] == $ctVal['replacement_details_id']) {
                            //         unset($oldReplacementDetailsData[$key]);

                            //     }
                            // }     
                        }
                    }

                    // if(isset($oldReplacementDetailsData) && !empty($oldReplacementDetailsData)){
                    //     foreach($oldReplacementDetailsData as $gkey=>$gval){
                    //         ReplacementItemDecisionDetails::where('replacement_details_id', $gval['replacement_details_id'])->delete();
                    //         stockEffect($locationID,$gval['item_id'],$gval['item_id'],0, $gval['decision_qty'],'delete','U','ReplacementItemDecision',$gval['replacement_details_id']);
                    //         unset($oldReplacementDetailsData[$gkey]);
                    //     }
                    // }

                    $oldReplacementDetailsData = ReplacementItemDecisionDetails::where('replacement_id','=',$request->id)->where('status','D')->get();

                    if($oldReplacementDetailsData->isNotEmpty()){
                        foreach($oldReplacementDetailsData as $key=>$val){
                            if(isset($val['item_details_id']) != null){
                                stockDetailsEffect($locationID,$val['item_details_id'],$val['item_details_id'],0,$val['decision_detail_qty'],'delete','U','ReplacementItemDecision',$val['replacement_details_id'],'Yes','ReplacementItemDecision',$val['replacement_details_id']);

                            }else{
                                stockEffect($locationID,$val['item_id'],$val['item_id'],0,$val['decision_qty'],'delete','U','ReplacementItemDecision',$val['replacement_details_id']);
                            }
                            
                        }
                    }

                    $replaceDetails = ReplacementItemDecisionDetails::where('replacement_id','=',$request->id)->where('status','D')->delete();


                    DB::commit();
                    return response()->json([
                        'response_code' => '1',
                        'response_message' => 'Record Updated Successfully.',
                    ]);
                }else{
                     DB::rollBack();
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Record Not Updated.',
                    ]);
                }
            }else {
                DB::rollBack();
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Updated.',
                ]);
            }
        }catch(\Exception $e){

            DB::rollBack();   
            getActivityLogs("Replacement Item Decision", "update", $e->getMessage(),$e->getLine());           
            if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Error Occured Record Not Updated',
                    'original_error' => $e->getMessage()
                ]);
            }
        }
    }



    
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
             
            $locationID = getCurrentLocation()->id;

            $replacement_data =  ReplacementItemDecision::where('replacement_id',$request->id)->first();

            if($replacement_data->replacement_type_id_fix == 1){
                
                $oldReplacementDetails = ReplacementItemDecisionDetails::where('replacement_id','=',$request->id)->get();
                $oldReplacementDetailsData = [];
                if($oldReplacementDetails != null){
                    $oldReplacementDetailsData = $oldReplacementDetails->toArray();
                }
                
                foreach($oldReplacementDetailsData as $gkey=>$gval){ 
                    if(isset($gval['item_details_id']) != null){
                        stockDetailsEffect($locationID,$gval['item_details_id'],$gval['item_details_id'],0,$gval['decision_detail_qty'],'delete','U','ReplacementItemDecision',$gval['replacement_details_id'],'Yes','ReplacementItemDecision',$gval['replacement_details_id']);

                    }else{
                        stockEffect($locationID,$gval['item_id'],$gval['item_id'],0,$gval['decision_qty'],'delete','U','ReplacementItemDecision',$gval['replacement_details_id']);
                    }
                }    
            }         
           
            ReplacementItemDecisionDetails::where('replacement_id',$request->id)->delete();
            ReplacementItemDecision::destroy($request->id);

            DB::commit();
 
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){

            DB::rollBack(); 
            getActivityLogs("Replacement Item Decision", "delete", $e->getMessage(),$e->getLine());  
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


    

    public function getSoMappingData(Request $request)
    {
        // $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();

        $locationCode = getCurrentLocation()->id;
        
        $so_mapping = SOMappingDetails::select(['so_mapping_details.so_mapping_details_id','so_mapping.so_mapping_number','so_mapping.mapping_date','so_mapping.customer_name', 'items.item_name',
        'items.item_code', 'item_groups.item_group_name', 'so_mapping.item_id',
        DB::raw("((SELECT so_mapping_details.map_qty -  IFNULL(SUM(replacement_item_decision_details.decision_qty),0) FROM replacement_item_decision_details  WHERE replacement_item_decision_details.so_mapping_details_id = so_mapping_details.so_mapping_details_id)) as pend_so_map_qty"),

        DB::raw("((SELECT so_mapping_details.item_detail_qty -  IFNULL(SUM(replacement_item_decision_details.decision_detail_qty),0) FROM replacement_item_decision_details  WHERE replacement_item_decision_details.so_mapping_details_id = so_mapping_details.so_mapping_details_id)) as pend_so_map_details_qty"),

        DB::raw("CASE WHEN item_details.item_details_id IS NOT NULL THEN units2.unit_name ELSE units1.unit_name END as unit_name") ,'item_details.secondary_item_name','so_mapping_details.item_details_id','item_details.secondary_qty',     
        ])
        ->leftJoin('so_mapping','so_mapping.mapping_id', 'so_mapping_details.mapping_id')
        ->leftJoin('items','items.id', 'so_mapping.item_id')
        ->leftJoin('item_details','item_details.item_details_id', 'so_mapping_details.item_details_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        // ->leftJoin('units','units.id', 'items.unit_id')
        ->leftJoin('units as units1','units1.id','=','items.unit_id')
        ->leftJoin('units as units2','units2.id','=','items.second_unit')
        // ->where('so_mapping.year_id',$year_data->id)
        ->whereIn('so_mapping.year_id',$yearIds)
        ->where('so_mapping.current_location_id',$locationCode)
        ->having('pend_so_map_qty','>', 0)
        ->get();


        if($so_mapping != null){
            foreach($so_mapping as $cpKey => $cpVal){    
                if ($cpVal->mapping_date != null) {
                    $cpVal->mapping_date = Date::createFromFormat('Y-m-d', $cpVal->mapping_date)->format('d/m/Y');
                }            
            }
        }
      

        if ($so_mapping != null) {
            return response()->json([
                'response_code' => '1',
                'so_mapping' => $so_mapping
            ]);
        } else {
                return response()->json([
                    'response_code' => '0',
                    'so_mapping' => []
                ]);
        }
    }



    public function getPendingQty(Request $request){
        $exectQty = $request->total_qty;
       
        $oldRecords = ReplacementItemDecisionDetails::select(DB::raw('SUM(decision_qty) as sum'))
        ->where('so_mapping_details_id','=',$request->so_mapping_details_id)
        // ->where('replacement_id','=',$request->record_id)
        ->groupBy(['so_mapping_details_id'])
        ->first();

        if($oldRecords != null){

            $diff = $exectQty - $oldRecords->sum;

			return $diff;
        }else{
            return abs($exectQty);
        }


    }

    public function getPendingDetailQty(Request $request){
        $exectQty = $request->total_detail_qty;
       
        $oldRecords = ReplacementItemDecisionDetails::select(DB::raw('SUM(decision_detail_qty) as sum'))
        ->where('so_mapping_details_id','=',$request->so_mapping_details_id)
        // ->where('replacement_id','=',$request->record_id)
        ->groupBy(['so_mapping_details_id'])
        ->first();

        if($oldRecords != null){

            $diff = $exectQty - $oldRecords->sum;

			return $diff;
        }else{
            return abs($exectQty);
        }


    }
}