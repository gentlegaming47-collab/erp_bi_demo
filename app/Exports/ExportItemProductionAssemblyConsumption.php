<?php

    namespace App\Exports;

    use App\Models\ItemAssemblyProduction;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Maatwebsite\Excel\Events\AfterSheet;
    use Illuminate\Support\Facades\DB;
    use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
    use Illuminate\Support\Collection;
    use Maatwebsite\Excel\Concerns\WithMapping;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
    use Maatwebsite\Excel\Concerns\WithStyles;
    use Maatwebsite\Excel\Concerns\WithColumnFormatting;
    use Maatwebsite\Excel\Concerns\WithEvents;
    use Carbon\Carbon;
    use Date;

    class ExportItemProductionAssemblyConsumption implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, WithEvents
    {
        use Exportable;
        
        private $searchData;

        public function __construct(array $searchData = [])
        {
            $this->searchData = $searchData;
        }

        public function collection()
        {
            $query = ItemAssemblyProduction::query()->select([
                'item_assembly_production.iap_number',
                'item_assembly_production.iap_sequence',
                DB::raw('DATE_FORMAT(item_assembly_production.iap_date, "%d/%m/%Y") as iap_date'),
                DB::raw("CASE WHEN item_details.secondary_item_name IS NOT NULL THEN item_details.secondary_item_name ELSE items.item_name END as item_name"),
                'items.item_code',
                'item_groups.item_group_name',
                'item_assembly_production.assembly_qty',
                DB::raw("CASE WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name ELSE units.unit_name END as unit_name"),
                'cons_items.item_name as cons_item_name',
                'cons_items.item_code as cons_item_code',
                'item_assembly_production_details.consumption_qty',
                'cons_items_unit.unit_name as cons_items_unit',
                'item_assembly_production.special_notes',
                'adminmod.user_name as modified_by',
                DB::raw('DATE_FORMAT(item_assembly_production.last_on, "' . DATE_TIME_FORMAT_RAW . '") as last_on'),
                'admin.user_name as createdby',
                DB::raw('DATE_FORMAT(item_assembly_production.created_on, "' . DATE_TIME_FORMAT_RAW . '") as created_on')
            ])
            ->leftJoin('item_assembly_production_details','item_assembly_production_details.iap_id','=','item_assembly_production.iap_id')
            ->leftJoin('items','items.id','=','item_assembly_production.item_id')
            ->leftJoin('item_details','item_details.item_details_id','=','item_assembly_production.item_details_id')
            ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
            ->leftJoin('units','units.id','=','items.unit_id')
            ->leftJoin('units as second_unit','second_unit.id','=','items.second_unit')
            ->leftJoin('items as cons_items','cons_items.id','=','item_assembly_production_details.item_id')
            ->leftJoin('units as cons_items_unit', 'cons_items_unit.id', 'cons_items.unit_id')
            ->join('admin','admin.id','=','item_assembly_production.created_by_user_id')
            ->leftJoin('admin as adminmod','adminmod.id','=','item_assembly_production.last_by_user_id')
            ->orderBy('item_assembly_production.iap_number', 'desc')
            ->orderBy('item_assembly_production.iap_sequence', 'desc');

            if(!empty($this->searchData['trans_from_date']))
            {
                try
                {
                    $date = Carbon::createFromFormat('d/m/Y', $this->searchData['trans_from_date'])->format('Y-m-d');
                    $query->whereDate('item_assembly_production.iap_date', '>=', $date);
                }
                catch(\Exception $e)
                {
                    /* Ignore invalid date format */
                }
            }

            if(!empty($this->searchData['trans_to_date']))
            {
                try
                {
                    $date = Carbon::createFromFormat('d/m/Y', $this->searchData['trans_to_date'])->format('Y-m-d');
                    $query->whereDate('item_assembly_production.iap_date', '<=', $date);
                }
                catch(\Exception $e)
                {
                    /* Ignore invalid date format */
                }
            }

            if(!empty($this->searchData['global']))
            {
                $global = $this->searchData['global'];
                $keywords = explode(' ', $global);
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->where(function ($subQ) use ($word) {
                            $subQ->where('item_assembly_production.iap_number', 'like', '%' . $word . '%')
                            ->orWhereRaw('DATE_FORMAT(item_assembly_production.iap_date, "%d/%m/%Y") LIKE ?', ['%' . $word . '%'])
                            ->orWhereRaw("CAST(CASE WHEN item_details.secondary_item_name IS NOT NULL THEN item_details.secondary_item_name ELSE items.item_name END AS CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci) LIKE ?", ['%' . $word . '%'])
                            ->orWhere('items.item_code', 'like', '%' . $word . '%')
                            ->orWhere('item_groups.item_group_name', 'like', '%' . $word . '%')
                            ->orWhereRaw('CAST(item_assembly_production.assembly_qty AS CHAR) LIKE ?', ['%' . $word . '%'])
                            ->orWhereRaw("(CASE WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name ELSE units.unit_name END) LIKE ?", ['%' . $word . '%'])
                            ->orWhere('item_assembly_production.special_notes', 'like', '%' . $word . '%')
                            ->orWhere('adminmod.user_name', 'like', '%' . $word . '%')
                            ->orWhereRaw('DATE_FORMAT(item_assembly_production.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $word . '%'])
                            ->orWhere('admin.user_name', 'like', '%' . $word . '%')
                            ->orWhereRaw('DATE_FORMAT(item_assembly_production.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $word . '%']);
                        });
                    }
                });
            }

            if(!empty($this->searchData['columns']))
            {
                $mappings = [
                    1 => 'item_assembly_production.iap_number',
                    2 => 'item_assembly_production.iap_date',
                    3 => DB::raw("CASE WHEN item_details.secondary_item_name IS NOT NULL THEN item_details.secondary_item_name ELSE items.item_name END"),
                    4 => 'items.item_code',
                    5 => 'item_groups.item_group_name',
                    6 => 'item_assembly_production.assembly_qty',
                    7 => DB::raw("(CASE WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name ELSE units.unit_name END)"),
                    8 => 'item_assembly_production.special_notes',
                    9 => 'adminmod.user_name', // Modified By
                    10 => 'item_assembly_production.last_on', // Modified On
                    11 => 'admin.user_name', // Created By
                    12 => 'item_assembly_production.created_on' // Created On
                ];

                foreach($this->searchData['columns'] as $idx => $cval)
                {
                    if($cval !== '')
                    {
                        $query->where(function ($q) use ($cval) {
                            $q->where('item_assembly_production.iap_number', 'like', '%' . $cval . '%')
                            ->orWhereRaw('DATE_FORMAT(item_assembly_production.iap_date, "%d/%m/%Y") LIKE ?', ['%' . $cval . '%'])
                            ->orWhereRaw("CAST(CASE WHEN item_details.secondary_item_name IS NOT NULL THEN item_details.secondary_item_name ELSE items.item_name END AS CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci) LIKE ?", ['%' . $cval . '%'])
                            ->orWhere('items.item_code', 'like', '%' . $cval . '%')
                            ->orWhere('item_groups.item_group_name', 'like', '%' . $cval . '%')
                            ->orWhereRaw('CAST(item_assembly_production.assembly_qty AS CHAR) LIKE ?', ['%' . $cval . '%'])
                            ->orWhereRaw("(CASE WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name ELSE units.unit_name END) LIKE ?", ['%' . $cval . '%'])
                            ->orWhere('item_assembly_production.special_notes', 'like', '%' . $cval . '%')
                            ->orWhere('adminmod.user_name', 'like', '%' . $cval . '%')
                            ->orWhereRaw('DATE_FORMAT(item_assembly_production.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%'])
                            ->orWhere('admin.user_name', 'like', '%' . $cval . '%')
                            ->orWhereRaw('DATE_FORMAT(item_assembly_production.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%']);
                        });

                        if(isset($mappings[$idx]))
                        {
                            if(in_array($mappings[$idx], ['item_assembly_production.last_on', 'item_assembly_production.created_on']))
                            {
                                try
                                {
                                    $parsedDate = Carbon::parse($cval)->format(explode(' ', DATE_TIME_FORMAT_RAW)[0]);
                                    $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $parsedDate . '%']);
                                }
                                catch(\Exception $e)
                                {
                                    $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $cval . '%']);
                                }
                            }
                            else if(in_array($mappings[$idx], ['item_assembly_production.iap_date']))
                            {
                                $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '%d/%m/%Y') LIKE ?", ['%' . $cval . '%']);
                            }
                            else if($mappings[$idx] instanceof \Illuminate\Database\Query\Expression)
                            {
                                $query->whereRaw("CAST({$mappings[$idx]->getValue()} AS CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci) LIKE ?", ['%' . $cval . '%']);
                            }
                            else if($mappings[$idx] === 'item_assembly_production_details.assembly_qty')
                            {
                                $query->whereRaw('CAST(item_assembly_production_details.assembly_qty AS CHAR) LIKE ?', ['%' . $cval . '%']);
                            }
                            else if($mappings[$idx] === 'unit_name')
                            {
                                $query->whereRaw("(CASE WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name ELSE units.unit_name END) LIKE ?", ['%' . $cval . '%']);
                            }
                            else
                            {
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
                'Sr. No.',
                'Date',
                'Item',
                'Code',
                'Group',
                'Ass. Qty.',
                'Unit',
                'Cons. Item',
                'Cons. Code',
                'Cons. Qty.',
                'Cons. Unit',
                'Sp. Note',
                'Modified By',
                'Modified On',
                'Created By',
                'Created On',
            ];
        }

        public function map($row): array
        {
            return [
                $row->iap_number,
                $row->iap_date,
                $row->item_name,
                $row->item_code,
                $row->item_group_name,
                $row->assembly_qty,
                $row->unit_name,
                $row->cons_item_name,
                $row->cons_item_code,
                $row->consumption_qty,
                $row->cons_items_unit,
                $row->special_notes,
                $row->modified_by,
                $row->last_on,
                $row->createdby,
                $row->created_on,
            ];
        }

        public function columnFormats(): array
        {
            return [
                'F' => '0.000',
                'J' => '0.000',
            ];
        }

        public function styles(Worksheet $sheet)
        {
            return [
                1 => ['font' => ['bold' => true]],
            ];
        }

        public function registerEvents(): array
        {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    for ($i = 'A'; $i <= 'P'; $i++) {
                        $event->sheet->getColumnDimension($i)->setAutoSize(true);
                    }
                },
            ];
        }
    }
?>