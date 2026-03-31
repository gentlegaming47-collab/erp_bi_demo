@extends('layouts.app',['pageTitle' => 'QC Approval'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-qc_approval') }}">QC Approval</a> <span class="separator"></span></li>
    <li>Add QC Approval</li>
</ul>
@endsection
@section('content')

@include('modals.pending_grn_for_qc_modal')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-qc_approval') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add QC Approval</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonQCForm" class="stdform" method="post">
      @csrf
      
      
      @include('common_form_files.qc_approval')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="qcButton">{{ config('define.value.add') }}</button>
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


