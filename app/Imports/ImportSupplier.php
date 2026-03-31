<?php
namespace App\Imports;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
class ImportSupplier implements ToModel
{
   public function model(array $row)
   {
       return new Supplier([
           'supplier_name' => $row[0],
           'supplier_code' => $row[1],
           'city_name' => $row[2],
           'state_name' => $row[3],
           'country_name' => $row[4],
           'phone_no' => $row[5],
           'email' => $row[6],
           'web_address' => $row[7],
           'pan' => $row[8],
           'gstin' => $row[9],
           'payment_terms' => $row[10],
           'contact_person' => $row[11],
           'person_phone' => $row[12],
           'person_email' => $row[13],
           'approved_supplier' => $row[14],
       ]);
   }
}
