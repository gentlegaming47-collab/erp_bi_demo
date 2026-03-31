jQuery(document).ready(function () {
    jQuery('#grnprintButton').prop('disabled', true);
});
let formId = jQuery('#GrnDetailsForm').find('input:hidden[name="id"]').val();
var route_path = jQuery('#GrnDetailsForm').find('input:hidden[name="route_path"]').val();



var grn_data = [];
var po_data = [];
var dc_data = [];
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        productDrpHtml += `<option value="${getItem[0][indx].id}" data-secondary_unit="${getItem[0][indx].secondary_unit}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}


if (formId == undefined) {
    jQuery(document).ready(function () {
        getLatestGrnNo();
        changeGrNValue();
        jQuery('#grnprintButton').prop('disabled', true);

        setTimeout(() => {
            // jQuery('#grn_date').focus();
            if (route_path == "grn_details") {
                // console.log("0000");
                jQuery("#grn_supplier_id").trigger('liszt:activate');
            }
            else if (route_path == "grn_location") {
                // console.log("1111");
                jQuery("#location_id").trigger('liszt:activate');
            }
            else {
                console.log("2222");
                jQuery('#grn_date').focus();
            }
        }, 800);
    });
}




function addPartDetail($fillData = null) {

    if ($fillData != null && $fillData.length > 0) {

        var thisHtml = '';
        var counter = 1;
        for (let key in $fillData) {

            var formIndx = key;
            var sr_no = counter;
            var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
            var po_details_id = $fillData[key].po_details_id ? $fillData[key].po_details_id : "";
            var po_id = $fillData[key].po_id ? $fillData[key].po_id : "";
            var po_no = $fillData[key].po_number ? $fillData[key].po_number : "";
            var po_date = $fillData[key].po_date ? $fillData[key].po_date : "";
            var item_code = $fillData[key].item_code ? $fillData[key].item_code : "";
            var item_group_name = $fillData[key].item_group_name ? $fillData[key].item_group_name : "";
            var grn_qty = $fillData[key].grn_qty > 0 ? parseFloat($fillData[key].grn_qty).toFixed(3) : 0;
            var unit_name = $fillData[key].unit_name ? $fillData[key].unit_name : "";
            var rate_per_unit = $fillData[key].rate_per_unit ? parseFloat($fillData[key].rate_per_unit).toFixed(3) : "";
            var remarks = $fillData[key].remarks ? checkSpecialCharacter($fillData[key].remarks) : "";

            var grn_details_id = formId == undefined ? 0 : $fillData[key].grn_details_id != null ? $fillData[key].grn_details_id : 0;

            var qc_required = $fillData[key].qc_required ? $fillData[key].qc_required : "";

            var service_item = $fillData[key].service_item ? $fillData[key].service_item : "";

            var in_use = $fillData[key].in_use ? $fillData[key].in_use : "";

            var used_qty = $fillData[key].used_qty ? $fillData[key].used_qty.toFixed(3) : "";

            if (grn_details_id == 0) {
                var pend_po_qty = $fillData[key].pend_po_qty ? parseFloat($fillData[key].pend_po_qty).toFixed(3) : "";
            } else {
                // var pend_po_qty = $fillData[key].pend_po_qty >= $fillData[key].grn_qty ? parseFloat($fillData[key].pend_po_qty).toFixed(3) : parseFloat(0).toFixed(3);
                var pend_po_qty = (parseFloat($fillData[key].show_pend_qty) + parseFloat($fillData[key].grn_qty)).toFixed(3);

            }




            thisHtml += `<tr>
                        <td><a ${in_use == true ? '' : 'onclick = "removeGrnDetails(this)"'}><i class="action-icon iconfa-trash so_details"></i></a></td>
                        <td class="sr_no">${sr_no}</td>
                        <td> <input type="hidden" name="form_indx" value="${formIndx}"/>
                            <input type="hidden" name="po_details_id[]" value="${po_details_id}">
                            <input type="hidden" name="po_id[]" value="${po_id}">
                            <input type="hidden" name="qc_required[]" value="${qc_required}">
                            <input type="hidden" name="service_item[]" value="${service_item}">
                            <input type="hidden" name="grn_details_id[]" value="${grn_details_id}">
                            <input type="hidden" name="po_no[]" id="po_no"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="" disabled/>${po_no}
                        </td>
                        <td><input type="hidden" name="po_date[]" class="form-control potabledate salesmanageTable date-picker po_date" value="${po_date}" disabled/>${po_date}</td>
                        
                        <td> <select name="item_id[]"  class="chzn-select grn_modal_item_select_width  item_id add_item item_id_${sr_no}" onChange="getItemData(this)" tabindex="-1" readonly>${productDrpHtml}</select></td>

                        <td><input type="hidden" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" value="${item_code}"tabindex="-1" readonly/>${item_code}</td>

                        <td><input type="hidden" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" value="${item_group_name}"tabindex="-1" readonly/>${item_group_name}</td>

                   

                        <td>
                        <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null ? item_id : 0}">
                         <input type="hidden" name="org_grn_qty[]" value="${grn_qty}">

                        <input type="text" name="po_qty[]" id="po_qty" onblur="formatPoints(this,3)" class="form-control isNumberKey po_qty" style="width:50px;" disabled value="${pend_po_qty}"/></td>

                        <td><input type="text" name="grn_qty[]" id="grn_qty" onkeyup="sumPoGRNQty(this)" onblur="formatPoints(this,3)"   class="form-control isNumberKey grn_qty" style="width:50px;" value="${grn_qty > 0 ? grn_qty : pend_po_qty}" min="${used_qty}"/></td>

                        <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly value="${unit_name}"/></td>

                        <td><input type="text" name="rate_unit[]"  onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit salesmanageTable isNumberKey" onblur="formatPoints(this,3)" value="${rate_per_unit}" readonly></td>

                        <td><input type="text" name="amount[]" id="amount"  class="form-control salesmanageTable amount" readonly tabindex="-1"/></td>

                        <td><textarea  name="remarks[]" id="remarks_${$fillData[key].po_details_id}" style="width:120px;" rows="4" value="${remarks}"/></td>
                        
                        </tr>`;


            // <td><input type="text" name="remarks[]" id="remarks"  class="form-control salesmanageTable potableremarks" value="${remarks}" /></td>
            counter++;



        }


        setTimeout(() => {
            var count = 1;
            for (let key in $fillData) {
                var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
                jQuery(`.item_id_${count}`).val(item_id).trigger('liszt:updated');
                var print_po_remarks = $fillData[key].remarks ? $fillData[key].remarks : "";
                jQuery(`#remarks_${$fillData[key].po_details_id}`).val(print_po_remarks);
                count++;
            }

        }, 100)




        jQuery('#grnDetails tbody').empty().append(thisHtml);

        jQuery('#grnDetails tbody tr').each(function (indx, tr) {
            if (jQuery(jQuery(tr)[0]).css('display') != 'none') {
                var GrnQty = jQuery(tr).find('td [name="grn_qty[]"]').val();
                var RateUnit = jQuery(tr).find('td [name="rate_unit[]"]').val();

                var Amount = parseFloat(GrnQty) * parseFloat(RateUnit);
                jQuery(tr).find('td [name="amount[]"]').val(isNaN(Amount) ? 0 : parseFloat(Amount));
                formatPoints(jQuery(tr).find('td [name="amount[]"]'), 3);
            }
        });

        totalAmount();
        disabledDropdownVal();

    } else {
        var thisHtml = `<tr>
                        <td><a onclick="removeGrnDetails(this)"><i class="action-icon iconfa-trash po_details"></i></a></td>
                        <td class="sr_no"></td>
                        <td>
                        <input type="hidden" name="grn_details_id[]" value="0">
                        <input type="text" name="po_no[]" id="po_no"  class="form-control salesmanageTable POaddtables" tabindex="-1" disabled/></td>
                        <td><input type="text" name="po_date[]" class="form-control potabledate salesmanageTable date-picker po_date" disabled/></td>
                        <td> <select name="item_id[]"  class="chzn-select  item_id grn_modal_item_select_width add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select></td>
                        <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>
                        <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

                        <td>
                        <input type="hidden" name="pre_item[]" id="pre_item" value="0">  

                        <input type="text" name="po_qty[]" onblur="formatPoints(this,3)" id="po_qty" onKeyup=""  class="form-control isNumberKey po_qty" style="width:50px;" disabled/></td> 

                        <td><input type="text" name="grn_qty[]" id="grn_qty" onKeyup="sumGRNQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey grn_qty" style="width:50px;" /></td> 

                        <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly/></td>

                        <td><input type="text" name="rate_unit[]" id="rate_unit" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit salesmanageTable isNumberKey" onblur="formatPoints(this,3)"  readonly/></td>


                        <td><input type="text" name="amount[]" id="amount"  class="form-control salesmanageTable amount" tabindex="-1" readonly/></td>

                        <td><textarea  name="remarks[]" id="remarks" style="width:120px;" rows="4" readonly/></td>

                        </tr>`;

        // <td><input type="text" name="remarks[]" id="remarks" class="form-control salesmanageTable potableremarks" readonly/></td>
        jQuery('#grnDetails tbody').append(thisHtml);
    }



    srNo();
    sumGRNQty();
    totalAmount();

    // Reinitialize date-picker for new elements
    jQuery('.date-picker').datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
    });

}

// it change the radio button
function changeGrNValue(e) {

    setTimeout(() => {
        // jQuery('#grn_date').focus();
        if (route_path == "grn_details") {
            // console.log("3333");
            jQuery("#grn_supplier_id").trigger('liszt:activate');
        }
        else if (route_path == "grn_location") {
            // console.log("4444");
            jQuery("#location_id").trigger('liszt:activate');
        }
        else {
            // console.log("5555");
            jQuery('#grn_date').focus();
        }
    }, 100);
    // setTimeout(() => {
    //     jQuery('#grn_sequence').focus();
    // }, 100);

    jQuery('#addPart').prop('disabled', true);
    jQuery('.toggleModalBtn').prop('disabled', true);

    if (e == undefined) {

        var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();
    } else {
        var grn_type_fix_id = e.value;
    }

    if (grn_type_fix_id != undefined) {
        if (grn_type_fix_id == 3) {

            jQuery.ajax({
                url: RouteBasePath + "/get-dc_location_for_grn?grn_type_fix_id=" + grn_type_fix_id,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    let suppHtml = '';
                    suppHtml += `<option value="">Select Location</option> `;
                    if (data.response_code == 1) {
                        for (let indx in data.get_dc_location) {
                            suppHtml += `<option value="${data.get_dc_location[indx].id}">${data.get_dc_location[indx].location_name}</option>`;

                        }
                        jQuery('#location_id').empty().append(suppHtml).trigger('liszt:updated')

                    } else {
                        console.log(data.response_message)
                    }
                },
            });

        } else {
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

                    } else {
                        console.log(data.response_message)
                    }
                },
            });
        }

    }

    if (grn_type_fix_id == 2) {
        jQuery('.toggleModalBtn').prop('disabled', true);
        jQuery('#addPart').prop('disabled', false);
        jQuery('div#supplier').show();
        jQuery('div#location').hide();
        jQuery('#grnDetails tbody').empty();
        poTable();
        addPartDetail();

    } else if (grn_type_fix_id == 1) {
        jQuery('div#location').hide();
        jQuery('div#supplier').show();
        jQuery('#grnDetails tbody').empty();
        jQuery('.toggleModalBtn').attr('data-target', '#pendingPoModal');
        poTable();

    } else {
        jQuery('div#location').show();
        jQuery('div#supplier').hide();
        jQuery('#grnDetails tbody').empty();
        jQuery('#grnDetails thead').empty();
        jQuery('#grnDetails tfoot').empty();
        jQuery('.toggleModalBtn').attr('data-target', '#pendingDcModal');
        dcTable();
    }

};



// function fillPendingGrn() {

//     let supId = jQuery('#grn_supplier_id option:selected').val();
//     var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();

//     var thisModal = jQuery('#pendingPoModal');
//     var thisForm = jQuery('#GrnDetailsForm');

//     if (supId != "" && grn_type_fix_id == 1) {

//         if (formId == undefined) {

//             var Url = RouteBasePath + "/get-po_list-grn?grn_supplier_id=" + supId;
//         } else {
//             var Url = RouteBasePath + "/get-po_list-grn?grn_supplier_id=" + supId + "&id=" + formId;
//         }

//         jQuery.ajax({

//             url: Url,

//             type: 'GET',

//             headers: headerOpt,

//             dataType: 'json',

//             processData: false,

//             success: function (data) {

//                 if (data.response_code == 1 && data.po_data.length > 0) {

//                     // new code
//                     var usedParts = [];
//                     var totalDisb = 0;
//                     var found = 0;

//                     thisForm.find('#grnDetails tbody input[name="form_indx"]').each(function (indx) {
//                         let frmIndx = jQuery(this).val();


//                         let jbEorkOrderId = po_data[frmIndx].po_details_id;
//                         if (jbEorkOrderId != "" && jbEorkOrderId != null) {
//                             usedParts.push(Number(jbEorkOrderId));
//                         }
//                     });



//                     function isUsed(pjId) {
//                         if (usedParts.includes(Number(pjId))) {
//                             totalDisb++;
//                             return true;
//                         }
//                         return false;
//                     }

//                     let totalEntry = 0;
//                     var tblHtml = ``;
//                     var found = 0;

//                     // end new code

//                     if (data.po_data.length > 0 && !jQuery.isEmptyObject(data.po_data)) {

//                         found = 1;

//                         for (let idx in data.po_data) {

//                             var inUse = isUsed(data.po_data[idx].po_details_id);

//                             totalEntry++;
//                             tblHtml += `<tr>
//                                         <td><input type="checkbox" name="po_details_id[]" class="simple-check ${inUse ? 'in-use' : ''}" id="po_details_ids_${data.po_data[idx].po_details_id}" 
//                                         value="${data.po_data[idx].po_details_id}" ${inUse ? 'checked' : ''} /></td>
//                                         <td>${data.po_data[idx].po_number}</td>
//                                         <td>${data.po_data[idx].po_date}</td>
//                                         <td>${data.po_data[idx].supplier_name}</td>
//                                         <td>${data.po_data[idx].location_name}</td>
//                                         <td>${data.po_data[idx].item_name}</td>
//                                         <td>${data.po_data[idx].item_code}</td>
//                                         <td>${data.po_data[idx].item_group_name}</td>
//                                         <td>${data.po_data[idx].po_qty != null ? parseFloat(data.po_data[idx].po_qty).toFixed(3) : ""}</td>
//                                         <td>${data.po_data[idx].pend_po_qty != null ? data.po_data[idx].po_qty >= data.po_data[idx].pend_po_qty ? parseFloat(data.po_data[idx].pend_po_qty).toFixed(3) : parseFloat(0).toFixed(3) : parseFloat(0).toFixed(3)}</td>                                 
//                                         <td>${data.po_data[idx].unit_name}</td>
//                                         <td>${data.po_data[idx].del_date}</td>
//                                         </tr>`;

//                         }

//                     } else {

//                         tblHtml += `<tr class="centeralign" id="noPendingPo">

//                             <td colspan="5">No Pending PO Available</td>

//                         </tr>`;

//                     }

//                     jQuery('#pendingPOTable tbody').empty().append(tblHtml);

//                     // thisModal.find('#pendingPOTable tbody').empty().append(tblHtml);
//                     // if (found == 1) {
//                     //     if (totalDisb == totalEntry) {
//                     //         thisModal.find('#pendingPoModal').prop('disabled', true);
//                     //     } else {
//                     //         thisModal.find('#pendingPoModal').prop('disabled', false);
//                     //     }
//                     //     thisForm.find('.toggleModalBtn').prop('disabled', false);

//                     // } else {
//                     //     resetPdWoForm();
//                     //     thisForm.find('.toggleModalBtn').prop('disabled', true);
//                     // }



//                     if (grn_type_fix_id == 2) {

//                         jQuery('.toggleModalBtn').prop('disabled', true);

//                     } else {

//                         jQuery('.toggleModalBtn').prop('disabled', false);
//                     }





//                 } else {

//                     // resetPdCoaForm();

//                     jQuery('.toggleModalBtn').prop('disabled', true);



//                     toastError(data.response_message);

//                 }

//             },

//             error: function (jqXHR, textStatus, errorThrown) {

//                 //    resetPdCoaForm();

//                 jQuery('.toggleModalBtn').prop('disabled', true);



//                 var errMessage = JSON.parse(jqXHR.responseText);



//                 if (jqXHR.status == 401) {

//                     toastError(jqXHR.statusText);

//                 } else {

//                     toastError('Something went wrong!');

//                     console.log(JSON.parse(jqXHR.responseText));

//                 }



//             }



//         });

//     } else {

//         //  resetPdCoaForm();

//         jQuery('.toggleModalBtn').prop('disabled', true);

//     }

// }



function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });

    jQuery(".item_id").chosen({
        search_contains: true
    });
}

function removeGrnDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        // let checkLength = jQuery("#grnDetails tbody tr").filter(function () {
        //     return jQuery(this).css('display') !== 'none';
        // }).length;

        // if (checkLength > 1) {
        if (r === true) {
            jQuery(th).parents("tr").remove();
            srNo();
            var grn_qty = jQuery(th).parents('tr').find('#grn_qty').val();
            var gr_amt = jQuery(th).parents('tr').find('#amount').val();

            if (grn_qty || gr_amt) {

                var gr_total = jQuery('.grnqtysum').text();
                var amt_total = jQuery('.amountsum').text();


                if (gr_total != "" || amt_total != "") {
                    gr_final_total = parseFloat(gr_total) - parseFloat(grn_qty);
                    amt_final_total = parseFloat(amt_total) - parseFloat(gr_amt);
                }

                gr_final_total > 0 ? jQuery('.grnqtysum').text(parseFloat(gr_final_total).toFixed(3)) : jQuery('.grnqtysum').text('');

                amt_final_total > 0 ? jQuery('.amountsum').text(parseFloat(amt_final_total).toFixed(3)) : jQuery('.amountsum').text('');

                // jQuery('.grnqtysum').text(gr_final_total);
                // jQuery('.amountsum').text(amt_final_total);
            }


        }

        // }
        // else {
        //     jAlert("Please At Least Item List Item Required");
        // }
        // let checkLength = jQuery("#grnDetails tbody tr").filter(function () {
        //     return jQuery(this).css('display') !== 'none';
        // }).length;

        // if (checkLength == '0') {
        //     console.log(checkLength)
        //     jQuery('#grn_supplier_id').trigger('liszt:updated').attr('readonly', false);
        // }

    });
}

// jQuery(document).on('change', '.item_id', function (e) {
//     var selected = jQuery(this).val();
//     var thisselected = jQuery(this);
//     if (selected) {
//         jQuery(jQuery('.item_id').not(jQuery(this))).each(function (index) {
//             if (thisselected.val() == jQuery(this).val()) {
//                 jAlert('This Item Is Already Selected.');
//                 thisselected.replaceWith(`<select name="item_id[]"  class="chzn-select  add_item item_id" onChange="getItemData
//                 (this)">${productDrpHtml}</select>`);
//             }
//         });
//     }
// });


function sumGRNQty(th) {
    var total = 0;
    jQuery('.grn_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            //total = parseInt(total) + parseInt(total1);
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    //total != 0 && total != "" ? jQuery('.grnqtysum').text(total) : jQuery('.grnqtysum').text('');
    total != 0 && total != "" ? jQuery('.grnqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.grnqtysum').text('');

    // if (jQuery(th).parents('tr').length > 0) {
    //     soRateUnit(jQuery(th).parents('tr'))
    // }

    if (route_path != 'grn_location') {
        soRateUnit(th)
    }
}

function sumPoGRNQty(th) {

    let grnQty = jQuery(th).parents('tr').find("#grn_qty").val();

    let RateUnit = jQuery(th).closest('tr').find("#rate_unit").val();

    var poUnit = 0;
    if (RateUnit != "" && grnQty != "") {
        // poUnit = parseInt(grnQty) * parseFloat(RateUnit);
        poUnit = parseFloat(grnQty) * parseFloat(RateUnit);
    }
    if (poUnit != 0) {
        jQuery(th).closest('tr').find("#amount").val(parseFloat(poUnit).toFixed(3));
    } else if (RateUnit == "") {
        jQuery(th).closest('tr').find("#amount").val('');

    } else {
        jQuery(th).closest('tr').find("#amount").val(0);
    }


    totalAmount()
    sumGRNQty()
}


function soRateUnit(th) {

    let grn_qty = jQuery(th).parents('tr').find("#grn_qty").val();

    let rateUnit = jQuery(th).parents('tr').find("#rate_unit").val();


    var poUnit = 0;
    if (rateUnit != "" && grn_qty != "") {
        //poUnit = parseInt(grn_qty) * parseFloat(rateUnit);
        poUnit = parseFloat(grn_qty) * parseFloat(rateUnit);
    }

    if (poUnit != 0) {
        jQuery(th).parents('tr').find("#amount").val(parseFloat(poUnit).toFixed(3));
    } else if (rateUnit == "") {
        jQuery(th).parents('tr').find("#amount").val('');

    } else {
        jQuery(th).parents('tr').find("#amount").val(0);
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
        jQuery('.amountsum').text(parseFloat(total_amount).toFixed(3));
    } else if (total_amount != 0) {
        jQuery('.amountsum').text('');
    } else {
        jQuery('.amountsum').text(0);
    }
}



function getLatestGrnNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_grn_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            // var stgDrpHtml = `<option value="">Select Location</option>`;
            jQuery('#grn_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#grn_no').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });
                jQuery('#grn_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
                jQuery('#grn_date').val(currentDate);
                // stgDrpHtml += `<option value="${data.location.id}">${data.location.location_name}</option>`;
                // jQuery('#location_id').append(stgDrpHtml);
                // jQuery('#location_id').val(data.location.id).trigger('liszt:updated');
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

// jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
//     return this.optional(element) || parseInt(value) > 0;
// });
jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    //formatPoints(element, 3); // Format the value before validation
    //return this.optional(element) || parseFloat(value) >= parseFloat(param);

    var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();
    if (grn_type_fix_id != '3') {
        return this.optional(element) || parseFloat(value) > 0;
    }
    return true;
    // return this.optional(element) || parseFloat(value) > 0;
});

// validation for rate
jQuery.validator.addMethod("salesRate", function (value, element, param) {
    return this.optional(element) || parseFloat(value) > 0;
    //return this.optional(element) || parseFloat(value) >= parseFloat(param);
});


jQuery(document).ready(function () {

    enablePrint(formId)
    if (formId != "" && formId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');

        var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };




        jQuery.ajax({

            url: RouteBasePath + "/get-grn_details/" + formId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    // setTimeout(() => {
                    //     jQuery('#grn_sequence').focus();
                    // }, 100);

                    jQuery('input:radio[name="grn_type_fix_id"][value="' + data.grnMaterial.grn_type_id_fix + '"]').attr('checked', true).trigger('click');



                    jQuery('#grn_sequence').val(data.grnMaterial.grn_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery('#grn_no').val(data.grnMaterial.grn_number).prop({ tabindex: -1, readonly: true });
                    jQuery('#grn_date').val(data.grnMaterial.grn_date);
                    jQuery('#location_id').val(data.grnMaterial.to_location_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                    if (data.grnMaterial.in_use == true) {
                        jQuery('#grn_sequence').attr('readonly', true);
                        jQuery('#grn_date').attr('readonly', true);
                    }


                    jQuery('#transporter').val(data.grnMaterial.transporter_id).trigger('liszt:updated');

                    jQuery('#challan_bill_no').val(data.grnMaterial.bill_no);

                    jQuery('#bill_date').val(data.grnMaterial.bill_date);
                    jQuery('#vehicle').val(data.grnMaterial.vehicle_no);

                    if (data.grnMaterial.grn_type_id_fix == 3) {
                        jQuery('#vehicle_no').val(data.grnMaterial.vehicle_no);
                    }
                    // jQuery('#lr_number').val(data.grnMaterial.lr_no);
                    // jQuery('#lr_date').val(data.grnMaterial.lr_date);
                    jQuery('#lr_no_date').val(data.grnMaterial.lr_no_date);
                    jQuery('#sp_notes').val(data.grnMaterial.special_notes);

                    setTimeout(() => {
                        // jQuery('#grn_date').focus();
                        if (route_path == "grn_details") {
                            // console.log("6666");
                            jQuery('#challan_bill_no').focus();
                        }
                        else if (route_path == "grn_location") {
                            // console.log("7777");
                            jQuery('#challan_bill_no').focus();
                        }
                        else {
                            // console.log("8888");
                            jQuery('#grn_date').focus();
                        }
                    }, 800);

                    if (data.grnMaterial.grn_type_id_fix == 1 || data.grnMaterial.grn_type_id_fix == 2) {
                        poTable();
                        fillGRNTable(data.grnMaterialDetails);


                        jQuery('.toggleModalBtn').attr('data-target', '#pendingPoModal');

                        // setTimeout(() => {
                        //     getPoData()
                        // }, 800);


                    } else {
                        dcTable();
                        addDCPartDetail(data.grnMaterialDetails);

                        jQuery('.toggleModalBtn').attr('data-target', '#pendingDcModal');
                    }




                    jQuery("input[name*='grn_type_fix_id']").prop({ tabindex: -1, readonly: true });


                    jQuery('#grn_supplier_id').val(data.grnMaterial.supplier_id).trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);


                    if (data.grnMaterial.grn_type_id_fix == 1) {
                        if (data.grnMaterialDetails.length > 0 && !jQuery.isEmptyObject(data.grnMaterialDetails)) {
                            for (let ind in data.grnMaterialDetails) {
                                po_data.push(data.grnMaterialDetails[ind]);
                            }

                        }

                        jQuery('#addPart').prop('disabled', true);
                        jQuery('div#location').hide();
                        jQuery('div#supplier').show();

                        // fillPendingGrn();
                        loadGrnData();

                    } else if (data.grnMaterial.grn_type_id_fix == 2) {


                        jQuery('.toggleModalBtn').prop('disabled', true);
                        jQuery('#addPart').prop('disabled', false);
                        // jQuery('div#supplier').show();
                        jQuery('div#location').hide();
                        fillPendingGrn();




                    } else {
                        if (data.grnMaterialDetails.length > 0 && !jQuery.isEmptyObject(data.grnMaterialDetails)) {
                            for (let ind in data.grnMaterialDetails) {
                                dc_data.push(data.grnMaterialDetails[ind]);
                            }

                        }

                        jQuery('div#location').show();
                        jQuery('div#supplier').hide();

                        fillPendingGrnDc();
                    }
                    jQuery('#show-progress').removeClass('loader-progress-whole-page');

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
        setTimeout(() => {
            // jQuery('#grn_sequence').focus();
            if (route_path == "grn_details") {
                // console.log("9999");
                jQuery("#grn_supplier_id").trigger('liszt:activate');
            }
            else if (route_path == "grn_location") {
                // console.log("101010");
                jQuery("#location_id").trigger('liszt:activate');
            }
            else {
                // console.log("121212");
                jQuery('#grn_date').focus();
            }
        }, 100);
        // addPartDetail();
    }

});


async function loadGrnData() {
    try {
        await fillPendingGrn();

        getPoData();
    } catch (error) {
        console.log("Error", error)
    }
}


var validator = jQuery("#GrnDetailsForm").validate({
    onclick: false,
    rules: {

        grn_sequence: {
            required: true,
        },
        grn_supplier_id: {
            required: function (e) {
                if (jQuery('#GrnDetailsForm').find('input[name*="grn_type_fix_id"]:checked').val() == "1" || jQuery('#GrnDetailsForm').find('input[name*="grn_type_fix_id"]:checked').val() == "2") {
                    return true;
                } else {
                    return false;
                }
            },
        },
        // location_id: {
        //     required: true
        // },
        location_id: {
            required: function (e) {

                // if (jQuery('#GrnDetailsForm').find('input[name*="grn_type_fix_id"]:checked').val() == "2") {
                if (jQuery('#GrnDetailsForm').find('input[name*="grn_type_fix_id"]:checked').val() == "3") {

                    return true;

                } else {

                    return false;

                }

            },
        },
        grn_date: {
            required: true,
            date_check: true,
            dateFormat: true
        },
        // bill_date: {
        //     dateFormat: true
        // },
        // lr_date: {
        //     dateFormat: true
        // },
        'item_id[]': {
            required: true
        },
        'item_details_id[]': {
            required: true
        },
        'po_qty[]': {
            required: true,
        },
        'grn_qty[]': {
            required: true,
            notOnlyZero: '0.001',
        },
        'rate_unit[]': {
            required: true,
            salesRate: '0.01',
        },
        // 'item_id[]': {
        //     required: function (e) {
        //         var sid = jQuery("#GrnDetailsForm").find('#grn_supplier_id').val();
        //         var type_id = jQuery('#GrnDetailsForm').find('input[name*="grn_type_fix_id"]:checked').val();
        //         var value = jQuery("#GrnDetailsForm").find('#item_id').val();
        //         if (sid != "" && type_id == "2" && value == "") {
        //             jQuery(e).addClass('error');
        //             jQuery(e).focus();
        //             return true;
        //         }
        //         else {
        //             jQuery(e).removeClass('error');
        //             return false;
        //         }
        //     },
        // },
        // 'po_qty[]': {
        //     required: function (e) {
        //         if (jQuery("#GrnDetailsForm").find('input[name="item_id[]"]').val() != "" && jQuery("#GrnDetailsForm").find('input[name="po_qty[]"]').val() == "") {
        //             jQuery(e).addClass('error');
        //             setTimeout(() => {
        //                 jQuery(e).focus();
        //             }, 1000);
        //             return true;
        //         } else {
        //             jQuery(e).removeClass('error');
        //         }
        //     },
        //     notOnlyZero: '0.01',
        // },
        // 'grn_qty[]': {
        //     required: function (e) {
        //         var sid = jQuery("#GrnDetailsForm").find('#grn_supplier_id').val();
        //         // var type_id = jQuery('#GrnDetailsForm').find('input[name*="grn_type_fix_id"]:checked').val();
        //         var value = jQuery("#GrnDetailsForm").find('input[name="grn_qty[]"]').val();
        //         var i_id = jQuery("#GrnDetailsForm").find('#item_id').val();

        //         if (sid != "" && i_id != "" && i_id != null && value == "") {
        //             jQuery(e).addClass('error');
        //             setTimeout(() => {
        //                 jQuery(e).focus();
        //             }, 1000);
        //             return true;
        //         } else {
        //             jQuery(e).removeClass('error');
        //         }
        //     },
        //     notOnlyZero: '0.01',
        // },
        // "rate_unit[]": {
        //     required: function (e) {
        //         if (jQuery("#GrnDetailsForm").find('input[name="grn_qty[]"]').val() != "" && jQuery("#GrnDetailsForm").find('input[name="rate_unit[]"]').val() == "") {
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
        bill_date: {
            required: function (e) {
                if (jQuery('#GrnDetailsForm').find('#challan_bill_no').val() != "") {
                    return true;
                } else {
                    return false;
                }
            },
            // date_check: true,
            dateFormat: true
        },
        challan_bill_no: {
            required: function (e) {
                if (jQuery('#GrnDetailsForm').find('#bill_date').val() != "") {
                    return true;
                } else {
                    return false;
                }
            },
        },
    },

    messages: {

        grn_sequence: {
            required: "Please enter GRN no."
        },

        grn_supplier_id: {
            required: "Please Select Supplier"
        },
        location_id: {
            required: "Please Select Location"
        },
        grn_date: {
            required: "Please Enter GRN Date."
        },
        bill_date: {
            required: "Please Enter Bill Date",
        },
        challan_bill_no: {
            required: "Please Enter Challan No.",
        },
        'item_id[]': {
            required: "Please Select Item"
        },
        'item_details_id[]': {
            required: "Please Select Item Detail"
        },
        'po_qty[]':
        {
            required: "Please Enter PO Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.'
        },
        'grn_qty[]':
        {
            required: "Please Enter GRN Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.'
        },
        'rate_unit[]':
        {
            required: "Please Enter Rate Per Unit.",
            salesRate: 'Please Enter A Value Greater Than 0.'
        },

    },
    errorPlacement: function (error, element) {
        jAlert(error.text());
        return false;
    },



    submitHandler: function (form) {

        let checkLength = jQuery("#grnDetails tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();

        if (checkLength < 1) {
            jAlert("Please Add At Least One Goods Receipt Note Detail.");
            grn_type_fix_id == 2 ? addPartDetail() : "";
            return false;
        }


        if (jQuery('#grnDetails tbody tr').length == 0) {
            jAlert("Please Add At Least One Goods Receipt Note Detail.");
            return false;
        }

        jQuery('#grnButton').prop('disabled', true);


        // if (grn_type_fix_id == 3) {

        //     var storesecondaryArr = [];


        //     jQuery('#grnDetails tbody tr').each(function (e) {
        //         var le_secondary_details_id = jQuery(this).find('input[name="le_secondary_details_id[]"]').val();

        //         if (le_secondary_details_id != null && le_secondary_details_id != '') {

        //             dp_details_id = jQuery(this).find('input[name="dp_details_id[]"]').val();
        //             le_details_id = jQuery(this).find('input[name="le_details_id[]"]').val();
        //             le_secondary_details_id = jQuery(this).find('input[name="le_secondary_details_id[]"]').val();
        //             qc_required = jQuery(this).find('input[name="qc_required[]"]').val();
        //             service_item = jQuery(this).find('input[name="service_item[]"]').val();
        //             item_id = jQuery(this).find('input[name="item_id[]"]').val();
        //             item_details_id = jQuery(this).find('input[name="item_details_id[]"]').val();
        //             grn_qty = jQuery(this).find('input[name="grn_qty[]"]').val();
        //             rate_unit = jQuery(this).find('input[name="rate_unit[]"]').val();
        //             amount = jQuery(this).find('input[name="amount[]"]').val();
        //             remarks = jQuery(this).find('input[text="remarks[]"]').val();

        //             storesecondaryArr.push({ 'dp_details_id': dp_details_id, 'le_details_id': le_details_id, 'le_secondary_details_id': le_secondary_details_id, 'qc_required': qc_required, 'service_item': service_item, 'item_id': item_id, 'item_details_id': item_details_id, 'grn_qty': grn_qty, 'rate_unit': rate_unit, 'amount': amount, 'remarks': remarks });


        //         } else {

        //             dp_details_id = jQuery(this).find('input[name="dp_details_id[]"]').val();
        //             le_details_id = jQuery(this).find('input[name="le_details_id[]"]').val();

        //             qc_required = jQuery(this).find('input[name="qc_required[]"]').val();
        //             service_item = jQuery(this).find('input[name="service_item[]"]').val();
        //             item_id = jQuery(this).find('input[name="item_id[]"]').val();
        //             grn_qty = jQuery(this).find('input[name="grn_qty[]"]').val();
        //             rate_unit = jQuery(this).find('input[name="rate_unit[]"]').val();
        //             amount = jQuery(this).find('input[name="amount[]"]').val();
        //             remarks = jQuery(this).find('input[text="remarks[]"]').val();

        //             storesecondaryArr.push({ 'dp_details_id': dp_details_id, 'le_details_id': le_details_id, 'qc_required': qc_required, 'service_item': service_item, 'item_id': item_id, 'grn_qty': grn_qty, 'rate_unit': rate_unit, 'amount': amount, 'remarks': remarks });

        //         }
        //     });



        // } else {
        //     var formData = jQuery('#GrnDetailsForm').serialize();

        // }

        // return;

        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-grn_details" : RouteBasePath + "/store-grn_details";
        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: jQuery('#GrnDetailsForm').serialize(),
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (formId != null && formId != "") {
                        // toastPreview(data.response_message, redirectFn, prePO);
                        toastSuccess(data.response_message, nextFn);

                        function nextFn() {

                            window.location.href = RouteBasePath + "/manage-" + route_path;
                        }
                        // function redirectFn() {
                        //     window.location.href = RouteBasePath + "/manage-grn_details";
                        // };
                        // function prePO() {
                        //     id = btoa(data.id);
                        //     window.location.reload();
                        // }
                    } else {
                        // toastPreview(data.response_message, redirectFn, prePO);

                        toastSuccess(data.response_message, nextFn);

                        function nextFn() {

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
            var formIndx = key;
            var sr_no = counter;
            var grn_details_id = grnMaterialDetails[key].grn_details_id ? grnMaterialDetails[key].grn_details_id : "";
            var item_id = grnMaterialDetails[key].item_id ? grnMaterialDetails[key].item_id : "";
            var po_details_id = grnMaterialDetails[key].po_details_id ? grnMaterialDetails[key].po_details_id : "";
            var po_id = grnMaterialDetails[key].po_id ? grnMaterialDetails[key].po_id : "";
            var po_no = grnMaterialDetails[key].po_number ? grnMaterialDetails[key].po_number : "";
            var po_date = grnMaterialDetails[key].po_date ? grnMaterialDetails[key].po_date : "";
            var item_code = grnMaterialDetails[key].item_code ? grnMaterialDetails[key].item_code : "";
            var pend_po_qty = grnMaterialDetails[key].pend_po_qty ? parseFloat(grnMaterialDetails[key].pend_po_qty).toFixed(3) : "";

            var org_pend_qty = grnMaterialDetails[key].org_pend_qty ? parseFloat(grnMaterialDetails[key].org_pend_qty).toFixed(3) : parseFloat(0).toFixed(3);

            var show_pend_qty = grnMaterialDetails[key].show_pend_qty ? parseFloat(grnMaterialDetails[key].show_pend_qty).toFixed(3) : parseFloat(0).toFixed(3);

            var stock_qty = grnMaterialDetails[key].stock_qty ? grnMaterialDetails[key].stock_qty : "";
            var grn_qty = grnMaterialDetails[key].grn_qty ? parseFloat(grnMaterialDetails[key].grn_qty).toFixed(3) : "";
            var unit_name = grnMaterialDetails[key].unit_name ? grnMaterialDetails[key].unit_name : "";
            var item_group_name = grnMaterialDetails[key].item_group_name ? grnMaterialDetails[key].item_group_name : "";
            var rate_per_unit = grnMaterialDetails[key].rate_per_unit ? parseFloat(grnMaterialDetails[key].rate_per_unit).toFixed(3) : "";
            var remarks = grnMaterialDetails[key].remarks ? checkSpecialCharacter(grnMaterialDetails[key].remarks) : "";
            var so_amount = grnMaterialDetails[key].amount ? grnMaterialDetails[key].amount : "";

            var total_pend_po_qty = (parseFloat(show_pend_qty) + parseFloat(grn_qty)).toFixed(3);

            var qc_required = grnMaterialDetails[key].qc_required ? grnMaterialDetails[key].qc_required : "";

            var service_item = grnMaterialDetails[key].service_item ? grnMaterialDetails[key].service_item : "";

            var in_use = grnMaterialDetails[key].in_use ? grnMaterialDetails[key].in_use : "";

            var second_unit_in_use = grnMaterialDetails[key].second_unit_in_use ? grnMaterialDetails[key].second_unit_in_use : "";

            var used_qty = grnMaterialDetails[key].used_qty ? grnMaterialDetails[key].used_qty.toFixed(2) : "";


            thisHtml += `<tr>
                            <td><a ${in_use == true ? '' : 'onclick = "removeGrnDetails(this)"'}> <i class="action-icon iconfa-trash so_details"></i></a ></td >
                            <td class="sr_no">${sr_no}</td>
                            <td>
                            <input type="hidden" name="form_indx" value="${formIndx}"/>
                                <input type="hidden" name="stock_qty[]" value="${stock_qty}">
                                <input type="hidden" name="po_id[]" value="${po_id}">
                                <input type="hidden" name="qc_required[]" value="${qc_required}">
                                <input type="hidden" name="service_item[]" value="${service_item}">
                                <input type="hidden" name="po_details_id[]" value="${po_details_id}">
                                <input type="hidden" name="grn_details_id[]" value="${grn_details_id}">
                                <input type="hidden" name="po_no[]" id="po_no"/>${po_no}
                            </td>
                            <td><input type="hidden" name="po_date[]" class="form-control potabledate salesmanageTable date-picker po_date" value="${po_date}" disabled/>${po_date}</td>
                            <td> <select name="item_id[]"  class="chzn-select  item_id grn_modal_item_select_width add_item item_id_${sr_no}" onChange="getItemData(this)" tabindex="-1" readonly>${productDrpHtml}</select></td>
                            <td><input type="hidden" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" value="${item_code}"tabindex="-1" readonly/>${item_code}</td>
                            <td><input type="hidden" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" value="${item_code}"tabindex="-1" readonly/>${item_group_name}</td>

                            <td><input type="text" name="po_qty[]" onblur="formatPoints(this,3)" id="po_qty" class="form-control isNumberKey po_qty" style="width:50px;" disabled value="${total_pend_po_qty}"/></td>`;


            if (second_unit_in_use == true) {
                thisHtml += `<td>
                            <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null ? item_id : 0}">
                            <input type="hidden" name="org_grn_qty[]" value="${grn_qty}">
                            <input type="hidden" name="is_approved[]" value="${grnMaterialDetails[key].is_approved}">
                            <input type="text" onblur="formatPoints(this,3)" name="grn_qty[]" id="grn_qty" onkeyup="sumPoGRNQty(this)" class="form-control  isNumberKey grn_qty" style="width:50px;" value="${grn_qty}" readonly min="${used_qty}" />
                            </td>`;

            } else {

                thisHtml += `<td>
                            <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null ? item_id : 0}">
                            <input type="hidden" name="org_grn_qty[]" value="${grn_qty}">
                            <input type="hidden" name="is_approved[]" value="${grnMaterialDetails[key].is_approved}">
                            <input type="text" onblur="formatPoints(this,3)" name="grn_qty[]" id="grn_qty" onkeyup="sumPoGRNQty(this)" class="form-control  isNumberKey grn_qty" style="width:50px;" value="${grn_qty}" ${grnMaterialDetails[key].is_approved == 'Y' ? 'readonly' : ''} min="${used_qty}" />
                            </td>`;
            }
            thisHtml += `<td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly value="${unit_name}" /></td>

                            <td><input type="text" name="rate_unit[]" onkeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit salesmanageTable isNumberKey" onblur="formatPoints(this,3)" / value="${rate_per_unit}" readonly tabindex="-1"></td>


                            <td><input type="text" name="amount[]" id="amount" class="form-control salesmanageTable amount" value="${parseFloat(so_amount).toFixed(3)}" readonly tabindex="-1" /></td>

                            <td><textarea name="remarks[]" id="remarks_${grnMaterialDetails[key].grn_details_id}" rows="4" style="width:120px;" value="${remarks}" /></td>

                        </tr>`;

            // <td><input type="text" name="remarks[]" id="remarks" tabindex="-1" class="form-control salesmanageTable potableremarks" value="${remarks}" /></td>

            counter++;

        }

        jQuery('#grnDetails tbody').append(thisHtml);


        var counter = 1;
        for (let key in grnMaterialDetails) {
            var item_id = grnMaterialDetails[key].item_id ? grnMaterialDetails[key].item_id : "";
            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            var print_po_remarks = grnMaterialDetails[key].remarks ? grnMaterialDetails[key].remarks : "";
            jQuery(`#remarks_${grnMaterialDetails[key].grn_details_id}`).val(print_po_remarks);
            counter++;
        }


        sumGRNQty();
        totalAmount();
        srNo();

        // disabledDropdownVal();

    } else {
        var thisHtml = '';
        var counter = 1;
        for (let key in grnMaterialDetails) {
            var formIndx = key;
            var sr_no = counter;
            var grn_details_id = grnMaterialDetails[key].grn_details_id ? grnMaterialDetails[key].grn_details_id : "";
            var item_id = grnMaterialDetails[key].item_id ? grnMaterialDetails[key].item_id : "";
            var item_group_name = grnMaterialDetails[key].item_group_name ? grnMaterialDetails[key].item_group_name : "";
            var po_no = grnMaterialDetails[key].po_details_id ? grnMaterialDetails[key].po_details_id : "";
            var grn_qty = grnMaterialDetails[key].grn_qty ? parseFloat(grnMaterialDetails[key].grn_qty).toFixed(3) : "";
            // var grn_qty = grnMaterialDetails[key].grn_qty ? grnMaterialDetails[key].grn_qty : "";
            var item_code = grnMaterialDetails[key].item_code ? grnMaterialDetails[key].item_code : "";
            var rate_per_unit = grnMaterialDetails[key].rate_per_unit ? parseFloat(grnMaterialDetails[key].rate_per_unit).toFixed(3) : "";
            //var rate_per_unit = grnMaterialDetails[key].rate_per_unit ? grnMaterialDetails[key].rate_per_unit : "";
            var unit_name = grnMaterialDetails[key].unit_name ? grnMaterialDetails[key].unit_name : "";
            var so_amount = grnMaterialDetails[key].amount ? grnMaterialDetails[key].amount : "";
            var remarks = grnMaterialDetails[key].remarks ? checkSpecialCharacter(grnMaterialDetails[key].remarks) : "";
            var stock_qty = grnMaterialDetails[key].stock_qty ? parseFloat(grnMaterialDetails[key].stock_qty).toFixed(3) : "";
            var qc_required = grnMaterialDetails[key].qc_required ? grnMaterialDetails[key].qc_required : "";
            var service_item = grnMaterialDetails[key].service_item ? grnMaterialDetails[key].service_item : "";

            thisHtml += `<tr>
                            <td>
                                <a onclick="removeGrnDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                            </td>
                            <td class="sr_no">${sr_no}</td>
                            <td>
                                <input type="hidden" name="form_indx" value="${formIndx}" />
                                <input type="hidden" name="qc_required[]" value="${qc_required}">
                                    <input type="hidden" name="service_item[]" value="${service_item}">
                                        <input type="hidden" name="grn_details_id[]" value="${grn_details_id}">
                                            <input type="text" name="po_no[]" id="po_no" class="form-control salesmanageTable POaddtables" tabindex="-1" disabled />${po_no}</td>

                                        <td><input type="text" name="po_date[]" class="form-control potabledate salesmanageTable date-picker po_date" disabled /></td>
                                        <td> <select name="item_id[]" class="chzn-select  item_id grn_modal_item_select_width add_item item_id_${sr_no}" onChange="getItemData(this)">${productDrpHtml}</select></td>

                                        <td><input type="text" name="code[]" id="code" class="form-control salesmanageTable POaddtables" value="${item_code}" tabindex="-1" readonly /></td>

                                        <td><input type="text" name="group[]" id="group" class="form-control salesmanageTable POaddtables" value="${item_group_name}" tabindex="-1" readonly /></td>



                                        <td><input type="text" name="po_qty[]" id="po_qty" onblur="formatPoints(this,3)" class="form-control isNumberKey po_qty" style="width:50px;" disabled /></td>

                                        <td>
                                            <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null ? item_id : 0}">
                                                <input type="hidden" name="stock_qty[]" value="${stock_qty}">
                                                    <input type="hidden" name="org_grn_qty[]" value="${grn_qty}">
                                                        <input type="text" name="grn_qty[]" value="${grn_qty}" id="grn_qty" onblur="formatPoints(this,3)" onKeyup="sumGRNQty(this)" class="form-control allow-desimal grn_qty" style="width:50px;" />
                                                    </td>
                                                    <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly value="${unit_name}" /></td>
                                                    <td><input type="text" name="rate_unit[]" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit salesmanageTable isNumberKey" onblur="formatPoints(this,3)" / value="${rate_per_unit}"></td>
                                                    <td><input type="number" name="amount[]" id="amount" class="form-control amount salesmanageTable" onblur="formatPoints(this,3)" tabindex="-1" value="${parseFloat(so_amount).toFixed(3)}" readonly
                                                    /></td>

                                                    <td><textarea name="remarks[]" id="remarks_${grnMaterialDetails[key].grn_details_id}" rows="4" style="width:120px;" value="${remarks}" readonly /></td>

                                                </tr>`;


            // <td><input type="text" name="remarks[]" id="remarks" class="form-control salesmanageTable potableremarks" value="${remarks}" readonly /></td>

            counter++;

        }




        jQuery('#grnDetails tbody').append(thisHtml);

        var counter = 1;
        for (let key in grnMaterialDetails) {
            var item_id = grnMaterialDetails[key].item_id ? grnMaterialDetails[key].item_id : "";
            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            var print_po_remarks = grnMaterialDetails[key].remarks ? grnMaterialDetails[key].remarks : "";
            jQuery(`#remarks_${grnMaterialDetails[key].grn_details_id}`).val(print_po_remarks);
            counter++;
        }
    }
    sumGRNQty();
    totalAmount();
    srNo();

}


function getItemData(th) {

    var selected = jQuery(th).val();
    var thisselected = jQuery(th);
    var selectedOption = jQuery(th).find('option:selected');
    var secondaryItem = selectedOption.data('secondary_unit');

    var thisForm = jQuery('#GrnDetailsForm');
    var grnType = thisForm.find("input[name*='grn_type_fix_id']:checked").val();

    if (grnType == 3) {
        if (selected) {
            jQuery(th).parents('tr').find("#grn_qty").val('');
            var $grn_qty = jQuery(th).parents('tr').find("#grn_qty");
            if (secondaryItem == 'Yes') {
                $grn_qty.removeAttr("onblur");
                $grn_qty.removeClass("isNumberKey").addClass("only-numbers");

            } else {
                $grn_qty.attr("onblur", "formatPoints(this,3)");
                $grn_qty.removeClass("only-numbers").addClass("isNumberKey");
                jQuery(jQuery('.item_id').not(jQuery(th))).each(function (index) {

                    if (thisselected.val() == jQuery(this).val()) {
                        jAlert('This Item Is Already Selected.');
                        var selectTd = thisselected.closest('td');

                        selectTd.html(`<select name="item_id[]" class="chzn-select add_item grn_modal_item_select_width item_id" onChange="getItemData(this), sumSoQty(this)">${productDrpHtml}</select>`);
                        jQuery('.item_id').chosen();
                    }
                });

            }

        }

    } else {

        if (selected) {
            jQuery(jQuery('.item_id').not(jQuery(th))).each(function (index) {

                if (thisselected.val() == jQuery(this).val()) {
                    jAlert('This Item Is Already Selected.');
                    var selectTd = thisselected.closest('td');

                    selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id grn_modal_item_select_width" onChange="getItemData(this), sumSoQty(this)">${productDrpHtml}</select>`);
                    jQuery('.item_id').chosen();
                }
            });
        }

    }


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
                    // jQuery(th).parents('tr').find("#remarks").prop({ tabindex: -1, readonly: false });
                    jQuery(th).parents('tr').find("#po_no").prop('disabled', false);
                    jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);


                    jQuery(th).parents('tr').find("#service_item").val(data.item.service_item);
                    jQuery(th).parents('tr').find("#qc_required").val(data.item.qc_required);

                    var productDetailDrpHtml = ``;
                    if (data.item_detail.length > 0) {
                        var productDetailDrpHtml = `<select name="item_details_id[]" class="chzn-select grn_modal_item_select_width item_id item_details_id add_item_details" onChange="getItemDetailData(this)"><option value="">Select Item</option>`;
                        for (let indx in data.item_detail) {
                            productDetailDrpHtml += `<option value="${data.item_detail[indx].item_details_id}" data-second_unit="${data.item_detail[indx].unit_name}">${data.item_detail[indx].secondary_item_name} </option>`;
                        }

                        productDetailDrpHtml += `</select>`;
                    } else {
                        productDetailDrpHtml = `<input type="hidden" name="item_details_id[]" value="0" />`;
                    }

                    jQuery(th).parents('tr').find('td').eq(jQuery(th).closest('td').index() + 1).html(productDetailDrpHtml);

                    jQuery('.item_details_id').chosen();




                    var thisForm = jQuery('#GrnDetailsForm');
                    var grnType = thisForm.find("input[name*='grn_type_fix_id']:checked").val();

                    if (grnType == 2) {

                        jQuery(th).parents('tr').find("#po_no").prop('disabled', true);
                        jQuery(th).parents('tr').find("#po_qty").prop('disabled', true);
                        jQuery(th).parents('tr').find(".po_date").prop('disabled', true);

                    }

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
    jQuery(th).parents('tr').find("#unit").val(second_unit);
}

jQuery(document).on('change', '.add_item_details  ', function (e) {
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




// jQuery('#pendingPoModal').on('click', function (e) {
//     jQuery('#pendingPoModal').modal('show');
// });



var coaPartValidator = jQuery("#addPendingPOForm").validate({

    rules: {

        "pd_po_id[]": {

            required: true

        },

    },

    messages: {

        "pd_po_id[]": {

            // required: "Please Select Pending PO",
            required: "Please Select Item From Pending PO",

        }



    },

    submitHandler: function (form) {


        var chkCount = 0;

        var chkArr = [];

        var chkId = [];

        jQuery("#addPendingPOForm").find("[id^='po_details_ids_']").each(function () {



            var thisId = jQuery(this).attr('id');
            var splt = thisId.split('po_details_ids_');
            var intId = splt[1];



            if (jQuery(this).is(':checked')) {

                chkArr.push(jQuery(this).val())

                chkId.push(intId);

                chkCount++;

            }

        });



        if (chkCount == 0) {

            // jQuery('#pd_po_ids_' + chkId[0]).parent('td').addClass('error');

            // toastError('Please Select Pending PO');
            toastError('Please Select Item From Pending PO');



        } else {


            // jQuery('#pd_coa_ids_' + chkId[0]).parent('td').removeClass('error');

            // jQuery("#pendingCoaModal").find("#addPendingCoaModal").addClass('btn-loader');

            if (formId == undefined) {

                var url = RouteBasePath + "/get-po_part_data-grn?po_ids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-po_part_data-grn?po_ids=" + chkArr.join(',') + "&id=" + formId;
            }

            jQuery.ajax({



                url: url,

                type: 'GET',

                dataType: 'json',

                processData: false,

                success: function (data) {


                    // jQuery("#pendingCoaModal").find("#addPendingCoaModal").removeClass('btn-loader');

                    if (data.response_code == 1) {
                        if (data.item.length > 0) {
                            for (let indx in data.item) {
                                productDrpHtml += `<option value="${data.item[indx].id}">${data.item[indx].item_name} </option>`;
                            }
                            jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
                        }


                        po_data = [];
                        if (data.po_data.length > 0 && !jQuery.isEmptyObject(data.po_data)) {
                            for (let ind in data.po_data) {
                                po_data.push(data.po_data[ind]);
                            }

                            addPartDetail(data.po_data);
                            //  fillGRNTable(data.po_data);

                        }

                        jQuery("#pendingPoModal").modal('hide');


                        jQuery("input[name*='grn_type_fix_id']").prop({ tabindex: -1, readonly: true });

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
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    // jQuery('#grn_sequence').focus();
                    if (route_path == "grn_details") {
                        // console.log("113131");
                        jQuery("#grn_supplier_id").trigger('liszt:activate');
                    }
                    else if (route_path == "grn_location") {
                        // console.log("141414");
                        jQuery("#location_id").trigger('liszt:activate');
                    }
                    else {
                        // console.log("151515");
                        jQuery('#grn_date').focus();
                    }
                }, 1000);
            });
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
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                // jQuery('#grn_sequence').focus();
                                if (route_path == "grn_details") {
                                    // console.log("1616161");
                                    jQuery("#grn_supplier_id").trigger('liszt:activate');
                                }
                                else if (route_path == "grn_location") {
                                    // console.log("171717");
                                    jQuery("#location_id").trigger('liszt:activate');
                                }
                                else {
                                    // console.log("181818");
                                    jQuery('#grn_date').focus();
                                }
                            }, 1000);
                        });
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

//<--On Work Order Modal Show-->//
jQuery('#pendingPoModal').on('show.bs.modal', function (e) {

    // getPoData()

    var usedParts = [];
    var usedQcParts = [];

    jQuery('#grnDetails tbody input[name="form_indx"]').each(function (indx) {
        var frmIndx = jQuery(this).val();
        var podId = po_data[frmIndx].po_details_id;
        var qcUsed = po_data[frmIndx].in_use;
        if (podId != "" && podId != null) {
            usedParts.push(Number(podId));
        }

        if (qcUsed) {
            usedQcParts.push(Number(podId));
        }
    });

    function isUsed(pjId) {
        if (usedParts.includes(Number(pjId))) {
            return true;
        }
        return false;
    }

    function isQcUesd(pjId) {
        if (usedQcParts.includes(Number(pjId))) {
            return true;
        }
        return false;
    }

    var totalEntry = 0;
    jQuery('#pendingPOTable tbody tr').each(function (indx) {

        totalEntry++;
        var checkField = jQuery(this).find('input[name="po_details_id[]"]');
        var partId = jQuery(checkField).val();
        var inUse = isUsed(partId);
        var inQcUse = isQcUesd(partId)

        // if (inUse) {
        //     jQuery(checkField).addClass('in-use').prop({'disabled': true, 'checked': true });
        // } else {
        //     //jQuery(checkField).removeClass('in-use').prop({'disabled': false, 'checked': false });
        // }

        if (inUse) {
            if (inQcUse) {
                jQuery(checkField).addClass('in-use').prop('checked', true);
            } else {

                jQuery(checkField).removeClass('in-use').prop('checked', true);
            }

        } else {
            jQuery(checkField).prop('checked', false);
        }

    });






    var usedItemParts = [];
    var usedQcItemParts = [];

    jQuery('#grnDetails tbody input[name="form_indx"]').each(function (indx) {
        var frmIndx = jQuery(this).val();
        var podItemId = po_data[frmIndx].po_id;
        var qcUsedItem = po_data[frmIndx].in_use;
        if (podItemId != "" && podItemId != null) {
            usedItemParts.push(Number(podItemId));
        }
        if (qcUsedItem) {
            usedQcItemParts.push(Number(podItemId));
        }
    });


    function isItemUsed(pjitemId) {
        if (usedItemParts.includes(Number(pjitemId))) {
            return true;
        }
        return false;
    }
    function isQcItemUesd(pjitemId) {
        if (usedQcItemParts.includes(Number(pjitemId))) {
            return true;
        }
        return false;
    }

    var totalEntry = 0;
    jQuery('#pendingPODataTable tbody tr').each(function (indx) {

        totalEntry++;
        var checkField = jQuery(this).find('input[name="po_id[]"]');
        var partId = jQuery(checkField).val();
        var inUse = isItemUsed(partId);
        var inQcUse = isQcItemUesd(partId)



        if (inUse) {
            if (inQcUse) {
                jQuery(checkField).addClass('in-use').prop('checked', true);
            } else {

                jQuery(checkField).removeClass('in-use').prop('checked', true);
            }


        } else {
            jQuery(checkField).removeClass('in-use').prop('checked', false);
        }

    });


    setTimeout(() => {
        jQuery(this).find('#checkall-po').focus();
    }, 300);


});
jQuery('#checkall-po').click(function () {
    if (jQuery(this).is(':checked')) {
        // jQuery("#addPendingPOForm").find("[id^='po_details_ids_']").prop('checked', true).trigger('change');
        jQuery("#addPendingPOForm").find("[id^='po_details_ids_']:not(.in-use)").prop('checked', true).trigger('change');
    } else {
        // jQuery("#addPendingPOForm").find("[id^='po_details_ids_']").prop('checked', false).trigger('change');
        jQuery("#addPendingPOForm").find("[id^='po_details_ids_']:not(.in-use)").prop('checked', false).trigger('change');
    }
});


jQuery('#checkall-po_data').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#addPendingPODataForm").find("[id^='po_ids_']:not(.in-use)").prop('checked', true).trigger('change');
        // jQuery("#addPendingPODataForm").find("[id^='po_ids_']").prop('checked', true).trigger('change');
    } else {
        jQuery("#addPendingPODataForm").find("[id^='po_ids_']:not(.in-use)").prop('checked', false).trigger('change');
        // jQuery("#addPendingPODataForm").find("[id^='po_ids_']").prop('checked', false).trigger('change');
    }
});



jQuery(document).ready(function () {
    function updateTitle() {
        if (jQuery("#GrnDetailsForm").find('input[name="grn_type_fix_id"]:checked').val() == "1" || jQuery("#GrnDetailsForm").find('input[name="grn_type_fix_id"]:checked').val() == "2") {
            jQuery("#labelclass").html('Supplier <sup class="astric"> *</sup>');
        } else {
            jQuery("#labelclass").html('Supplier');
        }
    }
    updateTitle();
    jQuery("#GrnDetailsForm").find('input[name="grn_type_fix_id"]').change(function () {
        updateTitle();
    });
});




var coaPartValidator = jQuery("#addPendingDcForm").validate({
    rules: {
        "le_id[]": {
            required: true
        },
    },

    messages: {
        "le_id[]": {
            required: "Please Select Pending Dispatch",
        }
    },

    submitHandler: function (form) {
        var chkCount = 0;
        var chkArr = [];
        var chkId = [];

        jQuery("#addPendingDcForm").find("[id^='le_ids_']").each(function () {
            var thisId = jQuery(this).attr('id');
            var splt = thisId.split('le_ids_');
            var intId = splt[1];
            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });



        if (chkCount == 0) {
            toastError('Please Select Pending Dispatch');
        } else {
            if (formId == undefined) {
                var url = RouteBasePath + "/get-dc_part_data-grn?le_ids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-dc_part_data-grn?le_ids=" + chkArr.join(',') + "&id=" + formId;
            }

            jQuery.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.item.length > 0) {
                            for (let indx in data.item) {
                                productDrpHtml += `<option value="${data.item[indx].id}">${data.item[indx].item_name} </option>`;
                            }
                            jQuery('.item_id').empty().append(productDrpHtml).trigger('liszt:updated');
                        }

                        if (data.dc_data.length > 0 && !jQuery.isEmptyObject(data.dc_data)) {
                            for (let ind in data.dc_data) {
                                dc_data.push(data.dc_data[ind]);
                            }
                            addDCPartDetail(data.dc_data);

                            jQuery('#vehicle_no').val(data.dc_data[0].vehicle_no)
                        }
                        jQuery("#pendingDcModal").modal('hide');

                        jQuery('#addPart').prop('disabled', false);

                        jQuery("input[name*='grn_type_fix_id']").prop({ tabindex: -1, readonly: true });
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







function fillPendingGrnDc() {

    let locId = jQuery('#location_id option:selected').val();
    var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();

    var thisModal = jQuery('#pendingPoModal');
    var thisForm = jQuery('#GrnDetailsForm');

    if (formId != undefined) {
        jQuery('.toggleModalBtn').prop('disabled', true);

    } else {
        if (locId != "" && grn_type_fix_id == 3) {

            if (formId == undefined) {

                var Url = RouteBasePath + "/get-dc_list-grn?grn_location_id=" + locId;
            } else {
                var Url = RouteBasePath + "/get-dc_list-grn?grn_location_id=" + locId + "&id=" + formId;
            }

            jQuery.ajax({

                url: Url,

                type: 'GET',

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if (data.response_code == 1 && data.dc_data.length > 0) {

                        // new code
                        var usedParts = [];
                        var totalDisb = 0;
                        var found = 0;

                        thisForm.find('#grnDetails tbody input[name="form_indx"]').each(function (indx) {
                            let frmIndx = jQuery(this).val();


                            let jbEorkOrderId = dc_data[frmIndx].dp_details_id;
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

                        if (data.dc_data.length > 0 && !jQuery.isEmptyObject(data.dc_data)) {

                            found = 1;

                            for (let idx in data.dc_data) {

                                let inUse = isUsed(data.dc_data[idx].dp_details_id);
                                var in_use = data.dc_data[idx].in_use == true ? 'readonly' : '';
                                totalEntry++;
                                tblHtml += `<tr>
                                                    <td><input type="radio" name="le_id[]" class="simple-check ${inUse ? 'in-use' : ''}" id="le_ids_${data.dc_data[idx].le_id}"
                                                        value="${data.dc_data[idx].le_id}" ${inUse ? 'checked' : ''} ${in_use} /></td>
                                                    <td>${data.dc_data[idx].dp_number}</td>
                                                    <td>${data.dc_data[idx].dp_date}</td>
                                                    <td>${data.dc_data[idx].vehicle_no != null ? data.dc_data[idx].vehicle_no : ''}</td>
                                                    <td>${data.dc_data[idx].transporter_name != null ? data.dc_data[idx].transporter_name : ''}</td>
                                                </tr>`;

                            }

                        } else {

                            tblHtml += `<tr class="centeralign" id="noPendingPo">

                            <td colspan="5">No Pending PO Available</td>

                        </tr>`;

                        }

                        jQuery('#pendingDcTable tbody').empty().append(tblHtml);

                        // thisModal.find('#pendingPOTable tbody').empty().append(tblHtml);
                        // if (found == 1) {
                        //     if (totalDisb == totalEntry) {
                        //         thisModal.find('#pendingPoModal').prop('disabled', true);
                        //     } else {
                        //         thisModal.find('#pendingPoModal').prop('disabled', false);
                        //     }
                        //     thisForm.find('.toggleModalBtn').prop('disabled', false);

                        // } else {
                        //     resetPdWoForm();
                        //     thisForm.find('.toggleModalBtn').prop('disabled', true);
                        // }



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



}



function addDCPartDetail($fillData = null) {
    if ($fillData != null && $fillData.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in $fillData) {

            var formIndx = key;
            var sr_no = counter;
            var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
            var grn_details_type = $fillData[key].grn_details_type ? $fillData[key].grn_details_type : "";
            var item_details_id = $fillData[key].item_details_id ? $fillData[key].item_details_id : "";
            var dp_details_id = $fillData[key].dp_details_id ? $fillData[key].dp_details_id : "";
            var le_details_id = $fillData[key].le_details_id ? $fillData[key].le_details_id : "";
            var le_secondary_details_id = $fillData[key].le_secondary_details_id ? $fillData[key].le_secondary_details_id : "";
            var dp_number = $fillData[key].dp_number ? $fillData[key].dp_number : "";
            var dp_date = $fillData[key].dp_date ? $fillData[key].dp_date : "";
            var item_code = $fillData[key].item_code ? $fillData[key].item_code : "";
            var item_group_name = $fillData[key].item_group_name ? $fillData[key].item_group_name : "";
            var grn_qty = $fillData[key].grn_qty > 0 ? parseFloat($fillData[key].grn_qty).toFixed(3) : 0;
            var unit_name = $fillData[key].unit_name ? $fillData[key].unit_name : "";
            var rate_per_unit = $fillData[key].rate_per_unit ? parseFloat($fillData[key].rate_per_unit).toFixed(3) : "";

            var grn_details_id = formId == undefined ? 0 : $fillData[key].grn_details_id != null ? $fillData[key].grn_details_id : 0;

            var grn_secondary_details_id = formId == undefined ? 0 : $fillData[key].grn_secondary_details_id != null ? $fillData[key].grn_secondary_details_id : 0;

            var qc_required = $fillData[key].qc_required ? $fillData[key].qc_required : "";

            var service_item = $fillData[key].service_item ? $fillData[key].service_item : "";

            var in_use = $fillData[key].in_use ? $fillData[key].in_use : "";

            var remarks = $fillData[key].remarks ? $fillData[key].remarks : "";


            var used_qty = $fillData[key].used_qty ? $fillData[key].used_qty.toFixed(3) : "";

            if (dp_details_id != '' && dp_details_id != null) {
                // var loading_qty = $fillData[key].plan_qty ? $fillData[key].plan_qty.toFixed(3) : $fillData[key].loading_qty.toFixed(3);
                // var loading_qty = $fillData[key].loading_qty ? $fillData[key].loading_qty : $fillData[key].loading_qty;
                var loading_qty = $fillData[key].loading_qty ? parseFloat($fillData[key].loading_qty).toFixed(3) : parseFloat($fillData[key].loading_qty).toFixed(3);
            } else {
                var loading_qty = parseFloat(0).toFixed(3);
                // var loading_qty = 0;
            }

            if (dp_details_id != '' && dp_details_id != null) {
                var max_loading_qty = $fillData[key].plan_qty ? parseFloat($fillData[key].plan_qty).toFixed(3) : parseFloat($fillData[key].loading_qty).toFixed(3);
                // var max_loading_qty = $fillData[key].plan_qty ? $fillData[key].plan_qty : $fillData[key].loading_qty;
            } else {
                var max_loading_qty = '';
            }

            if (grn_details_id == 0) {
                var grn_qty = '';
            } else {
                var grn_qty = $fillData[key].grn_qty ? item_details_id != "" ? $fillData[key].grn_qty : $fillData[key].grn_qty.toFixed(3) : parseFloat(0).toFixed(3);
            }



            var productDetailDrpHtml = ``;
            if ($fillData[key].item_detail.length > 0) {
                var productDetailDrpHtml = `<select name="item_details_id[]" class="chzn-select grn_modal_item_select_width item_id item_details_ids_${sr_no} add_item_details" tabindex="-1" ${grn_details_type == 'manual' ? in_use == true ? 'readonly' : '' : 'readonly'}><option value="">Select Item</option>`;
                for (let indx in $fillData[key].item_detail) {
                    productDetailDrpHtml += `<option value="${$fillData[key].item_detail[indx].item_details_id}" >${$fillData[key].item_detail[indx].secondary_item_name} </option>`;
                }

                productDetailDrpHtml += `</select>`;
            } else {
                productDetailDrpHtml = `<input type="hidden" name="item_details_id[]" value="0" />`;
            }




            thisHtml += `
                                                <tr>
                                                    <td>${grn_details_type == 'manual' ? in_use == true ? '' : '<a onclick="removeGrnDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>' : ''}</td>
                                                    <td class="sr_no">${sr_no}</td>

                                                    <td>
                                                        <input type="hidden" name="grn_details_id[]" value="${grn_details_id}">
                                                            <input type="hidden" name="grn_secondary_details_id[]" value="${grn_secondary_details_id}">
                                                                <input type="hidden" name="form_indx" value="${formIndx}" />
                                                                <input type="hidden" name="dp_details_id[]" value="${dp_details_id}">
                                                                    <input type="hidden" name="le_details_id[]" value="${le_details_id}">
                                                                        <input type="hidden" name="le_secondary_details_id[]" value="${le_secondary_details_id}">
                                                                            <input type="hidden" name="qc_required[]" id="qc_required" value="${qc_required}">
                                                                                <input type="hidden" name="service_item[]" id="service_item" value="${service_item}">
                                                                                    <input type="hidden" name="grn_details_type[]" value="${grn_details_type}">
                                                                                        <input type="hidden" name="dp_number[]" id="dp_number" class="form-control" tabindex="-1" value="" disabled />${dp_number}</td>
                                                                                    <td>
                                                                                        <input type="hidden" name="dp_date[]" class="form-control potabledatedate-picker dp_date" value="${dp_date}" disabled />${dp_date}</td>

                                                                                    <td> <select name="item_id[]" class="chzn-select grn_modal_item_select_width  item_id add_item item_id_${sr_no}" onChange="getItemData(this)" tabindex="-1" ${grn_details_type == 'manual' ? in_use == true ? 'readonly' : '' : 'readonly'}>${productDrpHtml}</select></td>

                                                                                    <td>${productDetailDrpHtml}</td>

                                                                                    <td><input type="text" name="code[]" id="code" class="form-control salesmanageTable POaddtables" value="${item_code}" tabindex="-1" readonly /></td>

                                                                                    <td><input type="text" name="group[]" id="group" class="form-control salesmanageTable POaddtables" value="${item_group_name}" tabindex="-1" readonly /></td>`;

            if (item_details_id != "") {
                thisHtml += `<td> 
                             <input type="hidden" name="loading_qty[]" id="loading_qty" value="${loading_qty}">
                             <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null && item_id != '' ? item_id : 0}">
                             <input type="hidden" name="pre_item_detail[]" id="pre_item_detail" value="${item_details_id != null && item_details_id != '' ? item_details_id : 0}">
                             <input type="hidden" name="org_grn_qty[]" value="${grn_qty}">
                             <input type="text" name="grn_qty[]" id="grn_qty" onkeyup="sumPoGRNQty(this)" class="form-control only-numbers grn_qty" style="width:50px;"   min="${used_qty}"   value="${grn_qty}" ${in_use == true ? 'readonly' : ''}/></td>`;

            } else {
                thisHtml += `<td> 
                             <input type="hidden" name="loading_qty[]" id="loading_qty" value="${loading_qty}">
                             <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null && item_id != '' ? item_id : 0}">
                             <input type="hidden" name="pre_item_detail[]" id="pre_item_detail" value="${item_details_id != null && item_details_id != '' ? item_details_id : 0}">
                             <input type="hidden" name="org_grn_qty[]" value="${grn_qty}">
                             <input type="text" name="grn_qty[]" id="grn_qty" onkeyup="sumPoGRNQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey grn_qty" style="width:50px;"   min="${used_qty}"   value="${grn_qty}" ${in_use == true ? 'readonly' : ''}/></td>`;

            }


            thisHtml += `    <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly value="${unit_name}" /></td>

                                                                                    <td><textarea name="remarks[]" id="remarks_${sr_no}" style="width:120px;" rows="4" value="${remarks}">${remarks}</textarea></td>

                                                                                </tr>`;

            counter++;



        }


        setTimeout(() => {
            var count = 1;
            for (let key in $fillData) {
                var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
                var item_details_id = $fillData[key].item_details_id ? $fillData[key].item_details_id : "";
                jQuery(`.item_id_${count}`).val(item_id).trigger('liszt:updated');
                jQuery(`.item_details_ids_${count}`).val(item_details_id).trigger('liszt:updated');
                count++;
            }

        }, 100)




        jQuery('#grnDetails tbody').empty().append(thisHtml);

        jQuery('#grnDetails tbody tr').each(function (indx, tr) {
            if (jQuery(jQuery(tr)[0]).css('display') != 'none') {
                var GrnQty = jQuery(tr).find('td [name="grn_qty[]"]').val();
                var RateUnit = jQuery(tr).find('td [name="rate_unit[]"]').val();

                var Amount = parseFloat(GrnQty) * parseFloat(RateUnit);
                jQuery(tr).find('td [name="amount[]"]').val(isNaN(Amount) ? 0 : parseFloat(Amount));
                formatPoints(jQuery(tr).find('td [name="amount[]"]'), 3);
            }
        });

        // totalAmount();
        disabledDropdownVal();

    }



    srNo();
    // sumGRNQty();
    // totalAmount();

    // Reinitialize date-picker for new elements
    jQuery('.date-picker').datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
    });

}



jQuery('#pendingDcModal').on('show.bs.modal', function (e) {
    var usedParts = [];
    var usedQcParts = [];

    var totalDisb = 0;

    jQuery('#grnDetails tbody input[name="form_indx"]').each(function (indx) {
        let frmIndx = jQuery(this).val();
        let podId = dc_data[frmIndx].dp_details_id;
        var qcUsed = dc_data[frmIndx].in_use;
        // console.log(podId);
        if (podId != "" && podId != null) {
            usedParts.push(Number(podId));
        }
        if (qcUsed) {
            usedQcParts.push(Number(podId));
        }
    });

    function isUsed(pjId) {
        if (usedParts.includes(Number(pjId))) {
            totalDisb++;
            return true;
        }
        return false;
    }

    function isQcUesd(pjId) {
        if (usedQcParts.includes(Number(pjId))) {
            return true;
        }
        return false;
    }


    let totalEntry = 0;
    // console.log('model2')
    jQuery('#pendingDcTable tbody tr').each(function (indx) {
        // console.log('modelbody21', indx)
        totalEntry++;
        let checkField = jQuery(this).find('input[name="dp_detail_id[]"]');
        let partId = jQuery(checkField).val();
        let inUse = isUsed(partId);
        var inQcUse = isQcUesd(partId);

        if (inUse) {
            if (inQcUse) {
                jQuery(checkField).addClass('in-use').prop('checked', true);
            } else {

                jQuery(checkField).removeClass('in-use').prop('checked', true);
            }

        } else {
            jQuery(checkField).prop('checked', false);
        }



    });

    if (totalDisb == totalEntry) {
        jQuery('#pendingDcModal').prop('disabled', true);
    } else {
        jQuery('#pendingDcModal').prop('disabled', false);
    }

    setTimeout(() => {
        jQuery(this).find('#checkall-dc').focus();
    }, 300);


});



jQuery('#checkall-dc').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#addPendingDcForm").find("[id^='le_ids_']:not(.in-use)").prop('checked', true).trigger('change');
    } else {
        jQuery("#addPendingDcForm").find("[id^='le_ids_']:not(.in-use)").prop('checked', false).trigger('change');
    }
});





function poTable() {
    var tableHtml = `<thead>
                                                                                    <tr>
                                                                                        <th>Action</th>
                                                                                        <th>Sr. No.</th>
                                                                                        <th>PO No.</th>
                                                                                        <th>PO Date</th>
                                                                                        <th>Item</th>
                                                                                        <th> Code</th>
                                                                                        <th> Group</th>
                                                                                        <th>Pend. PO Qty.</th>
                                                                                        <th>GRN Qty.</th>
                                                                                        <th>Unit</th>
                                                                                        <th>Rate/Unit</th>
                                                                                        <th>Amount</th>
                                                                                        <th>Remark</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                </tbody>
                                                                                <tfoot>
                                                                                    <tr class="total_tr"><td colspan="8" ></td>
                                                                                        <td class="grnqtysum" name="grn_total_qty"></td>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td class="amountsum" name="grn_total_amount">
                                                                                            <td></td>
                                                                                    </tr>

                                                                                </tfoot>`;

    jQuery('#grnDetails').html(tableHtml);
}


function dcTable() {
    var tableHtml = `<thead>
                                                                                    <tr>
                                                                                        <th>Action</th>
                                                                                        <th>Sr. No.</th>
                                                                                        <th>Dispatch Plan No.</th>
                                                                                        <th>Dispatch Plan Date</th>
                                                                                        <th>Item</th>
                                                                                        <th>Item Detail Name</th>
                                                                                        <th> Code</th>
                                                                                        <th> Group</th>
                                                                                        <th>GRN Qty.</th>
                                                                                        <th>Unit</th>
                                                                                        <th>Remark</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>


                                                                                </tbody>
                                                                                <tfoot>
                                                                                    <tr class="total_tr"><td colspan="8" ></td>
                                                                                        <td class="grnqtysum" name="grn_total_qty"></td>
                                                                                        <td colspan="2"></td>
                                                                                    </tr>

                                                                                </tfoot>`;

    jQuery('#grnDetails').html(tableHtml);
}


function enablePrint(grn_id) {
    if (grn_id != '') {
        jQuery.ajax({
            url: RouteBasePath + "/get-exceed_qty-grn?grn_id=" + grn_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {

                if (data.response_code == 1) {
                    if (data.in_use == false) {
                        jQuery('#grnprintButton').prop('disabled', false);
                    }
                }

            },
        });

    }

}



async function fillPendingGrn() {
    return new Promise((resolve, reject) => {
        let supId = jQuery('#grn_supplier_id option:selected').val();
        var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();

        var thisModal = jQuery('#pendingPoModal');
        var thisForm = jQuery('#GrnDetailsForm');

        if (supId != "" && grn_type_fix_id == 1) {
            if (formId == undefined) {
                var Url = RouteBasePath + "/get-po_list-grn?grn_supplier_id=" + supId;
            } else {
                var Url = RouteBasePath + "/get-po_list-grn?grn_supplier_id=" + supId + "&id=" + formId;
            }

            jQuery.ajax({
                url: Url,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1 && data.po_data.length > 0) {
                        // new code
                        var usedParts = [];
                        var totalDisb = 0;
                        var found = 0;

                        thisForm.find('#grnDetails tbody input[name="form_indx"]').each(function (indx) {
                            let frmIndx = jQuery(this).val();

                            let jbEorkOrderId = po_data[frmIndx].po_id;
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

                        if (data.po_data.length > 0 && !jQuery.isEmptyObject(data.po_data)) {
                            found = 1;

                            for (let idx in data.po_data) {
                                var inUse = isUsed(data.po_data[idx].po_id);
                                var in_use = data.po_data[idx].in_use == true ? 'readonly' : '';
                                totalEntry++;
                                tblHtml += `<tr>
                                                                                    <td><input type="checkbox" name="po_id[]" class="simple-check ${inUse ? 'in-use' : ''}" id="po_ids_${data.po_data[idx].po_id}"
                                                                                        value="${data.po_data[idx].po_id}" ${inUse ? 'checked' : ''} onchange="getPoData()" ${in_use} /></td>
                                                                                    <td>${data.po_data[idx].po_number}</td>
                                                                                    <td>${data.po_data[idx].po_date}</td>
                                                                                    <td>${data.po_data[idx].location_name}</td>
                                                                                </tr>`;
                            }

                        } else {

                            tblHtml += `<tr class="centeralign" id="noPendingPo">
                            <td colspan="5">No Pending PO Available</td>
                        </tr>`;

                        }

                        jQuery('#pendingPODataTable tbody').empty().append(tblHtml);


                        resolve();

                        if (grn_type_fix_id == 2) {
                            jQuery('.toggleModalBtn').prop('disabled', true);

                        } else {
                            setTimeout(() => {
                                jQuery('.toggleModalBtn').prop('disabled', false);
                            }, 1000);

                        }

                    } else {
                        jQuery('.toggleModalBtn').prop('disabled', true);
                        toastError(data.response_message);
                    }
                },

                error: function (jqXHR, textStatus, errorThrown) {
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
            jQuery('.toggleModalBtn').prop('disabled', true);
        }
    });
}


function getPoData() {
    var chkPOId = [];
    jQuery("#addPendingPODataForm").find("[id^='po_ids_']").each(function () {
        var thisId = jQuery(this).attr('id');
        var splt = thisId.split('po_ids_');
        var intId = splt[1];

        if (jQuery(this).is(':checked')) {
            chkPOId.push(jQuery(this).val())
        }

    });

    if (chkPOId.length > 0) {
        let supId = jQuery('#grn_supplier_id option:selected').val();
        var grn_type_fix_id = jQuery("input[name*='grn_type_fix_id']:checked").val();

        var thisModal = jQuery('#pendingPoModal');
        var thisForm = jQuery('#GrnDetailsForm');

        if (supId != "" && grn_type_fix_id == 1) {
            if (formId == undefined) {
                var Url = RouteBasePath + "/get-po_item_list-grn?grn_supplier_id=" + supId + "&chkPOId=" + chkPOId.join(',');
            } else {
                var Url = RouteBasePath + "/get-po_item_list-grn?grn_supplier_id=" + supId + "&id=" + formId + "&chkPOId=" + chkPOId.join(',');
            }

            jQuery.ajax({
                url: Url,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1 && data.po_data.length > 0) {


                        // new code
                        var usedParts = [];
                        var totalDisb = 0;
                        var found = 0;

                        thisForm.find('#grnDetails tbody input[name="form_indx"]').each(function (indx) {
                            let frmIndx = jQuery(this).val();

                            let jbEorkOrderId = po_data[frmIndx].po_details_id;
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
                        var tblitemHtml = ``;
                        var found = 0;

                        // end new code
                        // <td>${data.po_data[idx].pend_po_qty != null ? data.po_data[idx].po_qty >= data.po_data[idx].pend_po_qty ? parseFloat(data.po_data[idx].pend_po_qty).toFixed(3) : parseFloat(0).toFixed(3) : parseFloat(0).toFixed(3)}</td>          
                        if (data.po_data.length > 0 && !jQuery.isEmptyObject(data.po_data)) {
                            found = 1;

                            for (let idx in data.po_data) {
                                var inUse = isUsed(data.po_data[idx].po_details_id);
                                var in_use = data.po_data[idx].in_use == true ? 'readonly' : '';
                                totalEntry++;
                                tblitemHtml += `<tr>
                                                                                    <td><input type="checkbox" name="po_details_id[]" class="simple-check ${inUse ? 'in-use' : ''}" id="po_details_ids_${data.po_data[idx].po_details_id}"
                                                                                        value="${data.po_data[idx].po_details_id}" ${inUse ? 'checked' : 'checked'} ${in_use} /></td>
                                                                                    <td>${data.po_data[idx].item_name}</td>
                                                                                    <td>${data.po_data[idx].item_code}</td>
                                                                                    <td>${data.po_data[idx].item_group_name}</td>
                                                                                    <td>${data.po_data[idx].po_qty != null ? parseFloat(data.po_data[idx].po_qty).toFixed(3) : ""}</td>
                                                                                    <td>${(parseFloat(data.po_data[idx].show_pend_qty) + parseFloat(data.po_data[idx].grn_qty)).toFixed(3)}</td>
                                                                                    <td>${data.po_data[idx].unit_name}</td>
                                                                                    <td>${data.po_data[idx].del_date}</td>
                                                                                </tr>`;
                            }

                        } else {

                            tblitemHtml += `<tr class="centeralign" id="noPendingPo">
                            <td colspan="5">No Pending PO Available</td>
                        </tr>`;

                        }

                        jQuery('#pendingPOTable tbody').empty().append(tblitemHtml);




                        if (grn_type_fix_id == 2) {
                            jQuery('.toggleModalBtn').prop('disabled', true);
                        } else {
                            jQuery('.toggleModalBtn').prop('disabled', false);
                        }

                    } else {
                        jQuery('.toggleModalBtn').prop('disabled', true);
                        toastError(data.response_message);
                    }
                },

                error: function (jqXHR, textStatus, errorThrown) {
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
            jQuery('.toggleModalBtn').prop('disabled', true);
        }
    } else {

        var tblitemHtml = `<tr class="centeralign" id="noPendingPo">
                                                                                    <td colspan="5">No record found!</td>
                                                                                </tr>`;

        jQuery('#pendingPOTable tbody').empty().append(tblitemHtml);
    }


}


function resetPOdata() {

    var tblitemHtml = `<tr class="centeralign" id="noPendingPo">
                                                                                    <td colspan="5">No record found!</td>
                                                                                </tr>`;

    jQuery('#pendingPOTable tbody').empty().append(tblitemHtml);

}




function addLoadingPartDetail() {

    // <td><textarea name="remarks[]" id="remarks" rows="4" readonly /></td>
    var thisHtml = `
                                                                                <tr>
                                                                                    <td><a onclick="removeGrnDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>
                                                                                    <td class="sr_no"></td>
                                                                                    <td><input type="hidden" name="grn_secondary_details_id[]" value="0">
                                                                                        <input type="hidden" name="grn_details_id[]" value="0">
                                                                                            <input type="hidden" name="dp_details_id[]" value="0">
                                                                                                <input type="hidden" name="le_details_id[]" value="0">
                                                                                                    <input type="hidden" name="le_secondary_details_id[]" value="0">
                                                                                                        <input type="hidden" name="qc_required[]" id="qc_required">
                                                                                                            <input type="hidden" name="service_item[]" id="service_item">
                                                                                                                <input type="hidden" name="grn_details_type[]" value="manual">
                                                                                                                </td>
                                                                                                                <td></td>

                                                                                                                <td> <select name="item_id[]" class="chzn-select  item_id grn_modal_item_select_width " onChange="getItemData(this)">${productDrpHtml}</select></td>

                                                                                                                <td><input type="hidden" name="item_details_id[]" value="0" /></td>

                                                                                                                <td><input type="text" name="code[]" id="code" class="form-control salesmanageTable POaddtables" tabindex="-1" readonly /></td>

                                                                                                                <td><input type="text" name="group[]" id="group" class="form-control salesmanageTable POaddtables" tabindex="-1" readonly /></td>

                                                                                                                <td>
                                                                                                                    <input type="hidden" name="loading_qty[]" id="loading_qty" value="0">
                                                                                                                        <input type="hidden" name="pre_item[]" id="pre_item" value="0">
                                                                                                                            <input type="hidden" name="pre_item_detail[]" id="pre_item_detail" value="0">
                                                                                                                                <input type="hidden" name="org_grn_qty[]" value="0">
                                                                                                                                    <input type="text" name="grn_qty[]" id="grn_qty" onKeyup="sumGRNQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey grn_qty" style="width:50px;" /></td>

                                                                                                                                <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly /></td>

                                                                                                                                <td><textarea name="remarks[]" id="remarks" rows="4" style="width:120px;"/></td>

                                                                                                                            </tr>`;



    jQuery('#grnDetails tbody').append(thisHtml);




    srNo();
    sumGRNQty();
    // totalAmount();

    // Reinitialize date-picker for new elements
    jQuery('.date-picker').datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
    });

}




// function addDCPartDetail($fillData = null) {
//     if ($fillData != null && $fillData.length > 0) {
//         var thisHtml = '';
//         var counter = 1;
//         for (let key in $fillData) {

//             var formIndx = key;
//             var sr_no = counter;
//             var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
//             var dp_details_id = $fillData[key].dp_details_id ? $fillData[key].dp_details_id : "";
//             var mr_number = $fillData[key].mr_number ? $fillData[key].mr_number : "";
//             var mr_date = $fillData[key].mr_date ? $fillData[key].mr_date : "";
//             var dp_number = $fillData[key].dp_number ? $fillData[key].dp_number : "";
//             var dp_date = $fillData[key].dp_date ? $fillData[key].dp_date : "";
//             var item_code = $fillData[key].item_code ? $fillData[key].item_code : "";
//             var item_group_name = $fillData[key].item_group_name ? $fillData[key].item_group_name : "";
//             var grn_qty = $fillData[key].grn_qty > 0 ? parseFloat($fillData[key].grn_qty).toFixed(3) : 0;
//             var unit_name = $fillData[key].unit_name ? $fillData[key].unit_name : "";
//             var rate_per_unit = $fillData[key].rate_per_unit ? parseFloat($fillData[key].rate_per_unit).toFixed(3) : "";

//             var grn_details_id = formId == undefined ? 0 : $fillData[key].grn_details_id != null ? $fillData[key].grn_details_id : 0;

//             var qc_required = $fillData[key].qc_required ? $fillData[key].qc_required : "";

//             var service_item = $fillData[key].service_item ? $fillData[key].service_item : "";

//             var in_use = $fillData[key].in_use ? $fillData[key].in_use : "";

//             var used_qty = $fillData[key].used_qty ? $fillData[key].used_qty.toFixed(2) : "";

//             if (grn_details_id == 0) {
//                 var pend_plan_qty = $fillData[key].pend_plan_qty ? parseFloat($fillData[key].pend_plan_qty).toFixed(3) : "";
//             } else {
//                 // var pend_plan_qty = $fillData[key].pend_plan_qty < $fillData[key].plan_qty ? parseFloat($fillData[key].pend_plan_qty).toFixed(3) : parseFloat(0).toFixed(3);

//                 var pend_plan_qty = $fillData[key].pend_plan_qty ? (parseFloat($fillData[key].pend_plan_qty) + parseFloat($fillData[key].grn_qty)).toFixed(3) : grn_qty;

//             }




//             thisHtml += `
//                         <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="grn_details_id[]" value="${grn_details_id}"></td></tr>
//                         <tr>
//                             <td><a ${in_use == true ? '' : 'onclick = "removeGrnDetails(this)"'}><i class="action-icon iconfa-trash so_details"></i></a></td>
//                             <td class="sr_no">${sr_no}</td>
//                             <td><input type="hidden" name="form_indx" value="${formIndx}" />
//                                 <input type="hidden" name="dp_details_id[]" value="${dp_details_id}">
//                                 <input type="hidden" name="qc_required[]" value="${qc_required}">
//                                 <input type="hidden" name="service_item[]" value="${service_item}">
//                                 <input type="hidden" name="mr_number[]" id="mr_number" class="form-control" tabindex="-1" value="" disabled />${mr_number}
//                             </td>
//                             <td><input type="hidden" name="mr_date[]" class="form-control potabledatedate-picker dp_date" value="${mr_date}" disabled />${mr_date}</td>
//                             <td> <input type="hidden" name="dp_number[]" id="dp_number" class="form-control" tabindex="-1" value="" disabled />${dp_number}</td>
//                             <td><input type="hidden" name="dp_date[]" class="form-control potabledatedate-picker dp_date" value="${dp_date}" disabled />${dp_date}</td>

//                             <td> <select name="item_id[]" class="chzn-select  item_id add_item item_id_${sr_no}" onChange="getItemData(this)" tabindex="-1" readonly>${productDrpHtml}</select></td>

//                             <td><input type="hidden" name="code[]" id="code" class="form-control salesmanageTable POaddtables" value="${item_code}" tabindex="-1" readonly />${item_code}</td>

//                             <td><input type="hidden" name="group[]" id="group" class="form-control salesmanageTable POaddtables" value="${item_group_name}" tabindex="-1" readonly />${item_group_name}</td>

//                             <td>
//                             <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id != null ? item_id : 0}">
//                             <input type="hidden" name="org_grn_qty[]" value="${grn_qty}">
//                             <input type="text" name="plan_qty[]" id="plan_qty" onblur="formatPoints(this,3)" class="form-control isNumberKey plan_qty" style="width:50px;" disabled value="${pend_plan_qty}" /></td>

//                             <td><input type="text" name="grn_qty[]" id="grn_qty" onkeyup="sumPoGRNQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey grn_qty" style="width:50px;" value="${grn_qty > 0 ? grn_qty : pend_plan_qty}" readonly min="${used_qty}"  tabindex="-1"/></td>

//                             <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="-1" readonly value="${unit_name}" /></td>

//                             <td><input type="text" name="rate_unit[]" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control rate_unit salesmanageTable isNumberKey" onblur="formatPoints(this,3)" value="${rate_per_unit}" readonly></td>

//                             <td><input type="text" name="amount[]" id="amount" class="form-control salesmanageTable amount" readonly tabindex="-1" /></td>

//                             </tr>`;

//             counter++;



//         }


//         setTimeout(() => {
//             var count = 1;
//             for (let key in $fillData) {
//                 var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";
//                 jQuery(`.item_id_${count}`).val(item_id).trigger('liszt:updated');
//                 count++;
//             }

//         }, 100)




//         jQuery('#grnDetails tbody').empty().append(thisHtml);

//         jQuery('#grnDetails tbody tr').each(function (indx, tr) {
//             if (jQuery(jQuery(tr)[0]).css('display') != 'none') {
//                 var GrnQty = jQuery(tr).find('td [name="grn_qty[]"]').val();
//                 var RateUnit = jQuery(tr).find('td [name="rate_unit[]"]').val();

//                 var Amount = parseFloat(GrnQty) * parseFloat(RateUnit);
//                 jQuery(tr).find('td [name="amount[]"]').val(isNaN(Amount) ? 0 : parseFloat(Amount));
//                 formatPoints(jQuery(tr).find('td [name="amount[]"]'), 3);
//             }
//         });

//         totalAmount();
//         disabledDropdownVal();

//     }



//     srNo();
//     sumGRNQty();
//     totalAmount();

//     // Reinitialize date-picker for new elements
//     jQuery('.date-picker').datepicker({
//         dateFormat: "dd/mm/yy",
//         autoclose: true,
//     });

// }