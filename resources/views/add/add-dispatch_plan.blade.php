@extends('layouts.app',['pageTitle' => 'Dispatch Plan'])
@section('header')
<style>
  #DipatchPlanTable_filter label{
    width: auto;
    white-space: nowrap;
    padding: 0;
  }

  #DipatchPlanTable_length label{
    width: 0;
    white-space: nowrap;
    float: none;
    text-align: unset;
    padding: 0;
  }
  </style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-dispatch_plan') }}">Dispatch Plan</a> <span class="separator"></span></li>
    <li>Add Dispatch Plan</li>
</ul>
@endsection
@section('content')

@include('modals.pending_so_for_dispatch_modal')
@include('modals.so_fitting_for_dispatch_modal')
@include('modals.so_secondary_for_dispatch_modal')
@include('modals.so_assembly_for_dp_modal')

<div class="widgetbox">
    <div id="show-progress"></div>
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-dispatch_plan') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add Dispatch Plan</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonDispatchPlanForm" class="stdform" method="post">
      @csrf
      
      
      @include('common_form_files.dispatch_plan')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="dispatch_plan_button">{{ config('define.value.add') }}</button>
                             {{-- <button type="button" class="btn btn-primary">Dispatch Print</button> --}}
                        </span>
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


