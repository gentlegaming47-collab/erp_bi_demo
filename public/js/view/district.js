

districtHiddenId = jQuery('#commonDistrictForm').find('input:hidden[name="id"]').val();


jQuery(document).ready(function () {

    jQuery("#commonDistrictForm").find('#country_name').prop({ tabindex: -1, readonly: true });
    if (districtHiddenId != "" && districtHiddenId != undefined) {

        var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };


        jQuery.ajax({

            url: RouteBasePath + "/get-districts/" + districtHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {


                    //console.log(data.city.country_name);
                    jQuery('#district_name').val(data.city.district_name);

                    jQuery('#state_id').val(data.city.state_id).trigger('liszt:updated');

                    jQuery("#commonDistrictForm").find('#country_name').val(data.city.country_name);



                    jQuery('input:hidden[name="id"]').val(data.city.id);

                } else {

                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = RouteBasePath + "/manage-district";
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


    var validator = jQuery("#commonDistrictForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            district_name: {

                required: true,

                maxlength: 255

            },

            state_id: {

                required: true,

            },

            country_id: {

                required: true

            }

        },

        messages: {

            district_name: {

                required: "Please Enter District",

                maxlength: "Maximum 255 Characters Allowed"

            },

            state_id: {

                required: "Please Select State"

            }


        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            var formdata = jQuery('#commonDistrictForm').serialize();

            let stateName = jQuery("#state_id").val();
            let districtName = jQuery("#district_name").val();

            let formUrl = districtHiddenId != undefined && districtHiddenId != "" ? RouteBasePath + "/update-district" : RouteBasePath + "/store-district";

            if ((stateName != '' && stateName != undefined)) {

                jQuery.ajax({
                    url: RouteBasePath + "/verify-city-data?state=" + stateName + "&city=" + districtName + "&id=" + districtHiddenId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            jAlert(data.response_message);
                            toastElement(data.response_message, "#district_name");

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


                                        if (districtHiddenId != undefined && districtHiddenId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-district";
                                            });
                                            // addedDistrict(true);
                                        }
                                        else if (districtHiddenId == undefined || districtHiddenId == "") {

                                            function nextFn() {

                                                document.getElementById("commonDistrictForm").reset();


                                                jQuery('#commonDistrictForm').find('#state_id').val('').trigger('liszt:updated');

                                                // window.location.reload();
                                                //validator.resetForm();

                                                // jQuery('#country_name').val('');
                                                jQuery('#cityModal').modal('hide');
                                                jQuery('#district_name').focus();
                                            }

                                            toastSuccess(data.response_message, nextFn);
                                            addedCity(true);
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



// District  Duplication  Code


function CheckCity(city, state) {
    var state = jQuery('#state_id').val();
    var city = jQuery('#district_name').val();

    jQuery.ajax({
        url: RouteBasePath + "/verify-city-data?state=" + state + "&city=" + city + "&id=" + districtHiddenId,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                // jAlert(data.response_message);
                toastElement(data.response_message, "#district_name");

            }
            else {
                // jAlert("Alert");
            }
        }
    });
}

jQuery(document).on('click', '#city_name_list', function (e) {
    var suggest = e.target.innerHTML;
    var data = suggest;
    var state = jQuery('#statd_id').val();
    var city = jQuery('#city').val();

    // if state is not empty then call the function
    if (state != '') {
        CheckCity(data, city, state);

    }
});



// suggesion 


// function getCities($this = null) {



//     if ($this != null) {

//         jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

//     }


//     jQuery.ajax({

//         url: RouteBasePath + "/get-district",
//         type: 'GET',
//         dataType: 'json',
//         processData: false,
//         success: function(data) {

//             if ($this != null) {

//                 jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

//             }


//             if (data.response_code == 1) {

//                 if ($this != null) {

//                     var stgDrpHtml = `<option value="">Select District</option>`;

//                     for (let indx in data.cities) {

//                         stgDrpHtml += `<option value="${data.cities[indx].id}">${data.cities[indx].district_name}</option>`;

//                     }

//                     jQuery($this).each(function(e) {

//                         let Id = jQuery(this).attr('id');
//                         let Selected = jQuery(this).find("option:selected").val();
//                         jQuery(this).empty().append(stgDrpHtml);
//                         jQuery(this).val(Selected).trigger('liszt:updated');

//                     });
//                     // taluka district
//                     getTalukaDistrict();
//                 }

//             } else {
//                 toastError(data.response_message);
//             }
//         },
//         error: function(jqXHR, textStatus, errorThrown) {
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

function addedCity($event) {
    if ($event == true) {
        getCities(".mst-suggest_city");
    }
}




function suggestCity(e, $this) {

    // var thisModal = jQuery('#addCityFormModal');
    var keyevent = e
    if (keyevent.key != "Tab") {
        jQuery("#district_name").addClass('file-loader');
        var search = jQuery($this).val();

        jQuery.ajax({
            url: RouteBasePath + "/city-list?term=" + encodeURI(search),
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery("#district_name").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#district_name_list').html(data.cityList);
                } else {
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery("#district_name").removeClass('file-loader');
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
}



function getRelationData() {
    
    let stateId_val = jQuery('#state_id option:selected').val();


    if (stateId_val != "" && stateId_val !== undefined) {
        jQuery('#country_name').addClass('file-loader');

        jQuery.ajax({
            url: RouteBasePath + "/city-relation-field?state_id=" + stateId_val,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery("#country_name").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery("#commonDistrictForm").find('#country_name').val(data.relation_data.country_name);
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
    } else {
        jQuery("#commonDistrictForm").find('#country_name').val('');
    }
}
