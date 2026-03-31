@extends('layouts.app',['pageTitle' => 'Delivery Challan'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Delivery Challan</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            {{-- <a href="{{ route('export-State') }}" class="btn btn-inverse pre2">Export</a> --}}
           @if(hasAccess("delivery_challan","add"))
           <a href="{{ route('add-delivery_challan') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Delivery Challan</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr>
                    <th class="head0">Actions</th>
                    <th class="head0">DC No.</th>
                    <th class="head0">DC Date</th>
                    <th class="head0" style="display:none;"></th>
                    <th calss="head0">Customer</th>
                    <th calss="head0">Location</th>
                    <th calss="head1">Item Name</th>
                    <th class="head0">Item Code</th>                    
                    <th class="head0">SO Qty.</th>                  
                    <th class="head0">DC Qty.</th>                  
                    <th class="head0">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head0">Created by</th>
                    <th class="head0">Created on</th>
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
// "order": [[ 1, 'asc' ]],
"order": [[ 2, 'desc' ],[ 3, 'desc' ]],

ajax: {
        url: "{{ route('listing-delivery_challan') }}",
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

// columns: [
//      {
//         data: 'options',
//         name: 'options',
//         orderable: false,
//         searchable: false,
//     },
//     { data: 'return_number' ,name: 'item_return.return_number' , },
//     { data: 'return_date' ,name: 'item_return.return_date' ,},    
//     { data: 'return_sequence' ,name: 'item_return.return_sequence',  visible:false},
//     { data: 'supplier_name' ,name: 'suppliers.supplier_name' ,},
//     { data: 'item_name', name: 'items.item_name', },
//     { data: 'item_code', name: 'items.item_code', },    
//     { data: 'return_qty', name: 'item_return_details.return_qty', },    
//     { data: 'last_by_user_id', name: 'last_by_user_id', searchable: false,},
//     { data: 'last_on', name: 'last_on', searchable: false,},
//     { data: 'created_by_user_id', name: 'created_by_user_id', searchable: false,},
//     { data: 'created_on', name: 'created_on', searchable: false,}
// ]
});

jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
				url: "{{ route('remove-delivery_challan') }}",
				type: 'GET',
				data: "id="+data["dc_id"],
                headers: headerOpt,
                dataType: 'json',
                processData: false,
				success: function (data) {
                    if(data.response_code == 1){			          
                        jAlert(data.response_message);
                        table.row(jQuery(this)).draw(false);
                    }else{			           
                        jAlert(data.response_message);
                    }
				},
                error: function (jqXHR, textStatus, errorThrown){
                    if(jqXHR.status == 401){
                    
                        jAlert(jqXHR.statusText);
                    }else{
                     
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
