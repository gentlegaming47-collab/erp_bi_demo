@extends('layouts.app',['pageTitle' => 'Pending SO (From Location)'])
@section('header')
    <style>
        .dataTables_filter
        {
            top: 35px;
        }
    </style>

    <ul class="breadcrumbs">
        <li>
            <a href="{{ route('dashboard') }}">
                <i class="iconfa-home"></i>
            </a>
            <span class="separator"></span>
        </li>

        <li>Pending SO (From Location)</li>
    </ul>
@endsection

@section('content')
    <div class="widgetbox">
        <div class="headtitle">
            <div class="btn-group">
                <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
            </div>
            <h4 class="widgettitle">Pending SO (From Location)</h4>
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
            </form>

            <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
                <thead>
                    <tr class="main-header">
                        <th class="head1">From Location</th>
                        <th class="head1">MR No.</th>
                        <th class="head1">MR Date</th>
                        <th class="head0">Item</th>
                        <th class="head0">Code</th>
                        <th class="head0">MR. Qty.</th>
                        <th class="head0">Unit</th>
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
                jQuery('.pending_so_for_location').click();
            });

            loadDataTable();

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

                table = jQuery('#dyntable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "scrollX" : true,
                    "sScrollX": "100%",
                    "sScrollXInner": "110%",
                    "bScrollCollapse": true,
                    "order": [[ 2, 'asc' ],[7 , 'asc']],
                    pageLength : 25,
                    dom: 'Blfrtip',
                    buttons:[{
                        extend:'excel',
                        filename: 'Pending SO (From Location)',
                        title:"",
                        className: 'pending_so_for_location d-none',
                        exportOptions: {
                            columns: function(idx, data, node) {
                                return idx !== 7 && table.columns(idx).visible();
                            },
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }],
                    ajax: {
                        url: "{{ route('listing-pending_so_for_location') }}",
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
                        { data: 'location_name', name: 'locations.location_name', },
                        { data: 'mr_number', name: 'material_request.mr_number', },
                        { data: 'mr_date', name: 'material_request.mr_date', },
                        { data: 'item_name', name: 'items.item_name', },
                        { data: 'item_code', name: 'items.item_code', },
                        { data: 'mr_qty', name: 'material_request_details.mr_qty', },
                        { data: 'unit_name', name: 'units.unit_name', },
                        { data: 'mr_sequence' ,name: 'material_request.mr_sequence' , visible:false},
                    ],
                    initComplete: function () {
                        initColumnSearch('#dyntable', []);
                    },
                });
            }

            jQuery("#PrSearchForm").validate({
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