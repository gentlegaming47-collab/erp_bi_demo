
                      
                    <div class="row">

                        
                        <div class="span-4">
                            <div class="par control-group">
                                    <label class="control-label" for="customer_code">Customer Code </label>
                                <div class="controls">
                                    <span class="formwrapper"> 
                                        <input type="text" name="customer_code"  id="customer_code" class="input-large" readonly />
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="span-4">

                                <div class="par control-group">

                                    <label class="control-label" for="customer">Customer Name </label>

                                    <div class="controls">

                                        <span class="formwrapper">

                                            <input type="text" name="customer_name" id="customer" onkeyup="suggestCustomer(event,this)" onfocusout="verifyCustomer()"  class="input-large auto-suggest" autocomplete="nope" placeholder="Enter Customer Name" autofocus/>

                                            <div id="customer_list" class="suggestion_list" ></div>
                                            <input type="hidden" name="cname" id="cusname">
                                        </span>

                                    </div>

                                </div>

                            </div>

                    </div>
                    <div class="row">

                        <div class="span-4">
    
                            <div class="par control-group">
    
                                <label class="control-label" for="customer_type">Customer Group</label>
    
                                <div class="controls">
    
                                    <span class="formwrapper">

                                        {{-- customer godown condition --}}
                                        

                                        @if(!empty($checkUser))
                                         <select name="customer_group_id" id="customer_group_id" class="chzn-select" readonly>
                                        @else 
                                            <select name="customer_group_id" id="customer_group_id" class="chzn-select">
                                        @endif
                                        <option value="">Select Customer Group</option>
                                            @forelse (getCustomerGroup() as $customer_group)
            
                                            <option value="{{ $customer_group->id }}">{{ $customer_group->customer_group_name }}</option>
            
                                            @empty
            
                                        @endforelse
            
                                    </select>
                                    
                                 
            
                                    </span>

                                    </span>
    
                                </div>
    
                            </div>
    
                        </div>

                        <div class="span-4">
                            <div class="par control-group">
                                    <label class="control-label" for="register_number">Reg. No. </label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <input type="text" name="register_number"  id="register_number" class="input-large" placeholder="Enter Reg. No." />
                                    </span>
                                </div>
                            </div>
                        </div>
                   
                    </div>

                    <div class="row">

                        <div class="span-4">

                            <div class="par control-group">

                            <label class="control-label" for="address">Address </label>

                                <div class="controls">

                                    <span class="formwrapper">

                                        <textarea id="address" name="address"  class="input-large h-auto" placeholder="Enter Address" rows="3"></textarea>

                                    </span>

                                </div>

                            </div>

                        </div>

                        <div class="span-4">

                                    <div class="par control-group">
        
                                            <label class="control-label" for="country">Country </label>
        
                                        <div class="controls">
        
                                            <span class="formwrapper">
        
                                                <select name="customer_country_id" id="customer_country_id" class="chzn-select mst-country" onchange="getCustomerStates(event)">
                                                    <option value="">Select Country</option>
                                                         @forelse (getCountries() as $country)
                                                            <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                                         @empty
                                                         @endforelse    
                                                <select>
                                            @if(hasAccess('country','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#countryModal"></i></span>@endif
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
                                                       
                                                    
                                        <select data-placeholder="Select State" name="customer_state_id" id="customer_state_id" class="chzn-select mst-suggest_state" onchange="getCustomerDistrict(event), getCustomerTaluka(event)" tabindex="0">
                                        </select>
                                        @if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif
                                    </span>
        
                                </div>
        
                            </div>
        
                     </div>


                        <div class="span-4">

                            <div class="par control-group">

                                <label class="control-label" for="city_id">District</label>

                                <div class="controls">

                                    <span class="formwrapper">

                                        <select data-placeholder="Select District" name="customer_district_id" id="customer_district_id"  class="chzn-select mst-suggest_city" onchange="getCustomerTaluka(event)" tabindex="0">
                                        </select>
                                        @if(hasAccess('district','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#cityModal"></i></span>@endif

                                    </span>

                                </div>

                            </div>

                        </div>                       

                    </div>

                    <div class="row">

                            <div class="span-4">
    
                                    <div class="par control-group">
            
                                        <label class="control-label" for="customer_taluka_id">Taluka</label>
            
                                        <div class="controls">
            
                                            <span class="formwrapper">
            
                                                <select data-placeholder="Select Taluka" name="customer_taluka_id" id="customer_taluka_id" onchange="getCustomerVillage(event)"  class="chzn-select mst-suggest_taluka"tabindex="0">
                                                </select>

                                                @if(hasAccess('taluka','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#talukaModal"></i></span>@endif
            
                                            </span>
            
                                        </div>
            
                                    </div>
            
                                </div>

                            <div class="span-4">
    
                                    <div class="par control-group">
            
                                        <label class="control-label" for="village_id">Village</label>
            
                                        <div class="controls">
            
                                            <span class="formwrapper">
            
                                            <select data-placeholder="Select Village" name="village_id" id="customer_village_id" class="chzn-select mst-suggest_village" tabindex="0">
                               
                                                </select>
                                            @if(hasAccess('village','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#VillageModal"></i></span>@endif
            
                                            </span>
            
                                        </div>
            
                                    </div>
            
                                </div>

                     
                   
                        </div>

                            

                    <div class="row">
                        <div class="span-4">
    
                            <div class="par control-group">
    
                                    <label class="control-label" for="pincode">Pin Code </label>
    
                                <div class="controls">
    
                                    <span class="formwrapper">
    
                                        <input type="text" name="pincode"  id="pincode" class="input-large" placeholder="Enter Pin Code" />
    
                                    </span>
    
                                </div>
    
                            </div>
    
                        </div>

                        <div class="span-4">

                            <div class="par control-group">

                                    <label class="control-label" for="phone_no">Mobile No.</label>

                                <div class="controls">

                                    <span class="formwrapper">

                                        <input type="text" name="mobile_no" id="mobile_no" class="input-large mobile-f" placeholder="Enter Mobile Number" />

                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                    
                    <div class="row">

                        <div class="span-4">

                            <div class="par control-group">

                            <label class="control-label" for="email">Email ID</label>

                                <div class="controls">

                                    <span class="formwrapper">

                                        <input type="text" name="email" id="email" class="input-large checkEmail" placeholder="Enter Email" />

                                    </span>

                                </div>

                            </div>

                        </div>

                       

                        <div class="span-4">

                            <div class="par control-group">

                                    <label class="control-label" for="pan">PAN </label>

                                <div class="controls">

                                    <span class="formwrapper">

                                        <input type="text" name="pan" id="pan"  class="input-large" placeholder="Enter PAN Number"/>

                                    </span>

                                </div>

                            </div>

                        </div>
                     </div>

                     {{-- fourth row start --}}
                   

                    <div class="row">

                       

                        <div class="span-4">

                            <div class="par control-group">

                            <label class="control-label" for="gstin">GSTIN </label>

                                <div class="controls">

                                    <span class="formwrapper">

                                        <input type="text" name="gstin" id="gstin" class="input-large" placeholder="Enter GST Number"/>

                                    </span>

                                </div>

                            </div>

                        </div>



                        <div class="span-4">

                            <div class="par control-group">

                                    <label class="control-label" for="aadhar_no">Aadhar No.</label>

                                <div class="controls">

                                    <span class="formwrapper">

                                        <input type="text" name="aadhar_no"  id="aadhar_no" class="input-large checkNumberFormat" placeholder="Enter Aadhar Number"/>

                                    </span>

                                </div>

                            </div>

                        </div>

</div>

                    

              
<script src="{{ asset('js/view/customer.js?ver='.getJsVersion()) }}"></script>