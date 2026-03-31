@extends('layouts.app',['pageTitle' => 'Truck Wise Item'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-truck_wise_item') }}">Truck Wise Item</a> <span class="separator"></span></li>
    <li>Add Truck Wise Item</li>
</ul>
@endsection
@section('content')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-truck_wise_item') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add Truck Wise Item</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonDeliveryChallanForm" class="stdform" method="post">
      @csrf

      @include('common_form_files.truck_wise_item')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="truck_wise_item">{{ config('define.value.add') }}</button>
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


