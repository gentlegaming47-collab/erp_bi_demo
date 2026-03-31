@extends('layouts.app',['pageTitle' => 'Item Production (Assembly)'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Item Production (Assembly)</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">  
          <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-consumption">Consumption</a>       
          <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>       
           @if(hasAccess("item_assm_production","add"))
           <a href="{{ route('add-item_assm_production') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Item Production (Assembly)</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
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
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>                    
                    <th calss="head1">Sr. No. </th>
                    <th calss="head1">Prod. Date </th>
                    <th calss="head1">Item</th>
                    <th class="head0">Code</th>                                        
                    <th class="head0">Group</th>                                        
                    <th class="head0">Ass. Qty.</th>                                       
                    <th class="head0">Unit</th>                                       
                    <th class="head0">Sp. Note </th>                                                        
                    <th class="head0">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head0">Created by</th>
                    <th class="head0">Created on</th>
                    <th class="head0" style="display:none;"></th>
                </tr>
                </tr>
            </thead>
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
    jQuery('#export-excel').on('click',function(){
        jQuery('.export_item_assm_pro').click();
    });
    loadDataTable();
    jQuery(document).on('change', '#trans_from_date, #trans_to_date', function () {
        let fromValid = jQuery('#trans_from_date').valid();
        let toValid = jQuery('#trans_to_date').valid();

        if (fromValid && toValid) {
            loadDataTable();
        }
    });

    jQuery('#export-consumption').on('click', function() {
        var table = jQuery('#dyntable').DataTable();
        var globalSearch = table.search();
        var columnSearches = {};

        jQuery('.dataTables_scrollHeadInner tr.search-row th input').each(function(idx) {
            var value = jQuery(this).val().trim();
            if (value) {
                columnSearches[idx + 1] = value;
            }
        });

        var params = {};
        if (globalSearch) {
            params.global = globalSearch;
        }
        if (Object.keys(columnSearches).length > 0) {
            params.columns = columnSearches;
        }

        var fromDate = jQuery('#trans_from_date').val().trim();
        var toDate = jQuery('#trans_to_date').val().trim();

        if (fromDate) {
            params.trans_from_date = fromDate;
        }
        if (toDate) {
            params.trans_to_date = toDate;
        }

        var url = "{{ route('export-Item_Production_Assembly_Consumption') }}";
        if (Object.keys(params).length > 0) {
            url += '?' + jQuery.param(params);
        }
        window.location.href = url;
    });

    function loadDataTable() {

        if (jQuery.fn.DataTable.isDataTable('#dyntable')) {
                    jQuery('#dyntable').DataTable().destroy();
        }
        var data = new FormData(document.getElementById('psfdSearchForm'));
        var formValue = Object.fromEntries(data.entries());
        table = jQuery('#dyntable').DataTable({

        "processing": true,
        "serverSide": true,
        "scrollX" : true,
        "sScrollX": "100%",
        "sScrollXInner": "110%",
        "bScrollCollapse": true,
        // "order": [[ 1, 'asc' ]],
        "order": [[ 2, 'desc' ],[ 13, 'desc' ]],
        // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
            pageLength : 25,
            dom: 'Blfrtip',
            buttons:
                    [
                        {
                            extend:'excel',
                            filename: 'Item Production Assembly',
                            title:"",
                            className: 'export_item_assm_pro d-none',
                            exportOptions: {
                                columns: function(idx, data, node) {
                                    return idx !== 0 && table.column(idx).visible();
                                },
                                modifier: {
                                    page: 'all'
                                }
                        },
                        action: newexportaction
                            //   columns: function(idx, data, node) {
                            //    return idx !== 0 && table.column(idx).visible();
                            // },
                            // //columns: ':not(:eq(0))',
                            // modifier: {
                            //     page: 'all'
                            // }

                        }
                    ],

        ajax: {
                url: "{{ route('listing-item_assm_production') }}",
                type: "POST",
                headers: headerOpt,
                data: {
                        'trans_from_date': formValue.trans_from_date,
                        'trans_to_date':formValue.trans_to_date,
                    },
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
            { data: 'iap_number.' ,name: 'item_assembly_production.iap_number' , },

            { data: 'iap_date' ,name: 'item_assembly_production.iap_date' ,},   

            { data: 'item_name', name: 'item_name', },

            { data: 'item_code', name: 'items.item_code', },   

            { data: 'item_group_name', name: 'item_groups.item_group_name', },   

            { data: 'assembly_qty', name: 'item_assembly_production.assembly_qty', },   

            { data: 'unit_name', name: 'unit_name' },

            { data: 'special_notes', name: 'item_assembly_production.special_notes', },    
            
            { data: 'last_by_user_id', name: 'last_by_user_id', },
            { data: 'last_on', name: 'item_assembly_production.last_on', },
            { data: 'created_by_user_id', name: 'created_by_user_id', },
            { data: 'created_on', name: 'item_assembly_production.created_on', },
            { data: 'iap_sequence' ,name: 'item_assembly_production.iap_sequence',  visible:false},  
        ],
        initComplete: function () {
                // Exclude first column (index 0) from search
                initColumnSearch('#dyntable', [0]);
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

    jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
        var data = table.row( jQuery(this).parents('tr') ).data();

        jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
            if(r === true){
                jQuery.ajax({
                    url: "{{ route('remove-item_assm_production') }}",
                    type: 'GET',
                    data: "id="+data["iap_id"],
                    headers: headerOpt,
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if(data.response_code == 1){
                            // toastSuccess(data.response_message);
                            jAlert(data.response_message);
                            table.row(jQuery(this)).draw(false);
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
    });
});
</script>
@endsection
