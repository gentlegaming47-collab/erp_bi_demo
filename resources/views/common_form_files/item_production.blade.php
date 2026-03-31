<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="ip_sequence">IP. No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="ip_sequence" id="ip_sequence" class="input-large only-numbers sequence" />
                    <input name="ip_number" id="ip_number" class="input-large sequence-number" />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="ip_date">Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="ip_date" id="ip_date" class="input-large trans-date-picker  no-fill" />
                    </span> </div>
        </div>
    </div>
</div> <!-- row end -->


    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Item Production Detail <sup class="astric">*</sup></h4>
        </div>
        {{-- <div class="widgetcontent overflow-scroll"> --}}
        <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
            <table class="table table-bordered responsive" id="itemProductionTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item</th>
                        <th>Code</th>
                        <th>Group</th>
                        <th>Stock</th>
                        <th>Prod. Qty.</th>                        
                        <th>Unit.</th>                        
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="6" ></td>
                        <td class="itemproqtysum" name="item_production_total_qty"></td>
                        {{-- <td class="amountsum" name="src_total_amount">                            --}}
                             <td colspan="2" ></td>
                        </tr>

                </tfoot>

            </table><br>
            <button class="btn btn-primary" type="button" onclick="addItemProductionDetail()">Add</button>
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
    use App\Models\ItemProductionDetail;
    if(isset($id)){
        $locationCode = getCurrentLocation();
    
        $changedItemIds = ItemProductionDetail::
        leftJoin('items', 'items.id', '=', 'item_production_details.item_id')
        ->where('item_production_details.ip_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes')
                ->orWhere('items.secondary_unit', 'Yes')
                ->orWhere('items.own_manufacturing', 'No')
                ->orWhere('items.require_raw_material_mapping', 'Yes')
                ->orWhere('items.fitting_item', 'Yes');
        })
        ->pluck('item_production_details.item_id')
        ->toArray();

                   
    }else {  
        $changedItemIds = [];
    }
?>

  

    <script>
        var getItem = [<?php echo json_encode(getFittingMappingItemsForProduction($changedItemIds)); ?>];
        
         </script>
<script type="text/javascript" src="{{ asset('js/view/item_production.js?ver='.getJsVersion()) }}"></script>
