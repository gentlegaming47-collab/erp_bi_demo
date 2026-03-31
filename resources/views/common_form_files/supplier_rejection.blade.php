
<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label"></label>
                <span class="formwrapper radioClass">
                    <input type="radio" name="src_type_fix_id" value="1" onchange="changeSrcValue()" checked/> Manual &nbsp; &nbsp;
                    <input type="radio" name="src_type_fix_id" value="2" onchange="changeSrcValue()"/> From QC  &nbsp; &nbsp;                    
                </span>
             
        </div>
    </div>
</div>

<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="src_sequence">Challan No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="src_sequence" id="src_sequence" class="input-large only-numbers sequence"  />
                    <input name="src_number" id="src_no" class="input-large sequence-number" />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="src_date">Challan Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="src_date" id="src_date" class="input-large trans-date-picker  no-fill" />
                    </span> </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="supplier_id">Supplier <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <select name="supplier_id" id="supplier_id" class="chzn-select" onchange="fillPendingQc() ">
                            <option value="">Select Supplier</option>
                                {{-- @forelse (getSuppliers() as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name}}</option>
                                @empty
                            @endforelse --}}
                        </select>
                    </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="hide">
        <div class="par control-group form-control">
            <label class="control-label" for="toggle_btn">&nbsp;</label>
            <div class="controls"> <span class="formwrapper input-large">
                  <button class="btn btn-primary toggleModalBtn" type="button" data-target="#pendingQcRequest" data-toggle="modal" disabled>Pending</button>
            </div>
        </div>
    </div>   
    
</div> <!-- row end -->

<div class="row">
    <div class="span-6"  id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="person">GRN No. </label>
                <div class="controls">
                     <span class="formwrapper">
                        <input type="text" name="ref_no" id="ref_no" class="form-control" placeholder="Enter GRN No." />
                    </span> 
                </div>
        </div>
    </div>

    <div class="span-6"  id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="ref_date">Date </label>
                <div class="controls"> <span class="formwrapper">
                     <input name="ref_date" id="ref_date" class="input-large date-picker no-fill" placeholder="Enter Date" />
                    </span> </div>
        </div>
    </div>

    
   
</div>



    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Supplier Return Challan Detail <sup class="astric">*</sup></h4>
        </div>
        {{-- <div class="widgetcontent overflow-scroll"> --}}
        <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
            <table class="table table-bordered responsive" id="supplierRejectionTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item</th>
                        <th>Code</th>
                        <th>Group</th>
                        <th>Stock </th>
                        <th>Challan Qty.</th>
                        <th>Unit</th>                                          
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="6" ></td>
                        <td class="srcqtysum" name="src_total_qty"></td>
                        <td></td>                                                
                        <td class="amountsum" name="src_total_amount">                            
                        </tr>

                </tfoot>

            </table><br>
            <button class="btn btn-primary" type="button" id="addPart" onclick="addSuppDetail()">Add</button>
        </div>
    </div>

    <div class="row">

        <div class="span-6">
            <div class="par control-group form-control">
                    <label class="control-label" for="transporter">Transporter </label>
                        <div class="controls">
                                <span class="formwrapper">
                                    <select name="transporter_id" id="transporter" class="chzn-select mst-transporter">
                                        <option value="">Select Transporter</option>
                                        @forelse ($getTransporter as $transporter)
                                        {{-- @forelse (getTransporter() as $transporter) --}}
                                        <option value="{{ $transporter->id }}">{{ $transporter->transporter_name}}</option>
                                        @empty
                                    @endforelse
                                    </select>@if(hasAccess('transporter','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#transportModal"></i></span>@endif
                                </span>
                        </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="vehicle">Vehicle No. </label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" placeholder="Enter Vehicle No." />
                        </span>
                    </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="lr_number">LR No. & Date    </label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input type="text" name="lr_no_date" id="lr_no_date" class="form-control" placeholder="Enter LR No. & Date"  /> 
                            
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

<?php
    use App\Models\SupplierRejectoionDetails;
    if(isset($id)){
        $locationCode = getCurrentLocation();
    
        $changedItemIds = SupplierRejectoionDetails::
        leftJoin('items', 'items.id', '=', 'supplier_rejection_challan_details.item_id')
        ->where('supplier_rejection_challan_details.src_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->Where('items.secondary_unit','No')
                ->orWhere('items.service_item', 'Yes');
        })
        ->pluck('supplier_rejection_challan_details.item_id')
        ->toArray();

                   
    }else {  
        $changedItemIds = [];
    }
?>

    <script>
        var getItem = [<?php echo json_encode(getFittingItem($changedItemIds)); ?>];
         </script>
<script type="text/javascript" src="{{ asset('js/view/supplier_rejection.js?ver='.getJsVersion()) }}"></script>
