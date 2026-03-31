<!--Start taluka modal-->
<div aria-hidden="false" aria-labelledby="talukaLabel" role="dialog" class="modal modal-wide over hide fade in" id="pendingDispatchPlan">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="talukaModalLabel">Select Pending Dispatch Paln</h3>
    </div>
    <div class="modal-body" id="talukaBody">
        <form id="pendingDispatchPlanForm" class="stdform" method="post">
            @csrf
            <table class="table table-bordered responsive table-autowidth" id="pendingDispatchPlanTable">
                <thead>
                <tr>
                    <th></th>
                    <th>Disp. Plan No.</th>
                    <th>Disp. Plan Date</th>
                    <th>Dealer</th>
                    <th>Sp. Note</th>                              
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
           
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="pendingDispatchPlanModal" type="submit" form="pendingDispatchPlanForm" tabindex="0">Add</button>
        <button data-dismiss="modal"  type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End city modal-->




