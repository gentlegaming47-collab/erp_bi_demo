<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\MaterialRequest;
use App\Models\Admin;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportGMApproval;
use App\Exports\ExportSMApproval;
use App\Exports\ExportStateCoordinatorApproval;
use App\Exports\ExportZSMApproval;

class ApprovalReportController extends Controller
{
    public function manage()
    {
        return view('manage.manage-approval_report');
    }

    public function index(Request $request){

        $yearIds = getCompanyYearIdsToTill();
             
        if($request->PageName == 'sm_approval_report'){
            $user_id = 'sm_user_id';
        }else if($request->PageName == 'state_coordinator_approval_report'){
            $user_id = 'state_coordinator_user_id';
        }else if($request->PageName == 'zsm_approval_report'){
            $user_id = 'zsm_user_id';
        }else if($request->PageName == 'md_approval_report'){
            $user_id = 'md_user_id';
        }else if($request->PageName == 'gm_approval_report'){
            $user_id = 'gm_user_id';
        }else{
            $user_id='';
        }

        if($request->PageName == 'sm_approval_report'){
            $orderColumn = 'material_request.sm_approvaldate';
        }else if($request->PageName == 'state_coordinator_approval_report'){
            $orderColumn = 'material_request.state_coordinator_approvaldate';
        }else if($request->PageName == 'zsm_approval_report'){
            $orderColumn = 'material_request.zsm_approvaldate';
        }else if($request->PageName == 'md_approval_report'){
            $orderColumn = 'material_request.md_approvaldate';
        }else if($request->PageName == 'gm_approval_report'){
            $orderColumn = 'material_request.gm_approvaldate';
        }else{
            $orderColumn = 'material_request.mr_date'; // fallback
        }

        $getMaterialData = MaterialRequest::select(['material_request.mr_number', 'material_request.mr_id', 'material_request.mr_date', 'locations.location_name','material_request.sm_user_id','material_request.zsm_user_id','material_request.gm_user_id','material_request.md_user_id','to_location.location_name as to_location','material_request.state_coordinator_user_id','material_request.special_notes'])    
        ->leftJoin('locations','locations.id','material_request.current_location_id')
        ->leftJoin('locations as to_location','to_location.id','material_request.to_location_id')      
        ->whereNotNull('material_request.' . $user_id)
        ->whereIn('material_request.year_id',$yearIds)
        ->where('material_request.' . $user_id, '=', Auth::user()->id)
        ->orderBy($orderColumn, 'asc');
        if($request->trans_from_date != "" || $request->trans_to_date != ""){

            // PageName pramane date column select
            if ($request->PageName == 'sm_approval_report') {
                $dateColumn = 'material_request.sm_approvaldate';
            } elseif ($request->PageName == 'state_coordinator_approval_report') {
                $dateColumn = 'material_request.state_coordinator_approvaldate';
            } elseif ($request->PageName == 'zsm_approval_report') {
                $dateColumn = 'material_request.zsm_approvaldate';
            } elseif ($request->PageName == 'gm_approval_report') {
                $dateColumn = 'material_request.gm_approvaldate';
            } elseif ($request->PageName == 'md_approval_report') {
                $dateColumn = 'material_request.md_approvaldate';
            } 

            if($request->trans_from_date != "" && $request->trans_to_date != ""){
            
                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


                $getMaterialData->whereDate($dateColumn,'>=',$from);

                $getMaterialData->whereDate($dateColumn,'<=',$to);

            }else if($request->trans_from_date != ""){

                $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

                $getMaterialData->where($dateColumn,'>=',$from);

            }else if($request->trans_to_date != ""){

                $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

                $getMaterialData->where($dateColumn,'<=',$to);

            } 
        }
        return DataTables::of($getMaterialData)

        ->editColumn('mr_date', function($getMaterialData){ 
            if ($getMaterialData->mr_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $getMaterialData->mr_date)->format('d/m/Y'); 
                return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->filterColumn('material_request.mr_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(material_request.mr_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        
        ->addColumn('approve_date', function($getMaterialData) { 
            $request = request();
            $date = '';
        
            if ($request->PageName == 'sm_approval_report') {
                $sm_date = MaterialRequest::select('sm_approvaldate')->where('mr_id', $getMaterialData->mr_id)->first();
                if ($sm_date && $sm_date->sm_approvaldate) {
                    $date = Date::createFromFormat('Y-m-d', $sm_date->sm_approvaldate)->format('d/m/Y');
                }
        
            } else if ($request->PageName == 'state_coordinator_approval_report') {
                $zsm_date = MaterialRequest::select('state_coordinator_approvaldate')->where('mr_id', $getMaterialData->mr_id)->first();
                if ($zsm_date && $zsm_date->state_coordinator_approvaldate) {
                    $date = Date::createFromFormat('Y-m-d', $zsm_date->state_coordinator_approvaldate)->format('d/m/Y');
                }
        
            } else if ($request->PageName == 'zsm_approval_report') {
                $zsm_date = MaterialRequest::select('zsm_approvaldate')->where('mr_id', $getMaterialData->mr_id)->first();
                if ($zsm_date && $zsm_date->zsm_approvaldate) {
                    $date = Date::createFromFormat('Y-m-d', $zsm_date->zsm_approvaldate)->format('d/m/Y');
                }
        
            } else if ($request->PageName == 'md_approval_report') {
                $md_date = MaterialRequest::select('md_approvaldate')->where('mr_id', $getMaterialData->mr_id)->first();
                if ($md_date && $md_date->md_approvaldate) {
                    $date = Date::createFromFormat('Y-m-d', $md_date->md_approvaldate)->format('d/m/Y');
                }
            } else if ($request->PageName == 'gm_approval_report') {
                $md_date = MaterialRequest::select('gm_approvaldate')->where('mr_id', $getMaterialData->mr_id)->first();
                if ($md_date && $md_date->gm_approvaldate) {
                    $date = Date::createFromFormat('Y-m-d', $md_date->gm_approvaldate)->format('d/m/Y');
                }
            }
        
            return $date;
        })

        ->filterColumn('approve_date', function ($query, $keyword) {
            $request = request();

            if ($request->PageName == 'sm_approval_report') {
                $query->whereRaw("DATE_FORMAT(material_request.sm_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            } 
            else if ($request->PageName == 'state_coordinator_approval_report') {
                $query->whereRaw("DATE_FORMAT(material_request.state_coordinator_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            } 
            else if ($request->PageName == 'zsm_approval_report') {
                $query->whereRaw("DATE_FORMAT(material_request.zsm_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            } 
            else if ($request->PageName == 'md_approval_report') {
                $query->whereRaw("DATE_FORMAT(material_request.md_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            }
            else if ($request->PageName == 'gm_approval_report') {
                $query->whereRaw("DATE_FORMAT(material_request.gm_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            }
        })

        ->addColumn('approvad_by', function($getMaterialData) use ($user_id) { 

            $request = request();
           
            if ($request->PageName == 'sm_approval_report') {
                $userId = $getMaterialData->sm_user_id;
            }else if ($request->PageName == 'state_coordinator_approval_report') {
                $userId = $getMaterialData->state_coordinator_user_id; 
            } else if ($request->PageName == 'zsm_approval_report') {
                $userId = $getMaterialData->zsm_user_id; 
            } else if ($request->PageName == 'md_approval_report') {
                $userId = $getMaterialData->md_user_id; 
            }  else if ($request->PageName == 'gm_approval_report') {
                $userId = $getMaterialData->gm_user_id; 
            } else {
                return '';
            }

            $admin = Admin::select('user_name')->where('id', $userId)->first();
            return $admin ? $admin->user_name : ''; 
            
        })
        ->filterColumn('approvad_by', function($query, $keyword) {
            $request = request();

            if ($request->PageName == 'sm_approval_report') {
                $query->whereRaw("(SELECT user_name FROM admin WHERE admin.id = material_request.sm_user_id) LIKE ?", ["%{$keyword}%"]);
            } elseif ($request->PageName == 'state_coordinator_approval_report') {
                $query->whereRaw("(SELECT user_name FROM admin WHERE admin.id = material_request.state_coordinator_user_id) LIKE ?", ["%{$keyword}%"]);
            } elseif ($request->PageName == 'zsm_approval_report') {
                $query->whereRaw("(SELECT user_name FROM admin WHERE admin.id = material_request.zsm_user_id) LIKE ?", ["%{$keyword}%"]);
            } elseif ($request->PageName == 'gm_approval_report') {
                $query->whereRaw("(SELECT user_name FROM admin WHERE admin.id = material_request.gm_user_id) LIKE ?", ["%{$keyword}%"]);
            }elseif ($request->PageName == 'md_approval_report') {
                $query->whereRaw("(SELECT user_name FROM admin WHERE admin.id = material_request.md_user_id) LIKE ?", ["%{$keyword}%"]);
            }
        })

      
        ->rawColumns(['mr_date','approve_date','approvad_by '])
        ->make(true);
    }

    public function exportGMApproval(Request $request)
    {
        $searchData = [];
        $global = $request->input('global');
        $columns = $request->input('columns', []);

        $fromDate = $request->input('trans_from_date');
        $toDate = $request->input('trans_to_date');

        if($fromDate && is_string($fromDate))
        {
            $searchData['trans_from_date'] = trim($fromDate);
        }

        if($toDate && is_string($toDate))
        {
            $searchData['trans_to_date'] = trim($toDate);
        }

        if($global && is_string($global))
        {
            $searchData['global'] = trim($global);
        }

        if(is_array($columns))
        {
            foreach($columns as $idx => $val)
            {
                if(is_string($val) && strlen($val) <= 255)
                {
                    $searchData['columns'][$idx] = trim($val);
                }
            }
        }
        
        return Excel::download(new ExportGMApproval($searchData), 'GM Approval Report.xlsx');
    }

    public function exportSMApproval(Request $request)
    {
        $searchData = [];
        $global = $request->input('global');
        $columns = $request->input('columns', []);

        $fromDate = $request->input('trans_from_date');
        $toDate = $request->input('trans_to_date');

        if($fromDate && is_string($fromDate))
        {
            $searchData['trans_from_date'] = trim($fromDate);
        }

        if($toDate && is_string($toDate))
        {
            $searchData['trans_to_date'] = trim($toDate);
        }

        if($global && is_string($global))
        {
            $searchData['global'] = trim($global);
        }

        if(is_array($columns))
        {
            foreach($columns as $idx => $val)
            {
                if(is_string($val) && strlen($val) <= 255)
                {
                    $searchData['columns'][$idx] = trim($val);
                }
            }
        }
        
        return Excel::download(new ExportSMApproval($searchData), 'SM Approval Report.xlsx');
    }

    public function exportStateCoordinatorApproval(Request $request)
    {
        $searchData = [];
        $global = $request->input('global');
        $columns = $request->input('columns', []);

        $fromDate = $request->input('trans_from_date');
        $toDate = $request->input('trans_to_date');

        if($fromDate && is_string($fromDate))
        {
            $searchData['trans_from_date'] = trim($fromDate);
        }

        if($toDate && is_string($toDate))
        {
            $searchData['trans_to_date'] = trim($toDate);
        }

        if($global && is_string($global))
        {
            $searchData['global'] = trim($global);
        }

        if(is_array($columns))
        {
            foreach($columns as $idx => $val)
            {
                if(is_string($val) && strlen($val) <= 255)
                {
                    $searchData['columns'][$idx] = trim($val);
                }
            }
        }
        
        return Excel::download(new ExportStateCoordinatorApproval($searchData), 'State Coordinator Approval Report.xlsx');
    }

    public function exportZSMApproval(Request $request)
    {
        $searchData = [];
        $global = $request->input('global');
        $columns = $request->input('columns', []);

        $fromDate = $request->input('trans_from_date');
        $toDate = $request->input('trans_to_date');

        if($fromDate && is_string($fromDate))
        {
            $searchData['trans_from_date'] = trim($fromDate);
        }

        if($toDate && is_string($toDate))
        {
            $searchData['trans_to_date'] = trim($toDate);
        }

        if($global && is_string($global))
        {
            $searchData['global'] = trim($global);
        }

        if(is_array($columns))
        {
            foreach($columns as $idx => $val)
            {
                if(is_string($val) && strlen($val) <= 255)
                {
                    $searchData['columns'][$idx] = trim($val);
                }
            }
        }
        
        return Excel::download(new ExportZSMApproval($searchData), 'ZSM Approval Report.xlsx');
    }
}