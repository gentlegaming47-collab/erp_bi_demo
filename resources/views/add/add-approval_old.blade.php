@extends('layouts.app',['pageTitle' => 'Approval'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-approval') }}">Approval</a> <span class="separator"></span></li>
    <li>Add Approval</li>
</ul>
@endsection
@section('content')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-approval') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">SM Approval</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonApprovalRequestForm" class="stdform" method="post">
      @csrf
      
      
      @include('common_form_files.approval')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="approvalRequestButton">{{ config('define.value.add') }}</button>
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


