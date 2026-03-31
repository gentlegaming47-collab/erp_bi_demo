@extends('layouts.app',['pageTitle' => 'Material Request'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-material_request') }}">Material Request</a> <span class="separator"></span></li>
    <li>Edit Material Request</li>
</ul>
@endsection
@section('content')

<div class="widgetbox">
  <div id="show-progress"></div>
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-material_request') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Edit Material Request</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonMaterialRequestForm" class="stdform" method="post">
      @csrf
      

      <input type="hidden" name="id" id="id" value="{{ base64_decode($id) }}">
      
      @include('common_form_files.material_request')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="materialRequestButton">Update</button>
                            {{-- <button type="submit" class="btn btn-primary">Print</button>                           --}}
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


