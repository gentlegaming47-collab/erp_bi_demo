@extends('layouts.app',['pageTitle' => 'Sales Order'])

@section('header')

<style>
    #soPartTable_filter label{
      width: auto;
      white-space: nowrap;
      padding: 0;
    }
  
    #soPartTable_length label{
      width: 0;
      white-space: nowrap;
      float: none;
      text-align: unset;
      padding: 0;
    }
</style>

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-sales_order') }}">Sales Order</a> <span class="separator"></span></li>

    <li>Add Sales Order</li>

</ul>

@endsection

@section('content')


@include('modals.dealer_agreement_modal')
@include('modals.salesOrderDetails')
@include('modals.pending_material_request')
@include('modals.village_modal')
@include('modals.state_modal')
@include('modals.taluka_modal')
@include('modals.customer_search_modal')
@include('modals.dealer_modal')
@include('modals.previousSoDetail')
@include('modals.mis_cat_modal')
@include('modals.contact_modal')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

            <a href="{{ route('manage-sales_order') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Sales Order</h4>

    </div>




        <div class="widgetcontent">
                <form id="salesorderform" class="stdform" method="post">
                        @csrf
                        {{-- first row start --}}
                        <input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
                        <input type="hidden" value="salesOrder" name="hidViewPage" id="hidViewPage"/>
                        <input type="hidden" value="addPage" name="itemPage" id="itemHiddenPage"/>
                    
                       
                      @include('common_form_files.sales_order')

                      <div class="row">
                        <div class="span-6">            
                            <div class="par control-group form-control">
                                <label class="control-label"></label>
                                <div class="controls">
                                        <span class="formwrapper"> 
                                            <button type="submit" class="btn btn-primary" id="sup_rejection_button">{{ config('define.value.add') }}</button>
                                            {{-- <button class="btn btn-primary"> Print</button></span> --}}
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
// function soType(){
//     let sel= jQuery('input[name="so_from_id_fix"]:checked').val();
//       if(sel == '2'){
//           jQuery('div#show').hide();
//           jQuery('div#hide').show();
//           jQuery('#soPartTable tbody').empty();
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
    // $changedItemIds =  [];
    // $changedFittingItemIds =  [];
@endphp



<script>
    // var getItem = [<?php //echo json_encode(getItem($changedItemIds)); ?>];
  
    // var getSalesFittingItem = [<?php //echo json_encode(getSalesFittingItem($changedFittingItemIds)); ?>];
    
    </script> --}}
    
    <script type="text/javascript" src="{{ asset('views/js/sales_order.js?ver='.getJsVersion()) }}"></script>
@endsection
