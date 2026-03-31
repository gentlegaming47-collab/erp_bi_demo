<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Setting;
use App\Models\UserLocation;
use App\Models\Location;
use App\Models\File;
use App\Models\UserAccess;
use App\Models\Menus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DataTables;
use Date;

use App\Models\Unit;
use App\Models\ReplacementItemDecision;
use App\Models\CustomerReplacementEntry;
use App\Models\DispatchPlan;
use App\Models\GRNMaterial;
use App\Models\ItemIssue;
use App\Models\ItemProduction;
use App\Models\ItemReturn;
use App\Models\LoadingEntry;
use App\Models\MaterialRequest;
use App\Models\PurchaseOrder;
use App\Models\SOShortClose;
use App\Models\SalesOrder;
use App\Models\SupplierRejection;
use App\Models\ItemAssemblyProduction;
use App\Models\LocationStock;
use App\Models\LocationCustomerGroupMapping;
use App\Models\POShortClose;
use App\Models\SOMapping;
use App\Models\CompanyYear;
use App\Models\Country;
use App\Models\CustomerGroup;
use App\Models\Dealer;
use App\Models\City;
use App\Models\HsnCode;
use App\Models\Item;
use App\Models\ItemGroup;
use App\Models\ItemRawMaterialMappingDetail;
use App\Models\PriceList;
use App\Models\PRShortClose;
use App\Models\PurchaseRequisition;
use App\Models\QCApproval;
use App\Models\Supplier;
use App\Models\State;
use App\Models\SupplierItemMapping;
use App\Models\Taluka;
use App\Models\Transporter;
use App\Models\Village;

class AdminController extends Controller
{

    /**
     * Return all users data without filter
     */
    public function userData()
    {  
        $users = Admin::where('id','!=','1')->get();
        if($users){
            return response()->json([
                'users' => $users,
                'response_code' => '1'
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'No Data Avilable',
            ]);
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function manage()
    {
        return view('manage.manage-user');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Admin $user,Request $request,DataTables $dataTables)
    {
        $users_data = Admin::select(['admin.id','admin.user_name','admin.user_type','admin.email_id','admin.created_on','admin.created_by_user_id','admin.last_by_user_id','admin.last_on', 'admin.person_name', 'admin.mobile_no','admin.status','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'admin.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'admin.last_by_user_id');
    
        return DataTables::of($users_data)
        ->editColumn('created_by_user_id', function($users_data){ 
            if($users_data->created_by_user_id != null){
                // $created_by_user_id= Admin::where('id','=',$users_data->created_by_user_id)->first('user_name'); 
                $created_by_user_id = Admin::where('id','=',$users_data->created_by_user_id)->first(); 
                return !empty($created_by_user_id) ?  $created_by_user_id->user_name :'' ;
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($users_data){ 
            if($users_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$users_data->last_by_user_id)->first('user_name'); 
                // return $last_by_user_id->user_name;
                return !empty($last_by_user_id) ?  $last_by_user_id->user_name :'' ;
            }else{
                return '';
            }
        
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('user_name', function($users_data){ 
            if($users_data->user_name != ''){
                $user_name = ucfirst($users_data->user_name);
                return $user_name;
            }else{
                return '';
            }
            //return Str::limit($users_data->user_name, 50);
        })
        ->editColumn('user_type', function($users_data){ 
            if($users_data->user_type != ''){
                if($users_data->user_type == 'state_coordinator'){
                    $user_type = 'State Coordinator';
                }else if($users_data->user_type == 'zonal_manager'){
                    $user_type = 'Zonal Manager';
                }else if($users_data->user_type == 'state_manager'){
                    $user_type = 'State Manager';
                }else if($users_data->user_type == 'general_manager'){
                    $user_type = 'General Manager';
                }else{

                    $user_type = ucfirst($users_data->user_type);
                }
                return $user_type;
            }else{
                return '';
            }
        })
       ->filterColumn('user_type', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('admin.user_type', 'like', '%' . $keyword . '%')
                ->orWhere('admin.user_type', 'like', '%' . str_replace(' ', '_', strtolower($keyword)) . '%');
            });
        })
        
        ->editColumn('created_on', function($users_data){ 
            if ($users_data->created_on != null) { 
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $users_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 
            }else{
                return '';
            }
        })
        ->filterColumn('admin.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(admin.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($users_data){ 
            if ($users_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $users_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->filterColumn('admin.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(admin.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($users_data){ 
            
            $action = "<div>";
            
            if($users_data->id == "1"){
                if(Auth::user()->id == "1"){
                    $action .= "<a id='edit_a' href='".route('edit-user',['id' => base64_encode($users_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            }else{
                if(hasAccess("user","edit")){
                    $action .= "<a id='edit_a' href='".route('edit-user',['id' => base64_encode($users_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            }
            if($users_data->id != "1"){
                if(hasAccess("user","delete")){
                    $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            }

            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','user_name','options','user_type'])
        ->make(true);
    }

    /**
     * Display the specified user access resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function showUserAccess(Admin $admin)
    {
        $users = Admin::select('user_name','id')->where('id','!=',1)->get();
        $user2 = Admin::select('user_name','id')->where('id','!=',0)->get();
        // $users = Admin::select('user_name','id')->where('id','!=',1)->where('user_type','=','operator')->get();
        // $user2 = Admin::select('user_name','id')->where('id','!=',0)->where('user_type','=','operator')->get();
        return view('edit.edit-user_access')->with(['users' => $users,'user2' =>$user2]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {  
        // $units = Location::all();
        $units = Location::select(['locations.id','locations.location_name','locations.type','states.state_name','districts.district_name','talukas.taluka_name','villages.village_name'])
        ->leftJoin('villages','villages.id','=','locations.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->where('locations.status','=','active')->get();

        return view('add.add-user')->with(['units' => $units]);
        // return view('add.add-user');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      
        $setting = Setting::select('total_active_users')->first();
        
        if($setting != null && ($setting->total_active_users != "0" || $setting->total_active_users != "")){
         
            $activeLimit = $setting->total_active_users;
        
            $totalActiveUsers = Admin::select('id')->where('status','=','1')->count();
            
            if($totalActiveUsers == $activeLimit && $request->status == "1"){
                 return response()->json([
                    'response_code' => '0',
                    'response_message' => 'No. of Active Users already exceed '.$activeLimit.'.',
                ]);
            }
        }
        
        $validated = $request->validate([
            'user_name'=>'required|max:500|unique:admin',         
        ],
        [
            'user_name.required' => 'Please Enter User Name',     
        ]);
     
        $allow_multiple_veh_entry = isset($request->allow_multiple_veh_entry) ? 'Yes' : 'No';

        DB::beginTransaction();
        try{

        $user_data=  Admin::create([
            'user_name' => $request->user_name,
            'password' => $request->password != "" ? Hash::make($request->password) : "",
            'person_name' => $request->person_name,
            'mobile_no' => $request->mobile_no,
            'email_id' => $request->email,
            'user_type' => $request->user_type,
            'status' => $request->status,
            'allow_multiple_veh_entry' => $allow_multiple_veh_entry,
            'company_id' => 1,
            'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'created_by_user_id' => Auth::user()->id
        ]);
        if($user_data->save()){

            if(isset($request->unit_ids) && !empty($request->unit_ids)){
                foreach($request->unit_ids as $ctKey => $ctVal){
                    
                    if($ctVal != null){
                        $rt_camera_detail_data=  UserLocation::create([
                            'user_id' => $user_data->id,
                            'company_unit_id' => $ctVal,
                            'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                            'created_by' => Auth::user()->id
                        ]);
                    }
                }
            }

            if($request->user_type == 'director'){
                $page_id  = Menus::select('id')->where('page','=','approval_status')->first();
           

                for ($x = 1; $x <= 2; $x++) {
                    $user_access_data = UserAccess::create([
                        'user_id' =>$user_data->id,
                        'page' => $page_id->id,
                        'action' => $x,
                    ])->save();
                }
            }

            // if($request->user_type != 'operator'  && $request->user_type != ''){

            //     if($request->user_type == 'state_manager'){
            //         $page_id  = Menus::select('id')->where('page','=','sm_approval')->first();
                    
            //     }else if ($request->user_type == 'zonal_manager'){
            //         $page_id  = Menus::select('id')->where('page','=','zsm_approval')->first();

            //     }else if ($request->user_type == 'director'){
            //         $page_id  = Menus::select('id')->where('page','=','md_approval')->first();
            //     }

            //     for ($x = 1; $x <= 4; $x++) {
            //         $user_access_data = UserAccess::create([
            //             'user_id' =>$user_data->id,
            //             'page' => $page_id->id,
            //             'action' => $x,
            //         ])->save();
            //     }

            //     if ($request->user_type == 'director'){
            //         $page_id  = Menus::select('id')->where('page','=','po_approval')->first();

            //         for ($x = 1; $x <= 4; $x++) {
            //             $user_access_data = UserAccess::create([
            //                 'user_id' =>$user_data->id,
            //                 'page' => $page_id->id,
            //                 'action' => $x,
            //             ])->save();
            //         }
            //     }

            //     if ($request->user_type == 'director'){
            //         $page_id  = Menus::select('id')->where('page','=','grn_against_po_approval')->first();

            //         for ($x = 1; $x <= 4; $x++) {
            //             $user_access_data = UserAccess::create([
            //                 'user_id' =>$user_data->id,
            //                 'page' => $page_id->id,
            //                 'action' => $x,
            //             ])->save();
            //         }
            //     }

            //     if ($request->user_type == 'director'){
            //         $page_id  = Menus::select('id')->where('page','=','po_vs_excess_grn')->first();

            //         for ($x = 1; $x <= 4; $x++) {
            //             $user_access_data = UserAccess::create([
            //                 'user_id' =>$user_data->id,
            //                 'page' => $page_id->id,
            //                 'action' => $x,
            //             ])->save();
            //         }
            //     }


            //     if($request->user_type == 'state_manager'){
            //         $page_id  = Menus::select('id')->where('page','=','sm_approval_report')->first();
                    
            //     }else if ($request->user_type == 'zonal_manager'){
            //         $page_id  = Menus::select('id')->where('page','=','zsm_approval_report')->first();

            //     }else if ($request->user_type == 'director'){
            //         $page_id  = Menus::select('id')->where('page','=','md_approval_report')->first();
            //     }

               
            //     for ($x = 1; $x <= 4; $x++) {
            //         $user_access_data = UserAccess::create([
            //             'user_id' =>$user_data->id,
            //             'page' => $page_id->id,
            //             'action' => $x,
            //         ])->save();
            //     }

               

            // }
         
            DB::commit();

            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Inserted Successfully.',
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Not Inserted',
            ]);
        }

    }catch(\Exception $e){      

        DB::rollBack(); 
        return response()->json([
            'response_code' => '0',
            'response_message' => 'Record Not Inserted',
        ]);
    }
    } 

    /**
     * check password of current user
     */
    
    public function check(Request $request){
        $validated = $request->validate([
            'password' => 'required'  
        ],
        [
            'password.required' => 'Please enter Password'
        ]);

        $admin_data = Admin::where('id','=',Auth::user()->id)->first('password');
        if($admin_data != null){
            if(Hash::check($request->password, $admin_data->password)){
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Authenticated Successfully!',
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'password does not match',
                ]);
            }
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'password does not match',
            ]);
        }
    }

    /**
     * Change Pasword
     */
    public function changePassword(Request $request){
        $validate = $request->validate([
            'old_password'=>'required|max:12',
            'new_password'=>'required|max:12',
            'confirm_password' => 'required|same:new_password'
        ],
        [
            'new_password.max' => 'Maximum 12 characters allowed for new password',
            'old_password.max' => 'Maximum 12 characters allowed for old password',
            'confirm_password.same' => 'Confirm Password Does not match with new password',
            'old_password.required' => 'Please enter old password',
            'new_password.required' => 'Please enter new password',
            'confirm_password.required' => 'Please enter confirm password',
        ]);

        $adminData = Admin::select('password')->where('id','=',$request->id)->where('password','=',Hash::make($request->old_password))->first();
        if($adminData != null){

            $user = Admin::where('id','=',$request->id)->update([
                'password' => Hash::make($request->new_password),
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id
            ]);
    
            if($user){
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Password Updated',
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Password Not Updated',
                ]);
            }

        }else{

            $resp = [ "message" => "The value for fields are wrong",
                        "errors" => [      
                            "old_password.notmatch" => [
                                "Old Password Does not match."
                            ]
                        ]
                    ];
            return response(json_encode($resp),422);
        }
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Material  $Material
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $user,$id)
    {   
        $user = UserLocation::select(['user_locations.company_unit_id'])->where('user_id','=',base64_decode($id))->get();
        

        
        $units = Location::select(['locations.id','locations.location_name','locations.type','states.state_name','districts.district_name','talukas.taluka_name','villages.village_name'])           
        ->leftJoin('villages','villages.id','=','locations.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->where('locations.status','=','active')
        ->orWhereIn('locations.id',$user)
        ->get();


    
        return view('edit.edit-user')->with(['id' => $id,'units' => $units]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin,$id)
    {
        $admin_data = Admin::where('id','=',$id)->first();

        $admin_data->file_path = asset('storage').'/';

        $user_units = UserLocation::where('user_id','=',$id)->get();

        
        if($admin_data){

            return response()->json([

                'user' => $admin_data,              

                'user_units' => $user_units,

                'response_code' => '1',

                'response_message' => '',
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        
        $setting = Setting::select('total_active_users')->first();



        // // this code if user type change 

        //     $user_type = Admin::where('id','=',$request->id)->where('user_type','=',$request->user_type)->first();

        //     if($user_type == null){

        //         $user = Admin::where('id','=',$request->id)->first();

        //         $page_id = '';

        //         if($user->user_type == 'state_manager'){
        //             $page_id  = Menus::select('id')->where('page','=','sm_approval')->first();
                    
        //         }else if ($user->user_type == 'zonal_manager'){
        //             $page_id  = Menus::select('id')->where('page','=','zsm_approval')->first();

        //         }else if ($user->user_type == 'director'){
        //             $page_id  = Menus::select('id')->where('page','=','md_approval')->first();
        //         }
            
        //         if($page_id != ''){
        //         UserAccess::where('user_id','=',$request->id)->where('page',$page_id->id)->delete();
        //         }


        //         if($request->user_type == 'operator'){
        //             $userAccesspage  = Menus::select(['id'])->where('page','=','sm_approval')->orWhere('page','=','zsm_approval')->orWhere('page','=','md_approval')
        //             ->get();

        //             foreach($userAccesspage as $key=>$val){
        //                 UserAccess::where('user_id','=',$request->id)->where('page',$val)->delete();
        //             }
                    
        //         }

              

        //     }

        //     if($request->user_type == 'state_manager' || $request->user_type == 'zonal_manager'){
        //         $userAccesspage  = Menus::select(['id'])->where('page','=','po_approval')->orWhere('page','=','grn_against_po_approval')
        //         ->get();

        //         foreach($userAccesspage as $key=>$val){
        //             UserAccess::where('user_id','=',$request->id)->where('page',$val['id'])->delete();
        //         }
                
        //     }
        //     UserAccess::where('user_id','=',$request->id)->delete();
        //     // if($request->user_type == 'operator' ){
        //     //     $userAccesspage  = Menus::select(['id'])->where('page','=','sm_approval')->orWhere('page','=','zsm_approval')->orWhere('page','=','md_approval')->orWhere('page','=','po_approval')->orWhere('page','=','grn_against_po_approval')->orWhere('page','=','sm_approval_report')->orWhere('page','=','zsm_approval_report')->orWhere('page','=','md_approval_report')
        //     //     ->get();

        //     //     foreach($userAccesspage as $key=>$val){
        //     //         UserAccess::where('user_id','=',$request->id)->where('page',$val['id'])->delete();
        //     //     }
                
        //     // }
        // // end this code

      

        if($setting != null && ($setting->total_active_users != "0" || $setting->total_active_users != "")){

            

            $activeLimit = $setting->total_active_users;

            

            $totalActiveUsers = Admin::select('id')->where('status','=','1')->where('id','!=',$request->id)->count();

            if($totalActiveUsers == $activeLimit && $request->status == "1"){

                 return response()->json([

                    'response_code' => '0',

                    'response_message' => 'No. of Active Users already exceed '.$activeLimit.'.',

                ]);

            }

        }

        

        $validated = $request->validate([

            'user_name'=> ['required','max:500',Rule::unique('admin')->ignore($request->id, 'id')],

    
        ],

        [

            'user_name.required' => 'Please enter user name',

        ]);



        $status = isset($request->status) ? $request->status : '0';
        $allow_multiple_veh_entry = isset($request->allow_multiple_veh_entry) ? 'Yes' : 'No';



        $update_data = [
            'user_name' => $request->user_name,
            // 'password' => $request->password != "" ? Hash::make($request->password) : "",
            'person_name' => $request->person_name,
            'mobile_no' => $request->mobile_no,
            'email_id' => $request->email,
            'user_type' => $request->user_type,
            'status' => $request->status,
            'allow_multiple_veh_entry' => $allow_multiple_veh_entry,
            'company_id' => 1,
            'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'last_by_user_id' => Auth::user()->id

        ];



        if(isset($request->password) && $request->password != ""){
            
            $update_data['password'] = Hash::make($request->password);

        }



        $user = Admin::where('id','=',$request->id)->update($update_data);



        if($user){


            // if($request->user_type != 'operator'  && $request->user_type != ''){

            //     // This code use to by default create approval entry
                
            //         if($request->user_type == 'state_manager'){
            //             $page_id  = Menus::select('id')->where('page','=','sm_approval')->first();
                        
            //         }else if ($request->user_type == 'zonal_manager'){
            //             $page_id  = Menus::select('id')->where('page','=','zsm_approval')->first();

            //         }else if ($request->user_type == 'director'){
            //             $page_id  = Menus::select('id')->where('page','=','md_approval')->first();
            //         }


            //         for ($x = 1; $x <= 4; $x++) {
            //             $user_access_data = UserAccess::create([
            //                 'user_id' =>$request->id,
            //                 'page' => $page_id->id,
            //                 'action' => $x,
            //             ])->save();
            //         }

            //         if ($request->user_type == 'director'){
            //             $page_id  = Menus::select('id')->where('page','=','po_approval')->first();

            //             for ($x = 1; $x <= 4; $x++) {
            //                 $user_access_data = UserAccess::create([
            //                     'user_id' =>$request->id,
            //                     'page' => $page_id->id,
            //                     'action' => $x,
            //                 ])->save();
            //             }
            //         }

            //         if ($request->user_type == 'director'){
            //             $page_id  = Menus::select('id')->where('page','=','grn_against_po_approval')->first();

            //             for ($x = 1; $x <= 4; $x++) {
            //                 $user_access_data = UserAccess::create([
            //                     'user_id' =>$request->id,
            //                     'page' => $page_id->id,
            //                     'action' => $x,
            //                 ])->save();
            //             }
            //         }

            //         if ($request->user_type == 'director'){
            //             $page_id  = Menus::select('id')->where('page','=','po_vs_excess_grn')->first();
    
            //             for ($x = 1; $x <= 4; $x++) {
            //                 $user_access_data = UserAccess::create([
            //                     'user_id' =>$request->id,
            //                     'page' => $page_id->id,
            //                     'action' => $x,
            //                 ])->save();
            //             }
            //         }

            //         if($request->user_type == 'state_manager'){
            //             $page_id  = Menus::select('id')->where('page','=','sm_approval_report')->first();
                        
            //         }else if ($request->user_type == 'zonal_manager'){
            //             $page_id  = Menus::select('id')->where('page','=','zsm_approval_report')->first();

            //         }else if ($request->user_type == 'director'){
            //             $page_id  = Menus::select('id')->where('page','=','md_approval_report')->first();
            //         }


            //         for ($x = 1; $x <= 4; $x++) {
            //             $user_access_data = UserAccess::create([
            //                 'user_id' =>$request->id,
            //                 'page' => $page_id->id,
            //                 'action' => $x,
            //             ])->save();
            //         }

            //     // end this code
            // }

            $user_type = Admin::where('id','=',$request->id)->pluck('user_type')->first();

            if($user_type == 'operator'){
                $userAccesspage  = Menus::select(['id'])->where('page','=','sm_approval')->orWhere('page','=','zsm_approval')->orWhere('page','=','md_approval')->orWhere('page','=','po_approval')->orWhere('page','=','grn_against_po_approval')->orWhere('page','=','sm_approval_report')->orWhere('page','=','zsm_approval_report')->orWhere('page','=','md_approval_report')->orWhere('page','=','po_vs_excess_grn')->orWhere('page','=','expire_dealer_report')->get();
    
                foreach($userAccesspage as $key=>$val){
                    UserAccess::where('user_id','=',$request->id)->where('page',$val['id'])->delete();
                }
            }


            $oldUserUnit = UserLocation::where('user_id','=',$request->id)->get();

            $oldUserUnitData = [];

            if($oldUserUnit != null){

                $oldUserUnitData = $oldUserUnit->toArray();

            }



            $UserUnits = $request->only('unit_ids');





                if(isset($oldUserUnitData) && !empty($oldUserUnitData)){



                    foreach($oldUserUnitData as $oldCtKey => $oldCtVal){



                        if(isset($UserUnits['unit_ids'][$oldCtKey]) && $UserUnits['unit_ids'][$oldCtKey] != null){

                            $contact_data_updated =  UserLocation::where('user_id','=',$request->id)->where('id','=',$oldCtVal['id'])->update([

                                'company_unit_id' => $UserUnits['unit_ids'][$oldCtKey],

                                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),

                                'last_by' => Auth::user()->id

                            ]);



                            unset($oldUserUnitData[$oldCtKey]); //remove element from array after use it's key

                            unset($UserUnits['unit_ids'][$oldCtKey]); //remove element from array after use it's key

                        }



                    }



                    if(isset($oldUserUnitData) && !empty($oldUserUnitData)){

                        foreach($oldUserUnitData as $oldCtKey => $oldCtVal){

                            UserLocation::where('id','=',$oldCtVal['id'])->delete();

                        }

                    }



                }



                if(isset($UserUnits['unit_ids']) && !empty($UserUnits['unit_ids'])){



                    foreach($UserUnits['unit_ids'] as $ctKey => $ctVal){

                        if($ctVal != null){

                            $rt_camera_detail_data=  UserLocation::create([

                                'user_id' => $request->id,

                                'company_unit_id' => $ctVal,

                                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),

                                'created_by' => Auth::user()->id

                            ]);

                        }

                    }

                }

        



            if(isset($oldUserUnitData) && !empty($oldUserUnitData)){

                foreach($oldUserUnitData as $oldCtKey => $oldCtVal){

                    UserLocation::where('id','=',$oldCtVal['id'])->delete();

                }

            }
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Updated Successfully.',
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Not Updated',
            ]);
        }
    }

    public function existsDesignation(Request $request){
        if($request->term != ""){
            $fdDesignation = Admin::select('user_code')->where('user_code', 'LIKE', $request->term.'%')->groupBy('user_code')->get();
            if($fdDesignation != null){
                // $output = [];

                // foreach($fdDesignation as $dsKey){
                //     array_push($output ,$dsKey->user_code);
                // } 
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdDesignation as $dsKey){

                    $output .= '<li parent-id="user_code" list-id="user_code_list" class="list-group-item" tabindex="0">'.$dsKey->user_code.'</li>';
                } 
                $output .= '</ul>';

                return response()->json([
                    'user_codeList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Designation available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'user_codeList' => '',
                'response_code' => 1,
            ]);
        }
       
    }

    public function existsUsername(Request $request){
        if($request->term != ""){
            $fdUsername = Admin::select('user_name')->where('user_name', 'LIKE', $request->term.'%')->groupBy('user_name')->get();
            if($fdUsername != null){
                // $output = [];

                // foreach($fdUsername as $usKey){
                //     array_push($output ,$usKey->user_name);
                // } 
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdUsername as $usKey){

                    $output .= '<li parent-id="user_name" list-id="user_name_list" class="list-group-item" tabindex="0">'.$usKey->user_name.'</li>';
                } 
                $output .= '</ul>';

                return response()->json([
                    'usernameList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Username available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'usernameList' => '',
                'response_code' => 1,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Admin $admin)
    {

        if($request->id == "1"){
            return response()->json([
                'response_code' => '0',
                'response_message' => 'You Can\'t Delete Admin Record.',
            ]);
        }else if($request->id == Auth::user()->id){
            return response()->json([
                'response_code' => '0',
                'response_message' => 'You Can\'t Delete Current Logged In User.',
            ]);
        }
        
      
        
        DB::beginTransaction();
        try{

            $src_user = SupplierRejection::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($src_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Supplier Return Challan.",
                ]);
            }                   
            $src_user = QCApproval::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($src_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In QC Approval.",
                ]);
            }                   

            $item_issue_user = ItemIssue::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($item_issue_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Item Issue Slip.",
                ]);
            }

            $item_assm_user = ItemAssemblyProduction::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($item_assm_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Item Assembly Production.",
                ]);
            }
            
            $grn_current_user = GRNMaterial::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($grn_current_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In GRN.",
                ]);
            }
            
            $loading_entry_user = LoadingEntry::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($loading_entry_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Loading Entry.",
                ]);
            }

            $diapatch_plan_user = DispatchPlan::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($diapatch_plan_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Dispatch Plan.",
                ]);
            }

            $rep_user = ReplacementItemDecision::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($rep_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Replacement Item Decision.",
                ]);
            }

            $so_mapping_user = SOMapping::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($so_mapping_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Customer Replacement SO Mapping.",
                ]);
            }

            $so_short_user = SOShortClose::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($so_short_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Customer Replacement SO Short Close.",
                ]);
            }

            
            $cus_rep_user = CustomerReplacementEntry::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($cus_rep_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Customer Replacement Entry.",
                ]);
            }

        
            $item_return_user = ItemReturn::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($item_return_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Item Return Slip.",
                ]);
            }

            $item_production_user = ItemProduction::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($item_production_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Item Production.",
                ]);
            }

           
            $so_user = SalesOrder::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($so_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Sales Order.",
                ]);
            }
       
          
            $mr_user = MaterialRequest::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($mr_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Material Request.",
                ]);
            }

            $po_short_user = POShortClose::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($po_short_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In PO Short Close.",
                ]);
            }
            
            $po_user = PurchaseOrder::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($po_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Purchase Order.",
                ]);
            }
            $po_user = PRShortClose::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($po_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Purchase Requisition Short Close.",
                ]);
            }
            $po_user = PurchaseRequisition::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($po_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Purchase Requisition.",
                ]);
            }
            

            $company_user = CompanyYear::where('created_by',$request->id)->orWhere('last_by',$request->id)->get();
            if($company_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Company Year.",
                ]);
            }


            $location_user = Location::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($location_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Location.",
                ]);
            }
            
        

            $customer_mapping_user = LocationCustomerGroupMapping::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($customer_mapping_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Location to Customer Group Mapping.",
                ]);
            }

            $supplier_mapping_user = SupplierItemMapping::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($supplier_mapping_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Supplier Item Mapping.",
                ]);
            }

            $price_list_user = PriceList::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($price_list_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Price List.",
                ]);
            }

            $item_raw_user = ItemRawMaterialMappingDetail::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($item_raw_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Item to Item Mapping.",
                ]);
            }
            
            $item_user = Item::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($item_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Item.",
                ]);
            }

            $item_group_user = ItemGroup::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($item_group_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Item Group.",
                ]);
            }
            
            $unit = Unit::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($unit ->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Unit.",
                ]);
            }


            $hsn_user = HsnCode::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($hsn_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In HSN Code.",
                ]);
            }

            $transporter = Transporter::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($transporter ->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Transporter.",
                ]);
            }

          
            $supplier_user = Supplier::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($supplier_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Supplier.",
                ]);
            }
            
            $customer_group_user = CustomerGroup::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($customer_group_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Customer Group.",
                ]);
            }

            $dealer_user = Dealer::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($dealer_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Dealer.",
                ]);
            }

            $village = Village::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($village ->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Village.",
                ]);
            }

       
            $taluka = Taluka::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($taluka->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Taluka.",
                ]);
            }

            $districts_user = City::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($districts_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In District.",
                ]);
            }
       
            $states_user = State::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($states_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In State.",
                ]);
            }

            $country_user = Country::where('created_by_user_id',$request->id)->orWhere('last_by_user_id',$request->id)->get();
            if($country_user->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, User Is Used In Country.",
                ]);
            }
        

                 
            Admin::destroy($request->id);
            UserAccess::where('user_id','=',$request->id)->delete();
            UserLocation::where('user_id', $request->id)->delete();
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
}