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
use App\Models\ItemDetails;
use App\Models\LoadingEntrySecondaryDetails;
use App\Models\SOShortClose;
use App\Models\Admin;
use App\Models\Transporter;
use App\Models\DispatchPlanSecondaryDetails;
use App\Models\SalesReturnDetails;
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

        $loading_data = LoadingEntry::select(['loading_entry.le_id','dispatch_plan.dp_id','dispatch_plan.dp_date as dp_date','dispatch_plan.dp_sequence',
        // 'dispatch_plan.dp_number',
        'dispatch_plan.dispatch_from_value_fix',
        DB::raw("CASE 
                WHEN loading_entry.dp_number IS NOT NULL THEN loading_entry.dp_number 
                ELSE dispatch_plan.dp_number 
             END as dp_number"),
        'loading_entry.vehicle_no','loading_entry.transporter_id','loading_entry.loading_by','loading_entry.driver_name','loading_entry.driver_mobile_no','transporters.transporter_name','loading_entry.last_by_user_id','loading_entry.last_on','loading_entry.created_by_user_id','loading_entry.created_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name' ]) 
        ->leftJoin('loading_entry_details','loading_entry_details.le_id','=','loading_entry.le_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id', 'loading_entry_details.dp_details_id')
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'loading_entry.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'loading_entry.last_by_user_id')
    
        ->leftJoin('transporters','transporters.id', 'loading_entry.transporter_id')
        ->where('loading_entry.current_location_id','=',$location->id)
        ->where('loading_entry.year_id','=',$year_data->id)
        ->groupBy(['dispatch_plan.dp_id','loading_entry.le_id',]);
         if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $loading_data->whereDate('dispatch_plan.dp_date','>=',$from);
            $loading_data->whereDate('dispatch_plan.dp_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $loading_data->where('dispatch_plan.dp_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');
            $loading_data->where('dispatch_plan.dp_date','<=',$to);

        } 
        return DataTables::of($loading_data)
        ->editColumn('created_by_user_id', function($loading_data){
            if($loading_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$loading_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($loading_data){
            if($loading_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$loading_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('created_on', function($loading_data){
            if ($loading_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $loading_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('loading_entry.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(loading_entry.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($loading_data){
            if ($loading_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $loading_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('loading_entry.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(loading_entry.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('dp_date', function($loading_data){
            if ($loading_data->dp_date != null) {
                $date = Date::createFromFormat('Y-m-d', $loading_data->dp_date)->format(DATE_FORMAT);
                return $date;
            }else{
                return '';
            }
        })
        ->filterColumn('dispatch_plan.dp_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dispatch_plan.dp_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->editColumn('dispatch_from_value_fix', function($loading_data) {
            $value = $loading_data->dispatch_from_value_fix;

            if (empty($value)) {
                $value = DispatchPlanDetails::select('sales_order.so_from_value_fix')
                ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
                ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
                ->where('dispatch_plan_details.dp_id', $loading_data->dp_id)
                ->groupBy('sales_order.so_from_value_fix')
                ->pluck('so_from_value_fix')
                ->implode(', ');
            }
            $map = [
                'customer' => 'Subsidy',
                'cash_carry' => 'Cash & Carry',
                'location' => 'Location',
                // Add more mappings if needed
            ];

            $values = explode(',', $value);
            $mappedValues = array_map(function($item) use ($map) {
                $item = trim($item);
                return $map[$item] ?? ucfirst($item);
            }, $values);

            return implode(', ', $mappedValues);
        })

        ->filterColumn('dispatch_from_value_fix', function($query, $keyword) {
            $query->whereRaw("
                REPLACE(
                    REPLACE(
                        REPLACE(dispatch_plan.dispatch_from_value_fix, 'customer', 'Subsidy'),
                        'cash_carry', 'Cash & Carry'
                    ),
                    'location', 'Location'
                ) LIKE ?
            ", ["%{$keyword}%"]);
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

         ->addColumn('customer_group', function($loading_data){ 
           
            return $customer_group = DispatchPlanDetails::select('customer_groups.customer_group_name')
            ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
            ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
            ->leftJoin('customer_groups', 'customer_groups.id', '=', 'sales_order.customer_group_id')
            ->where('dp_id', $loading_data->dp_id)
            ->whereNotNull('customer_groups.customer_group_name') // Exclude null values
            ->groupBy('customer_groups.customer_group_name') // Group by customer group name
            ->pluck('customer_group_name') // Get only the column values
            ->implode(', '); // Convert to comma-separated string
        
       
        })
       
        ->filterColumn('customer_group', function ($query, $keyword) {
            $keywords = explode(',', $keyword);
            $query->whereIn('dispatch_plan_details.dp_id', function ($subQuery) use ($keywords) {
                $subQuery->select('dispatch_plan_details.dp_id')
                    ->from('dispatch_plan_details')
                    ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
                    ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
                    ->leftJoin('customer_groups', 'customer_groups.id', '=', 'sales_order.customer_group_id');

                foreach ($keywords as $key) {
                    $key = trim($key);
                    $subQuery->orWhere('customer_groups.customer_group_name', 'like', "%{$key}%");
                }
            });
        })

         ->addColumn('so_number', function($loading_data){ 
           
            return $so_no = DispatchPlanDetails::select('sales_order.so_number')
            ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
            ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')      
            ->where('dp_id', $loading_data->dp_id)     
            ->groupBy('sales_order.so_number') 
            ->pluck('so_number') 
            ->implode(', '); 
        
       
        })

        ->filterColumn('so_number', function ($query, $keyword) {
            $keywords = explode(',', $keyword);
            $query->whereIn('dispatch_plan_details.dp_id', function ($subQuery) use ($keywords) {
                $subQuery->select('dispatch_plan_details.dp_id')
                    ->from('dispatch_plan_details')
                    ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
                    ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id');

                foreach ($keywords as $key) {
                    $key = trim($key);
                    $subQuery->orWhere('sales_order.so_number', 'like', "%{$key}%");
                }
            });
        })
     
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
        // dd($request);
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;
        DB::beginTransaction();
        try{
            // first save dispatch form
            $loading_entry =  LoadingEntry::create([
                'current_location_id' => $locationID,                
                'dp_id'         => $request->Dpid,            
                'dp_number'         => $request->dp_number,            
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

                    foreach($request->dp_details_id as $key=>$val){
                          // if($request->so_from_value_fix[$dpKey] != 'customer'){       
                            $pdpsecondDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$val)->update([
                                'status' => 'D',
                            ]);
                        // }
                    }

                    foreach($request->dp_details_id as $dpKey => $dpval){     
                  
                        // if(isset($dpval)){

                        //     $dpQtySum = round(DispatchPlanDetails::where('dp_details_id',$dpval)->sum('plan_qty'),3);
            
                        //     $useloadQtySum = round(LoadingEntryDetails::where('dp_details_id',$dpval)->sum('loading_qty'),3);

                        //     $loadQty = isset($request->plan_qty[$dpKey]) && $request->plan_qty[$dpKey] > 0 ? $request->plan_qty[$dpKey] : 0;
                        //     $loadQtySum = $useloadQtySum + $loadQty;   
                            
                        //     // dd($dpQtySum  $loadQtySum)
            
                        //     if($dpQtySum < $loadQtySum){
                        //         DB::rollBack();
                        //         return response()->json([
                        //             'response_code' => '0',
                        //             'response_message' => 'Plan Qty. Is Used',                               
                        //         ]);
                        //     }

                        // }

                        $loading_entry_details = LoadingEntryDetails::create([
                            'le_id'    => $loading_entry->le_id,
                            'dp_details_id'   => $dpval,
                            'loading_qty'   => isset($request->plan_qty[$dpKey]) != '' ? $request->plan_qty[$dpKey] : 0,
                            'status'   => 'Y',
                        ]);

                        if(isset($request->loading_entry_details[$dpval]) && !empty($request->loading_entry_details[$dpval])){                             

                            foreach($request->loading_entry_details[$dpval] as $secondkey => $secondval){
                              
                             
                                // if($request->so_from_value_fix[$dpKey] != 'customer'){                


                                    $so_details_id = DispatchPlanSecondaryDetails::where('dp_details_id',$secondval['dp_details_id'])->value('so_details_id');

                                   
                                    if($secondval['dp_secondary_details_id'] == '0'){
                                    //    dd('sdfd');
                                        $dpd_second_details =  DispatchPlanSecondaryDetails::create([
                                        'dp_details_id' => $secondval['dp_details_id'],
                                        'so_details_id'=> $so_details_id,
                                        'item_id'=> $secondval['item_id'],
                                        'item_details_id'=> $secondval['item_details_id'],
                                        'plan_qty'=>$secondval['plan_qty'],                             
                                        'status'=> 'Y',
                                        ]);


                                        stockDetailsEffect($locationID,$secondval['item_details_id'],$secondval['item_details_id'],$secondval['plan_qty'],0,'add','D','Dispatch Secondary Details',$dpd_second_details->dp_secondary_details_id,'No','Dispatch Details Details',$secondval['dp_details_id']);

                                        $loading_entry_secondary_details = LoadingEntrySecondaryDetails::create([
                                            'le_details_id'  => $loading_entry_details->id,
                                            'dp_details_id'  => $secondval['dp_details_id'],
                                            'dp_secondary_details_id'  => $dpd_second_details->dp_secondary_details_id,
                                            'item_id'  => $secondval['item_id'],
                                            'item_details_id'  => $secondval['item_details_id'],
                                            'plan_qty'  => $secondval['plan_qty'],
                                            'status' => "Y",
                                        ]);


                                    }else{
                                        $dpd_second_details_update =  DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondval['dp_secondary_details_id'])->update([
                                            'dp_details_id' => $secondval['dp_details_id'],
                                            'so_details_id'=> $so_details_id,
                                            'item_id'=> $secondval['item_id'],
                                            'item_details_id'=> $secondval['item_details_id'],
                                            'plan_qty'=>$secondval['plan_qty'],                             
                                            'status'=> 'Y',
                                        ]);                                     

                                       

                                        stockDetailsEffect($locationID,$secondval['item_details_id'],$secondval['item_details_id'],$secondval['plan_qty'],$secondval['org_plan_qty'],'edit','D','Dispatch Secondary Details',$secondval['dp_secondary_details_id'],'No','Dispatch Details Details',$secondval['dp_details_id']);

                                        $loading_entry_secondary_details = LoadingEntrySecondaryDetails::create([
                                            'le_details_id'  => $loading_entry_details->id,
                                            'dp_details_id'  => $secondval['dp_details_id'],
                                            'dp_secondary_details_id'  => $secondval['dp_secondary_details_id'],
                                            'item_id'  => $secondval['item_id'],
                                            'item_details_id'  => $secondval['item_details_id'],
                                            'plan_qty'  => $secondval['plan_qty'],
                                            'status' => "Y",
                                        ]);

                                    }  
                                    
                                     // dd(DispatchPlanSecondaryDetails::where('dp_details_id',$dpval)->get());                                     

                                     
                                // }else{
                                    
                                //     $loading_entry_secondary_details = LoadingEntrySecondaryDetails::create([
                                //         'le_details_id'  => $loading_entry_details->id,
                                //         'dp_details_id'  => $secondval['dp_details_id'],
                                //         'dp_secondary_details_id'  => $secondval['dp_secondary_details_id'],
                                //         'item_id'  => $secondval['item_id'],
                                //         'item_details_id'  => $secondval['item_details_id'],
                                //         'plan_qty'  => $secondval['plan_qty'],
                                //         'status' => "Y",
                                //     ]);
                                // }                         
                                
                                
                            }                  
                

                        }

                        // if($request->so_from_value_fix[$dpKey] != 'customer'){

                            $checksecondDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$dpval)->where('status','D')->get();

                                            
                            if(!$checksecondDetails->isEmpty()){                          
                                foreach($checksecondDetails as $dkey=>$dval){
                                    $secondDetails = DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$dval->dp_secondary_details_id)->where('status','D')->first();
                                    if($secondDetails != null){
                                        stockDetailsEffect($locationID,$secondDetails->item_details_id,$secondDetails->item_details_id,0,$secondDetails->plan_qty,'delete','D','Dispatch Secondary Details',$secondDetails->dp_secondary_details_id,'No','Dispatch Details Details',$secondval['dp_details_id']);
                                        DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondDetails->dp_secondary_details_id)->where('status','D')->delete();

                                    }                                       
                                }                      
                            } 
                        // }

                     
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

                    // $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->whereIn('so_from_value_fix',['location','cash_carry'])->update(['status' => 'D',]);


                    // $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->whereIn('so_from_value_fix',['location','cash_carry'])->get();
                    
                    $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->update(['status' => 'D',]);


                    $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->get();

                    if($planDetails->isNotEmpty()){
                        foreach($planDetails as $okey => $oval){
                            $pdpDetails = DispatchPlanDetailsDetails::where('dp_details_id',$oval->dp_details_id)->update(['status' => 'D',]);

                            // $secDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$oval->dp_details_id)->update(['status' => 'D',]);
                        }
                    }

                 

                    foreach($request->dp_details_id as $ctKey => $ctVal){
                        // if($ctVal != null && $request->so_from_value_fix[$ctKey] != 'customer'){
                        
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
                        // }
                    }
 
                    // $PlanDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->whereIn('so_from_value_fix',['location','cash_carry'])->where('status','D')->get();

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

                            $checksecondDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$oval->dp_details_id)->get();
                            // $checksecondDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$oval->dp_details_id)->where('status','D')->get();

                                            
                            if(!$checksecondDetails->isEmpty()){                          
                                foreach($checksecondDetails as $dkey=>$dval){
                                    // $secondDetails = DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$dval->dp_secondary_details_id)->where('status','D')->first();
                                    $secondDetails = DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$dval->dp_secondary_details_id)->first();

                                    if($secondDetails != null){
                                        stockDetailsEffect($locationID,$secondDetails->item_details_id,$secondDetails->item_details_id,0,$secondDetails->plan_qty,'delete','D','Dispatch Secondary Details',$secondDetails->dp_secondary_details_id,'No','Dispatch Details Details',$secondDetails->dp_details_id);
                                        DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondDetails->dp_secondary_details_id)->delete();
                                        // DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondDetails->dp_secondary_details_id)->where('status','D')->delete();

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

                $wtUpDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->get();

                foreach($wtUpDetails as $wtKey => $wtVal){
                    if($wtVal->secondary_unit == "Yes"){
                        $findsecData = DispatchPlanSecondaryDetails::where('dp_details_id',$wtVal->dp_details_id)->get();

                        if($findsecData->isNotEmpty()){
                            $upTotalWt = 0;
                            foreach($findsecData as $sekey =>$seVal){
                                $detailWt = ItemDetails::where('item_details_id',$seVal->item_details_id)->sum('secondary_wt_pc');
                                $toatal_wt = $seVal->plan_qty * $detailWt;
                                $upTotalWt +=$toatal_wt;

                            }

                        }

                        DispatchPlanDetails::where('dp_details_id',$wtVal->dp_details_id)->update([
                                'wt_pc' => $upTotalWt,
                        ]);

                    }
                    // else{
                    //     if($wtVal->fitting_item == 'No'){
                    //         $dpTotalWt = $wtVal->wt_pc * $plan_qty;
                    //         DispatchPlanDetails::where('dp_details_id',$wtVal->dp_details_id)->update([
                    //             'wt_pc' => $dpTotalWt,
                    //         ]);
                    //     }                       
                        
                    // }
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
// dd($e->getMessage(),$e->getLine());
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
        DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),'sales_order_details.so_details_id',  'sales_order_details.so_qty',   
        'loading_entry_details.le_details_id',

        // DB::raw("((SELECT IFNULL(SUM(sales_order_details.so_qty),0) FROM sales_order_details WHERE so_details_id  = dispatch_plan_details.so_details_id ) - loading_entry_details.loading_qty) as pending_so_qty"), 

        // DB::raw("(dispatch_plan_details.plan_qty - loading_entry_details.loading_qty) as pending_so_qty"),  

        // DB::raw("(SELECT sales_order_details.so_qty - IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM supplier_inward_details_grn as sid  WHERE supplier_po_order_detail.id = sid.supplier_po_detail_id) as pend_po_qty"),

        DB::raw("(dispatch_plan_details.plan_qty  - (SELECT IFNULL(SUM(loading_entry_details.loading_qty),0) FROM loading_entry_details WHERE dp_details_id  = dispatch_plan_details.dp_details_id )) as pending_dp_qty"),

        DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pending_org_qty"),
        'sales_order.customer_village',
        'villages.village_name as customer_village',
        'dispatch_plan.multiple_loading_entry',
        'dispatch_plan_details.fitting_item',
        'dispatch_plan_details.secondary_unit',
        'dispatch_plan_details.so_from_value_fix',
        'dispatch_plan_details.allow_partial_dispatch',

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
                   $total_sr_qty = SalesReturnDetails::where('dp_details_id', '=', $le_details->dp_details_id)->sum('sr_qty');
                 $total_qty = $total_le_qty + $total_sr_qty;
                    if($le_details->dp_date != null){
                        $date = Date::createFromFormat('d/m/Y', $le_details->dp_date)->format('Y-m-d');
                    }
                    if($total_qty != null && $total_qty > 0 || LiveUpdateSecDate($date,$le_details->item_id)){
                        // dd("fdg");
                        $le_details->in_use = true;
                        $le_details->used_qty = $total_qty;
                        $isAnyPartInUse = true;
                    } else {
                        $le_details->in_use = false;
                        $le_details->used_qty = 0;
                    }
               }
               
               return $le_details;
           })->values();
       }
       
       $dispatchDetailArray = [];
            
            foreach($le_details as $cpKey => $cpVal){
                if($cpVal->secondary_unit == 'Yes'){
                    $dpSecondaryItem = LoadingEntrySecondaryDetails::select([
                    'loading_entry_secondary_details.le_secondary_details_id',
                    'loading_entry_secondary_details.dp_secondary_details_id',
                    'loading_entry_secondary_details.dp_details_id',
                    'loading_entry_secondary_details.item_id',
                    'loading_entry_secondary_details.item_details_id',
                    'items.item_name',
                    'items.item_code',
                    'item_groups.item_group_name',
                    'units.unit_name', 'item_details.secondary_item_name',
                    DB::raw("((SELECT dispatch_plan_secondary_details.plan_qty -  IFNULL(SUM(loading_entry_secondary_details.plan_qty),0) FROM loading_entry_secondary_details  WHERE loading_entry_secondary_details.dp_secondary_details_id = dispatch_plan_secondary_details.dp_secondary_details_id) + loading_entry_secondary_details.plan_qty) as pend_plan_qty"),
                   'loading_entry_secondary_details.plan_qty',
                   'loading_entry_secondary_details.plan_qty as org_plan_qty',
                
                    ])
                    ->leftJoin('dispatch_plan_secondary_details', 'dispatch_plan_secondary_details.dp_secondary_details_id', 'loading_entry_secondary_details.dp_secondary_details_id')
                    ->leftJoin('items', 'items.id', 'dispatch_plan_secondary_details.item_id')
                    ->leftJoin('item_details', 'item_details.item_details_id', 'dispatch_plan_secondary_details.item_details_id')
                    ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
                    ->leftJoin('units', 'units.id', 'items.unit_id')   
                    ->where('loading_entry_secondary_details.dp_details_id', $cpVal->dp_details_id)
                    ->where('loading_entry_secondary_details.le_details_id', $cpVal->le_details_id)
                    ->get();

                    $dispatchDetailArray[$cpVal->dp_details_id] = $dpSecondaryItem->isNotEmpty() ? $dpSecondaryItem : [];

                    

                }

                 

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
                   'le_details' => $le_details,
                  'dispatch_detail_data' => $dispatchDetailArray

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
        // dd($request);
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;
        DB::beginTransaction();
        try{

            $loading_entry =  LoadingEntry::where("le_id", "=", $request->id)->update([
                'current_location_id' => $locationID,                
                'dp_id'  => $request->Dpid,   
                'dp_number'  => $request->dp_number,                 
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

                    $editplanDetails =  LoadingEntryDetails::where('le_id',$request->id)->get();
                    if($editplanDetails != null){
                            foreach($editplanDetails as $okey => $oval){
                                $lsdDetail = LoadingEntrySecondaryDetails::where('le_details_id',$oval->le_details_id)->update([
                                     'status' => 'D',
                                
                                ]);

                            // if($request->so_from_value_fix[$sodKey] != 'customer'){       
                                $pdpsecondDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$oval->dp_details_id)->update([
                                    'status' => 'D',
                                ]);
                            // }
                            }
                        }


                    foreach ($request->le_details_id as $sodKey => $sodVal) {                    

                        if($sodVal == "0"){
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                $loading_entry_details = LoadingEntryDetails::create([
                                    'le_id'    => $request->id,
                                    'dp_details_id'   => $request->dp_details_id[$sodKey],
                                    'loading_qty'   => isset($request->plan_qty[$sodKey]) != '' ? $request->plan_qty[$sodKey] : 0,
                                    'status'   => 'Y',
                                ]);     
                                
                                 if(isset($request->loading_entry_details[$sodVal]) && !empty($request->loading_entry_details[$sodVal])){

                                    foreach($request->loading_entry_details[$sodVal] as $secondkey => $secondval){

                                        $loading_entry_secondary_details = LoadingEntrySecondaryDetails::create([
                                            'le_details_id'  => $loading_entry_details->id,
                                            'dp_details_id'  => $secondval['dp_details_id'],
                                            'dp_secondary_details_id'  => $secondval['dp_secondary_details_id'],
                                            'item_id'  => $secondval['item_id'],
                                            'item_details_id'  => $secondval['item_details_id'],
                                            'plan_qty'  => $secondval['plan_qty'],
                                            'status' => "Y",
                                        ]);                                      

                                        // if($request->so_from_value_fix[$sodKey] != 'customer'){ 
                                                $dpd_second_details_update =  DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondval['dp_secondary_details_id'])->update([
                                                'dp_details_id' => $secondval['dp_details_id'],
                                                'so_details_id'=> $so_details_id,
                                                'item_id'=> $secondval['item_id'],
                                                'item_details_id'=> $secondval['item_details_id'],
                                                'plan_qty'=>$secondval['plan_qty'],                            
                                                'status'=> 'Y',
                                            ]);                                    
    
                                        
    
                                            stockDetailsEffect($locationID,$secondval['item_details_id'],$secondval['item_details_id'],$secondval['plan_qty'],$secondval['org_plan_qty'],'edit','D','Dispatch Secondary Details',$secondval['dp_secondary_details_id'],'No','Dispatch Details Details',$secondval['dp_details_id']);
    
                                        }

                                    // }
                                        
                                }
                               
                            }
                        }else{                          
                            
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                
                                $LoadingEntryDetails =  LoadingEntryDetails::where('le_details_id',$sodVal)->update([
                                    'le_id'  => $request->id,
                                    'dp_details_id' => $request->dp_details_id[$sodKey],
                                    'loading_qty'   => isset($request->plan_qty[$sodKey]) != '' ? $request->plan_qty[$sodKey] : 0,                                  
                                    'status' => 'Y',
                                ]);         
                                
                                if(isset($request->loading_entry_details[$request->dp_details_id[$sodKey]]) && !empty($request->loading_entry_details[$request->dp_details_id[$sodKey]])){
                                    
                                    foreach($request->loading_entry_details[$request->dp_details_id[$sodKey]] as $secondkey => $secondval){

                                          $so_details_id = DispatchPlanSecondaryDetails::where('dp_details_id',$secondval['dp_details_id'])->value('so_details_id');

                                        if($secondval['le_secondary_details_id'] == '0'){
                                            // if($request->so_from_value_fix[$sodKey] != 'customer'){   

                                                $dpd_second_details =  DispatchPlanSecondaryDetails::create([
                                                'dp_details_id' => $secondval['dp_details_id'],
                                                'so_details_id'=> $so_details_id,
                                                'item_id'=> $secondval['item_id'],
                                                'item_details_id'=> $secondval['item_details_id'],
                                                'plan_qty'=>$secondval['plan_qty'],                            
                                                'status'=> 'Y',
                                                ]);

                                                stockDetailsEffect($locationID,$secondval['item_details_id'],$secondval['item_details_id'],$secondval['plan_qty'],0,'add','D','Dispatch Secondary Details',$dpd_second_details->dp_secondary_details_id,'No','Dispatch Details Details',$secondval['dp_details_id']);

                                                $loading_entry_secondary_details = LoadingEntrySecondaryDetails::create([
                                                    'le_details_id'  => $sodVal,
                                                    'dp_details_id'  => $secondval['dp_details_id'],
                                                    // 'dp_secondary_details_id'  => $secondval['dp_secondary_details_id'],
                                                    'dp_secondary_details_id'  => $dpd_second_details->dp_secondary_details_id,
                                                    'item_id'  => $secondval['item_id'],
                                                    'item_details_id'  => $secondval['item_details_id'],
                                                    'plan_qty'  => $secondval['plan_qty'],
                                                    'status' => "Y",
                                                ]);
 
    
                                            // }else{

                                            //     $loading_entry_secondary_details = LoadingEntrySecondaryDetails::create([
                                            //         'le_details_id'  => $sodVal,
                                            //         'dp_details_id'  => $secondval['dp_details_id'],
                                            //         'dp_secondary_details_id'  => $secondval['dp_secondary_details_id'],
                                            //         'dp_secondary_details_id'  => $secondval['dp_secondary_details_id'],
                                            //         'item_id'  => $secondval['item_id'],
                                            //         'item_details_id'  => $secondval['item_details_id'],
                                            //         'plan_qty'  => $secondval['plan_qty'],
                                            //         'status' => "Y",
                                            //     ]);

                                              
                                            // }

                                          
                                        }else{
                                             $loading_entry_secondary_details = LoadingEntrySecondaryDetails::where('le_secondary_details_id',$secondval['le_secondary_details_id'])->update([
                                                'le_details_id'  => $sodVal,
                                                'dp_details_id'  => $secondval['dp_details_id'],
                                                'dp_secondary_details_id'  => $secondval['dp_secondary_details_id'],
                                                'item_id'  => $secondval['item_id'],
                                                'item_details_id'  => $secondval['item_details_id'],
                                                'plan_qty'  => $secondval['plan_qty'],
                                                'status' => "Y",
                                            ]);

                                            //   if($request->so_from_value_fix[$sodKey] != 'customer'){ 
                                                        $dpd_second_details_update =  DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondval['dp_secondary_details_id'])->update([
                                                        'dp_details_id' => $secondval['dp_details_id'],
                                                        'so_details_id'=> $so_details_id,
                                                        'item_id'=> $secondval['item_id'],
                                                        'item_details_id'=> $secondval['item_details_id'],
                                                        'plan_qty'=>$secondval['plan_qty'],                            
                                                        'status'=> 'Y',
                                                    ]);                                    
            
                                                
            
                                                    stockDetailsEffect($locationID,$secondval['item_details_id'],$secondval['item_details_id'],$secondval['plan_qty'],$secondval['org_plan_qty'],'edit','D','Dispatch Secondary Details',$secondval['dp_secondary_details_id'],'No','Dispatch Details Details',$secondval['dp_details_id']);
            
                                                // }
                                            
                                        }

                                    }

                                    $checksecondDetails = LoadingEntrySecondaryDetails::where('le_details_id',$sodVal)->where('status','D')->get();

                                    if(!$checksecondDetails->isEmpty()){                          
                                            foreach($checksecondDetails as $dkey=>$dval){
                                                LoadingEntrySecondaryDetails::where('le_secondary_details_id',$dval->le_secondary_details_id)->where('status','D')->delete();
                                                LoadingEntrySecondaryDetails::where('le_secondary_details_id',$dval->le_secondary_details_id)->where('status','D')->delete();
                                                // if($request->so_from_value_fix[$sodKey] != 'customer'){   
                                                    $secondDetails = DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$dval->dp_secondary_details_id)->where('status','D')->first();
                                                    if($secondDetails != null){
                                                        stockDetailsEffect($locationID,$secondDetails->item_details_id,$secondDetails->item_details_id,0,$secondDetails->plan_qty,'delete','D','Dispatch Secondary Details',$secondDetails->dp_secondary_details_id,'No','Dispatch Details Details',$secondval['dp_details_id']);
                                                        DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondDetails->dp_secondary_details_id)->where('status','D')->delete();

                                                    }    
                                                // }          

                                              
                                                                                   
                                            }                      
                                        } 
                                        
                                }
                                      
                         
                            }
                        }                   

                    }
                }
            }

            // this code use for update dispatch qty         

                // $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->whereIn('so_from_value_fix',['location','cash_carry'])->update(['status' => 'D',]);

                // $planDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->whereIn('so_from_value_fix',['location','cash_carry'])->get();

                $upplanDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->update(['status' => 'D',]);

                $getplanDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->get();
                if($getplanDetails->isNotEmpty()){
                    foreach($getplanDetails as $okey => $oval){
                       
                        // $pdpDetails = DispatchPlanDetailsDetails::where('dp_details_id',$request->dp_details_id[$sodKey])->update(['status' => 'D',]);
                        $pdpDetails = DispatchPlanDetailsDetails::where('dp_details_id',$oval->dp_details_id)->update(['status' => 'D',]);

                        // $secDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$oval->dp_details_id)->update(['status' => 'D',]);
                       
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
                            LoadingEntrySecondaryDetails::where('le_details_id',$lval->le_details_id )->where('status','D')->delete();
                        }
                    }
                    // $PlanDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->whereIn('so_from_value_fix',['location','cash_carry'])->where('status','D')->get();
                    
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

                            // $checksecondDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$oval->dp_details_id)->where('status','D')->get();
                            $checksecondDetails = DispatchPlanSecondaryDetails::where('dp_details_id',$oval->dp_details_id)->get();
                                   
                            if(!$checksecondDetails->isEmpty()){                          
                                foreach($checksecondDetails as $dkey=>$dval){
                                    // $secondDetails = DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$dval->dp_secondary_details_id)->where('status','D')->first();
                                    $secondDetails = DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$dval->dp_secondary_details_id)->first();
                                    if($secondDetails != null){
                                        stockDetailsEffect($locationID,$secondDetails->item_details_id,$secondDetails->item_details_id,0,$secondDetails->plan_qty,'delete','D','Dispatch Secondary Details',$secondDetails->dp_secondary_details_id,'No','Dispatch Details Details',$secondDetails->dp_details_id);
                                        // DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondDetails->dp_secondary_details_id)->where('status','D')->delete();
                                        DispatchPlanSecondaryDetails::where('dp_secondary_details_id',$secondDetails->dp_secondary_details_id)->delete();

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
                         
            // $LoadingDetails = LoadingEntryDetails::where('le_id',$request->id)->where('status','D')->get();

            // if($LoadingDetails != null){
            //     foreach($LoadingDetails as $lkey => $lval){
            //         LoadingEntryDetails::where('le_details_id',$lval->le_details_id )->where('status','D')->delete();
            //         LoadingEntrySecondaryDetails::where('le_details_id',$lval->le_details_id )->where('status','D')->delete();
            //     }
            // }


            $wtUpDetails =  DispatchPlanDetails::where('dp_id',$request->Dpid)->get();

                foreach($wtUpDetails as $wtKey => $wtVal){
                    if($wtVal->secondary_unit == "Yes"){
                        $findsecData = DispatchPlanSecondaryDetails::where('dp_details_id',$wtVal->dp_details_id)->get();

                        if($findsecData->isNotEmpty()){
                            $upTotalWt = 0;
                            foreach($findsecData as $sekey =>$seVal){
                                $detailWt = ItemDetails::where('item_details_id',$seVal->item_details_id)->sum('secondary_wt_pc');
                                $toatal_wt = $seVal->plan_qty * $detailWt;
                                $upTotalWt +=$toatal_wt;

                            }

                        }

                        DispatchPlanDetails::where('dp_details_id',$wtVal->dp_details_id)->update([
                                'wt_pc' => $upTotalWt,
                        ]);

                    }
                    // else{
                    //     if($wtVal->fitting_item == 'No'){
                    //         $dpTotalWt = $wtVal->wt_pc * $plan_qty;
                    //         DispatchPlanDetails::where('dp_details_id',$wtVal->dp_details_id)->update([
                    //             'wt_pc' => $dpTotalWt,
                    //         ]);
                    //     }                       
                        
                    // }
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
            $date = LoadingEntry::where('le_id',$request->id)
            ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','loading_entry.dp_id')
            ->value('dispatch_plan.dp_date');

            $grn_data = GRNMaterialDetails::
            leftJoin('loading_entry_details','loading_entry_details.dp_details_id','=','material_receipt_grn_details.dc_details_id')
            ->where('loading_entry_details.le_id',$request->id)->get();
            if($grn_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Loading Entry Used In GRN.",
                ]);
            }
            
            $return_data = SalesReturnDetails::
            leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','sales_return_details.le_details_id')
            ->where('loading_entry_details.le_id',$request->id)->get();
            if($return_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Loading Entry Used In Sales Return.",
                ]);
            }


            $oldledDetails = LoadingEntryDetails::where('le_id','=',$request->id)
            ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
            ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
            ->get();
            if($oldledDetails->isNotEmpty()){
                foreach($oldledDetails as $key=>$lvalue){
                    $SecUnitBeforeUpdate =  LiveUpdateSecDate($date,$lvalue->item_id);

                    if($SecUnitBeforeUpdate == true){                
                        DB::rollBack();     
                        return response()->json([
                            'response_code' => '0',
                            'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
                        ]);
                    }
                    LoadingEntrySecondaryDetails::where('le_details_id',$lvalue->le_details_id)->delete();
                }
            
            }

            
            LoadingEntryDetails::where('le_id',$request->id)->delete();
            LoadingEntry::destroy($request->id);

            DB::commit();


            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
            // dd($e->getMessage());
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

        // $dp_details_id = LoadingEntryDetails::select('dp_details_id')->get();

        // $dp_data = DispatchPlan::distinct()->select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan.special_notes','dispatch_plan.dp_id','dispatch_plan_details.dp_details_id',         
        // ])
        // ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
        // ->where('dispatch_plan.current_location_id',$locationCode->id)
        // ->whereIn('dispatch_plan.year_id',$yearIds)  
        // ->whereNotIn('dispatch_plan_details.dp_details_id',$dp_details_id)  
        // ->get();
      

        $no_fitting_dp_details_id = LoadingEntryDetails::select('loading_entry_details.dp_details_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')
        ->pluck('loading_entry_details.dp_details_id')
        ->toArray();

        $fitting_dp_details_id = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id',])
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')  
        ->where('dispatch_plan_details.fitting_item','yes')   
        ->whereNotIn('dispatch_plan_details.dp_details_id',$no_fitting_dp_details_id)       
        ->pluck('dispatch_plan_details.dp_details_id')
        ->toArray();

        $dp_details_id = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id',          
        DB::raw("(dispatch_plan_details.plan_qty  - (SELECT IFNULL(SUM(loading_entry_details.loading_qty),0) FROM loading_entry_details WHERE dp_details_id  = dispatch_plan_details.dp_details_id )) as pend_dp_qty"), ])
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')  
        ->where('dispatch_plan_details.fitting_item','no')
        ->where('dispatch_plan.current_location_id',$locationCode->id)
        ->whereIn('dispatch_plan.year_id',$yearIds) 
        ->having('pend_dp_qty','>',0) 
        ->pluck('dispatch_plan_details.dp_details_id')
        ->toArray();


        $dp_data = DispatchPlan::distinct()->select(['dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan.special_notes','dispatch_plan.dp_id','dispatch_plan_details.dp_details_id', ])
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
        ->where('dispatch_plan.current_location_id',$locationCode->id)
        ->whereIn('dispatch_plan.year_id',$yearIds)  
        ->whereIn('dispatch_plan_details.dp_details_id',array_merge($dp_details_id,$fitting_dp_details_id))  
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

                $existingCount  = LoadingEntry::where('dp_number', 'LIKE', $cpVal->dp_number . '%')->where('le_id', '!=', $request->id)->count();

                if($existingCount > 0){
                    $cpVal->dp_number = $cpVal->dp_number . ' - ' . $existingCount ;
                }



               
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
        
        //   $dp_detail_id = DispatchPlanDetails::select('dp_details_id')->whereIN('dispatch_plan_details.dp_id',$request->dpids)->get();

          $edit_loading_data  = LoadingEntryDetails::select(['items.item_name','items.item_code','items.id', 'item_groups.item_group_name', 'units.unit_name','sales_order.so_number','sales_order.so_date', 
          'sales_order.customer_name','districts.district_name','locations.location_name',
          DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'), 'dispatch_plan_details.dp_details_id','dispatch_plan_details.plan_qty','sales_order_details.so_qty', 'dealers.dealer_name','dispatch_plan_details.item_id','dispatch_plan_details.fitting_item','dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.fitting_item','dispatch_plan.dp_id',
          'sales_order_details.so_qty', 'sales_order_details.so_details_id',
        //  'loading_entry_details.loading_qty  as pending_qty',   
        'sales_order.customer_village',
        'villages.village_name as customer_village',       
         'dispatch_plan.multiple_loading_entry',
         'dispatch_plan_details.secondary_unit',
         'loading_entry_details.loading_qty',
          'dispatch_plan_details.allow_partial_dispatch',
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
          ->where('loading_entry_details.le_id',$request->id)
        //   ->whereIn('loading_entry_details.dp_details_id',$dp_detail_id)
          ->get();

        }

        $no_fitting_dp_details_id = LoadingEntryDetails::select('loading_entry_details.dp_details_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')
        ->whereIn('dispatch_plan_details.dp_id',$request->dpids)
        ->pluck('loading_entry_details.dp_details_id')
        ->toArray();

        $fitting_dp_details_id = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id',])
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')  
        ->where('dispatch_plan_details.fitting_item','yes')   
        ->whereIn('dispatch_plan_details.dp_id',$request->dpids)
        ->whereNotIn('dispatch_plan_details.dp_details_id',$no_fitting_dp_details_id)       
        ->pluck('dispatch_plan_details.dp_details_id')
        ->toArray();

        $dp_details_id = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id',          
         DB::raw("(dispatch_plan_details.plan_qty  - (SELECT IFNULL(SUM(loading_entry_details.loading_qty),0) FROM loading_entry_details WHERE dp_details_id  = dispatch_plan_details.dp_details_id )) as pend_dp_qty"), ])
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')  
        ->where('dispatch_plan_details.fitting_item','no')     
        ->whereIn('dispatch_plan_details.dp_id',$request->dpids)
        ->having('pend_dp_qty','>',0) 
        ->pluck('dispatch_plan_details.dp_details_id')
        ->toArray();

 


        $dispatch_data = DispatchPlan::select(['items.item_name','items.item_code','items.id', 'item_groups.item_group_name', 'units.unit_name','sales_order.so_number','sales_order.so_date', 'sales_order.customer_name',
        'districts.district_name','locations.location_name',
        DB::raw('(CASE WHEN sales_order.so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'), 'dispatch_plan_details.dp_details_id','dispatch_plan_details.plan_qty','sales_order_details.so_qty', 'dealers.dealer_name','dispatch_plan_details.item_id','dispatch_plan_details.fitting_item','dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.secondary_unit','dispatch_plan.dp_id',
        'sales_order_details.so_qty', 'sales_order_details.so_details_id',
        // DB::raw("((SELECT IFNULL(SUM(sales_order_details.so_qty),0) FROM sales_order_details WHERE so_details_id  = dispatch_plan_details.so_details_id ) - dispatch_plan_details.plan_qty) as pending_qty"),  

        DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pending_org_qty"),

        'sales_order.customer_village',
        'villages.village_name as customer_village',
        'dispatch_plan.multiple_loading_entry',
          DB::raw('0 as loading_qty'), 
          'dispatch_plan_details.so_from_value_fix',
          'dispatch_plan_details.allow_partial_dispatch',
       
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
        ->whereIn('dispatch_plan_details.dp_details_id',array_merge($dp_details_id,$fitting_dp_details_id))  
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
                    $carry->loading_qty += (float) $item->loading_qty;
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

                  

                    if($cpVal->fitting_item == 'yes'){
                        $cpVal->pending_dp_qty = 0;
                    }else{

                        $pend_dp_qty = DispatchPlanDetails::where('dp_details_id', $cpVal->dp_details_id)
                        ->selectRaw('plan_qty - (SELECT IFNULL(SUM(loading_qty), 0) FROM loading_entry_details WHERE dp_details_id = dispatch_plan_details.dp_details_id) as pend_dp_qty')
                        ->value('pend_dp_qty'); 
                        $cpVal->pending_dp_qty = $pend_dp_qty;                     

                         if($cpVal->secondary_unit == 'Yes'){
                            if($cpVal->le_details_id == 0){
                                $cpVal->plan_qty = $cpVal->le_details_id == 0 ? $pend_dp_qty : $cpVal->loading_qty;
                            }else{
                                $dpSecondaryItemSum = LoadingEntrySecondaryDetails::where('loading_entry_secondary_details.dp_details_id', $cpVal->dp_details_id)->sum('plan_qty');

                                $SecondaryQtySum = LoadingEntrySecondaryDetails::
                                leftJoin('item_details','item_details.item_details_id','loading_entry_secondary_details.item_details_id')
                                ->where('loading_entry_secondary_details.dp_details_id',    $cpVal->dp_details_id)->sum('item_details.secondary_qty');

                                  $cpVal->plan_qty = $dpSecondaryItemSum * $SecondaryQtySum;
                                }

                         }else{
                               $cpVal->plan_qty = $cpVal->le_details_id == 0 ? $pend_dp_qty : $cpVal->loading_qty;
                         }
                    }                  

                }

                $existingCount  = LoadingEntry::where('dp_number', 'LIKE', $cpVal->dp_number . '%')->where('le_id', '!=', $request->id)->count();

                if($existingCount > 0){
                    $cpVal->dp_number = $cpVal->dp_number . ' - ' . $existingCount ;
                }

            }

            $dispatchDetailArray = [];

            foreach($dispatch_data as $cpKey => $cpVal){
                if($cpVal->secondary_unit == 'Yes' && $cpVal->le_details_id == 0){
                    $dpSecondaryItem = DispatchPlanSecondaryDetails::select([
                    'dispatch_plan_secondary_details.dp_secondary_details_id',
                    'dispatch_plan_secondary_details.dp_details_id',
                    'dispatch_plan_secondary_details.item_id',
                    'dispatch_plan_secondary_details.item_details_id',
                    'items.item_name',
                    'items.item_code',
                    'item_groups.item_group_name',
                    'units.unit_name', 'item_details.secondary_item_name',
                    DB::raw("((SELECT dispatch_plan_secondary_details.plan_qty -  IFNULL(SUM(loading_entry_secondary_details.plan_qty),0) FROM loading_entry_secondary_details  WHERE loading_entry_secondary_details.dp_secondary_details_id = dispatch_plan_secondary_details.dp_secondary_details_id)) as plan_qty"),
                    // 'dispatch_plan_secondary_details.plan_qty',
                    DB::raw('0 as le_secondary_details_id'), 
                    'dispatch_plan_secondary_details.plan_qty as org_plan_qty',
                
                    ])
                    ->leftJoin('items', 'items.id', 'dispatch_plan_secondary_details.item_id')
                    ->leftJoin('item_details', 'item_details.item_details_id', 'dispatch_plan_secondary_details.item_details_id')
                    ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
                    ->leftJoin('units', 'units.id', 'items.unit_id')   
                    ->where('dispatch_plan_secondary_details.dp_details_id', $cpVal->dp_details_id)
                    ->having('plan_qty','>',0)
                    ->get();

                    $dispatchDetailArray[$cpVal->dp_details_id] = $dpSecondaryItem->isNotEmpty() ? $dpSecondaryItem : [];

                }else{
                    $dpSecondaryItem = LoadingEntrySecondaryDetails::select([
                    'loading_entry_secondary_details.le_secondary_details_id',
                    'loading_entry_secondary_details.dp_secondary_details_id',
                    'loading_entry_secondary_details.dp_details_id',
                    'loading_entry_secondary_details.item_id',
                    'loading_entry_secondary_details.item_details_id',
                    'items.item_name',
                    'items.item_code',
                    'item_groups.item_group_name',
                    'units.unit_name', 'item_details.secondary_item_name',
                    DB::raw("((SELECT dispatch_plan_secondary_details.plan_qty -  IFNULL(SUM(loading_entry_secondary_details.plan_qty),0) FROM loading_entry_secondary_details  WHERE loading_entry_secondary_details.dp_secondary_details_id = dispatch_plan_secondary_details.dp_secondary_details_id) + loading_entry_secondary_details.plan_qty) as pend_plan_qty"),
                   'loading_entry_secondary_details.plan_qty',
                     'loading_entry_secondary_details.plan_qty as org_plan_qty',
                
                    ])
                    ->leftJoin('dispatch_plan_secondary_details', 'dispatch_plan_secondary_details.dp_secondary_details_id', 'loading_entry_secondary_details.dp_secondary_details_id')
                    ->leftJoin('items', 'items.id', 'dispatch_plan_secondary_details.item_id')
                    ->leftJoin('item_details', 'item_details.item_details_id', 'dispatch_plan_secondary_details.item_details_id')
                    ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
                    ->leftJoin('units', 'units.id', 'items.unit_id')   
                    ->where('loading_entry_secondary_details.dp_details_id', $cpVal->dp_details_id)
                    ->get();

                    $dispatchDetailArray[$cpVal->dp_details_id] = $dpSecondaryItem->isNotEmpty() ? $dpSecondaryItem : [];

                    
                }

                 

            }

          if($dispatch_data != null){
              return response()->json([
                  'response_code' => '1',
                  'dispatch_data' => $dispatch_data,
                  'dispatch_detail_data' => $dispatchDetailArray
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

public function getSecondaryDispatchItemForLoading(Request $request){
    $locationCode = getCurrentLocation()->id;

    $dpSecondaryItem = SalesOrderDetail::select([
    'sales_order_details.so_details_id',
    'sales_order_details.item_id',
    'item_details.item_details_id',
    'item_details.secondary_item_name',
    'item_details.secondary_qty',
    'items.item_code',
    'item_groups.item_group_name',
    'units.unit_name',
    'seond_units.unit_name as second_unit',
    'sales_order_details.so_qty',
    DB::raw('0 as dp_secondary_details_id'),
    DB::raw('0 as le_secondary_details_id'),
    DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_details.plan_qty),0) FROM dispatch_plan_details  WHERE dispatch_plan_details.so_details_id = sales_order_details.so_details_id)) as pend_sod_qty"),
    // DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_secondary_details.plan_qty),0) FROM dispatch_plan_secondary_details  WHERE dispatch_plan_secondary_details.so_details_id = sales_order_details.so_details_id)) as pend_plan_qty"),
    DB::raw('IFNULL(location_stock_details.stock_qty, 0) as stock_qty'), 
    DB::raw('IFNULL(location_stock_details.secondary_stock_qty, 0) as secondary_stock_qty') 
    ])
    ->leftJoin('items', 'items.id', 'sales_order_details.item_id')
    ->leftJoin('item_details', 'item_details.item_id', 'items.id')
    ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
    ->leftJoin('units', 'units.id', 'items.unit_id')
    ->leftJoin('units as seond_units','seond_units.id','=','items.second_unit')
    ->leftJoin('location_stock_details', function ($join) use ($locationCode) {
        $join->on('location_stock_details.item_details_id', '=', 'item_details.item_details_id')
            ->where('location_stock_details.location_id', '=', $locationCode); // Ensure location-specific stock
    })
    ->where('sales_order_details.so_details_id', $request->so_details_id)
    ->get();

    if(isset($request->dp_id) && $dpSecondaryItem->isNotEmpty()){
        foreach($dpSecondaryItem as $sKey => $sVal){        

            $secondData = DispatchPlanSecondaryDetails::select('dispatch_plan_secondary_details.dp_secondary_details_id','dispatch_plan_secondary_details.dp_details_id','dispatch_plan_secondary_details.plan_qty')
            ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','dispatch_plan_secondary_details.dp_details_id')
            ->where('dispatch_plan_secondary_details.so_details_id',$sVal->so_details_id)
            ->where('dispatch_plan_secondary_details.item_details_id',$sVal->item_details_id)
            ->where('dispatch_plan_details.dp_id',$request->dp_id)
            ->first();

            // $totalsecondQty = DispatchPlanSecondaryDetails::
            // leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','dispatch_plan_secondary_details.dp_details_id')
            // ->where('dispatch_plan_secondary_details.so_details_id',$sVal->so_details_id)
            // ->where('dispatch_plan_details.dp_id',$request->dp_id)
            // ->sum('dispatch_plan_secondary_details.plan_qty');

            $totalsecondQty = DispatchPlanDetails::where('dispatch_plan_details.so_details_id', $request->so_details_id)->sum('dispatch_plan_details.plan_qty'); 


            if($secondData != null){
            
                $sVal->dp_secondary_details_id = $secondData->dp_secondary_details_id;
                
                $sVal->org_plan_qty = $secondData->plan_qty;
            }
        
            //   dd($sVal->pend_sod_qty);
            $sVal->dp_details_id = $request->dp_details_id;
            $sVal->pend_plan_qty = $sVal->pend_sod_qty + $totalsecondQty;

            // if($request->pendingType == 'dispatch'){
            //     $lePlanQtySum = LoadingEntrySecondaryDetails::where('dp_secondary_details_id',$sVal->dp_secondary_details_id)->sum('plan_qty');

            //   $sVal->loading_qty_sec =  $lePlanQtySum;
            // //   $sVal->loading_qty_sec =  $sVal->org_plan_qty - $lePlanQtySum;
            // }

        }
    }            

    if(isset($request->id) && $dpSecondaryItem->isNotEmpty()){
        foreach($dpSecondaryItem as $sKey => $sVal){
        $ledData = LoadingEntrySecondaryDetails::select('loading_entry_secondary_details.le_secondary_details_id','loading_entry_secondary_details.plan_qty')
        ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','loading_entry_secondary_details.le_details_id')
        ->where('loading_entry_details.le_id',$request->id)
        ->where('loading_entry_secondary_details.dp_secondary_details_id',$sVal->dp_secondary_details_id)
        ->first();
        if($ledData != null){

            //   $sVal->pend_plan_qty = $sVal->pend_plan_qty + round($ledData->plan_qty,3)   ;
            $sVal->le_secondary_details_id = $ledData->le_secondary_details_id;
        
            $sVal->loading_qty = $ledData->plan_qty;
                $sVal->org_plan_qty = $ledData->plan_qty;

            //    if($request->pendingType == 'dispatch'){
            //        $sVal->org_plan_qty = $ledData->plan_qty + $ledData->plan_qty;
            //    }else{
            //        $sVal->org_plan_qty = $ledData->plan_qty;
            //    }
            
            
        }else{
            //   $sVal->pend_sod_qty = $sVal->pend_plan_qty ;
                $sVal->le_secondary_details_id = 0;
                $sVal->loading_qty = 0;
        }   

        }

    }




    if ($dpSecondaryItem != null) {
        return response()->json([
            'response_code' => '1',
            'dpSecondaryItem' => $dpSecondaryItem
        ]);
    } else {
        return response()->json([
            'response_code' => '0',
            'soFittingItem' => []
        ]);
    }

}
  
}











// public function getSecondaryDispatchItemForLoading(Request $request){
//     $locationCode = getCurrentLocation()->id;

    
//         if($request->pendingType == 'dispatch'){
//             // if(isset($request->id)){                

//             // $edit_loding_secondary = LoadingEntrySecondaryDetails::select(['loading_entry_secondary_details.le_secondary_details_id','loading_entry_secondary_details.plan_qty',
//             //  'items.item_name',
//             // 'items.item_code',
//             // 'item_groups.item_group_name',
//             // 'units.unit_name', 'item_details.secondary_item_name',
//             // 'item_details.secondary_qty',
//             // ])
//             // ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','loading_entry_secondary_details.le_details_id')
//             //  ->leftJoin('items', 'items.id', 'loading_entry_secondary_details.item_id')
//             // ->leftJoin('item_details', 'item_details.item_details_id', 'loading_entry_secondary_details.item_details_id')
//             // ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
//             // ->leftJoin('units', 'units.id', 'items.second_unit')              
//             // ->where('loading_entry_details.le_id',$request->id)              
//             // ->get();

              


//             // }

//             $dpSecondaryItem = DispatchPlanSecondaryDetails::select([
//             'dispatch_plan_secondary_details.dp_secondary_details_id',
//             'dispatch_plan_secondary_details.dp_details_id',
//             'dispatch_plan_secondary_details.item_id',
//             'dispatch_plan_secondary_details.item_details_id',
//             'items.item_name',
//             'items.item_code',
//             'item_groups.item_group_name',
//             'units.unit_name', 'item_details.secondary_item_name',
//             'item_details.secondary_qty',
//             DB::raw("((SELECT dispatch_plan_secondary_details.plan_qty -  IFNULL(SUM(loading_entry_secondary_details.plan_qty),0) FROM loading_entry_secondary_details  WHERE loading_entry_secondary_details.dp_secondary_details_id = dispatch_plan_secondary_details.dp_secondary_details_id)) as pend_plan_qty"),
//             DB::raw('0 as le_secondary_details_id'), 
//             'dispatch_plan_secondary_details.plan_qty as org_plan_qty',
//             ])
//             ->leftJoin('items', 'items.id', 'dispatch_plan_secondary_details.item_id')
//             ->leftJoin('item_details', 'item_details.item_details_id', 'dispatch_plan_secondary_details.item_details_id')
//             ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
//             ->leftJoin('units', 'units.id', 'items.second_unit')  
//             ->where('dispatch_plan_secondary_details.dp_details_id', $request->dp_details_id)
//             // ->having('pend_plan_qty','>', 0)  
//             ->get();       

//             $total_qty = 0;

//             if(isset($request->id) && $dpSecondaryItem->isNotEmpty()){
//                 foreach($dpSecondaryItem as $sKey => $sVal){
//                     $ledData = LoadingEntrySecondaryDetails::select('loading_entry_secondary_details.le_secondary_details_id','loading_entry_secondary_details.plan_qty')
//                     ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','loading_entry_secondary_details.le_details_id')
//                     ->where('loading_entry_details.le_id',$request->id)
//                     ->where('loading_entry_secondary_details.dp_secondary_details_id',$sVal->dp_secondary_details_id)
//                     ->first();

//                     if($ledData != null){

//                         $sVal->pend_plan_qty = $sVal->pend_plan_qty + $ledData->plan_qty;
//                         $sVal->le_secondary_details_id = $ledData->le_secondary_details_id;
                    
//                         $sVal->loading_qty = $ledData->plan_qty;               
                        
//                     }
//             }

//         }

            
// // dd($dpSecondaryItem);
            
//             // foreach ($dpSecondaryItem as $item) {
//             //     $item->pend_plan_qty *= $item->secondary_qty;
//             //     $total_qty += $item->pend_plan_qty;
//             // }
//             // foreach ($dpSecondaryItem as $item) {
//             //     $item->pend_plan_qty = $total_qty;
            
//             // }

//               $dpSecondaryItem = $dpSecondaryItem->filter(function ($item) {
//             return $item->pend_plan_qty > 0;
//         })->values();

       
               
//         }else{

//                $dpSecondaryItem = SalesOrderDetail::select([
//             'sales_order_details.so_details_id',
//             'sales_order_details.item_id',
//             'item_details.item_details_id',
//             'item_details.secondary_item_name',
//             'item_details.secondary_qty',
//             'items.item_code',
//             'item_groups.item_group_name',
//             'units.unit_name',
//             'sales_order_details.so_qty',
//             DB::raw('0 as dp_secondary_details_id'),
//             DB::raw('0 as le_secondary_details_id'),
//             DB::raw("((SELECT sales_order_details.so_qty -  IFNULL(SUM(dispatch_plan_secondary_details.plan_qty),0) FROM dispatch_plan_secondary_details  WHERE dispatch_plan_secondary_details.so_details_id = sales_order_details.so_details_id)) as pend_plan_qty"),
//             DB::raw('IFNULL(location_stock_details.stock_qty, 0) as stock_qty') // Use IFNULL to handle NULL values
//             ])
//             ->leftJoin('items', 'items.id', 'sales_order_details.item_id')
//             ->leftJoin('item_details', 'item_details.item_id', 'items.id')
//             ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
//             ->leftJoin('units', 'units.id', 'items.second_unit')
//             ->leftJoin('location_stock_details', function ($join) use ($locationCode) {
//                 $join->on('location_stock_details.item_details_id', '=', 'item_details.item_details_id')
//                     ->where('location_stock_details.location_id', '=', $locationCode); // Ensure location-specific stock
//             })
//             ->where('sales_order_details.so_details_id', $request->so_details_id)
//             ->get();

//             if(isset($request->dp_id) && $dpSecondaryItem->isNotEmpty()){
//                 foreach($dpSecondaryItem as $sKey => $sVal){        

//                 $secondData = DispatchPlanSecondaryDetails::select('dispatch_plan_secondary_details.dp_secondary_details_id','dispatch_plan_secondary_details.dp_details_id','dispatch_plan_secondary_details.plan_qty')
//                 ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','dispatch_plan_secondary_details.dp_details_id')
//                 ->where('dispatch_plan_secondary_details.so_details_id',$sVal->so_details_id)
//                 ->where('dispatch_plan_secondary_details.item_details_id',$sVal->item_details_id)
//                 ->where('dispatch_plan_details.dp_id',$request->dp_id)
//                 ->first();
// // dd($secondData);
//                 $totalsecondQty = DispatchPlanSecondaryDetails::
//                 leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','dispatch_plan_secondary_details.dp_details_id')
//                 ->where('dispatch_plan_secondary_details.so_details_id',$sVal->so_details_id)
//                 ->where('dispatch_plan_details.dp_id',$request->dp_id)
//                 ->sum('dispatch_plan_secondary_details.plan_qty');

//                 if($secondData != null){
                
//                     $sVal->dp_secondary_details_id = $secondData->dp_secondary_details_id;
                    
//                     $sVal->org_plan_qty = $secondData->plan_qty;
//                 }
        
//                 //   dd($sVal->pend_sod_qty);
//                 $sVal->dp_details_id = $request->dp_details_id;
//                 $sVal->pend_plan_qty = $sVal->pend_plan_qty + $totalsecondQty;

//                 // if($request->pendingType == 'dispatch'){
//                 //     $lePlanQtySum = LoadingEntrySecondaryDetails::where('dp_secondary_details_id',$sVal->dp_secondary_details_id)->sum('plan_qty');

//                 //   $sVal->loading_qty_sec =  $lePlanQtySum;
//                 // //   $sVal->loading_qty_sec =  $sVal->org_plan_qty - $lePlanQtySum;
//                 // }


//                 }


//         }         

//         if(isset($request->id) && $dpSecondaryItem->isNotEmpty()){
//             foreach($dpSecondaryItem as $sKey => $sVal){
//             $ledData = LoadingEntrySecondaryDetails::select('loading_entry_secondary_details.le_secondary_details_id','loading_entry_secondary_details.plan_qty')
//             ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','loading_entry_secondary_details.le_details_id')
//             ->where('loading_entry_details.le_id',$request->id)
//             ->where('loading_entry_secondary_details.dp_secondary_details_id',$sVal->dp_secondary_details_id)
//             ->first();
//             if($ledData != null){

//                 //   $sVal->pend_plan_qty = $sVal->pend_plan_qty + round($ledData->plan_qty,3)   ;
//                 $sVal->le_secondary_details_id = $ledData->le_secondary_details_id;
            
//                 $sVal->loading_qty = $ledData->plan_qty;
//                  $sVal->org_plan_qty = $ledData->plan_qty;

//                 //    if($request->pendingType == 'dispatch'){
//                 //        $sVal->org_plan_qty = $ledData->plan_qty + $ledData->plan_qty;
//                 //    }else{
//                 //        $sVal->org_plan_qty = $ledData->plan_qty;
//                 //    }
                
                
//             }else{
//                 //   $sVal->pend_sod_qty = $sVal->pend_plan_qty ;
//                     $sVal->le_secondary_details_id = 0;
//                     $sVal->loading_qty = 0;
//             }   

//             }

//         }

//     }


   

//     // if($request->pendingType == 'dispatch'){
//     //     $dpSecondaryItem = $dpSecondaryItem->filter(function ($item) {
//     //         return $item->dp_secondary_details_id > 0;
//     //     })->values();
//     // }
// // $dpSecondaryItem = $dpSecondaryItem->filter(function ($item) {
  
// //     if ($item->dp_secondary_details_id > 0) {
// //         $pendingQty = round($item->org_plan_qty - $item->loading_qty_sec, 3);
// //         return $pendingQty > 0;
// //     }
// //     return false;
// // })->values();



//     if ($dpSecondaryItem != null) {
//         return response()->json([
//             'response_code' => '1',
//             'dpSecondaryItem' => $dpSecondaryItem
//         ]);
//     } else {
//         return response()->json([
//             'response_code' => '0',
//             'soFittingItem' => []
//         ]);
//     }

// }