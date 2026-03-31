<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\Taluka;
use App\Models\SalesOrder;
use App\Models\CustomerReplacementEntry;
use App\Models\Admin;
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
use App\Exports\ExportCity;


class CityController extends Controller
{
    /**
     * Return all company data without filter
     */
    public function cityData()
    {
        $cities = City::orderBy('district_name', 'ASC')->get();
        if($cities){
            return response()->json([
                'cities' => $cities,
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
        return view('manage.manage-city');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(City $City,Request $request,DataTables $dataTables)
    {
        $city_data = City::select(['districts.district_name','districts.id','countries.country_name','states.state_name','districts.created_on','districts.created_by_user_id','districts.last_by_user_id','districts.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'districts.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'districts.last_by_user_id');
        

        return DataTables::of($city_data)
        ->editColumn('created_by_user_id', function($city_data){
            if($city_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$city_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($city_data){
            if($city_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$city_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('city_name', function($city_data){
            if($city_data->district_name != ''){
                $district_name = ucfirst($city_data->district_name);
                return $district_name;
            }else{
                return '';
            }
            //return Str::limit($city_data->district_name, 50);
        })
        ->editColumn('country_name', function($city_data){
            if($city_data->country_name != ''){
                $country_name = ucfirst($city_data->country_name);
                return $country_name;
            }else{
                return '';
            }
            // return Str::limit($city_data->country_name, 50);
        })
        ->editColumn('state_name', function($city_data){
            if($city_data->state_name != ''){
                $state_name = ucfirst($city_data->state_name);
                return $state_name;
            }else{
                return '';
            }
           // return Str::limit($city_data->state_name, 50);
        })
        ->editColumn('created_on', function($city_data){
            if ($city_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $city_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('districts.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(districts.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($city_data){
            if ($city_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $city_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('districts.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(districts.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($city_data){
            $action = "<div>";
            if(hasAccess("district","edit")){
            $action .="<a id='edit_a' href='".route('edit-district',['id' => base64_encode($city_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("district","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','district_name','country_name','state_name','options'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::select('id','country_name')->orderBy('country_name','asc')->get();
        $states = State::select('id','state_name')->orderBy('state_name','asc')->get();
        return view('add.add-city')->with(['states' => $states,'countries' => $countries]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([            
            'district_name' => ['required', 'max:155', Rule::unique('districts')->where(function ($query) use ($request) {
                return $query->where('state_id', '=', $request->state_id);
            })],
            'state_id'=>'required',
        ],
        [
            'district_name.required' => 'Please Enter District',
            'district_name.unique' => 'The District Name Has Already Been Taken',
            'district_name.max' => 'Maximum 255 Characters Allowed',
            'state_id.required' => 'Please Select State'
        ]);
        
        DB::beginTransaction();
        try{
            $city_data=  City::create([
                'district_name' => $request->district_name,
                'state_id' => $request->state_id,
                'company_id' => Auth::user()->company_id,
                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id
            ]);

            

            if($city_data->save()){
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city,$id)
    {
        
        $countries = Country::select('id','country_name')->orderBy('country_name','asc')->get();
        $states = State::select('id','state_name')->orderBy('state_name','asc')->get();
        
        return view('edit.edit-city')->with(['id' => $id,'states' => $states,'countries' => $countries]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city,Request $request,$id)
    {
        
        $city_data = City::select(['districts.id','districts.state_id','districts.district_name','countries.country_name'])
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->where('districts.id','=',$id)
        ->first();

    
        if($city_data){
            return response()->json([
                'city' => $city_data,
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
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'district_name' => ['required', 'max:155', Rule::unique('districts')->where(function ($query) use ($request) {
                return $query->where('state_id', '=', $request->state_id);
            })->ignore($request->id, 'id')],
            'state_id'=>'required',
        ],
        [
            'district_name.required' => 'Please Enter District',
            'district_name.unique' => 'The District Name Has Already Been Taken',
            'district_name.max' => 'Maximum 255 Characters Allowed',
            'state_id.required' => 'Please Select State'
        ]);
        DB::beginTransaction();
        try{
            $city_data =  City::where('id','=',$request->id)->update([
                'district_name' => $request->district_name,
                'state_id' => $request->state_id,
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id
            ]);

            if($city_data){
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
        $relData = State::select(['states.gst_code','countries.country_name'])
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->where('states.id','=',$request->state_id)
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

    public function existsCity(Request $request){
        if($request->term != ""){
            $fdCity = City::select('district_name')->where('district_name', 'LIKE', $request->term.'%')->groupBy('district_name')->get();
            if($fdCity != null){
                // $output = [];

                // foreach($fdCity as $dsKey){
                //     array_push($output ,$dsKey->city);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdCity as $dsKey){

                    $output .= '<li parent-id="district_name" list-id="city_name_list" class="list-group-item" tabindex="0">'.$dsKey->district_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'cityList' => $output,
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $city_so_id = SalesOrder::where('customer_district_id',$request->id)->get();
            if($city_so_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete , District Is Used In SO.",
                ]);
            }
            $city_cre_id = CustomerReplacementEntry::where('cre_district_id',$request->id)->get();
            if($city_so_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete , District Is Used In Customer Replacement Entry.",
                ]);
            }
            $city_taluka_id = Taluka::where('district_id',$request->id)->get();
            if($city_taluka_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete , District Is Used In Taluka.",
                ]);
            }
            City::destroy($request->id);
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

    public function exportCity(Request $request){
        return Excel::download(new ExportCity, 'district.xlsx');
    }
}