@extends('layouts.app',['pageTitle' => 'Sales Return'])

@section('header')

<style>
    #soPartTable_filter label{
      width: auto;
      white-space: nowrap;
      padding: 0;
    }
  
    #soPartTable_length label{
      width: 0;
      white-space: nowrap;
      float: none;
      text-align: unset;
      padding: 0;
    }
</style>

<ul class="breadcrumbs">

    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>

    <li><a href="{{ route('manage-sales_return') }}">Sales Return</a> <span class="separator"></span></li>

    <li>Add Sales Return</li>

</ul>

@endsection

@section('content')


<div class="widgetbox">

    <div class="headtitle">
    <div id="show-progress"></div>
        <div class="btn-group">

            <a href="{{ route('manage-sales_return') }}" class="btn btn-inverse">Back</a>

        </div>

        <h4 class="widgettitle">Add Sales Return</h4>

    </div>




        <div class="widgetcontent">
                <form id="salesreturnform" class="stdform" method="post">
                        @csrf
                        {{-- first row start --}}
                        {{-- <input type="hidden" value="N" name="IsAllState" id="IsAllState"/>
                        <input type="hidden" value="salesOrder" name="hidViewPage" id="hidViewPage"/>
                        <input type="hidden" value="addPage" name="itemPage" id="itemHiddenPage"/> --}}
                    
                       
                      @include('common_form_files.sales_return')

                      <div class="row">
                        <div class="span-6">            
                            <div class="par control-group form-control">
                                <label class="control-label"></label>
                                <div class="controls">
                                        <span class="formwrapper"> 
                                            <button type="submit" class="btn btn-primary" id="sales_return_button">{{ config('define.value.add') }}</button>
                                            {{-- <button class="btn btn-primary"> Print</button></span> --}}
                                        </span>
                                </div>
                            </div>
                        </div>
                
                      </div>
                </form>



        </div><!--widgetcontent-->




@endsection



@section('scripts')

{{-- <script>
jQuery(document).ready(function(){
    soType();
    getSoStates();
});
</script>    --}}

@endsection
