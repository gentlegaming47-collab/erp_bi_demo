@extends('layouts.app',['pageTitle' => 'Edit Purchase Order'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-purchase_order') }}">Edit Purchase Order</a> <span class="separator"></span></li>

    {{-- <li>Add Raw Material Mapping Group</li> --}}
    <li>Edit Purchase Order</li>

</ul>

@endsection



@section('content')

@include('modals.pendingPR_modal')

<div class="widgetbox">
    <div id="show-progress"></div>
    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-purchase_order') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit Purchase Order</h4>
        {{-- <h4 class="widgettitle">Add Item Material Mapping Group</h4> --}}

    </div>

    <div class="widgetcontent">

        <form id="PurchaseOrderForm" class="stdform" method="post">

            @csrf
            <input type="hidden" name="id" id="id" value="{{base64_decode($id)}}">
            @include('common_form_files.purchase_order')

            <div class="row">

                <div class="span-6">
        
                    <div class="par control-group form-control">
                        <label class="control-label"></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                    <button class="btn btn-primary checkUser" id="purchase_button">Update</button>
                        </div>
                    </div>
                </div>
        
            </div>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->
@endsection










