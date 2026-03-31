
var so_data = [];
var material_data = [];
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
var getLocationType = jQuery("#getLocationType").val();

var soIsUse = false;

var productDrpHtml = '<option value="">Select Item</option>';
var fittingProductDrpHtml = '<option value="">Select Item</option>';

// we will display the date as DD-MM-YYYY 

let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

var formId = jQuery('#salesorderform').find('input:hidden[name="id"]').val();

if (formId !== undefined) { //if form is edit

    jQuery(document).ready(function () {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({

            url: RouteBasePath + "/get-sales_order/" + formId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {
                    jQuery('#sup_rejection_button').prop('disabled', true);

                    // setTimeout(() => {
                    //     jQuery('#so_sequence').focus();
                    // }, 100);


                    jQuery('input:radio[name="so_from_id_fix"][value="' + data.so_data.so_from_id_fix + '"]').attr('checked', true).trigger('click');

                    jQuery('#so_from_id_fix').val(data.so_data.so_from_id_fix);

                    jQuery('input:radio[name="so_type_id_fix"][value="' + data.so_data.so_type_id_fix + '"]').attr('checked', true).trigger('click');

                    jQuery('#so_type_id_fix').val(data.so_data.so_type_id_fix);

                    jQuery('#so_sequence').val(data.so_data.so_sequence).prop({ tabindex: -1, readonly: true });

                    jQuery('#so_no').val(data.so_data.so_number).prop({ tabindex: -1, readonly: true });

                    jQuery('#so_date').val(data.so_data.so_date);

                    jQuery('#customer_reg_no').val(data.so_data.customer_reg_no);

                    if (data.so_data.so_type_id_fix == 1) {
                        jQuery('#customer_name').val(data.so_data.customer_name);
                    } else {
                        setTimeout(() => {
                            jQuery('#rep_customer_id').val(data.so_data.customer_name).trigger('liszt:updated');
                        }, 800);

                        jQuery('#pre_so_no').prop({ tabindex: -1 }).attr('readonly', true);

                        jQuery('#rep_customer_id').prop({ tabindex: -1 }).attr('readonly', true);
                    }


                    jQuery('#dealer_id').val(data.so_data.dealer_id).trigger('liszt:updated');

                    jQuery('#dealer_id').val(data.so_data.dealer_id).trigger('liszt:updated');


                    jQuery('#customer_village').val(data.so_data.customer_village);

                    jQuery('#customer_pincode').val(data.so_data.customer_pincode);

                    jQuery('#so_mobile_no').val(data.so_data.mobile_no);
                    jQuery('#area').val(data.so_data.area);
                    jQuery('#ship_to').val(data.so_data.ship_to);
                    jQuery('#so_country_id').val(data.so_data.country_id).trigger('liszt:updated');
                    jQuery('#mis_category_id').val(data.so_data.mis_category_id).trigger('liszt:updated');

                    if (data.so_data.file_upload != "") {

                        jQuery('#file_upload_doc').val(data.so_data.file_upload);

                        jQuery('#file_upload_prev').attr('href', data.so_data.file_path + data.so_data.file_upload);

                        jQuery('#file_upload_prev').removeClass('hide');

                        jQuery('.remove-file').addClass('i-block').removeClass('hide');

                    } else {

                        jQuery('#file_upload_doc').val();

                        jQuery('#file_upload_prev').attr('href', '#');

                        jQuery('#file_upload_prev').addClass('hide');

                        jQuery('.remove-file').removeClass('i-block').addClass('hide');

                    }





                    getSoStates();
                    soType();

                    // setTimeout(() => {
                    //     jQuery('#so_location_id').val(data.so_data.to_location_id).trigger('liszt:updated');
                    // }, 800);



                    loadSalesOrderData(data);

                    jQuery('#special_notes').val(data.so_data.special_notes);
                    jQuery('.soqtysum').val(data.so_qty);
                    jQuery('.soqtysum_second').val(data.soqtysum_second != null ? parseFloat(data.soqtysum_second).toFixed(3) : "");
                    jQuery('.amountsum').val(data.so_amount);

                    /* GST Fill Data */
                    jQuery('#basic_amount').val(data.so_data.basic_amount != null ? data.so_data.basic_amount.toFixed(2) : null);

                    jQuery('#less_discount_percentage').val(data.so_data.less_discount_percentage != null ? data.so_data.less_discount_percentage.toFixed(2) : null);
                    jQuery('#less_discount_amount').val(data.so_data.less_discount_amount != null ? data.so_data.less_discount_amount.toFixed(2) : null);

                    jQuery('#secondary_transport').val(data.so_data.secondary_transport != null ? data.so_data.secondary_transport.toFixed(2) : null);
                    jQuery('#sharing_head_unit_cost').val(data.so_data.sharing_head_unit_cost != null ? data.so_data.sharing_head_unit_cost.toFixed(2) : null);
                    jQuery('#installation_charge').val(data.so_data.installation_charge != null ? data.so_data.installation_charge.toFixed(2) : null);

                    jQuery('input:radio[name="gst_type_fix_id"][value="' + data.so_data.gst_type_fix_id + '"]').attr('checked', true).trigger('click');
                    jQuery('#sgst_percentage').val(data.so_data.sgst_percentage != null ? data.so_data.sgst_percentage.toFixed(2) : null);
                    jQuery('#sgst_amount').val(data.so_data.sgst_amount != null ? data.so_data.sgst_amount.toFixed(2) : null);
                    jQuery('#cgst_percentage').val(data.so_data.cgst_percentage != null ? data.so_data.cgst_percentage.toFixed(2) : null);
                    jQuery('#cgst_amount').val(data.so_data.cgst_amount != null ? data.so_data.cgst_amount.toFixed(2) : null);
                    jQuery('#igst_percentage').val(data.so_data.igst_percentage != null ? data.so_data.igst_percentage.toFixed(2) : null);
                    jQuery('#igst_amount').val(data.so_data.igst_amount != null ? data.so_data.igst_amount.toFixed(2) : null);


                    jQuery('#round_off').val(data.so_data.round_off_val != null ? data.so_data.round_off_val.toFixed(2) : null);
                    // jQuery('#net_amount').val(data.so_data.net_amount != null ? data.so_data.net_amount.toFixed(2) : null);



                    if (data.so_data.so_from_id_fix == 1 || data.so_data.so_from_id_fix == 2) {
                        if (data.so_data.country_id != null) {
                            getSoStates().done(function (resposne) {
                                jQuery('#so_state_id').val(data.so_data.state_id).trigger('liszt:updated');
                                getSoDistrict().done(function (resposne) {
                                    jQuery('#so_district_id').val(data.so_data.district_id).trigger('liszt:updated');
                                    getSoTaluka().done(function (resposne) {
                                        jQuery('#so_taluka_id').val(data.so_data.customer_taluka).trigger('liszt:updated');
                                        getSoVillage().done(function (resposne) {
                                            jQuery('#customer_village').val(data.so_data.customer_village).trigger('liszt:updated');
                                        });
                                    });

                                });
                            });
                        }

                    }


                    jQuery('#customer_group_id').prop({ tabindex: -1 }).attr('readonly', true);
                    if (data.so_data.in_use == true) {
                        jQuery('#so_sequence').prop({ tabindex: -1, readonly: true });
                        jQuery('#so_date').prop({ tabindex: -1, readonly: true });
                        jQuery('#customer_name').prop({ tabindex: -1, readonly: true });
                        jQuery('#dealer_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#customer_reg_no').prop({ tabindex: -1, readonly: true });
                        jQuery('#customer_village').prop({ tabindex: -1, readonly: true });
                        jQuery('#customer_pincode').prop({ tabindex: -1, readonly: true });
                        jQuery('#so_country_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#so_state_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#so_district_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#so_taluka_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#customer_village').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#so_mobile_no').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#area').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#ship_to').prop({ tabindex: -1 }).attr('readonly', true);


                        // jQuery('#so_location_id').prop({ tabindex: -1 }).attr('readonly', true);


                        jQuery('#special_notes').prop({ tabindex: -1, readonly: true });
                        jQuery('#basic_amount').prop({ tabindex: -1, readonly: true });
                        /*jQuery('#secondary_transport').prop({ tabindex: -1, readonly: true });
                        jQuery('#gst_type_fix_id').prop({ tabindex: -1, readonly: true });
                        jQuery('#sgst_percentage').prop({ tabindex: -1, readonly: true });
                        jQuery('#sgst_amount').prop({ tabindex: -1, readonly: true });
                        jQuery('#cgst_percentage').prop({ tabindex: -1, readonly: true });
                        jQuery('#cgst_amount').prop({ tabindex: -1, readonly: true });
                        jQuery('#igst_percentage').prop({ tabindex: -1, readonly: true });
                        jQuery('#igst_amount').prop({ tabindex: -1, readonly: true });
                        jQuery('#round_off').prop({ tabindex: -1, readonly: true });
                        jQuery('#net_amount').prop({ tabindex: -1, readonly: true });

                        jQuery("input[name*='gst_type_fix_id']").prop({ tabindex: -1 }).attr('readonly', true);*/


                        jQuery('#addPart').prop('disabled', true);

                        jQuery('#replace_btn').prop('disabled', true);


                        soIsUse = true;
                    }


                    if (data.so_part_details_details.length > 0 && !jQuery.isEmptyObject(data.so_part_details_details)) {


                        var groupedData = data.so_part_details_details.reduce((acc, obj) => {
                            var itemId = obj.mitem_id;
                            if (!acc[itemId]) {
                                acc[itemId] = [];
                            }
                            acc[itemId].push(obj);
                            return acc;
                        }, {});

                        // var resultArray = Object.values(groupedData);



                        // var Data = resultArray.reduce((acc, arr) => {
                        //     arr.forEach(obj => {
                        //         const key = obj.mitem_id.toString(); // Use mitem_id as string key
                        //         if (!acc[key]) {
                        //             acc[key] = [];
                        //         }
                        //         acc[key].push(obj);
                        //     });
                        //     return acc;
                        // }, {});

                        // console.log(groupedData[0]);

                        // console.log(resultArray)

                        for (let key in data.so_part_details) {
                            if (groupedData[data.so_part_details[key].item_id] != undefined) {
                                storeSalesOrderDetails[data.so_part_details[key].item_id] = groupedData[data.so_part_details[key].item_id];
                            }

                        }
                        // storeSalesOrderDetails(data.so_part_details_details);
                    }



                    jQuery("input[name*='so_from_id_fix']").prop({ tabindex: -1 }).attr('readonly', true);
                    jQuery("input[name*='so_type_id_fix']").prop({ tabindex: -1 }).attr('readonly', true);
                    // jQuery("input[name*='so_from_id_fix']").attr("readonly", true);
                    // jQuery("input[name*='so_type_id_fix']").attr("readonly", true);



                    if (data.so_data.so_from_id_fix == 3 && data.so_data.so_type_id_fix == 1) {

                        for (let key in data.so_part_details) {
                            material_data.push(data.so_part_details[key]);

                        }
                        setTimeout(() => {
                            fillPendingMaterialData();
                        }, 1000);

                        fillPendingMaterialTable();
                        getLocationForMR().done(function (resposne) {

                            jQuery('#so_location_id').val(data.so_data.to_location_id).trigger('liszt:updated');


                        });

                        jQuery("#so_location_id").prop({ tabindex: -1, readonly: true });
                        jQuery("#so_location_id").attr("readonly", true);

                        disabledDropdownVal();
                        setTimeout(() => {
                            jQuery('#sup_rejection_button').prop('disabled', false);
                        }, 1200);


                    } else {


                        jQuery('#sup_rejection_button').prop('disabled', false);


                    }
                    manageGstType();
                    jQuery('#net_amount').val(data.so_data.net_amount != null ? data.so_data.net_amount.toFixed(2) : data.so_data.total_amount.toFixed(2));

                    jQuery('#show-progress').removeClass('loader-progress-whole-page');
                }
            },

            error: function (jqXHR, textStatus, errorThrown) {

                var errMessage = JSON.parse(jqXHR.responseText);



                if (jqXHR.status == 401) {



                    toastError(jqXHR.statusText);

                } else {



                    toastError('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    });

} else { //for Add
    jQuery(document).ready(function () {

        getLatestSoNo();
        getCountyandStateForLocation();
        manageGstType();
        // changeRadio(1)
        // getLastSo(1);
        // changeFormRadio(1);

        // addPartDetail();
        getSoDealer();

    });

}


async function loadSalesOrderData(data) {
    try {
        jQuery('#customer_group_id').val(data.so_data.customer_group_id).trigger('liszt:updated');
        await getSoDealer();
        jQuery('#dealer_id').val(data.so_data.dealer_id).trigger('liszt:updated');
        jQuery('#dealer_id').change();
        // await getItemRateFromPriceListForSO();

        if (data.so_data.so_from_id_fix == 1 &&
            (data.so_data.so_type_id_fix == 1 || data.so_data.so_type_id_fix == 2)) {
            fillSoTable(data.so_part_details);
        }

        if (data.so_data.so_from_id_fix == 2 &&
            (data.so_data.so_type_id_fix == 1 || data.so_data.so_type_id_fix == 2)) {
            fillSoTable(data.so_part_details);
        }


    } catch (error) {
        console.log("Error: ", error);
    }
}
async function loadSalesOrderRepData(data) {
    try {
        jQuery('#customer_group_id').val(data.sales_order.customer_group_id).trigger('liszt:updated');
        await getSoDealer();
        jQuery('#dealer_id').val(data.sales_order.dealer_id).trigger('liszt:updated');
        jQuery('#dealer_id').change();
        // await getItemRateFromPriceListForSO();

        // if (data.so_data.so_from_id_fix == 1 &&
        //     (data.so_data.so_type_id_fix == 1 || data.so_data.so_type_id_fix == 2)) {
        //     fillSoTable(data.so_part_details);
        // }
        // if (data.so_data.so_from_id_fix == 2 &&
        //     (data.so_data.so_type_id_fix == 1 || data.so_data.so_type_id_fix == 2)) {
        //     fillSoTable(data.so_part_details);
        // }


        // jQuery('#soPartTable tbody').empty();
        // if (data.sales_order.so_from_id_fix == 1 &&
        //     (data.sales_order.so_type_id_fix == 1 || data.sales_order.so_type_id_fix == 2)) {
        //     fillSoTable(data.sales_order_part);
        // }
        // if (data.sales_order.so_from_id_fix == 2 &&
        //     (data.sales_order.so_type_id_fix == 1 || data.sales_order.so_type_id_fix == 2)) {
        //     fillSoTable(data.sales_order_part);
        // }

    } catch (error) {
        console.log("Error: ", error);
    }
}

// jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
//     return this.optional(element) || parseInt(value) >= 0.01;
// });
jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) >= 0.001;
});

// validation for rate
jQuery.validator.addMethod("salesRate", function (value, element, param) {
    return this.optional(element) || parseFloat(value) > 0.01;
    //return this.optional(element) || parseFloat(value) >= parseFloat(param);
});


var validator = jQuery("#salesorderform").validate({
    ignore: [],
    onclick: false,
    onkeyup: false,
    rules: {



        so_sequence: {



            required: true



        },
        so_date: {



            required: true,
            dateFormat: true,
            date_check: true


        },

        customer_name: {
            required: function (e) {

                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    if (jQuery("#salesorderform").find('input[name="so_type_id_fix"]:checked').val() != "2") {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            },
        },
        rep_customer_id: {
            required: function (e) {

                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    if (jQuery("#salesorderform").find('input[name="so_type_id_fix"]:checked').val() != "1") {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            },
        },

        customer_group_id: {
            required: function (e) {

                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        dealer_id: {
            //required: true
            required: function (e) {
                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        customer_village: {
            required: function (e) {

                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        so_country_id: {
            required: function (e) {

                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        so_state_id: {
            required: function (e) {

                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        so_district_id: {
            required: function (e) {

                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        so_taluka_id: {
            required: function (e) {

                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        so_location_id: {
            required: function (e) {

                if (jQuery('input[name="so_from_id_fix"]:checked').val() != "1" && jQuery('input[name="so_from_id_fix"]:checked').val() != "2") {
                    return true;
                } else {
                    return false;
                }
            },
        },

        'item_id[]': {

            required: function (e) {
                var selectedValue = jQuery("#salesorderform").find('#so_taluka_id').val();
                var value = jQuery("#salesorderform").find('#item_id').val();
                if (selectedValue != "" && selectedValue != null) {
                    jQuery(e).parent('tr').addClass('error');
                    // jQuery(e).focus();
                    return true;
                } else {
                    jQuery(e).removeClass('error');
                    return false;
                }
            },
        },

        'so_qty[]': {
            required: function (e) {
                if (jQuery(e).val().trim() === "" && jQuery(e).closest('tr').find("#item_id").val() != "") {
                    // jQuery(e).addClass('error');
                    // setTimeout(() => {
                    //     jQuery(e).focus();
                    // }, 1000);
                    // jQuery(e).focus();
                    return true;
                } else {
                    // jQuery(e).removeClass('error');
                    return false;
                }

            },
            notOnlyZero: '0.001',
        },
        'rate_unit[]': {
            required: function (e) {
                if (jQuery(e).prop('disabled')) {
                    return true;
                } else {
                    if (jQuery(e).val().trim() == "" && jQuery(e).closest('tr').find("#so_qty").val() != "") {
                        // jQuery(e).addClass('error');
                        // jQuery(e).focus();
                        return true;
                    } else {
                        jQuery(e).removeClass('error');
                        return false;
                    }
                }
            },
            salesRate: '0.01',
        },
        'amount[]': {


            required: true

        },


        // so_country_id: {
        //     required: function (e) {

        //         if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "2") {
        //             return true;
        //         } else {
        //             return false;
        //         }
        //     },
        // },
        mis_category_id: {
            required: function (e) {

                if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() != "3") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        so_mobile_no: {
            numberFormat: true
        },


    },

    messages: {



        so_sequence: {



            required: "Please Enter SO No."



        },

        so_date: {



            required: "Please Enter SO Date",



        },
        customer_group_id: {



            required: "Please Select Customer Group"



        },
        dealer_id: {
            required: "Please Select Dealer"
        },
        customer_name: {
            required: "Please Enter Customer Name",
        },
        rep_customer_id: {
            required: "Please Enter Customer Name",
        },

        so_location_id: {
            required: "Please Select Location",
        },
        customer_village: {
            required: "Please Enter Village"
        },
        so_country_id: {
            required: "Please Select Country"
        },
        so_state_id: {
            required: "Please Select State"
        },
        so_district_id: {
            required: "Please Select District"
        },
        so_taluka_id: {
            required: "Please Select Taluka"
        },


        // so_our_no: {



        //     required: "Please Enter Our SO No.",



        // },
        // so_our_date: {



        //     required: "Please Enter Our SO Date",



        // },
        'item_id[]': {

            required: "Please Select Item"

        },
        'so_qty[]': {

            required: "Please Enter SO Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.001'

        },
        'rate_unit[]': {

            required: "Please Enter Rate/Unit",
            salesRate: 'Please Enter A Value Greater Than 0.01'


        },
        'amount[]': {

            required: "Please Enter Amount",

        },
        mis_category_id: {
            required: "Please Select MIS Category"
        },

        // so_customer_group_id :{
        //     required: "Please Select Customer Group",
        // },

    },
    errorPlacement: function (error, element) {
        error.insertAfter(element);
        return false;
    },



    submitHandler: function (form) {

        // console.log(storeSalesOrderDetails);

        var error = false;
        jQuery('#soPartTable tbody tr').each(function (indx, td) {
            var check_fitting = jQuery(td).find('input[name="check_fitting[]"]').val();
            var item_id = jQuery(td).find('select[name="item_id[]"]').val();


            if (check_fitting == 'Yes') {
                if (storeSalesOrderDetails[item_id] == undefined) {
                    error = true;
                } else {
                    if (storeSalesOrderDetails[item_id].length == 0) {
                        error = true;
                    }
                }
            }

        });


        if (error) {
            toastError('Please Add At Least One Fitting Item Detail.');
            return false;
        }

        let checkLength = jQuery("#soPartTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        var sales_order_radio = jQuery("input[name*='so_from_id_fix']:checked").val();
        var typeFixId = jQuery('input[name="so_type_id_fix"]:checked').val();

        if (checkLength < 1) {
            jAlert("Please Add At Least One Sales Order Detail.");


            // sales_order_radio == 1 && typeFixId == 1 ? addPartDetail() : "";

            if (sales_order_radio == 1 && typeFixId == 1) {
                addPartDetail();
            } else {

                if (typeFixId == 2) {
                    jQuery('#pre_so_no').attr('readonly', false);
                    jQuery('#rep_customer_id').attr('readonly', false);
                    getSearchData();
                }

            }


            return false;
        }


        if (formId == undefined) {
            var so_form_date = jQuery('#salesorderform').find('#so_date').val();
            var so_agreement_end_date = jQuery('#salesorderform').find('#agreement_end_date').val();

            if (so_agreement_end_date != '') {
                var parts_so = so_form_date.split('/');
                var parts_agreement = so_agreement_end_date.split('/');

                var date_so = new Date(parts_so[2], parts_so[1] - 1, parts_so[0]);
                var date_agreement = new Date(parts_agreement[2], parts_agreement[1] - 1, parts_agreement[0]);

                // Compare dates
                if (date_agreement < date_so) {
                    toastError('Dealer Agreement Is Expired.');
                    return false;
                }
            }
        } else {

            var so_form_date = jQuery('#salesorderform').find('#so_date').val();
            var so_agreement_end_date = jQuery('#salesorderform').find('#agreement_end_date').val();
            var check_date = '31/05/2025';

            var parts_so = so_form_date.split('/');
            var parts_agreement = so_agreement_end_date.split('/');
            var parts_check = check_date.split('/');

            var date_so = new Date(parts_so[2], parts_so[1] - 1, parts_so[0]);
            var date_agreement = new Date(parts_agreement[2], parts_agreement[1] - 1, parts_agreement[0]);
            var date_check = new Date(parts_check[2], parts_check[1] - 1, parts_check[0]);

            if (date_so > date_check) {
                if (date_agreement < date_so) {
                    toastError('Dealer Agreement Is Expired.');
                    return false;
                }

            }


        }


        jQuery('#sup_rejection_button').prop('disabled', true);
        var formUrl = formId !== undefined ? RouteBasePath + "/update-sales_order" : RouteBasePath + "/store-sales_order";
        let formData = jQuery('#salesorderform').serialize();
        // console.log(formData)
        // return;

        // if (formId !== undefined) {
        //     storeSalesOrderDetails = storeSalesOrderDetails.filter(function (el) {
        //         return el != null;
        //     });
        // }

        // return;

        // console.log(storeSalesOrderDetails);


        // return;


        // let requestData = formData + '&' + jQuery.param({ storeSalesOrderDetails: storeSalesOrderDetails });

        const cleanedstoreSalesOrderDetails = Object.entries(storeSalesOrderDetails).reduce((acc, [key, value]) => {
            if (Array.isArray(value) && value.length > 0 && value[0] !== null) {
                acc[key] = value;
            }
            return acc;
        }, {});


        let requestData = formData + '&' + jQuery.param({ storeSalesOrderDetails: cleanedstoreSalesOrderDetails });

        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: requestData,
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (formId !== undefined) {
                        toastSuccess(data.response_message, redirectFn);
                        function redirectFn() {
                            window.location.href = RouteBasePath + "/manage-sales_order";
                        };
                    } else {
                        toastSuccess(data.response_message, redirectFn);
                        function redirectFn() {
                            window.location.reload();
                        }
                        jQuery('#sup_rejection_button').prop('disabled', false);
                    }
                } else {
                    jQuery('#sup_rejection_button').prop('disabled', false);
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);
                if (errMessage.errors) {
                    jQuery('#sup_rejection_button').prop('disabled', false);
                    validator.showErrors(errMessage.errors);
                } else if (jqXHR.status == 401) {
                    jQuery('#sup_rejection_button').prop('disabled', false);
                    toastError(jqXHR.statusText);
                } else {

                    jQuery('#sup_rejection_button').prop('disabled', false);
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
});



// Model Submit Handler 
let storeSalesOrderDetails = [];

validator = jQuery("#commonSalesOrderForm").validate({
    ignore: [],
    onclick: false,
    rules: {


        'item_id[]': {

            required: true

        },
        'so_qty[]': {

            required: true,
            notOnlyZero: '0.001'

        },

    },

    messages: {

        'item_id[]': {

            required: "Please Select Item"

        },
        'so_qty[]': {

            required: "Please Enter SO Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than Or Equal To 1.'

        },

    },



    submitHandler: function (form) {

        let checkLength = jQuery("#soPartModalTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;


        if (checkLength < 1) {
            jAlert("Please Add At Least One Sales Order Detail.");
            addModalPartDetail();

            return false;
        }


        let formSerialize = jQuery("#commonSalesOrderForm").serializeArray();


        let storeArr = [];


        let itemId;
        let soQty;

        var index = jQuery("#commonSalesOrderForm").find('#mainItemId').val();

        jQuery(formSerialize).each(function (i, field) {

            if ((`item_id_${i}` != undefined) && (`so_qty_${i}` != undefined)) {
                itemId = jQuery("#commonSalesOrderForm").find(`#item_id_${i}`).val();
                soQty = jQuery("#commonSalesOrderForm").find(`#so_qty_${i}`).val();
                sod_details_id = jQuery("#commonSalesOrderForm").find(`#sales_order_detail_details_id_${i}`).val();
                hitemId = jQuery("#commonSalesOrderForm").find(`#hitem_id_${i}`).val();




            }

            storeArr.push({ 'item_id': itemId, 'so_qty': soQty, 'sod_details_id': sod_details_id, 'hitem_id': hitemId, });
        });

        // console.log('str', storeArr)
        storeSalesOrderDetails[index] = storeArr.filter(function (val) {
            return val.item_id !== undefined && val.so_qty !== undefined;
            // return val.hitem_id !== undefined;
        }).map(function (val) {
            return {
                item_id: val.item_id,
                so_qty: val.so_qty,
                sod_details_id: val.sod_details_id,
                hitem_id: val.hitem_id,
            };
        });


        // let checkDetailsArray = [];
        // if (storeSalesOrderDetails.length > 0 && !jQuery.isEmptyObject(storeSalesOrderDetails)) {

        //     checkDetailsArray = storeArr.filter(function (val) {
        //         return val.item_id !== undefined && val.so_qty !== undefined;
        //     }).map(function (val) {

        //         return {
        //             item_id: val.item_id,
        //             so_qty: val.so_qty,
        //             sod_details_id: val.sod_details_id,
        //         };
        //     });

        // } else {
        // storeSalesOrderDetails[index] = storeArr.filter(function (val) {
        //     return val.item_id !== undefined && val.so_qty !== undefined;
        // }).map(function (val) {

        //     return {
        //         item_id: val.item_id,
        //         so_qty: val.so_qty,
        //         sod_details_id: val.sod_details_id,
        //     };
        // });
        // }



        jQuery("#salesOrderModal").modal('hide');
        index++;
        jQuery('#soPartModalTable tbody').empty();
    }


});





function fillSoTable(so_data) {

    if (so_data.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in so_data) {

            var sr_no = counter;
            var sales_order_id = so_data[key].so_details_id ? so_data[key].so_details_id : "";

            var item_id = so_data[key].item_id ? so_data[key].item_id : "";
            var item_code = so_data[key].item_code ? so_data[key].item_code : "";
            var item_group_name = so_data[key].item_group_name ? so_data[key].item_group_name : "";
            var so_qty = so_data[key].so_qty ? so_data[key].so_qty.toFixed(3) : "";

            var unit_name = so_data[key].unit_name ? so_data[key].unit_name : "";
            var rate_per_unit = so_data[key].rate_per_unit ? parseFloat(so_data[key].rate_per_unit).toFixed(2) : "";
            var so_amount = so_data[key].so_amount ? so_data[key].so_amount : "";
            var fitting_item = so_data[key].fitting_item ? so_data[key].fitting_item : "";
            var remarks = so_data[key].remarks ? so_data[key].remarks : "";
            var discount = so_data[key].discount ? so_data[key].discount.toFixed(2) : (0).toFixed(2);

            var type_id_fix = jQuery("#salesorderform").find('input[name="so_type_id_fix"]:checked').val();

            if (fitting_item == 'yes') {
                if (so_data[key].in_use == true) {
                    var eyeIcon1 = '<span class="eyeMargin"><a><i class="action-icon iconfa-eye-open"></i></a></span>';
                } else {
                    var eyeIcon1 = '<span class="eyeMargin"><a><i class="action-icon iconfa-eye-open eyeIcon1"></i></a></span>';
                }
                var value_fitting = "Yes";
            } else {
                var eyeIcon1 = '<span class="eyeMargin"><a><i class="action-icon iconfa-eye-open eyeIcon1 d-none"></i></a></span>';

                var value_fitting = "No";
            }
            if (formId != undefined && type_id_fix == 2) {


                thisHtml += `<tr>`
                if (so_data[key].in_use == true) {
                    //thisHtml += `<td><a><i class="action-icon iconfa-trash so_details"></i></a></td>`;
                    thisHtml += `<td></td>`;
                } else {
                    thisHtml += `<td><a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>`;

                }
                thisHtml += `<td class="sr_no" >${sr_no}</td>

                            <td><input type="hidden" name="sales_order_detail_id[]" value="${sales_order_id}">
                            <select name="item_id[]" class="chzn-select item_id item_id_${sr_no} so_item_select_width" onChange="getItemData(this), sumSoQty(this)" readonly tabindex="-1">${productDrpHtml}</select></td>   

                            <td id="code">${item_code}</td>`;
            } else {

                thisHtml += `<tr>`;
                if (so_data[key].in_use == true) {
                    // thisHtml += `<td><a><i class="action-icon iconfa-trash so_details"></i></a></td>`;
                    thisHtml += `<td></td>`;
                } else {
                    thisHtml += ` <td><a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>`;

                }
                thisHtml += `<td class="sr_no">${sr_no}</td>  

                            <td><input type="hidden" name="sales_order_detail_id[]" value="${sales_order_id}">
                            <select name="item_id[]" class="chzn-select item_id item_id_${sr_no} so_item_select_width" onChange="getItemData(this), sumSoQty(this)"  ${so_data[key].in_use == true ? 'readonly tabindex="-1"' : ''}>${productDrpHtml}</select>
                            ${eyeIcon1} <input type="hidden" name="check_fitting[]" value="${value_fitting}">
                            </td>          

                            <td id="code">${item_code}</td>`;
            }
            // if (fitting_item == 'yes') {
            //     thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty" tabindex="-1" readonly  onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}" /></td>`;
            // } else {
            //     thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty"  onKeyup="sumSoQty(this)"  onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}" ${so_qty == parseFloat(so_data[key].used_qty).toFixed(3) ? 'readonly tabindex="-1"' : ''}/></td>`;
            // }
            if (fitting_item == 'yes') {
                // thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty" tabindex="-1" readonly  onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}"  min="${parseFloat(so_data[key].used_qty).toFixed(3)}"/></td>`;
                thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty" tabindex="-1" onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}" ${so_qty == parseFloat(so_data[key].used_qty).toFixed(3) ? 'readonly tabindex="-1"' : ''} min="${parseFloat(so_data[key].used_qty).toFixed(3)}"/></td>`;
            } else {

                if (formId != undefined && type_id_fix == 2) {
                    thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty"  onKeyup="sumSoQty(this)"  onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}" readonly tabindex="-1" min="${parseFloat(so_data[key].used_qty).toFixed(3)}"/></td>`;

                } else {
                    thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty"  onKeyup="sumSoQty(this)"  onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}" ${so_qty == parseFloat(so_data[key].used_qty).toFixed(3) ? 'readonly tabindex="-1"' : ''} min="${parseFloat(so_data[key].used_qty).toFixed(3)}"/></td>`;
                }

            }
            thisHtml += `  <td id="unit">${unit_name}</td>`;

            if (fitting_item == 'yes' || getLocationType == "HO") {
                thisHtml += `    <td><input type="text" name="rate_unit[]"  onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey" onblur="formatPoints(this,2)"/ value="${rate_per_unit}" ${so_qty == parseFloat(so_data[key].used_qty).toFixed(3) ? 'readonly tabindex="-1"' : ''} style="width:60px;"></td>`;
            } else {
                thisHtml += `    <td><input type="text" name="rate_unit[]"  onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey" onblur="formatPoints(this,2)"/ value="${rate_per_unit}" ${so_qty == parseFloat(so_data[key].used_qty).toFixed(3) ? 'readonly tabindex="-1"' : ''} style="width:60px;"readonly></td>`;
            }
            thisHtml += `<td><input type="text" name="discount[]" id="discount" onKeyup="Discount(this)" id="discount" class="form-control  discount  isNumberKey" maxlength="5" value="${discount}" onblur="formatPoints(this,2)" style="width:50px;" /></td>

                <td><input type="number" name="amount[]" id="amount" class="form-control amount" onblur="formatPoints(this,2)" tabindex="-1" value="${formatAmount(so_amount)}" readonly tabindex="-1" style="width:70px;"/></td>  
                 <td>
                        <input type="text" name="remarks[]" id="remarks" class="form-control" value="${remarks}"/>
                    </td>            
                </tr>`;
            counter++;


        }


        jQuery('#soPartTable tbody').append(thisHtml);


        var counter = 1;
        for (let key in so_data) {


            var item_id = so_data[key].item_id ? so_data[key].item_id : "";

            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            counter++;


        }


        /* jQuery('#soPartTable tbody').append(`<tr class="total_tr"><td colspan="5" ></td><td class="soqtysum"></td>
         <td></td>
         <td></td>
         <td class="amountsum"></td></tr>`);*/

        sumSoQty();
        srNo();
        totalAmount();
        disabledDropdownVal();
    }
}

function getLatestSoNo() {

    jQuery.ajax({

        url: RouteBasePath + "/get-latest_so_no",

        type: 'GET',

        headers: headerOpt,

        dataType: 'json',

        processData: false,

        success: function (data) {

            jQuery('#so_no').removeClass('file-loader');

            if (data.response_code == 1) {

                jQuery('#so_no').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });

                jQuery('#so_sequence').val(data.number).prop({ tabindex: -1, readonly: true });

                jQuery('#so_date').val(currentDate);





            } else {

                console.log(data.response_message)

            }

        },

        error: function (jqXHR, textStatus, errorThrown) {

            jQuery('#so_no').removeClass('file-loader');

            console.log('Field To Get Latest SO No.!')

        }

    });





}


// function getCustomer() {
//     let customer = jQuery('input[name="so_from"]:checked').val();

//     if (customer != "" && customer != null) {
//         jQuery.ajax({

//             url: RouteBasePath + "/get-so_customer?customer=" + customer,

//             type: 'GET',

//             headers: headerOpt,

//             dataType: 'json',

//             processData: false,

//             success: function (data) {

//                 if (data.response_code == 1) {
//                     let dropHtml = `<option value=''>Select Customer</option>`;
//                     if (!jQuery.isEmptyObject(data.customer) && data.customer.length > 0) {
//                         for (let idx in data.customer) {
//                             dropHtml += `<option value="${data.customer[idx].id}">${data.customer[idx].customer_name}</option>`;
//                         }
//                     }
//                     jQuery('#so_customer_id').empty().append(dropHtml).trigger('liszt:updated');
//                 } else {
//                     jQuery('#so_customer_id').empty().append("<option value=''>Select Customer</option>").trigger('liszt:updated');
//                 }
//             },

//         });

//     }

// }

function getRegNo() {

    let customer = jQuery('#so_customer_id option:selected').val();

    if (customer != "" && customer != null) {
        jQuery.ajax({

            url: RouteBasePath + "/get-so_reg_no?customer=" + customer,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    jQuery('#so_reg_no').val(data.customer.register_number);
                } else {
                    jQuery('#so_reg_no').val('');
                }
            },

        });

    }

}


// if (getItem.length) {

//     var productDrpHtml = `<option value="">Select Item</option>`;
//     for (let indx in getItem[0]) {

//         // productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;

//         productDrpHtml += `<option value="${getItem[0][indx].id}" data-item_code="${getItem[0][indx].item_code}" data-unit_name="${getItem[0][indx].unit_name}">
//         ${getItem[0][indx].item_name} </option>`;

//     }

// }

// if (getSalesFittingItem.length) {

//     var fittingProductDrpHtml = `<option value="">Select Item</option>`;
//     for (let indx in getSalesFittingItem[0]) {

//         // fittingProductDrpHtml += `<option value="${getSalesFittingItem[0][indx].id}">${getSalesFittingItem[0][indx].item_name} </option>`;

//         fittingProductDrpHtml += `<option value="${getSalesFittingItem[0][indx].id}" data-item_code="${getSalesFittingItem[0][indx].item_code}" data-unit_name="${getSalesFittingItem[0][indx].unit_name}">${getSalesFittingItem[0][indx].item_name} </option>`;



//     }


// }
// if (getFittingItem.length) {

//     var fittingProductDrpHtml = `<option value="">Select Item</option>`;
//     for (let indx in getFittingItem[0]) {

//         fittingProductDrpHtml += `<option value="${getFittingItem[0][indx].id}">${getFittingItem[0][indx].item_name} </option>`;

//     }


// }

// let sr_no = 1;

function addPartDetail() {

    // var sr = sr_no++;



    var thisHtml = `<tr>
                        <td><a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>

                        <td class="sr_no"></td>

                        <td>
                         <select name="item_id[]"  class="chzn-select  add_item item_id so_item_select_width" onChange="getItemData(this), sumSoQty(this)">${productDrpHtml}</select> 
                            <span class="eyeMargin"><a><i class="action-icon iconfa-eye-open eyeIcon1 d-none"></i></a></span>
                            <input type="hidden" name="check_fitting[]">
                            <input type="hidden" name="sales_order_detail_id[]" value="0">
                         </td>

                        <td id="code"></td>

                        

                        <td><input type="text" name="so_qty[]" id="so_qty" onblur="formatPoints(this,3)"  onKeyup="sumSoQty(this)" class="form-control isNumberKey so_qty " style="width:50px;"/></td>

                        <td id="unit"></td>`;
    if (getLocationType == "HO") {
        thisHtml += `<td><input type="text" name="rate_unit[]"  onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey"  onfocusout="formatPoints(this,2)" style="width:60px;"/></td>`;
    } else {
        thisHtml += `<td><input type="text" name="rate_unit[]"  onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey"  onfocusout="formatPoints(this,2)" style="width:60px;" tabindex="-1" readonly/></td>`;
    }
    thisHtml += ` <td><input type="text" name="discount[]" id="discount" onKeyup="Discount(this)" id="discount" class="form-control  discount  isNumberKey" maxlength="5" onblur="formatPoints(this,2)" style="width:50px;" value="0.00" /></td>

                        <td><input type="number" name="amount[]" id="amount" class="form-control amount " onblur="formatPoints(this,2)" tabindex="-1" style="width:70px;" readonly/></td>

                        <td>
                        <input type="text" name="remarks[]" id="remarks" class="form-control"/>
                        </td>        
                    </tr>`;


    jQuery('#soPartTable tbody').append(thisHtml);

    setTimeout(() => {
        srNo();
    }, 200);



    sumSoQty();

    totalAmount();
    disabledDropdownVal();
}


var Modalcount = 0;
// function addModalPartDetail(sod_details_data, id) {
function addModalPartDetail(id) {

    if (formId == undefined) {
        storeDetails = storeSalesOrderDetails.filter((value, index) => index == id)
        // console.log(storeDetails);
        if (storeDetails.length > 0) {

            var thisHtml = ``;
            for (let key in storeDetails) {

                for (let idx in storeDetails[key]) {
                    var counter = 1;
                    var sr_no = counter;
                    var sales_order_id = storeDetails[key][idx].sod_details_id ? storeDetails[key][idx].sod_details_id : "";
                    var item_id = storeDetails[key][idx].item_id ? storeDetails[key][idx].item_id : "";
                    var item_code = storeDetails[key][idx].item_code ? storeDetails[key][idx].item_code : "";
                    var item_group = storeDetails[key][idx].item_group_name ? storeDetails[key][idx].item_group_name : "";
                    var unit = storeDetails[key][idx].unit_name ? storeDetails[key][idx].unit_name : "";
                    var so_qty = storeDetails[key][idx].so_qty ? parseFloat(storeDetails[key][idx].so_qty).toFixed(3) : "";

                    thisHtml += `
                            <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="sales_order_detail_details_id_${idx}" id="sales_order_detail_details_id_${idx}" value="0"></td>

                            <td><input type="hidden" name="hitem_id[]" id="hitem_id_${idx}"</td></tr>
                            <tr>
                            
                            <td >
                            <a onclick="removeSoModelDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                            </td>

                            <td class="sr_no_second">${sr_no}</td>
                            <td> <select name="item_id[]" id="item_id_${idx}" class="chzn-select modal_item_id so_modal_item_select_width" onChange="getModalItemData(this), sumSoQty(this)" >${fittingProductDrpHtml}</select>

                            <td><input type="text" name="code_${idx}" id="code" value="${item_code}" class="form-control salesmanageTable" tabindex="-1" readonly/></td>
                          
                            
                            

                            <td><input type="text" name="so_qty[]" id="so_qty_${idx}"  onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey soqtysum_second" style="width:50px;" value="${so_qty}"/></td>

                               
                            <td><input type="text" name="unit_${idx}" id="unit" value="${unit}" class="form-control salesmanageTable" tabindex="-1" readonly/></td> </td>
                        </tr>`;
                    Modalcount++;
                }

            }

            setTimeout(() => {
                for (let key in storeDetails) {

                    for (let idx in storeDetails[key]) {
                        jQuery("#commonSalesOrderForm").find(`#item_id_${idx}`).val(storeDetails[key][idx].item_id).change();
                    }
                }
            }, 100);




        } else {

            Modalcount++;
            var thisHtml = `
            <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="sales_order_detail_details_id[]"  id="sales_order_detail_details_id_${Modalcount}" value="0"></td>
            <td><input type="hidden" name="hitem_id[]" id="hitem_id_${Modalcount}" />
            </td>      
            </tr>        
            <tr>
                <td>
                <a onclick="removeSoModelDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                </td>

                <td class="sr_no_second"></td>  

                <td><select name="item_id[]" id="item_id_${Modalcount}" class="chzn-select add_item modal_item_id so_modal_item_select_width" onChange="getModalItemData(this), sumSoQty(this)">${fittingProductDrpHtml}</select></td>

                <td><input type="text" name="code_${Modalcount}" id="code"  class="form-control salesmanageTable" tabindex="-1" readonly/></td>    

                 

                <td><input type="text" name="so_qty[]"  onblur="formatPoints(this,3)" id="so_qty_${Modalcount}"  onKeyup="sumSoQty(this)" class="form-control isNumberKey soqtysum_second" style="width:50px;"/></td>
                
                <td><input type="text" name="unit_${Modalcount}" id="unit"  class="form-control salesmanageTable" tabindex="-1" readonly/></td> 
            </tr>`;
        }

    } else {

        storeDetails = storeSalesOrderDetails.filter((value, index) => index == id)

        if (storeDetails.length > 0) {

            var thisHtml = ``;
            for (let key in storeDetails) {

                for (let idx in storeDetails[key]) {
                    var counter = 1;
                    var sr_no = counter;
                    var sales_order_id = storeDetails[key][idx].sod_details_id ? storeDetails[key][idx].sod_details_id : "";
                    var item_id = storeDetails[key][idx].item_id ? storeDetails[key][idx].item_id : "";
                    var item_code = storeDetails[key][idx].item_code ? storeDetails[key][idx].item_code : "";
                    var item_group = storeDetails[key][idx].item_group_name ? storeDetails[key][idx].item_group_name : "";
                    var unit = storeDetails[key][idx].unit_name ? storeDetails[key][idx].unit_name : "";
                    var so_qty = storeDetails[key][idx].so_qty ? parseFloat(storeDetails[key][idx].so_qty).toFixed(3) : "";

                    thisHtml += `
                            <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="sales_order_detail_details_id_${idx}" id="sales_order_detail_details_id_${idx}" value="${sales_order_id}" /></td>
                            <td><input type="hidden" name="hitem_id[]" id="hitem_id_${idx}"</td></tr>
                            <tr>
                            <td >
                            <a onclick="removeSoModelDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                            </td>
                            <td class="sr_no_second">${sr_no}</td>

                            <td> <select name="item_id[]" id="item_id_${idx}" class="chzn-select  modal_item_id so_modal_item_select_width" onChange="getModalItemData(this), sumSoQty(this)" >${fittingProductDrpHtml}</select>
                            
                            <td id="code">${item_code}</td>

                           
                            
                            <td><input type="text" name="so_qty[]" id="so_qty_${idx}"  onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey soqtysum_second" style="width:50px;" value="${so_qty}"/></td>

                           <td id="unit">${unit}</td>
                        </tr>`;

                    Modalcount++;
                }

            }

            setTimeout(() => {
                for (let key in storeDetails) {

                    for (let idx in storeDetails[key]) {
                        jQuery("#commonSalesOrderForm").find(`#item_id_${idx}`).val(storeDetails[key][idx].item_id).change();
                    }
                }
            }, 100);




        } else {

            Modalcount++;
            var thisHtml = `
            <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="sales_order_detail_details_id[]"  id="sales_order_detail_details_id_${Modalcount}" value="0"></td>
            <td><input type="hidden" name="hitem_id[]" id="hitem_id_${Modalcount}" />
            </td>      
            </tr>        
            <tr>
                <td>
                <a onclick="removeSoModelDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                </td>
                <td class="sr_no_second"></td> 

                <td><select name="item_id[]" id="item_id_${Modalcount}" class="chzn-select add_item modal_item_id so_modal_item_select_width" onChange="getModalItemData(this), sumSoQty(this)">${fittingProductDrpHtml}</select></td>   

                <td><input type="text" name="code_${Modalcount}" id="code"  class="form-control salesmanageTable" tabindex="-1" readonly/></td>    

               

                <td><input type="text" name="so_qty[]" id="so_qty_${Modalcount}"  onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey soqtysum_second" style="width:50px;"/></td> 

                <td><input type="text" name="unit_${Modalcount}" id="unit"  class="form-control salesmanageTable" tabindex="-1" readonly/></td>        
            </tr>`;
        }

    }


    jQuery('#soPartModalTable tbody').append(thisHtml);
    srNo();

    sumSoQty();

    totalAmount();
    disabledDropdownVal();
}



function getItemData(th) {
    let openModal = "no";

    // var selectedValue = jQuery(th).val();
    var selected = jQuery(th).val();

    var thisselected = jQuery(th);
    if (selected) {
        jQuery(jQuery('.item_id').not(jQuery(th))).each(function (index) {
            // openModal = "yes";

            if (thisselected.val() == jQuery(this).val()) {
                jAlert('This Item Is Already Selected.');
                var selectTd = thisselected.closest('td');

                selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id so_item_select_width" onChange="getItemData(this), sumSoQty(this)">${productDrpHtml}</select><span class="eyeMargin"><a><i class="action-icon iconfa-eye-open eyeIcon1 d-none"></i></a></span>
                <input type="hidden" name="check_fitting[]">`);
                // jQuery('.item_id').chosen();
                jQuery(".item_id").chosen({
                    search_contains: true
                });
                openModal = "yes";
            }
        });
    }



    let item = th.value == undefined ? selected : th.value;

    var customerGroup = jQuery('#customer_group_id option:selected').val();

    jQuery(th).parents('tr').find("#code").text(jQuery(th).find('option:selected').data('item_code')); // Enable the input field
    jQuery(th).parents('tr').find("#unit").text(jQuery(th).find('option:selected').data('unit_name'));// Enable the input field
    jQuery(th).parents('tr').find("#item_id").val(item);

    jQuery("#customer_group_id").prop({ tabindex: -1 }).attr('readonly', true);



    var itemUrl = formId != undefined && formId != '' ? RouteBasePath + "/get-item_data?item=" + item + "&id=" + formId + "&customerGroup=" + customerGroup : RouteBasePath + "/get-item_data?item=" + item + "&customerGroup=" + customerGroup;


    if (item != "" && item != null) {
        jQuery.ajax({


            url: itemUrl,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {
                if (data.response_code == 1) {

                    // jQuery(th).closest('tr').find("#code").val(data.item.item_code);
                    // jQuery(th).closest('tr').find("#item_id").val(data.item.id);
                    // jQuery(th).closest('tr').find("#group").val(data.item.item_group_name);
                    // jQuery(th).closest('tr').find("#unit").val(data.item.unit_name);
                    if (formId == undefined) {
                        if (data.item != '' && data.item != undefined) {
                            if (data.item.sales_rate != '' && data.item.sales_rate != undefined) {
                                jQuery(th).closest('tr').find("#rate_unit").val(data.item.sales_rate.toFixed(2));
                            }
                        }

                    } else {
                        if (jQuery(th).closest('tr').find("input[name='sales_order_detail_id[]']").val() == 0) {
                            if (data.item != '' && data.item != undefined) {
                                if (data.item.sales_rate != '' && data.item.sales_rate != undefined) {
                                    jQuery(th).closest('tr').find("#rate_unit").val(data.item.sales_rate.toFixed(2));
                                }
                            }
                        }
                    }
                    // console.log(fittingProductDrpHtml)
                    var icon = jQuery(th).closest('tr').find(".eyeIcon1");

                    if (data.IffittingItem.length > 0) {

                        if (openModal == "no") {
                            jQuery('#soPartModalTable tbody').empty();

                            // addModalPartDetail(data.IffittingItem, data.item.id);
                            // addModalPartDetail(data.item.id);

                            if (th.value != undefined) {
                                if (storeSalesOrderDetails[item] != undefined) {
                                    jConfirm('Do you want <lw-c>to</lw-c> Reset Fitting Item Details?', 'ItemConfirmation', function (r) {

                                        if (r === true) {
                                            addModalPartDetail();
                                        } else {
                                            addModalPartDetail(item);
                                        }
                                    });

                                } else {
                                    addModalPartDetail(item);

                                }
                            } else {
                                addModalPartDetail(item);
                            }



                            jQuery("#commonSalesOrderForm").find('#mainItemId').val(item);

                            jQuery("#salesOrderModal").modal('show');
                            icon.removeClass('d-none');
                            jQuery(th).closest('tr').find("input[name='check_fitting[]']").val('Yes');

                            // jQuery(th).closest('tr').find("#so_qty").val("1.000").prop({ tabindex: -1, readonly: true });

                            var rate_unit = jQuery(th).closest('tr').find("#rate_unit").val();

                            if (rate_unit != '') {
                                var total_rate = parseFloat(1) * parseFloat(rate_unit);
                                jQuery(th).closest('tr').find("#amount").val(total_rate);
                            }

                            // jQuery(".modal_item_id").chosen();
                            jQuery(".modal_item_id").chosen({
                                search_contains: true
                            });

                            // jQuery(th).find(".add_item").append('<i class="action-icon iconfa-trash"></i>');


                        }
                        else {
                            jQuery("#salesOrderModal").modal('hide');
                        }
                        sumSoQty();
                        soRateUnit(jQuery(th));
                        jQuery(th).closest('tr').find("#rate_unit").attr('readonly', false);
                    }
                    else {
                        if (getLocationType != "HO") {
                            jQuery(th).closest('tr').find("#rate_unit").attr('readonly', true);
                        }
                        jQuery('#isFitting').val(0);
                        icon.addClass('d-none');
                        jQuery(th).closest('tr').find("#so_qty").val('').prop("readonly", false);
                        sumSoQty();
                        jQuery(th).closest('tr').find("input[name='check_fitting[]']").val('No');
                        // jQuery(th).closest('tr').find("#so_qty").prop({ tabindex: 1});



                        // jQuery(th).closest('tr').find("#so_qty").val('').prop({ tabindex: -1, readonly: false });


                    }






                } else {

                    jQuery(th).closest('tr').find("#code").text('');
                    jQuery(th).closest('tr').find("#unit").text('');
                    jQuery('#item_id').val('');
                    jQuery('#group').val('');
                    // jQuery('#unit').text('');
                }
            }

        });
    } else {
        jQuery(th).closest('tr').find("#code").text('');
        jQuery(th).closest('tr').find("#unit").text('');
    }

}


function getModalItemData(th) {

    let item = th.value;


    var selected = jQuery(th).val();

    var thisselected = jQuery(th);

    if (selected) {
        jQuery(jQuery('.modal_item_id').not(jQuery(th))).each(function (index) {

            if (thisselected.val() == jQuery(this).val()) {
                jAlert('This Item Is Already Selected.');
                var selectTd = thisselected.closest('td');

                var pre_id = jQuery(jQuery(selectTd).find('select')[0]).attr('id');

                selectTd.html(`<select name="item_id[]" id="${pre_id}" class="chzn-select add_item modal_item_id so_modal_item_select_width" onChange="getModalItemData(this), sumSoQty(this)">${productDrpHtml}</select><span class="eyeMargin"><a><i class="action-icon iconfa-eye-open eyeIcon1 d-none"></i></a></span>
                <input type="hidden" name="check_fitting[]">`);
                // jQuery('.modal_item_id').chosen();
                jQuery(".modal_item_id").chosen({
                    search_contains: true
                });
            }
        });
    }


    if (item != "" && item != null) {
        jQuery(th).parents('tr').find("#code").val(jQuery(th).find('option:selected').data('item_code')); // Enable the input field
        jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit_name'));// Enable the input field
        jQuery(th).parents('tr').find("#group").val(jQuery(th).find('option:selected').data('item_group')); // Enable the input field
        jQuery(th).parents('tr').find("#item_id").val(item);

        jQuery(th).closest('tr').find(".modal_item_id").val(item).trigger('liszt:updated');
        jQuery(th).closest('tr').prev('tr').find('input[name="hitem_id[]"]').val(item)
    }

    // if (item != "" && item != null) {
    //     jQuery.ajax({

    //         url: RouteBasePath + "/get-item_data?item=" + item,

    //         type: 'GET',

    //         headers: headerOpt,

    //         dataType: 'json',

    //         processData: false,

    //         success: function (data) {
    //             if (data.response_code == 1) {

    //                 jQuery(th).closest('tr').find("#code").val(data.item.item_code);
    //                 //jQuery(th).closest('tr').find("#item_id").val(data.item.id);
    //                 jQuery(th).closest('tr').find(".modal_item_id").val(data.item.id).trigger('liszt:updated');
    //                 jQuery(th).closest('tr').find("#group").val(data.item.item_group_name);
    //                 jQuery(th).closest('tr').find("#unit").val(data.item.unit_name);
    //                 jQuery(th).closest('tr').prev('tr').find('input[name="hitem_id[]"]').val(data.item.id)
    //                 // // console.log(jQuery(th).closest('tr').prev('tr').html());
    //                 // // console.log(jQuery(th).closest('tr').prev('tr').find('input[name="hitem_id[]"]'));
    //                 // if (jQuery(th).closest('tr').prev('tr').find('input[name="hitem_id[]"]')) {
    //                 //     jQuery(th).closest('tr').prev('tr').find('input[name="hitem_id[]"]').val(data.item.id)
    //                 // }
    //                 // console.log(jQuery(th).prev());


    //             } else {
    //                 jQuery('#code').val('');
    //                 jQuery('#item_id').val('');
    //                 jQuery('#group').val('');
    //                 jQuery('#unit').val('');
    //             }
    //         },

    //     });
    // }

}


// edit modal code
function getSalesOrderDetails(id) {

    var id = id.value;


    if (id != "" && id !== undefined) {
        return jQuery.ajax({

            url: RouteBasePath + "/get-item_name?item_id=" + id,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    if (!jQuery.isEmptyObject(data.salesOrderDetailsDetailsData) && data.salesOrderDetailsDetailsData.length > 0) {



                        var thisHtml;
                        for (let key in data.salesOrderDetailsDetailsData) {

                            var sod_details_id = data.salesOrderDetailsDetailsData[key].sod_details_id ? data.salesOrderDetailsDetailsData[key].sod_details_id : "";




                            thisHtml += `<tr style="display:none;">
                                            <td class="colspan=10"><input type="hidden" name="sales_order_detail_details_id_${key}" id="sales_order_detail_details_id_${key}" value="${sod_details_id}"></td>
                                        </tr>                              
                                        <tr>
                                            <td><a onclick="removeSoModelDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>       </td>                        
                                            <td class="sr_no_second"></td>                    
                                            <td> <select name="item_id" id="item_id_${key}" class="chzn-select chzn-done modal_item_id add_item item_id" onChange="getItemData(this)">${fittingProductDrpHtml}</select></td>               
                                            <td><input type="text" name="code[]" id="code_${key}"  class="form-control salesmanageTable" tabindex="-1" value="${data.salesOrderDetailsDetailsData[key].item_code}" readonly/></td>                        
                                            <td><input type="text" name="so_qty" id="so_qty_${key}"  onKeyup="sumSoQty(this)" class="form-control allow-desimal soqtysum_second" value="${data.salesOrderDetailsDetailsData[key].so_qty != null ? parseFloat(data.salesOrderDetailsDetailsData[key].so_qty).toFixed(3) : ''}" style="width:50px;"/></td>                
                                        </tr>`;
                            setTimeout(() => {
                                jQuery("#commonSalesOrderForm").find(`#item_id_${key}`).val(data.salesOrderDetailsDetailsData[key].item_id).trigger('liszt:updated');
                            }, 100);


                        }



                        jQuery('#soPartModalTable tbody').empty().append(thisHtml);

                        srNo();



                        sumSoQty();

                        totalAmount();

                        disabledDropdownVal();

                        jQuery("#salesOrderModal").modal('show');

                    }
                } else {
                    jQuery('#so_district_id').empty().append("<option value=''>Select District</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function sumSoQty(th) {

    var total = 0;
    var total2 = 0;
    jQuery('.so_qty').map(function () {

        var total1 = jQuery(this).val();

        if (total1 != "") {
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    jQuery('.soqtysum_second').map(function () {
        var total4 = jQuery(this).val();
        if (total4 != "") {

            total2 = parseFloat(total2) + parseFloat(total4);
        }
    });

    total != 0 && total != "" ? jQuery('.soqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.soqtysum').text('');


    total2 != 0 && total2 != "" ? jQuery('.soqtysum_second').text(parseFloat(total2).toFixed(3)) : jQuery('.soqtysum_second').text('');


    // console.log(total2)
    // jQuery("#commonSalesOrderForm").find('.soqtysum_second').text(total2);


    if (jQuery(th).closest('tr').length > 0) {
        // soRateUnit(jQuery(th).closest('tr'))
        soRateUnit(th)
    }
}

function soRateUnit(th) {

    let so_qty = jQuery(th).closest('tr').find("#so_qty").val();

    let rateUnit = jQuery(th).closest('tr').find("#rate_unit").val();


    var soUnit = 0;
    if (rateUnit >= 0) {
        jQuery("#basic_amount").val(parseFloat(0).toFixed(2));
    }
    if (rateUnit != "" && so_qty != "") {
        soUnit = parseFloat(so_qty) * parseFloat(rateUnit);
    }

    if (soUnit != 0) {
        jQuery(th).closest('tr').find("#amount").val(formatAmount(soUnit));
    } else if (rateUnit == "") {
        jQuery(th).closest('tr').find("#amount").val('');

    } else {
        jQuery(th).closest('tr').find("#amount").val(0);
    }

    Discount(th);

    totalAmount()
}



function totalAmount() {
    var total_amount = 0;
    jQuery('.amount').map(function () {
        var amount = jQuery(this).val();

        if (amount != "") {

            total_amount = parseFloat(total_amount) + parseFloat(amount);
        }

    });

    if (total_amount != 0) {
        jQuery('.amountsum').text(formatAmount(total_amount));
        jQuery('#basic_amount').val(formatAmount(total_amount));
        jQuery('#net_amount').val(formatAmount(total_amount));
    } else if (amount != 0) {
        jQuery('.amountsum').text('');
        jQuery('#net_amount').val('');
    } else {
        jQuery('.amountsum').text(0);
        jQuery('#net_amount').val(0);
    }

    calcLessDiscount();
    calcGstAmount();

}



function removeSoDetails(th) {

    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        // let checkLength = jQuery("#soPartTable tbody tr").filter(function () {
        //     return jQuery(this).css('display') !== 'none';
        // }).length;

        // if (checkLength > 1) {
        if (r === true) {

            let soPartId = jQuery(th).closest('tr').find('input[name="sales_order_detail_id[]"]').val();

            if (soPartId != '') {
                jQuery.ajax({
                    url: RouteBasePath + "/check-so_part_in_use?so_part_id=" + soPartId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        jQuery(th).removeClass('file-loader');
                        if (data.response_code == 1) {
                            toastError(data.response_message);
                        } else {
                            jQuery(th).closest("tr").remove();
                            var so_qty = jQuery(th).closest('tr').find('#so_qty').val();
                            var so_amt = jQuery(th).closest('tr').find('#amount').val();
                            // if (so_qty != "" && so_amt != "") {
                            if (so_qty != "" || so_amt != "") {

                                var so_total = jQuery('.soqtysum').text();
                                // var so_total_second = jQuery('.soqtysum_second').text();
                                var amt_total = jQuery('.amountsum').text();
                                if (so_total != "" || amt_total != "") {
                                    so_final_total = parseFloat(so_total) - parseFloat(so_qty);
                                    amt_final_total = parseFloat(amt_total) - parseFloat(so_amt);
                                }
                                so_final_total > 0 ? jQuery('.soqtysum').text(parseFloat(so_final_total).toFixed(3)) : jQuery('.soqtysum').text('');

                                amt_final_total > 0 ? jQuery('.amountsum').text(parseFloat(amt_final_total).toFixed(3)) : jQuery('.amountsum').text('');

                                // jQuery('.soqtysum').text(parseFloat(so_final_total).toFixed(3));
                                // jQuery('.amountsum').text(parseFloat(amt_final_total).toFixed(2));

                                srNo();

                                totalAmount();
                            }
                        }


                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        jQuery(th).removeClass('file-loader');
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

            } else {


                jQuery(th).closest("tr").remove();
                var so_qty = jQuery(th).closest('tr').find('#so_qty').val();
                var so_amt = jQuery(th).closest('tr').find('#amount').val();

                if (so_qty != "" && so_amt != "") {

                    var so_total = jQuery('.soqtysum').text();
                    // var so_total_second = jQuery('.soqtysum_second').text();
                    var amt_total = jQuery('.amountsum').text();
                    if (so_total != "" && amt_total != "") {
                        so_final_total = parseInt(so_total) - parseInt(so_qty);
                        amt_final_total = parseInt(amt_total) - parseInt(so_amt);
                    }

                    so_final_total > 0 ? jQuery('.soqtysum').text(parseFloat(so_final_total).toFixed(3)) : jQuery('.soqtysum').text('');

                    amt_final_total > 0 ? jQuery('.amountsum').text(parseFloat(amt_final_total).toFixed(3)) : jQuery('.amountsum').text('');

                    // jQuery('.soqtysum').text(parseFloat(so_final_total).toFixed(3));
                    // jQuery('.amountsum').text(parseFloat(amt_final_total).toFixed(2));

                    srNo();

                    totalAmount();
                }
            }
        }



        var itemsId = jQuery(th).closest('tr').find('#item_id').val();
        if (itemsId) {
            for (let idx in storeSalesOrderDetails[itemsId]) {
                storeSalesOrderDetails[itemsId][idx].item_id = undefined;
                storeSalesOrderDetails[itemsId][idx].mitem_id = undefined;
                storeSalesOrderDetails[itemsId][idx].so_qty = undefined;
            }
        }
        // } else {
        //     jAlert("Please At Least Sales Order Detail Item Required");
        // }





    });
}






function removeSoModelDetails(th) {
    // console.log(storeSalesOrderDetails);
    // console.log(th)
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {


        if (r === true) {

            jQuery(th).closest("tr").remove();
            var so_qty = jQuery(th).closest('tr').find('input[name="so_qty[]"]').val();

            if (so_qty) {

                //var so_total_second = jQuery('.soqtysum_second').text();
                var so_total_second = jQuery('#soPartModalTable tfoot').find('.soqtysum_second').text();
                // console.log(so_total_second)


                if (so_total_second != "") {

                    so_final_total_second = parseInt(so_total_second) - parseInt(so_qty);
                }
            }

            so_final_total_second > 0 ? jQuery('.soqtysum_second').text(parseFloat(so_final_total_second).toFixed(3)) : jQuery('.soqtysum_second').text('');

            // jQuery('#soPartModalTable tfoot').find('.soqtysum_second').text(parseFloat(so_final_total_second).toFixed(3));
        }


        srNo();

    });
}




function srNo() {

    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
    jQuery('.sr_no_second').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
    // jQuery(".item_id").chosen();

    jQuery(".item_id").chosen({
        search_contains: true
    });
    // jQuery(".modal_item_id").chosen();
    jQuery(".modal_item_id").chosen({
        search_contains: true
    });




}
// check duplication for so no
jQuery('#so_sequence').on('change', function () {

    let thisForm = jQuery('#salesorderform');
    let val = jQuery(this).val();


    var subBtn = jQuery(document).find('.stdform').find('.stdformbutton button').text();



    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.stdformbutton button');

    }

    if (val != "") {


        if (val > 0 == false) {

            jAlert('Please Enter Valid Sales Order No.');
            jQuery('#so_sequence').parent().parent().parent('div.control-group').addClass('error');
            // jQuery('#so_sequence').focus();
            jQuery('#so_sequence').val('');

        } else {
            jQuery(subBtn).prop('disabled', true);
            jQuery('#so_sequence').addClass('file-loader');
            jQuery('#so_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-so_no_duplication?for=add&so_sequence=" + val;

            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-so_no_duplication?for=edit&so_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#so_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {

                        toastError(data.response_message);
                        jQuery('#so_sequence').parent().parent().parent('div.control-group').addClass('error');
                        // jQuery('#so_sequence').focus();
                        jQuery('#so_sequence').val('');

                    } else {

                        jQuery('#so_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#so_no').val(data.latest_po_no);
                        jQuery('#so_sequence').val(val);
                    }
                    jQuery(subBtn).prop('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#so_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#so_no').val('');
        jQuery('#so_sequence').val('');
    }

});
// end duplication for so no

// jQuery(document).on('change', '.item_id', function (e) {

//     var selected = jQuery(this).val();
//     var thisselected = jQuery(this);
//     if (selected) {
//         jQuery(jQuery('.item_id').not(jQuery(this))).each(function (index) {
//             openModal = "yes";

//             if (thisselected.val() == jQuery(this).val()) {
//                 jAlert('This Item Is Already Selected.');
//                 thisselected.replaceWith(`<select name="item_id[]" id="item_id" class="chzn-select chzn-done add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select>`);
//             }
//             else {
//                 // console.log(2);
//                 openModal = "no";
//             }
//         });
//     }
// });


// jQuery(document).on('change', '.modal_item_id', function (e) {

//     var selected = jQuery(this).val();
//     var thisselected = jQuery(this);

//     if (selected) {
//         jQuery(jQuery('.modal_item_id').not(jQuery(this))).each(function (Modalcount) {
//             Modalcount++;
//             if (thisselected.val() == jQuery(this).val()) {
//                 jAlert('This Item Is Already Selected.');

//                 thisselected.replaceWith(` <select name="item_id[]" id="item_id_${Modalcount}" class="chzn-select chzn-done add_item modal_item_id" onChange="getModalItemData(this), sumSoQty(this)">${fittingProductDrpHtml}</select>`);
//             }
//         });
//     }
// });



function getSoStates(event) {
    let stateIdVal = jQuery('#so_country_id option:selected').val();

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
                    jQuery('#so_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    //    jQuery('#taluka_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    //    console.log(dropHtml);

                } else {
                    jQuery('#so_state_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getSoDistrict(event) {
    let districtVal = jQuery('#so_state_id option:selected').val();

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
                    jQuery('#so_district_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#so_district_id').empty().append("<option value=''>Select District</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getSoTaluka(event) {
    let talukaVal = jQuery('#so_district_id option:selected').val();
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
                            // dropHtml += `<option value="${data.taluka[idx].taluka_name}">${data.taluka[idx].taluka_name}</option>`;
                        }
                    }
                    jQuery('#so_taluka_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#so_taluka_id').empty().append("<option value=''>Select Taluka</option>").trigger('liszt:updated');
                }
            },
        });
    }
}


function getSoVillage(event) {
    let villageIdVal = jQuery('#so_taluka_id option:selected').val();
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
                    jQuery('#customer_village').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#customer_village').empty().append("<option value=''>Select Village</option>").trigger('liszt:updated');
                }
            },
        });
    }
}





jQuery('#so_sequence').on('change', function () {
    let val = jQuery(this).val();


    var subBtn = jQuery(document).find('.stdform').find('.formwrapper button').text();



    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.formwrapper button');
    }


    if (val != undefined) {

        if (val > 0 == false) {
            jAlert('Please Enter Valid SO No.');
            jQuery('#so_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
            });
            jQuery('#so_sequence').val('');

        } else {
            jQuery(subBtn).prop('disabled', true);


            jQuery('#so_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-so_no_duplication?for=add&so_sequence=" + val;

            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-so_no_duplication?for=edit&so_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#so_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {

                        toastError(data.response_message);
                        jQuery('#so_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                        });
                        jQuery('#so_sequence').val('');

                    } else {

                        jQuery('#so_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#so_no').val(data.latest_po_no);
                        jQuery('#so_sequence').val(val);
                    }
                    jQuery(subBtn).prop('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#so_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#so_no').val('');
        jQuery('#so_sequence').val('');
    }
});




function soTypeFix() {
    var thisModal = jQuery('#pendingMaterialRequest');
    var thisForm = jQuery('#salesorderform');

    var typeFixId = jQuery('input[name="so_type_id_fix"]:checked').val();

    if (typeFixId == '1') {
        if (jQuery('input[name="so_from_id_fix"]:checked').val() == 3) {
            jQuery('div#btn_hide').show();
            jQuery('#addPart').prop('disabled', true);

            // if (thisForm !== undefined) {
            if (formId !== undefined) {
                // console.log("if");
                setTimeout(() => {
                    jQuery(".pmrbutton").last().focus();
                }, 100);
            }
            else {
                // console.log("else");
                setTimeout(() => {
                    jQuery("#so_location_id").trigger('liszt:activate');
                }, 100);
            }
        } else {
            jQuery('div#btn_hide').hide();
            jQuery('#addPart').prop('disabled', false);
            jQuery("#salesorderform").find('#replace_btn').hide();
            jQuery('#soPartTable tbody').empty();

            if (formId == undefined) {
                addPartDetail();
            }

            // productDrpHtml = `<option value="">Select Item</option>`;
            // for (let indx in getItem[0]) {
            //     // productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;

            //     productDrpHtml += `<option value="${getItem[0][indx].id}" data-item_code="${getItem[0][indx].item_code}" data-unit_name="${getItem[0][indx].unit_name}">
            //     ${getItem[0][indx].item_name} </option>`;

            // }
            // jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
        }

        jQuery("#salesorderform").find('#replacement_show').hide();
        jQuery("#salesorderform").find('#replacement_btn_show').hide();

        jQuery("#salesorderform").find('#customer_name_show').show();
        jQuery("#salesorderform").find('#rep_customer_id_show').hide();


        if (formId == undefined) {
            jQuery('#customer_group_id').attr('readonly', false).val('').trigger('liszt:updated');
            jQuery('#dealer_id').attr('readonly', false).val('').trigger('liszt:updated');
            jQuery('#customer_reg_no').attr('readonly', false).val('');
            jQuery('#customer_village').attr('readonly', false).val('').trigger('liszt:updated');
            jQuery('#customer_pincode').attr('readonly', false).val('');
            jQuery('#so_country_id').attr('readonly', false).val('').trigger('liszt:updated');
            jQuery('#so_state_id').attr('readonly', false).val('').trigger('liszt:updated');
            jQuery('#so_district_id').attr('readonly', false).val('').trigger('liszt:updated');
            jQuery('#so_taluka_id').attr('readonly', false).val('').trigger('liszt:updated');
            jQuery('#mis_category_id').attr('readonly', false).val('').trigger('liszt:updated');
            jQuery('#so_mobile_no').attr('readonly', false).val('');
            jQuery('#area').attr('readonly', false).val('');
            jQuery('#ship_to').attr('readonly', false).val('');

            getCountyandStateForLocation();

            jQuery("#customer_group_id").trigger('liszt:activate');



        } else {
            jQuery("#customer_name").focus();
        }



    } else {
        setTimeout(() => {
            jQuery("#rep_customer_id").trigger('liszt:activate');
        }, 100);

        jQuery('div#btn_hide').hide();
        jQuery('#addPart').prop('disabled', false);
        jQuery("#salesorderform").find('#replace_btn').show();
        jQuery("#salesorderform").find('#addPart').hide();







        if (formId != undefined) {
            jQuery('#customer_group_id').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#dealer_id').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#customer_reg_no').prop({ tabindex: -1, readonly: true });
            jQuery('#customer_village').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#customer_pincode').prop({ tabindex: -1, readonly: true });
            jQuery('#so_country_id').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#so_state_id').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#so_district_id').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#so_taluka_id').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#mis_category_id').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#so_mobile_no').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#area').prop({ tabindex: -1 }).attr('readonly', true);
            jQuery('#ship_to').prop({ tabindex: -1 }).attr('readonly', true);


        } else {
            jQuery('#customer_group_id').prop({ tabindex: -1 }).attr('readonly', true).val('').trigger('liszt:updated');
            jQuery('#dealer_id').prop({ tabindex: -1 }).attr('readonly', true).val('').trigger('liszt:updated');
            jQuery('#customer_reg_no').prop({ tabindex: -1, readonly: true }).val('');
            jQuery('#customer_village').prop({ tabindex: -1 }).attr('readonly', true).trigger('liszt:updated');
            jQuery('#customer_pincode').prop({ tabindex: -1, readonly: true }).val('');
            jQuery('#so_country_id').prop({ tabindex: -1 }).attr('readonly', true).trigger('liszt:updated').val('');
            jQuery('#so_state_id').prop({ tabindex: -1 }).attr('readonly', true).trigger('liszt:updated').val('');
            jQuery('#so_district_id').prop({ tabindex: -1 }).attr('readonly', true).trigger('liszt:updated').val('');
            jQuery('#so_taluka_id').prop({ tabindex: -1 }).attr('readonly', true).trigger('liszt:updated').val('');
            jQuery('#mis_category_id').prop({ tabindex: -1 }).attr('readonly', true).trigger('liszt:updated').val('');
            jQuery('#so_mobile_no').prop({ tabindex: -1, readonly: true }).val('');
            jQuery('#area').prop({ tabindex: -1, readonly: true }).val('');
            jQuery('#ship_to').prop({ tabindex: -1, readonly: true }).val('');


            jQuery('#rep_customer_id').val('').trigger('liszt:updated');

            jQuery('#pre_so_no').val('').trigger('liszt:updated');

        }




        jQuery('#soPartTable tbody').empty();

        //     if (formId == undefined) {
        //         addPartDetail();
        //     }


        //     productDrpHtml = `<option value="">Select Item</option>`;
        //     for (let indx in getSalesFittingItem[0]) {
        //         productDrpHtml += `<option value="${getSalesFittingItem[0][indx].id}">${getSalesFittingItem[0][indx].item_name} </option>`;
        //     }

        //     jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');

        jQuery("#salesorderform").find('#replacement_show').show();
        jQuery("#salesorderform").find('#replacement_btn_show').show();

        jQuery("#salesorderform").find('#customer_name_show').hide();
        jQuery("#salesorderform").find('#rep_customer_id_show').show();

        getOldCustomer();
    }
}




jQuery('#pendingMaterialRequest').on('show.bs.modal', function (e) {
    var usedParts = [];
    var totalDisb = 0;

    jQuery('#soPartTable tbody input[name="form_indx"]').each(function (indx) {
        let frmIndx = jQuery(this).val();
        let woId = material_data[frmIndx].mr_id;
        if (woId != "" && woId != null) {
            usedParts.push(Number(woId));
        }
    });

    function isUsed(pjId) {
        if (usedParts.includes(Number(pjId))) {
            totalDisb++;
            return true;
        }
        return false;
    }

    let totalEntry = 0;

    jQuery('#pendingMaterialRequestTable tbody tr').each(function (indx) {

        totalEntry++;
        let checkField = jQuery(this).find('input[name="mr_id[]"]');
        let partId = jQuery(checkField).val();
        let inUse = isUsed(partId);
        // console.log(partId)
        if (inUse) {
            jQuery(checkField).addClass('in-use').prop('checked', true);

        } else {
            jQuery(checkField).removeClass('in-use').prop('checked', false);
        }

    });

    // if (totalDisb == totalEntry) {
    //     jQuery('#pendingMaterialRequestModal').prop('disabled', true);
    // } else {
    //     jQuery('#pendingMaterialRequestModal').prop('disabled', false);
    // }
    setTimeout(() => {
        jQuery(this).find('#checkall-material').focus();
    }, 300);
});






var validator = jQuery("#pendingMaterialRequestForm").validate({
    rules: {
        "mr_id[]": {
            required: true
        },
    },
    messages: {
        "mr_id[]": {
            required: "Please Select Material Request",
        },

    },
    submitHandler: function (form) {

        var chkCount = 0;
        var chkArr = [];
        var chkId = [];
        jQuery("#pendingMaterialRequestForm").find("[id^='mr_ids_']").each(function () {
            let thisId = jQuery(this).attr('id');
            let splt = thisId.split('mr_ids_');
            let intId = splt[1];

            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });
        if (chkCount == 0) {
            // jQuery('#material_ids_' + chkId[0]).parent('td').addClass('error');
            toastError('Please Select Pending  Material Request');

        } else {
            // jQuery('#material_ids_' + chkId[0]).parent('td').removeClass('error');

            // if (formId == undefined) {

            //     var url = RouteBasePath + "/get-material_parts_data-so?materialids=" + chkArr.join(',');
            // } else {
            //     var url = RouteBasePath + "/get-material_parts_data-so?materialids=" + chkArr.join(',') + "&id=" + formId;
            // }
            if (formId == undefined) {

                var url = RouteBasePath + "/get-material_parts_data-so?materialids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-material_parts_data-so?materialids=" + chkArr.join(',') + "&id=" + formId;
            }

            jQuery.ajax({

                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.material_data.length > 0 && !jQuery.isEmptyObject(data.material_data)) {
                            material_data = [];

                            for (let ind in data.material_data) {

                                material_data.push(data.material_data[ind]);

                            }
                            fillPendingMaterialTable(data.material_data);

                            if (formId == undefined) {
                                jQuery('#special_notes').val(data.sp_note.special_notes);
                            }
                        }
                        jQuery("#pendingMaterialRequest").modal('hide');
                        jQuery("#so_location_id").prop({ tabindex: -1 }).attr('readonly', true);

                    } else {


                        toastError(data.response_message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
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

    }

});



jQuery('#checkall-material').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#pendingMaterialRequestForm").find("[id^='material_ids_']:not(.in-use)").prop('checked', true).trigger('change');
    } else {
        jQuery("#pendingMaterialRequestForm").find("[id^='material_ids_']:not(.in-use)").prop('checked', false).trigger('change');
    }
});



function fillPendingMaterialTable() {
    if (material_data.length > 0) {
        // jQuery('#soPartTable tbody').empty();
        var thisHtml = '';
        var counter = 1;
        for (let key in material_data) {

            var formIndx = key;
            var sr_no = counter;
            var sales_order_id = material_data[key].so_details_id ? material_data[key].so_details_id : 0;

            var item_id = material_data[key].item_id ? material_data[key].item_id : "";
            var item_name = material_data[key].item_name ? material_data[key].item_name : "";
            var item_code = material_data[key].item_code ? material_data[key].item_code : "";
            var item_group_name = material_data[key].item_group_name ? material_data[key].item_group_name : "";
            var so_qty = material_data[key].so_qty ? parseFloat(material_data[key].so_qty).toFixed(3) : 0;
            var unit_name = material_data[key].unit_name ? material_data[key].unit_name : "";
            var rate_per_unit = material_data[key].rate_per_unit ? parseFloat(material_data[key].rate_per_unit).toFixed(2) : "";
            var fitting_item = material_data[key].fitting_item ? material_data[key].fitting_item : "";
            var mr_details_id = material_data[key].mr_details_id ? material_data[key].mr_details_id : "";
            var mr_id = material_data[key].mr_id ? material_data[key].mr_id : "";
            if (formId == undefined) {
                var mr_qty = material_data[key].mr_qty ? parseFloat(material_data[key].mr_qty).toFixed(3) : 0;
                var so_amount = rate_per_unit != '' ? so_qty * rate_per_unit : "";

            } else {
                var mr_qty = material_data[key].so_qty ? parseFloat(material_data[key].so_qty).toFixed(3) : 0;
                var so_amount = material_data[key].so_amount ? material_data[key].so_amount : "";

            }
            // var mr_qty = material_data[key].so_qty ? parseFloat(material_data[key].so_qty).toFixed(3) : 0;

            var totalMrQty = material_data[key].so_qty ? parseFloat(material_data[key].so_qty).toFixed(3) : 0;

            var remarks = material_data[key].remarks ? material_data[key].remarks : "";

            var discount = material_data[key].discount ? material_data[key].discount.toFixed(2) : (0).toFixed(2);



            thisHtml += `<tr>`;
            if (formId == undefined) {
                thisHtml += `<td><a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>`;

            } else {
                if (material_data[key].in_use == true) {
                    //thisHtml += `<td><a><i class="action-icon iconfa-trash so_details"></i></a></td>`;
                    thisHtml += `<td></td>`;
                } else {
                    thisHtml += `<td><a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>`;
                }
            }


            thisHtml += `<td class="sr_no"></td>          
                    <td class="so_mr_item"> 
                        <input type="hidden" name="sales_order_detail_id[]" value="${sales_order_id}">
                        <input type="hidden" name="form_indx" value="${formIndx}"/>
                        <input type="hidden" name="mr_id[]" value="${mr_id}"/>
                        <input type="hidden" name="mr_details_id[]" value="${mr_details_id}"/>                 
                        <input type="hidden" name="item_id[]" id="item_id" class="form-control" readonly tabindex="-1" value="${item_id}"/>${item_name}               
                    </td>              
                    <td id="code">
                       ${item_code}
                    </td>         
                                            
                    <td>
                        <input type="text" name="so_qty[]" id="so_qty"  onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)"  class="form-control isNumberKey so_qty" style="width:50px;" value="${mr_qty}" tabindex="-1"  readonly max="${totalMrQty}"/>
                    </td >                
                    <td id="unit">
                    ${unit_name}
                    </td>`;
            if (getLocationType == "HO") {
                thisHtml += ` <td>
                        <input type="text" name="rate_unit[]" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey" onblur="formatPoints(this,2)" data-rate="${rate_per_unit}"  value="${rate_per_unit}"${mr_qty == material_data[key].used_qty ? "readonly tabindex ='-1'" : ""} style="width:60px;">
                    </td>`;
            } else {
                thisHtml += ` <td>
                        <input type="text" name="rate_unit[]" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey" onblur="formatPoints(this,2)" data-rate="${rate_per_unit}"  value="${rate_per_unit}"${mr_qty == material_data[key].used_qty ? "readonly tabindex ='-1'" : ""} style="width:60px;"readonly>
                    </td>`;

            }
            thisHtml += `  <td><input type="text" name="discount[]" id="discount" onKeyup="Discount(this)" id="discount" class="form-control  discount  isNumberKey" maxlength="5" value="${discount}" onblur="formatPoints(this,2)" style="width:50px;" /></td>
                    
                    <td>
                        <input type="number" name="amount[]" id="amount" class="form-control amount " onblur="formatPoints(this,2)" tabindex="-1" value="${formatAmount(so_amount)}" readonly tabindex="-1" style="width:70px;"/>
                    </td>        
                    <td>
                        <input type="text" name="remarks[]" id="remarks" class="form-control" value="${remarks}"/>
                    </td>        
                </tr > `;


            counter++;



        }


        jQuery('#soPartTable tbody').empty().append(thisHtml);


        // var counter = 1;
        // for (let key in material_data) {


        //     var item_id = material_data[key].item_id ? material_data[key].item_id : "";

        //     jQuery(`.item_id_${ counter } `).val(item_id).trigger('liszt:updated');
        //     counter++;


        // }

        srNo();
        sumSoQty();

        totalAmount();

        discountRate();

    }
}

function soType() {
    let sel = jQuery('input[name="so_from_id_fix"]:checked').val();
    if (sel == '3') {

        jQuery('#soPartTable tbody').empty();
        jQuery('#soPartTable tfoot td').empty();
        jQuery('input:radio[name="so_type_id_fix"][value="' + 1 + '"]').attr('checked', true).trigger('click');


        jQuery('div#show').hide();
        jQuery('div#hide').show();
        jQuery('#radio_hide').hide();

        getLocationForMR();

    }
    else {

        jQuery('#soPartTable tbody').empty();


        jQuery('div#hide').hide();
        jQuery('div#show').show();
        jQuery('#radio_hide').show();

        if (formId == undefined) {
            addPartDetail();
            material_data = [];

        }


    }


    soTypeFix();
}
function changeRadio(val) {
    if (val == "2") {
        jQuery("#salesorderform").find('#replace_btn').show();
    } else {
        jQuery("#salesorderform").find('#replace_btn').hide();
    }
}
jQuery('input[name="so_type_id_fix"]').change(function () {
    changeRadio(this.value);
});

// jQuery('input[name="so_from_id_fix"]').change(function () {
//     jQuery('#soPartTable tbody tr').empty();
//     getLastSo(this.value);
// });
// search customer
// jQuery('#custSearchModal').on('show.bs.modal', function (e) {

// });


// modal validator
var coaPartValidator = jQuery("#searchCustomer").validate({
    rules: {
        "cust_id[]": {
            required: true
        },
    },
    messages: {
        "cust_id[]": {
            required: "Please Select Customer",
        }
    },

    submitHandler: function (form) {
        var modal = jQuery("#custSearchModal");


        var chkCount = 0;
        var chkArr = [];
        var chkId = [];

        jQuery("#custSearchModal").find("[id^='cust_id_']").each(function () {
            let thisId = jQuery(this).attr('id');
            let splt = thisId.split('cust_id_');
            let intId = splt[1];

            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });
        if (chkCount == 0) {
            toastError('Please Select Customer');
        } else {
            jQuery.ajax({
                url: RouteBasePath + "/get-oldcustomer?so_ids=" + chkArr.join(','),
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {

                        // let thisForm = jQuery("#salesorderform")
                        jQuery("#customer_reg_no").val(data.customer.customer_reg_no);
                        jQuery("#customer_name").val(data.customer.c_name);
                        jQuery("#customer_group_id").val(data.customer.customer_group_id).trigger('liszt:updated');
                        jQuery("#dealer_id").val(data.customer.dealer_id).trigger('liszt:updated');
                        jQuery("#customer_village").val(data.customer.customer_village);
                        jQuery("#customer_pincode").val(data.customer.customer_pincode);
                        jQuery("#so_country_id").val(data.customer.country_id).trigger('liszt:updated');
                        // jQuery("#so_state_id").val(data.customer.state_id).trigger('liszt:updated');
                        // jQuery("#so_district_id").val(data.customer.dis_id).trigger('liszt:updated');


                        if (data.customer.country_id != null) {
                            getSoStates().done(function (resposne) {
                                jQuery('#so_state_id').val(data.customer.state_id).trigger('liszt:updated');
                                getSoDistrict().done(function (resposne) {
                                    jQuery('#so_district_id').val(data.customer.dis_id).trigger('liszt:updated');
                                    getSoTaluka().done(function (resposne) {
                                        jQuery('#so_taluka_id').val(data.customer.customer_taluka).trigger('liszt:updated');
                                    });
                                });
                            });
                        }


                        modal.modal("hide");
                    } else {
                        toastError(data.response_message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
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

    }
});
// end



function fillPendingMaterialData() {

    var thisModal = jQuery('#pendingMaterialRequest');
    var thisForm = jQuery('#salesorderform');


    let location_id = jQuery('#so_location_id option:selected').val();
    var typeFixId = jQuery('input[name="so_from_id_fix"]:checked').val();

    if (location_id != "" && typeFixId == "3") {

        if (formId == undefined) {

            var Url = RouteBasePath + "/get-pending_material_request?location_id=" + location_id;
        } else {
            var Url = RouteBasePath + "/get-pending_material_request?location_id=" + location_id + "&id=" + formId;
        }

        jQuery.ajax({

            url: Url,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {

                    var usedParts = [];
                    var totalDisb = 0;
                    var found = 0;

                    thisForm.find('#soPartTable tbody input[name="form_indx"]').each(function (indx) {
                        let frmIndx = jQuery(this).val();
                        // console.log('jbEorkOrderId', frmIndx)
                        let jbEorkOrderId = material_data[frmIndx].mr_details_id;
                        if (jbEorkOrderId != "" && jbEorkOrderId != null) {
                            usedParts.push(Number(jbEorkOrderId));
                        }
                    });



                    function isUsed(pjId) {
                        // console.log(usedParts)
                        if (usedParts.includes(Number(pjId))) {
                            totalDisb++;
                            return true;
                        }
                        return false;
                    }

                    let totalEntry = 0;
                    var tblHtml = ``;
                    var found = 0;
                    if (data.mrData.length > 0 && !jQuery.isEmptyObject(data.mrData)) {
                        found = 1;
                        for (let idx in data.mrData) {
                            let inUse = isUsed(data.mrData[idx].mr_id);
                            totalEntry++;
                            tblHtml += `<tr>
                            <td><input type="radio" name="mr_id[]" class="simple-check ${inUse ? 'in-use' : ''}" id="mr_ids_${data.mrData[idx].mr_id}" value="${data.mrData[idx].mr_id}" ${inUse ? 'checked' : ''}/></td>
                            <td>${data.mrData[idx].mr_number}</td>                            
                            <td>${data.mrData[idx].mr_date}</td>
                        </tr >
                `;
                        }

                    } else {
                        tblHtml += `< tr class="centeralign" id = "noPendingPo" >
                <td colspan="15">No Pending Material Request Available</td>
                    </tr > `;
                    }

                    thisForm.find('.toggleModalBtn').prop('disabled', false);
                    thisModal.find('#pendingMaterialRequestTable tbody').empty().append(tblHtml);
                    if (found == 1) {

                        // if (totalDisb == totalEntry) {
                        //     jQuery('#pendingMaterialRequestModal').prop('disabled', true);
                        // } else {
                        //     jQuery('#pendingMaterialRequestModal').prop('disabled', false);
                        // }
                        thisForm.find('.toggleModalBtn').prop('disabled', false);

                    } else {
                        // resetPdWoForm();
                        thisForm.find('.toggleModalBtn').prop('disabled', true);
                    }
                    // thisModal.modal('show');
                    if (soIsUse == true) {
                        thisForm.find('.toggleModalBtn').prop('disabled', true);
                    }

                } else {
                    thisModal.find('#pendingMaterialRequestTable tbody').empty().append(tblHtml);
                    thisForm.find('.toggleModalBtn').prop('disabled', true);

                    toastError(data.response_message);
                }

            },



        });
    }
}




// Modal code value are not reset after submission


jQuery('#stateModal').on('show.bs.modal', function (e) {

    var dealer_modal_id = jQuery("#dealer_modal_id").val();

    let country = jQuery("#so_country_id").val();
    if (dealer_modal_id != "" && jQuery('#dealerModal').is(':visible')) {
        setTimeout(() => {
            jQuery("#country_id").val('').trigger('liszt:updated');
        }, 200);
    } else {
        setTimeout(() => {
            jQuery("#country_id").val(country).trigger('liszt:updated');
        }, 200);
    }





    if (country == "1" || jQuery('#country_id option:selected').val() == "1") {
        jQuery('#gst_code').prop("disabled", false);
    } else {

        jQuery('#gst_code').prop("disabled", true);

        jQuery('#gst_code').val('');
    }

});

jQuery('#cityModal').on('show.bs.modal', function (e) {
    let state = jQuery("#so_state_id").val();
    let country = jQuery("#so_country_id").val();
    var dealer_modal_id = jQuery("#dealer_modal_id").val();

    if (dealer_modal_id != "" && jQuery('#dealerModal').is(':visible')) {
        jQuery("#state_id").val('').trigger('liszt:updated');
        jQuery('#commonDistrictForm #country_name').val('');

    } else {
        jQuery("#state_id").val(state).trigger('liszt:updated');
        jQuery('#commonDistrictForm #country_name').val(jQuery('#so_country_id option:selected').text());

    }


    // if (country != '') {
    //     jQuery('#commonDistrictForm #country_name').val(jQuery('#so_country_id option:selected').text());
    // }

});

jQuery('#talukaModal').on('show.bs.modal', function (e) {
    let dist = jQuery("#so_district_id").val();
    let state = jQuery("#so_state_id").val();

    let country = jQuery("#so_country_id").val();
    var dealer_modal_id = jQuery("#dealer_modal_id").val();
    if (dealer_modal_id != "" && jQuery('#dealerModal').is(':visible')) {
        jQuery("#taluka_state_id").val('').trigger('liszt:updated');
        jQuery("#taluka_district_id").val('').trigger('liszt:updated');
        jQuery('#commonTalukaForm #country_name').val('');
    } else {

        jQuery("#taluka_state_id").val(state).trigger('liszt:updated');

        if (state != '' && state != null) {
            getDistrict().done(function (resposne) {
                jQuery("#taluka_district_id").val(dist).trigger('liszt:updated');
            });
        }

        if (country != '') {
            jQuery('#commonTalukaForm #country_name').val(jQuery('#so_country_id option:selected').text());
        }
    }


});

jQuery('#VillageModal').on('show.bs.modal', function (e) {

    let dist = jQuery("#so_district_id").val();
    let state = jQuery("#so_state_id").val();
    let taluka = jQuery("#so_taluka_id").val();
    let country = jQuery("#so_country_id").val();
    var dealer_modal_id = jQuery("#dealer_modal_id").val();

    if (dealer_modal_id != "" && jQuery('#dealerModal').is(':visible')) {
        jQuery("#village_state_id").val('').trigger('liszt:updated');
        jQuery("#district_id").val('').trigger('liszt:updated');
        jQuery("#taluka_id").val('').trigger('liszt:updated');
        jQuery('#commonVillageForm #country_name').val('');

    } else {

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
    }


});


// function sumSoQty(th) {
//     var total = parseFloat(0).toFixed(3);
//     jQuery('.so_qty').map(function () {
//         var total1 = jQuery(this).val();

//         if (total1 != "") {
//             // total = parseInt(total) + parseInt(total1);

//             total = parseFloat(total) + parseFloat(total1);

//         }
//     });

//     total != 0.000 && total != "" ? jQuery('.soqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.soqtysum').text('');

// }


// jQuery('#VillageModal').on('show.bs.modal', function (e) {

//     let dist = jQuery("#location_district_id").val();
//     let state = jQuery("#location_state_id").val();
//     let taluka = jQuery("#location_taluka_id").val();
//     let country = jQuery("#country_name").val();


//     jQuery("#village_state_id").val(state).trigger('liszt:updated');
//     jQuery("#district_id").val(dist).trigger('liszt:updated');

//     if ((dist != '' && dist != null) || (taluka != '' && taluka != null)) {
//         getDistrictData().done(function (resposne) {
//             jQuery("#district_id").val(dist).trigger('liszt:updated');

//             getTalukaData().done(function (resposne) {
//                 jQuery("#taluka_id").val(taluka).trigger('liszt:updated');
//             });
//         });
//     }

//     if (country != '') {
//         jQuery('#commonVillageForm #country_name').val(jQuery('#location_country_id option:selected').text());
//     }
// });
jQuery(document).ready(function () {
    function updateTitle() {
        if (jQuery("#salesorderform").find('input[name="so_from_id_fix"]:checked').val() == "1") {
            jQuery("#salesOrderTitle").html('Sales Order Detail <sup class="astric"> *</sup>');
        } else {
            jQuery("#salesOrderTitle").html('Sales Order Detail');
        }
    }
    updateTitle();
    jQuery("#salesorderform").find('input[name="so_from_id_fix"]').change(function () {
        updateTitle();
    });
});


function getLocationForMR() {
    var typeFixId = jQuery('input[name="so_from_id_fix"]:checked').val();

    if (typeFixId != '' && typeFixId == '3') {

        if (formId == undefined) {

            var Url = RouteBasePath + "/get-location_for_mr";
        } else {
            var Url = RouteBasePath + "/get-location_for_mr?id=" + formId;
        }


        return jQuery.ajax({
            url: Url,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    var stgDrpHtml = `<option value="">Select Location</option>`;
                    for (let indx in data.location) {
                        stgDrpHtml += `<option value="${data.location[indx].id}">${data.location[indx].location_name}</option>`;
                    }
                    jQuery('#so_location_id').empty().append(stgDrpHtml);
                    jQuery('#so_location_id').trigger('liszt:updated');
                }
            },

        });
    }
}



function getOldCustomer() {

    jQuery.ajax({
        url: RouteBasePath + "/getSearchCustomer",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                var tblHtml = "";
                if (data.search_customer.length > 0) {
                    for (let idx in data.search_customer) {
                        tblHtml += `<tr>`;


                        tblHtml += `<td><input type="radio" name="cust_id[]"  id="cust_id_${data.search_customer[idx].so_id}" value="${data.search_customer[idx].so_id}"} ${formId != undefined ? data.search_customer[idx].so_id == formId ? 'checked' : '' : ''}/></td> `;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].c_name)}</td>`;
                        // tblHtml += `<td>${checkInputNull(data.search_customer[idx].dealer_name)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].customer_reg_no)}</td> `;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].customer_village)}</td> `;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].customer_pincode)}</td> `;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].customer_taluka)}</td> `;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].dis_name)}</td> `;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].state_name)}</td> `;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].co_name)}</td>`;
                        tblHtml += `</tr>`;
                    }
                    jQuery('#searchCustomerTable tbody').empty().append(tblHtml);


                    if (jQuery.fn.DataTable.isDataTable('#searchCustomerTable')) {
                        jQuery('#searchCustomerTable').DataTable().destroy();
                    }

                    table = jQuery('#searchCustomerTable').DataTable({
                        pageLength: 50,
                        paging: true,
                        searching: true,
                        "oLanguage": {
                            "sSearch": "Search :"
                        },
                        // "sScrollY": calcDataTableHeight(),
                    });
                } else {
                    jQuery('#replace_btn').prop('disabled', true);
                }
            } else {
                toastError(data.response_message);
            }
        },
    });

}


function changePincode() {

    let thisForm = jQuery('#salesorderform');

    let getVillageData = jQuery('#customer_village option:selected').val();

    if (getVillageData != "" && getVillageData !== undefined) {
        jQuery.ajax({
            url: RouteBasePath + "/get-villageData/?village_id=" + getVillageData,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    jQuery("#customer_pincode").val(data.pincode);
                } else {
                    jAlert(data.response_message);
                }
            },
        });
    }
}

function getLastSo(val) {

    if (val == 1) {

        jQuery.ajax({
            url: RouteBasePath + "/get-last_so_details",
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                jQuery('#so_no').removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#customer_group_id').val(data.last_data.customer_group_id).trigger('liszt:updated');
                    jQuery('#customer_reg_no').val(data.last_data.customer_reg_no);
                    jQuery('#customer_name').val(data.last_data.customer_name);
                    jQuery('#dealer_id').val(data.last_data.dealer_id).trigger('liszt:updated');
                    jQuery('#so_country_id').val(data.last_data.country_id).trigger('liszt:updated');
                    jQuery('#customer_village').val(data.last_data.customer_village).trigger('liszt:updated');
                    jQuery('#special_notes').val(data.last_data.special_notes);
                    jQuery('#customer_pincode').val(data.last_data.customer_pincode);
                    if (data.last_data.country_id != null) {
                        getSoStates().done(function (resposne) {
                            jQuery('#so_state_id').val(data.last_data.state_id).trigger('liszt:updated');
                            getSoDistrict().done(function (resposne) {
                                jQuery('#so_district_id').val(data.last_data.district_id).trigger('liszt:updated');
                                getSoTaluka().done(function (resposne) {
                                    jQuery('#so_taluka_id').val(data.last_data.customer_taluka).trigger('liszt:updated');
                                    getSoVillage().done(function (resposne) {
                                        jQuery('#customer_village').val(data.last_data.customer_village).trigger('liszt:updated');
                                    });
                                });

                            });
                        });
                        fillLastSoTable(data.soDetails);
                    }
                } else {
                    console.log(data.response_message)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery('#so_no').removeClass('file-loader');
            }
        });
    } else {
        // jQuery('#soPartTable tbody').empty();

    }

}

function fillLastSoTable(so_data) {
    jQuery('#soPartTable tbody').empty();
    var thisHtml = '';

    if (so_data.length > 0) {
        var counter = 1;
        for (let key in so_data) {
            var sr_no = counter;
            var sales_order_id = so_data[key].so_details_id ? so_data[key].so_details_id : "";

            var item_id = so_data[key].item_id ? so_data[key].item_id : "";
            var item_code = so_data[key].item_code ? so_data[key].item_code : "";
            var item_group_name = so_data[key].item_group_name ? so_data[key].item_group_name : "";
            var so_qty = so_data[key].so_qty ? so_data[key].so_qty.toFixed(3) : "";

            var unit_name = so_data[key].unit_name ? so_data[key].unit_name : "";
            var rate_per_unit = so_data[key].rate_per_unit ? parseFloat(so_data[key].rate_per_unit).toFixed(2) : "";
            var so_amount = so_data[key].so_amount ? so_data[key].so_amount : "";
            var fitting_item = so_data[key].fitting_item ? so_data[key].fitting_item : "";

            thisHtml += `<tr>
                            <td><a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>                
                            <td class="sr_no">${sr_no}</td>   
                            <td><input type="hidden" name="sales_order_detail_id[]" value="${sales_order_id}">
                            <select name="item_id[]" class="chzn-select item_id item_id_${sr_no}" onChange="getItemData(this), sumSoQty(this)" readonly>${productDrpHtml}</select></td>          
                            <td id="code">${item_code}</td>`;

            if (fitting_item == 'yes') {
                thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty" tabindex="-1" readonly  onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}" /></td>`;
            } else {
                thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty"  onKeyup="sumSoQty(this)"  onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}"/></td>`;
            }
            thisHtml += `  <td id="unit">${unit_name}</td>`;
            if (getLocationType == "HO" || fitting_item == 'yes') {
                thisHtml += `   <td><input type="text" name="rate_unit[]"  onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey" onblur="formatPoints(this,2)"/ value="${rate_per_unit}" ${so_qty == parseFloat(so_data[key].used_qty).toFixed(3) ? 'readonly tabindex="-1"' : ''} style="width:60px;"></td> `;
            }
            else {
                thisHtml += `   <td><input type="text" name="rate_unit[]"  onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey" onblur="formatPoints(this,2)"/ value="${rate_per_unit}" ${so_qty == parseFloat(so_data[key].used_qty).toFixed(3) ? 'readonly tabindex="-1"' : ''} style="width:60px;" readonly></td> `;
            }
            thisHtml += ` <td><input type="number" name="amount[]" id="amount" class="form-control amount" onblur="formatPoints(this,2)" tabindex="-1" value="${formatAmount(so_amount)}" readonly tabindex="-1" style="width:70px;"/></td>             
                </tr>`;


            counter++;
        }



        jQuery('#soPartTable tbody').append(thisHtml);


        var counter = 1;
        for (let key in so_data) {


            var item_id = so_data[key].item_id ? so_data[key].item_id : "";

            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            counter++;


        }

        sumSoQty();
        srNo();
        totalAmount();
        disabledDropdownVal();
    }
}






function discountRate() {
    var discount = jQuery('#discount').val();

    if (discount !== '') {
        discount = parseFloat(discount);
        if (discount >= 0.01 && discount < 100) {

            // Loop through each table row in #soPartTable
            jQuery('#soPartTable tbody tr').each(function () {
                var rateElement = jQuery(this).find('[name="rate_unit[]"]');

                if (rateElement) {
                    var rateValue = rateElement.attr('data-rate') != '' ? parseFloat(rateElement.attr('data-rate')) : rateElement.val();

                    if (!isNaN(rateValue)) {
                        var discountAmount = rateValue * discount / 100;
                        var finalRate = rateValue - discountAmount;

                        finalRate = finalRate > 0 ? finalRate.toFixed(2) : '';

                        // console.log('rateValue:', rateValue, 'discountAmount:', discountAmount, 'finalRate:', finalRate);

                        rateElement.val(finalRate);
                    }
                }
                // soRateUnit(jQuery(this));
                soRateUnit(jQuery(this).find('[name="rate_unit[]"]'));
            });

        } else if (discount >= 100) {
            jQuery('#discount').val('');
            toastError('Please Enter Discount Value Less Than 100');
        } else {
            jQuery('#discount').val('');
            toastError('Please Enter Discount Value Greater Than 0.01');
        }
    }
}



function getOldCustomer() {
    var selectedCustomerId = null; // Variable to store the selected customer ID
    var searchCustomerData = null; // Variable to store API response

    jQuery.ajax({
        url: RouteBasePath + "/getSearchSOCustomer",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                var tblHtml = "";
                var dropdownHtml = "";

                if (data.search_customer.length > 0) {

                    for (let idx in data.search_customer) {

                        // Add customer to dropdown
                        dropdownHtml += `<option value="${data.search_customer[idx].customer_name}">${data.search_customer[idx].customer_name}</option>`;
                    }
                }

                jQuery("#salesorderform").find('#rep_customer_id').append(dropdownHtml).trigger('liszt:updated');

            } else {
                toastError(data.response_message);
            }

        },
    });
}


// get particular customer detail
function getSearchData() {
    var old_customer = jQuery('#rep_customer_id').val();
    jQuery.ajax({
        url: RouteBasePath + "/get-oldcustomer_so_no?old_customer=" + old_customer,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {

                var tblHtml = "";
                var dropdownHtml = "";

                dropdownHtml += `<option value="">Select SO No.</option>`;

                if (data.SoNo.length > 0) {
                    for (let idx in data.SoNo) {
                        dropdownHtml += `<option value="${data.SoNo[idx].id}">${data.SoNo[idx].so_number}</option>`;
                    }
                }

                jQuery("#salesorderform").find('#pre_so_no').empty().append(dropdownHtml).trigger('liszt:updated');

            } else {
                toastError(data.response_message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
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



function getPreSoDetail() {
    var old_so_id = jQuery('#pre_so_no').val();
    jQuery.ajax({
        url: RouteBasePath + "/get-old_so_no?so_id=" + old_so_id,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                if (data.sales_order_part.length > 0) {

                    var tblHtml = ``;

                    loadSalesOrderRepData(data);

                    // jQuery('#dealer_id').val(data.sales_order.dealer_id).trigger('liszt:updated');
                    jQuery('#so_country_id').val(data.sales_order.country_id).trigger('liszt:updated');

                    // jQuery('#customer_group_id').val(data.sales_order.customer_group_id).trigger('liszt:updated');
                    jQuery('#customer_pincode').val(data.sales_order.customer_pincode);
                    jQuery('#so_mobile_no').val(data.sales_order.mobile_no);
                    jQuery('#area').val(data.sales_order.area);
                    jQuery('#ship_to').val(data.sales_order.ship_to);
                    jQuery('#customer_reg_no').val(data.sales_order.customer_reg_no);
                    jQuery('#mis_category_id').val(data.sales_order.mis_category_id).trigger('liszt:updated');



                    getSoStates().done(function (resposne) {
                        jQuery('#so_state_id').val(data.sales_order.state_id).trigger('liszt:updated');
                        getSoDistrict().done(function (resposne) {
                            jQuery('#so_district_id').val(data.sales_order.district_id).trigger('liszt:updated');
                            getSoTaluka().done(function (resposne) {
                                jQuery('#so_taluka_id').val(data.sales_order.customer_taluka).trigger('liszt:updated');
                                getSoVillage().done(function (resposne) {
                                    jQuery('#customer_village').val(data.sales_order.customer_village).trigger('liszt:updated');
                                });
                            });

                        });
                    });


                    for (let idx in data.sales_order_part) {

                        tblHtml += `<tr>`;
                        tblHtml += `<td><input type="checkbox" name="so_detail_id[]" id="so_detail_id_${data.sales_order_part[idx].so_details_id}" value="${data.sales_order_part[idx].so_details_id}"}/></td>`;
                        tblHtml += `<td>${checkInputNull(data.sales_order_part[idx].item_name)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.sales_order_part[idx].item_code)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.sales_order_part[idx].item_group_name)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.sales_order_part[idx].so_qty.toFixed(3))}</td>`;
                        tblHtml += `<td>${checkInputNull(data.sales_order_part[idx].unit_name)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.sales_order_part[idx].rate_per_unit.toFixed(2))}</td>`;

                        tblHtml += `</tr>`;
                    }


                    jQuery('#soItemModalTable tbody').empty().append(tblHtml);

                    jQuery('#so_item_btn').prop('disabled', false);


                }
            } else {
                toastError(data.response_message);
            }
        },
    });

}



//<--On Work Order Modal Show-->//
jQuery('#previousSODetailModel').on('show.bs.modal', function (e) {
    var usedParts = [];
    var totalDisb = 0;



    jQuery('#soPartTable tbody tr').each(function () {
        var sodId = jQuery(this).find('[name="sales_order_detail_id[]"]').val();
        if (sodId != "" && sodId != null) {
            usedParts.push(Number(sodId));
        }
    });

    function isUsed(pjId) {
        if (usedParts.includes(Number(pjId))) {
            totalDisb++;
            return true;
        }
        return false;
    }

    var totalEntry = 0;
    jQuery('#soItemModalTable tbody tr').each(function (indx) {

        totalEntry++;
        var checkField = jQuery(this).find('input[name="so_detail_id[]"]');
        var partId = jQuery(checkField).val();
        var inUse = isUsed(partId);


        if (inUse) {
            jQuery(checkField).addClass('in-use').prop('checked', true);

        } else {
            jQuery(checkField).removeClass('in-use').prop('checked', false);
        }

    });



    setTimeout(() => {
        jQuery(this).find('#checkall-item').focus();
    }, 300);


});




var coaPartValidator = jQuery("#oldItemSalesOrderForm").validate({

    rules: {
        "so_detail_id[]": {
            required: true
        },
    },

    messages: {
        "so_detail_id[]": {
            required: "Please Select Item",
        }
    },

    submitHandler: function (form) {

        var chkCount = 0;
        var chkArr = [];
        var chkId = [];

        jQuery("#oldItemSalesOrderForm").find("[id^='so_detail_id_']").each(function () {
            var thisId = jQuery(this).attr('id');
            var splt = thisId.split('so_detail_id_');
            var intId = splt[1];



            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }

        });


        if (chkCount == 0) {
            toastError('Please Select Pending Item');

        } else {

            if (formId == undefined) {
                var url = RouteBasePath + "/get-old_item-so?sod_ids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-old_item-so?sod_ids=" + chkArr.join(',') + "&id=" + formId;
            }

            jQuery.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.item.length > 0) {
                            for (let indx in data.item) {
                                productDrpHtml += `<option value="${data.item[indx].id}">${data.item[indx].item_name} </option>`;
                            }
                            jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
                        }
                        storeSalesOrderDetails = [];
                        if (data.so_part_details_details.length > 0 && !jQuery.isEmptyObject(data.so_part_details_details)) {
                            var groupedData = data.so_part_details_details.reduce((acc, obj) => {
                                var itemId = obj.mitem_id;
                                if (!acc[itemId]) {
                                    acc[itemId] = [];
                                }
                                acc[itemId].push(obj);
                                return acc;
                            }, {});


                            for (let key in data.so_part_details) {
                                if (groupedData[data.so_part_details[key].item_id] != undefined) {
                                    storeSalesOrderDetails[data.so_part_details[key].item_id] = groupedData[data.so_part_details[key].item_id];
                                }

                            }
                            // storeSalesOrderDetails(data.so_part_details_details);
                        }
                        jQuery('#soPartTable tbody').empty();


                        fillOldSoTable(data.so_part_details);

                        jQuery('#rep_customer_id').prop({ tabindex: -1 }).attr('readonly', true);



                        jQuery("#previousSODetailModel").modal('hide');
                    } else {
                        toastError(data.response_message);
                    }

                },

                error: function (jqXHR, textStatus, errorThrown) {
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
    }
});




function fillOldSoTable(so_data) {

    if (so_data.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in so_data) {

            var sr_no = counter;
            var sales_order_id = so_data[key].so_details_id ? so_data[key].so_details_id : "";

            var item_id = so_data[key].item_id ? so_data[key].item_id : "";
            var item_code = so_data[key].item_code ? so_data[key].item_code : "";
            var item_group_name = so_data[key].item_group_name ? so_data[key].item_group_name : "";
            var so_qty = so_data[key].so_qty ? so_data[key].so_qty.toFixed(3) : "";

            var unit_name = so_data[key].unit_name ? so_data[key].unit_name : "";
            var rate_per_unit = so_data[key].rate_per_unit ? parseFloat(so_data[key].rate_per_unit).toFixed(2) : "";
            var so_amount = so_data[key].so_amount ? so_data[key].so_amount : "";
            var discount = so_data[key].discount ? parseFloat(so_data[key].discount).toFixed(2) : parseFloat(0).toFixed(2);
            var remarks = so_data[key].remarks ? so_data[key].remarks : "";
            var fitting_item = so_data[key].fitting_item ? so_data[key].fitting_item : "";


            thisHtml += `<tr>
                            <td><a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>   

                            <td class="sr_no">${sr_no}</td>  

                            <td><input type="hidden" name="sales_order_detail_id[]" value="${sales_order_id}">
                            <select name="item_id[]" class="chzn-select item_id item_id_${sr_no} so_item_select_width" onChange="getItemData(this), sumSoQty(this)"  ${so_data[key].in_use == true ? 'readonly tabindex="-1"' : ''}>${productDrpHtml}</select></td>   

                            <td id="code">${item_code}</td>`;

            if (fitting_item == 'yes') {
                thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty" tabindex="-1" readonly  onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}"  max="${parseFloat(so_data[key].used_qty).toFixed(3)}"/></td>`;
            } else {
                thisHtml += ` <td><input type="text" name="so_qty[]" id="so_qty"  onKeyup="sumSoQty(this)"  onblur="formatPoints(this,3)" class="form-control isNumberKey so_qty" style="width:50px;" value="${so_qty}" max="${parseFloat(so_data[key].used_qty).toFixed(3)}"/></td>`;
            }
            thisHtml += `  <td id="unit">${unit_name}</td>        
                <td><input type="text" name="rate_unit[]"  onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey" onblur="formatPoints(this,2)"/ value="${rate_per_unit}" style="width:60px;"></td>  
                <td><input type="text" name="discount[]" id="discount" onKeyup="Discount(this)" id="discount" class="form-control  discount  isNumberKey" maxlength="5" value="${discount}" onblur="formatPoints(this,2)" style="width:50px;" /></td>              
                <td><input type="number" name="amount[]" id="amount" class="form-control amount " onblur="formatPoints(this,2)" tabindex="-1" value="${formatAmount(so_amount)}" readonly tabindex="-1" style="width:70px;"/></td>  
                 <td><input type="text" name="remarks[]" id="remarks" class="form-control" value="${remarks}"/></td>             
                </tr>`;
            counter++;


        }


        jQuery('#soPartTable tbody').append(thisHtml);


        var counter = 1;
        for (let key in so_data) {


            var item_id = so_data[key].item_id ? so_data[key].item_id : "";
            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            counter++;


        }


        /* jQuery('#soPartTable tbody').append(`<tr class="total_tr"><td colspan="5" ></td><td class="soqtysum"></td>
         <td></td>
         <td></td>
         <td class="amountsum"></td></tr>`);*/

        sumSoQty();
        srNo();
        totalAmount();
        disabledDropdownVal();
    }
}


jQuery('#checkall-item').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#oldItemSalesOrderForm").find("[id^='so_detail_id_']:not(.in-use)").prop('checked', true).trigger('change');
    } else {
        jQuery("#oldItemSalesOrderForm").find("[id^='so_detail_id_']:not(.in-use)").prop('checked', false).trigger('change');
    }
});

jQuery(document).on('click', '.eyeIcon1', function () {
    var $td = jQuery(this).closest('td');
    var $select = $td.find('select');
    getItemData($select);
});


function getCountyandStateForLocation() {
    jQuery.ajax({
        url: RouteBasePath + "/get-country_state_for_location",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jQuery('#so_country_id').val(data.location_data.country_id).trigger('liszt:updated');
                getSoStates();
                if (data.location_data.country_id != null) {
                    getSoStates().done(function (resposne) {
                        jQuery('#so_state_id').val(data.location_data.state_id).trigger('liszt:updated');
                        getSoDistrict();
                    })
                }
            }
        },
    });

}


/* GST Calculation */

jQuery('.gst-fields').on('change keyup', function () {
    calcGstAmount();
});
function manageGstType() {

    var thisForm = jQuery('#salesorderform');



    var gstType = thisForm.find("input[name*='gst_type_fix_id']:checked").val();


    if (gstType != "") {

        if (gstType == 3) { // None

            thisForm.find(".igst-field").val('')

            thisForm.find(".igst-field:not(.disb)").prop('disabled', true);

            thisForm.find(".sgst-field").val('')

            thisForm.find(".sgst-field:not(.disb)").prop('disabled', true);

            thisForm.find(".cgst-field").val('')

            thisForm.find(".cgst-field:not(.disb)").prop('disabled', true);

        } else if (gstType == 2) { //  SGCT+CSGT


            thisForm.find(".igst-field").val('')

            thisForm.find(".igst-field:not(.disb)").prop('disabled', true);

            thisForm.find(".sgst-field:not(.disb)").prop('disabled', false);

            thisForm.find(".cgst-field:not(.disb)").prop('disabled', false);



        } else { // IGST

            thisForm.find(".igst-field:not(.disb)").prop('disabled', false);

            thisForm.find(".sgst-field").val('')

            thisForm.find(".sgst-field:not(.disb)").prop('disabled', true);

            thisForm.find(".cgst-field").val('')

            thisForm.find(".cgst-field:not(.disb)").prop('disabled', true);

        }

    }
    calcGstAmount();

}

function calcGstAmount() {

    var thisForm = jQuery('#salesorderform');
    var gstType = thisForm.find("input[name*='gst_type_fix_id']:checked").val();
    var basicAmount = thisForm.find("#basic_amount").val();
    var pfPer = thisForm.find("#secondary_transport").val();
    var shucPer = thisForm.find("#sharing_head_unit_cost").val();
    var icPer = thisForm.find("#installation_charge").val();
    pfAmount = isNaN(Number(pfPer)) ? 0 : Number(pfPer);
    shucAmount = isNaN(Number(shucPer)) ? 0 : Number(shucPer);
    icAmount = isNaN(Number(icPer)) ? 0 : Number(icPer);
    basicAmount = isNaN(Number(basicAmount)) ? 0 : Number(basicAmount);

    var lessDiscountAmount = thisForm.find("#less_discount_amount").val();
    lessDiscountAmount = isNaN(Number(lessDiscountAmount)) ? 0 : Number(lessDiscountAmount);

    var sumAmount = pfAmount + shucAmount + icAmount + basicAmount - lessDiscountAmount;


    if (gstType != "") {

        if (gstType == 3) { // NONE

        } else if (gstType == 2) { //   SGCT+CSGT

            var sgstPer = thisForm.find("#sgst_percentage").val();
            var cgstPer = thisForm.find("#cgst_percentage").val();
            sgstPer = isNaN(Number(sgstPer)) ? 0 : Number(sgstPer);
            cgstPer = isNaN(Number(cgstPer)) ? 0 : Number(cgstPer);

            if (sumAmount > 0 && sgstPer > 0) {
                thisForm.find("#sgst_amount").val(formatAmount(sumAmount * (sgstPer / 100)));
            } else {
                thisForm.find("#sgst_amount").val('');
            }

            if (sumAmount > 0 && cgstPer > 0) {
                thisForm.find("#cgst_amount").val(formatAmount(sumAmount * (cgstPer / 100)));
            } else {
                thisForm.find("#cgst_amount").val('');

            }

        } else { // IGST

            var igstPer = thisForm.find("#igst_percentage").val();
            igstPer = isNaN(Number(igstPer)) ? 0 : Number(igstPer);
            if (sumAmount > 0 && igstPer > 0) {
                thisForm.find("#igst_amount").val(formatAmount(sumAmount * (igstPer / 100)));
            } else {
                thisForm.find("#igst_amount").val('');
            }
        }
    }
    calcNetAmount();

}

function calcNetAmount() {

    var thisForm = jQuery('#salesorderform');
    var gstType = thisForm.find("input[name*='gst_type_fix_id']:checked").val();
    var basicAmount = thisForm.find("#basic_amount").val();
    basicAmount = isNaN(Number(basicAmount)) ? 0 : Number(basicAmount);
    var lessDiscountAmount = thisForm.find("#less_discount_amount").val();
    lessDiscountAmount = isNaN(Number(lessDiscountAmount)) ? 0 : Number(lessDiscountAmount);
    var pfPer = thisForm.find("#secondary_transport").val();
    var shucPer = thisForm.find("#sharing_head_unit_cost").val();
    var icPer = thisForm.find("#installation_charge").val();

    pfAmount = isNaN(Number(pfPer)) ? 0 : Number(pfPer);
    shucAmount = isNaN(Number(shucPer)) ? 0 : Number(shucPer);
    icAmount = isNaN(Number(icPer)) ? 0 : Number(icPer);

    var r_val = thisForm.find("#round_off").val();

    if (r_val != '') {
        if (r_val.trim() !== "") {
            var r = isNaN(Number(r_val)) ? 0 : Number(r_val);     // Convert round-off to number
        }
    } else {
        var r = 0;
    }


    if (gstType != "") {

        if (gstType == 3) { // None

            if (r < 0) {
                thisForm.find("#net_amount").val(parseFloat((basicAmount - lessDiscountAmount + pfAmount + shucAmount + icAmount) - Math.abs(r)).toFixed(2));
            } else {
                thisForm.find("#net_amount").val(parseFloat(basicAmount - lessDiscountAmount + pfAmount + shucAmount + icAmount + r).toFixed(2));
            }

        } else if (gstType == 2) { //   SGCT+CSGT

            var sgstAmount = thisForm.find("#sgst_amount").val();
            var cgstAmount = thisForm.find("#cgst_amount").val();
            sgstAmount = isNaN(Number(sgstAmount)) ? 0 : Number(sgstAmount);
            cgstAmount = isNaN(Number(cgstAmount)) ? 0 : Number(cgstAmount);

            if (r < 0) {

                thisForm.find("#net_amount").val(parseFloat(basicAmount - lessDiscountAmount + pfAmount + sgstAmount + cgstAmount + shucAmount + icAmount - Math.abs(r)).toFixed(2));

            } else {
                thisForm.find("#net_amount").val(parseFloat(basicAmount - lessDiscountAmount + pfAmount + sgstAmount + cgstAmount + shucAmount + icAmount + r).toFixed(2));
            }


        } else { //IGST

            var igstAmount = thisForm.find("#igst_amount").val();
            igstAmount = isNaN(Number(igstAmount)) ? 0 : Number(igstAmount);
            if (r < 0) {
                thisForm.find("#net_amount").val(parseFloat((basicAmount - lessDiscountAmount + pfAmount + shucAmount + icAmount + igstAmount) - Math.abs(r)).toFixed(2));
            } else {
                thisForm.find("#net_amount").val(parseFloat(basicAmount - lessDiscountAmount + pfAmount + shucAmount + icAmount + igstAmount + r).toFixed(2));
            }


        }
    }

}

function calcLessDiscount() {
    var thisForm = jQuery('#salesorderform');
    var basicAmount = thisForm.find("#basic_amount").val();
    var lessDiscountPercentage = thisForm.find("#less_discount_percentage").val();
    var lessDiscountAmount = thisForm.find("#less_discount_amount").val();
    lessDiscountPercentage = isNaN(Number(lessDiscountPercentage)) ? 0 : Number(lessDiscountPercentage);

    if (lessDiscountPercentage != '' && lessDiscountPercentage != null) {
        if (basicAmount > 0 && basicAmount != '' && lessDiscountPercentage > 0 && lessDiscountPercentage <= 100) {
            thisForm.find("#less_discount_amount").val(formatAmount(basicAmount * (lessDiscountPercentage / 100)));
        } else {
            thisForm.find("#less_discount_amount").val('');
        }
    } else {
        thisForm.find("#less_discount_amount").val(formatAmount(lessDiscountAmount));
    }
    calcGstAmount();
}

// function calcRoundOfAmount() {

//     calcGstAmount()
//     var thisForm = jQuery('#salesorderform');

//     var net_amt = thisForm.find("#net_amount").val();
//     var r_val = thisForm.find("#round_off").val();

//     var n = isNaN(Number(net_amt)) ? 0 : Number(net_amt);
//     var r = isNaN(Number(r_val)) ? 0 : Number(r_val);


//     var final_round;

//     if (net_amt.trim() !== "" && r_val.trim() !== "") {
//         var n = isNaN(Number(net_amt)) ? 0 : Number(net_amt); // Convert net amount to number
//         var r = isNaN(Number(r_val)) ? 0 : Number(r_val);     // Convert round-off to number
//         var final_round;
//         if (r < 0) {
//             final_round = n - Math.abs(r); // Subtract if round-off is negative
//             // console.log("Negative Round-off Applied:", r);

//         } else {
//             final_round = n + r; // Add if round-off is positive
//             // console.log("Positive Round-off Applied:", r);
//         }

//         thisForm.find("#net_amount").val(formatAmount(final_round));   // Update the result in the final amount field
//     }

// }

// jQuery(document).ready(function () {
//     jQuery('#round_off').on('input', function () {
//         var value = jQuery(this).val();
//           if (!isValidRoundOff(value)) {
//             jQuery(this).val(value.slice(0, -1));
//         }
//     });
// });
// function isValidRoundOff(value) {
//     var regex = /^\d+(\.\d*)?$/;
//     return regex.test(value);
// }

/* End GST Calculation */



jQuery(document).on('keydown', '.round-off', function (e) {

    // Allow numbers (0-9), plus (+), minus (-), and decimal point (.)
    if (
        (e.which >= 48 && e.which <= 57) ||  // Numbers 0-9 (main keyboard)
        (e.which >= 96 && e.which <= 105) || // Numbers 0-9 (numpad)
        e.key === '+' ||                    // Plus sign (+)
        e.key === '-' ||                    // Minus sign (-)
        e.key === '.' ||                    // Decimal point (.)
        e.which === 8 ||                     // Backspace
        e.which === 9 ||                     // Tab
        e.which === 32                       // Space (optional if you want to allow spaces)
    ) {
        // Allow input for numbers, plus, minus, decimal, and backspace, tab, space
        return;
    }

    // Prevent all other keys
    e.preventDefault();
});

async function getSoDealer() {
    return new Promise((resolve, reject) => {
        var customer_group_id = jQuery('#customer_group_id option:selected').val();
        if (customer_group_id != '') {

            if (formId == undefined) {
                var url = RouteBasePath + "/get-so_dealer?customer_group_id=" + customer_group_id;
            } else {
                var url = RouteBasePath + "/get-so_dealer?customer_group_id=" + customer_group_id + "&id=" + formId;
            }

            jQuery.ajax({
                url: url,
                type: 'POST',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        let dropHtml = `<option value=''>Select Dealer</option>`;
                        if (!jQuery.isEmptyObject(data.so_dealer) && data.so_dealer.length > 0) {
                            for (let idx in data.so_dealer) {
                                dropHtml += `<option value="${data.so_dealer[idx].id}" data-agreement_end_date="${data.so_dealer[idx].agreement_end_date}">${data.so_dealer[idx].dealer_name}</option>`;
                            }
                        }
                        jQuery('#dealer_id').empty().append(dropHtml).trigger('liszt:updated');

                        if (formId == undefined) {
                            jQuery('#soPartTable tbody').empty();
                            jQuery('#soPartModalTable tbody').empty();
                            addPartDetail();
                            addModalPartDetail();
                        } else {
                            setTimeout(() => {
                                let checkLength = jQuery("#soPartTable tbody tr").filter(function () {
                                    return jQuery(this).css('display') !== 'none';
                                }).length;

                                if (checkLength < 1) {
                                    addPartDetail();
                                }

                                let checkModelLength = jQuery("#soPartModalTable tbody tr").filter(function () {
                                    return jQuery(this).css('display') !== 'none';
                                }).length;

                                if (checkModelLength < 1) {
                                    addModalPartDetail();
                                }
                            }, 600);

                        }




                        if (data.mappedItems.length > 0) {
                            // var item_id = ``;
                            productDrpHtml = '';

                            productDrpHtml += '<option value="">Select Item</option>';


                            for (let indx in data.mappedItems) {

                                productDrpHtml += `<option value="${data.mappedItems[indx].id}" data-item_code="${data.mappedItems[indx].item_code}" data-item_group="${data.mappedItems[indx].item_group_name}" data-unit_name="${data.mappedItems[indx].unit_name}" data-stock_qty="${data.mappedItems[indx].stock_qty}">${data.mappedItems[indx].item_name} </option>`;
                                // item_id += `data-rate="${data.mappedItems[indx].id}" `;
                            }
                        }

                        jQuery(".item_id").chosen({
                            search_contains: true
                        });
                        jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');

                        if (data.fittingmappedItems.length > 0) {
                            fittingProductDrpHtml = '';

                            fittingProductDrpHtml += '<option value="">Select Item</option>';

                            for (let indx in data.fittingmappedItems) {

                                fittingProductDrpHtml += `<option value="${data.fittingmappedItems[indx].id}" data-item_code="${data.fittingmappedItems[indx].item_code}" data-unit_name="${data.fittingmappedItems[indx].unit_name}">${data.fittingmappedItems[indx].item_name} </option>`;
                            }
                        }

                        // jQuery('.item_id').chosen();
                        jQuery(".modal_item_id").chosen({
                            search_contains: true
                        });
                        jQuery('.modal_item_id').empty().append(fittingProductDrpHtml).trigger('liszt:updated');
                        resolve();
                        jQuery('#show-progress').removeClass('loader-progress-whole-page');


                        resolve();  // Resolve promise when finished
                    } else {
                        jQuery('#dealer_id').empty().append("<option value=''>Select Dealer</option>").trigger('liszt:updated');

                        jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');

                        jQuery('.modal_item_id').empty().append(fittingProductDrpHtml).trigger('liszt:updated');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
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
        } else {
            productDrpHtml = '';
            fittingProductDrpHtml = '';

            productDrpHtml += '<option value="">Select Item</option>';
            fittingProductDrpHtml += '<option value="">Select Item</option>';

            jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
            jQuery('.modal_item_id').empty().append(fittingProductDrpHtml).trigger('liszt:updated');
        }
    });

}


function getAgreementEndDate() {
    var dealer_id = jQuery('#dealer_id').val();

    if (dealer_id !== '') {
        var agreement_end_date = jQuery('#dealer_id').find('option:selected').data('agreement_end_date');
        jQuery('#salesorderform').find('#agreement_end_date').val(agreement_end_date);
    }
}



function Discount(th) {
    var so_qty = jQuery(th).parents('tr').find("#so_qty").val();
    var rateUnit = jQuery(th).parents('tr').find("#rate_unit").val();
    var discount = jQuery(th).parents('tr').find("#discount").val() != undefined ? jQuery(th).parents('tr').find("#discount").val() : 0;

    if (so_qty != '' && rateUnit != '' && rateUnit != undefined) {
        if (discount != '') {
            if (discount < 100) {
                rateUnit = parseFloat(rateUnit - (rateUnit * discount / 100)).toFixed(2);
                soUnit = so_qty * rateUnit;
                jQuery(th).parents('tr').find("#amount").val(parseFloat(soUnit).toFixed(2));

            } else {
                toastError('Please Enter Discount Value Less Than 100');
                jQuery(th).parents('tr').find("#discount").val(parseFloat(0).toFixed(2));
                soUnit = parseFloat(so_qty) * parseFloat(rateUnit);
                jQuery(th).parents('tr').find("#amount").val(parseFloat(soUnit).toFixed(2));
            }

        } else {
            jQuery(th).parents('tr').find("#discount").val(parseFloat(0).toFixed(2));
            soUnit = parseFloat(so_qty) * parseFloat(rateUnit);
            jQuery(th).parents('tr').find("#amount").val(parseFloat(soUnit).toFixed(2));
        }

    } else {
        jQuery(th).parents('tr').find("#amount").val('');
    }

    totalAmount();
}



function validateImage(filePath) {
    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.pdf)$/i;
    if (!allowedExtensions.exec(filePath)) {
        return false;
    }
    return true;
}

function fileUpload(e) {
    console.log("e");
    var form_data = new FormData();

    // Read selected files

    var target = e.target;

    var id = target.id;

    var files = target.files;

    var totalfiles = files.length;



    var oldImg = jQuery('#file_upload_doc').val();



    jQuery('#' + id).parent().parent().find('.uneditable-input').removeClass('iconfa-warning-sign upload-error');

    if (totalfiles > 0) {



        var notValid = 0;

        for (var index = 0; index < totalfiles; index++) {

            if (validateImage(files[index].name) == true) {

                form_data.append("docs[]", files[index]);

            } else {

                notValid = 1;

                // toastError("Only (jpeg,jpg,png,gif) files are allowed.");
                jAlert("Only (jpeg,jpg,png,gif,pdf) files are allowed.");

                e.stopImmediatePropagation();

                return false;

            }

        }



        if (notValid == 0) {



            jQuery('#' + id).parent().parent().find('.uneditable-input').addClass('file-loader');



            jQuery.ajax({

                url: RouteBasePath + "/upload-docs",


                type: 'POST',

                data: form_data,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                contentType: false,



                success: function (data) {

                    jQuery('#' + id).parent().parent().find('.uneditable-input').removeClass('file-loader');

                    if (data.response_code == 1) {



                        if (oldImg != "") {

                            removeMedia(oldImg);

                        }

                        jQuery('#file_upload_doc').val(data.files);

                        jQuery('#file_upload_prev').attr('href', data.files_url);

                        jQuery('#file_upload_prev').removeClass('hide');

                        jQuery('.remove-file').addClass('i-block').removeClass('hide');



                        console.log(data.response_message);



                    } else {

                        console.log(data.response_message);

                    }

                },

                error: function (jqXHR, textStatus, errorThrown) {

                    jQuery('#' + id).parent().parent().find('.uneditable-input').removeClass('file-loader');

                    jQuery('#' + id).parent().parent().find('.uneditable-input').addClass('iconfa-warning-sign upload-error');

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

    } else {



        if (oldImg != "") {

            removeMedia(oldImg);

        }

        jQuery('#file_upload_doc').val('');

        jQuery('#file_upload_prev').attr('href', '#');

        jQuery('#file_upload_prev').addClass('hide');

        jQuery('.remove-file').removeClass('i-block').addClass('hide');

    }

}




function removeFile(e) {
    console.log(e);
    e.stopImmediatePropagation();

    jConfirm('Are You Sure , You Want <lw-c>To</lw-c> Delete ?', 'Confirmation', function (r) {



        if (r === true) {

            var target = e.target;

            var id = target.getAttribute("data-remove");

            var fileName = jQuery('#' + id + '_doc').val();

            var oldImg = jQuery('#file_upload_doc').val();



            jQuery('#' + id).parent().parent().find('.uneditable-input').removeClass('iconfa-warning-sign upload-error');



            if (oldImg != "") {

                removeMedia(oldImg)

            }

            jQuery('#file_upload_doc').val('');

            jQuery('#file_upload_prev').attr('href', '#');

            jQuery('#file_upload_prev').addClass('hide');

            jQuery('.remove-file').removeClass('i-block').addClass('hide');

            jQuery('.fileupload-preview').html('');

        }

    })



}



function removeMedia(docName) {



    let form_data2 = new FormData();

    form_data2.append('docs[]', docName);

    jQuery.ajax({

        url: RouteBasePath + "/remove-docs",
        // url: "{{ route('remove-docs') }}",

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