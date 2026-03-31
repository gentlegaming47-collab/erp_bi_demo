<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="mis_category">MIS Category <sup class="astric">*</sup></label>
                    <span class="formwrapper">
                        <input type="text" name="mis_category" id="mis_category" onkeyup="suggestMisCategory(event,this)" onfocusout="verifyMisCategory()" class="input-large auto-suggest" autocomplete="nope" placeholder="Enter MIS Category" autofocus tabindex="1"/>

                    <div id="mis_category_list" class="suggestion_list" ></div>
                    <input type="hidden" name="mis_suggesion" id="mis_suggesion">   
                    </span>
        </div>
    </div>
</div>


<script src="{{ asset('js/view/mis_category.js?ver='.getJsVersion()) }}"></script>