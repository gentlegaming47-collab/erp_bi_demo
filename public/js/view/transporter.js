
let hiddenTransporterId = jQuery('#commonTransporterForm').find('input:hidden[name="id"]').val();

function validateEmail(e) {

    var email = e;
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if (email != '' && email != null && emailReg.test(email)) {
        jQuery("#commonTransporterForm").find('#contact_person_email_id').closest('.control-group').removeClass('error');
        return true;
    }
}

function validateGSTIN(e) {

    var gstin = e;
    // var GSTIINregexp = /^([0][1-9]|[1-2][0-9]|[3][0-7])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/;
    var GSTIINregexp = /[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9a-zA-Z]{3}$/;
    if (gstin != '' && gstin != null && GSTIINregexp.test(gstin)) {
        return true;
    }
}

function validatePAN(e) {
    var pan = e;
    var PANregexp = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
    if (PANregexp != '' && PANregexp != null && PANregexp.test(pan)) {
        jQuery("#commonTransporterForm").find('#pan').removeClass('error');
        return true;
    }
}

jQuery.validator.addMethod("varifymobile", function (value, element) {
    function testMobile(val) {
        var format = /[`!@#$%^&*()_,\=\[\]{};':"\\|.<>\/?~]/;
        if (format.test(value) == true) {
            return false;
        } else {
            return true;
        }
    }
    return this.optional(element) || testMobile(value);
}, "only 0-9 and ('-','+') allowed");


jQuery(document).ready(function () {


    if (hiddenTransporterId != "" && hiddenTransporterId != undefined) {
        var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

        jQuery.ajax({

            url: RouteBasePath + "/get-transporters/" + hiddenTransporterId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {


                    jQuery('#transporter_name').val(data.transporter_data.transporter_name);
                    jQuery('#address').val(data.transporter_data.address);
                    jQuery('#pan').val(data.transporter_data.pan);
                    jQuery('#gstin').val(data.transporter_data.gstin);
                    jQuery('#type_of_vehicle').val(data.transporter_data.type_of_vehicle);
                    jQuery('#contact_person').val(data.transporter_data.contact_person);
                    jQuery('#contact_person_mobile').val(data.transporter_data.contact_person_mobile);
                    jQuery('#contact_person_email_id').val(data.transporter_data.contact_person_email_id);
                    jQuery('#payment_terms').val(data.transporter_data.payment_terms);

                    jQuery('#status').val(data.transporter_data.status);

                    var statusHtml = '';

                    if (data.transporter_data.approval_status == 'deactive_approval_pending' || data.transporter_data.approval_status == 'approval_pending') {

                        statusHtml = `<option value="approval_pending">Active Approval Pending</option>
                        <option value="deactive_approval_pending">Deactive Approval Pending</option>
                        <option value="active">Active</option> 
                        <option value="deactive">Deactive</option>`;

                        jQuery('#transporter_status').empty().append(statusHtml).trigger('liszt:updated');

                        jQuery('#transporter_status').val(data.transporter_data.approval_status).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);



                    } else {

                        statusHtml = `<option value="active">Active</option> 
                        <option value="deactive">Deactive</option>`;

                        jQuery('#transporter_status').empty().append(statusHtml).trigger('liszt:updated');


                        jQuery('#transporter_status').val(data.transporter_data.approval_status).trigger('liszt:updated');
                    }



                } else {

                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        if (RouteBasePath + "/manage-transporter" == true) {
                            window.location.href = RouteBasePath + "/manage-transporter";
                        }
                        // window.location.href = "{{ route('manage-country')}}";
                    });

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                var errMessage = JSON.parse(jqXHR.responseText);



                if (jqXHR.status == 401) {



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
    else {
        jQuery('div#statushide').hide();
    }







    var validator = jQuery("#commonTransporterForm").validate({

        // onkeyup: false,
        // onfocusout: false,

        rules: {
            transporter_name: {

                required: true,

                maxlength: 255

            },
            contact_person_mobile: {
                varifymobile: true
            },
            // contact_person_email_id: {
            //     email: true
            // },
            // gstin: {

            //     maxlength: 15,

            //     gstInValidator: true

            // },

            // pan: {

            //     maxlength: 10,

            //     panValidator: true

            // },
            transporter_status: {
                required: true
            },

        },

        messages: {

            transporter_name: {

                required: "Please Enter Transporter Name",

                maxlength: "Maximum 255 Characters Allowed"

            },
            //  contact_person_email_id: {
            //     email: "Please Enter Valid Email"
            // }, 
            // gstin: {

            //     maxlength: "Maximum 15 Characters Are Allowed For Gstin"

            // },

            // pan: {

            //     maxlength: "Maximum 10 Characters Are Allowed For Pan No."

            // },
            transporter_status: {
                required: "Please Select Status"
            },

        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            var pan = jQuery('#pan').val();
            if (pan != '' && (!validatePAN(pan))) {
                // jAlert('Please Enter Valid Email');
                toastElement('Please Enter Valid PAN No.', "#pan")
                return false;
            }

            var gstin = jQuery('#gstin').val();
            if (gstin != '' && (!validateGSTIN(gstin))) {

                toastElement('Please Enter Valid GST No.', "#gstin")
                return false;
            }

            var email = jQuery('#contact_person_email_id').val();
            if (email != '' && (!validateEmail(email))) {
                // jAlert('Please Enter Valid Email');
                toastElement('Please Enter Valid Email', "#contact_person_email_id")
                return false;
            }




            var formdata = jQuery('#commonTransporterForm').serialize();

            let transporterName = jQuery("#transporter_name").val();

            let formUrl = hiddenTransporterId != undefined && hiddenTransporterId != "" ? RouteBasePath + "/update-transporters" : RouteBasePath + "/store-transporter";

            if (transporterName != '' && transporterName != undefined) {

                jQuery.ajax({
                    url: RouteBasePath + "/verify-transporter?transporter_name=" + transporterName + "&id=" + hiddenTransporterId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            // jAlert(data.response_message);
                            toastElement(data.response_message, "#transporter_name");
                        } else {
                            jQuery.ajax({

                                url: formUrl,

                                type: 'POST',

                                data: formdata,

                                headers: headerOpt,

                                dataType: 'json',

                                processData: false,

                                success: function (data) {

                                    if (data.response_code == 1) {


                                        if (hiddenTransporterId != undefined && hiddenTransporterId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-transporter";
                                            });
                                            addedTransporterGroup(true);
                                        }
                                        else if (hiddenTransporterId == undefined || hiddenTransporterId == "") {
                                            toastSuccess(data.response_message, nextFn);

                                            function nextFn() {

                                                document.getElementById("commonTransporterForm").reset();

                                                // validator.resetForm();

                                                // jQuery('#country_name').val('');
                                                //window.location.reload();
                                                // jQuery('#transporter_name').focus();
                                                jQuery("#transportModal").modal("hide");
                                                jQuery('#transporter_name').focus();
                                                jQuery('#transporter_id').trigger('liszt:activate');
                                            }

                                            //toastSuccess(data.response_message, nextFn);
                                            // jQuery("#transportModal").modal("hide");
                                            addedTransporterGroup(true);
                                        }
                                        else {
                                            toastError(data.response_message);
                                        }





                                    } else {
                                        console.log("main else");
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





function suggestTransporter(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#transporter_name").addClass('file-loader');

        var search = jQuery($this).val();




        jQuery.ajax({

            url: RouteBasePath + "/transporter-list?term=" + search,

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#transporter_name").removeClass('file-loader');

                if (data.response_code == 1) {

                    // console.log(transporterList);
                    jQuery('#transporter_name_list').html(data.transporterList);

                } else {

                    toastError(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#transporter_name").removeClass('file-loader');

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


function checkTransporter(transporter_name) {
    let transporterName = jQuery("#transporter_name").val();
    let id = jQuery("#id").val();
    jQuery.ajax({
        url: RouteBasePath + "/verify-transporter/?transporter_name=" + transporterName + "&id=" + id,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jAlert(data.response_message);
                toastElement(data.response_message, "#transporter_name");

            } else if (data.response_code == 2) {
                //
            } else {
                // jAlert('error');
            }
        }
    });
}



jQuery(document).on('click', '#transporter_name_list', function (e) {
    var suggest = e.target.innerHTML;
    jQuery('#transporter_name').val(suggest);
    var hidden = jQuery('#trans').val();
    var suggestion_list = jQuery('#transporter_name_list').html;

    var transporter_name = hidden;
    if (suggestion_list != '') {
        checkTransporter(transporter_name);
    }
});



function verifyTransporter() {
    var transporter_name = jQuery('#transporter_name').val();
    var hidden = jQuery('#trans').val();
    var suggestion_list = jQuery('#transporter_name_list').html;

    if (transporter_name != '') {
        checkTransporter(transporter_name);
    }
}



function getTransporterName($this = null) {



    if ($this != null) {

        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

    }


    jQuery.ajax({

        url: RouteBasePath + "/get-transporter",
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }


            if (data.response_code == 1) {

                if ($this != null) {

                    var stgDrpHtml = `<option value="">Select Transporer</option>`;

                    for (let indx in data.transporer) {
                        stgDrpHtml += `<option value="${data.transporer[indx].id}">${data.transporer[indx].transporter_name}</option>`;
                    }
                    jQuery($this).each(function (e) {
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

function addedTransporterGroup($event) {
    if ($event == true) {
        getTransporterName(".mst-transporter");
    }
}

