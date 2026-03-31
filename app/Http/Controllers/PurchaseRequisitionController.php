<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\SupplierItemMapping;
use App\Models\PurchaseRequisitionDetails;
use Illuminate\Support\Facades\DB;
use Date;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DataTables;
use App\Models\Admin;
use App\Models\PurchaseOrderDetails;
use Illuminate\Validation\Rule;
use App\Models\PRShortClose;
use App\Models\Supplier;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\SalesOrderDetail;


class PurchaseRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();

        $pr_data = PurchaseRequisition::select(['purchase_requisition.pr_id','purchase_requisition.pr_number','purchase_requisition.pr_sequence','purchase_requisition.pr_date','purchase_requisition.prepared_by',
        'purchase_requisition.pr_form_value_fix','locations.location_name',
        'purchase_requisition.special_notes','suppliers.supplier_name','purchase_requisition.created_by_user_id','purchase_requisition.last_by_user_id','purchase_requisition.created_on','purchase_requisition.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('suppliers','suppliers.id','=','purchase_requisition.supplier_id')
        ->leftJoin('locations','locations.id','=','purchase_requisition.to_location_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'purchase_requisition.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'purchase_requisition.last_by_user_id')
        ->where('year_id','=',$year_data->id)
        ->where('current_location_id','=',$location->id);
       
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $pr_data->whereDate('purchase_requisition.pr_date','>=',$from);

                $pr_data->whereDate('purchase_requisition.pr_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $pr_data->where('purchase_requisition.pr_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $pr_data->where('purchase_requisition.pr_date','<=',$to);

        }  

       return DataTables::of($pr_data)

       ->editColumn('pr_date', function($pr_data){
           if ($pr_data->pr_date != null) {
               $formatedDate3 = Date::createFromFormat('Y-m-d', $pr_data->pr_date)->format(DATE_FORMAT); return $formatedDate3;
           }else{
               return '';
           }
       })
       ->filterColumn('purchase_requisition.pr_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_requisition.pr_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('created_by_user_id', function($pr_data){
           if($pr_data->created_by_user_id != null){
               $created_by_user_id = Admin::where('id','=',$pr_data->created_by_user_id)->first('user_name');
               return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
           }else{
               return '';
           }
       })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('last_by_user_id', function($pr_data){
           if($pr_data->last_by_user_id != null){
               $last_by_user_id = Admin::where('id','=',$pr_data->last_by_user_id)->first('user_name');
               return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
           }else{
               return '';
           }

       })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('created_on', function($pr_data){
           if ($pr_data->created_on != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $pr_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
           }else{
               return '';
           }
       })
        ->filterColumn('purchase_requisition.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_requisition.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('last_on', function($pr_data){
           if ($pr_data->last_on != null) {
               $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $pr_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
           }else{
               return '';
           }
       })
        ->filterColumn('purchase_requisition.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_requisition.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })

       ->editColumn('pr_form_value_fix', function($pr_data){
           if ($pr_data->pr_form_value_fix != null) {
            if($pr_data->pr_form_value_fix == 'from_location'){
                 $pr_form_value_fix = 'From Location';
            }else{
                $pr_form_value_fix = 'Manual';
            }
              return $pr_form_value_fix;
           }else{
               return 'Manual';
           }
       })
    //    ->filterColumn('purchase_requisition.pr_form_value_fix', function($query, $keyword) {
    //         if (stripos('from location', $keyword) !== false) {
    //             $query->where('pr_form_value_fix', 'from_location');
    //         }
    //         elseif (stripos('manual', $keyword) !== false) {
    //             $query->where(function($q){
    //                 $q->whereNull('pr_form_value_fix')
    //                 ->orWhere('pr_form_value_fix', 'manual');
    //             });
    //         }
    //     })
    ->filterColumn('purchase_requisition.pr_form_value_fix', function($query, $keyword) { 
        $keyword = strtolower(trim($keyword));
        if (stripos('from location', $keyword) !== false) {
            $query->where('pr_form_value_fix', 'from_location');
        } elseif (stripos('manual', $keyword) !== false) {
            $query->where(function($q){
                $q->whereNull('pr_form_value_fix')
                ->orWhere('pr_form_value_fix', 'manual');
            });
        } else {
            // Return no results for any other keyword
            $query->whereRaw('1 = 0');
        }
    })
        ->editColumn('supplier_name', function($pr_data){
            if($pr_data->pr_form_value_fix != 'from_location'){
                if($pr_data->supplier_name != ''){
                    $supplier_name = ucfirst($pr_data->supplier_name);
                    return $supplier_name;
                }
                else{
                $supplier_name = PurchaseRequisitionDetails::select('suppliers.supplier_name')
                ->leftJoin('suppliers','suppliers.id','=','purchase_requisition_details.supplier_id')    
                ->where('purchase_requisition_details.pr_id',$pr_data->pr_id)
                ->groupBy('suppliers.id')
                ->pluck('supplier_name')
                ->map(function ($name) {
                return ucfirst($name); 
                }) 
                ->implode(' , ');
                return $supplier_name;
                    // return '';
                }
            }else{
                return '';
            }
        })

    //    ->editColumn('supplier_name', function($pr_data){ 
    //         if($pr_data->supplier_name != ''){
    //             $supplier_name = ucfirst($pr_data->supplier_name);
    //             return $supplier_name;
    //         }else{

    //         $supplier_name = PurchaseRequisitionDetails::select('suppliers.supplier_name')
    //         ->leftJoin('suppliers','suppliers.id','=','purchase_requisition_details.supplier_id')    
    //         ->where('purchase_requisition_details.pr_id',$pr_data->pr_id)
    //         ->groupBy('suppliers.id')
    //         ->pluck('supplier_name')
    //         ->map(function ($name) {
    //         return ucfirst($name); 
    //         }) 
    //         ->implode(' , ');
    //         return $supplier_name;
    //             // return '';
    //         }
    //     })

       ->addColumn('options',function($pr_data){
           $action = "<div>";    
           if(hasAccess("purchase_requisition","print")){
            $action .="<a id='print_a' target='_blank' href='".route('print-purchase_requisition',['id' => base64_encode($pr_data->pr_id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
            }    
           if(hasAccess("purchase_requisition","edit")){
           $action .="<a id='edit_a' href='".route('edit-purchase_requisition',['id' => base64_encode($pr_data->pr_id )]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
           }
           if(hasAccess("purchase_requisition","delete")){
           $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
           }
           $action .= "</div>";
           return $action;
       })
       ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options'])
       ->make(true);
    }

    public function manage()
    {
        return view('manage.manage-purchase_requisition');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('add.add-purchase_requisition');
        
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
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

          $existNumber = PurchaseRequisition::where('pr_number','=',$request->pr_number)->where('pr_sequence','=',$request->pr_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationID)->lockForUpdate()->first();
                   
          if($existNumber){
              $latestNo    = $this->getLatestPRNo($request);              
              $tmp         =  $latestNo->getContent();
              $area        = json_decode($tmp, true);
              $pr_number   =   $area['latest_pr_no'];
              $pr_sequence = $area['number'];
          }else{
             $pr_number    = $request->pr_number;
             $pr_sequence  = $request->pr_sequence;
          }    

          $no_item_mapping_required = Supplier::where('id',$request->supplier_id)->value('no_item_mapping_required');

         $prFormTypeValue = $request->pr_form_id_fix == 1 ? "manual" : "from_location";


          DB::beginTransaction();
          try{
            $locationID = getCurrentLocation()->id;
            $year_data = getCurrentYearData();

            $pr_data =  PurchaseRequisition::create([
                'current_location_id' => $locationID,
                'pr_form_id_fix'      => $request->pr_form_id_fix, 
                'pr_form_value_fix'   => $prFormTypeValue, 
                'pr_sequence'         => $pr_sequence,
                'pr_number'           => $pr_number,
                'pr_date'             => Date::createFromFormat('d/m/Y', $request->pr_date)->format('Y-m-d'),
                'supplier_id'         => $request->pr_form_id_fix == 1 ? isset($request->supplier_id) ? $request->supplier_id : 0 : 0,
                'to_location_id'      => isset($request->pr_location_id) ? $request->pr_location_id : 0,
                'prepared_by'         => $request->prepared_by,               
                'special_notes'       => $request->special_notes,
                'supplier_no_item_mapping_required' =>  $no_item_mapping_required,
                'year_id'             => $year_data->id,
                'company_id'          => Auth::user()->company_id,
                'created_by_user_id'  => Auth::user()->id,
                'created_on'          => Carbon::now('Asia/Kolkata')->toDateTimeString()               
            ]);

            if($pr_data->save())
            {
                foreach($request->item_id as $spKey => $spVal)
                {

                    if($spVal != null){

                        if($request->pr_form_id_fix == '2' && isset($request->mr_details_id[$spKey])){

                            $mrQtySum = MaterialRequestDetail::where('mr_details_id',$request->mr_details_id[$spKey])->sum('mr_qty');

                            $usePRQtySum = PurchaseRequisitionDetails::where('mr_details_id',$request->mr_details_id[$spKey])->sum('req_qty');

                            $prQty = isset($request->req_qty[$spKey]) && $request->req_qty[$spKey] > 0 ? $request->req_qty[$spKey] : 0;

                            $prQtySum = $usePRQtySum + $prQty;
                          

                            if(number_format($mrQtySum, 3) < number_format($prQtySum, 3)){
                                DB::rollBack();
                                return response()->json([
                                    'response_code' => '0',
                                    'response_message' => 'PR Qty. Is Used',                               
                                ]);
                            }

                        }

                        $pr_details = PurchaseRequisitionDetails::create([
                            'pr_id'        => $pr_data->pr_id,
                            'item_id'      => isset($request->item_id[$spKey]) ? $request->item_id[$spKey] : "",
                            'req_qty'      => isset($request->req_qty[$spKey]) ? $request->req_qty[$spKey] : "",
                            'supplier_id'  => is_array($request->supplier_id) ? $request->supplier_id[$spKey] : $request->supplier_id,
                            'rate_per_unit'  => isset($request->rate_per_unit[$spKey]) ? $request->rate_per_unit[$spKey] : "",
                            'remarks'      => isset($request->remarks[$spKey]) ? $request->remarks[$spKey] : "",
                            'mr_details_id' =>isset($request->mr_details_id[$spKey]) ? $request->mr_details_id[$spKey] : null,
    
                            'status' => 'Y',
                        ]);


                        if(isset($request->mr_details_id[$spKey]) && !empty($request->mr_details_id[$spKey])){
                                $mr_id = MaterialRequestDetail::where('mr_details_id',$request->mr_details_id[$spKey])->first();
                                $material =  MaterialRequest::where('mr_id',$mr_id->mr_id)->update([
                                    'approval_type_id_fix' => 5,                                    
                                ]);
                        }
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
            dd($e->getMessage());
            DB::rollBack();
            getActivityLogs("Purchase Requisition", "add", $e->getMessage(),$e->getLine());
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
     * @param  \App\Models\PurchaseRequisition  $purchaseRequisition
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('edit.edit-purchase_requisition')->with('id',$id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseRequisition  $purchaseRequisition
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $isAnyPartInUse = false;
        
        $pr_data = PurchaseRequisition::select(['purchase_requisition.pr_id','purchase_requisition.pr_sequence','purchase_requisition.pr_number','purchase_requisition.pr_date','purchase_requisition.supplier_id','purchase_requisition.prepared_by','purchase_requisition.special_notes','purchase_requisition.supplier_no_item_mapping_required','purchase_requisition.pr_form_id_fix','purchase_requisition.pr_form_value_fix','purchase_requisition.to_location_id'])->where('pr_id',$id)->first();

        if($pr_data->pr_date != ""){
            $pr_data->pr_date = Date::createFromFormat('Y-m-d', $pr_data->pr_date)->format('d/m/Y');
        }

        $pr_details = PurchaseRequisitionDetails::select(['purchase_requisition_details.pr_details_id','items.id as item_id','purchase_requisition_details.pr_id','items.item_code','units.unit_name','purchase_requisition_details.req_qty','purchase_requisition_details.supplier_id','suppliers.supplier_name','purchase_requisition_details.remarks','purchase_requisition_details.rate_per_unit','purchase_requisition_details.mr_details_id','items.item_name','material_request.mr_id',
        ])
        ->leftJoin('material_request_details','material_request_details.mr_details_id','=','purchase_requisition_details.mr_details_id')
        ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
        ->leftJoin('items', 'items.id', 'purchase_requisition_details.item_id')  
        ->leftJoin('suppliers', 'suppliers.id', 'purchase_requisition_details.supplier_id')  
        ->leftJoin('units', 'units.id', 'items.unit_id')  
        ->where('purchase_requisition_details.pr_id','=',$id)->get();

        foreach ($pr_details as $key => $value) {

            // devendra logic
            // $po_details = PurchaseOrderDetails::
            // where('purchase_order_details.pr_details_id',$value['pr_details_id'])->get();

            // $pr_sc_details = PRShortClose::
            // where('purchase_requisition_short_close.pr_details_id','=',$value['pr_details_id'])
            // ->get();

            // if($po_details->isNotEmpty()){
            //     $pr_details[$key]['in_use'] = true;
            // }
            // if($pr_sc_details->isNotEmpty()){
            //     $pr_details[$key]['in_use'] = true;
            // }


            $totalPoQty = PurchaseOrderDetails::where('purchase_order_details.pr_details_id',$value['pr_details_id'])->sum('po_qty');

            $totalShortCloseQty = PRShortClose::where('purchase_requisition_short_close.pr_details_id','=',$value['pr_details_id'])->sum('pr_sc_qty');

            $isFound = $totalPoQty + $totalShortCloseQty;

            if($isFound != null){
                $value->in_use = true;
                $value->used_qty = $isFound;
                $isAnyPartInUse = true;

            }else{
                $value->in_use = false;
                $value->used_qty = 0;
            }


            $value->suppliers = SupplierItemMapping::select('suppliers.id as supplier_id','suppliers.supplier_name')
            ->leftJoin('suppliers','suppliers.id','=','supplier_item_mapping.supplier_id')
            ->where('supplier_item_mapping.item_id',$value['item_id'])->get();

            $locationCode = getCurrentLocation();    

            if($pr_data->supplier_no_item_mapping_required == 'Yes'){

                $changedItemIds = PurchaseRequisitionDetails::
                leftJoin('items', 'items.id', '=', 'purchase_requisition_details.item_id')
                ->leftJoin('location_stock', function ($join) use ($locationCode) {
                    $join->on('location_stock.item_id', '=', 'items.id')
                        ->where('location_stock.location_id', '=', $locationCode->id); 
                })
                ->where('purchase_requisition_details.pr_id', $request->id)
                ->where(function($query) {
                    $query->where(function($q){
                        $q->where('items.dont_allow_req_msl', '=', 'No')
                        ->where('items.max_stock_qty','>',' IFNULL(location_stock.stock_qty, 0)');
                    })
                    ->orWhere(function($q){
                        $q->where('items.status', '=', 'deactive');
                    });
                })
                ->where('items.secondary_unit','No')
                ->pluck('purchase_requisition_details.item_id')
                ->toArray();

                $value->items  = getPRItem($changedItemIds);

            }else{
                $changedItemIds = [$value['item_id']];

                $value->items = SupplierItemMapping::select('items.id','items.item_name','items.item_code','units.unit_name','items.dont_allow_req_msl','items.max_stock_qty')
                ->leftJoin('items','items.id' ,'supplier_item_mapping.item_id')
                ->Join('units','units.id','=','items.unit_id')
                ->leftJoin('location_stock', function ($join) use ($locationCode) {
                    $join->on('location_stock.item_id', '=', 'items.id')
                        ->where('location_stock.location_id', '=', $locationCode->id); 
                })       
                ->where(function($query) use ($changedItemIds) {        
                    $query->where('items.status', '=', 'active')
                        ->orWhereIn('items.id', $changedItemIds);
                })
                ->where(function($query) use ($changedItemIds) {        
                    $query->where(function($q){
                        $q->where('items.dont_allow_req_msl', '=', 'No')
                        ->where(function($subQuery){
                                $subQuery->whereRaw('items.max_stock_qty > IFNULL(location_stock.stock_qty, 0)')
                                        ->orWhereNull('items.max_stock_qty');
                        });
                    })
                    ->orWhere(function($q){
                        $q->where('items.dont_allow_req_msl', '=', 'Yes')
                        ->orWhereNull('items.dont_allow_req_msl');
                    })
                    ->orWhereIn('items.id', $changedItemIds);
                })
                ->where('items.secondary_unit','No')
                ->where('supplier_id',$value['supplier_id'])
                ->get();      

            }

            
        }

        if($pr_data->pr_date != ""){

            $pr_data->in_use = false;
            if($isAnyPartInUse == true){
                $pr_data->in_use = true;
            }
        }

        if ($pr_details) {
            return response()->json([
                'pr_details'       => $pr_details,
                'pr_data'          => $pr_data,
                'response_code'    => '1',
                'response_message' => '',
            ]);
        } else {
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
     * @param  \App\Models\PurchaseRequisition  $purchaseRequisition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseRequisition $purchaseRequisition)
    {

        DB::beginTransaction();
        
        try{
       
            $locationID  = getCurrentLocation()->id;
            $year_data   = getCurrentYearData();
            
            

            $validated = $request->validate(
            [
                'pr_sequence' => ['required','max:155',Rule::unique('purchase_requisition')->where(function ($query) use ($request,$year_data, $locationID) {                  
                    return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'pr_id')],
                

                'pr_number' => ['required', 'max:155', Rule::unique('purchase_requisition')->where(function ($query) use ($request, $year_data, $locationID) {                 
                    return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'pr_id')],              
            ],
            [
                'pr_sequence.unique'  => 'PR Sequence Is Already Exists',    
                'pr_number.required'  => 'Please Enter PR Number',
                'pr_number.max'       => 'Maximum 155 Characters Allowed',
            ]
            );

            $prFormTypeValue = $request->pr_form_id_fix == 1 ? "manual" : "from_location";

       
            $pr_data =  PurchaseRequisition::where('pr_id',$request->id)->update([

                'current_location_id' => $locationID,
                'pr_form_id_fix'      => $request->pr_form_id_fix, 
                'pr_form_value_fix'   => $prFormTypeValue, 
                'pr_sequence'         => $request->pr_sequence,
                'pr_number'           => $request->pr_number,
                'pr_date'             => Date::createFromFormat('d/m/Y', $request->pr_date)->format('Y-m-d'),
                'supplier_id'         => isset($request->supplier_id) ? $request->supplier_id : 0,
                'to_location_id'      => isset($request->pr_location_id) ? $request->pr_location_id : 0,
                'prepared_by'         => $request->prepared_by,
                'special_notes'       => $request->special_notes,
                'year_id'             => $year_data->id,
                'company_id'          => Auth::user()->company_id,
                'last_by_user_id'     => Auth::user()->id,
                'last_on'             => Carbon::now('Asia/Kolkata')->toDateTimeString(),               
            ]);
            if($pr_data){

                if (isset($request->pr_details_id) && !empty($request->pr_details_id)) {

                    $prDetails =  PurchaseRequisitionDetails::where('pr_id',$request->id)->update([
                        'status' => 'D',
                    ]);
                  
                    foreach ($request->pr_details_id as $sodKey => $sodVal) {
                        if($sodVal == "0"){
                          
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                $pr_details=  PurchaseRequisitionDetails::create([
                                    'pr_id'         => $request->id,
                                    'item_id'       => $request->item_id[$sodKey],
                                    'req_qty'       => $request->req_qty[$sodKey],
                                    'supplier_id'   => is_array($request->supplier_id) ? $request->supplier_id[$sodKey] : $request->supplier_id,
                                    'rate_per_unit'  => isset($request->rate_per_unit[$sodKey]) ? $request->rate_per_unit[$sodKey] : "",
                                    'remarks'       => $request->remarks[$sodKey],
                                    'mr_details_id' =>isset($request->mr_details_id[$sodKey]) ? $request->mr_details_id[$sodKey] : null,

                                    'status' => 'Y',
                                ]);


                                if(isset($request->mr_details_id[$sodKey]) && !empty($request->mr_details_id[$sodKey])){
                                    $mr_id = MaterialRequestDetail::where('mr_details_id',$request->mr_details_id[$sodKey])->first();
                                    $material =  MaterialRequest::where('mr_id',$mr_id->mr_id)->update([
                                        'approval_type_id_fix' => 5,                                    
                                    ]);
                                }


                            }
                            }else{

                                if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                    $pr_details =  PurchaseRequisitionDetails::where('pr_details_id',$sodVal)->update([
                                        'item_id'       => $request->item_id[$sodKey],
                                        'req_qty'       => $request->req_qty[$sodKey],
                                        'supplier_id'   => is_array($request->supplier_id) ? $request->supplier_id[$sodKey] : $request->supplier_id,
                                        'rate_per_unit'  => isset($request->rate_per_unit[$sodKey]) ? $request->rate_per_unit[$sodKey] : "",
                                        'remarks'       => $request->remarks[$sodKey],
                                        'status' => 'Y',
                                    ]);
                                }
                                // else{
                                //     PurchaseRequisitionDetails::where('pr_details_id', $sodVal)->delete();
                                // }
                            }
                        }

                        $mrData = PurchaseRequisitionDetails::select('material_request_details.mr_id')
                        ->leftJoin('material_request_details','material_request_details.mr_details_id','purchase_requisition_details.mr_details_id')
                        ->where('purchase_requisition_details.pr_id',$request->id)->where('purchase_requisition_details.status','D')->get();

                        $mrYData = PurchaseRequisitionDetails::select('material_request_details.mr_id')
                        ->leftJoin('material_request_details','material_request_details.mr_details_id','purchase_requisition_details.mr_details_id')
                        ->where('purchase_requisition_details.pr_id',$request->id)->where('purchase_requisition_details.status','Y')->get();
            
                        if($mrData->isNotEmpty() && empty($mrYData)){
                            foreach ($mrData as $ctKey => $ctVal) {
                                MaterialRequest::where('mr_id', '=', $ctVal->mr_id)->update([
                                    'approval_type_id_fix' => '4'
                                ]);
                            }
                        }

                        $prDetails = PurchaseRequisitionDetails::where('pr_id',$request->id)->where('status','D')->delete();
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
                DB::rollBack();
                getActivityLogs("Purchase Requisition", "update", $e->getMessage(),$e->getLine());
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Error Occured Record Not Inserted',
                    'original_error' => $e->getMessage()
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseRequisition  $purchaseRequisition
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseRequisition $purchaseRequisition,Request $request)
    {
        DB::beginTransaction();
        try{

            $po_data = PurchaseOrderDetails::select('purchase_order_details.pr_details_id')
            ->leftJoin('purchase_requisition_details','purchase_requisition_details.pr_details_id','=','purchase_order_details.pr_details_id')
            ->where('purchase_requisition_details.pr_id',$request->id)->get();

            if($po_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Purchase Requisition Is Used In PO.",
                ]);
            }


            $pr_sc = PRShortClose::select('purchase_requisition_short_close.pr_details_id') 
            ->leftJoin('purchase_requisition_details', 'purchase_requisition_details.pr_details_id', '=', 'purchase_requisition_short_close.pr_details_id')
            ->leftJoin('purchase_requisition', 'purchase_requisition.pr_id', '=', 'purchase_requisition_details.pr_id')
            ->where('purchase_requisition_details.pr_id', $request->id)
            ->get();


             if($pr_sc->isNotEmpty()){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Purchase Requisition Is Used In Purchase Requisition Short Close.",
                ]);
             }

            $mrData = PurchaseRequisitionDetails::select('material_request_details.mr_id')
            ->leftJoin('material_request_details','material_request_details.mr_details_id','purchase_requisition_details.mr_details_id')
            ->where('purchase_requisition_details.pr_id', $request->id)
            ->groupBy('material_request_details.mr_id')
            ->get();

            foreach ($mrData as $data) {
                $mrId = $data->mr_id;

                $soMrDetailIds = MaterialRequestDetail::where('mr_id', $mrId)
                    ->where('form_type', 'SO')
                    ->pluck('mr_details_id')
                    ->toArray();

               
                $existsInSalesOrder = SalesOrderDetail::whereIn('mr_details_id', $soMrDetailIds)->exists();

                
                if (!$existsInSalesOrder) {
                    MaterialRequest::where('mr_id', $mrId)->update([
                        'approval_type_id_fix' => 4
                    ]);
                }
            }


            PurchaseRequisition::destroy($request->id);
            PurchaseRequisitionDetails::where('pr_id',$request->id)->delete();
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            getActivityLogs("Purchase Requisition", "delete", $e->getMessage(),$e->getLine());
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
                // $error_msg = "You Can't Delete, PO Is Used In GRN.";

            }else{
                $error_msg = "Record Not Deleted";
            }
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }

    public function getLatestPRNo(Request $request)
    {
          $modal  =  PurchaseRequisition::class;
          $sequence = 'pr_sequence';
          $prefix = 'PR';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_pr_no'  => $sup_num_format['format'],
            'number'        => $sup_num_format['isFound'],
            'location'      => $locationName
        ]);

    }

    public function getItemSupplierData(Request $request)
    {
        /*$item = Item::select(['items.id as item_id','items.item_code','units.unit_name'])
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('items.id',$request->item)->first();*/

        $suppliers = SupplierItemMapping::select('suppliers.id as supplier_id','suppliers.supplier_name')
        ->leftJoin('suppliers','suppliers.id','=','supplier_item_mapping.supplier_id')
        ->where('suppliers.status', 'active')    
        ->where('supplier_item_mapping.item_id',$request->item)->get();

        return response()->json([
            'response_code' => 1,
            //'item'  => $item,
            'suppliers' => $suppliers,
        ]);

    }

    public function existsPreparedBy(Request $request){
        if($request->term != ""){
            $fdOrderBy = PurchaseRequisition::select('prepared_by')->where('prepared_by', 'LIKE', $request->term.'%')->groupBy('prepared_by')->get();
            if($fdOrderBy != null){
             
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdOrderBy as $dsKey){
    
                    $output .= '<li parent-id="prepared_by" list-id="prepared_by_list" class="list-group-item" tabindex="0">'.$dsKey->prepared_by.'</li>';
                }
                $output .= '</ul>';
    
                return response()->json([
                    'orderByList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Order By available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'menuList' => '',
                'response_code' => 1,
            ]);
        }
    
    }

    public function getPRItemsFromMapping(Request $request){
        $locationCode = getCurrentLocation();    
        
        $no_item_mapping_required = Supplier::where('id',$request->supplier_id)->value('no_item_mapping_required');

        if(isset($request->id)){
            $changedItemIds = PurchaseRequisitionDetails::
            leftJoin('items', 'items.id', '=', 'purchase_requisition_details.item_id')
            ->leftJoin('location_stock', function ($join) use ($locationCode) {
                $join->on('location_stock.item_id', '=', 'items.id')
                     ->where('location_stock.location_id', '=', $locationCode->id); 
            })
            ->where('purchase_requisition_details.pr_id', $request->id)
            ->where(function($query) {
                $query->where(function($q){
                    $q->where('items.dont_allow_req_msl', '=', 'No')
                    ->where('items.max_stock_qty','>',' IFNULL(location_stock.stock_qty, 0)');
                })
                ->orWhere(function($q){
                    $q->where('items.status', '=', 'deactive');
                });
            })
            ->where('items.secondary_unit','=','No')
            ->pluck('purchase_requisition_details.item_id')
            ->toArray();

            if($no_item_mapping_required == "Yes"){
                

                 $mappedItems = getPRItem($changedItemIds);

                  
            }else{    
    
                $mappedItems =  SupplierItemMapping::select('items.id','items.item_name','items.item_code','units.unit_name','items.dont_allow_req_msl','items.max_stock_qty','items.secondary_unit')
                ->leftJoin('items','items.id' ,'supplier_item_mapping.item_id')
                ->Join('units','units.id','=','items.unit_id')
                ->leftJoin('location_stock', function ($join) use ($locationCode) {
                    $join->on('location_stock.item_id', '=', 'items.id')
                        ->where('location_stock.location_id', '=', $locationCode->id); 
                })       
                ->where(function($query) use ($changedItemIds) {        
                    $query->where('items.status', '=', 'active')
                        ->orWhereIn('items.id', $changedItemIds);
                })
                ->where(function($query) use ($changedItemIds) {        
                    $query->where(function($q){
                        $q->where('items.dont_allow_req_msl', '=', 'No')
                        ->where(function($subQuery){
                                $subQuery->whereRaw('items.max_stock_qty > IFNULL(location_stock.stock_qty, 0)')
                                        ->orWhereNull('items.max_stock_qty');
                        });
                    })
                    ->orWhere(function($q){
                        $q->where('items.dont_allow_req_msl', '=', 'Yes')
                        ->orWhereNull('items.dont_allow_req_msl');
                    })
                    ->orWhereIn('items.id', $changedItemIds);
                })
                ->where('items.secondary_unit','=','No')
                ->where('supplier_id',$request->supplier_id)
                ->get();      
            }
        }else{
         
            
            if($no_item_mapping_required == "Yes"){
                 $changedItemIds = [];

                 $mappedItems = getPRItem($changedItemIds);

                  
            }else{                
                
                $mappedItems = SupplierItemMapping::select('items.id','items.item_name','items.item_code','units.unit_name','items.secondary_unit')
                ->leftJoin('items','items.id' ,'supplier_item_mapping.item_id')
                ->Join('units','units.id','=','items.unit_id')
                ->leftJoin('location_stock', function ($join) use ($locationCode) {
                    $join->on('location_stock.item_id', '=', 'items.id')
                        ->where('location_stock.location_id', '=', $locationCode->id); 
                })       
                ->where(function($query) {
                    $query->where('items.dont_allow_req_msl', '=', 'No')
                        ->where(function($subQuery){                          
                            $subQuery->whereRaw('items.max_stock_qty > IFNULL(location_stock.stock_qty, 0)')
                                    ->orWhereNull('items.max_stock_qty');
                        })
                        ->orWhere(function($q){
                            $q->where('items.dont_allow_req_msl', '=', 'Yes')
                            ->orWhereNull('items.dont_allow_req_msl');
                        });
                })
                ->where('items.secondary_unit','=','No')
                ->where('items.status', 'active')
                ->where('supplier_id',$request->supplier_id)
                ->get();  
            }
         
        }
      
        return response()->json([
            'response_code' => 1,
            'mappedItems' => $mappedItems
        ]);
    }


    // public function getPRItemsFromMapping(Request $request){
    //     $locationCode = getCurrentLocation();    
        
    //     $no_item_mapping_required = Supplier::where('id',$request->supplier_id)->value('no_item_mapping_required');

    //     if(isset($request->id)){
    //         $changedItemIds = PurchaseRequisitionDetails::
    //         leftJoin('items', 'items.id', '=', 'purchase_requisition_details.item_id')
    //         ->leftJoin('location_stock', function ($join) use ($locationCode) {
    //             $join->on('location_stock.item_id', '=', 'items.id')
    //                  ->where('location_stock.location_id', '=', $locationCode->id); 
    //         })
    //         ->where('purchase_requisition_details.pr_id', $request->id)
    //         ->where(function($query) {
    //             $query->where(function($q){
    //                 $q->where('items.dont_allow_req_msl', '=', 'Yes')
    //                 ->where('items.min_stock_qty','>',' IFNULL(location_stock.stock_qty, 0)');
    //             })
    //             ->orWhere(function($q){
    //                 $q->where('items.status', '=', 'deactive');
    //             });
    //         })
    //         ->pluck('purchase_requisition_details.item_id')
    //         ->toArray();

    //         if($no_item_mapping_required == "Yes"){
                

    //              $mappedItems = getPRItem($changedItemIds);

                  
    //         }else{    
    
    //             $mappedItems =  SupplierItemMapping::select('items.id','items.item_name','items.item_code','units.unit_name','items.dont_allow_req_msl','items.min_stock_qty')
    //             ->leftJoin('items','items.id' ,'supplier_item_mapping.item_id')
    //             ->Join('units','units.id','=','items.unit_id')
    //             ->leftJoin('location_stock', function ($join) use ($locationCode) {
    //                 $join->on('location_stock.item_id', '=', 'items.id')
    //                     ->where('location_stock.location_id', '=', $locationCode->id); 
    //             })       
    //             ->where(function($query) use ($changedItemIds) {        
    //                 $query->where('items.status', '=', 'active')
    //                     ->orWhereIn('items.id', $changedItemIds);
    //             })
    //             ->where(function($query) use ($changedItemIds) {        
    //                 $query->where(function($q){
    //                     $q->where('items.dont_allow_req_msl', '=', 'Yes')
    //                     ->where(function($subQuery){
    //                             $subQuery->whereRaw('items.min_stock_qty > IFNULL(location_stock.stock_qty, 0)')
    //                                     ->orWhereNull('items.min_stock_qty');
    //                     });
    //                 })
    //                 ->orWhere(function($q){
    //                     $q->where('items.dont_allow_req_msl', '=', 'No')
    //                     ->orWhereNull('items.dont_allow_req_msl');
    //                 })
    //                 ->orWhereIn('items.id', $changedItemIds);
    //             })
    //             ->where('supplier_id',$request->supplier_id)
    //             ->get();      
    //         }
    //     }else{
         
            
    //         if($no_item_mapping_required == "Yes"){
    //              $changedItemIds = [];

    //              $mappedItems = getPRItem($changedItemIds);

                  
    //         }else{
                
    //             $mappedItems = SupplierItemMapping::select('items.id','items.item_name','items.item_code','units.unit_name')
    //             ->leftJoin('items','items.id' ,'supplier_item_mapping.item_id')
    //             ->Join('units','units.id','=','items.unit_id')
    //             ->leftJoin('location_stock', function ($join) use ($locationCode) {
    //                 $join->on('location_stock.item_id', '=', 'items.id')
    //                     ->where('location_stock.location_id', '=', $locationCode->id); 
    //             })       
    //             ->where(function($query) {
    //                 $query->where('items.dont_allow_req_msl', '=', 'Yes')
    //                     ->where(function($subQuery){
    //                         $subQuery->whereRaw('items.min_stock_qty > IFNULL(location_stock.stock_qty, 0)')
    //                                 ->orWhereNull('items.min_stock_qty');
    //                     })
    //                     ->orWhere(function($q){
    //                         $q->where('items.dont_allow_req_msl', '=', 'No')
    //                         ->orWhereNull('items.dont_allow_req_msl');
    //                     });
    //             })
    //             ->where('items.status', 'active')
    //             ->where('supplier_id',$request->supplier_id)
    //             ->get();  
    //         }
         
    //     }
      
    //     return response()->json([
    //         'response_code' => 1,
    //         'mappedItems' => $mappedItems
    //     ]);
    // }


    public function managePendingPO(){
        return view('manage.manage-pending_pr_for_po');
    }

    public function indexPendingPO(Request $request){

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation(); 

        $get_po_data = PurchaseRequisitionDetails::select('purchase_requisition.pr_number','purchase_requisition.pr_date','purchase_requisition.pr_sequence','suppliers.supplier_name','suppliers.id','items.item_name','items.item_code','purchase_requisition_details.req_qty','units.unit_name','purchase_requisition_details.rate_per_unit','purchase_requisition_details.remarks',
        DB::raw("(IFNULL(SUM(purchase_requisition_details.req_qty), 0) - (SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) FROM purchase_requisition_short_close AS prsc WHERE prsc.pr_details_id = purchase_requisition_details.pr_details_id)) - (SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id) AS pend_pr_qty"),)
        
        ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')
        ->leftJoin('suppliers','suppliers.id', 'purchase_requisition_details.supplier_id')
        ->leftJoin('items','items.id','=','purchase_requisition_details.item_id')
        ->leftJoin('units','units.id', 'items.unit_id')
        ->where('purchase_requisition.current_location_id',$locationCode->id)
        ->whereIn('purchase_requisition.year_id',$yearIds)
        ->groupBy('purchase_requisition_details.pr_details_id')               
        ->having('pend_pr_qty','>',0);    
        
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $get_po_data->whereDate('purchase_requisition.pr_date','>=',$from);

                $get_po_data->whereDate('purchase_requisition.pr_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $get_po_data->where('purchase_requisition.pr_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $get_po_data->where('purchase_requisition.pr_date','<=',$to);

        }

        

        // if($request->pr_number !=''){
        //     $get_po_data->where('purchase_requisition.pr_number','like', "%{$request->pr_number}%");
        // }

        // if($request->item_id !=''){
        //     $get_po_data->where('purchase_requisition_details.item_id', '=', $request->item_id);
        // }

        // if($request->supplier_id !=''){
        //     $get_po_data->where('purchase_requisition_details.supplier_id','=',$request->supplier_id);
        // }

        return DataTables::of($get_po_data)

        ->editColumn('pr_date', function($get_po_data){
            if ($get_po_data->pr_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $get_po_data->pr_date)->format(DATE_FORMAT); return $formatedDate3;
            }else{
                return '';
            }
        })
        ->filterColumn('purchase_requisition.pr_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_requisition.pr_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('supplier_name', function($get_po_data){ 
            if($get_po_data->supplier_name != ''){
                $supplier_name = ucfirst($get_po_data->supplier_name);
                return $supplier_name;
            }else{
                return '';
            }
        })
        ->editColumn('item_name', function($get_po_data){
            if($get_po_data->item_name != ''){
                $item_name = ucfirst($get_po_data->item_name);
                return $item_name;
            }else{
                return '';
            }
        })
        ->editColumn('req_qty', function($get_po_data) {
            return $get_po_data->req_qty > 0
                ? number_format((float)$get_po_data->req_qty, 3, '.', '')
                : number_format(0, 3, '.', '');
        })
        ->addColumn('pend_pr_qty', function($get_po_data) {
            return $get_po_data->pend_pr_qty > 0
                ? number_format((float)$get_po_data->pend_pr_qty, 3, '.', '')
                : number_format(0, 3, '.', '');
        })
        ->filterColumn('pend_pr_qty', function($query, $keyword) {
            $query->whereRaw("(
                (IFNULL(purchase_requisition_details.req_qty, 0)
                    - (SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) 
                        FROM purchase_requisition_short_close AS prsc 
                        WHERE prsc.pr_details_id = purchase_requisition_details.pr_details_id)
                    - (SELECT IFNULL(SUM(pod.po_qty), 0) 
                        FROM purchase_order_details AS pod 
                        WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id)
                ) LIKE ?)", ["%{$keyword}%"]);
        })
        ->editColumn('rate_per_unit', function($get_po_data) {
            return $get_po_data->rate_per_unit > 0
                ? number_format((float)$get_po_data->rate_per_unit, 2, '.', '')
                : '';
        })
 
        ->rawColumns(['pend_pr_qty'])
        ->make(true);

        
    }
}