@extends('layouts.app',['pageTitle' => 'Customer Replacement Entry'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-customer_replacement_entry') }}">Customer Replacement Entry</a> <span class="separator"></span></li>
    <li>Add Customer Replacement Entry</li>
</ul>
@endsection
@section('content')
@include('modals.customer_search_modal')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-customer_replacement_entry') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add Customer Replacement Entry</h4>
  </div>
  <div class="widgetcontent">
    <form id="customerReplacementEntryForm" class="stdform" method="post">
      @csrf

      @include('common_form_files.customer_replacement_entry')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="submitBtn">{{ config('define.value.add') }}</button>
                            {{-- <button type="submit" class="btn btn-primary">Print</button> --}}
                </div>
            </div>
        </div>

    </div>
     
    </form>
  </div>
  <!--widgetcontent-->

</div>
<!--widget-->
@endsection



