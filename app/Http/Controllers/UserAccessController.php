<?php



namespace App\Http\Controllers;


use Session;

use App\Models\Menus;

use App\Models\Module;

use App\Models\Actions;

use App\Models\UserAccess;

use App\Models\Location;

use App\Models\Admin;

use Illuminate\Http\Response;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use DataTables;

use Date;



class UserAccessController extends Controller

{

    public function getUserPremissionData(Request $request)
    {
        
        $location_type = Location::select('type')->where('id',$request->user_location_id)->first();
        Session::put('getLocationId', $request->user_location_id);

        Session::put('getLocationType', $location_type->type);

        $request->validate(
            [
                'user_location_id' => 'required',            
            ],
            [
                'user_location_id.required' => 'Please Select Location',            
            ]
        );
                        
        return redirect()->route('dashboard');
    }

    public static function checkUserAccess(Request $request,$user_id,$page = null,$action = null,$getLocation = null)

    {
    
        if($user_id == "" || $user_id == null){

            return false;

        }else{

            if($user_id == "1")

            {

                return true;

            }
            if($page != null){
                

                
                

                if(is_array($page)){


                    $user_access_data = UserAccess::select('users_access.user_id','actions.action','menus.page')

                        ->leftJoin('menus', 'users_access.page', '=', 'menus.id')

                        ->leftJoin('actions', 'users_access.action', '=', 'actions.id')

                        ->where('users_access.user_id','=',$user_id)

                        ->whereIn('menus.page',$page)

                        ->where('actions.action','=',$action)

                        ->get();


                        

                        if (count($user_access_data) > 0)

                        {

                            return true;

                        }else{

                            return false;

                        }

                    // $copy_access_data=Admin::select('')    



                }else{

                    
                    if(($page == "dashboard") || ($page == "selectLocation")){
                        return true;
                    }

                    

                    if(is_array($action)){

                        

                        $user_access_data = UserAccess::select('users_access.user_id','actions.action','menus.page')

                        ->leftJoin('menus', 'users_access.page', '=', 'menus.id')

                        ->leftJoin('actions', 'users_access.action', '=', 'actions.id')

                        ->where('users_access.user_id','=',$user_id)

                        ->where('menus.page','=',$page)

                        ->whereIn('actions.action',$action)

                        ->get();

        

                        if (count($user_access_data) > 0){

                            return true;

                        }else{

                            return false;

                        }

                        

                    }else{

                        // dd($page);

                        if($action == null || $action == ""){

                            $user_access_data = UserAccess::where('users_access.user_id','=',$user_id)

                            // ->where('menus.page','=',$page)

                            ->select('users_access.user_id')

                            ->leftJoin('menus', 'users_access.page', '=', 'menus.id')

                            ->leftJoin('actions', 'users_access.action', '=', 'actions.id')

                            ->first();

                        }else if($page == 'farmer_dispatch_plan' || $page == "farmer_wise_total_dispatch_plan"){
                            $user_access_data = UserAccess::where('users_access.user_id','=',$user_id)
                            ->where('actions.action','=',$action)
                            ->select('users_access.user_id')
                            ->leftJoin('menus', 'users_access.page', '=', 'menus.id')
                            ->leftJoin('actions', 'users_access.action', '=', 'actions.id')
                            ->first();                
                        
                        }else if($page == 'sales_order_fitting'){
                            $user_access_data = UserAccess::where('users_access.user_id','=',$user_id)
                            ->where('actions.action','=',$action)
                            ->select('users_access.user_id')
                            ->leftJoin('menus', 'users_access.page', '=', 'menus.id')
                            ->leftJoin('actions', 'users_access.action', '=', 'actions.id')
                            ->first();                
                        
                        }else{
                            
                            $user_access_data = UserAccess::where('users_access.user_id','=',$user_id)

                            ->where('menus.page','=',$page)

                            ->where('actions.action','=',$action)

                            ->select('users_access.user_id')

                            ->leftJoin('menus', 'users_access.page', '=', 'menus.id')

                            ->leftJoin('actions', 'users_access.action', '=', 'actions.id')

                            ->first();

                        }



                        if(isset($user_access_data->user_id)){

                            return true;

                        }else{

                            return false;

                        }

                    }

                }

                

            }

            return false;

        }

    }
    // public static function checkUserAccess(Request $request,$user_id,$page = null,$action = null)

    // {

    //     if($user_id == "" || $user_id == null){

    //         return false;

    //     }else{

    //         if($user_id == "1")

    //         {

    //             return true;

    //         }

    //         if($page != null){



    //             if(is_array($page)){



    //                 $user_access_data = UserAccess::select('users_access.user_id','actions.action','menus.page')

    //                     ->leftJoin('menus', 'users_access.page', '=', 'menus.id')

    //                     ->leftJoin('actions', 'users_access.action', '=', 'actions.id')

    //                     ->where('users_access.user_id','=',$user_id)

    //                     ->whereIn('menus.page',$page)

    //                     ->where('actions.action','=',$action)

    //                     ->get();

        

    //                     if (count($user_access_data) > 0)

    //                     {

    //                         return true;

    //                     }else{

    //                         return false;

    //                     }

    //                 // $copy_access_data=Admin::select('')    



    //             }else{

                

    //                 if($page == "dashboard"){

    //                     return true;

    //                 }



    //                 if(is_array($action)){

                        

    //                     $user_access_data = UserAccess::select('users_access.user_id','actions.action','menus.page')

    //                     ->leftJoin('menus', 'users_access.page', '=', 'menus.id')

    //                     ->leftJoin('actions', 'users_access.action', '=', 'actions.id')

    //                     ->where('users_access.user_id','=',$user_id)

    //                     ->where('menus.page','=',$page)

    //                     ->whereIn('actions.action',$action)

    //                     ->get();

        

    //                     if (count($user_access_data) > 0){

    //                         return true;

    //                     }else{

    //                         return false;

    //                     }

                        

    //                 }else{



    //                     if($action == null || $action == ""){

    //                         $user_access_data = UserAccess::where('users_access.user_id','=',$user_id)

    //                         ->where('menus.page','=',$page)

    //                         ->select('users_access.user_id')

    //                         ->leftJoin('menus', 'users_access.page', '=', 'menus.id')

    //                         ->leftJoin('actions', 'users_access.action', '=', 'actions.id')

    //                         ->first();

    //                     }else{

    //                         $user_access_data = UserAccess::where('users_access.user_id','=',$user_id)

    //                         ->where('menus.page','=',$page)

    //                         ->where('actions.action','=',$action)

    //                         ->select('users_access.user_id')

    //                         ->leftJoin('menus', 'users_access.page', '=', 'menus.id')

    //                         ->leftJoin('actions', 'users_access.action', '=', 'actions.id')

    //                         ->first();

    //                     }



    //                     if(isset($user_access_data->user_id)){

    //                         return true;

    //                     }else{

    //                         return false;

    //                     }

    //                 }

    //             }

                

    //         }

    //         return false;

    //     }

    // }

    

    public function getUserAccess(Request $request,$forWeb = null)

    {   
        
        $user_details = Admin::where('id','=',$request->id)->first('user_name');
        
       

        // $userCode = "";



        if($user_details != null){

            $userCode = $user_details->user_name;

        }
        
        

        $user_access_data = UserAccess::where('users_access.user_id','=',$request->id)

        ->select('users_access.user_id','users_access.id','users_access.page','users_access.action','menus.page as page_name','actions.action as action_name')

        ->leftJoin('menus', 'users_access.page', '=', 'menus.id')

        ->leftJoin('actions', 'users_access.action', '=', 'actions.id')

        ->get();

        

        if($forWeb != null){

            return $user_access_data;

        }else{

            
            
            if($user_access_data != null){

                return response()->json([

                    'response_code' => '1',

                    'user_access_data' => $user_access_data,

                    'user_code' => $userCode

                ]);

            }else{

                return response()->json([

                    'response_code' => '0',

                    'response_message' => 'User access not found',

                ]);

            } 



        }

    }


    // public function getUserAccess(Request $request,$forWeb = null)
    // {   
    //     $user_details = Admin::where('id','=',$request->id)->first('user_code');
       
    //     $userCode = "";

    //     if($user_details != null){
    //         $userCode = $user_details->user_code;
    //     }
        
    //     $user_access_data = UserAccess::where('users_access.user_id','=',$request->id)
    //     ->select('users_access.user_id','users_access.id','users_access.page','users_access.action','menus.page as page_name','actions.action as action_name')
    //     ->leftJoin('menus', 'users_access.page', '=', 'menus.id')
    //     ->leftJoin('actions', 'users_access.action', '=', 'actions.id')
    //     ->get();

    //     if($forWeb != null){
    //         return $user_access_data;
    //     }else{

    //         if($user_access_data != null){
    //             return response()->json([
    //                 'response_code' => '1',
    //                 'user_access_data' => $user_access_data,
    //                 'user_name' => $user_name
    //             ]);
    //         }else{
    //             return response()->json([
    //                 'response_code' => '0',
    //                 'response_message' => 'User Access Not Found',
    //             ]);
    //         } 

    //     }
    // }


    public function setUserAccess(Request $request)

    {

        try{

            $access_data = UserAccess::where('user_id','=',$request->user_id)->get(['page','action']);

            $old_access = [];

            if(!empty($access_data) && $access_data != null){

                foreach ($access_data as $okey => $ovalue) {

                    $old_access[$ovalue->page][$ovalue->action] = $ovalue->action;

                }

            }

                foreach($request->pages as $pagKey => $pagVal){

    

                    if(isset($request->actions[$pagVal])){

    

                        foreach($request->actions[$pagVal] as $actKey => $actVal){

     
                         
                            foreach($old_access as $okey => $ovalue)

                            {

                                if(isset($old_access[$okey]))

                                {

                                    foreach($old_access[$okey] as $odkey => $odvalue)

                                    {

                                        if(isset($request->actions[$okey][$odvalue]))

                                        {

                                            $old_access[$okey][$odvalue] = 0; //remove element from array after use it's key

                                        }

                                    }

                                }

                            }

                            if(!isset($old_access[$pagVal][$actVal]))

                            {

                                $user_access_data = UserAccess::create([

                                    'user_id' => $request->user_id,

                                    'page' => $pagVal,

                                    'action' => $actVal

                                ])->save();

                            }  

                            

                        }

                    }else{

                        

                        if(isset($old_access[$pagVal])){

                            foreach($old_access[$pagVal] as $okey => $ovalue){

                                UserAccess::where(['user_id' => $request->user_id,'page' => $pagVal,'action' => $old_access[$pagVal][$ovalue]])->delete();

                            }

                        }

                    }

                }

    

                if(!empty($old_access)){

                    foreach($old_access as $okey => $ovalue){

                        if(isset($old_access[$okey])){

                            foreach($old_access[$okey] as $odkey => $odvalue){

                                UserAccess::where(['user_id' => $request->user_id,'page' => $okey,'action' => $odvalue])->delete();

                            }

                        }

                    }

                }

                //update user tabel

                Admin::where('id','=',$request->user_id)->update([

                    'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),

                    'last_by_user_id' => Auth::user()->id

                ]);

                $user_type = Admin::where('id','=',$request->user_id)->pluck('user_type')->first();

                if($user_type == 'operator'){
                    $userAccesspage  = Menus::select(['id'])->where('page','=','sm_approval')->orWhere('page','=','zsm_approval')->orWhere('page','=','md_approval')->orWhere('page','=','po_approval')->orWhere('page','=','grn_against_po_approval')->orWhere('page','=','sm_approval_report')->orWhere('page','=','zsm_approval_report')->orWhere('page','=','md_approval_report')->orWhere('page','=','po_vs_excess_grn')->orWhere('page','=','expire_dealer_report')->get();
                    
                    foreach($userAccesspage as $key=>$val){
                        UserAccess::where('user_id','=',$request->user_id)->where('page',$val['id'])->delete();
                    }
                }

                return response()->json([

                    'response_code' => '1',

                    'response_message' => 'Record Updated',

                ]);

        }catch(\Exception $e){

            return response()->json([

                'response_code' => '0',

                'response_message' => 'Record Not Updated',

                'original_message' => $e->getMessage()

            ]);

        }

    }



    public function getPages()

    {

        $pg_data = Module::orderBy('module_index','asc')->get();

        $pages   = Menus::orderBy('sequence','asc')->get();

        if($pages != null){

            foreach($pages as $key => $page){

                if($page->actions != "no" && $page->actions !="all"){

                    $page->actions = explode(',',$page->actions);

                }

            }

        }



        return response()->json([

            'response_code' => '1',

            'pages' => $pages,

            'parents' => $pg_data

        ]);

    }



    public function getActions()

    {

        return response()->json([

            'response_code' => '1',

            'actions' => Actions::where('show','=','YES')->orderBy('sequence','asc')->get(),

        ]);

    }



    public function getAccessModules(Request $request)

    {
        $user_type = Admin::where('id','=',$request->id)->first('user_type');

        if($user_type->user_type == 'operator'){
            $user = Admin::select('user_name','id')->where('user_type','=','operator')->where('id','!=',1)->where('id','!=',$request->id)->get();
        }else{
            $user = Admin::select('user_name','id')->where('id','!=',0)->get();
        }

        $pg_data = Module::orderBy('module_index','asc')->get();

        $pages = Menus::orderBy('sequence','asc')      
        ->get();
     

        return response()->json([

            'response_code' => '1',

            'actions' => Actions::where('show','=','YES')->orderBy('sequence','asc')->get(),

            'pages' => $pages,

            'parents' => $pg_data,

            'user_type' => $user_type,

            'user' => $user

        ]);

    }

}