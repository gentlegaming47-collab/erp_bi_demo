<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemProduction;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use App\Models\ItemProductionDetail;

class ItemProductionController extends Controller
{
    public function manage()
    {
        return view('manage.manage-item_production');
    }

    public function index(ItemProduction $ItemProduction,Request $request,DataTables $dataTables)
    {
       $year_data = getCurrentYearData();
       $location = getCurrentLocation();

       $itemProduction = ItemProduction::select(['ip_number', 'ip_sequence', 'ip_date', 'items.item_name', 'items.item_code', 'item_production_details.production_qty', 'special_notes', 'remarks',

      'item_production.created_by_user_id','item_production.last_by_user_id','item_production.created_on','item_production.last_on','item_production.ip_id', 'item_groups.item_group_name', 'units.unit_name','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])

       ->leftJoin('item_production_details','item_production_details.ip_id','=','item_production.ip_id')
       ->leftJoin('items','items.id','=','item_production_details.item_id')
       ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
       ->leftJoin('units','units.id','=','items.unit_id')
       ->leftJoin('admin AS created_user', 'created_user.id', '=', 'item_production.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'item_production.last_by_user_id')
       ->where('item_production.year_id','=',$year_data->id)
       ->where('item_production.current_location_id','=',$location->id);

        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $itemProduction->whereDate('item_production.ip_date','>=',$from);

                $itemProduction->whereDate('item_production.ip_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $itemProduction->where('item_production.ip_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $itemProduction->where('item_production.ip_date','<=',$to);

        }  


      return DataTables::of($itemProduction)


       ->editColumn('ip_date', function($itemProduction){

           if ($itemProduction->ip_date != null) {

               $formatedDate3 = Date::createFromFormat('Y-m-d', $itemProduction->ip_date)->format(DATE_FORMAT); return $formatedDate3;

           }else{

               return '';

           }

       })

        ->filterColumn('item_production.ip_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_production.ip_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

    

       ->editColumn('production_qty', function($itemProduction){
            
        return $itemProduction->production_qty > 0 || $itemProduction->production_qty != ""  ? number_format((float)$itemProduction->production_qty, 3, '.','') : '';

     
       })


       ->editColumn('created_by_user_id', function($itemProduction){
           if($itemProduction->created_by_user_id != null){
               $created_by_user_id = Admin::where('id','=',$itemProduction->created_by_user_id)->first('user_name');
               return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
           }else{
               return '';
           }
       })
       ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('last_by_user_id', function($itemProduction){
           if($itemProduction->last_by_user_id != null){
               $last_by_user_id = Admin::where('id','=',$itemProduction->last_by_user_id)->first('user_name');
               return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
           }else{
               return '';
           }

       })
       ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('created_on', function($itemProduction){
           if ($itemProduction->created_on != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $itemProduction->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
           }else{
               return '';
           }
       })
       ->filterColumn('item_production.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_production.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('last_on', function($itemProduction){
           if ($itemProduction->last_on != null) {
               $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $itemProduction->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
           }else{
               return '';
           }
       })
       ->filterColumn('item_production.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_production.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('item_name', function($materialRequest){
            if($materialRequest->item_name != ''){
                $item_name = ucfirst($materialRequest->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

       ->addColumn('options',function($itemProduction){
           $action = "<div>";        
           if(hasAccess("item_issue","edit")){
           $action .="<a id='edit_a' href='".route('edit-item_production',['id' => base64_encode($itemProduction->ip_id )]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
           }
           if(hasAccess("item_issue","delete")){
           $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
           }
           $action .= "</div>";
           return $action;
       })
       ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options'])
       ->make(true);
    }

    public function create()
    {
        return view('add.add-item_production');
    }

    public function store(Request $request)
    {
       
        $locationID = getCurrentLocation()->id;
        $year_data = getCurrentYearData();

        DB::beginTransaction();
        
        $existNumber = ItemProduction::where([
            ['ip_sequence',  $request->ip_sequence],
            ['ip_number', $request->ip_number],
            ['year_id', $year_data->id],
            ['current_location_id', $locationID],
        ])->lockForUpdate()->first();

 
      
         if($existNumber){
             $latestNo = $this->getLatestItemProductionNo($request);              
             $tmp =  $latestNo->getContent();
             $area = json_decode($tmp, true);
             $ip_number =   $area['latest_po_no'];
             $ip_sequence = $area['number'];
         }else{
            $ip_number = $request->ip_number;
            $ip_sequence = $request->ip_sequence;
         }
      
         try{
 
             
             $totalQty = 0;
             $totalAmount = 0;
           
             
             foreach ($request->item_id as $ctKey => $ctVal) {
                 if ($ctVal != null) {
                     $totalQty += $request->production_qty[$ctKey];                     
                 }
             }
 
            
            
             $item_production =  ItemProduction::create([                 
                 'current_location_id'=>$locationID,
                 'ip_sequence'        => $ip_sequence,
                 'ip_number'          => $ip_number,
                 'ip_date'            => Date::createFromFormat('d/m/Y', $request->ip_date)->format('Y-m-d'),                 
                 'total_qty'          => $totalQty,                
                 'special_notes'      => $request->special_notes,
                 'year_id'            => $year_data->id,
                 'company_id'         => Auth::user()->company_id,
                 'created_by_user_id' => Auth::user()->id,
                 'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString()          ]);
              
             if($item_production->save())
             {
                 
                 
                 foreach($request->item_id as $spKey => $spVal)
                 {
      
                     $item_production_details = ItemProductionDetail::create([
                         'ip_id'    => $item_production->ip_id,
                         'item_id'   => isset($request->item_id[$spKey]) ? $request->item_id[$spKey] : "",
                         'production_qty'   => isset($request->production_qty[$spKey]) ? $request->production_qty[$spKey] : "",                         
                         'remarks'   => isset($request->remarks[$spKey]) ? $request->remarks[$spKey] : "",
                     ]);

                    //  increaseStockQty($locationID,$spVal,$request->production_qty[$spKey]);
                    // increaseStockQty($locationID,$spVal,$request->production_qty[$spKey],0,'add');

                    stockEffect($locationID,$spVal,$request->pre_item[$spKey],$request->production_qty[$spKey],0,'add','U','Item Production',$item_production_details->ip_details_id);
                 }
                 DB::commit();
                 return response()->json([
                     'response_code' => 1,
                     'response_message' => 'Record Inserted Successfully.',
                 ]);
             }
             else
             {
                DB::rollBack();
                 return response()->json([
                     'response_code' => '1',
                     'response_message' => 'Record Not Inserted.',
                 ]);
             }
 
         }
         catch(\Exception $e)
         {          
            
            //  DB::rollBack();
            //  return response()->json([
            //      'response_code' => '0',
            //      'response_message' => 'Error Occured Record Not Inserted',
            //      'original_error' => $e->getMessage()
            //  ]);

            
            DB::rollBack(); 
            getActivityLogs("Item Production", "add", $e->getMessage(),$e->getLine());  
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
        return view('edit.edit-item_production', compact('id'));
    }

    public function edit($id)
    {
        $location = getCurrentLocation();
        
        $itemProduction = ItemProduction::select('item_production.ip_id','item_production.ip_sequence','item_production.ip_number','item_production.ip_date','item_production.special_notes')->where('ip_id', $id)->first();
        // $itemProduction = ItemProduction::where('ip_id', $id)->first();

        $itemProduction->ip_date = Date::createFromFormat('Y-m-d', $itemProduction->ip_date)->format('d/m/Y');

        $itemProductionDetails = ItemProductionDetail::select(['item_production_details.*', 'items.item_code', 'units.unit_name', 'item_groups.item_group_name',
        'location_stock.stock_qty','items.secondary_unit',
        // DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE item_production_details.item_id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),          
        ])
        ->leftJoin('items', 'items.id', 'item_production_details.item_id')      
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')      
        ->leftJoin('location_stock', 'location_stock.item_id', 'item_production_details.item_id') 
        ->leftJoin('units', 'units.id', 'items.unit_id')       
        ->where('location_stock.location_id','=',$location->id)
        ->where('ip_id','=',$id)->get();


        if ($itemProductionDetails) {
            return response()->json([
                'itemProductionDetails' => $itemProductionDetails,
                'itemProduction' => $itemProduction,
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

        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

        $validated = $request->validate(
            [
                'ip_sequence' => ['required','max:155',Rule::unique('item_production')->where(function ($query) use ($request,$year_data, $locationID) {
                    return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'ip_id')],

                'ip_number' => ['required', 'max:155', Rule::unique('item_production')->where(function ($query) use ($request, $year_data, $locationID) {
                    return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'ip_id')],              
            ],
            [
                'ip_sequence.unique'=>'IP No. Is Already Exists',    
                'ip_number.required' => 'Please Enter IP. Number',
                'ip_number.max' => 'Maximum 155 Characters Allowed',
            ]
        );

        DB::beginTransaction();
        try{

            $totalQty = 0;
            $totalAmount = 0;

            if(isset($request->item_id))
            {
                foreach ($request->item_id as $ctKey => $ctVal) {
                    if ($ctVal != null) {
                        $totalQty += $request->production_qty[$ctKey];                      
                    }
                }
            }

            
            $ItemProduction =  ItemProduction::where('ip_id','=',$request->id)->update([         
                'current_location_id'   =>$locationID,
                'ip_sequence'        => $request->ip_sequence,
                'ip_number'          => $request->ip_number,
                'ip_date'            => Date::createFromFormat('d/m/Y', $request->ip_date)->format('Y-m-d'),                
                'total_qty'             => $totalQty,                
                'special_notes'         => $request->special_notes,
                'year_id'               => $year_data->id,
                'company_id'            => Auth::user()->company_id,
                'last_by_user_id'       => Auth::user()->id,
                'last_on'               => Carbon::now('Asia/Kolkata')->toDateTimeString()       
            ]);


            if($ItemProduction)
            {

                // this cose use to stock maintain
                $oldProductionDetails = ItemProductionDetail::where('ip_id','=',$request->id)->get();
                $oldProductionDetailsData = [];
                if($oldProductionDetails != null){
                    $oldProductionDetailsData = $oldProductionDetails->toArray();
                }


                if (isset($request->ip_details_id) && !empty($request->ip_details_id)) {
                    
                    foreach ($request->ip_details_id  as $sodKey => $sodVal) { 
                        
                        if($sodVal == "0"){                                    
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){

                                
                                $itemProductionDetails=   ItemProductionDetail::create([
                                    'ip_id'    => $request->id,
                                    'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                    'production_qty'   => isset($request->production_qty[$sodKey]) ? $request->production_qty[$sodKey] : "",
                                    'remarks'   => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : "",                             
                                ]);

                                // increaseStockQty($locationID,$request->item_id[$sodKey],$request->production_qty[$sodKey]);
                                // increaseStockQty($locationID,$request->item_id[$sodKey],$request->production_qty[$sodKey],0,'add');

                                // stockEffect($locationID,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->production_qty[$sodKey],0,'add','U');
                                stockEffect($locationID,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->production_qty[$sodKey],0,'add','U','Item Production',$itemProductionDetails->ip_details_id);

                            }
                            }else{     
                                
                                if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){                                    
                                    $itemIssue =  ItemProductionDetail::where('ip_details_id',$sodVal)->update([
                                        'ip_id'    => $request->id,
                                        'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                        'production_qty'   => isset($request->production_qty[$sodKey]) ? $request->production_qty[$sodKey] : "",
                                        'remarks'   => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : "",        
                                    ]);

                                    // increaseStockQty($locationID,$request->item_id[$sodKey],($request->production_qty[$sodKey] - $request->old_production_qty[$sodKey]));
                                    // increaseStockQty($locationID,$request->item_id[$sodKey],$request->production_qty[$sodKey],$request->old_production_qty[$sodKey],'edit');

                                    stockEffect($locationID,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->production_qty[$sodKey] , $request->old_production_qty[$sodKey],'edit','U','Item Production',$sodVal);

                                    foreach ($oldProductionDetailsData as $key => $value) {
                                        if ($value['item_id'] == $request->item_id[$sodKey]) {
                                            unset($oldProductionDetailsData[$key]);

                                        }
                                    }      

                                }else{                                    
                                    ItemProductionDetail::where('ip_details_id', $sodVal)->delete();

                                    if(isset($oldProductionDetailsData) && !empty($oldProductionDetailsData)){
                                        foreach($oldProductionDetailsData as $gkey=>$gval){
                                            // $qty = -$gval['production_qty'];
                                            // increaseStockQty($locationID,$gval['item_id'],$qty);

                                            // increaseStockQty($locationID,$gval['item_id'],0,$gval['production_qty'],'delete');

                                            stockEffect($locationID,$gval['item_id'],$gval['item_id'],0, $gval['production_qty'],'delete','U','Item Production',$gval['ip_details_id']);
                                            unset($oldProductionDetailsData[$gkey]);
                                        }
                                    }
                                }
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
                    'response_message' => 'Record Not Inserted.',
                ]);
            }
        }
        catch(\Exception $e)
        {
            // dd($e->getMessage() . $e->getLine());
            // DB::rollBack();
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => 'Error Occured Record Not Inserted',
            //     'original_error' => $e->getMessage()
            // ]);

            
            DB::rollBack(); 
            getActivityLogs("Item Production", "update", $e->getMessage(),$e->getLine());  
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
             
             $locationID = getCurrentLocation()->id;
            $date = ItemProduction::where('ip_id',$request->id)->value('ip_date');

            // $secondary_item = ItemProductionDetail::
            // leftJoin('items','items.id','=','item_production_details.item_id')
            // ->where('items.secondary_unit','=','Yes')
            // ->where('item_production_details.ip_id',$request->id)->get();

            //    if($secondary_item->isNotEmpty()){ 
            //     return response()->json([
            //         'response_code' => '0',
            //         'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
            //     ]);
            // }

             // this cose use to stock maintain
             $oldProductionDetails = ItemProductionDetail::where('ip_id','=',$request->id)->get();
             $oldProductionDetailsData = [];
             if($oldProductionDetails != null){
                 $oldProductionDetailsData = $oldProductionDetails->toArray();
             }

             foreach($oldProductionDetailsData as $gkey=>$gval){

                $SecUnitBeforeUpdate =  LiveUpdateSecDate($date,$gval['item_id']);
                if($SecUnitBeforeUpdate === true){                
                    DB::rollBack();     
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
                    ]);
                }
                // $qty = -$gval['production_qty'];
                // increaseStockQty($locationID,$gval['item_id'],$qty);

                // increaseStockQty($locationID,$gval['item_id'],0,$gval['production_qty'],'delete');

                stockEffect($locationID,$gval['item_id'],$gval['item_id'],0,$gval['production_qty'],'delete','U','Item Production',$gval['ip_details_id']);

            }


           
            ItemProductionDetail::where('ip_id',$request->id)->delete();
            ItemProduction::destroy($request->id);

            DB::commit();
 
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){

            DB::rollBack(); 
            getActivityLogs("Item Production", "delete", $e->getMessage(),$e->getLine());  
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

    public function getLatestItemProductionNo(Request $request)
    {
          $modal  =  ItemProduction::class;
          $sequence = 'ip_sequence';
          $prefix = 'PROD';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $sup_num_format['format'],
            'number'        => $sup_num_format['isFound'],
            'location'      => $locationName
        ]);
    }

}