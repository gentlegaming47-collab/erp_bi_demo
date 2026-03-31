<!--Start Sales Order modal-->
<div aria-hidden="false" aria-labelledby="stateLabel" role="dialog" class="modal modal-wide over hide fade in" id="DispatchSecondaryForLoadingModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="stateModalLabel">Dispatch Secondary Item</h3>
    </div>
    <div class="modal-body">
        <form id="addPendingDispatchSecondaryForm" class="stdform" method="post">
            <input type="hidden" name="dp_details_id" id="dp_details_id">
            <input type="hidden" name="pend_second_plan_qty" id="pend_second_plan_qty">
            <input type="hidden" name="pend_type" id="pend_type">
            @csrf
           
            <table class="table table-bordered responsive table-autowidth" id="DispatchSecondaryForLoadingModalTable">
                <thead>
                <tr>   
                    {{-- <th><input type="checkbox" name="checkall-sod_second_data" class="simple-check" id="checkall-sod_second_data"/></th>      --}}
                    <th>Item</th>
                    <th>Code</th> 
                    <th>Group</th>     
                    <th>Primary Stock Qty.</th>
                    <th>Primary Unit</th>
                    <th>Secondary Stock Qty.</th>
                    <th>Secondary Unit</th>            
                    <th>Pend. SO Qty.</th>  
                    <th>Plan Qty.</th>                         
                </tr>
                </thead>
                <tbody>                
                </tbody>                
            </table>          
        </form>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary" id="addPendingDispatchSecondaryBtn" type="submit" form="addPendingDispatchSecondaryForm" tabindex="0">Add</button>
        <button data-dismiss="modal" id="cancelState" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End state modal-->

