<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Responce;

class ConstantController extends Controller
{
    /**
     * ----------- getAllConstants() AND getSpecificConstants($array) defined in "helpers.php" file under (app/) folder
     */

    /**
     * Return constant variables
     */
    public function index(Request $request){
        return response()->json([
            'constants' => getAllConstants(),
            'response_code' => 1
        ]);
    }

     /**
     * Return specific constant variable
     */
    public function specific(Request $request){

        if(is_array($request->constant_names)){
            return response()->json([
                'constants' => getSpecificConstants($request->constant_names),
                'response_code' => 1
            ]);
        }else{
            return response()->json([
                'constants' => "",
                'response_message' => "Please send array of constant names",
                'response_code' => 0
            ]);
        }
        
    }
}
