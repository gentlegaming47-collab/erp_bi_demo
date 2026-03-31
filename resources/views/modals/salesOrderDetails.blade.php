
<!-- Start Country Modal -->




<!--Start Sales Order modal-->
<div aria-hidden="false" aria-labelledby="stateLabel" role="dialog" class="modal modal-wide salesOrderModal  over-over hide fade in change_state_modal materialDialog" id="salesOrderModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="stateModalLabel">Add Sales Order Detail</h3>
    </div>
    <div class="modal-body" style="overflow-y:auto !important; padding-bottom: 250px !important; max-height: calc(100vh - 400px) !important;">
        <form id="commonSalesOrderForm" class="stdform" method="post">
            <input type="hidden" name="mainItemId" id="mainItemId">
            @csrf
           
            <table class="table table-bordered responsive table-autowidth" id="soPartModalTable">
                <thead>
                <tr>                                
                    <th>Action</th>
                    <th>Sr. No.</th>
                    <th>Item</th>
                    <th>Code</th>                    
                    {{-- <th>Group</th>                     --}}
                    <th>SO Qty.</th>                    
                    <th>Unit</th>                    
                </tr>
                </thead>
                <tbody>
                
                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="4"></td>
                        <td class="soqtysum_second"></td>
                        <td></td>
                    </tr>
                       
                </tfoot>
            </table>
            <button class="btn btn-primary" type="button" onclick="addModalPartDetail()">Add</button>
        </form>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary" id="salesOrderModal" type="submit" form="commonSalesOrderForm" tabindex="0">Add</button>
        <button data-dismiss="modal" id="cancelState" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End state modal-->

