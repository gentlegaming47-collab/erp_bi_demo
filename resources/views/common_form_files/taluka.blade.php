<div class="row">
    <div class="span-6">
            <div class="par control-group form-control">
                    <label class="control-label" for="taluka">Taluka <sup class="astric">*</sup></label>
                        <div class="controls">
                                <span class="formwrapper">
                                    <input type="text" name="taluka_name" id="taluka_name" onkeyup="suggestTaluka(event,this)"  class="input-large auto-suggest" autocomplete="nope" placeholder="Enter Taluka" autofocus/>
                                    <div id="taluka_list" class="suggestion_list" ></div>
                                    <input type="hidden" name="tul_name" id="tul_name">
                                </span>
                             </div>
            </div>
    </div>
</div>


<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">

                <label class="control-label" for="states_id">State <sup class="astric">*</sup></label>
            
                <div class="controls">
                    
                    <span class="formwrapper">

                    <?php 
                        $taluka = request()->path();    
                        if($taluka != "add-taluka" && $taluka != "add-customer" && $taluka !=  "add-supplier" && $taluka != "add-location" && $taluka != "add-taluka" && $taluka != "add-village" && $taluka != "add-sales_order" && $taluka != "add-dealer" && $taluka != "add-quotation"){
                            $editId =  "edit-taluka/".($id);
                        }
                        else{
                            $editId =  "";
                        }
                        $id = $editId != "" ? $id : "";
                      
                        if($taluka == "add-taluka" || $taluka == "edit-taluka/".($id))    
                         $page = $taluka == "add-taluka" ? "add-taluka" : "edit-taluka/".base64_decode($id);       
                        else 
                            $page = '';
                    ?>
            
                            @if($page == '' || $page == "add-taluka" || "edit-taluka/".base64_decode($id)) 
                                <select name="taluka_state_id" id="taluka_state_id" class="chzn-select mst-suggest_state" onchange="getTalukaRelationData(),getDistrict()">
                        
                                    <option value="">Select State</option>
            
                                @forelse (getStates() as $state)
            
                                    <option value="{{ $state->id }}">{{ $state->state_name }}</option>
            
                                    @empty
            
                                @endforelse 
                            </select>@if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif      

                            @else 
            
                            <select name="taluka_state_id" id="taluka_state_id" class="chzn-select mst-suggest_state" onchange="getTalukaRelationData(),getDistrict()">
                        
                                    <option value="">Select State</option>
            
                            </select>@if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif  
                            
                            @endif
                    
                    {{-- @dd($id) --}}
                
                                    
                    
                    </span>
                </div>
            
        </div>
    </div>
</div>
            

<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="taluka_district_id">District <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper">

                        <select name="taluka_district_id" id="taluka_district_id" class="chzn-select mst-suggest_city" onchange="CheckTaluka()">
                            <option value="">Select District</option>
                                
                        </select>@if(hasAccess('district','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#cityModal"></i></span>@endif
                    </span>
                </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="country_name">Country </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="country_name" id="country_name" class="input-large" />
                    </span>
                </div>
        </div>
    </div>
</div>


<script src="{{ asset('js/view/taluka.js?ver='.getJsVersion()) }}"></script>
