<?php
namespace App\Imports;
use App\Models\HsnCode;
use Maatwebsite\Excel\Concerns\ToModel;
class ImportHsnCode implements ToModel
{
   public function model(array $row)
   {
       return new HsnCode([
           'hsn_code' => $row[0],
           'hsn_description' => $row[1],

       ]);
   }
}
