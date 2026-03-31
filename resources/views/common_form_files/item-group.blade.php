<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="item_group">Item Group <sup class="astric">*</sup></label>
                    <div class="controls">
                        <span class="formwrapper">
                            <input type="text" name="item_group_name" id="item_group_name" onkeyup="suggestItemGroupName(event,this)"  onfocusout="verifyItemGroup()" class="input-large auto-suggest mst-suggest_item_group" autocomplete="nope" autofocus placeholder="Enter Item Group" autofocus/>
                            <div id="item_group_name_list" class="suggestion_list" ></div>
                            <input type="hidden" name="item_group" id="item_group">
                        </span>
                    </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="item_group_code">Group Code <sup class="astric">*</sup></label>
                    <div class="controls">
                        <span class="formwrapper">
                            <input type="text" name="item_group_code" id="item_group_code" class="input-large auto-suggest"  onfocusout="verifyItemGroupCode()" onkeyup="suggestItemGroupCode(event,this)"   autocomplete="nope"  placeholder="Enter Group Code"/>
                            <div id="group_code_name_list" class="suggestion_list" ></div>
                            <input type="hidden" name="itemGroupCode" id="itemGroupCode">
                        </span>
                    </div>
        </div>
    </div>
</div>


<script src="{{ asset('js/view/item_group.js?ver='.getJsVersion()) }}"></script>