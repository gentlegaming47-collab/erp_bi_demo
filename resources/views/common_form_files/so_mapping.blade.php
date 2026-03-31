<input type="hidden" name="cre_detail_id" id="cre_detail_id">
<div class="row"> 

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="mapping_number">Sr. No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="so_mapping_sequence" id="so_mapping_sequence" class="input-large only-numbers sequence"/>
                    <input name="so_mapping_number" id="so_mapping_number" class="input-large sequence-number"/>
                    </span> 
                </div>
        </div>
    </div>


    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="mapping_date">Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="so_mapping_date" id="so_mapping_date" class="input-large trans-date-picker no-fill" />
                    </span> </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="customer_id">Customer <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <select name="customer" id="customer" class="chzn-select" onchange="fillPendingMapping()">
                            <option value="">Select Customer</option>
                               
                        </select>
                    </span>
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for=""></label>
                <div class="controls">
                     <span class="formwrapper">
                        <button class="btn btn-primary toggleModalBtn" type="button" data-target="#customerReplacementModal" data-toggle="modal" disabled>Pending</button>
                    </span> 
                </div>
        </div>
    </div>
</div> <!-- row end -->

<div class="row">

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="mapping_item_id">Item<sup class="astric">*</sup></label>
                <div class="controls">
                     <span class="formwrapper">
                        <select name="mapping_item_id" id="mapping_item_id" class="chzn-select" >
                            <option value="">Select Item</option>                          
                        </select>
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6" id="hide_details">
        <div class="par control-group form-control">
            <label class="control-label" for="mapping_item_details_id">Item Detail Name<sup class="astric">*</sup></label>
                <div class="controls">
                     <span class="formwrapper">
                        <input name="mapping_item_details_name" id="mapping_item_details_name" class="form-control" readonly/>
                        <input type="hidden" name="mapping_item_details_id" id="mapping_item_details_id" class="form-control"/>
                        <input type="hidden" name="secondary_qty" id="secondary_qty" class="form-control"/>
                    </span> 
                </div>
        </div>
    </div>
    
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="pend_return_qty">Pend. Return Qty.</label>
                <div class="controls"> <span class="formwrapper">
                     <input name="pend_return_qty" id="pend_return_qty" class="form-control" readonly/>
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="mapping_unit">Unit</label>
                <div class="controls"> <span class="formwrapper">
                     <input name="mapping_unit" id="mapping_unit" class="form-control" readonly/>
                    </span> 
                </div>
        </div>
    </div>

    

</div>



    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Sales Order Detail<sup class="astric">*</sup></h4>
        </div>
        <div class="widgetcontent overflow-scroll">
            <table class="table table-bordered responsive" id="SOMappingTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="checkall-so" class="simple-check" id="checkall-so"/></th>
                        <th>SO No.</th>
                        <th>SO Date</th>
                        <th>SO Qty.</th>
                        <th>Pend. SO Qty.</th>
                        <th>Item Detail Qty.</th>
                        <th>Map Qty.</th>                       
                        <th>Unit</th>
                      
                    </tr>
                </thead>
                <tbody>
                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="6" ></td>
                        <td class="mappqtysum" name="mapp_total_qty"></td>
                        <td></td>
                    </tr>
                </tfoot>

            </table>
           
        </div>
    </div>

   

    <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="sp_notes">Sp. Note </label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input type="text" name="sp_notes" id="sp_notes" class="form-control" placeholder="Enter Sp. Note"  />
                        </span> 
                    </div>
            </div>
        </div>
    </div>
<?php
    use App\Models\SOMapping;
    if(isset($id)){
        $locationCode = getCurrentLocation();
    
        $changedItemIds = SOMapping::
        leftJoin('items', 'items.id', '=', 'so_mapping.item_id')
        ->where('so_mapping.mapping_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes');
        })
        ->pluck('so_mapping.item_id')
        ->toArray();

                   
    }else {  
        $changedItemIds = [];
    }
?>    

<script>
var getItem = [<?php echo json_encode(getFittingItem($changedItemIds)); ?>];
 </script>
<script type="text/javascript" src="{{ asset('js/view/so_mapping.js?ver='.getJsVersion()) }}"></script>
