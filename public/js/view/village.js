

let villageHiddenId = jQuery('#commonVillageForm').find('input:hidden[name="id"]').val();


// custom jQuery validator

jQuery.validator.addMethod("verifypin", function (value, element) {
    function pincode(val) {
        var format = /[`!@#$%^&*()_,\=\[\]{};':"\\|.<>\/?~]/;
        if (format.test(value) == true) {
            return false;
        } else {
            return true;
        }
    }
    return this.optional(element) || pincode(value);
}, "only 0-9 and ('-','+') allowed");


jQuery(document).ready(function () {
    jQuery("#commonVillageForm").find('#country_name').prop({ tabindex: -1, readonly: true });
    // jQuery("#commonVillageForm").find('#country_name').attr('readonly', true);


    if (villageHiddenId != "" && villageHiddenId != undefined) {

        var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

        // get village data at edit time

        jQuery.ajax({

            url: RouteBasePath + "/get-villages/" + villageHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {

                    jQuery('#village_name').val(data.village.village_name);

                    jQuery('#village_state_id').val(data.village.s_id).trigger('liszt:updated');


                    jQuery("#commonVillageForm").find('#country_name').val(data.village.country_name);

                    jQuery('input:hidden[name="id"]').val(data.village.id);

                    jQuery('#default_pincode').val(data.village.default_pincode);

                    getDistrictData().done(function (resposne) {
                        jQuery('#district_id').val(data.village.d_id).trigger('liszt:updated');

                        getTalukaData().done(function (resposne) {
                            jQuery('#taluka_id').val(data.village.t_id).trigger('liszt:updated');
                        });
                    });


                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-village";
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

    var validator = jQuery("#commonVillageForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            village_name: {
                required: true,
                maxlength: 255
            },

            village_state_id: {
                required: true
            },

            district_id: {
                required: true
            },
            taluka_id: {
                required: true
            },

            // default_pincode:{
            //     required: true,
            //     verifypin: true
            // },

        },

        messages: {

            village_name: {
                required: "Please Enter Village",
                maxlength: "Maximum 255 Characters Allowed"
            },
            village_state_id: {
                required: "Please Select State"
            },
            district_id: {
                required: "Please Select District"
            },
            taluka_id: {
                required: "Please Select Taluka"
            },
            // default_pincode:{
            //      required: "Please Enter Pin code"
            // },

        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            var formdata = jQuery('#commonVillageForm').serialize();

            let village_name = jQuery('#village_name').val();
            let taluka_id = jQuery('#taluka_id').val();

            let formUrl = villageHiddenId != undefined && villageHiddenId != "" ? RouteBasePath + "/update-village" : RouteBasePath + "/store-village";

            if ((taluka_id != '' && taluka_id != undefined)) {
                jQuery.ajax({
                    url: RouteBasePath + "/verify-village?taluka_id=" + taluka_id + "&village_name=" + village_name + "&id=" + villageHiddenId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            // jAlert(data.response_message);
                            toastElement(data.response_message,"#village_name");
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


                                        if (villageHiddenId != undefined && villageHiddenId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-village";
                                            });
                                            addedVillage(true);
                                        }
                                        else if (villageHiddenId == undefined || villageHiddenId == "") {

                                            function nextFn() {

                                                document.getElementById("commonVillageForm").reset();


                                                jQuery('#commonVillageForm').find('#taluka_id').val('').trigger('liszt:updated');

                                                jQuery('#commonVillageForm').find('#village_state_id').val('').trigger('liszt:updated');

                                                jQuery('#commonVillageForm').find('#district_id').val('').trigger('liszt:updated');


                                                validator.resetForm();
                                                jQuery('#village_name').focus();

                                                // jQuery('#country_name').val('');
                                                jQuery('#VillageModal').modal('hide');
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
            }

        }

    });

});



// Village  Duplication  Code


function CheckVillage(village_name, taluka_id) {
    village_name = jQuery('#village_name').val();
    taluka_id = jQuery('#taluka_id').val();
    jQuery.ajax({
        url: RouteBasePath + "/verify-village?taluka_id=" + taluka_id + "&village_name=" + village_name + "&id=" + villageHiddenId,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jAlert(data.response_message);
                toastElement(data.response_message,"#village_name");
            }
        }
    });
}


jQuery(document).on('click', '#village_name_list', function (e) {
    let suggest = e.target.innerHTML;
    let data = suggest;

    let taluka_id = jQuery('#taluka_id').val();
    let village_name = jQuery('#village_name').val();

    // if taluka is not empty then call the function
    if (taluka_id != '') {
        CheckVillage(data, village_name, taluka_id);

    }
});




// fetch data in Drop down


function addedVillage($event) {

    if ($event == true) {
        get_village(".mst-suggest_village");
    }
}




// suggestionList

function suggestVillage(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#village_name").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/village-list/?term=" + encodeURI(search),

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    jQuery("#village_name").removeClass('file-loader');
                    jQuery('#village_name_list').html(data.villageList);

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


function getDistrictData() {


    let selectStateId = jQuery('#village_state_id option:selected').val();

    if (selectStateId != "" && selectStateId !== undefined) {

        return jQuery.ajax({


            url: RouteBasePath + "/getRelationDistrict?state_id=" + selectStateId,

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {


                jQuery("#commonVillageForm").find('#country_name').removeClass('file-loader');


                if (data.response_code == 1) {

                    let dropHtml = `<option value=''>Select District</option>`;

                    if (!jQuery.isEmptyObject(data.relation_data) && data.relation_data.length > 0) {
                        for (let idx in data.relation_data) {
                            dropHtml += `<option value="${data.relation_data[idx].id}">${data.relation_data[idx].district_name}</option>`;

                        }
                    }



                    jQuery('#district_id').empty().append(dropHtml).trigger('liszt:updated');
                    jQuery('#taluka_district_id').empty().append(dropHtml).trigger('liszt:updated');

                    jQuery("#commonVillageForm").find('#country_name').val(data.country_name.country_name);

                    // jQuery('#district_id').empty();

                } else {

                    jQuery('#district_id').empty().append("<option value=''>Select District</option>").trigger('liszt:updated');

                }



            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#commonVillageForm").find('#country_name').removeClass('file-loader');



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
    else {
        dropHtml = `<option value=''>Select District</option>`;
        jQuery('#district_id').empty().append(dropHtml).trigger('liszt:updated');

        dropHtml = `<option value=''>Select Taluka</option>`;
        jQuery('#taluka_id').empty().append(dropHtml).trigger('liszt:updated');

        jQuery("#commonVillageForm").find('#country_name').val('');
    }
}

function getTalukaData(e) {
    let selectDistricteId = jQuery('#district_id option:selected').val();

    if (selectDistricteId != "" && selectDistricteId !== undefined) {

        return jQuery.ajax({

            url: RouteBasePath + "/getTaluka/?district_id=" + selectDistricteId,

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#commonVillageForm").find('#country_name').removeClass('file-loader');

                if (data.response_code == 1) {

                    let dropHtml = `<option value=''>Select Taluka</option>`;

                    if (!jQuery.isEmptyObject(data.relation_taluka_data) && data.relation_taluka_data.length > 0) {

                        for (let idx in data.relation_taluka_data) {
                            dropHtml += `<option value="${data.relation_taluka_data[idx].id}">${data.relation_taluka_data[idx].taluka_name}</option>`;

                        }
                    }
                    jQuery('#taluka_id').empty().append(dropHtml).trigger('liszt:updated');

                } else {

                    console.log("else");

                    jQuery('#taluka_id').empty().append("<option value=''>Select Taluka </option>").trigger('liszt:updated');

                }



            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#commonVillageForm").find('#country_name').removeClass('file-loader');

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
    else {
        dropHtml = `<option value=''>Select Taluka</option>`;
        jQuery('#taluka_id').empty().append(dropHtml).trigger('liszt:updated');
    }
}




function getRelationData(e) {

    let thisVal = jQuery('#state_id option:selected').val();


    if (thisVal != "" && thisVal !== undefined) {


        jQuery("#commonVillageForm").find('#country_name').addClass('file-loader');


        jQuery.ajax({

            url: RouteBasePath + "/village-relation-field/?state_id=" + thisVal,

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#commonVillageForm").find('#country_name').removeClass('file-loader');


                if (data.response_code == 1) {

                    jQuery("#commonVillageForm").find('#country_name').val(data.relation_data[0].country_name).prop( {tabindex : -1}).attr('readonly', true);
                    jQuery("#commonDistrictForm").find('#country_name').val(data.relation_data[0].country_name).prop( {tabindex : -1}).attr('readonly', true)



                } else {

                    toastError(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#commonVillageForm").find('#country_name').removeClass('file-loader');

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
jQuery('#cityModal').on('show.bs.modal', function (e)
{
    let state = jQuery("#village_state_id").val();
    let country =   jQuery('#commonVillageForm #country_name').val();
    
    jQuery("#state_id").val(state).trigger('liszt:updated');
    jQuery("#commonDistrictForm #country_name").val(country).prop('diabled',false);

});
