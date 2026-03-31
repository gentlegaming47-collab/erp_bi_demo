<?php

namespace App\Exports;

use App\Models\Dealer;
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

class ExportDealer implements FromCollection, WithHeadings, WithColumnFormatting, WithEvents, WithStyles
{
    use Exportable;

    private $searchData;

    public function __construct(array $searchData = [])
    {
        $this->searchData = $searchData;
    }

    public function collection()
    {
        $query = Dealer::query()
            ->select([
                'dealers.dealer_name',
                'dealers.dealer_code',
                'dealers.address',
                'villages.village_name',
                'dealers.pincode',
                'talukas.taluka_name',
                'districts.district_name',
                'states.state_name',
                'countries.country_name',
                'dealers.mobile_no',
                'dealers.email',
                'dealers.PAN',
                'dealers.gst_code',
                'dealers.aadhar_no',
                DB::raw("CASE 
                    WHEN dealers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                    WHEN dealers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                    WHEN dealers.approval_status = 'active' THEN 'Active'
                    WHEN dealers.approval_status = 'deactive' THEN 'Deactive'
                    ELSE ''
                END as approval_status_text"),
                DB::raw('DATE_FORMAT(dealer_agreement.agreement_end_date, "%d/%m/%Y") as agreement_end_date'),
                DB::raw("CASE 
                    WHEN dealer_agreement.agreement_document = 'dealer_agreement.agreement_document' THEN ''
                    ELSE ''
                END as agreement_document"),
                'dealer_contacts.contact_person',
                'dealer_contacts.contact_mobile_no',
                'dealer_contacts.contact_email',
                'adminmod.user_name as modified_by',
                DB::raw('DATE_FORMAT(dealers.last_on, "' . DATE_TIME_FORMAT_RAW . '") as last_on'),
                'admin.user_name as createdby',
                DB::raw('DATE_FORMAT(dealers.created_on, "' . DATE_TIME_FORMAT_RAW . '") as created_on')
            ])
            ->leftJoin('dealer_contacts','dealer_contacts.dealer_id','=','dealers.id')
            ->leftJoin('dealer_agreement','dealer_agreement.dealer_id','=','dealers.id')
            ->leftJoin('villages','villages.id','=','dealers.village_id')
            ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
            ->leftJoin('districts','districts.id','=','talukas.district_id')
            ->leftJoin('states','states.id','=','districts.state_id')
            ->leftJoin('countries','countries.id','=','states.country_id')
            ->join('admin','admin.id','=','dealers.created_by_user_id')
            ->leftJoin('admin as adminmod','adminmod.id','=','dealers.last_by_user_id')
            ->orderBy('dealers.dealer_name','asc');

        if(!empty($this->searchData['global']))
        {
            $global = $this->searchData['global'];
            $query->where(function ($q) use ($global) {
                $q->where('dealers.dealer_name', 'like', '%' . $global . '%')
                    ->orWhere('dealers.dealer_code', 'like', '%' . $global . '%')
                    ->orWhere('villages.village_name', 'like', '%' . $global . '%')
                    ->orWhere('dealers.pincode', 'like', '%' . $global . '%')
                    ->orWhere('talukas.taluka_name', 'like', '%' . $global . '%')
                    ->orWhere('districts.district_name', 'like', '%' . $global . '%')
                    ->orWhere('states.state_name', 'like', '%' . $global . '%')
                    ->orWhere('countries.country_name', 'like', '%' . $global . '%')
                    ->orWhere('dealers.mobile_no', 'like', '%' . $global . '%')
                    ->orWhere('dealers.email', 'like', '%' . $global . '%')
                    ->orWhere('dealers.PAN', 'like', '%' . $global . '%')
                    ->orWhere('dealers.gst_code', 'like', '%' . $global . '%')
                    ->orWhere('dealers.aadhar_no', 'like', '%' . $global . '%')
                    ->orWhereRaw("(
                        CASE 
                            WHEN dealers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                            WHEN dealers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                            WHEN dealers.approval_status = 'active' THEN 'Active'
                            WHEN dealers.approval_status = 'deactive' THEN 'Deactive'
                            ELSE ''
                        END
                    ) = ?", [$global])
                    ->orWhereRaw("DATE_FORMAT(dealer_agreement.agreement_end_date, '%d/%m/%Y') like ?",  ['%' . $global . '%'])
                    // ->orWhere('dealer_contacts.contact_person', 'like', '%' . $global . '%')
                    // ->orWhere('dealer_contacts.contact_mobile_no', 'like', '%' . $global . '%')
                    // ->orWhere('dealer_contacts.contact_email', 'like', '%' . $global . '%')
                    ->orWhere('adminmod.user_name', 'like', '%' . $global . '%')
                    ->orWhereRaw('DATE_FORMAT(dealers.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%'])
                    ->orWhere('admin.user_name', 'like', '%' . $global . '%')
                    ->orWhereRaw('DATE_FORMAT(dealers.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $global . '%']);
            });
        }

        // if(!empty($this->searchData['global']))
        // {
        //     $global = $this->searchData['global'];
        //     $keywords = explode(' ', $global);
        //     $query->where(function ($q) use ($keywords) {
        //         foreach ($keywords as $word) {
        //             $q->where(function ($subQ) use ($word) {
        //                 $subQ->where('dealers.dealer_name', 'like', '%' . $word . '%')
        //             ->orWhere('dealers.dealer_code', 'like', '%' . $word . '%')
        //             ->orWhere('villages.village_name', 'like', '%' . $word . '%')
        //             ->orWhere('dealers.pincode', 'like', '%' . $word . '%')
        //             ->orWhere('talukas.taluka_name', 'like', '%' . $word . '%')
        //             ->orWhere('districts.district_name', 'like', '%' . $word . '%')
        //             ->orWhere('states.state_name', 'like', '%' . $word . '%')
        //             ->orWhere('countries.country_name', 'like', '%' . $word . '%')
        //             ->orWhere('dealers.mobile_no', 'like', '%' . $word . '%')
        //             ->orWhere('dealers.email', 'like', '%' . $word . '%')
        //             ->orWhere('dealers.PAN', 'like', '%' . $word . '%')
        //             ->orWhere('dealers.gst_code', 'like', '%' . $word . '%')
        //             ->orWhere('dealers.aadhar_no', 'like', '%' . $word . '%')
        //             // ->orWhereRaw("(
        //             //     CASE 
        //             //         WHEN dealers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
        //             //         WHEN dealers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
        //             //         WHEN dealers.approval_status = 'active' THEN 'Active'
        //             //         WHEN dealers.approval_status = 'deactive' THEN 'Deactive'
        //             //         ELSE ''
        //             //     END
        //             // ) LIKE ?", ['%' . $word . '%'])
        //             // ->orWhere('dealers.approval_status', 'like', '%' . $word . '%')
        //             ->orWhereRaw("(
        //                 CASE 
        //                     WHEN dealers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
        //                     WHEN dealers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
        //                     WHEN dealers.approval_status = 'active' THEN 'Active'
        //                     WHEN dealers.approval_status = 'deactive' THEN 'Deactive'
        //                     ELSE ''
        //                 END
        //             ) = ?", [$word])
        //             ->orWhereRaw("DATE_FORMAT(dealer_agreement.agreement_end_date, '%d/%m/%Y') like ?",  ['%' . $word . '%'])
        //             // ->orWhere('dealer_contacts.contact_person', 'like', '%' . $word . '%')
        //             // ->orWhere('dealer_contacts.contact_mobile_no', 'like', '%' . $word . '%')
        //             // ->orWhere('dealer_contacts.contact_email', 'like', '%' . $word . '%')
        //             ->orWhere('adminmod.user_name', 'like', '%' . $word . '%')
        //             ->orWhereRaw('DATE_FORMAT(dealers.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $word . '%'])
        //             ->orWhere('admin.user_name', 'like', '%' . $word . '%')
        //             ->orWhereRaw('DATE_FORMAT(dealers.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $word . '%']);
        //             });
        //         }
        //     });
        // }

        if(!empty($this->searchData['columns']))
        {
            $mappings = [
                1 => 'dealers.dealer_name',
                2 => 'dealers.dealer_code',
                3 => 'villages.village_name',
                4 => 'dealers.pincode',
                5 => 'talukas.taluka_name',
                6 => 'districts.district_name',
                7 => 'states.state_name',
                8 => 'countries.country_name',
                9 => 'dealers.mobile_no',
                10 => 'dealers.email',
                11 => 'dealers.PAN',
                12 => 'dealers.gst_code',
                13 => 'dealers.aadhar_no',
                14 => 'dealers.approval_status',
                15 => 'dealer_agreement.agreement_end_date',
                16 => 'adminmod.user_name', // Modified By
                17 => 'dealers.last_on', // Modified On
                18 => 'admin.user_name', // Created By
                19 => 'dealers.created_on', // Created On
            ];

            foreach($this->searchData['columns'] as $idx => $cval)
            {
                if($cval !== '')
                {
                    $query->where(function ($q) use ($cval) {
                        $q->where('dealers.dealer_name', 'like', '%' . $cval . '%')
                            ->orWhere('dealers.dealer_code', 'like', '%' . $cval . '%')
                            ->orWhere('villages.village_name', 'like', '%' . $cval . '%')
                            ->orWhere('dealers.pincode', 'like', '%' . $cval . '%')
                            ->orWhere('talukas.taluka_name', 'like', '%' . $cval . '%')
                            ->orWhere('districts.district_name', 'like', '%' . $cval . '%')
                            ->orWhere('states.state_name', 'like', '%' . $cval . '%')
                            ->orWhere('countries.country_name', 'like', '%' . $cval . '%')
                            ->orWhere('dealers.mobile_no', 'like', '%' . $cval . '%')
                            ->orWhere('dealers.email', 'like', '%' . $cval . '%')
                            ->orWhere('dealers.PAN', 'like', '%' . $cval . '%')
                            ->orWhere('dealers.gst_code', 'like', '%' . $cval . '%')
                            ->orWhere('dealers.aadhar_no', 'like', '%' . $cval . '%')
                            // ->orWhereRaw("CASE 
                            //         WHEN dealers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                            //         WHEN dealers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                            //         WHEN dealers.approval_status = 'active' THEN 'Active'
                            //         WHEN dealers.approval_status = 'deactive' THEN 'Deactive'
                            //         ELSE ''
                            //     END like ?", '%' . $cval . '%')
                            ->orWhereRaw("(
                                CASE 
                                    WHEN dealers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                                    WHEN dealers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                                    WHEN dealers.approval_status = 'active' THEN 'Active'
                                    WHEN dealers.approval_status = 'deactive' THEN 'Deactive'
                                    ELSE ''
                                END
                            ) LIKE ?", ['%' . $cval . '%'])
                            ->orWhereRaw("DATE_FORMAT(dealer_agreement.agreement_end_date, '%d/%m/%Y') like ?",  ['%' . $cval . '%'])
                            // ->orWhere('dealer_contacts.contact_person', 'like', '%' . $cval . '%')
                            // ->orWhere('dealer_contacts.contact_mobile_no', 'like', '%' . $cval . '%')
                            // ->orWhere('dealer_contacts.contact_email', 'like', '%' . $cval . '%')
                            ->orWhere('adminmod.user_name', 'like', '%' . $cval . '%')
                            ->orWhereRaw('DATE_FORMAT(dealers.last_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%'])
                            ->orWhere('admin.user_name', 'like', '%' . $cval . '%')
                            ->orWhereRaw('DATE_FORMAT(dealers.created_on, "' . DATE_TIME_FORMAT_RAW . '") LIKE ?', ['%' . $cval . '%']);
                    });

                    if(isset($mappings[$idx]))
                    {
                        if(in_array($mappings[$idx], ['dealers.last_on', 'dealers.created_on']))
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
                        else if(isset($mappings[$idx]) && $mappings[$idx] == 'dealers.approval_status')
                        {
                            $query->whereRaw("(
                                CASE 
                                    WHEN dealers.approval_status = 'deactive_approval_pending' THEN 'Deactive Approval Pending'
                                    WHEN dealers.approval_status = 'approval_pending' THEN 'Active Approval Pending'
                                    WHEN dealers.approval_status = 'active' THEN 'Active'
                                    WHEN dealers.approval_status = 'deactive' THEN 'Deactive'
                                    ELSE ''
                                END
                            ) = ?", [$cval]);
                        }

                        else if(in_array($mappings[$idx], ['dealer_agreement.agreement_end_date']))
                        {
                            $query->whereRaw("DATE_FORMAT({$mappings[$idx]}, '%d/%m/%Y') LIKE ?", ['%' . $cval . '%']);
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
            $item->mobile_no = $item->mobile_no ? " " . $item->mobile_no : $item->mobile_no;
            $item->aadhar_no = $item->aadhar_no ? " " . $item->aadhar_no : $item->aadhar_no;
            $item->contact_mobile_no = $item->contact_mobile_no ? " " . $item->contact_mobile_no : $item->contact_mobile_no;
            return $item;
        });
    }

    public function headings(): array
    {
        return [
            'Dealer',
            'Dealer Code',
            'Address',
            'Village',
            'Pin Code',
            'Taluka',
            'District',
            'State',
            'Country',
            'Mobile',
            'Email',
            'PAN',
            'GSTIN',
            'Aadhar No.',
            'Status',
            'Agreement End Date',
            'Agreement Document',
            'Name',
            'Mobile',
            'Email',
            'Modified By',
            'Modified On',
            'Created By',
            'Created On',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_TEXT, // Dealer Mobile No.
            'M' => NumberFormat::FORMAT_TEXT, // Dealer Aadhar No.
            'R' => NumberFormat::FORMAT_TEXT, // Contact Mobile No.
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(15);
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