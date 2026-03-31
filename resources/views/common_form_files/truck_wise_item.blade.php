
<div class="row">
 
    <div class="span-6" id="transport">
        <div class="par control-group form-control">
                <label class="control-label" for="Transport">Mode of Trasport </label>
            <div class="controls">
                    <span class="formwrapper">
                        <select name="mode_of_transport" id="mode_of_transport" class="chzn-select">
                            <option value="">Select Mode of Transport</option>
                                {{-- @forelse (getLocation() as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                @empty
                            @endforelse --}}
                        </select>
                    </span>
            </div>
        </div>
    </div>

    <div class="span-6" id="transport">
        <div class="par control-group form-control">
                <label class="control-label" for="Transport">Transport </label>
            <div class="controls">
                    <span class="formwrapper">
                        <select name="transport" id="transport" class="chzn-select">
                            <option value="">Select Transport</option>
                                {{-- @forelse (getLocation() as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                @empty
                            @endforelse --}}
                        </select>
                    </span>
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="vehicle_no">Vehicle No. </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="vehicle_no" id="vehicle_no" />
                    </span>
                </div>
            </div>
    </div>
    
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="lr_no">LR No. </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="lr_no" id="lr_no" />                    
                    </span>
                </div>
            </div>
    </div>
</div>


<div class="row">

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="grn_date">LR Date </label>
                <div class="controls"> <span class="formwrapper">
                    <input name="lr_date" id="lr_date" class="input-large trans-date-picker no-fill" />
                </span> </div>
                </div>
    </div>


    <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="lr_no">Invoice No. </label>
                    <div class="controls">
                        <span class="formwrapper">
                            <input type="text" name="invoice_no" id="invoice_no" />          </span>
                    </div>
                </div>
    </div>

    <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="grn_date">Invoice Date </label>
                    <div class="controls"> <span class="formwrapper">
                        <input name="invoice_date" id="invoice_date" class="input-large trans-date-picker no-fill" />
                    </span> </div>
                    </div>
        </div>
</div>


    <div class="widgetbox-inverse">
        <div class="headtitle">
            <h4 class="widgettitle">Items List</h4>
        </div>
        <div class="widgetcontent overflow-scroll">
            <table class="table table-bordered responsive" id="truckWiseItemTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>DC. No.</th>
                        <th>DC Dae</th>
                        <th>Customer</th>
                        <th>Mode of Transport</th>
                        <th>Transport</th>
                        <th>Vehicle No.</th>
                        <th>LR No.</th>
                        <th>Invoice No.</th>
                        <th>Invoice Dae</th>                                                 
                    </tr>
                </thead>
                <tbody>


                </tbody>
                <tfoot>
                    <tr class="total_tr">
                        {{-- <td colspan="8" ></td> --}}
                        <td class="grnqtysum" name="grn_total_qty"></td>                     
                        <td class="amountsum" name="grn_total_amount">    
                        </tr>

                </tfoot>

            </table><br>



            <button class="btn btn-primary" type="button" id="addPart" onclick="addPartDetail()">Add</button>
        </div>
    </div>



<script>
var getItem = [<?php echo json_encode(getFittingItem()); ?>];
 </script>

 <script src="{{ asset('js/view/truck_wise_item.js?ver='.getJsVersion()) }}"></script>

 
