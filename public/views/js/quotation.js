
var quot_data = [];
var material_data = [];
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();

var quotIsUse = false;


// we will display the date as DD-MM-YYYY

let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

var formId = jQuery('#quotationform').find('input:hidden[name="id"]').val();

if (formId !== undefined) { //if form is edit

    jQuery(document).ready(function () {

        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({

            url: RouteBasePath + "/get-quotation/" + formId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    jQuery('#sup_rejection_button').prop('disabled', true);

                    jQuery('#quot_sequence').val(data.quot_data.quot_sequence).prop({ tabindex: -1, readonly: true });

                    jQuery('#quot_no').val(data.quot_data.quot_number).prop({ tabindex: -1, readonly: true });

                    jQuery('#quot_date').val(data.quot_data.quot_date);

                    setTimeout(() => {
                        // jQuery("#quot_date").focus();
                        jQuery("#customer_group_id").trigger('liszt:activate');
                    }, 100);

                    jQuery('#customer_reg_no').val(data.quot_data.customer_reg_no);

                    jQuery('#customer_name').val(data.quot_data.customer_name);

                    jQuery('#dealer_id').val(data.quot_data.dealer_id).trigger('liszt:updated');

                    jQuery('#quotationform').find('#pincode').val(data.quot_data.pincode);

                    jQuery('#quot_mobile_no').val(data.quot_data.mobile_no);

                    jQuery('#quot_country_id').val(data.quot_data.country_id).trigger('liszt:updated');

                    jQuery('#mis_category_id').val(data.quot_data.mis_category_id).trigger('liszt:updated');

                    getQuotStates();

                    loadQuotationData(data);

                    jQuery('#special_notes').val(data.quot_data.special_notes);
                    jQuery('.quotqtysum').val(data.quot_qty);
                    jQuery('.quotqtysum_second').val(data.quotqtysum_second != null ? parseFloat(data.quotqtysum_second).toFixed(3) : "");
                    jQuery('.amountsum').val(data.quot_amount);

                    /* GST Fill Data */
                    jQuery('#basic_amount').val(data.quot_data.basic_amount != null ? data.quot_data.basic_amount.toFixed(2) : null);

                    jQuery('#less_discount_percentage').val(data.quot_data.less_discount_percentage != null ? data.quot_data.less_discount_percentage.toFixed(2) : null);
                    jQuery('#less_discount_amount').val(data.quot_data.less_discount_amount != null ? data.quot_data.less_discount_amount.toFixed(2) : null);

                    jQuery('#secondary_transport').val(data.quot_data.secondary_transport != null ? data.quot_data.secondary_transport.toFixed(2) : null);

                    jQuery('input:radio[name="gst_type_fix_id"][value="' + data.quot_data.gst_type_fix_id + '"]').attr('checked', true).trigger('click');
                    jQuery('#sgst_percentage').val(data.quot_data.sgst_percentage != null ? data.quot_data.sgst_percentage.toFixed(2) : null);
                    jQuery('#sgst_amount').val(data.quot_data.sgst_amount != null ? data.quot_data.sgst_amount.toFixed(2) : null);
                    jQuery('#cgst_percentage').val(data.quot_data.cgst_percentage != null ? data.quot_data.cgst_percentage.toFixed(2) : null);
                    jQuery('#cgst_amount').val(data.quot_data.cgst_amount != null ? data.quot_data.cgst_amount.toFixed(2) : null);
                    jQuery('#igst_percentage').val(data.quot_data.igst_percentage != null ? data.quot_data.igst_percentage.toFixed(2) : null);
                    jQuery('#igst_amount').val(data.quot_data.igst_amount != null ? data.quot_data.igst_amount.toFixed(2) : null);


                    jQuery('#round_off').val(data.quot_data.round_off_val != null ? data.quot_data.round_off_val.toFixed(2) : null);

                    if (data.quot_data.country_id != null) {
                        getQuotStates().done(function (resposne) {
                            jQuery('#quot_state_id').val(data.quot_data.state_id).trigger('liszt:updated');
                            getQuotDistrict().done(function (resposne) {
                                jQuery('#quot_district_id').val(data.quot_data.district_id).trigger('liszt:updated');
                                getQuotTaluka().done(function (resposne) {
                                    jQuery('#quot_taluka_id').val(data.quot_data.quot_taluka_id).trigger('liszt:updated');
                                    getQuotVillage().done(function (resposne) {
                                        jQuery('#quot_village_id').val(data.quot_data.quot_village_id).trigger('liszt:updated');
                                    });
                                });

                            });
                        });
                    }

                    if (data.quot_data.in_use == true) {
                        jQuery('#quot_sequence').prop({ tabindex: -1, readonly: true });
                        jQuery('#quot_date').prop({ tabindex: -1, readonly: true });
                        jQuery('#customer_group_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#customer_name').prop({ tabindex: -1, readonly: true });
                        jQuery('#dealer_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#customer_reg_no').prop({ tabindex: -1, readonly: true });
                        jQuery('#quot_village_id').prop({ tabindex: -1, readonly: true });
                        jQuery('#customer_pincode').prop({ tabindex: -1, readonly: true });
                        jQuery('#quot_country_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#quot_state_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#quot_district_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#quot_taluka_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#quot_village_id').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#quot_mobile_no').prop({ tabindex: -1 }).attr('readonly', true);

                        jQuery('#special_notes').prop({ tabindex: -1, readonly: true });
                        jQuery('#basic_amount').prop({ tabindex: -1, readonly: true });
                        /*jQuery('#secondary_transport').prop({ tabindex: -1, readonly: true });
                        jQuery('#gst_type_fix_id').prop({ tabindex: -1, readonly: true });
                        jQuery('#sgst_percentage').prop({ tabindex: -1, readonly: true });
                        jQuery('#sgst_amount').prop({ tabindex: -1, readonly: true });
                        jQuery('#cgst_percentage').prop({ tabindex: -1, readonly: true });
                        jQuery('#cgst_amount').prop({ tabindex: -1, readonly: true });
                        jQuery('#igst_percentage').prop({ tabindex: -1, readonly: true });
                        jQuery('#igst_amount').prop({ tabindex: -1, readonly: true });
                        jQuery('#round_off').prop({ tabindex: -1, readonly: true });
                        jQuery('#net_amount').prop({ tabindex: -1, readonly: true });

                        jQuery("input[name*='gst_type_fix_id']").prop({ tabindex: -1 }).attr('readonly', true);*/
                        jQuery('#addPart').prop('disabled', true);
                        jQuery('#replace_btn').prop('disabled', true);
                        quotIsUse = true;
                    }

                    fillQuotTable(data.quot_part_details);
                    jQuery('#sup_rejection_button').prop('disabled', false);
                    manageGstType();
                    jQuery('#net_amount').val(data.quot_data.net_amount != null ? data.quot_data.net_amount.toFixed(2) : data.quot_data.total_amount.toFixed(2));
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

} else { //for Add
    jQuery(document).ready(function () {

        getLatestQuotNo();
        addPartDetail();
        getCountyandStateForLocation();
        manageGstType();
        setTimeout(() => {
            // jQuery("#quot_date").focus();
            jQuery("#customer_group_id").trigger('liszt:activate');
        }, 100);

    });

}


async function loadQuotationData(data) {
    try {
        jQuery('#customer_group_id').val(data.quot_data.customer_group_id).trigger('liszt:updated');
        await getQuotDealer();
        jQuery('#dealer_id').val(data.quot_data.dealer_id).trigger('liszt:updated');
    } catch (error) {
        console.log("Error: ", error);
    }
}
async function loadQuotationRepData(data) {
    try {
        jQuery('#customer_group_id').val(data.sales_order.customer_group_id).trigger('liszt:updated');
        await getQuotDealer();
        jQuery('#dealer_id').val(data.sales_order.dealer_id).trigger('liszt:updated');
    } catch (error) {
        console.log("Error: ", error);
    }
}

// jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
//     return this.optional(element) || parseInt(value) >= 0.01;
// });
jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) >= 0.001;
});

// validation for rate
jQuery.validator.addMethod("salesRate", function (value, element, param) {
    return this.optional(element) || parseFloat(value) > 0.01;
    //return this.optional(element) || parseFloat(value) >= parseFloat(param);
});


var validator = jQuery("#quotationform").validate({
    ignore: [],
    onclick: false,
    // onkeyup: false,
    rules: {

        quot_sequence: {
            required: true
        },
        quot_date: {
            required: true,
            dateFormat: true,
            date_check: true
        },
        customer_group_id: {
            required: true
        },
        customer_name: {
            required: true
        },
        dealer_id: {
            required: true
        },
        quot_village_id: {
            required: true
        },
        quot_country_id: {
            required: true
        },
        quot_state_id: {
            required: true
        },
        quot_district_id: {
            required: true
        },
        quot_taluka_id: {
            required: true
        },
        'item_id[]': {
            required: true
        },

        'quot_qty[]': {
            required: true,
            notOnlyZero: '0.001',
        },
        'rate_unit[]': {
            required: true,
            salesRate: '0.01'
        },
        'amount[]': {
            required: true
        },

        mis_category_id: {
            required: true
        },
        quot_mobile_no: {
            numberFormat: true
        },
    },

    messages: {

        quot_sequence: {
            required: "Please Enter Quot No."
        },
        quot_date: {
            required: "Please Enter Quot Date",
        },
        customer_group_id: {
            required: "Please Select Customer Group"
        },
        dealer_id: {
            required: "Please Select Dealer"
        },
        customer_name: {
            required: "Please Enter Customer Name",
        },
        rep_customer_id: {
            required: "Please Enter Customer Name",
        },
        quot_village_id: {
            required: "Please Select Village"
        },
        quot_country_id: {
            required: "Please Select Country"
        },
        quot_state_id: {
            required: "Please Select State"
        },
        quot_district_id: {
            required: "Please Select District"
        },
        quot_taluka_id: {
            required: "Please Select Taluka"
        },
        'item_id[]': {
            required: "Please Select Item"
        },
        'quot_qty[]': {
            required: "Please Enter Quot. Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.001'
        },
        'rate_unit[]': {
            required: "Please Enter Rate/Unit",
            salesRate: 'Please Enter A Value Greater Than 0.01'
        },
        mis_category_id: {
            required: "Please Select MIS Category"
        },

    },
    errorPlacement: function (error, element) {
        error.insertAfter(element);
        return false;
    },



    submitHandler: function (form) {

        let checkLength = jQuery("#quotPartTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        if (checkLength < 1) {
            jAlert("Please Add At Least One Quotation Detail.");
            return false;
        }

        jQuery('#sup_rejection_button').prop('disabled', true);
        var formUrl = formId !== undefined ? RouteBasePath + "/update-quotation" : RouteBasePath + "/store-quotation";
        let formData = jQuery('#quotationform').serialize();


        let requestData = formData;
        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: requestData,
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (formId !== undefined) {
                        toastSuccess(data.response_message, redirectFn);
                        function redirectFn() {
                            window.location.href = RouteBasePath + "/manage-quotation";
                        };
                    } else {
                        toastSuccess(data.response_message, redirectFn);
                        function redirectFn() {
                            window.location.reload();
                        }
                        jQuery('#sup_rejection_button').prop('disabled', false);
                    }
                } else {
                    jQuery('#sup_rejection_button').prop('disabled', false);
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);
                if (errMessage.errors) {
                    jQuery('#sup_rejection_button').prop('disabled', false);
                    validator.showErrors(errMessage.errors);
                } else if (jqXHR.status == 401) {
                    jQuery('#sup_rejection_button').prop('disabled', false);
                    toastError(jqXHR.statusText);
                } else {

                    jQuery('#sup_rejection_button').prop('disabled', false);
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
});

function fillQuotTable(quot_data) {

    if (quot_data.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in quot_data) {

            var sr_no = counter;
            var quot_details_id = quot_data[key].quot_details_id ? quot_data[key].quot_details_id : "";

            var item_id = quot_data[key].item_id ? quot_data[key].item_id : "";
            var item_code = quot_data[key].item_code ? quot_data[key].item_code : "";
            var quot_qty = quot_data[key].quot_qty ? quot_data[key].quot_qty.toFixed(3) : "";

            var unit_name = quot_data[key].unit_name ? quot_data[key].unit_name : "";
            var rate_per_unit = quot_data[key].rate_per_unit ? parseFloat(quot_data[key].rate_per_unit).toFixed(2) : "";
            var quot_amount = quot_data[key].quot_amount ? quot_data[key].quot_amount : "";
            var in_use = quot_data[key].in_use ? quot_data[key].in_use : "";


            thisHtml += `<tr>

                    <td>
                        <a  ${in_use == true ? '' : 'onclick="removeQuotDetails(this)"'} ><i class="action-icon iconfa-trash so_details "></i></a>
                    </td>        
        
                    <td class="sr_no">${sr_no}</td>
               
                    <td>
                        <input type="hidden" name="quot_details_id[]"  value="${quot_details_id}">
                        <select name="item_id[]" class="chzn-select item_id item_id_${sr_no} so_item_select_width" onChange="getItemData(this), sumQuotQty(this)">${productDrpHtml}</select>
                    </td>

                    <td><input type="text" name="code[]" id="code"   class="form-control salesmanageTable" readonly tabindex="-1" value="${item_code}"/></td>
                  
                    <td><input type="text" name="quot_qty[]" id="quot_qty"  onKeyup="sumQuotQty(this)"  onblur="formatPoints(this,3)" class="form-control isNumberKey quot_qty" style="width:50px;" value="${quot_qty}"/></td>

                    <td><input type="text" name="unit[]" id="unit" class="form-control salesmanageTable" tabindex="-1"  readonly value="${unit_name}" style="width:50px;" tabindex="1" /></td>

                    <td><input type="text" name="rate_unit[]"  onKeyup="quotRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey" onblur="formatPoints(this,2)"/ value="${rate_per_unit}" style="width:60px;"></td>

                    <td><input type="number" name="amount[]" id="amount" class="form-control amount" onblur="formatPoints(this,2)" tabindex="-1" value="${formatAmount(quot_amount)}" readonly tabindex="-1" style="width:70px;"/></td>

                </tr>`;
            counter++;
        }


        jQuery('#quotPartTable tbody').append(thisHtml);

        var counter = 1;
        for (let key in quot_data) {
            var item_id = quot_data[key].item_id ? quot_data[key].item_id : "";

            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            counter++;

        }

        sumQuotQty();
        srNo();
        totalAmount();
        disabledDropdownVal();
    }
}

function getLatestQuotNo() {

    jQuery.ajax({

        url: RouteBasePath + "/get-latest_quotation_no",

        type: 'GET',

        headers: headerOpt,

        dataType: 'json',

        processData: false,

        success: function (data) {

            jQuery('#quot_no').removeClass('file-loader');

            if (data.response_code == 1) {

                jQuery('#quot_no').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });

                jQuery('#quot_sequence').val(data.number).prop({ tabindex: -1, readonly: true });

                jQuery('#quot_date').val(currentDate);

            } else {

                console.log(data.response_message)

            }

        },

        error: function (jqXHR, textStatus, errorThrown) {

            jQuery('#quot_no').removeClass('file-loader');

            console.log('Field To Get Latest Quot No.!')

        }

    });

}

if (getItem.length) {

    var productDrpHtml = `<option value="">Select Item</option>`;
    for (let indx in getItem[0]) {

        productDrpHtml += `<option value="${getItem[0][indx].id}" data-item_code="${getItem[0][indx].item_code}" data-unit_name="${getItem[0][indx].unit_name}">
        ${getItem[0][indx].item_name} </option>`;

    }

}


function addPartDetail() {

    // var sr = sr_no++;



    var thisHtml = `<tr>
                        <td><a onclick="removeQuotDetails(this)"><i class="action-icon iconfa-trash quot_details"></i></a></td>

                        <td class="sr_no"></td>

                        <td>                           
                            <select name="item_id[]"  class="chzn-select  add_item item_id so_item_select_width" onChange="getItemData(this), sumQuotQty(this)">${productDrpHtml}</select>
                        </td>

                        <td> <input type="hidden" name="quot_details_id[]" value="0"><input type="text" name="code[]" id="code"  class="form-control salesmanageTable" tabindex="-1" readonly/></td>

                        <td><input type="text" name="quot_qty[]" id="quot_qty" onblur="formatPoints(this,3)"  onKeyup="sumQuotQty(this)" class="form-control isNumberKey quot_qty " style="width:50px;"/></td>

                        <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control" tabindex="-1" readonly/></td>

                        <td><input type="text" name="rate_unit[]"  onKeyup="quotRateUnit(this)" id="rate_unit" class="form-control rate_unit  isNumberKey"  onblur="formatPoints(this,2)" style="width:60px;"/></td>

                        <td><input type="number" name="amount[]" id="amount" class="form-control amount" onblur="formatPoints(this,2)" tabindex="-1" style="width:70px;" readonly/></td>
                    </tr>`;


    jQuery('#quotPartTable tbody').append(thisHtml);

    setTimeout(() => {
        srNo();
    }, 200);

    sumQuotQty();
    totalAmount();
    disabledDropdownVal();
}





function getItemData(th) {

    // var selectedValue = jQuery(th).val();
    var selected = jQuery(th).val();

    var thisselected = jQuery(th);
    if (selected) {
        jQuery(jQuery('.item_id').not(jQuery(th))).each(function (index) {
            // openModal = "yes";

            if (thisselected.val() == jQuery(this).val()) {
                jAlert('This Item Is Already Selected.');
                var selectTd = thisselected.closest('td');

                selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id so_item_select_width" onChange="getItemData(this), sumQuotQty(this)">${productDrpHtml}</select>`);
                // jQuery('.item_id').chosen();
                jQuery(".item_id").chosen({
                    search_contains: true
                });
            }
        });
    }



    let item = th.value == undefined ? selected : th.value;


    var customerGroup = jQuery('#customer_group_id option:selected').val();

    jQuery(th).parents('tr').find("#code").val(jQuery(th).find('option:selected').data('item_code')); // Enable the input field
    jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit_name'));// Enable the input field
    jQuery(th).parents('tr').find("#item_id").val(item);


    var itemUrl = formId != undefined && formId != '' ? RouteBasePath + "/get-item_data?item=" + item + "&id=" + formId + "&customerGroup=" + customerGroup : RouteBasePath + "/get-item_data?item=" + item + "&customerGroup=" + customerGroup;


    if (item != "" && item != null) {
        jQuery.ajax({


            url: itemUrl,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {
                if (data.response_code == 1) {


                    if (formId == undefined) {
                        if (data.item != '' && data.item != undefined) {
                            if (data.item.sales_rate != '' && data.item.sales_rate != undefined) {
                                jQuery(th).closest('tr').find("#rate_unit").val(data.item.sales_rate);
                            }
                        }
                    }

                } else {
                    jQuery('#code').val('');
                    jQuery('#item_id').val('');
                    jQuery('#group').val('');
                    jQuery('#unit').val('');
                }
            }

        });
    }

}


function sumQuotQty(th) {

    var total = 0;
    var total2 = 0;
    jQuery('.quot_qty').map(function () {

        var total1 = jQuery(this).val();

        if (total1 != "") {
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    jQuery('.quotqtysum_second').map(function () {
        var total4 = jQuery(this).val();
        if (total4 != "") {

            total2 = parseFloat(total2) + parseFloat(total4);
        }
    });

    total != 0 && total != "" ? jQuery('.quotqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.quotqtysum').text('');


    total2 != 0 && total2 != "" ? jQuery('.quotqtysum_second').text(parseFloat(total2).toFixed(3)) : jQuery('.quotqtysum_second').text('');





    if (jQuery(th).closest('tr').length > 0) {
        quotRateUnit(jQuery(th).closest('tr'))
    }
}

function quotRateUnit(th) {

    let quot_qty = jQuery(th).closest('tr').find("#quot_qty").val();

    let rateUnit = jQuery(th).closest('tr').find("#rate_unit").val();


    var quotUnit = 0;
    if (rateUnit != "" && quot_qty != "") {
        quotUnit = parseFloat(quot_qty) * parseFloat(rateUnit);
    }

    if (quotUnit != 0) {
        jQuery(th).closest('tr').find("#amount").val(formatAmount(quotUnit));
    } else if (rateUnit == "") {
        jQuery(th).closest('tr').find("#amount").val('');

    } else {
        jQuery(th).closest('tr').find("#amount").val(0);
    }

    totalAmount()
}



function totalAmount() {
    var total_amount = 0;
    jQuery('.amount').map(function () {
        var amount = jQuery(this).val();

        if (amount != "") {

            total_amount = parseFloat(total_amount) + parseFloat(amount);
        }

    });

    if (total_amount != 0) {
        jQuery('.amountsum').text(formatAmount(total_amount));
        jQuery('#basic_amount').val(formatAmount(total_amount));
        jQuery('#net_amount').val(formatAmount(total_amount));
    } else if (amount != 0) {
        jQuery('.amountsum').text('');
        jQuery('#net_amount').val('');
    } else {
        jQuery('.amountsum').text(0);
        jQuery('#net_amount').val(0);
    }
    calcLessDiscount();
    calcGstAmount();

}



function removeQuotDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        if (r === true) {
            jQuery(th).closest("tr").remove();
            var quot_qty = jQuery(th).closest('tr').find('#quot_qty').val();
            var quot_amt = jQuery(th).closest('tr').find('#amount').val();
            // if (quot_qty != "" && quot_amt != "") {
            if (quot_qty != "" || quot_amt != "") {
                var quot_total = jQuery('.quotqtysum').text();
                // var quot_total_second = jQuery('.quotqtysum_second').text();
                var amt_total = jQuery('.amountsum').text();
                if (quot_total != "" || amt_total != "") {
                    quot_final_total = parseFloat(quot_total) - parseFloat(quot_qty);
                    amt_final_total = parseFloat(amt_total) - parseFloat(quot_amt);
                }
                quot_final_total > 0 ? jQuery('.quotqtysum').text(parseFloat(quot_final_total).toFixed(3)) : jQuery('.quotqtysum').text('');

                amt_final_total > 0 ? jQuery('.amountsum').text(parseFloat(amt_final_total).toFixed(3)) : jQuery('.amountsum').text('');

            }
            srNo();
            totalAmount();
        }

    });
}


function srNo() {

    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
    jQuery(".item_id").chosen({
        search_contains: true
    });

}
// check duplication for quot no
jQuery('#quot_sequence').on('change', function () {

    let thisForm = jQuery('#quotationform');
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.stdformbutton button').text();

    if (subBtn == "submit" || subBtn == "Submit") {
        subBtn = jQuery(document).find('.stdform').find('.stdformbutton button');
    }

    if (val != "") {
        if (val > 0 == false) {
            jAlert('Please Enter Valid Quotation No.');
            jQuery('#quot_sequence').parent().parent().parent('div.control-group').addClass('error');
            // jQuery('#quot_sequence').focus();
            jQuery("#customer_group_id").trigger('liszt:activate');
            jQuery('#quot_sequence').val('');

        } else {
            jQuery(subBtn).prop('disabled', true);
            jQuery('#quot_sequence').addClass('file-loader');
            jQuery('#quot_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-quot_no_duplication?for=add&quot_sequence=" + val;

            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-quot_no_duplication?for=edit&quot_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#quot_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {

                        toastError(data.response_message);
                        jQuery('#quot_sequence').parent().parent().parent('div.control-group').addClass('error');
                        // jQuery('#quot_sequence').focus();
                        jQuery("#customer_group_id").trigger('liszt:activate');
                        jQuery('#quot_sequence').val('');

                    } else {

                        jQuery('#quot_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#quot_no').val(data.latest_po_no);
                        jQuery('#quot_sequence').val(val);
                    }
                    jQuery(subBtn).prop('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#quot_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#quot_no').val('');
        jQuery('#quot_sequence').val('');
    }

});
// end duplication for quot no

function getQuotStates(event) {
    let stateIdVal = jQuery('#quot_country_id option:selected').val();

    if (stateIdVal != "" && stateIdVal !== undefined) {
        return jQuery.ajax({
            url: RouteBasePath + "/get-location-states?country_id=" + stateIdVal,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    let dropHtml = `<option value=''>Select State</option>`;
                    if (!jQuery.isEmptyObject(data.states) && data.states.length > 0) {
                        for (let idx in data.states) {
                            dropHtml += `<option value="${data.states[idx].id}">${data.states[idx].state_name}</option>`;
                        }
                    }
                    jQuery('#quot_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    //    jQuery('#taluka_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    //    console.log(dropHtml);

                } else {
                    jQuery('#quot_state_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getQuotDistrict(event) {
    let districtVal = jQuery('#quot_state_id option:selected').val();

    jQuery("#state_id").val(districtVal).trigger('liszt:updated');

    if (districtVal != "" && districtVal !== undefined) {
        return jQuery.ajax({

            url: RouteBasePath + "/get-district?state_id=" + districtVal,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    let dropHtml = `<option value=''>Select District</option>`;
                    if (!jQuery.isEmptyObject(data.cities) && data.cities.length > 0) {
                        for (let idx in data.cities) {
                            dropHtml += `<option value="${data.cities[idx].id}">${data.cities[idx].district_name}</option>`;
                        }
                    }
                    jQuery('#quot_district_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#quot_district_id').empty().append("<option value=''>Select District</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getQuotTaluka(event) {
    let talukaVal = jQuery('#quot_district_id option:selected').val();
    if (talukaVal != "" && talukaVal !== undefined) {
        return jQuery.ajax({

            url: RouteBasePath + "/get-taluka?district_id=" + talukaVal,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    let dropHtml = `<option value=''>Select Taluka</option>`;
                    if (!jQuery.isEmptyObject(data.taluka) && data.taluka.length > 0) {
                        for (let idx in data.taluka) {
                            dropHtml += `<option value="${data.taluka[idx].id}">${data.taluka[idx].taluka_name}</option>`;
                        }
                    }
                    jQuery('#quot_taluka_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#quot_taluka_id').empty().append("<option value=''>Select Taluka</option>").trigger('liszt:updated');
                }
            },
        });
    }
}


function getQuotVillage(event) {
    let villageIdVal = jQuery('#quot_taluka_id option:selected').val();
    if (villageIdVal != "" && villageIdVal !== undefined) {
        return jQuery.ajax({


            url: RouteBasePath + "/get-village?taluka_id=" + villageIdVal,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    let dropHtml = `<option value=''>Select Village</option>`;
                    if (!jQuery.isEmptyObject(data.village) && data.village.length > 0) {
                        for (let idx in data.village) {
                            dropHtml += `<option value="${data.village[idx].id}">${data.village[idx].village_name}</option>`;
                        }
                    }
                    jQuery('#quot_village_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#quot_village_id').empty().append("<option value=''>Select Village</option>").trigger('liszt:updated');
                }
            },
        });
    }
}








// Modal code value are not reset after submission


jQuery('#stateModal').on('show.bs.modal', function (e) {

    var dealer_modal_id = jQuery("#dealer_modal_id").val();

    let country = jQuery("#quot_country_id").val();
    if (dealer_modal_id != "" && jQuery('#dealerModal').is(':visible')) {
        setTimeout(() => {
            jQuery("#country_id").val('').trigger('liszt:updated');
        }, 200);
    } else {
        setTimeout(() => {
            jQuery("#country_id").val(country).trigger('liszt:updated');
        }, 200);
    }





    if (country == "1" || jQuery('#country_id option:selected').val() == "1") {
        jQuery('#gst_code').prop("disabled", false);
    } else {

        jQuery('#gst_code').prop("disabled", true);

        jQuery('#gst_code').val('');
    }

});

jQuery('#cityModal').on('show.bs.modal', function (e) {
    let state = jQuery("#quot_state_id").val();
    let country = jQuery("#quot_country_id").val();
    var dealer_modal_id = jQuery("#dealer_modal_id").val();

    if (dealer_modal_id != "" && jQuery('#dealerModal').is(':visible')) {
        jQuery("#state_id").val('').trigger('liszt:updated');
        jQuery('#commonDistrictForm #country_name').val('');

    } else {
        jQuery("#state_id").val(state).trigger('liszt:updated');
        jQuery('#commonDistrictForm #country_name').val(jQuery('#quot_country_id option:selected').text());

    }


    // if (country != '') {
    //     jQuery('#commonDistrictForm #country_name').val(jQuery('#quot_country_id option:selected').text());
    // }

});

jQuery('#talukaModal').on('show.bs.modal', function (e) {
    let dist = jQuery("#quot_district_id").val();
    let state = jQuery("#quot_state_id").val();

    let country = jQuery("#quot_country_id").val();
    var dealer_modal_id = jQuery("#dealer_modal_id").val();
    if (dealer_modal_id != "" && jQuery('#dealerModal').is(':visible')) {
        jQuery("#taluka_state_id").val('').trigger('liszt:updated');
        jQuery("#taluka_district_id").val('').trigger('liszt:updated');
        jQuery('#commonTalukaForm #country_name').val('');
    } else {

        jQuery("#taluka_state_id").val(state).trigger('liszt:updated');

        if (state != '' && state != null) {
            getDistrict().done(function (resposne) {
                jQuery("#taluka_district_id").val(dist).trigger('liszt:updated');
            });
        }

        if (country != '') {
            jQuery('#commonTalukaForm #country_name').val(jQuery('#quot_country_id option:selected').text());
        }
    }


});

jQuery('#VillageModal').on('show.bs.modal', function (e) {

    let dist = jQuery("#quot_district_id").val();
    let state = jQuery("#quot_state_id").val();
    let taluka = jQuery("#quot_taluka_id").val();
    let country = jQuery("#quot_country_id").val();
    var dealer_modal_id = jQuery("#dealer_modal_id").val();

    if (dealer_modal_id != "" && jQuery('#dealerModal').is(':visible')) {
        jQuery("#village_state_id").val('').trigger('liszt:updated');
        jQuery("#district_id").val('').trigger('liszt:updated');
        jQuery("#taluka_id").val('').trigger('liszt:updated');
        jQuery('#commonVillageForm #country_name').val('');

    } else {

        jQuery("#village_state_id").val(state).trigger('liszt:updated');
        jQuery("#district_id").val(dist).trigger('liszt:updated');

        if ((dist != '' && dist != null) || (taluka != '' && taluka != null)) {
            getDistrictData().done(function (resposne) {
                jQuery("#district_id").val(dist).trigger('liszt:updated');

                getTalukaData().done(function (resposne) {
                    jQuery("#taluka_id").val(taluka).trigger('liszt:updated');
                });
            });
        }

        if (country != '') {
            jQuery('#commonVillageForm #country_name').val(jQuery('#location_country_id option:selected').text());
        }
    }


});


function changePincode() {

    let thisForm = jQuery('#quotationform');

    let getVillageData = jQuery('#quot_village_id option:selected').val();

    if (getVillageData != "" && getVillageData !== undefined) {
        jQuery.ajax({
            url: RouteBasePath + "/get-villageData?village_id=" + getVillageData,
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    jQuery('#quotationform').find("#pincode").val(data.pincode);
                } else {
                    jAlert(data.response_message);
                }
            },
        });
    }
}

// function getLastQuot(val) {

//     if (val == 1) {

//         jQuery.ajax({
//             url: RouteBasePath + "/get-last_quot_details",
//             type: 'GET',
//             headers: headerOpt,
//             dataType: 'json',
//             processData: false,
//             success: function (data) {
//                 jQuery('#quot_no').removeClass('file-loader');
//                 if (data.response_code == 1) {
//                     jQuery('#customer_group_id').val(data.last_data.customer_group_id).trigger('liszt:updated');
//                     jQuery('#customer_reg_no').val(data.last_data.customer_reg_no);
//                     jQuery('#customer_name').val(data.last_data.customer_name);
//                     jQuery('#dealer_id').val(data.last_data.dealer_id).trigger('liszt:updated');
//                     jQuery('#quot_country_id').val(data.last_data.country_id).trigger('liszt:updated');
//                     jQuery('#quot_village_id').val(data.last_data.quot_village_id).trigger('liszt:updated');
//                     jQuery('#special_notes').val(data.last_data.special_notes);
//                     jQuery('#customer_pincode').val(data.last_data.customer_pincode);
//                     if (data.last_data.country_id != null) {
//                         getQuotStates().done(function (resposne) {
//                             jQuery('#quot_state_id').val(data.last_data.state_id).trigger('liszt:updated');
//                             getQuotDistrict().done(function (resposne) {
//                                 jQuery('#quot_district_id').val(data.last_data.district_id).trigger('liszt:updated');
//                                 getQuotTaluka().done(function (resposne) {
//                                     jQuery('#quot_taluka_id').val(data.last_data.customer_taluka).trigger('liszt:updated');
//                                     getQuotVillage().done(function (resposne) {
//                                         jQuery('#quot_village_id').val(data.last_data.quot_village_id).trigger('liszt:updated');
//                                     });
//                                 });

//                             });
//                         });
//                         fillLastQuotTable(data.quotDetails);
//                     }
//                 } else {
//                     console.log(data.response_message)
//                 }
//             },
//             error: function (jqXHR, textStatus, errorThrown) {
//                 jQuery('#quot_no').removeClass('file-loader');
//             }
//         });
//     } else {
//         // jQuery('#quotPartTable tbody').empty();

//     }

// }

function discountRate() {
    var discount = jQuery('#discount').val();

    if (discount !== '') {
        discount = parseFloat(discount);
        if (discount >= 0.01 && discount < 100) {

            // Loop through each table row in #quotPartTable
            jQuery('#quotPartTable tbody tr').each(function () {
                var rateElement = jQuery(this).find('[name="rate_unit[]"]');

                if (rateElement) {
                    var rateValue = rateElement.attr('data-rate') != '' ? parseFloat(rateElement.attr('data-rate')) : rateElement.val();

                    if (!isNaN(rateValue)) {
                        var discountAmount = rateValue * discount / 100;
                        var finalRate = rateValue - discountAmount;

                        finalRate = finalRate > 0 ? finalRate.toFixed(2) : '';

                        rateElement.val(finalRate);
                    }
                }
            });

        } else if (discount >= 100) {
            jQuery('#discount').val('');
            toastError('Please Enter Discount Value Less Than 100');
        } else {
            jQuery('#discount').val('');
            toastError('Please Enter Discount Value Greater Than 0.01');
        }
    }
}

function getCountyandStateForLocation() {
    jQuery.ajax({
        url: RouteBasePath + "/get-country_state_for_location",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jQuery('#quot_country_id').val(data.location_data.country_id).trigger('liszt:updated');
                getQuotStates();
                if (data.location_data.country_id != null) {
                    getQuotStates().done(function (resposne) {
                        jQuery('#quot_state_id').val(data.location_data.state_id).trigger('liszt:updated');
                        getQuotDistrict();
                    })
                }
            }
        },
    });

}


/* GST Calculation */

jQuery('.gst-fields').on('change keyup', function () {
    calcGstAmount();
});
function manageGstType() {

    var thisForm = jQuery('#quotationform');



    var gstType = thisForm.find("input[name*='gst_type_fix_id']:checked").val();


    if (gstType != "") {

        if (gstType == 3) { // None

            thisForm.find(".igst-field").val('')

            thisForm.find(".igst-field:not(.disb)").prop('disabled', true);

            thisForm.find(".sgst-field").val('')

            thisForm.find(".sgst-field:not(.disb)").prop('disabled', true);

            thisForm.find(".cgst-field").val('')

            thisForm.find(".cgst-field:not(.disb)").prop('disabled', true);

        } else if (gstType == 2) { //  SGCT+CSGT


            thisForm.find(".igst-field").val('')

            thisForm.find(".igst-field:not(.disb)").prop('disabled', true);

            thisForm.find(".sgst-field:not(.disb)").prop('disabled', false);

            thisForm.find(".cgst-field:not(.disb)").prop('disabled', false);



        } else { // IGST

            thisForm.find(".igst-field:not(.disb)").prop('disabled', false);

            thisForm.find(".sgst-field").val('')

            thisForm.find(".sgst-field:not(.disb)").prop('disabled', true);

            thisForm.find(".cgst-field").val('')

            thisForm.find(".cgst-field:not(.disb)").prop('disabled', true);

        }

    }
    calcGstAmount();

}

function calcGstAmount() {

    var thisForm = jQuery('#quotationform');
    var gstType = thisForm.find("input[name*='gst_type_fix_id']:checked").val();
    var basicAmount = thisForm.find("#basic_amount").val();
    var pfPer = thisForm.find("#secondary_transport").val();
    pfAmount = isNaN(Number(pfPer)) ? 0 : Number(pfPer);
    basicAmount = isNaN(Number(basicAmount)) ? 0 : Number(basicAmount);

    var lessDiscountAmount = thisForm.find("#less_discount_amount").val();
    lessDiscountAmount = isNaN(Number(lessDiscountAmount)) ? 0 : Number(lessDiscountAmount);

    var sumAmount = pfAmount + basicAmount - lessDiscountAmount;


    if (gstType != "") {

        if (gstType == 3) { // NONE

        } else if (gstType == 2) { //   SGCT+CSGT

            var sgstPer = thisForm.find("#sgst_percentage").val();
            var cgstPer = thisForm.find("#cgst_percentage").val();
            sgstPer = isNaN(Number(sgstPer)) ? 0 : Number(sgstPer);
            cgstPer = isNaN(Number(cgstPer)) ? 0 : Number(cgstPer);

            if (sumAmount > 0 && sgstPer > 0) {
                thisForm.find("#sgst_amount").val(formatAmount(sumAmount * (sgstPer / 100)));
            } else {
                thisForm.find("#sgst_amount").val('');
            }

            if (sumAmount > 0 && cgstPer > 0) {
                thisForm.find("#cgst_amount").val(formatAmount(sumAmount * (cgstPer / 100)));
            } else {
                thisForm.find("#cgst_amount").val('');

            }

        } else { // IGST

            var igstPer = thisForm.find("#igst_percentage").val();
            igstPer = isNaN(Number(igstPer)) ? 0 : Number(igstPer);
            if (sumAmount > 0 && igstPer > 0) {
                thisForm.find("#igst_amount").val(formatAmount(sumAmount * (igstPer / 100)));
            } else {
                thisForm.find("#igst_amount").val('');
            }
        }
    }
    calcNetAmount();

}

function calcNetAmount() {

    var thisForm = jQuery('#quotationform');
    var gstType = thisForm.find("input[name*='gst_type_fix_id']:checked").val();
    var basicAmount = thisForm.find("#basic_amount").val();
    basicAmount = isNaN(Number(basicAmount)) ? 0 : Number(basicAmount);
    var lessDiscountAmount = thisForm.find("#less_discount_amount").val();
    lessDiscountAmount = isNaN(Number(lessDiscountAmount)) ? 0 : Number(lessDiscountAmount);
    var pfPer = thisForm.find("#secondary_transport").val();

    pfAmount = isNaN(Number(pfPer)) ? 0 : Number(pfPer);


    var r_val = thisForm.find("#round_off").val();

    if (r_val != '') {
        if (r_val.trim() !== "") {
            var r = isNaN(Number(r_val)) ? 0 : Number(r_val);     // Convert round-off to number
        }
    } else {
        var r = 0;
    }


    if (gstType != "") {

        if (gstType == 3) { // None

            if (r < 0) {
                thisForm.find("#net_amount").val(parseFloat((basicAmount - lessDiscountAmount + pfAmount) - Math.abs(r)).toFixed(2));
            } else {
                thisForm.find("#net_amount").val(parseFloat(basicAmount - lessDiscountAmount + pfAmount + r).toFixed(2));
            }

        } else if (gstType == 2) { //   SGCT+CSGT

            var sgstAmount = thisForm.find("#sgst_amount").val();
            var cgstAmount = thisForm.find("#cgst_amount").val();
            sgstAmount = isNaN(Number(sgstAmount)) ? 0 : Number(sgstAmount);
            cgstAmount = isNaN(Number(cgstAmount)) ? 0 : Number(cgstAmount);

            if (r < 0) {

                thisForm.find("#net_amount").val(parseFloat(basicAmount - lessDiscountAmount + pfAmount + sgstAmount + cgstAmount - Math.abs(r)).toFixed(2));

            } else {
                thisForm.find("#net_amount").val(parseFloat(basicAmount - lessDiscountAmount + pfAmount + sgstAmount + cgstAmount + r).toFixed(2));
            }


        } else { //IGST

            var igstAmount = thisForm.find("#igst_amount").val();
            igstAmount = isNaN(Number(igstAmount)) ? 0 : Number(igstAmount);
            if (r < 0) {
                thisForm.find("#net_amount").val(parseFloat((basicAmount - lessDiscountAmount + pfAmount + igstAmount) - Math.abs(r)).toFixed(2));
            } else {
                thisForm.find("#net_amount").val(parseFloat(basicAmount - lessDiscountAmount + pfAmount + igstAmount + r).toFixed(2));
            }


        }
    }

}

function calcLessDiscount() {
    var thisForm = jQuery('#quotationform');
    var basicAmount = thisForm.find("#basic_amount").val();
    var lessDiscountPercentage = thisForm.find("#less_discount_percentage").val();
    lessDiscountPercentage = isNaN(Number(lessDiscountPercentage)) ? 0 : Number(lessDiscountPercentage);

    if (basicAmount > 0 && basicAmount != '' && lessDiscountPercentage > 0 && lessDiscountPercentage <= 100 && lessDiscountPercentage != '') {
        thisForm.find("#less_discount_amount").val(formatAmount(basicAmount * (lessDiscountPercentage / 100)));
    } else {
        thisForm.find("#less_discount_amount").val('');
    }
    calcGstAmount();
}


/* End GST Calculation */



jQuery(document).on('keydown', '.round-off', function (e) {

    // Allow numbers (0-9), plus (+), minus (-), and decimal point (.)
    if (
        (e.which >= 48 && e.which <= 57) ||  // Numbers 0-9 (main keyboard)
        (e.which >= 96 && e.which <= 105) || // Numbers 0-9 (numpad)
        e.key === '+' ||                    // Plus sign (+)
        e.key === '-' ||                    // Minus sign (-)
        e.key === '.' ||                    // Decimal point (.)
        e.which === 8 ||                     // Backspace
        e.which === 9 ||                     // Tab
        e.which === 32                       // Space (optional if you want to allow spaces)
    ) {
        // Allow input for numbers, plus, minus, decimal, and backspace, tab, space
        return;
    }

    // Prevent all other keys
    e.preventDefault();
});

async function getQuotDealer() {
    return new Promise((resolve, reject) => {
        var customer_group_id = jQuery('#customer_group_id option:selected').val();

        if (formId == undefined) {
            var url = RouteBasePath + "/get-quot_dealer?customer_group_id=" + customer_group_id;
        } else {
            var url = RouteBasePath + "/get-quot_dealer?customer_group_id=" + customer_group_id + "&id=" + formId;
        }

        jQuery.ajax({
            url: url,
            type: 'POST',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    let dropHtml = `<option value=''>Select Dealer</option>`;
                    if (!jQuery.isEmptyObject(data.quot_dealer) && data.quot_dealer.length > 0) {
                        for (let idx in data.quot_dealer) {
                            dropHtml += `<option value="${data.quot_dealer[idx].id}">${data.quot_dealer[idx].dealer_name}</option>`;
                        }
                    }
                    jQuery('#dealer_id').empty().append(dropHtml).trigger('liszt:updated');
                    resolve();  // Resolve promise when finished
                } else {
                    jQuery('#dealer_id').empty().append("<option value=''>Select Dealer</option>").trigger('liszt:updated');
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
    });

}

