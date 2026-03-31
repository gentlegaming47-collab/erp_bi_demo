
// setTimeout(() => {
//     jQuery('#po_short_date').focus();
// }, 100);
let poShortCloseIdId = jQuery('#commonPOShortClose').find('input:hidden[name="id"]').val();


const date = new Date();

let currentDay = String(date.getDate()).padStart(2, '0');

let currentMonth = String(date.getMonth() + 1).padStart(2, "0");

let currentYear = date.getFullYear();

// we will display the date as DD-MM-YYYY 

let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

let po_data = [];

jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };


    jQuery('#po_short_date').val(currentDate);



    jQuery.ajax({
        url: RouteBasePath + "/get-po",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                if (data.po_data.length > 0 && !jQuery.isEmptyObject(data.po_data)) {
                    for (let ind in data.po_data) {
                        po_data.push(data.po_data[ind]);
                    }
                    fillPOTable();
                }
            }
        }
    });

    // }

    jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
        return this.optional(element) || parseFloat(value) > 0;
        // return this.optional(element) || parseFloat(value) >= parseFloat(param);
    });

    // Store or Update

    var validator = jQuery("#commonPOShortClose").validate({



        rules: {
            onkeyup: false,
            onfocusout: false,

            po_short_date: {



                required: true,
                date_check: true,
                dateFormat: true,


            },


            "po_detail_id[]": {

                required: true

            },



            "so_po_qty[]": {

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
                notOnlyZero: '0.001',

            },


            "po_reason[]": {

                required: function (e) {

                    if (jQuery(e).prop('disabled')) {

                        return false;

                    } else {

                        return true;

                    }

                },

            },
        },

        messages: {

            po_short_date: {



                required: 'Please Enter Po Short Close Date',



            },


            "po_detail_id[]": {

                required: "Please Select At Least One PO No."

            },



            "so_po_qty[]": {

                required: "Please Enter Short Close Qty.",
                notOnlyZero: 'Please Enter A Value Greater Than 0.',

            },

            "po_reason[]": {

                required: "Please Enter Reason"

            },

        },

        submitHandler: function (form) {



            po_data = [];

            var index = 0;

            // main table loop 

            jQuery('#pendingPOTable tbody tr').each(function (e) {


                var POId = jQuery(this).find('input[name="po_detail_id[]"]');



                if (jQuery(POId).is(':checked')) {





                    POId = jQuery(POId).val();


                    id = jQuery(this).find('input[name="id[]"]').val();

                    poQty = jQuery(this).find('input[name="so_po_qty[]"]').val();

                    poReason = jQuery(this).find('textarea[name="po_reason[]"]').val();


                    // assign to object 


                    po_data[index] = { 'po_detail_id': POId, 'so_po_qty': poQty, 'po_reason': poReason };

                    index++;



                }

            });



            if (!jQuery.isEmptyObject(po_data)) {



                let data = new FormData(document.getElementById('commonPOShortClose'));


                let formValue = Object.fromEntries(data.entries());



                if (poShortCloseIdId !== undefined) { //Edit Form

                    formValue.id = poShortCloseId;

                }


                // remove the object key and value  after use 

                delete formValue["id[]"];



                delete formValue["po_detail_id[]"];

                delete formValue["po_qty[]"];

                delete formValue["po_reason[]"];

                delete formValue["company_id[]"];





                formValue = Object.assign(formValue, { 'po_short_details': JSON.stringify(po_data) });





                var formdata = new URLSearchParams(formValue).toString();


                // var formdata = jQuery('#commonPOShortClose').serialize();

                if (!jQuery.isEmptyObject(po_data)) {


                    let formUrl = RouteBasePath + "/store-po_short_close";




                    jQuery.ajax({

                        url: formUrl,

                        type: 'POST',

                        data: formdata,

                        headers: headerOpt,

                        dataType: 'json',

                        processData: false,

                        success: function (data) {

                            if (data.response_code == 1) {


                                if (poShortCloseIdId != undefined && poShortCloseIdId != "") {

                                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                                        window.location.href = RouteBasePath + "/manage-po_short_close";
                                    });
                                    //addedVillage(true);
                                }
                                else if (poShortCloseIdId == undefined || poShortCloseIdId == "") {



                                    toastSuccess(data.response_message, redirectFn);

                                    function redirectFn() {
                                        window.location.reload();
                                        // window.location.href = RouteBasePath + "/manage-po_short_close";

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


            }
        }



    });

});





//   UPDATE or ADD Form Data

function fillPOTable() {


    if (po_data.length > 0) {
        var tblHtml = ``;
        let sr_no = 0;

        for (let idx in po_data) {




            ++sr_no;
            tblHtml += `<tr>

            <td><input type="checkbox" name="po_detail_id[]" id="po_detail_ids_${po_data[idx].POId}" value="${po_data[idx].POId}" onchange="manageQtyfield(this)" ${idx == 0 ? "autofocus" :""}/>            
            </td>     
            <td>${po_data[idx].po_number}</td>
            <td>${po_data[idx].po_date}</td>            
            <td>${po_data[idx].supplier_name}</td>                        
            <td>${po_data[idx].location_name}</td>
            <td>${po_data[idx].item_name}</td>
            <td>${po_data[idx].item_code}</td>
            <td>${po_data[idx].item_group_name}</td>
            <td>${po_data[idx].po_qty != null ? parseFloat(po_data[idx].po_qty).toFixed(3) : ""}</td>
            <td>${po_data[idx].pend_po_qty != null ? parseFloat(po_data[idx].pend_po_qty).toFixed(3) : ""}</td>            
            <td>${po_data[idx].unit_name}</td>            
            <td>${po_data[idx].del_date}</td>            
            <td><input type="text" max="${po_data[idx].pend_po_qty}" name="so_po_qty[]" id="so_po_qty_${po_data[idx].po_id}" onblur="formatPoints(this,3)" class="input-mini isNumberKey" disabled/></td>
            <td><textarea  name="po_reason[]" id="po_reason_${po_data[idx].po_id}" rows="4" disabled/></td>
            </tr>`;

        }


        jQuery('#pendingPOTable tbody').empty().append(tblHtml);



        if (poShortCloseIdId !== undefined) { //Edit Form



            if (po_data.length > 0 && !jQuery.isEmptyObject(po_data)) {

                for (let ind in po_data) {

                    var selected = jQuery('#pendingPOTable tbody tr').find('#po_detail_ids_' + po_data[ind].supplier_po_detail_id);


                    jQuery(selected).attr('checked', true).addClass('in-use');



                    jQuery(selected).parent('td').find('input[name="id[]"]').val(po_data[ind].id);



                    jQuery(selected).parent('td').parent('tr').find('input[name="so_po_qty[]"]').val(po_data[ind].qty).prop('disabled', false);

                    jQuery(selected).parent('td').parent('tr').find('textarea[name="po_reason[]"]').val(checkSpecialCharacter(po_data[ind].reason)).prop('disabled', false);


                }

            }



        } else {
            // var calcDataTableHeight = function () {
            //     return jQuery(window).height() * 55 / 100;
            // };

            table = jQuery('#pendingPOTable').DataTable({
                pageLength: 50,
                paging: true,
                searching: true,
                "oLanguage": {
                    "sSearch": "Search :"
                },
                // "sScrollY": calcDataTableHeight(),
                "scrollX":true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                initComplete: function () {
                // Exclude first column (index 0) from search
                    initColumnSearch('#pendingPOTable', [0,12,13]);
                }   
            });

            


            // jQuery(window).resize(function () {
            //     var oSettings = table.fnSettings();
            //     oSettings.oScroll.sY = calcDataTableHeight();
            //     table.fnDraw();
            // });
        }

    } else {
        tblHtml += `<tr class="centeralign" id="noPendingDc">

            <td colspan="11">No Pending PO Details Available</td>

        </tr>`;

    }



}





jQuery('#checkall-po').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#pendingPOTable").find("[id^='po_detail_ids_']:not(.in-use)").prop('checked', true).trigger('change');
        jQuery("#pendingPOTable").find("[id^='po_detail_ids_']").prop('checked', true).trigger('change');
        jQuery("#pendingPOTable").find("[id^='so_po_qty_']").prop('disabled', false);
        jQuery("#pendingPOTable").find("[id^='po_reason_']").prop('disabled', false);
    } else {
        jQuery("#pendingPOTable").find("[id^='po_detail_ids_']:not(.in-use)").prop('checked', false).trigger('change');
        jQuery("#pendingPOTable").find("[id^='po_detail_ids_']").prop('checked', false).trigger('change');
        jQuery("#pendingPOTable").find("[id^='so_po_qty_']").prop('disabled', true);
        jQuery("#pendingPOTable").find("[id^='po_reason_']").prop('disabled', true);
    }

});



function manageQtyfield($this) {
    var oaQtyField = jQuery($this).parent('td').parent('tr').find('input[name="so_po_qty[]"]');
    var oaReasonField = jQuery($this).parent('td').parent('tr').find('textarea[name="po_reason[]"]');



    if (jQuery(oaQtyField).prop('disabled') && jQuery(oaReasonField).prop('disabled')) {
        jQuery(oaQtyField).prop('disabled', false);
        jQuery(oaReasonField).prop('disabled', false);

    } else {
        jQuery(oaQtyField).val('').trigger('change').prop('disabled', true);
        jQuery(oaReasonField).val('').trigger('change').prop('disabled', true);

    }

}