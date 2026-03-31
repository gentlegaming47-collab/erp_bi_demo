<!--Start Sales Order modal-->
<div aria-hidden="false" aria-labelledby="stateLabel" role="dialog" class="modal modal-wide over hide fade in" id="SoAssemblyForDispatchModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="stateModalLabel">Mapping Item</h3>
    </div>
    <div class="modal-body">
        <form id="addPendingSODAssemblyForm" class="stdform" method="post">
            <input type="hidden" name="so_details_id" id="so_details_id">
            <input type="hidden" name="pend_assembly_so_qty" id="pend_assembly_so_qty">
            @csrf
           
            <table class="table table-bordered responsive table-autowidth" id="SoAssemblyForDispatchModalTable">
                <thead>
                <tr>   
                    <th><input type="checkbox" name="checkall-sod_assem_data" class="simple-check" id="checkall-sod_assem_data"/></th>     
                    <th>Item</th>
                    <th>Code</th> 
                    <th>Group</th>     
                    <th>Unit</th>                    
                    <th>Pend. SO Qty.</th>  
                    <th>Stock</th>                  
                    <th>Plan Qty.</th>                  
                </tr>
                </thead>
                <tbody>                
                </tbody>                
            </table>          
        </form>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary" id="addPendingSODAssemblyForm" type="submit" form="addPendingSODAssemblyForm" tabindex="0">Add</button>
        <button data-dismiss="modal" id="cancelState" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End state modal-->

