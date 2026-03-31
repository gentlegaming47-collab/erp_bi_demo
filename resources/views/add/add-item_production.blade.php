@extends('layouts.app',['pageTitle' => 'Item Production'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-item_production') }}">Item Production</a> <span class="separator"></span></li>
    <li>Add Item Production</li>
</ul>
@endsection
@section('content')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-item_production') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add Item Production</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonItemProductionForm" class="stdform" method="post">
      @csrf
            
      @include('common_form_files.item_production')

      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="item_production_button">{{ config('define.value.add') }}</button>
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


