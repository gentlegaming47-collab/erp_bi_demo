@extends('layouts.app',['pageTitle' => 'District'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-district') }}">District</a> <span class="separator"></span></li>

    <li>Edit District</li>

</ul>



@endsection



@section('content')



<!-- Modals -->



@include('modals.state_modal')



<!-- End Modals -->



<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-district') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit District</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonDistrictForm" class="stdform" method="post">
            <input type="hidden" value="Y" name="IsAllState" id="IsAllState"/>
                        <input type="hidden" value="EditDistrict" name="hidViewPage" id="hidViewPage"/>
            @csrf

            <input type="hidden" value="{{base64_decode($id)}}" name="id" id="id"/>
                
            @include('common_form_files.district')


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



