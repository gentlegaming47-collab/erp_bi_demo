

let locationHiddenId = jQuery('#commonLocationForm').find('input:hidden[name="id"]').val();




jQuery(document).ready(function () {
    setTimeout(() => {
        jQuery('#cke_notifications_area_editor').css("display", 'none');
    }, 250);
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };



    jQuery(document).ready(function () {

        var initialLocationType = jQuery('#location_type').val();
        if (initialLocationType === 'HO') {
            jQuery("#mfg_process").css("display", 'flex');
            jQuery("#mfg_process").val('Yes');
        } else {
            jQuery("#mfg_process").css("display", 'none');
            jQuery("#mfg_process").val('');
        }

        // Bind change event handler
        jQuery('#location_type').on('change', function () {
            jQuery("#mfg_process").css("display", this.value === 'HO' ? 'flex' : 'none');
            jQuery("#mfg_process").val(this.value === 'godown' ? '' : 'Yes');
        });
    });






    let status = jQuery('#commonLocationForm').find('#status').val();
    status == 'active' ? jQuery("#hide-status").hide() : jQuery("#hide-status").show();

    jQuery("#customer_id").on("change", function () {
        let chekUer = checkUserExists();

        if (chekUer != "" && chekUer != undefined) {
            jAlert(chekUer);
            return false;
        }
    });



    if (locationHiddenId != "" && locationHiddenId != undefined) {


        // get village data at edit time

        jQuery.ajax({

            url: RouteBasePath + "/get-location/" + locationHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {


                    jQuery('#location_name').val(data.location.location_name);
                    jQuery('#customer_id').val(data.location.customer_id).trigger('liszt:updated');
                    jQuery('#location_type').val(data.location.type).trigger('liszt:updated');
                    jQuery('#location_code').val(data.location.location_code);
                    jQuery('#location_country_id').val(data.location.country_id).trigger('liszt:updated');
                    getLocationStates();

                    if (data.in_use == true) {
                        jQuery("#location_code").val(data.location.location_code).prop({ tabindex: -1 }).attr('readonly', true);
                        // jQuery("#location_code").val(data.location.location_code).attr("readonly", true);
                    }


                    jQuery('#status').val(data.location.status).trigger('liszt:updated');
                    if (data.location.type == 'godown') {
                        jQuery("#mfg_process").hide();
                    } else {
                        jQuery("#mfg_process").css("display", "flex");
                    }
                    jQuery('#mfg_process').val(data.location.mfg_process).trigger('liszt:updated');
                    jQuery('input:hidden[name="id"]').val(data.location.id);
                    CKEDITOR.instances['editor'].setData(data.location.header_print);
                    getLocationStates().done(function (resposne) {

                        jQuery('#location_state_id').val(data.location.state_id).trigger('liszt:updated');
                        //    });
                        getLocationDistrict().done(function (resposne) {
                            jQuery('#location_district_id').val(data.location.district_id).trigger('liszt:updated');

                            getLocationTaluka().done(function (resposne) {
                                jQuery('#location_taluka_id').val(data.location.taluka_id).trigger('liszt:updated');

                                getLocationVillage().done(function (resposne) {
                                    jQuery('#location_village_id').val(data.location.village_id).trigger('liszt:updated');
                                });
                            });
                        });
                    });


                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-location";
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

    var validator = jQuery("#commonLocationForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            location_name: {
                required: true
            },
            location_type: {
                required: true
            },
            location_code: {
                required: true
            },
            location_country_id: {
                required: true
            },
            // customer_id : {
            //     required: true
            // },
            location_state_id: {
                required: true
            },
            location_district_id: {
                required: true
            },
            location_taluka_id: {
                required: true,
            },
            location_village_id: {
                required: true,
            },
        },

        messages: {

            location_name: {
                required: "Please Enter Location Name"
            },
            location_type: {
                required: "Please Select Location Type"
            },
            // customer_id: {
            //     required: "Please Select Customer"
            // },
            location_code: {
                required: "Please Enter Location Code"
            },
            location_country_id: {
                required: "Please Select Country"
            },
            location_state_id: {
                required: "Please Select State"
            },
            location_district_id: {
                required: "Please Select District"
            },
            location_taluka_id: {
                required: "Please Select Taluka"
            },
            location_village_id: {
                required: "Please Select Village"
            },

        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            let chekUer = checkUserExists();
            var formdata = jQuery('#commonLocationForm').serialize();
            var desc = CKEDITOR.instances.editor.getData();
            formdata += '&editor=' + encodeURIComponent(desc);
            jQuery('#location-btn').attr('disabled', true);


            let locationName = jQuery('#location_name').val();


            let formUrl = locationHiddenId != undefined && locationHiddenId != "" ? RouteBasePath + "/update-location" : RouteBasePath + "/store-location";


            if (locationName != '' && locationName != undefined) {

                jQuery.ajax({
                    url: RouteBasePath + "/verify-location?location_name=" + locationName + "&id=" + locationHiddenId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            // jAlert(data.response_message);
                            toastElement(data.response_message, "#location_name");
                            jQuery('#location-btn').attr('disabled', true);

                        }
                        else {
                            jQuery('#location-btn').attr('disabled', false);

                            jQuery.ajax({

                                url: formUrl,

                                type: 'POST',

                                data: formdata,

                                headers: headerOpt,

                                dataType: 'json',

                                processData: false,

                                success: function (data) {

                                    if (data.response_code == 1) {


                                        if (locationHiddenId != undefined && locationHiddenId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-location";
                                            });
                                            //addedVillage(true);
                                        }
                                        else if (locationHiddenId == undefined || locationHiddenId == "") {

                                            function nextFn() {

                                                document.getElementById("commonLocationForm").reset();


                                                jQuery('#commonLocationForm').find('#location_country_id').val('').trigger('liszt:updated');
                                                jQuery('#commonLocationForm').find('#location_state_id').val('').trigger('liszt:updated');
                                                jQuery('#commonLocationForm').find('#location_district_id').val('').trigger('liszt:updated');
                                                jQuery('#commonLocationForm').find('#location_taluka_id').val('').trigger('liszt:updated');
                                                jQuery('#commonLocationForm').find('#location_village_id').val('').trigger('liszt:updated');
                                                jQuery('#commonLocationForm').find('#status').val('').trigger('liszt:updated');
                                                jQuery('#commonLocationForm').find('#mfg_process').val('').trigger('liszt:updated');
                                                jQuery('#commonLocationForm').find('#location_type').val('').trigger('liszt:updated');
                                                jQuery('#commonLocationForm').find('#description').val('');
                                                CKEDITOR.instances['editor'].setData('');
                                                jQuery('#commonLocationForm').find('#customer_id').val('').trigger('liszt:updated');

                                                validator.resetForm();
                                                jQuery('input#location_name').focus();

                                                // jQuery('#country_name').val('');

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
                // }

            }

        }

    });

});



// Item  Duplication  Code

function CheckLocation(location_name) {
    location_name = jQuery('#location_name').val();

    let id = jQuery('#location_id').val();

    jQuery.ajax({
        url: RouteBasePath + "/verify-location?location_name=" + location_name + "&id=" + id,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                // jAlert(data.response_message);
                toastElement(data.response_message, "#location_name");
                jQuery('#location-btn').attr('disabled', true);
            }
            else {
                jQuery('#location-btn').attr('disabled', false);

            }
        }
    });
}

jQuery(document).on('click', '#location_name_list', function (e) {
    var suggest = e.target.innerHTML;
    var data = suggest;
    var location_name = jQuery('#location_name').val();

    if (location_name != '') {
        CheckLocation(data, location_name);

    }
});




// suggestionList
function suggestLocationName(e, $this) {
    var keyevent = e
    if (keyevent.key != "Tab") {
        jQuery("#location_name").addClass('file-loader');
        var search = jQuery($this).val();

        jQuery.ajax({
            url: RouteBasePath + "/get-location-name?term=" + search,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery("#location_name").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#location_name_list').html(data.location_name);
                } else {
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery("#location_name").removeClass('file-loader');
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


// location_code code here 


// function suggestLocationCode(e, $this) {
//     var keyevent = e    
//     if (keyevent.key != "Tab") {
//         jQuery("#location_code").addClass('file-loader');
//         var search = jQuery($this).val();

//         jQuery.ajax({
//             url: RouteBasePath + "/get-location-code/?term=" + search,
//             type: 'GET',
//             dataType: 'json',
//             processData: false,
//             success: function(data) {
//                 jQuery("#location_code").removeClass('file-loader');
//                 if (data.response_code == 1) {
//                     jQuery('#location_code_list').html(data.location_code);
//                 } else {
//                     toastError(data.response_message);
//                 }
//             },
//             error: function(jqXHR, textStatus, errorThrown) {
//                 jQuery("#location_code").removeClass('file-loader');
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




function checkUserExists() {
    let getCustomerData = jQuery('#customer_id option:selected').val();
    let getId = jQuery('#location_id').val();
    var response = '';
    if (getCustomerData != "" && getCustomerData !== undefined) {
        jQuery.ajax({
            url: RouteBasePath + "/checkUserExists/?customer_id=" + getCustomerData + "&id=" + getId,
            type: 'GET',
            dataType: 'json',
            processData: false,
            async: false,
            success: function (data) {
                if (data.response_code == 1) {
                    response = data.response_message;

                }
                else {
                    return "";
                }
            },
        });
        return response;
    }
}


function getLocationStates(event) {
    let stateIdVal = jQuery('#location_country_id option:selected').val();
    if (stateIdVal != "" && stateIdVal !== undefined) {
        return jQuery.ajax({
            url: RouteBasePath + "/get-location-states?country_id=" + stateIdVal,
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
                    jQuery('#location_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    jQuery('#taluka_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    jQuery('#state_id').empty().append(dropHtml).trigger('liszt:updated');


                } else {
                    jQuery('#location_state_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}


function getLocationDistrict(event) {
    let districtVal = jQuery('#location_state_id option:selected').val();

    jQuery("#state_id").val(districtVal).trigger('liszt:updated');

    if (districtVal != "" && districtVal !== undefined) {
        return jQuery.ajax({

            url: RouteBasePath + "/get-district?state_id=" + districtVal,
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
                    jQuery('#location_district_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#location_district_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getLocationTaluka(event) {
    let talukaVal = jQuery('#location_district_id option:selected').val();
    if (talukaVal != "" && talukaVal !== undefined) {
        return jQuery.ajax({

            url: RouteBasePath + "/get-taluka?district_id=" + talukaVal,
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
                    jQuery('#location_taluka_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#location_taluka_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}


function getLocationVillage(event) {
    let villageIdVal = jQuery('#location_taluka_id option:selected').val();
    if (villageIdVal != "" && villageIdVal !== undefined) {
        return jQuery.ajax({


            url: RouteBasePath + "/get-village?taluka_id=" + villageIdVal,
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
                    jQuery('#location_village_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#location_village_id').empty().append("<option value=''>Select Village</option>").trigger('liszt:updated');
                }
            },
        });
    }
}




// Modal code value are not reset after submission


jQuery('#stateModal').on('show.bs.modal', function (e) {
    let country = jQuery("#location_country_id").val();

    setTimeout(() => {
        jQuery("#country_id").val(country).trigger('liszt:updated');
    }, 200);



    if (jQuery('#location_country_id option:selected').val() == "1") {

        jQuery('#gst_code').prop("disabled", false);

    } else {

        jQuery('#gst_code').prop("disabled", true);

        jQuery('#gst_code').val('');

    }

});

jQuery('#cityModal').on('show.bs.modal', function (e) {
    let state = jQuery("#location_state_id").val();
    let country = jQuery("#location_country_id").val();


    jQuery("#state_id").val(state).trigger('liszt:updated');

    if (country != '') {
        jQuery('#commonDistrictForm #country_name').val(jQuery('#location_country_id option:selected').text());
    }

});

jQuery('#talukaModal').on('show.bs.modal', function (e) {
    let dist = jQuery("#location_district_id").val();
    let state = jQuery("#location_state_id").val();

    let country = jQuery("#location_country_id").val();

    jQuery("#taluka_state_id").val(state).trigger('liszt:updated');

    if (state != '' && state != null) {
        getDistrict().done(function (resposne) {
            jQuery("#taluka_district_id").val(dist).trigger('liszt:updated');
        });
    }

    if (country != '') {
        jQuery('#commonTalukaForm #country_name').val(jQuery('#location_country_id option:selected').text());
    }
});

jQuery('#VillageModal').on('show.bs.modal', function (e) {

    let dist = jQuery("#location_district_id").val();
    let state = jQuery("#location_state_id").val();
    let taluka = jQuery("#location_taluka_id").val();
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
        jQuery('#commonVillageForm #country_name').val(jQuery('#location_country_id option:selected').text());
    }
});