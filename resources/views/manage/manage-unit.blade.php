@extends('layouts.app',['pageTitle' => 'Unit'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Unit</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
         <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
           @if(hasAccess("unit","add"))
           <a href="{{ route('add-unit') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Unit</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>
                    <th class="head1">Unit</th>
                    <th class="head0">Modified by</th>
                    <th class="head1">Modified on</th>
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
    jQuery('.export_unit').click();
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
                    filename: 'Unit',
					title:"",
                    className: 'export_unit d-none',
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
        url: "{{ route('listing-unit') }}",
        type: "POST",
        headers: headerOpt,
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
     {
        data: 'options',
        name: 'options',
        width: '3%',
        orderable: false,
        searchable: false,

      
    },   
    { data: 'unit_name', name: 'unit_name', width: '31%', },    
    { data: 'last_by_user_id', name: 'last_by_user_id', width: '7%', },
    { data: 'last_on', name: 'units.last_on', width: '9%', className: "wsn"},
    { data: 'created_by_user_id', name: 'created_by_user_id', width: '7%', },
    { data: 'created_on', name: 'units.created_on', width: '9%', className: "wsn"}
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
				url: "{{ route('remove-unit') }}",
				type: 'GET',
				data: "id="+data["id"],
                headers: headerOpt,
                dataType: 'json',
                processData: false,
				success: function (data) {
                    if(data.response_code == 1){
			            toastSuccess(data.response_message);
                        table.row(jQuery(this)).draw(false);
                    }else{
			            toastError(data.response_message);
                    }
				},
                error: function (jqXHR, textStatus, errorThrown){
                    if(jqXHR.status == 401){
                        toastError(jqXHR.statusText);
                    }else{
                       toastError('Somthing went wrong!');
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