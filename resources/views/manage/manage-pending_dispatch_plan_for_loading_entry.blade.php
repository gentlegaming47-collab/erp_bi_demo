@extends('layouts.app',['pageTitle' => 'Pending Dispatch Plan for Loading Entry'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Pending Dispatch Plan for Loading Entry</li>
</ul>
@endsection

@section('content')
@include('modals.so_fitting_dispatch_for_report_modal')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Pending Dispatch Plan for Loading Entry</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
            <form id="pdpfleSearchForm" name="pdpfleSearchForm" class="stdform">

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
                                <label class="control-label" for="customer_name">Customer </label>
                                <span class="formwrapper">
                                    <input type="text" name="customer_name" id="customer_name" onkeyup="suggestCustomer(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="customer_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="location_id">Location</label>
                                <span class="formwrapper">
                                    <select name="location_id" id="location_id" class="chzn-select">
                                        <option value="">All Locations</option>
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
                                <label class="control-label" for="dealer_name">Dealer</label>
                                <span class="formwrapper">
                                    <select name="dealer_id" id="dealer_id" class="chzn-select">
                                        <option value="">All Dealers</option>
                                            @forelse (getReportDealers() as $dealer)
                                            <option value="{{ $dealer->id }}">{{ $dealer->dealer_name}}</option>
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
                                        <option value="">All Items </option>
                                            @forelse (getReportItems() as $item)
                                            <option value="{{ $item->id }}">{{ $item->item_name}}</option>
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
                                <label class="control-label" for="village_name">Village </label>
                                <span class="formwrapper">
                                    <input type="text" name="village_name" id="village_name" class="form-control" onkeyup="suggestVillage(event,this)" autocomplete="nope"/>
                                    <div id="village_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="district_name">District </label>
                                <span class="formwrapper">
                                    <input type="text" name="district_name" id="district_name" onkeyup="suggestCity(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="district_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="dp_number">Disp. Plan No. </label>
                                <span class="formwrapper">
                                    <input type="text" name="dp_number" id="dp_number" class="form-control" onkeyup="suggestDpNo(event,this)" autocomplete="nope"/>
                                    <div id="dp_number_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="so_number">SO No. </label>
                                <span class="formwrapper">
                                    <input type="text" name="so_number" id="so_number" onkeyup="suggestSONumber(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="so_number_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                </div>

                <div class="row">
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
                        <th class="head1">Disp. Plan No.</th>
                        <th class="head1">Disp. Plan Date</th>
                        <th class="head0">SO No.</th>
                        <th class="head0">SO Date</th>
                        <th class="head0">Cust./Location</th>
                        <th class="head0">Dealer</th>
                        <th class="head0">Village</th>
                        <th class="head0">District</th>
                        <th class="head0">Item</th>
                        <th class="head0">Code</th>
                        <th class="head0">Pend. Qty.</th>
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

            var data = new FormData(document.getElementById('pdpfleSearchForm'));
            var formValue = Object.fromEntries(data.entries());

          table = jQuery('#dyntable').DataTable({

                "processing": true,
                "serverSide": true,
                "scrollX" : true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                "order": [[1, 'asc' ],[12 , 'asc']], // date wise desc
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Pending Dispatch Plan for Loading Entry',
                        title:"",
                        className: 'pdple d-none',
                        exportOptions: {
                            columns: function(idx, data, node) {
                                return idx !== 12 && table.columns(idx).visible();
                            },
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-pending_dispatch_plan_for_loading_entry_summary') }}",
                        type: "POST",
                        headers: headerOpt,
                        data : {
                                    'trans_from_date': formValue.trans_from_date,
                                    'trans_to_date':formValue.trans_to_date,
                                //  'customer_name':formValue.customer_name,
                                //  'location_id':formValue.location_id,
                                //  'village_name': formValue.village_name,
                                //  'district_name': formValue.district_name,
                                //  'dealer_id': formValue.dealer_id,
                                //  'item_id': formValue.item_id,
                                //  'so_number': formValue.so_number,
                                //  'dp_number': formValue.dp_number
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

                     { data: 'dp_number' ,name: 'dispatch_plan.dp_number' , },
                     { data: 'dp_date' ,name: 'dispatch_plan.dp_date' , },
                     { data: 'so_number', name: 'sales_order.so_number', },
                     { data: 'so_date', name: 'sales_order.so_date', },   
                     { data: 'name', name: 'name', },  
                     { data: 'dealer_name', name: 'dealers.dealer_name', },
                     { data: 'village_name', name: 'villages.village_name', },   
                     { data: 'district_name', name: 'districts.district_name', },
                     { data: 'item_name', name: 'items.item_name',
                     
                        render: function(data, type, row, meta) {
                            if (row.fitting_item === "yes") {
                                return `${data} <span><a><i class="action-icon iconfa-eye-open eyeIcon1" data-so_details_id="${row.so_details_id}" data-dp_id="${row.dp_id}"></i></a></span>`;
                            } else {
                                return data;
                            }
                        }
                     },
                     { data: 'item_code', name: 'items.item_code', },
                     { data: 'pend_qty', name: 'pend_qty', },
                     { data: 'unit_name' ,name: 'units.unit_name'},
                     { data: 'dp_sequence' ,name: 'dispatch_plan.dp_sequence' , visible:false},


                ],
                 initComplete: function () {
                    // Exclude first column (index 0) from search
                    initColumnSearch('#dyntable', []);
                }
            });

    }

    jQuery("#pdpfleSearchForm").validate({
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

    // search form logic

    jQuery('#reset-order-data').on('click',function(){

        var searchForm = jQuery("#pdpfleSearchForm");
        searchForm.find('#customer_name').val('');
        searchForm.find('#location_id').val('').trigger('liszt:updated');
        searchForm.find('#dealer_id').val('').trigger('liszt:updated');
        searchForm.find('#item_id').val('').trigger('liszt:updated');
        searchForm.find('#village_name').val('');
        searchForm.find('#district_name').val('');
        searchForm.find('#dp_number').val('');
        searchForm.find('#so_number').val('');
        loadDataTable();
    });


}); // .ready

jQuery(document).on('click', '.eyeIcon1', function () {


        var  so_detail_id = jQuery(this).data('so_details_id');
        var  dp_id = jQuery(this).data('dp_id');

        if (so_detail_id != '') {

          var formUrl =  RouteBasePath + "/get-fitting_so_item_data_for_dispatch?id="+dp_id+"&so_detail_id=" + so_detail_id;

        jQuery.ajax({
            url: formUrl,
            type: 'GET',
            headers: headerOpt,
            dataType: 'json',
            processData: false,
            success: function (data) {
                if (data.response_code == 1) {
                    var tblHtml = '';

                        for (let idx in data.soFittingItem) {
                       

                        tblHtml += `<tr>        
                                                          
                        <td>${data.soFittingItem[idx].item_name}</td>
                        <td>${data.soFittingItem[idx].item_code}</td>
                        <td>${data.soFittingItem[idx].item_group_name}</td>                                
                        <td>${data.soFittingItem[idx].unit_name}</td>                                
                        <td>${parseFloat(data.soFittingItem[idx].pend_sod_qty).toFixed(3)}</td>                                
                        </tr>`;

                    }

                    }
                    jQuery('#SoFittingForDispatchModalTable tbody').empty().append(tblHtml);
                    jQuery("#SoFittingForReportModal").modal('show');

                }

            });
        }
});
</script>
<script src="{{ asset('js/view/suggestreport.js?ver='.getJsVersion()) }}"></script>
@endsection
