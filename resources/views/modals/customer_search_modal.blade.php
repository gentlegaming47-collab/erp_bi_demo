<!--Start pending Purchase modal-->
<style>
  #searchCustomerTable_filter label{
    width: auto;
    white-space: nowrap;
    padding: 0;
  }

  #searchCustomerTable_length label{
    width: 0;
    white-space: nowrap;
    float: none;
    text-align: unset;
    padding: 0;
  }
  </style>
<div aria-hidden="false" aria-labelledby="pendingDcLabel" role="dialog" class="modal modal-wide over hide fade grnmodal" id="custSearchModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="custSearchModalLabel">Select Customer</h3>
    </div>

    
    <div class="modal-body">
        <form id="searchCustomer" name="searchCustomer" class="stdform" method="post">
           @csrf
            <table class="table table-bordered responsive table-autowidth remove-reset-filter" id="searchCustomerTable">
                <thead>
                 {{-- <input type="text" id="customerSearchInput" placeholder="Search for customers..."> --}}
                <tr>
                    <th><input type="hidden" name="checkall-dc" class="simple-check" id="checkall-dc"/></th>
                    <th>Customer</th>
                    <th>Reg.No.</th>
                    <th>Village</th>
                    <th>Pincode</th>  
                    <th>Taluka</th>
                    <th>District</th>
                    <th>State </th>
                    <th>Country</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="custSearchModal" type="submit" form="searchCustomer" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End pending COA modal-->




