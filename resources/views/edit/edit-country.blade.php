@extends('layouts.app',['pageTitle' => 'Country'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-country') }}">Country</a> <span class="separator"></span></li>

    <li>Edit country</li>

</ul>



@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-country') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit country</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonCountryForm" class="stdform" method="post">

            @csrf
            
            <input type="hidden" value="{{base64_decode($id)}}" name="id"/>
               
            @include('common_form_files.country')


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
