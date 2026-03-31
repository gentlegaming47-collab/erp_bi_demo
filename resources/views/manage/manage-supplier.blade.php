@extends('layouts.app',['pageTitle' => 'Supplier'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Supplier</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <!-- <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a> -->
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-2">Export</a>
           @if(hasAccess("supplier","add"))
           <a href="{{ route('add-supplier') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Supplier</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head1">Actions</th>
                    <th class="head0">Supplier</th>
                    <th class="head0">Supplier Code</th>
                    {{-- <th class="head1">Address</th> --}}
                    <th class="head1">Village</th>
                    <th class="head0">Pin Code</th>
                    <th class="head0">Taluka</th>
                    <th class="head1">District</th>
                    <th class="head1">State</th>
                    <th class="head0">Country</th>
                    <th class="head0">Person</th>
                    <th class="head0">Person Mobile</th>
                    <th class="head0">Person Email</th>
                    <th class="head0">Web</th>
                    <th class="head0">PAN</th>
                    <th class="head1">GSTIN</th>
                    <th class="head0">Pay. Terms </th>
                    <th class="head0">Status</th>
                    <th class="head0">Agreement Document </th>
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
//     jQuery('.export_supplier').click();
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

    var url = "{{ route('export-Supplier') }}";
    if (Object.keys(params).length > 0) {
        url += '?' + jQuery.param(params);
    }
    window.location.href = url;
});

var table= jQuery('#dyntable').DataTable({
"processing": true,
"serverSide": true,
"order": [[ 1, 'asc' ]],
//"scrollX":true,
"sScrollX": "100%",
"sScrollXInner": "110%",
"bScrollCollapse": true,
// "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
            [
                {
                    extend:'excel',
                    filename: 'Supplier',
					title:"",
                    className: 'export_supplier d-none',
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
        url: "{{ route('listing-supplier') }}",
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
    { data: 'supplier_name' ,name: 'suppliers.supplier_name' , },
    { data: 'supplier_code' ,name: 'suppliers.supplier_code' ,},
    // { data: 'address' ,name: 'suppliers.address' , },
    { data: 'village_name' ,name: 'villages.village_name' , },
    { data: 'pincode' ,name: 'suppliers.pincode' ,},
    { data: 'taluka_name' ,name: 'talukas.taluka_name' , },
    { data: 'district_name' ,name: 'districts.district_name' ,},
    { data: 'state_name' ,name: 'states.state_name' ,},
    { data: 'country_name' ,name: 'countries.country_name' ,},
    { data: 'contact_person' ,name: 'suppliers.contact_person' ,},
    { data: 'contact_person_mobile' ,name: 'suppliers.contact_person_mobile' ,},
    { data: 'contact_person_email_id' ,name: 'suppliers.contact_person_email_id' ,},
    { data: 'web_address' ,name: 'suppliers.web_address' ,},
    { data: 'PAN' ,name: 'suppliers.PAN' ,},
    { data: 'GSTIN' ,name: 'suppliers.GSTIN' ,},
    { data: 'payment_terms' ,name: 'suppliers.payment_terms' ,},
    { data: 'approval_status' ,name: 'suppliers.approval_status' ,},
    { data: 'agreement_document' ,name: 'agreement_document' ,},
    { data: 'last_by_user_id',name: 'last_by_user_id' , },
    { data: 'last_on',name: 'suppliers.last_on' , },
    { data: 'created_by_user_id',name: 'created_by_user_id' ,},
    { data: 'created_on',name: 'suppliers.created_on' , },
],
initComplete: function () {
    // Exclude first column (index 0) from search
    initColumnSearch('#dyntable', [0,17]);
}
});
jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
				url: "{{ route('remove-supplier') }}",
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
                        // toastError(jqXHR.statusText);/
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
