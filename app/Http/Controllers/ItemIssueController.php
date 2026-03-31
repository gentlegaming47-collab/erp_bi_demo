<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ItemDetails;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;
use App\Models\LocationDetailStock;
use App\Models\ItemIssue;
use App\Models\ItemIssueDetail;
use App\Models\ItemReturnDetail;
use App\Models\Location;
use Illuminate\Support\Collection;

class ItemIssueController extends Controller
{
   public function manage()
   {
    return view('manage.manage-item_issue');
   }


   // pending to monday

   public function index(ItemIssue $ItemIssue,Request $request,DataTables $dataTables)
   {
       $year_data = getCurrentYearData();
       $location = getCurrentLocation();

       $itemIssue = ItemIssue::select(['issue_number', 'issue_sequence', 'issue_date', 'items.item_name', 'items.item_code', 'item_issue_details.issue_qty', 'item_issue_details.item_type',  'item_issue_details.remarks', 'special_notes','item_issue.created_by_user_id','item_issue.last_by_user_id','item_issue.created_on','item_issue.last_on','item_issue.item_issue_id', 'item_issue.issue_type_value_fix','item_groups.item_group_name','units.unit_name','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
       ->leftJoin('item_issue_details','item_issue_details.item_issue_id','=','item_issue.item_issue_id')

       
       ->leftJoin('items','items.id','=','item_issue_details.item_id')
       ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
       ->leftJoin('units','units.id','=','items.unit_id')
       ->leftJoin('admin AS created_user', 'created_user.id', '=', 'item_issue.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'item_issue.last_by_user_id')
       ->where('item_issue.year_id','=',$year_data->id)
       ->where('item_issue.current_location_id','=',$location->id);

        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $itemIssue->whereDate('item_issue.issue_date','>=',$from);

                $itemIssue->whereDate('item_issue.issue_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $itemIssue->where('item_issue.issue_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $itemIssue->where('item_issue.issue_date','<=',$to);

        }  


      return DataTables::of($itemIssue)

       ->editColumn('issue_date', function($itemIssue){

           if ($itemIssue->issue_date != null) {

               $formatedDate3 = Date::createFromFormat('Y-m-d', $itemIssue->issue_date)->format(DATE_FORMAT); return $formatedDate3;

           }else{

               return '';

           }

       })

        ->filterColumn('item_issue.issue_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_issue.issue_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
       
    //    ->editColumn('item_type', function($itemIssue){ 
    //     if($itemIssue->item_type != ""){
    //         return ucfirst($itemIssue->item_type);
    //     }

    //     })

      ->editColumn('item_type', function($itemIssue){ 
        $map = [
            'consumable' => 'Consumable',
            'waste/scrap_entry' => 'Waste/Scrap entry',
        ];

        if($itemIssue->item_type != "" && isset($map[$itemIssue->item_type])){
            return $map[$itemIssue->item_type];
        }

        return '';
    })

     ->filterColumn('item_type', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('item_issue_details.item_type', 'like', "%{$keyword}%")
                ->orWhereRaw("CASE 
                    WHEN item_issue_details.item_type = 'consumable' THEN 'Consumable'
                    WHEN item_issue_details.item_type = 'waste/scrap_entry' THEN 'Waste/Scrap entry'
                    ELSE item_issue_details.item_type
                    END LIKE ?", ["%{$keyword}%"]);
            });
        })
    
    

       ->editColumn('issue_qty', function($itemIssue){

           return $itemIssue->issue_qty > 0 ? $itemIssue->issue_qty : 0;

       })


       ->editColumn('created_by_user_id', function($itemIssue){
           if($itemIssue->created_by_user_id != null){
               $created_by_user_id = Admin::where('id','=',$itemIssue->created_by_user_id)->first('user_name');
               return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
           }else{
               return '';
           }
       })
       ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('last_by_user_id', function($itemIssue){
           if($itemIssue->last_by_user_id != null){
               $last_by_user_id = Admin::where('id','=',$itemIssue->last_by_user_id)->first('user_name');
               return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
           }else{
               return '';
           }

       })
       ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('created_on', function($itemIssue){
           if ($itemIssue->created_on != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $itemIssue->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
           }else{
               return '';
           }
       })
       ->filterColumn('item_issue.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_issue.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('issue_qty', function($itemIssue){
        if($itemIssue->issue_qty != null){
            $issue_qty = number_format((float)$itemIssue->issue_qty, 3, '.','');
            
            return isset($issue_qty)?$issue_qty :number_format((float) 0, 3, '.','');
        }else{
            return '';
        }
    })
       ->editColumn('last_on', function($itemIssue){
           if ($itemIssue->last_on != null) {
               $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $itemIssue->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
           }else{
               return '';
           }
       })
       ->filterColumn('item_issue.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_issue.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('item_name', function($itemIssue){ 
        if($itemIssue->item_name != ''){
            $item_name = ucfirst($itemIssue->item_name);
            return $item_name;
        }else{
            return '';
        }
    })

       ->addColumn('options',function($itemIssue){
           $action = "<div>";        
           if(hasAccess("item_issue","edit")){
           $action .="<a id='edit_a' href='".route('edit-item_issue',['id' => base64_encode($itemIssue->item_issue_id )]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
           }
           if(hasAccess("item_issue","delete")){
           $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
           }
           $action .= "</div>";
           return $action;
       })
       ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options','item_type'])
       ->make(true);
   }


   public function create()
   {
       return view('add.add-item_issue');
   }


   public function store(Request $request)
   {
       
       DB::beginTransaction();
        
       $locationID = getCurrentLocation()->id;
        $year_data = getCurrentYearData();
       
       
        $existNumber = ItemIssue::where([
            ['issue_sequence',  $request->issue_sequence],
            ['issue_number',$request->issue_number],
            ['year_id',$year_data->id],
            ['current_location_id',$locationID],
        ])->lockForUpdate()->first();
        

        if($existNumber){
            $latestNo = $this->getLatestItemIssueNo($request);              
            $tmp =  $latestNo->getContent();
            $area = json_decode($tmp, true);
            $item_number =   $area['latest_po_no'];    
            $issue_sequence = $area['number'];
        }else{
           $item_number = $request->issue_number;
           $issue_sequence = $request->issue_sequence;
        }

        
       // dd($item_number, $issue_sequence);
        try{

         
            $totalQty = 0;
            $totalAmount = 0;
         
            
            foreach ($request->item_id as $ctKey => $ctVal) {
                if ($ctVal != null) {
                    $totalQty += $request->issue_qty[$ctKey];                     
                }
            }

            $itemtype = $request->issue_type_id_fix == 1 ? "Inhouse" : "Outside";
           
            $item_issue =  ItemIssue::create([
                'issue_type_id_fix'=>   isset($request->issue_type_id_fix) != "" ? $request->issue_type_id_fix : "",
                'issue_type_value_fix'=>$itemtype,
                'current_location_id'=>$locationID,
                'issue_sequence'        => $issue_sequence,
                'issue_number'          => $item_number,
                'issue_date'            => Date::createFromFormat('d/m/Y', $request->issue_date)->format('Y-m-d'),
                'supplier_id'        => $request->supplier_id,                                   
                'total_qty'          => $totalQty,                
                'special_notes'      => $request->special_notes,
                'year_id'            => $year_data->id,
                'company_id'         => Auth::user()->company_id,
                'created_by_user_id' => Auth::user()->id,
                'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString()          ]);
             
            if($item_issue->save())
            {
                
                
                foreach($request->item_id as $spKey => $spVal)
                {

                    $itemIssuetype = $request->issue_type_id_fix == 2 ? "returnable" :  $request->issue_type[$spKey];

                    
                    $item_issue = ItemIssueDetail::create([
                        'item_issue_id'    => $item_issue->item_issue_id,
                        'item_id'   => isset($request->item_id[$spKey]) ? $request->item_id[$spKey] : "",
                        'item_details_id' => isset($request->item_details_id[$spKey]) ? $request->item_details_id[$spKey] : null,
                        'issue_qty'   => isset($request->issue_qty[$spKey]) ? $request->issue_qty[$spKey] : "",
                        'item_type'   => isset($itemIssuetype) ? $itemIssuetype : "",
                        'remarks'   => isset($request->remarks[$spKey]) ? $request->remarks[$spKey] : "",
                        'status' => 'Y',
                    ]);


                    // increaseStockQty($locationID,$spVal,-$request->issue_qty[$spKey]);
                    // decreaseStockQty($locationID,$spVal,$request->issue_qty[$spKey],0,'add');

                    // stockEffect($locationID,$spVal,$request->pre_item[$spKey],$request->issue_qty[$spKey],0,'add','D','Item Issue',$item_issue->item_issue_details_id);

                    if($request->item_details_id[$spKey] == null || $request->item_details_id[$spKey] == "")
                        {
                            stockEffect($locationID,$request->item_id[$spKey],$request->item_id[$spKey],$request->issue_qty[$spKey],0,'add','D','Item Issue',$item_issue->item_issue_details_id);
                        }else{
                             stockDetailsEffect($locationID,$request->item_details_id[$spKey],$request->item_details_id[$spKey],$request->issue_qty[$spKey],0,'add','D','Item Issue Details',$item_issue->item_issue_details_id,'Yes','Item Issue Details',$item_issue->item_issue_details_id);
                                
                        }
                    
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
            // DB::rollBack();
            // if($e->getMessage() == 'Insufficient Stock'){
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

            
            DB::rollBack(); 
            getActivityLogs("Item Issue", "add", $e->getMessage(),$e->getLine());  
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
       return view('edit.edit-item_issue', compact('id'));
   }

   public function edit($id)
   { 
    $isAnyPartInUse = false;
       $location = getCurrentLocation();

       $itemIssue = ItemIssue::select('item_issue.item_issue_id','item_issue.issue_sequence','item_issue.issue_number','item_issue.issue_date','item_issue.special_notes')->where('item_issue.item_issue_id', $id)->first();
    //    $itemIssue = ItemIssue::where('item_issue_id', $id)->first();
       $itemIssue->issue_date = Date::createFromFormat('Y-m-d', $itemIssue->issue_date)->format('d/m/Y');

       $itemIssueDetails = ItemIssueDetail::select(['item_issue_details.*', 'items.item_code', 'item_groups.item_group_name', 'units.unit_name','item_details.item_details_id',
      'location_stock.stock_qty','item_details.secondary_item_name','item_details.item_details_id',
      
    //   DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE item_issue_details.item_id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),    
       ])
       ->leftJoin('items', 'items.id', 'item_issue_details.item_id')
       ->leftJoin('item_details','item_details.item_details_id','=','item_issue_details.item_details_id')  
       ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')  
       ->leftJoin('units', 'units.id', 'items.unit_id')  
      ->leftJoin('location_stock', 'location_stock.item_id', 'item_issue_details.item_id')  
      ->where('location_stock.location_id','=',$location->id)
       ->where('item_issue_id','=',$id)->get();


       if($itemIssueDetails != null){
            $itemIssueDetails->each(function ($item) use (&$isAnyPartInUse,&$itemIssue,&$location) {
                $total_issue_qty = ItemReturnDetail::where('item_issue_details_id', '=', $item->item_issue_details_id)->sum('return_qty');
                if($itemIssue->issue_date != null){
                    $date = Date::createFromFormat('d/m/Y', $itemIssue->issue_date)->format('Y-m-d');
                }
                if($total_issue_qty != null && $total_issue_qty > 0 || LiveUpdateSecDate($date,$item->item_id)){
                    $item->in_use = true;
                    $item->used_qty = $total_issue_qty;
                    $isAnyPartInUse = true;
                } else {
                    $item->in_use = false;
                    $item->used_qty = 0;
                }
                 $item->item_detail = ItemDetails::select('item_details.item_details_id','item_details.secondary_item_name','units.unit_name',
                //  'location_stock_details.secondary_stock_qty'
                DB::raw("(SELECT IFNULL(SUM(location_stock_details.secondary_stock_qty),0) FROM location_stock_details WHERE item_details.item_details_id = location_stock_details.item_details_id AND location_stock_details.location_id = $location->id) as secondary_stock_qty"),    
                 )
                // ->leftJoin('location_stock_details','location_stock_details.item_details_id','=','item_details.item_details_id')
                ->leftJoin('items','items.id','=','item_details.item_id')
                ->leftJoin('units','units.id','=','items.second_unit')
                // ->where('location_stock_details.location_id','=',$location->id)
                ->groupBy('item_details.item_details_id')
                ->where('item_id',$item->item_id)->get();

                foreach($item->item_detail as $ikey=>$ival){
                    // dd($ival->secondary_stock_qty);
                    if($ival->item_details_id == $item->item_details_id){
                        $ival->secondary_stock_qty = $ival->secondary_stock_qty + $item->issue_qty;
                    }else{
                        $ival->secondary_stock_qty = $ival->secondary_stock_qty;
                    }
                }
                
            });
           

       }

       if($itemIssue){
        $itemIssue->in_use = false;
        if($isAnyPartInUse == true){
            $itemIssue->in_use = true;
        }
    }

       if ($itemIssueDetails) {
        return response()->json([
            'itemIssueDetails' => $itemIssueDetails,
            'itemIssue' => $itemIssue,
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
                'issue_sequence' => ['required','max:155',Rule::unique('item_issue')->where(function ($query) use ($request,$year_data, $locationID) {
                    return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'item_issue_id')],

                'issue_number' => ['required', 'max:155', Rule::unique('item_issue')->where(function ($query) use ($request, $year_data, $locationID) {
                    return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'item_issue_id')],              
            ],
            [
                'issue_sequence.unique'=>'Issue Number Is Already Exists',    
                'issue_number.required' => 'Please Enter Issue Number',
                'issue_number.max' => 'Maximum 155 Characters Allowed',
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
                        $totalQty += $request->issue_qty[$ctKey];                      
                    }
                }
            }

            $itemtype = $request->issue_type_id_fix == 1 ? "Inhouse" : "Outside";
            $itemIssue =  ItemIssue::where('item_issue_id','=',$request->id)->update([             
                'issue_type_id_fix'     =>$request->issue_type_id_fix,
                'issue_type_value_fix'  =>$itemtype,
                'current_location_id'   =>$locationID,
                'issue_sequence'        => $request->issue_sequence,
                'issue_number'          => $request->issue_number,
                'issue_date'            => Date::createFromFormat('d/m/Y', $request->issue_date)->format('Y-m-d'),
                'supplier_id'           => $request->supplier_id,                                
                'total_qty'             => $totalQty,                
                'special_notes'         => $request->special_notes,
                'year_id'               => $year_data->id,
                'company_id'            => Auth::user()->company_id,
                'last_by_user_id'       => Auth::user()->id,
                'last_on'               => Carbon::now('Asia/Kolkata')->toDateTimeString()               
            ]);


            if($itemIssue)
            {

                // this is use for stock maintain
                // $oldIssueDetails = ItemIssueDetail::where('item_issue_id','=',$request->id)->get();
                // $oldIssueDetailsData = [];
                // if($oldIssueDetails != null){
                //     $oldIssueDetailsData = $oldIssueDetails->toArray();
                // }

                
                if (isset($request->item_issue_details_id) && !empty($request->item_issue_details_id)) {
                     $materialDtails =  ItemIssueDetail::where('item_issue_id',$request->id)->update([
                        'status' => 'D',
                    ]);
                    foreach ($request->item_issue_details_id as $sodKey => $sodVal) { 
                                               
                        //  $itemIssuetype = $request->issue_type_id_fix == 2 ? "returnable" :  $request->issue_type[$sodKey];
                        
                        if($sodVal == "0"){                                    
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){

                                $itemIssuetype = $request->issue_type_id_fix == 2 ? "returnable" :  $request->issue_type[$sodKey];

                                
                                $itemIssueDetails=   ItemIssueDetail::create([
                                    'item_issue_id'    => $request->id,
                                    'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                    'item_details_id'     => isset($request->item_details_id[$sodKey]) ? $request->item_details_id[$sodKey]   : null,
                                    'issue_qty'   => isset($request->issue_qty[$sodKey]) ? $request->issue_qty[$sodKey] : "",
                                    'remarks'   => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : "",
                                    'item_type'   => $itemIssuetype,
                                    'status' => 'Y', 
                                ]);

                                // increaseStockQty($locationID,$request->item_id[$sodKey],-$request->issue_qty[$sodKey]);

                                // decreaseStockQty($locationID,$request->item_id[$sodKey],$request->issue_qty[$sodKey],0,'add');
                                // stockEffect($locationID,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->issue_qty[$sodKey],0,'add','D');

                                // stockEffect($locationID,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->issue_qty[$sodKey],0,'add','D','Item Issue',$itemIssueDetails->item_issue_details_id);
                                if($request->item_details_id[$sodKey] == null || $request->item_details_id[$sodKey] == "")
                                {
                                    stockEffect($locationID,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->issue_qty[$sodKey],0,'add','D','Item Issue',$itemIssueDetails->item_issue_details_id);
                                }else{
                                    stockDetailsEffect($locationID,$request->item_details_id[$sodKey],$request->item_details_id[$sodKey],$request->issue_qty[$sodKey],0,'add','D','Item Issue Details',$itemIssueDetails->item_issue_details_id,'Yes','Item Issue Details',$itemIssueDetails->item_issue_details_id );
                                        
                                }

                            }
                            }else{     
                                
                                if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){        
                                  $itemIssuetype = $request->issue_type_id_fix == 2 ? "returnable" :  $request->issue_type[$sodKey];

                                    $itemIssue =  ItemIssueDetail::where('item_issue_details_id',$sodVal)->update([
                                        'item_issue_id'    => $request->id,
                                        'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                        'item_details_id'     => isset($request->item_details_id[$sodKey]) ? $request->item_details_id[$sodKey]   : null,
                                        'issue_qty'   => isset($request->issue_qty[$sodKey]) ? $request->issue_qty[$sodKey] : "",
                                        'remarks'   => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : "",
                                        'item_type'   => $itemIssuetype,
                                        'status' => 'Y',  
                                    ]);

                                    // increaseStockQty($locationID,$request->item_id[$sodKey],($request->stock_qty[$sodKey] - $request->issue_qty[$sodKey] - $request->org_stock_qty[$sodKey]));

                                    // decreaseStockQty($locationID,$request->item_id[$sodKey],$request->issue_qty[$sodKey],$request->org_issue_qty[$sodKey],'edit');

                                    // stockEffect($locationID,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->issue_qty[$sodKey],$request->org_issue_qty[$sodKey],'edit','D','Item Issue',$sodVal);

                                    if($request->pre_item_detail[$sodKey] == "" && $request->pre_item_detail[$sodKey] == null && $request->item_details_id[$sodKey] == "" && $request->item_details_id[$sodKey] == null)
                                    {    
                                        // dd("main if",$request);                                     
                                      stockEffect($locationID,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->issue_qty[$sodKey] ,$request->org_issue_qty[$sodKey],'edit','D','Item Issue',$sodVal);
                                    }
                                    else{
                                        // details mathi normal kare tyare
                                        if($request->pre_item_detail[$sodKey]!="" && $request->pre_item_detail[$sodKey]!= null && $request->item_details_id[$sodKey]=="" && $request->item_details_id[$sodKey] == null)
                                        {
                                            //  stockDetailsEffect($locationCode->id,$request->item_details_id[$sodKey],$request->pre_item_detail[$sodKey],$request->return_qty[$sodKey],$request->org_return_qty[$sodKey],'edit','U','Item Return Details',$sodVal,'Yes','Item Return Details',$sodVal );
                                            //  dd("nest if");         
                                              stockDetailsEffect($locationID,$request->pre_item_detail[$sodKey],$request->pre_item_detail[$sodKey],0,$request->org_issue_qty[$sodKey],'delete','D','Item Issue Details',$sodVal,'Yes','Item Issue Details',$sodVal);

                                                stockEffect($locationID,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->issue_qty[$sodKey],0,'add','D','Item Issue',$sodVal);
                                        }
                                        // normal mathi details vadi kare tyare
                                        else if($request->pre_item_detail[$sodKey]=="" && $request->pre_item_detail[$sodKey]== null && $request->item_details_id[$sodKey]!="" && $request->item_details_id[$sodKey] != null){

                                             stockEffect($locationID,$request->pre_item[$sodKey],$request->pre_item[$sodKey],0, $request->org_issue_qty[$sodKey],'delete','D','Item Issue',$sodVal);

                                             stockDetailsEffect($locationID,$request->item_details_id[$sodKey],$request->item_details_id[$sodKey],$request->issue_qty[$sodKey],0,'add','D','Item Issue Details',$sodVal,'Yes','Item Issue Details',$sodVal );
                                        }
                                        else {
                                           stockDetailsEffect($locationID,$request->item_details_id[$sodKey],$request->pre_item_detail[$sodKey],$request->issue_qty[$sodKey],$request->org_issue_qty[$sodKey],'edit','D','Item Issue Details',$sodVal,'Yes','Item Issue Details',$sodVal );
                                        }
                                    }

                                    // foreach ($oldIssueDetailsData as $key => $value) {
                                    //     if ($value['item_id'] == $request->item_id[$sodKey]) {
                                    //         unset($oldIssueDetailsData[$key]);

                                    //     }
                                    // }   
                                }
                                // }else{                                    
                                //     ItemIssueDetail::where('item_issue_details_id', $sodVal)->delete();

                                //     if(isset($oldIssueDetailsData) && !empty($oldIssueDetailsData)){
                                //         foreach($oldIssueDetailsData as $gkey=>$gval){
                                //             $qty = $gval['issue_qty'];
                                //             // increaseStockQty($locationID,$gval['item_id'],$qty);

                                //             // decreaseStockQty($locationID,$gval['item_id'],0,$qty,'delete');

                                //             stockEffect($locationID,$gval['item_id'],$gval['item_id'],0,$qty,'delete','D','Item Issue',$gval['item_issue_details_id']);

                                //         }
                                //     }
                                // }
                                
                            }
                    }
                    
                    $deleteissueDetails = ItemIssueDetail::where('item_issue_id',$request->id)->where('status','D')->get();

                       if(!empty($deleteissueDetails)){
                            foreach($deleteissueDetails as $dkey => $dval){

                                if($dval['item_details_id']!= "" &&$dval['item_details_id'] != null)
                                {
                                     stockDetailsEffect($locationID,$dval['item_details_id'],$dval['item_details_id'],0,$dval['issue_qty'],'delete','D','Item Issue Details',$dval['item_issue_details_id'],'Yes','Item Issue Details',$dval['item_issue_details_id']);
                                }
                                else{
                                    stockEffect($locationID,$dval['item_id'],$dval['item_id'],0, $dval['issue_qty'],'delete','D','Item Issue',$dval['item_issue_details_id']);
                                }
                            }

                       }


                     $deleteDetails = ItemIssueDetail::where('item_issue_id',$request->id)->where('status','D')->delete();

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
            // DB::rollBack();
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => 'Error Occured Record Not Inserted',
            //     'original_error' => $e->getMessage(), $e->getLine()
            // ]);

            
            DB::rollBack(); 
            getActivityLogs("Item Issue", "update", $e->getMessage(),$e->getLine());  

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
          
          $date = ItemIssue::where('item_issue_id',$request->id)->value('issue_date');
          // this is use for stock maintain
          $locationID = getCurrentLocation()->id;
          $oldIssueDetails = ItemIssueDetail::where('item_issue_id','=',$request->id)->get();
          $oldIssueDetailsData = [];
          if($oldIssueDetails != null){
            $oldIssueDetailsData = $oldIssueDetails->toArray();
          }

          foreach($oldIssueDetailsData as $gkey=>$gval){
            $qty = $gval['issue_qty'];
            $SecUnitBeforeUpdate =  LiveUpdateSecDate($date,$gval['item_id']);
            if($SecUnitBeforeUpdate === true){                
                DB::rollBack();     
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
                ]);
            }

            // increaseStockQty($locationID,$gval['item_id'],$qty);

            // decreaseStockQty($locationID,$gval['item_id'],0,$qty,'delete');

            // stockEffect($locationID,$gval['item_id'],$gval['item_id'],0,$qty,'delete','D','Item Issue',$gval['item_issue_details_id']);

            if($gval['item_details_id'] == null || $gval['item_details_id']== "" ||$gval['item_details_id'] == 0){

                stockEffect($locationID,$gval['item_id'],$gval['item_id'],0,$qty,'delete','D','Item Issue',$gval['item_issue_details_id']);
            }else{

                stockDetailsEffect($locationID,$gval['item_details_id'],$gval['item_details_id'],0,$gval['issue_qty'],'delete','D','Item Issue Details',$gval['item_issue_id'],'Yes','Item Issue Details',$gval['item_issue_id']);
            }

         }


          
           ItemIssueDetail::where('item_issue_id',$request->id)->delete();
           ItemIssue::destroy($request->id);

           DB::commit();

           return response()->json([
               'response_code' => '1',
               'response_message' => 'Record Deleted Successfully.',
           ]);

       }catch(\Exception $e){

        DB::rollBack(); 
        getActivityLogs("Item Issue", "delete", $e->getMessage(),$e->getLine());  

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

   public function getLatestItemIssueNo(Request $request)
    {
          $modal  =  ItemIssue::class;
          $sequence = 'issue_sequence';
          $prefix = 'ISSUE';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $sup_num_format['format'],
            'number'        => $sup_num_format['isFound'],
            'location'      => $locationName
        ]);

    }



    
public function isPartInUse(Request $request){
    if(isset($request->issue_part_id) && $request->issue_part_id != ""){

        $isFound = null;

        $isAllow = ItemIssueDetail::where('item_issue_id','=',$request->issue_id)->where('item_issue_details_id','=',$request->issue_part_id)->first();

        if($isAllow != null){

            $isFound =  ItemReturnDetail::where('item_issue_details_id','=',$request->issue_part_id)->first();

        }

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




//    public function createAutoSecondIssue(Request $request)
//    {
       

//       $locationData = LocationDetailStock::select('location_id')->where('secondary_stock_qty', '>', 0)->groupBy('location_id')->get();

//       foreach($locationData as $key=>$val){

//         $locationID = getCurrentLocation()->id;
//         $year_data = getCurrentYearData();   
        
        
//         $isFound = ItemIssue::where('year_id', '=', $year_data->id)->where('current_location_id',$val->location_id)->max('issue_sequence');         

//         if ($isFound != null) {
//             $isFound++;
//         } else {
//             $isFound = 1;
//         }

//         $middle_num = str_pad($isFound, 4, "0", STR_PAD_LEFT);

//         $postfix = $year_data->yearcode;
//         $locationCode = getCurrentLocation()->location_code;

 
//         $format =  $locationCode != "" ? $locationCode . '/'.'ISSUE'.'/' . $middle_num . '/' . $postfix : 'ISSUE'.'/' . $middle_num . '/' . $postfix;
        
        
    
         
//             $totalQty = LocationDetailStock::where('location_id',$val->location_id)->sum('secondary_stock_qty');
                

           
//             $item_issue =  ItemIssue::create([
//                 'issue_type_id_fix'=>   0,
//                 'issue_type_value_fix'=>'Outside',
//                 'current_location_id' => $locationID,
//                 'issue_sequence'        => $isFound,
//                 'issue_number'          => $format,
//                 'issue_date'            => '2025-10-26',
//                 'supplier_id'        => null,                                   
//                 'total_qty'          => $totalQty,                
//                 'special_notes'      => null,
//                 'year_id'            => $year_data->id,
//                 'company_id'         => Auth::user()->company_id,
//                 'created_by_user_id' => Auth::user()->id,
//                 'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString()          
//             ]);
             
//             if($item_issue->save())
//             {
                
//                 $stock_qty = LocationDetailStock::where('location_id',$val->location_id)->where('secondary_stock_qty', '>', 0)->get();


//                 foreach($stock_qty as $spKey => $spVal)
//                 {

//                     $item_id = ItemDetails::where('item_details_id',$spVal->item_details_id)->value('item_id');

                    
//                     $item_issue = ItemIssueDetail::create([
//                         'item_issue_id'    => $item_issue->item_issue_id,
//                         'item_id'   => $item_id,
//                         'item_details_id' => $spVal->item_details_id,
//                         'issue_qty'   => $spVal->secondary_stock_qty,
//                         'item_type'   => 'waste/scrap_entry',
//                         'remarks'   => null,
//                         'status' => 'Y',
//                     ]);              

                 
//                     stockDetailsEffect($val->location_id,$spVal->item_details_id,$spVal->item_details_id,$spVal->secondary_stock_qty,0,'add','D','Item Issue Details',$item_issue->item_issue_details_id,'Yes','Item Issue Details',$item_issue->item_issue_details_id);
                                
                      
                    
//                 }
                
//             }
            

 
       
        

//       }
//       return redirect()->route('manage-item_issue')->with('success', 'Item Stock Updated Successfully.');   

       

//    }

 public function createAutoSecondIssue(Request $request)
{
    // dd($request->id);
    $locationData = LocationDetailStock::select('location_id')
        ->where('secondary_stock_qty', '>', 0)
        ->groupBy('location_id')
        ->where('location_id',$request->id)
        ->get();


    foreach($locationData as $key => $val) {
        $year_data = getCurrentYearData();

        // Get all secondary stock entries for this location
        $stockEntries = LocationDetailStock::where('location_id', $val->location_id)->where('secondary_stock_qty', '>', 0)
            ->get();


        // Chunk into 25 entries per issue
        // $chunks = $stockEntries->chunk(25); // Laravel Collection chunk

        // foreach ($chunks as $chunkIndex => $chunkData) {
            // if($secondary_stock_qty)

       
            // Generate sequence
            $isFound = ItemIssue::where('year_id', $year_data->id)
                ->where('current_location_id', $val->location_id)
                ->max('issue_sequence');

            $isFound = $isFound ? $isFound + 1 : 1;

            $middle_num = str_pad($isFound, 4, "0", STR_PAD_LEFT);
            $postfix = $year_data->yearcode;
            $locationCode = Location::where('id',$val->location_id)->value('location_code');

            $format = $locationCode != "" 
                ? $locationCode . '/ISSUE/' . $middle_num . '/' . $postfix 
                : 'ISSUE/' . $middle_num . '/' . $postfix;

                $totalQty = LocationDetailStock::where('location_id',$val->location_id)->where('secondary_stock_qty', '>', 0)->sum('secondary_stock_qty');
                

            // Create ItemIssue
            $item_issue = ItemIssue::create([
                'issue_type_id_fix' => 0,
                'issue_type_value_fix' => 'Outside',
                'current_location_id' => $val->location_id,
                'issue_sequence' => $isFound,
                'issue_number' => $format,
                'issue_date' => '2025-10-26',
                'supplier_id' => null,
                'total_qty' => $totalQty,
                'special_notes' => null,
                'year_id' => $year_data->id,
                'company_id' => Auth::user()->company_id,
                'created_by_user_id' => Auth::user()->id,
                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString()
            ]);

            // Insert ItemIssueDetails
            foreach ($stockEntries as $spVal) {
                $item_id = ItemDetails::where('item_details_id', $spVal->item_details_id)->value('item_id');

                $item_issue_detail = ItemIssueDetail::create([
                    'item_issue_id' => $item_issue->item_issue_id,
                    'item_id' => $item_id,
                    'item_details_id' => $spVal->item_details_id,
                    'issue_qty' => $spVal->secondary_stock_qty,
                    'item_type' => 'waste/scrap_entry',
                    'remarks' => null,
                    'status' => 'Y',
                ]);

                stockDetailsEffect(
                    $val->location_id,
                    $spVal->item_details_id,
                    $spVal->item_details_id,
                    $spVal->secondary_stock_qty,
                    0,
                    'add',
                    'D',
                    'Item Issue Details',
                    $item_issue_detail->item_issue_details_id,
                    'Yes',
                    'Item Issue Details',
                    $item_issue_detail->item_issue_details_id
                );
            // }
        }
    }

    return redirect()->route('manage-item_issue')->with('success', 'Item Stock Updated Successfully.');
}



}