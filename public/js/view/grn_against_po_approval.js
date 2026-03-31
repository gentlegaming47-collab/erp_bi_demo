const date = new Date();



let currentDay = String(date.getDate()).padStart(2, '0');

let currentMonth = String(date.getMonth() + 1).padStart(2, "0");

let currentYear = date.getFullYear();

let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;


let GrnPOApproval = [];

var table;


jQuery('#approval_date').val(currentDate);
let page = jQuery("#pagename").val();

jQuery(document).ready(function () {
    jQuery.ajax({
        url: RouteBasePath + "/listing-po_approval_grn_request",
        type: 'POST',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                if (data.getMaterial.length > 0 && !jQuery.isEmptyObject(data.getMaterial)) {
                    for (let ind in data.getMaterial) {
                        GrnPOApproval.push(data.getMaterial[ind]);
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
});

function fillMaterialDemo(getMaterial) {

    if (getMaterial.length > 0) {

        var tblHtml = ``;
        let sr_no = 0;

        for (let idx in getMaterial) {

            ++sr_no;
            tblHtml += `<tr>
            <td><input type="checkbox" name="grn_details_id[]" id="grn_details_id_${getMaterial[idx].grn_details_id}" value="${getMaterial[idx].grn_details_id}"/></td>
            <td>${getMaterial[idx].grn_number}</td>
            <td>${getMaterial[idx].grn_date}</td>
            <td>${getMaterial[idx].supplier_name}</td>
            <td>${getMaterial[idx].bill_no}</td>
            <td>${getMaterial[idx].bill_date}</td>
            <td>${getMaterial[idx].po_number}</td>
            <td>${getMaterial[idx].po_date}</td>
            <td>${getMaterial[idx].item_name}</td>
            <td>${getMaterial[idx].item_code}</td>
            <td>${getMaterial[idx].item_group_name}</td>
            <td>${parseFloat(getMaterial[idx].po_qty).toFixed(3)}</td>   
            <td>${parseFloat(getMaterial[idx].grn_qty).toFixed(3)}</td>
            <td>${parseFloat(getMaterial[idx].excess_qty).toFixed(3)}</td>
            <td>${getMaterial[idx].unit_name}</td>

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
        "scrollX": true,
        pageLength: 25,
        paging: true,
        "sScrollX": "100%",
        "sScrollXInner": "110%",
        "scrollX": true,

        initComplete: function () {
            // Exclude first column (index 0) from search
            initColumnSearch('#approvalTable', [0]);
        }
    });



}

// Store or Update
var validator = jQuery("#commonGrnPOApprovalRequestForm").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,
    rules: {
        'grn_details_id[]': {
            required: true
        },
    },
    messages: {
        'grn_details_id[]': {
            required: "Please Select GRN No."
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
            var grnDetailsId = jQuery(this).find('input[name="grn_details_id[]"]');
            if (jQuery(grnDetailsId).is(':checked')) {

                grnDetailsId = jQuery(grnDetailsId).val();

                approve_data[index] = { 'grn_details_id': grnDetailsId, };
                index++;
            }
        });

        if (!jQuery.isEmptyObject(approve_data)) {

            let data = new FormData(document.getElementById('commonGrnPOApprovalRequestForm'));
            let formValue = Object.fromEntries(data.entries());

            delete formValue["grn_details_id[]"];

            formValue = Object.assign(formValue, { 'approve_data': JSON.stringify(approve_data) });
            var formdata = new URLSearchParams(formValue).toString();


            let formUrl = RouteBasePath + "/store-grn_against_po_approval";
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
            toastError("Please Select GRN No.");
        }

    }
});

jQuery('#checkall-sm').click(function () {

    if (jQuery(this).is(':checked')) {
        jQuery("#approvalTable").find("[id^='grn_details_id_']:not(.in-use)").prop('checked', true).trigger('change');
        jQuery("#approvalTable").find("[id^='grn_details_id_']").prop('checked', true).trigger('change');
    } else {
        jQuery("#approvalTable").find("[id^='grn_details_id_']:not(.in-use)").prop('checked', false).trigger('change');
        jQuery("#approvalTable").find("[id^='grn_details_id_']").prop('checked', false).trigger('change');
    }
});