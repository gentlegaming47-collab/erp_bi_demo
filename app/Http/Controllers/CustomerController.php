<?php

namespace App\Http\Controllers;

use App\Models\Customer;

use App\Models\Location;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\CustomerType;
use App\Models\CustomerContacts;
use App\Models\Village;
use App\Models\CustomerGroup;

use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportCustomer;

class CustomerController extends Controller
{
    /**
     * Return all company data without filter
     */
    public function customerData()
    {
        $customers = Customer::all();
        if($customers){
            return response()->json([
                'customers' => $customers,
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
        return view('manage.manage-customer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Customer $Customer,Request $request,DataTables $dataTables)
    {
        $customer_data = Customer::select(['customers.id','customers.customer_name','customers.customer_code','customers.register_number','villages.village_name','customers.mobile_no','customers.email','customers.PAN','customers.gst_code','customers.aadhar_no','customers.address','customers.pincode','customers.created_on','customers.created_by_user_id','customers.last_by_user_id','customers.last_on',
        'customer_groups.customer_group_name','customers.pan'])
        ->leftJoin('customer_groups','customer_groups.id','=','customers.customer_group_id')
        ->leftJoin('villages','villages.id','=','customers.village_id');

    
        return DataTables::of($customer_data)
        ->editColumn('created_by_user_id', function($customer_data){
            if($customer_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$customer_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->editColumn('last_by_user_id', function($customer_data){
            if($customer_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$customer_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->editColumn('GSTIN	', function($customer_data){
            if($customer_data->gst_code != null){
                $gstno = Customer::where('id','=',$customer_data->gst_code)->first('gst_code');
                return isset($gstno->gst_code) ? $gstno->gst_code : '';
            }else{
                return '';
            }

        })
        
        ->editColumn('customer_name', function($customer_data){
            if($customer_data->customer_name != ''){
                $cust_name = ucfirst($customer_data->customer_name);
                return $cust_name;
            }else{
                return '';
            }
            //return Str::limit($customer_data->customer_name, 50);
        })
        
        ->editColumn('village_name', function($customer_data){
            if($customer_data->village_name != ''){
                $village_name = ucfirst($customer_data->village_name);
                return $village_name;
            }else{
                return '';
            }
            //return Str::limit($customer_data->village_name, 50);
        })      
        ->editColumn('created_on', function($customer_data){
            if ($customer_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $customer_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->editColumn('last_on', function($customer_data){
            if ($customer_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $customer_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->addColumn('options',function($customer_data){
            $action = "<div>";
            if(hasAccess("customer","edit")){
            $action .="<a id='edit_a' href='".route('edit-customer',['id' => base64_encode($customer_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("customer","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','customer_name','village_name', 'options'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        $village = Village::orderBy('village_name', 'ASC')->get();                
        // $customer_types =CustomerType::select('id','name')->orderBy('name','asc')->get();
        return view('add.add-customer')->with(['village' => $village]);

    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'customer_name'=>'required|max:255|unique:customers',            
            'address' => 'max:255',
            'village_id' => 'required',
        
            
        ],
        [
            'customer_name.required' => 'Please enter Customer',
            'customer_name.unique'   => 'The Customer Name Has Already Been Taken',
            'customer_name.max' => 'Maximum 255 characters allowed',                    
            'village_id.required' => 'Please Select Village',
            'address.max' => 'Maximum 255 charactes allowed',
            
        ]);
        DB::beginTransaction();
        try{
            $customer_data=  Customer::create([
                'customer_name'=>$request->customer_name,                
                'customer_group_id'=>$request->customer_group_id ,            
                'address' => $request->address,
                'village_id' => $request->village_id,
                'pincode' => $request->pincode,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'PAN' => $request->PAN,                
                'gst_code' => $request->filled('gstin') ? $request->get('gstin') : '',
                'PAN' => $request->filled('pan') ? $request->get('pan') : '',
                'aadhar_no' => $request->aadhar_no,                
                'company_id' => Auth::user()->company_id,
                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id,
                'customer_code'   =>$request->customer_code,
                'register_number' => $request->register_number
            ]);
                
            if($customer_data->save()){
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

            }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer,$id)
    {
        $cities = Village::orderBy('village_name', 'ASC')->get();
        
        $checkCustomer = Location::where('customer_id', base64_decode($id))->first();
        $customer_group =CustomerGroup::select('id','customer_group_name')->orderBy('customer_group_name','asc')->get();
        
        return view('edit.edit-customer')->with(['id' => $id,'districts' => $cities,'customer_group'=>$customer_group, 'checkUser' => $checkCustomer ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer,Request $request, $id)
    {
      
        $customer_data = Customer::select(['customers.*', 'villages.village_name', 'countries.id as c_id', 'states.id as s_id', 'districts.id as d_id', 'talukas.id as t_id' ])
        ->leftJoin('villages','villages.id','=','customers.village_id')        
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')        
        ->leftJoin('districts','districts.id','=','talukas.district_id')        
        ->leftJoin('states','states.id','=','districts.state_id')        
        ->leftJoin('countries','countries.id','=','states.country_id')        
        ->where('customers.id','=',$id)->first();


        if($customer_data){
            return response()->json([
                'customer' => $customer_data,
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
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer_name'=>['required','max:255',Rule::unique('customers')->ignore($request->id, 'id')],            
            'address' => 'max:255',
            'village_id' => 'required',
            // 'mobile_no' => 'required',
            // 'email' => 'required',
        ],
        [
            'customer_name.required' => 'Please enter customer name',
            'customer_name.unique'          => 'The Customer Name Has Already Been Taken',
            'customer_name.max' => 'Maximum 255 characters allowed',
            'village_id.required' => 'Please select Village',
            'address.max' => 'Maximum 255 charactes allowed',
        ]);
        // dd($request->all());
        DB::beginTransaction();
        try{
            $customer_data =  Customer::where('id','=',$request->id)->update([
                'customer_name'=>$request->customer_name,                
                'customer_group_id'=>$request->customer_group_id ,            
                'address' => $request->address,
                'village_id' => $request->village_id,
                'pincode' => $request->pincode,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'PAN' => $request->PAN,
                'gst_code' => $request->gstin,
                'PAN' => $request->pan,
                'aadhar_no' => $request->aadhar_no,  
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id,
                'customer_code'   =>$request->customer_code,
                'register_number' => $request->register_number
            ]);
          

            if($customer_data){
            
                DB::commit();
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
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Updated',
                'original_error' => $e->getMessage()
            ]);
        }
    }


    public function getRelationValues(Request $request){
        $relData = Village::select(['talukas.district_id', 'districts.state_id', 'states.country_id','talukas.taluka_name', 'districts.district_name', 'countries.country_name','states.state_name'])

        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        
        ->where('villages.id','=',$request->village_id)
        ->first();

        if($relData != null){
            return response()->json([
                'response_code' => '1',
                'relation_data' =>  $relData
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }

    public function getCutomerRelationValue(Request $request){
        
        $relData = Country::select(['id', 'country_name'])  
        ->where('id','=',$request->country_id)        
        ->first();

        if($relData != null){
            return response()->json([
                'response_code' => '1',
                'relation_data' =>  $relData
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }

    public function existsCustomer(Request $request){
        if($request->term != ""){
            $fdCustomer = Customer::select('customer_name')->where('customer_name', 'LIKE', $request->term.'%')->groupBy('customer_name')->get();
            if($fdCustomer != null){
                // $output = [];

                // foreach($fdCustomer as $dsKey){
                //     array_push($output ,$dsKey->customer);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdCustomer as $dsKey){

                    $output .= '<li parent-id="customer_name" list-id="customer_name_list" class="list-group-item" tabindex="0">'.$dsKey->customer_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'customerList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Customer available',
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

    public function existsPaymentTerms(Request $request){
        if($request->term != ""){
            $fdPaymentTerms = Customer::select('payment_terms')->where('payment_terms', 'LIKE', $request->term.'%')->groupBy('payment_terms')->get();
            if($fdPaymentTerms != null){
                // $output = [];

                // foreach($fdPaymentTerms as $pmKey){
                //     array_push($output ,$pmKey->payment_terms);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdPaymentTerms as $pmKey){

                    $output .= '<li parent-id="payment_terms" list-id="payment_terms_list" class="list-group-item" tabindex="0">'.$pmKey->payment_terms.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'paymentTermsList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Payment terms available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'paymentTermsList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsDesignation(Request $request){
        if($request->term != ""){
            $fdDesignation = CustomerContacts::select('contact_designation')->where('contact_designation', 'LIKE', $request->term.'%')->groupBy('contact_designation')->get();
            if($fdDesignation != null){
                // $output = [];

                // foreach($fdDesignation as $dsKey){
                //     array_push($output ,$dsKey->customer);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdDesignation as $dsKey){

                    $output .= '<li parent-id="contact_designation" list-id="contact_designation_list" class="list-group-item" tabindex="0">'.$dsKey->contact_designation.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'designationList' => $output,
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
                'designationList' => '',
                'response_code' => 1,
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $giveLocation = Location::where('customer_id', $request->id)->first();
            
            if($giveLocation != null && $giveLocation != "")
            {
                DB::rollBack();
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "This is used somewhere, you can't delete"
                ]);
            }else{
                Customer::destroy($request->id);            
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Deleted Successfully.',
                ]);
            }
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

    public function exportCustomer(Request $request){
        return Excel::download(new ExportCustomer, 'customer.xlsx');
    }

    public function getCustomerCode(Request $request){
        
        // $cust_data = Customer::select(['customer_code'])->orderBy('customer_code','desc')->first();
        $cust_data = Customer::select(['customer_code'])->max('customer_code');

        // dd($cust_data);
        $isFound = 0; 
        if($cust_data != ''){
            $num = $cust_data+1;
            $middle_num = str_pad($num,5,"0",STR_PAD_LEFT);
        }else{
            $isFound++;
            $middle_num = str_pad($isFound,5,"0",STR_PAD_LEFT);
        }

        return response()->json([
            'response_code' => 1,
            'cust_code' => $middle_num,
            'number' => $isFound
        ]);
    }
    

    public function getVillageData(Request $request)
    {
        
        $village = $request->village_id;
        
        if(!empty($village))
        {
            $getVillage = Village::where('id', $village)->pluck('default_pincode')->first();
        }else{
            $getVillage = "";
        }

        return response()->json([
            'response_code' => '1',
            'pincode' => $getVillage,
        ]);

        // if($getVillage)
        // {
        //     return response()->json([
        //         'response_code' => '1',
        //         'pincode' => $getVillage,
        //     ]);
        // }
        // else 
        // {
        //     return response()->json([
        //         'response_code' => '0',
        //         'response_message' => $error_msg,
        //     ]);
        // }
    }
}