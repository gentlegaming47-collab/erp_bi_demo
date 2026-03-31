<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\PriceList;
use App\Models\PriceListDetails;
use App\Models\LocationStock;
use App\Models\Admin;
use App\Models\ItemDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Date;
use Illuminate\Support\Facades\Auth;
use Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportPriceList;

class PriceListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PriceList $price_list,Request $request,DataTables $dataTables)
    {
        $price_list = PriceList::select([
        'item_groups.item_group_name','items.item_name','units.unit_name','items.item_code','customer_groups.customer_group_name','price_list.created_on','price_list.last_on','price_list.created_by_user_id','price_list.last_by_user_id','price_list.pl_id','price_list_details.sales_rate','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('price_list_details', 'price_list_details.pl_id' ,'price_list.pl_id')
        ->leftJoin('customer_groups','customer_groups.id','=','price_list.customer_group_id')
        ->leftJoin('items','items.id','=','price_list_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'price_list.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'price_list.last_by_user_id')
        ->groupBy('customer_groups.id');
      
        return DataTables::of($price_list)

        ->editColumn('created_by_user_id', function($price_list){
            if($price_list->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$price_list->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($price_list){
            if($price_list->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$price_list->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('created_on', function($price_list){
            if ($price_list->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $price_list->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('price_list.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(price_list.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($price_list){
            if ($price_list->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $price_list->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('price_list.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(price_list.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('created_on', function($price_list){
            if ($price_list->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $price_list->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->editColumn('last_on', function($price_list){
            if ($price_list->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $price_list->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->addColumn('options',function($price_list){            
            $action = "<div>";
            if(hasAccess("price_list","edit")){
            $action .="<a id='edit_a' href='".route('edit-price_list',['id' => base64_encode($price_list->pl_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("price_list","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
       
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options'])
        ->make(true);
    }

    public function manage()
    {
        return view('manage.manage-price_list');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        return view('add.add-price_list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function getFittingItems(Request $request){
        $locationID = getCurrentLocation()->id;

        $item = Item::select(['items.*','item_groups.item_group_name','units.unit_name'])
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('items.id',$request->item)->first();
        // dd($item);

        $stock_qty = LocationStock::select('location_stock.stock_qty')->where('item_id',$request->item)->where('location_id',$locationID)->first();

        $sales_rate = PriceListDetails::select('sales_rate')->where('customer_group_id',$request->cust_id)->where('item_id',$request->item)->first();

        // $item_detail = ItemDetails::select('item_details.item_details_id','item_details.secondary_item_name','units.unit_name','location_stock_details.secondary_stock_qty')
        // ->leftJoin('location_stock_details','location_stock_details.item_details_id','=','item_details.item_details_id')
        // ->leftJoin('items','items.id','=','item_details.item_id')
        // ->leftJoin('units','units.id','=','items.second_unit')
        // ->where('item_details.item_id',$request->item)
        // ->where('location_stock_details.location_id',$locationID)
        // ->get();
                $item_detail = ItemDetails::select(
                'item_details.item_details_id',
                'item_details.secondary_item_name','item_details.secondary_qty',
                'units.unit_name',
                DB::raw('IFNULL(location_stock_details.secondary_stock_qty, 0) as secondary_stock_qty')

            )
            ->leftJoin('items','items.id','=','item_details.item_id')
            ->leftJoin('units','units.id','=','items.second_unit')
            ->leftJoin('location_stock_details', function($join) use ($locationID) {
                $join->on('location_stock_details.item_details_id','=','item_details.item_details_id')
                    ->where('location_stock_details.location_id', '=', $locationID);
            })
            ->where('item_details.item_id', $request->item)
            ->get();

        return response()->json([
            'response_code' => 1,
            'item' => $item,   
            'sales_rate' => $sales_rate,
            'stock_qty' => $stock_qty,
            'item_detail' => $item_detail,
        ]);

    }

    public function getAnyFittingItems(Request $request){
        $locationID = getCurrentLocation()->id;

        $item = Item::select(['items.*','item_groups.item_group_name','units.unit_name', 'price_list_details.sales_rate'])
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('price_list_details','price_list_details.item_id','=','items.id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('items.id',$request->item)->first();
        // dd($item);

        $stock_qty = LocationStock::select('location_stock.stock_qty')->where('item_id',$request->item)->where('location_id',$locationID)->first();

        $sales_rate = PriceListDetails::select('sales_rate')->where('customer_group_id',$request->cust_id)->where('item_id',$request->item)->first();

        return response()->json([
            'response_code' => 1,
            'item' => $item,   
            'sales_rate' => $sales_rate,
            'stock_qty' => $stock_qty,
        ]);

    }

    public function getExistItemRate(Request $request){
        $itemMaterial = PriceListDetails::select('item_id','sales_rate')->where('item_id',$request->id)->where('customer_group_id',$request->group_id)->first();
        return response()->json([
            'response_code' => 1,
            'rate' => $itemMaterial
        ]);
    }

     public function store(Request $request)
     {
        DB::beginTransaction();
         try{
           

            $checkCustomer = PriceList::where('customer_group_id', $request->customer_group_id)->first();
            
            if (!empty($checkCustomer) && $checkCustomer != null) {
             
                $price_list = PriceList::where('customer_group_id', $request->customer_group_id)->update([
                    'customer_group_id' => $request->customer_group_id,    
                    'company_id' => Auth::user()->company_id,
                    'last_by_user_id' => Auth::user()->id,
                    'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString()               
                ]);
                $plId = $checkCustomer->pl_id;
            } else {
                $price_list = PriceList::create([
                    'customer_group_id' => $request->customer_group_id,    
                    'company_id' => Auth::user()->company_id,
                    'created_by_user_id' => Auth::user()->id,
                    'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString()               
                ]); 
                $plId = $price_list->id;
            }

                
            $request->price_list_details = json_decode($request->price_list_details,true);
         
            if(isset($request->price_list_details) && !empty($request->price_list_details)){
                $priceData = PriceListDetails::where([ ['pl_id', $plId], ['customer_group_id', $request->customer_group_id]])->get();

            
                $priceListDetails = PriceListDetails::where([['pl_id', $plId],['customer_group_id', $request->customer_group_id]])->update(['status' => 'D',]);            

                foreach($request->price_list_details as $ctKey => $ctVal){

                    $price_list_details = PriceListDetails::
                    where([
                        ['item_id',  $ctVal['item_id']],
                        ['customer_group_id',  $request->customer_group_id],
                        ['pl_id', $plId],
                    ])->first();
                    
                        
                    
                    if($price_list_details != "" && $ctVal['sales_rate'] > '0.0')
                    {
                                
                        $price_list_details=  PriceListDetails::where('item_id', $ctVal['item_id'])->where('pl_id', $plId)->update([
                            'pl_id' => $price_list_details->pl_id,                                  
                            'item_id' => $ctVal['item_id'],
                            'sales_rate' => $ctVal['sales_rate'],
                            'customer_group_id' => $request->customer_group_id,
                            'status' => 'Y',
                        ]);
                        
                    }else{
                        if($ctVal['sales_rate'] > '0.0'){

                        $price_list_details=  PriceListDetails::create([
                            'pl_id' => $plId,
                            'item_id' => $ctVal['item_id'],
                            'sales_rate' => $ctVal['sales_rate'],
                            'customer_group_id' => $request->customer_group_id,
                            'status' => 'Y',
                        ]);
                        }
                    }
                }
            }

            $price_list_details = PriceListDetails::where([['customer_group_id', $request->customer_group_id], ['pl_id', $plId],['status', 'D']])->delete();

            $check_pl = PriceListDetails::where([['customer_group_id', $request->customer_group_id], ['pl_id', $plId]])->get();

            if($check_pl->isEmpty()){
                $price_list_details = PriceList::where('customer_group_id', $request->customer_group_id)->delete();
            }

             
                // if(isset($request->sales_rate))
                // {
                //     if($request->sales_rate != null)
                //     {
                       
                //             foreach($request->sales_rate as $skey => $sval)
                //             {
                                
                //                 if($sval > 0)
                //                 {
                                    
                                    
                //                     $price_list_details = PriceListDetails::
                //                     where([
                //                       ['item_id',  $request->item_id[$skey]],
                //                         ['customer_group_id',  $request->customer_group_id],
                //                         ['pl_id', $plId],
                //                     ])->first();
                                 
                                       
                                 
                //                     if($price_list_details != "")
                //                     {
                                               
                //                         $price_list_details=  PriceListDetails::where('item_id', $request->item_id[$skey])->where('pl_id', $plId)->update([
                //                             'pl_id' => $price_list_details->pl_id,                                  
                //                             'item_id' =>$request->item_id[$skey],
                //                             'sales_rate' => $request->sales_rate[$skey],
                //                             'customer_group_id' => $request->customer_group_id,
                //                         ]);
                                        
                //                     }else{
                //                         $price_list_details=  PriceListDetails::create([
                //                             'pl_id' => $plId,
                //                             'item_id' =>$request->item_id[$skey],
                //                             'sales_rate' => $request->sales_rate[$skey],
                //                             'customer_group_id' => $request->customer_group_id,
                //                         ]);
                //                     }
        
                //                 }
                //                 else{      
                                        
                //                     PriceListDetails::
                //                     where([
                //                       ['item_id',  $request->item_id[$skey]],
                //                         ['customer_group_id',  $request->customer_group_id],
                //                         ['pl_id', $plId],
                //                     ])->delete();
                                    
                                   
                //                 }   
                //             }
                        
                        
                        

                //     }
                
                   

                // } 
                        
           

            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Inserted Successfully.',
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
  
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PriceList $price_list, $id)
    {
        $getData = PriceListDetails::where('pld_id', '=', base64_decode($id))->get();
        return view('edit.edit-price_list', compact('getData', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PriceListDetails $price_list_details, Request $request, $id)
    {
        $price_list = PriceList::where('pl_id', $id)->first();
       $price_list_details = PriceListDetails::where('pl_id','=',$id)->get();
     
        if ($price_list_details ) {
         
            return response()->json([
                'price_list' => $price_list,
                'price_list_details' => $price_list_details,
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PriceListDetails $price_list_details)
    {
      DB::beginTransaction();
        try{
            $checkCustomer = PriceList::where('customer_group_id', $request->customer_group_id)->first();
            
            if (!empty($checkCustomer) && $checkCustomer != null) {
                $d = PriceList::where('customer_group_id', $request->customer_group_id)->get();

                $price_list = PriceList::where('customer_group_id', $request->customer_group_id)->update([
                    'customer_group_id' => $request->customer_group_id,    
                    'company_id' => Auth::user()->company_id,
                    'last_by_user_id' => Auth::user()->id,
                    'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString()               
                ]);
                $plId = $checkCustomer->pl_id;
            } else {
                $price_list = PriceList::create([
                    'customer_group_id' => $request->customer_group_id,    
                    'company_id' => Auth::user()->company_id,
                    'created_by_user_id' => Auth::user()->id,
                    'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString()               
                ]); 
                $plId = $price_list->id;
            }
            

             $request->price_list_details = json_decode($request->price_list_details,true);
         
            if(isset($request->price_list_details) && !empty($request->price_list_details)){
                $priceData = PriceListDetails::where([ ['pl_id', $plId], ['customer_group_id', $request->customer_group_id]])->get();

            
                $priceListDetails = PriceListDetails::where([['pl_id', $plId],['customer_group_id', $request->customer_group_id]])->update(['status' => 'D',]);                     

                     

                foreach($request->price_list_details as $ctKey => $ctVal){

                    $price_list_details = PriceListDetails::
                    where([
                        ['item_id',  $ctVal['item_id']],
                        ['customer_group_id',  $request->customer_group_id],
                        ['pl_id', $plId],
                    ])->first();
                    
                        
                    
                    if($price_list_details != "" && $ctVal['sales_rate'] > '0.00')
                    {
                                
                        $price_list_details=  PriceListDetails::where('item_id', $ctVal['item_id'])->where('pl_id', $plId)->update([
                            'pl_id' => $price_list_details->pl_id,                                  
                            'item_id' => $ctVal['item_id'],
                            'sales_rate' => $ctVal['sales_rate'],
                            'customer_group_id' => $request->customer_group_id,
                            'status' => 'Y',
                        ]);
                        
                    }else{

                        if($ctVal['sales_rate'] > '0.00'){

                        $price_list_details=  PriceListDetails::create([
                            'pl_id' => $plId,
                            'item_id' => $ctVal['item_id'],
                            'sales_rate' => $ctVal['sales_rate'],
                            'customer_group_id' => $request->customer_group_id,
                            'status' => 'Y',
                        ]);
                        }
                    }
                }
            }else{
                PriceList::where('pl_id','=',$request->id)->delete();
            }

            $price_list_details = PriceListDetails::where([['customer_group_id', $request->customer_group_id], ['pl_id', $plId],['status', 'D']])->delete();
            
            $check_pl = PriceListDetails::where([['customer_group_id', $request->customer_group_id], ['pl_id', $plId],['pl_id',$request->id]])->get();

            if($check_pl->isEmpty()){
                $price_list_details = PriceList::where('customer_group_id', $request->customer_group_id)->where('pl_id',$request->id)->delete();
            }
            
         
                // if(isset($request->sales_rate) && $request->sales_rate != null)
                // {
                //     foreach($request->sales_rate as $skey => $sval)
                //     {
                        
                //         if($sval > 0)
                //         {
                //             $price_list_details = PriceListDetails::
                //             where([
                //               ['item_id',  $request->item_id[$skey]],
                //                 ['customer_group_id',  $request->customer_group_id],
                //                 ['pl_id', $plId],
                //             ])->first();
                         
                               
                         
                //             if($price_list_details != "")
                //             {
                                       
                //                 $price_list_details=  PriceListDetails::where('item_id', $request->item_id[$skey])->where('pl_id', $plId)->update([
                //                     'pl_id' => $price_list_details->pl_id,                                  
                //                     'item_id' =>$request->item_id[$skey],
                //                     'sales_rate' => $request->sales_rate[$skey],
                //                     'customer_group_id' => $request->customer_group_id,
                //                 ]);
                                
                //             }else{
                //                 $price_list_details=  PriceListDetails::create([
                //                     'pl_id' => $plId,
                //                     'item_id' =>$request->item_id[$skey],
                //                     'sales_rate' => $request->sales_rate[$skey],
                //                     'customer_group_id' => $request->customer_group_id,
                //                 ]);
                //             }

                //         }
                //         else{
                //             PriceListDetails::
                //             where([
                //               ['item_id',  $request->item_id[$skey]],
                //                 ['customer_group_id',  $request->customer_group_id],
                //                 ['pl_id', $plId],
                //             ])->delete();
                //         }
                //     }

                    
                    DB::commit();
                    return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                    ]);
               
           
        }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Error Occured Record Not Inserted',
                    'original_error' => $e->getMessage()
                ]);
        }
    }
    // public function update(Request $request, PriceListDetails $price_list_details)
    // {

    //     try{
    //         $price_list =  PriceList::where("pl_id", "=", $request->id)->update([
    //             'customer_group_id'=>$request->customer_group_id,                
    //             'last_by_user_id' => Auth::user()->id,
    //             'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),    
    //         ]);

    //         if ($price_list) {

    //             $oldPriceList = PriceListDetails::where('pl_id', '=', $request->id)->get();
                
    //             if (isset($request->price_list_detail_id) && !empty($request->price_list_detail_id)) {
    //                 foreach ($request->price_list_detail_id as $sodKey => $sodVal) {
    //                     if($sodVal == "0"){
    //                         if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
    //                             $price_list_details=  PriceListDetails::create([
    //                                 'pl_id' => $request->id,
    //                                 'item_id' =>$request->item_id[$sodKey],
    //                                 'sales_rate' => $request->sales_rate[$sodKey],
    //                                 'customer_group_id' => $request->customer_group_id,
    //                             ]);
    //                         }
    //                     }else{
    //                         if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
    //                             if($request->sales_rate[$sodKey] == "0"){
    //                                 $price_list_details =  PriceListDetails::where('pld_id',$sodVal)->where('customer_group_id',$request->customer_group_id)->delete([
    //                                     'sales_rate' => $request->sales_rate[$sodKey],
    //                                 ]);
    //                             }else{
    //                                 $SalesOrdrerDetails =  PriceListDetails::where('pld_id',$sodVal)->update([
    //                                     'pl_id' => $request->id,
    //                                     'item_id' =>$request->item_id[$sodKey],
    //                                     'sales_rate' => $request->sales_rate[$sodKey],
    //                                     'customer_group_id' => $request->customer_group_id,
    //                                 ]);
    //                             }

    //                         }else{
    //                             PriceListDetails::where('pld_id', $sodVal)->delete();
    //                         }
    //                     }
    //                 }
    //             }

    //             DB::commit();
    //             return response()->json([
    //                 'response_code' => '1',
    //                 'response_message' => 'Record Updated Successfully.',
    //             ]);
    //             }
    //             else{
    //                 return response()->json([
    //                     'response_code' => '0',
    //                     'response_message' => 'Record Not Updated',
    //                 ]);
    //             }
    //             }
    //             catch(\Exception $e){
    //                     DB::rollBack();
    //                     return response()->json([
    //                         'response_code' => '0',
    //                         'response_message' => 'Error Occured Record Not Inserted',
    //                         'original_error' => $e->getMessage()
    //                     ]);
    //             }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            if(!empty($request->item_id))
            {
                PriceListDetails::where('item_id',$request->item_id)->delete();    
            }
            else{                
                PriceListDetails::where('pl_id',$request->id)->delete();
                PriceList::where('pl_id','=',$request->id)->delete();
            }
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

    
    
    public function getStockQty(Request $request)
    {
        if(isset($request->cust_id))
        {
            $getSalesRate = PriceListDetails::where('customer_group_id', $request->cust_id)->get();
         

            if($getSalesRate->isNotEmpty()){
                $changedItemIds = [];
                foreach($getSalesRate as $cpkey => $cpval){
                    $changedItemId = Item::where('id', $cpval->item_id)
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
    
            }

            if($getSalesRate->isNotEmpty())
            {
                return response()->json([
                    'response_code' => 1,
                    'salesRate' => $getSalesRate,
                    'item' => $item,
                ]);
            }else{
                return response()->json([
                    'response_code' => 0,
                    'response_message' => "No Sales Rate Found",
                ]);
            }
        }else{
                return response()->json([
                    'response_code' => 0,
                    'response_message' => "No Sales Rate Found",
                ]);
        }
    }

    public function exportPriceList(Request $request)
    {
        $searchData = [];
        $global = $request->input('global');
        $columns = $request->input('columns', []);

        if($global && is_string($global))
        {
            $searchData['global'] = trim($global);
        }

        if(is_array($columns))
        {
            foreach($columns as $idx => $val)
            {
                if(is_string($val) && strlen($val) <= 255)
                {
                    $searchData['columns'][$idx] = trim($val);
                }
            }
        }

        return Excel::download(new ExportPriceList($searchData), 'Price List.xlsx');
    }
}