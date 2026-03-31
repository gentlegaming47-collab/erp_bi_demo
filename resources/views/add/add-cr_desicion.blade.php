@extends('layouts.app',['pageTitle' => 'CR Decision'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-cr_decision') }}">CR Decision</a> <span class="separator"></span></li>
    <li>Add CR Decision</li>
</ul>
@endsection
@section('content')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-cr_decision') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add CR Decision</h4>
  </div>
  <div class="widgetcontent">
    <form id="CRDecisionForm" class="stdform" method="post">
      @csrf

      @include('common_form_files.cr_desicion')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="truck_wise_item">{{ config('define.value.add') }}</button>
                          
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


