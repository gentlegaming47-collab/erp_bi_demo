<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\City;
use App\Models\Country;
use App\Models\State;

use App\Models\Customer;

use App\Models\Taluka;
use App\Models\Village;

use App\Models\Unit;
use App\Models\HardnessUnit;
use App\Models\Location;
use App\Models\Transporter;
use App\Models\HsnCode;
use App\Models\ItemGroup;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\CustomerGroup;
use App\Models\Dealer;
// Transcation Modal
use App\Models\SupplierRejection;
use App\Models\GRNMaterial;

use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\ItemIssue;
use App\Models\ItemReturn;
use App\Models\ItemProduction;
use App\Models\ItemAssemblyProduction;
use App\Models\MaterialRequest;
use App\Models\DispatchPlan;
use App\Models\CustomerReplacementEntry;
use App\Models\MisCategory;
use App\Models\PurchaseRequisition;
use App\Models\SOMapping;
use App\Models\QCApproval;
use App\Models\Quotation;
use App\Models\SalesReturn;

class DuplicationVerificationController extends Controller
{


    // country verification controller 
    
    public function verifyCountry(Request $request){
        

        if (!empty($request->country_name)) { 
            $name = $request->country_name;
            if(!empty($name)) {
                
                if(isset($request->id)){
                    $users_count = Country::where('country_name',$name)->where('id','!=',$request->id)->first();
                }else{

                    $users_count = Country::where('country_name',$name)->first();

                }
            }
            
            if($users_count){
                return response()->json([
                    'country_name' => $users_count,                
                    'response_code' => '1',
                    'response_message' => 'The Country Name Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }


    public function verifyState(Request $request){
        
        
        if (!empty($request->state)) { 
            $name = $request->state;
            if(!empty($name)) {
                
                if(isset($request->id)){                            
                    $users_count = State::where('state_name',$request->state)->where('country_id', $request->country)->where('id','!=',$request->id)->first();                
                }else{                         
                    $users_count =  State::where('state_name',$request->state)->where('country_id', $request->country)->first();
                }
            }
            
            if($users_count){
                return response()->json([
                    'state' => $users_count,                
                    'response_code' => '1',
                    'response_message' => 'The State Name Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }


    // state and gst vetification

  
    public function verifyGst(Request $request){
            
        if(isset($request->id)){
            $checkGst= State::where('gst_code',$request->gstcode)->where('country_id', $request->countryId)->where('id','!=',$request->id)->first();
            
            if($checkGst){                
                return response()->json([                    
                    'response_code' => '1',
                    'response_message' => 'The Gst Code Has Already Been Taken.',
                ]);
            }else{
                return response()->json([                    
                    'response_code' => '0',
                    'response_message' => 'error.',
                ]);
            }
        }else{
            
            $checkGst= State::where('gst_code',$request->gstcode)->first();
            if($checkGst){
                return response()->json([                    
                    'response_code' => '1',
                    'response_message' => 'The Gst Code Has Already Been Taken.',
                ]);
            }else{
                return response()->json([                    
                    'response_code' => '0',
                    'response_message' => 'error.',
                ]);
            } 
        }

    }

    
    // city verification 

    public function verifyCityData(Request $request){

            if(!empty($request->city)){
                $name =  $request->city;
                if(!empty($name)){
                    if(isset($request->id)){
                        $users_count = City::where('district_name',$name)->where('state_id',$request->state)->where('id','!=',$request->id)->first();
                    }else{
                        $users_count = City::where('district_name',$name)->where('state_id',$request->state)->first();
                    }
                }


                if($users_count){
                    return response()->json([                    
                        'response_code' => '1',
                        'response_message' => 'District Name Has Already Been Taken.',
                    ]);
                }
                else{
                    return response()->json([
                        'response_code' => '2',
                        'response_message' => 'Success',
                    ]);
                }
            } else{
                return response()->json([                    
                    'response_code' => '0',
                    'response_message' => 'Record Does Not Exists.',
                ]);
            }
    }


    
    // Taluka Data

    public function verifyTalukaData(Request $request){
       
        if(!empty($request->taluka_name)){
            $name =  $request->taluka_name;
            if(!empty($name)){
                if(isset($request->id)){
                    $users_count = Taluka::where('taluka_name',$name)->where('district_id',$request->taluka_district_id)->where('id','!=',$request->id)->first();                    
                }else{
                    $users_count = Taluka::where('taluka_name',$name)->where('district_id',$request->taluka_district_id)->first();
                }
            }
       
            if($users_count){
                return response()->json([                    
                    'response_code' => '1',
                    'response_message' => 'The Taluka Name Has Already Been Taken.',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        
        }
        else{
            return response()->json([                    
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists.',
            ]);
        }
    }


    // Village Data
    public function verifyVillage(Request $request){
       
        if(!empty($request->village_name)){
            $name =  $request->village_name;
            if(!empty($name)){
                
                if(isset($request->id)){
                    
                    $users_count = Village::where('village_name',$name)->where('taluka_id',$request->taluka_id)->where('id','!=',$request->id)->first();                    
            
                    

                }else{
                    
                    $users_count = Village::where('village_name',$name)->where('taluka_id',$request->taluka_id)->first();
                }
            }
            
            if($users_count){
                return response()->json([                    
                    'response_code' => '1',
                    'response_message' => 'The Village Name Has Already Been Taken.',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        
        }
        else{
            return response()->json([                    
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists.',
            ]);
        }
    }


    // Location Name Data 
    
    public function verifyLocation(Request $request){
            
        
        if (!empty($request->location_name)) { 
            $name = $request->location_name;
            
            if(!empty($name)) {         
                
                if(isset($request->id)){
                  
                    $users_count = Location::where('location_name',$name)->where('id','!=',$request->id)->first();                    
                }else{                            
                    $users_count = Location::where('location_name',$name)->first();
                 
                }
            }
            
            if($users_count){
                return response()->json([
                    'location' => $users_count,                
                    'response_code' => '1',
                    'response_message' => 'The Location Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

    // location code
    public function verifyLocationCode(Request $request){
            
        
        if (!empty($request->location_code)) { 
            $code = $request->location_code;
            
            if(!empty($code)) {         
                
                if(isset($request->id)){
                  
                    $users_count = Location::where('location_code',$code)->where('id','!=',$request->id)->first();                    
                }else{                            
                    $users_count = Location::where('location_code',$code)->first();
                 
                }
            }
            
            if($users_count){
                return response()->json([
                    'location_code' => $users_count,                
                    'response_code' => '1',
                    'response_message' => 'The Location Code Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }


    // customer remark 

    
    public function verifyCustomer(Request $request){
        
        if (!empty($request->customer_name)) { 
            $name = $request->customer_name;
            if(!empty($name)) {
                
                if(isset($request->id)){
                    $users_count = Customer::where('customer_name',$name)->where('id','!=',$request->id)->first();
                }else{

                    $users_count = Customer::where('customer_name',$name)->first();

                }
            }
            
            if($users_count){
                return response()->json([
                    'customer' => $users_count,                
                    'response_code' => '1',
                    'response_message' => 'The Customer Name Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

     // Dealer name verification 
    public function verifyDealer(Request $request){
        
        if (!empty($request->customer_name)) { 
            $name = $request->customer_name;
            if(!empty($name)) {
                
                if(isset($request->id)){
                    $users_count = Dealer::where('dealer_name',$name)->where('id','!=',$request->id)->first();
                }else{

                    $users_count = Dealer::where('dealer_name',$name)->first();

                }
            }
            
            if($users_count){
                return response()->json([
                    'customer' => $users_count,                
                    'response_code' => '1',
                    'response_message' => 'The Dealer Name Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

     // Dealer Code verification 
     public function verifyDealerCode(Request $request){
        
        if (!empty($request->dealer_code)) { 
            $name = $request->dealer_code;
            if(!empty($name)) {
                
                if(isset($request->id)){
                    $users_count = Dealer::where('dealer_code',$name)->where('id','!=',$request->id)->first();
                }else{

                    $users_count = Dealer::where('dealer_code',$name)->first();

                }
            }
            
            if($users_count){
                return response()->json([
                    'customer' => $users_count,                
                    'response_code' => '1',
                    'response_message' => 'The Dealer Code Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }


    // supplier name verification 
    public function verifySupplierName(Request $request){
       
        if (!empty($request->supplier_name)) { 
            $name = $request->supplier_name;
            if(!empty($name)){

                if(isset($request->id)){
                    $supplier_data = Supplier::where('supplier_name',$name)->where('id','!=',$request->id)->first();
                }else{

                    $supplier_data = Supplier::where('supplier_name',$name)->first();

                }
            }

            if($supplier_data){
                return response()->json([
                    'state_name' => $supplier_data,                
                    'response_code' => '1',
                    'response_message' => 'The Supplier Name Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

     // Supplier Code verification 
     public function verifySupplierCode(Request $request){
        
        if (!empty($request->supplier_code)) { 
            $name = $request->supplier_code;
            if(!empty($name)) {
                
                if(isset($request->id)){
                    $users_count = Supplier::where('supplier_code',$name)->where('id','!=',$request->id)->first();
                }else{

                    $users_count = Supplier::where('supplier_code',$name)->first();

                }
            }
            
            if($users_count){
                return response()->json([
                    'customer' => $users_count,                
                    'response_code' => '1',
                    'response_message' => 'The Supplier Code Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }


    // Transporter verification

    public function verifyTransporter(Request $request)
    {
          if (!empty($request->transporter_name)) { 
              $name = $request->transporter_name;
              if(!empty($name)) {
                 
                  
                  if(isset($request->id)){
                      $users_count = Transporter::where('transporter_name',$name)->where('id','!=',$request->id)->first();
                      
                  }else{
                  
                      $users_count = Transporter::where('transporter_name',$name)->first();
  
                  }
              }
  
              if($users_count){
                  return response()->json([
                      'transporter_name' => $users_count,                
                      'response_code' => '1',
                      'response_message' => 'The Transporter Name Has Already Been Taken',
                  ]);
              }
              else{
                  return response()->json([
                      'response_code' => '2',
                      'response_message' => 'Success',
                  ]);
              }
          } else {
              return response()->json([
                  'response_code' => '0',
                  'response_message' => 'Record Does Not Exists',
              ]);
          }
    }

    // HSN Code Verification

    public function verifyHsnCode(Request $request){
        // dd($request->all());

        
        if (!empty($request->hsn_code)) { 
            
            $name = $request->hsn_code;
            
            if(!empty($name)){

                if(isset($request->id)){
                    $hsn_code = HsnCode::where('hsn_code',$name)->where('id','!=',$request->id)->first();
                    
                    
                }else{                    
                    $hsn_code = HsnCode::where('hsn_code',$name)->first();
                }
            }

       
            
            if($hsn_code != null && $hsn_code != ""){
              
                return response()->json([
                    'hsn_code' => $hsn_code,                
                    'response_code' => '1',
                    'response_message' => 'HSN Code Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

   // unit verification
    
      public function verifyUnit(Request $request)
      {
  
  
          if (!empty($request->unit_name)) { 
              $name = $request->unit_name;
              if(!empty($name)) {
                  
                  if(isset($request->id)){
                      $users_count = Unit::where('unit_name',$name)->where('id','!=',$request->id)->first();
                  }else{
  
                      $users_count = Unit::where('unit_name',$name)->first();
  
                  }
              }
  
              if($users_count){
                  return response()->json([
                      'unit' => $users_count,                
                      'response_code' => '1',
                      'response_message' => 'The Unit Has Already Been Taken',
                  ]);
              }
              else{
                  return response()->json([
                      'response_code' => '2',
                      'response_message' => 'Success',
                  ]);
              }
          } else {
              return response()->json([
                  'response_code' => '0',
                  'response_message' => 'Record Does Not Exists',
              ]);
          }
      }



      // customer group verification 

      public function verifyCustomerGroup(Request $request)
      {

          if (!empty($request->customer_group_name)) { 
              $customerGroupName = $request->customer_group_name;
              if(!empty($customerGroupName)) {
                  
                  if(isset($request->id)){
                      $users_count = CustomerGroup::where('customer_group_name',$customerGroupName)->where('id','!=',$request->id)->first();
                  }else{
  
                      $users_count = CustomerGroup::where('customer_group_name',$customerGroupName)->first();
  
                  }
              }
  
              if($users_count){
                  return response()->json([
                      'customer_group_name' => $users_count,                
                      'response_code' => '1',
                      'response_message' => 'The Customer Group Name Has Already Been Taken',
                  ]);
              }
              else{
                  return response()->json([
                      'response_code' => '2',
                      'response_message' => 'Success',
                  ]);
              }
          } else {
              return response()->json([
                  'response_code' => '0',
                  'response_message' => 'Record Does Not Exists',
              ]);
          }
      }


      // itme group verification

      public function verifyItemGroup(Request $request)
      {

        
  
          if (!empty($request->item_group_name)) { 
              $name = $request->item_group_name;
              if(!empty($name)) {
                  
                  if(isset($request->id)){
                      $users_count = ItemGroup::where('item_group_name',$name)->where('id','!=',$request->id)->first();
                  }else{
  
                      $users_count = ItemGroup::where('item_group_name',$name)->first();
  
                  }
              }
  
              if($users_count){
                  return response()->json([
                      'item_group_name' => $users_count,                
                      'response_code' => '1',
                      'response_message' => 'The Item Group Name Has Already Been Taken',
                  ]);
              }
              else{
                  return response()->json([
                      'response_code' => '2',
                      'response_message' => 'Success',
                  ]);
              }
          } else {
              return response()->json([
                  'response_code' => '0',
                  'response_message' => 'Record Does Not Exists',
              ]);
          }
      }



    //      // itme group code verification

     
    public function verifyItemGroupCode(Request $request){        
        
        if(!empty($request->item_group_code)){
            $code =  $request->item_group_code;
            if(!empty($code)){
                if(isset($request->id)){ 
                    
                    $users_count = ItemGroup::where('item_group_code',$code)->where('item_group_name',$request->item_group_name)->where('id','!=',$request->id)->first();                   
                }else{                    
                    $users_count = ItemGroup::where('item_group_code',$code)->where('item_group_name',$request->item_group_name)->first();
                }
            }
          
            if($users_count){
                return response()->json([                    
                    'response_code' => '1',
                    'response_message' => 'The Item Group Code Has Already Been Taken.',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        
        }
        else{
            return response()->json([                    
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists.',
            ]);
        }
    }


    // item verification 

    public function verifyItem(Request $request)
      {
          
          if (!empty($request->item_name)) { 
              $name = $request->item_name;
              if(!empty($name)) {
                  
                  if(isset($request->id)){
                      $users_count = Item::where('item_name',$name)->where('id','!=',$request->id)->first();
                  }else{
  
                      $users_count = Item::where('item_name',$name)->first();
  
                  }
              }
  
              if($users_count){
                  return response()->json([
                      'item_name' => $users_count,                
                      'response_code' => '1',
                      'response_message' => 'The Item Name Has Already Been Taken',
                  ]);
              }
              else{
                  return response()->json([
                      'response_code' => '2',
                      'response_message' => 'Success',
                  ]);
              }
          } else {
              return response()->json([
                  'response_code' => '0',
                  'response_message' => 'Record Does Not Exists',
              ]);
          }
      }
    

     public function checkDuplication(Request $request)
     {        
        $year_data = getCurrentYearData();
        $locationName = getCurrentLocation();    

          $modal  =  SupplierRejection::class;
          $sequence = 'src_sequence';
          $prefix = 'REJ';
          $sup_num_format = getLatestSequence($modal,$sequence,$prefix);            
          $locationName = getCurrentLocation();

          $check = SupplierRejection::where('year_id', '=', $year_data->id)->where('current_location_id',$locationName->id)->get();

          if($check != "" && $check != null)
          {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Challan No. Is Already Exists'
            ]);            
          }else{
            return response()->json([
                'response_code' => 1,
                'latest_sup_no'  => $sup_num_format['format'],
                'number'        => $sup_num_format['isFound'],
                'location'      => $locationName
            ]);
          }
    }  

    protected static function checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id , $table_id)
    {        

        $year_data = getCurrentYearData();
        $locationName = getCurrentLocation();    

        $po_num_format = duplicationSequnce($modal, $seq, $sequence, $prefix, $id, $table_id);       
     
        if($po_num_format != null)
        {
            if($po_num_format['format'] == 0)
            {
              return response()->json([
                  'response_code' => '0',
                  'response_message' => $message . ' Is Already Exists'
              ]);    
            }
            else{                
              return response()->json([
                  'response_code' => 1,
                  'latest_po_no'  => $po_num_format['format'],
                  'number'        => intval($po_num_format['isFound']),
                  'location'      => $locationName
              ]);
            }
        }
     
          if($po_num_format != null)
          {
              if($po_num_format['format'] == 0)
              {
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'PO No. Is Already Exists'
                ]);    
              }
              else{                
                return response()->json([
                    'response_code' => 1,
                    'latest_po_no'  => $po_num_format['format'],
                    'number'        => intval($po_num_format['isFound']),
                    'location'      => $locationName
                ]);
              }
          } 
    }

    public function verifyMisCategory(Request $request){
        

        if (!empty($request->cat_name)) { 
            $name = $request->cat_name;
            if(!empty($name)) {
                
                if(isset($request->id)){
                    $users_count = MisCategory::where('mis_category',$name)->where('id','!=',$request->id)->first();
                }else{

                    $users_count = MisCategory::where('mis_category',$name)->first();

                }
            }
            
            if($users_count){
                return response()->json([
                    'mis_cat_name' => $users_count,                
                    'response_code' => '1',
                    'response_message' => 'The MIS Category  Has Already Been Taken',
                ]);
            }
            else{
                return response()->json([
                    'response_code' => '2',
                    'response_message' => 'Success',
                ]);
            }
        } else {
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

    
    public function checkPurchaseSequnceDuplication(Request $request)
     {        
        $seq = "po_sequence";
        $modal  =  PurchaseOrder::class;
        $sequence = $request->po_sequence;
        $prefix = 'PO';
        $message = "PO NO.";
        $id = $request->id;
        $table_id = 'po_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id, $table_id) ;
    }
    

    public function checkSalesSequnceDuplication(Request $request)
     {     
        $seq = "so_sequence";
        $modal  =  SalesOrder::class;
        $sequence = $request->so_sequence;
        $prefix = 'SO';
        $message = "SO NO.";
        $id = $request->id;   
        $table_id = 'id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message , $id, $table_id) ;
    }    

    public function checkSalesReturnSequnceDuplication(Request $request)
     {     
        $seq = "sr_sequence";
        $modal  =  SalesReturn::class;
        $sequence = $request->sr_sequence;
        $prefix = 'SR';
        $message = "SR NO.";
        $id = $request->id;   
        $table_id = 'sr_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message , $id, $table_id) ;
    }     

    public function checkSupplierSequnceDuplication(Request $request)
     {     
        $seq = "src_sequence";
        $modal  =  SupplierRejection::class;
        $sequence = $request->src_sequence;
        $prefix = 'REJ';
        $message = "Challan NO.";
        $id = $request->id;
        $table_id = 'src_id';   
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id, $table_id) ;
    }
    
    public function checkItemIssueSequnceDuplication(Request $request)
     {     
        $seq = "issue_sequence";
        $modal  =  ItemIssue::class;
        $sequence = $request->issue_sequence;
        $prefix = 'ISSUE';
        $message = "Item Issue No.";
        $id = $request->id;
        $table_id = 'item_issue_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id, $table_id) ;
    }     


    public function checkItemReturnSequnceDuplication(Request $request)
     {     
        $seq = "return_sequence";
        $modal  =  ItemReturn::class;
        $sequence = $request->return_sequence;
        $prefix = 'RET';
        $message = "Item Return No.";
        $id = $request->id;
        $table_id = 'item_return_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id, $table_id) ;
    }     


    public function checkGrnSequnceDuplication(Request $request)
     {     
        $seq = "grn_sequence";
        $modal  =  GRNMaterial::class;
        $sequence = $request->grn_sequence;
        $prefix = 'GRN';
        $message = "GRN No.";
        $id = $request->id;
        $table_id = 'grn_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id, $table_id) ;
    }     

    public function checkItemProductionSequnceDuplication(Request $request)
     {     
        $seq = "ip_sequence";
        $modal  =  ItemProduction::class;
        $sequence = $request->ip_sequence;
        $prefix = 'PROD';
        $message = "IP. No.";
        $id = $request->id;
        $table_id = 'ip_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id, $table_id) ;
    }     

    public function checkItemAssmProductionSequnceDuplication(Request $request)
     {     
        $seq = "iap_sequence";
        $modal  =  ItemAssemblyProduction::class;
        $sequence = $request->iap_sequence;
        $prefix = 'ASS';
        $message = "Item Ass. Pro. No.";
        $id = $request->id;
        $table_id = 'iap_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id, $table_id) ;
    }  

    
    public function checkMaterialNumber(Request $request)
    {     
        $seq = "mr_sequence";
        $modal  =  MaterialRequest::class;
        $sequence = $request->mr_sequence;
        $prefix = 'MR';
        $message = "MR. No.";
        $id = $request->id;
        $table_id = 'mr_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id, $table_id) ;
    }     

    public function checkDispatchSequnceDuplication(Request $request)
    {     
        $seq = "dp_sequence";
        $modal  =  DispatchPlan::class;
        $sequence = $request->dp_sequence;
        $prefix = 'DP';
        $message = "DP. No.";
        $id = $request->id;
        $table_id = 'dp_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id , $table_id) ;
    }     

    public function checkCRESequnceDuplication(Request $request)
    {     
        $seq = "cre_sequence";
        $modal  =  CustomerReplacementEntry::class;
        $sequence = $request->cre_sequence;
        $prefix = 'RR';
        $message = "CRE. No.";
        $id = $request->id;
        $table_id = 'cre_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id , $table_id) ;
    }     
    
    public function checkMappingSequnceDuplication(Request $request)
    {     
        $seq = "so_mapping_sequence";
        $modal  =  SOMapping::class;
        $sequence = $request->mapping_sequence;
        $prefix = 'RRM';
        $message = "Sr. No.";
        $id = $request->id;
        $table_id = 'mapping_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id , $table_id) ;
    }     
    public function checkPurchaseRequisitionDuplication(Request $request)
    {     
        $seq = "pr_sequence";
        $modal  =  PurchaseRequisition::class;
        $sequence = $request->pr_sequence;
        $prefix = 'PR';
        $message = "PR. No.";
        $id = $request->id;
        $table_id = 'pr_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id , $table_id) ;
    }     
    
    public function checkQCApprovalDuplication(Request $request)
    {     
        $seq = "qc_sequence";
        $modal  =  QCApproval::class;
        $sequence = $request->pr_sequence;
        $prefix = 'QC';
        $message = "QC No.";
        $id = $request->id;
        $table_id = 'qc_id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id , $table_id) ;
    }  

    public function checkQuotationlDuplication(Request $request)
    {     
        $seq = "quot_sequence";
        $modal  =  Quotation::class;
        $sequence = $request->pr_sequence;
        $prefix = 'QO';
        $message = "Quotation No.";
        $id = $request->id;
        $table_id = 'id';
        return  $this->checkTranscationDuplication($seq, $modal, $sequence, $prefix, $message, $id , $table_id) ;
    }     
}