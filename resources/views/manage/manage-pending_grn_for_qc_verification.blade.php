@extends('layouts.app',['pageTitle' => 'Pending GRN For QC Verification'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Pending GRN For QC Verification</li>
</ul>
@endsection

@section('content')
@include('modals.so_fitting_dispatch_for_report_modal')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Pending GRN For QC Verification</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
            <form id="posSearchForm" name="posSearchForm" class="stdform">
                <div class="row">
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="trans_from_date">From Date</label>
                                <span class="formwrapper">
                                   {{-- <input name="from_date" id="from_date"
                                   class="form-control dates-picker from-april"/> --}}
                                     <input name="trans_from_date" id="trans_from_date"
                                   class="form-control "/>
                                </span>
                        </div>
                    </div>

                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="trans_to_date">To Date</label>
                                <span class="formwrapper">
                                   <input name="trans_to_date" id="trans_to_date" class="form-control"/>
                                </span>
                        </div>
                    </div>
                </div>

            </form>
            <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
                <thead>
                    <tr class="main-header">
                        <th class="head1">GRN No.</th>
                        <th class="head1">Date</th>
                        <th class="head0">Supplier</th>
                        <th class="head0">PO No.</th>
                        <th class="head0">PO Date</th>
                        <th class="head0">Item</th>
                        <th class="head0">Code</th>
                        <th class="head0">GRN Qty.</th>
                        <th class="head0">Unit</th>
                        <th class="head0">Pend. QC Qty.</th>
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
    var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};

    jQuery('#export-excel').on('click',function(){
        jQuery('.pdple').click();
    });
         setTimeout(() => {
            loadDataTable();
        }, 1000);

        
    jQuery(document).on('change', '#trans_from_date, #trans_to_date', function () {
        let fromValid = jQuery('#trans_from_date').valid();
        let toValid = jQuery('#trans_to_date').valid();

        if (fromValid && toValid) {
            loadDataTable();
        }
    });

    function loadDataTable(){

            if (jQuery.fn.DataTable.isDataTable('#dyntable') ) {
                jQuery('#dyntable').DataTable().destroy();
            }

            var data = new FormData(document.getElementById('posSearchForm'));
            var formValue = Object.fromEntries(data.entries());

            table = jQuery('#dyntable').DataTable({

                "processing": true,
                "serverSide": true,
                "scrollX" : true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                "order": [[0, 'asc' ],[10 , 'asc']], // date wise desc
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Pending GRN For QC Verification',
                        title:"",
                        className: 'pdple d-none',
                        exportOptions: {
                            columns: function(idx, data, node) {
                                return idx !== 10 && table.columns(idx).visible();
                            },
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-pending_grn_for_qc_verification') }}",
                        type: "POST",
                        headers: headerOpt,
                        data : {
                                'trans_from_date':formValue.trans_from_date,
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
                            { data: 'grn_number', name: 'grn_material_receipt.grn_number' },
                            { data: 'grn_date', name: 'grn_material_receipt.grn_date' },
                            { data: 'supplier_name', name: 'suppliers.supplier_name' },
                            { data: 'po_number', name: 'purchase_order.po_number' },
                            { data: 'po_date', name: 'purchase_order.po_date' },
                            { data: 'item_name', name: 'items.item_name' },
                            { data: 'item_code', name: 'items.item_code' },
                            { data: 'grn_qty', name: 'material_receipt_grn_details.grn_qty' },
                            { data: 'unit_name', name: 'units.unit_name', },    
                            { data: 'pend_grn_qty', name: 'pend_grn_qty' },
                            { data: 'grn_sequence',name: 'grn_material_receipt.grn_sequence', visible: false } // hidden id column
                        ],
                 initComplete: function () {
                    // Exclude first column (index 0) from search
                    initColumnSearch('#dyntable', []);
                }
            });

    }

    jQuery("#posSearchForm").validate({

        submitHandler: function(form) {
            var searchForm = jQuery('#orderSearchForm');
            loadDataTable();
        }

    });


}); 
</script>
<script src="{{ asset('js/view/suggestreport.js?ver='.getJsVersion()) }}"></script>
@endsection
