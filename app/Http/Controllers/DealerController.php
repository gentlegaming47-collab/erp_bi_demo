<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\City;
use App\Models\Country;
use App\Models\File;
use App\Models\State;
use App\Models\CustomerType;
use App\Models\CustomerContacts;
use App\Models\Village;
use App\Models\CustomerGroup;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Dealer;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportCustomer;
use App\Imports\ImportDealer;
use App\Models\DealerAgreement;
use App\Models\SalesOrder;
use App\Models\DealerContacts;
use App\Models\LocationCustomerGroupMapping;
use App\Exports\ExportDealer;

class DealerController extends Controller
{
    

    public function manage()
    {
        return view('manage.manage-dealer');
    }

    public function create()
    {
        // $village = Village::orderBy('village_name', 'ASC')->get();                
        return view('add.add-dealer');
    }

    public function index(Dealer $dealer,Request $request,DataTables $dataTables)
    {
        $dealer_data = Dealer::select([
            'dealers.id',
            'dealers.dealer_name',
            'dealers.dealer_code',
            'villages.village_name',
            'countries.country_name',
            'states.state_name',
            'districts.district_name',
            'talukas.taluka_name',
            'dealers.mobile_no',
            'dealers.email',
            'dealers.PAN',
            'dealers.gst_code',
            'dealers.aadhar_no',
            'dealers.address',
            'dealers.pincode',
            'dealers.status',
            'dealers.created_on',
            'dealers.created_by_user_id',
            'dealers.last_by_user_id',
            'dealers.last_on',
            'dealers.pan',
            'dealers.aggrement_start_date',
            // 'dealers.aggrement_end_date',
            'dealers.approval_status',
            // 'dealer_agreement.agreement_end_date',
            'created_user.user_name as created_by_name',
            'last_user.user_name as last_by_name',

            // DB::raw('(SELECT MAX(da.agreement_end_date) 
            //   FROM dealer_agreement AS da 
            //   WHERE da.dealer_id = dealers.id) AS agreement_end_date')
        ])
        ->leftJoin('villages','villages.id','=','dealers.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        // ->leftJoin('dealer_agreement','dealer_agreement.dealer_id','=','dealers.id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'dealers.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'dealers.last_by_user_id');
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
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($dealer_data){
            if($dealer_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$dealer_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
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
        // ->editColumn('aggrement_end_date', function($dealer_data){
        //     // if ($dealer_data->aggrement_end_date != null) {
        //     //     $formatedDate5 = Date::createFromFormat('Y-m-d', $dealer_data->aggrement_end_date)->format('d/m/Y'); 
        //     //     return $formatedDate5;
        //     // }else{
        //     //     return '';
        //     // }
        //     if($dealer_data->id != ''){
        //     $aggrement_end_date = DealerAgreement::select(DB::raw('MAX(dealer_agreement.agreement_end_date) as agreement_end_date'))->where('dealer_id',$dealer_data->id) ->groupBy('dealer_agreement.dealer_id')->first();         
        //     return $aggrement_end_date != '' ? Date::createFromFormat('Y-m-d', $aggrement_end_date->agreement_end_date)->format('d/m/Y') : '';
        //     }else{
        //         return '';
        //     }
        // })

        ->addColumn('agreement_end_date', function($dealer_data){
            if($dealer_data->id != ''){
            $agreement_end_date = DealerAgreement::select(DB::raw('MAX(dealer_agreement.agreement_end_date) as agreement_end_date'))->where('dealer_id',$dealer_data->id)->groupBy('dealer_agreement.dealer_id')->first();         
            return $agreement_end_date != '' ? Date::createFromFormat('Y-m-d', $agreement_end_date->agreement_end_date)->format('d/m/Y') : '';
            }else{
                return '';
            }
        })
        // ->filterColumn('agreement_end_date', function ($query, $keyword) {
        //     $query->whereRaw("DATE_FORMAT(dealer_agreement.agreement_end_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        // })

        ->filterColumn('agreement_end_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT((SELECT MAX(dealer_agreement.agreement_end_date) FROM dealer_agreement WHERE dealer_agreement.dealer_id = dealers.id), '%d/%m/%Y'
            ) LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('approval_status', function($dealer_data){
            if($dealer_data->approval_status != ''){
                if($dealer_data->approval_status == 'deactive_approval_pending'){
                    $status = 'Deactive Approval Pending';
                }elseif($dealer_data->approval_status == 'approval_pending'){
                    $status = 'Active Approval Pending';
                }elseif($dealer_data->approval_status == 'active'){
                    $status = 'Active';
                }elseif($dealer_data->approval_status == 'deactive'){
                    $status = 'Deactive';
                }else{
                    $status = '';
                }
                // $status = ucfirst($dealer_data->status);
                return $status;
            }else{
                return '';
            }
        })

        ->filterColumn('dealers.approval_status', function ($query, $keyword) {
            $globalSearch = request()->input('search.value');
            $searchValue = $globalSearch != '' ? $globalSearch : $keyword;
            $lowerKeyword = strtolower(trim($searchValue));
            if (str_contains($lowerKeyword, 'deactive a')) {
                $searchStatus = 'deactive_approval_pending';
            } 
            elseif (str_contains($lowerKeyword, 'active a')) {
                $searchStatus = 'approval_pending'; 
            }
            elseif ($lowerKeyword === 'active') {
                $searchStatus = 'active';
            } 
            elseif ($lowerKeyword === 'deactive') {
                $searchStatus = 'deactive';
            } 
            if (!empty($searchStatus)) {
                $query->where('dealers.approval_status', '=', $searchStatus);
            } 
            else {
                $dbFormatKeyword = str_replace(' ', '_', $lowerKeyword);
                $query->where('dealers.approval_status', 'like', "$dbFormatKeyword%");
            }
        })
        ->addColumn('agreement_document', function($dealer_data){
            if($dealer_data->id != ''){

                $doc_file = DealerAgreement::select('agreement_document','id')->where('dealer_id',$dealer_data->id)->latest('id')->first();

                if($doc_file != ""){

                    if($doc_file->agreement_document != ""){
                        $documentUrl = asset('storage/' . $doc_file->agreement_document);
    
                        $document = '<a href="' . $documentUrl . '" target="_blank">
                        <i class="iconfa-eye-open action-icon" ></i>
                     </a>';
    
                    }else{
                        $document = "";
                    }
                }else{
                    $document = "";
                }
                return $document;
            }else{
                return '';
            }
        })
        ->editColumn('created_on', function($dealer_data){
            if ($dealer_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $dealer_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('dealers.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dealers.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($dealer_data){
            if ($dealer_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $dealer_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('dealers.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dealers.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($dealer_data){
            $action = "<div>";
            if(hasAccess("dealer","edit")){
            $action .="<a id='edit_a' href='".route('edit-dealer',['id' => base64_encode($dealer_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("dealer","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','dealer_name','village_name', 'aggrement_start_date', 'aggrement_end_date', 'options','agreement_document'])
        ->make(true);
    }

    public function store(Request $request)
    {
       
       // $dealer_agg_upload = "";
        
     
      /*  if(isset($request->aggrement_document_doc) && $request->aggrement_document_doc != ""){
            $file = new File();
            $isFound =  $file->getFileFromTemp($request->aggrement_document_doc,$prefix = "prog");
           
            if($isFound != false){
                $dealer_agg_upload = $isFound;
            }
        }*/

      
        $validated = $request->validate([
            'dealer_name'=>'required|max:255|unique:dealers',            
            'dealer_code'=>'required|max:255|unique:dealers',            
            'address' => 'max:255',
            'village_id' => 'required',
            'account_name'=>'required',
             'bank_name'=>'required', 
              'account_no'=>'required',
                'ifsc_code'=>'required',

        ],
        [
            'dealer_name.required' => 'Please Enter Dealer',
            'dealer_code.required' => 'Please Enter Dealer Code',
            'dealer_name.unique'   => 'The Dealer Name Has Already Been Taken',
            'dealer_code.unique'   => 'The Dealer Code Has Already Been Taken',
            'dealer_name.max'      => 'Maximum 255 characters allowed',                    
            'village_id.required'  => 'Please Select Village',
            'address.max'          => 'Maximum 255 charactes allowed',
        ]);
        DB::beginTransaction();
        try{
            $dealer_data=  Dealer::create([
                'dealer_name'        => $request->dealer_name,                
                'dealer_code'        => $request->dealer_code,                
                'address'            => $request->address,
                'village_id'         => $request->village_id,
                'pincode'            => $request->pincode,
                'mobile_no'          => $request->mobile_no,
                'email'              => $request->email,
                'PAN'                => $request->PAN,                
                'gst_code'           => $request->filled('gstin') ? $request->get('gstin') : '',
                'PAN'                => $request->filled('pan')   ? $request->get('pan')   : '',
                'aadhar_no'          => $request->aadhar_no,                
               // 'aggrement_start_date' => check_convert_date($request->aggrement_start_date),                
              //  'aggrement_end_date' => check_convert_date($request->aggrement_end_date),                
              //  'aggrement_document' =>  $dealer_agg_upload, 
              //  'cheque_no'          =>  $request->cheque_no, 
                'status'             =>'approval_pending',
                'approval_pending'   =>'approval_pending',
                'account_name'       => $request->account_name,
                'bank_name'         =>$request->bank_name,
                'branch_name'       =>$request->branch_name	,
                'account_no'        =>$request->account_no,
                'account_type'      =>$request->account_type,
                'ifsc_code'          =>$request->ifsc_code,
                'micr_code'         =>$request->micr_code,
                'swift_code'        =>$request->swift_code, 
                'company_id'         => Auth::user()->company_id,
                'created_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id,
            ]);
                
            if($dealer_data->save()){

                if(isset($request->contacts) && !empty($request->contacts)){
                    $convertJson = json_decode($request->contacts, true);

                        foreach($convertJson as $ctKey => $ctVal){
                            
                        if($ctVal != null){
                            $contact_data=  DealerContacts::create([
                                'dealer_id' => $dealer_data->id,

                                'contact_person' => isset($ctVal['contact_person']) ? $ctVal['contact_person'] : "",

                                'contact_mobile_no' => isset($ctVal['contact_mobile_no']) ? $ctVal['contact_mobile_no'] : "",

                                'contact_email' => isset($ctVal['contact_email']) ? $ctVal['contact_email'] : "",
                             
                            ]);
                        }
                    }
                }

                // dd($request->agreement_details);
                
                if(isset($request->agreement_details) && !empty($request->agreement_details)){
                    $agreement_details = json_decode($request->agreement_details,true);
                    foreach($agreement_details as $ctKey => $ctVal){
                        if($ctVal != null){

                            $document = "";

                            if(isset($ctVal['agreement_document_doc']) && $ctVal['agreement_document_doc'] != ""){
                                $file = new File();
                                $isFound =  $file->getFileFromTemp($ctVal['agreement_document_doc']);

                                if($isFound != false){
                                    $document = $isFound;
                                }
                            }

                            $agreement_data =  DealerAgreement::create([

                                 'dealer_id' => $dealer_data->id,

                                 'agreement_start_date' =>  $ctVal['agreement_start_date'] != "" ? Date::createFromFormat('d/m/Y', $ctVal['agreement_start_date'])->format('Y-m-d') : null,

                                 'agreement_end_date' =>  $ctVal['agreement_end_date'] != "" ? Date::createFromFormat('d/m/Y', $ctVal['agreement_end_date'])->format('Y-m-d') : null,
                             
                                'agreement_document' => $document,

                                'cheque_no' => $ctVal['cheque_no'] != "" ?  $ctVal['cheque_no'] : null,
                               
                            ]);

                        }
                    }
                }
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Inserted Successfully.',
                ]);
                }else{
                    return response()->json([
                        'response_code' => '0',
                        'response_message' => 'Record Not Inserted',
                    ]);
                }

            }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    public function show(Dealer $customer,$id)
    {
        // $cities = Village::orderBy('village_name', 'ASC')->get();
        
        // $checkCustomer  = Location::where('dealer_id', base64_decode($id))->first();
        $customer_group = CustomerGroup::select('id','customer_group_name')->orderBy('customer_group_name','asc')->get();
        
        // return view('edit.edit-dealer')->with(['id' => $id,'districts' => $cities,'customer_group'=>$customer_group, 'checkUser' => $checkCustomer ]);
        // return view('edit.edit-dealer')->with(['id' => $id,'districts' => $cities,'customer_group'=>$customer_group ]);

        return view('edit.edit-dealer')->with(['id' => $id,'customer_group'=>$customer_group ]);
    }

    public function edit(Dealer $dealer,Request $request, $id)
    {
      
        $dealer_data = Dealer::select(['dealers.id','dealers.dealer_name','dealers.dealer_code','dealers.address', 'dealers.pincode','dealers.mobile_no','dealers.email','dealers.PAN','dealers.gst_code','dealers.aadhar_no','dealers.aggrement_start_date','dealers.aggrement_end_date','dealers.aggrement_document','dealers.aggrement_document','dealers.cheque_no','dealers.status','dealers.village_id','villages.village_name', 'countries.id as c_id', 'states.id as s_id', 'districts.id as d_id', 'talukas.id as t_id','dealers.account_name','dealers.bank_name','dealers.branch_name','dealers.account_no','dealers.account_type','dealers.ifsc_code','dealers.micr_code','dealers.swift_code','dealers.approval_status', ])
        ->leftJoin('villages','villages.id','=','dealers.village_id')        
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')        
        ->leftJoin('districts','districts.id','=','talukas.district_id')        
        ->leftJoin('states','states.id','=','districts.state_id')        
        ->leftJoin('countries','countries.id','=','states.country_id')        
        ->where('dealers.id','=',$id)->first();

       // $dealer_data->file_path = asset('storage').'/';

      //  $dealer_data->aggrement_start_date = parseCarbonDate($dealer_data->aggrement_start_date);

       // $dealer_data->aggrement_end_date = parseCarbonDate($dealer_data->aggrement_end_date);


        $contact_data = DealerContacts::where('dealer_id','=',$id)->get();

        $agreement_details = DealerAgreement::where('dealer_id','=',$id)->get();

        if($agreement_details != null){
            foreach($agreement_details as $dKey => $dVal){
                $dVal->agreement_document_doc = $dVal->agreement_document;

                if($dVal['agreement_start_date'] != null){
                    $dVal['agreement_start_date'] = Date::createFromFormat('Y-m-d', $dVal['agreement_start_date'])->format('d/m/Y');
                }

                if($dVal['agreement_end_date'] != null){
                    $dVal['agreement_end_date'] = Date::createFromFormat('Y-m-d', $dVal['agreement_end_date'])->format('d/m/Y');
                }
            }
        }

        $so_data = SalesOrder::where('dealer_id',$id)->get();
        if($so_data->isNotEmpty()){
             $dealer_data->in_use = true;
        }

        if($dealer_data){
            return response()->json([
                'customer' => $dealer_data,
                'contact'  => $contact_data,
                'agreement_details' => $agreement_details,
                'response_code' => '1',
                'response_message' => '',
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

    public function update(Request $request, Dealer $dealer)
    {
        $validated = $request->validate([
            'dealer_name'=>['required','max:255',Rule::unique('dealers')->ignore($request->id, 'id')],            
            'address'    => 'max:255',
            'village_id' => 'required',
        ],
        [
            'dealer_name.required' => 'Please Enter Dealer Name',
            'dealer_name.unique'   => 'The Dealer Name Has Already Been Taken',
            'dealer_name.max'      => 'Maximum 255 characters allowed',
            'village_id.required'  => 'Please Select Village',
            'address.max'          => 'Maximum 255 charactes allowed',
        ]);

      //  $uploadDocument = "";

        //$imgs = Dealer::where('id','=',$request->id)->first('aggrement_document');

        
        /*if($imgs){
            if($imgs->aggrement_document != "" && $request->aggrement_document_doc != $imgs->aggrement_document ){
                $file = new File();
                $file->delete_file($imgs->aggrement_document);
            }
        }

        if(isset($request->aggrement_document_doc) && $request->aggrement_document_doc != ""){
            $file = new File();
            $isFound =  $file->getFileFromTemp($request->aggrement_document_doc,$prefix = "sign");

            if($isFound != false){
                $uploadDocument = $isFound;
            }else{
                if($file->Is_Files_Exists($request->aggrement_document_doc)){
                    $uploadDocument = $request->aggrement_document_doc;
                }
            }
        }*/

        // dd($request->all());

        if($request->status == $request->dealer_status){
            $approval_status =  $request->dealer_status;
        }else{
            $approval_status = $request->dealer_status == 'active' ?  'approval_pending' :  'deactive_approval_pending' ;
        }
      

        DB::beginTransaction();
        try{
            $dealer_data =  Dealer::where('id','=',$request->id)->update([
                'dealer_name'     => $request->dealer_name,   
                'dealer_code'        => $request->dealer_code,                
                'address'         => $request->address,
                'village_id'      => $request->village_id,
                'pincode'         => $request->pincode,
                'mobile_no'       => $request->mobile_no,
                'email'           => $request->email,
                'PAN'             => $request->PAN,
                'gst_code'        => $request->gstin,
                'PAN'             => $request->pan,
                'aadhar_no'       => $request->aadhar_no,  
                // 'aggrement_start_date' => check_convert_date($request->aggrement_start_date),                
               // 'aggrement_end_date' => check_convert_date($request->aggrement_end_date),                
              //  'aggrement_document' => $uploadDocument,
               // 'cheque_no'          =>  $request->cheque_no, 
                'status'             => $request->status,  
                'approval_status'    => $approval_status,
                'account_name'       => $request->account_name,
                'bank_name'         =>$request->bank_name,
                'branch_name'       =>$request->branch_name	,
                'account_no'        =>$request->account_no,
                'account_type'      =>$request->account_type,
                'ifsc_code'          =>$request->ifsc_code,
                'micr_code'         =>$request->micr_code,
                'swift_code'        =>$request->swift_code, 
                'last_on'         => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id,
            ]);
            

            if($dealer_data){

                $oldContacts = DealerContacts::where('dealer_id','=',$request->id)->get();

                $getDealerId = Dealer::where('id', $request->id)->pluck('id')->first();

                $oldContactsData = [];
                if($oldContacts != null){
                    $oldContactsData = $oldContacts->toArray();
                }


                $contactDetails = $request->only('contacts');
                $contactDetails['contacts'] = json_decode($contactDetails['contacts'],true);
                
                if(isset($oldContactsData) && !empty($oldContactsData)){
                    
                        foreach($oldContactsData as $oldCtKey => $oldCtVal){
        
                            if(isset($contactDetails['contacts'][$oldCtKey]) && $contactDetails['contacts'][$oldCtKey] != null){
                            
                                $contact_data_updated =  DealerContacts::where('dealer_id','=',$request->id)->where('id','=',$oldCtVal['id'])->update([

                                    'contact_person' => isset($contactDetails['contacts'][$oldCtKey]['contact_person']) ? $contactDetails['contacts'][$oldCtKey]['contact_person'] : "",

                                    'contact_mobile_no' => isset($contactDetails['contacts'][$oldCtKey]['contact_mobile_no']) ? $contactDetails['contacts'][$oldCtKey]['contact_mobile_no'] : "",

                                    'contact_email' => isset($contactDetails['contacts'][$oldCtKey]['contact_email']) ? $contactDetails['contacts'][$oldCtKey]['contact_email'] : "",                                
                                
                                ]);
                                unset($oldContactsData[$oldCtKey]); 
                                unset($contactDetails['contacts'][$oldCtKey]);
                            }
                        }
                        if(isset($oldContactsData) && !empty($oldContactsData)){
                            foreach($oldContactsData as $oldCtKey => $oldCtVal){
                                DealerContacts::where('id','=',$oldCtVal['id'])->delete();
                            }
                        }
                }
             
                if(isset($contactDetails['contacts']) && !empty($contactDetails['contacts'])){
                    foreach($contactDetails['contacts'] as $ctKey => $ctVal){     
                       
                        if($ctVal != null){
                            $contact_data=  DealerContacts::create([
                                'dealer_id' =>  $getDealerId,
                                'contact_person' => isset($ctVal['contact_person']) ? $ctVal['contact_person'] : "",
                                'contact_mobile_no' => isset($ctVal['contact_mobile_no']) ? $ctVal['contact_mobile_no'] : "",
                                'contact_email' => isset($ctVal['contact_email']) ? $ctVal['contact_email'] : "",
                            ]);
                        }
                    }
                    }
                // }



                // Dealer Agreement 
                // Agreement Details

                // dd($request->agreement_details);
                
                $agreementDetails = $request->only('agreement_details');

                // dd($agreementDetails);
               
                $agreementDetails['agreement_details'] = json_decode($agreementDetails['agreement_details'],true);

                $oldDocument = DealerAgreement::where('dealer_id','=',$request->id)->get();
                $oldDocumentData = [];
                if($oldDocument != null){
                    $oldDocumentData = $oldDocument->toArray();
                }

                // dd($oldDocumentData);

                if(isset($oldDocumentData) && !empty($oldDocumentData)){


                    // dd("here");

                    foreach($oldDocumentData as $oldCtKey => $oldCtVal){

                        // dd($agreementDetails['agreement_details'][$oldCtKey]);

                        if(isset($agreementDetails['agreement_details'][$oldCtKey]) && $agreementDetails['agreement_details'][$oldCtKey] != null){

                            $document = "";
                            $file = new File();
                            $isFound =  $file->getFileFromTemp($agreementDetails['agreement_details'][$oldCtKey]['agreement_document_doc']);
                            if($isFound != false)
                            {
                                $document = $isFound;
                            }else{

                                if($file->Is_Files_Exists($agreementDetails['agreement_details'][$oldCtKey]['agreement_document_doc']))
                                {
                                    $document = $agreementDetails['agreement_details'][$oldCtKey]['agreement_document_doc'];
                                }
                            }

                            // dd(!empty($agreementDetails['agreement_details'][$oldCtKey]['agreement_start_date']));

                            $document_data_updated =  DealerAgreement::where('dealer_id','=',$request->id)->where('id','=',$oldCtVal['id'])->update([

                                'agreement_start_date' => !empty($agreementDetails['agreement_details'][$oldCtKey]['agreement_start_date']) 
                                ? Date::createFromFormat('d/m/Y', $agreementDetails['agreement_details'][$oldCtKey]['agreement_start_date'])->format('Y-m-d') 
                                : ($oldCtVal['agreement_start_date'] ?? null),
                
                                'agreement_end_date' => !empty($agreementDetails['agreement_details'][$oldCtKey]['agreement_end_date']) 
                                ? Date::createFromFormat('d/m/Y', $agreementDetails['agreement_details'][$oldCtKey]['agreement_end_date'])->format('Y-m-d') 
                                : ($oldCtVal['agreement_end_date'] ?? null),
                
                                'agreement_document' => !empty($document) 
                                ? $document 
                                : ($oldCtVal['agreement_document'] ?? null),
                
                                'cheque_no' => !empty($agreementDetails['agreement_details'][$oldCtKey]['cheque_no']) 
                                ? $agreementDetails['agreement_details'][$oldCtKey]['cheque_no'] 
                                : ($oldCtVal['cheque_no'] ?? null),
                

                            ]);

                            // dd($document_data_updated);

                            unset($oldDocumentData[$oldCtKey]); //remove element from array after use it's key
                            unset($agreementDetails['agreement_details'][$oldCtKey]); //remove element from array after use it's key
                        }

                    }

                    if(isset($oldDocumentData) && !empty($oldDocumentData)){
                        foreach($oldDocumentData as $oldCtKey => $oldCtVal){
                            DealerAgreement::where('id','=',$oldCtVal['id'])->delete();
                        }
                    }

                }

                if(isset($agreementDetails['agreement_details']) && !empty($agreementDetails['agreement_details'])){


                    foreach($agreementDetails['agreement_details'] as $ctKey => $ctVal){

                        if($ctVal != null){

                            $document = "";
                            $file = new File();
                            $isFound =  $file->getFileFromTemp($ctVal['agreement_document_doc']);

                            if($isFound != false)
                            {
                                $document = $isFound;
                            }else{
                                if($file->Is_Files_Exists($ctVal['agreement_document_doc']))
                                {
                                    $document = $ctVal['agreement_document_doc'];
                                }
                            }

                            $document_data=  DealerAgreement::create([


                                'dealer_id' => $request->id,

                                'agreement_start_date' => $ctVal['agreement_start_date'] != "" ?     Date::createFromFormat('d/m/Y', $ctVal['agreement_start_date'])->format('Y-m-d') : null ,


                                'agreement_end_date' => $ctVal['agreement_end_date'] != "" ?     Date::createFromFormat('d/m/Y', $ctVal['agreement_end_date'])->format('Y-m-d') : null ,

                                'agreement_document' => $document,
                                
                                'cheque_no' => $ctVal['cheque_no'] != "" ?  $ctVal['cheque_no'] : null,

                            ]);

                        }
                    }
                }else{
                    if(isset($oldDocumentData) && !empty($oldDocumentData)){
                        foreach($oldDocumentData as $oldCtKey => $oldCtVal){
                            DealerAgreement::where('id','=',$oldCtVal['id'])->delete();
                        }
                    }
                }
                

                // End Dealer Agreement
            
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Updated',
                ]);
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Updated',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    public function existsDealer(Request $request){
        if($request->term != ""){
            $fdCustomer = Dealer::select('dealer_name')->where('dealer_name', 'LIKE', $request->term.'%')->groupBy('dealer_name')->get();
            if($fdCustomer != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdCustomer as $dsKey){
                    $output .= '<li parent-id="customer_name" list-id="dealer_name_list" class="list-group-item" tabindex="0">'.$dsKey->dealer_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'customerList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Dealer available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'menuList' => '',
                'response_code' => 1,
            ]);
        }

    }
    public function existsDealerCode(Request $request){
        if($request->term != ""){
            $fdCustomer = Dealer::select('dealer_code')->where('dealer_code', 'LIKE', $request->term.'%')->groupBy('dealer_code')->get();
            if($fdCustomer != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdCustomer as $dsKey){
                    $output .= '<li parent-id="dealer_code" list-id="dealer_code_list" class="list-group-item" tabindex="0">'.$dsKey->dealer_code.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'codeList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Dealer Code available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'codeList' => '',
                'response_code' => 1,
            ]);
        }

    }


    public function getVillageData(Request $request)
    {
        
        $village = $request->village_id_;
        
        if(!empty($village))
        {
            $getVillage = Village::where('id', $village)->pluck('default_pincode');
        }

        if($getVillage)
        {
            return response()->json([
                'response_code' => '1',
                'pincode' => $getVillage,
            ]);
        }
        else 
        {
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{

            $so_data = SalesOrder::where('dealer_id',$request->id)->get();
            if($so_data->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Dealer Is Used In SO.",
                ]);
            }

          //  $imgs = Dealer::where('id','=',$request->id)->first('aggrement_document');
            $imgs = DealerAgreement::where('dealer_id','=',$request->id)->get();
            // dd($imgs);


            if($imgs->isNotEmpty()){

                foreach ($imgs as $key => $value) {
                    if($value->agreement_document != ""){
                        $file = new File();
                        $file->delete_file($value->agreement_document);
                    }
                }
            }
        
            
                Dealer::destroy($request->id);            
                DealerAgreement::where('dealer_id',$request->id)->delete();            
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Deleted Successfully.',
                ]);
        }catch(\Exception $e){
            DB::rollBack();
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

    public function importviewDealer(){
        return view('import_dealer');
    }
    public function importDealer()
    {
        
        Excel::import(new ImportDealer,request()->file('file'));
        return redirect()->back();
    }

    // public function DealerData()
    // {
    //     $dealer = Dealer::all();
    //     if($dealer){
    //         return response()->json([
    //             'dealer' => $dealer,
    //             'response_code' => '1'
    //         ]);
    //     }else{
    //         return response()->json([
    //             'response_code' => '0',
    //             'response_message' => 'No Data Avilable',
    //         ]);
    //     }

    // }

    public function DealerData(Request $request)
    {

        if($request->pagename == "salesOrder"){
            
            // $dealers = getSoDealer();
            if(isset($request->customer_group_id)){

                $location = LocationCustomerGroupMapping::select('location_id')->where('customer_group_id',$request->customer_group_id)->get();

                    $location_data = Location::select(['districts.state_id'])
                    ->leftJoin('villages','villages.id','=','locations.village_id')
                    ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
                    ->leftJoin('districts','districts.id','=','talukas.district_id')
                    ->leftJoin('states','states.id','=','districts.state_id')
                    ->whereIn('locations.id',$location)
                    ->get();

                    $dealers = Dealer::select('dealers.id', 'dealers.dealer_name', 'districts.state_id')
                    ->leftJoin('villages', 'villages.id', '=', 'dealers.village_id')
                    ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
                    ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
                    ->leftJoin('states', 'states.id', '=', 'districts.state_id')
                    ->whereIn('districts.state_id',$location_data)
                    ->where('dealers.status', '=', 'active')
                    ->orderBy('dealers.dealer_name', 'asc')
                    ->get();

            }else{
                $dealers = [];
            }
        }else{
            
            $dealers = Dealer::where('status', '=', 'active')->get();

        }

        if ($dealers && $dealers->count() > 0) {
            return response()->json([
                'dealer' => $dealers,
                'response_code' => '1'
            ]);
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'No Data Available',
            ]);
        }
    }

    public function getDealerCode(){

        $get_code = Dealer::latest('id')->pluck('dealer_code')->first();

        // Check if a supplier code exists
        if ($get_code) {
            // Extract the numeric part and increment it
            preg_match('/(\d+)/', $get_code, $matches);
            $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;

            // Generate the new supplier code (keeping the prefix "S")
            $new_code = 'D' . str_pad($number, 3, '0', STR_PAD_LEFT);
        } else {
            // If no supplier exists, start from S0001
            $new_code = 'D0001';
        }

        return response()->json([
            'dealer_code' => $new_code,
            'response_code' => 1,
        ]);


    
    }

    public function exportDealer(Request $request)
    {
        $searchData = [];
        $global = $request->input('global');
        $columns = $request->input('columns', []);

        if ($global && is_string($global)) {
            $searchData['global'] = trim($global);
        }
        if (is_array($columns)) {
            foreach ($columns as $idx => $val) {
                if (is_string($val) && strlen($val) <= 255) {
                    $searchData['columns'][$idx] = trim($val);
                }
            }
        }

        return Excel::download(new ExportDealer($searchData), 'Dealer.xlsx');
    }
}