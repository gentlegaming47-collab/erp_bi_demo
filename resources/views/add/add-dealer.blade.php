@extends('layouts.app',['pageTitle' => 'Dealer'])

@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-dealer') }}">Dealer</a> <span class="separator"></span></li>

    <li>Add Dealer</li>

</ul>

@endsection

@section('content')

@include('modals.taluka_modal')
@include('modals.country_modal')
@include('modals.village_modal')
@include('modals.state_modal')
@include('modals.city_modal')
@include('modals.contact_modal')
@include('modals.dealer_agreement_modal')




<div class="widgetbox">
                    <div class="headtitle">
                        <div class="btn-group">
                            <a href="{{ route('manage-dealer') }}" class="btn btn-inverse">Back</a>
                        </div>
                        <h4 class="widgettitle">Add Dealer</h4>
                    </div>

                    <div class="widgetcontent">
                        <form id="commonDealerForm" class="stdform" method="post">
                            @csrf
                            <input type="hidden" value="N" name="IsAllState" data-page="add" id="IsAllState"/>
                            <input type="hidden" value="Customer" name="hidViewPage" id="hidViewPage"/>    
                            @include('common_form_files.dealer')
                                <div class="row">
                                        <div class="span-6">            
                                            <div class="par control-group form-control">
                                                <label class="control-label"></label>
                                                <div class="controls">
                                                        <span class="formwrapper"> 
                                                            <button id="dealer-btn" type="submit" class="btn btn-primary">{{ config('define.value.add') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                        </form>
                </div>
</div><!--widgetcontent-->

@endsection


