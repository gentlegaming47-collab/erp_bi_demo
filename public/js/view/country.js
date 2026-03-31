
var hiddenId = jQuery('#commonCountryForm').find('input:hidden[name="id"]').val();


jQuery(document).ready(function(){


    if(hiddenId != "" && hiddenId != undefined)
    {
        var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}'};
        
        jQuery.ajax({
        
            url : RouteBasePath + "/get-country/" + hiddenId,            

            type: 'GET',
        
            headers:headerOpt,
        
            dataType: 'json',
        
            processData: false,
        
            success: function (data) {
        
                if(data.response_code == 1){
        
                   
        
                   jQuery('#country_name').val(data.country.country_name);
        
                   jQuery('input:hidden[name="id"]').val(data.country.id);
        
                    // if(data.country.id == 1)
                    // {
                    //     jQuery('.update').attr("disabled", true);
                    // }else{
                    //     jQuery('.update').attr("disabled", false);
                    // }
        
        
                }else{
                    
                    jAlert(data.response_message, 'Alert Dialog', function(r) {
                        if(RouteBasePath + "/manage-country" == true)
                        {
                            window.location.href = RouteBasePath + "/manage-country";
                        }
                        // window.location.href = "{{ route('manage-country')}}";
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
    }
   



    

    
    var validator = jQuery("#commonCountryForm").validate({
    
            rules: {
                 onkeyup: false,
                onfocusout: false,
                country_name: {
    
                    required: true,
    
                    maxlength: 255
    
                }           
    
            },
    
            messages: {
    
                country_name: {
    
                    required: "Please Enter Country Name",
    
                    maxlength: "Maximum 255 Characters Allowed"
    
                }
    
            },
              errorPlacement: function (error, element) {
                jAlert(error.text());
                return false;
            },
    
            submitHandler: function(form) {
                
                var formdata = jQuery('#commonCountryForm').serialize();
                    
                  let countryName = jQuery("#country_name").val();
                  let formUrl = hiddenId != undefined && hiddenId != "" ? RouteBasePath + "/update-country" : RouteBasePath + "/store-country";

                        if(countryName != '' && countryName != undefined)
                        {
                            
                            jQuery.ajax({
                                url: RouteBasePath + "/verify-country/?country_name=" + countryName + "&id=" + hiddenId ,
                                type: 'GET',
                                dataType: 'json',
                                processData: false,
                                success: function(data) {
                                    if (data.response_code == 1) {
                                        // jAlert(data.response_message,"#country_name");
                                        toastElement(data.response_message,"#country_name");
                                    }else{
                                        jQuery.ajax({
                                            
                                            url: formUrl,
    
                                            type: 'POST',
    
                                            data: formdata,
    
                                            headers:headerOpt,
    
                                            dataType: 'json',
    
                                            processData: false,
    
                                            success: function (data) {
    
                                                if(data.response_code == 1){
                                                    
                                                    if(hiddenId != undefined && hiddenId != "")
                                                    {
                                                        jAlert(data.response_message, 'Success', function(r) {
                                                            window.location.href =RouteBasePath + "/manage-country";
                                                        });
                                                        addedCountry(true);
                                                    }
                                                    else if(hiddenId == undefined || hiddenId == "")
                                                    {
                                                        function nextFn(){
                                                            document.getElementById("commonCountryForm").reset();
                                                               validator.resetForm();
                                                                jQuery('#country_name').focus();
                                                                jQuery('#countryModal').modal('hide');
                                                                // jQuery('#country_btn').prop('disabled', false);                
                                                            }
                                                            toastSuccess(data.response_message,nextFn);
                                                            addedCountry(true);
                                                    }
                                                    else{
                                                        toastError(data.response_message);
                                                    }
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
                                }
                            });
                        }
                    }
            });
        });
    
    
    
    
    
function suggestCountry(e, $this) {
    
    var keyevent = e
    // var thisModal = jQuery('#commonCountryForm');
    

    if (keyevent.key != "Tab") {
        //jQuery("#country_name").addClass('file-loader');
        var search = jQuery($this).val();

        jQuery.ajax({
            url: RouteBasePath + "/country-list?term=" + encodeURI(search),
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function(data) {
                jQuery("#country_name").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#country_name_list').html(data.countryList);
                } else {
                    toastError(data.response_message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                jQuery("#country_name").removeClass('file-loader');
                var errMessage = JSON.parse(jqXHR.responseText);

                if (errMessage.errors) {
                    countryValidator.showErrors(errMessage.errors);

                } else if (jqXHR.status == 401) {

                    toastError(jqXHR.statusText);
                } else {
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
}

    
    
    function checkCountryName(country_name){
        var id = jQuery('input[name=id]').val();
        jQuery.ajax({
            url: RouteBasePath + "/verify-country/?country_name=" + country_name + "&id=" + id ,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function(data) {
                if (data.response_code == 1) {
                    // jAlert(data.response_message);
                    toastElement(data.response_message,"#country_name");
                }
            }
        });
    }
    
    function verifyCountry(){
        var country_name = jQuery('#country_name').val();
        var hidden = jQuery('#country_suggesion').val();
        var suggestion_list = jQuery('#country_name_list').html;
    
        if(country_name!=''){
            checkCountryName(country_name);
        }
    }
    
    jQuery(document).on('click','#country_name_list', function(e){
        var suggest= e.target.innerHTML;
        jQuery('#country_suggesion').val(suggest);
        var hidden = jQuery('#country_suggesion').val();
        var suggestion_list = jQuery('#country_name_list').html;
        
        var country_name = hidden;
        if(suggestion_list!=''){
            checkCountryName(country_name);
        }    
    });





  

function getCountries($this = null) {



    if ($this != null) {

        //jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

    }




    jQuery.ajax({

        url: RouteBasePath + "/get-countries",
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function(data) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }


            if (data.response_code == 1) {

                if ($this != null) {

                    var stgDrpHtml = `<option value="">Select Country</option>`;

                    for (let indx in data.countries) {

                        stgDrpHtml += `<option value="${data.countries[indx].id}">${data.countries[indx].country_name}</option>`;

                    }

                    jQuery($this).each(function(e) {

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
        error: function(jqXHR, textStatus, errorThrown) {
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

function addedCountry($event) {
    if ($event == true) {
        getCountries(".mst-country");
    }
}