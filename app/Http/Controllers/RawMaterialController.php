<?php

namespace App\Http\Controllers;


use App\Models\Country;
use App\Models\Item;
use App\Models\Admin;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Unit;
use App\Models\HsnCode;
use DataTables;
use Date;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportCountry;
use App\Models\RawMaterial;


class RawMaterialController extends Controller
{
    public function rawMaterialData()
    {
        $rawMaterial = RawMaterial::orderBy('raw_material', 'ASC')->get();
        if($rawMaterial){
            return response()->json([
                'rawMaterial' => $rawMaterial,
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
        return view('manage.manage-raw-material');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RawMaterial $rawMaterial,Request $request,DataTables $dataTables)
    {
      
        $rawMaterialData = RawMaterial::select(['raw_materials.id','raw_materials.raw_material','raw_material_groups.raw_material_group_nm', 'raw_materials.min_stock_qty','raw_materials.max_stock_qty','raw_materials.re_order_qty','hsn_code.hsn_code','raw_materials.unit_id', 'units.unit_name','raw_materials.rate_per_unit', 'raw_materials.created_by_user_id','raw_materials.last_by_user_id','raw_materials.last_on','raw_materials.created_on'])
        ->leftJoin('raw_material_groups','raw_material_groups.id','=','raw_materials.raw_material_group_id')        
        ->leftJoin('units','units.id','raw_materials.unit_id')        
        ->leftJoin('hsn_code','hsn_code.id','=','raw_materials.hsn_code');

        return DataTables::of($rawMaterialData)
        ->editColumn('created_by_user_id', function($rawMaterialData){ 
            if($rawMaterialData->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$rawMaterialData->created_by_user_id)->first('user_name'); 
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->editColumn('last_by_user_id', function($rawMaterialData){ 
            if($rawMaterialData->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$rawMaterialData->last_by_user_id)->first('user_name'); 
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        
        })
        ->editColumn('raw_material', function($rawMaterialData){ 
            if($rawMaterialData->raw_material != ''){
                $raw_material = ucfirst($rawMaterialData->raw_material);
                return $raw_material;
            }else{
                return '';
            }
            //return Str::limit($rawMaterialData->raw_material, 50);
        })
        ->editColumn('raw_material_group_nm', function($rawMaterialData){ 
            if($rawMaterialData->raw_material_group_nm != ''){
                $raw_material_group_nm = ucfirst($rawMaterialData->raw_material_group_nm);
                return $raw_material_group_nm;
            }else{
                return '';
            }
            //return Str::limit($rawMaterialData->raw_material_group_nm, 50);
        })
        ->editColumn('unit', function($rawMaterialData){ 
            if($rawMaterialData->unit_name != ''){
                $unit_name = ucfirst($rawMaterialData->unit_name);
                return $unit_name;
            }else{
                return '';
            }
           // return Str::limit($rawMaterialData->unit_name, 50);
        })
        ->editColumn('min_stock_qty', function($rawMaterialData){ 
            return helpFormatWeight($rawMaterialData->min_stock_qty);

            // return Str::limit($rawMaterialData->min_stock_qty, 50);
        })
        ->editColumn('max_stock_qty', function($rawMaterialData){ 
            return helpFormatWeight($rawMaterialData->max_stock_qty);

            // return Str::limit($rawMaterialData->max_stock_qty, 50);
        })
        ->editColumn('re_order_qty', function($rawMaterialData){ 
            return helpFormatWeight($rawMaterialData->re_order_qty);

            // return Str::limit($rawMaterialData->re_order_qty, 50);
        })
        ->editColumn('hsn_code', function($rawMaterialData){ 
            if($rawMaterialData->hsn_code != ''){
                $hsn_code = ucfirst($rawMaterialData->hsn_code);
                return $hsn_code;
            }else{
                return '';
            }
           // return Str::limit($rawMaterialData->hsn_code, 50);
        })
        ->editColumn('rate_per_unit', function($rawMaterialData){ 
            return helpFormatWeight($rawMaterialData->rate_per_unit);

            // return Str::limit($rawMaterialData->rate_per_unit, 50);
        })      
        ->editColumn('created_on', function($rawMaterialData){ 
            if ($rawMaterialData->created_on != null) { 
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $rawMaterialData->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 
            }else{
                return '';
            }
        })
        ->editColumn('last_on', function($rawMaterialData){ 
            if ($rawMaterialData->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $rawMaterialData->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->addColumn('options',function($rawMaterialData){ 
            $action = "<div>";
            // if($rawMaterialData->id != 1){
                if(hasAccess("raw-material","edit")){
                $action .="<a id='edit_a' href='".route('edit-raw_material',['id' => base64_encode($rawMaterialData->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            // }
            // if($rawMaterialData->id != 1){
                if(hasAccess("raw-material","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            // }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','raw_material', 'raw_material_group_nm', 'unit', 'min_stock_qty','max_stock_qty','re_order_qty','hsn_code','rate_per_unit','options'])
        ->make(true);
    }

   /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unit = Unit::select('id','unit_name')->orderBy('unit_name','asc')->get();
        $hsn_code  = HsnCode::select('id','hsn_code')->orderBy('hsn_code','asc')->get();
        return view('add.add-raw-material')->with(['unit' => $unit,'hsn_code' => $hsn_code]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'raw_material'          =>'required|max:255|unique:raw_materials',
            'raw_material_group_id' =>'required',                        
            // 'min_stock_qty'      =>'required',
            // 'max_stock_qty'      =>'required',
            // 're_order_qty'       =>'required',
            // 'hsn_code'           =>'required',
            // 'rate_per_unit'      =>'required',            
        ],
        [
            'raw_material.required'          => 'Please enter Raw Material',
            'raw_material.max'               => 'Maximum 255 characters allowed',
            'raw_material_group_id.required' => 'Please Select Raw Material Group',                     
             // 'min_stock_qty.required'     => 'Please enter Minimum Stock',
            // 'max_stock_qty.required'      => 'Please enter Maximum Stock',
            // 're_order_qty.required'       => 'Please enter Re-Order Quantity',
            // 'hsn_code.required'           => 'Please enter Hsn-Code',
            // 'rate_per_unit.required'      => 'Please enter Rate,        
        ]);

        DB::beginTransaction();
        try{
            $rawMaterialData=  RawMaterial::create([
                'raw_material'          => $request->raw_material,                
                'raw_material_group_id' => $request->raw_material_group_id,                
                'unit_id'               => $request->unit_id,
                'min_stock_qty'         => $request->min_stock_qty,
                'max_stock_qty'         => $request->max_stock_qty,
                're_order_qty '         => $request->re_order_qty,
                'hsn_code'              => $request->hsn_code,
                'rate_per_unit'         => $request->rate_per_unit,                
                'company_id'            => Auth::user()->company_id,
                'created_on'            => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id'    => Auth::user()->id
            ]);
    
            if($rawMaterialData->save()){
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
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(RawMaterial $rawMaterial,$id)
    {
        $unit = Unit::select('id','unit_name')->orderBy('unit_name','asc')->get();
        $hsn_code  = HsnCode::select('id','hsn_code')->orderBy('hsn_code','asc')->get();
        return view('edit.edit-raw-material')->with('id',$id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RawMaterial  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(RawMaterial $rawMaterial,Request $request,$id)
    {
        $rawMaterialData = RawMaterial::where('id','=',$id)->first();

        if($rawMaterialData){
            return response()->json([
                'raw_material' => $rawMaterialData,
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
    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $request->validate([
            'raw_material'          =>['required','max:255',Rule::unique('raw_materials')->ignore($request->id, 'id'),
            'raw_material_group_id' =>'required','max:255',            
            // 'min_stock_qty'      =>'required',
            // 'max_stock_qty'      =>'required',
            // 're_order_qty'       =>'required',
            // 'hsn_code'           =>'required',
            // 'rate_per_unit'      =>'required'
        ],
        ],
        [
            'raw_material.required'          => 'Please Enter Raw Material',
            'raw_material.max'               => 'Maximum 255 characters allowed',
            'raw_material_group_id.required' => 'Please Select Raw Material Group',            
            // 'min_stock_qty.required'      => 'Please enter Minimum Stock',
            // 'max_stock_qty.required'      => 'Please enter Maximum Stock',
            // 're_order_qty.required'       => 'Please enter Re-Order Quantity',
            // 'hsn_code.required'           => 'Please enter Hsn-Code',
            // 'rate_per_unit.required'      => 'Please enter Rate,
        ]);
        DB::beginTransaction();
        try{
            $rawMaterialData=  RawMaterial::where('id','=',$request->id)->update([
                'raw_material' => $request->raw_material,                
                'raw_material_group_id' => $request->raw_material_group_id,                
                'unit_id' => $request->unit_id,
                'min_stock_qty' => $request->min_stock_qty,
                'max_stock_qty' => $request->max_stock_qty,
                're_order_qty' => $request->re_order_qty,
                'hsn_code' => $request->hsn_code,
                'rate_per_unit' => $request->rate_per_unit,                
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id
            ]);
                
            if($rawMaterialData){
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
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    public function existsRawMaterial(Request $request){
        if($request->term != ""){
            $fdRawMaterial = RawMaterial::select('raw_material')->where('raw_material', 'LIKE', $request->term.'%')->groupBy('raw_material')->get();
            if($fdRawMaterial != null){
              
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdRawMaterial as $dsKey){

                    $output .= '<li parent-id="raw_material" list-id="item_list" class="list-group-item" tabindex="0">'.$dsKey->raw_material.'</li>';
                } 
                $output .= '</ul>';

                return response()->json([
                    'rawMaterialList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Raw Material available',
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
     * @param  \App\Models\RawMaterial  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // if($request->id == "1"){
        //      return response()->json([
        //         'response_code' => "0",
        //         'response_message' => "This is used for reference,you can't delete",
        //     ]);
        // }
        
        try{
            RawMaterial::destroy($request->id);
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
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

    public function exportCountry(Request $request){
        return Excel::download(new ExportCountry, 'country.xlsx');
    }
}
