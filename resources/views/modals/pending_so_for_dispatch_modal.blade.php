<!--Start pending Purchase modal-->
<style>
 
    #pendingSODataTable_filter label{
      width: auto;
      white-space: nowrap;   
    } 
    #pendingSOTable_filter label{
      width: auto;
      white-space: nowrap;   
    } 
  
  #addPendingSODataForm .dataTables_filter{
      position: unset !important;
      padding-bottom : 45px !important;
  }
  #addPendingSOForm .dataTables_filter{
      position: unset !important;
      padding-bottom : 45px !important;
  }
  
  </style>
  <div aria-hidden="false" aria-labelledby="pendingPoLabel" role="dialog" class="modal modal-wide over modelWide hide fade" id="pendingSOModal">
      <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
          <h3 id="pendingPoModalLabel">Select Pending SO</h3>
      </div>
  
      
      <div class="modal-body">
          <div style="display: flex;">
              <div style="width:40%; overflow-x:scroll;">
                  <form id="addPendingSODataForm" name="addPendingPOForm" class="stdform" method="post" enctype="multipart/form-data">
                      @csrf
                       <table class="table table-bordered responsive table-autowidth remove-reset-filter" id="pendingSODataTable">
                           <thead>
                           <tr class="main-header">
                               <th><input type="checkbox" name="checkall-so_data" class="simple-check" id="checkall-so_data"/></th>
                               <th>SO No.</th>
                               <th>SO Date</th>
                               <th>Customer/Location</th>     
                               <th>Customer Group</th>     
                               <th>Village</th>
                               <th>District</th>
                               <th>Dealer </th>                        
                               <th>Sp. Note</th>                        
                           </tr>
                           </thead>
                           <tbody>         
                           </tbody>
                       </table>
                   </form>
              </div>
              <div style="width:1%;"></div>
              <div style="width:59%; overflow-x:scroll;"> 
                  <form id="addPendingSOForm" name="addPendingPOForm" class="stdform" method="post" enctype="multipart/form-data">
                      @csrf
                       <table class="table table-bordered responsive table-autowidth remove-reset-filter" id="pendingSOTable" style="width:100%;">
                           <thead>
                           <tr>
                               <th style="display: none;"><input type="hidden" name="checkall-so" class="simple-check" id="checkall-so"/></th>              
                               <th>Item </th>
                               <th>Code</th>
                               <th>Group</th>
                               <th>Unit</th>
                               <th>SO Qty.</th>
                               <th>Pend. SO Qty.</th>
                               <th>Stock</th>
                               <th>Remark</th>
                           </tr>
                           </thead>
                           <tbody>
                              <tr class="centeralign" id="noPendingPo">
                                  <td colspan="9" id="itemloader">No record found!</td>
                              </tr>
           
                           </tbody>
                       </table>
                   </form>
              </div>
          </div>        
      </div>
      <div class="modal-footer">
          <button class="btn btn-primary" id="pendingBtnSOModal" type="submit" form="addPendingSOForm" tabindex="0">Add</button>
          <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
      </div>
  </div>
  <!--End pending COA modal-->
  
  
  
  
  