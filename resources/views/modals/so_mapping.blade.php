<!--Start pending Purchase modal-->
<div aria-hidden="false" aria-labelledby="pendingDcLabel" role="dialog" class="modal modal-wide over hide fade grnmodal" id="customerReplacementModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="custSearchModalLabel">Select Customer Replacement</h3>
    </div>

    
    <div class="modal-body">
        <form id="customerReplacement" name="customerReplacement" class="stdform" method="post">
           @csrf
            <table class="table table-bordered responsive table-autowidth" id="customerReplacementTable">
                <thead>
                <tr>
                    <th></th>
                    <th>Sr. No.</th>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Item Detail Name</th>
                    <th>Code</th>
                    <th>Group</th>
                    <th>Ret. Qty.</th>
                    <th>pend. Ret. Qty. </th>
                    <th>Unit</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="customerReplacementModal" type="submit" form="customerReplacement" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End pending COA modal-->




