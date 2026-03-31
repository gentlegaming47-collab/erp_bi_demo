@extends('layouts.app',['pageTitle' => 'Item to Item Mapping'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Item to Item Mapping</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
           <!-- <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a> -->
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-2">Export</a>
           @if(hasAccess("item_raw_material_mapping","add"))
           <a href="{{ route('add-item_raw_material_mapping') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Item to Item Mapping</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>
                    <th class="head1">Item </th>
                    <th class="head1"> Code </th>                    
                    <th class="head1">Group </th>                    
                    <th class="head1">Unit</th>
                    <th class="head1">Map Item Name</th>
                    <th class="head1">Map Qty.</th>
                    {{-- <th class="head1">Mapping Item Name</th>                   
                    <th class="head1">Mapping Unit</th>                    
                    <th class="head1">Mapping Qty</th>                     --}}
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
    jQuery('.mapping_item').click();
});
// jQuery('#export-2').on('click', function() {
//     var search = table.search();
//     if (search) {
//         window.location.href = "{{ route('export-Item-to-Item-Mapping') }}?search=" + encodeURIComponent(search);
//     } else {
//         window.location.href = "{{ route('export-Item-to-Item-Mapping') }}";
//     }
// });
jQuery('#export-2').on('click', function() {
    var table = jQuery('#dyntable').DataTable();
    var globalSearch = table.search();
    var columnSearches = {};

    // Minimal addition: Capture column search inputs
    jQuery('.dataTables_scrollHeadInner tr.search-row th input').each(function(idx) {
        var value = jQuery(this).val().trim();
        if (value) {
            columnSearches[idx + 1] = value; // Start from index 1 (skip Actions)
        }
    });

    var params = {};
    if (globalSearch) {
        params.global = globalSearch;
    }
    if (Object.keys(columnSearches).length > 0) {
        params.columns = columnSearches;
    }

    var url = "{{ route('export-Item-to-Item-Mapping') }}";
    if (Object.keys(params).length > 0) {
        url += '?' + jQuery.param(params);
    }
    window.location.href = url;
});
var table= jQuery('#dyntable').DataTable({

"processing": true,
"serverSide": true,
"scrollX" : true,
"sScrollX": "100%",
"sScrollXInner": "110%",
"bScrollCollapse": true,
"order": [[ 1, 'asc' ]],
// "order": [[ 2, 'desc' ],[ 3, 'desc' ]],
// "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
            [
                {
                    extend:'excel',
                    filename: 'Item To Item Mapping',
					title:"",
                    className: 'mapping_item d-none',
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
        url: "{{ route('listing-item-raw-matrial-mapping') }}",
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
    // { data: 'item_name', name: 'items.item_name', },    
    { data: 'name', name: 'name', },    
    { data: 'item_code', name: 'items.item_code', },        
    { data: 'item_group_name', name: 'item_groups.item_group_name', },        
    { data: 'unit_name', name: 'units.unit_name', },  
    // { data: 'm_items', name: 'm_items.item_name', },
    {
        data: 'm_items',
        name: 'm_items.item_name',
        render: function(data, type, row) {
            if (data) {
                var items = data.split('|').map(function(item) {
                    return '<li>' + item.trim() + '</li>';
                });
                return '<ul style="padding-left: 20px; margin:0;">' + items.join('') + '</ul>';
            }
            return '';
        }
    },
    // { data: 'm_rate_per_unit', name: 'm_items.rate_per_unit', },      
    // { data: 'raw_material_qty', name: 'item_groups.raw_material_qty', },

    // {
    //     data: 'raw_material_qty',
    //     name: 'item_raw_material_mapping_details.raw_material_qty',
    //     render: function(data, type, row) {
    //         if (data) {
    //             var quantities = data.split('|').map(function(qty) {
    //                 return '<li>' + qty.trim() + '</li>';
    //             });
    //             return '<ul style="padding-left: 20px; margin:0;">' + quantities.join('') + '</ul>';
    //         }
    //         return '';
    //     }
    // },
    {
        data: 'raw_material_qty',
        name: 'item_raw_material_mapping_details.raw_material_qty',
        render: function(data, type, row) {
            if (data && row.map_item_unit) {
                var quantities = data.split('|');
                var units = row.map_item_unit.split('|');
                var formatted = quantities.map(function(qty, index) {
                    var unit = units[index] ? units[index].trim() : '';
                    return '<li>' + qty.trim() + ' ' + unit + '</li>';
                });
                return '<ul style="padding-left: 20px; margin:0;">' + formatted.join('') + '</ul>';
            }
            return '';
        }
    },
    { data: 'last_by_user_id',name: 'last_by_user_id' , },
    { data: 'last_on',name: 'item_raw_material_mapping_details.last_on' , },
    { data: 'created_by_user_id',name: 'created_by_user_id' ,},
    { data: 'created_on',name: 'item_raw_material_mapping_details.created_on' , },
],
initComplete: function () {
    // Exclude first column (index 0) from search
    initColumnSearch('#dyntable', [0]);
}
});

jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    if(data["item_details_id"] != undefined && data["item_details_id"] != ''){
        var formUrl = RouteBasePath + "/delete-details-item-raw-matrial-mapping";
        var id = "id="+data["item_details_id"];
    }else{
        var formUrl = RouteBasePath + "/delete-item_item_mapping";
        var id = "id="+data["item_id"];
    }

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
				url: formUrl,
				type: 'GET',
				data: id,
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
