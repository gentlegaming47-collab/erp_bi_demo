setTimeout(() => {
    jQuery('#vehicle_no').focus();
}, 100);
var loading_data = [];
var dispatchDetailArray = [];
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;




if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}

var formId = jQuery('#commonLoadingEntryForm').find('input:hidden[name="id"]').val();

jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };
    //  edit code
    if (formId != null && formId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({
            url: RouteBasePath + "/get-loading_entry/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    jQuery('#dp_number').prop({ tabindex: -1, readonly: true });
                    jQuery('#dp_date').prop({ tabindex: -1, readonly: true });

                    jQuery("#vehicle_no").val(data.le_data.vehicle_no)
                    jQuery("#transporter").val(data.le_data.transporter_id).trigger('liszt:updated');
                    jQuery("#loading_by").val(data.le_data.loading_by)
                    jQuery("#driver_name").val(data.le_data.driver_name)
                    jQuery("#driver_no").val(data.le_data.driver_mobile_no)

                    if (data.le_details.length > 0 && !jQuery.isEmptyObject(data.le_details)) {
                        for (let ind in data.le_details) {

                            jQuery('#Dpid').val(data.le_details[ind].dp_id);

                            if (data.le_data.dp_number != null) {
                                jQuery('#dp_number').val(data.le_data.dp_number).prop({ tabindex: -1, readonly: true });
                            } else {
                                jQuery('#dp_number').val(data.le_details[ind].dp_number).prop({ tabindex: -1, readonly: true });
                            }
                            jQuery('#dp_date').val(data.le_details[ind].dp_date).prop({ tabindex: -1, readonly: true });
                            loading_data.push(data.le_details[ind]);
                        }

                        fillLoadingPartTable(data.le_details);
                    }

                    if (!jQuery.isEmptyObject(data.dispatch_detail_data)) {
                        dispatchDetailArray = data.dispatch_detail_data;
                    }



                    if (data.le_data.in_use == true) {
                        jQuery("#vehicle_no").prop({ tabindex: -1, readonly: true });
                        jQuery("#transporter").prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery("#loading_by").prop({ tabindex: -1, readonly: true });
                        jQuery("#driver_name").prop({ tabindex: -1, readonly: true });
                        jQuery("#driver_no").prop({ tabindex: -1, readonly: true });
                        jQuery('.toggleModalBtn').prop('disabled', true);
                    } else {
                        fillPendingDispatchPlan();
                    }

                    jQuery('#show-progress').removeClass('loader-progress-whole-page');

                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-dispatch_plan";
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
        jQuery(document).ready(function () {  // at add time get the se. number            
            fillPendingDispatchPlan();
            jQuery('#dp_number').prop({ tabindex: -1, readonly: true });
            jQuery('#dp_date').prop({ tabindex: -1, readonly: true });
        });
    }
});


function fillPendingDispatchPlan() {

    var thisModal = jQuery('#pendingDispatchPlanForm');
    var thisForm = jQuery('#commonLoadingEntryForm');

    if (formId == undefined) {
        var Url = RouteBasePath + "/get-dispatch_list-loading-entry";
    } else {
        var Url = RouteBasePath + "/get-dispatch_list-loading-entry?id=" + formId;
    }

    jQuery.ajax({

        url: Url,

        type: 'GET',

        headers: headerOpt,

        dataType: 'json',

        processData: false,

        success: function (data) {

            if (data.response_code == 1) {

                // new code
                var usedParts = [];
                var totalDisb = 0;
                var found = 0;

                thisForm.find('#LoadingEntryTable tbody input[name="form_indx"]').each(function (indx) {
                    let frmIndx = jQuery(this).val();
                    let dpId = loading_data[frmIndx].dp_details_id;

                    if (dpId != "" && dpId != null) {
                        usedParts.push(Number(dpId));
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
                var tblHtml = ``;
                var found = 0;

                // end new code

                if (data.dp_data.length > 0 && !jQuery.isEmptyObject(data.dp_data)) {

                    found = 1;

                    for (let idx in data.dp_data) {

                        let inUse = isUsed(data.dp_data[idx].dp_id);
                        totalEntry++;
                        tblHtml += `<tr>
                                        <td><input type="radio" name="pd_dp_id[]" class="simple-check ${inUse ? 'in-use' : ''}" id="pd_dp_id_${data.dp_data[idx].dp_id}" 
                                        value="${data.dp_data[idx].dp_id}" ${inUse ? 'checked' : ''} /></td>
                                        <td>${data.dp_data[idx].dp_number}</td>                                       
                                        <td>${data.dp_data[idx].dp_date}</td>                                       
                                        <td>${data.dp_data[idx].dealer_name}</td>                                       
                                        <td>${data.dp_data[idx].special_notes != null ? data.dp_data[idx].special_notes : ""}</td>                                     
                                        </tr>`;

                    }

                } else {

                    tblHtml += `<tr class="centeralign" id="noPendingPo">

                            <td colspan="5">No Dispatch PO Available</td>

                        </tr>`;

                }

                jQuery('#pendingDispatchPlanTable tbody').empty().append(tblHtml);





                jQuery('.toggleModalBtn').prop('disabled', false);







            } else {

                // resetPdCoaForm();

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


}




var validator = jQuery("#pendingDispatchPlanForm").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,
    rules: {
        "pd_dp_id[]": {
            required: true,
        },
    },
    messages: {
        "pd_dp_id[]": {
            required: "Please Select Pending Dispatch Paln",
        },

    },
    submitHandler: function (form) {

        var chkCount = 0;
        var chkArr = [];
        var chkId = [];
        jQuery("#pendingDispatchPlanForm").find("[id^='pd_dp_id_']").each(function () {
            let thisId = jQuery(this).attr('id');
            let splt = thisId.split('pd_dp_id_');
            let intId = splt[1];

            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });

        if (chkCount == 0) {
            toastError('Please Select Pending Dispatch Paln');

        } else {

            if (formId == undefined) {

                var url = RouteBasePath + "/get-dispatch_plan_data?dpids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-dispatch_plan_data?dpids=" + chkArr.join(',') + "&id=" + formId;
            }


            jQuery.ajax({

                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.dispatch_data.length > 0 && !jQuery.isEmptyObject(data.dispatch_data)) {
                            for (let ind in data.dispatch_data) {
                                jQuery('#Dpid').val(data.dispatch_data[ind].dp_id);

                                jQuery('#dp_number').val(data.dispatch_data[ind].dp_number).prop({ tabindex: -1, readonly: true });

                                jQuery('#dp_date').val(data.dispatch_data[ind].dp_date).prop({ tabindex: -1, readonly: true });

                                loading_data.push(data.dispatch_data[ind]);

                            }
                            fillLoadingPartTable(data.dispatch_data);
                        }

                        if (!jQuery.isEmptyObject(data.dispatch_detail_data)) {
                            dispatchDetailArray = data.dispatch_detail_data;
                        }
                        jQuery("#pendingDispatchPlan").modal('hide');

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


// function fillLoadingPartTable($fillData = null) {

//     if ($fillData != null && $fillData.length > 0) {

//         jQuery('#LoadingEntryTable tbody').empty();
//         for (let key in $fillData) {

//             let formIndx = loading_data.indexOf($fillData[key]);

//             var sr_no = loading_data.indexOf($fillData[key]) + 1;

//             var so_no = $fillData[key].so_number ? $fillData[key].so_number : "";
//             var so_date = $fillData[key].so_date ? $fillData[key].so_date : "";
//             var name = $fillData[key].name ? $fillData[key].name : "";
//             var customer_village = $fillData[key].customer_village ? $fillData[key].customer_village : "";
//             var district_name = $fillData[key].district_name ? $fillData[key].district_name : "";
//             var dealer_name = $fillData[key].dealer_name ? $fillData[key].dealer_name : "";
//             var item_name = $fillData[key].item_name ? $fillData[key].item_name : "";
//             var item_code = $fillData[key].item_code ? $fillData[key].item_code : "";
//             var item_group_name = $fillData[key].item_group_name ? $fillData[key].item_group_name : "";
//             var unit_name = $fillData[key].unit_name ? $fillData[key].unit_name : "";
//             var item_name = $fillData[key].item_name ? $fillData[key].item_name : "";
//             var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
//             var dp_id = $fillData[key].dp_id ? $fillData[key].dp_id : "";
//             var dp_details_id = $fillData[key].dp_details_id ? $fillData[key].dp_details_id : "";
//             var le_details_id = formId == undefined ? 0 : $fillData[key].le_details_id != null ? $fillData[key].le_details_id : 0;

//             var pending_so_qty = $fillData[key].pending_so_qty > 0 ? parseFloat($fillData[key].pending_so_qty).toFixed(3) : parseFloat(0).toFixed(3);
//             var plan_qty = $fillData[key].plan_qty > 0 ? parseFloat($fillData[key].plan_qty).toFixed(3) : parseFloat(0).toFixed(3);

//             if (plan_qty > 0) {
//                 var maxQty = (parseFloat(plan_qty) + parseFloat($fillData[key].pending_org_qty)).toFixed(3);
//             } else {
//                 var maxQty = parseFloat(0).toFixed(3);
//             }

//             var totalQty = parseFloat(plan_qty) + parseFloat(pending_so_qty);

//             // if (formId == undefined) {
//             //     var maxQty = plan_qty;

//             // } else {
//             //     var maxtotalQty = parseFloat(plan_qty) + parseFloat(pending_so_qty);

//             //     //     console.log(maxtotalQty)
//             //     var maxQty = parseFloat(maxtotalQty).toFixed(3)
//             //     var pending_so_qty = maxQty;
//             // }

//             var tblHtml = ``;

//             if (formId == undefined) {
//                 if ($fillData[key].fitting_item == 'no') {
//                     tblHtml += `<tr>
//                             <td>
//                             <a onclick="editLoadingPart(this)"><i class="iconfa-pencil action-icon edit-coa_part_details"></i></a>
//                             <a onclick="removeLoadingPart(this)"><i class="action-icon iconfa-trash remove-spi_part"></i></a></td>
//                             <td class="sr_no"></td>`;
//                 } else {
//                     tblHtml += `<tr>
//                             <td>
//                             <a><i class="iconfa-pencil action-icon edit-coa_part_details"></i></a>
//                             <a onclick="removeLoadingPart(this)"><i class="action-icon iconfa-trash remove-spi_part"></i></a></td>
//                             <td class="sr_no"></td>`;
//                 }
//             } else {
//                 if ($fillData[key].fitting_item == 'no') {
//                     if (parseFloat($fillData[key].used_qty).toFixed(3) == plan_qty) {
//                         tblHtml += `<tr>
//                         <td>
//                         <a><i class="iconfa-pencil action-icon edit-coa_part_details"></i></a>
//                         <a onclick="removeLoadingPart(this)"><i class="action-icon iconfa-trash remove-spi_part"></i></a></td>
//                         <td class="sr_no"></td>`;
//                     } else {
//                         tblHtml += `<tr>
//                         <td>
//                         <a onclick="editLoadingPart(this)"><i class="iconfa-pencil action-icon edit-coa_part_details"></i></a>
//                         <a onclick="removeLoadingPart(this)"><i class="action-icon iconfa-trash remove-spi_part"></i></a></td>
//                         <td class="sr_no"></td>`;

//                     }
//                 } else {
//                     tblHtml += `<tr>
//                             <td>
//                             <a><i class="iconfa-pencil action-icon edit-coa_part_details"></i></a>
//                             <a onclick="removeLoadingPart(this)"><i class="action-icon iconfa-trash remove-spi_part"></i></a></td>
//                             <td class="sr_no"></td>`;
//                 }
//             }


//             tblHtml += `<td>
//                         <input type="hidden" name="fiting_item[]" value="${$fillData[key].fitting_item}">
//                         <input type="hidden" name="dp_id[]" value="${dp_id}">
//                         <input type="hidden" name="le_details_id[]" value="${le_details_id}">
//                         <input type="hidden" name="form_indx" value="${formIndx}"/>
//                         <input type="hidden" name="dp_details_id[]" value="${dp_details_id}"/>
//                         ${so_no}<input type='hidden' name='so_no[]' value="${so_no}"/>
//                         </td>`;
//             tblHtml += `<td>${so_date}<input type='hidden' name='so_date[]' value="${so_date}"/></td>`;
//             tblHtml += `<td>${name}<input type='hidden' name='name[]' value="${name}"/></td>`;
//             tblHtml += `<td>${customer_village}<input type='hidden' name='customer_village[]' value="${customer_village}"/></td>`;
//             tblHtml += `<td>${district_name}<input type='hidden' name='district_name[]' value="${district_name}"/></td>`;
//             tblHtml += `<td>${dealer_name}<input type='hidden' name='dealer_name[]' value="${dealer_name}"/></td>`;
//             tblHtml += `<td>${item_name}<input type='hidden' name='item_id[]' value="${item_id}"/></td>`;
//             tblHtml += `<td>${item_code}<input type='hidden' name='item_code[]' value="${item_code}"/></td>`;
//             tblHtml += `<td>${item_group_name}<input type='hidden' name='group[]' value="${item_group_name}"/></td>`;
//             if (formId == undefined) {
//                 tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
//                 <input type="text" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" readonly   /></td>`;
//             } else {
//                 tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
//                 <input type="text" min="${$fillData[key].used_qty ? parseFloat($fillData[key].used_qty).toFixed(3) : 0.000}" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" readonly   /></td>`;
//             }

//             tblHtml += `<td>${unit_name}<input type='hidden' name='unit[]' value="${unit_name}"/></td>`;
//             // tblHtml += `<td>${pending_so_qty}<input type='hidden' name='pending_so_qty[]' value="${pending_so_qty}"/></td>`;
//             tblHtml += `<td>${parseFloat(totalQty).toFixed(3)}<input type='hidden' name='pending_so_qty[]' value="${parseFloat(totalQty).toFixed(3)}"/></td>`;
//             tblHtml += `</tr>`;

//             jQuery('#LoadingEntryTable tbody').append(tblHtml);

//             sumPlanQty();
//             srNo();

//         }

//     }

// }




function fillLoadingPartTable($fillData = null) {

    if ($fillData != null && $fillData.length > 0) {

        jQuery('#LoadingEntryTable tbody').empty();
        for (let key in $fillData) {

            let formIndx = loading_data.indexOf($fillData[key]);

            var sr_no = loading_data.indexOf($fillData[key]) + 1;

            var so_no = $fillData[key].so_number ? $fillData[key].so_number : "";
            var so_date = $fillData[key].so_date ? $fillData[key].so_date : "";
            var name = $fillData[key].name ? $fillData[key].name : "";
            var customer_village = $fillData[key].customer_village ? $fillData[key].customer_village : "";
            var district_name = $fillData[key].district_name ? $fillData[key].district_name : "";
            var dealer_name = $fillData[key].dealer_name ? $fillData[key].dealer_name : "";
            var item_name = $fillData[key].item_name ? $fillData[key].item_name : "";
            var item_code = $fillData[key].item_code ? $fillData[key].item_code : "";
            var item_group_name = $fillData[key].item_group_name ? $fillData[key].item_group_name : "";
            var unit_name = $fillData[key].unit_name ? $fillData[key].unit_name : "";
            var item_name = $fillData[key].item_name ? $fillData[key].item_name : "";
            var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
            var dp_id = $fillData[key].dp_id ? $fillData[key].dp_id : "";
            var dp_details_id = $fillData[key].dp_details_id ? $fillData[key].dp_details_id : "";
            var so_details_id = $fillData[key].so_details_id ? $fillData[key].so_details_id : "";
            var le_details_id = formId == undefined ? 0 : $fillData[key].le_details_id != null ? $fillData[key].le_details_id : 0;

            var pending_so_qty = $fillData[key].pending_so_qty > 0 ? parseFloat($fillData[key].pending_so_qty).toFixed(3) : parseFloat(0).toFixed(3);
            var plan_qty = $fillData[key].plan_qty > 0 ? parseFloat($fillData[key].plan_qty).toFixed(3) : parseFloat(0).toFixed(3);

            var so_from_value_fix = $fillData[key].so_from_value_fix ? $fillData[key].so_from_value_fix : "";

            var maxQty = (parseFloat(plan_qty) + parseFloat($fillData[key].pending_org_qty)).toFixed(3);

            var totalQty = parseFloat(plan_qty) + parseFloat(pending_so_qty);


            // if (so_from_value_fix == 'location' || so_from_value_fix == 'cash_carry') {
            //     var maxQty = (parseFloat(plan_qty) + parseFloat($fillData[key].pending_org_qty)).toFixed(3);

            //     var totalQty = parseFloat(plan_qty) + parseFloat(pending_so_qty);

            // } else {
            //     if (formId == undefined) {
            //         var maxQty = parseFloat($fillData[key].pending_dp_qty).toFixed(3);
            //         var totalQty = parseFloat(plan_qty) + parseFloat(pending_so_qty);
            //     } else {
            //         var maxQty = parseFloat($fillData[key].pending_dp_qty) + parseFloat($fillData[key].plan_qty);

            //         var totalQty = parseFloat(plan_qty) + parseFloat(pending_so_qty) + parseFloat($fillData[key].pending_dp_qty);
            //     }

            // }



            // if (formId == undefined) {
            //     var maxQty = plan_qty;

            // } else {
            //     var maxtotalQty = parseFloat(plan_qty) + parseFloat(pending_so_qty);

            //     //     console.log(maxtotalQty)
            //     var maxQty = parseFloat(maxtotalQty).toFixed(3)
            //     var pending_so_qty = maxQty;
            // }

            var tblHtml = ``;

            tblHtml += `<tr>
             <td>
                  <a  ${ $fillData[key].in_use == true ? '' : onclick="removeLoadingPart(this)"}><i class="action-icon iconfa-trash remove-spi_part"></i></a></td>
                        <td class="sr_no"></td>
                        <td>
                        <input type="hidden" name="fiting_item[]" value="${$fillData[key].fitting_item}">
                        <input type="hidden" name="secondary_unit[]" value="${$fillData[key].secondary_unit}">
                        <input type="hidden" name="multiple_loading_entry[]" value="${$fillData[key].multiple_loading_entry}">
                        <input type="hidden" name="so_from_value_fix[]" value="${so_from_value_fix}">
                        <input type="hidden" name="allow_partial_dispatch[]" value="${$fillData[key].allow_partial_dispatch}">
                        <input type="hidden" name="dp_id[]" value="${dp_id}">
                        <input type="hidden" name="le_details_id[]" value="${le_details_id}">
                        <input type="hidden" name="form_indx" value="${formIndx}"/>
                        <input type="hidden" name="dp_details_id[]" value="${dp_details_id}"/>
                        <input type="hidden" name="so_details_id[]" value="${so_details_id}"/>
                        ${so_no}<input type='hidden' name='so_no[]' value="${so_no}"/>
                        </td>`;
            tblHtml += `<td>${so_date}<input type='hidden' name='so_date[]' value="${so_date}"/></td>`;
            tblHtml += `<td>${name}<input type='hidden' name='name[]' value="${name}"/></td>`;
            tblHtml += `<td>${customer_village}<input type='hidden' name='customer_village[]' value="${customer_village}"/></td>`;
            tblHtml += `<td>${district_name}<input type='hidden' name='district_name[]' value="${district_name}"/></td>`;
            tblHtml += `<td>${dealer_name}<input type='hidden' name='dealer_name[]' value="${dealer_name}"/></td>`;

            if ($fillData[key].multiple_loading_entry == 'Yes' && $fillData[key].fitting_item == 'no') {
                if ($fillData[key].secondary_unit == 'Yes') {
                    if ($fillData[key].in_use == true) {
                        tblHtml += `<td>${item_name}<input type='hidden' name='item_id[]' value="${item_id}"/><span><a><i class="action-icon iconfa-eye-open"></i></a></span></td>`;
                    } else {

                        tblHtml += `<td>${item_name}<input type='hidden' name='item_id[]' value="${item_id}"/><span><a><i class="action-icon iconfa-eye-open eyeIcon1"></i></a></span></td>`;
                    }

                } else {
                    tblHtml += `<td>${item_name}<input type='hidden' name='item_id[]' value="${item_id}"/></td>`;
                }
            } else {
                if ($fillData[key].secondary_unit == 'Yes') {
                    if ($fillData[key].in_use == true) {

                        tblHtml += `<td>${item_name}<input type='hidden' name='item_id[]' value="${item_id}"/><span><a><i class="action-icon iconfa-eye-open"></i></a></span></td>`;
                    } else {

                        tblHtml += `<td>${item_name}<input type='hidden' name='item_id[]' value="${item_id}"/><span><a><i class="action-icon iconfa-eye-open eyeIcon1"></i></a></span></td>`;
                    }
                } else {
                    tblHtml += `<td>${item_name}<input type='hidden' name='item_id[]' value="${item_id}"/></td>`;
                }
            }
            tblHtml += `<td>${item_code}<input type='hidden' name='item_code[]' value="${item_code}"/></td>`;
            tblHtml += `<td>${item_group_name}<input type='hidden' name='group[]' value="${item_group_name}"/></td>`;
            if (formId == undefined) {

                if ($fillData[key].secondary_unit == 'Yes' || $fillData[key].fitting_item == 'yes') {
                    tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                    <input type="text" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}"  readonly/></td>`;

                } else {

                    // if ($fillData[key].multiple_loading_entry == 'Yes' || $fillData[key].so_from_value_fix == 'location' || $fillData[key].so_from_value_fix == 'cash_carry') {
                    tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                    <input type="text" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}"  ${ $fillData[key].in_use == true ? 'readonly' :'' }/></td>`;

                    // } else {
                    //     tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                    // <input type="text" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" readonly   /></td>`;

                    // }

                }


                // if ($fillData[key].multiple_loading_entry == 'Yes' && $fillData[key].fitting_item == 'no') {
                //     if ($fillData[key].secondary_unit == 'Yes') {
                //         tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                //         <input type="text" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}"  readonly/></td>`;
                //     } else {
                //         tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                //         <input type="text" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" /></td>`;
                //     }


                // } else {
                //     tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                //     <input type="text" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" readonly   /></td>`;
                // }

            } else {

                if ($fillData[key].secondary_unit == 'Yes' || $fillData[key].fitting_item == 'yes') {
                    tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                      <input type="text" min="${$fillData[key].used_qty ? parseFloat($fillData[key].used_qty).toFixed(3) : 0.000}" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" readonly /> </td>`;

                } else {

                    // if ($fillData[key].multiple_loading_entry == 'Yes' || $fillData[key].so_from_value_fix == 'location' || $fillData[key].so_from_value_fix == 'cash_carry') {
                    tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                        <input type="text" min="${$fillData[key].used_qty ? parseFloat($fillData[key].used_qty).toFixed(3) : 0.000}" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" ${$fillData[key].in_use == true ? 'readonly' :'' } /></td>`;
                    // } else {
                    //     tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                    //    <input type="text" min="${$fillData[key].used_qty ? parseFloat($fillData[key].used_qty).toFixed(3) : 0.000}" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" readonly   /></td>`;

                    // }

                }


                // if ($fillData[key].multiple_loading_entry == 'Yes' && $fillData[key].fitting_item == 'no') {
                //     if ($fillData[key].secondary_unit == 'Yes') {
                //         tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                //         <input type="text" min="${$fillData[key].used_qty ? parseFloat($fillData[key].used_qty).toFixed(3) : 0.000}" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" readonly /> </td>`;

                //     } else {
                //         tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                //         <input type="text" min="${$fillData[key].used_qty ? parseFloat($fillData[key].used_qty).toFixed(3) : 0.000}" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}"  /></td>`;

                //     }

                // } else {
                //     tblHtml += `<td><input type="hidden" name="org_plan_qty[]" value="${plan_qty}">
                //     <input type="text" min="${$fillData[key].used_qty ? parseFloat($fillData[key].used_qty).toFixed(3) : 0.000}" max="${parseFloat(maxQty).toFixed(3)} " name="plan_qty[]" id="plan_qty" class="form-control isNumberKey plan_qty" data-fiting_item="${$fillData[key].fitting_item}" onfocusout="sumPlanQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${plan_qty}" readonly   /></td>`;

                // }

            }

            tblHtml += `<td>${unit_name}<input type='hidden' name='unit[]' value="${unit_name}"/></td>`;
            // tblHtml += `<td>${pending_so_qty}<input type='hidden' name='pending_so_qty[]' value="${pending_so_qty}"/></td>`;
            tblHtml += `<td>${parseFloat(totalQty).toFixed(3)}<input type='hidden' name='pending_so_qty[]' value="${parseFloat(totalQty).toFixed(3)}"/></td>`;
            tblHtml += `</tr>`;

            jQuery('#LoadingEntryTable tbody').append(tblHtml);

            sumPlanQty();
            srNo();

        }

    }

}




jQuery('#pendingDispatchPlan').on('show.bs.modal', function (e) {
    var usedParts = [];
    var totalDisb = 0;

    jQuery('#LoadingEntryTable tbody input[name="form_indx"]').each(function (indx) {
        let frmIndx = jQuery(this).val();
        let dp_id = loading_data[frmIndx].dp_id;
        if (dp_id != "" && dp_id != null) {
            usedParts.push(Number(dp_id));
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

    jQuery('#pendingDispatchPlanTable tbody tr').each(function (indx) {

        totalEntry++;
        let checkField = jQuery(this).find('input[name="pd_dp_id[]"]');
        let partId = jQuery(checkField).val();
        let inUse = isUsed(partId);
        if (inUse) {
            jQuery(checkField).addClass('in-use').prop('checked', true);

        } else {
            jQuery(checkField).removeClass('in-use').prop('checked', false);
        }

    });


    setTimeout(() => {
        jQuery(this).find('#checkall-issue').focus();
    }, 300);
});



function sumPlanQty(th) {
    var total = 0;
    jQuery('.plan_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            // total = parseInt(total) + parseInt(total1);
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    total != 0 && total != "" ? jQuery('.planqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.planqtysum').text('');
}


function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
}



function editLoadingPart($this) {
    var planQty = jQuery($this).parent('td').parent('tr').find('input[name="plan_qty[]"]');
    if (jQuery(planQty).prop('readonly')) {
        jQuery(planQty).prop('readonly', false);
    } else {
        jQuery(planQty).trigger('change').prop('readonly', true);
    }
}


jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    var fiting_item = jQuery(element).data('fiting_item');
    if (jQuery(element).prop('readonly') && fiting_item == 'yes') {
        return true;
    }
    return this.optional(element) || parseFloat(value) >= 0.001;
});


var validator = jQuery("#commonLoadingEntryForm").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,
    rules: {
        "plan_qty[]": {
            required: function (e) {
                if (jQuery(e).prop('disabled')) {
                    return false;
                } else {
                    setTimeout(() => {
                        jQuery(e).focus();
                    }, 1000);
                    return true;
                }
            },

            notOnlyZero: '0.001',
        },

    },

    messages: {
        "plan_qty[]": {
            required: "Please Enter Plan Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.'
        },
    },

    submitHandler: function (form) {

        let checkLength = jQuery("#LoadingEntryTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;


        if (checkLength < 1) {
            jAlert("Please Add At Least One Loading Entry Detail.");
            return false;
        }


        jQuery('#loading_button').prop('disabled', true);

        var formUrl = formId !== undefined ? RouteBasePath + "/update-loading_entry" : RouteBasePath + "/store-loading_entry";


        let formData = jQuery('#commonLoadingEntryForm').serialize();
        let requestData = formData + '&' + jQuery.param({ loading_entry_details: dispatchDetailArray });

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
                            window.location.href = RouteBasePath + "/manage-loading_entry";
                        };
                    } else {
                        toastSuccess(data.response_message, redirectFn);
                        function redirectFn() {
                            window.location.reload();
                        }
                        jQuery('#loading_button').prop('disabled', false);

                    }
                } else {

                    jQuery('#loading_button').prop('disabled', false);

                    toastError(data.response_message);

                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);
                if (errMessage.errors) {
                    jQuery('#loading_button').prop('disabled', false);
                    validator.showErrors(errMessage.errors);
                } else if (jqXHR.status == 401) {
                    jQuery('#loading_button').prop('disabled', false);
                    toastError(jqXHR.statusText);
                } else {
                    jQuery('#loading_button').prop('disabled', false);

                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });



    }
});

// function removeLoadingPart(th) {
//     jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

//         // let checkLength = jQuery("#LoadingEntryTable tbody tr").filter(function () {
//         //     return jQuery(this).css('display') !== 'none';
//         // }).length;

//         // if (checkLength > 1) {
//         if (r === true) {
//             jQuery(th).parents("tr").remove();
//             srNo();
//             var plan_qty = jQuery(th).parents('tr').find('#plan_qty').val();

//             if (plan_qty) {
//                 var item_total = jQuery('.planqtysum').text();
//                 if (item_total != "") {
//                     item_final_total = parseInt(item_total) - parseInt(plan_qty);
//                 }
//                 jQuery('.planqtysum').text(parseFloat(item_final_total).toFixed(3));
//             }
//             //jQuery('.amountsum').text(amt_final_total);
//         }
//         // }
//         // else {
//         //     jAlert("Please At Least Item List Item Required");
//         // }

//     });
// }


function removeLoadingPart(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        if (r === true) {

            let dpPartId = jQuery(th).closest('tr').find('input[name="dp_details_id[]"]').val();

            if (dpPartId != '') {
                jQuery.ajax({
                    url: RouteBasePath + "/check-dp_part_in_use?dp_part_id=" + dpPartId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        jQuery(th).removeClass('file-loader');
                        if (data.response_code == 1) {
                            toastError(data.response_message);
                        } else {
                            jQuery(th).parents("tr").remove();
                            srNo();
                            var plan_qty = jQuery(th).parents('tr').find('#plan_qty').val();

                            if (plan_qty) {
                                var item_total = jQuery('.planqtysum').text();
                                if (item_total != "") {
                                    item_final_total = parseFloat(item_total) - parseFloat(plan_qty);
                                }
                                jQuery('.planqtysum').text(parseFloat(item_final_total).toFixed(3));
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
                jQuery(th).parents("tr").remove();
                srNo();
                var plan_qty = jQuery(th).parents('tr').find('#plan_qty').val();

                if (plan_qty) {
                    var item_total = jQuery('.planqtysum').text();
                    if (item_total != "") {
                        item_final_total = parseInt(item_total) - parseInt(plan_qty);
                    }
                    jQuery('.planqtysum').text(parseFloat(item_final_total).toFixed(3));
                }
            }
        }


    });
}


jQuery(document).on('click', '.eyeIcon1', function () {
    var td = jQuery(this).closest('td');
    var dp_details_id = td.closest('tr').find("input[name='dp_details_id[]']").val();
    var so_details_id = td.closest('tr').find("input[name='so_details_id[]']").val();
    var multiple_loading_entry = td.closest('tr').find("input[name='multiple_loading_entry[]']").val();
    var so_from_value_fix = td.closest('tr').find("input[name='so_from_value_fix[]']").val();
    var allow_partial_dispatch = td.closest('tr').find("input[name='allow_partial_dispatch[]']").val();

    var dp_id = jQuery('#Dpid').val();



    if (so_from_value_fix == 'customer') {
        var pendingType = 'dispatch';
    } else {
        var pendingType = 'so';
    }


    if (dp_details_id != '') {

        // var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/get-secondary_dispatch_item_data_for_loading?id=" + formId + "&dp_details_id=" + dp_details_id : RouteBasePath + "/get-secondary_dispatch_item_data_for_loading?dp_details_id=" + dp_details_id;

        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/get-secondary_dispatch_item_data_for_loading?id=" + formId + "&dp_details_id=" + dp_details_id + "&so_details_id=" + so_details_id + "&pendingType=" + pendingType + '&dp_id=' + dp_id : RouteBasePath + "/get-secondary_dispatch_item_data_for_loading?dp_details_id=" + dp_details_id + "&so_details_id=" + so_details_id + "&pendingType=" + pendingType + '&dp_id=' + dp_id;


        jQuery.ajax({
            url: formUrl,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    var tblHtml = '';

                    jQuery("#DispatchSecondaryForLoadingModal").find('#dp_details_id').val(dp_details_id);
                    jQuery("#DispatchSecondaryForLoadingModal").find('#pend_second_plan_qty').val(parseFloat(data.dpSecondaryItem[0].pend_plan_qty).toFixed(3));
                    jQuery("#DispatchSecondaryForLoadingModal").find('#pend_type').val(pendingType);

                    var checkItemLength = dispatchDetailArray[dp_details_id] != undefined ? 1 : 0;


                    for (let idx in data.dpSecondaryItem) {


                        if (checkItemLength) {
                            var indetailUse = dispatchDetailArray[dp_details_id].filter((value) => Number(value.dp_secondary_details_id) == data.dpSecondaryItem[idx].dp_secondary_details_id);


                        } else {
                            var indetailUse = [];
                        }


                        if (data.dpSecondaryItem[idx].le_secondary_details_id == 0) {
                            if (formId == undefined) {
                                var detailQtyUse = indetailUse.length ? indetailUse[0].plan_qty : parseFloat(data.dpSecondaryItem[idx].pend_plan_qty).toFixed(3);
                            } else {
                                var detailQtyUse = '';
                            }

                        } else {
                            var detailQtyUse = indetailUse.length ? indetailUse[0].plan_qty : '';

                        }

                        indetailUse = indetailUse.length ? true : false;

                        if (indetailUse == false) {
                            var detailQtyUse = '';
                        }

                        tblHtml += `<tr>`;

                        // if (multiple_loading_entry == 'Yes') {
                        //     tblHtml += `<td><input type="checkbox" name="dp_secondary_details_id[]" class="simple-check" id="dp_secondary_details_ids_${data.dpSecondaryItem[idx].dp_secondary_details_id}" value="${data.dpSecondaryItem[idx].dp_secondary_details_id}"   onchange="manageDetailsQtyfield(this)" ${indetailUse ? 'checked' : ''}/></td>`;
                        // } else {

                        //     if (so_from_value_fix == 'location' || so_from_value_fix == 'cash_carry' || allow_partial_dispatch == 'Yes') {
                        //         tblHtml += `<td><input type="checkbox" name="dp_secondary_details_id[]" class="simple-check" id="dp_secondary_details_ids_${data.dpSecondaryItem[idx].dp_secondary_details_id}" value="${data.dpSecondaryItem[idx].dp_secondary_details_id}"   onchange="manageDetailsQtyfield(this)" ${indetailUse ? 'checked' : ''}/></td>`;

                        //     } else {
                        //         tblHtml += `<td><input type="checkbox" name="dp_secondary_details_id[]" class="simple-check in-use" id="dp_secondary_details_ids_${data.dpSecondaryItem[idx].dp_secondary_details_id}" value="${data.dpSecondaryItem[idx].dp_secondary_details_id}"   onchange="manageDetailsQtyfield(this)" checked readonly/></td>`;

                        //     }

                        // }



                        tblHtml += `<td>
                        <input type="hidden" name="dp_secondary_details_id[]" value="${data.dpSecondaryItem[idx].dp_secondary_details_id}">
                        <input type="hidden" name="item_id[]" value="${data.dpSecondaryItem[idx].item_id}">
                        <input type="hidden" name="item_details_id[]" value="${data.dpSecondaryItem[idx].item_details_id}">
                        <input type="hidden" name="dp_details_id[]" value="${data.dpSecondaryItem[idx].dp_details_id}">                                    
                        <input type="hidden" name="le_secondary_details_id[]" value="${data.dpSecondaryItem[idx].le_secondary_details_id}"> 
                        <input type="hidden" name="secondary_qty[]" value="${data.dpSecondaryItem[idx].secondary_qty}">      
                        <input type="hidden" name="org_plan_qty[]" value="${data.dpSecondaryItem[idx].le_secondary_details_id == 0 ? data.dpSecondaryItem[idx].org_plan_qty : data.dpSecondaryItem[idx].org_plan_qty}">                                                 
                        ${data.dpSecondaryItem[idx].secondary_item_name}</td>
                        <td>${data.dpSecondaryItem[idx].item_code}</td>
                        <td>${data.dpSecondaryItem[idx].item_group_name}</td>                          
                        <td>${parseFloat(data.dpSecondaryItem[idx].stock_qty).toFixed(3)}</td>                                
                        <td>${data.dpSecondaryItem[idx].unit_name}</td>   
                        <td>${parseFloat(data.dpSecondaryItem[idx].secondary_stock_qty).toFixed(3)}</td>                                
                        <td>${data.dpSecondaryItem[idx].second_unit}</td>                        
                        <td>${parseFloat(data.dpSecondaryItem[idx].pend_plan_qty).toFixed(3)}</td> `;


                        if (multiple_loading_entry == 'Yes') {
                            tblHtml += `<td>      
                                               
                                <input type="text" class="input-mini  only-numbers plan_qtys"  name="plan_qty[]" max="${data.dpSecondaryItem[idx].pend_plan_qty}" value="${detailQtyUse}" >
                                </td>                          
                                </tr>`;
                            // tblHtml += `<td>      

                            //     <input type="text" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" name="plan_qty[]" max="${parseFloat(data.dpSecondaryItem[idx].pend_plan_qty).toFixed(3)}" value="${detailQtyUse}" ${detailQtyUse == '' ? 'disabled' : ''}>
                            //     </td>                          
                            //     </tr>`;
                        } else {
                            if (so_from_value_fix == 'location' || so_from_value_fix == 'cash_carry' || allow_partial_dispatch == 'Yes') {
                                tblHtml += `<td>                         
                                    <input type="text" class="input-mini  only-numbers plan_qtys" name="plan_qty[]" max="${parseFloat(data.dpSecondaryItem[idx].pend_plan_qty).toFixed(3)}" value="${detailQtyUse}">
                                    </td>                          
                                    </tr>`;
                                // tblHtml += `<td>                         
                                //     <input type="text" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" name="plan_qty[]" max="${parseFloat(data.dpSecondaryItem[idx].pend_plan_qty).toFixed(3)}" value="${detailQtyUse}" ${detailQtyUse == '' ? 'disabled' : ''}>
                                //     </td>                          
                                //     </tr>`;

                            } else {
                                tblHtml += `<td>                         
                                    <input type="text" class="input-mini  only-numbers plan_qtys" name="plan_qty[]" max="${data.dpSecondaryItem[idx].pend_plan_qty}" value="${detailQtyUse}">
                                    </td>                          
                                    </tr>`;
                                // tblHtml += `<td>                         
                                //     <input type="text" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" name="plan_qty[]" max="${parseFloat(data.dpSecondaryItem[idx].pend_plan_qty).toFixed(3)}" value="${detailQtyUse}" readonly>
                                //     </td>                          
                                //     </tr>`;

                            }

                        }

                        // tblHtml += `<td>                         
                        // <input type="text" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" name="plan_qty[]" max="${parseFloat(data.dpSecondaryItem[idx].pend_plan_qty).toFixed(3)}" value="${detailQtyUse}" ${multiple_loading_entry == "Yes" ? detailQtyUse == '' ? 'disabled' : '' : 'readonly'}>
                        // </td>                          
                        // </tr>`;

                    }


                    jQuery('#DispatchSecondaryForLoadingModalTable tbody').empty().append(tblHtml);
                    jQuery("#DispatchSecondaryForLoadingModal").modal('show');

                    // if (multiple_loading_entry == 'No') {
                    //     if (so_from_value_fix == 'location' || so_from_value_fix == 'cash_carry' || allow_partial_dispatch == 'Yes') {
                    //         jQuery("#DispatchSecondaryForLoadingModal").find('#addPendingDispatchSecondaryBtn').prop('disabled', false);
                    //         jQuery("#DispatchSecondaryForLoadingModal").find('#checkall-sod_second_data').prop('disabled', false);
                    //     } else {
                    //         jQuery("#DispatchSecondaryForLoadingModal").find('#addPendingDispatchSecondaryBtn').prop('disabled', true);
                    //         jQuery("#DispatchSecondaryForLoadingModal").find('#checkall-sod_second_data').prop('disabled', true);

                    //     }

                    // }



                }

            }
        });

    }


});



var coaPartValidator = jQuery("#addPendingDispatchSecondaryForm").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,
    rules: {
        // "dp_secondary_details_id[]": {
        //     required: true
        // },
        "plan_qty[]": {
            // required: function (e) {
            //     if (jQuery(e).prop('disabled')) {
            //         return false;
            //     } else {

            //         return true;
            //     }
            // },

            notOnlyZero: '0.001',
        },
    },

    messages: {
        // "dp_secondary_details_id[]": {
        //     required: "Please Select Item From Dispatch Secondary Item",
        // },
        "plan_qty[]": {
            // required: "Please Enter Plan Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.',
        },
    },

    submitHandler: function (form) {

        let checkLength = jQuery("#DispatchSecondaryForLoadingModalTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;


        if (checkLength < 1) {
            jAlert("Please Add At Least One Dispatch Secondary Item.");
            return false;
        }


        let storeArr = [];


        var itemId;
        var planQty;
        var dpdId;
        var orgplanQty;
        var totalPlanqty = 0;
        var dispatchDetailsId;


        var index = jQuery("#addPendingDispatchSecondaryForm").find('#dp_details_id').val();

        var errorQty = true;



        jQuery('#DispatchSecondaryForLoadingModalTable tbody tr').each(function (e) {
            planQty = jQuery(this).find('input[name="plan_qty[]"]').val();

            if (planQty && parseFloat(planQty) > 0) {
                errorQty = false;

                // if (jQuery(dispatchDetailsId).is(':checked')) {

                // dispatchDetailsId = jQuery(dispatchDetailsId).val();
                dispatchDetailsId = jQuery(this).find('input[name="dp_secondary_details_id[]"]').val();

                itemId = jQuery(this).find('input[name="item_id[]"]').val();
                itemDetailId = jQuery(this).find('input[name="item_details_id[]"]').val();
                planQty = jQuery(this).find('input[name="plan_qty[]"]').val();
                orgplanQty = jQuery(this).find('input[name="org_plan_qty[]"]').val();
                secondaryQty = jQuery(this).find('input[name="secondary_qty[]"]').val();
                dpdId = jQuery(this).find('input[name="dp_details_id[]"]').val();
                le_secondary_details_id = jQuery(this).find('input[name="le_secondary_details_id[]"]').val();

                // totalPlanqty += parseFloat(planQty);
                totalPlanqty += parseFloat(planQty) * parseFloat(secondaryQty);


                storeArr.push({ 'dp_secondary_details_id': dispatchDetailsId, 'plan_qty': planQty, 'org_plan_qty': orgplanQty, 'item_id': itemId, 'item_details_id': itemDetailId, 'dp_details_id': dpdId, 'le_secondary_details_id': le_secondary_details_id });
            }
        });


        dispatchDetailArray[index] = storeArr.filter(function (val) {
            return val.item_id !== undefined && val.plan_qty !== undefined;
        }).map(function (val) {
            return {
                dp_secondary_details_id: val.dp_secondary_details_id,
                plan_qty: val.plan_qty,
                org_plan_qty: val.org_plan_qty,
                item_id: val.item_id,
                item_details_id: val.item_details_id,
                dp_details_id: val.dp_details_id,
                le_secondary_details_id: val.le_secondary_details_id,
            };
        });

        if (errorQty) {
            jAlert("Please Add At Least One SO Seconday Item.");
            return false;
        }

        if (dispatchDetailArray.length < 1) {
            jAlert("Please Add At Least One SO Seconday Item.");
            return false;
        }

        var pend_second_plan_qty = jQuery("#addPendingDispatchSecondaryForm").find('#pend_second_plan_qty').val();
        var pend_type = jQuery("#addPendingDispatchSecondaryForm").find('#pend_type').val();

        // if(pend_type == 'so'){ 
        if (parseFloat(pend_second_plan_qty) < totalPlanqty) {
            toastError('Plan Qty. is Greater Than Pend. SO Qty.');
            return false;
        }

        // }




        jQuery("#LoadingEntryTable tbody tr").each(function () {
            let $tr = jQuery(this);

            let rowSodId = $tr.find('input[name="dp_details_id[]"]').val();

            if (rowSodId == index) {
                $tr.closest('tr').find("input[name='plan_qty[]']").val(parseFloat(totalPlanqty).toFixed(3));
            }
        });

        jQuery("#DispatchSecondaryForLoadingModal").modal('hide');

        sumPlanQty();
    }
});

jQuery('#checkall-sod_second_data').click(function () {
    const isChecked = jQuery(this).is(':checked');


    jQuery("#addPendingDispatchSecondaryForm").find("[id^='dp_secondary_details_ids_']").each(function (index) {
        const $checkbox = jQuery(this);


        if (!$checkbox.hasClass('in-use')) {
            const $planQty = jQuery('input[name="plan_qty[]"]').eq(index);

            $checkbox.prop('checked', isChecked).trigger('change');

            if (isChecked) {
                $planQty.prop('disabled', false).trigger('change');
            } else {
                $planQty.val('').prop('disabled', true).trigger('change');
            }
        }
    });
});



function manageDetailsQtyfield($this) {
    var planQtyField = jQuery($this).parent('td').parent('tr').find('input[name="plan_qty[]"]');

    if (jQuery(planQtyField).prop('disabled') && jQuery(planQtyField).prop('disabled')) {
        jQuery(planQtyField).prop('disabled', false);
    } else {
        jQuery(planQtyField).val('').trigger('change').prop('disabled', true);

    }

}
