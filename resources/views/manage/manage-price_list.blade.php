@extends('layouts.app',['pageTitle' => 'Price List'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Price List</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <!-- <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export 1</a> -->
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-2">Export</a>
           @if(hasAccess("price_list","add"))
           <a href="{{ route('add-price_list') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Price List</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>
                    <th class="head1">Cust. Group </th>
                    {{-- <th class="head1">Item Name</th>
                    <th class="head1">Item Code</th>
                    <th class="head1">Sales Rate</th>
                    <th class="head1">Unit</th>
                    <th class="head1">Item Group</th> --}}
                    <th class="head0">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head0">Created by</th>
                    <th class="head1">Created on</th>
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
jQuery('#export-excel').on('click',function(){
    jQuery('.export_price_list').click();
});

// jQuery('#export-2').on('click', function() {
//     var search = table.search();
//     if (search) {
//         window.location.href = "{{ route('export-PriceList') }}?search=" + encodeURIComponent(search);
//     } else {
//         window.location.href = "{{ route('export-PriceList') }}";
//     }
// });

jQuery('#export-2').on('click', function() {
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

    var url = "{{ route('export-PriceList') }}";
    if (Object.keys(params).length > 0) {
        url += '?' + jQuery.param(params);
    }
    window.location.href = url;
});

var table= jQuery('#dyntable').DataTable({

"processing": true,
"serverSide": true,
"scrollX":true,
"sScrollX": "100%",
"sScrollXInner": "110%",
"bScrollCollapse": true,
"order": [[ 1, 'asc' ]],
// "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
            [
                {
                    extend:'excel',
                    filename: 'Price List',
					title:"",
                    className: 'export_price_list d-none',
                    exportOptions: {
                        columns: ':not(:eq(0))',
                        modifier: {
                            page: 'all'
                        }
                    },
                    action: newexportaction

                }
            ],

ajax: {
        url: "{{ route('listing-price_list') }}",
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
    { data: 'customer_group_name', name: 'customer_groups.customer_group_name', },
    // { data: 'item_name', name: 'items.item_name', },
    // { data: 'item_code', name: 'items.item_code', },
    // { data: 'sales_rate', name: 'price_list_details.sales_rate', },
    // { data: 'unit_name', name: 'units.unit_name', },
    // { data: 'item_group_name', name: 'item_groups.item_group_name', },
    { data: 'last_by_user_id', name: 'last_by_user_id', },
    { data: 'last_on', name: 'price_list.last_on', },
    { data: 'created_by_user_id', name: 'created_by_user_id', },
    { data: 'created_on', name: 'price_list.created_on', }
],
initComplete: function () {
    // Exclude first column (index 0) from search
    initColumnSearch('#dyntable', [0]);
}


});

jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();
    // console.log(data);
    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
                url: "{{ route('delete-price_list') }}",
                type: 'GET',
                data: "id="+data["pl_id"],
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
