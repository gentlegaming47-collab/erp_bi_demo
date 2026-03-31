<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierItemMapping;
use App\Models\Admin;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DataTables;
use Date;
use SplMinHeap;

class SupplierItemMappingController extends Controller
{

    public function manage()
    {
        return view('manage.manage-supplier_item_mapping');
    }

    public function create()
    {
        // $items = Item::orderBy('item_name', 'ASC')->get();
        $items = Item::where('status','active')->orderBy('item_name', 'ASC')->get();

        return view('add.add-supplier_item_mapping', compact('items'));
    }

    public function index()
    {
        $supplier_item_mapping = SupplierItemMapping::select([
            'suppliers.supplier_name','items.item_name','units.unit_name','items.item_code','supplier_item_mapping.created_on','supplier_item_mapping.last_on','supplier_item_mapping.created_by_user_id','supplier_item_mapping.last_by_user_id','supplier_item_mapping.supplier_id','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])

            ->leftJoin('items','items.id','=','supplier_item_mapping.item_id')
            ->leftJoin('suppliers','suppliers.id','=','supplier_item_mapping.supplier_id')
            ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
            ->leftJoin('units','units.id','=','items.unit_id')
            ->leftJoin('admin AS created_user', 'created_user.id', '=', 'supplier_item_mapping.created_by_user_id')
            ->leftJoin('admin AS last_user', 'last_user.id', '=', 'supplier_item_mapping.last_by_user_id');

          
            return DataTables::of($supplier_item_mapping)
    
            ->editColumn('created_by_user_id', function($supplier_item_mapping){
                if($supplier_item_mapping->created_by_user_id != null){
                    $created_by_user_id = Admin::where('id','=',$supplier_item_mapping->created_by_user_id)->first('user_name');
                    return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
                }else{
                    return '';
                }
            })
            ->filterColumn('created_by_user_id', function ($query, $keyword) {
                $query->where('created_user.user_name', 'like', "%{$keyword}%");
            })
            ->editColumn('last_by_user_id', function($supplier_item_mapping){
                if($supplier_item_mapping->last_by_user_id != null){
                    $last_by_user_id = Admin::where('id','=',$supplier_item_mapping->last_by_user_id)->first('user_name');
                    return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
                }else{
                    return '';
                }
            })
            ->filterColumn('last_by_user_id', function ($query, $keyword) {
                $query->where('last_user.user_name', 'like', "%{$keyword}%");
            })
            ->editColumn('created_on', function($supplier_item_mapping){
                if ($supplier_item_mapping->created_on != null) {
                    $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $supplier_item_mapping->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
                }else{
                    return '';
                }
            })
            ->filterColumn('supplier_item_mapping.created_on', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(supplier_item_mapping.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->editColumn('last_on', function($supplier_item_mapping){
                if ($supplier_item_mapping->last_on != null) {
                    $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $supplier_item_mapping->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
                }else{
                    return '';
                }
            })
            ->filterColumn('supplier_item_mapping.last_on', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(supplier_item_mapping.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->editColumn('item_name', function($supplier_item_mapping){ 
                if($supplier_item_mapping->item_name != ''){
                    $item_name = ucfirst($supplier_item_mapping->item_name);
                    return $item_name;
                }else{
                    return '';
                }
            })
            ->addColumn('options',function($supplier_item_mapping){            
                $action = "<div>";
                if(hasAccess("supplier_item_mapping","edit")){
                $action .="<a id='edit_a' href='".route('edit-supplier_item_mapping',['id' => base64_encode($supplier_item_mapping->supplier_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
                if(hasAccess("supplier_item_mapping","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
                $action .= "</div>";
                return $action;
            })
           
            ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options'])
            ->make(true);
    }

    public function store(Request $request)
    {
        

        $year_data = getCurrentYearData();
        DB::beginTransaction();
        try{

            $supplier_item_mapping =  SupplierItemMapping::where('supplier_id',$request->supplier_id)->update([
                'status' => 'D',
            ]);

            if(isset($request->items))
            {
                if($request->items != null)
                {
                    $itemIds = json_decode($request->items, true);
                    foreach ($itemIds as $key => $val) {

                        $supplier_item_mapping = SupplierItemMapping::where('supplier_id',$request->supplier_id)->where('item_id', $val['item_ids'])->get();

                        if($supplier_item_mapping->isEmpty()){
                  
                            $SupplierItemMapping = SupplierItemMapping::create([
                                'supplier_id'        => $request->supplier_id,
                                'item_id'            => $val['item_ids'],
                                'year_id'            => $year_data->id,
                                'company_id'         => Auth::user()->company_id,
                                'created_by_user_id' => Auth::user()->id,
                                'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString() 
                            ]);
                        }else{

                            $supplier_item_mapping = SupplierItemMapping::where('supplier_id',$request->supplier_id)->where('item_id', $val['item_ids'])->first();

                            if($supplier_item_mapping){               
                                $storeData =  SupplierItemMapping::where('supplier_id',$request->supplier_id)->where('item_id',$val['item_ids'])->update([
                                    'supplier_id'     => $request->supplier_id,             
                                    'item_id'         => $val['item_ids'],  
                                    'status'          => 'Y',
                                    'company_id'      => Auth::user()->company_id,
                                    'last_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                                    'last_by_user_id' => Auth::user()->id
                                ]);   
                            }else{
                                $storeData=  SupplierItemMapping::create([
                                    'supplier_id'        => $request->supplier_id,             
                                    'item_id'            => $val['item_ids'],                      
                                    'status'             => 'Y',
                                    'company_id'         => Auth::user()->company_id,
                                    'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                                    'created_by_user_id' => Auth::user()->id,
                                ]);
    
                            }
                            
                        }            
                    }
                }  


                $item = SupplierItemMapping::where('supplier_id',$request->supplier_id)->where('status','D')->delete();
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Inserted Successfully.',
                ]);     
            }else{
                DB::rollBack();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Not Inserted.',
                ]);
            } 
        }catch(\Exception $e)
        {
            // dd($e->getLine());
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Not Inserted',
            ]);
        }
    }

    public function show(SupplierItemMapping $supplier_item_mapping, $id)
    {      
        $getData = SupplierItemMapping::where('supplier_id', '=', base64_decode($id))->get();

        $changedItemIds = SupplierItemMapping::
        leftJoin('items', 'items.id', '=', 'supplier_item_mapping.item_id')
        ->where('supplier_item_mapping.supplier_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive');
        })
        ->pluck('supplier_item_mapping.item_id')
        ->toArray();

        $items = Item::select('items.id','items.item_name','items.item_code','units.unit_name','item_groups.item_group_name')
        ->leftJoin('item_groups', 'item_groups.id', '=', 'items.item_group_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')
        ->where('status','active')
        ->where('fitting_item', 'no')
        ->orwhereIn('items.id',$changedItemIds)
        ->orderBy('items.id', 'ASC')->get();      

        return view('edit.edit-supplier_item_mapping', compact('items', 'getData', 'id'));
    }

    public function edit(SupplierItemMapping $supplier_item_mapping, Request $request, $id)
    {

        $supplier_item_mapping = SupplierItemMapping::where('supplier_id', $id)->get();

        if($supplier_item_mapping != null){
            $supplier_item_mapping->each(function ($item) use (&$isAnyPartInUse) {      

                    $isFound = PurchaseOrderDetails::
                    leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
                    ->where('purchase_order.supplier_id','=',$item->supplier_id)
                    ->where('purchase_order_details.item_id','=',$item->item_id)
                    ->first();    

                    $isPRFound = PurchaseRequisitionDetails::where('purchase_requisition_details.supplier_id','=',$item->supplier_id)
                    ->where('purchase_requisition_details.item_id','=',$item->item_id)
                    ->first();    
                    

                    if($isFound != null || $isPRFound != ""){
                        $item->in_use = true;                     
                      

                    }else{
                        $item->in_use = false;                     
                    }
                  
                    return $item;

            })->values();
        }

     
        if ($supplier_item_mapping ) {
         
            return response()->json([
                'supplier_item_mapping' => $supplier_item_mapping,
                'response_code' => '1',
                'response_message' => '',
            ]);
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

    public function update(Request $request)
    {

        $validated = $request->validate([
            'supplier_id'          =>'required'                 
        ],
        [
            'supplier_id.required' => 'Please Select Supplier '        
        ]);
        DB::beginTransaction();
        try{

            $supplier_item_mapping =  SupplierItemMapping::where('supplier_id',$request->id)->update([
                'status' => 'D',
            ]);
              
           $convertjson =  $request->items = json_decode($request->items, true);
            foreach($convertjson as $ckey=> $cval){
               
                $supplier_item_mapping = SupplierItemMapping::where('supplier_id',$request->supplier_id)->where('item_id', $cval['item_ids'])->get();
                
                if(empty($supplier_item_mapping)){
                    $storeData=  SupplierItemMapping::create([
                        'supplier_id'        => $request->supplier_id,             
                        'item_id'            => $cval['item_ids'],                     
                        'status'             => 'Y',
                        'company_id'         => Auth::user()->company_id,
                        'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                        'created_by_user_id' => Auth::user()->id,
                    ]);
    
                }else{
                    
                    $supplier_item_mapping = SupplierItemMapping::where('supplier_id',$request->id)->where('supplier_id',$request->supplier_id)->where('item_id',$cval['item_ids'])->first();

                        if($supplier_item_mapping){               
                            $storeData =  SupplierItemMapping::where('supplier_id',$request->supplier_id)->where('item_id',$cval['item_ids'])->update([
                                'supplier_id'     => $request->supplier_id,             
                                'item_id'         => $cval['item_ids'],  
                                'status'          => 'Y',
                                'company_id'      => Auth::user()->company_id,
                                'last_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                                'last_by_user_id' => Auth::user()->id
                            ]);   
                        }else{
                            $storeData=  SupplierItemMapping::create([
                                'supplier_id'        => $request->supplier_id,             
                                'item_id'            => $cval['item_ids'],                      
                                'status'             => 'Y',
                                'company_id'         => Auth::user()->company_id,
                                'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                                'created_by_user_id' => Auth::user()->id,
                            ]);

                        }
                    }
                }
            
                $item = SupplierItemMapping::where('supplier_id',$request->id)->where('status','D')->delete();

            
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                ]);                
           

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

    public function destroy(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try{
            $pid = PurchaseOrder::where('supplier_id',$request->id)->exists();
            if(!$pid){
                SupplierItemMapping::where('supplier_id','=',$request->id)->delete();
            }else{
                DB::rollBack();    
                // $error_msg = "This is used somewhere, you can't delete";
                $error_msg = "You Can't Delete, Supplier Item Mapping Is Used In Purchase Order.";
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $error_msg,
                ]);
            }
            // $prid = PurchaseRequisition::where('supplier_id',$request->id)->exists();
            // if(!$prid){
            //     SupplierItemMapping::where('supplier_id','=',$request->id)->delete();
            // }else{
            //     DB::rollBack();    
            //     // $error_msg = "This is used somewhere, you can't delete";
            //     $error_msg = "You Can't Delete, Supplier Item Mapping Is Used In Purchase Requisition.";
            //     return response()->json([
            //         'response_code' => '0',
            //         'response_message' => $error_msg,
            //     ]);
            // }
            // $prdid = PurchaseRequisitionDetails::where('supplier_id',$request->id)->exists();
            // if(!$prdid){
            //     SupplierItemMapping::where('supplier_id','=',$request->id)->delete();
            // }else{
            //     DB::rollBack();    
            //     // $error_msg = "This is used somewhere, you can't delete";
            //     $error_msg = "You Can't Delete, Supplier Item Mapping Is Used In Purchase Requisition.";
            //     return response()->json([
            //         'response_code' => '0',
            //         'response_message' => $error_msg,
            //     ]);
            // }



            SupplierItemMapping::where('supplier_id','=',$request->id)->delete();
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

    public function getSupplierItems(Request $request)
    {
        if(isset($request->supplier_id))
        {
            $getSalesRate = SupplierItemMapping::where('supplier_id', $request->supplier_id)->get();

            $changedItemIds = [];

            if($getSalesRate != null){
                $getSalesRate->each(function ($item) use (&$changedItemIds ,&$isAnyPartInUse) {      
    
                        $isFound = PurchaseOrderDetails::
                        leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
                        ->where('purchase_order.supplier_id','=',$item->supplier_id)
                        ->where('purchase_order_details.item_id','=',$item->item_id)
                        ->first(); 

                        $isPRFound = PurchaseRequisitionDetails::where('purchase_requisition_details.supplier_id','=',$item->supplier_id)
                        ->where('purchase_requisition_details.item_id','=',$item->item_id)
                        ->first();   

                        
    
                        if($isFound != null || $isPRFound != null){
                            $item->in_use = true;                     
                          
    
                        }else{
                            $item->in_use = false;                     
                        }

                       
                        $changedItemId = Item::where('id', $item->item_id)
                        ->where(function($query) {
                            $query->where('items.status', 'deactive');                     
                        })
                        ->pluck('id')
                        ->first();
                        if ($changedItemId) {
                            $changedItemIds[] = $changedItemId; // Now it works
                        }
                        
                        return $item;
    
                })->values();
            }

            if($changedItemIds){
                $item = Item::select('items.id','items.item_name','items.item_code','item_groups.item_group_name','units.unit_name')
                ->leftJoin('item_groups', 'item_groups.id', '=','items.item_group_id')
                ->leftJoin('units','units.id','=','items.unit_id')
                ->whereIN('items.id',$changedItemIds)->get();
            }else{
                $item = '';
            }
    

            if(!empty($getSalesRate))
            {
                return response()->json([
                    'response_code' => 1,
                    'LastItems' => $getSalesRate,
                    'item' => $item,
                ]);
            }else{
                return response()->json([
                    'response_code' => 0,
                    'response_message' => "No Items Found",
                ]);
            }
        }else{
                return response()->json([
                    'response_code' => 0,
                    'response_message' => "No Items Found",
                ]);
        }
    }
}