var validator = jQuery("#addCustomerGroupFormModal").validate({


    rules: {

        onkeyup: false,

        onfocusout: false,



        customer_group_name: {

            required: true,

            maxlength: 255

        },


    },

    messages: {

        customer_group_name: {

            required: "Please enter Customer Group name",

            maxlength: "Maximum 255 characters allowed"

        },



    },



    errorPlacement: function(error, element) {

        jAlert(error.text());

        return false;

    },





    submitHandler: function(form) {



        var formdata = jQuery('#addCustomerGroupFormModal').serialize();

        jQuery.ajax({

            url: RouteBasePath + "/store-customer-group",
            type: 'POST',

            data: formdata,



            dataType: 'json',

            processData: false,

            success: function(data) {

                if (data.response_code == 1) {



                    toastSuccess(data.response_message, nextFn);


                    //run this function if clicked on ok in message

                    function nextFn() {



                        // window.location.href = "{{ route('manage-unit')}}";




                        document.getElementById("addCustomerGroupFormModal").reset();

                        validator.resetForm();

                        jQuery('input#customer_group_name').focus();

                        jQuery('#customerGroupModel').modal('hide');

                        addedCustomerGroup(true);


                    }

                } else {

                    // toastError(data.response_message);

                    jAlert(data.response_message);
                    addedCustomerGroup(false);

                }



            },

            error: function(jqXHR, textStatus, errorThrown) {

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    // toastError(jqXHR.statusText);

                    jAlert(jqXHR.statusText);

                } else {

                    // toastError('Something went wrong!');

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

});



function suggestCustomerGroup(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#customer_group_name").addClass('file-loader');

        var search = jQuery($this).val();



        jQuery.ajax({

            url: RouteBasePath + "/customer-group-list/?term=" + encodeURI(search),


            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function(data) {

                jQuery("#customer_group_name").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#customer_group_name_list').html(data.customerGroupList);

                } else {

                    toastError(data.response_message);

                }

            },

            error: function(jqXHR, textStatus, errorThrown) {

                jQuery("#customer_group_name").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



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




