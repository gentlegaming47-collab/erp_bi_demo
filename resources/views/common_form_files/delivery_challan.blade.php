
<div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                    <span class="formwrapper radioClass">
                        <input type="radio" name="dc_type_fix_id" value="1"  checked/> Against SO &nbsp; &nbsp;                     
                        <input type="radio" name="dc_type_fix_id" value="2" /> TO Location  &nbsp; &nbsp;
                    </span>
                  
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="dc_number">DC No. </label>
                    <div class="controls"> <span class="formwrapper">
                        <input name="dc_sequence" id="dc_sequence" class="input-large only-numbers sequence"  />
                        <input name="dc_number" id="dc_no" class="input-large sequence-number" readonly/>
                        </span>
                    </div>
            </div>
        </div>
    
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="grn_date">DC Date </label>
                    <div class="controls"> <span class="formwrapper">
                         <input name="dc_date" id="dc_date" class="input-large trans-date-picker no-fill" />
                        </span> </div>
            </div>
        </div>
    
        <div class="span-6" id="supplier">
            <div class="par control-group form-control">
                    <label class="control-label" for="supplier_id">Customer </label>
                <div class="controls">
                        <span class="formwrapper">                       
                            <input type="text" name="customer" id="customer" class="input-large trans-date-picker no-fill" />
                        </span>
    
                </div>
            </div>
        </div>

        <div class="span-6" id="dc_location">
            <div class="par control-group form-control">
                    <label class="control-label" for="location_id">Location </label>
                <div class="controls">
                        <span class="formwrapper">                       
    
                         <select name="dc_location" id="dc_location" class="chzn-select">
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
    
     
    </div>
    
    <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="customer_village">Village </label>
                    <div class="controls">
                        <span class="formwrapper">
                            <input type="text" name="customer_village" id="customer_village" />
                        </span>
                    </div>
                </div>
        </div>

        <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label" for="so_taluka_id">Taluka</label>
                    <div class="controls">
                    <span class="formwrapper"> 
                        <select data-placeholder="Select Taluka" name="so_taluka_id" id="so_taluka_id"   class="chzn-select mst-suggest_taluka" tabindex="0">
                        </select>                 
                    </span>
                    </div>
                </div>
        </div>

        <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label" for="location_district_id">District</label>
                    <div class="controls">
                    <span class="formwrapper"> 
                        <select data-placeholder="Select District" name="so_district_id" id="so_district_id" class="chzn-select mst-suggest_city" onchange="getSoTaluka(event)" tabindex="0">
                        </select>                    
                    </span>
                    </div>
                </div>
        </div>

        <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label" for="so_state_id">State</label>
                    <div class="controls">
                    <span class="formwrapper"> 
                        <select data-placeholder="Select State" name="so_state_id" id="so_state_id" class="chzn-select mst-suggest_state" onchange="getSoDistrict(event)" tabindex="0">
                        </select>
                        
                    </span>
                    </div>
                </div>
        </div>
    </div>
    
    <div class="row">        
            <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="toggle_btn">&nbsp;</label>
                        <div class="controls"> <span class="formwrapper input-large">
                              <button class="btn btn-primary toggleModalBtn" type="button" isabled>Pending</button>
                        </div>
                    </div>
                </div>
    </div>
    
    
        <div class="widgetbox-inverse">
            <div class="headtitle">
                <h4 class="widgettitle">Items List</h4>
            </div>
            <div class="widgetcontent overflow-scroll">
                <table class="table table-bordered responsive" id="deliveryChallanTable">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Sr. No.</th>
                            <th>SO No.</th>
                            <th>SO Date</th>
                            <th>Item Name</th>
                            <th>Item Code</th>
                            <th>SO Qty.</th>
                            <th>Pend Qty.</th>
                            <th>Stock DC. Qty.</th>
                            <th>Unit</th>                            
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
    
    
        <div class="row">
    
            <div class="span-6">
                <div class="par control-group form-control">
                        <label class="control-label" for="transporter">Transporter </label>
                            <div class="controls">
                                    <span class="formwrapper">
                                        <select name="transporter" id="transporter" class="chzn-select mst-transporter">
                                            <option value="">Select Transporter</option>
                                            @forelse (getTransporter() as $transporter)
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
                    <label class="control-label" for="lr_number">LR No.  </label>
                        <div class="controls">
                             <span class="formwrapper">
                                <input type="text" name="lr_no" id="lr_no" class="form-control"  /> 
                                
                            </span>
                        </div>
                </div>
            </div>

            <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="vehicle">Vehicle No. </label>
                            <div class="controls">
                                 <span class="formwrapper">
                                    <input type="text" name="vehicle" id="vehicle" class="form-control"  />
                                </span>
                            </div>
                    </div>
                </div>

            <div class="span-6">
                    <div class="par control-group form-control">
                            <label class="control-label" for="lr_number">Invoice No.    </label>
                                <div class="controls">
                                     <span class="formwrapper">
                                        <input type="text" name="invoice_no_date" id="invoice_no_date" class="form-control"  />            
                                    </span>
                                </div>
                        </div>
                </div>
        </div>
    

        <div class="row">
            <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="sp_notes">Sp Notes </label>
                            <div class="controls">
                                <span class="formwrapper">
                                    <input type="text" name="sp_notes" id="sp_notes" class="form-control"  />
                                </span>
                            </div>
                    </div>
                </div>
        </div>

    
    <script>
    var getItem = [<?php echo json_encode(getFittingItem()); ?>];
     </script>
    
     <script src="{{ asset('js/view/delivery_challan.js?ver='.getJsVersion()) }}"></script>

     
    