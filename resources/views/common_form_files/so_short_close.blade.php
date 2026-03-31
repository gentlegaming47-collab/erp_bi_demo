<div class="row">

                
      
    <div class="span-6">
      <div class="par control-group form-control">

        <label class="control-label" for="packing_dc_date">Short Close Date <sup class="astric">*</sup></label>
        <div class="controls">
        <span class="formwrapper">
          <input name="so_short_date" id="so_short_date" class="input-large trans-date-picker no-fill"/>
        </span>
        </div>
      </div>
    </div>
  </div>


<div class="divider15"></div>
<!--Second Section-->
<div class="row-fluid">
  <div class="widgetbox">
    <h4 class="widgettitle">Customer Replacement SO Detail <sup class="astric">*</sup></h4>
    {{-- <div class="widgetcontent overflow-scroll"> --}}
    <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
      <table class="table table-bordered responsive table-autowidth remove-reset-filter" id="pendingSOTable">
        <thead>
          <tr>
            <th><input type="checkbox" name="checkall-so" class="simple-check" id="checkall-so"/></th>
          
            <th>SO No.</th>                     
            <th>SO Date</th>
            <th>Customer</th>
            <th>Reg No.</th>
            <th>Item</th>
            <th>Code</th>
            <th>Group</th>
            <th>SO Qty. </th>
            <th>Pend. SO  map Qty. </th>
            <th>Unit</th>
            <th>Short Close Qty. </th>
            <th>Reason</th>
          </tr>
        </thead>
        <tbody>
          <tr class="centeralign" id="noPendingDc">
            <td colspan="15">No Pending Customer Replacement SO Details Available!</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!--END Second Section-->


<script type="text/javascript" src="{{ asset('js/view/so_short_close.js?ver='.getJsVersion()) }}"></script>
