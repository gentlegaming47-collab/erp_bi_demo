@extends('layouts.app',['pageTitle' => 'Taluka'])

@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-taluka') }}">Taluka</a> <span class="separator"></span></li>

    <li>Add Taluka</li>

</ul>

@endsection

@section('content')

<!-- Modals -->

@include('modals.state_modal')
@include('modals.city_modal')

<!-- End Modals -->



<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
           <a href="{{ route('manage-taluka') }}" class="btn btn-inverse">Back</a>
        </div>
        <h4 class="widgettitle">Add Taluka</h4>
    </div>

    <div class="widgetcontent">
        <form id="commonTalukaForm" class="stdform" method="post">
                <input type="hidden" value="Y" name="IsAllState" id="IsAllState"/>
                <input type="hidden" value="Taluka" name="hidViewPage" id="hidViewPage"/>
            @csrf


            @include('common_form_files.taluka')

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

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection

