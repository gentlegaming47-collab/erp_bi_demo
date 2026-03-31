<?php $getLocationType =  Session::get('getLocationType'); ?>
<input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
<input type="hidden" value="salesOrder" name="hidViewPage" id="hidViewPage"/>
<input type="hidden" value='{{$getLocationType}}' name="getLocationType" id="getLocationType"/>
@php
        $id = $id ?? ''; 
        if($id != "" && $id != 'undefined'){
           $so_dealer =  getSoDealer($id);
        }else{
          $so_dealer =  getSoDealer('');
        }
@endphp 

<input type="hidden" name="agreement_end_date" id="agreement_end_date">
<div class="row">                    
    <div class="span-6" id="radio_so">
        <div class="par control-group form-control">
           <label class="control-label"></label>
                {{-- <span class="formwrapper"> --}}
                    {{-- <input type="radio" name="so_from" value="customer" checked onchange="getCustomer(),soType()"/> Customer &nbsp; &nbsp; --}}
                    <input type="radio" class="so_from_id_fix" name="so_from_id_fix" value="1" checked onchange="soType()"/> Subsidy &nbsp;
                   
                    <input type="radio" class="so_from_id_fix" name="so_from_id_fix" value="2"  onchange="soType()"/> Cash & Carry &nbsp;
                   
                    {{-- <input type="radio" class="so_from_id_fix" name="so_from_id_fix" value="3" onchange="soType()" {{$getLocationType == 'godown' ? 'readonly' : '' }}/> Location  --}}
                    <input type="radio" class="so_from_id_fix" name="so_from_id_fix" value="3" onchange="soType()" /> Location 
                    
                {{-- </span> --}}
        </div>
    </div>                    
    {{-- <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label"></label>
                <span class="formwrapper">
                    <div class="span-6"><input type="radio" name="so_type_id_fix" value="1" checked onchange="soTypeFix()"/> General</div>

                  
                  <div class="span-6" id="radio_hide">  <input type="radio" name="so_type_id_fix"  value="2" onchange="soTypeFix()"/> Replacement </div>
                    
                </span>
        </div>
    </div> --}}
        <div class="span-6">
     
            <div class="par control-group form-control">                
                <label class="control-label"></label>
                    {{-- <span class="formwrapper"> --}}
                        <div class="span-6"><input type="radio" name="so_type_id_fix" value="1" checked onchange="soTypeFix()"/> General &nbsp;</div>

                    
                    <div id="radio_hide"><input type="radio" name="so_type_id_fix"  value="2" onchange="soTypeFix()"/>Replacement </div>                        
                    {{-- </span> --}}
            </div>      
    </div>
</div>



<div class="row">

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="so_number">SO No. <sup class="astric">*</sup></label>                                 
                <div class="controls"> <span class="formwrapper">
                    <input name="so_sequence" id="so_sequence" class="input-large only-numbers sequence"  />
                    <input name="so_number" id="so_no" class="input-large sequence-number"/>
                    </span> 
                </div>
        </div>
    </div>


    <div class="span-6">
        <div class="par control-group form-control">
        <label class="control-label" for="so_date">SO Date <sup class="astric">*</sup></label>
        <div class="controls"> <span class="formwrapper">
            <input name="so_date" id="so_date" class="trans-date-picker no-fill" />
            </span> </div>
        </div>
    </div>



    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="so_customer_id">Customer Group <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    <select name="customer_group_id" id="customer_group_id" class="chzn-select" onchange="getSoDealer()">
                        <option value="">Select Customer Group</option>
                        @forelse (getSoCustomerGroup() as $customer_group)
                        <option value="{{ $customer_group->id }}">{{ $customer_group->customer_group_name }}</option>
                        @empty
                        @endforelse
                    </select>
                </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="hide">
        <div class="par control-group form-control">
            <label class="control-label" for="so_location_id">Location <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    <select name="so_location_id" id="so_location_id" class="chzn-select" onchange="fillPendingMaterialData()">
                        <option value="">Select Location</option>
                        {{-- @forelse (getLocation() as $location)
                        <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                        @empty
                    @endforelse  --}}
                    </select>
                </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="btn_hide">
        <div class="par control-group form-control">
            <label class="control-label" for="toggle_btn">&nbsp;</label>
            <div class="controls"> <span class="formwrapper input-large">
                  <button class="btn btn-primary toggleModalBtn" type="button" data-target="#pendingMaterialRequest" data-toggle="modal" disabled>Pending</button>
            </div>
        </div>
    </div>



    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="customer_name">Customer <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    
                    <div id="customer_name_show">
                    <input type="text" name="customer_name" id="customer_name" placeholder="Enter Customer" />
                    </div>

                    <div id="rep_customer_id_show">
                    <select name="rep_customer_id" id="rep_customer_id" class="chzn-select" onchange="getSearchData()">
                        <option value="">Select Customer</option>
                    </select>
                    </div>
                    {{-- <span class="m-span"> --}}
                     {{-- <button class="btn btn-primary" type="button" data-toggle="modal" href="#custSearchModal" id="replace_btn">Search</button> --}}
                     {{-- <button class="btn btn-primary" type="button" data-toggle="modal" href="#custSearchModal" id="replace_btn"><i class="action-icon iconfa-search"></i></button> --}}
{{-- 
                     <i class="action-icon iconfa-search" data-toggle="modal" data-target="#custSearchModal" id="replace_btn"></i> --}}
                    {{-- </span> --}}
                </span>
            </div>
        </div>
    </div>

    </div>

    <div class="row">

     <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="dealer_id">Dealer <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper">
                        <select name="dealer_id" id="dealer_id" class="chzn-select mst-dealer" onchange="getAgreementEndDate()">
                            <option value="">Select Dealer</option>
                            {{-- @forelse ($so_dealer  as $dealer) --}}
                            {{-- @forelse (getSoDealer() as $dealer) --}}
                            {{-- <option value="{{ $dealer->id }}">{{ $dealer->dealer_name }}</option> --}}
                            {{-- @empty
                        @endforelse  --}}
                        </select>
                           @if(hasAccess('dealer','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#dealerModal"></i></span>@endif 
                    </span>
            </div>
        </div>
    </div>






{{-- <div class="row" id="show">    --}}

    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="customer_reg_no">Reg. No. </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="customer_reg_no" id="customer_reg_no" placeholder="Enter Reg. No."/>
                </span>
            </div>
        </div>       
    </div>

   

    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="so_country_id">Country <sup class="astric">*</sup></label>
                <div class="controls">
                <span class="formwrapper"> 
                        <select name="so_country_id" id="so_country_id" class="chzn-select mst-country" onchange="getSoStates(event)">
                            <option value="">Select Country</option>
                                @forelse (getCountries() as $country)
                                    @if($country->country_name == 'India')
                                    <option value="{{ $country->id }}" selected>{{ $country->country_name }}</option>
                                    @else
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                    @endif

                                @empty
                                @endforelse    
                        </select>
                    
                        {{-- @if(hasAccess('country','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#countryModal"></i></span>@endif --}}
                    </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="so_state_id">State <sup class="astric">*</sup></label>
            <div class="controls">
            <span class="formwrapper"> 
                <select data-placeholder="Select State" name="so_state_id" id="so_state_id" class="chzn-select mst-suggest_state" onchange="getSoDistrict(event)" tabindex="0">
                </select>
                @if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif      

            </span>
            </div>
        </div>
    </div>

</div>

<div class="row">
    

{{-- <div class="row" id="show">  --}}

    <div class="span-6" id="hide">
        <div class="par control-group form-control">
            <label class="control-label" for="discount">Discount</label>
            <div class="controls">
            <span class="formwrapper"> 
                <input type="text" name="discount" id="discount" class="form-control isNumberKey so_qty" maxlength="3" placeholder="Enter Discount" onkeyup="discountRate()"/>
                
            </span>
            </div>
        </div>
    </div>
   

    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="location_district_id">District <sup class="astric">*</sup></label>
            <div class="controls">
            <span class="formwrapper"> 
                <select data-placeholder="Select District" name="so_district_id" id="so_district_id" class="chzn-select mst-suggest_city" onchange="getSoTaluka(event)" tabindex="0">
                </select>
                @if(hasAccess('district','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#cityModal"></i></span>@endif
                
            </span>
            </div>
        </div>
    </div>
    
    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="so_taluka_id">Taluka <sup class="astric">*</sup></label>
            <div class="controls">
            <span class="formwrapper"> 
                <select data-placeholder="Select Taluka" name="so_taluka_id" id="so_taluka_id"   class="chzn-select mst-suggest_taluka" onchange="getSoVillage(event)" tabindex="0">
                </select>
                @if(hasAccess('taluka','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#talukaModal"></i></span>@endif
            </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="customer_village">Village <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    {{-- <input type="text" name="customer_village" id="customer_village" placeholder="Enter Village" /> --}}
                    <select data-placeholder="Select Village" name="customer_village" id="customer_village" class="chzn-select mst-suggest_village" tabindex="0" onchange="changePincode()">    
                    </select>
                    @if(hasAccess('village','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#VillageModal"></i></span>@endif
                </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="pincode">Pin Code </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="customer_pincode" id="customer_pincode" class="only-numbers" placeholder="Enter Pin Code" />
                </span>
            </div>
        </div>
    </div>
</div>


<div class="row">

     <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="mis_category_id">MIS Category <sup class="astric">*</sup></label>
            <div class="controls">
            <span class="formwrapper"> 
                <select data-placeholder="Select MIS Category" name="mis_category_id" id="mis_category_id" class="chzn-select mst_mis_cat" tabindex="0">
                            <option value=""> Select MIS Category </option>
                            @forelse (getMisCategory()  as $mis_cat)
                                <option value="{{ $mis_cat->id }}">{{ $mis_cat->mis_category }}</option>
                                @empty
                            @endforelse 
                </select>@if(hasAccess('mis_category','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#MisCatModal"></i></span>@endif        
            </span>
            </div>
        </div>
    </div>

     <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="so_mobile_no">Mobile </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="so_mobile_no" id="so_mobile_no" class="mobile-f" placeholder="Enter Mobile" />
                </span>
            </div>
        </div>
    </div>
     <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="area">Area </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="area" id="area" class="input-large" placeholder="Enter Area" />
                </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="ship_to">Ship To </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="ship_to" id="ship_to" class="input-large" placeholder="Enter Ship To"/>
                </span>
            </div>
        </div>
    </div>
</div>


<div class="row">

    <div class="span-6" id="replacement_show">
       <div class="par control-group form-control">
           <label class="control-label" for="pre_so_no">Cus. SO No.</label>
           <div class="controls">
                   <span class="formwrapper">
                       <select name="pre_so_no" id="pre_so_no" class="chzn-select" onchange="getPreSoDetail()">
                           <option value="">Select SO No.</option>            
                       </select>
                         
                   </span>
           </div>
       </div>
   </div>


    <div class="span-6" id="replacement_btn_show">
        <div class="par control-group form-control">
            <label class="control-label" for="toggle_btn">&nbsp;</label>
            <div class="controls"> <span class="formwrapper input-large">
                <button class="btn btn-primary toggleModalBtn presodetaibutton" type="button" data-target="#previousSODetailModel" data-toggle="modal"  id="so_item_btn" disabled>Pending</button>
                </span>
            </div>
        </div>
    </div>
</div>


<div class="divider15"></div>
<!--Second Section-->
<div class="row-fluid">
<div class="widgetbox">
    <h4 class="widgettitle" id="salesOrderTitle">Sales Order Detail</h4>
    <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">                        

    <table class="table table-bordered responsive table-autowidth" id="soPartTable">
        <thead>
        <tr>                                
            <th>Action</th>
            <th>Sr. No.</th>
            <th>Item</th>
            <th>Code</th>
            {{-- <th>Group</th> --}}
            <th>SO Qty.</th>
            <th>Unit</th>
            <th>Rate/Unit</th>
            <th>Discount</th>
            <th>Amount</th>                                
            <th>Remark</th>                                
        </tr>
        </thead>
        <tbody>
        
        </tbody>
        <tfoot>
            <tr class="total_tr"><td colspan="4" ></td>
                <td class="soqtysum"></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="amountsum"></td>
                <td></td>
                </tr>
        </tfoot>
    </table><br>
    <button class="btn btn-primary" type="button" id="addPart" onclick="addPartDetail()">Add</button>
    </div>
</div>
</div>



<!-- <div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="special_notes">Sp. Note </label>
            <div class="controls"> <span class="formwrapper">
            <input type="text" name="special_notes" id="special_notes" class="input-large" placeholder="Enter Sp. Note"/>
            {{-- <textarea id="special_notes" name="special_notes"  class="input-large h-auto" placeholder="Enter Sp.Note" rows="3"></textarea> --}}
                </span>
            </div>
        </div>
    </div>
</div>  -->

<!-- GST Calculation -->

<div class="row">
       
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="basic_amount">Basic Amount</label>
                <div class="controls"> <span class="formwrapper">
                   <input type="number" name="basic_amount" id="basic_amount" class="input-large"  readonly/>
                    </span>
                </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="less_discount">Less Discount</label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="less_discount_percentage" id="less_discount_percentage" step="0.01" min="0" max="100" class="input-small isNumberKey" onblur="formatPoints(this,2)" onkeyup="calcLessDiscount()"/>&nbsp;%&nbsp;
                        <input type="text" name="less_discount_amount" id="less_discount_amount" step="0.01" min="0" class="input-small isNumberKey" onblur="formatPoints(this,2)" onkeyup="calcNetAmount()"/>
                    </span>
                </div>
            </div>
          </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="sec_tra">Secondary Transport</label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="secondary_transport" id="secondary_transport" class="input-large" placeholder="Enter Secondary Transport" onkeyup="calcGstAmount()"  />
                    </span>
                </div>
            </div>
        </div>
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="sharing_head_unit_cost">Sharing Head Unit Cost</label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="sharing_head_unit_cost" id="sharing_head_unit_cost" class="input-large round-off"  placeholder="Enter Sharing Head Unit Cost" onkeyup="calcGstAmount()"/>
                    </span>
                </div>
            </div>
        </div>
        
    </div>

    <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="installation_charge">Installation Charge</label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="installation_charge" id="installation_charge" class="input-large isNumberKey" onkeyup="calcGstAmount()"  onblur="formatPoints(this,2)" placeholder="Enter Installation Charge"  />
                    </span>
                </div>
            </div>
        </div>

      <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="sec_tra"></label>

                <div class="controls">
                    <span class="formwrapper">
                      @forelse(gst_type("desc") as $gstVal)
                        <input type="radio" name="gst_type_fix_id" value="{{ $gstVal->id }}" tabindex="0" {{ $gstVal->id == '3' ? "checked='checked'" : 'false' }} tabindex="1" onclick="manageGstType()"/> {{ $gstVal->name }}&nbsp; &nbsp;
                      @empty
                      @endforelse
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="sgst_percentage">SGST</label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="sgst_percentage" id="sgst_percentage" step="0.01" min="0" max="100" class="input-small sgst-field gst-fields isNumberKey" onblur="formatPoints(this,2)"/>&nbsp;%&nbsp;
                    <input type="number" name="sgst_amount" id="sgst_amount" step="0.01" min="0" class="input-small sgst-field disb" onblur="formatPoints(this,2)" readonly/>
                </span>
            </div>
        </div>
      </div>
        <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="cgst_percentage">CGST</label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="cgst_percentage" id="cgst_percentage" step="0.01" min="0" max="100" class="input-small cgst-field gst-fields isNumberKey" onblur="formatPoints(this,2)"/>&nbsp;%&nbsp;
                    <input type="number" name="cgst_amount" id="cgst_amount" step="0.01" min="0" class="input-small cgst-field disb" onblur="formatPoints(this,2)" readonly/>
                </span>
            </div>
        </div>
      </div>
      <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="igst_percentage">IGST</label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="igst_percentage" id="igst_percentage" step="0.01" min="0" max="100" class="input-small igst-field gst-fields isNumberKey" onblur="formatPoints(this,2)"/>&nbsp;%&nbsp;
                    <input type="number" name="igst_amount" id="igst_amount" step="0.01" min="0" class="input-small igst-field disb" onblur="formatPoints(this,2)" readonly/>
                </span>
            </div>
        </div>
      </div>
        <div class="span-6">
             <div class="par control-group form-control">
                <label class="control-label" for="round_off">Round Off</label>
                <div class="controls"> <span class="formwrapper">
                 <input name="round_off" id="round_off"  class="input-large round-off" onchange="calcNetAmount()" onkeyup="calcNetAmount()" />
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="net_amount">Net Amount</label>
                <div class="controls"> <span class="formwrapper">
                 <input name="net_amount" id="net_amount" step="0.01" min="0" class="input-large" onblur="formatPoints(this,2)" readonly/>
                </div>
            </div>
        </div>
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="special_notes">Sp. Note </label>
                <div class="controls"> <span class="formwrapper">
                <input type="text" name="special_notes" id="special_notes" class="input-large" placeholder="Enter Sp. Note"/>
                {{-- <textarea id="special_notes" name="special_notes"  class="input-large h-auto" placeholder="Enter Sp.Note" rows="3"></textarea> --}}
                    </span>
                </div>
            </div>
        </div>
   
        <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="length">File Upload</label>
                        <div class="controls">
                            <span class="formwrapper">
                                <div class="fileupload fileupload-new" data-provides="fileupload">

                                        <div class="input-append">

                                            <div class="uneditable-input input-medium1">

                                                <i class="iconfa-file fileupload-exists"></i>

                                                <span class="fileupload-preview"></span>

                                            </div>
                                            

                                            <span class="btn btn-file"><span class="fileupload-new">Upload</span>

                                            <span class="fileupload-exists">Change</span>

                                            <input type="file" name="file_upload" id="file_upload" onchange="fileUpload(event)"/></span>

                                            <input type="hidden" id="file_upload_doc" name="file_upload_doc"/>

                                            <a href="#" data-remove="file_upload" class="btn fileupload-exists remove-file" data-dismiss="fileupload" onclick="removeFile(event)">Remove</a>

                                            <a target="_blank" class="btn img-prev hide" id="file_upload_prev">View</a>

                                        </div>

                                    </div>
                            </span>
                        </div>
            </div>
        </div>
     </div>

<!-- End GST Calculation -->