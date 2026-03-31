
const date = new Date();



let currentDay = String(date.getDate()).padStart(2, '0');

let currentMonth = String(date.getMonth() + 1).padStart(2, "0");

let currentYear = date.getFullYear();

let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;


let materialArray = [];
var removeMaterialDetailsId = [];

var table;
var approvalDataTable;


jQuery('#approval_date').val(currentDate);
let page = jQuery("#pagename").val();
jQuery('#commonApprovalRequestForm').find('#approvalButton').prop('disabled', true);
jQuery('#commonApprovalRequestForm').find('#addPart').prop('disabled', true);

jQuery(document).ready(function () {

    jQuery.ajax({

        url: RouteBasePath + "/listing-approval_request?pageName=" + page,

        type: 'POST',

        headers: headerOpt,

        dataType: 'json',

        processData: false,

        success: function (data) {

            if (data.response_code == 1) {

                jQuery("#approved_by").val(data.approved_by.user_name)

                if (data.getMaterial.length > 0 && !jQuery.isEmptyObject(data.getMaterial)) {
                    for (let ind in data.getMaterial) {
                        materialArray.push(data.getMaterial[ind]);
                    }

                    fillMaterialDemo(data.getMaterial);
                }
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

    approvalDataTable = jQuery('#approvalDataTable').DataTable({
        responsive: true,
        // "scrollX": true,
        pageLength: 100,
        paging: true,
        "scrollX": true,
        "sScrollX": "100%",
        "sScrollXInner": "110%",
        "bScrollCollapse": true,
    });

});


function fillMaterialDemo(getMaterial) {

    if (getMaterial.length > 0) {

        var tblHtml = ``;
        let sr_no = 0;

        for (let idx in getMaterial) {

            ++sr_no;
            tblHtml += `<tr>

            <td><input type="radio" name="mr_id[]" id="mr_id${getMaterial[idx].mr_id}" value="${getMaterial[idx].mr_id}" onChange="getMaterialData(this)"/>            
            </td>`;

            // if (getMaterial[idx].getType == "zsm") {
            //     tblHtml += `<td>${getMaterial[idx].sm_user_id}</td>`;
            //     tblHtml += `<td>${getMaterial[idx].sm_approvaldate}</td>`;
            // }
            // else if (getMaterial[idx].getType == "md") {
            //     tblHtml += `<td>${getMaterial[idx].zsm_user_id}</td>
            //     <td>${getMaterial[idx].zsm_approvaldate}</td>
            //     <td>${getMaterial[idx].sm_user_id}</td>
            //     <td>${getMaterial[idx].sm_approvaldate}</td>                
            //     `;
            // }

            tblHtml += `<td>${getMaterial[idx].location_name}</td>
            <td>${getMaterial[idx].mr_number}</td>
            <td>${getMaterial[idx].mr_date}</td>   
            <td>${getMaterial[idx].to_location}</td>`;

            if (getMaterial[idx].getType == "state_coordinator") {
                tblHtml += `<td>${getMaterial[idx].sm_user_id}</td>`;
                tblHtml += `<td>${getMaterial[idx].sm_approvaldate}</td>`;
                tblHtml += `<td>${getMaterial[idx].zsm_user_id}</td>`;
                tblHtml += `<td>${getMaterial[idx].zsm_approvaldate}</td>`;
            }
            else if (getMaterial[idx].getType == "zsm") {
                tblHtml += `<td>${getMaterial[idx].sm_user_id}</td>`;
                tblHtml += `<td>${getMaterial[idx].sm_approvaldate}</td>`;
                // tblHtml += `<td>${getMaterial[idx].state_coordinator_user_id}</td>`;
                // tblHtml += `<td>${getMaterial[idx].state_coordinator_approvaldate}</td>`;
            }
            else if (getMaterial[idx].getType == "md") {
                tblHtml += `<td>${getMaterial[idx].sm_user_id}</td>
                <td>${getMaterial[idx].sm_approvaldate}</td>
                <td>${getMaterial[idx].state_coordinator_user_id}</td>
                <td>${getMaterial[idx].state_coordinator_approvaldate}</td>
                <td>${getMaterial[idx].zsm_user_id}</td>
                <td>${getMaterial[idx].zsm_approvaldate}</td>
                `;
            }

            tblHtml += `<td>${getMaterial[idx].special_notes != null ? getMaterial[idx].special_notes : ''}</td>`;

            tblHtml += `</tr>`;
            // tblHtml += `<td>${getMaterial[idx].location_name}</td>
            // <td>${getMaterial[idx].mr_number}</td>
            // <td>${getMaterial[idx].mr_date}</td>   
            // <td>${getMaterial[idx].to_location}</td>         
            // <td>${getMaterial[idx].item_name}</td>
            // <td>${getMaterial[idx].item_code}</td>
            // <td>${parseFloat(getMaterial[idx].mr_qty).toFixed(3)}</td>
            // <td>${getMaterial[idx].unit_name}</td>
            // <td>${getMaterial[idx].remarks}</td>

            // </tr>`;

        }
    } else {

        tblHtml += `<tbody><tr class="centeralign" id="approvalTable">

            <td colspan="11">No Material Data Available</td>

        </tr></tbody>`;

    }

    jQuery('#approvalTable tbody').empty().append(tblHtml);

    table = jQuery('#approvalTable').DataTable({
        responsive: true,
        // "scrollX": true,
        pageLength: 100,
        paging: true,
        "scrollX": true,
        "sScrollX": "100%",
        "sScrollXInner": "110%",
        "bScrollCollapse": true,

    });



}



jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) >= 0.001;
});

// Store or Update

var validator = jQuery("#commonApprovalRequestForm").validate({
    onclick: false,
    // onkeyup: false,
    onfocusout: false,

    // rules: {
    //     'mr_id[]': {
    //         approvalDetail: true
    //     },
    // },

    rules: {
        'mr_id[]': {
            required: true,
        },
        // 'form_type[]': {
        //     required: true,
        // },
        // 'new_form_type[]': {
        //     required: true,
        // },
        'new_item_id[]': {
            required: true,
        },
        'new_mr_qty[]': {
            required: function (e) {
                if (jQuery("#commonApprovalRequestForm").find('select[name="item_id[]"]').val() != "") {
                    return true;
                } else {
                    return false;
                }
            },
            // required: true,
            notOnlyZero: '0.001',
        },
    },

    messages: {

        'mr_id[]': {
            required: "Please Select Approval Detail."
        },
        // 'form_type[]': {
        //     required: "Please Select Form Type"
        // },
        // 'new_form_type[]': {
        //     required: "Please Select Form Type"
        // },
        'new_item_id[]': {
            required: "Please Select Item"
        },
        'new_mr_qty[]': {
            required: "Please Enter MR. Qty.",
            notOnlyZero: 'Please Enter MR. Qty. Greater Than 0.'
        },

    },

    // messages: {

    //     // 'mr_id[]': {
    //     //     approvalDetail: "Please Select Approval Details"
    //     // },

    // },
    errorPlacement: function (error, element) {
        jAlert(error.text());
        return false;
    },

    submitHandler: function (form) {

        var errorEncountered = true;

        // table.$('tr').each(function (e) {
        //     console.log(e)

        //     var mrId = jQuery(this).find('input[name="mr_id[]"]');

        //     if (jQuery(mrId).is(':checked')) {
        //         errorEncountered = false;
        //     }

        // });

        jQuery('#approvalTable tbody tr').each(function (e) {
            var mrId = jQuery(this).find('input[name="mr_id[]"]');

            if (jQuery(mrId).is(':checked')) {
                errorEncountered = false;
            }
        });

        if (errorEncountered) {
            toastError("Please Select Approval Details");
            return false;
        }


        if (jQuery('#approvalDataTable  tbody').find('.dataTables_empty').length == '1') {
            toastError("lease Select at least one Approval Details.");
            return false;
        }



        if (!jQuery.isEmptyObject(materialArray)) {

            jQuery('#approvalButton').prop('disabled', true);
            jQuery('#addPart').prop('disabled', true);

            var formdata = jQuery('#commonApprovalRequestForm').serialize();

            removeMaterialDetailsId.forEach(function (item) {
                formdata += '&remove_mr_details_id[]=' + encodeURIComponent(item);
            });


            let formUrl = RouteBasePath + "/store-approval";


            jQuery.ajax({

                url: formUrl,

                type: 'POST',

                data: formdata,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if (data.response_code == 1) {

                        jAlert(data.response_message, 'Alert Dialog', function (r) {
                            window.location.reload();
                        });

                        // toastPreview(data.response_message, redirectFn, prePO);
                        // function redirectFn() {
                        //     window.location.reload();
                        // }
                        // function prePO() {
                        //     id = btoa(data.id);
                        //     window.location.reload();
                        // }
                        jQuery('#approvalButton').prop('disabled', false);
                        jQuery('#addPart').prop('disabled', false);


                    } else {
                        jQuery('#approvalButton').prop('disabled', false);
                        jQuery('#addPart').prop('disabled', false);

                        toastError(data.response_message);
                    }




                },

                error: function (jqXHR, textStatus, errorThrown) {

                    var errMessage = JSON.parse(jqXHR.responseText);



                    if (errMessage.errors) {
                        jQuery('#approvalButton').prop('disabled', false);
                        jQuery('#addPart').prop('disabled', false);

                        validator.showErrors(errMessage.errors);



                    } else if (jqXHR.status == 401) {
                        jQuery('#approvalButton').prop('disabled', false);
                        jQuery('#addPart').prop('disabled', false);

                        jAlert(jqXHR.statusText);


                        // toastError(jqXHR.statusText);

                    } else {

                        jQuery('#approvalButton').prop('disabled', false);
                        jQuery('#addPart').prop('disabled', false);

                        jAlert('Something went wrong!');

                        // toastError('Something went wrong!');

                        console.log(JSON.parse(jqXHR.responseText));

                    }

                }

            });
        } else {
            toastError("Please Select at least one Approval Details");
        }

    }

});




jQuery('#checkall-sm').click(function () {

    if (jQuery(this).is(':checked')) {
        jQuery("#approvalTable").find("[id^='material_detail_ids_']:not(.in-use)").prop('checked', true).trigger('change');
        jQuery("#approvalTable").find("[id^='material_detail_ids_']").prop('checked', true).trigger('change');
    } else {
        jQuery("#approvalTable").find("[id^='material_detail_ids_']:not(.in-use)").prop('checked', false).trigger('change');
        jQuery("#approvalTable").find("[id^='material_detail_ids_']").prop('checked', false).trigger('change');
    }
});



function getMaterialData(id) {
    jQuery('#commonApprovalRequestForm').find('#approvalButton').prop('disabled', true);
    jQuery('#commonApprovalRequestForm').find('#addPart').prop('disabled', true);

    var mr_id = id.value;
    if (mr_id != '') {
        jQuery.ajax({
            url: RouteBasePath + "/get-material_details?mr_id=" + mr_id,
            type: 'POST',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    var tblHtml = ``;
                    // console.log(data.mrDetailData);

                    if (data.mrDetailData.length > 0 && !jQuery.isEmptyObject(data.mrDetailData)) {
                        for (let idx in data.mrDetailData) {
                            if (page == 'add-state_coordinator_approval') {
                                tblHtml += `<tr>
                                <td><a onclick="removeMrDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                                <input type="hidden" name="mr_detail_id[]" id="mr_detail_id${data.mrDetailData[idx].mr_details_id}" value="${data.mrDetailData[idx].mr_details_id}"/>    
                                
                                </td>
                                <td>
                                    <select name="form_type[]" class="chzn-select form-type" ${data.mrDetailData[idx].secondary_unit == "Yes" ? 'readonly' : ''}>
                                        <option value="SO" ${data.mrDetailData[idx].form_type == 'SO' ? 'selected' : ''}>SO</option>
                                        <option value="PO" ${data.mrDetailData[idx].form_type == 'PO' ? 'selected' : ''}>PO</option>
                                    </select>
                                </td>
                                <td><input type="hidden" name="hidden_item_id[]" id="hidden_item_id" class="hitem_id" value="${data.mrDetailData[idx].item_id}">${data.mrDetailData[idx].item_name}</td>
                                <td>${data.mrDetailData[idx].item_code}</td>
                                <td><input type="text" name="mr_qty[]" style="width:100px;" value="${parseFloat(data.mrDetailData[idx].mr_qty).toFixed(3)}" onblur="formatPoints(this,3)"></td>
                                <td>${data.mrDetailData[idx].unit_name}</td>
                                <td>${parseFloat(data.mrDetailData[idx].stock_qty).toFixed(3)}</td>
                                <td>${data.mrDetailData[idx].remarks}</td>
                                </tr>`;
                            }
                            else if (page == 'add-gm_approval') {
                                tblHtml += `<tr>
                                <td><a onclick="removeMrDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                                <input type="hidden" name="mr_detail_id[]" id="mr_detail_id${data.mrDetailData[idx].mr_details_id}" value="${data.mrDetailData[idx].mr_details_id}"/>    
                                
                                </td>
                                <td>${data.mrDetailData[idx].form_type}</td>
                                <td><input type="hidden" name="hidden_item_id[]" id="hidden_item_id" class="hitem_id" value="${data.mrDetailData[idx].item_id}">${data.mrDetailData[idx].item_name}</td>
                                <td>${data.mrDetailData[idx].item_code}</td>
                                <td><input type="text" name="mr_qty[]" style="width:50%;" value="${parseFloat(data.mrDetailData[idx].mr_qty).toFixed(3)}" onblur="formatPoints(this,3)"></td>
                                <td>${data.mrDetailData[idx].unit_name}</td>
                                <td>${parseFloat(data.mrDetailData[idx].stock_qty).toFixed(3)}</td>
                                <td>${data.mrDetailData[idx].remarks}</td>
                                </tr>`;
                            }
                            else {
                                tblHtml += `<tr>
                                <td><a onclick="removeMrDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
                                <input type="hidden" name="mr_detail_id[]" id="mr_detail_id${data.mrDetailData[idx].mr_details_id}" value="${data.mrDetailData[idx].mr_details_id}"/>    
                                
                                </td>
                                <td><input type="hidden" name="hidden_item_id[]" id="hidden_item_id" class="hitem_id" value="${data.mrDetailData[idx].item_id}">${data.mrDetailData[idx].item_name}</td>
                                <td>${data.mrDetailData[idx].item_code}</td>
                                <td><input type="text" name="mr_qty[]" style="width:50%;" value="${parseFloat(data.mrDetailData[idx].mr_qty).toFixed(3)}" onblur="formatPoints(this,3)"></td>
                                <td>${data.mrDetailData[idx].unit_name}</td>
                                <td>${parseFloat(data.mrDetailData[idx].stock_qty).toFixed(3)}</td>
                                <td>${data.mrDetailData[idx].remarks}</td>
                                </tr>`;
                            }
                        }


                        if (jQuery.fn.DataTable.isDataTable('#approvalDataTable')) {
                            jQuery('#approvalDataTable').DataTable().destroy();
                        }
                        jQuery('#approvalDataTable tbody').empty().append(tblHtml);

                        approvalDataTable = jQuery('#approvalDataTable').DataTable({
                            responsive: true,
                            // "scrollX": true,
                            pageLength: 100,
                            paging: true,

                        });

                        jQuery('#commonApprovalRequestForm').find('#approvalButton').prop('disabled', false);
                        jQuery('#commonApprovalRequestForm').find('#addPart').prop('disabled', false);
                        if (page == 'add-state_coordinator_approval' || page == 'add-gm_approval') {
                            jQuery('.form-type').chosen();
                        }

                    } else {
                        window.location.reload();
                    }
                }
            },
        });

    }
}



function removeMrDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            var mr_detail_id = jQuery(th).closest('tr').find('input[name="mr_detail_id[]"]').val();

            if (mr_detail_id != "") {
                removeMaterialDetailsId.push(mr_detail_id);

                // jQuery(th).parents("tr").remove();

                var table = jQuery('#approvalDataTable').DataTable();
                var row = table.row(jQuery(th).parents('tr'));
                row.remove().draw();


                // jQuery.ajax({
                //     url: RouteBasePath + "/remove_mr_detail?mr_detail_id=" + mr_detail_id,
                //     type: 'GET',
                //     dataType: 'json',
                //     processData: false,
                //     success: function (data) {
                //         if (data.response_code == 1) {
                //             jQuery('#approvalTable tbody tr').each(function (e) {
                //                 var mrId = jQuery(this).find('input[name="mr_id[]"]')[0];
                //                 if (jQuery(mrId).is(':checked')) {
                //                     getMaterialData(mrId)
                //                 }
                //             });
                //         }

                //     },
                // });
            }
        }
    });
}


if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        // productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
        // item_id += `data-rate="${getItem[0][indx].id}" `;

        productDrpHtml += `<option value="${getItem[0][indx].id}" data-item_code="${getItem[0][indx].item_code}" data-item_group="${getItem[0][indx].item_group_name}" data-unit="${getItem[0][indx].unit_name}" data-stock_qty="${getItem[0][indx].stock_qty}"data-secondary_unit="${getItem[0][indx].secondary_unit}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}


// console.log(page);

function addMaterialDetail() {
    if (page == 'add-state_coordinator_approval') {
        var tblHtml = `
        <tr>
        <td>
            <a onclick="removeMaterialDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
        </td>
        <td>
            <select name="new_form_type[]" class="chzn-select form-type">
                <option value="SO">SO</option>
                <option value="PO">PO</option>
            </select>
        </td>

        <td><select name="new_item_id[]"  class="chzn-select item_id add_item item_id hitem_id mr_item_select_width" onChange="getItemData(this)">${productDrpHtml}</select></td>

        <td><input type="hidden" name="new_mr_details_id[]" value="0">
        <input type="text" name="code[]" id="code"  class="form-control POaddtables" tabindex="-1" readonly/></td>

        <td><input type="text" name="new_mr_qty[]" onblur="formatPoints(this,3)" id="new_mr_qty" class="form-control isNumberKey mr_qty" tabindex="-1" style="width:100px;" readonly/></td>

        <td><input type="text" name="unit[]" id="unit" class="form-control" tabindex="-1" style="width:50px;"readonly/></td>

        <td><input type="text" name="stock_qty[]" id="stock_qty" class="form-control allow-desimal stock_qty" style="width:80%;" tabindex="-1" readonly/></td>
        
        <td><input type="text" name="new_remarks[]" id="new_remarks" tabindex="-1"   class="form-control" readonly/></td>

        </tr>`;
    }
    else {
        var tblHtml = `
        <tr>
        <td>
            <a onclick="removeMaterialDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
        </td>

        <td><select name="new_item_id[]"  class="chzn-select item_id add_item item_id hitem_id mr_item_select_width" onChange="getItemData(this)">${productDrpHtml}</select></td>

        <td><input type="hidden" name="new_mr_details_id[]" value="0">
        <input type="text" name="code[]" id="code"  class="form-control POaddtables" tabindex="-1" readonly/></td>

        <td><input type="text" name="new_mr_qty[]" onblur="formatPoints(this,3)" id="new_mr_qty" class="form-control isNumberKey mr_qty" tabindex="-1" style="width:50%;" readonly/></td>

        <td><input type="text" name="unit[]" id="unit" class="form-control" tabindex="-1" style="width:50px;"readonly/></td>

        <td><input type="text" name="stock_qty[]" id="stock_qty" class="form-control allow-desimal stock_qty" style="width:80%;" tabindex="-1" readonly/></td>
        
        <td><input type="text" name="new_remarks[]" id="new_remarks" tabindex="-1"   class="form-control" readonly/></td>

        </tr>`;
    }

    if (jQuery.fn.DataTable.isDataTable('#approvalDataTable')) {
        jQuery('#approvalDataTable').DataTable().destroy();
    }
    jQuery('#approvalDataTable tbody').append(tblHtml);

    approvalDataTable = jQuery('#approvalDataTable').DataTable({
        responsive: true,
        // "scrollX": true,
        pageLength: 100,
        paging: true,
        ordering: false // prevent default sorting

    });

    jQuery(".item_id").chosen({
        search_contains: true
    });

    if (page == 'add-state_coordinator_approval' || page == 'add-gm_approval') {
        jQuery('.form-type').chosen();
    }
    // jQuery('#materialRequestTable tbody').append(thisHtml);


}


function getItemData(th) {
    let item = th.value;

    var selected = jQuery(th).val();
    var thisselected = jQuery(th);
    if (selected) {
        jQuery(jQuery('.hitem_id').not(jQuery(th))).each(function (index) {
            if (thisselected.val() == jQuery(this).val()) {
                jAlert('This Item Is Already Selected.');
                var selectTd = thisselected.closest('td');

                selectTd.html(`<select name="new_item_id[]" class="chzn-select add_item item_id hitem_id mr_item_select_width" onChange="getItemData(this), sumSoQty(this)">${productDrpHtml}</select>`);
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
        jQuery(th).parents('tr').find("#item_id").val(item);
        jQuery(th).parents('tr').find("#group").val(jQuery(th).find('option:selected').data('item_group'));
        jQuery(th).parents('tr').find("#unit").val(jQuery(th).find('option:selected').data('unit'));
        jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");
        jQuery(th).parents('tr').find("#new_mr_qty").prop('readonly', false);
        jQuery(th).parents('tr').find("#new_mr_qty").prop('tabindex', 0);
        jQuery(th).parents('tr').find("#new_remarks").prop('readonly', false);
        jQuery(th).parents('tr').find("#new_remarks").prop('tabindex', 0);


        if (jQuery(th).find('option:selected').data('secondary_unit') == "Yes") {
            jQuery(th).parents('tr').find('select[name="new_form_type[]"]').val("SO").trigger('liszt:updated').prop({ tabindex: -1 }).attr('readonly', true);
        } else {
            jQuery(th).parents('tr').find('select[name="new_form_type[]"]').trigger('liszt:updated').prop({ tabindex: 1 }).attr('readonly', false);
        }
    }





}


function removeMaterialDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        if (r === true) {
            // jQuery(th).parents("tr").remove();
            var table = jQuery('#approvalDataTable').DataTable();
            var row = table.row(jQuery(th).parents('tr'));
            row.remove().draw();
        }
    });
}
