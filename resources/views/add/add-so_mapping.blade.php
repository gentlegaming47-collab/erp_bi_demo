@extends('layouts.app',['pageTitle' => 'Customer Replacement SO Mapping'])



@section('header')



<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-so_mapping') }}">Customer Replacement SO Mapping</a> <span class="separator"></span></li>

    <li>Add Customer Replacement SO Mapping </li>

</ul>

@endsection



@section('content')

@include('modals.so_mapping')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-so_mapping') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Customer Replacement SO Mapping</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonSOmapping" class="stdform" method="post">
            @csrf
          
      
            @include('common_form_files.so_mapping')
          
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

