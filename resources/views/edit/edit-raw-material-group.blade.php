@extends('layouts.app',['pageTitle' => 'Raw Material Group'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-raw_material_group') }}">Raw Material</a> <span class="separator"></span></li>

    <li>Edit Raw Material</li>

</ul>



@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-raw_material_group') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit Raw material group</h4>

    </div>

    <div class="widgetcontent">

        <form id="editRawmaterialForm" class="stdform" method="post">

            @csrf

                <div class="par control-group">

                        <label class="control-label" for="raw_material_group_nm">Raw Marerial Group</label>

                        <input type="hidden" value="{{base64_decode($id)}}" name="id"/>

                    <div class="controls">

                        <input type="text" name="raw_material_group_nm" id="raw_material_group_nm" onkeyup="suggestRawMaterialGroup(event,this)" class="input-large auto-suggest" autocomplete="nope" autofocus/>

                        <div id="raw_material_group_nm_list" class="suggestion_list" ></div>

                    </div>

                </div>



                <p class="stdformbutton">

                    <button class="btn btn-primary update">Update</button>

                </p>

        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection



@section('scripts')

<script>



jQuery(document).ready(function(){

var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}'};

jQuery.ajax({

    url: "{{ route('get-raw-material-groups',['id' => base64_decode($id) ]) }}",

    type: 'GET',

    headers:headerOpt,

    dataType: 'json',

    processData: false,

    success: function (data) {

        if(data.response_code == 1){

           var thisForm =  jQuery('#editRawmaterialForm');

           thisForm.find('#raw_material_group_nm').val(data.raw_Material_Group_Data.raw_material_group_nm);

           thisForm.find('input:hidden[name="id"]').val(data.raw_Material_Group_Data.id);

            if(data.raw_Material_Group_Data.id == 1)
            {
                thisForm.find('.update').attr("disabled", true);
            }else{
                thisForm.find('.update').attr("disabled", false);
            }


        }else{

            // toastError(data.response_message);
            /*jAlert(data.response_message);

            setTimeout(() => {

                window.location.href = "{{ route('manage-country')}}";

            }, 800);*/
			
			jAlert(data.response_message, 'Alert Dialog', function(r) {
				window.location.href = "{{ route('manage-raw_material_group')}}";
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



var validator = jQuery("#editRawmaterialForm").validate({

        rules: {
             onkeyup: false,
            onfocusout: false,

            raw_material_group_nm: {

                required: true,

                maxlength: 255

            }           

        },

        messages: {

            raw_material_group_nm: {

                required: "Please enter Raw Material",

                maxlength: "Maximum 255 characters allowed"

            }

        },
          errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function(form) {

            var formdata = jQuery('#editRawmaterialForm').serialize();

            jQuery.ajax({

                url: "{{ route('update-raw-material-group') }}",

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
							window.location.href = "{{ route('manage-raw_material_group')}}";
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

</script>

@endsection