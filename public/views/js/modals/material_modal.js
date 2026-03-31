var materialValidator = jQuery("#addMaterialFormModal").validate({
    rules: {
        material_name: {
            required: true,
            maxlength: 255
        }			
    },
    messages: {
        material_name: {
            required: "Please enter material",
            maxlength: "Maximum 255 characters allowed"
        }
    },
    submitHandler: function(form) {
        jQuery('#addMaterialFormModal').find('#addMaterialModal').addClass('btn-loader');
        var formdata = jQuery('#addMaterialFormModal').serialize();
        jQuery.ajax({
            url: RouteBasePath+"/store-material",
            type: 'POST',
            data: formdata,
           
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery('#addMaterialFormModal').find('#addMaterialModal').removeClass('btn-loader');
                if(data.response_code == 1){
                    
                    toastSuccess(data.response_message);
                    jQuery('#materialModal').modal('hide');
                    addedMaterial(true);
            
                }else{
                    toastError(data.response_message);
                    addedMaterial(false);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown){
                addedMaterial(false);
                jQuery('#addMaterialFormModal').find('#addMaterialModal').removeClass('btn-loader');
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


function suggestMaterial(e,$this){
var keyevent = e
var thisModal = jQuery('#addMaterialFormModal');
if(keyevent.key != "Tab"){
thisModal.find("#material_name").addClass('file-loader');
var search = jQuery($this).val();

jQuery.ajax({
    url: RouteBasePath+"/material-list?term="+encodeURI(search),
    type: 'GET',
    dataType: 'json',
    processData: false,
    success: function (data) {
        thisModal.find("#material_name").removeClass('file-loader');
        if(data.response_code == 1){
            thisModal.find('#material_name_list').html(data.materialList);
        }else{
            toastError(data.response_message);
        }
    },
    error: function (jqXHR, textStatus, errorThrown){
        thisModal.find("#material_name").removeClass('file-loader');
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
}

//<--On modal show-->//
jQuery('#materialModal').on('show.bs.modal',function(e){  
if(jQuery('#jobCardModal').is(':visible')){
    jQuery('#materialModal').removeClass('over');
    jQuery('#materialModal').addClass('over-over-over');
}else{
    jQuery('#materialModal').removeClass('over-over-over');
    jQuery('#materialModal').addClass('over');
}
setTimeout(() => {
    jQuery(this).find('#material_name').focus();
},600);
});

//<--On modal hide-->//
jQuery('#materialModal').on('hide.bs.modal',function(e){
document.getElementById("addMaterialFormModal").reset();
materialValidator.resetForm();
});