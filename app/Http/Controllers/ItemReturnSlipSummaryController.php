<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemReturn;
use App\Models\ItemReturnDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;

class ItemReturnSlipSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-item_return_slip_summary');
    }
     public function index(ItemReturn $ItemReturn,Request $request,DataTables $dataTables)
   {
       $year_data = getCurrentYearData();
       $location = getCurrentLocation();

       $itemReturn = ItemReturn::select(['return_number', 'issue_no','return_sequence', 'return_date', 'items.item_name', 'items.item_code', 'item_return_details.return_qty', 'item_return_details.remarks','item_return.item_return_id','units.unit_name'])

       ->leftJoin('item_return_details','item_return_details.item_return_id','=','item_return.item_return_id')

       ->leftJoin('suppliers','suppliers.id','=','item_return.supplier_id')

       ->leftJoin('items','items.id','=','item_return_details.item_id')

       ->leftJoin('units','units.id','=','items.unit_id')


       ->where('item_return.current_location_id','=',$location->id);

        //searching logic
         if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $itemReturn->whereDate('item_return.return_date','>=',$from);
            $itemReturn->whereDate('item_return.return_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $itemReturn->where('item_return.return_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');
            $itemReturn->where('item_return.return_date','<=',$to);

        } 
        // if($request->return_number != ''){
        //      $itemReturn->where('item_return.return_number', 'like', "%{$request->return_number}%");
        // }
        //  if($request->item_id !=''){
        //     $itemReturn->where('item_return_details.item_id', '=', $request->item_id);
        // }
        //  if($request->issue_number != ''){
        //      $itemReturn->where('item_return.issue_no', 'like', "%{$request->issue_number}%");
        // }



      return DataTables::of($itemReturn)


       ->editColumn('return_date', function($itemReturn){

           if ($itemReturn->return_date != null) {

               $formatedDate3 = Date::createFromFormat('Y-m-d', $itemReturn->return_date)->format(DATE_FORMAT); return $formatedDate3;

           }else{

               return '';

           }

       })
       ->filterColumn('item_return.return_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(item_return.return_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

       ->editColumn('return_qty', function($itemReturn){

        return $itemReturn->return_qty > 0 ? number_format((float)$itemReturn->return_qty, 3, '.','') : number_format((float) 0, 3, '.','');

       })

       ->editColumn('item_name', function($itemReturn){
            if($itemReturn->item_name != ''){
                $item_name = ucfirst($itemReturn->item_name);
                return $item_name;
            }else{
                return '';
            }
        })


       ->make(true);
   }

}
