@extends('layouts.app',['pageTitle' => 'Pending PO List'])

@section('header')
<style>
.dataTables_filter {
    top: 35px;
}
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Pending PO List</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">          
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Pending PO List</h4>
    </div>
    <div class="widgetcontent overflow-scroll">  
          <form id="polistSearchForm" name="poapprovalSearchForm" class="stdform">
            <div class="row">
                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="trans_from_date">From Date</label>
                        <span class="formwrapper">
                        <input name="trans_from_date" id="trans_from_date" class="form-control manual-date"/>
                        </span>
                    </div>
                </div>
                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label" for="trans_to_date">To Date</label>
                        <span class="formwrapper">
                        <input name="trans_to_date" id="trans_to_date" class="form-control manual-date"/>
                        </span>
                    </div>
                </div>
            </div>
        </form>   
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Supplier</th>
                    <th class="head0">PO No.</th>
                    <th class="head0">PO Date</th>
                    <th class="head0">Ship To</th>
                    <th class="head0">Item</th>
                    <th class="head0">Code</th>
                    <th class="head0">Group</th>
                    <th class="head0">PO Qty.</th>
                    <th class="head0">Pend. PO Qty.</th>
                    <th class="head0">Unit</th>
                    <th class="head0">Del. Date</th>
                    <th class="head0" style="display:none;"></th>

                </tr>
            </thead>
        </table>
        <div class="note-text">Note: To search across multiple columns, add a space between words.</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var table;
jQuery(document).ready(function() {
    var headerOpt = {
        'Authorization': 'Bearer {{ Auth::user()->auth_token }}',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    };
    jQuery('#export-excel').on('click', function() {
        jQuery('.export_report').click();
    });
    jQuery(document).on('change', '#trans_from_date, #trans_to_date', function () {
        let fromValid = jQuery('#trans_from_date').valid();
        let toValid = jQuery('#trans_to_date').valid();

        if (fromValid && toValid) {
            loadDataTable();
        }
    });
     setTimeout(() => {
            loadDataTable();
        }, 1000);
        
    function loadDataTable(){

        if (jQuery.fn.DataTable.isDataTable('#dyntable') ) {
            jQuery('#dyntable').DataTable().destroy();
        }

        var data = new FormData(document.getElementById('polistSearchForm'));
        var formValue = Object.fromEntries(data.entries());

        table = jQuery('#dyntable').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "sScrollX": "100%",
            "sScrollXInner": "110%",
            "bScrollCollapse": true,
            "order": [[2, 'asc' ],[11, 'asc']], // date wise asc

            //"scrollX":true,
            //"lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]],
            pageLength: 25,
            dom: 'Blfrtip',
            buttons: [{
                extend: 'excel',
                filename: 'Pending PO List',
                title: "",
                className: 'export_report d-none',
                exportOptions: {
                    // columns: ':not(:eq(0))',
                    columns: function(idx, data, node) {
                        return idx !== 11 && table.columns(idx).visible();
                    },
                    modifier: {
                        page: 'all'
                    }
                },
                action: newexportaction

            }],

            ajax: {
                url: "{{ route('listing-pending_po_list') }}",
                type: "POST",
                headers: headerOpt,
                 data : {
                       'trans_from_date':formValue.trans_from_date,
                        'trans_to_date':formValue.trans_to_date,

                 },
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
                {data: 'supplier_name', name: 'suppliers.supplier_name',},
                {data: 'po_number', name: 'purchase_order.po_number',},
                {data: 'po_date', name: 'purchase_order.po_date',},
                {data: 'location_name', name: 'locations.location_name',},
                {data: 'item_name', name: 'items.item_name',},
                {data: 'item_code', name: 'items.item_code',},
                {data: 'item_group_name', name: 'item_groups.item_group_name',},
                {data: 'po_qty', name: 'purchase_order_details.po_qty',},
                {data: 'pend_po_qty', name: 'pend_po_qty',},
                {data: 'unit_name', name: 'units.unit_name',},
                {data: 'del_date', name: 'purchase_order_details.del_date',},
                { data: 'po_sequence' ,name: 'purchase_order.po_sequence' , visible:false},
                

            ],
            initComplete: function () {
                // Exclude first column (index 0) from search
                initColumnSearch('#dyntable', []);
            },
        
        });
    }
    jQuery("#polistSearchForm").validate({
        onkeyup: false,
        onfocusout: false,



    rules: {
        trans_from_date: {
          dateFormat: true,
          lessThan: "#trans_to_date"

        },
        trans_to_date: {
            dateFormat: true,
            greaterThan: "#trans_from_date"

        },

    },

    messages: {
        trans_from_date: {
            lessThan: "From Date Must Be Less Then To Date"

        },
        trans_to_date: {
            greaterThan: "To Date Must Be Greater Then From Date"

        }

    },
    submitHandler: function(form) {
        var searchForm = jQuery('#orderSearchForm');
        loadDataTable();
    }

    });
});
</script>
@endsection