<input type="hidden" name="old_require_raw_material_mapping" id="old_require_raw_material_mapping">
<div class="row">

        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="item">Item <sup class="astric">*</sup></label>

                <div class="controls">

                    <span class="formwrapper">

                        <input type="text" name="item_name" id="item_name" class="input-large auto-suggest"  onfocusout="verifyItem()"  onkeyup="suggestItemName(event,this)" autocomplete="nope" autofocus placeholder="Enter Item"/>

                        <div id="item_list" class="suggestion_list" ></div>
                        <input type="hidden" name="item" id="item">
                    </span>

                </div>

            </div>

        </div>

       
        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="customer_type">Item Group <sup class="astric">*</sup></label>

                <div class="controls">

                    <span class="formwrapper">

                    <select name="item_group_id" id="item_group_id" class="chzn-select mst-suggest_item_group" onchange="getLatestItemcode()">
                        <option value="">Select Item Group</option>
                            @forelse (getItemGroupData() as $item_group_data)

                            <option value="{{ $item_group_data->id }}">{{ $item_group_data->item_group_name }}</option>

                            @empty

                        @endforelse

                    </select>
                    
                    @if(hasAccess('items_group','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#itemGroupModal"></i></span>@endif
                
                    </span>

                    </span>

                </div>

            </div>

        </div>

   
   

        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="item">Item Code </label>

                <div class="controls">

                    <span class="formwrapper">
                        {{-- <div id="item_list" class="suggestion_list" ></div> --}}
                        {{-- <input type="text" name="item_code_sequence" id="item_code_sequence" class="input-large only-numbers sequence" /> --}}
                        <input type="text" name="item_code" id="item_code" class="input-large auto-suggest suggest_item_group" autocomplete="nope" tabindex="-1" readonly placeholder="Enter Item Code"/>
                    </span>

                    </span>

                </div>

            </div>

        </div>

       
        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="unit">Unit <sup class="astric">*</sup></label>

                <div class="controls">

                    <span class="formwrapper">

                    <select name="unit_id" id="unit_id" class="chzn-select mst-suggest_unit" >
                        <option value="">Select Unit</option>
                            @forelse (getUnit() as $unit)

                            <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>

                            @empty

                        @endforelse

                    </select>
                    
                    @if(hasAccess('units','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#unitModal"></i></span>@endif

                    </span>

                    </span>

                </div>

            </div>

        </div>

   
    </div>


    <div class="row">

        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="min_stock_qty">Min. Stock Qty. </label>

                <div class="controls">

                    <span class="formwrapper">

                        <input type="text" name="min_stock_qty" id="min_stock_qty"  class="input-large auto-suggest isNumberKey" autocomplete="nope" autofocus placeholder="Enter Min. Stock Qty." onblur="formatPoints(this,3)"/>

                        {{-- <div id="item_list" class="suggestion_list" ></div> --}}

                    </span>

                </div>

            </div>

        </div>

       
        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="max_stock_qty">Max. Stock Limit </label>

                <div class="controls">

                    <span class="formwrapper">

                       <input type="text" name="max_stock_qty" id="max_stock_qty" class="input-large auto-suggest isNumberKey" autocomplete="nope" autofocus placeholder="Enter Max. Stock Limit" onblur="formatPoints(this,3)"/>

                        {{-- <div id="item_list" class="suggestion_list" ></div> --}}

                    </span>

                    </span>

                </div>

            </div>

        </div>

   
 

        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="re_order_qty">Re-Order Qty. </label>

                <div class="controls">

                    <span class="formwrapper">

                         <input type="text" name="re_order_qty" id="re_order_qty" class="input-large auto-suggest isNumberKey" onblur="formatPoints(this,3)" autocomplete="nope" autofocus placeholder="Enter Re-Order Qty." />

                        {{-- <div id="item_list" class="suggestion_list" ></div> --}}

                    </span>

                </div>

            </div>

        </div>

       
        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="hsn_code">HSN Code </label>

                <div class="controls">

                    <span class="formwrapper">

                        <select name="hsn_code_id" id="hsn_code_id" class="chzn-select mst-suggest_hsn">
                            <option value="">Select HSN code</option>
                                @forelse (getHsnCodes() as $hsn_code)

                                <option value="{{ $hsn_code->id }}">{{ $hsn_code->hsn_code }}</option>

                                @empty

                            @endforelse

                        </select>

                        @if(hasAccess('hsn','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#HSNModal"></i></span>@endif

                    </span>

                    </span>

                </div>

            </div>

        </div>

   
    </div>

    <div class="row">

        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="rate_unit">Rate/Unit </label>

                <div class="controls">

                    <span class="formwrapper">

                        {{-- <input type="number" name="rate_per_unit" id="rate_per_unit"  class="input-large auto-suggest" step="0.01" min="0.01" onblur="formatPoints(this,2)" autocomplete="nope" autofocus placeholder="Enter Rate/Unit"/> <span class="withaddicon">Rs.</span> --}}

                        <input type="text" name="rate_per_unit" id="rate_per_unit"  class="input-large auto-suggest isNumberKey"  onblur="formatPoints(this,2)" autocomplete="nope" autofocus placeholder="Enter Rate/Unit"/> <span class="withaddicon">Rs.</span>

                        {{-- <div id="item_list" class="suggestion_list" ></div> --}}

                    </span>

                </div>

            </div>

        </div>
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label">Wt./Pc.</label>
                    <span class="formwrapper">                         
                        <input type="text" name="wt_pc" id="wt_pc" class="input-large auto-suggest isNumberKey" onblur="formatPoints(this,3)" autocomplete="nope" autofocus placeholder="Enter Wt./Pc." />
                        <span class="withaddicon">Kg.</span>

                    </span>
            </div>
        </div> 
       
        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="require_raw_material_mapping">Require Item <sup class="astric">*</sup> Mapping </label>

                <div class="controls">

                    <span class="formwrapper">

                        <select name="require_raw_material_mapping" id="require_raw_material_mapping" class="chzn-select" {{ empty($item_map) ? "" : "readonly" }}>
                            <option value="No">No</option>                               
                            <option value="Yes">Yes</option>
                        </select>

                    </span>

                    </span>

                </div>

            </div>

        </div>

         <div class="span-6" id="assembly_hide">

            <div class="par control-group form-control">

                <label class="control-label" for="show_item_in_print">Show Item In Print</label>

                <div class="controls">

                    <span class="formwrapper">
                        <select name="show_item_in_print" id="show_item_in_print" class="chzn-select">
                            <option value="No">No</option>                               
                            <option value="Yes">Yes</option>
                        </select>
                    </span>
                </div>

            </div>

        </div>


   
   
                    
        <div class="span-6">

            <div class="par control-group form-control">

                <label class="control-label" for="fitting_item">Fitting Item <sup class="astric">*</sup></label>

                <div class="controls">

                    <span class="formwrapper">

                        <select name="fitting_item" id="fitting_item" class="chzn-select">
                            <option value="No">No</option>                               
                            <option value="Yes">Yes</option>
                        </select>

                    </span>

                    </span>

                </div>

            </div>

        </div>

    </div>

    <div class="row">
        
        <div class="span-6" id="radio_so">
            <div class="par control-group form-control">
                <label class="control-label">Print in Dispatch Plan </label>
                    <span class="formwrapper">

                            <select name="print_dispatch_plan" id="print_dispatch_plan" class="chzn-select">
                                <option value="No">No</option>                               
                                <option value="Yes">Yes</option>
                            </select>             
                        
                    </span>
            </div>
        </div>  
        <div class="span-6" id="radio_so">
            <div class="par control-group form-control">
                <label class="control-label">Own Manufacturing</label>
                    <span class="formwrapper">
                            <select name="own_manufacturing" id="own_manufacturing" class="chzn-select">
                                <option value="No">No</option>                               
                                <option value="Yes">Yes</option>
                            </select>       
                        
                    </span>
            </div>
        </div>  
        <div class="span-6" id="radio_so">
            <div class="par control-group form-control">
                {{-- <label class="control-label">Allow Req. Above MSL</label> --}}
                <label class="control-label">Allow Req. Above Max. Stock</label>
                    <span class="formwrapper">                          
                            <select name="dont_allow_req_msl" id="dont_allow_req_msl" class="chzn-select">
                                <option value="No">No</option>                               
                                <option value="Yes">Yes</option>
                            </select>                    
                    </span>
            </div>
        </div>
        <div class="span-6" id="radio_so">
            <div class="par control-group form-control">
                <label class="control-label"> Service Item </label>
                    <span class="formwrapper">                    
                           <select name="service_item" id="service_item" class="chzn-select" onchange="serviceItem()">
                            <option value="No">No</option>                               
                            <option value="Yes">Yes</option>
                        </select>                        
                    </span>
            </div>
        </div>

        
      

    </div>
    <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="status">Status <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper">
                        <select name="status" id="status" class="chzn-select">
                            <option value="active">Active</option>
                            <option value="deactive">Deactive</option>                               
                        </select>
                    </span>
                    </span>
                </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="qc_required">QC Required </label>
                    <div class="controls">
                        <span class="formwrapper">
                            <select name="qc_required" id="qc_required" class="chzn-select">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </span>
                    </div>
            </div>
        </div>

        <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label">Allow Partial Dispatch</label>
                        <span class="formwrapper">                     

                                <select name="allow_partial_dispatch" id="allow_partial_dispatch" class="chzn-select">
                                    <option value="No">No</option>                               
                                    <option value="Yes">Yes</option>
                                </select>       
                            
                        </span>
                </div>
            </div> 
            <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label">Secondary unit</label>
                        <span class="formwrapper">                     

                                <select name="secondary_unit" id="secondary_unit" class="chzn-select" onchange="secondaryUnit()">
                                    <option value="No">No</option>                               
                                    <option value="Yes">Yes</option>
                                </select>       
                            
                        </span>
                </div>
            </div> 

            {{-- <div class="span-6" id="hide">
                <div class="par control-group form-control">
                    <label class="control-label" for="qty">Qty. <sup class="astric">*</sup></label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="qty" id="qty" class="input-large auto-suggest isNumberKey" onblur="formatPoints(this,3)" autocomplete="nope" autofocus placeholder="Enter Qty."/>

                             </span>
                        </div>
                </div>
            </div> --}}


            <div class="span-6" id="hide">
                <div class="par control-group form-control">
                    <label class="control-label" for="unit">Unit <sup class="astric">*</sup></label>
                        <div class="controls">
                            <span class="formwrapper">
                                <select name="second_unit" id="second_unit" class="chzn-select mst-suggest_unit" >
                                    <option value="">Select Unit</option>
                                        @forelse (getUnit() as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                        @empty
                                        @endforelse
                                </select>
                            </span>
                        </div>
                </div>
            </div> 
                    
        {{-- <div class="span-6" id="radio_so">
            <div class="par control-group form-control">
                <label class="control-label">Print Dispatch Plan</label>
                    <span class="formwrapper">
                      
                        <input type="radio" class="print_dispatch_plan" name="print_dispatch_plan" value="Yes" /> Yes
                       
                        <input type="radio" class="print_dispatch_plan" name="print_dispatch_plan" value="No"/> No
                        
                    </span>
            </div>
        </div>   --}}

   
    </div>
    <div class="divider15"></div>

    <div class="row-fluid" id="item_hide">
        <div class="widgetbox">
            <h4 class="widgettitle">Item Details</h4>
                <div class="widgetcontent">                        
                    <div>
                        {{-- <button class="btn btn-primary add-part"  type="button" data-toggle="modal" href="#itemdetailModal">Add</button> --}}
                        <button class="btn btn-primary" type="button" id="addPart" onclick="addItemDetail()">Add</button>
                    </div>
                        <table class="table table-bordered responsive" id="contactTable">
                            <thead>
                                <tr>
                                    <th> Action</th>
                                    <th >Qty</th>
                                    <th >Wt./Pc.</th>
                                    <th >Item</th>
                                    
                                </tr>
                            </thead>                            
                            <tbody>
                                <tr class="centeralign" id="noContact">
                                    <td colspan="6">No Item Details Added</td>
                                </tr>
                            </tbody>
                        </table>
                </div>
        </div>
    </div>





    <script src="{{ asset('js/view/item.js?ver='.getJsVersion()) }}"></script>
