<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\File;
use Illuminate\Validation\Rule;

use App\Models\SalesOrderDetail;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Item;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
use Str;
use App\Models\SalesOrderDetailsDetails;
use App\Models\DispatchPlanDetails;
use App\Models\DispatchPlanDetailsDetails;
use App\Models\SOShortClose;
use App\Models\SOMappingDetails;
use App\Models\Location;
use App\Models\LocationCustomerGroupMapping;
use App\Models\Dealer;
use App\Models\PriceListDetails;
use App\Models\DealerAgreement;
use App\Models\TransactionSOShortClose;
use App\Models\PurchaseRequisitionDetails;




class SaleOrderController extends Controller
{

    
    public function manage()

    {

        return view('manage.manage-sales_order');

    }


    public function index(SalesOrder $SalesOrder,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();


        $sales_order = SalesOrder::select([
        'sales_order.id',
        'sales_order.so_sequence',
        'sales_order.so_from_value_fix',
        'sales_order.so_number',
        'sales_order.customer_name',
        'locations.location_name',
        'sales_order.so_type_value_fix',
        'sales_order.file_upload','sales_order.so_date',
        'sales_order.created_on',
        'sales_order.created_by_user_id',
        'sales_order.last_by_user_id',
        'sales_order.last_on',
        'created_user.user_name as created_by_name',
        'last_user.user_name as last_by_name',
        // DB::raw('(CASE WHEN so_from_value_fix ="location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),
        'sales_order_details.so_qty',
        'sales_order_details.rate_per_unit',
        'sales_order_details.so_amount',
        'items.item_name',
        'items.item_code',
        'item_groups.item_group_name',
        'customer_groups.customer_group_name',
        'sales_order.customer_reg_no',
        'sales_order.customer_village',
        'sales_order.customer_taluka',
        'districts.district_name',
        'countries.country_name',
        'states.state_name',
        'units.unit_name',
        'dealers.dealer_name',
        'villages.village_name',
        'sales_order.net_amount',
        'talukas.taluka_name',
        'mis_category.mis_category',
        'sales_order.special_notes',
        ])

        ->leftJoin('customer_groups','customer_groups.id','=','sales_order.customer_group_id')
        ->leftJoin('sales_order_details','sales_order_details.so_id','=','sales_order.id')
        ->leftJoin('locations','locations.id','=','sales_order.to_location_id')
        ->leftJoin('items','items.id','=','sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('districts','districts.id','=','sales_order.customer_district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('talukas','talukas.id','=','sales_order.customer_taluka')
        ->leftJoin('villages','villages.id','=','sales_order.customer_village')      
        ->leftJoin('dealers','dealers.id','=','sales_order.dealer_id')
        ->leftJoin('mis_category','mis_category.id','=','sales_order.mis_category_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'sales_order.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'sales_order.last_by_user_id')
        ->where('current_location_id','=',$location->id)
        ->where('sales_order.year_id', '=', $year_data->id)
        ->groupBy('sales_order.so_number');
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $sales_order->whereDate('sales_order.so_date','>=',$from);

                $sales_order->whereDate('sales_order.so_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $sales_order->where('sales_order.so_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $sales_order->where('sales_order.so_date','<=',$to);

        }
        
        // dd($sales_order);

        return DataTables::of($sales_order)
        ->editColumn('created_by_user_id', function($sales_order){
            if($sales_order->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$sales_order->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($sales_order){
            if($sales_order->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$sales_order->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('item_name', function($sales_order){ 
            if($sales_order->item_name != ''){
                $item_name = ucfirst($sales_order->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

        ->editColumn('so_qty', function($sales_order){

            return $sales_order->so_qty > 0 || $sales_order->so_qty != ""  ? number_format((float)$sales_order->so_qty, 3, '.','') : '';
        })

        ->editColumn('rate_per_unit', function($sales_order){

            return $sales_order->rate_per_unit > 0 ? number_format((float)$sales_order->rate_per_unit, 2, '.','') : '';
        })
        ->editColumn('net_amount', function($sales_order){

            return $sales_order->net_amount > 0 ? number_format((float)$sales_order->net_amount, 2, '.','') : '';
        })
            
        ->editColumn('so_amount', function($sales_order){

            return $sales_order->so_amount > 0 ? number_format((float)$sales_order->so_amount, 2, '.','') : '';
        })
            
        ->editColumn('created_on', function($sales_order){
            if ($sales_order->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $sales_order->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('sales_order.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_order.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($sales_order){
            if ($sales_order->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $sales_order->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('sales_order.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_order.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($sales_order){
            $action = "<div>";
            if(hasAccess("sales_order","print")){
                $action .="<a id='print_a' target='_blank' href='".route('print-sales_order',['id' => base64_encode($sales_order->id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";

                $so_fiting_item = SalesOrderDetail::select(['sales_order_details.so_details_id','items.item_name',])
                ->leftJoin('items','items.id','=','sales_order_details.item_id')
                ->where('sales_order_details.fitting_item','yes')
                ->where('so_id',$sales_order->id)
                ->orderBy('items.item_name','asc')
                ->get();
            
                if($so_fiting_item->count() > 0){
                    $action .="<a id='print_a' target='_blank' href='".route('print-sales_order_fitting',['id' => base64_encode($sales_order->id)]) ."' data-placement='top' data-original-title='SO Fitini item' title='Print'><i class='iconfa-print action-icon'></i></a>";
                }else{
                    // session()->flash('message','No Data Available For Print!');
                    // $action .="<a id='print_a'  href='".route('print-sales_order_fitting',['id' => base64_encode($sales_order->id)]) ."' data-placement='top' data-original-title='SO Fitini item' title='Print'><i class='iconfa-print action-icon'></i></a>";

                }
            }
            if(hasAccess("sales_order","edit")){
            $action .="<a id='edit_a' href='".route('edit-sales_order',['id' => base64_encode($sales_order->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("sales_order","delete")){
            $action .= "<i id='del_a'  href='".route('delete-sales_order',['id' => base64_encode($sales_order->id)]) ."'  data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })   
        ->editColumn('type', function($sales_order){
            if($sales_order->so_from_id_fix != ''){
                $type = ucfirst($sales_order->so_from_id_fix);
                return $type;
            }else{
                return '';
            }
        })
        ->editColumn('so_type_value_fix', function($sales_order){
            if($sales_order->so_type_value_fix != ''){
                $so_type_value_fix = ucfirst($sales_order->so_type_value_fix);
                return $so_type_value_fix;
            }else{
                return '';
            }
        })
        ->editColumn('so_from_value_fix', function($sales_order){
            if($sales_order->so_from_value_fix != ''){
                if($sales_order->so_from_value_fix == 'customer'){
                    $so_from_value_fix = 'Subsidy';
                }elseif($sales_order->so_from_value_fix == 'cash_carry'){
                    $so_from_value_fix = 'Cash & Carry';
                }else{
                    $so_from_value_fix = ucfirst($sales_order->so_from_value_fix);
                }
                return $so_from_value_fix;
            }else{
                return '';
            }
        })
        ->filterColumn('so_from_value_fix', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('sales_order.so_from_value_fix', 'like', "%{$keyword}%")
                ->orWhereRaw("CASE 
                    WHEN sales_order.so_from_value_fix = 'customer' THEN 'Subsidy'
                    WHEN sales_order.so_from_value_fix = 'cash_carry' THEN 'Cash & Carry'
                    ELSE sales_order.so_from_value_fix
                    END LIKE ?", ["%{$keyword}%"]);
            });
        })
        
        // ->editColumn('name', function($sales_order){
        //     if($sales_order->name != ''){
        //         $name = ucfirst($sales_order->name);
        //         return $name;
        //     }else{
        //         return '';
        //     }
        // })
        ->addColumn('name', function($sales_order){           
            return $sales_order->so_from_value_fix == "location" ? $sales_order->location_name : $sales_order->customer_name;
        })
        ->filterColumn('name', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('location_name', 'like', "%$keyword%")
                  ->orWhere('customer_name', 'like', "%$keyword%");
            });
        })
        ->editColumn('so_date', function($sales_order){
            if ($sales_order->so_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $sales_order->so_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('sales_order.so_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(sales_order.so_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('file_upload', function($sales_order){
            if($sales_order->file_upload != '' && $sales_order->file_upload != null){                
                   
            $documentUrl = asset('storage/' . $sales_order->file_upload);

            $document = '<a href="' . $documentUrl . '" target="_blank">
            <i class="iconfa-eye-open action-icon" ></i>
            </a>';

                 
             
                return $document;
            }else{
                return '';
            }
        })
        ->editColumn('dealer_name', function($sales_order){
            if($sales_order->dealer_name != ''){
                $name = ucfirst($sales_order->dealer_name);
                return $name;
            }else{
                return '';
            }
        })
        
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','city_name','options','so_date','so_our_date','type','so_from_value_fix','so_type_value_fix','name','dealer_name','file_upload'])
        ->make(true);
    }


    public function create()
    {
        
        return view('add.add-sales_order');
    }


    
    public function show(SalesOrder $SalesOrder, $id)
    {
        return view('edit.edit-sales_order')->with('id',$id);
    }


    // public function edit(Request $request, $id,$forPrint = null)
    public function edit($id,$forPrint = null)
    {
        $isAnyPartInUse = false;   
        $sales_order = SalesOrder::select([
            'sales_order.id','sales_order.so_from_id_fix','sales_order.so_type_id_fix','sales_order.so_sequence','sales_order.so_number','sales_order.so_number','sales_order.so_date','sales_order.customer_group_id','sales_order.dealer_id', 'sales_order.customer_pincode','sales_order.current_location_id',
            'sales_order.customer_name','sales_order.customer_reg_no','sales_order.mobile_no','sales_order.area','sales_order.ship_to',
            'sales_order.to_location_id','sales_order.mis_category_id','sales_order.less_discount_percentage','sales_order.less_discount_amount',
            'sales_order.special_notes', 'sales_order.file_upload', 'sales_order.total_qty', 'sales_order.total_amount', 'sales_order.basic_amount', 'sales_order.secondary_transport','sales_order.sharing_head_unit_cost',
            'sales_order.installation_charge','sales_order.gst_type_fix_id', 'sales_order.sgst_percentage', 'sales_order.sgst_amount', 'sales_order.cgst_percentage','sales_order.cgst_amount','sales_order.igst_percentage','sales_order.igst_amount','sales_order.net_amount','sales_order.round_off_val','sales_order.customer_taluka','sales_order.customer_village','states.id as state_id', 'districts.id as district_id', 'countries.id as country_id'])
        ->leftJoin('districts', 'districts.id','sales_order.customer_district_id')
        ->leftJoin('talukas', 'talukas.district_id','districts.id')
        ->leftJoin('states','states.id', 'districts.state_id')
        ->leftJoin('countries','countries.id', 'states.country_id')
        ->where('sales_order.id',$id)->first();

        $sales_order_part = SalesOrderDetail::select(['sales_order_details.so_details_id','sales_order_details.item_id','sales_order_details.fitting_item','sales_order_details.rate_per_unit','sales_order_details.so_qty','sales_order_details.so_amount','items.item_code','item_groups.item_group_name','units.unit_name','items.item_name','items.show_item_in_print','material_request_details.mr_qty','material_request.mr_id','sales_order_details.mr_details_id','sales_order_details.remarks','sales_order_details.discount',])

        ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
        ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
        ->leftJoin('items','items.id','=','sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('sales_order_details.so_id',$id)->get(); 
        
        $sales_order->file_path = asset('storage').'/';
        //OlD qury
           // $salesOrderDetailsDetails = SalesOrderDetail::select([
        //  'sales_order_details.item_id as mitem_id'  , 'sales_order_detail_details.item_id', 'sales_order_detail_details.so_qty',  'sales_order_detail_details.sod_details_id',
        // ])
        // ->leftJoin('sales_order_detail_details','sales_order_detail_details.so_details_id','=','sales_order_details.so_details_id')                        
        // ->where('sales_order_details.so_id',$id)->get(); 

        // $salesOrderDetailsDetails = [];
        // $salesOrderId = SalesOrderDetail::select(['so_details_id'])->where('fitting_item','=','yes')->where('sales_order_details.so_id',$id)->get(); 

        //  if($salesOrderId->isNotEmpty()){
        //      $salesOrderDetailsDetails = SalesOrderDetailsDetails::select([
        //          'sales_order_details.item_id as mitem_id','sales_order_detail_details.item_id', 'sales_order_detail_details.so_qty',  'sales_order_detail_details.sod_details_id',
        //      ])                   
        //      ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','sales_order_detail_details.so_details_id')            
        //      ->whereIn('sales_order_detail_details.so_details_id',$salesOrderId)->get(); 
        //  }

        if($forPrint){

            $sales_order->so_date = Date::createFromFormat('Y-m-d', $sales_order->so_date)->format('d/m/Y');


            return response()->json([
                'so_data' => $sales_order,
                'so_part_details' => $sales_order_part,
                'response_code' => '1',
                'response_message' => '',
            ]);

        }else{

            $salesOrderDetailsDetails = [];
            $salesOrderId = SalesOrderDetail::select(['so_details_id'])->where('fitting_item','=','yes')->where('sales_order_details.so_id',$id)->get(); 
    
             if($salesOrderId->isNotEmpty()){
                 $salesOrderDetailsDetails = SalesOrderDetailsDetails::select([
                     'sales_order_details.item_id as mitem_id','sales_order_detail_details.item_id', 'sales_order_detail_details.so_qty',  'sales_order_detail_details.sod_details_id',
                 ])                   
                 ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','sales_order_detail_details.so_details_id')            
                 ->whereIn('sales_order_detail_details.so_details_id',$salesOrderId)->get(); 
             }

                if($sales_order_part != null){
        
                    $sales_order_part->each(function ($item) use (&$isAnyPartInUse) {
        
                    $totalDcQty = DispatchPlanDetails::where('so_details_id','=',$item->so_details_id)->sum('plan_qty'); 
        
                    $totalShortCloseQty = SOShortClose::where('so_details_id','=',$item->so_details_id)->sum('sc_qty'); 
        
                    $totalMappQty = SOMappingDetails::where('so_details_id','=',$item->so_details_id)->sum('map_qty'); 

                    $totalTrSCQty = TransactionSOShortClose::where('so_details_id','=',$item->so_details_id)->sum('tr_sc_qty');  

                    $isFound = $totalDcQty + $totalShortCloseQty + $totalMappQty + $totalTrSCQty;
        
                    if($item->fitting_item == 'yes'){
                        
                        $check_data = DispatchPlanDetails::select('sales_order_details.so_qty')
                        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
                        ->where('dispatch_plan_details.so_details_id', '=', $item->so_details_id)->first();

                        if($check_data != null){
                            $item->in_use = true;
                            $isAnyPartInUse = true;
                            $item->used_qty = $check_data->so_qty;
                        }else{
                            $item->in_use = false;
                            $item->used_qty = 0;
                        }

                    }else{
                        if($isFound != null){
                            $item->in_use = true;
                            $item->used_qty = $isFound;
                            $isAnyPartInUse = true;
            
            
                            if($item->used_qty > $item->so_qty){
                                $item->used_qty = $item->so_qty;
                            }
            
                        }else{
                            $item->in_use = false;
                            $item->used_qty = 0;
            
                        }
                    }

                    
                    // dd($inUse);
                    return $item;
        
                })->values();
            }
            if ($sales_order != null) {
                // $sales_order->so_our_date = Date::createFromFormat('Y-m-d', $sales_order->so_our_date)->format('d/m/Y');

                $sales_order->so_date = Date::createFromFormat('Y-m-d', $sales_order->so_date)->format('d/m/Y');

                $sales_order->in_use = false;
                if($isAnyPartInUse == true){
                    $sales_order->in_use = true;
                }
            }
            if ($sales_order) {
            
                return response()->json([
                    'so_data' => $sales_order,
                    'so_part_details' => $sales_order_part,
                    'so_part_details_details' => $salesOrderDetailsDetails,
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
      
    }

    public function update(Request $request, SalesOrder $SalesOrder)
    {
        // dd($request->all());
        $year_data = getCurrentYearData();
        $locationCode = getCurrentLocation()->id;
      

        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

        $validated = $request->validate(
            [
                'so_location_id' => 'required_if:so_from_id_fix,3',
                'so_sequence' => ['required','max:155',Rule::unique('sales_order')->where(function ($query) use ($request,$year_data, $locationID) {
                    return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'id')],

                'so_number' => ['required', 'max:155', Rule::unique('sales_order')->where(function ($query) use ($request, $year_data, $locationID) {
                    return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'id')],              
            ],
            [
                'so_location_id.required_if' => 'Please Select Location',
                'so_sequence.unique'=>'SO. No. Is Already Exists',    
                'so_number.required' => 'Please Enter SO NO.',
                'so_number.max' => 'Maximum 155 Characters Allowed',
            ]
        );
         $file_upload = "";

        $imgs = SalesOrder::where('id','=',$request->id)->first('file_upload');
         if($imgs){
            if($imgs->file_upload != "" && $request->file_upload_doc != $imgs->file_upload ){
                $file = new File();
                $file->delete_file($imgs->file_upload);
            }
        }

        if(isset($request->file_upload_doc) && $request->file_upload_doc != ""){
            $file = new File();
            $isFound =  $file->getFileFromTemp($request->file_upload_doc,$prefix = "file");

            if($isFound != false){
                $file_upload = $isFound;
            }else{
                if($file->Is_Files_Exists($request->file_upload_doc)){
                    $file_upload = $request->file_upload_doc;
                }
            }
        }
        // $soFormValue = $request->so_from_id_fix == 1 ? "customer" : "location";

         if($request->so_from_id_fix == 1){
            $soFormValue =  "customer";
        }elseif($request->so_from_id_fix == 2){
            $soFormValue = "cash_carry";
        }elseif($request->so_from_id_fix == 3){
            $soFormValue =  "location";
        }
 
         $soTypeValue = $request->so_type_id_fix == 1 ? "general" : "replacement";

         $totalQty = 0;
         $totalAmount = 0;
 
             foreach ($request->item_id as $ctKey => $ctVal) {
              
                 if ($ctVal != null) {
                     $totalQty += $request->so_qty[$ctKey];  
                     $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";        
                 }
             }
             

        DB::beginTransaction();
        try {
            $salesorder_data =  SalesOrder::where("id", "=", $request->id)->update([
                'so_from_value_fix'=>$soFormValue,                
                'so_from_id_fix'=>$request->so_from_id_fix,
                 'so_type_id_fix'=>$request->so_type_id_fix, 
                 'so_type_value_fix'=> $soTypeValue,                 
                 'total_qty' => $totalQty, 
                 'total_amount' => $totalAmount,
                'so_sequence' => $request->so_sequence,                
                'so_number' => $request->so_number,                
                'so_date' => Date::createFromFormat('d/m/Y', $request->so_date)->format('Y-m-d'),
                // 'so_customer_id' => $request->so_customer_id,                
                'customer_group_id' => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  $request->customer_group_id : null,
                'customer_taluka'=> $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  $request->so_taluka_id : null,
                'to_location_id'=> $request->so_from_id_fix == "3"   ?  $request->so_location_id : null,
                'customer_name' => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2" ?  ($request->so_type_id_fix == '1' || $request->so_from_id_fix == "2"  ? $request->customer_name : $request->rep_customer_id) : null,
                'dealer_id' => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  $request->dealer_id : null,
                'customer_village' => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  $request->customer_village : null, 
                'customer_pincode' => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  $request->customer_pincode : null,
                'customer_district_id' => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  $request->so_district_id : null,
                'mobile_no' => $request->so_from_id_fix == "1"|| $request->so_from_id_fix == "2"  ?  $request->so_mobile_no : null,
                'area' => $request->so_from_id_fix == "1"|| $request->so_from_id_fix == "2"  ?  $request->area : null,
                'ship_to' => $request->so_from_id_fix == "1"|| $request->so_from_id_fix == "2"  ?  $request->ship_to : null,
                'to_location_id' => $request->so_from_id_fix == "3"   ?  $request->so_location_id : null,
                'current_location_id' => $locationCode,
                'customer_reg_no'   => $request->so_from_id_fix == 1 || $request->so_from_id_fix == "2"  ?  $request->customer_reg_no : null,
                'mis_category_id'       => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  $request->mis_category_id : null, 

                'basic_amount'       => $request->basic_amount != "" ?  $request->basic_amount : null, 

                'less_discount_percentage'       => $request->less_discount_percentage != "" ?  $request->less_discount_percentage : null, 

                'less_discount_amount'       => $request->less_discount_amount != "" ?  $request->less_discount_amount : null, 
                
                'secondary_transport'       => $request->secondary_transport !="" ?  $request->secondary_transport : null, 

                'sharing_head_unit_cost'       => $request->sharing_head_unit_cost !="" ?  $request->sharing_head_unit_cost : null, 

                'installation_charge'       => $request->installation_charge !="" ?  $request->installation_charge : null, 

                'gst_type_fix_id'       => $request->gst_type_fix_id != "" ?  $request->gst_type_fix_id : null, 

                'sgst_percentage'       => $request->sgst_percentage != "" ?  $request->sgst_percentage : null, 

                'sgst_amount'       => $request->sgst_amount != "" ?  $request->sgst_amount : null,

                'cgst_percentage'       => $request->cgst_percentage != "" ?  $request->cgst_percentage : null,

                'cgst_amount'       => $request->cgst_amount != "" ?  $request->cgst_amount : null, 

                'igst_percentage'       => $request->igst_percentage != "" ?  $request->igst_percentage : null, 

                'igst_amount'       => $request->igst_amount != "" ?  $request->igst_amount : null,

                'round_off_val'       => $request->round_off != "" ?  $request->round_off : null,

                'net_amount'       => $request->net_amount != "" ?  $request->net_amount : null, 

                // 'country_id'        => $request->so_from == 1 ?  $request->so_country_id : null,
                // 'state_id'          => $request->so_from == 1 ?  $request->so_state_id : null,
                // 'district_id'       => $request->so_from == 1 ?  $request->so_district_id : null,
                // 'taluka_id'         => $request->so_from == 1 ?  $request->so_taluka_id : null,                     
                'special_notes' => $request->special_notes,   
                'file_upload' => $file_upload,           
                'year_id' =>  $year_data->id,
                'last_by_user_id' => Auth::user()->id,
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),                
            ]);


            if ($salesorder_data)   {
                
                $oldSoPart = SalesOrderDetail::where('so_id', '=', $request->id)->get();

                // this cose use to stock maintain
                $oldSoDetails = SalesOrderDetail::where('so_id', '=', $request->id)->get();
                $oldSoDetailsData = [];
                if($oldSoDetails != null){
                    $oldSoDetailsData = $oldSoDetails->toArray();
                }

                // foreach ($request->sales_order_detail_id as $sodKey => $sodVal) {
                //     SalesOrderDetailsDetails::where('so_details_id',$sodVal)->delete();
                // }

                if (isset($request->sales_order_detail_id) && !empty($request->sales_order_detail_id)) {

                    $OrdrerDetails =  SalesOrderDetail::where('so_id',$request->id)->update([
                        'status' => 'D',
                    ]);

                    $OrdrerDetails =  SalesOrderDetail::where('so_id',$request->id)->get();
                    if($OrdrerDetails != null){
                        foreach($OrdrerDetails as $okey => $oval){
                            $sodDetails = SalesOrderDetailsDetails::where('so_details_id',$oval->so_details_id)->update([
                                'status' => 'D',
                            ]);
                        }
                    }

                    foreach ($request->sales_order_detail_id as $sodKey => $sodVal) {
                        if($sodVal == "0"){
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                // $fittingItems = Item::where('id', $request->item_id[$sodKey])->pluck('fitting_item')->first();
                                // $sondaryItems = Item::where('id', $request->item_id[$sodKey])->pluck('secondary_unit')->first();                             
                                // $partialDispatch = Item::where('id', $request->item_id[$sodKey])->pluck('allow_partial_dispatch')->first();    
                                
                                $item = Item::select('fitting_item', 'secondary_unit', 'allow_partial_dispatch',)->where('id', $request->item_id[$sodKey])->first();
                                                       
                                $SalesOrdrerDetails =  SalesOrderDetail::create([
                                    'so_id' => $request->id,
                                    'item_id' => $request->item_id[$sodKey],
                                    'fitting_item' => $item->fitting_item,
                                    'secondary_unit' => $item->secondary_unit,
                                    'allow_partial_dispatch' => $item->allow_partial_dispatch,
                                     //  'production_assembly' => $item->production_assembly,
                                    'so_qty' => isset($request->so_qty[$sodKey]) ? $request->so_qty[$sodKey] : null,
                                    'rate_per_unit' => isset($request->rate_unit[$sodKey]) ? $request->rate_unit[$sodKey]: null,
                                    'so_amount' =>isset($request->amount[$sodKey]) ? $request->amount[$sodKey] : null,
                                    'remarks' => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : '',
                                    'discount' => isset($request->discount[$sodKey]) ? $request->discount[$sodKey] : '',
                                    'mr_details_id' =>isset($request->mr_details_id[$sodKey]) ? $request->mr_details_id[$sodKey] : null,
                                    'status' => 'Y',
                                ]);
                                if(isset($request->mr_details_id[$ctKey]) && !empty($request->mr_details_id[$ctKey])){
                                    $mr_id = MaterialRequestDetail::where('mr_details_id',$request->mr_details_id[$ctKey])->first();
                                
                                    $material =  MaterialRequest::where('mr_id',$mr_id->mr_id)->update([
                                        'approval_type_id_fix' => 5,                                    
                                    ]);
                                    // $material =  MaterialRequestDetail::where('mr_details_id',$request->mr_details_id[$ctKey])->update([
                                    //     'approval_type_id_fix' => 5,                                    
                                    // ]);
                                 }
                               
                                if(isset($request->storeSalesOrderDetails[$request->item_id[$sodKey]]) && !empty($request->storeSalesOrderDetails[$request->item_id[$sodKey]])) 
                                { 
                                    foreach($request->storeSalesOrderDetails[$request->item_id[$sodKey]] as $fsodKey => $fsodVal){
                                        if(isset($fsodVal['item_id']) && $fsodVal['item_id'] != null){                                        
                                            $SalesOrdrerDetailsDetail =  SalesOrderDetailsDetails::create([
                                                'so_details_id' =>$SalesOrdrerDetails->so_details_id,    
                                                'item_id' =>  isset($fsodVal['item_id']) ? $fsodVal['item_id'] : null,   
                                                'rate_per_unit' => null,
                                                'so_qty' => isset($fsodVal['so_qty']) ? $fsodVal['so_qty'] : null,   
                                                'so_amount' => null, 
                                                'status' => 'Y',                                
                                            ]);
                                        }      
                                    }
                                }
                            }
                        }else{                          
                            
                            if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){

                                // $fittingItems = Item::where('id', $request->item_id[$sodKey])->pluck('fitting_item')->first();

                                $old_item = SalesOrderDetail::where('so_details_id',$sodVal)->pluck('item_id')->first();

                                if($old_item != $request->item_id[$sodKey]){
                                    // $sondaryItems = Item::where('id', $request->item_id[$sodKey])->pluck('secondary_unit')->first();                             
                                    // $partialDispatch = Item::where('id', $request->item_id[$sodKey])->pluck('allow_partial_dispatch')->first();      

                                    $item = Item::select('fitting_item', 'secondary_unit', 'allow_partial_dispatch',)->where('id', $request->item_id[$sodKey])->first();

                                }else{
                                    // $sondaryItems = SalesOrderDetail::where('so_details_id',$sodVal)->pluck('secondary_unit')->first();
                                    // $partialDispatch = SalesOrderDetail::where('so_details_id',$sodVal)->pluck('allow_partial_dispatch')->first();

                                    $item = SalesOrderDetail::select('fitting_item', 'secondary_unit', 'allow_partial_dispatch',)->where('so_details_id',$sodVal)->first();
                                }
                               

                                $SalesOrdrerDetails =  SalesOrderDetail::where('so_details_id',$sodVal)->update([
                                    'so_id' => $request->id,
                                    'item_id' => $request->item_id[$sodKey],
                                    'fitting_item' => $item->fitting_item,
                                    'secondary_unit' => $item->secondary_unit,
                                    'allow_partial_dispatch' => $item->allow_partial_dispatch,
                                    //  'production_assembly' => $item->production_assembly,
                                    'so_qty' => isset($request->so_qty[$sodKey]) ? $request->so_qty[$sodKey] : null,
                                    'rate_per_unit' => isset($request->rate_unit[$sodKey]) ? $request->rate_unit[$sodKey]: null,
                                    'so_amount' =>isset($request->amount[$sodKey]) ? $request->amount[$sodKey] : null,
                                    'remarks' => isset($request->remarks[$sodKey]) ? $request->remarks[$sodKey] : '',
                                    'discount' => isset($request->discount[$sodKey]) ? $request->discount[$sodKey] : '',
                                    'mr_details_id' =>isset($request->mr_details_id[$sodKey]) ? $request->mr_details_id[$sodKey] : null,
                                    'status' => 'Y',
                                ]);                            
                        
                            
                             
                                if(isset($request->storeSalesOrderDetails[$request->item_id[$sodKey]]) && !empty($request->storeSalesOrderDetails[$request->item_id[$sodKey]])){
                                  
                                    $sodPart = $request->storeSalesOrderDetails[$request->item_id[$sodKey]];
                               

                                    foreach($sodPart as $pKey=>$pVal){
                                        if($pVal['sod_details_id'] == 0){
                                            $SalesOrdrerDetailsDetail =  SalesOrderDetailsDetails::create([
                                                'so_details_id' =>$sodVal,    
                                                'item_id' =>  isset($pVal['item_id']) ? $pVal['item_id'] : null,   
                                                'rate_per_unit' => null,
                                                'so_qty' => isset($pVal['so_qty']) ? $pVal['so_qty'] : null,   
                                                'so_amount' => null,  
                                                'status' => 'Y',                               
                                            ]);

                                        }else{
                                            if(isset($pVal['item_id']) && $pVal['item_id'] != null){
                                                $SalesOrdrerDetailsDetail =  SalesOrderDetailsDetails::where('sod_details_id',$pVal['sod_details_id'])->update([
                                                    'so_details_id' =>$sodVal,    
                                                    'item_id' =>  isset($pVal['item_id']) ? $pVal['item_id'] : null,   
                                                    'rate_per_unit' => null,
                                                    'so_qty' => isset($pVal['so_qty']) ? $pVal['so_qty'] : null,   
                                                    'so_amount' => null,
                                                    'status' => 'Y',                                 
                                                ]); 
                                                

                                            }
                                        }                        
                             
                                    }
                                }                  
                         
                            }
                        }

                    }
                }   


                $OrdrerDetails =  SalesOrderDetail::where('so_id',$request->id)->get();
                if($OrdrerDetails != null){
                    foreach($OrdrerDetails as $okey => $oval){
                        $sodDetails = SalesOrderDetailsDetails::where('so_details_id',$oval->so_details_id)->where('status','D')->delete();
                    }
                }

                $mrData = SalesOrderDetail::select('material_request_details.mr_id')
                ->leftJoin('material_request_details','material_request_details.mr_details_id','sales_order_details.mr_details_id')
                ->where('sales_order_details.so_id',$request->id)->where('sales_order_details.status','D')->get();

                $mrYData = SalesOrderDetail::select('material_request_details.mr_id')
                ->leftJoin('material_request_details','material_request_details.mr_details_id','sales_order_details.mr_details_id')
                ->where('sales_order_details.so_id',$request->id)->where('sales_order_details.status','Y')->get();

    
                if($mrData->isNotEmpty() && empty($mrYData)){
                    foreach ($mrData as $ctKey => $ctVal) {
                        MaterialRequest::where('mr_id', '=', $ctVal->mr_id)->update([
                            'approval_type_id_fix' => '4'
                        ]);
                    }
                }

                $OrdrerDetails = SalesOrderDetail::where('so_id',$request->id)->where('status','D')->delete();

                
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                ]);
            } 
        else {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Not Updated',
            ]);
        }
        }
        catch (\Exception $e) {
           
            DB::rollBack();
            getActivityLogs("Sales Order", "update", $e->getMessage(),$e->getLine());
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Updated',
                'original_error' => $e->getMessage()
            ]);
        }
    }


    public function Store(Request $request){

        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

        $validated = $request->validate(
            [
                'so_location_id' => 'required_if:so_from_id_fix,3',               
            ],
            [
                'so_location_id.required_if' => 'Please Select Location',               
            ]
        );
         $file_upload = "";

  
        if(isset($request->file_upload_doc) && $request->file_upload_doc != ""){
            $file = new File();
            $isFound =  $file->getFileFromTemp($request->file_upload_doc,$prefix = "file");

            if($isFound != false){
                $file_upload = $isFound;
            }
        }
       
          $existNumber = SalesOrder::where('so_number','=',$request->so_number)->where('so_sequence','=',$request->so_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationID)->lockForUpdate()->first();
          
          if($existNumber){
              $latestNo = $this->getLatestSoNo($request);
              $tmp =  $latestNo->getContent();
              $area = json_decode($tmp, true);
              $so_number =   $area['latest_po_no'];
              $so_sequence = $area['number'];              
          }else{
             $so_number = $request->so_number;
             $so_sequence = $request->so_sequence;
          }
          // end check duplicate number


         
        //  $year_data = getCurrentYearData();
 
        //  $locationCode = getCurrentLocation()->id;
         
         
        //  $soFormValue = $request->so_from_id_fix == 1 ? "customer" : "location";

        if($request->so_from_id_fix == 1){
            $soFormValue =  "customer";
        }elseif($request->so_from_id_fix == 2){
            $soFormValue = "cash_carry";
        }elseif($request->so_from_id_fix == 3){
            $soFormValue =  "location";
        }
 
         $soTypeValue = $request->so_type_id_fix == 1 ? "general" : "replacement";
 
         $totalQty = 0;
         $totalAmount = 0;
 
             foreach ($request->item_id as $ctKey => $ctVal) {
              
                 if ($ctVal != null) {
                     $totalQty += $request->so_qty[$ctKey];  
                     $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";        
                 }
             }
             
         DB::beginTransaction();
         try{
             
             $salesorder_data=  SalesOrder::create([ 
                 'so_from_id_fix'=>$request->so_from_id_fix, 
                 'so_from_value_fix'=>$soFormValue, 
                 'so_type_id_fix'=>$request->so_type_id_fix, 
                 'so_type_value_fix'=> $soTypeValue, 
                 'so_sequence' => $so_sequence, 
                 'so_number' => $so_number, 
                 'total_qty' => $totalQty, 
                 'total_amount' => $totalAmount, 
                 'so_date' => Date::createFromFormat('d/m/Y', $request->so_date)->format('Y-m-d'),
                 'so_customer_id' => $request->so_customer_id, 
                 'customer_group_id' => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2" ?  $request->customer_group_id : null, 
                 'customer_name'     => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  ($request->so_type_id_fix == '1' || $request->so_from_id_fix == "2"  ? $request->customer_name : $request->rep_customer_id) : null,
                 'dealer_id'     => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  $request->dealer_id : null, 
                 'customer_district_id' => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?   $request->customer_district_id : null, 
                 'to_location_id'       => $request->so_from_id_fix == "3"   ?  $request->so_location_id : null, 
                 'current_location_id'       => $locationID, 
                 'mobile_no'      => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2" ?  $request->so_mobile_no : null,
                 'area'      => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2" ?  $request->area : null,
                 'ship_to'      => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2" ?  $request->ship_to : null,
                 'customer_reg_no'       => $request->customer_reg_no,         
                 'customer_village'      => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2" ?  $request->customer_village : null,
                 'customer_pincode'          => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2" ?  $request->customer_pincode : null, 
                 'customer_district_id'       => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2" ?  $request->so_district_id : null, 
                 'customer_taluka'         => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"   ?  $request->so_taluka_id : null, 
                 'mis_category_id'       => $request->so_from_id_fix == "1" || $request->so_from_id_fix == "2"  ?  $request->mis_category_id : null, 

                 'basic_amount'       => $request->basic_amount != "" ?  $request->basic_amount : null, 

                 'less_discount_percentage'       => $request->less_discount_percentage != "" ?  $request->less_discount_percentage : null, 

                 'less_discount_amount'       => $request->less_discount_amount != "" ?  $request->less_discount_amount : null, 

                 'secondary_transport'       => $request->secondary_transport !="" ?  $request->secondary_transport : null, 

                 'sharing_head_unit_cost'       => $request->sharing_head_unit_cost !="" ?  $request->sharing_head_unit_cost : null,

                 'installation_charge'       => $request->installation_charge !="" ?  $request->installation_charge : null, 

                 'gst_type_fix_id'       => $request->gst_type_fix_id != "" ?  $request->gst_type_fix_id : null, 

                 'sgst_percentage'       => $request->sgst_percentage !=  "" ?  $request->sgst_percentage : null, 

                 'sgst_amount'       => $request->sgst_amount != "" ?  $request->sgst_amount : null,

                 'cgst_percentage'       => $request->cgst_percentage != "" ?  $request->cgst_percentage : null,

                 'cgst_amount'       => $request->cgst_amount != "" ?  $request->cgst_amount : null, 

                 'igst_percentage'       => $request->igst_percentage != "" ?  $request->igst_percentage : null, 

                 'igst_amount'       => $request->igst_amount != "" ?  $request->igst_amount : null,

                 'net_amount'       => $request->net_amount != "" ?  $request->net_amount : null, 

                 'round_off_val'       => $request->round_off != "" ?  $request->round_off : null, 
                 
                 //'country_id'        => $request->so_from_id_fix == "1" ?  $request->so_country_id : null, 
                 //'state_id'          => $request->so_from_id_fix == "1" ?  $request->so_state_id : null, 
                 'special_notes' => $request->special_notes, 
                 'file_upload' => $file_upload, 
                  'year_id' =>  $year_data->id,                 
                 'company_id' => Auth::user()->company_id,
                 'created_by_user_id' => Auth::user()->id, 
                 'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),   
             ]);
 
             
             if ($salesorder_data->save()) {
 
                 foreach ($request->item_id as $ctKey => $ctVal ) 
                 {
                     
                    //  $fittingItems = Item::where('id', $ctVal)->pluck('fitting_item')->first();
                    //  $sondaryItems = Item::where('id', $ctVal)->pluck('secondary_unit')->first();
                    //  $partialDispatch = Item::where('id', $ctVal)->pluck('allow_partial_dispatch')->first();

                    //  $item = Item::select('fitting_item', 'secondary_unit', 'allow_partial_dispatch','production_assembly')->where('id', $ctVal)->first();
                     $item = Item::select('fitting_item', 'secondary_unit', 'allow_partial_dispatch',)->where('id', $ctVal)->first();



 
                     if ($ctVal != null) {
                        
                        if($request->so_from_id_fix == '2' && isset($request->mr_details_id[$ctKey])){

                            $mrQtySum = MaterialRequestDetail::where('mr_details_id',$request->mr_details_id[$ctKey])->sum('mr_qty');

                            $useSOQtySum = SalesOrderDetail::where('mr_details_id',$request->mr_details_id[$ctKey])->sum('so_qty');

                            $soQty = isset($request->so_qty[$ctKey]) && $request->so_qty[$ctKey] > 0 ? $request->so_qty[$ctKey] : 0;

                            $soQtySum = $useSOQtySum + $soQty;
                          

                            if(number_format($mrQtySum, 3) < number_format($soQtySum, 3)){
                                DB::rollBack();
                                return response()->json([
                                    'response_code' => '0',
                                    'response_message' => 'SO Qty. Is Used',                               
                                ]);
                            }

                        }
                      
                             $coa_part_data =  SalesOrderDetail::create([
                                 'so_id' => $salesorder_data->id,
     
                                 'item_id' => $ctVal,
     
                                 'so_qty' => isset($request->so_qty[$ctKey]) ? $request->so_qty[$ctKey] : null,
     
                                 'rate_per_unit' => isset($request->rate_unit[$ctKey]) ? $request->rate_unit[$ctKey] : null,
     
                                 'so_amount' =>isset($request->amount[$ctKey]) ? $request->amount[$ctKey] : null,
 
                                 'fitting_item' => $item->fitting_item,

                                 'secondary_unit' => $item->secondary_unit,

                                 'allow_partial_dispatch' => $item->allow_partial_dispatch,

                                //  'production_assembly' => $item->production_assembly,

                                 'remarks' => isset($request->remarks[$ctKey]) ? $request->remarks[$ctKey] : '',

                                 'discount' => isset($request->discount[$ctKey]) ? $request->discount[$ctKey] : '',

                                 'mr_details_id' =>isset($request->mr_details_id[$ctKey]) ? $request->mr_details_id[$ctKey] : null,

                                 'status' => 'Y',

                              
                             ]);  

                             if(isset($request->mr_details_id[$ctKey]) && !empty($request->mr_details_id[$ctKey])){
                                $mr_id = MaterialRequestDetail::where('mr_details_id',$request->mr_details_id[$ctKey])->first();
                                
                                $material =  MaterialRequest::where('mr_id',$mr_id->mr_id)->update([
                                    'approval_type_id_fix' => 5,                                    
                                ]);
                                // $material =  MaterialRequestDetail::where('mr_details_id',$request->mr_details_id[$ctKey])->update([
                                //     'approval_type_id_fix' => 5,                                    
                                // ]);
                             }
                             
                    //    dd($request->item_id[$ctKey]);
                                
                            // if(isset($request->storeSalesOrderDetails[$request->item_id[$ctKey]]) && !empty($request->storeSalesOrderDetails[$request->item_id[$ctKey]])) 
                            // { 
                            //     foreach($request->storeSalesOrderDetails[$request->item_id[$ctKey]] as $skey => $sVal)
                            //     {   
                            //         $addSalesOrderDetails = SalesOrderDetailsDetails::create([
                            //             'so_details_id' => $coa_part_data->so_details_id,
                            //             'item_id' => $sVal['item_id'],
                            //             'so_qty' => $sVal['so_qty'],                                
                            //         ]);   
                            //     }
                            // }


                            if(isset($request->storeSalesOrderDetails[$request->item_id[$ctKey]]) && !empty($request->storeSalesOrderDetails[$request->item_id[$ctKey]])) 
                            { 
                                    foreach($request->storeSalesOrderDetails[$request->item_id[$ctKey]] as $fsodKey => $fsodVal){
                                        if(isset($fsodVal['item_id']) && $fsodVal['item_id'] != null){                                        
                                            $SalesOrdrerDetailsDetail =  SalesOrderDetailsDetails::create([
                                                'so_details_id' => $coa_part_data->so_details_id,  
                                                'item_id' =>  isset($fsodVal['item_id']) ? $fsodVal['item_id'] : null,   
                                                'rate_per_unit' => null,
                                                'so_qty' => isset($fsodVal['so_qty']) ? $fsodVal['so_qty'] : null,   
                                                'so_amount' => null, 
                                                'status' => 'Y',                                
                                            ]);
                                        }                        
        
                                }
                            }
                    }                     
                 }                 
             }
             
             if($salesorder_data->save())
             { 
                 DB::commit();
                 return response()->json([
                     'response_code' => '1',                
                    
                     'response_message' => 'Record Inserted Successfully.',
                 ]);
             }
             
             else {
                DB::rollBack();
                 return response()->json([
                     'response_code' => '0',
                     'response_message' => 'Record Not Inserted',
                 ]);
             }
         }
         catch(\Exception $e){
             DB::rollBack();
             getActivityLogs("Sales Order", "add", $e->getMessage(),$e->getLine());
             return response()->json([
                 'response_code' => '0',
                 'response_message' => 'Error Occured Record Not Inserted',
                 'original_error' => $e->getMessage()
             ]);
         }
 
     }

    // old Code
    // public function Store(Request $request){
        
        
    //    //dd($request->all());
        
    //     $year_data = getCurrentYearData();

    //     $locationCode = getCurrentLocation()->id;
        
        
    //     $soFormValue = $request->so_from_id_fix == 1 ? "customer" : "location";

    //     $soTypeValue = $request->so_type_id_fix == 1 ? "general" : "replacement";

    //     $totalQty = 0;
    //     $totalAmount = 0;

    //         foreach ($request->item_id as $ctKey => $ctVal) {
             
    //             if ($ctVal != null) {
    //                 $totalQty += $request->so_qty[$ctKey];  
    //                 $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";        
    //             }
    //         }
            
    //     DB::beginTransaction();
    //     try{
            
    //         $salesorder_data=  SalesOrder::create([

    //             'so_from_id_fix'=>$request->so_from_id_fix,

    //             'so_from_value_fix'=>$soFormValue,

    //             'so_type_id_fix'=>$request->so_type_id_fix,  

    //             'so_type_value_fix'=> $soTypeValue,

    //             'so_sequence' => $request->so_sequence,

    //             'so_number' => $request->so_number,

    //             'total_qty' => $totalQty,

    //             'total_amount' => $totalAmount,

    //             'so_date' => Date::createFromFormat('d/m/Y', $request->so_date)->format('Y-m-d'),
    //             'so_customer_id' => $request->so_customer_id,

    //             'customer_group_id' => $request->so_from_id_fix == "1" ?  $request->customer_group_id : null,

    //             'customer_name'     => $request->so_from_id_fix == "1" ?  $request->customer_name : null,

    //             'customer_district_id' => $request->so_from_id_fix == "1" ?  $request->customer_district_id : null,

    //             'to_location_id'       => $request->so_from_id_fix == "2"   ?  $request->so_location_id : null,

    //             'current_location_id'       => $locationCode,

    //             'customer_reg_no'       => $request->customer_reg_no,
                
            
    //             'customer_village'      => $request->so_from_id_fix == "1" ?  $request->customer_village : null,

    //             'customer_pincode'          => $request->so_from_id_fix == "1" ?  $request->customer_pincode : null,

    //             'customer_district_id'       => $request->so_from_id_fix == "1" ?  $request->so_district_id : null,

    //             'customer_taluka'         => $request->so_from_id_fix == "1" ?  $request->so_taluka_id : null,


    //             'country_id'        => $request->so_from_id_fix == "1" ?  $request->so_country_id : null,

    //             'state_id'          => $request->so_from_id_fix == "1" ?  $request->so_state_id : null,


    //             'special_notes' => $request->special_notes,  

    //             'year_id' =>  $year_data->id,
                
    //             'created_by_user_id' => Auth::user()->id,

    //             'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),  

    //         ]);

            
    //         if ($salesorder_data->save()) {

    //             foreach ($request->item_id as $ctKey => $ctVal ) {
                    
    //                 $fittingItems = Item::where('id', $ctVal)->pluck('fitting_item')->first();

    //                 if ($ctVal != null) {

                     
    //                         $coa_part_data =  SalesOrderDetail::create([
    //                             'so_id' => $salesorder_data->id,
    
    //                             'item_id' => $ctVal,
    
    //                             'so_qty' => isset($request->so_qty[$ctKey]) ? $request->so_qty[$ctKey] : null,
    
    //                             'rate_per_unit' => isset($request->rate_unit[$ctKey]) ? $request->rate_unit[$ctKey] : null,
    
    //                             'so_amount' =>isset($request->amount[$ctKey]) ? $request->amount[$ctKey] : null,

    //                              'fitting_item' => $fittingItems != "" ? $fittingItems : "",
                             
    //                         ]);
    //                 }
    //             }
    //         }
            
            
    //         if($salesorder_data->save())
    //         { 
    //             DB::commit();
    //             return response()->json([
    //                 'response_code' => '1',                
                   
    //                 'response_message' => 'Record Inserted Successfully.',
    //             ]);
    //         }
            
    //         else {
    //             return response()->json([
    //                 'response_code' => '0',
    //                 'response_message' => 'Record Not Inserted',
    //             ]);
    //         }
    //     }
    //     catch(\Exception $e){
    //         DB::rollBack();
    //         return response()->json([
    //             'response_code' => '0',
    //             'response_message' => 'Error Occured Record Not Inserted',
    //             'original_error' => $e->getMessage()
    //         ]);
    //     }

    // }


    public function getLatestSoNo(Request $request)
    {    
        $modal  =  SalesOrder::class;
        $sequence = 'so_sequence';
        $prefix = 'SO';
        $po_num_format = getLatestSequence($modal,$sequence,$prefix);

        $locationName = getCurrentLocation();

        return response()->json([
          'response_code' => 1,
          'latest_po_no'  => $po_num_format['format'],
          'number'        => $po_num_format['isFound'],
          'location'      => $locationName
      ]);
    }


    public function getSoCustomer(Request $request){

        if($request->customer == "location"){
        $customer_groups = CustomerGroup::select('id')->where('customer_group_name','=',$request->customer)->get();
        }else{
        $customer_groups = CustomerGroup::select('id')->where('customer_group_name','!=',"location")->get();
        }

        $customer = Customer::select(['id','customer_name'])->whereIn('customer_group_id',$customer_groups)->get();

        return response()->json([
            'response_code' => 1,
            'customer' => $customer,          
        ]);
    }


    public function getRegNo(Request $request){


        $customer = Customer::select(['id','register_number'])->where('id',$request->customer)->first();

        return response()->json([
            'response_code' => 1,
            'customer' => $customer,          
        ]);
    }
    public function getItemData(Request $request){

        if($request->customerGroup != ''){
            // $item = Item::select(['items.id','items.item_code','item_groups.item_group_name','units.unit_name',
            // // 'price_list_details.sales_rate',
            //   DB::raw("(SELECT (price_list_details.sales_rate) FROM price_list_details WHERE price_list_details.item_id = items.id AND price_list_details.customer_group_id = $request->customerGroup) as sales_rate"), 
            // ])
            // ->Join('item_groups','item_groups.id','=','items.item_group_id')
            // ->Join('units','units.id','=','items.unit_id')
            // // ->leftJoin('price_list_details','price_list_details.item_id','=','items.id')
            // ->where('items.id',$request->item)->first();

            $item = PriceListDetails::select(['price_list_details.sales_rate',]) 
            ->where('item_id',$request->item)         
            ->where('customer_group_id',$request->customerGroup)->first();


        }
        else{
            $item = null;
            // $item = Item::select(['items.id','items.item_code','item_groups.item_group_name','units.unit_name',])
            // ->Join('item_groups','item_groups.id','=','items.item_group_id')
            // ->Join('units','units.id','=','items.unit_id')
            // ->where('items.id',$request->item)->first();
        }

        // if($request->customerGroup != ''){
        //     $item = Item::select(['items.*','item_groups.item_group_name','units.unit_name',
        //     // 'price_list_details.sales_rate',
        //       DB::raw("(SELECT (price_list_details.sales_rate) FROM price_list_details WHERE price_list_details.item_id = items.id AND price_list_details.customer_group_id = $request->customerGroup) as sales_rate"), 
        //     ])
        //     ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        //     ->leftJoin('units','units.id','=','items.unit_id')
        //     // ->leftJoin('price_list_details','price_list_details.item_id','=','items.id')
        //     ->where('items.id',$request->item)->first();


        // }else{
        //     $item = Item::select(['items.*','item_groups.item_group_name','units.unit_name',  ])
        //     ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        //     ->leftJoin('units','units.id','=','items.unit_id')
        //     ->where('items.id',$request->item)->first();
        // }

      


    

        
       

        
        if($request->id != "undefined")
        {            
            $getData = SalesOrderDetail::select(['so_details_id'])->where('so_id', $request->id)->where('item_id', $request->item)->get();    


            $IffittingItem = Item::select(['items.item_code','sales_order_detail_details.*'])
            ->leftJoin('sales_order_detail_details','sales_order_detail_details.item_id','=','items.id')   
            ->whereIn('so_details_id', $getData)->get();

            if($IffittingItem->isEmpty()){
                $IffittingItem = Item::select(['items.*','item_groups.item_group_name','units.unit_name'])
                ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
                ->leftJoin('units','units.id','=','items.unit_id')
                ->where('items.id',$request->item)->where('items.fitting_item', 'yes')->get();
            }
        }
        else{
            
            $IffittingItem = Item::select(['items.*','item_groups.item_group_name','units.unit_name'])
            ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
            ->leftJoin('units','units.id','=','items.unit_id')
            ->where('items.id',$request->item)->where('items.fitting_item', 'yes')->get();
        }
        

        


        return response()->json([
            'response_code' => 1,
            'item' => $item,          
            'IffittingItem' => $IffittingItem,          
        ]);
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            
            $so_mapping = SOMappingDetails::
            leftJoin('sales_order_details','sales_order_details.so_details_id','=','so_mapping_details.so_details_id')
            ->where('sales_order_details.so_id',$request->id)->get();
            if($so_mapping->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, SO Is Used In Customer Replacement SO Mapping.",
                ]);
            }

            $so_short_close = SOShortClose::
            leftJoin('sales_order_details','sales_order_details.so_details_id','=','so_short_close.so_details_id')
            ->where('sales_order_details.so_id',$request->id)->get();
            if($so_short_close->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, SO Is Used In Customer Replacement SO Short Close.",
                ]);
            }

            $tr_so_short_close = TransactionSOShortClose::
            leftJoin('sales_order_details','sales_order_details.so_details_id','=','transaction_so_short_close.so_details_id')
            ->where('sales_order_details.so_id',$request->id)->get();
            if($tr_so_short_close->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, SO Is Used In SO Short Close.",
                ]);
            }

            $dispatch = DispatchPlanDetails::
            leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
            ->where('sales_order_details.so_id',$request->id)->get();
            if($dispatch->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, SO Is Used In Dispatch Plan.",
                ]);
            }

            SalesOrder::destroy($request->id);

            $so_data = SalesOrderDetail::where('so_id','=',$request->id)->get();
          
            foreach ($so_data as $ctKey => $ctVal) {
                SalesOrderDetailsDetails::where('so_details_id', '=', $ctVal->so_details_id)->delete();
            }

            // $mrData = SalesOrderDetail::select('material_request_details.mr_id')
            // ->leftJoin('material_request_details','material_request_details.mr_details_id','sales_order_details.mr_details_id')
            // ->where('sales_order_details.so_id',$request->id)->get();

            // if($mrData->isNotEmpty()){
            //     foreach ($mrData as $ctKey => $ctVal) {
            //         MaterialRequest::where('mr_id', '=', $ctVal->mr_id)->update([
            //             'approval_type_id_fix' => '4'
            //         ]);
            //     }
            // }


             $mrData = SalesOrderDetail::select('material_request_details.mr_id')
            ->leftJoin('material_request_details','material_request_details.mr_details_id','sales_order_details.mr_details_id')
            ->where('sales_order_details.so_id', $request->id)
            ->groupBy('material_request_details.mr_id')
            ->get();

            foreach ($mrData as $data) {
                $mrId = $data->mr_id;

                $soMrDetailIds = MaterialRequestDetail::where('mr_id', $mrId)
                    ->where('form_type', 'PO')
                    ->pluck('mr_details_id')
                    ->toArray();

               
                $existsInPO = PurchaseRequisitionDetails::whereIn('mr_details_id', $soMrDetailIds)->exists();

                
                if (!$existsInPO) {
                    MaterialRequest::where('mr_id', $mrId)->update([
                        'approval_type_id_fix' => 4
                    ]);
                }
            }

            

            SalesOrderDetail::where('so_id',$request->id)->delete();
            DB::commit();

            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }
        catch(\Exception $e){       
            DB::rollBack(); 
            getActivityLogs("Sales Order", "delete", $e->getMessage(),$e->getLine());

            // $errorMessage =  $e->errorInfo[2];
            // preg_match('/`([^`]+)`\.`([^`]+)`/', $errorMessage, $matches);

            // $tableName = $matches[2];            
        
            // $table = DeleteMessage($tableName);

            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
                // $error_msg = "You Can't Delete, SO Is Used In ".$table;
            }else{
                $error_msg = "Record Not Deleted";
            }
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }

    function checkDuplication(Request $request){
        $year_data = getCurrentYearData();

        // $middle_num = str_pad($request->woa_sequence, 4, "0", STR_PAD_LEFT);
        $isFound = SalesOrder::where('year_id', '=', $year_data->id)->max('so_sequence');
        if ($isFound != null) {
            $isFound++;
        } else {
            $isFound = 1;
        }

        $middle_num = str_pad($request->so_sequence, 4, "0", STR_PAD_LEFT);

        $postfix = $year_data->yearcode;


        $so_num_format = 'SO/' . $middle_num . '/' . $postfix;
       


        $isDuplicate = false;

        if($request->for == "add"){

            $quotData = SalesOrder::select(['id'])->where('so_sequence','=',$request->so_sequence)->where('year_id','=',$year_data->id)->get();

            if(!$quotData->isEmpty()){
                $isDuplicate = true;
            }


        }else{

            $quotData = SalesOrder::select(['id'])->where('so_sequence','=',$request->so_sequence)->where('year_id','=',$year_data->id)->where('id','!=',$request->id)->get();

            if(!$quotData->isEmpty()){
                $isDuplicate = true;
            }


        }

        if($isDuplicate == false){
             return response()->json([
                'response_code' => '1',
                'so_num_format' => $so_num_format,
                'response_message' => ''
            ]);
        }else{
             return response()->json([
                'response_code' => '0',
                'response_message' => 'Sales Order No. Is Already Exists'
            ]);
        }

    }

    public function getSalesOrderDetails(Request $request)
    {
       

        $salesOrderDetailsDetails = SalesOrderDetailsDetails::select([
            'sales_order_detail_details.*', 'items.item_code','sales_order_details.so_id as sales_orderDetailsId', 'sales_order_details.so_details_id  as so_details_id'
        ])
        ->leftJoin('items','items.id','=','sales_order_detail_details.item_id')        
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','sales_order_detail_details.so_details_id')                                
        ->where('sales_order_details.item_id', $request->item_id)
        ->get(); 

       
          
        if($salesOrderDetailsDetails != "")
        {       
             return response()->json([
                   'response_code' => '1',
                   'salesOrderDetailsDetailsData' => $salesOrderDetailsDetails,
                   'response_message' => ''
               ]);
           }
        else{
                return response()->json([
                   'response_code' => '0',
                   'response_message' => 'Item Not Found'
               ]);
        }


    }
    
    
    public function isPartInUse(Request $request){
        if(isset($request->mr_part_id) && $request->mr_part_id != ""){    
            $isFound = null;  
    
            $isFound =  SalesOrderDetail::where('mr_details_id','=',$request->mr_part_id)->first();    
    
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

     function getSearchCustomer(Request $request){
        $locationId = getCurrentLocation()->id;

        $search_customer = SalesOrder::select(['sales_order.id as so_id', 'sales_order.customer_name as c_name', 'sales_order.customer_group_id','sales_order.customer_district_id', 'sales_order.customer_pincode', 'sales_order.customer_reg_no','customer_groups.customer_group_name as cust_group','sales_order.customer_village','sales_order.customer_taluka','districts.id as dis_id','states.id as state_id','countries.id as country_id','districts.district_name as dis_name','talukas.taluka_name','villages.village_name','states.state_name as state_name','countries.country_name as co_name','dealers.dealer_name','dealers.id as dealer_id'])
        ->leftJoin('villages','villages.id','sales_order.customer_village')
        ->leftJoin('talukas','talukas.id','sales_order.customer_taluka')
        ->leftJoin('districts','districts.id','sales_order.customer_district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('customer_groups','customer_groups.id','=','sales_order.customer_group_id')
        ->leftJoin('dealers','dealers.id','=','sales_order.dealer_id')
        ->where('sales_order.so_from_id_fix','=','1')
        ->where('current_location_id',$locationId)
        ->groupBy('sales_order.customer_name', 'sales_order.customer_district_id', 'sales_order.customer_group_id','sales_order.customer_village','sales_order.customer_reg_no','sales_order.customer_pincode',)
        ->get();

        return response()->json([
            'response_code' => 1,
            'search_customer' => $search_customer,          
        ]);
    } 


    public function getOldCustomer(Request $request){

        $so_id = isset($request->so_ids) ? $request->so_ids : '';
        
        if($so_id){
            $old_customer = SalesOrder::select(['sales_order.id as so_id', 'sales_order.customer_name as c_name', 'sales_order.customer_group_id','sales_order.customer_district_id', 'sales_order.customer_pincode', 'sales_order.customer_reg_no','customer_groups.customer_group_name as cust_group','districts.id as dis_id','states.id as state_id','countries.id as country_id','districts.district_name as dis_name','states.state_name as state_name','countries.country_name as co_name','dealers.dealer_name','dealers.id as dealer_id','talukas.id as taluka_id',
            'sales_order.customer_village',
            'sales_order.area',
            'villages.village_name as customer_village',  
            'sales_order.customer_taluka',
            // 'talukas.taluka_name as customer_taluka',
            
            ])
            ->leftJoin('districts','districts.id','sales_order.customer_district_id')
            ->leftJoin('villages','villages.id','=','sales_order.customer_village')
            ->leftJoin('talukas', 'talukas.district_id','districts.id')
            ->leftJoin('states','states.id','=','districts.state_id')
            ->leftJoin('countries','countries.id','=','states.country_id')
            ->leftJoin('customer_groups','customer_groups.id','=','sales_order.customer_group_id')
            ->leftJoin('dealers','dealers.id','=','sales_order.dealer_id')
            ->where('sales_order.id','=',$so_id)          
            ->first();

            if($old_customer){
                return response()->json([
                    'response_code' => 1,
                    'customer' => $old_customer,          
                ]);
            }
        }else{
            return response()->json([
                'response_code' => 0,
                'response_message' => "",
            ]);
        }
    }

    public function getLastSoData(Request $request)
    {
        $latestRecord = SalesOrder::select('sales_order.*','sales_order.customer_district_id', 'states.id as state_id', 'districts.id as district_id', 'countries.id as country_id', 'talukas.id as taluka_id')
        ->leftJoin('districts', 'districts.id','sales_order.customer_district_id')
        ->leftJoin('talukas', 'talukas.district_id','districts.id')
        ->leftJoin('states','states.id', 'districts.state_id')
        ->leftJoin('countries','countries.id', 'states.country_id')
        ->where('so_from_id_fix','=',1)
        ->orderBy('sales_order.created_on','desc')
        ->first();

        $soDetails = [];
        if ($latestRecord) {
            $soDetails = SalesOrderDetail::select('sales_order_details.*','items.item_name','units.unit_name','items.item_code','item_groups.item_group_name')->where('so_id', $latestRecord->id)
            ->leftJoin('items','items.id','=','sales_order_details.item_id')
            ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
            ->leftJoin('units','units.id','=','items.unit_id')
            ->get();
        }

        return response()->json([
            'response_code' => 1,
            'last_data' => $latestRecord,
            'soDetails' => $soDetails
        ]);
    }


    function getSearchSOCustomer(Request $request){
        $locationId = getCurrentLocation()->id;

        $search_customer = SalesOrder::select(['sales_order.id as so_id', 'sales_order.customer_name'])      
        ->where('sales_order.so_from_id_fix','=','1')
        ->where('current_location_id',$locationId)
        ->groupBy('sales_order.customer_name',)
        ->get();

        return response()->json([
            'response_code' => 1,
            'search_customer' => $search_customer,          
        ]);
    } 


    public function getOldCustomerSoNo(Request $request){
        $locationId = getCurrentLocation()->id;
        $SoNo = SalesOrder::select(['sales_order.id', 'sales_order.so_number'])
        ->where('current_location_id',$locationId)
        ->where('sales_order.so_from_id_fix','=',1)           
        ->where('sales_order.customer_name','=',$request->old_customer)          
        ->get();

        if($SoNo){
            return response()->json([
                'response_code' => 1,
                'SoNo' => $SoNo,          
            ]);
            
        }else{
            return response()->json([
                'response_code' => 0,
                'response_message' => "",
            ]);
        }
    }


    public function getOldSoNo(Request $request){

        $sales_order = SalesOrder::select(['sales_order.*', 'sales_order.customer_district_id', 'states.id as state_id', 'districts.id as district_id', 'countries.id as country_id', 'talukas.id as taluka_id'])
        ->leftJoin('districts', 'districts.id','sales_order.customer_district_id')
        ->leftJoin('talukas', 'talukas.district_id','districts.id')
        ->leftJoin('states','states.id', 'districts.state_id')
        ->leftJoin('countries','countries.id', 'states.country_id')
        ->where('sales_order.id',$request->so_id)->first();

        $sales_order_part = SalesOrderDetail::select(['sales_order_details.*','items.item_code','item_groups.item_group_name','units.unit_name','items.item_name','material_request_details.mr_qty','material_request.mr_id'])
        ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
        ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
        ->leftJoin('items','items.id','=','sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('sales_order_details.so_id',$request->so_id)->get(); 

        if($sales_order_part){
            return response()->json([
                'response_code' => 1,
                'sales_order' => $sales_order,
                'sales_order_part' => $sales_order_part,          
            ]);
            
        }else{
            return response()->json([
                'response_code' => 0,
                'response_message' => "",
            ]);
        }

    }


    public function getOldItemSo(Request $request){

        $id = explode(',', $request->sod_ids);
        
        $sales_order_part = SalesOrderDetail::select(['sales_order_details.*','items.item_code','item_groups.item_group_name','units.unit_name','items.item_name','material_request_details.mr_qty','material_request.mr_id'])
        ->leftJoin('material_request_details','material_request_details.mr_details_id','=','sales_order_details.mr_details_id')
        ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
        ->leftJoin('items','items.id','=','sales_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->whereIn('sales_order_details.so_details_id',$id)->get(); 

     
        
        $salesOrderId = SalesOrderDetail::select(['so_details_id'])->whereIn('sales_order_details.so_details_id',$id)->get(); 
        
     
        $salesOrderDetailsDetails = SalesOrderDetailsDetails::select([
            'sales_order_details.item_id as mitem_id','sales_order_detail_details.item_id', 'sales_order_detail_details.so_qty',  'sales_order_detail_details.sod_details_id',
        ])                   
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','sales_order_detail_details.so_details_id')            
        ->whereIn('sales_order_detail_details.so_details_id',$salesOrderId)->get(); 

        $changedItemIds = [];

        if($sales_order_part != null){

            $sales_order_part->each(function ($item) use (&$changedItemIds, &$isAnyPartInUse) {  
                $item->in_use = true;  
                $item->used_qty = $item->so_qty;

                $changedItemId = SalesOrderDetail::leftJoin('items', 'items.id', '=', 'sales_order_details.item_id')           
                ->where('sales_order_details.so_details_id', $item->so_details_id)
                ->where(function($query) {
                    $query->where('items.status', 'deactive')
                        ->orWhere('items.service_item', 'Yes');
                })
                ->pluck('sales_order_details.item_id')
                ->first();
                if ($changedItemId) {

                    $changedItemIds[] = $changedItemId; // Now it works
                }
        
               
            return $item;
            })->values();
        }       

    if($changedItemIds){
        $item = Item::select('id','item_name')->whereIN('id',$changedItemIds)->get();
    }else{
        $item = '';
    }

    if($sales_order_part){
        return response()->json([
            'response_code' => 1,
            'so_part_details' => $sales_order_part,
            'so_part_details_details' => $salesOrderDetailsDetails,
            'item' => $item,
            'response_code' => '1',
            'response_message' => '',        
        ]);
        
    }else{
        return response()->json([
            'response_code' => 0,
            'response_message' => "",
        ]);
    }
    }


    public function getCountyandStateForLocation(Request $request){
        $locationId = getCurrentLocation()->id;

        $location_data = Location::select(['states.country_id','districts.state_id'])
        ->leftJoin('villages','villages.id','=','locations.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')        
        ->where('locations.id','=',$locationId)
        ->first();


        return response()->json([
            'response_code' => 1,
            'location_data' => $location_data,          
        ]);

    }


    public function getSoDealer(Request $request){

        $location = LocationCustomerGroupMapping::select('location_id')->where('customer_group_id',$request->customer_group_id)->get();

        $location_data = Location::select(['districts.state_id'])
        ->leftJoin('villages','villages.id','=','locations.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        //->leftJoin('states','states.id','=','districts.state_id')
        ->whereIn('locations.id',$location)
        ->groupBy('districts.state_id')
        ->get();
        // group by 


        if(isset($request->id)){
            $SoDealerId = DB::table('sales_order')
            ->where('id',$request->id)
            ->value('dealer_id');

            $dealers = Dealer::select('dealers.id', 'dealers.dealer_name', 'districts.state_id')
            ->leftJoin('villages', 'villages.id', '=', 'dealers.village_id')
            ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
            ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
            // ->leftJoin('states', 'states.id', '=', 'districts.state_id')
            ->where(function ($query) use ($SoDealerId, $location_data) {
                $query->where('dealers.id', '=', $SoDealerId) // Specific dealer by ID
                      ->orWhere(function ($subQuery) use ($location_data) {
                          $subQuery->whereIn('districts.state_id',$location_data)
                                   ->where('dealers.status', '=', 'active'); // Active dealers in the state
                      });
            })
            
            ->orderBy('dealers.dealer_name', 'asc')
            ->get();

        }else{
            
            $dealers = Dealer::select('dealers.id', 'dealers.dealer_name', 'districts.state_id')
            ->leftJoin('villages', 'villages.id', '=', 'dealers.village_id')
            ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
            ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
            //->leftJoin('states', 'states.id', '=', 'districts.state_id')
            ->whereIn('districts.state_id',$location_data)
            ->where('dealers.status', '=', 'active')
            ->orderBy('dealers.dealer_name', 'asc')
            ->get();
        }

        foreach($dealers as $dkey => $dval){
            // dd($dval);
             $dealerDate = DealerAgreement::select(DB::raw('MAX(dealer_agreement.agreement_end_date) as agreement_end_date'))                
            ->where('dealer_agreement.dealer_id','=',$dval->id)
            ->groupBy('dealer_agreement.dealer_id')
            ->first();

            $dval->agreement_end_date = $dealerDate != '' ? Date::createFromFormat('Y-m-d', $dealerDate->agreement_end_date)->format('d/m/Y') : '';

        }
        $location = getCurrentLocation();   
    
        if(isset($request->id)){             
            
            //  below quey is use main item and fitting item yes

            $fittingItemsYesIds = Item::where('items.fitting_item','=','Yes')->pluck('items.id')          ->toArray(); 
            
            $changedItemIds = SalesOrderDetail::leftJoin('items', 'items.id', '=', 'sales_order_details.item_id')           
            ->where('sales_order_details.so_id', $request->id)
            ->where(function($query) {
                $query->where('items.status', 'deactive')
                    ->orWhere('items.service_item', 'Yes');
            })
            ->pluck('sales_order_details.item_id')
            ->toArray();      

            $missingPriceItemsIds = SalesOrderDetail::
            leftJoin('price_list_details', function ($join) use ($request) {
                $join->on('sales_order_details.item_id', '=', 'price_list_details.item_id')
                    ->where('price_list_details.customer_group_id', '=', $request->customer_group_id);
            })
            ->where('sales_order_details.so_id', $request->id)
            ->pluck('sales_order_details.item_id')
            ->toArray();           
            
            $priceListItems = PriceListDetails::
            leftJoin('items','items.id' ,'price_list_details.item_id')
            ->Join('item_groups','item_groups.id','=','items.item_group_id')
            ->Join('units','units.id','=','items.unit_id')
            ->where('items.status', 'active')
            ->where('items.service_item','No')        
            ->where('price_list_details.customer_group_id',$request->customer_group_id)
            ->pluck('price_list_details.item_id')
            ->toArray();  

             if(empty($priceListItems)){
                $mappedItems = getItem($changedItemIds);
            } else{
               $mappedItems = Item::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name',)
               ->Join('item_groups','item_groups.id','=','items.item_group_id')
               ->Join('units','units.id','=','items.unit_id')
               ->whereIn('items.id',array_merge($changedItemIds,$missingPriceItemsIds,$priceListItems,$fittingItemsYesIds))  
               ->get();               
            }
               
            // end query
         
            //  below quey is use Fitting item No show in Eye Model


            $changedFittingItemIds = SalesOrderDetail::
            leftjoin('sales_order_detail_details','sales_order_detail_details.so_details_id','sales_order_details.so_details_id')
            ->leftJoin('items', 'items.id', '=', 'sales_order_detail_details.item_id')           
            ->where('sales_order_details.so_id', $request->id)
            ->where(function($query) {
                $query->where('items.status', 'deactive')
                    ->orWhere('items.service_item', 'Yes');
            })
            ->pluck('sales_order_detail_details.item_id')
            ->toArray();

            $missingFittingPriceItemsIds = SalesOrderDetail::
            leftjoin('sales_order_detail_details','sales_order_detail_details.so_details_id','sales_order_details.so_details_id')
            ->leftJoin('price_list_details', function ($join) use ($request) {
                $join->on('sales_order_detail_details.item_id', '=', 'price_list_details.item_id')
                    ->where('price_list_details.customer_group_id', '=', $request->customer_group_id);
            })
            ->where('sales_order_details.so_id', $request->id)
            ->whereNotNull('sales_order_detail_details.item_id') 
            ->pluck('sales_order_detail_details.item_id')
            ->toArray();  



            $priceListfittingItems = PriceListDetails::
            leftJoin('items','items.id' ,'price_list_details.item_id')
            ->Join('item_groups','item_groups.id','=','items.item_group_id')
            ->Join('units','units.id','=','items.unit_id')
            ->where(['fitting_item' => 'no', 'require_raw_material_mapping' => 'no'])
            ->where('items.status', 'active')
            ->where('items.service_item','No')        
            ->where('price_list_details.customer_group_id',$request->customer_group_id)
            ->pluck('price_list_details.item_id')
            ->toArray(); 
           

            if(empty($priceListfittingItems)){
                $fittingmappedItems = getSalesFittingItem($changedFittingItemIds);
            }else{
                $fittingmappedItems = Item::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name',)
                ->Join('item_groups','item_groups.id','=','items.item_group_id')
                ->Join('units','units.id','=','items.unit_id')
                ->whereIn('items.id',array_merge($changedFittingItemIds,$missingFittingPriceItemsIds,$priceListfittingItems))  
                ->get();
            }         
          
            
            
        }else{

            //  below quey is use main item and fitting item yes

            $fittingItemsYesIds = Item::where('items.fitting_item','=','Yes')->pluck('items.id')          ->toArray(); 

            $priceListItems = PriceListDetails::
             leftJoin('items','items.id' ,'price_list_details.item_id')
            ->Join('item_groups','item_groups.id','=','items.item_group_id')
            ->Join('units','units.id','=','items.unit_id')
            ->where('items.status', 'active')
            ->where('items.service_item','No')        
            ->where('price_list_details.customer_group_id',$request->customer_group_id)
            ->pluck('items.id')
            ->toArray(); 

            if(empty($priceListItems)){
                $changedItemIds =  [];
                $mappedItems = getItem($changedItemIds);

            }else{               
                $mappedItems = Item::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name',)
                ->Join('item_groups','item_groups.id','=','items.item_group_id')
                ->Join('units','units.id','=','items.unit_id')      
                ->whereIn('items.id',array_merge($priceListItems,$fittingItemsYesIds))  
                ->get();
            }

            // end query
         
            //  below quey is use Fitting item No show in Eye Model
            $fittingmappedItems = PriceListDetails::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name',)
            ->leftJoin('items','items.id' ,'price_list_details.item_id')
            ->Join('item_groups','item_groups.id','=','items.item_group_id')
            ->Join('units','units.id','=','items.unit_id')
            ->where(['fitting_item' => 'no', 'require_raw_material_mapping' => 'no'])
            ->where('items.status', 'active')
            ->where('items.service_item','No')        
            ->where('price_list_details.customer_group_id',$request->customer_group_id)
            ->get();           

             if($fittingmappedItems->isEmpty()){
                $changedFittingItemIds =  [];
                $fittingmappedItems = getSalesFittingItem($changedFittingItemIds);
            }
             // end query
            
        }

        return response()->json([
            'so_dealer'        => $dealers,
            'mappedItems' => $mappedItems,
            'fittingmappedItems' => $fittingmappedItems,
            'response_code'    => '1',
            'response_message' => '',
        ]);
    }

    public function managePendingMr(){
        return view('manage.manage-pending_mr_list');
    }

    public function indexPendingMr(){

        $yearIds = getCompanyYearIdsToTill();
        $locationCode = getCurrentLocation();  
        
        $so_mr_detail_id = SalesOrderDetail::select(['sales_order_details.mr_details_id'])
        ->whereNotNull('sales_order_details.mr_details_id')
        ->get();    

        $mrData = MaterialRequestDetail:: select(['material_request.mr_number','material_request.mr_date', 
        'material_request.mr_id','locations.location_name'
        ]) 
        ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')  
        ->leftJoin('locations','locations.id','=','material_request.current_location_id')   
        ->whereIn('material_request.approval_type_id_fix',['4','5']) 
        ->where('material_request.to_location_id','=',$locationCode->id)
        ->whereNotIn('material_request_details.mr_details_id',$so_mr_detail_id)    
        ->whereIN('material_request.year_id',$yearIds)   
        ->groupBy('material_request.mr_id');

      
    
       return DataTables::of($mrData)
       ->editColumn('mr_date', function($mrData){           
           if ($mrData->mr_date != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d', $mrData->mr_date)->format('d/m/Y'); 
               
               return $formatedDate1;
    
           }else{
               return '';
           }
       })  
    
       ->rawColumns(['mr_date'])
       ->make(true);
    
    }

}