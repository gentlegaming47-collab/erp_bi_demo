<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QCApproval;
use App\Models\GRNMaterialDetails;
use Illuminate\Support\Facades\DB;
use Date;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DataTables;
use App\Models\Admin;
use App\Models\SupplierRejectoionDetails;

class QCApprovalController extends Controller
{
    public function index(QCApproval $QCApproval,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();

        $qc_data = QCApproval::select(['qc_approval.qc_id','qc_approval.qc_number','qc_approval.qc_sequence','qc_approval.qc_date','qc_approval.ok_qty','qc_approval.qc_qty','qc_approval.reject_qty','qc_approval.rejection_reason','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','items.item_name' ,'items.item_code' ,'units.unit_name', 'item_groups.item_group_name','qc_approval.created_by_user_id','qc_approval.last_by_user_id','qc_approval.created_on','qc_approval.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','qc_approval.grn_details_id')
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
        ->leftJoin('items', 'items.id', 'qc_approval.item_id')
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'qc_approval.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'qc_approval.last_by_user_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('qc_approval.year_id','=',$year_data->id)
        ->where('qc_approval.current_location_id','=',$location->id);
       
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $qc_data->whereDate('qc_approval.qc_date','>=',$from);

                $qc_data->whereDate('qc_approval.qc_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $qc_data->where('qc_approval.qc_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $qc_data->where('qc_approval.qc_date','<=',$to);

        }  

       return DataTables::of($qc_data)      

       ->editColumn('created_by_user_id', function($qc_data){
           if($qc_data->created_by_user_id != null){
               $created_by_user_id = Admin::where('id','=',$qc_data->created_by_user_id)->first('user_name');
               return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
           }else{
               return '';
           }
       })
       ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('last_by_user_id', function($qc_data){
           if($qc_data->last_by_user_id != null){
               $last_by_user_id = Admin::where('id','=',$qc_data->last_by_user_id)->first('user_name');
               return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
           }else{
               return '';
           }

       })
       ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('created_on', function($qc_data){
           if ($qc_data->created_on != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $qc_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
           }else{
               return '';
           }
       })
       ->filterColumn('qc_approval.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(qc_approval.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
      
       ->editColumn('last_on', function($qc_data){
           if ($qc_data->last_on != null) {
               $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $qc_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
           }else{
               return '';
           }
       })
       ->filterColumn('qc_approval.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(qc_approval.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })

       ->editColumn('qc_date', function($qc_data){
            if ($qc_data->qc_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $qc_data->qc_date)->format(DATE_FORMAT); return $formatedDate3;
            }else{
                return '';
            }
        })
       ->editColumn('grn_date', function($qc_data){
            if ($qc_data->grn_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $qc_data->grn_date)->format(DATE_FORMAT); return $formatedDate3;
            }else{
                return '';
            }
        })
       ->editColumn('po_date', function($qc_data){
            if ($qc_data->po_date != null) {
                $formatedDate3 = Date::createFromFormat('Y-m-d', $qc_data->po_date)->format(DATE_FORMAT); return $formatedDate3;
            }else{
                return '';
            }
        })

        ->filterColumn('qc_approval.qc_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(qc_approval.qc_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('grn_material_receipt.grn_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(grn_material_receipt.grn_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('purchase_order.po_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.po_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->editColumn('ok_qty', function($qc_data) {
            return $qc_data->ok_qty > 0 
                ? number_format((float)$qc_data->ok_qty, 3, '.', '') 
                : number_format(0, 3, '.', ''); 
            })
        ->editColumn('qc_qty', function($qc_data) {
            return $qc_data->ok_qty > 0 
                ? number_format((float)$qc_data->qc_qty, 3, '.', '') 
                : number_format(0, 3, '.', ''); 
            })
        ->editColumn('reject_qty', function($qc_data) {
            return $qc_data->ok_qty > 0 
                ? number_format((float)$qc_data->reject_qty, 3, '.', '') 
                : number_format(0, 3, '.', ''); 
            })

       ->addColumn('options',function($qc_data){
           $action = "<div>";        
           if(hasAccess("purchase_requisition","edit")){
           $action .="<a id='edit_a' href='".route('edit-qc_approval',['id' => base64_encode($qc_data->qc_id )]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
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
        return view('manage.manage-qc_approval');
    }

    public function create()
    {
        return view('add.add-qc_approval');
    }


    public function store(Request $request)
    {
        // dd($request);
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

          $existNumber = QCApproval::where('qc_number','=',$request->qc_number)->where('qc_sequence','=',$request->qc_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationID)->lockForUpdate()->first();
                   
          if($existNumber){
              $latestNo    = $this->getLatestQCNo($request);              
              $tmp         =  $latestNo->getContent();
              $area        = json_decode($tmp, true);
              $qc_number   =   $area['latest_qc_no'];
              $qc_sequence = $area['number'];
          }else{
             $qc_number    = $request->qc_number;
             $qc_sequence  = $request->qc_sequence;
          }    

          DB::beginTransaction();
          try{

            if(isset($request->grn_details_id)){

                $grnQtySum = GRNMaterialDetails::where('grn_details_id',$request->grn_details_id)->sum('grn_qty');

                $useQcQtySum = QCApproval::where('grn_details_id',$request->grn_details_id)->sum('qc_qty');

                $qcQty = isset($request->qc_qty) && $request->qc_qty > 0 ? $request->qc_qty : 0;
                $qcQtySum = $useQcQtySum + $qcQty;                          

                if(number_format($grnQtySum, 3) < number_format($qcQtySum, 3)){
                    DB::rollBack();
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'GRN Qty. Is Used',                               
                    ]);
                }

            }
            $qc_data =  QCApproval::create([
                'qc_sequence'         => $qc_sequence,
                'qc_number'           => $qc_number,
                'qc_date'             => Date::createFromFormat('d/m/Y', $request->qc_date)->format('Y-m-d'),
                'grn_details_id'      => $request->grn_details_id,               
                'item_id'             => $request->item_id,               
                // 'item_details_id'     => $request->item_details_id,               
                'qc_qty'              => $request->qc_qty,               
                'ok_qty'              => $request->ok_qty,               
                'reject_qty'          => $request->reject_qty,               
                'rejection_reason'    => $request->rejection_reason,
                'current_location_id' => $locationID,
                'year_id'             => $year_data->id,
                'company_id'          => Auth::user()->company_id,
                'created_by_user_id'  => Auth::user()->id,
                'created_on'          => Carbon::now('Asia/Kolkata')->toDateTimeString()               
            ]);

            if($qc_data->save())
            {   
                if($request->item_details_id == null){

                    stockEffect($locationID,$request->item_id,$request->item_id,$request->ok_qty,0,'add','U','QC Approval',$qc_data->qc_id);
                }else{
                    stockDetailsEffect($locationID,$request->item_details_id,$request->item_details_id,$request->ok_qty,0,'add','U','QC Approval',$qc_data->qc_id,'Yes','QC Approval',$qc_data->qc_id );
                                
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
            getActivityLogs("QC Approval", "add", $e->getMessage(),$e->getLine());  

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
        return view('edit.edit-qc_approval')->with('id',$id);
    }


    public function edit(Request $request, $id)
    {
         $isAnyPartInUse = false;

        $qc_data = QCApproval::select(['qc_approval.qc_id','qc_approval.qc_sequence','qc_approval.qc_number','qc_approval.qc_date','qc_approval.ok_qty','qc_approval.item_id',
        // 'qc_approval.item_details_id',
        'qc_approval.rejection_reason'])->where('qc_id',$id)->first();

        $qc_details = QCApproval::select(['material_receipt_grn_details.grn_details_id','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','items.id as item_id','items.item_name' ,'items.item_code' ,'units.unit_name', 'item_groups.item_group_name',
        // 'qc_approval.item_details_id', 'item_details.secondary_item_name',
        'material_receipt_grn_details.grn_qty',
        DB::raw("(IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) - (SELECT IFNULL(SUM(qc_approval.qc_qty), 0) FROM qc_approval WHERE qc_approval.grn_details_id = material_receipt_grn_details.grn_details_id)) AS pend_grn_qty"),'qc_approval.qc_qty', 'qc_approval.ok_qty',       
        ])
        ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','qc_approval.grn_details_id')
        ->leftJoin('grn_secondary_details','grn_secondary_details.grn_details_id','=','material_receipt_grn_details.grn_details_id')
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
        ->leftJoin('items', 'items.id', 'material_receipt_grn_details.item_id')
        //  ->leftJoin('item_details','item_details.item_details_id','=','grn_secondary_details.item_details_id')
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('qc_approval.qc_id',$id)        
        ->get();

        foreach($qc_details as $key=>$val){
            
            if ($val->grn_date != null) {
                $val->grn_date = Date::createFromFormat('Y-m-d', $val->grn_date)->format('d/m/Y');
            }      
            if ($val->po_date != null) {
                $val->po_date = Date::createFromFormat('Y-m-d', $val->po_date)->format('d/m/Y');
            }      

            $newRequest = new Request();

            $newRequest->grn_details_id = $val->grn_details_id;
            $newRequest->record_id = $request->id;
            $newRequest->total_qty = $val->grn_qty;

            $val->show_pend_qty = self::getPendingQty($newRequest);
            $val->count = QCApproval::where('qc_approval.grn_details_id',$val->grn_details_id)->count();


            $isFound = SupplierRejectoionDetails::where('qc_id','=',$id)->sum('challan_qty');
            

            if($isFound || LiveUpdateSecDate($qc_data->qc_date,$val->item_id)){
                $val->in_use = true;
                $val->used_qty = $isFound;
                 $isAnyPartInUse = true;
            }else{
                $val->is_use = false;
                $val->used_qty = 0;
            }
        }

        if($qc_data->qc_date != ""){
            $qc_data->qc_date = Date::createFromFormat('Y-m-d', $qc_data->qc_date)->format('d/m/Y');
        }     

         if($qc_data){
            $qc_data->in_use = false;
            if($isAnyPartInUse == true){
                $qc_data->in_use = true;
            }
        }

       
        if ($qc_data) {
            return response()->json([
                'qc_details'       => $qc_details,
                'qc_data'          => $qc_data,
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


    public function update(Request $request)
    {   
        // dd($request);
        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

          
            DB::beginTransaction();

            try{
                $qc_data =  QCApproval::where('qc_id','=',$request->id)->update([
                    'qc_sequence'         => $request->qc_sequence,
                    'qc_number'           => $request->qc_number,
                    'qc_date'             => Date::createFromFormat('d/m/Y', $request->qc_date)->format('Y-m-d'),
                    'grn_details_id'      => $request->grn_details_id,               
                    'item_id'             => $request->item_id,               
                    'item_details_id'     => $request->item_details_id,               
                    'qc_qty'              => $request->qc_qty,               
                    'ok_qty'              => $request->ok_qty,               
                    'reject_qty'          => $request->reject_qty,               
                    'rejection_reason'    => $request->rejection_reason,
                    'current_location_id' => $locationID,
                    'year_id'             => $year_data->id,
                    'company_id'          => Auth::user()->company_id,
                    'last_by_user_id'  => Auth::user()->id,
                    'last_on'          => Carbon::now('Asia/Kolkata')->toDateTimeString()               
                ]);
    
                if($qc_data)
                { 
                    // normal as it is
                    if(($request->pre_item_details_id == null ||$request->pre_item_details_id== "") && ($request->item_details_id == null ||$request->item_details_id== "")){

                        stockEffect($locationID,$request->item_id,$request->pre_item_id,$request->ok_qty,$request->org_ok_qty,'edit','U','QC Approval',$request->id);
                    }
                    // details mathi normal 
                    else if(($request->pre_item_details_id != null ||$request->pre_item_details_id != "") && ($request->item_details_id == null ||$request->item_details_id== "")){
                        stockDetailsEffect($locationID,$request->pre_item_details_id,$request->pre_item_details_id,0,$request->org_ok_qty,'delete','U','QC Approval',$request->id,'Yes','QC Approval',$request->id);

                        stockEffect($locationID,$request->item_id,$request->item_id,$request->ok_qty,0,'add','U','QC Approval',$request->id);


                    }
                    // details as it is
                    else if(($request->pre_item_details_id != null ||$request->pre_item_details_id != "") && ($request->item_details_id != null || $request->item_details_id != "")){
                         stockDetailsEffect($locationID,$request->item_details_id,$request->pre_item_details_id,$request->ok_qty,$request->org_ok_qty,'edit','U','QC Approval',$request->id,'Yes','QC Approval',$request->id);
                    }
                    // normal to details
                    else{
                           stockEffect($locationID,$request->pre_item_id,$request->pre_item_id, 0, $request->org_ok_qty, 'delete', 'U', 'QC Approval',$request->id);

                           stockDetailsEffect($locationID, $request->item_details_id,$request->item_details_id,$request->ok_qty, 0, 'add', 'U', 'QC Approval',$request->id, 'Yes', 'QC Approval', $request->id);
                    }
        

                    DB::commit();
                    return response()->json([
                        'response_code' => '1',
                        'response_message' => 'Record Updated Successfully.',
                    ]);
                }else{
                    DB::rollBack(); 
                    return response()->json([
                        'response_code' => '1',
                        'response_message' => 'Record Not Updated.',
                    ]);
                }
            }
            catch(\Exception $e){

                DB::rollBack(); 
                getActivityLogs("QC Approval", "update", $e->getMessage(),$e->getLine());  
            
                if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => $e->getMessage(),
                    ]);
                }else{
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Error Occured Record Not Inserted',
                        'original_error' => $e->getLine()
                    ]);
                }
            }

    }



    public function getLatestQCNo(Request $request)
    {
          $modal  =  QCApproval::class;
          $sequence = 'qc_sequence';
          $prefix = 'QC';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_qc_no'  => $sup_num_format['format'],
            'number'        => $sup_num_format['isFound'],
            'location'      => $locationName
        ]);

    }


    public function getGrnDataForQc(Request $request){       
        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();
        
        if(isset($request->id)){
            $edit_grn_data = QCApproval::select(['qc_approval.qc_id','material_receipt_grn_details.grn_details_id','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','items.item_name' ,'items.item_code' , 'item_groups.item_group_name','material_receipt_grn_details.grn_qty',
            // 'item_details.item_details_id','item_details.secondary_item_name',
            DB::raw("(IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) - (SELECT IFNULL(SUM(qc_approval.qc_qty), 0) FROM qc_approval WHERE qc_approval.grn_details_id = material_receipt_grn_details.grn_details_id)) AS pend_grn_qty"),  'qc_approval.qc_qty',   'qc_approval.ok_qty',  
            'units1.unit_name  as unit_name' ,'items.secondary_unit',            
            ])
            ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','qc_approval.grn_details_id')
            ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
            ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
            ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
            ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
            ->leftJoin('items', 'items.id', 'material_receipt_grn_details.item_id')
            // ->leftJoin('item_details','item_details.item_details_id','=','qc_approval.item_details_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            // ->leftJoin('units','units.id','=','items.unit_id') 
            ->leftJoin('units as units1','units1.id','=','items.unit_id')
            ->leftJoin('units as units2','units2.id','=','items.second_unit')           
            ->where('qc_approval.qc_id',$request->id)->get();

        }


        $grn_data = GRNMaterialDetails::select(['material_receipt_grn_details.grn_details_id','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','items.item_name' ,'items.item_code', 'item_groups.item_group_name','material_receipt_grn_details.grn_qty',
        DB::raw("(IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) - (SELECT IFNULL(SUM(qc_approval.qc_qty), 0) FROM qc_approval WHERE qc_approval.grn_details_id = material_receipt_grn_details.grn_details_id)) AS pend_grn_qty"),
        // 'item_details.item_details_id','item_details.secondary_item_name',        
        DB::raw('0 as qc_qty'),
        DB::raw('0 as ok_qty'),
         'units1.unit_name  as unit_name' ,'items.secondary_unit', 
        // DB::raw("CASE WHEN item_details.item_details_id IS NOT NULL THEN units2.unit_name ELSE units1.unit_name END as unit_name")
        ])
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
        ->leftJoin('grn_secondary_details','grn_secondary_details.grn_details_id','=','material_receipt_grn_details.grn_details_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
        ->leftJoin('items', 'items.id', 'material_receipt_grn_details.item_id')
        // ->leftJoin('item_details','item_details.item_details_id','=','grn_secondary_details.item_details_id')
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
        // ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as units1','units1.id','=','items.unit_id')
        ->leftJoin('units as units2','units2.id','=','items.second_unit')
        ->where('material_receipt_grn_details.qc_required','=','Yes')
        ->where('grn_material_receipt.grn_type_id_fix','!=','3')
        ->where('grn_material_receipt.current_location_id',$locationCode->id)
        ->whereIn('grn_material_receipt.year_id',$yearIds)  
        ->groupBy('material_receipt_grn_details.grn_details_id')
        ->having('pend_grn_qty','>',0)   
        ->get();

        if(isset($edit_grn_data)){
            $data = collect($grn_data)->merge($edit_grn_data);
            $grouped = $data->groupBy('grn_details_id');    

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }

                    $carry->qc_qty += (float) $item->qc_qty;
                    $carry->ok_qty += (float) $item->ok_qty;
                    return $carry;
                });
            });
    
            $grn_data = $merged->values();   

        }

        
        foreach($grn_data as $key=>$val){
            
            if ($val->grn_date != null) {
                $val->grn_date = Date::createFromFormat('Y-m-d', $val->grn_date)->format('d/m/Y');
            }      
            if ($val->po_date != null) {
                $val->po_date = Date::createFromFormat('Y-m-d', $val->po_date)->format('d/m/Y');
            }      

            if(isset($request->id)){   

                $newRequest = new Request();

                $newRequest->grn_details_id = $val->grn_details_id;
                $newRequest->record_id = $request->id;
                $newRequest->total_qty = $val->grn_qty;

                $val->show_pend_qty = self::getPendingQty($newRequest);

            }else{
                $val->show_pend_qty = $val->pend_grn_qty;
            }

            if(isset($val->qc_id)){
                $isFound = SupplierRejectoionDetails::where('qc_id','=',$val->qc_id)->sum('challan_qty');
                if($isFound || $val->secondary_unit == 'Yes'){
                    $val->in_use = true;
                    $val->used_qty = $isFound;
                }else{
                    $val->is_use = false;
                    $val->used_qty = 0;
                }
            }else{
                $val->is_use = false;
                $val->used_qty = 0;
            }
        
        }


        if($grn_data->isNotEmpty()){
            return response()->json([
                'response_code' => 1,
                'grn_data'  => $grn_data,
            ]);
        }else{
            return response()->json([
                'response_code' => 0,
               
            ]);
        }

    }

    
    public function getGrnPartDataForQc(Request $request){

        $request->grn_details_ids = explode(',', $request->grn_details_ids);

        if(isset($request->id)){
            $edit_grn_data = QCApproval::select(['qc_approval.qc_id',
            'material_receipt_grn_details.grn_details_id','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','items.id as item_id','items.item_name','item_details.item_details_id','item_details.secondary_item_name','items.item_code' , 'item_groups.item_group_name','material_receipt_grn_details.grn_qty',
            DB::raw("(IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) - (SELECT IFNULL(SUM(qc_approval.qc_qty), 0) FROM qc_approval WHERE qc_approval.grn_details_id = material_receipt_grn_details.grn_details_id)) AS pend_grn_qty"), 'qc_approval.qc_qty' ,  'qc_approval.ok_qty' , DB::raw("CASE WHEN item_details.item_details_id IS NOT NULL THEN units2.unit_name ELSE units1.unit_name END as unit_name")   
            ])
            ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id','=','qc_approval.grn_details_id')
             ->leftJoin('grn_secondary_details','grn_secondary_details.grn_details_id','=','material_receipt_grn_details.grn_details_id')
            ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
            ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
            ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
            ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
            ->leftJoin('items', 'items.id', 'material_receipt_grn_details.item_id')
            ->leftJoin('item_details','item_details.item_details_id','=','grn_secondary_details.item_details_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            // ->leftJoin('units','units.id','=','items.unit_id')
            ->leftJoin('units as units1','units1.id','=','items.unit_id')
            ->leftJoin('units as units2','units2.id','=','items.second_unit')
            ->groupBy('material_receipt_grn_details.grn_details_id')
            ->whereIn('qc_approval.grn_details_id',$request->grn_details_ids)
            ->where('qc_approval.qc_id',$request->id)->get();
        }

        $grn_data = GRNMaterialDetails::select(['material_receipt_grn_details.grn_details_id','grn_material_receipt.grn_number','grn_material_receipt.grn_date','suppliers.supplier_name','purchase_order.po_number','purchase_order.po_date','items.id as item_id','items.item_name' ,'items.item_code' , 'item_groups.item_group_name','material_receipt_grn_details.grn_qty','item_details.item_details_id','item_details.secondary_item_name',
        DB::raw("(IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) - (SELECT IFNULL(SUM(qc_approval.qc_qty), 0) FROM qc_approval WHERE qc_approval.grn_details_id = material_receipt_grn_details.grn_details_id)) AS pend_grn_qty"),     DB::raw('0 as qc_qty'), DB::raw('0 as ok_qty'),  DB::raw("CASE WHEN item_details.item_details_id IS NOT NULL THEN units2.unit_name ELSE units1.unit_name END as unit_name")        
        ])
        ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
        ->leftJoin('grn_secondary_details','grn_secondary_details.grn_details_id','=','material_receipt_grn_details.grn_details_id')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('suppliers','suppliers.id','=','grn_material_receipt.supplier_id')
        ->leftJoin('items', 'items.id', 'material_receipt_grn_details.item_id')
        ->leftJoin('item_details','item_details.item_details_id','=','grn_secondary_details.item_details_id')
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
        // ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as units1','units1.id','=','items.unit_id')
        ->leftJoin('units as units2','units2.id','=','items.second_unit')
        ->where('material_receipt_grn_details.qc_required','=','Yes')
        ->whereIn('material_receipt_grn_details.grn_details_id',$request->grn_details_ids)      
        ->having('pend_grn_qty','>',0)      
        ->get();
        if(isset($edit_grn_data)){
            $data = collect($grn_data)->merge($edit_grn_data);
            $grouped = $data->groupBy('grn_details_id');    

            $merged = $grouped->map(function ($items) {
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }

                    $carry->qc_qty += (float) $item->qc_qty;
                    $carry->ok_qty += (float) $item->ok_qty;
                    return $carry;
                });
            });
    
            $grn_data = $merged->values();   

        }

        foreach($grn_data as $key=>$val){
            
            if ($val->grn_date != null) {
                $val->grn_date = Date::createFromFormat('Y-m-d', $val->grn_date)->format('d/m/Y');
            }      
            if ($val->po_date != null) {
                $val->po_date = Date::createFromFormat('Y-m-d', $val->po_date)->format('d/m/Y');
            }      

            if(isset($request->id)){   

                $newRequest = new Request();

                $newRequest->grn_details_id = $val->grn_details_id;
                $newRequest->record_id = $request->id;
                $newRequest->total_qty = $val->grn_qty;

                $val->show_pend_qty = self::getPendingQty($newRequest);

            }else{
                $val->show_pend_qty = $val->pend_grn_qty;
            }

            if(isset($val->qc_id)){
                $isFound = SupplierRejectoionDetails::where('qc_id','=',$val->qc_id)->sum('challan_qty');
                if($isFound){
                    $val->in_use = true;
                    $val->used_qty = $isFound;
                }else{
                    $val->is_use = false;
                    $val->used_qty = 0;
                }
            }else{
                $val->is_use = false;
                $val->used_qty = 0;
            }
        }


        if($grn_data != null){
            return response()->json([
                'response_code' => 1,
                'grn_data'  => $grn_data,
            ]);
        }

    }


    public function getPendingQty(Request $request){
        $exectQty = $request->total_qty;       
        $exectQty = number_format((float)$exectQty, 3, '.','');   

        $oldRecords = QCApproval::select(DB::raw('SUM(qc_qty) as sum'))
        ->where('grn_details_id','=',$request->grn_details_id)
        ->where('qc_id','<=',$request->record_id)
        ->groupBy(['grn_details_id'])
        ->first();        

        if($oldRecords != null){

            $diff = $exectQty - number_format((float)$oldRecords->sum, 3, '.','');
          
            return $diff;
        }else{
            return abs($exectQty);
        } 
        
    }


    public function existsRejectionReason(Request $request){
        if($request->term != ""){
            $fdOrderBy = QCApproval::select('rejection_reason')->where('rejection_reason', 'LIKE', $request->term.'%')->groupBy('rejection_reason')->get();
            if($fdOrderBy != null){
             
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdOrderBy as $dsKey){
    
                    $output .= '<li parent-id="rejection_reason" list-id="rejection_reason_list" class="list-group-item" tabindex="0">'.$dsKey->rejection_reason.'</li>';
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


    public function destroy(Request $request)
    {
        DB::beginTransaction();
        
        try{            

            $locationCode = getCurrentLocation();

            $src_data = SupplierRejectoionDetails::where('supplier_rejection_challan_details.qc_id',$request->id)->get();
            if($src_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, QC Approval  Is Used In Supplier Return Challan.",
                ]);
            }

            $qc_data = QCApproval::where('qc_id','=',$request->id)->first();  

            $SecUnitBeforeUpdate =  LiveUpdateSecDate($qc_data->qc_date,$qc_data->item_id);

            
            if($SecUnitBeforeUpdate === true){                
                DB::rollBack();     
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You can't delete.Please Contact CBS Webtech Solutions.",
                ]);
            }
             
            if($qc_data->item_details_id == null || $qc_data->item_details_id == 0){        
                stockEffect($locationCode->id,$qc_data->item_id,$qc_data->item_id,0,$qc_data->ok_qty,'delete','U','QC Approval',$request->id); 
            }else{
                stockDetailsEffect($locationCode->id,$qc_data->item_details_id,$qc_data->item_details_id,0,$qc_data->ok_qty,'delete','U','QC Approval',$request->id,'Yes','QC Approval',$request->id);
            }
                        
            QCApproval::destroy($request->id);

            DB::commit();
            
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){  
            
            DB::rollBack();
            getActivityLogs("QC Approval", "delete", $e->getMessage(),$e->getLine());  
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