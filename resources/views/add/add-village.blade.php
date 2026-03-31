@extends('layouts.app',['pageTitle' => 'Village'])



@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-village') }}">Village</a> <span class="separator"></span></li>

    <li>Add village</li>

</ul>

@endsection



@section('content')







@include('modals.state_modal')
@include('modals.taluka_modal')



<!-- End Modals -->



<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-village') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Village</h4>

    </div>

    <div class="widgetcontent">

        <form id="commonVillageForm" class="stdform" method="post">
                <input type="hidden" value="Y" name="IsAllState" id="IsAllState"/>
                <input type="hidden" value="Village" name="hidViewPage" id="hidViewPage"/>
            @csrf

                     @include('common_form_files.village')


                     <div class="row">
                        <div class="span-6">            
                            <div class="par control-group form-control">
                                <label class="control-label village_label"></label>
                                <div class="controls">
                                        <span class="formwrapper"> 
                                            <button type="submit" class="btn btn-primary">{{ config('define.value.add') }}</button>
                                </div>
                            </div>
                        </div>
                
                    </div>

        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection
