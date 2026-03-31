<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserAccessController;
use Session;
class AuthUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $defineActionsWeb = ["add","edit","manage","print","export"];

        $defineActionsAjax = ["add","edit","manage","delete"];

        if ($request->ajax()) {

            $path_infos = explode('-',$request->route()->getName());
            if(count($path_infos) > 1){
                $action = $path_infos[0];
                $page = $path_infos[1];
    
                if($action == "remove"){
                    $action = "delete";
                }

                if($action == "update"){
                    $action = "edit";
                }

                if($action == "listing"){
                    $action = "manage";
                }

                if($action == "store"){
                    $action = "add";
                }

                if(!in_array($action,$defineActionsAjax) || $page == "company" || $page == "approval_request" || $page == "approval" || $page == "po_approval_request" || $page == "po_approval_grn_request" || $page == "approval_report" || $page == "pending_so_for_dispatch_report" || $page == "pending_dispatch_plan_for_loading_entry_summary" || $page = "loading_entry_summary"){
                    return $next($request);
                }
                
                // if(!in_array($action,$defineActionsAjax) || $page == "company"){
                //     return $next($request);
                // }

                if(!UserAccessController::checkUserAccess($request,Auth::user()->id,$page,$action)){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'You Have No Rights To Perform This Action'
                    ], 200);
                }
            }
            return $next($request);

        }else{
            $path_infos = explode('-',$request->route()->getName());

            // add the location
            $getLocation =  Session::get('getLocationId');

            if(count($path_infos) > 1){ 
                
                if(( $getLocation != '' &&  $getLocation != 0 &&  $getLocation != null))
                    {    
                        $action = $path_infos[0];
                        $page = $path_infos[1];

                        
                    
                            
                        if(!in_array($action,$defineActionsWeb)){
                            return $next($request);
                        }
                        // check if location id not available then call

                        if(!UserAccessController::checkUserAccess($request,Auth::user()->id,$page,$action,  $getLocation)){
                            abort(401);
                        }
                    }
                    else{                           
                        // return redirect(route('selectLocation'));          
                        $checkUserType = Auth::user()->user_type;

                        if($checkUserType == "operator")
                           {                            
                               return redirect(route('selectLocation'));              
                           }else{
                               return $next($request);
                           }                    
                    }
                
            }else{     
                $page = $path_infos[0] == "/" ? "selectLocation" : $path_infos[0];
                $getLocation =  Session::get('getLocationId');
                
                // $page = $path_infos[0] == "/" ? "dashboard" : $path_infos[0];
                if($page != "selectLocation")
                {
                    // dd($page);
                    $page = $path_infos[0] == "getUserPremission" ? "dashboard" : $path_infos[0];
                    if(!UserAccessController::checkUserAccess($request,Auth::user()->id,$page,  $getLocation)){
                        abort(401);
                    }
                }
            }

            // if(count($path_infos) > 1){
            //     $action = $path_infos[0];
            //     $page = $path_infos[1];
    
            //     if(!in_array($action,$defineActionsWeb)){
            //         return $next($request);
            //     }
            //     if(!UserAccessController::checkUserAccess($request,Auth::user()->id,$page,$action)){
            //         abort(401);
            //     }
            // }else{
                
            //     $page = $path_infos[0] == "/" ? "dashboard" : $path_infos[0];
    
            //     if(!UserAccessController::checkUserAccess($request,Auth::user()->id,$page)){
            //         abort(401);
            //     }
            // }
            return $next($request);
        }
    }
}