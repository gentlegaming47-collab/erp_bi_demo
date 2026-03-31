

let itemGroupHiddenId = jQuery('#commonItmeGroupForm').find('input:hidden[name="id"]').val();


jQuery(document).ready(function () {

     
    if (itemGroupHiddenId != "" && itemGroupHiddenId != undefined) {

        var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

        // get Unit data at edit time

        jQuery.ajax({

            url: RouteBasePath + "/get-item-groups/" + itemGroupHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    jQuery('#item_group_name').val(data.item_data.item_group_name);

                    jQuery('#item_group_code').val(data.item_data.item_group_code);
         
                    jQuery('input:hidden[name="id"]').val(data.item_data.id);
         
            
                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "{{ route('manage-item_group')}}";
                    });
                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                var errMessage = JSON.parse(jqXHR.responseText);



                if (jqXHR.status == 401) {
                    jAlert(jqXHR.statusText);

                } else {


                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }


    // Store or update Code

    var validator = jQuery("#commonItmeGroupForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            item_group_name: {

                required: true,

                maxlength: 255

            },
            item_group_code : {
                required: true,
            },      
        },

        messages: {

            item_group_name: {

                required: "Please enter item group name",

                maxlength: "Maximum 255 characters allowed"

            },

            item_group_code: {
                required: "Please enter Group Code",
            },
        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            var formdata = jQuery('#commonItmeGroupForm').serialize();

            let itemName = jQuery("#item_group_name").val();
            let itemCode = jQuery("#item_group_code").val();

            let formUrl = itemGroupHiddenId != undefined && itemGroupHiddenId != "" ? RouteBasePath + "/update-item-group" : RouteBasePath + "/store-item-group";

            jQuery('#item_group_btn').prop('disabled', false);
        
            let callURL;

            if(itemName != "" && itemName != undefined)
            {
                callURL = RouteBasePath + "/verify-item_group?item_group_name=" + itemName + "&id=" + itemGroupHiddenId;
            }
            else{
                callURL = RouteBasePath + "/verify-item_group_code?item_group_code=" + itemCode + "&item_group_name=" + itemName + "&id=" + itemGroupHiddenId;
            }

            if (itemName != '' && itemName != undefined) {
                jQuery.ajax({
                    url : callURL,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            jAlert(data.response_message);
                            toastElement(data.response_message,"#item_group_name","#item_group_code");
                            jQuery('#item_group_btn').attr('disabled', true);
                        }
                        else {
                            jQuery('#item_group_btn').prop('disabled', false);


                            jQuery.ajax({

                                url: formUrl,

                                type: 'POST',

                                data: formdata,

                                headers: headerOpt,

                                dataType: 'json',

                                processData: false,

                                success: function (data) {

                                    if (data.response_code == 1) {


                                        if (itemGroupHiddenId != undefined && itemGroupHiddenId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-item_group  ";
                                            });
                                            addedItemGroup(true);
                                        }
                                        else if (itemGroupHiddenId == undefined || itemGroupHiddenId == "") {

                                            function nextFn() {

                                                document.getElementById("commonItmeGroupForm").reset();



                                                // validator.resetForm();
                                                window.location.reload();

                                                jQuery('#itemGroupModal').modal('hide');
                                                jQuery('#item_group_name').focus();
                                            }

                                            toastSuccess(data.response_message, nextFn);
                                            addedItemGroup(true);
                                        }
                                        else {
                                            toastError(data.response_message);
                                        }





                                    } else {

                                        jAlert(data.response_message);


                                    }

                                },

                                error: function (jqXHR, textStatus, errorThrown) {

                                    var errMessage = JSON.parse(jqXHR.responseText);



                                    if (errMessage.errors) {

                                        validator.showErrors(errMessage.errors);



                                    } else if (jqXHR.status == 401) {

                                        jAlert(jqXHR.statusText);


                                        // toastError(jqXHR.statusText);

                                    } else {


                                        jAlert('Something went wrong!');

                                        // toastError('Something went wrong!');

                                        console.log(JSON.parse(jqXHR.responseText));

                                    }

                                }

                            });
                        }
                    }
                });
            }

        }

    });

});



// Item Group  Duplication  Code


// function CheckItemName(item_group_name){
//     let item_group_name   = jQuery('#item_group_name').val();

//     if(item_group_name != ''){
//         jQuery.ajax({
//             url: RouteBasePath + "/verify-item_group?item_group_name=" + item_group_name  + "&id=" + itemGroupHiddenId, 
//             type: 'GET',
//             dataType: 'json',
//             processData: false,
//             success: function(data) {                    
//                 if (data.response_code == 1) {
//                     jAlert(data.response_message);
//                 }
//                 else{
//                 }
//             }                                                                           
//         });
//     }
// }


function checkItemGroup(item_group_name){        
    jQuery.ajax({
        url: RouteBasePath + "/verify-item_group/?item_group_name=" + item_group_name +  "&id=" + itemGroupHiddenId,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function(data) {
            if (data.response_code == 1) {
                // console.log(data.response_code);
                // jAlert(data.response_message);
                toastElement(data.response_message,"#item_group_name");
                jQuery('#item_group_btn').attr('disabled', true);

            }else{
                // jAlert('error');
                jQuery('#item_group_btn').attr('disabled', false);

            }
        }
    });
}


function CheckItemGroupCode(item_group_code, item_group_name, id){

    jQuery.ajax({
            url: RouteBasePath + "/verify-item_group_code?item_group_code=" + item_group_code + "&item_group_name=" + item_group_name + "&id=" + itemGroupHiddenId, 
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function(data) {                    
                if (data.response_code == 1) {
                    toastElement(data.response_message,"#item_group_code");
                jQuery('#item_group_btn').attr('disabled', true);

                }
                else{
                jQuery('#item_group_btn').attr('disabled', false);

                }
            }                                                                           
    });

}



function verifyItemGroup(){
var item_group_name = jQuery('#item_group_name').val();                    
var hidden = jQuery('#item_group').val();
var suggestion_list = jQuery('#item_group_name_list').html;

if(item_group_name!=''){
    checkItemGroup(item_group_name);
}
}   


function verifyItemGroupCode(){
    var item_group_code = jQuery('#item_group_code').val();                      
    // var item_group_name = jQuery('#item_group_name').val();                    
    var hidden = jQuery('#itemGroupCode').val();
    var suggestion_list = jQuery('#group_code_name_list').html;

    if(item_group_code!='' && item_group_name != ''){                        
        CheckItemGroupCode(item_group_code, item_group_name);
    }
}


jQuery(document).on('click','#item_group_name_list', function(e){
    var suggest= e.target.innerHTML;
    jQuery('#item_group').val(suggest);
    var hidden = jQuery('#item_group').val();
    var suggestion_list = jQuery('#item_group_name_list').html;
    
    var item_group_name = hidden;
    if(suggestion_list!=''){
        checkItemGroup(item_group_name);
    }    
});



jQuery(document).on('click','#group_code_name_list', function(e){
    var suggest= e.target.innerHTML;
    var item_group_code   = suggest;        
    var item_group_name   = jQuery("#item_group_name").val();
    var id   = jQuery("#id").val();
    if(item_group_code!='' || item_group_name != ''){
        CheckItemGroupCode(item_group_code, item_group_name, id);
    }    
});


// fetch data in Drop down

function getItemGroupName($this = null) {



    if ($this != null) {

        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

    }


    jQuery.ajax({

        url: RouteBasePath + "/get-item-group",
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }


            if (data.response_code == 1) {

                if ($this != null) {

                    var stgDrpHtml = `<option value="">Select Item Group Name</option>`;

                    for(let indx in data.itemGroupData){                            
                        stgDrpHtml += `<option value="${data.itemGroupData[indx].id}">${data.itemGroupData[indx].item_group_name}</option>`;
                    }                    
                    jQuery($this).each(function(e) {
                        //let Id = jQuery(this).attr('id');                              
                        let Selected = jQuery(this).find("option:selected").val();      
                        jQuery(this).empty().append(stgDrpHtml);                        
                        jQuery(this).val(Selected).trigger('liszt:updated');
                    });                    
                }

            } else {
                toastError(data.response_message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }

            var errMessage = JSON.parse(jqXHR.responseText);

            if (jqXHR.status == 401) {
                toastError(jqXHR.statusText);
            } else {
                toastError('Something went wrong!');
                console.log(JSON.parse(jqXHR.responseText));
            }
        }
    });
}

function addedItemGroup($event) {
    if ($event == true) {
        getItemGroupName(".mst-suggest_item_group");
    }
}



// suggestionList

function suggestItemGroupName(e,$this){    
    var keyevent = e
    
    if(keyevent.key != "Tab"){
    
    jQuery("#item_group_name").addClass('file-loader');

    
     var search = jQuery($this).val();
    
    
     jQuery.ajax({
        
         url: RouteBasePath + "/item-group-list/?term=" + encodeURI(search),
        
         type: 'GET',
    
         dataType: 'json',
    
         processData: false,
    
         success: function (data) {
    
             
    
             if(data.response_code == 1){
                 
                 jQuery('#item_group_name_list').html(data.itemGroupList);
                 jQuery("#commonItmeGroupForm").find("#item_group_name").removeClass('file-loader');
    
             }else{
    
                 jAlert(data.response_message);
    
             }
    
         },
    
         error: function (jqXHR, textStatus, errorThrown){
    
            jQuery("#item_group_name").removeClass('file-loader');
    
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




function suggestItemGroupCode(e,$this){    
    var keyevent = e
    
    if(keyevent.key != "Tab"){
    
    jQuery("#item_group_code").addClass('file-loader');

    
     var search = jQuery($this).val();
    
    
     jQuery.ajax({
        
         url: RouteBasePath + "/group-code-list/?term=" + encodeURI(search),
        
         type: 'GET',
    
         dataType: 'json',
    
         processData: false,
    
         success: function (data) {
    
             
    
             if(data.response_code == 1){
                 
                 jQuery('#group_code_name_list').html(data.GroupCode);
                 jQuery("#commonItmeGroupForm").find("#item_group_code").removeClass('file-loader');
    
             }else{
    
                 jAlert(data.response_message);
    
             }
    
         },
    
         error: function (jqXHR, textStatus, errorThrown){
    
            jQuery("#item_group_code").removeClass('file-loader');
    
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








// blank the modal after closed
jQuery('#itemGroupModal').on('hide.bs.modal', function(e) {    
    document.getElementById("commonItmeGroupForm").reset();
    document.getElementById("item_group_name_list").innerHTML = '';
    document.getElementById("group_code_name_list").innerHTML = '';
    // validator.resetForm();
});
