@extends('layouts.app',['pageTitle' => 'Company Year'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Company Year</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
           @if(hasAccess("company_year","add"))
           <a href="{{ route('add-company_year') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Company Year</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive">
            <thead>
                <tr class="main-header">
                    <th class="head0" width="4%">Actions</th>
                    <!--<th class="head1 center" width="6%">Default Year</th>-->
                    <th class="head1" width="14%">Year</th>
                    <th class="head0" width="14%">Start Date</th>
                    <th class="head1" width="14%">End Date</th> 
                    <th class="head0" width="9%">Modified by</th>
                    <th class="head1" width="10%">Modified on</th>
                    <th class="head0" width="9%">Created by</th>
                    <th class="head1" width="10%">Created on</th>
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
    jQuery('.export_company_year').click();
});
var table= jQuery('#dyntable').DataTable({
"processing": true,
"serverSide": true,
 "scrollX":true,
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "scrollX":true,
"order": [[ 1, 'asc' ]],
//  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
            [
                {
                    extend:'excel',
                    filename: 'Company Year',
					title:"",
                    className: 'export_company_year d-none',
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
        url: "{{ route('listing-company_year') }}",
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
        width: '4%',
        orderable: false,
        searchable: false,
    },
    // { data: null, name: 'default_year', className: 'center', width: '6%',
    //     render: function(data,type,row,meat){
    //         if(row.default_year == "Y" ){
    //             return "<i class='iconfa-ok text-success f-md'></i>";
    //         }else{
    //             return "<i class='iconfa-remove text-error f-md'></i>";
    //         }  
    //     }
    // },
    { data: 'year', name: 'company_years.year', width: '14%', },
    { data: 'startdate', name: 'company_years.startdate', width: '14%', className: "wsn"},
    { data: 'enddate', name: 'company_years.enddate', width: '14%', className: "wsn"},  
    { data: 'last_by', name: 'last_by', width: '9%', },
    { data: 'last_on', name: 'company_years.last_on', width: '10%', className: "wsn"},
    { data: 'created_by', name: 'created_by', width: '9%', },
    { data: 'created_on', name: 'company_years.created_on', width: '10%', className: "wsn"} 
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
				url: "{{ route('remove-company_year') }}",
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