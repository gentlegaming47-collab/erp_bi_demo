<!--Start pending Purchase modal-->
<div aria-hidden="false" aria-labelledby="pendingPrLabel" role="dialog" class="modal modal-wide over modelWide hide fade" id="pendingPrModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="pendingPrModalLabel">Select Pending PR</h3>
    </div>

    
    <div class="modal-body">
        <div style="display: flex;">
            <div style="width:35%;">
                <form id="addPendingPRDataForm" name="addPendingPRForm" class="stdform" method="post" enctype="multipart/form-data">
                    @csrf
                     <table class="table table-bordered responsive table-autowidth" id="pendingPRDataTable">
                         <thead>
                         <tr>
                             <th><input type="checkbox" name="checkall-pr_data" class="simple-check" id="checkall-pr_data"/></th>
                             <th>PR No.</th>
                             <th>PR Date</th>
                             <th>From Type</th>
                             <th>Location</th>
                         </tr>
                         </thead>
                         <tbody>         
                         </tbody>
                     </table>
                 </form>
            </div>
            <div style="width:1%;"></div>
            <div style="width:65%;"> 
                <form id="addPendingPRForm" name="addPendingPRForm" class="stdform" method="post" enctype="multipart/form-data">
                    @csrf
                     <table class="table table-bordered responsive table-autowidth" id="pendingPRTable">
                         <thead>
                         <tr>
                             <th><input type="checkbox" name="checkall-pr" class="simple-check" id="checkall-pr"/></th>                           
                             <th>Item </th>
                             <th>Code</th>
                             <th>Pend. Req. Qty.</th>   
                             <th>Unit</th>  
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
        <button class="btn btn-primary" id="pendingPrModal" type="submit" form="addPendingPRForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End pending COA modal-->




