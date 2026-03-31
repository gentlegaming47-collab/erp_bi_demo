<input type="hidden"  name="grn_details_id" id="grn_details_id"/>
<input type="hidden"  name="pre_item_id" id="pre_item_id"/>
<input type="hidden"  name="pre_item_details_id" id="pre_item_details_id"/>
<input type="hidden"  name="item_id" id="item_id"/>
<input type="hidden"  name="item_details_id" id="item_details_id"/>
<input type="hidden"  name="org_ok_qty" id="org_ok_qty"/>
<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="pr_sequence">QC No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="qc_sequence" id="qc_sequence" class="input-large only-numbers sequence" />
                    <input name="qc_number" id="qc_number" class="input-large sequence-number" />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="pr_date">QC Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="qc_date" id="qc_date" class="input-large trans-date-picker  no-fill" />
                    </span> </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="toggle_btn">&nbsp;</label>
            <div class="controls"> <span class="formwrapper input-large">
                  <button class="btn btn-primary toggleModalBtn" type="button" data-target="#pendingGrnModal" data-toggle="modal" disabled>Pending</button>
            </div>
        </div>
    </div>
    

</div> <!-- row end -->

<div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">QC Approval Detail <sup class="astric">*</sup></h4>
        </div>

        <div class="widgetcontent">
            <table class="table table-bordered responsive" id="qc_approval_table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>GRN No.</th>
                        <th>GRN Date</th>
                        <th>Supplier</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Item</th>
                        {{-- <th>Item Detail Name</th> --}}
                        <th>Code</th>
                        <th>Group</th>
                        <th>Unit</th>
                        <th>GRN Qty.</th>
                        <th>Pend. QC Qty.</th>
                    </tr>
                </thead>
                <tbody>                   

                </tbody>               

            </table>
        </div>
    </div>

    <div class="row">
         <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="qc_qty">QC Qty. <sup class="astric">*</sup></label>
                    <div class="controls">
                         <span class="formwrapper">                          
                            <input type="text" name="qc_qty" id="qc_qty" class="form-control isNumberKey" onblur="formatPoints(this,3)" onkeyup="getOKQty()"/>                           
                        </span>
                    </div>
            </div>
        </div>

         <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="ok_qty">OK Qty. <sup class="astric">*</sup></label>
                    <div class="controls">
                         <span class="formwrapper">                          
                            <input type="text" name="ok_qty" id="ok_qty" class="form-control isNumberKey" onblur="formatPoints(this,3)" onkeyup="getRejQty()"/>                           
                        </span>
                    </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="reject_qty">Reject Qty.</label>
                    <div class="controls">
                         <span class="formwrapper">                          
                            <input type="text" name="reject_qty" id="reject_qty" class="form-control isNumberKey" readonly tabindex="-1">                           
                        </span>
                    </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="rejection_reason">Rejection Reason</label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input type="text" name="rejection_reason" id="rejection_reason" class="form-control" placeholder="Enter Rejection Reason" onkeyup="suggestRejectionReason(event,this)" />
                            <div id="rejection_reason_list" class="suggestion_list" ></div>
                        </span>
                    </div>
            </div>
        </div>
    </div>


<script type="text/javascript" src="{{ asset('js/view/qc_approval.js?ver='.getJsVersion()) }}"></script>
