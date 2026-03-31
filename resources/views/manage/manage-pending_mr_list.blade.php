@extends('layouts.app',['pageTitle' => 'Pending Material Request for SO'])

@section('header')
<style>
.dataTables_filter {
    top: 35px;
}
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Pending Material Request for SO</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">          
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Pending Material Request for SO</h4>
    </div>
    <div class="widgetcontent overflow-scroll">       
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr>
                    <th class="head0">MR No.</th>
                    <th class="head0">MR Date</th>
                    <th class="head0">Location</th>
                    <th class="head0">Amount</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
jQuery(document).ready(function() {
    var headerOpt = {
        'Authorization': 'Bearer {{ Auth::user()->auth_token }}',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    };
    jQuery('#export-excel').on('click', function() {
        jQuery('.export_report').click();
    });
    var table = jQuery('#dyntable').DataTable({
        "processing": true,
        "serverSide": true,
        // "scrollX": true,
        // "sScrollX": "100%",
        // "sScrollXInner": "110%",
        // "bScrollCollapse": true,
        "order": [
            [0, 'desc']
        ],
       
        //"scrollX":true,
        //"lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]],
        pageLength: 25,
        dom: 'Blfrtip',
        buttons: [{
            extend: 'excel',
            filename: 'Pending Material Request for SO',
            title: "",
            className: 'export_report d-none',
            exportOptions: {
                // columns: ':not(:eq(0))',
                modifier: {
                    page: 'all'
                }
            },
            action: newexportaction

        }],

        ajax: {
            url: "{{ route('listing-pending_material_request_for_so') }}",
            type: "POST",
            headers: headerOpt,
            error: function(jqXHR, textStatus, errorThrown) {
                jQuery('#dyntable_processing').hide();
                if (jqXHR.status == 401) {
                    toastError(jqXHR.statusText);
                } else {
                    toastError('Somthing went wrong!');
                }
                console.log(JSON.parse(jqXHR.responseText));
            }
        },

        columns: [
            {data: 'mr_number', name: 'material_request.mr_number',},
            {data: 'mr_date', name: 'material_request.mr_date',},
            {data: 'location_name', name: 'locations.location_name',},
            {data: 'location_name', name: 'locations.location_name',},
                    
        ],       
    });
});
</script>
@endsection