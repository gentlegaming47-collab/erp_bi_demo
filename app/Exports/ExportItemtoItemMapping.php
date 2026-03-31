<?php

    namespace App\Exports;

    use App\Models\ItemRawMaterialMappingDetail;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\WithMapping;
    use Maatwebsite\Excel\Concerns\WithColumnFormatting;
    use Maatwebsite\Excel\Concerns\Exportable;
    use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
    use Maatwebsite\Excel\Concerns\WithStyles;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

    class ExportItemtoItemMapping implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles
    {
        use Exportable;

        private $searchData;

        public function __construct(array $searchData = [])
        {
            $this->searchData = $searchData;
        }

        public function collection()
        {
            $query = ItemRawMaterialMappingDetail::query()->select([
                'items.item_name',
                'items.item_code',
                'item_groups.item_group_name',
                'units.unit_name',
                'm_items.item_name as m_item_name',
                'm_items.item_code as map_item_code',
                'map_item_group.item_group_name as map_item_group',
                'item_raw_material_mapping_details.raw_material_qty',
                'map_item_unit_name.unit_name as map_item_unit_name',
                'adminmod.user_name as modified_by',
                DB::raw('DATE_FORMAT(item_raw_material_mapping_details.last_on, "' . DATE_TIME_FORMAT_RAW . '") as last_on'),
                'admin.user_name as createdby',
                DB::raw('DATE_FORMAT(item_raw_material_mapping_details.created_on, "' . DATE_TIME_FORMAT_RAW . '") as created_on')
            ])
            ->join('items', 'items.id', 'item_raw_material_mapping_details.item_id')
            ->leftJoin('items as m_items', 'm_items.id', 'item_raw_material_mapping_details.raw_material_id')
            ->join('units', 'units.id', '=', 'items.unit_id')
            ->join('item_groups', 'item_groups.id', '=', 'items.item_group_id')
            ->join('item_groups as map_item_group', 'map_item_group.id', '=', 'items.item_group_id')
            ->leftJoin('units as map_item_unit_name', 'map_item_unit_name.id', 'm_items.unit_id')
            ->leftJoin('item_details', 'item_details.item_details_id', '=', 'item_raw_material_mapping_details.item_details_id')
            ->leftJoin('admin as adminmod', 'adminmod.id', '=', 'item_raw_material_mapping_details.last_by_user_id')
            ->join('admin', 'admin.id', '=', 'item_raw_material_mapping_details.created_by_user_id')
            ->orderBy('items.item_name');

            // if(!empty($this->searchData['global']))
            // {
            //     // dd($this->searchData['global']);
            //     $global = $this->searchData['global'];
            //     $query->where(function ($q) use ($global)
            //     {
            //         $q->where('items.item_code', 'like', '%' . $global . '%')
            //             ->orWhere('items.item_name', 'like', '%' . $global . '%')
            //             ->orWhere('item_groups.item_group_name', 'like', '%' . $global . '%')
            //             ->orWhere('units.unit_name', 'like', '%' . $global . '%')
            //             ->orWhere('m_items.item_name', 'like', '%' . $global . '%')
            //             // ->orWhere('m_items.item_code', 'like', '%' . $global . '%')
            //             // ->orWhere('map_item_group.item_group_name', 'like', '%' . $global . '%')
            //             ->orWhereRaw('CAST(item_raw_material_mapping_details.raw_material_qty AS CHAR) LIKE ?', ['%' . $global . '%'])
            //             ->orWhere('map_item_unit_name.unit_name', 'like', '%' . $global . '%')
            //             ->orWhere('adminmod.user_name', 'like', '%' . $global . '%')
            //             ->orWhereRaw('DATE_FORMAT(item_raw_material_mapping_details.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%'])
            //             ->orWhere('admin.user_name', 'like', '%' . $global . '%')
            //             ->orWhereRaw('DATE_FORMAT(item_raw_material_mapping_details.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%']);
            //     });
            // }

            if(!empty($this->searchData['global']))
            {
                $global = $this->searchData['global'];
                $keywords = explode(' ', $global);
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->where(function ($subQ) use ($word) {
                            $subQ->where('items.item_code', 'like', '%' . $word . '%')
                                ->orWhere('items.item_name', 'like', '%' . $word . '%')
                                ->orWhere('item_groups.item_group_name', 'like', '%' . $word . '%')
                                ->orWhere('units.unit_name', 'like', '%' . $word . '%')
                                ->orWhere('m_items.item_name', 'like', '%' . $word . '%')
                                ->orWhereRaw('CAST(item_raw_material_mapping_details.raw_material_qty AS CHAR) LIKE ?', ['%' . $word . '%'])
                                ->orWhere('map_item_unit_name.unit_name', 'like', '%' . $word . '%')
                                ->orWhere('adminmod.user_name', 'like', '%' . $word . '%')
                                ->orWhereRaw("DATE_FORMAT(item_raw_material_mapping_details.last_on, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $word . '%'])
                                ->orWhere('admin.user_name', 'like', '%' . $word . '%')
                                ->orWhereRaw("DATE_FORMAT(item_raw_material_mapping_details.created_on, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $word . '%']);
                        });
                    }
                });
            }

            if(!empty($this->searchData['columns']))
            {
                $mappings = [
                    1 => 'items.item_name',
                    2 => 'items.item_code',
                    3 => 'item_groups.item_group_name',
                    4 => 'units.unit_name',
                    5 => 'm_items.item_name',
                    6 => 'item_raw_material_mapping_details.raw_material_qty',
                    7 => 'adminmod.user_name', // Modified By
                    8 => 'item_raw_material_mapping_details.last_on', // Modified On
                    9 => 'admin.user_name', // Created By
                    10 => 'item_raw_material_mapping_details.created_on', // Created On
                ];

                foreach($this->searchData['columns'] as $idx => $cval)
                {
                    if($cval !== '')
                    {
                        $query->where(function ($q) use ($cval) {
                            $q->where('items.item_name', 'like', '%' . $cval . '%')
                                ->orWhere('items.item_code', 'like', '%' . $cval . '%')
                                ->orWhere('item_groups.item_group_name', 'like', '%' . $cval . '%')
                                ->orWhere('units.unit_name', 'like', '%' . $cval . '%')
                                ->orWhere('m_items.item_name', 'like', '%' . $cval . '%')
                                // ->orWhere('m_items.item_code', 'like', '%' . $cval . '%')
                                // ->orWhere('map_item_group.item_group_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('CAST(item_raw_material_mapping_details.raw_material_qty AS CHAR) LIKE ?', ['%' . $cval . '%'])
                                ->orWhere('map_item_unit_name.unit_name', 'like', '%' . $cval . '%')
                                ->orWhere('adminmod.user_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('DATE_FORMAT(item_raw_material_mapping_details.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%'])
                                ->orWhere('admin.user_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('DATE_FORMAT(item_raw_material_mapping_details.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%']);
                        });

                        if(isset($mappings[$idx]))
                        {
                            if(in_array($mappings[$idx], ['item_raw_material_mapping_details.last_on', 'item_raw_material_mapping_details.created_on'])) {
                                try
                                {
                                    $parsedDate = Carbon::parse($cval)->format(explode(' ', DATE_TIME_FORMAT_RAW)[0]);
                                    $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $parsedDate . '%']);
                                }catch (\Exception $e)
                                {
                                    $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $cval . '%']);
                                }
                            } else {
                                $query->where($mappings[$idx], 'like', '%' . $cval . '%');
                            }
                        }
                    }
                }
            }

            return $query->get();
        }

        public function headings(): array
        {
            return [
                'Item',
                'Code',
                'Group',
                'Unit',
                'Map Item Name',
                'Map Item Code',
                'Map Item Group',
                'Map Item Qty.',
                'Map Item Unit',
                'Modified By',
                'Modified On',
                'Created By',
                'Created On',
            ];
        }

        public function map($row): array
        {
            return [
                $row->item_name,
                $row->item_code,
                $row->item_group_name,
                $row->unit_name,
                $row->m_item_name,
                $row->map_item_code,
                $row->map_item_group,
                $row->raw_material_qty,
                $row->map_item_unit_name,
                $row->modified_by,
                $row->last_on,
                $row->createdby,
                $row->created_on,
            ];
        }

        public function columnFormats(): array
        {
            return [
                'H' => '0.000',
            ];
        }

        public function styles(Worksheet $sheet)
        {
            return [
                1 => ['font' => ['bold' => true]],
            ];
        }
    }
?>