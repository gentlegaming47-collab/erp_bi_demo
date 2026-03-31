setTimeout(() => {
    jQuery('#dealer_name').focus();
}, 100);

let customerHiddenId = jQuery('#commonDealerForm').find('input:hidden[name="id"]').val();

var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

jQuery(".checkEmail").on("focusout", function (e) {
    if (e.target.value != '')
        validateEmail(e.target.value);

});

// jQuery(document).ready(function(){
//     generateCustomercode();
// });

function validateEmail(e) {

    var email = e;
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if (email != '' && email != null && emailReg.test(email)) {
        jQuery("#commonDealerForm").find('#email').removeClass('error');
        return true;
    } else {
        jAlert('Please Enter Valid Email ID');
        jQuery("#popup_ok").click(function () {
            setTimeout(() => {
                jQuery("#commonDealerForm").find('#email').addClass('error');
                jQuery("#commonDealerForm").find('#email').focus();
            }, 100);
        });
        return false;
    }
}

// contact 
var contact_data = [];

function validateContactEmail(e) {

    var email = e;
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if (email != '' && email != null && emailReg.test(email)) {
        jQuery("#contact_form").find('#contact_email').removeClass('error');
        return true;
    } else {
        jAlert('Please Enter Valid Email');
        jQuery("#popup_ok").click(function () {
            setTimeout(() => {
                jQuery("#contact_form").find('#contact_email').addClass('error');
                jQuery("#contact_form").find('#contact_email').focus();
            }, 100);
        });
        return false;
    }
}

jQuery(".checkContactEmail").on("change", function (e) {
    if (e.target.value != '')
        validateContactEmail(e.target.value);

});

function removeContactDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            let formIndx = jQuery(th).closest("tr").find('input[name="form_indx"]').val();
            removeFormObj(formIndx);
            jQuery(th).closest("tr").remove();
        }
    });
}

function editContactDetails(th) {
    let formIndx = jQuery(th).closest("tr").find('input[name="form_indx"]').val();
    let rawIndx = jQuery(th).closest('tr').index();
    fillContactForm(formIndx, rawIndx);
}

jQuery(document).ready(function () {
    changeUploadDesign();
    jQuery(window).resize(function () {
        changeUploadDesign();
    });

    if (customerHiddenId != "" && customerHiddenId != undefined) {

        jQuery.ajax({

            url: RouteBasePath + "/get-dealer/" + customerHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {

                    jQuery('#dealer_name').val(data.customer.dealer_name);
                    jQuery('#dealer_code').val(data.customer.dealer_code);

                    jQuery('#address').val(data.customer.address);



                    jQuery('#customer_country_id').val(data.customer.c_id).trigger('liszt:updated');

                    jQuery('#customer_state_id').val(data.customer.s_id).trigger('liszt:updated');

                    jQuery('#customer_district_id').val(data.customer.d_id).trigger('liszt:updated');

                    jQuery('#customer_taluka_id').val(data.customer.t_id).trigger('liszt:updated');

                    jQuery('#customer_village_id').val(data.customer.village_id).trigger('liszt:updated');

                    jQuery('#pincode').val(data.customer.pincode);

                    jQuery('#mobile_no').val(data.customer.mobile_no);

                    jQuery('#email').val(data.customer.email);

                    jQuery('#pan').val(data.customer.PAN);

                    jQuery('#gstin').val(data.customer.gst_code);

                    jQuery('#aadhar_no').val(data.customer.aadhar_no);

                    //  jQuery('#aggrement_start_date').val(data.customer.aggrement_start_date);

                    //  jQuery('#aggrement_end_date').val(data.customer.aggrement_end_date);

                    jQuery('input:hidden[name="id"]').val(data.customer.id);

                    //  jQuery('#cheque_no').val(data.customer.cheque_no);

                    // jQuery('#dealer_status').val(data.customer.status).trigger('liszt:updated');
                    jQuery('#status').val(data.customer.status);
                    var statusHtml = '';

                    if (data.customer.approval_status == 'deactive_approval_pending' || data.customer.approval_status == 'approval_pending') {
                        statusHtml = `<option value="approval_pending">Active Approval Pending</option>
                        <option value="deactive_approval_pending">Deactive Approval Pending</option>
                        <option value="active">Active</option> 
                        <option value="deactive">Deactive</option>`;

                        jQuery('#dealer_status').empty().append(statusHtml).trigger('liszt:updated');

                        jQuery('#dealer_status').val(data.customer.approval_status).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
                    } else {
                        statusHtml = `<option value="active">Active</option> 
                        <option value="deactive">Deactive</option>`;

                        jQuery('#dealer_status').empty().append(statusHtml).trigger('liszt:updated');

                        jQuery('#dealer_status').val(data.customer.approval_status).trigger('liszt:updated');
                    }


                    jQuery('#account_name').val(data.customer.account_name);
                    jQuery('#bank_name').val(data.customer.bank_name);
                    jQuery('#branch_name').val(data.customer.branch_name);
                    if (data.customer.account_no != 0) {
                        jQuery('#account_no').val(data.customer.account_no);
                    } else {
                        jQuery('#account_no').val('');
                    }

                    jQuery('#account_type').val(data.customer.account_type);

                    if (data.customer.ifsc_code != 0) {
                        jQuery('#ifsc_code').val(data.customer.ifsc_code);
                    } else {
                        jQuery('#ifsc_code').val('');
                    }

                    // jQuery('#micr_code').val(data.customer.micr_code);
                    if (data.customer.micr_code != 0) {
                        jQuery('#micr_code').val(data.customer.micr_code);
                    } else {
                        jQuery('#micr_code').val('');
                    }
                    // jQuery('#swift_code').val(data.customer.swift_code);
                    if (data.customer.swift_code != 0) {
                        jQuery('#swift_code').val(data.customer.swift_code);
                    } else {
                        jQuery('#swift_code').val('');
                    }
                    /*  if (data.customer.aggrement_document != "" && data.customer.aggrement_document != null) {
  
                          jQuery('#aggrement_document_doc').val(data.customer.aggrement_document);
  
                          jQuery('#aggrement_document_prev').attr('href', data.customer.file_path + data.customer.aggrement_document);
  
                          jQuery('#aggrement_document_prev').removeClass('hidden');
  
  
  
                          jQuery('.remove-file').addClass('i-block').removeClass('hidden');
  
  
                      } else {
  
                          jQuery('#aggrement_document_doc').val();
  
                          jQuery('#aggrement_document_prev').attr('href', '#');
  
                          jQuery('#aggrement_document_prev').addClass('hidden');
  
                          jQuery('.remove-file').removeClass('i-block').addClass('hidden');
  
                      }*/

                    //agreement details
                    if (data.agreement_details.length > 0 && !jQuery.isEmptyObject(data.agreement_details)) {
                        for (let ind in data.agreement_details) {
                            agreement_data.push(data.agreement_details[ind]);
                        }
                        fillAgreementTable();
                    }


                    getCustomerStates().done(function (resposne) {
                        jQuery('#customer_state_id').val(data.customer.s_id).trigger('liszt:updated');

                        getCustomerDistrict().done(function (resposne) {
                            jQuery('#customer_district_id').val(data.customer.d_id).trigger('liszt:updated');

                            getCustomerTaluka().done(function (resposne) {
                                jQuery('#customer_taluka_id').val(data.customer.t_id).trigger('liszt:updated');

                                getCustomerVillage().done(function (resposne) {
                                    jQuery('#customer_village_id').val(data.customer.village_id).trigger('liszt:updated');
                                });
                            });
                        });
                    });

                    if (data.customer.in_use == true) {
                        jQuery('#customer_country_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#customer_state_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#customer_district_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#customer_taluka_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#customer_village_id').prop({ tabindex: -1 }).attr('readonly', true);

                    } else {
                        jQuery('#customer_country_id').val(data.customer.c_id).trigger('liszt:updated');
                        jQuery('#customer_state_id').val(data.customer.s_id).trigger('liszt:updated');
                        jQuery('#customer_district_id').val(data.customer.d_id).trigger('liszt:updated');
                        jQuery('#customer_taluka_id').val(data.customer.t_id).trigger('liszt:updated');
                        jQuery('#customer_village_id').val(data.customer.village_id).trigger('liszt:updated');
                    }

                    if (data.customer.aggrement_start_date != null) {
                        var aggrement_start_date = jQuery('#aggrement_start_date').val();

                        jQuery(".date-picker1:not([readonly])").datepicker("destroy");

                        if (aggrement_start_date) {
                            var minDates = jQuery.datepicker.parseDate("dd/mm/yy", aggrement_start_date);

                            if (minDates != null) {
                                jQuery(".date-picker1:not([readonly])").datepicker({
                                    dateFormat: "dd/mm/yy",
                                    minDate: minDates,
                                });
                            }
                        }
                    }
                    if (data.contact.length > 0 && !jQuery.isEmptyObject(data.contact)) {
                        for (let ind in data.contact) {
                            contact_data.push(data.contact[ind]);
                        }
                        fillContactTable();
                    }


                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-dealer";
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
    else {
        jQuery('div#statushide').hide();
        // generateCustomercode();
    }



    // Store or Update

    var validator = jQuery("#commonDealerForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            dealer_name: {
                required: true,
                maxlength: 255
            },
            dealer_code: {
                required: true,
            },
            customer_country_id: {
                required: true
            },
            customer_state_id: {
                required: true
            },
            customer_district_id: {
                required: true
            },
            customer_taluka_id: {
                required: true
            },
            customer_village_id: {
                required: true
            },
            village_id: {
                required: true,
            },
            pincode: {
                numberFormat: true
            },
            mobile_no: {
                numberFormat: true
            },
            account_name:
            {
                required: true,
            },
            bank_name:
            {
                required: true
            },
            account_no:
            {
                required: true
            },
            ifsc_code:
            {
                required: true
            },

            // aggrement_start_date: {
            //     required: function () {

            //         if (jQuery("#aggrement_end_date").val() != "") {
            //             return true;
            //         } else {
            //             return false;
            //         }

            //     }
            // },
            // aggrement_end_date: {
            //     required: function () {

            //         if (jQuery("#aggrement_start_date").val() != "") {
            //             return true;
            //         } else {
            //             return false;
            //         }

            //     },

            // },
            dealer_status: {
                required: true
            },
        },

        messages: {

            dealer_name: {
                required: "Please Enter Dealer Name",
                maxlength: "Maximum 255 Characters Allowed"
            },
            dealer_code: {
                required: "Please Enter Dealer Code",
            },
            customer_country_id: {
                required: "Please Select Country"
            },
            customer_state_id: {
                required: "Please Select State"
            },
            customer_district_id: {
                required: "Please Select District"
            },
            customer_taluka_id: {
                required: "Please Select Taluka"
            },
            village_id: {
                required: "Please Select Village",
            },
            // aggrement_start_date: {
            //     required: "Please Enter Aggrement Start Date",
            // },
            // aggrement_end_date: {
            //     required: "Please Enter Aggrement End Date",
            // },
            dealer_status: {
                required: "Please Select Status",
            },
            account_name: {
                required: "Please Enter Account Name",
            },
            bank_name:
            {
                required: "Please Enter Bank Name",
            },
            account_no:
            {
                required: "Please Enter Account No ",
            },
            ifsc_code:
            {
                required: "Please Enter IFSC Code",
            },


        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            jQuery('#commonDealerForm').find('#dealer-btn').prop('disabled', true);

            // var startDate = jQuery("#aggrement_start_date").datepicker('getDate');
            // // var enddate = jQuery("#aggrement_end_date").datepicker('getDate');

            // var endDateObj = jQuery("#aggrement_end_date").val();

            // // Convert the string to a Date object
            // var enddate = jQuery.datepicker.parseDate("dd/mm/yy", endDateObj);

            // if (startDate != null && enddate != null) {

            //     if (startDate > enddate) {
            //         toastError("Aggrement End Date Must be Greater Than To Start Date");
            //         return false;
            //     }
            // }


            //var formdata = jQuery('#commonDealerForm').serialize();
            let customerName = jQuery("#dealer_name").val();
            let dealerCode = jQuery("#dealer_code").val();

            let formUrl = customerHiddenId != undefined && customerHiddenId != "" ? RouteBasePath + "/update-dealer" : RouteBasePath + "/store-dealer";

            jQuery('#commonDealerForm').find('#dealer-btn').attr('disabled', true);

            let email = jQuery("#commonDealerForm").find("#email").val();

            if (customerName != '' && customerName != undefined) {
                if (email != '' && (!validateEmail(email))) {

                    jAlert('Please Enter Valid Email ID');
                    jQuery("#popup_ok").click(function () {
                        setTimeout(() => {
                            jQuery("#commonDealerForm").find('#email').addClass('error');
                            jQuery("#commonDealerForm").find('#email').focus();
                        }, 100);
                    });
                    jQuery('#commonDealerForm').find('#dealer-btn').prop('disabled', false);
                }
                else {
                    jQuery.ajax({
                        url: RouteBasePath + "/verify-dealer?customer_name=" + customerName + "&id=" + customerHiddenId,
                        type: 'GET',
                        dataType: 'json',
                        processData: false,
                        success: function (data) {
                            if (data.response_code == 1) {
                                // jAlert(data.response_message);
                                toastElement(data.response_message, "#dealer_name");
                                jQuery('#commonDealerForm').find('#dealer-btn').attr('disabled', false);

                            }

                            else if (dealerCode != "" && dealerCode != undefined) {

                                jQuery.ajax({
                                    url: RouteBasePath + "/verify-dealer_code?dealer_code=" + dealerCode + "&id=" + customerHiddenId,
                                    type: 'GET',
                                    dataType: 'json',
                                    processData: false,
                                    success: function (data) {
                                        if (data.response_code == 1) {
                                            toastElement(data.response_message, "#dealer_code");
                                            jQuery('#commonDealerForm').find('#dealer-btn').attr('disabled', true);

                                        } else {

                                            jQuery('#commonDealerForm').find('#dealer-btn').attr('disabled', true);

                                            var data = new FormData(document.getElementById('commonDealerForm'));

                                            var formValue = Object.fromEntries(data.entries());
                                            let as1;

                                            as1 = Object.assign(formValue, {
                                                'contacts': JSON.stringify(contact_data),
                                                'agreement_details': JSON.stringify(agreement_data)
                                            });
                                            var formdata = new URLSearchParams(as1).toString();

                                            jQuery.ajax({

                                                url: formUrl,

                                                type: 'POST',

                                                data: formdata,

                                                headers: headerOpt,

                                                dataType: 'json',

                                                processData: false,

                                                success: function (data) {

                                                    if (data.response_code == 1) {


                                                        if (customerHiddenId != undefined && customerHiddenId != "") {

                                                            jAlert(data.response_message, 'Success', function (r) {
                                                                window.location.href = RouteBasePath + "/manage-dealer";
                                                            });
                                                            //addedVillage(true);
                                                            addedDealer(true);
                                                        }
                                                        else if (customerHiddenId == undefined || customerHiddenId == "") {

                                                            function nextFn() {

                                                                document.getElementById("commonDealerForm").reset();
                                                                jQuery('#commonDealerForm').find('#customer_country_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonDealerForm').find('#customer_state_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonDealerForm').find('#customer_district_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonDealerForm').find('#customer_taluka_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonDealerForm').find('#customer_village_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonDealerForm').find('#pan');
                                                                jQuery('#commonDealerForm').find('#gstin');
                                                                jQuery('#aggrement_document_remove').removeClass('i-block').addClass('hidden');
                                                                jQuery('#aggrement_document_prev').removeClass('i-block').addClass('hidden');
                                                                jQuery('#contactTable tbody').empty();
                                                                jQuery('#agreementTable tbody').empty();

                                                                // jQuery('#commonCustomerForm').find('#pan').prop('disabled', true);
                                                                // jQuery('#commonCustomerForm').find('#gstin').prop('disabled', true);

                                                                validator.resetForm();
                                                                // window.location.reload();

                                                                getDealerCode();
                                                                setTimeout(() => {
                                                                    jQuery('input#dealer_name').focus();
                                                                }, 200);
                                                                jQuery("#dealerModal").modal("hide");

                                                            }
                                                            jQuery('#commonDealerForm').find('#dealer-btn').attr('disabled', false);
                                                            toastSuccess(data.response_message, nextFn);
                                                            addedVillage(true);
                                                            addedDealer(true);
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
                                                    } else {
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
                }
            }
        }
    });

    // contact modal validator 
    var contactValidator = jQuery("#contact_form").validate({



        submitHandler: function (form) {


            var email = jQuery("#contact_form").find(".checkContactEmail").val();

            if (email != '' && (!validateContactEmail(email))) {
                jAlert('Please Enter Valid Email');
                jQuery("#popup_ok").click(function () {
                    setTimeout(() => {
                        jQuery("#contact_form").find('#contact_email').addClass('error');
                        jQuery("#contact_form").find('#contact_email').focus();
                    }, 100);
                });
                return;
            } else {
                setTimeout(() => {
                    jQuery("#contact_form").find('#contact_email').removeClass('error');
                }, 100);
            }

            var data = new FormData(document.getElementById('contact_form'));
            var formValue = Object.fromEntries(data.entries());
            var thisModal = jQuery('#contactModal');

            if (formValue.contact_person.trim() || formValue.contact_email.trim() || formValue.contact_mobile_no.trim()) {

                var noDuplicate = true;
                if (formValue.form_type == "edit") {
                    jQuery('#contactTable tbody input[name*="contact_person[]"]').each(function (indx) {
                        if (formValue.contact_person == jQuery(this).val() && formValue.row_index != jQuery(this).closest('tr').index()) {
                            noDuplicate = false;
                            return;
                        }
                    });
                } else {
                    jQuery('#contactTable tbody input[name*="contact_person[]"]').each(function (indx) {
                        if (formValue.contact_person == jQuery(this).val()) {
                            noDuplicate = false;
                            return;
                        }
                    });
                }
                if (noDuplicate) {
                    thisModal.find('#contact_person').closest('div.control-group').removeClass('error');
                    var contact_person = formValue.contact_person ? formValue.contact_person : "";
                    var contact_mobile_no = formValue.contact_mobile_no ? formValue.contact_mobile_no : "";
                    var contact_email = formValue.contact_email ? formValue.contact_email : "";

                    if (contact_person != "" || contact_mobile_no != "" || contact_email) {

                        if (formValue.form_type == "edit") {
                            contact_data[formValue.form_index] = formValue;
                            let tblHtml = ``;
                            tblHtml += `<td>
                                <a onclick="editContactDetails(this)"><i class="iconfa-pencil action-icon edit-contact"></i></a>
                                <a onclick="removeContactDetails(this)"><i class="action-icon iconfa-trash remove-contact"></i></a>
                                <input type="hidden" name="form_indx" value="${formValue.form_index}"/>
                                </td>`;
                            tblHtml += `<td>${contact_person}<input type='hidden' name='contact_person[]' value="${contact_person}"/></td>`;
                            tblHtml += `<td>${contact_mobile_no}<input type='hidden' name='contact_mobile_no[]' value="${contact_mobile_no}"/></td>`;
                            tblHtml += `<td>${contact_email}<input type='hidden' name='contact_email[]' value="${contact_email}"/></td>`;
                            jQuery('#contactTable tbody').find('tr').eq(formValue.row_index).empty().append(tblHtml);
                        } else {
                            contact_data.push(formValue)
                            let formIndx = contact_data.indexOf(formValue);
                            if (jQuery('#contactTable tbody').find('#noContact').length > 0) {
                                jQuery('#contactTable tbody').empty();
                            }

                            let tblHtml = `<tr>`;
                            tblHtml += `<td>
                                <a onclick="editContactDetails(this)"><i class="iconfa-pencil action-icon edit-contact"></i></a>
                                <a onclick="removeContactDetails(this)"><i class="action-icon iconfa-trash remove-contact"></i></a>
                                <input type="hidden" name="form_indx" value="${formIndx}"/>
                                </td>`;
                            tblHtml += `<td>${contact_person}<input type='hidden' name='contact_person[]' value="${contact_person}"/></td>`;
                            tblHtml += `<td>${contact_mobile_no}<input type='hidden' name='contact_mobile_no[]' value="${contact_mobile_no}"/></td>`;
                            tblHtml += `<td>${contact_email}<input type='hidden' name='contact_email[]' value="${contact_email}"/>
                                </td>`;
                            tblHtml += `</tr>`;
                            jQuery('#contactTable tbody').append(tblHtml);

                        }
                    }
                    thisModal.modal('hide');

                } else {
                    thisModal.find('#contact_person').closest('div.control-group').addClass('error').focus();
                    toastError("Name Is Already Taken");
                }
            } else {
                toastError("Please Enter Atleast One Field Value");
                jQuery("#popup_ok").click(function () {
                    setTimeout(() => {
                        thisModal.find('#contact_person').focus();
                    }, 100);
                });
            }
        }
    });
    if (customerHiddenId == undefined) {
        getDealerCode();
    }

});


// edit time 

//<--On modal show-->//
jQuery('#contactModal').on('show.bs.modal', function (e) {
    let thisForm = jQuery('#contactModal');
    let formType = thisForm.find("#form_type").val();
    if (formType == "add") {
        jQuery('span.checked').removeClass('checked');
        jQuery('div.error').removeClass('error');
        thisForm.find('flabel').text("Add");
        thisForm.find('slabel').text("Add");
        setTimeout(() => {
            thisForm.find("#contact_person").focus();
        }, 300)
    } else {
        thisForm.find('flabel').text("Edit");
        thisForm.find('slabel').text("Update");
    }
});


//<--On modal hide-->//

jQuery('#contactModal').on('hide.bs.modal', function (e) {
    let thisForm = jQuery('#contactModal');
    thisForm.find("#form_type").val("add");
    thisForm.find("#form_index").val("");
    thisForm.find("#row_index").val("");
    jQuery('#contact_form').trigger("reset");
});

function removeFormObj(formIndx) {
    delete contact_data[formIndx];
}

function fillContactForm(formIndx, rawIndx) {
    let thisForm = jQuery('#contactModal');
    thisForm.find("#form_type").val("edit");
    thisForm.find("#form_index").val(formIndx);
    thisForm.find("#row_index").val(rawIndx);
    var frmData = contact_data[formIndx];
    thisForm.find("#contact_person").val(frmData.contact_person);
    thisForm.find("#contact_mobile_no").val(frmData.contact_mobile_no);
    thisForm.find("#contact_email").val(frmData.contact_email);
    thisForm.modal('show');
}

function fillContactTable() {

    if (contact_data.length > 0) {
        for (let key in contact_data) {
            let formIndx = contact_data.indexOf(contact_data[key]);
            var contact_person = contact_data[key].contact_person ? contact_data[key].contact_person : "";
            var contact_mobile_no = contact_data[key].contact_mobile_no ? contact_data[key].contact_mobile_no : "";
            var contact_email = contact_data[key].contact_email ? contact_data[key].contact_email : "";

            if (jQuery('#contactTable tbody').find('#noContact').length > 0) {
                jQuery('#contactTable tbody').empty();
            }
            let tblHtml = `<tr>`;
            tblHtml += `<td>
            <a onclick="editContactDetails(this)"><i class="iconfa-pencil action-icon edit-contact"></i></a>
            <a onclick="removeContactDetails(this)"><i class="action-icon iconfa-trash remove-contact"></i></a>
            <input type="hidden" name="form_indx" value="${formIndx}"/>
            </td>`;
            tblHtml += `<td>${contact_person}<input type='hidden' name='contact_person[]' value="${contact_person}"/></td>`;
            tblHtml += `<td>${contact_mobile_no}<input type='hidden' name='contact_mobile_no[]' value="${contact_mobile_no}"/></td>`;
            tblHtml += `<td>${contact_email}<input type='hidden' name='contact_email[]' value="${contact_email}"/>
            </td>`;
            tblHtml += `</tr>`;
            jQuery('#contactTable tbody').append(tblHtml);
        }
    }
}


// Customer  Duplication  Code


function checkDealerName(customer) {
    jQuery.ajax({
        url: RouteBasePath + "/verify-dealer?customer_name=" + customer + "&id=" + customerHiddenId,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                // jAlert(data.response_message);
                toastElement(data.response_message, "#dealer_name");
                jQuery('#dealer-btn').attr('disabled', true);
            } else {
                jQuery('#dealer-btn').attr('disabled', false);
                // jAlert('error');
            }
        }
    });
}

function verifyDealer() {
    var customer = jQuery('#dealer_name').val();
    var hidden = jQuery('#del_name').val();
    var suggestion_list = jQuery('#dealer_list').html;

    if (suggestion_list != '') {
        checkDealerName(customer);
    }
}



jQuery(document).on('click', '#dealer_list', function (e) {
    // jQuery('#customer').val('');
    var suggest = e.target.innerHTML;
    jQuery('#del_name').val(suggest);
    var hidden = jQuery('#del_name').val();
    var suggestion_list = jQuery('#dealer_list').html;

    var customer = hidden;
    if (suggestion_list != '') {
        checkDealerName(customer);
    }
});


// suggestionList

function suggestDealer(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#dealer_name").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/dealer-list?term=" + encodeURI(search),



            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#dealer_name").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#dealer_list').html(data.customerList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#dealer_name").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestDealerCode(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#dealer_code").addClass('file-loader');

        var search = jQuery($this).val();

        jQuery.ajax({

            url: RouteBasePath + "/dealer-code_list?term=" + encodeURI(search),

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#dealer_code").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#dealer_code_list').html(data.codeList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#dealer_code").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);

                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);

                } else if (jqXHR.status == 401) {

                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
}

function checkDealerCode(dealer_code) {
    jQuery.ajax({
        url: RouteBasePath + "/verify-dealer_code?dealer_code=" + dealer_code + "&id=" + customerHiddenId,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                toastElement(data.response_message, "#dealer_code");
                jQuery('#dealer-btn').attr('disabled', true);
            } else {
                jQuery('#dealer-btn').attr('disabled', false);
            }
        }
    });
}

function verifyDealerCode() {
    var dealer_code = jQuery('#dealer_code').val();
    var hidden = jQuery('#del_code').val();
    var suggestion_list = jQuery('#dealer_code_list').html;

    if (suggestion_list != '') {
        checkDealerCode(dealer_code);
    }
}

jQuery(document).on('click', '#dealer_code_list', function (e) {
    var suggest = e.target.innerHTML;
    jQuery('#del_code').val(suggest);
    var hidden = jQuery('#del_code').val();
    var suggestion_list = jQuery('#dealer_code_list').html;

    var dealer_code = hidden;
    if (suggestion_list != '') {
        checkDealerCode(dealer_code);
    }
});



// Dependent Drop Down Code   State   District  Taluka   Village

function getCustomerStates(event) {

    let countryId = jQuery('#customer_country_id option:selected').val();


    if (countryId != "" && countryId !== undefined) {

        jQuery("#country_id").val(countryId).trigger('liszt:updated');

        jQuery('#cityModal #country_name').val(jQuery('#customer_country_id option:selected').text());



        // if (countryId == 1) {
        //     jQuery('#gst_code').prop('disabled', false);
        // } else {
        //     jQuery('#gst_code').prop('disabled', true);
        // }

        return jQuery.ajax({

            url: RouteBasePath + "/get-location-states?country_id=" + countryId,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    let dropHtml = `<option value=''>Select State</option>`;
                    if (!jQuery.isEmptyObject(data.states) && data.states.length > 0) {
                        for (let idx in data.states) {
                            dropHtml += `<option value="${data.states[idx].id}">${data.states[idx].state_name}</option>`;
                        }
                    }
                    jQuery('#customer_state_id').empty().append(dropHtml).trigger('liszt:updated');

                    jQuery('#state_id').empty().append(dropHtml).trigger('liszt:updated');

                    jQuery('#taluka_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    jQuery('#village_state_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#customer_state_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getCustomerDistrict(event) {
    let stateId = jQuery('#customer_state_id option:selected').val();
    jQuery("#state_id").val(stateId).trigger('liszt:updated');
    if (stateId != "" && stateId !== undefined) {
        return jQuery.ajax({
            url: RouteBasePath + "/get-district?state_id=" + stateId,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    let dropHtml = `<option value=''>Select District</option>`;
                    if (!jQuery.isEmptyObject(data.cities) && data.cities.length > 0) {
                        for (let idx in data.cities) {
                            dropHtml += `<option value="${data.cities[idx].id}">${data.cities[idx].district_name}</option>`;
                        }
                    }
                    jQuery('#customer_district_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#customer_district_id').empty().append("<option value=''>Select District</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getCustomerTaluka(event) {

    let districtId = jQuery('#customer_district_id option:selected').val();



    if (districtId != "" && districtId !== undefined) {
        return jQuery.ajax({

            url: RouteBasePath + "/get-taluka?district_id=" + districtId,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    let dropHtml = `<option value=''>Select Taluka</option>`;
                    if (!jQuery.isEmptyObject(data.taluka) && data.taluka.length > 0) {
                        for (let idx in data.taluka) {
                            dropHtml += `<option value="${data.taluka[idx].id}">${data.taluka[idx].taluka_name}</option>`;
                        }
                    }
                    jQuery('#customer_taluka_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#customer_taluka_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getCustomerVillage(event) {
    let talukaId = jQuery('#customer_taluka_id option:selected').val();
    if (talukaId != "" && talukaId !== undefined) {
        return jQuery.ajax({


            url: RouteBasePath + "/get-village?taluka_id=" + talukaId,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    let dropHtml = `<option value=''>Select Village</option>`;
                    if (!jQuery.isEmptyObject(data.village) && data.village.length > 0) {
                        for (let idx in data.village) {
                            dropHtml += `<option value="${data.village[idx].id}">${data.village[idx].village_name}</option>`;
                        }
                    }
                    jQuery('#customer_village_id').empty().append(dropHtml).trigger('liszt:updated');

                } else {
                    jQuery('#customer_village_id').empty().append("<option value=''>Select Village</option>").trigger('liszt:updated');
                }
            },
        });
    }
}


// generate customer code
// function generateCustomercode() {       
//     jQuery.ajax({            
//         url: RouteBasePath + "/get-latest-customer-code",
//         type: 'GET',
//         headers: headerOpt,
//         dataType: 'json',
//         processData: false,
//         success: function (data) {         
//             jQuery('#customer_code').removeClass('file-loader');
//             if (data.response_code == 1) {                  
//                 jQuery('#customer_code').val(data.cust_code);
//                 jQuery('#customer_code').attr('readonly', true);                  
//             } else {
//                // console.log(data.response_message)
//             }
//         },
//         error: function (jqXHR, textStatus, errorThrown) {
//             jQuery('#customer_code').removeClass('file-loader');
//             console.log('Field To Get Latest OA No.!')
//         }
//     });  
// }


// gst code enable disable code

// function cname()
// {
//     var cname = jQuery('#customer_country_id option:selected').val();
//     if (cname == 1) {
//         jQuery('#commonCustomerForm').find('#pan').prop('disabled', false);       
//     } else {
//         jQuery('#commonCustomerForm').find('#pan').prop('disabled', true);        
//     }
// }



jQuery("#customer_village_id").on("change", function () {
    let page = jQuery("#IsAllState").data('page');

    let getVillageData = jQuery('#customer_village_id option:selected').val();
    if (getVillageData != null && getVillageData != "" && page == "add")
        changePincode();
    // else     
    //     jQuery("#pincode").val('');               
});




function changePincode() {
    let getVillageData = jQuery('#customer_village_id option:selected').val();
    if (getVillageData != "" && getVillageData !== undefined) {
        jQuery.ajax({
            url: RouteBasePath + "/get-villageData?village_id=" + getVillageData,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                // console.log(data);
                if (data.response_code == 1) {
                    jQuery("#pincode").val(data.pincode);
                } else {
                    jAlert(data.response_message);
                }
            },
        });
    }
}



// Modal code value are not reset after submission


jQuery('#stateModal').on('show.bs.modal', function (e) {
    let country = jQuery("#customer_country_id").val();

    jQuery("#country_id").val(country).trigger('liszt:updated');


    if (jQuery('#country_id option:selected').val() == "1") {

        jQuery('#gst_code').prop("disabled", false);

    } else {

        jQuery('#gst_code').prop("disabled", true);

        jQuery('#gst_code').val('');

    }

});

jQuery('#cityModal').on('show.bs.modal', function (e) {
    let state = jQuery("#customer_state_id").val();
    let country = jQuery("#customer_country_id").val();

    jQuery("#state_id").val(state).trigger('liszt:updated');

    if (country != '') {
        jQuery('#commonDistrictForm #country_name').val(jQuery('#customer_country_id option:selected').text());
    }

});

jQuery('#talukaModal').on('show.bs.modal', function (e) {
    let dist = jQuery("#customer_district_id").val();
    let state = jQuery("#customer_state_id").val();
    let country = jQuery("#customer_country_id").val();



    jQuery("#taluka_state_id").val(state).trigger('liszt:updated');

    if (state != '' && state != null) {
        getDistrict().done(function (resposne) {
            jQuery("#taluka_district_id").val(dist).trigger('liszt:updated');
        });
    }

    if (country != '') {
        jQuery('#commonTalukaForm #country_name').val(jQuery('#customer_country_id option:selected').text());
    }


});
jQuery('#VillageModal').on('show.bs.modal', function (e) {

    let dist = jQuery("#customer_district_id").val();
    let state = jQuery("#customer_state_id").val();
    let taluka = jQuery("#customer_taluka_id").val();
    let country = jQuery("#country_name").val();

    jQuery("#village_state_id").val(state).trigger('liszt:updated');
    jQuery("#district_id").val(dist).trigger('liszt:updated');

    if ((dist != '' && dist != null) || (taluka != '' && taluka != null)) {
        getDistrictData().done(function (resposne) {
            jQuery("#district_id").val(dist).trigger('liszt:updated');

            getTalukaData().done(function (resposne) {
                jQuery("#taluka_id").val(taluka).trigger('liszt:updated');
            });
        });
    }

    if (country != '') {
        jQuery('#commonVillageForm #country_name').val(jQuery('#customer_country_id option:selected').text());
    }
});

function getDealer($this = null) {


    if ($this != null) {
        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');
    }
    var page = jQuery('#hidViewPage').val();
    var customer_group_id = jQuery("#salesorderform").find('#customer_group_id option:selected').val();

    if (page == 'salesOrder') {
        var url = RouteBasePath + "/get-dealers?pagename=" + page + "&customer_group_id=" + customer_group_id;
    } else {
        var url = RouteBasePath + "/get-dealers?pagename=" + page;

    }


    jQuery.ajax({

        url: url,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {

            if ($this != null) {
                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');
            }

            if (data.response_code == 1) {
                if ($this != null) {


                    var stgDrpHtml = `<option value="">Select Dealer</option>`;

                    for (let indx in data.dealer) {

                        stgDrpHtml += `<option value="${data.dealer[indx].id}">${data.dealer[indx].dealer_name}</option>`;

                    }

                    jQuery($this).each(function (e) {
                        let Id = jQuery(this).attr('id');
                        let Selected = jQuery(this).find("option:selected").val();
                        jQuery(this).empty().append(stgDrpHtml);
                        jQuery(this).val(Selected).trigger('liszt:updated');

                    });

                }

            } else {
                if (page == 'salesOrder') {
                    jQuery('#dealer_id').empty().append("<option value=''>Select Dealer</option>").trigger('liszt:updated');
                } else {
                    toastError(data.response_message);
                }
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

function addedDealer($event) {
    if ($event == true) {
        getDealer(".mst-dealer");
    }
}



function validateImage(filePath) {

    var allowedExtensions = /(\.pdf)$/i;
    // var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
    if (!allowedExtensions.exec(filePath)) {
        return false;
    }
    return true;
}


function fileUpload(e, type = null) {




    var form_data = new FormData();

    // Read selected files

    var target = e.target;

    var id = target.id;

    var files = target.files;

    var totalfiles = files.length;



    var oldImg = jQuery('#' + id + '_doc').val();



    jQuery('#' + id).parent().parent().find('.uneditable-input').removeClass('iconfa-warning-sign upload-error');

    if (totalfiles > 0) {



        var notValid = 0;

        for (var index = 0; index < totalfiles; index++) {


            if (type == 'file') {
                if (validateImage(files[index].name) == true) {

                    form_data.append("docs[]", files[index]);

                } else {

                    notValid = 1;

                    toastError("Only PDF files are allowed.");

                    e.stopImmediatePropagation();

                    return false;

                }

            } else {

                if (validateImage(files[index].name) == true) {

                    form_data.append("docs[]", files[index]);

                } else {

                    notValid = 1;

                    toastError("Only (pdf) files are allowed.");

                    e.stopImmediatePropagation();

                    return false;

                }
            }


        }



        if (notValid == 0) {



            jQuery('#' + id).parent().parent().parent().find('.uneditable-input').addClass('file-loader');



            jQuery.ajax({

                url: RouteBasePath + "/upload-docs",

                type: 'POST',

                data: form_data,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                contentType: false,



                success: function (data) {

                    jQuery('#' + id).parent().parent().parent().find('.uneditable-input').removeClass('file-loader');

                    if (data.response_code == 1) {



                        if (oldImg != "") {

                            removeMedia(oldImg);

                        }

                        jQuery('#' + id + '_doc').val(data.files);

                        jQuery('#' + id + '_prev').attr('href', data.files_url);

                        jQuery('#' + id + '_prev').removeClass('hidden');

                        jQuery('#' + id + '_img-prev-box').html(`<img class="img-polaroid img" alt="image preview" src="${data.files_url}"/>`);

                        jQuery('#' + id + '_img-prev-box').removeClass('hidden');

                        jQuery('#' + id + '_remove').addClass('i-block').removeClass('hidden');



                        // console.log(data.response_message);



                    } else {

                        console.log(data.response_message);

                    }

                },

                error: function (jqXHR, textStatus, errorThrown) {

                    jQuery('#' + id).parent().parent().find('.uneditable-input').removeClass('file-loader');

                    jQuery('#' + id).parent().parent().find('.uneditable-input').addClass('iconfa-warning-sign upload-error');

                    var errMessage = JSON.parse(jqXHR.responseText);



                    if (errMessage.errors) {

                        Validator.showErrors(errMessage.errors);



                    } else if (jqXHR.status == 401) {



                        toastError(jqXHR.statusText);

                    } else {



                        toastError('Something went wrong!');

                        console.log(JSON.parse(jqXHR.responseText));

                    }

                }

            });

        }

    } else {



        if (oldImg != "") {

            removeMedia(oldImg);

        }

        jQuery('#' + id + '_doc').val('');

        jQuery('#' + id + '_prev').attr('href', '#');

        jQuery('#' + id + '_prev').addClass('hidden');

        jQuery('#' + id + '_img-prev-box').addClass('hidden');

        jQuery('#' + id + '_img-prev-box').html('');

        jQuery('#' + id + '_remove').removeClass('i-block').addClass('hidden');

    }

}


function removeFile(e, type = null) {


    e.stopImmediatePropagation();


    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete ?', 'Confirmation', function (r) {



        if (r === true) {

            jQuery(".icon").removeClass(".iconfa-file");

            var target = e.target;

            var id = target.getAttribute("data-remove");

            var fileName = jQuery('#' + id + '_doc').val();

            var oldImg = jQuery('#' + id + '_doc').val();



            jQuery('#' + id).parent().parent().find('.uneditable-input').removeClass('iconfa-warning-sign upload-error');



            if (oldImg != "" && type != 'soft') {

                removeMedia(oldImg)

            }

            if (type == 'soft') {
                jQuery('#' + id + '_soft_delete').val(oldImg);
            }

            jQuery('#' + id + '_doc').val('');

            jQuery('#' + id + '_prev').attr('href', '#');

            jQuery('#' + id + '_prev').addClass('hidden');

            jQuery('#' + id + '_remove').removeClass('i-block').addClass('hidden');

            jQuery('#' + id + '_img-prev').html('');

            jQuery('#' + id + '_img-prev-box').addClass('hidden');

            jQuery('#' + id + '_img-prev-box').html('');

        }

    })



}

function removeMedia(docName) {



    let form_data2 = new FormData();

    form_data2.append('docs[]', docName);

    jQuery.ajax({

        url: RouteBasePath + "/remove-docs",

        type: 'POST',

        data: form_data2,

        headers: headerOpt,

        dataType: 'json',

        processData: false,

        contentType: false,

        success: function (data) {

            if (data.response_code == 1) {

                console.log(data.response_message);



            } else {

                console.log(data.response_message);

            }

        },

        error: function (jqXHR, textStatus, errorThrown) {



            var errMessage = JSON.parse(jqXHR.responseText);



            if (errMessage.errors) {

                Validator.showErrors(errMessage.errors);



            } else if (jqXHR.status == 401) {



                toastError(jqXHR.statusText);

            } else {



                toastError('Something went wrong!');

                console.log(JSON.parse(jqXHR.responseText));

            }

        }

    });

}

jQuery('#agreement_start_date').on('change', function (e) {
    var agreement_start_date = jQuery('#agreement_start_date').val();
    jQuery('#agreement_end_date').val('');
    var minDate = jQuery.datepicker.parseDate("dd/mm/yy", agreement_start_date);
    jQuery(".date-picker1:not([readonly])").datepicker("destroy");
    if (minDate != null) {
        jQuery(".date-picker1:not([readonly])").datepicker({
            dateFormat: "dd/mm/yy",
            minDate: minDate,
        });
    }
});

// jQuery(document).on('keydown', '#agreement_start_date, #agreement_end_date', function(e) {
//     e.preventDefault();
// });

function changeUploadDesign() {
    var $containerWidth = jQuery(window).width();
    if ($containerWidth > 1900) {
        var divElement = document.querySelector('.changeStyle');
        var spanElement = document.createElement('span');
        spanElement.className = divElement.className;
        spanElement.innerHTML = divElement.innerHTML;
        divElement.parentNode.replaceChild(spanElement, divElement);
    }
    else {
        var spanElement = document.querySelector('.changeStyle');
        var divElement = document.createElement('div');
        divElement.className = spanElement.className;
        divElement.innerHTML = spanElement.innerHTML;
        spanElement.parentNode.replaceChild(divElement, spanElement);
    }
}

function getDealerCode() {

    jQuery.ajax({
        url: RouteBasePath + "/get-dealer_code",
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jQuery("#dealer_code").val(data.dealer_code);
            } else {

            }
        },
    });

}

var agreement_data = [];


// Dealer Agreement Details
var DealerValidator = jQuery("#agreement_form").validate({

    //onclick: false,
    ignore: [], //to validate "document_doc" hidden field
    rules: {
        agreement_start_date: {
            required: true
        },
        agreement_end_date: {
            required: true
        },
        agreement_document_doc: {
            required: true,
        }
    },
    messages: {
        agreement_start_date: {
            required: "Please Enter The Agreement Start Date."
        },
        agreement_end_date: {
            required: "Please Enter The Agreement End Date."

        },
        agreement_document_doc: {
            required: "Please Upload The Agreement Document.",
        }
    },





    submitHandler: function (form) {


        var from = jQuery("#agreement_start_date").datepicker('getDate');
        var to = jQuery("#agreement_end_date").datepicker('getDate');

        if (from > to) {
            toastError("Agreement End Date Must Be Greather then or equal to Agreement Start Date");
            return false;
        }


        var data = new FormData(document.getElementById('agreement_form'));
        var formValue = Object.fromEntries(data.entries());
        var agreementModal = jQuery('#dealeragreementModal');

        var noDuplicate = true;
        var agree_strat_min_date = jQuery('#agree_strat_min_date').val();
        var agree_strat_max_date = jQuery('#agree_strat_max_date').val();
        var agree_end_max_date = jQuery('#agree_end_max_date').val();
        var agree_end_min_date = jQuery('#agree_end_min_date').val();

        var from_date = formValue.agreement_start_date;
        var to_date = formValue.agreement_end_date;


        if (from_date) {

            if (agree_strat_min_date) {
                if (parseDate(agree_strat_min_date) >= parseDate(from_date)) {
                    toastError('The agreement date is invalid as a record already exists within this date range.');
                    return;
                }

            } else {
                if (parseDate(agree_strat_max_date) <= parseDate(from_date)) {
                    toastError('The agreement date is invalid as a record already exists within this date range.');
                    return;
                }
            }

        }

        if (to_date) {
            if (agree_end_max_date) {
                if (parseDate(agree_end_max_date) <= parseDate(to_date)) {
                    toastError('The agreement date is invalid as a record already exists within this date range.');

                    return;
                }
            } else {
                if (parseDate(agree_end_min_date) > parseDate(to_date)) {
                    toastError('The agreement date is invalid as a record already exists within this date range.');
                    return;
                }
            }

        }
        /* var lastEndDate = null; // Variable to store the last agreement end date
 
 
         jQuery('#agreementTable tbody input[name*="agreement_end_date[]"]').each(function () {
 
             var dateStr = jQuery(this).val(); 
 
             var parts = dateStr.split("/");
             var formattedDate = parts[2] + "/" + parts[1] + "/" + parts[0]; // Rearrange to "YYYY-MM-DD"
         
             var existingEndDate = new Date(formattedDate); 
         
             if (!lastEndDate || existingEndDate > lastEndDate) {
                 lastEndDate = existingEndDate;
             }
 
             // Prevent duplicate end dates
             if (formValue.agreement_end_date == jQuery(this).val()) {
                 noDuplicate = false;
                 return false; 
             }
         });
 
         // Check if new Start Date is AFTER the last End Date
         if (lastEndDate && from <= lastEndDate || to <= lastEndDate) {
             jAlert("New Agreement Start Date must be AFTER the last Agreement End Date");
             return false;
         }
 
 
         if (formValue.form_type == "edit") {
             jQuery('#agreementTable tbody input[name*="agreement_end_date[]"]').each(function (indx) {
                 if (formValue.agreement_end_date == jQuery(this).val() && formValue.row_index != jQuery(this).closest('tr').index()) {
                     noDuplicate = false;
                     return;
                 }
             });
         } else {
             jQuery('#agreementTable tbody input[name*="agreement_end_date[]"]').each(function (indx) {
                 if (formValue.agreement_end_date == jQuery(this).val()) {
                     noDuplicate = false;
                     return;
                 }
             });
         }*/

        if (noDuplicate) {
            agreementModal.find('#agreement_end_date').closest('div.control-group').removeClass('error');


            var agreement_start_date = formValue.agreement_start_date ? formValue.agreement_start_date : "";
            var agreement_end_date = formValue.agreement_end_date ? formValue.agreement_end_date : "";
            var agreement_document_doc = formValue.agreement_document_doc ? formValue.agreement_document_doc : "";

            var cheque_no = formValue.cheque_no ? formValue.cheque_no : "";


            if (formValue.form_type == "edit") {
                agreement_data[formValue.form_index] = formValue;
                let tblHtml = ``;
                tblHtml += `<td>
                                <a onclick="editAgreementDetails(this)"><i class="iconfa-pencil action-icon edit-contact"></i></a>
                                <a onclick="removeAgreementDetails(this)"><i class="action-icon iconfa-trash remove-contact"></i></a>
                                <input type="hidden" name="form_indx" value="${formValue.form_index}"/>
                                </td>`;
                tblHtml += `<td>${agreement_start_date}<input type='hidden' name='agreement_start_date[]' value="${agreement_start_date}"/></td>`;
                tblHtml += `<td>${agreement_end_date}<input type='hidden' name='agreement_end_date[]' value="${agreement_end_date}"/></td>`;

                if (agreement_document_doc != "") {
                    tblHtml += `<td><a target="_blank" href="${uploadURL + agreement_document_doc}" title="view"><i class="iconfa-eye-open action-icon"></i></a><input type='hidden' name='agreement_document_doc[]' value="${agreement_document_doc}"/>`;

                } else {

                    tblHtml += `<td><input type='hidden' name='agreement_document_doc[]' value=""/>`;

                }

                tblHtml += `<td>${cheque_no}<input type='hidden' name='cheque_no[]' value="${cheque_no}"/></td>`;
                jQuery('#agreementTable tbody').find('tr').eq(formValue.row_index).empty().append(tblHtml);
            } else {
                agreement_data.push(formValue)
                let formIndx = agreement_data.indexOf(formValue);
                if (jQuery('#agreementTable tbody').find('#noContact').length > 0) {
                    jQuery('#agreementTable tbody').empty();
                }

                let tblHtml = `<tr>`;
                tblHtml += `<td>
                                <a onclick="editAgreementDetails(this)"><i class="iconfa-pencil action-icon edit-contact"></i></a>
                                <a onclick="removeAgreementDetails(this)"><i class="action-icon iconfa-trash remove-contact"></i></a>
                                <input type="hidden" name="form_indx" value="${formIndx}"/>
                                </td>`;
                tblHtml += `<td>${agreement_start_date}<input type='hidden' name='agreement_start_date[]' value="${agreement_start_date}"/></td>`;
                tblHtml += `<td>${agreement_end_date}<input type='hidden' name='agreement_end_date[]' value="${agreement_end_date}"/></td>`;

                if (agreement_document_doc != "") {

                    tblHtml += `<td><a target="_blank" href="${uploadURL + agreement_document_doc}" title="view"><i class="iconfa-eye-open action-icon"></i></a><input type='hidden' name='agreement_document_doc[]' value="${agreement_document_doc}"/>`;
                } else {
                    tblHtml += `<td><input type='hidden' name='agreement_document_doc[]' value=""/>`;
                }


                tblHtml += `<td>${cheque_no}<input type='hidden' name='cheque_no[]' value="${cheque_no}"/></td>`;
                tblHtml += `</tr>`;
                jQuery('#agreementTable tbody').append(tblHtml);

            }
            agreementModal.modal('hide');

        }
        else {
            toastError("Duplicate Agreement End Date is not allowed.");
        }
    }
});


function removeAgreementDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            let formIndx = jQuery(th).closest("tr").find('input[name="form_indx"]').val();
            removeDealerFormObj(formIndx);
            jQuery(th).closest("tr").remove();
        }
    });
}

function removeDealerFormObj(formIndx) {
    delete agreement_data[formIndx];
    agreement_data = agreement_data.filter(element => element != null);
}

function editAgreementDetails(th) {
    let formIndx = jQuery(th).closest("tr").find('input[name="form_indx"]').val();
    let rawIndx = jQuery(th).closest('tr').index();
    fillAgreementForm(formIndx, rawIndx);
}


jQuery('#dealeragreementModal').on('show.bs.modal', function (e) {
    let agreementForm = jQuery('#dealeragreementModal');
    let AggrementformType = agreementForm.find("#form_type").val(); // "add" or "edit"
    let lastEndDate = null;

    // Find the latest end date in the table
    jQuery("#agreementTable tbody tr").each(function () {
        let currentEndDate = jQuery(this).find('td:eq(2)').text().trim(); // Get end date column
        let parsedDate = moment(currentEndDate, 'DD/MM/YYYY', true);
        if (parsedDate.isValid() && (!lastEndDate || parsedDate.isAfter(lastEndDate))) {
            lastEndDate = parsedDate;
        }
    });

    if (AggrementformType === "add") {
        let newStartDate = lastEndDate ? lastEndDate.add(1, 'days').format('DD/MM/YYYY') : moment().format("DD/MM/YYYY");

        // Set start date field
        agreementForm.find("#agreement_start_date").val(newStartDate);

        // Initialize start date picker
        if (agreement_data.length > 0) {

            jQuery("#agreement_start_date").datepicker('destroy').datepicker({
                dateFormat: "dd/mm/yy",
                minDate: newStartDate // Ensure start date is at least the next day
            });

            // Initialize end date picker
            jQuery("#agreement_end_date").datepicker('destroy').datepicker({
                dateFormat: "dd/mm/yy",
                minDate: newStartDate // Ensure end date is at least start date
            });


            jQuery('#agree_strat_min_date').val(lastEndDate.subtract(1, 'days').format('DD/MM/YYYY')
            );
            jQuery('#agree_end_min_date').val(newStartDate);
        } else {
            jQuery('#agree_strat_min_date').val('');
            jQuery('#agree_strat_max_date').val('');
            jQuery('#agree_end_max_date').val('');
            jQuery('#agree_end_min_date').val('');
        }



        setTimeout(() => {

            agreementForm.find('#document_doc').val('');

            agreementForm.find('#agreement_document_doc').val('');

            agreementForm.find('#agreement_document_prev').attr('href', '#');

            agreementForm.find('#agreement_document_prev').addClass('hidden');

            agreementForm.find('#agreement_document_remove').removeClass('i-block').addClass('hidden');

            agreementForm.find('#agreement_document_img-prev').html('');

            agreementForm.find('#agreement_document_img-prev-box').addClass('hidden');

            agreementForm.find('#agreement_document_img-prev-box').html('');


        }, 300)

        agreementForm.find('flabel').text("Add");
        agreementForm.find('slabel').text("Add");

    } else { // Editing mode
        // agreementForm.find("#agreement_start_date").datepicker('destroy').datepicker({
        //     dateFormat: "dd/mm/yy",
        //     minDate: null // Allow selecting any date for edits
        // });

        // agreementForm.find("#agreement_end_date").datepicker('destroy').datepicker({
        //     dateFormat: "dd/mm/yy",
        //     minDate: null // Allow selecting any date for edits
        // });

        agreementForm.find('flabel').text("Edit");
        agreementForm.find('slabel').text("Update");
    }

    // Ensure the end date is never before the start date
    jQuery("#agreement_start_date").on("change", function () {
        let selectedStartDate = jQuery(this).val();
        jQuery("#agreement_end_date").datepicker("option", "minDate", selectedStartDate);
    });
});


jQuery('#dealeragreementModal').on('hide.bs.modal', function (e) {
    let agreementForm = jQuery('#dealeragreementModal');
    agreementForm.find("#form_type").val("add");
    agreementForm.find("#form_index").val("");
    agreementForm.find("#row_index").val("");
    jQuery('#agreement_form').trigger("reset");
});

function fillAgreementForm(formIndx, rawIndx) {
    let agreementForm = jQuery('#dealeragreementModal');
    agreementForm.find("#form_type").val("edit");
    agreementForm.find("#form_index").val(formIndx);
    agreementForm.find("#row_index").val(rawIndx);
    var agreementfrmData = agreement_data[formIndx];
    agreementForm.find("#agreement_start_date").val(agreementfrmData.agreement_start_date);
    agreementForm.find("#agreement_end_date").val(agreementfrmData.agreement_end_date);
    agreementForm.find("#agreement_document_doc").val(agreementfrmData.agreement_document_doc);
    agreementForm.find("#cheque_no").val(agreementfrmData.cheque_no);


    if (agreementfrmData.agreement_document_doc != "" && agreementfrmData.agreement_document_doc !== undefined) {

        agreementForm.find("#agreement_document_doc").val(agreementfrmData.agreement_document_doc);

        agreementForm.find('#agreement_document_prev').attr('href', uploadURL + agreementfrmData.agreement_document_doc);

        agreementForm.find('#agreement_document_prev').removeClass('hidden');

        agreementForm.find('#agreement_document_img-prev-box').removeClass('hidden');

        agreementForm.find('#agreement_document_remove').addClass('i-block').removeClass('hidden');

    } else {

        agreementForm.find('#agreement_document_doc').val('');

        agreementForm.find('#agreement_document_prev').attr('href', '#');

        agreementForm.find('#agreement_document_prev').addClass('hidden');

        agreementForm.find('#agreement_document_remove').removeClass('i-block').addClass('hidden');

        agreementForm.find('#agreement_document_img-prev').html('');

        agreementForm.find('#agreement_document_img-prev-box').addClass('hidden');

        agreementForm.find('#agreement_document_img-prev-box').html('');

    }
    agreementForm.modal('show');



    var prevIndex = parseInt(formIndx) - 1;
    var nextIndex = parseInt(formIndx) + 1;

    if (prevIndex < 0) {
        prevIndex = 0;
    }
    var minstartDate = agreement_data[prevIndex].agreement_end_date;
    var maxstartDate = agreement_data[nextIndex] != undefined ? agreement_data[nextIndex].agreement_start_date : null;

    if (formIndx == 0) {
        jQuery("#agreement_start_date").datepicker('destroy').datepicker({
            dateFormat: "dd/mm/yy",
            minDate: null,
            maxDate: maxstartDate // Ensure start date is at least the next day
        });
        agreementForm.find("#agree_strat_min_date").val('');
        agreementForm.find("#agree_strat_max_date").val(maxstartDate);
    } else {
        jQuery("#agreement_start_date").datepicker('destroy').datepicker({
            dateFormat: "dd/mm/yy",
            minDate: minstartDate,
            maxDate: maxstartDate // Ensure start date is at least the next day
        });
        agreementForm.find("#agree_strat_min_date").val(minstartDate);
        agreementForm.find("#agree_strat_max_date").val(maxstartDate);
    }


    jQuery("#agreement_end_date").datepicker('destroy').datepicker({
        dateFormat: "dd/mm/yy",
        minDate: agreementfrmData.agreement_start_date,
        maxDate: maxstartDate // Ensure start date is at least the next day
    });
    agreementForm.find("#agree_end_min_date").val(agreementfrmData.agreement_start_date);
    agreementForm.find("#agree_end_max_date").val(maxstartDate);
}

function fillAgreementTable() {

    if (agreement_data.length > 0) {
        for (let key in agreement_data) {
            let formIndx = agreement_data.indexOf(agreement_data[key]);

            var agreement_start_date = agreement_data[key].agreement_start_date ? agreement_data[key].agreement_start_date : "";
            var agreement_end_date = agreement_data[key].agreement_end_date ? agreement_data[key].agreement_end_date : "";
            var agreement_document_doc = agreement_data[key].agreement_document_doc ? agreement_data[key].agreement_document_doc : "";
            var cheque_no = agreement_data[key].cheque_no ? agreement_data[key].cheque_no : "";

            if (jQuery('#agreementTable tbody').find('#noContact').length > 0) {
                jQuery('#agreementTable tbody').empty();
            }
            let tblHtml = `<tr>`;
            tblHtml += `<td>
                <a onclick="editAgreementDetails(this)"><i class="iconfa-pencil action-icon edit-contact"></i></a>
                <a onclick="removeAgreementDetails(this)"><i class="action-icon iconfa-trash remove-contact"></i></a>
                <input type="hidden" name="form_indx" value="${formIndx}"/>
                </td>`;
            tblHtml += `<td>${agreement_start_date}<input type='hidden' name='agreement_start_date[]' value="${agreement_start_date}"/></td>`;
            tblHtml += `<td>${agreement_end_date}<input type='hidden' name='agreement_end_date[]' value="${agreement_end_date}"/></td>`;


            if (agreement_document_doc != "") {
                tblHtml += `<td><a target="_blank" href="${uploadURL + agreement_document_doc}" title="view"><i class="iconfa-eye-open action-icon"></i></a><input type='hidden' name='agreement_document_doc[]' value="${agreement_document_doc}"/>`;
            } else {
                tblHtml += `<td><input type='hidden' name='agreement_document_doc[]' value=""/>`;
            }


            tblHtml += `<td>${cheque_no}<input type='hidden' name='cheque_no[]' value="${cheque_no}"/>
                </td>`;
            tblHtml += `</tr>`;
            jQuery('#agreementTable tbody').append(tblHtml);
        }
    }
}
function parseDate(dateStr) {
    let parts = dateStr.split("/"); // Split by "/"
    let day = parseInt(parts[0], 10);
    let month = parseInt(parts[1], 10) - 1; // Month is 0-based in JS
    let year = 2000 + parseInt(parts[2], 10); // Convert YY to YYYY

    return new Date(year, month, day);
}






// End Dealer Agreements

