

let supplierHiddenId = jQuery('#commonSupplierForm').find('input:hidden[name="id"]').val();

var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };


jQuery(".checkEmail").on("focusout", function (e) {
    if (e.target.value != '')
        validateEmail(e.target.value);

});



function validateEmail(e) {

    var email = e;
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if (email != '' && email != null && emailReg.test(email)) {
        jQuery("#commonSupplierForm").find('#contact_person_email_id').removeClass('error');
        return true;
    } else {
        jAlert('Please Enter Valid Email ID');
        jQuery("#popup_ok").click(function () {
            setTimeout(() => {
                jQuery("#commonSupplierForm").find('#contact_person_email_id').addClass('error');
                jQuery("#commonSupplierForm").find('#contact_person_email_id').focus();
            }, 100);
        });
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

    if (supplierHiddenId != "" && supplierHiddenId != undefined) {


        // get Supplier data at edit time

        jQuery.ajax({

            url: RouteBasePath + "/get-supplier/" + supplierHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {
                    jQuery('#supplier_name').val(data.supplier.supplier_name);
                    jQuery('#supplier_code').val(data.supplier.supplier_code);
                    jQuery('#address').val(data.supplier.address);
                    jQuery('#supplier_country_id').val(data.supplier.country_id).trigger('liszt:updated');
                    jQuery('#supplier_state_id').val(data.supplier.state_id).trigger('liszt:updated');
                    jQuery('#supplier_district_id').val(data.supplier.district_id).trigger('liszt:updated');
                    jQuery('#supplier_taluka_id').val(data.supplier.taluka_id).trigger('liszt:updated');
                    jQuery('#supplier_village_id').val(data.supplier.village_id).trigger('liszt:updated');
                    jQuery('#pincode').val(data.supplier.pincode);
                    jQuery('#contact_person').val(data.supplier.contact_person);
                    jQuery('#contact_person_mobile').val(data.supplier.contact_person_mobile);
                    jQuery('#contact_person_email_id').val(data.supplier.contact_person_email_id);
                    jQuery('#contact_person_email_id').val(data.supplier.contact_person_email_id);
                    jQuery('#web_address').val(data.supplier.web_address);
                    jQuery('#payment_terms').val(data.supplier.payment_terms);


                    if (data.supplier.no_item_mapping_required == 'Yes') {
                        jQuery('#no_item_mapping_required').trigger('click');
                    }

                    // jQuery('#status').val(data.supplier.status).trigger('liszt:updated');
                    jQuery('#status').val(data.supplier.status);
                    var statusHtml = '';

                    if (data.supplier.approval_status == 'deactive_approval_pending' || data.supplier.approval_status == 'approval_pending') {

                        statusHtml = `<option value="approval_pending">Active Approval Pending</option>
                        <option value="deactive_approval_pending">Deactive Approval Pending</option>
                        <option value="active">Active</option> 
                        <option value="deactive">Deactive</option>`;

                        jQuery('#supplier_status').empty().append(statusHtml).trigger('liszt:updated');

                        jQuery('#supplier_status').val(data.supplier.approval_status).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
                    } else {
                        statusHtml = `<option value="active">Active</option> 
                        <option value="deactive">Deactive</option>`;

                        jQuery('#supplier_status').empty().append(statusHtml).trigger('liszt:updated');


                        jQuery('#supplier_status').val(data.supplier.approval_status).trigger('liszt:updated');
                    }

                    jQuery('#gstin').val(data.supplier.GSTIN);
                    jQuery('#pan').val(data.supplier.PAN);
                    jQuery('input:hidden[name="id"]').val(data.supplier.id);


                    getSupplierState().done(function (resposne) {
                        jQuery('#supplier_state_id').val(data.supplier.state_id).trigger('liszt:updated');
                        getSupplierDistrict().done(function (resposne) {
                            jQuery('#supplier_district_id').val(data.supplier.district_id).trigger('liszt:updated');
                            getSupplierTaluka().done(function (resposne) {
                                jQuery('#supplier_taluka_id').val(data.supplier.taluka_id).trigger('liszt:updated');
                                getSupplierVillage().done(function (resposne) {
                                    jQuery('#supplier_village_id').val(data.supplier.village_id).trigger('liszt:updated');
                                });
                            });
                        });
                    });

                    // agreement details
                    if (data.agreement_details.length > 0 && !jQuery.isEmptyObject(data.agreement_details)) {
                        for (let ind in data.agreement_details) {
                            agreement_data.push(data.agreement_details[ind]);
                        }
                        fillAgreementTable();
                    }
                    // cname();
                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.supplier.href = "{{ route('manage-supplier')}}";
                    });
                }

            },

            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);
                if (jqXHR.status == 401) {
                    jAlert(jqXHR.statusText);
                } else {
                    jAlert('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
    else {
        jQuery('div#statushide').hide();
    }


    // Store or Update

    var validator = jQuery("#commonSupplierForm").validate({

        // focusInvalid: true,
        // onkeyup: false,
        onfocusout: false,
        rules: {
            supplier_name: {
                required: true
            },
            supplier_code: {
                required: true
            },
            supplier_country_id: {
                required: true
            },
            supplier_state_id: {
                required: true
            },
            supplier_district_id: {
                required: true
            },
            supplier_taluka_id: {
                required: true
            },
            supplier_village_id: {
                required: true
            },
            contact_person_mobile: {
                varifymobile: true
            },
            contact_person_email_id: {
                email: true
            },
            status:
            {
                required: true
            },
        },
        messages: {

            supplier_name: {
                required: "Please Enter Supplier Name"
            },
            supplier_code: {
                required: "Please Enter Supplier Code"
            },
            supplier_country_id: {
                required: "Please Select Country"
            },
            supplier_state_id: {
                required: "Please Select State"
            },
            supplier_district_id: {
                required: "Please Select District"
            },
            supplier_taluka_id: {
                required: "Please Select Taluka"
            },
            supplier_village_id: {
                required: "Please Select Village"
            },
            contact_person_email_id: {
                email: "Please Enter Valid Email"
            },

        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            // var formdata = jQuery('#commonSupplierForm').serialize();
            let supplierName = jQuery("#supplier_name").val();
            let supplierCode = jQuery("#supplier_code").val();
            let formUrl = supplierHiddenId != undefined && supplierHiddenId != "" ? RouteBasePath + "/update-supplier" : RouteBasePath + "/store-supplier";
            //  jQuery('#supplier_btn').attr('disabled', true);
            jQuery('#commonSupplierForm').find('#supplier_btn').prop('disabled', false);


            let email = jQuery("#commonSupplierForm").find(".checkEmail").val();
            if (supplierName != '' && supplierName != undefined) {
                if (email != '' && (!validateEmail(email))) {
                    jAlert('Please Enter Valid Email ID');
                    jQuery("#popup_ok").click(function () {
                        setTimeout(() => {
                            jQuery("#commonSupplierForm").find('#contact_person_email_id').addClass('error');
                            jQuery("#commonSupplierForm").find('#contact_person_email_id').focus();
                        }, 100);
                    });
                }
                else {
                    jQuery("#commonSupplierForm").find('#contact_person_email_id').removeClass('error');
                    //  var formdata = jQuery('#commonSupplierForm').serialize();

                    var data = new FormData(document.getElementById('commonSupplierForm'));

                    var formValue = Object.fromEntries(data.entries());

                    formValue = Object.assign(formValue, { 'agreement_details': JSON.stringify(agreement_data) });

                    var formdata = new URLSearchParams(formValue).toString();

                    jQuery.ajax({
                        url: RouteBasePath + "/verify-supplier-name/?supplier_name=" + supplierName + "&id=" + supplierHiddenId,
                        type: 'GET',
                        dataType: 'json',
                        processData: false,
                        success: function (data) {
                            if (data.response_code == 1) {
                                // jAlert(data.response_message);
                                toastElement(data.response_message, "#supplier_name");
                                // jQuery('#supplier_btn').attr('disabled', true);
                                jQuery('#commonSupplierForm').find('#supplier_btn').prop('disabled', true);


                            } else if (supplierCode != "" && supplierCode != undefined) {
                                jQuery.ajax({
                                    url: RouteBasePath + "/verify-supplier_code?supplier_code=" + supplierCode + "&id=" + supplierHiddenId,
                                    type: 'GET',
                                    dataType: 'json',
                                    processData: false,
                                    success: function (data) {
                                        if (data.response_code == 1) {
                                            toastElement(data.response_message, "#supplier_code");
                                            //jQuery('#supplier-btn').attr('disabled', true); 
                                            jQuery('#commonSupplierForm').find('#supplier_btn').prop('disabled', true);


                                        } else {
                                            //jQuery('#supplier_btn').attr('disabled', false);
                                            jQuery('#commonSupplierForm').find('#supplier_btn').prop('disabled', false);


                                            jQuery.ajax({

                                                url: formUrl,

                                                type: 'POST',

                                                data: formdata,

                                                headers: headerOpt,

                                                dataType: 'json',

                                                processData: false,

                                                success: function (data) {

                                                    if (data.response_code == 1) {


                                                        if (supplierHiddenId != undefined && supplierHiddenId != "") {

                                                            jAlert(data.response_message, 'Success', function (r) {
                                                                window.location.href = RouteBasePath + "/manage-supplier";
                                                            });
                                                            //addedVillage(true);
                                                        }
                                                        else if (supplierHiddenId == undefined || supplierHiddenId == "") {

                                                            function nextFn() {

                                                                document.getElementById("commonSupplierForm").reset();

                                                                jQuery('#commonSupplierForm').find('#supplier_country_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonSupplierForm').find('#supplier_state_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonSupplierForm').find('#supplier_district_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonSupplierForm').find('#supplier_taluka_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonSupplierForm').find('#supplier_village_id').val('').trigger('liszt:updated');
                                                                jQuery('#commonSupplierForm').find('#pan');
                                                                jQuery('#commonSupplierForm').find('#gstin');
                                                                // jQuery('#commonSupplierForm').find('#gstin').prop('disabled', true);

                                                                jQuery('span.checked').removeClass('checked');

                                                                validator.resetForm();
                                                                // window.location.href = RouteBasePath + "/add-supplier";
                                                                getSupplireCode();
                                                                setTimeout(() => {
                                                                    jQuery('input#supplier_name').focus();
                                                                }, 200);
                                                                jQuery('#agreementTable tbody').empty();
                                                            }
                                                            toastSuccess(data.response_message, nextFn);
                                                            addedVillage(true);
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

                                                    }
                                                }
                                            });
                                        }
                                    }
                                })
                            }
                        }
                    });
                }
            }

        }
    });
    if (supplierHiddenId == undefined) {
        getSupplireCode();
    }


});
function getSupplireCode() {

    jQuery.ajax({
        url: RouteBasePath + "/get-supplier_code",
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jQuery("#supplier_code").val(data.supplier_code);
            } else {

            }
        },
    });

}

// get State function start
function getSupplierState(event) {
    // let thisForm = jQuery('#addSupplierForm');

    let countryId = jQuery('#supplier_country_id option:selected').val();
    if (countryId != "" && countryId !== undefined) {

        jQuery('#cityModal #country_name').val(jQuery('#supplier_country_id option:selected').text());

        jQuery("#country_id").val(countryId).trigger('liszt:updated');

        // if (countryId == 1) {
        //     jQuery('#gst_code').prop('disabled', false);
        // } else {
        //     jQuery('#gst_code').prop('disabled', true);
        // }

        return jQuery.ajax({

            // url: "{{ route('get-states') }}?country_id="+thisVal,
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
                    jQuery('#supplier_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    jQuery('#state_id').empty().append(dropHtml).trigger('liszt:updated');
                    jQuery('#state_name').empty().append(dropHtml).trigger('liszt:updated');
                    jQuery('#taluka_state_id').empty().append(dropHtml).trigger('liszt:updated');

                } else {
                    jQuery('#supplier_state_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}
// end getStates function


// get District function start
function getSupplierDistrict(event) {
    // let thisForm = jQuery('#addSupplierForm');
    let stateId = jQuery('#supplier_state_id option:selected').val();

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
                    jQuery('#supplier_district_id').empty().append(dropHtml).trigger('liszt:updated');
                    jQuery('#taluka_district_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#supplier_district_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}
// end getDistrict function

// get Taluka function start
function getSupplierTaluka(event) {
    let districtId = jQuery('#supplier_district_id option:selected').val();
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
                    jQuery('#supplier_taluka_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#supplier_taluka_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}
// end getTaluka function

// get Village function start
function getSupplierVillage(event) {
    //  let thisForm = jQuery('#addSupplierForm');
    let talukaId = jQuery('#supplier_taluka_id option:selected').val();
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
                    jQuery('#supplier_village_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#supplier_village_id').empty().append("<option value=''>Select Village</option>").trigger('liszt:updated');
                }
            },
        });
    }
}
// end getVillage function

// Customer  Duplication  Code






// Check Duplicate Supplier Name
function checkSupplierName(supplier_name) {
    var id = supplierHiddenId;
    jQuery.ajax({
        url: RouteBasePath + "/verify-supplier-name/?supplier_name=" + supplier_name + "&id=" + id,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                toastElement(data.response_message, "#supplier_name");
                jQuery('#supplier_btn').attr('disabled', true);

            } else {
                jQuery('#supplier_btn').attr('disabled', false);

            }
        }
    });
}

function verifySupplierName() {
    var supplier_name = jQuery('#supplier_name').val();
    var hidden = jQuery('#spname').val();
    var suggestion_list = jQuery('#supplier_name_list').html;

    if (supplier_name != '') {
        checkSupplierName(supplier_name);
    }
}

jQuery(document).on('click', '#supplier_name_list', function (e) {
    // jQuery('#supplier_name').val('');
    var suggest = e.target.innerHTML;
    jQuery('#spname').val(suggest);
    var hidden = jQuery('#spname').val();
    var suggestion_list = jQuery('#supplier_name_list').html;

    var supplier_name = hidden;
    if (suggestion_list != '') {
        checkSupplierName(supplier_name);
    }
});


// suggestionList

function suggestSupplierName(e, $this) {
    var keyevent = e
    if (keyevent.key != "Tab") {
        jQuery("#supplier_name").addClass('file-loader');
        var search = jQuery($this).val();
        jQuery.ajax({
            url: RouteBasePath + "/get-supplier-name?term=" + search,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery("#supplier_name").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#supplier_name_list').html(data.supplier_name);
                } else {
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery("#supplier_name").removeClass('file-loader');
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
function suggestSupplierCode(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#supplier_code").addClass('file-loader');

        var search = jQuery($this).val();

        jQuery.ajax({

            url: RouteBasePath + "/supplier-code_list?term=" + encodeURI(search),

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#supplier_code").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#supplier_code_list').html(data.codeList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#supplier_code").removeClass('file-loader');

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


function checkSupplierCode(supplier_code) {
    jQuery.ajax({
        url: RouteBasePath + "/verify-supplier_code?supplier_code=" + supplier_code + "&id=" + supplierHiddenId,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                toastElement(data.response_message, "#supplier_code");
                jQuery('#supplier_btn').attr('disabled', true);
            } else {
                jQuery('#supplier_btn').attr('disabled', false);
            }
        }
    });
}

function verifySupplierCode() {
    var supplier_code = jQuery('#supplier_code').val();
    var hidden = jQuery('#spcode').val();
    var suggestion_list = jQuery('#supplier_code_list').html;

    if (suggestion_list != '') {
        checkSupplierCode(supplier_code);
    }
}

jQuery(document).on('click', '#supplier_code_list', function (e) {
    var suggest = e.target.innerHTML;
    jQuery('#spcode').val(suggest);
    var hidden = jQuery('#spcode').val();
    var suggestion_list = jQuery('#supplier_code_list').html;

    var supplier_code = hidden;
    if (suggestion_list != '') {
        checkSupplierCode(supplier_code);
    }
});
// gst code enable disable code

// function cname()
// {
//     var cname = jQuery('#customer_country_id option:selected').val();
//     if (cname == 1) {
//         jQuery('#commonSupplierForm').find('#pan').prop('disabled', false);       
//     } else {
//         jQuery('#commonSupplierForm').find('#pan').prop('disabled', true);        
//     }
// }


function changePincode() {

    let thisForm = jQuery('#addSupplierForm');

    let getVillageData = jQuery('#supplier_village_id option:selected').val();

    if (getVillageData != "" && getVillageData !== undefined) {
        jQuery.ajax({
            url: RouteBasePath + "/get-villageData?village_id=" + getVillageData,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    jQuery("#pincode").val(data.pincode);
                } else {
                    jAlert(data.response_message);
                }
            },
        });
    }
}

jQuery("#supplier_village_id").on("change", function () {
    let page = jQuery("#IsAllState").data('page');
    let getVillageData = jQuery('#supplier_village_id option:selected').val();
    if (getVillageData != null && getVillageData != "" && page == "add")
        changePincode();
    // else     
    //     jQuery("#pincode").val('');               
});

jQuery('#stateModal').on('show.bs.modal', function (e) {
    let country = jQuery("#supplier_country_id").val();

    jQuery("#country_id").val(country).trigger('liszt:updated');


    if (jQuery('#country_id option:selected').val() == "1") {

        jQuery('#gst_code').prop("disabled", false);

    } else {

        jQuery('#gst_code').prop("disabled", true);

        jQuery('#gst_code').val('');

    }

});


jQuery('#cityModal').on('show.bs.modal', function (e) {
    let state = jQuery("#supplier_state_id").val();
    let country = jQuery("#supplier_country_id").val();

    jQuery("#state_id").val(state).trigger('liszt:updated');

    if (country != '') {
        jQuery('#commonDistrictForm #country_name').val(jQuery('#supplier_country_id option:selected').text());
    }

});


jQuery('#talukaModal').on('show.bs.modal', function (e) {
    let dist = jQuery("#supplier_district_id").val();
    let state = jQuery("#supplier_state_id").val();
    let country = jQuery("#supplier_country_id").val();



    jQuery("#taluka_state_id").val(state).trigger('liszt:updated');

    if (state != '' && state != null) {
        getDistrict().done(function (resposne) {
            jQuery("#taluka_district_id").val(dist).trigger('liszt:updated');
        });
    }

    if (country != '') {
        jQuery('#commonTalukaForm #country_name').val(jQuery('#supplier_country_id option:selected').text());
    }
});


jQuery('#VillageModal').on('show.bs.modal', function (e) {

    let dist = jQuery("#supplier_district_id").val();
    let state = jQuery("#supplier_state_id").val();
    let taluka = jQuery("#supplier_taluka_id").val();
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
        jQuery('#commonVillageForm #country_name').val(jQuery('#supplier_country_id option:selected').text());
    }
});

// jQuery(document).on('keydown', '#agreement_start_date, #agreement_end_date', function(e) {
//     e.preventDefault();
// });


var agreement_data = [];


// contact modal validator 
var contactValidator = jQuery("#agreement_form").validate({

    onclick: false,
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
            required: "Please enter the agreement start date."
        },
        agreement_end_date: {
            required: "Please enter the agreement end date."

        },
        agreement_document_doc: {
            required: "Please upload the agreement document.",
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
        var thisModal = jQuery('#agreementModal');

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

        /*var lastEndDate = null; // Variable to store the last agreement end date


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
            thisModal.find('#agreement_end_date').closest('div.control-group').removeClass('error');


            var agreement_start_date = formValue.agreement_start_date ? formValue.agreement_start_date : "";
            var agreement_end_date = formValue.agreement_end_date ? formValue.agreement_end_date : "";
            var agreement_document_doc = formValue.agreement_document_doc ? formValue.agreement_document_doc : "";

            var isImageSoftDelete = thisModal.find('#agreement_document_soft_delete').val();

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
            thisModal.modal('hide');
        }

        if (supplierHiddenId == undefined) {
            getSupplireCode();
        }
    }

});

function removeAgreementDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            let formIndx = jQuery(th).closest("tr").find('input[name="form_indx"]').val();
            removeFormObj(formIndx);
            jQuery(th).closest("tr").remove();
        }
    });
}

function editAgreementDetails(th) {
    let formIndx = jQuery(th).closest("tr").find('input[name="form_indx"]').val();
    let rawIndx = jQuery(th).closest('tr').index();
    fillAgreementForm(formIndx, rawIndx);
}


jQuery('#agreementModal').on('show.bs.modal', function (e) {
    let thisForm = jQuery('#agreementModal');
    let formType = thisForm.find("#form_type").val(); // "add" or "edit"
    let lastEndDate = null;

    // Find the latest end date in the table
    jQuery("#agreementTable tbody tr").each(function () {
        let currentEndDate = jQuery(this).find('td:eq(2)').text().trim(); // Get end date column
        // console.log(currentEndDate)
        let parsedDate = moment(currentEndDate, 'DD/MM/YYYY', true);
        if (parsedDate.isValid() && (!lastEndDate || parsedDate.isAfter(lastEndDate))) {
            lastEndDate = parsedDate;
        }
    });
    // console.log(lastEndDate)
    if (formType === "add") {
        let newStartDate = lastEndDate ? lastEndDate.add(1, 'days').format('DD/MM/YYYY') : moment().format("DD/MM/YYYY");

        // Set start date field
        thisForm.find("#agreement_start_date").val(newStartDate);

        if (agreement_data.length > 0) {

            // Initialize start date picker
            jQuery("#agreement_start_date").datepicker('destroy').datepicker({
                dateFormat: "dd/mm/yy",
                minDate: newStartDate // Ensure start date is at least the next day
            });

            // Initialize end date picker
            jQuery("#agreement_end_date").datepicker('destroy').datepicker({
                dateFormat: "dd/mm/yy",
                minDate: newStartDate // Ensure end date is at least start date
            });

            // jQuery('#agree_strat_min_date').val(lastEndDate);
            jQuery('#agree_strat_min_date').val(lastEndDate.subtract(1, 'days').format('DD/MM/YYYY')
            );
            jQuery('#agree_end_min_date').val(newStartDate);
        } else {
            jQuery('#agree_strat_min_date').val('');
            jQuery('#agree_strat_max_date').val('');
            jQuery('#agree_end_max_date').val('');
            jQuery('#agree_end_min_date').val('');
        }

        thisForm.find('flabel').text("Add");
        thisForm.find('slabel').text("Add");


        setTimeout(() => {

            thisForm.find('#document_doc').val('');

            thisForm.find('#agreement_document_doc').val('');

            thisForm.find('#agreement_document_prev').attr('href', '#');

            thisForm.find('#agreement_document_prev').addClass('hidden');

            thisForm.find('#agreement_document_remove').removeClass('i-block').addClass('hidden');

            thisForm.find('#agreement_document_img-prev').html('');

            thisForm.find('#agreement_document_img-prev-box').addClass('hidden');

            thisForm.find('#agreement_document_img-prev-box').html('');

        }, 300)

    } else { // Editing mode
        // thisForm.find("#agreement_start_date").datepicker('destroy').datepicker({
        //     dateFormat: "dd/mm/yy",
        //     minDate: null // Allow selecting any date for edits
        // });

        // thisForm.find("#agreement_end_date").datepicker('destroy').datepicker({
        //     dateFormat: "dd/mm/yy",
        //     minDate: null // Allow selecting any date for edits
        // });

        thisForm.find('flabel').text("Edit");
        thisForm.find('slabel').text("Update");
    }

    // Ensure the end date is never before the start date
    jQuery("#agreement_start_date").on("change", function () {
        let selectedStartDate = jQuery(this).val();
        jQuery("#agreement_end_date").datepicker("option", "minDate", selectedStartDate);
    });
});




//<--On modal hide-->//

jQuery('#agreementModal').on('hide.bs.modal', function (e) {
    let thisForm = jQuery('#agreementModal');
    thisForm.find("#form_type").val("add");
    thisForm.find("#form_index").val("");
    thisForm.find("#row_index").val("");
    jQuery('#agreement_form').trigger("reset");
});

function removeFormObj(formIndx) {
    delete agreement_data[formIndx];
    agreement_data = agreement_data.filter(element => element != null);
}


function fillAgreementForm(formIndx, rawIndx) {
    let thisForm = jQuery('#agreementModal');
    thisForm.find("#form_type").val("edit");
    thisForm.find("#form_index").val(formIndx);
    thisForm.find("#row_index").val(rawIndx);
    var frmData = agreement_data[formIndx];
    thisForm.find("#agreement_start_date").val(frmData.agreement_start_date);
    thisForm.find("#agreement_end_date").val(frmData.agreement_end_date);
    thisForm.find("#agreement_document_doc").val(frmData.agreement_document_doc);
    thisForm.find("#cheque_no").val(frmData.cheque_no);

    if (frmData.agreement_document_doc != "" && frmData.agreement_document_doc !== undefined) {

        thisForm.find("#agreement_document_doc").val(frmData.agreement_document_doc);

        thisForm.find('#agreement_document_prev').attr('href', uploadURL + frmData.agreement_document_doc);

        thisForm.find('#agreement_document_prev').removeClass('hidden');

        thisForm.find('#agreement_document_img-prev-box').removeClass('hidden');

        thisForm.find('#agreement_document_remove').addClass('i-block').removeClass('hidden');

    } else {

        thisForm.find('#agreement_document_doc').val('');

        thisForm.find('#agreement_document_prev').attr('href', '#');

        thisForm.find('#agreement_document_prev').addClass('hidden');

        thisForm.find('#agreement_document_remove').removeClass('i-block').addClass('hidden');

        thisForm.find('#agreement_document_img-prev').html('');

        thisForm.find('#agreement_document_img-prev-box').addClass('hidden');

        thisForm.find('#agreement_document_img-prev-box').html('');

    }

    thisForm.modal('show');
    // setTimeout(() => {

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
        thisForm.find("#agree_strat_min_date").val('');
        thisForm.find("#agree_strat_max_date").val(maxstartDate);
    } else {
        jQuery("#agreement_start_date").datepicker('destroy').datepicker({
            dateFormat: "dd/mm/yy",
            minDate: minstartDate,
            maxDate: maxstartDate // Ensure start date is at least the next day
        });
        thisForm.find("#agree_strat_min_date").val(minstartDate);
        thisForm.find("#agree_strat_max_date").val(maxstartDate);
    }


    jQuery("#agreement_end_date").datepicker('destroy').datepicker({
        dateFormat: "dd/mm/yy",
        minDate: frmData.agreement_start_date,
        maxDate: maxstartDate // Ensure start date is at least the next day
    });
    thisForm.find("#agree_end_min_date").val(frmData.agreement_start_date);
    thisForm.find("#agree_end_max_date").val(maxstartDate);

    // }, 500);


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

// Agreement details code 

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


