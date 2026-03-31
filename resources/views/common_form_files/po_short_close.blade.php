<div class="row">

                
      
    <div class="span-6">
      <div class="par control-group form-control">

        <label class="control-label" for="packing_dc_date">Short Close Date <sup class="astric">*</sup></label>
        <div class="controls">
        <span class="formwrapper">
          <input name="po_short_date" id="po_short_date" class="input-large trans-date-picker no-fill"/>
        </span>
        </div>
      </div>
    </div>
  </div>


<div class="divider15"></div>
<!--Second Section-->
<div class="row-fluid">
  <div class="widgetbox">
    <h4 class="widgettitle">PO Detail <sup class="astric">*</sup></h4>
    <div class="widgetcontent overflow-scroll">
      <table class="table table-bordered responsive table-autowidth remove-reset-filter" id="pendingPOTable">
        <thead>
          <tr class="main-header">
            <th><input type="checkbox" name="checkall-po" class="simple-check" id="checkall-po"/></th>
          
            <th>PO No.</th>                     
            <th>PO Date</th>
            <th>Supplier</th>
            <th>Ship To </th>
            <th>Item</th>
            <th>Code</th>
            <th>Group</th>
            <th>PO Qty. </th>
            <th>Pend. PO Qty. </th>
            <th>Unit</th>
            <th> Del. Date</th>
            <th>Short Close Qty. </th>
            <th>Reason</th>
          </tr>
        </thead>
        <tbody>
          <tr class="centeralign" id="noPendingDc">
            <td colspan="11">No Pending PO Details Available!</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!--END Second Section-->


<script type="text/javascript" src="{{ asset('js/view/po_short_close.js?ver='.getJsVersion()) }}"></script>
