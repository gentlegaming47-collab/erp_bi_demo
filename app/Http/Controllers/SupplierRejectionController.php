<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;
use App\Models\SupplierRejection;
use App\Models\SupplierRejectoionDetails;
use App\Models\Transporter;
use App\Models\QCApproval;


class SupplierRejectionController extends Controller
{
    public function manage()
    {
        return view('manage.manage-supplier_rejection');
    }

    public function index(SupplierRejection $SupplierRejection,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();

        $supplier_rej_challan_data = SupplierRejection::select(['src_sequence','src_number', 'src_date','suppliers.supplier_name','supplier_rejection_challan.created_by_user_id','supplier_rejection_challan.last_by_user_id','supplier_rejection_challan.created_on','supplier_rejection_challan.last_on',
        'supplier_rejection_challan.ref_no', 'supplier_rejection_challan.src_type_value_fix',
        'supplier_rejection_challan.ref_date','transporters.transporter_name', 'supplier_rejection_challan.vehicle_no','supplier_rejection_challan.lr_no_date', 'supplier_rejection_challan.special_notes',
        'supplier_rejection_challan.src_id','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])

        ->leftJoin('suppliers','suppliers.id','=','supplier_rejection_challan.supplier_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'supplier_rejection_challan.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'supplier_rejection_challan.last_by_user_id')
        ->leftJoin('transporters', 'transporters.id', '=', 'supplier_rejection_challan.transporter_id')
        ->where('supplier_rejection_challan.year_id','=',$year_data->id)
        ->where('supplier_rejection_challan.current_location_id','=',$location->id)
        ->groupBy('supplier_rejection_challan.src_number');
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $supplier_rej_challan_data->whereDate('supplier_rejection_challan.src_date','>=',$from);

                $supplier_rej_challan_data->whereDate('supplier_rejection_challan.src_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $supplier_rej_challan_data->where('supplier_rejection_challan.src_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $supplier_rej_challan_data->where('supplier_rejection_challan.src_date','<=',$to);

        }  

       return DataTables::of($supplier_rej_challan_data)


        ->editColumn('src_date', function($supplier_rej_challan_data){

            if ($supplier_rej_challan_data->src_date != null) {

                $formatedDate3 = Date::createFromFormat('Y-m-d', $supplier_rej_challan_data->src_date)->format(DATE_FORMAT); return $formatedDate3;

            }else{

                return '';

            }

        })

        ->editColumn('ref_date', function($supplier_rej_challan_data){

            if ($supplier_rej_challan_data->ref_date != null) {

                $formatedDate3 = Date::createFromFormat('Y-m-d', $supplier_rej_challan_data->ref_date)->format(DATE_FORMAT); return $formatedDate3;

            }else{

                return '';

            }

        })
        ->filterColumn('supplier_rejection_challan.src_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(supplier_rejection_challan.src_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('supplier_rejection_challan.ref_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(supplier_rejection_challan.ref_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })


        
        // ->editColumn('challan_qty', function($supplier_rej_challan_data){

        //     return $supplier_rej_challan_data->challan_qty > 0 ? number_format((float)$supplier_rej_challan_data->challan_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        // })
     

        // ->editColumn('challan_qty', function($supplier_rej_challan_data){

        //     return $supplier_rej_challan_data->challan_qty > 0 ? $supplier_rej_challan_data->challan_qty : 0;

        // })


        ->editColumn('created_by_user_id', function($supplier_rej_challan_data){
            if($supplier_rej_challan_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$supplier_rej_challan_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($supplier_rej_challan_data){
            if($supplier_rej_challan_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$supplier_rej_challan_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('created_on', function($supplier_rej_challan_data){
            if ($supplier_rej_challan_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $supplier_rej_challan_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('supplier_rejection_challan.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(supplier_rejection_challan.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($supplier_rej_challan_data){
            if ($supplier_rej_challan_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $supplier_rej_challan_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('supplier_rejection_challan.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(supplier_rejection_challan.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
   
        ->addColumn('options',function($supplier_rej_challan_data){
            $action = "<div>";
            if(hasAccess("supplier_rej_challan","print")){
            $action .="<a id='print_a' target='_blank' href='".route('print-supplier_rej_challan',['id' => base64_encode($supplier_rej_challan_data->src_id)])."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
            }
            if(hasAccess("supplier_rej_challan","edit")){
            $action .="<a id='edit_a' href='".route('edit-supplier_rej_challan',['id' => base64_encode($supplier_rej_challan_data->src_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("supplier_rej_challan","delete")){
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
        $getTransporter = Transporter::select('id','transporter_name') ->where('status', '=', 'active')->orderBy('transporter_name', 'asc')->get();
        
        return view('add.add-supplier_rejection')->with(['getTransporter' =>$getTransporter]);
    }

    public function store(Request $request)
    {
        // dd($request);        
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

          $existNumber = SupplierRejection::where('src_number','=',$request->src_number)->where('src_sequence','=',$request->src_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationID)->lockForUpdate()->first();
                   
          if($existNumber){
              $latestNo = $this->getLatestChallanNo($request);              
              $tmp =  $latestNo->getContent();
              $area = json_decode($tmp, true);
              $src_number =   $area['latest_po_no'];
              $src_sequence = $area['number'];
          }else{
             $src_number = $request->src_number;
             $src_sequence = $request->src_sequence;
          }       
          

          if($request->src_type_fix_id == 1 ){
              $srcType = "Manual" ;
          }elseif ($request->src_type_fix_id == 2){
              $srcType = "From QC";
          }
    
        
          // end check duplicate number
          DB::beginTransaction();
          try{
            $locationID = getCurrentLocation()->id;
            $totalQty = 0;
            $totalAmount = 0;
            $year_data = getCurrentYearData();

            foreach ($request->item_id as $ctKey => $ctVal) {
                if ($ctVal != null) {
                    $totalQty += $request->challan_qty[$ctKey];  
                    // $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";        
                }
            }

            $supplier_rejection =  SupplierRejection::create([
                'current_location_id'=>$locationID,
                'src_type_id_fix'      => $request->src_type_fix_id,
                'src_type_value_fix'      => $srcType,
                'src_sequence' => $src_sequence,
                'src_number' => $src_number,
                'src_date'  => Date::createFromFormat('d/m/Y', $request->src_date)->format('Y-m-d'),
                'supplier_id' => $request->supplier_id,                
                'ref_no'   => $request->ref_no,
                'ref_date' => $request->ref_date != "" ? Date::createFromFormat('d/m/Y', $request->ref_date)->format('Y-m-d') : null,
                'total_qty' => $totalQty,
                // 'total_amount'       => $totalAmount,
                'transporter_id' => $request->transporter_id ,
                'vehicle_no' => $request->vehicle_no ,
                'lr_no_date'  => $request->lr_no_date ,               
                'special_notes'   => $request->special_notes,
                'year_id'     => $year_data->id,
                'company_id'       => Auth::user()->company_id,
                'created_by_user_id' => Auth::user()->id,
                'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString()               
            ]);

            if($supplier_rejection->save())
            {
                
                foreach($request->item_id as $spKey => $spVal)
                {

                    if($request->src_type_fix_id == '2' && isset($request->qc_id[$spKey])){

                        $qcQtySum = QCApproval::where('qc_id',$request->qc_id[$spKey])->sum('reject_qty');

                        $useSrcQtySum = SupplierRejectoionDetails::where('qc_id',$request->qc_id[$spKey])->sum('challan_qty');

                        $srcQty = isset($request->challan_qty[$spKey]) && $request->challan_qty[$spKey] > 0 ? $request->challan_qty[$spKey] : 0;

                        $srcQtySum = $useSrcQtySum + $srcQty;

                        
                        if(number_format($qcQtySum, 3) < number_format($srcQtySum, 3)){
                            DB::rollBack();
                            return response()->json([
                                'response_code' => '0',
                                'response_message' => 'Challan Qty. Is Used',                               
                            ]);
                        }

                    }
                    
                    $supplier_rejection_details = SupplierRejectoionDetails::create([
                        'src_id'    => $supplier_rejection->src_id,
                        'item_id'   => isset($request->item_id[$spKey]) ? $request->item_id[$spKey] : "",
                        'challan_qty'   => isset($request->challan_qty[$spKey]) ? $request->challan_qty[$spKey] : "",
                        'remarks'   => isset($request->remarks[$spKey]) ? $request->remarks[$spKey] : "",
                        'qc_id'    => isset($request->qc_id[$spKey]) ? $request->qc_id[$spKey] : null,
                    ]);

                    // increaseStockQty($locationID,$spVal,-$request->challan_qty[$spKey]);
                    // decreaseStockQty($locationID,$spVal,$request->challan_qty[$spKey],0,'add');
                    if($request->src_type_fix_id == 1 ){
                    stockEffect($locationID,$spVal,$request->pre_item[$spKey],$request->challan_qty[$spKey],0,'add','D','Supplier Rejection',$supplier_rejection_details->src_details_id);
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
            
            getActivityLogs("Supplier Return Challan", "add", $e->getMessage(),$e->getLine());  
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

    public function show(SupplierRejection $supplierRejection, $id)
    {
        // $getData = SupplierRejection::where('src_id', '=', base64_decode($id))->get();
        // return view('edit.edit-supplier_rejection', compact('id'));

        $tId = DB::table('supplier_rejection_challan')
        ->where('src_id', base64_decode($id))
        ->value('transporter_id');

        $getTransporter = Transporter::select('transporters.id','transporters.transporter_name')
        ->where(function ($query) use ($tId) {
            $query->where('transporters.id', '=', $tId) 
            ->orWhere(function ($subQuery){
                $subQuery->where('transporters.status', '=', 'active');
            });
         
        })->orderBy('transporters.transporter_name', 'asc')->get();
        
        return view('edit.edit-supplier_rejection')->with(['id'=>$id,'getTransporter' =>$getTransporter]);
    }

    public function edit( Request $request, $id)
    {   
        $isAnyPartInUse = false;
        $location = getCurrentLocation();
        $supplier_rejection = SupplierRejection::select('supplier_rejection_challan.src_id','supplier_rejection_challan.src_type_id_fix','supplier_rejection_challan.src_sequence','supplier_rejection_challan.src_number','supplier_rejection_challan.src_date','supplier_rejection_challan.supplier_id','supplier_rejection_challan.ref_no','supplier_rejection_challan.ref_date','supplier_rejection_challan.supplier_id','supplier_rejection_challan.transporter_id','supplier_rejection_challan.vehicle_no','supplier_rejection_challan.lr_no_date','supplier_rejection_challan.special_notes')->where('src_id','=',$request->id)->first();
        //$supplier_rejection = SupplierRejection::where('src_id','=',$request->id)->first();

      
        

        if($supplier_rejection->ref_date != "" && $supplier_rejection->ref_date != null)
        {
            $supplier_rejection->ref_date = Date::createFromFormat('Y-m-d', $supplier_rejection->ref_date)->format('d/m/Y');
        }
        if( $supplier_rejection->src_date  != "" &&  $supplier_rejection->src_date  != null)
        {
            $supplier_rejection->src_date = Date::createFromFormat('Y-m-d', $supplier_rejection->src_date)->format('d/m/Y');
        }

      
        $supplier_rejection_details = SupplierRejectoionDetails::select(['units.unit_name','supplier_rejection_challan_details.*', 'items.item_code', 'item_groups.item_group_name as groupName',
        'location_stock.stock_qty','items.item_name' ,
        // DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE supplier_rejection_challan_details.item_id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),    
        ])
        ->leftJoin('items', 'items.id', 'supplier_rejection_challan_details.item_id')
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
        ->leftJoin('units', 'units.id', 'items.unit_id')
        ->leftJoin('location_stock', 'location_stock.item_id', 'supplier_rejection_challan_details.item_id')
        ->where('location_stock.location_id','=',$location->id)
        ->where('src_id','=',$request->id)->get();
        if($supplier_rejection->src_type_id_fix == 2){
            foreach ($supplier_rejection_details as $cpKey => $cpVal) {
                $qc_qty =  QCApproval::where('qc_approval.qc_id',$cpVal->qc_id)->sum('reject_qty');
                $challan_qty = SupplierRejectoionDetails::where('qc_id','=',$cpVal->qc_id)       
                ->sum('challan_qty');
                $cpVal->pend_qc_qty = $qc_qty - $challan_qty;           

                $qc_data =  QCApproval::select([ 'grn_material_receipt.grn_number','grn_material_receipt.grn_date',
                'purchase_order.po_number','purchase_order.po_date',])     
                ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','qc_approval.grn_details_id')
                ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
                ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
                ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')             
                ->where('qc_approval.qc_id', $cpVal->qc_id)            
                ->first();

                if ($qc_data->grn_date != null) {
                    $qc_data->grn_date = Date::createFromFormat('Y-m-d', $qc_data->grn_date)->format('d/m/Y');
                }             
              
                if ($qc_data->po_date != null) {
                    $qc_data->po_date = Date::createFromFormat('Y-m-d', $qc_data->po_date)->format('d/m/Y');
                }             

                $cpVal->grn_date =  $qc_data->grn_date;
                $cpVal->grn_number = $qc_data->grn_number;
                $cpVal->po_date = $qc_data->po_date;          
                $cpVal->po_number = $qc_data->po_number;          
            }

            $supplier_rejection_details = $supplier_rejection_details->sortBy('qc_id')
            ->values();
        }

        if($supplier_rejection_details != null){
            foreach($supplier_rejection_details as $cpKey => $cpVal){
                    if($supplier_rejection->src_date != null){
                    $date = Date::createFromFormat('d/m/Y', $supplier_rejection->src_date )->format('Y-m-d');
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
            if($supplier_rejection){
                $supplier_rejection->in_use = false;
                    if($isAnyPartInUse == true){
                        $supplier_rejection->in_use = true;
                    }
            }

        }

            

        if ($supplier_rejection_details) {
            return response()->json([
                'supplier_rejection_details' => $supplier_rejection_details,
                'supplier_rejection' => $supplier_rejection,
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
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

        $validated = $request->validate(
            [
                'src_sequence' => ['required','max:155',Rule::unique('supplier_rejection_challan')->where(function ($query) use ($request,$year_data, $locationID) {
                    return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'src_id')],

                'src_number' => ['required', 'max:155', Rule::unique('supplier_rejection_challan')->where(function ($query) use ($request, $year_data, $locationID) {
                    return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'src_id')],              
            ],
            [
                'src_sequence.unique'=>'Challan Number Is Already Exists',    
                'src_number.required' => 'Please Enter Challan Number',
                'src_number.max' => 'Maximum 155 Characters Allowed',
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
                        $totalQty += $request->challan_qty[$ctKey];                      
                    }
                }
            }

            if($request->src_type_fix_id == 1 ){
                $srcType = "Manual" ;
            }elseif ($request->src_type_fix_id == 2){
                $srcType = "From QC";
            }

            $supplier_rejection =  SupplierRejection::where('src_id','=',$request->id)->update([
                'current_location_id'=>$locationID,
                'src_type_id_fix'      => $request->src_type_fix_id,
                'src_type_value_fix'      => $srcType,
                'src_sequence'        => $request->src_sequence,
                'src_number'          => $request->src_number,
                'src_date'            => Date::createFromFormat('d/m/Y', $request->src_date)->format('Y-m-d'),
                'supplier_id'        => $request->supplier_id,                
                'ref_no'             => $request->ref_no,
                'ref_date'           => $request->ref_date != "" ? Date::createFromFormat('d/m/Y', $request->ref_date)->format('Y-m-d') : null,           
                'total_qty'          => $totalQty,
                // 'total_amount'       => $totalAmount,
                'transporter_id'       => $request->transporter_id ,
                'vehicle_no'       => $request->vehicle_no ,
                'lr_no_date'       => $request->lr_no_date ,               
                'special_notes'      => $request->special_notes,
                'year_id'            => $year_data->id,
                'company_id'         => Auth::user()->company_id,
                'last_by_user_id' => Auth::user()->id,
                'last_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString()               
            ]);


            if($supplier_rejection)
            {

                // this is use for stock maintain
                $oldSrcDetails = SupplierRejectoionDetails::where('src_id','=',$request->id)->get();
                $oldSrcDetailsData = [];
                if($oldSrcDetails != null){
                    $oldSrcDetailsData = $oldSrcDetails->toArray();
                }


                if (isset($request->supplier_rejection_id) && !empty($request->supplier_rejection_id)) {
                    foreach ($request->supplier_rejection_id as $sodKey => $sodVal) {           
                        if($sodVal == "0"){                                    
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){                              
                                
                                $supplier_details=   SupplierRejectoionDetails::create([
                                    'src_id'    => $request->id,
                                    'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                    'challan_qty'   => isset($request->challan_qty[$sodKey]) ? $request->challan_qty[$sodKey] : "",
                                    'remarks'   => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : "",
                                    'qc_id'    => isset($request->qc_id[$sodKey]) ? $request->qc_id[$sodKey] : null,
                                ]);

                                // increaseStockQty($locationID,$request->item_id[$sodKey],-$request->challan_qty[$sodKey]);

                                // decreaseStockQty($locationID,$request->item_id[$sodKey],$request->challan_qty[$sodKey],0,'add');

                                // stockEffect($locationID,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->challan_qty[$sodKey],0,'add','D');
                                if($request->src_type_fix_id == 1 ){
                                stockEffect($locationID,$request->item_id[$sodKey],$request->item_id[$sodKey],$request->challan_qty[$sodKey],0,'add','D','Supplier Rejection',$supplier_details->src_details_id);
                                }

                             
                            }
                        }else{                                                                    
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){  
                                $grn_order_details =  SupplierRejectoionDetails::where('src_details_id',$sodVal)->update([
                                'src_id'    => $request->id,
                                'item_id'   => isset($request->item_id[$sodKey]) ? $request->item_id[$sodKey] : "",
                                'challan_qty'   => isset($request->challan_qty[$sodKey]) ? $request->challan_qty[$sodKey] : "",
                                'remarks'   => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : "",
                                'qc_id'    => isset($request->qc_id[$sodKey]) ? $request->qc_id[$sodKey] : null,
                                ]);                                       
                                //  increaseStockQty($locationID,$request->item_id[$sodKey],($request->stock_qty[$sodKey] - $request->challan_qty[$sodKey] - $request->org_stock_qty[$sodKey]));

                                // decreaseStockQty($locationID,$request->item_id[$sodKey],$request->challan_qty[$sodKey],$request->org_challan_qty[$sodKey],'edit');
                                if($request->src_type_fix_id == 1 ){
                                stockEffect($locationID,$request->item_id[$sodKey],$request->pre_item[$sodKey],$request->challan_qty[$sodKey],$request->org_challan_qty[$sodKey],'edit','D','Supplier Rejection',$sodVal);
                                }

                                if($request->src_type_fix_id == 2){
                                    foreach ($oldSrcDetailsData as $key => $value) {
                                        if ($value['item_id'] == $request->item_id[$sodKey] && $value['qc_id'] == $request->qc_id[$sodKey]) {
                                            unset($oldSrcDetailsData[$key]);
                                            
                                        }
                                    }   
                                }else{                                       
                                    foreach ($oldSrcDetailsData as $key => $value) {
                                        if ($value['item_id'] == $request->item_id[$sodKey]) {
                                            unset($oldSrcDetailsData[$key]);

                                        }
                                    }   
                                }                                  
                            }else{
                                if($request->src_type_fix_id == 1){
                                    if(isset($oldSrcDetailsData) && !empty($oldSrcDetailsData)){
                                        foreach($oldSrcDetailsData as $gkey=>$gval){
                                            $qty = $gval['challan_qty'];
                                            // increaseStockQty($locationID,$gval['item_id'],$qty);
                                            
                                            // decreaseStockQty($locationID,$gval['item_id'],0,$qty,'delete');
                                            if($request->src_type_fix_id == 1 ){
                                            stockEffect($locationID,$gval['item_id'],$gval['item_id'],0,$qty,'delete','D','Supplier Rejection',$gval['src_details_id']);
                                            }
                                        }
                                    }

                                    SupplierRejectoionDetails::where('src_details_id', $sodVal)->delete();
                                }

                            }
                        }
                    }

                    if($request->src_type_fix_id == 2){
                        if(isset($oldSrcDetailsData) && !empty($oldSrcDetailsData)){
                          foreach($oldSrcDetailsData as $gkey=>$gval){                           
                            SupplierRejectoionDetails::where('src_details_id', $gval['src_details_id'])->delete();
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
            // DB::rollBack();
            // return response()->json([
            //     'response_code' => '0',
            //     'response_message' => 'Error Occured Record Not Inserted',
            //     'original_error' => $e->getMessage()
            // ]);

            
            DB::rollBack(); 
            getActivityLogs("Supplier Return Challan", "update", $e->getMessage(),$e->getLine());  
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
        try {
            $date = SupplierRejection::where('src_id',$request->id)->value('src_date');
            $src_type_fix_id = SupplierRejection::where('src_id',$request->id)->pluck('src_type_id_fix')->first();

            if($src_type_fix_id == 1){
                // this is use for stock maintain
                $locationID = getCurrentLocation()->id;
                $oldSrcDetails = SupplierRejectoionDetails::where('src_id','=',$request->id)->get();
                $oldSrcDetailsData = [];
                if($oldSrcDetails != null){
                    $oldSrcDetailsData = $oldSrcDetails->toArray();
                }

                foreach($oldSrcDetailsData as $gkey=>$gval){
                    $qty = $gval['challan_qty'];
                    $SecUnitBeforeUpdate =  LiveUpdateSecDate($date,$gval['item_id']);
                    if($SecUnitBeforeUpdate === true){                
                        DB::rollBack();     
                        return response()->json([
                            'response_code' => '0',
                            'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
                        ]);
                    }
                    // increaseStockQty($locationID,$gval['item_id'],$qty);
                    // decreaseStockQty($locationID,$gval['item_id'],0,$qty,'delete')
                    stockEffect($locationID,$gval['item_id'],$gval['item_id'],0,$qty,'delete','D','Supplier Rejection',$gval['src_details_id']);
                    
                }
            }         
            
            SupplierRejectoionDetails::where('src_id',$request->id)->delete();
            SupplierRejection::destroy($request->id);

            DB::commit();

            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            getActivityLogs("Supplier Return Challan", "delete", $e->getMessage(),$e->getLine());  
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
            }else if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }
            
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }

    public function getLatestChallanNo(Request $request)
    {
          $modal  =  SupplierRejection::class;
          $sequence = 'src_sequence';
          $prefix = 'REJ';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
        //   $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $sup_num_format['format'],
            'number'        => $sup_num_format['isFound'],
            // 'location'      => $locationName
        ]);

    }
    

    public function getsrcsupplier(Request $request){    
        
        $locationCode = getCurrentLocation();
        $yearIds = getCompanyYearIdsToTill();        
       
        if(isset($request->id)){
            $get_supplier = SupplierRejection::select('suppliers.supplier_name','suppliers.id',)
            ->leftJoin('suppliers','suppliers.id','=','supplier_rejection_challan.supplier_id')
            ->where('supplier_rejection_challan.src_id','=',$request->id)->get();
        }else{
            if($request->src_type_fix_id == 2){              
                $get_supplier = QCApproval::select(
                'suppliers.supplier_name','suppliers.id',
                DB::raw("(SELECT IFNUll(SUM(qc_approval.reject_qty),0) - (SELECT IFNULL(SUM(src.challan_qty),0)
                FROM supplier_rejection_challan_details AS src WHERE src.qc_id = qc_approval.qc_id)) as pend_qc_qty"),      
                )                 
                ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','qc_approval.grn_details_id')
                ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
                ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
                ->where('qc_approval.current_location_id',$locationCode->id)
                ->whereIn('qc_approval.year_id',$yearIds)      
                ->groupBy('qc_approval.qc_id')         
                ->having('pend_qc_qty','>',0)
                ->get();        

                $get_supplier = $get_supplier->unique('id')->values();                
     
             }else{     
                 $supplierIds = [];

                 $get_supplier = getSuppliers($supplierIds);
             }
        }
        
       
        
        return response()->json([
            'response_code' => 1,
            'get_supplier'  => $get_supplier,
        ]);
    }


    public function getQCListForSrc(Request $request){
        
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();
    
        if(isset($request->id)){         
        $edit_qc_data = SupplierRejectoionDetails::select([
        'qc_approval.qc_id','qc_approval.qc_number','qc_approval.qc_date','grn_material_receipt.grn_number','grn_material_receipt.grn_date','items.item_name' , 'items.item_code', 'units.unit_name','item_groups.item_group_name','supplier_rejection_challan_details.challan_qty as pend_qc_qty'    
        ])            
        ->leftJoin('qc_approval','qc_approval.qc_id','=','supplier_rejection_challan_details.qc_id')
        ->leftJoin('material_receipt_grn_details', 'material_receipt_grn_details.grn_details_id', '=', 'qc_approval.grn_details_id')
        ->leftJoin('grn_material_receipt', 'grn_material_receipt.grn_id', '=', 'material_receipt_grn_details.grn_id')
        ->leftJoin('items','items.id','=','supplier_rejection_challan_details.item_id')          
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        ->leftJoin('units','units.id','=','items.unit_id')  
        ->groupBY('qc_approval.qc_id')
        ->where('supplier_rejection_challan_details.src_id',$request->id)->get();

        }
    
        $qc_data = QCApproval::select(['qc_approval.qc_id','qc_approval.qc_number','qc_approval.qc_date','grn_material_receipt.grn_number','grn_material_receipt.grn_date','items.item_name' , 'items.item_code', 'units.unit_name','item_groups.item_group_name',
         DB::raw("IFNULL(SUM(qc_approval.reject_qty), 0) - IFNULL((SELECT SUM(src.challan_qty) FROM supplier_rejection_challan_details AS src WHERE src.qc_id = qc_approval.qc_id), 0) 
         AS pend_qc_qty")   
        ])     
        ->leftJoin('material_receipt_grn_details', 'material_receipt_grn_details.grn_details_id', '=', 'qc_approval.grn_details_id')
        ->leftJoin('grn_material_receipt', 'grn_material_receipt.grn_id', '=', 'material_receipt_grn_details.grn_id')
        ->leftJoin('items','items.id','=','qc_approval.item_id')          
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        ->leftJoin('units','units.id','=','items.unit_id')  
        ->where('grn_material_receipt.supplier_id', $request->src_supplier_id)
        ->where('qc_approval.current_location_id', $locationCode->id)
        ->whereIn('qc_approval.year_id', $yearIds)
        ->groupBY('qc_approval.qc_id')   
        ->having('pend_qc_qty','>',0) 
        ->get();
    
    
        if(isset($edit_qc_data)){
            $data = collect($qc_data)->merge($edit_qc_data);
            $grouped = $data->groupBy('qc_id');            
    
            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }              
                    $carry->pend_qc_qty += (float) $item->pend_qc_qty;  
                    return $carry;
                });
            });
    
            $qc_data = $merged->values();   
    
        }
    
    
    
        if ($qc_data != null) {
            foreach ($qc_data as $cpKey => $cpVal) {
    
                if ($cpVal->qc_date != null) {
                    $cpVal->qc_date = Date::createFromFormat('Y-m-d', $cpVal->qc_date)->format('d/m/Y');
                }          

                if ($cpVal->grn_date != null) {
                    $cpVal->grn_date = Date::createFromFormat('Y-m-d', $cpVal->grn_date)->format('d/m/Y');
                }             
              
            }
        }
    
        $qc_data = $qc_data->sortBy('qc_id')->values();
        if ($qc_data != null) {
            return response()->json([
                'response_code' => 1,
                'qc_data'  => $qc_data,            
            ]);
        }else{
            return response()->json([
                'response_code' => 1,
                'qc_data'  => [],            
            ]);
    
        }
    
    
    
    }


    public function getQCPartDataForSrc(Request $request){

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();
    
        $request->qc_ids = explode(',', $request->qc_ids);
    
        if(isset($request->id)){
        $edit_qc_data = SupplierRejectoionDetails::select([
        'qc_approval.qc_id','qc_approval.qc_number','qc_approval.qc_date','items.item_name' , 'items.item_code', 'units.unit_name','item_groups.item_group_name as groupName','location_stock.stock_qty',
        DB::raw("IFNULL(SUM(qc_approval.reject_qty), 0) - IFNULL((SELECT SUM(src.challan_qty) FROM supplier_rejection_challan_details AS src WHERE src.qc_id = qc_approval.qc_id), 0) 
        AS pend_qc_qty"),'supplier_rejection_challan_details.src_details_id',
        'supplier_rejection_challan_details.challan_qty',
        'qc_approval.rejection_reason as remarks','qc_approval.item_id',   'grn_material_receipt.grn_number','grn_material_receipt.grn_date', 'purchase_order.po_number','purchase_order.po_date',
        ])            
        ->leftJoin('qc_approval','qc_approval.qc_id','=','supplier_rejection_challan_details.qc_id')
        ->leftJoin('material_receipt_grn_details', 'material_receipt_grn_details.grn_details_id', '=', 'qc_approval.grn_details_id')
        ->leftJoin('grn_material_receipt', 'grn_material_receipt.grn_id', '=', 'material_receipt_grn_details.grn_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('items','items.id','=','supplier_rejection_challan_details.item_id')  
        ->leftJoin('location_stock', 'location_stock.item_id', 'supplier_rejection_challan_details.item_id')
        ->where('location_stock.location_id','=',$locationCode->id)        
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')           
        ->leftJoin('units','units.id','=','items.unit_id')  
        ->whereIn('qc_approval.qc_id',$request->qc_ids)  
        ->groupBY('qc_approval.qc_id')   
        ->where('supplier_rejection_challan_details.src_id',$request->id)->get();

        }

        $qc_data =  QCApproval::select(['qc_approval.qc_id','qc_approval.qc_number','qc_approval.qc_date','items.item_name' , 'items.item_code', 'units.unit_name','item_groups.item_group_name as groupName','location_stock.stock_qty',
        DB::raw("IFNULL(SUM(qc_approval.reject_qty), 0) - IFNULL((SELECT SUM(src.challan_qty) FROM supplier_rejection_challan_details AS src WHERE src.qc_id = qc_approval.qc_id), 0) 
        AS pend_qc_qty"),
        DB::raw('0 as challan_qty'),
        DB::raw('0 as src_details_id'),
        'qc_approval.rejection_reason as remarks','qc_approval.item_id',
        'grn_material_receipt.grn_number','grn_material_receipt.grn_date',
        'purchase_order.po_number','purchase_order.po_date',
        ])     
        ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','qc_approval.grn_details_id')
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('items','items.id','=','qc_approval.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')   
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('location_stock', 'location_stock.item_id', 'qc_approval.item_id')
        ->where('location_stock.location_id','=',$locationCode->id)
        ->whereIn('qc_approval.qc_id', $request->qc_ids)
        ->where('qc_approval.current_location_id', $locationCode->id)
        ->whereIn('qc_approval.year_id', $yearIds)
        ->groupBY('qc_approval.qc_id')   
        ->having('pend_qc_qty','>',0) 
        ->get();

        if(isset($edit_qc_data)){
            $data = collect($qc_data)->merge($edit_qc_data);
            $grouped = $data->groupBy('qc_id'); 
            
            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                   
                    if (!$carry) {
                        return $item;
                    }       
                    
                    $carry->challan_qty += (float) $item->challan_qty;
                    $carry->src_details_id += (float) $item->src_details_id;
                    return $carry;
                });
            });
    
            $qc_data = $merged->values();   
    
        }
      
        if ($qc_data != null) {
            foreach ($qc_data as $cpKey => $cpVal) {
    
                if ($cpVal->qc_date != null) {
                    $cpVal->qc_date = Date::createFromFormat('Y-m-d', $cpVal->qc_date)->format('d/m/Y');
                }          

                if ($cpVal->grn_date != null) {
                    $cpVal->grn_date = Date::createFromFormat('Y-m-d', $cpVal->grn_date)->format('d/m/Y');
                }             
              
                if ($cpVal->po_date != null) {
                    $cpVal->po_date = Date::createFromFormat('Y-m-d', $cpVal->po_date)->format('d/m/Y');
                }             
              
            }
        }
    
    
        $qc_data = $qc_data->sortBy('qc_id')->values();
        
        if ($qc_data != null) {
            return response()->json([
                'response_code' => '1',
                'qc_data' => $qc_data,         
               
            ]);
        } else {
            return response()->json([
                'response_code' => '1',
                'qc_data' => []
            ]);
        }
    }
    
}