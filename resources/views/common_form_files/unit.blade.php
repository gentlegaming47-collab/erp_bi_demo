
<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="unit_name">Unit <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                <input type="text" name="unit_name" id="unit_name" onkeyup="suggestUnit(event,this)" class="input-large auto-suggest" onfocusout="verifyUnit()" autocomplete="nope" placeholder="Enter Unit" autofocus/>
                <div id="company_unit_name_list" class="suggestion_list" ></div>
                <input type="hidden" name="unit_data" id="unit_data">
                </span>

            </div>
        </div>       
    </div>
</div>      


<script src="{{ asset('js/view/unit.js?ver='.getJsVersion()) }}"></script>