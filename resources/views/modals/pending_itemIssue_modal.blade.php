<!--Start taluka modal-->
<div aria-hidden="false" aria-labelledby="talukaLabel" role="dialog" class="modal modal-wide over hide fade in" id="pendingItemIssue">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="talukaModalLabel">Select Pending Item Issue</h3>
    </div>
    <div class="modal-body" id="talukaBody">
        <form id="pendingItemIssueForm" class="stdform" method="post">
            @csrf
            <table class="table table-bordered responsive table-autowidth" id="pendingIssueTable">
                <thead>
                <tr>
                    <th><input type="checkbox" name="checkall-issue" class="simple-check" id="checkall-issue"/></th>
                    <th>Issue No.</th>
                    <th>Issue Date</th>
                    <th>Item</th>
                    <th>Code</th>
                    <th>Group</th>
                    <th>Issue Qty.</th>                    
                    <th>Pend. Issue Qty. </th>                    
                    <th>Unit </th>                    
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
           
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="pendingItemIssueModal" type="submit" form="pendingItemIssueForm" tabindex="0">Add</button>
        <button data-dismiss="modal"  type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End city modal-->




