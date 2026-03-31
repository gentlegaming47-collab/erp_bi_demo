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
use App\Models\PurchaseRequisitionDetails;
use App\Models\PurchaseRequisition;

class PurchaseOrderSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-purchase_order_summary');
    }
    public function index(PurchaseOrder $purchase_order,Request $request,DataTables $dataTables)
    {

        $location = getCurrentLocation();
        $year_data = getCurrentYearData();

        $purchase_order = PurchaseOrderDetails::select([
            'purchase_order.po_id',
            'purchase_order.po_sequence',
            'purchase_order.po_number',
            'purchase_order.po_date',
            'purchase_order.person_name',
            'purchase_order.order_by',
            'items.item_name',
            'items.item_code',
            'purchase_order_details.po_qty',
            'units.unit_name',
            'purchase_order_details.rate_per_unit',
            'purchase_order_details.discount',
            'purchase_order_details.del_date',
            'suppliers.supplier_name',
            'locations.location_name',
            'purchase_requisition.pr_number',
            'purchase_requisition.pr_date',
            'purchase_order.is_approved',


           'purchase_order_details.amount',
                // DB::raw('SUM(purchase_order_details.amount) as amount')

        ])
        ->leftJoin('purchase_order','purchase_order.po_id' ,'purchase_order_details.po_id')
        ->leftJoin('purchase_requisition_details','purchase_requisition_details.pr_details_id','=',
            'purchase_order_details.pr_details_id')
        ->leftJoin('purchase_requisition','purchase_requisition.pr_id', 'purchase_requisition_details.pr_id')
        ->leftJoin('suppliers','suppliers.id' ,'purchase_order.supplier_id')
        ->leftJoin('locations','locations.id' ,'purchase_order.to_location_id')
        ->leftJoin('items', 'items.id', '=', 'purchase_order_details.item_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')

        ->where('purchase_order.current_location_id','=',$location->id);
            // ->where('purchase_order.year_id', '=', $year_data->id)
        //   ->groupBy('purchase_order.po_number');
            if($request->from_date != "" && $request->to_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');


            $purchase_order->whereDate('purchase_order.po_date','>=',$from);

            $purchase_order->whereDate('purchase_order.po_date','<=',$to);

        }else if($request->from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $purchase_order->where('purchase_order.po_date','>=',$from);

        }else if($request->to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');

            $purchase_order->where('purchase_order.po_date','<=',$to);

        }
        if($request->item_id !=''){
            $purchase_order->where('purchase_order_details.item_id', '=', $request->item_id);
        }
        if($request->po_number !=''){
            $purchase_order->where('purchase_order.po_number','like', "%{$request->po_number}%");
        }
        if($request->pr_number !=''){
            $purchase_order->where('purchase_requisition.pr_number','like', "%{$request->pr_number}%");
        }
        if($request->supplier_id !=''){
            $purchase_order->where('purchase_order.supplier_id', '=', $request->supplier_id);
        }
        if($request->order_by !=''){
            $purchase_order->where('purchase_order.order_by','like', "%{$request->order_by}%");
        }
        return DataTables::of($purchase_order)
           ->editColumn('rate_per_unit', function($row){
                return $row->rate_per_unit > 0 ? number_format((float)$row->rate_per_unit, 3, '.', '') : number_format(0, 3, '.', '');
            })
            ->editColumn('discount', function($row){
                return $row->discount > 0 ? number_format((float)$row->discount, 3, '.', '') : number_format(0, 3, '.', '');
            })
            ->editColumn('po_qty', function($row){
                return $row->po_qty > 0 ? number_format((float)$row->po_qty, 3, '.', '') : number_format(0, 3, '.', '');
            })
            ->editColumn('order_by', function($row){
            if($row->order_by != ''){
                $order_by = ucfirst($row->order_by);
                return $order_by;
            }else{
                return '';
            }
        })
            ->editColumn('amount', function($itemAssmProduction) {
                return $itemAssmProduction->amount > 0
                ? number_format((float)$itemAssmProduction->amount, 3, '.', '')
                : number_format(0, 3, '.', '');
            })
            ->editColumn('is_approved', function($row){
                if($row->is_approved === 1){
                    return 'Approved';
                } else {
                    return 'Pending';
                }
            })
            ->filterColumn('purchase_order.is_approved', function($query, $keyword) {
                if (stripos('Approved', $keyword) !== false) {
                    $query->where('purchase_order.is_approved', 1);
                } elseif (stripos('Pending', $keyword) !== false) {
                    $query->where('purchase_order.is_approved', 0);
                }
            })
            ->editColumn('po_date', function($purchase_order){
                if ($purchase_order->po_date != null) {
                    $formatedDate1 = Date::createFromFormat('Y-m-d', $purchase_order->po_date)->format('d/m/Y');
                    return $formatedDate1;
                }else{
                    return '';
                }

            })
            ->editColumn('pr_date', function($purchase_order){
                if ($purchase_order->pr_date != null) {
                    $formatedDate1 = Date::createFromFormat('Y-m-d', $purchase_order->pr_date)->format('d/m/Y');
                    return $formatedDate1;
                }else{
                    return '';
                }

            })
            ->editColumn('del_date', function($purchase_order){
                if ($purchase_order->del_date != null) {
                    $formatedDate1 = Date::createFromFormat('Y-m-d', $purchase_order->del_date)->format('d/m/Y');
                    return $formatedDate1;
                }else{
                    return '';
                }

            })
            ->filterColumn('purchase_order.po_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(purchase_order.po_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->filterColumn('purchase_requisition.pr_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(purchase_requisition.pr_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->filterColumn('purchase_order_details.del_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(purchase_order_details.del_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }
}
