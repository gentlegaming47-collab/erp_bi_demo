let formId = jQuery('#commonPRForm').find('input:hidden[name="id"]').val();
var po_data = [];
var material_data = [];

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
        productDrpHtml += `<option value="${getItem[0][indx].id}" data-item_code="${getItem[0][indx].item_code}" data-unit_name="${getItem[0][indx].unit_name}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}

var old_supplier_id = '';

jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

    //  edit code

    if (formId != null && formId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');


        jQuery.ajax({

            url: RouteBasePath + "/get-purchase_requisition/" + formId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {
                if (data.response_code == 1) {
                    // setTimeout(() => {
                        jQuery('#person').focus();
                    // }, 100);

                    jQuery('input:radio[name="pr_form_id_fix"][value="' + data.pr_data.pr_form_id_fix + '"]').attr('checked', true).trigger('click');

                    jQuery('#pr_form_id_fix').val(data.pr_data.pr_form_id_fix);



                    jQuery("#pr_sequence").val(data.pr_data.pr_sequence);
                    jQuery("#pr_number").val(data.pr_data.pr_number).prop({ tabindex: -1, readonly: true });
                    jQuery("#pr_date").val(data.pr_data.pr_date);
                    jQuery("#supplier_id").val(data.pr_data.supplier_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
                    jQuery("#supplier_id").trigger('change');

                    // prTypeFix(data.pr_data.pr_form_id_fix);
                    // return;
                    prTypeFix();




                    jQuery("#special_notes").val(data.pr_data.special_notes);
                    jQuery("#prepared_by").val(data.pr_data.prepared_by);
                    jQuery('#commonPRForm').find('#id').val(data.pr_data.pr_id);


                    if (data.pr_data.supplier_id == 0) {
                        if (data.pr_data.pr_form_id_fix == 1) {
                            oldPRTable();

                            fillSupplierPrDetailsTable(data.pr_details);
                        }

                        old_supplier_id = 0;

                    } else {
                        newPRTable();

                        fillPrDetailsTable(data.pr_details);
                        getItemsfromMapping();

                        old_supplier_id = '';
                    }


                    if (data.pr_data.in_use == true) {
                        jQuery("#pr_sequence").prop({ tabindex: -1, readonly: true });
                        jQuery("#pr_date").prop({ tabindex: -1, readonly: true });
                        // jQuery('#addPart').prop('disabled', true);
                        jQuery("#special_notes").prop({ tabindex: -1, readonly: true });
                        jQuery("#prepared_by").prop({ tabindex: -1, readonly: true });


                    }

                    // jQuery('#pr_date').focus();
                    // if (data.pr_data.pr_form_id_fix == 1) {
                    //     setTimeout(() => {
                    //         jQuery("#supplier_id").trigger('liszt:activate');
                    //     }, 100);
                    // }
                    // else if (data.pr_data.pr_form_id_fix == 2) {
                    //     setTimeout(() => {
                    //         jQuery("#pr_location_id").trigger('liszt:activate');
                    //     }, 100);
                    // }

                    jQuery("input[name*='pr_form_id_fix']").prop({ tabindex: -1 }).attr('readonly', true);

                    if (data.pr_data.pr_form_id_fix == 2 && data.pr_data.pr_form_value_fix == "from_location") {


                        for (let key in data.pr_details) {
                            material_data.push(data.pr_details[key]);

                        }
                        setTimeout(() => {
                            fillPendingMaterialData();
                        }, 1000);

                        fillPendingMaterialTable();


                        jQuery("#pr_location_id").prop({ tabindex: -1, readonly: true });

                        setTimeout(() => {
                            jQuery('#sup_rejection_button').prop('disabled', false);
                        }, 1200);

                        if (data.pr_data.in_use == true) {
                            jQuery('.toggleModalBtn').prop('disabled', true);
                        } else {
                            jQuery('.toggleModalBtn').prop('disabled', false);

                        }




                    } else {
                        jQuery('#sup_rejection_button').prop('disabled', false);
                        jQuery('.toggleModalBtn').prop('disabled', true);

                    }


                    if (data.pr_data.pr_form_id_fix == 2) {
                        getLocationForPR().done(function () {

                            jQuery("#pr_location_id").val(data.pr_data.to_location_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
                            jQuery("#pr_location_id").trigger('change');
                        });

                        setTimeout(() => {
                            // jQuery('#qc_date').focus();
                            jQuery(".toggleModalBtn").last().focus();
                        }, 1000);


                    } else {
                        jQuery(`.item_id_1`).trigger('liszt:activate');
                    }


                    jQuery('#show-progress').removeClass('loader-progress-whole-page');


                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-purchase_requisition";
                    });
                }
            },

            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);

                if (jqXHR.status == 401) {
                    jAlert(jqXHR.statusText);
                } else {
                    jAlert('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
    else {
        jQuery(document).ready(function () {  // at add time get the se. number            
            // setTimeout(() => {
            //     jQuery('#pr_sequence').focus();
            // }, 100);
            getLatestPRNo();
            // addPrDetail();
            getItemsfromMapping();
            // jQuery("#pr_date").focus();

            var supplier_id = jQuery('input[name="supplier_id"]:checked').val();
            var pr_location_id = jQuery('input[name="pr_location_id"]:checked').val();
            if (supplier_id == "1") {
                setTimeout(() => {
                    jQuery("#supplier_id").trigger('liszt:activate');
                }, 100);
            }
            else if (pr_location_id == "2") {
                setTimeout(() => {
                    jQuery("#pr_location_id").trigger('liszt:activate');
                }, 100);
            }
            else {
                jQuery("#supplier_id").trigger('liszt:activate');
            }
        });
    }

    jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
        return this.optional(element) || parseFloat(value) >= 0.001;
    });

    // validation for rate
    jQuery.validator.addMethod("salesRate", function (value, element, param) {
        //formatPoints(element, 3); // Format the value before validation
        //return this.optional(element) || parseFloat(value) >= parseFloat(param);
        return this.optional(element) || parseFloat(value) > 0;
    }, "Please Enter Rate/Unit Greater Than 0.00");

    // Store or Update
    var validator = jQuery("#commonPRForm").validate({
        ignore: [],
        onclick: false,
        // onkeyup: false,
        onfocusout: false,
        rules: {
            pr_sequence: {
                required: true
            },
            supplier_id: {
                required: function (e) {

                    if (jQuery("#commonPRForm").find('input[name="pr_form_id_fix"]:checked').val() == "1") {
                        return true;
                    } else {
                        return false;
                    }
                }
            },
            pr_date: {
                required: true,
                date_check: true,
                dateFormat: true
            },
            'item_id[]': {
                required: true
            },
            'req_qty[]': {
                required: true,
                notOnlyZero: '0.001',
            },
            'rate_per_unit[]': {
                // required: true,
                salesRate: '0.01',
            },
            'supplier_id[]': {
                required: function (e) {

                    if (jQuery("#commonPRForm").find('input[name="pr_form_id_fix"]:checked').val() == "2") {
                        return true;
                    } else {
                        return false;
                    }
                },
            },
            pr_location_id: {
                required: function (e) {

                    if (jQuery("#commonPRForm").find('input[name="pr_form_id_fix"]:checked').val() == "2") {
                        return true;
                    } else {
                        return false;
                    }
                },
            },
            prepared_by: {
                required: true,
            },
        },

        messages: {

            pr_sequence: {
                required: "Please Enter PR. Number"
            },
            pr_date: {
                required: "Please Enter PR. Date.",
            },
            supplier_id: {
                required: "Please Select Supplier"
            },
            'item_id[]': {
                required: "Please Select Item"
            },
            'req_qty[]': {
                required: "Please Enter Req. Qty.",
                notOnlyZero: 'Please Enter Req. Qty. Greater Than 0.'
            },
            'rate_per_unit[]':
            {
                // required: "Please Enter Rate Per Unit.",
                salesRate: 'Please Enter A Value Greater Than 0.00.'
            },
            'supplier_id[]': {
                required: "Please Select Supplier"
            },

            prepared_by: {
                required: "Please Enter Prepared By"
            },
            pr_location_id: {
                required: "Please Select Location"
            }

        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            let checkLength = jQuery("#purchase_requisition_table tbody tr").filter(function () {
                return jQuery(this).css('display') !== 'none';
            }).length;

            if (checkLength < 1) {
                jAlert("Please Add At Least One Purchase Requisition Detail.");
                addPrDetail();
                return false;
            }

            // // Check for duplicate item & supplier combination
            // let itemSupplierPairs = {};
            // let isDuplicate = false;
            // let duplicateRow = null;


            // jQuery("#purchase_requisition_table tbody tr").each(function () {
            //     let item = jQuery(this).find("[name='item_id[]']").val();
            //     let supplier = jQuery(this).find("[name='supplier_id[]']").val();

            //     if (item && supplier) {
            //         let key = item + "_" + supplier; // Unique key for each pair
            //         if (itemSupplierPairs[key]) {
            //             isDuplicate = true;
            //             duplicateRow = supplier;
            //             return false; // Break loop
            //         } else {
            //             itemSupplierPairs[key] = true;
            //         }
            //     }
            // });

            // if (isDuplicate) {
            //     jAlert("The same supplier cannot be added twice for the same item.");
            //     jQuery(duplicateRow).val("").focus().addClass('error');
            //     //toastElement("The same supplier cannot be added twice for the same item",duplicateRow);

            //     return false;
            // }

            jQuery('#PrButton').prop('disabled', true);
            var formdata = jQuery('#commonPRForm').serialize();


            let formUrl = formId != undefined && formId != "" ? RouteBasePath + "/update-purchase_requisition" : RouteBasePath + "/store-purchase_requisition";


            jQuery.ajax({

                url: formUrl,

                type: 'POST',

                data: formdata,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if (data.response_code == 1) {

                        if (formId != undefined && formId != null) {
                            toastSuccess(data.response_message, nextFn);
                            function nextFn() {
                                window.location.href = RouteBasePath + "/manage-purchase_requisition";
                            }
                        } else {
                            toastSuccess(data.response_message, nextFn);
                            function nextFn() {
                                window.location.reload();
                            }

                            jQuery('#PrButton').prop('disabled', false);
                        }
                    } else {
                        jQuery('#PrButton').prop('disabled', false);
                        toastError(data.response_message);
                    }
                },

                error: function (jqXHR, textStatus, errorThrown) {
                    var errMessage = JSON.parse(jqXHR.responseText);
                    if (errMessage.errors) {
                        jQuery('#PrButton').prop('disabled', false);
                        validator.showErrors(errMessage.errors);
                    }
                    else if (jqXHR.status == 401) {
                        jQuery('#PrButton').prop('disabled', false);
                        jAlert(jqXHR.statusText);
                    }
                    else {
                        jQuery('#PrButton').prop('disabled', false);
                        jAlert('Something went wrong!');
                        console.log(JSON.parse(jqXHR.responseText));
                    }
                }
            });
        }
    });

    // prTypeFix(1);
    prTypeFix();
});

// add time 
function addPrDetail() {
    if (formId != undefined && formId != '') {

        if (old_supplier_id == '0') {
            var thisHtml = `<tr>    
            <td><a onclick="removePrDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>
                
            <td class="sr_no"></td>
            <td> <select name="item_id[]" class="chzn-select add_item item_id" onchange="getItemData(this)">${productDrpHtml}</select></td>
        
            <td><input type="hidden" name="pr_details_id[]" value="0"><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>
        
            <td><input type="text" name="req_qty[]" onblur="formatPoints(this,3)" id="req_qty"  class="form-control isNumberKey req_qty" onKeyup="sumReqQty(this)" tabindex="-1" style="width:50px;" disabled /></td>
        
            <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly/></td>
        
            <td>
                <select name="supplier_id[]" class="chzn-select supplier_id" readonly>
                    <option value="">Select Supplier </option>
                </select>
            </td>

            <td><input type="text" name="rate_per_unit[]" id="rate_per_unit"  class="form-control  rate_unit  isNumberKey"  onblur="formatPoints(this,3)"  style="width:60px;" tabindex="-1"  readonly/></td>
        
            <td><textarea  name="remarks[]" id="remarks" rows="4" tabindex="-1"  readonly/></td>
            
            </tr>`;

        } else {
            var thisHtml = `<tr>
            <td><a onclick="removePrDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>  

            <td class="sr_no"></td>

            <td> <select name="item_id[]" class="chzn-select add_item item_id" onchange="getItemData(this)">${productDrpHtml}</select></td> 
            
            <td><input type="hidden" name="pr_details_id[]" value="0"><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>
        
            <td><input type="text" name="req_qty[]" onblur="formatPoints(this,3)" id="req_qty"  class="form-control isNumberKey req_qty" onKeyup="sumReqQty(this)" tabindex="-1" style="width:50px;" disabled /></td>
        
            <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly/></td>
        
            <td><input type="text" name="rate_per_unit[]" id="rate_per_unit"  class="form-control  rate_unit  isNumberKey"  onblur="formatPoints(this,3)"  style="width:60px;" tabindex="-1"  readonly/></td>

            <td><textarea  name="remarks[]" id="remarks" rows="4" tabindex="-1"  readonly/></td>
            
            </tr>`;
        }



    } else {

        var thisHtml = `<tr>
        <td><a onclick="removePrDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>  

        <td class="sr_no"></td>

        <td> <select name="item_id[]" class="chzn-select add_item item_id" onchange="getItemData(this)">${productDrpHtml}</select></td> 
          
        <td><input type="hidden" name="pr_details_id[]" value="0"><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>
    
        <td><input type="text" name="req_qty[]" onblur="formatPoints(this,3)" id="req_qty"  class="form-control isNumberKey req_qty" onKeyup="sumReqQty(this)" tabindex="-1" style="width:50px;" disabled /></td>
    
        <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly/></td>
    
       <td><input type="text" name="rate_per_unit[]" id="rate_per_unit"  class="form-control  rate_unit  isNumberKey"  onblur="formatPoints(this,3)"  style="width:60px;" tabindex="-1"  readonly/></td>

        <td><textarea  name="remarks[]" id="remarks" rows="4" tabindex="-1"  readonly/></td>
        
        </tr>`;
    }


    jQuery('#purchase_requisition_table tbody').append(thisHtml);

    setTimeout(() => {
        srNo();
    }, 200);
    // srNo();
    sumReqQty();

}

// get the latest number
function getLatestPRNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_pr_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#pr_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#pr_date').val(currentDate);
                jQuery('#pr_number').val(data.latest_pr_no).prop({ tabindex: -1, readonly: true });
                jQuery('#pr_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#pr_number').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}

function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
    // jQuery(".item_id").chosen();
    jQuery(".item_id").chosen({
        search_contains: true
    });
    jQuery(".supplier_id").chosen();
}

function removePrDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        if (r === true) {
            jQuery(th).parents("tr").remove();
            srNo();
            var req_qty = jQuery(th).parents('tr').find('#req_qty').val();

            if (req_qty) {
                var req_total = jQuery('.prqtysum').text();

                if (req_total) {
                    pr_final_total = parseFloat(req_total) - parseFloat(req_qty);
                }

                pr_final_total > 0 ? jQuery('.prqtysum').text(parseFloat(pr_final_total).toFixed(3)) : jQuery('.prqtysum').text('');

            }
        }
    });
}

function sumReqQty(th) {
    var total = 0;
    jQuery('.req_qty').map(function () {
        var total1 = jQuery(this).val();
        if (total1 != "") {
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    total != 0 && total != "" ? jQuery('.prqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.prqtysum').text('');

}


function getItemData(th) {
    let item = th.value;

    var selected = jQuery(th).val();
    var thisselected = jQuery(th);
    var radioval = jQuery(".pr_form_id_fix").val();

    if (selected && radioval != 1) {
        jQuery(jQuery('.item_id').not(jQuery(th))).each(function (index) {

            if (thisselected.val() == jQuery(this).val()) {
                jAlert('This Item Is Already Selected.');
                var selectTd = thisselected.closest('td');

                selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select>`);
                // jQuery('.item_id').chosen();
                jQuery(".item_id").chosen({
                    search_contains: true
                });
                BlankTrVal(selectTd);
            }
        });
    }


    if (item != "" && item != null) {
        jQuery(th).parents('tr').find("#req_qty").prop('disabled', false);
        jQuery(th).parents('tr').find("#rate_per_unit").prop('readonly', false);
        jQuery(th).parents('tr').find("#remarks").attr('readonly', false);

        jQuery(th).parents('tr').find("#req_qty").prop('tabindex', 0);
        jQuery(th).parents('tr').find("#rate_per_unit").prop('tabindex', 0);
        jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);

        jQuery(th).parents('tr').find("#code").val(jQuery(th).find('option:selected').data('item_code'));
        jQuery(th).parents('tr').find("#item_id").val(item);
        jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit_name'));
    }

    if (old_supplier_id == '0') {

        if (item != "" && item != null) {
            jQuery.ajax({
                url: RouteBasePath + "/get-item_supplier_pr_data?item=" + item,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        //jQuery(th).parents('tr').find("#code").val(data.item.item_code);
                        //jQuery(th).parents('tr').find("#item_id").val(data.item.id);
                        //jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);
                        // jQuery(th).parents('tr').find("#code").val(jQuery(th).find('option:selected').data('item_code')); // Enable the input field
                        // jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit_name')); // Enable the input field
                        // jQuery(th).parents('tr').find("#item_id").val(item); // Enable the input field

                        // jQuery(th).parents('tr').find("#req_qty").prop('disabled', false);
                        // jQuery(th).parents('tr').find("#req_qty").prop('tabindex', 0);
                        // jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);


                        // jQuery(th).parents('tr').find("#remarks").attr('readonly', false);
                        if (data.suppliers.length > 0) {
                            var supplierDrpHtml = `<option value="">Select Supplier</option>`;
                            for (let indx in data.suppliers) {
                                supplierDrpHtml += `<option value="${data.suppliers[indx].supplier_id}">${data.suppliers[indx].supplier_name} </option>`;
                            }
                        } else {
                            var supplierDrpHtml = `<option value="">Select Supplier</option>`;
                        }
                        jQuery(th).parents('tr').find(".supplier_id").html(supplierDrpHtml).attr('readonly', false).trigger('liszt:updated');

                    }
                },

                error: function (xhr) {
                    if (xhr.status === 500) {

                        setTimeout(() => {
                            getItemData(th);
                        }, 1500);
                    }
                }
            });
        }

    }


}


// edit time 
function fillPrDetailsTable(pr_details) {

    if (pr_details.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in pr_details) {

            var sr_no = counter;

            var pr_details_id = pr_details[key].pr_details_id ? pr_details[key].pr_details_id : "";

            var item_id = pr_details[key].item_id ? pr_details[key].item_id : "";

            var item_code = pr_details[key].item_code ? pr_details[key].item_code : "";

            var req_qty = pr_details[key].req_qty ? pr_details[key].req_qty.toFixed(2) : "";

            var unit_name = pr_details[key].unit_name ? pr_details[key].unit_name : "";

            var supplier_id = pr_details[key].supplier_id ? pr_details[key].supplier_id : "";

            var in_use = pr_details[key].in_use ? pr_details[key].in_use : "";

            var remarks = pr_details[key].remarks ? checkSpecialCharacter(pr_details[key].remarks) : "";

            var used_qty = pr_details[key].used_qty ? pr_details[key].used_qty.toFixed(2) : "";

            var rate_per_unit = pr_details[key].rate_per_unit ? pr_details[key].rate_per_unit.toFixed(3) : "";

            // var supplierDrpHtml = `<option value="">Select Supplier</option>`;
            // if (pr_details[key].suppliers.length) {
            //     for (let indx in pr_details[key].suppliers) {
            //         supplierDrpHtml += `<option value="${pr_details[key].suppliers[indx].supplier_id}">${pr_details[key].suppliers[indx].supplier_name} </option>`;
            //     }
            // }

            var productDrpHtml = `<option value="">Select Item</option>`;

            if (pr_details[key].items.length) {
                for (let indx in pr_details[key].items) {
                    productDrpHtml += `<option value="${pr_details[key].items[indx].id}" data-item_code="${pr_details[key].items[indx].item_code}" data-unit_name="${pr_details[key].items[indx].unit_name}">${pr_details[key].items[indx].item_name} </option>`;
                }
            }



            thisHtml += `
                            
            <tr>
        
            <td>
                <a  ${in_use == true ? '' : 'onclick="removePrDetails(this)"'}"><i class="action-icon iconfa-trash so_details"></i></a>
                
            </td>
        
            <td class="sr_no">${sr_no}</td>
        
            <td> <select name="item_id[]"  id="item_id_${counter}"  class="chzn-select item_id_${sr_no} item_id" onChange="getItemData(this)" ${in_use == true ? 'readonly  tabindex="-1"' : ''}>${productDrpHtml}</select></td>

                <td>
            <input type="hidden" name="pr_details_id[]" value="${pr_details_id}">
            <input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" readonly/></td> 
                
            <td><input type="text" name="req_qty[]" id="req_qty" onblur="formatPoints(this,3)" min="${used_qty}" onKeyup="sumReqQty(this)"  class="form-control allow-desimal req_qty" value="${req_qty}" style="width:50px;" ${in_use == true ? 'readonly  tabindex="-1"' : ''}/></td>
            
            <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" value="${unit_name}" readonly/></td>            

             <td><input type="text" name="rate_per_unit[]" id="rate_per_unit"  class="form-control  rate_unit  isNumberKey"  onblur="formatPoints(this,3)"  style="width:60px;" value="${rate_per_unit}"/></td>

          

            <td><textarea  name="remarks[]" id="remarks_${counter}" rows="4">${remarks}</textarea></td>
           
            </tr>`;

            counter++;
        }

        // <td>
        //     <select name="supplier_id[]"  id="supplier_id_${counter}"  class="chzn-select supplier_id"  ${in_use == true ? 'readonly tabindex="-1"' : ''}>${supplierDrpHtml}
        //     </select>
        // </td>


        jQuery('#purchase_requisition_table tbody').append(thisHtml);


        var counter = 1;
        for (let key in pr_details) {
            var item_id = pr_details[key].item_id ? pr_details[key].item_id : "";
            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            counter++;
        }

        // async function updateFields() {
        //     var counter = 1;
        //     for (let key in pr_details) {
        //         var item_id = pr_details[key].item_id ? pr_details[key].item_id : "";
        //         // jQuery(`.item_id_${counter}`).val(item_id).change();
        //         jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
        //         counter++;
        //     }

        //     await new Promise(resolve => setTimeout(resolve, 100)); // Wait for 100ms before moving to the next part

        //     counter = 1;
        //     for (let key in pr_details) {
        //         var supplier_id = pr_details[key].supplier_id ? pr_details[key].supplier_id : "";
        //         jQuery(`#supplier_id_${counter}`).val(supplier_id).trigger('liszt:updated');
        //         counter++;
        //     }
        // }
        // updateFields();
        // updateFields(pr_details);


    }
    sumReqQty();
    srNo();
}



// edit time 
function fillSupplierPrDetailsTable(pr_details) {

    if (pr_details.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in pr_details) {

            var sr_no = counter;

            var pr_details_id = pr_details[key].pr_details_id ? pr_details[key].pr_details_id : "";
            var item_id = pr_details[key].item_id ? pr_details[key].item_id : "";
            var item_code = pr_details[key].item_code ? pr_details[key].item_code : "";
            var req_qty = pr_details[key].req_qty ? pr_details[key].req_qty.toFixed(2) : "";
            var unit_name = pr_details[key].unit_name ? pr_details[key].unit_name : "";
            var supplier_id = pr_details[key].supplier_id ? pr_details[key].supplier_id : "";
            var in_use = pr_details[key].in_use ? pr_details[key].in_use : "";
            var remarks = pr_details[key].remarks ? checkSpecialCharacter(pr_details[key].remarks) : "";
            var used_qty = pr_details[key].used_qty ? pr_details[key].used_qty.toFixed(2) : "";
            var rate_per_unit = pr_details[key].rate_per_unit ? pr_details[key].rate_per_unit.toFixed(3) : "";

            var supplierDrpHtml = `<option value="">Select Supplier</option>`;
            if (pr_details[key].suppliers.length) {
                for (let indx in pr_details[key].suppliers) {
                    supplierDrpHtml += `<option value="${pr_details[key].suppliers[indx].supplier_id}">${pr_details[key].suppliers[indx].supplier_name} </option>`;
                }
            }

            thisHtml += `<tr>        
            <td><a  ${in_use == true ? '' : 'onclick="removePrDetails(this)"'}"><i class="action-icon iconfa-trash so_details"></i></a></td>

            <td class="sr_no">${sr_no}</td>

            <td> <select name="item_id[]"  id="item_id_${counter}"  class="chzn-select item_id_${sr_no} item_id" onChange="getItemData(this)" ${in_use == true ? 'readonly  tabindex="-1"' : ''}>${productDrpHtml}</select></td>

            <td><input type="hidden" name="pr_details_id[]" value="${pr_details_id}">
            <input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" readonly/></td> 
                
            <td><input type="text" name="req_qty[]" id="req_qty" onblur="formatPoints(this,3)" min="${used_qty}" onKeyup="sumReqQty(this)"  class="form-control allow-desimal req_qty" value="${req_qty}" style="width:50px;" ${in_use == true ? 'readonly  tabindex="-1"' : ''}/></td>
            
            <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" value="${unit_name}" readonly/></td>  
            
            <td><select name="supplier_id[]"  id="supplier_id_${counter}"  class="chzn-select supplier_id"  ${in_use == true ? 'readonly tabindex="-1"' : ''}>${supplierDrpHtml}
            </select></td>

             <td><input type="text" name="rate_per_unit[]" id="rate_per_unit"  class="form-control  rate_unit  isNumberKey"  onblur="formatPoints(this,3)"  style="width:60px;" value="${rate_per_unit}"/></td> 

            <td><textarea  name="remarks[]" id="remarks_${counter}" rows="4">${remarks}</textarea></td>
           
            </tr>`;

            counter++;
        }




        jQuery('#purchase_requisition_table tbody').append(thisHtml);




        async function updateFields() {
            var counter = 1;
            for (let key in pr_details) {
                var item_id = pr_details[key].item_id ? pr_details[key].item_id : "";
                // jQuery(`.item_id_${counter}`).val(item_id).change();
                jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
                counter++;
            }

            await new Promise(resolve => setTimeout(resolve, 100)); // Wait for 100ms before moving to the next part

            counter = 1;
            for (let key in pr_details) {
                var supplier_id = pr_details[key].supplier_id ? pr_details[key].supplier_id : "";
                jQuery(`#supplier_id_${counter}`).val(supplier_id).trigger('liszt:updated');
                counter++;
            }
        }
        updateFields();
        updateFields(pr_details);


    }
    sumReqQty();
    srNo();
}


// function to wait till supplier load and getting selected
async function updateFields(pr_details) {
    let counter = 1;

    for (let key in pr_details) {
        let item_id = pr_details[key]?.item_id || "";
        jQuery(`.item_id_${counter} `).val(item_id).change();
        counter++;
    }
    await new Promise(resolve => setTimeout(resolve, 100)); // Increase delay slightly

    counter = 1;

    for (let key in pr_details) {
        let supplier_id = pr_details[key]?.supplier_id || "";
        let supplierDropdown = jQuery(`#supplier_id_${counter} `);
        var in_use = pr_details[key]?.in_use || false;


        await new Promise(resolve => {
            let checkDropdown = setInterval(() => {
                if (supplierDropdown.find(`option[value = "${supplier_id}"]`).length) {
                    clearInterval(checkDropdown);
                    supplierDropdown
                        .val(supplier_id)
                        .trigger('change') // Ensure correct supplier selection
                        .trigger('liszt:updated'); // Refresh chosen.js or select2

                    if (in_use) {
                        supplierDropdown.attr('readonly', true);
                    } else {
                        supplierDropdown.attr('readonly', false);
                    }
                    resolve();
                    // jQuery('#show-progress').removeClass('loader-progress-whole-page');
                }
            }, 50); // Check every 50ms until supplier is available
        });

        if (in_use == true) {
            jQuery(`#remarks_${counter}`).attr('readonly', true);
        }
        counter++;
    }
}


jQuery('#pr_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();
    if (subBtn == "submit" || subBtn == "Submit") {
        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }
    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid PR No.');
            jQuery('#pr_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    jQuery('#pr_sequence').focus();
                }, 1000);
            });
            jQuery('#pr_sequence').val('');
        } else {
            jQuery("#materialRequestButton").attr('disabled', true);
            jQuery('#pr_sequence').parent().parent().parent('div.control-group').removeClass('error');
            var urL = RouteBasePath + "/check-purchase_requisition?for=add&pr_sequence=" + val;
            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-purchase_requisition?for=edit&pr_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#pr_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        jQuery('#pr_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                jQuery('#pr_sequence').focus();
                            }, 1000);
                        });

                        jQuery('#pr_sequence').val('');
                    } else {
                        jQuery('#pr_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#pr_number').val(data.latest_po_no);
                        jQuery('#pr_sequence').val(val);
                    }
                    jQuery("#materialRequestButton").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#pr_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#pr_number').val('');
        jQuery('#pr_sequence').val('');
    }
});

// // check duplication of supplier with same item
// function checkDuplicateSupplier(selectElement) {
//     let itemSupplierPairs = {};
//     let isDuplicate = false;
//     let duplicateRow = null;

//     jQuery("#purchase_requisition_table tbody tr").each(function () {
//         let item = jQuery(this).find("[name='item_id[]']").val();
//         let supplier = jQuery(this).find("[name='supplier_id[]']").val();

//         if (item && supplier) {
//             let key = item + "_" + supplier;
//             if (itemSupplierPairs[key]) {
//                 isDuplicate = true;
//                 duplicateRow = supplier;
//                 return false; // Exit loop
//             } else {
//                 itemSupplierPairs[key] = true;
//             }
//         }
//     });

//     if (isDuplicate) {
//         jAlert("The same supplier cannot be added twice for the same item.");
//         jQuery(duplicateRow).val("").focus().addClass('error');
//         //toastElement("The same supplier cannot be added twice for the same item",duplicateRow);
//         return false;
//     } else {
//         return true;
//     }
// }



function suggestPreparedBy(e, $this) {
    var keyevent = e
    if (keyevent.key != "Tab") {
        jQuery("#prepared_by").addClass('file-loader');
        var search = jQuery($this).val();

        jQuery.ajax({
            url: RouteBasePath + "/prepared_by_for_pr-list?term=" + encodeURI(search),
            type: 'GET',
            dataType: 'json',
            processData: false,
            success: function (data) {

                jQuery("#prepared_by").removeClass('file-loader');
                if (data.response_code == 1) {
                    jQuery('#prepared_by_list').html(data.orderByList);
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


function getItemsfromMapping() {
    var supplier_id = jQuery('#commonPRForm').find('#supplier_id').val();
    var pr_form_id_fix = jQuery('input[name="pr_form_id_fix"]:checked').val();

    if (supplier_id != "" && pr_form_id_fix == 1) {

        if (formId == undefined) {
            var url = RouteBasePath + "/get-pr_items_from_supplier_mapping?supplier_id=" + supplier_id;
        } else {
            var url = RouteBasePath + "/get-pr_items_from_supplier_mapping?supplier_id=" + supplier_id + "&id=" + formId;
        }



        jQuery.ajax({
            url: url,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    if (data.mappedItems.length > 0) {
                        productDrpHtml = `<option value="">Select Item</option>`;
                        var item_id = ``;
                        for (let indx in data.mappedItems) {
                            productDrpHtml += `<option value="${data.mappedItems[indx].id}" data-item_code="${data.mappedItems[indx].item_code}" data-unit_name="${data.mappedItems[indx].unit_name}">${data.mappedItems[indx].item_name} </option>`;

                            //jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
                            item_id += `data-rate="${data.mappedItems[indx].id}" `;
                        }
                    } else {
                        productDrpHtml = `<option value="">Select Item</option>`;
                    }
                    jQuery('.item_id').chosen();
                    jQuery('.add_item').empty().append(productDrpHtml).trigger('liszt:updated');

                } else {
                    productDrpHtml = `<option value="">Select Item</option>`;
                    jQuery('.add_item').empty().append(productDrpHtml).trigger('liszt:updated');
                }
            },
        });
    } else {
        productDrpHtml = `<option value="">Select Item</option>`;
        jQuery('.add_item').empty().append(productDrpHtml).trigger('liszt:updated');
    }
}



function oldPRTable() {
    var tableHtml = `<thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item</th>
                        <th>Code</th>
                        <th>Req. Qty.</th>
                        <th>Unit</th>                                          
                        <th>Supplier</th>
                        <th>Rate/Unit </th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                  <tfoot>
                    <tr class="total_tr"><td colspan="4"></td>
                        <td class="prqtysum" name="item_production_total_qty"></td>
                        <td colspan="4"> </td>
                    </tr>
                </tfoot>`;

    jQuery('#purchase_requisition_table').html(tableHtml);
}


function newPRTable() {
    var tableHtml = `<thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item </th>
                        <th>Code</th>
                        <th>Req. Qty.</th>
                        <th>Unit</th>
                        <th>Rate/Unit</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="4"></td>
                        <td class="prqtysum" name="item_production_total_qty"></td>
                        <td colspan="3"> </td>
                    </tr>
                </tfoot>`;

    jQuery('#supplierRejectionTable').html(tableHtml);
}

// Pending Hide Show Logic
function prTypeFix() {

    var pr_form_id_fix = jQuery('input[name="pr_form_id_fix"]:checked').val();

    if (pr_form_id_fix == 1) {
        jQuery('#addPart').prop('disabled', false);
        jQuery('.toggleModalBtn').prop('disabled', true);
        jQuery('.toggleModalBtn').hide();
        jQuery('div#hide').hide();
        prWoSupTable();
        // jQuery('div#show').show(); 
        if (formId == undefined) {
            jQuery('#purchase_requisition_table tbody').empty();
            addPrDetail();
            jQuery('#supplier_id').trigger('liszt:updated').attr('readonly', false);
            setTimeout(() => {
                jQuery("#supplier_id").trigger('liszt:activate');
            }, 100);
        }

    } else {
        jQuery('#addPart').prop('disabled', true);
        // jQuery('.toggleModalBtn').prop('disabled', false);
        jQuery('#purchase_requisition_table tbody').empty();
        jQuery('div#hide').show();
        jQuery('.toggleModalBtn').show();
        jQuery('#supplier_id').val("").trigger('liszt:updated').attr('readonly', true);
        // jQuery('div#show').hide();
        prsupplierTable();

        if (formId == undefined) {
            getLocationForPR();
            setTimeout(() => {
                jQuery("#pr_location_id").trigger('liszt:activate');
            }, 100);
        }


    }
}

function getLocationForPR() {
    var typeFixId = jQuery('input[name="pr_form_id_fix"]:checked').val();

    if (typeFixId != '' && typeFixId == '2') {

        if (formId == undefined) {

            var Url = RouteBasePath + "/get-location_for_pr";
        } else {
            var Url = RouteBasePath + "/get-location_for_pr?id=" + formId;
        }


        return jQuery.ajax({
            url: Url,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    var stgDrpHtml = `<option value="">Select Location</option>`;
                    for (let indx in data.location) {
                        stgDrpHtml += `<option value="${data.location[indx].id}">${data.location[indx].location_name}</option>`;
                    }
                    jQuery('#pr_location_id').empty().append(stgDrpHtml);
                    jQuery('#pr_location_id').trigger('liszt:updated');
                }
            },

        });
    }
}

function fillPendingMaterialData() {

    var thisModal = jQuery('#pendingMaterialRequest');
    var thisForm = jQuery('#commonPRForm');


    let location_id = jQuery('#pr_location_id option:selected').val();
    var typeFixId = jQuery('input[name="pr_form_id_fix"]:checked').val();

    if (location_id != "" && typeFixId == "2") {

        if (formId == undefined) {

            var Url = RouteBasePath + "/get-pending_material_request_for_pr?location_id=" + location_id;
        } else {
            var Url = RouteBasePath + "/get-pending_material_request_for_pr?location_id=" + location_id + "&id=" + formId;
        }

        jQuery.ajax({

            url: Url,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {

                    var usedParts = [];
                    var totalDisb = 0;
                    var found = 0;

                    thisForm.find('#soPartTable tbody input[name="form_indx"]').each(function (indx) {
                        let frmIndx = jQuery(this).val();
                        // console.log('jbEorkOrderId', frmIndx)
                        let jbEorkOrderId = material_data[frmIndx].mr_details_id;
                        if (jbEorkOrderId != "" && jbEorkOrderId != null) {
                            usedParts.push(Number(jbEorkOrderId));
                        }
                    });

                    function isUsed(pjId) {
                        // console.log(usedParts)
                        if (usedParts.includes(Number(pjId))) {
                            totalDisb++;
                            return true;
                        }
                        return false;
                    }

                    let totalEntry = 0;
                    var tblHtml = ``;
                    var found = 0;
                    if (data.mrData.length > 0 && !jQuery.isEmptyObject(data.mrData)) {
                        found = 1;
                        for (let idx in data.mrData) {
                            let inUse = isUsed(data.mrData[idx].mr_id);
                            totalEntry++;
                            tblHtml += `<tr>
                            <td><input type="radio" name="mr_id[]" class="simple-check ${inUse ? 'in-use' : ''}" id="mr_ids_${data.mrData[idx].mr_id}" value="${data.mrData[idx].mr_id}" ${inUse ? 'checked' : ''}/></td>
                            <td>${data.mrData[idx].mr_number}</td>                            
                            <td>${data.mrData[idx].mr_date}</td>
                        </tr >
                `;
                        }

                    } else {
                        tblHtml += `< tr class="centeralign" id = "noPendingPo" >
                <td colspan="15">No Pending Material Request Available</td>
                    </tr > `;
                    }

                    // thisForm.find('.toggleModalBtn').prop('disabled', false);
                    thisModal.find('#pendingMaterialRequestTable tbody').empty().append(tblHtml);
                    if (found == 1) {
                        if (formId == undefined) {
                            thisForm.find('.toggleModalBtn').prop('disabled', false);
                        }
                    } else {
                        thisForm.find('.toggleModalBtn').prop('disabled', true);
                    }

                } else {
                    thisModal.find('#pendingMaterialRequestTable tbody').empty().append(tblHtml);
                    thisForm.find('.toggleModalBtn').prop('disabled', true);

                    toastError(data.response_message);
                }

            },



        });
    }
}

var validator = jQuery("#pendingMaterialRequestForm").validate({
    rules: {
        "mr_id[]": {
            required: true
        },
    },
    messages: {
        "mr_id[]": {
            required: "Please Select Material Request",
        },

    },
    submitHandler: function (form) {

        var chkCount = 0;
        var chkArr = [];
        var chkId = [];
        jQuery("#pendingMaterialRequestForm").find("[id^='mr_ids_']").each(function () {
            let thisId = jQuery(this).attr('id');
            let splt = thisId.split('mr_ids_');
            let intId = splt[1];

            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });
        if (chkCount == 0) {
            toastError('Please Select Pending  Material Request');

        } else {

            if (formId == undefined) {

                var url = RouteBasePath + "/get-material_parts_data-pr?materialids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-material_parts_data-pr?materialids=" + chkArr.join(',') + "&id=" + formId;
            }

            jQuery.ajax({

                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.material_data.length > 0 && !jQuery.isEmptyObject(data.material_data)) {
                            material_data = [];

                            for (let ind in data.material_data) {

                                material_data.push(data.material_data[ind]);

                            }
                            fillPendingMaterialTable(data.material_data);

                            if (formId == undefined) {
                                jQuery('#special_notes').val(data.sp_note.special_notes);
                            }
                        }
                        jQuery("#pendingMaterialRequest").modal('hide');

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
        }

    }

});

function fillPendingMaterialTable() {
    if (material_data.length > 0) {
        // jQuery('#soPartTable tbody').empty();
        var thisHtml = ``;
        var counter = 1;
        for (let key in material_data) {

            var formIndx = key;
            var sr_no = counter;
            var pr_details_id = material_data[key].pr_details_id ? material_data[key].pr_details_id : 0;

            var item_id = material_data[key].item_id ? material_data[key].item_id : "";
            var item_name = material_data[key].item_name ? material_data[key].item_name : "";
            var item_code = material_data[key].item_code ? material_data[key].item_code : "";
            var req_qty = material_data[key].req_qty ? parseFloat(material_data[key].req_qty).toFixed(3) : 0;
            var unit_name = material_data[key].unit_name ? material_data[key].unit_name : "";
            var rate_per_unit = material_data[key].rate_per_unit ? parseFloat(material_data[key].rate_per_unit).toFixed(2) : "";
            var mr_details_id = material_data[key].mr_details_id ? material_data[key].mr_details_id : "";
            var mr_id = material_data[key].mr_id ? material_data[key].mr_id : "";
            if (formId == undefined) {
                var mr_qty = material_data[key].mr_qty ? parseFloat(material_data[key].mr_qty).toFixed(3) : 0;
                var so_amount = rate_per_unit != '' ? req_qty * rate_per_unit : "";

            } else {
                var mr_qty = material_data[key].req_qty ? parseFloat(material_data[key].req_qty).toFixed(3) : 0;
                var so_amount = material_data[key].so_amount ? material_data[key].so_amount : "";

            }

            var totalMrQty = material_data[key].req_qty ? parseFloat(material_data[key].req_qty).toFixed(3) : 0;

            var remarks = material_data[key].remarks ? material_data[key].remarks : "";

            // Build supplier dropdown
            var suppliers = material_data[key].suppliers || [];
            if (material_data[key].in_use == true) {
                var supplierSelect = `<select name="supplier_id[]"  class="form-control supplier_id changes_supplier_id_${key}" readonly>`;
            } else {
                var supplierSelect = `<select name="supplier_id[]"  class="form-control supplier_id changes_supplier_id_${key}">`;
            }
            supplierSelect += `<option value="">Select Supplier </option>`;

            if (suppliers.length > 0) {
                suppliers.forEach(function (supp) {
                    supplierSelect += `<option value="${supp.supplier_id}">${supp.supplier_name}</option>`;
                });
            }

            supplierSelect += `</select>`;





            thisHtml += `<tr>`;
            if (formId == undefined) {
                thisHtml += `<td><a onclick="removePrDetails(this)"><i class="action-icon iconfa-trash pr_details"></i></a></td>`;

            } else {
                if (material_data[key].in_use == true) {
                    thisHtml += `<td></td>`;
                } else {
                    thisHtml += `<td><a onclick="removePrDetails(this)"><i class="action-icon iconfa-trash pr_details"></i></a></td>`;
                }
            }

            thisHtml += `<td class="sr_no"></td>          
                    <td class="so_mr_item"> 
                        <input type="hidden" name="pr_details_id[]" value="${pr_details_id}">
                        <input type="hidden" name="form_indx" value="${formIndx}"/>
                        <input type="hidden" name="mr_id[]" value="${mr_id}"/>
                        <input type="hidden" name="mr_details_id[]" value="${mr_details_id}"/>                 
                        <input type="hidden" name="item_id[]" id="item_id" class="form-control" readonly tabindex="-1" value="${item_id}"/>${item_name}               
                    </td> 
                     <td>
                        ${supplierSelect}
                    </td>             
                    <td>
                        <input type="text" name="code[]" id="code"   class="form-control salesmanageTable" readonly tabindex="-1" value="${item_code}"/>
                    </td>         
                                            
                    <td>
                        <input type="text" name="req_qty[]" id="req_qty"  onKeyup="sumSoQty(this)" onblur="formatPoints(this,3)"  class="form-control isNumberKey req_qty" style="width:50px;" value="${mr_qty}" tabindex="-1"  readonly max="${totalMrQty}"/>
                    </td >                
                    <td>
                        <input type="text" name="unit[]" id="unit" class="form-control salesmanageTable" tabindex="-1" readonly value="${unit_name}" style="width:50px;" tabindex="1" />
                    </td>      
                    <td>
                        <input type="text" name="rate_per_unit[]" onKeyup="soRateUnit(this)" id="rate_per_unit" class="form-control rate_unit  isNumberKey" onblur="formatPoints(this,2)" data-rate="${rate_per_unit}"  value="${rate_per_unit}"${mr_qty == material_data[key].used_qty ? "readonly tabindex ='-1'" : ""} style="width:60px;">
                    </td>     
                         
                    <td>
                        <input type="text" name="remarks[]" id="remarks" class="form-control" value="${remarks}"/>
                    </td>        
                </tr > `;

            counter++;
        }

        jQuery('#purchase_requisition_table tbody').empty().append(thisHtml);
        srNo();
        sumReqQty();
        // jQuery('.changes_supplier_id').chosen();

        if (formId != undefined) {
            for (let key in material_data) {
                var supplier_id = material_data[key].supplier_id ? material_data[key].supplier_id : "";
                console.log(supplier_id)
                jQuery(`.changes_supplier_id_${key}`).val(supplier_id).trigger('liszt:updated');
            }

        }


    }
}

jQuery('#pendingMaterialRequest').on('show.bs.modal', function (e) {
    var usedParts = [];
    var totalDisb = 0;

    jQuery('#purchase_requisition_table tbody input[name="form_indx"]').each(function (indx) {
        let frmIndx = jQuery(this).val();
        let woId = material_data[frmIndx].mr_id;
        if (woId != "" && woId != null) {
            usedParts.push(Number(woId));
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

    jQuery('#pendingMaterialRequestTable tbody tr').each(function (indx) {

        totalEntry++;
        let checkField = jQuery(this).find('input[name="mr_id[]"]');
        let partId = jQuery(checkField).val();
        let inUse = isUsed(partId);
        // console.log(partId)
        if (inUse) {
            jQuery(checkField).addClass('in-use').prop('checked', true);

        } else {
            jQuery(checkField).removeClass('in-use').prop('checked', false);
        }

    });
    setTimeout(() => {
        jQuery(this).find('#checkall-material').focus();
    }, 300);
});

// use when from pending
function prsupplierTable() {
    var tableHtml = `<thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item </th>
                        <th>Supplier </th>
                        <th>Code</th>
                        <th>Req. Qty.</th>
                        <th>Unit</th>
                        <th>Rate/Unit</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="5"></td>
                        <td class="prqtysum" name="item_production_total_qty"></td>
                        <td colspan="3"> </td>
                    </tr>
            </tfoot>`;

    jQuery('#purchase_requisition_table').html(tableHtml);
}

function prWoSupTable() {
    var tableHtml = `<thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item </th>
                        <th>Code</th>
                        <th>Req. Qty.</th>
                        <th>Unit</th>
                        <th>Rate/Unit</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>                   

                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="4"></td>
                        <td class="prqtysum" name="item_production_total_qty"></td>
                        <td colspan="3"> </td>
                    </tr>
            </tfoot>`;

    jQuery('#purchase_requisition_table').html(tableHtml);
}
