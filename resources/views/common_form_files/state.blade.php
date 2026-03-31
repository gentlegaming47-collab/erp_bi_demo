    
<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="state_name">State  <sup class="astric">*</sup></label>
                        <span class="formwrapper">
                            <input type="text" name="state_name" id="state_name" onkeyup="suggestState(event,this)" onfocusout="CheckState()" class="input-large auto-suggest" autocomplete="nope" autofocus placeholder="Enter State"/>
                            <div id="state_name_list" class="suggestion_list" ></div>
                            <input type="hidden" name="sname" id="sname">
                        </span>
        </div>
    </div>
</div>



<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="country_id">Country  <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper">
                        <select name="country_id" id="country_id" class="chzn-select mst-country"   onchange="CheckState()">
                                <option value="">Select Country</option>
                                @forelse (getCountries() as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @empty
                                @endforelse    
                        </select>@if(hasAccess('country','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#countryModal"></i></span>@endif
                    </span>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="gst_code">GST Code </label>
                    <div class="controls">
                        <span class="formwrapper">
                            <input type="text" name="gst_code" id="gst_code" formControlName="gst_code"  onchange="CheckGst()"  class="input-large only-numbers" disabled/>
                        </span>
                    </div>
        </div>
    </div>
</div>


<script src="{{ asset('js/view/state.js?ver='.getJsVersion()) }}"></script>