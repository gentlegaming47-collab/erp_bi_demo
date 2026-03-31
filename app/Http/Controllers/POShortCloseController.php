<?php

namespace App\Http\Controllers;
use DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\GRNMaterialDetails;
use App\Models\GRNMaterial;
use App\Models\Admin;

use App\Models\Supplier;
use Carbon\Carbon;
use Date;
use App\Models\POShortClose;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetails;
class POShortCloseController extends Controller
{
    public function manage()
    {
        return view('manage.manage-po_short_close');
    }

    public function create()
    {
        return view('add.add-po_short_close');
    }

    public function index(POShortClose $PoShort,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();
        $po_short_data = PurchaseOrder::select(['purchase_order.po_number', 'purchase_order.po_id as po_id', 'purchase_order_short_close.sc_date','suppliers.supplier_name','items.item_name','items.item_code', 'item_groups.item_group_name', 'units.unit_name','purchase_order_details.po_qty', 'purchase_order_details.del_date', 'purchase_order_details.del_date', 'purchase_order.po_date', 'units.unit_name','purchase_order_short_close.reason', 'purchase_order_short_close.last_by_user_id','purchase_order_short_close.last_on', 'purchase_order_short_close.sc_qty',
        'purchase_order_short_close.created_by_user_id','purchase_order_short_close.created_on','purchase_order_short_close.year_id', 'purchase_order_short_close.posc_id', 'locations.location_name','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])

        ->leftJoin('purchase_order_details','purchase_order_details.po_id', 'purchase_order.po_id')

        ->leftJoin('purchase_order_short_close','purchase_order_short_close.po_details_id', 'purchase_order_details.po_details_id')

        ->leftJoin('locations','locations.id', 'purchase_order.to_location_id')
        ->leftJoin('suppliers','suppliers.id', 'purchase_order.supplier_id')
        ->leftJoin('items','items.id', 'purchase_order_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        ->leftJoin('units','units.id', 'items.unit_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'purchase_order_short_close.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'purchase_order_short_close.last_by_user_id')
        ->where('purchase_order_short_close.current_location_id','=',$location->id)
        ->where('purchase_order_short_close.year_id','=',$year_data->id);
    
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $po_short_data->whereDate('purchase_order_short_close.sc_date','>=',$from);

                $po_short_data->whereDate('purchase_order_short_close.sc_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $po_short_data->where('purchase_order_short_close.sc_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $po_short_data->where('purchase_order_short_close.sc_date','<=',$to);

        }  


        return DataTables::of($po_short_data)
        ->editColumn('created_by_user_id', function($po_short_data){
            if($po_short_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$po_short_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($po_short_data){
            if($po_short_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$po_short_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('created_on', function($po_short_data){
            if ($po_short_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $po_short_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('purchase_order_short_close.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order_short_close.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($po_short_data){
            if ($po_short_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $po_short_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('purchase_order_short_close.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order_short_close.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('po_qty', function($po_short_data){

            return $po_short_data->po_qty > 0 ? number_format((float)$po_short_data->po_qty, 3, '.','') : number_format((float) 0, 3,'.','');
        })
        ->editColumn('sc_qty', function($po_short_data){

            return $po_short_data->sc_qty > 0 ? number_format((float)$po_short_data->sc_qty, 3, '.') : number_format((float) 0, 3,'.','');
        })
        ->editColumn('sc_date', function($po_short_data){
            if ($po_short_data->sc_date != null) {
                $date = Date::createFromFormat('Y-m-d', $po_short_data->sc_date)->format(DATE_FORMAT);
                
                return $date;
            }else{
                return '';
            }
        })
        ->editColumn('po_date', function($po_short_data){
            if ($po_short_data->po_date != null) {
                $po_date = Date::createFromFormat('Y-m-d', $po_short_data->po_date)->format(DATE_FORMAT);
                return $po_date;
            }else{
                return '';
            }
        })
        ->editColumn('del_date', function($po_short_data){
            if ($po_short_data->del_date != null) {
                $po_date = Date::createFromFormat('Y-m-d', $po_short_data->del_date)->format(DATE_FORMAT);
                return $po_date;
            }else{
                return '';
            }
        })

        ->filterColumn('purchase_order_short_close.sc_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order_short_close.sc_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('purchase_order.po_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order.po_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('purchase_order_details.del_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(purchase_order_details.del_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('item_name', function($po_short_data){ 
            if($po_short_data->item_name != ''){
                $item_name = ucfirst($po_short_data->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

        // ->editColumn('sc_qty', function($po_short_data){
        //     if ($po_short_data->sc_qty != null) {
        //         return $po_short_data->sc_qty > 0 ?  $po_short_data->sc_qty : '';
        //     }
        // })
        ->addColumn('options',function($purchase_order){
            $action = "<div>";
            // if(hasAccess("purchase_order","edit")){
            // $action .="<a id='edit_a' href='".route('edit-purchase_order',['id' => base64_encode($purchase_order->po_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            // }
            if(hasAccess("po_short_close","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'date','sc_qty', 'po_date', 'options'])
        ->make(true);



    }

    public function getPOData()
    {

        // $year_data = getCurrentYearData();
        $yearIds = getCompanyYearIdsToTill();

        $locationCode = getCurrentLocation()->id;

       
            $po_data = PurchaseOrderDetails::select([
                'purchase_order.po_number', 
                'purchase_order.po_id as po_id', 
                'purchase_order.po_date',
                'purchase_order_details.po_qty',
                'purchase_order_details.del_date',
                'locations.location_name',
                'suppliers.supplier_name',
                'items.item_name',
                'items.item_code', 
                'item_groups.item_group_name', 
                 'units.unit_name as unitName', 
                'purchase_order_details.po_details_id as POId',


                DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0) FROM purchase_order_details AS pod WHERE pod.po_details_id = purchase_order_details.po_details_id)  -  (SELECT IFNULL(SUM(psid.sc_qty),0)
                FROM purchase_order_short_close AS psid  WHERE psid.po_details_id = purchase_order_details.po_details_id) - (SELECT IFNULL(SUM(gid.grn_qty),0)
                FROM material_receipt_grn_details AS gid WHERE gid.po_details_id = purchase_order_details.po_details_id)) as pend_po_qty"),'units.unit_name'
    
                // DB::raw("(((
                //     SELECT IFNULL(SUM(pod.po_qty),0)
                //     FROM purchase_order_details AS pod
                //     WHERE pod.po_details_id = purchase_order_details.po_details_id)
                //     -
                //     (SELECT IFNULL(SUM(psid.sc_qty),0)
                //     FROM purchase_order_short_close AS psid
                //     WHERE psid.po_details_id = purchase_order_details.po_details_id))
                //     -
                //     (CASE 
                //         WHEN material_receipt_grn_details.po_details_id IS NOT NULL THEN
                //             (SELECT IFNULL(SUM(gid.grn_qty),0)
                //             FROM material_receipt_grn_details AS gid
                //             WHERE gid.po_details_id = purchase_order_details.po_details_id)
                //         ELSE 0
                //     END)
                // ) AS pend_po_qty"
                // ),
                
            ])
                ->leftJoin('purchase_order','purchase_order.po_id', 'purchase_order_details.po_id')
                
                ->leftJoin('locations','locations.id', 'purchase_order.to_location_id')

                ->leftJoin('purchase_order_short_close','purchase_order_short_close.po_details_id', 'purchase_order_details.po_details_id')             
        
                ->leftJoin('suppliers','suppliers.id', 'purchase_order.supplier_id')
        
                ->leftJoin('items','items.id', 'purchase_order_details.item_id')

                ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')

              //  ->leftJoin('units','units.id', 'items.unit_id')

                
        
                ->leftJoin('units','units.id', 'items.unit_id')
        
                ->leftJoin('material_receipt_grn_details', 'material_receipt_grn_details.po_details_id', '=', 'purchase_order_details.po_details_id')          
                
                // ->where('purchase_order.year_id',$year_data->id)
                ->whereIn('purchase_order.year_id',$yearIds)
        
                ->where('purchase_order.current_location_id',$locationCode)

                ->where('purchase_order.is_approved','=',1)
        
              ->having('pend_po_qty','>',0)

              ->groupBy('purchase_order_details.po_details_id')
        
                ->get();
    
         
        if($po_data != null){
            $po_data = $po_data->filter(function ($po_data) {

                if($po_data->po_date != null){
                    $po_data->po_date = Date::createFromFormat('Y-m-d', $po_data->po_date)->format('d/m/Y');
                }

                if($po_data->del_date != null){
                    $po_data->del_date = Date::createFromFormat('Y-m-d', $po_data->del_date)->format('d/m/Y');
                }

                return $po_data;

            })->values();
        }



            if ($po_data != null) {
                    return response()->json([
                        'response_code' => '1',
                        'po_data' => $po_data
                    ]);
                } else {
                    return response()->json([
                        'response_code' => '0',
                        'po_data' => []
                    ]);
                }
    }
  
    // public function getPOData()
    // {

    //     $year_data = getCurrentYearData();

    //     $locationCode = getCurrentLocation()->id;

    //     $po_data = PurchaseOrderDetails::select(['purchase_order.po_number', 'purchase_order.po_id as po_id', 'purchase_order.po_date','suppliers.supplier_name','items.item_name','items.item_code', 'purchase_order_details.po_details_id as POId',
    //     DB::raw("((SELECT IFNULL(SUM(pod.po_qty),0)
    //             FROM purchase_order_details AS pod
    //             WHERE pod.po_details_id = material_receipt_grn_details.po_details_id)
    //         -
    //         (SELECT IFNULL(SUM(gid.grn_qty),0)
    //             FROM material_receipt_grn_details AS gid
    //             WHERE gid.po_details_id = material_receipt_grn_details.po_details_id))

    //         -
    //         (SELECT  IFNULL(SUM(psid.sc_qty),0)
    //             FROM purchase_order_short_close AS psid
    //             WHERE psid.po_details_id = purchase_order_details.po_details_id)
    //         AS pend_po_qty"),

    //     'units.unit_name'])


    //     ->leftJoin('purchase_order','purchase_order.po_id', 'purchase_order_details.po_id')
    //     ->leftJoin('purchase_order_short_close','purchase_order_short_close.po_details_id', 'purchase_order_details.po_details_id')
    //     ->leftJoin('material_receipt_grn_details', 'material_receipt_grn_details.po_details_id', '=', 'purchase_order_details.po_details_id')

    //     ->leftJoin('suppliers','suppliers.id', 'purchase_order.supplier_id')

    //     ->leftJoin('items','items.id', 'purchase_order_details.item_id')

    //     ->leftJoin('units','units.id', 'items.unit_id')

    //     ->where('purchase_order.year_id',$year_data->id)

    //     ->where('purchase_order.current_location_id',$locationCode)


    //     ->having('pend_po_qty','>',0)

    //     ->get();

    //     if($po_data != null){
    //         $po_data = $po_data->filter(function ($po_data) {

    //             if($po_data->po_date != null){
    //                 $po_data->po_date = Date::createFromFormat('Y-m-d', $po_data->po_date)->format('d/m/Y');
    //             }

    //             return $po_data;

    //         })->values();
    //     }



    //         if ($po_data != null) {
    //                 return response()->json([
    //                     'response_code' => '1',
    //                     'po_data' => $po_data
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'response_code' => '0',
    //                     'po_data' => []
    //                 ]);
    //             }
    // }
  

    public function store(Request $request){

        $validated = $request->validate([
            'po_short_date' => 'required',
        ],
        [
           'po_short_date.required' => 'Please Enter Po Short Close Date',
        ]);


        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;
        DB::beginTransaction();

        try{

            $request->po_short_details = json_decode($request->po_short_details,true);

            if(isset($request->po_short_details) && !empty($request->po_short_details)){
                foreach($request->po_short_details as $ctKey => $ctVal){

                    if(isset($ctVal['po_detail_id'])){

                        $poQtySum =  round(PurchaseOrderDetails::where('po_details_id',$ctVal['po_detail_id'])->sum('po_qty'),3);

                        $usePOScQtySum = round(POShortClose::where('po_details_id',$ctVal['po_detail_id'])->sum('sc_qty'),3);

                        $poQty = isset($ctVal['so_po_qty']) && $ctVal['so_po_qty'] > 0 ? $ctVal['so_po_qty'] : 0;
                        $poScQtySum = $usePOScQtySum + $poQty;     
                        

                        if($poQtySum < $poScQtySum){
                            DB::rollBack();
                            return response()->json([
                                'response_code' => '0',
                                'response_message' => 'PO Qty. Is Used',                               
                            ]);
                        }

                    }
                    
                    
                    if($ctVal != null){
                           $oa_short_data =  POShortClose::create([
                            'current_location_id'=>$locationID,
                                'po_details_id'=> (isset($ctVal['po_detail_id']) &&  $ctVal['po_detail_id'] != "") ? $ctVal['po_detail_id'] : null,
                                'sc_date'=> Date::createFromFormat('d/m/Y', $request->po_short_date)->format('Y-m-d'),
                                'sc_qty'=>(isset($ctVal['so_po_qty']) && $ctVal['so_po_qty'] > 0) ? $ctVal['so_po_qty'] : 0,
                                'reason'=>(isset($ctVal['po_reason']) &&  $ctVal['po_reason'] != "") ? $ctVal['po_reason'] : null,
                                'year_id' => $year_data->id,
                                'company_id' => Auth::user()->company_id,
                                'created_by_user_id' => Auth::user()->id,
                                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                           ]);
                    }
                }
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Inserted Successfully.',
                ]);
            }else{
                DB::rollBack();
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Inserted',
                ]);
            }




        }catch(\Exception $e){
            DB::rollBack();
            getActivityLogs("PO Short Close", "add", $e->getMessage(),$e->getLine());  
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }

    }


    public function destroy(Request $request)
    {
        try{
           
            POShortClose::where('posc_id','=',$request->id)->delete();
            
            
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){   
            DB::rollBack();
            getActivityLogs("PO Short Close", "delete", $e->getMessage(),$e->getLine());  

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


}