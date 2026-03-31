@extends('layouts.app',['pageTitle' => 'User'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-user') }}">User</a> <span class="separator"></span></li>

    <li>Edit User</li>

</ul>

@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

          <a href="{{ route('manage-user') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit User</h4>

    </div>

    <div class="widgetcontent">

        <form id="editUserForm" class="stdform" method="post" enctype="multipart/form-data">

            @csrf

              
            <div class="row">
                    <div class="span-6">
    
                        <div class="par control-group form-control">
    
                                <label class="control-label" for="firstname">User Name <sup class="astric">*</sup></label>
                            
                                <input type="hidden" value="{{base64_decode($id)}}" name="id"  id="id">
                                
                            <div class="controls">
                                <span class="formwrapper"> 
                                  <input type="text" name="user_name" id="user_name" onkeyup="suggestUserName(event,this)" class="input-large auto-suggest" autocomplete="nope" autofocus/>
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
                                    <input type="password" name="password" id="password" class="input-large"   autocomplete="new-password" />
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
                                    <input type="text" name="person_name" id="person_name" class="input-large" autofocus/>
                                </span>
                            
    
                            </div>
    
                        </div>
                    </div>

                    <div class="span-6">
    
                        <div class="par control-group form-control">

                                <label class="control-label" for="mobile_no">Mobile No. </label>

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
    
                            <label class="control-label" for="email">Email ID </label>
    
                                <div class="controls">
                                        <span class="formwrapper"> 
                                            <input type="email" name="email" id="email" class="input-large" placeholder="Enter Email ID"/>
                                        </span>
                                </div>
    
                        </div>
                    </div>
    
    
    
                    <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for="user_type">User Type <sup class="astric">*</sup></label>
                                <div class="controls">
                                    <span class="formwrapper"> 
                                        <select name="user_type" id="user_type" class="chzn-select">
                                            <option value="">Select User</option>                               
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

                        <h4 class="widgettitle">Location Map </h4>

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
                                        <button class="btn btn-primary checkUser">Update</button>
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

    var checkData = [];
jQuery(document).ready(function(){

    //auto suggestion off for select2
    jQuery(document).on( 'focus', ':input', function(){
        jQuery(this).attr( 'autocomplete', 'new-password' );
    }); 
    jQuery( "input.chzn-select" ).attr( 'autocomplete', 'new-password' );

    
    function checkUserName(user, userId)
    {
      
        if(user == "admin" && userId == 1)
        {
            jQuery("[id^='unit_ids_']").prop('checked',true);
            jQuery("#checkall").prop('checked',true);
        }else{
            jQuery("[id^='unit_ids_']").prop('checked',false);
        }
    }

    jQuery('#checkall').click(function(){

        if(jQuery(this).is(':checked')){

            jQuery("[id^='unit_ids_']").prop('checked',true);

        }else{

            jQuery("[id^='unit_ids_']").prop('checked',false);

        }

    });



jQuery.ajax({

    url: "{{ route('get-user',['id' => base64_decode($id) ]) }}",

    type: 'GET',

    dataType: 'json',

    processData: false,

    success: function (data) {

        // console.log(data);

        if(data.response_code == 1){

           var thisForm =  jQuery('#editUserForm');

       

           thisForm.find('input:hidden[name="id"]').val(data.user.id);

           thisForm.find('#user_name').val(data.user.user_name);

           thisForm.find('#person_name').val(data.user.person_name);

           thisForm.find('#mobile_no').val(data.user.mobile_no);
        

           thisForm.find('#email').val(data.user.email_id);

            checkUserName(data.user.user_name, data.user.id );
           

        //    $(document).on('click', '.update', function() {
        //     $("input[value='" + data.sex + "']").prop('checked', true);
        //     });

            
        //    console.log(data.user_units.company_unit_id);   

       
           for(k in data.user_units){                                               
               jQuery('#unit_ids_'+data.user_units[k].company_unit_id).prop('checked',true);

      
        }    





           if(data.user.id == 1){

                thisForm.find('#status').val('active').attr('readonly',true).trigger('liszt:updated');
                thisForm.find('#user_type').val('operator').attr('readonly',true).trigger('liszt:updated');

           }else{

                thisForm.find('#status').val(data.user.status).trigger('liszt:updated');
                thisForm.find('#user_type').val(data.user.user_type).trigger('liszt:updated');      


           }

           if (data.user.allow_multiple_veh_entry == 'Yes') {
                jQuery('#allow_multiple_veh_entry').trigger('click');
            }

          

        }else{

            /*toastError(data.response_message);

            setTimeout(() => {

               // window.location.href = "{{ route('manage-user')}}";

            }, 800);*/
			
			jAlert(data.response_message, 'Alert Dialog', function(r) {
				window.location.href = "{{ route('manage-user')}}";
			});

            

        }   

    },

    error: function (jqXHR, textStatus, errorThrown){

        var errMessage = JSON.parse(jqXHR.responseText);

        

        if(jqXHR.status == 401){

            

            jAlert(jqXHR.statusText);

        }else{

            jAlert('Something went wrong!');

            console.log(JSON.parse(jqXHR.responseText));

        }

    }

}).done(function(){

  if(jQuery('#id').val() != 1){
        if(jQuery('#user_type').val() == 'operator'){
            jQuery("[id^='checkall']").attr('disabled',true);  
            
            // jQuery('#contactTable tbody [type="checkbox"]').prop('checked',false);
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
} 
});


jQuery.validator.addMethod("varifypassword", function(value, element) {

  let passRegex = /((?=.*[A-Z])(?=.*[a-z])(?=.*\W))/;

  return this.optional( element ) || passRegex.test( value );

}, 'Please enter a valid password format.');



var validator = jQuery("#editUserForm").validate({

    rules: {
			onkeyup: false,
            onfocusout: false,
            user_name: {

            required: true,

            maxlength: 500

            },

            'unit_ids[]': {

            required: true

            },
            user_type:{

            required: true,
            },

            person_name:{

            required: true,

            },

            mobile_no:{

           // required: true,

            varifymobile:true

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
            person_name:{

            required: "Please Enter Person Name",

            },

            // mobile_no:{

            // required: "Please Enter Mobile No.",

            // },

               'unit_ids[]': {

            required: "Please Select Atleast One Location"

            }
          
		},
		
		errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function(form) {

        var formdata = jQuery('#editUserForm').serialize();
        // if(!jQuery.isEmptyObject(checkData))
        // {
            

            jQuery.ajax({

                url: "{{ route('update-user') }}",

                type: 'POST',

                data: formdata,

               

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if(data.response_code == 1){

                        

                       /* toastSuccess(data.response_message);

                        setTimeout(() => {

                           window.location.href = "{{ route('manage-user')}}";

                        }, 800);*/
						
						jAlert(data.response_message, 'Alert Dialog', function(r) {
							window.location.href = "{{ route('manage-user')}}";
						});

                    }else{

                        jAlert(data.response_message);

                    }   

                },

                error: function (jqXHR, textStatus, errorThrown){

                    var errMessage = JSON.parse(jqXHR.responseText);

                   

                    if(errMessage.errors){

                        validator.showErrors(errMessage.errors);

                      

                    }else if(jqXHR.status == 401){

        

                        jAlert(jqXHR.statusText);

                    }else{

      

                        jAlert('Something went wrong!');

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

                jAlert(data.response_message);

            }

        },

        error: function (jqXHR, textStatus, errorThrown){

            jQuery("#user_name").removeClass('file-loader');

            var errMessage = JSON.parse(jqXHR.responseText);

            

            if(errMessage.errors){

                validator.showErrors(errMessage.errors);

                

            }else if(jqXHR.status == 401){

                            

                jAlert(jqXHR.statusText);

            }else{

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