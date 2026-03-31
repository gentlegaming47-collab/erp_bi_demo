@extends('layouts.app',['pageTitle' => 'SO Short Close'])



@section('header')

<style>
  #dyntable_filter label{
    width: auto;
    white-space: nowrap;
    padding: 0;
  }

  #dyntable_length label{
    width: 0;
    white-space: nowrap;
    float: none;
    text-align: unset;
    padding: 0;
  }
  </style>

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-transaction_so_short_close') }}">SO Short Close</a> <span class="separator"></span></li>

    <li>Add SO Short Close </li>

</ul>

@endsection



@section('content')

<div class="widgetbox">
    <div id="show-progress"></div>

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-transaction_so_short_close') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add SO Short Close</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonTransactionSOShortClose" class="stdform" method="post">
            @csrf
          
      
            @include('common_form_files.transaction_so_short_close')
          
            <div class="row">
              <div class="span-6">            
                  <div class="par control-group form-control">
                      <label class="control-label"></label>
                      <div class="controls">
                              <span class="formwrapper"> 
                                  <button type="submit" class="btn btn-primary">{{ config('define.value.add') }}</button>
                      </div>
                  </div>
              </div>
      
            </div>

          </form>
        </div>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection



@section('scripts')
<script>
  var table;
jQuery(document).ready(function() {
var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};

 table= jQuery('#dyntable').DataTable({

"processing": true,
"serverSide": true,
 "scrollX": true,
"sScrollX": "100%",
"sScrollXInner": "110%",
"bScrollCollapse": true,
// "order": [[ 1, 'asc' ]],
// "order": [[ 1, 'desc' ],[ 2, 'desc' ]],
//"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
 

ajax: {
        url: "{{ route('get-transaction_so_short_close') }}",
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
    { data: 'so_number', name: 'sales_order.so_number', },
    { data: 'so_date', name: 'sales_order.so_date', },
    { data: 'so_from_value_fix', name: 'so_from_value_fix', },
    { data: 'so_type_value_fix', name: 'sales_order.so_type_value_fix', },
    { data: 'customer_group_name', name: 'customer_groups.customer_group_name', },
    { data: 'name', name: 'name', },  
    { data: 'item_name', name: 'items.item_name' },  
    { data: 'item_code', name: 'items.item_code' },  
    { data: 'item_group_name', name:  'item_groups.item_group_name', },  
    { data: 'so_qty', name:  'sales_order_details.so_qty', },  
    { data: 'pend_so_qty', name:  'pend_so_qty', },  
    { data: 'unit_name', name:   'units.unit_name', },  
    { data: 'short_close_qty', name:   'short_close_qty', },  
    { data: 'reason', name:   'reason', },  



   

],
initComplete: function () {
                // Exclude first column (index 0) from search
                    initColumnSearch('#dyntable', [0,13,14]);
                }   

});
});
</script>
@endsection
