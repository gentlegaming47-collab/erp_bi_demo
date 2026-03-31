@extends('layouts.app',['pageTitle' => 'Pending SO for Dispatch'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Pending SO for Dispatch</li>
</ul>
@endsection

@section('content')
@include('modals.so_fitting_dispatch_for_report_modal')

<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Pending SO for Dispatch</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
            <form id="psofordispatchSearchForm" name="psofordispatchSearchForm" class="stdform">
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
                                <label class="control-label" for="customer_name">Customer</label>
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
                </div>
                <div class="row">

                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="mis_category_name">MIS Category</label>
                                <span class="formwrapper">
                                    <select name="mis_category_id" id="mis_category_id" class="chzn-select">
                                        <option value="">All MIS Categories</option>
                                            @forelse (getMisCategory() as $mis_category)
                                            <option value="{{ $mis_category->id}}">{{ $mis_category->mis_category}}</option>
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
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="sgroup_name">Group</label>
                                <span class="formwrapper">
                                    <select name="group_id" id="group_id" class="chzn-select">
                                        <option value="">All Groups</option>
                                            @forelse (getItemGroupData() as $group)
                                            <option value="{{ $group->id }}">{{ $group->item_group_name}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="code">Code</label>
                                <span class="formwrapper">
                                    <select name="code_id" id="code_id" class="chzn-select">
                                        <option value="">All Codes</option>
                                            @forelse (getReportCode() as $code)
                                            <option value="{{ $code->id }}">{{ $code->item_code}}</option>
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
                                <label class="control-label" for="village_name">Village</label>
                                <span class="formwrapper">
                                    <input type="text" name="village_name" id="village_name" class="form-control" onkeyup="suggestVillage(event,this)" autocomplete="nope"/>
                                    <div id="village_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="taluka_name">Taluka</label>
                                <span class="formwrapper">
                                    <input type="text" name="taluka_name" id="taluka_name" onkeyup="suggestTaluka(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="taluka_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="district_name">Dist.</label>
                                <span class="formwrapper">
                                    <input type="text" name="district_name" id="district_name" onkeyup="suggestCity(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="district_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="state_name">State</label>
                                <span class="formwrapper">
                                    <input type="text" name="state_name" id="state_name" class="form-control" onkeyup="suggestState(event,this)" autocomplete="nope"/>
                                    <div id="state_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                   <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="so_number">SO No.</label>
                                <span class="formwrapper">
                                    <input type="text" name="so_number" id="so_number" onkeyup="suggestSONumber(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="so_number_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
                    <div class="span-6">
                        <div class="par control-group form-control">
                                <label class="control-label" for="so_type_id">SO Type</label>
                                <span class="formwrapper">
                                    <select name="so_type_id" id="so_type_id" class="chzn-select">
                                        <option value="">All SO Types</option>
                                           <option value="general">General</option>
                                           <option value="replacement">Replacement</option>
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
                </div> --}}

            </form>
            <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
                <thead>
                    <tr class="main-header">
                        <th class="head1">SO No.</th>
                        <th class="head1">Date</th>
                        <th class="head0">From</th>
                        <th class="head0">Type</th>
                        <th class="head0">Cust. Group</th>
                        <th class="head0">Cust./ Location</th>
                        <th class="head0">Dealer</th>
                        <th class="head0">Reg. No.</th>
                        <th class="head0">Village</th>
                        <th class="head0">Taluka</th>
                        <th class="head0">Dist.</th>
                        <th class="head0">State</th>
                        <th class="head0">Country</th>
                        <th class="head0">MIS Category</th>
                        <th class="head0">Item</th>
                        <th class="head0">Group</th>
                        <th class="head0">Code</th>
                        <th class="head0">Pend. SO Qty.</th>
                        <th class="head0">Unit</th>
                        <th class="head0">Remark</th>
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
        jQuery('.psfd').click();
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

            var data = new FormData(document.getElementById('psofordispatchSearchForm'));
            var formValue = Object.fromEntries(data.entries());

            table = jQuery('#dyntable').DataTable({

                "processing": true,
                "serverSide": true,
                "scrollX" : true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                "order": [[ 1, 'asc' ],[20 , 'asc']], // date wise asc
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Pending SO For Dispatch',
                        title:"",
                        className: 'psfd d-none',
                        exportOptions: {
                            columns: function(idx, data, node) {
                                return idx !== 20 && table.columns(idx).visible();
                            },
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-pending_so_for_dispatch_report') }}",
                        type: "POST",
                        headers: headerOpt,
                        data : {
                                'trans_from_date': formValue.trans_from_date,
                                'trans_to_date':formValue.trans_to_date,
                                //  'customer_name':formValue.customer_name,
                                //  'location_id':formValue.location_id,
                                //  'cust_group_id': formValue.cust_group_id,
                                //  'dealer_id': formValue.dealer_id,
                                //  'mis_category_id': formValue.mis_category_id,
                                //  'item_id': formValue.item_id,
                                //  'group_id': formValue.group_id,
                                //  'code_id': formValue.code_id,
                                //  'village_name': formValue.village_name,
                                //  'taluka_name': formValue.taluka_name,
                                //  'district_name': formValue.district_name,
                                //  'state_name': formValue.state_name,
                                //  'so_number': formValue.so_number,
                                //  'so_type_id': formValue.so_type_id

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
                     { data: 'so_number', name: 'sales_order.so_number', },
                     { data: 'so_date', name: 'sales_order.so_date', },
                     { data: 'so_from_value_fix', name: 'sales_order.so_from_value_fix', },
                     { data: 'so_type_value_fix', name: 'sales_order.so_type_value_fix', },
                     { data: 'customer_group_name', name: 'customer_groups.customer_group_name', },
                     { data: 'name', name: 'name', },  
                     { data: 'dealer_name', name: 'dealers.dealer_name', },
                     { data: 'customer_reg_no', name: 'sales_order.customer_reg_no', },
                     { data: 'village_name', name: 'villages.village_name', },   
                     { data: 'taluka_name', name: 'talukas.taluka_name', },
                     { data: 'district_name', name: 'districts.district_name', },
                     { data: 'state_name', name: 'states.state_name', },  
                     { data: 'country_name', name: 'countries.country_name', },  
                     { data: 'mis_category', name: 'mis_category.mis_category', },
                     { data: 'item_name', name: 'items.item_name',
                     
                      render: function(data, type, row, meta) {
                        if (row.fitting_item === "yes") {
                            return `${data} <span><a><i class="action-icon iconfa-eye-open eyeIcon1" data-so_details_id="${row.so_details_id}"></i></a></span>`;
                        } else {
                            return data;
                        }
                       }
                      },

                     { data: 'item_group_name' ,name: 'item_groups.item_group_name'},
                     { data: 'item_code', name: 'items.item_code', },
                     { data: 'pend_so_qty', name: 'pend_so_qty', },
                     { data: 'unit_name' ,name: 'units.unit_name'},
                     { data: 'remarks' ,name: 'sales_order_details.remarks',},
                     { data: 'so_sequence' ,name: 'sales_order.so_sequence' , visible:false},




                ],
                initComplete: function () {
                    // Exclude first column (index 0) from search
                    initColumnSearch('#dyntable', []);
                },
            });

    }

    jQuery("#psofordispatchSearchForm").validate({
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
        var searchForm = jQuery("#psofordispatchSearchForm");
        searchForm.find('#to_date').val('');
        searchForm.find('#from_date').val('');
        searchForm.find('#customer_name').val('');
        searchForm.find('#location_id').val('').trigger('liszt:updated');
        searchForm.find('#so_type_id').val('').trigger('liszt:updated');
        searchForm.find('#cust_group_id').val('').trigger('liszt:updated');
        searchForm.find('#dealer_id').val('').trigger('liszt:updated');
        searchForm.find('#mis_category_id').val('').trigger('liszt:updated');
        searchForm.find('#item_id').val('').trigger('liszt:updated');
        searchForm.find('#group_id').val('').trigger('liszt:updated');
        searchForm.find('#code_id').val('').trigger('liszt:updated');
        searchForm.find('#village_name').val('');
        searchForm.find('#taluka_name').val('');
        searchForm.find('#district_name').val('');
        searchForm.find('#state_name').val('');
        searchForm.find('#so_number').val('');
        loadDataTable();
    });

    var today       = new Date();
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
    });


}); // .ready

jQuery(document).on('click', '.eyeIcon1', function () {


        var  so_detail_id = jQuery(this).data('so_details_id');

        if (so_detail_id != '') {

        var formUrl =  RouteBasePath + "/get-fitting_so_item_data_for_dispatch?so_detail_id=" + so_detail_id;

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
