<!--Start pending Purchase modal-->
<div aria-hidden="false" aria-labelledby="pendingDcLabel" role="dialog" class="modal modal-wide over hide fade grnmodal" id="pendingDcModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="pendingDcModalLabel">Select Pending Dispatch</h3>
    </div>

    
    <div class="modal-body">
        <form id="addPendingDcForm" name="addPendingDcForm" class="stdform" method="post" enctype="multipart/form-data">
           @csrf
            <table class="table table-bordered responsive table-autowidth" id="pendingDcTable">
                <thead>
                <tr>
                    {{-- <th><input type="checkbox" name="checkall-dc" class="simple-check" id="checkall-dc"/></th>
                    <th>MR No.</th>
                    <th>MR Date</th>
                    <th>Dispatch Plan No.</th>
                    <th>Dispatch Plan Date</th>  --}}
                    {{-- <th>Ship To </th> --}}
                    {{-- <th>Item </th>
                    <th>Code</th>
                    <th>Group</th>
                    <th>Plan Qty.</th>   
                    <th>Pend. Plan Qty.</th>   --}}
                    {{-- <th>Pending PO Qty.</th>   --}}
                    {{-- <th>Unit</th>  
                    <th>Transporter</th>  
                    <th>Vehicle No.</th>   --}}

                    <th></th>                  
                    <th>Dispatch Plan No.</th>
                    <th>Dispatch Plan Date</th>
                    <th>Vehicle No.</th>  
                    <th>Transporter</th>  
              
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="pendingDcModal" type="submit" form="addPendingDcForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End pending COA modal-->




