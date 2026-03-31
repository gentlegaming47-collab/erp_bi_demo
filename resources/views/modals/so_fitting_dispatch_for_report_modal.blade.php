<!--Start So Fitting For Report -->
<div aria-hidden="false" aria-labelledby="stateLabel" role="dialog" class="modal modal-wide over hide fade in" id="SoFittingForReportModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="stateModalLabel">SO Fitting Item</h3>
    </div>
    <div class="modal-body">
        <form id="addPendingSODForm" class="stdform" method="post">
            <input type="hidden" name="so_details_id" id="so_details_id">
            @csrf
           
            <table class="table table-bordered responsive table-autowidth" id="SoFittingForDispatchModalTable">
                <thead>
                <tr>   
                      
                    <th>Item</th>
                    <th>Code</th> 
                    <th>Group</th>     
                    <th>Unit</th>                    
                    <th>Pend. SO Qty.</th>  
                               
                </tr>
                </thead>
                <tbody>                
                </tbody>                
            </table>          
        </form>
    </div>

    <div class="modal-footer">
        <button data-dismiss="modal" id="cancelState" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End So Fitting For Report Modal -->

