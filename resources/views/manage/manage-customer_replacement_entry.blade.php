@extends('layouts.app',['pageTitle' => 'Customer Replacement Entry'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Customer Replacement Entry</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
           @if(hasAccess("customer_replacement_entry","add"))
           <a href="{{ route('add-customer_replacement_entry') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Customer Replacement Entry</h4>
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
                    <th class="head1">CRE No.</th>
                    <th class="head0">Date</th>     
                    <th class="head0">Customer</th>                    
                    <th class="head0">Reg. No.</th>                    
                    <th class="head0">Village</th>                    
                    <th class="head0">Taluka</th>                    
                    <th class="head0">District</th>                    
                    <th class="head0">State</th>   
                    <!-- <th class="head0">Country</th>    -->
                    <!-- <th class="head0">Pin Code</th>                     -->
                    <th class="head0">Item</th>                                        
                    <th class="head0">Item Code</th>                                        
                    <th class="head0">Item Group</th>                                        
                    <th class="head0">Return Qty.</th>  
                    <th class="head0">Unit</th>  
                    <th class="head0">Remark</th>                                        
                    <th class="head0">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head0">Created by</th>
                    <th class="head0">Created on</th>
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
        jQuery('.export_cre').click();
    });
    loadDataTable();
    jQuery(document).on('change', '#trans_from_date, #trans_to_date', function () {
        let fromValid = jQuery('#trans_from_date').valid();
        let toValid = jQuery('#trans_to_date').valid();

        if (fromValid && toValid) {
            loadDataTable();
        }
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
    // "order": [[ 1, 'asc' ]],
    "scrollX":true,
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "bScrollCollapse": true,
    "order": [[ 2, 'desc' ],[ 19, 'desc' ]],
    // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
        pageLength : 25,
        dom: 'Blfrtip',
        buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Customer Replacement Entry',
                        title:"",
                        className: 'export_cre d-none',
                        exportOptions: {
                            columns: function(idx, data, node) {
                                return idx !== 0 && idx !== 19 && table.columns(idx).visible();
                            },
                            // columns: ':not(:eq(0))',
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction

                    }
                ],

    ajax: {
            url: "{{ route('listing-customer_replacement_entry') }}",
            type: "POST",
            headers: headerOpt,
            data: {
                    'trans_from_date': formValue.trans_from_date,
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
        {
            data: 'options',
            name: 'options',
            orderable: false,
            searchable: false,
        },
        { data: 'cre_number' ,name: 'customer_replacement_entry.cre_number' , },
        { data: 'cre_date' ,name: 'customer_replacement_entry.cre_date' ,},    
        { data: 'rep_customer_name' ,name: 'customer_replacement_entry.rep_customer_name' ,},
        { data: 'customer_reg_no' ,name: 'customer_replacement_entry.customer_reg_no' , },
        { data: 'cre_village' ,name: 'customer_replacement_entry.cre_village' ,},
        // { data: 'village_name' ,name: 'villages.village_name' ,},
        { data: 'taluka_name' ,name: 'talukas.taluka_name' ,},
        { data: 'district_name' ,name: 'districts.district_name' ,},
        { data: 'state_name' ,name: 'states.state_name' ,},
        // { data: 'country_name' ,name: 'countries.country_name' ,},
        // { data: 'cre_pincode' ,name: 'customer_replacement_entry.cre_pincode' ,},
        { data: 'item_name', name: 'items.item_name', },
        { data: 'item_code', name: 'items.item_code', },
        { data: 'item_group_name', name: 'item_groups.item_group_name', },
        { data: 'return_qty', name: 'customer_replacement_entry_details.return_qty', },    
        { data: 'unit_name', name: 'units.unit_name', },    
        { data: 'remark', name: 'customer_replacement_entry_details.remark', },    
        { data: 'last_by_user_id', name: 'last_by_user_id',},
        { data: 'last_on', name: 'customer_replacement_entry.last_on', },
        { data: 'created_by_user_id', name: 'created_by_user_id',},
        { data: 'created_on', name: 'customer_replacement_entry.created_on',},
        { data: 'cre_sequence' ,name: 'customer_replacement_entry.cre_sequence',  visible:false},  
    ],
    initComplete: function () {
    // Exclude first column (index 0) from search
        initColumnSearch('#dyntable', [0]);
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
				url: "{{ route('remove-customer_replacement_entry') }}",
				type: 'GET',
				data: "id="+data["cre_id"],
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
</script>
@endsection
