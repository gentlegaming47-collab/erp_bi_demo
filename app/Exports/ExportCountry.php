<?php
    namespace App\Exports;

    use App\Models\Country;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Illuminate\Support\Facades\DB;

    class ExportCountry implements FromCollection, WithHeadings {
        use Exportable;

        public function collection()
        {
            return Country::select(['countries.country_name','adminmod.user_name','countries.last_on','admin.user_name as createdby','countries.created_on'])
            ->join('admin','admin.id','=','countries.created_by_user_id')
			->leftjoin('admin as adminmod','adminmod.id','=','countries.last_by_user_id')
            ->get();
        }

        public function headings(): array
        {
            return [
                'Country',
                'Modified By',
                'Modified On',
                'Created By',
                'Created On',

            ];
        }
    }
?>
