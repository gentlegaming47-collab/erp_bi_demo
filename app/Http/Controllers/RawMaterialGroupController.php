<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RawMaterialGroup;
use App\Models\Admin;
use Illuminate\Http\Response;
// use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Transporter;

class RawMaterialGroupController extends Controller
{
    public function rawMaterialData()
    {
        $rawMaterialData = RawMaterialGroup::orderBy('raw_material_group_nm', 'ASC')->get();
        if($rawMaterialData)
        {
            return response()->json([
                'rawMaterial' => $rawMaterialData,
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
        return view('manage.manage-raw-material-group');
    }

    public function index(RawMaterialGroup $rawMaterialGroup,Request $request,DataTables $dataTables)
    {
        $raw_material_group = RawMaterialGroup::select(['raw_material_groups.id', 'raw_material_groups.raw_material_group_nm', 'raw_material_groups.company_id', 'raw_material_groups.created_by_user_id', 'raw_material_groups.created_on', 'raw_material_groups.last_by_user_id', 'raw_material_groups.last_on']);

        return DataTables::of($raw_material_group)
        ->editColumn('created_by_user_id', function($raw_material_group){ 
            if($raw_material_group->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$raw_material_group->created_by_user_id)->first('user_name'); 
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->editColumn('last_by_user_id', function($raw_material_group){ 
            if($raw_material_group->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$raw_material_group->last_by_user_id)->first('user_name'); 
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        
        })
        ->editColumn('raw_material_group_nm', function($raw_material_group){ 
            if($raw_material_group->raw_material_group_nm != ''){
                $raw_material_group_nm = ucfirst($raw_material_group->raw_material_group_nm);
                return $raw_material_group_nm;
            }else{
                return '';
            }
            // return Str::limit($raw_material_group->raw_material_group_nm, 50);
        })
        ->editColumn('created_on', function($raw_material_group){ 
            if ($raw_material_group->created_on != null) { 
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $raw_material_group->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 
            }else{
                return '';
            }
        })
        ->editColumn('last_on', function($raw_material_group){ 
            if ($raw_material_group->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $raw_material_group->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->addColumn('options',function($raw_material_group){ 
            $action = "<div>";
            if($raw_material_group->id != 1){
                if(hasAccess("transporter","edit")){
                $action .="<a id='edit_a' href='".route('edit-raw_material_group',['id' => base64_encode($raw_material_group->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            }
            if($raw_material_group->id != 1){
                if(hasAccess("transporter","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','raw_material_group_nm','options'])
        ->make(true);
    }

    public function create()
    {
        return view('add.add-raw-material-group');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'raw_material_group_nm'=>'required|max:255|unique:raw_material_groups',
        ],
        [
            'raw_material_group_nm.required' => 'Please enter Raw Material Group',
            'raw_material_group_nm.max' => 'Maximum 255 characters allowed',
            'raw_material_group_nm.unique' => 'The Raw Material Group Name Has Already Been Taken'
        ]);
        DB::beginTransaction();
        try{
            $raw_Material_Group_Data =  RawMaterialGroup::create([
                'raw_material_group_nm' => $request->raw_material_group_nm,
                'company_id' => Auth::user()->company_id,
                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id
            ]);

            DB::commit();
            if($raw_Material_Group_Data->save()){              
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

    public function show(Transporter $transporter, $id)
    {
        return view('edit.edit-raw-material-group')->with('id',$id);
    }

    public function edit(RawMaterialGroup $rawMaterialGroup,Request $request,$id)
    {
      
        $raw_Material_Group_Data = RawMaterialGroup::where('id','=',$id)->first();

        if($raw_Material_Group_Data){
            return response()->json([
                'raw_Material_Group_Data' => $raw_Material_Group_Data,
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

    public function update(Request $request, RawMaterialGroup $rawMaterialData)
    {
        $validated = $request->validate([
            'raw_material_group_nm'=>['required','max:255',Rule::unique('raw_material_groups')->ignore($request->id, 'id')],                        
        ],
        [
            'raw_material_group_nm.required' => 'Please enter Raw Material Group',
            'raw_material_group_nm.max' => 'Maximum 255 characters allowed',
            'raw_material_group_nm.unique' => 'The Raw Material Group Name Has Already Been Taken'

        ]);

        DB::beginTransaction();
        try{
            $rawMaterialData=  RawMaterialGroup::where('id','=',$request->id)->update([
                'raw_material_group_nm' => $request->raw_material_group_nm,                
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id
            ]);
            DB::commit();
            if($rawMaterialData){
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
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Updated',
                'original_error' => $e->getMessage()
            ]);  
        }
    }

    public function existsRawMaterial(Request $request){
        if($request->term != ""){
            $fdRawMaterial = RawMaterialGroup::select('raw_material_group_nm')->where('raw_material_group_nm', 'LIKE', $request->term.'%')->groupBy('raw_material_group_nm')->get();
            if($fdRawMaterial != null){
                // $output = [];

                // foreach($fdState as $dsKey){
                //     array_push($output ,$dsKey->state);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdRawMaterial as $dsKey){

                    $output .= '<li parent-id="raw_material_group_nm" list-id="raw_material_group_nm_list" class="list-group-item" tabindex="0">'.$dsKey->raw_material_group_nm.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'rawMaterialList' => $output,
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

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            RawMaterialGroup::destroy($request->id);
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

}
