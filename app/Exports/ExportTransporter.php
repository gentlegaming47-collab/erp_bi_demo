<?php

    namespace App\Exports;

    use App\Models\Transporter;
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

    class ExportTransporter implements FromCollection, WithHeadings, WithColumnFormatting, WithEvents, WithStyles
    {
        use Exportable;

        private $searchData;

        public function __construct(array $searchData = [])
        {
            $this->searchData = $searchData;
        }

        public function collection()
        {
            $query = Transporter::query()
                ->select([
                    'transporters.transporter_name',
                    'transporters.address',
                    'transporters.pan',
                    'transporters.gstin',
                    'transporters.contact_person',
                    'transporters.contact_person_mobile',
                    'transporters.contact_person_email_id',
                    'transporters.payment_terms',
                    DB::raw("CASE 
                        WHEN transporters.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                        WHEN transporters.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                        WHEN transporters.approval_status = 'active' THEN 'Active'
                        WHEN transporters.approval_status = 'deactive' THEN 'Deactive'
                        ELSE ''
                    END as approval_status_text"),
                    'adminmod.user_name as modified_by',
                    DB::raw('DATE_FORMAT(transporters.last_on, "' . DATE_TIME_FORMAT_RAW . '") as last_on'),
                    'admin.user_name as createdby',
                    DB::raw('DATE_FORMAT(transporters.created_on, "' . DATE_TIME_FORMAT_RAW . '") as created_on')
                ])
                ->join('admin','admin.id','=','transporters.created_by_user_id')
                ->leftJoin('admin as adminmod','adminmod.id','=','transporters.last_by_user_id');

            if(!empty($this->searchData['global']))
            {
                $global = $this->searchData['global'];
                $query->where(function ($q) use ($global) {
                    $q->where('transporters.transporter_name', 'like', '%' . $global . '%')
                        ->orWhere('transporters.pan', 'like', '%' . $global . '%')
                        ->orWhere('transporters.gstin', 'like', '%' . $global . '%')
                        ->orWhere('transporters.contact_person', 'like', '%' . $global . '%')
                        ->orWhere('transporters.contact_person_mobile', 'like', '%' . $global . '%')
                        ->orWhere('transporters.contact_person_email_id', 'like', '%' . $global . '%')
                        ->orWhere('transporters.payment_terms', 'like', '%' . $global . '%')
                        ->orWhereRaw("(
                            CASE 
                                WHEN transporters.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                                WHEN transporters.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                                WHEN transporters.approval_status = 'active' THEN 'Active'
                                WHEN transporters.approval_status = 'deactive' THEN 'Deactive'
                                ELSE ''
                            END
                        ) = ?", [$global])
                        ->orWhere('adminmod.user_name', 'like', '%' . $global . '%')
                        ->orWhereRaw('DATE_FORMAT(transporters.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%'])
                        ->orWhere('admin.user_name', 'like', '%' . $global . '%')
                        ->orWhereRaw('DATE_FORMAT(transporters.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%']);
                });
            }

            if(!empty($this->searchData['columns']))
            {
                $mappings = [
                    1 => 'transporters.transporter_name',
                    2 => 'transporters.pan',
                    3 => 'transporters.gstin',
                    4 => 'transporters.contact_person',
                    5 => 'transporters.contact_person_mobile',
                    6 => 'transporters.contact_person_email_id',
                    7 => 'transporters.payment_terms',
                    8 => 'transporters.approval_status',
                    9 => 'adminmod.user_name', // Modified By
                    10 => 'transporters.last_on', // Modified On
                    11 => 'admin.user_name', // Created By
                    12 => 'transporters.created_on', // Created On
                ];

                foreach($this->searchData['columns'] as $idx => $cval)
                {
                    if($cval !== '')
                    {
                        $query->where(function ($q) use ($cval) {
                            $q->where('transporters.transporter_name', 'like', '%' . $cval . '%')
                                ->orWhere('transporters.pan', 'like', '%' . $cval . '%')
                                ->orWhere('transporters.gstin', 'like', '%' . $cval . '%')
                                ->orWhere('transporters.contact_person', 'like', '%' . $cval . '%')
                                ->orWhere('transporters.contact_person_mobile', 'like', '%' . $cval . '%')
                                ->orWhere('transporters.contact_person_email_id', 'like', '%' . $cval . '%')
                                ->orWhere('transporters.payment_terms', 'like', '%' . $cval . '%')
                                ->orWhereRaw("(
                                    CASE 
                                        WHEN transporters.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                                        WHEN transporters.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                                        WHEN transporters.approval_status = 'active' THEN 'Active'
                                        WHEN transporters.approval_status = 'deactive' THEN 'Deactive'
                                        ELSE ''
                                    END
                                ) LIKE ?", ['%' . $cval . '%'])
                                ->orWhere('adminmod.user_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('DATE_FORMAT(transporters.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%'])
                                ->orWhere('admin.user_name', 'like', '%' . $cval . '%')
                                ->orWhereRaw('DATE_FORMAT(transporters.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%']);
                        });

                        if(isset($mappings[$idx]))
                        {
                            if(in_array($mappings[$idx], ['transporters.last_on', 'transporters.created_on']))
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
                            else if(isset($mappings[$idx]) && $mappings[$idx] == 'transporters.approval_status')
                            {
                                $query->whereRaw("(
                                    CASE 
                                        WHEN transporters.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                                        WHEN transporters.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                                        WHEN transporters.approval_status = 'active' THEN 'Active'
                                        WHEN transporters.approval_status = 'deactive' THEN 'Deactive'
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
                $item->pan = $item->pan ? " " . $item->pan : $item->pan;
                $item->gstin = $item->gstin ? " " . $item->gstin : $item->gstin;
                $item->contact_person_mobile = $item->contact_person_mobile ? " " . $item->contact_person_mobile : $item->contact_person_mobile;
                $item->contact_person_email_id = $item->contact_person_email_id ? " " . $item->contact_person_email_id : $item->contact_person_email_id;
                return $item;
            });
        }

        public function headings(): array
        {
            return [
                'Transporter',
                'Address',
                'PAN',
                'GSTIN',
                'Person',
                'Person Mobile',
                'Person Email',
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
                'B' => NumberFormat::FORMAT_TEXT,
                'C' => NumberFormat::FORMAT_TEXT,
                'E' => NumberFormat::FORMAT_TEXT,
                'F' => NumberFormat::FORMAT_TEXT,
            ];
        }

        public function registerEvents(): array
        {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
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
?>