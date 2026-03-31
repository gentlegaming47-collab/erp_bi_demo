

let unitHiddenId = jQuery('#commonUnitForm').find('input:hidden[name="id"]').val();


jQuery(document).ready(function () {

     
    if (unitHiddenId != "" && unitHiddenId != undefined) {

        var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

        // get Unit data at edit time

        jQuery.ajax({

            url: RouteBasePath + "/get-unit/" + unitHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    jQuery('#unit_name').val(data.unit.unit_name);      
                    jQuery('input:hidden[name="id"]').val(data.unit.id);
            
                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "{{ route('manage-unit')}}";
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

    var validator = jQuery("#commonUnitForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            unit_name: {
                required: true,
                maxlength: 255
            },   

        },

        messages: {

            unit_name: {

                required: "Please Enter Unit Name",

                maxlength: "Maximum 255 Characters Allowed"
            },   

        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            var formdata = jQuery('#commonUnitForm').serialize();

             let unitName = jQuery("#unit_name").val();

            let formUrl = unitHiddenId != undefined && unitHiddenId != "" ? RouteBasePath + "/update-unit" : RouteBasePath + "/store-unit";

            if (unitName != '' && unitName != undefined) {
                jQuery.ajax({
                    url: RouteBasePath + "/verify-unit/?unit_name=" + unitName +  "&id=" + unitHiddenId ,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            // jAlert(data.response_message);
                            toastElement(data.response_message,"#unit_name");

                        }
                        else {


                            jQuery.ajax({

                                url: formUrl,

                                type: 'POST',

                                data: formdata,

                                headers: headerOpt,

                                dataType: 'json',

                                processData: false,

                                success: function (data) {

                                    if (data.response_code == 1) {


                                        if (unitHiddenId != undefined && unitHiddenId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-unit";
                                            });
                                            addedUnit(true);
                                        }
                                        else if (unitHiddenId == undefined || unitHiddenId == "") {

                                            function nextFn() {

                                                document.getElementById("commonUnitForm").reset();


                                                // jQuery('#commonUnitForm').find('#taluka_state_id').val('').trigger('liszt:updated');


                                                validator.resetForm();

                                                // jQuery('#country_name').val('');
                                                jQuery('#unitModal').modal('hide');
                                                jQuery('#unit_name').focus();
                                            }

                                            toastSuccess(data.response_message, nextFn);
                                            addedUnit(true);
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






// Duplication check 
function checkUnit(unit_name){   
    var id = jQuery('input[name=id]').val();            
                jQuery.ajax({
                    url: RouteBasePath + "/verify-unit/?unit_name=" + unit_name +  "&id=" + id ,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function(data) {
                        if (data.response_code == 1) {
                            // console.log(data.response_code);
                            // jAlert(data.response_message);
                            toastElement(data.response_message,"#unit_name");
                        }else{
                            // jAlert('error');
                        }
                    }
                });
            }

            function verifyUnit(){
            var unit_name = jQuery('#unit_name').val();                    
                var hidden = jQuery('#unit_data').val();
                var suggestion_list = jQuery('#company_unit_name_list').html;

                if(unit_name!=''){
                    checkUnit(unit_name);
                }
            }

            jQuery(document).on('click','#company_unit_name_list', function(e){
                var suggest= e.target.innerHTML;
                jQuery('#unit_data').val(suggest);
                var hidden = jQuery('#unit_data').val();
                var suggestion_list = jQuery('#company_unit_name_list').html;
                
                var unit_name = hidden;
                if(suggestion_list!=''){
                    checkUnit(unit_name);
                }    
    });


// fetch data in Drop down

function getUnit($this = null) {



    if ($this != null) {

        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

    }


    jQuery.ajax({

        url: RouteBasePath + "/get-unitData",
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }


            if (data.response_code == 1) {

                if ($this != null) {

                    var stgDrpHtml = `<option value="">Select Unit</option>`;

                    for(let indx in data.unit){
    
                        stgDrpHtml += `<option value="${data.unit[indx].id}">${data.unit[indx].unit_name}</option>`;
                    }

                    jQuery($this).each(function (e) {

                        let Id = jQuery(this).attr('id');
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

function addedUnit($event) {
    if ($event == true) {
        getUnit(".mst-suggest_unit");
    }
}



// suggestionList

function suggestUnit(e,$this){

    var keyevent = e
    
    if(keyevent.key != "Tab"){
    
    jQuery("#unit_name").addClass('file-loader');
    
     var search = jQuery($this).val();
    
    
     jQuery.ajax({
        
         url: RouteBasePath + "/unit-list/?term=" + encodeURI(search),
    
    
    
         type: 'GET',
    
         dataType: 'json',
    
         processData: false,
    
         success: function (data) {
    
             
    
             if(data.response_code == 1){
    
                jQuery("#unit_name").removeClass('file-loader');
                 jQuery('#company_unit_name_list').html(data.unitList);
    
             }else{
    
                 jAlert(data.response_message);
    
             }
    
         },
    
         error: function (jqXHR, textStatus, errorThrown){
    
            jQuery("#unit_name").removeClass('file-loader');
    
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

