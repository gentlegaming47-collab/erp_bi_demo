@extends('layouts.app',['pageTitle' => 'Delivery Challanb'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-delivery_challan') }}">Delivery Challan</a> <span class="separator"></span></li>
    <li>Add Delivery Challan</li>
</ul>
@endsection
@section('content')
@include('modals.transporter_modal')
<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-delivery_challan') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add Delivery Challan</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonDeliveryChallanForm" class="stdform" method="post">
      @csrf
      
      
      @include('common_form_files.delivery_challan')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="delivery_challan">{{ config('define.value.add') }}</button>
                            {{-- <button type="submit" class="btn btn-primary">Print</button> --}}
                            {{-- <button type="submit" class="btn btn-primary">Fitting Item Print</button> --}}
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


