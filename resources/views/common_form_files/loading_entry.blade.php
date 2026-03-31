<input type="hidden" name="Dpid" id="Dpid" class="input-large"/>
<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label"  for="dp_sequence">Dispatch Plan No. </label>
                <div class="controls"> <span class="formwrapper">
                    {{-- <input name="dp_sequence" id="dp_sequence" class="input-large only-numbers sequence" /> --}}
                    <input name="dp_number" id="dp_number" class="input-large"/>
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="dp_date">Dispatch Plan Date </label>
                <div class="controls"> <span class="formwrapper">
                     <input name="dp_date" id="dp_date" class="input-large date-picker  no-fill" />
                    </span>
                 </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="dp_date">Vehicle No. </label>
                <div class="controls"> <span class="formwrapper">
                     <input name="vehicle_no" id="vehicle_no" class="input-large" />
                    </span>
                 </div>
        </div>
    </div>

     <div class="span-6">
            <div class="par control-group form-control">
                    <label class="control-label" for="transporter">Transporter </label>
                        <div class="controls">
                                <span class="formwrapper">
                                    <select name="transporter" id="transporter" class="chzn-select">
                                        <option value="">Select Transporter</option>
                                        @forelse ($getTransporter as $transporter)
                                        {{-- @forelse (getTransporter() as $transporter) --}}
                                        <option value="{{ $transporter->id }}">{{ $transporter->transporter_name}}</option>
                                        @empty
                                    @endforelse
                                    </select>
                                </span>
                        </div>
            </div>
    </div>
    
</div> <!-- 1 row end -->

<div class="row">
   
     <div class="span-6">
        <div class="par control-group form-control">
            <label  class="control-label" for="dp_date">Loading By </label>
                <div class="controls"> <span class="formwrapper">
                     <input name="loading_by" id="loading_by" class="input-large" />
                    </span>
                 </div>
        </div>
    </div>

     <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="dp_date">Driver Name </label>
                <div class="controls"> <span class="formwrapper">
                     <input name="driver_name" id="driver_name" class="input-large" />
                    </span>
                 </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="dp_date">Driver Mobile No. </label>
                <div class="controls"> <span class="formwrapper">
                     <input name="driver_no" id="driver_no" class="input-large only-numbers" />
                    </span>
                 </div>
        </div>
    </div>

    <div class="span-6" id="btn_hide">
        <div class="par control-group form-control">
            <label class="control-label" for="toggle_btn">&nbsp;</label>
            <div class="controls"> <span class="formwrapper input-large">
                  <button class="btn btn-primary toggleModalBtn" type="button" data-target="#pendingDispatchPlan" data-toggle="modal" disabled>Pending</button>
            </div>
        </div>
    </div>

</div> <!-- 2 row end -->

     <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Loading Entry Detail<sup class="astric">*</sup></h4>
        </div>
        <div class="widgetcontent overflow-scroll">
        {{-- <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;"> --}}
            <table class="table table-bordered responsive" id="LoadingEntryTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>SO No.</th>
                        <th>SO Date</th>
                        <th>Customer/Location</th>
                        <th>Village</th>
                        <th>District</th>
                        <th>Dealer </th>
                        <th>Item </th>
                        <th>Code</th>
                        <th>Group</th>
                        <th>Plan Qty.</th>                        
                        <th>Unit</th>
                        <th>Pend. SO Qty.</th>
                        
                    </tr>
                </thead>
                <tbody>                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="11" ></td>
                        <td class="planqtysum" name="plan_total_qty"></td>
                        <td></td>
                        <td></td>                    
                    </tr>

                </tfoot>

            </table><br>
            {{-- <button class="btn btn-primary" type="button" onclick="addLoadingEntryDetail()" disabled>Add</button> --}}
        </div>
    </div>

    {{-- <div class="row">
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
      
    </div> --}}

    <?php

    use App\Models\LoadingEntryDetails;
    if(isset($id)){
        $locationCode = getCurrentLocation();
    
        $changedItemIds = LoadingEntryDetails::
        leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id', 'loading_entry_details.dp_details_id')
        ->leftJoin('items', 'items.id', '=', 'dispatch_plan_details.item_id')
        ->where('loading_entry_details.le_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes');
        })
        ->pluck('dispatch_plan_details.item_id')
        ->toArray();

                   
    }else {  
        $changedItemIds = [];
    }
    ?>

   <script>
        var getItem = [<?php echo json_encode(noFittingItem($changedItemIds)); ?>];
        
         </script>
<script type="text/javascript" src="{{ asset('js/view/loading_entry.js?ver='.getJsVersion()) }}"></script>
  

  
