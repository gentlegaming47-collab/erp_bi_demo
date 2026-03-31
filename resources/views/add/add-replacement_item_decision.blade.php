@extends('layouts.app',['pageTitle' => 'Replacement Item Decision'])
@section('header')
<style>
  #replacementItemTable_filter label{
    width: auto;
    white-space: nowrap;
    padding: 0;
  }

  #replacementItemTable_length label{
    width: 0;
    white-space: nowrap;
    float: none;
    text-align: unset;
    padding: 0;
  }
  </style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-replacement_item_decision') }}">Replacement Item Decision</a> <span class="separator"></span></li>

    <li>Add Replacement Item Decision</li>
</ul>
@endsection
@section('content')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-replacement_item_decision') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add Replacement Item Decision</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonReplacementItemDecisionForm" class="stdform" method="post">
      @csrf

      @include('common_form_files.replacement_item_decision')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="submitBtn">{{ config('define.value.add') }}</button>
                           
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



