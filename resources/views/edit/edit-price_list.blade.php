@extends('layouts.app',['pageTitle' => 'Edit Price List'])


@section('header')


<style>
        #pricelisttable_filter label{
          width: auto;
          white-space: nowrap;
          padding: 0;
        }
      
        #pricelisttable_length label{
          width: 0;
          white-space: nowrap;
          float: none;
          text-align: unset;
          padding: 0;
        }
</style>

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-price_list') }}">Price List</a> <span class="separator"></span></li>

    {{-- <li>Add Raw Material Mapping Group</li> --}}
    <li>Edit Price List</li>

</ul>

@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-price_list') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Edit Price List</h4>
        {{-- <h4 class="widgettitle">Add Item Material Mapping Group</h4> --}}

    </div>

    <div class="widgetcontent">

        <form id="addPriceListForm" class="stdform" method="post">

            @csrf
            <input type="hidden" name="id" id="id" value="{{base64_decode($id)}}" >
                       @include('common_form_files.price_list')





                       <div class="row">

                        <div class="span-6">
                
                            <div class="par control-group form-control">
                                <label class="control-label"></label>
                                <div class="controls">
                                        <span class="formwrapper"> 
                                            <button class="btn btn-primary checkUser">Update</button>
                                </div>
                            </div>
                        </div>
                
                    </div>

        </form>

    </div><!--widgetcontent-->

</div><!--widget-->

@endsection



