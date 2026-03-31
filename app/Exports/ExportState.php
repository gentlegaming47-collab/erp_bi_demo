<?php
    namespace App\Exports;

    use App\Models\State;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Illuminate\Support\Facades\DB;

    class ExportState implements FromCollection, WithHeadings {
        use Exportable;

        public function collection()
        {
            return State::select(['states.state_name','states.gst_code','countries.country_name','adminmod.user_name','states.last_on','admin.user_name as createdby','states.created_on'])
            ->leftJoin('countries','countries.id','=','states.country_id')
            ->join('admin','admin.id','=','states.created_by_user_id')
			->leftjoin('admin as adminmod','adminmod.id','=','states.last_by_user_id')
            ->get();
        }

        public function headings(): array
        {
            return [
                'State',
                'Gst Code',
                'Country',
                'Modified By',
                'Modified On',
                'Created By',
                'Created On',
            ];
        }
    }
?>
