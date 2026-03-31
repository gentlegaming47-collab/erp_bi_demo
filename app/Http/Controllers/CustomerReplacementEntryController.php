<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomerReplacementEntry;
use App\Models\CustomerReplacementEntryDetails;
use App\Models\SOMappingDetails;
use App\Models\CustomerGroup;
use App\Models\Item;
use App\Models\ItemDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Date;
use Carbon\Carbon;
use DataTables;
use App\Models\Admin;
use Illuminate\Validation\Rule;



class CustomerReplacementEntryController extends Controller
{
    public function manage()
    {
        return view('manage.manage-customer_replacement_entry');
    }


    public function index(CustomerReplacementEntry $cre_data,Request $request,DataTables $dataTables)
    {
        $locationCode = getCurrentLocation()->id;

        $year_data    = getCurrentYearData();

        $cre_data = CustomerReplacementEntry::select(['customer_replacement_entry.cre_id', 'customer_replacement_entry.cre_sequence',
        'customer_replacement_entry.cre_number',
        'customer_replacement_entry.cre_date',
        'customer_replacement_entry.customer_reg_no',
        // 'customer_replacement_entry.customer_name',
        'customer_replacement_entry.rep_customer_name',
        // 'sales_order.customer_name',
        'customer_replacement_entry.cre_village',
        'districts.district_name','customer_replacement_entry.cre_taluka_id',
        'states.state_name','talukas.taluka_name',
        'customer_replacement_entry_details.return_qty',
        'customer_replacement_entry_details.remark',
        'items.item_name','items.item_code','item_groups.item_group_name','units.unit_name','customer_replacement_entry.created_on','customer_replacement_entry.last_on','customer_replacement_entry.created_by_user_id','customer_replacement_entry.last_by_user_id','created_user.user_name as created_by_name','last_user.user_name as last_by_name'
      ])

        ->leftJoin('customer_replacement_entry_details','customer_replacement_entry_details.cre_id','customer_replacement_entry.cre_id')
        ->leftJoin('items','items.id' ,'customer_replacement_entry_details.item_id')
        ->leftJoin('districts','districts.id','=','customer_replacement_entry.cre_district_id')
        ->leftJoin('talukas','talukas.id','=','customer_replacement_entry.cre_taluka_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'customer_replacement_entry.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'customer_replacement_entry.last_by_user_id')
        ->where('customer_replacement_entry.current_location_id', $locationCode)
        ->where('customer_replacement_entry.year_id', '=', $year_data->id);
        if($request->trans_from_date != "" && $request->trans_to_date != ""){
        
            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');

            $cre_data->whereDate('customer_replacement_entry.cre_date','>=',$from);
            $cre_data->whereDate('customer_replacement_entry.cre_date','<=',$to);

        }else if($request->trans_from_date != ""){

            $from =  Date::createFromFormat('d/m/Y', $request->trans_from_date)->format('Y-m-d');
            $cre_data->where('customer_replacement_entry.cre_date','>=',$from);

        }else if($request->trans_to_date != ""){

            $to =  Date::createFromFormat('d/m/Y', $request->trans_to_date)->format('Y-m-d');
            $cre_data->where('customer_replacement_entry.cre_date','<=',$to);

        } 

        return DataTables::of($cre_data)

      
        ->editColumn('created_by_user_id', function($cre_data){
            if($cre_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$cre_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name)?$created_by_user_id->user_name :'';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($cre_data){
            if($cre_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$cre_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('created_on', function($cre_data){
            if ($cre_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $cre_data->created_on)->format(DATE_TIME_FORMAT);
                return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('customer_replacement_entry.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(customer_replacement_entry.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($cre_data){
            if ($cre_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $cre_data->last_on)->format(DATE_TIME_FORMAT);
                 return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('customer_replacement_entry.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(customer_replacement_entry.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('cre_date', function($cre_data){
            if ($cre_data->cre_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $cre_data->cre_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->editColumn('return_qty', function($cre_data){
            return $cre_data->return_qty > 0 ? number_format((float)$cre_data->return_qty, 3, '.','') : number_format((float) 0, 3, '.','');
        })
       
        ->addColumn('options',function($cre_data){
            $action = "<div>";
            if(hasAccess("customer_replacement_entry","edit")){
            $action .="<a id='edit_a' href='".route('edit-customer_replacement_entry',['id' => base64_encode($cre_data->cre_id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }

            if(hasAccess("customer_replacement_entry","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->editColumn('cre_date', function($cre_data){
            if ($cre_data->cre_date != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d', $cre_data->cre_date)->format(DATE_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('customer_replacement_entry.cre_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(customer_replacement_entry.cre_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('item_name', function($cre_data){ 
            if($cre_data->item_name != ''){
                $item_name = ucfirst($cre_data->item_name);
                return $item_name;
            }else{
                return '';
            }
        })

        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on', 'options','cre_date','return_qty'])
        ->make(true);
    }

    public function create()
    {
        return view('add.add-customer_replacement_entry');
    }

    public function store(Request $request)
    {
        // dd($request->all());

          $year_data = getCurrentYearData();
          $locationID = getCurrentLocation()->id;
        
          $existNumber = CustomerReplacementEntry::where('cre_number','=',$request->cre_number)->where('cre_sequence','=',$request->cre_sequence)->where('year_id', '=', $year_data->id)->where('current_location_id',$locationID)->lockForUpdate()->first();
          
          if($existNumber){
              $latestNo = $this->getLatestCRENo($request);
              $tmp =  $latestNo->getContent();
              $area = json_decode($tmp, true);
              $cre_number =   $area['latest_cre_no'];
              $cre_sequence = $area['number'];              
          }else{
             $cre_number = $request->cre_number;
             $cre_sequence = $request->cre_sequence;
          }
          // end check duplicate number
         
             
         DB::beginTransaction();
         try{
             
             $cre_data=  CustomerReplacementEntry::create([
 
                 'cre_sequence'     => $cre_sequence,
                 'cre_number'       => $cre_number,
                 'cre_date' => Date::createFromFormat('d/m/Y', $request->cre_date)->format('Y-m-d'),
                 'rep_customer_id'     => $request->rep_customer_id != "" ?  $request->rep_customer_id : "",
                 'rep_customer_name'     => $request->rep_customer_name != "" ?  $request->rep_customer_name : "",
                 'customer_group_id' => $request->customer_group_id != "" ? $request->customer_group_id : null,
                 'cre_district_id'       =>  $request->cre_district_id != "" ? $request->cre_district_id : null,
                 'current_location_id'   => $locationID,
                 'customer_reg_no'       => $request->reg_no != "" ?  $request->reg_no : "",
                 'cre_village'           => $request->cre_village != "" ?  $request->cre_village : "",
                 'cre_pincode'           => $request->cre_pincode != "" ?   $request->cre_pincode : "",
                 'cre_district_id'       => $request->cre_district_id != "" ?  $request->cre_district_id : null,
                 'cre_taluka_id'         => $request->cre_taluka_id != "" ?  $request->cre_taluka_id : null,
                 'special_notes'      => $request->sp_notes != "" ? $request->sp_notes : "",  
                 'year_id'            =>  $year_data->id,
                 'company_id'         => Auth::user()->company_id,
                 'created_by_user_id' => Auth::user()->id,
                 'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),  
 
             ]);
            //  dd($cre_data);
             
             if ($cre_data->save()) {
 
                 foreach ($request->item_id as $ctKey => $ctVal ) 
                 {
                     
                     $fittingItems = Item::where('id', $ctVal)->pluck('fitting_item')->first();
 
                     if ($ctVal != null) {
                             $cre_details_data =  CustomerReplacementEntryDetails::create([
                                 'cre_id'     => $cre_data->cre_id,
                                 'item_id'    => $ctVal,
                                 'item_details_id' => isset($request->item_details_id[$ctKey]) ? $request->item_details_id[$ctKey] : null,
                                 'return_qty' => isset($request->return_qty[$ctKey]) ? $request->return_qty[$ctKey] : null,
                                 'return_details_qty' => isset($request->return_details_qty[$ctKey]) ? $request->return_details_qty[$ctKey] : null,
                                 'remark'     => isset($request->remark[$ctKey]) ? $request->remark[$ctKey] : null,
     
                             ]);  
                        }                     
                    }                 
                }
             
             if($cre_data->save())
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
             getActivityLogs("Customer Replacement Entry", "add", $e->getMessage(),$e->getLine());  
             return response()->json([
                 'response_code' => '0',
                 'response_message' => 'Error Occured Record Not Inserted',
                 'original_error' => $e->getMessage()
             ]);
         }
    }

    public function show($id)
    {
        return view('edit.edit-customer_replacement_entry')->with(['id' => $id ]);
    }

    public function edit(Request $request, $id)
    {
        $location = getCurrentLocation()->id;
        $isAnyPartInUse = false;
        $cre_data = CustomerReplacementEntry::select(['customer_replacement_entry.cre_id','customer_replacement_entry.cre_sequence','customer_replacement_entry.cre_number','customer_replacement_entry.cre_date','customer_replacement_entry.customer_reg_no','customer_replacement_entry.rep_customer_id', 'customer_replacement_entry.cre_village','customer_replacement_entry.cre_pincode','customer_replacement_entry.customer_group_id','customer_replacement_entry.special_notes','customer_replacement_entry.cre_taluka_id','districts.id as cre_district_id', 'states.id as cre_state_id','countries.id as cre_country_id', 'talukas.id as taluka_id',
        'customer_replacement_entry.rep_customer_name',])
        ->leftJoin('districts', 'districts.id','customer_replacement_entry.cre_district_id')
        ->leftJoin('talukas', 'talukas.district_id','districts.id')
        ->leftJoin('states','states.id', 'districts.state_id')
        ->leftJoin('countries','countries.id', 'states.country_id')
        ->where('customer_replacement_entry.cre_id',$id)->first();

        
        $cre_datails = CustomerReplacementEntryDetails::select(['customer_replacement_entry_details.*','items.item_code','item_groups.item_group_name','items.item_name','item_details.secondary_item_name','item_details.item_details_id', DB::raw("CASE WHEN item_details.item_details_id IS NOT NULL THEN units2.unit_name ELSE units1.unit_name END as unit_name")   ])
        ->leftJoin('items','items.id','=','customer_replacement_entry_details.item_id')
        ->leftJoin('item_details','item_details.item_details_id','=','customer_replacement_entry_details.item_details_id')  
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        // ->leftJoin('units','units.id','=','items.unit_id')
         ->leftJoin('units as units1','units1.id','=','items.unit_id')
        ->leftJoin('units as units2','units2.id','=','items.second_unit')      
        ->where('customer_replacement_entry_details.cre_id',$id)->get(); 

        if($cre_datails != null){

            $cre_datails->each(function ($item) use (&$isAnyPartInUse,&$location) {

                $isFound = SOMappingDetails::where('cre_detail_id','=',$item->cre_detail_id)->sum('map_qty');   

                if($isFound != null){
                    $item->in_use = true;
                    $item->used_qty = $isFound;
                    $isAnyPartInUse = true;


                }else{
                    $item->in_use = false;
                    $item->used_qty = 0;

                }
                $item->item_detail = ItemDetails::select('item_details.item_details_id','item_details.secondary_qty','item_details.secondary_item_name','units.unit_name',
                'location_stock_details.secondary_stock_qty'
                )
                ->leftJoin('location_stock_details','location_stock_details.item_details_id','=','item_details.item_details_id')
                ->leftJoin('items','items.id','=','item_details.item_id')
                ->leftJoin('units','units.id','=','items.second_unit')
                // ->where('location_stock_details.location_id','=',$location)
                ->groupBy('item_details.item_details_id')
                ->where('item_id',$item->item_id)->get();
                // dd($inUse);
                return $item;

            })->values();
        }

        if($cre_data){
            $cre_data->in_use = false;
            if($isAnyPartInUse == true){
                $cre_data->in_use = true;
            }
        }
     
        if ($cre_data) {
            if($cre_data != ""){
                $cre_data->cre_date = Date::createFromFormat('Y-m-d', $cre_data->cre_date)->format('d/m/Y');
            }
            return response()->json([
                'cre_data'                => $cre_data,
                'cre_datails'             => $cre_datails,
                'response_code'           => '1',
                'response_message'        => '',
            ]);
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }


    public function update(Request $request)
    {
        
        
        DB::beginTransaction();
        
        try{
            $locationID  = getCurrentLocation()->id;
            $year_data   = getCurrentYearData();

            $validated = $request->validate(
                [
                    'cre_sequence' => ['required','max:155',Rule::unique('customer_replacement_entry')->where(function ($query) use ($request,$year_data, $locationID) {                  
                        return $query->where('year_id','=',$year_data->id)->where('current_location_id','=',$locationID);
                    })->ignore($request->id, 'cre_id')],
                    
    
                    'cre_number' => ['required', 'max:155', Rule::unique('customer_replacement_entry')->where(function ($query) use ($request, $year_data, $locationID) {                 
                        return $query->where('year_id', '=', $year_data->id)->where('current_location_id','=',$locationID);
                    })->ignore($request->id, 'cre_id')],              
                ],
                [
                    'cre_sequence.unique'=>'PO Sequence Is Already Exists',    
                    'cre_number.required' => 'Please Enter PO Number',
                ]
                );



                $cre_data =  CustomerReplacementEntry::where('cre_id',$request->id)->update([
                    'cre_sequence'     => $request->cre_sequence,
                    'cre_number'       => $request->cre_number,
                    'cre_date' =>      Date::createFromFormat('d/m/Y', $request->cre_date)->format('Y-m-d'),
                    'rep_customer_id'     => $request->rep_customer_id != "" ?  $request->rep_customer_id : "",
                    'rep_customer_name'     => $request->rep_customer_name != "" ?  $request->rep_customer_name : "",
                    'customer_group_id' => $request->customer_group_id != "" ? $request->customer_group_id : null,
                    'cre_district_id'       =>  $request->cre_district_id != "" ? $request->cre_district_id : null,
                    'current_location_id'   => $locationID,
                    'customer_reg_no'       => $request->reg_no != "" ?  $request->reg_no : "",
                    'cre_village'           => $request->cre_village != "" ?  $request->cre_village : "",
                    'cre_pincode'           => $request->cre_pincode != "" ?   $request->cre_pincode : "",
                    'cre_district_id'       => $request->cre_district_id != "" ?  $request->cre_district_id : null,
                    'cre_taluka_id'         => $request->cre_taluka_id != "" ?  $request->cre_taluka_id : null,
                    'special_notes'      => $request->sp_notes != "" ? $request->sp_notes : "",  
                    'year_id'            => $year_data->id,
                    'company_id'         => Auth::user()->company_id,
                    'last_by_user_id'    => Auth::user()->id,
                    'last_on'            => Carbon::now('Asia/Kolkata')->toDateTimeString(), 
                ]);

                if($cre_data){
                    if (isset($request->cre_details_id) && !empty($request->cre_details_id)) {             
                        foreach ($request->cre_details_id as $sodKey => $sodVal) {  
                            
                            if($sodVal == "0"){
                                    if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                        $cre_details =  CustomerReplacementEntryDetails::create([
                                            'cre_id'        => $request->id,
                                            'item_id'       => $request->item_id[$sodKey],
                                            'item_details_id'     => isset($request->item_details_id[$sodKey]) ? $request->item_details_id[$sodKey]   : null,
                                            'return_qty'    => $request->return_qty[$sodKey],
                                            'return_details_qty'    => $request->return_details_qty[$sodKey],
                                            'remark'        => $request->remark[$sodKey],
                                        ]);
                                    }
                                }
                                else{        
                                    // dd($request->all());
                                    if(isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null){
                                        
                                        $cre_details =  CustomerReplacementEntryDetails::where('cre_detail_id',$sodVal)->update([
                                            'item_id'       => isset($request->item_id[$sodKey])?$request->item_id[$sodKey] : null ,
                                            'item_details_id'     => isset($request->item_details_id[$sodKey]) ? $request->item_details_id[$sodKey]   : null,
                                            'return_qty'    => isset($request->return_qty[$sodKey]) ? $request->return_qty[$sodKey] : null ,
                                            'return_details_qty'    => isset($request->return_details_qty[$sodKey]) ? $request->return_details_qty[$sodKey] : null ,
                                            'remark'        => isset($request->remark[$sodKey]) ? $request->remark[$sodKey] : "" 
                                        ]);
                                    }else{                                                                       CustomerReplacementEntryDetails::where('cre_detail_id', $sodVal)->delete();
                                    }
                                }
                         }
                    }
                    DB::commit();
                    return response()->json([
                        'response_code' => '1',
                        'response_message' => 'Record Updated Successfully.',
                    ]);
                } else{
                    DB::rollBack();
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Record Not Updated',
                    ]);
                }

        }
        catch(\Exception $e){ 
            // dd($e->getLine());                   
            DB::rollBack();
            getActivityLogs("Customer Replacement Entry", "update", $e->getMessage(),$e->getLine());  

            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Updated',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    // new code
    // public function update(Request $request)
    // {
    //     DB::beginTransaction();
        
    //     try {
    //         $locationID  = getCurrentLocation()->id;
    //         $year_data   = getCurrentYearData();

    //         $validated = $request->validate(
    //             [
    //                 'cre_sequence' => [
    //                     'required', 
    //                     'max:155', 
    //                     Rule::unique('customer_replacement_entry')->where(function ($query) use ($request, $year_data, $locationID) {                  
    //                         return $query->where('year_id', '=', $year_data->id)
    //                                     ->where('current_location_id', '=', $locationID);
    //                     })->ignore($request->id, 'cre_id')
    //                 ],
    //                 'cre_number' => [
    //                     'required', 
    //                     'max:155', 
    //                     Rule::unique('customer_replacement_entry')->where(function ($query) use ($request, $year_data, $locationID) {                 
    //                         return $query->where('year_id', '=', $year_data->id)
    //                                     ->where('current_location_id', '=', $locationID);
    //                     })->ignore($request->id, 'cre_id')
    //                 ],              
    //             ],
    //             [
    //                 'cre_sequence.unique' => 'PO Sequence Is Already Exists',    
    //                 'cre_number.required' => 'Please Enter PO Number',
    //             ]
    //         );

    //         $cre_data = CustomerReplacementEntry::where('cre_id', $request->id)->update([
    //             'cre_sequence' => $request->cre_sequence,
    //             'cre_number' => $request->cre_number,
    //             'cre_date' => Date::createFromFormat('d/m/Y', $request->cre_date)->format('Y-m-d'),
    //             'customer_name' => $request->customer_name != "" ? $request->customer_name : "",
    //             'customer_group_id' => $request->customer_group_id != "" ? $request->customer_group_id : null,
    //             'cre_district_id' => $request->cre_district_id != "" ? $request->cre_district_id : null,
    //             'current_location_id' => $locationID,
    //             'customer_reg_no' => $request->reg_no != "" ? $request->reg_no : "",
    //             'cre_village' => $request->cre_village != "" ? $request->cre_village : "",
    //             'cre_pincode' => $request->cre_pincode != "" ? $request->cre_pincode : "",
    //             'cre_district_id' => $request->cre_district_id != "" ? $request->cre_district_id : null,
    //             'cre_taluka_id' => $request->cre_taluka_id != "" ? $request->cre_taluka_id : null,
    //             'special_notes' => $request->sp_notes != "" ? $request->sp_notes : "",  
    //             'year_id' => $year_data->id,
    //             'company_id' => Auth::user()->company_id,
    //             'last_by_user_id' => Auth::user()->id,
    //             'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(), 
    //         ]);

    //         if ($cre_data) {
    //             if (isset($request->cre_details_id) && !empty($request->cre_details_id)) {
    //                 // Fetch existing details for the cre_id
    //                 $existingDetails = CustomerReplacementEntryDetails::where('cre_id', $request->id)->pluck('cre_detail_id')->toArray();
    //                 $detailsToKeep = [];

    //                 foreach ($request->cre_details_id as $sodKey => $sodVal) {  
    //                     if ($sodVal == "0") { // New entry
    //                         if (isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null) {
    //                             $cre_details = CustomerReplacementEntryDetails::create([
    //                                 'cre_id' => $request->id,
    //                                 'item_id' => $request->item_id[$sodKey],
    //                                 'return_qty' => $request->return_qty[$sodKey],
    //                                 'remark' => $request->remark[$sodKey],
    //                             ]);
    //                         }
    //                     } else { // Existing entry
    //                         $detailsToKeep[] = $sodVal; // Mark this detail to keep
    //                         if (isset($request->item_id[$sodKey]) && $request->item_id[$sodKey] != null) {
    //                             $cre_details = CustomerReplacementEntryDetails::where('cre_detail_id', $sodVal)->update([
    //                                 'item_id' => $request->item_id[$sodKey],
    //                                 'return_qty' => $request->return_qty[$sodKey],
    //                                 'remark' => $request->remark[$sodKey],
    //                             ]);
    //                         }
    //                     }
    //                 }

    //                 // Delete records that are no longer present in the request
    //                 $detailsToDelete = array_diff($existingDetails, $detailsToKeep);
    //                 if (!empty($detailsToDelete)) {
    //                     CustomerReplacementEntryDetails::whereIn('cre_detail_id', $detailsToDelete)->delete();
    //                 }
    //             }

    //             DB::commit();
    //             return response()->json([
    //                 'response_code' => '1',
    //                 'response_message' => 'Record Updated Successfully.',
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'response_code' => '0',
    //                 'response_message' => 'Record Not Updated',
    //             ]);
    //         }

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'response_code' => '0',
    //             'response_message' => 'Error Occurred. Record Not Updated.',
    //             'original_error' => $e->getMessage(),
    //         ]);
    //     }
    // }


    public function destroy(Request $request){
        DB::beginTransaction();
        try{

            $so_mapping = SOMappingDetails::
            leftJoin('customer_replacement_entry_details','customer_replacement_entry_details.cre_detail_id','=','so_mapping_details.cre_detail_id')
            ->where('customer_replacement_entry_details.cre_id',$request->id)->get();
            if($so_mapping->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Customer Replacement Entry Is Used In Customer Replacement SO Mapping.",
                ]);
            }


            CustomerReplacementEntry::destroy($request->id);
            CustomerReplacementEntryDetails::where('cre_id',$request->id)->delete();
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e) {
            DB::rollBack();
            getActivityLogs("Customer Replacement Entry", "delete", $e->getMessage(),$e->getLine());  
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

    


    public function getLatestCRENo(Request $request)
    {
          $modal  =  CustomerReplacementEntry::class;
          $sequence = 'cre_sequence';
          $prefix = 'RR';          
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);          
          $locationName = getCurrentLocation();            
          return response()->json([
            'response_code' => 1,
            'latest_cre_no'  => $sup_num_format['format'],
            'number'         => $sup_num_format['isFound'],
            'location'       => $locationName
        ]);

    }
}