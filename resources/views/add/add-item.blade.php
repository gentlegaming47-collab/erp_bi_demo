@extends('layouts.app',['pageTitle' => 'Item'])
@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-item') }}">Item</a> <span class="separator"></span></li>
    <li>Add item</li>
</ul>

@endsection
@section('content')
@include('modals.item_group_modal')
@include('modals.unit_modal')
@include('modals.hsn_model')
@include('modals.itemdetail_model')
<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-item') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Item</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonItemForm" class="stdform" method="post">        
     
            @csrf

            @include('common_form_files.item')



            <div class="row">
                <div class="span-6">            
                    <div class="par control-group form-control">
                        <label class="control-label"></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                    <button type="submit" id="item_btn" class="btn btn-primary">{{ config('define.value.add') }}</button>
                        </div>
                    </div>
                </div>
        
            </div>

        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection
