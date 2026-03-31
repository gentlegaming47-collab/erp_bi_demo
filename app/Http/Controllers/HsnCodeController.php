<?php

namespace App\Http\Controllers;

use App\Models\HsnCode;
use App\Models\Admin;
use App\Models\Item;
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
use App\Exports\ExportHsnCode;

class HsnCodeController extends Controller
{
        /**
     * Return all company data without filter
     */
    public function hsnCodeData()
    {
        $hsn_code = HsnCode::orderBy('hsn_code', 'ASC')->get();
        if($hsn_code){
            return response()->json([
                'hsn_code' => $hsn_code,
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
        return view('manage.manage-hsn_code');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(HsnCode $hsn_code,Request $request,DataTables $dataTables)
    {
        $hsn_code_data = HsnCode::select(['hsn_code.hsn_code','hsn_code.hsn_description','hsn_code.id','hsn_code.created_on','hsn_code.created_by_user_id','hsn_code.last_by_user_id','hsn_code.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'hsn_code.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'hsn_code.last_by_user_id');

        return DataTables::of($hsn_code_data)
        ->editColumn('created_by_user_id', function($hsn_code_data){
            if($hsn_code_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$hsn_code_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($hsn_code_data){
            if($hsn_code_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$hsn_code_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('hsn_code', function($hsn_code_data){
            if($hsn_code_data->hsn_code != ''){
                $hsn_code = ucfirst($hsn_code_data->hsn_code);
                return $hsn_code;
            }else{
                return '';
            }
            // return Str::limit($hsn_code_data->hsn_code, 50);
        })
        ->editColumn('created_on', function($hsn_code_data){
            if ($hsn_code_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $hsn_code_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('hsn_code.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(hsn_code.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($hsn_code_data){
            if ($hsn_code_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $hsn_code_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('hsn_code.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(hsn_code.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($hsn_code_data){
            $action = "<div>";
            if(hasAccess("hsn_code","edit")){
            $action .="<a id='edit_a' href='".route('edit-hsn_code',['id' => base64_encode($hsn_code_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            //if($hsn_code_data->id != 1){
                if(hasAccess("hsn_code","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            //}
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','hsn_code','options'])
        ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('add.add-hsn_code');
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
            'hsn_code'=>'required|max:255|unique:hsn_code',
            //'hsn_description'=>'required|max:255'
        ],
        [
            'hsn_code.unique' => 'HSN Code Has Already Been Taken',
            'hsn_code.required' => 'Please Enter HSN Code',
            'hsn_code.max' => 'Maximum 255 Characters Allowed',
           // 'hsn_description' =>'Please Enter HSN Description',
            //'hsn_description'=>'Maximum 255 Characters Allowed',
        ]);

        $hsn_code_data=  HsnCode::create([
            'hsn_code' => $request->hsn_code,
            'hsn_description'=>$request->hsn_description,
            'company_id' => Auth::user()->company_id,
            'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'created_by_user_id' => Auth::user()->id
        ]);

        if($hsn_code_data->save()){
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HsnCode $hsn_code
     * @return \Illuminate\Http\Response
     */
    public function show(HsnCode $hsn_code,$id)
    {
        return view('edit.edit-hsn_code')->with('id',$id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HsnCode $hsn_code
     * @return \Illuminate\Http\Response
     */
    public function edit(HsnCode $hsn_code,Request $request,$id)
    {
        $hsn_code_data = HsnCode::where('id','=',$id)->first();

        if($hsn_code_data){
            return response()->json([
                'hsn_code' => $hsn_code_data,
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
     * @param  \App\Models\HsnCode $hsn_code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HsnCode $hsn_code)
    {
       $validated = $request->validate([
            'hsn_code'=>['required','max:255',Rule::unique('hsn_code')->ignore($request->id, 'id')],
            //'hsn_description'=>'required',
        ],
        [
            'hsn_code.unique' => 'HSN Code Has Already Been Taken',
            'hsn_code.required' => 'Please Enter HSN Code',
            'hsn_code.max' => 'Maximum 255 Characters Allowed',
            //'hsn_description' =>'Please Enter HSN Description',
            //'hsn_description'=>'Maximum 255 Characters Allowed',
        ]);

        $hsn_code_data =  HsnCode::where('id','=',$request->id)->update([
            'hsn_code' => $request->hsn_code,
            'hsn_description' => $request->hsn_description,
            'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'last_by_user_id' => Auth::user()->id
        ]);

        if($hsn_code_data){
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

     public function existsHsnCode(Request $request){
        if($request->term != ""){
            $fdHancode = HsnCode::select('hsn_code')->where('hsn_code', 'LIKE', $request->term.'%')->groupBy('hsn_code')->get();
            if($fdHancode != null){
                // $output = [];

                // foreach($fdHancode as $dsKey){
                //     array_push($output ,$dsKey->country);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdHancode as $dsKey){

                    $output .= '<li parent-id="hsn_code" list-id="hsn_code_list" class="list-group-item" tabindex="0">'.$dsKey->hsn_code.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'hsncodeList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No HSN Code available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'hsncodeList' => '',
                'response_code' => 1,
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HsnCode $hsn_code
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{

            $item_hsn_id = Item::where('hsn_code',$request->id)->get();
            if($item_hsn_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, HSN Code Is Used In Item.",
                ]);
            }
            HsnCode::destroy($request->id);
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

    public function exportHsnCode(Request $request){
        return Excel::download(new ExportHsnCode, 'hsncode.xlsx');
    }
}