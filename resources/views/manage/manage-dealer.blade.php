@extends('layouts.app',['pageTitle' => 'Dealer'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Dealer</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <!-- <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a> -->
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-2">Export</a>
           @if(hasAccess("dealer","add"))
           <a href="{{ route('add-dealer') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Dealer</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head1">Actions</th>
                    <th class="head0">Dealer</th>
                    <th class="head0">Dealer Code</th>
                    <th class="head0">Village</th>
                    <th class="head1">Pin Code</th>                    
                    <th class="head1">Taluka</th>                    
                    <th class="head1">District</th>                    
                    <th class="head1">State</th>                    
                    <th class="head1">Country</th>                    
                    <th class="head1">Mobile</th>
                    <th class="head0">Email</th>                    
                    <th class="head1">PAN</th>
                    <th class="head0">GSTIN</th>
                    <th class="head1">Aadhar No.</th>
                    {{-- <th class="head1">Agreement Start date</th>--}}
                    <th class="head1">Status</th>
                    <th class="head1">Agreement End date</th> 
                    <th class="head1">Agreement Document</th>
                    <th class="head1">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head1">Created by</th>
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
// jQuery('#export-excel').on('click',function(){
//     jQuery('.export_dealer').click();
// });

// jQuery('#export-2').on('click', function() {
//     var search = table.search();
//     if (search) {
//         window.location.href = "{{ route('export-Dealer') }}?search=" + encodeURIComponent(search);
//     } else {
//         window.location.href = "{{ route('export-Dealer') }}";
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

    var url = "{{ route('export-Dealer') }}";
    if (Object.keys(params).length > 0) {
        url += '?' + jQuery.param(params);
    }
    window.location.href = url;
});

var table= jQuery('#dyntable').DataTable({
"processing": true,
"serverSide": true,
"order": [[ 1, 'asc' ]],
"scrollX":true,
"sScrollX": "100%",
"sScrollXInner": "110%",
"scrollX":true,
// "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
            [
                {
                    extend:'excel',
                    filename: 'Dealer',
					title:"",
                    className: 'export_dealer d-none',
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
        url: "{{ route('listing-dealer') }}",
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
    { data: 'dealer_name' ,name: 'dealers.dealer_name' , },   
    { data: 'dealer_code' ,name: 'dealers.dealer_code' , },   
    { data: 'village_name' ,name: 'villages.village_name' , },    
    { data: 'pincode' ,name: 'dealers.pincode' , },    
    { data: 'taluka_name' ,name: 'talukas.taluka_name' , },
    { data: 'district_name' ,name: 'districts.district_name' ,},
    { data: 'state_name' ,name: 'states.state_name' ,},
    { data: 'country_name' ,name: 'countries.country_name' ,},
    { data: 'mobile_no' ,name: 'dealers.mobile_no' , },    
    { data: 'email' ,name: 'dealers.email' , },    
    { data: 'PAN' ,name: 'dealers.PAN' , },    
    { data: 'gst_code' ,name: 'dealers.gst_code' , },    
    { data: 'aadhar_no' ,name: 'dealers.aadhar_no' , },       
  //  { data: 'aggrement_start_date' ,name: 'dealers.aggrement_start_date' , },       
    { data: 'approval_status' ,name: 'dealers.approval_status' , },       
    // { data: 'aggrement_end_date' ,name: 'dealers.aggrement_end_date' , },
    { data: 'agreement_end_date' ,name: 'agreement_end_date' , },       
    { data: 'agreement_document' ,name: 'agreement_document' ,},
    { data: 'last_by_user_id',name: 'last_by_user_id' , },
    { data: 'last_on',name: 'dealers.last_on' , },
    { data: 'created_by_user_id',name: 'created_by_user_id' ,},
    { data: 'created_on',name: 'dealers.created_on' , },
],
initComplete: function () {
    // Exclude first column (index 0) from search
    initColumnSearch('#dyntable', [0,16]);
}
});
jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
                url: "{{ route('remove-dealer') }}",
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
