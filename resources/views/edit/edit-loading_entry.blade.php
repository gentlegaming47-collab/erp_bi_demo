@extends('layouts.app',['pageTitle' => 'Loading Entry'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-loading_entry') }}">Loading Entry</a> <span class="separator"></span></li>
    <li>Edit Loading Entry</li>
</ul>
@endsection
@section('content')
@include('modals.pending_dispatch_modal')
@include('modals.dp_secondary_for_loading_modal')

<div class="widgetbox">
  <div id="show-progress"></div>
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-loading_entry') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Edit Loading Entry</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonLoadingEntryForm" class="stdform" method="post">
      @csrf
      <input type="hidden" name="id" id="id" value="{{ base64_decode($id) }}">
      
      @include('common_form_files.loading_entry')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="loading_button">Update</button>
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


