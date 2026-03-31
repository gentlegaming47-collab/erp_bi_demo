<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemStockTransfer;
use App\Models\ItemStockTransferDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Date;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Item;
use App\Models\ItemDetails;

class ItemStockTransferController extends Controller
{
    public function manage()
    {
        return view('manage.manage-item_stock_transfer');
    }

    public function create()
    {
        return view('add.add-item_stock_transfer');
    }

    public function index(ItemStockTransfer $ItemStockTransfer,Request $request,DataTables $dataTables)
    {
       $year_data = getCurrentYearData();
       $location  = getCurrentLocation();

       $ItemStockTransfer = ItemStockTransfer::select(['item_stock_transfer.ist_id', 'item_stock_transfer.ist_number','item_stock_transfer.ist_sequence','item_stock_transfer.ist_date','items.item_name','item_details.secondary_item_name','item_stock_transfer.created_by_user_id','item_stock_transfer.last_by_user_id','item_stock_transfer.created_on','item_stock_transfer.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])

       ->leftJoin('items','items.id','=','item_stock_transfer.ist_item_id')
       ->leftJoin('item_details','item_details.item_details_id','=','item_stock_transfer.ist_item_details_id')
       ->leftJoin('admin AS created_user', 'created_user.id', '=', 'item_stock_transfer.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'item_stock_transfer.last_by_user_id')
       ->where('item_stock_transfer.year_id','=',$year_data->id)
       ->where('item_stock_transfer.current_location_id','=',$location->id)
       ->groupBy('item_stock_transfer.ist_number');

        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


            $ItemStockTransfer->whereDate('item_stock_transfer.ist_date','>=',$from);

            $ItemStockTransfer->whereDate('item_stock_transfer.ist_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $ItemStockTransfer->where('item_stock_transfer.ist_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $ItemStockTransfer->where('item_stock_transfer.ist_date','<=',$to);

        } 

      return DataTables::of($ItemStockTransfer)


       ->editColumn('ist_date', function($ItemStockTransfer){
           if ($ItemStockTransfer->ist_date != null) {
               $formatedDate3 = Date::createFromFormat('Y-m-d', $ItemStockTransfer->ist_date)->format(DATE_FORMAT); return $formatedDate3;
           }else{
               return '';
           }
       })
       ->filterColumn('item_stock_transfer.ist_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_stock_transfer.ist_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        
       ->editColumn('created_by_user_id', function($ItemStockTransfer){
           if($ItemStockTransfer->created_by_user_id != null){
               $created_by_user_id = Admin::where('id','=',$ItemStockTransfer->created_by_user_id)->first('user_name');
               return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
           }else{
               return '';
           }
       })
       ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('last_by_user_id', function($ItemStockTransfer){
           if($ItemStockTransfer->last_by_user_id != null){
               $last_by_user_id = Admin::where('id','=',$ItemStockTransfer->last_by_user_id)->first('user_name');
               return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
           }else{
               return '';
           }

       })
       ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
       ->editColumn('created_on', function($ItemStockTransfer){
           if ($ItemStockTransfer->created_on != null) {
               $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $ItemStockTransfer->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
           }else{
               return '';
           }
       })
       ->filterColumn('item_stock_transfer.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_stock_transfer.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('last_on', function($ItemStockTransfer){
           if ($ItemStockTransfer->last_on != null) {
               $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $ItemStockTransfer->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
           }else{
               return '';
           }
       })
       ->filterColumn('item_stock_transfer.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_stock_transfer.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
       ->editColumn('item_name', function($ItemStockTransfer){ 
            if($ItemStockTransfer->item_name != ''){
                $item_name = ucfirst($ItemStockTransfer->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

       ->addColumn('options',function($ItemStockTransfer){
           $action = "<div>";        
           if(hasAccess("item_stock_transfer","edit")){
           $action .="<a id='edit_a' href='".route('edit-item_stock_transfer',['id' => base64_encode($ItemStockTransfer->ist_id )]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
           }
           if(hasAccess("item_stock_transfer","delete")){
           $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
           }
           $action .= "</div>";
           return $action;
       })
       ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options'])
       ->make(true);
    }

    public function store(Request $request){

        //  dd($request->all());
        $year_data    = getCurrentYearData();
        $locationCode = getCurrentLocation();  

        $existNumber = ItemStockTransfer::where([
            ['ist_sequence',  $request->ist_sequence],
            ['ist_number', $request->ist_number],
            ['year_id', $year_data->id],
            ['current_location_id', $locationCode->id],
        ])->lockForUpdate()->first();
        
        if($existNumber){
            $latestNo = $this->getLatestISTNo($request);              
            $tmp  =  $latestNo->getContent();
            $area = json_decode($tmp, true);
            $ist_number   =   $area['latest_po_no'];          
            $ist_sequence = $area['number'];
        }else{
           $ist_number    = $request->ist_number;
           $ist_sequence  = $request->ist_sequence;
        }        
        DB::beginTransaction();

        try{
           // $detailsCount = collect($request->details_item_id)->filter()->count();
                    $ist =  ItemStockTransfer::create([

                        'ist_sequence'        => $ist_sequence,
                        'ist_number'          => $ist_number,
                        'ist_date'            => Date::createFromFormat('d/m/Y', $request->ist_date)->format('Y-m-d'),
                        'ist_item_id'         => $request->ist_item_id,
                        'ist_item_details_id' => $request->ist_item_details_id,
                        'current_location_id' => $locationCode->id,
                        'year_id'             => $year_data->id,
                        'company_id'          => Auth::user()->company_id,
                        'created_by_user_id'  => Auth::user()->id,
                        'created_on'          => Carbon::now('Asia/Kolkata')->toDateTimeString()

                    ]);

                    // stockDetailsEffect($locationCode->id,$request->ist_item_details_id,$request->ist_item_details_id,$request->item_stock,0,'add','D','Item Stock Transfer',$ist->ist_id,'Yes','Item Stock Transfer',$ist->ist_id);
                        

                if($ist->save()){

                    foreach($request->details_item_id as $spKey => $spVal){
                         $main_stock = $request->second_stock_qty[$spKey] / $request->main_second_stock;       

                        if($spVal != null){                          

                            $StatusUp =  ItemStockTransferDetails::where('ist_id',$request->id)->update([
                                'status' => 'D',
                            ]);

                            $item_return_detail = ItemStockTransferDetails::create([
                                'ist_id'            => $ist->ist_id,
                                'item_details_id'   => isset($request->details_item_id[$spKey]) ? $request->details_item_id[$spKey] : NULL,
                                'item_stock_qty'   => $main_stock,
                                'stock_transfer_qty'   => ($request->stock_qty[$spKey]) ? $request->stock_qty[$spKey] : NULL,
                                'status' => 'Y',
                            ]);                                              

                            stockDetailsEffect($locationCode->id,$request->ist_item_details_id,$request->ist_item_details_id, $main_stock ,0,'add','D','Item Stock Transfer',$ist->ist_id,'Yes','Item Stock Transfer',$ist->ist_id);

                            stockDetailsEffect($locationCode->id,$request->details_item_id[$spKey],$request->details_item_id[$spKey],$request->stock_qty[$spKey],0,'add','U','Item Stock Transfer Details',$ist->ist_id,'Yes','Item Stock Transfer Details',$item_return_detail->ist_details_id);

                        //    stockDetailsEffect($locationCode->id,$request->details_item_id[$spKey],$request->details_item_id[$spKey],$request->item_stock,0,'add','U','Item Stock Transfer Details',$ist->ist_id,'Yes','Item Stock Transfer Details',$item_return_detail->ist_details_id);

                        }
                    }
               
                    DB::commit();
                    return response()->json([
                        'response_code' => 1,
                        'response_message' => 'Record Inserted Successfully.',
                    ]);
            }
            else
            {
                DB::rollBack();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Not Inserted.',
                ]);
            }
        } catch(\Exception $e)
        {            
            DB::rollBack(); 
            getActivityLogs("Item Stock Transfer", "add", $e->getMessage(),$e->getLine());  
            
            if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Error Occured Record Not Inserted',
                    'original_error' => $e->getMessage()
                ]);
            }
        }
    }


    public function show(ItemStockTransfer $ItemStockTransfer, $id)
    {
         return view('edit.edit-item_stock_transfer', compact('id'));
    }

     public function edit(ItemStockTransfer $ItemStockTransfer,Request $request,$id)
    {
        $locationCode = getCurrentLocation();  
        
        $stock_transfer_data = ItemStockTransfer::select('item_stock_transfer.*','units.unit_name',
        DB::raw("(SELECT IFNULL(SUM(location_stock_details.secondary_stock_qty),0) FROM location_stock_details WHERE item_stock_transfer.ist_item_details_id = location_stock_details.item_details_id AND location_stock_details.location_id = $locationCode->id) as stock_qty"), 
        )
        ->leftJoin('item_details','item_details.item_details_id','=','item_stock_transfer.ist_item_details_id')
        ->leftJoin('items','items.id','=','item_details.item_id')
        ->leftJoin('units','units.id','=','items.second_unit')
        ->where('item_stock_transfer.ist_id','=',$id)->first();         


        $stock_transfer_data->ist_date = Date::createFromFormat('Y-m-d', $stock_transfer_data->ist_date)->format('d/m/Y');
        $stock_transfer_detail_data = ItemStockTransferDetails::where('ist_id','=',$id)->get();

        if($stock_transfer_data){
            return response()->json([
                'stock_transfer_data' => $stock_transfer_data,
                'stock_transfer_detail_data' => $stock_transfer_detail_data,
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




    public function destroy(Request $request)
    {
        DB::beginTransaction();
        
        try{

            $locationID = getCurrentLocation()->id;
            $istQty = ItemStockTransfer::where('ist_id',$request->id)->first();

            if($istQty != null){
                if($istQty->ist_item_details_id != null){
                $sum_item_stoack = ItemStockTransferDetails::where('ist_id','=',$request->id)->sum('item_stock_qty');

                // main table
                stockDetailsEffect($istQty->current_location_id,$istQty->ist_item_details_id,$istQty->ist_item_details_id,0,$sum_item_stoack,'delete','D','Item Stock Transfer',$istQty->ist_id,'Yes','Item Stock Transfer',$istQty->ist_id);
                }

            }

            // this cose use to stock maintain
            $oldISTDetails = ItemStockTransferDetails::where('ist_id','=',$request->id)->get();
            $oldISTDetailsData = [];
            if($oldISTDetails != null){
                  $oldISTDetailsData = $oldISTDetails->toArray();
            }

            foreach($oldISTDetailsData as $gkey=>$gval){  

                // dd($gval);
                // details table
                 stockDetailsEffect($locationID,$gval['item_details_id'],$gval['item_details_id'],0,$gval['stock_transfer_qty'],'delete','U','Item Stock Transfer',$gval['ist_id'],'Yes','Item Stock Transfer Details',$gval['ist_details_id']);
                //  stockDetailsEffect($locationID,$gval['item_details_id'],$gval['item_details_id'],0,$gval['item_stock'],'delete','U','Item Stock Transfer',$gval['ist_id'],'Yes','Item Stock Transfer Details',$gval['ist_details_id']);

            }
          
            ItemStockTransferDetails::where('ist_id',$request->id)->delete();
            ItemStockTransfer::destroy($request->id);

            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){

            DB::rollBack(); 
            getActivityLogs("Item Stock Transfer", "delete", $e->getMessage(),$e->getLine());  
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                     $error_msg = "This is used somewhere, you can't delete";
                     return response()->json([
                        'response_code' => '0',
                        'response_message' => $error_msg,
                    ]);
             }
            else if($e->getMessage() == 'Insufficient Stock' || $e->getMessage() == 'Invalid Location Code' || $e->getMessage() == 'Invalid Item'){
                return response()->json([
                    'response_code' => '0',
                    'response_message' => $e->getMessage(),
                ]);
            }
           
        }
    }

    public function getLatestISTNo(Request $request){
          $modal  =  ItemStockTransfer::class;
          $sequence = 'ist_sequence';
          $prefix = 'IST';
          $po_num_format = getLatestSequence($modal,$sequence,$prefix);
          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $po_num_format['format'],
            'number'        => $po_num_format['isFound'],
        ]);
    }

    public function getISTDetailsItems(Request $request){
        $locationCode = getCurrentLocation();  

        $ISTDetailsItem = ItemDetails::select('item_details.item_details_id','item_details.secondary_item_name','item_details.secondary_qty','item_details.item_id','location_stock_details.stock_qty','item_details.secondary_qty','units.unit_name')
        ->leftJoin('items','items.id','=','item_details.item_id')
        ->leftJoin('units','units.id','=','items.second_unit')
        ->leftJoin('location_stock_details','location_stock_details.item_details_id','=','item_details.item_details_id')
        ->where('location_stock_details.location_id',$locationCode->id)
        ->where('item_details.item_id',$request->ist_item_id)->get();

        return response()->json([
            'response_code' => 1,
            'ISTDetailsItem' => $ISTDetailsItem,
        ]);
    }

    public function getISTSelectedDetailsItems(Request $request){

        $ISTSelectedDetailsItem = ItemDetails::select('item_details.item_details_id','item_details.secondary_item_name','item_details.secondary_qty','item_details.item_id','item_details.secondary_qty','units.unit_name')
        ->leftJoin('items','items.id','=','item_details.item_id')
        ->leftJoin('units','units.id','=','items.second_unit')
        ->where('item_id',$request->ist_item_id)
        ->where('item_details_id','!=',$request->ist_details_item_id)
        ->get();

        return response()->json([
            'response_code' => 1,
            'ISTSelectedDetailsItem' => $ISTSelectedDetailsItem,
        ]);
    }

}