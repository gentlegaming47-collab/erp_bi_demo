<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Dealer;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DealerStatusUpdateUtilityController extends Controller
{
    //
    public function manage(){
        return view('manage.manage-dealer_status_update_utility');
    }

     public function index(Dealer $dealer,Request $request,DataTables $dataTables)
    {
        $dealer_data = Dealer::select(['dealers.id','dealers.dealer_name','dealers.dealer_code','villages.village_name','countries.country_name','states.state_name','districts.district_name','talukas.taluka_name','dealers.mobile_no','dealers.email','dealers.PAN','dealers.gst_code','dealers.aadhar_no','dealers.address','dealers.pincode','dealers.status','dealers.created_on','dealers.created_by_user_id','dealers.last_by_user_id','dealers.last_on','dealers.pan', 'dealers.aggrement_start_date', 'dealers.aggrement_end_date', 'dealers.approval_status'])
        ->leftJoin('villages','villages.id','=','dealers.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->whereIn('dealers.approval_status',['active','deactive']);
        // ->get()
        // ;
       
        return DataTables::of($dealer_data)
        ->editColumn('created_by_user_id', function($dealer_data){
            if($dealer_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$dealer_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->editColumn('last_by_user_id', function($dealer_data){
            if($dealer_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$dealer_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->editColumn('GSTIN	', function($dealer_data){
            if($dealer_data->gst_code != null){
                $gstno = Dealer::where('id','=',$dealer_data->gst_code)->first('gst_code');
                return isset($gstno->gst_code) ? $gstno->gst_code : '';
            }else{
                return '';
            }

        })
        
        ->editColumn('dealer_name', function($dealer_data){
            if($dealer_data->dealer_name != ''){
                $dealer_name = ucfirst($dealer_data->dealer_name);
                return $dealer_name;
            }else{
                return '';
            }
            //return Str::limit($dealer_data->dealer_name, 50);
        })
        
        ->editColumn('village_name', function($dealer_data){
            if($dealer_data->village_name != ''){
                $village_name = ucfirst($dealer_data->village_name);
                return $village_name;
            }else{
                return '';
            }
        
        })      
        ->editColumn('aggrement_start_date', function($dealer_data){
            if ($dealer_data->aggrement_start_date != null) {
                $formatedDate4 = Date::createFromFormat('Y-m-d', $dealer_data->aggrement_start_date)->format('d/m/Y'); 
               
                return $formatedDate4;
            }else{
                return '';
            }
        })
     
        ->editColumn('approval_status', function($dealer_data){
            if($dealer_data->approval_status != ''){
                if($dealer_data->approval_status == 'active'){
                    $status = 'Active';
                }elseif($dealer_data->approval_status == 'deactive'){
                    $status = 'Deactive';
                }else{
                    $status = '';
                }
                return $status;
            }else{
                return '';
            }
        })
        // ->filterColumn('dealers.approval_status', function($query, $keyword) {
        //     $keyword = strtolower($keyword);
        //     $query->where('dealers.approval_status', 'like', "$keyword%");
        // })
       
        ->editColumn('created_on', function($dealer_data){
            if ($dealer_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $dealer_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->editColumn('last_on', function($dealer_data){
            if ($dealer_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $dealer_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->addColumn('options',function($dealer_data){
            $action = "<div>
               <input type='checkbox' name='dealer_id[]' class='simple-check' 
            id='dealer_ids_". $dealer_data->id ."'  value='". $dealer_data->id ."'  data-id='". $dealer_data->id ."' />
            <input type='hidden' name='dealer_current_status[]' value='". $dealer_data->approval_status ."'/>
            
            </div>";
           
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','dealer_name','village_name', 'aggrement_start_date', 'aggrement_end_date', 'options','agreement_document'])
        ->make(true);
    }


    public function update(Request $request){
        DB::beginTransaction();

        $approval_status = $request->dealer_status == 'active' ?  'approval_pending' :  'deactive_approval_pending' ;      
        
        try{
             $request->dealer_data = json_decode($request->dealer_data,true);
         
            if(isset($request->dealer_data) && !empty($request->dealer_data)){

                foreach ($request->dealer_data as $key => $value) {
                    if($request->dealer_status != $value['dealer_current_status']){
                        $dealer_data =  Dealer::where('id','=',$value['dealer_id'])->update([             
                            'approval_status'    => $approval_status,               
                        ]);   
                    }                
                
                }
            }
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Updated Successfully.',
            ]);

        }catch(\Exception $e){                    
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Updated',
                'original_error' => $e->getMessage()
            ]);
        }
    }

}