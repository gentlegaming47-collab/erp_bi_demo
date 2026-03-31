@extends('layouts.app',['pageTitle' => 'Raw Material Group'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-raw_material_group') }}">Raw Material Group</a> <span class="separator"></span></li>

    <li>Add Raw Material Group</li>

</ul>

@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-raw_material_group') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Raw Material Group</h4>

    </div>

    <div class="widgetcontent">

        <form id="addRawmaterialForm" class="stdform" method="post">

            @csrf

                <div class="par control-group">

                        <label class="control-label" for="raw_material_group_nm">Raw Material Group </label>

                    <div class="controls">

                        <input type="text" name="raw_material_group_nm" id="raw_material_group_nm" onkeyup="suggestRawMaterialGroup(event,this)" class="input-large auto-suggest" autocomplete="nope" autofocus placeholder="Enter Raw Material Group"/>

                        <div id="raw_material_group_nm_list" class="suggestion_list" ></div>

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

<script>


var validator = jQuery("#addRawmaterialForm").validate({
        onkeyup: false,
        onfocusout: false,
        rules: {

            raw_material_group_nm: {

                required: true,

                maxlength: 255

            }           

        },

        messages: {

            raw_material_group_nm: {

                required: "Please enter Raw Material Group",

                maxlength: "Maximum 255 characters allowed"

            }

        },
		errorPlacement: function (error, element) {
            // Customize the error message placement
            error.insertAfter(element); // You can change this to your preferred placement method
			console.log("3");
            element.focus(); // Focus on the field when the error appears
        },
        submitHandler: function(form) {

            

            var formdata = jQuery('#addRawmaterialForm').serialize();

            jQuery.ajax({

                url: "{{ route('store-raw-material-group') }}",

                type: 'POST',

                data: formdata,

               

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if(data.response_code == 1){

                        
                        toastSuccess(data.response_message,nextFn);
                         function nextFn(){
                            
                            document.getElementById("addRawmaterialForm").reset();

                            validator.resetForm();
    
                            jQuery('input#raw_material_group_nm').focus();
                        }


                    }else{

                        toastError(data.response_message);

                    }

                    

                },

                error: function (jqXHR, textStatus, errorThrown){

                    var errMessage = JSON.parse(jqXHR.responseText);

                   

                    if(errMessage.errors){

                        validator.showErrors(errMessage.errors);
						console.log("1");
                        

                    }else if(jqXHR.status == 401){

                        console.log("2");           

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