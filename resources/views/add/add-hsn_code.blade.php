@extends('layouts.app',['pageTitle' => 'HSN Code'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-hsn_code') }}">HSN Code</a> <span class="separator"></span></li>
    <li>Add HSN Code</li>
</ul>
@endsection
@section('content')
<!-- Modals -->
{{-- @include('modals.country_modal') --}}
<!-- End Modals -->
<div class="widgetbox">
    <div class="headtitle">
            <div class="btn-group">
               <a href="{{ route('manage-hsn_code') }}" class="btn btn-inverse">Back</a>
            </div>
        <h4 class="widgettitle">Add HSN Code</h4>
    </div>

    <div class="widgetcontent">

        <form id="commonHSNCodeForm" class="stdform" method="post">
            @csrf
                
            @include('common_form_files.hsn_code')
            
                {{-- <p class="stdformbutton">
                    <button class="btn btn-primary">{{ config('define.value.add') }}</button>
                </p> --}}

                <div class="row">
                    <div class="span-6">            
                        <div class="par control-group form-control">
                            <label class="control-label hsn_label"></label>
                            <div class="controls">
                                    <span class="formwrapper"> 
                                        <button type="submit" id="hsn-btn" class="btn btn-primary">{{ config('define.value.add') }}</button>
                            </div>
                        </div>
                    </div>
            
                </div>
                
        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection