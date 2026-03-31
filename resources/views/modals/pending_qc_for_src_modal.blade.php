<!--Start taluka modal-->
<div aria-hidden="false" aria-labelledby="talukaLabel" role="dialog" class="modal modal-wide over hide fade in " id="pendingQcRequest">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="pendingQcRequestModalLabel">Select Pending Qty.</h3>
    </div>
    <div class="modal-body" id="pendingQcRequestBody">
        <form id="pendingQcRequestForm" class="stdform" method="post">
            @csrf
            <table class="table table-bordered responsive table-autowidth" id="pendingQcRequestTable">
                <thead>
                <tr>
                    <th><input type="checkbox" name="checkall-qc" class="simple-check" id="checkall-qc"/></th>
                    <th>QC No.</th>
                    <th>QC Date</th> 
                    {{-- <th>GRN No. </th>  --}}
                    {{-- <th>GRN Date </th>  --}}
                    <th>Item</th> 
                    <th>Code</th> 
                    <th>Group</th> 
                    <th>Pending Reject Qty.</th> 
                    <th>Unit </th> 
                    
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
           
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="pendingQcRequestModal" type="submit" form="pendingQcRequestForm" tabindex="0">Add</button>
        <button data-dismiss="modal"  type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End city modal-->




