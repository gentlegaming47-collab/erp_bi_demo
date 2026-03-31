


var date = new Date();
var currentDay = String(date.getDate()).padStart(2, '0');
var currentMonth = String(date.getMonth() + 1).padStart(2, "0");
var currentYear = date.getFullYear();


var currentDate = `${currentDay}/${currentMonth}/${currentYear}`;


var productDrpHtml = '<option value="">Select Item</option>';
var dpnoDrpHtml = '<option value="">Select DP NO.</option>';
var srDetailArray = [];
var sr_data = [];

var formId = jQuery('#salesreturnform').find('input:hidden[name="id"]').val();

if (formId != undefined) { //if form is edit
    jQuery(document).ready(function () {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({

            url: RouteBasePath + "/get-sales_return/" + formId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {
                    //  getCustomer();


                    jQuery('#sales_return_button').prop('disabled', true);

                    // setTimeout(() => {
                    //     jQuery('#so_sequence').focus();
                    // }, 100);


                    // jQuery('input:radio[name="sr_from_id_fix"][value="' + data.sr_data.sr_from_id_fix + '"]').attr('checked', true).trigger('click');


                    // jQuery('#sr_from_id_fix').val(data.sr_data.sr_from_id_fix);

                    jQuery('#sr_sequence').val(data.sr_data.sr_sequence).prop({ tabindex: -1, readonly: true });

                    jQuery('#sr_no').val(data.sr_data.sr_number).prop({ tabindex: -1, readonly: true });

                    jQuery('#sr_date').val(data.sr_data.sr_date);

                    jQuery('#customer_name').append(new Option(data.sr_data.customer_name, data.sr_data.customer_name, true, true)).val(data.sr_data.customer_name).attr('readonly', true);
                    setTimeout(() => {

                        jQuery('#dp_no_id').val(data.sr_data.dp_no_id).trigger("liszt:updated").prop({ tabindex: -1 }).attr('readonly', true);
                    }, 300);

                    // getDpNoFromDispatchPlan();

                    setTimeout(() => {

                        jQuery('#transporter_id').val(data.sr_data.transporter_id).trigger('liszt:updated');

                    }, 700);


                    jQuery('#vehicle_no').val(data.sr_data.vehicle_no);


                    jQuery('#lr_no_date').val(data.sr_data.lr_no_date);

                    jQuery('#sp_note').val(data.sr_data.sp_note);

                    setTimeout(() => {
                        // jQuery('#so_sequence').focus();
                        // jQuery("#sr_date").focus();
                        // jQuery('#dp_no_id').trigger('liszt:activate');
                        jQuery('.up_item_id_0').trigger('liszt:activate');
                    }, 1000);
                    loadSalesReturnData(data);
                    loadDPNumberData(data);
                    loadDetailsData(data);


                    soIsUse = true;
                    //




                    // jQuery("input[name*='sr_from_id_fix']").prop({ tabindex: -1 }).attr('readonly', true);
                    // jQuery("input[name*='so_type_id_fix']").prop({ tabindex: -1 }).attr('readonly', true);
                    jQuery('#show-progress').removeClass('loader-progress-whole-page');
                    jQuery('#sales_return_button').prop('disabled', false);
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
        // getCustomer();
        addPartDetail();
        getLatestSrNo();
        getAllCustomer();
        // getDPNumber();
        // getCountyandStateForLocation();
        // manageGstType();
        // changeRadio(1)
        // getLastSo(1);
        // changeFormRadio(1);


        // getItemsFromSalesOrder();
        // getSoDealer();
        setTimeout(() => {
            // jQuery("#sr_date").focus();
            jQuery("#customer_name").trigger('liszt:activate');
        }, 100);

    });
}


function getLatestSrNo() {

    jQuery.ajax({

        url: RouteBasePath + "/get-latest_sr_no",

        type: 'GET',

        headers: headerOpt,

        dataType: 'json',

        processData: false,

        success: function (data) {

            jQuery('#sr_no').removeClass('file-loader');

            if (data.response_code == 1) {

                jQuery('#sr_no').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });

                jQuery('#sr_sequence').val(data.number).prop({ tabindex: -1, readonly: true });

                // jQuery('#sr_sequence').val(data.number);

                jQuery('#sr_date').val(currentDate);

            } else {

                console.log(data.response_message)

            }

        },

        error: function (jqXHR, textStatus, errorThrown) {

            jQuery('#sr_no').removeClass('file-loader');

            console.log('Field To Get Latest SR No.!')

        }

    });

}

// check duplication for sr no
jQuery('#sr_sequence').on('change', function () {

    let thisForm = jQuery('#salesreturnform');
    var val = jQuery(this).val();


    var subBtn = jQuery(document).find('.stdform').find('.stdformbutton button').text();



    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.stdformbutton button');

    }

    if (val != undefined) {

        if (val > 0 == false) {

            jAlert('Please Enter Valid Sales Return No.');
            jQuery('#sr_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    // jQuery('#sr_sequence').focus();
                    jQuery("#customer_name").trigger('liszt:activate');
                }, 100);
            });
            jQuery('#sr_sequence').val('');

        } else {
            jQuery(subBtn).prop('disabled', true);
            jQuery('#sr_sequence').addClass('file-loader');
            jQuery('#sr_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-sr_no_duplication?for=add&sr_sequence=" + val;

            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-sr_no_duplication?for=edit&sr_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#sr_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {

                        toastError(data.response_message);
                        jQuery('#sr_sequence').parent().parent().parent('div.control-group').addClass('error');
                        // jQuery('#sr_sequence').focus();
                        jQuery("#customer_name").trigger('liszt:activate');
                        jQuery('#sr_sequence').val('');

                    } else {

                        jQuery('#sr_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#sr_no').val(data.latest_po_no);
                        jQuery('#sr_sequence').val(val);
                    }
                    jQuery(subBtn).prop('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#sr_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#sr_no').val('');
        jQuery('#sr_sequence').val('');
    }

});

function getAllCustomer() {
    if (formId == undefined) {
        var Url = RouteBasePath + "/get-sales_order_all_customer";
    } else {
        var Url = RouteBasePath + "/get-sales_order_all_customer" + "?id=" + formId;
    }

    jQuery.ajax({
        url: Url,
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            let suppHtml = '';
            suppHtml += `<option value="">Select Customer</option> `;
            if (data.response_code == 1) {
                for (let indx in data.SOCustomer) {
                    suppHtml += `<option value="${data.SOCustomer[indx].customer_name}">${data.SOCustomer[indx].customer_name}</option>`;

                }
                jQuery('#customer_name').empty().append(suppHtml).trigger('liszt:updated')
                jQuery('#show-progress').removeClass('loader-progress-whole-page');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#show-progress').removeClass('loader-progress-whole-page');
        }
    });

    setTimeout(() => {
        jQuery("#customer_name").trigger('liszt:activate');
    }, 100);
}

// function getCustomer() {
//     jQuery('#show-progress').addClass('loader-progress-whole-page');
//     var sr_type = jQuery('input[name="sr_from_id_fix"]:checked').val();

//     if (sr_type == "1") {

//         if (formId == undefined) {
//             var Url = RouteBasePath + "/get-so_customer_sub";
//         } else {
//             var Url = RouteBasePath + "/get-so_customer_sub" + "?id=" + formId;
//         }

//         jQuery.ajax({
//             // url: RouteBasePath + "/get-pending_inward_customer",
//             url: Url,
//             type: 'GET',
//             headers: headerOpt,
//             dataType: 'json',
//             processData: false,
//             success: function (data) {
//                 let suppHtml = '';
//                 suppHtml += `<option value="">Select Customer</option> `;
//                 if (data.response_code == 1) {
//                     for (let indx in data.SOCustomer) {
//                         suppHtml += `<option value="${data.SOCustomer[indx].customer_name}">${data.SOCustomer[indx].customer_name}</option>`;

//                     }
//                     jQuery('#customer_name').empty().append(suppHtml).trigger('liszt:updated')
//                     jQuery('#show-progress').removeClass('loader-progress-whole-page');
//                 }
//             },
//             error: function (jqXHR, textStatus, errorThrown) {
//                 jQuery('#show-progress').removeClass('loader-progress-whole-page');
//             }
//         });

//     } else if (sr_type == "2") {
//         if (formId == undefined) {
//             var Url = RouteBasePath + "/get-so_customer_cash_carry";
//         } else {
//             var Url = RouteBasePath + "/get-so_customer_cash_carry" + "?id=" + formId;
//         }
//         jQuery.ajax({
//             url: Url,
//             type: 'GET',
//             headers: headerOpt,
//             dataType: 'json',
//             processData: false,
//             success: function (data) {
//                 let suppHtml = '';
//                 suppHtml += `<option value="">Select Customer</option> `;
//                 if (data.response_code == 1) {
//                     for (let indx in data.SOCustomer) {
//                         suppHtml += `<option value="${data.SOCustomer[indx].customer_name}">${data.SOCustomer[indx].customer_name}</option>`;

//                     }
//                     jQuery('#customer_name').empty().append(suppHtml).trigger('liszt:updated');
//                     jQuery('#show-progress').removeClass('loader-progress-whole-page');
//                 }
//             },
//             error: function (jqXHR, textStatus, errorThrown) {
//                 jQuery('#show-progress').removeClass('loader-progress-whole-page');
//             }
//         });

//     }

//     setTimeout(() => {
//         jQuery("#customer_name").trigger('liszt:activate');
//     }, 100);

// }

function addPartDetail() {

    // var sr = sr_no++;


    var thisHtml = `<tr>
                        <td><a onclick="removeSrDetails(this)"><i class="action-icon iconfa-trash sr_details"></i></a></td>

                        <td>
                         <select name="item_id[]"  class="chzn-select add_item item_id sr_item_select_width" onChange="getItemData(this)">${productDrpHtml}</select> 
                            <span class="eyeMargin"><a><i class="action-icon iconfa-eye-open eyeIcon1 d-none"></i></a></span>
                         </td>
                        <td><input type="hidden" name="item_details_id[]" value=""/></td>
                        <td>
                        <input type="hidden" name="sales_return_detail_id[]" value="0">
                        <input type="hidden" name="le_details_id[]" id="le_details_id" value="0">
                        <input type="hidden" name="dp_details_id[]" id="dp_details_id" value="0">
                        <input type="hidden" name="fitting_item[]">

                    
                        <input type="text" name="dc_qty[]" id="dc_qty" onblur="formatPoints(this,3)"  class="form-control isNumberKey dc_qty " style="width:50px;" readonly tabindex="-1"/></td>

                        <td><input type="text" name="pend_dc_qty[]" id="pend_dc_qty" onblur="formatPoints(this,3)"  class="form-control isNumberKey pend_dc_qty isNumberKey " style="width:100px;" readonly tabindex="-1"/></td>

                        <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control" tabindex="-1" readonly/></td>
                        
                         <td><input type="text" name="sr_details_qty[]" id="sr_details_qty" class="form-control only-numbers"onKeyup="calSecondQty(this)" style="width:50px;"/></td>

                        <td><input type="text" name="sr_qty[]" id="sr_qty" class="form-control sr_qty isNumberKey"onblur="formatPoints(this,3)" style="width:50px;"/></td>

                        <td>
                        <input type="text" name="remark[]" id="remark" class="form-control"/>
                        </td>        
                    </tr>`;

    jQuery('#srPartTable tbody').append(thisHtml);
    jQuery(".item_id").chosen();
    // jQuery(".dp_no").chosen();
    disabledDropdownVal();
}


async function getItemsFromSalesOrder() {

    return new Promise((resolve, reject) => {
        var dp_no_id = jQuery('#dp_no_id option:selected').val();
        if (dp_no_id != '') {

            if (formId == undefined) {
                var Url = RouteBasePath + "/get-items_from_so_customer?dp_no_id=" + dp_no_id;
            } else {
                var Url = RouteBasePath + "/get-items_from_so_customer?dp_no_id=" + dp_no_id + "&id=" + formId;
            }

            jQuery.ajax({
                url: Url,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {


                        if (data.SOCustomerItems.length > 0) {
                            productDrpHtml = `<option value="">Select Item</option>`;
                            var item_id = ``;
                            for (let indx in data.SOCustomerItems) {

                                productDrpHtml += `<option value="${data.SOCustomerItems[indx].id}" data-unit="${data.SOCustomerItems[indx].unit_name}">${data.SOCustomerItems[indx].item_name} </option>`;
                            }
                        } else {
                            productDrpHtml = `<option value="">Select Item</option>`;
                        }


                        // jQuery('.item_id').chosen();
                        jQuery(".item_id").chosen({
                            search_contains: true
                        });
                        jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
                        resolve();
                        jQuery('#show-progress').removeClass('loader-progress-whole-page');
                        resolve();
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

// async function getDpNoFromDispatchPlan(th) {

//        return new Promise((resolve, reject) => {
//         // var item_id = jQuery('input[name=item_id] option:selected').val();
//         // var item_id = jQuery('select[name="item_id[]"]').val();
//         var item_id = jQuery(th).val();
//         if (item_id != '') {

//             if (formId == undefined) {
//                 var Url = RouteBasePath + "/get-dp_no_from_dispatch_plan?item_id=" + item_id;
//             } else {
//                 var Url = RouteBasePath + "/get-dp_no_from_dispatch_plan?item_id=" + item_id + "&id=" + formId;
//             }

//             jQuery.ajax({
//                 url: Url,
//                 type: 'GET',
//                 headers: headerOpt,
//                 dataType: 'json',
//                 processData: false,
//                 success: function (data) {
//                     if (data.response_code == 1) {

//                         if (data.items_dp_no.length > 0) {
//                             dpnoDrpHtml = `<option value="">Select DP NO.</option>`;
//                             var item_id = ``;
//                             for (let indx in data.items_dp_no) {

//                                 dpnoDrpHtml += `<option value="${data.items_dp_no[indx].dp_id}"data-dc_qty="${data.items_dp_no[indx].plan_qty}" data-dp_details_id="${data.items_dp_no[indx].dp_details_id}" data-le_details_id="${data.items_dp_no[indx].le_details_id}">${data.items_dp_no[indx].dp_number} </option>`;
//                             }
//                         } else {
//                             dpnoDrpHtml = `<option value="">Select DP NO.</option>`;
//                         }


//                         // jQuery('.item_id').chosen();
//                         jQuery(".dp_no").chosen({
//                             search_contains: true
//                         });
//                         // jQuery(item_id).empty().append(dpnoDrpHtml).trigger('liszt:updated');
//                         jQuery('.dp_no').empty().append(dpnoDrpHtml).trigger('liszt:updated');
//                         resolve();  
//                         jQuery('#show-progress').removeClass('loader-progress-whole-page');
//                         // resolve();
//                     } else {

//                         dpnoDrpHtml = `<option value="">Select DP NO.</option>`;
//                         jQuery(item_id).empty().append(dpnoDrpHtml).trigger('liszt:updated');
//                     }
//                 },
//             });

//         } else {

//             dpnoDrpHtml = `<option value="">Select DP NO.</option>`;
//             jQuery(item_id).empty().append(dpnoDrpHtml).trigger('liszt:updated');
//         }
//     });
// }


async function getDpNoFromDispatchPlan(th) {
    return new Promise((resolve, reject) => {
        var item_id = jQuery(th).val();
        var $row = jQuery(th).closest('tr'); // target only this row
        var $dpNoSelect = $row.find('.dp_no'); // dp_no of this row

        var customer_name = jQuery('#customer_name option:selected').val();


        if (item_id != '') {
            var Url = formId == undefined
                ? RouteBasePath + "/get-dp_no_from_dispatch_plan?item_id=" + item_id + "&customer_name=" + customer_name
                : RouteBasePath + "/get-dp_no_from_dispatch_plan?item_id=" + item_id + "&customer_name=" + customer_name + "&id=" + formId;

            jQuery.ajax({
                url: Url,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    let dpnoDrpHtml = `<option value="">Select DP NO.</option>`;

                    if (data.response_code == 1 && data.items_dp_no.length > 0) {
                        for (let indx in data.items_dp_no) {
                            dpnoDrpHtml += `<option value="${data.items_dp_no[indx].dp_id}" 
                                data-dc_qty="${data.items_dp_no[indx].plan_qty}" 
                                data-dp_details_id="${data.items_dp_no[indx].dp_details_id}" 
                                data-le_details_id="${data.items_dp_no[indx].le_details_id}"
                                data-fitting_item="${data.items_dp_no[indx].fitting_item}"
                                data-pend_dc_qty="${data.items_dp_no[indx].pend_dc_qty}">
                                ${data.items_dp_no[indx].dp_number}
                            </option>`;
                        }
                    }

                    // update only current row dropdown
                    $dpNoSelect.empty().append(dpnoDrpHtml).trigger('liszt:updated');
                    $dpNoSelect.chosen({
                        search_contains: true
                    });

                    jQuery('#show-progress').removeClass('loader-progress-whole-page');
                    resolve();
                },
            });

        } else {
            // reset only current row dropdown
            $dpNoSelect.empty().append(`<option value="">Select DP NO.</option>`).trigger('liszt:updated');
            jQuery('#show-progress').removeClass('loader-progress-whole-page');
            resolve();
        }
    });
}

function getItemData(th) {
    let openModal = "no";

    let item = th.value;
    var selectedValue = jQuery(th).val();
    var selected = jQuery(th).val();
    var thisselected = jQuery(th);
    var selectedOption = jQuery(th).find('option:selected');
    var secondaryItem = selectedOption.data('secondary_unit');
    if (selected) {
        if (secondaryItem == 'Yes') {
            jQuery(th).parents('tr').find("#sr_details_qty").val('').prop({ tabindex: 1, readonly: false });
            jQuery(th).parents('tr').find("#sr_qty").val('').prop({ tabindex: -1, readonly: true });

        }
        else {
            jQuery(jQuery('.item_id').not(jQuery(th))).each(function (index) {
                // openModal = "yes";

                if (thisselected.val() == jQuery(this).val()) {
                    jAlert('This Item Is Already Selected.');
                    var selectTd = thisselected.closest('td');

                    selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id sr_item_select_width" onChange="getItemData(this)">${productDrpHtml}</select><span class="eyeMargin"><a><i class="action-icon iconfa-eye-open eyeIcon1 d-none"></i></a></span>
                    <input type="hidden" name="check_fitting[]">`);
                    // jQuery('.item_id').chosen();
                    jQuery(".item_id").chosen({
                        search_contains: true
                    });
                    openModal = "yes";
                }

            });
            jQuery(th).parents('tr').find("#sr_details_qty").val('').prop({ tabindex: -1, readonly: true });
            jQuery(th).parents('tr').find("#sr_qty").val('').attr('readonly', false);
        }
    }
    var selectedOption = jQuery(th).find('option:selected');
    var item_id = selectedOption.val();
    let row = jQuery(th).closest('tr');
    if (item_id != "" && item_id != undefined) {
        let unit = selectedOption.data('unit');
        let dp_details_id = selectedOption.data('dp_details_id');
        let le_details_id = selectedOption.data('le_details_id');
        var dc_qty = selectedOption.data('dc_qty');
        var pend_dc_qty = selectedOption.data('pend_dc_qty');
        var fitting_item = selectedOption.data('fitting_item');
        jQuery(row).find("input[name='unit[]']").val(unit);
        jQuery(row).find("input[name='dp_details_id[]']").val(dp_details_id);
        jQuery(row).find("input[name='le_details_id[]']").val(le_details_id);
        jQuery(row).find("input[name='fitting_item[]']").val(fitting_item);
        jQuery(row).find("#dc_qty").val(parseFloat(dc_qty).toFixed(3)).attr('readonly', true);
        jQuery(row).find(".pend_dc_qty").val(parseFloat(pend_dc_qty).toFixed(3));
        if (fitting_item == "yes") {
            jQuery(row).find(".sr_qty").val(parseFloat(0).toFixed(3)).attr('readonly', true);
        } else {
            if (secondaryItem == 'Yes') {
                jQuery(row).find('input').removeAttr('max');
                jQuery(row).parents('tr').find("#sr_qty").val('').prop({ tabindex: -1, readonly: true });
            } else {

                jQuery(row).find(".sr_qty").val("").attr('readonly', false).attr('max', pend_dc_qty);
            }
        }
    } else {
        jQuery(row).find("input[name='unit[]']").val('');
        jQuery(row).find("input[name='dp_details_id[]']").val('');
        jQuery(row).find("input[name='le_details_id[]']").val('');
        jQuery(row).find("input[name='fitting_item[]']").val('');
        jQuery(row).find("#dc_qty").val('');
        jQuery(row).find(".pend_dc_qty").val('');
        jQuery(row).find(".sr_qty").val("").attr('readonly', false);
        // jQuery(row).find(".sr_qty").val('');

    }

    if (item != "" && item != null) {
        var dp_details_id = jQuery(row).find("input[name='dp_details_id[]']").val();
        jQuery.ajax({
            url: RouteBasePath + "/get-fitting_item_data_for_sr?item=" + item + "&dp_details_id=" + dp_details_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (data.stock_qty != null) {

                        var minQty = isNaN(Number(data.stock_qty.stock_qty)) ? 0 : Number(data.stock_qty.stock_qty);
                    } else {
                        var minQty = 0;
                    }

                    var productDetailDrpHtml = ``;
                    if (data.item_detail.length > 0) {
                        var productDetailDrpHtml = `<select name="item_details_id[]" class="chzn-select  item_id item_details_id add_item_details" onChange="getItemDetailData(this)"><option value="">Select Item</option>`;
                        for (let indx in data.item_detail) {
                            var dc_qty = data.item_detail[indx].plan_qty ? data.item_detail[indx].plan_qty : 0.000;
                            var pend_dc_qty = data.item_detail[indx].pend_dc_qty ? data.item_detail[indx].pend_dc_qty : 0.000;
                            var secondary_qty = data.item_detail[indx].secondary_qty ? data.item_detail[indx].secondary_qty : 0.000;
                            var sec_unit = data.item_detail[indx].unit_name ? data.item_detail[indx].unit_name : "";
                            productDetailDrpHtml += `<option value="${data.item_detail[indx].item_details_id}" data-dc_qty="${dc_qty}" data-pend_dc_qty="${pend_dc_qty}" data-secondary_qty="${secondary_qty}"data-second_unit="${sec_unit}">${data.item_detail[indx].secondary_item_name} </option>`;
                        }

                        productDetailDrpHtml += `</select>`;
                    } else {
                        productDetailDrpHtml = `<input type="hidden" name="item_details_id[]" value="" />`;
                    }


                    jQuery(th).parents('tr').find('td').eq(jQuery(th).closest('td').index() + 1).html(productDetailDrpHtml);
                    jQuery('.item_details_id').chosen();

                    // jQuery(th).parents('tr').find("#code").val(data.item.item_code);
                    // jQuery(th).parents('tr').find("#item_id").val(data.item.id);
                    // jQuery(th).parents('tr').find("#group").val(data.item.item_group_name);
                    // jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);
                    // jQuery(th).parents('tr').find("#stock_qty").val(parseFloat(minQty).toFixed(3));
                    jQuery(th).parents('tr').find("#sr_details_qty").val('');
                    jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");
                    jQuery(th).parents('tr').find("#issue_qty").attr('max', minQty);
                    // jQuery(th).parents('tr').find("#dc_qty").prop('readonly', false);
                    jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
                    // jQuery(th).parents('tr').find("#issue_qty").prop({ tabindex: -1, readonly: false });
                    // jQuery(th).parents('tr').find("#remarks").prop({ tabindex: -1, readonly: false });
                    jQuery(th).parents('tr').find("#issue_qty").prop('tabindex', 0);
                    jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);


                    if (formId == undefined) {
                        jQuery(th).parents('tr').find("#pre_item").val(item);
                    } else {
                        if (jQuery(th).parents('tr').find("#pre_item").val() == 0) {
                            jQuery(th).parents('tr').find("#pre_item").val(item);
                        }
                    }



                } else {
                    jQuery('#code').val('');
                    jQuery('#item_id').val('');
                    jQuery('#group').val('');
                    jQuery('#unit').val('');
                    jQuery('#po_qty').val('');
                    jQuery('#rate_unit').val('');
                    jQuery('#remarks').val('');
                }
            },
        });
    }
}


function getItemDetailData(th) {
    var selected = jQuery(th).val();
    var thisselected = jQuery(th);
    var selectedOption = jQuery(th).find('option:selected');
    var second_unit = selectedOption.data('second_unit');
    var dc_qty = selectedOption.data('dc_qty');
    var pend_dc_qty = selectedOption.data('pend_dc_qty');
    var second_qty = jQuery(th).closest('tr').find("select[name='item_details_id[]'] option:selected").data('secondary_qty');
    jQuery(th).parents('tr').find("#unit").val(second_unit);
    jQuery(th).parents('tr').find("#dc_qty").val(parseFloat(dc_qty).toFixed(3));
    jQuery(th).parents('tr').find("#pend_dc_qty").val(parseFloat(pend_dc_qty).toFixed(3));
    // jQuery(th).parents('tr').find("#sr_qty").attr('max', parseFloat(pend_dc_qty) * parseFloat(second_qty));
    jQuery(th).parents('tr').find("#sr_details_qty").attr('max', parseFloat(pend_dc_qty));
    // jQuery(th).parents('tr').find("#sr_details_qty").val('').attr('readonly', false).attr('max', pend_dc_qty);
    // jQuery(th).parents('tr').find("#sr_qty").val('').prop({ tabindex: -1, readonly: true });

    if (selected == "") {
        jQuery(th).parents('tr').find("#dc_qty").val('');
        jQuery(th).parents('tr').find("#pend_dc_qty").val('');
    }

    calSecondQty(th)
}

// function getDpNoData(th) {
//     let openModal = "no";
//      var selected = jQuery(th).val();

//     var thisselected = jQuery(th);
//     if (selected) {
//         jQuery(jQuery('.dp_no').not(jQuery(th))).each(function (index) {
//             // openModal = "yes";

//             if (thisselected.val() == jQuery(this).val()) {
//                 jAlert('This Item Is Already Selected.');
//                 var selectTd = thisselected.closest('td');

//                 selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id so_item_select_width" onChange="getItemData(this),getDpNoFromDispatchPlan(this)">${productDrpHtml}</select><span class="eyeMargin"><a><i class="action-icon iconfa-eye-open eyeIcon1 d-none"></i></a></span>
//                 <input type="hidden" name="check_fitting[]">`);
//                 // jQuery('.item_id').chosen();
//                 jQuery(".item_id").chosen({
//                     search_contains: true
//                 });
//                 openModal = "yes";
//             }       

//         });
//     }
//     var selectedOption = jQuery(th).find('option:selected');
//     var dp_no = selectedOption.val();

//     var row = jQuery(th).closest('tr');
//     if (dp_no != "" && dp_no != undefined) {
//         var dc_qty = selectedOption.data('dc_qty');
//         var pend_dc_qty = selectedOption.data('pend_dc_qty');
//         var dp_details_id = selectedOption.data('dp_details_id');
//         var le_details_id = selectedOption.data('le_details_id');
//         jQuery(row).find(".dc_qty").val((dc_qty).toFixed(3));
//         if(jQuery(row).find('input[name="dp_details_id[]"]').val() == 0 ){
//             jQuery(row).find(".pend_dc_qty").val((pend_dc_qty).toFixed(3));
//         }
//          jQuery(row).find(".sr_qty").attr("max", pend_dc_qty.toFixed(3));
//         jQuery(row).find('input[name="dp_details_id[]"]').val(dp_details_id);
//         jQuery(row).find('input[name="le_details_id[]"]').val(le_details_id);
//     } else {
//         jQuery(row).find(".dc_qty").val('');
//         // jQuery(row).find(".pend_dc_qty").val('');
//         // jQuery(row).find(".sr_qty").removeAttr("max");
//        jQuery(row).find('input[name="dp_details_id[]"]').val("");
//         jQuery(row).find('input[name="le_details_id[]"]').val("");

//     }
// }

function getDpNoData(th) {
    let openModal = "no";
    var thisRow = jQuery(th).closest('tr');

    var selectedDpNo = jQuery(th).val();
    var selectedItemId = thisRow.find('.item_id').val(); // get item_id in same row
    var row = jQuery(th).closest('tr');

    if (selectedDpNo) {
        jQuery('.dp_no').not(th).each(function () {
            var otherRow = jQuery(this).closest('tr');
            var otherDpNo = jQuery(this).val();
            var otherItemId = otherRow.find('.item_id').val();

            // Check both item_id and dp_no
            if (selectedDpNo == otherDpNo && selectedItemId == otherItemId) {
                jAlert('This DP NO. Are Already Selected.');

                // Reset DP No dropdown
                jQuery(th).val('').trigger('liszt:updated');
                jQuery(row).find(".pend_dc_qty").val('');
                openModal = "yes";
                return false; // stop loop
            }
        });
    }

    var selectedOption = jQuery(th).find('option:selected');
    var dp_no = selectedOption.val();

    if (dp_no != "" && dp_no != undefined) {
        var dc_qty = selectedOption.data('dc_qty');
        var pend_dc_qty = selectedOption.data('pend_dc_qty');
        var dp_details_id = selectedOption.data('dp_details_id');
        var le_details_id = selectedOption.data('le_details_id');
        var fitting_item = selectedOption.data('fitting_item');


        thisRow.find(".dc_qty").val((dc_qty).toFixed(3));
        // if (thisRow.find('input[name="dp_details_id[]"]').val() == 0) {
        thisRow.find(".pend_dc_qty").val((pend_dc_qty).toFixed(3));
        // }

        thisRow.find('input[name="dp_details_id[]"]').val(dp_details_id);
        thisRow.find('input[name="le_details_id[]"]').val(le_details_id);
        thisRow.find('input[name="fitting_item[]"]').val(fitting_item);

        if (fitting_item == 'yes') {
            thisRow.find(".sr_qty").val(parseFloat(0).toFixed(3)).attr("readonly", true);
        } else {

            // thisRow.find(".sr_qty").val('').attr("readonly", false);
            thisRow.find(".sr_qty").attr("readonly", false);
            thisRow.find(".sr_qty").attr("max", pend_dc_qty.toFixed(3));
        }
    } else {
        thisRow.find(".dc_qty").val('');
        thisRow.find('input[name="dp_details_id[]"]').val("");
        thisRow.find('input[name="le_details_id[]"]').val("");
        thisRow.find(".pend_dc_qty").val('');
        thisRow.find(".sr_qty").val('').attr("readonly", false);
    }
}

jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    // var fiting_item = jQuery(element).data('fiting_item');

    // console.log(fiting_item);
    // if (jQuery(element).prop('readonly') && fiting_item == 'yes') {
    if (jQuery(element).prop('readonly')) {
        return true;
    }
    return this.optional(element) || parseFloat(value) >= 0.001;
});

jQuery.validator.addMethod("secUnit", function (value, element) {
    var $row = jQuery(element).closest("tr");
    // use attr to read exactly the data attribute name you set in option
    var secUnit = $row.find('select[name="item_id[]"] option:selected').attr('data-secondary_unit');

    // nothing selected or explicitly "No" => not required
    if (!secUnit || secUnit === "No") {
        return true;
    }

    // if element is readonly for some special case, consider it valid
    if (jQuery(element).prop('readonly')) {
        return true;
    }

    // when secUnit === "Yes" => it must have a non-empty (and non-zero if wanted) value
    var val = jQuery.trim(value);
    // adjust the second check if you want to disallow '0' or some placeholder
    return val !== "" && val !== "0";
}, "Please Select Item Detail.");

var validator = jQuery("#salesreturnform").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,
    rules: {
        sr_sequence: {
            required: true
        },
        sr_date: {

            required: true,
            dateFormat: true,
            date_check: true
        },
        customer_name: {
            required: true,
        },
        dp_no_id: {
            required: true,
        },
        "item_id[]": {
            required: true,
        },
        'item_details_id[]': {
            secUnit: true,
        },
        "sr_details_qty[]": {
            required: function (e) {
                if (jQuery(e).prop('readonly')) {
                    return false;
                }
                return true;
            },
            notOnlyZero: '0.001',
        },
        "sr_qty[]": {
            required: function (e) {
                if (jQuery(e).prop('readonly')) {
                    return false;
                }
                return true;
            },
            notOnlyZero: '0.001',
        },
    },
    messages: {

        sr_sequence: {
            required: "Please Enter SR No.",
        },
        sr_date: {
            required: "Please Enter SR Date",
        },
        customer_name: {
            required: "Please Select Customer"
        },
        dp_no_id: {
            required: "Please Select DP. NO."
        },
        "item_id[]": {
            required: "Please Select Item"
        },
        'item_details_id[]': {
            required: "Please Select Item Detail"
        },
        "sr_details_qty[]": {
            required: "Please Enter SR Details Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.'
        },
        "sr_qty[]": {
            required: "Please Enter SR Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.'
        },
    },
    submitHandler: function (form) {
        let visibleRows = jQuery("#srPartTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        if (visibleRows < 1) {
            jAlert("Please Add At Least One Sales Return Detail.");
            return false;
        }

        jQuery('#sales_return_button').prop('disabled', true);

        let formUrl = (typeof formId !== "undefined")
            ? RouteBasePath + "/update-sales_return"
            : RouteBasePath + "/store-sales_return";

        let requestData = jQuery('#salesreturnform').serialize();
        // let requestData = formData + '&' + jQuery.param({ sales_return_details: srDetailArray });

        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: requestData,
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    toastSuccess(data.response_message, function () {
                        if (typeof formId !== "undefined") {
                            window.location.href = RouteBasePath + "/manage-sales_return";
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    toastError(data.response_message);
                }
                jQuery('#sales_return_button').prop('disabled', false);
            },
            error: function (jqXHR) {
                try {
                    let errMessage = JSON.parse(jqXHR.responseText);
                    if (errMessage.errors) {
                        validator.showErrors(errMessage.errors);
                    } else if (jqXHR.status == 401) {
                        toastError(jqXHR.statusText);
                    } else {
                        toastError('Something went wrong!');
                        console.log(errMessage);
                    }
                } catch (e) {
                    toastError('Something went wrong!');
                }
                jQuery('#sales_return_button').prop('disabled', false);
            }
        });
    }
});

async function loadSalesReturnData(data) {
    try {
        jQuery('#customer_name').val(data.sr_data.customer_name).trigger('liszt:updated');

        await getItemsFromSalesOrder();

        // await fillSrTable(data.sr_part_details);
        await SRdetailData(data.sr_part_details);

    } catch (error) {
        console.error("Error: ", error);
    }
}

async function waitForOptions($select, minOptions = 1, timeout = 3000) {
    const start = Date.now();
    while ($select.find('option').length <= minOptions) {
        if (Date.now() - start > timeout) break; // stop after timeout
        await new Promise(r => setTimeout(r, 5));
    }
}

async function fillSrTable(sr_data) {

    if (sr_data.length > 0) {
        var thisHtml = '';
        var counter = 1;

        for (let key in sr_data) {
            var sr_no = counter;
            var rowId = `row_${counter}`;
            var sales_return_id = formId == undefined ? 0 : sr_data[key].sr_details_id != null ? sr_data[key].sr_details_id : 0;
            var item_id = sr_data[key].item_id || "";
            var item_name = sr_data[key].item_name || "";
            var item_details_id = sr_data[key].item_details_id ? sr_data[key].item_details_id : "";

            var le_details_id = sr_data[key].le_details_id || "";
            var dp_details_id = sr_data[key].dp_details_id || "";
            var fitting_item = sr_data[key].fitting_item || "";
            var dp_id = sr_data[key].dp_id || "";
            var dc_qty = sr_data[key].dc_qty ? parseFloat(sr_data[key].dc_qty).toFixed(3) : parseFloat(0).toFixed(3);
            var unit_name = sr_data[key].unit_name || "";
            var sr_details_qty = sr_data[key].sr_details_qty ? sr_data[key].sr_details_qty : "";

            if (fitting_item == "yes") {
                var sr_qty = sr_data[key].sr_qty ? parseFloat(sr_data[key].sr_qty).toFixed(3) : parseFloat(0).toFixed(3);
            }
            else {
                var sr_qty = sr_data[key].sr_qty ? parseFloat(sr_data[key].sr_qty).toFixed(3) : "";

            }
            if (sales_return_id == 0) {
                var pend_dc_qty = sr_data[key].pend_dc_qty ? parseFloat(sr_data[key].pend_dc_qty).toFixed(3) : "0.000";
                var pend_dc_detail_qty = sr_data[key].pend_dc_detail_qty ? parseFloat(sr_data[key].pend_dc_detail_qty).toFixed(3) : "0.000";

            } else {
                var pend_dc_qty = parseFloat(sr_data[key].pend_dc_qty) + parseFloat(sr_data[key].sr_qty);
                var pend_dc_detail_qty = parseFloat(sr_data[key].pend_dc_detail_qty) + parseFloat(sr_data[key].sr_qty);
                // console.log(sr_data[key].sr_qty,pend_dc_qty);

            }


            var remark = sr_data[key].remark || "";
            var productDetailDrpHtml = ``;

            if (sr_data[key].item_detail.length > 0) {
                var productDetailDrpHtml = `<select name="item_details_id[]" class="chzn-select  item_id item_details_ids_${sr_no} add_item_details"  onChange="getItemDetailData(this)"><option value="">Select Item</option>`;
                for (let indx in sr_data[key].item_detail) {
                    var dc_qty = sr_data[key].item_detail[indx].plan_qty ? sr_data[key].item_detail[indx].plan_qty : 0.000;
                    // if (sales_return_id == 0) {
                    var pend_dc_qty = sr_data[key].item_detail[indx].pend_dc_qty ? sr_data[key].item_detail[indx].pend_dc_qty : 0.000;
                    // } else {
                    //     var pend_dc_qty = parseFloat(sr_data[key].item_detail[indx].pend_dc_qty) + parseFloat(sr_data[key].sr_qty);


                    // }
                    var sec_unit = sr_data[key].item_detail[indx].unit_name ? sr_data[key].item_detail[indx].unit_name : "";
                    productDetailDrpHtml += `<option value="${sr_data[key].item_detail[indx].item_details_id}"data-dc_qty="${dc_qty}" data-pend_dc_qty="${pend_dc_qty}"data-second_unit="${sec_unit}"data-secondary_qty="${sr_data[key].item_detail[indx].secondary_qty}">${sr_data[key].item_detail[indx].secondary_item_name} </option>`;
                }

                productDetailDrpHtml += `</select>`;
            } else {
                productDetailDrpHtml = `<input type="hidden" name="item_details_id[]" value="" />`;
            }

            // console.log(sr_data[key]);

            thisHtml += `<tr id="${rowId}">`;
            if (sr_data[key].in_use == true) {
                thisHtml += `<td></td>`;
            } else {
                thisHtml += `<td><a onclick="removeSrDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>`;
            }

            thisHtml += `
                <td>
                    <input type="hidden" name="sales_return_detail_id[]" value="${sales_return_id}">
                    <select name="item_id[]" class="chzn-select item_id sr_item_select_width up_item_id_${key}" 
                        onChange="getItemData(this)">
                        ${productDrpHtml}
                    </select>
                
                    <input type="hidden" name="pre_item_id[]" value="${item_id}">
                    <input type="hidden" name="pre_item_details_id[]" value="${item_details_id}">
                    <input type="hidden" name="pre_sr_details_qty[]" value="${sr_details_qty}">
                    <input type="hidden" name="pre_sr_qty[]" value="${sr_qty}">
                    <input type="hidden" name="le_details_id[]" value="${le_details_id}">
                    <input type="hidden" name="dp_details_id[]" value="${dp_details_id}">
                    <input type="hidden" name="fitting_item[]" value="${fitting_item}">
                    <input type="hidden" name="org_fitting_item[]" value="${fitting_item}">
                    <input type="hidden" name="org_dp_details_id[]" value="${dp_details_id}">
                </td>
                <td>${productDetailDrpHtml}</td>
                <td><input type="text" name="dc_qty[]" id="dc_qty"  onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey dc_qty" readonly value="${parseFloat(dc_qty).toFixed(3)}" style="width:50px;" tabindex="-1"/></td>  

                <td><input type="text" name="pend_dc_qty[]" id="pend_dc_qty" onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey pend_dc_qty" value="${parseFloat(pend_dc_qty).toFixed(3)}" readonly style="width:100px;"  tabindex="-1"/></td>   
                <td><input type="text" name="unit[]" class="form-control salesmanageTable" readonly value="${unit_name}" style="width:50px;"tabindex="-1" /></td> 
                <td><input type="text" name="sr_details_qty[]" id="sr_details_qty" class="form-control salesmanageTable  only-numbers" value="${sr_details_qty}" ${item_details_id != "" ? "" : "readonly tabindex = '-1'"} style="width:50px;"  onKeyup="calSecondQty(this)" /></td>     
                <td><input type="text" name="sr_qty[]" id="sr_qty" onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey sr_qty" value="${sr_qty}" style="width:50px;" max="${fitting_item == 'yes' ? '0.000' : item_details_id != "" ? '' : pend_dc_qty}" ${fitting_item == 'yes' ? 'readonly tabindex = "-1"' : ''} ${item_details_id != "" ? "readonly tabindex = '-1'" : ""}/></td>     
                <td><input type="text" name="remark[]" class="form-control" value="${remark}"/></td>            
            </tr>`;
            counter++;
        }

        jQuery('#srPartTable tbody').append(thisHtml);

        jQuery('.item_id').chosen();

        var detailCounter = 1;
        for (let key in sr_data) {
            var item_id = sr_data[key].item_id ? sr_data[key].item_id : "";
            var item_details_id = sr_data[key].item_details_id ? sr_data[key].item_details_id : "";
            jQuery(`.item_details_ids_${detailCounter}`).val(item_details_id).trigger('liszt:updated').change();
            jQuery(`.up_item_id_${key}`).val(item_id).trigger('liszt:updated');
            detailCounter++;
        }



    }
}


function removeSrDetails(th) {

    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        if (r === true) {

            let srPartId = jQuery(th).closest('tr').find('input[name="sales_return_detail_id[]"]').val();

            jQuery(th).closest("tr").remove();

        }

    });
}


async function loadDPNumberData(data) {
    try {
        jQuery('#customer_name').val(data.sr_data.customer_name).trigger('liszt:updated');
        await getDPNumber();
        jQuery('#dp_no_id').val(data.sr_data.dp_no_id).trigger('liszt:updated');
    } catch (error) {
        console.log("Error: ", error);
    }
}
async function loadDetailsData(data) {
    try {
        // jQuery('#dp_no_id').val(data.sr_data.dp_no_id).trigger('liszt:updated');
        // jQuery('#customer_name').val(data.sr_data.customer_name).trigger('liszt:updated');
        // await getDetailsData();


        // if (data.itemDetails.length > 0) {
        //     productDrpHtml = `<option value="">Select Item</option>`;
        //     for (let indx in data.itemDetails) {

        //         productDrpHtml += `<option value="${data.itemDetails[indx].id}" data-unit="${data.itemDetails[indx].unit_name}" data-dp_details_id="${data.itemDetails[indx].dp_details_id}" data-le_details_id="${data.itemDetails[indx].le_details_id}" data-fitting_item="${data.itemDetails[indx].fitting_item}" data-dc_qty="${data.itemDetails[indx].dc_qty}" data-pend_dc_qty="${data.itemDetails[indx].pend_dc_qty}" data-secondary_unit="${data.itemDetails[indx].secondary_unit}">${data.itemDetails[indx].item_name} </option>`;
        //     }
        // }


        // await fillSrTable(data.sr_part_details);
        await SRdetailData(data.sr_part_details);

    } catch (error) {
        console.log("Error: ", error);
    }
}

async function getDPNumber() {
    return new Promise((resolve, reject) => {
        // var customer_name = jQuery('#customer_name option:selected').val();
        var customer_name = encodeURIComponent(jQuery('#customer_name option:selected').val());

        if (formId == undefined) {
            var url = RouteBasePath + "/get-dp_number?customer_name=" + customer_name;
        } else {
            var url = RouteBasePath + "/get-dp_number?customer_name=" + customer_name + "&id=" + formId;
        }

        jQuery.ajax({
            url: url,
            type: 'POST',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    // console.log(data);
                    let dropHtml = `<option value=''>Select DP NO.</option>`;
                    if (!jQuery.isEmptyObject(data.dp_number) && data.dp_number.length > 0) {
                        for (let idx in data.dp_number) {
                            dropHtml += `<option value="${data.dp_number[idx].dp_id}">${data.dp_number[idx].dp_number}</option>`;
                        }
                    }
                    jQuery('#dp_no_id').empty().append(dropHtml).trigger('liszt:updated');
                    resolve();
                    if (formId == undefined) {
                        getDetailsData();
                    }
                } else {
                    jQuery('#dp_no_id').empty().append("<option value=''>Select DP NO.</option>").trigger('liszt:updated');
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
async function getDetailsData() {
    return new Promise((resolve, reject) => {
        var dp_no = jQuery('#dp_no_id option:selected').val();
        var customer_name = jQuery('#customer_name option:selected').val();
        if (formId == undefined) {

            var url = RouteBasePath + "/get-dispatch_data?dpids=" + dp_no + "&customer_name=" + customer_name;
        } else {
            var url = RouteBasePath + "/get-dispatch_data?dpids=" + dp_no + "&customer_name=" + customer_name + "&id=" + formId;
        }


        jQuery.ajax({

            url: url,
            type: 'POST',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {


                    if (data.dispatch_data.length > 0 && !jQuery.isEmptyObject(data.dispatch_data)) {
                        SRdetailData(data.dispatch_data)
                        //     let tableHtml = '';
                        //     data.dispatch_data.forEach((row, i) => {

                        //         tableHtml += `
                        //             <tr>
                        //                 <td><a onclick="removeSrDetails(this)"><i class="action-icon iconfa-trash sr_details"></i></a></td>

                        //                 <td>
                        //                     <input type="hidden" name="sales_return_detail_id[]" value="0">
                        //                     <input type="hidden" name="dp_details_id[]" value="${row.dp_details_id}">
                        //                     <input type="hidden" name="le_details_id[]" value="${row.le_details_id}">
                        //                     <input type="hidden" id="secondary_qty" name="secondary_qty[]" value="${row.secondary_qty}">
                        //                     <input type="hidden" name="fitting_item[]" value="${row.fitting_item}">
                        //                     <input type="hidden" name="item_id[]" id="item_id" value="${row.item_id}">

                        //                     ${row.item_name}
                        //                 </td>

                        //                 <td> <input type="hidden" name="item_details_id[]" id="item_details_id" value="${row.item_details_id ?? ''}">${row.secondary_item_name ?? ''}</td>

                        //                 <td><input type="text" name="dc_qty[]" id="dc_qty"  onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey dc_qty" readonly value="${row.secondary_item_name != null ? parseFloat(row.plan_qty).toFixed(3) : parseFloat(row.dc_qty).toFixed(3)}" style="width:50px;" tabindex="-1"/></td> 

                        //                 <td><input type="text" name="pend_dc_qty[]" id="pend_dc_qty" onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey pend_dc_qty" value="${row.secondary_item_name != null ? parseFloat(row.pend_details_dc_qty).toFixed(3) : parseFloat(row.pend_dc_qty).toFixed(3)}" readonly style="width:100px;"  tabindex="-1"/></td>   

                        //                 <td><input type="text" name="unit[]" class="form-control salesmanageTable" readonly value="${row.unit_name}" style="width:50px;"tabindex="-1" /></td> 

                        //                <td><input type="text" name="sr_details_qty[]" id="sr_details_qty" onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey" max="${row.pend_details_dc_qty}"  ${row.secondary_item_name != null ? "" : "readonly tabindex = '-1'"} style="width:50px;"  onKeyup="calSecondQty(this)" /></td> 

                        //                 <td><input type="text" name="sr_qty[]" id="sr_qty" onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey sr_qty" style="width:50px;" max="${row.fitting_item == 'yes' ? '0.000' : row.secondary_item_name != null ? '' : row.pend_dc_qty}" ${row.fitting_item == 'yes' ? 'readonly tabindex = "-1"' : ''} ${row.secondary_item_name != null ? "readonly tabindex = '-1'" : ""} value="${row.fitting_item == 'yes' ? parseFloat(0).toFixed(3) : ""}"/></td>  

                        //                 <td><input type="text" class="form-control" name="remark[]" value=""></td>
                        //             </tr>
                        //         `;
                        //     });

                        //     jQuery('#srPartTable tbody').html(tableHtml);
                        //     // srDetailArray.push(data.dispatch_data);
                    } else {
                        jQuery('#srPartTable tbody').empty();
                        // addPartDetail(); // jo khali hoy to default ek row
                    }
                    jQuery(".item_id").chosen({
                        search_contains: true
                    });
                    jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated').change();
                    // resolve();
                    if (!jQuery.isEmptyObject(data.dispatch_detail_data)) {
                        dispatchDetailArray = data.dispatch_detail_data;
                    }
                    resolve();

                } else {

                    toastError(data.response_message);
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
}

jQuery(document).on('change', '.add_item_details', function (e) {
    var selected = jQuery(this).val();
    var thisselected = jQuery(this);
    if (selected) {
        jQuery(jQuery('.add_item_details').not(jQuery(this))).each(function (index) {
            if (thisselected.val() == jQuery(this).val()) {
                jAlert('This Item Is Already Selected.');
                thisselected.val('').trigger('liszt:updated');

                jQuery('.add_item_details').chosen();
            }
        });
    }
});


function calSecondQty(th) {
    var sr_details_qty = jQuery(th).closest('tr').find("#sr_details_qty").val();

    // var second_qty = jQuery(th).closest('tr').find("select[name='item_details_id[]'] option:selected").data('secondary_qty');
    var second_qty = jQuery(th).closest('tr').find("#secondary_qty").val();

    var srQty = 0;
    if (sr_details_qty != "" && second_qty != "") {
        srQty = parseFloat(sr_details_qty) * parseFloat(second_qty);
    }

    jQuery(th).parents('tr').find("#sr_qty").val(srQty.toFixed(3));

}


function SRdetailData(sr_data) {

    if (sr_data.length > 0) {
        var tableHtml = '';
        var counter = 1;

        for (let key in sr_data) {
            tableHtml += `
                                <tr>
                                    <td><a onclick="removeSrDetails(this)"><i class="action-icon iconfa-trash sr_details"></i></a></td>

                                    <td>
                                        <input type="hidden" name="sales_return_detail_id[]" value="${sr_data[key].sr_details_id ?? 0}">
                                        <input type="hidden" name="dp_details_id[]" value="${sr_data[key].dp_details_id}">
                                        <input type="hidden" name="le_details_id[]" value="${sr_data[key].le_details_id}">
                                        <input type="hidden" id="secondary_qty" name="secondary_qty[]" value="${sr_data[key].secondary_qty}">
                                        <input type="hidden" name="fitting_item[]" value="${sr_data[key].fitting_item}">
                                        <input type="hidden" name="item_id[]" id="item_id" value="${sr_data[key].item_id}">
                                        
                                        ${sr_data[key].item_name}
                                    </td>

                                    <td> <input type="hidden" name="item_details_id[]" id="item_details_id" value="${sr_data[key].item_details_id ?? ''}">
                                    <input type="hidden" name="le_secondary_details_id[]" id="le_secondary_details_id" value="${sr_data[key].le_secondary_details_id ?? ''}">
                                    
                                    ${sr_data[key].secondary_item_name ?? ''}</td>

                                    <td><input type="text" name="dc_qty[]" id="dc_qty"  onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey dc_qty" readonly value="${sr_data[key].secondary_item_name != null ? parseFloat(sr_data[key].plan_qty).toFixed(3) : parseFloat(sr_data[key].dc_qty).toFixed(3)}" style="width:50px;" tabindex="-1"/></td> `;

            if (sr_data[key].sr_details_id != 0) {

                tableHtml += ` <td> <input type="hidden" name="pre_item_id[]" value="${sr_data[key].item_id}">
                    <input type="hidden" name="pre_item_details_id[]" value="${sr_data[key].item_details_id ?? ''}">
                    <input type="hidden" name="pre_sr_details_qty[]" value="${sr_data[key].sr_details_qty}">
                    <input type="hidden" name="pre_sr_qty[]" value="${parseFloat(sr_data[key].sr_qty).toFixed(3)}">
                    <input type="hidden" name="org_fitting_item[]" value="${sr_data[key].fitting_item}">
                    <input type="hidden" name="org_dp_details_id[]" value="${sr_data[key].dp_details_id}">
                <input type="text" name="pend_dc_qty[]" id="pend_dc_qty" onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey pend_dc_qty" value="${sr_data[key].secondary_item_name != null ? parseFloat(sr_data[key].pend_details_dc_qty).toFixed(3)+ sr_data[key].sr_details_qty : parseFloat(sr_data[key].pend_dc_qty + sr_data[key].sr_qty).toFixed(3)}" readonly style="width:100px;"  tabindex="-1"/></td> `;

            } else {

                tableHtml += ` <td><input type="text" name="pend_dc_qty[]" id="pend_dc_qty" onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey pend_dc_qty" value="${sr_data[key].secondary_item_name != null ? parseFloat(sr_data[key].pend_details_dc_qty).toFixed(3) : parseFloat(sr_data[key].pend_dc_qty).toFixed(3)}" readonly style="width:100px;"  tabindex="-1"/></td> `;
            }


            tableHtml += `<td><input type="text" name="unit[]" class="form-control salesmanageTable" readonly value="${sr_data[key].unit_name}" style="width:50px;"tabindex="-1" /></td> `;

            if (sr_data[key].sr_details_id != 0) {
                tableHtml += `<td> <input type="text" name="sr_details_qty[]" id="sr_details_qty"  class="form-control salesmanageTable  only-numbers" max="${parseFloat(sr_data[key].pend_details_dc_qty).toFixed(3) + sr_data[key].sr_details_qty}" ${sr_data[key].secondary_item_name != null ? "" : "readonly tabindex = '-1'"} style="width:50px;" onKeyup="calSecondQty(this)" value="${sr_data[key].secondary_item_name != null ? sr_data[key].sr_details_qty : ''} "/></td>

                    <td> <input type="text" name="sr_qty[]" id="sr_qty" onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey sr_qty" style="width:50px;" max="${sr_data[key].fitting_item == 'yes' ? '0.000' : sr_data[key].secondary_item_name != null ? '' : parseFloat(sr_data[key].pend_dc_qty + sr_data[key].sr_qty).toFixed(3)}" ${sr_data[key].fitting_item == 'yes' ? 'readonly tabindex = "-1"' : ''} ${sr_data[key].secondary_item_name != null ? "readonly tabindex = '-1'" : ""} value="${sr_data[key].fitting_item == 'yes' ? parseFloat(0).toFixed(3) : parseFloat(sr_data[key].sr_qty).toFixed(3)}" /></td> `;

            } else {

                tableHtml += ` <td> <input type="text" name="sr_details_qty[]" id="sr_details_qty"  class="form-control salesmanageTable  only-numbers" max="${parseFloat(sr_data[key].pend_details_dc_qty).toFixed(3)}" ${sr_data[key].secondary_item_name != null ? "" : "readonly tabindex = '-1'"} style="width:50px;" onKeyup="calSecondQty(this)" /></td>

                    <td><input type="text" name="sr_qty[]" id="sr_qty" onblur="formatPoints(this,3)" class="form-control salesmanageTable isNumberKey sr_qty" style="width:50px;" max="${sr_data[key].fitting_item == 'yes' ? '0.000' : sr_data[key].secondary_item_name != null ? '' : parseFloat(sr_data[key].pend_dc_qty).toFixed(3)}" ${sr_data[key].fitting_item == 'yes' ? 'readonly tabindex = "-1"' : ''} ${sr_data[key].secondary_item_name != null ? "readonly tabindex = '-1'" : ""} value="${sr_data[key].fitting_item == 'yes' ? parseFloat(0).toFixed(3) : ""}"/></td>  `;
            }



            tableHtml += `<td> <input type="text" class="form-control" name="remark[]" value="${sr_data[key].remark ?? ''}"></td></tr > `;
        }

        jQuery('#srPartTable tbody').empty().html(tableHtml);
    }

}
