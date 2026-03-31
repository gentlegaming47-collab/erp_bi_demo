var materialValidator = jQuery("#addItemgroupModalForm").validate({
    rules: {
        item_group_name: {
            required: true,
            maxlength: 255
        },
        item_group_code: {
            required: true
        }		
    },
    messages: {
        item_group_name: {
            required: "Please enter item group",
            maxlength: "Maximum 255 characters allowed"
        }, 
        item_group_code: {
            required: "Please enter Group Code"
        }
    },
    submitHandler: function(form) {
        jQuery('#addItemgroupModalForm').find('#addItemgroupModalForm').addClass('btn-loader');
        var formdata = jQuery('#addItemgroupModalForm').serialize();
        jQuery.ajax({
            url: RouteBasePath + "/store-item-group",
            type: 'POST',
            data: formdata,
           
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {


                    toastSuccess(data.response_message, nextFn);
                    function nextFn() {

                      
                        jQuery("#item_group_name").val('');
                        jQuery("#item_group_code").val('');
                        jQuery('#itemGroupModal').modal('hide');
                        addedItems(true);

                    }


                } else {

                    toastError(data.response_message);
                    addedItems(false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown){
             
                
                var errMessage = JSON.parse(jqXHR.responseText);
               
                if(errMessage.errors){
                    materialValidator.showErrors(errMessage.errors);
                    
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

