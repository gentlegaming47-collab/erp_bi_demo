<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Country;
use App\Models\City;
use App\Models\Admin;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Exports\ExportState;
use Maatwebsite\Excel\Facades\Excel;

class StateController extends Controller
{

        /**
     * Return all company data without filter
     */
    public function stateData()
    {
        $states = State::orderBy('state_name', 'ASC')->get();
        if($states){
            return response()->json([
                'states' => $states,
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
        return view('manage.manage-state');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(State $State,Request $request,DataTables $dataTables)
    {
        $state_data = State::select(['states.state_name','states.id','states.gst_code','countries.country_name','states.created_on','states.created_by_user_id','states.last_by_user_id','states.last_on',
        'created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'states.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'states.last_by_user_id');

        return DataTables::of($state_data)
        ->editColumn('created_by_user_id', function($state_data){
            if($state_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$state_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($state_data){
            if($state_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$state_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('state', function($state_data){
            if($state_data->state != ''){
                $state = ucfirst($state_data->state);
                return $state;
            }else{
                return '';
            }
            //return Str::limit($state_data->state, 50);
        })
        ->editColumn('country', function($state_data){
            if($state_data->country != ''){
                $country = ucfirst($state_data->country);
                return $country;
            }else{
                return '';
            }
            // return Str::limit($state_data->country, 50);
        })
        ->editColumn('created_on', function($state_data){
            if ($state_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $state_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('states.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(states.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($state_data){
            if ($state_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $state_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('states.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(states.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($state_data){
            $action = "<div>";
            if(hasAccess("state","edit")){
            $action .="<a id='edit_a' href='".route('edit-state',['id' => base64_encode($state_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("state","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','state','country','options'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countriues = Country::select('id','country_name')->orderBy('country_name','asc')->get();
        return view('add.add-state')->with(['countries' => $countriues]);
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
            'state_name' => ['required', 'max:155', Rule::unique('states')->where(function ($query) use ($request) {
                return $query->where('state_name','=',$request->state_name)->where('country_id', '=', $request->country_id)->where('gst_code',$request->gstcode);
            })],
            'gst_code'=> 'max:15|unique:states',
            'country_id'=>'required',
        ],
        [
            'state_name.required' => 'Please Enter State',
            'state_name.max' => 'Maximum 255 Characters Allowed',
            'country_id.required' => 'Please Select Country',
            'gst_code' => 'Maximum 15 Characters Allowed',
            'gst_code.unique' => 'The GST Code Has Already Been Taken',
            'state_name.unique' => 'The State Name Has Already Been Taken'
        ]);
        DB::beginTransaction();
        try{
            $state_data=  State::create([
                'state_name' => $request->state_name,
                'gst_code' => $request->gst_code,
                'country_id' => $request->country_id,
                'company_id' => Auth::user()->company_id,
                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id
            ]);
            DB::commit();
            if($state_data->save()){
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
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function show(State $state,$id)
    {
        $countriues = Country::select('id','country_name')->orderBy('country_name','asc')->get();
        return view('edit.edit-state')->with(['id'=> $id,'countries' => $countriues]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function edit(State $state,Request $request,$id)
    {
        $state_data = State::select('states.id','states.state_name','states.country_id','states.gst_code')->where('id','=',$id)->first();
        // $state_data = State::where('id','=',$id)->first();
        
        if($state_data){
            return response()->json([
                'state' => $state_data,
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
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'state_name' => ['required', 'max:155', Rule::unique('states')->where(function ($query) use ($request) {
                return $query->where('state_name',$request->state)->where('country_id', '=', $request->country_id);
            })->ignore($request->id, 'id')],
            'gst_code'=> ['max:15',Rule::unique('states')->ignore($request->id, 'id')],
            'country_id'=>'required',
        ],
        [
            'state_name.required' => 'Please Enter State',
            'state_name.max' => 'Maximum 255 Characters Allowed',
            'country_id.required' => 'Please Select Country',
            'gst_code' => 'Maximum 15 Characters Allowed',
            'gst_code.unique' => 'The GST Code Has Already Been Taken',
            'state_name.unique' => 'The State Name Has Already Been Taken'
        ]);
        DB::beginTransaction();
        try{
            $state_data=  State::where('id','=',$request->id)->update([
                'state_name' => $request->state_name,
                'gst_code' => $request->gst_code,
                'country_id' => $request->country_id,
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id
            ]);
            DB::commit();
            if($state_data){
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

    public function existsState(Request $request){
        if($request->term != ""){
            $fdState = State::select('state_name')->where('state_name', 'LIKE', $request->term.'%')->groupBy('state_name')->get();
            if($fdState != null){
                // $output = [];

                // foreach($fdState as $dsKey){
                //     array_push($output ,$dsKey->state);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdState as $dsKey){

                    $output .= '<li parent-id="state_name" list-id="state_name_list" class="list-group-item" tabindex="0">'.$dsKey->state_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'stateList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No State available',
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
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $state_city_id = City::where('state_id',$request->id)->get();
            if($state_city_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, State Is Used In District.",
                ]);
            }
            State::destroy($request->id);
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
                // $error_msg = "You Can't Delete, State Is Used In ".$table;
            }else{
                $error_msg = "Record Not Deleted";
            }
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }

    public function exportState(Request $request){
        return Excel::download(new ExportState, 'state.xlsx');
    }
}