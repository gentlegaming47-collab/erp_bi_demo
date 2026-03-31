@extends('layouts.app',['pageTitle' => 'Item group'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-item_group') }}">Item Group</a> <span class="separator"></span></li>

    <li>Edit Item group</li>

</ul>



@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-item_group') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit item group</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonItmeGroupForm" class="stdform" method="post">

            @csrf

            <input type="hidden" value="{{base64_decode($id)}}" name="id" id="id"/>
            @include('common_form_files.item-group')

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

