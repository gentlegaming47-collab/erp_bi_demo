function suggestCustomer(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#customer_name").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/customer-list_report?term=" + encodeURI(search),



            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#customer_name").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#customer_list').html(data.customerList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#customer_name").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestVillage(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#village_name").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/village-list?term=" + encodeURI(search),



            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#village_name").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#village_list').html(data.villageList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#village_name").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestTaluka(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#taluka_name").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/taluka-list?term=" + encodeURI(search),



            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#taluka_name").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#taluka_list').html(data.talukaList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#taluka_name").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestCity(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#district_name").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/city-list?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#district_name").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#district_list').html(data.cityList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#district_name").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestState(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#state_name").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/state-list?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#state_name").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#state_list').html(data.stateList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#state_name").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestDpNo(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#dp_number").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/dp_number-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#dp_number").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#dp_number_list').html(data.dpnumberList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#dp_number").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestSONumber(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#so_number").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/so_number-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#so_number").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#so_number_list').html(data.sonumberList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#so_number").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestMRNumber(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#mr_number").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/mr_number-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#mr_number").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#mr_number_list').html(data.mrnumberList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#mr_number").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestPONumber(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#po_number").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/po_number-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#po_number").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#po_number_list').html(data.ponumberList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#po_number").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestPRNumber(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#pr_number").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/pr_number-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#pr_number").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#pr_number_list').html(data.prnumberList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#pr_number").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestOrderBy(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#order_by").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/order_by-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#order_by").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#order_by_list').html(data.orderbyList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#order_by").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestIPNumber(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#ip_number").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/ip_number-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#ip_number").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#ip_number_list').html(data.ipnumberList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#ip_number").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestIssueNumber(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#issue_number").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/issue_number-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#issue_number").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#issue_number_list').html(data.issuenumberList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#issue_number").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestReturnNumber(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#return_number").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/return_number-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#return_number").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#return_number_list').html(data.returnnumberList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#return_number").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestIssueNo(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#issue_no").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/issue_no-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#issue_no").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#issue_no_list').html(data.issuenoList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#issue_no").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}
function suggestGrnNo(e, $this) {

    var keyevent = e

    if (keyevent.key != "Tab") {

        jQuery("#grn_no").addClass('file-loader');

        var search = jQuery($this).val();


        jQuery.ajax({

            url: RouteBasePath + "/grn_no-list_report?term=" + encodeURI(search),
            type: 'GET',

            dataType: 'json',

            processData: false,

            success: function (data) {

                jQuery("#grn_no").removeClass('file-loader');

                if (data.response_code == 1) {

                    jQuery('#grn_no_list').html(data.grnnoList);

                } else {

                    jAlert(data.response_message);

                }

            },

            error: function (jqXHR, textStatus, errorThrown) {

                jQuery("#grn_no").removeClass('file-loader');

                var errMessage = JSON.parse(jqXHR.responseText);



                if (errMessage.errors) {

                    validator.showErrors(errMessage.errors);



                } else if (jqXHR.status == 401) {



                    jAlert(jqXHR.statusText);

                } else {

                    jAlert('Something went wrong!');

                    console.log(JSON.parse(jqXHR.responseText));

                }

            }

        });

    }

}

