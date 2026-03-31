@extends('layouts.app',['pageTitle' => 'Location'])

@section('header')

<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-location') }}">Location</a> <span class="separator"></span></li>
    <li>Add Location</li>
</ul>

@endsection

@section('content')

<!-- Modals -->
@include('modals.taluka_modal')
@include('modals.country_modal')
@include('modals.village_modal')
@include('modals.state_modal')
@include('modals.city_modal')
<!-- End Modals -->

<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
           <a href="{{ route('manage-location') }}" class="btn btn-inverse">Back</a>
        </div>
        <h4 class="widgettitle">Add Location</h4>
    </div>

    <div class="widgetcontent">
        <form id="commonLocationForm" class="stdform" method="post" enctype="multipart/form-data">
            @csrf
                        {{-- <input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
                        <input type="hidden" value="Location" name="hidViewPage" id="hidViewPage"/> --}}

                        @include('common_form_files.location')


                {{-- <p class="stdformbutton">
                    <button class="btn btn-primary">{{ config('define.value.add') }}</button>
                </p> --}}

                <div class="row">

                    <div class="span-6">
            
                        <div class="par control-group form-control">
                            <label class="control-label"></label>
                            <div class="controls">
                                    <span class="formwrapper"> 
                                        <button id="location-btn" class="btn btn-primary">{{ config('define.value.add') }}</button>
                            </div>
                        </div>
                    </div>
            
                </div>
        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection
