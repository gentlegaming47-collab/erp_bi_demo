<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">

                <label class="control-label" for="district_name">District <sup class="astric">*</sup></label>

                    <div class="controls">
                                <span class="formwrapper">
                                    <input type="text" name="district_name" id="district_name" onkeyup="suggestCity(event,this)"  onfocusout="CheckCity()"   class="input-large auto-suggest" autocomplete="nope" placeholder="Enter District" autofocus/>

                                    <div id="district_name_list" class="suggestion_list" ></div>
                                    <input type="hidden" name="ctname" id="ctname">
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
                    $district = request()->path();   
                    
                    if($district != "add-district" && $district != "add-customer" && $district !=  "add-supplier" && $district != "add-location" && $district != "add-taluka" &&  $district != "add-village" && $district != "add-sales_order" && $district != "add-dealer" && $district != "add-quotation"){                        
                        $editId =  "edit-district/".($id);                         
                    }
                    else{

                        $editId =  "";
                    }
                    $id = $editId != "" ? $id : "";
                    // $id = ($district == "edit-district/".$id ==  '') ? "": $id;       
                    if($district == "add-district" || $district == "edit-district/".($id))     
                        $page = $district == "add-district" ? "add-district" : "edit-district/".base64_decode($id);    
                    else 
                        $page = '';
                        
                ?>
                    
                        @if($district == "add-dealer" || $district == "edit-dealer" || $district == "add-supplier" || $district == "edit-supplier" )

                        <select name="state_id" id="state_id" class="chzn-select mst-suggest_state" onchange="getRelationData(), CheckCity()">
                    
                            <option value="">Select State</option>
                     
                    </select>
                    
                    @if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif   

                        @elseif($page == '' || $page == "add-district" || $page == "edit-district/".base64_decode($id)) 
                            <select name="state_id" id="state_id" class="chzn-select mst-suggest_state" onchange="getRelationData(), CheckCity()">
                    
                                <option value="">Select State</option>
                            @forelse (getStates() as $state)

                                <option value="{{ $state->id }}">{{ $state->state_name }}</option>

                                @empty

                            @endforelse 
                        </select>
                        
                        @if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif         
                        @else 

                        <select name="state_id" id="state_id" class="chzn-select mst-suggest_state" onchange="getRelationData(), CheckCity(), getDistrictData() ">
                    
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

            <label class="control-label" for="country_name">Country </label>

                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="country_name" id="country_name" class="input-large"/>
                    </span>

            </div>

        </div>
    </div>
</div>



<script src="{{ asset('js/view/district.js?ver='.getJsVersion()) }}"></script>