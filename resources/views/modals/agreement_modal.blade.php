   <div aria-hidden="false" aria-labelledby="stateLabel" role="dialog" class="modal over-over hide fade in change_state_modal" id="agreementModal">

    <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>

        <h3 class="modal-title" id="contactModalLabel"><flabel>Add</flabel> Supplier Agreement Details</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true"><i class="dripicons dripicons-cross"></i></span>
    </div>
    <div class="modal-body">
        <form id="agreement_form" class="stdform" name="agreement_form" method="post">      
            <input type="hidden" name="form_type" id="form_type" value="add"/>
            <input type="hidden" name="form_index" id="form_index" />
            <input type="hidden" name="row_index" id="row_index" />
            <input type="hidden" name="agree_strat_min_date" id="agree_strat_min_date" />
            <input type="hidden" name="agree_strat_max_date" id="agree_strat_max_date" />
            <input type="hidden" name="agree_end_max_date" id="agree_end_max_date" />
            <input type="hidden" name="agree_end_min_date" id="agree_end_min_date" />

                    <div class="row">
                        <div class="span-6">
                            <div class="par control-group form-control">
                                    <label class="control-label" for="agreement_start_date">Agreement Start Date </label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <input name="agreement_start_date" id="agreement_start_date" class="date-picker" />
                                    </span> </div>
                                    </span>
                                </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                                    <label class="control-label" for="agreement_end_date">Agreement End Date </label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <input name="agreement_end_date" id="agreement_end_date" class="date-picker  no-fill" />
                                       
                                    </span> </div>
                                    </span>
                                </div>
                        </div>

                        <div class="span-6">
                            <div class="par control-group form-control">
                                <label class="control-label" for="agreement_start_date">Agreement Document </label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="input-append">
                                                        <div class="uneditable-input input-medium1">
                                                            <i class="iconfa-file fileupload-exists icon"></i>
                                                            <span id="agreement_document_img-prev" class="fileupload-preview"></span>
                        
                                                        </div>
                        
                                                        
                                                        <div class="changeStyle" >
                                                                <span class="btn btn-file addButton"><span class="fileupload-new" accept=".pdf">Upload</span>
                                                            <span class="fileupload-exists">Change</span>
                            
                                                            <input type="file" name="agreement_document" id="agreement_document" onchange="fileUpload(event,'file')" style="width:auto;"/></span>
                            
                                                            <input type="hidden" id="agreement_document_doc" name="agreement_document_doc"/>
                            
                                                            <input type="hidden" id="agreement_document_soft_delete"/>
                            
                                                            <a href="#" data-remove="agreement_document" id="agreement_document_remove" class="btn fileupload-exists remove-file addButton" data-dismiss="fileupload" onclick="removeFile(event,'soft')">Remove</a>
                            
                                                            <a target="_blank" class="btn img-prev hidden addButton" id="agreement_document_prev">View</a>
                            
                                                        </div>
                        
                                                    </div>
                                        </div>
                                    </span> 
                                </div>
                            </div>
                        </div>



                         <div class="span-6">
                            <div class="par control-group form-control">
                                <label class="control-label" for="address">Cheque No. </label>
                                    <div class="controls">
                                        <span class="formwrapper">
                                            <input type="text" name="cheque_no" id="cheque_no" class="input-large" placeholder="Enter Cheque No."/>
                                        </span>
                                    </div>
                            </div>
                        </div>
                    </div>
        </form>
    </div>  


    <div class="modal-footer">
        <button class="btn btn-primary" id="submitagreementModal" type="submit" form="agreement_form" tabindex="0"><slabel>Add</slabel></button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
  
    

</div>
<!--End Contact modal-->
