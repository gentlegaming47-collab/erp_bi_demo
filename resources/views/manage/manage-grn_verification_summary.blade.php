@extends('layouts.app',['pageTitle' => 'GRN Verification Summary'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>GRN Verification Summary</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">GRN Verification Summary</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
            <form id="iisSearchForm" name="iisSearchForm" class="stdform">

                <div class="row">
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="from_date">From Date</label>
                                <span class="formwrapper">
                                   {{-- <input name="from_date" id="from_date"
                                   class="form-control dates-picker from-april"/> --}}
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
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="grn_no">GRN No.</label>
                                <span class="formwrapper">
                                    <input type="text" name="grn_no" id="grn_no" onkeyup="suggestGrnNo(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="grn_no_list" class="suggestion_list" ></div>
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
                 
                {{-- </div>
                <div class="row"> --}}

                    <div class="span-6">
                        <div class="par control-group form-control">
                            <label class="control-label" for=""></label>
                            <span class="formwrapper">
                                <button id="search" type="submit" class="btn btn-primary">Search</button>
                                <button id="reset-order-data" type="submit" class="btn btn-primary">Reset</button>
                            </span>
                        </div>
                    </div>

                </div>

            </form>
            <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
                <thead>
                     <tr class="main-header">
                        <th class="head1">Verification Date</th>
                        <th class="head1">Location</th>
                        <th class="head1">GRN No.</th>
                        <th class="head1">GRN Date</th>
                        <th class="head1">Item</th>
                        <th class="head1">Item Details Name</th>
                        <th class="head1">Dispatch Plan No.</th>
                        <th class="head1">Dispatch Plan Date</th>
                        <th class="head1">Dispatch Plan Qty.</th>
                        <th class="head1">GRN Qty.</th>
                        <th class="head1">Mismatch Qty.</th>
                        <th class="head1">Unit</th>
                        <th class="head1">Reason</th>
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
        jQuery('.grn_verification_summary').click();
    });
         setTimeout(() => {
            DataYearWise();
            loadDataTable();
        }, 1000);



    function loadDataTable(){

            if (jQuery.fn.DataTable.isDataTable('#dyntable') ) {
                jQuery('#dyntable').DataTable().destroy();
            }

            var data = new FormData(document.getElementById('iisSearchForm'));
            var formValue = Object.fromEntries(data.entries());

            jQuery('#dyntable').DataTable({

                "processing": true,
                "serverSide": true,
                "scrollX" : true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                "order": [[ 0, 'asc' ]],
                // "order": [[ 3, 'desc' ],[ 13, 'desc' ]],
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'GRN Verification Summary',
                        title:"",
                        className: 'grn_verification_summary d-none',
                        exportOptions: {
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-grn_verification_summary') }}",
                        type: "POST",
                        headers: headerOpt,
                        data : {

                                'from_date':formValue.from_date,
                                'to_date':formValue.to_date,
                                'grn_number': formValue.grn_no,
                                'item_id': formValue.item_id,
                               

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
                { data: 'gv_date', name: 'grn_verification.gv_date', },
                { data: 'location_name', name: 'locations.location_name ', },
                { data: 'grn_number', name: 'grn_material_receipt.grn_number', },
                { data: 'grn_date', name: 'grn_material_receipt.grn_date', },
                { data: 'item_name', name: 'items.item_name', },
                { data: 'secondary_item_name', name: 'item_details.secondary_item_name', },
                { data: 'dp_number', name: 'dispatch_plan.dp_number', },
                { data: 'dp_date', name: 'dispatch_plan.dp_date', },
                { data: 'plan_qty', name: 'plan_qty' },
                { data: 'grn_qty', name: 'material_receipt_grn_details.grn_qty', },
                { data: 'mismatch_qty', name: 'material_receipt_grn_details.mismatch_qty', },
                { data: 'unit_name', name: 'unit_name' },
                { data: 'gv_reason', name: 'grn_verification.gv_reason' },
                // { data: 'grn_sequence', name: 'grn_material_receipt.grn_sequence', visible:false}
                ],
                initComplete: function () {
                    // Exclude first column (index 0) from search
                    initColumnSearch('#dyntable', []);
                }

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

    jQuery("#iisSearchForm").validate({
        onkeyup: false,
        onfocusout: false,

    rules: {

        from_date: {

            required: function(e){
                if(jQuery("#iisSearchForm").find('#to_date').val() != ""){
                    return true;
                }else{
                    return false;
                }
            },
            lessThan: "#to_date"

        },

        to_date: {

            required: function(e){
                if(jQuery("#iisSearchForm").find('#from_date').val() != ""){
                    return true;
                }else{
                    return false;
                }
            },
            greaterThan: "#from_date"

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
        var searchForm = jQuery('#orderSearchForm');
        loadDataTable();
    }

    });

    jQuery('#reset-order-data').on('click',function(){

        var searchForm = jQuery("#iisSearchForm");
        searchForm.find('#to_date').val('');
        searchForm.find('#from_date').val('');
        searchForm.find('#grn_no').val('');
        searchForm.find('#item_id').val('').trigger('liszt:updated');


        DataYearWise();
        loadDataTable();
    });

}); // .ready
</script>
<script src="{{ asset('js/view/suggestreport.js?ver='.getJsVersion()) }}"></script>
@endsection
