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
    {{-- <li>Edit Item Raw Material Group</li> --}}
    <li>Edit Location to Customer Group Mapping </li>
</ul>

@endsection
@section('content')


<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="{{ route('manage-location_customer_group_mappning') }}" class="btn btn-inverse">Back</a>
        </div>
        <h4 class="widgettitle">Edit Location to Customer Group Mapping</h4>
    </div>
    	 <div class="widgetcontent">
        <form id="editLocationCusMappingForm" class="stdform" method="post">
            @csrf
            <input type="hidden" value="{{base64_decode($id)}}" name="id"/>


            <div class="row">
                <div class="span-6">
                    <div class="par control-group form-control">
                            <label class="control-label" for="location">Location <sup class="astric">*</sup></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                <select name="location_id" id="location_id" class="chzn-select">
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

                @php
                    $editItemIds = $getData->pluck('customer_group_id')->toArray();
                    $editItems = $CustomerGroup->filter(function ($material) use ($editItemIds) {
                        return in_array($material->id, $editItemIds);
                    });
                    $otherItems = $CustomerGroup->filter(function ($material) use ($editItemIds) {
                        return !in_array($material->id, $editItemIds);
                    });
                  
                    $sortedRawMaterials = $editItems->merge($otherItems);
                @endphp

                    <table class="table table-bordered responsive" id="contactTable">

                        <thead>
                            <tr>
                                <th width="5%"><input type="checkbox" name="checkall" class="simple-check" id="checkall"/></th>
                                <th>Customer Group</th>
                            </tr>

                        </thead>

                        <tbody>
                            {{-- @dd($sortedRawMaterials) --}}
                            @forelse($sortedRawMaterials as $key => $val)
                                <tr>
                                    <input type="hidden" name="customerId[]" id="customer_group_ids_{{ $key }}">

                                    <td><input type="checkbox" name="customer_group_id[]" class="simple-check" id="customer_group_id_{{ $val->id }}" data-number="{{ $key }}" value="{{ $val->id }}" data-ids="{{ $key }}"/> </td>
                                  
                                    <td><input type="hidden" name="customer_group_name[]" id="customer_group_name_{{ $key }}" value="{{ $val->customer_group_name }}"> {{ $val->customer_group_name }} </td>                                    
                                </tr>
                                @empty
                                <tr class="centeralign" id="noData">
                                    <td colspan="8">No Data Available!</td>
                                </tr>
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
                                    <button class="btn btn-primary checkUser">Update</button>
                        </div>
                    </div>
                </div>
        
            </div>


         </form>
      </div><!--widgetcontent-->
@endsection


@section('scripts')


<script>
 

 table= jQuery('#contactTable').DataTable({
    responsive: true,
    // "scrollX":true,
    pageLength : 50,
    // "order": false,  
    // "order": [ [ 3, 'asc' ],[ 2, 'asc' ]],
    "oLanguage": {
      "sSearch": "Search :"
      },
       columnDefs: [{
        targets: 0,
        "orderable": false,
         orderDataType: 'dom-checkbox'
    }]
});


jQuery('#checkall').click(function(){

if(jQuery(this).is(':checked')){

    jQuery("#contactTable").find("[id^='customer_group_id_']:not(.in-use)").prop('checked',true).trigger('change');

}else{

    jQuery("#contactTable").find("[id^='customer_group_id_']:not(.in-use)").prop('checked',false).trigger('change');
 
}

});






var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}'};

jQuery.ajax({

    url: "{{ route('get-location_customer_group_mappning',['id' => base64_decode($id) ]) }}",

    type: 'GET',

    headers:headerOpt,

    dataType: 'json',

    processData: false,

    success: function (data) {

        if(data.response_code == 1){
            var thisForm =  jQuery('#editLocationCusMappingForm');

         for (const key in data.location_to_customer_group_mapping) {
             
          
            thisForm.find('#location_id').val(data.location_to_customer_group_mapping[key].location_id).trigger("liszt:updated").trigger('change');

         
            thisForm.find(`#customer_group_id_${data.location_to_customer_group_mapping[key].customer_group_id}`).prop('checked',true);   



            if (data.location_to_customer_group_mapping[key].in_use == true) {
                thisForm.find('#location_id').trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                thisForm.find(`#customer_group_id_`+data.location_to_customer_group_mapping[key].customer_group_id).prop('checked', true).attr('readonly', true).addClass('in-use');

            }

        }
        }else{

			
			jAlert(data.response_message, 'Alert Dialog', function(r) {
				window.location.href = "{{ route('manage-item_raw_material_mapping')}}";
			});

        }   

    },

    error: function (jqXHR, textStatus, errorThrown){

        var errMessage = JSON.parse(jqXHR.responseText);       

        if(jqXHR.status == 401){ 
            // toastError(jqXHR.statusText);
            jAlert(jqXHR.statusText);
        }else{
            // toastError('Something went wrong!');
            jAlert('Something went wrong!');
            console.log(JSON.parse(jqXHR.responseText));
        }

    }

});


var validator = jQuery("#editLocationCusMappingForm").validate({

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

    var data = new FormData(document.getElementById('editLocationCusMappingForm'));
    var formValue = Object.fromEntries(data.entries());

    formValue = Object.assign(formValue, { 'customer_group_data': JSON.stringify(customer_group_data) });  
    var formdata = new URLSearchParams(formValue).toString();
      
            jQuery.ajax({
                url: "{{ route('update-location_customer_group_mappning') }}",
                type: 'POST',
                data: formdata,
                headers:headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if(data.response_code == 1){                      					
						jAlert(data.response_message, 'Alert Dialog', function(r) {
							window.location.href = "{{ route('manage-location_customer_group_mappning')}}";
						});
                    }else{
                        // toastError(data.response_message);
                        jAlert(data.response_message);

                    }   
                },

                error: function (jqXHR, textStatus, errorThrown){
                    var errMessage = JSON.parse(jqXHR.responseText);
                    if(errMessage.errors){

                        validator.showErrors(errMessage.errors);                        

                    }else if(jqXHR.status == 401){
                                 jAlert(jqXHR.statusText);
                        // toastError(jqXHR.statusText);
                    }else{      
                        jAlert('Something went wrong!');
                        // toastError('Something went wrong!');
                        console.log(JSON.parse(jqXHR.responseText));

                    }

                }

            });

        }

});









</script>

@endsection
