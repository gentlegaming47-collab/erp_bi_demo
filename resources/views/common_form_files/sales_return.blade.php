
<input type="hidden" value="salesReturn" name="hidViewPage" id="hidViewPage"/>
<div class="row">                    
    <div class="span-6" id="radio_sr">
        <div class="par control-group form-control">
           <label class="control-label"></label>
                {{-- <span class="formwrapper"> --}}
                    {{-- <input type="radio" name="so_from" value="customer" checked onchange="getCustomer(),soType()"/> Customer &nbsp; &nbsp; --}}
                    {{--<input type="radio" class="sr_from_id_fix" name="sr_from_id_fix" value="1" checked onchange="getCustomer()"/> Subsidy &nbsp;--}}
                   
                    {{--<input type="radio" class="sr_from_id_fix" name="sr_from_id_fix" value="2"  onchange="getCustomer()"/> Cash & Carry &nbsp;--}}
                   
                
                {{-- </span> --}}
        </div>
    </div>  
</div>    
<div class="row">

    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="sr_number">SR. No. <sup class="astric">*</sup></label>                                 
                <div class="controls"> <span class="formwrapper">
                    <input name="sr_sequence" id="sr_sequence" class="input-large only-numbers sequence"  />
                    <input name="sr_number" id="sr_no" class="input-large sequence-number"/>
                    </span> 
                </div>
        </div>
    </div>


    <div class="span-6">
        <div class="par control-group form-control">
        <label class="control-label" for="sr_date">SR. Date <sup class="astric">*</sup></label>
        <div class="controls"> <span class="formwrapper">
            <input name="sr_date" id="sr_date" class="trans-date-picker no-fill" />
            </span> </div>
        </div>
    </div>



    <div class="span-6" id="show">
        <div class="par control-group form-control">
            <label class="control-label" for="customer_name">Customer <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    <select name="customer_name" id="customer_name" class="chzn-select" onchange="getDPNumber()">
                        <option value="">Select Customer</option>
                    </select>
                </span>
            </div>
        </div>
    </div>
    
    <div class="span-6">
        <div class="par control-group form-control">
            <label class="control-label" for="dp_no_id">DP NO. <sup class="astric">*</sup></label>
            <div class="controls">
                <span class="formwrapper">
                    <select name="dp_no_id" id="dp_no_id" onchange="getDetailsData()" class="chzn-select">
                        <option value="">Select DP NO.</option>
                    </select>
                </span>
            </div>
        </div>
    </div>

</div>

<div class="divider15"></div>
<!--Second Section-->
<div class="row-fluid">
<div class="widgetbox">
    <h4 class="widgettitle" id="salesReturnTitle">Sales Return Detail</h4>
    <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">                        

    <table class="table table-bordered responsive table-autowidth" id="srPartTable">
        <thead>
        <tr>                                
            <th>Action</th>
            <th>Item</th>
            <th>Item Detail Name</th>
            {{-- <th>DP NO.</th> --}}
            {{-- <th>Group</th> --}}
            <th>DC Qty.</th>
            <th>Pending DC Qty.</th>
            <th>Unit</th>
            <th>SR Details Qty.</th>            
            <th>SR Qty.</th>                          
            <th>Remark</th>                                
        </tr>
        </thead>
        <tbody>
        
        </tbody>
    </table><br>
    {{-- <button class="btn btn-primary" type="button" id="addPart" onclick="addPartDetail()">Add</button> --}}
    </div>
</div>
</div>


    <div class="row">    
        <div class="span-6">
            <div class="par control-group form-control">
                    <label class="control-label" for="transporter">Transporter </label>
                        <div class="controls">
                                <span class="formwrapper">
                                    <select name="transporter_id" id="transporter_id" class="chzn-select mst-transporter">
                                        <option value="">Select Transporter</option>
                                        @forelse (getTransporter() as $transporter)
                                        {{-- @forelse (getTransporter() as $transporter) --}}
                                        <option value="{{ $transporter->id }}">{{ $transporter->transporter_name}}</option>
                                        @empty
                                    @endforelse
                                    </select>
                                </span>
                        </div>
            </div>
        </div>
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label" for="dp_date">Vehicle No. </label>
                    <div class="controls"> <span class="formwrapper">
                        <input name="vehicle_no" id="vehicle_no" class="input-large" placeholder="Enter Vehicle No."/>
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
                <label class="control-label" for="sp_note">Sp. Note </label>
                    <div class="controls">
                        <span class="formwrapper">
                            <input type="text" name="sp_note" id="sp_note" class="form-control" placeholder="Enter Sp. Note" />
                        </span>
                    </div>
            </div>
        </div>

    </div>
 <script type="text/javascript" src="{{ asset('js/view/sales_return.js?ver='.getJsVersion()) }}"></script>