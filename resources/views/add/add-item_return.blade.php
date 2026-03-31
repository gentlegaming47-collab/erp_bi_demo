@extends('layouts.app',['pageTitle' => 'Item Return Slip'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-item_return') }}">Item Return Slip</a> <span class="separator"></span></li>

    <li>Add Item Return Slip</li>

</ul>

@endsection



@section('content')

@include('modals.pending_itemIssue_modal')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-item_return') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Item Return Slip</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonItmeReturnForm" class="stdform" method="post">

            @csrf

            @include('common_form_files.item_return')


            <div class="row">
                <div class="span-6">            
                    <div class="par control-group form-control">
                        <label class="control-label"></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                    <button type="submit" class="btn btn-primary" id="item_retutn_button">{{ config('define.value.add') }}</button>
                                    {{-- <button class="btn btn-primary">Print</button>             --}}
                                </span>
                        </div>
                    </div>
                </div>
        
            </div>

        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection


