@extends('layouts.app',['pageTitle' => 'Supplier Return Challan'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-supplier_rej_challan') }}">Supplier Return Challan</a> <span class="separator"></span></li>
    <li>Edit Supplier Return Challan</li>
</ul>
@endsection
@section('content')
@include('modals.transporter_modal')
@include('modals.pending_qc_for_src_modal')

<div class="widgetbox">
  <div id="show-progress"></div>
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-supplier_rej_challan') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Edit Supplier Return Challan</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonsupplierRejChallan" class="stdform" method="post">
      @csrf


      <input type="hidden" name="id" id="id" value="{{base64_decode($id)}}">
      
      @include('common_form_files.supplier_rejection')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="sup_rejection_button">Update</button>
                            {{-- <button class="btn btn-primary"> Print</button> --}}
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


