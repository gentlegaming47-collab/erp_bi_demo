<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Taluka;
use App\Models\State;
use App\Models\Country;
use App\Models\Admin;
use App\Models\Village;
use App\Models\SalesOrder;
use App\Models\CustomerReplacementEntry;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Imports\talukaimport;
use DataTables;
use Date;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportCity;

class TalukaController extends Controller
{
    public function TalukaData($id)
    {
        // dd($id);
        $taluka_data = Taluka::select(['talukas.id','talukas.taluka_name','talukas.district_id','countries.country_name','districts.state_id'])
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->where('talukas.id','=',$id)
        ->first();
        // dd($taluka_data);

        if($taluka_data){
            return response()->json([
                'taluka' => $taluka_data,
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


    // get all taluka data 
    public function fetchTalukaData()
    {
        $taluka = Taluka::orderBy('taluka_name', 'ASC')->get();
        if($taluka){
            return response()->json([
                'taluka' => $taluka,
                'response_code' => '1'
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'No Data Avilable',
            ]);
        }

    }

    public function create()
    {
        $countries = Country::select('id','country_name')->orderBy('country_name','asc')->get();
        $states = State::select('id','state_name')->orderBy('state_name','asc')->get();
        return view('add.add-taluka')->with(['states' => $states,'countries' => $countries]);
    }

    public function manage()
    {
        return view('manage.manage-taluka');
    }

    public function index(Taluka $taluka,Request $request,DataTables $dataTables)
    {
        $taluka_data = Taluka::select(['talukas.id','talukas.taluka_name','districts.district_name','countries.country_name','states.state_name','talukas.created_on','talukas.created_by_user_id','talukas.last_by_user_id','talukas.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'talukas.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'talukas.last_by_user_id');
        

        return DataTables::of($taluka_data)
        ->editColumn('created_by_user_id', function($taluka_data){
            if($taluka_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$taluka_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($taluka_data){
            if($taluka_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$taluka_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('city_name', function($taluka_data){
            if($taluka_data->district_name != ''){
                $district_name = ucfirst($taluka_data->district_name);
                return $district_name;
            }else{
                return '';
            }
            //return Str::limit($taluka_data->district_name, 50);
        })
        ->editColumn('country_name', function($taluka_data){
            if($taluka_data->country_name != ''){
                $country_name = ucfirst($taluka_data->country_name);
                return $country_name;
            }else{
                return '';
            }
           // return Str::limit($taluka_data->country_name, 50);
        })
        ->editColumn('state_name', function($taluka_data){
            if($taluka_data->state_name != ''){
                $state_name = ucfirst($taluka_data->state_name);
                return $state_name;
            }else{
                return '';
            }
          //  return Str::limit($taluka_data->state_name, 50);
        })
        ->editColumn('created_on', function($taluka_data){
            if ($taluka_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $taluka_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('talukas.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(talukas.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($taluka_data){
            if ($taluka_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $taluka_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('talukas.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(talukas.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($taluka_data){
            $action = "<div>";
            if(hasAccess("taluka","edit")){
            $action .="<a id='edit_a' href='".route('edit-taluka',['id' => base64_encode($taluka_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("taluka","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','district_name','country_name','state_name','options'])
        ->make(true);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
         

            'taluka_name' => ['required', 'max:155', Rule::unique('talukas')->where(function ($query) use ($request) {
                return $query->where('district_id', '=', $request->taluka_district_id);
            })],

            'taluka_district_id' => 'required'
        ],
        [
            'taluka_name.required' => 'Please Enter Taluka',
            'taluka_name.unique' => 'The Taluka Name Has Already Been Taken',
            'taluka_name.max' => 'Maximum 255 Characters Allowed',
            'taluka_district_id.required' => 'Please Select District'
        ]);
        
        DB::beginTransaction();
        try{
            $city_data=  Taluka::create([
                'taluka_name' => $request->taluka_name,
                'district_id' => $request->taluka_district_id,
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

    public function edit(Taluka $taluka,Request $request,$id)
    {
        // dd($request->all());
        $taluka_data = Taluka::select(['talukas.id','talukas.district_id','districts.district_name','countries.country_name'])
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->where('talukas.id','=',$id)
        ->first();
        // dd($taluka_data);

        if($taluka_data){
            return response()->json([
                'taluka' => $taluka_data,
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

    public function update(Request $request, Taluka $taluka)
    {
        // dd($request->all());
        $validated = $request->validate([
            // 'taluka_name'         => ['required','max:255',Rule::unique('talukas')->ignore($request->id, 'id')],
            'taluka_name' => ['required', 'max:155', Rule::unique('talukas')->where(function ($query) use ($request) {
                return $query->where('district_id', '=', $request->taluka_district_id);
            })->ignore($request->id, 'id')],
            'taluka_district_id'  => 'required',
            'taluka_state_id'     => 'required'
        ],
        [
            'taluka_name.required' => 'Please Enter Taluka',
            'taluka_name.unique' => 'The Taluka Name Has Already Been Taken',
            'taluka_name.max' => 'Maximum 255 Characters Allowed',
            'taluka_district_id.required' => 'Please Select District'
        ]);
        DB::beginTransaction();
        try{
            $taluka =  Taluka::where('id','=',$request->id)->update([
                'taluka_name' => $request->taluka_name,
                'district_id' => $request->taluka_district_id,
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id
            ]);

            if($taluka){
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

    public function show(Taluka $taluka,$id)
    {
        // dd($taluka->all());
        $countries = Country::select('id','country_name')->orderBy('country_name','asc')->get();
        $states = State::select('id','state_name')->orderBy('state_name','asc')->get();
        return view('edit.edit-taluka')->with(['id' => $id,'states' => $states,'countries' => $countries]);
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $taluka_cre_id = CustomerReplacementEntry::where('cre_taluka_id',$request->id)->get();
            if($taluka_cre_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Taluka Is Used In Customer Replacement Entry.",
                ]);
            }
            $taluka_so_id = SalesOrder::where('customer_taluka',$request->id)->get();
            if($taluka_so_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Taluka Is Used In SO.",
                ]);
            }
        
            $taluka_village_id = Village::where('taluka_id',$request->id)->get();
            if($taluka_village_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Taluka Is Used In Village.",
                ]);
            }
            Taluka::destroy($request->id);
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

    public function getRelationValues(Request $request){
      
        // $relData = City::select(['id','district_name'])
        // ->where('districts.state_id','=',$request->state_id)
        // ->get();

        $relData = State::select(['states.gst_code','states.state_name','countries.country_name',])
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->where('states.id','=',$request->state_id)
        ->first();

        // $relDistrictData = State::select(['districts.district_name', 'districts.id' ])
        // ->leftJoin('districts','districts.state_id','=','states.id')
        // ->where('states.id','=',$request->state_id)
        // ->get();  
                 
        $relDistrictData = City::where('state_id', $request->state_id)->get();
        
        if($relData != null){
            return response()->json([
                'response_code' => '1',
                'relation_data' =>  $relData,
                'relation_district_data' =>  $relDistrictData
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }

    public function existsTaluka(Request $request){
        if($request->term != ""){
            $fdTaluka = Taluka::select('taluka_name')->where('taluka_name', 'LIKE', $request->term.'%')->groupBy('taluka_name')->get();
            if($fdTaluka != null){
                // $output = [];

                // foreach($fdCity as $dsKey){
                //     array_push($output ,$dsKey->city);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdTaluka as $dsKey){

                    $output .= '<li parent-id="taluka_name" list-id="taluka_name_list" class="list-group-item" tabindex="0">'.$dsKey->taluka_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'talukaList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Taluka available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'talukaList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function exportTaluka(Request $request){
        return Excel::download(new ExportTaluka, 'taluka.xlsx');
    }

    public function importview(){
        return view('import_taluka');
    }
    public function importTaluka()
    {
        Excel::import(new TalukaImport,request()->file('file'));
        return redirect()->back();
    }
}