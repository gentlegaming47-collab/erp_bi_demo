@extends('layouts.app',['pageTitle' => 'MIS Category'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>MIS Category</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
           @if(hasAccess("mis_category","add"))
           <a href="{{ route('add-mis_category') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">MIS Category</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>
                    <th class="head1">MIS Category</th>
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
    jQuery('.export_mis_category').click();
});
var table= jQuery('#dyntable').DataTable({

"processing": true,
"serverSide": true,
"scrollX":true,
"sScrollX": "100%",
"sScrollXInner": "110%",
"bScrollCollapse": true,
"order": [[ 1, 'asc' ]],
//"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
            [
                {
                    extend:'excel',
                    filename: 'MIS Category',
					title:"",
                    className: 'export_mis_category d-none',
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
        url: "{{ route('listing-mis_category') }}",
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
    { data: 'mis_category', name: 'mis_category.mis_category', },
    { data: 'last_by_user_id', name: 'last_by_user_id', },
    { data: 'last_on', name: 'mis_category.last_on', },
    { data: 'created_by_user_id', name: 'created_by_user_id', },
    { data: 'created_on', name: 'mis_category.created_on', }
],
initComplete: function () {
    // Exclude first column (index 0) from search
    initColumnSearch('#dyntable', [0]);
}


});

jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();
    // var data = table.row(jQuery(this).parents('tr')).data();
    // var rowToDelete = table.row(jQuery(this).parents('tr')); 

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
                url: "{{ route('remove-mis_category') }}",
                type: 'GET',
                data: "id="+data["id"],
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if(data.response_code == 1){
                        // toastSuccess(data.response_message);
                        jAlert(data.response_message);
                        //table.row(row).remove().draw(false);
                        table.row(jQuery(this)).draw(false);
                        // rowToDelete.remove().draw(false);

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
