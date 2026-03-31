

var stateHiddenId = jQuery('#commonStateForm').find('input:hidden[name="id"]').val();


jQuery(document).ready(function () {


    // gst code enable disable code 

    jQuery('#country_id').change(function () {

        if (jQuery('#country_id option:selected').val() == "1") {

            jQuery('#gst_code').prop("disabled", false);

        } else {

            jQuery('#gst_code').prop("disabled", true);

            jQuery('#gst_code').val('');

        }

    });



    if (stateHiddenId != "" && stateHiddenId != undefined) {

        var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };


        jQuery.ajax({

            url: RouteBasePath + "/get-state/" + stateHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    jQuery('#state_name').val(data.state.state_name);

                    jQuery('#country_id').val(data.state.country_id).trigger('liszt:updated');

                    if (data.state.country_id == 1) {

                        jQuery('#gst_code').prop("disabled", false);

                    } else {

                        jQuery('#gst_code').prop("disabled", true);

                    }

                    jQuery('#gst_code').val(data.state.gst_code);

                    jQuery('input:hidden[name="id"]').val(data.state.id);

                } else {

                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = RouteBasePath + "/manage-state";
                    });

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                var errMessage = JSON.parse(jqXHR.responseText);



                if (jqXHR.status == 401) {
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


    var validator = jQuery("#commonStateForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            state_name: {

                required: true,

                maxlength: 255

            },

            country_id: {

                required: true

            },

            gst_code: {

                required: function (e) {

                    if (!jQuery(e).is('[disabled]')) {

                        return true;

                    } else {

                        return false;

                    }

                },

                maxlength: 15

            }

        },

        messages: {

            state_name: {

                required: "Please Enter State",

                maxlength: "Maximum 255 Characters Allowed"

            },

            country_id: {

                required: "Please Select Country"

            },

            gst_code: {
                required: "Please Enter GST Code",

                maxlength: "Maximum 15 Characters Allowed",

            }


        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            var formdata = jQuery('#commonStateForm').serialize();

            let stateName = jQuery("#state_name").val();
            let countryName = jQuery("#country_id").val();
            let gstcode = jQuery("#gst_code").val();

            let formUrl = stateHiddenId != undefined && stateHiddenId != "" ? RouteBasePath + "/update-state" : RouteBasePath + "/store-state";

            jQuery('#state_btn').attr('disabled', true);

            let callURL;
            if (countryName != '' && stateName != "") {
                callURL = RouteBasePath + "/verify-state-data?state=" + stateName + "&country=" + countryName + "&gstcode=" + gstcode + "&id=" + stateHiddenId;
            }
            else if (gstcode != "" && gstcode != undefined) {
                callURL = RouteBasePath + "/verify-gst-data?gstcode=" + gstcode + "&country_name=" + countryName + "&state=" + stateName + "&id=" + stateHiddenId;
            }

            if ((stateName != '' && stateName != undefined) && (countryName != "" && countryName != undefined)) {

                jQuery.ajax({
                    url: callURL,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            // jAlert(data.response_message);
                            toastElement(data.response_message, "#state_name", "#gst_code");
                            jQuery('#state_btn').attr('disabled', true);

                        }
                        else {
                            jQuery('#state_btn').attr('disabled', false);

                            jQuery.ajax({

                                url: formUrl,

                                type: 'POST',

                                data: formdata,

                                headers: headerOpt,

                                dataType: 'json',

                                processData: false,

                                success: function (data) {

                                    if (data.response_code == 1) {


                                        if (stateHiddenId != undefined && stateHiddenId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-state";
                                            });
                                            addedStates(true);
                                        }
                                        else if (stateHiddenId == undefined || stateHiddenId == "") {
                                            toastSuccess(data.response_message, nextFn);

                                            function nextFn() {

                                                document.getElementById("commonStateForm").reset();


                                                jQuery('#commonStateForm').find('#country_id').val('').trigger('liszt:updated');

                                                // validator.resetForm();
                                                // window.location.reload();
                                                // jQuery('#country_name').val('');
                                                jQuery('#stateModal').modal('hide');
                                                jQuery('#state_name').focus();
                                            }

                                            jQuery("#gst_code").prop('disabled', true);
                                            addedStates(true);
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

    });

});






// State Duplication  Code

function CheckState(state, country, gstcode) {
    var state = jQuery('#state_name').val();
    var country = jQuery('#country_id').val();
    var gstcode = jQuery('#gst_code').val();
    var id = jQuery('#id').val();

    if (country != '' && state != "") {
        jQuery.ajax({
            url: RouteBasePath + "/verify-state-data?state=" + state + "&country=" + country + "&id=" + id,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    // jAlert(data.response_message);
                    toastElement(data.response_message, "#state_name");
                    jQuery('#state_btn').attr('disabled', true);


                }
                else {
                    jQuery('#state_btn').attr('disabled', false);

                }
            }
        });
    }
}

function CheckGst(gstcode) {
    var gstcode = jQuery('#gst_code').val();
    var id = jQuery('#id').val();
    var countryId = jQuery("#country_id").val();

    jQuery.ajax({
        url: RouteBasePath + "/verify-gst-data?gstcode=" + gstcode + "&countryId=" + countryId + "&id=" + id,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                // jAlert(data.response_message);
                toastElement(data.response_message, "#gst_code");
                jQuery('#state_btn').attr('disabled', true);

            }
            else {
                jQuery('#state_btn').attr('disabled', false);

            }
        }
    });

}


jQuery(document).on('click', '#state_name_list', function (e) {
    var suggest = e.target.innerHTML;
    var state = suggest;
    var country = jQuery('#country_id').val();
    var gstcode = jQuery('#gst_code').val();

    if (state != '') {
        CheckState(state, country, gstcode);

    }
});


// suggesion 



// function getStates($this = null) {



//     if ($this != null) {

//         jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

//     }




//     jQuery.ajax({

//         url: RouteBasePath + "/get-states",
//         type: 'GET',
//         dataType: 'json',
//         processData: false,
//         success: function (data) {

//             if ($this != null) {

//                 jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

//             }


//             if (data.response_code == 1) {

//                 if ($this != null) {

//                     var stgDrpHtml = `<option value="">Select State</option>`;

//                     for (let indx in data.states) {

//                         stgDrpHtml += `<option value="${data.states[indx].id}">${data.states[indx].state_name}</option>`;

//                     }

//                     jQuery($this).each(function (e) {

//                         let Id = jQuery(this).attr('id');
//                         let Selected = jQuery(this).find("option:selected").val();
//                         console.log("state", Selected);
//                         jQuery(this).empty().append(stgDrpHtml);
//                         jQuery(this).val(Selected).trigger('liszt:updated');

//                     });

//                 }

//             } else {
//                 toastError(data.response_message);
//             }
//         },
//         error: function (jqXHR, textStatus, errorThrown) {
//             if ($this != null) {

//                 jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

//             }

//             var errMessage = JSON.parse(jqXHR.responseText);

//             if (jqXHR.status == 401) {
//                 toastError(jqXHR.statusText);
//             } else {
//                 toastError('Something went wrong!');
//                 console.log(JSON.parse(jqXHR.responseText));
//             }
//         }
//     });
// }



// function getStates($this = null) {

//     if ($this != null) {

//         jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

//         let thisForm = jQuery('#customerform');
//     }

//     let IsAllState = jQuery('#IsAllState').val();
//     let ViewPageVal = jQuery("#hidViewPage").val();

//     console.log("IsAllState", IsAllState);

//     let urlData = '';
//     let thisVal = '';

//     //location 
//     if (ViewPageVal == "Location") {
//         console.log('in location page');
//         thisVal = jQuery('#location_country_id option:selected').val();

//         urlData = RouteBasePath + "/get-location-states/?country_id=" + thisVal;

//     }
//     // supplire
//     else if (ViewPageVal == "Supplier") {


//         thisVal = jQuery('#supplier_country_id option:selected').val();


//         urlData = RouteBasePath + "/get-location-states/?country_id=" + thisVal;
//     } else if (ViewPageVal == "Customer") {
//         thisVal = jQuery('#customer_country_id option:selected').val();
//         urlData = RouteBasePath + "/get-location-states/?country_id=" + thisVal;
//     }



//     if (IsAllState == 'Y') {
//         urlData = RouteBasePath + "/get-states";
//     }
//     // console.log("TE", urlData + "" + IsAllState);
//     jQuery.ajax({


//         url: urlData,

//         type: 'GET',

//         dataType: 'json',

//         processData: false,

//         success: function (data) {

//             if ($this != null) {

//                 jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');
//             }

//             if (data.response_code == 1) {

//                 if ($this != null) {

//                     var stgDrpHtml = `<option value="">Select State</option>`;

//                     for (let indx in data.states) {

//                         stgDrpHtml += `<option value="${data.states[indx].id}">${data.states[indx].state_name}</option>`;
//                     }

//                     if (ViewPageVal == 'Customer') {
//                         let Id = jQuery("#customer_state_id");
//                         console.log(`call state data2`);
//                         // console.log("customer_state_id", stgDrpHtml);
//                         let Selected = jQuery("#customer_state_id").find("option:selected").val();

//                         jQuery("#customer_state_id").empty().append(stgDrpHtml);

//                         jQuery("#customer_state_id").val(Selected).trigger('liszt:updated');
//                         jQuery('#state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
//                         jQuery('#taluka_state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
//                         jQuery('#village_state_id').empty().append(stgDrpHtml).trigger('liszt:updated')
//                     } else if (ViewPageVal == 'Location') {
//                         // console.log('location form');
//                         let Id = jQuery("#location_state_id");

//                         let Selected = jQuery("#location_state_id").find("option:selected").val();

//                         jQuery("#location_state_id").empty().append(stgDrpHtml);
//                         console.log(`data are ${stgDrpHtml}`);
//                         jQuery("#location_state_id").val(Selected).trigger('liszt:updated');
//                         jQuery('#state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
//                         jQuery('#taluka_state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
//                         jQuery('#village_state_id').empty().append(stgDrpHtml).trigger('liszt:updated')

//                     } else if (ViewPageVal == "Supplier") {
//                         console.log("supplier check");
//                         let Id = jQuery("#supplier_state_id");

//                         let Selected = jQuery("#supplier_state_id").find("option:selected").val();

//                         jQuery("#supplier_state_id").empty().append(stgDrpHtml);
//                         jQuery("#state_id").empty().append(stgDrpHtml);
//                         jQuery("#state_name").empty().append(stgDrpHtml);



//                         jQuery("#supplier_state_id").val(Selected).trigger('liszt:updated');
//                         jQuery('#state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
//                         jQuery('#taluka_state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
//                         jQuery('#village_state_id').empty().append(stgDrpHtml).trigger('liszt:updated')
//                     } else if (ViewPageVal == "EditDistrict") {
//                         // console.log('edit disat')

//                         let Id = jQuery("#state_id");

//                         let Selected = jQuery("#state_id").find("option:selected").val();

//                         jQuery("#state_id").empty().append(stgDrpHtml);


//                         jQuery("#state_id").val(Selected).trigger('liszt:updated');
//                     } else {
//                         jQuery($this).each(function (e) {
//                             let Id = jQuery(this).attr('id');
//                             console.log("Id", stgDrpHtml);
//                             let Selected = jQuery(this).find("option:selected").val();

//                             jQuery(this).empty().append(stgDrpHtml);

//                             jQuery(this).val(Selected).trigger('liszt:updated');

//                         });
//                     }
//                 }

//             } else {

//                 toastError(data.response_message);

//             }

//         },

//         error: function (jqXHR, textStatus, errorThrown) {

//             if ($this != null) {

//                 jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

//             }

//             var errMessage = JSON.parse(jqXHR.responseText);

//             if (jqXHR.status == 401) {

//                 toastError(jqXHR.statusText);

//             } else {

//                 toastError('Something went wrong!');

//                 console.log(JSON.parse(jqXHR.responseText));

//             }

//         }

//     });

// }


function addedStates($event) {

    if ($event == true) {
        getStates(".mst-suggest_state");
    }
}




function suggestState(e, $this) {
    var keyevent = e
    if (keyevent.key != "Tab") {
        jQuery("#state_name").addClass('file-loader');
        var search = jQuery($this).val();

        jQuery.ajax({
            url: RouteBasePath + "/state-list?term=" + encodeURI(search),
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {

                jQuery("#state_name").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#state_name_list').html(data.stateList);
                } else {
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery("#state_name").removeClass('file-loader');
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




//<--On modal hide-->//
jQuery('#stateModal').on('hide.bs.modal', function (e) {
    jQuery('#cityModal').find('.modal-footer').find('.btn').prop('disabled', false);
    document.getElementById("commonStateForm").reset();
    jQuery('#commonStateForm').find('#country_id').val('').trigger('liszt:updated');
});