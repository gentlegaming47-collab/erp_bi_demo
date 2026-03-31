setTimeout(() => {
    // jQuery('#ist_sequence').focus();
    jQuery("#ist_item_id").trigger('liszt:activate');
}, 100);
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

var formId = jQuery('#ItemStockTransfer').find('input:hidden[name="id"]').val();

var MainItemDrpHtml = `<option value="">Select Item</option>`;
var DetailItemDrpHtml = `<option value="">Select Item</option>`;
var productDrpHtml = `<option value="">Select Item</option>`;


jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

    if (formId != null && formId != undefined) {
        return jQuery.ajax({
            url: RouteBasePath + "/get-item_stock_transfer/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    jQuery("#ist_sequence").val(data.stock_transfer_data.ist_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery("#ist_number").val(data.stock_transfer_data.ist_number).prop({ tabindex: -1, readonly: true });
                    jQuery("#ist_date").val(data.stock_transfer_data.ist_date);
                    jQuery("#item_stock").val(parseFloat(data.stock_transfer_data.stock_qty).toFixed(3));
                    jQuery("#main_unit").val(data.stock_transfer_data.unit_name);

                    jQuery("#ist_item_id").val(data.stock_transfer_data.ist_item_id).trigger('liszt:updated');
                    getDetailsItems().done(function (resposne) {
                        jQuery('#ist_item_details_id').val(data.stock_transfer_data.ist_item_details_id).trigger('liszt:updated');
                        editItemDetail(data.stock_transfer_detail_data);
                    });

                    jQuery("#ist_item_id").prop({ tabindex: -1 }).attr('readonly', true);
                    jQuery("#ist_item_details_id").prop({ tabindex: -1 }).attr('readonly', true);



                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = RouteBasePath + "/manage-item_stock_transfer";
                    });
                }
            },

            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);

                if (jqXHR.status == 401) {
                    jAlert(jqXHR.statusText);
                    // toastError(jqXHR.statusText);
                } else {
                    jAlert('Something went wrong!');
                    // toastError('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });


    } else {
        getLatestISTNo();
    }

});

// add time 
function addItemDetail() {
    var counter = 1;
    var thisHtml = `
    <tr>
        <td>
            <a onclick="removeItemDetail(this)"><i class="action-icon iconfa-trash so_details"></i></a>
        </td>
   
        <td> 
            <select name="details_item_id[]"  class="chzn-select details_item_id so_item_select_width" onChange="VerifyItem(this),ItemStockItem(this)">${DetailItemDrpHtml}</select>
        </td>
        <td>
        <input type="hidden" name="second_stock_qty[]">
            <input type="text" name="stock_qty[]" class="only-numbers">
        </td>
        <td><input type="text" name="unit[]" id="unit" style="width:103px;" class="form-control POaddtables" tabindex="-1"  readonly/></td>

    </tr>`;
    jQuery('#ist_table tbody').append(thisHtml);
    jQuery('.details_item_id').chosen();
}

function removeItemDetail(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            jQuery(th).parents("tr").remove();
        }
    }
    )
}

// get details items
function getDetailsItems() {

    var ist_item_id = jQuery("#ist_item_id option:selected").val();

    if (ist_item_id != "" && ist_item_id != null) {
        return jQuery.ajax({
            url: RouteBasePath + "/get-ist_details_items?ist_item_id=" + ist_item_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    MainItemDrpHtml = `<option value="">Select Item</option>`;


                    if (data.ISTDetailsItem.length > 0) {
                        for (let indx in data.ISTDetailsItem) {
                            MainItemDrpHtml += `<option value="${data.ISTDetailsItem[indx].item_details_id}" data-item_id="${data.ISTDetailsItem[indx].item_id}" data-stock_qty="${data.ISTDetailsItem[indx].stock_qty}"  data-secondary_qty="${data.ISTDetailsItem[indx].secondary_qty}" data-secondary_unit="${data.ISTDetailsItem[indx].unit_name}">${data.ISTDetailsItem[indx].secondary_item_name} </option>`;

                            productDrpHtml += `<option value="${data.ISTDetailsItem[indx].item_details_id}" data-item_id="${data.ISTDetailsItem[indx].item_id}" data-stock_qty="${data.ISTDetailsItem[indx].stock_qty}"  data-secondary_qty="${data.ISTDetailsItem[indx].secondary_qty}" data-secondary_unit="${data.ISTDetailsItem[indx].unit_name}">${data.ISTDetailsItem[indx].secondary_item_name} </option>`;
                        }
                    } else {
                        MainItemDrpHtml = `<option value="">Select Item</option>`;
                        jQuery('#ist_item_details_id').empty().append(MainItemDrpHtml).trigger('liszt:updated');
                        DetailItemDrpHtml = `<option value="">Select Item</option>`;

                        jQuery('.details_item_id').empty().append(DetailItemDrpHtml).trigger('liszt:updated');

                        jQuery('#addPart').attr('disabled', true);


                    }
                    jQuery('#ist_item_details_id').empty().append(MainItemDrpHtml).trigger('liszt:updated');



                } else {
                }
            },
        });
    } else {
        jQuery('#ist_item_details_id').empty().val('').trigger('liszt:updated');


    }
}

// get Details items Except selected Item
function getDetailsExceptSelectedItems() {

    var ist_item_id = jQuery("#ist_item_id option:selected").val();
    var ist_details_item_id = jQuery("#ist_item_details_id option:selected").val();



    DetailItemDrpHtml = ``;

    jQuery('.details_item_id').each(function () {
        jQuery(this).chosen("destroy");
        jQuery(this).empty();
        jQuery(this).html(DetailItemDrpHtml);
        jQuery(this).chosen();
    });

    if (ist_item_id != "" && ist_item_id != null && ist_details_item_id != "" && ist_details_item_id != null) {

        var selectedOption = jQuery("#ist_item_details_id option:selected");
        var stock_qty = selectedOption.data("stock_qty");
        var secondary_qty = selectedOption.data("secondary_qty");
        var main_secondary_umit = selectedOption.data("secondary_unit");

        jQuery("#item_stock").val(parseFloat(stock_qty / secondary_qty).toFixed(3));
        jQuery("#main_unit").val(main_secondary_umit);

        jQuery.ajax({
            url: RouteBasePath + "/get-ist_selected_details_items?ist_item_id=" + ist_item_id + "&ist_details_item_id=" + ist_details_item_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    DetailItemDrpHtml = `<option value="">Select Item</option>`;
                    if (data.ISTSelectedDetailsItem.length > 0) {

                        for (let indx in data.ISTSelectedDetailsItem) {
                            DetailItemDrpHtml += `<option value="${data.ISTSelectedDetailsItem[indx].item_details_id}" data-item_id="${data.ISTSelectedDetailsItem[indx].item_id}" data-secondary_qty="${data.ISTSelectedDetailsItem[indx].secondary_qty}" data-max_stock="${parseFloat(stock_qty / data.ISTSelectedDetailsItem[indx].secondary_qty).toFixed(3)}" data-secondary_unit="${data.ISTSelectedDetailsItem[indx].unit_name}">${data.ISTSelectedDetailsItem[indx].secondary_item_name} </option>`;
                        }
                        jQuery('#addPart').attr('disabled', false);
                        if (formId == undefined) {
                            jQuery("input[name='stock_qty[]']").val('');
                        }

                    } else {
                        jQuery('.details_item_id').chosen('destroy');
                        DetailItemDrpHtml += `<option value="">Select Item</option>`;
                        jQuery('.details_item_id').empty().append(DetailItemDrpHtml).trigger('liszt:updated');

                        jQuery('#addPart').attr('disabled', true);
                        jQuery('.details_item_id').chosen();
                        jQuery("input[name='stock_qty[]']").val('');


                    }
                    jQuery('.details_item_id').each(function () {
                        jQuery(this).chosen("destroy");
                        jQuery(this).html(DetailItemDrpHtml).trigger('liszt:updated');
                        jQuery(this).chosen();
                    });

                } else {
                }
            },
        });
    } else {
        // jQuery('#ist_item_details_id').empty().val('').trigger('liszt:updated');

    }
}

function ItemStockItem(th) {
    // var selected = jQuery(th).find('option:selected').data('max_stock');
    // jQuery(th).closest('tr').find('input[name="stock_qty[]"]').attr('max', selected);
    var detail_unit_name = jQuery(th).find('option:selected').data('secondary_unit');
    jQuery(th).closest('tr').find('input[name="unit[]"]').val(detail_unit_name);

}


var validator = jQuery("#ItemStockTransfer").validate({
    onclick: false,
    onkeyup: false,
    rules: {
        itc_sequence: {
            required: true
        },
        itc_date: {
            required: true,
            date_check: true,
            dateFormat: true
        },
        ist_item_id: {
            required: true
        },
        ist_item_details_id: {
            required: true
        },
        'details_item_id[]': {
            required: true,
        },
        'stock_qty[]': {
            required: true
        }
    },

    messages: {

        itc_sequence: {
            required: "Please Enter Return Number"
        },
        itc_date: {
            required: "Please Enter Date."
        },
        ist_item_id: {
            required: "Please Select Item"
        },
        ist_item_details_id: {
            required: "Please Select Secondary Item"
        },
        'details_item_id[]': {
            required: "Please Select Detail Item"
        },
        'stock_qty[]': {
            required: "Please Enter Qty."
        }


    },


    submitHandler: function (form) {


        let checkLength = jQuery("#ist_table tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        if (checkLength < 1) {
            jAlert("Please Add At Least One Item Stock Transfer Detail.");
            return false;
        }

        var totalStockQty = 0;
        jQuery(".details_item_id").each(function () {
            var secondary_qty = jQuery(this).find("option:selected").data("secondary_qty");
            var stock_qty = jQuery(this).closest('tr').find("input[name='stock_qty[]']").val();
            if (secondary_qty) {
                jQuery(this).closest('tr').find("input[name='second_stock_qty[]']").val(parseFloat(secondary_qty) * parseFloat(stock_qty));
                totalStockQty += parseFloat(secondary_qty) * parseFloat(stock_qty);
            }
        });


        var selectedOption = jQuery("#ist_item_details_id option:selected");
        var stock_qty = selectedOption.data("stock_qty");
        var secondary_qty = selectedOption.data("secondary_qty");

        jQuery('#main_second_stock').val(secondary_qty);


        // var check_qty = totalStockQty / secondary_qty;

        // var isDecimal = check_qty % 1 !== 0;

        var check_qty = parseFloat(totalStockQty) / parseFloat(secondary_qty);

        var isDecimal = (check_qty === 1);

        if (!isDecimal) {
            jAlert("Detail Qty. Not Matched With Item Qty.");
            return false;
        }


        if (parseFloat(totalStockQty) > parseFloat(stock_qty)) {
            jAlert("Insufficient Stock");
            return false;
        }

        var formdata = jQuery('#ItemStockTransfer').serialize();


        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-item_stock_transfer" : RouteBasePath + "/store-item_stock_transfer";
        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: formdata,
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (formId != null && formId != "") {
                        function nextFn() {
                            window.location.href = RouteBasePath + "/manage-item_stock_transfer";
                        }

                        toastSuccess(data.response_message, nextFn);
                    } else {
                        function nextFn() {
                            window.location.reload();
                        }
                        toastSuccess(data.response_message, nextFn);
                    }
                } else {
                    jQuery("#ist_btn").attr('disabled', false);
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);

                if (errMessage.errors) {
                    jQuery("#ist_btn").attr('disabled', false);
                    validator.showErrors(errMessage.errors);
                } else if (jqXHR.status == 401) {
                    jQuery("#ist_btn").attr('disabled', false);
                    toastError(jqXHR.statusText);
                } else {
                    jQuery("#ist_btn").attr('disabled', false);
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
});
// get the latest number
function getLatestISTNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_ist_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#ist_number').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#ist_date').val(currentDate);
                jQuery('#ist_number').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });
                jQuery('#ist_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
                // jQuery('#ist_sequence').val(data.number);
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#ist_number').removeClass('file-loader');
            console.log('Field To Get Latest IST No.!')
        }
    });
}


// old validation of duplicate item
function VerifyItem(th) {
    var selected = jQuery(th).val();
    var thisselected = jQuery(th);

    if (selected) {
        jQuery(".details_item_id").not(thisselected).each(function () {
            if (thisselected.val() == jQuery(this).val()) {
                jAlert('This Item Is Already Selected.');
                var selectTd = thisselected.closest('td');
                var newSelect = `
                    <select name="details_item_id[]" 
                            class="chzn-select details_item_id so_item_select_width" 
                            onchange="VerifyItem(this)">
                        ${DetailItemDrpHtml}
                    </select>`;
                selectTd.html(newSelect);
                selectTd.find(".details_item_id").chosen();
            }
        });
    }
}


function editItemDetail(data) {
    var thisHtml = '';
    for (let key in data) {
        thisHtml += `
        <tr>
            <td>
                <a><i class="action-icon iconfa-trash so_details"></i></a>
            </td>
    
            <td> 
                <select name="details_item_id[]"  class="chzn-select details_item_id st_item_details_id_${key} so_item_select_width" onChange="VerifyItem(this),ItemStockItem(this)" tabindex="-1" readonly>${productDrpHtml}</select>
            </td>
            <td>
                <input type="hidden" name="second_stock_qty[]">
                <input type="text" name="stock_qty[]" class="only-numbers"  value="${data[key].stock_transfer_qty}" tabindex="-1" readonly>
            </td>
            <td><input type="text" name="unit[]" id="unit" style="width:103px;" class="form-control POaddtables" tabindex="-1" readonly /></td>

        </tr> `;
    }
    jQuery('#ist_table tbody').empty().append(thisHtml);

    for (let key in data) {
        var item_id = data[key].item_details_id ? data[key].item_details_id : "";
        jQuery(`.st_item_details_id_${key}`).val(item_id).trigger('liszt:updated').change();
    }
    jQuery('.details_item_id').chosen();
}