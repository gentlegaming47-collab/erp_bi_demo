<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocationDetailStock;
use DataTables;

class ItemDetailsReportController extends Controller
{
    public function manage()
    {
        return view('manage.manage-item_details_report');
    }

    public function index(LocationDetailStock $location_stock_details,Request $request,DataTables $dataTables)
    { 
        $Location = getCurrentLocation();

        $location_stock_details = LocationDetailStock::select(['location_stock_details.lsd_id','location_stock_details.stock_qty','locations.location_name','items.item_name','items.item_code','items.min_stock_qty','items.max_stock_qty','items.re_order_qty','item_groups.item_group_name','units.unit_name','item_details.secondary_item_name','items.second_unit','seond_units.unit_name as second_unit','location_stock_details.secondary_stock_qty','item_details.secondary_qty'])
        ->leftJoin('locations','locations.id','=','location_stock_details.location_id')
        ->leftJoin('item_details','item_details.item_details_id','=','location_stock_details.item_details_id')
        ->leftJoin('items','items.id','=','item_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('units as seond_units','seond_units.id','=','items.second_unit')
       ->where('location_stock_details.location_id','=',$Location->id);
        return DataTables::of($location_stock_details)
        ->editColumn('item_name', function($location_stock_details){ 
            if($location_stock_details->item_name != ''){
                $item_name = ucfirst($location_stock_details->item_name);
                return $item_name;
            }else{
                return '';
            }
        })
          ->editColumn('stock_qty', function($location_stock_details){
            if($location_stock_details->stock_qty != null || $location_stock_details->stock_qty){
                $stockQty = number_format((float)$location_stock_details->stock_qty, 3, '.','');
                
                return isset($stockQty)?$stockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })
          ->editColumn('secondary_stock_qty', function($location_stock_details){
            if($location_stock_details->secondary_stock_qty != null || $location_stock_details->secondary_stock_qty){
                $secondstockQty = number_format((float)$location_stock_details->secondary_stock_qty, 3, '.','');
                
                return isset($secondstockQty)?$secondstockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })

//           ->editColumn('second_stock_qty', function($location_stock_details){
//             if($location_stock_details->stock_qty != null || $location_stock_details->stock_qty){

//                 if($location_stock_details->secondary_qty != null){
//                     $StockSecondQty = $location_stock_details->stock_qty /  $location_stock_details->secondary_qty;
//                      $secondstockQty = number_format((float)$StockSecondQty, 3, '.','');
//                 }else{
//  $secondstockQty = '';
//                 }
               
                
        //         return isset($secondstockQty)?$secondstockQty :'';
        //     }else{
        //         return number_format((float)0, 3, '.','');
        //     }
        // })
        ->editColumn('min_stock_qty', function($location_stock_details){
            if($location_stock_details->min_stock_qty != null || $location_stock_details->min_stock_qty){
                $minStockQty = number_format((float)$location_stock_details->min_stock_qty, 3, '.','');
                
                return isset($minStockQty)?$minStockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })
        ->editColumn('max_stock_qty', function($location_stock_details){
            if($location_stock_details->max_stock_qty != null || $location_stock_details->max_stock_qty){
                $maxStockQty = number_format((float)$location_stock_details->max_stock_qty, 3, '.','');
                
                return isset($maxStockQty)?$maxStockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })
        ->editColumn('re_order_qty', function($location_stock_details){
            if($location_stock_details->re_order_qty != null || $location_stock_details->re_order_qty){
                $reOrderStockQty = number_format((float)$location_stock_details->re_order_qty, 3, '.','');
                
                return isset($reOrderStockQty)?$reOrderStockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })

        ->make(true);
    }
}