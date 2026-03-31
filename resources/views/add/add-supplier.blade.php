@extends('layouts.app',['pageTitle' => 'Supplier'])

@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-supplier') }}">Supplier</a> <span class="separator"></span></li>

    <li>Add Supplier</li>

</ul>

@endsection

@section('content')

<!-- Modals -->
@include('modals.taluka_modal')
@include('modals.country_modal')
@include('modals.village_modal')
@include('modals.state_modal')
@include('modals.city_modal')
@include('modals.agreement_modal')

<!-- End Modals -->
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
           <a href="{{ route('manage-supplier') }}" class="btn btn-inverse">Back</a>
        </div>
        <h4 class="widgettitle">Add Supplier</h4>
    </div>
    <div class="widgetcontent">
        <form id="commonSupplierForm" class="stdform" method="post">
            @csrf
            <input type="hidden" value="N" name="IsAllState" data-page="add" id="IsAllState"/>
            <input type="hidden" value="Supplier" name="hidViewPage" id="hidViewPage"/>
                @include('common_form_files.supplier')

             

                <div class="row">
                    <div class="span-6">            
                        <div class="par control-group form-control">
                            <label class="control-label"></label>
                            <div class="controls">
                                    <span class="formwrapper"> 
                                        <button type="submit" id="supplier_btn" class="btn btn-primary">{{ config('define.value.add') }}</button>
                            </div>
                        </div>
                    </div>
            
                </div>
        </form>

    </div><!--widgetcontent-->

</div><!--widget-->



@endsection
