@extends('layouts.app',['pageTitle' => 'Loading Summary'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Loading Summary</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Loading Summary</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
            <form id="psfdSearchForm" name="psfdSearchForm" class="stdform">

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
                    {{-- <div class="span-6">
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
                    </div> --}}

                </div>


                {{-- <div class="row">

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
                                <label class="control-label" for="district_name">District</label>
                                <span class="formwrapper">
                                    <input type="text" name="district_name" id="district_name" onkeyup="suggestCity(event,this)" class="form-control" autocomplete="nope"/>
                                    <div id="district_list" class="suggestion_list" ></div>
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
                                        <option value="">All Items</option>
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
                                <label class="control-label" for="dp_number">Disp. Plan No.</label>
                                <span class="formwrapper">
                                    <input type="text" name="dp_number" id="dp_number" class="form-control" onkeyup="suggestDpNo(event,this)" autocomplete="nope"/>
                                    <div id="dp_number_list" class="suggestion_list" ></div>
                                </span>
                        </div>
                    </div>
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
                        <th class="head0">Vehicle No.</th>
                        <th class="head0">Transporter</th>
                        <th class="head0">Loading By</th>
                        <th class="head0">Driver</th>
                        <th class="head0">Mobile No.</th>
                        <th class="head0">SO No.</th>
                        <th class="head0">SO Date</th>
                        <th class="head0">Cust./Location</th>
                        <th class="head0">Dealer</th>
                        <th class="head0">Village</th>
                        <th class="head0">District</th>
                        <th class="head0">Item</th>
                        <th class="head0">Code</th>
                        <th class="head0">Group</th>
                        <th class="head0">Plan Qty.</th>
                        <th class="head0">Unit</th>
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
        jQuery('.le_summary').click();
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
                "order": [[ 0, 'asc' ]], // date wise desc
                // "order": [[ 1, 'desc' ],[ 18, 'desc' ]],
                pageLength : 25,
                dom: 'Blfrtip',
                buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Loading Entry Summary',
                        title:"",
                        className: 'le_summary d-none',
                        exportOptions: {
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction
                    }
                ],
                ajax: {
                        url: "{{ route('listing-loading_entry_summary') }}",
                        type: "POST",
                        headers: headerOpt,
                        data : {

                                    'from_date': formValue.trans_from_date,
                                    'to_date':formValue.trans_to_date,
                                //  'from_date':formValue.from_date,
                                //  'to_date':formValue.to_date,
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
                     { data: 'vehicle_no' ,name: 'loading_entry.vehicle_no' , },
                     { data: 'transporter_name' ,name: 'transporters.transporter_name' , },
                     { data: 'loading_by' ,name: 'loading_entry.loading_by'},
                     { data: 'driver_name',name: 'loading_entry.driver_name'} ,
                     { data: 'driver_mobile_no',name: 'loading_entry.driver_mobile_no'} ,
                     { data: 'so_number', name: 'sales_order.so_number', },
                     { data: 'so_date', name: 'sales_order.so_date', },   
                     { data: 'name', name: 'name', },  
                     { data: 'dealer_name', name: 'dealers.dealer_name', },
                     { data: 'village_name', name: 'villages.village_name', },   
                     { data: 'district_name', name: 'districts.district_name', },
                     { data: 'item_name', name: 'items.item_name', },
                     { data: 'item_code', name: 'items.item_code', },
                     { data: 'item_group_name' ,name: 'item_groups.item_group_name'},
                     { data: 'plan_qty', name: 'plan_qty', },
                     { data: 'unit_name' ,name: 'units.unit_name'},
                    //  { data: 'dp_sequence', name: 'dispatch_plan.dp_sequence', visible:false}


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

    jQuery("#psfdSearchForm").validate({
        onkeyup: false,
        onfocusout: false,

    rules: {

        // from_date: {

        //     required: function(e){
        //         if(jQuery("#psfdSearchForm").find('#to_date').val() != ""){
        //             return true;
        //         }else{
        //             return false;
        //         }
        //     },
        //     lessThan: "#to_date"

        // },

        // to_date: {

        //     required: function(e){
        //         if(jQuery("#psfdSearchForm").find('#from_date').val() != ""){
        //             return true;
        //         }else{
        //             return false;
        //         }
        //     },
        //     greaterThan: "#from_date"

        // },

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
        searchForm.find('#customer_name').val('');
        searchForm.find('#location_id').val('').trigger('liszt:updated');
        searchForm.find('#village_name').val('');
        searchForm.find('#district_name').val('');
        searchForm.find('#dealer_id').val('').trigger('liszt:updated');
        searchForm.find('#item_id').val('').trigger('liszt:updated');
        searchForm.find('#so_number').val('');
        searchForm.find('#dp_number').val('');
        
        DataYearWise();    
        loadDataTable();
    });

   


}); // .ready
</script>
<script src="{{ asset('js/view/suggestreport.js?ver='.getJsVersion()) }}"></script>
@endsection
