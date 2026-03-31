@extends('layouts.app',['pageTitle' => 'Purchase Order Summary'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Purchase Order Summary</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Purchase Order Summary</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
            <form id="psfdSearchForm" name="psfdSearchForm" class="stdform">

                {{-- <div class="row">
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="from_date">From Date</label>
                                <span class="formwrapper">
                                  
                                     <input name="from_date" id="from_date"
                                   class="form-control report-date-picker from-april"/>
                                </span>
                        </div>
                    </div>

                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="to_date">To Date</label>
                                <span class="formwrapper">
                                   <input name="to_date" id="to_date" class="form-control  report-date-picker"/>
                                </span>
                        </div>
                    </div>
                </div> --}}

                <div class="row">
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="from_date">From Date</label>
                                <span class="formwrapper">
                                     <input name="trans_from_date" id="trans_from_date" class="form-control manual-date"/>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="to_date">To Date</label>
                                <span class="formwrapper">
                                   <input name="trans_to_date" id="trans_to_date" class="form-control manual-date"/>
                                </span>
                        </div>
                    </div>
                </div>
                    {{-- <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="po_number">PO No.</label>
                                <span class="formwrapper">
                                    <input type="text" name="po_number" id="po_number" onkeyup="suggestPONumber(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="po_number_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="pr_number">PR No.</label>
                                <span class="formwrapper">
                                    <input type="text" name="pr_number" id="pr_number" onkeyup="suggestPRNumber(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="pr_number_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="supplier_id">Supplier</label>
                                <span class="formwrapper">
                                    <select name="supplier_id" id="supplier_id" class="chzn-select">
                                        <option value="">All Suppliers</option>
                                            @forelse (getReportSuppliers() as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->supplier_name}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="item_name">Item</label>
                                <span class="formwrapper">
                                    <select name="item_id" id="item_id" class="chzn-select">
                                        <option value="">All Items</option>
                                            @forelse (getReportItems() as $item)
                                            <option value="{{ $item->id }}">{{ $item->item_name}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="order_by">Order By</label>
                                <span class="formwrapper">
                                    <input type="text" name="order_by" id="order_by" onkeyup="suggestOrderBy(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="order_by_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for=""></label>
                            <span class="formwrapper">
                                <button id="search" type="submit" class="btn btn-primary">Search</button>
                                <button id="reset-order-data" type="submit" class="btn btn-primary">Reset</button>
                            </span>
                        </div>
                    </div> --}}


            </form>
            <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth ">
                <thead>
                    <tr class="main-header">
                        <th class="head0">Approval</th>
                        <th class="head0">PO No. </th>
                        <th class="head0">PO Date</th>
                        <th class="head0">PR No.</th>
                        <th class="head0">PR Date</th>
                        <th class="head0">Supplier</th>
                        <th class="head0">Person</th>
                        <th class="head0">Order By</th>
                        <th class="head0">Item</th>
                        <th class="head0">Code</th>
                        <th class="head0">PO Qty.</th>
                        <th class="head0">Unit</th>
                        <th class="head0">Rate/Unit</th>
                        <th class="head0">Disc.(%)</th>
                        <th class="head0">Del. Date</th>
                        <th class="head0">Amount</th>
                        <!-- <th class="head0" style="display:none;"></th> -->
                    </tr>
                </thead>
                <tfoot>
                        <tr class="total_tr">
                            <td colspan="14"></td>
                            <td style="text-align:right">Total:</td>
                            {{-- <th colspan="14" class="export" style="text-align:right; background-color:white; color:black;">Total:</th> --}}
                            <td class="amountsum" name="total_amount">

                        </tr>
                </tfoot>
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
        jQuery('.purchase_order_summary').click();
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
                "order": [[ 1, 'asc' ]],
                // "order": [[ 1, 'desc' ],[ 15, 'desc' ]],
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Purchase Order Summary',
                        title:"",
                        className: 'purchase_order_summary d-none',
                        footer: true,
                        exportOptions: {
                            columns: ':not(.export)',
                            footer: true,
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-purchase_order_summary') }}",
                        type: "POST",
                        headers: headerOpt,
                        data : {

                                'from_date': formValue.trans_from_date,
                                'to_date':formValue.trans_to_date,
                                //'from_date':formValue.from_date,
                               // 'to_date':formValue.to_date,
                                //'po_number': formValue.po_number,
                                //'pr_number': formValue.pr_number,
                                //'supplier_id':formValue.supplier_id,
                                //'location_id':formValue.location_id,
                                //'item_id': formValue.item_id,
                               // 'order_by': formValue.order_by,
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
                            { data: 'is_approved', name: 'purchase_order.is_approved' },
                            { data: 'po_number', name: 'purchase_order.po_number' },
                            { data: 'po_date', name: 'purchase_order.po_date' },
                            { data: 'pr_number', name: 'purchase_requisition.pr_number' },
                            { data: 'pr_date', name: 'purchase_requisition.pr_date' },
                            { data: 'supplier_name', name: 'suppliers.supplier_name' },
                            { data: 'person_name', name: 'purchase_order.person_name' },
                            { data: 'order_by', name: 'purchase_order.order_by' },
                            { data: 'item_name', name: 'items.item_name' },
                            { data: 'item_code', name: 'items.item_code' },
                            { data: 'po_qty', name: 'purchase_order_details.po_qty' },
                            { data: 'unit_name', name: 'units.unit_name' },
                            { data: 'rate_per_unit', name: 'purchase_order_details.rate_per_unit' },
                            { data: 'discount', name: 'purchase_order_details.discount' },
                            { data: 'del_date', name: 'purchase_order_details.del_date' },
                            { data: 'amount', name: 'purchase_order_details.amount' },
                            // { data: 'po_sequence', name: 'purchase_order.po_sequence', visible:false}
                        ],
                         footerCallback: function ( row, data, start, end, display ) {
                            var api = this.api();

                            // Helper function to parse numbers from formatted strings
                            var parseValue = function(i) {
                                if (typeof i === 'string') {
                                    i = i.replace(/[\$,]/g, ''); // remove $ and commas
                                    i = parseFloat(i);
                                }
                                return isNaN(i) ? 0 : i;
                            };

                            // Calculate total over all pages
                            var total = api
                                .column(15) // 'Amount' column index (0 based)
                                .data()
                                .reduce(function (a, b) {
                                    return parseValue(a) + parseValue(b);
                                }, 0);

                            // Calculate total over this page
                            var pageTotal = api
                                .column(15, { page: 'current'} )
                                .data()
                                .reduce(function (a, b) {
                                    return parseValue(a) + parseValue(b);
                                }, 0);

                            // Update footer
                            jQuery(api.column(0).footer()).html('');
                            jQuery(api.column(1).footer()).html('');
                            jQuery(api.column(2).footer()).html('');
                            jQuery(api.column(3).footer()).html('');
                            jQuery(api.column(4).footer()).html('');
                            jQuery(api.column(5).footer()).html('');
                            jQuery(api.column(6).footer()).html('');
                            jQuery(api.column(7).footer()).html('');
                            jQuery(api.column(8).footer()).html('');
                            jQuery(api.column(9).footer()).html('');
                            jQuery(api.column(10).footer()).html('');
                            jQuery(api.column(11).footer()).html('');
                            jQuery(api.column(12).footer()).html('');
                            jQuery(api.column(13).footer()).html('');
                            jQuery(api.column(14).footer()).html('Total:');
                            jQuery(api.column(15).footer()).html(
                                pageTotal.toFixed(3)
                            );

                        },
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
        var searchForm = jQuery('#orderSearchForm');
        loadDataTable();
    }

    });

    jQuery('#reset-order-data').on('click',function(){
        var searchForm = jQuery("#psfdSearchForm");
        searchForm.find('#to_date').val('');
        searchForm.find('#from_date').val('');
        searchForm.find('#po_number').val('');
        searchForm.find('#pr_number').val('');
        searchForm.find('#item_id').val('').trigger('liszt:updated');
        searchForm.find('#supplier_id').val('').trigger('liszt:updated');
        searchForm.find('#order_by').val('');
        DataYearWise();
        loadDataTable();
    });

}); // .ready
</script>
<script src="{{ asset('js/view/suggestreport.js?ver='.getJsVersion()) }}"></script>
@endsection
