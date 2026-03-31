var hiddenId = jQuery('#commonMisCategoryForm').find('input:hidden[name="id"]').val();




jQuery(document).ready(function(){


    if(hiddenId != "" && hiddenId != undefined)
    {
        var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}'};
        
        jQuery.ajax({
        
            url : RouteBasePath + "/get-mis_category/" + hiddenId,            

            type: 'GET',
        
            headers:headerOpt,
        
            dataType: 'json',
        
            processData: false,
        
            success: function (data) {
        
                if(data.response_code == 1){
                   jQuery('#mis_category').val(data.mis_cat_data.mis_category);
                   jQuery('input:hidden[name="id"]').val(data.mis_cat_data.id);
                }else{
                    jAlert(data.response_message, 'Alert Dialog', function(r) {
                        if(RouteBasePath + "/manage-mis_category" == true)
                        {
                            window.location.href = RouteBasePath + "/manage-mis_category";
                        }
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
        });
    }
    
    var validator = jQuery("#commonMisCategoryForm").validate({
    
            rules: {
                    onkeyup: false,
                    onfocusout: false,
                        mis_category: {
                            required: true,
                            maxlength: 255
                    }           
    
            },
    
            messages: {
    
                mis_category: {
                    required: "Please Enter MIS Category",
                    maxlength: "Maximum 255 Characters Allowed"
                }
    
            },
              errorPlacement: function (error, element) {
                jAlert(error.text());
                return false;
            },
    
            submitHandler: function(form) {
                
                var formdata = jQuery('#commonMisCategoryForm').serialize();
                    
                  let MisCatName = jQuery("#mis_category").val();
                  let formUrl = hiddenId != undefined && hiddenId != "" ? RouteBasePath + "/update-mis_category" : RouteBasePath + "/store-mis_category";

                        if(MisCatName != '' && MisCatName != undefined)
                        {
                            
                            jQuery.ajax({
                                url: RouteBasePath + "/verify-mis_category?cat_name=" + MisCatName + "&id=" + hiddenId ,
                                type: 'GET',
                                dataType: 'json',
                                processData: false,
                                success: function(data) {
                                    if (data.response_code == 1) {
                                        toastElement(data.response_message,"#mis_category");
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
                                                            window.location.href =RouteBasePath + "/manage-mis_category";
                                                        });
                                                        addedMisCat(true);
                                                    }
                                                    else if(hiddenId == undefined || hiddenId == "")
                                                    {
                                                        function nextFn(){
                                                            document.getElementById("commonMisCategoryForm").reset();
                                                               validator.resetForm();
                                                                jQuery('#mis_category').focus();
                                                                jQuery('#MisCatModal').modal('hide');
                                                                // jQuery('#country_btn').prop('disabled', false);                
                                                            }
                                                            toastSuccess(data.response_message,nextFn);
                                                            addedMisCat(true);
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


        function suggestMisCategory(e, $this) {
            var keyevent = e
            
            if (keyevent.key != "Tab") {
                var search = jQuery($this).val();
        
                jQuery.ajax({
                    url: RouteBasePath + "/mis-cat_list?term=" + encodeURI(search),
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function(data) {
                        jQuery("#mis_category").removeClass('file-loader');
                        if (data.response_code == 1) {
                            jQuery('#mis_category_list').html(data.MisCatList);
                        } else {
                            toastError(data.response_message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        jQuery("#mis_category").removeClass('file-loader');
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


        function checkMisCategory(mis_category){
            var id = jQuery('input[name=id]').val();
            jQuery.ajax({
                url: RouteBasePath + "/verify-mis_category/?mis_category=" + mis_category + "&id=" + id ,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function(data) {
                    if (data.response_code == 1) {
                        toastElement(data.response_message,"#mis_category");
                    }
                }
            });
        }
        
        function verifyMisCategory(){
            var mis_category = jQuery('#mis_category').val();
            var hidden = jQuery('#mis_suggesion').val();
            var suggestion_list = jQuery('#mis_category_list').html;
        
            if(mis_category!=''){
                checkMisCategory(mis_category);
            }
        }
        
        jQuery(document).on('click','#mis_category_list', function(e){
            var suggest= e.target.innerHTML;
            jQuery('#mis_suggesion').val(suggest);
            var hidden = jQuery('#mis_suggesion').val();
            var suggestion_list = jQuery('#mis_category_list').html;
            
            var mis_category = hidden;
            if(suggestion_list!=''){
                checkMisCategory(mis_category);
            }    
        });

        function getMisCat($this = null) {
            if ($this != null) {
            }
        
            jQuery.ajax({
                url: RouteBasePath + "/get-mis_category_list",
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function(data) {
                    if ($this != null) {
                        jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');
                    }
        
                    if (data.response_code == 1) {
                        if ($this != null) {
                            var stgDrpHtml = `<option value="">Select MIS Category</option>`;
        
                            for (let indx in data.mis_cat_data) {
        
                                stgDrpHtml += `<option value="${data.mis_cat_data[indx].id}">${data.mis_cat_data[indx].mis_category}</option>`;
        
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
        
    function addedMisCat($event) {
        if ($event == true) {
            getMisCat(".mst_mis_cat");
        }
    }