@extends('layouts.app',['pageTitle' => 'Customer'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Customer</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            {{-- <a href="{{ route('export-Customer') }}" class="btn btn-inverse pre2">Export</a> --}}
           @if(hasAccess("customer","add"))
           <a href="{{ route('add-customer') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Customer</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr>
                    <th class="head1">Actions</th>
                    <th class="head1">Customer Code</th>
                    <th class="head0">Customer</th>
                    <th class="head1">Group</th>                    
                    <th class="head1">Reg. No.</th>
                    <th class="head0">Village</th>
                    <th class="head1">Address</th>
                    <th class="head1">Pin Code</th>                    
                    <th class="head1">Mobile No.</th>
                    <th class="head0">Email ID</th>                    
                    <th class="head1">PAN</th>
                    <th class="head0">GSTIN</th>
                    <th class="head1">Adhar No.</th>
                    <th class="head1">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head1">Created by</th>
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
"order": [[ 1, 'asc' ]],
"scrollX":true,

ajax: {
        url: "{{ route('listing-customer') }}",
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
    { data: 'customer_code' ,name: 'customers.customer_code' , },    
    { data: 'customer_name' ,name: 'customers.customer_name' , },   
    { data: 'customer_group_name' ,name: 'customer_groups.customer_group_name' ,},
    { data: 'register_number' ,name: 'customers.register_number' , },    
    { data: 'village_name' ,name: 'villages.village_name' , },    
    { data: 'address' ,name: 'customers.address' , },    
    { data: 'pincode' ,name: 'customers.pincode' , },    
    { data: 'mobile_no' ,name: 'customers.mobile_no' , },    
    { data: 'email' ,name: 'customers.email' , },    
    { data: 'PAN' ,name: 'customers.PAN' , },    
    { data: 'gst_code' ,name: 'customers.gst_code' , },    
    { data: 'aadhar_no' ,name: 'customers.aadhar_no' , },       
    { data: 'last_by_user_id',name: 'customers.last_by_user_id' , },
    { data: 'last_on',name: 'customers.last_on' , },
    { data: 'created_by_user_id',name: 'customers.created_by_user_id' ,},
    { data: 'created_on',name: 'customers.created_on' , },
]
});
jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
                url: "{{ route('remove-customer') }}",
                type: 'GET',
                data: "id="+data["id"],
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
