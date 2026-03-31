// setTimeout(() => {
//     jQuery('#dp_sequence').focus();
// }, 100);
var so_data = [];
var chkSOId = [];
var chkItem = [];
var editchkItem = [];
var chkArr = [];
var chkId = [];
var sochkItem = [];
var sodDetailArray = [];
var sodSecondaryDetailArray = [];


// jQuery('div#btn_hide').hide();   

const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

var formId = jQuery('#commonDispatchPlanForm').find('input:hidden[name="id"]').val();
var isAnyPartUse = false;

jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };
    // Store or Update

    if (formId != "" && formId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({
            url: RouteBasePath + "/get-dispatch_plan/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    jQuery('input:radio[name="dispatch_from_id_fix"][value="' + data.do_data.dispatch_from_id_fix + '"]').attr('checked', true).trigger('click');

                    jQuery("input:radio[name='dispatch_from_id_fix']").prop({ tabindex: -1 }).attr('readonly', true);

                    jQuery('#old_dispatch_from_id_fix').val(data.do_data.dispatch_from_id_fix);

                    jQuery('#dp_sequence').val(data.do_data.dp_sequence).attr('readonly', true);
                    jQuery('#dp_number').val(data.do_data.dp_number).attr('readonly', true);
                    jQuery('#dp_date').val(data.do_data.dp_date);
                    jQuery('#special_notes').val(data.do_data.special_notes);

                    // if (data.do_data.dispatch_from_id_fix == 2) {
                    //     jQuery('div#btn_hide').show();
                    // } else {
                    //     jQuery('div#btn_hide').hide();
                    // }
                    if (data.do_data.multiple_loading_entry == 'Yes') {
                        jQuery('#multiple_loading_entry').trigger('click');
                    }

                    if (data.do_data.in_use == true) {
                        jQuery('#multiple_loading_entry').attr('readonly', true);
                    } else {
                        jQuery('#multiple_loading_entry').attr('readonly', false);

                    }


                    // if (data.dp_details.length > 0 && !jQuery.isEmptyObject(data.dp_details)) {
                    //     for (let ind in data.dp_details) {
                    //         so_data.push(data.dp_details[ind]);
                    //     }
                    //     fillDispatchTable();
                    // }

                    if (data.dp_details.length > 0 && !jQuery.isEmptyObject(data.dp_details)) {
                        for (let ind in data.dp_details) {
                            so_data.push(data.dp_details[ind]);
                            editchkItem.push(data.dp_details[ind]);

                            if (!jQuery.isEmptyObject(data.dp_details[ind].sodDetailArray)) {
                                for (let key in data.dp_details[ind].sodDetailArray) {
                                    sodDetailArray[data.dp_details[ind].so_details_id] = data.dp_details[ind].sodDetailArray[key];
                                }
                            }

                            if (!jQuery.isEmptyObject(data.dp_details[ind].sodSecondaryDetailArray)) {
                                // for (let key in data.dp_details[ind].sodSecondaryDetailArray) {
                                sodSecondaryDetailArray[data.dp_details[ind].so_details_id] = data.dp_details[ind].sodSecondaryDetailArray;
                                // }
                            }
                        }
                        // editchkItem.push(data.dp_details)
                        fillDispatchTable();
                    }

                    // setTimeout(() => {
                    //     jQuery('#dp_date').focus();
                    // }, 100);

                    // getSOData()
                    // setTimeout(() => {
                    //     getSODetailData()
                    // }, 1800)

                    if (data.do_data.dispatch_from_id_fix != null && data.do_data.dispatch_from_id_fix != undefined) {
                        getSOData(data.do_data.dispatch_from_id_fix).done(function () {
                            getSODetailData()
                        })
                    } else {
                        jQuery('div#radio_so').hide();
                        getSOData().done(function () {
                            getSODetailData()
                        })

                    }


                    // if (formId != undefined) {
                    //     editchkItem.push(data.so_data)

                    if (data.edit_item) {
                        for (let idx in data.edit_item) {

                            for (let ind in data.edit_item[idx]) {
                                if (!checkItem.hasOwnProperty(idx)) {
                                    // console.log('for if 1')
                                    checkItem[idx] = [];

                                }

                                checkItem[idx].push(data.edit_item[idx][ind])
                            }


                            // // for (let ind in editchkItem[idx]) {
                            // // console.log('for')
                            // 
                            // if (editchkItem[idx].dp_details_id == 0) {
                            //     // console.log('for if 2')
                            //     checkItem[editchkItem[idx].id].push(editchkItem[idx].so_details_id)
                            // }
                            // }

                        }


                    }
                    // }

                    // below code is use to push array in not in use dispatch so item edit 
                    // setTimeout(() => {
                    //     if (editchkItem.length > 0 && !jQuery.isEmptyObject(editchkItem)) {
                    //         for (let idx in editchkItem) {
                    //             for (let ind in editchkItem[idx]) {
                    //                 console.log('for')
                    //                 if (!checkItem.hasOwnProperty(editchkItem[idx][ind].id)) {
                    //                     console.log('for if 1')
                    //                     checkItem[editchkItem[idx][ind].id] = [];
                    //                 }
                    //                 if (editchkItem[idx][ind].dp_details_id == 0) {
                    //                     console.log('for if 2')
                    //                     checkItem[editchkItem[idx][ind].id].push(editchkItem[idx][ind].so_details_id)
                    //                 }
                    //             }

                    //         }


                    //     }
                    //     console.log('edit', checkItem)
                    // }, 1600)


                    for (let idx in so_data) {
                        if (!chkArr[so_data[idx].id]) {
                            chkArr[so_data[idx].id] = [];
                        }

                        if (!chkArr[so_data[idx].id].includes(so_data[idx].so_details_id)) {
                            chkArr[so_data[idx].id].push(so_data[idx].so_details_id);
                        } else {
                            chkArr[so_data[idx].id] = [so_data[idx].so_details_id];
                        }
                    }

                    // console.log(chkArr, 'dsff')

                    if (data.do_data.in_use == true) {
                        jQuery('#dp_sequence').attr('readonly', true);
                        jQuery('#dp_date').attr('readonly', true);
                        jQuery('#special_notes').attr('readonly', true);
                        jQuery('#checkall-po').attr('readonly', true);
                        isAnyPartUse = true;
                    }

                    jQuery('#show-progress').removeClass('loader-progress-whole-page');
                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = RouteBasePath + "/manage-dispatch_plan";
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
    } else {

        // getSOData();
        dispatchType();
        getLatestDispatchNo();
        getSODetailData();
        checkCustomerData();

        // setTimeout(() => {
        //     jQuery('#dp_date').focus();
        // }, 100);
        // setTimeout(() => {
        jQuery(".toggleModalBtn").last().focus();
        // }, 800);

    }
});


jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    if (jQuery(element).prop('readonly')) {
        return true;
    }
    return this.optional(element) || parseFloat(value) >= 0.001;
});

jQuery.validator.addMethod("maxAttrIfEditable", function (value, element) {
    if (jQuery(element).prop("readonly") || jQuery(element).prop("disabled")) return true;

    var max = jQuery(element).attr("max");
    var val = parseFloat(value);

    if (!max || isNaN(val)) return true;

    return val <= parseFloat(max);
}, function (params, element) {
    var max = jQuery(element).attr("max");
    return "Please enter a value less than or equal to " + max + ".";
});



var validator = jQuery("#commonDispatchPlanForm").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,

    rules: {

        dp_date: {
            required: true,
            date_check: true,
            dateFormat: true,
        },
        "so_detail_id[]": {
            required: true
        },
        "plan_qty[]": {
            required: function (e) {
                if (jQuery(e).prop('disabled')) {
                    return false;
                } else {
                    // setTimeout(() => {
                    //     jQuery(e).focus();
                    // }, 1000);
                    return true;
                }
            },

            notOnlyZero: '0.001',

        },

    },

    messages: {
        dp_date: {
            required: 'Please Enter Dispatch Plan Date',
        },
        "so_detail_id[]": {
            required: "Please Select At Least One SO No."
        },
        "plan_qty[]": {
            required: "Please Enter Plan Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.',
        },
    },
    // showErrors: function (errorMap, errorList) {
    //     console.log(errorList[0].element.value, errorList[0].element.max)
    //     if (errorList.length) {
    //         var element = errorList[0].element;
    //         var message = errorList[0].message;
    //     }
    // },

    submitHandler: function (form) {
        so_data = [];
        var index = 0;
        // main table loop 
        // jQuery('#DipatchPlanTable tbody tr').each(function (e) {
        //     var SOId = jQuery(this).find('input[name="so_detail_id[]"]');
        //     if (jQuery(SOId).is(':checked')) {

        //         SOId = jQuery(SOId).val();
        //         id = jQuery(this).find('input[name="id[]"]').val();
        //         planQty = jQuery(this).find('input[name="plan_qty[]"]').val();
        //         item_id = jQuery(this).find('input[name="sod_item_id[]"]').val();
        //         fitting_item = jQuery(this).find('input[name="fitting_item[]"]').val();
        //         org_plan_qty = jQuery(this).find('input[name="org_plan_qty[]"]').val();
        //         dp_details_id = jQuery(this).find('input[name="dp_details_id[]"]').val();
        //         // sodd_details_id = jQuery(this).find('input[name="sodd_details_id"]').val();
        //         // sodd_details_qty = jQuery(this).find('input[name="sodd_details_qty"]').val();

        //         // assign to object 
        //         so_data[index] = { 'so_detail_id': SOId, 'plan_qty': planQty, 'item_id': item_id, 'fitting_item': fitting_item, 'org_plan_qty': org_plan_qty, 'dp_details_id': dp_details_id, };
        //         index++;
        //     }
        // });

        var error = false;
        var errorsecondary = false;
        var errorallowpartial = false;
        var errorPlanQty = false;

        var ismultiChecked = jQuery("#commonDispatchPlanForm").find("#multiple_loading_entry").prop("checked");

        jQuery('#DipatchPlanTable tbody tr').each(function (e) {
            var SOId = jQuery(this).find('input[name="so_detail_id[]"]');

            SOId = jQuery(SOId).val();
            id = jQuery(this).find('input[name="id[]"]').val();
            planQty = jQuery(this).find('input[name="plan_qty[]"]').val();
            maxAttrStr = jQuery(this).find('input[name="plan_qty[]"]').attr('max');
            item_id = jQuery(this).find('input[name="sod_item_id[]"]').val();
            fitting_item = jQuery(this).find('input[name="fitting_item[]"]').val();
            secondary_unit = jQuery(this).find('input[name="secondary_unit[]"]').val();
            org_plan_qty = jQuery(this).find('input[name="org_plan_qty[]"]').val();
            dp_details_id = jQuery(this).find('input[name="dp_details_id[]"]').val();
            allow_partial_dispatch = jQuery(this).find('input[name="allow_partial_dispatch[]"]').val();
            max_qty = jQuery(this).find('input[name="max_qty[]"]').val();
            require_qty = jQuery(this).find('input[name="require_qty[]"]').val();
            check_plan_qty = jQuery(this).find('input[name="check_plan_qty[]"]').val();
            so_from_value_fix = jQuery(this).find('input[name="so_from_value_fix[]"]').val();
            wt_pc = jQuery(this).find('input[name="wt_pc[]"]').val();
            // production_assembly = jQuery(this).find('input[name="production_assembly[]"]').val();


            // assign to object 
            so_data[index] = {
                'so_detail_id': SOId, 'plan_qty': planQty, 'item_id': item_id, 'fitting_item': fitting_item, 'secondary_unit': secondary_unit, 'org_plan_qty': org_plan_qty, 'dp_details_id': dp_details_id, 'allow_partial_dispatch': allow_partial_dispatch,
                'so_from_value_fix': so_from_value_fix, 'wt_pc': wt_pc,
            };
            //'production_assembly': production_assembly 


            index++;

            if (fitting_item == 'yes') {

                if (sodDetailArray[SOId] == undefined) {
                    error = true;
                } else {
                    if (sodDetailArray[SOId].length == 0) {
                        error = true;
                    }
                }
            }

            if (secondary_unit == 'Yes') {

                if (sodSecondaryDetailArray[SOId] == undefined) {
                    errorsecondary = true;
                } else {
                    if (sodSecondaryDetailArray[SOId].length == 0) {
                        errorsecondary = true;
                    }
                }
            }

            if (allow_partial_dispatch == 'No' && so_from_value_fix == 'customer') {

                if (ismultiChecked == false) {
                    if (parseFloat(require_qty) > parseFloat(check_plan_qty)) {
                        errorPlanQty = true;
                    }
                }



                if (maxAttrStr != undefined) {
                    if (parseFloat(maxAttrStr) < parseFloat(planQty)) {
                        errorallowpartial = true;
                    }
                } else {
                    if (parseFloat(max_qty) < parseFloat(planQty)) {
                        errorallowpartial = true;
                    }
                }

            }
        });

        if (errorPlanQty) {
            toastError('Plan Qty. Lessthen Pend. SO Qty.');
            return false;
        }

        if (error) {
            toastError('Please Add At Least One Fitting Item Detail.');
            return false;
        }
        if (errorsecondary) {
            toastError('Please Add At Least One Secondary Item Detail.');
            return false;
        }

        if (errorallowpartial) {
            toastError('Insufficient Stock');
            return false;
        }


        if (!jQuery.isEmptyObject(so_data)) {

            let data = new FormData(document.getElementById('commonDispatchPlanForm'));
            let formValue = Object.fromEntries(data.entries());

            if (formId !== undefined) { //Edit Form
                formValue.id = formId;
            }

            // remove the object key and value  after use 
            // delete formValue["id[]"];
            // delete formValue["so_detail_id[]"];
            // delete formValue["plan_qty[]"];
            const cleanedSodDetailArray = Object.entries(sodDetailArray).reduce((acc, [key, value]) => {
                if (Array.isArray(value) && value.length > 0 && value[0] !== null) {
                    acc[key] = value;
                }
                return acc;
            }, {});

            const cleanedSodSecondaryDetailArray = Object.entries(sodSecondaryDetailArray).reduce((acc, [key, value]) => {
                if (Array.isArray(value) && value.length > 0 && value[0] !== null) {
                    acc[key] = value;
                }
                return acc;
            }, {});

            formValue = Object.assign(formValue, { 'dispatch_plan_details': JSON.stringify(so_data), 'dispatch_plan_details_details': JSON.stringify(cleanedSodDetailArray), 'dispatch_plan_secondary_details': JSON.stringify(cleanedSodSecondaryDetailArray) });
            var formdata = new URLSearchParams(formValue).toString();

            jQuery('#dispatch_plan_button').prop('disabled', true);


            if (!jQuery.isEmptyObject(so_data)) {

                //let formUrl = RouteBasePath + "/store-dispatch_plan";
                var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-dispatch_plan" : RouteBasePath + "/store-dispatch_plan";

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
                                jAlert(data.response_message, 'Alert Dialog', function (r) {
                                    window.location.href = RouteBasePath + "/manage-dispatch_plan";
                                });

                                jQuery('#dispatch_plan_button').prop('disabled', false);
                            }
                            else if (formId == undefined || formId == "") {
                                toastSuccess(data.response_message, redirectFn);
                                function redirectFn() {
                                    window.location.reload();
                                }
                            }
                            else {
                                toastError(data.response_message);
                            }
                        } else {
                            jQuery('#dispatch_plan_button').prop('disabled', false);
                            jAlert(data.response_message);

                            jQuery('#DipatchPlanTable tbody tr').each(function (e) {
                                item_id = jQuery(this).find('input[name="sod_item_id[]"]').val();

                                if (data.item == item_id) {
                                    jQuery(this).css({
                                        'background': ' #f39898'
                                    });
                                }

                            });

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        var errMessage = JSON.parse(jqXHR.responseText);
                        if (errMessage.errors) {
                            jQuery('#dispatch_plan_button').prop('disabled', false);
                            validator.showErrors(errMessage.errors);
                        } else if (jqXHR.status == 401) {
                            jQuery('#dispatch_plan_button').prop('disabled', false);
                            jAlert(jqXHR.statusText);
                        } else {
                            jAlert('Something went wrong!');
                            jQuery('#dispatch_plan_button').prop('disabled', false);
                            console.log(JSON.parse(jqXHR.responseText));
                        }
                    }
                });
            }
        } else {
            jAlert("Please Add At Least One Dispatch Plan Detail.");
        }
    }
});
// end store or update

jQuery.fn.dataTable.ext.order['dom-checkbox'] = function (settings, col) {
    return this.api().column(col, { order: 'index' }).nodes().map(function (td, i) {
        return jQuery('input', td).prop('checked') ? '0' : '1';
    });
};

function getSOData(dispatch_from_id_fix) {

    var thisForm = jQuery('#commonDispatchPlanForm');

    if (dispatch_from_id_fix != undefined && dispatch_from_id_fix != '') {
        if (formId == undefined) {
            var Url = RouteBasePath + "/get-soData?so_from_id_fix=" + dispatch_from_id_fix;
        } else {
            var Url = RouteBasePath + "/get-soData?id=" + formId + "&so_from_id_fix=" + dispatch_from_id_fix;
        }
    } else {
        if (formId == undefined) {
            var Url = RouteBasePath + "/get-soData";
        } else {
            var Url = RouteBasePath + "/get-soData?id=" + formId;
        }

    }

    jQuery('.toggleModalBtn').prop('disabled', true);
    return jQuery.ajax({
        url: Url,
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            // if (data.response_code == 1) {
            //     if (data.so_data.length > 0 && !jQuery.isEmptyObject(data.so_data)) {
            //         for (let ind in data.so_data) {
            //             so_data.push(data.so_data[ind]);
            //         }
            //         fillDispatchTable();
            //     }
            // }

            if (data.response_code == 1 && data.so_data.length > 0) {
                // new code
                var usedParts = [];
                var totalDisb = 0;
                var found = 0;

                thisForm.find('#DipatchPlanTable tbody input[name="form_indx"]').each(function (indx) {
                    let frmIndx = jQuery(this).val();

                    let jbEorkOrderId = so_data[frmIndx].id;
                    if (jbEorkOrderId != "" && jbEorkOrderId != null) {
                        usedParts.push(Number(jbEorkOrderId));
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

                if (data.so_data.length > 0 && !jQuery.isEmptyObject(data.so_data)) {
                    found = 1;

                    for (let idx in data.so_data) {
                        var inUse = isUsed(data.so_data[idx].id);
                        totalEntry++;
                        tblHtml += `<tr>
                                    <td><input type="checkbox" name="so_id[]" class="simple-check ${inUse ? 'in-use' : ''} ${data.so_data[idx].in_use == true ? 'in_use_check' : ''}" id="so_ids_${data.so_data[idx].id}" 
                                    value="${data.so_data[idx].id}" ${inUse ? 'checked' : ''} onchange="getSODetailData()" ${data.so_data[idx].in_use == true ? 'readonly' : ''}/></td>
                                    <td style="white-space: unset;">${data.so_data[idx].so_number}</td>
                                    <td style="white-space: unset;">${data.so_data[idx].so_date}</td>
                                    <td style="white-space: unset;">${data.so_data[idx].name}</td>                  
                                    <td style="white-space: unset;">${data.so_data[idx].customer_group_name != null ? data.so_data[idx].customer_group_name : ''}</td>
                                    <td style="white-space: unset;">${data.so_data[idx].customer_village != null ? data.so_data[idx].customer_village : ''}</td>
                                    <td style="white-space: unset;">${data.so_data[idx].district_name != null ? data.so_data[idx].district_name : ''}</td>
                                    <td style="white-space: unset;">${data.so_data[idx].dealer_name != null ? data.so_data[idx].dealer_name : ''}</td>              
                                    <td style="white-space: unset;">${data.so_data[idx].special_notes != null ? data.so_data[idx].special_notes : ''}</td>              
                                    </tr>`;

                    }

                } else {

                    tblHtml += `<tr class="centeralign" id="noPendingPo">
                        <td colspan="5">No Pending PO Available</td>
                    </tr>`;

                }

                var $table = jQuery("#pendingSOModal").find('#pendingSODataTable');

                if (jQuery.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().destroy();
                }
                jQuery('#pendingSODataTable tbody').empty().append(tblHtml);


                $table = jQuery('#pendingSODataTable').DataTable({
                    pageLength: 50,
                    paging: false,
                    info: false,
                    searching: true,
                    "scrollX": true,
                    "scrollX": true,
                    "sScrollX": "100%",
                    "sScrollXInner": "110%",
                    "oLanguage": {
                        "sSearch": "Search :"
                    },
                    initComplete: function () {
                        // Exclude first column (index 0) from search
                        initColumnSearchDisp('#pendingSODataTable', [0,8]);
                    },
                    columnDefs: [{
                        targets: 0,
                        orderDataType: 'dom-checkbox'
                    }]
                    // "scrollX": true,
                    // "sScrollX": "100%",
                    //    "sScrollXInner": "110%",
                    //   "bScrollCollapse": true,
                });
                jQuery('#pendingSODataTable_filter').remove();
                jQuery('#show-progress').removeClass('loader-progress-whole-page');
                if (formId == undefined) {
                    setTimeout(() => {
                        jQuery('.toggleModalBtn').prop('disabled', false).last().focus();
                    }, 2200)
                } else {
                    if (isAnyPartUse == true) {
                        jQuery('.toggleModalBtn').prop('disabled', true);
                    }
                    // else {
                    //     setTimeout(() => {
                    //         jQuery('.toggleModalBtn').prop('disabled', false);
                    //     }, 2500)
                    // }
                }

            } else {
                jQuery('.toggleModalBtn').prop('disabled', true);
                jQuery('#show-progress').removeClass('loader-progress-whole-page');
                // toastError(data.response_message);

            }
        },




    });
}



// single check box script
function manageQtyfield($this) {
    var oaQtyField = jQuery($this).parent('td').parent('tr').find('input[name="plan_qty[]"]');
    if (jQuery(oaQtyField).prop('disabled')) {
        jQuery(oaQtyField).prop('disabled', false);
    } else {
        jQuery(oaQtyField).val('').trigger('change').prop('disabled', true);
    }
}

function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
}

function sumSoQty(th) {
    var total = 0;
    jQuery('.plan_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            // total = parseInt(total) + parseInt(total1);
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    total != 0 && total != "" ? jQuery('.displansum').text(parseFloat(total).toFixed(3)) : jQuery('.displansum').text('');

}


function removeDisDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        let checkLength = jQuery("#DipatchPlanTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        if (checkLength > 1) {
            if (r === true) {
                jQuery(th).parents("tr").remove();
                srNo();
                var plan_qty = jQuery(th).parents('tr').find('#plan_qty').val();
                //var po_amt = jQuery(th).parents('tr').find('#amount').val();

                if (plan_qty) {
                    var item_total = jQuery('.displansum').text();
                    // var amt_total = jQuery('.amountsum').text();
                    if (item_total != "") {
                        item_final_total = parseInt(item_total) - parseInt(plan_qty);
                        //amt_final_total = parseInt(amt_total) - parseInt(po_amt);
                    }
                    jQuery('.displansum').text(parseFloat(item_final_total).toFixed(3));
                }
                //jQuery('.amountsum').text(amt_final_total);
            }
        }
        else {
            jAlert("Please At Least Item List Item Required");
        }

    });
}

// get the latest number
function getLatestDispatchNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_dispatch_plan_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#dp_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#dp_date').val(currentDate);
                jQuery('#dp_number').val(data.latest_dp_no).prop({ tabindex: -1, readonly: true });
                jQuery('#dp_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#dp_number').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}



// check duplication 
jQuery('#dp_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();

    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }

    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid Dispatch Plan No.');
            jQuery('#dp_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery('#dp_sequence').focus();
            jQuery('#dp_sequence').val('');

        } else {


            jQuery("#dispatch_plan_button").attr('disabled', true);

            jQuery('#dp_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-dispatch_no_duplication?for=add&dp_sequence=" + val;

            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-dispatch_no_duplication?for=edit&dp_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#dp_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        jQuery('#dp_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery('#dp_sequence').focus();
                        jQuery('#dp_sequence').val('');
                    } else {
                        jQuery('#dp_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#dp_number').val(data.latest_po_no);
                        jQuery('#dp_sequence').val(val);
                    }
                    jQuery("#dispatch_plan_button").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#dp_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#dp_number').val('');
        jQuery('#dp_sequence').val('');
    }
});


jQuery(document).on('change', '.plan_qtys', function () {
    var maxAttributeValue = jQuery(this).attr('max');
    // console.log('Max attribute value:', maxAttributeValue);
    if (maxAttributeValue == 0) {

        jQuery.extend(jQuery.validator.messages, {
            min: 'Minimum value must be {0}',
            max: 'Dispatch Plan Qty. cannot be inserted.'
        });
        return;
    }
});



var checkItem = [];

function getSODetailData() {
    jQuery('#pendingBtnSOModal').prop('disabled', true);
    var check_dispatch_from_id_fix = jQuery('input[name="dispatch_from_id_fix"]:checked').val();


    // jQuery("#addPendingSODataForm").find("[id^='so_ids_']").each(function () {
    //     var thisId = jQuery(this).attr('id');
    //     var splt = thisId.split('so_ids_');
    //     var intId = splt[1];

    //     if (jQuery(this).is(':checked')) {
    //         chkSOId.push(jQuery(this).val())
    //     }

    // });

    jQuery("#addPendingSODataForm").find("[id^='so_ids_']").each(function () {
        var thisValue = jQuery(this).val();

        if (jQuery(this).is(':checked')) {

            if (!chkSOId.includes(thisValue)) {
                chkSOId.push(thisValue);
            }
        } else {
            chkSOId = chkSOId.filter(function (value) {
                return value !== thisValue;
            });
            delete checkItem[thisValue];
            delete chkArr[thisValue];
        }
    });


    if (chkSOId.length > 0) {
        var thisModal = jQuery('#pendingSOModal');
        var thisForm = jQuery('#commonDispatchPlanForm');

        jQuery('#pendingSOTable tbody').find('#noPendingDc').find('td').addClass('file-loader')

        if (formId == undefined) {
            var Url = RouteBasePath + "/get-so_item_list-dispatch?chkSOId=" + chkSOId.join(',');
        } else {
            var Url = RouteBasePath + "/get-so_item_list-dispatch?&id=" + formId + "&chkSOId=" + chkSOId.join(',');
        }

        jQuery.ajax({
            url: Url,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1 && data.so_data.length > 0) {
                    jQuery('#pendingBtnSOModal').prop('disabled', true);
                    // if (formId != undefined) {
                    //     editchkItem.push(data.so_data)


                    //     if (editchkItem.length > 0 && !jQuery.isEmptyObject(editchkItem)) {
                    //         for (let idx in editchkItem) {
                    //             for (let ind in editchkItem[idx]) {
                    //                 // console.log('for')
                    //                 if (!checkItem.hasOwnProperty(editchkItem[idx][ind].id)) {
                    //                     // console.log('for if 1')
                    //                     checkItem[editchkItem[idx][ind].id] = [];
                    //                 }
                    //                 if (editchkItem[idx][ind].dp_details_id == 0) {
                    //                     // console.log('for if 2')
                    //                     checkItem[editchkItem[idx][ind].id].push(editchkItem[idx][ind].so_details_id)
                    //                 }
                    //             }

                    //         }


                    //     }
                    // }

                    // console.log(editchkItem, 'api')

                    // new code
                    var usedParts = [];
                    var totalDisb = 0;
                    var found = 0;

                    thisForm.find('#DipatchPlanTable tbody input[name="form_indx"]').each(function (indx) {
                        let frmIndx = jQuery(this).val();

                        let jbEorkOrderId = so_data[frmIndx].so_details_id;
                        if (jbEorkOrderId != "" && jbEorkOrderId != null) {
                            usedParts.push(Number(jbEorkOrderId));
                        }
                    });

                    function isUsed(pjId) {
                        if (usedParts.includes(Number(pjId))) {
                            totalDisb++;
                            return true;
                        }
                        return false;
                    }

                    function iseditUsed(pjId, idx) {
                        if (checkItem.hasOwnProperty(idx)) {
                            if (checkItem[idx].includes(Number(pjId))) {
                                return false;
                            }
                        }
                        return true;
                    }
                    function isItemUsedcheck(pjId, idx) {
                        if (checkItem.hasOwnProperty(idx)) {
                            if (checkItem[idx].includes(Number(pjId))) {
                                return true;

                            }
                        }

                        return false;
                    }


                    let totalEntry = 0;
                    var tblitemHtml = ``;
                    var found = 0;

                    // end new code
                    // <td>${data.po_data[idx].pend_po_qty != null ? data.po_data[idx].po_qty >= data.po_data[idx].pend_po_qty ? parseFloat(data.po_data[idx].pend_po_qty).toFixed(3) : parseFloat(0).toFixed(3) : parseFloat(0).toFixed(3)}</td>          
                    if (data.so_data.length > 0 && !jQuery.isEmptyObject(data.so_data)) {

                        found = 1;

                        var lastKey = data.so_data.length - 1;

                        for (let idx in data.so_data) {

                            var inUse = formId == undefined ? isUsed(data.so_data[idx].so_details_id) : iseditUsed(data.so_data[idx].so_details_id, data.so_data[idx].id);
                            var isItemUsedchecked = isItemUsedcheck(data.so_data[idx].so_details_id, data.so_data[idx].id);




                            //  <td><input type="checkbox" name="so_details_id[]" class="simple-check ${inUse ? 'in-use' : ''} ${data.so_data[idx].in_use == true ? 'in_use_check' : ''}" id="so_details_ids_${data.so_data[idx].so_details_id}" value="${data.so_data[idx].so_details_id}" ${isItemUsedchecked ? '' : 'checked'} ${data.so_data[idx].in_use == true ? 'readonly' : ''} onchange="getItemCheck(this)" data-so_id="${data.so_data[idx].id}"/></td>               
                            // <td><input type="checkbox" name="so_details_id[]" class="simple-check ${inUse ? 'in-use' : ''} ${data.so_data[idx].in_use == true ? 'in_use_check' : ''}" id="so_details_ids_${data.so_data[idx].so_details_id}" value="${data.so_data[idx].so_details_id}" ${inUse ? 'checked' : isItemUsedchecked ? '' : 'checked'} ${data.so_data[idx].in_use == true ? 'readonly' : ''} onchange="getItemCheck(this)" data-so_id="${data.so_data[idx].id}"/></td>      
                            totalEntry++;

                            if (data.so_data[idx].so_from_value_fix == 'customer') {
                                // if (check_dispatch_from_id_fix == 1) {
                                tblitemHtml += `<tr>
                                <td style="display:none;"><input type="checkbox" name="so_details_id[]" class="simple-check ${inUse ? 'in-use' : ''} ${data.so_data[idx].in_use == true ? 'in_use_check' : ''}" id="so_details_ids_${data.so_data[idx].so_details_id}" value="${data.so_data[idx].so_details_id}" ${inUse ? 'checked' : isItemUsedchecked ? '' : 'checked'} readonly  onchange="getItemCheck(this)" data-so_id="${data.so_data[idx].id}"/></td> `;

                            } else {
                                tblitemHtml += `<tr>
                                <td style="display:none;"><input type="checkbox" name="so_details_id[]" class="simple-check ${inUse ? 'in-use' : ''} ${data.so_data[idx].in_use == true ? 'in_use_check' : ''}" id="so_details_ids_${data.so_data[idx].so_details_id}" value="${data.so_data[idx].so_details_id}" ${inUse ? 'checked' : isItemUsedchecked ? '' : 'checked'} ${data.so_data[idx].in_use == true ? 'readonly' : ''} onchange="getItemCheck(this)" data-so_id="${data.so_data[idx].id}"/></td> `;

                                // if (data.so_data[idx].allow_partial_dispatch == 'No') {
                                //     tblitemHtml += `<tr>
                                //     <td><input type="checkbox" name="so_details_id[]" class="simple-check ${inUse ? 'in-use' : ''} ${data.so_data[idx].in_use == true ? 'in_use_check' : ''}" id="so_details_ids_${data.so_data[idx].so_details_id}" value="${data.so_data[idx].so_details_id}" ${inUse ? 'checked' : isItemUsedchecked ? '' : 'checked'} readonly  onchange="getItemCheck(this)" data-so_id="${data.so_data[idx].id}"/></td> `;

                                // } else {
                                //     tblitemHtml += `<tr>
                                //     <td><input type="checkbox" name="so_details_id[]" class="simple-check ${inUse ? 'in-use' : ''} ${data.so_data[idx].in_use == true ? 'in_use_check' : ''}" id="so_details_ids_${data.so_data[idx].so_details_id}" value="${data.so_data[idx].so_details_id}" ${inUse ? 'checked' : isItemUsedchecked ? '' : 'checked'} ${data.so_data[idx].in_use == true ? 'readonly' : ''} onchange="getItemCheck(this)" data-so_id="${data.so_data[idx].id}"/></td> `;
                                // }



                            }

                            tblitemHtml += `<td>${data.so_data[idx].item_name}</td>
                                    <td>${data.so_data[idx].item_code}</td>
                                    <td>${data.so_data[idx].item_group_name}</td>
                                    <td>${data.so_data[idx].unitName}</td>
                                    <td>${parseFloat(data.so_data[idx].org_so_qty).toFixed(3)}</td>`;
                            if (formId == undefined) {
                                tblitemHtml += `<td>${parseFloat(data.so_data[idx].pend_so_qty).toFixed(3)}</td>
                                <td>${parseFloat(data.so_data[idx].stock_qty).toFixed(3)}</td>`;
                            } else {
                                if (data.so_data[idx].fitting_item == 'yes') {
                                    tblitemHtml += `<td>${parseFloat(data.so_data[idx].show_pend_so_qty + data.so_data[idx].so_qty).toFixed(3)}</td>
                                    <td> ${parseFloat(data.so_data[idx].stock_qty + data.so_data[idx].so_qty).toFixed(3)}</td>`;
                                    // tblitemHtml += `<td>${parseFloat(data.so_data[idx].pend_so_qty + data.so_data[idx].so_qty).toFixed(3)}</td>
                                    // <td> ${parseFloat(data.so_data[idx].stock_qty).toFixed(3)}</td>`;
                                } else {
                                    tblitemHtml += `<td>${parseFloat(data.so_data[idx].show_pend_so_qty + data.so_data[idx].so_qty).toFixed(3)}</td>
                                    <td> ${parseFloat(data.so_data[idx].stock_qty + data.so_data[idx].so_qty).toFixed(3)}</td>`;
                                    // tblitemHtml += `<td>${parseFloat(data.so_data[idx].pend_so_qty + data.so_data[idx].so_qty).toFixed(3)}</td>
                                    // <td> ${parseFloat(data.so_data[idx].stock_qty + data.so_data[idx].so_qty).toFixed(3)}</td>`;
                                }
                            }
                            tblitemHtml += `<td>${data.so_data[idx].remarks != null ? data.so_data[idx].remarks : ''}</td></tr> `;


                            // if (data.so_data[idx].dp_details_id == 0) {
                            //Check if the so_details_id is already in the array
                            if (!chkArr[data.so_data[idx].id]) {
                                // If the property doesn't exist, initialize it as an empty array
                                chkArr[data.so_data[idx].id] = [];
                            }


                            if (!chkArr[data.so_data[idx].id].includes(data.so_data[idx].so_details_id)) {
                                // If not, push the new so_details_id
                                chkArr[data.so_data[idx].id].push(data.so_data[idx].so_details_id);
                            } else {
                                // Otherwise, initialize it with the current so_details_id
                                chkArr[data.so_data[idx].id] = [data.so_data[idx].so_details_id];
                            }
                            // }

                            if (idx == lastKey) {
                                setTimeout(() => {
                                    jQuery('#pendingBtnSOModal').prop('disabled', false);
                                }, 1000);
                            } else {
                                jQuery('#pendingBtnSOModal').prop('disabled', true);
                            }
                        }


                        // chkArr = chkArr.filter(item => !sochkItem.includes(item));
                        for (let key in checkItem) {
                            if (chkArr.hasOwnProperty(key)) {
                                // Remove values in sochkItem[key] from chkArr[key]
                                chkArr[key] = chkArr[key].filter(item => !checkItem[key].includes(item));
                            }
                        }




                    } else {

                        tblitemHtml += `   <tr class="centeralign" id="noPendingPo">
                                <td colspan="9" id="itemloader">No record found!</td>
                            </tr>`;

                    }
                    // setTimeout(() => {
                    //     jQuery('#pendingBtnSOModal').prop('disabled', false);
                    // }, 500)
                    jQuery('#pendingSOTable tbody').find('#noPendingDc').find('td').removeClass('file-loader')
                    var $table1 = jQuery("#pendingSOModal").find('#pendingSOTable');

                    if (jQuery.fn.DataTable.isDataTable($table1)) {
                        $table1.DataTable().destroy();

                    }

                    jQuery('#pendingSOTable tbody').empty().append(tblitemHtml);
                    $table1.DataTable({
                        pageLength: 50,
                        paging: false,
                        info: false,
                        searching: true,
                        "oLanguage": {
                            "sSearch": "Search :"
                        },
                        columnDefs: [{
                            targets: 0,
                            orderDataType: 'dom-checkbox'
                        }]
                    });

                    if (formId != undefined) {
                        setTimeout(() => {
                            if (isAnyPartUse == true) {
                                jQuery('.toggleModalBtn').prop('disabled', true);
                            } else {

                                jQuery('.toggleModalBtn').prop('disabled', false);
                            }
                        }, 1000)
                    }

                } else {
                    toastError(data.response_message);
                }
            },

            error: function (jqXHR, textStatus, errorThrown) {
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
        var tblitemHtml = `   <tr class="centeralign" id="noPendingPo">
                                <td colspan="7" id="itemloader">No record found!</td>
                            </tr>`;

        var $table1 = jQuery("#pendingSOModal").find('#pendingSOTable');
        jQuery('#pendingBtnSOModal').prop('disabled', true);

        if (jQuery.fn.DataTable.isDataTable($table1)) {
            $table1.DataTable().destroy();

        }
        jQuery('#pendingSOTable tbody').empty();

        $table1.DataTable({
            pageLength: 50,
            paging: false,
            info: false,
            searching: true,
            "oLanguage": {
                "sSearch": "Search :"
            },
            columnDefs: [{
                targets: 0,
                orderDataType: 'dom-checkbox'
            }]
        });
    }


}


var coaPartValidator = jQuery("#addPendingSOForm").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,
    rules: {
        "checkall_so[]": {
            required: true
        },
    },

    messages: {
        "checkall_so[]": {
            required: "Please Select Item From Pending SO",
        }
    },

    submitHandler: function (form) {
        var chkCount = 0;
        // var chkArr = [];
        // var chkId = [];

        // jQuery("#addPendingSOForm").find("[id^='so_details_ids_']").each(function () {

        //     var thisId = jQuery(this).attr('id');
        //     var splt = thisId.split('so_details_ids_');
        //     var intId = splt[1];

        //     if (jQuery(this).is(':checked')) {
        //         chkArr.push(jQuery(this).val())
        //         chkId.push(intId);
        //         chkCount++;
        //     }

        // });

        // if (chkCount == 0) {
        //     toastError('Please Select Item From Pending SO');
        // } else {

        for (let key in checkItem) {
            if (chkArr.hasOwnProperty(key)) {
                // Remove values in sochkItem[key] from chkArr[key]
                chkArr[key] = chkArr[key].filter(item => !checkItem[key].includes(item));
            } else {
                console.log(checkItem[key])
                chkArr[key].push(checkItem[key]);
            }
        }

        if (chkArr.length == 0) {
            toastError('Please Select Item From Pending SO');
        } else {

            var chkArray = chkArr.filter(item => item !== "" && item !== undefined && item !== null);
            if (formId == undefined) {
                var url = RouteBasePath + "/get-so_part_data-dispatch?so_details_ids=" + chkArray.join(',');
            } else {
                var url = RouteBasePath + "/get-so_part_data-dispatch?so_details_ids=" + chkArray.join(',') + "&id=" + formId;
            }

            jQuery.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {

                    if (data.response_code == 1) {
                        so_data = [];
                        if (data.so_data.length > 0 && !jQuery.isEmptyObject(data.so_data)) {
                            jQuery("input:radio[name='dispatch_from_id_fix']").prop({ tabindex: -1 }).attr('readonly', true);
                            for (let ind in data.so_data) {
                                so_data.push(data.so_data[ind]);

                                if (!jQuery.isEmptyObject(data.so_data[ind].sodDetailArray)) {
                                    for (let key in data.so_data[ind].sodDetailArray) {
                                        sodDetailArray[data.so_data[ind].so_details_id] = data.so_data[ind].sodDetailArray[key];
                                    }
                                }
                            }

                            fillDispatchTable();
                        }

                        // for (let ind in data.so_data) {

                        //     if (Array.isArray(sodDetailArray[data.so_data[ind].so_details_id])) {
                        //         const sum = sodDetailArray[data.so_data[ind].so_details_id].reduce((total, item) => {
                        //             return total + parseFloat(item.plan_qty);
                        //         }, 0);

                        //         jQuery("#DipatchPlanTable tbody tr").each(function () {
                        //             let $tr = jQuery(this);

                        //             let rowSodId = $tr.find('input[name="so_detail_id[]"]').val();

                        //             if (rowSodId == data.so_data[ind].so_details_id) {
                        //                 $tr.closest('tr').find("input[name='check_plan_qty[]']").val(parseFloat(sum).toFixed(3));
                        //             }
                        //         });


                        //     }

                        // }





                        jQuery("#pendingSOModal").modal('hide');

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




function fillDispatchTable() {

    var fill_dispatch_from_id_fix = jQuery('input[name="dispatch_from_id_fix"]:checked').val();

    if (so_data.length > 0) {
        var tblHtml = ``;
        let sr_no = 0;

        for (let idx in so_data) {
            ++sr_no;

            if (so_data[idx].fitting_item == 'yes') {
                // var addCss = so_data[idx].dp_details_id == 0 ? so_data[idx].stock_qty == 1 ? '' : 'background:#f39898' : '';
                var addCss = '';
            } else {
                if (so_data[idx].secondary_unit == 'Yes') {
                    var addCss = '';
                } else {
                    var addCss = so_data[idx].dp_details_id == 0 ? so_data[idx].stock_qty < so_data[idx].pend_so_qty ? 'background:#f39898' : '' : '';
                }

            }

            tblHtml += `
            <tr style="${addCss}">`;

            if (so_data[idx].so_from_value_fix == 'customer') {
                // tblHtml += `<td></td>`;
                if (so_data[idx].in_use == true) {
                    tblHtml += `<td><i class="action-icon iconfa-trash so_details form_custome_type"></i></td>`;
                } else {
                    tblHtml += `<td><a onclick="removeSODetails(this)" class="form_custome_type"><i class="action-icon iconfa-trash so_details"></i></a></td>`;
                }
            } else {
                // if (so_data[idx].allow_partial_dispatch == 'No') {
                //     tblHtml += `<td></td>`;
                // } else {
                if (so_data[idx].in_use == true) {
                    tblHtml += `<td><i class="action-icon iconfa-trash so_details"></i></td>`;
                } else {
                    tblHtml += `<td><a onclick="removeSODetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>`;
                }
                // }


            }



            tblHtml += `<td> ${so_data[idx].so_number}
                        <input type="hidden" name="sod_item_id[]" value="${so_data[idx].item_id != null ? so_data[idx].item_id : ""}" > 
                        <input type="hidden" name="fitting_item[]" value="${so_data[idx].fitting_item != null ? so_data[idx].fitting_item : ""}" > 
                        <input type="hidden" name="require_qty[]" value="${isNaN(so_data[idx].require_qty) ? 0 : parseFloat(so_data[idx].require_qty).toFixed(3)}" > 
                        <input type="hidden" name="check_plan_qty[]" value="${isNaN(so_data[idx].check_plan_qty) ? 0 : parseFloat(so_data[idx].check_plan_qty).toFixed(3)}" > 
                        <input type="hidden" name="secondary_unit[]" value="${so_data[idx].secondary_unit}" >                     
                        <input type="hidden" name="allow_partial_dispatch[]" value="${so_data[idx].allow_partial_dispatch}" > 
                        <input type="hidden" name="so_from_value_fix[]" value="${so_data[idx].so_from_value_fix}" > 
                        <input type="hidden" name="org_plan_qty[]" value="${isNaN(so_data[idx].so_qty) ? 0 : parseFloat(so_data[idx].so_qty).toFixed(3)}" >
                        <input type="hidden" name="dp_details_id[]" value="${formId == undefined ? 0 : so_data[idx].dp_details_id != null ? so_data[idx].dp_details_id : 0}" >
                        <input type="hidden" name="form_indx" value="${idx}" />
                        <input type="hidden" name="so_detail_id[]" id="so_detail_ids_${so_data[idx].so_details_id}" value="${so_data[idx].so_details_id}" />
                        <input type="hidden" name="so_id[]" id="so_ids_${so_data[idx].id}" value="${so_data[idx].id}" />
                        <input type="hidden" name="allow_multi_vehicle[]" value="${so_data[idx].allow_multi_vehicle}" />
                        <input type="hidden" name="wt_pc[]"  value="${parseFloat(so_data[idx].wt_pc).toFixed(3)}">
                    </td>
                    <td>${so_data[idx].so_date}</td>
                    <td>${so_data[idx].name}</td>
                    <td>${so_data[idx].customer_group_name != null ? so_data[idx].customer_group_name : ""}</td>
                    <td>${so_data[idx].customer_village != null ? so_data[idx].customer_village : ""}</td>
                    <td>${so_data[idx].district_name != null ? so_data[idx].district_name : ""}</td>
                    <td>${so_data[idx].dealer_name != null ? so_data[idx].dealer_name : ""}</td>`;

            if (so_data[idx].fitting_item == 'yes') {

                if (so_data[idx].in_use == true) {
                    tblHtml += `<td>${so_data[idx].item_name} <span><a><i class="action-icon iconfa-eye-open"></i></a></span></td>`;
                } else {
                    tblHtml += `<td>${so_data[idx].item_name} <span><a><i class="action-icon iconfa-eye-open eyeIcon1"></i></a></span></td>`;
                }

            } else {
                if (so_data[idx].secondary_unit == 'Yes') {
                    if (so_data[idx].in_use == true) {
                        tblHtml += `<td>${so_data[idx].item_name} <span><a><i class="action-icon iconfa-eye-open"></i></a></span></td>`;
                    } else {
                        tblHtml += `<td>${so_data[idx].item_name} <span><a><i class="action-icon iconfa-eye-open eyeSecondaryIcon1"></i></a></span></td>`;
                    }
                } else {
                    tblHtml += `<td>${so_data[idx].item_name}</td>`;
                }
            }
            tblHtml += `<td>${so_data[idx].item_code}</td>
                <td>${so_data[idx].item_group_name}</td>
                <td>${so_data[idx].unitName}</td>`;
            if (formId == undefined) {

                tblHtml += `
                 <td>${parseFloat(so_data[idx].pend_so_qty).toFixed(3)}</td>

                <td>${parseFloat(so_data[idx].stock_qty).toFixed(3)}</td>`;

                if (so_data[idx].fitting_item == 'yes') {

                    tblHtml += `<td><input type="text" name="plan_qty[]"  id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys"  onblur="formatPoints(this,3)" value="0.000" readonly tabindex="-1" onkeyup="sumTotalWt(this)">
                    </td>`;
                    var total_wt = 0;
                } else {
                    if (so_data[idx].secondary_unit == 'Yes') {
                        tblHtml += `<td><input type="text" name="plan_qty[]"  max="${parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty) ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty).toFixed(3)}"  id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys"  onblur="formatPoints(this,3)" value="0.000" readonly tabindex="-1" onkeyup="sumTotalWt(this)">
                    </td>`;

                        var total_wt = 0;

                    } else {

                        if (so_data[idx].so_from_value_fix == 'cash_carry' || so_data[idx].so_from_value_fix == 'location') {
                            // if (fill_dispatch_from_id_fix == 2 || fill_dispatch_from_id_fix == 3) {
                            // if (so_data[idx].allow_partial_dispatch == 'No') {
                            //     tblHtml += `<td><input type="text" name="plan_qty[]"  id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys"  onblur="formatPoints(this,3)" value="${parseFloat(so_data[idx].pend_so_qty).toFixed(3)}" readonly tabindex="-1" onkeyup="sumTotalWt(this)">

                            //     <input type="hidden" name="max_qty[]" id="max_qty[]" value="${parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty) ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty).toFixed(3)}">

                            //     </td>`;

                            //     var total_wt = parseFloat(so_data[idx].pend_so_qty) * parseFloat(so_data[idx].wt_pc);
                            // } else {

                            tblHtml += `<td><input type="text" name="plan_qty[]" max="${parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty) ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty).toFixed(3)}" id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys"  onblur="formatPoints(this,3)" value="${parseFloat(so_data[idx].pend_so_qty).toFixed(3)}" onkeyup="sumTotalWt(this)">

                                <input type="hidden" name="max_qty[]" id="max_qty[]" value="${parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty) ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty).toFixed(3)}">
                                
                                </td>`;

                            var total_wt = parseFloat(so_data[idx].pend_so_qty) * parseFloat(so_data[idx].wt_pc);
                            // }

                        } else {

                            if (so_data[idx].allow_partial_dispatch == 'No') {
                                tblHtml += `<td><input type="text" name="plan_qty[]" max="${parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty) ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty).toFixed(3)}"  id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys"  onblur="formatPoints(this,3)" value="${parseFloat(so_data[idx].pend_so_qty).toFixed(3)}" readonly tabindex="-1" onkeyup="sumTotalWt(this)">

                                <input type="hidden" name="max_qty[]" id="max_qty[]" value="${parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty) ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty).toFixed(3)}">
                                
                                </td>`;
                            } else {
                                tblHtml += `<td><input type="text" name="plan_qty[]" max="${parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty) ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty).toFixed(3)}"  id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys"  onblur="formatPoints(this,3)" value="${parseFloat(so_data[idx].pend_so_qty).toFixed(3)}"  onkeyup="sumTotalWt(this)">

                                <input type="hidden" name="max_qty[]" id="max_qty[]" value="${parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty) ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty).toFixed(3)}">
                                
                                </td>`;

                            }

                            var total_wt = parseFloat(so_data[idx].pend_so_qty) * parseFloat(so_data[idx].wt_pc);

                        }


                    }


                }

            } else {

                tblHtml += `<td>${parseFloat(so_data[idx].show_pend_qty + so_data[idx].so_qty).toFixed(3)}</td>`;
                if (so_data[idx].fitting_item == 'yes') {
                    tblHtml += ` <td>${parseFloat(so_data[idx].stock_qty).toFixed(3)}</td>`;
                } else {

                    tblHtml += ` <td>${parseFloat(so_data[idx].stock_qty + so_data[idx].so_qty).toFixed(3)}</td>`;
                }



                if (so_data[idx].dp_details_id == 0) {
                    var total_max_qty = parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty + so_data[idx].so_qty) ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty + so_data[idx].so_qty).toFixed(3);
                } else {
                    var total_max_qty = parseFloat(so_data[idx].pend_so_qty) < parseFloat(so_data[idx].stock_qty + so_data[idx].so_qty) ? parseFloat(so_data[idx].pend_so_qty + so_data[idx].so_qty).toFixed(3) : parseFloat(so_data[idx].stock_qty + so_data[idx].so_qty).toFixed(3);

                }



                if (so_data[idx].fitting_item == 'yes') {

                    tblHtml += `<td><input type="text" name="plan_qty[]"  id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys"  onblur="formatPoints(this,3)" value="0.000" readonly tabindex="-1" onkeyup="sumTotalWt(this)">
                    </td>`;

                    var total_wt = 0;
                } else {

                    if (so_data[idx].in_use == true) {
                        tblHtml += ` <td><input type="text" name="plan_qty[]" min="${parseFloat(so_data[idx].used_qty).toFixed(3)}" max="${total_max_qty}" id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" value="${so_data[idx].dp_details_id == 0 ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty).toFixed(3) : ''}" readonly tabindex="-1" onkeyup="sumTotalWt(this)">
                        </td>`;

                        if (so_data[idx].secondary_unit == 'No') {

                            var total_wt = so_data[idx].dp_details_id == 0 ? parseFloat(so_data[idx].pend_so_qty) * parseFloat(so_data[idx].wt_pc) : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty) * parseFloat(so_data[idx].wt_pc) : 0;
                        } else {
                            var total_wt = so_data[idx].dp_details_id == 0 ? 0 : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].wt_pc) : 0;
                        }
                    } else {

                        if (so_data[idx].secondary_unit == 'Yes') {
                            tblHtml += ` <td><input type="text" name="plan_qty[]" min="${parseFloat(so_data[idx].used_qty).toFixed(3)}" max="${total_max_qty}" id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" value="${so_data[idx].dp_details_id == 0 ? parseFloat(0).toFixed(3) : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty).toFixed(3) : ''}" readonly tabindex="-1" onkeyup="sumTotalWt(this)">
                            </td>`;

                            // var total_wt = so_data[idx].dp_details_id == 0 ? 0 : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty) * parseFloat(so_data[idx].wt_pc) : 0;
                            var total_wt = so_data[idx].dp_details_id == 0 ? 0 : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].wt_pc) : 0;
                        } else {
                            if (so_data[idx].so_from_value_fix == 'customer') {
                                // if (fill_dispatch_from_id_fix == 2) {

                                if (so_data[idx].allow_partial_dispatch == 'No') {
                                    tblHtml += ` <td><input type="text" name="plan_qty[]" min="${parseFloat(so_data[idx].used_qty).toFixed(3)}" id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" value="${so_data[idx].dp_details_id == 0 ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty).toFixed(3) : ''}" readonly tabindex="-1" onkeyup="sumTotalWt(this)">

                                    <input type="hidden" name="max_qty[]" id="max_qty[]" value="${total_max_qty}">


                                    </td>`;
                                    if (so_data[idx].secondary_unit == 'No') {
                                        var total_wt = so_data[idx].dp_details_id == 0 ? parseFloat(so_data[idx].pend_so_qty) * parseFloat(so_data[idx].wt_pc) : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty) * parseFloat(so_data[idx].wt_pc) : 0;
                                    } else {
                                        var total_wt = so_data[idx].dp_details_id == 0 ? 0 : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].wt_pc) : 0;
                                    }

                                } else {
                                    tblHtml += ` <td><input type="text" name="plan_qty[]" min="${parseFloat(so_data[idx].used_qty).toFixed(3)}" max="${total_max_qty}" id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" value="${so_data[idx].dp_details_id == 0 ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty).toFixed(3) : ''}" onkeyup="sumTotalWt(this)">

                                     <input type="hidden" name="max_qty[]" id="max_qty[]" value="${total_max_qty}">

                                    </td>`;
                                    if (so_data[idx].secondary_unit == 'No') {
                                        var total_wt = so_data[idx].dp_details_id == 0 ? parseFloat(so_data[idx].pend_so_qty) * parseFloat(so_data[idx].wt_pc) : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty) * parseFloat(so_data[idx].wt_pc) : 0;
                                    } else {
                                        var total_wt = so_data[idx].dp_details_id == 0 ? 0 : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].wt_pc) : 0;
                                    }
                                }

                            } else {
                                tblHtml += ` <td><input type="text" name="plan_qty[]" min="${parseFloat(so_data[idx].used_qty).toFixed(3)}" max="${total_max_qty}" id="plan_qty_${so_data[idx].id}" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" value="${so_data[idx].dp_details_id == 0 ? parseFloat(so_data[idx].pend_so_qty).toFixed(3) : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty).toFixed(3) : ''}"  onkeyup="sumTotalWt(this)">

                                <input type="hidden" name="max_qty[]" id="max_qty[]" value="${total_max_qty}">

                                </td>`;

                                if (so_data[idx].secondary_unit == 'No') {

                                    var total_wt = so_data[idx].dp_details_id == 0 ? parseFloat(so_data[idx].pend_so_qty) * parseFloat(so_data[idx].wt_pc) : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].so_qty) * parseFloat(so_data[idx].wt_pc) : 0;
                                } else {
                                    var total_wt = so_data[idx].dp_details_id == 0 ? 0 : so_data[idx].so_qty > 0 ? parseFloat(so_data[idx].wt_pc) : 0;

                                }

                            }
                        }

                    }
                }


            }
            // tblHtml += `<td class="item_wt_pc"><input type="hidden" name="wt_pc[]"  value="${parseFloat(so_data[idx].wt_pc).toFixed(3)}">${parseFloat(so_data[idx].wt_pc).toFixed(3)}</td>`;

            tblHtml += `<td class="total_wt">${parseFloat(total_wt).toFixed(3)}</td>`;

            tblHtml += `</tr > `;
        }


    } else {
        tblHtml += `   <tr class="centeralign" id="noPendingPo">
                                <td colspan="7" id="itemloader">No record found!</td>
                            </tr>`;
    }

    jQuery('#DipatchPlanTable tbody').empty().append(tblHtml);

    checkCustomerData();


    TotalWt();

}


function removeSODetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            jQuery(th).parents("tr").remove();

            var value = Number(jQuery(th).parents("tr").find('input[name="so_detail_id[]"]').val());
            var idx = jQuery(th).parents("tr").find('input[name="so_id[]"]').val();

            if (checkItem.hasOwnProperty(idx)) {
                // Check if the value already exists in the array
                if (checkItem[idx].includes(value)) {
                    // Remove the value from the array if it exists
                    checkItem[idx] = checkItem[idx].filter(item => item !== value);
                } else {
                    // Add the value to the array if it doesn't exist
                    checkItem[idx].push(value);
                }
            } else {
                // If the key doesn't exist, initialize it as an empty array and add the value
                checkItem[idx] = [value];
            }
            // if (chkArr.hasOwnProperty(idx)) {
            //     // Check if the value already exists in the array
            //     if (chkArr[idx].includes(value)) {
            //         // Remove the value from the array if it exists
            //         chkArr[idx] = chkArr[idx].filter(item => item !== value);
            //     } else {
            //         // Add the value to the array if it doesn't exist
            //         chkArr[idx].push(value);
            //     }
            // } else {
            //     // If the key doesn't exist, initialize it as an empty array and add the value
            //     chkArr[idx] = [value];
            // }

            TotalWt();

        }

    });
}




jQuery('#pendingSOModal').on('show.bs.modal', function (e) {
    // jQuery('#pendingSODataTable tbody').empty();
    // dispatchType();
    var $table3 = jQuery('#pendingSOTable').DataTable();
    var $table4 = jQuery('#pendingSODataTable').DataTable();

    setTimeout(function () {
        $table4.columns.adjust().draw();
    }, 200);

    $table3.search('').draw();
    $table4.draw();
    var usedParts = [];
    var totalDisb = 0;

    jQuery('#DipatchPlanTable tbody input[name="form_indx"]').each(function (indx) {
        let frmIndx = jQuery(this).val();
        let sodId = so_data[frmIndx].so_details_id;

        if (sodId != "" && sodId != null) {
            usedParts.push(Number(sodId));
        }
    });
    // function isUsed(pjId) {
    //     if (usedParts.includes(Number(pjId))) {
    //         totalDisb++;
    //         return true;
    //     }
    //     return false;
    // }


    function isItemUsedchecked(pjId, idx) {
        if (checkItem.hasOwnProperty(idx)) {
            if (checkItem[idx].includes(Number(pjId))) {
                return true;

            }
        }

        return false;
    }
    // console.log(checkItem)
    var totalEntry = 0;

    jQuery('#pendingSOTable tbody tr').each(function (indx) {
        totalEntry++;
        let checkField = jQuery(this).find('input[name="so_details_id[]"]');
        var so_id = jQuery(jQuery(this).find('input[name="so_details_id[]"]')[0]).attr('data-so_id');
        let partId = jQuery(checkField).val();
        // let inUse = isUsed(partId);
        let inUse = isItemUsedchecked(partId, so_id);

        if (inUse) {
            jQuery(checkField).removeClass('in-use').prop('checked', false);

        } else {
            jQuery(checkField).addClass('in-use').prop('checked', true);
        }


    });


    var usedItemParts = [];
    var totalItemDisb = 0;

    jQuery('#DipatchPlanTable tbody input[name="form_indx"]').each(function (indx) {
        var frmIndx = jQuery(this).val();
        var podItemId = so_data[frmIndx].id;
        if (podItemId != "" && podItemId != null) {

            usedItemParts.push(Number(podItemId));
        }
    });


    function isItemUsed(pjitemId) {
        if (usedItemParts.includes(Number(pjitemId))) {
            totalItemDisb++;
            return true;
        }
        return false;
    }

    var totalEntry = 0;
    var check_count = 0;
    jQuery('#pendingSODataTable tbody tr').each(function (indx) {

        totalEntry++;
        var checkField = jQuery(this).find('input[name="so_id[]"]');
        var partId = jQuery(checkField).val();
        var inUse = isItemUsed(partId);

        if (inUse) {
            jQuery(checkField).addClass('in-use').prop('checked', true);
            check_count++;
        } else {
            jQuery(checkField).removeClass('in-use').prop('checked', false);
            delete checkItem[partId];
            // getSODetailData()

            if (chkSOId.includes(partId)) {
                chkSOId = chkSOId.filter(item => item !== partId);
                getSODetailData()

            }
        }

    });

    if (check_count == 0) {
        var tblHtml = `<tr class="centeralign" id = "noPendingDc" >
                <td colspan="11">No record found!</td>
        </tr> `;
        jQuery('#pendingSOTable tbody').empty().append(tblHtml);

    }


});


jQuery('#checkall-so').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#addPendingSOForm").find("[id^='so_details_ids_']:not(.in_use_check)").prop('checked', true).trigger('change');
    } else {
        jQuery("#addPendingSOForm").find("[id^='so_details_ids_']:not(.in_use_check)").prop('checked', false).trigger('change');
    }
});


jQuery('#checkall-so_data').click(function () {
    if (jQuery(this).is(':checked')) {
        // jQuery("#addPendingSODataForm").find("[id^='so_ids_']:not(.in_use_check)").prop('checked', true).trigger('change');
        jQuery("#addPendingSODataForm").find("[id^='so_ids_']:not(.in_use_check)").prop('checked', true);
        getSODetailData();
    } else {
        // jQuery("#addPendingSODataForm").find("[id^='so_ids_']:not(.in_use_check)").prop('checked', false).trigger('change');
        jQuery("#addPendingSODataForm").find("[id^='so_ids_']:not(.in_use_check)").prop('checked', false);
        getSODetailData();
        chkArr = [];
    }
});



function getItemCheck(id) {

    var value = Number(id.value);
    var idx = jQuery(id).attr('data-so_id');


    // if (!jQuery(id).is(':checked')) {
    //     if (chkArr.hasOwnProperty(idx)) {
    //         chkArr[idx] = chkArr[idx].filter(item => item !== value);
    //     }
    // }




    // console.log(chkArr)

    // Check if the key exists in the object
    if (checkItem.hasOwnProperty(idx)) {
        // Check if the value already exists in the array
        if (checkItem[idx].includes(value)) {
            // Remove the value from the array if it exists
            checkItem[idx] = checkItem[idx].filter(item => item !== value);
        } else {
            // Add the value to the array if it doesn't exist
            checkItem[idx].push(value);
        }
    } else {
        // If the key doesn't exist, initialize it as an empty array and add the value
        checkItem[idx] = [value];
    }

    if (!jQuery(id).is(':checked')) {
        for (let key in checkItem) {
            if (chkArr.hasOwnProperty(key)) {
                // Remove values in sochkItem[key] from chkArr[key]
                chkArr[key] = chkArr[key].filter(item => !checkItem[key].includes(item));
            }
        }
    } else {
        if (chkArr.hasOwnProperty(idx)) {
            if (chkArr[idx].includes(value)) {
            } else {
                chkArr[idx].push(value);
            }
        } else {
            chkArr[idx] = [value];
        }
    }

    return checkItem;




}




// function getSOArray(id) {
//     var value = Number(id.value);

//     if (chkSOId.includes(value)) {
//         chkSOId = chkSOId.filter(function (item) {
//             return item !== value;
//         });
//     } else {
//         if (!chkSOId.includes(value)) {
//             chkSOId.push(value);
//         }
//     }
//     getSODetailData()
// }


jQuery(document).on('click', '.eyeIcon1', function () {
    var td = jQuery(this).closest('td');
    var so_detail_id = td.closest('tr').find("input[name='so_detail_id[]']").val();

    if (so_detail_id != '') {

        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/get-fitting_so_item_data_for_dispatch?id=" + formId + "&so_detail_id=" + so_detail_id : RouteBasePath + "/get-fitting_so_item_data_for_dispatch?so_detail_id=" + so_detail_id;

        jQuery.ajax({
            url: formUrl,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    var tblHtml = '';

                    jQuery("#SoFittingForDispatchModal").find('#so_details_id').val(so_detail_id);

                    var checkItemLength = sodDetailArray[so_detail_id] != undefined ? 1 : 0;

                    for (let idx in data.soFittingItem) {

                        if (data.soFittingItem[idx].dpd_details_id == 0) {
                            var max_qty = parseFloat(data.soFittingItem[idx].pend_sod_qty) < parseFloat(data.soFittingItem[idx].stock_qty) ? parseFloat(data.soFittingItem[idx].pend_sod_qty).toFixed(3) : parseFloat(data.soFittingItem[idx].stock_qty).toFixed(3);
                        } else {
                            var max_qty = parseFloat(data.soFittingItem[idx].stock_qty) > 0 ? parseFloat(data.soFittingItem[idx].stock_qty) ? parseFloat(data.soFittingItem[idx].pend_sod_qty).toFixed(3) : parseFloat(data.soFittingItem[idx].stock_qty).toFixed(3) : parseFloat(data.soFittingItem[idx].pend_sod_qty).toFixed(3);
                        }


                        if (checkItemLength) {
                            var indetailUse = sodDetailArray[so_detail_id].filter((value) => Number(value.so_details_detail_id) == data.soFittingItem[idx].sod_details_id);
                        } else {
                            var indetailUse = [];
                        }

                        var detailQtyUse = indetailUse.length ? indetailUse[0].plan_qty : parseFloat(data.soFittingItem[idx].pend_sod_qty).toFixed(3);


                        indetailUse = indetailUse.length ? true : false;

                        tblHtml += `<tr>        
                        <td><input type="checkbox" name="so_details_detail_id[]" class="simple-check" id="so_details_detail_ids_${data.soFittingItem[idx].sod_details_id}" value="${data.soFittingItem[idx].sod_details_id}"  onchange="manageDetailsQtyfield(this)" ${indetailUse ? 'checked' : ''}/></td>                               
                        <td>
                        <input type="hidden" name="item_id[]" value="${data.soFittingItem[idx].item_id}">
                        <input type="hidden" name="dpd_details_id[]" value="${data.soFittingItem[idx].dpd_details_id}">                                    
                        ${data.soFittingItem[idx].item_name}</td>
                        <td>${data.soFittingItem[idx].item_code}</td>
                        <td>${data.soFittingItem[idx].item_group_name}</td>                                
                        <td>${data.soFittingItem[idx].unit_name}</td>                                
                        <td>${parseFloat(data.soFittingItem[idx].pend_sod_qty).toFixed(3)}</td>                                
                        <td>${parseFloat(data.soFittingItem[idx].stock_qty).toFixed(3)}</td>  
                        <td>
                            <input type="hidden" name="org_plan_qty[]" value="${data.soFittingItem[idx].dpd_details_id == 0 ? parseFloat(data.soFittingItem[idx].pend_sod_qty).toFixed(3) : parseFloat(data.soFittingItem[idx].org_plan_qty).toFixed(3)}">
                        <input type="text" class="input-mini  only-numbers plan_qtys"name="plan_qty[]" value="${indetailUse ? detailQtyUse : ''}" max="${max_qty}" ${indetailUse ? '' : 'disabled'}>
                        </td>                          
                        </tr>`;

                    }


                    jQuery('#SoFittingForDispatchModalTable tbody').empty().append(tblHtml);
                    jQuery("#SoFittingForDispatchModal").modal('show');

                }

            }
        });

    }


});



var coaPartValidator = jQuery("#addPendingSODForm").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,
    rules: {
        "so_details_detail_id[]": {
            required: true
        },
        "plan_qty[]": {
            required: function (e) {
                if (jQuery(e).prop('disabled')) {
                    return false;
                } else {

                    return true;
                }
            },

            notOnlyZero: '0.001',
        },
    },

    messages: {
        "so_details_detail_id[]": {
            required: "Please Select Item From Pending SO Fitting Item",
        },
        "plan_qty[]": {
            required: "Please Enter Plan Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.',
        },
    },

    submitHandler: function (form) {

        let checkLength = jQuery("#SoFittingForDispatchModalTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;


        if (checkLength < 1) {
            jAlert("Please Add At Least One SO Fitting Item.");
            addModalPartDetail();

            return false;
        }


        let storeArr = [];


        var itemId;
        var planQty;
        var dpdId;
        var orgplanQty;

        var totalPlanqty = 0;

        var index = jQuery("#addPendingSODForm").find('#so_details_id').val();


        jQuery('#SoFittingForDispatchModalTable tbody tr').each(function (e) {
            var sodDetailsId = jQuery(this).find('input[name="so_details_detail_id[]"]');
            if (jQuery(sodDetailsId).is(':checked')) {

                sodDetailsId = jQuery(sodDetailsId).val();

                itemId = jQuery(this).find('input[name="item_id[]"]').val();
                planQty = jQuery(this).find('input[name="plan_qty[]"]').val();
                dpdId = jQuery(this).find('input[name="dpd_details_id[]"]').val();
                orgplanQty = jQuery(this).find('input[name="org_plan_qty[]"]').val();

                totalPlanqty += parseFloat(planQty);


                storeArr.push({ 'so_details_detail_id': sodDetailsId, 'plan_qty': planQty, 'item_id': itemId, 'dpd_details_id': dpdId, 'org_plan_qty': orgplanQty });
            }
        });


        sodDetailArray[index] = storeArr.filter(function (val) {
            return val.item_id !== undefined && val.plan_qty !== undefined;
        }).map(function (val) {
            return {
                so_details_detail_id: val.so_details_detail_id,
                plan_qty: val.plan_qty,
                item_id: val.item_id,
                dpd_details_id: val.dpd_details_id,
                org_plan_qty: val.org_plan_qty,
            };
        });

        jQuery("#SoFittingForDispatchModal").modal('hide');




        jQuery("#DipatchPlanTable tbody tr").each(function () {
            let $tr = jQuery(this);

            let rowSodId = $tr.find('input[name="so_detail_id[]"]').val();

            if (rowSodId == index) {
                $tr.closest('tr').find("input[name='check_plan_qty[]']").val(parseFloat(totalPlanqty).toFixed(3));
            }
        });
    }
});


jQuery(document).on('click', '.eyeSecondaryIcon1', function () {
    var td = jQuery(this).closest('td');
    var so_detail_id = td.closest('tr').find("input[name='so_detail_id[]']").val();
    var secondary_unit = td.closest('tr').find("input[name='secondary_unit[]']").val();

    if (so_detail_id != '' && secondary_unit == 'Yes') {

        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/get-secondary_so_item_data_for_dispatch?id=" + formId + "&so_detail_id=" + so_detail_id : RouteBasePath + "/get-secondary_so_item_data_for_dispatch?so_detail_id=" + so_detail_id;

        jQuery.ajax({
            url: formUrl,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    var tblHtml = '';

                    jQuery("#SoSecondaryForDispatchModal").find('#so_details_id').val(so_detail_id);
                    jQuery("#SoSecondaryForDispatchModal").find('#pend_second_so_qty').val(parseFloat(data.soSecondaryItem[0].pend_sod_qty).toFixed(3));

                    var checkItemLength = sodSecondaryDetailArray[so_detail_id] != undefined ? 1 : 0;


                    for (let idx in data.soSecondaryItem) {

                        if (data.soSecondaryItem[idx].dp_secondary_details_id == 0) {
                            var max_qty = parseFloat(data.soSecondaryItem[idx].pend_sod_qty) < parseFloat(data.soSecondaryItem[idx].secondary_stock_qty) ? parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3) : parseFloat(data.soSecondaryItem[idx].secondary_stock_qty).toFixed(3);
                        } else {
                            var max_qty = parseFloat(data.soSecondaryItem[idx].secondary_stock_qty) > 0 ? parseFloat(data.soSecondaryItem[idx].secondary_stock_qty) ? parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3) : parseFloat(data.soSecondaryItem[idx].secondary_stock_qty).toFixed(3) : parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3);
                        }


                        if (checkItemLength) {
                            var inseconddetailUse = sodSecondaryDetailArray[so_detail_id].filter((value) => Number(value.item_details_id) == data.soSecondaryItem[idx].item_details_id);
                        } else {
                            var inseconddetailUse = [];
                        }


                        var detailQtyUse = inseconddetailUse.length ? inseconddetailUse[0].plan_qty :data.soSecondaryItem[idx].pend_sod_qty;





                        inseconddetailUse = inseconddetailUse.length ? true : false;

                        // tblHtml += `<tr>        
                        // <td><input type="checkbox" name="item_details_id[]" class="simple-check" id="item_details_ids_${data.soSecondaryItem[idx].item_details_id}" value="${data.soSecondaryItem[idx].item_details_id}"  onchange="manageDetailsQtyfield(this)" ${inseconddetailUse ? 'checked' : ''}/></td>`;                               
                        tblHtml += `<tr>  <td>
                        <input type="hidden" name="item_details_id[]" value="${data.soSecondaryItem[idx].item_details_id}">
                        <input type="hidden" name="item_id[]" value="${data.soSecondaryItem[idx].item_id}">
                        <input type="hidden" name="so_details_id[]" value="${data.soSecondaryItem[idx].so_details_id}">
                        <input type="hidden" name="dp_secondary_details_id[]" value="${data.soSecondaryItem[idx].dp_secondary_details_id}">                                    
                        <input type="hidden" name="secondary_qty[]" value="${data.soSecondaryItem[idx].secondary_qty}">                                    
                        <input type="hidden" name="secondary_wt_pc[]" value="${data.soSecondaryItem[idx].secondary_wt_pc}">                                    
                        ${data.soSecondaryItem[idx].item_name}</td>
                        <td>${data.soSecondaryItem[idx].item_code}</td>
                        <td>${data.soSecondaryItem[idx].item_group_name}</td>                                
                        <td>${parseFloat(data.soSecondaryItem[idx].stock_qty).toFixed(3)}</td>                                
                        <td>${parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3)}</td>                          
                        <td>${data.soSecondaryItem[idx].unit_name}</td>   
                        <td>${parseFloat(data.soSecondaryItem[idx].secondary_stock_qty).toFixed(3)}</td>
                        <td>
                            <input type="hidden" name="org_plan_qty[]" value="${data.soSecondaryItem[idx].dp_secondary_details_id == 0 ? parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3) : parseFloat(data.soSecondaryItem[idx].org_plan_qty).toFixed(3)}">
                        <input type="text" class="input-mini  only-numbers plan_qtys"  name="plan_qty[]" value="${inseconddetailUse ? detailQtyUse : ''}" max="${max_qty}">
                        </td> 
                        <td>${data.soSecondaryItem[idx].second_unit}</td>                             

                        </tr>`;

                    }

                    jQuery('#SoSecondaryForDispatchModalTable tbody').empty().append(tblHtml);
                    jQuery("#SoSecondaryForDispatchModal").modal('show');

                } else {
                    var tblHtml = `<tr class="centeralign" id="noPendingPo">
                        <td colspan="10">No Item detail Available</td>
                    </tr>`;
                    jQuery('#SoSecondaryForDispatchModalTable tbody').empty().append(tblHtml);
                    jQuery("#SoSecondaryForDispatchModal").modal('show');
                }

            }
        });

    }


});






var coaValidator = jQuery("#addPendingSODSecondaryForm").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,
    rules: {
        // // "item_details_id[]": {
        // //     required: true
        // // },
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
        // "item_details_id[]": {
        //     required: "Please Select Item From Pending SO Seconday Item",
        // },
        "plan_qty[]": {
            // required: "Please Enter Plan Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.',
        },
    },

    submitHandler: function (form) {

        let checkLength = jQuery("#SoSecondaryForDispatchModalTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;


        if (checkLength < 1) {
            jAlert("Please Add At Least One SO Seconday Item.");
            return false;
        }


        var storesecondaryArr = [];


        var itemId;
        var planQty;
        var dpdId;
        var orgplanQty;
        var itemdetailId;

        var totalPlanqty = 0;
        var total_wt = 0;

        var index = jQuery("#addPendingSODSecondaryForm").find('#so_details_id').val();

        var errorQty = true;


        jQuery('#SoSecondaryForDispatchModalTable tbody tr').each(function (e) {
            planQty = jQuery(this).find('input[name="plan_qty[]"]').val();
            if (planQty && parseFloat(planQty) > 0) {
                errorQty = false;
                // if (jQuery(itemdetailId).is(':checked')) {

                // itemdetailId = jQuery(itemdetailId).val();

                itemdetailId = jQuery(this).find('input[name="item_details_id[]"]').val();

                itemId = jQuery(this).find('input[name="item_id[]"]').val();
                soDetailsId = jQuery(this).find('input[name="so_details_id[]"]').val();
                planQty = jQuery(this).find('input[name="plan_qty[]"]').val();
                secondaryQty = jQuery(this).find('input[name="secondary_qty[]"]').val();
                dpdId = jQuery(this).find('input[name="dp_secondary_details_id[]"]').val();
                orgplanQty = jQuery(this).find('input[name="org_plan_qty[]"]').val();
                secondary_wt_pc = jQuery(this).find('input[name="secondary_wt_pc[]"]').val();



                totalPlanqty += parseFloat(planQty) * parseFloat(secondaryQty);
                total_wt += parseFloat(planQty) * parseFloat(secondary_wt_pc);

                storesecondaryArr.push({ 'so_detail_id': soDetailsId, 'plan_qty': planQty, 'item_id': itemId, 'item_details_id': itemdetailId, 'dp_secondary_details_id': dpdId, 'org_plan_qty': orgplanQty });
            }
        });


        sodSecondaryDetailArray[index] = storesecondaryArr.filter(function (val) {
            return val.item_id !== undefined && val.plan_qty !== undefined;
        }).map(function (val) {
            return {
                so_details_id: val.so_detail_id,
                plan_qty: val.plan_qty,
                item_id: val.item_id,
                item_details_id: val.item_details_id,
                dp_secondary_details_id: val.dp_secondary_details_id,
                org_plan_qty: val.org_plan_qty,
            };
        });

        if (errorQty) {
            jAlert("Please Add At Least One SO Seconday Item.");
            return false;
        }
        if (sodSecondaryDetailArray.length < 1) {
            jAlert("Please Add At Least One SO Seconday Item.");
            return false;
        }

        var pend_second_so_qty = jQuery("#addPendingSODSecondaryForm").find('#pend_second_so_qty').val();

        if (parseFloat(pend_second_so_qty) < totalPlanqty) {
            toastError('Plan Qty. is Greater Than Pend. SO Qty.');
            return false;
        }


        jQuery("#DipatchPlanTable tbody tr").each(function () {
            let $tr = jQuery(this);

            let rowSodId = $tr.find('input[name="so_detail_id[]"]').val();

            if (rowSodId == index) {
                $tr.closest('tr').find("input[name='plan_qty[]']").val(parseFloat(totalPlanqty).toFixed(3));
                $tr.closest('tr').find("input[name='check_plan_qty[]']").val(parseFloat(totalPlanqty).toFixed(3));

                // var colest_wt = $tr.closest('tr').find("input[name='wt_pc[]']").val();

                // var totalWt = 0;
                // if (colest_wt != "" && totalPlanqty != "") {
                //     totalWt = parseFloat(totalPlanqty) * parseFloat(colest_wt);
                // }

                $tr.closest('tr').find("input[name='wt_pc[]']").val(parseFloat(total_wt).toFixed(3));
                $tr.closest('tr').find(".total_wt").text(parseFloat(total_wt).toFixed(3));

                TotalWt();


            }
        });

        jQuery("#SoSecondaryForDispatchModal").modal('hide');
    }
});



// jQuery(document).on('click', '.eyeSecondaryIcon2', function () {
//     var td = jQuery(this).closest('td');
//     var so_detail_id = td.closest('tr').find("input[name='so_detail_id[]']").val();
//     var production_assembly = td.closest('tr').find("input[name='production_assembly[]']").val();
//     var sod_item_id = td.closest('tr').find("input[name='sod_item_id[]']").val();

//     if (so_detail_id != '' && production_assembly == 'No') {

//         var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/get-production_assembly_so_item_data_for_dispatch?id=" + formId + "&so_detail_id=" + so_detail_id + "&item_id=" + sod_item_id : RouteBasePath + "/get-production_assembly_so_item_data_for_dispatch?so_detail_id=" + so_detail_id + "&item_id=" + sod_item_id;

//         jQuery.ajax({
//             url: formUrl,
//             type: 'GET',
//             headers: headerOpt,
//             dataType: 'json',
//             processData: false,
//             success: function (data) {
//                 if (data.response_code == 1) {
//                     var tblHtml = '';

//                     jQuery("#SoAssemblyForDispatchModal").find('#so_details_id').val(so_detail_id);
//                     jQuery("#SoAssemblyForDispatchModal").find('#pend_assembly_so_qty').val(parseFloat(data.soSecondaryItem[0].pend_sod_qty).toFixed(3));

//                     var checkItemAssLength = sodSecondaryDetailArray[so_detail_id] != undefined ? 1 : 0;


//                     for (let idx in data.soSecondaryItem) {

//                         if (data.soSecondaryItem[idx].dp_secondary_details_id == 0) {
//                             var max_qty = parseFloat(data.soSecondaryItem[idx].pend_sod_qty) < parseFloat(data.soSecondaryItem[idx].stock_qty) ? parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3) : parseFloat(data.soSecondaryItem[idx].stock_qty).toFixed(3);
//                         } else {
//                             var max_qty = parseFloat(data.soSecondaryItem[idx].stock_qty) > 0 ? parseFloat(data.soSecondaryItem[idx].stock_qty).toFixed(3) : parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3);
//                         }


//                         if (checkItemAssLength) {
//                             var inseconddetailUse = sodSecondaryDetailArray[so_detail_id].filter((value) => Number(value.raw_material_id) == data.soSecondaryItem[idx].raw_material_id);
//                         } else {
//                             var inseconddetailUse = [];
//                         }


//                         var detailQtyUse = inseconddetailUse.length ? parseFloat(inseconddetailUse[0].plan_qty).toFixed(3) : parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3);





//                         inseconddetailUse = inseconddetailUse.length ? true : false;

//                         tblHtml += `<tr>        
//                         <td><input type="checkbox" name="raw_material_id[]" class="simple-check" id="raw_material_ids_${data.soSecondaryItem[idx].raw_material_id}" value="${data.soSecondaryItem[idx].raw_material_id}"  onchange="manageDetailsQtyfield(this)" ${inseconddetailUse ? 'checked' : ''}/></td>                               
//                         <td>
//                         <input type="hidden" name="item_id[]" value="${data.soSecondaryItem[idx].item_id}">
//                         <input type="hidden" name="so_details_id[]" value="${data.soSecondaryItem[idx].so_details_id}">
//                         <input type="hidden" name="dp_secondary_details_id[]" value="${data.soSecondaryItem[idx].dp_secondary_details_id}">                                    
//                         ${data.soSecondaryItem[idx].item_name}</td>
//                         <td>${data.soSecondaryItem[idx].item_code}</td>
//                         <td>${data.soSecondaryItem[idx].item_group_name}</td>                                
//                         <td>${data.soSecondaryItem[idx].unit_name}</td>                                
//                         <td>${parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3)}</td>                                
//                         <td>${parseFloat(data.soSecondaryItem[idx].stock_qty).toFixed(3)}</td>  
//                         <td>         
//                          <input type="hidden" name="org_plan_qty[]" value="${data.soSecondaryItem[idx].dp_secondary_details_id == 0 ? parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3) : parseFloat(data.soSecondaryItem[idx].org_plan_qty).toFixed(3)}">                  
//                          <input type="hidden" name="org_plan_qty[]" value="${data.soSecondaryItem[idx].dp_secondary_details_id == 0 ? parseFloat(data.soSecondaryItem[idx].pend_sod_qty).toFixed(3) : parseFloat(data.soSecondaryItem[idx].org_plan_qty).toFixed(3)}">                  
//                         <input type="text" class="input-mini isNumberKey plan_qtys" onblur="formatPoints(this,3)" name="plan_qty[]" value="${inseconddetailUse ? detailQtyUse : ''}" max="${max_qty}" ${inseconddetailUse ? '' : 'disabled'}>
//                         </td>                          
//                         </tr>`;

//                     }

//                     jQuery('#SoAssemblyForDispatchModalTable tbody').empty().append(tblHtml);
//                     jQuery("#SoAssemblyForDispatchModal").modal('show');

//                 } else {
//                     var tblHtml = `<tr class="centeralign" id="noPendingPo">
//                         <td colspan="10">No Item detail Available</td>
//                     </tr>`;
//                     jQuery('#SoAssemblyForDispatchModalTable tbody').empty().append(tblHtml);
//                     jQuery("#SoAssemblyForDispatchModal").modal('show');
//                 }

//             }
//         });

//     }


// });





// var assValidator = jQuery("#addPendingSODAssemblyForm").validate({
//     rules: {
//         "raw_material_id[]": {
//             required: true
//         },
//         "plan_qty[]": {
//             required: function (e) {
//                 if (jQuery(e).prop('disabled')) {
//                     return false;
//                 } else {

//                     return true;
//                 }
//             },

//             notOnlyZero: '0.001',
//         },
//     },

//     messages: {
//         "raw_material_id[]": {
//             required: "Please Select Item",
//         },
//         "plan_qty[]": {
//             required: "Please Enter Plan Qty.",
//             notOnlyZero: 'Please Enter A Value Greater Than 0.',
//         },
//     },

//     submitHandler: function (form) {

//         let checkLength = jQuery("#SoAssemblyForDispatchModalTable tbody tr").filter(function () {
//             return jQuery(this).css('display') !== 'none';
//         }).length;


//         if (checkLength < 1) {
//             jAlert("Please Add At Least One Mapping Item.");
//             return false;
//         }


//         var storeassemblyArr = [];


//         var itemId;
//         var planQty;
//         var dpdId;
//         var orgplanQty;

//         var totalassPlanqty = 0;

//         var index = jQuery("#addPendingSODAssemblyForm").find('#so_details_id').val();


//         jQuery('#SoAssemblyForDispatchModalTable tbody tr').each(function (e) {
//             var rawmaterailId = jQuery(this).find('input[name="raw_material_id[]"]');
//             if (jQuery(rawmaterailId).is(':checked')) {

//                 rawmaterailId = jQuery(rawmaterailId).val();

//                 itemId = jQuery(this).find('input[name="item_id[]"]').val();
//                 soDetailsId = jQuery(this).find('input[name="so_details_id[]"]').val();
//                 planQty = jQuery(this).find('input[name="plan_qty[]"]').val();
//                 dpdId = jQuery(this).find('input[name="dp_secondary_details_id[]"]').val();
//                 orgplanQty = jQuery(this).find('input[name="org_plan_qty[]"]').val();

//                 totalassPlanqty += parseFloat(planQty);

//                 storeassemblyArr.push({ 'so_detail_id': soDetailsId, 'plan_qty': planQty, 'item_id': itemId, 'raw_material_id': rawmaterailId, 'dp_secondary_details_id': dpdId, 'org_plan_qty': orgplanQty });
//             }
//         });


//         sodSecondaryDetailArray[index] = storeassemblyArr.filter(function (val) {
//             return val.item_id !== undefined && val.plan_qty !== undefined;
//         }).map(function (val) {
//             return {
//                 so_details_id: val.so_detail_id,
//                 plan_qty: val.plan_qty,
//                 item_id: val.item_id,
//                 raw_material_id: val.raw_material_id,
//                 dp_secondary_details_id: val.dp_secondary_details_id,
//                 org_plan_qty: val.org_plan_qty,

//             };
//         });

//         if (sodSecondaryDetailArray.length < 1) {
//             jAlert("Please Add At Least One Mapping Item.");
//             return false;
//         }

//         var pend_assembly_so_qty = jQuery("#addPendingSODAssemblyForm").find('#pend_assembly_so_qty').val();

//         if (parseFloat(pend_assembly_so_qty) < totalassPlanqty) {
//             toastError('Plan Qty. is Greater Than Pend. SO Qty.');
//             return false;
//         }


//         jQuery("#DipatchPlanTable tbody tr").each(function () {
//             let $tr = jQuery(this);

//             let rowSodId = $tr.find('input[name="so_detail_id[]"]').val();

//             if (rowSodId == index) {
//                 $tr.closest('tr').find("input[name='plan_qty[]']").val(parseFloat(totalassPlanqty).toFixed(3));
//                 $tr.closest('tr').find("input[name='check_plan_qty[]']").val(parseFloat(totalassPlanqty).toFixed(3));
//             }
//         });

//         jQuery("#SoAssemblyForDispatchModal").modal('hide');
//     }
// });




jQuery('#checkall-sod_data').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#addPendingSODForm").find("[id^='so_details_detail_ids_']:not(.in_use_check)").prop('checked', true).trigger('change');
        jQuery("#addPendingSODForm").find('input[name="plan_qty[]"]').trigger('change').prop('disabled', false);

    } else {
        jQuery("#addPendingSODForm").find("[id^='so_details_detail_ids_']:not(.in_use_check)").prop('checked', false).trigger('change');
        jQuery("#addPendingSODForm").find('input[name="plan_qty[]"]').val('').trigger('change').prop('disabled', true);
    }
});


jQuery('#checkall-sod_second_data').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#addPendingSODSecondaryForm").find("[id^='item_details_ids_']:not(.in_use_check)").prop('checked', true).trigger('change');
        jQuery("#addPendingSODSecondaryForm").find('input[name="plan_qty[]"]').trigger('change').prop('disabled', false);

    } else {
        jQuery("#addPendingSODSecondaryForm").find("[id^='item_details_ids_']:not(.in_use_check)").prop('checked', false).trigger('change');
        jQuery("#addPendingSODSecondaryForm").find('input[name="plan_qty[]"]').val('').trigger('change').prop('disabled', true);
    }
});

jQuery('#checkall-sod_assem_data').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#addPendingSODAssemblyForm").find("[id^='raw_material_ids_']:not(.in_use_check)").prop('checked', true).trigger('change');
        jQuery("#addPendingSODAssemblyForm").find('input[name="plan_qty[]"]').trigger('change').prop('disabled', false);

    } else {
        jQuery("#addPendingSODAssemblyForm").find("[id^='raw_material_ids_']:not(.in_use_check)").prop('checked', false).trigger('change');
        jQuery("#addPendingSODAssemblyForm").find('input[name="plan_qty[]"]').val('').trigger('change').prop('disabled', true);
    }
});




function manageDetailsQtyfield($this) {
    var planQtyField = jQuery($this).parent('td').parent('tr').find('input[name="plan_qty[]"]');

    if (jQuery(planQtyField).prop('disabled') && jQuery(planQtyField).prop('disabled')) {
        jQuery(planQtyField).prop('disabled', false);
    } else {
        jQuery(planQtyField).val('').trigger('change').prop('disabled', true);

    }

}


function dispatchType() {
    chkSOId = [];
    jQuery('.toggleModalBtn').prop('disabled', true);
    jQuery('#show-progress').addClass('loader-progress-whole-page');
    var dispatch_from_id_fix = jQuery('input[name="dispatch_from_id_fix"]:checked').val();

    getSOData(dispatch_from_id_fix);

    // if (dispatch_from_id_fix == 2) {
    //     jQuery('div#btn_hide').show();
    // } else {

    //     jQuery('div#btn_hide').hide();
    // }


}


function TotalWt() {
    var total = 0;
    jQuery('.total_wt').map(function () {
        var total1 = jQuery(this).text();

        if (total1 != "") {

            total = parseFloat(total) + parseFloat(total1);
        }
    });

    jQuery('.total_wt_pc').text(parseFloat(total).toFixed(3));

}


function sumTotalWt(th) {

    let planQty = jQuery(th).parents('tr').find("input[name='plan_qty[]']").val();

    let WtPc = jQuery(th).closest('tr').find("input[name='wt_pc[]']").val();

    var totalWt = 0;
    if (WtPc != "" && planQty != "") {
        totalWt = parseFloat(planQty) * parseFloat(WtPc);
    }

    jQuery(th).closest('tr').find(".total_wt").text(parseFloat(totalWt).toFixed(3));

    TotalWt();
}


function checkCustomerData() {

    var isChecked = jQuery("#commonDispatchPlanForm").find("#multiple_loading_entry").prop("checked");

    jQuery("#DipatchPlanTable tbody tr").each(function () {
        var row = jQuery(this);

        var isCustomerRow = row.find('input[name="so_from_value_fix[]"]').val() === "customer";
        var isFittingItem = row.find('input[name="fitting_item[]"]').val() === "yes";
        var isSecondaryUnit = row.find('input[name="secondary_unit[]"]').val() === "Yes";
        var allow_multi_vehicle = row.find('input[name="allow_multi_vehicle[]"]').val() === "Yes";
        var allow_partial_dispatch = row.find('input[name="allow_partial_dispatch[]"]').val() === "Yes";

        if (isCustomerRow) {
            if (isChecked) {

                row.find('.form_custome_type').show();
                if (isFittingItem || isSecondaryUnit) {
                    row.find('input[name="plan_qty[]"]').prop({ tabindex: -1 }).attr('readonly', true);
                    // row.find('input[name="plan_qty[]"]').attr("readonly", true);
                } else {
                    row.find('input[name="plan_qty[]"]').removeAttr("readonly").removeAttr("tabindex");
                    // row.find('input[name="plan_qty[]"]').removeAttr("readonly");
                }

            } else {
                // row.find('a[onclick="removeSODetails(this)"]').show();

                if (allow_multi_vehicle) {
                    if (isFittingItem || isSecondaryUnit) {
                        row.find('.form_custome_type').hide();
                        row.find('input[name="plan_qty[]"]').prop({ tabindex: -1 }).attr('readonly', true);
                        // row.find('input[name="plan_qty[]"]').attr("readonly", true);
                    } else {
                        row.find('.form_custome_type').show();
                        row.find('input[name="plan_qty[]"]').removeAttr("readonly").removeAttr("tabindex");
                        // row.find('input[name="plan_qty[]"]').removeAttr("readonly");
                    }


                } else {
                    if (allow_partial_dispatch) {
                        row.find('.form_custome_type').show();
                        if (isFittingItem || isSecondaryUnit) {
                            row.find('input[name="plan_qty[]"]').prop({ tabindex: -1 }).attr('readonly', true);
                            // row.find('input[name="plan_qty[]"]').attr("readonly", true);
                        } else {
                            row.find('input[name="plan_qty[]"]').removeAttr("readonly").removeAttr("tabindex");
                            // row.find('input[name="plan_qty[]"]').removeAttr("readonly");
                        }
                    } else {
                        row.find('.form_custome_type').hide();
                        row.find('input[name="plan_qty[]"]').prop({ tabindex: -1 }).attr('readonly', true);
                        // row.find('input[name="plan_qty[]"]').attr("readonly", true);
                    }

                }
            }
        }
    });

}
function initColumnSearchDisp(tableSelector, excludeColumns = []) {
    let $table = jQuery(tableSelector);
    if (!$table.length || !$table.DataTable().settings().length) return;

    let tableApi = $table.DataTable();

    // Target scrollable header thead
    let $header = $table.closest('.dataTables_scroll').find('.dataTables_scrollHeadInner table thead');

    // Remove any existing search-row to avoid duplicates
    $header.find('tr.search-row').remove();

    // Create search row
    let $searchRow = jQuery('<tr class="search-row"></tr>').appendTo($header);

    // Determine searchable columns automatically
    let totalColumns = $header.find('tr').first().find('th').length;

    let searchableColumns = [];
    for (let i = 0; i < totalColumns; i++) {
        if (!excludeColumns.includes(i)) {
            searchableColumns.push(i);
        }
    }

    // Add input fields or empty ths
    $header.find('tr').first().find('th').each(function (i) {
        if (searchableColumns.includes(i)) {
            $searchRow.append(`<th><input type="text" style="width: 100%;" placeholder="${jQuery(this).text().trim()}" /></th>`);
        } else {
            $searchRow.append('<th></th>');
        }
    });

    // Add column search functionality with normalization
    tableApi.columns().every(function () {
        let column = this;
        let idx = this.index();

        if (searchableColumns.includes(idx)) {
            jQuery('input', $searchRow.find('th').eq(idx)).on('keyup change clear', function () {
                let val = this.value
                    .replace(/\s+/g, ' ')
                    .replace(/[\u200B-\u200D\uFEFF]/g, '')
                    .trim()
                    .toLowerCase();

                if (column.search() !== val) {
                    column.search(val).draw();
                }
            });
        }
    });

    // Swap search-row before main-header for scrollable table
    let $scrollHead = jQuery(tableApi.table().container()).find('.dataTables_scrollHead thead');
    let $main = $scrollHead.find('tr.main-header');
    let $search = $scrollHead.find('tr.search-row');
    $search.insertBefore($main);

    // Also sync with original header
    let $origHead = jQuery(tableApi.table().header());
    let $mainOrig = $origHead.find('tr.main-header');
    let $searchOrig = $origHead.find('tr.search-row');
    $searchOrig.insertBefore($mainOrig);
}

