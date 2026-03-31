<!--Start pending Purchase modal-->
<div aria-hidden="false" aria-labelledby="pendingPoLabel" role="dialog" class="modal modal-wide over modelWide hide fade" id="pendingPoModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="pendingPoModalLabel">Select Pending PO</h3>
    </div>

    
    <div class="modal-body">
        <div style="display: flex;">
            <div style="width:25%;">
                <form id="addPendingPODataForm" name="addPendingPOForm" class="stdform" method="post" enctype="multipart/form-data">
                    @csrf
                     <table class="table table-bordered responsive table-autowidth" id="pendingPODataTable">
                         <thead>
                         <tr>
                             <th><input type="checkbox" name="checkall-po_data" class="simple-check" id="checkall-po_data"/></th>
                             <th>PO No.</th>
                             <th>PO Date</th>
                             <th>Ship To </th>                             
                         </tr>
                         </thead>
                         <tbody>         
                         </tbody>
                     </table>
                 </form>
            </div>
            <div style="width:1%;"></div>
            <div style="width:75%;"> 
                <form id="addPendingPOForm" name="addPendingPOForm" class="stdform" method="post" enctype="multipart/form-data">
                    @csrf
                     <table class="table table-bordered responsive table-autowidth" id="pendingPOTable">
                         <thead>
                         <tr>
                             <th><input type="checkbox" name="checkall-po" class="simple-check" id="checkall-po"/></th>                           
                             <th>Item </th>
                             <th>Code</th>
                             <th>Group</th>
                             <th>PO Qty.</th>   
                             <th>Pend. PO Qty.</th>  
                             <th>Unit</th>  
                             <th>Del. Date</th>
                         </tr>
                         </thead>
                         <tbody>
                            <tr class="centeralign" id="noPendingPo">
                                <td colspan="8">No record found!</td>
                            </tr>
         
                         </tbody>
                     </table>
                 </form>
            </div>
        </div>        
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="pendingPoModal" type="submit" form="addPendingPOForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End pending COA modal-->




