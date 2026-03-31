<!--Modals -->

<!--Start material modal-->
<div aria-hidden="false" aria-labelledby="materialLabel" role="dialog" class="modal over hide fade in" id="materialModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="materialModalLabel">Add Material</h3>
    </div>
    <div class="modal-body">
        <form id="addMaterialFormModal" class="stdform" method="post">
            @csrf
            <div class="row">
                <div class="par control-group">
                    <label class="control-label" for="firstname">Material </label>
                    <div class="controls">
                        <input type="text" name="name" id="name" class="input-large"  tabindex="1"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="addMaterialModal" type="submit" form="addMaterialFormModal" tabindex="1">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="1">Close</button>
    </div>
</div>
<!--End material modal-->

<!--Start inco term modal-->
<div aria-hidden="false" aria-labelledby="incoTermLabel" role="dialog" class="modal over hide fade in" id="incoTermModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="incoTermModalLabel">Add Inco Term</h3>
    </div>
    <div class="modal-body">
        <form id="addIncoTermFormModal" class="stdform" method="post">
            @csrf
            <div class="row">
                <div class="par control-group">
                    <label class="control-label" for="firstname">Inco Terms </label>
                    <div class="controls">
                        <input type="text" name="name" id="name" class="input-large"  tabindex="1"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="addIncoTermModal" type="submit" form="addIncoTermFormModal" tabindex="1">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="1">Close</button>
    </div>
</div>
<!--End inco term modal-->

<!--Start currency modal-->
<div aria-hidden="false" aria-labelledby="currencyLabel" role="dialog" class="modal over hide fade in" id="currencyModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="currencyModalLabel">Add Currency</h3>
    </div>
    <div class="modal-body">
        <form id="addCurrencyFormModal" class="stdform" method="post">
            @csrf
            <div class="row">
                <div class="par control-group">
                    <label class="control-label" for="firstname">Currency Code</label>
                    <div class="controls">
                        <input type="text" name="currency_code" id="currency_code" class="input-large"  tabindex="1"/>
                    </div>
                </div>
                <div class="par control-group">
                    <label class="control-label" for="firstname">Currency Name </label>
                    <div class="controls">
                        <input type="text" name="currency_name" id="currency_name" class="input-large" tabindex="1"/>
                    </div>
                </div>
                <div class="par control-group">
                    <label class="control-label" for="firstname">Name for Paise </label>
                    <div class="controls">
                        <input type="text" name="paise_name" id="paise_name" class="input-large" tabindex="1"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="addCurrencyModal" type="submit" form="addCurrencyFormModal" tabindex="1">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="1">Close</button>
    </div>
</div>
<!--End currency modal-->

<!--Start process modal-->
<div aria-hidden="false" aria-labelledby="processLabel" role="dialog" class="modal over hide fade in" id="processModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="processModalLabel">Add Process</h3>
    </div>
    <div class="modal-body">
        <form id="addProcessFormModal" class="stdform" method="post">
            @csrf
            <div class="row">
                <div class="par control-group">
                    <label class="control-label" for="firstname">Type </label>
                    <div class="controls">
                        <select class="form-control chzn-select" id="type" name="type" readonly tabindex="1">
                            <option value="">Select Type</option>
                            <option value="Process">Process</option>
                            <option value="Machining">Machining</option>
                            <option value="Development">Development</option>
                        </select>
                    </div>
                </div>
                <div class="par control-group">
                    <label class="control-label" for="firstname">Process </label>
                    <div class="controls">
                        <input type="text" name="process" id="process" class="input-large"  tabindex="1"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="addProcessModal" type="submit" form="addProcessFormModal" tabindex="1">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="1">Close</button>
    </div>
</div>
<!--End process modal-->

<!--Start term condition modal-->
<div aria-hidden="false" aria-labelledby="termConditionLabel" role="dialog" class="modal over hide fade in" id="termConditionModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="termConditionModalLabel">Add Terms & Condition</h3>
    </div>
    <div class="modal-body">
        <form id="addTermConditionFormModal" class="stdform" method="post">
            @csrf
            <div class="row">
                <div class="par control-group">
                    <label class="control-label" for="firstname">Sequence </label>
                    <div class="controls">
                        <input type="number" name="sequence" id="sequence" min="1" step="0.1" class="input-large"  tabindex="1"/>
                    </div>
                </div>
                <div class="par control-group">
                    <label class="control-label" for="firstname">Terms </label>
                    <div class="controls">
                        <textarea name="terms" id="terms" class="input-large" tabindex="1"></textarea>
                    </div>
                </div>
                <div id="number-message"></div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="addTermConditionModal" type="submit" form="addTermConditionFormModal" tabindex="1">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="1">Close</button>
    </div>
</div>
<!--End term condition modal-->

<!--Start Customer modal-->
<div aria-hidden="false" aria-labelledby="customerLabel" role="dialog" class="modal over hide fade in" id="customerModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="customerModalLabel">Add Customer</h3>
    </div>
    <div class="modal-body">
        <form id="addCustomerFormModal" class="stdform" method="post">
            @csrf   
            <div class="row">
                <div class="par control-group">
                    <label class="control-label" for="firstname">Customer </label>
                    <div class="controls">
                        <input type="text" name="customer" id="customer" class="input-large"  tabindex="1"/>
                    </div>
                </div>
            
                <div class="par control-group">
                    <label class="control-label" for="firstname">Address </label>
                    <div class="controls">
                        <textarea name="address" id="address" class="input-large" tabindex="1"></textarea>
                    </div>
                </div>
            
                <div class="par control-group">
                    <label class="control-label" for="country">City</label>
                    <div class="controls">
                        <input type="text" name="city" id="city" class="input-large" autocomplete="nope" tabindex="1"/>
                        <div id="city_list" class="suggestion_list"></div>
                    </div>
                </div>
                <div class="par control-group">
                    <label class="control-label" for="pin_code">Pin code</label>
                    <div class="controls">
                    <input type="text" name="pin_code" id="pin_code" class="input-large only-numbers" />
                    </div>
                </div>
                <div class="par control-group">
                    <label class="control-label" for="country">State</label>
                    <div class="controls">
                        <input type="text" name="state_name" id="state_name" class="input-large" autocomplete="nope" tabindex="1"/>
                        <div id="state_name_list" class="suggestion_list"></div>
                    </div>
                </div>
            
                <div class="par control-group">
                    <label class="control-label" class="typeahead form-control" for="country">Country</label>
                    <div class="controls">
                        <input type="text" name="country" id="country" class="input-large" autocomplete="nope" tabindex="1"/>
                        <div id="country_list" class="suggestion_list"></div>
                    </div>
                </div>
            
                <div class="par control-group">
                    <label class="control-label" for="firstname">Phone No </label>
                    <div class="controls">
                        <input type="text" name="phone_no" id="phone_no" class="input-large mobile-f" tabindex="1"/>
                    </div>
                </div>
        
                <div class="par control-group">
                    <label class="control-label" for="firstname">Email ID </label>
                    <div class="controls">
                        <input type="text" name="email" id="email" class="input-large" tabindex="1"/>
                    </div>
                </div>
            
                <div class="par control-group">
                    <label class="control-label" for="firstname">Web Address </label>
                    <div class="controls">
                        <input type="text" name="web_address" id="web_address" class="input-large" tabindex="1"/>
                    </div>
                </div>
            
                <div class="par control-group">
                    <label class="control-label" for="firstname">Person Name </label>
                    <div class="controls">
                        <input type="text" name="person_name" id="person_name" class="input-large" tabindex="1"/>
                    </div>
                </div>
            
                <div class="par control-group">
                    <label class="control-label" for="firstname">Designation </label>
                    <div class="controls">
                        <input type="text" name="designation" id="designation" class="input-large" tabindex="1"/>
                    </div>
                </div>
            
                <div class="par control-group">
                    <label class="control-label" for="firstname">Mobile No </label>
                    <div class="controls">
                        <input type="text" name="mobile_no" id="mobile_no" class="input-large mobile-f" tabindex="1"/>
                    </div>
                </div>  
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="addCustomerModal" type="submit" form="addCustomerFormModal" tabindex="1">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="1">Close</button>
    </div>
</div>
<!--End Customer modal-->