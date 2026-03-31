<?php

namespace App\Exports;

use App\Models\Village; 
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportVillage implements FromCollection, WithHeadings, WithChunkReading, WithStyles
{
    use Exportable;

    public function collection()
    {
        $query =  Village::select(['villages.village_name', 'villages.default_pincode', 'talukas.taluka_name', 'districts.district_name', 'states.state_name',
        'countries.country_name', 'b.user_name as last_by_user_id', 'villages.last_on', 'a.user_name as created_by_user_id', 'villages.created_on'])
        ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
        ->leftJoin('districts', 'districts.id', 'talukas.district_id')
        ->leftJoin('states', 'states.id', '=', 'districts.state_id')
        ->leftJoin('countries', 'countries.id', '=', 'states.country_id')
        ->leftJoin('admin as a', 'a.id', '=', 'villages.created_by_user_id')
        ->leftJoin('admin as b', 'b.id', '=', 'villages.last_by_user_id')
        ->get();

        return $query;
    }
    
    public function headings(): array
    {
        return [
            'Village',
            'Pin Code',
            'Taluka',
            'District',
            'State',
            'Country',
            'Modified By',
            'Modified On',
            'Created On',
            'Created By'
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Make the header row bold
        ];
    }
}
