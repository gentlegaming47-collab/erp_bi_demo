let formId = jQuery('#commonDeliveryChallanForm').find('input:hidden[name="id"]').val();

//var grn_data = [];
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}


if (formId == undefined) {
    jQuery(document).ready(function () {
        getLatestDCNo();
        changeGrNValue();
    });
}



function addPartDetail($fillData = null) {

    if ($fillData != null && $fillData.length > 0) {

        var thisHtml = '';
        var counter = 1;
        for (let key in $fillData) {
            var sr_no = counter;
            var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
            var po_details_id = $fillData[key].po_details_id ? $fillData[key].po_details_id : "";
            var po_no = $fillData[key].po_number ? $fillData[key].po_number : "";
            var po_date = $fillData[key].po_date ? $fillData[key].po_date : "";
            var item_code = $fillData[key].item_code ? $fillData[key].item_code : "";
            var pend_po_qty = $fillData[key].pend_po_qty ? $fillData[key].pend_po_qty : "";
            var unit_name = $fillData[key].unit_name ? $fillData[key].unit_name : "";
            var rate_per_unit = $fillData[key].rate_per_unit ? $fillData[key].rate_per_unit : "";
            var remarks = $fillData[key].remarks ? $fillData[key].remarks : "";

            thisHtml += `
                        <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="grn_details_id[]" value="0"></td></tr>
                        <tr>
                        <td><a onclick="removeGrnDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>
                        <td class="sr_no">${sr_no}</td>
                        <td>
                            <input type="hidden" name="po_details_id[]" value="${po_details_id}">
                            <input type="hidden" name="po_no[]" id="po_no"  class="form-control salesmanageTable POaddtables" tabindex="1" value="" disabled/>${po_no}
                        </td>
                        <td><input type="hidden" name="po_date[]" class="form-control potabledate salesmanageTable date-picker po_date" value="${po_date}" disabled/>${po_date}</td>
                        <td> <select name="item_id[]" id="item_id" class="chzn-select chzn-done item_id add_item item_id_${sr_no}" onChange="getItemData(this)" readonly>${productDrpHtml}</select></td>
                        <td><input type="hidden" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" value="${item_code}"tabindex="1" readonly/>${item_code}</td>
                        <td><input type="text" name="po_qty[]" id="po_qty" class="form-control only-numbers po_qty" style="width:50px;" disabled value="${pend_po_qty}"/></td>
                        <td><input type="text" name="grn_qty[]" id="grn_qty" onKeyup="sumPoGRNQty(this)"  min="1" max="${pend_po_qty > 0 ? pend_po_qty : ''}" class="form-control only-numbers grn_qty" style="width:50px;"/></td>
                        <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="1" readonly value="${unit_name}"/></td>
                        <td><input type="number" name="rate_unit[]"  step="0.01" min="0.01" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit salesmanageTable" onblur="formatPoints(this,2)" value="${rate_per_unit}"></td>
                        <td><input type="text" name="amount[]" id="amount"  class="form-control salesmanageTable amount potabledate" readonly/></td>
                        <td><input type="text" name="remarks[]" id="remarks"  class="form-control salesmanageTable potableremarks" value="${remarks}" /></td>
                        </tr>`;

            counter++;

        }

        setTimeout(() => {
            var count = 1;
            for (let key in $fillData) {
                var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
                jQuery(`.item_id_${count}`).val(item_id).trigger('liszt:updated');
                count++;
            }

        }, 100)

        jQuery('#grnDetails tbody').empty().append(thisHtml);

    } else {
        var thisHtml = `
        <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="grn_details_id[]" value="0"></td></tr>
                        <tr>
                        <td><a onclick="removeGrnDetails(this)"><i class="action-icon iconfa-trash po_details"></i></a></td>
                        <td class="sr_no"></td>
                        <td><input type="text" name="po_no[]" id="po_no"  class="form-control salesmanageTable POaddtables" tabindex="1" disabled/></td>
                        <td><input type="text" name="po_date[]" class="form-control potabledate salesmanageTable date-picker po_date" disabled/></td>
                        <td> <select name="item_id[]" id="item_id" class="chzn-select chzn-done item_id add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select></td>
                        <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="1" readonly/></td>
                        <td><input type="text" name="po_qty[]" id="po_qty" onKeyup=""  class="form-control only-numbers po_qty" style="width:50px;" disabled/></td> 
                        <td><input type="text" name="grn_qty[]" id="grn_qty" onKeyup="sumGRNQty(this)"  class="form-control only-numbers grn_qty" style="width:50px;" /></td>   
                        <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="1" readonly/></td>
                        <td><input type="number" name="rate_unit[]" id="rate_unit" step="0.01" min="0.01" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit salesmanageTable" onblur="formatPoints(this,2)" onblur="formatPoints(this,2)" disabled/></td>
                        <td><input type="text" name="amount[]" id="amount"  class="form-control salesmanageTable amount potabledate" readonly/></td>
                        <td><input type="text" name="remarks[]" id="remarks"  class="form-control salesmanageTable potableremarks" readonly/></td>
                        </tr>`;
        jQuery('#grnDetails tbody').append(thisHtml);
    }

    srNo();
    sumGRNQty();
    // sumGrnQty();
    // totalAmount();

    // Reinitialize date-picker for new elements
    jQuery('.date-picker').datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
    });

}

// it change the radio button
function changeGrNValue(e) {

    jQuery('#addPart').prop('disabled', true);

    if (e == undefined) {

        var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();
    } else {
        var grn_type_fix_id = e.value;
    }

    if (grn_type_fix_id != undefined) {
        jQuery.ajax({
            url: RouteBasePath + "/get-po_supplier_for_grn?grn_type_fix_id=" + grn_type_fix_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                let suppHtml = '';
                suppHtml += `<option value="">Select Supplier</option> `;
                if (data.response_code == 1) {


                    for (let indx in data.get_po_supplier) {

                        suppHtml += `<option value="${data.get_po_supplier[indx].id}">${data.get_po_supplier[indx].supplier_name}</option>`;

                    }
                    jQuery('#grn_supplier_id').empty().append(suppHtml).trigger('liszt:updated')
                    //  jQuery('#grn_supplier_id').val(data.getSupplier.supplier_id).trigger('liszt:updated');
                } else {
                    console.log(data.response_message)
                }
            },
        });
    }

    if (grn_type_fix_id == 2) {
        jQuery('.toggleModalBtn').prop('disabled', true);
        jQuery('#addPart').prop('disabled', false);
        jQuery('div#supplier').show();
        jQuery('div#location').show();

    } else if (grn_type_fix_id == 1) {
        jQuery('div#location').hide();
        jQuery('div#supplier').show();

    } else {

        jQuery('div#location').show();
        jQuery('div#supplier').hide();
    }

};



function fillPendingGrn() {

    let supId = jQuery('#grn_supplier_id option:selected').val();
    var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();

    if (supId != "" && grn_type_fix_id == 1 && formId == undefined) {

        jQuery.ajax({

            url: RouteBasePath + "/get-po_list-grn?grn_supplier_id=" + supId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {
                    var tblHtml = ``;

                    if (data.po_data.length > 0 && !jQuery.isEmptyObject(data.po_data)) {

                        for (let idx in data.po_data) {

                            // let inUse = isUsed(data.po_data[idx].po_id);


                            tblHtml += `<tr>

                                        <td><input type="radio" name="pd_po_id[]" class="simple-check" id="pd_po_ids_${data.po_data[idx].po_id}" value="${data.po_data[idx].po_id}"}/></td>

                                        <td>${data.po_data[idx].po_number}</td>

                                        <td>${data.po_data[idx].po_date}</td>

                                        <td>${data.po_data[idx].total_qty}</td>


                                        </tr>`;

                        }



                    } else {

                        tblHtml += `<tr class="centeralign" id="noPendingPo">

                            <td colspan="5">No Pending PO Available</td>

                        </tr>`;

                    }

                    jQuery('#pendingPOTable tbody').empty().append(tblHtml);




                    if (grn_type_fix_id == 2) {

                        jQuery('.toggleModalBtn').prop('disabled', true);

                    } else {

                        jQuery('.toggleModalBtn').prop('disabled', false);
                    }





                } else {

                    // resetPdCoaForm();

                    jQuery('.toggleModalBtn').prop('disabled', true);



                    toastError(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                //    resetPdCoaForm();

                jQuery('.toggleModalBtn').prop('disabled', true);



                var errMessage = JSON.parse(jqXHR.responseText);



                if (jqXHR.status == 401) {

                    toastError(jqXHR.statusText);

                } else {

                    toastError('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }



            }



        });

    } else {

        //  resetPdCoaForm();

        jQuery('.toggleModalBtn').prop('disabled', true);

    }

}



function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
}

function removeGrnDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        let checkLength = jQuery("#grnDetails tbody tr").length;         
        
        if(checkLength > 3)
        {
            if (r === true) {
                jQuery(th).parents("tr").remove();
                srNo();
                var grn_qty = jQuery(th).parents('tr').find('#grn_qty').val();
                var gr_amt = jQuery(th).parents('tr').find('#amount').val();
    
    
    
                if (grn_qty && gr_amt) {
    
                    var gr_total = jQuery('.grnqtysum').text();
                    var amt_total = jQuery('.amountsum').text();
    
    
                    if (gr_total != "" && amt_total != "") {
                        gr_final_total = parseInt(gr_total) - parseInt(grn_qty);
                        amt_final_total = parseInt(amt_total) - parseInt(gr_amt);
                    }
                    jQuery('.grnqtysum').text(gr_final_total);
                    jQuery('.amountsum').text(amt_final_total);
                }
    
    
            }

        }
        else{
            jAlert("Please At Least Item List Item Required");
        }

    });
}

jQuery(document).on('change', '.item_id', function (e) {
    var selected = jQuery(this).val();
    var thisselected = jQuery(this);
    if (selected) {
        jQuery(jQuery('.item_id').not(jQuery(this))).each(function (index) {
            if (thisselected.val() == jQuery(this).val()) {
                jAlert('This Item Is Already Selected.');
                thisselected.replaceWith(`<select name="item_id[]" id="item_id" class="chzn-select chzn-done add_item item_id" onChange="getItemData
                (this)">${productDrpHtml}</select>`);
            }
        });
    }
});


function sumGRNQty(th) {
    var total = 0;
    jQuery('.grn_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            total = parseInt(total) + parseInt(total1);
        }
    });

    total != 0 && total != "" ? jQuery('.grnqtysum').text(total) : jQuery('.grnqtysum').text('');

    if (jQuery(th).parents('tr').length > 0) {
        soRateUnit(jQuery(th).parents('tr'))
    }
}

function sumPoGRNQty(th) {

    let grnQty = jQuery(th).parents('tr').find("#grn_qty").val();

    let RateUnit = jQuery(th).closest('tr').find("#rate_unit").val();

    var poUnit = 0;
    if (RateUnit != "" && grnQty != "") {
        poUnit = parseInt(grnQty) * parseFloat(RateUnit);
    }
    if (poUnit != 0) {
        jQuery(th).closest('tr').find("#amount").val(formatAmount(poUnit));
    } else if (RateUnit == "") {
        jQuery(th).closest('tr').find("#amount").val('');

    } else {
        jQuery(th).closest('tr').find("#amount").val(0);
    }


    // totalAmount()
    sumGRNQty()
}


function soRateUnit(th) {

    let grn_qty = jQuery(th).parents('tr').find("#grn_qty").val();

    let rateUnit = jQuery(th).parents('tr').find("#rate_unit").val();


    var poUnit = 0;
    if (rateUnit != "" && grn_qty != "") {
        poUnit = parseInt(grn_qty) * parseFloat(rateUnit);
    }

    if (poUnit != 0) {
        jQuery(th).parents('tr').find("#amount").val(formatAmount(poUnit));
    } else if (rateUnit == "") {
        jQuery(th).parents('tr').find("#amount").val('');

    } else {
        jQuery(th).parents('tr').find("#amount").val(0);
    }

    // totalAmount()
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
    } else if (amount != 0) {
        jQuery('.amountsum').text('');
    } else {
        jQuery('.amountsum').text(0);
    }
}



function getLatestDCNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_dc_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            var stgDrpHtml = `<option value="">Select DC NO.</option>`;
            jQuery('#grn_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#dc_no').val(data.latest_po_no);
                jQuery('#dc_sequence').val(data.number);
                jQuery('#dc_date').val(currentDate);               
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#dc_no').removeClass('file-loader');
            console.log('Field To Get Latest DC No.!')
        }
    });
}

jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseInt(value) > 0;
});



jQuery(document).ready(function () {

    if (formId != "" && formId != undefined) {


        var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };




        jQuery.ajax({

            url: RouteBasePath + "/get-grn_details/" + formId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    jQuery('input:radio[name="grn_type_fix_id"][value="' + data.grnMaterial.grn_type_id_fix + '"]').attr('checked', true).trigger('click');



                    jQuery('#grn_sequence').val(data.grnMaterial.grn_sequence).trigger('liszt:updated');
                    jQuery('#grn_no').val(data.grnMaterial.grn_number).trigger('liszt:updated');
                    jQuery('#grn_date').val(data.grnMaterial.grn_date).trigger('liszt:updated');
                    jQuery('#location_id').val(data.grnMaterial.current_location_id).trigger('liszt:updated');


                    jQuery('#transporter').val(data.grnMaterial.transporter_id).trigger('liszt:updated');

                    jQuery('#challan_bill_no').val(data.grnMaterial.bill_no);

                    jQuery('#bill_date').val(data.grnMaterial.bill_date);
                    jQuery('#vehicle').val(data.grnMaterial.vehicle_no);
                    // jQuery('#lr_number').val(data.grnMaterial.lr_no);
                    // jQuery('#lr_date').val(data.grnMaterial.lr_date);
                    jQuery('#lr_no_date').val(data.grnMaterial.lr_no_date);
                    jQuery('#sp_notes').val(data.grnMaterial.special_notes);

                    fillGRNTable(data.grnMaterialDetails);

                    // changeGrNValue()

                    //  for(let key in data.grnMaterialDetails)
                    //  {
                    //     getItemData(data.grnMaterialDetails[key].item_id) 
                    //  }


                    jQuery("input[name*='grn_type_fix_id']").attr("readonly", true);



                    jQuery('#grn_supplier_id').val(data.grnMaterial.supplier_id).trigger('liszt:updated').trigger('change');

                    if (data.grnMaterial.grn_type_id_fix == 1) {
                        jQuery('#addPart').prop('disabled', true);
                        jQuery('div#location').hide();
                        jQuery('div#supplier').show();
                    } else if (data.grnMaterial.grn_type_id_fix == 2) {
                        jQuery('.toggleModalBtn').prop('disabled', true);
                        jQuery('#addPart').prop('disabled', false);
                        jQuery('div#supplier').show();
                        jQuery('div#location').show();


                    } else {

                        jQuery('div#location').show();
                        jQuery('div#supplier').hide();
                    }


                } else {

                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = RouteBasePath + "/manage-grn_details";
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

    }
    else {
        addPartDetail();
    }

});
var validator = jQuery("#commonDeliveryChallanForm").validate({
    onclick: false,
    rules: {

        dc_sequence: {
            required: true,
        },
        customer: {
            required: true
        },
        location_id: {
            required: true
        },
        dc_date: {
            required: true,
            date_check: true,
            dateFormat: true
        },
        'item_id[]': {
            required: true
        },
        'dc_qty[]': {
            required: function (e) {
                if (jQuery(e).prop('disabled')) {
                    return false;
                } else {
                    return true;
                }
            },
            notOnlyZero: '0',
        },
        // 'grn_qty[]': {
        //     required: function (e) {
        //         if (jQuery(e).prop('disabled')) {
        //             return false;
        //         } else {
        //             return true;
        //         }
        //     },
        //     notOnlyZero: '0',
        // },
        // 'rate_unit[]': {
        //     required: function (e) {
        //         if (jQuery(e).prop('disabled')) {
        //             return false;
        //         } else {
        //             return true;
        //         }
        //     },
        //     notOnlyZero: '0',
        // },
    },

    messages: {

        dc_sequence: {
            required: "Please enter DC no."
        },

        customer: {
            required: "Please Select Supplier"
        },
        location_id: {
            required: "Please Select Location"
        },
        dc_date: {
            required: "Please Enter GRN Date."
        },
        'item_id[]': {
            required: "Please Select Item"
        },
        'dc_qty[]':
        {
            required: "Please Enter PO Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.'
        },
        // 'grn_qty[]':
        // {
        //     required: "Please Enter GRN Qty.",
        //     notOnlyZero: 'Please Enter A Value Greater Than Or Equal To 1.'
        // },
        // 'rate_unit[]':
        // {
        //     required: "Please Enter Rate Per Unit.",
        //     notOnlyZero: 'Please Enter A Value Greater Than Or Equal To 1.'
        // },

    },
    errorPlacement: function (error, element) {
        jAlert(error.text());
        return false;
    },



    submitHandler: function (form) {

        jQuery('#grnButton').prop('disabled', true);

        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-grn_details" : RouteBasePath + "/store-grn_details";
        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: jQuery('#commonDeliveryChallanForm').serialize(),
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (formId != null && formId != "") {
                        toastPreview(data.response_message, redirectFn, prePO);
                        function redirectFn() {
                            window.location.href = RouteBasePath + "/manage-grn_details";
                        };
                        function prePO() {
                            id = btoa(data.id);
                            window.location.reload();
                        }
                    } else {
                        toastPreview(data.response_message, redirectFn, prePO);
                        function redirectFn() {
                            window.location.reload();
                        }
                        function prePO() {
                            id = btoa(data.id);
                            window.location.reload();
                        }
                        jQuery('#grnButton').prop('disables', false);
                    }
                } else {
                    jQuery('#grnButton').prop('disabled', false);
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);
                if (errMessage.errors) {
                    jQuery('#grnButton').prop('disabled', false);
                    validator.showErrors(errMessage.errors);
                } else if (jqXHR.status == 401) {
                    jQuery('#grnButton').prop('disabled', false);
                    toastError(jqXHR.statusText);
                } else {
                    jQuery('#grnButton').prop('disabled', false);
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
});






function fillGRNTable(grnMaterialDetails) {


    var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();


    if (grn_type_fix_id == 1) {

        var thisHtml = '';
        var counter = 1;
        for (let key in grnMaterialDetails) {
            var sr_no = counter;
            var grn_details_id = grnMaterialDetails[key].grn_details_id ? grnMaterialDetails[key].grn_details_id : "";
            var item_id = grnMaterialDetails[key].item_id ? grnMaterialDetails[key].item_id : "";
            var po_details_id = grnMaterialDetails[key].po_details_id ? grnMaterialDetails[key].po_details_id : "";
            var po_no = grnMaterialDetails[key].po_number ? grnMaterialDetails[key].po_number : "";
            var po_date = grnMaterialDetails[key].po_date ? grnMaterialDetails[key].po_date : "";
            var item_code = grnMaterialDetails[key].item_code ? grnMaterialDetails[key].item_code : "";
            var pend_po_qty = grnMaterialDetails[key].pend_po_qty ? grnMaterialDetails[key].pend_po_qty : "";
            var org_pend_qty = grnMaterialDetails[key].org_pend_qty ? grnMaterialDetails[key].org_pend_qty : "";
            var stock_qty = grnMaterialDetails[key].stock_qty ? grnMaterialDetails[key].stock_qty : "";
            var grn_qty = grnMaterialDetails[key].grn_qty ? grnMaterialDetails[key].grn_qty : "";
            var unit_name = grnMaterialDetails[key].unit_name ? grnMaterialDetails[key].unit_name : "";
            var rate_per_unit = grnMaterialDetails[key].rate_per_unit ? grnMaterialDetails[key].rate_per_unit : "";
            var remarks = grnMaterialDetails[key].remarks ? grnMaterialDetails[key].remarks : "";
            var so_amount = grnMaterialDetails[key].amount ? grnMaterialDetails[key].amount : "";

            thisHtml += `<tr style="display:none;"><td class="colspan=10"><input type="hidden" name="grn_details_id[]" value="${grn_details_id}"></td></tr>
                            <tr>
                            <td><a onclick="removeGrnDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>
                            <td class="sr_no">${sr_no}</td>
                            <td>
                                <input type="hidden" name="stock_qty[]" value="${stock_qty}">
                                <input type="hidden" name="po_details_id[]" value="${po_details_id}">
                                <input type="hidden" name="po_no[]" id="po_no"  class="form-control salesmanageTable POaddtables" tabindex="1" value="" disabled/>${po_no}
                            </td>
                            <td><input type="hidden" name="po_date[]" class="form-control potabledate salesmanageTable date-picker po_date" value="${po_date}" disabled/>${po_date}</td>
                            <td> <select name="item_id[]" id="item_id" class="chzn-select chzn-done item_id  add_item item_id_${sr_no}" onChange="getItemData(this)" readonly>${productDrpHtml}</select></td>
                            <td><input type="hidden" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" value="${item_code}"tabindex="1" readonly/>${item_code}</td>
                            <td><input type="text" name="po_qty[]" id="po_qty" class="form-control only-numbers po_qty" style="width:50px;" disabled value="${org_pend_qty}"/></td>
                            <td><input type="text" name="grn_qty[]" id="grn_qty" onKeyup="sumPoGRNQty(this)"  min="1" max="${org_pend_qty > 0 ? org_pend_qty : ''}" class="form-control only-numbers grn_qty" style="width:50px;" value="${grn_qty}"/></td>
                            <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="1" readonly value="${unit_name}"/></td>
                            <td><input type="number" name="rate_unit[]"  step="0.01" min="0.01" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit salesmanageTable" onblur="formatPoints(this,2)"/ value="${rate_per_unit}"></td>
                            <td><input type="text" name="amount[]" id="amount"  class="form-control salesmanageTable amount potabledate"  value="${formatAmount(so_amount)}" readonly/></td>
                            <td><input type="text" name="remarks[]" id="remarks"  class="form-control salesmanageTable potableremarks" value="${remarks}" /></td>
                            </tr>`;

            counter++;

        }

        jQuery('#grnDetails tbody').append(thisHtml);

        var counter = 1;
        for (let key in grnMaterialDetails) {
            var item_id = grnMaterialDetails[key].item_id ? grnMaterialDetails[key].item_id : "";
            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            counter++;
        }


        sumGRNQty();
        // totalAmount();
        srNo();


    } else {
        var thisHtml = '';
        var counter = 1;
        for (let key in grnMaterialDetails) {

            var sr_no = counter;
            var grn_details_id = grnMaterialDetails[key].grn_details_id ? grnMaterialDetails[key].grn_details_id : "";
            var item_id = grnMaterialDetails[key].item_id ? grnMaterialDetails[key].item_id : "";
            var po_no = grnMaterialDetails[key].po_details_id ? grnMaterialDetails[key].po_details_id : "";
            var grn_qty = grnMaterialDetails[key].grn_qty ? grnMaterialDetails[key].grn_qty : "";
            var item_code = grnMaterialDetails[key].item_code ? grnMaterialDetails[key].item_code : "";
            var rate_per_unit = grnMaterialDetails[key].rate_per_unit ? grnMaterialDetails[key].rate_per_unit : "";
            var unit_name = grnMaterialDetails[key].unit_name ? grnMaterialDetails[key].unit_name : "";
            var so_amount = grnMaterialDetails[key].amount ? grnMaterialDetails[key].amount : "";
            var remarks = grnMaterialDetails[key].remarks ? grnMaterialDetails[key].remarks : "";
            var stock_qty = grnMaterialDetails[key].stock_qty ? grnMaterialDetails[key].stock_qty : "";

            thisHtml += `<tr style="display:none;"><td class="colspan=10"><input type="hidden" name="grn_details_id[]" value="${grn_details_id}"></td></tr>
                             <tr>
                                <td>
                                <a onclick="removeGrnDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                                </td>
                                <td class="sr_no">${sr_no}</td>
                                <td><input type="text" name="po_no[]" id="po_no"  class="form-control salesmanageTable POaddtables" tabindex="1" disabled/>${po_no}</td>
                                <td><input type="text" name="po_date[]" class="form-control potabledate salesmanageTable date-picker po_date" disabled/></td>
                                <td> <select name="item_id[]" id="item_id" class="chzn-select chzn-done item_id  add_item item_id_${sr_no}" onChange="getItemData(this)">${productDrpHtml}</select></td>
                                <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" value="${item_code}"tabindex="1" readonly/></td>
                                <td><input type="text" name="po_qty[]" id="po_qty" class="form-control only-numbers po_qty" style="width:50px;" disabled/></td>
                                <td>
                                <input type="hidden" name="stock_qty[]" value="${stock_qty}">
                                <input type="text" name="grn_qty[]" value="${grn_qty}" id="grn_qty" onKeyup="sumGRNQty(this)"  class="form-control only-numbers grn_qty" style="width:50px;" />
                                </td>
                                <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="1" readonly value="${unit_name}"/></td>
                                <td><input type="number" name="rate_unit[]"  step="0.01" min="0.01" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit salesmanageTable" onblur="formatPoints(this,2)"/ value="${rate_per_unit}"></td>
                                <td><input type="number" name="amount[]" id="amount" class="form-control amount salesmanageTable" onblur="formatPoints(this,2)" tabindex="-1" value="${formatAmount(so_amount)}" readonly
                                /></td>
                                <td><input type="text" name="remarks[]" id="remarks"  class="form-control salesmanageTable potableremarks" value="${remarks}" readonly/></td>
                            </tr>`;

            counter++;

        }




        jQuery('#grnDetails tbody').append(thisHtml);

        var counter = 1;
        for (let key in grnMaterialDetails) {
            var item_id = grnMaterialDetails[key].item_id ? grnMaterialDetails[key].item_id : "";
            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            counter++;
        }
    }
    sumGRNQty();
    // totalAmount();
    srNo();

}


function getItemData(th) {
    let item = th.value;
    if (item != undefined && item != null) {
        jQuery.ajax({
            url: RouteBasePath + "/get-fitting_item_data?item=" + item,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    jQuery(th).parents('tr').find("#code").val(data.item.item_code);
                    jQuery(th).parents('tr').find("#item_id").val(data.item.id);
                    jQuery(th).parents('tr').find("#group").val(data.item.item_group_name);
                    jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);
                    jQuery(th).parents('tr').find("#po_qty").prop('disabled', false);
                    jQuery(th).parents('tr').find("#grn_qty").prop('readonly', false);
                    jQuery(th).parents('tr').find(".po_date").prop('disabled', false);
                    jQuery(th).parents('tr').find("#rate_unit").prop('disabled', false);
                    jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
                    jQuery(th).parents('tr').find("#po_no").prop('disabled', false);

                    var thisForm = jQuery('#commonDeliveryChallanForm');
                    var grnType = thisForm.find("input[name*='grn_type_fix_id']:checked").val();

                    if (grnType == 2) {
                        jQuery(th).parents('tr').find("#po_no").prop('disabled', true);
                        jQuery(th).parents('tr').find("#po_qty").prop('disabled', true);
                        jQuery(th).parents('tr').find(".po_date").prop('disabled', true);

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




jQuery('#pendingPoModal').on('click', function (e) {
    jQuery('#pendingPoModal').modal('show');
});



var coaPartValidator = jQuery("#addPendingPOForm").validate({

    rules: {

        "pd_po_id[]": {

            required: true

        },

    },

    messages: {

        "pd_po_id[]": {

            required: "Please Select Part",

        }



    },

    submitHandler: function (form) {


        var chkCount = 0;

        var chkArr = [];

        var chkId = [];

        jQuery("#addPendingPOForm").find("[id^='pd_po_ids_']").each(function () {



            let thisId = jQuery(this).attr('id');

            let splt = thisId.split('pd_po_ids_');

            let intId = splt[1];



            if (jQuery(this).is(':checked')) {

                chkArr.push(jQuery(this).val())

                chkId.push(intId);

                chkCount++;

            }

        });



        if (chkCount == 0) {

            // jQuery('#pd_po_ids_' + chkId[0]).parent('td').addClass('error');

            toastError('Please Select Part');



        } else {


            // jQuery('#pd_coa_ids_' + chkId[0]).parent('td').removeClass('error');

            // jQuery("#pendingCoaModal").find("#addPendingCoaModal").addClass('btn-loader');

            jQuery.ajax({



                url: RouteBasePath + "/get-po_part_data-grn?po_ids=" + chkArr.join(','),

                type: 'GET',

                dataType: 'json',

                processData: false,

                success: function (data) {



                    // jQuery("#pendingCoaModal").find("#addPendingCoaModal").removeClass('btn-loader');

                    if (data.response_code == 1) {

                        if (data.po_data.length > 0 && !jQuery.isEmptyObject(data.po_data)) {


                            addPartDetail(data.po_data);

                        }

                        jQuery("#pendingPoModal").modal('hide');



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




jQuery('#grn_sequence').on('change', function () {
    let val = jQuery(this).val();


    // var subBtn = jQuery(document).find('.stdform').find('.formwrapper button').text();


    var subBtn = jQuery(document).find('.stdform').find('button[type="submit"]').text();




    if (subBtn == "submit" || subBtn == "Submit" || subBtn == "Update" || subBtn == "update") {

        // subBtn = jQuery(document).find('.stdform').find('.formwrapper button');
        subBtn = jQuery(document).find('.stdform').find('button[type="submit"]');
    }



    if (val != undefined) {

        if (val > 0 == false) {
            jAlert('Please Enter Valid GRN No.');
            jQuery('#grn_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery('#grn_sequence').focus();
            jQuery('#grn_sequence').val('');

        } else {
            jQuery(subBtn).prop('disabled', true);


            jQuery('#grn_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-grn_no_duplication?for=add&grn_sequence=" + val;

            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-grn_no_duplication?for=edit&grn_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#grn_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {

                        toastError(data.response_message);
                        jQuery('#grn_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery('#grn_sequence').focus();
                        jQuery('#grn_sequence').val('');

                    } else {
                        jQuery('#grn_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#grn_no').val(data.latest_po_no);
                        jQuery('#grn_sequence').val(val);
                    }
                    jQuery(subBtn).prop('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#grn_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#grn_no').val('');
        jQuery('#grn_sequence').val('');
    }
});

