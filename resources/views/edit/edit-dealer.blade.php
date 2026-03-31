@extends('layouts.app',['pageTitle' => 'Dealer'])

@section('header')

<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li><a href="{{ route('manage-dealer') }}">Dealer</a> <span class="separator"></span></li>
    <li>Edit Dealer</li>
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
        <h4 class="widgettitle">Edit Dealer</h4>
    </div>
    	 <div class="widgetcontent">
        <form id="commonDealerForm" class="stdform" method="post">
            @csrf
            <input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
            <input type="hidden" value="Customer" name="hidViewPage" id="hidViewPage"/>    
            <input type="hidden" value="{{base64_decode($id)}}" name="id" id="id"/>

                    @include('common_form_files.dealer')
                      <div class="row">
                        <div class="span-6">            
                            <div class="par control-group form-control">
                                <label class="control-label"></label>
                                    <div class="controls">
                                        <span class="formwrapper"> 
                                            <button class="btn btn-primary"  id="dealer-btn">Update</button>
                                        </span>
                                    </div>
                            </div>
                        </div>
                    </div>
         </form>
      </div><!--widgetcontent-->
@endsection
