<input type="hidden" name="status" id="status">
<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="supplier_name">Supplier <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="supplier_name" id="supplier_name" class="input-large auto-suggest" onkeyup="suggestSupplierName(event,this)"  onfocusout="verifySupplierName()" placeholder="Enter Supplier" autocomplete="nope" autofocus/>
                        <div id="supplier_name_list" class="suggestion_list" ></div>
                        <input type="hidden" name="spname" id="spname">
                    </span>
                </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="supplier_code">Supplier Code <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="supplier_code" id="supplier_code" class="input-large auto-suggest" onkeyup="suggestSupplierCode(event,this)"  onfocusout="verifySupplierCode()" placeholder="Enter Supplier Code" autocomplete="nope" autofocus/>
                        <div id="supplier_code_list" class="suggestion_list" ></div>
                        <input type="hidden" name="spcode" id="spcode">
                    </span>
                </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="address">Address </label>
                    <div class="controls">
                        <span class="formwrapper">
                            <textarea id="address" name="address" class="h-auto" rows="3" placeholder="Enter Address"></textarea>
                        </span>
                    </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="country">Country <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    <select name="supplier_country_id" id="supplier_country_id" class="chzn-select mst-country" onchange="getSupplierState(event)">
                        <option value="">Select Country</option>
                             @forelse (getCountries() as $country)
                                <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                             @empty
                             @endforelse    
                    </select>
                @if(hasAccess('country','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#countryModal"></i></span>@endif
                </span>
            </div>
        </div>

    </div>

       
</div>

<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="state">State <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">               
                    <select data-placeholder="Select State" name="supplier_state_id" id="supplier_state_id" class="chzn-select mst-suggest_state" onchange="getSupplierDistrict(event)" tabindex="0">
                    </select>
                    @if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif
                </span>
            </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="supplier_district_id">District <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    <select data-placeholder="Select District" name="supplier_district_id" id="supplier_district_id" class="chzn-select mst-suggest_city" onchange="getSupplierTaluka(event)" tabindex="0">
                    </select>
                    @if(hasAccess('district','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#cityModal"></i></span>@endif
                </span>
            </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="supplier_taluka_id">Taluka <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    <select data-placeholder="Select Taluka" name="supplier_taluka_id" id="supplier_taluka_id" onchange="getSupplierVillage(event)"  class="chzn-select mst-suggest_taluka" tabindex="0">
                    </select>
                    @if(hasAccess('taluka','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#talukaModal"></i></span>@endif
                </span>
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="supplier_village_id">Village <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                <select data-placeholder="Select Village" name="supplier_village_id" id="supplier_village_id" class="chzn-select  mst-suggest_village"  tabindex="0">
                    </select>
                    @if(hasAccess('village','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#VillageModal"></i></span>@endif
                </span>
            </div>
        </div>
    </div>
   
</div>

<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="pincode">Pin Code </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="pincode" id="pincode" class="input-large auto-suggest only-numbers" placeholder="Enter Pin Code" autocomplete="nope" autofocus/>
                    </span>
                </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="contact_person">Contact Person </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="contact_person" id="contact_person" class="input-large auto-suggest" placeholder="Enter Contact Person" autocomplete="nope" autofocus/>
                    </span>
                </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="contact_person_mobile">Contact Person Mobile </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="contact_person_mobile" id="contact_person_mobile" class="input-large mobile-f" placeholder="Enter Contact Person Mobile" autofocus />
                    </span>
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="contact_person_email_id">Contact Person Email </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="contact_person_email_id" id="contact_person_email_id" class="input-large checkEmail" placeholder="Enter Contact Person Email" autofocus  />
                    </span>
                </div>
        </div>
    </div>

    
</div>

<div class="row">
    <div class="span-6"> 
        <div class="par control-group form-control">
                <label class="control-label" for="web_address">Web Address </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="web_address" id="web_address" class="input-large"  placeholder="Enter Web Address" autofocus/>
                </span>
            </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="pan">PAN </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="pan" id="pan" class="input-large" placeholder="Enter PAN" autofocus/>
                </span>
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="gstin">GSTIN </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="gstin" id="gstin" class="input-large" placeholder="Enter GSTIN" autofocus/>
                </span>
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="payment_terms">Payment Terms </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="payment_terms" id="payment_terms" class="input-large" placeholder="Enter Payment Terms" autofocus/>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="row">
<div id="statushide" class="span-6">
    <div class="par control-group form-control">
        <label class="control-label" for="supplier_status">Status <sup class="astric">*</sup></label>
        <div class="controls">
            <span class="formwrapper">
                <select data-placeholder="Select Status" name="supplier_status" id="supplier_status" class="chzn-select">
                 <option value="approval_pending">Active Approval Pending</option>
                 <option value="deactive_approval_pending">Deactive Approval Pending</option>
                 <option value="active">Active</option> 
                 <option value="deactive">Deactive</option>
                </select>
            </span>
        </div>
    </div>
</div>

<div class="span-6">
    <div class="par control-group form-control">
        <label class="control-label" for="no_item_mapping_required"></label>
        <div class="controls">
            <span class="formwrapper">
             <input type="checkbox" name="no_item_mapping_required" id="no_item_mapping_required" value="Yes"/>  No Item Mapping Required
            </span>
        </div>
    </div>
</div>
</div>

<div class="divider15"></div>

                    <div class="row-fluid">
                        <div class="widgetbox">
                            <h4 class="widgettitle">Supplier Agreement Details</h4>
                                <div class="widgetcontent">                        
                                    <div>
                                        <button class="btn btn-primary add-part" type="button" data-toggle="modal" href="#agreementModal">Add</button>
                                    </div>
                                        <table class="table table-bordered responsive" id="agreementTable">
                                            <thead>
                                                <tr>
                                                    <th>Action</th>
                                                    <th>Agreement Start Date</th>
                                                    <th>Agreement End Date</th>
                                                    <th>Agreement Document</th>
                                                    <th>Cheque No.</th>
                                                </tr>
                                            </thead>                            
                                            <tbody>
                                                <tr class="centeralign" id="noContact">
                                                    <td colspan="5">No Agreement Details Added</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                </div>
                        </div>
                    </div>
<script src="{{ asset('js/view/supplier.js?ver='.getJsVersion()) }}"></script>
