@extends('layouts.app',['pageTitle' => 'Sales Return'])

@section('header')

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-sales_return') }}">Sales Return</a> <span class="separator"></span></li>

    <li>Edit Sales Return</li>

</ul>

@endsection

@section('content')

<div class="widgetbox">

    <div id="show-progress"></div>
    <div class="headtitle">
        <div class="btn-group">

            <a href="{{ route('manage-sales_return') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit Sales Return</h4>

    </div>




    <div class="widgetcontent">
        <form id="salesreturnform" class="stdform" method="post">
            @csrf
            {{-- first row start --}}
            <input type="hidden"  name="id" id="id" value="{{base64_decode($id)}}"/>

          @include('common_form_files.sales_return')


          <div class="row">
            <div class="span-6">            
                <div class="par control-group form-control">
                    <label class="control-label"></label>
                    <div class="controls">
                            <span class="formwrapper"> 
                                <button type="submit" class="btn btn-primary" id="sales_return_button">Update</button>
                                
                            </span>
                    </div>
                </div>
            </div>
    
          </div>
    </form>

    </div><!--widgetcontent-->




@endsection
