@extends('layouts.app',['pageTitle' => 'QC Approval Summary'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>QC Approval Summary</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">QC Approval Summary</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
            <form id="psfdSearchForm" name="psfdSearchForm" class="stdform">

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
                        <th class="head0">QC No.</th>
                        <th class="head0">Date</th>
                        <th class="head0">GRN No.</th>
                        <th class="head0">GRN Date</th>
                        <th class="head0">Supplier</th>
                        <th class="head0">PO No.</th>
                        <th class="head0">PO Date</th>
                        <th class="head0">Item</th>
                        <th class="head0">Code</th>
                        <th class="head0">GRN Qty.</th>
                        <th class="head0">QC Qty.</th>
                        <th class="head0">Unit</th>
                        <th class="head0">Rej. Reason</th>
                        <!-- <th class="head0" style="display:none;"></th> -->
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
        jQuery('.qc_approval_summary').click();
    });
         setTimeout(() => {
            DataYearWise();
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

            var data = new FormData(document.getElementById('psfdSearchForm'));
            var formValue = Object.fromEntries(data.entries());

            jQuery('#dyntable').DataTable({

                "processing": true,
                "serverSide": true,
                "scrollX" : true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                "order": [[ 0, 'asc' ]],
                // "order": [[ 1, 'desc' ],[ 15, 'desc' ]],
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'QC Approval Summary',
                        title:"",
                        className: 'qc_approval_summary d-none',
                        exportOptions: {
                            columns: ':not(.export)',
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-qc_approval_summary') }}",
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

                columns:[
                            { data: 'qc_number', name: 'qc_approval.qc_number' },
                            { data: 'qc_date', name: 'qc_approval.qc_date' },
                            { data: 'grn_number', name: 'grn_material_receipt.grn_number' },
                            { data: 'grn_date', name: 'grn_material_receipt.grn_date' },
                            { data: 'supplier_name', name: 'suppliers.supplier_name' },
                            { data: 'po_number', name: 'purchase_order.po_number' },
                            { data: 'po_date', name: 'purchase_order.po_date' },
                            { data: 'item_name', name: 'items.item_name' },
                            { data: 'item_code', name: 'items.item_code' },
                            { data: 'grn_qty', name: 'material_receipt_grn_details.grn_qty' },
                            { data: 'qc_qty', name: 'qc_approval.qc_qty' },
                            { data: 'unit_name', name: 'units.unit_name' },
                            { data: 'rejection_reason', name: 'qc_approval.rejection_reason' },
                        ],
                        initComplete: function () {
                                // Exclude first column (index 0) from search
                                initColumnSearch('#dyntable', []);
                        },
            });

    }


    // search form logic

    jQuery.validator.addMethod("greaterThan",
    function(value, element, params) {

        value = value != "" ? value.split("/"):[];
        let value2 = jQuery(params).val() != "" ? jQuery(params).val().split("/"):[];

        if(value.length && value2.length){
            value = `${value[2]}-${value[1]}-${value[0]}`;
            value2 = `${value2[2]}-${value2[1]}-${value2[0]}`;

            return new Date(value) >= new Date(value2);
        }

        return true;

    },'Must be greater than equal to {0}.');

    jQuery.validator.addMethod("lessThan",
    function(value, element, params) {

        value = value != "" ? value.split("/"):[];
        let value2 = jQuery(params).val() != "" ? jQuery(params).val().split("/"):[];

        if(value.length && value2.length){
            value = `${value[2]}-${value[1]}-${value[0]}`;
            value2 = `${value2[2]}-${value2[1]}-${value2[0]}`;

            return new Date(value) <= new Date(value2);
        }
        return true;

    },'Must be less than or equal to {0}.');

    jQuery("#psfdSearchForm").validate({
        onkeyup: false,
        onfocusout: false,

    rules: {

        from_date: {

            required: function(e){
                if(jQuery("#psfdSearchForm").find('#trans_to_date').val() != ""){
                    return true;
                }else{
                    return false;
                }
            },
            lessThan: "#trans_to_date"

        },

        to_date: {

            required: function(e){
                if(jQuery("#psfdSearchForm").find('#trans_from_date').val() != ""){
                    return true;
                }else{
                    return false;
                }
            },
            greaterThan: "#trans_from_dates"

        },

    },

    messages: {

        from_date: {

            required: "Please Enter From Date",
            lessThan: "From Date must be less then to date"

        },

        to_date: {

            required: "Please Enter To Date",
            greaterThan: "To Date must be greater then from date"

        }

    },
    submitHandler: function(form) {
        var searchForm = jQuery('#psfdSearchForm');
        loadDataTable();
    }

    });

    jQuery('#reset-order-data').on('click',function(){
        var searchForm = jQuery("#psfdSearchForm");
        searchForm.find('#trans_to_date').val('');
        searchForm.find('#trans_from_date').val('');
        DataYearWise();
        loadDataTable();
    });

}); // .ready
</script>
<script src="{{ asset('js/view/suggestreport.js?ver='.getJsVersion()) }}"></script>
@endsection
