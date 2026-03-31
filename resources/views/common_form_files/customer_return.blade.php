   
<div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="cr_number">CR No. </label>
                    <div class="controls"> <span class="formwrapper">
                        <input name="cr_sequence" id="cr_sequence" class="input-large only-numbers sequence"  />
                        <input name="cr_number" id="cr_no" class="input-large sequence-number" readonly/>
                        </span>
                    </div>
            </div>
        </div>
    
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="grn_date">Reg. No. </label>
                <div class="controls"> <span class="formwrapper">
                        <input name="reg_no" id="reg_no" class="form-control"  />                      
                        </span>
                    </div>
            </div>
        </div>
    
        <div class="span-6" id="customer_re">
            <div class="par control-group form-control">
                <label class="control-label" for="customer">Customer </label>
                <div class="controls"> <span class="formwrapper">
                    <input name="customer" id="customer" class="form-control"  />                      
                    </span>
                </div>
            </div>
        </div>

        <div class="span-6" id="dc_village">
            <div class="par control-group form-control">
                    <label class="control-label" for="village">Village </label>
                <div class="controls">
                        <span class="formwrapper">                      
                         <select name="village" id="village" class="chzn-select">
                                <option value="">Select Village</option>
                                    {{-- @forelse (getLocation() as $location)
                                    <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                    @empty
                                @endforelse --}}
                            </select>
                        </span>
    
                </div>
            </div>
        </div>
    
     
    </div>
    
    <div class="row">
        <div class="span-6" id="dc_village">
            <div class="par control-group form-control">
                    <label class="control-label" for="village">Taluka </label>
                <div class="controls">
                        <span class="formwrapper">                      
                         <select name="taluka" id="taluka" class="chzn-select">
                                <option value="">Select Taluka</option>
                                    {{-- @forelse (getLocation() as $location)
                                    <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                    @empty
                                @endforelse --}}
                            </select>
                        </span>
    
                </div>
            </div>
        </div>

        <div class="span-6" id="dc_village">
            <div class="par control-group form-control">
                    <label class="control-label" for="district">District </label>
                <div class="controls">
                        <span class="formwrapper">                      
                         <select name="district" id="district" class="chzn-select">
                                <option value="">Select District</option>
                                    {{-- @forelse (getLocation() as $location)
                                    <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                    @empty
                                @endforelse --}}
                            </select>
                        </span>
    
                </div>
            </div>
        </div>

        <div class="span-6" id="dc_village">
            <div class="par control-group form-control">
                    <label class="control-label" for="state">State </label>
                <div class="controls">
                        <span class="formwrapper">                      
                         <select name="state" id="state" class="chzn-select">
                                <option value="">Select State</option>
                                    {{-- @forelse (getLocation() as $location)
                                    <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                    @empty
                                @endforelse --}}
                            </select>
                        </span>
    
                </div>
            </div>
        </div>

        <div class="span-6" id="dc_village">
            <div class="par control-group form-control">
                    <label class="control-label" for="country">Country </label>
                <div class="controls">
                        <span class="formwrapper">                      
                         <select name="country" id="country" class="chzn-select">
                                <option value="">Select Country</option>
                                    {{-- @forelse (getLocation() as $location)
                                    <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                    @empty
                                @endforelse --}}
                            </select>
                        </span>
    
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="widgetbox-inverse">
            <div class="headtitle">
                <h4 class="widgettitle">Items List</h4>
            </div>
            <div class="widgetcontent overflow-scroll">
                <table class="table table-bordered responsive" id="customerReturnTable">
                    <thead>
                        <tr>
                            <th>Action</th>                        
                            <th>Item Name</th>
                            <th>Item Code</th>
                            <th>Ref. Qty.</th>                            
                            <th>Unit</th>                            
                            <th>Remarks</th>                            
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
                        <label class="control-label" for="mode_of_disptach">Mode of Dispatch </label>
                            <div class="controls">
                                    <span class="formwrapper">
                                        <select name="mode_of_dispatch" id="mode_of_dispatch" class="chzn-select mst-transporter">
                                            <option value="">Select Mode of Dispatch</option>
                                            {{-- @forelse (getTransporter() as $transporter)
                                            <option value="{{ $transporter->id }}">{{ $transporter->transporter_name}}</option>
                                            @empty
                                        @endforelse --}}
                                        </select>
                                    </span>
                            </div>
                </div>
            </div>
            
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
                                        </select>
                                        @if(hasAccess('transporter','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#transportModal"></i></span>@endif
                                    </span>
                            </div>
                </div>
            </div>
    
            <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label" for="lr_number">Vehicle No. </label>
                        <div class="controls">
                             <span class="formwrapper">
                                <input type="text" name="vehicle_no" id="vehicle_no" class="form-control"  /> 
                                
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

     
    