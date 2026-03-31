@extends('layouts.app',['pageTitle' => 'Item Raw Material Mapping Group '])

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
    {{-- <li>Edit Item Raw Material Group</li> --}}
    <li>Edit Item to Item Mapping </li>
</ul>

@endsection

<?php
    use App\Models\ItemRawMaterialMappingDetail;
    if(isset($id)){
        $locationCode = getCurrentLocation();
    
        $changedItemIds = ItemRawMaterialMappingDetail::
        leftJoin('items', 'items.id', '=', 'item_raw_material_mapping_details.item_id')
        ->where('item_raw_material_mapping_details.item_id', base64_decode($id))
        ->where(function($query) {
            $query->where('items.status', 'deactive')
                ->orWhere('items.service_item', 'Yes')
                ->orWhere('items.require_raw_material_mapping','No');
        })
        ->groupBy('item_raw_material_mapping_details.item_id')
        ->pluck('item_raw_material_mapping_details.item_id')
        ->toArray();

    }else {  
        $changedItemIds = [];
    }
?>

@section('content')

<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="{{ route('manage-item_raw_material_mapping') }}" class="btn btn-inverse">Back</a>
        </div>
        <h4 class="widgettitle">Edit Item to Item Mapping </h4>
    </div>
    	 <div class="widgetcontent">
        <form id="editItemRawmaterialMappingForm" class="stdform" method="post">
            @csrf
            <input type="hidden" value="{{base64_decode($id)}}" name="id"/>
            <input type="hidden" value="{{!empty($item_details_id) ? base64_decode($item_details_id) : ''}}" name="item_details_id"/>
            <input type="hidden" value="{{!empty($item_details_id) ? base64_decode($item_details_id) : ''}}" name="old_item_details_id"/>


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
                                {{-- <div id="item_name_list" class="suggestion_list" ></div> --}}
                                </span>
                            </div>
                        </div>
                </div>
            </div>

            <div class="widgetbox-inverse">

                <div class="headtitle">

                    <h4 class="widgettitle">Item to Item Mapping <sup class="astric">*</sup></h4>

                </div>

                <div class="widgetcontent overflow-scroll">

                @php
                    $editItemIds = $getData->pluck('raw_material_id')->toArray();
                    $editItems = $rawMaterial->filter(function ($material) use ($editItemIds) {
                        return in_array($material->id, $editItemIds);
                    });
                    $otherItems = $rawMaterial->filter(function ($material) use ($editItemIds) {
                        return !in_array($material->id, $editItemIds);
                    });
                    //$sortedRawMaterials = $rawMaterial->sortBy(function ($material) use ($editItemIds) {
                    //return in_array($material->id, $editItemIds) ? 0 : 1;
                    //});
                    $sortedRawMaterials = $editItems->merge($otherItems);
                @endphp

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
                            {{-- @dd($sortedRawMaterials) --}}
                            @forelse($sortedRawMaterials as $key => $material)
                                <tr>
                                    <input type="hidden" name="rawMappingID[]" id="raw_mapping_id_{{ $key }}">

                                    <td><input type="checkbox" name="material_ids[]" class="simple-check" id="material_ids_{{ $material->id }}" data-number="{{ $key }}" value="{{ $material->id }}" data-ids="{{ $key }}"/> </td>
                                    <input type="hidden" name="material_ids_hidden[]" id="material_ids_hidden_{{ $material->id }}" value="{{ $material->id }}">

                                    <td><input type="hidden" name="raw_material_id[]" id="raw_material_id_{{ $key }}" value="{{ $material->id }}"> {{ $material->item_name }} </td>

                                    <td> <input type="hidden" name="item_code[]" id="rate_per_unit" value="{{ $material->id }}"> {{ $material->item_code }} </td>

                                    <td><input type="hidden" name="mapping_raw_material[]" id="mapping_raw_material_{{ $key }}" value="{{ $material->id }}"> {{ $material->item_group_name }} </td>

                                    <td>
                                        <input type="text" class="input-mini isNumberKey" name="raw_material_qty[]" id="raw_material_qty_{{ $key }}" data-qty="{{ $material->id }}" onblur="formatPoints(this,3)" disabled>
                                    </td>
                                    
                            
                                    <td><input type="hidden" name="rate_per_unit[]" id="rate_per_unit{{ $key }}" value="{{ $material->id }}"> {{ $material->unit_name }} </td>
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

    jQuery("#contactTable").find("[id^='material_ids_']:not(.in-use)").prop('checked',true).trigger('change');

    jQuery("#contactTable").find("[id^='raw_material_qty']:not(.in-use)").prop('disabled',false).trigger('change');

  

    

}else{

    jQuery("#contactTable").find("[id^='material_ids_']:not(.in-use)").prop('checked',false).trigger('change');

      jQuery("#contactTable").find("[id^='raw_material_qty']:not(.in-use)").prop('disabled',true).trigger('change');

        jQuery("#contactTable").find("[id^='raw_material_qty']:not(.in-use)").val("");
}

});


// Adjust the table selector based on your table's actual ID or class
var tableSelector = '#contactTable';

jQuery(document).on('click', tableSelector + ' [id^="material_ids_"]', function() {
    var seq = jQuery(this).data("ids");
    var rawMaterialQty = jQuery("#raw_material_qty_" + seq);

    if (jQuery(this).prop('checked') == true) {
      //  console.log("click " + seq);
        rawMaterialQty.prop("disabled", false);
        rawMaterialQty.select();
    } else {
        rawMaterialQty.prop("disabled", true);
        rawMaterialQty.val("");
    }
});

   

jQuery(document).ready(function(){
    setTimeout(() => {
    jQuery('#item_name').trigger('liszt:activate');
}, 100);

var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}'};

var formId = jQuery('#editItemRawmaterialMappingForm').find('input:hidden[name="id"]').val();
var formDetailsId = jQuery('#editItemRawmaterialMappingForm').find('input:hidden[name="item_details_id"]').val();

if(formDetailsId != undefined && formDetailsId != ''){
    var formUrl = RouteBasePath + "/get-deteils_item-raw-matrial-mapping/" + formDetailsId;
}else{
    var formUrl = RouteBasePath + "/get-item_item_mapping/" + formId;
}


jQuery.ajax({

    // url:  "{{ route('get-item-raw-matrial-mapping',['id' => base64_decode($id) ]) }}",
    url:  formUrl,

    type: 'GET',

    headers:headerOpt,

    dataType: 'json',

    processData: false,

    success: function (data) {

        if(data.response_code == 1){
            var thisForm =  jQuery('#editItemRawmaterialMappingForm');

         for (const key in data.item_Raw_Material_Mapping) {
             
            //  console.table(data.item_Raw_Material_Mapping[key]);
            thisForm.find('#item_name').val(data.item_Raw_Material_Mapping[key].item_id).trigger("liszt:updated");

            thisForm.find('#item_group_id').val(data.item_Raw_Material_Mapping[key].item_group_id);
            
            thisForm.find('#item_code').val(data.item_Raw_Material_Mapping[key].item_code);
          

                // thisForm.find(`#raw_material_qty_`+data.item_Raw_Material_Mapping[key].raw_material_id).val(data.item_Raw_Material_Mapping[key].raw_material_qty);
            
                

                thisForm.find(`[data-qty="${data.item_Raw_Material_Mapping[key].raw_material_id}"]`).val(data.item_Raw_Material_Mapping[key].raw_material_qty != null ? parseFloat(data.item_Raw_Material_Mapping[key].raw_material_qty).toFixed(3) : "");   

                thisForm.find(`[data-qty="${data.item_Raw_Material_Mapping[key].raw_material_id}"]`).prop("disabled", false);  
                
                thisForm.find(`#material_ids_`+data.item_Raw_Material_Mapping[key].raw_material_id).prop('checked',true);   


                // if(data.item_Raw_Material_Mapping[key].raw_material_qty!=""){
                //     thisForm.find(`#material_ids_`+data.item_Raw_Material_Mapping[key].raw_material_id).prop('checked',true);  
                //     jQuery(`#raw_material_qty_${key}`).prop("disabled", false);   
         

                // }
            //  

            thisForm.find(`#raw_mapping_id_${key}`).val(data.item_Raw_Material_Mapping[key].id);

            // thisForm.find('#unit').val(data.item_Raw_Material_Mapping[key].unit);
            
            if(data.item_Raw_Material_Mapping[key].id == 1)
            {
                thisForm.find('.update').attr("disabled", true);
            }else{
                thisForm.find('.update').attr("disabled", false);
            }

            // setTimeout(()=> {

            
            // },1000)

        }

         thisForm.find('#item_name').trigger('change');

        getSecondUnit().done(function (resposne) {
                thisForm.find('#item_details_id').val(data.item_Raw_Material_Mapping[0].item_details_id).trigger("liszt:updated").trigger('change');
            });
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
jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) >= 0.001;
});  

// jQuery.validator.addMethod("validateDetailItem", function (value, element, params) {
//      var item_scond_unit = jQuery('#item_name option:selected').data('secondary_unit');
//     console.log("Item Secondary Unit:", item_scond_unit, "| Value:", value);
//     if (item_scond_unit == 'Yes') {
//             return value !== "";
//     }
//     return true;
// }, "Please Select Item Detail.");

var validator = jQuery("#editItemRawmaterialMappingForm").validate({


    rules: {
 

         item_id: {

            required: true,

            // maxlength: 500

            },

            item_details_id: {
               required: function (e) {
                    if(jQuery('#item_name option:selected').data('secondary_unit')  == 'Yes'){
                        if(jQuery('#item_details_id option:selected').val().trim() !== '' ){
                             return false;
                        }else{
                             return true;
                        }

                    }else{
                        return false;
                    }
                },
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

		messages: {

		 item_id: {

        required:"Please enter Item name",

        maxlength: "Maximum 500 characters allowed"		    

        },
         item_details_id: {
              required:"Please Select Item Detail",
         },

        'material_ids[]': {

            required: "Please Select Item Mapping"

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

        submitHandler: function(form) {

            item_data = [];
            var index = 0;

            // var formdata = jQuery('#editItemRawmaterialMappingForm').serialize();

          

            table.$('tr').each(function (e) {

            var matId = jQuery(this).find('input[name="material_ids[]"]');

            // console.log(matId);


            if (jQuery(matId).is(':checked')) {
                matId = jQuery(matId).val();
                raw_material_qty = jQuery(this).find('input[name="raw_material_qty[]"]').val();   

                item_data[index] = { 'material_ids': matId, 'raw_material_qty': raw_material_qty, };
                index++;
            }
            });

            var data = new FormData(document.getElementById('editItemRawmaterialMappingForm'));
            var formValue = Object.fromEntries(data.entries());

            formValue = Object.assign(formValue, { 'item_data': JSON.stringify(item_data) });  
            var formdata = new URLSearchParams(formValue).toString();


            jQuery.ajax({

                url: "{{ route('update-item-raw-matrial-mapping') }}",

                type: 'POST',

                data: formdata,

                headers:headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if(data.response_code == 1){

                        

                        // toastSuccess(data.response_message);
                        //jAlert(data.response_message);
						
						
						jAlert(data.response_message, 'Alert Dialog', function(r) {
							window.location.href = "{{ route('manage-item_raw_material_mapping')}}";
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
           // console.log('Field To Get Latest  No.!')

            jQuery('#item_group_id').remove('file-loader');
           // console.log('Field To Get Latest  No.!')

            jQuery('#unit').remove('file-loader');
            //console.log('Field To Get Latest  No.!')
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

function getSecondUnit(){
    table.$("[name='material_ids[]']").prop('checked', false).trigger('change');
    table.$("[name='raw_material_qty[]']").val('').prop("disabled", true);
    table.draw();

    var item_scond_unit = jQuery('#item_name option:selected').data('secondary_unit');
    var item_id = jQuery('#item_name').val();

       if(item_scond_unit == 'Yes'){
        jQuery('div#hide').show();

        return jQuery.ajax({
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
              // પહેલા table body સાફ કરો
            if (jQuery.fn.DataTable.isDataTable('#contactTable')) {
                jQuery('#contactTable').DataTable().destroy();
            }
            jQuery('#contactTable tbody').empty(); // જૂના rows remove કરો                        

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
        
            jQuery('#contactTable tbody').append(tbltr);

            
            // if (jQuery.fn.DataTable.isDataTable('#contactTable')) {
            //     jQuery('#contactTable').DataTable().destroy();
            // }

            // jQuery('#contactTable').append(tbltr);
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
              jQuery('#unit').val(data.qty[key].unit_name);
        }
        table.draw();

    }
}


</script>

@endsection
