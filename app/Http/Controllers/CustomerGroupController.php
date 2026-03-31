<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Admin;
use App\Models\SalesOrder;
use App\Models\CustomerReplacementEntry;
use App\Models\MaterialRequest;
use App\Models\LocationCustomerGroupMapping;
use App\Models\PriceList;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportCountry;
use App\Models\CustomerGroup;


class CustomerGroupController extends Controller
{
   public function customerGroupData()
   {
       $customer_group_data = CustomerGroup::orderBy('customer_group_name', 'ASC')->get();
       if($customer_group_data)
       {
           return response()->json([
               'customer_group_data' => $customer_group_data,
               'response_code' => 1
           ]);
       }else{
           return response()->json([
               'response_code' => 0,
               'response_message' => 'Customer Group Not Found',
           ]);
       }
   }

   public function manage()
   {
       return view('manage.manage-customer_group');
   }

   public function index(Country $Country,Request $request,DataTables $dataTables)
    {
        $customer_group = CustomerGroup::select(['customer_groups.customer_group_name','customer_groups.id','customer_groups.created_on','customer_groups.created_by_user_id','customer_groups.last_by_user_id','customer_groups.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'customer_groups.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'customer_groups.last_by_user_id');
    
        return DataTables::of($customer_group)
        ->editColumn('created_by_user_id', function($customer_group){ 
            if($customer_group->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$customer_group->created_by_user_id)->first('user_name'); 
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($customer_group){ 
            if($customer_group->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$customer_group->last_by_user_id)->first('user_name'); 
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('customer_group', function($customer_group){ 
            if($customer_group->customer_customer_group_namename != ''){
                $customer_group_name = ucfirst($customer_group->customer_group_name);
                return $customer_group_name;
            }else{
                return '';
            }
           // return Str::limit($customer_group->abc, 50);
        })
        ->editColumn('created_on', function($customer_group){ 
            if ($customer_group->created_on != null) { 
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $customer_group->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 
            }else{
                return '';
            }
        })
        ->filterColumn('customer_groups.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(customer_groups.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($customer_group){ 
            if ($customer_group->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $customer_group->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->filterColumn('customer_groups.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(customer_groups.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($customer_group){ 
            $action = "<div>";
            // if($customer_group->id != 1){
                if(hasAccess("customer_group","edit")){
                $action .="<a id='edit_a' href='".route('edit-customer_group',['id' => base64_encode($customer_group->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            // }
            // if($customer_group->id != 1){
                if(hasAccess("customer_group","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            // }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','customer_group','options'])
        ->make(true);
    }

    public function create()
    {
        return view('add.add-customer_group');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_group_name'=>'required|max:255|unique:customer_groups',
        ],
        [
            'customer_group_name.required' => 'Please enter Customer Group Name',
            'customer_group_name.max' => 'Maximum 255 characters allowed',
        ]);

        $customer_group_data=  CustomerGroup::create([
            'customer_group_name' => $request->customer_group_name,
            'company_id' => Auth::user()->company_id,
            'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'created_by_user_id' => Auth::user()->id
        ]); 



        if($customer_group_data->save())
        {
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
    }


    public function show(CustomerGroup $customerGroup, $id)
    {
        
        return view('edit.edit-customer-group')->with('id',$id);
    }

    public function edit($id)
    {           
        
        $customer_group = CustomerGroup::select('customer_groups.id','customer_groups.customer_group_name')->where('id','=',$id)->first();
        // $customer_group = CustomerGroup::where('id','=',$id)->first();
        
        if($customer_group){
            return response()->json([
                'customerGroup' => $customer_group,
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

    public function update(CustomerGroup $customerGroup, Request $request)
    {
        $validated = $request->validate([
            'customer_group_name'=>['required','max:255',Rule::unique('customer_groups')->ignore($request->id, 'id')],
        ],
        [
            'customer_group_name.required' => 'Please enter Customer Group Name',
            'customer_group_name.max' => 'Maximum 255 characters allowed',
        ]);
         
        $customer_group=  CustomerGroup::where('id','=',$request->id)->update([
            'customer_group_name' => $request->customer_group_name,            
            'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'last_by_user_id' => Auth::user()->id
        ]);

        if($customer_group){
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

    public function destroy(Request $request)
    {
        // if($request->id == "1"){
        //      return response()->json([
        //         'response_code' => "0",
        //         'response_message' => "This is used for reference,you can't delete",
        //     ]);
        // }
        DB::beginTransaction();
        try{
            
            $customer_grp_cre = CustomerReplacementEntry::where('customer_group_id',$request->id)->get();
            if($customer_grp_cre->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Customer Group Is Used In Customer Replacement Entry.",
                ]);
            }

            $customer_grp_so = SalesOrder::where('customer_group_id',$request->id)->get();
            if($customer_grp_so->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Customer Group Is Used In SO.",
                ]);
            }
          
            $customer_grp_mr = MaterialRequest::where('customer_group_id',$request->id)->get();
            if($customer_grp_mr->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Customer Group Is Used In Material Request.",
                ]);
            }

            $customer_grp_location_customer = LocationCustomerGroupMapping::where('customer_group_id',$request->id)->get();
            if($customer_grp_location_customer->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Customer Group Is Used In Location to Customer Group Mapping.",
                ]);
            }

            $customer_grp_price_list = PriceList::where('customer_group_id',$request->id)->get();
            if($customer_grp_price_list->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Customer Group Is Used In Price List.",
                ]);
            }
            
            CustomerGroup::destroy($request->id);
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

    public function existsCustomerGroup(Request $request){
        if($request->term != ""){
            $fetchGroup = CustomerGroup::select('customer_group_name')->where('customer_group_name', 'LIKE', $request->term.'%')->groupBy('customer_group_name')->get();
            
            if($fetchGroup != null){
                // $output = [];

                // foreach($fdCountry as $dsKey){
                //     array_push($output ,$dsKey->country);
                // } 
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fetchGroup as $dsKey){
                  
                    $output .= '<li parent-id="customer_group_name" list-id="customer_group_name_list" class="list-group-item" tabindex="0">'.$dsKey->customer_group_name.'</li>';
                } 
                $output .= '</ul>';
                
                return response()->json([
                    'customerGroupList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Country available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'menuList' => '',
                'response_code' => 1,
            ]);
        }
       
    }

}

         