
setTimeout(() => {
    jQuery('#hsn_code').focus();
}, 100);
let hiddenHSNrId = jQuery('#commonHSNCodeForm').find('input:hidden[name="id"]').val();


jQuery(document).ready(function(){


    if(hiddenHSNrId != "" && hiddenHSNrId != undefined)
    {
        var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}'};
        
        jQuery.ajax({
        
            url : RouteBasePath + "/get-hsn_code/" + hiddenHSNrId,           

            type: 'GET',
        
            headers:headerOpt,
        
            dataType: 'json',
        
            processData: false,
        
            success: function (data) {
        
                if(data.response_code == 1){
        

                    jQuery('#hsn_code').val(data.hsn_code.hsn_code);

                    jQuery('#hsn_description').val(data.hsn_code.hsn_description);
        
                    jQuery('input:hidden[name="id"]').val(data.hsn_code.id);
        
                }else{
                    
                    jAlert(data.response_message, 'Alert Dialog', function(r) {
                        if(RouteBasePath + "/manage-hsn_code" == true)
                        {
                            window.location.href = RouteBasePath + "/manage-hsn_code";
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
   



    
    
    
    var validator = jQuery("#commonHSNCodeForm").validate({
    
            rules: {
                 onkeyup: false,
                onfocusout: false,
    
                hsn_code: {
                    required: true,
                    maxlength: 255
                },
    
               // hsn_description: {
                 //   required: true,
                //    maxlength: 255
                //}
              
    
            },
    
            messages: {
    
                hsn_code: {

                    required: "Please Enter HSN Code",

                    maxlength: "Maximum 255 Characters Allowed"

                    },

                    //hsn_description: {

                    //required: "Please Enter HSN Description",

                    //maxlength: "Maximum 255 Characters Allowed"


                //}
    
            },
              errorPlacement: function (error, element) {
                jAlert(error.text());
                return false;
            },
    
            submitHandler: function(form) {
                
                var formdata = jQuery('#commonHSNCodeForm').serialize();
                    
                let HSNCode = jQuery("#hsn_code").val();
        
                jQuery('#hsn-btn').attr('disabled', true);

                  let formUrl = hiddenHSNrId != undefined && hiddenHSNrId != "" ? RouteBasePath + "/update-hsn_code" : RouteBasePath + "/store-hsn_code";

                        if(HSNCode != '' && HSNCode != undefined)
                        {
                            
                            jQuery.ajax({
                                url: RouteBasePath + "/verify-hsn_code/?hsn_code=" + HSNCode + "&id="+hiddenHSNrId,
                                type: 'GET',
                                dataType: 'json',
                                processData: false,
                                success: function(data) {
                                    if (data.response_code == 1) {
                                        //jAlert(data.response_message);
                                        toastElement(data.response_message,"#hsn_code");
                                        jQuery('#hsn-btn').attr('disabled', true);


                                    }else{
                                        jQuery('#hsn-btn').attr('disabled', false);

                                        jQuery.ajax({
                                            
                                            url: formUrl,
    
                                            type: 'POST',
    
                                            data: formdata,
    
                                            headers:headerOpt,
    
                                            dataType: 'json',
    
                                            processData: false,
    
                                            success: function (data) {
    
                                                if(data.response_code == 1){
                                                    
                                                       
                                                    if(hiddenHSNrId != undefined && hiddenHSNrId != "")
                                                    {
                                                       
                                                        jAlert(data.response_message, 'Success', function(r) {
                                                            window.location.href =RouteBasePath + "/manage-hsn_code";
                                                        });
                                                        add
                                                      addedHSN(true);
                                                    }
                                                    else if(hiddenHSNrId == undefined || hiddenHSNrId == "")
                                                    {
                                                        
                                                        function nextFn(){
                                                                
                                                            document.getElementById("commonHSNCodeForm").reset();
                                    
                                                               validator.resetForm();
                                        
                                                                  // jQuery('#country_name').val('');
                                                            jQuery('#HSNModal').modal('hide');
                                                            jQuery('#hsn_code').focus();
                                                            }

                                                            toastSuccess(data.response_message,nextFn);
                                                         
                                                           addedHSN(true);
                                                         
                                                    }
                                                    else{
                                                        toastError(data.response_message);
                                                    }
                                                    
                                                    
                                                    
                                                    
    
                                                }else{
                                                    console.log("main else");
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
                                }
                            });
                        }
                     
            }
    
        });
    
    });
    
    
    


    // function suggestTransporter(e, $this) {

    //     var keyevent = e
    
    //     if (keyevent.key != "Tab") {
    
    //         jQuery("#transporter_name").addClass('file-loader');
    
    //         var search = jQuery($this).val();
    
    
    
    
    //         jQuery.ajax({
    
    //             url: RouteBasePath + "/transporter-list/?term=" + search,
    
    //             type: 'GET',
    
    //             dataType: 'json',
    
    //             processData: false,
    
    //             success: function(data) {
    
    //                 jQuery("#transporter_name").removeClass('file-loader');
    
    //                 if (data.response_code == 1) {
    
    //                     // console.log(transporterList);
    //                     jQuery('#transporter_name_list').html(data.transporterList);
    
    //                 } else {
    
    //                     toastError(data.response_message);
    
    //                 }
    
    //             },
    
    //             error: function(jqXHR, textStatus, errorThrown) {
    
    //                 jQuery("#transporter_name").removeClass('file-loader');
    
    //                 var errMessage = JSON.parse(jqXHR.responseText);
    
    
    
    //                 if (errMessage.errors) {
    
    //                     validator.showErrors(errMessage.errors);
    
    
    
    //                 } else if (jqXHR.status == 401) {
    
    
    
    //                     toastError(jqXHR.statusText);
    
    //                 } else {
    
    //                     toastError('Something went wrong!');
    
    //                     console.log(JSON.parse(jqXHR.responseText));
    
    //                 }
    
    //             }
    
    //         });
    
    //     }
    
    // }






    
        // Check Duplicate HSN Code
        function checkHsnCode(HSNCode){
           /// var id = jQuery('input[name=id]').val();
                
            jQuery.ajax({
                url: RouteBasePath + "/verify-hsn_code/?hsn_code=" + HSNCode + "&id="+hiddenHSNrId,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function(data) {
                    if (data.response_code == 1) {                
                        // jAlert(data.response_message);
                        toastElement(data.response_message,"#hsn_code");
                        jQuery('#hsn-btn').attr('disabled', true);
                    }else{
                        jQuery('#hsn-btn').attr('disabled', false);

                    }
                }
            });
        }

        function verifyHSNCode(){
            var HSNCode = jQuery('#hsn_code').val();    
            console.log(HSNCode);
            var hidden = jQuery('#hsn').val();
            var suggestion_list = jQuery('#hsn_code_list').html;

            if(suggestion_list!=''){
                checkHsnCode(HSNCode);
            }
        }

        jQuery(document).on('click','#hsn_code_list', function(e){
            var suggest= e.target.innerHTML;
            jQuery('#hsn').val(suggest);
            var hidden = jQuery('#hsn').val();
            
            var suggestion_list = jQuery('#hsn_code_list').html;

            var hsn_code = hidden;
            if(suggestion_list!=''){
                checkHsnCode(hsn_code);
            }    
        });


   

    function suggestHsnCode(e,$this){

        var keyevent = e
     
       if(keyevent.key != "Tab"){
     
         jQuery("#hsn_code").addClass('file-loader');
     
         var search = jQuery($this).val();
     
     
     
         jQuery.ajax({

            url: RouteBasePath + "/hsn_code-list/?term=" + encodeURI(search),
          
             type: 'GET',
     
             dataType: 'json',
     
             hsn_codeData: false,
     
             success: function (data) {
     
                 jQuery("#hsn_code").removeClass('file-loader');
     
                 if(data.response_code == 1){
     
                     jQuery('#hsn_code_list').html(data.hsncodeList);
     
                 }else{
     
                     toastError(data.response_message);
     
                 }
     
             },
     
             error: function (jqXHR, textStatus, errorThrown){
     
                 jQuery("#hsn_code").removeClass('file-loader');
     
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
     
     }
    



     
function getHSN($this = null) {



    if ($this != null) {

        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

    }


    jQuery.ajax({

        url: RouteBasePath + "/get-hsn_codes",
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }


            if (data.response_code == 1) {

                if ($this != null) {

                    var stgDrpHtml = `<option value="">Select HSN Code</option>`;

                    for(let indx in data.hsn_code){
    
                        stgDrpHtml += `<option value="${data.hsn_code[indx].id}">${data.hsn_code[indx].hsn_code}</option>`;
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

function addedHSN($event) {
    if ($event == true) {
        getHSN(".mst-suggest_hsn");
    }
}
