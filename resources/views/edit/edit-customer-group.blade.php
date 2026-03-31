@extends('layouts.app',['pageTitle' => 'Customer Group'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('add-customer_group') }}">Customer Group</a> <span class="separator"></span></li>

    <li>Edit customer group</li>

</ul>



@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-customer_group') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit customer-group</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonCustomerGroupForm" class="stdform" method="post">

            @csrf

            <input type="hidden" value="{{base64_decode($id)}}" name="id"/>
            
            @include('common_form_files.customer_group')

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

