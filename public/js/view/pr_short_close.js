// setTimeout(() => {
//     jQuery('#pr_short_date').focus();
// }, 100);
let prShortCloseIdId = jQuery('#commonPRShortClose').find('input:hidden[name="id"]').val();


const date = new Date();

let currentDay = String(date.getDate()).padStart(2, '0');

let currentMonth = String(date.getMonth() + 1).padStart(2, "0");

let currentYear = date.getFullYear();

let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

let pr_data = [];



jQuery(document).ready(function () {
    
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

    jQuery('#pr_short_date').val(currentDate);

    jQuery.ajax({
        url: RouteBasePath + "/get-pr",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                if (data.pr_data.length > 0 && !jQuery.isEmptyObject(data.pr_data)) {
                    for (let ind in data.pr_data) {
                        pr_data.push(data.pr_data[ind]);
                    }
                    fillPRTable();
                }
            }
        }
    });

    // }

    jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
        return this.optional(element) || parseFloat(value) > 0;
    });

    // Store or Update
    var validator = jQuery("#commonPRShortClose").validate({
        rules: {
            onkeyup: false,
            onfocusout: false,
            pr_short_date: {
                required: true,
                date_check: true,
                dateFormat: true,
            },
            "pr_detail_id[]": {
                required: true
            },
            "so_pr_qty[]": {
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

            // "pr_reason[]": {
            //     required: function (e) {
            //         if (jQuery(e).prop('disabled')) {
            //             return false;
            //         } else {
            //             return true;
            //         }
            //     },
            // },
        },

        messages: {

            pr_short_date: {
                required: 'Please Enter PR Short Close Date',
            },
            "pr_detail_id[]": {
                required: "Please Select At Least One PR No."

            },
            "so_pr_qty[]": {
                required: "Please Enter Short Close Qty.",
                notOnlyZero: 'Please Enter A Value Greater Than 0.',

            },
            // "pr_reason[]": {
            //     required: "Please Enter Reason"
            // },
        },

        submitHandler: function (form) {

            pr_data = [];

            var index = 0;

            jQuery('#pendingPRTable tbody tr').each(function (e) {

                var PRId = jQuery(this).find('input[name="pr_detail_id[]"]');

                if (jQuery(PRId).is(':checked')) {

                    PRId = jQuery(PRId).val();

                    id = jQuery(this).find('input[name="id[]"]').val();

                    prQty = jQuery(this).find('input[name="so_pr_qty[]"]').val();

                    prReason = jQuery(this).find('textarea[name="pr_reason[]"]').val();

                    // assign to object 

                    pr_data[index] = { 'pr_detail_id': PRId, 'so_pr_qty': prQty, 'pr_reason': prReason };
                    index++;
                }
            });

            if (!jQuery.isEmptyObject(pr_data)) {

                let data = new FormData(document.getElementById('commonPRShortClose'));

                let formValue = Object.fromEntries(data.entries());

                if (prShortCloseIdId !== undefined) { //Edit Form
                    formValue.id = prShortCloseId;

                }

                delete formValue["id[]"];

                delete formValue["pr_detail_id[]"];

                delete formValue["pr_qty[]"];

                delete formValue["pr_reason[]"];

                delete formValue["company_id[]"];


                formValue = Object.assign(formValue, { 'pr_short_details': JSON.stringify(pr_data) });

                var formdata = new URLSearchParams(formValue).toString();

                if (!jQuery.isEmptyObject(pr_data)) {

                    let formUrl = RouteBasePath + "/store-purchase_requisition_short_close";

                    jQuery.ajax({

                        url: formUrl,

                        type: 'POST',

                        data: formdata,

                        headers: headerOpt,

                        dataType: 'json',

                        processData: false,

                        success: function (data) {

                            if (data.response_code == 1) {


                                if (prShortCloseIdId != undefined && prShortCloseIdId != "") {

                                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                                        window.location.href = RouteBasePath + "/manage-purchase_requisition_short_close";
                                    });
                                }
                                else if (prShortCloseIdId == undefined || prShortCloseIdId == "") {
                                    toastSuccess(data.response_message, redirectFn);
                                    function redirectFn() {
                                        window.location.reload();
                                    }
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
                            } else {
                                jAlert('Something went wrong!');
                                console.log(JSON.parse(jqXHR.responseText));
                            }
                        }
                    });
                }

            }
        }
    });

});


//   UPDATE or ADD Form Data

function fillPRTable() {

    if (pr_data.length > 0) {
        var tblHtml = ``;
        let sr_no = 0;

        for (let idx in pr_data) {

            ++sr_no;
            tblHtml += `<tr>

            <td><input type="checkbox" name="pr_detail_id[]" id="pr_detail_ids_${pr_data[idx].PRId}" value="${pr_data[idx].PRId}" onchange="manageQtyfield(this)"${idx == 0 ? "autofocus" :""}/>            
            </td>     
            <td>${pr_data[idx].pr_number}</td>
            <td>${pr_data[idx].pr_date}</td>            
            <td>${pr_data[idx].pr_form_value_fix}</td>            
            <td>${pr_data[idx].supplier_name}</td>  
            <td>${pr_data[idx].location_name != null ? pr_data[idx].location_name : "" }</td>  
            <td>${pr_data[idx].item_name}</td>
            <td>${pr_data[idx].item_code}</td>
            <td>${pr_data[idx].item_group_name}</td>
            <td>${pr_data[idx].req_qty != null ? parseFloat(pr_data[idx].req_qty).toFixed(3) : ""}</td>
            <td>${pr_data[idx].pend_req_qty != null ? parseFloat(pr_data[idx].pend_req_qty).toFixed(3) : ""}</td>            
            <td>${pr_data[idx].unit_name}</td>            
            <td><input type="text" max="${pr_data[idx].pend_req_qty}" name="so_pr_qty[]" id="so_pr_qty_${pr_data[idx].pr_id}" onblur="formatPoints(this,3)" class="input-mini isNumberKey" disabled/></td>
            <td><textarea  name="pr_reason[]" id="pr_reason_${pr_data[idx].pr_id}" rows="4" disabled/></td>
            </tr>`;

        }

        jQuery('#pendingPRTable tbody').empty().append(tblHtml);

        if (prShortCloseIdId !== undefined) { //Edit Form
            if (pr_data.length > 0 && !jQuery.isEmptyObject(pr_data)) {

                for (let ind in pr_data) {

                    var selected = jQuery('#pendingPRTable tbody tr').find('#pr_detail_ids_' + pr_data[ind].supplier_pr_detail_id);

                    jQuery(selected).attr('checked', true).addClass('in-use');

                    jQuery(selected).parent('td').find('input[name="id[]"]').val(pr_data[ind].id);

                    jQuery(selected).parent('td').parent('tr').find('input[name="so_pr_qty[]"]').val(pr_data[ind].qty).prop('disabled', false);

                    jQuery(selected).parent('td').parent('tr').find('textarea[name="pr_reason[]"]').val(checkSpecialCharacter(pr_data[ind].reason)).prop('disabled', false);
                }
            }
        } else {
            // var calcDataTableHeight = function () {
            //     return jQuery(window).height() * 55 / 100;
            // };
            table = jQuery('#pendingPRTable').DataTable({
                pageLength: 50,
                paging: true,
                searching: true,
                "oLanguage": {
                    "sSearch": "Search :"
                },
                // "sScrollY": calcDataTableHeight(),
                "scrollX":true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                initComplete: function () {
                // Exclude first column (index 0) from search
                    initColumnSearch('#pendingPRTable', [0,12,13]);
                }   
            });
        }

    } else {
        tblHtml += `<tr class="centeralign" id="noPendingDc">
            <td colspan="11">No Pending PR Details Available</td>
        </tr>`;
    }
}


jQuery('#checkall-pr').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#pendingPRTable").find("[id^='pr_detail_ids_']:not(.in-use)").prop('checked', true).trigger('change');
        jQuery("#pendingPRTable").find("[id^='pr_detail_ids_']").prop('checked', true).trigger('change');
        jQuery("#pendingPRTable").find("[id^='so_pr_qty_']").prop('disabled', false);
        jQuery("#pendingPRTable").find("[id^='pr_reason_']").prop('disabled', false);
    } else {
        jQuery("#pendingPRTable").find("[id^='pr_detail_ids_']:not(.in-use)").prop('checked', false).trigger('change');
        jQuery("#pendingPRTable").find("[id^='pr_detail_ids_']").prop('checked', false).trigger('change');
        jQuery("#pendingPRTable").find("[id^='so_pr_qty_']").prop('disabled', true);
        jQuery("#pendingPRTable").find("[id^='pr_reason_']").prop('disabled', true);
    }

});



function manageQtyfield($this) {
    var oaQtyField = jQuery($this).parent('td').parent('tr').find('input[name="so_pr_qty[]"]');
    var oaReasonField = jQuery($this).parent('td').parent('tr').find('textarea[name="pr_reason[]"]');

    if (jQuery(oaQtyField).prop('disabled') && jQuery(oaReasonField).prop('disabled')) {
        jQuery(oaQtyField).prop('disabled', false);
        jQuery(oaReasonField).prop('disabled', false);

    } else {
        jQuery(oaQtyField).val('').trigger('change').prop('disabled', true);
        jQuery(oaReasonField).val('').trigger('change').prop('disabled', true);

    }

}