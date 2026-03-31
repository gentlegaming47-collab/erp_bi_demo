var approvalTable;

jQuery(document).ready(function () {
    var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };
    var table; // Declare DataTable variable outside

    // jQuery('#myDropdownd').change(function () {

    //     var selectedOption = jQuery(this).val();

    jQuery.ajax({
        url: RouteBasePath + "/get-approval_status",
        method: "GET",
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {

            var tblHtml = ``;
            if (data.response_code == 1 && data.approval_data.length > 0) {
                for (let idx in data.approval_data) {
                    // if (selectedOption === 'Dealer') {
                    //     nameField = data.approval_data[idx].dealer_name;
                    // } else if (selectedOption === 'Suppliers') {
                    //     nameField = data.approval_data[idx].supplier_name;
                    // } else if (selectedOption === 'Transporter') {
                    //     nameField = data.approval_data[idx].transporter_name;
                    // }

                    tblHtml += `<tr>
                                    <td><input type="checkbox" name="approval_status_id[]" id="approval_status_id_${data.approval_data[idx].id}" value="${data.approval_data[idx].id}"/></td>
                                    <td>${data.approval_data[idx].type}<input type="hidden" name="approval_status_type[]" id="approval_status_type_${data.approval_data[idx].id}" value="${data.approval_data[idx].type}"/></td>                          
                                    <td>${data.approval_data[idx].approval_status}<input type="hidden" name="approval_status_change[]" id="approval_status_change_${data.approval_data[idx].id}" value="${data.approval_data[idx].approval_status}"/></td> 
                                    <td>${data.approval_data[idx].name}</td>
                                    <td>${data.approval_data[idx].state}</td>

                                    </tr>`;
                }
            }

            // if (jQuery.fn.DataTable.isDataTable('#approvalTable')) {
            //     jQuery('#approvalTable').DataTable().destroy();
            // }
            jQuery('#approvalTable tbody').empty().append(tblHtml);

            approvalTable = jQuery('#approvalTable').DataTable({
                responsive: true,
                "scrollX": true,
                pageLength: 10,
                paging: true,
                "scrollX": true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",

                initComplete: function () {
                    // Exclude first column (index 0) from search
                    initColumnSearch('#approvalTable', [0]);

                    jQuery.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        if (settings.nTable.id !== 'approvalTable') {
                            return true;
                        }

                        var api = new jQuery.fn.dataTable.Api(settings);
                        var globalSearch = api.search().toLowerCase().trim();
                        var columnSearch = api.column(2).search().toLowerCase().trim();

                        var rawStatus = data[2] || '';
                        var statusText = rawStatus.replace(/<\/?[^>]+(>|$)/g, "").toLowerCase().trim();

                        if (columnSearch) {
                            if (
                                ('active approval pending'.startsWith(columnSearch) && !statusText.startsWith(columnSearch)) ||
                                ('deactive approval pending'.startsWith(columnSearch) && !statusText.startsWith(columnSearch))
                            ) {
                                return false;
                            }

                            if (!statusText.includes(columnSearch)) {
                                return false;
                            }
                        }

                        if (globalSearch) {
                            if (
                                'active approval pending'.startsWith(globalSearch) && !statusText.startsWith(globalSearch) &&
                                statusText !== globalSearch
                            ) {
                                return false;
                            }

                            if (
                                'deactive approval pending'.startsWith(globalSearch) && !statusText.startsWith(globalSearch) &&
                                statusText !== globalSearch
                            ) {
                                return false;
                            }

                            var rowData = data.join(' ').replace(/<\/?[^>]+(>|$)/g, "").toLowerCase();
                            if (!rowData.includes(globalSearch)) {
                                return false;
                            }
                        }
                        return true;
                    });

                    this.api().draw();
                }

            });


        }
    });

    // });

});



jQuery('#checkall-sm').click(function () {

    if (jQuery(this).is(':checked')) {
        jQuery("#approvalTable").find("[id^='approval_status_id']:not(.in-use)").prop('checked', true).trigger('change');
        jQuery("#approvalTable").find("[id^='approval_status_id']").prop('checked', true).trigger('change');
    } else {
        jQuery("#approvalTable").find("[id^='approval_status_id']:not(.in-use)").prop('checked', false).trigger('change');
        jQuery("#approvalTable").find("[id^='approval_status_id']").prop('checked', false).trigger('change');
    }
});


var validator = jQuery("#common_approval_status").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,

    rules: {
        'approval_status_id[]': {
            required: true
        },
    },

    messages: {
        'approval_status_id[]': {
            required: "Please Select Approval Status List"
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
            var approveId = jQuery(this).find('input[name="approval_status_id[]"]');
            if (jQuery(approveId).is(':checked')) {
                approveId = jQuery(approveId).val();
                var statusType = jQuery(this).find('input[name="approval_status_type[]"]').val();
                var statusChange = jQuery(this).find('input[name="approval_status_change[]"]').val();

                approve_data[index] = { 'approval_status_id': approveId, 'status_type': statusType, 'change_type': statusChange };
                index++;
            }
        });

        if (!jQuery.isEmptyObject(approve_data)) {

            let data = new FormData(document.getElementById('common_approval_status'));
            let formValue = Object.fromEntries(data.entries());

            delete formValue["approval_status_id[]"];

            formValue = Object.assign(formValue, { 'approve_data': JSON.stringify(approve_data) });
            var formdata = new URLSearchParams(formValue).toString();


            let formUrl = RouteBasePath + "/store-approval_status";
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
                    } else {
                        toastError(data.response_message);
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
        } else {
            toastError("Please Select Approval Status List");
        }

    }

});
