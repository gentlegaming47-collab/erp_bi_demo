@extends('layouts.app',['pageTitle' => 'Purchase Requisition'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-purchase_requisition') }}">Purchase Requisition</a> <span class="separator"></span></li>
    <li>Edit Purchase Requisition</li>
</ul>
@endsection
@section('content')

@include('modals.pending_material_request')


<div class="widgetbox">
  <div id="show-progress"></div>
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-purchase_requisition') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Edit Purchase Requisition</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonPRForm" class="stdform" method="post">
      @csrf
      

      <input type="hidden" name="id" id="id" value="{{ base64_decode($id) }}">
      
      @include('common_form_files.purchase_requisition')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                    <div class="controls">
                        <span class="formwrappers">
                        <button type="submit" class="btn btn-primary" id="PrButton">Update</button>
            
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


