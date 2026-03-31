@extends('layouts.app',['pageTitle' => 'Replacement Item Decision'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Replacement Item Decision</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
           @if(hasAccess("replacement_item_decision","add"))
           <a href="{{ route('add-replacement_item_decision') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Replacement Item Decision</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>                                 
                    {{-- <th class="head1">Sr. No.</th>
                    <th class="head0">Date</th>                     --}}
                    <th class="head0">Replacement Type</th>          
                    <th class="head0">Customer</th>          
                    <th class="head0">Item</th>                                        
                    <th class="head0">Item Code</th>                                        
                    <th class="head0">Item Group</th>                                        
                    <th class="head0">Decision Qty.</th>  
                    <th class="head0">Unit</th>                                            
                    <th class="head0">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head0">Created by</th>
                    <th class="head0">Created on</th>
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
    jQuery('.export_rid').click();
});
var table= jQuery('#dyntable').DataTable({

    "processing": true,
    "serverSide": true,
    "scrollX" : true,
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "bScrollCollapse": true,
    "order": [[ 11, 'asc' ]],
    // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
            [
                {
                    extend:'excel',
                    filename: 'Replacement Item Decision',
					title:"",
                    className: 'export_rid d-none',
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
            url: "{{ route('listing-replacement_item_decision') }}",
            type: "POST",
            headers: headerOpt,
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
        // { data: 'so_mapping_number' ,name: 'so_mapping.so_mapping_number' , },
        // { data: 'mapping_date' ,name: 'so_mapping.mapping_date' ,},    
        { data: 'replacement_type_value_fix' ,name: 'replacement_item_decision.replacement_type_value_fix' ,},    
        { data: 'customer_name' ,name: 'so_mapping.customer_name' ,},    
        { data: 'item_name', name: 'items.item_name', },
        { data: 'item_code', name: 'items.item_code', },
        { data: 'item_group_name', name: 'item_groups.item_group_name', },
        { data: 'decision_qty', name: 'replacement_item_decision_details.decision_qty', },    
        { data: 'unit_name', name: 'units.unit_name', },      
        { data: 'last_by_user_id', name: 'last_by_user_id',},
        { data: 'last_on', name: 'replacement_item_decision.last_on',},
        { data: 'created_by_user_id', name: 'created_by_user_id',},
        { data: 'created_on', name: 'replacement_item_decision.created_on',}
    ],
    initComplete: function () {
    // Exclude first column (index 0) from search
        initColumnSearch('#dyntable', [0]);
    }   
});


jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
                url: "{{ route('remove-replacement_item_decision') }}",
                type: 'GET',
                data: "id="+data["replacement_id"],
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
