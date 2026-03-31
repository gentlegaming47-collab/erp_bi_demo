

{{-- <div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label"></label>
                <span class="formwrapper radioClass">
                    <input type="radio" name="issue_type_id_fix"  value="1" onchange="changeItemTypeValue()"/> Inhouse &nbsp; &nbsp;
                    <input type="radio" name="issue_type_id_fix" value="2" onchange="changeItemTypeValue()"  /> Outside  &nbsp; &nbsp;   </span>
        </div>
    </div>
</div> --}}

<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="issue_sequence">Issue No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="issue_sequence" id="issue_sequence" class="input-large only-numbers sequence"  />
                    <input name="issue_number" id="issue_number" class="input-large sequence-number" />
                      <input type="hidden" value="itemIssue" name="hidViewPage" id="hidViewPage"/>
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="issue_date">Issue Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="issue_date" id="issue_date" class="input-large trans-date-picker no-fill" />
                    </span> </div>
        </div>
    </div>

    {{-- <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label supplierTitle" for="supplier_id">Supplier </label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <select name="supplier_id" id="supplier_id" class="chzn-select">
                            <option value="">Select Supplier</option>
                                @forelse (getSuppliers() as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name}}</option>
                                @empty
                            @endforelse
                        </select>
                    </span>
            </div>
        </div>
    </div> --}}
</div> <!-- row end -->


    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Item Issue Slip Detail <sup class="astric">*</sup></h4>
        </div>
        {{-- <div class="widgetcontent overflow-scroll" style="overflow-y:hidden;"> --}}
         <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
            <table class="table table-bordered responsive" id="itemIssueTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item</th>
                        <th>Item Detail Name</th>
                        <th>Code</th>
                        <th>Group</th>
                        <th>Stock</th>
                        <th>Issue Qty.</th>
                        <th>Unit</th>
                        <th>Issue Type</th>                                    
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="7"></td>
                        <td class="itemqtysum" name="item_issue_total_qty"></td>
                        <td></td>                                                
                        <td class="amountsum" name="src_total_amount">                                   <td></td>
                        </tr>

                </tfoot>

            </table><br>
            <button class="btn btn-primary" type="button" onclick="addItemDetail()">Add</button>
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
    use App\Models\ItemIssueDetail;
    if(isset($id)){
        $locationCode = getCurrentLocation();
    
        $changedItemIds = ItemIssueDetail::
        leftJoin('items', 'items.id', '=', 'item_issue_details.item_id')
        ->where('item_issue_details.item_issue_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes');
        })
        ->pluck('item_issue_details.item_id')
        ->toArray();

                   
    }else {  
        $changedItemIds = [];
    }
?>
  

    <script>
        var getItem = [<?php echo json_encode(getFittingItem($changedItemIds)); ?>];
        
         </script>
<script type="text/javascript" src="{{ asset('js/view/item_issue.js?ver='.getJsVersion()) }}"></script>
