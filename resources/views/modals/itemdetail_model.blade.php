<div aria-hidden="false" aria-labelledby="stateLabel" role="dialog" class="modal over-over hide fade in change_state_modal" id="itemdetailModal">
 
    <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
 
        <h3 class="modal-title" id="contactModalLabel"><flabel>Add</flabel> Item Details</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true"><i class="dripicons dripicons-cross"></i></span>
    </div>
    <div class="modal-body">
        <form id="itemdetail_form" class="stdform" name="itemdetail_form" method="post">      
            <input type="hidden" name="form_type" id="form_type" value="add"/>
            <input type="hidden" name="form_index" id="form_index" />
            <input type="hidden" name="row_index" id="row_index" />
    
            <div class="row">
                    <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for="item_detail_name">Name </label>
                                <div class="controls">
                                <span class="formwrapper">
                                        <input type="text" name="item_detail_name" id="item_detail_name" formControlName="item_detail_name" class="input-large" />
                                    </span>
                                </div>
                        </div>
                    </div> 
            </div>
        </form>
    </div>
        <div class="modal-footer">
            <button class="btn btn-primary" id="submitContactModal" type="submit" form="itemdetail_form" tabindex="0"><slabel>Add</slabel></button>
            <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
        </div>
</div>