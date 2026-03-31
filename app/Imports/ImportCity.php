<?php
namespace App\Imports;
use App\Models\City;
use Maatwebsite\Excel\Concerns\ToModel;
class ImportCity implements ToModel
{
   public function model(array $row)
   {
       return new City([
           'city_name' => $row[0],
           'state_name' => $row[1],
           'country_name' => $row[2],
       ]);
   }
}
