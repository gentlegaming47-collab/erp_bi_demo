<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoadingEntry;
use App\Models\LoadingEntryDetails;
use App\Models\DispatchPlan;
use App\Models\SalesOrderDetail;
use App\Models\SalesOrderDetailsDetails;
use App\Models\DispatchPlanDetails;
use App\Models\DispatchPlanDetailsDetails;
use App\Models\GRNMaterialDetails;
use App\Models\SOShortClose;
use App\Models\Admin;
use App\Models\Transporter;
use Carbon\Carbon;
use DataTables;
use Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class LoadingEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function manage()
    {
        return view('manage.manage-loading_entry');
    }

    public function index(LoadingEntry $loading_data,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();

    //     $loading_data = LoadingEntry::select(['loading_entry.le_id','dispatch_plan.dp_id','dispatch_plan.dp_date as dp_date','dispatch_plan.dp_number','loading_entry_details.loading_qty','loading_entry.le_id','loading_entry.vehicle_no','loading_entry.transporter_id','loading_entry.loading_by','loading_entry.driver_name','loading_entry.driver_mobile_no','transporters.transporter_name','items.item_name','items.item_code', 'item_groups.item_group_name', 
    //     'loading_entry.last_by_user_id','loading_entry.last_on','loading_entry.created_by_user_id','loading_entry.created_on' ]) 
    //     ->leftJoin('loading_entry_details','loading_entry_details.le_id','=','loading_entry.le_id')
    //     ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id', 'loading_entry_details.dp_details_id')
    //     ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')
    //     ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')
    //     ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
    //     ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
    //     ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
    //     ->leftJoin('items','items.id', 'dispatch_plan_details.item_id')
    //     ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
    //     ->leftJoin('units','units.id', 'items.unit_id')
    //    ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
    //    ->leftJoin('transporters','transporters.id', 'loading_entry.transporter_id')
    //     ->where('loading_entry.current_location_id','=',$location->id)
    //     ->where('loading_entry.year_id','=',$year_data->id);

        $loading_data = LoadingEntry::select(['loading_entry.le_id','dispatch_plan.dp_id','dispatch_plan.dp_date as dp_date','dispatch_plan.dp_number','loading_entry.vehicle_no','loading_entry.transporter_id','loading_entry.loading_by','loading_entry.driver_name','loading_entry.driver_mobile_no','transporters.transporter_name','loading_entry.last_by_user_id','loading_entry.last_on','loading_entry.created_by_user_id','loading_entry.created_on' ]) 
        ->leftJoin('loading_entry_details','loading_entry_details.le_id','=','loading_entry.le_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id', 'loading_entry_details.dp_details_id')
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')
    
        ->leftJoin('transporters','transporters.id', 'loading_entry.transporter_id')
        ->where('loading_entry.current_location_id','=',$location->id)
        ->where('loading_entry.year_id','=',$year_data->id)
        ->groupBy(['dispatch_plan.dp_id']);
    
        return DataTables::of($loading_data)
        ->editColumn('created_by_user_id', function($loading_data){
            if($loading_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$loading_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->editColumn('last_by_user_id', function($loading_data){
            if($loading_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$loading_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->editColumn('created_on', function($loading_data){
            if ($loading_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $loading_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->editColumn('last_on', function($loading_data){
            if ($loading_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $loading_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->editColumn('dp_date', function($loading_data){
            if ($loading_data->dp_date != null) {
                $date = Date::createFromFormat('Y-m-d', $loading_data->dp_date)->format(DATE_FORMAT);
                return $date;
            }else{
                return '';
            }
        })
      
        // ->editColumn('loading_qty', function($loading_data){

        //     return $loading_data->loading_qty > 0 ? number_format((float)$loading_data->loading_qty, 3, '.') : '';
        // })      

        // ->editColumn('item_name', function($loading_data){ 
        //     if($loading_data->item_name != ''){
        //         $item_name = ucfirst($loading_data->item_name);
        //         return $item_name;
        //     }else{
        //         return '';
        //     }
        // })
     
        ->addColumn('options',function($loading_data){
            $action = "<div>";
            if(hasAccess("loading_entry","print")){
                $action .="<a id='print_a' target='_blank' href='".route('print-loading_entry',['id' => base64_encode($loading_data->le_id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
            }
             if(hasAccess("loading_entry","edit")){
             $action .="<a id='edit_a' href='".route('edit-loading_entry',['id' => base64_encode($loading_data->le_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
             }
            if(hasAccess("loading_entry","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        // ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'dp_date', 'loading_qty','options'])
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'dp_date','options'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $getTransporter = Transporter::select('id','transporter_name') ->where('status', '=', 'active')->orderBy('transporter_name', 'asc')->get();
        
        return view('add.add-loading_entry')->with(['getTransporter' =>$getTransporter]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;
        DB::beginTransaction();
        try{
            // first save dispatch form
            $loading_entry =  LoadingEntry::create([
                'current_location_id' => $locationID,                
                'dp_id'         => $request->Dpid,            
                'vehicle_no'           => $request->vehicle_no,
                'transporter_id'       => $request->transporter,
                'loading_by'       => $request->loading_by,
                'driver_name'       => $request->driver_name,
                'driver_mobile_no'       => $request->driver_no,
                'year_id'             => $year_data->id,
                'company_id'          => Auth::user()->company_id,
                'created_on'          => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id'  => Auth::user()->id
            ]);
            if($loading_entry->save()){

                if(isset($request->dp_details_id) && !empty($request->dp_details_id)){


                    foreach($request->dp_details_id as $dpKey => $dpval){

                        if(isset($dpval)){

                            $dpQtySum = round(DispatchPlanDetails::where('dp_details_id',$dpval)->sum('plan_qty'),3);
            
                            $useloadQtySum = round(LoadingEntryDetails::where('dp_details_id',$dpval)->sum('loading_qty'),3);

                            $loadQty = isset($request->plan_qty[$dpKey]) && $request->plan_qty[$dpKey] > 0 ? $request->plan_qty[$dpKey] : 0;
                            $loadQtySum = $useloadQtySum + $loadQty;                          
            
                            if($dpQtySum < $loadQtySum){
                                DB::rollBack();
                                return response()->json([
                                    'response_code' => '0',
                                    'response_message' => 'Plan Qty. Is Used',                               
                                ]);
                            }

                        }

                        $loading_entry_details = LoadingEntryDetails::create([
                            'le_id'    => $loading_entry->le_id,
                            'dp_details_id'   => $dpval,
                            'loading_qty'   => isset($request->plan_qty[$dpKey]) != '' ? $request->plan_qty[$dpKey] : 0,
                            'status'   => 'Y',
                        ]);
                        // if($request->fiting_item[$dpKey]  == 'no'){
                        //     stockEffect($locationID,$request->item_id[$dpKey],$request->item_id[$dpKey],$request->plan_qty[$dpKey],0,'add','U');
                        // }else{
                        //     $dpd_detail = DispatchPlanDetailsDetails::where('dp_details_id',$dpval)->get();
                            
                        //     if(!$dpd_detail->isEmpty()){  
                        //         foreach($dpd_detail as $skey => $sval){                             
                        //            stockEffect($locationID,$sval->item_id,$sval->item_id,$sval->plan_qty,0,'add','U');     
                        //         }                              
                        //     }                          
                                
                        // }                       
                            
                        
                    }


                    // this code use for update dispatch qty         

                    $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->update([                     'status' => 'D',]);

                    $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->get();
                    if($planDetails != null){
                        foreach($planDetails as $okey => $oval){
                            $pdpDetails = DispatchPlanDetailsDetails::where('dp_details_id',$oval->dp_details_id)->update(['status' => 'D',]);
                        }
                    }
                    foreach($request->dp_details_id as $ctKey => $ctVal){
                        if($ctVal != null){
                            $dp_details =  DispatchPlanDetails::where('dp_details_id',$ctVal)->update([               
                                'item_id'=>isset($request->item_id[$ctKey]) != '' ? $request->item_id[$ctKey] : null,
                                'plan_qty'=>isset($request->plan_qty[$ctKey]) != '' ? $request->plan_qty[$ctKey] : 0,
                                'status'=> 'Y',
                            ]);

                            if($request->fiting_item[$ctKey]  == 'no'){
                                stockEffect($locationID,$request->item_id[$ctKey],$request->item_id[$ctKey],$request->plan_qty[$ctKey],$request->org_plan_qty[$ctKey],'edit','D','Loading Dispatch Detail',$ctVal);
                                // stockEffect($locationID,$request->item_id[$ctKey],$request->item_id[$ctKey],$request->plan_qty[$ctKey],$request->plan_qty[$ctKey],'edit','D');
                            }


                            if($ctVal != null){
                                $dpd_detail = DispatchPlanDetailsDetails::where('dp_details_id',$ctVal)->get();

                                if(!$dpd_detail->isEmpty()){

                                    // stockEffect($locationID,$ctVal['item_id'],$ctVal['item_id'],1,0,'add','D');
                                    foreach($dpd_detail as $skey => $sval){
                                        $dpd_details =  DispatchPlanDetailsDetails::where('dpd_details_id',$sval->dpd_details_id)->update([
                                            'dp_details_id' => $sval->dp_details_id,
                                            'so_details_detail_id'=> $sval->so_details_detail_id,
                                            'item_id'=> $sval->item_id,
                                            'plan_qty'=>$sval->plan_qty,                                             
                                            'status'=> 'Y',
                                       ]);
                                       stockEffect($locationID,$sval->item_id,$sval->item_id,$sval->plan_qty,$sval->plan_qty,'edit','D','Loading Dispatch Detail Detail',$sval->dpd_details_id);
                                       
                                    }                          
                                    
                                }
                            }
                        }
                    }

                    $PlanDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->where('status','D')->get();

                    if($PlanDetails != null){
                        foreach($PlanDetails as $okey => $oval){
                            $dpdDetails = DispatchPlanDetailsDetails::where('dp_details_id',$oval->dp_details_id)->where('status','D')->get();

                            if(!$dpdDetails->isEmpty()){                          
                                foreach($dpdDetails as $dkey=>$dval){
                                    $pdpDetails = DispatchPlanDetailsDetails::where('dpd_details_id',$dval->dpd_details_id)->where('status','D')->first();
                                    if($pdpDetails != null){
                                        stockEffect($locationID,$pdpDetails->item_id,$pdpDetails->item_id,0,$pdpDetails->plan_qty,'delete','D','Loading Dispatch Detail Detail',$pdpDetails->dpd_details_id);
                                        DispatchPlanDetailsDetails::where('dpd_details_id',$pdpDetails->dpd_details_id)->where('status','D')->delete();

                                    }                                       
                                }                      
                            } 
                            
                            $dpDetails = DispatchPlanDetails::where('dp_details_id',$oval->dp_details_id)->where('status','D')->first();

                            if($dpDetails != null){
                                if($dpDetails->fitting_item == 'no'){
                                    stockEffect($locationID,$dpDetails->item_id,$dpDetails->item_id,0,$dpDetails->plan_qty,'delete','D','Loading Dispatch Detail',$dpDetails->dp_details_id);
                                }
                               
                                DispatchPlanDetails::where('dp_details_id',$dpDetails->dp_details_id)->where('status','D')->delete();
                            }
                        }
                    }
                }
              
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Inserted Successfully.',
            ]);
               
            }else {
                DB::rollBack();
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Inserted.',
                ]);
            }
        }catch(\Exception $e){

            // DB::rollBack();
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => 'Error Occured Record Not Inserted',
            //     'original_error' => $e->getMessage()
            // ]);

            DB::rollBack();     
            getActivityLogs("Loading Entry", "add", $e->getMessage(),$e->getLine());  
       
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $loding_data = LoadingEntry::where('le_id', '=', base64_decode($id))->get();
        // return view('edit.edit-loading_entry', compact('loding_data', 'id'));

        $tId = DB::table('loading_entry')
        ->where('le_id', base64_decode($id))
        ->value('transporter_id');

        $getTransporter = Transporter::select('transporters.id','transporters.transporter_name')
        ->where(function ($query) use ($tId) {
            $query->where('transporters.id', '=', $tId) 
            ->orWhere(function ($subQuery){
                $subQuery->where('transporters.status', '=', 'active');
            });
         
        })->orderBy('transporters.transporter_name', 'asc')->get();
        
        return view('edit.edit-loading_entry')->with(['id'=>$id,'getTransporter' =>$getTransporter]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $isAnyPartInUse = false;
       $year_data = getCurrentYearData();
       $locationCode = getCurrentLocation()->id;

       $le_data =  LoadingEntry::where('le_id',$id)->first();

       $le_details = LoadingEntryDetails::select(['dispatch_plan_details.dp_details_id','dispatch_plan_details.dp_id','dispatch_plan_details.so_details_id','dispatch_plan_details.item_id','dispatch_plan_details.plan_qty as so_qty','dispatch_plan_details.fitting_item','sales_order.so_number','sales_order.so_date','sales_order.customer_name','sales_order.customer_village','districts.district_name','locations.location_name', 'dealers.dealer_name','items.item_name','items.item_code', 'item_groups.item_group_name',  'units.unit_name','dispatch_plan.dp_number','dispatch_plan.dp_date','loading_entry_details.loading_qty as plan_qty','loading_entry_details.le_details_id',
        DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),'sales_order_details.so_details_id',  'sales_order_details.so_qty',   

        // DB::raw("((SELECT IFNULL(SUM(sales_order_details.so_qty),0) FROM sales_order_details WHERE so_details_id  = dispatch_plan_details.so_details_id ) - loading_entry_details.loading_qty) as pending_so_qty"), 

        // DB::raw("(dispatch_plan_details.plan_qty - loading_entry_details.loading_qty) as pending_so_qty"),  

        // DB::raw("(SELECT sales_order_details.so_qty - IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM supplier_inward_details_grn as sid  WHERE supplier_po_order_detail.id = sid.supplier_po_detail_id) as pend_po_qty"),

        DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pending_org_qty"),
        'sales_order.customer_village',
        'villages.village_name as customer_village',

       ])
       ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id', 'loading_entry_details.dp_details_id')
       ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')
       ->leftJoin('sales_order_details','sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')
       ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
       ->leftJoin('villages','villages.id','=','sales_order.customer_village')
       ->leftJoin('districts','districts.id', 'sales_order.customer_district_id')  
       ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
       ->leftJoin('items','items.id', 'dispatch_plan_details.item_id')
       ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
       ->leftJoin('units','units.id', 'items.unit_id')
      ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
       ->where('loading_entry_details.le_id',$id)->get();
              

       if($le_details != null){
           $le_details = $le_details->each(function ($le_details) use (&$isAnyPartInUse) {
               if($le_details->so_date != null){
                   $le_details->so_date = Date::createFromFormat('Y-m-d', $le_details->so_date)->format('d/m/Y');
               }
               if($le_details->dp_date != null){
                   $le_details->dp_date = Date::createFromFormat('Y-m-d', $le_details->dp_date)->format('d/m/Y');
               }

               $newRequest = new Request();

               $newRequest->so_details_id = $le_details->so_details_id;
               $newRequest->record_id = $le_details->dp_details_id;
               $newRequest->total_qty = $le_details->so_qty;

               $le_details->pending_so_qty = self::getPendingQty($newRequest);

               if($le_details->dp_details_id != null){
                   $total_le_qty = GRNMaterialDetails::where('dc_details_id', '=', $le_details->dp_details_id)->sum('grn_qty');
    
                    if($total_le_qty != null && $total_le_qty > 0){
                        $le_details->in_use = true;
                        $le_details->used_qty = $total_le_qty;
                        $isAnyPartInUse = true;
                    } else {
                        $le_details->in_use = false;
                        $le_details->used_qty = 0;
                    }
               }
               
               return $le_details;
           })->values();
       }


       if($le_data){
            $le_data->in_use = false;
            if($isAnyPartInUse == true){
                $le_data->in_use = true;
            }
       }

       if ($le_details != null) {
               return response()->json([
                   'response_code' => '1',
                   'le_data' => $le_data,
                   'le_details' => $le_details
               ]);
       } else {
               return response()->json([
                   'response_code' => '0',
                   'le_data' => [],
                   'le_details' => []
               ]);
       }

       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;
        DB::beginTransaction();
        try{

            $loading_entry =  LoadingEntry::where("le_id", "=", $request->id)->update([
                'current_location_id' => $locationID,                
                'dp_id'  => $request->Dpid,            
                'vehicle_no' => $request->vehicle_no,
                'transporter_id' => $request->transporter,
                'loading_by'  => $request->loading_by,
                'driver_name'   => $request->driver_name,
                'driver_mobile_no'   => $request->driver_no,
                'year_id'     => $year_data->id,
                'company_id'     => Auth::user()->company_id,
                'last_by_user_id' => Auth::user()->id,
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),  
            ]);
            if($loading_entry){

                if(isset($request->dp_details_id) && !empty($request->dp_details_id)){

                    $LoadingDetails =  LoadingEntryDetails::where('le_id',$request->id)->update([
                        'status' => 'D',
                    ]);


                    foreach ($request->le_details_id as $sodKey => $sodVal) {
                        if($sodVal == "0"){
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                $loading_entry_details = LoadingEntryDetails::create([
                                    'le_id'    => $request->id,
                                    'dp_details_id'   => $request->dp_details_id[$sodKey],
                                    'loading_qty'   => isset($request->plan_qty[$sodKey]) != '' ? $request->plan_qty[$sodKey] : 0,
                                    'status'   => 'Y',
                                ]);

                                // if($request->fiting_item[$sodKey]  == 'no'){
                                //     stockEffect($locationID,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->plan_qty[$sodKey],0,'add','U');
                                // }else{
                                //     $dpd_detail = DispatchPlanDetailsDetails::where('dp_details_id',$request->dp_details_id[$sodKey])->get();
                                    
                                //     if(!$dpd_detail->isEmpty()){  
                                //         foreach($dpd_detail as $skey => $sval){                             
                                //            stockEffect($locationID,$sval->item_id,$sval->item_id,$sval->plan_qty,0,'add','U');     
                                //         }                              
                                //     }                  
                                        
                                // }
                               
                               
                            }
                        }else{                          
                            
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                
                                $LoadingEntryDetails =  LoadingEntryDetails::where('le_details_id',$sodVal)->update([
                                    'le_id'  => $request->id,
                                    'dp_details_id' => $request->dp_details_id[$sodKey],
                                    'loading_qty'   => isset($request->plan_qty[$sodKey]) != '' ? $request->plan_qty[$sodKey] : 0,                                  
                                    'status' => 'Y',
                                ]);         
                                
                                // if($request->fiting_item[$sodKey]  == 'no'){
                                //     // stockEffect($locationID,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->plan_qty[$sodKey],$request->org_plan_qty[$sodKey],'edit','U');
                                //     stockEffect($locationID,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->plan_qty[$sodKey],$request->plan_qty[$sodKey],'edit','U');
                                // }else{
                                //     $dpd_detail = DispatchPlanDetailsDetails::where('dp_details_id',$request->dp_details_id[$sodKey])->get();
                                    
                                //     if(!$dpd_detail->isEmpty()){  
                                //         foreach($dpd_detail as $skey => $sval){                             
                                //            stockEffect($locationID,$sval->item_id,$sval->item_id,$sval->plan_qty,$sval->plan_qty,'edit','U');     
                                //         }                              
                                //     }                          
                                        
                                // }                          
                         
                            }
                        }                   

                    }
                }
            }

                // this code use for update dispatch qty         

                $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->update(['status' => 'D',]);

                $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->get();
                if($planDetails != null){
                    foreach($planDetails as $okey => $oval){
                        $pdpDetails = DispatchPlanDetailsDetails::where('dp_details_id',$request->dp_details_id[$sodKey])->update(['status' => 'D',]);
                    }
                }
                
                foreach($request->dp_details_id as $ctKey => $ctVal){
                    if($ctVal != null){
                        $dp_details =  DispatchPlanDetails::where('dp_details_id',$ctVal)->update([               
                            'item_id'=>isset($request->item_id[$ctKey]) != '' ? $request->item_id[$ctKey] : null,
                            'plan_qty'=>isset($request->plan_qty[$ctKey]) != '' ? $request->plan_qty[$ctKey] : 0,
                            'status'=> 'Y',
                        ]);

                        if($request->fiting_item[$ctKey]  == 'no'){
                            stockEffect($locationID,$request->item_id[$ctKey],$request->item_id[$ctKey],$request->plan_qty[$ctKey],$request->org_plan_qty[$ctKey],'edit','D','Loading Dispatch Detail',$ctVal);
                            // stockEffect($locationID,$request->item_id[$ctKey],$request->item_id[$ctKey],$request->plan_qty[$ctKey],$request->plan_qty[$ctKey],'edit','D');
                        }


                        if($ctVal != null){
                            $dpd_detail = DispatchPlanDetailsDetails::where('dp_details_id',$ctVal)->get();

                            if(!$dpd_detail->isEmpty()){

                                // stockEffect($locationID,$ctVal['item_id'],$ctVal['item_id'],1,0,'add','D');
                                foreach($dpd_detail as $skey => $sval){
                                    $dpd_details =  DispatchPlanDetailsDetails::where('dpd_details_id',$sval->dpd_details_id)->update([
                                        'dp_details_id' => $sval->dp_details_id,
                                        'so_details_detail_id'=> $sval->so_details_detail_id,
                                        'item_id'=> $sval->item_id,
                                        'plan_qty'=>$sval->plan_qty,                                             
                                        'status'=> 'Y',
                                   ]);
                                   stockEffect($locationID,$sval->item_id,$sval->item_id,$sval->plan_qty,$sval->plan_qty,'edit','D','Loading Dispatch Detail Detail',$sval->dpd_details_id);
                                   
                                }                          
                                
                            }
                        }
                    }
                }

                    $LoadingDetails = LoadingEntryDetails::where('le_id',$request->id)->where('status','D')->get();

                    if($LoadingDetails != null){
                        foreach($LoadingDetails as $lkey => $lval){
                            $dp_detail = DispatchPlanDetails::where('dp_details_id',$lval->dp_details_id)->first();

                            // if($dp_detail != null && $dp_detail->fitting_item == 'no'){
                            //     stockEffect($locationID,$dp_detail->item_id,$dp_detail->item_id,0, $lval->loading_qty,'delete','U');
                            // }else{
                            //     $dpd_detail = DispatchPlanDetailsDetails::where('dp_details_id',$lval->dp_details_id)->get();
                                
                            //     if(!$dpd_detail->isEmpty()){  
                            //         foreach($dpd_detail as $skey => $sval){                             
                            //            stockEffect($locationID,$sval->item_id,$sval->item_id,0,$sval->plan_qty,'delete','U');     
                            //         }                              
                            //     }                          
                                    
                            // }              
                          

                            LoadingEntryDetails::where('le_details_id',$lval->le_details_id )->where('status','D')->delete();
                        }
                    }
                    $PlanDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->where('status','D')->get();

                    if($PlanDetails != null){
                        foreach($PlanDetails as $okey => $oval){
                            $dpdDetails = DispatchPlanDetailsDetails::where('dp_details_id',$oval->dp_details_id)->where('status','D')->get();

                            if(!$dpdDetails->isEmpty()){                          
                                foreach($dpdDetails as $dkey=>$dval){
                                    $pdpDetails = DispatchPlanDetailsDetails::where('dpd_details_id',$dval->dpd_details_id)->where('status','D')->first();
                                    if($pdpDetails != null){
                                        stockEffect($locationID,$pdpDetails->item_id,$pdpDetails->item_id,0,$pdpDetails->plan_qty,'delete','D','Loading Dispatch Detail Detail',$pdpDetails->dpd_details_id);
                                        DispatchPlanDetailsDetails::where('dpd_details_id',$pdpDetails->dpd_details_id)->where('status','D')->delete();

                                    }                                       
                                }                      
                            } 
                            $dpDetails = DispatchPlanDetails::where('dp_details_id',$oval->dp_details_id)->where('status','D')->first();

                            if($dpDetails != null){
                                if($dpDetails->fitting_item == 'no'){
                                    // dd('dsf');
                                    stockEffect($locationID,$dpDetails->item_id,$dpDetails->item_id,0,$dpDetails->plan_qty,'delete','D','Loading Dispatch Detail',$dpDetails->dp_details_id);
                                }
                               
                                DispatchPlanDetails::where('dp_details_id',$dpDetails->dp_details_id)->where('status','D')->delete();
                            }
                        }
                    }
                    DB::commit();
                    return response()->json([
                        'response_code' => '1',
                        'response_message' => 'Record Updated Successfully.',
                    ]);            

            
        } 
        catch (\Exception $e) {
            // dd($e->getLine());
        DB::rollBack();       
        getActivityLogs("Loading Entry", "update", $e->getMessage(),$e->getLine());  

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       
        DB::beginTransaction();
        try{   

                // this is use for stock maintain
            // $locationID = getCurrentLocation()->id;
            // $oldLoadingDetails = LoadingEntryDetails::where('le_id',$request->id)->get();
            // $oldLoadingDetailsData = [];
            // if($oldLoadingDetails != null){
            // $oldLoadingDetailsData = $oldLoadingDetails->toArray();
            // }
            
            // foreach($oldLoadingDetailsData as $gkey=>$gval){
            // $qty = $gval['loading_qty'];
            // $dp_detail = DispatchPlanDetails::where('dp_details_id',$gval['dp_details_id'])->first();

            // if($dp_detail != null && $dp_detail->fitting_item == 'no'){
            //     stockEffect($locationID,$dp_detail->item_id,$dp_detail->item_id,0,$qty,'delete','U');
            // } else{
            //     $dpd_detail = DispatchPlanDetailsDetails::where('dp_details_id',$gval['dp_details_id'])->get();
                
            //     if(!$dpd_detail->isEmpty()){  
            //         foreach($dpd_detail as $skey => $sval){                             
            //            stockEffect($locationID,$sval->item_id,$sval->item_id,0,$sval->plan_qty,'delete','U');     
            //         }                              
            //     }                          
                    
            // }            
            

            // }            

            $grn_data = GRNMaterialDetails::
            leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
            ->where('loading_entry_details.le_id',$request->id)->get();
            if($grn_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Loading Entry Used In GRN.",
                ]);
            }

            
            LoadingEntryDetails::where('le_id',$request->id)->delete();
            LoadingEntry::destroy($request->id);

            DB::commit();


            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack(); 
            getActivityLogs("Loading Entry", "delete", $e->getMessage(),$e->getLine());  
            
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
                // $error_msg = "You Can't Delete, Loading Entry Used In GRN.";

                return response()->json([
                    'response_code' => '0',
                    'response_message' =>  $error_msg,
                ]);
            }else if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }
            
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => $error_msg,
            // ]);
        }
    }

    public function getLoadingListForDispatch(Request $request){
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        if(isset($request->id)){
            $edit_loading_data = LoadingEntryDetails::select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan.special_notes','dispatch_plan.dp_id','dispatch_plan_details.dp_details_id',
            ])
            ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
            ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
            ->where('loading_entry_details.le_id','=',$request->id)
            ->get();
        }

        // $dp_data = DispatchPlan::distinct()->select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan.special_notes','dispatch_plan.dp_id','dispatch_plan_details.dp_details_id',
        // DB::raw("(dispatch_plan_details.plan_qty  - (SELECT IFNULL(SUM(loading_entry_details.loading_qty),0) FROM loading_entry_details WHERE dp_details_id  = dispatch_plan_details.dp_details_id )) as pending_plan_qty"),    
        // ])
        // ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
        // ->where('dispatch_plan.current_location_id',$locationCode->id)
        // ->whereIn('dispatch_plan.year_id',$yearIds)  
        // ->having('pending_plan_qty','>',0)
        // ->get();

        $dp_details_id = LoadingEntryDetails::select('dp_details_id')->get();

        $dp_data = DispatchPlan::distinct()->select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan.special_notes','dispatch_plan.dp_id','dispatch_plan_details.dp_details_id',         
        ])
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
        ->where('dispatch_plan.current_location_id',$locationCode->id)
        ->whereIn('dispatch_plan.year_id',$yearIds)  
        ->whereNotIn('dispatch_plan_details.dp_details_id',$dp_details_id)  
        ->get();


        $dp_data = $dp_data->unique(['dp_id']);

        $dp_data = $dp_data->values()->all();


        if(isset($edit_loading_data)){          

            $data = collect($dp_data)->merge($edit_loading_data);
            $grouped = $data->groupBy('dp_id');    
            

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pending_plan_qty += (float) $item->pending_plan_qty;
                    return $carry;
                });
            });
    
            $dp_data = $merged->values();


        }

        if ($dp_data != null) {
            foreach ($dp_data as $cpKey => $cpVal) {
                if ($cpVal->dp_date != null) {
                    $cpVal->dp_date = Date::createFromFormat('Y-m-d', $cpVal->dp_date)->format('d/m/Y');
                }


                $dearName = DispatchPlan::select('dealers.dealer_name')
                ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
                ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
                ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
                ->leftJoin('dealers','dealers.id','=','sales_order.dealer_id')
                ->where('dispatch_plan.dp_id',$cpVal->dp_id)
                ->groupBy('dealers.id')
                ->pluck('dealer_name')
                ->implode(', ');

                $cpVal->dealer_name = $dearName;

               
            }
        }



        if ($dp_data != null) {
            return response()->json([
                'response_code' => '1',
                'dp_data' => $dp_data
            ]);
        } else {
            return response()->json([
                'response_code' => '1',
                'dp_data' => []
            ]);
        }


    }


    public function getDispatchDataForLoading(Request $request){
        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation();

        $request->dpids = explode(',',$request->dpids);


        if(isset($request->id)){          
        
          $dp_detail_id = DispatchPlanDetails::select('dp_details_id')->whereIN('dispatch_plan_details.dp_id',$request->dpids)->get();

          $edit_loading_data  = LoadingEntryDetails::select(['items.item_name','items.item_code','items.id', 'item_groups.item_group_name', 'units.unit_name','sales_order.so_number','sales_order.so_date', 
          'sales_order.customer_name','districts.district_name','locations.location_name',
          DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'), 'dispatch_plan_details.dp_details_id','dispatch_plan_details.plan_qty','sales_order_details.so_qty', 'dealers.dealer_name','dispatch_plan_details.item_id','dispatch_plan_details.fitting_item','dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.fitting_item','dispatch_plan.dp_id',
          'sales_order_details.so_qty', 'sales_order_details.so_details_id',
        //  'loading_entry_details.loading_qty  as pending_qty',   
        'sales_order.customer_village',
        'villages.village_name as customer_village',       
           ])
         ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
         ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
         ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
         ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
         ->leftJoin('villages','villages.id','=','sales_order.customer_village')
         ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
         ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
         ->leftJoin('districts','districts.id', 'sales_order.customer_district_id') 
         ->leftJoin('items', 'items.id', 'dispatch_plan_details.item_id')
         ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
         ->leftJoin('units', 'units.id', 'items.unit_id')
          ->whereIn('loading_entry_details.dp_details_id',$dp_detail_id)
          ->get();

        }

        $dispatch_data = DispatchPlan::select(['items.item_name','items.item_code','items.id', 'item_groups.item_group_name', 'units.unit_name','sales_order.so_number','sales_order.so_date', 'sales_order.customer_name',
        'districts.district_name','locations.location_name',
        DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'), 'dispatch_plan_details.dp_details_id','dispatch_plan_details.plan_qty','sales_order_details.so_qty', 'dealers.dealer_name','dispatch_plan_details.item_id','dispatch_plan_details.fitting_item','dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.fitting_item','dispatch_plan.dp_id',
        'sales_order_details.so_qty', 'sales_order_details.so_details_id',
        // DB::raw("((SELECT IFNULL(SUM(sales_order_details.so_qty),0) FROM sales_order_details WHERE so_details_id  = dispatch_plan_details.so_details_id ) - dispatch_plan_details.plan_qty) as pending_qty"),  

        DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pending_org_qty"),

        'sales_order.customer_village',
        'villages.village_name as customer_village',
       
        ])          
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
        ->leftJoin('villages','villages.id','=','sales_order.customer_village')
        ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
        ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
        ->leftJoin('districts','districts.id', 'sales_order.customer_district_id') 
        ->leftJoin('items', 'items.id', 'dispatch_plan_details.item_id')
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
        ->leftJoin('units', 'units.id', 'items.unit_id')
        ->whereIn('dispatch_plan_details.dp_id',$request->dpids)
        ->get();

        if (isset($edit_loading_data)) {
            $data = collect($dispatch_data)->merge($edit_loading_data);


        
            // Assuming the issue_detail_id you mentioned is actually item_issue_details_id
            $grouped = $data->groupBy('dp_details_id');    
          

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pending_qty += (float) $item->pending_qty;
                    return $carry;
                });
            });
        
            $dispatch_data = $merged->values();   
        }
        if($dispatch_data != null){

            foreach($dispatch_data as $cpKey => $cpVal){
                    if($cpVal->so_date != null){
                        $cpVal->so_date = Date::createFromFormat('Y-m-d', $cpVal->so_date)->format('d/m/Y');
                    }
                    if($cpVal->dp_date != null){
                        $cpVal->dp_date = Date::createFromFormat('Y-m-d', $cpVal->dp_date)->format('d/m/Y');
                    }

                    if(isset($request->id)){                         
                        $loding_id = LoadingEntryDetails::where('dp_details_id','=',$cpVal->dp_details_id)->where('le_id',$request->id)->first();
                        $cpVal->le_details_id = $loding_id!= null ? $loding_id->le_details_id  : 0;    
                        
                        // if($loding_id != null){                           
                        //     $cpVal->pending_so_qty = $cpVal->so_qty - $loding_id->loading_qty;
                        // }else{
                        //     $cpVal->pending_so_qty = $cpVal->so_qty  - $cpVal->plan_qty;
                        // }
                        
                    }else{
                        $cpVal->le_details_id = 0;
                        // $cpVal->pending_so_qty = $cpVal->so_qty - $cpVal->plan_qty;
                    }

                    
                    $newRequest = new Request();

                    $newRequest->so_details_id = $cpVal->so_details_id;
                    $newRequest->record_id = $cpVal->dp_details_id;
                    $newRequest->total_qty = $cpVal->so_qty;

                    $cpVal->pending_so_qty = self::getPendingQty($newRequest);

                    $pending_org_qty =  DispatchPlan::select([DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pending_org_qty"),])                
                    ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
                    ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
                    ->where('dispatch_plan_details.dp_details_id',$cpVal->dp_details_id)
                    // ->where('dispatch_plan_details.dp_id',$cpVal->dp_id)
                    ->first();

                    $cpVal->pending_org_qty = $pending_org_qty->pending_org_qty;

                }
            }

          if($dispatch_data != null){
              return response()->json([
                  'response_code' => '1',
                  'dispatch_data' => $dispatch_data
              ]);
          }else{
              return response()->json([
                  'response_code' => '0',
                  'dispatch_data' => []
              ]);
          }

  }


  public function getPendingQty(Request $request){
    $exectQty = $request->total_qty;

        $oldRecords = DispatchPlanDetails::select(DB::raw('SUM(plan_qty) as sum'))
        ->where('so_details_id','=',$request->so_details_id)
        ->where('dp_details_id','<=',$request->record_id)
        ->groupBy(['so_details_id'])
        ->first();

        // $oldScRecords = SOShortClose::select(DB::raw('SUM(sc_qty) as sc_sum'))
        // ->where('so_details_id','=',$request->so_details_id)
        // ->groupBy(['so_details_id'])
        // ->first();
        
        // $sc_sum = $oldScRecords ? $oldScRecords->sc_sum : 0;

    
        if($oldRecords != null){

            $diff = $exectQty - $oldRecords->sum;
            // $diff = $exectQty - $oldRecords->sum - $sc_sum;

            // if($diff > 0){
            //     return $diff;
            // }else{
            //     return abs($exectQty);
            // }
            return $diff;
        }else{
            return abs($exectQty);
        } 
    
}


public function isPartInUse(Request $request){
    if(isset($request->dp_part_id) && $request->dp_part_id != ""){
        $isFound = null;
        $isFound =  GRNMaterialDetails::where('dc_details_id','=',$request->dp_part_id)->first();
        if($isFound != null){
            return response()->json([
                'response_code' => '1',
                'response_message' => "This is used somewhere, you can't delete",
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => "",
            ]);
        }
    }else{
        return response()->json([
            'response_code' => '0',
            'response_message' => "",
        ]);
    }
}

public function managePendingDispatch(){
    return view('manage.manage-pending_dispatch_list');
}

public function indexPendingDispatch(){

    $yearIds = getCompanyYearIdsToTill();
    $locationCode = getCurrentLocation(); 

    $dp_data = DispatchPlan::distinct()->select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan.special_notes','dispatch_plan.dp_id','dispatch_plan_details.dp_details_id',
    DB::raw("(dispatch_plan_details.plan_qty  - (SELECT IFNULL(SUM(loading_entry_details.loading_qty),0) FROM loading_entry_details WHERE dp_details_id  = dispatch_plan_details.dp_details_id )) as pending_plan_qty"), 
    'dealers.dealer_name','customer_groups.customer_group_name', 'sales_order.so_from_value_fix','sales_order.customer_name','locations.location_name',     
    ])
    ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
     ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
    ->leftJoin('sales_order','sales_order.id', 'sales_order_details.so_id')
    ->leftJoin('dealers','dealers.id','=','sales_order.dealer_id')
    ->leftJoin('customer_groups','customer_groups.id','=','sales_order.customer_group_id')
    ->leftJoin('locations','locations.id', 'sales_order.to_location_id') 
    ->where('dispatch_plan.current_location_id',$locationCode->id)
    ->whereIn('dispatch_plan.year_id',$yearIds)  
    ->having('pending_plan_qty','>',0); 
  

   return DataTables::of($dp_data)

   ->editColumn('dp_date', function($dp_data){           
       if ($dp_data->dp_date != null) {
           $formatedDate1 = Date::createFromFormat('Y-m-d', $dp_data->dp_date)->format('d/m/Y'); 
           
           return $formatedDate1;

       }else{
           return '';
       }
   })
 
   
   ->editColumn('name', function($dp_data){ 
    return $dp_data->so_from_value_fix == "location" ? $dp_data->location_name : $dp_data->customer_name;
    })
    ->filterColumn('name', function($query, $keyword) {
        $query->where(function($q) use ($keyword) {
            $q->where('location_name', 'like', "%$keyword%")
            ->orWhere('customer_name', 'like', "%$keyword%");
        });
    }) 

   ->rawColumns(['dp_date','name'])
   ->make(true);

}
  
}