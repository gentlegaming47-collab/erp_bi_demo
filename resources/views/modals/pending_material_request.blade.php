<!--Start taluka modal-->
<div aria-hidden="false" aria-labelledby="talukaLabel" role="dialog" class="modal modal-wide over hide fade in " id="pendingMaterialRequest">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="pendingMaterialRequestModalLabel">Select Pending Material Request</h3>
    </div>
    <div class="modal-body" id="pendingMaterialRequestBody">
        <form id="pendingMaterialRequestForm" class="stdform" method="post">
            @csrf
            <table class="table table-bordered responsive table-autowidth" id="pendingMaterialRequestTable">
                <thead>
                <tr>
                    <th></th>
                    <th>MR No.</th>
                    <th>MR Date </th> 
                    {{-- <th>Item  </th>
                    <th>Code   </th>
                    <th>Group    </th>
                    <th>MR Qty.</th>   
                    <th>Pending MR Qty.</th>                                     
                    <th>Unit</th>                                     
                    <th>Rate Per Unit</th> --}}
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
           
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="pendingMaterialRequestModal" type="submit" form="pendingMaterialRequestForm" tabindex="0">Add</button>
        <button data-dismiss="modal"  type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End city modal-->




