<?php

use App\Models\PurchaseRequisitionDetails;
if(isset($id)){
    $locationCode = getCurrentLocation();

    $changedItemIds = PurchaseRequisitionDetails::
    leftJoin('items', 'items.id', '=', 'purchase_requisition_details.item_id')
    ->leftJoin('location_stock', function ($join) use ($locationCode) {
        $join->on('location_stock.item_id', '=', 'items.id')
             ->where('location_stock.location_id', '=', $locationCode->id); 
    })
    ->where('purchase_requisition_details.pr_id', base64_decode($id))
    ->where(function($query) {
        $query->where(function($q){
            $q->where('items.dont_allow_req_msl', '=', 'No')
            ->where('items.max_stock_qty','>',' IFNULL(location_stock.stock_qty, 0)');
            // $q->where('items.dont_allow_req_msl', '=', 'Yes')
            // ->where('items.min_stock_qty','>',' IFNULL(location_stock.stock_qty, 0)');
        })
        ->orWhere(function($q){
            $q->where('items.status', '=', 'deactive');
        })
        ->Where(function($q){
            $q->where('items.secondary_unit', '=', 'No');
        });
    })
    ->pluck('purchase_requisition_details.item_id')
    ->toArray();

    $supplierIds = PurchaseRequisitionDetails:: 
    leftJoin('suppliers', 'suppliers.id', '=', 'purchase_requisition_details.supplier_id')
    ->where('purchase_requisition_details.pr_id', base64_decode($id))
    ->where('suppliers.status','!=','active')
    ->pluck('purchase_requisition_details.supplier_id')
    ->toArray();
               
}else {  
    $changedItemIds = [];

    $supplierIds = [];
}
?>

<input type="hidden" value="purchaseRequisition" name="hidViewPage" id="hidViewPage"/>

<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
           <label class="control-label"></label>
                <input type="radio" class="pr_form_id_fix" name="pr_form_id_fix" value="1" onchange="prTypeFix()" checked/> Manual 
                <input type="radio" class="pr_form_id_fix" name="pr_form_id_fix" value="2" onchange="prTypeFix()" /> From Location &nbsp;
        </div>
    </div> 
</div>
<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="pr_sequence">PR No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="pr_sequence" id="pr_sequence" class="input-large only-numbers sequence" />
                    <input name="pr_number" id="pr_number" class="input-large sequence-number" />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="pr_date">PR Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="pr_date" id="pr_date" class="input-large trans-date-picker  no-fill" />
                    </span> </div>
        </div>
    </div>

    <div class="span-6" id="show">
        <div class="par control-group form-control">
                <label class="control-label" for="supplier_id">Supplier <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                            <select name="supplier_id" id="supplier_id" class="chzn-select" onchange="getItemsfromMapping()">
                            <option value="">Select Supplier</option>
                                @forelse (getSuppliers($supplierIds) as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name}}</option>
                                @empty
                            @endforelse
                        </select>
                    </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="hide">
        <div class="par control-group form-control">
                <label class="control-label" for="pr_location_id">Location <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                            <select name="pr_location_id" id="pr_location_id" class="chzn-select" onchange="fillPendingMaterialData()">
                            <option value="">Select Location</option>
                        </select>
                    </span>
            </div>
        </div>
    </div>

     <div class="span-6" id="btn_hide">
        <div class="par control-group form-control">
            <label class="control-label" for="toggle_btn">&nbsp;</label>
            <div class="controls"> <span class="formwrapper input-large">
                  <button class="btn btn-primary toggleModalBtn" type="button" data-target="#pendingMaterialRequest" data-toggle="modal" disabled>Pending</button>
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
            <table class="table table-bordered responsive" id="purchase_requisition_table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item </th>
                        <th>Code</th>
                        <th>Req. Qty.</th>
                        <th>Unit</th>
                        <th>Rate/Unit</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="4"></td>
                        <td class="prqtysum" name="item_production_total_qty"></td>
                        <td colspan="3"> </td>
                    </tr>
                </tfoot>

            </table><br>
            <button class="btn btn-primary" id="addPart" type="button" onclick="addPrDetail()">Add</button>
        </div>
    </div>

    <div class="row">
         <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="prepared_by">Prepared By <sup class="astric">*</sup></label>
                    <div class="controls">
                         <span class="formwrapper">
                            {{-- <input type="text" name="prepared_by" id="prepared_by" class="form-control" placeholder="Enter Prepared By"  /> --}}

                            <input type="text" name="prepared_by" id="prepared_by" class="form-control" placeholder="Enter Prepared By" onkeyup="suggestPreparedBy(event,this)" />
                            <div id="prepared_by_list" class="suggestion_list" ></div>
                        </span>
                    </div>
            </div>
        </div>

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

<script>

    var getItem = [<?php echo json_encode(getPRItem($changedItemIds)); ?>];
</script>

<script type="text/javascript" src="{{ asset('js/view/purchase_requisition.js?ver='.getJsVersion()) }}"></script>
