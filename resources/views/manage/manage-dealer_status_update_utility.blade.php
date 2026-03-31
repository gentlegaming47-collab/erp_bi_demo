
@extends('layouts.app',['pageTitle' => 'Dealer Status Update Utility'])

@section('header')


<style>
    
        #approvalTable_filter label{
          width: auto;
          white-space: nowrap;
          padding: 0;
        }
      
        #approvalTable_length label{
          width: 0;
          white-space: nowrap;
          float: none;
          text-align: unset;
          padding: 0;
        }
        #approvalDataTable_filter label{
          width: auto;
          white-space: nowrap;
          padding: 0;
        }
      
        #approvalDataTable_length label{
          width: 0;
          white-space: nowrap;
          float: none;
          text-align: unset;
          padding: 0;
        }

        .dataTables_filter {
            position: absolute;
            top: -35px;
            right: 10px;
        }
    </style>

<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Dealer Status Update Utility</li>
</ul>
@endsection


@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            {{-- <div class="btn-group"> <a href="{{ route('manage-'.$getFristPageName.'_report') }}" class="btn btn-inverse">Back</a> </div> --}}      
        </div>      
        <h4 class="widgettitle">Dealer Status Update Utility</h4>
        
    </div>
    <div class="widgetcontent">
        <form id="dealerUtilityForm" name="dealerUtilityForm" class="stdform" method="post">
            
        @csrf

            <div class="row">
                <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="dealer_status">Status</label>
                                <div class="controls">
                                    <span class="formwrapper">
                                        <select data-placeholder="Select Status" name="dealer_status" id="dealer_status" class="chzn-select">
                                         <option value="">Select Status</option> 
                                         <option value="active">Active</option> 
                                         <option value="deactive">Deactive</option>
                                        </select>
                                    </span>
                                </div>
                        </div>
                </div> 

                <div class="span-6">
                        <div class="par control-group form-control">
                          
                            <span class="formwrapper">
                                <button id="search" type="submit" class="btn btn-primary">Apply</button>
                            </span>
                        </div>
                </div>
            </div><br>

            <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
                <thead>
                    <tr class="main-header">
                        <th><input type="checkbox" name="checkall" class="simple-check" id="checkall"/></th>
                        <th>Status</th>
                        <th>Dealer</th>
                        <th>Dealer Code</th>
                        <th>Village</th>
                        <th>Pin Code</th>                    
                        <th>Taluka</th>                    
                        <th>District</th>                    
                        <th>State</th>                    
                        <th>Country</th>                    
                        <th>Mobile</th>
                        <th>Email</th>                    
                        <th>PAN</th>
                        <th>GSTIN</th>
                        <th>Aadhar No.</th>
                    </tr>
                </thead>
                <tbody>            
                    <tr> <td colspan="15" >No record found! </td> </tr>
                </tbody>        
            </table>
            <div class="note-text">Note: To search across multiple columns, add a space between words.</div>
      
         </div>
        </div>
    </form>
    </div>
</div>
@endsection


@section('scripts')
<script>
 
var table;
jQuery(document).ready(function() {

    var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};

     table = jQuery('#approvalTable').DataTable({
    "processing": true,
    "serverSide": false,
    "order": [[ 2, 'asc' ]],
    "scrollX":true,
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "scrollX":true,
        paging: false,  
            info: false,      
    lengthChange: false,
    ajax: {
            url: "{{ route('listing-dealer_status_update_utility') }}",
            type: "POST",
            headers: headerOpt,
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#dyntable_processing').hide();
                if(jqXHR.status == 401){
                    // toastError(jqXHR.statusText);
                    jAlert(jqXHR.statusText);
                }else{
                // toastError('Somthing went wrong!');
                    jAlert('Somthing went wrong!');
                }
                console.log(JSON.parse(jqXHR.responseText));
            }
    },

    columns: [
        {
            data: 'options',
            name: 'options',
            orderable: false,
            searchable: false,
        },
        { data: 'approval_status' ,name: 'dealers.approval_status' , },   
        { data: 'dealer_name' ,name: 'dealers.dealer_name' , },   
        { data: 'dealer_code' ,name: 'dealers.dealer_code' , },   
        { data: 'village_name' ,name: 'villages.village_name' , },    
        { data: 'pincode' ,name: 'dealers.pincode' , },    
        { data: 'taluka_name' ,name: 'talukas.taluka_name' , },
        { data: 'district_name' ,name: 'districts.district_name' ,},
        { data: 'state_name' ,name: 'states.state_name' ,},
        { data: 'country_name' ,name: 'countries.country_name' ,},
        { data: 'mobile_no' ,name: 'dealers.mobile_no' , },    
        { data: 'email' ,name: 'dealers.email' , },    
        { data: 'PAN' ,name: 'dealers.PAN' , },    
        { data: 'gst_code' ,name: 'dealers.gst_code' , },    
        { data: 'aadhar_no' ,name: 'dealers.aadhar_no' , },       
        
    ],
     initComplete: function () {
        // Exclude first column (index 0) from search
        initColumnSearch('#approvalTable', [0]);
    },
});


// // Add a custom search filter
// jQuery.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
//   var search = table.search().toLowerCase().trim(); // global search term
//   var status = data[1].toLowerCase(); // assuming status column index = 3
 
//   // If search is empty, keep all rows
//   if (!search) return true;
 
//   // If searching for active/inactive — match exact word only
//   if (search === "active" || search === "deactive") {
//     return status === search;
//   }
 
//   // Default behavior for other search terms
//   return data.join(' ').toLowerCase().includes(search);
// });
 
// // Redraw on every keypress
// jQuery('#approvalTable_filter input').off().on('keyup', function() {
//   table.search(this.value).draw();
// });

jQuery.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    if (settings.nTable.id !== 'approvalTable') {
        return true;
    }

    var globalSearch = table.search().toLowerCase().trim();
    var columnSearch = table.column(1).search().toLowerCase().trim();
    var rawStatus = data[1] || '';
    var statusText = rawStatus.replace(/<\/?[^>]+(>|$)/g, "").toLowerCase().trim();

    if (columnSearch) {
        if (
            ('active'.startsWith(columnSearch) && !statusText.startsWith(columnSearch)) ||
            ('deactive'.startsWith(columnSearch) && !statusText.startsWith(columnSearch))
        ) {
            return false;
        }

        if (!statusText.includes(columnSearch)) {
            return false;
        }
    }

    if (globalSearch) {
        if (
            'active'.startsWith(globalSearch) && !statusText.startsWith(globalSearch) &&
            statusText !== globalSearch
        ) {
            return false;
        }

        if (
            'deactive'.startsWith(globalSearch) && !statusText.startsWith(globalSearch) &&
            statusText !== globalSearch
        ) {
            return false;
        }

        var rowData = data.join(' ').replace(/<\/?[^>]+(>|$)/g, "").toLowerCase();
        if (!rowData.includes(globalSearch)) {
            return false;
        }
    }

    return true;
});

jQuery('#checkall').click(function () {

    if (jQuery(this).is(':checked')) {
        jQuery("#approvalTable").find("[id^='dealer_ids_']:not(.in-use)").prop('checked', true).trigger('change');
    } else {
        jQuery("#approvalTable").find("[id^='dealer_ids_']:not(.in-use)").prop('checked', false).trigger('change');
    }

});



var chkArr =  [];
var chkId =  [];
var chkCount = 0;

var coaPartValidator = jQuery("#dealerUtilityForm").validate({

    onclick:false,

    rules: {
        "dealer_id[]": {
            required: true
        },
        dealer_status: {
            required: function () {
                return jQuery("input[name='dealer_id[]']:checked").length > 0; 
            },
        },
    
    },
    
    messages: {
        "dealer_id[]": {
            required: "Please Select One Record",
        },
        dealer_status: {
            required: "Please Select Status"
        },
        
    },
   

    submitHandler: function (form) {
        dealer_data = [];
        // var index = 0;

        // jQuery('#approvalTable tbody tr').each(function (e) {
        //     var dealer_id = jQuery(this).find('input[name="dealer_id[]"]');

        //     if (jQuery(dealer_id).is(':checked')) {
        //         dealer_id = jQuery(dealer_id).val();   
        //         console.log(dealer_id)         

        //         dealer_data[index] = { 'dealer_id': dealer_id,};
        //         index++;
        //     }
        // });

        table.rows().every(function () {
            var data = this.node();
            var isChecked = jQuery(data).find('input[name="dealer_id[]"]').is(':checked');

            if (isChecked) {
                var dealer_id = jQuery(data).find('input[name="dealer_id[]"]').val();         
                var dealer_current_status = jQuery(data).find('input[name="dealer_current_status[]"]').val();         
                dealer_data.push({dealer_id : dealer_id , dealer_current_status : dealer_current_status});
            }       
        
        });

        if (!jQuery.isEmptyObject(dealer_data)) {

            let data = new FormData(document.getElementById('dealerUtilityForm'));
            let formValue = Object.fromEntries(data.entries());

            delete formValue["dealer_id[]"];

            formValue = Object.assign(formValue, { 'dealer_data': JSON.stringify(dealer_data) });
            var formdata = new URLSearchParams(formValue).toString();


            let formUrl = RouteBasePath + "/update-dealer_status_update_utility";
            jQuery.ajax({
                url: formUrl,
                type: 'POST',
                data: formdata,
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if (data.response_code == 1) {
                        jAlert(data.response_message, 'Alert Dialog', function (r) {
                            window.location.reload();
                        });
                    } else {
                        toastError(data.response_message);
                    }
                },

                error: function (jqXHR, textStatus, errorThrown) {
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
        } else {
            toastError("Please Select One Record");
        }       
    }
});


});       
// .ready
</script>
<script src="{{ asset('js/view/suggestreport.js?ver='.getJsVersion()) }}"></script>
@endsection
