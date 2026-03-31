<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="country_name">Customer Group <sup class="astric">*</sup></label>
                <span class="formwrapper">
                    <input type="text" name="customer_group_name" id="customer_group_name" onkeyup="suggestCustomerGroup(event,this)" onfocusout="verifyCustomerGroup()"   class="input-large auto-suggest" autocomplete="nope" placeholder="Enter Customer Group" autofocus/>
                    {{-- <div id="country_name_list" class="suggestion_list" ></div> --}}
                    <div id="customer_group_name_list" class="suggestion_list" ></div>        
                    <input type="hidden" name="cname" id="cus_gpname">
                </span>
        </div>
    </div>
</div>

<script src="{{ asset('js/view/customer_group.js?ver='.getJsVersion()) }}"></script>