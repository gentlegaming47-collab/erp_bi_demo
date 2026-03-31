@extends('layouts.app',['pageTitle' => 'Sales Order'])

@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-sales_order') }}">Sales Order</a> <span class="separator"></span></li>

    <li>Edit Sales Order</li>

</ul>

@endsection

@section('content')


@include('modals.dealer_agreement_modal')
@include('modals.salesOrderDetails')
@include('modals.pending_material_request')
@include('modals.state_modal')
@include('modals.taluka_modal')
@include('modals.customer_search_modal')
@include('modals.village_modal')
@include('modals.dealer_modal')
@include('modals.previousSoDetail')
@include('modals.mis_cat_modal')
@include('modals.contact_modal')



<div class="widgetbox">

    <div id="show-progress"></div>
    <div class="headtitle">
        <div class="btn-group">

            <a href="{{ route('manage-sales_order') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit Sales Order</h4>

    </div>




    <div class="widgetcontent">
        <form id="salesorderform" class="stdform" method="post">
            @csrf
            {{-- first row start --}}
            <input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
            <input type="hidden" value="Customer" name="hidViewPage" id="hidViewPage"/>
            <input type="hidden" value="editPage" name="itemPage" id="itemHiddenPage"/>
            <input type="hidden" value="{{base64_decode($id)}}" name="id"/>

          @include('common_form_files.sales_order')


          <div class="row">
            <div class="span-6">            
                <div class="par control-group form-control">
                    <label class="control-label"></label>
                    <div class="controls">
                            <span class="formwrapper"> 
                                <button type="submit" class="btn btn-primary" id="sup_rejection_button">Update</button>
                                {{-- <button class="btn btn-primary"> Print</button> --}}
                            </span>
                    </div>
                </div>
            </div>
    
          </div>
    </form>



    </div><!--widgetcontent-->




@endsection



@section('scripts')

<script>
    jQuery(document).ready(function(){
        soType();
        getSoStates();
    });
//     function soType(){
//     let sel= jQuery('input[name="so_from_id_fix"]:checked').val();
    
//       if(sel == '2'){
//           jQuery('div#show').hide();
//           jQuery('div#hide').show();
//       }
//       else{
//           jQuery('div#hide').hide();
//           jQuery('div#show').show();
//       }
//       soTypeFix();
// }   
</script>


{{-- 
@php

// use App\Models\SalesOrderDetail;

// $changedItemIds = SalesOrderDetail::leftJoin('items', 'items.id', '=', 'sales_order_details.item_id')           
//     ->where('sales_order_details.so_id', base64_decode($id))
//     ->where(function($query) {
//         $query->where('items.status', 'deactive')
//               ->orWhere('items.service_item', 'Yes');
//     })
//     ->pluck('sales_order_details.item_id')
//     ->toArray();

// $changedFittingItemIds = SalesOrderDetail::
//     leftjoin('sales_order_detail_details','sales_order_detail_details.so_details_id','sales_order_details.so_details_id')
//     ->leftJoin('items', 'items.id', '=', 'sales_order_detail_details.item_id')           
//     ->where('sales_order_details.so_id', base64_decode($id))
//     ->where(function($query) {
//         $query->where('items.status', 'deactive')
//               ->orWhere('items.service_item', 'Yes');
//     })
//     ->pluck('sales_order_detail_details.item_id')
//     ->toArray();
    
@endphp
<script>
   
  
    // var getItem = [<?php //echo json_encode(getItem($changedItemIds)); ?>];
    // var getSalesFittingItem = [<?php //echo json_encode(getSalesFittingItem($changedFittingItemIds)); ?>];
   
    
    </script> --}}


    <script type="text/javascript" src="{{ asset('views/js/sales_order.js?ver='.getJsVersion()) }}"></script>

@endsection
