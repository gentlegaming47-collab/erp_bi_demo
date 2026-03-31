@extends('layouts.app',['pageTitle' => 'Item Stock Transfer'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Item Stock Transfer</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
           @if(hasAccess("item_stock_transfer","add"))
           <a href="{{ route('add-item_stock_transfer') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Item Stock Transfer</h4>
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
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>
                    <th class="head0">Sr. No.</th>
                    <th class="head0">Date</th>
                    <th calss="head1">Item </th>
                    <th class="head0">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head0">Created by</th>
                    <th class="head0">Created on</th>
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
var table;
jQuery(document).ready(function() {
    var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};

    jQuery('#export-excel').on('click',function(){
        jQuery('.export_ist').click();
    });
    loadDataTable();
    jQuery(document).on('change', '#trans_from_date, #trans_to_date', function () {
        let fromValid = jQuery('#trans_from_date').valid();
        let toValid = jQuery('#trans_to_date').valid();

        if (fromValid && toValid) {
            loadDataTable();
        }
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
            "order": [[ 2, 'desc' ],[ 8, 'desc' ]],
            pageLength : 25,
            dom: 'Blfrtip',
            buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Item Stock Transfer',
                        title:"",
                        className: 'export_ist d-none',
                        exportOptions: {
                            columns: function(idx, data, node) {
                                return idx !== 0 && table.column(idx).visible();
                            },
                            modifier: {
                                page: 'all'
                            }
                    },
                    action: newexportaction

                    }
                ],

            ajax: {
                    url: "{{ route('listing-item_stock_transfer') }}",
                    type: "POST",
                    headers: headerOpt,
                    data: {
                            'trans_from_date': formValue.trans_from_date,
                            'trans_to_date':formValue.trans_to_date,
                        },
                    error: function (jqXHR, textStatus, errorThrown){
                        jQuery('#dyntable_processing').hide();
                        if(jqXHR.status == 401){
                            jAlert(jqXHR.statusText);
                        }else{
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
                { data: 'ist_number' ,name: 'item_stock_transfer.ist_number' , },
                { data: 'ist_date' ,name: 'item_stock_transfer.ist_date' ,},    
                { data: 'secondary_item_name', name: 'item_details.secondary_item_name', },
                { data: 'last_by_user_id', name: 'last_by_user_id', },
                { data: 'last_on', name: 'item_stock_transfer.last_on', },
                { data: 'created_by_user_id', name: 'created_by_user_id', },
                { data: 'created_on', name: 'item_stock_transfer.created_on', },
                { data: 'ist_sequence' ,name: 'item_stock_transfer.ist_sequence',  visible:false},  
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
                    url: "{{ route('delete-item_stock_transfer') }}",
                    type: 'GET',
                    data: "id="+data["ist_id"],
                    headers: headerOpt,
                    dataType: 'json',
                    processData: false,
                    success: function (data) {
                        if(data.response_code == 1){
                            jAlert(data.response_message);
                            table.row(jQuery(this)).draw(false);
                        }else{
                            jAlert(data.response_message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown){
                        if(jqXHR.status == 401){
                            jAlert(jqXHR.statusText);
                        }else{
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


