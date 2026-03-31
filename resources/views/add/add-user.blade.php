@extends('layouts.app',['pageTitle' => 'User'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-user') }}">User</a> <span class="separator"></span></li>

    <li>Add User</li>

</ul>

@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-user') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add User</h4>

    </div>

    <div class="widgetcontent">

        <form id="addUserForm" class="stdform" method="post" enctype="multipart/form-data">

            @csrf

            <div class="row">
                <div class="span-6">

                    <div class="par control-group form-control">

                            <label class="control-label" for="firstname">User Name <sup class="astric">*</sup></label>

                        <div class="controls">
                            <span class="formwrapper"> 
                              <input type="text" name="user_name" id="user_name" onkeyup="suggestUserName(event,this)" class="input-large auto-suggest" autocomplete="nope" placeholder="Enter User Name"  autofocus/>
                            </span>
                            <div id="user_name_list" class="suggestion_list" ></div>

                        </div>

                    </div>
                </div>


                <div class="span-6">
                    <div class="par control-group form-control">

                            <label class="control-label" for="password">Password <sup class="astric">*</sup></label>

                        <div class="controls">
                            <span class="formwrapper"> 
                                <input type="password" name="password" id="password" class="input-large" autocomplete="new-password" placeholder="Enter Password"  />  <!----autocomplete="new-password"---->
                            </span>
                        <!-- <i data-placement="right" rel="tooltip" href="" data-original-title="Format (1 : minimum 1 Uppercase, 2 : minimum 1 Lowercase, 3 : minimum 1 special character)" class="iconfa-exclamation-sign"></i> -->

                        

                        </div>

                    </div>
                </div>



                <!-- <div class="par control-group">

                        <label class="control-label" for="firstname"> Person Name </label>

                    <div class="controls"><input type="text" name="person_name" id="person_name" class="input-large" />

                        

                    </div>

                </div> -->


                <div class="span-6">
                    <div class="par control-group form-control">

                            <label class="control-label" for="person_name">Person Name <sup class="astric">*</sup></label>

                        <div class="controls">
                            <span class="formwrapper">
                                <input type="text" name="person_name" id="person_name" class="input-large" placeholder="Enter Person Name"  autofocus/>
                            </span>
                        

                        </div>

                    </div>
                </div>

                <div class="span-6">

                    <div class="par control-group form-control">

                            <label class="control-label" for="mobile_no">Mobile No.</label>

                        <div class="controls">
                            <span class="formwrapper"> 
                                <input type="text" name="mobile_no" id="mobile_no" class="input-large only-numbers" placeholder="Enter Mobile No." autofocus/>
                            </span>
                        

                        </div>

                    </div>
            </div>
            </div>


            <div class="row">              


                <div class="span-6">
                    <div class="par control-group form-control">

                        <label class="control-label" for="email">Email ID</label>

                            <div class="controls">
                                    <span class="formwrapper"> 
                                        <input type="email" name="email" id="email" class="input-large" placeholder="Enter Email ID" />
                                    </span>
                            </div>

                    </div>
                </div>



                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="so_customer_id">User Type <sup class="astric">*</sup></label>
                        <div class="controls">
                            <span class="formwrapper">
                                <select name="user_type" id="user_type" class="chzn-select">
                                    <option value="">Select Type</option>  
                                        <option value="operator">Operator</option>
                                        <option value="state_manager">State Manager</option>
                                        <option value="state_coordinator">State Coordinator</option>
                                        <option value="zonal_manager">Zonal Manager</option>
                                        <option value="director">Managing Director</option>
                                        <option value="general_manager">General Manager</option>
                                <select>
                            </span>
                        </div>
                    </div>
                </div>

               
                <div class="span-6">
                    <div class="par control-group form-control">

                        <label class="control-label" for="status">Status </label>
 
                        <div class="controls">
                            <span class="formwrapper"> 
                                <select data-placeholder="Select Status" name="status" id="status" class="chzn-select">

                                    <option value="active">Active</option> 

                                    <option value="deactive">Deactive</option>

                                </select>   
                            </span>

                        </div>

                    </div>
                </div>

                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="allow_multiple_veh_entry"></label>
                        <div class="controls">
                            <span class="formwrapper">
                                <input type="checkbox" name="allow_multiple_veh_entry" id="allow_multiple_veh_entry" value="Yes"/> Allow Multiple Veh. Entry
                            </span>
                        </div>
                    </div>
                </div>
            </div>


          
                <div class="widgetbox-inverse">

                    <div class="headtitle">

                        <h4 class="widgettitle">Location Map <sup class="astric">*</sup></h4>

                    </div>

                    <div class="widgetcontent">

    

                        <table class="table table-bordered responsive" id="contactTable">

                            <thead>

                                <tr>

                                    <th width="5%"><input type="checkbox" name="checkall" class="simple-check" id="checkall"/></th>

                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>State</th>
                                    <th>District</th>
                                    <th>Taluka</th>
                                    <th>Village</th>   

                                </tr>

                            </thead>

                            <tbody>
                                @forelse($units as $unit)

                                    <tr>

                                        <td><input type="checkbox" name="unit_ids[]" class="simple-check" id="unit_ids_{{ $unit->id }}" value="{{ $unit->id }}"/></td>
                                    
                                        <td>{{ $unit->location_name }}</td>                      
                                        <td>{{ ucfirst(trans($unit->type)) }}</td>
                                        <td>{{ $unit->state_name }}</td>
                                        <td>{{ $unit->district_name }}</td>
                                        <td>{{ $unit->taluka_name }}</td>
                                        <td>{{ $unit->village_name }}</td>

                                    </tr>

                                @empty

                                <tr class="centeralign" id="noData">

                                    <td colspan="2">No Unit Available!</td>

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





  jQuery.validator.addMethod("varifymobile", function(value, element) {

function testMobile(val){

     var format = /[`!@#$%^&*()_,\=\[\]{};':"\\|.<>\/?~]/;

       if(format.test(value) == true){

           return false;

       }else{

           return true;

       }

   }

   return this.optional( element ) || testMobile(value);

   

}, "only 0-9 and ('-','+') allowed");


var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};



jQuery.validator.addMethod("varifypassword", function(value, element) {

  let passRegex = /((?=.*[A-Z])(?=.*[a-z])(?=.*\W))/;

  return this.optional( element ) || passRegex.test( value );

}, 'Please enter a valid password format.');


jQuery.validator.addMethod("varifypassword", function(value, element) {

let passRegex = /((?=.*[A-Z])(?=.*[a-z])(?=.*\W))/;

return this.optional( element ) || passRegex.test( value );

}, 'Please enter a valid password format.');



    var checkData = [];

jQuery(document).ready(function(){

    //auto suggestion off for select2
    jQuery(document).on( 'focus', ':input', function(){
        jQuery(this).attr( 'autocomplete', 'new-password' );
    }); 
    jQuery( "input.chzn-select" ).attr( 'autocomplete', 'new-password' );

    jQuery('#checkall').click(function(){

        if(jQuery(this).is(':checked')){

            jQuery("[id^='unit_ids_']").prop('checked',true);

        }else{

            jQuery("[id^='unit_ids_']").prop('checked',false);

        }

    });



var validator = jQuery("#addUserForm").validate({

		rules: {

            user_name: {

                required: true,

                maxlength: 500

            },

            
            user_type:{

                required: true,
            },

            password: {

                required: true,

                // minlength: 8,

                // varifypassword: true

            },

            person_name:{

                required: true,

            },

            mobile_no:{

             //   required: true,

                varifymobile:true

            },
        

            'unit_ids[]': {

                required: true

            },

            


		},

		messages: {

			user_name: {

			    required:"Please enter user name",

			    maxlength: "Maximum 500 characters allowed"		    

			},

            user_type:{
                
                required: "Please Select User Type",
            },

            password: {

                required: "Please Enter Password",

                // minlength: "Password must be minimum 8 characters long",

                // varifypassword: "Please enter valid password format"

            },

            person_name:{

                required: "Please Enter Person Name",

            },

            // mobile_no:{

            //     required: "Please Enter Mobile No.",

            // },

            'unit_ids[]': {

                required: "Please Select Atleast One Location"

            }

		},

        submitHandler: function(form) {



            var formdata = jQuery('#addUserForm').serialize();

            jQuery.ajax({

                url: "{{ route('store-user') }}",

                type: 'POST',

                data: formdata,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if(data.response_code == 1){

                        

                        toastSuccess(data.response_message,nextFn);
                        function nextFn(){

 
                            validator.resetForm();

                            document.getElementById("addUserForm").reset();

                            jQuery('input#user_name').focus();

                            jQuery('#signature_image_doc').val('');

                            jQuery('#signature_image_prev').attr('href','#');

                            jQuery('#signature_image_prev').removeClass('i-block').addClass('hide');

                              jQuery('#addUserForm').find('#user_type').val('').trigger('liszt:updated');
                        

                            jQuery('.remove-file').removeClass('i-block').addClass('hide');

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

});


function suggestUserName(e,$this){

   var keyevent = e

  if(keyevent.key != "Tab"){

    jQuery("#user_name").addClass('file-loader');

    var search = jQuery($this).val();



    jQuery.ajax({

        url: "{{ route('username-list') }}?term="+encodeURI(search),

        type: 'GET',

        dataType: 'json',

        processData: false,

        success: function (data) {

            jQuery("#user_name").removeClass('file-loader');

            if(data.response_code == 1){

                jQuery('#user_name_list').html(data.usernameList);

            }else{

                // toastError(data.response_message);
                jAlert(data.response_message);

            }

        },

        error: function (jqXHR, textStatus, errorThrown){

            jQuery("#user_name").removeClass('file-loader');

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

}


jQuery('#user_type').on('change', function() {
    if(jQuery(this).val() == 'operator'){
        jQuery("[id^='checkall']").attr('disabled',true);  

        jQuery('#contactTable tbody [type="checkbox"]').prop('checked',false);
        jQuery('#checkall').prop('checked',false);

        jQuery('#contactTable tbody [type="checkbox"]').click(function(){
            if(jQuery('#user_type').val() == 'operator'){
                jQuery('#contactTable tbody [type="checkbox"]').prop('checked',false);
                jQuery(this).prop('checked',true);
            }
        });

    }else{
        jQuery("[id^='checkall']").attr('disabled',false);
     

    }
});




</script>

@endsection