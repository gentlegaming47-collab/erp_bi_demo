@extends('layouts.app',['pageTitle' => 'Purchase Requisition Summary'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Purchase Requisition Summary</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Purchase Requisition Summary</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
            <form id="PrSearchForm" name="PrSearchForm" class="stdform">

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
                                   <input name="to_date" id="to_date" class="form-control report-date-picker"/>
                                </span>
                        </div>
                    </div>

                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="pr_number">PR No.</label>
                                <span class="formwrapper">
                                    <input type="text" name="pr_number" id="pr_number" class="form-control" autocomplete="nope"/>
                                </span>
                        </div>
                    </div>

                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="item_id">Item</label>
                                <span class="formwrapper">
                                    <select name="item_id" id="item_id" class="chzn-select">
                                        <option value="">All Items</option>
                                            @forelse (getReportItems() as $items)
                                            <option value="{{ $items->id }}">{{ $items->item_name}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </span>
                        </div>
                    </div>


                </div>


                <div class="row">

                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="supplier_name">Supplier</label>
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
                                <label class="control-label" for="prepared_by">Prepared By</label>
                                <span class="formwrapper">
                                    <input type="text" name="prepared_by" id="prepared_by" class="form-control" autocomplete="nope"/>
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
                    </div>


                </div> --}}


                

            </form>
            <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
                <thead>
                    <tr class="main-header">
                        <th class="head1">PR No.</th>
                        <th class="head1">PR Date</th>
                        <th class="head0">Supplier</th>
                        <th class="head0">Location</th>
                        <th class="head0">Item</th>
                        <th class="head0">Code</th>
                        <th class="head0">Req. Qty.</th>
                        <th class="head0">Unit</th>
                        <th class="head0">Rate/Unit</th>
                        <th class="head0">Remark</th>
                        <th class="head1">Prepared By</th>
                        <th class="head1">Sp. Note</th>
                        <!-- <th class="head0" style="display:none;"></th>                    -->
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
        jQuery('.purchase_requisition_summary').click();
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

            var data = new FormData(document.getElementById('PrSearchForm'));
            var formValue = Object.fromEntries(data.entries());

            jQuery('#dyntable').DataTable({

                "processing": true,
                "serverSide": true,
                "scrollX" : true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                "order": [[ 0, 'asc' ]],
                // "order": [[ 1, 'desc' ],[ 12, 'desc' ]],
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Purchase Requisition Summary',
                        title:"",
                        className: 'purchase_requisition_summary d-none',
                        exportOptions: {
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-purchase_requisition_summary') }}",
                        type: "POST",
                        headers: headerOpt,
                        data : {

                            'from_date': formValue.trans_from_date,
                                    'to_date':formValue.trans_to_date,
                                // 'pr_number':formValue.pr_number,
                                // 'from_date':formValue.from_date,
                                // 'to_date':formValue.to_date,
                                // 'item_id':formValue.item_id,
                                // 'supplier_id': formValue.supplier_id,
                                // 'prepared_by': formValue.prepared_by
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
                
                    { data: 'pr_number', name: 'purchase_requisition.pr_number', },
                    { data: 'pr_date', name: 'purchase_requisition.pr_date', },
                    { data: 'supplier_name', name: 'suppliers.supplier_name', },    
                    { data: 'location_name', name: 'locations.location_name', },    
                    { data: 'item_name', name: 'items.item_name', },
                    { data: 'item_code', name: 'items.item_code', },
                    { data: 'req_qty', name: 'purchase_requisition_details.req_qty', },
                    { data: 'unit_name', name: 'units.unit_name', },
                    { data: 'rate_per_unit', name: 'purchase_requisition_details.rate_per_unit', },
                    { data: 'remarks', name: 'purchase_requisition_details.remarks', },    
                    { data: 'prepared_by', name: 'purchase_requisition.prepared_by', },    
                    { data: 'special_notes', name: 'purchase_requisition.special_notes', },
                    // { data: 'pr_sequence', name: 'purchase_requisition.pr_sequence',  visible:false},    
                    
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

    jQuery("#PrSearchForm").validate({
        onkeyup: false,
        onfocusout: false,

    rules: {

        from_date: {

            required: function(e){
                if(jQuery("#PrSearchForm").find('#to_date').val() != ""){
                    return true;
                }else{
                    return false;
                }
            },
            lessThan: "#to_date"

        },

        to_date: {

            required: function(e){ 
                if(jQuery("#PrSearchForm").find('#from_date').val() != ""){
                    return true;
                }else{
                    return false;
                }
            },
            greaterThan: "#from_date"

        },

    }, onkeyup: false,
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

        var searchForm = jQuery("#PrSearchForm");
        searchForm.find('#pr_number').val('');
        searchForm.find('#from_date').val('');
        searchForm.find('#to_date').val('');
        searchForm.find('#item_id').val('').trigger('liszt:updated');
        searchForm.find('#supplier_id').val('').trigger('liszt:updated');
        searchForm.find('#prepared_by').val('');
        DataYearWise();
        loadDataTable();
    });

   /* var today       = new Date();
    var currentYear = today.getFullYear();
    var aprilFirst  = new Date(currentYear, 3, 1); // April = 3 (0-indexed)

    // If today is before April 1, use April 1 of the previous year
    if (today < aprilFirst) {
        aprilFirst = new Date(currentYear - 1, 3, 1);
    }
    // Override the global today-date with april 1 for this specific input
    jQuery(".dates-picker.from-april:not(.no-fill)").each(function () {
        if (jQuery(this).val() === "") {
            jQuery(this).datepicker("setDate", aprilFirst);
        }
    });*/


}); // .ready 
</script>
@endsection
