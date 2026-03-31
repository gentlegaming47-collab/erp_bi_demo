@extends('layouts.app',['pageTitle' => 'Location'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Location</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
            {{-- <a href="{{ route('export-Location') }}" class="btn btn-inverse pre2">Export</a> --}}
           @if(hasAccess("location","add"))
           <a href="{{ route('add-location') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Location</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>
                    <th class="head1">Location</th>
                    <th class="Head0">Type</th>                    
                    <th class="head1">Code</th>
                    <th class="Head1">Mfg. Process</th>                    
                    <th class="Head0">Village</th>
                    <th class="Head1">Taluka</th>
                    <th class="head0">District</th>
                    <th class="head1">State</th>
                    <th class="head0">Country</th>                    
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
    jQuery('.export_location').click();
});
var table= jQuery('#dyntable').DataTable({

"processing": true,
"serverSide": true,
"scrollX": true,
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
                    filename: 'Location',
					title:"",
                    className: 'export_location d-none',
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
        url: "{{ route('listing-location') }}",
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
    { data: 'location_name' ,name: 'locations.location_name', },
    { data: 'type' ,name: 'locations.type', },
    { data: 'location_code' ,name: 'locations.location_code', },
    // { data: 'customer_name' ,name: 'customers.customer_name', },
    { data: 'mfg_process' ,name: 'locations.mfg_process', },
    // { data: 'header_print' ,name: 'locations.header_print', },
    // { data: 'header_print', name: 'locations.header_print',
    //     render: function ( data, type, row, meta ) {
    //         return data ;
    //     }
    // },
    { data: 'village_name' ,name: 'villages.village_name', },
    { data: 'taluka_name' ,name: 'talukas..taluka_name', },
    { data: 'district_name' ,name: 'districts.district_name', },
    { data: 'state_name' ,name: 'states.state_name', },
    { data: 'country_name' ,name: 'countries.country_name', },
    // { data: 'status' ,name: 'locations.status', },
    { data: 'last_by_user_id',name: 'last_by_user_id',  },
    { data: 'last_on',name: 'locations.last_on',  },
    { data: 'created_by_user_id',name: 'created_by_user_id', },
    { data: 'created_on',name: 'locations.created_on',  },
],
 initComplete: function () {
        // Exclude first column (index 0) from search
        initColumnSearch('#dyntable', [0]);
    },
});

jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
				url: "{{ route('remove-location') }}",
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
</script>
@endsection
