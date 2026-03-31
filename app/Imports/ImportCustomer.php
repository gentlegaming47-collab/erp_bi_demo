<?php
namespace App\Imports;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
class ImportCustomer implements ToModel
{
   public function model(array $row)
   {
       return new User([
           'customer_name' => $row[0],
           'customer_code' => $row[1],
           'customer_type_fix_id' => $row[2],
           'city_name' => $row[3],
           'state_name' => $row[4],
           'country_name' => $row[5],
           'phone' => $row[6],
           'email' => $row[7],
           'web_address' => $row[8],
           'pan' => $row[9],
           'gstin' => $row[10],
           'payment_terms' => $row[11],
       ]);
   }
}
