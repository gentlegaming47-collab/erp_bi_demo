@extends('layouts.app',['pageTitle' => 'Item Issue Summary'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Item Issue Summary</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Item Issue Summary</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
             {{-- <form id="iisSearchForm" name="iisSearchForm" class="stdform">

               <div class="row">
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
                                <label class="control-label" for="issue_number">Issue No.</label>
                                <span class="formwrapper">
                                    <input type="text" name="issue_number" id="issue_number" onkeyup="suggestIssueNumber(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="issue_number_list" class="suggestion_list" ></div>
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
                                <label class="control-label" for="issue_type_value_fix">Issue Type</label>
                                <span class="formwrapper">
                                    <select name="issue_type_value_fix" id="issue_type_value_fix" class="chzn-select">
                                        <option value="">All Issue Types</option>
                                        <option value="consumable"> Consumable </option>
                                        <option value="waste/scrap_entry"> Waste/Scrap entry </option>
                                    </select>
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

                </div>

            </form> --}}
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
                        <th class="head0">Issue No.</th>
                        <th class="head0">Issue Date</th>
                        <th class="head0">Item</th>
                        <th class="head0">Code</th>
                        <th class="head0">Issue Qty.</th>
                        <th class="head0">Unit</th>
                        <th class="head0">Issue Type</th>
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
        jQuery('.item_issue_summary').click();
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
                // "order": [[ 1, 'desc' ],[ 8, 'desc' ]],
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Item Issue Summary',
                        title:"",
                        className: 'item_issue_summary d-none',
                        exportOptions: {
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-item_issue_summary') }}",
                        type: "POST",
                        headers: headerOpt,
                        data : {

                                // 'from_date':formValue.from_date,
                                // 'to_date':formValue.to_date,
                                // 'issue_number': formValue.issue_number,
                                // 'item_id': formValue.item_id,
                                // 'issue_type_value_fix': formValue.issue_type_value_fix,
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
                    { data: 'issue_number', name: 'item_issue.issue_number' },
                    { data: 'issue_date', name: 'item_issue.issue_date' },
                    { data: 'item_name', name: 'items.item_name' },
                    { data: 'item_code', name: 'items.item_code' },
                    { data: 'issue_qty', name: 'item_issue_details.issue_qty' },
                    { data: 'unit_name', name: 'units.unit_name' },
                    { data: 'item_type', name: 'item_type' },
                    { data: 'remarks', name: 'item_issue_details.remarks' },
                    // { data: 'issue_sequence', name: 'item_issue.issue_sequence', visible:false}
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

    jQuery("#psfdSearchForm").validate({
        onkeyup: false,
        onfocusout: false,

    rules: {

        trans_from_date: {

            required: function(e){
                if(jQuery("#psfdSearchForm").find('#trans_to_date').val() != ""){
                    return true;
                }else{
                    return false;
                }
            },
            lessThan: "#trans_to_date"

        },

        trans_to_date: {

            required: function(e){
                if(jQuery("#psfdSearchForm").find('#trans_from_date').val() != ""){
                    return true;
                }else{
                    return false;
                }
            },
            greaterThan: "#trans_from_date"

        },

    },

    messages: {

        trans_from_date: {

            required: "Please Enter From Date",
            lessThan: "From Date must be less then to date"

        },

        trans_to_date: {

            required: "Please Enter To Date",
            greaterThan: "To Date must be greater then from date"

        }

    },
    submitHandler: function(form) {
        var searchForm = jQuery('#orderSearchForm');
        loadDataTable();
    }

    });

    // jQuery('#reset-order-data').on('click',function(){

    //     var searchForm = jQuery("#iisSearchForm");
    //     searchForm.find('#to_date').val('');
    //     searchForm.find('#from_date').val('');
    //     searchForm.find('#issue_number').val('');
    //     searchForm.find('#item_id').val('').trigger('liszt:updated');
    //     searchForm.find('#issue_type_value_fix').val('').trigger('liszt:updated');

    //     DataYearWise();
    //     loadDataTable();
    // });

}); // .ready
</script>
<script src="{{ asset('js/view/suggestreport.js?ver='.getJsVersion()) }}"></script>
@endsection
