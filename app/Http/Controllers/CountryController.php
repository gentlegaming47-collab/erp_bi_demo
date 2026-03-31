<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Admin;
use App\Models\State;
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


class CountryController extends Controller
{
    /**
     * Return all company data without filter
     */
    public function countryData()
    {
        $countries = Country::orderBy('country_name', 'ASC')->get();
        if($countries){
            return response()->json([
                'countries' => $countries,
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
        return view('manage.manage-country');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Country $Country,Request $request,DataTables $dataTables)
    {
        $country_data = Country::select(['countries.country_name','countries.id','countries.created_on',
        'countries.created_by_user_id','countries.last_by_user_id','countries.last_on',            'created_user.user_name as created_by_name','last_user.user_name as last_by_name'
        ])
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'countries.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'countries.last_by_user_id');
    
        return DataTables::of($country_data)
        ->editColumn('created_by_user_id', function($country_data){ 
            if($country_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$country_data->created_by_user_id)->first('user_name'); 
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('last_by_user_id', function($country_data){ 
            if($country_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$country_data->last_by_user_id)->first('user_name'); 
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('country', function($country_data){ 
            if($country_data->country != ''){
                $country = ucfirst($country_data->country);
                return $country;
            }else{
                return '';
            }
            //return Str::limit($country_data->country, 50);
        })
        ->editColumn('created_on', function($country_data){ 
            if ($country_data->created_on != null) { 
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $country_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 
            }else{
                return '';
            }
        })
        ->filterColumn('created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(countries.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($country_data){ 
            if ($country_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $country_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->filterColumn('last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(countries.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($country_data){ 
            $action = "<div>";
            if($country_data->id != 1){
                if(hasAccess("country","edit")){
                $action .="<a id='edit_a' href='".route('edit-country',['id' => base64_encode($country_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            }
            if($country_data->id != 1){
                if(hasAccess("country","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','country','options'])
        ->make(true);
    }

   /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('add.add-country');
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
            'country_name'=>'required|max:255|unique:countries',
        ],
        [
            'country_name.required' => 'Please Enter Country',
            'country_name.max' => 'Maximum 255 Characters Allowed',
        ]);

        $country_data=  Country::create([
            'country_name' => $request->country_name,
            'company_id' => Auth::user()->company_id,
            'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'created_by_user_id' => Auth::user()->id
        ]);
        DB::beginTransaction();
        if($country_data->save()){
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Inserted Successfully.',
            ]);
        }else{
            DB::rollBack();            
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Not Inserted',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country,$id)
    {
        return view('edit.edit-country')->with('id',$id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit(Country $country,Request $request,$id)
    {
        $country_data = Country::select('countries.id','countries.country_name')->where('id','=',$id)->first();
        // $country_data = Country::where('id','=',$id)->first();

        if($country_data){
            return response()->json([
                'country' => $country_data,
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
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'country_name'=>['required','max:255',Rule::unique('countries')->ignore($request->id, 'id')]
        ],
        [
            'country_name.required' => 'Please Enter Country',
            'country_name.max' => 'Maximum 255 Characters Allowed'
        ]);

        $country_data=  Country::where('id','=',$request->id)->update([
            'country_name' => $request->country_name,
            'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'last_by_user_id' => Auth::user()->id
        ]);

        if($country_data){
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

    public function existsCountry(Request $request){
        if($request->term != ""){
            $fdCountry = Country::select('country_name')->where('country_name', 'LIKE', $request->term.'%')->groupBy('country_name')->get();
            if($fdCountry != null){
                // $output = [];

                // foreach($fdCountry as $dsKey){
                //     array_push($output ,$dsKey->country);
                // } 
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdCountry as $dsKey){

                    $output .= '<li parent-id="country_name" list-id="country_name_list" class="list-group-item" tabindex="0">'.$dsKey->country_name.'</li>';
                } 
                $output .= '</ul>';

                return response()->json([
                    'countryList' => $output,
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if($request->id == "1"){
             return response()->json([
                'response_code' => "0",
                'response_message' => "This is used for reference,you can't delete",
            ]);
        }
        DB::beginTransaction();
        
        try{
            $country_state_id = State::where('country_id',$request->id)->get();
            if($country_state_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Country Is Used In State.",
                ]);
            }
            
            Country::destroy($request->id);
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){

            DB::rollBack();

            // $errorMessage =  $e->errorInfo[2];
            // preg_match('/`([^`]+)`\.`([^`]+)`/', $errorMessage, $matches);

            // $tableName = $matches[2];            
        
            // $table = DeleteMessage($tableName);
            
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
                // $error_msg = "You Can't Delete, Country Is Used In ".$table;
            }else{
                $error_msg = "Record Not Deleted";
            }
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }

    public function exportCountry(Request $request){
        return Excel::download(new ExportCountry, 'country.xlsx');
    }
}