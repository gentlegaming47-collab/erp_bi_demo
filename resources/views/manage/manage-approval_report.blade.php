@extends('layouts.app',['pageTitle' => 'Approval Report'])

@section('header')


<style>
     .dataTables_filter {
        top: 35px;
    }
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
    </style>

@php 
    $userType = Request::path();
    
    $getpagename = $userType == "sm_approval_report" ? "sm" : ($userType == "state_coordinator_approval_report" ? "state_coordinator" : ($userType == "zsm_approval_report" ? "zsm" : ($userType == "md_approval_report" ? "md" : ($userType == "gm_approval_report" ? "gm" : ""))));
    $getName = $userType == "sm_approval_report" ? "SM Approval Report" : ($userType == "state_coordinator_approval_report" ? "State Coordinator Approval Report" : ($userType == "zsm_approval_report" ? "ZSM Approval Report" : ($userType == "md_approval_report" ? "MD Approval Report" : ($userType == "gm_approval_report" ? "GM Approval Report" : ""))));
@endphp

<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>{{ $getName }}</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">    
          <!-- <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>  -->
           <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-2">Export</a>    
        </div>
         <div class="btn-group">    
            {{-- @if(hasAccess("country","add")) --}}
            {{-- <a href="{{ route('manage-'.$getpagename.'_approval') }}" class="btn btn-inverse">Add</a> --}}
            {{-- @endif --}}
        </div>       
        <h4 class="widgettitle">{{ $getName }}</h4>        
    </div>
    <div class="widgetcontent">                        
       <form id="psfdSearchForm" name="psfdSearchForm" class="stdform">
            <div class="row">
                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="trans_from_date">From Date</label>
                        <span class="formwrapper">
                        <input name="trans_from_date" id="trans_from_date" class="form-control manual-date"/>
                        </span>
                    </div>
                </div>
                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="trans_to_date">To Date</label>
                        <span class="formwrapper">
                        <input name="trans_to_date" id="trans_to_date" class="form-control manual-date"/>
                        </span>
                    </div>
                </div>
            </div>
        </form>
        <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Approval Date</th>
                    <th class="head0">From Location</th>
                    <th class="head0">MR No.</th>
                    <th class="head0">MR Date</th>
                    <th class="head0">To Location</th>   
                    <th class="head0">Approval By</th>
                    <th class="head0">Sp. Note</th>

                </tr>
            </thead>
            <tbody>
                <tr> <td colspan="17">No Data Available! </td> </tr>
            </tbody>        
        </table>
        <div class="note-text">Note: To search across multiple columns, add a space between words.</div>            
            
    </div>
</div>
@endsection




@section('scripts')
<script>
var table;
jQuery(document).ready(function() {
    var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};
    //jQuery('#export-excel').on('click',function(){
    // jQuery('.export_sm_approval').click();
    //});
    var path = "{{ Request::path() }}";
    loadDataTable();
    jQuery(document).on('change', '#trans_from_date, #trans_to_date', function () {
        let fromValid = jQuery('#trans_from_date').valid();
        let toValid = jQuery('#trans_to_date').valid();

        if (fromValid && toValid) {
            loadDataTable();
        }
    });

    // jQuery('#export-2').on('click', function() {
    //     // Correct DataTable reference
    //     var table = jQuery('#approvalTable').DataTable();

    //     // Global search
    //     var globalSearch = table.search();

    //     // Column searches (if per-column filters exist)
    //     var columnSearches = {};
    //     jQuery('.dataTables_scrollHeadInner tr.search-row th input').each(function(idx) {
    //         var value = jQuery(this).val().trim();
    //         if (value) {
    //             columnSearches[idx + 1] = value;
    //         }
    //     });

    //     // Build parameters
    //     var params = {};
    //     if (globalSearch) {
    //         params.global = globalSearch;
    //     }
    //     if (Object.keys(columnSearches).length > 0) {
    //         params.columns = columnSearches;
    //     }

    //     // Determine export route dynamically from PHP variable
    //     var exportUrl = '';
    //     @if($getpagename == 'sm')
    //         exportUrl = "{{ route('export-SM_Approval') }}";
    //     @elseif($getpagename == 'state_coordinator')
    //         exportUrl = "{{ route('export-State_Coordinator_Approval') }}";
    //     @elseif($getpagename == 'zsm')
    //         exportUrl = "{{ route('export-ZSM_Approval') }}";
    //     @elseif($getpagename == 'gm')
    //         exportUrl = "{{ route('export-GM_Approval') }}";
    //     @endif

    //     // Append filters if any
    //     if (Object.keys(params).length > 0) {
    //         exportUrl += '?' + jQuery.param(params);
    //     }

    //     // Redirect to export route
    //     window.location.href = exportUrl;
    // });

    jQuery('#export-2').on('click', function() {
        var table = jQuery('#approvalTable').DataTable();
        var globalSearch = table.search();
        var columnSearches = {};
        
        // 1. Get Column Searches
        jQuery('.dataTables_scrollHeadInner tr.search-row th input').each(function(idx) {
            var value = jQuery(this).val().trim();
            // DataTables column indexing starts from 0, but we need to map to server-side column index (starting from 1 or data index)
            // Assuming your server-side column mapping starts at 1
            if (value) {
                columnSearches[idx + 1] = value; 
            }
        });

        // 2. Build parameters including date filters
        var params = {};
        if (globalSearch) {
            params.global = globalSearch;
        }
        if (Object.keys(columnSearches).length > 0) {
            params.columns = columnSearches;
        }

        // --- NEW: ADD DATE FILTERS TO EXPORT PARAMETERS ---
        var fromDate = jQuery('#trans_from_date').val().trim();
        var toDate = jQuery('#trans_to_date').val().trim();

        if (fromDate) {
            params.trans_from_date = fromDate;
        }
        if (toDate) {
            params.trans_to_date = toDate;
        }
        // ---------------------------------------------------

        // Determine export route dynamically
        var exportUrl = '';
        @if($getpagename == 'sm')
            exportUrl = "{{ route('export-SM_Approval') }}";
        @elseif($getpagename == 'state_coordinator')
            exportUrl = "{{ route('export-State_Coordinator_Approval') }}";
        @elseif($getpagename == 'zsm')
            exportUrl = "{{ route('export-ZSM_Approval') }}";
        @elseif($getpagename == 'gm')
            exportUrl = "{{ route('export-GM_Approval') }}";
        @endif

        // Append filters if any
        if (Object.keys(params).length > 0) {
            // Encode the parameters correctly
            exportUrl += '?' + jQuery.param(params);
        }

        // Redirect to export route
        window.location.href = exportUrl;
    });

    // const url = window.location.pathname;
    // getLstname = url.split('/')[3];

    // let urlNm = getLstname.split("_")[0];
    // var fileName;
    // if(urlNm == "sm")
    //     var fileName = "SM Approval Report";
    // else if(urlNm == "zsm")
    //     var fileName = "ZSM Approval Report";
    // else if(urlNm == "md")
    //     var fileName = "MD Approval Report";



    // staging or testing code

    // const url = window.location.pathname; 
    // const segment = url.split('/')[1]; 
    // const getLstname = segment.split('_')[0]; 

    // // console.log(getLstname)
    // var fileName;
    // if(getLstname == "sm")
    //     var fileName = "SM Approval Report";
    // else if(getLstname == "zsm")
    //     var fileName = "ZSM Approval Report";
    // else if(getLstname == "md")
    //     var fileName = "MD Approval Report";



function loadDataTable() {

    if (jQuery.fn.DataTable.isDataTable('#approvalTable')) {
                jQuery('#approvalTable').DataTable().destroy();
    }
    var data = new FormData(document.getElementById('psfdSearchForm'));
    var formValue = Object.fromEntries(data.entries());
    table= jQuery('#approvalTable').DataTable({
    "processing": true,
    "serverSide": true,
    "scrollX":true,
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "bScrollCollapse": true,
    // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
        [
            {
                extend:'excel',
                
               // filename: fileName,
                title:"",
                className: 'export_sm_approval d-none',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    }
                },
                action: newexportaction
            }
        ],

        ajax: {
                url: "{{ route('listing-approval_report') }}",
                type: "POST",
                headers: headerOpt,
                data : {
                    'PageName':path,  
                    'trans_from_date': formValue.trans_from_date,
                    'trans_to_date':formValue.trans_to_date,          
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

            { data: 'approve_date' ,name: 'approve_date' , },
            { data: 'location_name' ,name: 'locations.location_name' , },
            { data: 'mr_number' ,name: 'material_request.mr_number' , },
            { data: 'mr_date' ,name: 'material_request.mr_date' , },
            { data: 'to_location' ,name: 'to_location.location_name' , },
            { data: 'approvad_by' ,name: 'approvad_by' , },
            { data: 'special_notes' ,name: 'material_request.special_notes' , },

        ],
        initComplete: function () {
            // Exclude first column (index 0) from search
            initColumnSearch('#approvalTable', []);
        }
    });
}
jQuery("#psfdSearchForm").validate({
        onkeyup: false,
        onfocusout: false,
        rules: {
            trans_from_date: {
                dateFormat: true,
                lessThan: "#trans_to_date",
            },
            trans_to_date: {
                dateFormat: true,
                greaterThan: "#trans_from_date",
            },
        },

        messages: {
            trans_from_date: {
                lessThan: "From Date Must Be Less Then To Date"
            },
            trans_to_date: {
                greaterThan: "To Date Must Be Greater Then From Date"
            }

        },
        submitHandler: function(form) {
            var searchForm = jQuery('#psfdSearchForm');
            loadDataTable();

        }

});
});




</script>
@endsection
