@extends('layouts.app',['pageTitle' => 'Material Request Summary'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Material Request Summary</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Material Request Summary</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
            <form id="mrsSearchForm" name="mrsSearchForm" class="stdform">

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
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="from_location_id">From Location</label>
                                <span class="formwrapper">
                                    <select name="from_location_id" id="from_location_id" class="chzn-select">
                                        <option value="">All From Locations</option>
                                            @forelse (getLocation() as $location)
                                            <option value="{{ $location->id }}">{{ $location->location_name}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="to_location_id">To Location</label>
                                <span class="formwrapper">
                                    <select name="to_location_id" id="to_location_id" class="chzn-select">
                                        <option value="">All To Locations</option>
                                            @forelse (getLocation() as $location)
                                            <option value="{{ $location->id }}">{{ $location->location_name}}</option>
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
                                <label class="control-label" for="cust_group">Cust.Group</label>
                                <span class="formwrapper">
                                    <select name="cust_group_id" id="cust_group_id" class="chzn-select">
                                        <option value="">All Cust.Groups</option>
                                            @forelse (getCustomerGroup() as $cust_group)
                                            <option value="{{ $cust_group->id }}">{{ $cust_group->customer_group_name}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="mr_number">MR No.</label>
                                <span class="formwrapper">
                                    <input type="text" name="mr_number" id="mr_number" onkeyup="suggestMRNumber(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="mr_number_list" class="suggestion_list" ></div>
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

                </div>  --}}

            </form>
            <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
                <thead>
                    <tr class="main-header">
                        <th class="head0">MR No.</th>
                        <th class="head0">MR Date</th>
                        <th class="head0">From Location</th>
                        <th class="head0">To Location</th>
                        <th class="head0">Cust.Group</th>
                        <th class="head0">Item</th>
                        <th class="head0">Code</th>
                        <th class="head0">MR Qty.</th>
                        <th class="head0">Unit</th>
                        <th class="head0">Remark</th>
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
        jQuery('.material_request_summary').click();
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

            var data = new FormData(document.getElementById('mrsSearchForm'));
            var formValue = Object.fromEntries(data.entries());

            jQuery('#dyntable').DataTable({

                "processing": true,
                "serverSide": true,
                "scrollX" : true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                "order": [[ 0, 'asc' ]],
                // "order": [[ 1, 'desc' ],[ 10, 'desc' ]],
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Material Request Summary',
                        title:"",
                        className: 'material_request_summary d-none',
                        exportOptions: {
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-material_request_summary') }}",
                        type: "POST",
                        headers: headerOpt,
                        data : {
                        'from_date': formValue.trans_from_date,
                        'to_date':formValue.trans_to_date,
                        // 'from_date': formValue.from_date,
                       //  'to_date': formValue.to_date,
                         //'from_location_id': formValue.from_location_id,
                        // 'location_id': formValue.to_location_id, // to_location_id maps to location_id in controller
                        // 'cust_group_id': formValue.cust_group_id,
                        // 'mr_number': formValue.mr_number
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

                        { data: 'mr_number', name: 'material_request.mr_number' },
                        { data: 'mr_date', name: 'material_request.mr_date' },
                        { data: 'from_location_name', name: 'from_location.location_name' },
                        { data: 'to_location_name', name: 'to_location.location_name' },
                        { data: 'customer_group_name', name: 'customer_groups.customer_group_name' },
                        { data: 'item_name', name: 'items.item_name' },
                        { data: 'item_code', name: 'items.item_code' },
                        { data: 'mr_qty', name: 'material_request_details.mr_qty' },
                        { data: 'unit_name', name: 'units.unit_name' },
                        { data: 'remarks', name: 'material_request_details.remarks' },
                        // { data: 'mr_sequence', name: 'material_request.mr_sequence', visible:false}
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

    jQuery("#mrsSearchForm").validate({
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

        var searchForm = jQuery("#mrsSearchForm");
        searchForm.find('#from_date').val('');
        searchForm.find('#to_date').val('');
        searchForm.find('#mr_number').val('');
        searchForm.find('#from_location_id').val('').trigger('liszt:updated');
        searchForm.find('#to_location_id').val('').trigger('liszt:updated');
        searchForm.find('#cust_group_id').val('').trigger('liszt:updated');

        DataYearWise();
        loadDataTable();
    });

}); // .ready
</script>
<script src="{{ asset('js/view/suggestreport.js?ver='.getJsVersion()) }}"></script>
@endsection
