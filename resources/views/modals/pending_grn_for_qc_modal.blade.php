<!--Start pending Purchase modal-->
<div aria-hidden="false" aria-labelledby="pendingPrLabel" role="dialog" class="modal modal-wide over modelWide hide fade" id="pendingGrnModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="pendingPrModalLabel">Select Pending GRN</h3>
    </div>

    
    <div class="modal-body">
                <form id="addPendingGRNForm" name="addPendingPRForm" class="stdform" method="post" enctype="multipart/form-data">
                    @csrf
                     <table class="table table-bordered responsive table-autowidth" id="pendingGRNDataTable">
                         <thead>
                         <tr>
                             <th><input type="hidden" name="checkall-pr_data" class="simple-check" id="checkall-pr_data"/></th>
                             <th>GRN No.</th>
                             <th>GRN Date</th>
                             <th>Supplier</th>
                             <th>PO No.</th>
                             <th>PO Date</th>
                             <th>Item</th>
                             {{-- <th>Item Detail Name</th> --}}
                             <th>Code</th>
                             <th>Group</th>
                             <th>Unit</th>
                             <th>GRN Qty.</th>
                             <th>Pend. QC Qty.</th>
                         </tr>
                         </thead>
                         <tbody>         
                         </tbody>
                     </table>
                 </form>
            </div>
          
      
    <div class="modal-footer">
        <button class="btn btn-primary" id="pendingGrnModal" type="submit" form="addPendingGRNForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End pending COA modal-->




