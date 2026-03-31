<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemProduction;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use App\Models\MaterialRequest;
use App\Models\Location;
use App\Models\MaterialRequestDetail;
use App\Models\UserAccess;
use App\Models\UserLocation;
use App\Models\PriceListDetails;
use Str;

class ApprovalController extends Controller
{
    public function manage()
    {
        return view('manage.manage-approval');
    }

    public function index(Request $request){
        $yearIds = getCompanyYearIdsToTill();

        if($request->PageName == 'manage-sm_approval'){
            $user_id = 'sm_user_id';
        }else if($request->PageName == 'manage-state_coordinator_approval'){
            $user_id = 'state_coordinator_user_id';
        }else if($request->PageName == 'manage-zsm_approval'){
            $user_id = 'zsm_user_id';
        }else if($request->PageName == 'manage-gm_approval'){
            $user_id = 'gm_user_id';
        }
        // else if($request->PageName == 'manage-md_approval'){
        //     $user_id = 'md_user_id';
        // }
        if($request->PageName == 'manage-sm_approval'){
            $orderColumn = 'material_request.sm_approvaldate';
        }else if($request->PageName == 'manage-state_coordinator_approval'){
            $orderColumn = 'material_request.state_coordinator_approvaldate';
        }else if($request->PageName == 'manage-zsm_approval'){
            $orderColumn = 'material_request.zsm_approvaldate';
        }else if($request->PageName == 'manage-gm_approval'){
            $orderColumn = 'material_request.gm_approvaldate';
        }else{
            $orderColumn = 'material_request.mr_date'; // fallback
        }

        $getMaterialData = MaterialRequest::select(['material_request.mr_number', 'material_request.mr_id', 'material_request.mr_date', 'locations.location_name','material_request.sm_user_id','material_request.zsm_user_id','material_request.md_user_id','to_location.location_name as to_location','material_request.state_coordinator_user_id','material_request.approval_type_id_fix','material_request.special_notes','material_request.gm_user_id','material_request.sm_approvaldate','material_request.state_coordinator_approvaldate','material_request.zsm_approvaldate','material_request.gm_approvaldate'])    
        ->leftJoin('locations','locations.id','material_request.current_location_id')
        ->leftJoin('locations as to_location','to_location.id','material_request.to_location_id')      
        ->whereNotNull('material_request.' . $user_id)
        ->whereIn('material_request.year_id',$yearIds)
        ->where('material_request.' . $user_id, '=', Auth::user()->id)
        ->orderBy($orderColumn, 'desc');  
        if($request->trans_from_date != "" || $request->trans_to_date != ""){

            // PageName pramane date column select
            if ($request->PageName == 'manage-sm_approval') {
                $dateColumn = 'material_request.sm_approvaldate';
            } elseif ($request->PageName == 'manage-state_coordinator_approval') {
                $dateColumn = 'material_request.state_coordinator_approvaldate';
            } elseif ($request->PageName == 'manage-zsm_approval') {
                $dateColumn = 'material_request.zsm_approvaldate';
            } elseif ($request->PageName == 'manage-gm_approval') {
                $dateColumn = 'material_request.gm_approvaldate';
            } else {
                $dateColumn = 'material_request.mr_date';
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
        
            if ($request->PageName == 'manage-sm_approval') {
                $sm_date = MaterialRequest::select('sm_approvaldate')->where('mr_id', $getMaterialData->mr_id)->first();
                if ($sm_date && $sm_date->sm_approvaldate) {
                    $date = Date::createFromFormat('Y-m-d', $sm_date->sm_approvaldate)->format('d/m/Y');
                }
        
            } else if ($request->PageName == 'manage-state_coordinator_approval') {
                $zsm_date = MaterialRequest::select('state_coordinator_approvaldate')->where('mr_id', $getMaterialData->mr_id)->first();
                if ($zsm_date && $zsm_date->state_coordinator_approvaldate) {
                    $date = Date::createFromFormat('Y-m-d', $zsm_date->state_coordinator_approvaldate)->format('d/m/Y');
                }
        
            } else if ($request->PageName == 'manage-zsm_approval') {
                $zsm_date = MaterialRequest::select('zsm_approvaldate')->where('mr_id', $getMaterialData->mr_id)->first();
                if ($zsm_date && $zsm_date->zsm_approvaldate) {
                    $date = Date::createFromFormat('Y-m-d', $zsm_date->zsm_approvaldate)->format('d/m/Y');
                }
        
            } else if ($request->PageName == 'manage-gm_approval') {
                $md_date = MaterialRequest::select('gm_approvaldate')->where('mr_id', $getMaterialData->mr_id)->first();
                if ($md_date && $md_date->gm_approvaldate) {
                    $date = Date::createFromFormat('Y-m-d', $md_date->gm_approvaldate)->format('d/m/Y');
                }
            }
        
            return $date;
        })

        ->filterColumn('approve_date', function ($query, $keyword) {
            $request = request();

            if ($request->PageName == 'manage-sm_approval') {
                $query->whereRaw("DATE_FORMAT(material_request.sm_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            } 
            else if ($request->PageName == 'manage-state_coordinator_approval') {
                $query->whereRaw("DATE_FORMAT(material_request.state_coordinator_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            } 
            else if ($request->PageName == 'manage-zsm_approval') {
                $query->whereRaw("DATE_FORMAT(material_request.zsm_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            } 
            else if ($request->PageName == 'manage-gm_approval') {
                $query->whereRaw("DATE_FORMAT(material_request.gm_approvaldate, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            }
        })

        ->addColumn('approvad_by', function($getMaterialData) use ($user_id) { 

            $request = request();
           
            if ($request->PageName == 'manage-sm_approval') {
                $userId = $getMaterialData->sm_user_id;
            }else if ($request->PageName == 'manage-state_coordinator_approval') {
                $userId = $getMaterialData->state_coordinator_user_id; 
            } else if ($request->PageName == 'manage-zsm_approval') {
                $userId = $getMaterialData->zsm_user_id; 
            } else if ($request->PageName == 'manage-gm_approval') {
                $userId = $getMaterialData->gm_user_id; 
            } else {
                return '';
            }

            $admin = Admin::select('user_name')->where('id', $userId)->first();
            return $admin ? $admin->user_name : ''; 
            
        })
        ->filterColumn('approvad_by', function($query, $keyword) {
            $request = request();

            if ($request->PageName == 'manage-sm_approval') {
                $query->whereRaw("(SELECT user_name FROM admin WHERE admin.id = material_request.sm_user_id) LIKE ?", ["%{$keyword}%"]);
            } elseif ($request->PageName == 'manage-state_coordinator_approval') {
                $query->whereRaw("(SELECT user_name FROM admin WHERE admin.id = material_request.state_coordinator_user_id) LIKE ?", ["%{$keyword}%"]);
            } elseif ($request->PageName == 'manage-zsm_approval') {
                $query->whereRaw("(SELECT user_name FROM admin WHERE admin.id = material_request.zsm_user_id) LIKE ?", ["%{$keyword}%"]);
            } elseif ($request->PageName == 'manage-gm_approval') {
                $query->whereRaw("(SELECT user_name FROM admin WHERE admin.id = material_request.gm_user_id) LIKE ?", ["%{$keyword}%"]);
            }
        })


        ->addColumn('options', function($getMaterialData) use ($user_id) { 

            $request = request();

            $action = "<div>";
           
            if ($request->PageName == 'manage-sm_approval') {
                $userId = $getMaterialData->zsm_user_id;
               
                if($userId == null){
                    if(hasAccess("sm_approval","delete")){
                     $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                    }
                }        
            }else if ($request->PageName == 'manage-state_coordinator_approval') {
                // $userId = $getMaterialData->zsm_user_id; .
                $userId = $getMaterialData->gm_user_id;     
                if($userId == null){
                    if(hasAccess("state_coordinator_approval","delete")){
                     $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                    }
                }        
            } else if ($request->PageName == 'manage-zsm_approval') {
                // $userId = $getMaterialData->md_user_id;                 
                $userId = $getMaterialData->state_coordinator_user_id;                 

                if($userId == null){                   
                    if(hasAccess("zsm_approval","delete")){
                     $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                    }
                }        

            } else if ($request->PageName == 'manage-gm_approval') {
                $userId = $getMaterialData->approval_type_id_fix; 

                // if($userId <= '4'){
                if($userId == '4'){
                    if(hasAccess("gm_approval","delete")){
                     $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                    }
                }        
            } else {
                return '';
            }

            $action .= "</div>";
            return $action;
            
        })
      
        ->rawColumns(['mr_date','approve_date','approvad_by','options'])
        ->make(true);
    }

    public function create()
    {
        return view('add.add-approval');
    }


    public function show(Request $request){

        $yearIds = getCompanyYearIdsToTill();
        $locationID = getCurrentLocation();
        // get the user type based on location
        // $getUser = Admin::where('id', $locationID->created_by_user_id)->select(['user_type', 'user_name'])->first();
       
        $getMaterialData = '';
        
        $pageName =  $request->pageName;
       
        $stateManagerLocationId = UserLocation::select('company_unit_id')->where('user_id',Auth::user()->id)->get();  
        
        $getUser = Admin::where('id', Auth::user()->id)->select(['user_type', 'user_name'])->first();

       
        // $getMaterial = [];
        if($pageName =="add-sm_approval")   
        // if($getUser->user_type == "state_manager" || $pageName =="sm_approval")   
        {
           
            $getMaterialData = MaterialRequest::select(['material_request.mr_number', 'material_request.mr_date', 'locations.location_name', 'material_request.mr_id','to_location.location_name as to_location','material_request.special_notes'])
            // ->leftJoin('material_request_details','material_request_details.mr_id','=','material_request.mr_id')
            ->leftJoin('locations','locations.id','material_request.current_location_id')
            ->leftJoin('locations as to_location','to_location.id','material_request.to_location_id')
            // ->leftJoin('items','items.id','material_request_details.item_id')
            // ->leftJoin('units','units.id','items.unit_id')
            ->whereIn('material_request.year_id', $yearIds)
            ->where('material_request.sm_user_id', null);
            // ->where('material_request.approval_type_id_fix', 0);
            if (Auth::user()->id != 1 && Auth::user()->user_type != "admin") {
                // $getMaterialData->whereIn('material_request.to_location_id', $stateManagerLocationId);
                $getMaterialData->whereIn('material_request.current_location_id', $stateManagerLocationId);
            }
            // ->orderBy('material_request.mr_date','desc')
             $getMaterialData = $getMaterialData->get();
        }    

        else if($pageName =="add-state_coordinator_approval"){

            $getMaterialData = MaterialRequest::select(['material_request.mr_number', 'material_request.mr_date', 'locations.location_name', 'material_request.mr_id','to_location.location_name as to_location','admin.user_name as sm_user_id','material_request.sm_approvaldate','material_request.special_notes','admin2.user_name as zsm_user_id','material_request.zsm_approvaldate',])         
            ->leftJoin('locations','locations.id','material_request.current_location_id')
            ->leftJoin('locations as to_location','to_location.id','material_request.to_location_id')    
            ->leftJoin('admin','admin.id','material_request.sm_user_id') 
            ->leftJoin('admin as admin2','admin2.id','material_request.zsm_user_id')    
            ->whereIn('material_request.year_id', $yearIds) 
            ->where('material_request.sm_user_id', '!=', null)
            ->where('material_request.zsm_user_id', '!=', null)
            ->where('material_request.state_coordinator_user_id', '=', null);
            // ->where('material_request.approval_type_id_fix', 1);
            if (Auth::user()->id != 1 && Auth::user()->user_type != "admin") {
                // $getMaterialData->whereIn('material_request.to_location_id', $stateManagerLocationId);
                $getMaterialData->whereIn('material_request.current_location_id', $stateManagerLocationId);
            }
            // ->orderBy('material_request.mr_date','desc')
             $getMaterialData = $getMaterialData->get();

             if($getMaterialData != null){
                foreach($getMaterialData as $val){
                    $val->getType = "state_coordinator";

                    if($val->sm_approvaldate != null){
                        $val->sm_approvaldate = Date::createFromFormat('Y-m-d', $val->sm_approvaldate)->format('d/m/Y');
                    }

                     if($val->zsm_approvaldate != null){
                        $val->zsm_approvaldate = Date::createFromFormat('Y-m-d', $val->zsm_approvaldate)->format('d/m/Y');
                    }
                }
            }
            
        }

    //    else if($getUser->user_type == "zonal_manager"  || $pageName =="zsm_approval")   
       else if($pageName =="add-zsm_approval")   
        {
            
            $getMaterialData = MaterialRequest::select(['material_request.mr_number', 'material_request.mr_date', 'locations.location_name', 'material_request.mr_id','to_location.location_name as to_location','admin.user_name as sm_user_id','material_request.sm_approvaldate','material_request.state_coordinator_approvaldate',
            // 'admin2.user_name as state_coordinator_user_id',
            'material_request.special_notes'])         
            ->leftJoin('locations','locations.id','material_request.current_location_id')
            ->leftJoin('locations as to_location','to_location.id','material_request.to_location_id')    
            ->leftJoin('admin','admin.id','material_request.sm_user_id') 
            // ->leftJoin('admin as admin2','admin2.id','material_request.state_coordinator_user_id') 
            ->whereIn('material_request.year_id', $yearIds) 
            ->where('material_request.sm_user_id', '!=', null)
            // ->where('material_request.state_coordinator_user_id', '!=', null)
            ->where('material_request.zsm_user_id', '=', null);
            // ->where('material_request.approval_type_id_fix', 1);
            if (Auth::user()->id != 1 && Auth::user()->user_type != "admin") {
                // $getMaterialData->whereIn('material_request.to_location_id', $stateManagerLocationId);
                $getMaterialData->whereIn('material_request.current_location_id', $stateManagerLocationId);
            }
            // ->orderBy('material_request.mr_date','desc')
             $getMaterialData = $getMaterialData->get();

            if($getMaterialData != null){
                foreach($getMaterialData as $val){
                    $val->getType = "zsm";

                    if($val->sm_approvaldate != null){
                        $val->sm_approvaldate = Date::createFromFormat('Y-m-d', $val->sm_approvaldate)->format('d/m/Y');
                    }
                    if($val->state_coordinator_approvaldate != null){
                        $val->state_coordinator_approvaldate = Date::createFromFormat('Y-m-d', $val->state_coordinator_approvaldate)->format('d/m/Y');
                    }
                }
            }

    
           
        }   

       else if($pageName =="add-gm_approval")   
    //    else if($getUser->user_type == "director" || $pageName =="md_approval")   
        {
     
            $getMaterialData = MaterialRequest::select(['material_request.mr_number', 'material_request.mr_date', 'locations.location_name', 'material_request.mr_id','to_location.location_name as to_location','admin.user_name as sm_user_id','material_request.sm_approvaldate','material_request.state_coordinator_approvaldate','admin2.user_name as state_coordinator_user_id','material_request.zsm_approvaldate','admin3.user_name as zsm_user_id','material_request.special_notes'])         
            ->leftJoin('locations','locations.id','material_request.current_location_id')
            ->leftJoin('locations as to_location','to_location.id','material_request.to_location_id') 
            ->leftJoin('admin','admin.id','material_request.sm_user_id')
            ->leftJoin('admin as admin2','admin2.id','material_request.state_coordinator_user_id')    
            ->leftJoin('admin as admin3','admin3.id','material_request.zsm_user_id')    
            ->whereIn('material_request.year_id', $yearIds) 
            ->where('material_request.sm_user_id', '!=', null)
            ->where('material_request.zsm_user_id', '!=', null)
            ->where('material_request.gm_user_id', '=', null);
            // ->where('material_request.md_user_id', '=', null);
            // ->where('material_request.approval_type_id_fix', 2);
            if (Auth::user()->id != 1 && Auth::user()->user_type != "admin") {
                // $getMaterialData->whereIn('material_request.to_location_id', $stateManagerLocationId);
                $getMaterialData->whereIn('material_request.current_location_id', $stateManagerLocationId);
            }
            // ->orderBy('material_request.mr_date','desc')
            $getMaterialData = $getMaterialData->get();

            if($getMaterialData != null){
                foreach($getMaterialData as $val){
                    $val->getType = "md";

                    if($val->sm_approvaldate != null){
                        $val->sm_approvaldate = Date::createFromFormat('Y-m-d', $val->sm_approvaldate)->format('d/m/Y');
                    }
                    if($val->state_coordinator_approvaldate != null){
                        $val->state_coordinator_approvaldate = Date::createFromFormat('Y-m-d', $val->state_coordinator_approvaldate)->format('d/m/Y');
                    }
                    if($val->zsm_approvaldate != null){
                        $val->zsm_approvaldate = Date::createFromFormat('Y-m-d', $val->zsm_approvaldate)->format('d/m/Y');
                    }
                }

            }
       
        }     
        else
        {
            if(Auth::user()->id==1)
            {
                $getMaterialData = MaterialRequest::select(['material_request.mr_number', 'material_request.mr_date', 'locations.location_name', 'material_request.mr_id','to_location.location_name as to_location','material_request.special_notes'])          
                ->leftJoin('locations','locations.id','material_request.current_location_id')
                ->leftJoin('locations as to_location','to_location.id','material_request.to_location_id')            
                ->whereIn('material_request.year_id', $yearIds)
                ->where('material_request.sm_user_id', null)->get();
                


            }
    
        }
        if ($getMaterialData != null) {
            foreach ($getMaterialData as $cpKey => $cpVal) {
                if ($cpVal->mr_date != null) {
                    $cpVal->mr_date = Date::createFromFormat('Y-m-d', $cpVal->mr_date)->format('d/m/Y');
                }
            }
        }
        if($getUser != "")
        {
            return response()->json([               
                'getMaterial' => $getMaterialData, 
                'approved_by' => $getUser,                 
                'response_code' => 1,
                'response_message' => "Get Material Record",
            ]);
        }else{
            return response()->json([               
                'response_code' => 0,
                'response_message' => "Material Not Found",
            ]);
        }
    }

    
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            // dd($request->all(),$request->new_mr_details_id);
            // if(Auth::user()->id == 1 || Auth::user()->user_type == "operator")
            // {
              
            //     $userType = $request->pagename;
               
            //     // $aprovId  = $userType == "sm_approval" ? 1 : ($userType == "zsm_approval" ? 2 : ($userType == "md_approval" ? 3 : 4)); 
            //     $aprovId  = $userType == "sm_approval" ? 1 : ($userType == "state_coordinator" ? 2 : ($userType == "zsm_approval" ? 3 : ($userType == "md_approval" ? 4 : 5)));              
                
            // }else{
            //     $userType = Auth::user()->user_type;

            //     // $aprovId = $userType == "state_manager" ? 1 : ($userType == "zonal_manager" ? 2 : ($userType == "director" ? 3 : 4));
            //     $aprovId = $userType == "state_manager" ? 1 : ($userType == "state_coordinator" ? 2 : ($userType == "zonal_manager" ? 3 : ($userType == "director" ? 4 : 5)));
            // }

            $userType = $request->pagename;
               
            // $aprovId  = $userType == "sm_approval" ? 1 : ($userType == "zsm_approval" ? 2 : ($userType == "md_approval" ? 3 : 4)); 

            // old condition
            // $aprovId  = $userType == "add-sm_approval" ? 1 : ($userType == "add-state_coordinator_approval" ? 2 : ($userType == "add-zsm_approval" ? 3 : ($userType == "add-md_approval" ? 4 : 5))); 
            
             $aprovId  = $userType == "add-sm_approval" ? 1 : ($userType == "add-zsm_approval" ? 2 : ($userType == "add-state_coordinator_approval" ? 3 : ($userType == "add-gm_approval" ? 4 : 5))); 
            
            
            if(isset($request->mr_id))
            {
                foreach($request->mr_id as $mkey => $mval){
                
                    if($request->pagename == "add-sm_approval")
                    // if(Auth::user()->user_type == "state_manager" ||  $userType == "sm_approval" )
                    {
                        
                            $store = MaterialRequest::where('mr_id', $mval)->update([
                                'sm_approvaldate' =>  Date::createFromFormat('d/m/Y', $request->approval_date)->format('Y-m-d'),             
                                'sm_user_id' => Auth::user()->id,
                                'approval_type_id_fix' => $aprovId ,
                                'sm_created_on' =>  Carbon::now('Asia/Kolkata')->toDateTimeString(),
                            ]);            
                    
                    }
                    else if($request->pagename == "add-state_coordinator_approval")
                    // else if(Auth::user()->user_type == "zonal_manager" ||  $userType == "zsm_approval")
                    {
                        $store = MaterialRequest::where('mr_id', $mval)->update([
                            'state_coordinator_approvaldate' =>  Date::createFromFormat('d/m/Y', $request->approval_date)->format('Y-m-d'),             
                            'state_coordinator_user_id' => Auth::user()->id,
                            'approval_type_id_fix' => $aprovId ,
                            'state_coordinator_created_on' =>  Carbon::now('Asia/Kolkata')->toDateTimeString(),
                        ]);    
                    }
                    else if($request->pagename == "add-zsm_approval")
                    // else if(Auth::user()->user_type == "zonal_manager" ||  $userType == "zsm_approval")
                    {
                        $store = MaterialRequest::where('mr_id', $mval)->update([
                            'zsm_approvaldate' =>  Date::createFromFormat('d/m/Y', $request->approval_date)->format('Y-m-d'),             
                            'zsm_user_id' => Auth::user()->id,
                            'approval_type_id_fix' => $aprovId ,
                            'zsm_created_on' =>  Carbon::now('Asia/Kolkata')->toDateTimeString(),
                        ]);    
                    }
                    else if($request->pagename == "add-gm_approval")
                    // else if(Auth::user()->user_type == "director" ||  $userType == "md_approval")
                    {
                        $store = MaterialRequest::where('mr_id', $mval)->update([
                            'gm_approvaldate' =>  Date::createFromFormat('d/m/Y', $request->approval_date)->format('Y-m-d'),             
                            'gm_user_id' => Auth::user()->id,
                            'approval_type_id_fix' => $aprovId ,
                            'gm_created_on' =>  Carbon::now('Asia/Kolkata')->toDateTimeString(),
                        ]);    
                    }
                }

                $total_mr_qty = 0;
                if(isset($request->mr_detail_id) && $request->pagename == "add-state_coordinator_approval"){
                    foreach($request->mr_detail_id as $key => $val){                       
                        $mrDetail = MaterialRequestDetail::where('mr_details_id', $val)->update([
                            'mr_qty' => $request->mr_qty[$key],
                            'form_type'=> isset($request->form_type[$key]) ? $request->form_type[$key] : "",
                        ]);
                        $total_mr_qty += $request->mr_qty[$key];
                    }

                }else{
                    foreach($request->mr_detail_id as $key => $val){                       
                        $mrDetail = MaterialRequestDetail::where('mr_details_id', $val)->update([
                            'mr_qty' => $request->mr_qty[$key],
                            // 'form_type'=> isset($request->form_type[$key]) ? $request->form_type[$key] : "",
                        ]);
                        $total_mr_qty += $request->mr_qty[$key];
                    }
                }

                if(isset($request->mr_id))
                {
                    foreach($request->mr_id as $mkey => $mval){
                        $store_update = MaterialRequest::where('mr_id', $mval)->update([
                            'total_qty' => $total_mr_qty,
                        ]);
                    }
                }

                if (isset($request->new_mr_details_id) && !empty($request->new_mr_details_id)) {
                    foreach ($request->new_mr_details_id as $sodKey => $sodVal) { 

                        if($sodVal == "0"){                                    
                            if(isset($request->new_item_id[$sodKey]) && $request->new_item_id[$sodKey] != null){
                                $customer_group_id = MaterialRequest::select('customer_group_id')->where('mr_id',$request->mr_id[0])->first('customer_group_id');

                                if(isset($request->new_item_id[$sodKey]) && $request->new_item_id[$sodKey] != null){  
                                    $rate_unit = PriceListDetails::select('sales_rate')->where('customer_group_id',$customer_group_id->customer_group_id)->where('item_id',$request->new_item_id[$sodKey])->first();
                                }else{
                                    $rate_unit = '';
                                }

                                $materialDtai=   MaterialRequestDetail::create([
                                    'mr_id'    => $request->mr_id[0],
                                    'item_id'   => isset($request->new_item_id[$sodKey]) ? $request->new_item_id[$sodKey] : "",
                                    'form_type'   => isset($request->new_form_type[$sodKey]) ? $request->new_form_type[$sodKey] : "",
                                    'mr_qty'   => isset($request->new_mr_qty[$sodKey]) ? $request->new_mr_qty[$sodKey] : "",
                                    'rate_unit'   => isset($rate_unit) ? $rate_unit->sales_rate : '',    
                                    'remarks'   => isset($request->new_remarks[$sodKey]) ? $request->new_remarks[$sodKey] : "",                                 
                                ]);

                            }
                        }
                    }
                }

                if (isset($request->remove_mr_details_id) && !empty($request->remove_mr_details_id)) {
                    foreach ($request->remove_mr_details_id as $sodKey => $sodVal) { 
                        MaterialRequestDetail::where('mr_details_id',$sodVal)->delete();
                    }
                }
                                
            
                DB::commit();
                if($store != "")
                {
                    return response()->json([
                        'response_code' => 1,
                        'response_message' => 'Record Inserted Successfully.',
                    ]);
                }else{
                    return response()->json([
                        'response_code' => 0,
                        'response_message' => 'Error Occured Record Not Inserted',
                    ]);
                }
            }
           
        }catch(\Exception $e)
        {            
            DB::rollBack();
            getActivityLogs($userType, "add", $e->getMessage(),$e->getLine());  
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }
    // public function store(Request $request)
    // {
    //     try{
    //         if(Auth::user()->id == 1)
    //         {
              
    //             $userType = $request->pagename;
                
    //             $aprovId  = $userType == "sm_approval" ? 1 : ($userType == "zsm_approval" ? 2 : ($userType == "md_approval" ? 3 : 4)); 
    //         }else{
    //             $userType = Auth::user()->user_type;
    //             $aprovId = $userType == "state_manager" ? 1 : ($userType == "zonal_manager" ? 2 : ($userType == "director" ? 3 : 4));
    //         }
            
            
    //         if($request->material_detail_id != "" && $request->material_detail_id != null)
    //         {
    //             foreach($request->material_detail_id as $mid)
    //             {
    //                 if(Auth::user()->user_type == "state_manager" ||  $userType == "sm_approval" )
    //                 {
    //                         $store = MaterialRequestDetail::where('mr_details_id', $mid)->update([
    //                             'sm_approvaldate' =>  Date::createFromFormat('d/m/Y', $request->approval_date)->format('Y-m-d'),             
    //                             'sm_user_id' => Auth::user()->id,
    //                             'approval_type_id_fix' => $aprovId ,
    //                             'sm_created_on' =>  Carbon::now('Asia/Kolkata')->toDateTimeString(),
    //                         ]);            
                    
    //                 }
    //                 else if(Auth::user()->user_type == "zonal_manager" ||  $userType == "zsm_approval")
    //                 {
    //                     $store = MaterialRequestDetail::where('mr_details_id', $mid)->update([
    //                         'zsm_approvaldate' =>  Date::createFromFormat('d/m/Y', $request->approval_date)->format('Y-m-d'),             
    //                         'zsm_user_id' => Auth::user()->id,
    //                         'approval_type_id_fix' => $aprovId ,
    //                         'zsm_created_on' =>  Carbon::now('Asia/Kolkata')->toDateTimeString(),
    //                     ]);    
    //                 }
    //                 else if(Auth::user()->user_type == "director" ||  $userType == "md_approval")
    //                 {
    //                     $store = MaterialRequestDetail::where('mr_details_id', $mid)->update([
    //                         'md_approvaldate' =>  Date::createFromFormat('d/m/Y', $request->approval_date)->format('Y-m-d'),             
    //                         'md_user_id' => Auth::user()->id,
    //                         'approval_type_id_fix' => $aprovId ,
    //                         'md_created_on' =>  Carbon::now('Asia/Kolkata')->toDateTimeString(),
    //                     ]);    
    //                 }
    //             }
                
    //             if($store != "")
    //             {
    //                 return response()->json([
    //                     'response_code' => 1,
    //                     'response_message' => 'Record Inserted Successfully.',
    //                 ]);
    //             }else{
    //                 return response()->json([
    //                     'response_code' => 0,
    //                     'response_message' => 'Error Occured Record Not Inserted',
    //                 ]);
    //             }
    //         }
           
    //     }catch(\Exception $e)
    //     {            
    //         DB::rollBack();
          
    //         return response()->json([
    //             'response_code' => '0',
    //             'response_message' => 'Error Occured Record Not Inserted',
    //             'original_error' => $e->getMessage()
    //         ]);
    //     }
    // }

    public function destroy(Request $request){
        DB::beginTransaction();
        try{
            if ($request->PageName == 'manage-sm_approval') {
                
                $sm_data = MaterialRequest::where('mr_id', $request->id)->update([
                    'sm_approvaldate' => null,
                    'sm_user_id' => null,
                    'sm_created_on' => null,
                    'approval_type_id_fix' => 0
                ]);
                
            } else if ($request->PageName == 'manage-state_coordinator_approval') {
            
                $state_coordinate_data = MaterialRequest::where('mr_id', $request->id)->update([
                    'state_coordinator_approvaldate' => null,
                    'state_coordinator_user_id' => null,
                    'state_coordinator_created_on' => null,
                    'approval_type_id_fix' => 2
                ]);
            
        
            } else if ($request->PageName == 'manage-zsm_approval') {

                $zsm_data = MaterialRequest::where('mr_id', $request->id)->update([
                    'zsm_approvaldate' => null,
                    'zsm_user_id' => null,
                    'zsm_created_on' => null,
                    'approval_type_id_fix' => 1
                ]);
            
        
            } else if ($request->PageName == 'manage-gm_approval') {

                $md_data = MaterialRequest::where('mr_id', $request->id)->update([
                    'gm_approvaldate' => null,
                    'gm_user_id' => null,
                    'gm_created_on' => null,
                    'approval_type_id_fix' => 3
                ]);
            }
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This Is Used Somewhere, You Can't Delete";
            }else{
                $error_msg = "Record Not Deleted";
            }
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }

    }
    public function getMaterialDetails(Request $request){      

        $location = getCurrentLocation();

        $mrDetailData = MaterialRequestDetail::select(['material_request_details.mr_details_id','items.item_name', 'items.item_code','items.secondary_unit','units.unit_name', 'material_request_details.remarks','material_request_details.mr_qty','material_request_details.form_type','items.id as item_id',
        DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE items.id = location_stock.item_id AND location_stock.location_id = $location->id) as stock_qty"),
        ])
        ->leftJoin('items','items.id','material_request_details.item_id')
        ->leftJoin('units','units.id','items.unit_id')
        ->where('material_request_details.mr_id', $request->mr_id)->get();


        if($mrDetailData)
        {
            return response()->json([               
                'mrDetailData' => $mrDetailData, 
                'response_code' => 1,               
            ]);
        }else{
            return response()->json([               
                'response_code' => 0,
                'response_message' => "Material Not Found",
            ]);
        }

    }

    public function destroyMrDetail(Request $request){
            
        DB::beginTransaction();
        try{
           
            $mr_data =  MaterialRequestDetail::where('mr_details_id',$request->mr_detail_id)->first();

            if($mr_data){
                $mr_id = $mr_data->mr_id;
            }
            MaterialRequestDetail::where('mr_details_id',$request->mr_detail_id)->delete();
    
            if($mr_id != ''){
                $mr_partData = MaterialRequestDetail::where('mr_id',$mr_id)->get();

                if($mr_partData->count() == 0){
                    MaterialRequest::where('mr_id',$mr_id)->delete();
                }
            }
    
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
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