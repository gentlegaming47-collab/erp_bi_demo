<input type="hidden" name="status" id="status">
                      
                    <div class="row">
                        <div class="span-6">
                                <div class="par control-group form-control">
                                    <label class="control-label" for="dealer">Dealer Name <sup class="astric">*</sup></label>
                                    <div class="controls">
                                        <span class="formwrapper">
                                            <input type="text" name="dealer_name" id="dealer_name" onkeyup="suggestDealer(event,this)" onfocusout="verifyDealer()"   class="input-large auto-suggest" autocomplete="nope" autofocus placeholder="Enter Dealer Name" />

                                            <div id="dealer_list" class="suggestion_list" ></div>
                                            <input type="hidden" name="dname" id="del_name">
                                        </span>
                                    </div>
                                </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                                    <label class="control-label" for="dealer_code">Dealer Code <sup class="astric">*</sup></label>
                                    <div class="controls">
                                        <span class="formwrapper">
                                            <input type="text" name="dealer_code" id="dealer_code" class="input-large auto-suggest" onkeyup="suggestDealerCode(event,this)"  onfocusout="verifyDealerCode()" placeholder="Enter Dealer Code" autocomplete="nope" />
                                            <div id="dealer_code_list" class="suggestion_list" ></div>
                                            <input type="hidden" name="del_code" id="del_code">
                                        </span>
                                    </div>
                            </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                            <label class="control-label" for="address">Address </label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <textarea id="address" name="address"  class="h-auto" placeholder="Enter Address" rows="3"></textarea>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <div class="span-6">
                                    <div class="par control-group form-control">
                                            <label class="control-label" for="country">Country <sup class="astric">*</sup></label>
                                        <div class="controls">
                                            <span class="formwrapper">
                                                <select name="customer_country_id" id="customer_country_id" class="chzn-select mst-country" onchange="getCustomerStates(event)">
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
                                            <select data-placeholder="Select State" name="customer_state_id" id="customer_state_id" class="chzn-select mst-suggest_state" onchange="getCustomerDistrict(event), getCustomerTaluka(event)" tabindex="0">
                                                
                                            </select>
                                            @if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif
                                        </span>
                                    </div>
                            </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                                <label class="control-label" for="city_id">District <sup class="astric">*</sup></label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <select data-placeholder="Select District" name="customer_district_id" id="customer_district_id" class="chzn-select mst-suggest_city" onchange="getCustomerTaluka(event)" >
                                        </select>
                                        @if(hasAccess('district','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#cityModal"></i></span>@endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                                <label class="control-label" for="customer_taluka_id">Taluka <sup class="astric">*</sup></label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <select data-placeholder="Select Taluka" name="customer_taluka_id" id="customer_taluka_id" onchange="getCustomerVillage(event)" class="chzn-select mst-suggest_taluka">
                                        </select>
                                        @if(hasAccess('taluka','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#talukaModal"></i></span>@endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                                <label class="control-label" for="village_id">Village <sup class="astric">*</sup></label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <select data-placeholder="Select Village" name="village_id" id="customer_village_id" class="chzn-select mst-suggest_village">
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
                                        <input type="text" name="pincode" id="pincode" class="input-large" placeholder="Enter Pin Code" />
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                                    <label class="control-label" for="phone_no">Mobile No. </label>
                                        <div class="controls">
                                            <span class="formwrapper">
                                                <input type="text" name="mobile_no" id="mobile_no" class="input-large mobile-f" placeholder="Enter Mobile No." />
                                            </span>
                                        </div>
                            </div>
                        </div>
                    
                        <div class="span-6">
                            <div class="par control-group form-control">
                            <label class="control-label" for="email">Email ID </label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <input type="text" name="email" id="email" class="input-large checkEmail" placeholder="Enter Email ID" />
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                                    <label class="control-label" for="pan">PAN </label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <input type="text" name="pan" id="pan"  class="input-large" placeholder="Enter PAN"/>
                                    </span>
                                </div>
                            </div>
                        </div>


                        
                    </div>

                    <div class="row">

                        <div class="span-6">
                            <div class="par control-group form-control">
                            <label class="control-label" for="gstin">GSTIN </label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <input type="text" name="gstin" id="gstin" class="input-large" placeholder="Enter GSTIN"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                                    <label class="control-label" for="aadhar_no">Aadhar No. </label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <input type="text" name="aadhar_no"  id="aadhar_no" class="input-large checkNumberFormat" placeholder="Enter Aadhar No."/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div id="statushide" class="span-6">
                            <div  class="par control-group form-control">
                                <label class="control-label" for="dealer_status">Status <sup class="astric">*</sup></label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <select data-placeholder="Select Status" name="dealer_status" id="dealer_status" class="chzn-select">
                                         <option value="approval_pending">Active Approval Pending</option>
                                         <option value="deactive_approval_pending">Deactive Approval Pending</option>
                                         <option value="active">Active</option> 
                                         <option value="deactive">Deactive</option>
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span-6">
                            <div class="par control-group form-control">
                                <label class="control-label" for="dealer">Account Name <sup class="astric">*</sup></label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <input type="text" name="account_name" id="account_name" class="input-large auto-suggest" autocomplete="nope" placeholder="Enter Account Name" />

                                   
                                    </span>
                                </div>
                            </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for="dealer">Bank Name <sup class="astric">*</sup></label>
                            <div class="controls">
                                <span class="formwrapper">
                                    <input type="text" name="bank_name" id="bank_name"  class="input-large auto-suggest" autocomplete="nope"  placeholder="Enter Bank Name" />
                                </span>
                            </div>
                        </div>
                </div>
                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="dealer">Branch Name</label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="branch_name" id="branch_name"  class="input-large auto-suggest" autocomplete="nope"  placeholder="Enter Branch Name" />                      
                            </span>
                        </div>
                    </div>
            </div>
            <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label" for="dealer">Account No. <sup class="astric">*</sup></label>
                    <div class="controls">
                        <span class="formwrapper">
                            <input type="text" name="account_no" id="account_no" class="input-large auto-suggest" autocomplete="nope"  placeholder="Enter Account No." />                           
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="dealer">Account Type </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="account_type" id="account_type" class="input-large auto-suggest" autocomplete="nope"  placeholder="Enter Account Type" />
                    </span>
                </div>
            </div>
    </div>
                        <div class="span-6">
                            <div class="par control-group form-control">
                                <label class="control-label" for="dealer">IFSC Code <sup class="astric">*</sup></label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <input type="text" name="ifsc_code" id="ifsc_code" class="input-large auto-suggest" autocomplete="nope"  placeholder="Enter IFSC Code" />                                    </span>
                                </div>
                            </div>
                    </div>

                    <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for="dealer">MICR Code </label>
                            <div class="controls">
                                <span class="formwrapper">
                                    <input type="text" name="micr_code" id="micr_code" class="input-large auto-suggest" autocomplete="nope"  placeholder="Enter MICR Code" />
                                </span>
                            </div>
                        </div>
                </div>

                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="dealer">Swift Code </label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="swift_code" id="swift_code" class="input-large auto-suggest" autocomplete="nope"  placeholder="Enter Swift Code" />
                            </span>
                        </div>
                    </div>
            </div>
        </div>

                    <div class="divider15"></div>

                    <div class="row-fluid">
                        <div class="widgetbox">
                            <h4 class="widgettitle">Dealer Contact Details</h4>
                                <div class="widgetcontent">                        
                                    <div>
                                        <button class="btn btn-primary add-part" type="button" data-toggle="modal" href="#contactModal">Add</button>
                                    </div>
                                        <table class="table table-bordered responsive" id="contactTable">
                                            <thead>
                                                <tr>
                                                    <th> Action</th>
                                                    <th >Name</th>
                                                    <th >Mobile</th>
                                                    <th >Email</th>
                                                </tr>
                                            </thead>                            
                                            <tbody>
                                                <tr class="centeralign" id="noContact">
                                                    <td colspan="6">No Dealer Contact Details Added</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                </div>
                        </div>
                    </div>


                    <div class="divider15"></div>

                    <div class="row-fluid">
                        <div class="widgetbox">
                            <h4 class="widgettitle">Dealer Agreement Details</h4>
                                <div class="widgetcontent">                        
                                    <div>
                                        <button class="btn btn-primary add-part" type="button" data-toggle="modal" href="#dealeragreementModal">Add</button>
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


                   
                    

              
<script src="{{ asset('js/view/customer.js?ver='.getJsVersion()) }}"></script>