<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="mr_sequence">MR No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="mr_sequence" id="mr_sequence" class="input-large only-numbers sequence" />
                    <input name="mr_number" id="mr_number" class="input-large sequence-number" />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="mr_date">MR Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="mr_date" id="mr_date" class="input-large trans-date-picker  no-fill" />
                    </span> </div>
        </div>
    </div>
    
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="to_location_id">To Location <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <select name="to_location_id" id="to_location_id" class="chzn-select">
                            <option value="">Select To Location</option>
                                @forelse (getMaterialLocation() as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                @empty
                            @endforelse
                        </select>
                    </span>
            </div>
        </div>
    </div>

      <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="so_customer_id">Customer Group <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    <select name="customer_group_id" id="customer_group_id" class="chzn-select" onchange="getItemRateFromPriceList()">
                        <option value="">Select Customer Group</option>
                        @forelse (getSoCustomerGroup() as $customer_group)
                        <option value="{{ $customer_group->id }}">{{ $customer_group->customer_group_name }}</option>
                        @empty
                    @endforelse
                    </select>
                </span>
            </div>
        </div>
    </div>
</div> <!-- row end -->


    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Material Request Detail  <sup class="astric">*</sup></h4>
        </div>
        {{-- <div class="widgetcontent overflow-scroll"> --}}
        <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
            <table class="table table-bordered responsive" id="materialRequestTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item </th>
                        <th>Code</th>
                        {{-- <th>Group</th> --}}
                        <th>Stock</th>
                        <th>Unit</th>
                        <th>MR Qty.</th>                        
                        {{-- <th>Remarks</th> --}}
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="6" ></td>
                        <td class="materialrequestsum" name="item_production_total_qty"></td>
                        
                        <td class="amountsum" name="src_total_amount">                            
                        </tr>

                </tfoot>

            </table><br>
            <button class="btn btn-primary" id="addPart" type="button" onclick="addMaterialDetail()">Add</button>
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
    <?php

    // use App\Models\MaterialRequestDetail;
    // if(isset($id)){
    //     $locationCode = getCurrentLocation();
    
    //     $changedItemIds = MaterialRequestDetail::
    //     leftJoin('items', 'items.id', '=', 'material_request_details.item_id')
    //     ->where('material_request_details.mr_id', base64_decode($id))
    //     ->where(function($query) {
    //         $query->where('items.status', 'deactive')
    //             ->orWhere('items.service_item', 'Yes');
    //     })
    //     ->pluck('material_request_details.item_id')
    //     ->toArray();

                   
    // }else {  
    //     $changedItemIds = [];
    // }
    ?>
  

    {{-- <script>
        var getItem = [<?php //echo json_encode(noFittingItem($changedItemIds)); ?>];
        
         </script> --}}
<script type="text/javascript" src="{{ asset('js/view/material_request.js?ver='.getJsVersion()) }}"></script>
