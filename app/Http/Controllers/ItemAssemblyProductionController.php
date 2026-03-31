<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemAssemblyProduction;
use App\Models\Item;
use App\Models\ItemRawMaterialMappingDetail;
use App\Models\ItemAssemblyProductionDetails;
use App\Models\LocationStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportItemProductionAssemblyConsumption;

class ItemAssemblyProductionController extends Controller
{
    public function manage()
    {
        return view('manage.manage-item_assm_production');
    }

    public function index(ItemAssemblyProduction $item_assm_production,Request $request,DataTables $dataTables)
    {
       $year_data = getCurrentYearData();
       $location = getCurrentLocation();

       $itemAssmProduction = ItemAssemblyProduction::select(['iap_number', 'iap_sequence', 'iap_date', 'items.item_name', 'items.item_code',
    //    'units.unit_name', 
     DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),
       'assembly_qty', 'special_notes','item_assembly_production.item_details_id','item_details.secondary_item_name','item_assembly_production.created_by_user_id','item_assembly_production.last_by_user_id','item_assembly_production.created_on','item_assembly_production.last_on','item_assembly_production.iap_id', 'item_groups.item_group_name','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])

    //    ->leftJoin('item_assembly_production_details','item_assembly_production_details.iap_id','=','item_assembly_production.iap_id')

       ->leftJoin('items','items.id','=','item_assembly_production.item_id')
       ->leftJoin('item_details','item_details.item_details_id','=','item_assembly_production.item_details_id')
       ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
       ->leftJoin('units','units.id','=','items.unit_id')
       ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')    
       ->leftJoin('admin AS created_user', 'created_user.id', '=', 'item_assembly_production.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'item_assembly_production.last_by_user_id')
       
       ->where('item_assembly_production.year_id','=',$year_data->id)
       
       ->where('item_assembly_production.current_location_id','=',$location->id);
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $itemAssmProduction->whereDate('item_assembly_production.iap_date','>=',$from);

                $itemAssmProduction->whereDate('item_assembly_production.iap_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $itemAssmProduction->where('item_assembly_production.iap_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $itemAssmProduction->where('item_assembly_production.iap_date','<=',$to);

        }  
      return DataTables::of($itemAssmProduction)

        ->filterColumn('unit_name', function($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('units.unit_name', 'like', "%{$keyword}%")
                ->orWhere('second_unit.unit_name', 'like', "%{$keyword}%");
            });
        })


       ->editColumn('iap_date', function($itemAssmProduction){

           if ($itemAssmProduction->iap_date != null) {

               $formatedDate3 = Date::createFromFormat('Y-m-d', $itemAssmProduction->iap_date)->format(DATE_FORMAT); return $formatedDate3;

           }else{

               return '';

           }

       })
        ->filterColumn('item_assembly_production.iap_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_assembly_production.iap_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

    

       ->editColumn('assembly_qty', function($itemAssmProduction){

        return $itemAssmProduction->assembly_qty > 0 ? number_format((float)$itemAssmProduction->assembly_qty, 3, '.','') : number_format((float) 0, 3, '.','');

       })
      


       ->editColumn('created_by_user_id', function($itemAssmProduction){
           if($itemAssmProduction->created_by_user_id != null){
               $created_by_user_id = Admin::where('id','=',$itemAssmProduction->created_by_user_id)->first('user_name');
               return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
           }else{
               return '';
           }
       })
       ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('last_by_user_id', function($itemAssmProduction){
           if($itemAssmProduction->last_by_user_id != null){
               $last_by_user_id = Admin::where('id','=',$itemAssmProduction->last_by_user_id)->first('user_name');
               return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
           }else{
               return '';
           }

       })
       ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('created_on', function($itemAssmProduction){
           if ($itemAssmProduction->created_on != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $itemAssmProduction->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
           }else{
               return '';
           }
       })
       ->filterColumn('item_assembly_production.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_assembly_production.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('last_on', function($itemAssmProduction){
           if ($itemAssmProduction->last_on != null) {
               $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $itemAssmProduction->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
           }else{
               return '';
           }
       })
       ->filterColumn('item_assembly_production.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_assembly_production.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('item_name', function($itemAssmProduction){ 
            if($itemAssmProduction->item_details_id != null){
                if($itemAssmProduction->secondary_item_name != ''){
                    $item_name = ucfirst($itemAssmProduction->secondary_item_name);
                    return $item_name;
                }else{
                    return '';
                }

            }else{
                if($itemAssmProduction->item_name != ''){
                    $item_name = ucfirst($itemAssmProduction->item_name);
                    return $item_name;
                }else{
                    return '';
                }
            }

            
        })
        // ->filterColumn('item_name', function($query, $keyword) {
        //     $query->where(function($q) use ($keyword) {
        //         $q->where('items.item_name', 'like', "%{$keyword}%")
        //         ->orWhere('item_details.secondary_item_name', 'like', "%{$keyword}%");
        //     });
        // })
        ->filterColumn('item_name', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->orWhere('item_details.secondary_item_name', 'like', "%{$keyword}%");
                $q->orWhere('items.item_name', 'like', "%{$keyword}%");
            });
        })

       ->addColumn('options',function($itemAssmProduction){
           $action = "<div>";        
           if(hasAccess("item_assm_production","edit")){
           $action .="<a id='edit_a' href='".route('edit-item_assm_production',['id' => base64_encode($itemAssmProduction->iap_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
           }
           if(hasAccess("item_assm_production","delete")){
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
        return view('add.add_item_assm_production');
    }

    public function store(Request $request)
    {
        $locationID = getCurrentLocation()->id;
        $year_data = getCurrentYearData();

        DB::beginTransaction();
        
        //  $existNumber = ItemAssemblyProduction::where('iap_sequence', $request->iap_sequence)->where('iap_number', $request->iap_number)->first();    

        $existNumber = ItemAssemblyProduction::where([
            ['iap_sequence',  $request->iap_sequence],
            ['iap_number', $request->iap_number],
            ['year_id', $year_data->id],
            ['current_location_id', $locationID],
        ])->first();
      
         if($existNumber){
             $latestNo = $this->getLatestItemAPNo($request);              
             $tmp =  $latestNo->getContent();
             $area = json_decode($tmp, true);
             $iap_number =   $area['latest_po_no'];
             $iap_sequence = $area['number'];
         }else{
            $iap_number = $request->iap_number;
            $iap_sequence = $request->iap_sequence;
         }
      
         try{
                            
             $item_assm_production =  ItemAssemblyProduction::create([                 
                 'current_location_id' => $locationID,
                 'iap_sequence'        => $iap_sequence,
                 'iap_number'          => $iap_number,
                 'iap_date'            => Date::createFromFormat('d/m/Y', $request->iap_date)->format('Y-m-d'),                 
                 'item_id'            => $request->iap_item_id,                
                 'item_details_id'    => $request->item_details_id,                
                 'assembly_qty'       => $request->assembly_qty,                
                 'special_notes'      => $request->special_notes,
                 'year_id'            => $year_data->id,
                 'company_id'         => Auth::user()->company_id,
                 'created_by_user_id' => Auth::user()->id,
                 'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString()         
             ]);

                // increaseStockQty($locationID,$request->iap_item_id,$request->assembly_qty,0,'add');

               

                if(!empty($request->item_details_id)){
                    stockDetailsEffect($locationID,$request->pre_item_details_id,$request->item_details_id,$request->assembly_qty,0,'add','U','Item Assembly Production Item Detail Record',$item_assm_production->iap_id,'Yes','Item Assembly Production',$item_assm_production->iap_id);
                }else{
                    stockEffect($locationID,$request->pre_iap_item,$request->iap_item_id,$request->assembly_qty,0,'add','U','Item Assembly Production',$item_assm_production->iap_id);
                }
              
              
             if($item_assm_production->save())
             {
                 
                 
                 foreach($request->item_id as $spKey => $spVal)
                 {
      
                     $item_assm_production_details = ItemAssemblyProductionDetails::create([
                         'iap_id'    => $item_assm_production->iap_id,
                         'item_id'   => isset($request->item_id[$spKey]) ? $request->item_id[$spKey] : "",
                         'raw_material_qty'   => isset($request->mapped_qty[$spKey]) ? $request->mapped_qty[$spKey] : "",                         
                         'consumption_qty'   => isset($request->consumotion_qty[$spKey]) ? $request->consumotion_qty[$spKey] : "",
                     ]);
                    //  increaseStockQty($locationID,$request->item_id[$spKey],($request->org_mapped_qty[$spKey] - $request->mapped_qty[$spKey]));
                    
                    // decreaseStockQty($locationID,$request->item_id[$spKey],$request->consumotion_qty[$spKey],0,'add');

                    stockEffect($locationID,$request->item_id[$spKey],$request->pre_item[$spKey],$request->consumotion_qty[$spKey],0,'add','D','Item Assembly Production Detail',$item_assm_production_details->iap_details_id);

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
            //  dd($e->getLine(). $e->getMessage())  ;
            //  DB::rollBack();

            //  if($e->getMessage() == 'Insufficient Stock'){
            //     return response()->json([
            //         'response_code' => '0',
            //         'response_message' => 'Insufficient Stock',
            //     ]);
            // }else{
            //     return response()->json([
            //         'response_code' => '0',
            //         'response_message' => 'Error Occured Record Not Inserted',
            //         'original_error' => $e->getMessage()
            //     ]);
            // }
            
            //  return response()->json([
            //      'response_code' => '0',
            //      'response_message' => 'Error Occured Record Not Inserted',
            //      'original_error' => $e->getMessage(),
            //  ]);

            DB::rollBack(); 
            getActivityLogs("Item Production (Assembly)", "add", $e->getMessage(),$e->getLine());  
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
        return view('edit.edit-item_assm_production', compact('id'));
    }

    public function edit($id)
    {
        $location = getCurrentLocation();
         $isAnyPartInUse = false;
        $itemAssProduction = ItemAssemblyProduction::select('item_assembly_production.iap_id','item_assembly_production.iap_sequence','item_assembly_production.iap_number', 'item_assembly_production.iap_date','item_assembly_production.assembly_qty','item_assembly_production.special_notes','item_assembly_production.item_id','item_assembly_production.item_details_id','items.item_code', 'units.unit_name',
         DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),        
        )->leftJoin('items', 'items.id', 'item_assembly_production.item_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')    
        ->where('iap_id', $id)->first();
        // $itemAssProduction = ItemAssemblyProduction::where('iap_id', $id)->first();
        $itemAssProduction->iap_date = Date::createFromFormat('Y-m-d', $itemAssProduction->iap_date)->format('d/m/Y');
 
        $itemAssmProductionDetails = ItemAssemblyProductionDetails::select(['item_assembly_production_details.*', 'items.item_code', 'items.item_name', 'items.id as itemID', 'units.unit_name','item_groups.item_group_name',
        'location_stock.stock_qty', 
        // DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE item_assembly_production_details.item_id = location_stock.item_id AND location_stock.location_id = $location->id ) as stock_qty"),  
        
        ])
        ->leftJoin('items', 'items.id', 'item_assembly_production_details.item_id')     
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')     
        ->leftJoin('units', 'units.id', 'items.unit_id')   
        ->leftJoin('location_stock', 'location_stock.item_id',  'item_assembly_production_details.item_id')
        ->where('location_stock.location_id','=',$location->id)
        ->where('item_assembly_production_details.iap_id','=',$id)        
        ->get();
        if($itemAssProduction != null){
                    if($itemAssProduction->iap_date != null){
                    $date = Date::createFromFormat('d/m/Y', $itemAssProduction->iap_date )->format('Y-m-d');
                }
                $OldSecondryItem = LiveUpdateSecDate($date,$itemAssProduction->item_id);
            
                if($OldSecondryItem == true)
                {
                    $itemAssProduction->in_use = true;
                    $isAnyPartInUse = true;
                }
                else{
                    $itemAssProduction->in_use = false;
                }

    

        }
        
 
        if ($itemAssmProductionDetails) {
         return response()->json([
             'itemAssmProductionDetails' => $itemAssmProductionDetails,
             'itemAssProduction' => $itemAssProduction,
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
                'iap_sequence' => ['required','max:155',Rule::unique('item_assembly_production')->where(function ($query) use ($request,$year_data, $locationID) {
                    return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'iap_id')],

                'iap_number' => ['required', 'max:155', Rule::unique('item_assembly_production')->where(function ($query) use ($request, $year_data, $locationID) {
                    return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'iap_id')],              
            ],
            [
                'iap_sequence.unique'=>'IAP Number Is Already Exists',    
                'iap_number.required' => 'Please Enter IAP Number',
                'iap_number.max' => 'Maximum 155 Characters Allowed',
            ]
        );

        DB::beginTransaction();
        try{

            $itemAssmPro =  ItemAssemblyProduction::where('iap_id','=',$request->id)->update([                             
                'current_location_id'   =>$locationID,
                'iap_sequence'        => $request->iap_sequence,
                'iap_number'          => $request->iap_number,
                'iap_date'            => Date::createFromFormat('d/m/Y', $request->iap_date)->format('Y-m-d'),                 
                'item_id'            => $request->iap_item_id,     
                'item_details_id'    => $request->item_details_id,                   
                'assembly_qty'       => $request->assembly_qty,                
                'special_notes'      => $request->special_notes,
                'year_id'            => $year_data->id,
                'company_id'         => Auth::user()->company_id,
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id]);
                // 'created_by_user_id' => Auth::user()->id,
                // 'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString()]);  


            //  increaseStockQty($locationID,$request->iap_item_id,$request->assembly_qty,$request->org_assembly_qty,'edit');

            

            if(!empty($request->item_details_id)){
                stockDetailsEffect($locationID,$request->pre_item_details_id,$request->item_details_id,$request->assembly_qty, $request->org_assembly_qty,'edit','U','Item Assembly Production Item Detail Record',$request->id,'Yes','Item Assembly Production',$request->id);
            }else{
                stockEffect($locationID,$request->iap_item_id,$request->pre_iap_item,$request->assembly_qty, $request->org_assembly_qty,'edit','U','Item Assembly Production',$request->id);
            }

            


            if($itemAssmPro)
            {
                // this cose use to stock maintain
                $oldAssProductionDetails = ItemAssemblyProductionDetails::where('iap_id','=',$request->id)->get();
                $oldAssProductionDetailsData = [];
                if($oldAssProductionDetails != null){
                     $oldAssProductionDetailsData = $oldAssProductionDetails->toArray();
                }


                if (isset($request->iap_details_id) && !empty($request->iap_details_id)) {
                    
                    foreach ($request->iap_details_id as $sodKey => $sodVal) { 
                                               
                      
                        if($sodVal == "0"){                                    
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){

                                
                                $itemAssProDetails=   ItemAssemblyProductionDetails::create([
                                    'iap_id'    => $request->id,
                                    'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                    'raw_material_qty'   => isset($request->mapped_qty[$sodKey]) ? $request->mapped_qty[$sodKey] : "",                         
                                    'consumption_qty'   => isset($request->consumotion_qty[$sodKey]) ? $request->consumotion_qty[$sodKey] : "",            
                                ]);
                                // increaseStockQty($locationID,$request->item_id[$sodKey],($request->org_mapped_qty[$sodKey] - $request->mapped_qty[$sodKey]));
                                // decreaseStockQty($locationID,$request->item_id[$sodKey],$request->consumotion_qty[$sodKey],0,'add');

                                stockEffect($locationID,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->consumotion_qty[$sodKey],0,'add','D','Item Assembly Production Detail',$itemAssProDetails->iap_details_id);
                            }
                            }else{     
                                
                                if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){                                    
                                    $itemIssue =  ItemAssemblyProductionDetails::where('iap_details_id',$sodVal)->update([
                                        'iap_id'    => $request->id,
                                        'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                        'raw_material_qty'   => isset($request->mapped_qty[$sodKey]) ? $request->mapped_qty[$sodKey] : "",                         
                                        'consumption_qty'   => isset($request->consumotion_qty[$sodKey]) ? $request->consumotion_qty[$sodKey] : "",                                      
                                    ]);

                                    // increaseStockQty($locationID,$request->item_id[$sodKey],($request->mapped_qty[$sodKey] - $request->mapped_qty[$sodKey]));


                                    // if($request->item_id[$sodKey] == $request->prev_item_id[$sodKey]){
                                    //     decreaseStockQty($locationID,$request->item_id[$sodKey],$request->consumotion_qty[$sodKey],$request->org_consumption_qty[$sodKey],'edit');
                                    // }else{
                                    //     decreaseStockQty($locationID,$request->prev_item_id[$sodKey],0,$request->org_consumption_qty[$sodKey],'delete');

                                    //     decreaseStockQty($locationID,$request->item_id[$sodKey],$request->consumotion_qty[$sodKey],0,'edit');
                                    // }

                                    stockEffect($locationID,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->consumotion_qty[$sodKey] ,$request->org_consumption_qty[$sodKey],'edit','D','Item Assembly Production Detail',$sodVal);

                                    foreach ($oldAssProductionDetailsData as $key => $value) {
                                        if ($value['item_id'] == $request->item_id[$sodKey]) {
                                            unset($oldAssProductionDetailsData[$key]);

                                        }
                                    }         
                                }else{                                    
                                    ItemAssemblyProductionDetails::where('iap_details_id', $sodVal)->delete();

                                    if(isset($oldAssProductionDetailsData) && !empty($oldAssProductionDetailsData)){
                                        foreach($oldAssProductionDetailsData as $gkey=>$gval){
                                            $qty = $gval['consumotion_qty'];
                                            // increaseStockQty($locationID,$gval['item_id'],$qty);

                                            // decreaseStockQty($locationID,$gval['item_id'],0,$qty,'delete');

                                            stockEffect($locationID,$gval['item_id'],$gval['item_id'],0, $qty,'delete','D','Item Assembly Production Detail',$gval['iap_details_id']);

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
            getActivityLogs("Item Production (Assembly)", "update", $e->getMessage(),$e->getLine());  
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
            $date = ItemAssemblyProduction::where('iap_id',$request->id)->value('iap_date');
            $locationID = getCurrentLocation()->id;

            $assQty = ItemAssemblyProduction::where('iap_id',$request->id)->first();
            $SecUnitBeforeUpdate =  LiveUpdateSecDate($date,$assQty->item_id);
            if($SecUnitBeforeUpdate === true){                
                DB::rollBack();     
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
                ]);
            }

            if($assQty != null){
                // increaseStockQty($assQty->current_location_id,$assQty->item_id,-($assQty->assembly_qty));
                // increaseStockQty($assQty->current_location_id,$assQty->item_id,0,$assQty->assembly_qty,'delete');

               

                if($assQty->item_details_id != null){
                    stockDetailsEffect($assQty->current_location_id,$assQty->item_details_id,$assQty->item_details_id,0,$assQty->assembly_qty,'delete','U','Item Assembly Production Item Detail Record',$request->id,'Yes','Item Assembly Production',$request->id);
                }else{
                    stockEffect($assQty->current_location_id,$assQty->item_id,$assQty->item_id,0,$assQty->assembly_qty,'delete','U','Item Assembly Production',$request->id);
                }

            }

            // this cose use to stock maintain
            $oldAssProductionDetails = ItemAssemblyProductionDetails::where('iap_id','=',$request->id)->get();
            $oldAssProductionDetailsData = [];
            if($oldAssProductionDetails != null){
                  $oldAssProductionDetailsData = $oldAssProductionDetails->toArray();
            }

            foreach($oldAssProductionDetailsData as $gkey=>$gval){
                $qty = $gval['consumption_qty'];
                // increaseStockQty($locationID,$gval['item_id'],$qty);
                // decreaseStockQty($locationID,$gval['item_id'],0,$qty,'delete');

                stockEffect($locationID,$gval['item_id'],$gval['item_id'],0, $qty,'delete','D','Item Assembly Production Detail',$gval['iap_details_id']);

            }

          
            ItemAssemblyProductionDetails::where('iap_id',$request->id)->delete();
            ItemAssemblyProduction::destroy($request->id);

            
            DB::commit();
 
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){

            DB::rollBack(); 
            getActivityLogs("Item Production (Assembly)", "delete", $e->getMessage(),$e->getLine());  
            // if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
            //     $error_msg = "This is used somewhere, you can't delete";
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
             }
            else if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }
           
        }
    }

    public function getLatestItemAPNo(Request $request)
    {
          $modal  =  ItemAssemblyProduction::class;
          $sequence = 'iap_sequence';
          $prefix = 'ASS';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $sup_num_format['format'],
            'number'        => $sup_num_format['isFound'],
            'location'      => $locationName
        ]);
    }

    public function fetchItemCode(Request $request)
    {

        
        $locationID = getCurrentLocation()->id;
// dd($request->all());
        // $getItemCode = Item::select('items.item_code','units.unit_name','items.second_unit')
        // ->leftjoin('units', 'units.id','=','items.unit_id)
        // ->where('items.id', $request->item_id)
        // ->first();
        $getItemCode = Item::select(
            'items.item_code',
            DB::raw("CASE 
                        WHEN items.second_unit IS NOT NULL 
                            THEN (SELECT unit_name FROM units WHERE units.id = items.second_unit) 
                        ELSE (SELECT unit_name FROM units WHERE units.id = items.unit_id) 
                    END as unit_name"),
            'items.second_unit'
        )
        ->where('items.id', $request->item_id)
        ->first();

        if(!empty($request->item_details_id)){
            $getItemMapping = ItemRawMaterialMappingDetail::select('items.item_name', 'items.id as ItemID','units.unit_name', 'items.item_code', 'item_groups.item_group_name as itemGroup','item_raw_material_mapping_details.*')
            ->join('items', 'items.id', 'item_raw_material_mapping_details.raw_material_id')
            ->join('item_groups', 'item_groups.id', 'items.item_group_id')
            ->join('units', 'units.id', 'items.unit_id')   
            ->where('items.status', 'active')
            ->where('items.service_item','No')     
            ->where('item_details_id', $request->item_details_id)
            ->get();

        }else{
            $getItemMapping = ItemRawMaterialMappingDetail::select('items.item_name', 'items.id as ItemID','units.unit_name', 'items.item_code', 'item_groups.item_group_name as itemGroup','item_raw_material_mapping_details.*')
            ->join('items', 'items.id', 'item_raw_material_mapping_details.raw_material_id')
            ->join('item_groups', 'item_groups.id', 'items.item_group_id')
            ->join('units', 'units.id', 'items.unit_id')   
            ->where('items.status', 'active')
            ->where('items.service_item','No')     
            ->where('item_id', $request->item_id)
            ->get();
        }

      

        
        $stock_qty = [];
        foreach ($getItemMapping as $gmap) {
            
            $locationStock = LocationStock::select('location_stock.stock_qty')
                ->where('item_id', $gmap->ItemID)
                ->where('location_id', $locationID)
                ->first();

            if ($locationStock) {              
                $gmap->stock_qty = $locationStock->stock_qty;
            } else {                
                $gmap->stock_qty = 0;
            }
            $stock_qty[] = $gmap;
        }

        if ($getItemCode != "" || $getItemMapping != "") {
            return response()->json([
                'response_code' => 1,
                'item_code' => $getItemCode,
                'item_mapping' => $stock_qty, // Merge item_mapping with stock_qty
                'response_message' => 'Item Code Found Successfully',
            ]);
        }else{
                return response()->json([
                    'response_code' => 0,
                    'response_message' => 'Item Code Not Found successfully',
                ]);
            }


        // $getItemMapping = ItemRawMaterialMappingDetail::select('items.item_name', 'items.id','units.unit_name', 'items.item_code', 'item_raw_material_mapping_details.*')
        // ->join('items', 'items.id', 'item_raw_material_mapping_details.raw_material_id')
        // ->join('units', 'units.id', 'items.unit_id')        
        // ->where('item_id', $request->item_id)
        // ->get();

        // $stock_qty = [];
        // foreach ($getItemMapping as $gmap) {
        //     $locationStock = LocationStock::select('location_stock.stock_qty')
        //                                    ->where('item_id', $gmap->id)
        //                                    ->where('location_id', $locationID)
        //                                    ->first();
        //     if ($locationStock) {
        //         $stock_qty[] = $locationStock;
        //     }         
        // }
        
        
     
        // if($getItemCode != "" || $getItemMapping != "")
        // {
        //     return response()->json([
        //         'response_code' => 1,
        //         'item_code' => $getItemCode,
        //         'item_mapping' => $getItemMapping,
        //         'stock_qty' => $stock_qty,
        //         'response_message' => 'Item Code Found Successfully',
        //     ]);
        // }else{
        //     return response()->json([
        //         'response_code' => 0,
        //         'response_message' => 'Item Code Not Found successfully',
        //     ]);
        // }
    }

    public function getFittingItems(Request $request)
    {
        $locationID = getCurrentLocation()->id;
        
        $item = Item::select(['items.*','item_groups.item_group_name','units.unit_name'])
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('items.id',$request->item)->first();
      
        
        $getItemMapping = ItemRawMaterialMappingDetail::select('items.item_name', 'item_raw_material_mapping_details.*',)
        ->leftJoin('items', 'items.id', 'item_raw_material_mapping_details.raw_material_id')
        ->where('raw_material_id', $request->item)
        ->first();


            $stock_qty = LocationStock::select('location_stock.stock_qty')->where('item_id',$request->item)->where('location_id',$locationID)->first();

        

        return response()->json([
            'response_code' => 1,
            'item' => $item,   
            'getItemMapping' => $getItemMapping,
            'stock_qty' => $stock_qty,    
        ]);

    }

    public function exportItemProductionAssemblyConsumption(Request $request)
    {
        $searchData = [];
        $global = $request->input('global');
        $columns = $request->input('columns', []);

        $fromDate = $request->input('trans_from_date');
        $toDate = $request->input('trans_to_date');

        if($fromDate && is_string($fromDate))
        {
            $searchData['trans_from_date'] = trim($fromDate);
        }

        if($toDate && is_string($toDate))
        {
            $searchData['trans_to_date'] = trim($toDate);
        }

        if($global && is_string($global))
        {
            $searchData['global'] = trim($global);
        }

        if(is_array($columns))
        {
            foreach($columns as $idx => $val)
            {
                if(is_string($val) && strlen($val) <= 255)
                {
                    $searchData['columns'][$idx] = trim($val);
                }
            }
        }
        
        return Excel::download(new ExportItemProductionAssemblyConsumption($searchData), 'Item Production Assembly Consumption.xlsx');
    }
}