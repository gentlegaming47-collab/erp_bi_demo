@extends('layouts.app',['pageTitle' => 'Location'])



@section('header')

<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-district') }}">Location</a> <span class="separator"></span></li>
    <li>Edit Location</li>
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
        <h4 class="widgettitle">Edit Location</h4>
    </div>
    <div class="widgetcontent">
        <form id="commonLocationForm" class="stdform" method="post">
            @csrf
            {{-- <input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
            <input type="hidden" value="Location" name="hidViewPage" id="hidViewPage"/> --}}

                <input type="hidden" value="{{base64_decode($id)}}" name="id" id="location_id"/>

                @include('common_form_files.location')
       

            <div class="row">

                <div class="span-6">
        
                    <div class="par control-group form-control">
                        <label class="control-label"></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                    <button class="btn btn-primary checkUser">Update</button>
                        </div>
                    </div>
                </div>
        
            </div>
        </form>
    </div><!--widgetcontent-->

</div><!--widget-->

@endsection


