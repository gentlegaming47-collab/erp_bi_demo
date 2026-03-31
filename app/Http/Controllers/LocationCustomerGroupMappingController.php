<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\LocationCustomerGroupMapping;
use App\Models\SalesOrder;
use App\Models\MaterialRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;

class LocationCustomerGroupMappingController extends Controller
{
    public function manage()
    {
        return view('manage.manage-location_customer_group_mappning');
    }

    public function index(Request $request,DataTables $dataTables)
    {
        $customerGroup = LocationCustomerGroupMapping::select([
            'location_to_customer_group_mapping.id','customer_groups.customer_group_name','locations.location_name','location_to_customer_group_mapping.location_id','location_to_customer_group_mapping.created_on','location_to_customer_group_mapping.created_by_user_id','location_to_customer_group_mapping.last_by_user_id','location_to_customer_group_mapping.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
            ->leftJoin('customer_groups','customer_groups.id','=','location_to_customer_group_mapping.customer_group_id')
            ->leftJoin('locations','locations.id','=','location_to_customer_group_mapping.location_id')
            ->leftJoin('admin AS created_user', 'created_user.id', '=', 'location_to_customer_group_mapping.created_by_user_id')
            ->leftJoin('admin AS last_user', 'last_user.id', '=', 'location_to_customer_group_mapping.last_by_user_id');

        
        return DataTables::of($customerGroup)
        ->editColumn('created_by_user_id', function($customerGroup){
            if($customerGroup->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$customerGroup->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($customerGroup){
            if($customerGroup->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$customerGroup->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('created_on', function($customerGroup){
            if ($customerGroup->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $customerGroup->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('location_to_customer_group_mapping.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(location_to_customer_group_mapping.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($customerGroup){
            if ($customerGroup->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $customerGroup->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('location_to_customer_group_mapping.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(location_to_customer_group_mapping.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })       
        ->addColumn('options',function($customerGroup){
            $action = "<div>";
            if(hasAccess("location_customer_group_mappning","edit")){
            $action .="<a id='edit_a' href='".route('edit-location_customer_group_mappning',['id' => base64_encode($customerGroup->location_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("location_customer_group_mappning","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options'])
        ->make(true);
    }

    public function create()
    {      

        $CustomerGroup = CustomerGroup::orderBy('customer_group_name', 'ASC')->get();
       

        return view('add.add-location_customer_group_mappning', compact('CustomerGroup'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id'=>'required'                 
        ],
        [
            'location_id.required' => 'Please Select Location '        
                       
        ]);
        DB::beginTransaction();

        try{    
           
            $locationData =  LocationCustomerGroupMapping::where('location_id',$request->location_id)->update([
                'status' => 'D',
            ]);
            
                
            $request->customer_group_data = json_decode($request->customer_group_data, true);
            foreach($request->customer_group_data as $ckey=>$cval){
               
                $locationData = LocationCustomerGroupMapping::where('location_id',$request->location_id)->where('customer_group_id',$cval['customer_group_id'])->get();
                
              
              
                if($locationData->isEmpty()){
                  
                    $storeData=  LocationCustomerGroupMapping::create([
                        'location_id' => $request->location_id,             
                        'customer_group_id' =>$cval['customer_group_id'],                       
                        'status' => 'Y',
                        'company_id' => Auth::user()->company_id,
                        'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                        'created_by_user_id' => Auth::user()->id,
                    ]);
    
                }else{
                    
                    $locationData = LocationCustomerGroupMapping::where('location_id',$request->location_id)->where('customer_group_id',$cval['customer_group_id'])->first();

                    if($locationData){
                        $storeData =  LocationCustomerGroupMapping::where('location_id',$request->location_id)->where('customer_group_id',$cval['customer_group_id'])->update([
                            'location_id' => $request->location_id,             
                            'customer_group_id' =>$cval['customer_group_id'],     
                            'status' => 'Y',
                            'company_id' => Auth::user()->company_id,
                            'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                            'last_by_user_id' => Auth::user()->id
                        ]);             
                    }else{
                        $storeData=  LocationCustomerGroupMapping::create([
                            'location_id' => $request->location_id,             
                            'customer_group_id' =>$cval['customer_group_id'],                       
                            'status' => 'Y',
                            'company_id' => Auth::user()->company_id,
                            'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                            'created_by_user_id' => Auth::user()->id,
                        ]);

                    }                            
                }
            }
            $item = LocationCustomerGroupMapping::where('location_id',$request->location_id)->where('status','D')->delete();
            DB::commit();
                     return response()->json([
                            'response_code' => '1',
                            'response_message' => 'Record Inserted Successfully.',
            ]);
                 
        }catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }


    public function show($id)
    {
       
        $CustomerGroup = CustomerGroup::orderBy('customer_group_name', 'ASC')->get();
        
        $getData = LocationCustomerGroupMapping::where('location_id', '=', base64_decode($id))->get();
        
    
        return view('edit.edit-location_customer_group_mappning', compact('CustomerGroup', 'getData', 'id'));
    }

    public function edit(Request $request, $id)
    {
        
        $location_to_customer_group_mapping = LocationCustomerGroupMapping::where('location_id','=',$id)->get();

        if($location_to_customer_group_mapping != null){
            $location_to_customer_group_mapping->each(function ($item) use (&$isAnyPartInUse) {      

                    $isFound = SalesOrder::where('sales_order.customer_group_id','=',$item->customer_group_id)
                    ->where('sales_order.current_location_id','=',$item->location_id)
                    ->first();   

                    $isMRFound = MaterialRequest::where('material_request.customer_group_id','=',$item->customer_group_id)
                    ->where('material_request.current_location_id','=',$item->location_id)
                    ->first();    
                    

                    if($isFound != null){
                        $item->in_use = true;                     
                      

                    }elseif($isMRFound != null){
                        $item->in_use = true;                     
                    
                    }else{
                        $item->in_use = false;                     
                    }
                  
                    return $item;

            })->values();
        }

       
        if($location_to_customer_group_mapping){
            return response()->json([
                'location_to_customer_group_mapping' => $location_to_customer_group_mapping,
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

    public function update(Request $request)
    {
        $validated = $request->validate([
            'location_id'=>'required'                 
        ],
        [
            'location_id.required' => 'Please Select Location '        
                       
        ]);
        DB::beginTransaction();
        try{

            $locationData =  LocationCustomerGroupMapping::where('location_id',$request->id)->update([
                'status' => 'D',
            ]);
            
              
            $request->customer_group_data = json_decode($request->customer_group_data, true);
            foreach($request->customer_group_data as $ckey=>$cval){
               
                $locationData = LocationCustomerGroupMapping::where('location_id',$request->location_id)->where('customer_group_id',$cval['customer_group_id'])->get();
                
              
              
                if(empty($locationData)){
                    $storeData=  LocationCustomerGroupMapping::create([
                        'location_id' => $request->location_id,             
                        'customer_group_id' =>$cval['customer_group_id'],                       
                        'status' => 'Y',
                        'company_id' => Auth::user()->company_id,
                        'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                        'created_by_user_id' => Auth::user()->id,
                    ]);
    
                }else{
                    
                    $locationData = LocationCustomerGroupMapping::where('location_id',$request->id)->where('location_id',$request->location_id)->where('customer_group_id',$cval['customer_group_id'])->first();

                    if($locationData){               
                        $storeData =  LocationCustomerGroupMapping::where('location_id',$request->location_id)->where('customer_group_id',$cval['customer_group_id'])->update([
                            'location_id' => $request->location_id,             
                            'customer_group_id' =>$cval['customer_group_id'],     
                            'status' => 'Y',
                            'company_id' => Auth::user()->company_id,
                            'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                            'last_by_user_id' => Auth::user()->id
                        ]);   
                    }else{
                        $storeData=  LocationCustomerGroupMapping::create([
                            'location_id' => $request->location_id,             
                            'customer_group_id' =>$cval['customer_group_id'],                       
                            'status' => 'Y',
                            'company_id' => Auth::user()->company_id,
                            'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                            'created_by_user_id' => Auth::user()->id,
                        ]);

                    }
                    
                  
                                  
                    }
                }
            
            $item = LocationCustomerGroupMapping::where('location_id',$request->id)->where('status','D')->delete();

            
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                ]);                
           

        }catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }   
    }

    public function destory(Request $request)
    {
        DB::beginTransaction();
       try{

        $customerGroupMapping = LocationCustomerGroupMapping::where('location_id',$request->id)->get();

        if($customerGroupMapping != null){

            foreach($customerGroupMapping as $key => $val){
                // $inUse = SalesOrder::where('customer_group_id',$val['customer_group_id'])->first();

                $isMRFound = MaterialRequest::where('customer_group_id','=',$val['customer_group_id'])
                ->where('current_location_id','=',$val['location_id'])->first(); 

                if($isMRFound != null){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Location to Customer Group Mapping Is Used In Material Request.",
                        // 'response_message' => "This Is Used Somewhere, You Can't Delete",
                    ]);
                }  

                $inUse = SalesOrder::where('customer_group_id',$val['customer_group_id'])->where('current_location_id',$val['location_id'])->first();
                if($inUse){
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => "You Can't Delete, Location to Customer Group Mapping Is Used In Sales Order.",
                        // 'response_message' => "This Is Used Somewhere, You Can't Delete",
                    ]);
                }

                 
            }
        }          
     
        LocationCustomerGroupMapping::where('location_id',$request->id)->delete();

        
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

    public function getExistCustomerGroup(Request $request){
        $customerGroup = LocationCustomerGroupMapping::select('customer_group_id','location_id')->where('location_id',$request->id)->get();

        if($customerGroup != null){
            $customerGroup->each(function ($item) use (&$isAnyPartInUse) {      

                    $isFound = SalesOrder::where('sales_order.customer_group_id','=',$item->customer_group_id)
                    ->where('sales_order.current_location_id','=',$item->location_id)
                    ->first();    
                    

                    if($isFound != null){
                        $item->in_use = true;                     
                      

                    }else{
                        $item->in_use = false;                     
                    }
                  
                    return $item;

            })->values();
        }


        return response()->json([
            'response_code' => 1,
            'customerGroup' => $customerGroup
        ]);
    }

}