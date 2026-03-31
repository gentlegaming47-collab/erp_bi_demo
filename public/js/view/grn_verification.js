

var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}', 'X-CSRF-TOKEN': '{{ csrf_token() }}' };

var date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

jQuery('#GrnVerification').find('#gv_date').val(currentDate);



// Check All functionality
jQuery('#checkall').on('click', function () {

    var isChecked = jQuery(this).is(':checked');

    jQuery("#grn_verification_table")
        .find("[id^='grn_details_ids_']:not(.in-use)")
        .prop('checked', isChecked)
        .trigger('change');

    // Toggle all reason inputs
    jQuery("#grn_verification_table")
        .find("input[name='gv_reason[]']:not(.in-use)")
        .prop('readonly', !isChecked);
});

// Per-row checkbox change event
jQuery("#grn_verification_table").on('change', "[id^='grn_details_ids_']", function () {
    var row = jQuery(this).closest('tr');
    var reasonInput = row.find("input[name='gv_reason[]']");
    reasonInput.prop('readonly', !jQuery(this).is(':checked'));
    reasonInput.val('');
    
});

jQuery(document).ready(function () {
    var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

    var table = jQuery('#grn_verification_table').DataTable({

        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "sScrollX": "100%",
        "sScrollXInner": "110%",
        "bScrollCollapse": true,
        pageLength: 25,
        dom: 'Blfrtip',
        buttons:
            [
                {
                    extend: 'excel',
                    filename: 'GRN Verification',
                    title: "",
                    className: 'export_grn_verification d-none',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return idx !== 0 && table.column(idx).visible();
                        },
                        modifier: {
                            page: 'all'
                        }
                    },
                    action: newexportaction
                }
            ],
        ajax: {
            url: RouteBasePath + "/get-pending_grn_verification",
            type: "GET",
            dataType: 'json',
            headers: headerOpt,

            error: function (jqXHR, textStatus, errorThrown) {
                jQuery('#dyntable_processing').hide();
                if (jqXHR.status == 401) {
                    jAlert(jqXHR.statusText);
                } else {
                    jAlert('Somthing went wrong!');
                }
                console.log(JSON.parse(jqXHR.responseText));
            }
        },

        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    let autofocus = meta.row === 0 ? 'autofocus' : '';
                    return `<input type="checkbox" id="grn_details_ids_${row.grn_details_id}" name="grn_details_id[]"
                    data-qty=${row.mismatch_qty}
                    data-grn_secondary_details_id="${row.grn_secondary_details_id}"
                    data-item_details_id="${row.item_details_id}"
                    data-item_id="${row.item_id}"
                    data-to_location_id="${row.to_location_id}"
                    class="row-checkbox" value="${row.grn_details_id}" ${autofocus}>`;
                }
            },

            { data: 'location_name', name: 'locations.location_name ', },
            { data: 'grn_number', name: 'grn_material_receipt.grn_number', },
            { data: 'grn_date', name: 'grn_material_receipt.grn_date', },
            { data: 'item_name', name: 'items.item_name', },
            { data: 'secondary_item_name', name: 'item_details.secondary_item_name', },
            { data: 'dp_number', name: 'dispatch_plan.dp_number', },
            { data: 'dp_date', name: 'dispatch_plan.dp_date', },
            { data: 'plan_qty', name: 'plan_qty', },
            { data: 'grn_qty', name: 'material_receipt_grn_details.grn_qty', },
            { data: 'mismatch_qty', name: 'material_receipt_grn_details.mismatch_qty', },
            { data: 'unit_name', name: 'units.unit_name' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `<input type="text" 
                                name="gv_reason[]" 
                                class="form-control" 
                                placeholder="Enter Reason" readonly>`;
                }
            }


        ]
    });
});


// store 
var validator = jQuery("#GrnVerification").validate({

    rules: {
        gv_date: {
            required: true,
            date_check: true
        },
        'grn_details_id[]': {
            required: true
        },
        /* "gv_reason[]": {
             required: function (element) {
                 var row = jQuery(element).closest('tr');
                 var checkbox = row.find("input[name='grn_details_id[]']");
                 return checkbox.is(':checked'); 
             }
         }*/

    },

    messages: {

        gv_date: {
            required: "Please Enter Date",
        },
        'grn_details_id[]': {
            required: "Please Select At Least One GRN No."
        },
        /* "gv_reason[]": {
             required: "Please Enter Reason"
         }*/
    },
    errorPlacement: function (error, element) {
        jAlert(error.text());
        return false;
    },


    submitHandler: function (form) {


        gv_data = [];
        var index = 0;


        jQuery('#grn_verification_table tbody tr').each(function () {

            var grdId = jQuery(this).find('input[name="grn_details_id[]"]');

            if (jQuery(grdId).is(':checked')) {
                grdId = jQuery(grdId).val();
                mismatch_qty = jQuery(this).find('input[name="grn_details_id[]"]').data('qty');
                grn_secondary_details_id = jQuery(this).find('input[name="grn_details_id[]"]').data('grn_secondary_details_id');
                item_details_id = jQuery(this).find('input[name="grn_details_id[]"]').data('item_details_id');
                item_id = jQuery(this).find('input[name="grn_details_id[]"]').data('item_id');
                gv_reason = jQuery(this).find('input[name="gv_reason[]"]').val();
                to_location_id = jQuery(this).find('input[name="grn_details_id[]"]').data('to_location_id');


                gv_data[index] =
                {
                    'grn_details_id': grdId,
                    'mismatch_qty': mismatch_qty,
                    'grn_secondary_details_id': grn_secondary_details_id,
                    'item_details_id': item_details_id,
                    'item_id': item_id,
                    'gv_reason': gv_reason,
                    'to_location_id': to_location_id
                };
                index++;
            }
        });

        var data = new FormData(document.getElementById('GrnVerification'));
        var formValue = Object.fromEntries(data.entries());
        formValue = Object.assign(formValue, { 'gv_data': JSON.stringify(gv_data) });
        var formdata = new URLSearchParams(formValue).toString();
        jQuery.ajax({

            url: RouteBasePath + "/store-grn_verification",
            type: 'POST',
            data: formdata,
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    toastSuccess(data.response_message, nextFn);
                    function nextFn() {
                        window.location.reload();
                    }

                } else {
                    toastError(data.response_message);
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

    }

});
