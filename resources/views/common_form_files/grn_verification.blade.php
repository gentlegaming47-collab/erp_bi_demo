<style>
    .dataTables_filter {
    top: 35px;
    }
     #grn_verification_table_filter label {
            width: auto;
            white-space: nowrap;
            padding: 0;
    }
 
    #grn_verification_table_length label {
        width: 0;
        white-space: nowrap;
        float: none;
        text-align: unset;
        padding: 0;
    }
</style>
            
            
        <div class="row">
            <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label" for="gv_date">Date <sup class="astric">*</sup></label>
                    <div class="controls">
                        <span class="formwrapper">
                            <input name="gv_date" id="gv_date" class="trans-date-picker no-fill" />
                        </span> 
                    </div>
                </div>
            </div>
        </div>


        <table id="grn_verification_table" class="table table-infinite table-bordered responsive table-autowidth remove-reset-filter">
            <thead>
                <tr>
                    <th class="head1"><input type="checkbox" name="checkall" class="simple-check" id="checkall"/></th>
                    <th class="head1">Location</th>
                    <th class="head1">GRN No.</th>
                    <th class="head1">GRN Date</th>
                    <th class="head1">Item</th>
                    <th class="head1">Item Details Name</th>
                    <th class="head1">Dispatch Plan No.</th>
                    <th class="head1">Dispatch Plan Date</th>
                    <th class="head1">Dispatch Plan Qty.</th>
                    <th class="head1">GRN Qty.</th>
                    <th class="head1">Mismatch Qty.</th>
                    <th class="head1">Unit</th>
                    <th class="head1">Reason</th>
                </tr>
            </thead>
        </table>
            
<script src="{{ asset('js/view/grn_verification.js?ver='.getJsVersion()) }}"></script>
