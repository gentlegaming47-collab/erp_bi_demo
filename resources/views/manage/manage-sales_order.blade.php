@extends('layouts.app',['pageTitle' => 'Sales Order'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Sales Order</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
           @if(hasAccess("sales_order","add"))
           <a href="{{ route('add-sales_order') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Sales Order</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
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
                    <th class="head0">Actions</th>
                    <th class="head1">SO No.</th>
                    <th class="head0">SO Date</th>
                    <th class="head1">SO From </th>
                    <th class="head1">SO Type </th>
                    <th class="head1">Cust. Group  </th>
                    {{-- <th class="head1">Value </th> --}}
                    <th class="head1">Customer / Location </th>             
                    <th class="head0">Dealer </th>
                    <th class="head0">Reg. No. </th>
                    <th class="head0">MIS Category </th>
                    <th class="head0">Country </th>
                    <th class="head0">State </th>
                    <th class="head0">District  </th>
                    <th class="head0">Taluka  </th>
                    <th class="head0">Village </th>
                    <th class="head0">Net Amount</th>
                    <th class="head1">File Upload</th>
                    <th class="head0">Sp. Note  </th>
                    {{-- <th class="head0">Item</th>
                    <th class="head0">Code </th>
                    <th class="head0">Group  </th>
                    <th  class="head0">SO Qty.</th>
                    <th  class="head0">Unit </th>
                    <th  class="head0">Rate/Unit  </th>
                    <th  class="head0">Amount</th>     --}}
                    <th class="head0">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head0">Created by</th>
                    <th class="head1">Created on</th>
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
 loadDataTable();

jQuery(document).on('change', '#trans_from_date, #trans_to_date', function () {
    let fromValid = jQuery('#trans_from_date').valid();
    let toValid = jQuery('#trans_to_date').valid();

    if (fromValid && toValid) {
        loadDataTable();
    }
});
jQuery('#export-excel').on('click',function(){
    jQuery('.export_sales_order').click();
});


function loadDataTable() {
    if (jQuery.fn.DataTable.isDataTable('#dyntable')) {
                    jQuery('#dyntable').DataTable().destroy();
            }

    var data = new FormData(document.getElementById('psfdSearchForm'));
    var formValue = Object.fromEntries(data.entries());

     table = jQuery('#dyntable').DataTable({

    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "bScrollCollapse": true,
    // "order": [[ 1, 'asc' ]],
    "order": [[ 2, 'desc' ],[ 22, 'desc' ]],
    // "order": [[ 2, 'desc' ],[ 20, 'desc' ]],
    //"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
        pageLength : 25,
        dom: 'Blfrtip',
        buttons:
            [
                {
                    extend:'excel',
                    filename: 'Sales Order',
                    title:"",
                    className: 'export_sales_order d-none',
                    exportOptions: {
                            columns: function(idx, data, node) {
                                return idx !== 0 && table.column(idx).visible();
                            },
                            modifier: {
                                page: 'all'
                            }
                    },
                    action: newexportaction
                    // exportOptions: {
                    //     columns: ':not(:eq(0))',
                    //     modifier: {
                    //         page: 'all'
                    //     }
                    // },

                }
            ],

    ajax: {
            url: "{{ route('listing-sales_order') }}",
            type: "POST",
            headers: headerOpt,
            data: {
                    'trans_from_date': formValue.trans_from_date,
                    'trans_to_date':formValue.trans_to_date,
                },
            error: function (jqXHR, textStatus, errorThrown){
                jQuery('#dyntable_processing').hide();
                if(jqXHR.status == 401){
                    // toastError(jqXHR.statusText);
                    jAlert(jqXHR.statusText);
                }else{
                // toastError('Somthing went wrong!');
                    jAlert('Somthing went wrong!');
                }
                console.log(JSON.parse(jqXHR.responseText));
            }
    },

    columns: [
        {
            data: 'options',
            name: 'options',
            orderable: false,
            searchable: false,
        },
        { data: 'so_number', name: 'sales_order.so_number', },
        { data: 'so_date', name: 'sales_order.so_date', },
        { data: 'so_from_value_fix', name: 'so_from_value_fix', },
        { data: 'so_type_value_fix', name: 'sales_order.so_type_value_fix', },
        { data: 'customer_group_name', name: 'customer_groups.customer_group_name', },
        { data: 'name', name: 'name', },  
        { data: 'dealer_name', name: 'dealers.dealer_name', },
        { data: 'customer_reg_no', name: 'sales_order.customer_reg_no', },
        { data: 'mis_category', name: 'mis_category.mis_category', },
        { data: 'country_name', name: 'countries.country_name', },
        { data: 'state_name', name: 'states.state_name', },
        { data: 'district_name', name: 'districts.district_name', },
        { data: 'taluka_name', name: 'talukas.taluka_name', },
        { data: 'village_name', name: 'villages.village_name', },
        { data: 'net_amount', name: 'sales_order.net_amount', },
        { data: 'file_upload' ,name: 'file_upload' ,},
        { data: 'special_notes' ,name: 'sales_order.special_notes' ,},
        //{ data: 'item_name' ,name: 'items.item_name'},
        //{ data: 'item_code' ,name: 'items.item_code'},
        //{ data: 'item_group_name' ,name: 'item_groups.item_group_name'},
        //{ data: 'so_qty' ,name: 'sales_order_details.so_qty'},
        //{ data: 'unit_name' ,name: 'units.unit_name'},
        //{ data: 'rate_per_unit' ,name: 'sales_order_details.rate_per_unit'},
        //{ data: 'so_amount' ,name: 'sales_order_details.so_amount'}, 
        { data: 'last_by_user_id', name: 'last_by_user_id', },
        { data: 'last_on', name: 'sales_order.last_on', },
        { data: 'created_by_user_id', name: 'created_by_user_id', },
        { data: 'created_on', name: 'sales_order.created_on', },
        { data: 'so_sequence' ,name: 'sales_order.so_sequence' , visible:false},


    ],
initComplete: function () {
    // Exclude first column (index 0) from search
    initColumnSearch('#dyntable', [0,16]);
}

    });
}

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
                var searchForm = jQuery('#psfdSearchForm');
                loadDataTable();
            }

        });
jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
                url: "{{ route('delete-sales_order') }}",
                type: 'GET',
                data: "id="+data["id"],
                headers: headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if(data.response_code == 1){
                        // toastSuccess(data.response_message);
                        jAlert(data.response_message);
                        table.row(jQuery(this)).draw(false);
                    }else{
                        // toastError(data.response_message);
                        jAlert(data.response_message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown){
                    if(jqXHR.status == 401){
                        // toastError(jqXHR.statusText);
                        jAlert(jqXHR.statusText);
                    }else{
                       // toastError('Somthing went wrong!');
                        jAlert('Somthing went wrong!');
                    }
                    console.log(JSON.parse(jqXHR.responseText));
                }
        });
        }
    });
});
});
var sessionError = @json(session('error'));
    if(sessionError != null){
        toastError(sessionError);       
    }
</script>
@endsection
