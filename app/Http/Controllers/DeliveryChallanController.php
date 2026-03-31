<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryChallan;

class DeliveryChallanController extends Controller
{
    public function manage()
    {
        return view('manage.manage-delivery_challan');
    }

    public function create()
    {
        return view('add.add-delivery_challan');
    }

    public function getLatestDCNo(Request $request)
    {
          $modal  =  DeliveryChallan::class;
          $sequence = 'dc_sequence';
          $prefix = 'DC';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_po_no'  => $sup_num_format['format'],
            'number'        => $sup_num_format['isFound'],
            'location'      => $locationName
        ]);

    }

}
