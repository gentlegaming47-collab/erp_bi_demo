<div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="quot_no">Quot. No. <sup class="astric">*</sup></label>
                    <div class="controls"> <span class="formwrapper">
                        <input name="quot_sequence" id="quot_sequence" class="input-large only-numbers sequence"  />
                        <input name="quot_no" id="quot_no" class="input-large sequence-number"/>
                        </span>
                    </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
            <label class="control-label" for="quot_date">Quot. Date <sup class="astric">*</sup></label>
            <div class="controls"> <span class="formwrapper">
                <input name="quot_date" id="quot_date" class="trans-date-picker no-fill" />
                </span> </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="customer_group_id">Cust. Group <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper">
                        <select name="customer_group_id" id="customer_group_id" class="chzn-select" onchange="getQuotDealer()">
                            <option value="">Select Cust Group</option>
                            @forelse (getSoCustomerGroup() as $customer_group)
                            <option value="{{ $customer_group->id }}">{{ $customer_group->customer_group_name }}</option>
                            @empty
                        @endforelse
                        </select>
                    </span>
                </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="customer_name">Customer <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="customer_name" id="customer_name" placeholder="Enter Customer"/>
                    </span>
                </div>
            </div>       
        </div>
    </div>

    <div class="row">

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="dealer_id">Dealer <sup class="astric">*</sup></label>
                <div class="controls">
                        <span class="formwrapper">
                            <select name="dealer_id" id="dealer_id" class="chzn-select mst-dealer">
                                <option value="">Select Dealer</option>

                            </select>
                            @if(hasAccess('dealer','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#dealerModal"></i></span>@endif
                        </span>
                </div>
            </div>
        </div>
   

        <div class="span-6" >
            <div class="par control-group form-control">
                <label class="control-label" for="quot_country_id">Country <sup class="astric">*</sup></label>
                    <div class="controls">
                    <span class="formwrapper">
                            <select name="quot_country_id" id="quot_country_id" class="chzn-select mst-country" onchange="getQuotStates(event)">
                                <option value="">Select Country</option>
                                    @forelse (getCountries() as $country)
                                        @if($country->country_name == 'India')
                                        <option value="{{ $country->id }}" selected>{{ $country->country_name }}</option>
                                        @else
                                        <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                        @endif

                                    @empty
                                    @endforelse
                            </select>
                        </span>
                </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="quot_state_id">State <sup class="astric">*</sup></label>
                <div class="controls">
                <span class="formwrapper">
                    <select data-placeholder="Select State" name="quot_state_id" id="quot_state_id" class="chzn-select mst-suggest_state" onchange="getQuotDistrict(event)" tabindex="0">
                    </select>
                    @if(hasAccess('state','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#stateModal"></i></span>@endif

                </span>
                </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="quot_district_id">District <sup class="astric">*</sup></label>
                <div class="controls">
                <span class="formwrapper">
                    <select data-placeholder="Select District" name="quot_district_id" id="quot_district_id" class="chzn-select mst-suggest_city" onchange="getQuotTaluka(event)" tabindex="0">
                    </select>
                    @if(hasAccess('district','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#cityModal"></i></span>@endif

                </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="quot_taluka_id">Taluka <sup class="astric">*</sup></label>
                <div class="controls">
                <span class="formwrapper">
                    <select data-placeholder="Select Taluka" name="quot_taluka_id" id="quot_taluka_id"   class="chzn-select mst-suggest_taluka" onchange="getQuotVillage(event)" tabindex="0">
                    </select>
                    @if(hasAccess('taluka','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#talukaModal"></i></span>@endif
                </span>
                </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="quot_village_id">Village <sup class="astric">*</sup></label>
                <div class="controls">
                    <span class="formwrapper">
                        {{-- <input type="text" name="quot_village_id" id="quot_village_id" placeholder="Enter Village" /> --}}
                        <select data-placeholder="Select Village" name="quot_village_id" id="quot_village_id" class="chzn-select mst-suggest_village" tabindex="0" onchange="changePincode()">
                        </select>
                        @if(hasAccess('village','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#VillageModal"></i></span>@endif
                    </span>
                </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="pincode">Pin Code </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="pincode" id="pincode" class="only-numbers" placeholder="Enter Pin Code" />
                    </span>
                </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="mis_category_id">MIS Category <sup class="astric">*</sup></label>
                <div class="controls">
                <span class="formwrapper">
                    <select data-placeholder="Select MIS Category" name="mis_category_id" id="mis_category_id" class="chzn-select mst_mis_cat" tabindex="0">
                                <option value=""> Select MIS Category </option>
                                @forelse (getMisCategory()  as $mis_cat)
                                    <option value="{{ $mis_cat->id }}">{{ $mis_cat->mis_category }}</option>
                                    @empty
                                @endforelse
                    </select>@if(hasAccess('mis_category','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#MisCatModal"></i></span>@endif
                </span>
                </div>
            </div>
        </div>

    </div>
    
    <div class="row">

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="quot_mobile_no">Mobile </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="quot_mobile_no" id="quot_mobile_no" class="mobile-f" placeholder="Enter Mobile" />
                    </span>
                </div>
            </div>
        </div>
    </div>

<div class="divider15"></div>

<!--Second Section-->
<div class="row-fluid">
    <div class="widgetbox">
        <h4 class="widgettitle" id="salesOrderTitle">Quotation Detail</h4>
            <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">                        
                <table class="table table-bordered responsive table-autowidth" id="quotPartTable">
                    <thead>
                        <tr>                                
                            <th>Action</th>
                            <th>Sr. No.</th>
                            <th>Item</th>
                            <th>Code</th>
                            <th>Qty.</th>
                            <th>Unit</th>
                            <th>Rate/Unit</th>
                            <th>Amount</th>                                
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                    <tfoot>
                        <tr class="total_tr"><td colspan="4" ></td>
                            <td class="quotqtysum"></td>
                            <td></td>
                            <td></td>
                            <td class="amountsum"></tr>
                    </tfoot>
                </table><br>
                <button class="btn btn-primary" type="button" id="addPart" onclick="addPartDetail()">Add</button>
            </div>
    </div>
</div>

<div class="divider15"></div>

<!-- GST Calculation -->
<div class="row">

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="basic_amount">Basic Amount</label>
            <div class="controls"> <span class="formwrapper">
               <input type="number" name="basic_amount" id="basic_amount" class="input-large"  readonly/>
                </span>
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="less_discount">Less Discount</label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="less_discount_percentage" id="less_discount_percentage" step="0.01" min="0" max="100" class="input-small isNumberKey" onblur="formatPoints(this,2)" onkeyup="calcLessDiscount()"/>&nbsp;%&nbsp;
                    <input type="text" name="less_discount_amount" id="less_discount_amount" step="0.01" min="0" class="input-small isNumberKey" onblur="formatPoints(this,2)"/>
                </span>
            </div>
        </div>
      </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="sec_tra">Secondary Transport</label>
            <div class="controls">
                <span class="formwrapper">
                    <input type="text" name="secondary_transport" id="secondary_transport" class="input-large" placeholder="Enter Secondary Transport" onkeyup="calcGstAmount()" onblur="formatPoints(this,2)" />
                </span>
            </div>
        </div>
  </div>

  <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="sec_tra"></label>

            <div class="controls">
                <span class="formwrapper">
                  @forelse(gst_type("desc") as $gstVal)
                    <input type="radio" name="gst_type_fix_id" value="{{ $gstVal->id }}" tabindex="0" {{ $gstVal->id == '3' ? "checked='checked'" : 'false' }} tabindex="1" onclick="manageGstType()"/> {{ $gstVal->name }}&nbsp; &nbsp;
                  @empty
                  @endforelse
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="span-6">
    <div class="par control-group form-control">
        <label class="control-label" for="sgst_percentage">SGST</label>
        <div class="controls">
            <span class="formwrapper">
                <input type="text" name="sgst_percentage" id="sgst_percentage" step="0.01" min="0" max="100" class="input-small sgst-field gst-fields isNumberKey" onblur="formatPoints(this,2)"/>&nbsp;%&nbsp;
                <input type="number" name="sgst_amount" id="sgst_amount" step="0.01" min="0" class="input-small sgst-field disb" onblur="formatPoints(this,2)" readonly/>
            </span>
        </div>
    </div>
  </div>
  <div class="span-6">
    <div class="par control-group form-control">
        <label class="control-label" for="cgst_percentage">CGST</label>
        <div class="controls">
            <span class="formwrapper">
                <input type="text" name="cgst_percentage" id="cgst_percentage" step="0.01" min="0" max="100" class="input-small cgst-field gst-fields isNumberKey" onblur="formatPoints(this,2)"/>&nbsp;%&nbsp;
                <input type="number" name="cgst_amount" id="cgst_amount" step="0.01" min="0" class="input-small cgst-field disb" onblur="formatPoints(this,2)" readonly/>
            </span>
        </div>
    </div>
  </div>
  <div class="span-6">
    <div class="par control-group form-control">
        <label class="control-label" for="igst_percentage">IGST</label>
        <div class="controls">
            <span class="formwrapper">
                <input type="text" name="igst_percentage" id="igst_percentage" step="0.01" min="0" max="100" class="input-small igst-field gst-fields isNumberKey" onblur="formatPoints(this,2)"/>&nbsp;%&nbsp;
                <input type="number" name="igst_amount" id="igst_amount" step="0.01" min="0" class="input-small igst-field disb" onblur="formatPoints(this,2)" readonly/>
            </span>
        </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="span-6">
         <div class="par control-group form-control">
            <label class="control-label" for="round_off">Round Off</label>
            <div class="controls"> <span class="formwrapper">
             <input name="round_off" id="round_off"  class="input-large round-off" onchange="calcNetAmount()" />
            </div>
        </div>
    </div>

     <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="net_amount">Net Amount</label>
            <div class="controls"> <span class="formwrapper">
             <input name="net_amount" id="net_amount" step="0.01" min="0" class="input-large" onblur="formatPoints(this,2)" readonly/>
            </div>
        </div>
    </div>

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="special_notes">Sp. Note </label>
            <div class="controls"> <span class="formwrapper">
            <input type="text" name="special_notes" id="special_notes" class="input-large" placeholder="Enter Sp. Note"/>
            {{-- <textarea id="special_notes" name="special_notes"  class="input-large h-auto" placeholder="Enter Sp.Note" rows="3"></textarea> --}}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- End GST Calculation -->
