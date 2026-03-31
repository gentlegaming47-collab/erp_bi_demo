<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemRawMaterialMappingDetail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Admin;
use App\Models\ItemAssemblyProduction;

use App\Models\Item;
use App\Models\ItemDetails;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Transporter;
use App\Models\RawMaterial;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportItemtoItemMapping;

class ItemRawMaterialMappingController extends Controller
{
    
    public function manage()
    {
        return view('manage.manage-item-raw-material-mapping');
    }

    public function index(Item $Item,Request $request,DataTables $dataTables)
    {
        $item_Raw_Material_Mapping = ItemRawMaterialMappingDetail::select([
            'item_raw_material_mapping_details.id',
            'item_raw_material_mapping_details.item_id',
            'items.item_name',
            'item_groups.item_group_name',
            'items.item_code',
            'units.unit_name',
            'm_items.rate_per_unit as m_rate_per_unit',
            'item_raw_material_mapping_details.created_on',
            'item_raw_material_mapping_details.created_by_user_id',
            'item_raw_material_mapping_details.last_by_user_id',
            'item_raw_material_mapping_details.last_on',
            // 'm_items.item_name as m_items',
            // 'item_raw_material_mapping_details.raw_material_qty',
            DB::raw('GROUP_CONCAT(m_items.item_name SEPARATOR "|") as m_items'),
            DB::raw('GROUP_CONCAT(item_raw_material_mapping_details.raw_material_qty SEPARATOR "|") as raw_material_qty'),
            DB::raw('GROUP_CONCAT(raw_material_units.unit_name SEPARATOR "|") as map_item_unit'),
            'item_details.secondary_item_name',
            'item_raw_material_mapping_details.item_details_id',
            'created_user.user_name as created_by_name',
            'last_user.user_name as last_by_name'
        ])
        ->join('items', 'items.id', 'item_raw_material_mapping_details.item_id')
        ->leftJoin('items as m_items', 'm_items.id', 'item_raw_material_mapping_details.raw_material_id')
        ->join('units','units.id','=','items.unit_id')               
        ->join('item_groups','item_groups.id','=','items.item_group_id')
        ->leftjoin('item_details','item_details.item_details_id','=','item_raw_material_mapping_details.item_details_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'item_raw_material_mapping_details.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'item_raw_material_mapping_details.last_by_user_id')
        ->leftJoin('units as raw_material_units', 'raw_material_units.id', '=', 'm_items.unit_id')
        ->groupBy('items.item_name','item_details.secondary_item_name');

        
        return DataTables::of($item_Raw_Material_Mapping)
        ->editColumn('created_by_user_id', function($item_Raw_Material_Mapping){
            if($item_Raw_Material_Mapping->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$item_Raw_Material_Mapping->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($item_Raw_Material_Mapping){
            if($item_Raw_Material_Mapping->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$item_Raw_Material_Mapping->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('created_on', function($item_Raw_Material_Mapping){
            if ($item_Raw_Material_Mapping->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $item_Raw_Material_Mapping->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('item_raw_material_mapping_details.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_raw_material_mapping_details.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($item_Raw_Material_Mapping){
            if ($item_Raw_Material_Mapping->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $item_Raw_Material_Mapping->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('item_raw_material_mapping_details.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_raw_material_mapping_details.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })

        
        // ->editColumn('item_name', function($item_Raw_Material_Mapping){
        //     if($item_Raw_Material_Mapping->item_name != ''){
        //         $item_name = ucfirst($item_Raw_Material_Mapping->item_name);
        //         return $item_name;
        //     }else{
        //         return '';
        //     }
        //    // return Str::limit($item_Raw_Material_Mapping->item_name, 50);
        // })

        ->addColumn('name', function($item_Raw_Material_Mapping){           
            return $item_Raw_Material_Mapping->item_details_id != null ? 
            ucfirst($item_Raw_Material_Mapping->secondary_item_name) : ucfirst($item_Raw_Material_Mapping->item_name);
        })
        ->filterColumn('name', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('item_details.secondary_item_name', 'like', "%$keyword%")
                  ->orWhere('items.item_name', 'like', "%$keyword%");
            });
        })
        ->editColumn('item_group_name', function($item_Raw_Material_Mapping){
            if($item_Raw_Material_Mapping->item_group_name != ''){
                $item_group_name = ucfirst($item_Raw_Material_Mapping->item_group_name);
                return $item_group_name;
            }else{
                return '';
            }
            //return Str::limit($item_Raw_Material_Mapping->item_group_name, 50);
        })
        ->editColumn('item_code', function($item_Raw_Material_Mapping){
            if($item_Raw_Material_Mapping->item_code != ''){
                $item_code = ucfirst($item_Raw_Material_Mapping->item_code);
                return $item_code;
            }else{
                return '';
            }
            //return Str::limit($item_Raw_Material_Mapping->item_code, 50);
        })
        ->editColumn('unit_name', function($item_Raw_Material_Mapping){
            if($item_Raw_Material_Mapping->unit_name != ''){
                $unit_name = ucfirst($item_Raw_Material_Mapping->unit_name);
                return $unit_name;
            }else{
                return '';
            }
            //return Str::limit($item_Raw_Material_Mapping->unit_name, 50);
        })
        ->editColumn('raw_material', function($item_Raw_Material_Mapping){
            if($item_Raw_Material_Mapping->raw_material != ''){
                $raw_material = ucfirst($item_Raw_Material_Mapping->raw_material);
                return $raw_material;
            }else{
                return '';
            }
           // return Str::limit($item_Raw_Material_Mapping->raw_material, 50);
        })
        ->editColumn('raw_material_group', function($item_Raw_Material_Mapping){
            if($item_Raw_Material_Mapping->raw_material_group != ''){
                $raw_material_group = ucfirst($item_Raw_Material_Mapping->raw_material_group);
                return $raw_material_group;
            }else{
                return '';
            }
           // return Str::limit($item_Raw_Material_Mapping->raw_material_group, 50);
        })
        ->editColumn('re_order_qty', function($item_Raw_Material_Mapping){
            return helpFormatWeight($item_Raw_Material_Mapping->raw_material_qty);

          //  return Str::limit($item_Raw_Material_Mapping->raw_material_qty, 50);
        })
        ->editColumn('rate_per_unit', function($item_Raw_Material_Mapping){
            return helpFormatWeight($item_Raw_Material_Mapping->rate_per_unit);

            // return Str::limit($item_Raw_Material_Mapping->rate_per_unit, 50);
        })
        // ->editColumn('created_on', function($item_Raw_Material_Mapping){
        //     if ($item_Raw_Material_Mapping->created_on != null) {
        //         $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $item_Raw_Material_Mapping->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
        //     }else{
        //         return '';
        //     }
        // })
        // ->filterColumn('item_raw_material_mapping_details.created_on', function ($query, $keyword) {
        //     $query->whereRaw("DATE_FORMAT(item_raw_material_mapping_details.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        // })
        // ->editColumn('last_on', function($item_Raw_Material_Mapping){
        //     if ($item_Raw_Material_Mapping->last_on != null) {
        //         $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $item_Raw_Material_Mapping->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
        //     }else{
        //         return '';
        //     }
        // })
        // ->filterColumn('item_raw_material_mapping_details.last_on', function ($query, $keyword) {
        //     $query->whereRaw("DATE_FORMAT(item_raw_material_mapping_details.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        // })
        ->addColumn('options',function($item_Raw_Material_Mapping){
            $action = "<div>";

            if($item_Raw_Material_Mapping->item_details_id != null){
                if(hasAccess("item_raw_material_mapping","edit")){
                $action .="<a id='edit_a' href='".route('edit-item_raw_material_mapping',['id' => base64_encode($item_Raw_Material_Mapping->item_id),'item_details_id' =>  base64_encode($item_Raw_Material_Mapping->item_details_id),]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }

            }else{
                if(hasAccess("item_raw_material_mapping","edit")){
                $action .="<a id='edit_a' href='".route('edit-item_raw_material_mapping',['id' => base64_encode($item_Raw_Material_Mapping->item_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            }
           
            if(hasAccess("item_raw_material_mapping","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','item_group_name','item_code','unit_name','raw_material', 'raw_material_group', 're_order_qty', 'rate_per_unit', 'options','name'])
        ->make(true);
    }

    public function create()
    {
       

        $itemMaterial = Item::select(['items.*','item_groups.item_group_name', 'units.unit_name'])
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('items.status', 'active')            
        ->where('items.require_raw_material_mapping',"No")  
        ->where('items.secondary_unit','=',"No")  
        ->where('items.fitting_item',"No")->get();  
       
        $itemName = Item::where('require_raw_material_mapping', 'Yes')->select('item_name')->get();

        return view('add.add-item-raw-material-mapping', compact('itemMaterial'));
    }


    public function store(Request $request){

        $validated = $request->validate([            
              'item_id'=>'required' 
        ],
        [
            'item_id.required' => 'Please Select Item Name',
        ]);


        DB::beginTransaction();

        try {

            $itemId = $request->item_id;
            $itemDetailsId = $request->item_details_id;
            $companyId = Auth::user()->company_id;
            $userId = Auth::user()->id;

            $materialData = json_decode($request->item_data, true);

            $baseQuery = ItemRawMaterialMappingDetail::query();

            // Define the base column for filtering
            if (!empty($itemDetailsId)) {
                $baseQuery->where('item_details_id', $itemDetailsId);
            } else {
                $baseQuery->where('item_id', $itemId);
            }

            // Mark existing as deleted
            $baseQuery->update(['status' => 'D']);

            $processedMaterialIds = [];

            foreach ($materialData as $material) {
                $materialId = $material['material_ids'];
                $materialQty = $material['raw_material_qty'];

                $processedMaterialIds[] = $materialId;

                $existing = ItemRawMaterialMappingDetail::where(function ($query) use ($itemDetailsId, $itemId) {
                    if (!empty($itemDetailsId)) {
                        $query->where('item_details_id', $itemDetailsId);
                    } else {
                        $query->where('item_id', $itemId);
                    }
                })->where('raw_material_id', $materialId)->first();

                $data = [
                    'item_id' => $itemId,
                    'item_details_id' => $itemDetailsId,
                    'raw_material_id' => $materialId,
                    'raw_material_qty' => $materialQty,
                    'status' => 'Y',
                    'company_id' => $companyId,
                ];

                if ($existing) {
                    $data['last_on'] = Carbon::now('Asia/Kolkata')->toDateTimeString();
                    $data['last_by_user_id'] = $userId;
                    $existing->update($data);
                } else {
                    $data['created_on'] = Carbon::now('Asia/Kolkata')->toDateTimeString();
                    $data['created_by_user_id'] = $userId;
                    ItemRawMaterialMappingDetail::create($data);
                }
            }

            // Delete remaining (status='D') records if needed
            $deleteQuery = ItemRawMaterialMappingDetail::where('status', 'D');
            if (!empty($itemDetailsId)) {
                $deleteQuery->where('item_details_id', $itemDetailsId);
            } else {
                $deleteQuery->where('item_id', $itemId);
            }
            $deleteQuery->delete();

            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Inserted Successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occurred: Record Not Inserted',
                'original_error' => $e->getMessage(),
            ]);
        }
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'item_id'=>'required'                 
    //     ],
    //     [
    //         'item_id.required' => 'Please enter Item Name'        
                       
    //     ]);
    //     DB::beginTransaction();

    //     try{    
    //         // dd($request->item_id);
    //         // dd($request);
    //         $OrdrerDetails =  ItemRawMaterialMappingDetail::where('item_id',$request->item_id)->update([
    //             'status' => 'D',
    //         ]);

    //         $request->item_data = json_decode($request->item_data, true);
    //         foreach($request->item_data as $mkey=>$mval){
    //             // dd($mval['material_ids']);
    //             $item = ItemRawMaterialMappingDetail::where('raw_material_id',$mval['material_ids'])->get();

    //             // dd($item->isEmpty());

    //             if($item->isEmpty()){
    //                 // dd($mval['material_ids'],'of');
    //                 $storeData=  ItemRawMaterialMappingDetail::create([
    //                     'item_id' => $request->item_id,             
    //                     'raw_material_id' =>$mval['material_ids'],
    //                     'raw_material_qty' => $mval['raw_material_qty'],
    //                     'status' => 'Y',
    //                     'company_id' => Auth::user()->company_id,
    //                     'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
    //                     'created_by_user_id' => Auth::user()->id,
    //                 ]);
    
    //             }else{
                    
    //                 $itemMaterial = ItemRawMaterialMappingDetail::where('item_id',$request->item_id)->where('raw_material_id',$mval['material_ids'])->first();

    //                 // dd($itemMaterial);
    //                 // dd($itemMaterial->raw_material_qty == $mval['raw_material_qty']);
    //                 if($itemMaterial !='' && $itemMaterial->raw_material_qty != $mval['raw_material_qty'] && $mval['material_ids']){
    //                     $SalesOrdrerDetails =  ItemRawMaterialMappingDetail::where('item_id',$request->item_id)->where('raw_material_id',$mval['material_ids'])->update([
    //                         'item_id' => $request->item_id,             
    //                         'raw_material_id' => $mval['material_ids'],
    //                         'raw_material_qty' => $mval['raw_material_qty'],
    //                         'status' => 'Y',
    //                         'company_id' => Auth::user()->company_id,
    //                         'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
    //                         'last_by_user_id' => Auth::user()->id
    //                     ]);                
    //                 }
    //                 else{
    //                         // $storeData=  ItemRawMaterialMappingDetail::create([
    //                         //     'item_id' => $request->item_id,             
    //                         //     'raw_material_id' =>$mval['material_ids'],
    //                         //     'raw_material_qty' => $mval['raw_material_qty'],
    //                         //     'status' => 'Y',
    //                         //     'company_id' => Auth::user()->company_id,
    //                         //     'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
    //                         //     'created_by_user_id' => Auth::user()->id,
    //                         // ]);

    //                         $SalesOrdrerDetails =  ItemRawMaterialMappingDetail::where('item_id',$request->item_id)->where('raw_material_id',$mval['material_ids'])->update([
    //                             'item_id' => $request->item_id,             
    //                             'raw_material_id' => $mval['material_ids'],
    //                             'raw_material_qty' => $mval['raw_material_qty'],
    //                             'status' => 'Y',
    //                             // 'company_id' => Auth::user()->company_id,
    //                             // 'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
    //                             // 'last_by_user_id' => Auth::user()->id
    //                         ]);             
    //                     }                          
    //                 }
    //             }
    //         $item = ItemRawMaterialMappingDetail::where('item_id',$request->item_id)->where('status','D')->delete();
    //         DB::commit();
    //                  return response()->json([
    //                         'response_code' => '1',
    //                         'response_message' => 'Record Inserted Successfully.',
    //         ]);
                 
    //     }catch(\Exception $e)
    //     {
    //         DB::rollBack();
    //         return response()->json([
    //             'response_code' => '0',
    //             'response_message' => 'Error Occured Record Not Inserted',
    //             'original_error' => $e->getMessage()
    //         ]);
    //     }
    // }
    public function show(ItemRawMaterialMappingDetail $ItemRawMaterialMappingDetail, $id)
    {
        $item_details_id = request()->query('item_details_id');
       
        // $rawMaterial = RawMaterial::join('raw_material_groups', 'raw_material_groups.id', 'raw_materials.raw_material_group_id')->select('raw_materials.id as r_id', 'raw_material_groups.id as rg_id', 'raw_materials.raw_material', 'raw_material_groups.raw_material_group_nm', 'raw_materials.rate_per_unit')->get();   
        
        if($item_details_id != null){
            $getData = ItemRawMaterialMappingDetail::where('item_details_id', '=', base64_decode($item_details_id))->get();    
        }else{
            $getData = ItemRawMaterialMappingDetail::where('item_id', '=', base64_decode($id))->get();          
        }


    
        // $changedItemIds = ItemRawMaterialMappingDetail::
        // leftJoin('items', 'items.id', '=', 'item_raw_material_mapping_details.raw_material_id')
        // ->where('item_raw_material_mapping_details.item_id', base64_decode($id))
        // ->where(function($query) {
        //     $query->where('items.status', 'deactive');
        // })
        // ->pluck('item_raw_material_mapping_details.raw_material_id')
        // ->toArray();

        $rawMaterial = Item::select(['items.*','item_groups.item_group_name', 'units.unit_name'])
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units', 'units.id', 'items.unit_id')
        ->where('items.status', 'active')        
        ->where('items.require_raw_material_mapping',"No") 
        ->where('items.secondary_unit','=',"No")  
        ->where('items.fitting_item',"No")
        // ->orwhereIn('items.id',$changedItemIds)
        ->get();          
    
        return view('edit.edit-item-raw-material-mapping', compact('rawMaterial', 'getData', 'id' , 'item_details_id'));
    }

    public function edit(ItemRawMaterialMappingDetail $ItemRawMaterialMappingDetail, Request $request, $id)
    {
        
        $item_Raw_Material_Mapping = ItemRawMaterialMappingDetail::where('item_id','=',$id)->get();

        // if($item_Raw_Material_Mapping->isNotEmpty()){
        //     $changedItemIds = [];
        //     foreach($item_Raw_Material_Mapping as $cpkey => $cpval){
        //         $changedItemId = Item::where('id', $cpval->raw_material_id)
        //         ->where(function($query) {
        //             $query->where('items.status', 'deactive');                     
        //         })
        //         ->pluck('id')
        //         ->first();
        //         if ($changedItemId) {
        //             $changedItemIds[] = $changedItemId; // Now it works
        //         }
        //     }

        //     if($changedItemIds){
        //         $item = Item::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name')
        //         ->leftJoin('item_groups', 'item_groups.id', '=','items.item_group_id')
        //         ->leftJoin('units','units.id','=','items.unit_id')
        //         ->whereIN('items.id',$changedItemIds)->get();
        //     }else{
        //         $item = '';
        //     }

        // }else{
        //     $item = '';
        // }

       $item = '';
        if($item_Raw_Material_Mapping){
            return response()->json([
                'item_Raw_Material_Mapping' => $item_Raw_Material_Mapping,
                'item' => $item,
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

    public function editDetails(ItemRawMaterialMappingDetail $ItemRawMaterialMappingDetail, Request $request, $id)
    {
        
        $item_Raw_Material_Mapping = ItemRawMaterialMappingDetail::where('item_details_id','=',$id)->get();

        if($item_Raw_Material_Mapping->isNotEmpty()){
            $changedItemIds = [];
            foreach($item_Raw_Material_Mapping as $cpkey => $cpval){
                $changedItemId = Item::where('id', $cpval->raw_material_id)
                ->where(function($query) {
                    $query->where('items.status', 'deactive');                     
                })
                ->pluck('id')
                ->first();
                if ($changedItemId) {
                    $changedItemIds[] = $changedItemId; // Now it works
                }
            }

            if($changedItemIds){
                $item = Item::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name')
                ->leftJoin('item_groups', 'item_groups.id', '=','items.item_group_id')
                ->leftJoin('units','units.id','=','items.unit_id')
                ->whereIN('items.id',$changedItemIds)->get();
            }else{
                $item = '';
            }

        }else{
            $item = '';
        }

       
        if($item_Raw_Material_Mapping){
            return response()->json([
                'item_Raw_Material_Mapping' => $item_Raw_Material_Mapping,
                'item' => $item,
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

    public function update(Request $request){
        $validated = $request->validate([            
              'item_id'=>'required' 
        ],
        [
            'item_id.required' => 'Please Select Item Name',
        ]);

        DB::beginTransaction();

        try {
            $itemId = $request->item_id;
            $itemDetailsId = $request->item_details_id ?? null;
            $companyId = Auth::user()->company_id;
            $userId = Auth::user()->id;
            $timestamp = Carbon::now('Asia/Kolkata');

            $itemData = json_decode($request->item_data, true);

            // Determine base query
            $baseQuery = ItemRawMaterialMappingDetail::query();
            $baseQuery->where($itemDetailsId ? 'item_details_id' : 'item_id', $itemDetailsId ?? $itemId);
            
            // Soft-delete existing mappings
            $baseQuery->update(['status' => 'D']);

            foreach ($itemData as $material) {
                $rawMaterialId = $material['material_ids'];
                $rawMaterialQty = $material['raw_material_qty'];

                $conditionColumn = $itemDetailsId ? 'item_details_id' : 'item_id';
                $conditionValue = $itemDetailsId ?? $itemId;

                $existing = ItemRawMaterialMappingDetail::where($conditionColumn, $conditionValue)
                    ->where('raw_material_id', $rawMaterialId)
                    ->first();

                $data = [
                    'item_id' => $itemId,
                    'item_details_id' => $itemDetailsId,
                    'raw_material_id' => $rawMaterialId,
                    'raw_material_qty' => $rawMaterialQty,
                    'status' => 'Y',
                    'company_id' => $companyId,
                ];

                if ($existing) {
                    $data['last_on'] = $timestamp;
                    $data['last_by_user_id'] = $userId;
                    $existing->update($data);
                } else {
                    $data['created_on'] = $timestamp;
                    $data['created_by_user_id'] = $userId;
                    ItemRawMaterialMappingDetail::create($data);
                }
            }

            // Delete previously soft-deleted entries
            ItemRawMaterialMappingDetail::where($itemDetailsId ? 'item_details_id' : 'item_id', $itemDetailsId ?? $itemId)
                ->where('status', 'D')
                ->delete();

            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Updated Successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occurred: Record Not Updated',
                'original_error' => $e->getMessage(),
            ]);
        }
    }


    public function destory(Request $request)
    {
        DB::beginTransaction();
       try{
        //    ItemRawMaterialMappingDetail::destroy($request->id);

            $item_ass_pro_id = ItemAssemblyProduction::where('item_id',$request->id)->get();
            if($item_ass_pro_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Item Is Used In Item Production (Assembly).",
                ]);
            }

           ItemRawMaterialMappingDetail::where('item_id',$request->id)->delete();
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

    public function destoryDetails(Request $request)
    {
        DB::beginTransaction();
       try{
        //    ItemRawMaterialMappingDetail::destroy($request->id);

            $item_ass_pro_id = ItemAssemblyProduction::where('item_id',$request->id)->get();
            if($item_ass_pro_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Item Is Used In Item Production (Assembly).",
                ]);
            }

           ItemRawMaterialMappingDetail::where('item_details_id',$request->id)->delete();
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



    // used to fetch the group_name, unit, and item code 
    public function fetch_Groupname_Code_Unit(Request $request)
    {
       
        $itemRawData = Item::join('item_groups', 'item_groups.id', 'items.item_group_id')->join('units','units.id', 'items.unit_id')->select(['items.id','item_groups.id','item_group_name','item_code as item_group_code', 'unit_name'])->where('items.id', '=', $request->id)->first();
      
        
        return response()->json([
            'response_code' => 1,
            'item_data' => $itemRawData
        ]);        
    }

    public function getExistItemQty(Request $request){
        $itemMaterial = ItemRawMaterialMappingDetail::select('raw_material_id','raw_material_qty','units.unit_name')
        ->leftJoin('items', 'items.id', '=','item_raw_material_mapping_details.item_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('item_id',$request->id)->get();

        if($itemMaterial->isNotEmpty()){
            $changedItemIds = [];
            foreach($itemMaterial as $cpkey => $cpval){
                
                $changedItemId = Item::where('id', $cpval->raw_material_id)
                ->where(function($query) {
                    $query->where('items.status', 'deactive');                     
                })
                ->pluck('id')
                ->first();
                if ($changedItemId) {
                    $changedItemIds[] = $changedItemId; // Now it works
                }  
            }

            if($changedItemIds){
                $item = Item::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name')
                ->leftJoin('item_groups', 'item_groups.id', '=','items.item_group_id')
                ->leftJoin('units','units.id','=','items.unit_id')
                ->whereIN('items.id',$changedItemIds)->get();
            }else{
                $item = '';
            }

        }else{
            $item = '';
        }
        return response()->json([
            'response_code' => 1,
            'qty' => $itemMaterial,
            'item' => $item,
        ]);
    }


    public function getDetailExistItemQty(Request $request){
        $itemMaterial = ItemRawMaterialMappingDetail::select('raw_material_id','raw_material_qty','items.second_unit','units.unit_name')
        ->leftJoin('items', 'items.id', '=','item_raw_material_mapping_details.item_id')
        ->leftJoin('units', 'units.id', '=','items.second_unit')
        ->where('item_details_id',$request->item_details_id)->get();

        $secunit = ItemDetails::select('units.unit_name')
        ->leftJoin('items', 'items.id', '=','item_details.item_id')
        ->leftJoin('units', 'units.id', '=','items.second_unit')
        ->where('item_details_id',$request->item_details_id)
        ->first();

        if($itemMaterial->isNotEmpty()){
            $changedItemIds = [];
            foreach($itemMaterial as $cpkey => $cpval){
                
                $changedItemId = Item::where('id', $cpval->raw_material_id)
                ->where(function($query) {
                    $query->where('items.status', 'deactive');                     
                })
                ->pluck('id')
                ->first();
                if ($changedItemId) {
                    $changedItemIds[] = $changedItemId; // Now it works
                }  
            }

            if($changedItemIds){
                $item = Item::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name')
                ->leftJoin('item_groups', 'item_groups.id', '=','items.item_group_id')
                ->leftJoin('units','units.id','=','items.unit_id')
                ->whereIN('items.id',$changedItemIds)->get();
            }else{
                $item = '';
            }

        }else{
            $item = '';
        }
        return response()->json([
            'response_code' => 1,
            'qty' => $itemMaterial,
            'secunit' => $secunit,
            'item' => $item,
        ]);
    }

    public function getItemsDetails(Request $request){ 
        $item = ItemDetails::where('item_details.item_id',$request->item_id)->get();
        return response()->json([
            'response_code' => 1,
            'item' => $item,
        ]);

    }

    public function exportItemtoItemMapping(Request $request)
    {
        $searchData = [];
        $global = $request->input('global');
        $columns = $request->input('columns', []);

        if ($global && is_string($global)) {
            $searchData['global'] = trim($global);
        }
        if (is_array($columns)) {
            foreach ($columns as $idx => $val) {
                if (is_string($val) && strlen($val) <= 255) {
                    $searchData['columns'][$idx] = trim($val);
                }
            }
        }

        return Excel::download(new ExportItemtoItemMapping($searchData), 'Item to Item Mapping.xlsx');
    }
}