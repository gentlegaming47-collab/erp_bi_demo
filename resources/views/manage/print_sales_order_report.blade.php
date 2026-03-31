@extends('layouts.app',['pageTitle' => 'Print Sales Order'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Print Sales Order</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div id="show-progress"></div>
    <div class="headtitle">      
        <h4 class="widgettitle">Print Sales Order</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
     <form id="orderSearchForm" name="orderSearchForm" class="stdform">
                <div class="row">

                        <div class="span-6">
                            <div class="par control-group form-control">
                              <label class="control-label" for="from_date">From Date  <sup class="astric">*</sup> </label>
                              <div class="controls"> <span class="formwrapper">
                                <input name="from_date" id="from_date" class="input-large date-picker  " />
                                </span> </div>
                            </div>
                          </div>
                          <div class="span-6">
                                <div class="par control-group form-control">
                                  <label class="control-label" for="to_date">To Date  <sup class="astric">*</sup> </label>
                                  <div class="controls"> <span class="formwrapper">
                                    <input name="to_date" id="to_date" class="input-large date-picker no-fill " />
                                    </span> </div>
                                </div>
                          </div>
                             
                </div>
               
                <p class="stdformbutton">
                        <button id="btnsearch" class="btn btn-primary">Search</button>&nbsp;
                        <button id="reset-order-data" type="button" class="btn btn-primary">Reset</button>
                        <button id="print-order-data" type="button" class="btn btn-primary">Print</button>
                         {{-- <button id="download-order-data" type="button" class="btn btn-primary">Download</button> --}}
                </p>

        </form>
        <div class="divider15"> </div>
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                     <th> <input type="checkbox" name="checkall-inward" class="simple-check" id="checkall-so"/> </th>                  
                    <!-- <th class="head0 report-itemlabel"> Select</th>                     -->
                    {{-- <th class="head0 report-itemlabel"> Result</th>                     --}}
                    <th class="head1">SO No.</th>

                    <th class="head0">SO Date</th>

                    <th class="head1">SO From </th>

                    <th class="head1">SO Type </th>

                    <th class="head1">Cust. Group  </th>

                    {{-- <th class="head1">Value </th> --}}

                    <th class="head1">Customer / Location </th>   

                    {{-- <th class="head0" style="display:none;"></th> --}}

                    <th class="head0">Dealer </th>

                    <th class="head0">Reg. No. </th>
                    <th class="head0">MIS Category </th>

                    <th class="head0">Country </th>

                    <th class="head0">State </th>

                    <th class="head0">District  </th>

                    <th class="head0">Taluka  </th>

                    <th class="head0">village </th>

                    {{-- <th class="head0">Item</th>

                    <th class="head0">Code </th>

                    <th class="head0">Group  </th>

                    <th  class="head0">SO Qty.</th>

                    <th  class="head0">Unit </th>

                    <th  class="head0">Rate/Unit  </th>

                    <th  class="head0">Amount</th>     --}}

                    {{-- <th class="head0">Modified by</th>

                    <th class="head0">Modified on</th>

                    <th class="head0">Created by</th>

                    <th class="head1">Created on</th>
                    --}}
                    <th class="head0" style="display:none;"></th>
                </tr>
            </thead>
        </table>
        <div class="note-text">Note: To search across multiple columns, add a space between words.</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
jQuery(document).ready(function() {
    var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};
    var table = "";

     table = jQuery('#dyntable').DataTable({
        "lengthMenu": [ 10],
        "pageLength":25
    });

    var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};
   
   LoadDataTable();

function LoadDataTable(){

     if (jQuery.fn.DataTable.isDataTable('#dyntable')) {       
        jQuery('#dyntable').DataTable().clear().destroy();
    }

    var data = new FormData(document.getElementById('orderSearchForm'));
    var formValue = Object.fromEntries(data.entries());
    var recordsTotal=0;
 
    table= jQuery('#dyntable').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX":true,
        "sScrollX": "100%",
        "sScrollXInner": "110%",
        "bScrollCollapse": true,
        "oLanguage": {
            "sSearch": "Search :"
        },
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        pageLength : 25,
        // "order": [[3, 'desc']],
        "order": [[ 2, 'desc' ],[ 15, 'desc' ]],


        ajax: {
            url: "{{ route('listing-print_sales_order_report') }}",
            type: "POST",
            headers: headerOpt,
            data : {
                        'from_date':formValue.from_date,
                        'to_date':formValue.to_date,
                    },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#dyntable_processing').hide();
                if(jqXHR.status == 401){
                    toastError(jqXHR.statusText);
                }else{
                toastError('Somthing went wrong!');
                }
                console.log(JSON.parse(jqXHR.responseText));
            }
        },

        columns: [
        { data: null, name: 'checkbox_val', className: 'center', width: '5%',searchable: false,
        render: function(data,type,row,meta){   
            // meta get the zero based index 
            //return `<input type='checkbox' class='simple-check' id='rt_report_id_${meta.row}'>`;
             return `<input type='checkbox' class='simple-check so_check' id='so_id_${data.id}'  value='${data.id}'>`;
           // return `<input type='checkbox' class='simple-check so_check' id='rt_report_id_${btoa(data.rt_report_id)}' value=${btoa(data.rt_report_id)} >`;
        }
    },
    { data: 'so_number', name: 'sales_order.so_number', },

    { data: 'so_date', name: 'sales_order.so_date', },

    { data: 'so_from_value_fix', name: 'so_from_value_fix', },

    { data: 'so_type_value_fix', name: 'sales_order.so_type_value_fix', },

    { data: 'customer_group_name', name: 'customer_groups.customer_group_name', },

    { data: 'name', name: 'name', },  

    { data: 'dealer_name', name: 'dealers.dealer_name', },

    { data: 'customer_reg_no', name: 'sales_order.customer_reg_no', },
    { data: 'mis_category', name: 'mis_category.mis_category', },

    { data: 'country_name', name: 'countries.country_name', },

    { data: 'state_name', name: 'states.state_name', },

    { data: 'district_name', name: 'districts.district_name', },

    { data: 'taluka_name', name: 'talukas.taluka_name', },

    { data: 'village_name', name: 'villages.village_name', },

    { data: 'so_sequence' ,name: 'sales_order.so_sequence' , visible:false},
    ],
     initComplete: function () {
        // Exclude first column (index 0) from search
        initColumnSearch('#dyntable', [0]);
    },
});

    }

            jQuery.validator.addMethod("greaterThan", 

            function(value, element, params) {
            
                value = value != "" ? value.split("/"):[];
                let value2 = jQuery(params).val() != "" ? jQuery(params).val().split("/"):[];
            
                if(value.length && value2.length){
                    value = `${value[2]}-${value[1]}-${value[0]}`;
                    value2 = `${value2[2]}-${value2[1]}-${value2[0]}`;
                    return new Date(value) >= new Date(value2);
                }
                return true;
                
            },'Must be greater than equal to {0}.');
            
            jQuery.validator.addMethod("lessThan", 
            function(value, element, params) {
            
                value = value != "" ? value.split("/"):[];
                let value2 = jQuery(params).val() != "" ? jQuery(params).val().split("/"):[];
            
                if(value.length && value2.length){
                    value = `${value[2]}-${value[1]}-${value[0]}`;
                    value2 = `${value2[2]}-${value2[1]}-${value2[0]}`;
                    return new Date(value) <= new Date(value2);
                }
                return true;
            
            },'Must be less than or equal to {0}.');

            jQuery("#orderSearchForm").validate({
            
                rules: {
                    onkeyup: false,
                    onfocusout: false,
                    from_date: {
                        required: true,
                        lessThan: "#to_date"
                    },
                    to_date: {
                        required: function(e){
                                if(jQuery("#orderSearchForm").find('#from_date').val() != ""){
                                    return true;
                                }else{
                                    return false;
                                }
                        },
                        greaterThan: "#from_date"
                    },         
                },
                messages: {
                    from_date: {
                        required: "Please Select From Date",
                        lessThan: "From Date must be less then to date"
                    },
                    to_date: {
                        required: "Please Select To Date",
                        greaterThan: "To Date must be greater then from date"
                    }
                },
                submitHandler: function(form) {
                    var searchForm = jQuery('#orderSearchForm');
                    var from_date = jQuery('#from_date').val();
                    var to_date = jQuery('#to_date').val();
                    if(from_date != "" && to_date != ""){
                        LoadDataTable();
                    }

                }
            });
        
            jQuery('#from_date').on('change',(function(e) {
                var from_date = jQuery('#from_date').val();
                jQuery(".date-picker:not([readonly])").datepicker({
                        dateFormat: "dd/mm/yy",
                        minDate: from_date
                });
            }));

            jQuery('#reset-order-data').on('click',function(){
                <?php  Session::forget('serchData'); ?>
                var searchForm = jQuery("#orderSearchForm");
                searchForm.find('#from_date').val(returnCurrentDate());
                searchForm.find('#to_date').val('');
                jQuery('#dyntable').DataTable().clear().destroy();
                table = jQuery('#dyntable').DataTable({
                        "lengthMenu": [ 10],
                        "pageLength":25
                    });
                    
            });

            jQuery('#print-order-data').on('click',function(){            
                var searchForm = jQuery("#orderSearchForm");
              
            });
});

// preview Rt Report Logics Here

    jQuery('#print-order-data').on('click', function() {
        var selectedIds = [];
        jQuery('.so_check:checked').each(function() {
            selectedIds.push(jQuery(this).val());
            // selectedIds.push({
            //         id: jQuery(this).val(),          // Value of the checkbox
            //         name: jQuery(this).data('name'),  // Value of the data-name attribute
            //         type: 'so',
            // });
        });
        if(selectedIds.length > 0) {
              jQuery('#show-progress').addClass('loader-progress-whole-page');
            //  jQuery('#print-order-data').prop('disabled',true).removeClass('btn btn-primary').addClass("loading");

           // var url = RouteBasePath + '/merge_and_print-rt_report_details/' + selectedIds;
            // var  as1 = {
            //     report_data: JSON.stringify(selectedIds), // Example: Reusing the same array for another key
            // };
            // var  formdata = new URLSearchParams(as1).toString();
            // var url = RouteBasePath + '/merge_and_print_report';   // new 

            var formdata = {
                report_data: JSON.stringify(selectedIds)
            };



            jQuery.ajax({
                 url: RouteBasePath + '/merge_and_print_report',
                type: 'post',
                data: formdata,
                headers: headerOpt,
                success: function(response) {
                    var fileUrl = response.outputPath;
                    window.open(fileUrl,'_blank');

                   /* setTimeout(function() {
                    jQuery.ajax({
                        url:  RouteBasePath + '/delete-file',
                        type: 'POST',
                        headers: headerOpt,
                        data: { filePath: fileUrl },  
                        success: function(res) {
                            console.log('File Deleted Successfully');
                        },
                        error: function(xhr, status, error) {
                            console.error('File Deletion Failed');
                        }
                    });
                }, 1800000);*/
                // jQuery('#print-order-data').prop('disabled',false).removeClass('loading').addClass("btn btn-primary");
                 jQuery('#show-progress').removeClass('loader-progress-whole-page');

                },
                error: function(xhr, status, error) {
                }
            });
        } else {
            jAlert('Please Select At Least One Report');
        }
    });

     // download separate pdfs 
     jQuery('#download-order-data').on('click', function() {
        
        var selectedIds = [];
        jQuery('.so_check:checked').each(function() {
                   selectedIds.push({
                    id: jQuery(this).val(),          // Value of the checkbox
                    name: jQuery(this).data('name'),  // Value of the data-name attribute
                    type: 'so',
                });

        });
      
        if(selectedIds.length > 0) {
            jQuery('#download-order-data').prop('disabled',true).removeClass('btn btn-primary').addClass("loading");
            var  as1 = {
                report_data: JSON.stringify(selectedIds), // Example: Reusing the same array for another key
            };

            var  formdata = new URLSearchParams(as1).toString();
            var url = RouteBasePath + '/download_single_reports';   // new 

            jQuery.ajax({
                url: url,  
                //type: 'GET',
                type: 'post',
                data: formdata,
                headers: headerOpt,
                success: function(response) {

                    // open in new tab
                    /*  if (response.files && response.files.length > 0) {
                            response.files.forEach(function (file) {
                                window.open(file.url, '_blank'); // Open each PDF in a new tab
                            });
                        } else {
                            alert("No files found to download.");
                    }*/

                    // direct download
                    if (response.files && response.files.length > 0) {
                        response.files.forEach(function (file) {
                            let a = document.createElement('a'); // Create an anchor element
                            a.href = file.url;  // Set the file URL
                            a.download = file.name; // Set the file name
                            document.body.appendChild(a); // Append to the body
                            a.click(); // Trigger the download
                            document.body.removeChild(a); // Remove the anchor after download
                        });
                      jQuery('#download-order-data').prop('disabled',false).removeClass('loading').addClass("btn btn-primary");
                    } else {
                        alert("No files found to download.");
                    }
                },
                error: function(xhr, status, error) {
                }
            });
        } else {
            jAlert('Please Select At Least One Report');
        }
     });

 
jQuery('#checkall-so').click(function () {
    if (jQuery(this).is(':checked')) {
        jQuery("#dyntable").find("[id^='so_id_']").prop('checked', true).trigger('change');
    } else {
        jQuery("#dyntable").find("[id^='so_id_']").prop('checked', false).trigger('change');
    }
});

</script>
@endsection
