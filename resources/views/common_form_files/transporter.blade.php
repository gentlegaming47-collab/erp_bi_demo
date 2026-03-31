<input type="hidden" name="status" id="status">
<div class="row">
    <div class="span-6">
            <div class="par control-group form-control">
                    <label class="control-label" for="transporter_name">Transporter <sup class="astric">*</sup></label>
                    <div class="controls">
                        <span class="formwrapper">
                            <input type="text" name="transporter_name" id="transporter_name" onkeyup="suggestTransporter(event,this)" onfocusout="verifyTransporter()" class="input-large auto-suggest" placeholder="Enter Transporter" autocomplete="nope" autofocus/>
    
                        <div id="transporter_name_list" class="suggestion_list" ></div>
                        {{-- get the suggesion at duplication time --}}
                        <input type="hidden" name="trans" id="trans">   
                        </span>
                    </div>
            </div>       
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="address">Address </label>
                    <div class="controls">
                        <span class="formwrapper">
                            <textarea id="address" name="address" class="h-auto textAreaCustom"  rows="3" placeholder="Enter Address"></textarea>
                        </span>
                    </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="pan">Transporter PAN</label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="pan" id="pan" class="input-large" placeholder="Enter Transporter PAN" />
                </span>
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="gstin">Transporter GSTIN</label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="gstin" id="gstin" class="input-large" placeholder="Enter Transporter GSTIN" />
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="type_of_vehicle">Type of Vehicle</label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="type_of_vehicle" id="type_of_vehicle" class="input-large" placeholder="Enter Type of Vehicle" />
                </span>
            </div>
        </div>
    </div>   
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="contact_person">Contact Person </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="contact_person" id="contact_person" class="input-large auto-suggest" placeholder="Enter Contact Person" autocomplete="nope" />
                    </span>
                </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="contact_person_mobile">Contact Person Mobile </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="contact_person_mobile" id="contact_person_mobile" class="input-large mobile-f" placeholder="Enter Contact Person Mobile"  />
                    </span>
                </div>
        </div>
    </div>

    
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="contact_person_email_id">Contact Person Email </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="contact_person_email_id" id="contact_person_email_id" class="input-large checkEmail" placeholder="Enter Contact Person Email"   />
                    </span>
                </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="payment_terms">Payment Terms </label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="payment_terms" id="payment_terms" class="input-large" placeholder="Enter Payment Terms" />
                </span>
            </div>
        </div>
    </div>
     <div id="statushide" class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="transporter_status">Status <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    <select data-placeholder="Select Status" name="transporter_status" id="transporter_status" class="chzn-select">
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


<script src="{{ asset('js/view/transporter.js?ver='.getJsVersion()) }}"></script>