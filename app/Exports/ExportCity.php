<?php
    namespace App\Exports;
    use App\Models\City;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Illuminate\Support\Facades\DB;

    class ExportCity implements FromCollection, WithHeadings {
        use Exportable;

        public function collection()
        {
            return City::select(['cities.city_name','states.state_name','countries.country_name','adminmod.user_name','cities.last_on','admin.user_name as createdby','cities.created_on'])
            ->leftJoin('states','states.id','=','cities.state_id')
            ->leftJoin('countries','countries.id','=','states.country_id')
            ->join('admin','admin.id','=','cities.created_by_user_id')
			->leftjoin('admin as adminmod','adminmod.id','=','cities.last_by_user_id')
            ->get();
        }

        public function headings(): array
        {
            return [
                'City',
                'State',
                'Country',
                'Modified By',
                'Modified On',
                'Created By',
                'Created On',
            ];
        }
    }
?>
