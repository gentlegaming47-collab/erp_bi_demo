<div class="row">

                
      
    <div class="span-6">
      <div class="par control-group form-control">

        <label class="control-label" for="packing_dc_date">Short Close Date <sup class="astric">*</sup></label>
        <div class="controls">
        <span class="formwrapper">
          <input name="pr_short_date" id="pr_short_date" class="input-large trans-date-picker no-fill"/>
        </span>
        </div>
      </div>
    </div>
  </div>


<div class="divider15"></div>
<!--Second Section-->
<div class="row-fluid">
  <div class="widgetbox">
    <h4 class="widgettitle">Purchase Requisition Detail <sup class="astric">*</sup></h4>
    <div class="widgetcontent overflow-scroll">
      <table class="table table-bordered responsive table-autowidth remove-reset-filter" id="pendingPRTable">
        <thead>
          <tr class="main-header">
            <th><input type="checkbox" name="checkall-po" class="simple-check" id="checkall-pr"/></th>
          
            <th>PR. No.</th>                     
            <th>PR. Date</th>
            <th>PR From</th>
            <th>Supplier</th>
            <th>Location</th>
            <th>Item</th>
            <th>Code</th>
            <th>Group</th>
            <th>PR. Qty.</th>
            <th>Pend. PR. Qty.</th>
            <th>Unit</th>
            <th>Short Close Qty.</th>
            <th>Reason</th>
          </tr>
        </thead>
        <tbody>
          <tr class="centeralign" id="noPendingDc">
            <td colspan="13">No Pending PR. Details Available!</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!--END Second Section-->


<script type="text/javascript" src="{{ asset('js/view/pr_short_close.js?ver='.getJsVersion()) }}"></script>
