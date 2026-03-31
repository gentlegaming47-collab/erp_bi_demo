
setTimeout(() => {
    // jQuery('#cre_sequence').focus();
    jQuery('#rep_customer_id').trigger('liszt:activate');
}, 100);
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

var cre_details = [];

var creformId = jQuery('#customerReplacementEntryForm').find('input:hidden[name="id"]').val();

if (creformId != undefined && creformId != "") {
    jQuery(document).ready(function () {
        let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

        jQuery('#show-progress').addClass('loader-progress-whole-page');

        jQuery.ajax({
            url: RouteBasePath + "/get-customer_replacement_entry/" + creformId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    // console.log(data.cre_data.customer_name)
                    getOldCustomer();
                    // setTimeout(() => {
                    //     jQuery('#cre_sequence').focus();
                    // }, 100);

                    jQuery('#cre_sequence').val(data.cre_data.cre_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery('#cre_no').val(data.cre_data.cre_number).prop({ tabindex: -1, readonly: true });
                    // jQuery('#cre_no').val(data.cre_data.cre_number).prop('readonly', true);
                    // jQuery('#cre_no').attr('tabindex', -1);
                    jQuery('#cre_date').val(data.cre_data.cre_date);
                    jQuery('#reg_no').val(data.cre_data.customer_reg_no);
                    // jQuery('#customer_name').val(data.cre_data.customer_name);
                    jQuery("#rep_customer_name").val(data.cre_data.rep_customer_name);
                    setTimeout(() => {
                        jQuery('#rep_customer_id').val(data.cre_data.rep_customer_id).trigger('liszt:updated');
                    }, 1500);
                    jQuery('#cre_village').val(data.cre_data.cre_village);
                    jQuery('#cre_pincode').val(data.cre_data.cre_pincode);
                    jQuery('#so_country_id').val(data.cre_data.cre_country_id).trigger('liszt:updated');
                    jQuery('#customer_group_id').val(data.cre_data.customer_group_id).trigger('liszt:updated');
                    jQuery('#sp_notes').val(data.cre_data.special_notes);
                    jQuery("#customerReplacementEntryForm").find("#search_customer_val").val(1);

                    if (data.cre_data.cre_country_id != null) {
                        getSoStates().done(function (resposne) {
                            jQuery('#cre_state_id').val(data.cre_data.cre_state_id).trigger('liszt:updated');
                            getSoDistrict().done(function (resposne) {
                                jQuery('#cre_district_id').val(data.cre_data.cre_district_id).trigger('liszt:updated');
                                getSoTaluka().done(function (resposne) {
                                    jQuery('#cre_taluka_id').val(data.cre_data.cre_taluka_id).trigger('liszt:updated');
                                });
                            });
                        });
                    }
                    if (data.cre_datails.length > 0 && !jQuery.isEmptyObject(data.cre_datails)) {
                        for (let ind in data.cre_datails) {
                            cre_details.push(data.cre_datails[ind]);
                        }
                        FillDetailsTable(data.cre_datails);
                    }

                    // setTimeout(() => {
                    //     jQuery('#cre_date').focus();
                    // }, 100);

                    if (data.cre_data.in_use == true) {
                        jQuery('#cre_sequence').prop({ tabindex: -1, readonly: true });
                        jQuery('#cre_date').prop({ tabindex: -1, readonly: true });
                        jQuery('#rep_customer_id').trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);

                        jQuery('#replace_btn').prop('disabled', true);
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

} else {
    jQuery(document).ready(function () {
        getLatestCRENo();
        addPartDetail();
        getOldCustomer();

        // setTimeout(() => {
        //     // jQuery('#cre_date').focus();
        // }, 100);
    });
}

// check duplication 
jQuery('#cre_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();

    if (subBtn == "submit" || subBtn == "Submit") {
        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }

    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid CRE No.');
            jQuery('#cre_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery('#cre_sequence').focus();
            jQuery('#cre_sequence').val('');

        } else {
            jQuery("#submitBtn").attr('disabled', true);
            jQuery('#cre_sequence').parent().parent().parent('div.control-group').removeClass('error');
            var urL = RouteBasePath + "/check-cre_no_duplication?for=add&cre_sequence=" + val;
            if (creformId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-cre_no_duplication?for=edit&cre_sequence=" + val + "&id=" + creformId;
            }

            jQuery.ajax({
                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#cre_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        setTimeout(() => {
                            jQuery('#cre_sequence').focus();
                        }, 1000);
                        jQuery('#cre_sequence').val('');
                    } else {
                        jQuery('#cre_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#cre_no').val(data.latest_po_no);
                        jQuery('#cre_sequence').val(val);
                    }
                    jQuery("#submitBtn").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#cre_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')
                }
            });
        }
    } else {
        jQuery('#cre_no').val('');
        jQuery('#cre_sequence').val('');
    }
});
// end check duplication


if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        // productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
        // item_id += `data-rate="${getItem[0][indx].id}" `;

        productDrpHtml += `<option value="${getItem[0][indx].id}" data-item_code="${getItem[0][indx].item_code}" data-item_group="${getItem[0][indx].item_group_name}" data-unit="${getItem[0][indx].unit_name}" data-stock_qty="${getItem[0][indx].stock_qty}"
         data-secondary_unit="${getItem[0][indx].secondary_unit}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}

function addPartDetail() {
    var thisHtml = '';

    thisHtml += `<tr style="display:none;">
                    <td><input type="hidden" name="cre_details_id[]" value="0"></td>
        
                </tr>    
                <tr>
                    <td><a onclick="removeDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>
                    <td class="sr_no"></td>
                    <td>
                        <select name="item_id[]" onChange="getItemData(this)" class="chzn-select  add_item item_id">${productDrpHtml}</select></td>
                    <td><input type="hidden" name="item_details_id[]" id="item_details_id" value="" /></td>    
                    <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable" tabindex="-1" readonly/></td>

                    <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable" tabindex="-1" readonly/></td>

                    <td><input type="text" name="return_details_qty[]" id="return_details_qty" onKeyup="calSecondQty(this)" class="form-control  only-numbers  POaddtables" /></td>
                    
                    <td><input type="text" name="return_qty[]" id="return_qty" onblur="formatPoints(this,3)" onKeyup="sumSoQty(this)" class="form-control isNumberKey return_qty POaddtables" /></td>

                    <td><input type="text" name="unit[]" id="unit" class="form-control POaddtables" tabindex="-1" readonly/></td>

                    <td><input type="text" name="remark[]" id="remark" class="form-control  salesmanageTable"  /></td>
                </tr>`;

    jQuery('#creTable tbody').append(thisHtml);
    // srNo();
    setTimeout(() => {
        srNo();
    }, 200);
    sumSoQty();

}


function getItemDetailData(th) {
    var selected = jQuery(th).val();
    var thisselected = jQuery(th);
    var selectedOption = jQuery(th).find('option:selected');
    var second_unit = selectedOption.data('second_unit');
    var secondary_qty = selectedOption.data('secondary_qty');
    jQuery(th).parents('tr').find("#unit").val(second_unit);
    jQuery(th).parents('tr').find("#return_details_qty").val('').attr('readonly', false);
    jQuery(th).parents('tr').find("#return_qty").val('').prop({ tabindex: -1, readonly: true });
    jQuery("#return_total_qty").text('');

}
// edit time details
function FillDetailsTable(filldata) {

    var editHtml = '';
    if (filldata && filldata.length > 0) {

        var counter = 1;
        for (let key in filldata) {

            var sr_no = counter;
            var cre_id = filldata[key].cre_id ? filldata[key].cre_id : null;
            var cre_details_id = filldata[key].cre_detail_id ? filldata[key].cre_detail_id : null;
            var item_id = filldata[key].item_id ? filldata[key].item_id : null;
            var item_details_id = filldata[key].item_details_id ? filldata[key].item_details_id : "";
            var item_code = filldata[key].item_code ? filldata[key].item_code : null;
            var return_qty = filldata[key].return_qty ? filldata[key].return_qty.toFixed(3) : "";
            var return_details_qty = filldata[key].return_details_qty ? filldata[key].return_details_qty : "";
            var unit_name = filldata[key].unit_name ? filldata[key].unit_name : "";
            var item_group = filldata[key].item_group_name ? filldata[key].item_group_name : "";
            var remark = filldata[key].remark ? checkSpecialCharacter(filldata[key].remark) : "";
            var productDetailDrpHtml = ``;

            if (filldata[key].item_detail.length > 0) {
                var productDetailDrpHtml = `<select name="item_details_id[]" id="item_details_id" class="chzn-select  item_id item_details_ids_${sr_no} add_item_details" ${filldata[key].in_use == true ? 'readonly  tabindex="-1"' : ''}  onChange="getItemDetailData(this)"><option value="">Select Item</option>`;
                for (let indx in filldata[key].item_detail) {
                    var sec_unit = filldata[key].item_detail[indx].unit_name ? filldata[key].item_detail[indx].unit_name : "";
                    var secondary_qty = filldata[key].item_detail[indx].secondary_qty ? filldata[key].item_detail[indx].secondary_qty : "";
                    productDetailDrpHtml += `<option value="${filldata[key].item_detail[indx].item_details_id}"data-second_unit="${sec_unit}"data-secondary_qty="${secondary_qty}">${filldata[key].item_detail[indx].secondary_item_name} </option>`;
                }

                productDetailDrpHtml += `</select>`;
            } else {
                productDetailDrpHtml = `<input type="hidden" name="item_details_id[]" value="" />`;
            }

            editHtml += `
              
              <tr style="display:none;">
                <td>
                   
                    <input type="hidden" name="cre_details_id[]" value="${cre_details_id}">
                    <input type="hidden" name="cre_id[]" value="${cre_id}">
                </td>
              
              </tr>    
              <tr>
                <td> 
                    <a ${filldata[key].in_use == false ? 'onclick="removeDetails(this)"' : ''} ><i class="action-icon iconfa-trash so_details "></i></a> 
                </td>
                <td class="sr_no">${sr_no}</td>
        
                <td>
                     <select name="item_id[]"  class="chzn-select  item_id add_item item_id_${sr_no}"  onChange="getItemData(this)"   ${filldata[key].in_use == true ? 'readonly  tabindex="-1"' : ''}>${productDrpHtml}</select>
                </td>
                <td>${productDetailDrpHtml}</td>
                <td>
                    <input type="text" name="code[]" id="code"  class="form-control  salesmanageTable" tabindex="-1" value="${item_code}" readonly  tabindex="-1"/>
                </td>
            
                <td>
                    <input type="text" name="group[]" id="group"  class="form-control  salesmanageTable" tabindex="-1" value="${item_group}" readonly  tabindex="-1"/>
                </td>`;

            if (item_details_id != "") {
                editHtml += `  
                 <td> 
                    <input type="text" name="return_details_qty[]" id="return_details_qty"  class="form-control POaddtables   only-numbers" value="${return_details_qty}"  onKeyup="calSecondQty(this)" min="${filldata[key].used_qty > 0 ?filldata[key].used_qty : ''}"  ${return_details_qty ==filldata[key].used_qty ? 'readonly tabindex="-1"' : ''}/>
                </td>
                <td> 
                        <input type="text" name="return_qty[]" id="return_qty"  class="form-control POaddtables return_qty isNumberKey" value="${return_qty}" onblur="formatPoints(this,3)"  onKeyup="sumSoQty(this)"  min="${filldata[key].used_qty > 0 ? parseFloat(filldata[key].used_qty).toFixed(3) : ''}" readonly  tabindex="-1"/>
                    </td>`;

            } else {
                editHtml += `  <td> 
                    <input type="text" name="return_details_qty[]" id="return_details_qty"  class="form-control POaddtables  only-numbers" value="${return_details_qty}"  onKeyup="calSecondQty(this)" min="${filldata[key].used_qty > 0 ?filldata[key].used_qty : ''}"  readonly tabindex="-1"}/>
                </td>
                 <td> 
                     <input type="text" name="return_qty[]" id="return_qty"  class="form-control POaddtables return_qty isNumberKey" value="${return_qty}" onblur="formatPoints(this,3)"  onKeyup="sumSoQty(this)"  min="${filldata[key].used_qty > 0 ? parseFloat(filldata[key].used_qty).toFixed(3) : ''}"  ${return_qty == parseFloat(filldata[key].used_qty) ? 'readonly tabindex="-1" ' : ''}  />
                    </td>`;

            }
            editHtml += ` <td>
                    <input type="text" name="unit[]" id="unit" class="form-control POaddtables" tabindex="-1" value="${unit_name}" readonly  tabindex="-1"/>
                </td>
            
                <td>
                    <input type="text" name="remark[]" id="remark"  class="form-control salesmanageTable" value="${remark}" />
                </td>
        
            </tr>`;
            counter++;
        }

        jQuery('#creTable tbody').append(editHtml);
        var counter = 1;
        for (let key in filldata) {
            var item_id = filldata[key].item_id ? filldata[key].item_id : "";
            var item_details_id = filldata[key].item_details_id ? filldata[key].item_details_id : "";
            jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
            jQuery(`.item_details_ids_${counter}`).val(item_details_id).trigger('liszt:updated');
            counter++;
        }
        srNo();
        sumSoQty();

    }
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
function getItemData(th) {
    let item = th.value;
    var selected = jQuery(th).val();
    var selectedOption = jQuery(th).find('option:selected');
    var secondaryItem = selectedOption.data('secondary_unit');
    var thisselected = jQuery(th);
    if (selected) {
        if (secondaryItem == 'Yes') {

            jQuery(th).parents('tr').find("#return_qty").val('').prop({ tabindex: -1, readonly: true });
        }
        else {
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
            jQuery(th).parents('tr').find("#return_details_qty").val('').prop({ tabindex: -1, readonly: true });
            jQuery(th).parents('tr').find("#return_qty").val('').attr('readonly', false);
        }
    }

    if (item != "" && item != null) {
        jQuery(th).parents('tr').find("#code").val(jQuery(th).find('option:selected').data('item_code'));
        jQuery(th).parents('tr').find("#item_id").val(item);
        jQuery(th).parents('tr').find("#group").val(jQuery(th).find('option:selected').data('item_group'));
        jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit'));
    }
    if (item != "" && item != null) {
        jQuery.ajax({
            url: RouteBasePath + "/get-fitting_item_data?item=" + item,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    var productDetailDrpHtml = ``;
                    if (data.item_detail.length > 0) {
                        var productDetailDrpHtml = `<select name="item_details_id[]" id="item_details_id"  class="chzn-select  item_id item_details_id add_item_details" onChange="getItemDetailData(this)"><option value="">Select Item</option>`;
                        for (let indx in data.item_detail) {
                            var sec_unit = data.item_detail[indx].unit_name ? data.item_detail[indx].unit_name : "";
                            var secondary_qty = data.item_detail[indx].secondary_qty ? data.item_detail[indx].secondary_qty : "";
                            productDetailDrpHtml += `<option value="${data.item_detail[indx].item_details_id}" data-second_unit="${sec_unit}" data-secondary_qty="${secondary_qty}">${data.item_detail[indx].secondary_item_name} </option>`;
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
                    jQuery(th).parents('tr').find("#return_details_qty").val('');

                    jQuery("#return_total_qty").text('');



                    if (creformId == undefined) {
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
                }
            },
        });
    }


    // var customerGroup = jQuery('#customer_group_id option:selected').val();

    // if (item != "" && item != null) {
    //     jQuery.ajax({

    //         url: RouteBasePath + "/get-item_data?item=" + item + "&id=" + creformId + "&customerGroup=" + customerGroup,
    //         type: 'GET',
    //         headers: headerOpt,
    //         dataType: 'json',
    //         processData: false,
    //         success: function (data) {
    //             if (data.response_code == 1) {
    //                 jQuery(th).closest('tr').find("#code").val(data.item.item_code);
    //                 jQuery(th).closest('tr').find("#item_id").val(data.item.id);
    //                 jQuery(th).closest('tr').find("#group").val(data.item.item_group_name);
    //                 jQuery(th).closest('tr').find("#unit").val(data.item.unit_name);

    //             } else {
    //                 jQuery('#code').val('');
    //                 jQuery('#item_id').val('');
    //                 jQuery('#group').val('');
    //                 jQuery('#unit').val('');
    //             }
    //         }
    //     });
    // }
}
function sumSoQty(th) {
    var total = 0;
    jQuery('.return_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            // total = parseInt(total) + parseInt(total1);
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    total != 0 && total != "" ? jQuery('.cre_qty').text(parseFloat(total).toFixed(3)) : jQuery('.cre_qty').text('');

}



function removeDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            jQuery(th).parents("tr").remove();
            srNo();
            var return_qty = jQuery(th).parents('tr').find('#return_qty').val();
            if (return_qty) {
                var total = jQuery('.cre_qty').text();
                if (total != "") {
                    final_total = parseFloat(total) - parseFloat(return_qty);
                }
                final_total > 0 ? jQuery('.cre_qty').text(parseFloat(final_total).toFixed(3)) : jQuery('.cre_qty').text('');
            }
        }
    });
}

function getLatestCRENo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_cre_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#cre_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#cre_no').val(data.latest_cre_no).prop({ tabindex: -1, readonly: true });
                jQuery('#cre_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
                jQuery('#cre_date').val(currentDate);
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#cre_no').removeClass('file-loader');
            console.log('Failed To Get Latest CRE No.!')
        }
    });
}

function getSoStates(event) {
    let stateIdVal = jQuery('#so_country_id option:selected').val();

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
                    jQuery('#cre_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    //    jQuery('#taluka_state_id').empty().append(dropHtml).trigger('liszt:updated');
                    //    console.log(dropHtml);

                } else {
                    jQuery('#cre_state_id').empty().append("<option value=''>Select State</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getSoDistrict(event) {
    let districtVal = jQuery('#cre_state_id option:selected').val();

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
                    jQuery('#cre_district_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#cre_district_id').empty().append("<option value=''>Select District</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

function getSoTaluka(event) {
    let talukaVal = jQuery('#cre_district_id option:selected').val();
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
                            // dropHtml += `<option value="${data.taluka[idx].id}">${data.taluka[idx].taluka_name}</option>`;
                            dropHtml += `<option value="${data.taluka[idx].id}">${data.taluka[idx].taluka_name}</option>`;
                            // dropHtml += `<option value="${data.taluka[idx].id}">${data.taluka[idx].taluka_name}</option>`;
                        }
                    }
                    jQuery('#cre_taluka_id').empty().append(dropHtml).trigger('liszt:updated');
                } else {
                    jQuery('#cre_taluka_id').empty().append("<option value=''>Select Taluka</option>").trigger('liszt:updated');
                }
            },
        });
    }
}

jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) >= 0.001;
});
jQuery.validator.addMethod("checkCustomer", function (value, element) {
    if (value >= 1) {
        return false;
    } else {
        return true;
    }
}, "Please Select Customer From Search Modal");

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
}, "Please Select Item Detail");

jQuery.validator.addMethod("conditionalRequired", function (value, element, param) {
    var $row = jQuery(element).closest("tr");
    var secUnit = $row.find('select[name="item_id[]"] option:selected').attr('data-secondary_unit');
    var val = jQuery.trim(value);

    // Jo param "Yes" hoy → only required when secondary unit Yes
    if (param === "Yes") {
        if (secUnit === "Yes") {
            return val !== "" && val !== "0";
        }
        return true; // No hoy to valid
    }

    // Jo param "No" hoy → only required when secondary unit No
    if (param === "No") {
        if (secUnit === "No") {
            return val !== "" && val !== "0";
        }
        return true; // Yes hoy to valid
    }

    return true;
}, "Please Enter Return Qty.");

// store or update 
var validator = jQuery("#customerReplacementEntryForm").validate({
    onclick: false,
    onkeyup: false,
    onfocusout: false,
    ignore: [],
    rules: {
        // search_customer_val: {
        //     checkCustomer: true 
        // },
        cre_sequence: {
            required: true
        },
        cre_date: {
            required: true,
            dateFormat: true,
            date_check: true
        },
        rep_customer_id: {
            required: true
        },
        reg_no: {
            required: true
        },
        // cre_village:{
        //     required : true
        // },
        // so_country_id:{
        //     required: true
        // },
        // cre_state_id:{
        //     required: true
        // },
        // cre_district_id:{
        //     required : true
        // },
        // cre_taluka_id:{
        //     required: true
        // },
        // customer_group_id:{
        //     required: true
        // },
        // 'item_id[]': {
        //     required: function (e) {
        //         var value = jQuery(e).val();
        //         if (value == "" || value == null) {
        //             jQuery(e).parent('tr').addClass('error');
        //             return true;
        //         } else {
        //             jQuery(e).removeClass('error');
        //             return false;
        //         }
        //     },
        // },
        'item_id[]': {
            required: true,
        },
        'item_details_id[]': {
            secUnit: true,
        },
        // 'return_qty[]': {
        //     required: function (e) {
        //         if (jQuery(e).val().trim() === "" && jQuery(e).closest('tr').find("#item_id").val() != "") {
        //             jQuery(e).addClass('error');
        //             setTimeout(() => {
        //                 jQuery(e).focus();
        //             }, 1000);
        //             return true;
        //         } else {
        //             jQuery(e).removeClass('error');
        //             return false;
        //         }

        //     },
        //     notOnlyZero: '0.001',
        // },
        'return_qty[]': {
            required: true,
            notOnlyZero: '0.001'
        },
        'return_details_qty[]': {
            conditionalRequired: "Yes",
            notOnlyZero: '0.001'
        }
    },

    messages: {
        search_customer_val: {
            required: "Please Select Customer from Modal"
        },
        cre_sequence: {
            required: "Please Enter CRE No."
        },
        cre_date: {
            required: "Please Enter CRE Date."
        },
        rep_customer_id: {
            required: "Please Select Customer OR Select From Search Modal"
        },
        reg_no: {
            required: "Please Enter Reg. No."
        },
        // cre_village:{
        //     required: "Please Enter Village"
        // },
        // so_country_id:{
        //     required: "Please Select Country"
        // },
        // cre_state_id:{
        //     required: "Please Select State"
        // },
        // cre_district_id:{
        //     required: "Please Select District"
        // },
        // cre_taluka_id:{
        //     required: "Please Select Taluka"
        // },
        // customer_group_id:{
        //     required: "Please Select Customer Group"
        // },
        'item_id[]': {
            required: "Please Select Item"
        },
        'item_details_id[]': {
            required: "Please Select Item Detail"
        },
        'return_qty[]': {
            required: "Please Enter Return Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.001'
        },
        'return_details_qty[]': {
            required: "Please Enter Return Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.001'
        },
    },
    errorPlacement: function (error, element) {
        jAlert(error.text());
        return false;
    },



    submitHandler: function (form) {

        let checkLength = jQuery("#creTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        if (checkLength == 0) {
            jAlert("Please Add Atleast One Item.");
            return false;
        }

        var formUrl = creformId != undefined && creformId != '' ? RouteBasePath + "/update-customer_replacement_entry" : RouteBasePath + "/store-customer_replacement_entry";
        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            data: jQuery('#customerReplacementEntryForm').serialize(),
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (creformId != undefined && creformId != "") {
                        toastSuccess(data.response_message, function (r) {
                            window.location.href = RouteBasePath + "/manage-customer_replacement_entry";
                        });
                    }
                    else if (creformId == undefined || creformId == "") {
                        toastSuccess(data.response_message, redirectFn);
                        function redirectFn() {
                            window.location.reload();
                        }
                    }
                    else {
                        toastError(data.response_message);
                    }
                } else {
                    jAlert(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);
                if (errMessage.errors) {
                    jQuery('#submitBtn').prop('disabled', false);
                    validator.showErrors(errMessage.errors);
                } else if (jqXHR.status == 401) {
                    jQuery('#submitBtn').prop('disabled', false);
                    toastError(jqXHR.statusText);
                } else {
                    jQuery('#submitBtn').prop('disabled', false);
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
});

function getOldCustomer() {
    var selectedCustomerId = null; // Variable to store the selected customer ID
    var searchCustomerData = null; // Variable to store API response

    jQuery.ajax({
        url: RouteBasePath + "/getSearchCustomer",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                var tblHtml = "";
                var dropdownHtml = "";

                if (data.search_customer.length > 0) {
                    searchCustomerData = data.search_customer.length;
                    jQuery("#customerReplacementEntryForm").find("#search_customer_val").val(searchCustomerData);

                    for (let idx in data.search_customer) {

                        // Add customer to dropdown
                        dropdownHtml += `<option value="${data.search_customer[idx].so_id}">`;
                        let displayText = [
                            checkInputNull(data.search_customer[idx].c_name),
                            checkInputNull(data.search_customer[idx].customer_reg_no),
                            checkInputNull(data.search_customer[idx].village_name)
                        ].filter(Boolean).join(" - ");
                        dropdownHtml += `${displayText}</option>`;


                        tblHtml += `<tr>`;
                        tblHtml += `<td><input type="radio" name="cust_id" id="cust_id_${data.search_customer[idx].so_id}" value="${data.search_customer[idx].so_id}" ${selectedCustomerId == data.search_customer[idx].so_id ? 'checked' : ''}/></td>`;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].c_name)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].customer_reg_no)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].village_name)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].customer_pincode)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].taluka_name)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].dis_name)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].state_name)}</td>`;
                        tblHtml += `<td>${checkInputNull(data.search_customer[idx].co_name)}</td>`;

                        tblHtml += `</tr>`;
                    }
                    // Append options to the dropdown
                    setTimeout(() => {
                        jQuery("#customerReplacementEntryForm").find('#rep_customer_id').append(dropdownHtml).trigger('liszt:updated');
                    }, 500)
                    jQuery('#searchCustomerTable tbody').empty().append(tblHtml);

                    table = jQuery('#searchCustomerTable').DataTable({
                        pageLength: 50,
                        paging: true,
                        searching: true,
                        "oLanguage": {
                            "sSearch": "Search :"
                        },
                        // "sScrollY": calcDataTableHeight(),
                    });

                    jQuery('input[name="cust_id"]').on('change', function () {
                        selectedCustomerId = jQuery(this).val();
                        jQuery("#customerReplacementEntryForm").find("#search_customer_val").val(0);
                    });
                } else {
                    jQuery('#replace_btn').prop('disabled', true);
                    jQuery('#customer_name').attr('readonly', false);
                    jQuery('#reg_no').attr('readonly', false);
                    jQuery('#cre_village').attr('readonly', false);
                    jQuery('#cre_pincode').attr('readonly', false);
                    jQuery('#so_country_id').attr('readonly', false);
                    jQuery('#cre_state_id').attr('readonly', false);
                    jQuery('#cre_district_id').attr('readonly', false);
                    jQuery('#cre_taluka_id').attr('readonly', false);
                    jQuery('#customer_group_id').attr('readonly', false);
                    jQuery('#searchCustomerTable tbody').empty().append('<tr><td colspan="10">No customers found.</td></tr>');
                }
            } else {
                toastError(data.response_message);
            }
        },
    });
}


// modal validator
var coaPartValidator = jQuery("#searchCustomer").validate({
    rules: {
        "cust_id[]": {
            required: true
        },
    },
    messages: {
        "cust_id[]": {
            required: "Please Select Customer",
        }
    },

    submitHandler: function (form) {
        var modal = jQuery("#custSearchModal");


        var chkCount = 0;
        var chkArr = [];
        var chkId = [];

        jQuery("#custSearchModal").find("[id^='cust_id_']").each(function () {
            let thisId = jQuery(this).attr('id');
            let splt = thisId.split('cust_id_');
            let intId = splt[1];

            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });
        if (chkCount == 0) {
            toastError('Please Select Customer');
        } else {
            jQuery.ajax({
                url: RouteBasePath + "/get-oldcustomer?so_ids=" + chkArr.join(','),
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {

                        // let thisForm = jQuery("#salesorderform")
                        jQuery("#reg_no").val(data.customer.customer_reg_no);
                        // jQuery("#customer_name").val(data.customer.c_name);
                        jQuery("#rep_customer_name").val(data.customer.c_name);
                        jQuery("#rep_customer_id").val(data.customer.so_id).trigger('liszt:updated');
                        jQuery("#customer_group_id").val(data.customer.customer_group_id).trigger('liszt:updated');
                        //jQuery("#dealer_id").val(data.customer.dealer_id).trigger('liszt:updated');
                        jQuery("#cre_village").val(data.customer.customer_village);
                        jQuery("#cre_pincode").val(data.customer.customer_pincode);
                        jQuery("#so_country_id").val(data.customer.country_id).trigger('liszt:updated');
                        jQuery("#customerReplacementEntryForm").find("#search_customer_val").val(0);

                        if (data.customer.country_id != null) {
                            getSoStates().done(function (resposne) {
                                jQuery('#cre_state_id').val(data.customer.state_id).trigger('liszt:updated');
                                getSoDistrict().done(function (resposne) {
                                    jQuery('#cre_district_id').val(data.customer.dis_id).trigger('liszt:updated');
                                    getSoTaluka().done(function (resposne) {
                                        jQuery("#customerReplacementEntryForm").find('#cre_taluka_id').val(data.customer.customer_taluka).trigger('liszt:updated');
                                    });
                                });
                            });
                        }
                        modal.modal("hide");
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
// end

// get particular customer detail
function getSearchData() {
    var particular_cust_id = jQuery('#rep_customer_id').val();
    jQuery.ajax({
        url: RouteBasePath + "/get-oldcustomer?so_ids=" + particular_cust_id,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {

                jQuery("#reg_no").val(data.customer.customer_reg_no);
                jQuery("#rep_customer_id").val(data.customer.so_id);
                jQuery("#customer_group_id").val(data.customer.customer_group_id).trigger('liszt:updated');
                jQuery("#rep_customer_name").val(data.customer.c_name);
                jQuery("#cre_village").val(data.customer.customer_village);
                jQuery("#cre_pincode").val(data.customer.customer_pincode);
                jQuery("#so_country_id").val(data.customer.country_id).trigger('liszt:updated');
                jQuery("#customerReplacementEntryForm").find("#search_customer_val").val(1);

                if (data.customer.country_id != null) {
                    getSoStates().done(function (resposne) {
                        jQuery('#cre_state_id').val(data.customer.state_id).trigger('liszt:updated');
                        getSoDistrict().done(function (resposne) {
                            jQuery('#cre_district_id').val(data.customer.dis_id).trigger('liszt:updated');
                            getSoTaluka().done(function (resposne) {
                                jQuery("#customerReplacementEntryForm").find('#cre_taluka_id').val(data.customer.customer_taluka).trigger('liszt:updated');
                            });
                        });
                    });
                }
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

function calSecondQty(th) {

    var return_details_qty = jQuery(th).closest('tr').find("#return_details_qty").val();

    var second_qty = jQuery(th).closest('tr').find("#item_details_id option:selected").data('secondary_qty');

    var returnQty = 0;
    if (return_details_qty != "" && second_qty != "") {
        returnQty =return_details_qty * parseFloat(second_qty);
    }

    jQuery(th).parents('tr').find("#return_qty").val(returnQty.toFixed(3));
    sumSoQty();

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
// end get particular customer detail