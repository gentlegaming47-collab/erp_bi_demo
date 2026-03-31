<?php
    use App\Models\PurchaseOrderDetails;
    use App\Models\PurchaseOrder;
    if(isset($id)){
        $locationCode = getCurrentLocation();
    
        $changedItemIds = PurchaseOrderDetails::
        leftJoin('items', 'items.id', '=', 'purchase_order_details.item_id')
        ->where('purchase_order_details.po_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes');
        })
        ->pluck('purchase_order_details.item_id')
        ->toArray();

        $supplierIds = PurchaseOrder:: 
        leftJoin('suppliers', 'suppliers.id', '=', 'purchase_order.supplier_id')
        ->where('purchase_order.po_id', base64_decode($id))
        ->where('suppliers.status','!=','active')
        ->pluck('purchase_order.supplier_id')
        ->toArray();

                   
    }else {  
        $changedItemIds = [];

        $supplierIds = [];
    }
?>



<input type="hidden" value="purchaseOrder" name="hidViewPage" id="hidViewPage"/>
<?php $getLocationType =  Session::get('getLocationType'); ?>

<div class="row"> 

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="po_number">PO No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="po_sequence" id="po_sequence" class="input-large only-numbers sequence"/>
                    <input name="po_number" id="po_no" class="input-large sequence-number"/>
                    </span> 
                </div>
        </div>
    </div>


    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="po_date">PO Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="po_date" id="po_date" class="input-large trans-date-picker no-fill" />
                    </span> </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="supplier_id">Supplier <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                        {{-- <select name="supplier_id" id="supplier_id" class="chzn-select" onchange="getContactPerson(),getLastSupplierDetails()"> --}}
                        {{-- <select name="supplier_id" id="supplier_id" class="chzn-select" onchange="getContactPerson(),getLastSupplierDetails(),getItemsfromMapping()"> --}}
                            <select name="supplier_id" id="supplier_id" class="chzn-select" onchange="fillPendingPr()">
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

    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="toggle_btn">&nbsp;</label>
            <div class="controls"> <span class="formwrapper input-large">
                  <button class="btn btn-primary toggleModalBtn" type="button" data-target="#pendingPrModal" data-toggle="modal" disabled>Pending</button>
            </div>
        </div>
    </div>

   
</div> <!-- row end -->

<div class="row">

    {{-- <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="ref_no">Ref. No. </label>
                <div class="controls">
                     <span class="formwrapper">
                        <input type="text" name="ref_no" id="ref_no" class="form-control" placeholder="Enter Ref. No."  />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="ref_date">Ref. Date </label>
                <div class="controls"> <span class="formwrapper">
                     <input name="ref_date" id="ref_date" class="input-large date-picker no-fill" placeholder="Enter Ref. Date" />
                    </span> </div>
        </div>
    </div> --}}
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="person">Person </label>
                <div class="controls">
                     <span class="formwrapper">
                        <input type="text" name="person" id="person" class="form-control" placeholder="Enter Person"  />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="order_by">Order By </label>
                <div class="controls">
                     <span class="formwrapper">
                        <input type="text" name="order_by" id="order_by" class="form-control" placeholder="Enter Order By" onkeyup="suggestOrderBy(event,this)" />
                        <div id="order_by_list" class="suggestion_list" ></div>
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="ship_to">Ship To </label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <select name="ship_to" id="ship_to" class="chzn-select" {{$getLocationType == 'godown' ? 'readonly' : '' }}>
                            <option value="">Select Ship To</option>
                                @forelse (getLocation() as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                @empty
                            @endforelse
                        </select>
                    </span>
            </div>
        </div>
    </div>

    
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="ref_date">Delivery Date </label>
                <div class="controls"> <span class="formwrapper">
                        <input name="check_date" id="check_date" class="input-large trans1-date-picker "  placeholder="Enter Delivery Date"/>

               
                    </span> </div>
        </div>
    </div>

</div>



    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Purchase Order Detail <sup class="astric">*</sup></h4>
        </div>
        {{-- <div class="widgetcontent overflow-scroll"> --}}
        <div class="widgetcontent"  style="overflow-x:scroll;overflow:inherit;">
            <table class="table table-bordered responsive" id="purchasetable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item</th>
                        <th> Code</th>
                        {{-- <th>Group</th> --}}
                        <th>PO Qty.</th>
                        <th>Stock Qty.</th>
                        <th>Rate/Unit</th>
                        <th>Discount</th>
                        <th>Del. Date</th>
                        <th>Unit</th>
                        <th>Amount</th>                        
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="4" ></td>
                        <td class="poqtysum" name="po_total_qty"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="amountsum" name="po_total_amount">
                        <td></td>
                        </tr>

                </tfoot>

            </table><br>
            {{-- <button class="btn btn-primary" id="addPartButton" type="button" onclick="addPartDetail()">Add</button> --}}
        </div>
    </div>

    <div class="row"> 

        {{-- <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="pf_charge">P & F Charge </label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input type="text" name="pf_charge" id="pf_charge" class="form-control" placeholder="Enter P & F Charge"  />
                        </span> 
                    </div>
            </div>
        </div> --}}

        {{-- <div class="span-6">
            <div class="par control-group form-control">
                    <label class="control-label" for="freight">Freight </label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                    <select name="freight" id="freight" class="chzn-select">
                                        <option value="">Select Freight</option>
                                            <option value="to_pay">To Pay</option>
                                            <option value="paid">To Paid</option>
                                    </select>
                                </span>
                        </div>
            </div>
        </div> --}}

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="gst">GST </label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input type="text" name="gst" id="gst" class="form-control" placeholder="Enter GST"  />
                        </span> 
                    </div>
            </div>
        </div>

        {{-- <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="payment_terms">Payment Terms </label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input type="text" name="payment_terms" id="payment_terms" class="form-control"  placeholder="Enter Payment Terms" />
                        </span> 
                    </div>
            </div>
        </div> --}}

        <div class="span-6">
            <div class="par control-group form-control">
                    <label class="control-label" for="test_certificate">Test Certificate </label>
                        <div class="controls">
                                <span class="formwrapper">
                                    <input type="text" name="test_certificate" id="test_certificate" class="form-control"  placeholder="Enter Test Certificate" />
                                </span> 
                        </div>
            </div>
        </div> 


        <div class="span-6">
            <div class="par control-group form-control">
                    <label class="control-label" for="order_acceptance">Order Acceptance </label>
                        <div class="controls">
                                <span class="formwrapper">
                                    <input type="text" name="order_acceptance" id="order_acceptance" class="form-control"  placeholder="Enter Order Acceptance" />
                                </span> 
                        </div>
            </div>
        </div> 

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="prepared_by">Prepared By </label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input type="text" name="prepared_by" id="prepared_by" class="form-control" placeholder="Enter Prepared By" onkeyup="suggestPreparedBy(event,this)" />
                          <div id="prepared_by_list" class="suggestion_list" ></div>
                        </span> 
                    </div>
            </div>
        </div>

    </div>

    <div class="row">


        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="sp_notes">Sp. Note </label>
                    <div class="controls">
                         <span class="formwrapper">
                            {{-- <input type="text" name="sp_notes" id="sp_notes" class="form-control" placeholder="Enter Sp. Note"  /> --}}

                            <textarea name="sp_notes" id="sp_notes" rows="3" class="h-auto input-large" style="width:580px;"></textarea>
                        </span> 
                    </div>
            </div>
        </div>
    </div>



<script>
var getItem = [<?php echo json_encode(getFittingItem($changedItemIds)); ?>];
var prepared_by = [<?php echo json_encode(Auth::user()->person_name); ?>];

 </script>
<script type="text/javascript" src="{{ asset('views/js/purchase_order.js?ver='.getJsVersion()) }}"></script>
