<?php

namespace App\Http\Controllers;


use App\Models\Country;
use App\Models\Item;
use App\Models\Admin;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Unit;
use App\Models\HsnCode;
use DataTables;
use Date;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportCountry;
use App\Models\ItemGroup;
use App\Models\CustomerGroup;
use App\Models\ItemRawMaterialMappingDetail;
use App\Models\SalesOrderDetail;
use App\Models\PriceListDetails;
use App\Models\PriceList;
use App\Models\ReplacementItemDecisionDetails;
use App\Models\SOMapping;
use App\Models\GRNMaterialDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\DispatchPlanDetailsDetails;
use App\Models\SOShortClose;
use App\Models\SalesOrderDetailsDetails;
use App\Models\DispatchPlanDetails;
use App\Models\CustomerReplacementEntryDetails;
use App\Models\PurchaseOrder;
use App\Models\ItemIssueDetail;
use App\Models\ItemReturnDetail;
use App\Models\ItemProductionDetail;
use App\Models\ItemAssemblyProduction;
use App\Models\LoadingEntryDetails;
use App\Models\ItemAssemblyProductionDetails;
use App\Models\MaterialRequestDetail;
use App\Models\SupplierRejectoionDetails;
use App\Models\SupplierItemMapping;
use App\Models\LocationStock;
use App\Models\LocationDetailStock;


use App\Exports\exportItem;
use App\Models\ItemDetails;
use App\Models\PRShortClose;
use App\Models\PurchaseRequisitionDetails;
use App\Models\QCApproval;

class ItemController extends Controller
{
    public function itemData()
    {
        $item = Item::orderBy('item_name', 'ASC')->get();
         if($item){
            return response()->json([
                'item' => $item,
                'response_code' => '1'
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'No Data Avilable',
            ]);
        }
           
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function manage()
    {
        return view('manage.manage-item');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Item $Item,Request $request,DataTables $dataTables)
    {
        $itemData = Item::select(['items.id','items.item_name','items.item_code','item_groups.item_group_name','items.item_sequence','items.item_code','items.min_stock_qty','items.max_stock_qty','items.re_order_qty','hsn_code.hsn_code','units.unit_name','items.rate_per_unit','items.require_raw_material_mapping','items.fitting_item','items.show_item_in_print','items.print_dispatch_plan','items.own_manufacturing','items.dont_allow_req_msl','items.service_item','items.allow_partial_dispatch','items.secondary_unit','items.qty','second_unit.unit_name as second_unit','items.qc_required','items.status','items.created_by_user_id','items.last_by_user_id','items.last_on','items.created_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('units','units.id','=','items.unit_id')        
        ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')        
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')        
        ->leftJoin('hsn_code','hsn_code.id','=','items.hsn_code')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'items.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'items.last_by_user_id');
    
        return DataTables::of($itemData)
        ->editColumn('created_by_user_id', function($itemData){ 
            if($itemData->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$itemData->created_by_user_id)->first('user_name'); 
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($itemData){ 
            if($itemData->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$itemData->last_by_user_id)->first('user_name'); 
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('item_name', function($itemData){ 
            if($itemData->item_name != ''){
                $item_name = ucfirst($itemData->item_name);
                return $item_name;
            }else{
                return '';
            }
            // return Str::limit($itemData->item_name, 50);
        })
        ->editColumn('min_stock_qty', function($itemData){ 
            return helpFormatWeight($itemData->min_stock_qty);
        })
        // ->editColumn('item_group', function($itemData){ 
        //     return Str::limit($itemData->item_group_name, 50);
        // })
        ->editColumn('unit', function($itemData){ 
            if($itemData->unit_name != ''){
                $unit_name = ucfirst($itemData->unit_name);
                return $unit_name;
            }else{
                return '';
            }
            // return Str::limit($itemData->unit_name, 50);
        })
        // ->editColumn('min_stock_qty', function($itemData){ 
        //     return Str::limit($itemData->min_stock_qty, 50);
        // })
        ->editColumn('max_stock_qty', function($itemData){ 
            return helpFormatWeight($itemData->max_stock_qty);

            //return Str::limit($itemData->max_stock_qty, 50);
        })
        ->editColumn('re_order_qty', function($itemData){ 
            return helpFormatWeight($itemData->re_order_qty);
            // return Str::limit($itemData->re_order_qty, 50);
        })
        // ->editColumn('hsn_code', function($itemData){ 
        //     if($itemData->hsn_code != ''){
        //         $hsn_code = ucfirst($itemData->hsn_code);
        //         return $hsn_code;
        //     }else{
        //         return '';
        //     }
        //   //  return Str::limit($itemData->hsn_code, 50);
        // })
        ->editColumn('rate_per_unit', function($itemData){ 
            return helpFormatAmount($itemData->rate_per_unit);
            // return Str::limit($itemData->rate_per_unit, 50);
        })
        ->editColumn('require_raw_material_mapping', function($itemData){ 
            if($itemData->require_raw_material_mapping != ''){
                $require_raw_material_mapping = ucfirst($itemData->require_raw_material_mapping);
                return $require_raw_material_mapping;
            }else{
                return '';
            }
           // return Str::limit($itemData->require_raw_material_mapping, 50);
        })
        ->editColumn('fitting_item', function($itemData){ 
            if($itemData->fitting_item != ''){
                $fitting_item = ucfirst($itemData->fitting_item);
                return $fitting_item;
            }else{
                return '';
            }
            // return Str::limit($itemData->fitting_item, 50);
        })
        ->editColumn('show_item_in_print', function($itemData){ 
            if($itemData->show_item_in_print != ''){
                $show_item_in_print = ucfirst($itemData->show_item_in_print);
                return $show_item_in_print;
            }else{
                return 'No';
            }
            // return Str::limit($itemData->fitting_item, 50);
        })
        ->editColumn('dont_allow_req_msl', function($itemData){ 
            if($itemData->dont_allow_req_msl != ''){
                $dont_allow_req_msl = ucfirst($itemData->dont_allow_req_msl);
                return $dont_allow_req_msl;
            }else{
                return 'No';
            }
            // return Str::limit($itemData->fitting_item, 50);
        })
        ->editColumn('qty', function($itemData){ 
            if($itemData->second_unit != ""){
                return helpFormatWeight($itemData->qty);
            }

            //return Str::limit($itemData->max_stock_qty, 50);
        })
        ->editColumn('second_unit', function($itemData){ 
            if($itemData->second_unit != '' ){
                $second_unit = ucfirst($itemData->second_unit);
                return $second_unit;
            }else{
                return '';
            }
            // return Str::limit($itemData->unit_name, 50);
        })
        ->editColumn('status', function($itemData){ 
            if($itemData->status != ""){
                return ucfirst($itemData->status);
            }

            //return Str::limit($itemData->max_stock_qty, 50);
        })
        ->filterColumn('items.status', function($query, $keyword) {
            // Convert keyword to lowercase for case-insensitive matching
            $keyword = strtolower($keyword);

            
                $query->where('items.status', 'like', "$keyword%");
        })
        // ->filterColumn('items.status', function($query, $keyword) {
        //     $query->where(function($q) use ($keyword) {
        //         $keyword = strtolower(trim($keyword));

        //         if ($keyword === 'deactive') {
        //             $q->orWhere('items.status', 'deactive');
        //         } elseif ($keyword === 'active') {
        //             $q->orWhere('items.status', 'active');
        //         } else {
        //             $q->orWhere('items.status', 'like', "%{$keyword}%");
        //         }
        //     });
        // })
        
        ->editColumn('created_on', function($itemData){ 
            if ($itemData->created_on != null) { 
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $itemData->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 
                // dd($formatedDate1);
            }else{
                return '';
            }
        })
        ->filterColumn('items.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(items.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($itemData){ 
            if ($itemData->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $itemData->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->filterColumn('items.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(items.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        // ->addColumn('item_detail', function($itemData){
        //     if($itemData->id != ''){

        //         $doc_file = ItemDetails::select('item_detail_name','id')->where('item_id',$itemData->id)->latest('id')->first();

        //         if($doc_file != ""){

        //             if($doc_file->agreement_document != ""){
        //                 $documentUrl = asset('storage/' . $doc_file->agreement_document);
    
        //                 $document = '<a href="' . $documentUrl . '" target="_blank">
        //                 <i class="iconfa-eye-open action-icon" ></i>
        //              </a>';
    
        //             }else{
        //                 $document = "";
        //             }
        //         }else{
        //             $document = "";
        //         }
        //         return $document;
        //     }else{
        //         return '';
        //     }
        // })
        ->addColumn('options',function($itemData){ 
            $action = "<div>";
            // if($itemData->id != 1){
                if(hasAccess("item","edit")){
                $action .="<a id='edit_a' href='".route('edit-item',['id' => base64_encode($itemData->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            // }
            // if($itemData->id != 1){
                if(hasAccess("item","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            // }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','item_name', 'unit', 'min_stock_qty','max_stock_qty','re_order_qty','hsn_code','rate_per_unit','require_raw_material_mapping','fitting_item', 'options'])
        ->make(true);
    }

   /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unit = Unit::select('id','unit_name')->orderBy('unit_name','asc')->get();
        $hsn_code  = HsnCode::select('id','hsn_code')->orderBy('hsn_code','asc')->get();
        return view('add.add-item')->with(['unit' => $unit,'hsn_code' => $hsn_code]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'item_name'=>'required|max:255|unique:items',
            'item_group_id'=>'required|max:255',            
            'item_code'=>'required',    
            'require_raw_material_mapping'=>'required',
            'fitting_item'=>'required'
        ],
        [
            'item_name.required' => 'Please Enter Item',            
            'item_name.unique'   => 'The Item  Name Has Already Been Taken', 
            'item_name.max' => 'Maximum 255 Characters Allowed',
            'item_group_id.required' => 'Please Enter  Item',            
            'item_code.required' => 'Please Select Item Code',        
            'require_raw_material_mapping.required' => 'Please Select Item Mapping',
            'fitting_item.required' => 'Please Select Fitting Mapping'         
        ]);
        // dd($request->all());
        DB::beginTransaction();
      //  $item_code = explode(" ", $request->item_code);
            
        // $item_sequence = Str::substr( $request->item_code, -5); // change date 23-05-2024
        $item_sequence = (int) preg_replace('/\D/', '', $request->item_code);         
        
        $isFound = Item::where('item_sequence',$item_sequence)->where('item_group_id',$request->item_group_id)->lockForUpdate()->first();
        // $isFound = Item::where('item_sequence',$item_sequence)->where('item_group_id',$request->item_group_id)->first();

        if($isFound != null){
            $item_sequence++;

            $item_data = ItemGroup::select('item_group_code')->where('id', '=', $request->item_group_id)->first();

            $middle_num = str_pad($item_sequence, 5, "0", STR_PAD_LEFT);            
            
            $item_code = $item_data->item_group_code.$middle_num;

        }else{
            $item_code = $request->item_code;
        }
      
        $planDispatchData = $request->print_dispatch_plan == "" ? "No" : $request->print_dispatch_plan;
        $own_manu = $request->own_manufacturing == "" ? "No" : $request->own_manufacturing;
        $dont_allow_req_msl = $request->dont_allow_req_msl == "" ? "No" : $request->dont_allow_req_msl;
        $service_item = $request->service_item == "" ? "No" : $request->service_item;
        $show_item_in_print = $request->require_raw_material_mapping == "No" ? NULL : $request->show_item_in_print;
          
      
        try{
            // $itemData=  Item::create([
            //     'item_name' => $request->item_name,
            //     'item_sequence' => $request->item_sequence,
            //     'item_group_id' => $request->item_group_id,
            //     'item_code' => $request->item_code,
            //     'item_sequence' => $item_sequence,
            //     'unit_id' => $request->unit_id,
            //     'min_stock_qty' => $request->min_stock_qty,
            //     'max_stock_qty' => $request->max_stock_qty,
            //     're_order_qty' => $request->re_order_qty,
            //     'hsn_code' => $request->hsn_code_id,
            //     'rate_per_unit' => $request->rate_per_unit,
            //     'require_raw_material_mapping' => $request->require_raw_material_mapping,
            //     'fitting_item' => $request->fitting_item,
            //     'print_dispatch_plan' =>   $planDispatchData ,
            //     'company_id' => Auth::user()->company_id,
            //     'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            //     'created_by_user_id' => Auth::user()->id
            // ]);
            $itemData=  Item::create([
                'item_name' => $request->item_name,
                'item_group_id' => $request->item_group_id,
                'item_sequence' => $item_sequence,
                // 'item_code' => $request->item_code,
                'item_code' => $item_code,
                'unit_id' => $request->unit_id,
                'min_stock_qty' => $request->min_stock_qty,
                'max_stock_qty' => $request->max_stock_qty,
                're_order_qty' => $request->re_order_qty,
                'hsn_code' => $request->hsn_code_id,
                'rate_per_unit' => $request->rate_per_unit,
                'require_raw_material_mapping' => $request->require_raw_material_mapping,
                'fitting_item' => $request->fitting_item,
                'print_dispatch_plan' =>   $planDispatchData ,
                'own_manufacturing' =>   $own_manu ,
                'dont_allow_req_msl' =>   $dont_allow_req_msl ,
                'service_item' =>   $service_item ,
               'show_item_in_print' =>   $show_item_in_print ,
                'status' =>   $request->status ,
                'allow_partial_dispatch' =>   $request->allow_partial_dispatch ,
                'secondary_unit' =>   $request->secondary_unit,
                'wt_pc' =>   $request->wt_pc,
                'qc_required' =>   $request->qc_required,
                // 'qty' =>   $request->qty != "" ? $request->qty : 1,
                'qty' =>   $request->secondary_unit == "Yes" ? null : 1,
                'second_unit' =>   $request->second_unit,
                'company_id' => Auth::user()->company_id,
                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id
            ]);
            

            if($itemData->save()){
                if($request->secondary_unit == 'Yes'){
                    if(isset($request->secondary_qty) && !empty($request->secondary_qty)){
                        foreach($request->secondary_qty as $ctKey => $ctVal){
                            $itemDetail = ItemDetails::create([
                                'item_id' => $itemData->id,
                                'secondary_qty' => $ctVal,
                                'secondary_wt_pc' => $request->secondary_wt_pc[$ctKey],
                                'secondary_item_name' => $request->secondary_item_name[$ctKey],
                            ]);  
                        }                      
                    }
                    
                }

                // if(isset($request->contacts) && !empty($request->contacts)){
                //     $convertJson = json_decode($request->contacts, true);

                //         foreach($convertJson as $ctKey => $ctVal){
                            
                //             if($ctVal != null){
                //                 $contact_data=  ItemDetails::create([
                //                     'item_id' => $itemData->id,

                //                     'item_detail_name' => isset($ctVal['item_detail_name']) ? $ctVal['item_detail_name'] : "",

                //                     // 'contact_mobile_no' => isset($ctVal['contact_mobile_no']) ? $ctVal['contact_mobile_no'] : "",

                //                     // 'contact_email' => isset($ctVal['contact_email']) ? $ctVal['contact_email'] : "",
                                
                //                 ]);
                //             }
                //         }
                // }
                        

                // $csGroup = CustomerGroup::select('id')->get();
                
                // foreach($csGroup as $cKey => $cval)
                // {
                //     $mainPrice = PriceList::create([
                //         'customer_group_id' => $cval['id'],
                //         'company_id' => Auth::user()->company_id,
                //         'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                //         'created_by_user_id' => Auth::user()->id
                //     ]);
                        
                //     $pdata=  PriceListDetails::create([
                //         'pl_id' => $mainPrice->id,
                //         'item_id' => $itemData->id,
                //         'sales_rate' => 0,             
                //         'customer_group_id' => $cval['id'],
                //     ]);
                // }
    
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Inserted Successfully.',
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Inserted',
                ]);
            }
        
    }catch(\Exception $e){
            dd($e->getMessage());
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item,$id,Request $request)
    {
        $unit = Unit::select('id','unit_name')->orderBy('unit_name','asc')->get();
        $hsn_code  = HsnCode::select('id','hsn_code')->orderBy('hsn_code','asc')->get();
        $item_map = ItemRawMaterialMappingDetail::where('raw_material_id', base64_decode($request->id))->orWhere('item_id',base64_decode($request->id))->first();  
     
        return view('edit.edit-item')->with(['id' => $id, 'item_map' => $item_map]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item,Request $request,$id)
    {
        $itemData = Item::where('id','=',$id)->first(); 
        $item_data = ItemDetails::where('item_id','=',$id)->get();            
        $checkItem  = checkItemDisabled($id);   
        // $details = $item_data->pluck('item_details_id')->toArray();     
        // $checkItemDetails  = checkItemDetailDisabled($details); 
        $isAnyPartInUse = false;
        $item_data_with_usage = $item_data->map(function ($detail) use (&$isAnyPartInUse)  {
        $detail->is_used = checkItemDetailDisabled([$detail->item_details_id]);
        if($detail->is_used == true)
        {
              $isAnyPartInUse = true;
        }
        return $detail;
        }); 
        
        $itemData->secondry_unit_in_use = $isAnyPartInUse;

        if($itemData){
            return response()->json([
                'item' => $itemData, 
                'item_data'=>$item_data_with_usage,               
                'in_use' => $checkItem,
                'response_code' => '1',
                'response_message' => '',
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        // dd($request);
        $validated = $request->validate([
            'item_name'=>['required','max:255',Rule::unique('items')->ignore($request->id, 'id'),
            'item_group_id'=>'required','max:255',
            'item_sequence'=>'required',
            'item_code'=>'required',           
            'require_raw_material_mapping'=>'required',
            'fitting_item'=>'required'],
        ],
        [
            'item_name.required' => 'Please Enter Item',            
            'item_name.unique'   => 'The Item  Name Has Already Been Taken', 
            'item_name.max' => 'Maximum 255 Characters Allowed',
            'item_group_id.required' => 'Please Enter  Item',            
            'item_code.required' => 'Please Select Item Code',        
            'require_raw_material_mapping.required' => 'Please Select Item Mapping',
            'fitting_item.required' => 'Please Select Fitting Mapping'         
        ]);
        DB::beginTransaction();
        $locationID = getCurrentLocation()->id;


        $item_sequence = Str::substr( $request->item_code, -5); 

          $isFound = Item::where('item_sequence',$item_sequence)->where('item_group_id',$request->item_group_id)->where('id','!=',$request->id)->lockForUpdate()->first();
        // $isFound = Item::where('item_sequence',$item_sequence)->where('item_group_id',$request->item_group_id)->first();

        if($isFound != null){
            $item_sequence++;

            $item_data = ItemGroup::select('item_group_code')->where('id', '=', $request->item_group_id)->first();

            $middle_num = str_pad($item_sequence, 5, "0", STR_PAD_LEFT);            
            
            $item_code = $item_data->item_group_code.$middle_num;

        }else{
            $item_code = $request->item_code;
        }

        $planDispatchData = $request->print_dispatch_plan == "" ? "No" : $request->print_dispatch_plan;
        $own_manu = $request->own_manufacturing == "" ? "No" : $request->own_manufacturing;
        $dont_allow_req_msl = $request->dont_allow_req_msl == "" ? "No" : $request->dont_allow_req_msl;
        $service_item = $request->service_item == "" ? "No" : $request->service_item;
       $show_item_in_print = $request->require_raw_material_mapping == "No" ? NULL : $request->show_item_in_print;


        try{
            $itemData=  Item::where('id','=',$request->id)->update([
                'item_name' => $request->item_name,
                // 'item_sequence' => $request->item_sequence,
                'item_group_id' => $request->item_group_id,
                'item_sequence' => $item_sequence,
                'item_code' => $item_code,
                'unit_id' => $request->unit_id,
                'min_stock_qty' => $request->min_stock_qty,
                'max_stock_qty' => $request->max_stock_qty,
                're_order_qty' => $request->re_order_qty,
                'hsn_code' => $request->hsn_code_id,
                'rate_per_unit' => $request->rate_per_unit,
                'require_raw_material_mapping' => $request->require_raw_material_mapping,
                'fitting_item' => $request->fitting_item,
                'print_dispatch_plan' =>  $planDispatchData ,
                'secondary_unit' =>   $request->secondary_unit,
                'qc_required' =>   $request->qc_required,
               'show_item_in_print' =>   $show_item_in_print ,
                // 'qty' =>   $request->secondary_unit == "Yes" ? $request->qty != "" ? $request->qty : 1 : 1,
                'qty' =>   $request->secondary_unit == "Yes" ? null : 1,
                'second_unit' =>   $request->secondary_unit == "Yes" ? $request->second_unit : null,
                'own_manufacturing' =>  $own_manu ,
                'dont_allow_req_msl' =>   $dont_allow_req_msl ,
                'service_item' =>   $service_item ,
                'status' =>   $request->status ,
                'allow_partial_dispatch' =>   $request->allow_partial_dispatch ,
                'wt_pc' =>   $request->wt_pc,
                'company_id' => Auth::user()->company_id,
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id
            ]);
                
            if($itemData){

                if($request->require_raw_material_mapping == 'No' && $request->require_raw_material_mapping != $request->old_require_raw_material_mapping){
                    ItemRawMaterialMappingDetail::where('item_id',$request->id)->delete();               
                }

                $item_data =  ItemDetails::where('item_id',$request->id)->update([
                    'status' => 'D',
                ]);


                if($request->secondary_unit == "Yes"){

                     if(isset($request->item_details_id) && !empty($request->item_details_id)){
                        foreach($request->item_details_id as $ctKey => $ctVal){
                            if($ctVal == '0'){
                                $itemDetail = ItemDetails::create([
                                    'item_id' => $request->id,
                                    'secondary_qty' => $request->secondary_qty[$ctKey],
                                    'secondary_wt_pc' => $request->secondary_wt_pc[$ctKey],
                                    'secondary_item_name' => $request->secondary_item_name[$ctKey],
                                    'status' => 'Y',
                                ]);  
                            }else{
                                $itemDetail =  ItemDetails::where('item_id',$request->id)->where('item_details_id','=',$ctVal)->update([
                                    'item_id' => $request->id,
                                    'secondary_qty' => $request->secondary_qty[$ctKey],
                                    'secondary_wt_pc' => $request->secondary_wt_pc[$ctKey],
                                    'secondary_item_name' => $request->secondary_item_name[$ctKey],
                                    'status' => 'Y',
                                ]);

                            }                           
                        }                      
                    }

                }

                $itmDetails = ItemDetails::where('item_id',$request->id)->where('status','D')->delete();
                
                // $oldDetails = ItemDetails::where('item_id','=',$request->id)->get();
    
                // $getItemId = Item::where('id', $request->id)->pluck('id')->first();

                // $oldDetailsData = [];
                // if($oldDetails != null){
                //     $oldDetailsData = $oldDetails->toArray();
                // }


                // $contactDetails = $request->only('contacts');
                // $contactDetails['contacts'] = json_decode($contactDetails['contacts'],true);
                
                // if(isset($oldDetailsData) && !empty($oldDetailsData)){
                    
                //         foreach($oldDetailsData as $oldCtKey => $oldCtVal){
        
                //             if(isset($contactDetails['contacts'][$oldCtKey]) && $contactDetails['contacts'][$oldCtKey] != null){
                            
                //                 $contact_data_updated = ItemDetails::where('item_id','=',$request->id)->where('id','=',$oldCtVal['id'])->update([

                //                     'item_detail_name' => isset($contactDetails['contacts'][$oldCtKey]['item_detail_name']) ? $contactDetails['contacts'][$oldCtKey]['item_detail_name'] : "",
                //                 ]);
                //                 unset($oldDetailsData[$oldCtKey]); 
                //                 unset($contactDetails['contacts'][$oldCtKey]);
                //             }
                //         }
                //         if(isset($oldDetailsData) && !empty($oldDetailsData)){
                //             foreach($oldDetailsData as $oldCtKey => $oldCtVal){
                //                 ItemDetails ::where('id','=',$oldCtVal['id'])->delete();
                //             }
                //         }
                // }
             
                // if(isset($contactDetails['contacts']) && !empty($contactDetails['contacts'])){
                //     foreach($contactDetails['contacts'] as $ctKey => $ctVal){     
                       
                //         if($ctVal != null){
                //             $contact_data=  ItemDetails::create([
                //                 'item_id' =>  $getItemId,
                //                 'item_detail_name' => isset($ctVal['item_detail_name']) ? $ctVal['item_detail_name'] : "",
                               
                //             ]);
                //         }
                //     }
                // } 

                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Updated',
                ]);
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    public function existsItem(Request $request){
        
        if($request->term != ""){
            $fdItem = Item::select('item_name')->where('item_name', 'LIKE', $request->term.'%')->groupBy('item_name')->get();
            
            if($fdItem != null){
                
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdItem as $dsKey){

                    $output .= '<li parent-id="item_name" list-id="item_list" class="list-group-item" tabindex="0">'.$dsKey->item_name.'</li>';
                } 
                $output .= '</ul>';
                
                return response()->json([
                    'itemList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Item available',
                    'response_code' => 0,
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{    

                // stock Decrease

                $src_item =  SupplierRejectoionDetails::where('item_id',$request->id)->get();
                if($src_item->isNotEmpty()){	
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In  Supplier Return Challan.",
                    ]);
                }

                $issue_item = ItemIssueDetail::where('item_id',$request->id)->get();
                if($issue_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Item Issue Slip.",
                    ]);
                }

                $item_ass_prod_details =  ItemAssemblyProductionDetails::where('item_id',$request->id)->get();
                if($item_ass_prod_details->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Item Assembly Production.",
                    ]);
                }

                $item_ass_prod =  ItemAssemblyProduction::where('item_id',$request->id)->get();
		        if($item_ass_prod->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Item Assembly Production.",
                    ]);
                }

                $qc_item = QCApproval::where('item_id',$request->id)->get();
                if($qc_item->isNotEmpty()){	

                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In QC Approval .",
                    ]);
                }


                $grn_item = GRNMaterialDetails::where('item_id',$request->id)->get();
                if($grn_item->isNotEmpty()){                
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In GRN.",
                    ]);   
                }
                  

                $loading_item = LoadingEntryDetails::
                leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
                ->where('dispatch_plan_details.item_id',$request->id)->get();
                if($loading_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Loading Entry.",
                    ]);
                }

                $dipatch_item = DispatchPlanDetails::where('item_id',$request->id)->get();
                if($dipatch_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Dispatch Plan.",
                    ]);
                }
                
                
              
              
            
                
               // stock Increase
                $replace_item = ReplacementItemDecisionDetails::where('item_id',$request->id)->get();

                if($replace_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Replacement Item Decision.",
                    ]);
                }
                $So_item = SOMapping::where('item_id',$request->id)->get();

                if($So_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                       'response_message' => "You Can't Delete, Item Is Used In Customer Replacement SO Mapping.",
                    ]);
                }

                $so_short_item = SOShortClose::
                leftJoin('sales_order_details','sales_order_details.so_details_id','=','so_short_close.so_details_id')
                ->where('sales_order_details.item_id',$request->id)->get();

                if($so_short_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                       'response_message' => "You Can't Delete, Item Is Used In Customer Replacement SO Short Close.",
                    ]);
                }

                $cre_item = CustomerReplacementEntryDetails::where('item_id',$request->id)->get();

                if($cre_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                       'response_message' => "You Can't Delete, Item Is Used In Customer Replacement Entry.",
                    ]);
                }


               

                $item_return = ItemReturnDetail::where('item_id',$request->id)->get();
                if($item_return->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Item Return.",
                    ]);
                }
                
                $item_production =  ItemProductionDetail::where('item_id',$request->id)->get();
                if($item_production->isNotEmpty()){ 
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Item Production.",
                    ]);
                }     
                
              
            
            
              

          

                $so_item = SalesOrderDetail::where('item_id',$request->id)->get();
                if($so_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In SO.",
                    ]);
                }

                $sod_item = SalesOrderDetailsDetails::where('item_id',$request->id)->get();
                if($sod_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In SO.",
                    ]);
                }


                $material_item = MaterialRequestDetail::where('item_id',$request->id)->get();
                if($material_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Material Request.",
                    ]);
                }
		

                $po_approve_item = PurchaseOrderDetails::where('item_id',$request->id)->get();

                if($po_approve_item->isNotEmpty()){
                    foreach($po_approve_item as $key=>$val){
                        $isApproeve = PurchaseOrder::where('po_id',$val['po_id'])->where('is_approved','=','1')->first();

                        if($isApproeve){
                            return response()->json([
                                'response_code' => '0',
                                'response_message' => "You Can't Delete, Item Is Used In PO Approval.",
                            ]);
                        }
                    }
                }
    
                $po_item = PurchaseOrderDetails::where('item_id',$request->id)->get();
                if($po_item->isNotEmpty()){	

                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In PO.",
                    ]);
                }

                $pr_short_item = PRShortClose::
                leftJoin('purchase_requisition_details','purchase_requisition_details.pr_details_id','=','purchase_requisition_short_close.pr_details_id')
                ->where('purchase_requisition_details.item_id',$request->id)->get();

                if($pr_short_item->isNotEmpty()){
                    return response()->json([
                        'response_code' => '0',
                       'response_message' => "You Can't Delete, Item Is Used In Purchase Requisition Short Close.",
                    ]);
                }

                $pr_item = PurchaseRequisitionDetails::where('item_id',$request->id)->get();
                if($pr_item->isNotEmpty()){	

                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Purchase Requisition .",
                    ]);
                }

              

                $supp_item_mapping = SupplierItemMapping::where('item_id',$request->id)->get();
                if($supp_item_mapping->isNotEmpty()){                

                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Supplier Item Mapping.",
                    ]);
                }

                $item_to_mapping =  ItemRawMaterialMappingDetail::where('item_id',$request->id)->orWhere('raw_material_id',$request->id)->get();
                if($item_to_mapping->isNotEmpty()){	

                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Item to Item Mapping.",
                    ]);
                }

                $item_stock_report =  LocationStock::where('item_id',$request->id)->get();
                if($item_stock_report->isNotEmpty()){	

                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Item Is Used In Item Stock Report.",
                    ]);
                }

                $stockDetail =  ItemDetails::where('item_id',$request->id)->get();

                if($stockDetail->isNotEmpty()){
                    foreach($stockDetail as $skey=>$sval){
                        LocationDetailStock::where('item_details_id',$sval->item_details_id)->delete();
                    }
                }

                ItemDetails::where('item_id',$request->id)->delete();

                Item::destroy($request->id);
                    DB::commit();
                    return response()->json([
                        'response_code' => '1',
                        'response_message' => 'Record Deleted Successfully.',
                    ]);
                
                // $item = ItemRawMaterialMappingDetail::where('item_id',$request->id)->orWhere('raw_material_id',$request->id)->first();
                // // $sales = SalesOrderDetail::where('item_id',$request->id)->first();
                
                // if($item != '' || $item != null){
                //     // dd('here');
                //     return response()->json([
                //         'response_code' => '1',
                //         'response_message' => "This Is Used Somewhere, You Can't Delete.",
                //     ]);
                // }
                // // else if($sales != '' || $sales != null){
                // //     return response()->json([
                // //         'response_code' => '1',
                // //         'response_message' => "This Is Used Somewhere, You Can't Delete.",
                // //     ]);
                // // }
                // else{
                //     Item::destroy($request->id);
                //     DB::commit();
                //     return response()->json([
                //         'response_code' => '1',
                //         'response_message' => 'Record Deleted Successfully.',
                //     ]);
                // }
        }catch(\Exception $e){  
            DB::rollBack();      
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

    public function exportCountry(Request $request){
        return Excel::download(new ExportCountry, 'country.xlsx');
    }

   

    public function getItemCode(Request $request){


        $hiddenId = isset($request->hiddenid) ? $request->hiddenid : "";     
        
        $getItem =  Item::where('id', '=', $hiddenId)->where('item_group_id', $request->id)->pluck('item_code')->first();
        if($hiddenId && $getItem != "")
        {
            $item =  Item::where('id', '=', $hiddenId)->pluck('item_code')->first();

            $isFound =  Item::where('id', '=', $hiddenId)->pluck('item_sequence')->first();
        }
        else{
        $item_data = ItemGroup::select(['id','item_group_code','item_group_name'])->where('id', '=', $request->id)->first();

            $isFound = Item::where('item_group_id', '=', $request->id)->max('item_sequence');
            
          //  $isFound = Item::where('item_group_id', '=', $request->id)->latest('item_sequence')->first();
          
            
            
            
            if ($isFound != null) {
                $isFound++;
            } else {
                $isFound = 1;
            }
    
            $middle_num = str_pad($isFound, 5, "0", STR_PAD_LEFT);
            
            
            $item = $item_data->item_group_code.' '.$middle_num;
            // $item = $item_data->item_group_code.' '.$middle_num;
        }




        return response()->json([
            'response_code' => 1,
            'item_data' => $item,
            'number' => $isFound
        ]);
    }

    public function exportItem(Request $request)
    {
        return Excel::download(new exportItem, 'Item.xlsx');
       
    }
    
    public function updatedItemCode()
    {
        $item_groups = ItemGroup::select('id', 'item_group_code')->get();
        foreach ($item_groups as $group) {     
            // $items = Item::where('item_group_id', $group->id)->orderBy('item_name', 'asc')->get();                          
            $items = Item::where('item_group_id', $group->id)->orderBy('id', 'asc')->get();                          
            foreach ($items as $key => $item) {                
                $sequence_number = $key + 1;                
                $formatted_sequence = str_pad($sequence_number, 5, "0", STR_PAD_LEFT);                
                $new_item_code = $group->item_group_code.''.$formatted_sequence;    
              

                $item->update([
                    'item_code' => $new_item_code,
                    'item_sequence' => $sequence_number,
                ]);
            }
        }
        return redirect()->route('manage-item')->with('success', 'Item Code Updated Successfully.');       
    }

}



    // Extra Code

     // public function getItemCode(Request $request){
        
    //     $item_data = ItemGroup::select(['id','item_group_code','item_group_name'])->where('id', '=', $request->id)->first();
    //     // dd($item_data->item_group_name);

    //     $isFound = Item::where('item_group_id', '=', $request->id)->max('item_sequence');

    //     // dd($isFound);
    //     if ($isFound != null) {
    //         $isFound++;
    //     } else {
    //         $isFound = 1;
    //     }

    //     $middle_num = str_pad($isFound, 5, "0", STR_PAD_LEFT);

    //     // $postfix = $year_data->yearcode;

    //     $item = $item_data->item_group_code.' '.$middle_num;

    //     // dd($item);

    //     // $order_acceptance_num_format = $item_data->itme_group_name . '/OA/' . $middle_num . '/' . $postfix;

    //     // $order_acceptance_num_format = $item_data->itme_group_name . '/AI/'.$item_data->group_id;

    //     return response()->json([
    //         'response_code' => 1,
    //         'item_data' => $item,
    //         'number' => $isFound
    //     ]);
    // }

     // $myFile =  Excel::raw(new exportItem($request->all()), 'Xlsx');
        // $response =  array(
        // 'name' => "item.xlsx",
        // 'file' => "data:application/vnd.ms-excel;base64,".base64_encode($myFile)
        //  );
        // return response()->json($response);