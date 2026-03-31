@extends('layouts.app',['pageTitle' => 'Transporter'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-transporter') }}">Transporter</a> <span class="separator"></span></li>

    <li>Edit transporter</li>

</ul>



@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-transporter') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit transporter</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonTransporterForm" class="stdform" method="post">

            @csrf

            <input type="hidden" value="{{base64_decode($id)}}" name="id" id="id"/>
                
            @include('common_form_files.transporter')



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


