<!--Start Sales Order modal-->
<div aria-hidden="false" aria-labelledby="stateLabel" role="dialog" class="modal modal-wide over-over hide fade in change_state_modal materialDialog" id="previousSODetailModel">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="stateModalLabel">Add Previous Sales Order Detail</h3>
    </div>
    <div class="modal-body">
        <form id="oldItemSalesOrderForm" class="stdform" method="post">
            @csrf
           
            <table class="table table-bordered responsive table-autowidth" id="soItemModalTable">
                <thead>
                <tr>                                
                    <th><input type="checkbox" name="checkall-item" class="simple-check" id="checkall-item"/></th>                 
                    <th>Item</th>
                    <th>Code</th>                    
                    <th>Group</th>                    
                    <th>SO Qty.</th>                    
                    <th>Unit</th>                    
                    <th>Rate/Unit</th>                    
                </tr>
                </thead>
                <tbody>
                
                </tbody>
              
            </table>          
        </form>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary" id="previousSODetailModel" type="submit" form="oldItemSalesOrderForm" tabindex="0">Add</button>
        <button data-dismiss="modal" id="cancelState" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End state modal-->

