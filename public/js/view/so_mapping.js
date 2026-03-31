
// setTimeout(() => {
//     jQuery('#so_mapping_sequence').focus();
// }, 100);
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;


var so_data = [];


if (getItem.length) {

    var productDrpHtml = `<option value="">Select Item</option>`;
    for (let indx in getItem[0]) {

        productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;

    }

}



var formId = jQuery('#commonSOmapping').find('input:hidden[name="id"]').val();
if (formId != undefined && formId != "") {
    jQuery(document).ready(function () {
        let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };
        jQuery("div#hide_details").hide();
        jQuery('#show-progress').addClass('loader-progress-whole-page');

        jQuery.ajax({
            url: RouteBasePath + "/get-so_mapping/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    // setTimeout(() => {
                    //     jQuery('#so_mapping_sequence').focus();
                    // }, 100);
                    jQuery("#so_mapping_sequence").val(data.so_mapping.so_mapping_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery("#so_mapping_number").val(data.so_mapping.so_mapping_number).prop({ tabindex: -1, readonly: true });
                    jQuery("#so_mapping_date").val(data.so_mapping.mapping_date);
                    jQuery("#cre_detail_id").val(data.so_mapping.cre_detail_id);

                    // getCustomer();
                    // setTimeout(() => {
                    //     jQuery("#customer").val(data.so_mapping.customer_name).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                    //     fillPendingMapping();
                    // }, 500);

                    var suppHtml = '';
                    for (let indx in data.get_customer) {
                        suppHtml += `<option value="${data.get_customer[indx].customer_name}">${data.get_customer[indx].customer_name}</option>`;

                    }
                    jQuery('#customer').empty().append(suppHtml).trigger('liszt:updated')

                    jQuery("#customer").val(data.so_mapping.customer_name).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
                    jQuery('.toggleModalBtn').prop('disabled', true);

                    jQuery("#mapping_item_id").empty().append(productDrpHtml).trigger('liszt:updated');
                    jQuery("#mapping_item_id").val(data.so_mapping.item_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
                    // jQuery("#mapping_item_details_id").val(data.so_mapping.item_details_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                    jQuery("#mapping_item_details_name").val(data.so_mapping.secondary_item_name).prop({ tabindex: -1 }).attr('readonly', true);
                    jQuery("#mapping_item_details_id").val(data.so_mapping.item_details_id);
                    jQuery("#secondary_qty").val(data.so_mapping.secondary_qty);


                    if (data.so_mapping.item_details_id == "" || data.so_mapping.item_details_id == null) {
                        jQuery("div#hide_details").hide();
                    } else {
                        jQuery("div#hide_details").show();

                    }


                    jQuery("#mapping_unit").val(data.so_mapping.unit_name);
                    jQuery("#pend_return_qty").val(parseFloat(data.so_mapping.pend_return_qty).toFixed(3));
                    jQuery("#sp_notes").val(data.so_mapping.special_notes);

                    // setTimeout(() => {
                    //     // jQuery('#so_mapping_date').focus();
                    //     jQuery('#customer').trigger('liszt:activate');
                    // }, 100);


                    if (data.so_mapping.in_use == true) {
                        jQuery('#so_mapping_sequence').prop({ tabindex: -1, readonly: true });
                        jQuery('#so_mapping_date').prop({ tabindex: -1, readonly: true });
                    }

                    if (data.so_data.length > 0 && !jQuery.isEmptyObject(data.so_data)) {

                        for (let ind in data.so_data) {
                            so_data.push(data.so_data[ind]);
                        }

                        fillSOData();

                    }
                    jQuery('#show-progress').removeClass('loader-progress-whole-page');

                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-so_mapping";
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


    });

} else {
    jQuery(document).ready(function () {
        getLatestMappingNo();
        getCustomer();
        jQuery("div#hide_details").hide();
        setTimeout(() => {
            // jQuery('#so_mapping_date').focus();
            jQuery('#customer').trigger('liszt:activate');
        }, 100);
    });
}





// check duplicate serial number
// check duplication 
jQuery('#so_mapping_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();

    if (subBtn == "submit" || subBtn == "Submit") {
        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }

    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid Sr. No.');
            jQuery('#so_mapping_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery('#so_mapping_sequence').focus();
            jQuery('#so_mapping_sequence').val('');

        } else {
            jQuery("#submitBtn").attr('disabled', true);
            jQuery('#so_mapping_sequence').parent().parent().parent('div.control-group').removeClass('error');
            var urL = RouteBasePath + "/check-mapping_no_duplication?for=add&mapping_sequence=" + val;
            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-mapping_no_duplication?for=edit&mapping_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({
                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#so_mapping_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        setTimeout(() => {
                            jQuery('#so_mapping_sequence').focus();
                        }, 1000);
                        jQuery('#so_mapping_sequence').val('');
                    } else {
                        jQuery('#so_mapping_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#so_mapping_number').val(data.latest_po_no);
                        jQuery('#so_mapping_sequence').val(val);
                    }
                    jQuery("#submitBtn").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#so_mapping_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')
                }
            });
        }
    } else {
        jQuery('#so_mapping_number').val('');
        jQuery('#so_mapping_sequence').val('');
    }
});
// end check duplication


function getLatestMappingNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_so_mapping_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#so_mapping_number').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#so_mapping_number').val(data.latest_mapping_no).prop({ tabindex: -1, readonly: true });
                jQuery('#so_mapping_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
                jQuery('#so_mapping_date').val(currentDate);
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#so_mapping_number').removeClass('file-loader');
            console.log('Failed To Get Latest RRM No.!')
        }
    });
}



function getCustomer() {
    var urL = RouteBasePath + "/get-customer_for_replacement";
    // if (formId !== undefined) { //if form is edit
    //     urL = RouteBasePath + "/get-customer_for_replacement?id=" + formId;
    // }
    jQuery.ajax({
        url: urL,
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            let suppHtml = '';
            suppHtml += `<option value="">Select Customer</option> `;
            if (data.response_code == 1) {
                for (let indx in data.get_customer) {
                    suppHtml += `<option value="${data.get_customer[indx].customer_name}">${data.get_customer[indx].customer_name}</option>`;

                }
                jQuery('#customer').empty().append(suppHtml).trigger('liszt:updated')

            } else {
                console.log(data.response_message)
            }
        },
    });
}



function fillPendingMapping() {
    let cusId = jQuery('#customer option:selected').val();

    var thisModal = jQuery('#pendingPoModal');
    var thisForm = jQuery('#GrnDetailsForm');

    if (cusId != "") {


        // if (formId == undefined) {
        //     var Url = RouteBasePath + "/get-replacement_list-mapping?customer=" + cusId;
        // } else {
        //     var Url = RouteBasePath + "/get-replacement_list-mapping?customer=" + cusId + "&id=" + formId;
        // }
        var Url = RouteBasePath + "/get-replacement_list-mapping?customer=" + cusId;
        jQuery.ajax({
            url: Url,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1 && data.replacement_data.length > 0) {
                    // new code
                    // var usedParts = [];
                    // var totalDisb = 0;
                    // var found = 0;
                    // thisForm.find('#grnDetails tbody input[name="form_indx"]').each(function (indx) {
                    //     let frmIndx = jQuery(this).val();
                    //     let jbEorkOrderId = po_data[frmIndx].po_details_id;
                    //     if (jbEorkOrderId != "" && jbEorkOrderId != null) {
                    //         usedParts.push(Number(jbEorkOrderId));
                    //     }
                    // });

                    // function isUsed(pjId) {
                    //     if (usedParts.includes(Number(pjId))) {
                    //         totalDisb++;
                    //         return true;
                    //     }
                    //     return false;
                    // }

                    // let totalEntry = 0;
                    var tblHtml = ``;
                    // var found = 0;

                    // end new code

                    if (data.replacement_data.length > 0 && !jQuery.isEmptyObject(data.replacement_data)) {
                        found = 1;
                        for (let idx in data.replacement_data) {
                            // var inUse = isUsed(data.replacement_data[idx].po_details_id);
                            // totalEntry++;
                            tblHtml += `<tr>
                                        <td><input type="radio" name="cre_detail_id[]" class="simple-check" id="cre_detail_ids_${data.replacement_data[idx].cre_detail_id}" 
                                        value="${data.replacement_data[idx].cre_detail_id}" /></td>                                        
                                        <td>${data.replacement_data[idx].cre_number}</td>
                                        <td>${data.replacement_data[idx].cre_date}</td>
                                        <td>${data.replacement_data[idx].item_name}</td>
                                        <td>${checkInputNull(data.replacement_data[idx].secondary_item_name)}</td>
                                        <td>${data.replacement_data[idx].item_code}</td>
                                        <td>${data.replacement_data[idx].item_group_name}</td>                                      
                                        <td>${data.replacement_data[idx].return_qty != null ? parseFloat(data.replacement_data[idx].return_qty).toFixed(3) : ""}</td>
                                        <td>${parseFloat(data.replacement_data[idx].pend_return_qty).toFixed(3)}</td>                                 
                                        <td>${data.replacement_data[idx].unit_name}</td>
                                        </tr>`;
                        }
                    } else {
                        tblHtml += `<tr class="centeralign" id="noPendingPo">
                            <td colspan="5">No Pending Customer Replacement</td>
                        </tr>`;
                    }
                    jQuery('#customerReplacementTable tbody').empty().append(tblHtml);

                    jQuery('.toggleModalBtn').prop('disabled', false);

                } else {
                    jQuery('.toggleModalBtn').prop('disabled', true);
                    toastError(data.response_message);

                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                //    resetPdCoaForm();
                jQuery('.toggleModalBtn').prop('disabled', true);
                var errMessage = JSON.parse(jqXHR.responseText);
                if (jqXHR.status == 401) {
                    toastError(jqXHR.statusText);
                } else {
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });

    } else {

        //  resetPdCoaForm();

        jQuery('.toggleModalBtn').prop('disabled', true);

    }

}





// modal validator
var coaPartValidator = jQuery("#customerReplacement").validate({
    rules: {
        "cre_detail_id[]": {
            required: true
        },
    },
    messages: {
        "cre_detail_id[]": {
            required: "Please Customer Replacement",
        }
    },

    submitHandler: function (form) {
        var modal = jQuery("#customerReplacementModal");


        var chkCount = 0;
        var chkArr = [];
        var chkId = [];

        jQuery("#customerReplacement").find("[id^='cre_detail_ids_']").each(function () {
            let thisId = jQuery(this).attr('id');
            let splt = thisId.split('cre_detail_ids_');
            let intId = splt[1];
            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });


        if (chkCount == 0) {
            toastError('Please Customer Replacement');
        } else {

            var customer = jQuery('#customer option:selected').val();
            jQuery.ajax({
                url: RouteBasePath + "/get-pending_customer_replacement?customer=" + customer + "&cre_details_ids=" + chkArr.join(','),
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.item.length > 0) {
                            for (let indx in data.item) {
                                productDrpHtml += `<option value="${data.item[indx].id}">${data.item[indx].item_name} </option>`;
                            }
                            jQuery("#mapping_item_id").empty().append(productDrpHtml).trigger('liszt:updated');
                        }

                        jQuery("#mapping_unit").val(data.replacement_data.unit_name);
                        jQuery("#pend_return_qty").val(parseFloat(data.replacement_data.pend_return_qty).toFixed(3));
                        jQuery("#mapping_item_id").empty().append(productDrpHtml).trigger('liszt:updated');
                        jQuery("#mapping_item_id").val(data.replacement_data.item_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery("#mapping_item_details_name").val(data.replacement_data.secondary_item_name).prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery("#mapping_item_details_id").val(data.replacement_data.item_details_id);
                        jQuery("#secondary_qty").val(data.replacement_data.secondary_qty);
                        jQuery("#cre_detail_id").val(data.replacement_data.cre_detail_id);
                        modal.modal("hide");
                        if (data.replacement_data.item_details_id == "" || data.replacement_data.item_details_id == null) {
                            jQuery("div#hide_details").hide();
                        } else {
                            jQuery("div#hide_details").show();

                        }

                        if (data.so_data.length > 0 && !jQuery.isEmptyObject(data.so_data)) {
                            so_data = [];
                            for (let ind in data.so_data) {
                                so_data.push(data.so_data[ind]);
                            }

                            fillSOData();

                        } else {
                            jQuery('#SOMappingTable tbody').empty();
                        }
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

jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) >= 0.001;
});


var validator = jQuery("#commonSOmapping").validate({
    onkeyup: false,
    onfocusout: false,
    rules: {

        so_mapping_sequence: {
            required: true
        },
        so_mapping_date: {
            required: true,
            date_check: true,
            dateFormat: true,
        },
        customer: {
            required: true,
        },
        mapping_item_id: {
            required: true,
        },
        'map_qty[]': {
            required: function (e) {
                if (jQuery(e).prop('readonly')) {
                    return false;
                } else {
                    return true;
                }
            },
            notOnlyZero: '0.001'
        },
        'item_detail_qty[]': {
            required: function (e) {
                if (jQuery(e).prop('readonly')) {
                    return false;
                } else {
                    return true;
                }
            },
            notOnlyZero: '0.001'
        }

    },

    messages: {

        so_mapping_sequence: {
            required: "Please Enter Sr. No."
        },
        so_mapping_date: {
            required: 'Please Enter  Date',
        },
        customer: {
            required: 'Please Select Customer',
        },
        mapping_item_id: {
            required: 'Please Select Item',
        },
        'map_qty[]': {
            required: "Please Enter Map Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.001'
        },
        'item_detail_qty[]': {
            required: "Please Enter Item Detail Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.001'
        }


    },

    submitHandler: function (form) {

        so_details_data = [];
        var index = 0;

        // main table loop 

        jQuery('#SOMappingTable tbody tr').each(function (e) {
            var so_detail_id = jQuery(this).find('input[name="so_detail_id[]"]');
            if (jQuery(so_detail_id).is(':checked')) {
                so_detail_id = jQuery(so_detail_id).val();
                mapQty = jQuery(this).find('input[name="map_qty[]"]').val();
                so_mapping_detail = jQuery(this).find('input[name="so_mapping_detail[]"]').val();
                mapping_detail_qty = jQuery(this).find('input[name="item_detail_qty[]"]').val();

                so_details_data[index] = { 'so_detail_id': so_detail_id, 'map_qty': mapQty, 'so_mapping_detail': so_mapping_detail, 'mapping_detail_qty': mapping_detail_qty };
                index++;
            }

        });

        if (!jQuery.isEmptyObject(so_details_data)) {
            let data = new FormData(document.getElementById('commonSOmapping'));
            let formValue = Object.fromEntries(data.entries());
            delete formValue["so_detail_id[]"];
            delete formValue["map_qty[]"];
            delete formValue["so_mapping_detail[]"];

            formValue = Object.assign(formValue, { 'so_details': JSON.stringify(so_details_data) });
            var formdata = new URLSearchParams(formValue).toString();

            var pendReturnQty = jQuery('#pend_return_qty').val();
            var totalMappQty = jQuery('.mappqtysum').text();


            var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-so_mapping" : RouteBasePath + "/store-so_mapping";

            if (parseFloat(totalMappQty) <= parseFloat(pendReturnQty)) {
                jQuery.ajax({
                    url: formUrl,
                    type: 'POST',
                    data: formdata,
                    headers: headerOpt,
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {

                            if (formId != undefined && formId != "") {
                                toastSuccess(data.response_message, function (r) {
                                    window.location.href = RouteBasePath + "/manage-so_mapping";
                                });
                            }
                            else {
                                toastSuccess(data.response_message, redirectFn);
                                function redirectFn() {
                                    window.location.reload();
                                }
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
            } else {
                toastError('Total Map Qty. Should Not More Than Pend. Return Qty.')
            }

        } else {
            toastError('Please Select At Least One SO No.')
        }
    }
});



function fillSOData() {

    if (so_data.length > 0) {
        var tblHtml = '';
        for (let key in so_data) {
            var so_no = so_data[key].so_number ? so_data[key].so_number : '';
            var so_date = so_data[key].so_date ? so_data[key].so_date : '';
            var so_qty = so_data[key].so_qty ? parseFloat(so_data[key].so_qty).toFixed(3) : '';

            if (formId == undefined) {
                var pend_so_map_qty = so_data[key].pend_so_map_qty ? parseFloat(so_data[key].pend_so_map_qty).toFixed(3) : parseFloat(0).toFixed(3);
            } else {
                // var pend_so_map_qty = so_data[key].show_pend_qty ? parseFloat(so_data[key].show_pend_qty + so_data[key].so_mapp_qty).toFixed(3) : parseFloat(0).toFixed(3);
                // var pend_so_map_qty = parseFloat(so_data[key].show_pend_qty + so_data[key].so_mapp_qty).toFixed(3);

                var pend_so_map_qty = parseFloat(so_data[key].pend_so_map_qty + so_data[key].so_mapp_qty).toFixed(3);
                var show_pend_qty = parseFloat(so_data[key].show_pend_qty + so_data[key].so_mapp_qty).toFixed(3);
            }
            var unit = so_data[key].unit_name ? so_data[key].unit_name : '';

            var pend_return_qty = jQuery("#pend_return_qty").val() != '' ? jQuery("#pend_return_qty").val() : '';

            // var total_mapp_qty = parseFloat(pend_return_qty) < parseFloat(pend_so_map_qty) ? pend_return_qty : pend_so_map_qty;


            if (formId == undefined) {
                var total_mapp_qty = parseFloat(pend_return_qty) < parseFloat(pend_so_map_qty) ? pend_return_qty : pend_so_map_qty;
            } else {
                // var total_qty = parseFloat(pend_so_map_qty) + parseFloat(so_data[key].so_mapp_qty);
                var total_qty = parseFloat(pend_so_map_qty);
                var total_mapp_qty = parseFloat(pend_return_qty) < parseFloat(total_qty) ? pend_return_qty : total_qty;
            }

            var secondary_qty = jQuery('#secondary_qty').val();

            var detail_total_mapp_qty = secondary_qty != '' ? parseFloat(total_mapp_qty) / parseFloat(secondary_qty) : total_mapp_qty;

            var so_mapping_detail = so_data[key].so_mapping_detail ? so_data[key].so_mapping_detail : 0;

            tblHtml += `<tr>
            <td><input type="checkbox" class="simple-check ${so_data[key].in_use == true ? 'in-use' : ''}"  name="so_detail_id[]" id="so_detail_ids_${so_data[key].so_details_id}" value="${so_data[key].so_details_id}" onchange="manageQtyfield(this)" ${formId != undefined ? so_data[key].so_details_id == so_data[key].so_mapping_so_detail ? 'checked' : '' : ''} ${formId != undefined ? so_data[key].in_use == true ? 'readonly' : '' : ''}${key == 0 ? "autofocus" :""}/>            
            </td>     
                <td>${so_no}<input type="hidden" name="so_mapping_detail[]" id="so_mapping_detail" value="${so_mapping_detail}"></td>
            <td>${so_date}</td>            
            <td>${parseFloat(so_qty).toFixed(3)}</td>`;

            if (formId == undefined) {
                tblHtml += `  <td>${parseFloat(pend_so_map_qty).toFixed(3)}</td>
                <td><input type="text" max="${detail_total_mapp_qty}" name="item_detail_qty[]" id="item_detail_qty_${so_data[key].so_details_id}"  class="input-mini  only-numbers " onKeyup="calSecondQty(this)" readonly /></td>
                <td><input type="text" max="${parseFloat(total_mapp_qty).toFixed(3)}" name="map_qty[]" id="map_qty_${so_data[key].so_details_id}" onblur="formatPoints(this,3)" class="input-mini isNumberKey map_qty" onKeyup="sumSoMapQty(this)" readonly /></td>`;
            } else {
                tblHtml += `<td>${parseFloat(show_pend_qty).toFixed(3)}</td>`;

                if (jQuery("#mapping_item_details_id").val() != "") {
                    tblHtml += ` <td><input type="text" max="${detail_total_mapp_qty}" name="item_detail_qty[]" id="item_detail_qty_${so_data[key].so_details_id}" class="input-mini  only-numbers "value="${so_data[key].so_mapp_detail_qty > 0 ? so_data[key].so_mapp_detail_qty : ''}" onKeyup="calSecondQty(this)"   min="${so_data[key].used_detail_qty > 0 ? so_data[key].used_detail_qty : ''}"/></td>`;


                    tblHtml += ` <td><input type="text" max="${parseFloat(total_mapp_qty).toFixed(3)}" name="map_qty[]" id="map_qty_${so_data[key].so_details_id}" onblur="formatPoints(this,3)" class="input-mini isNumberKey map_qty" onKeyup="sumSoMapQty(this)" value="${so_data[key].so_mapp_qty > 0 ? parseFloat(so_data[key].so_mapp_qty).toFixed(3) : ''}"  min="${so_data[key].used_qty > 0 ? parseFloat(so_data[key].used_qty).toFixed(3) : ''}" readonly/></td > `;
                } else {
                    tblHtml += ` <td><input type="text" max="${detail_total_mapp_qty}" name="item_detail_qty[]" id="item_detail_qty_${so_data[key].so_details_id}"  class="input-mini  only-numbers " onKeyup="calSecondQty(this)" readonly /></td>`;
                    tblHtml += ` <td><input type="text" max="${parseFloat(total_mapp_qty).toFixed(3)}" name="map_qty[]" id="map_qty_${so_data[key].so_details_id}" onblur="formatPoints(this,3)" class="input-mini isNumberKey map_qty" onKeyup="sumSoMapQty(this)"   ${so_data[key].so_details_id == so_data[key].so_mapping_so_detail ? '' : 'readonly'} value="${so_data[key].so_mapp_qty > 0 ? parseFloat(so_data[key].so_mapp_qty).toFixed(3) : ''}"  min="${so_data[key].used_qty > 0 ? parseFloat(so_data[key].used_qty).toFixed(3) : ''}"  ${parseFloat(so_data[key].so_mapp_qty).toFixed(3) == parseFloat(so_data[key].used_qty).toFixed(3) ? 'readonly' : ''}/></td > `;
                }



            }
            tblHtml += `<td> ${unit}</td>                              
            </tr > `;


        }
        jQuery('#SOMappingTable tbody').empty().append(tblHtml);

        sumSoMapQty()
    }
}


jQuery('#checkall-so').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#SOMappingTable").find("[id^='so_detail_ids_']:not(.in-use)").prop('checked', true).trigger('change');

        // if (jQuery("#mapping_item_details_id").val() != "") {
        //     jQuery("#SOMappingTable").find("[id^='item_detail_qty_']").prop('readonly', false);
        // } else {
        //     jQuery("#SOMappingTable").find("[id^='map_qty_']").prop('readonly', false);
        // }
    } else {
        jQuery("#SOMappingTable").find("[id^='so_detail_ids_']:not(.in-use)").prop('checked', false).trigger('change');
        // if (jQuery("#mapping_item_details_id").val() != "") {
        //     jQuery("#SOMappingTable").find("[id^='item_detail_qty_']").val('').prop('readonly', true);
        // } else {
        //     jQuery("#SOMappingTable").find("[id^='map_qty_']").val('').prop('readonly', true);
        // }


    }
});



function manageQtyfield($this) {
    var mapQtyField = jQuery($this).parent('td').parent('tr').find('input[name="map_qty[]"]');
    var mapitemdetailsQtyField = jQuery($this).parent('td').parent('tr').find('input[name="item_detail_qty[]"]');


    if (jQuery("#mapping_item_details_id").val() != "") {
        jQuery(mapQtyField).prop('readonly', true);
        if (jQuery(mapitemdetailsQtyField).prop('readonly')) {
            jQuery(mapitemdetailsQtyField).prop('readonly', false);
        } else {
            jQuery(mapitemdetailsQtyField).val('').trigger('change').prop('readonly', true);
        }
    } else {
        if (jQuery(mapQtyField).prop('readonly')) {
            jQuery(mapQtyField).prop('readonly', false);
        } else {
            jQuery(mapQtyField).val('').trigger('change').prop('readonly', true);
            sumSoMapQty($this)

        }
    }
}



function sumSoMapQty(th) {
    var total = 0;
    var total2 = 0;
    jQuery('.map_qty').map(function () {
        var total1 = jQuery(this).val();
        if (total1 != "") {
            total = parseFloat(total) + parseFloat(total1);
        }
    });



    total != 0 && total != "" ? jQuery('.mappqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.mappqtysum').text('');


}



function calSecondQty(th) {

    var map_details_qty = jQuery(th).closest('tr').find("input[name='item_detail_qty[]']").val();


    var second_qty = jQuery('#secondary_qty').val();

    var mapQty = 0;
    if (map_details_qty != "" && second_qty != "") {
        mapQty = parseFloat(map_details_qty) * parseFloat(second_qty);
    }

    jQuery(th).parents('tr').find("input[name='map_qty[]']").val(mapQty.toFixed(3));
    sumSoMapQty();

}