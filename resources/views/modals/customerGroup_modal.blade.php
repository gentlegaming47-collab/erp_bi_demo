
<!--Start customer group modal-->
<div aria-hidden="false" aria-labelledby="cityLabel" role="dialog" class="modal over hide fade in" id="customerGroupModel">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="customerGroupModalLabel">Add Customer Group</h3>
    </div>
    <div class="modal-body">
        <form id="addCustomerGroupFormModal" class="stdform" method="post">
            @csrf
            <div class="row">
                <div class="par control-group">
                    <label class="control-label" for="customer_group_name">Customer Group </label>
                <div class="controls">
                    
                    <input type="text" name="customer_group_name" id="customer_group_name" onkeyup="suggestCustomerGroup(event,this)" class="input-large auto-suggest" autocomplete="nope" autofocus tabindex="0"/>
                        
                    <div id="customer_group_name_list" class="customer_group_suggestion_list" ></div> 
                </div>
            
                    {{-- <div class="controls">
                        <input type="text" name="customer_group_name" id="customer_group_name" onkeyup="suggestCustomerGroup(event,this)" class="input-large auto-suggest" autocomplete="nope" autofocus tabindex="0"/>
                        <div id="customer_group_name_list" class="suggestion_list" ></div>
                    </div> --}}
                </div>

                
            </div>
        </form>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary" id="addCustomerGroupFormModal" type="submit" form="addCustomerGroupFormModal" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>

    
</div>
<!--End customer group modal-->