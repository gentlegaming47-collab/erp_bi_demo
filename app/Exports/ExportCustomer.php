<?php
    namespace App\Exports;

    use App\Models\Customer;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Illuminate\Support\Facades\DB;

    class ExportCustomer implements FromCollection, WithHeadings {
        use Exportable;

        public function collection()
        {
            return Customer::select(['customers.customer_name','customers.customer_code','customer_type.name','cities.city_name','states.state_name','countries.country_name','customers.phone','customers.email','customers.web_address','customers.gstin'
            ,'customers.payment_terms','customers.pan','adminmod.user_name','customers.last_on','admin.user_name as createdby','customers.created_on'])
            ->leftJoin('customer_type','customer_type.id','=','customers.customer_type_fix_id')
            ->leftJoin('cities','cities.id','=','customers.city_id')
            ->leftJoin('states','states.id','=','cities.state_id')
            ->leftJoin('countries','countries.id','=','states.country_id')
            ->join('admin','admin.id','=','customers.created_by_user_id')
			->leftjoin('admin as adminmod','adminmod.id','=','customers.last_by_user_id')
            ->get();
        }

        public function headings(): array
        {
            return [
                'Customer',
                'Code',
                'Type',
                'City',
                'State',
                'Country',
                'Phone',
                'Email',
                'Web',
                'PAN',
                'GSTIN',
                'Payment Terms',
                'Modified By',
                'Modified On',
                'Created By',
                'Created On',
            ];
        }
    }
?>
