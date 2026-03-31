
let ItemProductionHiddenId = jQuery('#commonItemProductionForm').find('input:hidden[name="id"]').val();
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;


if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        /*productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;*/
        productDrpHtml += `<option value="${getItem[0][indx].id}"
                                data-item_code="${getItem[0][indx].item_code}"
                                data-unit_name="${getItem[0][indx].unit_name}" 
                                data-item_group="${getItem[0][indx].item_group_name}"
                                data-stock_qty="${getItem[0][indx].stock_qty}">
                                ${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}


jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

    //  edit code

    if (ItemProductionHiddenId != null && ItemProductionHiddenId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({

            url: RouteBasePath + "/get-item_production/" + ItemProductionHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {

                    // setTimeout(() => {
                    //     jQuery('#ip_sequence').focus();
                    // }, 100);

                    jQuery("#ip_sequence").val(data.itemProduction.ip_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery("#ip_number").val(data.itemProduction.ip_number).prop({ tabindex: -1, readonly: true });
                    jQuery("#ip_date").val(data.itemProduction.ip_date)

                    jQuery("#special_notes").val(data.itemProduction.special_notes)

                    fillitemIProductionTable(data.itemProductionDetails)

                    setTimeout(() => {
                        // jQuery('#ip_date').focus();
                        jQuery('.item_id_1').trigger('liszt:activate');
                    }, 1000);

                    jQuery('#show-progress').removeClass('loader-progress-whole-page');


                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-item_issue";
                    });
                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                var errMessage = JSON.parse(jqXHR.responseText);



                if (jqXHR.status == 401) {
                    jAlert(jqXHR.statusText);




                } else {


                    jAlert('Something went wrong!');
                    // toastError('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }
    else {
        jQuery(document).ready(function () {  // at add time get the se. number

            // setTimeout(() => {
            //     jQuery('#ip_sequence').focus();
            // }, 100);
            getLatestItemProductionNo();
            addItemProductionDetail();

            setTimeout(() => {
                // jQuery('#ip_date').focus();
                jQuery('.item_id').trigger('liszt:activate');
            }, 1000);
        });
    }


    // jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    //     return this.optional(element) || parseInt(value) > 0.001;
    // });
    // validation for rate
    // jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    //     formatPoints(element, 3); // Format the value before validation
    //     return this.optional(element) || parseFloat(value) >= parseFloat(param);
    // });
    jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
        //formatPoints(element, 3); // Format the value before validation
        //return this.optional(element) || parseFloat(value) >= parseFloat(param);
        return this.optional(element) || parseFloat(value) > 0;

    });

    // Store or Update

    var validator = jQuery("#commonItemProductionForm").validate({
        onclick: false,
        rules: {
            onkeyup: false,
            onfocusout: false,

            ip_sequence: {
                required: true
            },

            ip_date: {
                required: true,
                date_check: true,
                dateFormat: true
            },
            'item_id[]': {
                required: true
            },
            'production_qty[]': {
                required: true,
                notOnlyZero: '0.001',
            },

            // 'item_id[]': {
            //     required: function (e) {
            //         if (jQuery(e).val().trim() == "") {
            //             jQuery(e).addClass('error');
            //             jQuery(e).focus();
            //             return true;
            //         } else {
            //             jQuery(e).removeClass('error');
            //         }
            //     },
            // },
            // 'production_qty[]': {
            //     required: function (e) {
            //         if (jQuery("#commonItemProductionForm").find('input[name="item_id[]"]').val() != "" && jQuery("#commonItemProductionForm").find('input[name="production_qty[]"]').val() == "") {
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
        },

        messages: {


            ip_sequence: {
                required: "Please Enter IP. Number"
            },

            ip_date: {
                required: "Please Enter Issue Date.",
            },

            'item_id[]': {
                required: "Please Select Item"
            },

            'production_qty[]': {
                required: "Please Enter Production Qty.",
                notOnlyZero: 'Please Enter A Value Greater Than 0.'
            },


        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {



            let checkLength = jQuery("#itemProductionTable tbody tr").filter(function () {
                return jQuery(this).css('display') !== 'none';
            }).length;

            if (checkLength < 1) {
                jAlert("Please Add At Least One Item Production Detail.");
                addItemProductionDetail();
                return false;
            }

            jQuery('#item_production_button').prop('disabled', true);
            var formdata = jQuery('#commonItemProductionForm').serialize();


            let formUrl = ItemProductionHiddenId != undefined && ItemProductionHiddenId != "" ? RouteBasePath + "/update-item_production" : RouteBasePath + "/store-item_production";


            jQuery.ajax({

                url: formUrl,

                type: 'POST',

                data: formdata,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if (data.response_code == 1) {

                        if (ItemProductionHiddenId != undefined && ItemProductionHiddenId != null) {
                            toastSuccess(data.response_message, nextFn);
                            function nextFn() {
                                window.location.href = RouteBasePath + "/manage-item_production";
                            }
                            // toastPreview(data.response_message, redirectFn, prePO);
                            // function redirectFn() {
                            //     window.location.href = RouteBasePath + "/manage-item_production";
                            // };
                            // function prePO() {
                            //     id = btoa(data.id);
                            //     window.location.reload();
                            // }
                        } else {
                            toastSuccess(data.response_message, nextFn);
                            //toastPreview(data.response_message, redirectFn, prePO);
                            function nextFn() {
                                window.location.reload();
                            }
                            // toastPreview(data.response_message, redirectFn, prePO);
                            // function redirectFn() {
                            //     window.location.reload();
                            // }
                            // function prePO() {
                            //     id = btoa(data.id);
                            //     window.location.reload();
                            // }
                            jQuery('#item_production_button').prop('disabled', false);
                        }
                    } else {
                        jQuery("#item_production_button").attr('disabled', false);
                        toastError(data.response_message);
                    }




                },

                error: function (jqXHR, textStatus, errorThrown) {

                    var errMessage = JSON.parse(jqXHR.responseText);



                    if (errMessage.errors) {
                        jQuery('#item_production_button').prop('disabled', false);
                        validator.showErrors(errMessage.errors);



                    } else if (jqXHR.status == 401) {
                        jQuery('#item_production_button').prop('disabled', false);
                        jAlert(jqXHR.statusText);


                        // toastError(jqXHR.statusText);

                    } else {

                        jQuery('#item_production_button').prop('disabled', false);
                        jAlert('Something went wrong!');

                        // toastError('Something went wrong!');

                        console.log(JSON.parse(jqXHR.responseText));

                    }

                }

            });

        }

    });

});


// get the latest number
function getLatestItemProductionNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-pending_item_production_qty",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#po_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#ip_date').val(currentDate);
                jQuery('#ip_number').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });
                jQuery('#ip_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#issue_number').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}


// add time 
function addItemProductionDetail() {
    jQuery("#item_production_total_qty").attr('disabled', true);
    var thisHtml = `
    <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="ip_details_id[]" value="0"></td></tr>
          
    <tr>

    <td>
        <a onclick="removeItemProductionDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
    </td>


    <td class="sr_no"></td>

    <td> <select name="item_id[]"  class="chzn-select  item_id add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select></td>

    <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

    <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

    <td>
    <input type="hidden" name="pre_item[]" id="pre_item" value="0">  
    <input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" style="width:60%;"  tabindex="-1" readonly/></td>    

    <td><input type="text" name="production_qty[]" tabindex="-1" onblur="formatPoints(this,3)" id="production_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey production_qty" style="width:50px;" tabindex="-1" readonly/></td>

    <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly/></td>

    <td><input type="text" name="remarks[]" tabindex="-1" id="remarks"  class="form-control salesmanageTable potableremarks" tabindex="-1" readonly/></td>

    </tr>`;
    jQuery('#itemProductionTable tbody').append(thisHtml);

    // srNo();
    setTimeout(() => {
        srNo();
    }, 200);

    sumSoQty();
}



// edit time 
function fillitemIProductionTable(itemProductionDetails) {
    if (itemProductionDetails.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in itemProductionDetails) {

            var sr_no = counter;

            var ip_details_id = itemProductionDetails[key].ip_details_id ? itemProductionDetails[key].ip_details_id : "";

            var unit = itemProductionDetails[key].unit_name ? itemProductionDetails[key].unit_name : "";
            var secondary_unit = itemProductionDetails[key].secondary_unit ? itemProductionDetails[key].secondary_unit : "";

            var item_code = itemProductionDetails[key].item_code ? itemProductionDetails[key].item_code : "";

            var item_group_name = itemProductionDetails[key].item_group_name ? itemProductionDetails[key].item_group_name : "";

            var item_id = itemProductionDetails[key].item_id ? itemProductionDetails[key].item_id : "";


            var stock_qty = itemProductionDetails[key].stock_qty ? parseFloat(itemProductionDetails[key].stock_qty).toFixed(3) : parseFloat(0).toFixed(3);

            var production_qty = itemProductionDetails[key].production_qty ? parseFloat(itemProductionDetails[key].production_qty).toFixed(3) : "";

            var remarks = itemProductionDetails[key].remarks ? checkSpecialCharacter(itemProductionDetails[key].remarks) : "";

            thisHtml += `
            <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="ip_details_id[]" value="${ip_details_id}"></td></tr>                   
            <tr>
        
            <td>
                <a ${secondary_unit == "Yes" ? '' : 'onclick="removeItemProductionDetails(this)"'}><i class="action-icon iconfa-trash so_details"></i></a>
            </td>
        
        
            <td class="sr_no">${sr_no}</td>
        
            <td> <select name="item_id[]"  class="chzn-select  item_id add_item item_id_${sr_no}" onChange="getItemData(this)" ${secondary_unit == "Yes" ? 'readonly' : ''}>${productDrpHtml}</select></td>

            
            <td>
            <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null ? item_id : 0}"> 
            <input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" tabindex="-1" readonly/></td>     
            
            <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_group_name}" tabindex="-1" readonly/></td>           
            

            `;


            if (ItemProductionHiddenId == undefined) {
                thisHtml += `   

                <td><input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" value="${stock_qty}" style="width:60%;"  tabindex="-1" readonly/></td> 


               
                <td><input type="text" name="production_qty[]" id="production_qty" onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)"  class="form-control isNumberKey production_qty" readonly/></td>

                <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly/></td>
            
              

                <td><input type="text" name="remarks[]"   id="remarks" class="form-control salesmanageTable potableremarks" readonly /></td>
                `;
            } else {

                thisHtml += `                   
                <td>
                <input type="hidden" name="old_production_qty[]" value="${production_qty}">
                <input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" onblur="formatPoints(this,3)" value="${stock_qty}" style="width:60%;"   tabindex="-1" readonly/></td> 
                     
                <td><input type="text" name="production_qty[]" id="production_qty" onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)"  class="form-control isNumberKey production_qty" style="width:50px;"  value="${production_qty}" style="width:60px;" ${secondary_unit == "Yes" ? 'readonly' : ''}/></td>

                <td><input type="text" name="unit[]" id="unit" value="${unit}" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly /></td>`;

                thisHtml += `<td><input type="text" name="remarks[]" id="remarks"  value="${remarks}" class="form-control salesmanageTable potableremarks"></td>`;



            }

            `</tr>`;

            counter++;

        }

        jQuery('#itemProductionTable tbody').append(thisHtml);
        setTimeout(() => {
            var counter = 1;

            for (let key in itemProductionDetails) {
                var item_id = itemProductionDetails[key].item_id ? itemProductionDetails[key].item_id : ""
                var item_type = itemProductionDetails[key].item_type ? itemProductionDetails[key].item_type : ""


                jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
                jQuery(`.item_type_${counter}`).val(item_type).trigger('liszt:updated');
                counter++;
            }
            // jQuery(".item_id").trigger('liszt:activate');
        }, 100);
    }
    sumSoQty();
    //  totalAmount();
    srNo();
    disabledDropdownVal();
}

function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);

    });
    // jQuery(".item_id").chosen();
    jQuery(".item_id").chosen({
        search_contains: true
    });

}

function sumSoQty(th) {
    var total = 0;
    jQuery('.production_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            // total = parseInt(total) + parseInt(total1);
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    total != 0 && total != "" ? jQuery('.itemproqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.itemproqtysum').text('');

}


function getItemData(th) {
    let item = th.value;

    var selected = jQuery(th).val();
    var thisselected = jQuery(th);
    if (selected) {
        jQuery(jQuery('.item_id').not(jQuery(th))).each(function (index) {

            if (thisselected.val() == jQuery(this).val()) {
                jAlert('This Item Is Already Selected.');
                var selectTd = thisselected.closest('td');

                selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id" onChange="getItemData(this), sumSoQty(this)">${productDrpHtml}</select>`);
                // jQuery('.item_id').chosen();
                jQuery(".item_id").chosen({
                    search_contains: true
                });
                BlankTrVal(selectTd);
            }
        });
    }

    if (item != "" && item != null) {

        if (jQuery(th).find('option:selected').data('stock_qty') != null) {
            var minQty = isNaN(Number(jQuery(th).find('option:selected').data('stock_qty'))) ? 0 : Number(jQuery(th).find('option:selected').data('stock_qty'));
        } else {
            var minQty = 0;
        }

        jQuery(th).parents('tr').find("#code").val(jQuery(th).find('option:selected').data('item_code'));
        jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit_name'));
        jQuery(th).parents('tr').find("#group").val(jQuery(th).find('option:selected').data('item_group'));
        jQuery(th).parents('tr').find("#item_id").val(item);

        jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");

        jQuery(th).parents('tr').find("#production_qty").prop('readonly', false);
        jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
        jQuery(th).parents('tr').find("#production_qty").prop('tabindex', 0);
        jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);

        if (ItemProductionHiddenId == undefined) {
            jQuery(th).parents('tr').find("#pre_item").val(item);
        } else {
            if (jQuery(th).parents('tr').find("#pre_item").val() == 0) {
                jQuery(th).parents('tr').find("#pre_item").val(item);
            }
        }

    }

    /* if (item != "" && item != null) {
         jQuery.ajax({
             url: RouteBasePath + "/get-fitting_item_data?item=" + item,
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
                     jQuery(th).parents('tr').find("#code").val(data.item.item_code);
                     jQuery(th).parents('tr').find("#item_id").val(data.item.id);
                     jQuery(th).parents('tr').find("#group").val(data.item.item_group_name);
                     jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);
                     // jQuery(th).parents('tr').find("#stock_qty").val(parseFloat(minQty).toFixed(3));
                     jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : '');
                     jQuery(th).parents('tr').find("#production_qty").prop('readonly', false);
                     jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
                     // jQuery(th).parents('tr').find("#production_qty").prop({ tabindex: -1, readonly: false });
                     // jQuery(th).parents('tr').find("#remarks").prop({ tabindex: -1, readonly: false});
                     jQuery(th).parents('tr').find("#production_qty").prop('tabindex', 0);
                     jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);
 
 
                     if (ItemProductionHiddenId == undefined) {
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
     }*/
}


function removeItemProductionDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            jQuery(th).parents("tr").remove();
            srNo();
            var production_qty = jQuery(th).parents('tr').find('#production_qty').val();
            if (production_qty) {
                var item_total = jQuery('.itemproqtysum').text();
                if (item_total != "") {
                    item_final_total = parseFloat(item_total) - parseFloat(production_qty);
                }
                item_final_total > 0 ? jQuery('.itemproqtysum').text(parseFloat(item_final_total).toFixed(3)) : jQuery('.itemproqtysum').text('');
            }
        }
    });
}


function changeItemTypeValue(e) {

    let selectVal = e.value;
    let editVal = e;

    if ((editVal != undefined && editVal == 1) || (selectVal != undefined && selectVal == 1)) {

        jQuery("#supplier_id").prop({ tabindex: -1 }).attr('readonly', true).val('').trigger('liszt:updated');

        jQuery("#issue_type").attr('disabled', false).val('');
        disabledDropdownVal();
    }
    else {
        jQuery("#supplier_id").attr('readonly', false).val();
        jQuery("#issue_type").attr('disabled', true).val('returnable');
    }

}




jQuery('#ip_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();

    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }

    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid IP No.');
            jQuery('#ip_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    // jQuery('#ip_sequence').focus();
                    jQuery('.item_id').trigger('liszt:activate');
                }, 1000);
            });
            jQuery('#ip_sequence').val('');

        } else {


            jQuery("#item_production_button").attr('disabled', true);

            jQuery('#ip_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-ip_no_duplication?for=add&ip_sequence=" + val;

            if (ItemProductionHiddenId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-ip_no_duplication?for=edit&ip_sequence=" + val + "&id=" + ItemProductionHiddenId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#issue_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        jQuery('#ip_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                // jQuery('#ip_sequence').focus();
                                jQuery('.item_id').trigger('liszt:activate');
                            }, 1000);
                        });
                        jQuery('#ip_sequence').val('');
                    } else {
                        jQuery('#ip_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#ip_number').val(data.latest_po_no);
                        jQuery('#ip_sequence').val(val);
                    }
                    jQuery("#item_production_button").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#ip_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#ip_number').val('');
        jQuery('#ip_sequence').val('');
    }
});





// jQuery(document).on('change', '.item_id', function (e) {
//     var selected = jQuery(this).val();
//     var thisselected = jQuery(this);
//     if (selected) {
//         jQuery(jQuery('.item_id').not(jQuery(this))).each(function (index) {
//             if (thisselected.val() == jQuery(this).val()) {
//                 jAlert('This Item Is Already Selected.');
//                 thisselected.replaceWith(`<select name="item_id[]" id="item_id" class="chzn-select chzn-done add_item item_id" onChange="getItemData
//                 (this)">${productDrpHtml}</select>`);
//             }
//         });
//     }
// });