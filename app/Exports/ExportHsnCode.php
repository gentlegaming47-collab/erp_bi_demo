<?php
    namespace App\Exports;

    use App\Models\HsnCode;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Illuminate\Support\Facades\DB;

    class ExportHsnCode implements FromCollection, WithHeadings {
        use Exportable;

        public function collection()
        {
            return HsnCode::select('hsn_code','hsn_description','adminmod.user_name','hsn_code.last_on','admin.user_name as createdby','hsn_code.created_on')
            ->join('admin','admin.id','=','hsn_code.created_by_user_id')
			->leftjoin('admin as adminmod','adminmod.id','=','hsn_code.last_by_user_id')
            ->get();
        }

        public function headings(): array
        {
            return [
                'HSN Code',
                'Description',
                'Modified By',
                'Modified On',
                'Created By',
                'Created On',

            ];
        }
    }
?>
