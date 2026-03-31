<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrystalReportController extends Controller
{
    //

     public function checkReportExists($id,$name,$type){
        
        $filePath = storage_path('app/public/reports/'.$type.'_reports_file/' . $name . '.pdf'); 
        // dd($filePath);
       // $filePath = public_path('reports/'.$type.'_reports_file/' . $name . '.pdf'); 
        $isFileExists = file_exists($filePath); 

        if($isFileExists){
            // $urldd = asset('storage/reports/'.$type.'_reports_file/'. $name.'.pdf');
            // // dd($urldd);
            // return redirect($urldd);
            $url = asset('storage/reports/'.$type.'_reports_file/'. $name.'.pdf');
            return redirect($url);

        }else{
            GeneratePdf(base64_decode($id),$name,$type,'','listing');
            sleep(2);
            $url = asset('storage/reports/'.$type.'_reports_file/'. $name.'.pdf');
            return redirect($url);
        }
    }
}