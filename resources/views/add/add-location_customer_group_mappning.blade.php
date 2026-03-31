@extends('layouts.app',['pageTitle' => 'Location to Customer Group Mapping '])



@section('header')

<style>
    #contactTable_filter label{
      width: auto;
      white-space: nowrap;
      padding: 0;
    }
  
    #contactTable_length label{
      width: 0;
      white-space: nowrap;
      float: none;
      text-align: unset;
      padding: 0;
    }
</style>

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-location_customer_group_mappning') }}">Location to Customer Group Mapping</a> <span class="separator"></span></li>

    {{-- <li>Add Raw Material Mapping Group</li> --}}
    <li>Add Location to Customer Group Mapping</li>

</ul>

@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-location_customer_group_mappning') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Location to Customer Group Mapping</h4>

    </div>

    <div class="widgetcontent">

        <form id="addLocationCusMappingForm" class="stdform" method="post">

            @csrf

            <div class="row">
                <div class="span-6">
                    <div class="par control-group form-control">
                            <label class="control-label" for="location">Location <sup class="astric">*</sup></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                <select name="location_id" id="location_id" class="chzn-select" onchange="getExistCustomerGroup()">
                                        <option value="">Select Location</option>

                                            @forelse (getLocation() as $location)
                                    
                                            <option value="{{ $location->id }}">{{ $location->location_name }}</option>

                                            @empty

                                        @endforelse

                                    </select>
                                </span>
                            {{-- <div id="item_name_list" class="suggestion_list" ></div> --}}
                        </div>
                    </div>
                </div>

               
            </div>

                <div class="widgetbox-inverse">

                    <div class="headtitle">

                        <h4 class="widgettitle">Location to Customer Group Mapping <sup class="astric">*</sup></h4>

                    </div>

                    <div class="widgetcontent overflow-scroll">    

                        <table class="table table-bordered responsive" id="contactTable">

                            <thead>
                                <tr>
                                    <th width="5%"><input type="checkbox" name="checkall" class="simple-check" id="checkall"/></th>
                                    <th>Customer Group</th>
                                </tr>

                            </thead>

                            <tbody>
                                @forelse($CustomerGroup as $key => $val)
                                
                                <tr>
                                        
                                        <input type="hidden" name="cid[]"  value={{ $key }}>

                                        <td><input type="checkbox" name="customer_group_id[]" class="simple-check" id="customer_group_id_{{ $val->id }}" value="{{ $val->id }}"/></td>
                                        <td> <input type="hidden" name="customer_group_name[]" id="customer_group_name" value="{{ $val->customer_group_name }}"> {{ $val->customer_group_name }} </td>

                                       
                                        
                                    </tr>

                                @empty
                                
                                {{-- <tr class="centeralign" id="noData">

                                    <td colspan="8">No Data Available!</td>

                                </tr> --}}

                                @endforelse

                            </tbody>

                        </table>

                       

                    </div>

                </div>



                <div class="row">
                    <div class="span-6">            
                        <div class="par control-group form-control">
                            <label class="control-label"></label>
                            <div class="controls">
                                    <span class="formwrapper"> 
                                        <button type="submit" class="btn btn-primary">{{ config('define.value.add') }}</button>
                            </div>
                        </div>
                    </div>
            
                </div>

        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection



@section('scripts')

<script>

// var calcDataTableHeight = function () {
// return jQuery(window).height() * 55 / 100;
// };

table = jQuery('#contactTable').DataTable({
    responsive: true,
    // "scrollX":true,
    pageLength : 50,  
    // "order": false,  
    // "order": [ [ 3, 'asc' ],[ 2, 'asc' ]],
    "oLanguage": {
      "sSearch": "Search :"
      },
    // old code
    //   columnDefs: [{
    //         targets: 0, // Assuming the checkbox is in the first column
    //         orderDataType: 'dom-checkbox'
    //     }]
    columnDefs: [{
        targets: 0,
        "orderable": false,
         orderDataType: 'dom-checkbox'
    }]
    //  "sScrollY": calcDataTableHeight(), 
});
        
   
jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) >= 0.001;
});  

var validator = jQuery("#addLocationCusMappingForm").validate({

    rules: {

        location_id: {
            required: true,
        },

        
        'customer_group_id[]': {        
            required: true,
        },



    },
        
    // },

    messages: {

        location_id: {
            required:"Please Select Location",
        },

        'customer_group_id[]': {           
            required: "Please Select Customer Group for Mapping"            

        }, 

   
        },
        errorPlacement: function (error, element) {
                jAlert(error.text());
                return false;
        },

    // },

    submitHandler: function(form) {

        
        customer_group_data = [];
            var index = 0;

      
        table.$('tr').each(function (e) {

        var cusGrpId = jQuery(this).find('input[name="customer_group_id[]"]');


        if (jQuery(cusGrpId).is(':checked')) {
            cusGrpId = jQuery(cusGrpId).val();                        
            customer_group_data[index] = { 'customer_group_id': cusGrpId, };
            index++;
        }
        });

        var data = new FormData(document.getElementById('addLocationCusMappingForm'));
        var formValue = Object.fromEntries(data.entries());

        formValue = Object.assign(formValue, { 'customer_group_data': JSON.stringify(customer_group_data) });  
        var formdata = new URLSearchParams(formValue).toString();

        jQuery.ajax({

            url: "{{ route('store-location_customer_group_mappning') }}",

            type: 'POST',

            data: formdata,
            // data: item_data,

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if(data.response_code == 1){         

                    toastSuccess(data.response_message,nextFn);
                    function nextFn(){                        
                        window.location.reload();

                    };                    

                }else{
                    toastError(data.response_message);
                }

            },

            error: function (jqXHR, textStatus, errorThrown){

                var errMessage = JSON.parse(jqXHR.responseText);            

                if(errMessage.errors){

                    validator.showErrors(errMessage.errors);
                    

                }else if(jqXHR.status == 401){

                    toastError(jqXHR.statusText);

                }else{

                    toastError('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });

    }

});





jQuery('#checkall').click(function(){
    console.log(jQuery(this).is(':checked'))
    if(jQuery(this).is(':checked')){
        jQuery("#contactTable").find("[id^='customer_group_id_']:not(.in-use)").prop('checked',true).trigger('change');
    }else{
        jQuery("#contactTable").find("[id^='customer_group_id_']:not(.in-use)").prop('checked',false).trigger('change');
    }

});


var headerOpt = { 'X-CSRF-TOKEN': jQuery('input[name="_token"]').val() };


// this code usind sort checked item first
jQuery.fn.dataTable.ext.order['dom-checkbox'] = function(settings, col) {
    return this.api().column(col, {order: 'index'}).nodes().map(function(td, i) {
        return jQuery('input', td).prop('checked') ? '0' : '1';
    });
};

function getExistCustomerGroup(){
    var location_id = jQuery('#location_id').val();
    var thisform = jQuery('#addLocationCusMappingForm');

    if(location_id != ""){
        jQuery.ajax({
            url: RouteBasePath + "/fetch_customer_group?id=" + location_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function(data) {
                if (data) {
                    table.$("[name='customer_group_id[]']").prop('checked', false).trigger('change');             

                    for (const key in data.customerGroup) {
                        var customer_group_id = data.customerGroup[key].customer_group_id;                       
                         table.$(`#customer_group_id_${customer_group_id}`).prop('checked', true);

                         if (data.customerGroup[key].in_use == true) {
                            table.$(`#customer_group_id_${customer_group_id}`).prop('checked', true).trigger('change').attr('readonly', true);
                            }
                    }
                    table.draw();

                }
            }
        });
    }
}



// Initialize DataTable
var table = jQuery('#contactTable').DataTable();

// Function to bind events
function bindEvents() {
    jQuery("[id^='customer_group_id_']").off('click').on('click', function(){
        if (jQuery(this).prop('checked') == true) {
            jQuery(this).closest('tr').find("#customer_group_id_").select();          
        }
    });
}

// Bind events on document ready
jQuery(document).ready(function() {
    setTimeout(() => {
    jQuery('#item_name').trigger('liszt:activate');
}, 100);
    bindEvents();
});

// Bind events every time the table is redrawn
table.on('draw', function() {
    bindEvents();
});
</script>

@endsection