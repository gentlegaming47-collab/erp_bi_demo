let formId = jQuery('#commonQCForm').find('input:hidden[name="id"]').val();

const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

var qc_data = [];


jQuery(document).ready(function () {
    if (formId != null && formId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({
            url: RouteBasePath + "/get-qc_approval/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {

                if (data.response_code == 1) {
                    // setTimeout(() => {
                    //     jQuery('#qc_sequence').focus();
                    // }, 100);

                    jQuery("#qc_sequence").val(data.qc_data.qc_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery("#qc_number").val(data.qc_data.qc_number).prop({ tabindex: -1, readonly: true });
                    jQuery("#qc_date").val(data.qc_data.qc_date);
                    jQuery("#rejection_reason").val(data.qc_data.rejection_reason);
                    jQuery("#org_ok_qty").val(data.qc_data.ok_qty);
                    jQuery('#pre_item_id').val(data.qc_data.item_id);
                    jQuery('#pre_item_details_id').val(data.qc_data.item_details_id);




                    if (data.qc_details.length > 0 && !jQuery.isEmptyObject(data.qc_details)) {
                        for (let ind in data.qc_details) {
                            qc_data.push(data.qc_details[ind]);
                        }

                    }


                    fillQCTable(data.qc_details);

                    if (data.qc_data.in_use == true) {
                        jQuery('#qc_sequence').attr('readonly', true);
                        jQuery('#qc_date').attr('readonly', true);
                        jQuery('#qc_qty').attr('readonly', true);
                        jQuery('#ok_qty').attr('readonly', true);
                    }

                    setTimeout(() => {
                        // jQuery('#qc_date').focus();
                        jQuery(".toggleModalBtn").last().focus();
                    }, 800);

                    jQuery('#show-progress').removeClass('loader-progress-whole-page');
                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-qc_approval";
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
        // setTimeout(() => {
        //     jQuery('#qc_sequence').focus();
        // }, 100);
        getLatestQCNo()

        jQuery('#qc_qty').prop({ tabindex: -1, readonly: true });
        jQuery('#ok_qty').prop({ tabindex: -1, readonly: true });

        // jQuery('#qc_date').focus();
        // jQuery(".toggleModalBtn").last().focus();
        setTimeout(() => {
            // jQuery('#qc_sequence').focus();
            jQuery(".toggleModalBtn").last().focus();
        }, 1000);
    }
    getPendingGrnData()
})

// get the latest number
function getLatestQCNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_qc_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#qc_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#qc_date').val(currentDate);
                jQuery('#qc_number').val(data.latest_qc_no).prop({ tabindex: -1, readonly: true });
                jQuery('#qc_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#qc_number').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}



function getPendingGrnData() {
    var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/get-grn_data-qc?id=" + formId : RouteBasePath + "/get-grn_data-qc";
    jQuery.ajax({
        url: formUrl,
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                var tblHtml = ``;


                var usedParts = [];
                var totalDisb = 0;
                var found = 0;

                jQuery('#qc_approval_table tbody input[name="form_indx"]').each(function (indx) {
                    let frmIndx = jQuery(this).val();

                    let jbEorkOrderId = qc_data[frmIndx].grn_details_id;
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

                if (data.grn_data.length > 0 && !jQuery.isEmptyObject(data.grn_data)) {
                    var in_use = false;
                    for (let idx in data.grn_data) {
                        if (data.grn_data[idx].in_use == true) {
                            in_use = true;
                        }
                    }
                    for (let idx in data.grn_data) {
                        var inUse = isUsed(data.grn_data[idx].grn_details_id);

                        var show_pend_qty = parseFloat(data.grn_data[idx].show_pend_qty) + parseFloat(data.grn_data[idx].qc_qty);
                        tblHtml += `<tr>
                                    <td><input type="radio" name="grn_details_id[]" class="simple-check" id="grn_details_id_${data.grn_data[idx].grn_details_id}" value="${data.grn_data[idx].grn_details_id}" ${inUse ? 'checked' : ''} ${in_use == true ? 'readonly' : ''}/></td>
                                    <td>${data.grn_data[idx].grn_number}</td>
                                    <td>${data.grn_data[idx].grn_date}</td>                                                                    
                                    <td>${checkInputNull(data.grn_data[idx].supplier_name)}</td>                                                                    
                                    <td>${checkInputNull(data.grn_data[idx].po_number)}</td>                                                                    
                                    <td>${checkInputNull(data.grn_data[idx].po_date)}</td>                                                                    
                                    <td>${data.grn_data[idx].item_name}</td>                                                                                                                           
                                    <td>${data.grn_data[idx].item_code}</td>                                                                    
                                    <td>${data.grn_data[idx].item_group_name}</td>                                                                    
                                    <td>${data.grn_data[idx].unit_name}</td>                                                                    
                                    <td>${parseFloat(data.grn_data[idx].grn_qty).toFixed(3)}</td>                                                                    
                                    <td>${parseFloat(show_pend_qty).toFixed(3)}</td>                                                                    
                                </tr>`;

                    }

                } else {
                    tblHtml += `<tr class="centeralign" id="noPendingPo">
                        <td colspan="5">No Pending PR Available</td>
                    </tr>`;

                }

                jQuery('#pendingGRNDataTable tbody').empty().append(tblHtml);
                jQuery('.toggleModalBtn').prop('disabled', false);

            }
        },
    });
}



var coaPartValidator = jQuery("#addPendingGRNForm").validate({
    rules: {
        "grn_details_id[]": {
            required: true
        },
    },
    messages: {
        "grn_details_id[]": {
            required: "Please Select Item From Pending GRN",
        }
    },

    submitHandler: function (form) {
        var chkCount = 0;
        var chkArr = [];
        var chkId = [];

        jQuery("#addPendingGRNForm").find("[id^='grn_details_id_']").each(function () {
            var thisId = jQuery(this).attr('id');
            var splt = thisId.split('grn_details_id_');
            var intId = splt[1];

            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });

        if (chkCount == 0) {
            toastError('Please Select Item From Pending GRN');
        } else {
            if (formId == undefined) {
                var url = RouteBasePath + "/get-grn_part_data-qc?grn_details_ids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-grn_part_data-qc?grn_details_ids=" + chkArr.join(',') + "&id=" + formId;
            }

            jQuery.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.grn_data.length > 0 && !jQuery.isEmptyObject(data.grn_data)) {
                            qc_data = [];
                            for (let ind in data.grn_data) {
                                qc_data.push(data.grn_data[ind]);
                            }

                            fillQCTable(data.grn_data);
                        } else {
                            jQuery('#qc_approval_table tbody').empty();
                        }
                        jQuery("#pendingGrnModal").modal('hide');

                        setTimeout(() => {
                            jQuery('#qc_qty').focus();
                        }, 1200)


                    } else {
                        toastError(data.response_message);
                    }

                },
            });
        }
    }
});



function fillQCTable(grn_data) {
    if (grn_data.length > 0) {
        var thisHtml = '';
        var counter = 1;

        for (let key in grn_data) {

            var formIndx = key;
            var sr_no = counter;

            // var purchase_order_details_id = purchase_order_details[key].po_details_id ? purchase_order_details[key].po_details_id : 0;

            // var item_id = purchase_order_details[key].item_id ? purchase_order_details[key].item_id : "";
            var grn_details_id = grn_data[key].grn_details_id ? grn_data[key].grn_details_id : "";
            var grn_number = grn_data[key].grn_number ? grn_data[key].grn_number : "";
            var grn_date = grn_data[key].grn_date ? grn_data[key].grn_date : "";
            var supplier_name = grn_data[key].supplier_name ? grn_data[key].supplier_name : "";
            var po_number = grn_data[key].po_number ? grn_data[key].po_number : "";
            var po_date = grn_data[key].po_date ? grn_data[key].po_date : "";
            var item_id = grn_data[key].item_id ? grn_data[key].item_id : "";
            // var item_details_id = grn_data[key].item_details_id ? grn_data[key].item_details_id : "";
            // var secondary_item_name = grn_data[key].secondary_item_name ? grn_data[key].secondary_item_name : "";
            var item_name = grn_data[key].item_name ? grn_data[key].item_name : "";
            var item_code = grn_data[key].item_code ? grn_data[key].item_code : "";
            var unit_name = grn_data[key].unit_name ? grn_data[key].unit_name : "";
            var group = grn_data[key].item_group_name ? grn_data[key].item_group_name : "";
            var grn_qty = grn_data[key].grn_qty ? grn_data[key].grn_qty.toFixed(3) : 0;
            var pend_grn_qty = grn_data[key].pend_grn_qty ? grn_data[key].pend_grn_qty.toFixed(3) : 0;
            var show_pend_qty = grn_data[key].show_pend_qty ? grn_data[key].show_pend_qty.toFixed(3) : 0;
            var qc_qty = grn_data[key].qc_qty ? grn_data[key].qc_qty.toFixed(3) : 0;

            var total_pend_qty = grn_data[key].qc_qty > 0 ? grn_data[key].qc_qty : parseFloat(show_pend_qty) + parseFloat(qc_qty);

            var max_qty = parseFloat(pend_grn_qty) + parseFloat(qc_qty);

            var ok_qty = grn_data[key].ok_qty > 0 ? grn_data[key].ok_qty.toFixed(3) : total_pend_qty.toFixed(3);

            var count = grn_data[key].count ? grn_data[key].count : '';

            var in_use = grn_data[key].in_use ? grn_data[key].in_use : '';
            var used_qty = grn_data[key].used_qty ? grn_data[key].used_qty : 0;

            thisHtml += `            
            <tr>        
            <td><a ${in_use == true ? '' : 'onclick="removeQCDetails(this)'}"><i class="action-icon iconfa-trash"></i></a></td>        
            <td><input type="hidden" name="form_indx" value="${formIndx}"/>${sr_no}</td>        
            <td>${grn_number}</td>        
            <td>${grn_date}</td>        
            <td>${supplier_name}</td>        
            <td>${po_number}</td>        
            <td>${po_date}</td>        
            <td>${item_name}</td>
            <td>${item_code}</td>
            <td>${group}</td>
            <td>${unit_name}</td>
            <td>${grn_qty}</td>
            <td>${(parseFloat(show_pend_qty) + parseFloat(qc_qty)).toFixed(3)}</td>
            <tr>`;

            // <td>${secondary_item_name}</td>

            jQuery('#qc_qty').val(parseFloat(total_pend_qty).toFixed(3)).prop({ readonly: false });
            jQuery('#ok_qty').val(ok_qty).prop({ readonly: false });
            jQuery('#grn_details_id').val(grn_details_id);
            jQuery('#item_id').val(item_id);
            // jQuery('#item_details_id').val(item_details_id);

            jQuery("#qc_qty").attr('max', (parseFloat(max_qty).toFixed(3)));
            jQuery("#qc_qty").attr('data-pend_grn_qty', (parseFloat(pend_grn_qty).toFixed(3)));
            jQuery("#qc_qty").attr('data-pend_grn_qty', (parseFloat(pend_grn_qty).toFixed(3)));
            jQuery("#qc_qty").attr('data-count', (count));
            jQuery("#reject_qty").attr('min', (parseFloat(used_qty).toFixed(3)));

            counter++;

        }

        jQuery('#qc_approval_table tbody').empty().append(thisHtml);

        getRejQty()

    } else {
        jQuery('#qc_approval_table tbody').empty();
    }

}


function getOKQty() {
    var qc_qty = jQuery('#qc_qty').val();
    var pend_grn_qty = jQuery('#qc_qty').attr('data-pend_grn_qty');
    var count = jQuery('#qc_qty').attr('data-count');

    var maxValue = parseFloat(jQuery('#qc_qty').attr('max'));

    if (qc_qty > maxValue) {

        if (qc_qty > pend_grn_qty && pend_grn_qty == 0 && count > 1) {
            toastError('There is no Pending QC Qty. Available');
            jQuery("#ok_qty").val('');
            return;
        }
        toastError('QC Qty. Can not Be More than Pending QC Qty.');
        jQuery("#ok_qty").val('');
        return;
    }

    if (qc_qty != '') {
        jQuery("#ok_qty").attr('max', parseFloat(qc_qty).toFixed(3));
        jQuery('#ok_qty').val(parseFloat(qc_qty).toFixed(3));
    } else {
        jQuery('#ok_qty').val('');
    }

    getRejQty()
}


function getRejQty() {
    var qc_qty = jQuery('#qc_qty').val();
    var ok_qty = jQuery('#ok_qty').val();
    var used_qty = parseFloat(jQuery('#reject_qty').attr('min'));
    var minValue = parseFloat(qc_qty) - used_qty;

    jQuery("#ok_qty").attr('max', parseFloat(minValue).toFixed(3));

    // jQuery("#ok_qty").attr('max', parseFloat(qc_qty).toFixed(3));


    if (parseFloat(ok_qty) > parseFloat(qc_qty)) {
        toastError('OK Qty. Can not Be More than QC Qty.');
    }

    var rej_qty = qc_qty != '' && ok_qty != '' ? parseFloat(qc_qty - ok_qty).toFixed(3) : '';
    jQuery('#reject_qty').val(rej_qty >= 0 ? rej_qty : '');

}


jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) > 0;
    // return this.optional(element) || parseFloat(value) >= parseFloat(param);
});


var validator = jQuery("#commonQCForm").validate({

    onclick: false,
    rules: {

        qc_sequence: {
            required: true
        },

        qc_date: {
            required: true,
            date_check: true,
            dateFormat: true
        },

        qc_qty: {
            required: true,
            notOnlyZero: '0.001',
        },
        ok_qty: {
            required: true,
            notOnlyZero: '0.001',
        },
        rejection_reason: {
            required: function (e) {
                var reject_qty = jQuery("#commonQCForm").find('#reject_qty').val();
                var rejection_reason = jQuery("#commonQCForm").find('#rejection_reason').val();
                if (reject_qty > 0 && rejection_reason == '') {
                    jQuery(e).addClass('error');
                    jQuery(e).focus();
                    return true;
                } else {
                    jQuery(e).removeClass('error');
                    return false;
                }
            },
        },
    },

    messages: {

        qc_sequence: {
            required: "Please Enter QC No."
        },

        qc_date: {
            required: "Please Enter QC Date.",
        },

        qc_qty:
        {
            required: "Please Enter QC Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.000.'
        },
        ok_qty:
        {
            required: "Please Enter OK Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.000.'
        },
        rejection_reason: {
            required: "Please Enter Rejection Reason."
        },

    },
    errorPlacement: function (error, element) {
        jAlert(error.text());
        return false;
    },


    submitHandler: function (form) {


        // check table length 

        let checkLength = jQuery("#qc_approval_table tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        if (checkLength < 2) {
            jAlert("Please Add At Least One QC Approval Detail.");

            return false;
        }

        var minQty = jQuery("#commonQCForm").find('#reject_qty').attr('min') != '' ? parseFloat(jQuery("#commonQCForm").find('#reject_qty').attr('min')).toFixed(3) : 0;
        var rej_qty = jQuery("#commonQCForm").find('#reject_qty').val();

        if (rej_qty < minQty) {
            jAlert('Please enter a value less than or equal to ' + min + '.');
            return false;
        }

        jQuery('#qcButton').prop('disabled', true);

        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-qc_approval" : RouteBasePath + "/store-qc_approval";
        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: jQuery('#commonQCForm').serialize(),
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (formId != null && formId != "") {
                        toastSuccess(data.response_message, nextFn);
                        function nextFn() {
                            window.location.href = RouteBasePath + "/manage-qc_approval";
                        }
                    } else {

                        toastSuccess(data.response_message, nextFn);

                        function nextFn() {
                            window.location.reload();
                        }

                        jQuery('#qcButton').prop('disabled', false);
                    }
                } else {
                    jQuery('#qcButton').prop('disabled', false);
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);
                if (errMessage.errors) {
                    jQuery('#qcButton').prop('disabled', false);
                    validator.showErrors(errMessage.errors);
                } else if (jqXHR.status == 401) {
                    jQuery('#qcButton').prop('disabled', false);
                    toastError(jqXHR.statusText);
                } else {
                    jQuery('#qcButton').prop('disabled', false);
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }

            }
        });
    }
});


jQuery('#pendingGrnModal').on('show.bs.modal', function (e) {
    var usedParts = [];
    var totalDisb = 0;

    jQuery('#qc_approval_table tbody input[name="form_indx"]').each(function (indx) {
        var frmIndx = jQuery(this).val();
        var grnId = qc_data[frmIndx].grn_details_id;
        if (grnId != "" && grnId != null) {
            usedParts.push(Number(grnId));
        }
    });

    function isUsed(pjId) {
        if (usedParts.includes(Number(pjId))) {
            totalDisb++;
            return true;
        }
        return false;
    }

    jQuery('#pendingGRNDataTable tbody tr').each(function (indx) {

        var checkField = jQuery(this).find('input[name="grn_details_id[]"]');
        var partId = jQuery(checkField).val();
        var inUse = isUsed(partId);

        if (inUse) {
            jQuery(checkField).prop('checked', true);

        } else {
            jQuery(checkField).prop('checked', false);
        }

    });



});



function removeQCDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        if (r === true) {
            jQuery(th).closest("tr").remove();
            jQuery('#qc_qty').val('');
            jQuery('#ok_qty').val('');
            jQuery('#reject_qty').val('');
            jQuery('#rejection_reason').val('');

            jQuery('#qc_qty').prop({ tabindex: -1, readonly: true });
            jQuery('#ok_qty').prop({ tabindex: -1, readonly: true });
        }

    });
}



jQuery('#qc_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();
    if (subBtn == "submit" || subBtn == "Submit") {
        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }
    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid QC No.');
            jQuery('#qc_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    // jQuery('#qc_sequence').focus();
                    jQuery(".toggleModalBtn").last().focus();
                }, 1000);
            });
            jQuery('#qc_sequence').val('');
        } else {
            jQuery("#qcButton").attr('disabled', true);
            jQuery('#qc_sequence').parent().parent().parent('div.control-group').removeClass('error');
            var urL = RouteBasePath + "/check-qc_approval?for=add&pr_sequence=" + val;
            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-qc_approval?for=edit&pr_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#qc_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        jQuery('#qc_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                // jQuery('#qc_sequence').focus();
                                jQuery(".toggleModalBtn").last().focus();
                            }, 1000);
                        });

                        jQuery('#qc_sequence').val('');
                    } else {
                        jQuery('#qc_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#qc_number').val(data.latest_po_no);
                        jQuery('#qc_sequence').val(val);
                    }
                    jQuery("#qcButton").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#qc_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#qc_number').val('');
        jQuery('#qc_sequence').val('');
    }
});


function suggestRejectionReason(e, $this) {
    var keyevent = e
    if (keyevent.key != "Tab") {
        jQuery("#rejection_reason").addClass('file-loader');
        var search = jQuery($this).val();

        jQuery.ajax({
            url: RouteBasePath + "/rejection_reason_for_qc-list?term=" + encodeURI(search),
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {

                jQuery("#rejection_reason").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#rejection_reason_list').html(data.orderByList);
                } else {
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery("#rejection_reason").removeClass('file-loader');
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