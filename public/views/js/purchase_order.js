var po_data = [];
var pr_data = [];
var check_pr_data = [];
var btn_disabled = false;
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}

var formId = jQuery('#PurchaseOrderForm').find('input:hidden[name="id"]').val();

// if (formId == '' || formId == null && formId == undefined) {
//     // addPartDetail();
//     // var sid = jQuery('#PurchaseOrderForm').find('#supplier_id').val();
//     // getItemsfromMapping(sid);

// }
// jQuery("#PurchaseOrderForm").find("#supplier_id").on("change", function () {
//     getItemsfromMapping(this.value);
// });

// edit data
if (formId != undefined && formId != '') { //if form is  edit

    jQuery(document).ready(function () {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({
            url: RouteBasePath + "/get-purchase_order/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    if (data.purchase_order.in_use == true) {
                        setTimeout(() => {
                            // jQuery('#po_date').focus();
                            // jQuery("#supplier_id").trigger('liszt:activate');
                            jQuery('#person').focus();
                        }, 100);

                        // jQuery('#supplier_id').val(data.purchase_order.supplier_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                        jQuery('#ship_to').val(data.purchase_order.to_location_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                        jQuery('#po_sequence').val(data.purchase_order.po_sequence).prop({ tabindex: -1, readonly: true });

                        disabledDropdownVal();

                    } else {
                        setTimeout(() => {
                            // jQuery('#po_sequence').focus();
                            jQuery('#person').focus();
                        }, 100);

                        // jQuery('#supplier_id').val(data.purchase_order.supplier_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#ship_to').val(data.purchase_order.to_location_id).trigger('liszt:updated');
                        // jQuery('#po_sequence').val(data.purchase_order.po_sequence);
                        jQuery('#po_sequence').val(data.purchase_order.po_sequence).prop({ tabindex: -1, readonly: true });
                    }

                    jQuery('#supplier_id').val(data.purchase_order.supplier_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                    jQuery('#supplier_id').change();

                    jQuery('#ref_no').val(data.purchase_order.ref_no);
                    jQuery('#po_date').val(data.purchase_order.po_date);
                    jQuery('#ref_date').val(data.purchase_order.ref_date);
                    jQuery('#po_no').val(data.purchase_order.po_number).prop({ tabindex: -1, readonly: true });
                    jQuery('#person').val(data.purchase_order.person_name);
                    jQuery('#order_by').val(data.purchase_order.order_by);
                    jQuery('#check_date').val(data.purchase_order.delivery_date);
                    jQuery('#po_total_qty').val(data.purchase_order.total_qty);
                    jQuery('#po_total_amount').val(data.purchase_order.total_amount);
                    jQuery('#pf_charge').val(data.purchase_order.pf_charge);
                    jQuery('#freight').val(data.purchase_order.frieght).trigger('liszt:updated');
                    jQuery('#gst').val(data.purchase_order.gst);
                    jQuery('#test_certificate').val(data.purchase_order.test_certificate);
                    jQuery('#order_acceptance').val(data.purchase_order.order_acceptance);
                    jQuery('#prepared_by').val(data.purchase_order.prepared_by);
                    jQuery('#payment_terms').val(data.purchase_order.payment_terms);
                    jQuery('#sp_notes').val(data.purchase_order.special_notes);

                    if (data.purchase_order.is_approved == 1) {
                        jQuery('#purchase_button').attr('disabled', true);
                        jQuery('#addPartButton').attr('disabled', true);
                        jQuery('.toggleModalBtn').prop('disabled', true);
                        btn_disabled = true;
                    }

                    setTimeout(() => {
                        getPrData()
                    }, 1000);

                    if (data.purchase_order_details.length > 0 && !jQuery.isEmptyObject(data.purchase_order_details)) {
                        for (let ind in data.purchase_order_details) {
                            pr_data.push(data.purchase_order_details[ind]);
                        }

                    }

                    if (data.not_use_pr_details_id.length > 0 && !jQuery.isEmptyObject(data.not_use_pr_details_id)) {
                        for (let ind in data.not_use_pr_details_id) {
                            check_pr_data.push(data.not_use_pr_details_id[ind]);
                        }

                    }

                    fillPOTable(data.purchase_order_details);

                    // loadPurchaseOrderData(data)
                    // await getItemsfromMapping(data.purchase_order.supplier_id);
                    // setTimeout(() => {
                    //     fillPOTable(data.purchase_order_details);
                    // }, 500);

                    if (data.purchase_order.po_form_type == "M") {
                        jQuery('div#show').hide();
                    }

                    jQuery('#show-progress').removeClass('loader-progress-whole-page');

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

    changeDateFormat();
    jQuery("#po_date").on("change", function (e) {
        if (e.target.value != undefined && e.target.value != "") {
            var po_date = jQuery(e.target).datepicker('getDate');
            changeDateFormat(po_date);
        }
    });


} else { //for Add
    jQuery(document).ready(function () {
        setTimeout(() => {
            // jQuery('#po_sequence').focus();
            jQuery("#supplier_id").trigger('liszt:activate');
        }, 100);
        getLatestPoNo();
        getSupplier();


        changeDateFormat();
        jQuery("#po_date").on("change", function (e) {
            if (e.target.value != undefined && e.target.value != "") {
                var po_date = jQuery(e.target).datepicker('getDate');
                // var po_date = jQuery(e.target).datepicker('getDate');
                // changeDateFormat(po_date);
                // checkPODate(null, po_date);
            }
        });
    });
}
// end edit data

// async function loadPurchaseOrderData(data) {
//     try {
//         await getItemsfromMapping(data.purchase_order.supplier_id); // Wait for items to load
//         fillPOTable(data.purchase_order_details); // Once items are loaded, fill the table
//     } catch (error) {
//         console.log("Error: ", error);
//         toastError('Something went wrong!');
//     }
// }

function changeDateFormat(date = null) {
    if (date != null) {
        jQuery('.trans1-date-picker').datepicker('destroy');
        jQuery(".trans1-date-picker:not([readonly])").datepicker({
            dateFormat: "dd/mm/yy",
            minDate: date,
        });

    } else {
        jQuery(".trans1-date-picker:not([readonly])").datepicker({
            dateFormat: "dd/mm/yy",
            minDate: new Date(),
        });
    }
}

function fillPOTable(purchase_order_details) {
    if (purchase_order_details.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in purchase_order_details) {
            var formIndx = key;
            var sr_no = counter;
            var purchase_order_details_id = purchase_order_details[key].po_details_id ? purchase_order_details[key].po_details_id : 0;
            var item_id = purchase_order_details[key].item_id ? purchase_order_details[key].item_id : "";
            var item_name = purchase_order_details[key].item_name ? purchase_order_details[key].item_name : "";
            var item_code = purchase_order_details[key].item_code ? purchase_order_details[key].item_code : "";
            var rate_per_unit = purchase_order_details[key].rate_per_unit ? purchase_order_details[key].rate_per_unit.toFixed(3) : "";
            var discount = purchase_order_details[key].discount ? purchase_order_details[key].discount.toFixed(2) : parseFloat(0).toFixed(2);
            var unit_name = purchase_order_details[key].unit_name ? purchase_order_details[key].unit_name : "";
            var po_qty = purchase_order_details[key].po_qty ? purchase_order_details[key].po_qty.toFixed(3) : "";
            var del_date = purchase_order_details[key].del_date ? purchase_order_details[key].del_date : "";
            if (purchase_order_details_id == 0) {
                var amount = (po_qty * rate_per_unit).toFixed(3);
            } else {
                var amount = purchase_order_details[key].amount ? parseFloat(purchase_order_details[key].amount).toFixed(3) : '';
            }
            var remarks = purchase_order_details[key].remarks ? checkSpecialCharacter(purchase_order_details[key].remarks) : "";
            var in_use = purchase_order_details[key].in_use ? purchase_order_details[key].in_use : "";
            var pr_details_id = purchase_order_details[key].pr_details_id ? purchase_order_details[key].pr_details_id : "";
            var stock_qty = purchase_order_details[key].stock_qty ? purchase_order_details[key].stock_qty.toFixed(3) : parseFloat(0).toFixed(3);



            thisHtml += `
            <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="purchase_order_detail_id[]" value="${purchase_order_details_id}"></td></tr>
                  
            <tr>        
            <td>
                <a  ${in_use == true ? '' : 'onclick="removeSoDetails(this)"'} ><i class="action-icon iconfa-trash so_details "></i></a>
            </td>        
        
            <td class="sr_no">${sr_no}</td>
        
            <td class="item_select_width">${item_name}</td>
         
            <td><input type="hidden" name="form_indx" value="${formIndx}"/><input type="hidden" name="pr_details_id[]" id="pr_details_id" value="${pr_details_id}"><input type="hidden" name="item_id[]" id="item_id" value="${item_id}"><input type="text" name="code[]" id="code"  class="form-control POaddtables salesmanageTable" tabindex="-1" value="${item_code}" readonly  tabindex="-1"/></td>
        
            <td><input type="text" name="po_qty[]" onblur="formatPoints(this,3)" id="po_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey po_qty"  value="${po_qty}" style="width:50px;" readonly tabindex="-1"/></td>

            <td>${stock_qty}</td>
            
            <td><input type="text" name="rate_unit[]" id="rate_unit" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control  rate_unit  isNumberKey" value="${rate_per_unit}" onblur="formatPoints(this,3)"   onchange="checkRateUnitPrice(this)" style="width:60px;"/></td>

            <td><input type="text" name="discount[]" id="discount" onchange="soRateUnit(this)" id="discount" class="form-control  discount  isNumberKey" maxlength="5" value="${discount}" onblur="formatPoints(this,2)" style="width:50px;" /></td>

            <td><input type="text" name="del_date[]" class="form-control potabledate salesmanageTable date-picker no-fill del_date" value="${del_date}" onChange="checkDelDate(this.value)" /></td>

            <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" value="${unit_name}" readonly  tabindex="-1"/></td>

            <td><input type="text" name="amount[]" id="amount"  class="form-control  amount"
             value="${amount}" readonly style="width:60px;" tabindex="-1"/></td>

            
            <td><textarea  name="remarks[]" id="remark" rows="4">${remarks}</textarea></td><tr>`;


            counter++;

        }

        jQuery('#purchasetable tbody').empty().append(thisHtml);

    }
    sumSoQty();
    totalAmount();
    srNo();
    disabledDropdownVal();
    // checkPODate();

    jQuery('.date-picker').datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
    });
}


function addPartDetail() {

    var thisHtml = `
                < tr style = "display:none;" > <td class="colspan=10"><input type="hidden" name="purchase_order_detail_id[]" value="0"></td></tr >

                    <tr>

                        <td>
                            <a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                        </td>


                        <td class="sr_no"></td>

                        <td> <select name="item_id[]" class="chzn-select  add_item item_id item_select_width" onChange="getItemData(this),getItemRateSupplierWise(this)">${productDrpHtml}</select></td>

                        <td><input type="text" name="code[]" id="code" class="form-control salesmanageTable POaddtables" tabindex="-1" readonly /></td>

                        <td><input type="text" name="po_qty[]" onblur="formatPoints(this,3)" id="po_qty" onKeyup="sumSoQty(this)" class="form-control isNumberKey po_qty" style="width:50px;" disabled /></td>

                        <td><input type="text" name="rate_unit[]" id="rate_unit" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control  rate_unit  isNumberKey" onblur="formatPoints(this,3)" onchange="checkRateUnitPrice(this)" style="width:60px;" disabled /></td>

                        <td><input type="text" name="discount[]" maxlength="5" id="discount" onchange="soRateUnit(this)" id="discount" class="form-control  discount  isNumberKey" onblur="formatPoints(this,2)" style="width:50px;" disabled /></td>

                        <td><input type="text" name="del_date[]" class="form-control potabledate salesmanageTable date-picker no-fill del_date" onChange="checkDelDate(this.value)" disabled /></td>

                        <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly /></td>

                        <td><input type="text" name="amount[]" id="amount" tabindex="-1" class="form-control  amount " tabindex="-1" style="width:60px;" readonly /></td>

                        <td><textarea name="remarks[]" id="remarks" rows="4" disabled /></td>



                    </tr>`;
    jQuery('#purchasetable tbody').append(thisHtml);
    srNo();
    // sumSoQty();
    totalAmount();
    // checkPODate();

    // Reinitialize date-picker for new elements
    jQuery('.date-picker').datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
    });

}


function getLatestPoNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_po_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            //  var stgDrpHtml = `< option value = "" > Select Ship To</option > `;
            jQuery('#po_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#po_no').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });
                jQuery('#po_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
                // jQuery('#po_sequence').val(data.number);
                jQuery('#po_date').val(currentDate);
                //stgDrpHtml += `< option value = "${data.location.id}" > ${ data.location.location_name }</option > `;
                // jQuery('#ship_to').append(stgDrpHtml);
                jQuery('#ship_to').val(data.location.id).trigger('liszt:updated');
                //jQuery('#test_certificate').val(data.LastCertificate.test_certificate);
                //jQuery('#order_acceptance').val(data.LastCertificate.order_acceptance);
                jQuery('#prepared_by').val(prepared_by[0]);
                jQuery('#gst').val('Extra As Applicable');
                jQuery('#test_certificate').val('Yes');
                jQuery('#order_acceptance').val('Required');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#po_no').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}



// function getLatestPoNo() {
//     jQuery.ajax({
//         url: RouteBasePath + "/get-latest_po_no",
//         type: 'GET',
//         headers: headerOpt,
//         dataType: 'json',
//         processData: false,
//         success: function (data) {
//             var stgDrpHtml = `< option value = "" > Select Ship To</option > `;
//             jQuery('#po_no').removeClass('file-loader');
//             if (data.response_code == 1) {
//                 jQuery('#po_no').val(data.latest_po_no);
//                 jQuery('#po_sequence').val(data.number);
//                 jQuery('#po_date').val(currentDate);
//                 stgDrpHtml += `< option value = "${data.location.id}" > ${ data.location.location_name }</option > `;
//                 jQuery('#ship_to').append(stgDrpHtml);
//                 jQuery('#ship_to').val(data.location.id).trigger('liszt:updated');
//             } else {
//                 console.log(data.response_message)
//             }
//         },
//         error: function (jqXHR, textStatus, errorThrown) {
//             jQuery('#po_no').removeClass('file-loader');
//             console.log('Field To Get Latest SO No.!')
//         }
//     });
// }

function getItemData(th) {
    let item = th.value;

    // var selected = jQuery(th).val();
    // var thisselected = jQuery(th);
    // if (selected) {
    //     jQuery(jQuery('.item_id').not(jQuery(th))).each(function (index) {

    //         if (thisselected.val() == jQuery(this).val()) {
    //             jAlert('This Item Is Already Selected.');
    //             var selectTd = thisselected.closest('td');

    //             selectTd.html(`< select name = "item_id[]" class="chzn-select add_item item_id item_select_width" onChange = "getItemData(this), sumSoQty(this)" > ${ productDrpHtml }</select > `);
    //             jQuery('.item_id').chosen();
    //         }
    //     });
    // }

    if (item != "" && item != null) {
        jQuery.ajax({
            url: RouteBasePath + "/get-fitting_item_data?item=" + item,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    // jQuery(th).closest('tr').find("#code").val(data.item.item_code);
                    jQuery(th).parents('tr').find("#code").val(data.item.item_code);
                    jQuery(th).parents('tr').find("#item_id").val(data.item.id);
                    jQuery(th).parents('tr').find("#group").val(data.item.item_group_name);
                    jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);
                    jQuery(th).parents('tr').find("#po_qty").prop('disabled', false);
                    jQuery(th).parents('tr').find(".del_date").prop('disabled', false);
                    jQuery(th).parents('tr').find("#rate_unit").prop('disabled', false);
                    if (formId == undefined) {
                        jQuery(th).parents('tr').find("#discount").prop('disabled', false).val(parseFloat(0).toFixed(2));
                    } else {
                        jQuery(th).parents('tr').find("#discount").prop('disabled', false);
                    }
                    jQuery(th).parents('tr').find("#remarks").prop('disabled', false);
                    // if (formId == undefined) {
                    //     // checkPODate(item);
                    //     // changeCheckDate(item);

                    // }

                } else {
                    jQuery('#code').val('');
                    jQuery('#item_id').val('');
                    jQuery('#group').val('');
                    jQuery('#unit').val('');
                    jQuery('#po_qty').val('');
                    jQuery('#rate_unit').val('');
                    jQuery('#discount').val('');
                    jQuery('#remarks').val('');
                }
            },
        });
    }
}

function checkDelDate(delDate) {

    let poDate = jQuery("#po_date").val();

    var poDateArray = poDate.split('/');
    var delDateArray = delDate.split('/');

    var poDateObj = new Date(poDateArray[2], poDateArray[1] - 1, poDateArray[0]);
    var delDateObj = new Date(delDateArray[2], delDateArray[1] - 1, delDateArray[0]);
    if (poDateObj > delDateObj) {
        jAlert("Delivery Date Less Then to PO Date")
        return false;
    }
}
function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
    jQuery(".item_id").chosen();
}

function removeSoDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        // let checkLength = jQuery("#purchasetable tbody tr").filter(function () {
        //     return jQuery(this).css('display') !== 'none';
        // }).length;


        // if (checkLength > 1) {

        if (r === true) {

            let poPartId = jQuery(th).closest('tr').prev('tr').find('input[name="purchase_order_detail_id[]"]').val();
            if (poPartId != '') {
                jQuery.ajax({
                    url: RouteBasePath + "/check-po_part_in_use?po_part_id=" + poPartId + "&po_id=" + formId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        jQuery(th).removeClass('file-loader');
                        if (data.response_code == 1) {
                            toastError(data.response_message);
                        } else {
                            // if (checkLength > 1) {
                            jQuery(th).parents("tr").remove();
                            srNo();
                            var po_qty = jQuery(th).parents('tr').find('#po_qty').val();
                            var po_amt = jQuery(th).parents('tr').find('#amount').val();

                            if (po_qty && po_amt) {
                                var po_total = jQuery('.poqtysum').text();
                                var amt_total = jQuery('.amountsum').text();
                                if (po_total != "" && amt_total != "") {
                                    po_final_total = parseFloat(po_total) - parseFloat(po_qty);
                                    amt_final_total = parseFloat(amt_total) - parseFloat(po_amt);
                                }

                                po_final_total > 0 ? jQuery('.poqtysum').text(parseFloat(po_final_total).toFixed(3)) : jQuery('.poqtysum').text('');

                                amt_final_total > 0 ? jQuery('.amountsum').text(parseFloat(amt_final_total).toFixed(3)) : jQuery('.amountsum').text('');

                                // jQuery('.poqtysum').text(po_final_total);
                                // jQuery('.amountsum').text(amt_final_total);
                            }
                            // } else {
                            //     jAlert("Please At Least Sales Order Detail Item Required");
                            // }

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        jQuery(th).removeClass('file-loader');
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

            } else {
                // if (checkLength > 1) {
                jQuery(th).parents("tr").remove();
                srNo();
                var po_qty = jQuery(th).parents('tr').find('#po_qty').val();
                var po_amt = jQuery(th).parents('tr').find('#amount').val();

                if (po_qty && po_amt) {
                    var po_total = jQuery('.poqtysum').text();
                    var amt_total = jQuery('.amountsum').text();
                    if (po_total != "" && amt_total != "") {
                        po_final_total = parseFloat(po_total) - parseFloat(po_qty);
                        amt_final_total = parseFloat(amt_total) - parseFloat(po_amt);
                    }
                    jQuery('.poqtysum').text(po_final_total);
                    jQuery('.amountsum').text(amt_final_total);
                }
                // } else {
                //     jAlert("Please At Least Sales Order Detail Item Required");
                // }
            }
        }

        setTimeout(() => {

            sumSoQty();
            soRateUnit();
        }, 800)
        // } else {
        //     jAlert("Please At Least Sales Order Detail Item Required");
        // }


    });
}
// old validation of duplicate item
// jQuery(document).on('change', '.item_id', function (e) {
//     var selected = jQuery(this).val();
//     var thisselected = jQuery(this);
//     if (selected) {
//         jQuery(jQuery('.item_id').not(jQuery(this))).each(function (index) {
//             if (thisselected.val() == jQuery(this).val()) {
//                 jAlert('This Item Is Already Selected.');
//                 thisselected.replaceWith(`< select name = "item_id[]" class="chzn-select add_item item_id" onChange = "getItemData
//                 (this)">${productDrpHtml}</select>`);
//             }
//             // jQuery(".chzn-select").chosen();
//         });
//     }
// });


// // new validation of duplicate item as per search in  item
// jQuery(document).on('change', '.item_id', function (e) {
//     var selected = jQuery(this).val(); 
//     var thisSelected = jQuery(this);  

//     if (selected) {
//         var duplicateFound = false; 

//         jQuery('.item_id').not(thisSelected).each(function () {
//             if (thisSelected.val() == jQuery(this).val()) {
//                 duplicateFound = true;  
//                 return false; 
//             }
//         });

//         if (duplicateFound) {
//             jAlert('This Item Is Already Selected.');

//             var selectTd = thisSelected.closest('td');
//             selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id">${productDrpHtml}</select>`);
//             jQuery(".chzn-select").chosen();
//         }
//     }
// });


function sumSoQty(th) {
    var total = 0;
    jQuery('.po_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            total = parseFloat(total) + parseFloat(total1);
        }
    });
    total != 0 && total != "" ? jQuery('.poqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.poqtysum').text('');

    // if (jQuery(th).parents('tr').length > 0) {
    //     soRateUnit(jQuery(th).parents('tr'))
    // }
    soRateUnit(th)
}

function soRateUnit(th) {
    var po_qty = jQuery(th).parents('tr').find("#po_qty").val();
    var rateUnit = jQuery(th).parents('tr').find("#rate_unit").val();
    var discount = jQuery(th).parents('tr').find("#discount").val() != undefined ? jQuery(th).parents('tr').find("#discount").val() : 0;


    if (po_qty != '' && rateUnit != '') {
        if (discount != '') {
            if (discount < 100) {
                rateUnit = parseFloat(rateUnit - (rateUnit * discount / 100)).toFixed(3);
                poUnit = po_qty * rateUnit;
                jQuery(th).parents('tr').find("#amount").val(parseFloat(poUnit).toFixed(3));

            } else {
                toastError('Please Enter Discount Value Less Than 100');
                jQuery(th).parents('tr').find("#discount").val(parseFloat(0).toFixed(2));
                poUnit = parseFloat(po_qty) * parseFloat(rateUnit);
                jQuery(th).parents('tr').find("#amount").val(parseFloat(poUnit).toFixed(3));
            }

        } else {
            jQuery(th).parents('tr').find("#discount").val(parseFloat(0).toFixed(2));
            poUnit = parseFloat(po_qty) * parseFloat(rateUnit);
            jQuery(th).parents('tr').find("#amount").val(parseFloat(poUnit).toFixed(3));
        }

    } else {
        jQuery(th).parents('tr').find("#amount").val('');
    }

    totalAmount();
}



// function soRateUnit(th, po_qty1 = null, rate_per_unit1 = null, discount1 = null) {

//     if (th != null) {

//         if (discount1 != null && rate_per_unit1 != null && po_qty1 != null) {
//             var po_qty = po_qty1;

//             var rateUnit = rate_per_unit1;

//             var discount = discount1;
//         } else {

//             var po_qty = jQuery(th).parents('tr').find("#po_qty").val();

//             var rateUnit = jQuery(th).parents('tr').find("#rate_unit").val();

//             var discount = jQuery(th).parents('tr').find("#discount").val();
//         }
//         discount = parseFloat(discount).toFixed(2);
//         var poUnit = 0;

//         var discountAmt = 0;
//         if (!isNaN(discount)) {

//             if (discount < 100) {

//                 if (rateUnit != "" && po_qty != "") {
//                     poUnit = parseFloat(po_qty) * parseFloat(rateUnit);
//                     discountAmt = parseFloat(poUnit) * parseFloat(discount) / 100;

//                     if (discount != null && discount != "") {
//                         poUnit = parseFloat(poUnit) - parseFloat(discountAmt);
//                     } else {
//                         poUnit = parseFloat(po_qty) * parseFloat(rateUnit);
//                     }
//                 }

//                 if (poUnit != 0) {
//                     jQuery(th).parents('tr').find("#amount").val(formatAmount(poUnit));
//                 } else if (rateUnit == "") {
//                     jQuery(th).parents('tr').find("#amount").val('');

//                 } else {
//                     jQuery(th).parents('tr').find("#amount").val(0);
//                 }

//             } else {
//                 toastError('Please Enter Discount Value Less Than 100');
//                 jQuery(th).parents('tr').find("#discount").val(parseFloat(0).toFixed(2));
//                 poUnit = parseFloat(po_qty) * parseFloat(rateUnit);
//                 jQuery(th).parents('tr').find("#amount").val(formatAmount(poUnit));

//             }
//         }
//         totalAmount();
//     }
// }



function totalAmount() {
    var total_amount = 0;
    jQuery('.amount').map(function () {
        var amount = jQuery(this).val();
        if (amount != "") {
            total_amount = parseFloat(total_amount) + parseFloat(amount);
        }
    });
    if (total_amount != 0) {
        jQuery('.amountsum').text(parseFloat(total_amount).toFixed(3));
    } else if (amount != 0) {
        jQuery('.amountsum').text('');
    } else {
        jQuery('.amountsum').text(0);
    }
}

function getContactPerson() {
    var supplier_id = jQuery('#supplier_id').val();
    jQuery.ajax({
        url: RouteBasePath + "/get-supplier_contact_person?supplier_id=" + supplier_id,
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jQuery('#person').val(data.contact_person.contact_person);
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#po_no').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}

async function getItemsfromMapping(th) {
    return new Promise((resolve, reject) => {
        var supplier_id = th;
        if (supplier_id != "") {
            jQuery.ajax({
                url: RouteBasePath + "/get-items_from_supplier_mapping?supplier_id=" + supplier_id,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        jQuery('#purchasetable tbody').empty();
                        if (formId == undefined) {
                            addPartDetail();
                        } else {
                            setTimeout(() => {
                                let checkLength = jQuery("#purchasetable tbody tr").filter(function () {
                                    return jQuery(this).css('display') !== 'none';
                                }).length;

                                if (checkLength < 1) {
                                    // addPartDetail();
                                }
                            }, 600);

                        }
                        if (data.mappedItems.length > 0) {
                            productDrpHtml = `<option value="">Select Item</option>`;
                            var item_id = ``;
                            for (let indx in data.mappedItems) {
                                productDrpHtml += `<option value="${data.mappedItems[indx].id}">${data.mappedItems[indx].item_name} </option>`;
                                //jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
                                item_id += `data-rate="${data.mappedItems[indx].id}" `;
                            }
                            // checkPODate();
                        } else {
                            productDrpHtml = `<option value="">Select Item</option>`;
                        }
                        jQuery('.item_id').chosen();
                        jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
                        resolve();  // Resolve promise when finished
                    } else {
                        productDrpHtml = `<option value="">Select Item</option>`;
                        jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
                    }
                },
            });
        } else {
            productDrpHtml = `<option value="">Select Item</option>`;
            jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
        }
    });
}
// validation for quantity field
// jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
//     return this.optional(element) || parseInt(value) > 0;
// });

jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) > 0;
    // return this.optional(element) || parseFloat(value) >= parseFloat(param);
});

// validation for rate
jQuery.validator.addMethod("salesRate", function (value, element, param) {
    //formatPoints(element, 3); // Format the value before validation
    //return this.optional(element) || parseFloat(value) >= parseFloat(param);
    return this.optional(element) || parseFloat(value) > 0;
}, "Please Enter Rate/Unit Greater Than 0.00");



// store and update purchase order and purchase order details
var validator = jQuery("#PurchaseOrderForm").validate({
    onclick: false,
    rules: {

        po_sequence: {
            required: true
        },
        supplier_id: {
            required: true
        },
        po_date: {
            required: true,
            date_check: true,
            dateFormat: true
        },
        ref_date: {
            dateFormat: true
        },
        ship_to: {
            required: true,
        },
        ref_date: {
            required: function (e) {
                if (jQuery('#PurchaseOrderForm').find('#ref_no').val() != "") {
                    return true;
                } else {
                    return false;
                }
            },
            date_check: true,
            dateFormat: true
        },
        ref_no: {
            required: function (e) {
                if (jQuery('#PurchaseOrderForm').find('#ref_date').val() != "") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        // person: {
        //     required: true
        // },
        'item_id[]': {
            required: true
        },
        'del_date[]': {
            required: true,
            dateFormat: true
        },
        'po_qty[]': {
            required: true,
            notOnlyZero: '0.001',
        },
        'rate_unit[]': {
            required: true,
            salesRate: '0.01',
        },

        // 'item_id[]': {
        //     required: function (e) {
        //         var selectedValue = jQuery("#PurchaseOrderForm").find('#supplier_id').val();
        //         var value = jQuery("#PurchaseOrderForm").find('#item_id').val();
        //         if (selectedValue != "" && value == "") {
        //             jQuery(e).addClass('error');
        //             jQuery(e).focus();
        //             return true;
        //         } else {
        //             jQuery(e).removeClass('error');
        //             return false;
        //         }
        //     },
        // },
        // 'del_date[]': {
        //     required: function (e) {
        //         if (jQuery("#PurchaseOrderForm").find('input[name="po_qty[]"]').val() != "" && jQuery("#PurchaseOrderForm").find('input[name="del_date[]"]').val() == "") {
        //             jQuery(e).addClass('error');
        //             jQuery("#popup_ok").click(function () {
        //                 setTimeout(() => {
        //                     jQuery(e).focus();
        //                 }, 100);
        //             });
        //             return true;
        //         } else {
        //             jQuery(e).removeClass('error');
        //             return false;

        //         }
        //     },
        //     dateFormat: true
        // },
        // 'po_qty[]': {
        //     required: function (e) {
        //         if (jQuery("#PurchaseOrderForm").find('input[name="item_id[]"]').val() != "" && jQuery("#PurchaseOrderForm").find('input[name="po_qty[]"]').val() == "") {
        //             jQuery(e).addClass('error');
        //             setTimeout(() => {
        //                 jQuery(e).focus();
        //             }, 1000);
        //             return true;
        //         } else {
        //             jQuery(e).removeClass('error');
        //         }
        //     },
        //     notOnlyZero: '0.001',
        // },
        // 'rate_unit[]': {
        //     required: function (e) {
        //         if (jQuery("#PurchaseOrderForm").find('input[name="po_qty[]"]').val() != "" && jQuery("#PurchaseOrderForm").find('input[name="del_date[]"]').val() != "" && jQuery("#PurchaseOrderForm").find('input[name="rate_unit[]"]').val() == "") {
        //             jQuery(e).addClass('error');
        //             setTimeout(() => {
        //                 jQuery(e).focus();
        //             }, 1000);
        //             return true;
        //         } else {
        //             jQuery(e).removeClass('error');
        //         }
        //     },
        //     salesRate: '0.01',
        // },
    },

    messages: {

        po_sequence: {
            required: "Please Enter PO No."
        },
        supplier_id: {
            required: "Please Select Supplier"
        },
        po_date: {
            required: "Please Enter PO Date.",
        },
        // person: {
        //     required: "Please Enter Person",
        // },
        ref_date: {
            required: "Please Enter Ref. Date",
        },
        ref_no: {
            required: "Please Enter Ref. No.",
        },
        ship_to: {
            required: "Please Select Ship To.",
        },
        'item_id[]': {
            required: "Please Select Item"
        },
        'del_date[]': {
            required: "Please Enter Delivery Date"
        },
        'po_qty[]':
        {
            required: "Please Enter PO Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.000.'
        },
        'rate_unit[]':
        {
            required: "Please Enter Rate Per Unit.",
            salesRate: 'Please Enter A Value Greater Than 0.00.'
        },

    },
    errorPlacement: function (error, element) {
        jAlert(error.text());
        return false;
    },

    // },

    submitHandler: function (form) {


        // check table length 

        let checkLength = jQuery("#purchasetable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        if (checkLength < 1) {
            jAlert("Please Add At Least One Purchase Order Detail.");

            // addPartDetail();
            return false;
        }


        let poDate = jQuery("#po_date").val();
        let errorEncountered = true;

        let delDate = '';
        jQuery('#purchasetable tbody tr').each(function (indx, td) {

            delDate = jQuery(td).find('input[name="del_date[]"]').val();
            if (delDate) {
                // Split both dates in dd/mm/yyyy format
                var poDateArray = poDate.split('/');
                var delDateArray = delDate.split('/');

                var poDateObj = new Date(poDateArray[2], poDateArray[1] - 1, poDateArray[0]);
                var delDateObj = new Date(delDateArray[2], delDateArray[1] - 1, delDateArray[0]);

                if (poDateObj > delDateObj) {
                    toastError("Delivery Date Less Then to PO Date");
                    jQuery(td).find('input[name="del_date[]"]').addClass('error');
                    jQuery("#popup_ok").click(function () {
                        setTimeout(() => {
                            jQuery(td).find('input[name="del_date[]"]').focus();
                        }, 100);
                    });
                    errorEncountered = false;
                }
            }
        });


        if (errorEncountered == false) {
            return false;
        }

        jQuery('#purchase_button').prop('disabled', true);

        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-purchase_order" : RouteBasePath + "/store-purchase_order";
        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: jQuery('#PurchaseOrderForm').serialize(),
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (formId != null && formId != "") {
                        toastSuccess(data.response_message, nextFn);
                        function nextFn() {
                            window.location.href = RouteBasePath + "/manage-purchase_order";
                        }
                        //   jQuery('#purchase_button').prop('disabled',false);
                        // toastPreview(data.response_message, redirectFn, prePO);
                        // function redirectFn() {
                        //     window.location.href = RouteBasePath + "/manage-purchase_order";
                        // };
                        // function prePO() {
                        //     id = btoa(data.id);
                        //     window.location.reload();
                        //     // window.location.href = RouteBasePath + "/preview-puchase_order/" + id;
                        // }
                    } else {

                        toastSuccess(data.response_message, nextFn);

                        function nextFn() {
                            window.location.reload();
                        }
                        //  jQuery('#purchase_button').prop('disabled',false);
                        // toastSuccess(data.response_message, redirectFn);
                        // toastPreview(data.response_message, redirectFn, prePO);
                        // function redirectFn() {
                        //     window.location.reload();
                        // }
                        // function prePO() {
                        //     id = btoa(data.id);
                        //     window.location.reload();
                        //     // window.location.href = RouteBasePath + "/preview-puchase_order/" + id;
                        // }
                        jQuery('#purchase_button').prop('disabled', false);
                    }
                } else {
                    jQuery('#purchase_button').prop('disabled', false);
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);
                if (errMessage.errors) {
                    jQuery('#purchase_button').prop('disabled', false);
                    validator.showErrors(errMessage.errors);
                } else if (jqXHR.status == 401) {
                    jQuery('#purchase_button').prop('disabled', false);
                    toastError(jqXHR.statusText);
                } else {
                    jQuery('#purchase_button').prop('disabled', false);
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }

            }
        });
    }
});
// end store and update

// get Last Supplier Details
function getLastSupplierDetails() {
    s_id = jQuery('#supplier_id').val();
    jQuery.ajax({
        url: RouteBasePath + "/get-last_supplier_details?supplier_id=" + s_id,
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                if (data.last_data != null) {
                    // jQuery('#pf_charge').val(data.last_data.pf_charge);
                    // jQuery('#freight').val(data.last_data.frieght).trigger('liszt:updated');
                    // jQuery('#gst').val(data.last_data.gst);
                    // jQuery('#test_certificate').val(data.last_data.test_certificate);
                    // jQuery('#order_acceptance').val(data.last_data.order_acceptance);
                    // jQuery('#payment_terms').val(data.last_data.payment_terms);
                    // jQuery('#sp_notes').val(data.last_data.special_notes);
                }
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#po_no').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });

}



jQuery('#po_sequence').on('change', function () {
    let val = jQuery(this).val();


    var subBtn = jQuery(document).find('.stdform').find('.formwrapper button').text();



    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.formwrapper button');
    }


    if (val != undefined) {

        if (val > 0 == false) {
            jAlert('Please Enter Valid PO. No.');
            jQuery('#po_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    // jQuery('#po_sequence').focus();
                    jQuery("#supplier_id").trigger('liszt:activate');
                }, 1000);
            });
            jQuery('#po_sequence').val('');

        } else {
            jQuery(subBtn).prop('disabled', true);


            jQuery('#po_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-po_no_duplication?for=add&po_sequence=" + val;

            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-po_no_duplication?for=edit&po_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#po_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {

                        toastError(data.response_message);
                        jQuery('#po_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                // jQuery('#po_sequence').focus();
                                jQuery("#supplier_id").trigger('liszt:activate');
                            }, 1000);
                        });
                        jQuery('#po_sequence').val('');

                    } else {
                        jQuery('#po_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#po_no').val(data.latest_po_no);
                        jQuery('#po_sequence').val(val);
                    }
                    jQuery(subBtn).prop('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#po_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#po_no').val('');
        jQuery('#po_sequence').val('');
    }
});

function getItemRateSupplierWise(th) {
    var details_id = jQuery(th).closest('tr').prev('tr').find('input[name="purchase_order_detail_id[]"]').val();


    if (details_id == 0) {

        let item = th.value;
        var supplier_id = jQuery("#supplier_id").val();

        if (supplier_id != "" && supplier_id != null && item != "" && item != null) {
            jQuery.ajax({
                url: RouteBasePath + "/get-Item_rate?item=" + item + "&supplier_id=" + supplier_id,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.last_details != null) {

                            if (data.last_details.rate_per_unit != null || data.last_details.rate_per_unit != "") {

                                jQuery(th).parents('tr').find("#rate_unit").val(data.last_details.rate_per_unit.toFixed(3));
                            } else {
                                jQuery(th).parents('tr').find("#rate_unit").val('');
                            }
                        } else {
                            jQuery(th).parents('tr').find("#rate_unit").val('');

                        }
                    } else {
                        jQuery('#rate_unit').val('');
                    }
                },
            });
        }
    }
}


// function changeCheckDate(item) {
//     jQuery(document).on("change", "#check_date", function (e) {
//         if (formId == undefined && formId == null) {
//             console.log('sdgfdf')
//             checkPODate(item);
//         }
//     });
// }

// function checkPODate(item = null, po_date = null) {

//     let date = jQuery("#PurchaseOrderForm").find("#check_date").val();
//     let datecheck = jQuery("#PurchaseOrderForm").find("#check_date").datepicker('getDate')

//     if (datecheck != null && datecheck != undefined && po_date != null) {
//         if (po_date >= datecheck) {
//             jQuery("#PurchaseOrderForm").find("#check_date").val('');
//             jQuery("#purchasetable tbody tr:odd").each(function () {
//                 jQuery(this).find("input[name='del_date[]']").val('');
//             });
//         }
//     }

//     if (date != undefined && date != null && date != "") {

//         if (item != null && item != undefined && item != "") {
//             jQuery("#purchasetable tbody tr:odd").each(function () {
//                 jQuery(this).find("input[name='del_date[]']").val(date);
//             });
//         }
//     }

// }



jQuery(document).on("change", "#check_date", function (e) {
    if (formId == undefined && formId == null) {
        let date = jQuery("#PurchaseOrderForm").find("#check_date").val();
        let datecheck = jQuery("#PurchaseOrderForm").find("#check_date").datepicker('getDate')

        if (datecheck != null && datecheck != undefined && po_date != null) {
            if (po_date >= datecheck) {
                jQuery("#PurchaseOrderForm").find("#check_date").val('');
                jQuery("#purchasetable tbody tr").each(function () {
                    jQuery(this).find("input[name='del_date[]']").val('');
                });
            }
        }

        if (date != undefined && date != null && date != "") {
            jQuery("#purchasetable tbody tr").each(function () {
                jQuery(this).find("input[name='del_date[]']").val(date);
            });
        }
    }
});


function checkRateUnitPrice(value) {
    let valueInput = jQuery(value).val();
    if (valueInput <= 0.00) {
        jAlert("Please Enter Rate/Unit Greater Than 0.00 ");
        focusInput_second(value);
        jQuery(value).addClass('error');
    } else {
        jQuery(value).removeClass('error');
    }
}

function suggestOrderBy(e, $this) {
    var keyevent = e
    if (keyevent.key != "Tab") {
        jQuery("#order_by").addClass('file-loader');
        var search = jQuery($this).val();

        jQuery.ajax({
            url: RouteBasePath + "/ordre_by-list?term=" + encodeURI(search),
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {

                jQuery("#order_by").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#order_by_list').html(data.orderByList);
                } else {
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery("#order_by").removeClass('file-loader');
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

function suggestPreparedBy(e, $this) {
    var keyevent = e
    if (keyevent.key != "Tab") {
        jQuery("#prepared_by").addClass('file-loader');
        var search = jQuery($this).val();

        jQuery.ajax({
            url: RouteBasePath + "/prepared_by-list?term=" + encodeURI(search),
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {

                jQuery("#prepared_by").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#prepared_by_list').html(data.preparedByList);
                } else {
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery("#prepared_by").removeClass('file-loader');
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





// jQuery(document).on('change', '.del_date', (function (e) {
//     var del_date = e.target.value;
//     var po_date = jQuery('#po_date').val();
//     if (del_date < po_date) {
//         //jAlert('Delivery Date Must Be Greater Than Or Equal To PO Date.');
//         jQuery(".date-picker").datepicker({
//             dateFormat: "dd/mm/yy",
//             minDate: po_date
//         }).datepicker("setDate", po_date);
//     //
//     //     jQuery(".date-picker:not([readonly])").datepicker({
//     //         dateFormat: "dd/mm/yy",
//     //         minDate: from_date
//     // });
//     // jQuery('.date-picker').datepicker('destroy');
//    // jQuery('.date-picker').datepicker('option', { minDate: new Date(po_date) });
//         jQuery("#popup_ok").click(function(){
//             setTimeout(()=>{
//                 jQuery('.del_date').focus();
//             },100);
//         });
//         jQuery('.del_date').val('');
//         jQuery('.del_date').addClass('error');
//      } else {
//          jQuery('.del_date').removeClass('error');
//      }
// }));

// jQuery(document).ready(function() {
//     jQuery(".date-picker:not([readonly])").datepicker({
//         dateFormat: "dd/mm/yy"
//     });
// });

// jQuery(document).on('change', '.del_date', function (e) {
//     var del_date = e.target.value;
//     var po_date = jQuery('#po_date').val();

//     var delDateParts = del_date.split('/');
//     var poDateParts = po_date.split('/');

//     var formattedDelDate = new Date(delDateParts[2], delDateParts[1] - 1, delDateParts[0]);
//     var formattedPoDate = new Date(poDateParts[2], poDateParts[1] - 1, poDateParts[0]);

//     if (formattedDelDate < formattedPoDate) {
//         jQuery(".date-picker:not([readonly])").datepicker("destroy").datepicker({
//             dateFormat: "dd/mm/yy",
//             minDate: formattedPoDate
//         }).datepicker("setDate", formattedPoDate);
//     }
//     else {
//         jQuery(".date-picker:not([readonly])").datepicker("destroy").datepicker({
//             dateFormat: "dd/mm/yy"
//         });
//     }
// });




function getSupplier() {
    jQuery.ajax({
        url: RouteBasePath + "/get-pr_supplier_for_po",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            let suppHtml = '';
            suppHtml += `<option value="">Select Supplier</option> `;
            if (data.response_code == 1) {
                for (let indx in data.get_pr_supplier) {
                    suppHtml += `<option value="${data.get_pr_supplier[indx].id}">${data.get_pr_supplier[indx].supplier_name}</option>`;

                }
                jQuery('#supplier_id').empty().append(suppHtml).trigger('liszt:updated')

            } else {
                console.log(data.response_message)
            }
        },
    });
}


function fillPendingPr() {

    let supId = jQuery('#supplier_id option:selected').val();

    var thisModal = jQuery('#pendingPrModal');
    var thisForm = jQuery('#PurchaseOrderForm');

    if (supId != "") {
        if (formId == undefined) {
            var Url = RouteBasePath + "/get-pr_list-po?po_supplier_id=" + supId;
        } else {
            var Url = RouteBasePath + "/get-pr_list-po?po_supplier_id=" + supId + "&id=" + formId;
        }

        jQuery.ajax({
            url: Url,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    if (formId == undefined) {
                        jQuery('#person').val(data.contact_person.contact_person);
                    }
                    // new code
                    var usedParts = [];
                    var totalDisb = 0;
                    var found = 0;

                    thisForm.find('#purchasetable tbody input[name="form_indx"]').each(function (indx) {
                        let frmIndx = jQuery(this).val();

                        let jbEorkOrderId = pr_data[frmIndx].pr_id;
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

                    if (data.pr_data.length > 0 && !jQuery.isEmptyObject(data.pr_data)) {
                        found = 1;

                        for (let idx in data.pr_data) {
                            var inUse = isUsed(data.pr_data[idx].pr_id);

                            if (!jQuery.isEmptyObject(pr_data[idx]) && pr_data[idx].pr_id == data.pr_data[idx].pr_id) {
                                var grnUse = pr_data[idx].in_use ? pr_data[idx].in_use == true ? 'in_use' : '' : '';

                                var checkField = pr_data[idx].in_use ? pr_data[idx].in_use == true ? 'readonly' : '' : '';
                            } else {
                                var grnUse = '';
                                var checkField = '';
                            }

                            totalEntry++;
                            tblHtml += `<tr>
                                        <td><input type="checkbox" name="pr_id[]" class="simple-check ${grnUse}" id="pr_ids_${data.pr_data[idx].pr_id}" 
                                        value="${data.pr_data[idx].pr_id}" ${inUse ? 'checked' : ''} ${checkField}onchange="getPrData()"/></td>
                                        <td>${data.pr_data[idx].pr_number}</td>
                                        <td>${data.pr_data[idx].pr_date}</td>                                                                    
                                        <td>${data.pr_data[idx].pr_form_value_fix == 'from_location' ? 'From Location' : 'Manual'}</td>
                                        <td>${data.pr_data[idx].location_name != null ? data.pr_data[idx].location_name : ""}</td>                           
                                        </tr>`;

                        }

                    } else {

                        tblHtml += `<tr class="centeralign" id="noPendingPo">
                            <td colspan="5">No Pending PR Available</td>
                        </tr>`;

                    }

                    jQuery('#pendingPRDataTable tbody').empty().append(tblHtml);

                    if (formId == undefined) {
                        jQuery("#addPendingPRDataForm").find("[id^='pr_ids_']").each(function () {
                            var chkCounts = 0;
                            if (jQuery(this).is(':checked')) {
                                chkCounts++;
                            }

                            if (chkCounts == 0) {
                                var tblitemHtml = '';
                                tblitemHtml += `<tr class="centeralign" id="noPendingPo">
                                <td colspan="5">No Pending PO Available</td>
                                </tr>`;
                                jQuery('#pendingPRTable tbody').empty().append(tblitemHtml);
                            }
                        });
                    }


                    if (formId == undefined) {
                        jQuery('.toggleModalBtn').prop('disabled', false);
                    } else {
                        if (btn_disabled) {
                            jQuery('.toggleModalBtn').prop('disabled', true);
                        } else {
                            jQuery('.toggleModalBtn').prop('disabled', false);
                        }
                    }


                } else {
                    jQuery('.toggleModalBtn').prop('disabled', true);
                    toastError(data.response_message);
                }
            },

        });

    } else {
        jQuery('.toggleModalBtn').prop('disabled', true);
    }
}


function getPrData() {
    var chkPRId = [];
    jQuery("#addPendingPRDataForm").find("[id^='pr_ids_']").each(function () {
        var thisId = jQuery(this).attr('id');
        var splt = thisId.split('pr_ids_');
        var intId = splt[1];

        if (jQuery(this).is(':checked')) {
            chkPRId.push(jQuery(this).val())
        }

    });

    if (chkPRId.length > 0) {
        let supId = jQuery('#supplier_id option:selected').val();

        var thisModal = jQuery('#pendingPrModal');
        var thisForm = jQuery('#PurchaseOrderForm');

        if (supId != "") {
            if (formId == undefined) {
                var Url = RouteBasePath + "/get-pr_item_list-po?supplier_id=" + supId + "&chkPRId=" + chkPRId.join(',');
            } else {
                var Url = RouteBasePath + "/get-pr_item_list-po?supplier_id=" + supId + "&id=" + formId + "&chkPRId=" + chkPRId.join(',');
            }

            jQuery.ajax({
                url: Url,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1 && data.pr_data.length > 0) {


                        // new code
                        var usedParts = [];
                        var totalDisb = 0;
                        var found = 0;

                        thisForm.find('#purchasetable tbody input[name="form_indx"]').each(function (indx) {
                            let frmIndx = jQuery(this).val();

                            let jbEorkOrderId = pr_data[frmIndx].pr_details_id;
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

                        function isItemUsedcheck(pjId) {
                            if (check_pr_data.includes(Number(pjId))) {
                                return true;
                            }
                            return false;
                        }

                        let totalEntry = 0;
                        var tblitemHtml = ``;
                        var found = 0;

                        // end new code
                        // <td>${data.po_data[idx].pend_po_qty != null ? data.po_data[idx].po_qty >= data.po_data[idx].pend_po_qty ? parseFloat(data.po_data[idx].pend_po_qty).toFixed(3) : parseFloat(0).toFixed(3) : parseFloat(0).toFixed(3)}</td>          
                        if (data.pr_data.length > 0 && !jQuery.isEmptyObject(data.pr_data)) {
                            found = 1;

                            for (let idx in data.pr_data) {
                                var inUse = isUsed(data.pr_data[idx].pr_details_id);
                                var isItemUsedchecked = isItemUsedcheck(data.pr_data[idx].pr_details_id);

                                if (!jQuery.isEmptyObject(pr_data[idx]) && pr_data[idx].pr_details_id == data.pr_data[idx].pr_details_id) {
                                    var grnUse = pr_data[idx].in_use ? pr_data[idx].in_use == true ? 'in_use' : '' : '';
                                    var checkField = pr_data[idx].in_use ? pr_data[idx].in_use == true ? 'readonly' : '' : '';
                                } else {
                                    var grnUse = '';
                                    var checkField = '';
                                }
                                totalEntry++;
                                tblitemHtml += `<tr>
                                        <td><input type="checkbox" name="pr_details_id[]" class="simple-check ${grnUse}" id="pr_details_ids_${data.pr_data[idx].pr_details_id}" 
                                        value="${data.pr_data[idx].pr_details_id}" ${inUse ? 'checked' : isItemUsedchecked ? '' : 'checked'} ${checkField}/></td>                                                 
                                        <td>${data.pr_data[idx].item_name}</td>
                                        <td>${data.pr_data[idx].item_code}</td>
                                        <td>${data.pr_data[idx].po_qty != null ? parseFloat(data.pr_data[idx].po_qty).toFixed(3) : ""}</td>
                                        <td>${data.pr_data[idx].unit_name}</td>
                                        </tr>`;
                            }

                        } else {
                            console.log('else')
                            tblitemHtml += `<tr class="centeralign" id="noPendingPo">
                                <td colspan="5">No Pending PO Available</td>
                            </tr>`;

                        }

                        jQuery('#pendingPRTable tbody').empty().append(tblitemHtml);


                    } else {
                        var tblitemHtml = '';
                        tblitemHtml += `<tr class="centeralign" id="noPendingPo">
                        <td colspan="5">No Pending PO Available</td>
                        </tr>`;
                        jQuery('#pendingPRTable tbody').empty().append(tblitemHtml);
                        // toastError(data.response_message);
                    }
                },


            });

        }
    } else {

        var tblitemHtml = `<tr class="centeralign" id="noPendingPo">
                <td colspan="5">No record found!</td>
            </tr>`;

        jQuery('#pendingPRTable tbody').empty().append(tblitemHtml);
    }


}


var coaPartValidator = jQuery("#addPendingPRForm").validate({
    rules: {
        "pr_details_id[]": {
            required: true
        },
    },
    messages: {
        "pr_details_id[]": {
            required: "Please Select Item From Pending PR",
        }
    },

    submitHandler: function (form) {
        var chkCount = 0;
        var chkArr = [];
        var chkId = [];

        jQuery("#addPendingPRForm").find("[id^='pr_details_ids_']").each(function () {
            var thisId = jQuery(this).attr('id');
            var splt = thisId.split('pr_details_ids_');
            var intId = splt[1];

            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });

        if (chkCount == 0) {
            toastError('Please Select Item From Pending PR');
        } else {
            if (formId == undefined) {
                var url = RouteBasePath + "/get-pr_part_data-po?pr_ids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-pr_part_data-po?pr_ids=" + chkArr.join(',') + "&id=" + formId;
            }

            jQuery.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (formId == undefined) {
                            if (data.orderBy.prepared_by != null) {
                                jQuery('#order_by').val(data.orderBy.prepared_by);
                            } else {
                                jQuery('#order_by').val('');
                            }
                        }
                        if (data.pr_data.length > 0 && !jQuery.isEmptyObject(data.pr_data)) {
                            pr_data = [];
                            for (let ind in data.pr_data) {
                                pr_data.push(data.pr_data[ind]);
                                if (data.pr_data[ind].to_location_id != 0 && data.pr_data[ind].to_location_id != null) {
                                    jQuery('#ship_to').val(data.pr_data[ind].to_location_id).trigger('liszt:updated');
                                }
                            }

                            fillPOTable(data.pr_data);
                        }

                        jQuery('#supplier_id').trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                        jQuery("#pendingPrModal").modal('hide');

                    } else {
                        toastError(data.response_message);
                        jQuery('#supplier_id').trigger('liszt:updated').prop({ tabindex: 1 }).attr('readonly', false);
                    }

                },
            });
        }
    }
});




//<--On Work Order Modal Show-->//
jQuery('#pendingPrModal').on('show.bs.modal', function (e) {


    var usedItemParts = [];
    var totalItemDisb = 0;

    jQuery('#purchasetable tbody input[name="form_indx"]').each(function (indx) {
        var frmIndx = jQuery(this).val();
        var prItemId = pr_data[frmIndx].pr_id;
        if (prItemId != "" && prItemId != null) {
            usedItemParts.push(Number(prItemId));
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
    jQuery('#pendingPRDataTable tbody tr').each(function (indx) {

        totalEntry++;
        var checkField = jQuery(this).find('input[name="pr_id[]"]');
        var partId = jQuery(checkField).val();
        var inUse = isItemUsed(partId);

        if (inUse) {
            // jQuery(checkField).addClass('in-use').prop('checked', true);
            jQuery(checkField).prop('checked', true);
        } else {
            // jQuery(checkField).removeClass('in-use').prop('checked', false);
            jQuery(checkField).prop('checked', false);
        }

    });






    var usedParts = [];
    var totalDisb = 0;

    jQuery('#purchasetable tbody input[name="form_indx"]').each(function (indx) {
        var frmIndx = jQuery(this).val();
        var prdId = pr_data[frmIndx].pr_details_id;
        if (prdId != "" && prdId != null) {
            usedParts.push(Number(prdId));
        }
    });

    function isUsed(pjId) {
        if (usedParts.includes(Number(pjId))) {
            totalDisb++;
            return true;
        }
        return false;
    }

    var totalEntry = 0;
    jQuery('#pendingPRTable tbody tr').each(function (indx) {

        totalEntry++;
        var checkField = jQuery(this).find('input[name="pr_details_id[]"]');
        var partId = jQuery(checkField).val();
        var inUse = isUsed(partId);

        if (inUse) {
            // jQuery(checkField).addClass('in-use').prop('checked', true);
            jQuery(checkField).prop('checked', true);

        } else {
            // jQuery(checkField).removeClass('in-use').prop('checked', false);
            jQuery(checkField).prop('checked', false);
        }

    });



});


jQuery('#checkall-pr_data').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#pendingPRDataTable").find("[id^='pr_ids_']:not(.in_use)").prop('checked', true).trigger('change');
    } else {
        jQuery("#pendingPRDataTable").find("[id^='pr_ids_']:not(.in_use)").prop('checked', false).trigger('change');
    }
});



jQuery('#checkall-pr').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#pendingPRTable").find("[id^='pr_details_ids_']:not(.in_use)").prop('checked', true).trigger('change');
    } else {
        jQuery("#pendingPRTable").find("[id^='pr_details_ids_']:not(.in_use)").prop('checked', false).trigger('change');
    }
});


