@extends('layouts.app',['pageTitle' => 'Add Item to Item Mapping '])



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

    <li><a href="{{ route('manage-item_raw_material_mapping') }}">Item to Item Mapping </a> <span class="separator"></span></li>

    {{-- <li>Add Raw Material Mapping Group</li> --}}
    <li>Add Item to Item Mapping </li>

</ul>

@endsection

<?php
    $changedItemIds = [];
?>

@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-item_raw_material_mapping') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Item to Item Mapping </h4>
        {{-- <h4 class="widgettitle">Add Item Material Mapping Group</h4> --}}

    </div>

    <div class="widgetcontent">

        <form id="addItemRawmaterialMappingForm" class="stdform" method="post">

            @csrf

            <div class="row">
                <div class="span-6">
                    <div class="par control-group form-control">
                            <label class="control-label" for="item_name">Item Name <sup class="astric">*</sup></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                <select name="item_id" id="item_name" class="chzn-select" onchange="fetch_group_code_unit_data(),getExistItemQty(),getSecondUnit()">
                                        <option value="">Select Item Name</option>

                                            @forelse (getParticularItem($changedItemIds) as $item)
                                    
                                            <option value="{{ $item->id }}" data-secondary_unit="{{$item->secondary_unit}}">{{ $item->item_name }}</option>

                                            @empty

                                        @endforelse

                                    </select>
                                </span>
                            {{-- <div id="item_name_list" class="suggestion_list" ></div> --}}
                        </div>
                    </div>
                </div>

                <div class="span-6" id="hide">
                    <div class="par control-group form-control">
                            <label class="control-label" for="item_name">Item Detail Name <sup class="astric">*</sup></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                    <select name="item_details_id" id="item_details_id" class="chzn-select" onchange="getDetailsExistItemQty()">
                                        <option value="">Select Item Name</option>  
                                    </select>
                                </span>
                            {{-- <div id="item_name_list" class="suggestion_list" ></div> --}}
                        </div>
                    </div>
                </div>

                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="item_group">Item Group </label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                <input type="text" name="item_group_id" id="item_group_id" class="input-large auto-suggest" autocomplete="nope" disabled />
                                </span>
                        </div>
                    </div>
                </div>
         
                    <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="item_code">Item Code </label>
                        <div class="controls">
                            <span class="formwrapper"> 
                                <input type="text" name="item_code" id="item_code"  class="input-large auto-suggest" autocomplete="nope" disabled />
                            </span>
                        </div>
                    </div>
                </div>

                <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label" for="unit">Unit </label>
                    <div class="controls">
                        <span class="formwrapper"> 
                            <input type="text" name="unit" id="unit"  class="input-large auto-suggest" autocomplete="nope" disabled />
                        </span>
                        {{-- <div id="item_name_list" class="suggestion_list" ></div> --}}
                    </div>
                </div>
                </div>
            </div>

                <div class="widgetbox-inverse">

                    <div class="headtitle">

                        <h4 class="widgettitle"> Item to Item Mapping <sup class="astric">*</sup></h4>
                        {{-- <h4 class="widgettitle">Raw Material Mapping</h4> --}}

                    </div>

                    <div class="widgetcontent overflow-scroll">    

                        <table class="table table-bordered responsive" id="contactTable">

                            <thead>

                                <tr>

                                    <th width="5%"><input type="checkbox" name="checkall" class="simple-check" id="checkall"/></th>

                                    <th >Item </th>
                                    <th>Code</th>
                                    <th>Group</th>
                                    <th >Map Qty. </th>
                                    <th >Unit</th>

                                </tr>

                            </thead>

                            <tbody>
                                @forelse($itemMaterial as $key => $material)
                                
                                <tr>
                                        
                                        <input type="hidden" name="mid[]"  value={{ $key }}>

                                        <td><input type="checkbox" name="material_ids[]" class="simple-check" id="material_ids_{{ $material->id }}" value="{{ $material->id }}"/></td>

                                        <td> <input type="hidden" name="raw_material_id[]" id="raw_material_id" value="{{ $material->id }}"> {{ $material->item_name }} </td>

                                        <td> <input type="hidden" name="item_code[]" id="rate_per_unit" value="{{ $material->id }}"> {{ $material->item_code }} </td>


                                        <td> <input type="hidden" name="mapping_raw_material[]" id="mapping_raw_material" value="{{ $material->id }}"> {{ $material->item_group_name }} </td>

                                       

                                        

                                        <td> <input type="text"  class="input-mini isNumberKey" name="raw_material_qty[]" id="raw_material_qty" data-qty="{{ $material->id }}" onblur="formatPoints(this,3)"  disabled> </td> 

                                        <td> <input type="hidden" name="rate_per_unit[]" id="rate_per_unit" value="{{ $material->id }}"> {{ $material->unit_name }} </td>
                                    
                                        
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
    //    columnDefs: [{
    //         targets: 0, // Assuming the checkbox is in the first column
    //          orderDataType: 'dom-checkbox'
    //      }]
    columnDefs: [{
        targets: 0,
        "orderable": false,
         orderDataType: 'dom-checkbox'
    }]
    //  "sScrollY": calcDataTableHeight(), 
});

//jQuery('#contactTable').on('click', 'input[type="checkbox"]', function() {
  //      table.draw();
//});
              
   
jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) >= 0.001;
});  

jQuery.validator.addMethod("validateDetailItem", function (value, element, params) {
     var item_scond_unit = jQuery('#item_name option:selected').data('secondary_unit');
  
    if (item_scond_unit == 'Yes') {
            return value !== "";
    }
    return true;
}, "Please Select Item Detail.");

var validator = jQuery("#addItemRawmaterialMappingForm").validate({

    rules: {

        item_id: {

            required: true,

            // maxlength: 500

        },

        item_details_id: {
            validateDetailItem: true,
        },

        
    'material_ids[]': {

        
            required: true

        },



        "raw_material_qty[]": {

            required: function (e) {

                if (jQuery(e).prop('disabled')) {

                    return false;

                } else {

                    return true;

                }

            },

            notOnlyZero: '0.001',

            },

    },
        
    // },

    messages: {

        item_id: {

            required:"Please Select Item",

            maxlength: "Maximum 500 characters allowed"		    

        },

        'material_ids[]': {

            //required: "Please Select Item Mapping"
            required: "Please Select Item Name for Mapping"
            

        }, 

    'raw_material_qty[]' :
        {
            required: "Please Enter Item Mapping Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.'
        }
        },
        errorPlacement: function (error, element) {
                jAlert(error.text());
                return false;
        },

    // },

    submitHandler: function(form) {

        
        item_data = [];
            var index = 0;

        // var formdata = jQuery('#addItemRawmaterialMappingForm').serialize();

        // var params = table.$('input,select,textarea').serializeArray();

        table.$('tr').each(function (e) {

        var matId = jQuery(this).find('input[name="material_ids[]"]');


        if (jQuery(matId).is(':checked')) {
            matId = jQuery(matId).val();
            raw_material_qty = jQuery(this).find('input[name="raw_material_qty[]"]').val();               
            item_data[index] = { 'material_ids': matId, 'raw_material_qty': raw_material_qty != null ? parseFloat(raw_material_qty).toFixed(3) : "", };
            index++;
        }
        });

        var data = new FormData(document.getElementById('addItemRawmaterialMappingForm'));
        var formValue = Object.fromEntries(data.entries());

        formValue = Object.assign(formValue, { 'item_data': JSON.stringify(item_data) });  
        var formdata = new URLSearchParams(formValue).toString();

        jQuery.ajax({

            url: "{{ route('store-item-raw-matrial-mapping') }}",

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


                        validator.resetForm();

                        document.getElementById("addItemRawmaterialMappingForm").reset();

                        jQuery('input#item_name').focus();

                        jQuery('#addItemRawmaterialMappingForm').find('#item_name').val('').trigger('liszt:updated');

                        jQuery('#item_group_id').val('');

                        jQuery('#item_code').val('');
                        
                        jQuery('#unit').val('');
                        

                        
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

    if(jQuery(this).is(':checked')){

        jQuery("#contactTable").find("[id^='material_ids_']:not(.in-use)").prop('checked',true).trigger('change');

        jQuery("#contactTable").find("[id^='raw_material_qty']:not(.in-use)").prop('disabled',false).trigger('change');


    }else{

        jQuery("#contactTable").find("[id^='material_ids_']:not(.in-use)").prop('checked',false).trigger('change');

        jQuery("#contactTable").find("[id^='raw_material_qty']:not(.in-use)").prop('disabled',true).trigger('change');

        jQuery("#contactTable").find("[id^='raw_material_qty']:not(.in-use)").val("");

        

    }

});


var headerOpt = { 'X-CSRF-TOKEN': jQuery('input[name="_token"]').val() };
function fetch_group_code_unit_data() {


let item_name = jQuery("#item_name option:selected").val();
if (item_name != "") {

    jQuery('#item_group_id').addClass('file-loader');
    jQuery('#item_code').addClass('file-loader');
    jQuery('#unit').addClass('file-loader');

    jQuery.ajax({
        url: RouteBasePath + "/fetch_Groupname_Code_Unit?id=" + item_name,
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function(data) {

            if (data.response_code == 1) {

                jQuery('#item_code').val(data.item_data.item_group_code);
                jQuery('#item_code').removeClass('file-loader');

                jQuery('#item_group_id').val(data.item_data.item_group_name);
                jQuery('#item_group_id').removeClass('file-loader');


                jQuery('#unit').val(data.item_data.unit_name);
                jQuery('#unit').removeClass('file-loader');


            } else {
                console.log(data.response_message)
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            jQuery('#item_code').remove('file-loader');
            console.log('Field To Get Latest  No.!')

            jQuery('#item_group_id').remove('file-loader');
            console.log('Field To Get Latest  No.!')

            jQuery('#unit').remove('file-loader');
            console.log('Field To Get Latest  No.!')
        }
    });
}
}

// this code usind sort checked item first
jQuery.fn.dataTable.ext.order['dom-checkbox'] = function(settings, col) {
    return this.api().column(col, {order: 'index'}).nodes().map(function(td, i) {
        return jQuery('input', td).prop('checked') ? '0' : '1';
    });
};

function getExistItemQty(){
    var item_name = jQuery('#item_name').val();
    var thisform = jQuery('#addItemRawmaterialMappingForm');

    var item_scond_unit = jQuery('#item_name option:selected').data('secondary_unit');

    if(item_scond_unit != 'Yes'){
        if(item_name != ""){
            jQuery.ajax({
                url: RouteBasePath + "/fetch_item_qty?id=" + item_name,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function(data) {
                    updateTable(data);
                }
            });
        }
    }

    
}

function getDetailsExistItemQty(){
    var item_details_id = jQuery('#item_details_id').val();

    if(item_details_id != ""){
        jQuery.ajax({
            url: RouteBasePath + "/fetch_details_item_qty?item_details_id=" + item_details_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function(data) {
                updateTable(data);
                if(data.response_code !="")
                {
                    jQuery('#unit').val(data.secunit.unit_name);
                }
            }
        });
    }    
}

jQuery('div#hide').hide();

function getSecondUnit(){                      
        
    table.$("[name='material_ids[]']").prop('checked', false).trigger('change');
    table.$("[name='raw_material_qty[]']").val('').prop("disabled", true);
    table.draw();


    var item_scond_unit = jQuery('#item_name option:selected').data('secondary_unit');
    var item_id = jQuery('#item_name').val();

       if(item_scond_unit == 'Yes'){
        jQuery('div#hide').show();

        jQuery.ajax({
            url: RouteBasePath + "/get-item_details_data?item_id=" + item_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function(data) {
                if (data.response_code == 1) {

                          var  dropHtml = '<option value="">Select Item Name</option>';

                        if (data.item.length > 0) {                       

                            for (let indx in data.item) {
                                dropHtml += `<option value="${data.item[indx].item_details_id}">${data.item[indx].secondary_item_name} </option>`;
                            }
                        }

                        jQuery('#item_details_id').empty().append(dropHtml).trigger('liszt:updated');
                }else {
                    jQuery('#item_details_id').empty().append("<option value=''>Select Item Name</option>").trigger('liszt:updated');
                    
                }
            }
        });
    }else{
        jQuery('div#hide').hide();
        jQuery('#item_details_id').val('').trigger("liszt:updated");
    }

}


function updateTable(data){
    if (data) {
        var tbltr = '';
        if (data.item.length > 0) {                        

            for (let indx in data.item) {
                tbltr += `<tr>                                  
                            <td><input type="checkbox" name="material_ids[]" class="simple-check" id="material_ids_${ data.item[indx].id}" value="${ data.item[indx].id}"/></td>
                            <td><input type="hidden" name="raw_material_id[]" value="${data.item[indx].id}"> ${data.item[indx].item_name}</td>
                            <td><input type="hidden" name="item_code[]" value="${data.item[indx].id}"> ${data.item[indx].item_code}</td>
                            <td><input type="hidden" name="mapping_raw_material[]" value="${data.item[indx].id}"> ${data.item[indx].item_group_name}</td>
                            <td><input type="text" class="input-mini isNumberKey" name="raw_material_qty[]" data-qty="${data.item[indx].id}" onblur="formatPoints(this,3)" disabled></td>
                            <td><input type="hidden" name="rate_per_unit[]" value="${data.item[indx].id}"> ${data.item[indx].unit_name}</td>                                  
                        </tr>`;
            }
        
            if (jQuery.fn.DataTable.isDataTable('#contactTable')) {
                jQuery('#contactTable').DataTable().destroy();
            }

            jQuery('#contactTable').append(tbltr);
            table = jQuery('#contactTable').DataTable({
                responsive: true,
                pageLength : 50,   
                "oLanguage": {
                "sSearch": "Search :"
                }, 
                columnDefs: [{
                    targets: 0,
                    "orderable": false,
                    orderDataType: 'dom-checkbox'
                }]
            });                        
        }
        
        table.$("[name='material_ids[]']").prop('checked', false).trigger('change');
        table.$("[name='raw_material_qty[]']").val('').prop("disabled", true);

        for (const key in data.qty) {
            var materialId = data.qty[key].raw_material_id;
            table.$(`[data-qty="${materialId}"]`).val(data.qty[key].raw_material_qty != "" ? parseFloat(data.qty[key].raw_material_qty).toFixed(3) : "");
            table.$(`[data-qty="${materialId}"]`).prop("disabled", false);
            table.$(`#material_ids_${materialId}`).prop('checked', true);
        }
      
        table.draw();

    }
}

// Initialize DataTable
var table = jQuery('#contactTable').DataTable();

// Function to bind events
function bindEvents() {
    jQuery("[id^='material_ids_']").off('click').on('click', function(){
        if (jQuery(this).prop('checked') == true) {
            jQuery(this).closest('tr').find("#raw_material_qty").prop("disabled", false);          
            jQuery(this).closest('tr').find("#raw_material_qty").select();          
        } else {
            jQuery(this).closest('tr').find("#raw_material_qty").prop("disabled", true);
            jQuery(this).closest('tr').find("#raw_material_qty").val("");
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