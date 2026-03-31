<?php

use App\Models\ItemStockTransfer;
if(isset($id)){
    $locationCode = getCurrentLocation();

    $changedItemIds = ItemStockTransfer::
    leftJoin('items', 'items.id', '=', 'item_stock_transfer.ist_item_id')
    ->where('item_stock_transfer.ist_id', base64_decode($id))
    ->where(function($query) {
        $query->where('items.status', 'deactive');         
    })
    ->pluck('item_stock_transfer.ist_item_id')
    ->toArray();

               
}else {  
    $changedItemIds = [];
}
?>   
   <input type="hidden" name="main_second_stock" id="main_second_stock"  />
      <div class="row">
                    <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for="ist_sequence">Sr. No. <sup class="astric">*</sup></label>
                                <div class="controls"> <span class="formwrapper">
                                    <input name="ist_sequence" id="ist_sequence" class="input-large only-numbers sequence" />
                                    <input name="ist_number" id="ist_number" class="input-large sequence-number" readonly />
                                    </span> 
                                </div>
                        </div>
                    </div>

                    <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for="ist_date">Date <sup class="astric">*</sup></label>
                                <div class="controls"> <span class="formwrapper">
                                    <input name="ist_date" id="ist_date" class="input-large trans-date-picker  no-fill" />
                                    </span> </div>
                        </div>
                    </div>

                    <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for="ist_item_id">Item</label>
                                <span class="formwrapper">
                                    <select name="ist_item_id" id="ist_item_id" class="chzn-select" onchange="getDetailsItems()">
                                        <option value="">Select Item</option>
                                            @forelse (getItemsForIST($changedItemIds) as $item)
                                            <option value="{{ $item->id }}">{{ $item->item_name}}</option>
                                            @empty
                                        @endforelse
                                    </select>

                                </span>
                        </div>
                    </div>

                     <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for="ist_item_details_id">Detail Item</label>
                                <span class="formwrapper">
                                    <select name="ist_item_details_id" id="ist_item_details_id" class="chzn-select" onchange="getDetailsExceptSelectedItems()">
                                        <option value="">Select Item</option>
                                    </select>

                                </span>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="item_stock">Stock </label>
                                <span class="formwrapper">
                                    <input type="text" name="item_stock" id="item_stock" class="form-control" autocomplete="nope" readonly tabindex="-1"/>
                                </span>
                        </div>
                    </div>

                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="main_unit">Unit </label>
                                <span class="formwrapper">
                                    <input type="text" name="main_unit" id="main_unit" class="form-control" autocomplete="nope" readonly tabindex="-1"/>
                                </span>
                        </div>
                    </div>
                </div>
                <div class="divider15"></div>


        <div class="widgetbox-inverse">
            <div class="headtitle">
                <h4 class="widgettitle">Item Stock Transfer Details <sup class="astric">*</sup></h4>
            </div>
            {{-- <div class="widgetcontent overflow-scroll"> --}}
            <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
                <table class="table table-bordered responsive" id="ist_table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Item </th>
                            <th>Qty. </th>
                            <th>Unit</th>

                        </tr>
                    </thead>
                    <tbody>                   

                    </tbody>
                    

                </table><br>
                @if(isset($id))   
                @else   
                <button class="btn btn-primary" id="addPart" type="button" onclick="addItemDetail()" disabled>Add</button>
                @endif
            </div>
        </div>


<script type="text/javascript" src="{{asset('js/view/item_stock_transfer.js?ver='.getJsVersion()) }}"></script>