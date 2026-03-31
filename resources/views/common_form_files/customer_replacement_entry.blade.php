<input type="hidden" name="rep_customer_name" id="rep_customer_name">
<div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="cr_number">Sr. No. <sup class="astric">*</sup></label>
                    <div class="controls"> <span class="formwrapper">
                        <input name="cre_sequence" id="cre_sequence" class="input-large only-numbers sequence"  />
                        <input name="cre_number" id="cre_no" class="input-large sequence-number"/>
                        <input type="hidden" value="CustomerReplacementEntry" name="hidViewPage" id="hidViewPage"/>
                        </span>
                    </div>
            </div>
        </div>
    
       <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="cre_date">Date <sup class="astric">*</sup></label>
                    <div class="controls">
                         <span class="formwrapper">
                            <input name="cre_date" id="cre_date" class="input-large trans-date-picker no-fill" />
                        </span>
                    </div>
            </div>
        </div>
    
        <div class="span-6">
           
            <div class="par control-group form-control">
                <label class="control-label" for="rep_customer_id">Customer </label>
                    <div class="controls">
                        <span class="formwrapper"> 
                            <select name="rep_customer_id" id="rep_customer_id" class="chzn-select" onchange="getSearchData()">
                                <option value="">Select Customer</option>
                            </select>
                                <input type="hidden" name="search_customer_val" id="search_customer_val">

                                <span class="m-span">
                                    {{-- <button class="btn btn-primary" type="button" data-toggle="modal" href="#custSearchModal" id="replace_btn">Search</button> --}}
               
                                    <i class="action-icon iconfa-search" data-toggle="modal" data-target="#custSearchModal" id="replace_btn"></i>
                                   </span>
                        </span>
                    </div>
            </div>
        </div>


        {{-- <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="customer"></label>
                <div class="controls">
                    <span class="formwrapper">
                        <button class="btn btn-primary" type="button" data-toggle="modal" href="#custSearchModal" id="replace_btn">Search</button>
                           

                    </span>
                </div>
            </div>
        </div> --}}
    
     
  
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="reg_no">Reg No.</label>
                    <div class="controls"> <span class="formwrapper">
                        <input type="text" name="reg_no" id="reg_no" class="input-large only-numbers" placeholder="Enter Reg. No." readonly  />
                        </span>
                    </div>
            </div>
        </div>
</div>

<div class="row">

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="cre_village">Village </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="cre_village" id="cre_village" placeholder="Enter Village" readonly tabindex="-1"/>
                    </span>
                </div>
            </div>
        </div>

        <div class="span-6" id="show">
            <div class="par control-group form-control">
                <label class="control-label" for="cre_pincode">Pin Code </label>
                <div class="controls">
                    <span class="formwrapper">
                        <input type="text" name="cre_pincode" id="cre_pincode" class="only-numbers" placeholder="Enter Pin Code" readonly tabindex="-1"/>
                    </span>
                </div>
            </div>
        </div>


        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="so_country_id">Country </label>
                    <div class="controls">
                        <span class="formwrapper"> 
                            <select name="so_country_id" id="so_country_id" class="chzn-select mst-country" onchange="getSoStates(event)" readonly tabindex="-1">
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

   
         <div class="span-6" id="show">
                <div class="par control-group form-control">
                    <label class="control-label" for="cre_state_id">State </label>
                    <div class="controls">
                    <span class="formwrapper"> 
                        <select data-placeholder="Select State" name="cre_state_id" id="cre_state_id" class="chzn-select mst-suggest_state" onchange="getSoDistrict(event)" readonly tabindex="-1">
                        </select>
    

                    </span>
                    </div>
                </div>
        </div>
</div>

<div class="row">

        <div class="span-6" id="show">
            <div class="par control-group form-control">
                <label class="control-label" for="cre_district_id">District </label>
                <div class="controls">
                <span class="formwrapper"> 
                    <select data-placeholder="Select District" name="cre_district_id" id="cre_district_id" class="chzn-select mst-suggest_city" onchange="getSoTaluka(event)" readonly tabindex="-1">
                    </select>
                   
                    
                </span>
                </div>
            </div>
        </div>
    
        <div class="span-6" id="show">
            <div class="par control-group form-control">
                <label class="control-label" for="cre_taluka_id">Taluka </label>
                <div class="controls">
                <span class="formwrapper"> 
                    <select data-placeholder="Select Taluka" name="cre_taluka_id" id="cre_taluka_id"   class="chzn-select mst-suggest_taluka" readonly tabindex="-1">
                    </select>
                 
                </span>
                </div>
            </div>
        </div>

         <div class="span-6" >
            <div class="par control-group form-control">
                <label class="control-label" for="so_customer_id">Customer Group </label>
                <div class="controls">
                    <span class="formwrapper">
                        <select name="customer_group_id" id="customer_group_id" class="chzn-select" readonly tabindex="-1">
                            <option value="">Select Customer Group</option>
                            @forelse (getCustomerGroup() as $customer_group)
                            <option value="{{ $customer_group->id }}">{{ $customer_group->customer_group_name }}</option>
                            @empty
                        @endforelse
                        </select>
                    </span>
                </div>
            </div>
        </div>

</div>
    
   <div class="divider15"> </div>
    
    
    <div class="widgetbox-inverse">
            <div class="headtitle">
                <h4 class="widgettitle">Items List</h4>
            </div>
            {{-- <div class="widgetcontent overflow-scroll"> --}}
            <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
                <table class="table table-bordered responsive" id="creTable">
                    <thead>
                        <tr>
                            <th>Action</th>                        
                            <th>Sr.No.</th>                        
                            <th>Item Name</th>
                            <th>Item Detail Name</th>
                            <th>Item Code</th>
                            <th>Group</th>
                            <th>Return Details Qty.</th>                            
                            <th>Return Qty.</th>                            
                            <th>Unit</th>                            
                            <th>Remark</th>                            
                        </tr>
                    </thead>
                    <tbody>
    
    
                    </tbody>
                    <tfoot>
                            <tr class="total_tr">
                            <td colspan="7" ></td>
                                <td class="cre_qty" name="return_total_qty" id="return_total_qty"></td>                     

                                <td colspan="2"></td>
                            </tr>
    
                    </tfoot>
    
                </table><br>
    
    
    
                <button class="btn btn-primary" type="button" id="addPart" onclick="addPartDetail()">Add</button>
            </div>
        </div>
    
    
        <div class="row">
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
<?php
        use App\Models\CustomerReplacementEntryDetails;
        if(isset($id)){
            $locationCode = getCurrentLocation();
        
            $changedItemIds = CustomerReplacementEntryDetails::
            leftJoin('items', 'items.id', '=', 'customer_replacement_entry_details.item_id')
            ->where('customer_replacement_entry_details.cre_id', base64_decode($id))
            ->where(function($query) {
                $query->where('items.status', 'deactive')
                    ->orWhere('items.service_item', 'Yes');
            })
            ->pluck('customer_replacement_entry_details.item_id')
            ->toArray();
    
                       
        }else {  
            $changedItemIds = [];
        }
?>
    
 
    
     @section('scripts')

<script>
jQuery(document).ready(function(){
    getSoStates();
});

</script>

<script>
    var getItem = [<?php echo json_encode(getFittingItem($changedItemIds)); ?>];
    </script>
    <script type="text/javascript" src="{{ asset('js/view/customer_replacement.js?ver='.getJsVersion()) }}"></script>
@endsection


     
    