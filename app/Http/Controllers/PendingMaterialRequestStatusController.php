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

class PendingMaterialRequestStatusController extends Controller
{
    //

     public function manage()
    {
        return view('manage.manage-pending_material_request_status');
    }

     public function index(MaterialRequest $MaterialRequest,Request $request,DataTables $dataTables)
    {
         $yearIds = getCompanyYearIdsToTill();

        $locationCode = getCurrentLocation()->id;        
        
        $mrData = MaterialRequestDetail:: select(['material_request.mr_number','material_request.mr_date','material_request.mr_sequence','items.item_name','items.item_code','units.unit_name',
        'material_request.mr_id','locations.location_name','material_request_details.mr_qty','material_request_details.form_type','customer_groups.customer_group_name', 'material_request_details.remarks','material_request.sm_approvaldate','material_request.state_coordinator_approvaldate','material_request.zsm_approvaldate',
        ]) 
        ->leftJoin('material_request','material_request.mr_id','=','material_request_details.mr_id') 
        ->leftJoin('customer_groups','customer_groups.id', 'material_request.customer_group_id')
        ->leftJoin('items','items.id','=','material_request_details.item_id')  
        ->leftJoin('units','units.id','=','items.unit_id')  
        ->leftJoin('locations','locations.id','=','material_request.current_location_id') 
        ->where('material_request.current_location_id', $locationCode)
        ->where('material_request.gm_user_id', null)
        ->whereIN('material_request.year_id',$yearIds);
        
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $mrData->whereDate('material_request.mr_date','>=',$from);

                $mrData->whereDate('material_request.mr_date','<=',$to);

        }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $mrData->where('material_request.mr_date','>=',$from);

        }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $mrData->where('material_request.mr_date','<=',$to);

        }
       
        return DataTables::of($mrData)
           ->editColumn('mr_qty', function($row){
                return $row->mr_qty > 0 ? number_format((float)$row->mr_qty, 3, '.', '') : number_format(0, 3, '.', '');
            })
           
            ->editColumn('mr_date', function($mrData){
                if ($mrData->mr_date != null) {
                    $formatedDate1 = Date::createFromFormat('Y-m-d', $mrData->mr_date)->format('d/m/Y');
                    return $formatedDate1;
                }else{
                    return '';
                }

            })
            ->filterColumn('material_request.mr_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(material_request.mr_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->editColumn('sm_approvaldate', function($mrData){
                if ($mrData->sm_approvaldate != null) {
                    $formatedDate1 = Date::createFromFormat('Y-m-d', $mrData->sm_approvaldate)->format('d/m/Y');
                    return $formatedDate1;
                }else{
                    return '';
                }

            })
            ->filterColumn('material_request.sm_approvaldate', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(material_request.sm_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->editColumn('state_coordinator_approvaldate', function($mrData){
                if ($mrData->state_coordinator_approvaldate != null) {
                    $formatedDate1 = Date::createFromFormat('Y-m-d', $mrData->state_coordinator_approvaldate)->format('d/m/Y');
                    return $formatedDate1;
                }else{
                    return '';
                }

            })
            ->filterColumn('material_request.state_coordinator_approvaldate', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(material_request.state_coordinator_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->editColumn('zsm_approvaldate', function($mrData){
                if ($mrData->zsm_approvaldate != null) {
                    $formatedDate1 = Date::createFromFormat('Y-m-d', $mrData->zsm_approvaldate)->format('d/m/Y');
                    return $formatedDate1;
                }else{
                    return '';
                }

            })
            ->filterColumn('material_request.zsm_approvaldate', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(material_request.zsm_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })
           
            ->make(true);
    }
            
     

}
