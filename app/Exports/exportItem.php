<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;
use Date;

class exportItem implements FromCollection ,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    
    */
    use Exportable;

    public function collection()
    {
        $itemData = Item::select(['items.item_name',
        'items.item_code',
        'item_groups.item_group_name',
        'units.unit_name',
        'items.min_stock_qty',
        'items.max_stock_qty',
        'items.re_order_qty',
        'hsn_code.hsn_code',
        'items.rate_per_unit',
        'items.require_raw_material_mapping',
        'items.fitting_item',
        'adminmod.user_name',
        'items.last_on',
        'admin.user_name as createdby',
        'items.created_on'])
        ->leftJoin('units','units.id','=','items.unit_id')        
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')        
        ->leftJoin('hsn_code','hsn_code.id','=','items.hsn_code')
        ->join('admin','admin.id','=','items.created_by_user_id')
		->leftjoin('admin as adminmod','adminmod.id','=','items.last_by_user_id')
        ->get();
        // dd($itemData);
        return $itemData;

    }

    public function headings(): array
    {
        return [
            'Item',
            'Code',
            'Group',
            'Unit',
            'Min.Stock',
            'Max.Stock',
            'Re-Order',
            'HSN Code',
            'Rate/Unit',
            'Item Mapping?',
            'Fitting Item?',
            'Modified By',
            'Modified On',
            'Created By',
            'Created On',
        ];
    }
}
