@extends('layouts.app',['pageTitle' => 'Dispatch Plan'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-dispatch_plan') }}">Dispatch Plan</a> <span class="separator"></span></li>
    <li>Edit Dispatch Plan</li>
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
    <h4 class="widgettitle">Edit Dispatch Plan</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonDispatchPlanForm" class="stdform" method="post">
      @csrf
      <input type="hidden" name="id" id="id" value="{{ base64_decode($id) }}">
      
      
      @include('common_form_files.dispatch_plan')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                          <button type="submit" class="btn btn-primary" id="dispatch_plan_button">Update</button>
                            {{-- <button type="button" class="btn btn-primary">Dispatch Print</button>                           --}}
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


