@extends('layouts.app',['pageTitle' => 'Item Stock Transfer'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-item_stock_transfer') }}">Item Stock Transfer</a> <span class="separator"></span></li>
    <li>Edit Item Stock Transfer</li>
</ul>
@endsection
@section('content')
<div class="widgetbox">
  <div id="show-progress"></div>
  <div class="headtitle">
    <div class="btn-group"> <a href="{{ route('manage-item_stock_transfer') }}" class="btn btn-inverse">Back</a> </div>
    <h4 class="widgettitle">Edit Item Stock Transfer</h4>
  </div>
  <div class="widgetcontent">
    <form id="ItemStockTransfer" class="stdform" method="post">
      @csrf
      

      <input type="hidden" name="id" id="id" value="{{ base64_decode($id) }}">
      
      @include('common_form_files.item_stock_transfer')


      {{-- <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                <div class="controls">
                        <span class="formwrappers">
                            <button type="submit" class="btn btn-primary" id="ist_btn">Update</button>
                </div>
            </div>
        </div>

    </div>
      --}}
    </form>
  </div>
  <!--widgetcontent-->

</div>
<!--widget-->
@endsection


