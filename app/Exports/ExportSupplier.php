<?php

    namespace App\Exports;

    use App\Models\Supplier;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Maatwebsite\Excel\Concerns\WithColumnFormatting;
    use Maatwebsite\Excel\Concerns\WithEvents;
    use Maatwebsite\Excel\Events\AfterSheet;
    use Illuminate\Support\Facades\DB;
    use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
    use Illuminate\Support\Collection;
    use Maatwebsite\Excel\Concerns\WithMapping;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
    use Maatwebsite\Excel\Concerns\WithStyles;
    use Carbon\Carbon;

    class ExportSupplier implements FromCollection, WithHeadings, WithColumnFormatting, WithEvents, WithStyles
    {
        use Exportable;

        private $searchData;

        public function __construct(array $searchData = [])
        {
            $this->searchData = $searchData;
        }

        public function collection()
        {
            $query = Supplier::query()
                ->select([
                    'suppliers.supplier_name',
                    'suppliers.supplier_code',
                    'suppliers.address',
                    'villages.village_name',
                    'suppliers.pincode',
                    'talukas.taluka_name',
                    'districts.district_name',
                    'states.state_name',
                    'countries.country_name',
                    'suppliers.contact_person',
                    'suppliers.contact_person_mobile',
                    'suppliers.contact_person_email_id',
                    'suppliers.web_address',
                    'suppliers.pan',
                    'suppliers.gstin',
                    'suppliers.payment_terms',
                    DB::raw("CASE 
                        WHEN suppliers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                        WHEN suppliers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                        WHEN suppliers.approval_status = 'active' THEN 'Active'
                        WHEN suppliers.approval_status = 'deactive' THEN 'Deactive'
                        ELSE ''
                    END as approval_status_text"),
                    'adminmod.user_name as modified_by',
                    DB::raw('DATE_FORMAT(suppliers.last_on, "' . DATE_TIME_FORMAT_RAW . '") as last_on'),
                    'admin.user_name as createdby',
                    DB::raw('DATE_FORMAT(suppliers.created_on, "' . DATE_TIME_FORMAT_RAW . '") as created_on')
                ])
                ->leftJoin('villages','villages.id','=','suppliers.village_id')
                ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
                ->leftJoin('districts','districts.id','=','talukas.district_id')
                ->leftJoin('states','states.id','=','districts.state_id')
                ->leftJoin('countries','countries.id','=','states.country_id')
                ->join('admin','admin.id','=','suppliers.created_by_user_id')
                ->leftJoin('admin as adminmod','adminmod.id','=','suppliers.last_by_user_id');

            if(!empty($this->searchData['global']))
            {
                $global = $this->searchData['global'];
                $query->where(function ($q) use ($global) {
                    $q->where('suppliers.supplier_name', 'like', '%' . $global . '%')
                        ->orWhere('suppliers.supplier_code', 'like', '%' . $global . '%')
                        ->orWhere('villages.village_name', 'like', '%' . $global . '%')
                        ->orWhere('suppliers.pincode', 'like', '%' . $global . '%')
                        ->orWhere('talukas.taluka_name', 'like', '%' . $global . '%')
                        ->orWhere('districts.district_name', 'like', '%' . $global . '%')
                        ->orWhere('states.state_name', 'like', '%' . $global . '%')
                        ->orWhere('countries.country_name', 'like', '%' . $global . '%')
                        ->orWhere('suppliers.contact_person', 'like', '%' . $global . '%')
                        ->orWhere('suppliers.contact_person_mobile', 'like', '%' . $global . '%')
                        ->orWhere('suppliers.contact_person_email_id', 'like', '%' . $global . '%')
                        ->orWhere('suppliers.web_address', 'like', '%' . $global . '%')
                        ->orWhere('suppliers.pan', 'like', '%' . $global . '%')
                        ->orWhere('suppliers.gstin', 'like', '%' . $global . '%')
                        ->orWhere('suppliers.payment_terms', 'like', '%' . $global . '%')
                        ->orWhereRaw("(
                            CASE 
                                WHEN suppliers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                                WHEN suppliers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                                WHEN suppliers.approval_status = 'active' THEN 'Active'
                                WHEN suppliers.approval_status = 'deactive' THEN 'Deactive'
                                ELSE ''
                            END
                        ) = ?", [$global])
                        ->orWhere('adminmod.user_name', 'like', '%' . $global . '%')
                        ->orWhereRaw('DATE_FORMAT(suppliers.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%'])
                        ->orWhere('admin.user_name', 'like', '%' . $global . '%')
                        ->orWhereRaw('DATE_FORMAT(suppliers.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%']);
                });
            }

            if(!empty($this->searchData['columns']))
            {
                $mappings = [
                    1 => 'suppliers.supplier_name',
                    2 => 'suppliers.supplier_code',
                    3 => 'villages.village_name',
                    4 => 'suppliers.pincode',
                    5 => 'talukas.taluka_name',
                    6 => 'districts.district_name',
                    7 => 'states.state_name',
                    8 => 'countries.country_name',
                    9 => 'suppliers.contact_person',
                    10 => 'suppliers.contact_person_mobile',
                    11 => 'suppliers.contact_person_email_id',
                    12 => 'suppliers.web_address',
                    13 => 'suppliers.pan',
                    14 => 'suppliers.gstin',
                    15 => 'suppliers.payment_terms',
                    16 => 'suppliers.approval_status',
                    17 => 'adminmod.user_name', // Modified By
                    18 => 'suppliers.last_on', // Modified On
                    19 => 'admin.user_name', // Created By
                    20 => 'suppliers.created_on', // Created On
                ];

                foreach($this->searchData['columns'] as $idx => $cval)
                {
                    if($cval !== '')
                    {
                        $query->where(function ($q) use ($cval) {
                            $q->where('suppliers.supplier_name', 'like', '%' . $cval . '%')
                                ->orWhere('suppliers.supplier_code', 'like', '%' . $cval . '%')
                                ->orWhere('villages.village_name', 'like', '%' . $cval . '%')
                                ->orWhere('suppliers.pincode', 'like', '%' . $cval . '%')
                                ->orWhere('talukas.taluka_name', 'like', '%' . $cval . '%')
                                ->orWhere('districts.district_name', 'like', '%' . $cval . '%')
                                ->orWhere('states.state_name', 'like', '%' . $cval . '%')
                                ->orWhere('countries.country_name', 'like', '%' . $cval . '%')
                                ->orWhere('suppliers.contact_person', 'like', '%' . $cval . '%')
                                ->orWhere('suppliers.contact_person_mobile', 'like', '%' . $cval . '%')
                                ->orWhere('suppliers.contact_person_email_id', 'like', '%' . $cval . '%')
                                ->orWhere('suppliers.web_address', 'like', '%' . $cval . '%')
                                ->orWhere('suppliers.pan', 'like', '%' . $cval . '%')
                                ->orWhere('suppliers.gstin', 'like', '%' . $cval . '%')
                                ->orWhere('suppliers.payment_terms', 'like', '%' . $cval . '%')
                                ->orWhereRaw("(
                                    CASE 
                                        WHEN suppliers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                                        WHEN suppliers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                                        WHEN suppliers.approval_status = 'active' THEN 'Active'
                                        WHEN suppliers.approval_status = 'deactive' THEN 'Deactive'
                                        ELSE ''
                                    END
                                ) LIKE ?", ['%' . $cval . '%'])
                                ->orWhere('adminmod.user_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('DATE_FORMAT(suppliers.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%'])
                                ->orWhere('admin.user_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('DATE_FORMAT(suppliers.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%']);
                        });

                        if(isset($mappings[$idx]))
                        {
                            if(in_array($mappings[$idx], ['suppliers.last_on', 'suppliers.created_on']))
                            {
                                try
                                {
                                    $parsedDate = Carbon::parse($cval)->format(explode(' ', DATE_TIME_FORMAT_RAW)[0]);
                                    $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $parsedDate . '%']);
                                }
                                catch (\Exception $e)
                                {
                                    $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '" . DATE_TIME_FORMAT_RAW . "') LIKE ?", ['%' . $cval . '%']);
                                }
                            }
                            else if(isset($mappings[$idx]) && $mappings[$idx] == 'suppliers.approval_status')
                            {
                                $query->whereRaw("(
                                    CASE 
                                        WHEN suppliers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                                        WHEN suppliers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                                        WHEN suppliers.approval_status = 'active' THEN 'Active'
                                        WHEN suppliers.approval_status = 'deactive' THEN 'Deactive'
                                        ELSE ''
                                    END
                                ) = ?", [$cval]);
                            }
                            else
                            {
                                $query->where($mappings[$idx], 'like', '%' . $cval . '%');
                            }
                        }
                    }
                }
            }

            return $query->get()->map(function ($item) {
                $item->contact_person_mobile = $item->contact_person_mobile ? " " . $item->contact_person_mobile : $item->contact_person_mobile;
                $item->contact_person_email_id = $item->contact_person_email_id ? " " . $item->contact_person_email_id : $item->contact_person_email_id;
                $item->pan = $item->pan ? " " . $item->pan : $item->pan;
                $item->gstin = $item->gstin ? " " . $item->gstin : $item->gstin;
                return $item;
            });
        }

        public function headings(): array
        {
            return [
                'Supplier',
                'Supplier Code',
                'Address',
                'Village',
                'Pin Code',
                'Taluka',
                'District',
                'State',
                'Country',
                'Person',
                'Person Mobile',
                'Person Email',
                'Web',
                'PAN',
                'GSTIN',
                'Pay. Terms',
                'Status',
                'Modified By',
                'Modified On',
                'Created By',
                'Created On',
            ];
        }

        public function columnFormats(): array
        {
            return [
                'J' => NumberFormat::FORMAT_TEXT,
                'K' => NumberFormat::FORMAT_TEXT,
                'M' => NumberFormat::FORMAT_TEXT,
                'N' => NumberFormat::FORMAT_TEXT,
            ];
        }

        public function registerEvents(): array
        {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(15);
                },
            ];
        }

        public function styles(Worksheet $sheet)
        {
            return [
                1 => ['font' => ['bold' => true]],
            ];
        }
    }

    // namespace App\Exports;

    // use App\Models\Supplier;
    // use Maatwebsite\Excel\Concerns\FromCollection;
    // use Maatwebsite\Excel\Concerns\WithHeadings;
    // use Maatwebsite\Excel\Concerns\Exportable;
    // use Illuminate\Support\Facades\DB;

    // class ExportSupplier implements FromCollection, WithHeadings {
    //     use Exportable;

    //     public function collection()
    //     {
    //         return Supplier::select(['suppliers.supplier_name','suppliers.supplier_code','cities.city_name','states.state_name',
    //         'countries.country_name','suppliers.phone_no','suppliers.email','suppliers.web_address','suppliers.pan','suppliers.gstin','suppliers.payment_terms','suppliers.contact_person',
    //         'suppliers.person_phone','suppliers.person_email','suppliers.approved_supplier',
    //         'adminmod.user_name','suppliers.last_on','admin.user_name as createdby','suppliers.created_on'])
    //         ->leftJoin('cities','cities.id','=','suppliers.city_id')
    //         ->leftJoin('states','states.id','=','cities.state_id')
    //         ->leftJoin('countries','countries.id','=','states.country_id')
    //         ->join('admin','admin.id','=','suppliers.created_by_user_id')
	// 		->leftjoin('admin as adminmod','adminmod.id','=','suppliers.last_by_user_id')
    //         ->get();
    //     }

    //     public function headings(): array
    //     {
    //         return [
    //             'Supplier',
    //             'Code',
    //             'City',
    //             'State',
    //             'Country',
    //             'Phone',
    //             'Email',
    //             'Web',
    //             'PAN',
    //             'GSTIN',
    //             'Payment Terms',
    //             'Person',
    //             'Phone No.',
    //             'Email ID',
    //             'Approved Supplier',
    //             'Modified By',
    //             'Modified On',
    //             'Created By',
    //             'Created On',

    //         ];
    //     }
    // }
?>