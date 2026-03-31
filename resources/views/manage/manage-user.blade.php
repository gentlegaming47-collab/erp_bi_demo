@extends('layouts.app',['pageTitle' => 'User'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>User</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
           @if(hasAccess("user","add"))
           <a href="{{ route('add-user') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">User</h4>
    </div>
    <div class="widgetcontent  overflow-scroll">
        <table id="dyntable" class="table table-infinite table-bordered responsive">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>
                    <th class="head1" >Status</th>
                    <th class="head0" >User Name</th>
                    <th class="head0" >Type</th>
                    <th class="head0">Person</th>
                    <th class="head0" >Mobile</th>
                    {{-- <th class="head1" width="14%">User code</th> --}}
                    <!-- <th class="head1" width="10%">Person Name</th> -->
                    <th class="head0" >Email</th>
                    <th class="head1" >Modified by</th>
                    <th class="head0" >Modified on</th>
                    <th class="head1" >Created by</th>
                    <th class="head0" >Created on</th> 
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
    jQuery('.export_user').click();
});
var table= jQuery('#dyntable').DataTable({

"processing": true,
"serverSide": true,
"scrollX": true,
"sScrollX": "100%",
"sScrollXInner": "110%",
"bScrollCollapse": true,
"order": [[ 2, 'asc' ]],
//  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
            [
                {
                    extend:'excel',
                    filename: 'Users',
					title:"",
                    className: 'export_user d-none',
                     exportOptions: {
                        columns: ':not(:eq(0), :eq(1))', 
                        modifier: {
                            page: 'all'
                        }
                    },
                    action: newexportaction

                }
            ],

ajax: {
        url: "{{ route('listing-user') }}",
        type: "POST",
        headers :headerOpt,
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
        width: '7%',
        orderable: false,
        searchable: false,
    },
    { data: null, name: 'status', className: 'center', width: '5%',
        render: function(data,type,row,meat){
    
            if(row.status == "active" ){
                return "<i class='iconfa-ok text-success f-md'></i>";
            }else{
                return "<i class='iconfa-remove text-error f-md'></i>";
            }  
        }
    },
    { data: 'user_name', name: 'admin.user_name', },
    { data: 'user_type', name: 'user_type',  },
    { data: 'person_name', name: 'admin.person_name',  },
    { data: 'mobile_no', name: 'admin.mobile_no', },
    // { data: 'user_code', name: 'user_code', width: '14%',},
    { data: 'email_id', name: 'admin.email_id', },
    { data: 'last_by_user_id', name: 'last_by_user_id', },
    { data: 'last_on', name: 'admin.last_on', className: "wsn"},
    { data: 'created_by_user_id', name: 'created_by_user_id', },
    { data: 'created_on', name: 'admin.created_on', className: "wsn"}
],
 initComplete: function () {
        // Exclude first column (index 0) from search
        initColumnSearch('#dyntable', [0,1]);
    },
});

jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
				url: "{{ route('remove-user') }}",
				type: 'GET',
				data: "id="+data["id"],
                
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