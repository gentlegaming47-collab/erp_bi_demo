
setTimeout(() => {
    // jQuery('#return_sequence').focus();
    jQuery('#issue_no').focus();
}, 100);
const date = new Date();


let currentDay = String(date.getDate()).padStart(2, '0');

let currentMonth = String(date.getMonth() + 1).padStart(2, "0");

let currentYear = date.getFullYear();

// we will display the date as DD-MM-YYYY 

let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;



var return_data = [];

var formId = jQuery('#commonItmeReturnForm').find('input:hidden[name="id"]').val();


if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        // productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
        // item_id += `data-rate="${getItem[0][indx].id}" `;

        productDrpHtml += `<option value="${getItem[0][indx].id}" data-item_code="${getItem[0][indx].item_code}" data-item_group="${getItem[0][indx].item_group_name}" data-unit="${getItem[0][indx].unit_name}" data-stock_qty="${getItem[0][indx].stock_qty}" data-secondary_unit="${getItem[0][indx].secondary_unit}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}

if (formId !== undefined) { //if form is edit

    jQuery(document).ready(function () {
        jQuery('#show-progress').addClass('loader-progress-whole-page');

        jQuery.ajax({

            url: RouteBasePath + "/get-item_return/" + formId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {

                if (data.response_code == 1) {

                    jQuery('#return_sequence').val(data.itemReturn.return_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery('#return_number').val(data.itemReturn.return_number).prop({ tabindex: -1, readonly: true });
                    if(data.itemReturn.in_use == true)
                    {
                        jQuery('#return_date').val(data.itemReturn.return_date).prop({ tabindex: -1, readonly: true });
                    }else{  
                        jQuery('#return_date').val(data.itemReturn.return_date);
                    }
                    jQuery('#supplier_id').val(data.itemReturn.supplier_id).trigger('liszt:updated');

                    jQuery('#supplier_id').prop({ tabindex: -1 }).attr('readonly', true);

                    jQuery('#issue_no').val(data.itemReturn.issue_no);
                    jQuery('#special_notes').val(data.itemReturn.special_notes);
                    //  jQuery('#supplier_id').prop({ tabindex: -1, readonly: true });
                    // jQuery('#supplier_id').attr('readonly', true);

                    // if (data.iteReturnDetails.length > 0 && !jQuery.isEmptyObject(data.iteReturnDetails)) {
                    //     for (let ind in data.iteReturnDetails) {
                    //         return_data.push(data.iteReturnDetails[ind]);

                    //     }
                    //     fillReturnPartTable();
                    // }

                    setTimeout(() => {
                        // jQuery('#return_date').focus();
                        jQuery('#issue_no').focus();
                    }, 100);
                    fillItemReturnTable(data.iteReturnDetails)

                    disabledDropdownVal();

                    jQuery('#show-progress').removeClass('loader-progress-whole-page');

                    // fillPendingItemIssue();

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
        getLatestItemReturnNo();
        addPartDetail();

        setTimeout(() => {
            // jQuery('#return_date').focus();
            jQuery('#issue_no').focus();
        }, 100);

    });

}





// get the latest number
function getLatestItemReturnNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_return_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#return_number').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#return_date').val(currentDate);
                jQuery('#return_number').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });
                jQuery('#return_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#return_number').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}



function fillPendingItemIssue() {
    var thisModal = jQuery('#pendingItemIssueForm');
    var thisForm = jQuery('#commonItmeReturnForm');

    let suppId = jQuery('#supplier_id option:selected').val();


    if (suppId != "") {


        if (formId == undefined) {

            var Url = RouteBasePath + "/get-issue_list-return?supplier_id=" + suppId;
        } else {
            var Url = RouteBasePath + "/get-issue_list-return?supplier_id=" + suppId + "&id=" + formId;
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

                    thisForm.find('#itemIssueTable tbody input[name="form_indx"]').each(function (indx) {
                        let frmIndx = jQuery(this).val();
                        let jbEorkOrderId = return_data[frmIndx].item_issue_details_id;
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
                    if (data.itemIssueData.length > 0 && !jQuery.isEmptyObject(data.itemIssueData)) {
                        found = 1;
                        for (let idx in data.itemIssueData) {
                            let inUse = isUsed(data.itemIssueData[idx].issue_detail_id);
                            totalEntry++;
                            tblHtml += `<tr>
                                <td><input type="checkbox" name="issue_ids[]" class="simple-check ${inUse ? 'in-use' : ''}" id="issue_ids_${data.itemIssueData[idx].issue_detail_id}" value="${data.itemIssueData[idx].issue_detail_id}" ${inUse ? 'checked' : ''}/></td>

                                <td>${data.itemIssueData[idx].issue_number}</td>

                                <td>${data.itemIssueData[idx].item_name}</td>
                                
                                <td>${data.itemIssueData[idx].item_code}</td>

                                <td>${data.itemIssueData[idx].item_group_name}</td>

                                <td>${data.itemIssueData[idx].issue_qty != null ? parseFloat(data.itemIssueData[idx].issue_qty).toFixed(3) : ''}</td>
                                <td>${data.itemIssueData[idx].pending_issue_qty != null ? parseFloat(data.itemIssueData[idx].pending_issue_qty).toFixed(3) : ''}</td>

                                <td>${data.itemIssueData[idx].unit_name}</td>
                            </tr>
                            `;
                        }

                    } else {
                        tblHtml += `<tr class="centeralign" id="noPendingPo">
                            <td colspan="15">No Pending Item Issue Available</td>
                        </tr>`;
                    }

                    thisForm.find('.toggleModalBtn').prop('disabled', false);
                    thisModal.find('#pendingIssueTable tbody').empty().append(tblHtml);
                    if (found == 1) {

                        if (totalDisb == totalEntry) {
                            jQuery('#addPendingPoModal').prop('disabled', true);
                        } else {
                            jQuery('#addPendingPoModal').prop('disabled', false);
                        }
                        thisForm.find('.toggleModalBtn').prop('disabled', false);

                    } else {
                        // resetPdWoForm();
                        thisForm.find('.toggleModalBtn').prop('disabled', true);
                    }
                    // thisModal.modal('show');

                } else {
                    thisModal.find('#pendingIssueTable tbody').empty().append(tblHtml);
                    thisForm.find('.toggleModalBtn').prop('disabled', true);

                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                thisModal.find('#pendingIssueTable tbody').empty().append(tblHtml);
                thisForm.find('.toggleModalBtn').prop('disabled', true);
                // fillSpiPartData(null);

                // jQuery('#openPendingPoModal').removeClass('btn-loader');
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
        thisModal.find('#pendingIssueTable tbody').empty().append(tblHtml);
        thisForm.find('.toggleModalBtn').prop('disabled', true);
    }
}



jQuery('#pendingItemIssue').on('show.bs.modal', function (e) {
    var usedParts = [];
    var totalDisb = 0;

    jQuery('#itemIssueTable tbody input[name="form_indx"]').each(function (indx) {
        let frmIndx = jQuery(this).val();
        let woId = return_data[frmIndx].item_issue_details_id;
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

    jQuery('#pendingIssueTable tbody tr').each(function (indx) {

        totalEntry++;
        let checkField = jQuery(this).find('input[name="issue_ids[]"]');
        let partId = jQuery(checkField).val();
        let inUse = isUsed(partId);
        // console.log(partId)
        if (inUse) {
            jQuery(checkField).addClass('in-use').prop('checked', true);

        } else {
            jQuery(checkField).removeClass('in-use').prop('checked', false);
        }

    });

    // if (totalDisb == totalEntry) {
    //     jQuery('#pendingItemIssueModal').prop('disabled', true);
    // } else {
    //     jQuery('#pendingItemIssueModal').prop('disabled', false);
    // }
    setTimeout(() => {
        jQuery(this).find('#checkall-issue').focus();
    }, 300);
});




var validator = jQuery("#pendingItemIssueForm").validate({
    rules: {
        "issue_ids[]": {
            required: true
        },
    },
    messages: {
        "issue_ids[]": {
            required: "Please Select Item Issue",
        },

    },
    submitHandler: function (form) {

        var chkCount = 0;
        var chkArr = [];
        var chkId = [];
        jQuery("#pendingItemIssueForm").find("[id^='issue_ids_']").each(function () {
            let thisId = jQuery(this).attr('id');
            let splt = thisId.split('issue_ids_');
            let intId = splt[1];

            if (jQuery(this).is(':checked')) {
                chkArr.push(jQuery(this).val())
                chkId.push(intId);
                chkCount++;
            }
        });

        if (chkCount == 0) {
            // jQuery('#issue_ids_' + chkId[0]).parent('td').addClass('error');
            toastError('Please Select Pending Item Issue');

        } else {
            // jQuery('#issue_ids_' + chkId[0]).parent('td').removeClass('error');

            if (formId == undefined) {

                var url = RouteBasePath + "/get-issue_parts_data-return?issueids=" + chkArr.join(',');
            } else {
                var url = RouteBasePath + "/get-issue_parts_data-return?issueids=" + chkArr.join(',') + "&id=" + formId;
            }


            jQuery.ajax({

                url: url,
                type: 'GET',
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (data.issue_part_data.length > 0 && !jQuery.isEmptyObject(data.issue_part_data)) {
                            for (let ind in data.issue_part_data) {

                                return_data.push(data.issue_part_data[ind]);

                            }
                            fillReturnPartTable(data.issue_part_data);
                        }
                        jQuery("#pendingItemIssue").modal('hide');

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



jQuery('#checkall-issue').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#pendingItemIssue").find("[id^='issue_ids_']:not(.in-use)").prop('checked', true).trigger('change');
    } else {
        jQuery("#pendingItemIssue").find("[id^='issue_ids_']:not(.in-use)").prop('checked', false).trigger('change');
    }
});


function fillReturnPartTable($fillData = null) {

    if ($fillData != null && $fillData.length > 0) {

        jQuery('#itemIssueTable tbody').empty();
        for (let key in $fillData) {
            let formIndx = return_data.indexOf($fillData[key]);

            var sr_no = return_data.indexOf($fillData[key]) + 1;

            var issue_no = $fillData[key].issue_number ? $fillData[key].issue_number : "";
            // var issue_date = $fillData[key].issue_date ? $fillData[key].issue_date : "";
            var item_name = $fillData[key].item_name ? $fillData[key].item_name : "";
            var item_code = $fillData[key].item_code ? $fillData[key].item_code : "";
            var unit_name = $fillData[key].unit_name ? $fillData[key].unit_name : "";
            var item_group_name = $fillData[key].item_group_name ? $fillData[key].item_group_name : "";
            var item_group_name = $fillData[key].item_group_name ? $fillData[key].item_group_name : "";


            var item_id = $fillData[key].id ? $fillData[key].id : "";
            var pending_issue_qty = $fillData[key].pending_issue_qty ? parseFloat($fillData[key].pending_issue_qty).toFixed(3) : "";

            var item_return_details_id = formId == undefined ? 0 : $fillData[key].item_return_details_id != null ? $fillData[key].item_return_details_id : 0;

            var return_qty = $fillData[key].return_qty > 0 ? parseFloat($fillData[key].return_qty).toFixed(3) : 0;

            if (jQuery('#itemIssueTable tbody').find('#noitemPart').length > 0) {
                jQuery('#itemIssueTable tbody').empty();
            }

            var tblHtml = `<tr><td>                
            <a onclick="removeReturnPart(this)"><i class="action-icon iconfa-trash remove-spi_part"></i></a></td>
            <td class="sr_no"></td>`;


            tblHtml += `<td>
                          <input type="hidden" name="form_indx" value="${formIndx}"/>
                        <input type="hidden" name="item_issue_details_id[]" value="${$fillData[key].item_issue_details_id}"/>
           </td>`;

            tblHtml += `<td>
            <input type="hidden" name="item_return_details_id[]" value="${item_return_details_id}">
            <input type='hidden' name='pre_item[]' value="${item_id}"/>
            ${item_name}<input type='hidden' name='item_id[]' value="${item_id}"/></td>`;

            tblHtml += `<td>${item_code}<input type='hidden' name='item_code[]' value="${item_code}"/></td>`;

            tblHtml += `<td>${item_group_name}<input type='hidden' name='group[]' value="${item_group_name}"/></td>`;


            tblHtml += `<td>${pending_issue_qty}<input type='hidden' name='pending_issue_qty[]'value="${pending_issue_qty != null ? parseFloat(pending_issue_qty).toFixed(3) : ''}"/></td>`;

            tblHtml += `<td>
            <input type="hidden" name="org_return_qty[]" value="${return_qty}">
            <input type="text" max="${pending_issue_qty}" name="return_qty[]" id="return_qty" class="form-control isNumberKey return_qty" onfocusout="sumReturnQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${return_qty > 0 ? return_qty : pending_issue_qty}" /></td>`;
            tblHtml += `<td>${unit_name}<input type='hidden' name='unit[]' value="${unit_name}"/></td>`;

            // tblHtml += `<td>${unit_name}<input type='hidden' name='group[]' value="${unit_name}"/></td>`;
            tblHtml += `<td><textarea  name="remark[]" id="remark" rows="4" /></td>`;
            // tblHtml += `<td><input type='text' name='remark[]'/></td>`;
            tblHtml += `</tr>`;

            jQuery('#itemIssueTable tbody').append(tblHtml);

            let issue_drop = `<option value=''>Select Issue No.</option>`;
            if (issue_no != null) {
                issue_drop += `<option value="${issue_no}">${issue_no}</option>`;
                jQuery('#issue_no').empty().append(issue_drop).trigger('liszt:updated');
            } else {
                issue_drop += `<option value=''>Select Issue No.</option>`;

            }
            sumReturnQty();
            srNo();
        }
        // jQuery('#itemIssueTable tbody').append(tblHtml);

    } else {
        if (return_data.length > 0) {
            for (let key in return_data) {
                console.log(return_data)
                let formIndx = return_data.indexOf(return_data[key]);

                var sr_no = return_data.indexOf(return_data[key]) + 1;

                // var issue_no = return_data[key].issue_number ? return_data[key].issue_number : "";
                // var issue_date = return_data[key].issue_date ? return_data[key].issue_date : "";
                var item_name = return_data[key].item_name ? return_data[key].item_name : "";
                var item_code = return_data[key].item_code ? return_data[key].item_code : "";
                var item_id = return_data[key].item_id ? return_data[key].item_id : "";
                var item_group_name = return_data[key].item_group_name ? return_data[key].item_group_name : "";
                var unit_name = return_data[key].unit_name ? return_data[key].unit_name : "";
                var remark = return_data[key].remarks ? checkSpecialCharacter(return_data[key].remarks) : ""; var return_qty = return_data[key].return_qty ? parseFloat(return_data[key].return_qty).toFixed(3) : 0;

                var showPdQty = return_data[key].return_qty !== '' ? parseFloat((eval(return_data[key].return_qty) + eval(return_data[key].pending_issue_qty))).toFixed(3) : parseFloat(return_data[key].pending_issue_qty).toFixed(3);

                if (jQuery('#itemIssueTable tbody').find('#noitemPart').length > 0) {
                    jQuery('#itemIssueTable tbody').empty();
                }

                var tblHtml = `<tr><td>                
                <a onclick="removeReturnPart(this)"><i class="action-icon iconfa-trash remove-spi_part"></i></a></td>
                <td class="sr_no"></td>`;

                tblHtml += `<td><input type='hidden' name='pre_item[]' value="${item_id}"/>
                ${item_name}<input type='hidden' name='item_id[]' value="${item_id}"/>
                <input type="hidden" name="form_indx" value="${formIndx}"/>
                <input type="hidden" name="item_return_details_id[]" value="${return_data[key].item_return_details_id}">
               <input type="hidden" name="item_issue_details_id[]" value="${return_data[key].item_issue_details_id}"/>
   
               </td>`;
                tblHtml += `<td>${item_code}<input type='hidden' name='item_code[]' value="${item_code}"/></td>`;

                tblHtml += `<td>${item_group_name}<input type='hidden' name='group[]' value="${item_group_name}"/></td>`;



                // tblHtml += `<td>${showPdQty}<input type='hidden' name='pending_issue_qty[]' value="${(showPdQty)}"/></td>`;

                tblHtml += `<td>
                <input type="hidden" name="org_return_qty[]" value="${return_qty}">
                <input type="text" max="${showPdQty}" name="return_qty[]" id="return_qty" class="form-control isNumberKey return_qty" onfocusout="sumReturnQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${return_qty}"//></td>`;

                tblHtml += `<td>${unit_name}<input type='hidden' name='unit[]' value="${unit_name}"/></td>`;

                tblHtml += `<td><textarea  name="remark[]" id="remark_${return_data[key].item_return_details_id}" rows="4" value="${remark}"/></td>`;
                // tblHtml += `<td><input type='text' name='remark[]' value="${remark}"/></td>`;
                tblHtml += `</tr>`;


                jQuery('#itemIssueTable tbody').append(tblHtml);

                for (let key in return_data) {

                    var print_po_remarks = return_data[key].remarks ? return_data[key].remarks : "";

                    jQuery(`#remark_${return_data[key].item_return_details_id}`).val(print_po_remarks);

                }

                sumReturnQty();
                srNo();
            }
        }
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


function sumReturnQty(th) {
    var total = 0;
    jQuery('.return_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            // total = parseInt(total) + parseInt(total1);
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    // total != 0 && total != "" ? jQuery('.returnqtysum').text(total) : jQuery('.returnqtysum').text('');
    total != 0 && total != "" ? jQuery('.returnqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.returnqtysum').text('');
}
jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) > 0;
});

// jQuery.validator.addMethod("secUnit", function (value, element) {
//     var $row = jQuery(element).closest("tr");
//     var $item = $row.find('select[name="item_id[]"] option:selected');
//     var secUnit = $item.data('secondary_unit');
//         // console.log(secUnit);
//         // return;
//     if (secUnit == "No") {
//         return true;
//     }else{
//          return false;
//     }


//     // return this.optional(element);
// }, "Please Select Item Detail.");
// custom method
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

// initialize validator (example)



var validator = jQuery("#commonItmeReturnForm").validate({
    onclick: false,
    rules: {
        return_sequence: {
            required: true
        },

        supplier_id: {
            required: true
        },
        return_date: {
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
        'return_qty[]': {
            required: true,
            notOnlyZero: '0.001',
        },
        // "return_qty[]": {
        //     required: function (e) {
        //         if (jQuery("#commonItmeReturnForm").find('input[name="return_qty[]"]').val() == "") {
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

        return_sequence: {
            required: "Please Enter Return Number"
        },
        supplier_id: {
            required: "Please Select Supplier"
        },
        issue_date: {
            required: "Please Enter Date."
        },
        'item_id[]': {
            required: "Please Select Item"
        },
        'item_details_id[]': {
            required: "Please Select Item Detail"
        },
        "return_qty[]": {
            required: "Please Enter Return Qty.",
            notOnlyZero: "Please Enter A Value Greater Than 0."
        },



    },


    submitHandler: function (form) {


        let checkLength = jQuery("#itemIssueTable tbody tr").filter(function () {
            return jQuery(this).css('display') !== 'none';
        }).length;

        if (checkLength < 1) {
            jAlert("Please Add At Least One Item Return Slip Detail.");
            addPartDetail();
            return false;
        }

        // let checkLength = jQuery("#itemIssueTable tbody tr").length;
        // if (checkLength <= 1) {
        //     jAlert("Please Select One Pending Item");
        //     return false;
        // }


        // jQuery('#item_retutn_button').prop('disabled', true);
        var formdata = jQuery('#commonItmeReturnForm').serialize();


        var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-item_return" : RouteBasePath + "/store-item_return";
        jQuery.ajax({
            url: formUrl,
            type: 'POST',
            // data: jQuery('#commonItmeReturnForm').serialize(),
            data: formdata,
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (formId != null && formId != "") {
                        function nextFn() {

                            window.location.href = RouteBasePath + "/manage-item_return";
                        }

                        toastSuccess(data.response_message, nextFn);
                    } else {
                        function nextFn() {

                            window.location.reload();
                        }

                        toastSuccess(data.response_message, nextFn);
                    }
                } else {
                    jQuery("#item_retutn_button").attr('disabled', false);
                    toastError(data.response_message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errMessage = JSON.parse(jqXHR.responseText);

                if (errMessage.errors) {
                    jQuery("#item_retutn_button").attr('disabled', false);
                    validator.showErrors(errMessage.errors);
                } else if (jqXHR.status == 401) {
                    jQuery("#item_retutn_button").attr('disabled', false);
                    toastError(jqXHR.statusText);
                } else {
                    jQuery("#item_retutn_button").attr('disabled', false);
                    toastError('Something went wrong!');
                    console.log(JSON.parse(jqXHR.responseText));
                }
            }
        });
    }
});




function removeItemReturnDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {



        if (r === true) {
            jQuery(th).parents("tr").remove();
            srNo();
            var return_qty = jQuery(th).parents('tr').find('#return_qty').val();

            if (return_qty) {
                var item_total = jQuery('.returnqtysum').text();

                if (item_total != "") {
                    item_final_total = parseFloat(item_total) - parseFloat(return_qty);
                }
                item_final_total > 0 ? jQuery('.returnqtysum').text(parseFloat(item_final_total).toFixed(3)) : jQuery('.returnqtysum').text('');


            }
        }


    });
}





jQuery('#return_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();

    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }

    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid Sr No.');
            jQuery('#return_sequence').parent().parent().parent('div.control-group').addClass('error');

            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    // jQuery('#return_sequence').focus();
                    jQuery('#issue_no').focus();
                }, 1000);
            });
            jQuery('#return_sequence').val('');

        } else {


            jQuery("#item_retutn_button").attr('disabled', true);

            jQuery('#return_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-item_return_no_duplication?for=add&return_sequence=" + val;

            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-item_retutn_no_duplication?for=edit&return_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#return_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        jQuery('#return_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                jQuery('#issue_no').focus();
                                // jQuery('#return_sequence').focus();
                            }, 1000);
                        });
                        jQuery('#return_sequence').val('');
                    } else {
                        jQuery('#return_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#return_number').val(data.latest_po_no);
                        jQuery('#return_sequence').val(val);
                    }
                    jQuery("#item_retutn_button").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#return_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#return_number').val('');
        jQuery('#return_sequence').val('');
    }
});

// add time 
function addPartDetail() {


    var thisHtml = `       
    <tr>

    <td>
        <a onclick="removeItemReturnDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
    </td>


    <td class="sr_no"></td>

    <td> <select name="item_id[]" class="chzn-select  item_id add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select></td>

    <td><input type="hidden" name="item_details_id[]" value="" /></td>    
    <td>
    <input type="hidden" name="item_issue_details_id[]" value="0"><input type="hidden" name="item_return_details_id[]" value="0">
    <input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

    <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

    <td>
      <input type="hidden" name="pre_item[]" id="pre_item" value="0"> 
    <input type="text" name="return_qty[]" id="return_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey  return_qty"  onblur="formatPoints(this,3)" style="width:60px;"  tabindex="-1" readonly/></td>
   
    <td><input type="text" name="unit[]" id="unit"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

    
    <td><textarea  name="remark[]" id="remark" rows="4" /></td>

  
    </tr>`;
    jQuery('#itemIssueTable tbody').append(thisHtml);


    // srNo();

    setTimeout(() => {
        srNo();
    }, 200);
    sumSoQty();

    // <td><input type="text" name="remark[]" id="remark"  class="form-control salesmanageTable potableremarks" tabindex="-1" readonly/></td>

    // Reinitialize date-picker for new elements
    jQuery('.date-picker').datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
    });

}
function sumSoQty(th) {
    var total = 0;
    jQuery('.return_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            //  total = parseInt(total) + parseInt(total1);
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    total != 0 && total != "" ? jQuery('.returnqtysum').text(parseFloat(total).toFixed(3)) : jQuery('.returnqtysum').text('');

}
function getItemData(th) {
    let item = th.value;

    var selected = jQuery(th).val();
    var selectedOption = jQuery(th).find('option:selected');
    var secondaryItem = selectedOption.data('secondary_unit');

    var thisselected = jQuery(th);
    if (selected) {
        jQuery(th).parents('tr').find("#return_qty").val('');
        sumSoQty();
        var $returnQty = jQuery(th).parents('tr').find("#return_qty");
        if (secondaryItem == 'Yes') {
            $returnQty.removeAttr("onblur");                
            $returnQty.removeClass("isNumberKey").addClass("only-numbers"); 
        } else {
            $returnQty.attr("onblur", "formatPoints(this,3)");
            $returnQty.removeClass("only-numbers").addClass("isNumberKey");
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

        jQuery(th).parents('tr').find("#code").val(jQuery(th).find('option:selected').data('item_code'));
        jQuery(th).parents('tr').find("#item_id").val(item);
        jQuery(th).parents('tr').find("#group").val(jQuery(th).find('option:selected').data('item_group'));
        jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit'));

        jQuery(th).parents('tr').find("#return_qty").prop('readonly', false);
        jQuery(th).parents('tr').find("#remark").prop('readonly', false);

        jQuery(th).parents('tr').find("#return_qty").prop('tabindex', 0);
        jQuery(th).parents('tr').find("#remark").prop('tabindex', 0);

        if (formId == undefined) {
            jQuery(th).parents('tr').find("#pre_item").val(item);
        } else {
            if (jQuery(th).parents('tr').find("#pre_item").val() == 0) {
                jQuery(th).parents('tr').find("#pre_item").val(item);
            }
        }

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
                    // if (data.stock_qty != null) {

                    //     var minQty = isNaN(Number(data.stock_qty.stock_qty)) ? 0 : Number(data.stock_qty.stock_qty);
                    // } else {
                    //     var minQty = 0;
                    // }
                    var productDetailDrpHtml = ``;
                    if (data.item_detail.length > 0) {
                        var productDetailDrpHtml = `<select name="item_details_id[]" class="chzn-select  item_id item_details_id add_item_details" onChange="getItemDetailData(this)"><option value="">Select Item</option>`;
                        for (let indx in data.item_detail) {
                            productDetailDrpHtml += `<option value="${data.item_detail[indx].item_details_id}" data-second_unit="${data.item_detail[indx].unit_name}">${data.item_detail[indx].secondary_item_name} </option>`;
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
                    //  jQuery(th).parents('tr').find("#return_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");
                    // jQuery(th).parents('tr').find("#return_qty").attr('max', minQty);
                    jQuery(th).parents('tr').find("#return_qty").prop('readonly', false);
                    jQuery(th).parents('tr').find("#remark").prop('readonly', false);

                    jQuery(th).parents('tr').find("#return_qty").prop('tabindex', 0);
                    jQuery(th).parents('tr').find("#remark").prop('tabindex', 0);


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
                    jQuery('#return_qty').val('');
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





// edit time 
function fillItemReturnTable($fillData) {



    if ($fillData.length > 0) {
        var tblHtml = '';

        for (let key in $fillData) {


            // var sr_no = return_data.indexOf($fillData[key]) + 1;
            var sr_no = parseInt(key) + 1;

            var issue_no = $fillData[key].issue_number ? $fillData[key].issue_number : "";
            // var issue_date = $fillData[key].issue_date ? $fillData[key].issue_date : "";
            var item_name = $fillData[key].item_name ? $fillData[key].item_name : "";
            var item_code = $fillData[key].item_code ? $fillData[key].item_code : "";
            var unit_name = $fillData[key].unit_name ? $fillData[key].unit_name : "";
            var item_group_name = $fillData[key].item_group_name ? $fillData[key].item_group_name : "";
            var item_group_name = $fillData[key].item_group_name ? $fillData[key].item_group_name : "";


            var item_id = $fillData[key].item_id ? $fillData[key].item_id : "";

            var item_details_id = $fillData[key].item_details_id ? $fillData[key].item_details_id : "";
            var return_qty = $fillData[key].return_qty > 0 ? item_details_id !="" ? $fillData[key].return_qty :  parseFloat($fillData[key].return_qty).toFixed(3) : 0;

            var item_return_details_id = formId == undefined ? 0 : $fillData[key].item_return_details_id != null ? $fillData[key].item_return_details_id : 0;

            var remark = $fillData[key].remarks ? $fillData[key].remarks : "";

            var in_use = $fillData[key].in_use ? $fillData[key].in_use : "";

            var productDetailDrpHtml = ``;
            if ($fillData[key].item_detail.length > 0) {

                var productDetailDrpHtml = `<select name="item_details_id[]" class="chzn-select  item_id item_details_ids_${sr_no} add_item_details" onChange="getItemDetailData(this)"${in_use == true ? 'readonly' : '' }><option value="">Select Item</option>`;
                for (let indx in $fillData[key].item_detail) {
                    productDetailDrpHtml += `<option value="${$fillData[key].item_detail[indx].item_details_id}" data-second_stock_qty="${$fillData[key].item_detail[indx].second_stock_qty}"data-second_unit="${$fillData[key].item_detail[indx].unit_name}">${$fillData[key].item_detail[indx].secondary_item_name} </option>`;
                }

                productDetailDrpHtml += `</select>`;
            } else {
                productDetailDrpHtml = `<input type="hidden" name="item_details_id[]" value="" />`;
            }

            tblHtml += `<tr><td>                
            <a ${in_use == true ? '' : onclick="removeReturnPart(this)" } ><i class="action-icon iconfa-trash remove-spi_part"></i></a></td>
            <td class="sr_no"></td>`;



            tblHtml += `
         
    <td> <select name="item_id[]" class="chzn-select  item_id add_item item_id_${sr_no}"  onChange="getItemData(this)" ${in_use == true ? 'readonly' : '' }>${productDrpHtml}</select></td>
        <td>${productDetailDrpHtml}</td>
    <td>
    <input type="hidden" name="item_return_details_id[]" value="${item_return_details_id}">
    <input type='text' name='item_code[]' id='code' class="form-control salesmanageTable POaddtables"  value="${item_code}" tabindex="-1" readonly/></td>

    <td><input type='text' name='group[]'  id='group' class="form-control salesmanageTable POaddtables"  value="${item_group_name}" tabindex="-1" readonly/></td>`;

    if(item_details_id != "")
    {
        tblHtml += `<td>
        <input type='hidden' name='pre_item[]' value="${item_id}"/>
        <input type="hidden" name="pre_item_detail[]" id="pre_item_detail" value="${item_details_id != null && item_details_id != '' ? item_details_id : ''}">
        <input type="hidden" name="org_return_qty[]" value="${return_qty}">
        <input type="text" name="return_qty[]" id="return_qty" class="form-control only-numbers return_qty" onfocusout="sumReturnQty(this)"  style="width:60px;" value="${return_qty}" ${in_use == true ? 'readonly' : '' }/></td>`;
    }else{
        tblHtml += `<td>
        <input type='hidden' name='pre_item[]' value="${item_id}"/>
        <input type="hidden" name="pre_item_detail[]" id="pre_item_detail" value="${item_details_id != null && item_details_id != '' ? item_details_id : ''}">
        <input type="hidden" name="org_return_qty[]" value="${return_qty}">
        <input type="text" name="return_qty[]" id="return_qty" class="form-control isNumberKey return_qty" onfocusout="sumReturnQty(this)" onblur="formatPoints(this,3)" style="width:60px;" value="${return_qty}" ${in_use == true ? 'readonly' : '' }/></td>`;

    }
   
    tblHtml += ` <td><input type='text' name='unit[]' id="unit" class="form-control salesmanageTable POaddtables"  value="${unit_name}" tabindex="-1" readonly/></td>


    
   <td><textarea  name="remark[]" id="remark_${$fillData[key].item_return_details_id}" rows="4" value="${remark}"/></td>

  
    </tr>`;


        }

        jQuery('#itemIssueTable tbody').append(tblHtml);
        setTimeout(() => {
            var counter = 1;

            for (let key in $fillData) {
                var item_id = $fillData[key].item_id ? $fillData[key].item_id : ""
                var item_details_id = $fillData[key].item_details_id ? $fillData[key].item_details_id : "";

                jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
                jQuery(`.item_details_ids_${counter}`).val(item_details_id).trigger('liszt:updated').change();
                var print_po_remarks = $fillData[key].remarks ? $fillData[key].remarks : "";
                jQuery(`#remark_${$fillData[key].item_return_details_id}`).val(print_po_remarks);

                counter++;
            }
        }, 100);
    }
    sumReturnQty();
    srNo();
    disabledDropdownVal();
}



function removeReturnPart(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {


        if (r === true) {
            jQuery(th).parents("tr").remove();
            srNo();
            var return_qty = jQuery(th).parents('tr').find('#return_qty').val();

            if (return_qty) {

                var return_total = jQuery('.returnqtysum').text();


                if (return_total != "") {
                    return_final_total = parseFloat(return_total) - parseFloat(return_qty);
                }

                return_final_total > 0 ? jQuery('.returnqtysum').text(parseFloat(return_final_total).toFixed(3)) : jQuery('.returnqtysum').text('');

            }


        }



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