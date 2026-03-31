<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SOMapping;
use App\Models\SOMappingDetails;
use App\Models\CustomerReplacementEntry;
use App\Models\CustomerReplacementEntryDetails;
use App\Models\ReplacementItemDecisionDetails;
use App\Models\SalesOrder;
use App\Models\Item;
use App\Models\SOShortClose;
use Illuminate\Support\Facades\DB;
use Date;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DataTables;
use App\Models\Admin;
use Illuminate\Validation\Rule;
use App\Models\SalesOrderDetail;






class SOmappingController extends Controller
{
    //
    public function manage()
    {
        return view('manage.manage-so_mapping');
    }

    public function create()
    {
        return view('add.add-so_mapping');
    }

    public function index(SOMapping $mapping_data,Request $request,DataTables $dataTables)
    {
        $locationCode = getCurrentLocation()->id;
        $year_data    = getCurrentYearData();

        $mapping_data = SOMappingDetails::select(['so_mapping.mapping_id', 'so_mapping.so_mapping_number','so_mapping.mapping_date','so_mapping_details.map_qty','units.unit_name','so_mapping.customer_name',
        'items.item_name','so_mapping.created_on','so_mapping.last_on','so_mapping.created_by_user_id','so_mapping.last_by_user_id','created_user.user_name as created_by_name','last_user.user_name as last_by_name','so_mapping.so_mapping_sequence',
      ])
        ->leftJoin('so_mapping','so_mapping.mapping_id','so_mapping_details.mapping_id')
        ->leftJoin('items','items.id' ,'so_mapping.item_id')
        ->leftJoin('units','units.id','=','items.unit_id') 
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'so_mapping.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'so_mapping.last_by_user_id')       
        ->where('so_mapping.current_location_id', $locationCode)
        ->where('so_mapping.year_id', '=', $year_data->id);
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $mapping_data->whereDate('so_mapping.mapping_date','>=',$from);
            $mapping_data->whereDate('so_mapping.mapping_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $mapping_data->where('so_mapping.mapping_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');
            $mapping_data->where('so_mapping.mapping_date','<=',$to);

        } 

        return DataTables::of($mapping_data)

      
        ->editColumn('created_by_user_id', function($mapping_data){
            if($mapping_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$mapping_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->filterColumn('so_mapping.created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($mapping_data){
            if($mapping_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$mapping_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('so_mapping.last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('created_on', function($mapping_data){
            if ($mapping_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $mapping_data->created_on)->format(DATE_TIME_FORMAT);
                return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('so_mapping.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(so_mapping.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($mapping_data){
            if ($mapping_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $mapping_data->last_on)->format(DATE_TIME_FORMAT);
                 return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('so_mapping.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(so_mapping.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('mapping_date', function($mapping_data){
            if ($mapping_data->mapping_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $mapping_data->mapping_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('so_mapping.mapping_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(so_mapping.mapping_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('map_qty', function($mapping_data){

            return $mapping_data->map_qty > 0 ? number_format((float)$mapping_data->map_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
        ->addColumn('options',function($mapping_data){
            $action = "<div>";
            if(hasAccess("so_mapping","edit")){
            $action .="<a id='edit_a' href='".route('edit-so_mapping',['id' => base64_encode($mapping_data->mapping_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }

            if(hasAccess("customer_replacement_entry","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })       

        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'map_qty','options','mapping_date'])
        ->make(true);
    }


    public  function show($id)
    {
        return view('edit.edit-so_mapping')->with('id', $id);
    }

    public function getLatestMappingNo(Request $request)
    {
          $modal  =  SOMapping::class;
          $sequence = 'so_mapping_sequence';
          $prefix = 'RRM';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_mapping_no'  => $sup_num_format['format'],
            'number'         => $sup_num_format['isFound'],
            'location'       => $locationName
        ]);

    }


    public function store(Request $request){
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;
      
        $existNumber = SOMapping::where('so_mapping_number','=',$request->so_mapping_number)->where('so_mapping_sequence','=',$request->so_mapping_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationID)->lockForUpdate()->first();
        
        if($existNumber){
            $latestNo = $this->getLatestMappingNo($request);
            $tmp =  $latestNo->getContent();
            $area = json_decode($tmp, true);
            $so_mapping_number =   $area['latest_mapping_no'];
            $so_mapping_sequence = $area['number'];              
        }else{
           $so_mapping_number = $request->so_mapping_number;
           $so_mapping_sequence = $request->so_mapping_sequence;
        }
        // end check duplicate number
       
           
       DB::beginTransaction();
       try{
           
           $mapping_data=  SOMapping::create([
               'so_mapping_sequence'     => $so_mapping_sequence,
               'so_mapping_number'       => $so_mapping_number,
               'mapping_date'     => Date::createFromFormat('d/m/Y', $request->so_mapping_date)->format('Y-m-d'),
               'customer_name'     =>    $request->customer ,               
               'item_id'     =>    $request->mapping_item_id ,               
               'item_details_id'     =>    $request->mapping_item_details_id,               
               'special_notes'      => $request->sp_notes, 
               'current_location_id'   => $locationID, 
               'year_id'            =>  $year_data->id,
               'company_id'         => Auth::user()->company_id,
               'created_by_user_id' => Auth::user()->id,
               'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),  

           ]);

           
           if ($mapping_data->save()) {
                
            $request->so_details = json_decode($request->so_details,true);

            if(isset($request->so_details) && !empty($request->so_details)){
                foreach ($request->so_details as $ctKey => $ctVal ) 
                {   
                    if(isset($ctVal['so_detail_id'])){

                        $soQtySum = SalesOrderDetail::where('so_details_id',$ctVal['so_detail_id'])->sum('so_qty');

                        $useSOmapQtySum = SOMappingDetails::where('so_details_id',$ctVal['so_detail_id'])->sum('map_qty');

                        $mapQty = isset($ctVal['map_qty']) && $ctVal['map_qty'] > 0 ? $ctVal['map_qty'] : 0;
                        $somapQtySum = $useSOmapQtySum + $mapQty;                          

                        if( number_format($soQtySum, 3) <  number_format($somapQtySum, 3)){
                            DB::rollBack();
                            return response()->json([
                                'response_code' => '0',
                                'response_message' => 'Map Qty. Is Used',                               
                            ]);
                        }

                    }
                    
                    $mapping_details_data =  SOMappingDetails::create([
                        'mapping_id'     => $mapping_data->mapping_id,
                        'map_qty'    => $ctVal['map_qty'],
                        'so_details_id' => $ctVal['so_detail_id'],
                        'cre_detail_id' => $request->cre_detail_id,
                        'item_details_id' => $request->mapping_item_details_id,
                        'item_detail_qty' => $ctVal['mapping_detail_qty'],
                    ]);  
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
                   'response_message' => 'Record Not Inserted',
               ]);
           }
       }
       catch(\Exception $e){
           DB::rollBack();
           getActivityLogs("Customer Replacement SO Mapping", "add", $e->getMessage(),$e->getLine());  
           return response()->json([
               'response_code' => '0',
               'response_message' => 'Error Occured Record Not Inserted',
               'original_error' => $e->getMessage()
           ]);
       }
    }


    public function edit($id){
        $isAnyPartInUse = false;

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();   
        
        $so_mapping = SOMapping::select('so_mapping.mapping_id','so_mapping.so_mapping_sequence','so_mapping.so_mapping_number','so_mapping.mapping_date','so_mapping.customer_name','so_mapping.item_id','so_mapping.special_notes','units.unit_name','so_mapping.item_details_id',
        DB::raw("CASE WHEN item_details.item_details_id IS NOT NULL THEN units2.unit_name ELSE units1.unit_name END as unit_name"),'item_details.secondary_qty','item_details.secondary_item_name', 
        )
        ->leftJoin('items','items.id','=','so_mapping.item_id')   
        ->leftJoin('item_details','item_details.item_details_id','=','so_mapping.item_details_id')           
        ->leftJoin('units as units1','units1.id','=','items.unit_id')
        ->leftJoin('units as units2','units2.id','=','items.second_unit')        
        ->leftJoin('units','units.id','=','items.unit_id')      
        ->where('so_mapping.mapping_id', $id)
        ->first();
        
        $so_mapping->mapping_date = Date::createFromFormat('Y-m-d', $so_mapping->mapping_date)->format('d/m/Y'); 
        
        $get_customer = SOMapping::select('so_mapping.customer_name')->where('so_mapping.mapping_id',$so_mapping->mapping_id)->get();       
             

        $cre_detail_id = SOMappingDetails::leftJoin('customer_replacement_entry_details','customer_replacement_entry_details.cre_detail_id','=','so_mapping_details.cre_detail_id')
        ->where('so_mapping_details.mapping_id',$so_mapping->mapping_id)
        ->first();   
        $so_mapping->cre_detail_id = $cre_detail_id->cre_detail_id;

        $map_qty = SOMappingDetails::where('so_mapping_details.mapping_id',$so_mapping->mapping_id)
        ->sum('so_mapping_details.map_qty');
        $replacement_data = CustomerReplacementEntry::select([
        DB::raw("(SELECT customer_replacement_entry_details.return_qty -  (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.cre_detail_id = customer_replacement_entry_details.cre_detail_id ) ) as pend_return_qty"),
         ])
        ->leftJoin('customer_replacement_entry_details','customer_replacement_entry_details.cre_id','=','customer_replacement_entry.cre_id')    
        // -> where(DB::raw('BINARY `rep_customer_name`'), $so_mapping->customer_name)
        ->where('customer_replacement_entry_details.cre_detail_id',$cre_detail_id->cre_detail_id)     
        ->where('customer_replacement_entry.current_location_id',$locationCode->id)
        ->whereIn('customer_replacement_entry.year_id',$yearIds)  
        ->first();
        // dd($replacement_data);
        $so_mapping->pend_return_qty = $replacement_data != null ? ($replacement_data->pend_return_qty + $map_qty): $map_qty;


        $so_data = SalesOrder::select('sales_order.so_number','sales_order.so_date','sales_order_details.so_qty','units.unit_name', 'sales_order_details.so_details_id',
        
        DB::raw("(SELECT sales_order_details.so_qty - (SELECT IFNULL(SUM(so_short_close.sc_qty),0) FROM so_short_close WHERE so_short_close.so_details_id = sales_order_details.so_details_id ) - (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.so_details_id = sales_order_details.so_details_id )) as pend_so_map_qty"),
        
        )
       ->leftJoin('sales_order_details','sales_order_details.so_id','=','sales_order.id')
       ->leftJoin('items','items.id','=','sales_order_details.item_id')           
       ->leftJoin('units','units.id','=','items.unit_id')       
       -> where(DB::raw('BINARY `customer_name`'), $so_mapping->customer_name)  
       ->where('sales_order.current_location_id',$locationCode->id)
       ->whereIn('sales_order.year_id',$yearIds)  
       ->where('sales_order.so_type_id_fix','=',2)
       ->where('sales_order_details.item_id','=',$so_mapping->item_id)
       ->get();

       if($so_data != null){
            foreach($so_data as $cpKey => $cpVal){

                if ($cpVal->so_date != null) {
                    $cpVal->so_date = Date::createFromFormat('Y-m-d', $cpVal->so_date)->format('d/m/Y');
                }
            
                $so_mapping_detail = SOMappingDetails::where('so_details_id','=',$cpVal->so_details_id)->where('mapping_id',$id)->first();
                
                $cpVal->so_mapping_detail  = $so_mapping_detail != null ? $so_mapping_detail->so_mapping_details_id  : 0;       

                $cpVal->so_mapping_so_detail  = $so_mapping_detail != null ? $so_mapping_detail->so_details_id  : 0;  

                $cpVal->so_mapp_qty  = $so_mapping_detail != null ? $so_mapping_detail->map_qty  : 0;  

                $cpVal->so_mapp_detail_qty  = $so_mapping_detail != null ? $so_mapping_detail->item_detail_qty  : 0;  
            
                // if($so_mapping_detail){
                //     $cpVal->pend_so_map_qty = $cpVal->pend_so_map_qty  + $so_mapping_detail->map_qty;
                // }else{
                //     $cpVal->pend_so_map_qty = $cpVal->pend_so_map_qty;
                // }                

                $newRequest = new Request();

                $newRequest->so_details_id = $cpVal->so_details_id;
                $newRequest->record_id = $id;
                $newRequest->total_qty = $cpVal->so_qty;
                $cpVal->show_pend_qty = self::getPendingQty($newRequest);
                // $cpVal->pend_so_map_qty = self::getPendingQty($newRequest);   

                if($so_mapping_detail){
                    $isFound = ReplacementItemDecisionDetails::where('so_mapping_details_id','=',$so_mapping_detail->so_mapping_details_id)->sum('decision_qty'); 
                      
                    $isDetailFound = ReplacementItemDecisionDetails::where('so_mapping_details_id','=',$so_mapping_detail->so_mapping_details_id)->sum('decision_detail_qty');   

                    if($isFound != null){
                        $cpVal->in_use = true;
                        $cpVal->used_qty = $isFound;
                        $cpVal->used_detail_qty = $isDetailFound;
                        $isAnyPartInUse = true;
    
                    }else{
                        $cpVal->in_use = false;
                        $cpVal->used_qty = 0;
                        $cpVal->used_detail_qty = 0;
    
                    }
                }else{
                    $cpVal->in_use = false;
                    $cpVal->used_qty = 0;
                    $cpVal->used_detail_qty = 0;
                }              
      
            }

            $so_data = $so_data->reject(function($cpVal) {
                return $cpVal->pend_so_map_qty <= 0 && $cpVal->so_mapping_so_detail == 0;
            });
        }

        if($so_mapping){
            $so_mapping->in_use = false;
            if($isAnyPartInUse == true){
                $so_mapping->in_use = true;
            }
        }
          
      
       if ($so_mapping) {
        return response()->json([
            'so_mapping' => $so_mapping,
            'get_customer'  => $get_customer,
            'so_data' => $so_data,
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
        $locationID  = getCurrentLocation()->id;
        $year_data   = getCurrentYearData();
  
        $validated = $request->validate(
        [
         'so_mapping_sequence' => ['required','max:155',Rule::unique('so_mapping')->where(function ($query) use ($request,$year_data, $locationID) {                  
             return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
         })->ignore($request->id, 'mapping_id')],  
         'so_mapping_number' => ['required', 'max:155', Rule::unique('so_mapping')->where(function ($query) use ($request, $year_data, $locationID) {                 
             return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
         })->ignore($request->id, 'mapping_id')],              
        ],
        [
         'so_mapping_sequence.unique'=>'Sr. No. Already Exists',                   
        ]
        );

       
       DB::beginTransaction();       
       try{
           
               $mapping_data =  SOMapping::where('mapping_id',$request->id)->update([
                'so_mapping_sequence'     => $request->so_mapping_sequence,
                'so_mapping_number'       => $request->so_mapping_number,
                'mapping_date'     => Date::createFromFormat('d/m/Y', $request->so_mapping_date)->format('Y-m-d'),
                'customer_name'     =>    $request->customer ,               
                'item_id'     =>    $request->mapping_item_id ,   
                'item_details_id'     =>    $request->mapping_item_details_id,               
                'special_notes'      => $request->sp_notes, 
                'current_location_id'   => $locationID, 
                'year_id'            => $year_data->id,
                'company_id'         => Auth::user()->company_id,
                'last_by_user_id'    => Auth::user()->id,
                'last_on'            => Carbon::now('Asia/Kolkata')->toDateTimeString(), 
               ]);

               if($mapping_data){
                $request->so_details = json_decode($request->so_details,true);

                  $oldMappingDetails = SOMappingDetails::where('mapping_id','=',$request->id)->get();
                  $oldMappingDetailsData = [];
                  if($oldMappingDetails != null){
                      $oldMappingDetailsData = $oldMappingDetails->toArray();
                  }
                  
                   if (isset($request->so_details) && !empty($request->so_details)) {             
                       foreach ($request->so_details as $sodKey => $sodVal) {                      
                           
                           if($sodVal['so_mapping_detail'] == "0"){
                                $mapping_details_data =  SOMappingDetails::create([
                                    'mapping_id'     => $request->id,
                                    'map_qty'    => $sodVal['map_qty'],
                                    'so_details_id' => $sodVal['so_detail_id'],
                                    'cre_detail_id' => $request->cre_detail_id,
                                    'item_details_id' => $request->mapping_item_details_id,
                                    'item_detail_qty' => $sodVal['mapping_detail_qty'],
                                ]);  
                            } else{        

                                $mapping_details_data =  SOMappingDetails::where('so_mapping_details_id',$sodVal['so_mapping_detail'])->where('so_details_id',$sodVal['so_detail_id'])->update([
                                    'mapping_id'     => $request->id,
                                    'map_qty'    => $sodVal['map_qty'],
                                    'so_details_id' => $sodVal['so_detail_id'],
                                    'cre_detail_id' => $request->cre_detail_id,
                                    'item_details_id' => $request->mapping_item_details_id,
                                    'item_detail_qty' => $sodVal['mapping_detail_qty'],
                                ]);  
                                
                                foreach ($oldMappingDetailsData as $key => $value) {
                                    if ($value['so_details_id'] == $sodVal['so_detail_id']) {
                                        unset($oldMappingDetailsData[$key]);

                                    }
                                }       
                             
                               }
                        }
                        if(isset($oldMappingDetailsData) && !empty($oldMappingDetailsData)){
                            foreach($oldMappingDetailsData as $gkey=>$gval){                               
                                SOMappingDetails::where('so_mapping_details_id', $gval['so_mapping_details_id'])->delete();
                            }
                        }

                   }
                   DB::commit();
                   return response()->json([
                       'response_code' => '1',
                       'response_message' => 'Record Updated Successfully.',
                   ]);
               } else{
                DB::rollBack();
                   return response()->json([
                       'response_code' => '0',
                       'response_message' => 'Record Not Updated',
                   ]);
               }

       }
       catch(\Exception $e){ 
        //    dd($e->getLine().$e->getMessage());                   
           DB::rollBack();
           getActivityLogs("Customer Replacement SO Mapping", "add", $e->getMessage(),$e->getLine());  

           return response()->json([
               'response_code' => '0',
               'response_message' => 'Error Occured Record Not Updated',
               'original_error' => $e->getMessage()
           ]);
       }
   }




   public function destroy(Request $request){
    DB::beginTransaction();
    try{

        $replacement_data = ReplacementItemDecisionDetails::
        leftJoin('so_mapping_details','so_mapping_details.so_mapping_details_id','=','replacement_item_decision_details.so_mapping_details_id')
        ->where('so_mapping_details.mapping_id',$request->id)->get();
        if($replacement_data->isNotEmpty()){ 
            return response()->json([
                'response_code' => '0',
                'response_message' => "You Can't Delete, Customer Replacement SO Mapping Is Used In Replacement Item Decision.",
            ]);
        }

        SOMapping::destroy($request->id);
        SOMappingDetails::where('mapping_id',$request->id)->delete();
        DB::commit();
        return response()->json([
            'response_code' => '1',
            'response_message' => 'Record Deleted Successfully.',
        ]);
    }catch(\Exception $e) {
        DB::rollBack();
        getActivityLogs("Customer Replacement SO Mapping", "delete", $e->getMessage(),$e->getLine());  

        if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
            $error_msg = "This is used somewhere, you can't delete";
        }else{
            $error_msg = "Record Not Deleted";
        }
        return response()->json([
            'response_code' => '0',
            'response_message' => $error_msg,
        ]); 
    }
    
}


    public function getReplacementCustomer(Request $request){  
     
        // $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();      
        
        // if(isset($request->id)){
        //     $get_customer = SOMapping::select('so_mapping.customer_name')->where('so_mapping.mapping_id',$request->id)->get();          

        // }else{
            $get_customer = CustomerReplacementEntryDetails::select('sales_order.customer_name',
            DB::raw("(SELECT customer_replacement_entry_details.return_qty -  (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.cre_detail_id = customer_replacement_entry_details.cre_detail_id ) ) as pend_return_qty"),)
            ->leftJoin('customer_replacement_entry','customer_replacement_entry.cre_id', 'customer_replacement_entry_details.cre_id')
            ->leftJoin('sales_order','sales_order.id', 'customer_replacement_entry.rep_customer_id')
            ->where('customer_replacement_entry.current_location_id',$locationCode->id)
            // ->where('customer_replacement_entry.year_id',$year_data->id)               
            ->whereIn('customer_replacement_entry.year_id',$yearIds)               
            ->having('pend_return_qty','>',0)
            ->get();

            $get_customer = $get_customer->unique(['customer_name']);
            $get_customer = $get_customer->values()->all();
        // }

       

        return response()->json([
            'response_code' => 1,
            'get_customer'  => $get_customer,
        ]);
    }


    public function getReplacementListForMapping(Request $request)
    {
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        // if(isset($request->id)){            

        //     $edit_mapping_data = SOMappingDetails::select(['customer_replacement_entry.cre_number','customer_replacement_entry.cre_date','items.item_name' ,'units.unit_name',
        //     'items.item_code', 'item_groups.item_group_name','customer_replacement_entry_details.cre_detail_id',  
        //     'customer_replacement_entry_details.return_qty', 'so_mapping_details.map_qty as pend_return_qty', 
        //     ])
        //     ->leftJoin('customer_replacement_entry_details','customer_replacement_entry_details.cre_detail_id','=','so_mapping_details.cre_detail_id')
        //     ->leftJoin('customer_replacement_entry','customer_replacement_entry.cre_id','=','customer_replacement_entry_details.cre_id')
        //     ->leftJoin('items','items.id','=','customer_replacement_entry_details.item_id')           
        //     ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        //     ->leftJoin('units','units.id','=','items.unit_id')
        //     ->where('so_mapping_details.mapping_id',$request->id)
        //     ->get();           

        // }

        $replacement_data = CustomerReplacementEntry::select(['customer_replacement_entry.cre_number','customer_replacement_entry.cre_date','items.item_name' ,
        'items.item_code', 'item_groups.item_group_name','customer_replacement_entry_details.cre_detail_id',  
        'customer_replacement_entry_details.return_qty', 'customer_replacement_entry_details.item_details_id','item_details.secondary_item_name',    
        DB::raw("(SELECT customer_replacement_entry_details.return_qty -  (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.cre_detail_id = customer_replacement_entry_details.cre_detail_id ) ) as pend_return_qty"),DB::raw("CASE WHEN item_details.item_details_id IS NOT NULL THEN units2.unit_name ELSE units1.unit_name END as unit_name")   
         ])
        ->leftJoin('customer_replacement_entry_details','customer_replacement_entry_details.cre_id','=','customer_replacement_entry.cre_id')
        ->leftJoin('items','items.id','=','customer_replacement_entry_details.item_id')           
        ->leftJoin('item_details','item_details.item_details_id','=','customer_replacement_entry_details.item_details_id')           
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        // ->leftJoin('units','units.id','=','items.unit_id')        
        ->leftJoin('units as units1','units1.id','=','items.unit_id')
        ->leftJoin('units as units2','units2.id','=','items.second_unit')
        // ->where('customer_replacement_entry.customer_name', $request->customer)
        -> where(DB::raw('BINARY `rep_customer_name`'), $request->customer)     
        ->where('customer_replacement_entry.current_location_id',$locationCode->id)
        ->whereIn('customer_replacement_entry.year_id',$yearIds)  
        ->having('pend_return_qty','>',0)      
        ->get();


        // if(isset($edit_mapping_data)){
        //     $data = collect($replacement_data)->merge($edit_mapping_data);
        //     $grouped = $data->groupBy('cre_detail_id');    
            

        //     $merged = $grouped->map(function ($items) {
        //         return $items->reduce(function ($carry, $item) {
        //             if (!$carry) {
        //                 return $item;
        //             }
        //             $carry->pend_return_qty += (float) $item->pend_return_qty;
        //             return $carry;
        //         });
        //     });
    
        //     $replacement_data = $merged->values();   

        // }



        if ($replacement_data != null) {
            foreach ($replacement_data as $cpKey => $cpVal) {
                if ($cpVal->cre_date != null) {
                    $cpVal->cre_date = Date::createFromFormat('Y-m-d', $cpVal->cre_date)->format('d/m/Y');
                }
               
               
            }
        }
        // $po_data = $po_data->sortBy('po_details_id')->values();


    

        if ($replacement_data != null) {
            return response()->json([
                'response_code' => 1,
                'replacement_data'  => $replacement_data,                   
            ]);
        }else{
            return response()->json([
                'response_code' => 1,
                'replacement_data'  => [],                         
            ]);

        }

    }

    public function getPendingCustomerReplacement(Request $request){

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();

        $replacement_data = CustomerReplacementEntryDetails::select('customer_replacement_entry_details.cre_detail_id','customer_replacement_entry_details.item_id','customer_replacement_entry_details.item_details_id','customer_replacement_entry_details.return_qty','item_details.secondary_item_name',  
        DB::raw("(SELECT customer_replacement_entry_details.return_qty -  (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.cre_detail_id = customer_replacement_entry_details.cre_detail_id ) ) as pend_return_qty"),
        DB::raw("CASE WHEN item_details.item_details_id IS NOT NULL THEN units2.unit_name ELSE units1.unit_name END as unit_name"),'item_details.secondary_qty'  
        
        )
        ->leftJoin('customer_replacement_entry','customer_replacement_entry.cre_id','=','customer_replacement_entry_details.cre_id')
        ->leftJoin('items','items.id','=','customer_replacement_entry_details.item_id')           
        ->leftJoin('item_details','item_details.item_details_id','=','customer_replacement_entry_details.item_details_id')           
        // ->leftJoin('units','units.id','=','items.unit_id')  
        ->leftJoin('units as units1','units1.id','=','items.unit_id')
        ->leftJoin('units as units2','units2.id','=','items.second_unit')      
        ->where('customer_replacement_entry_details.cre_detail_id',$request->cre_details_ids)
        ->first();

        $so_data = SalesOrder::select('sales_order.so_number','sales_order.so_date','sales_order_details.so_qty','units.unit_name', 'sales_order_details.so_details_id',
        // DB::raw("(SELECT sales_order_details.so_qty -  (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.so_details_id = sales_order_details.so_details_id )  -  (SELECT IFNULL(SUM(so_short_close.sc_qty),0) FROM so_short_close WHERE so_short_close.so_details_id = sales_order_details.so_details_id )) as pend_so_map_qty"),
        DB::raw("(SELECT sales_order_details.so_qty -  (SELECT IFNULL(SUM(so_mapping_details.map_qty),0) FROM so_mapping_details WHERE so_mapping_details.so_details_id = sales_order_details.so_details_id )  -  (SELECT IFNULL(SUM(so_short_close.sc_qty),0) FROM so_short_close WHERE so_short_close.so_details_id = sales_order_details.so_details_id ) -  (SELECT IFNULL(SUM(transaction_so_short_close.tr_sc_qty),0) FROM transaction_so_short_close WHERE transaction_so_short_close.so_details_id = sales_order_details.so_details_id )) as pend_so_map_qty"),
       )
       ->leftJoin('sales_order_details','sales_order_details.so_id','=','sales_order.id')
       ->leftJoin('items','items.id','=','sales_order_details.item_id')           
       ->leftJoin('units','units.id','=','items.unit_id')       
    //    ->where('sales_order.customer_name',$request->customer)
       -> where(DB::raw('BINARY `customer_name`'), $request->customer)  
       ->where('sales_order.current_location_id',$locationCode->id)
       ->whereIn('sales_order.year_id',$yearIds)  
       ->where('sales_order.so_type_id_fix','=',2)
       ->where('sales_order_details.item_id','=',$replacement_data->item_id)
       ->having('pend_so_map_qty','>',0)
       ->get();

       if ($so_data != null) {
           foreach ($so_data as $cpKey => $cpVal) {
               if ($cpVal->so_date != null) {
                   $cpVal->so_date = Date::createFromFormat('Y-m-d', $cpVal->so_date)->format('d/m/Y');
               }
           }
       }

       $changedItemIds = [];
        if ($replacement_data != null) {
            $changedItemId = Item::where('id', $replacement_data->item_id)
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

       if($changedItemIds){
        $item = Item::select('id','item_name')->whereIN('id',$changedItemIds)->get();
       }else{
            $item = '';
       }

        if ($replacement_data  || $so_data) {
            return response()->json([
                'response_code' => 1,
                'replacement_data'  => $replacement_data, 
                'so_data'  => $so_data,   
                'item' => $item,           
            ]);
        }else{
            return response()->json([
                'response_code' => 1,
                'replacement_data'  => [],   
                'so_data'  => [],            
            ]);

        }
       
    }


    public function getPendingQty(Request $request){
        $exectQty = $request->total_qty;

        $oldRecords = SOMappingDetails::select(DB::raw('SUM(map_qty) as sum'))
        ->where('so_details_id','=',$request->so_details_id)
        ->where('mapping_id','<=',$request->record_id)
        ->groupBy(['so_details_id'])
        ->first();

        $oldScRecords = SOShortClose::select(DB::raw('SUM(sc_qty) as sc_sum'))
        ->where('so_details_id','=',$request->so_details_id)
        ->groupBy(['so_details_id'])
        ->first();
        
        $sc_sum = $oldScRecords ? $oldScRecords->sc_sum : 0;

        if($oldRecords != null){
            $diff = $exectQty - $oldRecords->sum - $sc_sum;
            return $diff;            
        }else{
            $diff = $exectQty - $sc_sum;
            return abs($diff);
        } 
        
    }



}