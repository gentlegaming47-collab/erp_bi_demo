<div class="row">

                
      
    <div class="span-6">
      <div class="par control-group form-control">

        <label class="control-label" for="packing_dc_date">Short Close Date <sup class="astric">*</sup></label>
        <div class="controls">
        <span class="formwrapper">
          <input name="tr_sc_date" id="tr_sc_date" class="input-large trans-date-picker no-fill"/>
        </span>
        </div>
      </div>
    </div>
  </div>


<div class="divider15"></div>
<div class="row-fluid">
  <div class="widgetbox">
    <h4 class="widgettitle">SO Detail <sup class="astric">*</sup></h4>
    <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
      <table class="table table-bordered responsive table-autowidth remove-reset-filter" id="dyntable">
        <thead>
          <tr class="main-header">
            <th><input type="checkbox" name="checkall-so" class="simple-check" id="checkall-so"/></th>          
            <th>SO No.</th>                     
            <th>SO Date</th>
            <th>SO From</th>
            <th>SO Type</th>
            <th>Customer Group</th>
            <th>Customer/Location</th>
            <th>Item</th>
            <th>Code</th>
            <th>Group</th>
            <th>SO Qty. </th>
            <th>Pend. SO Qty.</th>
            <th>Unit</th>
            <th>Short Close Qty. </th>
            <th>Reason</th>
          </tr>
        </thead>
        <tbody>
          {{-- <tr class="centeralign" id="noPendingDc">
            <td colspan="15">No Pending SO Details Available!</td>
          </tr> --}}
        </tbody>
      </table>
    </div>
  </div>
</div>
<!--END Second Section-->


<script type="text/javascript" src="{{ asset('js/view/transaction_so_short_close.js?ver='.getJsVersion()) }}"></script>
