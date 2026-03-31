<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Taluka;
use App\Models\Village;
use App\Models\State;
use App\Models\Country;
use App\Models\Supplier;
use App\Models\ItemReturn;
use App\Models\ItemIssue;
use App\Models\GRNMaterial;
use App\Models\PurchaseOrder;
use App\Models\SupplierRejection;
use App\Models\SupplierItemMapping;

use App\Models\Admin;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportCity;
use App\Models\SupplierAgreement;
use App\Models\File;
use App\Models\PurchaseRequisitionDetails;
use App\Exports\ExportSupplier;

class SupplierController extends Controller
{
    public function create()
    {
        return view('add.add-supplier');
    }

    public function manage()
    {
        return view('manage.manage-supplier');
    }

    public function index(Supplier $supplier,Request $request,DataTables $dataTables)
    {
        $supplier_data = Supplier::select([
            'suppliers.id',
            'suppliers.supplier_name',
            'suppliers.supplier_code',
            'suppliers.address',
            'suppliers.pincode',
            'suppliers.contact_person',
            'suppliers.contact_person_mobile',
            'suppliers.contact_person_email_id',
            'suppliers.web_address',
            'suppliers.GSTIN',
            'suppliers.PAN',
            'suppliers.payment_terms',
            'suppliers.status',
            'countries.country_name',
            'states.state_name',
            'districts.district_name',
            'talukas.taluka_name',
            'villages.village_name',
            'suppliers.created_on',
            'suppliers.created_by_user_id',
            'suppliers.last_by_user_id',
            'suppliers.last_on',
            'suppliers.approval_status',
            'created_user.user_name as created_by_name',
            'last_user.user_name as last_by_name'
        ])
        ->leftJoin('villages','villages.id','=','suppliers.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'suppliers.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'suppliers.last_by_user_id');
        // dd($supplier_data);
        

        return DataTables::of($supplier_data)
        ->editColumn('created_by_user_id', function($supplier_data){
            if($supplier_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$supplier_data->created_by_user_id)->first('user_name');
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($supplier_data){
            if($supplier_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$supplier_data->last_by_user_id)->first('user_name');
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }

        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('header_print', function($supplier_data){
            return  strip_tags($supplier_data->header_print);
        })
        ->editColumn('city_name', function($supplier_data){
            if($supplier_data->state != ''){
                $district_name = ucfirst($supplier_data->district_name);
                return $district_name;
            }else{
                return '';
            }
            // return Str::limit($supplier_data->district_name, 50);
        })
        ->editColumn('country_name', function($supplier_data){
            if($supplier_data->country_name != ''){
                $country_name = ucfirst($supplier_data->country_name);
                return $country_name;
            }else{
                return '';
            }
            //return Str::limit($supplier_data->country_name, 50);
        })
        ->editColumn('state_name', function($supplier_data){
            if($supplier_data->state_name != ''){
                $state_name = ucfirst($supplier_data->state_name);
                return $state_name;
            }else{
                return '';
            }
            //return Str::limit($supplier_data->state_name, 50);
        })
        ->addColumn('agreement_document', function($supplier_data){
            if($supplier_data->id != ''){

                $doc_file = SupplierAgreement::select('agreement_document','id')->where('supplier_id',$supplier_data->id)->latest('id')->first();

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
        ->editColumn('approval_status', function($supplier_data){          
            if($supplier_data->approval_status != ''){
                if($supplier_data->approval_status == 'deactive_approval_pending'){
                    $status = 'Deactive Approval Pending';
                }elseif($supplier_data->approval_status == 'approval_pending'){
                    $status = 'Active Approval Pending';
                }elseif($supplier_data->approval_status == 'active'){
                    $status = 'Active';
                }elseif($supplier_data->approval_status == 'deactive'){
                    $status = 'Deactive';
                }else{
                    $status = '';
                }
                // $status = ucfirst($supplier_data->status);
                return $status;
            }else{
                return '';
            }
        })
        ->filterColumn('suppliers.approval_status', function ($query, $keyword) {
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
                    $query->where('suppliers.approval_status', '=', $searchStatus);
                } 
                else {
                    $dbFormatKeyword = str_replace(' ', '_', $lowerKeyword);
                    $query->where('suppliers.approval_status', 'like', "$dbFormatKeyword%");
                }
            })
        // ->filterColumn('suppliers.approval_status', function($query, $keyword) {
        //     $query->where(function($q) use ($keyword) {
        //         $keyword = strtolower(trim($keyword));

        //         if ($keyword === 'deactive approval pending') {
        //             $q->orWhere('approval_status', 'deactive_approval_pending');
        //         } elseif ($keyword === 'active approval pending') {
        //             $q->orWhere('approval_status', 'approval_pending');
        //         } elseif ($keyword === 'active') {
        //             $q->orWhere('approval_status', 'active');
        //         } elseif ($keyword === 'deactive') {
        //             $q->orWhere('approval_status', 'deactive');
        //         } else {
        //             $q->orWhere('suppliers.approval_status', 'like', "%{$keyword}%");
        //         }
        //     });
        // })
        ->editColumn('created_on', function($supplier_data){
            if ($supplier_data->created_on != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $supplier_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1;
            }else{
                return '';
            }
        })
        ->filterColumn('suppliers.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(suppliers.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($supplier_data){
            if ($supplier_data->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $supplier_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2;
            }else{
                return '';
            }
        })
        ->filterColumn('suppliers.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(suppliers.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($supplier_data){
            $action = "<div>";
            if(hasAccess("location","edit")){
            $action .="<a id='edit_a' href='".route('edit-supplier',['id' => base64_encode($supplier_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
            }
            if(hasAccess("location","delete")){
            $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
            }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','options','header_print','agreement_document'])
        ->make(true);
    }

    public function store(Request $request)
    {
       
        // dd($request->all());
        $validated = $request->validate([
            'supplier_name'=>'required|max:255|unique:suppliers',            
            'supplier_code'=>'required|max:255|unique:suppliers',            
            'address' => 'max:255',
            'supplier_village_id' => 'required',
        ],
        [
            'supplier_name.required'     => 'Please Enter Supplier Name',
            'supplier_code.required'     => 'Please Enter Supplier Code',
            'supplier_name.unique'       => 'The Supplier Name Has Already Been Taken',        
            'supplier_code.unique'       => 'The Supplier Code Has Already Been Taken',        
            'supplier_village_id.required'       => 'Please Select Village',
            'address.max'               => 'Maximum 255 charactes allowed',
            
        ]);
            $item_mapping_required = isset($request->no_item_mapping_required) ? 'Yes' : 'No';
        
        DB::beginTransaction();
        try{
            $supplier_data =  Supplier::create([
                'supplier_name'          => $request->supplier_name,
                'supplier_code'          => $request->supplier_code,
                'address'                => $request->address,
                'village_id'             => $request->supplier_village_id,
                'pincode'                => $request->pincode,
                'contact_person'         => $request->contact_person,
                'contact_person_mobile'  => $request->contact_person_mobile,
                'contact_person_email_id'=> $request->contact_person_email_id,
                'web_address'            => $request->web_address,
                'GSTIN'                  => $request->gstin,
                'PAN'                    => $request->pan,
                'payment_terms'          => $request->payment_terms,
                'status'                 =>'approval_pending',
                'approval_status'      =>'approval_pending',
                'no_item_mapping_required' =>$item_mapping_required,
                'company_id'             => Auth::user()->company_id,
                'created_on'             => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id'     => Auth::user()->id
            ]);

            
            // dd($supplier_data);
            if($supplier_data->save()){

                
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

                            $agreement_data =  SupplierAgreement::create([

                                 'supplier_id' => $supplier_data->id,

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
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    public function show(Supplier $supplier,$id)
    {
       
        $supplier = Supplier::where('id',base64_decode($id))->first();

        $addressIds =  getAddressDetails($supplier->village_id);
        
        $countries = Country::select('id as c_id','country_name')->where('id',$addressIds['country_id'])->orderBy('country_name','asc')->get();
        $states    = State::select('id as s_id','state_name')->where('id',$addressIds['state_id'])->orderBy('state_name','asc')->get();
        $district  = City::select('id as d_id','district_name')->where('id',$addressIds['district_id'])->orderBy('district_name','asc')->get();
        $taluka    = Taluka::select('id as t_id','taluka_name')->where('id',$addressIds['taluka_id'])->orderBy('taluka_name','asc')->get();
        $village   = Village::select('id as v_id','village_name')->where('id',$supplier->village_id)->orderBy('village_name','asc')->get();

        return view('edit.edit-supplier')->with([
            'id'        => $id,
            'states'    => $states,
            'countries' => $countries,
            'district'  => $district,
            'taluka'    => $taluka,
            'village'   => $village
        ]);
    }

    public function SupplierData($id)
    {
        // dd($id);
        $supplier_data = Supplier::select(['suppliers.id','suppliers.supplier_name','suppliers.supplier_code','suppliers.address','suppliers.pincode','suppliers.contact_person','suppliers.contact_person_mobile','suppliers.contact_person_email_id','suppliers.web_address','suppliers.GSTIN','suppliers.PAN','suppliers.payment_terms','suppliers.status','suppliers.approval_status','suppliers.no_item_mapping_required','states.country_id','districts.state_id','suppliers.village_id','talukas.district_id','villages.taluka_id','suppliers.created_on','suppliers.created_by_user_id','suppliers.last_by_user_id','suppliers.last_on'])
        ->leftJoin('villages','villages.id','=','suppliers.village_id')
        ->leftJoin('talukas','talukas.id','=','villages.taluka_id')
        ->leftJoin('districts','districts.id','=','talukas.district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->where('suppliers.id','=',$id)
        ->first();
        // dd($supplier_data);

        $agreement_details = SupplierAgreement::where('supplier_id','=',$id)->get();

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

        if($supplier_data){
            return response()->json([
                'supplier' => $supplier_data,
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

    public function update(Request $request, Supplier $supplier)
    {

        // dd($request->all());
        // $validated = $request->validate([
        //     'supplier_name'       =>'required|max:255',
        //     'supplier_village_id' => 'required'
        // ],
        // [
        //     'supplier_name.required'       => 'Please enter Supplier Name',
        //     'supplier_name.max'            => 'Maximum 255 characters allowed',
        //     'supplier_village_id.required' => 'Please Select Village'
        // ]);

        if($request->status == $request->supplier_status){
            $approval_status =  $request->supplier_status;
        }else{
            $approval_status = $request->supplier_status == 'active' ?  'approval_pending' :  'deactive_approval_pending' ;
        }

        $item_mapping_required = isset($request->no_item_mapping_required) ? 'Yes' : 'No';
      

        DB::beginTransaction();

        try{
            $supplier =  Supplier::where('id','=',$request->id)->update([

                'supplier_name'          => $request->supplier_name,
                'supplier_code'          => $request->supplier_code,
                'address'                => $request->address,
                'village_id'             => $request->supplier_village_id,
                'pincode'                => $request->pincode,
                'contact_person'         => $request->contact_person,
                'contact_person_mobile'  => $request->contact_person_mobile,
                'contact_person_email_id'=> $request->contact_person_email_id,
                'web_address'            => $request->web_address,
                'GSTIN'                  => $request->gstin,
                'PAN'                    => $request->pan,
                'payment_terms'          => $request->payment_terms,
                'status'                 =>$request->status,
                'approval_status'        =>$approval_status,
                'no_item_mapping_required' =>$item_mapping_required,
                'last_on'                => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id'        => Auth::user()->id
            ]);

            
            // dd($supplier_data);
            if($supplier){


                // Agreement Details
                
                $agreementDetails = $request->only('agreement_details');
               
                $agreementDetails['agreement_details'] = json_decode($agreementDetails['agreement_details'],true);

                $oldDocument = SupplierAgreement::where('supplier_id','=',$request->id)->get();
                $oldDocumentData = [];
                if($oldDocument != null){
                    $oldDocumentData = $oldDocument->toArray();
                }

                if(isset($oldDocumentData) && !empty($oldDocumentData)){

                    foreach($oldDocumentData as $oldCtKey => $oldCtVal){

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

                            $document_data_updated =  SupplierAgreement::where('supplier_id','=',$request->id)->where('id','=',$oldCtVal['id'])->update([

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

                            unset($oldDocumentData[$oldCtKey]); //remove element from array after use it's key
                            unset($agreementDetails['agreement_details'][$oldCtKey]); //remove element from array after use it's key
                        }

                    }

                    if(isset($oldDocumentData) && !empty($oldDocumentData)){
                        foreach($oldDocumentData as $oldCtKey => $oldCtVal){
                            SupplierAgreement::where('id','=',$oldCtVal['id'])->delete();
                        }
                    }

                }

                if(isset($agreementDetails['agreement_details']) && !empty($agreementDetails['agreement_details'])){


                    foreach($agreementDetails['agreement_details'] as $ctKey => $ctVal){

                        if($ctVal != null){

                            $document = "";
                            $file = new File();
                            $isFound =  $file->getFileFromTemp($ctVal['agreement_document_doc'],$prefix = "doc");

                            if($isFound != false)
                            {
                                $document = $isFound;
                            }else{
                                if($file->Is_Files_Exists($ctVal['agreement_document_doc']))
                                {
                                    $document = $ctVal['agreement_document_doc'];
                                }
                            }

                            $document_data=  SupplierAgreement::create([


                                'supplier_id' => $request->id,

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
                            SupplierAgreement::where('id','=',$oldCtVal['id'])->delete();
                        }
                    }
                }

                //Now Compare Old Uploads And New Uploads and remove upload from folder which are deleted from db

                // $new_uploads = SupplierAgreement::where('supplier_id','=',$request->id)->get();
                // $new_uploads_arr = [];
                // if($new_uploads != null){
                //     foreach($new_uploads as $nKey => $nVal){
                //         $new_uploads_arr[$nKey] = $nVal->document;
                //     }
                // }


                // End Agreement Details
                DB::commit();
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Inserted',
                ]);
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $supplier_src_id = SupplierRejection::where('supplier_id',$request->id)->get();
            if($supplier_src_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Supplier Is Used In Supplier Return Challan.",
                ]);
            }

            $supplier_grn_id = GRNMaterial::where('supplier_id',$request->id)->get();
            if($supplier_grn_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Supplier Is Used In GRN.",
                ]);
            } 

            $supplier_po_id = PurchaseOrder::where('supplier_id',$request->id)->get();
            if($supplier_po_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Supplier Is Used In Purchase Order.",
                ]);
            }

            $supplier_req_id = PurchaseRequisitionDetails::where('supplier_id',$request->id)->get();
            if($supplier_req_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Supplier Is Used In Purchase Requisition.",
                ]);
            }

            $supplier_item_mapping_id = SupplierItemMapping::where('supplier_id',$request->id)->get();
            if($supplier_item_mapping_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Supplier Is Used In Supplier Item Mapping.",
                ]);
            }

            $imgs = SupplierAgreement::where('supplier_id','=',$request->id)->get();
            // dd($imgs);


            if($imgs->isNotEmpty()){

                foreach ($imgs as $key => $value) {
                    if($value->agreement_document != ""){
                        $file = new File();
                        $file->delete_file($value->agreement_document);
                    }
                }
            }

          
            Supplier::destroy($request->id);
            SupplierAgreement::where('supplier_id',$request->id)->delete();            
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

    public function getSupplierDistrict(Request $request){
        // dd($request->all()); 
        $relData = City::select(['states.id','states.state_name'])
        ->leftJoin('states','states.id','=','districts.state_id')
        ->where('districts.id','=',$request->dis_id)
        ->get();
        if($relData != null){
            return response()->json([
                'response_code' => '1',
                'relation_data' =>  $relData
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'relation_data' =>  '',
                'response_message' => 'No Relation Data Available!'
            ]);
        }
    }

    public function getSupplierName(Request $request){
        if($request->term != ""){
            $supplier_name = Supplier::select('supplier_name')->where('supplier_name', 'LIKE', $request->term.'%')->groupBy('supplier_name')->get();
            // dd($supplier_name);
            if($supplier_name != null){
              
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($supplier_name as $dsKey){

                    $output .= '<li parent-id="supplier_name" list-id="supplier_name_list" class="list-group-item" tabindex="0">'.$dsKey->supplier_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'supplier_name' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Customer available',
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

    public function getSupplierCode(){

        $get_code = Supplier::latest('id')->pluck('supplier_code')->first();

        // Check if a supplier code exists
        if ($get_code) {
            // Extract the numeric part and increment it
            preg_match('/(\d+)/', $get_code, $matches);
            $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;

            // Generate the new supplier code (keeping the prefix "S")
            $new_code = 'S' . str_pad($number, 3, '0', STR_PAD_LEFT);
        } else {
            // If no supplier exists, start from S0001
            $new_code = 'S0001';
        }

        return response()->json([
            'supplier_code' => $new_code,
            'response_code' => 1,
        ]);
    }

    public function existsSupplierCode(Request $request){
        if($request->term != ""){
            $fdCustomer = Supplier::select('supplier_code')->where('supplier_code', 'LIKE', $request->term.'%')->groupBy('supplier_code')->get();
            if($fdCustomer != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdCustomer as $dsKey){
                    $output .= '<li parent-id="supplier_code" list-id="supplier_code_list" class="list-group-item" tabindex="0">'.$dsKey->supplier_code.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'codeList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Supplier Code available',
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

    public function exportSupplier(Request $request)
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

        return Excel::download(new ExportSupplier($searchData), 'Supplier.xlsx');
    }
}