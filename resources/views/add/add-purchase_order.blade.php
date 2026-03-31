@extends('layouts.app',['pageTitle' => 'Add Purchase Order'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-purchase_order') }}">Purchase Order</a> <span class="separator"></span></li>

    {{-- <li>Add Raw Material Mapping Group</li> --}}
    <li>Add Purchase Order</li>

</ul>

@endsection



@section('content')

@include('modals.pendingPR_modal')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-purchase_order') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Purchase Order</h4>
        {{-- <h4 class="widgettitle">Add Item Material Mapping Group</h4> --}}

    </div>

    <div class="widgetcontent">

        <form id="PurchaseOrderForm" class="stdform" method="post">

            @csrf
            @include('common_form_files.purchase_order')
           
            <div class="row">
                <div class="span-6">            
                    <div class="par control-group form-control">
                        <label class="control-label"></label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                    <button type="submit" class="btn btn-primary" id="purchase_button">{{ config('define.value.add') }}</button>
                        </div>
                    </div>
                </div>
        
            </div>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->
@endsection










