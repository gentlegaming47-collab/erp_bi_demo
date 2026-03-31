@extends('layouts.app',['pageTitle' => 'Item'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Item</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
          
            <a href="javascript:;" class="btn btn-inverse pre2" id="export-excel">Export</a>

           @if(hasAccess("item","add"))
           <a href="{{ route('add-item') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Item</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
        @if(Session::has('success'))
            <div class="alert alert-success item_success_fadeout">{{ Session::get('success') }}</div>
        @endif
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>
                    <th class="head1">Item</th>
                    <th class="head0">Code</th>                 
                    <th class="head1">Group </th>   
                    <th class="head1">Unit</th>
                    <th class="head1">Min. Stock </th>
                    <th class="head1">Max. Stock </th>
                    <th class="head1">Re-Order </th>
                    <th class="head1">HSN Code</th>
                    <th class="head1">Rate/Unit</th>
                    <th class="head1">Item Mapping ? </th>
                    <th class="head1">Show In Print ? </th>
                    <th class="head1">Fitting Item ?</th>
                    <th class="head1">Print In Disp. Plan ?</th>
                    <th class="head1">Own Mfg. ?</th>
                    <th class="head1">Allow Above MSL ?</th>
                    <th class="head1">Service Item ?</th>
                    <th class="head1">QC Required ?</th>
                    <th class="head1">Allow Partial Disp. ?</th>
                    <th class="head1">Sec. Unit ?</th>
                    <th class="head1">Sec. Unit</th>
                    <th class="head1">Status</th>
                    {{-- <th class="head1">Secondary Unit</th>
                    <th class="head1">Qty.</th>
                    <th class="head1">Unit</th> --}}
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
{{-- <!-- buttons -->  jquery button --}}


<script>
    jQuery('#export-excel').on('click',function(){
    jQuery('.export_item').click();
});
jQuery(document).ready(function() {
var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};
var table= jQuery('#dyntable').DataTable({

"processing": true,
"serverSide": true,
"scrollX" : true,
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
                    filename: 'Item',
					title:"",
                    className: 'export_item d-none',
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
        url: "{{ route('listing-item') }}",
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
    { data: 'item_name', name: 'item_name', },    
    { data: 'item_code', name: 'item_code', },        
    { data: 'item_group_name', name: 'item_groups.item_group_name', },      
    { data: 'unit_name', name: 'units.unit_name', },
    { data: 'min_stock_qty', name: 'min_stock_qty', },
    { data: 'max_stock_qty', name: 'max_stock_qty', },
    { data: 're_order_qty', name: 're_order_qty', },
    { data: 'hsn_code', name: 'hsn_code.hsn_code', },
    { data: 'rate_per_unit', name: 'rate_per_unit', },    
    { data: 'require_raw_material_mapping', name: 'require_raw_material_mapping', },
    { data: 'show_item_in_print', name: 'show_item_in_print', },
    { data: 'fitting_item', name: 'fitting_item', },
    { data: 'print_dispatch_plan', name: 'print_dispatch_plan', },
    { data: 'own_manufacturing', name: 'own_manufacturing', },
    { data: 'dont_allow_req_msl', name: 'dont_allow_req_msl', },
    { data: 'service_item', name: 'service_item', },
    { data: 'qc_required', name: 'qc_required', },
    { data: 'allow_partial_dispatch', name: 'allow_partial_dispatch', },
    { data: 'secondary_unit', name: 'secondary_unit', },
    { data: 'second_unit', name: 'second_unit', },
    { data: 'status', name: 'items.status', },
   /* { data: 'secondary_unit', name: 'secondary_unit', },
    { data: 'qty', name: 'qty', },
    { data: 'second_unit', name: 'second_unit.unit_name', },*/
    { data: 'last_by_user_id', name: 'last_by_user_id', },
    { data: 'last_on', name: 'items.last_on', },
    { data: 'created_by_user_id', name: 'created_by_user_id', },
    { data: 'created_on', name: 'items.created_on', }
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
                url: "{{ route('remove-item') }}",
                type: 'GET',
                data: "id="+data["id"],
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

setInterval(function(){ jQuery(".item_success_fadeout").fadeOut(); }, 3000);

</script>
@endsection
