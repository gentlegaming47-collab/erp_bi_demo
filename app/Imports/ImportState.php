<?php
namespace App\Imports;
use App\Models\State;
use Maatwebsite\Excel\Concerns\ToModel;
class ImportState implements ToModel
{
   public function model(array $row)
   {
       return new User([
           'state_name' => $row[0],
           'gst_code' => $row[1],
           'country' => $row[2],
       ]);
   }
}
