<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="return_sequence">Sr No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="return_sequence" id="return_sequence" class="input-large only-numbers sequence"  />
                    <input name="return_number" id="return_number" class="input-large sequence-number" />
                    <input type="hidden" value="itemReturn" name="hidViewPage" id="hidViewPage"/>
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="return_date">Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="return_date" id="return_date" class="input-large trans-date-picker no-fill" />
                    </span> </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="issue_no">Issue No. </label>
                    <div class="controls">
                            <span class="formwrapper"> 
                                <input  name="issue_no" id="issue_no" class="input-large" placeholder="Enter Issue No.">
                            </span>
                    </div>
        </div>
    </div>

    {{-- <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="supplier_id">Supplier <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <select name="supplier_id" id="supplier_id" class="chzn-select" onchange="fillPendingItemIssue()">
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

    {{-- <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="toggle_btn">&nbsp;</label>
            <div class="controls"> <span class="formwrapper input-large">
                  <button class="btn btn-primary toggleModalBtn" type="button" data-target="#pendingItemIssue" data-toggle="modal" disabled>Pending</button>
            </div>
        </div>
    </div> --}}
</div> <!-- row end -->
<div class="divider15"></div>

    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Item Return Slip Detail <sup class="astric">*</sup></h4>
        </div>
        {{-- <div class="widgetcontent overflow-scroll"> --}}
        <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
            <table class="table table-bordered responsive" id="itemIssueTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item</th>
                        {{-- <th>Issue No.</th> --}}
                        {{-- <th width="11.87%">Issue Date</th> --}}
                        <th>Item Detail Name</th>
                        <th>Code</th>
                        <th>Group</th>                        
                        {{-- <th>Pend. Issue Qty. </th>                              --}}
                        <th>Return Qty.</th>  
                        <th>Unit </th>                                        
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>            
                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="6" ></td>
                        <td class="returnqtysum" name="item_return_total_qty"></td>
                        <td></td>                                                
                        <td></td>                                                
                        </tr>

                </tfoot>

            </table><br>
            <button class="btn btn-primary" type="button" onclick="addPartDetail()">Add</button>
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
    use App\Models\ItemReturnDetail;
    if(isset($id)){
        $locationCode = getCurrentLocation();
    
        $changedItemIds = ItemReturnDetail::
        leftJoin('items', 'items.id', '=', 'item_return_details.item_id')
        ->where('item_return_details.item_return_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes');
        })
        ->pluck('item_return_details.item_id')
        ->toArray();

                   
    }else {  
        $changedItemIds = [];
    }
?>

   <script>
        var getItem = [<?php echo json_encode(getFittingItem($changedItemIds)); ?>];
        
         </script>

<script type="text/javascript" src="{{ asset('js/view/item_return.js?ver='.getJsVersion()) }}"></script>
