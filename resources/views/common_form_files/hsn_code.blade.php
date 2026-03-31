<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                    <label class="control-label hsn_label" for="hsn_code">HSN Code <sup class="astric">*</sup></label>

                    <div class="controls">
                        <span class="formwrapper">
                            <input type="text" name="hsn_code" id="hsn_code" onkeyup="suggestHsnCode(event,this)" onfocusout="verifyHSNCode()"  class="input-large auto-suggest only-numbers" autocomplete="nope" autofocus placeholder="Enter HSN Code"/>

                            <div id="hsn_code_list" class="suggestion_list" ></div>
                            <input type="hidden" name="hsn" id="hsn">
                        </span>
                </div>
            </div>
    </div>
</div>
<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label hsn_label" for="hsn_description">HSN Description </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="hsn_description" id="hsn_description" class="input-large auto-suggest" placeholder="Enter HSN Description" autocomplete="nope"/>
                    </span>

                </div>
        </div>
    </div>
</div>



<script src="{{ asset('js/view/hsn_code.js?ver='.getJsVersion()) }}"></script>

