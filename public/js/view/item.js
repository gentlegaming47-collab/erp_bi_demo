

let itemHiddenId = jQuery('#commonItemForm').find('input:hidden[name="id"]').val();




// custom jQuery validator

// jQuery.validator.addMethod("verifypin", function (value, element) {
//     function pincode(val) {
//         let format = /[`!@#$%^&*()_,\=\[\]{};':"\\|.<>\/?~]/;
//         if (format.test(value) == true) {
//             return false;
//         } else {
//             return true;
//         }
//     }
//     return this.optional(element) || pincode(value);
// }, "only 0-9 and ('-','+') allowed");

var contact_data = [];
function removeContactDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            let formIndx = jQuery(th).closest("tr").find('input[name="form_indx"]').val();
            removeFormObj(formIndx);
            jQuery(th).closest("tr").remove();
        }
    });
}
function editContactDetails(th) {
    let formIndx = jQuery(th).closest("tr").find('input[name="form_indx"]').val();
    let rawIndx = jQuery(th).closest('tr').index();
    fillContactForm(formIndx, rawIndx);
}

jQuery.validator.addMethod("minstock", function (value, element) {
    function minstocks(val) {
        var format = /[`!@#$%^&*()_,\=\[\]{};':"\\|.<>\/?~]/;
        if (format.test(value) == true) {
            return false;
        } else {
            return true;
        }
    }
    return this.optional(element) || minstocks(value);
}, "only 0-9 and ('-','+') allowed");


jQuery.validator.addMethod("maxstock", function (value, element) {
    function maxstocks(val) {
        var format = /[`!@#$%^&*()_,\=\[\]{};':"\\|.<>\/?~]/;
        if (format.test(value) == true) {
            return false;
        } else {
            return true;
        }
    }
    return this.optional(element) || maxstocks(value);
}, "only 0-9 and ('-','+') allowed");

jQuery.validator.addMethod("reorder", function (value, element) {
    function reorders(val) {
        var format = /[`!@#$%^&*()_,\=\[\]{};':"\\|.<>\/?~]/;
        if (format.test(value) == true) {
            return false;
        } else {
            return true;
        }
    }
    return this.optional(element) || reorders(value);
}, "only 0-9 and ('-','+') allowed");


jQuery.validator.addMethod("rate", function (value, element) {
    function rates(val) {
        var format = /[`!@#$%^&*()_,\=\[\]{};':"\\|.<>\/?~]/;
        if (format.test(value) == true) {
            return false;
        } else {
            return true;
        }
    }
    return this.optional(element) || rates(value);
}, "only 0-9 and ('-','+') allowed");


jQuery(".allow_decimal").on("input", function (evt) {
    var self = jQuery(this);
    self.val(self.val().replace(/[^0-9.]/g, ''));
    if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
        evt.preventDefault();
    }
});


var headerOpt = { 'Authorization': 'Bearer {{ Auth::user()->auth_token }}' };

jQuery(document).ready(function () {
    jQuery('#wt_pc').val('0.000');

    jQuery("#require_raw_material_mapping").on("change", function () {
        let rawMaterialValue = jQuery("#require_raw_material_mapping").val();
        if (rawMaterialValue == "Yes")
            jQuery("#fitting_item").val("No").trigger("liszt:updated").prop({ tabindex: -1 }).attr('readonly', true);


        // jQuery("#fitting_item").val("No").attr("readonly", true).trigger("liszt:updated");
        else
            jQuery("#fitting_item").attr("readonly", false);
    });

    jQuery("#require_raw_material_mapping").on("change", function () {
        let rawMaterialValue = jQuery("#require_raw_material_mapping").val();
        if (rawMaterialValue == "Yes") {
            jQuery('#show_item_in_print').val("No").trigger('liszt:updated');
            jQuery('div#assembly_hide').show();

        } else {
            jQuery('div#assembly_hide').hide();
        }



    });


    if (itemHiddenId != "" && itemHiddenId != undefined) {


        // get village data at edit time

        jQuery.ajax({

            url: RouteBasePath + "/get-items/" + itemHiddenId,

            type: 'GET',

            headers: headerOpt,

            dataType: 'json',

            processData: false,

            success: function (data) {


                if (data.response_code == 1) {
                    serviceItem();
                    // console.log(data);
                    jQuery('#item_name').val(data.item.item_name);

                    jQuery('#item_group_id').val(data.item.item_group_id).trigger("liszt:updated");

                    jQuery('#item_code').val(data.item.item_code);

                    if (data.in_use == true) {
                        jQuery("#fitting_item").val(data.item.fitting_item).prop({ tabindex: -1 }).attr('readonly', true);

                        // if (data.item.require_raw_material_mapping == 'No') {
                        //     jQuery("#require_raw_material_mapping").val(data.item.require_raw_material_mapping);
                        // } else {
                        //     jQuery("#require_raw_material_mapping").val(data.item.require_raw_material_mapping).prop({ tabindex: -1 }).attr('readonly', true);
                        // }
                        // jQuery("#fitting_item").val(data.item.fitting_item).attr("readonly", true);
                        // jQuery("#require_raw_material_mapping").val(data.item.require_raw_material_mapping).attr("readonly", true);
                    }

                    // if(data.itemdata == "no")
                    // {
                    //     jQuery("#fitting_item").val("No").attr("readonly", true);
                    //     jQuery("#require_raw_material_mapping").val("No").attr("readonly", true);
                    // }


                    //jQuery('#item_code').prop( {tabindex : -1}).attr('readonly', true);
                    // jQuery('#item_code').attr('readonly', true);
                    jQuery('#unit_id').val(data.item.unit_id).trigger("liszt:updated");
                    jQuery('#qc_required').val(data.item.qc_required).trigger("liszt:updated");

                    jQuery('#print_dispatch_plan').val(data.item.print_dispatch_plan).trigger("liszt:updated");
                    jQuery('#own_manufacturing').val(data.item.own_manufacturing).trigger("liszt:updated");
                    jQuery('#dont_allow_req_msl').val(data.item.dont_allow_req_msl).trigger("liszt:updated");
                    jQuery('#service_item').val(data.item.service_item).trigger("liszt:updated");
                    setTimeout(() => {
                        jQuery('#show_item_in_print').val(data.item.show_item_in_print).trigger("liszt:updated");
                    }, 300);

                    secondaryUnit();


                    // if (data.item.print_dispatch_plan == 'Yes') {
                    //     jQuery('#print_dispatch_plan').trigger('click');
                    // }

                    // if (data.item.own_manufacturing == 'Yes') {
                    //     jQuery('#own_manufacturing').trigger('click');
                    // }

                    // if (data.item.dont_allow_req_msl == 'Yes') {
                    //     jQuery('#dont_allow_req_msl').trigger('click');
                    // }

                    // if (data.item.service_item == 'Yes') {
                    //     jQuery('#service_item').trigger('click');
                    // }

                    jQuery('#status').val(data.item.status).trigger("liszt:updated");
                    jQuery('#allow_partial_dispatch').val(data.item.allow_partial_dispatch).trigger("liszt:updated");
                    jQuery('#wt_pc').val(data.item.wt_pc != null ? parseFloat(data.item.wt_pc).toFixed(3) : "0.000");
                    if (data.in_use == true) {
                        jQuery('#own_manufacturing').prop('readonly', true);
                    }

                    jQuery('#min_stock_qty').val(data.item.min_stock_qty != null ? parseFloat(data.item.min_stock_qty).toFixed(3) : "");

                    jQuery('#max_stock_qty').val(data.item.max_stock_qty != null ? parseFloat(data.item.max_stock_qty).toFixed(3) : "");

                    jQuery('#re_order_qty').val(data.item.re_order_qty != null ? parseFloat(data.item.re_order_qty).toFixed(3) : "");


                    // jQuery('#re_order_qty').val(parseFloat(data.item.re_order_qty).toFixed(3));

                    jQuery('#hsn_code_id').val(data.item.hsn_code).trigger("liszt:updated");

                    //jQuery('#rate_per_unit').val(data.item.rate_per_unit);

                    jQuery('#rate_per_unit').val(data.item.rate_per_unit != null ? parseFloat(data.item.rate_per_unit).toFixed(3) : "");

                    // jQuery('#rate_per_unit').val(parseFloat(data.item.rate_per_unit).toFixed(2));


                    jQuery('#old_require_raw_material_mapping').val(data.item.require_raw_material_mapping);

                    jQuery('#require_raw_material_mapping').val(data.item.require_raw_material_mapping).trigger('liszt:updated').change();

                    if (data.item.require_raw_material_mapping == "Yes") {
                        jQuery("#fitting_item").val(data.item.fitting_item).trigger("liszt:updated").prop({ tabindex: -1 }).attr('readonly', true);
                        // jQuery("#fitting_item").val(data.item.fitting_item).attr("readonly", true).trigger("liszt:updated");
                    } else {
                        jQuery('#commonItemForm').find('#fitting_item').val(data.item.fitting_item).trigger('liszt:updated');
                    }

                    disabledDropdownVal();

                    if (data.item.secondry_unit_in_use == true) {
                        jQuery('#secondary_unit').val(data.item.secondary_unit).trigger("liszt:updated").attr('readonly', true);
                    } else {
                        jQuery('#secondary_unit').val(data.item.secondary_unit).trigger("liszt:updated");
                    }
                    if (data.item.secondary_unit == 'Yes') {
                        // jQuery('#secondary_unit').trigger('click');
                        jQuery('div#hide').show();
                        jQuery('div#item_hide').show();
                        jQuery('#wt_pc').attr('readonly', true);
                        // jQuery('#qty').val(parseFloat(data.item.qty).toFixed(3));
                        jQuery('#second_unit').val(data.item.second_unit).trigger('liszt:updated');

                        fillItemDetailTable(data.item_data);

                    }
                    // if (data.contact.length > 0 && !jQuery.isEmptyObject(data.contact)) {
                    //     for (let ind in data.contact) {
                    //         contact_data.push(data.contact[ind]);
                    //     }
                    //     fillContactTable();
                    // }


                } else {
                    jAlert(data.response_message, 'Alert Dialog', function (r) {
                        window.location.href = "/manage-item";
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


    // Store or Update
    jQuery.validator.addMethod("Zero", function (value, element, param) {
        return this.optional(element) || parseFloat(value) > 0.000;
    });

    jQuery.validator.addMethod("validateUnit", function (value, element, params) {
        // var checkboxChecked = jQuery("#secondary_unit").prop('checked');
        var checkboxChecked = jQuery("#secondary_unit").val();
        if (checkboxChecked == 'Yes') {
            if (element.id === "second_unit") {
                return value !== "";
            }
        }
        return true;
    }, "Please Select Unit.");

    jQuery.validator.addMethod("validateQty", function (value, element, params) {
        // var checkboxChecked = jQuery("#secondary_unit").prop('checked');
        var checkboxChecked = jQuery("#secondary_unit").val();
        if (checkboxChecked == 'Yes') {
            // if (checkboxChecked) {
            if (element.id === "qty") {
                return parseFloat(value) > 0.000;
            }
        }
        return true;
    }, "Please Enter Qty.");


    jQuery.validator.addMethod("validateminQty", function (value, element, params) {
        var checkboxChecked = jQuery("#dont_allow_req_msl").prop('checked');
        if (checkboxChecked) {
            if (element.id === "min_stock_qty") {
                return parseFloat(value) > 0.000;
            }
        }
        return true;
    }, "Please Enter Min. Stock Qty.");



    var validator = jQuery("#commonItemForm").validate({

        rules: {
            onkeyup: false,
            onfocusout: false,

            item_name: {

                required: true,

                maxlength: 255

            },
            item_group_id: {

                required: true,

            },
            item_code: {

                required: true,

            },
            unit_id: {

                required: true,

            },
            min_stock_qty: {

                // minstock: true,
                // required: true
                Zero: true,
                validateminQty: true,

            },
            // hsn_code: {
            //     required: true
            // },
            max_stock_qty: {

                //maxstock: true,
                //  required: true
                Zero: true

            },
            // re_order_qty: {

            //     required: true,

            // },
            // rate_per_unit: {

            //     // rate: true,

            //     required: true

            // },
            require_raw_material_mapping: {

                required: true,

            },
            fitting_item: {

                required: true,

            },
            // qty: {
            //     validateQty: true
            // },
            second_unit: {
                validateUnit: true
            },
            'secondary_qty[]': {
                required: true
            }
        },

        messages: {

            item_name: {

                required: "Please Enter Item",

                maxlength: "Maximum 255 Characters Allowed"

            },
            item_group_id: {

                required: "Please Select Item Group",

            },
            item_code: {

                required: "Please Enter Item Code",

            },
            unit_id: {

                required: "Please Select Unit",

            },
            min_stock_qty: {

                //     required: "Please Enter Minimum Qty.",
                Zero: "Please Enter Min Stock Qty. Greater than 0.",

            },
            max_stock_qty: {

                //     required: "Please Enter Maximum Limit.",
                Zero: "Please Enter Max Stock Limit Greater than 0.",

            },
            // re_order_qty: {

            //     required: "Please Enter Re Order Qty.",

            // },
            // hsn_code: {

            //     required: "Please Enter HSN Code",

            // },
            // rate_per_unit: {

            //     required: "Please Enter Rate Per Unit",

            // },
            require_raw_material_mapping: {

                required: "Please Select Item Mapping",

            },
            fitting_item: {

                required: "Please Select Fitting Item",

            },
            // qty: {
            //     validateQty: "Please Enter Qty. Greater than 0.000"
            // },
            second_unit: {
                validateUnit: "Please Select Secondary Unit"
            },
            'secondary_qty[]': {
                required: "Please Enter Qty."
            },


        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {

            var formdata = jQuery('#commonItemForm').serialize();

            let itemName = jQuery('#item_name').val();

            // Min Max Validations
            var minStockQty = parseFloat(jQuery('#min_stock_qty').val());
            var maxStockQty = parseFloat(jQuery('#max_stock_qty').val());

            if (maxStockQty <= minStockQty) {
                jAlert("Max. Stock Limit must be greater than Min. Stock Qty.");
                jQuery('#max_stock_qty').addClass('error');
                return false;
            } else {
                jQuery('#max_stock_qty').removeClass('error');
            }

            // if (minStockQty !== "" && isNaN(minStockQty) === false) {
            //     if (maxStockQty === "" || isNaN(maxStockQty) === true) {
            //         jAlert("Please Enter Max. Stock Limit");
            //         jQuery('#max_stock_qty').addClass('error'); 
            //         return;
            //     }else{
            //         jQuery('#max_stock_qty').removeClass('error'); 
            //     }
            // }

            var second_unit = jQuery('#secondary_unit').val();
            if (second_unit == 'Yes') {
                var rows = jQuery("#contactTable tbody tr").not("#noContact");

                if (rows.length === 0) {
                    jAlert("Please Add At Least One Item Detail.");
                    return false;
                }

            }

            var seen = [];
            var duplicateFound = false;

            jQuery("input[name='secondary_qty[]']").each(function () {
                var val = jQuery(this).val().trim();
                if (val !== "") {
                    if (seen.indexOf(val) !== -1) {
                        duplicateFound = true;
                        jQuery(this).addClass('error');

                        jQuery("input[name='secondary_qty[]']").filter(function () {
                            return jQuery(this).val().trim() === val;
                        }).first().addClass('error');
                    } else {
                        seen.push(val);
                        jQuery(this).removeClass('error');
                    }
                }
            });

            if (duplicateFound) {
                jAlert("Qty. Is Already Exists");
                return false;
            }

            jQuery("input[name='secondary_qty[]']").removeClass('error');

            jQuery('#item_btn').attr('disabled', true);
            let formUrl = itemHiddenId != undefined && itemHiddenId != "" ? RouteBasePath + "/update-item" : RouteBasePath + "/store-item";

            if ((itemName != '' && itemName != undefined)) {
                jQuery.ajax({
                    url: RouteBasePath + "/verify-item?item_name=" + itemName + "&id=" + itemHiddenId,
                    type: 'GET',
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if (data.response_code == 1) {
                            jAlert(data.response_message);
                            toastElement(data.response_message, "#item_name");
                            jQuery('#item_btn').attr('disabled', true);
                        }
                        else {
                            jQuery('#item_btn').attr('disabled', false);
                            // var data = new FormData(document.getElementById('commonItemForm'));
                            // var formValue = Object.fromEntries(data.entries());
                            // let as1;

                            // as1 = Object.assign(formValue, {
                            //     'contacts': JSON.stringify(contact_data),
                            // });

                            // var formdata = new URLSearchParams(as1).toString();


                            jQuery.ajax({

                                url: formUrl,

                                type: 'POST',

                                data: formdata,

                                headers: headerOpt,

                                dataType: 'json',

                                processData: false,

                                success: function (data) {

                                    if (data.response_code == 1) {


                                        if (itemHiddenId != undefined && itemHiddenId != "") {

                                            jAlert(data.response_message, 'Success', function (r) {
                                                window.location.href = RouteBasePath + "/manage-item";
                                            });
                                            // addedVillage(true);
                                        }
                                        else if (itemHiddenId == undefined || itemHiddenId == "") {

                                            function nextFn() {

                                                document.getElementById("commonItemForm").reset();


                                                jQuery('#commonItemForm').find('#item_group_id').val('').trigger('liszt:updated');
                                                jQuery('#commonItemForm').find('#unit_id').val('').trigger('liszt:updated');
                                                jQuery('#commonItemForm').find('#hsn_code_id').val('').trigger('liszt:updated');
                                                jQuery('#commonItemForm').find('#fitting_item').val('').trigger('liszt:updated');
                                                jQuery('#commonItemForm').find('#require_raw_material_mapping').val('').trigger('liszt:updated');
                                                jQuery('#commonItemForm').find('#fitting_item').val('').trigger('liszt:updated');
                                                jQuery('input#item_name').focus();
                                                jQuery('#contactTable tbody').empty();
                                                window.location.reload();
                                                //validator.resetForm();
                                                jQuery('#item_name').focus();

                                                // jQuery('#country_name').val('');

                                            }

                                            toastSuccess(data.response_message, nextFn);
                                            // addedVillage(true);
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
                });
            }

        }

    });


    // Min Max Validation for Qty

    jQuery('#min_stock_qty, #max_stock_qty').on('focusout', function (e) {
        var minStockQty = parseFloat(jQuery('#min_stock_qty').val());
        var maxStockQty = parseFloat(jQuery('#max_stock_qty').val());

        if (minStockQty == 0 || maxStockQty == 0) {
            e.preventDefault();
            if (minStockQty == 0) {
                jAlert("Please Enter Min. Stock Qty. must be greater than 0");
                jQuery('#min_stock_qty').addClass('error');
            } else {
                jQuery('#min_stock_qty').removeClass('error');
            }
            if (maxStockQty == 0) {
                jAlert("Please Enter Max. Stock Limit must be greater than 0");
                jQuery('#max_stock_qty').addClass('error');
            } else {
                jQuery('#max_stock_qty').removeClass('error');
            }
        }

        if (maxStockQty <= minStockQty) {
            jAlert("Max. Stock Limit must be greater than Min. Stock Qty.");
            jQuery('#max_stock_qty').addClass('error');
        } else {
            jQuery('#max_stock_qty').removeClass('error');
        }

        // If Min Stock Qty is not empty, Max Stock Limit must be required
        //   if (minStockQty !== "" && isNaN(minStockQty) === false) {
        //     if (maxStockQty === "" || isNaN(maxStockQty) === true) {
        //         e.preventDefault();  // Prevent form submission
        //         jQuery('#max_stock_qty').addClass('error');  // Add error class to max_stock_qty input
        //         jAlert("Please Enter Max. Stock Limit");

        //     }
        // }
    });
    var contactValidator = jQuery("#itemdetail_form").validate({




        rules: {
            onkeyup: false,
            onfocusout: false,

            item_detail_name: {

                required: true,

                maxlength: 255

            },
        },
        messages: {
            item_detail_name:
            {
                required: "Please Enter Name",
            }
        },
        errorPlacement: function (error, element) {
            jAlert(error.text());
            return false;
        },

        submitHandler: function (form) {


            // var email = jQuery("#contact_form").find(".checkContactEmail").val();

            // if (email != '' && (!validateContactEmail(email))) {
            //     jAlert('Please Enter Valid Email');
            //     jQuery("#popup_ok").click(function () {
            //         setTimeout(() => {
            //             jQuery("#contact_form").find('#contact_email').addClass('error');
            //             jQuery("#contact_form").find('#contact_email').focus();
            //         }, 100);
            //     });
            //     return;
            // } else {
            //     setTimeout(() => {
            //         jQuery("#contact_form").find('#contact_email').removeClass('error');
            //     }, 100);
            // }

            var data = new FormData(document.getElementById('itemdetail_form'));
            var formValue = Object.fromEntries(data.entries());
            var thisModal = jQuery('#itemdetailModal');

            if (formValue.item_detail_name.trim()) {

                var noDuplicate = true;
                if (formValue.form_type == "edit") {
                    jQuery('#contactTable tbody input[name*="item_detail_name[]"]').each(function (indx) {
                        if (formValue.item_detail_name == jQuery(this).val() && formValue.row_index != jQuery(this).closest('tr').index()) {
                            noDuplicate = false;
                            return;
                        }
                    });
                } else {
                    jQuery('#contactTable tbody input[name*="item_detail_name[]"]').each(function (indx) {
                        if (formValue.item_detail_name == jQuery(this).val()) {
                            noDuplicate = false;
                            return;
                        }
                    });
                }
                if (noDuplicate) {
                    thisModal.find('#item_detail_name').closest('div.control-group').removeClass('error');
                    var item_detail_name = formValue.item_detail_name ? formValue.item_detail_name : "";
                    // var contact_mobile_no = formValue.contact_mobile_no ? formValue.contact_mobile_no : "";
                    // var contact_email = formValue.contact_email ? formValue.contact_email : "";

                    if (item_detail_name != "") {

                        if (formValue.form_type == "edit") {
                            contact_data[formValue.form_index] = formValue;
                            let tblHtml = ``;
                            tblHtml += `<td>
                                <a onclick="editContactDetails(this)"><i class="iconfa-pencil action-icon edit-contact"></i></a>
                                <a onclick="removeContactDetails(this)"><i class="action-icon iconfa-trash remove-contact"></i></a>
                                <input type="hidden" name="form_indx" value="${formValue.form_index}"/>
                                </td>`;
                            tblHtml += `<td>${item_detail_name}<input type='hidden' name='item_detail_name[]' value="${item_detail_name}"/></td>`;
                            // tblHtml += `<td>${contact_mobile_no}<input type='hidden' name='contact_mobile_no[]' value="${contact_mobile_no}"/></td>`;
                            // tblHtml += `<td>${contact_email}<input type='hidden' name='contact_email[]' value="${contact_email}"/></td>`;
                            jQuery('#contactTable tbody').find('tr').eq(formValue.row_index).empty().append(tblHtml);
                        } else {
                            contact_data.push(formValue)
                            let formIndx = contact_data.indexOf(formValue);
                            if (jQuery('#contactTable tbody').find('#noContact').length > 0) {
                                jQuery('#contactTable tbody').empty();
                            }

                            let tblHtml = `<tr>`;
                            tblHtml += `<td>
                                <a onclick="editContactDetails(this)"><i class="iconfa-pencil action-icon edit-contact"></i></a>
                                <a onclick="removeContactDetails(this)"><i class="action-icon iconfa-trash remove-contact"></i></a>
                                <input type="hidden" name="form_indx" value="${formIndx}"/>
                                </td>`;
                            tblHtml += `<td>${item_detail_name}<input type='hidden' name='item_detail_name[]' value="${item_detail_name}"/></td>`;
                            // tblHtml += `<td>${contact_mobile_no}<input type='hidden' name='contact_mobile_no[]' value="${contact_mobile_no}"/></td>`;
                            // tblHtml += `<td>${contact_email}<input type='hidden' name='contact_email[]' value="${contact_email}"/>
                            // </td>`;
                            tblHtml += `</tr>`;
                            jQuery('#contactTable tbody').append(tblHtml);

                        }
                    }
                    thisModal.modal('hide');

                } else {
                    thisModal.find('#item_detail_name').closest('div.control-group').addClass('error').focus();
                    toastError("Name Is Already Taken");
                }
            }
            // else {
            //     toastError("Please Enter Atleast One Field Value");
            //     jQuery("#popup_ok").click(function () {
            //         setTimeout(() => {
            //             thisModal.find('#contact_person').focus();
            //         }, 100);
            //     });
            // }
        }
    });

});
jQuery('#itemdetailModal').on('show.bs.modal', function (e) {
    let thisForm = jQuery('#itemdetailModal');
    let formType = thisForm.find("#form_type").val();
    // console.log(formType);
    if (formType == "add") {
        jQuery('span.checked').removeClass('checked');
        jQuery('div.error').removeClass('error');
        thisForm.find('flabel').text("Add");
        thisForm.find('slabel').text("Add");
        setTimeout(() => {
            thisForm.find("#item_detail_name").focus();
        }, 300)
    } else {
        thisForm.find('flabel').text("Edit");
        thisForm.find('slabel').text("Update");
    }
});


//<--On modal hide-->//

jQuery('#itemdetailModal').on('hide.bs.modal', function (e) {
    let thisForm = jQuery('#itemdetailModal');
    thisForm.find("#form_type").val("add");
    thisForm.find("#form_index").val("");
    thisForm.find("#row_index").val("");
    jQuery('#itemdetail_form').trigger("reset");
});


function removeFormObj(formIndx) {
    delete contact_data[formIndx];
}

function fillContactForm(formIndx, rawIndx) {
    let thisForm = jQuery('#itemdetailModal');
    thisForm.find("#form_type").val("edit");
    thisForm.find("#form_index").val(formIndx);
    thisForm.find("#row_index").val(rawIndx);
    var frmData = contact_data[formIndx];
    thisForm.find("#item_detail_name").val(frmData.item_detail_name);
    // thisForm.find("#contact_mobile_no").val(frmData.contact_mobile_no);
    // thisForm.find("#contact_email").val(frmData.contact_email);
    thisForm.modal('show');
}

function fillContactTable() {

    if (contact_data.length > 0) {
        for (let key in contact_data) {
            let formIndx = contact_data.indexOf(contact_data[key]);
            var item_detail_name = contact_data[key].item_detail_name ? contact_data[key].item_detail_name : "";
            // var contact_mobile_no = contact_data[key].contact_mobile_no ? contact_data[key].contact_mobile_no : "";
            // var contact_email = contact_data[key].contact_email ? contact_data[key].contact_email : "";

            if (jQuery('#contactTable tbody').find('#noContact').length > 0) {
                jQuery('#contactTable tbody').empty();
            }
            let tblHtml = `<tr>`;
            tblHtml += `<td>
            <a onclick="editContactDetails(this)"><i class="iconfa-pencil action-icon edit-contact"></i></a>
            <a onclick="removeContactDetails(this)"><i class="action-icon iconfa-trash remove-contact"></i></a>
            <input type="hidden" name="form_indx" value="${formIndx}"/>
            </td>`;
            tblHtml += `<td>${item_detail_name}<input type='hidden' name='item_detail_name[]' value="${item_detail_name}"/></td>`;
            // tblHtml += `<td>${contact_mobile_no}<input type='hidden' name='contact_mobile_no[]' value="${contact_mobile_no}"/></td>`;
            // tblHtml += `<td>${contact_email}<input type='hidden' name='contact_email[]' value="${contact_email}"/>
            // </td>`;
            tblHtml += `</tr>`;
            jQuery('#contactTable tbody').append(tblHtml);
        }
    }
}





// Item  Duplication  Code

function checkItemName(item_name, id) {
    jQuery.ajax({
        url: RouteBasePath + "/verify-item?item_name=" + item_name + "&id=" + id,
        type: 'GET',
        dataType: 'json',
        processData: false,
        success: function (data) {
            if (data.response_code == 1) {
                // console.log(data.response_code);
                // jAlert(data.response_message);
                toastElement(data.response_message, "#item_name");
                jQuery('#item_btn').attr('disabled', true);
            } else {
                // jAlert('error');
                jQuery('#item_btn').attr('disabled', false);

            }
        }
    });
}

function verifyItem() {
    var item_name = jQuery('#item_name').val();
    var id = jQuery('#id').val();
    var hidden = jQuery('#item').val();
    var suggestion_list = jQuery('#item_list').html;

    if (suggestion_list != '') {
        checkItemName(item_name, id);
    }
}




jQuery(document).on('click', '#item_list', function (e) {
    var suggest = e.target.innerHTML;
    jQuery('#item').val(suggest);
    var hidden = jQuery('#item').val();
    var suggestion_list = jQuery('#item_list').html;

    var item_name = hidden;
    if (suggestion_list != '') {
        checkItemName(item_name);
    }
});


// suggestionList

function suggestItemName(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#item_name").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/item-list?term=" + search,

            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#item_name").removeClass('file-loader');

                // console.log(data.response_code);
                if (data.response_code == 1) {


                    jQuery('#item_list').html(data.itemList);

                } else {

                    toastError(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#item_name").removeClass('file-loader');

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

    }

}





function getLatestItemcode() {
    let itemGroupId = jQuery('#item_group_id option:selected').val();

    let getUrl = itemHiddenId != undefined && itemHiddenId != "" ? RouteBasePath + "/get-latest-itemcode?id=" + itemGroupId + "&hiddenid=" + itemHiddenId : RouteBasePath + "/get-latest-itemcode?id=" + itemGroupId;

    if (itemGroupId != "") {
        jQuery('#item_code').addClass('file-loader');
        jQuery.ajax({
            // url: RouteBasePath + "/get-latest-itemcode?id=" + itemGroupId,   
            url: getUrl,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {

                jQuery('#item_code').removeClass('file-loader');
                if (data.response_code == 1) {

                    jQuery('#item_code').val(data.item_data.replace(/\s/g, ""));
                    // jQuery('#item_code').attr('readonly', true);
                    // jQuery('#item_code').prop({ tabindex: 1, readonly: true });
                    //  jQuery('#item_code').attr('readonly', true);

                } else {
                    console.log(data.response_message)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery('#item_code').removeClass('file-loader');
                console.log('Field To Get Latest OA No.!')
            }
        });
    } else {
    }
}
jQuery(document).ready(function () {
    jQuery('div#hide').hide();
    jQuery('div#item_hide').hide();
    jQuery('div#assembly_hide').hide();
});

function secondaryUnit() {
    var secondary_unit = jQuery('#secondary_unit option:selected').val();
    if (secondary_unit == 'Yes') {
        jQuery('div#hide').show();
        jQuery('div#item_hide').show();
        jQuery('#contactTable tbody').empty();
        addItemDetail();
        jQuery('#wt_pc').attr('readonly', true);
    } else {
        jQuery('div#hide').hide();
        jQuery('div#item_hide').hide();
        jQuery('#wt_pc').attr('readonly', false);
    }
}

function serviceItem() {
    var service_item = jQuery('#service_item option:selected').val();
    if (service_item == 'Yes') {
        jQuery("#qc_required").prop({ tabindex: -1 }).attr('readonly', true);

        jQuery("#require_raw_material_mapping").prop({ tabindex: -1 }).attr('readonly', true);
        jQuery("#fitting_item").prop({ tabindex: -1 }).attr('readonly', true);
        jQuery("#print_dispatch_plan").prop({ tabindex: -1 }).attr('readonly', true);
        jQuery("#own_manufacturing").prop({ tabindex: -1 }).attr('readonly', true);
        jQuery("#dont_allow_req_msl").prop({ tabindex: -1 }).attr('readonly', true);
        jQuery("#secondary_unit").prop({ tabindex: -1 }).attr('readonly', true);
        jQuery("#allow_partial_dispatch").val('No').prop({ tabindex: -1 }).attr('readonly', true).trigger('liszt:updated');

    } else {
        jQuery("#qc_required").prop({ tabindex: 1 }).attr('readonly', false);

        jQuery("#require_raw_material_mapping").prop({ tabindex: -1 }).attr('readonly', false);
        jQuery("#fitting_item").prop({ tabindex: -1 }).attr('readonly', false);
        jQuery("#print_dispatch_plan").prop({ tabindex: -1 }).attr('readonly', false);
        jQuery("#own_manufacturing").prop({ tabindex: -1 }).attr('readonly', false);
        jQuery("#dont_allow_req_msl").prop({ tabindex: -1 }).attr('readonly', false);
        jQuery("#secondary_unit").prop({ tabindex: -1 }).attr('readonly', false);
        jQuery("#allow_partial_dispatch").prop({ tabindex: -1 }).attr('readonly', false);

    }

    secondaryUnit();
}

// add time 
function addItemDetail() {
    var thisHtml = `<tr>
    <td>
        <a onclick="removeItemDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
    </td>
    <td>
        <input type="hidden" name="item_details_id[]" id="item_details_id" value="0"/>
        <input type="text" name="secondary_qty[]" id="secondary_qty"  class="form-control salesmanageTable POaddtables  only-numbers" onkeyup="getSecondItemName(this)" />
    </td>
    <td>
        <input type="text" name="secondary_wt_pc[]" id="secondary_wt_pc"  class="form-control salesmanageTable POaddtables isNumberKey" onblur="formatPoints(this,3)" value="0.000"/>
    </td>
    <td>
        <input type="text" name="secondary_item_name[]" id="secondary_item_name" style="width:99%;" class="form-control" tabindex="-1" readonly/>
    </td>    
    </tr>`;
    jQuery('#contactTable tbody').append(thisHtml);

}

function fillItemDetailTable(item_data) {
    jQuery('#contactTable tbody').empty();
    if (item_data.length > 0) {
        var thisHtml = '';
        var counter = 1;
        for (let key in item_data) {

            var sr_no = counter;

            var in_use_details = item_data[key].is_used ? item_data[key].is_used : "";
            var item_details_id = item_data[key].item_details_id ? item_data[key].item_details_id : 0;
            var item_id = item_data[key].item_id ? item_data[key].item_id : 0;
            var secondary_wt_pc = item_data[key].secondary_wt_pc ? item_data[key].secondary_wt_pc.toFixed(3) : parseFloat(0).toFixed(3);
            var secondary_item_name = item_data[key].secondary_item_name ? item_data[key].secondary_item_name : "";
            var secondary_qty = item_data[key].secondary_qty ? item_data[key].secondary_qty : "";



            thisHtml += `<tr>`;
            if (in_use_details == true) {

                thisHtml += ` 
            <td>
                <a><i class="action-icon iconfa-trash so_details" readonly></i></a>
            </td>
            <td>
                <input type="hidden" name="item_details_id[]" id="item_details_id" value="${item_details_id}"/>
                <input type="text" name="secondary_qty[]" id="secondary_qty"  class="form-control salesmanageTable POaddtables" onkeyup="getSecondItemName(this)" value="${secondary_qty}" readonly/>
            </td>`;
            } else {
                thisHtml += `
             <td>
                <a onclick="removeItemDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a>
            </td>
             <td>
                <input type="hidden" name="item_details_id[]" id="item_details_id" value="${item_details_id}"/>
                <input type="text" name="secondary_qty[]" id="secondary_qty"  class="form-control salesmanageTable POaddtables" onkeyup="getSecondItemName(this)" value="${secondary_qty}"/>
            </td>`;

            }
            thisHtml += ` <td>
                <input type="text" name="secondary_wt_pc[]" id="secondary_wt_pc"  class="form-control salesmanageTable POaddtables isNumberKey"  value="${secondary_wt_pc}" onblur="formatPoints(this,3)"/>
            </td>
            <td>
                <input type="text" name="secondary_item_name[]" id="secondary_item_name"  class="form-control" tabindex="-1" style ="width:99%;" readonly value="${secondary_item_name}"/>
            </td>    
            </tr>`;

        }

        jQuery('#contactTable tbody').append(thisHtml);
    }

}


function getSecondItemName(th) {
    var second_qty = parseInt(th.value) || "";
    var item_name = jQuery('#item_name').val();
    jQuery(th).parents('tr').find("#secondary_item_name").val(item_name + ' - ' + second_qty);
}


function removeItemDetails(th) {
    jConfirm('Are you sure you want <lw-c>to</lw-c> Delete?', 'Confirmation', function (r) {
        if (r === true) {
            jQuery(th).closest("tr").remove();
        }
    });
}