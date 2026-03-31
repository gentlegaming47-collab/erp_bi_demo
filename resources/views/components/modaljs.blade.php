validatorCurrency = jQuery("#addCurrencyFormModal").validate({
    rules: {
        currency_code: {
            required: true,
            // remote: '/validate-code',
            maxlength: 100
        },
        currency_name: {
            required: true,
            maxlength: 100
        },
        paise_name: {
            required: true,
            maxlength: 100
        }		
    },
    messages: {
        currency_code: {
            required: "Please enter currency code",
            //remote:"Email already exist",
            maxlength: "Maximum 100 characters allowed",
        },
        currency_name: {
            required: "Please enter currency name",
            maxlength: "Maximum 100 characters allowed"
        },
        paise_name: {
            required: "Please enter paise name",
            maxlength: "Maximum 100 characters allowed"
        }
    },
    submitHandler: function(form) {
        
        var formdata = jQuery('#addCurrencyFormModal').serialize();

        jQuery('#currencyModal').find('#addCurrencyModal').addClass('btn-loader');
        jQuery.ajax({
            url: "{{ route('add_currency') }}",
            type: 'POST',
            data: formdata,
            
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery('#currencyModal').find('#addCurrencyModal').removeClass('btn-loader');
                if(data.response_code == 2)
                {
                    toastError(data.response_message);
                    jQuery('#addCurrencyFormModal').find('input#currency_code').focus();
                }
                if(data.response_code == 1){
                    
                    toastSuccess(data.response_message);
                    document.getElementById("addCurrencyFormModal").reset();
                    validatorCurrency.resetForm();
                    jQuery('#currencyModal').modal('hide');

                    getCurrencies('.mst-currency');
                    
                }else{
                    toastError(data.response_message);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#currencyModal').find('#addCurrencyModal').removeClass('btn-loader');
                var errMessage = JSON.parse(jqXHR.responseText);
                
                if(errMessage.errors){
                    validatorCurrency.showErrors(errMessage.errors);
                    
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

var validatorMaterial = jQuery("#addMaterialFormModal").validate({
    rules: {
        name: {
            required: true,
            maxlength: 500
        }			
    },
    messages: {
        name: {
            required: "Please enter material",
            maxlength: "Maximum 500 characters allowed"
        }
    },
    submitHandler: function(form) {
        
        var formdata = jQuery('#addMaterialFormModal').serialize();
        jQuery('#materialModal').find('#addMaterialModal').addClass('btn-loader');
        jQuery.ajax({
            url: "{{ route('add-material') }}",
            type: 'POST',
            data: formdata,
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery('#materialModal').find('#addMaterialModal').removeClass('btn-loader');
                if(data.response_code == 1){
                    
                    toastSuccess(data.response_message);
                    document.getElementById("addMaterialFormModal").reset();
                    validatorMaterial.resetForm();
                    jQuery('#addMaterialFormModal').find('input#name').focus();
                    jQuery('#materialModal').modal('hide');

                    getMaterials('.mst-material');
                }else{
                    toastError(data.response_message);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#materialModal').find('#addMaterialModal').removeClass('btn-loader');
                var errMessage = JSON.parse(jqXHR.responseText);
                
                if(errMessage.errors){
                    validatorMaterial.showErrors(errMessage.errors);
                    
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

var validatorIncoTerm = jQuery("#addIncoTermFormModal").validate({
    rules: {
        name: {
            required: true,
            maxlength: 500
        }			
    },
    messages: {
        name: {
            required: "Please enter terms",
            maxlength: "Maximum 500 characters allowed"
        }
    },
    submitHandler: function(form) {
        var formdata = jQuery('#addIncoTermFormModal').serialize();
        jQuery('#incoTermModal').find('#addIncoTermModal').addClass('btn-loader');
        jQuery.ajax({
            url: "{{ route('add-inco_terms') }}",
            type: 'POST',
            data: formdata,
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery('#incoTermModal').find('#addIncoTermModal').removeClass('btn-loader');
                if(data.response_code == 1){
                    
                    toastSuccess(data.response_message);
                    document.getElementById("addIncoTermFormModal").reset();
                    validatorIncoTerm.resetForm();
                    jQuery('#addIncoTermFormModal').find('input#name').focus();
                    jQuery('#incoTermModal').modal('hide');

                    getCommingTerms('.mst-inco_term');
    
                }else{
                    toastError(data.response_message);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#incoTermModal').find('#addIncoTermModal').removeClass('btn-loader');
                var errMessage = JSON.parse(jqXHR.responseText);
                
                if(errMessage.errors){
                    validatorIncoTerm.showErrors(errMessage.errors);
                    
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

var validatorProcess = jQuery("#addProcessFormModal").validate({
    rules: {
        type: {
            required: true,
        },
        process: {
            required: true,
            maxlength: 500
        }			
    },
    messages: {
        type: {
            required: "Please select type",
        },
        process: {
            required: "Please enter process",
            maxlength: "Maximum 500 characters allowed"
        }
    },
    submitHandler: function(form) {
        var formdata = jQuery('#addProcessFormModal').serialize();
        jQuery('#processModal').find('#addProcessModal').addClass('btn-loader');
        console.log(formdata);
        jQuery.ajax({
            url: "{{ route('add-process_data') }}",
            type: 'POST',
            data: formdata,
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery('#processModal').find('#addProcessModal').removeClass('btn-loader');
                if(data.response_code == 1){
                    
                    toastSuccess(data.response_message);
                    document.getElementById("addProcessFormModal").reset();
                    
                    validatorProcess.resetForm();
                    jQuery('#addProcessFormModal').find('#type').val('').trigger('liszt:updated');
                    jQuery('#addProcessFormModal').find('#type').trigger('liszt:activate');
                    jQuery('#processModal').modal('hide');

                    getProcesses('.mst-process');
            
                }else{
                    toastError(data.response_message);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#processModal').find('#addProcessModal').removeClass('btn-loader');
                var errMessage = JSON.parse(jqXHR.responseText);
                
                if(errMessage.errors){
                    validatorProcess.showErrors(errMessage.errors);
                    
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

var validatorTermCondition = jQuery("#addTermConditionFormModal").validate({
    rules: {
        sequence: {
            required: true,
            number: true
        },
        terms: {
            required: true,
            maxlength: 500
        }			
    },
    messages: {
        sequence: {
            required: "Please enter sequence only numeric value",
            //  number: "Please enter only numeric value"
        },
        terms: {
            required: "Please enter terms",
            maxlength: "Maximum 500 characters allowed"
        }
    },
    submitHandler: function(form) {
        var formdata = jQuery('#addTermConditionFormModal').serialize();
        jQuery('#termConditionModal').find('#addTermConditionModal').addClass('btn-loader');
        console.log(formdata);
        jQuery.ajax({
            url: "{{ route('add-conditions') }}",
            type: 'POST',
            data: formdata,
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery('#termConditionModal').find('#addTermConditionModal').removeClass('btn-loader');
                if(data.response_code == 1){
                    
                    toastSuccess(data.response_message);
                    document.getElementById("addTermConditionFormModal").reset();
                    validatorTermCondition.resetForm();
                    jQuery('#addTermConditionFormModal').find('input#sequence').focus();
                    jQuery('#termConditionModal').modal('hide');

                    @if(isset($id))
                        getTermConditions('.term-conditions',null,null,forQuot = {{ $id }});
                    @else
                        if(jQuery('#addQuotationForm').find('input:radio[name="quot_type"]:checked').val() == "revision"){

                            let quotId = jQuery('#quotation_revisions').find('option:selected').val();

                            getTermConditions('.term-conditions',null,null,forQuot = quotId);
                        }else{
                            getTermConditions('.term-conditions');
                        }
                        
                    @endif
            
                }else{
                    toastError(data.response_message);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#termConditionModal').find('#addTermConditionModal').removeClass('btn-loader');
                var errMessage = JSON.parse(jqXHR.responseText);
                
                if(errMessage.errors){
                    validatorTermCondition.showErrors(errMessage.errors);
                    
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

jQuery.validator.addMethod("varifymobile", function(value, element) {
     function testMobile(val){
          var format = /[ `!@#$%^&*()_+\=\[\]{};':"\\|.<>\/?~]/;
            if(format.test(value) == true){
                return false;
            }else{
                return true;
            }
        }
        return this.optional( element ) || testMobile(value);
        
    }, "only 0-9 and ('-',',') allowed");

var validatorCustomer = jQuery("#addCustomerFormModal").validate({
    rules: {
        customer: {
            required: true,
            maxlength: 255
        },
        country: {
            maxlength: 150
        },
        state_name: {
            maxlength: 150
        },
        city: {
            maxlength: 150
        },
        city: {
            maxlength: 150
        },
        pin_code: {
            maxlength: 6
        },
        phone_no: {
            maxlength: 255,
            varifymobile: true,
            //  remote:'/validate-phone'
        },
        email: {
            maxlength: 150,
            email:true
        //    remote:'/validate-email'
        },
        
        web_address: {
            maxlength: 150
        },
        person_name: {
            maxlength: 100
        },
        designation: {
            maxlength: 100
        },
        mobile_no: {
            maxlength: 255,
            varifymobile: true,
            //remote:'/validate-mobile'
        }		
    },
    messages: {
        customer: {
            required: "Please enter customer",
            maxlength: "Maximum 255 characters allowed"
        },
        country: {
            maxlength: "Maximum 150 characters allowed"
        },
        state_name: {
            maxlength: "Maximum 150 characters allowed"
        },
        city: {
            maxlength: "Maximum 150 characters allowed"
        },
        pin_code: {
            maxlength: "Maximum 6 characters allowed"
        },
        phone_no: {
            maxlength: "Maximum 255 characters allowed",
        },
        email: {
            maxlength: "Maximum 150 characters allowed",
            email: "Please Enter Valid Email"
            //remote:"Email already exist"
        },
        web_address: {
            maxlength: "Maximum 150 characters allowed"
        },
        person_name: {
            maxlength: "Maximum 100 characters allowed"
        },
        designation: {
            maxlength: "Maximum 100 characters allowed"
        },
        mobile_no: {
            maxlength: "Maximum 255 characters allowed",
        }
    },
    submitHandler: function(form) {
        var formdata = jQuery('#addCustomerFormModal').serialize();
        jQuery('#customerModal').find('#addCustomerModal').addClass('btn-loader');
        jQuery.ajax({
            url: "{{ route('add_customer_detail') }}",
            type: 'POST',
            data: formdata,
            
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery('#customerModal').find('#addCustomerModal').removeClass('btn-loader');
                if(data.response_code == 1){
                    
                    toastSuccess(data.response_message);
                    document.getElementById("addCustomerFormModal").reset();
                    validatorCustomer.resetForm();
                    jQuery('#addCustomerFormModal').find('input#customer').focus();
                    jQuery('#customerModal').modal('hide');

                    getCustomers('.mst-customer');
            
                }else{
                    toastError(data.response_message);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#customerModal').find('#addCustomerModal').removeClass('btn-loader');
                var errMessage = JSON.parse(jqXHR.responseText);
                
                if(errMessage.errors){
                    validatorCustomer.showErrors(errMessage.errors);
                    
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

jQuery('#customerModal').on('keyup','#country',function(e) {
    if(e.which != 9){
        jQuery('#customerModal').find("#country").addClass('file-loader');
        var query = e.target.value; 
        jQuery.ajax({
            url:"{{ route('search') }}",
            type:"GET",
            data:{'term':query},
            success:function (data) {
                jQuery('#customerModal').find("#country").removeClass('file-loader');
                if(data.response_code == "1"){
                    jQuery('#customerModal').find('#country_list').html(data.countryList);
                }else{
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#customerModal').find("#country").removeClass('file-loader');
                console.log(JSON.parse(jqXHR.responseText));
            }
        })                     
    }
});

jQuery('#customerModal').on('keyup','#state_name',function(e) {
    if(e.which != 9){
        jQuery('#customerModal').find("#state_name").addClass('file-loader');
        var query = e.target.value; 
        jQuery.ajax({
            url:"{{ route('search_state_name') }}",
            type:"GET",
            data:{'term':query},
            
            success:function (data) {
                jQuery('#customerModal').find("#state_name").removeClass('file-loader');
                if(data.response_code == "1"){
                    jQuery('#customerModal').find('#state_name_list').html(data.stateNameList);
                }else{
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#customerModal').find("#state_name").removeClass('file-loader');
                console.log(JSON.parse(jqXHR.responseText));
            }
        })
    }
});
    

jQuery('#customerModal').on('keyup','#city',function(e) {
    if(e.which != 9){
        var query = e.target.value;
        jQuery('#customerModal').find("#city").addClass('file-loader');
        jQuery.ajax({
            url:"{{ route('search_city') }}",
            type:"GET",
            data:{'term':query},
            success:function (data) {
                jQuery('#customerModal').find("#city").removeClass('file-loader');
                if(data.response_code == "1"){
                    jQuery('#customerModal').find('#city_list').html(data.cityList);
                }else{
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#customerModal').find("#city").removeClass('file-loader');
                console.log(JSON.parse(jqXHR.responseText));
            }
        })
    }
});

//<--On modal show-->//

function ManageTop($This){
    if(jQuery('#partModal').is(':visible')){
        jQuery($This).addClass('extra-top');
        jQuery($This).addClass('over');
    }else{
        jQuery($This).removeClass('extra-top');
        jQuery($This).removeClass('over');
    }
}

jQuery('#currencyModal').on('show.bs.modal',function(e){
    ManageTop(this);
    setTimeout(() => {
        jQuery(this).find('#currency_code').focus();
    },600);
});

jQuery('#processModal').on('show.bs.modal',function(e){

    var triggerFrom = e.relatedTarget;
    var processType = jQuery(triggerFrom).attr('pr-type');

    if(processType == "Process"){
        jQuery('#processModal').find('#type').val('Process');
    }else if(processType == "Machining"){
        jQuery('#processModal').find('#type').val('Machining');
    }else {
        jQuery('#processModal').find('#type').val('Development');
    }
    
    jQuery('#processModal').find('.chzn-select').trigger('liszt:updated');
    
    ManageTop(this);
    setTimeout(() => {
        jQuery(this).find('#process').focus();
    },600);
});

jQuery('#customerModal').on('show.bs.modal',function(e){
    ManageTop(this);
    setTimeout(() => {
        jQuery(this).find('#customer').focus();
    },600);
});

jQuery('#incoTermModal').on('show.bs.modal',function(e){
    ManageTop(this);
    setTimeout(() => {
        jQuery(this).find('#name').focus();
    },600);
});

jQuery('#termConditionModal').on('show.bs.modal',function(e){
    ManageTop(this);
    setTimeout(() => {
        jQuery(this).find('#terms').focus();
    },600);
});

jQuery('#materialModal').on('show.bs.modal',function(e){
    ManageTop(this);   
    setTimeout(() => {
        jQuery(this).find('#name').focus();
    },600);
});

//<--On modal hide-->//
jQuery('#currencyModal').on('hide.bs.modal',function(e){
    document.getElementById("addCurrencyFormModal").reset();
    validatorCurrency.resetForm();
});

jQuery('#processModal').on('hide.bs.modal',function(e){
    document.getElementById("addProcessFormModal").reset();
    jQuery('#processModal').find('#type').val('').trigger('liszt:updated');
    validatorProcess.resetForm();
});

jQuery('#customerModal').on('hide.bs.modal',function(e){
    document.getElementById("addCustomerFormModal").reset();
    validatorCustomer.resetForm();
});

jQuery('#incoTermModal').on('hide.bs.modal',function(e){
    document.getElementById("addIncoTermFormModal").reset();
    validatorIncoTerm.resetForm();
});

jQuery('#termConditionModal').on('hide.bs.modal',function(e){
    document.getElementById("addTermConditionFormModal").reset();
    validatorTermCondition.resetForm();
});

jQuery('#materialModal').on('hide.bs.modal',function(e){
    document.getElementById("addMaterialFormModal").reset();
    validatorMaterial.resetForm();
});