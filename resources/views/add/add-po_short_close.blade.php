@extends('layouts.app',['pageTitle' => 'PO Short Close'])



@section('header')

<style>
  #pendingPOTable_filter label{
    width: auto;
    white-space: nowrap;
    padding: 0;
  }

  #pendingPOTable_length label{
    width: 0;
    white-space: nowrap;
    float: none;
    text-align: unset;
    padding: 0;
  }
  </style>

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-po_short_close') }}">PO Short Close</a> <span class="separator"></span></li>

    <li>Add PO Short Close </li>

</ul>

@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-po_short_close') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add PO Short Close</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonPOShortClose" class="stdform" method="post">
            @csrf
          
      
            @include('common_form_files.po_short_close')
          
            <div class="row">
              <div class="span-6">            
                  <div class="par control-group form-control">
                      <label class="control-label"></label>
                      <div class="controls">
                              <span class="formwrapper"> 
                                  <button type="submit" class="btn btn-primary">{{ config('define.value.add') }}</button>
                      </div>
                  </div>
              </div>
      
            </div>

          </form>
        </div>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection


