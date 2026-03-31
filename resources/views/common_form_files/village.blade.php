<div class="row">
    <div class="span-6">
            <div class="par control-group form-control">

                    <label class="control-label village_label" for="village_name">Village <sup class="astric">*</sup></label>

                <div class="controls">
                <span class="formwrapper">

                    <input type="text" name="village_name" id="village_name" onkeyup="suggestVillage(event,this)"  class="input-large auto-suggest" autocomplete="nope" placeholder="Enter Village" autofocus/>
                    {{-- <input type="text" name="village_name" id="village_name" onkeyup="suggestVillage(event,this)" onfocusout="CheckVillage()" class="input-large auto-suggest mst-village-data" autocomplete="nope" placeholder="Enter Village" autofocus/> --}}

                    <div id="village_name_list" class="suggestion_list" ></div>
                    <input type="hidden" name="vil_name" id="vil_name">
                    </span>
                </div>

            </div>
    </div>
</div>



<div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
    
                    <label class="control-label village_label" for="states_id">State <sup class="astric">*</sup></label>
                
                    <div class="controls">
                        <span class="formwrapper">
                        
                        
                        <?php 
                            $village = request()->path();    
                            if($village != "add-village" && $village != "add-customer" && $village !=  "add-supplier" && $village != "add-location" && $village != "add-taluka" && $village != "add-village" && $village != "add-sales_order" && $village != "add-dealer" && $village != "add-quotation"){
                                $editId =  "edit-village/".($id);
                            }
                            else{
                                $editId =  "";
                            }
                            $id = $editId != "" ? $id : "";
                          
                            if($village == "add-village" || $village == "edit-village/".($id))    
                             $page = $village == "add-village" ? "add-village" : "edit-village/".base64_decode($id);       
                            else 
                                $page = '';
                        ?>
                
                                @if($page == '' || $page == "add-village" || "edit-village/".base64_decode($id)) 
                                
                                    <select name="village_state_id" id="village_state_id" class="chzn-select mst-suggest_state" onchange="getDistrictData(), getTalukaData()">
                            
                                        <option value="">Select State</option>
                
                                    @forelse (getStates() as $state)
                
                                        <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                
                                        @empty
                
                                    @endforelse 
                                </select>@if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif      
    
                                @else 
                
                                <select name="village_state_id" id="village_state_id" class="chzn-select mst-suggest_state" onchange="getDistrictData(),getTalukaData()">
                            
                                        <option value="">Select State</option>
                
                                </select>@if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif  
                                
                                @endif
                        
                        {{-- @dd($id) --}}
                    
                                        
                        
                        </span>
                    </div>
                
            </div>
        </div>
    </div>

{{-- <div class="row">
    <div class="span-6">
        <div class="par control-group form-control">

            <label class="control-label" for="state_id">State</label>

            <div class="controls">

                <select name="state_id" id="village_state_id" class="chzn-select mst-suggest_state" onchange="getDistrictData()">
                    
                    <option value="">Select State</option>

                    @forelse (getStates() as $state)

                        <option value="{{ $state->id }}">{{ $state->state_name }}</option>

                        @empty

                    @endforelse    

                </select>
                @if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif      


            </div>

        </div>
    </div>
</div> --}}

        
<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">

            <label class="control-label village_label" for="district_name">District <sup class="astric">*</sup></label>

            <div class="controls">
                <span class="formwrapper">
                <select name="district_id" id="district_id" class="chzn-select mst-suggest_city" onchange="getTalukaData(event)">

                    <option value="">Select District</option>

                    {{-- @forelse ($district as $districts)

                        <option value="{{ $districts->id }}">{{ $districts->district_name }}</option>

                        @empty

                    @endforelse     --}}

                </select>
                @if(hasAccess('district','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#cityModal"></i></span>@endif
                </span>
            </div>

        </div>
    </div>
</div>


<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">

            <label class="control-label village_label" for="taluka_name">Taluka <sup class="astric">*</sup></label>

            <div class="controls">
                <span class="formwrapper">

                <select name="taluka_id" id="taluka_id" class="chzn-select mst-suggest_taluka"  onchange="CheckVillage()">

                    <option value="">Select Taluka</option>
        {{-- 
                    @forelse (getTalukas() as $village)

                        <option value="{{ $village->id }}">{{ $village->taluka_name }}</option>

                        @empty

                    @endforelse     --}}

                </select>
                @if(hasAccess('taluka','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#talukaModal"></i></span>@endif
                </span>
            </div>

        </div>
    </div>
</div>



<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">

                <label class="control-label village_label" for="country_name">Country </label>

                <div class="controls">
                    <span class="formwrapper">

                    <input type="text" name="country_name" id="country_name" class="input-large" tabindex="-1"/>
                    </span>

                </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="span-6">

        <div class="par control-group form-control">

            <label class="control-label village_label" for="default_pincode">Default Pin Code </label>

                <div class="controls">
                    <span class="formwrapper">

                    <input type="text" name="default_pincode" id="default_pincode"  class="input-large auto-suggest only-numbers" placeholder="Enter Pin Code" autocomplete="nope" />

                    <div id="default_pincode_list" class="suggestion_list" ></div>
                    </span>
                </div>

        </div>
    </div>
</div>


<script src="{{ asset('js/view/village.js?ver='.getJsVersion()) }}"></script>