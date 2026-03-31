@extends('layouts.app',['pageTitle' => 'Item Issue Slip'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-item_issue') }}">Item Issue Slip</a> <span class="separator"></span></li>
    <li>Add Item Issue Slip</li>
</ul>
@endsection
@section('content')

<div class="widgetbox">
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-item_issue') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Add Item Issue Slip</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonItemIssueForm" class="stdform" method="post">
      @csrf
      
      
      @include('common_form_files.item_issue')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="item_issue_button">{{ config('define.value.add') }}</button>
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


