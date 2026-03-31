@extends('layouts.app',['pageTitle' => 'Company Year'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li>Add Company Year</li>

</ul>

@endsection



@section('content')



<!--Modals -->



<!--Start Auth modal-->

<div aria-hidden="false" aria-labelledby="authLabel" role="dialog" class="modal over hide fade in" id="authModal">

    <div class="modal-header">

        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>

        <h3 id="authModalLabel">Confirm Password</h3>

    </div>

    <div class="modal-body">

        <form id="login" class="stdform" action="{{ route('login') }}" method="post">

            @csrf

            <div class="row">

                <div class="par control-group">

                        <label class="control-label" for="password">Password </label>

                    <div class="controls">

                        <input type="password" name="password" id="password" class="input-large" placeholder="Enter Password" autofocus tabindex="1"/>

                    </div>

                </div>   

            </div>

        </form>

    </div>

    <div class="modal-footer">

        <button class="btn btn-primary" id="submitAuthModal" type="submit" form="login" tabindex="1">Submit</button>

        <button data-dismiss="modal" type="button" class="btn" tabindex="1">Close</button>

    </div>

</div>

<!--End Auth modal-->



<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-company_year') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Company Year</h4>

    </div>

    <div class="widgetcontent">

        <form id="addCompanyYearForm" class="stdform" method="post">

            @csrf

            <div class="row">
                <div class="span-6">  
                    <div class="par control-group form-control">

                        <label class="control-label" for="type">Type <sup class="astric">*</sup></label>

                        <div class="controls">
                            <span class="formwrapper">
                            <select class="form-control chzn-select"  id="type" name="type" >

                                <option value="">Select Type</option>

                                <option value="forward">Forward</option>

                                <option value="reverse">Reverse</option>

                            </select>
                            </span>
                        </div>

                    </div>
                </div>
            </div>



            <div class="row">
                <div class="span-6">  

                    <div class="par control-group form-control">

                            <label class="control-label" for="startdate">Start Date </label>

                        <div class="controls">

                            <span class="formwrapper">

                                <input type="text" name="startdate" id="startdate" class="input-large" readonly/>

                            </span>

                        </div>

                    </div>
                </div>
            </div>



            <div class="row">
                <div class="span-6">  

                    <div class="par control-group form-control">

                            <label class="control-label" for="endate">End Date </label>

                        <div class="controls">

                            <span class="formwrapper">

                                <input type="text" name="enddate" id="enddate" class="input-large" readonly/>

                            </span>

                        </div>

                    </div>
                </div>
            </div>



                <input type="hidden" name="year" id="year"/>

                <input type="hidden" name="yearcode" id="yearcode"/>



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

jQuery(document).ready(function(){

 setTimeout(() => {
        jQuery('#type').trigger('liszt:activate');
    }, 100);  

jQuery('#authModal').on('show.bs.modal',function(e){ 

    setTimeout(() => {

        jQuery(this).find('#password').focus(); 

    },600); 

});



    

    jQuery('#type').change(function(){

        let sel = jQuery(this).find('option:selected').val();



        jQuery('#startdate').addClass('file-loader');

        jQuery('#enddate').addClass('file-loader');



        jQuery.ajax({

            url: "{{ route('get-company_year') }}?type="+sel,

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {



                jQuery('#startdate').removeClass('file-loader');

                jQuery('#enddate').removeClass('file-loader');



                if(data.response_code == 1){



                    jQuery('#yearcode').val(data.response_data.year_code);

                    jQuery('#year').val(data.response_data.year);

                    jQuery('#startdate').val(data.response_data.startdate);

                    jQuery('#enddate').val(data.response_data.enddate);

                }else{

                    console.log(data.response_message);

                }

                

            },

            error: function (jqXHR, textStatus, errorThrown){



                jQuery('#startdate').removeClass('file-loader');

                jQuery('#enddate').removeClass('file-loader');

                

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

    });

});



var validatorAuth = jQuery("#login").validate({

    rules: {

        password: "required"			

    },

    messages: {

        password: "Password is required"

    },

    submitHandler: function(form) {      

        var formdata = jQuery('#login').serialize();

        jQuery('#authModal').find('#submitAuthModal').addClass('btn-loader');

        jQuery.ajax({

            url: "{{ route('check-login') }}",

            type: 'POST',

            data: formdata,     

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery('#authModal').find('#submitAuthModal').removeClass('btn-loader');

                if(data.response_code == 1){

                    

                    document.getElementById("login").reset();

                    validatorAuth.resetForm();

                    jQuery('#authModal').modal('hide');

                    

                    var formdata = jQuery('#addCompanyYearForm').serialize();

                    jQuery.ajax({

                        url: "{{ route('store-company_year') }}",

                        type: 'POST',

                        data: formdata,

                       

                        dataType: 'json',

                        processData: false,

                        success: function (data) {

                            if(data.response_code == 1){

                                

                                // toastSuccess(data.response_message);
                                jAlert(data.response_message);

                                document.getElementById("addCompanyYearForm").reset();

                                validator.resetForm();

                                jQuery('#type').val('').trigger('liszt:updated');

                                jQuery('input#type').trigger('liszt:activate');

                        

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

                                           

                                // toastError(jqXHR.statusText);
                                jAlert(jqXHR.statusText);

                            }else{

                                // toastError('Something went wrong!');
                                jAlert('Something went wrong!');

        			            console.log(JSON.parse(jqXHR.responseText));

                            }

                        }

                    });



                }else{

                    // toastError(data.response_message);
                    jAlert(data.response_message);

                }

                

            },

            error: function (jqXHR, textStatus, errorThrown){

                jQuery('#authModal').find('#submitAuthModal').removeClass('btn-loader');

                var errMessage = JSON.parse(jqXHR.responseText);

                

                if(errMessage.errors){

                    validator.showErrors(errMessage.errors);

                    

                }else if(jqXHR.status == 401){

                                

                    // toastError(jqXHR.statusText);
                    jAlert(jqXHR.statusText);

                }else{

                    // toastError('Something went wrong!');
                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

});





var validator = jQuery("#addCompanyYearForm").validate({

		rules: {

            type: {

                required: true,

            }			

		},

		messages: {

			type: {

                required: "Please select type",

            }

		},

        submitHandler: function(form) {

            

            jQuery('#authModal').modal('show');

            

        }

	});

</script>

@endsection