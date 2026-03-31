const date = new Date();



let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;
let POApproval = [];
var table;

jQuery('#approval_date').val(currentDate);
let page = jQuery("#pagename").val();

jQuery(document).ready(function () {
    jQuery.ajax({
        url: RouteBasePath + "/listing-po_approval_request",
        type: 'POST',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                if (data.getMaterial.length > 0 && !jQuery.isEmptyObject(data.getMaterial)) {
                    for (let ind in data.getMaterial) {
                        POApproval.push(data.getMaterial[ind]);
                    }
                    fillMaterialDemo(data.getMaterial);
                }
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

    approvalDataTable = jQuery('#approvalDataTable').DataTable({
        responsive: true,
        // "scrollX": true,
        pageLength: 10,
        paging: true,

    });

});

function fillMaterialDemo(getMaterial) {

    if (getMaterial.length > 0) {

        var tblHtml = ``;
        let sr_no = 0;

        for (let idx in getMaterial) {

            ++sr_no;
            tblHtml += `<tr>

            <td><input type="radio" name="po_id[]" id="po_id_${getMaterial[idx].po_id}" value="${getMaterial[idx].po_id}" onChange="getPOData(this)"/>       
            </td>`;

            tblHtml +=
                `<td><input type="hidden" name="po_details_id[]" id="po_details_id_${getMaterial[idx].po_details_id}" value="${getMaterial[idx].po_details_id}"/>${getMaterial[idx].location_name}</td>
            <td>${getMaterial[idx].po_number}</td>
            <td>${getMaterial[idx].po_date}</td>   
            <td>${getMaterial[idx].supplier_name}</td>   
            <td>${getMaterial[idx].person_name != null ? getMaterial[idx].person_name : ''}</td>   
            <td>${getMaterial[idx].to_location != null ? getMaterial[idx].to_location : ''}</td>       
            </tr>`;

        }
    } else {

        tblHtml += `<tbody><tr class="centeralign" id="approvalTable">

            <td colspan="11">No PO Data Available</td>

        </tr></tbody>`;

    }
    jQuery('#approvalTable tbody').empty().append(tblHtml);

    table = jQuery('#approvalTable').DataTable({
        responsive: true,
        // "scrollX": true,
        pageLength: 10,
        paging: true,
    });


}

// Store or Update
var validator = jQuery("#commonPOApprovalRequestForm").validate({
    onclick: false,
    rules: {
        onkeyup: false,
        onfocusout: false,
        'po_id[]': {
            required: true
        },
    },
    messages: {
        'po_id[]': {
            required: "Please Select PO No."
        },
    },
    errorPlacement: function (error, element) {
        jAlert(error.text());
        return false;
    },

    submitHandler: function (form) {

        approve_data = [];
        var index = 0;

        jQuery('#approvalTable tbody tr').each(function (e) {
            var poId = jQuery(this).find('input[name="po_id[]"]');
            if (jQuery(poId).is(':checked')) {

                poId = jQuery(poId).val();

                approve_data[index] = { 'poId': poId, };
                index++;
            }
        });

        approve_data_table = [];
        var index_table = 0;

        jQuery('#approvalDataTable tbody tr').each(function (e) {
            var po_details_id = jQuery(this).find('input[name="po_details_id[]"]').val();
            var po_qty = jQuery(this).find('input[name="po_qty[]"]').val();
            var amount = jQuery(this).find('input[name="amount[]"]').val();

            approve_data_table[index_table] = { 'po_details_id': po_details_id, 'po_qty': po_qty, 'amount': amount };
            index_table++;

        });

        if (!jQuery.isEmptyObject(approve_data)) {


            let data = new FormData(document.getElementById('commonPOApprovalRequestForm'));
            let formValue = Object.fromEntries(data.entries());

            delete formValue["po_id[]"];
            delete formValue["po_details_id[]"];

            formValue = Object.assign(formValue, { 'approve_data': JSON.stringify(approve_data), 'approve_data_table': JSON.stringify(approve_data_table) });
            var formdata = new URLSearchParams(formValue).toString();


            let formUrl = RouteBasePath + "/store-po_approval";
            jQuery.ajax({
                url: formUrl,
                type: 'POST',
                data: formdata,
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        jAlert(data.response_message, 'Alert Dialog', function (r) {
                            window.location.reload();
                        });
                        jQuery('#approvalButton').prop('disabled', false);

                    } else {
                        jQuery('#approvalButton').prop('disabled', false);
                        toastError(data.response_message);
                    }
                },

                error: function (jqXHR, textStatus, errorThrown) {
                    var errMessage = JSON.parse(jqXHR.responseText);
                    if (errMessage.errors) {
                        jQuery('#approvalButton').prop('disabled', false);
                        validator.showErrors(errMessage.errors);
                    } else if (jqXHR.status == 401) {
                        jQuery('#approvalButton').prop('disabled', false);
                        jAlert(jqXHR.statusText);
                    } else {
                        jQuery('#approvalButton').prop('disabled', false);
                        jAlert('Something went wrong!');
                        console.log(JSON.parse(jqXHR.responseText));
                    }
                }

            });
        } else {
            toastError("Please Select PO No.");
        }
    }
});

jQuery('#checkall-sm').click(function () {

    if (jQuery(this).is(':checked')) {
        jQuery("#approvalTable").find("[id^='po_id_']:not(.in-use)").prop('checked', true).trigger('change');
        jQuery("#approvalTable").find("[id^='po_id_']").prop('checked', true).trigger('change');
    } else {
        jQuery("#approvalTable").find("[id^='po_id_']:not(.in-use)").prop('checked', false).trigger('change');
        jQuery("#approvalTable").find("[id^='po_id_']").prop('checked', false).trigger('change');
    }
});




function getPOData(id) {
    let checkChekbox = jQuery(id).is(':checked');
    if (checkChekbox) {
        // console.log(id);
        var po_id = id.value;

        if (po_id != '') {
            jQuery.ajax({
                url: RouteBasePath + "/get-po_details?po_id=" + po_id,
                type: 'POST',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        var tblHtml = ``;

                        if (data.poApprorvl.length > 0 && !jQuery.isEmptyObject(data.poApprorvl)) {
                            for (let idx in data.poApprorvl) {
                                tblHtml += `<tr>                             
                                    <input type="hidden" name="po_id[]" id="po_id${data.poApprorvl[idx].po_id}" value="${data.poApprorvl[idx].po_id}"/>    
                                    <input type="hidden" name="po_details_id[]" id="po_details_id_${data.poApprorvl[idx].po_details_id}" value="${data.poApprorvl[idx].po_details_id}"/>
                                    <td>${parseFloat(idx) + 1}</td>
                                    <td>${data.poApprorvl[idx].item_name != null ? data.poApprorvl[idx].item_name : ''}</td>
                                    <td>${data.poApprorvl[idx].item_code != null ? data.poApprorvl[idx].item_code : ''}</td>
                                    <td>${data.poApprorvl[idx].item_group_name != null ? data.poApprorvl[idx].item_group_name : ''}</td>
                                    <td><input type="text" name="po_qty[]" onblur="formatPoints(this,3)" id="po_qty" onKeyup="soRateUnit(this)"  class="form-control isNumberKey po_qty" style="width:100px;" value="${data.poApprorvl[idx].po_qty != null ? parseFloat(data.poApprorvl[idx].po_qty).toFixed(3) : ''}"/></td>                               
                                    <td><input type="hidden" name="rate_per_unit[]" onblur="formatPoints(this,3)" id="rate_per_unit" value="${data.poApprorvl[idx].rate_per_unit != null ? parseFloat(data.poApprorvl[idx].rate_per_unit).toFixed(3) : ''}"/>${data.poApprorvl[idx].rate_per_unit != null ? parseFloat(data.poApprorvl[idx].rate_per_unit).toFixed(3) : ''}</td>                               
                                    <td><input type="hidden" name="discount[]"  id="discount" value="${data.poApprorvl[idx].discount != null ? parseFloat(data.poApprorvl[idx].discount).toFixed(2) : ''}"/>
                                    <input type="hidden" name="amount[]"  id="amount" value="${data.poApprorvl[idx].amount != null ? parseFloat(data.poApprorvl[idx].amount).toFixed(3) : ''}"/>${data.poApprorvl[idx].discount != null ? parseFloat(data.poApprorvl[idx].discount).toFixed(2) : ''}</td>                             
                                    <td>${data.poApprorvl[idx].del_date != null ? data.poApprorvl[idx].del_date : ''}</td>                             
                                    <td>${data.poApprorvl[idx].unit_name != null ? data.poApprorvl[idx].unit_name : ''}</td>                             
                                    <td id="td_amount">${data.poApprorvl[idx].amount != null ? parseFloat(data.poApprorvl[idx].amount).toFixed(3) : ''}</td>     
                                    <td>${data.poApprorvl[idx].remarks != null ? data.poApprorvl[idx].remarks : ''}</td>                              
                                    </tr>`;
                            }

                            if (jQuery.fn.DataTable.isDataTable('#approvalDataTable')) {
                                jQuery('#approvalDataTable').DataTable().destroy();
                            }
                            jQuery('#approvalDataTable tbody').empty().append(tblHtml);

                            approvalDataTable = jQuery('#approvalDataTable').DataTable({
                                responsive: true,
                                // "scrollX": true,
                                pageLength: 10,
                                paging: true,

                            });


                        }
                    }
                },
            });

        }
    } else {
        checkPOVal()
    }
}

function checkPOVal() {
    var poId = [];
    jQuery('#approvalTable tbody').each(function () {
        poId = jQuery(this).find('input[name="po_id[]"]:checked');
        if (poId.length > 0) {
            poId.each(function () {
                getPOData(this);
            });
        } else {
            jQuery("#approvalDataTable tbody").empty();
        }
    });

}




function soRateUnit(th) {
    let po_qty = jQuery(th).parents('tr').find("#po_qty").val();

    let rateUnit = jQuery(th).parents('tr').find("#rate_per_unit").val();

    let discount = jQuery(th).parents('tr').find("#discount").val();

    var poUnit = 0;
    if (discount < 100) {
        var discountAmt = 0;

        if (rateUnit != "" && po_qty != "") {
            poUnit = parseFloat(po_qty) * parseFloat(rateUnit);
            discountAmt = parseFloat(poUnit) * parseFloat(discount) / 100;

            if (discount != null && discount != "") {
                poUnit = parseFloat(poUnit) - parseFloat(discountAmt);
            } else {
                poUnit = parseFloat(po_qty) * parseFloat(rateUnit);
            }
        }
    } else {
        toastError('Please Enter Discount Value Less Than 100');
    }


    if (poUnit != 0) {
        jQuery(th).parents('tr').find("#amount").val(parseFloat(poUnit).toFixed(3));
        jQuery(th).parents('tr').find("#td_amount").text(parseFloat(poUnit).toFixed(3));
    } else if (rateUnit == "") {
        jQuery(th).parents('tr').find("#amount").val('');

    } else {
        jQuery(th).parents('tr').find("#amount").val(0);
    }

}