@php
    $getpagename = Request::path();

    $routeType = $getpagename == 'add-grn_location' ?  'grn_location' : 'grn_details';

    $pageName = $routeType == 'grn_details' ?  'Goods Receipt Note (GRN)' : 'GRN (Location)';

@endphp


@extends('layouts.app',['pageTitle' => $pageName])



@section('header')


<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-'.$routeType) }}">{{$pageName}}</a> <span class="separator"></span></li>

    <li>Add {{$pageName}}</li>

</ul>

@endsection




@section('content')

@include('modals.transporter_modal')
@include('modals.pendingPurchase_model')
@include('modals.pending_dc_grn_model')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-'.$routeType) }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add {{$pageName}}</h4>

    </div>

    <div class="widgetcontent">

        <form id="GrnDetailsForm" class="stdform" method="post">

            @csrf
            @include('common_form_files.grn_details')


            <div class="row">
                <div class="span-6">
                    <div class="par control-group form-control">
                        <label class="control-label"></label>
                        <div class="controls">
                                <span class="formwrapper">
                                    <button type="submit" id="grnButton" class="btn btn-primary">{{ config('define.value.add') }}</button>
                                    {{-- <button class="btn btn-primary"> Print</button> --}}

                                    {{-- <button type="button" class="btn btn-primary" id="grnprintButton">Preview</button> --}}
                                </span>
                        </div>
                    </div>
                </div>

            </div>

        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<script>
// jQuery(document).ready(function() {
	
// 	jQuery('#pendingPoModal').on('hidden.bs.modal', function (e) {
// 	  jQuery('.modal-backdrop').remove();
// 	  jQuery("#pendingPoModal").removeClass("in");
// 	  jQuery('body').removeClass('modal-open');
// 	  jQuery("#pendingPoModal").modal('hide');
// 	});
	
	
// });
</script>
@endsection