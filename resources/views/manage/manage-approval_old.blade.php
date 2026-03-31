<?php 
$pageType = Request::path();
                $getPageName = $pageType == "sm_approval" ? "SM Approval" : ($pageType == "state_coordinator" ? "State Coordinator" : ($pageType == "zsm_approval" ? "ZSM Approval" : ($pageType == "md_approval" ? "MD Approval" : ""))); 
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
            <div class="btn-group"> <a href="{{ route('manage-'.$getFristPageName.'_report') }}" class="btn btn-inverse">Back</a> </div>
            
          
        </div>

        @php 
            $userType = Auth::user()->user_type;
           
             if(Auth::user()->id == 1)
             {
                $userType = Request::path();
                
                $getName = $userType == "sm_approval" ? "SM Approval" : ($userType == "state_coordinator" ? "State Coordinator" : ($userType == "zsm_approval" ? "ZSM Approval" : ($userType == "md_approval" ? "MD Approval" : "")));     
                
             }
             else 
             {
                $userType = Request::path();
                $getName = $userType == "sm_approval" ? "SM Approval" : ($userType == "state_coordinator" ? "State Coordinator" : ($userType == "zsm_approval" ? "ZSM Approval" : ($userType == "md_approval" ? "MD Approval" : "")));           
             }
             
             if($userType == 'operator'){
                $userType = Request::path();
                $getName = $userType == "sm_approval" ? "SM Approval" : ($userType == "state_coordinator" ? "State Coordinator" : ($userType == "zsm_approval" ? "ZSM Approval" : ($userType == "md_approval" ? "MD Approval" : "")));  
             }

             //  $getName = $userType == "state_manager" ? "SM Approval" : ($userType == "zonal_manager" ? "ZSM Approval" : ($userType == "director" ? "MD Approval" : ""));             
         @endphp
        <h4 class="widgettitle">{{ $getName }}</h4>
        
    </div>
    <div class="widgetcontent">
        
        <form id="commonApprovalRequestForm" class="stdform" method="post">
                <input type="hidden" name="pagename" id="pagename" value='{{ $userType }}'>
            @csrf
            
                    <div class="row">
                        <div class="span-6">
                            <div class="par control-group form-control">
                            <label class="control-label" for="approval_date">Approval Date </label>
                            <div class="controls"> <span class="formwrapper">
                                <input name="approval_date" id="approval_date" class="trans-date-picker no-fill" />
                                </span> </div>
                            </div>
                        </div>
                    </div><br>

                    <div class="widgetbox-inverse">

                            <div class="headtitle">
                    
                                <h4 class="widgettitle"> Approval List</h4>
                                {{-- <h4 class="widgettitle">Raw Material Mapping</h4> --}}
                    
                            </div>
                 
                        <div class="widgetcontent overflow-scroll">
                            <div>
                                <div>  
                                    @if($userType == 'state_manager' || $userType == "sm_approval")

                            
                                    <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th class="head0">From Location</th>
                                            <th class="head0">MR No.</th>
                                            <th class="head0">MR Date</th>
                                            <th class="head0">To Location</th>
                                            {{-- <th calss="head1">Item Name</th>
                                            <th class="head0">Item Code</th>                    
                                            <th class="head0">MR. Qty.</th>                  
                                            <th class="head0">Unit</th>
                                            <th class="head0">Remark</th>                 --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr> <td colspan="17">No Data Available! </td> </tr>
                                    </tbody>
                                
                                    </table><br>
                                    @elseif ($userType == "state_coordinator" || $userType=="state_coordinator")
                                    <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            {{-- <th class="head0">Sm</th> --}}
                                            {{-- <th class="head0">SM</th> --}}
                                            {{-- <th class="head0">Sm App. Date</th> --}}
                                            {{-- <th class="head0">SM.App.Date</th> --}}
                                            <th class="head0">From Location</th>
                                            <th class="head0">MR No.</th>
                                            <th class="head0">MR Date</th>
                                            <th class="head0">To Location</th>
                                            <th class="head0">SM Approval</th>
                                            <th class="head0">SM.App.Date</th>                                 
                                            {{-- <th calss="head1">Item Name</th>
                                            <th class="head0">Item Code</th>                    
                                            <th class="head0">MR. Qty.</th>                  
                                            <th class="head0">Unit</th>
                                            <th class="head0">Remark</th>                 --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr> <td colspan="17">No Data Available! </td> </tr>
                                    </tbody>
                            
                                    </table><br>

                                    @elseif ($userType == "zonal_manager" || $userType=="zsm_approval")
                                    <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            {{-- <th class="head0">Sm</th> --}}
                                            {{-- <th class="head0">SM</th> --}}
                                            {{-- <th class="head0">Sm App. Date</th> --}}
                                            {{-- <th class="head0">SM.App.Date</th> --}}
                                            <th class="head0">From Location</th>
                                            <th class="head0">MR No.</th>
                                            <th class="head0">MR Date</th>
                                            <th class="head0">To Location</th>
                                            <th class="head0">SM Approval</th>
                                            <th class="head0">SM.App.Date</th>
                                            <th class="head0">State Coordinator Approval</th>
                                            <th class="head0">State Coordinator.App.Date</th>
                                            {{-- <th calss="head1">Item Name</th>
                                            <th class="head0">Item Code</th>                    
                                            <th class="head0">MR. Qty.</th>                  
                                            <th class="head0">Unit</th>
                                            <th class="head0">Remark</th>                 --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr> <td colspan="17">No Data Available! </td> </tr>
                                    </tbody>
                            
                                    </table><br>
                                    @else
                                    <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            {{-- <th class="head0">Zsm</th> --}}
                                            {{-- <th class="head0">ZSM</th> --}}
                                            {{-- <th class="head0">Zsm App. Date</th> --}}
                                            {{-- <th class="head0">ZSM.App.Date</th> --}}
                                            {{-- <th class="head0">Sm</th> --}}
                                            {{-- <th class="head0">SM</th> --}}
                                            {{-- <th class="head0">Sm App. Date</th> --}}
                                            {{-- <th class="head0">SM.App.Date</th> --}}
                                            <th class="head0">From Location</th>
                                            <th class="head0">MR No.</th>
                                            <th class="head0">MR Date</th>
                                            <th class="head0">To Location</th>
                                            <th class="head0">SM Approval</th>
                                            <th class="head0">SM.App.Date</th>
                                            <th class="head0">State Coordinator Approval</th>
                                            <th class="head0">State Coordinator.App.Date</th>
                                            <th class="head0">ZSM Approval</th>
                                            <th class="head0">ZSM.App.Date</th>
                                            {{-- <th calss="head1">Item Name</th>
                                            <th class="head0">Item Code</th>                    
                                            <th class="head0">MR. Qty.</th>                  
                                            <th class="head0">Unit</th>
                                            <th class="head0">Remark</th>                 --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr> <td colspan="17">No Data Available! </td> </tr>
                                    </tbody>
                                
                                    </table><br>
                                    @endif
                                </div>
                                    <table id="approvalDataTable" class="table table-infinite table-bordered responsive table-autowidth">
                                        <thead>
                                            <tr>                                         
                                                <th calss="head0">Action</th>
                                                <th calss="head1">Item Name</th>
                                                <th class="head0">Item Code</th>                    
                                                <th class="head1">MR. Qty.</th>                  
                                                <th class="head0">Unit</th>
                                                <th class="head1">Remark</th>                
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                            </div>
                           
                        </div>
                    </div>
                            
                            {{-- <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" name="checkall-sm" class="simple-check" id="checkall-sm"/></th>
                                        
                                        @if($userType == "zonal_manager")
                                            <th class="head0">SM</th>
                                            <th class="head0">SM App. Date</th>

                                        @elseif($userType == "director")
                                            <th class="head0">ZSM</th>
                                            <th class="head0">ZSM App. Date</th>
                                            <th class="head0">SM</th>
                                            <th class="head0">SM App. Date</th>
                                        @endif

                                        <th class="head0">Location</th>
                                        <th class="head0">MR No.</th>
                                        <th class="head0">MR Date</th>
                                        <th calss="head1">Item Name</th>
                                        <th class="head0">Item Code</th>               
                                        <th class="head0">MR. Qty.</th>                
                                        <th class="head0">Unit</th>
                                        <th class="head0">Remark</th>                
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table><br>
                  
                    {{-- </table><br> --}}

                    <div class="row">
                        <div class="span-6">
                            <div class="par control-group form-control">
                            <label class="control-label" for="approved_by">Approved By </label>
                            <div class="controls"> <span class="formwrapper">
                                <input  type="text" name="approved_by" id="approved_by" class="input-large" readonly />
                                </span> </div>
                            </div>
                        </div>
                    </div>
                    @php
                        
                        $userType = Request::path();
                    @endphp
                    @if(hasAccess($userType,"add"))
                    <div class="row">
                        <div class="span-6">            
                            <div class="par control-group form-control">
                                <label class="control-label"></label>
                                <div class="controls">
                                        <span class="formwrapper"> 
                                            <button type="submit" class="btn btn-primary" id="approvalButton">{{ config('define.value.add') }}</button>
                                </div>
                            </div>
                        </div>
                
                    </div>
                    @endif
        </form>
    </div>
</div>
@endsection




@section('scripts')    
<script type="text/javascript" src="{{asset('js/view/approval.js?ver='.getJsVersion()) }}"></script>

    

@endsection
