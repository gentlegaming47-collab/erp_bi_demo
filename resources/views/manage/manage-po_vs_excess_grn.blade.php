@extends('layouts.app',['pageTitle' => 'PO v/s. Excess GRN'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>PO v/s. Excess GRN</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">PO v/s. Excess GRN</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
         <form id="psfdSearchForm" name="psfdSearchForm" class="stdform">
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
                    <th class="head1">GRN No.</th>
                    <th class="head1">GRN Date</th>
                    <th class="head1">Supplier</th>
                    <th class="head1">Challan/Bill No.</th>
                    <th class="head1">Date</th>
                    <th class="head1">PO No.</th>
                    <th class="head1">PO Date</th>
                    <th class="head1">Item</th>
                    <th class="head1">Code</th>
                    <th class="head1">Group</th>
                    <th class="head1">PO Qty.  </th>
                    <th class="head1">GRN Qty. </th>
                    <th class="head1">Excess Qty. </th>
                    <th class="head1">Unit </th>
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
var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};

jQuery('#export-excel').on('click',function(){
    jQuery('.export_po_excess').click();
});
loadDataTable();
    jQuery(document).on('change', '#trans_from_date, #trans_to_date', function () {
        let fromValid = jQuery('#trans_from_date').valid();
        let toValid = jQuery('#trans_to_date').valid();

        if (fromValid && toValid) {
            loadDataTable();
        }
    });
 function loadDataTable() {

    if (jQuery.fn.DataTable.isDataTable('#dyntable')) {
                jQuery('#dyntable').DataTable().destroy();
    }
    var data = new FormData(document.getElementById('psfdSearchForm'));
    var formValue = Object.fromEntries(data.entries());
    table= jQuery('#dyntable').DataTable({

    "processing": true,
    "serverSide": true,
    "scrollX" : true,
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
                filename: 'Po Vs Excess GRN',
                title:"",
                className: 'export_po_excess d-none',
                exportOptions: {
                        modifier: {
                            page: 'all'
                        }
                },
                action: newexportaction
            }
        ],
        ajax: {
                url: "{{ route('listing-po_vs_excess_grn') }}",
                type: "POST",
                headers: headerOpt,
                data: {
                        'trans_from_date': formValue.trans_from_date,
                        'trans_to_date':formValue.trans_to_date,
                    },
                error: function (jqXHR, textStatus, errorThrown){
                    jQuery('#dyntable_processing').hide();
                    if(jqXHR.status == 401){
                        jAlert(jqXHR.statusText);
                    }else{
                        jAlert('Somthing went wrong!');
                    }
                    console.log(JSON.parse(jqXHR.responseText));
                }
        },

        columns: [
        
            { data: 'grn_number', name: 'grn_material_receipt.grn_number', },
            { data: 'grn_date', name: 'grn_material_receipt.grn_date', },
            { data: 'supplier_name', name: 'suppliers.supplier_name', },
            { data: 'bill_no', name: 'grn_material_receipt.bill_no', },
            { data: 'bill_date', name: 'grn_material_receipt.bill_date', },
            { data: 'po_number', name: 'purchase_order.po_number', },
            { data: 'po_date', name: 'purchase_order.po_date', },
            { data: 'item_name', name: 'items.item_name', },
            { data: 'item_code', name: 'items.item_code', },
            { data: 'item_group_name', name: 'item_groups.item_group_name', },
            { data: 'po_qty', name: 'purchase_order_details.po_qty', },
            { data: 'grn_qty', name: 'material_receipt_grn_details.grn_qty', },
            { data: 'excess_qty', name: 'excess_qty', },
            { data: 'unit_name', name: 'units.unit_name', },

        ],
         initComplete: function () {
        // Exclude first column (index 0) from search
        initColumnSearch('#dyntable', []);
        },
    });
}
jQuery("#psfdSearchForm").validate({
        onkeyup: false,
        onfocusout: false,
        rules: {
            trans_from_date: {
                dateFormat: true,
                lessThan: "#trans_to_date",
            },
            trans_to_date: {
                dateFormat: true,
                greaterThan: "#trans_from_date",
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
            var searchForm = jQuery('#psfdSearchForm');
            loadDataTable();

        }

});
 


});
</script>
@endsection
