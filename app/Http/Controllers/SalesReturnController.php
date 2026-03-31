<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
use Str;
use App\Models\Location;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetails;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\DispatchPlanDetails;
use App\Models\LoadingEntryDetails;
use App\Models\LoadingEntrySecondaryDetails;
use App\Models\LoadingEntry;
use App\Models\DispatchPlanDetailsDetails;
use App\Models\DispatchPlanSecondaryDetails;
use App\Models\DispatchPlan;
use App\Models\LocationStock;
use App\Models\PriceListDetails;
use App\Models\ItemDetails;
use App\Models\stockDetailsEffect;

class SalesReturnController extends Controller
{
    public function manage()
    {
        return view('manage.manage-sales_return');
    }

    
    public function index(SalesReturn $SalesReturn,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();


        $sales_return = SalesReturn::select(['sales_return.sr_id','sales_return.sr_sequence','sales_return.sr_number','sales_return.customer_name',
        'sales_return.sr_date','sales_return.transporter_id','transporters.transporter_name','sales_return.created_on','sales_return.created_by_user_id','sales_return.last_by_user_id','sales_return.last_on',
        'sales_return.vehicle_no', 'sales_return.lr_no_date','sales_return.sp_note','created_user.user_name as created_by_name','last_user.user_name as last_by_name'
        ])

        ->leftJoin('sales_return_details','sales_return_details.sr_id','=','sales_return.sr_id')
        // ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','sales_return_details.dp_details_id')
        // ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order','sales_order.customer_name','=','sales_return.customer_name')
        ->leftJoin('transporters','transporters.id','=','sales_return.transporter_id')
       ->leftJoin('admin AS created_user', 'created_user.id', '=', 'sales_return.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'sales_return.last_by_user_id')
        ->where('sales_return.current_location_id','=',$location->id)
        ->where('sales_return.year_id', '=', $year_data->id)
        ->groupBy('sales_return.sr_number');
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


            $sales_return->whereDate('sales_return.sr_date','>=',$from);

            $sales_return->whereDate('sales_return.sr_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $sales_return->where('sales_return.sr_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $sales_return->where('sales_return.sr_date','<=',$to);

        }  
        // dd($sales_order);

        return DataTables::of($sales_return)
        ->editColumn('created_by_user_id', function($sales_return){
            if($sales_return->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$sales_return->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($sales_return){
            if($sales_return->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$sales_return->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('transporter_name', function($sales_return){ 
            if($sales_return->transporter_name != ''){
                $transporter_name = ucfirst($sales_return->transporter_name);
                return $transporter_name;
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
        ->editColumn('created_on', function($sales_return){
            if ($sales_return->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $sales_return->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('sales_return.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_return.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($sales_return){
            if ($sales_return->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $sales_return->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('sales_return.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_return.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($sales_return){
            $action = "<div>";

            if(hasAccess("sales_return","print")){
                $action .="<a id='print_a' target='_blank' href='".route('print-sales_return',['id' => base64_encode($sales_return->sr_id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
            }
         
            if(hasAccess("sales_return","edit")){
            $action .="<a id='edit_a' href='".route('edit-sales_return',['id' => base64_encode($sales_return->sr_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("sales_return","delete")){
            $action .= "<i id='del_a'  href='".route('delete-sales_return',['id' => base64_encode($sales_return->sr_id)]) ."'  data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
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
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'sr_date', 'options'])
        ->make(true);
    }

    public function create()
    {
        return view('add.add-sales_return');
    }

    
    public function store(Request $request){
// dd($request->all());
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;


          $existNumber = SalesReturn::where('sr_number','=',$request->sr_number)->where('sr_sequence','=',$request->sr_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationID)->lockForUpdate()->first();
          
          if($existNumber){
              $latestNo = $this->getLatestSrNo($request);
              $tmp =  $latestNo->getContent();
              $area = json_decode($tmp, true);
              $sr_number =   $area['latest_po_no'];
              $sr_sequence = $area['number'];              
          }else{
             $sr_number = $request->sr_number;
             $sr_sequence = $request->sr_sequence;
          }

        // if($request->sr_from_id_fix == 1){
        //     $srFormValue =  "customer";
        // }elseif($request->sr_from_id_fix == 2){
        //     $srFormValue = "cash_carry";
        // }
         DB::beginTransaction();
         try{
             
             $salesreturn_data=  SalesReturn::create([ 
                //  'sr_from_id_fix'=>$request->sr_from_id_fix, 
                //  'sr_from_value_fix'=>$srFormValue, 
                 'sr_sequence' => $sr_sequence, 
                 'sr_number' => $sr_number, 
                 'sr_date' => Date::createFromFormat('d/m/Y', $request->sr_date)->format('Y-m-d'),
                 'customer_name' => $request->customer_name, 
                 'dp_no_id' => $request->dp_no_id, 

                 'transporter_id'       => $request->transporter_id !="" ?  $request->transporter_id : null, 

                 'vehicle_no'       => $request->vehicle_no != "" ?  $request->vehicle_no : null, 

                 'lr_no_date'       => $request->lr_no_date !=  "" ?  $request->lr_no_date : null, 

                 'sp_note'       => $request->sp_note != "" ?  $request->sp_note : null,
                  'year_id' =>  $year_data->id,                 
                  'current_location_id' =>  $locationID,                 
                 'company_id' => Auth::user()->company_id,
                 'created_by_user_id' => Auth::user()->id, 
                 'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),   
             ]);
//  dd($salesreturn_data);
             
             if ($salesreturn_data->save()) {
                 foreach ($request->item_id as $ctKey => $ctVal ) 
                    {
                        
                        if ($ctVal != null) {
                        //  dd($ctVal);
                      
                             $sr_part_data =  SalesReturnDetails::create([
                                 'sr_id' => $salesreturn_data->sr_id,

                                 'le_details_id' => !empty($request->le_details_id[$ctKey]) 
                                    ? $request->le_details_id[$ctKey] 
                                    : null,

                                 'le_secondary_details_id' => !empty($request->le_secondary_details_id[$ctKey]) 
                                    ? $request->le_secondary_details_id[$ctKey] 
                                    : null,

                                'dp_details_id' => !empty($request->dp_details_id[$ctKey]) 
                                                ? $request->dp_details_id[$ctKey] 
                                                : null,

                            
                                 'item_id'=>$ctVal,
                                'item_details_id' => isset($request->item_details_id[$ctKey]) ? $request->item_details_id[$ctKey] : null,
     
                                 'sr_details_qty' => isset($request->sr_details_qty[$ctKey]) ? $request->sr_details_qty[$ctKey] : null,

                                 'sr_qty' => isset($request->sr_qty[$ctKey]) ? $request->sr_qty[$ctKey] : null,
                                 'fitting_item' => isset($request->fitting_item[$ctKey]) ? $request->fitting_item[$ctKey] : null,
     
                                 'remark' => isset($request->remark[$ctKey]) ? $request->remark[$ctKey] : '',

                                 'status' => 'Y',

                              
                             ]);

                             if(isset($request->fitting_item[$ctKey]) && $request->fitting_item[$ctKey] == 'yes'){
                                 $dpd_details_data = DispatchPlanDetailsDetails::where('dp_details_id',$request->dp_details_id[$ctKey])->get();

                                if($dpd_details_data->isNotEmpty()){
                                    foreach($dpd_details_data as $dpdKey=>$dpdVal){
                                        stockEffect($locationID,$dpdVal->item_id,$dpdVal->item_id,$dpdVal->plan_qty,0,'add','U','Sales Return Details',$sr_part_data->sr_details_id);
                                    }
                                     
                                    
                                }
                            }else if(isset($request->item_details_id[$ctKey]) && $request->item_details_id[$ctKey] != ""){
                            //    stockDetailsEffect($locationID,$request->item_details_id[$ctKey],$request->item_details_id[$ctKey],$request->sr_qty[$ctKey],0,'add','U','Sales Return Details',$sr_part_data->sr_details_id,'Yes','Sales Return Details',$sr_part_data->sr_details_id);
                               stockDetailsEffect($locationID,$request->item_details_id[$ctKey],$request->item_details_id[$ctKey],$request->sr_details_qty[$ctKey],0,'add','U','Sales Return Details',$sr_part_data->sr_details_id,'Yes','Sales Return Details',$sr_part_data->sr_details_id);
                            }
                            else{
                                 stockEffect($locationID,$ctVal,$ctVal,$request->sr_qty[$ctKey],0,'add','U','Sales Return Details',$sr_part_data->sr_details_id);
                               
                             }
                             
                     
                           
                    }                     
                 }                 
             }
             
             if($salesreturn_data->save())
             { 
                 DB::commit();
                 return response()->json([
                     'response_code' => '1',                
                    
                     'response_message' => 'Record Inserted Successfully.',
                 ]);
             }
             
             else {
                DB::rollBack();
                 return response()->json([
                     'response_code' => '0',
                     'response_message' => 'Record Not Inserted',
                 ]);
             }
         }
         catch(\Exception $e){
             DB::rollBack();
             getActivityLogs("Sales Return", "add", $e->getMessage(),$e->getLine());

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
            //  return response()->json([
            //      'response_code' => '0',
            //      'response_message' => 'Error Occured Record Not Inserted',
            //      'original_error' => $e->getMessage()
            //  ]);
         }
 
    }

     public function show(SalesReturn $SalesReturn, $id)
    {
        // return view('edit.edit-sales_return')->with('id',$id);
         return view('edit.edit-sales_return', compact('id'));
    }


    public function edit($id,$forPrint = null)
    // public function edit(Request $request, $id,$forPrint = null)
    {
        $locationID = getCurrentLocation()->id;
        $isAnyPartInUse = false;
        
        $sales_return = SalesReturn::select([
            'sales_return.*','sales_order.customer_name','dispatch_plan.dp_number','dispatch_plan.dp_date','transporters.transporter_name'])
        ->leftJoin('sales_order','sales_order.customer_name','=','sales_return.customer_name')      
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','sales_return.dp_no_id')      
        ->leftJoin('transporters','transporters.id','=','sales_return.transporter_id')      
        ->where('sales_return.sr_id',$id)->first();
        
        $sales_return_part = SalesReturnDetails::select(['sales_return_details.*','dispatch_plan.dp_id','dispatch_plan.dp_number as dp_no','items.item_name','items.item_code','loading_entry_details.loading_qty as dc_qty','item_details.secondary_item_name','item_details.secondary_qty',       'loading_entry_secondary_details.plan_qty','loading_entry_secondary_details.item_details_id',  
        DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),      
             DB::raw("(SELECT loading_entry_details.loading_qty  
        -  
        (SELECT IFNULL(SUM(sales_return_details.sr_qty),0)
        FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),  

          DB::raw("(SELECT loading_entry_secondary_details.plan_qty  
    -  
    (SELECT IFNULL(SUM(sales_return_details.sr_details_qty),0)
    FROM sales_return_details  WHERE sales_return_details.item_details_id = loading_entry_secondary_details.item_details_id AND sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_details_dc_qty"), 
        ])
        ->leftJoin('items','items.id','=','sales_return_details.item_id') 
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')    
        ->leftJoin('loading_entry_secondary_details', 'loading_entry_secondary_details.le_secondary_details_id', '=', 'sales_return_details.le_secondary_details_id')        
        ->leftJoin('loading_entry_details', 'loading_entry_details.le_details_id', '=', 'sales_return_details.le_details_id')        
        ->leftJoin('item_details','item_details.item_details_id','=','sales_return_details.item_details_id')    
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','sales_return_details.dp_details_id')
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id','dispatch_plan_details.dp_id')
        ->where('sales_return_details.sr_id',$id)->get(); 

   
        if($sales_return){

            $sales_return->sr_date = Date::createFromFormat('Y-m-d', $sales_return->sr_date)->format('d/m/Y');
            $sales_return->dp_date = Date::createFromFormat('Y-m-d', $sales_return->dp_date)->format('d/m/Y');


            return response()->json([
                'sr_data' => $sales_return,
                'sr_part_details' => $sales_return_part,
                // 'itemDetails' => $itemDetails,
                'response_code' => '1',
                'response_message' => '',
            ]);
        }
        
      
    }
    public function update(Request $request, SalesReturn $SalesReturn)
    {
        // dd($request->all());
        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation()->id;
        $locationID = getCurrentLocation()->id;

        $validated = $request->validate(
            [
                'sr_sequence' => ['required','max:155',Rule::unique('sales_return')->where(function ($query) use ($request,$year_data, $locationID) {
                    return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'sr_id')],

                'sr_number' => ['required', 'max:155', Rule::unique('sales_return')->where(function ($query) use ($request, $year_data, $locationID) {
                    return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'sr_id')],              
            ],
            [
                'sr_sequence.unique'=>'SR. No. Is Already Exists',    
                'sr_number.required' => 'Please Enter SR NO.',
                'sr_number.max' => 'Maximum 155 Characters Allowed',
            ]
        );


        // $soFormValue = $request->so_from_id_fix == 1 ? "customer" : "location";

        //  if($request->sr_from_id_fix == 1){
        //     $srFormValue =  "customer";
        // }elseif($request->sr_from_id_fix == 2){
        //     $srFormValue = "cash_carry";
        // }
 
        DB::beginTransaction();
        try {
            $salesreturn_data =  SalesReturn ::where("sr_id", "=", $request->id)->update([
                // 'sr_from_value_fix'=>$srFormValue,                
                // 'sr_from_id_fix'=>$request->sr_from_id_fix,     
                'sr_sequence' => $request->sr_sequence,                
                'sr_number' => $request->sr_number,                
                'sr_date' => Date::createFromFormat('d/m/Y', $request->sr_date)->format('Y-m-d'),
                // 'so_customer_id' => $request->so_customer_id,                
                'customer_name' => $request->customer_name ? $request->customer_name : "" ,
                'dp_no_id' => $request->dp_no_id ? $request->dp_no_id : "" ,
                'current_location_id' => $locationCode,
                'transporter_id'   => $request->transporter_id !="" ?  $request->transporter_id : null,
                'vehicle_no'       => $request->vehicle_no !="" ?  $request->vehicle_no : "", 

                'lr_no_date'       => $request->lr_no_date != "" ?  $request->lr_no_date : "", 

                'sp_note'       => $request->sp_note != "" ?  $request->sp_note : "", 
                'year_id' =>  $year_data->id,
                'last_by_user_id' => Auth::user()->id,
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),                
            ]);


            if ($salesreturn_data)   {              
           

                if (isset($request->sales_return_detail_id) && !empty($request->sales_return_detail_id)) {

                    $ReturnDetails =  SalesReturnDetails::where('sr_id',$request->id)->update([
                      'status' => 'D',
                    ]);
                  

                    foreach ($request->sales_return_detail_id as $sodKey => $sodVal) {

                        if($sodVal == "0"){
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                               
                                $SalesReturnDetails =  SalesReturnDetails::create([
                                    'sr_id' => $request->id,
                                    'item_id' => $request->item_id[$sodKey],
                                     'item_details_id'     => isset($request->item_details_id[$sodKey]) ? $request->item_details_id[$sodKey]   : null,
                                    'le_details_id' => isset($request->le_details_id[$sodKey]) ? $request->le_details_id[$sodKey] : null,
                                    'le_secondary_details_id' => !empty($request->le_secondary_details_id[$sodKey]) 
                                    ? $request->le_secondary_details_id[$sodKey] 
                                    : null,
                                    'dp_details_id' => isset($request->dp_details_id[$sodKey]) ? $request->dp_details_id[$sodKey]: null,
                                    'sr_details_qty' =>isset($request->sr_details_qty[$sodKey]) ? $request->sr_details_qty[$sodKey] : null,
                                    'sr_qty' =>isset($request->sr_qty[$sodKey]) ? $request->sr_qty[$sodKey] : null,
                                    'fitting_item' => isset($request->fitting_item[$sodKey]) ? $request->fitting_item[$sodKey] : null,
                                    'remark' => isset($request->remark[$sodKey]) ? $request->remark[$sodKey] : '',
                                    'status'=> 'Y',
                                ]);
                               
                                
                                if(isset($request->fitting_item[$sodKey]) && $request->fitting_item[$sodKey] == 'yes'){
                                    $dpd_details_data = DispatchPlanDetailsDetails::where('dp_details_id',$request->dp_details_id[$sodKey])->get();

                                    if($dpd_details_data->isNotEmpty()){
                                        foreach($dpd_details_data as $dpdKey=>$dpdVal){
                                            stockEffect($locationID,$dpdVal->item_id,$dpdVal->item_id,$dpdVal->plan_qty,0,'add','U','Sales Return Details',$SalesReturnDetails->sr_details_id);
                                        }                                    
                                        
                                    }

                                   
                                }else if(isset($request->item_details_id[$sodKey]) && $request->item_details_id[$sodKey] != ""){
                                //  stockDetailsEffect($locationID,$request->item_details_id[$sodKey],$request->item_details_id[$sodKey],$request->sr_qty[$sodKey],0,'add','U','Sales Return Details',$SalesReturnDetails->sr_details_id,'Yes','Sales Return Details',$SalesReturnDetails->sr_details_id);
                                  stockDetailsEffect($locationID,$request->item_details_id[$sodKey],$request->item_details_id[$sodKey],$request->sr_details_qty[$sodKey],0,'add','U','Sales Return Details',$SalesReturnDetails->sr_details_id,'Yes','Sales Return Details',$SalesReturnDetails->sr_details_id);
                                }else{
                                   
                                    stockEffect($locationID,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->sr_qty[$sodKey],0,'add','U','Sales Return Details',$SalesReturnDetails->sr_details_id);  
                                }
                            }

                        }else{                          
                            
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){

                                
                                $SalesReturnDetails =  SalesReturnDetails::where('sr_details_id',$sodVal)->update([
                                    'sr_id' => $request->id,
                                    'item_id' => $request->item_id[$sodKey],
                                    'item_details_id'     => isset($request->item_details_id[$sodKey]) ? $request->item_details_id[$sodKey]   : null,
                                    'le_details_id' => isset($request->le_details_id[$sodKey]) ? $request->le_details_id[$sodKey] : null,
                                    'le_secondary_details_id' => !empty($request->le_secondary_details_id[$sodKey]) 
                                    ? $request->le_secondary_details_id[$sodKey] 
                                    : null,
                                    'dp_details_id' => isset($request->dp_details_id[$sodKey]) ? $request->dp_details_id[$sodKey]: null,
                                    'sr_details_qty' =>isset($request->sr_details_qty[$sodKey]) ? $request->sr_details_qty[$sodKey] : null,
                                    'sr_qty' =>isset($request->sr_qty[$sodKey]) ? $request->sr_qty[$sodKey] : null,
                                    'fitting_item' => isset($request->fitting_item[$sodKey]) ? $request->fitting_item[$sodKey] : null,
                                    'remark' => isset($request->remark[$sodKey]) ? $request->remark[$sodKey] : '',
                                    'status'=> 'Y',
                                ]); 
                                
                                
                                $orgFit = $request->org_fitting_item[$sodKey] ?? null;
                                $newFit = $request->fitting_item[$sodKey] ?? null;
                                $preDetails = $request->pre_item_details_id[$sodKey] ?? "";
                                $newDetails = $request->item_details_id[$sodKey] ?? "";

                                $itemId     = $request->item_id[$sodKey] ?? null;
                                $preItemId  = $request->pre_item_id[$sodKey] ?? null;
                                $srQty      = $request->sr_qty[$sodKey] ?? 0.000;
                                $preSrQty   = $request->pre_sr_qty[$sodKey] ?? 0.000;
                                $SrDetailsQty   = $request->sr_details_qty[$sodKey] ?? '';
                                $preSrDetailsQty   = $request->pre_sr_details_qty[$sodKey] ?? '';


                                // Case A: Normal item (no fitting, no details)
                                if ($orgFit === 'no' && $newFit === 'no' && empty($preDetails) && empty($newDetails)) {
                                    // normal item update
                                    stockEffect(
                                        $locationID,$itemId,$preItemId,$srQty,$preSrQty,'edit','U','Sales Return Details',$sodVal
                                    );
                                }

                                // Case B1: Fitting As-it-is
                                elseif ($orgFit === 'yes' && $newFit === 'yes') {
                                    //fit As it is update
                                    stockEffect(
                                        $locationID,
                                        $request->item_id[$sodKey],
                                        $request->item_id[$sodKey],
                                        $request->sr_qty[$sodKey],
                                        $request->pre_sr_qty[$sodKey],
                                        'edit','U','Sales Return Details',$sodVal
                                    );
                                }

                                // Case B2: Details As-it-is
                                elseif (!empty($preDetails) && !empty($newDetails)) {
                                    // details As it is update
                                    // stockDetailsEffect(
                                    //     $locationID,$newDetails,$preDetails,$srQty,$preSrQty,'edit','U','Sales Return Details',$sodVal,'Yes','Sales Return Details',$sodVal
                                    // );
                                    stockDetailsEffect(
                                        $locationID,$newDetails,$preDetails,$SrDetailsQty,$preSrDetailsQty,'edit','U','Sales Return Details',$sodVal,'Yes','Sales Return Details',$sodVal
                                    );
                                }

                                // Case B3: Other
                                else {
                                    // details mathi sadi k fit karva
                                    if(!empty($preDetails) && empty($newDetails))
                                    { 
                                        // details delet karo
                                    //   stockDetailsEffect($locationID,$preDetails,$preDetails,0,$preSrQty,'delete','U','Sales Return Details',$sodVal,'Yes','Sales Return Details',$sodVal);
                                      stockDetailsEffect($locationID,$preDetails,$preDetails,0,$preSrDetailsQty,'delete','U','Sales Return Details',$sodVal,'Yes','Sales Return Details',$sodVal);

                                        if($newFit === 'yes')
                                        {
                                            //details  mathi fit kare tyare
                                            $dpd_details_data = DispatchPlanDetailsDetails::where('dp_details_id',$request->dp_details_id[$sodKey])->get();
                                            if($dpd_details_data->isNotEmpty()){
                                                foreach($dpd_details_data as $dpdKey=>$dpdVal){
                                                    stockEffect($locationID,$dpdVal->item_id,$dpdVal->item_id,$dpdVal->plan_qty,0,'add','U','Sales Return Details',$sodVal);
                                                }                                    
                                            
                                            }

                                        }else{
                                            // details  mathi sadi kare tyare
                                            stockEffect($locationID, $itemId, $itemId, $srQty, 0, 'add','U','Sales Return Details',$sodVal);
                                        }

                                    }
                                    // fitting mathi sadi k details kare tyare
                                    else if($orgFit === 'yes' && $newFit !='yes'){
                                        // fit change kare tyare
                                        $dpd_details_data = DispatchPlanDetailsDetails::where('dp_details_id',$request->org_dp_details_id[$sodKey])->get();

                                        if($dpd_details_data->isNotEmpty()){
                                            foreach($dpd_details_data as $dpdKey=>$dpdVal){
                                                stockEffect($locationID,$dpdVal->item_id,$dpdVal->item_id,0,$dpdVal->plan_qty,'delete','U','Sales Return Details',$sodVal);
                                            }                                    
                                            
                                        }
                                        if(!empty($newDetails)){   
                                            //Fit mathi details kare tyare
                                            // stockDetailsEffect($locationID, $newDetails, $newDetails, $srQty, 0, 'add', 'U', 'Sales Return Details', $sodVal, 'Yes', 'Sales Return Details', $sodVal);
                                            stockDetailsEffect($locationID, $newDetails, $newDetails, $SrDetailsQty, 0, 'add', 'U', 'Sales Return Details', $sodVal, 'Yes', 'Sales Return Details', $sodVal);
                                        }
                                        else{
                                            // Fit mathi sadi kare tyare
                                            stockEffect($locationID, $itemId, $itemId, $srQty, 0, 'add','U','Sales Return Details',$sodVal);
                                        }

                                    }
                                    // sadi item mathi details k fit kare tyare
                                    else if(empty($preDetails) && $orgFit != 'yes'){
                                        // sadi change kare tyare
                                        stockEffect($locationID, $preItemId,$preItemId, 0, $preSrQty, 'delete', 'U', 'Sales Return Details', $sodVal);

                                        if($newFit === 'yes')
                                        {
                                            // sadi item mathi fit kare tyare
                                            $dpd_details_data = DispatchPlanDetailsDetails::where('dp_details_id',$request->dp_details_id[$sodKey])->get();
                                            if($dpd_details_data->isNotEmpty()){
                                                foreach($dpd_details_data as $dpdKey=>$dpdVal){
                                                    stockEffect($locationID,$dpdVal->item_id,$dpdVal->item_id,$dpdVal->plan_qty,0,'add','U','Sales Return Details',$sodVal);
                                                }                                    
                                            
                                            }

                                        }
                                        else{
                                            // sadi mathi details kare tyare
                                            // stockDetailsEffect($locationID, $newDetails, $newDetails, $srQty, 0, 'add', 'U', 'Sales Return Details', $sodVal, 'Yes', 'Sales Return Details', $sodVal);
                                            stockDetailsEffect($locationID, $newDetails, $newDetails, $SrDetailsQty, 0, 'add', 'U', 'Sales Return Details', $sodVal, 'Yes', 'Sales Return Details', $sodVal);
                                        }
                                    }

                                }

                            }
                        }

                    }
                }   


                $OldReturnDetails =  SalesReturnDetails::where('sr_id',$request->id)->where('status','D')->get();
                if($OldReturnDetails->isNotEmpty()){
                    foreach($OldReturnDetails as $okey => $oval){

                        if($oval->fitting_item == 'yes'){
                            $dpd_details_data = DispatchPlanDetailsDetails::where('dp_details_id',$oval->dp_details_id)->get();

                            if($dpd_details_data->isNotEmpty()){
                                foreach($dpd_details_data as $dpdKey=>$dpdVal){
                                    stockEffect($locationID,$dpdVal->item_id,$dpdVal->item_id,0,$dpdVal->plan_qty,'delete','U','Sales Return Details',$oval->sr_details_id);
                                }    
                                
                            }

                        }
                        else if($oval->item_details_id != ""){
                            // stockDetailsEffect($locationID,$oval->item_details_id,$oval->item_details_id,0,$oval->sr_qty,'delete','U','Sales Return Details',$oval->sr_details_id,'Yes','Sales Return Details',$oval->sr_details_id);
                            stockDetailsEffect($locationID,$oval->item_details_id,$oval->item_details_id,0,$oval->sr_details_qty,'delete','U','Sales Return Details',$oval->sr_details_id,'Yes','Sales Return Details',$oval->sr_details_id);
                        }else{
                            stockEffect($locationID,$oval->item_id,$oval->item_id,0,$oval->sr_qty,'delete','U','Sales Return Details',$oval->sr_details_id);              
                        }
                        

                        $sodDetails = SalesReturnDetails::where('sr_details_id',$oval->sr_details_id)->where('status','D')->delete();
                    }
                }


                

                
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                ]);
            } 
            else {
                DB::rollBack();
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Updated',
                ]);
            }
        }
        catch (\Exception $e) {
        //    dd($e->getLine());
            DB::rollBack();
            getActivityLogs("Sales Return", "update", $e->getMessage(),$e->getLine());

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
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => 'Error Occured Record Not Updated',
            //     'original_error' => $e->getMessage()
            // ]);
        }
    }

    public function destroy(Request $request)
    {
        $locationID = getCurrentLocation()->id;

        DB::beginTransaction();
        try{
            
        
            SalesReturn::destroy($request->id);

            $sr_data = SalesReturnDetails::where('sr_id','=',$request->id)->get();
            
            if($sr_data->isNotEmpty()){
                foreach($sr_data as $skey=>$sval){ 
                  
                    if($sval->fitting_item == 'yes'){
                        $dpd_details_data = DispatchPlanDetailsDetails::where('dp_details_id',$sval->dp_details_id)->get();

                        if($dpd_details_data->isNotEmpty()){
                            foreach($dpd_details_data as $dpdKey=>$dpdVal){
                                stockEffect($locationID,$dpdVal->item_id,$dpdVal->item_id,0,$dpdVal->plan_qty,'delete','U','Sales Return Details',$sval['sr_details_id']);
                            }    
                            
                        }

                    }else if($sval->item_details_id != ""){
                        //   stockDetailsEffect($locationID,$sval['item_details_id'],$sval['item_details_id'],0,$sval['sr_qty'],'delete','U','Sales Return Details',$sval['sr_details_id'],'Yes','Sales Return Details',$sval['sr_details_id']);
                          stockDetailsEffect($locationID,$sval['item_details_id'],$sval['item_details_id'],0,$sval['sr_details_qty'],'delete','U','Sales Return Details',$sval['sr_details_id'],'Yes','Sales Return Details',$sval['sr_details_id']);
                    }
                    else{
                        stockEffect($locationID,$sval['item_id'],$sval['item_id'],0,$sval['sr_qty'],'delete','U','Sales Return Details',$sval['sr_details_id']);                  
                    }
                }

            }   
            

            SalesReturnDetails::where('sr_id',$request->id)->delete();
            DB::commit();

            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }
        catch(\Exception $e){       
            dd($e->getMessage());
            DB::rollBack(); 
            getActivityLogs("Sales Return", "delete", $e->getMessage(),$e->getLine());

            // $errorMessage =  $e->errorInfo[2];
            // preg_match('/`([^`]+)`\.`([^`]+)`/', $errorMessage, $matches);

            // $tableName = $matches[2];            
        
            // $table = DeleteMessage($tableName);

            // if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
            //     $error_msg = "This is used somewhere, you can't delete";
            //     // $error_msg = "You Can't Delete, SO Is Used In ".$table;
            // }else{
            //     $error_msg = "Record Not Deleted";
            // }
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => $error_msg,
            // ]);

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

    public function getLatestSrNo(Request $request)
    {    
        $modal  =  SalesReturn::class;
        $sequence = 'sr_sequence';
        $prefix = 'SR';
        $po_num_format = getLatestSequence($modal,$sequence,$prefix);

        $locationName = getCurrentLocation();

        return response()->json([
          'response_code' => 1,
          'latest_po_no'  => $po_num_format['format'],
          'number'        => $po_num_format['isFound'],
          'location'      => $locationName
      ]);
    }
    
    public function getSalesOrderAllCustomer(Request $request)
    {
        $yearIds = getCompanyYearIdsToTill();
        $locationName = getCurrentLocation();

        // below query to check le_detail_id us in sales_return table
        $sr_fitting_le_details_id = SalesReturnDetails::select('loading_entry_details.le_details_id')
        ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','sales_return_details.le_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')
        ->whereIn('loading_entry.year_id',$yearIds)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();

        // below query to get le_detail_id where not us in sales_return table
        $fitting_le_details_id = LoadingEntryDetails::select('loading_entry_details.le_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')
        ->whereNotIn('loading_entry_details.le_details_id',$sr_fitting_le_details_id)
        ->whereIn('loading_entry.year_id',$yearIds)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();

        // below query get le_details_id fitting_item = no
        $without_fitting_le_detail_id = LoadingEntryDetails::select(['loading_entry_details.le_details_id',
        DB::raw("(SELECT loading_entry_details.loading_qty -  (SELECT IFNULL(SUM(sales_return_details.sr_qty),0) FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),     
        ])
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id') 
        ->where('loading_entry.current_location_id',$locationName->id)
        ->where('dispatch_plan_details.fitting_item','no')
        ->whereIn('loading_entry.year_id',$yearIds)
        ->having('pend_dc_qty','>',0)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();

        $getCustomer = SalesOrderDetail::select('sales_order.customer_name')
        ->leftJoin('sales_order','sales_order.id','sales_order_details.so_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.so_details_id', 'sales_order_details.so_details_id')
        ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','dispatch_plan_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')
        ->whereIn('loading_entry_details.le_details_id',array_merge($fitting_le_details_id,$without_fitting_le_detail_id))
        ->where('sales_order.so_from_id_fix','!=',3)
        ->where('loading_entry.current_location_id',$locationName->id)
        ->whereIn('loading_entry.year_id',$yearIds)
        ->groupBy('sales_order.customer_name')
        ->get();

        return response()->json([
            'response_code' => 1,
            'SOCustomer'  => $getCustomer
        ]);
    }

    public function getCustomerSubsidy(Request $request){

        $yearIds = getCompanyYearIdsToTill();
        $locationName = getCurrentLocation();       
       

        // below query to check le_detail_id us in sales_return table
        $sr_fitting_le_details_id = SalesReturnDetails::select('loading_entry_details.le_details_id')         
        ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','sales_return_details.le_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')    
        ->whereIn('loading_entry.year_id',$yearIds)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();
      

        // below query to get le_detail_id where not us in sales_return table
        $fitting_le_details_id = LoadingEntryDetails::select('loading_entry_details.le_details_id')    
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')      
        ->whereNotIn('loading_entry_details.le_details_id',$sr_fitting_le_details_id)
        ->whereIn('loading_entry.year_id',$yearIds)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();


        // below query get le_details_id fitting_item = no

        $without_fitting_le_detail_id = LoadingEntryDetails::select(['loading_entry_details.le_details_id',
        DB::raw("(SELECT loading_entry_details.loading_qty -  (SELECT IFNULL(SUM(sales_return_details.sr_qty),0) FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),     
        ])
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id') 
        ->where('loading_entry.current_location_id',$locationName->id)           
        ->where('dispatch_plan_details.fitting_item','no')       
        ->whereIn('loading_entry.year_id',$yearIds)
        ->having('pend_dc_qty','>',0)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();

        

        $getCustomer = SalesOrderDetail::select('sales_order.customer_name')
        ->leftJoin('sales_order','sales_order.id','sales_order_details.so_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.so_details_id', 'sales_order_details.so_details_id')  
        ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','dispatch_plan_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id') 
        ->whereIn('loading_entry_details.le_details_id',array_merge($fitting_le_details_id,$without_fitting_le_detail_id))
        ->where('sales_order.so_from_id_fix',1)
        ->where('loading_entry.current_location_id',$locationName->id)
        ->whereIn('loading_entry.year_id',$yearIds)        
        ->groupBy('sales_order.customer_name')
        ->get();
       

         return response()->json([
            'response_code' => 1,
            'SOCustomer'  => $getCustomer
        ]);

        
    }
    
public function getCustomerCashCarry(Request $request)
{
    $yearIds = getCompanyYearIdsToTill();
    $locationName = getCurrentLocation();

       // below query to check le_detail_id us in sales_return table
        $sr_fitting_le_details_id = SalesReturnDetails::select('loading_entry_details.le_details_id')         
        ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','sales_return_details.le_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')
        ->whereIn('loading_entry.year_id',$yearIds)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();

        // below query to get le_detail_id where not us in sales_return table
        $fitting_le_details_id = LoadingEntryDetails::select('loading_entry_details.le_details_id')    
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')
        ->whereNotIn('loading_entry_details.le_details_id',$sr_fitting_le_details_id)
        ->whereIn('loading_entry.year_id',$yearIds)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();

        // below query get le_details_id fitting_item = no

        $without_fitting_le_detail_id = LoadingEntryDetails::select(['loading_entry_details.le_details_id',
        DB::raw("(SELECT  loading_entry_details.loading_qty -  (SELECT IFNULL(SUM(sales_return_details.sr_qty),0) FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),     
        ])
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id') 
        ->where('loading_entry.current_location_id',$locationName->id)
        ->where('dispatch_plan_details.fitting_item','no')       
        ->whereIn('loading_entry.year_id',$yearIds)
        ->having('pend_dc_qty','>',0)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();


        $getCustomer = SalesOrderDetail::select('sales_order.customer_name')
        ->leftJoin('sales_order','sales_order.id','sales_order_details.so_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.so_details_id', 'sales_order_details.so_details_id')  
        ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id','dispatch_plan_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id') 
        ->whereIn('loading_entry_details.le_details_id',array_merge($fitting_le_details_id,$without_fitting_le_detail_id))
        ->where('sales_order.so_from_id_fix',2)
        ->where('loading_entry.current_location_id',$locationName->id)
        ->whereIn('loading_entry.year_id',$yearIds)
        ->groupBy('sales_order.customer_name')
        ->get();

    return response()->json([
        'response_code' => 1,
        'SOCustomer'    => $getCustomer
    ]);
}


 
public function getItemsFromSalesOrder(Request $request)
{
    $location = getCurrentLocation();
    $yearIds = getCompanyYearIdsToTill();
    
     if(isset($request->id)){

        $edit_mappedItems = SalesReturnDetails::select( 'items.id','items.item_name','units.unit_name','items.item_code','item_groups.item_group_name','loading_entry.current_location_id',
        DB::raw('sales_return_details.sr_qty as pend_dc_qty'))
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id' ,'sales_return_details.dp_details_id')
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id' ,'dispatch_plan_details.dp_id')
        ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id' ,'dispatch_plan_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id' ,'loading_entry_details.le_id')
        ->leftJoin('items', 'items.id', '=', 'sales_return_details.item_id')
        ->leftJoin('item_groups', 'item_groups.id', '=', 'items.item_group_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')
        ->where('sales_return_details.sr_id',$request->id) 
        ->where('items.status', 'active')
        ->where('loading_entry.current_location_id', $location->id) 
        ->get(); 

    }    

        // below query to check le_detail_id us in sales_return table
        $sr_fitting_le_details_id = SalesReturnDetails::select('loading_entry_details.le_details_id')         
        ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','sales_return_details.le_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')
        ->whereIn('loading_entry.year_id',$yearIds)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();

        // below query to get le_detail_id where not us in sales_return table
        $fitting_le_details_id = LoadingEntryDetails::select('loading_entry_details.le_details_id')    
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->where('dispatch_plan_details.fitting_item','yes')
        ->whereNotIn('loading_entry_details.le_details_id',$sr_fitting_le_details_id)
        ->whereIn('loading_entry.year_id',$yearIds)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();

        // below query get le_details_id fitting_item = no

        $without_fitting_le_detail_id = LoadingEntryDetails::select(['loading_entry_details.le_details_id',
        DB::raw("(SELECT  loading_entry_details.loading_qty -  (SELECT IFNULL(SUM(sales_return_details.sr_qty),0) FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),     
        ])
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id') 
        ->where('loading_entry.current_location_id',$location->id)
        ->where('dispatch_plan_details.fitting_item','no')       
        ->whereIn('loading_entry.year_id',$yearIds)
        ->having('pend_dc_qty','>',0)
        ->pluck('loading_entry_details.le_details_id')
        ->toArray();


        $mappedItems = LoadingEntryDetails::select('items.id','items.item_name','items.item_code',
        'item_groups.item_group_name','units.unit_name','loading_entry.current_location_id',
        )
        ->leftJoin('dispatch_plan_details', 'dispatch_plan_details.dp_details_id', '=', 'loading_entry_details.dp_details_id')
        ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
        ->leftJoin('items', 'items.id', '=', 'sales_order_details.item_id')
        ->leftJoin('loading_entry', 'loading_entry.le_id', '=', 'loading_entry_details.le_id')
        ->leftJoin('item_groups', 'item_groups.id', '=', 'items.item_group_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')
        ->leftJoin('sales_return_details', 'sales_return_details.le_details_id', '=', 'loading_entry_details.le_details_id')
        ->where('items.status', 'active')
        ->where('loading_entry.current_location_id', $location->id)
        ->where('sales_order.customer_name', $request->customer_name)
        ->whereIn('loading_entry_details.le_details_id',array_merge($fitting_le_details_id,$without_fitting_le_detail_id))
        ->get();


        if(isset($edit_mappedItems)){
            $data = collect($mappedItems)->merge($edit_mappedItems);
            $grouped = $data->groupBy('id');            

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }  
                    
                      $carry->pend_dc_qty += (float) $item->pend_dc_qty;
                    return $carry;
                });
            });



          $mappedItems = $merged->values();   

        }
        
        return response()->json([
            'response_code' => 1,
            'SOCustomerItems' => $mappedItems
        ]);
}

public function getDPNumber(Request $request){
    $location = getCurrentLocation();   
    $yearIds = getCompanyYearIdsToTill();
        // dd($request->all());
        if(isset($request->id)){
         
            $edit_dp_no = SalesReturnDetails::select('dispatch_plan.dp_number','loading_entry_details.loading_qty as plan_qty','dispatch_plan.dp_id','dispatch_plan_details.dp_details_id','loading_entry_details.le_details_id','sales_return_details.item_id','sales_return_details.fitting_item',
            DB::raw('sales_return_details.sr_qty as pend_dc_qty'))
            ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id' ,'sales_return_details.dp_details_id')
            ->leftJoin('sales_order_details','sales_order_details.so_details_id' ,'dispatch_plan_details.so_details_id')
            ->leftJoin('sales_order','sales_order.id' ,'sales_order_details.so_id')
            ->leftJoin('dispatch_plan','dispatch_plan.dp_id' ,'dispatch_plan_details.dp_id')
            ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id' ,'dispatch_plan_details.dp_details_id')
            ->where('sales_return_details.sr_id',$request->id)  
            // ->where('sales_return_details.item_id',$request->item_id)  
            ->where('sales_order.customer_name', $request->customer_name)
            ->get(); 
        }

            // below query to check le_detail_id us in sales_return table
            $sr_fitting_le_details_id = SalesReturnDetails::select('loading_entry_details.le_details_id')         
            ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','sales_return_details.le_details_id')
            ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
            ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
            ->where('dispatch_plan_details.fitting_item','yes')
            ->whereIn('loading_entry.year_id',$yearIds)
            ->pluck('loading_entry_details.le_details_id')
            ->toArray();

            // below query to get le_detail_id where not us in sales_return table
            $fitting_le_details_id = LoadingEntryDetails::select('loading_entry_details.le_details_id')    
            ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
            ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
            ->where('dispatch_plan_details.fitting_item','yes')
            ->whereNotIn('loading_entry_details.le_details_id',$sr_fitting_le_details_id)
            ->whereIn('loading_entry.year_id',$yearIds)
            ->pluck('loading_entry_details.le_details_id')
            ->toArray();

            // below query get le_details_id fitting_item = no

            $without_fitting_le_detail_id = LoadingEntryDetails::select(['loading_entry_details.le_details_id',
            DB::raw("(SELECT  loading_entry_details.loading_qty  -  (SELECT IFNULL(SUM(sales_return_details.sr_qty),0) FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),     
            ])
            ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
            ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id') 
            ->where('loading_entry.current_location_id',$location->id)
            ->where('dispatch_plan_details.fitting_item','no')       
            ->whereIn('loading_entry.year_id',$yearIds)
            ->having('pend_dc_qty','>',0)
            ->pluck('loading_entry_details.le_details_id')
            ->toArray();

        
            $dp_no = DispatchPlanDetails::select('dispatch_plan.dp_number','loading_entry_details.loading_qty as plan_qty','dispatch_plan.dp_id','dispatch_plan_details.dp_details_id','loading_entry.current_location_id','loading_entry_details.le_details_id','dispatch_plan_details.item_id','dispatch_plan_details.fitting_item',
            DB::raw("(SELECT loading_entry_details.loading_qty  
            -  
            (SELECT IFNULL(SUM(sales_return_details.sr_qty),0)
            FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),     
            )

            ->leftJoin('dispatch_plan','dispatch_plan.dp_id' ,'dispatch_plan_details.dp_id')
            ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id' ,'dispatch_plan_details.dp_details_id')
            ->leftJoin('loading_entry','loading_entry.le_id' ,'loading_entry_details.le_id')
            ->leftJoin('sales_order_details','sales_order_details.so_details_id','dispatch_plan_details.so_details_id')
       
            ->leftJoin('sales_order','sales_order.id' ,'sales_order_details.so_id')
            // ->where('dispatch_plan_details.item_id',$request->item_id)
            ->where('loading_entry.current_location_id',$location->id)
            ->where('sales_order.customer_name', $request->customer_name)
            ->groupBy('dispatch_plan.dp_id')
            ->whereIn('loading_entry_details.le_details_id',array_merge($fitting_le_details_id,$without_fitting_le_detail_id))
            ->get(); 

            if(isset($edit_dp_no)){
                $data = collect($dp_no)->merge($edit_dp_no);
                $grouped = $data->groupBy('dp_id');            

                $merged = $grouped->map(function ($items) {
                    return $items->reduce(function ($carry, $item) {
                        if (!$carry) {
                            return $item;
                        }  
                        
                        $carry->pend_dc_qty += (float) $item->pend_dc_qty;
                        return $carry;
                    });
                });

                $dp_no = $merged->values();   

            }
            return response()->json([
                'response_code' => 1,
                'dp_number' => $dp_no
            ]);
}

public function getDispatchDataForSR(Request $request){
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();



        if(isset($request->id)){          
        
        //   $dp_detail_id = DispatchPlanDetails::select('dp_details_id')->whereIN('dispatch_plan_details.dp_id',$request->dpids)->get();

          $edit_loading_data  = SalesReturnDetails::select(['items.item_name','items.item_code','items.id', 'item_groups.item_group_name', 'units.unit_name','sales_order.so_number','sales_order.so_date', 'items.secondary_unit',
          'sales_order.customer_name','districts.district_name','locations.location_name', 'dispatch_plan_details.dp_details_id','sales_order_details.so_qty', 'dealers.dealer_name','dispatch_plan_details.item_id','dispatch_plan_details.fitting_item','dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.fitting_item','dispatch_plan.dp_id',
          'sales_order_details.so_qty', 'sales_order_details.so_details_id','loading_entry_details.loading_qty as dc_qty','loading_entry_details.le_details_id',DB::raw('sales_return_details.sr_qty as pend_dc_qty'),
        //  'loading_entry_details.loading_qty  as pending_qty',   
         'dispatch_plan_details.secondary_unit',
         'loading_entry_details.loading_qty',
          'dispatch_plan_details.allow_partial_dispatch',
           ])
         ->leftJoin('sales_return','sales_return.sr_id','=','sales_return_details.sr_id')
         ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','sales_return_details.le_details_id')
         ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','sales_return_details.dp_details_id')
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
        ->where('sales_return_details.sr_id',$request->id)  
        //   ->whereIn('loading_entry_details.dp_details_id',$dp_detail_id)
          ->get();

        }   

        $dispatch_data = getItemForSupllierReturn($request->customer_name,$request->dpids);

         
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


                    

                    
                    $newRequest = new Request();

                    $newRequest->so_details_id = $cpVal->so_details_id;
                    $newRequest->record_id = $cpVal->dp_details_id;
                    $newRequest->total_qty = $cpVal->so_qty;

                 

                  

                    // if($cpVal->fitting_item == 'yes'){
                    //     $cpVal->pending_dp_qty = 0;
                    // }else{

                    //     $pend_dp_qty = DispatchPlanDetails::where('dp_details_id', $cpVal->dp_details_id)
                    //     ->selectRaw('plan_qty - (SELECT IFNULL(SUM(loading_qty), 0) FROM loading_entry_details WHERE dp_details_id = dispatch_plan_details.dp_details_id) as pend_dp_qty')
                    //     ->value('pend_dp_qty'); 
                    //     $cpVal->pending_dp_qty = $pend_dp_qty;                     

                    //      if($cpVal->secondary_unit == 'Yes'){
                    //         if($cpVal->le_details_id == 0){
                    //             $cpVal->plan_qty = $cpVal->le_details_id == 0 ? $pend_dp_qty : $cpVal->loading_qty;
                    //         }else{
                    //             $dpSecondaryItemSum = LoadingEntrySecondaryDetails::where('loading_entry_secondary_details.dp_details_id', $cpVal->dp_details_id)->sum('plan_qty');

                    //             $SecondaryQtySum = LoadingEntrySecondaryDetails::
                    //             leftJoin('item_details','item_details.item_details_id','loading_entry_secondary_details.item_details_id')
                    //             ->where('loading_entry_secondary_details.dp_details_id',    $cpVal->dp_details_id)->sum('item_details.secondary_qty');

                    //               $cpVal->plan_qty = $dpSecondaryItemSum * $SecondaryQtySum;
                    //             }

                    //      }else{
                    //            $cpVal->plan_qty = $cpVal->le_details_id == 0 ? $pend_dp_qty : $cpVal->loading_qty;
                    //      }
                    // }                  

                }

                // $existingCount  = LoadingEntry::where('dp_number', 'LIKE', $cpVal->dp_number . '%')->where('le_id', '!=', $request->id)->count();

                // if($existingCount > 0){
                //     $cpVal->dp_number = $cpVal->dp_number . ' - ' . $existingCount ;
                // }

            }

            // foreach($dispatch_data as $cpKey => $cpVal){
            //     if($cpVal->secondary_unit == 'Yes' && $cpVal->le_details_id == 0){
            //         $dpSecondaryItem = DispatchPlanSecondaryDetails::select([
            //         'dispatch_plan_secondary_details.dp_secondary_details_id',
            //         'dispatch_plan_secondary_details.dp_details_id',
            //         'dispatch_plan_secondary_details.item_id',
            //         'dispatch_plan_secondary_details.item_details_id',
            //         'items.item_name',
            //         'items.item_code',
            //         'item_groups.item_group_name',
            //         'units.unit_name', 'item_details.secondary_item_name',
            //         DB::raw("((SELECT dispatch_plan_secondary_details.plan_qty -  IFNULL(SUM(loading_entry_secondary_details.plan_qty),0) FROM loading_entry_secondary_details  WHERE loading_entry_secondary_details.dp_secondary_details_id = dispatch_plan_secondary_details.dp_secondary_details_id)) as plan_qty"),
            //         // 'dispatch_plan_secondary_details.plan_qty',
            //         DB::raw('0 as le_secondary_details_id'), 
            //         'dispatch_plan_secondary_details.plan_qty as org_plan_qty',
                
            //         ])
            //         ->leftJoin('items', 'items.id', 'dispatch_plan_secondary_details.item_id')
            //         ->leftJoin('item_details', 'item_details.item_details_id', 'dispatch_plan_secondary_details.item_details_id')
            //         ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            //         ->leftJoin('units', 'units.id', 'items.unit_id')   
            //         ->where('dispatch_plan_secondary_details.dp_details_id', $cpVal->dp_details_id)
            //         ->having('plan_qty','>',0)
            //         ->get();

            //         $dispatchDetailArray[$cpVal->dp_details_id] = $dpSecondaryItem->isNotEmpty() ? $dpSecondaryItem : [];

            //     }else{
            //         $dpSecondaryItem = LoadingEntrySecondaryDetails::select([
            //         'loading_entry_secondary_details.le_secondary_details_id',
            //         'loading_entry_secondary_details.dp_secondary_details_id',
            //         'loading_entry_secondary_details.dp_details_id',
            //         'loading_entry_secondary_details.item_id',
            //         'loading_entry_secondary_details.item_details_id',
            //         'items.item_name',
            //         'items.item_code',
            //         'item_groups.item_group_name',
            //         'units.unit_name', 'item_details.secondary_item_name',
            //         DB::raw("((SELECT dispatch_plan_secondary_details.plan_qty -  IFNULL(SUM(loading_entry_secondary_details.plan_qty),0) FROM loading_entry_secondary_details  WHERE loading_entry_secondary_details.dp_secondary_details_id = dispatch_plan_secondary_details.dp_secondary_details_id) + loading_entry_secondary_details.plan_qty) as pend_plan_qty"),
            //        'loading_entry_secondary_details.plan_qty',
            //          'loading_entry_secondary_details.plan_qty as org_plan_qty',
                
            //         ])
            //         ->leftJoin('dispatch_plan_secondary_details', 'dispatch_plan_secondary_details.dp_secondary_details_id', 'loading_entry_secondary_details.dp_secondary_details_id')
            //         ->leftJoin('items', 'items.id', 'dispatch_plan_secondary_details.item_id')
            //         ->leftJoin('item_details', 'item_details.item_details_id', 'dispatch_plan_secondary_details.item_details_id')
            //         ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            //         ->leftJoin('units', 'units.id', 'items.unit_id')   
            //         ->where('loading_entry_secondary_details.dp_details_id', $cpVal->dp_details_id)
            //         ->get();

            //         $dispatchDetailArray[$cpVal->dp_details_id] = $dpSecondaryItem->isNotEmpty() ? $dpSecondaryItem : [];

                    
            //     }

                 

            // }

          if($dispatch_data != null){
              return response()->json([
                  'response_code' => '1',
                  'dispatch_data' => $dispatch_data,
              ]);
          }else{
              return response()->json([
                  'response_code' => '0',
                  'dispatch_data' => []
              ]);
          }

  }

    public function getFittingItemsForSR(Request $request){
        $locationID = getCurrentLocation()->id;
       $item_detail =    getItemDetailsForSalesReturn($request->dp_details_id,$request->item);
        // $item_detail = LoadingEntrySecondaryDetails::select('loading_entry_secondary_details.plan_qty','loading_entry_secondary_details.item_details_id','loading_entry_secondary_details.item_id','item_details.secondary_item_name','loading_entry_secondary_details.dp_details_id','units.unit_name','location_stock_details.secondary_stock_qty','dispatch_plan_details.dp_id',
        //  DB::raw("(SELECT loading_entry_secondary_details.plan_qty  
        //     -  
        //     (SELECT IFNULL(SUM(sales_return_details.sr_qty),0)
        //     FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"), )    
            
        // ->leftJoin('location_stock_details','location_stock_details.item_details_id','=','loading_entry_secondary_details.item_details_id')
        // ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_secondary_details.dp_details_id')
        // // ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
        // ->leftJoin('items','items.id','=','loading_entry_secondary_details.item_id')
        // ->leftJoin('item_details','item_details.item_details_id','=','loading_entry_secondary_details.item_details_id')
        // ->leftJoin('units','units.id','=','items.second_unit')
        // ->where('loading_entry_secondary_details.dp_details_id',$request->dp_details_id)
        // ->where('loading_entry_secondary_details.item_id',$request->item)
        // ->where('location_stock_details.location_id',$locationID)
        // ->get();

        return response()->json([
            'response_code' => 1,
           
            'item_detail' => $item_detail,
        ]);

    }




}