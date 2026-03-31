<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemIssue;
use App\Models\ItemIssueDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Admin;

class ItemIssueSummaryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-item_issue_summary');
    }

    public function index(ItemIssue $ItemIssue, Request $request, DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();
        $itemIssue = ItemIssue::select([
            'item_issue.issue_number',
            'item_issue.issue_sequence',
            'item_issue.issue_date',
            'items.item_name',
            'items.item_code',
            'item_issue_details.issue_qty',
            'item_issue_details.item_type',
            'item_issue_details.remarks',
            'item_issue.item_issue_id',
            'item_issue.issue_type_value_fix',
            'item_groups.item_group_name',
            'units.unit_name'
        ])
        ->leftJoin('item_issue_details','item_issue_details.item_issue_id','=','item_issue.item_issue_id')
        ->leftJoin('items','items.id','=','item_issue_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('current_location_id','=',$location->id);

        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $itemIssue->whereDate('item_issue.issue_date','>=',$from);
            $itemIssue->whereDate('item_issue.issue_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $itemIssue->where('item_issue.issue_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');
            $itemIssue->where('item_issue.issue_date','<=',$to);

        } 
        // if($request->issue_number != ''){
        //      $itemIssue->where('item_issue.issue_number', 'like', "%{$request->issue_number}%");
        // }
        //  if($request->item_id !=''){
        //     $itemIssue->where('item_issue_details.item_id', '=', $request->item_id);
        // }
        //  if($request->issue_type_value_fix !=''){
        //     $itemIssue->where('item_issue_details.item_type','=',$request->issue_type_value_fix);
        // }


        return DataTables::of($itemIssue)
            ->editColumn('issue_date', function($itemIssue){
                return $itemIssue->issue_date
                    ? \Carbon\Carbon::parse($itemIssue->issue_date)->format(DATE_FORMAT)
                    : '';
            })
            ->filterColumn('item_issue.issue_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(item_issue.issue_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
            })
            ->editColumn('issue_qty', function($itemIssue){
                return $itemIssue->issue_qty !== null
                    ? number_format((float)$itemIssue->issue_qty, 3, '.', '')
                    : '0.000';
            })
            ->editColumn('item_name', function($itemIssue){
                return $itemIssue->item_name
                    ? ucfirst($itemIssue->item_name)
                    : '';
            })
            // ->editColumn('item_type', function($itemIssue){
            //     return $itemIssue->item_type
            //         ? ucfirst(str_replace('_', ' ', $itemIssue->item_type))
            //         : '';
            // })
              ->editColumn('item_type', function($itemIssue){ 
        $map = [
            'consumable' => 'Consumable',
            'waste/scrap_entry' => 'Waste/Scrap entry',
        ];

        if($itemIssue->item_type != "" && isset($map[$itemIssue->item_type])){
            return $map[$itemIssue->item_type];
        }

        return '';
    })

     ->filterColumn('item_type', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('item_issue_details.item_type', 'like', "%{$keyword}%")
                ->orWhereRaw("CASE 
                    WHEN item_issue_details.item_type = 'consumable' THEN 'Consumable'
                    WHEN item_issue_details.item_type = 'waste/scrap_entry' THEN 'Waste/Scrap entry'
                    ELSE item_issue_details.item_type
                    END LIKE ?", ["%{$keyword}%"]);
            });
        })
            ->make(true);
    }


}