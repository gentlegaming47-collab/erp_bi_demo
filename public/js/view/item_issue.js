
let ItemIssueHiddenId = jQuery('#commonItemIssueForm').find('input:hidden[name="id"]').val();
var po_data = [];
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
                                data-stock_qty="${getItem[0][indx].stock_qty}"
                                data-secondary_unit="${getItem[0][indx].secondary_unit}">
                                ${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}


var ItemTypeDropDown = `    
    <option value="consumable"> Consumable </option>
    <option value="waste/scrap_entry"> Waste/Scrap entry </option>    
    `;




jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };


    //  edit code

    if (ItemIssueHiddenId != null && ItemIssueHiddenId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({

            url: RouteBasePath + "/get-item_issue/" + ItemIssueHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {

                    // setTimeout(() => {
                    //     jQuery('#issue_sequence').focus();
                    // }, 100);

                    jQuery('input:radio[name="issue_type_id_fix"][value="' + data.itemIssue.issue_type_id_fix + '"]').attr('checked', true).trigger('click');
                    jQuery('input:radio[name="issue_type_id_fix"]').prop({ tabindex: -1, readonly: true });

                    if (data.itemIssue.issue_type_id_fix == "1") {
                        jQuery("#supplier_id").prop({ tabindex: -1 }).attr('readonly', true).val('').trigger('liszt:updated');
                        // jQuery("#supplier_id").prop({ tabindex: -1, readonly: true }).val('').trigger('liszt:updated');     
                        disabledDropdownVal();
                    }
                    else {
                        jQuery("#supplier_id").val(data.itemIssue.supplier_id).trigger('liszt:updated');
                    }

                    // if item used return supplier disabled
                    if (data.itemIssue.in_use == true) {


                        jQuery("#supplier_id").val(data.itemIssue.supplier_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                        //
                        jQuery('#issue_sequence').val(data.itemIssue.issue_sequence).prop({ tabindex: -1, readonly: true });
                        jQuery("#issue_date").val(data.itemIssue.issue_date).attr('readonly', true);


                        // jQuery('#issue_sequence').val(data.itemIssue.issue_sequence).attr('readonly', true);
                    } else {
                        jQuery('#supplier_id').val(data.itemIssue.supplier_id).trigger('liszt:updated');
                        jQuery('#issue_sequence').val(data.itemIssue.issue_sequence).prop({ tabindex: -1, readonly: true });
                        jQuery("#issue_date").val(data.itemIssue.issue_date);

                        // setTimeout(() => {
                        //     // jQuery('#issue_date').focus();
                        // }, 100);
                    }
                    setTimeout(() => {
                        // jQuery('#issue_date').focus();
                        jQuery('.item_id_1').trigger('liszt:activate');
                    }, 100);

                    jQuery("#issue_sequence").val(data.itemIssue.issue_sequence);
                    jQuery("#issue_number").val(data.itemIssue.issue_number).prop({ tabindex: -1, readonly: true });

                    jQuery("#special_notes").val(data.itemIssue.special_notes);

                    disabledDropdownVal();

                    fillItemIssueTable(data.itemIssueDetails)

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
            //     jQuery('#issue_sequence').focus();
            // }, 100);

            jQuery('input:radio[name="issue_type_id_fix"][value="1"]').attr('checked', true).trigger('click');
            // jQuery("#supplier_id").prop({ tabindex: -1, readonly: true }).val('').trigger('liszt:updated');
            jQuery("#supplier_id").prop({ tabindex: -1 }).attr('readonly', true).val('').trigger('liszt:updated');

            // jQuery("#supplier_id").attr('readonly', true).val('').trigger('liszt:updated');
            getLatestItemIssueNo();
            addItemDetail();

            setTimeout(() => {
                // jQuery('#issue_date').focus();
                jQuery('.item_id').trigger('liszt:activate');
            }, 1000);
        });
    }



    // jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    //     return this.optional(element) || parseInt(value) > 0;
    // });

    jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
        //formatPoints(element, 3); // Format the value before validation
        //return this.optional(element) || parseFloat(value) >= parseFloat(param);
        return this.optional(element) || parseFloat(value) > 0;

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


    // Store or Update

    var validator = jQuery("#commonItemIssueForm").validate({
        onclick: false,
        rules: {
            onkeyup: false,
            onfocusout: false,

            issue_sequence: {
                required: true
            },

            issue_type_id_fix: {
                required: true
            },

            supplier_id: {
                required: true
            },
            issue_date: {
                required: true,
                date_check: true,
                dateFormat: true
            },
            'item_id[]': {
                required: true
            },
            'item_details_id[]': {
                secUnit: true,
            },
            'issue_type[]': {
                required: true
            },
            'issue_qty[]': {
                required: true,
                notOnlyZero: '0.001',
            },
            // 'item_id[]': {
            //     required: function (e) {
            //         var selectedValue = jQuery("#commonItemIssueForm").find('#supplier_id').val();
            //         var check = jQuery("#commonItemIssueForm").find('input[name="issue_type_id_fix"]:checked').val();

            //         if (selectedValue == undefined || selectedValue == "" && check == "1" && jQuery(e).val().trim() == "") {
            //             jQuery(e).addClass('error');
            //             setTimeout(() => {
            //                 jQuery(e).focus();
            //             }, 1000);
            //             return true;
            //         } else if (selectedValue != undefined && selectedValue != "" && check == "2" && jQuery(e).val().trim() == "") {
            //             jQuery(e).addClass('error');
            //             setTimeout(() => {
            //                 jQuery(e).focus();
            //             }, 1000);
            //             return true;
            //         } else {
            //             jQuery(e).removeClass('error');
            //         }
            //     },
            // },
            // 'item_id[]': {
            //     required: function (e) {
            //         var selectedValue = jQuery("#commonItemIssueForm").find('#supplier_id').val();
            //         var value = jQuery("#commonItemIssueForm").find('#item_id').val();
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
            // 'issue_type[]': {
            //     required: function (e) {
            //         var value = jQuery("#commonItemIssueForm").find('#item_id').val();
            //         var iss_qty = jQuery("#commonItemIssueForm").find('#issue_qty').val();
            //         var iss_type = jQuery("#commonItemIssueForm").find('#issue_type').val();
            //         if (value != "" && iss_qty != "" && iss_type == "") {
            //             jQuery(e).addClass('error');
            //             jQuery(e).focus();
            //             return true;
            //         } else {
            //             jQuery(e).removeClass('error');
            //         }
            //     },
            // },
            // 'issue_qty[]': {
            //     required: function (e) {
            //         if (jQuery("#commonItemIssueForm").find('input[name="item_id[]"]').val() != "" && jQuery("#commonItemIssueForm").find('input[name="issue_qty[]"]').val() == "") {
            //             jQuery(e).addClass('error');
            //             setTimeout(() => {
            //                 jQuery(e).focus();
            //             }, 1000);
            //             //   jQuery(e).focus();
            //             return true;
            //         } else {
            //             jQuery(e).removeClass('error');
            //         }
            //     },
            //     notOnlyZero: '0.001',
            // },
        },

        messages: {

            issue_type_id_fix: {
                required: "Please Select Item  Type"
            },
            issue_sequence: {
                required: "Please Enter Item Issue Number"
            },
            supplier_id: {
                required: "Please Select Supplier"
            },
            issue_date: {
                required: "Please Enter Issue Date.",
            },
            'item_id[]': {
                required: "Please Select Item"
            },
            'item_details_id[]': {
                required: "Please Select Item Detail"
            },
            'issue_type[]': {
                required: "Please Select Item Issue Type"
            },
            'issue_qty[]': {
                required: "Please Enter Issue Qty.",
                notOnlyZero: 'Please Enter A Value Greater Than 0.'
            },


        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },


        submitHandler: function (form) {

            let checkLength = jQuery("#itemIssueTable tbody tr").filter(function () {
                return jQuery(this).css('display') !== 'none';
            }).length;

            //  let checkLength = jQuery("#itemIssueTable tbody tr").length; 
            if (checkLength < 1) {
                jAlert("Please Add At Least One Item Issue Slip Detail.");
                addItemDetail();
                return false;
            }
            jQuery('#item_issue_button').prop('disabled', true);
            var formdata = jQuery('#commonItemIssueForm').serialize();


            let formUrl = ItemIssueHiddenId != undefined && ItemIssueHiddenId != "" ? RouteBasePath + "/update-item_issue" : RouteBasePath + "/store-item_issue";


            jQuery.ajax({

                url: formUrl,

                type: 'POST',

                data: formdata,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if (data.response_code == 1) {

                        if (ItemIssueHiddenId != undefined && ItemIssueHiddenId != null) {
                            // toastPreview(data.response_message, redirectFn, prePO);
                            // function redirectFn() {
                            //     window.location.href = RouteBasePath + "/manage-item_issue";
                            // };
                            // function prePO() {
                            //     id = btoa(data.id);
                            //     window.location.reload();
                            // }

                            toastSuccess(data.response_message, nextFn);
                            function nextFn() {
                                window.location.href = RouteBasePath + "/manage-item_issue";
                            }
                        } else {
                            // toastPreview(data.response_message, redirectFn, prePO);
                            // function redirectFn() {
                            //     window.location.reload();
                            // }
                            // function prePO() {
                            //     id = btoa(data.id);
                            //     window.location.reload();
                            // }
                            toastSuccess(data.response_message, nextFn);
                            //toastPreview(data.response_message, redirectFn, prePO);
                            function nextFn() {
                                window.location.reload();
                            }
                            jQuery('#item_issue_button').prop('disabled', false);
                        }
                    } else {
                        jQuery('#item_issue_button').prop('disabled', false);
                        toastError(data.response_message);
                    }




                },

                error: function (jqXHR, textStatus, errorThrown) {

                    var errMessage = JSON.parse(jqXHR.responseText);



                    if (errMessage.errors) {
                        jQuery('#item_issue_button').prop('disabled', false);
                        validator.showErrors(errMessage.errors);



                    } else if (jqXHR.status == 401) {
                        jQuery('#item_issue_button').prop('disabled', false);
                        jAlert(jqXHR.statusText);


                        // toastError(jqXHR.statusText);

                    } else {

                        jQuery('#item_issue_button').prop('disabled', false);
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
function getLatestItemIssueNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-pending_item_issue_qty",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#po_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#issue_date').val(currentDate);
                jQuery('#issue_number').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });
                jQuery('#issue_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
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
function addItemDetail() {
    // jQuery("#item_issue").attr('disabled', true);

    var grn_type_fix_id = jQuery("input[name*='issue_type_id_fix']:checked").val();
    jQuery("input[name*='issue_type_id_fix']:checked").change(function () {
        changeItemTypeValue(grn_type_fix_id);
    });


    // <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="item_issue_details_id[]" value="0"></td></tr>
    var thisHtml = `
          
    <tr>

    <td>
        <a onclick="removeItemDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
    </td>


    <td class="sr_no"></td>

    <td><input type="hidden" name="item_issue_details_id[]" value="0"> <select name="item_id[]" class="chzn-select  item_id add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select></td>
    
    <td><input type="hidden" name="item_details_id[]" value=""/></td>

    <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

    <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

    <td><input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty"  onblur="formatPoints(this,3)" style="width:60%;"  tabindex="-1" readonly/></td>

    <td>
      <input type="hidden" name="pre_item[]" id="pre_item" value="0"> 
    <input type="text" name="issue_qty[]" tabindex="-1" onblur="formatPoints(this,3)" id="issue_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey issue_qty" style="width:50px;" readonly/></td>
      <td><input type="text" name="unit[]" id="unit"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>`;

    thisHtml += `<td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-done readonlyIssue">
        <option value=""> Select Item Type  </option>
        ${ItemTypeDropDown}
        </select></td>`;

    // if (grn_type_fix_id == 1) {
    //     thisHtml += `<td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-done readonlyIssue">
    //     <option value=""> Select Item Type  </option>
    //     ${ItemTypeDropDown}
    //     </select></td>`
    // } else {
    //     thisHtml += `<td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-done readonlyIssue" tabindex="-1" readonly>
    //     <option value="returnable">  Returnable </option>
    //     </select></td>`
    // }

    thisHtml += `
    
    <td><textarea  name="remarks[]" id="remarks" rows="4" readonly tabindex="-1"/></td>

    </tr>`;
    jQuery('#itemIssueTable tbody').append(thisHtml);


    // srNo();
    setTimeout(() => {
        srNo();
    }, 200);
    sumSoQty();

    // <td><input type="text" name="remarks[]" tabindex="-1" id="remarks"  class="form-control salesmanageTable potableremarks" readonly/></td>

}

function getItemDetailData(th) {
    var selected = jQuery(th).val();
    var thisselected = jQuery(th);
    var selectedOption = jQuery(th).find('option:selected');
    var second_unit = selectedOption.data('second_unit');
    var second_stock_qty = selectedOption.data('second_stock_qty');
    jQuery(th).parents('tr').find("#unit").val(second_unit);
    jQuery(th).parents('tr').find("#stock_qty").val(parseFloat(second_stock_qty).toFixed(3));
    jQuery(th).parents('tr').find("#issue_qty").attr('max', second_stock_qty);
    if (selected == "") {
        jQuery(th).parents('tr').find("#stock_qty").val('');
    }
}


// edit time 
function fillItemIssueTable(itemIssueDetails) {

    var grn_type_fix_id = jQuery("input[name*='issue_type_id_fix']:checked").val();

    var grn_type_fix_id = jQuery("input[name*='issue_type_id_fix']:checked").val();
    jQuery("input[name*='issue_type_id_fix']:checked").change(function () {
        changeItemTypeValue(grn_type_fix_id);
    });

    // changeItemTypeValue(grn_type_fix_id);


    if (itemIssueDetails.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in itemIssueDetails) {

            var sr_no = counter;

            var item_issue_details_id = itemIssueDetails[key].item_issue_details_id ? itemIssueDetails[key].item_issue_details_id : "";

            var item_id = itemIssueDetails[key].item_id ? itemIssueDetails[key].item_id : "";

            var item_code = itemIssueDetails[key].item_code ? itemIssueDetails[key].item_code : "";

            var stock_qty = itemIssueDetails[key].stock_qty ? itemIssueDetails[key].stock_qty : "";

            var item_group_name = itemIssueDetails[key].item_group_name ? itemIssueDetails[key].item_group_name : "";

            var item_details_id = itemIssueDetails[key].item_details_id ? itemIssueDetails[key].item_details_id : "";

            var secondary_stock_qty = itemIssueDetails[key].secondary_stock_qty ? itemIssueDetails[key].secondary_stock_qty : "";

            var sec_unit_name = itemIssueDetails[key].sec_unit_name ? itemIssueDetails[key].sec_unit_name : "";

            var unit_name = itemIssueDetails[key].unit_name ? itemIssueDetails[key].unit_name : "";

            var issue_qty = itemIssueDetails[key].issue_qty ? itemIssueDetails[key].issue_qty : "";

            var used_qty = itemIssueDetails[key].used_qty ? parseFloat(itemIssueDetails[key].used_qty).toFixed(3) : "";

            var in_use = itemIssueDetails[key].in_use ? itemIssueDetails[key].in_use : "";

            var remarks = itemIssueDetails[key].remarks ? checkSpecialCharacter(itemIssueDetails[key].remarks) : "";

            var productDetailDrpHtml = ``;

            if (itemIssueDetails[key].item_detail.length > 0) {
                var productDetailDrpHtml = `<select name="item_details_id[]" class="chzn-select  item_id item_details_ids_${sr_no} add_item_details" ${in_use == true ? 'readonly tabindex="-1"' : ''}  onChange="getItemDetailData(this)"><option value="">Select Item</option>`;
                for (let indx in itemIssueDetails[key].item_detail) {
                    var sec_stock = itemIssueDetails[key].item_detail[indx].secondary_stock_qty ? itemIssueDetails[key].item_detail[indx].secondary_stock_qty : 0.000;
                    var sec_unit = itemIssueDetails[key].item_detail[indx].unit_name ? itemIssueDetails[key].item_detail[indx].unit_name : "";
                    productDetailDrpHtml += `<option value="${itemIssueDetails[key].item_detail[indx].item_details_id}"data-second_stock_qty="${sec_stock}"data-second_unit="${sec_unit}">${itemIssueDetails[key].item_detail[indx].secondary_item_name} </option>`;
                }

                productDetailDrpHtml += `</select>`;
            } else {
                productDetailDrpHtml = `<input type="hidden" name="item_details_id[]" value="" />`;
            }

            // <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="item_issue_details_id[]" value="${item_issue_details_id}"></td></tr>                   
            thisHtml += `
            <tr>
        
            <td>
                <a ${in_use == true ? "" : onclick="removeItemDetails(this)"} ><i class="action-icon iconfa-trash so_details"></i></a>
            </td>
        
        
            <td class="sr_no">${sr_no}</td>
        
            <td><input type="hidden" name="item_issue_details_id[]" value="${item_issue_details_id}">
             <select name="item_id[]"  class="chzn-select  item_id add_item item_id_${sr_no}" onChange="getItemData(this)"  ${in_use == true ? 'readonly tabindex="-1"' : ''}>${productDrpHtml}</select></td>

            <td>${productDetailDrpHtml}</td>
            
            <td>
            <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null ? item_id : 0}">
            <input type="hidden" name="pre_item_detail[]" id="pre_item_detail" value="${item_details_id != null && item_details_id != '' ? item_details_id : ''}">
            <input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" readonly/></td>

            <td>
            <input type="text" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_group_name}" readonly/></td>
            
            `;

            // get the radio button value 


            // it's add code
            if (ItemIssueHiddenId == undefined) {
                thisHtml += `  
                <td><input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" value="${stock_qty}" style="width:60%;"   tabindex="-1" readonly/></td> 

                
                <td><input type="text" name="issue_qty[]" onblur="formatPoints(this,3)" id="issue_qty" onKeyup="sumSoQty(this)"  min="${used_qty}" class="form-control isNumberKey issue_qty" tabindex="-1" readonly/></td>`;

                thisHtml += `<td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-done readonlyIssue">
                <option value=""> Select Item Type  </option>
                ${ItemTypeDropDown}
                </select></td>`;

                // if (grn_type_fix_id == 1) {
                //     thisHtml += `<td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-done readonlyIssue">
                //     <option value=""> Select Item Type  </option>
                //     ${ItemTypeDropDown}
                //     </select></td>`
                // } else {
                //     thisHtml += `<td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-done readonlyIssue" tabindex="-1" readonly>
                //     <option value="returnable">  Returnable </option>
                //     </select></td>`
                // }


                thisHtml += `
                <td>
                <input type="text" name="unit[]" id="unit"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${unit_name}" readonly/></td>

                <td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-  item_type_${sr_no} readonlyIssue" readonly>
                ${ItemTypeDropDown}
                </select></td>

                <td><textarea  name="remarks[]" id="remarks" rows="4" readonly tabindex="-1"/></td>

                `;
                // <td><input type="text" name="remarks[]" tabindex="-1" id="remarks" class="form-control salesmanageTable potableremarks" readonly /></td>
            }
            else {
                thisHtml += ` 

                <input type="hidden" name="org_stock_qty[]" value="${stock_qty != 0 ? stock_qty : 0.000}">                
                <input type="hidden" name="org_issue_qty[]" value="${parseFloat(issue_qty).toFixed(3)}">                
                <td><input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" onblur="formatPoints(this,3)" value="${stock_qty != null ? parseFloat(stock_qty + issue_qty).toFixed(3) : ''}" style="width:60%;"  tabindex="-1" readonly/></td> `;       
               if(item_details_id !="")
               {
                    thisHtml += `   <td><input type="text" name="issue_qty[]" id="issue_qty" onKeyup="sumSoQty(this)"  class="form-control only-numbers issue_qty" style="width:50px;"  value="${issue_qty}" min="${used_qty}" max="${stock_qty + issue_qty}" tabindex="-1" style="width:50px;" ${used_qty == issue_qty || in_use == true ? 'readonly' : ''} /></td>`;
               }
               else{
                 thisHtml += `   <td><input type="text" name="issue_qty[]" id="issue_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey issue_qty" onblur="formatPoints(this,3)" style="width:50px;"  value="${parseFloat(issue_qty).toFixed(3)}" min="${used_qty}" max="${parseFloat(stock_qty + issue_qty).toFixed(3)}" tabindex="-1" style="width:50px;" ${used_qty == issue_qty || in_use == true  ? 'readonly' : ''} /></td>`;

               }
                thisHtml += `
                    <td>
                     <input type="text" name="unit[]" id="unit"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${unit_name}" readonly/></td>

                     <td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-done item_type_${sr_no} readonlyIssue">
                <option value=""> Select Item Type  </option>
                     ${ItemTypeDropDown}
                     </select></td>`;

                // if (grn_type_fix_id == 1) {
                //     thisHtml += `
                //     <td>
                //     <input type="text" name="unit[]" id="unit"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${unit_name}" readonly/></td>

                //     <td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-done item_type_${sr_no} readonlyIssue">
                //     <option value=""> Select Item Type  </option>
                //     ${ItemTypeDropDown}
                //     </select></td>`
                // } else {
                //     thisHtml += `
                //     <td>
                //     <input type="text" name="unit[]" id="unit"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${unit_name}" readonly/></td>

                //     <td> <select name="issue_type[]" id="issue_type" class="chzn-select chzn-done item_type_${sr_no} readonlyIssue" tabindex="-1"  readonly>
                //     <option value="returnable">  Returnable </option>
                //     </select></td>`
                // }







                thisHtml += `
                <td><textarea  name="remarks[]" id="remarks_${itemIssueDetails[key].item_issue_details_id}" rows="4" tabindex="-1"/></td>
                `;
            }
            // <td><input type="text" name="remarks[]" tabindex="-1" id="remarks"  value="${remarks}" class="form-control salesmanageTable potableremarks" ></td>
            `</tr>`;

            counter++;

        }

        jQuery('#itemIssueTable tbody').append(thisHtml);
        setTimeout(() => {
            var counter = 1;

            for (let key in itemIssueDetails) {
                var item_id = itemIssueDetails[key].item_id ? itemIssueDetails[key].item_id : "";
                var item_type = itemIssueDetails[key].item_type ? itemIssueDetails[key].item_type : "";
                var item_details_id = itemIssueDetails[key].item_details_id ? itemIssueDetails[key].item_details_id : "";


                jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
                jQuery(`.item_details_ids_${counter}`).val(item_details_id).trigger('liszt:updated').change();
                jQuery(`.item_type_${counter}`).val(item_type).trigger('liszt:updated');

                var print_po_remarks = itemIssueDetails[key].remarks ? itemIssueDetails[key].remarks : "";
                jQuery(`#remarks_${itemIssueDetails[key].item_issue_details_id}`).val(print_po_remarks);

                counter++;
            }
        }, 100);
    }
    sumSoQty();
    //  totalAmount();
    srNo();
    disabledDropdownVal();
}




// function changeItemTypeValue() {

//     setTimeout(() => {
//         jQuery('#issue_sequence').focus();
//     }, 100);

//     var stgDrpHtml;
//     let selectVal = jQuery('input:radio[name="issue_type_id_fix"]:checked').val();

//     jQuery(".readonlyIssue").each(function () {

//         if (selectVal == 1) {

//             jQuery("#supplier_id").prop({ tabindex: -1 }).attr('readonly', true).val('').trigger('liszt:updated');

//             // jQuery("#supplier_id").prop({ tabindex: -1, readonly: true }).val('').trigger('liszt:updated');
//             // jQuery("#supplier_id").attr('readonly', true).val('').trigger('liszt:updated');

//             jQuery('.readonlyIssue').off('mouseup mousedown');

//             stgDrpHtml = ` <option value=""> Select Item Type  </option>
//              ${ItemTypeDropDown}`;

//             jQuery(this).empty();
//             jQuery(this).append(stgDrpHtml).prop({ tabindex: 0 }).attr('readonly', false);
//             // jQuery(this).append(stgDrpHtml).attr('readonly', false);



//         }
//         else {

//             jQuery('.readonlyIssue').on('mousedown', function (e) {
//                 e.preventDefault();
//                 this.blur();
//                 window.focus();
//             });

//             // jQuery("#supplier_id").prop( {tabindex : -1}).attr('readonly', false).val().trigger('liszt:updated');

//             // jQuery("#supplier_id").prop({ tabindex: -1, readonly: false}).val();
//             jQuery("#supplier_id").attr('readonly', false).val();

//             stgDrpHtml = ` <option value="returnable">  Returnable </option>`;

//             jQuery(this).empty();
//             //    jQuery(this).append(stgDrpHtml).prop({ tabindex: -1, readonly: false });
//             jQuery(this).append(stgDrpHtml).attr('readonly', false);

//             jQuery('.readonlyIssue').prop({ tabindex: -1, readonly: true }).val('returnable');
//             jQuery(".readonlyIssue").attr("readonly", "readonly").val('returnable');
//             //  disabledDropdownVal();
//         }
//     });
// }

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
    jQuery('.issue_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            //  total = parseInt(total) + parseInt(total1);
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    total != 0 && total != "" ? jQuery('.itemqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.itemqtysum').text('');

}


function getItemData(th) {
    let item = th.value;

    var selected = jQuery(th).val();
    var selectedOption = jQuery(th).find('option:selected');
    var secondaryItem = selectedOption.data('secondary_unit');
    var thisselected = jQuery(th);
    if (selected) {
        jQuery(th).parents('tr').find("#issue_qty").val('');
        sumSoQty();
        var $issueQty = jQuery(th).parents('tr').find("#issue_qty");
        if (secondaryItem == 'Yes') {
            $issueQty.removeAttr("onblur");                
            $issueQty.removeClass("isNumberKey").addClass("only-numbers"); 
        } else {
            $issueQty.attr("onblur", "formatPoints(this,3)");
            $issueQty.removeClass("only-numbers").addClass("isNumberKey");
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
    }

    if (item != "" && item != null) {

        if (jQuery(th).find('option:selected').data('stock_qty') != null) {
            var minQty = isNaN(Number(jQuery(th).find('option:selected').data('stock_qty'))) ? 0 : Number(jQuery(th).find('option:selected').data('stock_qty'));
        } else {
            var minQty = 0;
        }

        jQuery(th).parents('tr').find("#code").val(jQuery(th).find('option:selected').data('item_code')); // Enable the input field
        jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit_name'));// Enable the input field
        jQuery(th).parents('tr').find("#group").val(jQuery(th).find('option:selected').data('item_group')); // Enable the input field
        jQuery(th).parents('tr').find("#item_id").val(item); // Enable the input field

        jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");
        jQuery(th).parents('tr').find("#issue_qty").attr('max', minQty);
        jQuery(th).parents('tr').find("#issue_qty").prop('readonly', false);
        jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
        jQuery(th).parents('tr').find("#issue_qty").prop('tabindex', 0);
        jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);

        if (ItemIssueHiddenId == undefined) {
            jQuery(th).parents('tr').find("#pre_item").val(item);
        } else {
            if (jQuery(th).parents('tr').find("#pre_item").val() == 0) {
                jQuery(th).parents('tr').find("#pre_item").val(item);
            }
        }

    }

    // if (item != "" && item != null) {
    //     jQuery.ajax({
    //         url: RouteBasePath + "/get-fitting_item_data?item=" + item,
    //         type: 'GET',
    //         headers: headerOpt,
    //         dataType: 'json',
    //         processData: false,
    //         success: function (data) {
    //             if (data.response_code == 1) {
    //                 if (data.stock_qty != null) {

    //                     var minQty = isNaN(Number(data.stock_qty.stock_qty)) ? 0 : Number(data.stock_qty.stock_qty);
    //                 } else {
    //                     var minQty = 0;
    //                 }
    //                 jQuery(th).parents('tr').find("#code").val(data.item.item_code);
    //                 jQuery(th).parents('tr').find("#item_id").val(data.item.id);
    //                 jQuery(th).parents('tr').find("#group").val(data.item.item_group_name);
    //                 jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);
    //                 // jQuery(th).parents('tr').find("#stock_qty").val(parseFloat(minQty).toFixed(3));
    //                 jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");
    //                 jQuery(th).parents('tr').find("#issue_qty").attr('max', minQty);
    //                 jQuery(th).parents('tr').find("#issue_qty").prop('readonly', false);
    //                 jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
    //                 // jQuery(th).parents('tr').find("#issue_qty").prop({ tabindex: -1, readonly: false });
    //                 // jQuery(th).parents('tr').find("#remarks").prop({ tabindex: -1, readonly: false });
    //                 jQuery(th).parents('tr').find("#issue_qty").prop('tabindex', 0);
    //                 jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);


    //                 if (ItemIssueHiddenId == undefined) {
    //                     jQuery(th).parents('tr').find("#pre_item").val(item);
    //                 } else {
    //                     if (jQuery(th).parents('tr').find("#pre_item").val() == 0) {
    //                         jQuery(th).parents('tr').find("#pre_item").val(item);
    //                     }
    //                 }



    //             } else {
    //                 jQuery('#code').val('');
    //                 jQuery('#item_id').val('');
    //                 jQuery('#group').val('');
    //                 jQuery('#unit').val('');
    //                 jQuery('#po_qty').val('');
    //                 jQuery('#rate_unit').val('');
    //                 jQuery('#remarks').val('');
    //             }
    //         },
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
                    if (data.stock_qty != null) {

                        var minQty = isNaN(Number(data.stock_qty.stock_qty)) ? 0 : Number(data.stock_qty.stock_qty);
                    } else {
                        var minQty = 0;
                    }

                    var productDetailDrpHtml = ``;
                    if (data.item_detail.length > 0) {
                        var productDetailDrpHtml = `<select name="item_details_id[]" class="chzn-select  item_id item_details_id add_item_details" onChange="getItemDetailData(this)"><option value="">Select Item</option>`;
                        for (let indx in data.item_detail) {
                            var sec_stock = data.item_detail[indx].secondary_stock_qty ? data.item_detail[indx].secondary_stock_qty : 0.000;
                            var sec_unit = data.item_detail[indx].unit_name ? data.item_detail[indx].unit_name : "";
                            productDetailDrpHtml += `<option value="${data.item_detail[indx].item_details_id}" data-second_stock_qty="${sec_stock}"data-second_unit="${sec_unit}">${data.item_detail[indx].secondary_item_name} </option>`;
                        }

                        productDetailDrpHtml += `</select>`;
                    } else {
                        productDetailDrpHtml = `<input type="hidden" name="item_details_id[]" value="" />`;
                    }


                    jQuery(th).parents('tr').find('td').eq(jQuery(th).closest('td').index() + 1).html(productDetailDrpHtml);
                    jQuery('.item_details_id').chosen();

                    jQuery(th).parents('tr').find("#code").val(data.item.item_code);
                    jQuery(th).parents('tr').find("#item_id").val(data.item.id);
                    jQuery(th).parents('tr').find("#group").val(data.item.item_group_name);
                    jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);
                    // jQuery(th).parents('tr').find("#stock_qty").val(parseFloat(minQty).toFixed(3));
                    jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");
                    jQuery(th).parents('tr').find("#issue_qty").attr('max', minQty);
                    jQuery(th).parents('tr').find("#issue_qty").prop('readonly', false);
                    jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
                    // jQuery(th).parents('tr').find("#issue_qty").prop({ tabindex: -1, readonly: false });
                    // jQuery(th).parents('tr').find("#remarks").prop({ tabindex: -1, readonly: false });
                    jQuery(th).parents('tr').find("#issue_qty").prop('tabindex', 0);
                    jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);


                    if (ItemIssueHiddenId == undefined) {
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



function removeItemDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        let checkLength = jQuery("#itemIssueTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        //  let checkLength = jQuery("#itemIssueTable tbody tr").length; 
        // if (checkLength > 1) {
        if (r === true) {

            let issuePartId = jQuery(th).closest('tr').prev('tr').find('input[name="item_issue_details_id[]"]').val();
            if (issuePartId != '') {

                jQuery.ajax({
                    url: RouteBasePath + "/check-issue_part_in_use?issue_part_id=" + issuePartId + "&issue_id=" + ItemIssueHiddenId,
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
                            var issue_qty = jQuery(th).parents('tr').find('#issue_qty').val();
                            //var po_amt = jQuery(th).parents('tr').find('#amount').val();

                            if (issue_qty) {
                                var item_total = jQuery('.itemqtysum').text();
                                // var amt_total = jQuery('.amountsum').text();
                                if (item_total != "") {
                                    item_final_total = parseFloat(item_total) - parseFloat(issue_qty);
                                    //amt_final_total = parseInt(amt_total) - parseInt(po_amt);
                                }

                                item_final_total > 0 ? jQuery('.itemqtysum').text(parseFloat(item_final_total).toFixed(3)) : jQuery('.itemqtysum').text('');


                                // jQuery('.itemqtysum').text(parseFloat(item_final_total).toFixed(3));
                            }
                            // } else {
                            //     jAlert("Please At Least Item List Item Required");
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

                jQuery(th).parents("tr").remove();
                srNo();
                var issue_qty = jQuery(th).parents('tr').find('#issue_qty').val();
                //var po_amt = jQuery(th).parents('tr').find('#amount').val();

                if (issue_qty) {
                    var item_total = jQuery('.itemqtysum').text();
                    // var amt_total = jQuery('.amountsum').text();
                    if (item_total != "") {
                        item_final_total = parseFloat(item_total) - parseFloat(issue_qty);
                        //amt_final_total = parseInt(amt_total) - parseInt(po_amt);
                    }
                    jQuery('.itemqtysum').text(parseFloat(item_final_total).toFixed(3));
                }
                //jQuery('.amountsum').text(amt_final_total);

            }

        }
        // }
        // else {
        //     jAlert("Please At Least Item List Item Required");
        // }
    });
}







jQuery('#issue_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();

    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }

    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid Item Issue No.');
            jQuery('#issue_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    jQuery('#issue_sequence').focus();
                }, 1000);
            });
            jQuery('#issue_sequence').val('');

        } else {


            jQuery("#item_issue_button").attr('disabled', true);

            jQuery('#issue_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-item_issue_no_duplication?for=add&issue_sequence=" + val;

            if (ItemIssueHiddenId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-item_issue_no_duplication?for=edit&issue_sequence=" + val + "&id=" + ItemIssueHiddenId;
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
                        jQuery('#issue_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                jQuery('#issue_sequence').focus();
                            }, 1000);
                        });
                        jQuery('#issue_sequence').val('');
                    } else {
                        jQuery('#issue_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#issue_number').val(data.latest_po_no);
                        jQuery('#issue_sequence').val(val);
                    }
                    jQuery("#item_issue_button").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#issue_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#issue_number').val('');
        jQuery('#issue_sequence').val('');
    }
});


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
jQuery(document).ready(function () {
    function updateTitle() {
        if (jQuery("#commonItemIssueForm").find('input[name="issue_type_id_fix"]:checked').val() == "2") {
            jQuery(".supplierTitle").html('Supplier <sup class="astric"> *</sup>');
        } else {
            jQuery(".supplierTitle").html('Supplier');
        }
    }
    updateTitle();
    jQuery("#commonItemIssueForm").find('input[name="issue_type_id_fix"]').change(function () {
        updateTitle();
    });
});



