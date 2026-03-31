<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemReturn;
use App\Models\ItemReturnDetail;
use App\Models\ItemIssue;
use App\Models\ItemIssueDetail;
use App\Models\ItemDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Date;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class ItemReturnController extends Controller
{
    public function manage()
    {
        return view('manage.manage-item_return');
    }


    public function index(ItemIssue $ItemIssue,Request $request,DataTables $dataTables)
   {
       $year_data = getCurrentYearData();
       $location = getCurrentLocation();

       $itemReturn = ItemReturn::select(['return_number', 'issue_no','return_sequence', 'return_date', 'items.item_name', 'items.item_code', 'item_return_details.return_qty', 'item_return_details.remarks','item_return.created_by_user_id','item_return.last_by_user_id','item_return.created_on','item_return.last_on','item_return.item_return_id','item_groups.item_group_name','created_user.user_name as created_by_name','last_user.user_name as last_by_name','item_details.secondary_item_name',
        DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"),])

       ->leftJoin('item_return_details','item_return_details.item_return_id','=','item_return.item_return_id')

       ->leftJoin('suppliers','suppliers.id','=','item_return.supplier_id') 

       ->leftJoin('items','items.id','=','item_return_details.item_id')

       ->leftJoin('item_details','item_details.item_details_id','=','item_return_details.item_details_id')

       ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')

    //    ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')
       ->leftJoin('admin AS created_user', 'created_user.id', '=', 'item_return.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'item_return.last_by_user_id')

       ->where('item_return.year_id','=',$year_data->id)
       ->where('item_return.current_location_id','=',$location->id);
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $itemReturn->whereDate('item_return.return_date','>=',$from);

                $itemReturn->whereDate('item_return.return_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $itemReturn->where('item_return.return_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $itemReturn->where('item_return.return_date','<=',$to);

        }  
  

      return DataTables::of($itemReturn)


       ->editColumn('return_date', function($itemReturn){

           if ($itemReturn->return_date != null) {

               $formatedDate3 = Date::createFromFormat('Y-m-d', $itemReturn->return_date)->format(DATE_FORMAT); return $formatedDate3;

           }else{

               return '';

           }

       })
        ->filterColumn('item_return.return_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_return.return_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })



       ->editColumn('return_qty', function($itemReturn){

        return $itemReturn->return_qty > 0 ? number_format((float)$itemReturn->return_qty, 3, '.','') : number_format((float) 0, 3, '.','');


       
       })

       ->editColumn('created_by_user_id', function($itemReturn){
           if($itemReturn->created_by_user_id != null){
               $created_by_user_id = Admin::where('id','=',$itemReturn->created_by_user_id)->first('user_name');
               return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
           }else{
               return '';
           }
       })
       ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('last_by_user_id', function($itemReturn){
           if($itemReturn->last_by_user_id != null){
               $last_by_user_id = Admin::where('id','=',$itemReturn->last_by_user_id)->first('user_name');
               return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
           }else{
               return '';
           }

       })
       ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('created_on', function($itemReturn){
           if ($itemReturn->created_on != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $itemReturn->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
           }else{
               return '';
           }
       })
       ->filterColumn('item_return.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_return.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('last_on', function($itemReturn){
           if ($itemReturn->last_on != null) {
               $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $itemReturn->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
           }else{
               return '';
           }
       })
       ->filterColumn('item_return.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_return.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('item_name', function($itemReturn){ 
            if($itemReturn->item_name != ''){
                $item_name = ucfirst($itemReturn->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

       ->addColumn('options',function($itemReturn){
           $action = "<div>";        
           if(hasAccess("item_issue","edit")){
           $action .="<a id='edit_a' href='".route('edit-item_return',['id' => base64_encode($itemReturn->item_return_id )]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
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
        return view('add.add-item_return');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation();  

        $existNumber = ItemReturn::where([
            ['return_sequence',  $request->return_sequence],
            ['return_number', $request->return_number],
            ['year_id', $year_data->id],
            ['current_location_id', $locationCode->id],
        ])->lockForUpdate()->first();
        
        if($existNumber){
            $latestNo = $this->getLatestItemReturnNo($request);              
            $tmp =  $latestNo->getContent();
            $area = json_decode($tmp, true);
            $return_number =   $area['latest_po_no'];          
            $return_sequence = $area['number'];
        }else{
           $return_number = $request->return_number;
           $return_sequence = $request->return_sequence;
        }        
        DB::beginTransaction();

        try{

         
            $totalQty = 0;

            foreach ($request->item_id as $ctKey => $ctVal) {
                if ($ctVal != null) {
                    $totalQty += $request->return_qty[$ctKey];                     
                }
            }


            $item_return =  ItemReturn::create([
                'current_location_id'=> $locationCode->id,
                'return_sequence'=> $return_sequence,
                'return_number'=> $return_number,
                'return_date'=>Date::createFromFormat('d/m/Y', $request->return_date)->format('Y-m-d'),
                'supplier_id'=> $request->supplier_id,
                'issue_no'=> $request->issue_no,
                'total_qty'=>  $totalQty,
                'special_notes'=>$request->special_notes,
                'year_id'=> $year_data->id,
                'company_id'=> Auth::user()->company_id,
                'created_by_user_id'=> Auth::user()->id,
                'created_on'=>Carbon::now('Asia/Kolkata')->toDateTimeString()

            ]);
            // dd($item_return);

            if($item_return->save()){

                foreach($request->item_id as $spKey => $spVal){
                    if($spVal != null){
                        $item_return_detail = ItemReturnDetail::create([
                            'item_return_id'    => $item_return->item_return_id,
                            'item_issue_details_id'   => isset($request->item_issue_details_id[$spKey]) ? $request->item_issue_details_id[$spKey] : "",
                            'item_id'   => isset($request->item_id[$spKey]) ? $request->item_id[$spKey] : "",
                            'item_details_id'     => isset($request->item_details_id[$spKey]) ? $request->item_details_id[$spKey]   : null,
                            'return_qty'   => isset($request->return_qty[$spKey]) ? $request->return_qty[$spKey] : "",
                            'remarks'   => isset($request->remark[$spKey]) ? $request->remark[$spKey] : "",
                            'status' => 'Y',
                        ]);

                        // stockEffect($locationCode->id,$request->item_id[$spKey],$request->pre_item[$spKey],$request->return_qty[$spKey],0,'add','U');
                        if($request->item_details_id[$spKey] == null || $request->item_details_id[$spKey] == "")
                        {
                            stockEffect($locationCode->id,$request->item_id[$spKey],$request->item_id[$spKey],$request->return_qty[$spKey],0,'add','U','Item Return',$item_return_detail->id);
                        }else{
                             stockDetailsEffect($locationCode->id,$request->item_details_id[$spKey],$request->item_details_id[$spKey],$request->return_qty[$spKey],0,'add','U','Item Return Details',$item_return_detail->id,'Yes','Item Return Details',$item_return_detail->id );
                                
                        }

                    }
                   
                }

                
                // foreach($request->item_return_details_id as $spKey => $spVal){
                //     // dd($request->item_id[$spKey]);
                //     $item_return_detail = ItemReturnDetail::create([
                //         'item_return_id'    => $item_return->item_return_id,
                //         'item_issue_details_id'   => isset($request->item_issue_details_id[$spKey]) ? $request->item_issue_details_id[$spKey] : "",
                //         'item_id'   => isset($request->item_id[$spKey]) ? $request->item_id[$spKey] : "",
                //         'return_qty'   => isset($request->return_qty[$spKey]) ? $request->return_qty[$spKey] : "",
                //         'remarks'   => isset($request->remark[$spKey]) ? $request->remark[$spKey] : "",
                //     ]);

                //     // stockEffect($locationCode->id,$request->item_id[$spKey],$request->pre_item[$spKey],$request->return_qty[$spKey],0,'add','U');
                //     stockEffect($locationCode->id,$request->item_id[$spKey],$request->item_id[$spKey],$request->return_qty[$spKey],0,'add','U','Item Return',$item_return_detail->id);
                // }

                
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
        } catch(\Exception $e)
        {            
            // DB::rollBack();
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => 'Error Occured Record Not Inserted',
            //     'original_error' => $e->getMessage()
            // ]);

            DB::rollBack(); 
            getActivityLogs("Item Return", "add", $e->getMessage(),$e->getLine());  
            
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
       return view('edit.edit-item_return', compact('id'));
   }


   public function edit($id)
   {    
       $location = getCurrentLocation();
       $isAnyPartInUse = false;
       $itemReturn = ItemReturn::select('item_return.item_return_id','item_return.return_sequence','item_return.return_number','item_return.return_date','item_return.issue_no','item_return.special_notes')->where('item_return.item_return_id', $id)->first();
        //    $itemReturn = ItemReturn::where('item_return_id', $id)->first();
       $itemReturn->return_date = Date::createFromFormat('Y-m-d', $itemReturn->return_date)->format('d/m/Y');

       $iteReturnDetails = ItemReturnDetail::select(['item_return_details.item_return_details_id', 'item_return_details.item_return_id','item_return_details.item_id','item_return_details.return_qty','item_return_details.remarks','item_details.secondary_item_name','item_details.item_details_id',
       'items.item_code','items.item_name',  'item_groups.item_group_name', 'units.unit_name',
       ])      
       ->leftJoin('items', 'items.id', 'item_return_details.item_id') 
       ->leftJoin('item_details','item_details.item_details_id','=','item_return_details.item_details_id')
       ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')       
       ->leftJoin('units', 'units.id', 'items.unit_id')             
       ->where('item_return_id','=',$id)->get();

       if($iteReturnDetails != null){
        foreach($iteReturnDetails as $cpKey => $cpVal){
            if($cpVal->issue_date != null){
                $cpVal->issue_date = Date::createFromFormat('Y-m-d', $cpVal->issue_date)->format('d/m/Y');
            }
             $cpVal->item_detail = ItemDetails::select('item_details.item_details_id','item_details.secondary_item_name','units.unit_name','location_stock_details.secondary_stock_qty')
            ->leftJoin('location_stock_details','location_stock_details.item_details_id','=','item_details.item_details_id')
            ->leftJoin('items','items.id','=','item_details.item_id')
            ->leftJoin('units','units.id','=','items.second_unit')
            // ->where('location_stock_details.location_id','=',$location->id)
            ->groupBy('item_details.item_details_id')
            ->where('item_id',$cpVal->item_id)->get();

            if($itemReturn->return_date != null){
                $date = Date::createFromFormat('d/m/Y', $itemReturn->return_date)->format('Y-m-d');
            }
            $OldSecondryItem = LiveUpdateSecDate($date,$cpVal->item_id);
           
            if($OldSecondryItem == true)
            {
                $cpVal->in_use = true;
                $isAnyPartInUse = true;
            }
            else{
                $cpVal->in_use = false;
            }

        }
        if($itemReturn){
        $itemReturn->in_use = false;
        if($isAnyPartInUse == true){
            $itemReturn->in_use = true;
        }
    }
    }
    if ($iteReturnDetails) {
        return response()->json([
            'iteReturnDetails' => $iteReturnDetails,
            'itemReturn' => $itemReturn,
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
        $locationCode = getCurrentLocation();  

        DB::beginTransaction();
        try{


            $totalQty = 0;

            if(isset($request->item_id))
            {
                foreach ($request->item_id as $ctKey => $ctVal) {
                    if ($ctVal != null) {
                        $totalQty += $request->return_qty[$ctKey];                      
                    }
                }
            }

            $item_return =  ItemReturn::where('item_return_id','=',$request->id)->update([             
                'current_location_id'=> $locationCode->id,
                'return_sequence'=> $request->return_sequence,
                'return_number'=> $request->return_number,
                'return_date'=>Date::createFromFormat('d/m/Y', $request->return_date)->format('Y-m-d'),
                'supplier_id'=> $request->supplier_id,
                'issue_no'=> $request->issue_no,
                'total_qty'=>  $totalQty,
                'special_notes'=>$request->special_notes,
                'year_id'=> $year_data->id,
                'company_id'=> Auth::user()->company_id,
                'last_by_user_id'=> Auth::user()->id,
                'last_on'=> Carbon::now('Asia/Kolkata')->toDateTimeString()                
            ]);


            if($item_return)
            {

                //  // this cose use to stock maintain
                //  $oldReturnDetails = ItemReturnDetail::where('item_return_id','=',$request->id)->get();
                //  $oldReturnDetailsData = [];
                //  if($oldReturnDetails != null){
                //      $oldReturnDetailsData = $oldReturnDetails->toArray();
                //  }
                 

                if (isset($request->item_return_details_id) && !empty($request->item_return_details_id)) {

                     $materialDtails =  ItemReturnDetail::where('item_return_id',$request->id)->update([
                        'status' => 'D',
                    ]);
                    
                    foreach ($request->item_return_details_id as $sodKey => $sodVal) { 
                                               
                        
                        if($sodVal == "0"){                                    
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){                                
                                $item_return_detail = ItemReturnDetail::create([
                                    'item_return_id'    => $request->id,
                                    'item_issue_details_id'   => isset($request->item_issue_details_id[$sodKey]) ? $request->item_issue_details_id[$sodKey] : "",
                                    'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                    'item_details_id'     => isset($request->item_details_id[$sodKey]) ? $request->item_details_id[$sodKey]   : null,
                                    'return_qty'   => isset($request->return_qty[$sodKey]) ? $request->return_qty[$sodKey] : "",
                                    'remarks'   => isset($request->remark[$sodKey]) ? $request->remark[$sodKey] : "",
                                    'status' => 'Y',  
                                ]);

                                // stockEffect($locationCode->id,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->return_qty[$sodKey],0,'add','U');

                                // stockEffect($locationCode->id,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->return_qty[$sodKey],0,'add','U','Item Return',$item_return_detail->id);

                                if($request->item_details_id[$sodKey] == null || $request->item_details_id[$sodKey] == "")
                                {
                                    stockEffect($locationCode->id,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->return_qty[$sodKey],0,'add','U','Item Return',$item_return_detail->id);
                                }else{
                                    stockDetailsEffect($locationCode->id,$request->item_details_id[$sodKey],$request->item_details_id[$sodKey],$request->return_qty[$sodKey],0,'add','U','Item Return Details',$item_return_detail->id,'Yes','Item Return Details',$item_return_detail->id );
                                        
                                }
                            }
                        }else{     
                                
                                if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){                                    
                                    $item_return_detail = ItemReturnDetail::where('item_return_details_id',$sodVal)->update([
                                        'item_return_id'    => $request->id,
                                        'item_issue_details_id'   => isset($request->item_issue_details_id[$sodKey]) ? $request->item_issue_details_id[$sodKey] : "",
                                        'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                        'item_details_id'     => isset($request->item_details_id[$sodKey]) ? $request->item_details_id[$sodKey]   : null,
                                        'return_qty'   => isset($request->return_qty[$sodKey]) ? $request->return_qty[$sodKey] : "",
                                        'remarks'   => isset($request->remark[$sodKey]) ? $request->remark[$sodKey] : "",
                                        'status' => 'Y',  
                                    ]);
                                    if($request->pre_item_detail[$sodKey] == "" && $request->pre_item_detail[$sodKey] == null && $request->item_details_id[$sodKey] == "" && $request->item_details_id[$sodKey] == null)
                                    {    
                                        // dd("main if",$request);                                     
                                      stockEffect($locationCode->id,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->return_qty[$sodKey] ,$request->org_return_qty[$sodKey],'edit','U','Item Return',$sodVal);
                                    }
                                    else{
                                        // details mathi normal kare tyare
                                        if($request->pre_item_detail[$sodKey]!="" && $request->pre_item_detail[$sodKey]!= null && $request->item_details_id[$sodKey]=="" && $request->item_details_id[$sodKey] == null)
                                        {
                                            //  stockDetailsEffect($locationCode->id,$request->item_details_id[$sodKey],$request->pre_item_detail[$sodKey],$request->return_qty[$sodKey],$request->org_return_qty[$sodKey],'edit','U','Item Return Details',$sodVal,'Yes','Item Return Details',$sodVal );
                                            //  dd("nest if");         
                                              stockDetailsEffect($locationCode->id,$request->pre_item_detail[$sodKey],$request->pre_item_detail[$sodKey],0,$request->org_return_qty[$sodKey],'delete','U','Item Return Details',$sodVal,'Yes','Item Return Details',$sodVal);

                                                stockEffect($locationCode->id,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->return_qty[$sodKey],0,'add','U','Item Return',$sodVal);
                                        }
                                        // normal mathi details vadi kare tyare
                                        else if($request->pre_item_detail[$sodKey]=="" && $request->pre_item_detail[$sodKey]== null && $request->item_details_id[$sodKey]!="" && $request->item_details_id[$sodKey] != null){

                                             stockEffect($locationCode->id,$request->pre_item[$sodKey],$request->pre_item[$sodKey],0, $request->org_return_qty[$sodKey],'delete','U','Item Return',$sodVal);

                                             stockDetailsEffect($locationCode->id,$request->item_details_id[$sodKey],$request->item_details_id[$sodKey],$request->return_qty[$sodKey],0,'add','U','Item Return Details',$sodVal,'Yes','Item Return Details',$sodVal );
                                        }
                                        else {
                                           stockDetailsEffect($locationCode->id,$request->item_details_id[$sodKey],$request->pre_item_detail[$sodKey],$request->return_qty[$sodKey],$request->org_return_qty[$sodKey],'edit','U','Item Return Details',$sodVal,'Yes','Item Return Details',$sodVal );
                                        }
                                    }
                                    // return;
                                    // stockEffect($locationCode->id,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->return_qty[$sodKey] ,$request->org_return_qty[$sodKey],'edit','U','Item Return',$sodVal);

                                    // foreach ($oldReturnDetailsData as $key => $value) {
                                    //     if ($value['item_id'] == $request->item_id[$sodKey]) {
                                    //         unset($oldReturnDetailsData[$key]);

                                    //     }
                                    // }          
                                    
                                }else{        
                                    // ItemReturnDetail::where('item_return_details_id', $sodVal)->delete();
                              
                                    // foreach ($oldReturnDetailsData as $key => $value) {
                                    //     if ($value['item_return_details_id'] == $sodVal) {
                                    //         unset($oldReturnDetailsData[$key]);

                                    //     }
                                    // }          

                                    // if(isset($oldReturnDetailsData) && !empty($oldReturnDetailsData)){
                                    //     foreach($oldReturnDetailsData as $gkey=>$gval){                                          
                                    //         // increaseStockQty($locationCode->id,$gval['item_id'],0,$gval['grn_qty'],'delete');
                                    //         stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0, $gval['return_qty'],'delete','U','Item Return',$gval['item_return_details_id']);
                                    //         unset($oldReturnDetailsData[$gkey]);
                                    //     }
                                    // }
                                    
                                }
                            }
                    }

                     // pendig from itemm issue logic below code
                    // if(isset($oldReturnDetailsData) && !empty($oldReturnDetailsData)){
                    //     foreach($oldReturnDetailsData as $gkey=>$gval){
                    //     stockEffect($locationCode->id,$gval['item_id'],$gval['item_id'],0, $gval['return_qty'],'delete','U');
                    //     ItemReturnDetail::where('item_return_details_id', $gval['item_return_details_id'])->delete();
                    //     }
                    // }

                       $deleteretunDetails = ItemReturnDetail::where('item_return_id',$request->id)->where('status','D')->get();

                       if(!empty($deleteretunDetails)){
                            foreach($deleteretunDetails as $dkey => $dval){

                                if($dval['item_details_id']!= "" &&$dval['item_details_id'] != null)
                                {
                                     stockDetailsEffect($locationCode->id,$dval['item_details_id'],$dval['item_details_id'],0,$dval['return_qty'],'delete','U','Item Return Details',$dval['item_return_details_id'],'Yes','Item Return Details',$dval['item_return_details_id']);
                                }
                                else{
                                    stockEffect($locationCode->id,$dval['item_id'],$dval['item_id'],0, $dval['return_qty'],'delete','U','Item Return',$dval['item_return_details_id']);
                                }
                            }

                       }

                        $deleteDetails = ItemReturnDetail::where('item_return_id',$request->id)->where('status','D')->delete();

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
        catch(\Exception $e)
        {
            // dd($e->getMessage(),$e->getLine());
            // DB::rollBack();
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => 'Error Occured Record Not Inserted',
            //     'original_error' => $e->getMessage()
            // ]);

            DB::rollBack(); 
            getActivityLogs("Item Return", "update", $e->getMessage(),$e->getLine());  

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

   
    public function getLatestItemReturnNo(Request $request){
        $modal  =  ItemReturn::class;
          $sequence = 'return_sequence';
          $prefix = 'RET';
          $po_num_format = getLatestSequence($modal,$sequence,$prefix);

          $locationName = getCurrentLocation();

          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $po_num_format['format'],
            'number'        => $po_num_format['isFound'],
            'location'      => $locationName
        ]);
    }


    public function getIssueListForReturn(Request $request){

        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation();

        if(isset($request->id)){
            $issue_id = ItemReturnDetail::select('item_issue_details_id')->where('item_return_id',$request->id)->get();

            $edit_issue_data = ItemReturnDetail::select(['item_issue.issue_number','item_issue.issue_date','item_issue_details.item_issue_details_id as issue_detail_id','item_issue_details.issue_qty',
            'item_return_details.return_qty as pending_issue_qty','items.item_name', 'items.item_code','item_groups.item_group_name', 'units.unit_name',
            //  DB::raw("(item_issue_details.issue_qty  - (SELECT IFNULL(SUM(item_return_details.return_qty),0) FROM item_return_details WHERE item_issue_details_id  = item_issue_details.item_issue_details_id )) as pending_issue_qty"),
             ])
            ->leftJoin('item_issue_details','item_issue_details.item_issue_details_id','=','item_return_details.item_issue_details_id')
            ->leftJoin('item_issue','item_issue.item_issue_id','=','item_issue_details.item_issue_id')
            ->leftJoin('items', 'items.id', 'item_issue_details.item_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            ->leftJoin('units', 'units.id', 'items.unit_id')
            ->where('item_return_details.item_return_id',$request->id)
            ->get();
        }

        $itemIssueData = ItemIssue::select(['item_issue.issue_number','item_issue.issue_date','item_issue_details.item_issue_details_id as issue_detail_id','item_issue_details.issue_qty','items.item_name', 'item_groups.item_group_name', 'units.unit_name', 'items.item_code',
        DB::raw("(item_issue_details.issue_qty  - (SELECT IFNULL(SUM(item_return_details.return_qty),0) FROM item_return_details WHERE item_issue_details_id  = item_issue_details.item_issue_details_id )) as pending_issue_qty"),  
        ])
        ->leftJoin('item_issue_details','item_issue_details.item_issue_id','=','item_issue.item_issue_id')
        ->leftJoin('items', 'items.id', 'item_issue_details.item_id')
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
        ->leftJoin('units', 'units.id', 'items.unit_id')
        ->where('item_issue.current_location_id',$locationCode->id)
        ->where('item_issue.year_id',$year_data->id)             
        ->where('item_issue.supplier_id',$request->supplier_id)
        ->having('pending_issue_qty','>',0)
        ->get();

        if(isset($edit_issue_data)){
            $data = collect($itemIssueData)->merge($edit_issue_data);
            $grouped = $data->groupBy('issue_detail_id');    
            

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pending_issue_qty += (float) $item->pending_issue_qty;
                    return $carry;
                });
            });
    
            $itemIssueData = $merged->values();   

        }

        
        if($itemIssueData != null){
            foreach($itemIssueData as $cpKey => $cpVal){
                if($cpVal->issue_date != null){
                    $cpVal->issue_date = Date::createFromFormat('Y-m-d', $cpVal->issue_date)->format('d/m/Y');
                }
            }
        }

        if($itemIssueData != null){
            return response()->json([
                'response_code' => '1',
                'itemIssueData' => $itemIssueData
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'itemIssueData' => []
            ]);
        }
    

    }


    public function getIssuePartsDataForReturn(Request $request){
          $year_data = getCurrentYearData();
          $locationCode = getCurrentLocation();

          $request->issueids = explode(',',$request->issueids);


          if(isset($request->id)){
            
            $issue_id = ItemReturnDetail::select('item_issue_details_id')->where('item_return_id',$request->id)->get();      
            
            
            $edit_issue_data = ItemReturnDetail::select(['item_issue_details.item_issue_details_id as item_issue_details_id','item_issue.issue_number','item_issue.issue_date','items.item_name','items.item_code','items.id', 'units.unit_name', 'item_groups.item_group_name',
            'item_return_details.return_qty as pending_issue_qty'
         
             ])
            ->leftJoin('item_issue_details','item_issue_details.item_issue_details_id','=','item_return_details.item_issue_details_id')
            ->leftJoin('item_issue','item_issue.item_issue_id','=','item_issue_details.item_issue_id')
            ->leftJoin('items', 'items.id', 'item_issue_details.item_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            ->leftJoin('units', 'units.id', 'items.unit_id')
            ->whereIn('item_return_details.item_issue_details_id',$request->issueids)
            ->where('item_return_details.item_return_id',$request->id)
            ->get();

        }

          $issue_data = ItemIssue::select(['item_issue_details.item_issue_details_id as item_issue_details_id','item_issue.issue_number','item_issue.issue_date','items.item_name','items.item_code','items.id', 'item_groups.item_group_name', 'units.unit_name',
          DB::raw("(item_issue_details.issue_qty  - (SELECT IFNULL(SUM(item_return_details.return_qty),0) FROM item_return_details WHERE item_issue_details_id  = item_issue_details.item_issue_details_id )) as pending_issue_qty"),  
          ])          
          ->leftJoin('item_issue_details','item_issue_details.item_issue_id','=','item_issue.item_issue_id')
          ->leftJoin('items', 'items.id', 'item_issue_details.item_id')
          ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
          ->leftJoin('units', 'units.id', 'items.unit_id')
          ->whereIn('item_issue_details.item_issue_details_id',$request->issueids)
          ->where('item_issue.current_location_id',$locationCode->id)
          ->where('item_issue.year_id',$year_data->id)
          ->having('pending_issue_qty','>',0)
          ->get();

        if (isset($edit_issue_data)) {
            $data = collect($issue_data)->merge($edit_issue_data);
        
            // Assuming the issue_detail_id you mentioned is actually item_issue_details_id
            $grouped = $data->groupBy('item_issue_details_id');    
        
            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pending_issue_qty += (float) $item->pending_issue_qty;
                    return $carry;
                });
            });
        
            $issue_data = $merged->values();   
        }

        if($issue_data != null){
                foreach($issue_data as $cpKey => $cpVal){
                    if($cpVal->issue_date != null){
                        $cpVal->issue_date = Date::createFromFormat('Y-m-d', $cpVal->issue_date)->format('d/m/Y');
                    }

                    if(isset($request->id)){  
                        $return_qty =   ItemReturnDetail::where('item_issue_details_id','=',$cpVal->item_issue_details_id)->where('item_return_id',$request->id)->sum('return_qty');
                        $grn_id = ItemReturnDetail::where('item_issue_details_id','=',$cpVal->item_issue_details_id)->where('item_return_id',$request->id)->first();

                        $cpVal->return_qty = $return_qty;
                        $cpVal->item_return_details_id  = $grn_id!= null ? $grn_id->item_return_details_id : 0;            
                        
                    }else{
                        $cpVal->return_qty = 0;
                        $cpVal->item_return_details_id  = 0;
                    }
                }
            }

            if($issue_data != null){
                return response()->json([
                    'response_code' => '1',
                    'issue_part_data' => $issue_data
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'issue_data' => []
                ]);
            }

    }



    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{


               // this is use for stock maintain
          $date = ItemReturn::where('item_return_id',$request->id)->value('return_date');
          $locationID = getCurrentLocation()->id;
          $oldReturnDetails = ItemReturnDetail::where('item_return_id',$request->id)->get();
          $oldReturnDetailsData = [];
          if($oldReturnDetails != null){
            $oldReturnDetailsData = $oldReturnDetails->toArray();
          }

          foreach($oldReturnDetailsData as $gkey=>$gval){
            $qty = $gval['return_qty'];
            $SecUnitBeforeUpdate =  LiveUpdateSecDate($date,$gval['item_id']);
            if($SecUnitBeforeUpdate === true){                
                DB::rollBack();     
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
                ]);
            }
            if($gval['item_details_id'] == null || $gval['item_details_id']== "" ||$gval['item_details_id'] == 0){

                stockEffect($locationID,$gval['item_id'],$gval['item_id'],0,$qty,'delete','U','Item Return',$gval['item_return_details_id']);
            }else{

                stockDetailsEffect($locationID,$gval['item_details_id'],$gval['item_details_id'],0,$gval['return_qty'],'delete','U','Item Return Details',$gval['item_return_id'],'Yes','Item Return Details',$gval['item_return_id']);
            }

         }

         
            ItemReturnDetail::where('item_return_id',$request->id)->delete();
            ItemReturn::destroy($request->id);

            DB::commit();

 
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){

            DB::rollBack(); 
            getActivityLogs("Item Return", "delete", $e->getMessage(),$e->getLine());  
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