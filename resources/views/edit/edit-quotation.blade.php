@extends('layouts.app',['pageTitle' => 'Quotation'])

@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-quotation') }}">Quotation</a> <span class="separator"></span></li>

    <li>Edit Quotation</li>

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

            <a href="{{ route('manage-quotation') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit Quotation</h4>

    </div>




    <div class="widgetcontent">
        <form id="quotationform" class="stdform" method="post">
            @csrf
            {{-- first row start --}}
            <input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
            <input type="hidden" value="Quotation" name="hidViewPage" id="hidViewPage"/>
            <input type="hidden" value="editPage" name="itemPage" id="itemHiddenPage"/>
            <input type="hidden" value="{{base64_decode($id)}}" name="id"/>

          @include('common_form_files.quotation')


          <div class="row">
            <div class="span-6">            
                <div class="par control-group form-control">
                    <label class="control-label"></label>
                    <div class="controls">
                            <span class="formwrapper"> 
                                <button type="submit" class="btn btn-primary" id="sup_rejection_button">Update</button>
                                
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
        getQuotStates();
    });

</script>
@php

use App\Models\QuotationDetails;

$changedItemIds = QuotationDetails::leftJoin('items', 'items.id', '=', 'quotation_details.item_id')           
    ->where('quotation_details.quot_id', base64_decode($id))
    ->where(function($query) {
        $query->where('items.status', 'deactive');
    })
    ->pluck('quotation_details.item_id')
    ->toArray();
    
@endphp
<script>
   
    var getItem = [<?php echo json_encode(getItem($changedItemIds)); ?>];
    
    </script>
    <script type="text/javascript" src="{{ asset('views/js/quotation.js?ver='.getJsVersion()) }}"></script>

@endsection
