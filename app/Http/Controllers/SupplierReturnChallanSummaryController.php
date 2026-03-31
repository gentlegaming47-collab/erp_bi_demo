<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierRejection;
use App\Models\SupplierRejectoionDetails;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;

class SupplierReturnChallanSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-supplier_return_challan_summary');
    }
    public function index(SupplierRejection $SupplierRejection,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();

        $supplier_rej_challan_data = SupplierRejection::select(['src_sequence','src_number', 'src_date','suppliers.supplier_name','supplier_rejection_challan_details.item_id','items.item_code','supplier_rejection_challan_details.challan_qty','supplier_rejection_challan_details.remarks','supplier_rejection_challan.transporter_id','supplier_rejection_challan.vehicle_no','supplier_rejection_challan.lr_no_date','supplier_rejection_challan.special_notes',
        'supplier_rejection_challan.ref_no', 'supplier_rejection_challan.src_type_value_fix',
        'supplier_rejection_challan.ref_date','transporters.transporter_name',
        'supplier_rejection_challan.src_id','units.unit_name',
        ])

        ->leftJoin('transporters','transporters.id','=','supplier_rejection_challan.transporter_id')
        ->leftJoin('suppliers','suppliers.id','=','supplier_rejection_challan.supplier_id')
        ->leftJoin('supplier_rejection_challan_details','supplier_rejection_challan_details.src_id','=','supplier_rejection_challan.src_id')
        ->leftJoin('items','items.id','=','supplier_rejection_challan_details.item_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'supplier_rejection_challan.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'supplier_rejection_challan.last_by_user_id')
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


        
        ->editColumn('challan_qty', function($supplier_rej_challan_data){

            return $supplier_rej_challan_data->challan_qty > 0 ? number_format((float)$supplier_rej_challan_data->challan_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
     

        // ->editColumn('challan_qty', function($supplier_rej_challan_data){

        //     return $supplier_rej_challan_data->challan_qty > 0 ? $supplier_rej_challan_data->challan_qty : 0;

        // })


        ->make(true);
    }


}
