<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;

class MaterialRequestSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-material_request_summary');
    }
    public function index(MaterialRequest $MaterialRequest,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();


       $material_request = MaterialRequest::select([
            'material_request.mr_number',
            'material_request.mr_sequence',
            'material_request.mr_date',
            'items.item_name',
            'items.item_code',
            'material_request_details.mr_qty',
            'material_request_details.remarks',
            'from_location.location_name as from_location_name',
            'to_location.location_name as to_location_name',
            'material_request.mr_id',
            'item_groups.item_group_name',
            'customer_groups.customer_group_name',
            'units.unit_name as unit_name'
        ])


        ->leftJoin('material_request_details','material_request_details.mr_id','=','material_request.mr_id')

        ->leftJoin('locations as to_location', 'to_location.id', '=', 'material_request.to_location_id')
        ->leftJoin('locations as from_location', 'from_location.id', '=', 'material_request.current_location_id')

        ->leftJoin('items','items.id','=','material_request_details.item_id')
        ->leftJoin('item_groups','item_groups.id', 'items.item_group_id')
        ->leftJoin('customer_groups','customer_groups.id', 'material_request.customer_group_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')

        ->where('material_request.current_location_id','=',$location->id);


        // filter for search data
        if($request->from_date != "" && $request->to_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');


            $material_request->whereDate('material_request.mr_date','>=',$from);

            $material_request->whereDate('material_request.mr_date','<=',$to);

        }else if($request->from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

            $material_request->where('material_request.mr_date','>=',$from);

        }else if($request->to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');

            $material_request->where('material_request.mr_date','<=',$to);

        }

        if($request->customer_name !=''){
            $material_request->where('material_request.customer_name','like', "%{$request->customer_name}%");
        }

        if($request->location_id !=''){
            $material_request->where('material_request.to_location_id','=',$request->location_id);
        }
        if($request->mr_number != ''){
             $material_request->where('material_request.mr_number', 'like', "%{$request->mr_number}%");
        }
        if ($request->from_location_id != '') {
            $material_request->where('material_request.current_location_id', '=', $request->from_location_id);
        }
        if ($request->cust_group_id != '') {
            $material_request->where('material_request.customer_group_id', '=', $request->cust_group_id);
        }




        // end search terms


        return DataTables::of($material_request)

        ->editColumn('item_name', function($material_request){
            if($material_request->item_name != ''){
                $item_name = ucfirst($material_request->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

        ->editColumn('mr_qty', function($material_request){

            return $material_request->mr_qty > 0 || $material_request->mr_qty != ""  ? number_format((float)$material_request->mr_qty, 3, '.','') : '';
        })



        ->editColumn('mr_date', function($material_request){
            if ($material_request->mr_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $material_request->mr_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('material_request.mr_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(material_request.mr_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->make(true);
    }

}
