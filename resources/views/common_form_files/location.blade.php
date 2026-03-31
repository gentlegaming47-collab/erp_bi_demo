<input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
<input type="hidden" value="Location" name="hidViewPage" id="hidViewPage"/>

<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="location_name">Location <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper"> 
                 <input type="text" name="location_name" id="location_name" class="input-large auto-suggest" placeholder="Enter Location" onfocusout="CheckLocation()"   onkeyup="suggestLocationName(event,this)" autocomplete="nope" autofocus/>
                </span>
                <div id="location_name_list" class="suggestion_list" ></div>
                <input type="hidden" name="loc_name" id="loc_name">
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="location_type">Type <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper"> 
                        <select name="location_type" id="location_type" class="chzn-select">
                            <option value="">Select Type</option>
                            <option value="HO">HO</option>
                            <option value="godown">Godown</option>
                        </select>
                    </span>
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="location_type">Location Code <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper"> 
                    <input type="text" name="location_code" id="location_code" class="input-large auto-suggest" placeholder="Enter Location Code" autocomplete="nope" autofocus/>
                    </span>                            
                </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control" id="mfg_process">
            <label class="control-label" for="mfg_process">Mfg. Process </label>
                <div class="controls">
                    <span class="formwrapper"> 
                        <select name="mfg_process" id="mfg_process" class="chzn-select">                                    
                            <option value="Yes" selected>Yes</option>
                            <option value="No">No</option>
                        </select>
                    </span>
                </div>
        </div>
    </div>

    </div>
    
    <div class="row"> 
       

       

    

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="header_print">header Print </label>
                    <div class="controls">
                        <span class="formwrapper"  style="width: 800px;" > 
                            <textarea class="ckeditor form-control" name="editor" id="editor"></textarea>
                        </span>
                    </div>
            </div>
        </div>
    
        {{-- <div class="span-6">
            <div class="par control-group form-control" id="mfg_process">
                <label class="control-label" for="header_print">header Print</label>

                <div class="par control-group" style="width: 800px; magin-left:220px;">
                        <div class="controls">                            
                            <textarea class="ckeditor form-control" name="editor" id="editor"></textarea>
                        </div>
                </div> --}}
</div>

    <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="location_country_id">Country <sup class="astric">*</sup></label>
                    <div class="controls">
                    <span class="formwrapper"> 
                            <select name="location_country_id" id="location_country_id" class="chzn-select mst-country" onchange="getLocationStates(event)">
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

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="location_state_id">State <sup class="astric">*</sup></label>
                <div class="controls">
                <span class="formwrapper"> 
                    <select data-placeholder="Select State" name="location_state_id" id="location_state_id" class="chzn-select mst-suggest_state" onchange="getLocationDistrict(event), getLocationTaluka(event)" tabindex="0">
                    </select>
                    @if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif
                </span>
                </div>
            </div>
        </div>
  
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="location_district_id">District <sup class="astric">*</sup></label>
                <div class="controls">
                <span class="formwrapper"> 
                    <select data-placeholder="Select District" name="location_district_id" id="location_district_id" class="chzn-select mst-suggest_city" onchange="getLocationTaluka(event)" tabindex="0">
                    </select>
                    @if(hasAccess('district','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#cityModal"></i></span>@endif
                </span>
                </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="location_taluka_id">Taluka <sup class="astric">*</sup></label>
                <div class="controls">
                <span class="formwrapper"> 
                    <select data-placeholder="Select Taluka" name="location_taluka_id" id="location_taluka_id" onchange="getLocationVillage(event)"  class="chzn-select mst-suggest_taluka" tabindex="0">
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
                <label class="control-label" for="location_village_id">Village <sup class="astric">*</sup></label>
                <div class="controls">
                        <span class="formwrapper"> 
                    <select data-placeholder="Select Village" name="location_village_id" id="location_village_id" class="chzn-select mst-suggest_village" tabindex="0">    
                    </select>
                    @if(hasAccess('village','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#VillageModal"></i></span>@endif
                        </span>
                </div>
            </div>
        </div>

    </div>

    <div class="par control-group form-control" id="hide-status">
        <label class="control-label" for="status">Status </label>
            <div class="controls">
                    <select name="status" id="status" class="chzn-select">
                        {{-- <option value="">Select Status</option> --}}
                        <option value="active">Active</option>
                        <option value="deactive">Deactive</option>
                    </select>
            </div>
    </div>

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js">
        jQuery('.ckeditor').ckeditor();
    </script>
    
<script src="{{ asset('js/view/location.js?ver='.getJsVersion()) }}"></script>