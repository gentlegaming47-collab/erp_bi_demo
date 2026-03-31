<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\ItemReturn;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\CompanyYearController;
use App\Http\Controllers\ItemRawMaterialMappingController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SupplierInwardGrnController;
use App\Models\JobworkOutwardChallanDetail;
use App\Models\JobworkInwardChallanDetail;
use App\Models\QcCheck;
use App\Models\MoveToRfd;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\Company;
use App\Models\CompanyUnit;
use App\Models\Material;
use App\Models\ModeOfDispatch;
use App\Models\PatternCoreMaterial;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Taluka;
use App\Models\HsnCode;
use App\Models\RejectionReason;
use App\Models\Process;
use App\Models\PurchaseOrder;
use App\Models\Transporter;
use App\Models\LocationStock;
use App\Models\UserLocation;
use App\Models\LogActivity;
use App\Models\StockLog;
use App\Models\LocationDetailStock;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetails;
use App\Models\DispatchPlanDetails;
use App\Models\LoadingEntryDetails;
use App\Models\GRNVerification;
use App\Models\QCApproval;

// use Session;
use App\Models\PackingUnit;

use App\Models\Village;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Item;
use App\Models\ItemGroup;
use App\Models\Unit;

use App\Models\ItemRawMaterialMappingDetail;
use App\Models\Menus;
use App\Models\Module;

use App\Models\Location;
use App\Http\Controllers\LocationController;
use App\Models\CustomerReplacementEntryDetails;
use App\Models\Dealer;
// Main Transcation Model
use App\Models\SalesOrder;
use App\Models\GRNMaterial;
use App\Models\MaterialRequest;
use App\Models\SupplierRejection;
use App\Models\ItemIssue;
use App\Models\ItemProduction;
use App\Models\ItemAssemblyProduction;
use App\Models\RawMaterialGroup;
use App\Models\ItemDetails;



// Details Model
use App\Models\SalesOrderDetail;
use App\Models\SalesOrderDetailsDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\MaterialRequestDetail;
use App\Models\GRNMaterialDetails;
use App\Models\SupplierRejectoionDetails;
use App\Models\ItemIssueDetail;
use App\Models\ItemReturnDetail;
use App\Models\ItemProductionDetail;
use App\Models\ItemAssemblyProductionDetails;
use App\Models\LoadingEntrySecondaryDetails;
use App\Models\DispatchPlan;
use App\Models\DispatchPlanSecondaryDetails;
use App\Models\GRNMaterailSecondaryDetails;
use App\Models\ItemStockTransfer;
use App\Models\ItemStockTransferDetails;
use App\Models\LocationCustomerGroupMapping;
use App\Models\MisCategory;
use App\Models\ReplacementItemDecisionDetails;
use App\Models\SOMapping;
use App\Models\SOMappingDetails;
use Carbon\Carbon;






/**
 *
 * Date Time Format
 */
define('DATE_TIME_FORMAT','d-m-Y | H:i:s');

/**
 * Date Format
*/
define('DATE_FORMAT','d/m/Y');

/**
 * Date Time Format Sql Raw
 */
define('DATE_TIME_FORMAT_RAW','%d-%m-%Y | %H:%i:%s');

/**
 * Date Format Sql Raw
 */
define('DATE_FORMAT_RAW','%d/%m/%Y');

/**
 * JS Version
*/
function getJsVersion(){
    return "0.17.7";
}

/**
 * CSS Version
*/
function getCssVersion(){
    return "0.17.7";
}

/**
 * Get Master Data
 */
// function getCities(){
//     return City::orderBy('city_name', 'ASC')->get();
// }

function getCities(){
    return City::orderBy('district_name', 'ASC')->get();
}
function getTalukas(){
    return Taluka::orderBy('taluka_name', 'ASC')->get();
}
function getVillages(){
    return Village::orderBy('village_name', 'ASC')->get();
}
function getItemGroupData(){
    return ItemGroup::orderBy('item_group_name', 'ASC')->get();
}
// function getItem()
// {
//        return Item::orderBy('item_name', 'ASC')->get();

// }

function getItem($changedItemIds = [])
{
    // $item = Item::orderBy('item_name', 'ASC');
    $item = Item::select(['items.id','items.item_name','items.item_code','units.unit_name', ])
    ->Join('units','units.id','=','items.unit_id')
    ->orderBy('items.item_name', 'ASC');

    // If editing, show both active and previously selected inactive items
    if (!empty($changedItemIds)) {
        $item->where(function ($q) use ($changedItemIds) {
            $q->where('items.status', 'active')
              ->where('items.service_item','No')
              ->orWhereIn('items.id', $changedItemIds);
        });
    } else {

        // For Add Mode, show only active items
        $item->where('items.status', 'active')->where('items.service_item','No');
    }


    return $item->get();
}


// function getPRItem($changedItemIds){
//     // dd($changedItemIds);
//     $locationCode = getCurrentLocation();

//     // this use to if min_stock_qty > stock_qty then don't show in pr form

//     // $item = Item::leftJoin('location_stock', function ($join) use ($locationCode) {
//     $item = Item::select('items.id','items.item_name','items.item_code','units.unit_name')
//     ->leftJoin('units','units.id','items.unit_id')
//     ->leftJoin('location_stock', function ($join) use ($locationCode) {
//     $join->on('location_stock.item_id', '=', 'items.id')
//          ->where('location_stock.location_id', '=', $locationCode->id);
//     })
//     ->where(function($query) use ($changedItemIds) {
//         $query->where('items.status', '=', 'active')
//             ->orWhereIn('items.id', $changedItemIds);
//     })
//     ->where(function($query) use ($changedItemIds) {
//         $query->where(function($q){
//             $q->where('items.dont_allow_req_msl', '=', 'Yes')
//             ->where(function($subQuery){
//                     $subQuery->whereRaw('items.min_stock_qty > IFNULL(location_stock.stock_qty, 0)')
//                             ->orWhereNull('items.min_stock_qty');
//             });
//         })
//         ->orWhere(function($q){
//             $q->where('items.dont_allow_req_msl', '=', 'No')
//             ->orWhereNull('items.dont_allow_req_msl');
//         })
//         ->orWhereIn('items.id', $changedItemIds);
//     })
//     ->orderBy('items.item_name', 'ASC')
//     ->get();

//     return $item;
// }


function getPRItem($changedItemIds){
    // dd($changedItemIds);
    $locationCode = getCurrentLocation();

    // this use to if min_stock_qty > stock_qty then don't show in pr form

    // $item = Item::leftJoin('location_stock', function ($join) use ($locationCode) {
    $item = Item::select('items.id','items.item_name','items.item_code','units.unit_name','items.secondary_unit')
    ->leftJoin('units','units.id','items.unit_id')
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
    ->orderBy('items.item_name', 'ASC')
    ->get();

    return $item;
}

function getTransporter(){
    return Transporter::select('id','transporter_name') ->where('status', '=', 'active')->orderBy('transporter_name', 'asc')->get();
}


// function getFittingItem()
// {
//       return Item::where('fitting_item', 'no')->orderBy('item_name', 'ASC')->get();
// }

function getFittingItem($changedItemIds)
{
    // $item = Item::where('fitting_item', 'no')->orderBy('item_name', 'ASC');

    $location = getCurrentLocation();

    $item = Item::select(['items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name','items.secondary_unit',
    DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE items.id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),
    ])
    ->Join('item_groups','item_groups.id','=','items.item_group_id')
    ->Join('units','units.id','=','items.unit_id')
    ->where('items.fitting_item', 'no')->orderBy('items.item_name', 'ASC');

    if (!empty($changedItemIds)) {
        $item->where(function ($q) use ($changedItemIds) {
            $q->where('items.status', 'active')
              ->where('items.service_item','No')
              ->orWhereIn('items.id', $changedItemIds);
        });
    } else {
        $item->where('items.status', 'active')->where('items.service_item','No');
    }


    return $item->get();

}

// function getItemsForSupplierMapping(){
//     $data =  Item::select('items.id','items.item_name','items.item_code','units.unit_name','item_groups.item_group_name')
//     ->leftJoin('item_groups', 'item_groups.id', '=', 'items.item_group_id')
//     ->leftJoin('units', 'units.id', '=', 'items.unit_id')
//     // ->orderBy('items.id', 'ASC')->get();
//     ->where('fitting_item', 'no')->orderBy('items.id', 'ASC')->get();
//     return $data;
// }

function getItemsForSupplierMapping(){
    $data =  Item::select('items.id','items.item_name','items.item_code','units.unit_name','item_groups.item_group_name')
    ->leftJoin('item_groups', 'item_groups.id', '=', 'items.item_group_id')
    ->leftJoin('units', 'units.id', '=', 'items.unit_id')
    // ->orderBy('items.id', 'ASC')->get();
    ->where('fitting_item', 'no')
    ->where('status', 'active')
    ->orderBy('items.id', 'ASC')->get();
    return $data;
}

function getItemsForIST($changedItemIds){
    $data =  Item::select('items.id','items.item_name','items.item_code','units.unit_name','item_groups.item_group_name')
    ->leftJoin('item_groups', 'item_groups.id', '=', 'items.item_group_id')
    ->leftJoin('units', 'units.id', '=', 'items.unit_id')
    ->where('secondary_unit', 'Yes')->orderBy('items.id', 'ASC');

    if (!empty($changedItemIds)) {
        $data->where(function ($q) use ($changedItemIds) {
               $q->where('items.status', 'active')
              ->orWhereIn('items.id', $changedItemIds);
        });
    }else{
        $data->where('items.status', 'active');
    }

    return $data->get();
}

// function getSalesFittingItem()
// {
//       return Item::where(['fitting_item' => 'no', 'require_raw_material_mapping' => 'no'])->orderBy('item_name', 'ASC')->get();
// }


function getSalesFittingItem($changedItemIds)
{

    // // Log the function call count
    // Log::info("getSalesFittingItem function called  times.");


    //   $item = Item::where(['fitting_item' => 'no', 'require_raw_material_mapping' => 'no'])->orderBy('item_name', 'ASC');

      $item = Item::select(['items.id','items.item_name','items.item_code','units.unit_name', ])
      ->Join('units','units.id','=','items.unit_id')
      ->where(['fitting_item' => 'no', 'require_raw_material_mapping' => 'no'])->orderBy('item_name', 'ASC');

      // If editing, show both active and previously selected inactive items
      if (!empty($changedItemIds)) {
          $item->where(function ($q) use ($changedItemIds) {
              $q->where('items.status', 'active')
                ->where('items.service_item','No')
                ->orWhereIn('items.id', $changedItemIds);
          });
      } else {


          $item->where('items.status', 'active')->where('items.service_item','No');
      }


      return $item->get();
}

function getFittingAnyItem()
{
      return Item::orderBy('item_name', 'ASC')->get();
}


// function getFittingMappingItem()
// {
//       //return Item::where('fitting_item', 'no')->where('require_raw_material_mapping', 'no')->orderBy('item_name', 'ASC')->get();
//       return Item::where('fitting_item', 'no')->where('require_raw_material_mapping', 'no')->where('own_manufacturing', 'Yes')->orderBy('item_name', 'ASC')->get();
// }
function getFittingMappingItem($changedItemIds)
{
    //   $item = Item::where('fitting_item', 'no')->where('require_raw_material_mapping', 'no')->where('own_manufacturing', 'Yes')->orderBy('item_name', 'ASC');

    $location = getCurrentLocation();

    $item = Item::select('items.id','items.item_name','items.item_code','units.unit_name','item_groups.item_group_name',
    DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE items.id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),
    )
    ->leftJoin('item_groups', 'item_groups.id', '=', 'items.item_group_id')
    ->leftJoin('units', 'units.id', '=', 'items.unit_id')
    ->where('items.fitting_item', 'no')->where('items.require_raw_material_mapping', 'no')->where('items.own_manufacturing', 'Yes')->orderBy('items.item_name', 'ASC');

      // If editing, show both active and previously selected inactive items
      if (!empty($changedItemIds)) {
          $item->where(function ($q) use ($changedItemIds) {
              $q->where('items.status', 'active')
                ->where('items.service_item','No')
                ->orWhereIn('items.id', $changedItemIds);
          });
      } else {


          $item->where('items.status', 'active')->where('items.service_item','No');
      }


      return $item->get();
}
// function getFittingMappingItems()
// {
//       return Item::where('fitting_item', 'no')->where('require_raw_material_mapping', 'yes')->orderBy('item_name', 'ASC')->get();
// }

function getFittingMappingItems($changedItemIds)
{
    //   $item = Item::where('fitting_item', 'no')->where('require_raw_material_mapping', 'yes')->orderBy('item_name', 'ASC');

    //   // If editing, show both active and previously selected inactive items
    //   if (!empty($changedItemIds)) {
    //       $item->where(function ($q) use ($changedItemIds) {
    //           $q->where('status', 'active')
    //             ->where('service_item','No')
    //             ->orWhereIn('id', $changedItemIds);
    //       });
    //   } else {

    //       $item->where('status', 'active')->where('service_item','No');
    //   }


    //   return $item->get();


    if (!empty($changedItemIds)) {
    $item = Item::where('fitting_item', 'no')->where('require_raw_material_mapping', 'yes')->orderBy('item_name', 'ASC')->where('status', 'active')
            ->where('service_item','No')
            ->orWhereIn('id', $changedItemIds)->get();
    } else {

    $item = Item::where('fitting_item', 'no')->where('require_raw_material_mapping', 'yes')->orderBy('item_name', 'ASC')->where('status', 'active')->where('service_item','No')->get();

    }

    return $item;
}
function getFittingMappingItemsForProduction($changedItemIds)
{
    $location = getCurrentLocation();

    $query = Item::select(
        'items.id',
        'items.item_name',
        'items.item_code',
        'units.unit_name',
        'item_groups.item_group_name',
        DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty), 0) FROM location_stock WHERE items.id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty")
    )
    ->leftJoin('item_groups', 'item_groups.id', '=', 'items.item_group_id')
    ->leftJoin('units', 'units.id', '=', 'items.unit_id')
    ->where('fitting_item', 'no')
    ->where('require_raw_material_mapping', 'No')
    ->where('status', 'active')
    ->where('own_manufacturing', 'Yes')
    ->where('service_item', 'No')
    ->where('secondary_unit', 'No');

    if (!empty($changedItemIds)) {
        $query->orWhereIn('items.id', $changedItemIds);
    }

    $query->orderBy('item_name', 'ASC');

    $item = $query->get();

    return $item;
}

// function noFittingItem()
// {
//     return Item::where('fitting_item', 'no')->orderBy('item_name', 'ASC')->get();
// }
function noFittingItem($changedItemIds)
{
    // $item = Item::where('fitting_item', 'no')->orderBy('item_name', 'ASC');

    $location = getCurrentLocation();


    $item = Item::select(['items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name','items.secondary_unit',
    DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE items.id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),
    ])
    ->Join('item_groups','item_groups.id','=','items.item_group_id')
    ->Join('units','units.id','=','items.unit_id')
    ->where('items.fitting_item', 'no')
    ->orderBy('items.item_name', 'ASC');

    // If editing, show both active and previously selected inactive items
    if (!empty($changedItemIds)) {
        $item->where(function ($q) use ($changedItemIds) {
            $q->where('items.status', 'active')
              ->where('items.service_item','No')
              ->orWhereIn('items.id', $changedItemIds);
        });
    } else {


        $item->where('items.status', 'active')->where('items.service_item','No');
    }


    return $item->get();
}

// function getItemMapping()
// {
//     $request = new Request();
//     return  ItemRawMaterialMappingDetail::select('item_raw_material_mapping_details.*')->where('item_id', $request->item_id)->get();
// }






// if req are yes then show the code

// function getParticularItem()
// {
//     // return  Item::where('require_raw_material_mapping', 'yes')->select('items.*')->orderBy('item_name', 'ASC')->get();
//     return  Item::where('require_raw_material_mapping', 'Yes')->where('fitting_item','No')->select('items.*')->orderBy('item_name', 'ASC')->get();
// }
function getParticularItem($changedItemIds)
{
    // If editing, show both active and previously selected inactive items
    if (!empty($changedItemIds)) {
        $item =  Item::where('require_raw_material_mapping', 'Yes')->where('fitting_item','No')->select('items.*')->orderBy('item_name', 'ASC')->where('status', 'active')
        ->orWhereIn('id', $changedItemIds)->get();

    } else {
    $item =  Item::where('require_raw_material_mapping', 'Yes')->where('fitting_item','No')->select('items.*')->orderBy('item_name', 'ASC')->where('status', 'active')->get();
    }
    return $item;
}


function check_convert_date($date)
{
    $return = '';
    if(isset($date) && !empty($date))
    {
       $return = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
    }else{
        $return = null;
    }
    return $return;
}

function parseCarbonDate($date)
{
    if(isset($date) && !empty($date))
        return Carbon::parse($date)->format('d/m/Y');
    else
        return null;
}




function getUnit(){
    return Unit::orderBy('unit_name', 'ASC')->get();
}
function getRawMaterialGroupData()
{
    return RawMaterialGroup::orderBy('raw_material_group_nm', 'ASC')->get();
}

function getVillage()
{
    return Village::orderBy('village_name', 'ASC')->get();
}
function getCountries(){
    return Country::select('id','country_name')->orderBy('country_name','asc')->get();
}
function getStates(){
    return State::select('id','state_name')->orderBy('state_name','asc')->get();
}
function getMaterials(){
    return Material::select('id','material_name')->orderBy('material_name','asc')->get();
}
function getHsnCodes(){
    return HsnCode::select('id','hsn_code')->orderBy('hsn_code','asc')->get();
}
function getCustomers(){
    return Customer::select('id','customer_name')->orderBy('customer_name','asc')->get();
}


// function getAllSalesOrder()
// {
//     $locationId = getCurrentLocation()->id;
//     return SalesOrderDetail::select('sales_order_details.*', 'sales_order.so_number','items.item_name', 'items.item_code', 'units.unit_name', 'stock_qty')
//     ->leftJoin('sales_order', 'sales_order.id', 'sales_order_details.so_id')
//     ->leftJoin('items', 'items.id', 'sales_order_details.item_id')
//     ->leftJoin('location_stock', 'location_stock.item_id', 'sales_order_details.item_id')
//     ->leftJoin('units', 'units.id', 'items.unit_id')
//     ->where('sales_order.current_location_id', $locationId)
//     ->groupby('sales_order.so_number')
//     ->get();
// }


// function getAllSalesOrder()
// {
//     $locationId = getCurrentLocation()->id;
//     $yearIds = getCompanyYearIdsToTill();


//     // return SalesOrderDetail::select('sales_order.so_number','sales_order_details.so_id',
//     // DB::raw("SUM(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE sales_order_details.so_details_id = dispatch_plan_details.so_details_id), 0)
//     // - IFNULL((SELECT SUM(location_stock.stock_qty) FROM location_stock
//     // WHERE sales_order_details.item_id = location_stock.item_id AND location_stock.location_id = $locationId), 0)
//     // - (IFNULL((SELECT SUM(material_request_details.mr_qty) FROM material_request_details
//     // LEFT JOIN material_request ON material_request.mr_id = material_request_details.mr_id
//     // WHERE material_request_details.item_id = sales_order_details.item_id
//     // AND material_request.current_location_id = $locationId
//     // GROUP BY material_request_details.item_id), 0)
//     // - IFNULL((SELECT SUM(material_receipt_grn_details.grn_qty) FROM material_receipt_grn_details
//     // LEFT JOIN grn_material_receipt ON grn_material_receipt.grn_id = material_receipt_grn_details.grn_id
//     // WHERE material_receipt_grn_details.item_id = sales_order_details.item_id
//     // AND grn_material_receipt.current_location_id = $locationId
//     // AND grn_material_receipt.grn_type_id_fix = 3
//     // GROUP BY material_receipt_grn_details.item_id), 0))) as need_qty")
//     // )
//     // ->leftJoin('sales_order', 'sales_order.id', 'sales_order_details.so_id')
//     // ->leftJoin('items', 'items.id', 'sales_order_details.item_id')
//     // ->leftJoin('location_stock', 'location_stock.item_id', 'sales_order_details.item_id')
//     // ->leftJoin('units', 'units.id', 'items.unit_id')
//     // ->where('sales_order.current_location_id', $locationId)
//     // ->where('sales_order_details.fitting_item','=','no')
//     // ->groupby('sales_order.so_number')
//     // ->having('need_qty', '>', 0)
//     // ->get();


//     // $locationId = getCurrentLocation()->id;

//     $sodData = SalesOrderDetail::select(['sales_order_details.so_details_id'])
//     ->leftJoin('sales_order', 'sales_order.id','=','sales_order_details.so_id')
//     ->where('sales_order.current_location_id', $locationId)
//     ->where('sales_order_details.fitting_item','=','no');

//     $salesOrders = SalesOrderDetail::select(['sales_order.so_number','sales_order_details.so_id',
//     DB::raw('(sales_order_details.so_qty - IFNULL((SELECT SUM(dispatch_plan_details.plan_qty) FROM dispatch_plan_details WHERE sales_order_details.so_details_id = dispatch_plan_details.so_details_id), 0) ) as pending_so_qty'),
//     DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE sales_order_details.item_id = location_stock.item_id  AND location_stock.location_id = $locationId) as stock_qty"),
//     DB::raw("(SELECT IFNULL(SUM(material_request_details.mr_qty), 0) FROM material_request_details
//     LEFT JOIN material_request ON material_request.mr_id = material_request_details.mr_id
//     WHERE material_request_details.item_id = sales_order_details.item_id
//     AND material_request.current_location_id = $locationId
//     GROUP BY material_request_details.item_id) as mr_qty"),
//     DB::raw("COALESCE((SELECT SUM(material_receipt_grn_details.grn_qty) FROM material_receipt_grn_details
//     LEFT JOIN grn_material_receipt ON grn_material_receipt.grn_id = material_receipt_grn_details.grn_id
//     WHERE material_receipt_grn_details.item_id = sales_order_details.item_id
//     AND grn_material_receipt.current_location_id = $locationId
//     AND grn_material_receipt.grn_type_id_fix = 3), 0) as grn_qty"),
//     DB::raw('(SELECT IFNULL(SUM(so_short_close.sc_qty), 0) FROM so_short_close
//     LEFT JOIN sales_order_details ON sales_order_details.so_details_id = so_short_close.so_details_id
//     WHERE sales_order_details.mr_details_id IS NOT NULL) as sc_qty')

//     ])
//     ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
//     ->leftJoin('items', 'items.id', '=', 'sales_order_details.item_id')
//     ->leftJoin('units', 'units.id', '=', 'items.unit_id')
//     ->whereIn('sales_order_details.so_details_id', $sodData)
//     ->where('sales_order.current_location_id', $locationId)
//     ->whereIn('sales_order.year_id',$yearIds)
//     ->groupBy('sales_order.so_number')
//     ->get();


//     $filteredSalesOrders = $salesOrders->filter(function ($order) {
//         $grn_qty = $order->grn_qty != null ? $order->grn_qty : 0;

//         $pend_mat_rec_qty = ($order->mr_qty -  $grn_qty  - $order->sc_qty) > 0 ? ($order->mr_qty - $grn_qty  - $order->sc_qty) : 0;

//         $needQty = $order->pending_so_qty - $order->stock_qty - $pend_mat_rec_qty;
//         // $needQty = $order->pending_so_qty - $order->stock_qty - ($order->mr_qty - $grn_qty);
//         return $needQty > 0;
//     });

//     return $filteredSalesOrders;
// }

function getAllSalesOrder(){ 

    $location = getCurrentLocation();
    $yearIds = getCompanyYearIdsToTill();


    $sodData = SalesOrderDetail::select(['sales_order_details.so_details_id'])
    ->leftJoin('sales_order', 'sales_order.id','=','sales_order_details.so_id')    
    ->where('sales_order.current_location_id', $location->id)   
    ->where('sales_order_details.fitting_item','=','no');

    $filteredSalesOrderDetails = SalesOrderDetail::select(['sales_order.so_number','sales_order.id',
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
    ->groupBy('sales_order.so_number','sales_order_details.so_id',
    'sales_order_details.item_id',)
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
        ) > 0', [$location->id, $location->id, $location->id])->get();

    $filteredSalesOrderDetails = $filteredSalesOrderDetails->unique(['id']);


    return $filteredSalesOrderDetails;
}

// function getCompanies(){
//     return CompanyUnit::select('id','company_unit_name')->orderBy('company_unit_name','asc')->get();
// }





function getReasons(){
    return RejectionReason::select('id','rejection_reason')->orderBy('rejection_reason','asc')->get();
}
function getPatternCoreMaterials(){
    return PatternCoreMaterial::select('id','pattern_core_material')->orderBy('pattern_core_material','asc')->get();
}
// function getSuppliers(){
//     return Supplier::select('id','supplier_name')->orderBy('supplier_name','asc')->get();
// }
function getSuppliers($supplierIds){
    $supplier =  Supplier::select('id','supplier_name')->orderBy('supplier_name','asc');

    // If editing, show both active and previously selected inactive items
    if (!empty($supplierIds)) {
        $supplier->where(function ($q) use ($supplierIds) {
            $q->where('status', 'active')
              ->orWhereIn('id', $supplierIds);
        });
    } else {
        $supplier->where('status', 'active');
    }

    return $supplier->get();
}
function getModeOfDispatches(){
    return ModeOfDispatch::select('id','mode_of_dispatch')->orderBy('mode_of_dispatch','asc')->get();
}
function getCurrencies(){
    return Currency::select('id','currency','symbol')->orderBy('currency','asc')->get();
}
function getProducts(){
    return Product::select(['id','part_no','revision_no','status_fix_id'])->orderBy('part_no','asc')->get();
}
function getProcess(){
    return Process::select('id','process')->orderBy('process','asc')->get();
}
function getLocation(){
    return Location::select('id','location_name')->orderBy('location_name','asc')->get();
}
function getDealer(){
    return Dealer::select('id','dealer_name')->where('status', '=', 'active')->orderBy('dealer_name','asc')->get();
}
function getMisCategory(){
    return MisCategory::select('id','mis_category')->orderBy('mis_category','asc')->get();
}

// function getSoDealer(){
//     $locationId = getCurrentLocation()->id;


//     $location_data = Location::select(['locations.id', 'locations.location_code','locations.location_name','locations.village_id','locations.type','locations.mfg_process','locations.header_print','locations.status','states.country_id','districts.state_id','talukas.district_id','villages.taluka_id','locations.created_on','locations.created_by_user_id','locations.last_by_user_id','locations.last_on'])
//     ->leftJoin('villages','villages.id','=','locations.village_id')
//     ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
//     ->leftJoin('districts','districts.id','=','talukas.district_id')
//     ->leftJoin('states','states.id','=','districts.state_id')
//     ->leftJoin('countries','countries.id','=','states.country_id')
//     ->where('locations.id','=',$locationId)
//     ->first();

//     $dealer = Dealer::select('dealers.id','dealers.dealer_name','districts.state_id')
//     ->leftJoin('villages','villages.id','=','dealers.village_id')
//     ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
//     ->leftJoin('districts','districts.id','=','talukas.district_id')
//     ->leftJoin('states','states.id','=','districts.state_id')
//     ->where('districts.state_id','=',$location_data->state_id)
//     ->orderBy('dealers.dealer_name','asc')->get();

//     return $dealer;
// }


function getSoDealer($id){
    $locationId = getCurrentLocation()->id;

    $location_data = Location::select(['locations.id', 'locations.location_code','locations.location_name','locations.village_id','locations.type','locations.mfg_process','locations.header_print','locations.status','states.country_id','districts.state_id','talukas.district_id','villages.taluka_id','locations.created_on','locations.created_by_user_id','locations.last_by_user_id','locations.last_on'])
    ->leftJoin('villages','villages.id','=','locations.village_id')
    ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
    ->leftJoin('districts','districts.id','=','talukas.district_id')
    ->leftJoin('states','states.id','=','districts.state_id')
    ->leftJoin('countries','countries.id','=','states.country_id')
    ->where('locations.id','=',$locationId)
    ->first();

    if (!empty($id)) {

        $SoDealerId = DB::table('sales_order')
        ->where('id', base64_decode($id))
        ->value('dealer_id');

        $dealers = Dealer::select('dealers.id', 'dealers.dealer_name', 'districts.state_id')
        ->leftJoin('villages', 'villages.id', '=', 'dealers.village_id')
        ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
        ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
        ->leftJoin('states', 'states.id', '=', 'districts.state_id')
        ->where(function ($query) use ($SoDealerId, $location_data) {
            $query->where('dealers.id', '=', $SoDealerId) // Specific dealer by ID
                  ->orWhere(function ($subQuery) use ($location_data) {
                      $subQuery->where('districts.state_id', '=', $location_data->state_id)
                               ->where('dealers.status', '=', 'active'); // Active dealers in the state
                  });
        })
        ->orderBy('dealers.dealer_name', 'asc')
        ->get();
    } else {
        $dealers = Dealer::select('dealers.id', 'dealers.dealer_name', 'districts.state_id')
            ->leftJoin('villages', 'villages.id', '=', 'dealers.village_id')
            ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
            ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
            ->leftJoin('states', 'states.id', '=', 'districts.state_id')
            ->where('districts.state_id', '=', $location_data->state_id)
            ->where('dealers.status', '=', 'active')
            ->orderBy('dealers.dealer_name', 'asc')
            ->get(); // Fetch all matching records
    }

    return $dealers;

}


function checkItemDisabled($itemId)
{
    $in_use = false;
    $tables = [
        SalesOrderDetail::class,
        SalesOrderDetailsDetails::class,
        PurchaseOrderDetails::class,
        GRNMaterialDetails::class,
        MaterialRequestDetail::class,
        SupplierRejectoionDetails::class,
        ItemIssueDetail::class,
        ItemReturnDetail::class,
        ItemProductionDetail::class,
        ItemAssemblyProduction::class,
        ItemAssemblyProductionDetails::class
    ];


    foreach ($tables as $table) {
        $checkItem = $table::where('item_id', $itemId)->first();

        if ($checkItem !== null) {
            $in_use = true;
        }
    }
    return $in_use;
}
function checkItemDetailDisabled($itemDetailIds)
{
    $tables = [
        [GRNMaterailSecondaryDetails::class, 'item_details_id'],
        [GRNVerification::class, 'item_details_id'],
        // [QCApproval::class, 'item_details_id'],
        [ItemReturnDetail::class, 'item_details_id'],
        [ItemIssueDetail::class, 'item_details_id'],
        [ItemAssemblyProduction::class, 'item_details_id'],
        [ItemRawMaterialMappingDetail::class, 'item_details_id'],
        [SalesReturnDetails::class, 'item_details_id'],
        [ItemStockTransfer::class, 'ist_item_details_id'],
        [ItemStockTransferDetails::class, 'item_details_id'],
        [DispatchPlanSecondaryDetails::class, 'item_details_id'],
        [LoadingEntrySecondaryDetails::class, 'item_details_id'],
        [CustomerReplacementEntryDetails::class, 'item_details_id'],
        [SOMapping::class, 'item_details_id'],
        [SOMappingDetails::class, 'item_details_id'],
        [ReplacementItemDecisionDetails::class, 'item_details_id'],
    ];

    foreach ($tables as [$modelClass, $column]) {
        if ($modelClass::whereIn($column, $itemDetailIds)->exists()) {
            return true;
        }
    }

    return false;
}


function checkLocationcode($locationID)
{

    $in_use = false;

    $toTables = [
        SalesOrder::class,
        PurchaseOrder::class,
        GRNMaterial::class,
        MaterialRequest::class,
    ];

    $fromTable = [
        SalesOrder::class,
        PurchaseOrder::class,
        GRNMaterial::class,
        MaterialRequest::class,
        SupplierRejection::class,
        ItemIssue::class,
        ItemReturn::class,
        ItemProduction::class,
        ItemAssemblyProduction::class,
    ];

    foreach ($toTables as $table) {
        $checkLocation = $table::where('to_location_id', $locationID)->first();
        if ($checkLocation != null) {
            $in_use = true;
        }
    }

    foreach ($fromTable as $table) {
        $checkLocation = $table::where('current_location_id', $locationID)->first();
        if ($checkLocation != null) {
            $in_use = true;
        }
    }

    return $in_use;
}

// function checkItemDisabled($itemId)
// {
//     $isFound = false;

//     $checkItem = SalesOrderDetail::join('sales_order_detail_details', 'sales_order_detail_details.so_details_id', 'sales_order_details.so_details_id')->where('sales_order_details.item_id', $itemId)->orWhere('sales_order_detail_details.item_id', $itemId)->first();

//     if($checkItem == false)
//     {
//         $checkItem = PurchaseOrderDetails::where('item_id', $itemId)->first();
//         if($checkItem == false)
//         {
//             $checkItem = GRNMaterialDetails::where('item_id', $itemId)->first();
//             if($checkItem == false)
//             {
//                 $checkItem = MaterialRequestDetail::where('item_id', $itemId)->first();
//             }
//             if($checkItem == false)
//             {
//                 $checkItem = SupplierRejectoionDetails::where('item_id', $itemId)->first();
//             }
//             if($checkItem == false)
//             {
//                 $checkItem = ItemIssueDetail::where('item_id', $itemId)->first();
//             }
//             if($checkItem == false)
//             {
//                 $checkItem = ItemReturnDetail::where('item_id', $itemId)->first();
//             }
//             if($checkItem == false)
//             {
//                 $checkItem = ItemProductionDetail::where('item_id', $itemId)->first();
//             }
//             if($checkItem == false)
//             {
//                 $checkItem = ItemAssemblyProductionDetails::where('item_id', $itemId)->first();
//             }
//             $isFound = true;
//         }
//         $isFound = true;
//     }
//     $isFound = true;
// }

function getHOLocation(){
    return Location::select('id','location_name')->where('type', 'HO')->orderBy('location_name','asc')->get();
}

function getMaterialLocation()
{
    $getCurrentLocation = getCurrentLocation();
    return Location::select('id','location_name')->where('id', '!=', $getCurrentLocation->id)->orderBy('location_name','asc')->get();
}


// Show the Location Name in App Blade
function getUserLocation(){
   $location = Session::get('getLocationId');
   $lc = Location::where('id', $location)->pluck('location_name')->first();
   $location_type = Location::select('type')->where('id',$location)->first();
   Session::put('getLocationType', $location_type->type);
   return $lc;

}

// function getTalukas(){
//     return Taluka::orderBy('taluka_name', 'ASC')->get();
// }

function packingIn()
{
   return PackingUnit::select('id','packing_unit')->orderBy('packing_unit','asc')->get();
}
function getSpiList($use_for = 'qc_check',$showUsed = false,$includeZeroQty = false,$formType="add",$recordId = null){
    $request = new Request();
    $request->use_for = $use_for;
    $request->show_used = $showUsed;
    $request->includeZeroQty = $includeZeroQty;
    $request->formType = $formType;
    $request->recordId = $recordId;
    $sipObj = new SupplierInwardGrnController();
    return $sipObj->getSpiList($request,false);
}


function getCustomerGroup()
{
    return CustomerGroup::orderBy('customer_group_name', 'ASC')->get();
}

function getSoCustomerGroup(){
    $locationId = getCurrentLocation()->id;

    $soCustomerGroup = LocationCustomerGroupMapping::select('customer_groups.id','customer_groups.customer_group_name')
    ->leftJoin('customer_groups','customer_groups.id','=','location_to_customer_group_mapping.customer_group_id')
    ->where('location_to_customer_group_mapping.location_id',$locationId)
    ->orderBy('customer_groups.customer_group_name', 'ASC')
    ->get();
    return  $soCustomerGroup;

}

/**
 * Get Constant Dropdown Data
 */

function customer_type()
{
  return DB::table('customer_type')->where('status','Y')->get();
}

function entry_type()
{
  return DB::table('entry_type')->where('status','Y')->get();
}

function gst_type($ord="ASC")
{
   return DB::table('gst_type')->where('status','Y')->orderBy('id',$ord)->get();
}

function machining_type()
{

  return DB::table('machining_type')->where('status','Y')->get();
}

function pattern_type()
{
   return DB::table('pattern_type')->where('status','Y')->get();
}
function rate_type()
{
   return DB::table('rate_type')->where('status','Y')->get();
}

function status_type()
{
   return DB::table('status_type')->where('status','Y')->get();
}

function supplier_po_type()
{
   return DB::table('supplier_po_type')->where('status','Y')->get();
}


function fixed_rate_type(){

    return DB::table('rate_type')->where('id','2')->get();
}

/**
 * Get Global raw sql format for finding out pending GRN Qty
 */
function getPendingGrnQtyRawSql($grn_detail_id = null,$alias_name = "pend_grn_qty",$include_inward=true,$include_qc = true,$include_rfd = true){
   return SupplierInwardGrnController::getPendingGrnQtyRawSql($grn_detail_id,$alias_name,$include_inward,$include_qc,$include_rfd);
}

/**
 * Get Global total GRN Qty by id
 */
function  getTotalGrnQtyById($grn_detail_id = null){
    $sipObj = new SupplierInwardGrnController();
    return $sipObj-> getTotalGrnQtyById($grn_detail_id);
}

/**
 * Supplier Inward Detail Can Be used or not
 */
function isThisSPIDetailCanBeUse($spi_detail_id = null,$useFor = "all"){

    if($useFor == "jobwork_outward"){

        // $isUsed = QcCheck::where('supplier_inward_detail_id','=',$spi_detail_id)->first();

        // if($isUsed != null){
        //     return false;
        // }else{

        //     $isUsed = MoveToRfd::where('supplier_inward_detail_id','=',$spi_detail_id)->first();
        //     if($isUsed != null){
        //         return false;
        //     }else{
        //         return true;
        //     }
        // }
        return true;

    }else if($useFor == "qc_check"){

        $isUsed = JobworkOutwardChallanDetail::where('supplier_inward_detail_id','=',$spi_detail_id)->first();

        if($isUsed != null){

            $isFound = JobworkInwardChallanDetail::where('jobwork_outward_id','=',$isUsed->id)->first();

            if($isFound != null){
                // $isUsed = MoveToRfd::where('supplier_inward_detail_id','=',$spi_detail_id)->first();

                // if($isUsed != null){
                //     return false;
                // }else{
                //     return true;
                // }
                return true;
            }else{
                return false;
            }

        }else{
            // $isUsed = MoveToRfd::where('supplier_inward_detail_id','=',$spi_detail_id)->first();

            // if($isUsed != null){
            //     return false;
            // }else{
            //     return true;
            // }
            return true;
        }


    }else if($useFor == "move_to_rfd"){

        // $isUsed = QcCheck::where('supplier_inward_detail_id','=',$spi_detail_id)->first();
        // if($isUsed != null){
        //     return false;
        // }else{
            $isUsed = JobworkOutwardChallanDetail::where('supplier_inward_detail_id','=',$spi_detail_id)->first();

            if($isUsed != null){
                $isFound = JobworkInwardChallanDetail::where('jobwork_outward_id','=',$isUsed->id)->first();

                if($isFound != null){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        // }

    }else{
        return true;
    }

}

/**
 * Supplier Inward Detail Can Be used or not -- old
 */
// function isThisSPIDetailCanBeUse($spi_detail_id = null,$useFor = "all"){

//     if($useFor == "jobwork_outward"){

//         $isUsed = QcCheck::where('supplier_inward_detail_id','=',$spi_detail_id)->first();

//         if($isUsed != null){
//             return false;
//         }else{
//             return true;
//         }

//     }else if($useFor == "qc_check"){

//         $isUsed = JobworkOutwardChallanDetail::where('supplier_inward_detail_id','=',$spi_detail_id)->first();

//         if($isUsed != null){

//             $isFound = JobworkInwardChallanDetail::where('jobwork_outward_id','=',$isUsed->id)->first();

//             if($isFound != null){
//                 return true;
//             }else{
//                 return false;
//             }

//         }else{
//             return true;
//         }


//     }else{
//         return true;
//     }

// }

/**
 * Get Attributes for Currency based on cuntry
 */
function getCurrencyAttributes($local = "en_IN"){

    $wholePart = "rupees";
    $fractionalPart = "paise";

    $formatter = new NumberFormatter($local, NumberFormatter::CURRENCY);

    // Get the currency code for the given locale
    $currency_code = $formatter->getTextAttribute(NumberFormatter::CURRENCY_CODE);

    if($local == "en_US"){
        $wholePart = "dollars";
        $fractionalPart = "cents";
    }

    return [
        "currency_code" => $currency_code,
        "whole_part" => $wholePart,
        "fractional_part" => $fractionalPart
    ];

}

/**
 * Numeric To Words (Amount)
 */
// function digitsToWords($amount,$local = "en_IN"){

//     $currAttrs = getCurrencyAttributes($local);

//     $currencyCode = $currAttrs["currency_code"];
//     $wholePartSpell = $currAttrs["whole_part"];
//     $fractionalPartSpell = $currAttrs["fractional_part"];

//     $wholePart = floor($amount);
//     $fractionalPart = ($amount - $wholePart) * 100; // Convert the decimal part to paise

//     $formatter = new NumberFormatter("en_IN", NumberFormatter::SPELLOUT);
//     $formatter->setAttribute(NumberFormatter::ROUNDING_MODE,NumberFormatter::ROUND_HALFUP);

//     // Set the currency code
//     $formatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, $currencyCode);

//     // Format the amount in wholePart using the SPELLOUT style
//     $wholePartInWords = $formatter->format($wholePart);

//     // Format the Fractional part in words
//     $fractionalPartInWords = $formatter->format($fractionalPart);

//     // Construct the final result
//     $result = $wholePartInWords ." ". $wholePartSpell;
//     if ($fractionalPart > 0) {
//         $result .= " and " . $fractionalPartInWords ." ". $fractionalPartSpell;
//     }

//     return str_replace(' and ', ' & ', str_replace('-', ' ', ucwords($result)))." Only";
// }



function digitsToWords($amount){

    $wholePart = floor($amount);
    $fractionalPart = (int) round(($amount - $wholePart) * 100); // <- cast AFTER rounding

    $formatter = new NumberFormatter("en_IN", NumberFormatter::SPELLOUT);
    $formatter->setAttribute(NumberFormatter::ROUNDING_MODE, NumberFormatter::ROUND_HALFUP);

    $wholePartInWords = $wholePart > 0 ? $formatter->format($wholePart) : "zero";

    $result = $wholePartInWords . " Rupees";

    if ($fractionalPart > 0) {
        // Ensure this is integer, not float!
        $fractionalPartInWords = $formatter->format($fractionalPart);
        $result .= " and " . $fractionalPartInWords . " Paisa";
    }

    $result .= " Only";

    return ucwords(str_replace('-', ' ', $result));
    // // Format the amount in wholePart using the SPELLOUT style
    // $wholePartInWords = $formatter->format($wholePart);


    // // Format the Fractional part in words
    // $fractionalPartInWords = $formatter->format($fractionalPart);


    // // Construct the final result
    // if($wholePart > 0){
    //     $result = $wholePartInWords;
    // }else{
    //     $result = "No";
    // }

    // if ($fractionalPart > 0) {
    //     $result .= " and " . $fractionalPartInWords;
    // }else{
    //     $result .= " and " . 'No';
    // }


    // return ucwords( str_replace(' and ', ' Rupees and ', str_replace('-', ' ', ucfirst($result)))." Paisa Only");
}


/**
 * Format Numbers
 */
function helpFormatWeight($number){
    if($number != "" && $number > 0){
        return number_format((float)$number, 3, '.', '');
    }else{
        return '';
    }
}

function helpFormatAmount($number){
    if($number != "" && $number > 0){
        return number_format((float)$number, 2, '.', '');
    }else{
        return '';
    }
}

/**
 * Function is used to know current page is active or not, *( 'for this here we use route name' ).
 * var Page = type string -- single page name should be provided
 * var actions = type @array
 * var noactions = type @boolean -- If true no actions in $actions array append to page name
 * var postfix = type string -- this string append to page name at last
 * var divider = type string
 * Ex: (
 *  '-' is divider and 'edit' is action ,'web' is post fix -- in below example
 *       example == " edit-{page name}-web "-- route name
 * )
 */

function isActivePage($Page = null,$actions = array(),$noactions = false,$postfix = "web",$divider = "-"){
    if($Page == null){
        return false;
    }
    if(empty($actions)){
        $actions = [
            'add',
            'edit',
            'manage'
        ];
    }

    if($noactions == false){

        foreach ($actions as $action) {
            if(Route::currentRouteName() == $action.$divider.$Page.$divider.$postfix || Route::currentRouteName() == $action.$divider.$Page){
                return true;
            }
        }
        return false;

    }else{
        if(Route::currentRouteName() == $Page){
            return true;
        }else{
            return false;
        }
    }

}

/**
 * Function is used to know single page in $Pages array is active or not, *( 'for this here we use route name' ).
 * var Pages = type @array -- Multiple pages name should be provided
 * var actions = type @array
 * var noactions = type @boolean -- If true no actions in $actions array prepend to page name at first
 * var postfix = type string -- this string append to page name at last
 * var divider = type string
 * Ex: (
 *  '-' is divider and 'edit' is action ,'web' is post fix -- in below example
 *      example == " edit-{page name}-web "-- route name
 * )
 */

function isActivePages($Pages = array(),$actions = array(),$noactions = false,$postfix = "web",$divider = "-"){
    if(empty($Pages)){
        return false;
    }
    if(empty($actions)){
        $actions = [
            'add',
            'edit',
            'manage'
        ];
    }

    if($noactions == false){
        foreach( $Pages as $page){
            foreach ($actions as $action) {
                if(Route::currentRouteName() == $action.$divider.$page.$divider.$postfix || Route::currentRouteName() == $action.$divider.$page){
                    return true;
                }
            }
        }

        return false;
    }else{
        foreach( $Pages as $page){
            if(Route::currentRouteName() == $Page){
                return true;
            }
        }

        return false;

    }
}
/**
 * Get Unit Data According To User Access
 */
function getUnits(){
    $obj_units = new UnitController();
    return $obj_units->unitData(true);
}


/**
 * User Access Check
 */
function hasAccess($pageNm,$actionNm){
    $request = new Request();
    $getLocation =  Session::get('getLocationId');

    if($getLocation == null){
     $stateManagerLocationId = UserLocation::select('company_unit_id')->where('user_id',Auth::user()->id)->first();
     Session::put('getLocationId', $stateManagerLocationId->company_unit_id);
     $getLocation =  Session::get('getLocationId');

    }

    if(!UserAccessController::checkUserAccess($request,Auth::user()->id,$pageNm,$actionNm, $getLocation)){
        return false;
    }
    return true;
}

/* For get All Company_year  ids which is less or equal to current company_year */

function getCompanyYearIdsToTill(){
    return CompanyYearController::getTillYearIds();
}


/**
 * Retrive Company Year Data based on session('default_year_id') set by selecting year on front side
 *
 * Note : if not found data then it will return json response included empty data set and message string
 */
function getCurrentYearData(){
    return CompanyYearController::getDefaultYearData();
}

function getCurrentLocation(){
    return LocationController::getDefaultLocationData();
}

/**
 * This function will return startdate and enddate value for current selected company year
 */
// function getCurrentYearDates(){
//     $defYearData = getCurrentYearData();
//     return ['startdate' => $defYearData != null ? $defYearData->startdate : '',
//     'enddate' => $defYearData != null ? $defYearData->enddate : ''];
// }

function getCurrentYearDates(){
    $defYearData = getCurrentYearData();
    return ['startdate' => $defYearData != null ? isset($defYearData->startdate) ? $defYearData->startdate : '' : '',
    'enddate' => $defYearData != null ? isset($defYearData->enddate) ? $defYearData->enddate : '' : ''];
}
/**
 * Retrive Default company Year
 */
function defaultCompanyYear(){
    $dcompanyYear = CompanyYearController::getDefaultComapnyYear();

    if($dcompanyYear != null){
        return $dcompanyYear;
    }else{
        return  "";
    }
}

/**
 * return constant value by key
 */

 function getConstValueBykey($varname = null,$key = null){
    if($varname == null || $key == null){
        return '';
    }else{
        $tmparr = constant(Str::upper($varname));
        if(is_array($tmparr) && !empty($tmparr)){
            if(array_key_exists($key,$tmparr)){
                return $tmparr[$key];
            }
        }
        return '';
    }
 }



//   start Dyanemic Menu code


function accessModule()
{
    return Module::orderBy('module_index', 'ASC')->get();
}



function accessMenu($id)
 {


     $result = DB::table('menus')
     ->select('page')
    ->where('Parent', '=', $id)
    ->orderBy('Sequence', 'asc')
    ->pluck('page')
    ->toArray();


     return $result;
 }

 function manageAccessMenu($id)
{
    return  Menus::where('parent', $id)->where('show_in_menu', 'YES')->where('show_in_access', 'YES')->orderBy('sequence' ,'ASC')->get();
}

//end Dyanemic Menu Code



function getGodwonCustomer()
{
    return CustomerGroup::join('customers','customers.customer_group_id', 'customer_groups.id')->where('customer_group_name', 'Godown')->get();
}



function getMappingRecord()
{

   return Item::join('item_groups', 'item_groups.id', 'items.item_group_id')->join('item_raw_material_mapping_details', 'item_raw_material_mapping_details.item_id', 'items.id')->select('items.id as i_id', 'items.item_name', 'item_groups.item_group_name', 'items.rate_per_unit', 'item_raw_material_mapping_details.id as itrawId', 'item_raw_material_mapping_details.raw_material_qty', 'item_raw_material_mapping_details.raw_material_id')->get();
}

 function getLatestSequence($modal,$sequence,$prefix)
{
    $year_data = getCurrentYearData();
    $locationName = getCurrentLocation();

    $isFound = $modal::where('year_id', '=', $year_data->id)->where('current_location_id',$locationName->id)->max($sequence);

    if ($isFound != null) {
        $isFound++;
    } else {
        $isFound = 1;
    }

    $middle_num = str_pad($isFound, 4, "0", STR_PAD_LEFT);

    $postfix = $year_data->yearcode;
    $locationCode = getCurrentLocation()->location_code;

    // if($prefix == 'ASS'){
        // $format =  'AP/'.$prefix.'/' . $middle_num . '/' . $postfix;
    // }else{
        $format =  $locationCode != "" ? $locationCode . '/'. $prefix.'/' . $middle_num . '/' . $postfix : $prefix.'/' . $middle_num . '/' . $postfix;
    // }
    return [
        'format' => $format,
        'isFound' => $isFound
    ];

}

function duplicationSequnce($modal,$seq, $sequence,$prefix,$id,$table_id)
{
    $year_data = getCurrentYearData();
    $locationName = getCurrentLocation();

    $check = $modal::where('year_id', '=', $year_data->id)->where('current_location_id',$locationName->id)->where($seq, $sequence)->where($table_id, '!=', $id)->first();

    if($check != null && $check != "")
    {
        return [
            'format' => 0,
        ];
    }
    else{



        // $isFound =  $modal::where('year_id', '=', $year_data->id)->where('current_location_id',$locationName->id)->where($seq, $sequence)->first();


            // $isFound++;

        $middle_num = str_pad($sequence, 4, "0", STR_PAD_LEFT);
        $postfix = $year_data->yearcode;
        $locationCode = getCurrentLocation()->location_code;

        $format =  $locationCode != "" ? $locationCode . '/'. $prefix.'/' . $middle_num . '/' . $postfix : $prefix.'/' . $middle_num . '/' . $postfix;



        return [
            'format' => $format,
            'isFound' => $sequence
        ];


    }
}






// locatin wise stock qty maintain function

// function increaseStockQty($location,$item,$qty){
//     $checkItem = LocationStock::where('item_id',$item)->where('location_id',$location)->first();

//     if($checkItem == null){
//         $location = LocationStock::create([
//             'location_id' => $location,
//             'item_id' => $item,
//             'stock_qty' => $qty > 0 ? $qty : 0,
//         ]);

//     }else{

//         $qtySum = LocationStock::where('item_id',$item)->where('location_id',$location)->sum('stock_qty');

//         $totalSumQty = $qtySum + $qty;

//         $totalQty = $totalSumQty > 0 ? $totalSumQty : 0 ;



//         $location = LocationStock::where('item_id',$item)->where('location_id',$location)
//         ->update([
//             'location_id' => $location,
//             'item_id' => $item,
//             'stock_qty' => $totalQty,
//         ]);


//     }



// }

//end function


// function stockEffect($location,$curItem,$preItem,$curQty,$preQty,$mode,$type){
function stockEffect($location,$curItem,$preItem,$curQty,$preQty,$mode,$type,$section,$form_id){
    $locationCode = getCurrentLocation();

    if($locationCode->id == $location){
        if($location != 0 && $curItem !=0 && $preItem !=0){
            if($type == 'U'){
                if($curItem == $preItem){
                    increaseStockQty($location,$curItem,$curQty,$preQty,$mode);              
                    getStockLogs($section,$mode,$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }else{
                    increaseStockQty($location,$preItem,$curQty,$preQty,'delete');
                    getStockLogs($section,'delete',$location,$preItem,$curItem,$preQty,$curQty,$form_id);

                    increaseStockQty($location,$curItem,$curQty,$preQty,'add');
                    getStockLogs($section,'add',$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }
            }elseif($type == 'D'){
                if($curItem == $preItem){
                    decreaseStockQty($location,$curItem,$curQty,$preQty,$mode);
                    getStockLogs($section,$mode,$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }else{
                    decreaseStockQty($location,$preItem,$curQty,$preQty,'delete');
                    getStockLogs($section,'delete',$location,$preItem,$curItem,$preQty,$curQty,$form_id);

                    decreaseStockQty($location,$curItem,$curQty,$preQty,'add');
                    getStockLogs($section,'add',$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }
            }
        }else{
            if($location == 0){
                abort(404,'Invalid Location Code');
            }elseif($curItem == 0){
                abort(404,'Invalid Item');
            }elseif($preItem == 0){
                abort(404,'Invalid Item');
            }
        }
    }else{
        if($section == 'GRN Verification'){
               if($location != 0 && $curItem !=0 && $preItem !=0){
            if($type == 'U'){
                if($curItem == $preItem){
                    increaseStockQty($location,$curItem,$curQty,$preQty,$mode);              
                    getStockLogs($section,$mode,$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }else{
                    increaseStockQty($location,$preItem,$curQty,$preQty,'delete');
                    getStockLogs($section,'delete',$location,$preItem,$curItem,$preQty,$curQty,$form_id);

                    increaseStockQty($location,$curItem,$curQty,$preQty,'add');
                    getStockLogs($section,'add',$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }
            }elseif($type == 'D'){
                if($curItem == $preItem){
                    decreaseStockQty($location,$curItem,$curQty,$preQty,$mode);
                    getStockLogs($section,$mode,$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }else{
                    decreaseStockQty($location,$preItem,$curQty,$preQty,'delete');
                    getStockLogs($section,'delete',$location,$preItem,$curItem,$preQty,$curQty,$form_id);

                    decreaseStockQty($location,$curItem,$curQty,$preQty,'add');
                    getStockLogs($section,'add',$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }
            }
        }else{
            if($location == 0){
                abort(404,'Invalid Location Code');
            }elseif($curItem == 0){
                abort(404,'Invalid Item');
            }elseif($preItem == 0){
                abort(404,'Invalid Item');
            }
        }

        }else{

            abort(404,'Location Code Mismatched');
        }
    }

}




// below this function use to stock qty increase
function increaseStockQty($location,$item,$curQty,$preQty,$mode){
    if($location != 0 && $item !=0){
        // $checkItem = LocationStock::where('item_id',$item)->where('location_id',$location)->first();
        $checkItem = LocationStock::where('item_id',$item)->where('location_id',$location)->lockForUpdate()->first();

        if($checkItem == null){
            $location = LocationStock::create([
                'location_id' => $location,
                'item_id' => $item,
                'stock_qty' => $curQty > 0 ? $curQty : 0,
            ]);

        }else{
            $qtySum = LocationStock::where('item_id',$item)->where('location_id',$location)->sum('stock_qty');
            // $qtySum = number_format((float)$qtySum, 3, '.');
            $qtySum = number_format((float)$qtySum, 3, '.','');

            if($mode == 'add'){
                    $totalSumQty = $qtySum + $curQty;
            }elseif($mode == 'edit'){
                    $totalSumQty = $qtySum + ($curQty - $preQty);
            }elseif($mode == 'delete'){
                    $totalSumQty = $qtySum - $preQty ;
            }

            if($totalSumQty < 0){
                throw new \App\Exceptions\InsufficientStockException('Insufficient Stock',$item);
                abort(404,'Insufficient Stock');
            }else{
                $totalQty = $totalSumQty > 0 ? $totalSumQty : 0 ;

                $location = LocationStock::where('item_id',$item)->where('location_id',$location)
                ->update([
                    'location_id' => $location,
                    'item_id' => $item,
                    'stock_qty' => $totalQty,
                ]);

            }
        }

    }else{
        if($location == 0){
            throw new \App\Exceptions\InsufficientStockException('Invalid Location Code',$item);
            abort(404,'Invalid Location Code');
        }elseif($item == 0){
            throw new \App\Exceptions\InsufficientStockException('Invalid Item',$item);
            abort(404,'Invalid Item');
        }

    }


}
//end this  function



// below this function use to stock qty decrease
function decreaseStockQty($location,$item,$curQty,$preQty,$mode){
    if($location != 0 && $item !=0){
        // $checkItem = LocationStock::where('item_id',$item)->where('location_id',$location)->first();
        $checkItem = LocationStock::where('item_id',$item)->where('location_id',$location)->lockForUpdate()->first();

        if($checkItem == null){
            throw new \App\Exceptions\InsufficientStockException('Insufficient Stock',$item);
            abort(404,'Insufficient Stock');
            // $location = LocationStock::create([
            //     'location_id' => $location,
            //     'item_id' => $item,
            //     'stock_qty' =>  0,
            // ]);
        }else{

            $qtySum = LocationStock::where('item_id',$item)->where('location_id',$location)->sum('stock_qty');
            // $qtySum = number_format((float)$qtySum, 3, '.');
            $qtySum = number_format((float)$qtySum, 3, '.','');

            if($mode == 'add'){
                $totalSumQty = $qtySum - $curQty;
            }elseif($mode == 'edit'){
                $totalSumQty = $qtySum + ($preQty - $curQty);
            }elseif($mode == 'delete'){
                $totalSumQty = $qtySum + $preQty ;
            }

            // dd($totalSumQty,$qtySum,$preQty,$curQty);
            if($totalSumQty < 0){
                throw new \App\Exceptions\InsufficientStockException('Insufficient Stock',$item);
                abort(404,'Insufficient Stock');
            }else{
                $totalQty = $totalSumQty > 0 ? $totalSumQty : 0 ;
                $location = LocationStock::where('item_id',$item)->where('location_id',$location)
                ->update([
                    'location_id' => $location,
                    'item_id' => $item,
                    'stock_qty' => $totalQty,
                ]);
            }
        }
    }else{
        if($location == 0){
            throw new \App\Exceptions\InsufficientStockException('Invalid Location Code',$item);
            abort(404,'Invalid Location Code');
        }elseif($item == 0){
            throw new \App\Exceptions\InsufficientStockException('Invalid Item',$item);
            abort(404,'Invalid Item');
        }
    }

}
//end this  function




// function stockEffect($location,$curItem,$preItem,$curQty,$preQty,$mode,$type){
function stockDetailsEffect($location,$curItem,$preItem,$curQty,$preQty,$mode,$type,$section,$form_id,$main_stock_effect,$main_section,$main_form_id){
    $curSecondQty = $curQty;
    $preSecondQty = $preQty;
    if($curItem != 0){
        $cur_details_qty = ItemDetails::where('item_details_id',$curItem)->sum('secondary_qty');
        $cur_details_qty = number_format((float)$cur_details_qty, 3, '.','');

        $curQty = $curQty * $cur_details_qty;     
        
        $curMainItem = ItemDetails::where('item_details_id',$curItem)->value('item_id');
    }
  
    if($preItem != 0){
        $pre_details_qty = ItemDetails::where('item_details_id',$preItem)->sum('secondary_qty');
        $pre_details_qty = number_format((float)$pre_details_qty, 3, '.','');

        $preQty = $preQty * $pre_details_qty;    
        
        $preMainItem = ItemDetails::where('item_details_id',$preItem)->value('item_id');

    }

    
    $locationCode = getCurrentLocation();

    if($locationCode->id == $location){
        if($location != 0 && $curItem !=0 && $preItem !=0){
            if($type == 'U'){
                if($curItem == $preItem){
                    increaseDetailsStockQty($location,$curItem,$curQty,$preQty,$curSecondQty,$preSecondQty,$mode);
                    if($main_stock_effect == 'Yes'){
                     increaseStockQty($location,$curMainItem,$curQty,$preQty,$mode);  
                     getStockLogs($main_section,$mode,$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id);   
                    }                    
                    getStockLogs($section,$mode,$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }else{
                    increaseDetailsStockQty($location,$preItem,$curQty,$preQty,$curSecondQty,$preSecondQty,'delete');
                    if($main_stock_effect == 'Yes'){
                     increaseStockQty($location,$preMainItem,$curQty,$preQty,'delete');
                     getStockLogs($main_section,'delete',$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id); 
                    }
                    getStockLogs($section,'delete',$location,$preItem,$curItem,$preQty,$curQty,$form_id);

                    increaseDetailsStockQty($location,$curItem,$curQty,$preQty,$curSecondQty,$preSecondQty,'add');
                    if($main_stock_effect == 'Yes'){
                     increaseStockQty($location,$curMainItem,$curQty,$preQty,'add');
                     getStockLogs($main_section,'add',$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id); 
                    }
                    getStockLogs($section,'add',$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }
            }elseif($type == 'D'){
                if($curItem == $preItem){
                    decreaseDetailsStockQty($location,$curItem,$curQty,$preQty,$curSecondQty,$preSecondQty,$mode);
                    if($main_stock_effect == 'Yes'){
                     decreaseStockQty($location,$curMainItem,$curQty,$preQty,$mode);
                     getStockLogs($main_section,$mode,$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id);   
                    }
                    getStockLogs($section,$mode,$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }else{
                    decreaseDetailsStockQty($location,$preItem,$curQty,$preQty,$curSecondQty,$preSecondQty,'delete');
                    if($main_stock_effect == 'Yes'){
                     decreaseStockQty($location,$preMainItem,$curQty,$preQty,'delete');
                     getStockLogs($main_section,'delete',$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id); 
                    }
                    getStockLogs($section,'delete',$location,$preItem,$curItem,$preQty,$curQty,$form_id);

                    decreaseDetailsStockQty($location,$curItem,$curQty,$preQty,$curSecondQty,$preSecondQty,'add');
                    if($main_stock_effect == 'Yes'){
                     decreaseStockQty($location,$curMainItem,$curQty,$preQty,'add');
                     getStockLogs($main_section,'add',$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id); 
                    }
                    getStockLogs($section,'add',$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                }
            }
        }else{
            if($location == 0){
                abort(404,'Invalid Location Code');
            }elseif($curItem == 0){
                abort(404,'Invalid Item');
            }elseif($preItem == 0){
                abort(404,'Invalid Item');
            }
        }
    }else{
        if($section == 'GRN Verification'){
            if($location != 0 && $curItem !=0 && $preItem !=0){
                if($type == 'U'){
                    if($curItem == $preItem){
                        increaseDetailsStockQty($location,$curItem,$curQty,$preQty,$curSecondQty,$preSecondQty,$mode);
                        if($main_stock_effect == 'Yes'){
                         increaseStockQty($location,$curMainItem,$curQty,$preQty,$mode); 
                         getStockLogs($main_section,$mode,$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id);   
                        }    
                        getStockLogs($section,$mode,$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                    }else{
                        increaseDetailsStockQty($location,$preItem,$curQty,$preQty,$curSecondQty,$preSecondQty,'delete');
                        if($main_stock_effect == 'Yes'){
                         increaseStockQty($location,$preMainItem,$curQty,$preQty,'delete');
                         getStockLogs($main_section,'delete',$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id); 
                        }
                        getStockLogs($section,'delete',$location,$preItem,$curItem,$preQty,$curQty,$form_id);

                        increaseDetailsStockQty($location,$curItem,$curQty,$preQty,$curSecondQty,$preSecondQty,'add');
                        if($main_stock_effect == 'Yes'){
                         increaseStockQty($location,$curMainItem,$curQty,$preQty,'add');
                         getStockLogs($main_section,'add',$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id); 
                        }
                        getStockLogs($section,'add',$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                    }
                }elseif($type == 'D'){
                    if($curItem == $preItem){
                        decreaseDetailsStockQty($location,$curItem,$curQty,$preQty,$curSecondQty,$preSecondQty,$mode);
                        if($main_stock_effect == 'Yes'){
                         decreaseStockQty($location,$curMainItem,$curQty,$preQty,$mode);
                         getStockLogs($main_section,$mode,$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id);   
                        }
                        getStockLogs($section,$mode,$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                    }else{
                        decreaseDetailsStockQty($location,$preItem,$curQty,$preQty,$curSecondQty,$preSecondQty,'delete');
                        if($main_stock_effect == 'Yes'){
                         decreaseStockQty($location,$preMainItem,$curQty,$preQty,'delete');
                         getStockLogs($main_section,'delete',$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id); 
                        }
                        getStockLogs($section,'delete',$location,$preItem,$curItem,$preQty,$curQty,$form_id);

                        decreaseDetailsStockQty($location,$curItem,$curQty,$preQty,$curSecondQty,$preSecondQty,'add');
                        if($main_stock_effect == 'Yes'){
                         decreaseStockQty($location,$curMainItem,$curQty,$preQty,'add');
                         getStockLogs($main_section,'add',$location,$preMainItem,$curMainItem,$preQty,$curQty,$main_form_id); 
                        }
                        getStockLogs($section,'add',$location,$preItem,$curItem,$preQty,$curQty,$form_id);
                    }
                }
            }else{
                if($location == 0){
                    abort(404,'Invalid Location Code');
                }elseif($curItem == 0){
                    abort(404,'Invalid Item');
                }elseif($preItem == 0){
                    abort(404,'Invalid Item');
                }
            }
        }else{
            abort(404,'Location Code Mismatched');
        }
    }

}


// below this function use to stock qty increase
function increaseDetailsStockQty($location,$item,$curQty,$preQty,$curSecondQty,$preSecondQty,$mode){
    if($location != 0 && $item !=0){
        // $checkItem = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)->first();
        $checkItem = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)->lockForUpdate()->first();

        if($checkItem == null){
            $location = LocationDetailStock::create([
                'location_id' => $location,
                'item_details_id' => $item,
                'stock_qty' => $curQty > 0 ? $curQty : 0,
                'secondary_stock_qty' =>  $curSecondQty > 0 ? $curSecondQty : 0,
            ]);

        }else{
            $qtySum = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)->sum('stock_qty');
            // $qtySum = number_format((float)$qtySum, 3, '.');
            $qtySum = number_format((float)$qtySum, 3, '.','');

            if($mode == 'add'){
                    $totalSumQty = $qtySum + $curQty;
            }elseif($mode == 'edit'){
                    $totalSumQty = $qtySum + ($curQty - $preQty);
            }elseif($mode == 'delete'){
                    $totalSumQty = $qtySum - $preQty ;
            }


                
            $secondQtySum = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)->sum('secondary_stock_qty');
            $secondQtySum = number_format((float)$secondQtySum, 3, '.','');  


            if($mode == 'add'){
                    $secondtotalSumQty = $secondQtySum + $curSecondQty;
            }elseif($mode == 'edit'){
                    $secondtotalSumQty = $secondQtySum + ($curSecondQty - $preSecondQty);
            }elseif($mode == 'delete'){
                    $secondtotalSumQty = $secondQtySum - $preSecondQty ;
            }



            if($totalSumQty < 0){
                throw new \App\Exceptions\InsufficientStockException('Insufficient Stock',$item);
                abort(404,'Insufficient Stock');
            }else{
                $totalQty = $totalSumQty > 0 ? $totalSumQty : 0 ;
                $secondtotalQty = $secondtotalSumQty > 0 ? $secondtotalSumQty : 0 ;


                $location = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)
                ->update([
                    'location_id' => $location,
                    'item_details_id' => $item,
                    'stock_qty' => $totalQty,
                    'secondary_stock_qty' => $secondtotalQty,
                ]);   

            }
        }

    }else{
        if($location == 0){
            throw new \App\Exceptions\InsufficientStockException('Invalid Location Code',$item);
            abort(404,'Invalid Location Code');
        }elseif($item == 0){
            throw new \App\Exceptions\InsufficientStockException('Invalid Item',$item);
            abort(404,'Invalid Item');
        }

    }


}
//end this  function



// below this function use to stock qty decrease
function decreaseDetailsStockQty($location,$item,$curQty,$preQty,$curSecondQty,$preSecondQty,$mode){
    if($location != 0 && $item !=0){
        // $checkItem = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)->first();
        $checkItem = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)->lockForUpdate()->first();

        // dd($checkItem);

        if($checkItem == null){
            throw new \App\Exceptions\InsufficientStockException('Insufficient Stock',$item);
            abort(404,'Insufficient Stock');
            // $location = LocationDetailStock::create([
            //     'location_id' => $location,
            //     'item_details_id' => $item,
            //     'stock_qty' =>  0,
            //     'secondary_stock_qty' =>  0,
            // ]);
        }else{

            $qtySum = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)->sum('stock_qty');
            // $qtySum = number_format((float)$qtySum, 3, '.');
            $qtySum = number_format((float)$qtySum, 3, '.','');

            if($mode == 'add'){
                $totalSumQty = $qtySum - $curQty;
            }elseif($mode == 'edit'){
                $totalSumQty = $qtySum + ($preQty - $curQty);
            }elseif($mode == 'delete'){
                $totalSumQty = $qtySum + $preQty ;
            }

            
            $secondQtySum = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)->sum('secondary_stock_qty');
            $secondQtySum = number_format((float)$secondQtySum, 3, '.','');           


            if($mode == 'add'){
                $secondtotalSumQty = $secondQtySum - $curSecondQty;
            }elseif($mode == 'edit'){
                $secondtotalSumQty = $secondQtySum + ($preSecondQty - $curSecondQty);
            }elseif($mode == 'delete'){
                $secondtotalSumQty = $secondQtySum + $preSecondQty ;
            }

            // dd($totalSumQty,$qtySum,$preQty,$curQty);
            if($totalSumQty < 0){
                throw new \App\Exceptions\InsufficientStockException('Insufficient Stock',$item);
                abort(404,'Insufficient Stock');
            }else{
                $totalQty = $totalSumQty > 0 ? $totalSumQty : 0 ;
                $secondtotalQty = $secondtotalSumQty > 0 ? $secondtotalSumQty : 0 ;
                $location = LocationDetailStock::where('item_details_id',$item)->where('location_id',$location)
                ->update([
                    'location_id' => $location,
                    'item_details_id' => $item,
                    'stock_qty' => $totalQty,
                    'secondary_stock_qty' => $secondtotalQty,
                ]);
            }
        }
    }else{
        if($location == 0){
            throw new \App\Exceptions\InsufficientStockException('Invalid Location Code',$item);
            abort(404,'Invalid Location Code');
        }elseif($item == 0){
            throw new \App\Exceptions\InsufficientStockException('Invalid Item',$item);
            abort(404,'Invalid Item');
        }
    }

}
//end this  function

function getAddressDetails($village_id){
    $taluka_id = Village::where('id',$village_id)->first();
    $district_id = Taluka::where('id',$taluka_id->taluka_id)->first();
    $state_id = City::where('id',$district_id->district_id)->first();
    $country_id = State::where('id',$state_id->state_id)->first();

    return [
        'taluka_id' => $taluka_id->taluka_id,
        'district_id' => $district_id->district_id,
        'state_id' => $state_id->state_id,
        'country_id' => $country_id->country_id,

    ];
}



// function getPriceListDetails()
// {
//    return Item::select(['items.*','item_groups.item_group_name','units.unit_name',])
//         ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
//         ->leftJoin('units','units.id','=','items.unit_id')
//         ->where('fitting_item', 'no')
//         ->get();

// }

function getPriceListDetails($changedItemIds = [])
{
        $item = Item::select(['items.*','item_groups.item_group_name','units.unit_name',])
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('fitting_item', 'no');

        // If editing, show both active and previously selected inactive items
        if (!empty($changedItemIds)) {
            $item->where(function ($q) use ($changedItemIds) {
                $q->where('status', 'active')
                  ->orWhereIn('id', $changedItemIds);
            });
        } else {

            // For Add Mode, show only active items
            $item->where('status', 'active');
        }


        return $item->get();

}






/**
 * Return all constants
 */

 function getAllConstants(){
    return [
      'PWHT_APPLICABLE' => PWHT_APPLICABLE,
      'OVERLAY_APPLICABLE' => OVERLAY_APPLICABLE,
      'APPLICABLE_NDT' => APPLICABLE_NDT,
      'BLOCK_REQUIREMENT' =>  BLOCK_REQUIREMENT,
      'PROJECT_LOCATION' => PROJECT_LOCATION,
      'ASME_CODE_STAMP' => ASME_CODE_STAMP,
      'UT_PROCEDURE' => UT_PROCEDURE,
      'RT_PROCEDURE' => RT_PROCEDURE,
      'PT_PROCEDURE' => PT_PROCEDURE,
      'MPT_PROCEDURE' => MPT_PROCEDURE,
      'TOFD_PROCEDURE' => TOFD_PROCEDURE,
      'OVERLAY_UT_PROCEDURE' => OVERLAY_UT_PROCEDURE,
      'PAUT_PROCEDURE' => PAUT_PROCEDURE
    ];
 }

 /**
  * return specific constants array
  */
 function getSpecificConstants($arrayOfConstants){
     $returnArr = array();
    if(is_array($arrayOfConstants) && !empty($arrayOfConstants)){
        foreach($arrayOfConstants as $const){
            if(defined(constant($const))){
                array_push($const, $returnArr);
            }
        }
    }
    return $returnArr;
 }

 function commonPdfHeader($print){
     $location  = getCurrentLocation();
     $imagePath = public_path('images/logo/bhumi_logo_print.jpg');
     $venrureimagePath = public_path('images/logo/bhumi_venture_2_logo.jpg');

        if($print == "PO"){
            $tbl = <<<EOD
            <table width="100%" cellpadding="2" cellspacing="0" border="1">
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td width="25%"><img src="$imagePath"></td>
                                <td width="5%"></td>
                                <td rowspan="2" width="70%"><img src="$venrureimagePath"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            EOD;

        }else{

            $tbl = <<<EOD
            <table width="100%" cellpadding="2" cellspacing="0" border="1">
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td width="25%"><img src="$imagePath"></td>
                                <td width="5%"></td>
                                <td rowspan="2" width="70%">$location->header_print</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            EOD;
        }
    return $tbl;
    // $this->writeHTMLCell(0, '', '', '', $tbl, 0, 0, false,true, "L", true);
 }
 function checkAddress($village=null,$pincode=null,$taluka=null,$city=null,$state=null,$country=null){
    $addres = '';
    if($village!=null){$addres .= 'At. '.$village;}
    if($pincode!=null){$addres .= ' - ' . $pincode.', ';} else {
        $addres .= ', ';
    }
    if($taluka!=null){$addres .= 'Ta. '. $taluka. ', ';}
    if($city!=null){$addres .= 'Dist. '. $city.' ';}
    if($state!=null){$addres .= ' (' .$state.', ';}
    if($country!=null){$addres .= $country. ')';}
    return $addres;
}
 function checkPOAddress($village=null,$pincode=null,$taluka=null,$city=null,$state=null,$country=null){
    $addres = '';
    if($village!=null){$addres .= 'At. '.$village;}
    if($pincode!=null){$addres .= ' - ' . $pincode.', ';} else {
        $addres .= ', ';
    }
    if($taluka!=null){$addres .= 'Ta. '. $taluka. ', ';}
    if($city!=null){$addres .= 'Dist. '. $city.' ';}
    if($state!=null){$addres .= '<br>(' .$state.', ';}
    if($country!=null){$addres .= $country. ')';}
    return $addres;
}

function getActivityLogs($section=null, $operation=null, $message=null,$line_number=null,$item=null)
  {
      try{
          $userID   = Auth::id();
          $createAt = Carbon::now('Asia/Kolkata')->toDateTimeString();

          $userLog = new LogActivity;
          $userLog->user_id = $userID;
          $userLog->operation = $operation;
          $userLog->section = $section;
          $userLog->item_id = $item;
          $userLog->message = $message;
          $userLog->line_number = $line_number;
          $userLog->created_at = $createAt;
          $userLog->save();
      }
      catch(\Exception $e)
      {
        return response()->json([

            'response_code' => '0',

            'response_message' => $e->getMessage(),

        ]);
      }
    }

function getStockLogs($section=null,$operation=null,$location=null, $pre_item=null,$cur_item=null, $pre_qty=null,$cur_qty=null,$form_id=null){
      try{
         $locationCode = getCurrentLocation();

          $userLog = new StockLog;
          $userLog->section = $section;
          $userLog->operation = $operation;
          $userLog->location_id = $locationCode->id;
          $userLog->pre_item_id = $pre_item;
          $userLog->current_item_id = $cur_item;
          $userLog->pre_qty = $pre_qty;
          $userLog->current_qty = $cur_qty;
          $userLog->form_id = $form_id;
          $userLog->created_by_user_id =  Auth::user()->id;
          $userLog->created_on = Carbon::now('Asia/Kolkata')->toDateTimeString();
          $userLog->save();
      }
      catch(\Exception $e)
      {
        return response()->json([
            'response_code' => '0',
            'response_message' => $e->getMessage(),
        ]);
      }
}

// use all data get for report
function getReportSuppliers(){
    return Supplier::select('id','supplier_name')->orderBy('supplier_name','asc')->get();
}

function getReportItems(){
    return Item::select('id','item_name')->orderBy('item_name','asc')->get();
}

function getReportDealers(){
    return Dealer::select('id','dealer_name')->orderBy('dealer_name','asc')->get();
}
function getReportCode(){
   return  Item::select('id','item_code')->orderBy('item_code','asc')->get();
}




//  function DeleteMessage($table){
//     $return_message = array(
//         'blank'=>'','states'=>'States.','districts'=>'Districts.','material_request_details'=>'Material Request.','sales_order_details'=>'Sales Order.','purchase_order_details'=>'Purchase Order.','item_production_details'=>'Item Production.'
//     );

//     $table = isset($return_message[$table])?$return_message[$table]:$return_message['blank'];

//     return $table;
//  }

 /**
  * All constant to be used in whole application
  */


function GeneratePdf($id, $name, $type, $header, $formType) {
    $crystal_url = env('CRYSTAL_URL');

    $remotePdfUrl = "{$crystal_url}{$type}_reports/{$type}.php?id={$id}&name={$name}";
    $localDirectory = storage_path("app/public/reports/{$type}_reports_file/");
    // dd($localDirectory);
    $localFileName = $name . '.pdf';
    $localFilePath = $localDirectory . $localFileName;
    $returnurl = "{$crystal_url}{$type}_reports/report_pdf_file/{$localFileName}";

    // Create directory if it doesn't exist
    if (!is_dir($localDirectory)) mkdir($localDirectory, 0777, true);

    // Check if the PDF exists remotely (for 'listing' formType)
    if ($formType == 'listing') {
        $exitsPdfUrl = "{$crystal_url}{$type}_reports/report_pdf_file/{$name}.pdf";
        $statusCode = get_headers($exitsPdfUrl, true)[0];
        if (strpos($statusCode, "200") !== false) {
            copy($returnurl, $localFilePath); // Copy if exists
        } else {
            checkPdf($remotePdfUrl, $localFilePath, $returnurl);
        }
    } else {
        checkPdf($remotePdfUrl, $localFilePath, $returnurl); // For other formTypes
    }
}

function checkPdf($remotePdfUrl, $localFilePath, $returnurl) {
    $fp = fopen($localFilePath, 'w');
    $ch = curl_init($remotePdfUrl);
    curl_setopt_array($ch, [
        CURLOPT_FILE => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FAILONERROR => true
    ]);

    if (curl_exec($ch) === false) {
        echo "cURL Error: " . curl_error($ch);
    } else {
        sleep(2);
        if (copy($returnurl, $localFilePath)) {
        } else {
            echo "Failed to copy the PDF file.";
        }
    }

    fclose($fp);
    curl_close($ch);
}


function getItemForSupllierReturn($customer_name,$dp_id,$item_id=null)
{   
    $yearIds = getCompanyYearIdsToTill();
    $locationCode = getCurrentLocation();

    $sr_fitting_le_details_id = SalesReturnDetails::select('loading_entry_details.le_details_id')         
    ->leftJoin('loading_entry_details','loading_entry_details.le_details_id','=','sales_return_details.le_details_id')
    ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
    ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
    ->where('dispatch_plan_details.fitting_item','yes')
    ->whereIn('loading_entry.year_id',$yearIds)
    ->pluck('loading_entry_details.le_details_id')
    ->toArray();

    // below query to get le_detail_id where not us in sales_return table
    $fitting_le_details_id = LoadingEntryDetails::select('loading_entry_details.le_details_id')    
    ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')    
    ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
    ->where('dispatch_plan_details.fitting_item','yes')
    ->whereNotIn('loading_entry_details.le_details_id',$sr_fitting_le_details_id)
    ->whereIn('loading_entry.year_id',$yearIds)
    ->pluck('loading_entry_details.le_details_id')
    ->toArray();


    $dp_details_id = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id',          
    DB::raw("(SELECT  loading_entry_details.loading_qty  -  (SELECT IFNULL(SUM(sales_return_details.sr_qty),0) FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),     
        ])
    ->leftJoin('dispatch_plan','dispatch_plan.dp_id', 'dispatch_plan_details.dp_id')  
    ->leftJoin('loading_entry_details','loading_entry_details.dp_details_id', 'dispatch_plan_details.dp_details_id')  
    ->where('dispatch_plan_details.fitting_item','no')     
    ->where('dispatch_plan_details.dp_id',$dp_id)
    ->having('pend_dc_qty','>',0) 
    ->pluck('dispatch_plan_details.dp_details_id')
    ->toArray();

    $without_fitting_le_detail_id = LoadingEntryDetails::select(['loading_entry_details.le_details_id',
    DB::raw("(SELECT  loading_entry_details.loading_qty  -  (SELECT IFNULL(SUM(sales_return_details.sr_qty),0) FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),     
    ])
    ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
    ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id') 
    ->where('loading_entry.current_location_id',$locationCode->id)
    ->where('dispatch_plan_details.fitting_item','no')       
    ->whereIn('loading_entry.year_id',$yearIds)
    ->having('pend_dc_qty','>',0)
    ->pluck('loading_entry_details.le_details_id')
    ->toArray();


    $dispatch_data = LoadingEntryDetails::select(['items.item_name','items.item_code','items.id', 'item_groups.item_group_name', 'sales_order.so_number','sales_order.so_date', 'sales_order.customer_name',
    'districts.district_name','locations.location_name',
    'dispatch_plan_details.dp_details_id','sales_order_details.so_qty', 'dealers.dealer_name','dispatch_plan_details.item_id','dispatch_plan_details.fitting_item','dispatch_plan.dp_number','dispatch_plan.dp_date','dispatch_plan_details.secondary_unit','dispatch_plan.dp_id',
    'sales_order_details.so_qty', 'sales_order_details.so_details_id','loading_entry_details.loading_qty as dc_qty','loading_entry_details.le_details_id','loading_entry_secondary_details.item_details_id','item_details.secondary_item_name','loading_entry_secondary_details.plan_qty','item_details.secondary_qty','loading_entry_secondary_details.le_secondary_details_id',
    // DB::raw("((SELECT IFNULL(SUM(sales_order_details.so_qty),0) FROM sales_order_details WHERE so_details_id  = dispatch_plan_details.so_details_id ) - dispatch_plan_details.plan_qty) as pending_qty"),  

        DB::raw("(SELECT loading_entry_details.loading_qty  
        -  
        (SELECT IFNULL(SUM(sales_return_details.sr_qty),0)
        FROM sales_return_details  WHERE sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"),     
    

    'dispatch_plan.multiple_loading_entry',
        DB::raw('0 as loading_qty'), 
        DB::raw('0 as sr_details_id'), 
        'dispatch_plan_details.so_from_value_fix',
        'dispatch_plan_details.allow_partial_dispatch',

    DB::raw("(SELECT loading_entry_secondary_details.plan_qty  
    -  
    (SELECT IFNULL(SUM(sales_return_details.sr_details_qty),0)
    FROM sales_return_details  WHERE sales_return_details.item_details_id = loading_entry_secondary_details.item_details_id AND sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_details_dc_qty"), 

    DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
            ELSE units.unit_name END as unit_name"), 
    
    ])  
    ->leftJoin('loading_entry_secondary_details','loading_entry_secondary_details.le_details_id','=','loading_entry_details.le_details_id')        
    ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_details.dp_details_id')
    ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
    ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
    ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
    ->leftJoin('villages','villages.id','=','sales_order.customer_village')
    ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
    ->leftJoin('dealers','dealers.id', 'sales_order.dealer_id')
    ->leftJoin('districts','districts.id', 'sales_order.customer_district_id') 
    ->leftJoin('items', 'items.id', 'dispatch_plan_details.item_id')
    ->leftJoin('item_details','item_details.item_details_id','=','loading_entry_secondary_details.item_details_id')
    ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
    ->leftJoin('units', 'units.id', 'items.unit_id')
    ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')
    ->where('dispatch_plan_details.dp_id',$dp_id)
    ->where(function($q) use($fitting_le_details_id,$without_fitting_le_detail_id,$item_id){
        $q->whereIn('loading_entry_details.le_details_id', array_merge($fitting_le_details_id,$without_fitting_le_detail_id));
        
        if($item_id){
            $q->orWhereIn('dispatch_plan_details.item_id',$item_id);
        }
   
        
    })
    ->where('sales_order.customer_name',$customer_name) 
    ->havingRaw("
    (CASE 
        WHEN loading_entry_secondary_details.item_details_id IS NOT NULL 
        THEN (SELECT loading_entry_secondary_details.plan_qty - (SELECT IFNULL(SUM(sales_return_details.sr_details_qty),0) 
            FROM sales_return_details 
            WHERE sales_return_details.item_details_id = loading_entry_secondary_details.item_details_id 
            AND sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id))
        ELSE 1
    END) > 0
    ")
    ->get();
    
    
    return $dispatch_data;
}

function getItemDetailsForSalesReturn($dp_details_id,$item_id,$request_id = null){
    
     $locationCode = getCurrentLocation();

    $item_detail = LoadingEntrySecondaryDetails::select('loading_entry_secondary_details.plan_qty','loading_entry_secondary_details.item_details_id','loading_entry_secondary_details.item_id','item_details.secondary_item_name','loading_entry_secondary_details.dp_details_id','units.unit_name','location_stock_details.secondary_stock_qty','item_details.secondary_qty','dispatch_plan_details.dp_id',
    DB::raw("(SELECT loading_entry_secondary_details.plan_qty  
    -  
    (SELECT IFNULL(SUM(sales_return_details.sr_details_qty),0)
    FROM sales_return_details  WHERE sales_return_details.item_details_id = loading_entry_secondary_details.item_details_id AND sales_return_details.dp_details_id = dispatch_plan_details.dp_details_id)) pend_dc_qty"), )    
            
    ->leftJoin('location_stock_details','location_stock_details.item_details_id','=','loading_entry_secondary_details.item_details_id')
    ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','=','loading_entry_secondary_details.dp_details_id')
    // ->leftJoin('dispatch_plan','dispatch_plan.dp_id','=','dispatch_plan_details.dp_id')
    ->leftJoin('items','items.id','=','loading_entry_secondary_details.item_id')
    ->leftJoin('item_details','item_details.item_details_id','=','loading_entry_secondary_details.item_details_id')
    ->leftJoin('units','units.id','=','items.second_unit')
    ->where('loading_entry_secondary_details.dp_details_id',$dp_details_id)
    ->where('loading_entry_secondary_details.item_id',$item_id)
    ->where('location_stock_details.location_id',$locationCode->id)
    ->get();

    if($request_id){
        foreach($item_detail as $ikey=>$ival){
            $sr_qty = SalesReturnDetails::where('sr_id',$request_id)->where('dp_details_id',$dp_details_id)->where('item_details_id',$ival->item_details_id)->sum('sr_details_qty');

            if($sr_qty){
                $ival->pend_dc_qty = $ival->pend_dc_qty + $sr_qty;

            }else{
                    $ival->pend_dc_qty = $ival->pend_dc_qty ;  
            }
        }

    }

     $item_detail = $item_detail->filter(function ($item) {
        return $item->pend_dc_qty > 0;
    })->values(); // reset array
              
              

    return $item_detail;

}

function LiveUpdateSecDate($date,$item){
    $sec_unit = Item::where('id',$item)->where('secondary_unit','=','Yes')->first();

    if(!empty($sec_unit) && $date <= '2025-10-26' )
    {
        return true;
    }
    else{
        return false;
    }
}


define("PWHT_APPLICABLE" ,[
    'yes' => 'Yes',
    'no' => 'No',
    'partially' => 'Partially'
]);

define("OVERLAY_APPLICABLE" ,[
    'yes' => 'Yes',
    'no' => 'No',
    'partially' => 'Partially'
]);

define("APPLICABLE_NDT",[
    'rt' => 'RT',
    'paut' => 'PAUT',
    'rt+paut' => 'RT + PAUT',
    'tofd+paut' => 'TOFD + PAUT',
    'rt+tofd+paut' => 'RT + TOFD + PAUT'
]);

define("BLOCK_REQUIREMENT",[
    'given' => 'Given',
    'not_given' => 'Not Given',
    'not_applicable' => 'Not Applicable'
]);

define("PROJECT_LOCATION",[
    'plant-15' => 'Plant-15',
    'plant-15a' => 'Plant-15A',
    'plant-19' => 'Plant-19',
    'dahej' => 'Dahej'
]);

define("ASME_CODE_STAMP",[
    'yes' => 'Yes',
    'no' => 'No'
]);

define("UT_PROCEDURE",[
    'not_submitted' => 'Not Submitted',
    'submitted' => 'Submitted',
    'code-1' => 'Code-1',
    'code-2' => 'Code-2',
    'code-3' => 'Code-3',
    'not_applicable' => 'Not Applicable'
]);

define("RT_PROCEDURE",[
    'not_submitted' => 'Not Submitted',
    'submitted' => 'Submitted',
    'code-1' => 'Code-1',
    'code-2' => 'Code-2',
    'code-3' => 'Code-3',
    'not_applicable' => 'Not Applicable'
]);

define("PT_PROCEDURE",[
    'not_submitted' => 'Not Submitted',
    'submitted' => 'Submitted',
    'code-1' => 'Code-1',
    'code-2' => 'Code-2',
    'code-3' => 'Code-3',
    'not_applicable' => 'Not Applicable'
]);

define("MPT_PROCEDURE",[
    'not_submitted' => 'Not Submitted',
    'submitted' => 'Submitted',
    'code-1' => 'Code-1',
    'code-2' => 'Code-2',
    'code-3' => 'Code-3',
    'not_applicable' => 'Not Applicable'
]);

define("TOFD_PROCEDURE",[
    'not_submitted' => 'Not Submitted',
    'submitted' => 'Submitted',
    'code-1' => 'Code-1',
    'code-2' => 'Code-2',
    'code-3' => 'Code-3',
    'not_applicable' => 'Not Applicable'
]);

define("OVERLAY_UT_PROCEDURE",[
    'not_submitted' => 'Not Submitted',
    'submitted' => 'Submitted',
    'code-1' => 'Code-1',
    'code-2' => 'Code-2',
    'code-3' => 'Code-3',
    'not_applicable' => 'Not Applicable'
]);

define("PAUT_PROCEDURE",[
    'not_submitted' => 'Not Submitted',
    'submitted' => 'Submitted',
    'code-1' => 'Code-1',
    'code-2' => 'Code-2',
    'code-3' => 'Code-3',
    'not_applicable' => 'Not Applicable'
]);
?>