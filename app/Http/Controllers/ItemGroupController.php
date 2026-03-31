<?php

namespace App\Http\Controllers;

use App\Models\ItemGroup;
use App\Models\Country;
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
use App\Exports\ExportCountry;

class ItemGroupController extends Controller
{
    public function itemGroupData()
    {
        $itemGroup = ItemGroup::orderBy('item_group_name', 'ASC')->get();

        if($itemGroup)
        {
            return response()->json([
                'itemGroupData' => $itemGroup,
                'response_code' => "1"
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
        return view('manage.manage-item-group');
    }

    public function index(ItemGroup $itemGroup,Request $request,DataTables $dataTables)
    {
        $item_group = ItemGroup::select(['item_groups.item_group_name','item_groups.item_group_code','item_groups.id','item_groups.created_on','item_groups.created_by_user_id','item_groups.last_by_user_id','item_groups.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'item_groups.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'item_groups.last_by_user_id');
    
        return DataTables::of($item_group)
        ->editColumn('created_by_user_id', function($item_group){ 
            if($item_group->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$item_group->created_by_user_id)->first('user_name'); 
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($item_group){ 
            if($item_group->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$item_group->last_by_user_id)->first('user_name'); 
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('item_group_name', function($item_group){ 
            if($item_group->item_group_name != ''){
                $item_group_name = ucfirst($item_group->item_group_name);
                return $item_group_name;
            }else{
                return '';
            }
            
            //return Str::limit($item_group->item_group_name, 50);
        })
        ->editColumn('group_id', function($item_group){ 
            return Str::limit($item_group->item_group_code, 50);
        })
        ->editColumn('created_on', function($item_group){ 
            if ($item_group->created_on != null) { 
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $item_group->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 
            }else{
                return '';
            }
        })
        ->filterColumn('item_groups.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_groups.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($item_group){ 
            if ($item_group->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $item_group->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->filterColumn('item_groups.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_groups.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($item_group){ 
            $action = "<div>";
            // if($item_group->id != 1){
                if(hasAccess("item_group","edit")){
                $action .="<a id='edit_a' href='".route('edit-item_group',['id' => base64_encode($item_group->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            // }
            // if($item_group->id != 1){
                if(hasAccess("item_group","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            // }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','item_group_name', 'group_id', 'options'])
        ->make(true);
    }

    public function create()
    {
        return view('add.add-item-group');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'item_group_name'=>'required|max:255|unique:item_groups',
            'item_group_code'=>'required|max:255|unique:item_groups',

            // 'item_group_name' => ['required', 'max:155', Rule::unique('item_groups')->where(function ($query) use ($request) {
            //     return $query->where('item_group_name','=',$request->item_group_name)->where('item_group_code', '=', $request->item_group_code);
            // })],

            // 'item_group_code'=>'required|max:255|unique:item_groups',

            // 'item_group_code' => ['required', 'max:155', Rule::unique('item_groups')->where(function ($query) use ($request) {
            //     return $query->where('item_group_code','=',$request->item_group_code)->where('item_group_name', '=', $request->item_group_name);
            // })
        ],
        [
            'item_group_name.required' => 'Please enter Item Group',
            'item_group_name.max'      => 'Maximum 255 characters allowed',
            'item_group_name.unique'   => 'The Item Group Name Has Already Been Taken', 
            'item_group_code.unique'   => 'The Item Group Code Has Already Been Taken',
            'item_group_code.required' => 'Please enter group code',
            'item_group_code.max'      => 'Maximum 255 characters allowed',
            
        ]);
            
        DB::beginTransaction();
        try{
            $itemGroupData =  ItemGroup::create([
                'item_group_name' => $request->item_group_name,
                'item_group_code' => $request->item_group_code,
                'company_id' => Auth::user()->company_id,
                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id
            ]);

            DB::commit();
            if($itemGroupData->save()){              
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
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not inserted',
                'original_error' => $e->getMessage()
            ]);  
        }
    }

    public function show(ItemGroup $itemGroup,$id)
    {
        return view('edit.edit-item-group')->with('id',$id);
    }

    public function edit(ItemGroup $itemGroup,Request $request,$id)
    {
        $item_data = ItemGroup::where('id','=',$id)->first();

        if($item_data){
            return response()->json([
                'item_data' => $item_data,
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

    public function update(Request $request, ItemGroup $itemGroup)
    {
        $validated = $request->validate([
            'item_group_name'=>['required','max:255',Rule::unique('item_groups')->ignore($request->id, 'id')],
            'item_group_code'=>['required','max:255',Rule::unique('item_groups')->ignore($request->id, 'id')],
            //'item_group_code'=>'required','max:255',Rule::unique('item_groups')->ignore($request->id, 'id')]

            // 'item_group_code' => ['required', 'max:155', Rule::unique('item_groups')->where(function ($query) use ($request) {
            //     return $query->where('item_group_code','=',$request->item_group_code)->where('item_group_name', '=', $request->item_group_name);
            // })->ignore($request->id, 'id')]],

        ],
        [
            'item_group_name.required' => 'Please enter item group',
            'item_group_name.max'      => 'Maximum 255 characters allowed',
            'item_group_name.unique'   => 'The Item Group Name Has Already Been Taken', 
            'item_group_code.unique'   => 'The Item Group Code Has Already Been Taken'
        ]);

        $item_group=  ItemGroup::where('id','=',$request->id)->update([
            'item_group_name' => $request->item_group_name,
            'item_group_code' => $request->item_group_code,
            'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
            'last_by_user_id' => Auth::user()->id
        ]);

        if($item_group){
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

    public function existsItemGroup(Request $request){
        if($request->term != ""){
            $fdItemGroup = ItemGroup::select('item_group_name')->where('item_group_name', 'LIKE', $request->term.'%')->groupBy('item_group_name')->get();
            if($fdItemGroup != null){
                
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdItemGroup as $dsKey){

                    $output .= '<li parent-id="item_group_name" list-id="item_group_name_list" class="list-group-item" tabindex="0">'.$dsKey->item_group_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'itemGroupList' => $output,
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


    public function existsGroupCode(Request $request){

        if($request->term != ""){
            $fdGroupCode = ItemGroup::select('item_group_code')->where('item_group_code', 'LIKE', $request->term.'%')->groupBy('item_group_code')->get();
            if($fdGroupCode != null){
                    
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdGroupCode as $dsKey){

                    $output .= '<li parent-id="group_id" list-id="group_code_name_list" class="list-group-item" tabindex="0">'.$dsKey->item_group_code.'</li>';
                }
                $output .= '</ul>';
                
                return response()->json([
                'GroupCode' => $output,
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
            $item_grp_id = Item::where('item_group_id',$request->id)->get();
            if($item_grp_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Item Group Is Used In Item.",
                ]);
            }


            ItemGroup::destroy($request->id);
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