@extends('layouts.app',['pageTitle' => 'Material Request'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-material_request') }}">Material Request</a> <span class="separator"></span></li>
    <li>Add Material Request</li>
</ul>
@endsection
@section('content')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-material_request') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add Material Request</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonMaterialRequestForm" class="stdform" method="post">
      @csrf
      
      
      @include('common_form_files.material_request')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="materialRequestButton">{{ config('define.value.add') }}</button>
                            {{-- <button type="submit" class="btn btn-primary">Print</button> --}}
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


