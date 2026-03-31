<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Taluka;
use App\Models\City;
use App\Models\Village;
use App\Models\State;
use App\Models\Country;
use App\Models\Dealer;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



class ImportDealer implements ToModel,WithHeadingRow
{
  
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
// dd($row);
        $dealerName = $row['dealer_name'];
        $address = $row['address'];
        $country = $row['country'];
        $stateName = $row['state'];
        $districtName = $row['district'];
        $talukaName = $row['taluka'];
        $village = $row['village'];
        $pincode = $row['pin_code'];
        $mobile_no = $row['mobile_no'];
        $email_id = $row['email_id'];
        $pan = $row['pan'];
        $gst_in = $row['gstin'];
        $aadhar_no = $row['aadhar_no'];


        $countryId = null;
        $countryName =  Country::where('country_name', $country)->first();
        if($countryName){
            $countryId = $countryName->id;
        } else {
            info("@ " . __LINE__ . " ==> State not found: " );
        }


        $stateId = null;
        $state =  State::where('country_id', $countryId)->where('state_name',$stateName)->first();
        if($state){
            $stateId = $state->id;
        } else {
            info("@ " . __LINE__ . " ==> State not found: ");
        }

        $districtId = null;
        $district =  City::where('state_id', $stateId)->where('district_name',$districtName)->first();
        if($district){
            $districtId = $district->id;
        } else {
            info("@ " . __LINE__ . " ==> State not found: " );
        }

        $talukaId = null;
        $taluka =  Taluka::where('district_id', $districtId)->where('taluka_name',$talukaName)->first();
        if($taluka){
            $talukaId = $taluka->id;
        } else {
            info("@ " . __LINE__ . " ==> State not found: " );
        }

        // dd($talukaId);
        $villageId = null;
        $village =  Village::where('taluka_id', $talukaId)->where('village_name',$village)->first();
        if($village){
            $villageId = $village->id;
        } else {
            info("@ " . __LINE__ . " ==> State not found: " );
        }

        
        if(!empty($villageId)){

            $dealer = Dealer::query()->create([
                    'dealer_name'    => $dealerName,
                    'address'       => $address,
                    'village_id' => $villageId,
                    'pincode'      => $pincode,
                    'mobile_no'      => $mobile_no,
                    'email'      => $email_id,
                    'PAN'      => $pan,
                    'gst_code'      => $gst_in,
                    'aadhar_no'      => $aadhar_no,
                    'company_id'         => Auth::user()->company_id,
                    'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                    'created_by_user_id' => Auth::user()->id,
            ]);
            
        }else{
            $dealer = Dealer::query()->create([
                'dealer_name'    => $dealerName,
                'address'       => $address,
                'village_id' => 0,
                'pincode'      => $pincode,
                'mobile_no'      => $mobile_no,
                'email'      => $email_id,
                'PAN'      => $pan,
                'gst_code'      => $gst_in,
                'aadhar_no'      => $aadhar_no,
                'company_id'         => Auth::user()->company_id,
                'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id,
        ]);
        }

    }
}