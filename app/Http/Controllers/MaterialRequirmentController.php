<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrderDetail;
use App\Models\SalesOrder;
use App\Models\MaterialRequestDetail;
use App\Models\GRNMaterialDetails   ;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;

class MaterialRequirmentController extends Controller
{
    public function manage()
    {
        return view('manage.manage-material_requirement');
    }
    

    // public function index(SalesOrderDetail $SalesOrderDetail,Request $request,DataTables $dataTables)
    // {       
    //     $year_data = getCurrentYearData();
    //     $location = getCurrentLocation();


    //     $sodData = SalesOrderDetail::select(['sales_order_details.so_details_id'])
    //     ->leftJoin('sales_order', 'sales_order.id','=','sales_order_details.so_id')    
    //     ->where('sales_order.current_location_id', $location->id)   
    //     ->where('sales_order_details.fitting_item','=','no');    

    //     if($request->sales_order_id !=''){
    //         $sodData->where('sales_order_details.so_id','=',$request->sales_order_id);
    //     }

    //     $sodData  = $sodData->get();


    //     $SalesOrderDetail = SalesOrderDetail::select([
    //                         'items.item_name',
    //                         'items.item_code',
    //                         'units.unit_name',
    //                         'sales_order_details.item_id',

    //                         DB::raw('SUM(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE sales_order_details.so_details_id = dispatch_plan_details.so_details_id), 0) ) as pending_so_qty'),
                          
    //                         DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock 
    //                         WHERE sales_order_details.item_id = location_stock.item_id  AND location_stock.location_id = $location->id) as stock_qty"),


    //                         // DB::raw("(SELECT IFNULL(SUM(material_receipt_grn_details.grn_qty),0) FROM material_receipt_grn_details WHERE sales_order_details.item_id = material_receipt_grn_details.item_id 
    //                         // ) as grn_qty"),
                            
    //                     ])
    //                     ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
    //                     ->leftJoin('sales_order_detail_details', 'sales_order_detail_details.so_details_id', '=', 'sales_order_details.so_details_id')
    //                     ->leftJoin('items', 'items.id','=','sales_order_details.item_id')                       
    //                     ->leftJoin('units', 'units.id','=','items.unit_id')          
    //                     ->whereIn('sales_order_details.so_details_id', $sodData)
    //                     ->where('sales_order.current_location_id', $location->id)
    //                     ->groupBy('sales_order_details.item_id', 'items.item_name', 'items.item_code', 'units.unit_name')
    //                     ->get();
                        
    //                     // dd($SalesOrderDetail);
    //                     return DataTables::of($SalesOrderDetail)

    //                     ->editColumn('pending_so_qty', function($SalesOrderDetail){             
    //                         return $SalesOrderDetail->pending_so_qty > 0 ?  number_format((float)$SalesOrderDetail->pending_so_qty, 3, '.') : number_format((float) 0, 3,'.');
    //                     })

                      
    //                     ->editColumn('stock_qty', function($SalesOrderDetail){             
    //                         return $SalesOrderDetail->stock_qty > 0 ?  number_format((float)$SalesOrderDetail->stock_qty, 3, '.') : number_format((float) 0, 3,'.');
    //                     })
                        
    //                     ->editColumn('pend_mat_rec_qty', function($SalesOrderDetail){ 
    //                         $location = getCurrentLocation();
                                    
    //                         $mr_qty = MaterialRequestDetail::
    //                         leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
    //                         ->where('material_request.current_location_id', $location->id)
    //                         ->where('material_request_details.item_id',$SalesOrderDetail->item_id)                            
    //                         ->sum('material_request_details.mr_qty');


    //                         $grn_qty = GRNMaterialDetails::
    //                         leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
    //                         ->where('grn_material_receipt.current_location_id', $location->id)
    //                         ->where('material_receipt_grn_details.item_id',$SalesOrderDetail->item_id)                            
    //                         ->sum('material_receipt_grn_details.grn_qty');

                        

    //                         $pend_mat_rec_qty =  ($mr_qty - $grn_qty);          
    //                         return $pend_mat_rec_qty > 0 ?  number_format((float)$pend_mat_rec_qty, 3, '.') : number_format((float) 0, 3,'.');
    //                     })
    //                     ->editColumn('need_qty', function($SalesOrderDetail){
    //                         $location = getCurrentLocation();
                                    
    //                         $mr_qty = MaterialRequestDetail::
    //                         leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id')
    //                         ->where('material_request.current_location_id', $location->id)
    //                         ->where('material_request_details.item_id',$SalesOrderDetail->item_id)                            
    //                         ->sum('material_request_details.mr_qty');

                           

    //                         $grn_qty = GRNMaterialDetails::
    //                         leftJoin('grn_material_receipt','grn_material_receipt.grn_id','=','material_receipt_grn_details.grn_id')
    //                         ->where('grn_material_receipt.current_location_id', $location->id)
    //                         ->where('material_receipt_grn_details.item_id',$SalesOrderDetail->item_id)                            
    //                         ->sum('material_receipt_grn_details.grn_qty');
                          
    //                         $needQty = ($SalesOrderDetail->pending_so_qty - $SalesOrderDetail->stock_qty - ($mr_qty - $grn_qty) ); 
                            
    //                         // dd($SalesOrderDetail->pending_so_qty,$SalesOrderDetail->stock_qty,$mr_qty ,$grn_qty);
    //                         return $needQty > 0 ?  number_format((float)$needQty, 3, '.') : number_format((float) 0, 3,'.');
    //                     })
                        
    //                     ->make(true);

    // }



//     public function index(SalesOrderDetail $SalesOrderDetail,Request $request,DataTables $dataTables)
//     {       
//         $year_data = getCurrentYearData();
//         $location = getCurrentLocation();
//         $yearIds = getCompanyYearIdsToTill();


//         $sodData = SalesOrderDetail::select(['sales_order_details.so_details_id'])
//         ->leftJoin('sales_order', 'sales_order.id','=','sales_order_details.so_id')    
//         ->where('sales_order.current_location_id', $location->id)   
//         ->where('sales_order_details.fitting_item','=','no');    

//         if($request->sales_order_id !=''){
//             $sodData->where('sales_order_details.so_id','=',$request->sales_order_id);
//         }

//         $sodData  = $sodData->get();


//         $SalesOrderDetail = SalesOrderDetail::select(['items.item_name','items.item_code','units.unit_name',
//         'sales_order_details.item_id',
//         DB::raw('SUM(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE sales_order_details.so_details_id = dispatch_plan_details.so_details_id), 0) ) as pending_so_qty'),
//         DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE sales_order_details.item_id = location_stock.item_id  AND location_stock.location_id = $location->id) as stock_qty"),
//         DB::raw("(SELECT IFNULL(SUM(material_request_details.mr_qty), 0) FROM material_request_details 
//         LEFT JOIN material_request ON material_request.mr_id = material_request_details.mr_id
//         WHERE material_request_details.item_id = sales_order_details.item_id 
//         AND material_request.current_location_id = $location->id
//         GROUP BY material_request_details.item_id) as mr_qty"),
//         DB::raw("(SELECT IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) FROM material_receipt_grn_details 
//         LEFT JOIN grn_material_receipt ON grn_material_receipt.grn_id = material_receipt_grn_details.grn_id
//         WHERE material_receipt_grn_details.item_id = sales_order_details.item_id 
//         AND grn_material_receipt.current_location_id = $location->id
//         AND grn_material_receipt.grn_type_id_fix = 3
//         GROUP BY material_receipt_grn_details.item_id) as grn_qty"),
//         DB::raw('(SELECT IFNULL(SUM(so_short_close.sc_qty), 0) FROM so_short_close 
//         LEFT JOIN sales_order_details ON sales_order_details.so_details_id = so_short_close.so_details_id
//         WHERE sales_order_details.mr_details_id IS NOT NULL) as sc_qty')

//         // DB::raw('(SELECT IFNULL(SUM(so_short_close.sc_qty), 0) FROM so_short_close 
//         // WHERE sales_order_details.so_details_id = so_short_close.so_details_id) as sc_qty')


//         // DB::raw("SUM(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE sales_order_details.so_details_id = dispatch_plan_details.so_details_id), 0) 
//         // - IFNULL((SELECT SUM(location_stock.stock_qty) FROM location_stock 
//         // WHERE sales_order_details.item_id = location_stock.item_id AND location_stock.location_id = $location->id), 0)
//         // - (IFNULL((SELECT SUM(material_request_details.mr_qty) FROM material_request_details 
//         // LEFT JOIN material_request ON material_request.mr_id = material_request_details.mr_id
//         // WHERE material_request_details.item_id = sales_order_details.item_id 
//         // AND material_request.current_location_id = $location->id
//         // GROUP BY material_request_details.item_id), 0) 
//         // - IFNULL((SELECT SUM(material_receipt_grn_details.grn_qty) FROM material_receipt_grn_details 
//         // LEFT JOIN grn_material_receipt ON grn_material_receipt.grn_id = material_receipt_grn_details.grn_id
//         // WHERE material_receipt_grn_details.item_id = sales_order_details.item_id 
//         // AND grn_material_receipt.current_location_id = $location->id
//         // AND grn_material_receipt.grn_type_id_fix = 3
//         // GROUP BY material_receipt_grn_details.item_id), 0))) as need_qty"),

        
//         ])
//         ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
//         ->leftJoin('items', 'items.id', '=', 'sales_order_details.item_id')
//         ->leftJoin('units', 'units.id', '=', 'items.unit_id')
//         ->whereIn('sales_order_details.so_details_id', $sodData)
//         ->where('sales_order.current_location_id', $location->id)
//         ->whereIn('sales_order.year_id',$yearIds)
//         ->groupBy('sales_order_details.item_id', 'items.item_name', 'items.item_code', 'units.unit_name')
//         ->get();
// // dd($SalesOrderDetail);
//         $filteredSalesOrderDetails = $SalesOrderDetail->filter(function ($item) {
//             $needQty = $item->pending_so_qty - $item->stock_qty - ($item->mr_qty - $item->grn_qty);
//             return $needQty > 0;
//         });
    
//         return DataTables::of($filteredSalesOrderDetails)
//         ->editColumn('pending_so_qty', function($SalesOrderDetail){             
//             return $SalesOrderDetail->pending_so_qty > 0 ? number_format((float)$SalesOrderDetail->pending_so_qty, 3, '.','') : number_format((float) 0, 3,'.','');
//         })
//         ->editColumn('stock_qty', function($SalesOrderDetail){             
//             return $SalesOrderDetail->stock_qty > 0 ? number_format((float)$SalesOrderDetail->stock_qty, 3, '.','') : number_format((float) 0, 3,'.','');
//         })
//         ->editColumn('pend_mat_rec_qty', function($SalesOrderDetail){ 
//             $pend_mat_rec_qty = ($SalesOrderDetail->mr_qty - $SalesOrderDetail->grn_qty  - $SalesOrderDetail->sc_qty);          
//             return $pend_mat_rec_qty > 0 ? number_format((float)$pend_mat_rec_qty, 3, '.','') : number_format((float) 0, 3,'.','');
//         })
//         ->editColumn('need_qty', function($SalesOrderDetail){
//             $pend_mat_rec_qty = ($SalesOrderDetail->mr_qty - $SalesOrderDetail->grn_qty  - $SalesOrderDetail->sc_qty) > 0 ? ($SalesOrderDetail->mr_qty - $SalesOrderDetail->grn_qty  - $SalesOrderDetail->sc_qty) : 0;

//             $needQty = ($SalesOrderDetail->pending_so_qty - $SalesOrderDetail->stock_qty - $pend_mat_rec_qty); 

//             // $needQty = ($SalesOrderDetail->pending_so_qty - $SalesOrderDetail->stock_qty - ($SalesOrderDetail->mr_qty - $SalesOrderDetail->grn_qty) ); 
//             return $needQty > 0 ? number_format((float)$needQty, 3, '.','') : number_format((float) 0, 3,'.','');
//         })
//         ->make(true);
//     }


    
    public function index(SalesOrderDetail $SalesOrderDetail,Request $request,DataTables $dataTables)
    {       
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();
        $yearIds = getCompanyYearIdsToTill();


        $sodData = SalesOrderDetail::select(['sales_order_details.so_details_id'])
        ->leftJoin('sales_order', 'sales_order.id','=','sales_order_details.so_id')    
        ->where('sales_order.current_location_id', $location->id)   
        ->where('sales_order_details.fitting_item','=','no');    

        if($request->sales_order_id !=''){
            $sodData->where('sales_order_details.so_id','=',$request->sales_order_id);
        }

        $sodData  = $sodData->get();


        $filteredSalesOrderDetails = SalesOrderDetail::select(['items.item_name','items.item_code','units.unit_name',
        'sales_order_details.item_id',
        DB::raw('SUM(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE sales_order_details.so_details_id = dispatch_plan_details.so_details_id), 0) ) as pending_so_qty'),
        DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE sales_order_details.item_id = location_stock.item_id  AND location_stock.location_id = $location->id) as stock_qty"),
        DB::raw("(SELECT IFNULL(SUM(material_request_details.mr_qty), 0) FROM material_request_details 
        LEFT JOIN material_request ON material_request.mr_id = material_request_details.mr_id
        WHERE material_request_details.item_id = sales_order_details.item_id 
        AND material_request.current_location_id = $location->id
        GROUP BY material_request_details.item_id) as mr_qty"),
        DB::raw("(SELECT IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) FROM material_receipt_grn_details 
        LEFT JOIN grn_material_receipt ON grn_material_receipt.grn_id = material_receipt_grn_details.grn_id
        WHERE material_receipt_grn_details.item_id = sales_order_details.item_id 
        AND grn_material_receipt.current_location_id = $location->id
        AND grn_material_receipt.grn_type_id_fix = 3
        GROUP BY material_receipt_grn_details.item_id) as grn_qty"),
        DB::raw('(SELECT IFNULL(SUM(so_short_close.sc_qty), 0) FROM so_short_close 
        LEFT JOIN sales_order_details ON sales_order_details.so_details_id = so_short_close.so_details_id
        WHERE sales_order_details.mr_details_id IS NOT NULL) as sc_qty')     
        
        ])
        ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
        ->leftJoin('items', 'items.id', '=', 'sales_order_details.item_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')
        ->whereIn('sales_order_details.so_details_id', $sodData)
        ->where('sales_order.current_location_id', $location->id)
        ->whereIn('sales_order.year_id',$yearIds)
        ->groupBy('sales_order_details.item_id', 'items.item_name', 'items.item_code', 'units.unit_name')
        ->havingRaw('(SUM(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE sales_order_details.so_details_id = dispatch_plan_details.so_details_id), 0)) 
              - (SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE sales_order_details.item_id = location_stock.item_id  AND location_stock.location_id = ?) 
              - (SELECT IFNULL(SUM(material_request_details.mr_qty), 0) FROM material_request_details 
                LEFT JOIN material_request ON material_request.mr_id = material_request_details.mr_id
                WHERE material_request_details.item_id = sales_order_details.item_id 
                AND material_request.current_location_id = ?) 
              + (SELECT IFNULL(SUM(material_receipt_grn_details.grn_qty), 0) FROM material_receipt_grn_details 
                LEFT JOIN grn_material_receipt ON grn_material_receipt.grn_id = material_receipt_grn_details.grn_id
                WHERE material_receipt_grn_details.item_id = sales_order_details.item_id 
                AND grn_material_receipt.current_location_id = ?
                AND grn_material_receipt.grn_type_id_fix = 3)
            ) > 0', [$location->id, $location->id, $location->id]);
        // ->get();
// dd($SalesOrderDetail);
        // $filteredSalesOrderDetails = $SalesOrderDetail->filter(function ($item) {
        //     $needQty = $item->pending_so_qty - $item->stock_qty - ($item->mr_qty - $item->grn_qty);
        //     return $needQty > 0;
        // });
    
        return DataTables::of($filteredSalesOrderDetails)
        ->addColumn('pending_so_qty', function($SalesOrderDetail){             
            return $SalesOrderDetail->pending_so_qty > 0 ? number_format((float)$SalesOrderDetail->pending_so_qty, 3, '.','') : number_format((float) 0, 3,'.','');
        })
        ->addColumn('stock_qty', function($SalesOrderDetail){             
            return $SalesOrderDetail->stock_qty > 0 ? number_format((float)$SalesOrderDetail->stock_qty, 3, '.','') : number_format((float) 0, 3,'.','');
        })
        ->addColumn('pend_mat_rec_qty', function($SalesOrderDetail){ 
            $pend_mat_rec_qty = ($SalesOrderDetail->mr_qty - $SalesOrderDetail->grn_qty  - $SalesOrderDetail->sc_qty);          
            return $pend_mat_rec_qty > 0 ? number_format((float)$pend_mat_rec_qty, 3, '.','') : number_format((float) 0, 3,'.','');
        })
        ->addColumn('need_qty', function($SalesOrderDetail){
            $pend_mat_rec_qty = ($SalesOrderDetail->mr_qty - $SalesOrderDetail->grn_qty  - $SalesOrderDetail->sc_qty) > 0 ? ($SalesOrderDetail->mr_qty - $SalesOrderDetail->grn_qty  - $SalesOrderDetail->sc_qty) : 0;

            $needQty = ($SalesOrderDetail->pending_so_qty - $SalesOrderDetail->stock_qty - $pend_mat_rec_qty); 

            // $needQty = ($SalesOrderDetail->pending_so_qty - $SalesOrderDetail->stock_qty - ($SalesOrderDetail->mr_qty - $SalesOrderDetail->grn_qty) ); 
            return $needQty > 0 ? number_format((float)$needQty, 3, '.','') : number_format((float) 0, 3,'.','');
        })
   

        ->rawColumns(['pending_so_qty','stock_qty','pend_mat_rec_qty','need_qty'])
        ->make(true);
    }


}