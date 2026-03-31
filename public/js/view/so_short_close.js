
setTimeout(() => {
    // jQuery('#so_short_date').focus();
}, 100);


const date = new Date();

let currentDay = String(date.getDate()).padStart(2, '0');

let currentMonth = String(date.getMonth() + 1).padStart(2, "0");

let currentYear = date.getFullYear();

// we will display the date as DD-MM-YYYY 

let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

let so_data = [];

jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };


    jQuery('#so_short_date').val(currentDate);



    jQuery.ajax({
        url: RouteBasePath + "/get-so_short_close_data",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                if (data.so_data.length > 0 && !jQuery.isEmptyObject(data.so_data)) {
                    for (let ind in data.so_data) {
                        so_data.push(data.so_data[ind]);
                    }
                    fillSOTable();
                }
            }
        }
    });

    // }

    // Store or Update

    var validator = jQuery("#commonSOShortClose").validate({
        onkeyup: false,
        onfocusout: false,
        rules: {
            so_short_date: {
                required: true,
                date_check: true,
                dateFormat: true,
            },
            "so_detail_id[]": {
                required: true
            },
            "so_qty[]": {
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
            },
        },

        messages: {

            so_short_date: {
                required: 'Please Enter Short Close Date',
            },
            "so_detail_id[]": {
                required: "Please Select At Least One SO No."
            },
            "so_qty[]": {
                required: "Please Enter Short Close Qty."
            },

        },

        submitHandler: function (form) {

            so_data = [];
            var index = 0;

            // main table loop 

            jQuery('#pendingSOTable tbody tr').each(function (e) {
                var so_detail_id = jQuery(this).find('input[name="so_detail_id[]"]');
                if (jQuery(so_detail_id).is(':checked')) {
                    so_detail_id = jQuery(so_detail_id).val();
                    soQty = jQuery(this).find('input[name="so_qty[]"]').val();
                    soReason = jQuery(this).find('textarea[name="so_reason[]"]').val();

                    so_data[index] = { 'so_detail_id': so_detail_id, 'short_close_qty': soQty, 'so_reason': soReason };
                    index++;
                }

            });

            if (!jQuery.isEmptyObject(so_data)) {
                let data = new FormData(document.getElementById('commonSOShortClose'));
                let formValue = Object.fromEntries(data.entries());
                delete formValue["so_detail_id[]"];
                delete formValue["so_qty[]"];
                delete formValue["so_reason[]"];
                formValue = Object.assign(formValue, { 'so_short_details': JSON.stringify(so_data) });
                var formdata = new URLSearchParams(formValue).toString();

                if (!jQuery.isEmptyObject(so_data)) {
                    jQuery.ajax({
                        url: RouteBasePath + "/store-so_short_close",
                        type: 'POST',
                        data: formdata,
                        headers: headerOpt,
                        dataType: 'json',
                        processData: false,
                        success: function (data) {
                            if (data.response_code == 1) {
                                toastSuccess(data.response_message, redirectFn); function redirectFn() {
                                    window.location.reload();
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
        }
    });

});





//   UPDATE or ADD Form Data

function fillSOTable() {


    if (so_data.length > 0) {
        var tblHtml = ``;
        let sr_no = 0;

        for (let idx in so_data) {
            ++sr_no;
            tblHtml += `<tr>
            <td><input type="checkbox" ${idx == '0' ? 'autofocus' : ''} name="so_detail_id[]" id="so_detail_ids_${so_data[idx].so_details_id}" value="${so_data[idx].so_details_id}" onchange="manageQtyfield(this)"/>            
            </td>     
            <td>${checkInputNull(so_data[idx].so_number)}</td>
            <td>${checkInputNull(so_data[idx].so_date)}</td>            
            <td>${checkInputNull(so_data[idx].customer_name)}</td>                        
            <td>${checkInputNull(so_data[idx].customer_reg_no)}</td>
            <td>${checkInputNull(so_data[idx].item_name)}</td>
            <td>${checkInputNull(so_data[idx].item_code)}</td>
            <td>${checkInputNull(so_data[idx].item_group_name)}</td>
            <td>${so_data[idx].so_qty != null ? parseFloat(so_data[idx].so_qty).toFixed(3) : ""}</td>
            <td>${so_data[idx].pend_so_map_qty != null ? parseFloat(so_data[idx].pend_so_map_qty).toFixed(3) : ""}</td>            
            <td>${checkInputNull(so_data[idx].unit_name)}</td>            
            <td><input type="text" max="${parseFloat(so_data[idx].pend_so_map_qty).toFixed(3)}" name="so_qty[]" id="so_qty_${so_data[idx].id}" onblur="formatPoints(this,3)" class="input-mini isNumberKey" disabled/></td>
            <td><textarea  name="so_reason[]" id="so_reason_${so_data[idx].id}" rows="4" disabled/></td>
            </tr>`;

        }


        jQuery('#pendingSOTable tbody').empty().append(tblHtml);




        var calcDataTableHeight = function () {
            return jQuery(window).height() * 55 / 100;
        };

        table = jQuery('#pendingSOTable').DataTable({
            pageLength: 50,
            paging: true,
            searching: true,
            "oLanguage": {
                "sSearch": "Search :"
            },
            "sScrollY": calcDataTableHeight(),
        });


        // jQuery(window).resize(function () {
        //     var oSettings = table.fnSettings();
        //     oSettings.oScroll.sY = calcDataTableHeight();
        //     table.fnDraw();
        // });

    } else {
        tblHtml += `<tr class="centeralign" id="noPendingDc">

            <td colspan="15">No Pending Customer Replacement SO Details Available!</td>

        </tr>`;

    }



}





jQuery('#checkall-so').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#pendingSOTable").find("[id^='so_detail_ids_']:not(.in-use)").prop('checked', true).trigger('change');
        jQuery("#pendingSOTable").find("[id^='so_detail_ids_']").prop('checked', true).trigger('change');
        jQuery("#pendingSOTable").find("[id^='so_qty_']").prop('disabled', false);
        jQuery("#pendingSOTable").find("[id^='so_reason_']").prop('disabled', false);
    } else {
        jQuery("#pendingSOTable").find("[id^='so_detail_ids_']:not(.in-use)").prop('checked', false).trigger('change');
        jQuery("#pendingSOTable").find("[id^='so_detail_ids_']").prop('checked', false).trigger('change');
        jQuery("#pendingSOTable").find("[id^='so_qty_']").prop('disabled', true);
        jQuery("#pendingSOTable").find("[id^='so_reason_']").prop('disabled', true);
    }

});



function manageQtyfield($this) {
    var oaQtyField = jQuery($this).parent('td').parent('tr').find('input[name="so_qty[]"]');
    var oaReasonField = jQuery($this).parent('td').parent('tr').find('textarea[name="so_reason[]"]');



    if (jQuery(oaQtyField).prop('disabled') && jQuery(oaReasonField).prop('disabled')) {
        jQuery(oaQtyField).prop('disabled', false);
        jQuery(oaReasonField).prop('disabled', false);

    } else {
        jQuery(oaQtyField).val('').trigger('change').prop('disabled', true);
        jQuery(oaReasonField).val('').trigger('change').prop('disabled', true);

    }

}