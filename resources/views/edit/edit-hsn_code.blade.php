@extends('layouts.app',['pageTitle' => 'HSN Code '])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-hsn_code') }}">HSN Code </a> <span class="separator"></span></li>

    <li>Edit HSN Code </li>

</ul>



@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-hsn_code') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit HSN Code </h4>

    </div>

    <div class="widgetcontent">

        <form id="commonHSNCodeForm" class="stdform" method="post">

            @csrf
            <input type="hidden" value="{{base64_decode($id)}}" name="id" id="id"/>

            @include('common_form_files.hsn_code')

                {{-- <p class="stdformbutton">

                    <button class="btn btn-primary">Update</button>

                </p> --}}
                <div class="row">
                    <div class="span-6">            
                        <div class="par control-group form-control">
                            <label class="control-label hsn_label"></label>
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

