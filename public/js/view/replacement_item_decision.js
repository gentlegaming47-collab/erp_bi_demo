var rid_details = [];

var formId = jQuery('#commonReplacementItemDecisionForm').find('input:hidden[name="id"]').val();


jQuery(document).ready(function () {

    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };


    if (formId != "" && formId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({
            url: RouteBasePath + "/get-replacement_item_decision/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    jQuery('input:radio[name="replacement_fix_id"][value="' + data.replacement_data.replacement_type_id_fix + '"]').attr('checked', true).trigger('click');

                    jQuery("input[name*='replacement_fix_id']").prop({ tabindex: -1, readonly: true });

                    if (data.replacement_details.length > 0 && !jQuery.isEmptyObject(data.replacement_details)) {
                        for (let ind in data.replacement_details) {
                            rid_details.push(data.replacement_details[ind]);
                        }
                        fillReplacementItem(data.replacement_details);
                    }
                    jQuery('#show-progress').removeClass('loader-progress-whole-page');
                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = RouteBasePath + "/manage-replacement_item_decision";
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

    } else {
        jQuery.ajax({
            url: RouteBasePath + "/get-so_mapping_data",
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    if (data.so_mapping.length > 0 && !jQuery.isEmptyObject(data.so_mapping)) {
                        for (let ind in data.so_mapping) {
                            rid_details.push(data.so_mapping[ind]);
                        }
                        fillReplacementItem(data.so_mapping);
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
    }
});


function fillReplacementItem(so_mapping) {
    if (so_mapping.length > 0) {
        var tblHtml = '';
        var sr_no = 0;
        for (let idx in so_mapping) {

            var so_mapping_details_id = so_mapping[idx].so_mapping_details_id ? so_mapping[idx].so_mapping_details_id : '';
            var so_mapping_number = so_mapping[idx].so_mapping_number ? so_mapping[idx].so_mapping_number : '';
            var mapping_date = so_mapping[idx].mapping_date ? so_mapping[idx].mapping_date : '';
            var customer = so_mapping[idx].customer_name ? so_mapping[idx].customer_name : "";
            var item_name = so_mapping[idx].item_name ? so_mapping[idx].item_name : "";
            var item_details_name = so_mapping[idx].secondary_item_name ? so_mapping[idx].secondary_item_name : "";
            var item_details_id = so_mapping[idx].item_details_id ? so_mapping[idx].item_details_id : "";
            var secondary_qty = so_mapping[idx].secondary_qty ? so_mapping[idx].secondary_qty : "";

            var item_code = so_mapping[idx].item_code ? so_mapping[idx].item_code : "";
            var item_group = so_mapping[idx].item_group_name ? so_mapping[idx].item_group_name : "";
            if (formId == undefined) {
                var pend_so_map_qty = so_mapping[idx].pend_so_map_qty ? so_mapping[idx].pend_so_map_qty : "";
                var pend_so_map_details_qty = so_mapping[idx].pend_so_map_details_qty ? so_mapping[idx].pend_so_map_details_qty : "";

                var replacement_details_id = 0;
            } else {
                var pend_so_map_qty = parseFloat(so_mapping[idx].show_pend_qty) + parseFloat(so_mapping[idx].decision_qty);
                var pend_so_map_details_qty = parseFloat(so_mapping[idx].show_pend_detail_qty) + parseFloat(so_mapping[idx].decision_detail_qty);

                var replacement_details_id = so_mapping[idx].replacement_details_id ? so_mapping[idx].replacement_details_id : 0;
            }

            var unit_name = so_mapping[idx].unit_name ? so_mapping[idx].unit_name : "";
            var item_id = so_mapping[idx].item_id ? so_mapping[idx].item_id : "";

            ++sr_no;


            tblHtml +=
                `<tr>
                  <td><input type="checkbox" ${idx == '0' ? 'autofocus' : ''} name="so_mapping_details_id[]" id="so_mapping_details_ids_${so_mapping_details_id}" value="${so_mapping_details_id}" onchange="manageQtyfield(this)" ${formId != undefined ? 'checked' : ''}/> 
                  <input type="hidden" name="replacement_details_id[]" id="replacement_details_id" value=${replacement_details_id}>        
                  <input type="hidden" name="item_details_id[]" id="item_details_id" value=${item_details_id}>        
                  <input type="hidden" name="secondary_qty[]" id="secondary_qty" value=${secondary_qty}>        
                  </td> 
                  <td>${so_mapping_number}</td>
                  <td>${mapping_date}</td>
                  <td>${customer}</td>
                  <td><input type="hidden" name="item_id[]" id="item_id_${so_mapping_details_id}" value="${item_id}"/>  ${item_name}</td>
                  <td>${item_details_name}</td>
                  <td>${item_code}</td>            
                  <td>${item_group}</td>
                  <td>${parseFloat(pend_so_map_qty).toFixed(3)}</td>`;
            if (formId == undefined) {
                tblHtml += `<td>
                <input type="hidden" name="org_decision_detail_qty[]" id="org_decision_detail_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" value="0"/>                 
                <input type="text" max="${pend_so_map_details_qty}" name="decision_detail_qty[]" id="decision_detail_qty_${so_mapping_details_id}"  class="input-mini only-numbers return_qty"  onkeyup="calSecondQty(this)" disabled/></td>`;
                if (item_details_id != "") {
                    tblHtml += `<td>
                     <input type="hidden" name="org_decision_qty[]" id="org_decision_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" value="0"/>                 
                    <input type="text"  name="decision_qty[]" id="decision_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" onblur="formatPoints(this,3)" disabled/></td>`;
                } else {

                    tblHtml += `<td>
                     <input type="hidden" name="org_decision_qty[]" id="org_decision_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" value="0"/>                 
                    <input type="text" max="${parseFloat(pend_so_map_qty).toFixed(3)}" name="decision_qty[]" id="decision_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" onblur="formatPoints(this,3)" disabled/></td>`;
                }
            } else {

                tblHtml += `<td>
                <input type="hidden" name="org_decision_detail_qty[]" id="org_decision_detail_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" value="${parseFloat(so_mapping[idx].decision_detail_qty).toFixed(3)}"/>   

                <input type="text" max="${pend_so_map_details_qty}" name="decision_detail_qty[]" id="decision_detail_qty_${so_mapping_details_id}" value="${so_mapping[idx].decision_detail_qty != null ?so_mapping[idx].decision_detail_qty : ''}"   class="input-mini  only-numbers return_qty" onkeyup="calSecondQty(this)" ${item_details_id != '' ? '' : 'disabled'}/></td>`;
                if (item_details_id != "") {
                    tblHtml += `<td>
                <input type="hidden" name="org_decision_qty[]" id="org_decision_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" value="${parseFloat(so_mapping[idx].decision_qty).toFixed(3)}"/>                
                <input type="text" name="decision_qty[]" id="decision_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" onblur="formatPoints(this,3)" value="${parseFloat(so_mapping[idx].decision_qty).toFixed(3)}" ${item_details_id != '' ? 'readonly' : ''}/></td>`;
                } else {
                    tblHtml += `<td>
                <input type="hidden" name="org_decision_qty[]" id="org_decision_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" value="${parseFloat(so_mapping[idx].decision_qty).toFixed(3)}"/>                
                <input type="text" max="${parseFloat(pend_so_map_qty).toFixed(3)}" name="decision_qty[]" id="decision_qty_${so_mapping_details_id}"  class="input-mini isNumberKey return_qty" onblur="formatPoints(this,3)" value="${parseFloat(so_mapping[idx].decision_qty).toFixed(3)}" ${item_details_id != '' ? 'readonly' : ''}/></td>`;
                }
            }
            tblHtml += `<td>${unit_name}</td></tr>`;
        }
    } else {
        tblHtml += `<tbody><tr class="centeralign" id="replacementItemTable">
            <td colspan="10">Customer Replacement SO Mapping Details Not Available</td>
        </tr></tbody>`;
    }
    jQuery('#replacementItemTable tbody').empty().append(tblHtml);
    table = jQuery('#replacementItemTable').DataTable({
        responsive: true,
        // "scrollX": true,
        pageLength: 10,
        paging: true,
        "scrollX": true,
        "sScrollX": "100%",
        "sScrollXInner": "110%",
        "bScrollCollapse": true,
    });
}


jQuery('#checkall-so_mapping').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#replacementItemTable").find("[id^='so_mapping_details_ids_']").prop('checked', true).trigger('change');
        // jQuery("#replacementItemTable").find("[id^='decision_qty_']").prop('disabled', false);
    } else {
        jQuery("#replacementItemTable").find("[id^='so_mapping_details_ids_']").prop('checked', false).trigger('change');
        // jQuery("#replacementItemTable").find("[id^='decision_qty_']").val('').prop('disabled', true);

    }
});



function manageQtyfield($this) {
    var mapQtyField = jQuery($this).parent('td').parent('tr').find('input[name="decision_qty[]"]');
    var mapDetailQtyField = jQuery($this).parent('td').parent('tr').find('input[name="decision_detail_qty[]"]');
    var item_details_id = jQuery($this).parent('td').parent('tr').find('input[name="item_details_id[]"]').val();
    var is_checked = jQuery($this).parent('td').parent('tr').find("[id^='so_mapping_details_ids_']").prop('checked');

    if (item_details_id != '') {
        // if (jQuery(mapDetailQtyField).prop('disabled')) {
        if (is_checked) {
            jQuery(mapDetailQtyField).prop('disabled', false);
        } else {
            jQuery(mapQtyField).val('').trigger('change').prop('disabled', true);
            jQuery(mapDetailQtyField).val('').trigger('change').prop('disabled', true);

        }

    } else {
        // if (jQuery(mapQtyField).prop('disabled')) {
        if (is_checked) {
            jQuery(mapQtyField).prop('disabled', false);
        } else {
            jQuery(mapQtyField).val('').trigger('change').prop('disabled', true);

        }
    }



}


jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseFloat(value) >= 0.001;
});

var validator = jQuery("#commonReplacementItemDecisionForm").validate({
    onkeyup: false,
    onfocusout: false,
    rules: {

        "so_mapping_details_id[]": {
            required: true
        },
        "decision_qty[]": {
            required: function (e) {
                if (jQuery(e).prop('disabled')) {
                    return false;
                } else {
                    setTimeout(() => {
                        jQuery(e).focus();
                    }, 1000);
                    return true;
                }
            },
            notOnlyZero: '0.001'
        },
        "decision_detail_qty[]": {
            required: function (e) {
                if (jQuery(e).prop('disabled')) {
                    return false;
                } else {
                    setTimeout(() => {
                        jQuery(e).focus();
                    }, 1000);
                    return true;
                }
            },
            notOnlyZero: '0.001'
        },
    },

    messages: {

        "so_mapping_details_id[]": {
            required: "Please Select At Least One Sr. No."
        },
        "decision_qty[]": {
            required: "Please Enter Decision Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.001'
        },
        "decision_detail_qty[]": {
            required: "Please Enter Item Detail Qty.",
            notOnlyZero: 'Please Enter A Value Greater Than 0.001'
        },

    },

    submitHandler: function (form) {

        so_mapping_data = [];
        var index = 0;

        // main table loop 

        jQuery('#replacementItemTable tbody tr').each(function (e) {
            var so_mapping_details_id = jQuery(this).find('input[name="so_mapping_details_id[]"]');
            if (jQuery(so_mapping_details_id).is(':checked')) {
                so_mapping_details_id = jQuery(so_mapping_details_id).val();
                decision_qty = jQuery(this).find('input[name="decision_qty[]"]').val();
                item_id = jQuery(this).find('input[name="item_id[]"]').val();
                replacement_details_id = jQuery(this).find('input[name="replacement_details_id[]"]').val();
                org_decision_qty = jQuery(this).find('input[name="org_decision_qty[]"]').val();
                org_decision_detail_qty = jQuery(this).find('input[name="org_decision_detail_qty[]"]').val();
                item_details_id = jQuery(this).find('input[name="item_details_id[]"]').val();
                decision_detail_qty = jQuery(this).find('input[name="decision_detail_qty[]"]').val();

                so_mapping_data[index] = { 'so_mapping_details_id': so_mapping_details_id, 'decision_qty': decision_qty, 'item_id': item_id, 'replacement_details_id': replacement_details_id, 'org_decision_qty': org_decision_qty, 'org_decision_detail_qty': org_decision_detail_qty, 'item_details_id': item_details_id, 'decision_detail_qty': decision_detail_qty };
                index++;
            }

        });

        if (!jQuery.isEmptyObject(so_mapping_data)) {
            let data = new FormData(document.getElementById('commonReplacementItemDecisionForm'));
            let formValue = Object.fromEntries(data.entries());
            delete formValue["so_mapping_details_id[]"];
            delete formValue["decision_qty[]"];
            formValue = Object.assign(formValue, { 'so_mapping_details': JSON.stringify(so_mapping_data) });
            var formdata = new URLSearchParams(formValue).toString();

            if (!jQuery.isEmptyObject(so_mapping_data)) {

                var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-replacement_item_decision" : RouteBasePath + "/store-replacement_item_decision";

                jQuery.ajax({
                    url: formUrl,
                    type: 'POST',
                    data: formdata,
                    headers: headerOpt,
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            if (formId != undefined && formId != "") {
                                toastSuccess(data.response_message, function (r) {
                                    window.location.href = RouteBasePath + "/manage-replacement_item_decision";
                                });
                            }
                            else {
                                toastSuccess(data.response_message, redirectFn);
                                function redirectFn() {
                                    window.location.reload();
                                }
                            }
                        } else {
                            jAlert(data.response_message);

                        }

                    },

                    error: function (jqXHR, textStatus, errorThrown) {
                        var errMessage = JSON.parse(jqXHR.responseText);
                        if (errMessage.errors) {
                            validator.showErrors(errMessage.errors);
                        } else if (jqXHR.status == 401) {
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
        } else {
            toastError("Please Select At Least One Sr. No.");
        }
    }
});



function calSecondQty(th) {

    var decision_detail_qty = jQuery(th).closest('tr').find("input[name='decision_detail_qty[]']").val();
    var secondary_qty = jQuery(th).closest('tr').find("input[name='secondary_qty[]']").val();

    var decQty = 0;
    if (decision_detail_qty != "" && secondary_qty != "") {
        decQty = parseFloat(decision_detail_qty) * parseFloat(secondary_qty);
    }

    jQuery(th).parents('tr').find("input[name='decision_qty[]']").val(decQty.toFixed(3));

}