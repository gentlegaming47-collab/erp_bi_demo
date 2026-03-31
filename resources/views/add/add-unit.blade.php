@extends('layouts.app',['pageTitle' => 'Unit'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-unit') }}">Unit</a> <span class="separator"></span></li>

    <li>Add unit</li>

</ul>

@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-unit') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Unit</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonUnitForm" class="stdform" method="post">

            @csrf

            @include('common_form_files.unit')


            <div class="row">

                <div class="span-6">
        
                    <div class="par control-group form-control">
                        <label class="control-label"></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                    <button class="btn btn-primary">{{ config('define.value.add') }}</button>
                        </div>
                    </div>
                </div>
        
            </div>
        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection

