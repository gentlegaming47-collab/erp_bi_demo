@extends('layouts.app',['pageTitle' => 'Raw Material'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-raw_material') }}">Raw Material</a> <span class="separator"></span></li>
    <li>Add Raw Material</li>
</ul>

@endsection
@section('content')
@include('modals.unit_modal')
@include('modals.raw_material_group')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-raw_material') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Raw Material</h4>

    </div>

    <div class="widgetcontent">

        <form id="addRawMaterialForm" class="stdform" method="post">

            @csrf

            <div class="row">

                <div class="span-4">

                    <div class="par control-group">

                        <label class="control-label" for="item">Raw Material</label>

                        <div class="controls">

                            <span class="formwrapper">

                                <input type="text" name="raw_material" id="raw_material" class="input-large auto-suggest" onkeyup="suggestRawMaterial(event,this)" autocomplete="nope" autofocus placeholder="Enter Raw Material"/>

                                <div id="raw_material_list" class="suggestion_list" ></div>

                            </span>

                        </div>

                    </div>

                </div>

               
                <div class="span-4">

                    <div class="par control-group">

                        <label class="control-label" for="customer_type">Raw Material Group</label>

                        <div class="controls">

                            <span class="formwrapper">

                            <select name="raw_material_group_id" id="raw_material_group_id" class="chzn-select mst-raw-material">
                                <option value="">Select Item Group</option>
                                    @forelse (getRawMaterialGroupData() as $raw_material_data)

                                    <option value="{{ $raw_material_data->id }}">{{ $raw_material_data->raw_material_group_nm }}</option>

                                    @empty

                                @endforelse

                            </select>
                            
                            @if(hasAccess('raw-material','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#rawMaterialGroup"></i></span>@endif

                            </span>

                            </span>

                        </div>

                    </div>

                </div>

           
            </div>


            <div class="row">

               
                <div class="span-4">

                    <div class="par control-group">

                        <label class="control-label" for="unit">Unit</label>

                        <div class="controls">

                            <span class="formwrapper">

                            <select name="unit_id" id="unit_id" class="chzn-select" >
                                <option value="">Select Unit</option>
                                    @forelse (getUnit() as $unit)

                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>

                                    @empty

                                @endforelse

                            </select>
                            
                            {{-- @if(hasAccess('units','add'))<span class="m-span"><i class="action-icon iconfa-plus" data-toggle="modal" data-target="#unitModal"></i></span>@endif --}}


                            </span>

                            </span>

                        </div>

                    </div>

                </div>

                <div class="span-4">

                    <div class="par control-group">

                        <label class="control-label" for="min_stock_qty">Min. Stock Qty.</label>

                        <div class="controls">

                            <span class="formwrapper">

                                <input type="text" name="min_stock_qty" id="min_stock_qty"  class="input-large auto-suggest allow_decimal" autocomplete="nope" autofocus placeholder="Enter Min Stock Qty"/>

                                <div id="item_list" class="suggestion_list" ></div>

                            </span>

                        </div>

                    </div>

                </div>
           
            </div>


            <div class="row">

                         
                <div class="span-4">

                    <div class="par control-group">

                        <label class="control-label" for="max_stock_qty">Max. Stock Limit</label>

                        <div class="controls">

                            <span class="formwrapper">

                                <input type="text" name="max_stock_qty" id="max_stock_qty" class="input-large auto-suggest mobile-f" autocomplete="nope" autofocus placeholder="Enter Max Stock Limit"/>

                                <div id="item_list" class="suggestion_list" ></div>

                            </span>

                            </span>

                        </div>

                    </div>

                </div>

                

                <div class="span-4">

                    <div class="par control-group">

                        <label class="control-label" for="re_order_qty">Re-Order Qty.</label>

                        <div class="controls">

                            <span class="formwrapper">

                                <input type="text" name="re_order_qty" id="re_order_qty" class="input-large auto-suggest mobile-f" autocomplete="nope" autofocus placeholder="Enter Re-Order Qty"/>

                                <div id="item_list" class="suggestion_list" ></div>

                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <div class="row">

                             
                <div class="span-4">

                    <div class="par control-group">

                        <label class="control-label" for="max_stock_qty">HSN Code</label>

                        <div class="controls">

                            <span class="formwrapper">

                                <select name="hsn_code" id="hsn_code" class="chzn-select">
                                    <option value="">Select HSN code</option>
                                        @forelse (getHsnCodes() as $hsn_code)
    
                                        <option value="{{ $hsn_code->id }}">{{ $hsn_code->hsn_code }}</option>
    
                                        @empty
    
                                    @endforelse
    
                                </select>

                            </span>

                            </span>

                        </div>

                    </div>

                </div>

                
                <div class="span-4">

                    <div class="par control-group">

                        <label class="control-label" for="rate_unit">Rate/Unit</label>

                        <div class="controls">

                            <span class="formwrapper">

                                <input type="text" name="rate_per_unit" id="rate_per_unit"  class="input-large auto-suggest allow_decimal" placeholder="Enter Rate/Unit" autocomplete="nope" autofocus/> &nbsp;&nbsp;Rs.

                                <div id="item_list" class="suggestion_list" ></div>

                            </span>

                        </div>

                    </div>

                </div>

            </div>

          




                <p class="stdformbutton">

                    <button class="btn btn-primary">{{ config('define.value.add') }}</button>

                </p>

        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection



@section('scripts')
<script type="text/javascript" src="{{ asset('views/js/item.js?ver='.getJsVersion()) }}"></script>

<script>

@include('modalsjs.unit_modal_js')
@include('modalsjs.raw_material_group_modal_js')
jQuery(".allow_decimal").on("input", function(evt) {
    var self = jQuery(this);
    self.val(self.val().replace(/[^0-9.]/g, ''));
    if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
    {
      evt.preventDefault();
    }
  });
var validator = jQuery("#addRawMaterialForm").validate({

		rules: {
            onkeyup: false,
            onfocusout: false,


            raw_material: {

                required: true,

                maxlength: 255

            },			
            raw_material_group_id  : {

                required: true,

            },				
            // unit_id  : {

            //     required: true,

            // },			
            min_stock_qty  : {

                // minstock: true,

            },			
            max_stock_qty  : {

                maxstock: true,

            },			
            re_order_qty  : {

                reorder: true,

            },			
            // rate_per_unit   : {

            //     rate: true,

            // },			
            // hsn_code   : {

            //     required: true,

            // },			
            // rate_per_unit   : {

            //     required: true,

            // },			
            		

		},

		messages: {

			raw_material: {

                required: "Please enter Raw Material",

                maxlength: "Maximum 255 characters allowed"

            },
			raw_material_group_id  : {

                required: "Please Select Raw Material Group",

            },			
			// unit_id  : {

            //     required: "Please enter Unit",

            // },
			// min_stock_qty  : {

            //     required: "Please enter Minimum qty",

            // },
			// max_stock_qty  : {

            //     required: "Please enter Maximum qty",

            // },
			// re_order_qty  : {

            //     required: "Please enter re order qty",

            // },
			// hsn_code   : {

            //     required: "Please enter HSN Code",

            // },
			// rate_per_unit   : {

            //     required: "Please enter Rate per unit",

            // },
			

		},
        submitHandler: function(form) {

            

            var formdata = jQuery('#addRawMaterialForm').serialize();
            console.log(formdata);
         
            jQuery.ajax({
                url: "{{ route('store-raw-material') }}",
                type: 'POST',
                data: formdata,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if(data.response_code == 1){
                       toastSuccess(data.response_message,nextFn);
                        function nextFn(){
                            document.getElementById("addRawMaterialForm").reset();
                            validator.resetForm();
                            jQuery('#addRawMaterialForm').find('#raw_material_group_id').val('').trigger('liszt:updated');
                            jQuery('#addRawMaterialForm').find('#unit_id').val('').trigger('liszt:updated');
                            jQuery('#addRawMaterialForm').find('#hsn_code').val('').trigger('liszt:updated');
                            jQuery('input#raw_material').focus();
                        }
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






</script>

@endsection