

var formId = jQuery('#SupplierItemMappingForm').find('input:hidden[name="id"]').val();

table = jQuery('#supplierItemMappingTable').DataTable({
    responsive: true,
    pageLength: 50,
    "oLanguage": {
        "sSearch": "Search :"
    },
    // columnDefs: [{
    //     targets: 0,
    //     "orderable": false,
    // }]
    columnDefs: [{
        targets: 0,
        "orderable": false,
        orderDataType: 'dom-checkbox'
    }]
});



// store and update process
if (formId != undefined && formId != '') { //if form is  edit

    jQuery(document).ready(function () {
        jQuery.ajax({
            url: RouteBasePath + "/get-supplier_item_mapping/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    for (let key in data.supplier_item_mapping) {

                        jQuery('#SupplierItemMappingForm').find('#supplier_id').val(data.supplier_item_mapping[key].supplier_id).trigger('liszt:updated');
                        getSupplierItems(data.supplier_item_mapping[key].supplier_id);

                        jQuery('#SupplierItemMappingForm').find(`#item_id_` + data.supplier_item_mapping[key].item_id).prop('checked', true);

                        if (data.supplier_item_mapping[key].in_use == true) {
                            jQuery('#SupplierItemMappingForm').find('#supplier_id').trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                            jQuery('#SupplierItemMappingForm').find(`#item_id_` + data.supplier_item_mapping[key].item_id).prop('checked', true).attr('readonly', true).addClass('in-use');

                        }


                    }
                    // }

                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "{{ route('manage-supplier_item_mapping')}}";
                    });
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
        jQuery('#supplierItemMappingTable').DataTable();
    });
} else { //for Add
    jQuery(document).ready(function () {
        setTimeout(() => {
            jQuery('#supplier_id').trigger('liszt:activate');
        }, 100);

    });
}

var newvalidator = jQuery("#SupplierItemMappingForm").validate({
    rules: {

        supplier_id: {
            required: true
        },
        'item_id[]': {
            required: true
        },
    },

    messages: {

        supplier_id: {
            required: "Please Select Supplier"
        },
        'item_id[]': {
            required: "Please Select Item"
        },
    },
    errorPlacement: function (error, element) {
        jAlert(error.text());
        return false;
    },


    submitHandler: function (form) {


        var storeArray = [];
        var index = 0;

        // jQuery("#supplierItemMappingTable tbody tr").each(function () {
        //     var checkbox = jQuery(this).find('input[type="checkbox"]');
        //     if (checkbox.is(':checked')) {
        //         var itemsValue = checkbox.val();
        //         if (itemsValue) {
        //             storeArray.push(itemsValue);
        //         }
        //     }
        // });

        // if (storeArray.length < 1) {
        //     toastError("Select At Least One Item");
        //     return false;
        // }

        table.$('tr').each(function (e) {

            var itemId = jQuery(this).find('input[name="item_id[]"]');


            if (jQuery(itemId).is(':checked')) {
                itemId = jQuery(itemId).val();

                storeArray[index] = { 'item_ids': itemId, };
                index++;
            }
        });

        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-supplier_item_mapping" : RouteBasePath + "/store-supplier_item_mapping";

        var data = new FormData(document.getElementById('SupplierItemMappingForm'));
        var formValue = Object.fromEntries(data.entries());

        // as1 = Object.assign(formValue, {
        //     'items': JSON.stringify(storeArray),
        // });
        // var formdata = new URLSearchParams(as1).toString();

        formValue = Object.assign(formValue, { 'items': JSON.stringify(storeArray) });
        var formdata = new URLSearchParams(formValue).toString();


        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: formdata,
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (formId !== undefined) {
                        toastSuccess(data.response_message, redirectFn);
                        function redirectFn() {
                            window.location.href = RouteBasePath + "/manage-supplier_item_mapping";
                        };
                    } else {
                        toastSuccess(data.response_message, redirectFn);
                        function redirectFn() {
                            window.location.reload();
                        }
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



// this code usind sort checked item first
jQuery.fn.dataTable.ext.order['dom-checkbox'] = function (settings, col) {
    return this.api().column(col, { order: 'index' }).nodes().map(function (td, i) {
        return jQuery('input', td).prop('checked') ? '0' : '1';
    });
};
// get items on supplier select

if (formId == undefined) {
    jQuery('#supplier_id').on('change', function () {
        var sid = jQuery('#supplier_id').val();
        getSupplierItems(sid);
    })
}

function getSupplierItems(th) {
    let supplier_id = th;

    if (supplier_id != "" && supplier_id != null) {
        jQuery.ajax({
            url: RouteBasePath + "/getSupplierItems?&supplier_id=" + supplier_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    if (formId == undefined) {
                        var tbltr = '';
                        if (data.item.length > 0) {


                            for (let indx in data.item) {
                                tbltr += `<tr>
                                        <td><input type="checkbox" class="simple-check" name="item_id[]" id="item_id_${data.item[indx].id}"  value="${data.item[indx].id}">
                                        </td>
                                        <td><input type="hidden" name="item_name[]" id="item_name_${data.item[indx].item_name}"  class="itemClass" value=" ${data.item[indx].item_name}">${data.item[indx].item_name}</td>                                        <td><input type="hidden" name="code[]" id="code" value="${data.item[indx].id}">${data.item[indx].item_code} 
                                        </td>
                                        <td><input type="hidden" name="code[]" id="code" value="${data.item[indx].id}"> ${data.item[indx].item_group_name} </td>                         
                                        <td><input type="hidden" name="unit[]" id="unit" value=${data.item[indx].id}">${data.item[indx].unit_name} 
                                        </td>                                       
                                    </tr>`;
                            }

                            if (jQuery.fn.DataTable.isDataTable('#supplierItemMappingTable')) {
                                jQuery('#supplierItemMappingTable').DataTable().destroy();
                            }

                            jQuery('#supplierItemMappingTable').append(tbltr);
                            table = jQuery('#supplierItemMappingTable').DataTable({
                                responsive: true,
                                pageLength: 50,
                                "oLanguage": {
                                    "sSearch": "Search :"
                                },
                                columnDefs: [{
                                    targets: 0,
                                    "orderable": false,
                                    orderDataType: 'dom-checkbox'
                                }]
                            });
                        }
                    }

                    if (data) {
                        table.$("[name='item_id[]']").prop('checked', false).trigger('change');
                        for (let key in data.LastItems) {
                            var item_ids = data.LastItems[key].item_id;
                            table.$(`#item_id_${item_ids}`).prop('checked', true).trigger('change');
                            if (data.LastItems[key].in_use == true) {
                                table.$(`#item_id_${item_ids}`).prop('checked', true).trigger('change').attr('readonly', true).addClass('in-use');
                            }

                        }
                        table.draw();
                    }
                } else {

                }
            },
        });
    }
}

jQuery('#checkall').click(function () {

    if (jQuery(this).is(':checked')) {
        jQuery("#supplierItemMappingTable").find("[id^='item_id_']:not(.in-use)").prop('checked', true).trigger('change');
    } else {
        jQuery("#supplierItemMappingTable").find("[id^='item_id_']:not(.in-use)").prop('checked', false).trigger('change');
    }

});