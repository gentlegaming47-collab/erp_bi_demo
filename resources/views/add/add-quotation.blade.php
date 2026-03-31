@extends('layouts.app',['pageTitle' => 'Quotation'])

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

    <li><a href="{{ route('manage-quotation') }}">Quotation</a> <span class="separator"></span></li>

    <li>Add Quotation</li>

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

            <a href="{{ route('manage-quotation') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Quotation</h4>

    </div>




        <div class="widgetcontent">
                <form id="quotationform" class="stdform" method="post">
                        @csrf
                        {{-- first row start --}}
                        <input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
                        <input type="hidden" value="Quotation" name="hidViewPage" id="hidViewPage"/>
                        <input type="hidden" value="addPage" name="itemPage" id="itemHiddenPage"/>


                      @include('common_form_files.quotation')

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
    getQuotStates();
});
</script>

@php
    $changedItemIds =  [];
@endphp



<script>
    var getItem = [<?php echo json_encode(getItem($changedItemIds)); ?>];
    </script>
    <script type="text/javascript" src="{{ asset('views/js/quotation.js?ver='.getJsVersion()) }}"></script>
@endsection
