let supplierRejectionHiddenId = jQuery('#commonsupplierRejChallan').find('input:hidden[name="id"]').val();
var po_data = [];
var qc_data = [];
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;


if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    // for (let indx in getItem[0]) {
    //     // productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
    //     // item_id += `data-rate="${getItem[0][indx].id}" `;

    //     productDrpHtml += `<option value="${getItem[0][indx].id}" data-item_code="${getItem[0][indx].item_code}" data-item_group="${getItem[0][indx].item_group_name}" data-unit="${getItem[0][indx].unit_name}" data-stock_qty="${getItem[0][indx].stock_qty}">${getItem[0][indx].item_name} </option>`;
    //     item_id += `data-rate="${getItem[0][indx].id}" `;
    // }
}
if (supplierRejectionHiddenId == '' || supplierRejectionHiddenId == null && supplierRejectionHiddenId == undefined) {
    var sid = jQuery('#commonsupplierRejChallan').find('#supplier_id').val();
    getItemsfromMapping(sid);
}
// jQuery("#commonsupplierRejChallan").find("#supplier_id").on("change", function () {
//     getItemsfromMapping(this.value);
// });

jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };


    //  edit code

    if (supplierRejectionHiddenId != null && supplierRejectionHiddenId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({
            url: RouteBasePath + "/get-supplier_rej_challan/" + supplierRejectionHiddenId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    // setTimeout(() => {
                    // jQuery('#src_sequence').focus();
                    // }, 100);
                    jQuery('input:radio[name="src_type_fix_id"][value="' + data.supplier_rejection.src_type_id_fix + '"]').attr('checked', true).trigger('click');
                    jQuery("input[name*='src_type_fix_id']").prop({ tabindex: -1 }).attr('readonly', true);
                    jQuery("#src_sequence").val(data.supplier_rejection.src_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery("#src_no").val(data.supplier_rejection.src_number).prop({ tabindex: -1, readonly: true });
                    jQuery("#src_date").val(data.supplier_rejection.src_date)
                    jQuery("#ref_no").val(data.supplier_rejection.ref_no)
                    jQuery("#ref_date").val(data.supplier_rejection.ref_date)
                    jQuery("#transporter").val(data.supplier_rejection.transporter)
                    jQuery("#transporter").val(data.supplier_rejection.transporter_id).trigger('liszt:updated');
                    jQuery("#vehicle_no").val(data.supplier_rejection.vehicle_no)
                    jQuery("#special_notes").val(data.supplier_rejection.special_notes)
                    jQuery("#lr_no_date").val(data.supplier_rejection.lr_no_date)

                    setTimeout(() => {
                        jQuery('#ref_no').focus();
                        // jQuery('#src_date').focus();
                    }, 800);

                    loadSrcSupplier(data);

                    if (data.supplier_rejection.src_type_id_fix == 1) {
                        loadSrcData(data);
                    } else {

                        if (data.supplier_rejection_details.length > 0 && !jQuery.isEmptyObject(data.supplier_rejection_details)) {
                            for (let ind in data.supplier_rejection_details) {
                                qc_data.push(data.supplier_rejection_details[ind]);
                            }

                        }

                        fillQCTable(data.supplier_rejection_details);
                    }



                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-supplier_rej_challan";
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
            //     jQuery('#src_sequence').focus();
            // }, 100);

            getLatestChallanNo();
            changeSrcValue();
            // addSuppDetail();
            setTimeout(() => {
                // jQuery('#src_date').focus();
                jQuery("#supplier_id").trigger('liszt:activate');
            }, 100);

        });
    }


    async function loadSrcSupplier(data) {
        try {
            await changeSrcValue();
            jQuery("#supplier_id").val(data.supplier_rejection.supplier_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
            if (data.supplier_rejection.src_type_id_fix == 2) {
                fillPendingQc();
            }

        } catch {
            console.log('Error', error)
        }
    }

    async function loadSrcData(data) {
        try {
            await getItemsfromMapping(data.supplier_rejection.supplier_id); // Wait for items to load
            fillSupplierTable(data.supplier_rejection_details); // Once items are loaded, fill the table
        } catch (error) {
            console.log("Error: ", error);
            toastError('Something went wrong!');
        }
    }


    // jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    //     return this.optional(element) || parseInt(value) > 0;
    // });

    jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
        //formatPoints(element, 3); // Format the value before validation
        //return this.optional(element) || parseFloat(value) >= parseFloat(param);
        return this.optional(element) || parseFloat(value) > 0;

    });

    // Store or Update

    var validator = jQuery("#commonsupplierRejChallan").validate({
        onclick: false,
        rules: {
            onkeyup: false,
            onfocusout: false,

            src_sequence: {
                required: true
            },

            supplier_id: {
                required: true
            },
            ref_date: {
                required: function (e) {
                    if (jQuery('#commonsupplierRejChallan').find('#ref_no').val() != "") {
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
                    if (jQuery('#commonsupplierRejChallan').find('#ref_date').val() != "") {
                        return true;
                    } else {
                        return false;
                    }
                },
            },
            src_date: {
                required: true,
                date_check: true,
                dateFormat: true
            },
            'item_id[]': {
                required: true
            },
            'challan_qty[]': {
                required: true,
                notOnlyZero: '0.001',
            },
            // 'item_id[]': {
            //     required: function (e) {
            //         var selectedValue = jQuery("#commonsupplierRejChallan").find('#supplier_id').val();
            //         var value = jQuery("#commonsupplierRejChallan").find('#item_id').val();
            //         if(selectedValue != ""  && value == "") {
            //             jQuery(e).addClass('error');
            //             jQuery(e).focus();
            //             return true;
            //         } else {
            //             jQuery(e).removeClass('error');
            //             return false;
            //         }
            //     },
            // },
            // 'challan_qty[]': {
            //     required: function (e) {
            //         if (jQuery("#commonsupplierRejChallan").find('input[name="item_id[]"]').val() != "" && jQuery("#commonsupplierRejChallan").find('input[name="challan_qty[]"]').val() == "" ) {
            //             jQuery(e).addClass('error');
            //             setTimeout(()=>{
            //                 jQuery(e).focus();
            //             },1000);
            //             return true;
            //         } else {
            //             jQuery(e).removeClass('error');
            //         }
            //     },
            //     notOnlyZero: '0.001',
            // },
        },

        messages: {

            src_sequence: {
                required: "Please Enter Challan Number"
            },
            supplier_id: {
                required: "Please Select Supplier"
            },
            src_date: {
                required: "Please Enter PO Date.",
            },
            ref_date: {
                required: "Please Enter Ref. Date",
            },
            ref_no: {
                required: "Please Enter Ref. No.",
            },
            'item_id[]': {
                required: "Please Select Item"
            },
            'challan_qty[]': {
                required: "Please Enter Challan Qty.",
                notOnlyZero: 'Please Enter A Value Greater Than 0.'
            },


        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            var src_type_fix_id = jQuery("input[name*='src_type_fix_id']:checked").val();

            let checkLength = jQuery("#supplierRejectionTable tbody tr").filter(function () {
                return jQuery(this).css('display') !== 'none';
            }).length;


            if (checkLength < 1) {
                jAlert("Please Add At Least One Supplier Rej. Challan Detail.");
                if (src_type_fix_id == 1) {
                    addSuppDetail();
                } else {
                    jQuery('#supplierRejectionTable tbody').empty();
                }

                return false;
            }

            jQuery('#sup_rejection_button').prop('disabled', true);

            var formdata = jQuery('#commonsupplierRejChallan').serialize();


            let formUrl = supplierRejectionHiddenId != undefined && supplierRejectionHiddenId != "" ? RouteBasePath + "/update-supplier_rej_challan" : RouteBasePath + "/store-supplier_rej_challan";


            jQuery.ajax({

                url: formUrl,

                type: 'POST',

                data: formdata,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if (data.response_code == 1) {

                        if (supplierRejectionHiddenId != undefined && supplierRejectionHiddenId != null) {
                            // toastPreview(data.response_message, redirectFn, prePO);                                            
                            // function redirectFn() {
                            //     window.location.href = RouteBasePath + "/manage-supplier_rej_challan";
                            // };
                            // function prePO() {
                            //     id = btoa(data.id);
                            //     window.location.reload();                                  
                            // }
                            toastSuccess(data.response_message, nextFn);
                            function nextFn() {
                                window.location.href = RouteBasePath + "/manage-supplier_rej_challan";
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
                            jQuery('#sup_rejection_button').prop('disabled', false);
                        }
                    } else {
                        jQuery("#sup_rejection_button").attr('disabled', false);
                        toastError(data.response_message);
                    }




                },

                error: function (jqXHR, textStatus, errorThrown) {

                    var errMessage = JSON.parse(jqXHR.responseText);



                    if (errMessage.errors) {

                        jQuery("#sup_rejection_button").attr('disabled', false);
                        validator.showErrors(errMessage.errors);



                    } else if (jqXHR.status == 401) {
                        jQuery("#sup_rejection_button").attr('disabled', false);
                        jAlert(jqXHR.statusText);


                        // toastError(jqXHR.statusText);

                    } else {
                        jQuery("#sup_rejection_button").attr('disabled', false);

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
function getLatestChallanNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_src_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#src_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#src_date').val(currentDate);
                jQuery('#src_no').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });
                jQuery('#src_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#src_no').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}

// add time 
function addSuppDetail() {

    var thisHtml = `
    <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="supplier_rejection_id[]" value="0"></td></tr>
          
    <tr>

    <td>
        <a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
    </td>


    <td class="sr_no"></td>

    <td> <select name="item_id[]"  class="chzn-select src_item_select_width item_id add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select></td>

    <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>
    <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

    <td>
     <input type="hidden" name="pre_item[]" id="pre_item" value="0">  
    <input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" style="width:60%;"  tabindex="-1" readonly/></td>

    <td><input type="text" name="challan_qty[]" onblur="formatPoints(this,3)" id="challan_qty" onfocusout="sumSoQty(this)"  class="form-control isNumberKey challan_qty" tabindex="-1" style="width:60%;" readonly/></td>
    
    <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly/></td>
       
    <td><textarea  name="remarks[]" id="remarks" rows="4" tabindex="-1"  readonly/></td>

    
    </tr>`;
    jQuery('#supplierRejectionTable tbody').append(thisHtml);
    // srNo();
    setTimeout(() => {
        srNo();
    }, 200);
    sumSoQty();

    // <td><input type="text" name="remarks[]" id="remarks" tabindex="-1"  class="form-control salesmanageTable potableremarks"  readonly/></td>
    // totalAmount();
}

// edit time 
function fillSupplierTable(supplier_rejection_details) {
    if (supplier_rejection_details.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in supplier_rejection_details) {

            var sr_no = counter;

            var src_details_id = supplier_rejection_details[key].src_details_id ? supplier_rejection_details[key].src_details_id : "";

            var item_id = supplier_rejection_details[key].item_id ? supplier_rejection_details[key].item_id : "";

            var item_code = supplier_rejection_details[key].item_code ? supplier_rejection_details[key].item_code : "";

            var unit_name = supplier_rejection_details[key].unit_name ? supplier_rejection_details[key].unit_name : "";

            var groupName = supplier_rejection_details[key].groupName ? supplier_rejection_details[key].groupName : "";
            var in_use = supplier_rejection_details[key].in_use ? supplier_rejection_details[key].in_use : "";



            var stock_qty = supplier_rejection_details[key].stock_qty ? parseFloat(supplier_rejection_details[key].stock_qty).toFixed(3) : "";

            var challan_qty = supplier_rejection_details[key].challan_qty ? parseFloat(supplier_rejection_details[key].challan_qty).toFixed(3) : "";
            var remarks = supplier_rejection_details[key].remarks ? supplier_rejection_details[key].remarks : "";

            // var totalQty = supplier_rejection_details[key].stock_qty !='' && supplier_rejection_details[key].challan_qty !='' ? supplier_rejection_details[key].stock_qty + supplier_rejection_details[key].challan_qty : 0;

            var totalQty = supplier_rejection_details[key].stock_qty + supplier_rejection_details[key].challan_qty;




            thisHtml += `
            <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="supplier_rejection_id[]" value="${src_details_id}"></td></tr>                   
            <tr>
        
            <td>
                <a ${in_use == true ? '' : onclick="removeSoDetails(this)"} ><i class="action-icon iconfa-trash so_details"></i></a>
            </td>
        
        
            <td class="sr_no">${sr_no}</td>
        
            <td> <select name="item_id[]"  class="chzn-select src_item_select_width item_id add_item item_id_${sr_no}" onChange="getItemData(this)" ${in_use == true ? 'readonly tabindex="-1"' : ''}>${productDrpHtml}</select></td>

            
            <td>
            <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null ? item_id : 0}">
            <input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" readonly/></td>          
             <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${groupName}" readonly/></td>      
            

            `;

            if (supplierRejectionHiddenId == undefined) {
                thisHtml += `   
                <td><input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" value="${stock_qty != null ? parseFloat(stock_qty).toFixed(3) : ''}" style="width:60%;" tabindex="-1" readonly/></td> 

                <td><input type="text" name="challan_qty[]" id="challan_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey challan_qty" onblur="formatPoints(this,3)" style="width:60%;" tabindex="-1" readonly/></td>
                
                `;


            } else {
                thisHtml += `                     
                <td>
                <input type="hidden" name="org_stock_qty[]" id="org_stock_qty" value="${stock_qty != 0 ? stock_qty : 0}">
                <input type="hidden" name="org_challan_qty[]" id="org_challan_qty" value="${challan_qty}">
                <input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" value="${isNaN(totalQty) ? 0 : parseFloat(totalQty).toFixed(3)} " style="width:60%;" tabindex="-1" readonly/>
                </td> 
                
                <td><input type="text" name="challan_qty[]" id="challan_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey challan_qty" onblur="formatPoints(this,3)" style="width:60%;"   value="${challan_qty}" max="${isNaN(totalQty) ? 0 : parseFloat(totalQty).toFixed(3)}" style="width:50px;" ${in_use == true ? 'readonly tabindex="-1"' : ''}/></td>`;
            }




            thisHtml += `<td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" value="${unit_name}"readonly/></td>`;

            if (supplierRejectionHiddenId == undefined) {
                thisHtml += `   
                <td><textarea  name="remarks[]" id="remarks" rows="4"  readonly/></td>`;
                // <td><input type="text" name="remarks[]" id="remarks"  class="form-control salesmanageTable potableremarks" tabindex="-1"  readonly/></td>
            } else {
                thisHtml += `                     
                
                <td><textarea  name="remarks[]" id="remarks_${supplier_rejection_details[key].src_details_id}" rows="4"  value="${remarks}"/></td>`;

            }



            `</tr>`;

            counter++;

            // <td><input type="text" name="remarks[]" id="remarks" tabindex="-1"  class="form-control salesmanageTable potableremarks" value="${remarks}"/></td>`;
        }

        jQuery('#supplierRejectionTable tbody').append(thisHtml);
        setTimeout(() => {
            var counter = 1;

            for (let key in supplier_rejection_details) {
                var item_id = supplier_rejection_details[key].item_id ? supplier_rejection_details[key].item_id : "";
                jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');

                var print_po_remarks = supplier_rejection_details[key].remarks ? supplier_rejection_details[key].remarks : "";
                jQuery(`#remarks_${supplier_rejection_details[key].src_details_id}`).val(print_po_remarks);

                counter++;
            }
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
    jQuery('.challan_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            //total = parseInt(total) + parseInt(total1);
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    total != 0 && total != "" ? jQuery('.srcqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.srcqtysum').text('');

    // if (jQuery(th).parents('tr').length > 0) {
    //     soRateUnit(jQuery(th).parents('tr'))
    // }
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

                selectTd.html(`<select name="item_id[]" class="chzn-select src_item_select_width add_item item_id" onChange="getItemData(this), sumSoQty(this)">${productDrpHtml}</select>`);
                // jQuery('.item_id').chosen();
                jQuery(".item_id").chosen({
                    search_contains: true
                });
                BlankTrVal(selectTd);
            }
        });
    }

    if (item != "" && item != null) {

        if (supplierRejectionHiddenId == undefined) {
            if (jQuery(th).find('option:selected').data('stock_qty') != null) {
                var minQty = isNaN(Number(jQuery(th).find('option:selected').data('stock_qty'))) ? 0 : Number(jQuery(th).find('option:selected').data('stock_qty'));
            } else {
                var minQty = 0;
            }

        } else {
            var old_item = jQuery(th).parents('tr').find("#pre_item").val();
            if (item == old_item) {
                var challan_qty = jQuery(th).parents('tr').find("#org_challan_qty").val() != '' ? parseFloat(jQuery(th).parents('tr').find("#org_challan_qty").val()).toFixed(3) : 0;

                if (jQuery(th).find('option:selected').data('stock_qty') != null) {
                    var stockQty = isNaN(Number(jQuery(th).find('option:selected').data('stock_qty'))) ? 0 : Number(jQuery(th).find('option:selected').data('stock_qty'));
                    console.log(challan_qty);
                    var minQty = parseFloat(parseFloat(challan_qty) + parseFloat(stockQty)).toFixed(3);
                } else {
                    var minQty = 0;
                }
            } else {
                if (jQuery(th).find('option:selected').data('stock_qty') != null) {
                    var minQty = isNaN(Number(jQuery(th).find('option:selected').data('stock_qty'))) ? 0 : Number(jQuery(th).find('option:selected').data('stock_qty'));
                } else {
                    var minQty = 0;
                }
            }

        }


        jQuery(th).parents('tr').find("#code").val(jQuery(th).find('option:selected').data('item_code'));
        jQuery(th).parents('tr').find("#item_id").val(item);
        jQuery(th).parents('tr').find("#group").val(jQuery(th).find('option:selected').data('item_group'));
        jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit'));
        jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");
        jQuery(th).parents('tr').find("#challan_qty").attr('max', parseFloat(minQty).toFixed(3)); jQuery(th).parents('tr').find("#challan_qty").prop('readonly', false);
        jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
        jQuery(th).parents('tr').find("#challan_qty").prop('tabindex', 0);
        jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);

        if (supplierRejectionHiddenId == undefined) {
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

    //                 if (supplierRejectionHiddenId == undefined) {
    //                     if (data.stock_qty != null) {
    //                         var minQty = isNaN(Number(data.stock_qty.stock_qty)) ? 0 : Number(data.stock_qty.stock_qty);
    //                     } else {
    //                         var minQty = 0;
    //                     }

    //                 } else {
    //                     var old_item = jQuery(th).parents('tr').find("#item_id").val();

    //                     if (item == old_item) {
    //                         var challan_qty = jQuery(th).parents('tr').find("#challan_qty").val() != '' ? parseFloat(jQuery(th).parents('tr').find("#challan_qty").val()).toFixed(3) : 0;

    //                         if (data.stock_qty != null) {
    //                             var stockQty = isNaN(Number(data.stock_qty.stock_qty)) ? 0 : Number(data.stock_qty.stock_qty);

    //                             var minQty = parseFloat(parseFloat(challan_qty) + parseFloat(stockQty)).toFixed(3);
    //                         } else {
    //                             var minQty = 0;
    //                         }
    //                     } else {
    //                         if (data.stock_qty != null) {
    //                             var minQty = isNaN(Number(data.stock_qty.stock_qty)) ? 0 : Number(data.stock_qty.stock_qty);
    //                         } else {
    //                             var minQty = 0;
    //                         }
    //                     }

    //                 }

    //                 jQuery(th).parents('tr').find("#code").val(data.item.item_code);
    //                 jQuery(th).parents('tr').find("#item_id").val(data.item.id);
    //                 jQuery(th).parents('tr').find("#group").val(data.item.item_group_name);
    //                 jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);
    //                 // jQuery(th).parents('tr').find("#stock_qty").val(parseFloat(minQty).toFixed(3));   
    //                 jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");
    //                 jQuery(th).parents('tr').find("#challan_qty").attr('max', parseFloat(minQty).toFixed(3)); jQuery(th).parents('tr').find("#challan_qty").prop('readonly', false);
    //                 jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
    //                 // jQuery(th).parents('tr').find("#challan_qty").prop({ tabindex: -1, readonly: false });                  
    //                 // jQuery(th).parents('tr').find("#remarks").prop({ tabindex: -1, readonly: false });
    //                 jQuery(th).parents('tr').find("#challan_qty").prop('tabindex', 0);
    //                 jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);

    //                 if (supplierRejectionHiddenId == undefined) {
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
}


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

function removeSoDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        //   let checkLength = jQuery("#supplierRejectionTable tbody tr").length;   

        // let checkLength = jQuery("#supplierRejectionTable tbody tr").filter(function() {
        //     return jQuery(this).css('display') !== 'none';
        // }).length;


        // if(checkLength > 1)
        // {
        if (r === true) {
            jQuery(th).parents("tr").remove();
            srNo();
            var challan_qty = jQuery(th).parents('tr').find('#challan_qty').val();
            //var po_amt = jQuery(th).parents('tr').find('#amount').val();

            if (challan_qty) {
                var challan_total = jQuery('.srcqtysum').text();
                // var amt_total = jQuery('.amountsum').text();
                if (challan_total != "") {
                    challan_final_total = parseFloat(challan_total) - parseFloat(challan_qty);
                    //amt_final_total = parseInt(amt_total) - parseInt(po_amt);
                }
                challan_final_total > 0 ? jQuery('.srcqtysum').text(parseFloat(challan_final_total).toFixed(3)) : jQuery('.srcqtysum').text('');

                // jQuery('.srcqtysum').text(challan_final_total);
            }
            //jQuery('.amountsum').text(amt_final_total);
        }
        // }
        // else{
        //     jAlert("Please At Least Item List Item Required");
        // }
    });
}

jQuery('#src_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();

    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }

    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid Supplier Rejection No.');
            jQuery('#src_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    // jQuery('#src_sequence').focus();
                    jQuery("#supplier_id").trigger('liszt:activate');
                }, 1000);
            });
            jQuery('#src_sequence').val('');

        } else {


            jQuery("#sup_rejection_button").attr('disabled', true);

            jQuery('#src_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-src_no_duplication?for=add&src_sequence=" + val;

            if (supplierRejectionHiddenId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-src_no_duplication?for=edit&src_sequence=" + val + "&id=" + supplierRejectionHiddenId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#src_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        jQuery('#src_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                // jQuery('#src_sequence').focus();
                                jQuery("#supplier_id").trigger('liszt:activate');
                            }, 1000);
                        });

                        jQuery('#src_sequence').val('');
                    } else {
                        jQuery('#src_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#src_no').val(data.latest_po_no);
                        jQuery('#src_sequence').val(val);
                    }
                    jQuery("#sup_rejection_button").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#src_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#src_no').val('');
        jQuery('#src_sequence').val('');
    }
});


// jQuery('#src_sequence').on('change', function () {      
//     let val = jQuery(this).val();

//     var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();

//     if (subBtn == "submit" || subBtn == "Submit") {

//         subBtn = jQuery(document).find('.stdform').find('.formwrappers button');        
//     }

//     if (val != undefined) {

//         if (val > 0 == false) {
//             console.log("if");
//             jAlert('Please Enter Valid Supplier Rej. Challan No.');
//             jQuery('#src_sequence').parent().parent().parent('div.control-group').addClass('error');
//             jQuery('#src_sequence').focus();
//             jQuery('#src_sequence').val('');

//         } else {

//             jQuery(subBtn).prop('disabled', true);


//             jQuery('#src_sequence').parent().parent().parent('div.control-group').removeClass('error');

//             var  urL = RouteBasePath + "/check-src_no_duplication?for=add&src_sequence=" + val;

//             if (supplierRejectionHiddenId !== undefined) { //if form is edit
//                 urL = RouteBasePath + "/check-src_no_duplication?for=edit&src_sequence=" + val + "&id=" + supplierRejectionHiddenId;
//             }

//             jQuery.ajax({

//                 url: urL,
//                 type: 'GET',
//                 headers: headerOpt,
//                 dataType: 'json',
//                 processData: false,
//                 success: function (data) {
//                     jQuery('#src_sequence').removeClass('file-loader');
//                     if (data.response_code == 0) {

//                         toastError(data.response_message);
//                         jQuery('#src_sequence').parent().parent().parent('div.control-group').addClass('error');
//                         jQuery('#src_sequence').focus();
//                         jQuery('#src_sequence').val('');

//                     } else {

//                         jQuery('#src_sequence').parent().parent().parent('div.control-group').removeClass('error');
//                         jQuery('#src_no').val(data.latest_po_no);
//                         jQuery('#src_sequence').val(val);
//                     }
//                     jQuery(subBtn).prop('disabled', false);
//                 },
//                 error: function (jqXHR, textStatus, errorThrown) {
//                     jQuery('#src_sequence').removeClass('file-loader');
//                     toastError('Somthing want wrong!')

//                 }
//             });
//         }
//     } else {
//         jQuery('#src_no').val('');
//         jQuery('#src_sequence').val('');
//     }
// });






// function soRateUnit(th) {

//     let po_qty = jQuery(th).parents('tr').find("#po_qty").val();

//     let rateUnit = jQuery(th).parents('tr').find("#rate_unit").val();


//     var poUnit = 0;
//     if (rateUnit != "" && po_qty != "") {
//         poUnit = parseInt(po_qty) * parseFloat(rateUnit);
//     }

//     if (poUnit != 0) {
//         jQuery(th).parents('tr').find("#amount").val(formatAmount(poUnit));
//     } else if (rateUnit == "") {
//         jQuery(th).parents('tr').find("#amount").val('');

//     } else {
//         jQuery(th).parents('tr').find("#amount").val(0);
//     }

//     totalAmount()
// }

// function totalAmount() {
//     var total_amount = 0;
//     jQuery('.amount').map(function () {
//         var amount = jQuery(this).val();
//         if (amount != "") {
//             total_amount = parseFloat(total_amount) + parseFloat(amount);
//         }
//     });
//     if (total_amount != 0) {
//         jQuery('.amountsum').text(formatAmount(total_amount));
//     } else if (amount != 0) {
//         jQuery('.amountsum').text('');
//     } else {
//         jQuery('.amountsum').text(0);
//     }
// }





// function removeSoDetails(th) {
//     jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
//         if (r === true) {
//             jQuery(th).parents("tr").remove();
//             srNo();
//             var challan_qty = jQuery(th).parents('tr').find('#challan_qty').val();
//             //var po_amt = jQuery(th).parents('tr').find('#amount').val();

//             if (challan_qty) {
//                 var challan_total = jQuery('.srcqtysum').text();
//                 // var amt_total = jQuery('.amountsum').text();
//                 if (challan_total != "") {
//                     challan_final_total = parseInt(challan_total) - parseInt(challan_qty);
//                     //amt_final_total = parseInt(amt_total) - parseInt(po_amt);
//                 }
//             }
//             jQuery('.srcqtysum').text(challan_final_total);
//             //jQuery('.amountsum').text(amt_final_total);
//         }
//     });
// }

async function getItemsfromMapping(th) {
    return new Promise((resolve, reject) => {
        var supplier_id = th;
        if (supplier_id != "") {
            if (supplierRejectionHiddenId == undefined) {
                var url = RouteBasePath + "/get-items_from_supplier_mapping?supplier_id=" + supplier_id;
            } else {
                var url = RouteBasePath + "/get-items_from_supplier_mapping?supplier_id=" + supplier_id + "&id=" + supplierRejectionHiddenId;
            }

            jQuery.ajax({
                url: url,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        jQuery('#supplierRejectionTable tbody').empty();
                        if (supplierRejectionHiddenId == undefined) {
                            addSuppDetail();
                        } else {
                            setTimeout(() => {
                                let checkLength = jQuery("#supplierRejectionTable tbody tr").filter(function () {
                                    return jQuery(this).css('display') !== 'none';
                                }).length;

                                if (checkLength < 1) {
                                    addSuppDetail();
                                }
                            }, 600);

                        }
                        if (data.mappedItems.length > 0) {
                            productDrpHtml = `<option value="">Select Item</option>`;
                            var item_id = ``;
                            for (let indx in data.mappedItems) {
                                // productDrpHtml += `<option value="${data.mappedItems[indx].id}">${data.mappedItems[indx].item_name} </option>`;

                                productDrpHtml += `<option value="${data.mappedItems[indx].id}" data-item_code="${data.mappedItems[indx].item_code}" data-item_group="${data.mappedItems[indx].item_group_name}" data-unit="${data.mappedItems[indx].unit_name}" data-stock_qty="${data.mappedItems[indx].stock_qty}">${data.mappedItems[indx].item_name} </option>`;
                                item_id += `data-rate="${data.mappedItems[indx].id}" `;
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



async function changeSrcValue() {
    return new Promise((resolve, reject) => {


        // setTimeout(() => {
        //     jQuery('#src_sequence').focus();
        // }, 100);
        setTimeout(() => {
            // jQuery('#src_date').focus();
            // jQuery("#supplier_id").trigger('liszt:activate');
        }, 100);

        jQuery('#addPart').prop('disabled', true);
        jQuery('.toggleModalBtn').prop('disabled', true);


        var src_type_fix_id = jQuery("input[name*='src_type_fix_id']:checked").val();


        if (src_type_fix_id != '') {

            if (supplierRejectionHiddenId == undefined) {
                var Url = RouteBasePath + "/get-qc_supplier_for_src?src_type_fix_id=" + src_type_fix_id;
            } else {
                var Url = RouteBasePath + "/get-qc_supplier_for_src?src_type_fix_id=" + src_type_fix_id + "&id=" + supplierRejectionHiddenId;
            }


            jQuery.ajax({
                url: Url,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    let suppHtml = '';
                    suppHtml += `<option value="">Select Supplier</option> `;
                    if (data.response_code == 1) {
                        for (let indx in data.get_supplier) {
                            suppHtml += `<option value="${data.get_supplier[indx].id}">${data.get_supplier[indx].supplier_name}</option>`;

                        }
                        jQuery('#supplier_id').empty().append(suppHtml).trigger('liszt:updated')
                        resolve();
                    } else {
                        console.log(data.response_message)
                    }
                },
            });


        }

        if (src_type_fix_id == 2) {
            // jQuery('.toggleModalBtn').prop('disabled', false);
            jQuery('#addPart').prop('disabled', true);
            jQuery('#supplierRejectionTable tbody').empty();
            jQuery('#ref_no').val('').prop({ tabindex: -1, readonly: true });
            jQuery('#ref_date').val('').prop({ tabindex: -1, readonly: true });
            jQuery('#ref_date').css('pointer-events', 'none');
            jQuery('div#hide').show();
            jQuery('div#show').hide();
            qcTable();

        } else {
            jQuery('#addPart').prop('disabled', false);
            if (supplierRejectionHiddenId == undefined) {
                jQuery('#ref_no').val('').prop({ readonly: false });
                jQuery('#ref_date').val('').prop({ readonly: false });
            } else {
                jQuery('#ref_no').prop({ readonly: false });
                jQuery('#ref_date').prop({ readonly: false });
            }
            jQuery('#ref_date').css('pointer-events', 'auto');

            jQuery('div#hide').hide();
            jQuery('div#show').show();
            // jQuery('#supplierRejectionTable tbody').empty();
            manualTable();
            addSuppDetail();

        }
    });
}


function fillPendingQc() {
    var src_type_fix_id = jQuery("input[name*='src_type_fix_id']:checked").val();

    let supId = jQuery('#supplier_id option:selected').val();

    if (src_type_fix_id != '' && src_type_fix_id == 2) {

        var thisModal = jQuery('#pendingPrModal');
        var thisForm = jQuery('#PurchaseOrderForm');

        if (supId != "") {
            if (supplierRejectionHiddenId == undefined) {
                var Url = RouteBasePath + "/get-qc_list-src?src_supplier_id=" + supId;
            } else {
                var Url = RouteBasePath + "/get-qc_list-src?src_supplier_id=" + supId + "&id=" + supplierRejectionHiddenId;
            }

            jQuery.ajax({
                url: Url,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {

                    if (data.response_code == 1) {

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

                        if (data.qc_data.length > 0 && !jQuery.isEmptyObject(data.qc_data)) {
                            jQuery('.toggleModalBtn').prop('disabled', false);
                            found = 1;

                            for (let idx in data.qc_data) {
                                var inUse = isUsed(data.qc_data[idx].qc_id);

                                totalEntry++;
                                tblHtml += `<tr>
                                        <td><input type="checkbox" name="qc_id[]" class="simple-check" id="qc_ids_${data.qc_data[idx].qc_id}" 
                                        value="${data.qc_data[idx].qc_id}" /></td>
                                        <td>${data.qc_data[idx].qc_number}</td>
                                        <td>${data.qc_data[idx].qc_date}</td>                                                                   
                                        <td>${data.qc_data[idx].item_name}</td>                                                                    
                                        <td>${data.qc_data[idx].item_code}</td>                                                                    
                                        <td>${data.qc_data[idx].item_group_name}</td>                                                                    
                                        <td>${parseFloat(data.qc_data[idx].pend_qc_qty).toFixed(3)}</td>                                                                    
                                        <td>${data.qc_data[idx].unit_name}</td>                                                                    
                                        </tr>`;
                            }

                        } else {
                            tblHtml += `<tr class="centeralign" id="noPendingPo">
                                             <td colspan="12">No Pending QC Available</td>
                                        </tr>`;
                            jQuery('.toggleModalBtn').prop('disabled', true);
                        }

                        jQuery('#pendingQcRequestTable tbody').empty().append(tblHtml);

                    } else {
                        jQuery('.toggleModalBtn').prop('disabled', true);
                        toastError(data.response_message);
                    }
                },
            });

        } else {
            jQuery('.toggleModalBtn').prop('disabled', true);
        }
    } else {
        getItemsfromMapping(supId);
    }
}





var coaPartValidator = jQuery("#pendingQcRequestForm").validate({
    rules: {
        "qc_id[]": {
            required: true
        },
    },
    messages: {
        "qc_id[]": {
            required: "Please Select Item From Pending QC Approval",
        }
    },

    submitHandler: function (form) {
        var chkCount = 0;
        var chkArr = [];
        var chkId = [];

        jQuery("#pendingQcRequestForm").find("[id^='qc_ids_']").each(function () {
            var thisId = jQuery(this).attr('id');
            var splt = thisId.split('qc_ids_');
            var intId = splt[1];

            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });

        if (chkCount == 0) {
            toastError('Please Select Item From Pending QC Approval');
        } else {
            if (supplierRejectionHiddenId == undefined) {
                var url = RouteBasePath + "/get-qc_part_data-src?qc_ids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-qc_part_data-src?qc_ids=" + chkArr.join(',') + "&id=" + supplierRejectionHiddenId;
            }

            jQuery.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.qc_data.length > 0 && !jQuery.isEmptyObject(data.qc_data)) {
                            qc_data = [];
                            for (let ind in data.qc_data) {
                                qc_data.push(data.qc_data[ind]);
                            }
                            fillQCTable(data.qc_data);
                        }

                        jQuery('#supplier_id').trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery("#pendingQcRequest").modal('hide');

                    } else {
                        toastError(data.response_message);
                        jQuery('#supplier_id').trigger('liszt:updated').prop({ tabindex: 1 }).attr('readonly', false);
                    }

                },
            });
        }
    }
});





function fillQCTable(supplier_rejection_details) {
    if (supplier_rejection_details.length > 0) {
        var thisHtml = '';
        var counter = 1;

        for (let key in supplier_rejection_details) {
            var sr_no = counter;
            var formIndx = key;
            var src_details_id = supplier_rejection_details[key].src_details_id ? supplier_rejection_details[key].src_details_id : 0;
            var qc_id = supplier_rejection_details[key].qc_id ? supplier_rejection_details[key].qc_id : "";
            var item_id = supplier_rejection_details[key].item_id ? supplier_rejection_details[key].item_id : "";
            var grn_number = supplier_rejection_details[key].grn_number ? supplier_rejection_details[key].grn_number : "";
            var grn_date = supplier_rejection_details[key].grn_date ? supplier_rejection_details[key].grn_date : "";
            var po_number = supplier_rejection_details[key].po_number ? supplier_rejection_details[key].po_number : "";
            var po_date = supplier_rejection_details[key].po_date ? supplier_rejection_details[key].po_date : "";
            var item_name = supplier_rejection_details[key].item_name ? supplier_rejection_details[key].item_name : "";
            var item_code = supplier_rejection_details[key].item_code ? supplier_rejection_details[key].item_code : "";
            var unit_name = supplier_rejection_details[key].unit_name ? supplier_rejection_details[key].unit_name : "";
            var groupName = supplier_rejection_details[key].groupName ? supplier_rejection_details[key].groupName : "";
            var stock_qty = supplier_rejection_details[key].stock_qty ? parseFloat(supplier_rejection_details[key].stock_qty).toFixed(3) : "";
            // var challan_qty = supplier_rejection_details[key].challan_qty ? parseFloat(supplier_rejection_details[key].challan_qty).toFixed(3) : "";
            var pend_qc_qty = parseFloat(parseFloat(supplier_rejection_details[key].pend_qc_qty) + parseFloat(supplier_rejection_details[key].challan_qty)).toFixed(3);
            var remarks = supplier_rejection_details[key].remarks ? supplier_rejection_details[key].remarks : "";
            var totalQty = supplier_rejection_details[key].stock_qty + supplier_rejection_details[key].challan_qty;

            var challan_qty = supplier_rejection_details[key].challan_qty > 0 ? parseFloat(supplier_rejection_details[key].challan_qty).toFixed(3) : pend_qc_qty;




            thisHtml += `
                        <tr style="display:none;">
                            <td class="colspan=10"><input type="hidden" name="supplier_rejection_id[]" value="${src_details_id}"></td>
                        </tr>                   
                        <tr>        
                            <td><a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>
                            <td class="sr_no">${sr_no}</td>        
                            <td>${grn_number}</td>        
                            <td>${grn_date}</td>        
                            <td>${po_number}</td>        
                            <td>${po_date}</td>        
                            <td>${item_name}<input type="hidden" name="form_indx" value="${formIndx}"/><input type="hidden" name="qc_id[]" id="qc_id"  class="form-control" tabindex="-1" value="${qc_id}"/><input type="hidden" name="item_id[]" id="item_id"  class="form-control src_item_select_width" tabindex="-1" value="${item_id}"/></td>            
                            <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" readonly/></td>          
                            <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${groupName}" readonly/></td>  
                            <td><input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" value="${stock_qty != null ? parseFloat(stock_qty).toFixed(3) : ''}" style="width:60%;" tabindex="-1" readonly/></td> 
                            <td><input type="text" name="challan_qty[]" id="challan_qty" onKeyup="sumSoQty(this)" class="form-control isNumberKey challan_qty" onblur="formatPoints(this,3)" style="width:60%;" value="${challan_qty}" max="${parseFloat(pend_qc_qty).toFixed(3)}" style="width:50px;" /></td>    
                            <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" value="${unit_name}"readonly/></td>
                            <td><textarea  name="remarks[]" id="remark" rows="4">${remarks}</textarea></td><tr>
                 </tr>`;
            counter++;
        }

        jQuery('#supplierRejectionTable tbody').empty().append(thisHtml);
    }
    jQuery('#show-progress').removeClass('loader-progress-whole-page');
    sumSoQty();
    srNo();

}


jQuery('#pendingQcRequest').on('show.bs.modal', function (e) {
    var usedParts = [];

    var totalDisb = 0;

    jQuery('#supplierRejectionTable tbody input[name="form_indx"]').each(function (indx) {
        let frmIndx = jQuery(this).val();
        let qcId = qc_data[frmIndx].qc_id;

        if (qcId != "" && qcId != null) {
            usedParts.push(Number(qcId));
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

    jQuery('#pendingQcRequestTable tbody tr').each(function (indx) {
        totalEntry++;
        let checkField = jQuery(this).find('input[name="qc_id[]"]');
        let partId = jQuery(checkField).val();
        let inUse = isUsed(partId);

        if (inUse) {
            jQuery(checkField).prop('checked', true);
        } else {
            jQuery(checkField).prop('checked', false);
        }
    });

});



jQuery('#checkall-qc').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#pendingQcRequestTable").find("[id^='qc_ids_']:not(.in-use)").prop('checked', true).trigger('change');
        jQuery("#pendingQcRequestTable").find("[id^='qc_ids_']").prop('checked', true).trigger('change');
    } else {
        jQuery("#pendingQcRequestTable").find("[id^='qc_ids_']:not(.in-use)").prop('checked', false).trigger('change');
        jQuery("#pendingQcRequestTable").find("[id^='qc_ids_']").prop('checked', false).trigger('change');
    }
});



function manualTable() {
    var tableHtml = `<thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>Item</th>
                        <th>Code</th>
                        <th>Group</th>
                        <th>Stock </th>
                        <th>Challan Qty.</th>
                        <th>Unit</th>                                          
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="6" ></td>
                        <td class="srcqtysum" name="src_total_qty"></td>
                        <td></td>                                                
                        <td class="amountsum" name="src_total_amount">                            
                        </tr>

                </tfoot>`;

    jQuery('#supplierRejectionTable').html(tableHtml);
}


function qcTable() {
    var tableHtml = `<thead>
                    <tr>
                        <th>Action</th>
                        <th>Sr. No.</th>
                        <th>GRN No.</th>
                        <th>GRN Date</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Item</th>
                        <th>Code</th>
                        <th>Group</th>
                        <th>Stock </th>
                        <th>Challan Qty.</th>
                        <th>Unit</th>                                          
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr class="total_tr"><td colspan="10" ></td>
                        <td class="srcqtysum" name="src_total_qty"></td>
                        <td></td>                                                
                        <td class="amountsum" name="src_total_amount">                            
                        </tr>

                </tfoot>`;

    jQuery('#supplierRejectionTable').html(tableHtml);
}
