@extends('layouts.app',['pageTitle' => 'Customer Replacement Entry'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('add-customer_replacement_entry') }}">Customer Replacement Entry</a> <span class="separator"></span></li>

    <li>Edit Customer Replacement Entry</li>

</ul>



@endsection



@section('content')
@include('modals.customer_search_modal')

<div class="widgetbox">
    <div id="show-progress"></div>

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-customer_replacement_entry') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit Customer Replacement Entry</h4>

    </div>

    <div class="widgetcontent">

        <form id="customerReplacementEntryForm" class="stdform" method="post">

            @csrf

            <input type="hidden" value="{{base64_decode($id)}}" name="id" id="id"/>

            
            @include('common_form_files.customer_replacement_entry')

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

