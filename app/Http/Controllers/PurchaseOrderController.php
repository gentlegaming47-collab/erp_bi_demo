<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetails;

use Illuminate\Validation\Rule;
use App\Models\Supplier;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Date;
use Illuminate\Support\Facades\Auth;
use Str;
use Illuminate\Support\Facades\Schema;
use App\Models\POShortClose;
use App\Models\GRNMaterialDetails;
use App\Models\SupplierItemMapping;
use App\Models\PurchaseRequisitionDetails;
use App\Models\PurchaseRequisition;
use App\Models\SupplierRejectoionDetails;

class PurchaseOrderController extends Controller
{
    public function manage()
    {
        return view('manage.manage-purchase_order');
    }

    public function index(PurchaseOrder $purchase_order,Request $request,DataTables $dataTables)
    {

        $location = getCurrentLocation();
        $year_data = getCurrentYearData();
        
        $purchase_order = PurchaseOrder::select([
            'purchase_order.po_id','purchase_order.po_sequence','purchase_order.created_on','purchase_order.last_on','purchase_order.created_by_user_id','purchase_order.last_by_user_id','purchase_order.po_number','purchase_order.po_date','purchase_order.person_name','suppliers.supplier_name','locations.location_name', 'purchase_order.is_approved','created_user.user_name as created_by_name','last_user.user_name as last_by_name',
            DB::raw('SUM(purchase_order_details.amount) as amount')
            //  'purchase_order.total_amount',
            ])
            ->leftJoin('purchase_order_details','purchase_order_details.po_id' ,'purchase_order.po_id')
            ->leftJoin('suppliers','suppliers.id' ,'purchase_order.supplier_id')
            ->leftJoin('locations','locations.id' ,'purchase_order.to_location_id')
            ->leftJoin('admin AS created_user', 'created_user.id', '=', 'purchase_order.created_by_user_id')
            ->leftJoin('admin AS last_user', 'last_user.id', '=', 'purchase_order.last_by_user_id')
            ->where('purchase_order.current_location_id','=',$location->id)
            ->where('purchase_order.year_id', '=', $year_data->id)
            ->groupBy('purchase_order.po_number');
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $purchase_order->whereDate('purchase_order.po_date','>=',$from);

                $purchase_order->whereDate('purchase_order.po_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $purchase_order->where('purchase_order.po_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $purchase_order->where('purchase_order.po_date','<=',$to);

        }  
        // dd($purchase_order);
      
        return DataTables::of($purchase_order)

        ->editColumn('created_by_user_id', function($purchase_order){
            if($purchase_order->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$purchase_order->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
    
    
        //    })
           ->editColumn('rate_per_unit', function($purchase_order){

            return $purchase_order->rate_per_unit > 0 ? number_format((float)$purchase_order->rate_per_unit, 2, '.','') : number_format((float) 0, 2, '.','');
    
           })
       
        //    })
           ->editColumn('rate_per_unit', function($purchase_order){

            return $purchase_order->rate_per_unit > 0 ? number_format((float)$purchase_order->rate_per_unit, 3, '.','') : number_format((float) 0, 3, '.','');
    
           })
        // ->editColumn('total_amount', function($purchase_order) {
        // return $purchase_order->total_amount > 0 
        //     ? number_format((float)$purchase_order->total_amount, 3, '.', '') 
        // : number_format(0, 3, '.', ''); 
        // })
            ->editColumn('amount', function($purchase_order) {
                return $purchase_order->amount > 0
                ? number_format((float)$purchase_order->amount, 3, '.', '')
                : number_format(0, 3, '.', '');
            })
    
    
        //     //    return $itemAssmProduction->assembly_qty > 0 ? $itemAssmProduction->assembly_qty : 0;
    
        //    })
     
    
        //     //    return $itemAssmProduction->assembly_qty > 0 ? $itemAssmProduction->assembly_qty : 0;
    
        //    })
        ->editColumn('last_by_user_id', function($purchase_order){
            if($purchase_order->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$purchase_order->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('po_date', function($purchase_order){           
            if ($purchase_order->po_date != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d', $purchase_order->po_date)->format('d/m/Y'); 
                
                return $formatedDate1;

            }else{
                return '';
            }
        })
        ->filterColumn('purchase_order.po_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.po_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('created_on', function($purchase_order){
            if ($purchase_order->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $purchase_order->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('purchase_order.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($purchase_order){
            if ($purchase_order->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $purchase_order->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('purchase_order.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->addColumn('options',function($purchase_order){
            $action = "<div>";        
            if($purchase_order->is_approved == 1){
            // if($purchase_order->po_number!=''){                
            //     $pdfName= 'Purchase_Order_'.str_replace("/", "_",$purchase_order->po_number);
                
            // }else{
            //     $pdfName= 'Purchase_Order_';
            // }
            // if (hasAccess("purchase_order", "print")) {        
     
            //      $action .= "<a target='_blank' href='" . route('check-file_exists', ['id' => base64_encode($purchase_order->po_id ),'name' => $pdfName,'type' => 'po']) . "' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
            // } 

            if(hasAccess("purchase_order","print")){
                    $action .="<a id='print_a' target='_blank' href='".route('print-purchase_order',['id' => base64_encode($purchase_order->po_id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
                    }
            }
            if(hasAccess("purchase_order","edit")){
            $action .="<a id='edit_a' href='".route('edit-purchase_order',['id' => base64_encode($purchase_order->po_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("purchase_order","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
       
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options','po_date'])
        ->make(true);
    }


    public function create()
    {
        return view('add.add-purchase_order');
    }

    public function store(Request $request)
    {
        // check duplicate number
        // dd($request->all());
        $locationID = getCurrentLocation()->id;
        $year_data = getCurrentYearData();

        $existNumber = PurchaseOrder::where([
            ['po_number',  $request->po_number],
            ['po_sequence',$request->po_sequence],
            ['year_id',$year_data->id],
            ['current_location_id',$locationID],
        ])->lockForUpdate()->first();
        
        if($existNumber){
            $latestNo = $this->getLatestPoNo($request);
            $tmp =  $latestNo->getContent();
            $area = json_decode($tmp, true);
            $po_number =   $area['latest_po_no'];
            $po_sequence = $area['number'];
        }else{
           $po_number = $request->po_number;
           $po_sequence = $request->po_sequence;
        }
        // end check duplicate number

        DB::beginTransaction();
        
        try{
            
          
            $totalQty = 0;
            $totalAmount = 0;
            
    
            foreach ($request->item_id as $ctKey => $ctVal) {
                if ($ctVal != null) {
                    $totalQty += $request->po_qty[$ctKey];  
                    $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";        
                }
            }
                $purchase_order =  PurchaseOrder::create([
                    'current_location_id'=>$locationID,
                    'po_sequence'        => $po_sequence,
                    'po_number'          => $po_number,
                    'po_date'            => Date::createFromFormat('d/m/Y', $request->po_date)->format('Y-m-d'),
                    'supplier_id'        => $request->supplier_id,
                    'person_name'        => $request->person,
                    'order_by'           => $request->order_by,
                    'ref_no'             => $request->ref_no,
                    'ref_date'           => $request->ref_date != "" ? Date::createFromFormat('d/m/Y', $request->ref_date)->format('Y-m-d') : null,
                    'to_location_id' => $request->ship_to,
                    'delivery_date'      => $request->check_date != "" ? Date::createFromFormat('d/m/Y', $request->check_date)->format('Y-m-d') : "",
                    'total_qty'          => $totalQty,
                    'total_amount'       => $totalAmount,
                    'pf_charge'          => $request->pf_charge,
                    'frieght'            => $request->freight,
                    'gst'                => $request->gst,
                    'test_certificate'   => $request->test_certificate,
                    'order_acceptance'   => $request->order_acceptance,
                    'prepared_by'        => $request->prepared_by,
                    'payment_terms'      => $request->payment_terms,
                    'special_notes'      => $request->sp_notes,
                    'is_approved'        => 0,
                    'po_form_type'        => "P",
                    'year_id'            => $year_data->id,
                    'company_id'         => Auth::user()->company_id,
                    'created_by_user_id' => Auth::user()->id,
                    'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString()               
                ]);
                if($purchase_order->save()){                    
                    foreach($request->item_id as $mkey=>$mval){

                        if(isset($request->pr_details_id[$mkey])){

                            $prQtySum = PurchaseRequisitionDetails::where('pr_details_id',$request->pr_details_id[$mkey])->sum('req_qty');

                            $usePOQtySum = PurchaseOrderDetails::where('pr_details_id',$request->pr_details_id[$mkey])->sum('po_qty');

                            $poQty = isset($request->po_qty[$mkey]) && $request->po_qty[$mkey] > 0 ? $request->po_qty[$mkey] : 0;

                            $poQtySum = $usePOQtySum + $poQty;                          

                            if(number_format($prQtySum, 3) < number_format($poQtySum, 3)){
                                DB::rollBack();
                                return response()->json([
                                    'response_code' => '0',
                                    'response_message' => 'PO Qty. Is Used',                               
                                ]);
                            }

                        }
                        
                            $po_list_details=  PurchaseOrderDetails::create([
                                'po_id'         => $purchase_order->po_id,
                                'item_id'       => $request->item_id[$mkey],
                                'po_qty'        => $request->po_qty[$mkey],
                                'rate_per_unit' => $request->rate_unit[$mkey],
                                'discount'      => $request->discount[$mkey] != "" ? $request->discount[$mkey] : 0.00,
                                'amount'        => $request->amount[$mkey],
                                'del_date'      => Date::createFromFormat('d/m/Y', $request->del_date[$mkey])->format('Y-m-d'),
                                'remarks'       => $request->remarks[$mkey],
                                'pr_details_id'  => $request->pr_details_id[$mkey]
                            ]);
                        }
                        DB::commit();

                        //  $pdf_name = 'Purchase_Order_'.str_replace("/", "_",$po_number);
                        //  GeneratePdf($purchase_order->po_id,$pdf_name,'po','','add');
                        return response()->json([
                            'response_code' => '1',
                            'response_message' => 'Record Inserted Successfully.',
                        ]);
                }else{
                    DB::rollBack();
                    return response()->json([
                        'response_code' => '1',
                        'response_message' => 'Record Not Inserted.',
                    ]);
                }
            }
            catch(\Exception $e){    
                                
                    DB::rollBack();
                    getActivityLogs("Purchase Order", "add", $e->getMessage(),$e->getLine());
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Error Occured Record Not Inserted',
                        'original_error' => $e->getMessage()
                    ]);
            }
    }

    public function getLatestPoNo(Request $request)
    {
          $modal  =  PurchaseOrder::class;
          $sequence = 'po_sequence';
          $prefix = 'PO';
          $po_num_format = getLatestSequence($modal,$sequence,$prefix);

          $locationName = getCurrentLocation();

          $getLastTestCertificate = PurchaseOrder::select('test_certificate','order_acceptance')
                                    ->orderBy('po_id', 'desc')
                                    ->first();
        //   $prepared_by = PurchaseOrder::select('prepared_by')
        //                             ->where('current_location_id',$locationName->id)
        //                             ->orderBy('po_id', 'desc')
        //                             ->first();



          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $po_num_format['format'],
            'number'        => $po_num_format['isFound'],
            'location'      => $locationName,
            'LastCertificate' => $getLastTestCertificate
            // 'PreparedBy' => $prepared_by,
        ]);
    }

    public function show(PurchaseOrder $purchase_order, $id)
    {
        $getData = PurchaseOrder::where('po_id', '=', base64_decode($id))->get();
        return view('edit.edit-purchase_order', compact('getData', 'id'));
    }

    public function edit(Request $request, $id)
    {
        $locationCode = getCurrentLocation();
        $isAnyPartInUse = false;
        $purchase_order = PurchaseOrder::select('purchase_order.po_id','purchase_order.po_sequence','purchase_order.po_number','purchase_order.po_date','purchase_order.supplier_id','purchase_order.person_name','purchase_order.order_by','purchase_order.to_location_id','purchase_order.delivery_date','purchase_order.gst','purchase_order.test_certificate','purchase_order.order_acceptance','purchase_order.prepared_by','purchase_order.special_notes','purchase_order.is_approved','purchase_order.po_form_type')->where('po_id','=',$request->id)->first();
        // $purchase_order = PurchaseOrder::where('po_id','=',$request->id)->first();

        
            $purchase_order->po_date = Date::createFromFormat('Y-m-d', $purchase_order->po_date)->format('d/m/Y');
           
            if($purchase_order->delivery_date != "0000-00-00" && $purchase_order->delivery_date != null)
            {
                $purchase_order->delivery_date = Date::createFromFormat('Y-m-d', $purchase_order->delivery_date)->format('d/m/Y');
                }else{
                    $purchase_order->delivery_date = "";
                }

            // $purchase_order_details = PurchaseOrderDetails::where('po_id','=',$request->id)->get();
            $purchase_order_details = PurchaseOrderDetails::select(['purchase_order_details.po_details_id','purchase_order_details.po_id','purchase_order_details.item_id','purchase_order_details.po_qty','purchase_order_details.rate_per_unit','purchase_order_details.discount','purchase_order_details.amount','purchase_order_details.del_date','purchase_order_details.remarks','purchase_order_details.pr_details_id', 'items.item_name', 'items.item_code','units.unit_name','purchase_requisition_details.pr_details_id','purchase_requisition.pr_id','location_stock.stock_qty',])
            ->leftJoin('purchase_requisition_details','purchase_requisition_details.pr_details_id','=',
            'purchase_order_details.pr_details_id')
            ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')
            ->leftJoin('items', 'items.id', '=', 'purchase_order_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->leftJoin('location_stock', function ($join) use ($locationCode) {
                $join->on('location_stock.item_id', '=', 'purchase_requisition_details.item_id')
                     ->where('location_stock.location_id', '=', $locationCode->id); 
            })
            ->where('po_id','=',$request->id)->get();

            // dd($purchase_order_details);
            // purchase order to grn qty disable logic
            if($purchase_order_details != null){

                    $purchase_order_details->each(function ($item) use (&$isAnyPartInUse) {
                    // dd($item);

                    $total_grn_qty = GRNMaterialDetails::where('po_details_id','=',$item->po_details_id)->sum('grn_qty');
                    $total_po_short_qty = POShortClose::where('po_details_id','=',$item->po_details_id)->sum('sc_qty');
                    $isFound = $total_grn_qty + $total_po_short_qty;

                    // $isFound = GRNMaterialDetails::where('po_details_id','=',$item->po_details_id)->sum('grn_qty');

                    if($isFound != null){
                        $item->in_use = true;
                        $item->used_qty = $isFound;
                        $isAnyPartInUse = true;


                        if($item->used_qty > $item->po_qty){
                            $item->used_qty = $item->po_qty;
                        }

                    }else{
                        $item->in_use = false;
                        $item->used_qty = 0;

                    }
                    // dd($inUse);
                    return $item;

                })->values();
            }

            // ends 

                if ($purchase_order_details != null) {
                    foreach ($purchase_order_details as $poKey => $poVal) {
                        if ($poVal->del_date != null) {
                            $poVal->del_date = Date::createFromFormat('Y-m-d', $poVal->del_date)->format('d/m/Y');
                        }

                        // this is use to check edittime uncheck pr_details_id
                        $not_use_pr_details_id =  PurchaseRequisitionDetails::where('pr_details_id','!=',$poVal->pr_details_id)->where('pr_id',$poVal->pr_id)->where('supplier_id', $purchase_order->supplier_id)->pluck('pr_details_id');
                    }
                }
                // $purchase_order->in_use = false;
                if($purchase_order){
                    $purchase_order->in_use = false;
                    if($isAnyPartInUse == true){
                        $purchase_order->in_use = true;
                    }
                }
                

        if ($purchase_order_details) {
            return response()->json([
                'purchase_order_details' => $purchase_order_details,
                'purchase_order' => $purchase_order,
                'not_use_pr_details_id' => $not_use_pr_details_id,
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

    public function update(Request $request, PurchaseOrder $purchase_order)
    {
        
        
        DB::beginTransaction();
        
        try{
       
            $locationID  = getCurrentLocation()->id;
            $totalQty    = 0;
            $totalAmount = 0;
            $year_data   = getCurrentYearData();
            
            

            $validated = $request->validate(
                [
                    'po_sequence' => ['required','max:155',Rule::unique('purchase_order')->where(function ($query) use ($request,$year_data, $locationID) {                  
                        return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                    })->ignore($request->id, 'po_id')],
                    
    
                    'po_number' => ['required', 'max:155', Rule::unique('purchase_order')->where(function ($query) use ($request, $year_data, $locationID) {                 
                        return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                    })->ignore($request->id, 'po_id')],              
                ],
                [
                    'po_sequence.unique'=>'PO Sequence Is Already Exists',    
                    'po_number.required' => 'Please Enter PO Number',
                    'po_number.max' => 'Maximum 155 Characters Allowed',
                    ]
                );
                
                // dd("1ef");

       
      
        
    
            foreach ($request->item_id as $ctKey => $ctVal) {
                if ($ctVal != null) {
                    $totalQty += $request->po_qty[$ctKey];  
                    $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";        
                }
            }

                    $po_form_type = PurchaseOrder::where('po_id',$request->id)->pluck('po_form_type')->first();

                    $purchase_order =  PurchaseOrder::where('po_id',$request->id)->update([
                    'current_location_id' =>$locationID,
                    'po_sequence'         => $request->po_sequence,
                    'po_number'           => $request->po_number,
                    'po_date'             => Date::createFromFormat('d/m/Y', $request->po_date)->format('Y-m-d'),
                    'supplier_id'         => $request->supplier_id,
                    'person_name'         => $request->person,
                    'order_by'           => $request->order_by,
                    //'ref_no'              => $request->ref_no,
                   // 'ref_date'           => $request->ref_date != "" ? Date::createFromFormat('d/m/Y', $request->ref_date)->format('Y-m-d') : null,
                    'to_location_id'  => $request->ship_to,
                    'delivery_date'      => $request->check_date != "" ? Date::createFromFormat('d/m/Y', $request->check_date)->format('Y-m-d') : null,
                    'total_qty'           => $totalQty,
                    'total_amount'        => $totalAmount,
                    'pf_charge'           => $request->pf_charge,
                    'frieght'             => $request->freight,
                    'gst'                 => $request->gst,
                    'test_certificate'    => $request->test_certificate,
                    'order_acceptance'    => $request->order_acceptance,
                    'prepared_by'         => $request->prepared_by,
                    'payment_terms'       => $request->payment_terms,
                    'special_notes'       => $request->sp_notes,
                    'is_approved'         => 0,
                    'po_form_type'        => $po_form_type,
                    'year_id'             => $year_data->id,
                    'company_id'          => Auth::user()->company_id,
                    'last_by_user_id'     => Auth::user()->id,
                    'last_on'             => Carbon::now('Asia/Kolkata')->toDateTimeString(),               
                ]);
                if($purchase_order){

                      // this cose use to stock maintain
                      $oldPoDetails = PurchaseOrderDetails::where('po_id','=',$request->id)->get();
                      $oldPoDetailsData = [];
                      if($oldPoDetails != null){
                          $oldPoDetailsData = $oldPoDetails->toArray();
                      }
 
                    if (isset($request->purchase_order_detail_id) && !empty($request->purchase_order_detail_id)) {
                        
                        foreach ($request->purchase_order_detail_id as $sodKey => $sodVal) {
                            if($sodVal == "0"){
                                if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                    $purchase_order_details=  PurchaseOrderDetails::create([
                                        'po_id'         => $request->id,
                                        'item_id'       => $request->item_id[$sodKey],
                                        'po_qty'        => $request->po_qty[$sodKey],
                                        'rate_per_unit' => $request->rate_unit[$sodKey],
                                        'discount'      => $request->discount[$sodKey] != "" ? $request->discount[$sodKey] : 0.00,
                                        'amount'        => $request->amount[$sodKey],
                                        'del_date'      => Date::createFromFormat('d/m/Y', $request->del_date[$sodKey])->format('Y-m-d'),
                                        'remarks'       => $request->remarks[$sodKey],
                                        'pr_details_id'  => $request->pr_details_id[$sodKey]
                                    ]);
                                }
                                }else{
                                    if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                        $purchase_order_details =  PurchaseOrderDetails::where('po_details_id',$sodVal)->update([
                                            'item_id'       => $request->item_id[$sodKey],
                                            'po_qty'        => $request->po_qty[$sodKey],
                                            'rate_per_unit' => $request->rate_unit[$sodKey],
                                            'discount'      => $request->discount[$sodKey] != "" ? $request->discount[$sodKey] : 0.00,
                                            'amount'        => $request->amount[$sodKey],
                                            'del_date'      => Date::createFromFormat('d/m/Y', $request->del_date[$sodKey])->format('Y-m-d'),
                                            'remarks'       => $request->remarks[$sodKey],
                                            'pr_details_id'  => $request->pr_details_id[$sodKey]
                                        ]);

                                        foreach ($oldPoDetailsData as $key => $value) {
                                            if ($value['item_id'] == $request->item_id[$sodKey] && $value['pr_details_id'] == $request->pr_details_id[$sodKey]) {
                                                unset($oldPoDetailsData[$key]);

                                            }
                                        }      
                                    }
                                    // else{
                                    //     PurchaseOrderDetails::where('po_details_id', $sodVal)->delete();
                                    // }
                                }
                         }

                         if(isset($oldPoDetailsData) && !empty($oldPoDetailsData)){
                            foreach($oldPoDetailsData as $gkey=>$gval){                                 
                                PurchaseOrderDetails::where('po_details_id', $gval['po_details_id'])->delete();
                            }
                          }
                    }
                    DB::commit();
                    //  $pdf_name = 'Purchase_Order_'.str_replace("/", "_",$request->po_number);
                    //  GeneratePdf($request->id,$pdf_name,'po','','edit');
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
                    getActivityLogs("Purchase Order", "update", $e->getMessage(),$e->getLine());
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

         
            $grn_data = GRNMaterialDetails::
            leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
            ->where('purchase_order_details.po_id',$request->id)->get();
            if($grn_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, PO Is Used In GRN.",
                ]);
            }

            $po_short_data = POShortClose::
            leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','purchase_order_short_close.po_details_id')
            ->where('purchase_order_details.po_id',$request->id)->get();
            if($po_short_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, PO Is Used In PO Short Close.",
                ]);
            }


            $poData = PurchaseOrder::where('po_id','=',$request->id)->where('is_approved','=',1)->first();

            if($poData != null){
                return response()->json([
                    'response_code' => '0',
                    // 'response_message' => "This Is Used Somewhere, You Can't Delete",
                    'response_message' => "You Can't Delete, PO Is Approved.",
                ]);

            }  


            PurchaseOrder::where('po_id','=',$request->id)->delete();
            // $po_data = PurchaseOrderDetails::where('po_id','=',$request->id)->get();
          
            // foreach ($po_data as $ctKey => $ctVal) {
            //     POShortClose::where('po_details_id', '=', $ctVal->po_details_id)->delete();
            // }

            PurchaseOrderDetails::where('po_id',$request->id)->delete();
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){     
            // dd($e);
            DB::rollBack();    
            getActivityLogs("Purchase Order", "delete", $e->getMessage(),$e->getLine());  
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

    public function getContactPerson(Request $request){
        $contact_person = Supplier::select('id','contact_person')->where('id',$request->supplier_id)->first();
        return response()->json([
            'response_code' => 1,
            'contact_person' => $contact_person
        ]);
    }

    public function getLastSupplierData(Request $request){
        $fetch = PurchaseOrder::where('supplier_id',$request->supplier_id)->max('po_id');
        $last_data = PurchaseOrder::where('po_id',$fetch)->first();
        return response()->json([
            'response_code' => 1,
            'last_data' => $last_data
        ]);
    }

    public function previewPurchseOrder(Request $request, $id){
        $id = $request->id;
        return view('preview.puchase_order')->with(['id' => $id]);
    }



public function isPartInUse(Request $request){
    if(isset($request->po_part_id) && $request->po_part_id != ""){

        $isFound = null;

        $isAllow = PurchaseOrderDetails::where('po_id','=',$request->po_id)->where('po_details_id','=',$request->po_part_id)->first();

        if($isAllow != null){

            $isFound =  GRNMaterialDetails::where('po_details_id','=',$request->po_part_id)->first();

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
public function getItemsFromMapping(Request $request){
    $location = getCurrentLocation();    
    if(isset($request->id)){
        $changedItemIds = SupplierRejectoionDetails::
        leftJoin('items', 'items.id', '=', 'supplier_rejection_challan_details.item_id')
        ->where('supplier_rejection_challan_details.src_id',$request->id)
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes')
                ->Where('items.secondary_unit','No');
        })
        ->pluck('supplier_rejection_challan_details.item_id')
        ->toArray(); 


        $mappedItems = SupplierItemMapping::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name','items.secondary_unit',
    DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE items.id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),)
        ->leftJoin('items', 'items.id', 'supplier_item_mapping.item_id')
        ->Join('item_groups','item_groups.id','=','items.item_group_id')
        ->Join('units','units.id','=','items.unit_id')
        ->where(function ($query) use ($changedItemIds) {
            $query->where('items.status', 'active')
                  ->where('items.service_item', 'No')
                  ->Where('items.secondary_unit','No')
                  ->orWhereIn('items.id', $changedItemIds);
        })
        ->where('supplier_id', $request->supplier_id)
        ->get();  
    }else{
        $mappedItems = SupplierItemMapping::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name','items.secondary_unit',
    DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE items.id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),)
        ->leftJoin('items','items.id' ,'supplier_item_mapping.item_id')
        ->Join('item_groups','item_groups.id','=','items.item_group_id')
        ->Join('units','units.id','=','items.unit_id')
        ->where('items.status', 'active')
        ->where('items.service_item','No')  
        ->Where('items.secondary_unit','No')    
        ->where('supplier_id',$request->supplier_id)
        ->get();  
    }

  
    
  
   
    return response()->json([
        'response_code' => 1,
        'mappedItems' => $mappedItems
    ]);
}

public function getRatePerUnit(Request $request){
    $fetch = PurchaseOrder::where('supplier_id',$request->supplier_id)->max('po_id');
    $last_details = PurchaseOrderDetails::where('po_id',$fetch)->where('item_id',$request->item)->first();
    return response()->json([
        'response_code' => 1,
        'last_details' => $last_details
    ]);
}
public function existsOrderBy(Request $request){
    if($request->term != ""){
        $fdOrderBy = PurchaseOrder::select('order_by')->where('order_by', 'LIKE', $request->term.'%')->groupBy('order_by')->get();
        if($fdOrderBy != null){
            // $output = [];

            // foreach($fdState as $dsKey){
            //     array_push($output ,$dsKey->state);
            // }
            $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
            foreach($fdOrderBy as $dsKey){

                $output .= '<li parent-id="order_by" list-id="order_by_list" class="list-group-item" tabindex="0">'.$dsKey->order_by.'</li>';
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
public function existsPreparedBy(Request $request){
    $locationName = getCurrentLocation();
    if($request->term != ""){
        $fdPreparedBy = PurchaseOrder::select('prepared_by')->where('prepared_by', 'LIKE', $request->term.'%')->where('current_location_id',$locationName->id)->groupBy('prepared_by')->get();
        if($fdPreparedBy != null){
            // $output = [];

            // foreach($fdState as $dsKey){
            //     array_push($output ,$dsKey->state);
            // }
            $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
            foreach($fdPreparedBy as $dsKey){

                $output .= '<li parent-id="order_by" list-id="prepared_by_list" class="list-group-item" tabindex="0">'.$dsKey->prepared_by.'</li>';
            }
            $output .= '</ul>';

            return response()->json([
                'preparedByList' => $output,
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

public function getPrSupplierData(Request $request){

    $yearIds = getCompanyYearIdsToTill();
    $locationCode = getCurrentLocation();

    // $get_pr_supplier = PurchaseRequisitionDetails::select(
    // 'suppliers.supplier_name','suppliers.id',
    // DB::raw("((SELECT IFNULL(SUM(prd.req_qty), 0) FROM purchase_requisition_details AS prd WHERE prd.pr_id = purchase_requisition.pr_id) - (SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) FROM purchase_requisition_short_close AS prsc WHERE prsc.pr_details_id IN ( SELECT prd.pr_details_id FROM purchase_requisition_details AS prd
    // WHERE prd.pr_id = purchase_requisition.pr_id)) - (SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.pr_details_id IN ( SELECT prd.pr_details_id
    // FROM purchase_requisition_details AS prd WHERE prd.pr_id = purchase_requisition.pr_id))) AS pend_pr_qty"), )    
    // ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')
    // ->leftJoin('suppliers','suppliers.id', 'purchase_requisition_details.supplier_id')
    // ->where('purchase_requisition.current_location_id',$locationCode->id)
    // ->whereIn('purchase_requisition.year_id',$yearIds)               
    // ->having('pend_pr_qty','>',0)
    // // ->groupBY('suppliers.id')
    // ->get();
    
    // $get_pr_supplier = $get_pr_supplier->unique(['id']);
    // $get_pr_supplier = $get_pr_supplier->values()->all();
    
    $get_pr_supplier = PurchaseRequisitionDetails::select(
    'suppliers.supplier_name','suppliers.id',    
    // DB::raw("SUM((IFNULL(purchase_requisition_details.req_qty, 0) -(SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) 
    // FROM purchase_requisition_short_close AS prsc WHERE prsc.pr_details_id = purchase_requisition_details.pr_details_id)) - (SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id)) AS pend_pr_qty"),)

    DB::raw("(IFNULL(SUM(purchase_requisition_details.req_qty), 0) - (SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) FROM purchase_requisition_short_close AS prsc WHERE prsc.pr_details_id = purchase_requisition_details.pr_details_id)) - (SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id) AS pend_pr_qty"),)
    
    ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')
    ->leftJoin('suppliers','suppliers.id', 'purchase_requisition_details.supplier_id')
    ->where('purchase_requisition.current_location_id',$locationCode->id)
    ->whereIn('purchase_requisition.year_id',$yearIds)
    ->groupBy('suppliers.id', 'suppliers.supplier_name','purchase_requisition_details.pr_details_id')               
    ->having('pend_pr_qty','>',0)    
    ->get();     

    $get_pr_supplier = $get_pr_supplier->unique(['id']);

    $get_pr_supplier = $get_pr_supplier->values()->all();


    return response()->json([
        'response_code' => 1,
        'get_pr_supplier'  => $get_pr_supplier,
    ]);
}

public function getPrListForPO(Request $request){

    $contact_person = Supplier::select('id','contact_person')->where('id',$request->po_supplier_id)->first();
    
    $yearIds = getCompanyYearIdsToTill();
    $locationCode = getCurrentLocation();

    if(isset($request->id)){
        $edit_pr_data = PurchaseOrderDetails::select(['purchase_requisition.pr_id','purchase_requisition.pr_number','purchase_requisition.pr_date','purchase_requisition.pr_form_value_fix','purchase_requisition.to_location_id','locations.location_name'])
        ->leftJoin('purchase_requisition_details','purchase_requisition_details.pr_details_id','=',
        'purchase_order_details.pr_details_id')
        ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id') 
        ->leftJoin('locations','locations.id', 'purchase_requisition.to_location_id')   
        ->where('purchase_order_details.po_id',$request->id)->get();
    }

    $pr_data = PurchaseRequisitionDetails::select(['purchase_requisition.pr_id','purchase_requisition.pr_number','purchase_requisition.pr_date','purchase_requisition.pr_form_value_fix','purchase_requisition.to_location_id','locations.location_name',
    // DB::raw("SUM(IFNULL(purchase_requisition_details.req_qty, 0) - (SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) 
    // FROM purchase_requisition_short_close AS prsc WHERE prsc.pr_details_id = purchase_requisition_details.pr_details_id) - (SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id)) AS pend_pr_qty"),
    
    DB::raw("(IFNULL(SUM(purchase_requisition_details.req_qty), 0) - (SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) FROM purchase_requisition_short_close AS prsc WHERE prsc.pr_details_id = purchase_requisition_details.pr_details_id)) - (SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id) AS pend_pr_qty"),

    ])
    ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')  
    ->leftJoin('locations','locations.id', 'purchase_requisition.to_location_id')  
    ->where('purchase_requisition_details.supplier_id', $request->po_supplier_id)
    ->where('purchase_requisition.current_location_id',$locationCode->id)
    ->whereIn('purchase_requisition.year_id',$yearIds)  
    ->groupBY('purchase_requisition.pr_id','purchase_requisition_details.pr_details_id')   
    ->having('pend_pr_qty','>',0) 
    ->get();


    if(isset($edit_pr_data)){
        $data = collect($pr_data)->merge($edit_pr_data);
        $grouped = $data->groupBy('pr_id');            

        $merged = $grouped->map(function ($items) {
            return $items->reduce(function ($carry, $item) {
                if (!$carry) {
                    return $item;
                }                
                return $carry;
            });
        });

        $pr_data = $merged->values();   

    }



    if ($pr_data != null) {
        foreach ($pr_data as $cpKey => $cpVal) {

            if ($cpVal->pr_date != null) {
                $cpVal->pr_date = Date::createFromFormat('Y-m-d', $cpVal->pr_date)->format('d/m/Y');
            }      
            
            // $pr_details_id = PurchaseRequisitionDetails::select('purchase_requisition_details.pr_details_id')->where('purchase_requisition_details.supplier_id',$request->po_supplier_id)->get();
            // $po_details_id = PurchaseOrderDetails::select('purchase_order_details.po_details_id')->whereIn('purchase_order_details.pr_details_id',$pr_details_id)->get();
            // $grnData = GRNMaterialDetails::whereIn('material_receipt_grn_details.po_details_id',$po_details_id)->get();
// dd($grnData);
//             if($grnData != null){
//                 $cpVal->in_use = true;
//             }else{
//                 $cpVal->in_use = false;
//             }

    //         $grnExists = GRNMaterialDetails::leftJoin('purchase_order_details', 'purchase_order_details.po_details_id', '=', 'material_receipt_grn_details.po_details_id')
    // ->leftJoin('purchase_requisition_details', 'purchase_requisition_details.pr_details_id', '=', 'purchase_order_details.pr_details_id')
    // ->where('purchase_requisition_details.supplier_id', $request->po_supplier_id)
    // ->exists(); // Use exists() for better performance

    // $cpVal->in_use = $grnExists;
          
        }
    }

    if($pr_data->isNotEmpty()){
        $pr_data = $pr_data->unique(['pr_number']);
    }
    $pr_data = $pr_data->sortBy('pr_id')->values();
    if ($pr_data != null) {
        return response()->json([
            'response_code' => 1,
            'pr_data'  => $pr_data,            
            'contact_person'  => $contact_person,            
        ]);
    }else{
        return response()->json([
            'response_code' => 1,
            'pr_data'  => [],            
        ]);

    }



}


public function getPrItemListForPo(Request $request)
{
    $yearIds = getCompanyYearIdsToTill();
    $locationCode = getCurrentLocation();

    $request->chkPRId = explode(',', $request->chkPRId);


    if(isset($request->id)){
        $edit_pr_data = PurchaseOrderDetails::select(['purchase_requisition_details.pr_details_id', 'items.item_name' , 'items.item_code', 'units.unit_name','item_groups.item_group_name','purchase_requisition_details.req_qty','purchase_order_details.po_qty',
        ])
        ->leftJoin('purchase_requisition_details','purchase_requisition_details.pr_details_id','=',
        'purchase_order_details.pr_details_id')
        ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')
        ->leftJoin('items','items.id','=','purchase_order_details.item_id')          
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        ->leftJoin('units','units.id','=','items.unit_id')  
        ->where('purchase_order_details.po_id',$request->id)->get();
    }

    $pr_data = PurchaseRequisition::select(['purchase_requisition_details.pr_details_id', 'items.item_name' ,
    'items.item_code', 'units.unit_name','item_groups.item_group_name',
    'purchase_requisition_details.req_qty', 
    // DB::raw("(SUM(IFNULL(purchase_requisition_details.req_qty, 0)) - (SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) FROM purchase_requisition_short_close AS prsc WHERE prsc.pr_details_id = purchase_requisition_details.pr_details_id) - (SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod 
    // WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id) ) AS pend_pr_qty
    // ")
    DB::raw("(IFNULL(SUM(purchase_requisition_details.req_qty), 0) - (SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) FROM purchase_requisition_short_close AS prsc WHERE prsc.pr_details_id = purchase_requisition_details.pr_details_id)) - (SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id) AS po_qty"),
    ])
    ->leftJoin('purchase_requisition_details','purchase_requisition_details.pr_id','=','purchase_requisition.pr_id')
    ->leftJoin('items','items.id','=','purchase_requisition_details.item_id')          
    ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
    ->leftJoin('units','units.id','=','items.unit_id')
    ->where('purchase_requisition_details.supplier_id', $request->supplier_id)
    ->where('purchase_requisition.current_location_id',$locationCode->id)
    ->whereIn('purchase_requisition.year_id',$yearIds)  
    ->whereIn('purchase_requisition_details.pr_id',$request->chkPRId)  
    ->groupBy('purchase_requisition_details.pr_details_id')
    ->having('po_qty','>',0)      
    ->get();

    if(isset($edit_pr_data)){
        $data = collect($pr_data)->merge($edit_pr_data);
        $grouped = $data->groupBy('pr_details_id');            

        $merged = $grouped->map(function ($items) {
            return $items->reduce(function ($carry, $item) {
                if (!$carry) {
                    return $item;
                }                
                return $carry;
            });
        });

        $pr_data = $merged->values();   

    }

    $pr_data = $pr_data->sortBy('pr_details_id')->values();
    if ($pr_data != null) {
        return response()->json([
            'response_code' => 1,
            'pr_data'  => $pr_data,            
        ]);
    }else{
        return response()->json([
            'response_code' => 1,
            'pr_data'  => [],            
        ]);

    }

}


public function getPrPartDataForPo(Request $request){

    $yearIds = getCompanyYearIdsToTill();
    $locationCode = getCurrentLocation();

    $request->pr_ids = explode(',', $request->pr_ids);

    if(isset($request->id)){
        $edit_pr_data = PurchaseOrderDetails::select(['purchase_requisition_details.pr_details_id','purchase_requisition.pr_id','items.item_name' , 'items.item_code', 'units.unit_name','item_groups.item_group_name','purchase_order_details.po_details_id','purchase_order_details.po_qty','purchase_order_details.remarks','purchase_order_details.rate_per_unit','purchase_order_details.discount','purchase_order_details.del_date','purchase_order_details.amount','purchase_order_details.item_id','location_stock.stock_qty',])
        ->leftJoin('purchase_requisition_details','purchase_requisition_details.pr_details_id','=',
        'purchase_order_details.pr_details_id')
        ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')
        ->leftJoin('items','items.id','=','purchase_order_details.item_id')          
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('location_stock', function ($join) use ($locationCode) {
            $join->on('location_stock.item_id', '=', 'purchase_order_details.item_id')
                 ->where('location_stock.location_id', '=', $locationCode->id); 
        })
        ->whereIn('purchase_order_details.pr_details_id',$request->pr_ids)  
        ->where('purchase_order_details.po_id',$request->id)->get();
    }
    

    $pr_data =  PurchaseRequisition::select(['purchase_requisition_details.pr_details_id','purchase_requisition.pr_id','purchase_requisition_details.item_id','items.item_name','items.item_code', 'units.unit_name','item_groups.item_group_name','purchase_requisition_details.rate_per_unit', 'purchase_requisition.to_location_id',
    // 'purchase_requisition_details.req_qty as po_qty', 
    'purchase_requisition_details.remarks','location_stock.stock_qty',
    DB::raw("(IFNULL(SUM(purchase_requisition_details.req_qty), 0) - (SELECT IFNULL(SUM(prsc.pr_sc_qty), 0) FROM purchase_requisition_short_close AS prsc WHERE prsc.pr_details_id = purchase_requisition_details.pr_details_id)) - (SELECT IFNULL(SUM(pod.po_qty), 0) FROM purchase_order_details AS pod WHERE pod.pr_details_id = purchase_requisition_details.pr_details_id) AS po_qty"),   
    ])
    ->leftJoin('purchase_requisition_details','purchase_requisition_details.pr_id','=','purchase_requisition.pr_id')
    ->leftJoin('items','items.id','=','purchase_requisition_details.item_id')
    ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')   
    ->leftJoin('units','units.id','=','items.unit_id')
    ->leftJoin('location_stock', function ($join) use ($locationCode) {
        $join->on('location_stock.item_id', '=', 'purchase_requisition_details.item_id')
             ->where('location_stock.location_id', '=', $locationCode->id); 
    })
    ->whereIn('purchase_requisition_details.pr_details_id', $request->pr_ids)
    ->where('purchase_requisition.current_location_id',$locationCode->id)
    ->whereIn('purchase_requisition.year_id',$yearIds)  
    ->groupBy('purchase_requisition_details.pr_details_id')
    ->having('po_qty','>',0) 
    ->get();

    if(isset($edit_pr_data)){
        $data = collect($pr_data)->merge($edit_pr_data);
        $grouped = $data->groupBy('pr_details_id');            

        $merged = $grouped->map(function ($items) {
            return $items->reduce(function ($carry, $item) {
                if (!$carry) {
                    return $item;
                }                
                return $carry;
            });
        });

        $pr_data = $merged->values();   

    }
   
    if ($pr_data != null) {
        foreach ($pr_data as $cpKey => $cpVal) {
            if ($cpVal->del_date != null) {
                $cpVal->del_date = Date::createFromFormat('Y-m-d', $cpVal->del_date)->format('d/m/Y');
            }     
            
            $orderBy = PurchaseRequisitionDetails::select('prepared_by')
            ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')
            ->where('pr_details_id',$cpVal->pr_details_id)->first();

          
           
        }
    }
    

    $pr_data = $pr_data->sortBy('pr_details_id')->values();
    
    if ($pr_data != null) {
        return response()->json([
            'response_code' => '1',
            'pr_data' => $pr_data,
            'orderBy' => $orderBy
           
        ]);
    } else {
        return response()->json([
            'response_code' => '1',
            'pr_data' => []
        ]);
    }
}





}