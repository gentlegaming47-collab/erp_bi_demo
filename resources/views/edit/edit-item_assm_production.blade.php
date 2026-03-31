

@extends('layouts.app',['pageTitle' => 'Item Production (Assembly)'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-item_assm_production') }}"> Item Production (Assembly)</a> <span class="separator"></span></li>
    <li>Edit Item Production (Assembly)</li>
</ul>
@endsection
@section('content')

<div class="widgetbox">
  <div id="show-progress"></div>
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-item_assm_production') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Edit Item Assmbly Production</h4>
  </div>
  <div class="widgetcontent">
    <form id="commonItemAssmProductionForm" class="stdform" method="post">
      @csrf
      

      <input type="hidden" name="id" id="id" value="{{ base64_decode($id) }}">
      <input type="hidden" name="org_assembly_qty" id="org_assembly_qty">
      
      @include('common_form_files.item_assm_production')


      <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="item_assm_production_button">Update</button>
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


