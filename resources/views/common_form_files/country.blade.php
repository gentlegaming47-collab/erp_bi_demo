<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="supplier_name">Country <sup class="astric">*</sup></label>
                    <span class="formwrapper">
                        <input type="text" name="country_name" id="country_name" onkeyup="suggestCountry(event,this)" onfocusout="verifyCountry()" class="input-large auto-suggest" autocomplete="nope" placeholder="Enter Country" autofocus tabindex="1"/>

                    <div id="country_name_list" class="suggestion_list" ></div>
                    {{-- get the suggesion at duplication time --}}
                    <input type="hidden" name="country_suggesion" id="country_suggesion">   
                    </span>
        </div>
    </div>
</div>


<script src="{{ asset('js/view/country.js?ver='.getJsVersion()) }}"></script>