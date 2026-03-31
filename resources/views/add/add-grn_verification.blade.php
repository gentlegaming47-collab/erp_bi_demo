

@extends('layouts.app',['pageTitle' => 'GRN Verification'])

@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-grn_verification') }}">GRN Verification</a> <span class="separator"></span></li>

    <li>Add GRN Verification</li>

</ul>

@endsection




@section('content')


<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-grn_verification') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add GRN Verification</h4>

    </div>

    <div class="widgetcontent">

        <form id="GrnVerification" class="stdform" method="post">

            @csrf
            @include('common_form_files.grn_verification')


            <div class="row">
                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label"></label>
                        <div class="controls">
                                <span class="formwrapper">
                                    <button type="submit" class="btn btn-primary">{{ config('define.value.add') }}</button>
                                </span>
                        </div>
                    </div>
                </div>

            </div>

        </form>
    </div><!--widgetcontent-->
</div><!--widget-->
@endsection