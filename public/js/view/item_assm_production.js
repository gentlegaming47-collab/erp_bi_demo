
let ItemAssmProductionHiddenId = jQuery('#commonItemAssmProductionForm').find('input:hidden[name="id"]').val();

const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;
let minValue;
let assArray = [];

var productDrpHtml = '<option value="">Select Item</option>';
var getProductDataEdit = '';
var item_id = ``;



jQuery('#mininum_qty').prop({ tabindex: -1, readonly: true }).css('border', 'none');
// jQuery('#mininum_qty').prop('readonly', true).css('border', 'none');
jQuery('#mininum_qty').addClass('minQtyStyle')
jQuery('div#hide').hide();


// Validation 

jQuery.validator.addMethod("checkAssQty", function (value, element, param) {
    jQuery(element).prop("readonly", false);
    // jQuery(element).prop("readonly", false);
    return this.optional(element) || parseInt(value) > 0.001;
});

jQuery.validator.addMethod("validateDetailItem", function (value, element, params) {
     var item_scond_unit = jQuery('#iap_item_id option:selected').data('secondary_unit');
  
    if (item_scond_unit == 'Yes') {
            return value !== "";
    }
    return true;
}, "Please Select Item Detail.");

// general function 




jQuery("#iap_item_id").on("change", function (e) {
    let getItemName = e.target.value;  
    let selectedOption = jQuery(this).find('option:selected'); 
    let secondaryUnit = selectedOption.data('secondary_unit'); 
    
    
    jQuery('#pre_iap_item').val(getItemName);
    if(secondaryUnit != 'Yes'){    
        assArray = [];  
        fetchItemMapping(getItemName);
    }else{
        if(ItemAssmProductionHiddenId == undefined){
            //  jQuery('#pre_iap_item').val(0);
        }

        jQuery("#item_code").val('');
        jQuery("#item_unit").val('');
        jQuery('#itemAssmProductionTable tbody').empty();
        jQuery("#itemAssmProductionTable").find(".item_pro_assm_qtysum").empty();
        jQuery("#mininum_qty").val('');
        jQuery("#assembly_qty").val('');
    }
});

jQuery("#item_details_id").on("change", function (e) {
     var item_details_id = e.target.value;  
     var item_id = jQuery('#iap_item_id').val();

    jQuery('#pre_item_details_id').val(item_details_id);
    fetchItemDetailsMapping(item_details_id,item_id);
  
});

jQuery("#assembly_qty").on("keyup", function(){
    calComQty();
});

let calculateAssQty = (stockQty, mapQty) => {  
    if (stockQty != "" && mapQty != "")
    return mapQty && stockQty != "" ? stockQty / mapQty : "";
    // return mapQty && stockQty != "" ? mapQty / stockQty : "";
}


function fetchItemMapping(getItemName) {
    
    if (getItemName != "" && getItemName != null) {

        jQuery.ajax({
            url: RouteBasePath + "/fetchItemCode?item_id=" + getItemName,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    
                    jQuery("#item_code").val(data.item_code.item_code);
                    jQuery("#item_unit").val(data.item_code.unit_name);

                    if (data.item_mapping.length > 0) {
                        
                        addItemAPDetails(data.item_mapping);

                        calComQty();
                        // jQuery('#assembly_qty').attr('readonly',false);

                    }
                    else {
                        toastError('Item Mapping is Pending.');
                        jQuery('#itemAssmProductionTable tbody').empty();
                        jQuery("#itemAssmProductionTable").find(".item_pro_assm_qtysum").empty();
                        jQuery("#mininum_qty").val('');
                        jQuery("#assembly_qty").val('');
                        // jQuery('#assembly_qty').attr('readonly',true);
                    }
                } else {
                    jQuery('#itemAssmProductionTable tbody').empty();
                    jQuery("#itemAssmProductionTable").find(".item_pro_assm_qtysum").empty();
                }
            },
        });
    }
}

function fetchItemDetailsMapping(item_details_id,item_id) {
    
    if (item_details_id != "" && item_details_id != null) {

        jQuery.ajax({
            url: RouteBasePath + "/fetchItemCode?item_details_id=" + item_details_id + "&item_id=" + item_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    
                    jQuery("#item_code").val(data.item_code.item_code);
                    jQuery("#item_unit").val(data.item_code.unit_name);

                    if (data.item_mapping.length > 0) {
                        
                        addItemAPDetails(data.item_mapping);

                        calComQty();
                        // jQuery('#assembly_qty').attr('readonly',false);

                    }
                    else {
                        toastError('Item Mapping is Pending.');
                        jQuery('#itemAssmProductionTable tbody').empty();
                        jQuery("#itemAssmProductionTable").find(".item_pro_assm_qtysum").empty();
                        jQuery("#mininum_qty").val('');
                        jQuery("#assembly_qty").val('');
                        // jQuery('#assembly_qty').attr('readonly',true);
                    }
                } else {
                    jQuery('#itemAssmProductionTable tbody').empty();
                    jQuery("#itemAssmProductionTable").find(".item_pro_assm_qtysum").empty();
                }
            },
        });
    }
}

// get the latest number
function getLatestItemAPNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-pending_item_assm_production_qty",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            jQuery('#po_no').removeClass('file-loader');
            if (data.response_code == 1) {
                jQuery('#iap_date').val(currentDate);
                jQuery('#iap_number').val(data.latest_po_no).prop({ tabindex: -1, readonly: true });
                jQuery('#iap_sequence').val(data.number).prop({ tabindex: -1, readonly: true });
            } else {
                console.log(data.response_message)
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('#iap_sequence').removeClass('file-loader');
            console.log('Field To Get Latest SO No.!')
        }
    });
}



function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
}

function sumSoQty(th) {
    var total = 0;
    jQuery('.mapped_qty').map(function () {
        var total1 = jQuery(this).val();
        if (total1 != "") {
            total = parseInt(total) + parseInt(total1);
        }
    });
    total != 0 && total != "" ? jQuery('.item_pro_assm_qtysum').text(parseFloat(total).toFixed(3)) : jQuery('.item_pro_assm_qtysum').text('');


}


function changeItemTypeValue(e) {

    let selectVal = e.value;
    let editVal = e;

    if ((editVal != undefined && editVal == 1) || (selectVal != undefined && selectVal == 1)) {

        jQuery("#supplier_id").prop( {tabindex : -1}).attr('readonly', true).val('').trigger('liszt:updated');
        


        // jQuery("#supplier_id").prop({ tabindex: -1, readonly: true }).val('').trigger('liszt:updated');
        // jQuery("#supplier_id").attr('readonly', true).val('').trigger('liszt:updated');
        jQuery("#issue_type").attr('disabled', false).val('');
    }
    else {
        jQuery("#supplier_id").prop({ tabindex: -1, readonly: false }).val();
        // jQuery("#supplier_id").attr('readonly', false).val();
        jQuery("#issue_type").attr('disabled', true).val('returnable');
    }

}


function calComQty() {        
    var checkassQty = jQuery("#assembly_qty").val() ;
    
    if (!isNaN(checkassQty) && checkassQty != "") {
        jQuery('#itemAssmProductionTable tbody tr').each(function (e) {
          
            var mapQty = jQuery(this).find('input[name="mapped_qty[]"]').val();            
            jQuery(this).find('input[name="consumotion_qty[]"]').val(checkassQty != null ? parseFloat(checkassQty * mapQty).toFixed(3) : '');
            // jQuery(this).find('input[name="consumotion_qty[]"]').val(parseFloat(checkassQty * mapQty).toFixed(3));
        });
    } else {
        jQuery('#itemAssmProductionTable tbody tr').each(function (indx) {
            jQuery(this).find('input[name="consumotion_qty[]"]').val('');
        });
    }

}


jQuery(document).ready(function () {
    let headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

    if (ItemAssmProductionHiddenId != null && ItemAssmProductionHiddenId != undefined) {
        jQuery('#show-progress').addClass('loader-progress-whole-page'); 

        jQuery.ajax({

            url: RouteBasePath + "/get-item_assm_production/" + ItemAssmProductionHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {  


                    // setTimeout(() => {
                    //     jQuery('#iap_sequence').focus();
                    // }, 100);

                    jQuery("#iap_sequence").val(data.itemAssProduction.iap_sequence).prop({ tabindex: -1, readonly: true });
                    jQuery("#iap_number").val(data.itemAssProduction.iap_number).prop({ tabindex: -1, readonly: true });
                    if(data.itemAssProduction.in_use == true)
                    {

                        jQuery("#iap_date").val(data.itemAssProduction.iap_date).prop({ tabindex: -1, readonly: true });
                    }
                    else{
                        jQuery("#iap_date").val(data.itemAssProduction.iap_date);
                    }
     
                    jQuery("#iap_item_id").val(data.itemAssProduction.item_id).trigger('liszt:updated');

                     jQuery('#iap_item_id').prop({ tabindex: -1 }).attr('readonly', true);
                    //  jQuery('#iap_item_id').change();

                    if(data.itemAssProduction.item_details_id != null){
                        getSecondUnit();
                    }else{
                        jQuery("#assembly_qty").attr("onblur", "formatPoints(this,3)");
                        jQuery("#assembly_qty").removeClass("only-numbers").addClass("isNumberKey");
                    }
                    
                    setTimeout(() => {
                        // jQuery("#item_details_id").val(data.itemAssProduction.item_details_id).trigger('liszt:updated').change();
                        jQuery("#item_details_id").val(data.itemAssProduction.item_details_id).trigger('liszt:updated');
                    }, 1500);
                     jQuery('#item_details_id').prop({ tabindex: -1 }).attr('readonly', true);
                    // jQuery("#iap_item_id").val(data.itemAssProduction.item_id).trigger('liszt:updated').attr('readonly', true);
                    jQuery('#pre_iap_item').val(data.itemAssProduction.item_id);
                    jQuery('#pre_item_details_id').val(data.itemAssProduction.item_details_id);
                    jQuery("#item_code").val(data.itemAssProduction.item_code).prop({ tabindex: -1, readonly: true });
                    jQuery("#item_unit").val(data.itemAssProduction.unit_name).prop({ tabindex: -1, readonly: true });

                    jQuery("#special_notes").val(data.itemAssProduction.special_notes)
                    jQuery("#assembly_qty").val(data.itemAssProduction.assembly_qty).prop({ tabindex: -1, readonly: true });
                    // jQuery("#assembly_qty").val(parseFloat(data.itemAssProduction.assembly_qty).toFixed(3))
                 
                    jQuery("#org_assembly_qty").val(data.itemAssProduction.assembly_qty)
                    disabledDropdownVal();

                   fillitemIProductionTable(data.itemAssmProductionDetails)

                   setTimeout(() => {
                    // jQuery('#iap_date').focus();
                   }, 100);

                   jQuery('#show-progress').removeClass('loader-progress-whole-page');

                } else {

                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-item_issue";
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
        
        jQuery(document).ready(function () {  

            // setTimeout(() => {
            //     jQuery('#iap_sequence').focus();
            // }, 100);
            
            getLatestItemAPNo();
         
            jQuery("#commonItemAssmProductionForm").find('#item_code').prop({ tabindex: -1, readonly: true });
            jQuery("#commonItemAssmProductionForm").find('#item_unit').prop({ tabindex: -1, readonly: true });
            disabledDropdownVal();

            setTimeout(() => {
                // jQuery('#iap_date').focus();
                jQuery("#iap_item_id").trigger('liszt:activate');
            }, 100);
        });
    }


    var validator = jQuery("#commonItemAssmProductionForm").validate({
        ignore: [],
        onclick: false,
        rules: {
            onkeyup: false,
            onfocusout: false,

            ip_sequence: {
                required: true
            },

            ip_date: {
                required: true,
                date_check: true,
                dateFormat: true
            },
            iap_item_id:{
                required: true
            },
            item_details_id: {
            validateDetailItem: true,
            },

            'item_id[]': {
                required: function (e) {
                    var selectedValue = jQuery("#commonItemAssmProductionForm").find('#iap_item_id').val();
                    var value = jQuery("#commonItemAssmProductionForm").find('#item_id').val();
                    var ass_qty = jQuery("#commonItemAssmProductionForm").find('#assembly_qty').val();
                    if(selectedValue != "" && ass_qty != ""  && value == "") {
                        jQuery(e).addClass('error');
                        jQuery(e).focus();
                        return true;
                    } else {
                        jQuery(e).removeClass('error');
                        return false;
                    }
                },
            },
            'production_qty[]': {
                required: true
            },
            'assembly_qty': {
                required: true,
              //  checkAssQty: '0.001'
            },
            'check_stockQty[]': {
               // checkAssQty: '0.001'
            },
        },

        messages: {


            ip_sequence: {
                required: "Please Enter IP. Number"
            },

            ip_date: {
                required: "Please Enter Issue Date.",
            },
            iap_item_id:{
                required: "Please Select Item Name"
            },
            'item_id[]': {
                required: "Please Select Item"
            },

            'production_qty[]': {
                required: "Please Enter Production Qty."
            },
            'assembly_qty': {
                required: "Please Enter Assembly Qty.",
               // checkAssQty: "Please Enter A Value Greater Than 0."
            },
            'check_stockQty[]': {
                //checkAssQty: "Please Enter A Stock Qty. Greater Than 0."
            },

        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            let checkAssQty = jQuery("#assembly_qty").val();
           
            // let checkAssQty = jQuery("#assembly_qty").val();
            if(checkAssQty <= 0)
            {
                toastError("Please Enter A Value Greater Than 0.");
                return false;
            }
            
            // var checkMinVal = Math.min(...assArray);
            var checkMinVal = jQuery('#mininum_qty').val() != '' ? jQuery('#mininum_qty').val() : 0;
            
            
        
            if (parseFloat(checkMinVal) < parseFloat(checkAssQty)) {
                toastError("There Is Not Enough Stock");
                return false;
            }

            jQuery('#item_assm_production_button').prop('disabled', true);
            var formdata = jQuery('#commonItemAssmProductionForm').serialize();

            var rowCount = jQuery('#itemAssmProductionTable tr').length;
            if(rowCount <= 2)
            {
                jAlert("Please Add At Least One Item Production Detail.");
                return false;
            }
            

            let formUrl = ItemAssmProductionHiddenId != undefined && ItemAssmProductionHiddenId != "" ? RouteBasePath + "/update-item_assm_production" : RouteBasePath + "/store-item_assm_production";


            jQuery.ajax({

                url: formUrl,

                type: 'POST',

                data: formdata,

                headers: headerOpt,

                dataType: 'json',

                processData: false,

                success: function (data) {

                    if (data.response_code == 1) {

                        if (ItemAssmProductionHiddenId != undefined && ItemAssmProductionHiddenId != null) {                          
                            toastSuccess(data.response_message, nextFn);
                            function nextFn() {
                              window.location.href = RouteBasePath + "/manage-item_assm_production";
                              }
                        } else {                       
                            toastSuccess(data.response_message, nextFn);
                          
                            function nextFn() {
                                window.location.reload();
                            }
                            jQuery('#item_assm_production_button').prop('disabled', false);
                        }
                    } else {
                        jQuery('#item_assm_production_button').prop('disabled', false);
                        toastError(data.response_message);
                    }
                },

                error: function (jqXHR, textStatus, errorThrown) {

                    var errMessage = JSON.parse(jqXHR.responseText);

                    if (errMessage.errors) {
                        jQuery("#item_assm_production_button").attr('disabled', true);
                        validator.showErrors(errMessage.errors);

                    } else if (jqXHR.status == 401) {
                        jQuery("#item_assm_production_button").attr('disabled', true);
                        jAlert(jqXHR.statusText);


                    } else {

                        jQuery("#item_assm_production_button").attr('disabled', true);
                        jAlert('Something went wrong!');

                        console.log(JSON.parse(jqXHR.responseText));
                    }
                }
            });
        }
    });
});


// add time 
function addItemAPDetails(item_mapping) {
    var thisHtml = '';
    var counter = 1;
    var assArray = []; // Declare assArray outside the loop to store qtys

    // Loop through item_mapping using for...in
    for (let key in item_mapping) {
        var sr_no = counter;

        // Fetch values for mapQty, stock_qty, etc.
        var mapQty = item_mapping[key].raw_material_qty ? parseFloat(item_mapping[key].raw_material_qty).toFixed(3) : "";
        var item_id = item_mapping[key].ItemID ? item_mapping[key].ItemID : "";
        var item_name = item_mapping[key].item_name ? item_mapping[key].item_name : "";
        var item_code = item_mapping[key].item_code ? item_mapping[key].item_code : "";
        var item_group_name = item_mapping[key].itemGroup ? item_mapping[key].itemGroup : "";
        var stock_qty = item_mapping[key].stock_qty != 0 ? parseFloat(item_mapping[key].stock_qty).toFixed(3) : 0;
        var unit = item_mapping[key].unit_name ? item_mapping[key].unit_name : "";
        var consumption_qty = item_mapping[key].consumption_qty ? item_mapping[key].consumption_qty : "";

        // Check if mapQty and stock_qty are valid
        if (mapQty != undefined && mapQty != '' && stock_qty != undefined && stock_qty != '') {
            var qty = parseInt(calculateAssQty(stock_qty, mapQty));

            if (!isNaN(qty)) {
                assArray.push(qty); // Add valid qty to assArray
            } else {
                assArray.push(0); // If qty is NaN, push 0
            }
        } else {
            assArray.push(0); // If mapQty or stock_qty is invalid, push 0
        }

        // Generate the HTML content for the table
        thisHtml += `
            <tr>
                <input type="hidden" name="assmbly_production[]" id="assmbly_production"></td></tr>
                <td class="sr_no">${sr_no}</td>

                <td> 
                    <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id}">
                    ${item_name}
                    <input type="hidden" name="item_id[]" id="item_id" style="width:100px" class="form-control salesmanageTable POaddtables item_id add_item item_id" tabindex="-1" value="${item_id}" readonly/>
                </td>

                <td><input type="text" name="code[]" id="code" style="width:100px" class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" readonly/></td>

                <td><input type="text" name="group[]" id="group" style="width:100px" class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_group_name}" readonly/></td>

                <td>
                    <input type="hidden" name="org_mapped_qty[]" value="${mapQty}">
                    <input type="text" name="mapped_qty[]" id="mapped_qty" onkeyup="sumSoQty(this)" onblur="formatPoints(this,3)" class="form-control isNumberKey mapped_qty" style="width:50px;" tabindex="-1" value="${mapQty}" readonly/>
                </td>

                <td><input type="text" name="stock_qty[]" id="stock_qty" class="form-control allow-desimal stock_qty" style="width:60%;" tabindex="-1" readonly value="${stock_qty + consumption_qty}"/></td>

                <td>
                    <input type="hidden" name="org_consumption_qty[]" value="${consumption_qty}">
                    <input type="text" name="consumotion_qty[]" id="consumotion_qty" class="form-control allow-desimal consumotion_qty" style="width:50px;" tabindex="-1" value="${consumption_qty}" readonly/>
                </td>

                <td><input type="text" name="unit[]" id="unit" style="width:103px;" class="form-control POaddtables" tabindex="-1" value="${unit}" readonly/></td>
            </tr>`;

        counter++;
    }

    // After the loop, calculate the min value and set it
    var minValue = Math.min(...assArray.length ? assArray : [0]); // Get the minimum value

    // Update the form fields with the calculated min value
    jQuery('#itemAssmProductionTable tbody').empty().append(thisHtml);
    jQuery("#assembly_qty").val(minValue);
    jQuery("#mininum_qty").val(minValue);

    srNo(); // Optional function call (add if needed)
    sumSoQty(); // Optional function call (add if needed)
}


// edit time 
function fillitemIProductionTable(itemAssmProductionDetails) {
    if (itemAssmProductionDetails.length > 0) {
        var thisHtml = '';
        var counter = 1;
        var assArray = []; // Define assArray here to collect all qtys

        for (let key in itemAssmProductionDetails) {
            var sr_no = counter;

            var iap_details_id = itemAssmProductionDetails[key].iap_details_id || "";

            var mapQty = itemAssmProductionDetails[key].raw_material_qty ? parseFloat(itemAssmProductionDetails[key].raw_material_qty).toFixed(3) : "";
            var item_name = itemAssmProductionDetails[key].item_name || "";
            var item_id = itemAssmProductionDetails[key].item_id || "";
            var item_code = itemAssmProductionDetails[key].item_code || "";
            var item_group_name = itemAssmProductionDetails[key].item_group_name || "";

            var stock_qty = itemAssmProductionDetails[key].stock_qty ? parseFloat(itemAssmProductionDetails[key].stock_qty).toFixed(3) : 0;
            var consumption_qty = itemAssmProductionDetails[key].consumption_qty ? parseFloat(itemAssmProductionDetails[key].consumption_qty).toFixed(3) : "";
            var unit = itemAssmProductionDetails[key].unit_name || "";

            var totalStockQty = (itemAssmProductionDetails[key].stock_qty || 0) + (itemAssmProductionDetails[key].consumption_qty || 0);

            var qty = parseInt(calculateAssQty(totalStockQty, mapQty));
            if (!isNaN(qty)) {
                assArray.push(qty); // store valid qty
            } else {
                assArray.push(0); // store 0 if invalid
            }

            thisHtml += `
                <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="iap_details_id[]" value="${iap_details_id}"></td></tr>                   
                <tr>
                    <td class="sr_no"></td>

                    <td> 
                        <input type="hidden" name="pre_item[]" id="pre_item" value="${item_id}">
                        ${item_name}
                        <input type="hidden" name="item_id[]" id="item_id" style="width:100px" class="form-control salesmanageTable POaddtables item_id add_item item_id" tabindex="-1" value="${item_id}" readonly/>
                    </td>
                
                    <td><input type="text" name="code[]" id="code" style="width:100px" class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" readonly/></td>

                    <td><input type="text" name="group[]" id="group" style="width:100px" class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_group_name}" readonly/></td>
                
                    <td>
                        <input type="hidden" name="org_mapped_qty[]" value="${mapQty}">
                        <input type="text" name="mapped_qty[]" id="mapped_qty" onkeyup="sumSoQty(this)" class="form-control allow-desimal mapped_qty" style="width:50px;" tabindex="-1" value="${mapQty}" readonly/>
                    </td>       

                    <td><input type="text" name="stock_qty[]" id="stock_qty" class="form-control allow-desimal stock_qty" style="width:60%;" tabindex="-1" readonly value="${parseFloat(totalStockQty).toFixed(3)}"/></td>
                
                    <td>
                        <input type="hidden" name="org_consumption_qty[]" value="${consumption_qty}">
                        <input type="text" name="consumotion_qty[]" id="consumotion_qty" class="form-control allow-desimal consumotion_qty" style="width:50px;" tabindex="-1" value="${consumption_qty}" readonly/>
                    </td>

                    <td><input type="text" name="unit[]" id="unit" style="width:103px;" class="form-control POaddtables" tabindex="-1" value="${unit}" readonly/></td>
                </tr>`;

            counter++;
        }

        // ⬇️ Loop baad ma minValue calculate karo
        var minValue = Math.min(...assArray.length ? assArray : [0]);
        jQuery("#mininum_qty").val(minValue);

        jQuery('#itemAssmProductionTable tbody').append(thisHtml);

        sumSoQty();
        srNo();
    }
}





jQuery('#iap_sequence').on('change', function () {
    let val = jQuery(this).val();
    var subBtn = jQuery(document).find('.stdform').find('.formwrappers button').text();

    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.formwrappers button');
    }

    if (val != undefined) {
        if (val > 0 == false) {
            jAlert('Please Enter Valid IAP. No.');
            jQuery('#iap_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery("#popup_ok").click(function () {
                setTimeout(() => {
                    // jQuery('#iap_sequence').focus();
                    jQuery("#iap_item_id").trigger('liszt:activate');
                }, 1000);
            });
            jQuery('#iap_sequence').val('');

        } else {


            jQuery("#item_assm_production_button").attr('disabled', true);

            jQuery('#ip_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var urL = RouteBasePath + "/check-iap_no_duplication?for=add&iap_sequence=" + val;

            if (ItemAssmProductionHiddenId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-iap_no_duplication?for=edit&iap_sequence=" + val + "&id=" + ItemAssmProductionHiddenId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#iap_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {
                        toastError(data.response_message);
                        jQuery('#iap_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery("#popup_ok").click(function () {
                            setTimeout(() => {
                                // jQuery('#iap_sequence').focus();
                                jQuery("#iap_item_id").trigger('liszt:activate');
                            }, 1000);
                        });
                        jQuery('#iap_sequence').val('');
                    } else {
                        jQuery('#iap_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#iap_number').val(data.latest_po_no);
                        jQuery('#iap_sequence').val(val);
                    }
                    jQuery("#item_assm_production_button").attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#iap_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#iap_number').val('');
        jQuery('#iap_sequence').val('');
    }
});


jQuery("#assembly_qty").on("focusout", function (e) {
    let curVal = e.target.value;    
    // let assmVal = Math.min(...assArray);
    let assmVal = jQuery('#mininum_qty').val() != '' ? jQuery('#mininum_qty').val() : 0;
    
    if (parseFloat(curVal) > parseFloat(assmVal)) {
        // jAlert(`You can not order more than Minimum qty.`);
        jAlert(`There Is Not Enough Stock`);
    }
})


function getSecondUnit(){                     
 
    var item_scond_unit = jQuery('#iap_item_id option:selected').data('secondary_unit');
    var item_id = jQuery('#iap_item_id').val();
    $assembly_qty = jQuery("#assembly_qty");
       if(item_scond_unit == 'Yes'){
        jQuery('div#hide').show();
        $assembly_qty.removeAttr("onblur");                
        $assembly_qty.removeClass("isNumberKey").addClass("only-numbers"); 

        jQuery.ajax({
            url: RouteBasePath + "/get-item_details_data?item_id=" + item_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function(data) {
                if (data.response_code == 1) {

                          var  dropHtml = '<option value="">Select Item Name</option>';

                        if (data.item.length > 0) {                       

                            for (let indx in data.item) {
                                dropHtml += `<option value="${data.item[indx].item_details_id}">${data.item[indx].secondary_item_name} </option>`;
                            }
                        }

                        jQuery('#item_details_id').empty().append(dropHtml).trigger('liszt:updated');
                }else {
                    jQuery('#item_details_id').empty().append("<option value=''>Select Item Name</option>").trigger('liszt:updated');
                    
                }
            }
        });
    }else{
        jQuery('div#hide').hide();
        jQuery('#item_details_id').val('').trigger("liszt:updated");
    }

}









// only ref. code 

// // Consumation Qty. calculation

// let calculateComQty = (assQty, mapQty) => {
//         console.log("called");
    
//     if (!isNaN(assQty) && assQty != "") {
        
      
//         jQuery('#itemAssmProductionTable tbody tr').each(function (e) {
//             var qty = jQuery(this).find('input[name="mapped_qty[]"]').val();
//           //  var GrnQty = jQuery('input[name="mapped_qty[]"]').val();
//             console.log(qty);
         
           
//             jQuery(this).find('input[name="consumotion_qty[]"]').val(assQty * mapQty);
//         });

//     }
// }



// check duplicate item


// jQuery(document).on('change', '.item_id', function (e) {
//     var selected = jQuery(this).val();
//     var thisselected = jQuery(this);
//     if (selected) {
//         jQuery(jQuery('.item_id').not(jQuery(this))).each(function (index) {
//             if (thisselected.val() == jQuery(this).val()) {
//                 jAlert('This Item Is Already Selected.');
//                 thisselected.replaceWith(`<select name="item_id[]" id="item_id" class="chzn-select chzn-done add_item item_id" onChange="getItemData
//                     (this)">${productDrpHtml}</select>`);
//             }
//         });
//     }
// });



// function fetchItemMapping(getItemName) {
    
//     if (getItemName != "" && getItemName != null) {

//         jQuery.ajax({
//             url: RouteBasePath + "/fetchItemCode?item_id=" + getItemName,
//             type: 'GET',
//             headers: headerOpt,
//             dataType: 'json',
//             processData: false,
//             success: function (data) {
//                 if (data.response_code == 1) {
                    
//                     jQuery("#item_code").val(data.item_code.item_code);
//                     jQuery("#item_unit").val(data.item_code.unit_name);

//                     if (data.item_mapping.length > 0) {
//                         productDrpHtml = '<option value="">Select Item</option>';

//                         for (let indx in data.item_mapping) {
//                             productDrpHtml += `<option value="${data.item_mapping[indx].raw_material_id}">${data.item_mapping[indx].item_name} </option>`;
//                             item_id += `data-rate="${data.item_mapping[indx].raw_material_id}" `;
//                         }

//                         jQuery("#itemAssmProductionTable").find(".getEditData").empty().append(productDrpHtml);


//                     }
//                     else {
//                         jQuery("#itemAssmProductionTable").find("#item_id").empty();
//                         jQuery("#itemAssmProductionTable").find("#code").val('');
//                         jQuery("#itemAssmProductionTable").find("#unit").val('');
//                      //   jQuery("#item_code").val('');
//                         jQuery("#mapped_qty").val('');
//                         jQuery("#stock_qty").val('');
//                         jQuery("#consumotion_qty").val('');
//                     }
//                 } else {
//                     jQuery("#itemAssmProductionTable").find("#item_id").empty()
//                     jQuery("#itemAssmProductionTable").find("#code").empty()
//                     jQuery("#item_code").val('');
//                     jQuery("#mapped_qty").val('');
//                     jQuery("#stock_qty").val('');
//                     jQuery("#consumotion_qty").val('');
//                 }
//             },
//         });
//     }
// }



// new fetchitemmapp code

// function fetchItemMapping(getItemName) {
    
//     if (getItemName != "" && getItemName != null) {

//         jQuery.ajax({
//             url: RouteBasePath + "/fetchItemCode?item_id=" + getItemName,
//             type: 'GET',
//             headers: headerOpt,
//             dataType: 'json',
//             processData: false,
//             success: function (data) {
//                 if (data.response_code == 1) {
                    
//                     jQuery("#item_code").val(data.item_code.item_code);
//                     jQuery("#item_unit").val(data.item_code.unit_name);

//                     if (data.item_mapping.length > 0) {
                        
//                         addItemAPDetails(data.item_mapping);
                        
//                         calComQty();
                
        
            

//                         // for (let indx in data.item_mapping) {
//                         //     productDrpHtml += `<option value="${data.item_mapping[indx].raw_material_id}">${data.item_mapping[indx].item_name} </option>`;
//                         //     item_id += `data-rate="${data.item_mapping[indx].raw_material_id}" `;
//                         // }

//                         // jQuery("#itemAssmProductionTable").find(".getEditData").empty().append(productDrpHtml);


//                     }
//                     else {
//                         jQuery('#itemAssmProductionTable tbody').empty();
//                         jQuery("#itemAssmProductionTable").find(".item_pro_assm_qtysum").empty();
//                         jQuery("#mininum_qty").val('');
//                         jQuery("#assembly_qty").val('');

//                        // jQuery('#itemAssmProductionTable').empty();                        
//                         // jQuery("#itemAssmProductionTable").find("#item_id").empty();
//                         // jQuery("#itemAssmProductionTable").find("#code").val('');
//                         // jQuery("#itemAssmProductionTable").find("#unit").val('');                    
//                         // jQuery("#mapped_qty").val('');
//                         // jQuery("#stock_qty").val('');
//                         // jQuery("#consumotion_qty").val('');
//                     }
//                 } else {
//                     jQuery("#itemAssmProductionTable").find("#item_id").empty()
//                     jQuery("#itemAssmProductionTable").find("#code").empty()
//                     jQuery("#item_code").val('');
//                     jQuery("#mapped_qty").val('');
//                     jQuery("#stock_qty").val('');
//                     jQuery("#consumotion_qty").val('');
//                 }
//             },
//         });
//     }
// }



// function addItemAPDetails() {
//     var thisHtml = `
//     <tr style="display:none;"><td class="colspan=10">

//     <input type="hidden" name="iap_details_id[]" value="0"></td></tr>
  
//             <tr>
//     <tr>
//     <input type="hidden" name="assmbly_production[]" id="assmbly_production"></td></tr>

//     <td class="sr_no"></td>
//     <td> <select name="item_id[]" id="item_id" class="chzn-select chzn-done item_id add_item item_id getEditData" onChange="getItemData(this)">${productDrpHtml}</select></td>

//     <td><input type="text" name="code[]" id="code" style="width:100px" class="form-control salesmanageTable POaddtables" tabindex="-1" readonly/></td>

//     <td>
//     <input type="hidden" name="org_mapped_qty[]" value="0">
//     <input type="text" name="mapped_qty[]" id="mapped_qty" onChange="sumSoQty(this)"  class="form-control allow-desimal mapped_qty" tabindex="-1" style="width:50px;" readonly/>
//     </td>

//     <td><input type="text" name="unit[]" id="unit" style="width:103px;" class="form-control POaddtables" tabindex="-1" readonly/></td>

//     <td>
//     <input type="hidden" name="check_stockQty[]" id = "check_stockQty">
//     <input type="text" name="stock_qty[]" id="stock_qty"class="form-control allow-desimal stock_qty" style="width:50px;" tabindex="-1" readonly/>
//     </td>

//     <td><input type="text" name="consumotion_qty[]" id="consumotion_qty"  class="form-control allow-desimal consumotion_qty" style="width:50px;" tabindex="-1" readonly/></td>

  

//     </tr>`;
//     jQuery('#itemAssmProductionTable tbody').append(thisHtml);

//     srNo();
//     sumSoQty();
// }



// // edit time 
// function fillitemIProductionTable(itemAssmProductionDetails) {
//     if (itemAssmProductionDetails.length > 0) {

//         var thisHtml = '';
//         var counter = 1;
//         for (let key in itemAssmProductionDetails) {

//             var sr_no = counter;

//             var iap_details_id = itemAssmProductionDetails[key].iap_details_id ? itemAssmProductionDetails[key].iap_details_id : "";

//             var mapQty = itemAssmProductionDetails[key].raw_material_qty ? itemAssmProductionDetails[key].raw_material_qty : "";

//             var item_id = itemAssmProductionDetails[key].item_id ? itemAssmProductionDetails[key].item_id : "";



//             var item_code = itemAssmProductionDetails[key].item_code ? itemAssmProductionDetails[key].item_code : "";

//             var stock_qty = itemAssmProductionDetails[key].stock_qty != 0 ? itemAssmProductionDetails[key].stock_qty : 0;

//             var unit = itemAssmProductionDetails[key].unit_name ? itemAssmProductionDetails[key].unit_name : "";

//             var consumption_qty = itemAssmProductionDetails[key].consumption_qty ? itemAssmProductionDetails[key].consumption_qty : "";


//             thisHtml += `
//             <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="iap_details_id[]" value="${iap_details_id}"></td></tr>                   
//             <tr>

//             <td class="sr_no"></td>

//             <td> 
//             <input type="hidden" name="prev_item_id[]" value="${item_id}">
//             <select name="item_id[]" id="item_id" class="chzn-select chzn-done item_id add_item item_id_${sr_no} getEditData"  onChange="getItemData(this)">${getProductDataEdit}</select></td>
        
//             <td><input type="text" name="code[]" id="code" style="width:100px" class="form-control salesmanageTable POaddtables" tabindex="-1" value="${item_code}" readonly/></td>
        
//             <td>
//             <input type="hidden" name="org_mapped_qty[]" value="${mapQty}">
//             <input type="text" name="mapped_qty[]" id="mapped_qty" onkeyup="sumSoQty(this)"  class="form-control allow-desimal mapped_qty" style="width:50px;" tabindex="-1"  value="${mapQty}" readonly/>
//             </td>
        
//             <td><input type="text" name="unit[]" id="unit" style="width:103px;" class="form-control POaddtables" tabindex="-1" value="${unit}" readonly/></td>
        
//             <td><input type="text" name="stock_qty[]" id="stock_qty" class="form-control allow-desimal stock_qty" style="width:50px;" tabinde="-1" readonly value="${stock_qty + consumption_qty}"/></td>
        
//             <td>
//             <input type="hidden" name="org_consumption_qty[]" value="${consumption_qty}">
//             <input type="text" name="consumotion_qty[]" id="consumotion_qty"   class="form-control allow-desimal consumotion_qty" style="width:50px;" tabindex="-1" value="${consumption_qty}" readonly/></td>
            
//             </tr>`;
//             counter++;
//         }



//         setTimeout(() => {
//             var counter = 1;

//             for (let key in itemAssmProductionDetails) {
//                 var item_id = itemAssmProductionDetails[key].item_id ? itemAssmProductionDetails[key].item_id : "";
//                 jQuery(`.item_id_${counter}`).val(item_id).trigger('liszt:updated');

//                 counter++;
//             }
//         }, 500);
//         jQuery('#itemAssmProductionTable tbody').append(thisHtml);


//     }
//     sumSoQty();
//     //  totalAmount();
//     srNo();

// }





// function getItemData(th) {
    
//     let minvalue = th;

//     let item = th.value;

//     if (item != "" && item != null) {
//         jQuery.ajax({
//             url: RouteBasePath + "/get-fitting_item_assm_data?item=" + item,
//             type: 'GET',
//             headers: headerOpt,
//             dataType: 'json',
//             processData: false,
//             success: function (data) {
//                 if (data.response_code == 1) {

//                     if (data.stock_qty != null) {

//                         var minQty = isNaN(Number(data.stock_qty.stock_qty)) ? 0 : Number(data.stock_qty.stock_qty);
//                     } else {
//                         var minQty = 0;
//                     }


//                     jQuery(th).parents('tr').find("#code").val(data.item.item_code);

//                     jQuery(th).parents('tr').find("#mapped_qty").val(data.getItemMapping.raw_material_qty);



//                     jQuery(th).parents('tr').find("#stock_qty").val(parseFloat(minQty).toFixed(3));


//                     jQuery(th).parents('tr').find("#check_stockQty").val(minQty);

//                     jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);



//                     assArray = [];
//                     jQuery('#itemAssmProductionTable tbody tr').each(function (e) {
//                         var mapQty = jQuery(this).find('input[name="mapped_qty[]"]').val();
//                         var stQty = jQuery(this).find('input[name="stock_qty[]"]').val();

//                         if (mapQty != undefined && mapQty != '' && stQty != undefined && stQty != '') {
//                             var qty = parseInt(calculateAssQty(stQty, mapQty))
//                             if (!isNaN(qty)) {
                                
//                                 jQuery(th).parents('tr').prev('tr').find("#assmbly_production").val(qty);


//                                 assArray.push(qty);


//                             } else {
//                                 jQuery("#assembly_qty").val("ABC");
//                             }
//                         }

//                     })

//                     minValue = Math.min(...assArray);
//                     // jQuery("#assembly_qty").val(parseInt(minValue));
//                     jQuery("#assembly_qty").val(parseFloat(minValue));
//                    // calculateComQty(minValue)


                    
//                     sumSoQty();

//                 } else {
//                     jQuery('#code').val('');
//                     jQuery('#item_id').val('');
//                     jQuery('#group').val('');
//                     jQuery('#unit').val('');
//                     jQuery('#po_qty').val('');
//                     jQuery('#rate_unit').val('');
//                     jQuery('#remarks').val('');
//                 }
//             },
//         });
//     }
// }


// function calComQty(e) {        
//     var checkassQty = e.value;    
//     if (!isNaN(checkassQty) && checkassQty != "") {
//         jQuery('#itemAssmProductionTable tbody tr').each(function (e) {
//             var mapQty = jQuery(this).find('input[name="mapped_qty[]"]').val();            
//             jQuery(this).find('input[name="consumotion_qty[]"]').val(parseFloat(checkassQty * mapQty).toFixed(3));
//         });
//     } else {
//         jQuery('#itemAssmProductionTable tbody tr').each(function (indx) {
//             jQuery(this).find('input[name="consumotion_qty[]"]').val('');
//         });
//     }

// }
