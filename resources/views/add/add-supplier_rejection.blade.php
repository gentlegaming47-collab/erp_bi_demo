@extends('layouts.app',['pageTitle' => 'Supplier Return Challan'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-supplier_rej_challan') }}">Supplier Return Challan</a> <span class="separator"></span></li>
    <li>Add Supplier Return Challan</li>
</ul>
@endsection
@section('content')
@include('modals.transporter_modal')
@include('modals.pending_qc_for_src_modal')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-supplier_rej_challan') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add Supplier Return Challan</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonsupplierRejChallan" class="stdform" method="post">
      @csrf
      
      
      @include('common_form_files.supplier_rejection')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="sup_rejection_button">{{ config('define.value.add') }}</button>
                            {{-- <button class="btn btn-primary"> Print</button></span> --}}
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


