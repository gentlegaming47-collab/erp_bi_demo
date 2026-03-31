@extends('layouts.app',['pageTitle' => 'Raw Material'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Raw Material</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            {{-- <a href="{{ route('export-Country') }}" class="btn btn-inverse pre2">Export</a> --}}
           @if(hasAccess("raw-material","add"))
           <a href="{{ route('add-raw_material') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Raw Material</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr>
                    <th class="head0">Actions</th>
                    <th class="head1">Raw Material</th>
                    <th class="head1">Raw Material Group </th>                    
                    <th class="head1">Unit</th>
                    <th class="head1">Min. stock Qty.</th>
                    <th class="head1">Max. stock Limit</th>
                    <th class="head1">Re. Order Qty.</th>
                    <th class="head1">HSN Code</th>
                    <th class="head1">Rate per unit</th>                    
                    <th class="head0">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head0">Created by</th>
                    <th class="head1">Created on</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
jQuery(document).ready(function() {
var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};
var table= jQuery('#dyntable').DataTable({

"processing": true,
"serverSide": true,
"scrollX" : true,
"order": [[ 1, 'asc' ]],

ajax: {
        url: "{{ route('listing-raw-material') }}",
        type: "POST",
        headers: headerOpt,
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
    { data: 'raw_material', name: 'raw_material', },    
    { data: 'raw_material_group_nm', name: 'raw_material_group_nm', },        
    { data: 'unit', name: 'unit', },
    { data: 'min_stock_qty', name: 'min_stock_qty', },
    { data: 'max_stock_qty', name: 'max_stock_qty', },
    { data: 're_order_qty', name: 're_order_qty', },
    { data: 'hsn_code', name: 'hsn_code', },
    { data: 'rate_per_unit', name: 'rate_per_unit', },    
    { data: 'last_by_user_id', name: 'last_by_user_id', searchable: false,},
    { data: 'last_on', name: 'last_on', searchable: false,},
    { data: 'created_by_user_id', name: 'created_by_user_id', searchable: false,},
    { data: 'created_on', name: 'created_on', searchable: false,}
]


});

jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
                url: "{{ route('remove-raw-material') }}",
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
</script>
@endsection
