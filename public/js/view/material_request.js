
let materialHiddenId = jQuery('#commonMaterialRequestForm').find('input:hidden[name="id"]').val();
var po_data = [];
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

var productDrpHtml = `<option value="">Select Item</option>`;
// if (getItem.length) {
//     var productDrpHtml = `<option value="">Select Item</option>`;
//     var item_id = ``;
//     // for (let indx in getItem[0]) {
//     //     // productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
//     //     // item_id += `data-rate="${getItem[0][indx].id}" `;

//     //     productDrpHtml += `<option value="${getItem[0][indx].id}" data-item_code="${getItem[0][indx].item_code}" data-item_group="${getItem[0][indx].item_group_name}" data-unit="${getItem[0][indx].unit_name}" data-stock_qty="${getItem[0][indx].stock_qty}">${getItem[0][indx].item_name} </option>`;
//     //     item_id += `data-rate="${getItem[0][indx].id}" `;
//     // }
// }

jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

    //  edit code

    if (materialHiddenId != null && materialHiddenId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page');
        jQuery.ajax({

            url: RouteBasePath + "/get-material_request/" + materialHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {

                    // setTimeout(() => {
                    //     jQuery('#mr_sequence').focus();
                    // }, 100);


                    jQuery("#to_location_id").val(data.materialRequest.to_location_id).trigger('liszt:updated');

                    jQuery("#mr_sequence").val(data.materialRequest.mr_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery("#mr_number").val(data.materialRequest.mr_number).prop({ tabindex: -1, readonly: true });
                    jQuery("#mr_date").val(data.materialRequest.mr_date)

                    jQuery("#special_notes").val(data.materialRequest.special_notes)

                    loadSalesOrderData(data);

                    setTimeout(() => {
                        // jQuery('#so_sequence').focus();               
                        // jQuery("#mr_date").focus();
                        jQuery("#to_location_id").trigger('liszt:activate');
                    }, 800);

                    if (data.materialRequest.in_use == true) {
                        jQuery("#mr_sequence").prop({ tabindex: -1, readonly: true });
                        jQuery("#mr_date").prop({ tabindex: -1, readonly: true });
                        jQuery("#to_location_id").prop({ tabindex: -1 }).attr('readonly', true);
                        jQuery('#addPart').prop('disabled', true);
                        jQuery("#special_notes").prop({ tabindex: -1, readonly: true });
                        jQuery("#customer_group_id").prop({ tabindex: -1 }).attr('readonly', true);
                    }

                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-material_request";
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
            // jQuery('#mr_date').focus();

            // jQuery("#mr_date").datepicker();

            // jQuery("#mr_date").on("click", function () {
            //     console.log('fdggf')
            //     // jQuery(this).datepicker("show");
            // });
            // }, 100);

            // jQuery("#mr_date").focus();
            setTimeout(() => {
                jQuery("#to_location_id").trigger('liszt:activate');
            }, 800);

            getLatestMaterialNo();
            addMaterialDetail();
            getItemRateFromPriceList();

        });
    }

    async function loadSalesOrderData(data) {
        try {

            jQuery("#customer_group_id").val(data.materialRequest.customer_group_id).trigger('liszt:updated');

            jQuery("#customer_group_id").prop({ tabindex: -1 }).attr('readonly', true);

            await getItemRateFromPriceList()

            fillmaterialRequestTable(data.materialRequestDetails)

            jQuery('#show-progress').removeClass('loader-progress-whole-page');


        } catch (error) {
            console.log("Error: ", error);
        }
    }


    jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
        return this.optional(element) || parseFloat(value) >= 0.001;
    });

    // Store or Update
    var validator = jQuery("#commonMaterialRequestForm").validate({
        onclick: false,
        // onkeyup: false,
        onfocusout: false,
        rules: {
            mr_sequence: {
                required: true
            },
            to_location_id: {
                required: true
            },
            customer_group_id: {
                required: true
            },
            mr_date: {
                required: true,
                date_check: true,
                dateFormat: true
            },
            'item_id[]': {
                required: true
            },
            'mr_qty[]': {
                required: true,
                notOnlyZero: '0.001',
            },
        },

        messages: {

            mr_sequence: {
                required: "Please Enter MR. Number"
            },
            to_location_id: {
                required: "Please Select To Location"
            },
            customer_group_id: {
                required: "Please Select Customer Group"
            },
            mr_date: {
                required: "Please Enter MR. Date.",
            },
            'item_id[]': {
                required: "Please Select Item"
            },
            'mr_qty[]': {
                required: "Please Enter MR. Qty.",
                notOnlyZero: 'Please Enter MR. Qty. Greater Than 0.'
            },

        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            let checkLength = jQuery("#materialRequestTable tbody tr").filter(function () {
                return jQuery(this).css('display') !== 'none';
            }).length;

            if (checkLength < 1) {
                jAlert("Please Add At Least One Material Request Detail.");
                addMaterialDetail();
                return false;
            }

            jQuery('#materialRequestButton').prop('disabled', true);
            var formdata = jQuery('#commonMaterialRequestForm').serialize();


            let formUrl = materialHiddenId != undefined && materialHiddenId != "" ? RouteBasePath + "/update-material_request" : RouteBasePath + "/store-material_request";


            jQuery.ajax({

                url: formUrl,

                type: 'POST',

                data: formdata,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if (data.response_code == 1) {

                        if (materialHiddenId != undefined && materialHiddenId != null) {
                            toastSuccess(data.response_message, nextFn);
                            function nextFn() {
                                window.location.href = RouteBasePath + "/manage-material_request";
                            }
                        } else {
                            toastSuccess(data.response_message, nextFn);
                            function nextFn() {
                                window.location.reload();
                            }

                            jQuery('#materialRequestButton').prop('disabled', false);
                        }
                    } else {
                        jQuery('#materialRequestButton').prop('disabled', false);
                        toastError(data.response_message);
                    }
                },

                error: function (jqXHR, textStatus, errorThrown) {
                    var errMessage = JSON.parse(jqXHR.responseText);
                    if (errMessage.errors) {
                        jQuery('#materialRequestButton').prop('disabled', false);
                        validator.showErrors(errMessage.errors);
                    }
                    else if (jqXHR.status == 401) {
                        jQuery('#materialRequestButton').prop('disabled', false);
                        jAlert(jqXHR.statusText);
                    }
                    else {
                        jQuery('#materialRequestButton').prop('disabled', false);
                        jAlert('Something went wrong!');
                        console.log(JSON.parse(jqXHR.responseText));
                    }
                }
            });
        }
    });
});


// get the latest number
function getLatestMaterialNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_material_request_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#po_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#mr_date').val(currentDate);
                jQuery('#mr_number').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });
                jQuery('#mr_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#mr_number').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}


// add time 
function addMaterialDetail() {
    var thisHtml = `
    <tr>

    <td>
        <a onclick="removeMaterialDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
    </td>


    <td class="sr_no"></td>

    <td> <select name="item_id[]"  class="chzn-select item_id  add_item item_id mr_item_select_width" onChange="getItemData(this)">${productDrpHtml}</select></td>

    <td>
    <input type="hidden" name="mr_details_id[]" value="0">
    <input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

    <td><input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" style="width:60%;" tabindex="-1" readonly/></td>
    
    <td><input type="text" name="unit[]" id="unit" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" style="width:100px;"  tabindex="-1" readonly/></td>

    <td><input type="text" name="mr_qty[]" onblur="formatPoints(this,3)" id="mr_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey mr_qty" tabindex="-1" style="width:50px;" readonly/></td>

    
   <td><input type="text" name="remarks[]" id="remarks" tabindex="-1"   class="form-control salesmanageTable potableremarks" readonly/></td>

    </tr>`;
    jQuery('#materialRequestTable tbody').append(thisHtml);

    setTimeout(() => {
        srNo();
    }, 200);
    sumSoQty();
}


// edit time 
function fillmaterialRequestTable(materialRequestDetails) {
    if (materialRequestDetails.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in materialRequestDetails) {

            var sr_no = counter;

            var mr_details_id = materialRequestDetails[key].mr_details_id ? materialRequestDetails[key].mr_details_id : "";

            var item_id = materialRequestDetails[key].item_id ? materialRequestDetails[key].item_id : "";

            var item_code = materialRequestDetails[key].item_code ? materialRequestDetails[key].item_code : "";

            var stock_qty = materialRequestDetails[key].stock_qty ? materialRequestDetails[key].stock_qty.toFixed(3) : parseFloat(0).toFixed(3);

            var item_group_name = materialRequestDetails[key].item_group_name ? materialRequestDetails[key].item_group_name : "";


            var unit_name = materialRequestDetails[key].unit_name ? materialRequestDetails[key].unit_name : "";

            var mr_qty = materialRequestDetails[key].mr_qty ? materialRequestDetails[key].mr_qty.toFixed(3) : "";



            var remarks = materialRequestDetails[key].remarks ? checkSpecialCharacter(materialRequestDetails[key].remarks) : "";

            thisHtml += `
            <tr>
        
            <td>
                <a onclick="removeMaterialDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
            </td>
        
        
            <td class="sr_no">${sr_no}</td>
        
            <td> <select name="item_id[]"  class="chzn-select  item_id add_item item_id_${sr_no} mr_item_select_width" onChange="getItemData(this)" ${materialHiddenId != undefined ? materialRequestDetails[key].in_use == true ? 'readonly' : '' : ''}>${productDrpHtml}</select></td>

            
            <td><input type="hidden" name="mr_details_id[]" value="${mr_details_id}"><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" readonly/></td>  
            
           
            <td><input type="text" name="stock_qty[]" id="stock_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" value="${stock_qty}" style="width:60%;" tabindex="-1" readonly/></td> 

            <td><input type="text" name="unit[]" id="unit" onKeyup="sumSoQty(this)"  class="form-control allow-desimal stock_qty" value="${unit_name}" style="width:100px;" tabindex="-1" readonly/></td>
            `;

            if (materialHiddenId == undefined) {
                thisHtml += `   
                
                <td><input type="text" name="mr_qty[]" onblur="formatPoints(this,3)" id="mr_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey mr_qty" tabindex="-1" readonly/></td>

                <td><input type="text" name="remarks[]" id="remarks" class="form-control salesmanageTable potableremarks" tabindex="-1" readonly /></td>
                `;
            } else {
                // thisHtml += `                     

                // <td><input type="text" name="mr_qty[]" onblur="formatPoints(this,3)" id="mr_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey mr_qty" style="width:50px;" value="${mr_qty}"  min="${parseFloat(materialRequestDetails[key].used_qty).toFixed(3)}" ${parseFloat(materialRequestDetails[key].used_qty).toFixed(3) == mr_qty ? 'readonly' : ''}></td>


                // <td><input type="text" name="remarks[]" id="remarks"  value="${remarks}" class="form-control salesmanageTable potableremarks"  ${materialHiddenId != undefined ? materialRequestDetails[key].in_use == true ? 'readonly' : ''   : ''}></td>
                // `;

                thisHtml += `                     
                
                <td><input type="text" name="mr_qty[]" onblur="formatPoints(this,3)" id="mr_qty" onKeyup="sumSoQty(this)"  class="form-control isNumberKey mr_qty" style="width:50px;" value="${mr_qty}"  min="${parseFloat(materialRequestDetails[key].used_qty).toFixed(3)}" ${materialRequestDetails[key].in_use == true ? 'readonly' : ''}></td>
                
              
                <td><input type="text" name="remarks[]" id="remarks"  value="${remarks}" class="form-control salesmanageTable potableremarks"  ${materialHiddenId != undefined ? materialRequestDetails[key].in_use == true ? 'readonly' : '' : ''}></td>
                `;
            }

            `</tr>`;

            counter++;

        }

        jQuery('#materialRequestTable tbody').append(thisHtml);
        setTimeout(() => {
            var counter = 1;

            for (let key in materialRequestDetails) {
                var item_id = materialRequestDetails[key].item_id ? materialRequestDetails[key].item_id : ""
                var item_type = materialRequestDetails[key].item_type ? materialRequestDetails[key].item_type : ""


                jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');
                jQuery(`.item_type_${counter}`).val(item_type).trigger('liszt:updated');
                counter++;
            }
        }, 100);
    }
    sumSoQty();
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
    var total = parseFloat(0).toFixed(3);
    jQuery('.mr_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            total = parseFloat(total) + parseFloat(total1);
        }
    });

    total != 0.000 && total != "" ? jQuery('.materialrequestsum').text(parseFloat(total).toFixed(3)) : jQuery('.materialrequestsum').text('');
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

                selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id mr_item_select_width" onChange="getItemData(this), sumSoQty(this)">${productDrpHtml}</select>`);
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
        jQuery(th).parents('tr').find("#mr_qty").prop('readonly', false);
        jQuery(th).parents('tr').find("#mr_qty").prop('tabindex', 0);
        jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
        jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);

        jQuery("#customer_group_id").prop({ tabindex: -1 }).attr('readonly', true);
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
    //                 jQuery(th).parents('tr').find("#stock_qty").val(minQty != null ? parseFloat(minQty).toFixed(3) : "");                  
    //                 jQuery(th).parents('tr').find("#mr_qty").prop('readonly', false);
    //                 jQuery(th).parents('tr').find("#mr_qty").prop('tabindex', 0);
    //                 jQuery(th).parents('tr').find("#remarks").prop('readonly', false);
    //                 jQuery(th).parents('tr').find("#remarks").prop('tabindex', 0);
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


// function removeMaterialDetails(th) {
//     jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
//             if (r === true) {
//                 jQuery(th).parents("tr").remove();
//                 srNo();
//                 var mr_qty = jQuery(th).parents('tr').find('#mr_qty').val();
//                 if (mr_qty) {
//                     var item_total = jQuery('.materialrequestsum').text();

//                     if (item_total != "") 
//                         item_final_total = parseInt(item_total) - parseInt(mr_qty);

//                     item_final_total > 0 ?     jQuery('.materialrequestsum').text(parseFloat(item_final_total).toFixed(3)) : jQuery('.materialrequestsum').text('');
//                 }
//             }
//     });
// }


function removeMaterialDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        if (r === true) {

            let mrPartId = jQuery(th).closest('tr').prev('tr').find('input[name="mr_details_id[]"]').val();

            if (mrPartId != '') {
                jQuery.ajax({
                    url: RouteBasePath + "/check-mr_part_in_use?mr_part_id=" + mrPartId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        jQuery(th).removeClass('file-loader');
                        if (data.response_code == 1) {
                            toastError(data.response_message);
                        } else {
                            jQuery(th).parents("tr").remove();
                            srNo();
                            var mr_qty = jQuery(th).parents('tr').find('#mr_qty').val();
                            if (mr_qty) {
                                var item_total = jQuery('.materialrequestsum').text();

                                if (item_total != "")
                                    item_final_total = parseFloat(item_total) - parseFloat(mr_qty);

                                item_final_total > 0 ? jQuery('.materialrequestsum').text(parseFloat(item_final_total).toFixed(3)) : jQuery('.materialrequestsum').text('');
                            }
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
                var mr_qty = jQuery(th).parents('tr').find('#mr_qty').val();
                if (mr_qty) {
                    var item_total = jQuery('.materialrequestsum').text();

                    if (item_total != "")
                        item_final_total = parseFloat(item_total) - parseFloat(mr_qty);

                    item_final_total > 0 ? jQuery('.materialrequestsum').text(parseFloat(item_final_total).toFixed(3)) : jQuery('.materialrequestsum').text('');
                }
            }
        }


    });
}

function changeItemTypeValue(e) {
    let selectVal = e.value;
    let editVal = e;
    if ((editVal != undefined && editVal == 1) || (selectVal != undefined && selectVal == 1)) {
        jQuery("#supplier_id").prop({ tabindex: -1 }).attr('readonly', true).val('').trigger('liszt:updated');
        jQuery("#issue_type").attr('disabled', false).val('');
    }
    else {
        jQuery("#supplier_id").attr('readonly', false).val();
        jQuery("#issue_type").attr('disabled', true).val('returnable');
    }
}




jQuery('#mr_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();
    if (subBtn == "submit" || subBtn == "Submit") {
        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }
    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid MR.web No.');
            jQuery('#mr_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    // jQuery('#mr_sequence').focus();
                    jQuery("#to_location_id").trigger('liszt:activate');
                }, 1000);
            });
            jQuery('#mr_sequence').val('');
        } else {
            jQuery("#materialRequestButton").attr('disabled', true);
            jQuery('#mr_sequence').parent().parent().parent('div.control-group').removeClass('error');
            var urL = RouteBasePath + "/check-material_request?for=add&mr_sequence=" + val;
            if (materialHiddenId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-material_request?for=edit&mr_sequence=" + val + "&id=" + materialHiddenId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#mr_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        jQuery('#mr_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                // jQuery('#mr_sequence').focus();
                                jQuery("#to_location_id").trigger('liszt:activate');
                            }, 1000);
                        });

                        jQuery('#mr_sequence').val('');
                    } else {
                        jQuery('#mr_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#mr_number').val(data.latest_po_no);
                        jQuery('#mr_sequence').val(val);
                    }
                    jQuery("#materialRequestButton").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#mr_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#mr_number').val('');
        jQuery('#mr_sequence').val('');
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














// ===========================================================================


// // extra code 
// jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
//     // formatPoints(element, 3); // Format the value before validation
//     //return this.optional(element) || parseFloat(value) >= parseFloat(param);
//     return this.optional(element) || parseFloat(value) >= 0.001;
// });


// // 'item_id[]': {
//         //     required: function (e) {
//         //         if (jQuery(e).val().trim() == "") {
//         //             jQuery(e).addClass('error');
//         //             jQuery(e).focus();
//         //             return true;
//         //         } else {
//         //             jQuery(e).removeClass('error');
//         //         }
//         //     },
//         // },

// //  toastPreview(data.response_message, redirectFn, prePO);
//                         // function redirectFn() {
//                         //     window.location.href = RouteBasePath + "/manage-material_request";
//                         // };
//                         // function prePO() {
//                         //     id = btoa(data.id);
//                         //     window.location.reload();
//                         // }

//      // toastPreview(data.response_message, redirectFn, prePO);
//                         // function redirectFn() {
//                         //     window.location.reload();
//                         // }
//                         // function prePO() {
//                         //     id = btoa(data.id);
//                         //     window.location.reload();
//                         // }

//                       // jQuery(th).parents('tr').find("#mr_qty").attr('max', minQty);


// function removeMaterialDetails(th) {
// jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

//     // let checkLength = jQuery("#materialRequestTable tbody tr").filter(function () {
//     //     return jQuery(this).css('display') !== 'none';
//     // }).length;

//     // if (checkLength > 1) {
//         if (r === true) {
//             jQuery(th).parents("tr").remove();
//             srNo();
//             var mr_qty = jQuery(th).parents('tr').find('#mr_qty').val();
//             //var po_amt = jQuery(th).parents('tr').find('#amount').val();

//             if (mr_qty) {
//                 var item_total = jQuery('.materialrequestsum').text();

//                 if (item_total != "") 
//                     item_final_total = parseInt(item_total) - parseInt(mr_qty);

//                 item_final_total > 0 ?     jQuery('.materialrequestsum').text(parseFloat(item_final_total).toFixed(3)) : jQuery('.materialrequestsum').text('');

//             }
//             //jQuery('.amountsum').text(amt_final_total);
//         }
//     // }
//     // else {
//     //     jAlert("Please At Least Item List Item Required");
//     // }

// });
// }


async function getItemRateFromPriceList() {
    return new Promise((resolve, reject) => {
        var customer_group_id = jQuery('#customer_group_id option:selected').val();

        if (customer_group_id != '') {

            if (materialHiddenId == undefined) {
                var Url = RouteBasePath + "/get-items_from_price_list_to_customer?customer_group_id=" + customer_group_id;
            } else {
                var Url = RouteBasePath + "/get-items_from_price_list_to_customer?customer_group_id=" + customer_group_id + "&id=" + materialHiddenId;
            }

            jQuery.ajax({
                url: Url,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        if (materialHiddenId == undefined) {
                            jQuery('#materialRequestTable tbody').empty();
                            addMaterialDetail();
                        } else {
                            setTimeout(() => {
                                let checkLength = jQuery("#materialRequestTable tbody tr").filter(function () {
                                    return jQuery(this).css('display') !== 'none';
                                }).length;

                                if (checkLength < 1) {
                                    addMaterialDetail();
                                }
                            }, 600);

                        }
                        if (data.mappedItems.length > 0) {
                            productDrpHtml = `<option value="">Select Item</option>`;
                            var item_id = ``;
                            for (let indx in data.mappedItems) {

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

                        resolve();  // Resolve promise when finished

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