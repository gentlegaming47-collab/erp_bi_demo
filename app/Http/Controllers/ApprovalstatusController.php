<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\Supplier;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalstatusController extends Controller
{
    public function manage()
    {
        return view('manage.manage-approval_status');
    }
    public function getApprovalStatus(Request $request)
    {
        // $type = $request->type;
        // // dd($type);
        // // $term = $request->term;
        // if ($type == 'Dealer') {
        //     $query = Dealer::select('id','approval_status','dealer_name')->whereIn('approval_status', ['approval_pending', 'deactive_approval_pending']);
        //     // if ($term != "") {
        //     //     $query->where('dealer_name', 'LIKE', $term . '%'); // Filter by dealer name
        //     // }
        //     $data = $query->get();
            
        // } elseif ($type == 'Suppliers') {
        //     $query = Supplier::select('id','approval_status','supplier_name')->whereIn('approval_status', ['approval_pending', 'deactive_approval_pending']);
        //     // if ($term != "") {
        //     //     $query->where('supplier_name', 'LIKE', $term . '%'); // Filter by supplier name
        //     // }
        //     $data = $query->get(); 

        // } elseif ($type == 'Transporter') {
        //     $query = Transporter::select('id','approval_status','transporter_name')->whereIn('approval_status', ['approval_pending', 'deactive_approval_pending']);
        //     // if ($term != "") {
        //     //     $query->where('transporter_name', 'LIKE', $term . '%'); // Filter by transporter name
        //     // }
        //     $data = $query->get(); 
        //     // dd($data);

        // } else {
        //     $data = [];
        // }


        $map = [
            'Dealer' => [\App\Models\Dealer::class, 'dealer_name'],
            'Suppliers' => [\App\Models\Supplier::class, 'supplier_name'],
            'Transporter' => [\App\Models\Transporter::class, 'transporter_name'],
        ];
        
        $data = collect();
        
        foreach ($map as $type => [$model, $nameColumn]) {
            $results = $model::select('id', 'approval_status', $nameColumn)
                ->whereIn('approval_status', ['approval_pending', 'deactive_approval_pending'])
                ->get()
                ->map(function ($item) use ($type, $nameColumn) {
                    return [
                        'id' => $item->id,
                        'approval_status' => $item->approval_status,
                        'name' => $item->{$nameColumn},
                        'type' => $type, // optional: include model type
                    ];
                });
        
            $data = $data->merge($results);
        }
        


        $data = $data->map(function ($item) {
            // Format name
            $item['name'] = !empty($item['name']) 
                ? ucfirst($item['name']) 
                : '';
        
            // Format approval status
            if (!empty($item['approval_status'])) {
                if ($item['approval_status'] === 'approval_pending') {
                    $item['approval_status'] = 'Active Approval Pending';
                } elseif ($item['approval_status'] === 'deactive_approval_pending') {
                    $item['approval_status'] = 'Deactive Approval Pending';
                }
            }
        
            return $item;
        });

      $data = $data->transform(function ($val) {
        if ($val['type'] === 'Dealer') {
            $dealer_state = \App\Models\Dealer::select('states.state_name')
                ->leftJoin('villages', 'villages.id', '=', 'dealers.village_id')
                ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
                ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
                ->leftJoin('states', 'states.id', '=', 'districts.state_id')
                ->where('dealers.id', $val['id'])
                ->first();

            $val['state'] = $dealer_state->state_name ?? '';
        } elseif ($val['type'] === 'Suppliers') {
            $supplier_state = \App\Models\Supplier::select('states.state_name')
                ->leftJoin('villages', 'villages.id', '=', 'suppliers.village_id')
                ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
                ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
                ->leftJoin('states', 'states.id', '=', 'districts.state_id')
                ->where('suppliers.id', $val['id'])
                ->first();

            $val['state'] = $supplier_state->state_name ?? '';
        } else {
            $val['state'] = '';
        }

        return $val;
    });


        if($data){
            return response()->json([
                'response_code' => '1',
                'approval_data' => $data,
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => '',
            ]);
        }

    }


    public function store(Request $request){
        DB::beginTransaction();
        try{
            $request->approve_data = json_decode($request->approve_data,true);

            if(isset($request->approve_data) && !empty($request->approve_data)){
                foreach($request->approve_data as $ctKey => $ctVal){
                    if($ctVal['status_type'] == 'Dealer'){
                        $update = Dealer::where('id', $ctVal['approval_status_id'])->update([
                            'status' => $ctVal['change_type'] != '' ? $ctVal['change_type'] == 'Active Approval Pending' ? 'active' : 'deactive' : '',
                            'approval_status' => $ctVal['change_type'] != '' ? $ctVal['change_type'] == 'Active Approval Pending' ? 'active' : 'deactive' : '',
                        ]);   

                    }elseif($ctVal['status_type'] == 'Suppliers'){                        
                        $update = Supplier::where('id', $ctVal['approval_status_id'])->update([
                            'status' => $ctVal['change_type'] != '' ? $ctVal['change_type'] == 'Active Approval Pending' ? 'active' : 'deactive' : '',
                            'approval_status' => $ctVal['change_type'] != '' ? $ctVal['change_type'] == 'Active Approval Pending' ? 'active' : 'deactive' : '',
                        ]);   

                    }elseif($ctVal['status_type'] == 'Transporter'){                        
                        $update = Transporter::where('id', $ctVal['approval_status_id'])->update([
                            'status' => $ctVal['change_type'] != '' ? $ctVal['change_type'] == 'Active Approval Pending' ? 'active' : 'deactive' : '',
                            'approval_status' => $ctVal['change_type'] != '' ? $ctVal['change_type'] == 'Active Approval Pending' ? 'active' : 'deactive' : '',
                        ]);   
                        
                    }

                }
            }


            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Approved Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack();           
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
           
       
    }

}