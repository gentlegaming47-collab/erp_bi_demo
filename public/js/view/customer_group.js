

let customerGroupHiddenId = jQuery('#commonCustomerGroupForm').find('input:hidden[name="id"]').val();

var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };


jQuery(document).ready(function () {

    if (customerGroupHiddenId != "" && customerGroupHiddenId != undefined) {


        // get village data at edit time

        jQuery.ajax({

            url: RouteBasePath + "/get-customer-group/" + customerGroupHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {


                    jQuery('#customer_group_name').val(data.customerGroup.customer_group_name);



                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-customer_group";
                    });
                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                var errMessage = JSON.parse(jqXHR.responseText);



                if (jqXHR.status == 401) {
                    jAlert(jqXHR.statusText);




                } else {


                    jAlert('Something went wrong!');
                    // toastError('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }




    // Store or Update

    var validator = jQuery("#commonCustomerGroupForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            customer_group_name: {

                required: true,

                maxlength: 255

            }
        },

        messages: {

            customer_group_name: {

                required: "Please Enter Customer Group Name",

                maxlength: "Maximum 255 Characters Allowed"

            }
        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {


            var formdata = jQuery('#commonCustomerGroupForm').serialize();
            let customerGroupName = jQuery("#customer_group_name").val();
            jQuery('#cust_group').prop('disabled', true);

            let formUrl = customerGroupHiddenId != undefined && customerGroupHiddenId != "" ? RouteBasePath + "/update-customer_group" : RouteBasePath + "/store-customer_group";


            if (customerGroupName != '' && customerGroupName != undefined) {

                jQuery.ajax({
                    url: RouteBasePath + "/verify-customer-group?customer_group_name=" + customerGroupName + "&id=" + customerGroupHiddenId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            // jAlert(data.response_message);
                            toastElement(data.response_message, "#customer_group_name");

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


                                        if (customerGroupHiddenId != undefined && customerGroupHiddenId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-customer_group";
                                            });
                                            //addedVillage(true);
                                        }
                                        else if (customerGroupHiddenId == undefined || customerGroupHiddenId == "") {

                                            function nextFn() {

                                                document.getElementById("commonCustomerGroupForm").reset();

                                                validator.resetForm();
                                                jQuery('input#customer_group_name').focus();

                                                // jQuery('#country_name').val('');

                                            }

                                            toastSuccess(data.response_message, nextFn);
                                            addedCustomerGroup(true);
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

        // });

    });

});



// Customer  Duplication  Code


function checkCustomerGroupName(customer_group_name) {
    jQuery.ajax({
        url: RouteBasePath + "/verify-customer-group?customer_group_name=" + customer_group_name + "&id=" + customerGroupHiddenId,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jAlert(data.response_message);
                toastElement(data.response_message, "#customer_group_name");
            } else {
                // jAlert('error');
            }
        }
    });
}

function verifyCustomerGroup() {
    var customer_group_name = jQuery('#customer_group_name').val();
    var hidden = jQuery('#cus_gpname').val();
    var suggestion_list = jQuery('#customer_group_name_list').html;

    if (suggestion_list != '') {
        checkCustomerGroupName(customer_group_name);
    }
}



jQuery(document).on('click', '#customer_group_name_list', function (e) {
    // jQuery('#customer').val('');
    var suggest = e.target.innerHTML;
    jQuery('#cus_gpname').val(suggest);
    var hidden = jQuery('#cus_gpname').val();
    var suggestion_list = jQuery('#customer_group_name_list').html;

    var customer = hidden;
    if (suggestion_list != '') {
        checkCustomerGroupName(customer);
        //jQuery('#cname').val('');
        //return false;
    }
});


// suggestionList




function getCustomerGroup($this = null) {


    if ($this != null) {
        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');
    }

    jQuery.ajax({

        url: RouteBasePath + "/get-customer-groups",
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {

            if ($this != null) {
                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');
            }

            if (data.response_code == 1) {
                if ($this != null) {


                    var stgDrpHtml = `<option value=""></option>`;

                    for (let indx in data.customer_group_data) {
                        console.log(data.customer_group_data);

                        stgDrpHtml += `<option value="${data.customer_group_data[indx].id}">${data.customer_group_data[indx].customer_group_name}</option>`;

                    }

                    jQuery($this).each(function (e) {
                        console.log("true");
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

function addedCustomerGroup($event) {
    console.log("1");
    if ($event == true) {
        console.log("2");
        getCustomerGroup(".mst-customer_group");
    }
}


function suggestCustomerGroup(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#customer_group_name").addClass('file-loader');

        var search = jQuery($this).val();



        jQuery.ajax({

            url: RouteBasePath + "/customer-group-list?term=" + encodeURI(search),


            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#customer_group_name").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#customer_group_name_list').html(data.customerGroupList);

                } else {

                    toastError(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

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