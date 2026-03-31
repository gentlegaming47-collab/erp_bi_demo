

let talukaHiddenId = jQuery('#commonTalukaForm').find('input:hidden[name="id"]').val();


jQuery(document).ready(function () {

    jQuery("#commonTalukaForm").find('#country_name').prop({ tabindex: -1, readonly: true });

    if (talukaHiddenId != "" && talukaHiddenId != undefined) {

        var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

        // get taluka data at edit time

        jQuery.ajax({

            url: RouteBasePath + "/get-talukas/" + talukaHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {


                    jQuery('#taluka_name').val(data.taluka.taluka_name);

                    jQuery('#taluka_state_id').val(data.taluka.state_id).trigger('liszt:updated');

                    // city drop down updated
                    getDistrict().done(function (response) {
                        jQuery('#taluka_district_id').val(data.taluka.district_id).trigger('liszt:updated');
                    })




                    jQuery('input:hidden[name="id"]').val(data.taluka.id);

                    getTalukaRelationData();

                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "{{ route('manage-taluka')}}";
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


    // Store or update Code

    var validator = jQuery("#commonTalukaForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            taluka_name: {
                required: true
            },

            taluka_state_id: {
                required: true
            },

            taluka_district_id: {
                required: true
            },

        },

        messages: {

            taluka_name: {
                required: "Please Enter Taluka"
            },

            taluka_district_id: {
                required: "Please Select District"
            },

            taluka_state_id: {
                required: "Please Select State"
            },


        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            var formdata = jQuery('#commonTalukaForm').serialize();

            let talukaName = jQuery('#taluka_name').val();
            let districtName = jQuery("#taluka_district_id").val();

            let formUrl = talukaHiddenId != undefined && talukaHiddenId != "" ? RouteBasePath + "/update-taluka" : RouteBasePath + "/store-taluka";

            if ((districtName != '' && districtName != undefined)) {
                jQuery.ajax({
                    url: RouteBasePath + "/verify-taluka?taluka_district_id=" + districtName + "&taluka_name=" + talukaName + "&id=" + talukaHiddenId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            // jAlert(data.response_message);
                            toastElement(data.response_message, "#taluka_name");

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


                                        if (talukaHiddenId != undefined && talukaHiddenId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-taluka";
                                            });
                                            addedTaluka(true);
                                        }
                                        else if (talukaHiddenId == undefined || talukaHiddenId == "") {

                                            function nextFn() {

                                                document.getElementById("commonTalukaForm").reset();


                                                jQuery('#commonTalukaForm').find('#taluka_state_id').val('').trigger('liszt:updated');

                                                jQuery('#commonTalukaForm').find('#taluka_district_id').val('').trigger('liszt:updated');


                                                // validator.resetForm();
                                                // window.location.reload();

                                                // jQuery('#country_name').val('');
                                                jQuery('#talukaModal').modal('hide');
                                                jQuery('#taluka_name').focus();
                                            }

                                            toastSuccess(data.response_message, nextFn);
                                            addedTaluka(true);
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



// Taluka  Duplication  Code

function CheckTaluka(taluka_name, taluka_district_id) {
    var taluka_name = jQuery('#taluka_name').val();
    var taluka_district_id = jQuery('#taluka_district_id').val();

    jQuery.ajax({
        url: RouteBasePath + "/verify-taluka?taluka_district_id=" + taluka_district_id + "&taluka_name=" + taluka_name + "&id=" + talukaHiddenId,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jAlert(data.response_message);
                toastElement(data.response_message, "#taluka_name");
            }
            else {
                // jAlert("Alert");
            }
        }
    });
}

jQuery(document).on('click', '#taluka_list', function (e) {
    var suggest = e.target.innerHTML;
    var data = suggest;
    var taluka_district_id = jQuery('#taluka_district_id').val();
    var taluka_name = jQuery('#taluka_name').val();

    // if district is not empty then call the function
    if (taluka_district_id != '') {
        CheckTaluka(data, taluka_name, taluka_district_id);

    }
});





// fetch data in Drop down

// function getCities($this = null) {

//     if ($this != null) {

//         jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

//     }


//     jQuery.ajax({

//         url: RouteBasePath + "/get-taluka",
//         type: 'GET',
//         dataType: 'json',
//         processData: false,
//         success: function (data) {

//             if ($this != null) {

//                 jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

//             }


//             if (data.response_code == 1) {

//                 if ($this != null) {

//                     var stgDrpHtml = `<option value="">Select Taluka</option>`;

//                     for (let indx in data.taluka) {

//                         stgDrpHtml += `<option value="${data.taluka[indx].id}">${data.taluka[indx].taluka_name}</option>`;

//                     }

//                     jQuery($this).each(function (e) {

//                         let Id = jQuery(this).attr('id');
//                         let Selected = jQuery(this).find("option:selected").val();
//                         jQuery(this).empty().append(stgDrpHtml);
//                         jQuery(this).val(Selected).trigger('liszt:updated');

//                     });
//                     // taluka district
//                     getDistrict();
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

function addedTaluka($event) {
    if ($event == true) {
        getTaluka(".mst-suggest_taluka");
    }
}



// suggestionList

function suggestTaluka(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#taluka_name").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/taluka-list/?term=" + encodeURI(search),



            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {



                if (data.response_code == 1) {

                    jQuery("#taluka_name").removeClass('file-loader');
                    jQuery('#taluka_list').html(data.talukaList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                //         jQuery("#taluka_name").removeClass('file-loader');

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




function getTalukaRelationData() {

    let stateId_val = jQuery('#taluka_state_id option:selected').val();


    if (stateId_val != "" && stateId_val !== undefined) {

        jQuery.ajax({
            url: RouteBasePath + "/city-relation-field?state_id=" + stateId_val,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery("#country_name").removeClass('file-loader');
                if (data.response_code == 1) {

                    jQuery("#commonTalukaForm").find('#country_name').val(data.relation_data.country_name);
                } else {
                    toastError(data.response_message);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery("#country_name").removeClass('file-loader');
                var errMessage = JSON.parse(jqXHR.responseText);

                if (errMessage.errors) {
                    cityValidator.showErrors(errMessage.errors);

                } else if (jqXHR.status == 401) {

                    toastError(jqXHR.statusText);
                } else {
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
    else {
        jQuery("#commonTalukaForm").find('#country_name').val('');
    }
}


// get the district data

function getDistrict() {

    let thisVal = jQuery('#taluka_state_id option:selected').val();


    if (thisVal != "" && thisVal !== undefined) {

        return jQuery.ajax({

            url: RouteBasePath + "/taluka-relation-field?state_id=" + thisVal,

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    let dropHtml = `<option value=''>Select District</option>`;


                    if (!jQuery.isEmptyObject(data.relation_district_data) && data.relation_district_data.length > 0) {

                        for (let idx in data.relation_district_data) {
                            dropHtml += `<option value="${data.relation_district_data[idx].id}">${data.relation_district_data[idx].district_name}</option>`;
                        }
                    }

                    jQuery('#taluka_district_id').empty().append(dropHtml).trigger('liszt:updated');

                } else {
                    jQuery('#taluka_district_id').empty().append("<option value=''>Select District</option>").trigger('liszt:updated');
                }
            },

        });

    }
    else {
        dropHtml = `<option value=''>Select District</option>`;
        jQuery('#taluka_district_id').empty().append(dropHtml).trigger('liszt:updated');
    }
}
jQuery('#cityModal').on('show.bs.modal', function (e) {
    let state = jQuery("#taluka_state_id").val();
    let country = jQuery('#commonTalukaForm #country_name').val();

    jQuery("#state_id").val(state).trigger('liszt:updated');
    jQuery("#commonDistrictForm #country_name").val(country).prop('diabled', false);

});

jQuery('#talukaModal').on('show.bs.modal', function (e) {
    let dist = jQuery("#district_id").val();
    let state = jQuery("#village_state_id").val();
    let country = jQuery("#commonVillageForm #country_name").val();

    // console.log(jQuery("#commonTalukaForm #taluka_district_id").val(dist).trigger('liszt:updated'));
    jQuery("#taluka_state_id").val(state).trigger('liszt:updated');
    jQuery("#commonTalukaForm #country_name").val(country);
    jQuery("#commonTalukaForm #taluka_district_id").val(dist).trigger('liszt:updated');

});