<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\City;
use App\Models\Taluka;
use App\Models\Village;
use App\Models\State;
use App\Models\Country;
use App\Models\Admin;
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
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportCity;

class LocationController extends Controller
{
    public function create()
    {
        return view('add.add-location');
    }

    public function manage()
    {
        return view('manage.manage-location');
    }

    public function index(Location $location,Request $request,DataTables $dataTables)
    {
    $location_data = Location::select(['locations.id', 'locations.location_code','locations.location_name','locations.type', 'locations.mfg_process','locations.header_print','locations.status','countries.country_name','states.state_name','districts.district_name','talukas.taluka_name','villages.village_name','locations.created_on','locations.created_by_user_id','locations.last_by_user_id','locations.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('villages','villages.id','=','locations.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'locations.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'locations.last_by_user_id');

        
        
        // dd($location_data);
        

        return DataTables::of($location_data)
        ->editColumn('created_by_user_id', function($location_data){
            if($location_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$location_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($location_data){
            if($location_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$location_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('header_print', function($location_data){
            return  strip_tags($location_data->header_print);
        })
        ->editColumn('location_name', function($location_data){
            if($location_data->location_name != ''){
                $location_name = ucfirst($location_data->location_name);
                return $location_name;
            }else{
                return '';
            }
           // return Str::limit($location_data->district_name, 50);
        })
        ->editColumn('city_name', function($location_data){
            if($location_data->district_name != ''){
                $district_name = ucfirst($location_data->district_name);
                return $district_name;
            }else{
                return '';
            }
           // return Str::limit($location_data->district_name, 50);
        })
        // ->editColumn('customer_name', function($location_data){
        //     return Str::limit($location_data->customer_name, 50);
        // })
        ->editColumn('country_name', function($location_data){
            if($location_data->country_name != ''){
                $country_name = ucfirst($location_data->country_name);
                return $country_name;
            }else{
                return '';
            }
            //return Str::limit($location_data->country_name, 50);
        })
        ->editColumn('state_name', function($location_data){
            if($location_data->state_name != ''){
                $state_name = ucfirst($location_data->state_name);
                return $state_name;
            }else{
                return '';
            }
            //return Str::limit($location_data->state_name, 50);
        })
        ->editColumn('type', function($location_data){
            if($location_data->type != ''){
                $type = ucfirst($location_data->type);
                return $type;
            }else{
                return '';
            }
        })
        ->editColumn('mfg_process', function($location_data){
            if($location_data->mfg_process != ''){
                $mfg_process = ucfirst($location_data->mfg_process);
                return $mfg_process;
            }else{
                return '';
            }
        })
        ->editColumn('created_on', function($location_data){
            if ($location_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $location_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('locations.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(locations.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($location_data){
            if ($location_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $location_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('locations.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(locations.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($location_data){
            $action = "<div>";
            if(hasAccess("location","edit")){
            $action .="<a id='edit_a' href='".route('edit-location',['id' => base64_encode($location_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            
            if($location_data->id != getCurrentLocation()->id)
            {
                if(hasAccess("location","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options','header_print'])
        ->make(true);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'location_name'       =>'required|max:255|unique:locations',
            'location_code'       =>'required|unique:locations',
            // 'mfg_process'         =>'required',
            // 'mfg_process'         =>'required',
            'location_village_id' => 'required'
        ],
        [
            'location_name.required'       => 'Please Enter Location',
            'location_code.required'       => 'Please Enter Location Code',
            'location_code.unique'       =>     'The Location Code Has Already Been Taken',
            'location_name.unique'          => 'The Location Name Has Already Been Taken',
            'location_name.max'            => 'Maximum 255 Characters Allowed',
            'location_village_id.required' => 'Please Select Village'
        ]);

        
        
        DB::beginTransaction();
        try{
            $mfgProcess = $request->location_type == "godown" ? "" : $request->mfg_process;
            $location_data =  Location::create([
                'location_name'      => $request->location_name,
                'location_code'      => $request->location_code,
                'type'               => $request->location_type,
                'mfg_process'        => $mfgProcess,
                // 'customer_id'       => $request->customer_id,
                'header_print'       => $request->input('editor'),
                'village_id'         => $request->location_village_id,
                'status'             => $request->status,
                'company_id'         => Auth::user()->company_id,
                'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id
            ]);

            

            if($location_data->save()){
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
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    public function show(Location $location,$id)
    {
        // dd($taluka->all());

        $location = Location::where('id',base64_decode($id))->first();

        $addressIds =  getAddressDetails($location->village_id);
        
        $countries = Country::select('id as c_id','country_name')->where('id',$addressIds['country_id'])->orderBy('country_name','asc')->get();
        $states    = State::select('id as s_id','state_name')->where('id',$addressIds['state_id'])->orderBy('state_name','asc')->get();
        $district  = City::select('id as d_id','district_name')->where('id',$addressIds['district_id'])->orderBy('district_name','asc')->get();
        $taluka    = Taluka::select('id as t_id','taluka_name')->where('id',$addressIds['taluka_id'])->orderBy('taluka_name','asc')->get();
        $village   = Village::select('id as v_id','village_name')->where('id',$location->village_id)->orderBy('village_name','asc')->get();


        // $countries = Country::select('id','country_name')->orderBy('country_name','asc')->get();
        // $states    = State::select('id','state_name')->orderBy('state_name','asc')->get();
        // $district  = City::select('id','district_name')->orderBy('district_name','asc')->get();
        // $taluka    = Taluka::select('id','taluka_name')->orderBy('taluka_name','asc')->get();
        // $village   = Village::select('id','village_name')->orderBy('village_name','asc')->get();        
        return view('edit.edit-location')->with([
            'id'        => $id,
            'states'    => $states,
            'countries' => $countries,
            'district'  => $district,
            'taluka'    => $taluka,
            'village'   => $village
        ]);
    }

    public function LocationData($id)
    {
        $getLocation =  Session::get('getLocationId');
        // dd($id);
        $location_data = Location::select(['locations.id', 'locations.location_code','locations.location_name','locations.village_id','locations.type','locations.mfg_process','locations.header_print','locations.status','states.country_id','districts.state_id','talukas.district_id','villages.taluka_id','locations.created_on','locations.created_by_user_id','locations.last_by_user_id','locations.last_on'])
        ->leftJoin('villages','villages.id','=','locations.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')        
        ->where('locations.id','=',$id)
        ->first();
        // dd($location_data);
        $checkLocation  = checkLocationcode($id);        

        if($location_data){
            return response()->json([
                'location' => $location_data,
                'in_use' => $checkLocation,
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

    public function update(Request $request, Location $location)
    {
        
       
        $validated = $request->validate([
            'location_name' => ['required', 'max:155', Rule::unique('locations')->ignore($request->id, 'id')],     
            'location_code'       =>['required', Rule::unique('locations')->ignore($request->id, 'id')],  
        ],
        [
            'location_name.required'       => 'Please Enter Location',
            'location_code.required'       => 'Please Enter Location Code',
            'location_code.unique'       => 'The Location Code Has Already Been Taken',
            'location_name.unique'          => 'The Location Name Has Already Been Taken',
            'location_name.max'            => 'Maximum 255 Characters Allowed',            
        ]);

        DB::beginTransaction();
        $mfgProcess = $request->location_type == "godown" ? "" : $request->mfg_process;
        try{
            $location =  Location::where('id','=',$request->id)->update([
                'location_name'      => $request->location_name,
                'type'               => $request->location_type,
                'mfg_process'        => $mfgProcess,
                'location_code'      => $request->location_code,
                // 'customer_id'          => $request->customer_id,
                'header_print'       => $request->input('editor'),
                'village_id'         => $request->location_village_id,
                'status'             => $request->status,
                'last_on'            => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id'    => Auth::user()->id
            ]);
         
            if($location){
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

    public function destroy(Request $request)
    {
        DB::beginTransaction();
       
      
        try{

            $src_location = SupplierRejection::where('current_location_id',$request->id)->get();
            if($src_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Supplier Return Challan.",
                ]);
            }

            $item_issue_location = ItemIssue::where('current_location_id',$request->id)->get();
            if($item_issue_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Item Issue Slip.",
                ]);
            }

            $item_assm_location = ItemAssemblyProduction::where('current_location_id',$request->id)->get();
            if($item_assm_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Item Assembly Production.",
                ]);
            }

            
            $grn_current_location = GRNMaterial::where('current_location_id',$request->id)->get();
            if($grn_current_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In GRN.",
                ]);
            }
            
            $grn_to_location = GRNMaterial::where('to_location_id',$request->id)->get();
            if($grn_to_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In GRN.",
                ]);
            }

            $loading_entry_location = LoadingEntry::where('current_location_id',$request->id)->get();
            if($loading_entry_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Loading Entry.",
                ]);
            }

            $diapatch_plan_location = DispatchPlan::where('current_location_id',$request->id)->get();
            if($diapatch_plan_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Dispatch Plan.",
                ]);
            }

            $rep_location = ReplacementItemDecision::where('current_location_id',$request->id)->get();
            if($rep_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Replacement Item Decision.",
                ]);
            }


            $so_mapping_location = SOMapping::where('current_location_id',$request->id)->get();
            if($so_mapping_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Customer Replacement SO Mapping.",
                ]);
            }

            $so_short_location = SOShortClose::where('current_location_id',$request->id)->get();
            if($so_short_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Customer Replacement SO Short Close.",
                ]);
            }

            
            $cus_rep_location = CustomerReplacementEntry::where('current_location_id',$request->id)->get();
            if($cus_rep_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Customer Replacement Entry.",
                ]);
            }

        
            $item_return_location = ItemReturn::where('current_location_id',$request->id)->get();
            if($item_return_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Item Return Slip.",
                ]);
            }

            $item_production_location = ItemProduction::where('current_location_id',$request->id)->get();
            if($item_production_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Item Production.",
                ]);
            }

           
            $so_current_location = SalesOrder::where('current_location_id',$request->id)->get();
            if($so_current_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Sales Order.",
                ]);
            }

            $so_to_location = SalesOrder::where('to_location_id',$request->id)->get();
            if($so_to_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Sales Order.",
                ]);
            }

          
            $mr_current_location = MaterialRequest::where('current_location_id',$request->id)->get();
            if($mr_current_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Material Request.",
                ]);
            }

            $mr_to_location = MaterialRequest::where('to_location_id',$request->id)->get();
            if($mr_to_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Material Request.",
                ]);
            }

            $po_short_location = POShortClose::where('current_location_id',$request->id)->get();
            if($po_short_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In PO Short Close.",
                ]);
            }


            $po_current_location = PurchaseOrder::where('current_location_id',$request->id)->get();
            if($po_current_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Purchase Order.",
                ]);
            }
            
            $po_to_location = PurchaseOrder::where('to_location_id',$request->id)->get();
            if($po_to_location->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Purchase Order.",
                ]);
            }

         
            $location_to_customer_mapping = LocationCustomerGroupMapping::where('location_id',$request->id)->get();
            if($location_to_customer_mapping->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Location to Customer Group Mapping.",
                ]);
            }

            $location_stock = LocationStock::where('location_id',$request->id)->get();
            if($location_stock->isNotEmpty()){
                return response()->json([
                   'response_code' => '0',
                   'response_message' => "You Can't Delete, Location Is Used In Item Stock Report.",
                ]);
            }

          
            Location::destroy($request->id);          
           
            
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
    // public function destroy(Request $request)
    // {
    //     DB::beginTransaction();
    //     $location = Location::join('customers', 'customers.id', 'locations.customer_id')->where('locations.id', $request->id)->pluck('customer_id')->first();
        
      
    //     try{
    //         if($location)
    //         {
    //             DB::commit();
    //             return response()->json([
    //                 'response_code' => '0',
    //                 'response_message' => "This is used somewhere, you can't delete"
    //             ]);

    //         }else{
    //             Location::destroy($request->id);
    //         }
            
            
            
    //        // $findCustomer = Customer::where('id', )
           
            
    //         DB::commit();
    //         return response()->json([
    //             'response_code' => '1',
    //             'response_message' => 'Record Deleted Successfully.',
    //         ]);
    //     }catch(\Exception $e){
    //         DB::rollBack();
    //         if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
    //             $error_msg = "This is used somewhere, you can't delete";
    //         }else{
    //             $error_msg = "Record Not Deleted";
    //         }
    //         return response()->json([
    //             'response_code' => '0',
    //             'response_message' => $error_msg,
    //         ]);
    //     }
    // }

    public function getStates(Request $request){
        $relData = State::select(['id','state_name'])
        ->where('states.country_id','=',$request->country_id)
        ->orderBy('state_name', 'ASC')
        ->get();
        if($relData != null){
            return response()->json([
                'states' =>  $relData,
                'response_code' => '1'
                // 'relation_data' =>  $relData
             
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'No Data Avilable',
            ]);
        }
    }

    public function getLocationDistrict(Request $request){
        $relData = City::select(['id','district_name'])
        ->where('districts.state_id','=',$request->state_id)        
        ->orderBy('district_name', 'ASC')
        ->get();
        if($relData != null){
            return response()->json([
                'cities' => $relData,
                'response_code' => '1',
                // 'relation_data' =>  $relData
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }

    public function getTaluka(Request $request){
        $relData = Taluka::select(['id','taluka_name'])
        ->where('talukas.district_id','=',$request->district_id)
        ->orderBy('taluka_name', 'ASC')
        ->get();
        if($relData != null){
            return response()->json([
                'response_code' => '1',
                'taluka' => $relData,
                // 'relation_data' =>  $relData
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }

    public function getVillage(Request $request){
        $relData = Village::select(['id','village_name'])
        ->where('villages.taluka_id','=',$request->taluka_id)
        ->orderBy('village_name', 'ASC')
        ->get();
        if($relData != null){
            return response()->json([
                'response_code' => '1',
                'village' => $relData,
                // 'relation_data' =>  $relData
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }


    public function getLocationName(Request $request){
        if($request->term != ""){
            $location_name = Location::select('location_name')->where('location_name', 'LIKE', $request->term.'%')->groupBy('location_name')->get();
            // dd($location_name);
            if($location_name != null){
              
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($location_name as $dsKey){

                    $output .= '<li parent-id="location_name" list-id="location_name_list" class="list-group-item" tabindex="0">'.$dsKey->location_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'location_name' => $output,
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
    
    public function getLocationCode(Request $request){
        if($request->term != ""){
            $location_code = Location::select('location_code')->where('location_code', 'LIKE', $request->term.'%')->groupBy('location_code')->get();
            // dd($location_name);
            if($location_code != null){
              
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($location_code as $dsKey){

                    $output .= '<li parent-id="location_code" list-id="location_code_list" class="list-group-item" tabindex="0">'.$dsKey->location_code.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'location_code' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Location code available',
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

    public function checkUserExists(Request $request)
    {
        
        $customerId = $request->customer_id;
        
        $getId = $request->id;
        $checkUser = Location::where('customer_id', $customerId)->where('id', '!=', $getId)->first();
        
        if($checkUser)
        {
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Customer Already Taken.',
            ]);
        }
        else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'success',
            ]);
        }
    }


    public static function getDefaultLocationData($forBlade = false){
        
        $locationData = Location::where('id','=',session('getLocationId'))->first();
        if($locationData == null){
           
            if($forBlade == false){
                return response()->json([
                    'response_code' => '0',
                    'data' => $locationData,
                    'response_message' => 'Company Year Data Not Available'
                ]);
            }else{
                 return null;
            }
            
        }
        return $locationData;
    }
}