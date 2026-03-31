var po_data = [];
const date = new Date();
let currentDay = String(date.getDate()).padStart(2, '0');
let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
let currentYear = date.getFullYear();
// we will display the date as DD-MM-YYYY 
let currentDate = `${currentDay}/${currentMonth}/${currentYear}`;

if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
}

var formId = jQuery('#PurchaseOrderForm').find('input:hidden[name="id"]').val();
if(formId == '' || formId == null){
    addPartDetail();
}

// edit data
if (formId != undefined && formId != '') { //if form is  edit

    jQuery(document).ready(function () {
        jQuery.ajax({
            url: RouteBasePath + "/get-purchase_order/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    jQuery('#supplier_id').val(data.purchase_order.supplier_id).trigger('liszt:updated');
                    jQuery('#ref_no').val(data.purchase_order.ref_no);
                    jQuery('#po_date').val(data.purchase_order.po_date);
                    jQuery('#ref_date').val(data.purchase_order.ref_date);
                    jQuery('#po_no').val(data.purchase_order.po_number);
                    jQuery('#po_sequence').val(data.purchase_order.po_sequence);
                    jQuery('#person').val(data.purchase_order.person_name);
                    jQuery('#ship_to').val(data.purchase_order.to_location_id).trigger('liszt:updated');
                    jQuery('#po_total_qty').val(data.purchase_order.total_qty);
                    jQuery('#po_total_amount').val(data.purchase_order.total_amount);
                    jQuery('#pf_charge').val(data.purchase_order.pf_charge);
                    jQuery('#freight').val(data.purchase_order.frieght).trigger('liszt:updated');
                    jQuery('#gst').val(data.purchase_order.gst);
                    jQuery('#payment_terms').val(data.purchase_order.payment_terms);
                    jQuery('#sp_notes').val(data.purchase_order.special_notes);
                    fillPOTable(data.purchase_order_details);
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
        getLatestPoNo();
    });
}
// end edit data


function fillPOTable(purchase_order_details) {
    if (purchase_order_details.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in purchase_order_details) {

            var sr_no = counter;
            var purchase_order_details_id = purchase_order_details[key].po_details_id ? purchase_order_details[key].po_details_id : "";
            var item_id                   = purchase_order_details[key].item_id ? purchase_order_details[key].item_id : "";
            var item_code                 = purchase_order_details[key].item_code ? purchase_order_details[key].item_code : "";
            var rate_per_unit             = purchase_order_details[key].rate_per_unit ? purchase_order_details[key].rate_per_unit : "";
            var unit_name                 = purchase_order_details[key].unit_name ? purchase_order_details[key].unit_name : "";
            var po_qty                    = purchase_order_details[key].po_qty ? purchase_order_details[key].po_qty : "";
            var del_date                  = purchase_order_details[key].del_date ? purchase_order_details[key].del_date : "";
            var amount                    = purchase_order_details[key].amount ? purchase_order_details[key].amount : "";
            var remarks                   = purchase_order_details[key].remarks ? purchase_order_details[key].remarks : "";

             thisHtml += `
            <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="purchase_order_detail_id[]" value="${purchase_order_details_id}"></td></tr>
                    <tr>
            <tr>
        
            <td>
                <a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
            </td>
        
        
            <td class="sr_no">${sr_no}</td>
        
            <td> <select name="item_id[]" id="item_id" class="chzn-select chzn-done add_item item_id_${sr_no}" onChange="getItemData(this)">${productDrpHtml}</select></td>
        
            <td><input type="text" name="code[]" id="code"  class="form-control POaddtables salesmanageTable" tabindex="1" value="${item_code}" readonly/></td>
        
            <td><input type="text" name="po_qty[]" id="po_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal po_qty" value="${po_qty}" style="width:50px;" disabled/></td>
            
            <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="1" value="${unit_name}" readonly/></td>
            
            <td><input type="text" name="del_date[]" class="form-control potabledate salesmanageTable date-picker del_date" value="${del_date}" disabled/></td>
            
            <td><input type="number" name="rate_unit[]" id="rate_unit" step="0.01" min="0.01" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control potabledate rate_unit salesmanageTable" value="${rate_per_unit}" onblur="formatPoints(this,2)" disabled/></td>
            
            <td><input type="text" name="amount[]" id="amount"  class="form-control salesmanageTable amount" value="${amount}" readonly/></td>
            
            <td><input type="text" name="remarks[]" id="remarks"  class="form-control potableremarks salesmanageTable" value="${remarks}" disabled/></td>
        
            </tr>`;

            counter++;

        }

        jQuery('#purchasetable tbody').append(thisHtml);
        var counter = 1;
        for (let key in purchase_order_details) {
            var item_id = purchase_order_details[key].item_id ? purchase_order_details[key].item_id : "";
            jQuery(`.item_id_${counter}`).val(item_id).change();
            counter++;
        }
    }
    sumSoQty();
    totalAmount();

    srNo();
  
}


function addPartDetail() {

    var thisHtml = `
    <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="purchase_order_detail_id[]" value="0"></td></tr>
            <tr>
    <tr>

    <td>
        <a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
    </td>


    <td class="sr_no"></td>

    <td> <select name="item_id[]" id="item_id" class="chzn-select chzn-done add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select></td>

    <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable POaddtables" tabindex="1" readonly/></td>

    <td><input type="text" name="po_qty[]" id="po_qty" onKeyup="sumSoQty(this)"  class="form-control allow-desimal po_qty" style="width:50px;" disabled/></td>
    
    <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control POaddtables" tabindex="1" readonly/></td>
    
    <td><input type="text" name="del_date[]" class="form-control potabledate salesmanageTable date-picker del_date" disabled/></td>
    
    <td><input type="number" name="rate_unit[]" id="rate_unit" step="0.01" min="0.01" onKeyup="soRateUnit(this)" id="rate_unit" class="form-control potabledate rate_unit salesmanageTable" onblur="formatPoints(this,2)" disabled/></td>
    
    <td><input type="text" name="amount[]" id="amount"  class="form-control salesmanageTable amount potabledate" readonly/></td>
    
    <td><input type="text" name="remarks[]" id="remarks"  class="form-control salesmanageTable potableremarks" disabled/></td>

    </tr>`;
    jQuery('#purchasetable tbody').append(thisHtml);
    srNo();
    sumSoQty();
    totalAmount();

    // Reinitialize date-picker for new elements
    jQuery('.date-picker').datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
    });
}


function getLatestPoNo() {
    jQuery.ajax({
        url: RouteBasePath + "/get-latest_po_no",
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            var stgDrpHtml = `<option value="">Select Ship To</option>`; 
            jQuery('#po_no').removeClass('file-loader');
            if (data.response_code == 1) {         
                jQuery('#po_no').val(data.latest_po_no);
                jQuery('#po_sequence').val(data.number);
                jQuery('#po_date').val(currentDate);
                stgDrpHtml += `<option value="${data.location.id}">${data.location.location_name}</option>`;
                jQuery('#ship_to').append(stgDrpHtml);
                jQuery('#ship_to').val(data.location.id).trigger('liszt:updated');
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

function getItemData(th) {
    let item = th.value;
    console.log(item);
    if (item != "" && item != null) {
        jQuery.ajax({
            url: RouteBasePath + "/get-fitting_item_data?item=" + item,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    // jQuery(th).closest('tr').find("#code").val(data.item.item_code);
                    jQuery(th).parents('tr').find("#code").val(data.item.item_code);
                    jQuery(th).parents('tr').find("#item_id").val(data.item.id);
                    jQuery(th).parents('tr').find("#group").val(data.item.item_group_name);
                    jQuery(th).parents('tr').find("#unit").val(data.item.unit_name);
                    jQuery(th).parents('tr').find("#po_qty").prop('disabled',false);
                    jQuery(th).parents('tr').find(".del_date").prop('disabled',false);
                    jQuery(th).parents('tr').find("#rate_unit").prop('disabled',false);
                    jQuery(th).parents('tr').find("#remarks").prop('disabled',false);
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

function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
    });
}

function removeSoDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            jQuery(th).parents("tr").remove();
            srNo();
            var po_qty = jQuery(th).parents('tr').find('#po_qty').val();
            var po_amt = jQuery(th).parents('tr').find('#amount').val();
            
            if (po_qty && po_amt) {
                var po_total = jQuery('.poqtysum').text();
                var amt_total = jQuery('.amountsum').text();
                if (po_total != "" && amt_total != "") {
                    po_final_total = parseInt(po_total) - parseInt(po_qty);
                    amt_final_total = parseInt(amt_total) - parseInt(po_amt);
                }
            }
            jQuery('.poqtysum').text(po_final_total);
            jQuery('.amountsum').text(amt_final_total);
        }
    });
}

jQuery(document).on('change','.item_id',function(e){
    var selected =jQuery(this).val();
    var thisselected =jQuery(this);
    if(selected) {
        jQuery( jQuery('.item_id').not(jQuery(this)) ).each(function( index ) {
            if(thisselected.val() == jQuery(this).val()){
                jAlert('This Item Is Already Selected.');
                thisselected.replaceWith(`<select name="item_id[]" id="item_id" class="chzn-select chzn-done add_item item_id" onChange="getItemData
                (this)">${productDrpHtml}</select>`);
            }
        });
    }
});


function sumSoQty(th) {
    var total = 0;
    jQuery('.po_qty').map(function () {
        var total1 = jQuery(this).val();

        if (total1 != "") {
            total = parseInt(total) + parseInt(total1);
        }
    });

    total != 0 && total != "" ?    jQuery('.poqtysum').text(total) : jQuery('.poqtysum').text('') ;
    
    if (jQuery(th).parents('tr').length > 0) {
        soRateUnit(jQuery(th).parents('tr'))
    }
}

function soRateUnit(th) {

    let po_qty = jQuery(th).parents('tr').find("#po_qty").val();

    let rateUnit = jQuery(th).parents('tr').find("#rate_unit").val();


    var poUnit = 0;
    if (rateUnit != "" && po_qty != "") {
        poUnit = parseInt(po_qty) * parseFloat(rateUnit);
    }

    if (poUnit != 0) {
        jQuery(th).parents('tr').find("#amount").val(formatAmount(poUnit));
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
        jQuery('.amountsum').text(formatAmount(total_amount));
    } else if (amount != 0) {
        jQuery('.amountsum').text('');
    } else {
        jQuery('.amountsum').text(0);
    }
}

function getContactPerson(){
    var supplier_id = jQuery('#supplier_id').val();
     jQuery.ajax({
        url: RouteBasePath + "/get-supplier_contact_person?supplier_id=" + supplier_id,
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jQuery('#person').val(data.contact_person.contact_person);
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

jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseInt(value) >= 0.01;
});
// store and update purchase order and purchase order details
var validator = jQuery("#PurchaseOrderForm").validate({
    onclick : false,
    rules: {
        
    po_sequence: {
            required: true
   },
    supplier_id: {
        required: true
    },
    po_date :{
        required: true,
        date_check: true,
        dateFormat: true
    },
    ref_date:{
        dateFormat: true
    },
   'item_id[]': {
        required: true
    },
    'po_qty[]': {
        required: function (e) {
            if (jQuery(e).prop('disabled')) {
                return false;
            } else {
                return true;
            }
        },
        notOnlyZero: '0',
        },
    'rate_unit[]': {
        required: function (e) {
            if (jQuery(e).prop('disabled')) {
                return false;
            } else {
                return true;
            }
        },
        notOnlyZero: '0',
        },
    },

messages: {

    po_sequence: {
        required:"Please Enter PO No."
    },
    supplier_id: {
        required:"Please Select Supplier"
    },
    po_date :{
        required: "Please Enter PO Date.",
    },
    'item_id[]': {
        required: "Please Select Item"
    }, 
    'po_qty[]' :
    {
        required: "Please Enter PO Qty.",
        notOnlyZero: 'Please Enter A Value Greater Than Or Equal To 1.'
    },
   'rate_unit[]' :
    {
        required: "Please Enter Rate Per Unit.",
        notOnlyZero: 'Please Enter A Value Greater Than Or Equal To 1.'
    },
   
    },
    errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
    },

// },

submitHandler: function(form) {

    jQuery('#purchase_button').prop('disabled',true);
   
    var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-purchase_order" : RouteBasePath + "/store-purchase_order";
    jQuery.ajax({
        url: formUrl,
        type: 'POST',
        data: jQuery('#PurchaseOrderForm').serialize(),
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if(data.response_code == 1){
                if (formId != null && formId != "") {
                    toastPreview(data.response_message, redirectFn, prePO);
                    // toastSuccess(data.response_message, redirectFn);
                    function redirectFn() {
                        window.location.href = RouteBasePath + "/manage-purchase_order";
                    };
                    function prePO() {
                        id = btoa(data.id);
                        window.location.reload();
                        // window.location.href = RouteBasePath + "/preview-puchase_order/" + id;
                    }
                } else {
                    // toastSuccess(data.response_message, redirectFn);
                    toastPreview(data.response_message, redirectFn, prePO);
                    function redirectFn() {
                        window.location.reload();
                    }
                    function prePO() {
                        id = btoa(data.id);
                        window.location.reload();
                        // window.location.href = RouteBasePath + "/preview-puchase_order/" + id;
                    }
                    jQuery('#purchase_button').prop('disabled',false);
                }
            }else{
                toastError(data.response_message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown){
            var errMessage = JSON.parse(jqXHR.responseText);
            if(errMessage.errors){
                validator.showErrors(errMessage.errors);
            }else if(jqXHR.status == 401){
                toastError(jqXHR.statusText);
            }else{
                toastError('Something went wrong!');
                console.log(JSON.parse(jqXHR.responseText));
            }
        }
    });
}
});
// end store and update

// get Last Supplier Details
function getLastSupplierDetails(){
    s_id = jQuery('#supplier_id').val();
    jQuery.ajax({
        url: RouteBasePath + "/get-last_supplier_details?supplier_id=" + s_id,
        type: 'GET',
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                jQuery('#pf_charge').val(data.last_data.pf_charge);
                jQuery('#freight').val(data.last_data.frieght).trigger('liszt:updated');
                jQuery('#gst').val(data.last_data.gst);
                jQuery('#payment_terms').val(data.last_data.payment_terms);
                jQuery('#sp_notes').val(data.last_data.special_notes);
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




jQuery('#po_sequence').on('change', function () {      
    let val = jQuery(this).val();
    

    var subBtn = jQuery(document).find('.stdform').find('.formwrapper button').text();



    if (subBtn == "submit" || subBtn == "Submit") {

        subBtn = jQuery(document).find('.stdform').find('.formwrapper button');        
    }


    if (val != undefined) {
        
        if (val > 0 == false) {            
            jAlert('Please Enter Valid PO. No.');
            jQuery('#po_sequence').parent().parent().parent('div.control-group').addClass('error');
            jQuery('#po_sequence').focus();
            jQuery('#po_sequence').val('');

        } else {
            jQuery(subBtn).prop('disabled', true);

           
            jQuery('#po_sequence').parent().parent().parent('div.control-group').removeClass('error');

            var  urL = RouteBasePath + "/check-po_no_duplication?for=add&po_sequence=" + val;

            if (formId !== undefined) { //if form is edit
                urL = RouteBasePath + "/check-po_no_duplication?for=edit&po_sequence=" + val + "&id=" + formId;
            }

            jQuery.ajax({

                url: urL,
                type: 'GET',
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    jQuery('#po_sequence').removeClass('file-loader');
                    if (data.response_code == 0) {

                        toastError(data.response_message);
                        jQuery('#po_sequence').parent().parent().parent('div.control-group').addClass('error');
                        jQuery('#po_sequence').focus();
                        jQuery('#po_sequence').val('');

                    } else {                        
                        jQuery('#po_sequence').parent().parent().parent('div.control-group').removeClass('error');
                        jQuery('#po_no').val(data.latest_po_no);
                        jQuery('#po_sequence').val(val);
                    }
                    jQuery(subBtn).prop('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#po_sequence').removeClass('file-loader');
                    toastError('Somthing want wrong!')

                }
            });
        }
    } else {
        jQuery('#po_no').val('');
        jQuery('#po_sequence').val('');
    }
});
