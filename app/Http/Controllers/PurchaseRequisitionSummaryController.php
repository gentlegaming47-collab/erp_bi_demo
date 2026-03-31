<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseRequisitionDetails;
use Yajra\DataTables\DataTables;
use Date;


class PurchaseRequisitionSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-purchase_requisition_summary');
    }


    public function index(Request $request, DataTables $dataTables, PurchaseRequisitionDetails $pr_data)
    {
        $locationId = getCurrentLocation()->id;
        $yearIds    = getCompanyYearIdsToTill();

        $pr_data = PurchaseRequisitionDetails::select(['purchase_requisition.pr_id','purchase_requisition.pr_number','purchase_requisition.pr_sequence','purchase_requisition.pr_date','suppliers.supplier_name','items.item_name','items.item_code','purchase_requisition_details.req_qty','units.unit_name','purchase_requisition_details.rate_per_unit','purchase_requisition_details.remarks','purchase_requisition.prepared_by','purchase_requisition.special_notes','locations.location_name','purchase_requisition.pr_form_value_fix',])

        ->leftJoin('purchase_requisition','purchase_requisition.pr_id','=','purchase_requisition_details.pr_id')
        ->leftJoin('suppliers','suppliers.id','=','purchase_requisition.supplier_id')
        ->leftJoin('locations','locations.id','=','purchase_requisition.to_location_id')
        ->leftJoin('items','items.id','=','purchase_requisition_details.item_id')
        ->leftJoin('units','units.id', 'items.unit_id')
        ->where('purchase_requisition.current_location_id', $locationId);
        // ->whereIn('purchase_requisition.year_id',$yearIds);


        // search terms
        if($request->from_date != "" && $request->to_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');   
           

            $pr_data->whereDate('purchase_requisition.pr_date','>=',$from);

            $pr_data->whereDate('purchase_requisition.pr_date','<=',$to);

        }else if($request->from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $pr_data->where('purchase_requisition.pr_date','>=',$from);

        }else if($request->to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');

            $pr_data->where('purchase_requisition.pr_date','<=',$to);

        }

        if($request->pr_number !=''){
            $pr_data->where('purchase_requisition.pr_number','like', "%{$request->pr_number}%");
        }

        if($request->item_id !=''){
            $pr_data->where('purchase_requisition_details.item_id', '=', $request->item_id);
        }

        if($request->supplier_id !=''){
            $pr_data->where('purchase_requisition_details.supplier_id','=',$request->supplier_id);
        }
        if($request->prepared_by !=''){
            $pr_data->where('purchase_requisition.prepared_by','like', "%{$request->prepared_by}%");
        }
      

        // end search terms


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

        // ->editColumn('supplier_name', function($pr_data){ 
        //         if($pr_data->supplier_name != ''){
        //             $supplier_name = ucfirst($pr_data->supplier_name);
        //             return $supplier_name;
        //         }else{
        //             return '';
        //         }
        // })
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
        ->filterColumn('supplier_name', function($query, $keyword) {
            $query->where(function($subQuery) use ($keyword) {
                $subQuery
                    ->where('purchase_requisition.supplier_name', 'like', "%{$keyword}%")
                    
                    ->orWhereIn('purchase_requisition.pr_id', function($q) use ($keyword) {
                        $q->select('purchase_requisition_details.pr_id')
                            ->from('purchase_requisition_details')
                            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchase_requisition_details.supplier_id')
                            ->where('suppliers.supplier_name', 'like', "%{$keyword}%");
                    });
            });
        })

        ->editColumn('item_name', function($pr_data){ 
                if($pr_data->item_name != ''){
                    $item_name = ucfirst($pr_data->item_name);
                    return $item_name;
                }else{
                    return '';
                }
        })
        ->editColumn('req_qty', function($pr_data) {
            return $pr_data->req_qty > 0 
                ? number_format((float)$pr_data->req_qty, 3, '.', '') 
                : number_format(0, 3, '.', ''); 
        })
        ->editColumn('rate_per_unit', function($pr_data) {
            return $pr_data->rate_per_unit > 0 
                ? number_format((float)$pr_data->rate_per_unit, 2, '.', '') 
                : ''; 
        })
        ->make(true);
    }
}
