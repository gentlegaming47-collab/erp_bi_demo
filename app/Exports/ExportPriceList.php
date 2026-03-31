<?php
    namespace App\Exports;

    use App\Models\PriceList;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
    use Maatwebsite\Excel\Concerns\WithStyles;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

    class ExportPriceList implements FromCollection, WithHeadings, WithStyles
    {
        use Exportable;
        
        private $searchData;

        public function __construct(array $searchData = [])
        {
            $this->searchData = $searchData;
        }

        public function collection()
        {
            $query = PriceList::query()
                ->select([
                    'customer_groups.customer_group_name',
                    'items.item_name',
                    'items.item_code',
                    'item_groups.item_group_name',
                    'price_list_details.sales_rate',
                    'adminmod.user_name as modified_by',
                    DB::raw('DATE_FORMAT(price_list.last_on, "' . DATE_TIME_FORMAT_RAW . '") as last_on'),
                    'admin.user_name as createdby',
                    DB::raw('DATE_FORMAT(price_list.created_on, "' . DATE_TIME_FORMAT_RAW . '") as created_on')
                ])
                ->leftJoin('price_list_details','price_list_details.pl_id','price_list.pl_id')
                ->leftJoin('customer_groups','customer_groups.id','=','price_list.customer_group_id')
                ->leftJoin('items','items.id','=','price_list_details.item_id')
                ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
                ->leftJoin('units','units.id','=','items.unit_id')
                ->join('admin','admin.id','=','price_list.created_by_user_id')
                ->leftJoin('admin as adminmod','adminmod.id','=','price_list.last_by_user_id')
                ->orderBy('customer_groups.customer_group_name','asc');

            // if (!empty($this->searchData['global'])) {
            //     $global = $this->searchData['global'];
            //     $query->where(function ($q) use ($global) {
            //         $q->where('customer_groups.customer_group_name', 'like', '%' . $global . '%')
            //             ->orWhere('items.item_name', 'like', '%' . $global . '%')
            //             ->orWhere('items.item_code', 'like', '%' . $global . '%')
            //             ->orWhere('item_groups.item_group_name', 'like', '%' . $global . '%')
            //             ->orWhereRaw('CAST(price_list_details.sales_rate AS CHAR) LIKE ?', ['%' . $global . '%'])
            //             ->orWhere('adminmod.user_name', 'like', '%' . $global . '%')
            //             ->orWhereRaw('DATE_FORMAT(price_list.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%'])
            //             ->orWhere('admin.user_name', 'like', '%' . $global . '%')
            //             ->orWhereRaw('DATE_FORMAT(price_list.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%']);
            //     });
            // }

            if(!empty($this->searchData['global']))
            {
                $global = $this->searchData['global'];
                $keywords = explode(' ', $global);
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->where(function ($subQ) use ($word) {
                            $subQ->where('customer_groups.customer_group_name', 'like', '%' . $word . '%')
                            ->orWhere('adminmod.user_name', 'like', '%' . $word . '%')
                            ->orWhereRaw('DATE_FORMAT(price_list.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $word . '%'])
                            ->orWhere('admin.user_name', 'like', '%' . $word . '%')
                            ->orWhereRaw('DATE_FORMAT(price_list.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $word . '%']);
                        });
                    }
                });
            }

            if(!empty($this->searchData['columns']))
            {
                $mappings = [
                    1 => 'customer_groups.customer_group_name', // Cust. Group
                    2 => 'adminmod.user_name',                 // Modified By
                    3 => 'price_list.last_on',                 // Modified On
                    4 => 'admin.user_name',                    // Created By
                    5 => 'price_list.created_on'               // Created On
                ];

                foreach ($this->searchData['columns'] as $idx => $cval) {
                    if ($cval !== '') {

                        // Search across all columns for each column-specific term
                        $query->where(function ($q) use ($cval) {
                            $q->where('customer_groups.customer_group_name', 'like', '%' . $cval . '%')
                                ->orWhere('items.item_name', 'like', '%' . $cval . '%')
                                ->orWhere('items.item_code', 'like', '%' . $cval . '%')
                                ->orWhere('item_groups.item_group_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('CAST(price_list_details.sales_rate AS CHAR) LIKE ?', ['%' . $cval . '%'])
                                ->orWhere('adminmod.user_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('DATE_FORMAT(price_list.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%'])
                                ->orWhere('admin.user_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('DATE_FORMAT(price_list.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%']);
                        });

                        // Additional specific column filter
                        if (isset($mappings[$idx])) {
                            if (in_array($mappings[$idx], ['price_list.last_on', 'price_list.created_on'])) {
                                try {
                                    $parsedDate = Carbon::parse($cval)->format(explode(' ', DATE_TIME_FORMAT_RAW)[0]);
                                    $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $parsedDate . '%']);
                                } catch (\Exception $e) {
                                    $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $cval . '%']);
                                }
                            } else if ($mappings[$idx] === 'price_list_details.sales_rate') {
                                $query->whereRaw('CAST(price_list_details.sales_rate AS CHAR) LIKE ?', ['%' . $cval . '%']);
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
                'Cust. Group',
                'Item',
                'Item Code',
                'Group',
                'Sales Rate/Unit',
                'Modified By',
                'Modified On',
                'Created By',
                'Created On',
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