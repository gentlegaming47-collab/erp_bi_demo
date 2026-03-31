<?php

namespace App\Imports;

use App\Models\Taluka;
use App\Models\City;
use App\Models\Village;
use App\Models\State;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


use Maatwebsite\Excel\Concerns\ToModel;

class talukaimport implements ToModel,WithHeadingRow
{
    

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //  dd($row);
        // Assuming the row structure is [district_name, sub_district_name]
        $stateName = $row['state_name'];
        $districtName = $row['district_name'];
        $subDistrictName = $row['subdistrict_name'];
        $villageName = $row['village_name'];
        $pincode = $row['pincode'];

        // dd($districtName);

        info("@ " . __LINE__ . " ==> State Name: " . $stateName);
        info("@ " . __LINE__ . " ==> District Name: " . $districtName);
        info("@ " . __LINE__ . " ==> Taluka Name: " . $subDistrictName);
        info("@ " . __LINE__ . " ==> Village Name: " . $villageName);


        // Find the State_id ID by statename
        $stateId = null;
        $state =  State::where('state_name', $stateName)->first();
        if($state){
            $stateId = $state->id;
        } else {
            info("@ " . __LINE__ . " ==> State not found: " . $stateName);
        }

        // Find the district ID by district name
        $districtId = null;
        $district = City::where('district_name', $districtName)->first();

        // add sitrcit 
        if (empty($district)) {
            info("@ " . __LINE__ . " ==> Creatting District " . $districtName);
            $district = City::query()->create([
                'district_name' => $districtName,
                'state_id'      => $stateId,
                'company_id'    => 1, // Add any additional fields if necessary
            ]);
            $districtId = $district->id;
        } else {
            $districtId = $district->id;
            info("@ " . __LINE__ . " ==> District Found " . $districtName . ", Id: " . $districtId);
        }

        // If the district is found, create a new Taluka entry
        if (!empty($district)) {
            // dd($district);
            
            // for check unique ness
            $existingTaluka = Taluka::where('taluka_name', $subDistrictName)
            ->where('district_id', $districtId)
            ->first();

                $talukaId = null;
                if (empty($existingTaluka)) {
                    info("@ " . __LINE__ . " ==> Creatting Taluka " . $subDistrictName);
                    $taluka = Taluka::query()->create([
                        'taluka_name' => $subDistrictName,
                        'district_id' => $districtId,
                        'company_id'  => 1,
                    ]);
                    $talukaId = $taluka->id;
                } else {
                    $talukaId = $existingTaluka->id;
                    info("@ " . __LINE__ . " ==> Taluka Found: " . $subDistrictName . ", Talukd Id: " .$talukaId);
                }

                if(!empty($talukaId)){
                    info("@ " . __LINE__ . " ==> Creatting Village " . $villageName);
                    $village = Village::query()->create([
                            'village_name'    => $villageName,
                            'taluka_id'       => $talukaId,
                            'default_pincode' => $pincode,
                            'company_id'      => 1,
                    ]);
                    
                }else{
                    info("@ " . __LINE__ . " ==> Taluka Id not found " . $subDistrictName);
                }
            } else {
                info("@ " . __LINE__ . " ==> District not found " . $districtName);
            }
            info("@ " . __LINE__ . " Continueing...");
            return null; // Return null if the district was not found
    }
}


    //chat gpt code
    // public function model(array $row)
    // {
    //     // Extract row data
    //     $districtName = $row['district_name'];
    //     $subDistrictName = $row['subdistrict_name'];
    //     $villageName = $row['village_name'];
    //     $pincode = $row['pincode'];

    //     // Find the district by name
    //     $district = City::where('district_name', $districtName)->first();

    //     if ($district) {
    //         // Find or create the Taluka
    //         $taluka = Taluka::firstOrCreate(
    //             [
    //                 'taluka_name' => $subDistrictName,
    //                 'district_id' => $district->id,
    //             ],
    //             [
    //                 'taluka_name' => $subDistrictName,
    //                 'district_id' => $district->id,
    //             ]
    //         );

    //         // Find or create the Village
    //         Village::firstOrCreate(
    //             [
    //                 'village_name' => $villageName,
    //                 'taluka_id' => $taluka->id,
    //             ],
    //             [
    //                 'village_name' => $villageName,
    //                 'taluka_id' => $taluka->id,
    //                 'default_pincode' => $pincode
    //             ]
    //         );
    //     }

    //     return null; // Return null if the district was not found
    // }
    // }
