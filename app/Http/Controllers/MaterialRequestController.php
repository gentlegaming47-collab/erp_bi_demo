<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;
use App\Models\SalesOrderDetail;
use App\Models\SalesOrder;
use App\Models\PriceListDetails;
use App\Models\PriceList;
use App\Models\Item;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionDetails;
use App\Models\SupplierItemMapping;

class MaterialRequestController extends Controller
{
    public function manage()
    {
        return view('manage.manage-material_request');
    }


   
    public function index(MaterialRequest $materialRequest,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();
 
        // $materialRequest = MaterialRequest::select(['mr_number', 'mr_sequence', 'mr_date', 'items.item_name', 'items.item_code', 'material_request_details.mr_qty', 'special_notes', 'material_request_details.remarks', 'material_request.sm_user_id','material_request.zsm_user_id','material_request.md_user_id',
 
        // 'locations.location_name','material_request.created_by_user_id','material_request.last_by_user_id','material_request.created_on','material_request.last_on','material_request.mr_id', 'item_groups.item_group_name'])

        // ->leftJoin('material_request_details','material_request_details.mr_id','=','material_request.mr_id')

        // ->leftJoin('locations','locations.id','=','material_request.to_location_id')
        // ->leftJoin('items','items.id','=','material_request_details.item_id')
        // ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        // ->where('material_request.year_id','=',$year_data->id)
        // ->where('material_request.current_location_id','=',$location->id);

        $materialRequest = MaterialRequest::select(['mr_number', 'mr_sequence', 'mr_date', 'items.item_name', 'items.item_code', 'material_request_details.mr_qty', 'special_notes', 'material_request_details.remarks', 'material_request.sm_user_id','material_request.zsm_user_id','material_request.md_user_id',
 
        'locations.location_name','material_request.created_by_user_id','material_request.last_by_user_id','material_request.created_on','material_request.last_on','material_request.mr_id', 'item_groups.item_group_name','customer_groups.customer_group_name','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])

        ->leftJoin('material_request_details','material_request_details.mr_id','=','material_request.mr_id')

        ->leftJoin('locations','locations.id','=','material_request.to_location_id')
        ->leftJoin('items','items.id','=','material_request_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        ->leftJoin('customer_groups','customer_groups.id', 'material_request.customer_group_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'material_request.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'material_request.last_by_user_id')
        ->where('material_request.year_id','=',$year_data->id)
        ->where('material_request.current_location_id','=',$location->id)
        ->groupBy('material_request.mr_number');
 
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


            $materialRequest->whereDate('material_request.mr_date','>=',$from);

            $materialRequest->whereDate('material_request.mr_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $materialRequest->where('material_request.mr_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $materialRequest->where('material_request.mr_date','<=',$to);

        }  

       return DataTables::of($materialRequest)
 
 
        ->editColumn('mr_date', function($materialRequest){
 
            if ($materialRequest->mr_date != null) {
 
                $formatedDate3 = Date::createFromFormat('Y-m-d', $materialRequest->mr_date)->format(DATE_FORMAT); return $formatedDate3;
 
            }else{
 
                return '';
 
            }
 
        })
        ->filterColumn('material_request.mr_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(material_request.mr_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->editColumn('mr_qty', function($materialRequest){

            return $materialRequest->mr_qty > 0 ? number_format((float)$materialRequest->mr_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
 
     
 
        ->editColumn('sm_approval', function($materialRequest){
            if($materialRequest->sm_user_id != null){
                $sm = "Approved";
            }else{
                $sm = "";
            }
            return $sm;
        })
        ->editColumn('zsm_approval', function($materialRequest){
            if($materialRequest->zsm_user_id != null){
                $zsm = "Approved";
            }else{
                $zsm = "";
            }
            return $zsm;
        })
        ->editColumn('md_approval', function($materialRequest){
            if($materialRequest->md_user_id != null){
                $md = "Approved";
            }else{
                $md = "";
            }
            return $md;
        })

        ->editColumn('location_name', function($materialRequest){
 
            return $materialRequest->location_name > 0 ? $materialRequest->location_name : 0;
 
        })

        ->editColumn('item_name', function($materialRequest){ 
            if($materialRequest->item_name != ''){
                $item_name = ucfirst($materialRequest->item_name);
                return $item_name;
            }else{
                return '';
            }
        })
 
 
        ->editColumn('created_by_user_id', function($materialRequest){
            if($materialRequest->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$materialRequest->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($materialRequest){
            if($materialRequest->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$materialRequest->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
 
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('created_on', function($materialRequest){
            if ($materialRequest->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $materialRequest->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('material_request.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(material_request.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($materialRequest){
            if ($materialRequest->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $materialRequest->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('material_request.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(material_request.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
 
        ->addColumn('options',function($materialRequest){
            $action = "<div>";        
            if(hasAccess("material_request","print")){
            $action .="<a id='print_a' target='_blank' href='".route('print-material_request',['id' => base64_encode($materialRequest->mr_id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
            }
            if(hasAccess("material_request","edit")){
            $action .="<a id='edit_a' href='".route('edit-material_request',['id' => base64_encode($materialRequest->mr_id )]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("material_request","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options','sm_approval','zsm_approval','md_approval'])
        ->make(true);
    }


    public function create()
    {
        return view('add.add-material_request');
    }

    public function store(Request $request)
    {
        $locationID = getCurrentLocation()->id;
        $year_data = getCurrentYearData();
   
        DB::beginTransaction();

        $existNumber = MaterialRequest::where([
            ['mr_sequence',  $request->mr_sequence],
            ['mr_number',$request->mr_number],
            ['year_id',$year_data->id],
            ['current_location_id',$locationID],
        ])->lockForUpdate()->first();
        
        
        //  $existNumber = MaterialRequest::where('mr_sequence', $request->mr_sequence)->where('mr_number', $request->mr_number)->first();    
      
         if($existNumber){
             $latestNo = $this->getLatestMaterialNo($request);              
             $tmp =  $latestNo->getContent();
             $area = json_decode($tmp, true);
             $mr_number =   $area['latest_po_no'];
             $mr_sequence = $area['number'];
         }else{
            $mr_number = $request->mr_number;
            $mr_sequence = $request->mr_sequence;
         }
      
         try{
 
          
          
             $totalQty = 0;
             $totalAmount = 0;

             foreach ($request->item_id as $ctKey => $ctVal) {
                if ($ctVal != null) {
                    $totalQty += $request->mr_qty[$ctKey];  
                    $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";        
                }
            }
                
             $materialRequest =  MaterialRequest::create([                 
                 'current_location_id' => $locationID,
                 'mr_sequence'        => $mr_sequence,
                 'mr_number'          => $mr_number,
                 'mr_date'            => Date::createFromFormat('d/m/Y', $request->mr_date)->format('Y-m-d'),                 
                 'to_location_id'     => $request->to_location_id,                
                 'customer_group_id'     => $request->customer_group_id,                
                 'total_qty'       => $totalQty,                
                 'special_notes'      => $request->special_notes,
                 'year_id'            => $year_data->id,
                 'company_id'         => Auth::user()->company_id,
                 'created_by_user_id' => Auth::user()->id,
                 'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString()          ]);
              
             if($materialRequest->save())
             {
                 
                 
                 foreach($request->item_id as $spKey => $spVal)
                 {

                    $rate_unit = PriceListDetails::select('sales_rate')->where('customer_group_id',$request->customer_group_id)->where('item_id',$request->item_id[$spKey])->first();
      
                     $materialRequestDetail = MaterialRequestDetail::create([
                         'mr_id'    => $materialRequest->mr_id,
                         'item_id'   => isset($request->item_id[$spKey]) ? $request->item_id[$spKey] : "",
                         'mr_qty'   => isset($request->mr_qty[$spKey]) ? $request->mr_qty[$spKey] : "",                         
                         'rate_unit'   => isset($rate_unit) ? $rate_unit->sales_rate : '',                         
                         'remarks'   => isset($request->remarks[$spKey]) ? $request->remarks[$spKey] : "",
                         'status' => 'Y',
                     ]);
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
             
             DB::rollBack();
             getActivityLogs("Material Request", "add", $e->getMessage(),$e->getLine());
             return response()->json([
                 'response_code' => '0',
                 'response_message' => 'Error Occured Record Not Inserted',
                 'original_error' => $e->getMessage()
             ]);
         }
         
    }

    public function show($id)
    {
        return view('edit.edit-material_request', compact('id'));
    }

    public function edit($id)
    {
        $isAnyPartInUse = false;
        $location = getCurrentLocation();

        
        // $materialRequest = MaterialRequest::where('mr_id', $id)->first();
        $materialRequest = MaterialRequest::select('material_request.mr_id','material_request.current_location_id','material_request.to_location_id','material_request.mr_sequence','material_request.mr_number','material_request.mr_date','material_request.customer_group_id','material_request.special_notes')->where('mr_id', $id)->first();
        $materialRequest->mr_date = Date::createFromFormat('Y-m-d', $materialRequest->mr_date)->format('d/m/Y');

        $materialRequestDetails = MaterialRequestDetail::select(['material_request_details.mr_details_id','material_request_details.mr_id', 'material_request_details.mr_qty','material_request_details.item_id','items.item_code', 'units.unit_name', 'item_groups.item_group_name','material_request_details.remarks',
        // 'location_stock.stock_qty',
        DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE material_request_details.item_id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),    
        ])
        ->leftJoin('items', 'items.id', 'material_request_details.item_id')       
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')       
        ->leftJoin('units', 'units.id', 'items.unit_id')
        // ->leftJoin('location_stock', 'location_stock.item_id','=', 'material_request_details.item_id')
        // ->Where('location_stock.location_id','=',$location->id)
        ->where('material_request_details.mr_id','=',$id)->get();

        if($materialRequestDetails != null){
            $materialRequestDetails->each(function ($item) use (&$isAnyPartInUse,$id) {
                $total_so_qty = SalesOrderDetail::where('mr_details_id', '=', $item->mr_details_id)->sum('so_qty');

                $mrapprove = MaterialRequest::select('material_request.sm_user_id')->where('mr_id', $id)->first();


                if($total_so_qty != null && $total_so_qty > 0 || $mrapprove->sm_user_id != null){
                    $item->in_use = true;
                    $item->used_qty = $total_so_qty;
                    $isAnyPartInUse = true;
                } else {
                    $item->in_use = false;
                    $item->used_qty = 0;
                }
            });
        }

        if($materialRequest){
            $materialRequest->in_use = false;
            if($isAnyPartInUse == true){
                $materialRequest->in_use = true;
            }
        }

        if ($materialRequestDetails) {
            return response()->json([
                'materialRequestDetails' => $materialRequestDetails,
                'materialRequest' => $materialRequest,
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
                 'mr_sequence' => ['required','max:155',Rule::unique('material_request')->where(function ($query) use ($request,$year_data, $locationID) {
                     return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                 })->ignore($request->id, 'mr_id')],
 
                 'mr_number' => ['required', 'max:155', Rule::unique('material_request')->where(function ($query) use ($request, $year_data, $locationID) {
                     return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                 })->ignore($request->id, 'mr_id')],              
             ],
             [
                 'mr_sequence.unique'=>'MR. Number Is Already Exists',    
                 'mr_number.required' => 'Please Enter MR Number',
                 'mr_number.max' => 'Maximum 155 Characters Allowed',
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
                         $totalQty += $request->mr_qty[$ctKey];                      
                     }
                 }
             }
 
            
             $materialRequest =  MaterialRequest::where('mr_id','=',$request->id)->update([             
                'current_location_id' => $locationID,
                'mr_sequence'        => $request->mr_sequence,
                'mr_number'          => $request->mr_number,
                'mr_date'            => Date::createFromFormat('d/m/Y', $request->mr_date)->format('Y-m-d'),                 
                'to_location_id'     => $request->to_location_id, 
                'customer_group_id'     => $request->customer_group_id,                      
                'total_qty'       => $totalQty,                
                'special_notes'      => $request->special_notes,
                 'year_id'               => $year_data->id,
                 'company_id'            => Auth::user()->company_id,
                 'last_by_user_id'       => Auth::user()->id,
                 'last_on'               => Carbon::now('Asia/Kolkata')->toDateTimeString()               
             ]);
 
 
             if($materialRequest)
             {
                 if (isset($request->mr_details_id) && !empty($request->mr_details_id)) {
                    $materialDtails =  MaterialRequestDetail::where('mr_id',$request->id)->update([
                        'status' => 'D',
                    ]);

                     
                    foreach ($request->mr_details_id as $sodKey => $sodVal) {                     
                        
                        if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){  
                            $rate_unit = PriceListDetails::select('sales_rate')->where('customer_group_id',$request->customer_group_id)->where('item_id',$request->item_id[$sodKey])->first();
                        }else{
                            $rate_unit = '';
                        }
                      
                         
                         if($sodVal == "0"){                                    
                             if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
 
                                 
                                 $materialDtais=   MaterialRequestDetail::create([
                                     'mr_id'    => $request->id,
                                     'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                     'mr_qty'   => isset($request->mr_qty[$sodKey]) ? $request->mr_qty[$sodKey] : "",
                                     'rate_unit'   => isset($rate_unit) ? $rate_unit->sales_rate : '',    
                                     'remarks'   => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : "",       
                                    'status' => 'Y',                          
                                 ]);
                             }
                             }else{     
                                 
                                 if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){                                    
                                     $materialDtais =  MaterialRequestDetail::where('mr_details_id',$sodVal)->update([
                                         'mr_id'    => $request->id,
                                         'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                         'mr_qty'   => isset($request->mr_qty[$sodKey]) ? $request->mr_qty[$sodKey] : "",
                                         'rate_unit'   => isset($rate_unit) ? $rate_unit->sales_rate : '',   
                                         'remarks'   => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : "",    
                                           'status' => 'Y',                                   
                                     ]);
                                 }
                                //  else{                                    
                                //      MaterialRequestDetail::where('mr_details_id', $sodVal)->delete();
                                //  }
                             }
                    }
                    
                    $prDetails = MaterialRequestDetail::where('mr_id',$request->id)->where('status','D')->delete();
 
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
           //  dd($e->getMessage() . $e->getLine());
             DB::rollBack();
             getActivityLogs("Material Request", "update", $e->getMessage(),$e->getLine());
             return response()->json([
                 'response_code' => '0',
                 'response_message' => 'Error Occured Record Not Inserted',
                 'original_error' => $e->getMessage()
             ]);
         }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{

            $so_data = SalesOrderDetail::
            leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
            ->where('material_request_details.mr_id',$request->id)->get();
            if($so_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Material Request Is Used In SO.",
                ]);
            }


            $mrData = MaterialRequest::where('mr_id','=',$request->id)->where('sm_user_id','!=',null)->first();

            if($mrData != null){
                return response()->json([
                    'response_code' => '0',
                    // 'response_message' => "This Is Used Somewhere, You Can't Delete",
                    'response_message' => "You Can't Delete, Material Request Is Approved.",
                ]);

            }  


            MaterialRequestDetail::where('mr_id',$request->id)->delete();
            MaterialRequest::destroy($request->id);
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            getActivityLogs("Material Request", "delete", $e->getMessage(),$e->getLine());
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
                // $error_msg = "You Can't Delete, Material Request Used In Sales Order.";
            }else{
                $error_msg = "Record Not Deleted";
            }
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }

    public function getLatestMaterialNo(Request $request)
    {
          $modal  =  MaterialRequest::class;
          $sequence = 'mr_sequence';
          $prefix = 'MR';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $sup_num_format['format'],
            'number'        => $sup_num_format['isFound'],
            'location'      => $locationName
        ]);

    }


    public function getPendingMaterialRequest(Request $request){
        
        // $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();

        $locationCode = getCurrentLocation();

        $so_mr_detail_id = SalesOrderDetail::select(['sales_order_details.mr_details_id'])
        ->whereNotNull('sales_order_details.mr_details_id')
        ->get();    
       


        if(isset($request->id)){

            // $edit_mrData = SalesOrderDetail::select(['material_request_details.mr_details_id','material_request_details.mr_qty','material_request.mr_number','material_request.mr_date',  'items.item_name', 'items.id as ItemID', 'items.item_code', 'item_groups.item_group_name', 'units.unit_name',  'sales_order_details.so_qty as pending_material_qty' 
            //  ])
            $edit_mrData = SalesOrderDetail::select(['material_request.mr_number','material_request.mr_date', 
            'material_request.mr_id' ])
            ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
            ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
            ->leftJoin('items', 'items.id', 'sales_order_details.item_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            ->leftJoin('units', 'units.id', 'items.unit_id')
            ->where('sales_order_details.so_id',$request->id)
            ->get();
        }

        


        if($so_mr_detail_id != null){           

            $mrData = MaterialRequestDetail:: select(['material_request.mr_number','material_request.mr_date', 
            'material_request.mr_id' 
            ]) 
            ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')  
            ->leftJoin('locations','locations.id','=','material_request.current_location_id')   
            ->whereIn('material_request.approval_type_id_fix',['4','5']) 
            ->where('material_request_details.form_type','=','SO') 
            ->where('material_request.current_location_id','=',$request->location_id)
            ->where('material_request.to_location_id','=',$locationCode->id)
            ->whereNotIn('material_request_details.mr_details_id',$so_mr_detail_id)    
            // ->where('material_request.year_id','=',$year_data->id)   
            ->whereIN('material_request.year_id',$yearIds)   
            ->groupBy('material_request.mr_id')       
            ->get();

           
        }


        if(isset($edit_mrData)){
            $data = collect($mrData)->merge($edit_mrData);
            $grouped = $data->groupBy('mr_id');    
            

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pending_material_qty += (float) $item->pending_material_qty;
                    return $carry;
                });
            });
    
            $mrData = $merged->values();   

        }

        $mrData = $mrData->sortBy('mr_id')->values();

        if ($mrData != null) {
            foreach ($mrData as $cpKey => $cpVal) {
                if ($cpVal->mr_date != null) {
                    $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
                }
                if ($cpVal->pending_material_qty != null) {
                    $cpVal->pending_material_qty = $cpVal->pending_material_qty > 0 ? $cpVal->pending_material_qty : 0 ;
                }
               
            }
        }
        // dd($mrData);
        if ($mrData != null) {
            return response()->json([
                'mrData' => $mrData,
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

    public function getPendingMaterialRequestPR(Request $request){
        
        // $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();

        $locationCode = getCurrentLocation();

        $pr_mr_detail_id = PurchaseRequisitionDetails::select(['purchase_requisition_details.mr_details_id'])
        ->whereNotNull('purchase_requisition_details.mr_details_id')
        ->get();    
       


        if(isset($request->id)){

            
            $edit_mrData = PurchaseRequisitionDetails::select(['material_request.mr_number','material_request.mr_date', 
            'material_request.mr_id' ])
            ->leftJoin('material_request_details','material_request_details.mr_details_id','=','purchase_requisition_details.mr_details_id')
            ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
            ->leftJoin('items', 'items.id', 'purchase_requisition_details.item_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            ->leftJoin('units', 'units.id', 'items.unit_id')
            ->where('purchase_requisition_details.pr_id',$request->id)
            ->get();
        }

        


        if($pr_mr_detail_id != null){           

            $mrData = MaterialRequestDetail:: select(['material_request.mr_number','material_request.mr_date', 
            'material_request.mr_id' 
            ]) 
            ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')  
            ->leftJoin('locations','locations.id','=','material_request.current_location_id')   
            ->whereIn('material_request.approval_type_id_fix',['4','5']) 
            ->where('material_request_details.form_type','=','PO') 
            ->where('material_request.current_location_id','=',$request->location_id)
            ->where('material_request.to_location_id','=',$locationCode->id)
            ->whereNotIn('material_request_details.mr_details_id',$pr_mr_detail_id)    
            // ->where('material_request.year_id','=',$year_data->id)   
            ->whereIN('material_request.year_id',$yearIds)   
            ->groupBy('material_request.mr_id')       
            ->get();

           
        }


        if(isset($edit_mrData)){
            $data = collect($mrData)->merge($edit_mrData);
            $grouped = $data->groupBy('mr_id');    
            

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pending_material_qty += (float) $item->pending_material_qty;
                    return $carry;
                });
            });
    
            $mrData = $merged->values();   

        }

        $mrData = $mrData->sortBy('mr_id')->values();

        if ($mrData != null) {
            foreach ($mrData as $cpKey => $cpVal) {
                if ($cpVal->mr_date != null) {
                    $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
                }
                if ($cpVal->pending_material_qty != null) {
                    $cpVal->pending_material_qty = $cpVal->pending_material_qty > 0 ? $cpVal->pending_material_qty : 0 ;
                }
               
            }
        }
        // dd($mrData);
        if ($mrData != null) {
            return response()->json([
                'mrData' => $mrData,
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
    // public function getPendingMaterialRequest(Request $request){
        
    //     $year_data = getCurrentYearData();


    //     if(isset($request->id)){

    //         $edit_mrData = SalesOrderDetail::select(['material_request_details.mr_details_id','material_request_details.mr_qty','material_request.mr_number','material_request.mr_date',  'items.item_name', 'items.id as ItemID', 'items.item_code', 'item_groups.item_group_name', 'units.unit_name',  'sales_order_details.so_qty as pending_material_qty' 
    //          ])
    //         ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
    //         ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
    //         ->leftJoin('items', 'items.id', 'sales_order_details.item_id')
    //         ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
    //         ->leftJoin('units', 'units.id', 'items.unit_id')
    //         ->where('sales_order_details.so_id',$request->id)
    //         ->get();
    //     }

    //     $mrData = MaterialRequest::select(['material_request_details.mr_details_id','material_request_details.mr_qty','material_request.mr_number','material_request.mr_date',  'items.item_name', 'items.id as ItemID', 'items.item_code', 'item_groups.item_group_name', 'units.unit_name',    
    //     'sales_order_details.rate_per_unit',
    //     DB::raw("(material_request_details.mr_qty - (SELECT IFNULL(SUM(sales_order_details.so_qty),0)   FROM sales_order_details WHERE mr_details_id  = material_request_details.mr_details_id )) as pending_material_qty"),  
    //     ])
    //     ->leftJoin('material_request_details','material_request_details.mr_id','=','material_request.mr_id')      
    //     ->leftJoin('items','items.id','=','material_request_details.item_id')      
    //     ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')      
    //     ->leftJoin('units','units.id','=','items.unit_id')            
    //     ->leftJoin('sales_order_details', 'sales_order_details.item_id', '=', 'material_request_details.item_id')
    //     ->whereIn('material_request_details.approval_type_id_fix',['4','5'])
    //     ->where('material_request.year_id','=',$year_data->id)
    //     ->where('material_request.current_location_id','=',$request->location_id)
    //     ->having('pending_material_qty','>',0)
    //     ->groupBy('material_request_details.mr_details_id')
    //     ->get();


    //     if(isset($edit_mrData)){
    //         $data = collect($mrData)->merge($edit_mrData);
    //         $grouped = $data->groupBy('mr_details_id');    
            

    //         $merged = $grouped->map(function ($items) {
    //             return $items->reduce(function ($carry, $item) {
    //                 if (!$carry) {
    //                     return $item;
    //                 }
    //                 $carry->pending_material_qty += (float) $item->pending_material_qty;
    //                 return $carry;
    //             });
    //         });
    
    //         $mrData = $merged->values();   

    //     }

    //     $mrData = $mrData->sortBy('mr_details_id')->values();

    //     if ($mrData != null) {
    //         foreach ($mrData as $cpKey => $cpVal) {
    //             if ($cpVal->mr_date != null) {
    //                 $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
    //             }
    //             if ($cpVal->pending_material_qty != null) {
    //                 $cpVal->pending_material_qty = $cpVal->pending_material_qty > 0 ? $cpVal->pending_material_qty : 0 ;
    //             }
               
    //         }
    //     }
    //     // dd($mrData);
    //     if ($mrData != null) {
    //         return response()->json([
    //             'mrData' => $mrData,
    //             'response_code' => '1',
    //             'response_message' => '',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'response_code' => '0',
    //             'response_message' => 'Record Does Not Exists',
    //         ]);
    //     }
        
    // }
    // public function getPendingMaterialRequest(Request $request){
        
    //     $year_data = getCurrentYearData();
    //     // $locationID = getCurrentLocation()->id;

    //     $mrData = MaterialRequest::select(['material_request_details.mr_details_id','material_request_details.mr_qty','material_request.mr_number','material_request.mr_date',       
    //     DB::raw("(material_request_details.mr_qty - (SELECT IFNULL(SUM(sales_order_details.so_qty),0)   FROM sales_order_details WHERE mr_details_id  = material_request_details.mr_details_id )) as pending_material_qty"),  
    //     ])
    //     ->leftJoin('material_request_details','material_request_details.mr_id','=','material_request.mr_id')      
    //     // ->leftJoin('sales_order_details','sales_order_details.mr_details_id','=','material_request_details.mr_details_id')      
    //     ->whereIn('material_request.approval_type_id_fix',['4','5'])
    //     ->where('material_request.year_id','=',$year_data->id)
    //     ->where('material_request.to_location_id','=',$request->location_id)
    //     ->having('pending_material_qty','>',0)
    //     ->get();

    //     if ($mrData != null) {
    //         foreach ($mrData as $cpKey => $cpVal) {
    //             if ($cpVal->mr_date != null) {
    //                 $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
    //             }
    //             if ($cpVal->pending_material_qty != null) {
    //                 $cpVal->pending_material_qty = $cpVal->pending_material_qty > 0 ? $cpVal->pending_material_qty : 0 ;
    //             }
               
    //         }
    //     }

    //     if ($mrData != null) {
    //         return response()->json([
    //             'mrData' => $mrData,
    //             'response_code' => '1',
    //             'response_message' => '',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'response_code' => '0',
    //             'response_message' => 'Record Does Not Exists',
    //         ]);
    //     }
        
    // }


    public function getMaterialPartsDataForSo(Request $request){
        // $year_data = getCurrentYearData();
           $yearIds = getCompanyYearIdsToTill();

        // $locationCode = getCurrentLocation();


        $sp_note = MaterialRequest::select('special_notes')->where('material_request.mr_id',$request->materialids)->first();   

      
        $request->materialids = explode(',',$request->materialids);

        $so_mr_detail_id = SalesOrderDetail::select(['sales_order_details.mr_details_id'])
        ->whereNotNull('sales_order_details.mr_details_id')
        ->get();    


        if(isset($request->id)){

            // $mr_detail_id = SalesOrderDetail::select('mr_details_id')->where('so_id',$request->id)->get();      
            // $mr_detail_id = MaterialRequestDetail::select('mr_details_id')->whereIn('mr_id',$request->materialids)->get();      
            
            
           $edit_mrData = SalesOrderDetail::select(['material_request_details.mr_details_id','material_request_details.mr_qty','material_request.mr_number','material_request.mr_date',  'items.item_name', 'material_request_details.item_id','items.item_code', 'item_groups.item_group_name', 'units.unit_name',  'sales_order_details.so_qty as pending_material_qty' ,'sales_order_details.rate_per_unit','sales_order_details.remarks','material_request.mr_id',
             ])
            ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
            ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
            ->leftJoin('items', 'items.id', 'sales_order_details.item_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            ->leftJoin('units', 'units.id', 'items.unit_id')
            // ->whereIn('sales_order_details.mr_details_id',$mr_detail_id)
             ->where('sales_order_details.so_id',$request->id)
             ->whereIn('material_request.mr_id',$request->materialids)

            ->get();

        }

        $material_data = MaterialRequest::select(['material_request_details.mr_details_id','material_request_details.mr_qty','item_groups.item_group_name','units.unit_name','material_request_details.item_id','items.item_code','material_request.mr_id','items.item_name','material_request_details.rate_unit as rate_per_unit' ,'material_request_details.remarks',
        ])          
        ->leftJoin('material_request_details','material_request_details.mr_id','=','material_request.mr_id')
        ->leftJoin('items', 'items.id', 'material_request_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('material_request_details.form_type','=','SO') 
        ->whereNotIn('material_request_details.mr_details_id',$so_mr_detail_id)    
        ->whereIn('material_request.mr_id',$request->materialids)
        // ->where('material_request.year_id',$year_data->id)
        ->whereIn('material_request.year_id',$yearIds)
        ->get();

        if (isset($edit_mrData)) {
            $data = collect($material_data)->merge($edit_mrData);
        
            // Assuming the issue_detail_id you mentioned is actually item_issue_details_id
            $grouped = $data->groupBy('mr_details_id');    
        
            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pending_material_qty += (float) $item->pending_material_qty;
                    return $carry;
                });
            });
        
            $material_data = $merged->values();   
        }


        if($material_data != null){
            foreach($material_data as $cpKey => $cpVal){

                if(isset($request->id)){  
                    $mr_qty =   SalesOrderDetail::where('mr_details_id','=',$cpVal->mr_details_id)->where('so_id',$request->id)->sum('so_qty');
                    $so_detail = SalesOrderDetail::where('mr_details_id','=',$cpVal->mr_details_id)->where('so_id',$request->id)->first();
                  
                    
                    $cpVal->so_qty = $mr_qty > 0 ? $mr_qty : $cpVal->mr_qty;
                    $cpVal->so_details_id  = $so_detail!= null ? $so_detail->so_details_id : 0;  
                    $cpVal->rate_per_unit =  $so_detail!= null ? $so_detail->rate_per_unit : $cpVal->rate_per_unit;
                    // $cpVal->so_amount =  $so_detail!= null ? $so_detail->so_amount : '';

                    if($so_detail!= null){                       
                        $cpVal->so_amount = $so_detail->so_amount ;                      
                    }else{
                        $cpVal->so_amount = $cpVal->rate_per_unit !="" ? $cpVal->rate_per_unit *  $cpVal->mr_qty : '';
                    }

                    
                }else{
                    $cpVal->so_qty = $cpVal->mr_qty;
                    $cpVal->so_details_id  = 0;
                }
            }
        }


          if($material_data != null){
              return response()->json([
                  'response_code' => '1',
                  'material_data' => $material_data,
                  'sp_note' => $sp_note,
              ]);
          }else{
              return response()->json([
                  'response_code' => '0',
                  'material_data' => []
              ]);
          }

  }

   public function getMaterialPartsDataForPR(Request $request){

        $yearIds = getCompanyYearIdsToTill();

        $sp_note = MaterialRequest::select('special_notes')->where('material_request.mr_id',$request->materialids)->first();   

      
        $request->materialids = explode(',',$request->materialids);

        $pr_mr_detail_id = PurchaseRequisitionDetails::select(['purchase_requisition_details.mr_details_id'])
        ->whereNotNull('purchase_requisition_details.mr_details_id')
        ->get();    


        if(isset($request->id)){

           $edit_mrData = PurchaseRequisitionDetails::select(['material_request_details.mr_details_id','material_request_details.mr_qty','material_request.mr_number','material_request.mr_date',  'items.item_name', 'material_request_details.item_id','items.item_code', 'item_groups.item_group_name', 'units.unit_name',  'purchase_requisition_details.req_qty as pending_material_qty' ,'purchase_requisition_details.rate_per_unit','purchase_requisition_details.remarks','material_request.mr_id','purchase_requisition_details.supplier_id',
             ])
            ->leftJoin('material_request_details','material_request_details.mr_details_id','=','purchase_requisition_details.mr_details_id')
            ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
            ->leftJoin('items', 'items.id', 'purchase_requisition_details.item_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            ->leftJoin('units', 'units.id', 'items.unit_id')
            ->where('purchase_requisition_details.pr_id',$request->id)
            ->whereIn('material_request.mr_id',$request->materialids)

            ->get();

        }

        // dd($edit_mrData);

        $material_data = MaterialRequest::select(['material_request_details.mr_details_id','material_request_details.mr_qty','item_groups.item_group_name','units.unit_name','material_request_details.item_id','items.item_code','material_request.mr_id','items.item_name','material_request_details.rate_unit as rate_per_unit' ,'material_request_details.remarks',
        ])          
        ->leftJoin('material_request_details','material_request_details.mr_id','=','material_request.mr_id')
        ->leftJoin('items', 'items.id', 'material_request_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('material_request_details.form_type','=','PO') 
        ->whereNotIn('material_request_details.mr_details_id',$pr_mr_detail_id)    
        ->whereIn('material_request.mr_id',$request->materialids)
        ->whereIn('material_request.year_id',$yearIds)
        ->get();

        if (isset($edit_mrData)) {
            $data = collect($material_data)->merge($edit_mrData);
        
            // Assuming the issue_detail_id you mentioned is actually item_issue_details_id
            $grouped = $data->groupBy('mr_details_id');    
        
            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->pending_material_qty += (float) $item->pending_material_qty;
                    return $carry;
                });
            });
        
            $material_data = $merged->values();   
        }


        if($material_data != null){
            foreach($material_data as $cpKey => $cpVal){

                if(isset($request->id)){  
                    $mr_qty =   PurchaseRequisitionDetails::where('mr_details_id','=',$cpVal->mr_details_id)->where('pr_id',$request->id)->sum('req_qty');
                    $pr_detail = PurchaseRequisitionDetails::where('mr_details_id','=',$cpVal->mr_details_id)->where('pr_id',$request->id)->first();

                    $cpVal->req_qty = $mr_qty > 0 ? $mr_qty : $cpVal->mr_qty;
                    $cpVal->pr_details_id  = $pr_detail!= null ? $pr_detail->pr_details_id : 0;  
                    $cpVal->rate_per_unit =  $pr_detail!= null ? $pr_detail->rate_per_unit : $cpVal->rate_per_unit;
                    // $cpVal->so_amount =  $so_detail!= null ? $so_detail->so_amount : '';

                    if($pr_detail!= null){                       
                        $cpVal->pr_amount = $pr_detail->pr_amount ;                      
                    }else{
                        $cpVal->pr_amount = $cpVal->rate_per_unit !="" ? $cpVal->rate_per_unit *  $cpVal->mr_qty : '';
                    }

                    
                }else{
                    $cpVal->req_qty = $cpVal->mr_qty;
                    $cpVal->pr_details_id  = 0;                   
                }

                 $cpVal->suppliers = SupplierItemMapping::select('suppliers.id as supplier_id','suppliers.supplier_name')
                ->leftJoin('suppliers','suppliers.id','=','supplier_item_mapping.supplier_id')
                ->where('supplier_item_mapping.item_id',$cpVal->item_id)->get();
            }
        }


          if($material_data != null){
              return response()->json([
                  'response_code' => '1',
                  'material_data' => $material_data,
                  'sp_note' => $sp_note,
              ]);
          }else{
              return response()->json([
                  'response_code' => '0',
                  'material_data' => []
              ]);
          }

  }
//     public function getMaterialPartsDataForSo(Request $request){
//         $year_data = getCurrentYearData();
//         // $locationCode = getCurrentLocation();

//         $request->materialids = explode(',',$request->materialids);
//         // dd($request->materialids);


//         if(isset($request->id)){

//             $mr_detail_id = SalesOrderDetail::select('mr_details_id')->where('so_id',$request->id)->get();      
            
            
//            $edit_mrData = SalesOrderDetail::select(['material_request_details.mr_details_id','material_request_details.mr_qty','material_request.mr_number','material_request.mr_date',  'items.item_name', 'items.id as ItemID', 'items.item_code', 'item_groups.item_group_name', 'units.unit_name',  'sales_order_details.so_qty as pending_material_qty' ,'sales_order_details.rate_per_unit',
//              ])
//             ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
//             ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
//             ->leftJoin('items', 'items.id', 'sales_order_details.item_id')
//             ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
//             ->leftJoin('units', 'units.id', 'items.unit_id')
//             ->whereIn('sales_order_details.mr_details_id',$request->materialids)
//             ->get();

//         }

//         $material_data = MaterialRequest::select(['material_request_details.mr_details_id','material_request_details.mr_qty','item_groups.item_group_name','units.unit_name','material_request_details.item_id  as item_id','items.item_code','material_request.mr_id','items.item_name',  
//         ])          
//         ->leftJoin('material_request_details','material_request_details.mr_id','=','material_request.mr_id')
//         ->leftJoin('items', 'items.id', 'material_request_details.item_id')
//         ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
//         ->leftJoin('units','units.id','=','items.unit_id')
//         ->whereIn('material_request_details.mr_details_id',$request->materialids)
//         // ->where('material_request.current_location_id',$locationCode->id)
//         ->where('material_request.year_id',$year_data->id)
//         ->get();

//         if (isset($edit_mrData)) {
//             $data = collect($material_data)->merge($edit_mrData);
        
//             // Assuming the issue_detail_id you mentioned is actually item_issue_details_id
//             $grouped = $data->groupBy('mr_details_id');    
        
//             $merged = $grouped->map(function ($items) {
//                 return $items->reduce(function ($carry, $item) {
//                     if (!$carry) {
//                         return $item;
//                     }
//                     $carry->pending_material_qty += (float) $item->pending_material_qty;
//                     return $carry;
//                 });
//             });
        
//             $material_data = $merged->values();   
//         }

//         if($material_data != null){
//             foreach($material_data as $cpKey => $cpVal){

//                 if(isset($request->id)){  
//                     $mr_qty =   SalesOrderDetail::where('mr_details_id','=',$cpVal->mr_details_id)->where('so_id',$request->id)->sum('so_qty');
//                     $so_detail = SalesOrderDetail::where('mr_details_id','=',$cpVal->mr_details_id)->where('so_id',$request->id)->first();

                    
//                     $cpVal->so_qty = $mr_qty > 0 ? $mr_qty : $cpVal->mr_qty;
//                     $cpVal->so_details_id  = $so_detail!= null ? $so_detail->so_details_id : 0;  
//                     $cpVal->rate_per_unit =  $so_detail!= null ? $so_detail->rate_per_unit : '';
//                     $cpVal->so_amount =  $so_detail!= null ? $so_detail->so_amount : '';

                    
//                 }else{
//                     $cpVal->so_qty = $cpVal->mr_qty;
//                     $cpVal->so_details_id  = 0;
//                 }
//             }
//         }


//           if($material_data != null){
//               return response()->json([
//                   'response_code' => '1',
//                   'material_data' => $material_data
//               ]);
//           }else{
//               return response()->json([
//                   'response_code' => '0',
//                   'material_data' => []
//               ]);
//           }

//   }


  public function getLocationForMR(Request $request){
    // $year_data = getCurrentYearData(); 
    $yearIds = getCompanyYearIdsToTill();

    $locationCode = getCurrentLocation();
    if(isset($request->id)){
        $edit_location = SalesOrder::select(['locations.id','locations.location_name', ])
        ->leftJoin('locations','locations.id','=','sales_order.to_location_id')   
        ->where('sales_order.id',$request->id)
        ->get();
    }
    
    // $location = MaterialRequestDetail::select(['locations.id','locations.location_name',    
    // DB::raw("(material_request_details.mr_qty - (SELECT IFNULL(SUM(sales_order_details.so_qty),0)   FROM sales_order_details WHERE mr_details_id  = material_request_details.mr_details_id )) as pending_material_qty"),  
    // ])
    // ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')   
    // ->leftJoin('locations','locations.id','=','material_request.current_location_id')   
    // ->whereIn('material_request_details.approval_type_id_fix',['4','5'])
    // ->where('material_request.year_id','=',$year_data->id)
    // ->where('material_request.to_location_id','=',$locationCode->id)
    // ->having('pending_material_qty','>',0)
    // // ->groupBy('locations.id')
    // ->get();

   

    $so_mr_detail_id = SalesOrderDetail::select(['sales_order_details.mr_details_id'])
    ->whereNotNull('sales_order_details.mr_details_id')
    ->get();

    if($so_mr_detail_id != null){
        $location = MaterialRequestDetail::select(['locations.id','locations.location_name'])
        ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')  
        ->leftJoin('locations','locations.id','=','material_request.current_location_id')   
        ->whereIn('material_request.approval_type_id_fix',['4','5']) 
        ->where('material_request_details.form_type','=','SO') 
        ->where('material_request.to_location_id','=',$locationCode->id)
        ->whereNotIn('material_request_details.mr_details_id',$so_mr_detail_id)    
        // ->where('material_request.year_id','=',$year_data->id)  
        ->whereIn('material_request.year_id',$yearIds)  
        ->get();       
    }
  

     if (isset($edit_location)) {
        $data = collect($location)->merge($edit_location);  
        $location = $data->values();   
    }

    $location = $location->unique(['id']);
 

    if($location != null){
        return response()->json([
            'response_code' => '1',
            'location' => $location
        ]);
    }else{
        return response()->json([
            'response_code' => '0',
            'location' => []
        ]);
    }

  }

  public function getLocationForPR(Request $request){
    // $year_data = getCurrentYearData(); 
    $yearIds = getCompanyYearIdsToTill();

    $locationCode = getCurrentLocation();
    if(isset($request->id)){
        $edit_location = PurchaseRequisition::select(['locations.id','locations.location_name', ])
        ->leftJoin('locations','locations.id','=','purchase_requisition.to_location_id')   
        ->where('purchase_requisition.pr_id',$request->id)
        ->get();
    }

    $pr_mr_detail_id = PurchaseRequisitionDetails::select(['purchase_requisition_details.mr_details_id'])
    ->whereNotNull('purchase_requisition_details.mr_details_id')
    ->get();

    // dd($pr_mr_detail_id);

    if($pr_mr_detail_id != null){
        $location = MaterialRequestDetail::select(['locations.id','locations.location_name'])
        ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')  
        ->leftJoin('locations','locations.id','=','material_request.current_location_id')   
        ->whereIn('material_request.approval_type_id_fix',['4','5']) 
        ->where('material_request_details.form_type','=','PO') 
        ->where('material_request.to_location_id','=',$locationCode->id)
        ->whereNotIn('material_request_details.mr_details_id',$pr_mr_detail_id)    
        ->whereIn('material_request.year_id',$yearIds)  
        ->get();       
    }
  

     if (isset($edit_location)) {
        $data = collect($location)->merge($edit_location);  
        $location = $data->values();   
    }

    $location = $location->unique(['id']);
 

    if($location != null){
        return response()->json([
            'response_code' => '1',
            'location' => $location
        ]);
    }else{
        return response()->json([
            'response_code' => '0',
            'location' => []
        ]);
    }

  }


public function getItemsFromPriceList(Request $request){
    $location = getCurrentLocation();   
    
    if(isset($request->id)){     

        
        $changedItemIds = MaterialRequestDetail::
        leftJoin('items', 'items.id', '=', 'material_request_details.item_id')
        ->where('material_request_details.mr_id',$request->id)
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes');
        })
        ->pluck('material_request_details.item_id')
        ->toArray();

      
        $missingPriceItemsIds = MaterialRequestDetail::
        leftJoin('price_list_details', function ($join) use ($request) {
            $join->on('material_request_details.item_id', '=', 'price_list_details.item_id')
                ->where('price_list_details.customer_group_id', '=', $request->customer_group_id);
        })
        ->where('material_request_details.mr_id', $request->id)
        ->pluck('material_request_details.item_id')
        ->toArray();  
       
       
        $priceListItemsIds = PriceListDetails::
        leftJoin('items','items.id' ,'price_list_details.item_id')
        ->where('items.fitting_item', 'no')
        ->where('items.status', 'active')
        ->where('items.service_item','No')        
        ->where('price_list_details.customer_group_id',$request->customer_group_id)
        ->pluck('price_list_details.item_id')
        ->toArray();  

        if(empty($priceListItemsIds)){
            $mappedItems = noFittingItem($changedItemIds);
        } else{             
            $mappedItems = Item::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name',
            DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE items.id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),)
            ->Join('item_groups','item_groups.id','=','items.item_group_id')
            ->Join('units','units.id','=','items.unit_id')
            ->whereIn('items.id',array_merge($changedItemIds,$missingPriceItemsIds,$priceListItemsIds))  
            ->get();      
        }
             
    }else{

        $mappedItems = PriceListDetails::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name',
        DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE items.id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),)
        ->leftJoin('items','items.id' ,'price_list_details.item_id')
        ->Join('item_groups','item_groups.id','=','items.item_group_id')
        ->Join('units','units.id','=','items.unit_id')
        ->where('items.fitting_item', 'no')
        ->where('items.status', 'active')
        ->where('items.service_item','No')        
        ->where('price_list_details.customer_group_id',$request->customer_group_id)
        ->get(); 

        if($mappedItems->isEmpty()){
           $changedItemIds =  [];
           $mappedItems = noFittingItem($changedItemIds);
        }

         
    }

    return response()->json([
        'response_code' => 1,
        'mappedItems' => $mappedItems
    ]);
}


}