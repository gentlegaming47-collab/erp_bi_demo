





if (getItem.length) {
    var productDrpHtml = `<option value="">Select Item</option>`;
    var item_id = ``;
    for (let indx in getItem[0]) {
        
        productDrpHtml += `<option value="${getItem[0][indx].id}">${getItem[0][indx].item_name} </option>`;
        item_id += `data-rate="${getItem[0][indx].id}" `;
    }
    
}



var formId = jQuery('#addPriceListForm').find('input:hidden[name="id"]').val();
if(formId == '' || formId == null){
    addPartDetail();
}
// store and update process
if (formId != undefined && formId != '') { //if form is  edit

    jQuery(document).ready(function () {

        setTimeout(() => {
            jQuery('#customer_group_id').trigger('liszt:activate');
        }, 100);
        jQuery.ajax({
            url: RouteBasePath + "/get-price_list/" + formId,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                // for(let customerId in data.price_list_details)
                // {                    
                    jQuery('#customer_group_id').val(data.price_list.customer_group_id).trigger('liszt:updated');  
                    
                    getSalesRate(data.price_list.customer_group_id)   ;
                    // jQuery('#customer_group_id').val(data.price_list_details[customerId].customer_group_id).trigger('liszt:updated');     
                // }
                
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
            jQuery('#pricelisttable').DataTable();
    });
} else { //for Add
    jQuery(document).ready(function () {
        setTimeout(() => {
            jQuery('#customer_group_id').trigger('liszt:activate');
        }, 100);
        // getLatestSoNo();

        // getCustomer();

        // addPartDetail();

    });

}

// edit data get


// store and update
    
// jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
//     return this.optional(element) || parseInt(value) >= 0;
// });  
jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
    // console.log(this.optional(element));
    return this.optional(element) || parseInt(value) > 0;
});  

var validator = jQuery("#addPriceListForm").validate({
    onclick : false,
    rules: {

    customer_group_id: {
        required: true
    },
   'item_id[]': {
        required: true
    },
    "sales_rate[]": {
        // required: function (e) {
        //     if (jQuery(e).prop('disabled')) {
        //         return false;
        //     } else {
        //         return true;
        //     }
        // },
        // notOnlyZero: '0',
        },
    },

messages: {

    customer_group_id: {
        required:"Please Select Customer Group"
    },
    'item_id[]': {
        required: "Please Select Item"
    }, 
//    'sales_rate[]' :
//     {
//         required: "Please Enter Sales Rate.",
//         notOnlyZero: 'Please Enter A Value Greater Than Or Equal To 1.'
//     }
    },
    errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
    },

// },

submitHandler: function(form) {
    
    let storeArray = [];
    // jQuery("#pricelisttable tbody tr").each(function(e){
    //     salesRateValue = jQuery(this).find('input[name="sales_rate[]"]').val();
        
    //     if(salesRateValue != null && salesRateValue != "")
    //     {
    //         storeArray.push(salesRateValue);
    //     }
    // })
    table.rows().every(function (rowIdx, tableLoop, rowLoop) {
      var data = this.node();
        var itemId = jQuery(data).find('input[name="item_id[]"]').val();
         var salesRate = jQuery(data).find('input[name="sales_rate[]"]').val();

       if(salesRate != null && salesRate != "")
        {
            storeArray.push({
                item_id: itemId,
                sales_rate: salesRate
            });
        }
    });

    if(formId == undefined){
        if(storeArray.length < 1)
        {
            toastError(" Insert At Least One Sales Rate");
            return false;
        }  
    }
    
     let data = new FormData(document.getElementById('addPriceListForm'));
     let formValue = Object.fromEntries(data.entries());


    formValue = Object.assign(formValue, { 'price_list_details': JSON.stringify(storeArray) });
    var formdata = new URLSearchParams(formValue).toString();

    var formUrl = formId != undefined && formId != '' ? RouteBasePath + "/update-price_list" : RouteBasePath + "/store-price_list";
    
    // console.log(formUrl);
    // return false;
    jQuery.ajax({
        url: formUrl,
        type: 'POST',
        data: formdata,
        headers: headerOpt,
        dataType: 'json',
        processData: false,
        success: function (data) {
            if(data.response_code == 1){
                if (formId !== undefined) {
                    toastSuccess(data.response_message, redirectFn);
                    function redirectFn() {
                        window.location.href = RouteBasePath + "/manage-price_list";
                    };
                } else {
                    toastSuccess(data.response_message, redirectFn);
                    function redirectFn() {
                        window.location.reload();
                    }
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

function addPartDetail() {

    var thisHtml = `
    <tr style="display:none;"><td class="colspan=10"><input type="hidden" name="price_list_detail_id[]" value="0"></td></tr>
            <tr>
    <tr>

    <td>
        <a onclick="removeSoDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
    </td>


    <td class="sr_no"></td>

    <td> <select name="item_id[]" id="item_id" class="chzn-select chzn-done add_item item_id" onChange="getItemData(this)">${productDrpHtml}</select></td>

    <td><input type="text" name="code[]" id="code"  class="form-control salesmanageTable" tabindex="-1" readonly/></td>

    <td><input type="text" name="sales_rate[]" id="sales_rate"  class="form-control allow-desimal so_qty" style="width:50px;" disabled/></td>
    
    <td><input type="text" name="unit[]" id="unit" style="width:50px;" class="form-control" tabindex="-1" readonly/></td>
    
    <td><input type="text" name="group[]" id="group"  class="form-control salesmanageTable" tabindex="-1" readonly/></td>

    </tr>`;
    
    srNo();

    


}

function getItemData(th) {
    c_id = jQuery('#customer_group_id').val();
    let item = th.value;
    if (item != "" && item != null) {
        jQuery.ajax({
            url: RouteBasePath + "/get-fitting_any_item_data?item=" + item + '&cust_id=' + c_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    jQuery(th).closest('tr').find("#code").val(data.item.item_code);
                    jQuery(th).closest('tr').find("#item_id").val(data.item.id);
                    jQuery(th).closest('tr').find("#group").val(data.item.item_group_name);
                    jQuery(th).closest('tr').find("#unit").val(data.item.unit_name);
                    jQuery(th).closest('tr').find("#sales_rate").attr('data-rate',data.item.id);
                    if(data.sales_rate != null){
                        jQuery(th).closest('tr').find("#sales_rate").val(data.sales_rate.sales_rate).prop('disabled',false);
                    }else{
                        jQuery(th).closest('tr').find("#sales_rate").val(data.item.sales_rate).prop('disabled',false);
                    }
                } else {
                    jQuery('#code').val('');
                    jQuery('#item_id').val('');
                    jQuery('#group').val('');
                    jQuery('#unit').val('');
                    jQuery('#sales_rate').val('');
                }
            },
        });
    }
}

function removeSoDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {


        if (r === true) {
            jQuery(th).closest("tr").remove();
            srNo();
            var so_qty = jQuery(th).closest('tr').find('#sales_rate').val();
            var so_amt = jQuery(th).closest('tr').find('#amount').val();
            
            if (so_qty && so_amt) {
                var so_total = jQuery('.soqtysum').text();
                var amt_total = jQuery('.amountsum').text();
                if (so_total != "" && amt_total != "") {
                    so_final_total = parseInt(so_total) - parseInt(so_qty);
                    amt_final_total = parseInt(amt_total) - parseInt(so_amt);
                }
                console.log("so_final_total",so_final_total);
                console.log("amt_final_total",amt_final_total);
            }
            jQuery('.soqtysum').text(so_final_total);
            jQuery('.amountsum').text(amt_final_total);
        }
    });
}

function srNo() {
    jQuery('.sr_no').map(function (i, e) {
        jQuery(this).text(i + 1);
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

function fillPLTable(price_list_details) {
    
    if (price_list_details.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in price_list_details) {

            var sr_no = counter;
            var price_list_details_id = price_list_details[key].pld_id ? price_list_details[key].pld_id : "";
     
            var item_id = price_list_details[key].item_id ? price_list_details[key].item_id : "";
            var item_code = price_list_details[key].item_code ? price_list_details[key].item_code : "";
            var item_group_name = price_list_details[key].item_group_name ? price_list_details[key].item_group_name : "";
            var sales_rate = price_list_details[key].sales_rate ? price_list_details[key].sales_rate : "";
            var unit_name = price_list_details[key].unit_name ? price_list_details[key].unit_name : "";


             thisHtml += `
             <tr>

             <input type="hidden" name="mid[]"  value={{ $key }}>
           
             <td><a onclick="removePriceListDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>

             <td> <input type="text"  class="input-mini only-numbers" name="sales_rate[]" value="${sales_rate}"> </td>    

         </tr>
                `;

            counter++;
            jQuery('#customer_group_id').val(price_list_details[key].customer_group_id).trigger('liszt:updated');

        }

        jQuery('#pricelisttable tbody').append(thisHtml);
        var counter = 1;
        for (let key in price_list_details) {
            var item_id = price_list_details[key].item_id ? price_list_details[key].item_id : "";
            jQuery(`.item_id_${counter}`).val(item_id).change();
            counter++;
        }
    }
}



// function getSalesRate(th) {    
        
//     jQuery("#pricelisttable tbody tr").find('input[name="sales_rate[]"]').val("");

//     let c_id = jQuery('#customer_group_id').val();

//     if (c_id != "" && c_id != null) {
//         jQuery.ajax({
//             url: RouteBasePath + "/getStockQty?&cust_id=" + c_id,
//             type: 'GET',
//             headers: headerOpt,
//             dataType: 'json',
//             processData: false,
//             success: function (data) {
//                 if (data.response_code == 1) {
//                     for(let salesrate in data.salesRate)
//                     {
//                         jQuery("#pricelisttable tbody tr").each(function(e){                              
//                             var qty = jQuery(this).find('input[name="item_id[]"]').val();
                            
//                             if(qty == data.salesRate[salesrate].item_id )
//                             {                      
//                                 jQuery(`#sales_rate_${qty}`).val(data.salesRate[salesrate].sales_rate);
//                             }                            
//                         })
                       
//                     }
                   

                    
//                 } else {
//                     jQuery('#code').val('');
//                     jQuery('#item_id').val('');
//                     jQuery('#group').val('');
//                     jQuery('#unit').val('');
//                     jQuery('#sales_rate').val('');
//                 }
//             },
//         });
//     }
// }

function getSalesRate(th) {
    let c_id = jQuery('#customer_group_id').val();

    if (c_id != "" && c_id != null) {
        jQuery.ajax({
            url: RouteBasePath + "/getStockQty?&cust_id=" + c_id,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {

                    var tbltr = '';
                    if (data.item.length > 0) {

                        for (let indx in data.item) {
                            tbltr += `<tr>
                                    <td><a onclick="removePriceListDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>                                  
                                    <td> <input type="hidden" name="item_id[]" id="item_id_${indx}"  class="itemClass" value="${data.item[indx].id}"> ${data.item[indx].item_name} </td>
                                    <td> <input type="hidden" name="code[]" id="code" value="${data.item[indx].id}"> ${data.item[indx].item_code} </td>
                                    <td> <input type="hidden" name="code[]" id="code" value="${data.item[indx].id}">${data.item[indx].item_group_name}</td>                                        
                                    <td><input type="text" name="sales_rate[]" id="sales_rate_${data.item[indx].id}"  class="input-large auto-suggest isNumberKey" autocomplete="nope" onblur="formatPoints(this,2)" /> </td>                                   
                                    <td> <input type="hidden" name="unit[]" id="unit" value="${data.item[indx].id}"> ${data.item[indx].unit_name}</td>                                  
                                    </tr>`;
                        }

                        if (jQuery.fn.DataTable.isDataTable('#pricelisttable')) {
                            jQuery('#pricelisttable').DataTable().destroy();
                        }

                        jQuery('#pricelisttable').append(tbltr);
                        table = jQuery('#pricelisttable').DataTable({
                            responsive: true,
                            pageLength: 50,
                            "oLanguage": {
                                "sSearch": "Search :"
                            },                           
                        });
                    }

                    
                    // Access the DataTable instance
                    var table = jQuery("#pricelisttable").DataTable();

                    // Clear previous sales rate values
                    table.rows().every(function() {
                        var row = this.node();
                        jQuery(row).find('input[name="sales_rate[]"]').val("");
                    });

                    // Iterate through each sales rate received
                    data.salesRate.forEach(function(salesrate) {
                        // Use DataTables API to iterate through all rows
                        table.rows().every(function() {
                            var row = this.node();
                            var itemId = jQuery(row).find('input[name="item_id[]"]').val();

                            if (itemId == salesrate.item_id) {
                                // Use jQuery to set the value correctly
                                jQuery(row).find(`#sales_rate_${itemId}`).val(salesrate.sales_rate != null ? parseFloat( salesrate.sales_rate).toFixed(2) : "");
                            }
                        });
                    });
                } else {
                    // Clear form fields if response code is not 1
                    jQuery('#code').val('');
                    jQuery('#item_id').val('');
                    jQuery('#group').val('');
                    jQuery('#unit').val('');
                    jQuery('#sales_rate').val('');
                }
            },
        });
    }
}


// jQuery('#pricelisttable tbody').on( 'click', '#del_a', function () {
//     // console.log(data);
//     jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
//         if(r === true){
//             jQuery.ajax({
//                 url: "{{ route('delete-price_list') }}",
//                 type: 'GET',
//                 data: "id="+data["pl_id"],
//                 headers: headerOpt,
//                 dataType: 'json',
//                 processData: false,
//                 success: function (data) {
//                     if(data.response_code == 1){
//                         // toastSuccess(data.response_message);
//                         jAlert(data.response_message);
//                         table.row(jQuery(this)).draw(false);
//                     }else{
//                         // toastError(data.response_message);
//                         jAlert(data.response_message);
//                     }
//                 },
//                 error: function (jqXHR, textStatus, errorThrown){
//                     if(jqXHR.status == 401){
//                         // toastError(jqXHR.statusText);
//                         jAlert(jqXHR.statusText);
//                     }else{
//                        // toastError('Somthing went wrong!');
//                         jAlert('Somthing went wrong!');
//                     }
//                     console.log(JSON.parse(jqXHR.responseText));
//                 }
//         });
//         }
//     });
// });



function removePriceListDetails(th) {

    var id  = jQuery(th).closest('tr').find('input[name="item_id[]"]').val();
    
    
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {

        if(r==1)
        {
            jQuery.ajax({
                url: RouteBasePath + "/delete-price_list?&item_id=" + id,
                type: 'GET',                   
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if(data.response_code == 1){                    
                        jAlert(data.response_message);
                        // jQuery(th).closest('tr').remove();
                        var table = jQuery('#pricelisttable').DataTable();
                        var row = table.row(jQuery(th).parents('tr'));
                        row.remove().draw();
                        srNo();
                    }else{
                        // toastError(data.response_message);
                        jAlert(data.response_message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown){
                    if(jqXHR.status == 401){
                        // toastError(jqXHR.statusText);
                        jAlert(jqXHR.statusText);
                    }else{
                       // toastError('Somthing went wrong!');
                        jAlert('Somthing went wrong!');
                    }
                    console.log(JSON.parse(jqXHR.responseText));
                }
        });

        }

      
    });
}

  
jQuery(".allow_decimal").on("input", function (evt) {
    var self = jQuery(this);
    self.val(self.val().replace(/[^0-9.]/g, ''));
    if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
        evt.preventDefault();
    }
});
 


// all check box 
// jQuery('#checkall').click(function(){
//             console.log("Called") ;
//     if(jQuery(this).is(':checked')){
    
//         jQuery("#pricelisttable").find("[id^='material_ids_']:not(.in-use)").prop('checked',true).trigger('change');
    
//         jQuery("#pricelisttable").find("[id^='raw_material_qty']:not(.in-use)").prop('disabled',false).trigger('change');
    
    
//     }else{
    
//         jQuery("#pricelisttable").find("[id^='material_ids_']:not(.in-use)").prop('checked',false).trigger('change');
    
//           jQuery("#pricelisttable").find("[id^='raw_material_qty']:not(.in-use)").prop('disabled',true).trigger('change');
    
//           jQuery("#pricelisttable").find("[id^='raw_material_qty']:not(.in-use)").val("");
    
          
    
//     }
    
// });



// jQuery("[id^='material_ids_']").click(function(){
    
//     if(jQuery(this).prop('checked')== true){        
//         jQuery(this).closest('tr').find("#stock_qty").prop("disabled", false);          
//         jQuery(this).closest('tr').find("#stock_qty").select();          
//     } else {
//         jQuery(this).closest('tr').find("#stock_qty").prop("disabled", true);        
//     }
// });


/* jQuery(document).ready(function(){
    jQuery(document).on('change','#item_id',function(){
        jQuery('#sales_rate').prop('checked',false);   
    });
}); */ 

// jQuery("[id^='item_id']").click(function(){
// if(jQuery(this).prop('checked')== true){
//     jQuery(this).closest('tr').find("#sales_rate").prop("disabled", false);          
//     jQuery(this).closest('tr').find("#sales_rate").select();          
// } else {
//     jQuery(this).closest('tr').find("#sales_rate").prop("disabled", true);
//     jQuery(this).closest('tr').find("#sales_rate").val("");
    
// }
// });