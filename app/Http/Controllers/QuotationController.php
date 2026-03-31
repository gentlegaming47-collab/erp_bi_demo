<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocationCustomerGroupMapping;
use App\Models\Dealer;
use App\Models\Location;
use App\Models\QuotationDetails;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QuotationController extends Controller
{
    public function index(Quotation $quotation,Request $request,DataTables $dataTables)
    {
        $year_data = getCurrentYearData();
        $location = getCurrentLocation();

        $quotation = Quotation::select(['quotation.id','quotation.quot_sequence','quotation.quot_number','quotation.customer_name','quotation.quot_date',
        'quotation_details.quot_qty','quotation_details.rate_per_unit','quotation_details.quot_amount',
        'customer_groups.customer_group_name', 'villages.village_name','talukas.taluka_name','districts.district_name','states.state_name','countries.country_name','dealers.dealer_name','mis_category.mis_category','quotation.created_on','quotation.created_by_user_id','quotation.last_by_user_id','quotation.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'

        ])

        ->leftJoin('customer_groups','customer_groups.id','=','quotation.customer_group_id')
        ->leftJoin('quotation_details','quotation_details.quot_id','=','quotation.id')
        ->leftJoin('districts','districts.id','=','quotation.quot_district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('talukas','talukas.id','=','quotation.quot_taluka_id')
        ->leftJoin('villages','villages.id','=','quotation.quot_village_id')      
        ->leftJoin('dealers','dealers.id','=','quotation.dealer_id')
        ->leftJoin('mis_category','mis_category.id','=','quotation.mis_category_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'quotation.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'quotation.last_by_user_id')
        ->where('current_location_id','=',$location->id)
        ->where('quotation.year_id', '=', $year_data->id)
        ->groupBy('quotation.quot_number');
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');


            $quotation->whereDate('quotation.quot_date','>=',$from);

            $quotation->whereDate('quotation.quot_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');

            $quotation->where('quotation.quot_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $quotation->where('quotation.quot_date','<=',$to);

        }  

        return DataTables::of($quotation)
        ->editColumn('created_by_user_id', function($quotation){
            if($quotation->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$quotation->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($quotation){
            if($quotation->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$quotation->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('item_name', function($quotation){ 
            if($quotation->item_name != ''){
                $item_name = ucfirst($quotation->item_name);
                return $item_name;
            }else{
                return '';
            }
        })
        ->editColumn('quot_qty', function($quotation){
            return $quotation->quot_qty > 0 || $quotation->quot_qty != ""  ? number_format((float)$quotation->quot_qty, 3, '.','') : '';
        })
        ->editColumn('rate_per_unit', function($quotation){
            return $quotation->rate_per_unit > 0 ? number_format((float)$quotation->rate_per_unit, 2, '.','') : '';
        })
            
        ->editColumn('quot_amount', function($quotation){
            return $quotation->quot_amount > 0 ? number_format((float)$quotation->quot_amount, 2, '.','') : '';
        })
        ->editColumn('created_on', function($quotation){
            if ($quotation->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $quotation->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('quotation.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(quotation.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($quotation){
            if ($quotation->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $quotation->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('quotation.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(quotation.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($quotation){
            $action = "<div>";
            if(hasAccess("quotation","print")){
                $action .="<a id='print_a' target='_blank' href='".route('print-quotation',['id' => base64_encode($quotation->id)]) ."' data-placement='top' data-original-title='Print' title='Print'><i class='iconfa-print action-icon'></i></a>";
            }
            if(hasAccess("quotation","edit")){
            $action .="<a id='edit_a' href='".route('edit-quotation',['id' => base64_encode($quotation->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("quotation","delete")){
            $action .= "<i id='del_a'  href='".route('delete-quotation',['id' => base64_encode($quotation->id)]) ."'  data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })   
    
        ->editColumn('quot_date', function($quotation){
            if ($quotation->quot_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $quotation->quot_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('quotation.quot_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(quotation.quot_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('dealer_name', function($quotation){
            if($quotation->dealer_name != ''){
                $name = ucfirst($quotation->dealer_name);
                return $name;
            }else{
                return '';
            }
        })
        
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','city_name','options','quot_date','dealer_name'])
        ->make(true);
    }

    public function manage()
    {
        return view('manage.manage-quotation');
    }

    public function create()
    {
        return view('add.add-quotation');
    }

    public function Store(Request $request){

        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

        //  check duplicate number

        $existNumber = Quotation::where('quot_number','=',$request->quot_no)->where('quot_sequence','=',$request->quot_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationID)->lockForUpdate()->first();
          
        if($existNumber){
            $latestNo = $this->getLatestQuotationNo($request);
            $tmp =  $latestNo->getContent();
            $area = json_decode($tmp, true);
            $quot_number =   $area['latest_po_no'];
            $quot_sequence = $area['number'];              
        }else{
            $quot_number   = $request->quot_no;
            $quot_sequence = $request->quot_sequence;
        }
        // end check duplicate number


        $totalQty = 0;
        $totalAmount = 0;
 
        foreach ($request->item_id as $ctKey => $ctVal) {
        
            if ($ctVal != null) {
                $totalQty += $request->quot_qty[$ctKey];  
                $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";        
            }
        }
             
         DB::beginTransaction();
         try{
             
             $quotation_data=  Quotation::create([ 

                 'quot_sequence'      =>  $quot_sequence, 
                 'quot_number'         => $quot_number, 
                 'total_qty'           => $totalQty, 
                 'total_amount'        => $totalAmount, 
                 'quot_date'           => Date::createFromFormat('d/m/Y', $request->quot_date)->format('Y-m-d'),
                 'customer_group_id'   => $request->customer_group_id, 
                 'customer_name'       => $request->customer_name,
                 'dealer_id'           => $request->dealer_id, 
                 'quot_district_id'    => $request->quot_district_id, 
                 'current_location_id' => $locationID, 
                 'mobile_no'           => $request->quot_mobile_no,
                 'quot_village_id'     => $request->quot_village_id,
                 'pincode'             => $request->pincode, 
                 'quot_district_id'    => $request->quot_district_id, 
                 'quot_taluka_id'      => $request->quot_taluka_id, 
                 'mis_category_id'     => $request->mis_category_id, 
                 'basic_amount'        => $request->basic_amount != "" ?  $request->basic_amount : null, 
                 'less_discount_percentage' => $request->less_discount_percentage != "" ?  $request->less_discount_percentage : null, 
                 'less_discount_amount'     => $request->less_discount_amount != "" ?  $request->less_discount_amount : null, 
                 'secondary_transport'      => $request->secondary_transport !="" ?  $request->secondary_transport : null, 
                 'gst_type_fix_id'  => $request->gst_type_fix_id != "" ?  $request->gst_type_fix_id : null, 
                 'sgst_percentage'  => $request->sgst_percentage !=  "" ?  $request->sgst_percentage : null, 
                 'sgst_amount'      => $request->sgst_amount != "" ?  $request->sgst_amount : null,
                 'cgst_percentage'  => $request->cgst_percentage != "" ?  $request->cgst_percentage : null,
                 'cgst_amount'      => $request->cgst_amount != "" ?  $request->cgst_amount : null, 
                 'igst_percentage'  => $request->igst_percentage != "" ?  $request->igst_percentage : null, 
                 'igst_amount'      => $request->igst_amount != "" ?  $request->igst_amount : null,
                 'net_amount'       => $request->net_amount != "" ?  $request->net_amount : null, 
                 'round_off_val'    => $request->round_off != "" ?  $request->round_off : null, 
                 'special_notes'    => $request->special_notes,  
                 'year_id'            =>  $year_data->id,                 
                 'company_id'         => Auth::user()->company_id,
                 'created_by_user_id' => Auth::user()->id, 
                 'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),   
             ]);
 
             
             if ($quotation_data->save()) {
 
                 foreach ($request->item_id as $ctKey => $ctVal ) 
                 {
 
                    if ($ctVal != null) {
                      
                             $quot_details_data =  QuotationDetails::create([

                                 'quot_id' => $quotation_data->id,
     
                                 'item_id' => $ctVal,
     
                                 'quot_qty' => isset($request->quot_qty[$ctKey]) ? $request->quot_qty[$ctKey] : null,
     
                                 'rate_per_unit' => isset($request->rate_unit[$ctKey]) ? $request->rate_unit[$ctKey] : null,
     
                                 'quot_amount' =>isset($request->amount[$ctKey]) ? $request->amount[$ctKey] : null,

                                 'status' => 'Y',
                              
                             ]);  
                            
                    }                        
                 }                 
             }
             
             if($quotation_data->save())
             { 
                 DB::commit();
                 return response()->json([
                     'response_code' => '1',                
                     'response_message' => 'Record Inserted Successfully.',
                 ]);
             }
             else {
                DB::rollBack();
                 return response()->json([
                     'response_code' => '0',
                     'response_message' => 'Record Not Inserted',
                 ]);
             }
         }
         catch(\Exception $e){
             DB::rollBack();
             getActivityLogs("Quotation", "add", $e->getMessage(),$e->getLine());
             return response()->json([
                 'response_code' => '0',
                 'response_message' => 'Error Occured Record Not Inserted',
                 'original_error' => $e->getMessage()
             ]);
         }
 
    }

    public function show(Quotation $quotation, $id)
    {
        return view('edit.edit-quotation')->with('id',$id);
    }

    public function edit($id)
    {
    
        $quotation = Quotation::select([

        'quotation.id','quotation.quot_sequence','quotation.quot_number','quotation.quot_date',
        'quotation.customer_group_id','quotation.dealer_id', 'quotation.pincode',
        'quotation.current_location_id','quotation.customer_name',
        'quotation.mobile_no','quotation.mis_category_id','quotation.less_discount_percentage','quotation.less_discount_amount', 'quotation.special_notes', 'quotation.total_qty', 'quotation.total_amount', 'quotation.basic_amount', 'quotation.secondary_transport', 'quotation.gst_type_fix_id',
        'quotation.sgst_percentage', 'quotation.sgst_amount', 'quotation.cgst_percentage',
        'quotation.cgst_amount','quotation.igst_percentage','quotation.igst_amount',
        'quotation.net_amount','quotation.round_off_val','quotation.quot_taluka_id',
        'quotation.quot_village_id','states.id as state_id', 'districts.id as district_id', 'countries.id as country_id'])
        ->leftJoin('districts', 'districts.id','quotation.quot_district_id')
        ->leftJoin('talukas', 'talukas.district_id','districts.id')
        ->leftJoin('states','states.id', 'districts.state_id')
        ->leftJoin('countries','countries.id', 'states.country_id')
        ->where('quotation.id',$id)->first();
    
        $quotation_details = QuotationDetails::select(['quotation_details.quot_details_id','quotation_details.item_id','quotation_details.rate_per_unit','quotation_details.quot_qty','quotation_details.quot_amount','items.item_code','item_groups.item_group_name','units.unit_name','items.item_name'])

        ->leftJoin('items','items.id','=','quotation_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('quotation_details.quot_id',$id)->get(); 

        if ($quotation) {

            $quotation->quot_date = Date::createFromFormat('Y-m-d', $quotation->quot_date)->format('d/m/Y');
            
            return response()->json([
                'quot_data'         => $quotation,
                'quot_part_details' => $quotation_details,
                'response_code'     => '1',
                'response_message'  => '',
            ]);
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

    public function update(Request $request, Quotation $quotation_data)
    {
        // dd($request->all());
        $year_data = getCurrentYearData();

        $year_data = getCurrentYearData();
        $locationID = getCurrentLocation()->id;

        $validated = $request->validate(
            [
                'quot_sequence' => ['required','max:155',Rule::unique('quotation')->where(function ($query) use ($request,$year_data, $locationID) {
                    return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'id')],

                'quot_no' => ['required', 'max:155', Rule::unique('quotation','quot_number')->where(function ($query) use ($request, $year_data, $locationID) {
                    return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                })->ignore($request->id, 'id')],              
            ],
            [
                'quot_sequence.unique' => 'Quotation NO.Is Already Exists',    
                'quot_no.required' => 'Please Enter Quotation NO.',
                'quot_no.max'      => 'Maximum 155 Characters Allowed',
            ]
        );


      

         $totalQty = 0;
         $totalAmount = 0;
 
        foreach ($request->item_id as $ctKey => $ctVal) {
        
            if ($ctVal != null) {
                $totalQty += $request->quot_qty[$ctKey];  
                $request->amount != "" && $request->amount != null ? $totalAmount += $request->amount[$ctKey] : "";        
            }
        }

        DB::beginTransaction();
        try {
            $quotation_data =  Quotation::where("id", "=", $request->id)->update([

                'quot_sequence'       => $request->quot_sequence, 
                'quot_number'         => $request->quot_no,           
                'total_qty'           => $totalQty, 
                'total_amount'        => $totalAmount, 
                'quot_date'           => Date::createFromFormat('d/m/Y', $request->quot_date)->format('Y-m-d'),
                'customer_group_id'   => $request->customer_group_id, 
                'customer_name'       => $request->customer_name,
                'dealer_id'           => $request->dealer_id, 
                'quot_district_id'    => $request->quot_district_id, 
                'current_location_id' => $locationID, 
                'mobile_no'           => $request->quot_mobile_no,
                'quot_village_id'     => $request->quot_village_id,
                'pincode'             => $request->pincode, 
                'quot_district_id'    => $request->quot_district_id, 
                'quot_taluka_id'      => $request->quot_taluka_id, 
                'mis_category_id'     => $request->mis_category_id, 
                'basic_amount'        => $request->basic_amount != "" ?  $request->basic_amount : null, 
                'less_discount_percentage' => $request->less_discount_percentage != "" ?  $request->less_discount_percentage : null, 
                'less_discount_amount'     => $request->less_discount_amount != "" ?  $request->less_discount_amount : null, 
                'secondary_transport'      => $request->secondary_transport !="" ?  $request->secondary_transport : null, 
                'gst_type_fix_id'  => $request->gst_type_fix_id != "" ?  $request->gst_type_fix_id : null, 
                'sgst_percentage'  => $request->sgst_percentage !=  "" ?  $request->sgst_percentage : null, 
                'sgst_amount'      => $request->sgst_amount != "" ?  $request->sgst_amount : null,
                'cgst_percentage'  => $request->cgst_percentage != "" ?  $request->cgst_percentage : null,
                'cgst_amount'      => $request->cgst_amount != "" ?  $request->cgst_amount : null, 
                'igst_percentage'  => $request->igst_percentage != "" ?  $request->igst_percentage : null, 
                'igst_amount'      => $request->igst_amount != "" ?  $request->igst_amount : null,
                'net_amount'       => $request->net_amount != "" ?  $request->net_amount : null, 
                'round_off_val'    => $request->round_off != "" ?  $request->round_off : null, 
                'special_notes'    => $request->special_notes,              
                'year_id'          => $year_data->id,
                'last_by_user_id'  => Auth::user()->id,
                'last_on'          => Carbon ::now('Asia/Kolkata')->toDateTimeString(),                
            ]);


            if ($quotation_data)   {
                
                $QuotationDetails = QuotationDetails::where('quot_id', '=', $request->id)->get();

                // this cose use to stock maintain
                $oldQuotDetails = QuotationDetails::where('quot_id', '=', $request->id)->get();
                $oldQuotDetailsData = [];
                if($oldQuotDetails != null){
                    $oldQuotDetailsData = $oldQuotDetails->toArray();
                }

             

                if (isset($request->quot_details_id) && !empty($request->quot_details_id)) {

                    $QuotationDetails =  QuotationDetails::where('quot_id',$request->id)->update([
                        'status' => 'D',
                    ]);

                    foreach ($request->quot_details_id as $quotKey => $quotVal) {
                        
                        // create new 
                        if($quotVal == "0"){


                            if(isset($request->item_id[$quotKey]) && $request->item_id[$quotKey] != null){
                               
                                $QuotationDetails =  QuotationDetails::create([

                                    'quot_id'   => $request->id,
                                    'item_id'   => $request->item_id[$quotKey],
                                    'quot_qty'  => isset($request->quot_qty[$quotKey]) ? $request->quot_qty[$quotKey] : null,
                                    'rate_per_unit' => isset($request->rate_unit[$quotKey]) ? $request->rate_unit[$quotKey]: null,
                                    'quot_amount' =>isset($request->amount[$quotKey]) ? $request->amount[$quotKey] : null,
                                    'status' => 'Y',

                                ]);
                            }
                        }else{                          
                            
                            // update as it is old data

                            if(isset($request->item_id[$quotKey]) && $request->item_id[$quotKey] != null){
                               
                                $QuotationDetails =  QuotationDetails::where('quot_details_id',$quotVal)->update([
                                    'quot_id'  => $request->id,
                                    'item_id'  => $request->item_id[$quotKey],
                                    'quot_qty' => isset($request->quot_qty[$quotKey]) ? $request->quot_qty[$quotKey] : null,
                                    'rate_per_unit' => isset($request->rate_unit[$quotKey]) ? $request->rate_unit[$quotKey]: null,
                                    'quot_amount' =>isset($request->amount[$quotKey]) ? $request->amount[$quotKey] : null,
                                    'status' => 'Y',
                                ]);                            
                         
                            }
                        }

                    }
                }   

                $QuotationDetails = QuotationDetails::where('quot_id',$request->id)->where('status','D')->delete();

                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                ]);
            } 
        else {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Not Updated',
            ]);
        }
        }
        catch (\Exception $e) {
           
            DB::rollBack();
            getActivityLogs("Quotation", "update", $e->getMessage(),$e->getLine());
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Updated',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{

            Quotation::destroy($request->id);
            QuotationDetails::where('quot_id',$request->id)->delete();
            DB::commit();

            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){       
            DB::rollBack(); 
            getActivityLogs("Quotation", "delete", $e->getMessage(),$e->getLine());
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
            }else{
                $error_msg = "Record Not Deleted";
            }
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }

    public function getLatestQuotationNo(Request $request)
    {    
        $modal  =  Quotation::class;
        $sequence = 'quot_sequence';
        $prefix = 'QUOT';
        $po_num_format = getLatestSequence($modal,$sequence,$prefix);

        $locationName = getCurrentLocation();

        return response()->json([
          'response_code' => 1,
          'latest_po_no'  => $po_num_format['format'],
          'number'        => $po_num_format['isFound'],
          'location'      => $locationName
      ]);
    }

    public function getQuotationDealer(Request $request){

        $location = LocationCustomerGroupMapping::select('location_id')->where('customer_group_id',$request->customer_group_id)->get();

        $location_data = Location::select(['districts.state_id'])
        ->leftJoin('villages','villages.id','=','locations.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        //->leftJoin('states','states.id','=','districts.state_id')
        ->whereIn('locations.id',$location)
        ->groupBy('districts.state_id')
        ->get();
        // group by 


        if(isset($request->id)){
            $QuotDealerId = DB::table('quotation')
            ->where('id',$request->id)
            ->value('dealer_id');

            $dealers = Dealer::select('dealers.id', 'dealers.dealer_name', 'districts.state_id')
            ->leftJoin('villages', 'villages.id', '=', 'dealers.village_id')
            ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
            ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
            // ->leftJoin('states', 'states.id', '=', 'districts.state_id')
            ->where(function ($query) use ($QuotDealerId, $location_data) {
                $query->where('dealers.id', '=', $QuotDealerId) // Specific dealer by ID
                      ->orWhere(function ($subQuery) use ($location_data) {
                          $subQuery->whereIn('districts.state_id',$location_data)
                                   ->where('dealers.status', '=', 'active'); // Active dealers in the state
                      });
            })
            
            ->orderBy('dealers.dealer_name', 'asc')
            ->get();

        }else{
            
            $dealers = Dealer::select('dealers.id', 'dealers.dealer_name', 'districts.state_id')
            ->leftJoin('villages', 'villages.id', '=', 'dealers.village_id')
            ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
            ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
            //->leftJoin('states', 'states.id', '=', 'districts.state_id')
            ->whereIn('districts.state_id',$location_data)
            ->where('dealers.status', '=', 'active')
            ->orderBy('dealers.dealer_name', 'asc')
            ->get();
        }

        return response()->json([
            'quot_dealer'      => $dealers,
            'response_code'    => '1',
            'response_message' => '',
        ]);
    }
}