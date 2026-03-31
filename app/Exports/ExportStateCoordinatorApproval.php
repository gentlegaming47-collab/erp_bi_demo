<?php
    namespace App\Exports;
    
    use App\Models\MaterialRequest;
    use App\Models\MaterialRequestDetail;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\WithStyles;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
    use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
    use Maatwebsite\Excel\Concerns\ShouldAutoSize;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;
    

    class ExportStateCoordinatorApproval implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
    {
        use Exportable;
        
        private $searchData;

        public function __construct(array $searchData = [])
        {
            $this->searchData = $searchData;
        }

        public function collection()
        {
            $query = MaterialRequest::query()
                ->select([
                    DB::raw('DATE_FORMAT(material_request.state_coordinator_approvaldate, "%d/%m/%Y") as state_coordinator_approvaldate'),
                    'locations.location_name',
                    'material_request.mr_number',
                    DB::raw('DATE_FORMAT(material_request.mr_date, "%d/%m/%Y") as mr_date'),
                    'to_location.location_name as to_location',
                    'admin.user_name',
                    'material_request.special_notes',
                    'items.item_name',
                    'items.item_code',
                    'material_request_details.mr_qty',
                ])
                ->leftJoin('locations','locations.id','material_request.current_location_id')
                ->leftJoin('locations as to_location','to_location.id','material_request.to_location_id')
                ->join('admin','admin.id','=','material_request.state_coordinator_user_id')
                ->join('material_request_details','material_request_details.mr_id','=','material_request.mr_id')
                ->join('items','items.id','=','material_request_details.item_id')
                ->where('material_request.state_coordinator_user_id','=', Auth::user()->id)
                ->orderBy('material_request.mr_date', 'desc')
                ->orderBy('material_request.state_coordinator_approvaldate', 'desc');
            
            if(!empty($this->searchData['trans_from_date']))
            {
                try
                {
                    $date = Carbon::createFromFormat('d/m/Y', $this->searchData['trans_from_date'])->format('Y-m-d');
                    $query->whereDate('material_request.state_coordinator_approvaldate', '>=', $date);
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
                    $query->whereDate('material_request.state_coordinator_approvaldate', '<=', $date);
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
                            $subQ->whereRaw('DATE_FORMAT(material_request.state_coordinator_approvaldate, "%d/%m/%Y") LIKE ?', ['%' . $word . '%'])
                            ->orWhere('locations.location_name', 'like', '%' . $word . '%')
                            ->orWhere('material_request.mr_number', 'like', '%' . $word . '%')
                            ->orWhereRaw('DATE_FORMAT(material_request.mr_date, "%d/%m/%Y") LIKE ?', ['%' . $word . '%'])
                            ->orWhere('to_location.location_name', 'like', '%' . $word . '%')
                            ->orWhere('admin.user_name', 'like', '%' . $word . '%')
                            ->orWhere('material_request.special_notes', 'like', '%' . $word . '%');
                        });
                    }
                });
            }

            if(!empty($this->searchData['columns']))
            {
                $mappings = [
                    1 => 'material_request.state_coordinator_approvaldate',
                    2 => 'locations.location_name',
                    3 => 'material_request.mr_number',
                    4 => 'material_request.mr_date',
                    5 => 'to_location.location_name',
                    6 => 'admin.user_name',
                    7 => 'material_request.special_notes',
                ];

                foreach($this->searchData['columns'] as $idx => $cval)
                {
                    if($cval !== '')
                    {
                        $query->where(function ($q) use ($cval) {
                            $q->whereRaw('DATE_FORMAT(material_request.state_coordinator_approvaldate, "%d/%m/%Y") LIKE ?', ['%' . $cval . '%'])
                            ->orWhere('locations.location_name', 'like', '%' . $cval . '%')
                            ->orWhere('material_request.mr_number', 'like', '%' . $cval . '%')
                            ->orWhereRaw('DATE_FORMAT(material_request.mr_date, "%d/%m/%Y") LIKE ?', ['%' . $cval . '%'])
                            ->orWhere('to_location.location_name', 'like', '%' . $cval . '%')
                            ->orWhere('admin.user_name', 'like', '%' . $cval . '%')
                            ->orWhere('material_request.special_notes', 'like', '%' . $cval . '%');
                        });

                        if(isset($mappings[$idx]))
                        {
                            if(in_array($mappings[$idx], ['material_request.state_coordinator_approvaldate', 'material_request.mr_date']))
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

            // return $query->get();

            $results = $query->get();
            $results->transform(function ($item) {
                $qty = (float)$item->mr_qty;
                $item->mr_qty = number_format($qty, 3, '.', ''); 
                return $item;
            });

            return $results;
        }

        public function headings(): array
        {
            return [
                'Approval Date',
                'From Location',
                'MR No.',
                'MR Date',
                'To Location',
                'Approval By',
                'Sp. Note',
                'Item Name',
                'Item Code',
                'MR Qty.',
            ];
        }

        public function styles(Worksheet $sheet)
        {
            $sheet->getColumnDimension('J')->setWidth(15); 
            $sheet->getStyle('J2:J' . $sheet->getHighestRow())->getNumberFormat()->setFormatCode('0.000');
            
            return [
                1 => ['font' => ['bold' => true]],
            ];
        }
    }
?>