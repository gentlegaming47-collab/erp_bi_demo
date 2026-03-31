@extends('layouts.app',['pageTitle' => 'Pending SO List'])

@section('header')
<style>
.dataTables_filter {
    top: 35px;
}
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Pending SO List</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">          
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Pending SO List</h4>
    </div>
    <div class="widgetcontent overflow-scroll">       
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr>
                    <th class="head0">MIS Type</th>
                    <th class="head0">Reg. No.</th>
                    <th class="head0">Farmer Name</th>
                    <th class="head0">Village</th>
                    <th class="head0">Taluka</th>
                    <th class="head0">District</th>
                    <th class="head0">Dealer Name</th>
                    <th class="head0">Sales Order Date</th>
                    <th class="head0">Sales Order Amount</th>
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
        "scrollX": true,
        "sScrollX": "100%",
        "sScrollXInner": "110%",
        "bScrollCollapse": true,
        "order": [
            [1, 'asc']
        ],
        "columnDefs": [
        { "orderable": false, "targets": 2 }  // Disable sorting on the first column (index 0)
        ],
        //"scrollX":true,
        //"lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]],
        pageLength: 25,
        dom: 'Blfrtip',
        buttons: [{
            extend: 'excel',
            filename: 'Pending SO List',
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
            url: "{{ route('listing-pending_so_for_dispatch') }}",
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
            {data: 'mis_category', name: 'mis_category.mis_category',},
            {data: 'customer_reg_no', name: 'sales_order.customer_reg_no',},
            {data: 'name', name: 'name',},
            { data: 'village_name', name: 'villages.village_name', },
            { data: 'taluka_name', name: 'talukas.taluka_name', },
            { data: 'district_name', name: 'districts.district_name', },
            { data: 'dealer_name', name: 'dealers.dealer_name', },
            { data: 'so_date', name: 'sales_order.so_date', },
            { data: 'net_amount', name: 'sales_order.net_amount', },
        ],
       
    });
});
</script>
@endsection