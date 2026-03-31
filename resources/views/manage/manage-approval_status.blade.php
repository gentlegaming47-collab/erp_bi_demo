<?php 
$pageType = Request::path();
// dd($pageType);
                $getPageName = $pageType == "sm_approval" ? "SM Approval" : ($pageType == "state_coordinator" ? "State Coordinator" : ($pageType == "zsm_approval" ? "ZSM Approval" : ($pageType == "md_approval" ? "MD Approval" : ($pageType=="manage-approval_status"?"Approval Status":"")))); 
                $getFristPageName = $pageType == "sm_approval" ? "sm_approval" : ($pageType == "state_coordinator" ? "state_coordinator_approval" : ($pageType == "zsm_approval" ? "zsm_approval" : ($pageType == "md_approval" ? "md_approval" : "")));      
?>
@extends('layouts.app',['pageTitle' => $getPageName])

@section('header')


<style>
        #approvalTable_filter label{
          width: auto;
          white-space: nowrap;
          padding: 0;
        }
      
        #approvalTable_length label{
          width: 0;
          white-space: nowrap;
          float: none;
          text-align: unset;
          padding: 0;
        }
        #approvalDataTable_filter label{
          width: auto;
          white-space: nowrap;
          padding: 0;
        }
      
        #approvalDataTable_length label{
          width: 0;
          white-space: nowrap;
          float: none;
          text-align: unset;
          padding: 0;
        }
    </style>

<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>{{$getPageName}}</li>
</ul>
@endsection


@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            {{-- <div class="btn-group"> <a href="{{ route('manage-'.$getFristPageName.'_report') }}" class="btn btn-inverse">Back</a> </div> --}}
            
          
        </div>

      
        <h4 class="widgettitle">Approval Status</h4>
        
    </div>
    <div class="widgetcontent">
        <form id="common_approval_status" class="stdform" method="get">
            <input type="hidden" name="pagename" id="pagename" value='approval_status'>
        @csrf

    {{-- <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
            <label class="control-label" for="approval_status">Type <sup class="astric">*</sup></label>
            <div class="controls"> <span class="formwrapper">
                <select id="myDropdownd" name="options" class="chzn-select">
                    <option value="">Select Type</option>
                    <option value="Dealer">Dealer</option>
                    <option value="Suppliers">Suppliers</option>
                    <option value="Transporter">Transporter</option>
                </select>
                </span> </div>
            </div>
        </div>
    </div> --}}
   
   
    {{-- <div class="widgetbox-inverse">

        <div class="headtitle">

            <h4 class="widgettitle"> Approval Status List</h4> --}}
            {{-- <h4 class="widgettitle">Raw Material Mapping</h4> --}}

        {{-- </div> --}}

    {{-- <div class="widgetcontent overflow-scroll"> --}}
        

        
        <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
        <thead>
                <tr class="main-header">
                    <th><input type="checkbox" name="checkall-sm" class="simple-check" id="checkall-sm"/></th>                 
                    <th>Type</th>
                    <th>Change</th>
                    <th>Name</th>
                    <th>state</th>
                </tr>
            </thead>
            <tbody>            
                <tr> <td colspan="4" >No record found! </td> </tr>
            </tbody>
      
       
       
        </table>

        <div class="row">
            <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label"></label>
                    <div class="controls">
                        <span class="formwrapper">
                            <button type="submit" class="btn btn-primary" id="approvalButton">{{ config('define.value.add') }}</button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="note-text">
                Note: To search across multiple columns, add a space between words.
            </div>
        </div>
      
    {{-- </div> --}}
{{-- </div> --}}
   

<!-- <div class="row">
    <div class="span-6">            
        <div class="par control-group form-control">
            <label class="control-label"></label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <button type="submit" class="btn btn-primary" id="approvalButton">{{ config('define.value.add') }}</button>
            </div>
        </div>
    </div>

</div> -->
</form>
    </div>
</div>
@endsection


    @section('scripts')    


<script type="text/javascript" src="{{ asset('js/view/approval_status.js?ver='.getJsVersion()) }}"></script>
@endsection