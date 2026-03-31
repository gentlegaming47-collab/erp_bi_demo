@extends('layouts.app',['pageTitle' => 'Customer'])

@section('header')

<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-customer') }}">Customer</a> <span class="separator"></span></li>
    <li>Edit Customer</li>
</ul>

@endsection
@section('content')

{{-- @include('modals.customerGroup_modal') --}}
@include('modals.taluka_modal')
@include('modals.country_modal')
@include('modals.village_modal')
@include('modals.state_modal')
@include('modals.city_modal')

<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="{{ route('manage-customer') }}" class="btn btn-inverse">Back</a>
        </div>
        <h4 class="widgettitle">Edit Customer</h4>
    </div>
    	 <div class="widgetcontent">
        <form id="commonCustomerForm" class="stdform" method="post">
            @csrf
            <input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
<input type="hidden" value="Customer" name="hidViewPage" id="hidViewPage"/>    

            <input type="hidden" value="{{base64_decode($id)}}" name="id" id="id"/>

                    @include('common_form_files.customer')
                   <div class="stdformbutton">

                    <button type="submit" class="btn btn-primary">Update</button>

                </div>


         </form>
      </div><!--widgetcontent-->
@endsection
