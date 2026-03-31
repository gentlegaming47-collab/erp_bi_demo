<?php
    use App\Models\GRNMaterialDetails;
    use App\Models\GRNMaterial;
    if(isset($id)){
        $locationCode = getCurrentLocation();
    
        $changedItemIds = GRNMaterialDetails::
        leftJoin('items', 'items.id', '=', 'material_receipt_grn_details.item_id')
        ->where('material_receipt_grn_details.grn_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes');
        })
        ->pluck('material_receipt_grn_details.item_id')
        ->toArray();

        $supplierIds = GRNMaterial:: 
        leftJoin('suppliers', 'suppliers.id', '=', 'grn_material_receipt.supplier_id')
        ->where('grn_material_receipt.grn_id', base64_decode($id))
        ->where('suppliers.status','!=','active')
        ->pluck('grn_material_receipt.supplier_id')
        ->toArray();

                   
    }else {  
        $changedItemIds = [];

        $supplierIds = [];
    }
?>



<input type="hidden" value="GRN" name="hidViewPage" id="hidViewPage"/>
<input type="hidden" value="{{$routeType}}" name="route_path" id="route_path"/>

<div class="row">
    <div class="span-6" style="display:none;">
        <div class="par control-group form-control">
            <label class="control-label"></label>
                <span class="formwrapper radioClass">
                    {{-- <input type="radio" name="grn_type_fix_id" value="1" onchange="changeGrNValue(this)" checked/> Against PO &nbsp; &nbsp; --}}
                    {{-- <input type="radio" name="grn_type_fix_id" value="2" onchange="changeGrNValue(this)"/> Manual  &nbsp; &nbsp; --}}
                    {{-- <input type="radio" name="grn_type_fix_id" value="3" onchange="changeGrNValue(this)"/> From Location  &nbsp; &nbsp; --}}

                    @if($routeType == 'grn_details')
                    <input type="radio" name="grn_type_fix_id" value="1" onchange="changeGrNValue(this)" checked readonly/> Against PO
                    @else
                    <input type="radio" name="grn_type_fix_id" value="3" onchange="changeGrNValue(this)" checked readonly/> From Location 
                    @endif
                </span>
             
        </div>
    </div>
</div>

<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="grn_number">GRN No. <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                    <input name="grn_sequence" id="grn_sequence" class="input-large only-numbers sequence"  />
                    <input name="grn_number" id="grn_no" class="input-large sequence-number"/>
                    </span>
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="grn_date">GRN Date <sup class="astric">*</sup></label>
                <div class="controls"> <span class="formwrapper">
                     <input name="grn_date" id="grn_date" class="input-large trans-date-picker no-fill" />
                    </span> </div>
        </div>
    </div>

     @if($routeType == 'grn_details')

    <div class="span-6" id="supplier">
        <div class="par control-group form-control">
                <label class="control-label" id="labelclass" for="supplier_id">Supplier </label>
            <div class="controls">
                    <span class="formwrapper">
                        {{-- <select name="grn_supplier_id" id="grn_supplier_id" class="chzn-select chz-done" onchange="fillPendingCoaForm()">           
                                             
                        </select> --}}

                     <select name="grn_supplier_id" id="grn_supplier_id" class="chzn-select" onchange="fillPendingGrn(),resetPOdata()">
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

    @else
    <div class="span-6" id="location">
        <div class="par control-group form-control">
                <label class="control-label" for="location_id">Location <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper">
                        <select name="location_id" id="location_id" class="chzn-select"  onchange="fillPendingGrnDc()">
                            <option value="">Select Location</option>
                                @forelse (getLocation() as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                @empty
                            @endforelse
                        </select>
                    </span>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="challan_bill_no">Challan/Bill No. </label>
                <div class="controls"> <span class="formwrapper">
                    <input type="text" name="challan_bill_no" id="challan_bill_no" class="input-large" placeholder="Enter Challan/Bill No."  />
                    </span>
                </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="bill_date">Date </label>
                <div class="controls"> <span class="formwrapper">
                     <input name="bill_date" id="bill_date" class="input-large date-picker no-fill" placeholder="Enter Date" />
                    </span> </div>
        </div>
    </div>
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="toggle_btn">&nbsp;</label>
            <div class="controls"> <span class="formwrapper input-large">
                  <button class="btn btn-primary toggleModalBtn" type="button" data-target="#pendingPoModal" data-toggle="modal" disabled>Pending</button>
            </div>
        </div>
    </div>

    {{-- @if($routeType == 'grn_location')
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="vehicle_no">Vehicle No.</label>
                <div class="controls"> <span class="formwrapper input-large">
                      <input name="vehicle_no" id="vehicle_no" class="input-large date-picker no-fill" readonly />
                </div>
            </div>
        </div>
    @endif --}}

</div> <!-- row end -->


    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Goods Receipt Note Detail<sup class="astric"> *</sup></h4>
        </div>
        {{-- <div class="widgetcontent overflow-scroll" style="overflow-y:hidden;"> --}}
        <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;"> 
            <table class="table table-bordered responsive" id="grnDetails">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Item</th>
                        <th> Code</th>
                        <th> Group</th>
                        <th>Pend. PO Qty.</th>
                        <th>GRN Qty.</th>
                        <th>Unit</th>
                        <th>Rate/Unit</th>
                        <th>Amount</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>


                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="8" ></td>
                        <td class="grnqtysum" name="grn_total_qty"></td>
                        <td></td>
                        <td></td>
                        <td class="amountsum" name="grn_total_amount">
                            <td></td>
                        </tr>

                </tfoot>

            </table><br>


            @if($routeType == 'grn_details')   
            @else 
            <button class="btn btn-primary" type="button" onclick="addLoadingPartDetail()" id="addPart">Add</button>
            @endif
        </div>
    </div>

 {{-- @if($routeType == 'grn_details') --}}

    <div class="row">

        <div class="span-6">
            <div class="par control-group form-control">
                    <label class="control-label" for="transporter">Transporter </label>
                        <div class="controls">
                                <span class="formwrapper">
                                    <select name="transporter" id="transporter" class="chzn-select mst-transporter">
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
                            <input type="text" name="vehicle" id="vehicle" class="form-control" placeholder="Enter Vehicle No."  />
                        </span>
                    </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="lr_number">LR No. & Date </label>
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
                            <input type="text" name="sp_notes" id="sp_notes" class="form-control" placeholder="Enter Sp. Note"  />
                        </span>
                    </div>
            </div>
        </div>



    </div>
{{-- @endif --}}

<script>
var getItem = [<?php echo json_encode(getFittingItem($changedItemIds)); ?>];
 </script>


 <script src="{{ asset('js/view/grn_details.js?ver='.getJsVersion()) }}"></script>
