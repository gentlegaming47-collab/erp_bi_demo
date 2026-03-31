<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\MisCategory;
use App\Models\SalesOrder;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use Maatwebsite\Excel\Facades\Excel;


class MisCategoryController extends Controller
{

    public function misCategoryData()
    {
        $mis_cat_data = MisCategory::orderBy('mis_category', 'ASC')->get();
        if($mis_cat_data){
            return response()->json([
                'mis_cat_data' => $mis_cat_data,
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
        return view('manage.manage-mis_category');
    }

    public function index(MisCategory $Country,Request $request,DataTables $dataTables)
    {
        $mis_cat_data = MisCategory::select(['mis_category.mis_category','mis_category.id','mis_category.created_on','mis_category.created_by_user_id','mis_category.last_by_user_id','mis_category.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'mis_category.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'mis_category.last_by_user_id');
    
        return DataTables::of($mis_cat_data)
        ->editColumn('created_by_user_id', function($mis_cat_data){ 
            if($mis_cat_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$mis_cat_data->created_by_user_id)->first('user_name'); 
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($mis_cat_data){ 
            if($mis_cat_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$mis_cat_data->last_by_user_id)->first('user_name'); 
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('mis_category', function($mis_cat_data){ 
            if($mis_cat_data->mis_category != ''){
                $mis_category = ucfirst($mis_cat_data->mis_category);
                return $mis_category;
            }else{
                return '';
            }
        })
        ->editColumn('created_on', function($mis_cat_data){ 
            if ($mis_cat_data->created_on != null) { 
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $mis_cat_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 
            }else{
                return '';
            }
        })
        ->filterColumn('mis_category.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(mis_category.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($mis_cat_data){ 
            if ($mis_cat_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $mis_cat_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->filterColumn('mis_category.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(mis_category.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($mis_cat_data){ 
            $action = "<div>";
                if(hasAccess("mis_category","edit")){
                $action .="<a id='edit_a' href='".route('edit-mis_category',['id' => base64_encode($mis_cat_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
                if(hasAccess("mis_category","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','mis_category','options'])
        ->make(true);
    }

    public function create()
    {
        return view('add.add-mis_category');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mis_category'=>'required|max:255|unique:mis_category',
        ],
        [
            'mis_category.required' => 'Please Enter Mis Category',
            'mis_category.max' => 'Maximum 255 Characters Allowed',
        ]);

        $mis_cat_data=  MisCategory::create([
            'mis_category'       => $request->mis_category,
            'company_id'         => Auth::user()->company_id,
            'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'created_by_user_id' => Auth::user()->id
        ]);
        DB::beginTransaction();
        if($mis_cat_data->save()){
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

    public function show(MisCategory $mis_cat_data,$id)
    {
        return view('edit.edit-mis_category')->with('id',$id);
    }

    public function edit(MisCategory $mis_cat_data,Request $request,$id)
    {
        $mis_cat_data = MisCategory::where('id','=',$id)->first();

        if($mis_cat_data){
            return response()->json([
                'mis_cat_data' => $mis_cat_data,
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

    public function update(Request $request, MisCategory $country)
    {
        $validated = $request->validate([
            'mis_category'=>['required','max:255',Rule::unique('mis_category')->ignore($request->id, 'id')]
        ],
        [
            'mis_category.required' => 'Please Enter MIS Category',
            'mis_category.max'      => 'Maximum 255 Characters Allowed'
        ]);

        $mis_cat_data=  MisCategory::where('id','=',$request->id)->update([
            'mis_category'    => $request->mis_category,
            'last_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'last_by_user_id' => Auth::user()->id
        ]);

        if($mis_cat_data){
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
      
        DB::beginTransaction();
        
        try{

            $src_item =  SalesOrder::where('mis_category_id',$request->id)->get();
            if($src_item->isNotEmpty()){	
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, MIS Category Is Used In Sales Order.",
                ]);
            }else{

                MisCategory::destroy($request->id);
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

    public function existsMisCategory(Request $request){
        if($request->term != ""){
            $fdMisCat = MisCategory::select('mis_category')->where('mis_category', 'LIKE', $request->term.'%')->groupBy('mis_category')->get();
            if($fdMisCat != null){
                // $output = [];

                // foreach($fdMisCat as $dsKey){
                //     array_push($output ,$dsKey->country);
                // } 
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdMisCat as $dsKey){

                    $output .= '<li parent-id="mis_category" list-id="mis_category_list" class="list-group-item" tabindex="0">'.$dsKey->mis_category.'</li>';
                } 
                $output .= '</ul>';

                return response()->json([
                    'MisCatList' => $output,
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
