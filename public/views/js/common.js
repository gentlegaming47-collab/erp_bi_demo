var headerOpt = { 'X-CSRF-TOKEN': jQuery('input[name="_token"]').val() };






// location  customer and suppluer district 

function getStates($this = null) {    
  
    if ($this != null) {

        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

        
    }

    let IsAllState = jQuery('#IsAllState').val();
    let ViewPageVal = jQuery("#hidViewPage").val();

    let urlData = '';
    let thisVal = '';
    let thisVal1 = '';

    //location 
    if (ViewPageVal == "Location") {        
        thisVal = jQuery('#location_country_id option:selected').val();
        urlData = RouteBasePath + "/get-location-states?country_id=" + thisVal;
    }
    // supplire
    else if (ViewPageVal == "Supplier") {
        thisVal = jQuery('#supplier_country_id option:selected').val();
        urlData = RouteBasePath + "/get-location-states?country_id=" + thisVal;
    } 
    
    else if (ViewPageVal == "Customer") {
        thisVal = jQuery('#customer_country_id option:selected').val();
        urlData = RouteBasePath + "/get-location-states?country_id=" + thisVal;
    }

    else if (ViewPageVal == "salesOrder") {
        thisVal = jQuery('#so_country_id option:selected').val();     
        urlData = RouteBasePath + "/get-location-states?country_id=" + thisVal;
       
        thisVal1 = jQuery('#customer_country_id option:selected').val();     
        urlData = RouteBasePath + "/get-location-states?country_id=" + thisVal1;
       
    }
    else if (ViewPageVal == "Quotation") {
        thisVal = jQuery('#quot_country_id option:selected').val();     
        urlData = RouteBasePath + "/get-location-states?country_id=" + thisVal;
    }


    if (IsAllState == 'Y') {
        urlData = RouteBasePath + "/get-states";
    }
    

        // console.log("TE", urlData + "" + IsAllState);
    jQuery.ajax({


        url: urlData,

        type: 'GET',

        dataType: 'json',

        processData: false,

        success: function(data) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');
            }

            if (data.response_code == 1) {

                if ($this != null) {

                    var stgDrpHtml = `<option value="">Select State</option>`;

                    for (let indx in data.states) {

                        stgDrpHtml += `<option value="${data.states[indx].id}">${data.states[indx].state_name}</option>`;
                    }

                    if (ViewPageVal == 'Customer') {                            
                        let Id = jQuery("#customer_state_id");
                        console.log(`call state data2`);
                        // console.log("customer_state_id", stgDrpHtml);
                        let Selected = jQuery("#customer_state_id").find("option:selected").val();

                        jQuery("#customer_state_id").empty().append(stgDrpHtml);

                        jQuery("#customer_state_id").val(Selected).trigger('liszt:updated');
                        jQuery('#state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
                        console.log(stgDrpHtml);
                        jQuery('#taluka_state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
                        jQuery('#village_state_id').empty().append(stgDrpHtml).trigger('liszt:updated')
                    } 
                    else if (ViewPageVal == 'Location') {
                        // console.log('location form');
                        let Id = jQuery("#location_state_id");

                        let Selected = jQuery("#location_state_id").find("option:selected").val();

                        jQuery("#location_state_id").empty().append(stgDrpHtml);
                        console.log(`data are ${stgDrpHtml}`);
                        jQuery("#location_state_id").val(Selected).trigger('liszt:updated');
                        jQuery('#state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
                        jQuery('#taluka_state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
                        jQuery('#village_state_id').empty().append(stgDrpHtml).trigger('liszt:updated')

                    } else if (ViewPageVal == "Supplier") {
                        console.log("supplier check");
                        let Id = jQuery("#supplier_state_id");

                        let Selected = jQuery("#supplier_state_id").find("option:selected").val();

                        jQuery("#supplier_state_id").empty().append(stgDrpHtml);
                        jQuery("#state_id").empty().append(stgDrpHtml);
                        jQuery("#state_name").empty().append(stgDrpHtml);



                        jQuery("#supplier_state_id").val(Selected).trigger('liszt:updated');
                        jQuery('#state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
                        jQuery('#taluka_state_id').empty().append(stgDrpHtml).trigger('liszt:updated');
                        jQuery('#village_state_id').empty().append(stgDrpHtml).trigger('liszt:updated')
                    } 
                    else if (ViewPageVal == "Taluka")
                    {
                        let Id = jQuery("#taluka_state_id");

                        let Selected = jQuery("#taluka_state_id").find("option:selected").val();

                       
                        jQuery("#taluka_state_id").empty().append(stgDrpHtml);
                        jQuery("#commonDistrictForm #state_id").empty().append(stgDrpHtml);
                        
                        jQuery("#taluka_state_id").val(Selected).trigger('liszt:updated');
                        
                      
                    }
                    else if (ViewPageVal == "Village")
                    {
                        let Id = jQuery("#village_state_id");

                        let Selected = jQuery("#village_state_id").find("option:selected").val();

                        jQuery("#village_state_id").empty().append(stgDrpHtml);
                        jQuery("#commonDistrictForm #state_id").empty().append(stgDrpHtml);
                        jQuery("#village_state_id").val(Selected).trigger('liszt:updated');
                        
                    }
                    else if (ViewPageVal == "salesOrder")
                    {
                        
                        let Id = jQuery("#so_state_id");

                        let Selected = jQuery("#so_state_id").find("option:selected").val();
                        let customer_sales_order_id = jQuery("#customer_state_id").find("option:selected").val();
                        let city_state_id = jQuery("#commonDistrictForm").find("#state_id option:selected").val();
                        let taluka_state_id = jQuery("#commonTalukaForm").find("#taluka_state_id option:selected").val();
                        let village_state_id = jQuery("#commonVillageForm").find("#village_state_id option:selected").val();
                            

                        jQuery("#so_state_id").empty().append(stgDrpHtml);
                        jQuery("#customer_state_id").empty().append(stgDrpHtml);
                        jQuery("#commonDistrictForm").find("#state_id").empty().append(stgDrpHtml);
                        jQuery("#commonTalukaForm").find("#taluka_state_id").empty().append(stgDrpHtml);
                        jQuery("#commonVillageForm").find("#village_state_id").empty().append(stgDrpHtml);


                        jQuery("#so_state_id").val(Selected).trigger('liszt:updated');
                        jQuery("#customer_state_id").val(customer_sales_order_id).trigger('liszt:updated');
                        jQuery("#commonDistrictForm").find("#state_id").val(city_state_id).trigger('liszt:updated');
                        jQuery("#commonTalukaForm").find("#taluka_state_id").val(taluka_state_id).trigger('liszt:updated');
                        jQuery("#commonVillageForm").find("#village_state_id").val(village_state_id).trigger('liszt:updated');


                          // jQuery("#commonDistrictForm").find("#state_id").empty().append(stgDrpHtml);
                        // jQuery("#cityModal").find("#state_id").empty().append(stgDrpHtml);
                        // // jQuery("#commonDistrictForm #state_id").empty().append(stgDrpHtml);
                        // jQuery("#commonTalukaForm #taluka_state_id").empty().append(stgDrpHtml);
                        
                        
                    }
                    else if (ViewPageVal == "Quotation")
                    {
                        
                        let Id = jQuery("#quot_state_id");

                        let Selected = jQuery("#quot_state_id").find("option:selected").val();
                        let customer_sales_order_id = jQuery("#customer_state_id").find("option:selected").val();
                        let city_state_id = jQuery("#commonDistrictForm").find("#state_id option:selected").val();
                        let taluka_state_id = jQuery("#commonTalukaForm").find("#taluka_state_id option:selected").val();
                        let village_state_id = jQuery("#commonVillageForm").find("#village_state_id option:selected").val();
                            

                        jQuery("#quot_state_id").empty().append(stgDrpHtml);
                        jQuery("#customer_state_id").empty().append(stgDrpHtml);
                        jQuery("#commonDistrictForm").find("#state_id").empty().append(stgDrpHtml);
                        jQuery("#commonTalukaForm").find("#taluka_state_id").empty().append(stgDrpHtml);
                        jQuery("#commonVillageForm").find("#village_state_id").empty().append(stgDrpHtml);


                        jQuery("#quot_state_id").val(Selected).trigger('liszt:updated');
                        jQuery("#customer_state_id").val(customer_sales_order_id).trigger('liszt:updated');
                        jQuery("#commonDistrictForm").find("#state_id").val(city_state_id).trigger('liszt:updated');
                        jQuery("#commonTalukaForm").find("#taluka_state_id").val(taluka_state_id).trigger('liszt:updated');
                        jQuery("#commonVillageForm").find("#village_state_id").val(village_state_id).trigger('liszt:updated');
                        
                    }
                    else {                  
                        jQuery($this).each(function(e) {
                            let Id = jQuery(this).attr('id');  
                            
                            let Selected = jQuery(this).find("option:selected").val();
                            jQuery(this).empty().append(stgDrpHtml);
                            jQuery(this).val(Selected).trigger('liszt:updated');
                        });
                    }
                }

            } else {

                toastError(data.response_message);

            }

        },

        error: function(jqXHR, textStatus, errorThrown) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }

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

function getCities($this = null) {
  
    if ($this != null) {

        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

    }
    let IsAllState = jQuery('#IsAllState').val();
    let ViewPageVal = jQuery("#hidViewPage").val();

    let urlData = '';
    let thisVal = '';

    if (ViewPageVal == "Location") {
        console.log('in location page');
        thisVal = jQuery('#location_state_id option:selected').val();

        urlData = RouteBasePath + "/get-district/?state_id=" + thisVal;

    } 
    else if (ViewPageVal == "Supplier") {
        thisVal = jQuery('#supplier_state_id option:selected').val();
        urlData = RouteBasePath + "/get-district/?state_id=" + thisVal;
    } 
    else if (ViewPageVal == "Customer") {
        thisVal = jQuery('#customer_state_id option:selected').val();        
        urlData = RouteBasePath + "/get-district/?state_id=" + thisVal;
    }
    else if (ViewPageVal == "salesOrder") {
        thisVal = jQuery('#so_state_id option:selected').val();        
        urlData = RouteBasePath + "/get-district/?state_id=" + thisVal;
    }
    else if (ViewPageVal == "Quotation") {
        thisVal = jQuery('#quot_state_id option:selected').val();        
        urlData = RouteBasePath + "/get-district/?state_id=" + thisVal;
    }
    
    
    
    
 
    if (IsAllState == 'Y') {
        urlData = RouteBasePath + "/get-cities";
    }

    
  
    
    
    jQuery.ajax({


        url: urlData,

        type: 'GET',

        dataType: 'json',

        processData: false,

        success: function(data) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');
            }

            if (data.response_code == 1) {

                if ($this != null) {

                    var stgDrpHtml = `<option value="">Select District</option>`;


                    for (let indx in data.cities) {

                        stgDrpHtml += `<option value="${data.cities[indx].id}">${data.cities[indx].district_name}</option>`;
                    }

                    if (ViewPageVal == 'Customer') {
                        let Id = jQuery("#customer_district_id");
                        let Selected = jQuery("#customer_district_id").find("option:selected").val();
                     
                        jQuery("#customer_district_id").empty().append(stgDrpHtml);

                        jQuery("#customer_district_id").val(Selected).trigger('liszt:updated');

                       
                            getDistrict().done(function(response){
                                jQuery('#taluka_district_id').val(Selected).trigger('liszt:updated');
                            })
                       
    
    
                    } else if (ViewPageVal === "Location") {
                        // console.log('location form');
                        let Id = jQuery("#location_district_id");
                        // console.log("location_district_id", stgDrpHtml);
                        let Selected = jQuery("#location_district_id").find("option:selected").val();

                        jQuery("#location_district_id").empty().append(stgDrpHtml);

                        jQuery("#location_district_id").val(Selected).trigger('liszt:updated');

                        // call the function no ref dropdown
                       
                            getDistrict().done(function(response){
                                jQuery('#taluka_district_id').val(Selected).trigger('liszt:updated');
                            })
                        
                        
                    } else if (ViewPageVal == "Supplier") {
                        let Id = jQuery("#supplier_district_id");
                        
                        let Selected = jQuery("#supplier_district_id").find("option:selected").val();

                        jQuery("#supplier_district_id").empty().append(stgDrpHtml);

                        jQuery("#supplier_district_id").val(Selected).trigger('liszt:updated');
                        console.log(Selected);
                      
                            getDistrict().done(function(response){
                                jQuery('#taluka_district_id').val(Selected).trigger('liszt:updated');
                            })
                        
    
                    } else if (ViewPageVal == "Taluka") {
                        let Id = jQuery("#taluka_district_id");
                        let Selected = jQuery("#taluka_district_id").find("option:selected").val();                      
                        jQuery("#taluka_district_id").empty().append(stgDrpHtml);
                        getDistrict().done(function(resposne) {            
                                jQuery("#taluka_district_id").val(Selected).trigger('liszt:updated');
                        });

                    }
                    else if (ViewPageVal == "Village") {
                        let Id = jQuery("#district_id");
                        let Selected = jQuery("#district_id").find("option:selected").val();                      
                        jQuery("#district_id").empty().append(stgDrpHtml);
                        getDistrictData().done(function(resposne) {            
                                jQuery("#district_id").val(Selected).trigger('liszt:updated');
                        });

                    } else if (ViewPageVal == "salesOrder") {
                        let Selected = jQuery("#so_district_id").find("option:selected").val();                      
                        let Selected1 = jQuery("#customer_district_id").find("option:selected").val();                      
                        jQuery("#so_district_id").empty().append(stgDrpHtml);
                        //jQuery("#customer_district_id").empty().append(stgDrpHtml);
                        getSoDistrict().done(function(resposne) {            
                            jQuery("#so_district_id").val(Selected).trigger('liszt:updated');
                       //     jQuery("#customer_district_id").val(Selected1).trigger('liszt:updated');
                    });

                    jQuery("#commonTalukaForm #taluka_district_id").empty().append(stgDrpHtml);
                    
                    }
                    else if (ViewPageVal == "Quotation") {
                        let Selected = jQuery("#quot_district_id").find("option:selected").val();                      
                        jQuery("#quot_district_id").empty().append(stgDrpHtml);
                        getQuotDistrict().done(function(resposne) {            
                            jQuery("#quot_district_id").val(Selected).trigger('liszt:updated');
                    });

                    jQuery("#commonTalukaForm #taluka_district_id").empty().append(stgDrpHtml);
                    
                    }
                    else {
                        jQuery($this).each(function(e) {

                            let Id = jQuery(this).attr('id');                            
                            let Selected = jQuery(this).find("option:selected").val();

                            jQuery(this).empty().append(stgDrpHtml);

                            jQuery(this).val(Selected).trigger('liszt:updated');

                        });
                    }
                }

            } else {

                toastError(data.response_message);

            }

        },

        error: function(jqXHR, textStatus, errorThrown) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }

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


function getTaluka($this = null) {



    if ($this != null) {

        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

        
    }
    let IsAllState = jQuery('#IsAllState').val();
    let ViewPageVal = jQuery("#hidViewPage").val();

    let urlData = '';
    let thisVal = '';

    if (ViewPageVal == "Location") {
        
        thisVal = jQuery('#location_district_id option:selected').val();
        
        urlData = RouteBasePath + "/get-taluka?district_id=" + thisVal;


    } else if (ViewPageVal == "Supplier") {
        thisVal = jQuery('#supplier_district_id option:selected').val();

        urlData = RouteBasePath + "/get-taluka?district_id=" + thisVal;
    }
    else if (ViewPageVal == "Quotation") {
        thisVal = jQuery('#quot_district_id option:selected').val();

        urlData = RouteBasePath + "/get-taluka?district_id=" + thisVal;
    } else {
        thisVal = jQuery('#customer_district_id option:selected').val();

        urlData = RouteBasePath + "/get-taluka?district_id=" + thisVal;
    }


    if (IsAllState == 'Y') {
        urlData = RouteBasePath + "/fetch-talukas";
    }


    jQuery.ajax({



        url: urlData,

        type: 'GET',

        dataType: 'json',

        processData: false,

        success: function(data) {



            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }



            if (data.response_code == 1) {



                if ($this != null) {



                    var stgDrpHtml = `<option value="">Select Taluka</option>`;



                    for (let indx in data.taluka) {



                        stgDrpHtml += `<option value="${data.taluka[indx].id}">${data.taluka[indx].taluka_name}</option>`;



                    }



                    if (ViewPageVal == 'Customer') {
                        let Id = jQuery("#customer_taluka_id");
                        let Selected = jQuery("#customer_taluka_id").find("option:selected").val();
                       
                        jQuery("#customer_taluka_id").empty().append(stgDrpHtml);

                        jQuery("#customer_taluka_id").val(Selected).trigger('liszt:updated');
                    } else if (ViewPageVal == 'Location') {                        
                        
                        
                        let Id = jQuery("#location_taluka_id");
                        let Selected = jQuery("#location_taluka_id").find("option:selected").val();
                       
                        jQuery("#location_taluka_id").empty().append(stgDrpHtml);

                        jQuery("#location_taluka_id").val(Selected).trigger('liszt:updated');
                        
                      
                    } else if (ViewPageVal == "Supplier") {
                        let Id = jQuery("#supplier_taluka_id");
                        let Selected = jQuery("#supplier_taluka_id").find("option:selected").val();
                       
                        jQuery("#supplier_taluka_id").empty().append(stgDrpHtml);

                        jQuery("#supplier_taluka_id").val(Selected).trigger('liszt:updated');
                    } 
                    else if (ViewPageVal == "Village") {

                        let Id = jQuery("#taluka_id");
                        let Selected = jQuery("#taluka_id").find("option:selected").val();                      
                        jQuery("#taluka_id").empty().append(stgDrpHtml);
                        getTalukaData().done(function(resposne) {            
                                jQuery("#taluka_id").val(Selected).trigger('liszt:updated');
                        });

                      
                    } 
                    else if (ViewPageVal == "salesOrder") {
                        let Selected = jQuery("#so_taluka_id").find("option:selected").val();                      
                        jQuery("#so_taluka_id").empty().append(stgDrpHtml);
                        getSoTaluka().done(function(resposne) {            
                                jQuery("#so_taluka_id").val(Selected).trigger('liszt:updated');
                        });
                    } 
                    else if (ViewPageVal == "Quotation") {
                        let Selected = jQuery("#quot_taluka_id").find("option:selected").val();                      
                        jQuery("#quot_taluka_id").empty().append(stgDrpHtml);
                        getQuotTaluka().done(function(resposne) {            
                                jQuery("#quot_taluka_id").val(Selected).trigger('liszt:updated');
                        });
                    } 
                    
                    else {
                        jQuery($this).each(function(e) {

                            let Id = jQuery(this).attr('id');
                            console.log("Id", stgDrpHtml);
                            let Selected = jQuery(this).find("option:selected").val();

                            jQuery(this).empty().append(stgDrpHtml);

                            jQuery(this).val(Selected).trigger('liszt:updated');

                        });
                    }


                }



            } else {

                toastError(data.response_message);

            }

        },

        error: function(jqXHR, textStatus, errorThrown) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }



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

function get_village($this = null) {

    
    
    if ($this != null) {

        jQuery($this).next('.chzn-container').find('a').addClass('file-loader');

    }

        let IsAllState = jQuery('#IsAllState').val();
        let ViewPageVal = jQuery("#hidViewPage").val();

        let urlData = '';
        let thisVal = '';
        
        
        if (ViewPageVal === "Location")
        {
            thisVal = jQuery("#location_taluka_id option:selected").val();
            urlData = RouteBasePath + "/get-village/?taluka_id=" + thisVal;
        } 
        else if (ViewPageVal == "Customer")
        {
            thisVal = jQuery("#customer_taluka_id option:selected").val();
            
            urlData = RouteBasePath + "/get-village/?taluka_id=" + thisVal;
        } 
        else if (ViewPageVal === "Supplier"){
            thisVal = jQuery("#supplier_taluka_id option:selected").val();
            urlData = RouteBasePath + "/get-village/?taluka_id=" + thisVal;
        }
        else if (ViewPageVal == "salesOrder") {
            thisVal = jQuery("#so_taluka_id option:selected").val();
            urlData = RouteBasePath + "/get-village/?taluka_id=" + thisVal;
        }
        else if (ViewPageVal == "Quotation") {
            thisVal = jQuery("#quot_taluka_id option:selected").val();
            urlData = RouteBasePath + "/get-village/?taluka_id=" + thisVal;
        }
    
    if (IsAllState == 'Y') {
        urlData = RouteBasePath + "/get-villagedata";
    }

  

    jQuery.ajax({

        url: urlData,

        type: 'GET',

        dataType: 'json',

        processData: false,

        success: function(data) {

            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');
            }

            if (data.response_code == 1) {

                if ($this != null) {

                    var stgDrpHtml = `<option value="">Select Village</option>`;

                    for (let indx in data.village) {

                        stgDrpHtml += `<option value="${data.village[indx].id}">${data.village[indx].village_name}</option>`;
                    }
                    
                    if (ViewPageVal == 'Customer') {
                        
                        let Id = jQuery("#customer_village_id");
                        let Selected = jQuery("#customer_village_id").find("option:selected").val();                        
                        jQuery("#customer_village_id").empty().append(stgDrpHtml);
                        jQuery("#customer_village_id").val(Selected).trigger('liszt:updated');
                    } else if (ViewPageVal === "Location") {
                        let Id = jQuery("#location_village_id");
                        let Selected = jQuery("#location_village_id").find("option:selected").val();                        
                        jQuery("#location_village_id").empty().append(stgDrpHtml);
                        jQuery("#location_village_id").val(Selected).trigger('liszt:updated');

                    } else if (ViewPageVal == "Supplier") {
                        let Id = jQuery("#supplier_village_id");
                        let Selected = jQuery("#supplier_village_id").find("option:selected").val();                        
                        jQuery("#supplier_village_id").empty().append(stgDrpHtml);
                        jQuery("#supplier_village_id").val(Selected).trigger('liszt:updated');
                    } else {                      
                        jQuery($this).each(function(e) {
                            let Id = jQuery(this).attr('id');
                            
                            let Selected = jQuery(this).find("option:selected").val();

                            jQuery(this).empty().append(stgDrpHtml);

                            jQuery(this).val(Selected).trigger('liszt:updated');

                        });
                    }
                }

            } else {

                toastError(data.response_message);

            }

        },

        error: function(jqXHR, textStatus, errorThrown) {
            console.log("error called now ");
            return false;
            if ($this != null) {

                jQuery($this).next('.chzn-container').find('a').removeClass('file-loader');

            }

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








// color picker
if (jQuery('.color-picker').length > 0) {
    jQuery('#colorSelector').ColorPicker({
        onShow: function(colpkr) {
            jQuery(colpkr).fadeIn(500);
            return false;
        },
        onHide: function(colpkr) {
            jQuery(colpkr).fadeOut(500);
            return false;
        },
        onChange: function(hsb, hex, rgb) {
            jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
            jQuery('.color-picker').val('#' + hex);
        }
    });
}



function disabledDropdownVal() {
    jQuery('select').on('keydown', function(e) {
        var arrowKeys = [37, 38, 39, 40]; 
        if (arrowKeys.includes(e.keyCode)) {
            e.preventDefault();
        }
    });

    jQuery('select[readonly] option').on('click', function() {
        var readonly_select = jQuery(this);
        readonly_select.attr('readonly', true).attr('data-original-value', readonly_select.val()).on('change', function(i) {
            jQuery(i.target).val(jQuery(this).attr('data-original-value'));
        });
    });
}

function checkInputNaN(input) {
	return input != null && input != undefined && input != "" && !isNaN(input) ? input : "";
}



function checkSpecialCharacter(string) {
    return string.replace(/"/g, '&quot;');
}

function checkInputNull(input)
{
	return input != null ? input : "";
}














// new working code 

// function disabledDropdownVal(value)
// {
//     if(jQuery(value).attr('readonly')!= undefined && jQuery(value).attr('readonly')=='readonly'){
//         jQuery(`[name="${jQuery(value).attr('name')}"] option`).each(function () {  
//             if (!jQuery(this).is(':selected')) {
//                 jQuery(`[name="${jQuery(value).attr('name')}"] option[value='${jQuery(this).val() }']`).attr('disabled', true).trigger("chosen:updated");
               
//             }
//         });
//     }
// }





// function disabledDropdownVal()
// {   
//         jQuery('select[readonly] option').each(function () {              
//             if (!jQuery(this).is(':selected')) {
//                     jQuery("select[readonly] option[value='" + jQuery(this).val() + "']").attr('disabled', true).trigger("chosen:updated");
//             }
//         });
// }



// function disabledDropdownVal()
// {
//     jQuery('select').on('click', function() {
//         var readonly_select = jQuery('select');
//         jQuery(readonly_select).attr('readonly', true).attr('data-original-value', jQuery(readonly_select).val()).on('change', function(i) {
//             jQuery(i.target).val($(this).attr('data-original-value'));
//         });
//     });
// }

    //28-06-2024
    // jQuery('select').each(function(index,elemenet){    
    //     console.log("jQuery(this).attr('readonly')", elemenet);
    //     jQuery(elemenet).bind("change", function(){
    //         console.log("jQuery(this).attr('readonly')", jQuery(this).attr('readonly'));
    //         if(jQuery(this).attr('readonly')){
    //             jQuery('select[readonly] option').each(function () {  
    //                 if (!jQuery(this).is(':selected')) {
    //                         jQuery("select[readonly] option[value='" + jQuery(this).val() + "']").attr('disabled', true).trigger("chosen:updated");
    //                     }
    //             });
    //         }
    //     });
    // })
// }





// // disalbed the button when click the first modal 

// jQuery('*[data-target="#countryModal"], *[data-target="#cityModal"], *[data-target="#stateModal"]').click(function(){
//     jQuery("#addStateModal").attr("disabled", true);
//     jQuery("#cancelState").attr("disabled", true);
//     jQuery("#talukaButton").attr("disabled", true);
//     jQuery("#cancelTaluka").attr("disabled", true);
//  });

// jQuery('*[data-target="#stateModal"]').click(function(){
//     jQuery("#addStateModal").attr("disabled", false);
//     jQuery("#cancelState").attr("disabled", false);    
//  });

//  jQuery('#countryModal, #stateModal, #cityModal, #talukaModal').on('hide.bs.modal', function (e)
//  {
//     jQuery("#addStateModal").attr("disabled", false);
//     jQuery("#cancelState").attr("disabled", false);
//     jQuery("#talukaButton").attr("disabled", false);
//     jQuery("#cancelTaluka").attr("disabled", false);
//  });


