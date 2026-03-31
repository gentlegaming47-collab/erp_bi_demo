<?php

namespace App\Http\Controllers;

use App\Exports\ExportVillage;
use App\Jobs\ExportLargeDataJob;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\Admin;
use App\Models\Village;
use App\Models\SalesOrder;
use App\Models\Dealer;
use App\Models\Supplier;
use App\Models\Location;

use App\Models\Taluka;
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


// use App\Exports\ExportCity;


class VillageController extends Controller
{
    public function villageData()
    {
        $village = Village::orderBy('village_name', 'ASC')->get();
        if($village){
            return response()->json([
                'village' => $village,
                'response_code' => '1'
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'No Data Avilable',
            ]);
        }

    }

    public function manage()
    {
        return view('manage.manage-village');
    }

    public function index(Village $Village,Request $request,DataTables $dataTables)
    {
        $villageData = Village::select(['villages.village_name','villages.default_pincode','villages.id','villages.default_pincode','talukas.taluka_name','districts.district_name','states.state_name', 'countries.country_name','villages.created_on','villages.created_by_user_id','villages.last_by_user_id','villages.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts', 'districts.id', 'talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'villages.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'villages.last_by_user_id');

        return DataTables::of($villageData)
        ->editColumn('created_by_user_id', function($villageData){
            if($villageData->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$villageData->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($villageData){
            if($villageData->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$villageData->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('village_name', function($villageData){
            if($villageData->village_name != ''){
                $village_name = ucfirst($villageData->village_name);
                return $village_name;
            }else{
                return '';
            }
            //return Str::limit($villageData->village_name, 50);
        })
        ->editColumn('taluka_name', function($villageData){
            if($villageData->taluka_name != ''){
                $taluka_name = ucfirst($villageData->taluka_name);
                return $taluka_name;
            }else{
                return '';
            }
            //return Str::limit($villageData->taluka_name, 50);
        })
        ->editColumn('district_name', function($villageData){
            if($villageData->district_name != ''){
                $district_name = ucfirst($villageData->district_name);
                return $district_name;
            }else{
                return '';
            }
            //return Str::limit($villageData->district_name, 50);
        })
        ->editColumn('state_name', function($villageData){
            if($villageData->state_name != ''){
                $state_name = ucfirst($villageData->state_name);
                return $state_name;
            }else{
                return '';
            }
            // return Str::limit($villageData->state_name, 50);
        })
        ->editColumn('country_name', function($villageData){
            if($villageData->country_name != ''){
                $country_name = ucfirst($villageData->country_name);
                return $country_name;
            }else{
                return '';
            }
           // return Str::limit($villageData->country_name, 50);
        })
        ->editColumn('created_on', function($villageData){
            if ($villageData->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $villageData->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('villages.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(villages.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($villageData){
            if ($villageData->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $villageData->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('villages.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(villages.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($villageData){
            $action = "<div>";
            if(hasAccess("village","edit")){
            $action .="<a id='edit_a' href='".route('edit-village',['id' => base64_encode($villageData->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("village","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','village_name','taluka_name','district_name','country_name','state_name','options'])
        ->make(true);
    }

    public function create()
    {
        $countries = Country::select('id','country_name')->orderBy('country_name','asc')->get();
        $states = State::select('id','state_name')->orderBy('state_name','asc')->get();         
        $district = City::select('id','district_name')->orderBy('district_name','asc')->get();

        return view('add.add-village')->with(['states' => $states,'countries' => $countries, 'district' => $district]);
    }
    public function store(Request $request)
    {
        
        $validated = $request->validate([
         //   'village_name'=>'required|max:255|unique:villages',
            'village_name' => ['required', 'max:155', Rule::unique('villages')->where(function ($query) use ($request) {
                return $query->where('village_name',$request->village_name)->where('taluka_id', '=', $request->taluka_id);
            })],

            //'default_pincode' => 'required'
        ],
        [
            'village_name.required' => 'Please Enter Village',
            'village_name.unique' => 'The Village Name Has Already Been Taken',
            'village_name.max' => 'Maximum 255 Characters Allowed',
           // 'default_pincode.required' => 'Please Enter Default Pincode'
        ]);
        // dd($request->all());
        DB::beginTransaction();          
        try{
          
            $villageData=  Village::create([
                'village_name'       => $request->village_name,                                
                'taluka_id'          => $request->taluka_id,
                'default_pincode'    => $request->default_pincode,
                'company_id'         => Auth::user()->company_id,
                'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id
            ]);

                if($villageData->save()){
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
    
    public function show(Village $village, $id)
    {
        $countries = Country::select('id','country_name')->orderBy('country_name','asc')->get();
        $states = State::select('id','state_name')->orderBy('state_name','asc')->get();         
        $district = City::select('id','district_name')->orderBy('district_name','asc')->get();

        return view('edit.edit-village')->with(['id' => $id, 'states' => $states,'countries' => $countries, 'district' => $district]);
    }

    public function edit(Request $request, $id)
    {
        $villageData = Village::select(['villages.village_name','villages.id','villages.default_pincode','talukas.id as t_id', 'districts.id as d_id','states.id as s_id', 'countries.country_name'])
        
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts', 'districts.id', 'talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->where('villages.id', '=', $id)
        ->first();

        if($villageData)
        {
            return response()->json([
                'village' => $villageData,
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
            // 'village_name'=>['required','max:255',Rule::unique('villages')->ignore($request->id, 'id')],

            'village_name' => ['required', 'max:155', Rule::unique('villages')->where(function ($query) use ($request) {
                return $query->where('village_name',$request->village_name)->where('taluka_id', '=', $request->taluka_id);
            })->ignore($request->id, 'id')],

            'taluka_id'=>'required',
           // 'default_pincode' => 'required'
        ],
        [
            'village_name.required' => 'Please Enter Village',
            'village_name.unique' => 'The Village Name Has Already Been Taken',
            'village_name.max' => 'Maximum 255 Characters Allowed',
            //'default_pincode.required' => 'Please Enter Default Pincode'
        ]);
        DB::beginTransaction();          
        try{
            // $getVillagename = Village::where('village_name', $request->village_name)->where('id', '!=', $request->id)->first();
            
            // if($getVillagename != null)
            // {
                
            //      $getId = $getVillagename->id;
            //      $taluka_id = $getVillagename->taluka_id;

            //     if($taluka_id != null)
            //     {
            //         if(($taluka_id == $request->taluka_id))
            //         {
            //                 return response()->json([
            //                     'response_code' => '0',
            //                     'response_message' => 'Village already exists',
            //                 ]);
            //         }
            //     }
            // }
            // else
            // {
                
                $villageData=  Village::where('id','=',$request->id)->update([
                    'village_name'    => $request->village_name,
                    'taluka_id'       => $request->taluka_id,
                    'default_pincode' => $request->default_pincode,
                    'company_id'      => Auth::user()->company_id,
                    'last_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                    'last_by_user_id' => Auth::user()->id
                ]);
                    
                if($villageData){
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
            // }
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


   

    public function existsVillage(Request $request){
        if($request->term != ""){
            $fdCity = Village::select('village_name')->where('village_name', 'LIKE', $request->term.'%')->groupBy('village_name')->get();
            if($fdCity != null){
                // $output = [];

                // foreach($fdCity as $dsKey){
                //     array_push($output ,$dsKey->city);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdCity as $dsKey){

                    $output .= '<li parent-id="village_name" list-id="city_name_list" class="list-group-item" tabindex="0">'.$dsKey->village_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'villageList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No City available',
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

    public function getRelationValues(Request $request){
        $releationData = State::select(['countries.country_name'])
        ->leftJoin('countries','countries.id','=','states.country_id')        
        ->where('states.id','=',$request->state_id)
        ->get();
      

        
        if($releationData != null){
            return response()->json([
                'response_code' => '1',
                'relation_data' =>  $releationData
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }
    
    public function getRelationDistrict(Request $request)
    {
        $releationData = City::select(['districts.district_name', 'districts.id'])     
        ->where('districts.state_id','=',$request->state_id)
        ->get();

        $countryName = State::select(['countries.country_name'])
        ->leftJoin('countries','countries.id','=','states.country_id')        
        ->where('states.id','=',$request->state_id)
        ->first();

        if($releationData != null){
            return response()->json([
                'response_code' => '1',
                'relation_data' =>  $releationData,
                'country_name' =>  $countryName
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }

    public function getTaluka(Request $request)
    {
        
        $releationTaluka = Taluka::select(['talukas.taluka_name', 'talukas.id'])        
        ->where('talukas.district_id','=',$request->district_id)
        ->get();

        
        if($releationTaluka->isNotEmpty()){
            return response()->json([
                'response_code' => '1',
                'relation_taluka_data' =>  $releationTaluka,                
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }
    
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{

            $so_data = SalesOrder::where('customer_village',$request->id)->get();
            if($so_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Village Is Used In SO.",
                ]);
            }
            
            $location = Location::where('village_id',$request->id)->get();
            if($location->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Village Is Used In Location.",
                ]);
            }

            $dealer = Dealer::where('village_id',$request->id)->get();
            if($dealer->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Village Is Used In Dealer.",
                ]);
            }
            
            $suuplier = Supplier::where('village_id',$request->id)->get();
            if($suuplier->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Village Is Used In Supplier.",
                ]);
            }

            Village::destroy($request->id);
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

    public function exportVillage(Request $request){
        $CTS = time();
        return (new ExportVillage)->download('village.xlsx',null,['File-Name' => 'village.xlsx']);
    }
    
}