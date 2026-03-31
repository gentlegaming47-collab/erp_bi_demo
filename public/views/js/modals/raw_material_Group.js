// function getRawMaterialGroup($this = null) {

//     if ($this != null) {
//         jQuery($this).next('.chzn-container').find('a').addClass('file-loader');
//     }

//     jQuery.ajax({

//         url: RouteBasePath + "/get-unitData",

//         type: 'GET',

//         dataType: 'json',

//         processData: false,

//         success: function (data) {

//             if ($this != null) {
//                 jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');
//             }

//             if (data.response_code == 1) {

//                 if ($this != null) {

//                     var stgDrpHtml = `<option value=""></option>`;

//                     for (let indx in data.unit) {

//                         stgDrpHtml += `<option value="${data.unit[indx].id}">${data.unit[indx].unit_name}</option>`;
//                     }

//                     jQuery($this).each(function (e) {

//                         let Id = jQuery(this).attr('id');

//                         let Selected = jQuery(this).find("option:selected").val();

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

// function addedUnits($event) {
//     if ($event == true) {

//         getUnits(".mst-unit");
//     }
// }



var validator = jQuery("#addrawMaterialGroupFormModal").validate({

    rules: {

        onkeyup: false,

        onfocusout: false,



        raw_material_group_nm: {

            required: true,

            maxlength: 255

        },


    },

    messages: {

        raw_material_group_nm: {

            required: "Please Enter Raw Material Group",

            maxlength: "Maximum 255 characters allowed"

        },



    },



    errorPlacement: function (error, element) {

        jAlert(error.text());

        return false;

    },





    submitHandler: function (form) {



        var formdata = jQuery('#addrawMaterialGroupFormModal').serialize();

        jQuery.ajax({

            url: RouteBasePath + "/store-raw-material-group",
            type: 'POST',

            data: formdata,



            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {



                    toastSuccess(data.response_message, nextFn);


                    //run this function if clicked on ok in message

                    function nextFn() {
                        jQuery("#raw_material_group_nm").val('');
                        jQuery('#rawMaterialGroup').modal('hide');
                        addedRawMaterialGroup(true);
                    }
                } else {
                    jAlert(data.response_message);
                    addedRawMaterialGroup(false);
                }
            },

            error: function (jqXHR, textStatus, errorThrown) {

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





function suggestUnit(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#unit_name").addClass('file-loader');

        var search = jQuery($this).val();



        jQuery.ajax({

            // url: "{{ route('unit-list') }}?term=" + encodeURI(search),

            url: RouteBasePath + "/unit-list/?term=" + encodeURI(search),



            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#unit_name").removeClass('file-loader');

                if (data.response_code == 1) {
                    jQuery('#company_unit_name_list').html(data.unitList);

                } else {

                    // toastError(data.response_message);

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#company_unit_name").removeClass('file-loader');

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

}