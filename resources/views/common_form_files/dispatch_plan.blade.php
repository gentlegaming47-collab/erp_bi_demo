<input type="hidden"  name="old_dispatch_from_id_fix" id="old_dispatch_from_id_fix"/> 
<div class="row">                    
    <div class="span-6" id="radio_so">
        <div class="par control-group form-control">
           <label class="control-label"></label>               
                <input type="radio" name="dispatch_from_id_fix" value="1" checked onchange="dispatchType()"/> Subsidy &nbsp; + &nbsp; Cash & Carry  &nbsp; 
                {{-- <input type="radio" name="dispatch_from_id_fix" value="2"  onchange="dispatchType()"/> Cash & Carry &nbsp; --}}
                <input type="radio"  name="dispatch_from_id_fix" value="3" onchange="dispatchType()"/> Location          
        </div>
    </div> 
</div>
<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="dp_sequence">Dispatch Plan No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="dp_sequence" id="dp_sequence" class="input-large only-numbers sequence" />
                    <input name="dp_number" id="dp_number" class="input-large sequence-number" />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="dp_date">Dispatch Plan Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="dp_date" id="dp_date" class="input-large trans-date-picker  no-fill" />
                    </span>
                 </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            {{-- <label class="control-label" for="toggle_btn">&nbsp;</label> --}}
            <div class="controls"> <span class="formwrapper input-large">
                <button class="btn btn-primary toggleModalBtn" type="button" data-target="#pendingSOModal" data-toggle="modal" disabled>Pending</button>
            </div>
            @if(Auth::user()->allow_multiple_veh_entry == 'Yes' || Auth::user()->user_name == 'admin') 
            <div class="controls" id="btn_hide">
                <span class="formwrapper">
                <input type="checkbox" name="multiple_loading_entry" id="multiple_loading_entry" value="Yes" onchange="checkCustomerData()"/> Allow Multiple Veh. Entry
                </span>
            </div>
            @endif
        </div>
    </div>
   

</div> <!-- row end -->


     <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Dispatch Plan Detail<sup class="astric">*</sup></h4>
        </div>
        <div class="widgetcontent overflow-scroll">
        {{-- <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;"> --}}
            <table class="table table-bordered responsive" id="DipatchPlanTable">
                <thead>
                    <tr>
                        {{-- <th><input type="checkbox" name="checkall-po" class="simple-check" id="checkall-po"/></th> --}}
                        <th>Action</th>
                        <th>SO No.</th>
                        <th>SO Date</th>
                        <th>Customer/Location</th>
                        <th>Customer Group</th> 
                        <th>Village</th>
                        <th>District</th>
                        <th>Dealer </th>
                        <th>Item </th>
                        <th>Code</th>
                        <th>Group</th>
                        <th>Unit</th>
                        <th>Pend. SO Qty.</th>
                        <th>Stock</th>
                        <th>Plan Qty.</th>                        
                        <th>Total Wt./Pc.</th>                        
                        {{-- <th>Total Wt.</th>                         --}}
                        
                    </tr>
                </thead>
                <tbody>                   
                    <tr class="centeralign" id="noSpoPart">
                        <td colspan="18">No Dispatch Plan Detail Added</td>
                      </tr>
                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="15" ></td>
                        <td class="total_wt_pc" name="total_wt_pc"></td>
                              
                        </tr>

                </tfoot>

            </table><br>
        </div>
    </div>

    <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="sp_notes">Sp. Note </label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input type="text" name="special_notes" id="special_notes" class="form-control" placeholder="Enter Sp. Note"  />
                        </span>
                    </div>
            </div>
        </div>
      
    </div>

  

        
<script type="text/javascript" src="{{ asset('js/view/dispatch_plan.js?ver='.getJsVersion()) }}"></script>
