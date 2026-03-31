
<!-- Modals -->

@include('modals.city_modal')

<!-- End Modals -->


<!--Start Customer modal-->
<div aria-hidden="false" aria-labelledby="customerLabel" role="dialog" class="modal modal-wide over hide fade in" id="customerModal">
    <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h3 id="customerModalLabel">Add Customer</h3>
        </div>
    <div class="modal-body">
        <form id="commonCustomerForm" class="stdform" method="post">
            @csrf
                @include('common_form_files.customer')
            {{-- <div class="row">
                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="customer">Customer</label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="customer_name" id="customer_name" onkeyup="suggestCustomer(event,this)" class="input-large auto-suggest" autocomplete="nope" tabindex="0" autofocus/>
                                <div id="customer_name_list" class="suggestion_list" ></div>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="span-4">
                    <div class="par control-group">
                        <label class="control-label" for="customer_code" tabindex="0">Customer Code </label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="customer_code" id="customer_code" class="input-large" />
                            </span>
                        </div>
                    </div>
                </div>
              
            </div>
                
            <div class="row">
                <div class="span-4">
                    <div class="par control-group">
                        <label class="control-label" for="customer_type_fix_id">Customer Type</label>
                        <div class="controls">    
                            <span class="formwrapper">
                            <select name="customer_type_fix_id" id="customer_type_fix_id" class="chzn-select">
                                <option value="">Select Customer Type</option>
                                @forelse (customer_type() as $customer_type)

                                <option value="{{ $customer_type->id }}">{{ $customer_type->name }}</option>

                                @empty

                                @endforelse  
                            </select>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="address" tabindex="0">Address </label>
                        <div class="controls">
                            <span class="formwrapper">
                                <textarea id="address" name="address" class="input-large h-auto" rows="3"></textarea>
                            </span>
                        </div>
                    </div>
                </div>
            
            
               
            </div>    
            <div class="row">   
                <div class="span-4">
                    <div class="par control-group">
                        <label class="control-label" for="city_id">City</label>
                        <div class="controls">    
                            <span class="formwrapper">
                                <select name="city_id" id="city_id" class="chzn-select mst-city" onchange="getCustomerRelationData(event)">
                                    <option value="">Select City</option>
                                        @forelse (getCities() as $city)
                                            <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                                            @empty
                                        @endforelse    
                                </select>@if(hasAccess('city','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#cityModal"></i></span>@endif
                            </span>
                        </div>
                    </div>
                </div> 
            

                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="pin_code">Pin Code </label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="pin_code" id="pin_code" class="input-large" tabindex="0"/>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="state">State </label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="state" id="state" class="input-large" readonly tabindex="0"/>
                            </span>
                        </div>
                    </div>
                </div>
        
                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="country">Country </label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="country" id="country" class="input-large" readonly tabindex="0"/>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">    
                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="phone">Phone No.</label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="phone" id="phone" class="input-large mobile-f" tabindex="0"/>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="email">Email ID</label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="email" id="email" class="input-large" tabindex="0"/>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="web_address">Web Address </label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="web_address" id="web_address" class="input-large" tabindex="0"/>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="pan">PAN </label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="pan" id="pan" class="input-large" disabled tabindex="0"/>
                            </span>
                        </div>
                    </div>
                </div>

                
            </div>    
            
            <div class="row">
                    <div class="span-4">
                        <div class="par control-group">
                                <label class="control-label" for="gstin">GSTIN </label>
                            <div class="controls">
                                <span class="formwrapper">
                                    <input type="text" name="gstin" id="gstin" class="input-large" disabled tabindex="0"/>
                                </span>
                            </div>
                        </div>
                    </div>
                
            
                <div class="span-4">
                    <div class="par control-group">
                            <label class="control-label" for="payment_terms">Payment Terms</label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="payment_terms" id="payment_terms" class="input-large" tabindex="0"/>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="span-4">
                            <div class="par control-group">
                              <label class="control-label" for="currency_id">Currency </label>
                              <div class="controls"> <span class="formwrapper">
                                <select name="currency_id" id="currency_id" class="chzn-select mst-currency">
                                  <option value="">Select Currency</option>
                                  @forelse (getCurrencies() as $currency)
                                      <option value="{{ $currency->id }}">{{ $currency->currency }}</option>
                                      @empty
                                  @endforelse
                                </select>
                                </div>
                            </div>
                        </div>

                    </div>
            </div>
        </form> --}}
        
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="commonCustomerForm" type="submit" form="commonCustomerForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End Customer modal-->