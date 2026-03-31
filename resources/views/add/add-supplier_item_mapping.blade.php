@extends('layouts.app',['pageTitle' => 'Add Supplier Item Mapping'])



@section('header')


@section('header')

<style>
    #supplierItemMappingTable_filter label{
      width: auto;
      white-space: nowrap;
      padding: 0;
    }
  
    #supplierItemMappingTable_length label{
      width: 0;
      white-space: nowrap;
      float: none;
      text-align: unset;
      padding: 0;
    }
</style>


<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-supplier_item_mapping') }}">Supplier Item Mapping</a> <span class="separator"></span></li>

    <li>Add Supplier Item Mapping</li>

</ul>

@endsection



@section('content')

<div class="widgetbox">

    <div class="headtitle">

        <div class="btn-group">

           <a href="{{ route('manage-supplier_item_mapping') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Supplier Item Mapping</h4>
        
    </div>

    <div class="widgetcontent">

        <form id="SupplierItemMappingForm" class="stdform" method="post">

            @csrf
            @include('common_form_files.supplier_item_mapping')
           
            <div class="row">
                <div class="span-6">            
                    <div class="par control-group form-control">
                        <label class="control-label"></label>
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








