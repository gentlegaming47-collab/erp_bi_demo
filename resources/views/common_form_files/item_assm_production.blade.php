<?php

use App\Models\ItemAssemblyProduction;
if(isset($id)){
    $locationCode = getCurrentLocation();

    $changedItemIds = ItemAssemblyProduction::
    leftJoin('items', 'items.id', '=', 'item_assembly_production.item_id')
    ->where('item_assembly_production.iap_id', base64_decode($id))
    ->where(function($query) {
        $query->where('items.status', 'deactive')
            ->orWhere('items.service_item', 'Yes')
            ->orWhere('items.require_raw_material_mapping','No');
    })
    ->pluck('item_assembly_production.item_id')
    ->toArray();

               
}else {  
    $changedItemIds = [];
}
?>

<input type="hidden" name="pre_iap_item" id="pre_iap_item" value="0">
<input type="hidden" name="pre_item_details_id" id="pre_item_details_id" value="0">
<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="iap_sequence">IAP. No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="iap_sequence" id="iap_sequence" class="input-large only-numbers sequence" />
                    <input name="iap_number" id="iap_number" class="input-large sequence-number" />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="iap_date">Date <sup class="astric">*</sup></label>
                    <div class="controls"> <span class="formwrapper">
                         <input name="iap_date" id="iap_date" class="input-large trans-date-picker no-fill" />
                        </span> </div>
            </div>
    </div>
        
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="item_id">Item Name <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <select name="iap_item_id" id="iap_item_id" class="chzn-select" onchange="getSecondUnit()">
                            <option value="">Select Item</option>
                                @forelse (getFittingMappingItems($changedItemIds) as $item_data)
                                    <option value="{{ $item_data->id }}" data-secondary_unit="{{$item_data->secondary_unit}}">{{ $item_data->item_name}}</option>
                                    @empty
                                @endforelse
                        </select>
                    </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="hide">
        <div class="par control-group form-control">
                <label class="control-label" for="item_name">Item Detail Name <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <select name="item_details_id" id="item_details_id" class="chzn-select">
                            <option value="">Select Item Name</option>  
                        </select>
                    </span>
                {{-- <div id="item_name_list" class="suggestion_list" ></div> --}}
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="item_code">Item Code <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="item_code" id="item_code" class="input-large" ></span> 
                </div>
        </div>
    </div>



</div> <!-- row end -->

<div class="row"> 
   
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="assembly_qty">Ass. Qty. <sup class="astric">*</sup></label>
            {{-- <label class="control-label" for="assembly_qty">Maximum Possible Ass. Qty. <sup class="astric">*</sup></label> --}}
            {{-- <label class="control-label" for="assembly_qty">Access Quantity <sup class="astric">*</sup></label> --}}
                <div class="controls"> <span class="formwrapper">
                <input type="text" name="assembly_qty" id="assembly_qty" class="input-large"  tabindex="1" placeholder="Enter Ass. Qty." />                   
                {{-- <input type="text" name="assembly_qty" id="assembly_qty" class="input-large"   onblur="formatPoints(this,3)" tabindex="1" placeholder="Enter Ass. Qty." />                    --}}
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="item_unit">Item Unit </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="item_unit" id="item_unit" class="input-large" /></span> 
                </div>
        </div>
    </div>


    <div class="span-6">
        <div class="par control-group form-control">
            {{-- <label class="control-label minQtyStyle" for="assembly_qty">Mininum qty is </label> --}}
            <label class="control-label minQtyStyle" for="assembly_qty">Max. Possible Qty. </label>
                <div class="controls"> 
                     <span class="formwrapper">                       
                        <input type="text" style="color:red;" id="mininum_qty" class="input-large" />
                    </span> 
                </div>
        </div>
    </div>
</div> <!-- row end -->



    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Item Production Detail<sup class="astric">*</sup></h4>
        </div>
        {{-- <div class="widgetcontent overflow-scroll"> --}}
        <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
            <table class="table table-bordered responsive" id="itemAssmProductionTable">
                <thead>
                    <tr>                        
                        <th>Sr. No.</th>
                        <th>Item</th>
                        <th>Code</th>
                        <th>Group</th>
                        <th>Mapped Qty. </th>
                        <th>Stock</th>
                        <th>Consumption</th>                        
                        <th>Unit</th>
                        {{-- <th>Unit</th> --}}
                    </tr>
                </thead>
                <tbody>                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="4" ></td>
                        <td class="item_pro_assm_qtysum" name="item_production_total_qty"></td>
                        <td colspan="3"></td>                                                
                              
                        </tr>

                </tfoot>

            </table><br>
            {{-- <button class="btn btn-primary" type="button" onclick="addItemAPDetails()">Add</button> --}}
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

  



<script type="text/javascript" src="{{ asset('js/view/item_assm_production.js?ver='.getJsVersion()) }}"></script>
