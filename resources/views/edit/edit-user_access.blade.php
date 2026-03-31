@extends('layouts.app',['pageTitle' => 'User Access'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-user') }}">User</a> <span class="separator"></span></li>

    <li>Edit User Access</li>

</ul>

@endsection



@section('content')

<div class="widgetbox">

    <div id="show-progress"></div>

     <div class="headtitle">

        <div class="btn-group">

           <!--<button class="toggle-accordion active btn btn-inverse" accordion-id="accordionMaster">Expand All</button>-->

           <a href="{{ route('manage-user') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit User Access <span id="user_code_text"></span></h4>

    </div>

    <div class="widgetcontent">

        <form id="editUserAccessForm" class="stdform" method="post">

            @csrf

            <div class="row">
                <div class="span-6 pull-left">
                    <div class="control-group form-control">
                        <label class="control-label" for="user_id">User</label>    
                        <div class="controls">
                            <span class="formwrapper">
                                <select  name="user_id" id="user_id" class="chzn-select" >
                                    <option value="">Select User</option>
                                    @forelse ($users as $user)
                                        <option value="{{ $user->id }}" >{{ $user->user_name }}</option>
                                        @empty
                                    @endforelse   
                                </select>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="span-6 pull-right">
                    <div class="control-group form-control">
                        <label class="control-label" for="copy_user_id">Copy Access</label>    
                        <div class="controls">
                            <span class="formwrapper">
                                <select  name="copy_user_id" id="copy_user_id" class="chzn-select" disabled>
                                    <option value="">Select User</option>
                                    @forelse ($user2 as $users)
                                        <option value="{{ $users->id }}" >{{ $users->user_name }}</option>
                                        @empty
                                    @endforelse   
                                </select>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

                <div id="accessTable" class="table table-bordered responsive"> </div>



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



jQuery(document).ready(function(){



var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}'};

jQuery(document).on('change','#user_id',function(){
    let selected = jQuery(this).find('option:selected').val();
    if(selected != ""){
        jQuery('#copy_user_id').prop('disabled',false);
        jQuery("#copy_user_id option").removeAttr('disabled').trigger('liszt:updated');
        jQuery("#copy_user_id option[value='"+selected+"']").attr('disabled','disabled').trigger('liszt:updated');
        resetForm();
        
        loadAccessOption(selected);
    }else{
        jQuery('#copy_user_id').prop('disabled',false);
        jQuery("#copy_user_id option").removeAttr('disabled').trigger('liszt:updated');
        resetForm();
       jQuery('#accessTable').empty();
    }
});

jQuery(document).on('change','#copy_user_id',function(){
    let userSelected = jQuery('#user_id').find('option:selected').val();
    let selected = jQuery(this).find('option:selected').val();
    if(selected != ""){
        if(selected == 1){ // Only admin
            resetForm(true); //This Will Uncheck all Selected Checkbox
            jQuery('#editUserAccessForm').find('input[type="checkbox"]').prop('checked',true); //For Check All Checkbox
        }else{ // Other User 
            resetForm(true);
            getAccessData(selected,true);
        }
        
    }else{
        resetForm(true);
    }
});

function resetForm(onlyForm = false){
    if(onlyForm == false){
    jQuery('#user_code_text').text('');
    }
    jQuery('input[type="checkbox"]:checked').each(function(){
        jQuery(this).prop('checked',false);
    });
}

function getAccessData(userId,forCopy = false){


    jQuery.ajax({

    url: "{{ route('get-user_access') }}?id="+userId,

    type: 'GET',

    headers: headerOpt,

    dataType: 'json',

    async: false,

    processData: false,

    success: function (data) {

        jQuery('#show-progress').removeClass('loader-progress');

        if(data.response_code == 1){ 

            if(forCopy == false)
                jQuery('#user_code_text').text(' - '+data.user_code);

            // check box print
            for(dkey in data.user_access_data){

                jQuery("input[name='actions["+data.user_access_data[dkey].page+"]["+data.user_access_data[dkey].action+"]'] ").prop('checked',true);
                
                // if all action checked then check box chekced
                    
                // if(data.user_access_data[dkey].action == "1" && data.user_access_data[dkey].action == "2" && data.user_access_data[dkey].action == "3" || data.user_access_data[dkey].action == "4" || data.user_access_data[dkey].action == "5")
                // {
                    
                //     jQuery("input[name='chkname["+data.user_access_data[dkey].page+"]'] ").prop('checked',true);
                // }             

            }

         

            

             jQuery('input:checkbox[name*="actions"]').click(function(){

                    let actionCompare = ['2','3','4','5'];

                    let actionVal = jQuery(this).val();

                    let explArr = jQuery(this).attr('name').replace('actions','').replaceAll('][',',').replaceAll('[','').replaceAll(']','').split(',');

                    let ischecked = 0;



                if(jQuery(this).prop('checked') && jQuery.inArray(actionVal,actionCompare) != -1){
                        
                    if(jQuery.inArray(actionVal,actionCompare) != -1 ){

                        jQuery("input[name='actions["+explArr[0]+"][1]'] ").prop('checked',true);

                    }

                }else if(!jQuery(this).prop('checked') && actionVal == "1" ){

                    for(k in actionCompare){

                        if(jQuery("input[name='actions["+explArr[0]+"]["+actionCompare[k]+"]'] ").prop('checked')){

                            ischecked++;

                        }

                    }



                    if(ischecked > 0){

                        return false;

                    }

                    return true;

                }

               

            });

        }else{

            // toastError(data.response_message);
            /*jAlert(data.response_message);

            setTimeout(() => {

                window.location.href = "{{ route('manage-user')}}";

            }, 800);*/
			
			jAlert(data.response_message, 'Alert Dialog', function(r) {
				window.location.href = "{{ route('manage-user')}}";
			});

        }   

    },

    error: function (jqXHR, textStatus, errorThrown){

        jQuery('#show-progress').removeClass('loader-progress');

        var errMessage = JSON.parse(jqXHR.responseText);

        

       if(jqXHR.status == 401){

            

            jAlert(jqXHR.statusText);

        }else{

            jAlert('Something went wrong!');

            console.log(JSON.parse(jqXHR.responseText));

        }

    }

});

return false;

}


function loadAccessOption(user_id){

jQuery('#show-progress').addClass('loader-progress');

    jQuery.ajax({
    
        // url: "{{ route('get-access_modules') }}",
        url: "{{ route('get-access_modules') }}?id="+user_id,
    
        type: 'GET',
    
        headers: headerOpt,
    
        dataType: 'json',
    
        processData: false,
    
        success: function (data) {
    
            if(data.response_code == 1){
    
                var accordionTabs = data.parents;

                  var tabHeadersCount =  [];

                  var totalTabHeadersCount = [];

                  for(let indx in accordionTabs){
                    tabHeadersCount[accordionTabs[indx].module] = 0;
                    totalTabHeadersCount[accordionTabs[indx].module] = 0;
                  }


                  for(let indx in accordionTabs){
                    for(let pindx in data.pages){

                      if(data.pages[pindx].parent == accordionTabs[indx].id){
                        let prev = parseInt(totalTabHeadersCount[accordionTabs[indx].module]);
                        totalTabHeadersCount[accordionTabs[indx].module] = prev+1;
                      }
                    }
                  }

                  var tableHtml = ``;

                  var totalActions = data.actions.length+1;

                  var thWidth = (66/parseInt(totalActions))+0;

                  var actionsHeader = `<tr>
                    <th width="34%">Access</th>
                    <th width="${thWidth}%"></th>`;

                  for(let aindx in data.actions){

                      actionsHeader +=`<th class="org-text" width="${thWidth}%">${data.actions[aindx].display_name}</th>`

                  }

                  actionsHeader +=`</tr>`;
      
                  tableHtml +=`<div class="accordion accordion-primary" id="accordionMaster">`;

                  for(let accKey in accordionTabs){

                    for(let key in data.pages){
    
                        if(data.pages[key].parent == accordionTabs[accKey].id){
                          
                            if(tabHeadersCount[accordionTabs[accKey].module] == 0){
                                var approvalListMenuDisplay = '';

                                if(data.user_type.user_type == 'operator'){
                                    if(accordionTabs[accKey].display_name == 'Approval'){
                                        approvalListMenuDisplay =  'style="display:none;"';
                                    }else{
                                        approvalListMenuDisplay = '';
                                    }
                                }
                                
        
                                tableHtml +=`
        
                                    <h3 ${approvalListMenuDisplay}><a href="javascript:void(0);">${accordionTabs[accKey].display_name}</a></h3>
        
                                        <div>
        
                                            <table class="table table-bordered responsive">
        
                                            <thead>
        
                                              ${actionsHeader}
        
                                            </thead>
        
                                            <tbody>`;
        
                            }
                            tabHeadersCount[accordionTabs[accKey].module] = tabHeadersCount[accordionTabs[accKey].module]+1;
        
                          if(data.pages[key].show_in_access == "YES"){

                          var approvalMenuDisplay = '';

                          
                            if(data.user_type.user_type == 'operator'){
                                if(data.pages[key].page == 'sm_approval' || data.pages[key].page == 'state_coordinator_approval' || data.pages[key].page == 'gm_approval' || data.pages[key].page == 'zsm_approval' || data.pages[key].page == 'po_approval' || data.pages[key].page == 'grn_against_po_approval' || data.pages[key].page == 'sm_approval_report' || data.pages[key].page == 'state_coordinator_approval_report' || data.pages[key].page == 'zsm_approval_report' || data.pages[key].page == 'gm_approval_report' || data.pages[key].page == 'po_vs_excess_grn' ){

                                    approvalMenuDisplay =  'style="display:none;"';

                                }
                            }else if(data.user_type.user_type == 'director'){

                                if(data.pages[key].page == 'sm_approval' || data.pages[key].page == 'state_coordinator_approval' || data.pages[key].page == 'gm_approval' || data.pages[key].page == 'zsm_approval' ||  data.pages[key].page == 'sm_approval_report' || data.pages[key].page == 'state_coordinator_approval_report' || data.pages[key].page == 'zsm_approval_report' || data.pages[key].page == 'gm_approval_report' || data.pages[key].page == 'po_vs_excess_grn' ){

                                    approvalMenuDisplay =  'style="display:none;"';

                                }
                            }else{
                                if(data.pages[key].page == 'approval_status'){
                                    approvalMenuDisplay =  'style="display:none;"';
                                }else{
                                    approvalMenuDisplay =  '';
                                }
                            }

                        
                            tableHtml += `<tr ${approvalMenuDisplay}>`;

                            tableHtml += `<td width="34%" class="org-text"><input type="hidden" name="pages[]" class="exp" value="${data.pages[key].id}"/>${data.pages[key].display_name}</td>`;
                            tableHtml += `<td width="11%"><input type="checkbox" data-page="${data.pages[key].page}" id="chk_${data.pages[key].id}" name="chkname[${data.pages[key].id}]"></td>`;
                            for(let akey in data.actions){
        
                                tableHtml +=`<td width="${thWidth}%">`;
        
                                if(data.pages[key].actions != "all" ){
                                        
                                  if(data.pages[key].actions.includes(String(data.actions[akey].id))){
                                    tableHtml +=`<input type="checkbox" id="${data.pages[key].page}[]" name="actions[${data.pages[key].id}][${data.actions[akey].id}]" value="${data.actions[akey].id}"/>`;
                                  }

                                }else{
                                  
                                  tableHtml +=`<input type="checkbox" id="${data.pages[key].page}[]" name="actions[${data.pages[key].id}][${data.actions[akey].id}]" value="${data.actions[akey].id}"/>`;
                                }
        
        
                                tableHtml +=`</td>`;
        
                            };
        
                            tableHtml += `</tr>`;
        
                          }

                          if(tabHeadersCount[accordionTabs[accKey].module] == totalTabHeadersCount[accordionTabs[accKey].module]){
                            tableHtml +=`</tbody></table></div>`;
                          }
        
                        }
                    }
                }
      
                  jQuery('#accessTable').empty().html(tableHtml+=`</tbody></table></div></div>`);
      
                  jQuery('.accordion ').accordion({heightStyle: "content"});
                  
                  jQuery("[id*='chk_']").click(function(){
                    
                    var strpage = jQuery(this).attr('data-page');
                    
                    if(jQuery(this).prop("checked")){
                      jQuery('input[id="'+ strpage +'[]"]').each(function(){
                        jQuery(this).prop('checked', true);
                        
                      });	
                    }else{
                      jQuery('input[id="'+ strpage +'[]"]').each(function(){
                        jQuery(this).prop('checked', false);
                      });	
                    }
                    
                  });
                  
                getAccessData(user_id);

                  if(data.user.length > 0){
                    var productDrpHtml = `<option value="">Select User</option>`;
                    for (let indx in data.user) {
                        productDrpHtml += `<option value="${data.user[indx].id}">${data.user[indx].user_name} </option>`;
                    }

                    jQuery('#editUserAccessForm').find('#copy_user_id').empty().append(productDrpHtml);
                    jQuery('#editUserAccessForm').find('#copy_user_id').trigger('liszt:updated');


                  }
                  
      
    
            }else{
    
                jAlert(data.response_message);
    
            }   
    
        },
    
        error: function (jqXHR, textStatus, errorThrown){
    
            jQuery('#show-progress').removeClass('loader-progress');
            var errMessage = JSON.parse(jqXHR.responseText);
    
            
    
            if(jqXHR.status == 401){
    
                
    
                jAlert(jqXHR.statusText);
    
            }else{
    
                jAlert('Something went wrong!');
    
                console.log(JSON.parse(jqXHR.responseText));
    
            }
    
        }
    
    });
}



var validator = jQuery("#editUserAccessForm").validate({

	    rules: {
            user_id: {
                required: true,
            },
		},
		messages: {
			user_id: {
			    required:"Please select user",	    
			},
        },
        submitHandler: function(form) {

            var formdata = jQuery('#editUserAccessForm').serialize();

            jQuery.ajax({

                url: "{{ route('update-user_access') }}",

                type: 'POST',

                data: formdata,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if(data.response_code == 1){

                        jAlert(data.response_message);

                        // setTimeout(() => {

                        //     window.location.href = "{{ route('manage-user')}}";

                        // }, 800);
                        window.location.reload();
						

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



                    




</script>
@endsection